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
            // التحقق من أمان الطلب (CSRF)
            if (!isset($_POST['csrf_token']) || !CSRF::validate($_POST['csrf_token'])) {
                $_SESSION['toast_msg'] = 'فشل التحقق من أمان الطلب.';
                $_SESSION['toast_type'] = 'error';
                header('Location: /admin/messages');
                exit;
            }

            $messageModel = new Message();
            $type = $_POST['type'] ?? 'contact';
            $id = intval($_POST['msg_id'] ?? 0);
            $reply_text = trim($_POST['reply_text'] ?? '');

            if ($type === 'user') {
                $saved = $messageModel->replyUserMessage($id, $reply_text);
                if ($saved && $id > 0 && $reply_text !== '') {
                    $this->notifyUserOfReply($messageModel, $id, $reply_text);
                }
            } else {
                $messageModel->replyContactMessage($id, $reply_text);
            }
            $_SESSION['toast_msg'] = 'تم حفظ الرد بنجاح!';
            $_SESSION['toast_type'] = 'success';
        }
        header('Location: /admin/messages');
        exit;
    }

    private function notifyUserOfReply(Message $messageModel, int $id, string $reply): void {
        try {
            $userMessage = $messageModel->findUserMessageById($id);
            if (empty($userMessage['user_id'])) {
                return;
            }

            $preview = $reply;
            if (function_exists('mb_strimwidth')) {
                $preview = mb_strimwidth($reply, 0, 140, '...', 'UTF-8');
            } elseif (preg_match('/^(\X{137})\X{4}/u', $reply, $matches)) {
                $preview = $matches[1] . '...';
            }

            $subject = trim((string)($userMessage['subject'] ?? ''));
            (new Notification())->create(
                (int)$userMessage['user_id'],
                'message',
                $id,
                'رد جديد من الدعم الفني',
                'New Reply from Support',
                ($subject !== '' ? "الموضوع: {$subject}\n" : '') . $preview,
                ($subject !== '' ? "Subject: {$subject}\n" : '') . $preview,
                '/my-messages'
            );
        } catch (Throwable $e) {
            error_log('Support reply notification failed: ' . $e->getMessage());
        }
    }

    public function delete(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // التحقق من أمان الطلب (CSRF)
            if (!isset($_POST['csrf_token']) || !CSRF::validate($_POST['csrf_token'])) {
                $_SESSION['toast_msg'] = 'فشل التحقق من أمان الطلب.';
                $_SESSION['toast_type'] = 'error';
                header('Location: /admin/messages');
                exit;
            }

            $messageModel = new Message();
            $id = intval($_POST['id'] ?? 0);
            $type = $_POST['type'] ?? 'contact';

            if ($id > 0) {
                $messageModel->deleteMessage($id, $type);
                $_SESSION['toast_msg'] = 'تم حذف الرسالة بنجاح.';
                $_SESSION['toast_type'] = 'success';
            }
        }
        header('Location: /admin/messages');
        exit;
    }
}
