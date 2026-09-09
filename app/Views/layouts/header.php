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

  <!-- درع الحماية وتنسيقات الاستجابة للهواتف (مستقل تماماً) -->
  <style>
    /* الإعدادات الأساسية للهيدر */
    .master-header { background: #fff; box-shadow: 0 2px 15px rgba(0,0,0,0.04); position: relative; z-index: 1000; width: 100%; }
    .header-wrapper { display: flex; align-items: center; justify-content: space-between; padding: 15px 40px; max-width: 1400px; margin: 0 auto; box-sizing: border-box; }
    
    /* تقسيم الهيدر إلى مجموعتين: يمين ويسار */
    .header-right { display: flex; align-items: center; gap: 40px; }
    .header-left { display: flex; align-items: center; gap: 20px; flex: 1; justify-content: flex-end; }

    /* فئات الإظهار والإخفاء الذكية */
    .mobile-only { display: none !important; }
    .desktop-only { display: flex !important; align-items: center; gap: 20px; }

    /* تنسيق روابط الكمبيوتر */
    .nav-list { display: flex; align-items: center; gap: 25px; list-style: none; padding: 0; margin: 0; }
    .nav_link { color: #475569; font-size: 16px; font-weight: 700; text-decoration: none; transition: 0.2s; }
    .nav_link:hover { color: var(--main-color); }
    .nav_link.active { color: var(--main-color); }

    /* شريط البحث المطور */
    .search-box-container { width: 100%; max-width: 450px; }
    .smart-search-form { display: flex; align-items: center; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 25px; padding: 6px 20px; transition: 0.3s; box-shadow: inset 0 2px 4px rgba(0,0,0,0.02); }
    .smart-search-form:focus-within { border-color: var(--main-color); box-shadow: 0 0 0 3px rgba(14,165,233,0.1); background: #fff; }
    .smart-search-form input { border: none; background: transparent; width: 100%; padding: 8px 0; outline: none; font-family: inherit; font-size: 15px; color: #334155; }
    .smart-search-form button { background: none; border: none; color: var(--main-color); font-size: 18px; cursor: pointer; margin-left: 10px; }

    /* --- وضع الموبايل --- */
    @media (max-width: 800px) {
        .header-wrapper { display: flex; flex-direction: column; padding: 15px 20px !important; gap: 15px; }
        
        .header-right { width: 100%; justify-content: flex-start; gap: 15px; margin: 0; }
        .header-left { width: 100%; justify-content: center; }
        
        /* إخفاء أيقونات الكمبيوتر وإظهار قوائم الموبايل */
        .desktop-only { display: none !important; } 
        .mobile-only { display: flex !important; } 
        
        /* ترتيب العناصر في الموبايل */
        .nav_toggle { font-size: 26px !important; color: var(--main-color) !important; cursor: pointer; margin: 0 !important; }
        .logo-container { margin: 0 auto; padding-right: 30px; } /* لضبط التوسيط البصري */
        .nav_logo { max-height: 40px !important; width: auto !important; }
        
        .search-box-container { max-width: 100%; }
        
        /* تصميم القائمة الجانبية المنسدلة */
        .nav_menu { 
            position: fixed;
            top: 0; right: -100%; /* يبدأ مخفياً خارج الشاشة */
            background: #ffffff !important; 
            width: 320px !important; 
            height: 100vh;
            max-width: 85% !important; 
            padding: 0 !important; 
            box-shadow: -5px 0 30px rgba(0,0,0,0.15) !important;
            display: flex !important;
            flex-direction: column !important;
            align-items: stretch !important;
            transition: 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1005;
            overflow-y: auto;
        }
        .nav_menu.show_menu { right: 0; }
        .nav_menu_close { position: absolute !important; top: 20px !important; left: 20px !important; color: #ef4444 !important; font-size: 24px !important; cursor: pointer; display: block !important; }
        
        /* أزرار السلة والحساب داخل القائمة الجانبية */
        .sidebar-actions-panel {
            background: #f8fafc;
            padding: 60px 20px 20px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .sidebar-btn {
            display: flex;
            align-items: center;
            gap: 12px;
            background: #fff;
            padding: 14px 15px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            color: #0f172a;
            font-weight: 700;
            text-decoration: none;
            transition: 0.3s;
        }
        .sidebar-btn i { color: var(--main-color); font-size: 20px; width: 25px; text-align: center; }
        
        /* روابط الصفحات في الموبايل */
        .nav-list { padding: 15px 20px !important; margin: 0 !important; flex-direction: column !important; width: 100% !important; box-sizing: border-box !important; align-items: flex-start !important; }
        .nav-item { width: 100%; margin: 0; }
        .nav_link { 
            color: #475569 !important; 
            font-size: 16px !important; 
            padding: 14px 10px !important; 
            border-bottom: 1px solid #f1f5f9 !important; 
            display: block !important;
            font-weight: 600 !important;
        }
        .nav_link.active { color: var(--main-color) !important; padding-right: 20px !important; background: transparent; }
    }
  </style>
</head>

<body>
  <header class="master-header" id="header">
    <div class="header-wrapper">
      
      <!-- ============================================== -->
      <!-- اليمين: اللوجو + روابط الصفحات -->
      <!-- ============================================== -->
      <div class="header-right">
          <!-- زر الموبايل (يظهر في الموبايل فقط) -->
          <div class="nav_toggle mobile-only" id="nav-toggle">
            <i class="fa-solid fa-bars"></i>
          </div>

          <!-- اللوجو -->
          <a href="/" class="logo-container">
            <img src="/images/logos/logo.png" alt="MY Store Logo" class="nav_logo" style="max-height: 50px;" />
          </a>

          <!-- القائمة (بجوار اللوجو للكمبيوتر / جانبية للموبايل) -->
          <div class="nav_menu" id="nav-menu">
            <i class="fa-solid fa-xmark nav_menu_close mobile-only" id="menu-close"></i>
            
            <!-- قسم الحساب والسلة (يظهر داخل القائمة الجانبية للموبايل فقط) -->
            <div class="mobile-only sidebar-actions-panel">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div style="font-size: 15px; color: #64748b; margin-bottom: 5px; font-weight: bold;">
                        مرحباً بك، <span style="color: var(--main-color);"><?php echo (isset($_SESSION['user_name']) && !empty(trim($_SESSION['user_name']))) ? htmlspecialchars(explode(' ', trim($_SESSION['user_name']))[0]) : 'ضيف'; ?></span>
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
                
                <!-- زر السلة المربوط برمجياً بالسلة الأصلية -->
                <div class="sidebar-btn" onclick="document.getElementById('cart-shop').click();" style="cursor:pointer; justify-content: space-between;">
                    <div style="display:flex; align-items:center; gap:12px;">
                        <i class="fa-solid fa-cart-shopping"></i> <span>سلة المشتريات</span>
                    </div>
                    <span class="cart_count" style="background:#ef4444; color:#fff; padding:2px 8px; border-radius:10px; font-size:13px;">0</span>
                </div>
            </div>

            <!-- روابط الصفحات -->
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

      <!-- ============================================== -->
      <!-- اليسار: البحث + أيقونات الكمبيوتر -->
      <!-- ============================================== -->
      <div class="header-left">
          
          <!-- شريط البحث -->
          <div class="search-box-container">
              <form action="/products" method="GET" class="smart-search-form">
                  <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                  <input type="text" name="search" placeholder="ابحث عن منتجك هنا..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
              </form>
          </div>

          <!-- أيقونات الكمبيوتر (تختفي في الموبايل) -->
          <div class="desktop-only nav_btns">
            <div class="login_toggle profile-dropdown-container">
              <?php if (isset($_SESSION['user_id'])): ?>
                <a href="javascript:void(0);" class="login_link profile-trigger" id="profile-btn">
                  <i class="fa-solid fa-circle-user" style="color: var(--main-color); font-size: 28px;"></i>
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
                <a href="/login" class="login_link"><i class="fa-regular fa-user" style="font-size: 24px; color: var(--main-color);"></i></a>
              <?php endif; ?>
            </div>

            <div class="nav_shop" id="cart-shop" style="cursor:pointer; display:flex; align-items:center; position:relative;">
              <img src="/images/icons/cart.png" alt="" style="width: 28px;">
              <span class="cart_count" style="position:absolute; top:-8px; right:-8px; background:#ef4444; color:#fff; font-size:12px; width:20px; height:20px; display:flex; justify-content:center; align-items:center; border-radius:50%;">0</span>
            </div>
          </div>
          
      </div>

    </div>
  </header>