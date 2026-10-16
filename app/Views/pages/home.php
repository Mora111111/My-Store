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
              border-radius: 22px;
              background: linear-gradient(145deg, #ffffff 0%, #f4f5f7 100%);
              box-shadow: 0 24px 60px rgba(0, 0, 0, 0.12), 0 4px 14px rgba(0, 0, 0, 0.05);
              -webkit-font-smoothing: antialiased;
            }

            .dynamic-hero-slider .slider-stage {
              position: absolute;
              inset: 0;
              display: block;
              width: 100%;
              height: 100%;
              padding: 34px;
              box-sizing: border-box;
            }

            .dynamic-hero-slider .slider-item {
              position: absolute;
              inset: 0;
              display: block;
              opacity: 0;
              visibility: hidden;
              transform: scale(0.96) translateY(14px);
              pointer-events: none;
              transition: opacity 0.75s cubic-bezier(0.16, 1, 0.3, 1), transform 0.85s cubic-bezier(0.16, 1, 0.3, 1), visibility 0s linear 0.85s;
              will-change: opacity, transform;
            }

            .dynamic-hero-slider .slider-item.active {
              opacity: 1;
              visibility: visible;
              transform: scale(1) translateY(0);
              pointer-events: auto;
              transition-delay: 0s;
              z-index: 2;
            }

            .dynamic-hero-slider .slider-item.prev {
              transform: scale(0.96) translateY(-14px);
            }

            .dynamic-hero-slider .slider-item img {
              display: block;
              width: 100%;
              height: 100%;
              object-fit: contain;
              object-position: center;
              filter: drop-shadow(0 22px 32px rgba(15, 23, 42, 0.16));
            }

            .dynamic-hero-slider .slider-badge {
              position: absolute;
              inset-block-start: 16px;
              inset-inline-start: 16px;
              z-index: 6;
              display: inline-flex;
              align-items: center;
              gap: 6px;
              padding: 7px 14px;
              border-radius: 40px;
              background: linear-gradient(135deg, #ef4444, #f97316);
              color: #fff;
              font-size: 0.82rem;
              font-weight: 800;
              box-shadow: 0 8px 20px rgba(239, 68, 68, 0.35);
            }

            .dynamic-hero-slider .slider-counter {
              position: absolute;
              inset-block-start: 16px;
              inset-inline-end: 16px;
              z-index: 6;
              padding: 6px 12px;
              border-radius: 40px;
              border: 1px solid rgba(255, 255, 255, 0.7);
              background: rgba(255, 255, 255, 0.66);
              backdrop-filter: blur(14px);
              -webkit-backdrop-filter: blur(14px);
              color: #1d1d1f;
              font-size: 0.8rem;
              font-weight: 700;
              font-variant-numeric: tabular-nums;
            }

            .dynamic-hero-slider .slider-caption {
              position: absolute;
              inset-inline: 16px;
              inset-block-end: 16px;
              z-index: 5;
              display: flex;
              align-items: center;
              justify-content: space-between;
              gap: 14px;
              padding-block: 14px;
              padding-inline: 18px;
              border: 1px solid rgba(255, 255, 255, 0.68);
              border-radius: 18px;
              background: linear-gradient(135deg, rgba(255, 255, 255, 0.8), rgba(248, 250, 252, 0.64));
              backdrop-filter: blur(26px) saturate(180%);
              -webkit-backdrop-filter: blur(26px) saturate(180%);
              box-shadow: 0 12px 35px rgba(15, 23, 42, 0.1), inset 0 1px 0 rgba(255, 255, 255, 0.9);
              text-align: start;
              opacity: 0;
              visibility: hidden;
              transform: translateY(16px);
              transition: opacity 0.6s cubic-bezier(0.16, 1, 0.3, 1), transform 0.7s cubic-bezier(0.16, 1, 0.3, 1), visibility 0s linear 0.7s;
            }

            .dynamic-hero-slider .slider-item.active .slider-caption {
              opacity: 1;
              visibility: visible;
              transform: translateY(0);
              transition-delay: 0.32s, 0.32s, 0s;
            }

            .dynamic-hero-slider .slider-caption .slider-meta {
              min-width: 0;
            }

            .dynamic-hero-slider .slider-caption strong {
              display: block;
              margin-block-end: 4px;
              color: #1d1d1f;
              font-size: clamp(0.95rem, 1.5vw, 1.12rem);
              font-weight: 700;
              line-height: 1.4;
              letter-spacing: -0.02em;
              white-space: nowrap;
              overflow: hidden;
              text-overflow: ellipsis;
            }

            .dynamic-hero-slider .slider-caption .slider-price {
              display: inline-flex;
              align-items: baseline;
              gap: 8px;
              color: var(--main-color);
              font-size: clamp(0.92rem, 1.35vw, 1.05rem);
              font-weight: 800;
              line-height: 1.4;
            }

            .dynamic-hero-slider .slider-caption del {
              color: #94a3b8;
              font-size: 0.82em;
              font-weight: 600;
            }

            .dynamic-hero-slider .slider-arrow {
              flex-shrink: 0;
              display: inline-flex;
              align-items: center;
              justify-content: center;
              inline-size: 44px;
              block-size: 44px;
              border-radius: 50%;
              background: linear-gradient(135deg, var(--main-color), #38bdf8);
              color: #fff;
              font-size: 1rem;
              text-decoration: none;
              box-shadow: 0 8px 20px rgba(56, 189, 248, 0.4);
              transition: transform 0.25s ease;
            }

            .dynamic-hero-slider .slider-arrow:hover {
              transform: translateY(-2px) scale(1.06);
            }

            .dynamic-hero-slider .slider-nav-btn {
              position: absolute;
              inset-block-start: 50%;
              z-index: 7;
              display: inline-flex;
              align-items: center;
              justify-content: center;
              inline-size: 42px;
              block-size: 42px;
              margin-block-start: -21px;
              border: 1px solid rgba(255, 255, 255, 0.7);
              border-radius: 50%;
              background: rgba(255, 255, 255, 0.7);
              backdrop-filter: blur(14px);
              -webkit-backdrop-filter: blur(14px);
              color: #1d1d1f;
              font-size: 0.9rem;
              cursor: pointer;
              opacity: 0;
              transition: opacity 0.35s ease, transform 0.25s ease;
            }

            .dynamic-hero-slider:hover .slider-nav-btn,
            .dynamic-hero-slider .slider-nav-btn:focus-visible {
              opacity: 1;
            }

            .dynamic-hero-slider .slider-nav-btn:hover {
              transform: scale(1.08);
            }

            .dynamic-hero-slider .slider-nav-btn.prev {
              inset-inline-start: 14px;
            }

            .dynamic-hero-slider .slider-nav-btn.next {
              inset-inline-end: 14px;
            }

            .dynamic-hero-slider .slider-progress {
              position: absolute;
              inset-block-end: 0;
              inset-inline-start: 0;
              z-index: 8;
              block-size: 4px;
              inline-size: 0;
              background: linear-gradient(90deg, var(--main-color), #38bdf8);
              border-start-start-radius: 4px;
              border-end-start-radius: 4px;
            }

            @media (max-width: 800px) {
              .dynamic-hero-slider {
                display: block;
                width: 100%;
                max-width: 480px;
                min-height: 240px;
                aspect-ratio: 4 / 3;
                margin: 0 auto;
                border-radius: 16px;
              }

              .dynamic-hero-slider .slider-stage {
                padding: 20px;
              }

              .dynamic-hero-slider .slider-caption {
                inset-inline: 10px;
                inset-block-end: 10px;
                padding-block: 10px;
                padding-inline: 12px;
                border-radius: 14px;
              }

              .dynamic-hero-slider .slider-nav-btn {
                inline-size: 34px;
                block-size: 34px;
                margin-block-start: -17px;
                opacity: 1;
              }

              .dynamic-hero-slider .slider-arrow {
                inline-size: 36px;
                block-size: 36px;
              }
            }

            @media (prefers-reduced-motion: reduce) {
              .dynamic-hero-slider .slider-item,
              .dynamic-hero-slider .slider-caption {
                transition-duration: 0.01ms;
                transition-delay: 0s;
              }

              .dynamic-hero-slider .slider-progress {
                display: none;
              }
            }
          </style>
          <?php $sliderItems = $sliderProducts ?? $featuredProducts ?? []; ?>
          <?php if (!empty($sliderItems)): ?>
          <div class="dynamic-hero-slider" data-autoplay="5000">
            <div class="slider-stage">
              <?php foreach (array_values($sliderItems) as $i => $p): ?>
              <?php
                $slideDiscount = false;
                $slidePct = 0;
                $slideFinal = floatval($p['price']);
                $slideOld = 0;
                foreach ($activeCoupons as $c) {
                    if (($c['target_type'] ?? '') === 'all' || (($c['target_type'] ?? '') === 'specific_product' && (int)($c['target_product_id'] ?? 0) === (int)$p['id'])) {
                        $slideDiscount = true;
                        if (($c['discount_type'] ?? '') === 'percentage') {
                            $slideFinal = $slideFinal - ($slideFinal * ($c['discount_value'] / 100));
                            $slidePct = round($c['discount_value']);
                        } else {
                            $slideFinal = $slideFinal - $c['discount_value'];
                            $slidePct = ($slideFinal > 0) ? round((($c['discount_value'] / ($slideFinal + $c['discount_value'])) * 100)) : 0;
                        }
                        $slideFinal = max(0, $slideFinal);
                        break;
                    }
                }
                if (!$slideDiscount && !empty($p['old_price']) && (float)$p['old_price'] > (float)$p['price']) {
                    $slideDiscount = true;
                    $slideOld = floatval($p['old_price']);
                    $slideFinal = floatval($p['price']);
                    $slidePct = ($slideOld > 0) ? round(((($slideOld - $slideFinal) / $slideOld) * 100)) : 0;
                }
              ?>
              <a href="/product?id=<?= $p['id'] ?>" class="slider-item<?= $i === 0 ? ' active' : '' ?>">
                <img src="<?= Product::getImageUrl($p['image_url']) ?>" alt="<?= htmlspecialchars(langField($p, 'title')) ?>" loading="<?= $i === 0 ? 'eager' : 'lazy' ?>" decoding="async" />
                <?php if ($slideDiscount && $slidePct > 0): ?>
                <span class="slider-badge"><i class="fa-solid fa-tag"></i> <?= lang('discount_text') ?> <?= $slidePct ?>%</span>
                <?php endif; ?>
                <span class="slider-caption">
                  <span class="slider-meta">
                    <strong><?= htmlspecialchars(langField($p, 'title')) ?></strong>
                    <span class="slider-price">
                      <?= number_format($slideFinal, 2) ?> <?= lang('currency_egp') ?>
                      <?php if ($slideDiscount && $slideOld > 0): ?>
                      <del><?= number_format($slideOld, 2) ?> <?= lang('currency_egp') ?></del>
                      <?php endif; ?>
                    </span>
                  </span>
                  <span class="slider-arrow"><i class="fa-solid fa-arrow-up-left-from-square"></i></span>
                </span>
              </a>
              <?php endforeach; ?>
            </div>
            <span class="slider-counter">1 / <?= count($sliderItems) ?></span>
            <button type="button" class="slider-nav-btn prev" aria-label="previous"><i class="fa-solid fa-chevron-right"></i></button>
            <button type="button" class="slider-nav-btn next" aria-label="next"><i class="fa-solid fa-chevron-left"></i></button>
            <span class="slider-progress"></span>
          </div>
          <script>
            (function () {
              var sliders = document.querySelectorAll('.dynamic-hero-slider');

              sliders.forEach(function (slider) {
                var items = Array.prototype.slice.call(slider.querySelectorAll('.slider-item'));
                if (!items.length) return;

                var counter = slider.querySelector('.slider-counter');
                var progress = slider.querySelector('.slider-progress');
                var prevBtn = slider.querySelector('.slider-nav-btn.prev');
                var nextBtn = slider.querySelector('.slider-nav-btn.next');
                var delay = parseInt(slider.getAttribute('data-autoplay'), 10) || 5000;
                var current = items.findIndex(function (item) {
                  return item.classList.contains('active');
                });
                if (current < 0) current = 0;

                var timer = null;
                var progressTimer = null;
                var single = items.length === 1;

                function isRtl() {
                  return window.getComputedStyle(slider).direction === 'rtl';
                }

                function paint() {
                  items.forEach(function (item, index) {
                    item.classList.toggle('active', index === current);
                    item.classList.toggle('prev', index !== current);
                  });
                  if (counter) {
                    counter.textContent = (current + 1) + ' / ' + items.length;
                  }
                }

                function go(step) {
                  current = (current + step + items.length) % items.length;
                  paint();
                  restart();
                }

                function startProgress() {
                  if (!progress || single) return;
                  progress.style.transition = 'none';
                  progress.style.width = '0%';
                  window.requestAnimationFrame(function () {
                    progress.style.transition = 'width ' + delay + 'ms linear';
                    progress.style.width = '100%';
                  });
                }

                function stopProgress() {
                  if (!progress) return;
                  progress.style.transition = 'none';
                  progress.style.width = '0%';
                }

                function start() {
                  if (single || timer !== null || document.hidden) return;
                  timer = window.setInterval(function () {
                    go(isRtl() ? -1 : 1);
                  }, delay);
                  startProgress();
                }

                function stop() {
                  if (timer !== null) {
                    window.clearInterval(timer);
                    timer = null;
                  }
                  if (progressTimer !== null) {
                    window.clearTimeout(progressTimer);
                    progressTimer = null;
                  }
                  stopProgress();
                }

                function restart() {
                  stop();
                  start();
                }

                if (prevBtn) {
                  prevBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    go(isRtl() ? 1 : -1);
                  });
                }

                if (nextBtn) {
                  nextBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    go(isRtl() ? -1 : 1);
                  });
                }

                slider.addEventListener('mouseenter', function () {
                  if (timer !== null) {
                    window.clearInterval(timer);
                    timer = null;
                    stopProgress();
                  }
                });

                slider.addEventListener('mouseleave', function () {
                  start();
                });

                var touchStartX = 0;
                var touchDeltaX = 0;

                slider.addEventListener('touchstart', function (e) {
                  touchStartX = e.touches[0].clientX;
                  touchDeltaX = 0;
                  stop();
                }, { passive: true });

                slider.addEventListener('touchmove', function (e) {
                  touchDeltaX = e.touches[0].clientX - touchStartX;
                }, { passive: true });

                slider.addEventListener('touchend', function () {
                  var threshold = 45;
                  if (Math.abs(touchDeltaX) > threshold) {
                    go(touchDeltaX < 0 ? 1 : -1);
                  } else {
                    start();
                  }
                  touchDeltaX = 0;
                });

                document.addEventListener('visibilitychange', function () {
                  if (document.hidden) {
                    stop();
                  } else {
                    start();
                  }
                });

                paint();
                start();
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
