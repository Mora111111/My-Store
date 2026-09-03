  <div class="product_details_section container">
    <div class="card" style="margin-top: 150px; margin-bottom: 50px; max-width: 600px; margin-left: auto; margin-right: auto; padding: 20px;">
     <div class="product-gallery" style="display: flex; flex-direction: column; gap: 15px;">
       <div class="box_img main-image-container" style="height: 400px; background: #fff; padding: 10px; border-radius: 12px; border: 1px solid #eee; display: flex; align-items: center; justify-content: center; position: relative;">
           <?php if(!empty($product['old_price']) && $product['old_price'] >$product['price']): 
               $pct = round((($product['old_price'] - $product['price']) /$product['old_price']) * 100);
           ?>
           <div style="position: absolute; top: 20px; right: 20px; background: #ef4444; color: #fff; padding: 8px 15px; border-radius: 8px; font-weight: bold; font-size: 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">خصم <?php echo $pct; ?>%</div>
           <?php endif; ?>
           <img id="mainProductImage" src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['title']); ?>" class="card_image" style="max-height: 100%; max-width: 100%; object-fit: contain; transition: 0.3s;" />
       </div>
       <div class="thumbnails-container" style="display: flex; gap: 10px; justify-content: center;">
           <?php 
           $gallery = array_filter([$product['image_url'],$product['image_2'] ?? '', $product['image_3'] ?? '',$product['image_4'] ?? '']);
           foreach($gallery as $index =>$imgBase64): 
               if(!empty($imgBase64)):
           ?>
               <div class="thumb-box" onclick="document.getElementById('mainProductImage').src='<?php echo htmlspecialchars($imgBase64); ?>';" style="width: 80px; height: 80px; border: 2px solid <?php echo $index === 0 ? 'var(--main-color)' : '#eee'; ?>; border-radius: 8px; cursor: pointer; padding: 5px; background: #fff; overflow: hidden;">
                   <img src="<?php echo htmlspecialchars($imgBase64); ?>" style="width: 100%; height: 100%; object-fit: cover;" />
               </div>
           <?php 
               endif;
           endforeach; 
           ?>
       </div>
       <script>
           document.querySelectorAll('.thumb-box').forEach(box => {
               box.addEventListener('click', function() {
                   document.querySelectorAll('.thumb-box').forEach(b => b.style.borderColor = '#eee');
                   this.style.borderColor = 'var(--main-color)';
               });
           });
       </script>
     </div>
      <div class="card_details" style="text-align: center; margin-top: 20px;">
        <div style="margin-bottom: 15px;">
            <h2 class="product_details_title card_title" style="font-size: 24px; color: var(--main-color); margin: 0;"><?php echo htmlspecialchars($product['title']); ?></h2>
        </div>

        <div class="rating" style="margin-bottom: 15px; font-size: 18px;">
          <?php echo $starsHtml; ?>
        </div>

        <p class="product_details_price card_price" style="font-size: 28px; font-weight: bold; margin-bottom: 30px; display: inline-flex; align-items: center; gap: 12px;">
            <span><?php echo htmlspecialchars($product['price']); ?> ج.م</span>
            <?php if(!empty($product['old_price']) && $product['old_price'] > $product['price']): ?>
                <del style="color: #94a3b8; font-size: 0.7em; font-weight: normal;"><?php echo htmlspecialchars($product['old_price']); ?> ج.م</del>
            <?php endif; ?>
        </p>

        <?php if (!empty($product['description'])): ?>
        <div style="text-align: right; background: #f9f9f9; padding: 20px; border-radius: 8px; margin-bottom: 25px; line-height: 1.8; color: #555; border: 1px solid #eee;">
            <h3 style="margin-top:0; color:#2c3e50; border-bottom: 2px solid var(--main-color); display: inline-block; padding-bottom: 5px; font-size: 18px;"><i class="fa-solid fa-circle-info"></i> مواصفات وتفاصيل المنتج</h3>
            <div style="margin-top: 15px; font-size: 15px;">
                <?php echo nl2br(strip_tags($product['description'])); ?>
            </div>
        </div>
        <?php endif; ?>
        <div style="display: flex; justify-content: center; align-items: center; gap: 15px; width: 100%; margin-top: 15px;">
            <button class="card_btn" data-id="<?php echo $product['id']; ?>" style="flex: 1; margin: 0; padding: 12px 15px; font-size: 16px; position: relative; left: 0; transform: none;">أضافة إلي العربة</button>
            <?php 
            $favoriteIds = [];
            if (Session::isLoggedIn()) {
                $favoriteModel = new Favorite();
                $favoriteIds = $favoriteModel->getUserFavoriteIds(Session::get('user_id'));
            }
            $isFavorited = in_array($product['id'], $favoriteIds);
            ?>
            <button class="heart-action-btn" data-product-id="<?php echo $product['id']; ?>" style="font-size: 26px; color: #e74c3c; cursor: pointer; transition: 0.3s; padding: 10px 15px; border: 1px solid #eee; border-radius: 8px; display: flex; align-items: center; justify-content: center; background: #fff;" title="إضافة للمفضلة">
                <i class="<?php echo $isFavorited ? 'fa-solid' : 'fa-regular'; ?> fa-heart"></i>
            </button>
        </div>
      </div>
    </div>
  </div>

  <div id="comments-section" class="container" style="margin-bottom: 60px;">
    <div class="card" style="max-width: 800px; margin: 0 auto; padding: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border-radius: 10px;">
        <h2 style="color: #2c3e50; margin-top:0; border-bottom: 2px solid #eee; padding-bottom: 15px;">آراء وتقييمات العملاء <i class="fa-solid fa-comments" style="color: var(--main-color);"></i></h2>

        <div class="comments-list" style="margin-top: 25px; margin-bottom: 40px;">
            <?php if (!empty($comments)): ?>
                <?php foreach ($comments as $c): ?>
                    <div style='background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 15px; border-right: 4px solid var(--main-color);'>
                        <h4 style='margin: 0 0 5px 0; color: #333; display: flex; justify-content: space-between; align-items: center;'>
                            <span><i class='fa-solid fa-circle-user' style='color:#bdc3c7;'></i> <?php echo htmlspecialchars($c['customer_name']); ?></span>
                            <span style='font-size:12px; color:#999; font-weight:normal;'><i class='fa-regular fa-clock'></i> <?php echo date('Y-m-d', strtotime($c['created_at'])); ?></span>
                        </h4>
                        <div style='margin-bottom: 10px; color:#f1c40f; font-size:13px;'>
                            <?php 
                            $u_rating = isset($c['user_rating']) ? (int)$c['user_rating'] : 5;
                            for($i=1; $i<=5; $i++) echo $i <= $u_rating ? '<i class="fa-solid fa-star"></i>' : '<i class="fa-regular fa-star"></i>';
                            ?>
                        </div>
                        <p style='margin: 0; color: #555; line-height: 1.6; font-size: 15px;'><?php echo nl2br(htmlspecialchars($c['comment_text'])); ?></p>
                        <?php if (!empty($c['admin_reply'])): ?>
                            <div style='margin-top: 15px; padding: 15px; background: #e8f4f8; border-radius: 5px; border-right: 4px solid #3498db;'>
                                <strong style='color: #2980b9; display:block; margin-bottom: 5px;'><i class='fa-solid fa-headset'></i> رد إدارة المتجر:</strong>
                                <p style='margin: 0; color: #444; line-height: 1.6; font-size: 14.5px;'><?php echo nl2br(htmlspecialchars($c['admin_reply'])); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style='text-align:center; padding: 30px; background: #f9f9f9; border-radius: 8px; border: 1px dashed #ddd;'>
                    <i class='fa-regular fa-comment-dots' style='font-size: 40px; color: #ccc; margin-bottom: 10px;'></i>
                    <p style='color:#777; margin: 0; font-size: 16px;'>لا توجد تعليقات حتى الآن. كن أول من يشاركنا رأيه وتجربته!</p>
                </div>
            <?php endif; ?>
        </div>

        <div style="background: #fff; padding: 25px; border-radius: 8px; border: 1px solid #eee;">
            <h3 style="margin-top:0; margin-bottom: 20px; color:#333;"><i class="fa-solid fa-pen"></i> أضف تقييمك للمنتج</h3>
            <?php if (!Session::isLoggedIn()): ?>
                <div style="text-align: center; padding: 20px; background: #f8fafc; border-radius: 8px; border: 1px dashed #cbd5e1;">
                    <i class="fa-solid fa-lock" style="font-size: 30px; color: #94a3b8; margin-bottom: 10px;"></i>
                    <p style="color: #475569; font-size: 16px; margin: 0;">يجب عليك <a href="/login" style="color: var(--main-color); font-weight: bold;">تسجيل الدخول</a> وشراء المنتج لتتمكن من إضافة تقييم.</p>
                </div>
            <?php elseif (!isset($hasPurchased) || !$hasPurchased): ?>
                <div style="text-align: center; padding: 20px; background: #fef2f2; border-radius: 8px; border: 1px dashed #fca5a5;">
                    <i class="fa-solid fa-cart-circle-xmark" style="font-size: 30px; color: #f87171; margin-bottom: 10px;"></i>
                    <p style="color: #991b1b; font-size: 16px; margin: 0;">عذراً نظام التقييم متاح فقط للمشترين المؤكدين يجب إتمام شراء هذا المنتج لتتمكن من تقييمه</p>
                </div>
            
            <?php else: ?>
                <style>
                    .star-rating-input { display: flex; flex-direction: row-reverse; justify-content: flex-end; gap: 5px; margin-bottom: 15px; }
                    .star-rating-input input { display: none; }
                    .star-rating-input label { cursor: pointer; font-size: 28px; color: #e2e8f0; transition: color 0.2s; }
                    .star-rating-input input:checked ~ label, .star-rating-input label:hover, .star-rating-input label:hover ~ label { color: #f1c40f; }
                </style>
                <form method="POST" action="/product?id=<?php echo $id; ?>#comments-section">
                    <?= CSRF::getField() ?>
                    <div style="margin-bottom: 15px;">
                        <label style="display:block; margin-bottom:5px; color:#555; font-weight:bold;">تقييمك للمنتج:</label>
                        <div class="star-rating-input">
                            <input type="radio" id="star5" name="user_rating" value="5" checked><label for="star5"><i class="fa-solid fa-star"></i></label>
                            <input type="radio" id="star4" name="user_rating" value="4"><label for="star4"><i class="fa-solid fa-star"></i></label>
                            <input type="radio" id="star3" name="user_rating" value="3"><label for="star3"><i class="fa-solid fa-star"></i></label>
                            <input type="radio" id="star2" name="user_rating" value="2"><label for="star2"><i class="fa-solid fa-star"></i></label>
                            <input type="radio" id="star1" name="user_rating" value="1"><label for="star1"><i class="fa-solid fa-star"></i></label>
                        </div>
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label style="display:block; margin-bottom:5px; color:#555; font-weight:bold;">الاسم :</label>
                        <input type="text" name="customer_name" required value="<?= htmlspecialchars(explode(' ', Session::get('user_name'))[0]) ?>" style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 5px; font-family: inherit; font-size: 15px; box-sizing: border-box;">
                    </div>
                    <div style="margin-bottom: 20px;">
                        <label style="display:block; margin-bottom:5px; color:#555; font-weight:bold;">نص التقييم:</label>
                        <textarea name="comment_text" required placeholder="اكتب رأيك بصدق هنا ليفيد الآخرين..." rows="5" style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 5px; font-family: inherit; font-size: 15px; resize: vertical; box-sizing: border-box;"></textarea>
                    </div>
                    <button type="submit" name="submit_comment" style="background: var(--main-color); color: white; border: none; padding: 12px 30px; border-radius: 5px; cursor: pointer; font-size: 16px; font-family: inherit; font-weight: bold; transition: 0.3s; width: 100%;"><i class="fa-solid fa-paper-plane"></i> إرسال التقييم</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
  </div>

