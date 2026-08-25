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
           Ø£Ø¯ÙˆØ§ØªÙƒ Ù„Ù„Ù†Ø¬Ø§Ø­ ØªØ¨Ø¯Ø£ Ø¨Ø¬Ù‡Ø§Ø² Ø°ÙƒÙŠ.. Ø§Ø®ØªØ± Ù…Ù†ØªØ¬Ø§ØªÙƒ    <br />
         Ø¨Ø§Ù„Ø³Ø¹Ø± Ø§Ù„Ù„ÙŠ ÙŠÙ†Ø§Ø³Ø¨Ùƒ
          </h1>
          <p class="home_description">
             Ù„Ø§ ØªØªØ±Ø¯Ø¯ ÙÙŠ Ø®ÙˆØ¶ Ø§Ù„ØªØ¬Ø±Ø¨Ø© ðŸ˜‰ ÙØ§Ù„ØªÙƒÙ†ÙˆÙ„ÙˆØ¬ÙŠØ§ ÙˆÙØ¬Ø¯Øª Ù„ØªÙƒØ³Ø± Ø­Ø¯ÙˆØ¯ Ø§Ù„Ù…Ø³ØªØ­ÙŠÙ„<br />
             ÙƒÙ† Ù…Ù† Ø§ÙˆØ§Ø¦Ù„ Ø§Ù„Ø°ÙŠÙ† ÙŠØ¬Ø±Ø¨ÙˆÙ† Ø§Ù„Ø§Ø¬Ù‡Ø²Ø© Ø§Ù„Ø­Ø¯ÙŠØ«Ø©  ðŸ¤©
          </p>
          <a href="/products" class="home_btn">â†’ ØªØ³ÙˆÙ‚ Ø§Ù„Ø§Ù†</a>
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
        <h2 class="main_title">Ø§Ù„Ù…Ù†ØªØ¬Ø§Øª Ø§Ù„Ù…Ù…ÙŠØ²Ø©</h2>
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
              <div class="card_title_wrapper" style="margin-bottom:10px;">
                <a href="/product?id=<?php echo $row['id']; ?>" class="card_title" style="display:block; color:#555; font-weight:normal; font-size:14px; margin:0; position:relative; z-index:2;"><?php echo htmlspecialchars($row['title']); ?></a>
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
              <button class="card_btn">Ø£Ø¶Ø§ÙØ© Ø¥Ù„ÙŠ Ø§Ù„Ø¹Ø±Ø¨Ø©</button>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </section>
      <section class="latest_products container" id="latest">
        <h2 class="main_title">Ø£Ø­Ø¯Ø« Ø§Ù„Ù…Ù†ØªØ¬Ø§Øª</h2>
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
              <div class="card_title_wrapper" style="margin-bottom:10px;">
                <a href="/product?id=<?php echo $row['id']; ?>" class="card_title" style="display:block; color:#555; font-weight:normal; font-size:14px; margin:0; position:relative; z-index:2;"><?php echo htmlspecialchars($row['title']); ?></a>
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
              <button class="card_btn">Ø£Ø¶Ø§ÙØ© Ø¥Ù„ÙŠ Ø§Ù„Ø¹Ø±Ø¨Ø©</button>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </section>
      <section class="offer">
        <div class="offer_items container">
          <div class="offer_item ">
            <span>Ù…ØªÙˆÙØ± Ø­ØµØ±ÙŠØ§Ù‹ Ø¹Ù„Ù‰ MY Store</span>
            <h2 class="offer_title">Smart Band 4</h2>
            <p class="offer_description">
              Ø³Ø§Ø¹Ø© Ø³Ø§Ù…Ø³ÙˆÙ†Ø¬ Ø¬Ø§Ù„ÙƒØ³ÙŠ Ø§Ù„ØªØ±Ø§  (47 Ù…Ù„Ù…ØŒ LTEØŒ Ø±Ù…Ø§Ø¯ÙŠ) Ù…Ø¹ Ø¨Ø·Ø§Ø±ÙŠØ© ØªØ¯ÙˆÙ… Ø­ØªÙ‰ 100 Ø³Ø§Ø¹Ø© | Ù…Ø¹Ø§Ù„Ø¬ 3 Ù†Ø§Ù†ÙˆÙ…ØªØ± | Ù†Ø¸Ø§Ù… ØªØ­Ø¯ÙŠØ¯ Ø§Ù„Ù…ÙˆØ§Ù‚Ø¹ Ø§Ù„Ø¹Ø§Ù„Ù…ÙŠ (GPS)
            </p>
            <a href="/products" class="btn">â†’  Ø§Ø´ØªØ±Ù‰ Ø§Ù„Ø§Ù‘Ù†</a>
          </div>
          <div class="offer_item offer_item_mobile">
            <img src="/images/products/product-13.png" class="offer-img">
          </div>
        </div>
      </section>
      <section class="services container" id="services">
        <h2 class="main_title">Ø§Ù„Ø®Ø¯Ù…Ø§Øª</h2>
        <div class="ser-container grid_content">
          <div class="service">
            <img src="/images/services/service-1.png" alt="" class="service_img">
            <h3 class="service_title">ØªÙˆØµÙŠÙ„ Ø³Ø±ÙŠØ¹ ÙˆÙ…Ø¬Ø§Ù†Ù‰</h3>
            <p class="service_p">Ø´Ø­Ù† Ù…Ø¬Ø§Ù†ÙŠ Ù„Ø¬Ù…ÙŠØ¹ Ø§Ù„Ø·Ù„Ø¨Ø§Øª Ø§Ù„ØªÙ‰ ØªØ²ÙŠØ¯ Ø¹Ù† 1000 Ø¬Ù†ÙŠØ©</p>
          </div>
          <div class="service">
            <img src="/images/services/service-2.png" alt="" class="service_img">
            <h3 class="service_title">Ø®Ø¯Ù…Ø© Ø§Ù„Ø¹Ù…Ù„Ø§Ø¡ Ø¹Ù„Ù‰ Ù…Ø¯Ø§Ø± 24 / 7</h3>
            <p class="service_p">Ø®Ø¯Ù…Ø© Ø§Ù„Ø¹Ù…Ù„Ø§Ø¡ ÙˆØ¯ÙŠØ© Ø¹Ù„Ù‰ Ù…Ø¯Ø§Ø± 24 Ø§Ù„Ø³Ø§Ø¹Ø© Ø·ÙˆØ§Ù„ Ø§ÙŠØ§Ù… Ø§Ù„Ø£Ø³Ø¨ÙˆØ¹</p>
          </div>
          <div class="service">
            <img src="/images/services/service-3.png" alt="" class="service_img">
            <h3 class="service_title">Ø¶Ù…Ø§Ù† Ø£Ø³ØªØ¹Ø§Ø¯Ø© Ø§Ù„Ø£Ù…ÙˆØ§Ù„</h3>
            <p class="service_p">Ù†Ù‚ÙˆÙ… Ø¨Ø¥Ø±Ø¬Ø§Ø¹ Ø§Ù„Ø£Ù…ÙˆØ§Ù„ Ø®Ù„Ø§Ù„ 15 ÙŠÙˆÙ… ÙƒØ­Ø¯ Ø£Ù‚ØµÙŠ</p>
          </div>
        </div>
      </section>
      <section class="testimonial container">
        <h2 class="main_title">ØªØ¹Ù„ÙŠÙ‚Ø§Øª Ø§Ù„Ø¹Ù…Ù„Ø§Ø¡</h2>
        <div class="testimonial_boxs grid_content">
          <div class="testimonial_box">
            <i class="fa-solid fa-quote-left quote_icon"></i>
            <p>Ø£ÙˆØµÙŠ Ø¨Ù‡Ø°Ø§ Ø§Ù„Ù…ØªØ¬Ø± Ù„ÙƒÙ… Ø¬Ù…ÙŠØ¹Ø§Ù‹ØŒ Ù„Ø¯ÙŠÙ‡Ù… Ù…Ù†ØªØ¬Ø§Øª Ø°Ø§Øª Ø¬ÙˆØ¯Ø© Ø¹Ø§Ù„ÙŠØ©.</p>
            <div class="rating">
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-regular fa-star"></i>
            </div>
            <img src="/images/users/user-1.png">
            <h3>Ø¹Ù…ÙŠÙ„</h3>
          </div>
          <div class="testimonial_box">
            <i class="fa-solid fa-quote-left quote_icon"></i>
              <p>
                Ù‡Ø°Ø§ Ø§Ù„Ù…ØªØ¬Ø± Ø±Ø§Ø¦Ø¹ ÙÙŠ Ø§Ù„Ø´Ø­Ù† . Ø¥Ù†Ù‡Ù… ÙŠØ´Ø­Ù†ÙˆÙ† Ø¨Ø³Ø±Ø¹Ø©. Ù„Ù‚Ø¯ Ø·Ù„Ø¨Øª Ù…Ù†Ù‡Ù… Ù…Ø±Ø© ÙˆØ§Ø­Ø¯Ø© ÙˆÙˆØµÙ„Øª Ø§Ù„Ø³Ù„Ø¹Ø© Ù„ÙŠ ÙÙŠ Ù†ÙØ³ Ø§Ù„Ø£Ø³Ø¨ÙˆØ¹!
              </p>
              <div class="rating">
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
              </div>
              <img src="/images/users/user-2.png">
              <h3>Ø¹Ù…ÙŠÙ„</h3>
          </div>
          <div class="testimonial_box">
            <i class="fa-solid fa-quote-left quote_icon"></i>
              <p>
                Ù…ØªØ¬Ø± MY Store Ù‡Ùˆ Ø£ÙØ¶Ù„ Ù…ÙƒØ§Ù† Ù„Ù„Ø´Ø±Ø§Ø¡ Ø¹Ø¨Ø± Ø§Ù„Ø£Ù†ØªØ±Ù†Øª. Ù„Ø¯ÙŠÙ‡Ù… Ù…Ù†ØªØ¬Ø§Øª Ø°Ø§Øª Ø¬ÙˆØ¯Ø© Ø³Ø­Ø±ÙŠØ©.
              </p>
              <div class="rating">
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-regular fa-star-half-stroke fa-flip-horizontal"></i>
              </div>
              <img src="/images/users/user-3.png">
              <h3>Ø¹Ù…ÙŠÙ„</h3>
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
