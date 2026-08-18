<?php
class CheckoutController {
    public function index(): void {
        if (!Session::get('user_id')) {
            header('Location: /login');
            exit;
        }
        require_once APP_DIR . '/Views/layouts/header.php';
        require_once APP_DIR . '/Views/pages/payment.php';
        require_once APP_DIR . '/Views/layouts/footer.php';
    }
    public function process(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $orderModel = new Order();
            $data = [
                'user_id' => Session::get('user_id'),
                'full_name' => $_POST['full_name'] ?? '',
                'phone' => $_POST['phone'] ?? '',
                'address_line1' => $_POST['address_line1'] ?? '',
                'address_line2' => $_POST['address_line2'] ?? '',
                'city' => $_POST['city'] ?? '',
                'governorate' => $_POST['governorate'] ?? '',
                'zip_code' => $_POST['zip_code'] ?? '',
                'total_price' => $_POST['total_price'] ?? 0.00,
                'products' => $_POST['products'] ?? '[]'
            ];
            if ($orderModel->create($data)) {
                header('Location: /?order_success=1');
                exit;
            }
            header('Location: /checkout?error=1');
            exit;
        }
    }
}
