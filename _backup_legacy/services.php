<?php session_start(); ?>
<!DOCTYPE html>

<html lang="en" dir="rtl">

<head>

  <meta charset="UTF-8" />

  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <link rel="stylesheet" href="css/all.min.css" />

  <link rel="stylesheet" href="style.css" />

  <link rel="icon" href="images/icons/shopping-cart_head.png">

  <title>MY Store - الخدمات</title>

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
                      <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                          <li><a href="dashboard.php"><i class="fa-solid fa-gauge"></i> لوحة الإدارة</a></li>
                      <?php else: ?>
                          <li><a href="my_orders.php"><i class="fa-solid fa-box-open"></i> طلباتي</a></li>
                      <?php endif; ?>
                      <li>
                          <a href="logout.php" class="logout-link"><i class="fa-solid fa-arrow-right-from-bracket"></i> تسجيل خروج</a>
                      </li>
                  </ul>
              </div>
            <?php else: ?>
              <a href="signin.php" class="login_link"><i class="fa-regular fa-user"></i></a>
            <?php endif; ?>
          </div>

          <div class="nav_shop" id="cart-shop">
            <img src="images/icons/cart.png" alt="">
            <span class="cart_count">0</span>
          </div>
        </div>

        <div class="nav_menu" id="nav-menu">
          <i class="fa-solid fa-xmark nav_menu_close" id="menu-close"></i>
          <ul class="nav-list">
            <li class="nav-item"><a href="index.php" class="nav_link">الرئيسية</a></li>
            <li class="nav-item"><a href="products.php" class="nav_link">المنتجات</a></li>
            <li class="nav-item"><a href="services.php" class="nav_link">الخدمات</a></li>
            <li class="nav-item"><a href="about_us.php" class="nav_link">من نحن</a></li>
            <li class="nav-item"><a href="contact_us.php" class="nav_link">اتصل بنا</a></li>
          </ul>
        </div>
      </div>
      <img src="images/logos/logo.png" alt="MY Store Logo" class="nav_logo" />
    </nav>

  </header>

  <div class="cart">

    <h2 class="cart_title">عربة التسوق</h2>

    <div class="cart_content"></div>

    <div class="total">

      <div class="total_title">الاجمالي</div>

      <div class="total_price">. جنية</div>

    </div>

    <a href="products.php" class="btn_buy">شراء</a>

    <div class="cart_empty">

      <div><img src="images/Cart-img.png"></div>

      <p>عربة التسوق فارغة</p>

      <a href="products.php" class="btn_shopping">إستكشف المنتجات</a>

    </div>

    <i class="fa-solid fa-xmark" id="cart-close"></i>

  </div>

  <main class="main">

    <section class="services services_page container">

      <h2 class="main_title">الخدمات</h2>

      <div class="services_horizontal">

        <div class="service">

          <div class="service_icon_wrap">

            <i class="fa-solid fa-wrench service_icon"></i>

          </div>

          <h3 class="service_title">صيانة معتمدة</h3>

          <p class="service_p">خدمة الضمان في حالة وجود تلف في المنتج وتتم المتابعه مع خدمة العملاء</p>

        </div>

        <div class="service">

          <div class="service_icon_wrap">

            <i class="fa-solid fa-envelope service_icon"></i>

          </div>

          <h3 class="service_title">الرد الفوري علي الرسائل</h3>

          <p class="service_p">يتم الرد علي رسائل العملاء بشكل فوري و دوري من قبل الادارة </p>

        </div>

        <div class="service">

          <div class="service_icon_wrap">

            <i class="fa-solid fa-headset service_icon"></i>

          </div>

          <h3 class="service_title">دعم فني متخصص</h3>

          <p class="service_p">خدمات ما بعد البيع تشمل استبدال اجزاء تالفة او اجزاء مكسورة طالما انها تحت الضمان</p>

        </div>

        <div class="service">

          <div class="service_icon_wrap">

            <i class="fa-solid fa-memory service_icon"></i>

          </div>

          <h3 class="service_title">تحديث الأجهزة</h3>

          <p class="service_p">تطوير الهاردوير (RAM/SSD) للابتوبات لرفع كفاءة العمل والدراسة</p>

        </div>

        <div class="service">

          <div class="service_icon_wrap">

            <i class="fa-solid fa-shop service_icon"></i>

          </div>

          <h3 class="service_title">مكان الاستلام</h3>

          <p class="service_p">الاستلام من الفرع الاقرب ليك ويتم التواصل هاتفيا او من خلال Gmail</p>

        </div>

      </div>

    </section>

  </main>

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

          <li class="footer_li">

            <a href="signin.php" class="footer_link">تسجيل الدخول</a>

          </li>

          <li class="footer_li">

            <a href="signup.php" class="footer_link">إنشاء حساب</a>

          </li>

          <li class="footer_li">

            <a href="my_orders.php" class="footer_link">طلباتي</a>

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

            <a href="index.php#features" class="footer_link">المنتجات المميزة </a>

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

            <span dir="ltr">+01017******</span>

          </li>

          <li class="footer_li">

            <i class="fa-solid fa-phone footer-icon"></i>

            <span dir="ltr">+01034******</span>

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

    <p class="copyright container">جميع الحقوق محفوظة.MY Store &copy; 2025 - 2026</p>

  </footer>

  <script src="Js/app.js"></script>

  <script src="Js/scroll.js"></script>

</body>

</html>