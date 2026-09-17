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

                    // تأمين الجلسة بتوليد معرف جديد (Session Fixation Prevention)
                    session_regenerate_id(true);

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
            $turnstile_response = $_POST['cf-turnstile-response'] ?? '';

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
                $turnstile_secret = '0x4AAAAAAE6g1I4o1QA8uOHnemJ29zNiqyQ';
                $verify_url = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';
                $data = [
                    'secret' => $turnstile_secret,
                    'response' => $turnstile_response,
                    'remoteip' => $_SERVER['REMOTE_ADDR']
                ];
                
                $options = [
                    'http' => [
                        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                        'method'  => 'POST',
                        'content' => http_build_query($data)
                    ]
                ];
                $context  = stream_context_create($options);
                $result = file_get_contents($verify_url, false, $context);
                $cf_data = json_decode($result);

                if (!$cf_data || !$cf_data->success) {
                    $error = "فشل التحقق الأمني. يرجى التحقق من أنك لست روبوت والمحاولة مجدداً.";
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

    private const GOOGLE_REDIRECT_URI = 'https://my-store-pz2s.onrender.com/auth/google/callback';

    public function googleLogin(): void
    {
        require_once APP_DIR . '/Models/Setting.php';
        $settingModel = new Setting();
        $settings = $settingModel->getSettings();

        // التحقق من أن المشتري قام بإدخال مفاتيحه
        if (empty($settings['google_client_id'])) {
            header('Location: /login?error=تسجيل الدخول بجوجل غير مفعل حالياً');
            exit;
        }

        $url = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query([
            'client_id' => $settings['google_client_id'],
            'redirect_uri' => self::GOOGLE_REDIRECT_URI,
            'response_type' => 'code',
            'scope' => 'email profile',
            'access_type' => 'online',
            'prompt' => 'select_account'
        ]);
        header('Location: ' . $url);
        exit;
    }

    public function googleCallback(): void
    {
        require_once APP_DIR . '/Models/Setting.php';
        $settingModel = new Setting();
        $settings = $settingModel->getSettings();

        if (isset($_GET['code'])) {
            $ch = curl_init('https://oauth2.googleapis.com/token');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                'client_id' => $settings['google_client_id'],
                'client_secret' => $settings['google_client_secret'],
                'redirect_uri' => self::GOOGLE_REDIRECT_URI,
                'grant_type' => 'authorization_code',
                'code' => $_GET['code']
            ]));
            $response = curl_exec($ch);
            curl_close($ch);
            $data = json_decode($response, true);

            if (isset($data['access_token'])) {
                $ch2 = curl_init('https://www.googleapis.com/oauth2/v2/userinfo');
                curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch2, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $data['access_token']]);
                $userInfoJson = curl_exec($ch2);
                curl_close($ch2);
                $userInfo = json_decode($userInfoJson, true);

                if (isset($userInfo['email'])) {
                    $userModel = new User();
                    $existingUser = $userModel->findByEmail($userInfo['email']);
                    
                    if ($existingUser) {
                        Session::set('user_id', $existingUser['id']);
                        Session::set('user_name', $existingUser['name']);
                        Session::set('user_role', $existingUser['role']);
                    } else {
                        $randomPassword = bin2hex(random_bytes(8));
                        $userModel->create([
                            'name' => $userInfo['name'] ?? 'مستخدم جوجل',
                            'email' => $userInfo['email'],
                            'password' => $randomPassword,
                            'role' => 'user'
                        ]);
                        $newUser = $userModel->findByEmail($userInfo['email']);
                        Session::set('user_id', $newUser['id']);
                        Session::set('user_name', $newUser['name']);
                        Session::set('user_role', $newUser['role']);
                    }
                    header('Location: /');
                    exit;
                }
            }
        }
        header('Location: /login?error=حدث خطأ أثناء تسجيل الدخول بواسطة جوجل');
        exit;
    }
}