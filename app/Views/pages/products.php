<?php 
$favoriteIds = [];
if (Session::isLoggedIn()) {
    $favoriteModel = new Favorite();
    $favoriteIds = $favoriteModel->getUserFavoriteIds(Session::get('user_id'));
}
?>
  <div class="all_products container">
    <div class="category_filter">
      <h2 class="all_products_title">Ù…Ù†ØªØ¬Ø§ØªÙ†Ø§</h2>
      <div class="filter_btns">
        <button class="filter_btn active_btn" id="all">Ø§Ù„ÙƒÙ„</button>
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
                <button class="heart-action-btn" data-product-id="<?php echo $row['id']; ?>" style="background:transparent; border:none; cursor:pointer; font-size:18px; color:#ff4757; transition: transform 0.2s;">
                    <i class="<?php echo $isFavorited ? 'fa-solid' : 'fa-regular'; ?> fa-heart"></i>
                </button>
            </div>
            <p class="card_price"><?php echo $price_parts[0]; ?>.<small><?php echo $price_parts[1]; ?></small> <span>Ø¬Ù†ÙŠÙ‡</span></p>
            <button class="card_btn" id="add_to_card">Ø£Ø¶Ø§ÙØ© Ø¥Ù„ÙŠ Ø§Ù„Ø¹Ø±Ø¨Ø©</button>
          </div>
        </div>
      <?php
          endforeach;
      else:
      ?>
        <h3 style='text-align:center; width:100%; color:#777; margin-top:50px;'>Ø¹Ø°Ø±Ø§Ù‹ØŒ Ø¬Ø§Ø±ÙŠ ØªØ­Ø¯ÙŠØ« Ø§Ù„Ù…Ø®Ø²Ù† ÙˆØ¥Ø¶Ø§ÙØ© Ù…Ù†ØªØ¬Ø§Øª Ø¬Ø¯ÙŠØ¯Ø© Ù‚Ø±ÙŠØ¨Ø§Ù‹..</h3>
      <?php endif; ?>
    </div>
  </div>
