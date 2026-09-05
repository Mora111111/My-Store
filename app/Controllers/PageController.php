<?php
class PageController {public function sendMessage(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $messageModel = new Message();
            
            if (Session::isLoggedIn()) {
                $data = [
                    'user_id' => Session::get('user_id'),
                    'subject' => 'رسالة من صفحة اتصل بنا',
                    'message' => trim($_POST['message'] ?? '')
                ];
                $messageModel->create($data);
                $redirect = '/my-messages?success=1';
            } else {
                $data = [
                    'first_name' => trim($_POST['first_name'] ?? ''),
                    'last_name' => trim($_POST['last_name'] ?? ''),
                    'phone' => trim($_POST['phone'] ?? ''),
                    'email' => trim($_POST['email'] ?? ''),
                    'message' => trim($_POST['message'] ?? '')
                ];
                $messageModel->createContactMessage($data);
                $redirect = '/contact?success=1';
            }
            
            header("Location: {$redirect}");
            exit();
        }
        header("Location: /contact");
        exit();
    }
}