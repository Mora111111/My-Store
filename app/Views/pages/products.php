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
        <div class="card all <?php echo $css_class; ?>">
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
            <p class="card_price"><?php echo $price_parts[0]; ?>.<small><?php echo $price_parts[1]; ?></small> <span>جنيه</span></p>
            <div style="display: flex; justify-content: center; align-items: center; gap: 15px; width: 100%; margin-top: 15px;">
                <button class="card_btn" id="add_to_card" style="flex: 1; margin: 0; padding: 10px 15px;">أضافة إلي العربة</button>
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
