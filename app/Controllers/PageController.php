<?php
class PageController {
    public function about(): void {
        require_once APP_DIR . '/Views/layouts/header.php';
        require_once APP_DIR . '/Views/pages/about.php';
        require_once APP_DIR . '/Views/layouts/footer.php';
    }

    public function contact(): void {
        require_once APP_DIR . '/Views/layouts/header.php';
        require_once APP_DIR . '/Views/pages/contact.php';
        require_once APP_DIR . '/Views/layouts/footer.php';
    }

    public function sendMessage(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'first_name' => trim($_POST['first_name'] ?? ''),
                'last_name' => trim($_POST['last_name'] ?? ''),
                'phone' => trim($_POST['phone'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'message' => trim($_POST['message'] ?? '')
            ];
            $messageModel = new Message();
            $messageModel->createContactMessage($data);
            $redirect = Session::isLoggedIn() ? '/my-messages' : '/contact';
            echo "<script>alert('شكراً لتواصلك معنا! تم إرسال رسالتك بنجاح.'); window.location.href='{$redirect}';</script>";
            exit();
        }
        header("Location: /contact");
        exit();
    }
}