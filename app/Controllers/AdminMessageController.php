<?php
class AdminMessageController {
    public function __construct() {
        if (Session::get('user_role') !== 'admin') {
            header('Location: /');
            exit;
        }
    }

    public function index(): void {
        $messageModel = new Message();
        $contact_messages = $messageModel->getAllContactMessages();
        $user_messages = $messageModel->getAllUserMessages();
        $showSearch = true;
        $toast_msg = $_SESSION['toast_msg'] ?? '';
        $toast_type = $_SESSION['toast_type'] ?? '';
        unset($_SESSION['toast_msg'], $_SESSION['toast_type']);
        $pageIcon = 'fa-envelope';
        $pageTitle = 'الرسائل';
        require_once APP_DIR . '/Views/admin/layout_start.php';
        require_once APP_DIR . '/Views/admin/messages/index.php';
        require_once APP_DIR . '/Views/admin/layout_end.php';
    }

    public function reply(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $messageModel = new Message();
            $type = $_POST['type'] ?? 'contact';
            $id = intval($_POST['msg_id'] ?? 0);
            $reply_text = trim($_POST['reply_text'] ?? '');
            if ($type === 'user') {
                $messageModel->replyUserMessage($id, $reply_text);
            } else {
                $messageModel->replyContactMessage($id, $reply_text);
            }
            $_SESSION['toast_msg'] = 'تم حفظ الرد بنجاح!';
            $_SESSION['toast_type'] = 'success';
        }
        header('Location: /admin/messages');
        exit;
    }
}
