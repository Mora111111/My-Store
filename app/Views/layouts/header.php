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

  <style>
    /* درع حماية الهيدر - يمنع أي تدخل من ملف style.css القديم */
    .safe-header {
      background-color: rgba(255, 255, 255, 0.98);
      backdrop-filter: blur(8px);
      box-shadow: 0 4px 15px rgba(0,0,0,0.05);
      position: fixed; left: 0; top: 0; width: 100%; z-index: 1000;
    }
    .safe-nav {
      display: flex; justify-content: space-between; align-items: center;
      height: 80px; padding: 0 30px; max-width: 1400px; margin: 0 auto;
    }
    /* 1. قسم اليمين (القوائم والأيقونات) */
    .safe-right {
      display: flex; align-items: center; gap: 30px;
    }
    .safe-icons {
      display: flex; align-items: center; gap: 15px;
    }
    .safe-links {
      display: flex; align-items: center; gap: 20px; list-style: none; margin: 0; padding: 0;
    }
    .safe-links a {
      color: #0f172a; font-weight: 700; font-size: 16px; transition: 0.2s; text-decoration: none;
    }
    .safe-links a:hover, .safe-links a.active { color: var(--main-color); }

    /* 2. قسم المنتصف (البحث) */
    .safe-search {
      flex: 1; max-width: 500px; margin: 0 20px;
      display: flex; align-items: center; background: #f8fafc;
      border: 1px solid #e2e8f0; border-radius: 25px; padding: 5px 20px;
      transition: 0.3s; box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
    }
    .safe-search:focus-within { border-color: var(--main-color); background: #fff; box-shadow: 0 0 0 3px rgba(14,165,233,0.1); }
    .safe-search input { border: none; background: transparent; width: 100%; padding: 8px 0; outline: none; font-size: 14px; font-family: inherit; }
    .safe-search button { background: none; border: none; color: var(--main-color); font-size: 18px; cursor: pointer; margin-left: 10px; }
    
    /* 3. قسم اليسار (اللوجو) */
    .safe-logo img { max-height: 50px; }

    .mobile-toggle { display: none; }
    .mobile-sidebar-content { display: none; }

    /* --- استجابة الموبايل --- */
    @media (max-width: 800px) {
      .safe-nav { height: auto; flex-wrap: wrap; padding: 15px !important; gap: 15px; }
      
      /* ترتيب الموبايل: زر يمين، لوجو يسار، بحث أسفل */
      .safe-right { order: 1; width: auto; gap: 0; }
      .safe-logo { order: 2; margin-right: auto; }
      .safe-search { order: 3; width: 100%; max-width: 100%; margin: 0; flex-basis: 100%; }

      .safe-icons { display: none !important; } /* إخفاء أيقونات الكمبيوتر */
      .mobile-toggle { display: block; font-size: 26px; color: var(--main-color); cursor: pointer; margin-left: 10px; }

      /* القائمة الجانبية */
      .nav_menu {
        position: fixed; top: 0; right: -100%; width: 300px; max-width: 85%; height: 100vh;
        background: #ffffff !important; box-shadow: -5px 0 25px rgba(0,0,0,0.15) !important;
        flex-direction: column; z-index: 1005; transition: 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        overflow-y: auto; padding: 0 !important; display: block !important;
      }
      .nav_menu.show_menu { right: 0 !important; }
      .nav_menu_close { position: absolute; top: 20px; left: 20px; font-size: 24px; color: #ef4444 !important; cursor: pointer; display: block !important;}
      
      .safe-links { flex-direction: column; align-items: flex-start; padding: 10px 20px; width: 100%; box-sizing: border-box; }
      .safe-links li { width: 100%; border-bottom: 1px solid #f1f5f9; }
      .safe-links a { display: block; padding: 15px 5px; color: #334155; }
      .safe-links a.active { color: var(--main-color); padding-right: 15px; }
      
      /* محتوى القائمة الجانبية (حساب وسلة) */
      .mobile-sidebar-content {
        display: flex; flex-direction: column; gap: 10px; padding: 60px 20px 20px; background: #f8fafc; border-bottom: 1px solid #e2e8f0;
      }
      .mob-btn {
        display: flex; align-items: center; justify-content: flex-start; gap: 12px;
        background: #fff; padding: 14px 15px; border-radius: 12px; border: 1px solid #e2e8f0;
        color: #0f172a; font-weight: 700; text-decoration: none; cursor: pointer;
      }
      .mob-btn i { color: var(--main-color); font-size: 20px; width: 25px; text-align: center; }
    }
  </style>
</head>

<body>
  <header class="safe-header" id="header">
    <nav class="safe-nav container">
      
      <!-- 1. أقصى اليمين: القوائم والأيقونات -->
      <div class="safe-right">
        
        <!-- زر الموبايل -->
        <div class="mobile-toggle" id="nav-toggle">
          <i class="fa-solid fa-bars"></i>
        </div>

        <!-- أيقونات الكمبيوتر (تختفي في الموبايل) -->
        <div class="safe-icons nav_btns">
          <div class="login_toggle profile-dropdown-container">
            <?php if (isset($_SESSION['user_id'])): ?>
              <a href="javascript:void(0);" class="login_link profile-trigger" id="profile-btn">
                <i class="fa-solid fa-circle-user" style="color: var(--main-color); font-size: 26px;"></i>
              </a>
              <div class="profile-menu" id="profile-menu">
                <div class="profile-header">
                  مرحباً، <span><?php echo (isset($_SESSION['user_name']) && !empty(trim($_SESSION['user_name']))) ? htmlspecialchars(explode(' ', trim($_SESSION['user_name']))[0]) : 'ضيف'; ?></span> 👋
                </div>
                <ul class="profile-links">
                  <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                    <li><a href="/admin"><i class="fa-solid fa-gauge"></i> لوحة الإدارة</a></li>
                  <?php else: ?>
                    <li><a href="/profile"><i class="fa-solid fa-user-gear"></i> الملف الشخصي</a></li>
                    <li><a href="/my-orders"><i class="fa-solid fa-box-open"></i> طلباتي</a></li>
                    <li><a href="/my-messages"><i class="fa-solid fa-envelope"></i> رسائلي</a></li>
                  <?php endif; ?>
                  <li><a href="/logout" class="logout-link"><i class="fa-solid fa-arrow-right-from-bracket"></i> تسجيل خروج</a></li>
                </ul>
              </div>
            <?php else: ?>
              <a href="/login" class="login_link"><i class="fa-regular fa-user" style="font-size: 22px; color: #000;"></i></a>
            <?php endif; ?>
          </div>

          <div class="nav_shop" id="cart-shop" style="position: relative; display: flex; cursor: pointer;">
            <img src="/images/icons/cart.png" alt="" style="width: 26px;">
            <span class="cart_count" style="position: absolute; top: -5px; right: -8px; background: #e35f26; color: white; width: 18px; height: 18px; border-radius: 50%; font-size: 11px; display: flex; justify-content: center; align-items: center; font-weight: bold;">0</span>
          </div>
        </div>

        <!-- حاوية القوائم (في الكمبيوتر أفقية، وفي الموبايل جانبية) -->
        <div class="nav_menu" id="nav-menu">
          <i class="fa-solid fa-xmark nav_menu_close" id="menu-close"></i>
          
          <!-- أزرار الحساب والسلة (تظهر في الموبايل فقط داخل القائمة الجانبية) -->
          <div class="mobile-sidebar-content">
              <?php if (isset($_SESSION['user_id'])): ?>
                  <div style="font-size: 15px; color: #64748b; margin-bottom: 5px; font-weight: bold;">
                      مرحباً بك، <span style="color: var(--main-color);"><?php echo htmlspecialchars(explode(' ', trim($_SESSION['user_name']))[0] ?? 'ضيف'); ?></span>
                  </div>
                  <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                      <a href="/admin" class="mob-btn"><i class="fa-solid fa-gauge"></i> لوحة الإدارة</a>
                  <?php else: ?>
                      <a href="/profile" class="mob-btn"><i class="fa-solid fa-user-gear"></i> حسابي</a>
                      <a href="/my-orders" class="mob-btn"><i class="fa-solid fa-box-open"></i> طلباتي</a>
                  <?php endif; ?>
                  <a href="/logout" class="mob-btn" style="color: #ef4444;"><i class="fa-solid fa-arrow-right-from-bracket" style="color: #ef4444;"></i> تسجيل خروج</a>
              <?php else: ?>
                  <a href="/login" class="mob-btn"><i class="fa-solid fa-user-lock"></i> تسجيل الدخول</a>
              <?php endif; ?>
              
              <div class="mob-btn" onclick="document.getElementById('cart-shop').click();" style="justify-content: space-between;">
                  <div style="display:flex; align-items:center; gap:12px;">
                      <i class="fa-solid fa-cart-shopping"></i> <span>سلة المشتريات</span>
                  </div>
                  <span class="cart_count" style="background:#ef4444; color:#fff; padding:2px 8px; border-radius:10px; font-size:13px;">0</span>
              </div>
          </div>

          <!-- روابط الصفحات الرئيسية -->
          <ul class="safe-links nav-list">
            <?php $currentUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); ?>
            <li class="nav-item"><a href="/" class="nav_link <?= ($currentUri === '/') ? 'active' : '' ?>">الرئيسية</a></li>
            <li class="nav-item"><a href="/products" class="nav_link <?= ($currentUri === '/products' || $currentUri === '/product') ? 'active' : '' ?>">المنتجات</a></li>
            <li class="nav-item"><a href="/services" class="nav_link <?= ($currentUri === '/services') ? 'active' : '' ?>">الخدمات</a></li>
            <li class="nav-item"><a href="/about" class="nav_link <?= ($currentUri === '/about') ? 'active' : '' ?>">من نحن</a></li>
            <li class="nav-item"><a href="/contact" class="nav_link <?= ($currentUri === '/contact') ? 'active' : '' ?>">اتصل بنا</a></li>
          </ul>
        </div>
      </div>

      <!-- 2. المنتصف: شريط البحث -->
      <form action="/products" method="GET" class="safe-search">
          <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
          <input type="text" name="search" placeholder="ابحث عن منتجك هنا..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
      </form>

      <!-- 3. أقصى اليسار: اللوجو -->
      <a href="/" class="safe-logo">
        <img src="/images/logos/logo.png" alt="MY Store Logo" class="nav_logo" />
      </a>

    </nav>
  </header>