<?php 
$favoriteIds = [];
if (Session::isLoggedIn()) {
    $favoriteModel = new Favorite();
    $favoriteIds = $favoriteModel->getUserFavoriteIds(Session::get('user_id'));
}
?>
    <div class="cart">
      <h2 class="cart_title">عربة التسوق</h2>
      <div class="cart_content"></div>
      <div class="total">
        <div class="total_title">الاجمالي</div>
        <div class="total_price">. جنية</div>
      </div>
      <a href="/checkout" class="btn_buy">شراء</a>
      <div class="cart_empty">
        <div><img src="/images/Cart-img.png"></div>
        <p>عربة التسوق فارغة</p>
        <a href="/products" class="btn_shopping">إستكشف المنتجات</a>
      </div>
      <i class="fa-solid fa-xmark" id="cart-close"></i>
    </div>
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
              $rating = isset($row['rating']) ? (float)$row['rating'] : 5;
              $stars_html = '';
              for ($i = 1; $i <= 5; $i++) {
                  if ($rating >= $i) $stars_html .= '<i class="fa-solid fa-star"></i>';
                  elseif ($rating >= $i - 0.5) $stars_html .= '<i class="fa-regular fa-star-half-stroke fa-flip-horizontal"></i>';
                  else $stars_html .= '<i class="fa-regular fa-star"></i>';
              }
              $price_parts = explode('.', number_format($row['price'], 2, '.', ''));
          ?>
          <div class="card">
            <div class="box_img">
              <img src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="" class="card_image" />
            </div>
            <div class="card_details">
              <a href="/product?id=<?php echo $row['id']; ?>" class="card_title" style="display:block; color:#555; font-weight:normal; font-size:14px; margin-bottom:10px;"><?php echo htmlspecialchars($row['title']); ?></a>
              <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                  <div class="rating"><?php echo $stars_html; ?></div>
                  <?php 
                  $favoriteIds = [];
                  if (Session::isLoggedIn()) {
                      $favoriteModel = new Favorite();
                      $favoriteIds = $favoriteModel->getUserFavoriteIds(Session::get('user_id'));
                  }
                  $isFavorited = in_array($row['id'], $favoriteIds);
                  ?>
                  <button class="favorite-btn" data-product-id="<?php echo $row['id']; ?>" style="background:transparent; border:none; cursor:pointer; font-size:18px; color:#ff4757; transition: transform 0.2s;">
                      <i class="<?php echo $isFavorited ? 'fa-solid' : 'fa-regular'; ?> fa-heart"></i>
                  </button>
              </div>
              <p class="card_price"><?php echo $price_parts[0]; ?>.<small><?php echo $price_parts[1]; ?></small> <span>جنيه</span></p>
              <button class="card_btn">أضافة إلي العربة</button>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </section>
      <section class="latest_products container" id="latest">
        <h2 class="main_title">أحدث المنتجات</h2>
        <div class="cards grid_content">
          <?php foreach ($latestProducts as $row):
              $rating = isset($row['rating']) ? (float)$row['rating'] : 5;
              $stars_html = '';
              for ($i = 1; $i <= 5; $i++) {
                  if ($rating >= $i) $stars_html .= '<i class="fa-solid fa-star"></i>';
                  elseif ($rating >= $i - 0.5) $stars_html .= '<i class="fa-regular fa-star-half-stroke fa-flip-horizontal"></i>';
                  else $stars_html .= '<i class="fa-regular fa-star"></i>';
              }
              $price_parts = explode('.', number_format($row['price'], 2, '.', ''));
          ?>
          <div class="card">
            <div class="box_img">
              <img src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="" class="card_image" />
            </div>
            <div class="card_details">
              <a href="/product?id=<?php echo $row['id']; ?>" class="card_title" style="display:block; color:#555; font-weight:normal; font-size:14px; margin-bottom:10px;"><?php echo htmlspecialchars($row['title']); ?></a>
              <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                  <div class="rating"><?php echo $stars_html; ?></div>
                  <?php 
                  $favoriteIds = [];
                  if (Session::isLoggedIn()) {
                      $favoriteModel = new Favorite();
                      $favoriteIds = $favoriteModel->getUserFavoriteIds(Session::get('user_id'));
                  }
                  $isFavorited = in_array($row['id'], $favoriteIds);
                  ?>
                  <button class="favorite-btn" data-product-id="<?php echo $row['id']; ?>" style="background:transparent; border:none; cursor:pointer; font-size:18px; color:#ff4757; transition: transform 0.2s;">
                      <i class="<?php echo $isFavorited ? 'fa-solid' : 'fa-regular'; ?> fa-heart"></i>
                  </button>
              </div>
              <p class="card_price"><?php echo $price_parts[0]; ?>.<small><?php echo $price_parts[1]; ?></small> <span>جنيه</span></p>
              <button class="card_btn">أضافة إلي العربة</button>
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