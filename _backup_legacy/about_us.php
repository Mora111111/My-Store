<?php session_start(); ?>
<!DOCTYPE html>

<html lang="en" dir="rtl">

<head>

  <meta charset="UTF-8" />

  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <link rel="stylesheet" href="css/all.min.css" />

  <link rel="stylesheet" href="style.css" />

  <link rel="icon" href="images/icons/shopping-cart_head.png">

  <title>MY Store - من نحن</title>

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

    <section class="about_us_page container">

      <h2 class="main_title">من نحن</h2>



      <div class="about_us_content">

        <h3 class="about_brand">MY Store</h3>



        <p class="about_us_lead">

          منصة الكترونية متخصصة في بيع أحدث الأجهزة الذكية والإلكترونيات الأصلية داخل السوق المصري برؤية واضحة تقوم

          على الجودة

          الثقة والقيمة الحقيقية للعميل

        </p>



        <p class="about_us_lead">

          نعمل على توفير مجموعة منتقاة بعناية من الهواتف الذكية أجهزة اللابتوب، الأجهزة اللوحية الساعات الذكية

          والإكسسوارات التقنية من العلامات التجارية العالمية الرائدة مع ضمان رسمي يضمن أصالة المنتج وأعلى معايير

          الأداء

        </p>



        <p class="about_us_lead">

          نلتزم بتقديم تجربة شراء احترافية تبدأ من سهولة التصفح والطلب عبر متجرنا الإلكتروني مرورًا بخيارات دفع مرنة

          وأنظمة تقسيط مناسبة وصولًا إلى خدمة شحن آمنة وسريعة تغطي كفر الشيخ وجميع محافظات مصر

        </p>



        <p class="about_us_lead">

          كما يضم فريقنا دعمًا فنيًا متخصصًا لمساعدتك في اختيار الجهاز الأنسب لاحتياجاتك، مع متابعة مستمرة وخدمة ما

          بعد

          البيع تعكس التزامنا الكامل برضا عملائنا

        </p>

      </div>

    </section>



    <section class="team_section container" style="padding-bottom: 100px;">

      <h2 class="main_title">شركاء النجاح</h2>

      <div class="grid_content">



        <div class="testimonial_box">

          <i class="fa-solid fa-laptop-code quote_icon"></i>

          <h2>Amr Mansour</h2>

          <p style="font-weight: 700; color: var(--black-color); margin: 5px 0;">رئيس قسم IT</p>

          <p>مسؤول عن تطوير وبناء المنصة الإلكترونية وضمان أمان وحماية بيانات العملاء.</p>

        </div>



        <div class="testimonial_box">

          <i class="fa-solid fa-cart-shopping quote_icon"></i>

          <h3>⏸️</h3>

          <p style="font-weight: 700; color: var(--black-color); margin: 5px 0;">قسم المبيعات</p>

          <p>يتولى إدارة المخزون وتوفير أفضل العروض السعرية للأجهزة الحديثة.</p>

        </div>



        <div class="testimonial_box">

          <i class="fa-solid fa-bullhorn quote_icon"></i>

          <h3>⏸️</h3>

          <p style="font-weight: 700; color: var(--black-color); margin: 5px 0;">قسم التسويق</p>

          <p>مسؤول عن الحملات الإعلانية ووصول منتجاتنا لكل مكان في مصر .</p>

        </div>



        <div class="testimonial_box">

          <i class="fa-solid fa-users-gear quote_icon"></i>

          <h3>⏸️</h3>

          <p style="font-weight: 700; color: var(--black-color); margin: 5px 0;">قسم HR</p>

          <p>مسؤول عن تنظيم العمل داخل الفريق وتطوير مهارات خدمة العملاء.</p>

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



    <p class="copyright container">جميع الحقوق محفوظة.MY Store &copy; 2025 - 2026</p>

  </footer>

  <script src="Js/app.js"></script>

  <script src="Js/scroll.js"></script>



</body>



</html>
