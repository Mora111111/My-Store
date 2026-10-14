<?php
class PaymentController {
    public function pay(): void {
        if (!Session::get('user_id')) {
            header('Location: /login');
            exit;
        }
        
        $orderId = (int)($_GET['order_id'] ?? 0);
        if (!$orderId) {
            header('Location: /my-orders');
            exit;
        }
        
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
        $stmt->execute([$orderId, Session::get('user_id')]);
        $order = $stmt->fetch();
        
        $isWallet = ($order['payment_method'] === 'online_wallet');
        $isCard = ($order['payment_method'] === 'online_card' || $order['payment_method'] === 'online');
        
        if (!$order || (!$isWallet && !$isCard) || $order['payment_status'] === 'paid' || $order['status'] === 'ملغي') {
            header('Location: /my-orders');
            exit;
        }
        
        $settingModel = new Setting();
        $settings = $settingModel->getSettings();
        
        $apiKey = trim($settings['gateway_api_key'] ?? '');
        $iframeId = trim($settings['gateway_iframe_id'] ?? '');
        
        $cardIntId = (int)trim($settings['gateway_integration_id'] ?? 0);
        $walletIntId = (int)trim($settings['gateway_integration_id_wallet'] ?? 0);
        $integrationId = $isWallet ? $walletIntId : $cardIntId;
        
        if (empty($apiKey) || $integrationId === 0) {
            die("<h2 style='text-align:center; margin-top:50px; font-family:sans-serif;'>عذراً، رقم الدمج غير موجود. يرجى التأكد من حفظ إعدادات بوابات الدفع في لوحة التحكم.</h2>");
        }
        
        $amountCents = (int)($order['total_price'] * 100);
        
        $authResponse = $this->cURL('https://accept.paymob.com/api/auth/tokens', ['api_key' => $apiKey]);
        $token = $authResponse->token ?? null;
        if (!$token) die("فشل المصادقة مع سيرفر الدفع.");
        
        $orderResponse = $this->cURL('https://accept.paymob.com/api/ecommerce/orders', [
            'auth_token' => $token,
            'delivery_needed' => 'false',
            'amount_cents' => $amountCents,
            'currency' => 'EGP',
            'merchant_order_id' => $order['id'] . '_' . time()
        ]);
        $paymobOrderId = $orderResponse->id ?? null;
        if (!$paymobOrderId) die("فشل تسجيل الطلب في بوابة الدفع.");
        
        $fullName = trim($order['full_name']);
        $nameParts = explode(' ', $fullName);
        $firstName = !empty($nameParts[0]) ? $nameParts[0] : 'Customer';
        $lastName = (count($nameParts) > 1 && !empty($nameParts[1])) ? $nameParts[1] : 'User';
        
        $rawPhone = !empty($order['phone']) ? preg_replace('/[^0-9]/', '', $order['phone']) : '01000000000';
        $finalPhone = $rawPhone ?: '01000000000';

        $paymentKeyResponse = $this->cURL('https://accept.paymob.com/api/acceptance/payment_keys', [
            'auth_token' => $token,
            'amount_cents' => $amountCents,
            'expiration' => 1800,
            'order_id' => $paymobOrderId,
            'billing_data' => [
                'apartment' => 'NA',
                'email' => 'customer@domain.com',
                'floor' => 'NA',
                'first_name' => $firstName,
                'street' => !empty($order['address_line1']) ? $order['address_line1'] : 'NA',
                'building' => 'NA',
                'phone_number' => $finalPhone,
                'shipping_method' => 'NA',
                'postal_code' => !empty($order['zip_code']) ? $order['zip_code'] : 'NA',
                'city' => !empty($order['city']) ? $order['city'] : 'NA',
                'country' => 'EG',
                'last_name' => $lastName,
                'state' => !empty($order['governorate']) ? $order['governorate'] : 'NA'
            ],
            'currency' => 'EGP',
            'integration_id' => $integrationId
        ]);
        $paymentToken = $paymentKeyResponse->token ?? null;
        if (!$paymentToken) die("فشل توليد مفتاح الدفع النهائي.");
        
        if ($isWallet) {
            $walletResponse = $this->cURL('https://accept.paymob.com/api/acceptance/payments/pay', [
                'source' => [
                    'identifier' => $finalPhone,
                    'subtype' => 'WALLET'
                ],
                'payment_token' => $paymentToken
            ]);
            $redirectUrl = $walletResponse->redirect_url ?? null;
            if ($redirectUrl) {
                header('Location: ' . $redirectUrl);
                exit;
            } else {
                die("فشل توليد رابط الدفع للمحفظة الإلكترونية.");
            }
        } else {
            header('Location: https://accept.paymob.com/api/acceptance/iframes/' . $iframeId . '?payment_token=' . $paymentToken);
            exit;
        }
    }
    
    public function callback(): void {
        $data = file_get_contents('php://input');
        $json = json_decode($data, true);

        if (!$json || !isset($json['obj']) || !is_array($json['obj'])) {
            http_response_code(400);
            exit;
        }

        $obj = $json['obj'];
        $merchantOrderId = (string)($obj['order']['merchant_order_id'] ?? '');
        $orderIdParts = explode('_', $merchantOrderId);
        $realOrderId = (int)($orderIdParts[0] ?? 0);

        $settingModel = new Setting();
        $settings = $settingModel->getSettings();
        $hmacSecret = trim($settings['gateway_hmac_secret'] ?? '');

        $receivedHmac = $_GET['hmac'] ?? '';

        if ($this->verifyHmac($obj, (string)$receivedHmac, $hmacSecret) && $realOrderId > 0) {
            $isSuccess = ($obj['success'] ?? false) === true || ($obj['success'] ?? '') === 'true';
            $trxId = (string)($obj['id'] ?? ($isSuccess ? 'paid_transaction' : 'failed_transaction'));
            $this->settleOrderPayment($realOrderId, $isSuccess, $trxId);
        }

        http_response_code(200);
        exit;
    }
    
    public function response(): void {
        if (isset($_GET['hmac'], $_GET['merchant_order_id'])) {
            $settingModel = new Setting();
            $settings = $settingModel->getSettings();
            $hmacSecret = trim($settings['gateway_hmac_secret'] ?? '');
            if ($this->verifyHmac($_GET, (string)$_GET['hmac'], $hmacSecret)) {
                $responseOrderIdParts = explode('_', (string)$_GET['merchant_order_id']);
                $responseOrderId = (int)($responseOrderIdParts[0] ?? 0);
                $isSuccess = ($_GET['success'] ?? 'false') === 'true';
                $trxId = (string)($_GET['id'] ?? '');
                $this->settleOrderPayment($responseOrderId, $isSuccess, $trxId);
            }
        }
        $success = $_GET['success'] ?? 'false';
        if ($success === 'true') {
            echo "<div style='text-align:center; padding:50px; font-family:sans-serif; background:#f0fdf4; height:100vh; display:flex; flex-direction:column; justify-content:center; align-items:center;'>
                    <h1 style='color:#166534; font-size:40px; margin-bottom:20px;'>تم الدفع بنجاح!</h1>
                    <p style='color:#15803d; font-size:20px; margin-bottom:40px;'>نشكرك، تم استلام طلبك وتأكيد الدفع.</p>
                    <a href='/my-orders' style='padding:15px 30px; background:#10b981; color:#fff; text-decoration:none; border-radius:8px; font-size:18px; font-weight:bold;'>العودة لطلباتي</a>
                  </div>
                  <script>
                    localStorage.removeItem('cards');
                    localStorage.removeItem('total_Price');
                    localStorage.removeItem('activeCoupon');
                  </script>";
        } else {
            echo "<div style='text-align:center; padding:50px; font-family:sans-serif; background:#fef2f2; height:100vh; display:flex; flex-direction:column; justify-content:center; align-items:center;'>
                    <h1 style='color:#991b1b; font-size:40px; margin-bottom:20px;'>فشلت عملية الدفع!</h1>
                    <p style='color:#b91c1c; font-size:20px; margin-bottom:40px;'>عذراً، حدث خطأ أثناء معالجة الدفع أو تم رفض العملية من البنك.</p>
                    <a href='/checkout' style='padding:15px 30px; background:#ef4444; color:#fff; text-decoration:none; border-radius:8px; font-size:18px; font-weight:bold;'>حاول مرة أخرى</a>
                  </div>";
        }
        exit;
    }

    private function verifyHmac(array $data, string $receivedHmac, string $hmacSecret): bool {
        if ($hmacSecret === '' || $receivedHmac === '') {
            return false;
        }
        $boolFields = ['error_occured', 'has_parent_transaction', 'is_3d_secure', 'is_auth', 'is_capture', 'is_refunded', 'is_standalone_payment', 'is_voided', 'pending', 'success'];
        $normalized = $data;
        foreach ($boolFields as $field) {
            if (!array_key_exists($field, $normalized)) {
                continue;
            }
            $value = $normalized[$field];
            $normalized[$field] = ($value === true || $value === 'true' || $value === 1 || $value === '1') ? 'true' : 'false';
        }
        $requestData = [
            'amount_cents' => $normalized['amount_cents'] ?? '',
            'created_at' => $normalized['created_at'] ?? '',
            'currency' => $normalized['currency'] ?? '',
            'error_occured' => $normalized['error_occured'] ?? 'false',
            'has_parent_transaction' => $normalized['has_parent_transaction'] ?? 'false',
            'id' => $normalized['id'] ?? '',
            'integration_id' => $normalized['integration_id'] ?? '',
            'is_3d_secure' => $normalized['is_3d_secure'] ?? 'false',
            'is_auth' => $normalized['is_auth'] ?? 'false',
            'is_capture' => $normalized['is_capture'] ?? 'false',
            'is_refunded' => $normalized['is_refunded'] ?? 'false',
            'is_standalone_payment' => $normalized['is_standalone_payment'] ?? 'false',
            'is_voided' => $normalized['is_voided'] ?? 'false',
            'order' => (isset($normalized['order']) && is_array($normalized['order'])) ? ($normalized['order']['id'] ?? '') : ($normalized['order'] ?? ''),
            'owner' => $normalized['owner'] ?? '',
            'pending' => $normalized['pending'] ?? 'false',
            'source_data_pan' => (isset($normalized['source_data']) && is_array($normalized['source_data'])) ? ($normalized['source_data']['pan'] ?? '') : ($normalized['source_data_pan'] ?? ''),
            'source_data_sub_type' => (isset($normalized['source_data']) && is_array($normalized['source_data'])) ? ($normalized['source_data']['sub_type'] ?? '') : ($normalized['source_data_sub_type'] ?? ''),
            'source_data_type' => (isset($normalized['source_data']) && is_array($normalized['source_data'])) ? ($normalized['source_data']['type'] ?? '') : ($normalized['source_data_type'] ?? ''),
            'success' => $normalized['success'] ?? 'false'
        ];
        $calculatedHmac = hash_hmac('sha512', implode('', $requestData), $hmacSecret);
        return hash_equals($calculatedHmac, $receivedHmac);
    }

    private function settleOrderPayment(int $realOrderId, bool $success, string $transactionId): void {
        if ($realOrderId <= 0) {
            return;
        }
        $db = Database::getInstance()->getConnection();
        try {
            $db->beginTransaction();
            $lockStmt = $db->prepare("SELECT id, status, payment_status, products FROM orders WHERE id = ? LIMIT 1 FOR UPDATE");
            $lockStmt->execute([$realOrderId]);
            $order = $lockStmt->fetch(PDO::FETCH_ASSOC);
            if (!$order) {
                $db->rollBack();
                return;
            }
            if ($success === true) {
                if ($order['payment_status'] !== 'paid') {
                    $updateStmt = $db->prepare("UPDATE orders SET payment_status = 'paid', status = 'قيد المراجعة', user_hidden = 0, admin_hidden = 0, transaction_id = ? WHERE id = ?");
                    $updateStmt->execute([$transactionId, $realOrderId]);
                    if ($order['status'] === 'ملغي') {
                        $products = json_decode($order['products'] ?? '[]', true);
                        if (is_array($products)) {
                            $deduct = $db->prepare("UPDATE products SET quantity = GREATEST(0, quantity - ?) WHERE id = ?");
                            foreach ($products as $item) {
                                if (!is_array($item)) {
                                    continue;
                                }
                                $qty = (int)($item['quantity'] ?? $item['number'] ?? 1);
                                $prodId = (int)($item['id'] ?? 0);
                                if ($prodId > 0 && $qty > 0) {
                                    $deduct->execute([$qty, $prodId]);
                                }
                            }
                        }
                    }
                }
                $db->commit();
                return;
            }
            if ($order['status'] === 'بانتظار الدفع' && $order['payment_status'] === 'pending') {
                $failStmt = $db->prepare("UPDATE orders SET payment_status = 'failed', status = 'ملغي', user_hidden = 1, admin_hidden = 1, transaction_id = ? WHERE id = ? AND status = 'بانتظار الدفع' AND payment_status = 'pending'");
                $failStmt->execute([$transactionId, $realOrderId]);
                if ($failStmt->rowCount() > 0) {
                    $products = json_decode($order['products'] ?? '[]', true);
                    if (is_array($products)) {
                        $restock = $db->prepare("UPDATE products SET quantity = quantity + ? WHERE id = ?");
                        foreach ($products as $item) {
                            if (!is_array($item)) {
                                continue;
                            }
                            $qty = (int)($item['quantity'] ?? $item['number'] ?? 1);
                            $prodId = (int)($item['id'] ?? 0);
                            if ($prodId > 0 && $qty > 0) {
                                $restock->execute([$qty, $prodId]);
                            }
                        }
                    }
                }
            }
            $db->commit();
            return;
        } catch (Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
        }
    }

    private function cURL($url, $data) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); 
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response);
    }
}