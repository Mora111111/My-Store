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

        // حماية: إذا الطلب غير موجود، أو كاش، أو مدفوع مسبقاً، نمنع الدخول
        if (!$order || $order['payment_method'] !== 'online' || $order['payment_status'] === 'paid') {
            header('Location: /my-orders');
            exit;
        }

        $settingModel = new Setting();
        $settings = $settingModel->getSettings();

        $apiKey = trim($settings['gateway_api_key'] ?? '');
        $integrationId = trim($settings['gateway_integration_id'] ?? '');
        $iframeId = trim($settings['gateway_iframe_id'] ?? '');

        if (empty($apiKey) || empty($integrationId) || empty($iframeId)) {
            die("<h2 style='text-align:center; margin-top:50px; font-family:sans-serif;'>عذراً، بوابات الدفع غير مهيأة بشكل كامل. يرجى مراجعة الإدارة.</h2>");
        }

        // Paymob تتعامل بالقرش، لذا نضرب السعر في 100
        $amountCents = (int)($order['total_price'] * 100);

        // 1. Authentication Request
        $authResponse = $this->cURL('https://accept.paymob.com/api/auth/tokens', [
            'api_key' => $apiKey
        ]);
        $token = $authResponse->token ?? null;
        if (!$token) die("فشل المصادقة مع سيرفر الدفع (تأكد من صحة الـ API Key).");

        // 2. Order Registration Request
        $orderResponse = $this->cURL('https://accept.paymob.com/api/ecommerce/orders', [
            'auth_token' => $token,
            'delivery_needed' => 'false',
            'amount_cents' => $amountCents,
            'currency' => 'EGP',
            'merchant_order_id' => $order['id'] . '_' . time() // نضيف الوقت لمنع تعارض الأرقام في وضع الاختبار
        ]);
        $paymobOrderId = $orderResponse->id ?? null;
        if (!$paymobOrderId) die("فشل تسجيل الطلب في بوابة الدفع.");

        // فصل الاسم الأول والأخير لتمريره للبنك
        $nameParts = explode(' ', $order['full_name'], 2);
        $firstName = $nameParts[0] ?? 'Customer';
        $lastName = $nameParts[1] ?? 'Name';

        // 3. Payment Key Request
        $paymentKeyResponse = $this->cURL('https://accept.paymob.com/api/acceptance/payment_keys', [
            'auth_token' => $token,
            'amount_cents' => $amountCents,
            'expiration' => 3600,
            'order_id' => $paymobOrderId,
            'billing_data' => [
                'apartment' => 'NA',
                'email' => 'customer@domain.com', // يفضل تغييره لبريد العميل إن وجد
                'floor' => 'NA',
                'first_name' => $firstName,
                'street' => !empty($order['address_line1']) ? $order['address_line1'] : 'NA',
                'building' => 'NA',
                'phone_number' => !empty($order['phone']) ? $order['phone'] : 'NA',
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

        // 4. Redirect to Paymob Iframe
        header('Location: https://accept.paymob.com/api/acceptance/iframes/' . $iframeId . '?payment_token=' . $paymentToken);
        exit;
    }

    // دالة مساعدة للاتصال السريع والمحمي بـ API
    private function cURL($url, $data) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        // تجاهل التحقق من SSL محلياً لتجنب الأخطاء، لكن يفضل تفعيله في الإنتاج
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response);
    }
}