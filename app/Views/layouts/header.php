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

  <!-- درع الحماية وتنسيق الهيدر النظيف (نفس الترتيب الأصلي) -->
  <style>
    .clean-header {
      background-color: rgba(255, 255, 255, 0.98);
      backdrop-filter: blur(8px);
      box-shadow: 0 4px 15px rgba(0,0,0,0.05);
      position: fixed; left: 0; top: 0; width: 100%; z-index: 1000;
    }
    .clean-nav {
      height: 80px; display: flex; justify-content: space-between; align-items: center; gap: 20px;
      padding: 0 30px; max-width: 1400px; margin: 0 auto; box-sizing: border-box;
    }
    
    /* شريط البحث في المنتصف */
    .clean-search-box {
      flex: 1; max-width: 550px; display: flex; align-items: center; margin: 0 2vw;
      background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 25px; padding: 5px 20px; transition: 0.3s; box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
    }
    .clean-search-box:focus-within { border-color: var(--main-color); box-shadow: 0 0 0 3px rgba(14,165,233,0.1); background: #fff; }
    .clean-search-box input { border: none; background: transparent; width: 100%; padding: 8px 0; outline: none; font-size: 14px; font-family: inherit; color: #334155; }
    .clean-search-box button { background: none; border: none; color: var(--main-color); font-size: 18px; cursor: pointer; margin-left: 10px; }
    
    .mobile-sidebar-actions { display: none; }

    /* --- وضع الموبايل --- */
    @media (max-width: 800px) {
      .clean-nav { height: auto; flex-wrap: wrap; padding: 15px 20px !important; gap: 15px; }
      
      /* الترتيب الصحيح للموبايل: الزر يمين، اللوجو يسار، والبحث أسفلهم */
      .nav_box { order: 1; width: auto; gap: 0 !important; }
      .nav_logo-link { order: 2; margin-right: auto; }
      .nav_logo { max-height: 40px !important; width: auto !important; }
      .clean-search-box { order: 3; width: 100%; max-width: 100%; flex-basis: 100%; margin: 0; }

      /* إخفاء الأيقونات من الهيدر وإظهار زر القائمة المنسدلة فقط */
      .nav_btns .login_toggle, .nav_btns .nav_shop { display: none !important; }
      .nav_toggle { display: block !important; font-size: 26px; color: var(--main-color); cursor: pointer; margin-left: 10px; }

      /* القائمة الجانبية الاحترافية */
      .nav_menu {
          position: fixed; top: 0; right: -100%;
          background: #ffffff !important;
          box-shadow: -5px 0 25px rgba(0,0,0,0.15) !important;
          width: 320px !important; max-width: 85% !important; height: 100vh;
          padding: 0 !important;
          display: flex; flex-direction: column; justify-content: flex-start;
          transition: 0.4s cubic-bezier(0.4, 0, 0.2, 1);
          z-index: 1005; overflow-y: auto;
      }
      .nav_menu.show_menu { right: 0 !important; }
      .nav_menu_close { position: absolute; top: 20px; left: 20px; color: #ef4444 !important; font-size: 24px; cursor: pointer; display: block !important;}
      
      /* أزرار السلة والحساب داخل القائمة الجانبية للموبايل */
      .mobile-sidebar-actions {
          display: flex; flex-direction: column; gap: 10px;
          padding: 60px 20px 20px; background: #f8fafc; border-bottom: 1px solid #e2e8f0;
      }
      .sidebar-btn {
          display: flex; align-items: center; justify-content: flex-start; gap: 12px;
          background: #fff; padding: 14px 15px; border-radius: 12px;
          border: 1px solid #e2e8f0; color: #0f172a; font-weight: 700;
          text-decoration: none; cursor: pointer; transition: 0.3s;
      }
      .sidebar-btn i { color: var(--main-color); font-size: 20px; width: 25px; text-align: center; }

      /* روابط الصفحات داخل الموبايل */
      .nav-list { margin-top: 10px !important; padding: 0 20px; width: 100%; flex-direction: column !important; align-items: flex-start !important; }
      .nav-item { width: 100%; margin: 0 !important; }
      .nav_link { color: #334155 !important; font-size: 16px; border-bottom: 1px solid #f1f5f9; padding: 15px 5px !important; display: block !important; font-weight: 600; }
      .nav_link.active { color: var(--main-color) !important; background: transparent !important; border: none !important; border-bottom: 1px solid #f1f5f9 !important; padding-right: 15px !important; }
    }
  </style>
</head>

<body>
  <header class="clean-header" id="header">
    <nav class="nav clean-nav">
      
      <!-- 1. أقصى اليمين: القوائم والأيقونات -->
      <div class="nav_box">
        <div class="nav_btns">
          <!-- زر القائمة الجانبية للموبايل -->
          <div class="nav_toggle" id="nav-toggle">
            <i class="fa-solid fa-bars"></i>
          </div>

          <!-- أيقونات الحساب والسلة (تظهر في الكمبيوتر وتختفي في الموبايل) -->
          <div class="login_toggle profile-dropdown-container">
            <?php if (isset($_SESSION['user_id'])): ?>
              <a href="javascript:void(0);" class="login_link profile-trigger" id="profile-btn">
                <i class="fa-solid fa-circle-user" style="color: var(--main-color); font-size: 26px;"></i>
              </a>
              <div class="profile-menu" id="profile-menu">
                <div class="profile-header">
                  مرحباً،
                  <span><?php echo (isset($_SESSION['user_name']) && !empty(trim($_SESSION['user_name']))) ? htmlspecialchars(explode(' ', trim($_SESSION['user_name']))[0]) : 'ضيف'; ?></span> 👋
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
              <a href="/login" class="login_link"><i class="fa-regular fa-user" style="color:var(--main-color); font-size:24px;"></i></a>
            <?php endif; ?>
          </div>

          <div class="nav_shop" id="cart-shop">
            <img src="/images/icons/cart.png" alt="" style="width: 26px;">
            <span class="cart_count">0</span>
          </div>
        </div>

        <!-- القائمة العلوية -->
        <div class="nav_menu" id="nav-menu">
          <i class="fa-solid fa-xmark nav_menu_close" id="menu-close"></i>
          
          <!-- أزرار الموبايل (الحساب والسلة) تظهر هنا داخل القائمة الجانبية فقط -->
          <div class="mobile-sidebar-actions">
              <?php if (isset($_SESSION['user_id'])): ?>
                  <div style="font-size: 15px; color: #64748b; margin-bottom: 5px; font-weight: bold;">
                      مرحباً بك، <span style="color: var(--main-color);"><?php echo htmlspecialchars(explode(' ', trim($_SESSION['user_name']))[0] ?? 'ضيف'); ?></span>
                  </div>
                  <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                      <a href="/admin" class="sidebar-btn"><i class="fa-solid fa-gauge"></i> لوحة الإدارة</a>
                  <?php else: ?>
                      <a href="/profile" class="sidebar-btn"><i class="fa-solid fa-user-gear"></i> حسابي</a>
                      <a href="/my-orders" class="sidebar-btn"><i class="fa-solid fa-box-open"></i> طلباتي</a>
                  <?php endif; ?>
                  <a href="/logout" class="sidebar-btn" style="color: #ef4444;"><i class="fa-solid fa-arrow-right-from-bracket" style="color: #ef4444;"></i> تسجيل خروج</a>
              <?php else: ?>
                  <a href="/login" class="sidebar-btn"><i class="fa-solid fa-user-lock"></i> تسجيل الدخول</a>
              <?php endif; ?>
              
              <div class="sidebar-btn" onclick="document.getElementById('cart-shop').click();" style="justify-content: space-between;">
                  <div style="display:flex; align-items:center; gap:12px;">
                      <i class="fa-solid fa-cart-shopping"></i> <span>سلة المشتريات</span>
                  </div>
                  <span class="cart_count" style="background:#ef4444; color:#fff; padding:2px 8px; border-radius:10px; font-size:13px;">0</span>
              </div>
          </div>

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

      <!-- 2. المنتصف: شريط البحث المطور -->
      <form action="/products" method="GET" class="clean-search-box">
          <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
          <input type="text" name="search" placeholder="ابحث عن منتجك هنا..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
      </form>

      <!-- 3. أقصى اليسار: اللوجو -->
      <a href="/" class="nav_logo-link">
        <img src="/images/logos/logo.png" alt="MY Store Logo" class="nav_logo" style="max-height: 50px;" />
      </a>

    </nav>
  </header>