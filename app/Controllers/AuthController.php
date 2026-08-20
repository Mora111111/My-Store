<?php

class AuthController
{
    public function showLogin(): void
    {
        require_once APP_DIR . '/Views/layouts/header.php';
        require_once APP_DIR . '/Views/pages/login.php';
        require_once APP_DIR . '/Views/layouts/footer.php';
    }
public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userModel = new User();
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $user = $userModel->findByEmail($email);

            if ($user) {
                if ($user['is_banned']) {
                    Session::set('login_error', 'هذا الحساب محظور من قبل الإدارة.');
                    header('Location: /login');
                    exit;
                }

                $db = Database::getInstance()->getConnection();

                if (!empty($user['lockout_until']) && strtotime($user['lockout_until']) > time()) {
                    Session::set('login_error', 'تم قفل الحساب مؤقتاً لمحاولات متكررة. حاول لاحقاً.');
                    header('Location: /login');
                    exit;
                }

                if (password_verify($password, $user['password'])) {
                    $db->prepare("UPDATE elogin SET failed_attempts = 0, lockout_until = NULL WHERE id = ?")->execute([$user['id']]);

                    Session::set('user_id', $user['id']);
                    Session::set('user_name', $user['name']);
                    Session::set('user_role', $user['role']);
                    
                    if ($user['role'] === 'admin') {
                        header('Location: /admin');
                    } else {
                        header('Location: /');
                    }
                    exit;
                } else {
                    $attempts = $user['failed_attempts'] + 1;
                    $lockout = null;
                    if ($attempts >= 5) {
                        $lockout = date('Y-m-d H:i:s', time() + 900);
                    }
                    $db->prepare("UPDATE elogin SET failed_attempts = ?, lockout_until = ? WHERE id = ?")->execute([$attempts, $lockout, $user['id']]);
                }
            }
            
            Session::set('login_error', 'بيانات الدخول غير صحيحة.');
            header('Location: /login');
            exit;
        }
    }

    public function showSignup(): void
    {
        $error = "";
        $success = "";
        require_once APP_DIR . '/Views/layouts/header.php';
        require_once APP_DIR . '/Views/pages/signup.php';
        require_once APP_DIR . '/Views/layouts/footer.php';
    }

    public function register(): void
    {
        $error = "";
        $success = "";

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            $accept = $_POST['accept'] ?? '';

            if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
                $error = "الرجاء تعبئة جميع الحقول.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = "صيغة البريد الإلكتروني غير صحيحة.";
            } elseif ($accept !== 'yes') {
                $error = "يرجى الموافقة على الشروط والأحكام.";
            } elseif (strlen($password) < 8) {
                $error = "كلمة المرور يجب أن تكون 8 أحرف على الأقل.";
            } elseif ($password !== $confirm_password) {
                $error = "كلمات المرور غير متطابقة.";
            } else {
                $username = htmlspecialchars($username, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                $userModel = new User();
                if ($userModel->findByEmail($email)) {
                    $error = "هذا البريد الإلكتروني مسجل بالفعل.";
                } else {
                    $created = $userModel->create([
                        'name' => $username,
                        'email' => $email,
                        'password' => $password,
                        'role' => 'user'
                    ]);
                    if ($created) {
                        header('Location: /login?registered=1');
                        exit;
                    } else {
                        $error = "حدث خطأ أثناء التسجيل. الرجاء المحاولة مرة أخرى.";
                    }
                }
            }
        }

        require_once APP_DIR . '/Views/layouts/header.php';
        require_once APP_DIR . '/Views/pages/signup.php';
        require_once APP_DIR . '/Views/layouts/footer.php';
    }

    public function logout(): void
    {
        Session::destroy();
        header('Location: /');
        exit;
    }
}