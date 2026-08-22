<?php 
$favoriteIds = [];
if (Session::isLoggedIn()) {
    $favoriteModel = new Favorite();
    $favoriteIds = $favoriteModel->getUserFavoriteIds(Session::get('user_id'));
}
?>
  <div class="cart">
    <h2 class="cart_title">سلة التسوق</h2>
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
          <?php $isFavorited = in_array($row['id'], $favoriteIds); ?>
          <button class="favorite-btn" data-product-id="<?php echo $row['id']; ?>" style="position:absolute; top:10px; right:10px; background:transparent; border:none; cursor:pointer; font-size:1.5rem; color:#ff4757; z-index:10;">
              <i class="<?php echo $isFavorited ? 'fa-solid' : 'fa-regular'; ?> fa-heart"></i>
          </button>
          <div class="box_img">
            <img src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="" class="card_image" />
          </div>
          <div class="card_details">
            <a href="/product?id=<?php echo $row['id']; ?>" class="card_title"><?php echo htmlspecialchars($row['title']); ?></a>
            <div class="rating"><?php echo $stars_html; ?></div>
            <p class="card_price"><?php echo $price_parts[0]; ?>.<small><?php echo $price_parts[1]; ?></small> <span>جنيه</span></p>
            <button class="card_btn" id="add_to_card">أضافة إلي العربة</button>
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