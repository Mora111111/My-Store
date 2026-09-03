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
        <td><span class="badge"><?= $row['target_type'] === 'all' ? 'المتجر بالكامل' : 'منتج محدد' ?></span></td>
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
      <tr><td colspan="6" style="text-align:center; padding:40px; color:#94a3b8;"><i class="fa-solid fa-ticket-simple" style="font-size:40px; margin-bottom:15px; opacity:0.5;"></i><br>لا توجد كوبونات خصم حتى الآن.</td></tr>
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

      <div class="form-group" id="productSelectWrapper" style="display:none;">
        <label>اختر المنتج (يظهر في حالة منتج محدد):</label>
        <select name="target_product_id">
          <?php if(!empty($products)): foreach($products as $p): ?>
            <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['title']) ?> (<?= $p['price'] ?> ج.م)</option>
          <?php endforeach; endif; ?>
        </select>
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
function toggleProductSelect() {
    const type = document.getElementById('targetTypeSelect').value;
    document.getElementById('productSelectWrapper').style.display = type === 'specific_product' ? 'block' : 'none';
}
</script>
