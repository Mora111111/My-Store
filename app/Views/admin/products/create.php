  <div class="card">
    <h2><i class="fa-solid fa-plus-circle" style="color:#38bdf8; margin-left:8px;"></i>إضافة منتج جديد</h2>

    <form action="/admin/products/store" method="POST" enctype="multipart/form-data">
      <?= CSRF::getField() ?>

      <div class="form-group">
        <label>اسم المنتج:</label>
        <input type="text" name="title" required placeholder="أدخل اسم المنتج">
      </div>

      <div class="form-group">
        <label>القسم (Category):</label>
        <select name="category_class" required>
          <option value="">-- اختر القسم --</option>
          <option value="هواتف">هواتف</option>
          <option value="جهاز لوحي">جهاز لوحي (تابلت)</option>
          <option value="لابتوب">لابتوب</option>
          <option value="ساعات ذكية">ساعات ذكية</option>
          <option value="فلاشات">فلاشات</option>
          <option value="كاميرات">كاميرات</option>
          <option value="راوترات">راوترات</option>
          <option value="اكسسوارات">اكسسوارات</option>
          <option value="مستعمل">مستعمل</option>
        </select>
      </div>

      <div class="form-group">
        <label>السعر (بالجنيه):</label>
        <input type="number" name="price" step="0.01" min="0" required placeholder="مثال: 45000">
      </div>
      <div class="form-group">
        <label>سعر المنتج قبل الشطب </label>
        <input type="number" name="old_price" step="0.01" min="0" value="0" placeholder="اتركه 0 إذا لم يكن هناك خصم">
      </div>
      <div class="form-group">
        <label>الكمية المتاحة في المخزن:</label>
        <input type="number" name="quantity" min="0" value="10" required>
      </div>
      <div class="form-group">
        <label>الوصف التفصيلي للمنتج:</label>
        <textarea name="description" rows="8" required placeholder="أدخل وصفاً تفصيلياً للمنتج..."></textarea>
      </div>

      <div class="form-group">
        <label>صورة المنتج الرئيسية:</label>
        <input type="file" name="image" accept="image/png, image/jpeg, image/gif, image/webp" required style="padding: 5px;">
      </div>
      <div class="form-group">
        <label>صور إضافية للمنتج (اختياري - حتى 3 صور):</label>
        <input type="file" name="image_2" accept="image/png, image/jpeg, image/gif, image/webp" style="padding: 5px; margin-bottom: 5px;">
        <input type="file" name="image_3" accept="image/png, image/jpeg, image/gif, image/webp" style="padding: 5px; margin-bottom: 5px;">
        <input type="file" name="image_4" accept="image/png, image/jpeg, image/gif, image/webp" style="padding: 5px;">
      </div>

      <button type="submit" class="btn-submit" style="width:100%; display:block; margin-top:10px;"><i class="fa-solid fa-floppy-disk" style="margin-left:8px;"></i> إضافة المنتج</button>
      <a href="/admin/products" class="cancel-btn"><i class="fa-solid fa-arrow-right" style="margin-left:5px;"></i> إلغاء والعودة للقائمة</a>
    </form>
  </div>
</div>
