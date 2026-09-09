<div class="card">
  <div style="display: flex; justify-content: space-between; align-items: center;">
    <h2 style="margin-bottom:0;"><i class="fa-solid fa-tags"></i> إدارة كوبونات الخصم</h2>
    <button onclick="document.getElementById('addCouponModal').style.display='flex';" class="btn-submit" style="text-decoration:none; border:none; cursor:pointer;"><i class="fa-solid fa-plus"></i> إضافة كود جديد</button>
  </div>
</div>

<div class="card">
  <table>
    <tr>
      <th>كود الخصم</th>
      <th>القيمة</th>
      <th>الاستهداف</th>
      <th>شطب السعر</th>
      <th>الحالة</th>
      <th>الإجراءات</th>
    </tr>
    <?php if (!empty($coupons)): ?>
      <?php foreach ($coupons as $row): ?>
      <tr>
        <td style="font-weight:700; font-family: monospace; font-size: 16px; color:#3b82f6;"><?= htmlspecialchars($row['code']) ?></td>
        <td style="font-weight:600;"><?= $row['discount_value'] ?> <?= $row['discount_type'] === 'percentage' ? '%' : 'ج.م' ?></td>
        <td>
          <?php if($row['target_type'] === 'all'): ?>
            <span class="badge" style="background:#f0fdf4; color:#166534; border:1px solid #bbf7d0; padding:6px 14px; font-size:13px;"><i class="fa-solid fa-store" style="margin-left:5px;"></i> المتجر بالكامل</span>
          <?php else: ?>
            <span class="badge" style="background:#eff6ff; color:#1e40af; border:1px solid #bfdbfe; padding:6px 14px; font-size:13px;"><i class="fa-solid fa-box-open" style="margin-left:5px;"></i> منتج محدد</span>
          <?php endif; ?>
        </td>
        <td><?= $row['show_strikethrough'] ? '<i class="fa-solid fa-check" style="color:#10b981;"></i>' : '<i class="fa-solid fa-xmark" style="color:#ef4444;"></i>' ?></td>
        <td>
            <form method="POST" action="/admin/coupons/toggle" style="margin:0;">
                <?= CSRF::getField() ?>
                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                <input type="hidden" name="current_status" value="<?= $row['status'] ?>">
                <button type="submit" style="background:none; border:none; cursor:pointer; font-family:inherit;">
                    <?php if($row['status']): ?>
                        <span class="badge-success"><i class="fa-solid fa-toggle-on"></i> فعال</span>
                    <?php else: ?>
                        <span class="badge" style="background:#fee2e2; color:#991b1b;"><i class="fa-solid fa-toggle-off"></i> معطل</span>
                    <?php endif; ?>
                </button>
            </form>
        </td>
        <td>
          <form method="POST" action="/admin/coupons/delete" style="display:inline-block;" onsubmit="return confirm('هل أنت متأكد من حذف هذا الكود نهائياً؟');">
            <?= CSRF::getField() ?>
            <input type="hidden" name="id" value="<?= $row['id'] ?>">
            <button type="submit" class="action-btn btn-delete" style="cursor:pointer;"><i class="fa-solid fa-trash"></i> حذف</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
    <?php else: ?>
      <tr>
          <td colspan="6" style="text-align:center; padding:60px 20px; color:#94a3b8;">
              <div style="background: #f8fafc; display: inline-flex; justify-content: center; align-items: center; width: 100px; height: 100px; border-radius: 50%; margin-bottom: 20px; border: 1px dashed #cbd5e1;">
                 <i class="fa-solid fa-tags" style="font-size:40px; color:#cbd5e1;"></i>
              </div>
              <br><span style="font-size: 18px; font-weight: 700; color: #475569;">لا توجد كوبونات خصم حتى الآن</span>
              <p style="margin-top: 8px; font-size: 14px;">قم بإضافة كوبونات خصم جديدة لزيادة مبيعات متجرك.</p>
          </td>
      </tr>
    <?php endif; ?>
  </table>
</div>

<div class="modal-overlay" id="addCouponModal">
  <div class="modal-content" style="max-width: 600px;">
    <h2 style="margin:0 0 20px 0; color:#0f172a;"><i class="fa-solid fa-tag" style="color:#38bdf8; margin-left:8px;"></i>إضافة كود خصم</h2>
    <form action="/admin/coupons/store" method="POST">
      <?= CSRF::getField() ?>
      <div class="form-group">
        <label>كود الخصم (مثال: WINTER20):</label>
        <input type="text" name="code" required placeholder="ادخل الكود باللغة الإنجليزية" style="text-transform: uppercase;">
      </div>
      
      <div style="display:flex; gap:15px;">
          <div class="form-group" style="flex:1;">
            <label>نوع الخصم:</label>
            <select name="discount_type" required>
              <option value="percentage">نسبة مئوية (%)</option>
              <option value="fixed">مبلغ ثابت (ج.م)</option>
            </select>
          </div>
          <div class="form-group" style="flex:1;">
            <label>قيمة الخصم:</label>
            <input type="number" name="discount_value" step="0.01" min="0.01" required placeholder="مثال: 20">
          </div>
      </div>

      <div class="form-group">
        <label>استهداف الخصم:</label>
        <select name="target_type" id="targetTypeSelect" required onchange="toggleProductSelect()">
          <option value="all">كل منتجات المتجر</option>
          <option value="specific_product">منتج محدد فقط</option>
        </select>
      </div>

      <div class="form-group" id="productSelectWrapper" style="display:none; position:relative;">
        <label>اختر المنتج المستهدف:</label>
        <input type="hidden" name="target_product_id" id="hidden_target_product_id">
        <div style="position: relative;">
            <input type="text" id="product_search_input" placeholder="🔍 ابحث باسم المنتج لفرز القائمة..." autocomplete="off" style="width: 100%; padding: 14px; border: 2px solid #e2e8f0; border-radius: 12px; outline: none; font-family: inherit; font-size: 14px; transition: 0.3s; background: #fafbfc;">
        </div>
        
        <div id="product_dropdown_list" style="display:none; position:absolute; width:100%; max-height:250px; overflow-y:auto; background:#fff; border:1px solid #e2e8f0; border-radius:12px; top:calc(100% + 5px); left:0; z-index:1000; box-shadow:0 10px 25px rgba(0,0,0,0.1);">
          <?php if(!empty($products)): foreach($products as $p): ?>
            <div class="product-option" data-id="<?= $p['id'] ?>" data-title="<?= htmlspecialchars($p['title']) ?>" style="padding:10px 15px; cursor:pointer; border-bottom:1px solid #f1f5f9; display:flex; align-items:center; gap:12px; transition:0.2s;">
               <img src="<?= Product::getImageUrl($p['image_url']) ?>" style="width:40px; height:40px; border-radius:8px; object-fit:contain; border:1px solid #f1f5f9;">
               <div style="flex:1; display:flex; flex-direction:column;">
                   <span style="font-weight:700; font-size:14px; color:#0f172a;"><?= htmlspecialchars($p['title']) ?></span>
                   <span style="color:#ef4444; font-weight:bold; font-size:13px; direction:ltr; text-align:left;"><?= $p['price'] ?> ج.م</span>
               </div>
            </div>
          <?php endforeach; endif; ?>
        </div>
      </div>

      <div class="form-group" style="display:flex; align-items:center; gap:10px; background:#f8fafc; padding:15px; border-radius:12px; border:1px solid #e2e8f0;">
          <input type="checkbox" name="show_strikethrough" id="showStrike" value="1" style="width:20px; height:20px; cursor:pointer;" checked>
          <label for="showStrike" style="margin:0; cursor:pointer; font-weight:bold; color:#0f172a;">تطبيق علامة "شطب السعر القديم" على المنتجات المستهدفة فوراً</label>
      </div>

      <div style="display:flex; gap:10px; margin-top:25px;">
        <button type="submit" class="btn-submit" style="flex:2;">حفظ الكود</button>
        <button type="button" class="btn-cancel" style="flex:1;" onclick="document.getElementById('addCouponModal').style.display='none';">إلغاء</button>
      </div>
    </form>
  </div>
</div>
<script>
const searchInput = document.getElementById('product_search_input');
const dropdownList = document.getElementById('product_dropdown_list');
const hiddenInput = document.getElementById('hidden_target_product_id');
const options = document.querySelectorAll('.product-option');

// إظهار القائمة عند الضغط على مربع البحث
searchInput.addEventListener('focus', () => {
    dropdownList.style.display = 'block';
    searchInput.style.borderColor = '#38bdf8';
    searchInput.style.background = '#fff';
});

// إخفاء القائمة عند النقر خارجها
document.addEventListener('click', (e) => {
    if (!searchInput.contains(e.target) && !dropdownList.contains(e.target)) {
        dropdownList.style.display = 'none';
        searchInput.style.borderColor = '#e2e8f0';
        searchInput.style.background = '#fafbfc';
    }
});

// فلترة المنتجات بناءً على البحث
searchInput.addEventListener('input', function() {
    const filter = this.value.toLowerCase();
    let hasVisible = false;
    options.forEach(option => {
        const title = option.getAttribute('data-title').toLowerCase();
        if (title.includes(filter)) {
            option.style.display = 'flex';
            hasVisible = true;
        } else {
            option.style.display = 'none';
        }
    });
    dropdownList.style.display = hasVisible ? 'block' : 'none';
});

// اختيار منتج من القائمة
options.forEach(option => {
    option.addEventListener('click', function() {
        const id = this.getAttribute('data-id');
        const title = this.getAttribute('data-title');
        
        hiddenInput.value = id;
        searchInput.value = title; // إظهار الاسم في المربع
        dropdownList.style.display = 'none';
    });
    
    // تأثير Hover على الخيارات
    option.addEventListener('mouseover', () => option.style.backgroundColor = '#f8fafc');
    option.addEventListener('mouseout', () => option.style.backgroundColor = 'transparent');
});

function toggleProductSelect() {
    const type = document.getElementById('targetTypeSelect').value;
    const wrapper = document.getElementById('productSelectWrapper');
    if (type === 'specific_product') {
        wrapper.style.display = 'block';
        hiddenInput.required = true;
        searchInput.required = true;
    } else {
        wrapper.style.display = 'none';
        hiddenInput.required = false;
        searchInput.required = false;
        hiddenInput.value = '';
        searchInput.value = '';
    }
}
</script>
