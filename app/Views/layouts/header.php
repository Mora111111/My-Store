<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="<?= CSRF::generate() ?>">
  <link rel="stylesheet" href="/css/all.min.css" />
  <link rel="stylesheet" href="/style.css" />
  <link rel="icon" href="/images/icons/shopping-cart_head.png">
  <title>MY Store - متجر على الإنترنت</title>
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
            <?php if (isset($_SESSION['user_id'])): ?>
              <a href="javascript:void(0);" class="login_link profile-trigger" id="profile-btn">
                <i class="fa-solid fa-circle-user" style="color: var(--main-color); font-size: 26px;"></i>
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
                  <?php endif; ?>
                  <li>
                    <a href="/logout" class="logout-link"><i class="fa-solid fa-arrow-right-from-bracket"></i> تسجيل
                      خروج</a>
                  </li>
                </ul>
              </div>
            <?php else: ?>
              <a href="/login" class="login_link"><i class="fa-regular fa-user"></i></a>
            <?php endif; ?>
          </div>

          <div class="nav_shop" id="cart-shop">
            <img src="/images/icons/cart.png" alt="">
            <span class="cart_count">0</span>
          </div>
        </div>

        <div class="nav_menu" id="nav-menu">
          <i class="fa-solid fa-xmark nav_menu_close" id="menu-close"></i>
          <ul class="nav-list">
            <li class="nav-item"><a href="/" class="nav_link active">الرئيسية</a></li>
            <li class="nav-item"><a href="/products" class="nav_link">المنتجات</a></li>
            <li class="nav-item"><a href="/services" class="nav_link">الخدمات</a></li>
            <li class="nav-item"><a href="/about" class="nav_link">من نحن</a></li>
            <li class="nav-item"><a href="/contact" class="nav_link">اتصل بنا</a></li>
          </ul>
        </div>
      </div>
      <img src="/images/logos/logo.png" alt="MY Store Logo" class="nav_logo" />
    </nav>
  </header>