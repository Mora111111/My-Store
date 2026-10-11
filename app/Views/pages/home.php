<?php 
$favoriteIds = [];
if (Session::isLoggedIn()) {
    $favoriteModel = new Favorite();
    $favoriteIds = $favoriteModel->getUserFavoriteIds(Session::get('user_id'));
}
$globalCouponModel = new Coupon();
$activeCoupons =$globalCouponModel->getActiveStrikethroughCoupons();
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
          <?php $currentLang = $_SESSION['lang'] ?? 'ar'; ?>
          <style>
            .dynamic-hero-slider {
              position: relative;
              width: 100%;
              aspect-ratio: 4 / 3;
              overflow: hidden;
              overflow: clip;
              isolation: isolate;
              perspective: 4000px;
              perspective-origin: 50% 50%;
              transform-style: preserve-3d;
              border-radius: 22px;
              background: #f5f5f7;
              box-shadow:
                0 24px 60px rgba(0, 0, 0, 0.12),
                0 4px 14px rgba(0, 0, 0, 0.05);
              -webkit-font-smoothing: antialiased;
            }

            .dynamic-hero-slider .slider-item {
              position: absolute;
              inset: 0;
              width: 100%;
              height: 100%;
              display: block;
              transform: rotateY(var(--cube-angle, 0deg)) translateZ(var(--cube-depth, 0px));
              transform-origin: center center;
              transform-style: preserve-3d;
              backface-visibility: hidden;
              -webkit-backface-visibility: hidden;
              pointer-events: none;
              transition: transform 1.25s cubic-bezier(0.76, 0, 0.24, 1);
              will-change: transform;
              background: linear-gradient(145deg, #ffffff 0%, #f5f5f7 100%);
            }

            .dynamic-hero-slider .slider-item.active {
              pointer-events: auto;
            }

            .dynamic-hero-slider .slider-item img {
              display: block;
              width: 100%;
              height: 100%;
              object-fit: contain;
              object-position: center;
              background: linear-gradient(145deg, #ffffff 0%, #f5f5f7 100%);
              transform: scale(0.82);
              transition: transform 1.25s cubic-bezier(0.16, 1, 0.3, 1);
              backface-visibility: hidden;
              -webkit-backface-visibility: hidden;
            }

            .dynamic-hero-slider .slider-item.active img {
              transform: scale(0.86);
              transition-delay: 0.08s;
            }

            .dynamic-hero-slider .slider-caption {
              display: none;
              position: absolute;
              inset-inline-start: 16px;
              inset-inline-end: 16px;
              inset-block-end: 16px;
              z-index: 3;
              padding-block: 16px;
              padding-inline: 20px;
              border: 1px solid rgba(255, 255, 255, 0.68);
              border-radius: 18px;
              background: linear-gradient(
                135deg,
                rgba(255, 255, 255, 0.78),
                rgba(248, 250, 252, 0.62)
              );
              backdrop-filter: blur(28px) saturate(180%);
              -webkit-backdrop-filter: blur(28px) saturate(180%);
              box-shadow:
                0 12px 35px rgba(15, 23, 42, 0.10),
                inset 0 1px 0 rgba(255, 255, 255, 0.9);
              text-align: start;
              opacity: 0;
              visibility: hidden;
              transform: translateY(14px) scale(0.985);
              transform-origin: center;
              transition:
                opacity 0.65s cubic-bezier(0.16, 1, 0.3, 1),
                transform 0.75s cubic-bezier(0.16, 1, 0.3, 1),
                visibility 0s linear 0.65s;
            }

            .dynamic-hero-slider .slider-item.active .slider-caption {
              opacity: 1;
              visibility: visible;
              transform: translateY(0) scale(1);
              transition-delay: 0.3s, 0.3s, 0s;
            }

            .dynamic-hero-slider .slider-caption strong {
              display: block;
              margin-block-end: 6px;
              color: #1d1d1f;
              font-size: clamp(1rem, 1.6vw, 1.2rem);
              font-weight: 700;
              line-height: 1.45;
              letter-spacing: -0.025em;
              text-wrap: pretty;
            }

            .dynamic-hero-slider .slider-caption span {
              display: inline-block;
              color: var(--main-color);
              font-size: clamp(0.95rem, 1.4vw, 1.08rem);
              font-weight: 750;
              line-height: 1.5;
              letter-spacing: -0.015em;
            }

            @media (max-width: 600px) {
              .dynamic-hero-slider {
                border-radius: 16px;
                perspective: 4000px;
              }

              .dynamic-hero-slider .slider-caption {
                inset-inline-start: 10px;
                inset-inline-end: 10px;
                inset-block-end: 10px;
                padding-block: 11px;
                padding-inline: 14px;
                border-radius: 14px;
                backdrop-filter: blur(20px) saturate(170%);
                -webkit-backdrop-filter: blur(20px) saturate(170%);
              }

              .dynamic-hero-slider .slider-caption strong {
                font-size: 0.95rem;
              }

              .dynamic-hero-slider .slider-caption span {
                font-size: 0.9rem;
              }
            }

            @media (prefers-reduced-motion: reduce) {
              .dynamic-hero-slider .slider-item,
              .dynamic-hero-slider .slider-item img,
              .dynamic-hero-slider .slider-caption {
                transition-duration: 0.01ms;
                transition-delay: 0s;
              }
            }
          </style>
          <?php $sliderItems = $sliderProducts ?? $featuredProducts ?? []; ?>
          <?php if (!empty($sliderItems)): ?>
          <div class="dynamic-hero-slider">
            <?php foreach ($sliderItems as $i => $p): ?>
            <?php $slideTitle = ($currentLang === 'en' && !empty($p['title_en'])) ? $p['title_en'] : $p['title']; ?>
            <a href="/product?id=<?= $p['id'] ?>" class="slider-item<?= $i === 0 ? ' active' : '' ?>">
              <img src="<?= Product::getImageUrl($p['image_url']) ?>" alt="<?= htmlspecialchars($slideTitle) ?>" />
              <span class="slider-caption">
                <strong><?= htmlspecialchars($slideTitle) ?></strong>
                <span><?= $p['price'] ?> <?= lang('currency_egp') ?></span>
              </span>
            </a>
            <?php endforeach; ?>
          </div>
          <script>
            (function () {
              var sliders = document.querySelectorAll('.dynamic-hero-slider');

              sliders.forEach(function (slider) {
                var items = Array.from(slider.querySelectorAll('.slider-item'));

                if (!items.length) return;

                var current = items.findIndex(function (item) {
                  return item.classList.contains('active');
                });

                if (current < 0) {
                  current = 0;
                  items[current].classList.add('active');
                }

                if (items.length === 1) return;

                var timer = null;
                var delay = 5000;
                var duration = 1250;
                var direction = 1;

                function updateDirection() {
                  direction = window.getComputedStyle(slider).direction === 'rtl' ? -1 : 1;
                }

                function updateCube(animate) {
                  updateDirection();

                  var width = slider.clientWidth;

                  if (!width) return;

                  var count = items.length;
                  var step = 360 / count;
                  var depth = count === 2
                    ? width / 2
                    : width / (2 * Math.tan(Math.PI / count));

                  slider.style.setProperty('--cube-depth', depth + 'px');
                  slider.style.transitionDuration = animate ? duration + 'ms' : '0ms';

                  items.forEach(function (item, index) {
                    var angle = (index - current) * step * direction;

                    item.style.setProperty('--cube-angle', angle + 'deg');
                    item.style.transitionDuration = animate ? duration + 'ms' : '0ms';
                  });
                }

                function showNext() {
                  items[current].classList.remove('active');
                  current = (current + 1) % items.length;
                  items[current].classList.add('active');
                  updateCube(true);
                }

                function startSlider() {
                  if (timer !== null || document.hidden) return;

                  timer = window.setInterval(showNext, delay);
                }

                function stopSlider() {
                  if (timer !== null) {
                    window.clearInterval(timer);
                    timer = null;
                  }
                }

                updateCube(false);

                window.requestAnimationFrame(function () {
                  window.requestAnimationFrame(function () {
                    items.forEach(function (item) {
                      item.style.transitionDuration = '';
                    });

                    startSlider();
                  });
                });

                window.addEventListener('resize', function () {
                  updateCube(false);

                  window.requestAnimationFrame(function () {
                    items.forEach(function (item) {
                      item.style.transitionDuration = '';
                    });
                  });
                });

                document.addEventListener('visibilitychange', function () {
                  if (document.hidden) {
                    stopSlider();
                  } else {
                    startSlider();
                  }
                });
              });
            })();
          </script>
          <?php else: ?>
          <img src="/images/image1.png" alt="" class="home-img" />
          <?php endif; ?>
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
        <h2 class="main_title"><?= lang('featured_products') ?></h2>
        <div class="cards grid_content">
          <?php foreach ($featuredProducts as $row):
             // Dynamic Strikethrough Logic
             $final_price = $row['price'];$has_coupon_discount = false;
              $discount_pct_badge = 0;
              
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
               <img src="<?= Product::getImageUrl($row['image_url']) ?>" alt="" class="card_image" />
             </div>
             <div class="card_details">
               <div class="card_title_wrapper" style="margin-bottom:10px;">
                 <a href="/product?id=<?php echo $row['id']; ?>" class="card_title" style="display:block; color:#555; font-weight:normal; font-size:14px; margin:0; position:relative; z-index:2;"><?php echo htmlspecialchars(langField($row, 'title')); ?></a>
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
                   <button class="card_btn" data-id="<?php echo $row['id']; ?>" style="flex: 1; margin: 0; padding: 10px 15px;"><?= lang('add_to_cart') ?></button>
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
        <h2 class="main_title"><?= lang('latest_products') ?></h2>
        <div class="cards grid_content">
          <?php foreach ($latestProducts as $row):
             // Dynamic Strikethrough Logic
             $final_price = $row['price'];$has_coupon_discount = false;
              $discount_pct_badge = 0;
              
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
               <img src="<?= Product::getImageUrl($row['image_url']) ?>" alt="" class="card_image" />
             </div>
             <div class="card_details">
               <div class="card_title_wrapper" style="margin-bottom:10px;">
                 <a href="/product?id=<?php echo $row['id']; ?>" class="card_title" style="display:block; color:#555; font-weight:normal; font-size:14px; margin:0; position:relative; z-index:2;"><?php echo htmlspecialchars(langField($row, 'title')); ?></a>
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
                   <button class="card_btn" data-id="<?php echo $row['id']; ?>" style="flex: 1; margin: 0; padding: 10px 15px;"><?= lang('add_to_cart') ?></button>
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
            <span><?= lang('exclusive_on_store') ?? 'متوفر حصرياً على MY Store' ?></span>
            <h2 class="offer_title">Smart Band 4</h2>
            <p class="offer_description">
              ساعة سامسونج جالكسي الترا  (47 ملم، LTE، رمادي) مع بطارية تدوم حتى 100 ساعة | معالج 3 نانومتر | نظام تحديد المواقع العالمي (GPS)
            </p>
            <a href="/products" class="btn">→ <?= lang('buy_now') ?? 'اشترى الاّن' ?></a>
          </div>
          <div class="offer_item offer_item_mobile">
            <img src="/images/products/product-13.png" class="offer-img">
          </div>
        </div>
      </section>
      <section class="services container" id="services">
        <h2 class="main_title"><?= lang('services') ?></h2>
        <div class="ser-container grid_content">
          <div class="service">
            <img src="/images/services/service-1.png" alt="" class="service_img">
            <h3 class="service_title"><?= lang('delivery') ?></h3>
            <p class="service_p"><?= lang('free_shipping_over') ?? 'شحن مجاني لجميع الطلبات التى تزيد عن 1000 جنية' ?></p>
          </div>
          <div class="service">
            <img src="/images/services/service-2.png" alt="" class="service_img">
            <h3 class="service_title"><?= lang('customer_service') ?></h3>
            <p class="service_p"><?= lang('customer_service_24_7') ?? 'خدمة العملاء ودية على مدار 24 الساعة طوال ايام الأسبوع' ?></p>
          </div>
          <div class="service">
            <img src="/images/services/service-3.png" alt="" class="service_img">
            <h3 class="service_title"><?= lang('money_back_guarantee') ?></h3>
            <p class="service_p"><?= lang('money_back_15_days') ?? 'نقوم بإرجاع الأموال خلال 15 يوم كحد أقصي' ?></p>
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
