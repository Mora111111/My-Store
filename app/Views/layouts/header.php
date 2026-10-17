<?php
$currentLang = $_SESSION['lang'] ?? 'ar';
$pageDir = ($currentLang === 'en') ? 'ltr' : 'rtl';

$maintenanceSetting = new Setting();
$sysSettings = $maintenanceSetting->getSettings();
if (!empty($sysSettings['maintenance_mode'])) {
    $isAdmin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    $currentUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $isLoginRoute = in_array($currentUri, ['/login', '/auth/google/login', '/auth/google/callback', '/switch-lang'], true);

    if (!$isAdmin && !$isLoginRoute) {
        $m_title = lang('maintenance_title');
        $m_heading = lang('maintenance_heading');
        $m_desc = lang('maintenance_desc');
        echo '<!DOCTYPE html><html lang="' . $currentLang . '" dir="' . $pageDir . '"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>' . $m_title . '</title><link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet"><style>body{font-family: "Tajawal", sans-serif; background:#f8fafc; display:flex; justify-content:center; align-items:center; height:100vh; margin:0; padding:20px;} .box{text-align:center; background:#fff; padding:50px 30px; border-radius:20px; box-shadow:0 10px 25px rgba(0,0,0,0.05); max-width:500px;} h1{color:#0f172a; font-size:28px; margin-bottom:15px;} p{color:#64748b; font-size:18px; line-height:1.6;}</style></head><body><div class="box"><img src="/images/logos/logo.png" alt="Logo" style="max-height:80px; margin-bottom:20px;"><h1>' . $m_heading . '</h1><p>' . $m_desc . '</p></div></body></html>';
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="<?= $currentLang ?>" dir="<?= $pageDir ?>">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="/css/all.min.css" />
  <link rel="stylesheet" href="/style.css" />
  <link rel="icon" href="/images/icons/shopping-cart_head.png">
  <title>MY Store - <?= lang('home') ?></title>
  <meta name="csrf-token" content="<?= CSRF::generate() ?>">

  <style>
    /* درع حماية الهيدر */
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
    .safe-right { display: flex; align-items: center; gap: 30px; }
    .safe-icons { display: flex; align-items: center; gap: 15px; }
    .safe-links { display: flex; align-items: center; gap: 20px; list-style: none; margin: 0; padding: 0; }
    .safe-links a { color: #0f172a; font-weight: 700; font-size: 16px; transition: 0.2s; text-decoration: none; }
    .safe-links a:hover, .safe-links a.active { color: var(--main-color); }
    
    .safe-search {
      flex: 1; max-width: 500px; margin: 0 20px; display: flex; align-items: center; background: #f8fafc;
      border: 1px solid #e2e8f0; border-radius: 25px; padding: 5px 20px; transition: 0.3s;
    }
    .safe-search:focus-within { border-color: var(--main-color); background: #fff; box-shadow: 0 0 0 3px rgba(14,165,233,0.1); }
    .safe-search input { border: none; background: transparent; width: 100%; padding: 8px 0; outline: none; font-size: 14px; font-family: inherit; }
    .safe-search button { background: none; border: none; color: var(--main-color); font-size: 18px; cursor: pointer; margin-inline-start: 10px; }
    
    .safe-logo img { max-height: 50px; }
    
    /* زر تغيير اللغة الأنيق */
    .lang-switch-btn {
        background: #f1f5f9; color: #334155; border: 1px solid #e2e8f0; padding: 6px 12px;
        border-radius: 20px; font-size: 14px; font-weight: 700; text-decoration: none;
        display: flex; align-items: center; gap: 6px; transition: 0.3s;
    }
    .lang-switch-btn:hover { background: #e2e8f0; color: var(--main-color); }

    .profile-dropdown-container { position: relative; }
    html[dir="ltr"] .profile-menu::before { left: 25px; right: auto; }
    html[dir="rtl"] .profile-menu::before { right: 25px; left: auto; }

    .mobile-toggle { display: none; }
    .mobile-sidebar-content { display: none; }

    @media (max-width: 800px) {
      .safe-nav { height: auto; flex-wrap: wrap; justify-content: space-between; align-items: center; padding: 0.5rem 3% !important; gap: 0.5rem; }
      .safe-right { order: 1; flex: 0 0 auto; width: auto; display: flex; flex-wrap: wrap; align-items: center; gap: 0; }
      .safe-search { order: 2; flex: 1 1 0%; width: auto; min-width: 0; max-width: none; margin: 0; padding: 0.1875rem 0.625rem; box-sizing: border-box; }
      .safe-search input { width: 100%; min-width: 0; padding: 0.3125rem 0; font-size: 0.8125rem; }
      .safe-search button { font-size: 0.9375rem; margin-inline-start: 0.375rem; padding: 0; flex-shrink: 0; }
      .safe-logo { order: 3; flex: 0 0 auto; margin: 0; display: flex; flex-wrap: wrap; align-items: center; }
      .safe-logo img, .safe-logo .nav_logo { max-height: 2.125rem !important; width: auto !important; max-width: 24vw !important; object-fit: contain; border-radius: 0.375rem; }
      .safe-icons { display: none !important; }
      .mobile-toggle { display: block; font-size: 1.5rem; color: var(--main-color); cursor: pointer; margin: 0; }
      
      .nav_menu {
        position: fixed; top: 0; <?= $pageDir === 'rtl' ? 'right' : 'left' ?>: -100%; width: 82vw; max-width: 85%; height: 100vh;
        background: #ffffff !important; box-shadow: -0.3125rem 0 1.5625rem rgba(0,0,0,0.15) !important;
        flex-direction: column; z-index: 1005; transition: 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        overflow-y: auto; overflow-x: hidden; padding: 0 !important; display: block !important; box-sizing: border-box;
      }
      .nav_menu.show_menu { <?= $pageDir === 'rtl' ? 'right' : 'left' ?>: 0 !important; }
      .nav_menu_close { position: absolute; top: 1.25rem; <?= $pageDir === 'rtl' ? 'left' : 'right' ?>: 1.25rem; font-size: 1.5rem; color: #ef4444 !important; cursor: pointer; display: block !important;}
      
      .safe-links { flex-direction: column; flex-wrap: wrap; align-items: flex-start; padding: 0.625rem 5%; width: 100%; box-sizing: border-box; }
      .safe-links li { width: 100%; border-bottom: 1px solid #f1f5f9; }
      .safe-links a { display: block; padding: 0.9375rem 0.3125rem; color: #334155; }
      
      .mobile-sidebar-content { display: flex; flex-direction: column; flex-wrap: wrap; gap: 0.625rem; padding: 3.25rem 5% 1.25rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0; width: 100%; box-sizing: border-box; }
      .mob-btn { display: flex; flex-wrap: wrap; align-items: center; justify-content: flex-start; gap: 0.75rem; background: #fff; padding: 0.875rem 0.9375rem; border-radius: 0.75rem; border: 1px solid #e2e8f0; color: #0f172a; font-weight: 700; text-decoration: none; cursor: pointer; width: 100%; box-sizing: border-box; }
      .mob-btn i { color: var(--main-color); font-size: 1.25rem; width: 1.5rem; text-align: center; flex-shrink: 0; }
    }
  </style>
</head>

<body>
  <header class="safe-header" id="header">
    <nav class="safe-nav container">
      
      <div class="safe-right">
        
        <div class="mobile-toggle" id="nav-toggle">
          <i class="fa-solid fa-bars"></i>
        </div>

        <div class="safe-icons nav_btns">
          <!-- زر تغيير اللغة -->
          <?php if ($currentLang === 'ar'): ?>
            <a href="/switch-lang?lang=en" class="lang-switch-btn"><i class="fa-solid fa-globe"></i> EN</a>
          <?php else: ?>
            <a href="/switch-lang?lang=ar" class="lang-switch-btn"><i class="fa-solid fa-globe"></i> AR</a>
          <?php endif; ?>

          <div class="login_toggle profile-dropdown-container">
            <?php if (isset($_SESSION['user_id'])): ?>
              <a href="javascript:void(0);" class="login_link profile-trigger" id="profile-btn">
                <i class="fa-solid fa-circle-user" style="color: var(--main-color); font-size: 26px;"></i>
              </a>
              <div class="profile-menu" id="profile-menu" style="<?= $pageDir === 'rtl' ? 'right:-15px; left:auto;' : 'left:-15px; right:auto;' ?>">
                <div class="profile-header">
                  <?= lang('hello') ?>، <span><?php echo (isset($_SESSION['user_name']) && !empty(trim($_SESSION['user_name']))) ? htmlspecialchars(explode(' ', trim($_SESSION['user_name']))[0]) : lang('guest_user'); ?></span> 👋
                </div>
                <ul class="profile-links">
                  <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                    <li><a href="/admin"><i class="fa-solid fa-gauge"></i> <?= lang('dashboard') ?></a></li>
                  <?php else: ?>
                    <li><a href="/profile"><i class="fa-solid fa-user-gear"></i> <?= lang('profile') ?></a></li>
                    <li><a href="/my-orders"><i class="fa-solid fa-box-open"></i> <?= lang('my_orders') ?></a></li>
                    <li><a href="/my-messages"><i class="fa-solid fa-envelope"></i> <?= lang('my_messages') ?></a></li>
                  <?php endif; ?>
                  <li><a href="/logout" class="logout-link"><i class="fa-solid fa-arrow-right-from-bracket"></i> <?= lang('logout') ?></a></li>
                </ul>
              </div>
            <?php else: ?>
              <a href="/login" class="login_link"><i class="fa-regular fa-user" style="font-size: 22px; color: #000;"></i></a>
            <?php endif; ?>
          </div>

          <div class="nav_shop" id="cart-shop" style="position: relative; display: flex; cursor: pointer;">
            <img src="/images/icons/cart.png" alt="" style="width: 26px;">
            <span class="cart_count" style="position: absolute; top: -5px; <?= $pageDir === 'rtl' ? 'right' : 'left' ?>: -8px; background: #e35f26; color: white; width: 18px; height: 18px; border-radius: 50%; font-size: 11px; display: flex; justify-content: center; align-items: center; font-weight: bold;">0</span>
          </div>
        </div>

        <div class="nav_menu" id="nav-menu">
          <i class="fa-solid fa-xmark nav_menu_close" id="menu-close"></i>
          
          <div class="mobile-sidebar-content">
              <?php if ($currentLang === 'ar'): ?>
                <a href="/switch-lang?lang=en" class="mob-btn" style="background:#f1f5f9;"><i class="fa-solid fa-globe"></i> English</a>
              <?php else: ?>
                <a href="/switch-lang?lang=ar" class="mob-btn" style="background:#f1f5f9;"><i class="fa-solid fa-globe"></i> العربية</a>
              <?php endif; ?>

              <?php if (isset($_SESSION['user_id'])): ?>
                  <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                      <a href="/admin" class="mob-btn"><i class="fa-solid fa-gauge"></i> <?= lang('dashboard') ?></a>
                  <?php else: ?>
                      <a href="/profile" class="mob-btn"><i class="fa-solid fa-user-gear"></i> <?= lang('profile') ?></a>
                      <a href="/my-orders" class="mob-btn"><i class="fa-solid fa-box-open"></i> <?= lang('my_orders') ?></a>
                      <a href="/my-messages" class="mob-btn"><i class="fa-solid fa-envelope"></i> <?= lang('my_messages') ?></a>
                  <?php endif; ?>
                  <a href="/logout" class="mob-btn" style="color: #ef4444;"><i class="fa-solid fa-arrow-right-from-bracket" style="color: #ef4444;"></i> <?= lang('logout') ?></a>
              <?php else: ?>
                  <a href="/login" class="mob-btn"><i class="fa-solid fa-user-lock"></i> <?= lang('login') ?></a>
              <?php endif; ?>
              
              <div class="mob-btn" onclick="document.getElementById('cart-shop').click();" style="justify-content: space-between;">
                  <div style="display:flex; align-items:center; gap:12px;">
                      <i class="fa-solid fa-cart-shopping"></i> <span><?= lang('cart') ?></span>
                  </div>
                  <span class="cart_count" style="background:#ef4444; color:#fff; padding:2px 8px; border-radius:10px; font-size:13px;">0</span>
              </div>
          </div>

          <ul class="safe-links nav-list">
            <?php $currentUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); ?>
            <li class="nav-item"><a href="/store" class="nav_link <?= ($currentUri === '/store') ? 'active' : '' ?>"><?= lang('home') ?></a></li>
            <li class="nav-item"><a href="/products" class="nav_link <?= ($currentUri === '/products' || $currentUri === '/product') ? 'active' : '' ?>"><?= lang('products') ?></a></li>
            <li class="nav-item"><a href="/services" class="nav_link <?= ($currentUri === '/services') ? 'active' : '' ?>"><?= lang('services') ?></a></li>
            <li class="nav-item"><a href="/about" class="nav_link <?= ($currentUri === '/about') ? 'active' : '' ?>"><?= lang('about') ?></a></li>
            <li class="nav-item"><a href="/contact" class="nav_link <?= ($currentUri === '/contact') ? 'active' : '' ?>"><?= lang('contact') ?></a></li>
          </ul>
        </div>
      </div>

      <form action="/products" method="GET" class="safe-search">
          <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
          <input type="text" name="search" placeholder="<?= lang('search_placeholder') ?>" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
      </form>

      <a href="/" class="safe-logo">
        <img src="/images/logos/logo.png" alt="MY Store Logo" class="nav_logo" />
      </a>

    </nav>
  </header>
<script>
(function () {
  var h = document.getElementById('header');
  if (!h) return;
  function syncHeaderOffset() {
    var height = h.offsetHeight;
    if (height > 0) {
      document.documentElement.style.setProperty('--mobile-header-offset', height + 'px');
    }
  }
  syncHeaderOffset();
  if (typeof ResizeObserver !== 'undefined') {
    new ResizeObserver(syncHeaderOffset).observe(h);
  } else {
    window.addEventListener('resize', syncHeaderOffset);
  }
  window.addEventListener('load', syncHeaderOffset);
})();
</script>