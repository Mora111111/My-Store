  <div class="cart">
    <h2 class="cart_title">سلة التسوق</h2>
    <div class="cart_content"></div>
    <div class="total">
      <div class="total_title">الاجمالي</div>
      <div class="total_price">.جنية</div>
    </div>
    <a href="/checkout" class="btn_buy">شراء</a>
    <div class="cart_empty">
      <div><img src="/images/Cart-img.png"></div>
      <p>عربة التسوق فارغة</p>
      <a href="/products" class="btn_shopping">إستكشف المنتجات</a>
    </div>
    <i class="fa-solid fa-xmark" id="cart-close"></i>
  </div>

  <div class="product_details_section container">
    <div class="card" style="margin-top: 150px; margin-bottom: 50px; max-width: 600px; margin-left: auto; margin-right: auto; padding: 20px;">
      <div class="box_img" style="height: 400px; background: transparent;">
          <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['title']); ?>" class="card_image" style="max-height: 100%; object-fit: contain;" />
      </div>
      <div class="card_details" style="text-align: center; margin-top: 20px;">
        <h2 class="product_details_title card_title" style="font-size: 24px; color: var(--main-color); margin-bottom: 15px;"><?php echo htmlspecialchars($product['title']); ?></h2>

        <div class="rating" style="margin-bottom: 15px; font-size: 18px;">
          <?php echo $starsHtml; ?>
        </div>

        <p class="product_details_price card_price" style="font-size: 28px; font-weight: bold; margin-bottom: 30px;">
          <?php echo $mainPrice; ?>.<small><?php echo $decimals; ?></small> <span>جنيه</span>
        </p>

        <?php if (!empty($product['description'])): ?>
        <div style="text-align: right; background: #f9f9f9; padding: 20px; border-radius: 8px; margin-bottom: 25px; line-height: 1.8; color: #555; border: 1px solid #eee;">
            <h3 style="margin-top:0; color:#2c3e50; border-bottom: 2px solid var(--main-color); display: inline-block; padding-bottom: 5px; font-size: 18px;"><i class="fa-solid fa-circle-info"></i> مواصفات وتفاصيل المنتج</h3>
            <div style="margin-top: 15px; font-size: 15px;">
                <?php echo $product['description']; ?>
            </div>
        </div>
        <?php endif; ?>
        <button class="card_btn" style="position: relative; left: 0; transform: none; margin: auto; padding: 12px 50px; font-size: 16px;">أضافة إلي العربة</button>
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
                        <h4 style='margin: 0 0 10px 0; color: #333; display: flex; justify-content: space-between; align-items: center;'>
                            <span><i class='fa-solid fa-circle-user' style='color:#bdc3c7;'></i> <?php echo htmlspecialchars($c['customer_name']); ?></span>
                            <span style='font-size:12px; color:#999; font-weight:normal;'><i class='fa-regular fa-clock'></i> <?php echo date('Y-m-d', strtotime($c['created_at'])); ?></span>
                        </h4>
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
            <h3 style="margin-top:0; margin-bottom: 20px; color:#333;"><i class="fa-solid fa-pen"></i> أضف تعليقك على المنتج</h3>
            <form method="POST" action="/product?id=<?php echo $id; ?>#comments-section">
                <?= CSRF::getField() ?>
                <div style="margin-bottom: 15px;">
                    <label style="display:block; margin-bottom:5px; color:#555; font-weight:bold;">الاسم :</label>
                    <input type="text" name="customer_name" required placeholder="أكتب اسمك هنا" style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 5px; font-family: inherit; font-size: 15px; box-sizing: border-box;">
                </div>
                <div style="margin-bottom: 20px;">
                    <label style="display:block; margin-bottom:5px; color:#555; font-weight:bold;">نص التعليق أو الاستفسار:</label>
                    <textarea name="comment_text" required placeholder="اكتب رأيك بصدق هنا ليفيد الآخرين..." rows="5" style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 5px; font-family: inherit; font-size: 15px; resize: vertical; box-sizing: border-box;"></textarea>
                </div>
                <button type="submit" name="submit_comment" style="background: var(--main-color); color: white; border: none; padding: 12px 30px; border-radius: 5px; cursor: pointer; font-size: 16px; font-family: inherit; font-weight: bold; transition: 0.3s; width: 100%;"><i class="fa-solid fa-paper-plane"></i> إرسال التعليق</button>
            </form>
        </div>
    </div>
  </div>
