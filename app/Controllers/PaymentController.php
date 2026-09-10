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

        // هنا في المستقبل يتم كتابة أكواد الـ API الخاصة بالبنك
        // ولأننا في وضع الـ Sandbox التوضيحي، سنعرض صفحة دفع محاكاة (Mock)

        require_once APP_DIR . '/Views/layouts/header.php';
        require_once APP_DIR . '/Views/pages/sandbox_payment.php';
        require_once APP_DIR . '/Views/layouts/footer.php';
    }

    public function processSandbox(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && Session::get('user_id')) {
            $orderId = (int)($_POST['order_id'] ?? 0);
            
            // محاكاة عملية الدفع (توليد رقم عملية وهمي للبنك)
            $transactionId = 'TXN-' . strtoupper(uniqid()) . rand(1000, 9999);

            // تحديث حالة الطلب في قاعدة البيانات إلى "مدفوع" وإرفاق رقم العملية
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("UPDATE orders SET payment_status = 'paid', transaction_id = ? WHERE id = ? AND user_id = ?");
            $stmt->execute([$transactionId, $orderId, Session::get('user_id')]);

            header('Location: /my-orders?payment=success&trx=' . $transactionId);
            exit;
        }
    }
}