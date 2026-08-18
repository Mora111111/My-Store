<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="css/all.min.css" />
  <link rel="stylesheet" href="style.css" />
  <link rel="icon" href="images/icons/shopping-cart_head.png">
  <title>MY Store - اتصل بنا</title>
</head>
<body>
  
  <header class="header" id="header">
    <nav class="nav container">
      <div class="nav_box">
        <div class="nav_btns">
          <div class="nav_toggle" id="nav-toggle">
            <i class="fa-solid fa-bars"></i>
          </div>

          <div class="login_toggle profile-dropdown-container">
            <?php if(isset($_SESSION['user_id'])): ?>
              <a href="javascript:void(0);" class="login_link profile-trigger" id="profile-btn">
                <i class="fa-solid fa-circle-user" style="color: var(--main-color); font-size: 26px;"></i>
              </a>
              <div class="profile-menu" id="profile-menu">
                  <div class="profile-header">
                      مرحباً، <span><?php echo (isset($_SESSION['user_name']) && !empty(trim($_SESSION['user_name']))) ? htmlspecialchars(explode(' ', trim($_SESSION['user_name']))[0]) . ' ' : ''; ?></span> 👋
                  </div>
                  <ul class="profile-links">
                      <li>
                          <a href="dashboard.php">
                              <i class="fa-solid fa-id-badge"></i> الملف الشخصي
                          </a>
                      </li>
                      <li>
                          <a href="logout.php" class="logout-link">
                              <i class="fa-solid fa-arrow-right-from-bracket"></i> تسجيل خروج
                          </a>
                      </li>
                  </ul>
              </div>
            <?php else: ?>
              <a href="signin.php" class="login_link">
                <i class="fa-regular fa-user"></i>
              </a>
            <?php endif; ?>
          </div>
        </div>

        <div class="nav_menu" id="nav-menu">
          <i class="fa-solid fa-xmark nav_menu_close" id="menu-close"></i>
          <ul class="nav-list">
            <li class="nav-item">
              <a href="index.php" class="nav_link">الرئيسية</a>
            </li>
            <li class="nav-item">
              <a href="products.php" class="nav_link">المنتجات</a>
            </li>
            <li class="nav-item">
              <a href="services.php" class="nav_link">الخدمات</a>
            </li>
            <li class="nav-item">
              <a href="about_us.php" class="nav_link">من نحن</a>
            </li>
            <li class="nav-item">
              <a href="contact_us.php" class="nav_link active">اتصل بنا</a>
            </li>
          </ul>
        </div>
      </div>
      <img src="images/logos/logo.png" alt="MY Store Logo" class="nav_logo" />
    </nav>
  </header>

  <div class="contact_us">
    <div class="contact_box container">
      
      <div class="contact form">
        <h3 class="title">أرسل لنا رسالة</h3>

        <form action="send_message.php" method="POST">
          <div class="form_box">
            <div class="row_50">
              <div class="input_box">
                <span>الأسم الأول</span>
                <input type="text" name="first_name" placeholder="الاسم الأول" required />
              </div>
              <div class="input_box">
                <span>الأسم الأخير</span>
                <input type="text" name="last_name" placeholder="الاسم الأخير" required />
              </div>
            </div>

            <div class="row_50">
              <div class="input_box">
                <span>الإيميل/المسجل به فقط</span>
                <input type="email" name="email" placeholder="البريد الإلكتروني" required />
              </div>
              <div class="input_box">
                <span>رقم الهاتف / واتساب</span>
                <input type="text" name="phone" placeholder="رقم الهاتف" required />
              </div>
            </div>

            <div class="row_100">
              <div class="input_box">
                <span>الرسالة</span>
                <textarea name="message" placeholder="اكتب رسالتك أو استفسارك هنا..." required></textarea>
              </div>
            </div>

            <div class="row_100">
              <div class="input_box">
                <input type="submit" value="ارسال" />
              </div>
            </div>
          </div>
        </form>
      </div>

      <div class="contact info">
        <h3 class="title">تواصل معنا</h3>
        <div>
          <i class="fa-solid fa-phone footer-icon"></i>
          <a href="#">01017******</a>
        </div>
        <div>
          <i class="fa-solid fa-phone footer-icon"></i>
          <a href="#">01034******</a>
        </div>
        <div>
          <i class="fa-solid fa-envelope footer-icon"></i>
          <a href="#">MY-Store@gmail.com</a>
        </div>
        <div>
          <i class="fa-solid fa-location-dot footer-icon"></i>
          <a href="#">كفر الشيخ - مصر</a>
        </div>
      </div>

      <div class="contact map">
        <img src="images/map.png" alt="Map Location" style="width:100%; height:100%; border-radius:10px; object-fit:cover;" />
      </div>
    </div>
  </div>

  <footer class="footer">
    <div class="footer_container container grid_content">
      <div class="footer_item">
        <h3 class="footer_title">معلومات عنا</h3>
        <p class="footer_p">
          نحن متجر على الإنترنت نقدم أفضل المنتجات ذات الجودة العالية والسعر المثالي والتسليم
          السريع
        </p>
        <img src="images/logos/logo-white.png" alt="" class="footer_img" />
      </div>

      <div class="footer_item">
        <h3 class="footer_title">الحساب</h3>
        <ul class="footer_list">
          <li class="footer_li">
            <a href="signin.php" class="footer_link">تسجيل الدخول</a>
          </li>
          <li class="footer_li">
            <a href="signup.php" class="footer_link">إنشاء حساب</a>
          </li>
          <li class="footer_li">
            <a href="#" class="footer_link"></a>
          </li>
          <li class="footer_li">
            <a href="products.php" class="footer_link">المنتجات</a>
          </li>
        </ul>
      </div>

      <div class="footer_item">
        <h3 class="footer_title">الروابط</h3>
        <ul class="footer_list">
          <li class="footer_li">
            <a href="services.php" class="footer_link">الخدمات</a>
          </li>
          <li class="footer_li">
            <a href="about_us.php" class="footer_link">من نحن</a>
          </li>
          <li class="footer_li">
            <a href="index.php#features" class="footer_link">المنتجات المميزة
            </a>
          </li>
          <li class="footer_li">
            <a href="index.php#latest" class="footer_link">أحدث المنتجات</a>
          </li>
          <li class="footer_li">
            <a href="contact_us.php" class="footer_link">اتصل بنا</a>
          </li>
        </ul>
      </div>

      <div class="footer_item">
        <h3 class="footer_title">اتصل بنا</h3>
        <ul class="footer_list">
          <li class="footer_li">
            <i class="fa-solid fa-phone footer-icon"></i>
            <span>01017******</span>
          </li>
          <li class="footer_li">
            <i class="fa-solid fa-phone footer-icon"></i>
            <span>01034******</span>
          </li>
          <li class="footer_li">
            <i class="fa-solid fa-envelope footer-icon"></i>
            <span>MY-Store@gmail.com</span>
          </li>
          <li class="footer_li">
            <i class="fa-solid fa-location-dot footer-icon"></i>
            <span>كفر الشيخ - مصر</span>
          </li>
        </ul>
      </div>
    </div>

    <p class="copyright container">
      جميع الحقوق محفوظة.MY Store &copy; 2025 - 2026
    </p>
  </footer>
  
  <script src="Js/app.js"></script>
  <script src="Js/scroll.js"></script>

</body>
</html>