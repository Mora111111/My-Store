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
        
        if (!$order || (!$isWallet && !$isCard) || $order['payment_status'] === 'paid') {
            header('Location: /my-orders');
            exit;
        }
        
        $settingModel = new Setting();
        $settings = $settingModel->getSettings();
        
        $apiKey = trim($settings['gateway_api_key'] ?? '');
        $iframeId = trim($settings['gateway_iframe_id'] ?? '');
        $integrationId = $isWallet ? (int)trim($settings['gateway_integration_id_wallet'] ?? 0) : (int)trim($settings['gateway_integration_id'] ?? 0);
        
        if (empty($apiKey) || empty($integrationId)) {
            die("<h2 style='text-align:center; margin-top:50px; font-family:sans-serif;'>عذراً، بوابات الدفع غير مهيأة بشكل كامل. يرجى مراجعة الإدارة.</h2>");
        }
        
        $amountCents = (int)($order['total_price'] * 100);
        
        $authResponse = $this->cURL('https://accept.paymob.com/api/auth/tokens', [
            'api_key' => $apiKey
        ]);
        $token = $authResponse->token ?? null;
if (!$token) die("<div style='direction:ltr; text-align:left; padding:20px; background:#1e293b; color:#10b981; font-family:monospace;'><h3>1. Auth Error:</h3><pre>" . json_encode($authResponse, JSON_PRETTY_PRINT) . "</pre></div>");        
        $orderResponse = $this->cURL('https://accept.paymob.com/api/ecommerce/orders', [
            'auth_token' => $token,
            'delivery_needed' => 'false',
            'amount_cents' => $amountCents,
            'currency' => 'EGP',
            'merchant_order_id' => $order['id'] . '_' . time()
        ]);
        $paymobOrderId = $orderResponse->id ?? null;
        if (!$paymobOrderId) die("<div style='direction:ltr; text-align:left; padding:20px; background:#1e293b; color:#10b981; font-family:monospace;'><h3>2. Order Error:</h3><pre>" . json_encode($orderResponse, JSON_PRETTY_PRINT) . "</pre></div>");
        
        $fullName = trim($order['full_name']);
        $nameParts = explode(' ', $fullName);
        $firstName = !empty($nameParts[0]) ? $nameParts[0] : 'Customer';
        $lastName = (count($nameParts) > 1 && !empty($nameParts[1])) ? $nameParts[1] : 'User';
        $phone = !empty($order['phone']) ? preg_replace('/[^0-9]/', '', $order['phone']) : '01000000000';
        
        $paymentKeyResponse = $this->cURL('https://accept.paymob.com/api/acceptance/payment_keys', [
            'auth_token' => $token,
            'amount_cents' => $amountCents,
            'expiration' => 3600,
            'order_id' => $paymobOrderId,
            'billing_data' => [
                'apartment' => 'NA',
                'email' => 'customer@domain.com',
                'floor' => 'NA',
                'first_name' => $firstName,
                'street' => !empty($order['address_line1']) ? $order['address_line1'] : 'NA',
                'building' => 'NA',
                'phone_number' => $phone,
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
        if (!$paymentToken) die("<div style='direction:ltr; text-align:left; padding:20px; background:#1e293b; color:#10b981; font-family:monospace;'><h3>3. Payment Key Error:</h3><pre>" . json_encode($paymentKeyResponse, JSON_PRETTY_PRINT) . "</pre></div>");
        
        if ($isWallet) {
            $walletResponse = $this->cURL('https://accept.paymob.com/api/acceptance/payments/pay', [
                'source' => [
                    'identifier' => $phone,
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
        
        if (!$json || !isset($json['obj'])) {
            http_response_code(400);
            exit;
        }
        
        $obj = $json['obj'];
        $success = $obj['success'] ?? false;
        $merchantOrderId = $obj['order']['merchant_order_id'] ?? '';
        
        $orderIdParts = explode('_', $merchantOrderId);
        $realOrderId = (int)$orderIdParts[0];
        
        $settingModel = new Setting();
        $settings = $settingModel->getSettings();
        $hmacSecret = trim($settings['gateway_hmac_secret'] ?? '');
        
        $receivedHmac = $_GET['hmac'] ?? '';
        
        $requestData = [
            'amount_cents' => $obj['amount_cents'] ?? '',
            'created_at' => $obj['created_at'] ?? '',
            'currency' => $obj['currency'] ?? '',
            'error_occured' => ($obj['error_occured'] ?? false) ? 'true' : 'false',
            'has_parent_transaction' => ($obj['has_parent_transaction'] ?? false) ? 'true' : 'false',
            'id' => $obj['id'] ?? '',
            'integration_id' => $obj['integration_id'] ?? '',
            'is_3d_secure' => ($obj['is_3d_secure'] ?? false) ? 'true' : 'false',
            'is_auth' => ($obj['is_auth'] ?? false) ? 'true' : 'false',
            'is_capture' => ($obj['is_capture'] ?? false) ? 'true' : 'false',
            'is_refunded' => ($obj['is_refunded'] ?? false) ? 'true' : 'false',
            'is_standalone_payment' => ($obj['is_standalone_payment'] ?? false) ? 'true' : 'false',
            'is_voided' => ($obj['is_voided'] ?? false) ? 'true' : 'false',
            'order' => $obj['order']['id'] ?? '',
            'owner' => $obj['owner'] ?? '',
            'pending' => ($obj['pending'] ?? false) ? 'true' : 'false',
            'source_data_pan' => $obj['source_data']['pan'] ?? '',
            'source_data_sub_type' => $obj['source_data']['sub_type'] ?? '',
            'source_data_type' => $obj['source_data']['type'] ?? '',
            'success' => $success ? 'true' : 'false'
        ];
        
        $concatenatedString = implode('', $requestData);
        $calculatedHmac = hash_hmac('sha512', $concatenatedString, $hmacSecret);
        
        if ($calculatedHmac === $receivedHmac && $success && $realOrderId > 0) {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("UPDATE orders SET payment_status = 'paid' WHERE id = ?");
            $stmt->execute([$realOrderId]);
        }
        
        http_response_code(200);
        exit;
    }
    
    public function response(): void {
        $success = $_GET['success'] ?? 'false';
        if ($success === 'true') {
            echo "<div style='text-align:center; padding:50px; font-family:sans-serif; background:#f0fdf4; height:100vh; display:flex; flex-direction:column; justify-content:center; align-items:center;'>
                    <h1 style='color:#166534; font-size:40px; margin-bottom:20px;'>تم الدفع بنجاح!</h1>
                    <p style='color:#15803d; font-size:20px; margin-bottom:40px;'>نشكرك، تم استلام طلبك وتأكيد الدفع.</p>
                    <a href='/my-orders' style='padding:15px 30px; background:#10b981; color:#fff; text-decoration:none; border-radius:8px; font-size:18px; font-weight:bold;'>العودة لطلباتي</a>
                  </div>";
        } else {
            echo "<div style='text-align:center; padding:50px; font-family:sans-serif; background:#fef2f2; height:100vh; display:flex; flex-direction:column; justify-content:center; align-items:center;'>
                    <h1 style='color:#991b1b; font-size:40px; margin-bottom:20px;'>فشلت عملية الدفع!</h1>
                    <p style='color:#b91c1c; font-size:20px; margin-bottom:40px;'>عذراً، حدث خطأ أثناء معالجة الدفع أو تم رفض العملية من البنك.</p>
                    <a href='/checkout' style='padding:15px 30px; background:#ef4444; color:#fff; text-decoration:none; border-radius:8px; font-size:18px; font-weight:bold;'>حاول مرة أخرى</a>
                  </div>";
        }
        exit;
    }

    private function cURL($url, $data) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $logData = "[" . date('Y-m-d H:i:s') . "]\n";
        $logData .= "URL: " . $url . "\n";
        $logData .= "Payload: " . json_encode($data) . "\n";
        $logData .= "HTTP Code: " . $http_code . "\n";
        $logData .= "Response: " . $response . "\n";
        $logData .= str_repeat("=", 50) . "\n";
        file_put_contents(__DIR__ . '/paymob_debug.txt', $logData, FILE_APPEND);

        return json_decode($response);
    }
}