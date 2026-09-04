  <div class="card">
    <h2><i class="fa-solid fa-pen-to-square" style="color:#38bdf8; margin-left:8px;"></i>تعديل بيانات المنتج</h2>

    <?php if (!empty($product)): ?>
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
        <label>سعر المنتج قبل الشطب </label>
        <input type="number" name="old_price" step="0.01" min="0" value="<?php echo htmlspecialchars($product['old_price'] ?? '0'); ?>">
      </div>
      <div class="form-group">
        <label>الوصف التفصيلي للمنتج:</label>
        <textarea name="description" rows="8" required><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
      </div>

     <div class="form-group" style="margin-top: 20px;">
        <label>إدارة صور المنتج (تعديل المستقل):</label>
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-top: 10px;">
           <?php 
           $imgFields = [
               ['name' => 'image', 'db' => 'image_url', 'label' => 'الرئيسية'],
               ['name' => 'image_2', 'db' => 'image_2', 'label' => 'صورة 2'],
               ['name' => 'image_3', 'db' => 'image_3', 'label' => 'صورة 3'],
               ['name' => 'image_4', 'db' => 'image_4', 'label' => 'صورة 4']
           ];
           foreach($imgFields as $f):$currentImg = $product[$f['db']] ?? '';
           ?>
           <div style="border: 1px solid #e2e8f0; padding: 10px; border-radius: 8px; text-align: center; background: #f8fafc;">
               <p style="margin: 0 0 10px 0; font-size: 14px; font-weight: bold; color: #475569;"><?php echo $f['label']; ?></p>
               <div style="height: 100px; display: flex; align-items: center; justify-content: center; margin-bottom: 10px; background: #fff; border-radius: 4px; overflow: hidden;">
                   <?php if(!empty($currentImg)): ?>
                       <?php $displaySrc = strpos($currentImg, 'data:image') === 0 ? $currentImg : '/' . ltrim($currentImg, '/'); ?>
                       <img src="<?php echo htmlspecialchars($displaySrc); ?>" onerror="this.onerror=null;this.src='/images/logos/logo.png';" style="max-height: 100%; max-width: 100%; object-fit: contain;">
                   <?php else: ?>
                       <span style="color: #cbd5e1; font-size: 12px;">لا توجد صورة</span>
                   <?php endif; ?>
               </div>
               <input type="file" name="<?php echo $f['name']; ?>" accept="image/png, image/jpeg, image/webp" style="width: 100%; font-size: 12px;">
           </div>
           <?php endforeach; ?>
        </div>
     </div>

      <button type="submit" class="btn-submit" style="width:100%; display:block; margin-top:10px;"><i class="fa-solid fa-floppy-disk" style="margin-left:8px;"></i> حفظ التعديلات</button>
      <a href="/admin/products" class="cancel-btn"><i class="fa-solid fa-arrow-right" style="margin-left:5px;"></i> إلغاء والعودة للقائمة</a>
    </form>
    <?php endif; ?>
  </div>
</div>
