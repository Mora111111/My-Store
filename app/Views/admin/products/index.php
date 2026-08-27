<div class="card">
  <div style="display: flex; justify-content: space-between; align-items: center;">
    <h2 style="margin-bottom:0;"><i class="fa-solid fa-plus-circle"></i> إضافة منتج جديد</h2>
    <button onclick="openAddModal()" class="btn-submit" style="text-decoration:none; border:none; cursor:pointer;"><i class="fa-solid fa-plus"></i> إضافة</button>
  </div>
</div>

<div class="card">
  <h2><i class="fa-solid fa-list"></i> المنتجات الحالية</h2>
  <div style="margin-bottom:15px;"><input type="text" id="searchInput" placeholder="بحث ذكي باسم المنتج..." style="padding:12px; width:100%; max-width:400px; border:1px solid #cbd5e1; border-radius:8px; outline:none; font-family:inherit;"></div>
  <table>
    <tr>
      <th>الصورة</th>
      <th>اسم المنتج</th>
      <th>القسم</th>
      <th>السعر</th>
      <th>الإجراءات</th>
    </tr>
    <?php if (!empty($products)): ?>
      <?php foreach ($products as $row): ?>
      <tr>
<?php $imgPath = !empty($row['image_url']) ? $row['image_url'] : (!empty($row['image']) ? $row['image'] : 'uploads/default.png'); ?>
        <td><img src="/<?php echo ltrim($imgPath, '/'); ?>" width="60" height="60" style="border-radius:12px; object-fit:cover; box-shadow:0 4px 6px rgba(0,0,0,0.05);"></td>        <td style="font-weight:500;"><?php echo htmlspecialchars($row['title']); ?></td>
        <td><span class="badge"><?php echo htmlspecialchars($row['category_class']); ?></span></td>
        <td style="font-weight:700; color:#0f172a;"><?php echo htmlspecialchars($row['price']); ?> ج.م</td>
        <td>
          <a href="/admin/products/edit?id=<?php echo $row['id']; ?>" class="action-btn btn-edit"><i class="fa-solid fa-pen"></i> تعديل</a>
          <a href="/admin/products/delete?id=<?php echo $row['id']; ?>" class="action-btn btn-delete" onclick="return confirm('هل أنت متأكد من حذف هذا المنتج نهائياً؟');"><i class="fa-solid fa-trash"></i> حذف</a>
        </td>
      </tr>
      <?php endforeach; ?>
    <?php else: ?>
      <tr><td colspan="5" style="text-align:center; padding:40px; color:#94a3b8; font-size:16px;"><i class="fa-solid fa-box-open" style="font-size:40px; margin-bottom:15px; opacity:0.5;"></i><br>لا توجد منتجات مضافة حتى الآن.</td></tr>
    <?php endif; ?>
  </table>
</div>

<div class="modal-overlay" id="addProductModal">
  <div class="modal-content" style="max-width: 900px; width: 95%; max-height: 90vh; overflow-y: auto;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
      <h2 style="margin:0; color:#0f172a;"><i class="fa-solid fa-plus-circle" style="color:#38bdf8; margin-left:8px;"></i>إضافة منتج جديد</h2>
      <button type="button" class="ai-magic-btn" id="openAiModal">
        <i class="fa-solid fa-wand-magic-sparkles"></i> Ai
    </div>

    <form action="/admin/products/store" method="POST" enctype="multipart/form-data">
      <?= CSRF::getField() ?>
      
      <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-bottom: 15px;">
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
      </div>

      <div class="form-group">
        <label>الوصف التفصيلي للمنتج:</label>
        <textarea name="description" rows="6" required placeholder="أدخل وصفاً تسويقياً وتفصيلياً للمنتج..."></textarea>
      </div>

      <div class="form-group">
        <label>صورة المنتج:</label>
        <input type="file" name="image" accept="image/png, image/jpeg, image/gif, image/webp" required style="padding: 5px;">
      </div>
      <div class="form-group">
        <label>صور إضافية للمنتج (اختياري - حتى 3 صور):</label>
        <input type="file" name="image_2" accept="image/png, image/jpeg, image/gif, image/webp" style="padding: 5px; margin-bottom: 5px;">
        <input type="file" name="image_3" accept="image/png, image/jpeg, image/gif, image/webp" style="padding: 5px; margin-bottom: 5px;">
        <input type="file" name="image_4" accept="image/png, image/jpeg, image/gif, image/webp" style="padding: 5px;">
      </div>

      <div style="display:flex; gap:10px; margin-top:25px;">
        <button type="submit" class="btn-submit" style="flex:2;"><i class="fa-solid fa-plus" style="margin-left: 8px;"></i> حفظ المنتج</button>
        <button type="button" class="btn-cancel" style="flex:1;" onclick="closeAddModal()">إلغاء</button>
      </div>
    </form>
  </div>
</div>

<div class="ai-modal-overlay" id="aiModal">
  <div class="ai-modal-content">
    <h3 style="margin-top:0;"><i class="fa-solid fa-robot" style="color:#8b5cf6;"></i> المساعد الذكي</h3>
    <p style="font-size: 0.9rem; color: #64748b; margin-bottom: 5px;">أدخل اسم الجهاز وسأقوم بتوليد البيانات تلقائياً.</p>
    <input type="text" id="aiPrompt" placeholder="مثال: سامسونج S24 الترا..." style="width:100%; padding:12px; margin:15px 0; border:1px solid #cbd5e1; border-radius:8px; box-sizing:border-box;">
    <div style="display:flex; gap:10px;">
      <button type="button" id="generateAiData" style="background:#8b5cf6; color:white; border:none; padding:10px 20px; border-radius:8px; cursor:pointer; font-weight:bold; flex:1;">توليد البيانات</button>
      <button type="button" onclick="closeAiModal()" style="background:#e2e8f0; color:#475569; border:none; padding:10px 20px; border-radius:8px; cursor:pointer; font-weight:bold; flex:1;">رجوع</button>
    </div>
  </div>
</div>
<script>
function openAddModal() { document.getElementById('addProductModal').style.display = 'flex'; }
function closeAddModal() { document.getElementById('addProductModal').style.display = 'none'; }

document.getElementById('openAiModal').addEventListener('click', () => {
    document.getElementById('aiModal').style.display = 'flex';
});
function closeAiModal() { document.getElementById('aiModal').style.display = 'none'; }

document.getElementById('generateAiData').addEventListener('click', async () => {
    const prompt = document.getElementById('aiPrompt').value.trim();
    const btn = document.getElementById('generateAiData');
    
    const csrfToken = document.querySelector('input[name="csrf_token"]')?.value || '';
    
    if(!prompt) return;
    
    btn.textContent = 'جاري التوليد...';
    btn.disabled = true;
    
    try {
        const formData = new FormData();
        formData.append('prompt', prompt);
        formData.append('csrf_token', csrfToken);

        const response = await fetch('/ai/generate-product', {
            method: 'POST',
            body: formData 
        });
        
        const data = await response.json();
        
        if(data && !data.error) {
            if(data.title) document.querySelector('input[name="title"]').value = data.title;
            if(data.category_class) document.querySelector('select[name="category_class"]').value = data.category_class;
            if(data.price) document.querySelector('input[name="price"]').value = data.price;
            if(data.description) {
                let cleanText = data.description.replace(/<br\s*[\/]?>/gi, "\n").replace(/<\/p>/gi, "\n\n").replace(/<[^>]+>/ig, "");
                document.querySelector('textarea[name="description"]').value = cleanText.trim();
            }
            
            closeAiModal();
        } else {
            alert('تعذر التوليد: ' + (data.error || 'خطأ غير معروف'));
        }
    } catch (error) {
        alert('حدث خطأ في الاتصال بالخادم. تأكد من أدوات المطور.');
    } finally {
        btn.textContent = 'توليد البيانات';
        btn.disabled = false;
    }
});

const searchInput = document.getElementById('searchInput');
if(searchInput) {
    searchInput.addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('table tr:not(:first-child)');
        rows.forEach(row => {
            let titleCell = row.querySelector('td:nth-child(2)');
            if(titleCell) {
                let text = titleCell.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            }
        });
    });
}
</script>