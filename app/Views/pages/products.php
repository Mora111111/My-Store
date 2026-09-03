<?php 
$favoriteIds = [];
if (Session::isLoggedIn()) {
    $favoriteModel = new Favorite();
    $favoriteIds = $favoriteModel->getUserFavoriteIds(Session::get('user_id'));
}
?>
  <div class="all_products container">
    <div class="category_filter">
      <h2 class="all_products_title">منتجاتنا</h2>
      <div class="filter_btns">
        <button class="filter_btn active_btn" id="all">الكل</button>
        <?php foreach ($categories as $catName):
            $catId = $catMap[$catName] ?? '';
        ?>
        <button class="filter_btn" id="<?php echo $catId; ?>"><?php echo htmlspecialchars($catName); ?></button>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="cards grid_content">
      <?php if (count($allProducts) > 0):
          foreach ($allProducts as $row):
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
              $category = $row['category_class'];
              $css_class = isset($catMap[$category]) ? $catMap[$category] : 'all';
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
      ?>
        <div class="card all <?php echo $css_class; ?>" style="position: relative;">
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
              <a href="/product?id=<?php echo $row['id']; ?>" class="card_title" style="margin:0; position:relative; z-index:2;"><?php echo htmlspecialchars($row['title']); ?></a>
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
                <button class="card_btn" id="add_to_card" data-id="<?php echo $row['id']; ?>" style="flex: 1; margin: 0; padding: 10px 15px;">أضافة إلي العربة</button>
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
      <?php
          endforeach;
      else:
      ?>
        <h3 style='text-align:center; width:100%; color:#777; margin-top:50px;'>عذراً، جاري تحديث المخزن وإضافة منتجات جديدة قريباً..</h3>
      <?php endif; ?>
    </div>
  </div>
