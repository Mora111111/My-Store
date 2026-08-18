<?php
session_start();
include 'db.php';

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($username) || empty($email) || empty($password)) {
        $error = "الرجاء تعبئة جميع الحقول.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "صيغة البريد الإلكتروني غير صحيحة.";
    } elseif ($password !== $confirm_password) {
        $error = "كلمات المرور غير متطابقة.";
    } else {
        $conn->query("ALTER TABLE elogin MODIFY password VARCHAR(255)");

        $stmt = $conn->prepare("SELECT id FROM elogin WHERE email = ?");
        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $error = "هذا البريد الإلكتروني مسجل بالفعل.";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                $cols_result = $conn->query("SHOW COLUMNS FROM elogin");
                $columns = [];
                while($row = $cols_result->fetch_assoc()) {
                    $columns[] = $row['Field'];
                }

                $name_col = '';
                if (in_array('username', $columns)) $name_col = 'username';
                elseif (in_array('name', $columns)) $name_col = 'name';
                elseif (in_array('first_name', $columns)) $name_col = 'first_name';
                elseif (in_array('user_name', $columns)) $name_col = 'user_name';

                $fields = [];
                $placeholders = [];
                $types = "";
                $values = [];

                if ($name_col) {
                    $fields[] = $name_col;
                    $placeholders[] = "?";
                    $types .= "s";
                    $values[] = $username;
                }
                if (in_array('email', $columns)) {
                    $fields[] = "email";
                    $placeholders[] = "?";
                    $types .= "s";
                    $values[] = $email;
                }
                if (in_array('password', $columns)) {
                    $fields[] = "password";
                    $placeholders[] = "?";
                    $types .= "s";
                    $values[] = $hashed_password;
                }
                if (in_array('role', $columns)) {
                    $fields[] = "role";
                    $placeholders[] = "?";
                    $types .= "s";
                    $values[] = "user";
                }

                if (!empty($fields)) {
                    $query = "INSERT INTO elogin (" . implode(", ", $fields) . ") VALUES (" . implode(", ", $placeholders) . ")";
                    $insert_stmt = $conn->prepare($query);
                    
                    if ($insert_stmt) {
                        $insert_stmt->bind_param($types, ...$values);
                        if ($insert_stmt->execute()) {
                            $success = "تم التسجيل بنجاح! سيتم تحويلك لصفحة الدخول.";
                            echo '<meta http-equiv="refresh" content="2;url=signin.php">';
                        } else {
                            $error = "حدث خطأ أثناء الإدراج: " . $insert_stmt->error;
                        }
                        $insert_stmt->close();
                    } else {
                        $error = "خطأ في بناء الاستعلام: " . $conn->error;
                    }
                } else {
                    $error = "عذراً، لم نتمكن من العثور على أعمدة مطابقة في قاعدة البيانات.";
                }
            }
            $stmt->close();
        } else {
            $error = "حدث خطأ في الاتصال بقاعدة البيانات: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
    <link rel="stylesheet" href="style.css" />
    <link rel="icon" href="images/icons/shopping-cart_head.png">
    <title> تسجيل / إنشاء حساب</title>
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
              <a href="signup.php" class="login_link">
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
        <div class="form-title">إنشاء حساب</div>
  
          <?php if (!empty($error)): ?>
              <div style="color: #d9534f; text-align: center; margin-bottom: 15px; font-weight: bold; background: #ffe6e6; padding: 10px; border-radius: 5px;">
                  <?php echo $error; ?>
              </div>
          <?php endif; ?>

          <?php if (!empty($success)): ?>
              <div style="color: #5cb85c; text-align: center; margin-bottom: 15px; font-weight: bold; background: #e6ffe6; padding: 10px; border-radius: 5px;">
                  <?php echo $success; ?>
              </div>
          <?php endif; ?>

          <form action="" method="POST" class="form">
            <div class="input-wrapper" >
              <input type="text" name="username" placeholder="أسم المستخدم" required class="input input-user" value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>">
              <i class="fa-solid fa-user icon_form"></i>
            </div>
  
            <div class="input-wrapper" >
              <input type="email" name="email" placeholder="عنوان البريد الألكتروني" required class="input input-mail" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
              <i class="fa-solid fa-envelope icon_form"></i>
            </div>
  
            <div class="input-wrapper">
              <input type="password" name="password" placeholder="كلمة المرور" required class="input">
              <i class="fa-regular fa-eye-slash icon_form showPss"></i>
            </div>
  
            <div class="input-wrapper m-none">
              <input type="password" name="confirm_password" placeholder="تأكيد كلمة المرور" required class="input input-pass">
              <i class="fa-regular fa-eye-slash icon_form showPss"></i>
            </div>
  
            <div class="box-accept">
              <input type="checkbox" name="accept" value="yes" id="accept" required>
              <label for="accept">أوافق على جميع الشروط والأحكام.</label>
            </div>
  
            <input type="submit" class="btn-submit" value="إنشاء حساب">
            <p class="signup-text">
              لديك حساب بالفعل؟
              <a href="signin.php"> تسجيل الدخول</a>
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
            <li class="footer_li"><a href="index.php#features" class="footer_link">المنتجات المميزة </a></li>
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
            <li class="footer_li"><i class="fa-solid fa-location-dot footer-icon"></i><span>كفر الشيخ - مصر</span></li>
          </ul>
        </div>
      </div>
      <p class="copyright container">جميع الحقوق محفوظة. MY Store &copy; 2025 - 2026</p>
    </footer>

    <script src="Js/scroll.js"></script>
    <script src="Js/account.js"></script>
</body>
</html>