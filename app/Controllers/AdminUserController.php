<?php
class AdminUserController {
    public function __construct() {
        if (Session::get('user_role') !== 'admin') {
            header('Location: /');
            exit;
        }
    }

    public function index(): void {
        $userModel = new User();
        $users = $userModel->getAll();
        $totalUsers = count($users);
        $totalAdmins = count(array_filter($users, function($u) { return ($u['role'] ?? '') === 'admin'; }));
        $showSearch = true;
        $toast_msg = $_SESSION['toast_msg'] ?? '';
        $toast_type = $_SESSION['toast_type'] ?? '';
        unset($_SESSION['toast_msg'], $_SESSION['toast_type']);
        $pageIcon = 'fa-users';
        $pageTitle = 'إدارة المستخدمين';
        require_once APP_DIR . '/Views/admin/layout_start.php';
        require_once APP_DIR . '/Views/admin/users/index.php';
        require_once APP_DIR . '/Views/admin/layout_end.php';
    }

    public function add(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !CSRF::validate($_POST['csrf_token'])) {
                $_SESSION['toast_msg'] = 'فشل التحقق من أمان الطلب.';
                $_SESSION['toast_type'] = 'error';
                header('Location: /admin/users');
                exit;
            }

            $userModel = new User();
            $fname = trim($_POST['fname'] ?? '');
            $lname = trim($_POST['lname'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? 'user';

            if (!empty($fname) && !empty($lname) && !empty($email) && !empty($password)) {
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $_SESSION['toast_msg'] = 'صيغة البريد الإلكتروني غير صحيحة.';
                    $_SESSION['toast_type'] = 'error';
                } elseif (strlen($password) < 8) {
                    $_SESSION['toast_msg'] = 'كلمة المرور يجب أن تكون 8 أحرف على الأقل.';
                    $_SESSION['toast_type'] = 'error';
                } else {
                    $fname = htmlspecialchars($fname, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                    $lname = htmlspecialchars($lname, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                    $existing = $userModel->findByEmail($email);
                    if ($existing) {
                        $_SESSION['toast_msg'] = 'البريد الإلكتروني مستخدم بالفعل.';
                        $_SESSION['toast_type'] = 'error';
                    } else {
                        $userModel->create([
                            'name' => $fname . ' ' . $lname,
                            'email' => $email,
                            'password' => $password,
                            'role' => $role
                        ]);
                        $_SESSION['toast_msg'] = 'تم إضافة المستخدم بنجاح.';
                        $_SESSION['toast_type'] = 'success';
                    }
                }
            } else {
                $_SESSION['toast_msg'] = 'يرجى ملء جميع الحقول المطلوبة.';
                $_SESSION['toast_type'] = 'error';
            }
        }
        header('Location: /admin/users');
        exit;
    }

    public function updateRole(): void {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['csrf_token']) || !CSRF::validate($_POST['csrf_token'])) {
            $_SESSION['toast_msg'] = 'فشل التحقق من أمان الطلب.';
            $_SESSION['toast_type'] = 'error';
            header('Location: /admin/users');
            exit;
        }
        $userModel = new User();
        $id = intval($_POST['user_id'] ?? 0);
        $new_role = $_POST['new_role'] ?? 'user';
        
        // منع الترقية المباشرة لمدير (يجب المرور بالـ OTP)
        if ($new_role === 'admin') {
            $_SESSION['toast_msg'] = 'الترقية لمدير تتطلب التحقق من الرمز.';
            $_SESSION['toast_type'] = 'error';
            header('Location: /admin/users');
            exit;
        }
        
        if ($id > 0 && $id !== (int)Session::get('user_id')) {
            $userModel->updateRole($id, $new_role);
            $_SESSION['toast_msg'] = 'تم تحديث صلاحية المستخدم بنجاح.';
            $_SESSION['toast_type'] = 'success';
        }
    }
    header('Location: /admin/users');
    exit;
}
public function requestOtp(): void {
    header('Content-Type: application/json');
    $id = intval($_POST['user_id'] ?? 0);
    if ($id <= 0) { echo json_encode(['success' => false]); exit; }
    
    $_SESSION['admin_otp_user_id'] = $id;
    echo json_encode(['success' => true, 'message' => 'افتح تطبيق Google Authenticator على هاتفك للحصول على الكود']);
    exit;
}

public function verifyRoleOtp(): void {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['csrf_token']) || !CSRF::validate($_POST['csrf_token'])) {
            $_SESSION['toast_msg'] = 'فشل التحقق من أمان الطلب.';
            $_SESSION['toast_type'] = 'error';
            header('Location: /admin/users');
            exit;
        }

        $inputOtp = $_POST['otp_code'] ?? '';
        $userId = $_SESSION['admin_otp_user_id'] ?? 0;
        
        require_once CORE_DIR . '/GoogleAuthenticator.php';
        if (!GoogleAuthenticator::checkCode('AMRMYSTORE2222XX', $inputOtp)) { 
            echo json_encode(['success'=>false, 'message'=>'الكود غير صحيح أو منتهي الصلاحية']); 
            exit; 
        }
        
        if ($userId > 0) {
            $userModel = new User();
            $userModel->updateRole($userId, 'admin');
            unset($_SESSION['admin_otp_user_id']);
            $_SESSION['toast_msg'] = 'تم منح صلاحيات المدير بنجاح.';
            $_SESSION['toast_type'] = 'success';
        } else {
            $_SESSION['toast_msg'] = 'طلب غير صالح.';
            $_SESSION['toast_type'] = 'error';
        }
    }
    header('Location: /admin/users');
    exit;
}

    public function ban(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !CSRF::validate($_POST['csrf_token'])) {
                $_SESSION['toast_msg'] = 'فشل التحقق من أمان الطلب.';
                $_SESSION['toast_type'] = 'error';
                header('Location: /admin/users');
                exit;
            }

            $userModel = new User();
            $id = intval($_POST['user_id'] ?? 0);
            $current_status = intval($_POST['current_status'] ?? 0);
            if ($id > 0 && $id !== (int)Session::get('user_id')) {
                $new_status = $current_status ? 0 : 1;
                $userModel->toggleBan($id, $new_status);
                $_SESSION['toast_msg'] = $new_status ? 'تم حظر المستخدم بنجاح. لن يتمكن من تسجيل الدخول.' : 'تم فك الحظر عن المستخدم.';
                $_SESSION['toast_type'] = 'success';
            }
        }
        header('Location: /admin/users');
        exit;
    }

    public function delete(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !CSRF::validate($_POST['csrf_token'])) {
                $_SESSION['toast_msg'] = 'فشل التحقق من أمان الطلب.';
                $_SESSION['toast_type'] = 'error';
                header('Location: /admin/users');
                exit;
            }
            $userModel = new User();
            $id = intval($_POST['id'] ?? 0);
            if ($id > 0 && $id !== (int)Session::get('user_id')) {
                $userModel->delete($id);
                $_SESSION['toast_msg'] = 'تم حذف المستخدم بنجاح.';
                $_SESSION['toast_type'] = 'success';
            }
        }
        header('Location: /admin/users');
        exit;
    }
}