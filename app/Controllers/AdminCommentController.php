<?php
class AdminCommentController {
    public function __construct() {
        if (Session::get('user_role') !== 'admin') {
            header('Location: /');
            exit;
        }
    }

    public function index(): void {
        $commentModel = new Comment();
        $comments = $commentModel->getAll();
        $showSearch = true;
        $toast_msg = $_SESSION['toast_msg'] ?? '';
        $toast_type = $_SESSION['toast_type'] ?? '';
        unset($_SESSION['toast_msg'], $_SESSION['toast_type']);
        $pageIcon = 'fa-comments';
        $pageTitle = 'التعليقات';
        require_once APP_DIR . '/Views/admin/layout_start.php';
        require_once APP_DIR . '/Views/admin/comments/index.php';
        require_once APP_DIR . '/Views/admin/layout_end.php';
    }

    public function reply(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $commentModel = new Comment();
            $id = intval($_POST['comment_id'] ?? 0);
            $admin_reply = trim($_POST['admin_reply'] ?? '');
            if ($commentModel->reply($id, $admin_reply)) {
                $_SESSION['toast_msg'] = 'تم حفظ الرد بنجاح!';
                $_SESSION['toast_type'] = 'success';
            } else {
                $_SESSION['toast_msg'] = 'حدث خطأ أثناء الحفظ.';
                $_SESSION['toast_type'] = 'error';
            }
        }
        header('Location: /admin/comments');
        exit;
    }

    public function delete(): void {
        $commentModel = new Comment();
        $id = intval($_GET['id'] ?? 0);
        $commentModel->delete($id);
        $_SESSION['toast_msg'] = 'تم حذف التعليق بنجاح.';
        $_SESSION['toast_type'] = 'success';
        header('Location: /admin/comments');
        exit;
    }
}
