<style>
  .card { max-width: 700px; margin: auto; }
  .card h2 { text-align: center; }
  .cancel-btn { display: block; text-align: center; margin-top: 20px; color: #64748b; text-decoration: none; font-weight: 600; }
  .cancel-btn:hover { color: #ef4444; }
</style>
  <div class="card">
    <h2><i class="fa-solid fa-pen-to-square" style="color:#38bdf8; margin-left:8px;"></i>تعديل بيانات المنتج</h2>

    <?php if (!empty($product)): ?>
    <div style="text-align:center; margin-bottom:25px;">
      <img src="<?php echo htmlspecialchars($product['image_url']); ?>" width="120" height="120" style="border-radius:16px; object-fit:cover; box-shadow: 0 8px 16px rgba(0,0,0,0.1);">
    </div>

    <form action="/admin/products/update" method="POST" enctype="multipart/form-data">
      <?= CSRF::getField() ?>
      <input type="hidden" name="id" value="<?php echo $product['id']; ?>">

      <div class="form-group">
        <label>اسم المنتج:</label>
        <input type="text" name="title" value="<?php echo htmlspecialchars($product['title']); ?>" required>
      </div>

      <div class="form-group">
        <label>القسم (Category):</label>
        <select name="category_class" required>
          <option value="">-- اختر القسم --</option>
          <?php
          $cats = ["هواتف", "جهاز لوحي", "لابتوب", "ساعات ذكية", "فلاشات", "كاميرات", "راوترات", "اكسسوارات", "مستعمل"];
          foreach ($cats as $cat) {
            $sel = ($product['category_class'] == $cat) ? 'selected' : '';
            echo "<option value='$cat' $sel>$cat</option>";
          }
          ?>
        </select>
      </div>

      <div class="form-group">
        <label>السعر (بالجنيه):</label>
        <input type="number" name="price" step="0.01" min="0" value="<?php echo htmlspecialchars($product['price']); ?>" required>
      </div>

      <div class="form-group">
        <label>الوصف التفصيلي للمنتج:</label>
        <textarea name="description" rows="10" required><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
      </div>

      <div class="form-group">
        <label>صورة المنتج (اتركها فارغة إذا لم ترد تغييرها):</label>
        <input type="file" name="image" accept="image/png, image/jpeg, image/gif, image/webp" style="padding: 12px;">
      </div>
      
      <div class="form-group">
        <label>صور إضافية للمنتج (اختياري):</label>
        <div style="display: flex; gap: 10px; margin-bottom: 10px;">
          <?php if (!empty($product['image_2'])): ?>
            <img src="<?php echo htmlspecialchars($product['image_2']); ?>" width="60" height="60" style="border-radius:8px; object-fit:cover;">
          <?php endif; ?>
          <input type="file" name="image_2" accept="image/png, image/jpeg, image/gif, image/webp" style="padding: 5px; flex: 1;">
        </div>
        <div style="display: flex; gap: 10px; margin-bottom: 10px;">
          <?php if (!empty($product['image_3'])): ?>
            <img src="<?php echo htmlspecialchars($product['image_3']); ?>" width="60" height="60" style="border-radius:8px; object-fit:cover;">
          <?php endif; ?>
          <input type="file" name="image_3" accept="image/png, image/jpeg, image/gif, image/webp" style="padding: 5px; flex: 1;">
        </div>
        <div style="display: flex; gap: 10px;">
          <?php if (!empty($product['image_4'])): ?>
            <img src="<?php echo htmlspecialchars($product['image_4']); ?>" width="60" height="60" style="border-radius:8px; object-fit:cover;">
          <?php endif; ?>
          <input type="file" name="image_4" accept="image/png, image/jpeg, image/gif, image/webp" style="padding: 5px; flex: 1;">
        </div>
      </div>

      <button type="submit" class="btn-submit" style="width:100%; display:block; margin-top:10px;"><i class="fa-solid fa-floppy-disk" style="margin-left:8px;"></i> حفظ التعديلات</button>
      <a href="/admin/products" class="cancel-btn"><i class="fa-solid fa-arrow-right" style="margin-left:5px;"></i> إلغاء والعودة للقائمة</a>
    </form>
    <?php endif; ?>
  </div>
</div>
