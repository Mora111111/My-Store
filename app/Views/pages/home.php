<?php 
$favoriteIds = [];
if (Session::isLoggedIn()) {
    $favoriteModel = new Favorite();
    $favoriteIds = $favoriteModel->getUserFavoriteIds(Session::get('user_id'));
}
?>
    <main class="main">
      <section class="home">
        <div class="home_box home_box_mobile">
          <h1 class="home_title">
           أدواتك للنجاح تبدأ بجهاز ذكي.. اختر منتجاتك    <br />
         بالسعر اللي يناسبك
          </h1>
          <p class="home_description">
             لا تتردد في خوض التجربة 😉 فالتكنولوجيا وُجدت لتكسر حدود المستحيل<br />
             كن من اوائل الذين يجربون الاجهزة الحديثة  🤩
          </p>
          <a href="/products" class="home_btn">→ تسوق الان</a>
        </div>
        <div class="home_box">
          <img src="/images/image1.png" alt="" class="home-img" />
        </div>
      </section>
      <section class="categories container">
        <div class="category_item">
          <img src="/images/category-1.jpg" alt="" class="category_img" />
        </div>
        <div class="category_item">
          <img src="/images/category-2.jpg" alt="" class="category_img" />
        </div>
        <div class="category_item">
          <img src="/images/category-3.jpg" alt="" class="category_img" />
        </div>
      </section>
      <section class="featured_products container" id="features">
        <h2 class="main_title">المنتجات المميزة</h2>
        <div class="cards grid_content">
          <?php foreach ($featuredProducts as $row):
             // Dynamic Strikethrough Logic
             $final_price = $row['price'];$has_coupon_discount = false;
             $discount_pct_badge = 0;
             $couponModel = new Coupon();
             $activeCoupons =$couponModel->getActiveStrikethroughCoupons();
             
             foreach($activeCoupons as $c) {
                 if($c['target_type'] === 'all' || ($c['target_type'] === 'specific_product' &&$c['target_product_id'] == $row['id'])) {$has_coupon_discount = true;
                     if($c['discount_type'] === 'percentage') {
                         $discount_amount = ($row['price'] * ($c['discount_value'] / 100));$final_price = $row['price'] -$discount_amount;
                         $discount_pct_badge = round($c['discount_value']);
                     } else {
                         $final_price =$row['price'] - $c['discount_value'];$discount_pct_badge = round(($c['discount_value'] /$row['price']) * 100);
                     }
                     $final_price = max(0,$final_price);
                     break; // Apply the highest value coupon available
                 }
             }
             
             // Fallback to manual old_price if no coupon is active
             if(!$has_coupon_discount && !empty($row['old_price']) &&$row['old_price'] > $row['price']) {$has_coupon_discount = true;
                 $final_price =$row['price'];
                 $row['price'] =$row['old_price']; // Swap for display
                 $discount_pct_badge = round((($row['price'] - $final_price) /$row['price']) * 100);
             }
              $rating = isset($row['rating']) ? (float)$row['rating'] : 5;
              $stars_html = '';
              for ($i = 1; $i <= 5; $i++) {
                  if ($rating >= $i) $stars_html .= '<i class="fa-solid fa-star"></i>';
                  elseif ($rating >= $i - 0.5) $stars_html .= '<i class="fa-regular fa-star-half-stroke fa-flip-horizontal"></i>';
                  else $stars_html .= '<i class="fa-regular fa-star"></i>';
              }
              $price_parts = explode('.', number_format($row['price'], 2, '.', ''));
          ?>
          <div class="card" style="position: relative;">
            <?php if($has_coupon_discount): ?>
            <div style="position: absolute; top: 15px; right: 15px; background: #ef4444; color: #fff; padding: 5px 10px; border-radius: 8px; font-weight: bold; font-size: 13px; z-index: 10; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                خصم <?php echo $discount_pct_badge; ?>%
            </div>
            <?php endif; ?>
            <div class="box_img">
              <img src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="" class="card_image" />
            </div>
            <div class="card_details">
              <div class="card_title_wrapper" style="margin-bottom:10px;">
                <a href="/product?id=<?php echo $row['id']; ?>" class="card_title" style="display:block; color:#555; font-weight:normal; font-size:14px; margin:0; position:relative; z-index:2;"><?php echo htmlspecialchars($row['title']); ?></a>
              </div>
              <div style="margin-bottom: 10px;">
                  <div class="rating"><?php echo $stars_html; ?></div>
              </div>
              <p class="card_price" style="display: inline-flex; align-items: center; gap: 8px;">
                  <span style="font-weight: 700; color: #0f172a;"><?php echo number_format($final_price, 2); ?> ج.م</span>
                  <?php if($has_coupon_discount): ?>
                      <del style="color: #ef4444; font-size: 0.85em; font-weight: normal;"><?php echo htmlspecialchars($row['price']); ?> ج.م</del>
                  <?php endif; ?>
              </p>
              <div style="display: flex; justify-content: center; align-items: center; gap: 15px; width: 100%; margin-top: 15px;">
                  <button class="card_btn" data-id="<?php echo $row['id']; ?>" style="flex: 1; margin: 0; padding: 10px 15px;">أضافة إلي العربة</button>
                  <?php 
                  $favoriteIds = [];
                  if (Session::isLoggedIn()) {
                      $favoriteModel = new Favorite();
                      $favoriteIds = $favoriteModel->getUserFavoriteIds(Session::get('user_id'));
                  }
                  $isFavorited = in_array($row['id'], $favoriteIds);
                  ?>
                  <button class="heart-action-btn" data-product-id="<?php echo $row['id']; ?>" style="background:transparent; border:1px solid #eee; border-radius:5px; cursor:pointer; font-size:18px; color:#ff4757; transition: transform 0.2s; padding: 8px 12px; display:flex; align-items:center; justify-content:center;">
                      <i class="<?php echo $isFavorited ? 'fa-solid' : 'fa-regular'; ?> fa-heart"></i>
                  </button>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </section>
      <section class="latest_products container" id="latest">
        <h2 class="main_title">أحدث المنتجات</h2>
        <div class="cards grid_content">
          <?php foreach ($latestProducts as $row):
             // Dynamic Strikethrough Logic
             $final_price = $row['price'];$has_coupon_discount = false;
             $discount_pct_badge = 0;
             $couponModel = new Coupon();
             $activeCoupons =$couponModel->getActiveStrikethroughCoupons();
             
             foreach($activeCoupons as $c) {
                 if($c['target_type'] === 'all' || ($c['target_type'] === 'specific_product' &&$c['target_product_id'] == $row['id'])) {$has_coupon_discount = true;
                     if($c['discount_type'] === 'percentage') {
                         $discount_amount = ($row['price'] * ($c['discount_value'] / 100));$final_price = $row['price'] -$discount_amount;
                         $discount_pct_badge = round($c['discount_value']);
                     } else {
                         $final_price =$row['price'] - $c['discount_value'];$discount_pct_badge = round(($c['discount_value'] /$row['price']) * 100);
                     }
                     $final_price = max(0,$final_price);
                     break; // Apply the highest value coupon available
                 }
             }
             
             // Fallback to manual old_price if no coupon is active
             if(!$has_coupon_discount && !empty($row['old_price']) &&$row['old_price'] > $row['price']) {$has_coupon_discount = true;
                 $final_price =$row['price'];
                 $row['price'] =$row['old_price']; // Swap for display
                 $discount_pct_badge = round((($row['price'] - $final_price) /$row['price']) * 100);
             }
              $rating = isset($row['rating']) ? (float)$row['rating'] : 5;
              $stars_html = '';
              for ($i = 1; $i <= 5; $i++) {
                  if ($rating >= $i) $stars_html .= '<i class="fa-solid fa-star"></i>';
                  elseif ($rating >= $i - 0.5) $stars_html .= '<i class="fa-regular fa-star-half-stroke fa-flip-horizontal"></i>';
                  else $stars_html .= '<i class="fa-regular fa-star"></i>';
              }
              $price_parts = explode('.', number_format($row['price'], 2, '.', ''));
          ?>
          <div class="card" style="position: relative;">
            <?php if($has_coupon_discount): ?>
            <div style="position: absolute; top: 15px; right: 15px; background: #ef4444; color: #fff; padding: 5px 10px; border-radius: 8px; font-weight: bold; font-size: 13px; z-index: 10; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                خصم <?php echo $discount_pct_badge; ?>%
            </div>
            <?php endif; ?>
            <div class="box_img">
              <img src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="" class="card_image" />
            </div>
            <div class="card_details">
              <div class="card_title_wrapper" style="margin-bottom:10px;">
                <a href="/product?id=<?php echo $row['id']; ?>" class="card_title" style="display:block; color:#555; font-weight:normal; font-size:14px; margin:0; position:relative; z-index:2;"><?php echo htmlspecialchars($row['title']); ?></a>
              </div>
              <div style="margin-bottom: 10px;">
                  <div class="rating"><?php echo $stars_html; ?></div>
              </div>
              <p class="card_price" style="display: inline-flex; align-items: center; gap: 8px;">
                  <span style="font-weight: 700; color: #0f172a;"><?php echo number_format($final_price, 2); ?> ج.م</span>
                  <?php if($has_coupon_discount): ?>
                      <del style="color: #ef4444; font-size: 0.85em; font-weight: normal;"><?php echo htmlspecialchars($row['price']); ?> ج.م</del>
                  <?php endif; ?>
              </p>
              <div style="display: flex; justify-content: center; align-items: center; gap: 15px; width: 100%; margin-top: 15px;">
                  <button class="card_btn" data-id="<?php echo $row['id']; ?>" style="flex: 1; margin: 0; padding: 10px 15px;">أضافة إلي العربة</button>
                  <?php 
                  $favoriteIds = [];
                  if (Session::isLoggedIn()) {
                      $favoriteModel = new Favorite();
                      $favoriteIds = $favoriteModel->getUserFavoriteIds(Session::get('user_id'));
                  }
                  $isFavorited = in_array($row['id'], $favoriteIds);
                  ?>
                  <button class="heart-action-btn" data-product-id="<?php echo $row['id']; ?>" style="background:transparent; border:1px solid #eee; border-radius:5px; cursor:pointer; font-size:18px; color:#ff4757; transition: transform 0.2s; padding: 8px 12px; display:flex; align-items:center; justify-content:center;">
                      <i class="<?php echo $isFavorited ? 'fa-solid' : 'fa-regular'; ?> fa-heart"></i>
                  </button>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </section>
      <section class="offer">
        <div class="offer_items container">
          <div class="offer_item ">
            <span>متوفر حصرياً على MY Store</span>
            <h2 class="offer_title">Smart Band 4</h2>
            <p class="offer_description">
              ساعة سامسونج جالكسي الترا  (47 ملم، LTE، رمادي) مع بطارية تدوم حتى 100 ساعة | معالج 3 نانومتر | نظام تحديد المواقع العالمي (GPS)
            </p>
            <a href="/products" class="btn">→  اشترى الاّن</a>
          </div>
          <div class="offer_item offer_item_mobile">
            <img src="/images/products/product-13.png" class="offer-img">
          </div>
        </div>
      </section>
      <section class="services container" id="services">
        <h2 class="main_title">الخدمات</h2>
        <div class="ser-container grid_content">
          <div class="service">
            <img src="/images/services/service-1.png" alt="" class="service_img">
            <h3 class="service_title">توصيل سريع ومجانى</h3>
            <p class="service_p">شحن مجاني لجميع الطلبات التى تزيد عن 1000 جنية</p>
          </div>
          <div class="service">
            <img src="/images/services/service-2.png" alt="" class="service_img">
            <h3 class="service_title">خدمة العملاء على مدار 24 / 7</h3>
            <p class="service_p">خدمة العملاء ودية على مدار 24 الساعة طوال ايام الأسبوع</p>
          </div>
          <div class="service">
            <img src="/images/services/service-3.png" alt="" class="service_img">
            <h3 class="service_title">ضمان أستعادة الأموال</h3>
            <p class="service_p">نقوم بإرجاع الأموال خلال 15 يوم كحد أقصي</p>
          </div>
        </div>
      </section>
      <section class="testimonial container">
        <h2 class="main_title">تعليقات العملاء</h2>
        <div class="testimonial_boxs grid_content">
          <div class="testimonial_box">
            <i class="fa-solid fa-quote-left quote_icon"></i>
            <p>أوصي بهذا المتجر لكم جميعاً، لديهم منتجات ذات جودة عالية.</p>
            <div class="rating">
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-regular fa-star"></i>
            </div>
            <img src="/images/users/user-1.png">
            <h3>عميل</h3>
          </div>
          <div class="testimonial_box">
            <i class="fa-solid fa-quote-left quote_icon"></i>
              <p>
                هذا المتجر رائع في الشحن . إنهم يشحنون بسرعة. لقد طلبت منهم مرة واحدة ووصلت السلعة لي في نفس الأسبوع!
              </p>
              <div class="rating">
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
              </div>
              <img src="/images/users/user-2.png">
              <h3>عميل</h3>
          </div>
          <div class="testimonial_box">
            <i class="fa-solid fa-quote-left quote_icon"></i>
              <p>
                متجر MY Store هو أفضل مكان للشراء عبر الأنترنت. لديهم منتجات ذات جودة سحرية.
              </p>
              <div class="rating">
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-regular fa-star-half-stroke fa-flip-horizontal"></i>
              </div>
              <img src="/images/users/user-3.png">
              <h3>عميل</h3>
          </div>
        </div>
      </section>
      <div class="brands container">
        <div class="brand">
          <img src="/images/brands/Adidas.png" alt="">
        </div>
        <div class="brand">
          <img src="/images/brands/dell.png" alt="">
        </div>
        <div class="brand">
          <img src="/images/brands/mi.png" alt="">
        </div>
        <div class="brand">
          <img src="/images/brands/huawei.png" alt="">
        </div>
      </div>
    </main>
