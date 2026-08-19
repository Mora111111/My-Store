<?php 
session_start();
require_once 'db.php'; 
?>
<!DOCTYPE html>
<html lang="en" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="css/all.min.css" />
  <link rel="stylesheet" href="style.css" />
  <link rel="icon" href="images/icons/shopping-cart_head.png">
  <title>MY Store - المنتجات</title>
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
                      مرحباً، <span><?php echo htmlspecialchars(explode(' ', $_SESSION['user_name'])[0]); ?></span> 👋
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
          <div class="nav_shop" id="cart-shop">
            <img src="images/icons/cart.png" alt="">
            <span class="cart_count">0</span>
          </div>
        </div>
        <div class="nav_menu" id="nav-menu">
          <i class="fa-solid fa-xmark nav_menu_close" id="menu-close"></i>
          <ul class="nav-list">
            <li class="nav-item">
              <a href="index.php" class="nav_link">الرئيسية</a>
            </li>
            <li class="nav-item">
              <a href="products.php" class="nav_link active">المنتجات</a>
            </li>
            <li class="nav-item">
              <a href="services.php" class="nav_link">الخدمات</a>
            </li>
            <li class="nav-item">
              <a href="about_us.php" class="nav_link">من نحن</a>
            </li>
            <li class="nav-item">
              <a href="contact_us.php" class="nav_link">اتصل بنا</a>
            </li>
          </ul>
        </div>
      </div>
    
      <img src="images/logos/logo.png" alt="MY Store Logo" class="nav_logo" />
    </nav>
  </header>

  <div class="cart">
    <h2 class="cart_title">سلة التسوق</h2>
    <div class="cart_content"></div>
    <div class="total">
      <div class="total_title">الاجمالي</div>
      <div class="total_price">. جنية</div>
    </div>
    <a href="payment.php" class="btn_buy">شراء</a>
    <div class="cart_empty">
      <div><img src="images/Cart-img.png"></div>
      <p>عربة التسوق فارغة</p>
      <a href="products.php" class="btn_shopping">إستكشف المنتجات</a>
    </div>
    <i class="fa-solid fa-xmark" id="cart-close"></i>
  </div>

  <div class="all_products container">
    <div class="category_filter">
      <h2 class="all_products_title">منتجاتنا</h2>
      <div class="filter_btns">
        <button class="filter_btn active_btn" id="all">الكل</button>
        <?php
        $cat_query = mysqli_query($conn, "SELECT DISTINCT category_class FROM products WHERE category_class != ''");
        $cat_map = [];
        $cat_counter = 1;
        while ($c = mysqli_fetch_assoc($cat_query)) {
            $cat_name = $c['category_class'];
            $cat_id = 'cat_' . $cat_counter;
            $cat_map[$cat_name] = $cat_id;
            echo '<button class="filter_btn" id="' . $cat_id . '">' . htmlspecialchars($cat_name) . '</button>';
            $cat_counter++;
        }
        ?>
      </div>
    </div>
    <div class="cards grid_content">
      <?php
      $query = "SELECT * FROM products ORDER BY id DESC";
      $result = mysqli_query($conn, $query);

      if (mysqli_num_rows($result) > 0) {
          while ($row = mysqli_fetch_assoc($result)) {
              $category = $row['category_class'];
              $css_class = isset($cat_map[$category]) ? $cat_map[$category] : 'all';
              
              $rating = isset($row['rating']) ? (float)$row['rating'] : 5;
              $stars_html = '';
              for ($i = 1; $i <= 5; $i++) {
                  if ($rating >= $i) {
                      $stars_html .= '<i class="fa-solid fa-star"></i>';
                  } elseif ($rating >= $i - 0.5) {
                      $stars_html .= '<i class="fa-regular fa-star-half-stroke fa-flip-horizontal"></i>';
                  } else {
                      $stars_html .= '<i class="fa-regular fa-star"></i>';
                  }
              }

              $price_parts = explode('.', number_format($row['price'], 2, '.', ''));
              $main_price = $price_parts[0];
              $decimals = $price_parts[1];

              echo '
              <div class="card all ' . $css_class . '">
                <div class="box_img">
                  <img src="' . htmlspecialchars($row['image_url']) . '" alt="" class="card_image" />
                </div>
                <div class="card_details">
                  <a href="product-details.php?id=' . $row['id'] . '" class="card_title">' . htmlspecialchars($row['title']) . '</a>
                  <div class="rating">' . $stars_html . '</div>
                  <p class="card_price">' . $main_price . '.<small>' . $decimals . '</small> <span>جنيه</span></p>
                  <button class="card_btn" id="add_to_card">أضافة إلي العربة</button>
                </div>
              </div>
              ';
          }
      } else {
          echo "<h3 style='text-align:center; width:100%; color:#777; margin-top:50px;'>عذراً، جاري تحديث المخزن وإضافة منتجات جديدة قريباً..</h3>";
      }
      ?>
    </div>
  </div>
  <footer class="footer">
    <div class="footer_container container grid_content">
      <div class="footer_item">
        <h3 class="footer_title">معلومات عنا</h3>
        <p class="footer_p">نحن متجر على الإنترنت نقدم أفضل المنتجات الالكترونية ذات الجودة العالية والتسليم السريع</p>
        <img src="images/logos/logo-white.png" alt="" class="footer_img">
      </div>
      <div class="footer_item">
        <h3 class="footer_title">الحساب</h3>
        <ul class="footer_list">
          <li class="footer_li"><a href="signin.php" class="footer_link">تسجيل الدخول</a></li>
          <li class="footer_li"><a href="sginup.php" class="footer_link">إنشاء حساب</a></li>
          <li class="footer_li"><a href="#" class="footer_link"></a></li>
          <li class="footer_li"><a href="products.php" class="footer_link">المنتجات</a></li>
        </ul>
      </div>
      <div class="footer_item">
        <h3 class="footer_title">الروابط</h3>
        <ul class="footer_list">
          <li class="footer_li"><a href="services.php" class="footer_link">الخدمات</a></li>
          <li class="footer_li"><a href="about_us.php" class="footer_link">من نحن</a></li>
          <li class="footer_li"><a href="index.php#features" class="footer_link">المنتجات المميزة </a></li>
          <li class="footer_li"><a href="index.php#latest" class="footer_link">أحدث المنتجات</a></li>
          <li class="footer_li"><a href="contact_us.php" class="footer_link">اتصل بنا</a></li>
        </ul>
      </div>
      <div class="footer_item">
          <h3 class="footer_title">اتصل بنا</h3>
          <ul class="footer_list">
            <li class="footer_li"><i class="fa-solid fa-phone footer-icon"></i><span>+201017******</span></li>
            <li class="footer_li"><i class="fa-solid fa-phone footer-icon"></i><span>+201034******</span></li>
            <li class="footer_li"><i class="fa-solid fa-envelope footer-icon"></i><span>MY-Store@gmail.com</span></li>
            <li class="footer_li"><i class="fa-solid fa-location-dot footer-icon"></i><span>المحافظات - مصر</span></li>
        </ul>
      </div>
    </div>
    <p class="copyright container">جميع الحقوق محفوظة MY Store &copy; 2025 - 2026</p>
  </footer>
  <script src="Js/app.js"></script>
  <script src="Js/scroll.js"></script>
</body>
</html>