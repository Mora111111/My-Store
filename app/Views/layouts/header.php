<?php
$maintenanceSetting = new Setting();
$sysSettings = $maintenanceSetting->getSettings();
if (!empty($sysSettings['maintenance_mode'])) {
    $isAdmin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    $currentUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $isLoginRoute = ($currentUri === '/login');

    if (!$isAdmin && !$isLoginRoute) {
        echo '<!DOCTYPE html><html lang="ar" dir="rtl"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>الموقع تحت الصيانة</title><link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet"><style>body{font-family: "Tajawal", sans-serif; background:#f8fafc; display:flex; justify-content:center; align-items:center; height:100vh; margin:0; padding:20px;} .box{text-align:center; background:#fff; padding:50px 30px; border-radius:20px; box-shadow:0 10px 25px rgba(0,0,0,0.05); max-width:500px;} h1{color:#0f172a; font-size:28px; margin-bottom:15px;} p{color:#64748b; font-size:18px; line-height:1.6;}</style></head><body><div class="box"><img src="/images/logos/logo.png" alt="Logo" style="max-height:80px; margin-bottom:20px;"><h1>نعود إليكم قريباً 🛠️</h1><p>المتجر مغلق حالياً لإجراء بعض التحديثات وأعمال الصيانة لتقديم تجربة تسوق أفضل.<br>شكراً لتفهمكم!</p></div></body></html>';
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="/css/all.min.css" />
  <link rel="stylesheet" href="/style.css" />
  <link rel="icon" href="/images/icons/shopping-cart_head.png">
  <title>MY Store - متجر على الإنترنت</title>
  <meta name="csrf-token" content="<?= CSRF::generate() ?>">
</head>

<body>
  <header class="header" id="header">
    <div class="container header-wrapper">
      
      <!-- زر القائمة الجانبية للموبايل -->
      <div class="nav_toggle" id="nav-toggle">
        <i class="fa-solid fa-bars"></i>
      </div>

      <!-- اللوجو -->
      <a href="/" class="logo-link">
        <img src="/images/logos/logo.png" alt="MY Store Logo" class="nav_logo" />
      </a>

      <!-- شريط البحث -->
      <div class="header-search-container">
        <form action="/products" method="GET" class="search-form">
            <button type="submit" class="search-btn"><i class="fa-solid fa-magnifying-glass"></i></button>
            <input type="text" name="search" placeholder="ابحث عن منتجك هنا..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" class="search-input">
        </form>
      </div>

      <!-- القائمة (أفقية للكمبيوتر - جانبية للموبايل) -->
      <div class="nav_menu" id="nav-menu">
        
        <!-- رأس القائمة الجانبية (يظهر للموبايل فقط) -->
        <div class="menu-mobile-header">
           <img src="/images/logos/logo.png" alt="MY Store" class="mobile-menu-logo" style="width: 90px;" />
           <i class="fa-solid fa-xmark nav_menu_close" id="menu-close"></i>
        </div>

        <!-- أيقونات الحساب والسلة (مدمجة داخل القائمة) -->
        <div class="nav_actions">
          <div class="login_toggle profile-dropdown-container">
            <?php if (isset($_SESSION['user_id'])): ?>
              <a href="javascript:void(0);" class="login_link profile-trigger" id="profile-btn">
                <i class="fa-solid fa-circle-user"></i> <span class="action-text">حسابي</span>
              </a>
              <div class="profile-menu" id="profile-menu">
                <div class="profile-header">
                  مرحباً،
                  <span><?php echo (isset($_SESSION['user_name']) && !empty(trim($_SESSION['user_name']))) ? htmlspecialchars(explode(' ', trim($_SESSION['user_name']))[0]) : 'ضيف'; ?></span>
                  👋
                </div>
                <ul class="profile-links">
                  <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                    <li><a href="/admin"><i class="fa-solid fa-gauge"></i> لوحة الإدارة</a></li>
                  <?php else: ?>
                    <li><a href="/profile"><i class="fa-solid fa-user-gear"></i> الملف الشخصي</a></li>
                    <li><a href="/my-orders"><i class="fa-solid fa-box-open"></i> طلباتي</a></li>
                    <li><a href="/my-messages"><i class="fa-solid fa-envelope"></i> رسائلي</a></li>
                  <?php endif; ?>
                  <li>
                    <a href="/logout" class="logout-link"><i class="fa-solid fa-arrow-right-from-bracket"></i> تسجيل خروج</a>
                  </li>
                </ul>
              </div>
            <?php else: ?>
              <a href="/login" class="login_link"><i class="fa-solid fa-user-lock"></i> <span class="action-text">تسجيل الدخول</span></a>
            <?php endif; ?>
          </div>

          <div class="nav_shop" id="cart-shop">
            <i class="fa-solid fa-cart-shopping"></i> <span class="action-text">سلة المشتريات</span>
            <span class="cart_count">0</span>
          </div>
        </div>

        <!-- روابط الصفحات الرئيسية -->
        <ul class="nav-list">
          <?php $currentUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); ?>
          <li class="nav-item"><a href="/" class="nav_link <?= ($currentUri === '/') ? 'active' : '' ?>">الرئيسية</a></li>
          <li class="nav-item"><a href="/products" class="nav_link <?= ($currentUri === '/products' || $currentUri === '/product') ? 'active' : '' ?>">المنتجات</a></li>
          <li class="nav-item"><a href="/services" class="nav_link <?= ($currentUri === '/services') ? 'active' : '' ?>">الخدمات</a></li>
          <li class="nav-item"><a href="/about" class="nav_link <?= ($currentUri === '/about') ? 'active' : '' ?>">من نحن</a></li>
          <li class="nav-item"><a href="/contact" class="nav_link <?= ($currentUri === '/contact') ? 'active' : '' ?>">اتصل بنا</a></li>
        </ul>
      </div>

    </div>
  </header>