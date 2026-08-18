<?php 
session_start();
require_once 'db.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit();
}

$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en" dir="rtl">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link
      rel="stylesheet"
      href="css/all.min.css"
    />
    <link rel="stylesheet" href="style.css" />
    <link rel="icon" href="images/icons/shopping-cart_head.png">
    <title>MY Store - مفضلاتي</title>
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
                      <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                          <li><a href="dashboard.php"><i class="fa-solid fa-gauge"></i> لوحة الإدارة</a></li>
                      <?php else: ?>
                          <li><a href="my_orders.php"><i class="fa-solid fa-box-open"></i> طلباتي</a></li>
                      <?php endif; ?>
                      <li><a href="my_favorites.php"><i class="fa-solid fa-heart"></i> مفضلاتي</a></li>
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

    <main class="main" style="margin-top: 100px; min-height: 50vh;">
      <section class="container">
        <h2 class="main_title" style="margin-bottom: 30px;">منتجاتي المفضلة</h2>
        
        <div class="cards grid_content">
          <?php
          $fav_query = "
            SELECT products.* FROM products 
            INNER JOIN favorites ON products.id = favorites.product_id 
            WHERE favorites.user_id = $user_id 
            ORDER BY favorites.created_at DESC
          ";
          $fav_result = mysqli_query($conn, $fav_query);

          if (mysqli_num_rows($fav_result) > 0) {
              while ($row = mysqli_fetch_assoc($fav_result)) {
                  $rating = isset($row['rating']) ? (float)$row['rating'] : 5;
                  $stars_html = '';
                  for ($i = 1; $i <= 5; $i++) {
                      if ($rating >= $i) $stars_html .= '<i class="fa-solid fa-star"></i>';
                      elseif ($rating >= $i - 0.5) $stars_html .= '<i class="fa-regular fa-star-half-stroke fa-flip-horizontal"></i>';
                      else $stars_html .= '<i class="fa-regular fa-star"></i>';
                  }

                  $price_parts = explode('.', number_format($row['price'], 2, '.', ''));
                  
                  $heart_btn = '<button class="favorite-btn" data-product-id="'.$row['id'].'" style="background:none; border:none; cursor:pointer; font-size:22px; outline:none; padding:0; margin:0;"><i class="fa-solid fa-heart heart-icon" style="color: red;"></i></button>';

                  echo '
                  <div class="card" id="card-'.$row['id'].'">
                    <div class="box_img">
                      <img src="' . htmlspecialchars($row['image_url']) . '" alt="" class="card_image" />
                    </div>
                    <div class="card_details">
                      <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                        <a href="product-details.php?id=' . $row['id'] . '" class="card_title" style="flex:1; display:block; color:#555; font-weight:normal; font-size:14px; margin-bottom:10px;">' . htmlspecialchars($row['title']) . '</a>
                        ' . $heart_btn . '
                      </div>
                      <div class="rating">' . $stars_html . '</div>
                      <p class="card_price">' . $price_parts[0] . '.<small>' . $price_parts[1] . '</small> <span>جنيه</span></p>
                      <button class="card_btn" id="add_to_card">أضافة إلي العربة</button>
                    </div>
                  </div>';
              }
          } else {
              echo '<div style="grid-column: 1 / -1; text-align: center; padding: 50px;">
                      <i class="fa-regular fa-heart" style="font-size: 50px; color: #ccc; margin-bottom: 20px;"></i>
                      <h3>لا توجد منتجات في المفضلة حالياً</h3>
                      <p style="margin-top: 10px; color: #777;">قم باستكشاف المنتجات وأضف ما يعجبك هنا!</p>
                      <a href="products.php" class="btn" style="display: inline-block; margin-top: 20px;">الذهاب للمنتجات</a>
                    </div>';
          }
          ?>
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
              <a href="sginup.php" class="footer_link">إنشاء حساب</a>
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
    <script>
      document.querySelectorAll('.favorite-btn').forEach(btn => {
          btn.addEventListener('click', function(e) {
              if (this.querySelector('.heart-icon').style.color === 'gray') {
                  return; 
              }
              
              let cardId = 'card-' + this.getAttribute('data-product-id');
              let cardElement = document.getElementById(cardId);
              
              if (cardElement) {
                  cardElement.style.transition = 'opacity 0.3s ease';
                  cardElement.style.opacity = '0';
                  setTimeout(() => {
                      cardElement.remove();
                      let cardsContainer = document.querySelector('.cards');
                      if (cardsContainer && cardsContainer.children.length === 0) {
                           location.reload(); 
                      }
                  }, 300);
              }
          });
      });
    </script>
  </body>
</html>