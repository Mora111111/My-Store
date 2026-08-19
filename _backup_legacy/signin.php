<?php
session_start();
include 'db.php'; 

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: dashboard.php");
    } else {
        header("Location: index.php");
    }
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = mysqli_real_escape_string($conn, trim($_POST['password']));

    if (empty($email) || empty($password)) {
        $error = "الرجاء إدخال البريد الإلكتروني وكلمة المرور.";
    } else {
        $query = "SELECT * FROM elogin WHERE email = '$email'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);
            if (password_verify($password, $user['password'])) {
                
                if (isset($user['is_banned']) && $user['is_banned'] == 1) {
                    $error = "عفواً، لقد تم حظر هذا الحساب من قبل الإدارة.";
                } else {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['username'] = $user['username'];

                    if ($user['role'] == 'admin') {
                        header("Location: dashboard.php");
                    } else {
                        header("Location: index.php");
                    }
                    exit();
                }

            } else {
                $error = "كلمة المرور غير صحيحة.";
            }
        } else {
            $error = "البريد الإلكتروني غير مسجل.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
    <link rel="stylesheet" href="style.css" />
    <link rel="icon" href="images/icons/shopping-cart_head.png">
    <title> تسجيل / تسجيل الدخول</title>
</head>
<body>
    <header class="header" id="header">
        <nav class="nav container">
            <div class="nav_box">
                <div class="nav_btns">
                    <div class="nav_toggle" id="nav-toggle">
                        <i class="fa-solid fa-bars"></i>
                    </div>

                    <div class="login_toggle">
                        <a href="signin.php" class="login_link">
                            <i class="fa-regular fa-user active_icon"></i>
                        </a>
                    </div>
                </div>

                <div class="nav_menu" id="nav-menu">
                    <i class="fa-solid fa-xmark nav_menu_close" id="menu-close"></i>
                    <ul class="nav-list">
                        <li class="nav-item">
                            <a href="index.php" class="nav_link ">الرئيسية</a>
                        </li>
                        <li class="nav-item">
                            <a href="products.php" class="nav_link">المنتجات</a>
                        </li>
                        <li class="nav-item">
                            <a href="services.php" class="nav_link">الخدمات</a>
                        </li>
                        <li class="nav-item">
                            <a href="contact_us.php" class="nav_link">اتصل بنا</a>
                        </li>
                    </ul>
                </div>
            </div>
            <img src="images/logos/logo.png" alt="Logo" class="nav_logo" />
        </nav>
    </header>

    <div class="form_account">
        <div class="form-container container">
            <div class="form-title">تسجيل الدخول</div>

            <div class="social-form">
                <button class="social-btn">
                    Google
                    <img src="images/logos/google.svg" alt="google" class="social-icon">
                </button>
                <button class="social-btn">
                    Apple
                    <img src="images/logos/apple.svg" alt="Apple" class="social-icon">
                </button>
            </div>

            <p class="separator"><span>أو</span></p>

            <?php if (!empty($error)): ?>
                <div style="color: #d9534f; text-align: center; margin-bottom: 15px; font-weight: bold;"><?php echo $error; ?></div>
            <?php endif; ?>

            <form action="signin.php" method="POST" class="form">
                <div class="input-wrapper" >
                    <input type="email" name="email" placeholder="عنوان البريد الألكتروني" required class="input input-mail">
                    <i class="fa-solid fa-envelope icon_form"></i>
                </div>

                <div class="input-wrapper m-none">
                    <input type="password" name="password" placeholder="كلمة المرور" required class="input input-mail">
                    <i class="fa-regular fa-eye-slash icon_form showPss"></i>
                </div>

                <a href="forgot_password.php" class="forgot-pass-link">هل نسيت كلمة المرور؟</a>
                
                <input type="submit" class="btn-submit" value="تسجيل الدخول">
                
                <p class="signup-text">
                    ليس لديك حساب؟
                    <a href="signup.php">إنشاء حساب</a>
                </p>
            </form>

        </div>
    </div>

    <footer class="footer">
        <div class="footer_container container grid_content">
            <div class="footer_item">
                <h3 class="footer_title">معلومات عنا</h3>
                <p class="footer_p">نحن متجر على الإنترنت نقدم أفضل المنتجات ذات الجودة العالية والتسليم السريع</p>
                <img src="images/logos/logo-white.png" alt="" class="footer_img">
            </div>

            <div class="footer_item">
                <h3 class="footer_title">الحساب</h3>
                <ul class="footer_list">
                    <li class="footer_li"><a href="signin.php" class="footer_link">تسجيل الدخول</a></li>
                    <li class="footer_li"><a href="signup.php" class="footer_link">إنشاء حساب</a></li>
                    <li class="footer_li"><a href="#" class="footer_link"></a></li>
                    <li class="footer_li"><a href="products.php" class="footer_link">المنتجات</a></li>
                </ul>
            </div>

            <div class="footer_item">
                <h3 class="footer_title">الروابط</h3>
                <ul class="footer_list">
                    <li class="footer_li"><a href="services.php" class="footer_link">الخدمات</a></li>
                    <li class="footer_li"><a href="index.php#features" class="footer_link">المنتجات المميزة</a></li>
                    <li class="footer_li"><a href="index.php#latest" class="footer_link">أحدث المنتجات</a></li>
                    <li class="footer_li"><a href="contact_us.php" class="footer_link">اتصل بنا</a></li>
                </ul>
            </div>

            <div class="footer_item">
                <h3 class="footer_title">اتصل بنا</h3>
                <ul class="footer_list">
                    <li class="footer_li"><i class="fa-solid fa-phone footer-icon"></i><span>01017******</span></li>
                    <li class="footer_li"><i class="fa-solid fa-phone footer-icon"></i><span>01034******</span></li>
                    <li class="footer_li"><i class="fa-solid fa-envelope footer-icon"></i><span> MY-Store@gmail.com</span></li>
                    <li class="footer_li"><i class="fa-solid fa-location-dot footer-icon"></i><span>المحافظات - مصر</span></li>
                </ul>
            </div>
        </div>

        <p class="copyright container">جميع الحقوق محفوظة. MY Store &copy; 2025 - 2026</p>
    </footer>

    <script src="Js/scroll.js"></script>
    <script src="Js/account.js"></script>
</body>
</html>