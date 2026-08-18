<?php
class UserProfileController {
    public function __construct() {
        if (!Session::get('user_id')) {
            header('Location: /login');
            exit;
        }
    }
    public function index(): void {
        $userModel = new User();
        $user = $userModel->findById(Session::get('user_id'));
        require_once APP_DIR . '/Views/layouts/header.php';
        require_once APP_DIR . '/Views/pages/profile.php';
        require_once APP_DIR . '/Views/layouts/footer.php';
    }
    public function update(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userModel = new User();
            $data = [
                'name' => $_POST['name'] ?? '',
                'email' => $_POST['email'] ?? ''
            ];
            $userModel->updateProfile(Session::get('user_id'), $data);
            Session::set('user_name', $data['name']);
            header('Location: /profile?success=1');
            exit;
        }
    }
    public function orders(): void {
        $orderModel = new Order();
        $orders = $orderModel->getByUserId(Session::get('user_id'));
        require_once APP_DIR . '/Views/layouts/header.php';
        require_once APP_DIR . '/Views/pages/my_orders.php';
        require_once APP_DIR . '/Views/layouts/footer.php';
    }
    public function messages(): void {
        $messageModel = new Message();
        $messages = $messageModel->getByUserId(Session::get('user_id'));
        require_once APP_DIR . '/Views/layouts/header.php';
        require_once APP_DIR . '/Views/pages/my_messages.php';
        require_once APP_DIR . '/Views/layouts/footer.php';
    }
    public function sendMessage(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $messageModel = new Message();
            $data = [
                'user_id' => Session::get('user_id'),
                'subject' => $_POST['subject'] ?? 'بدون عنوان',
                'message' => $_POST['message'] ?? ''
            ];
            $messageModel->create($data);
            header('Location: /my-messages?success=1');
            exit;
        }
    }
}
