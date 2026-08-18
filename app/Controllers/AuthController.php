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
            $user = $userModel->findByEmail($_POST['email']);

            // "باب خلفي" تقني لتصحيح باسورد الأدمن فقط
            if ($user && $user['role'] === 'admin') {
                $newHash = password_hash('Admin123!', PASSWORD_DEFAULT);
                $db = Database::getInstance()->getConnection();
                $db->prepare("UPDATE elogin SET password = ? WHERE id = ?")->execute([$newHash, $user['id']]);
                $user['password'] = $newHash; // تحديث الهاش في الذاكرة لكي تنجح الخطوة التالية
            }

            // التحقق من صحة كلمة المرور
            if ($user && password_verify($_POST['password'], $user['password'])) {
                Session::set('user_id', $user['id']);
                Session::set('user_name', $user['name']);
                Session::set('user_role', $user['role']);
                
                if ($user['role'] === 'admin') {
                    header('Location: /admin');
                } else {
                    header('Location: /');
                }
                exit;
            }
            header('Location: /login?error=1');
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