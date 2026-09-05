<div class="card">
    <h2 style="margin-top:0;"><i class="fa-solid fa-list"></i> سجل جميع الطلبات</h2>
    <table>
      <tr>
        <th>رقم الطلب</th>
        <th>العميل</th>
        <th>الهاتف</th>
        <th>الإجمالي</th>
        <th>الحالة الحالية</th>
        <th>تغيير الحالة</th>
        <th>الإجراءات</th>
      </tr>
      <?php
      $status_map = [
        'قيد المراجعة' => 'status-pending',
        'تم الشحن' => 'status-shipped',
        'مكتمل' => 'status-completed',
        'ملغي' => 'status-cancelled'
      ];
      ?>
      <?php if (!empty($orders)): ?>
        <?php foreach ($orders as $row):
          $status = $row['status'];
          $status_class = $status_map[$status] ?? 'status-pending';
          $products_json = htmlspecialchars($row['products'], ENT_QUOTES, 'UTF-8');
        ?>
        <tr>
          <td><span style="font-weight:700;">#<?php echo $row['id']; ?></span></td>
          <td style="font-weight:500;"><?php echo htmlspecialchars($row['full_name']); ?></td>
          <td><a href="tel:<?php echo htmlspecialchars($row['phone']); ?>" style="color:#3b82f6; text-decoration:none;"><?php echo htmlspecialchars($row['phone']); ?></a></td>
          <td style="font-weight:700; color:#0f172a;"><?php echo htmlspecialchars($row['total_price']); ?> ج.م</td>
          <td><span class="status-badge <?php echo $status_class; ?>"><?php echo $status; ?></span></td>
          <td>
            <form method="POST" action="/admin/orders/update" class="status-form">
              <?= CSRF::getField() ?>
              <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
              <select name="new_status" class="status-select">
                <option value="قيد المراجعة" <?php echo $status == 'قيد المراجعة' ? 'selected' : ''; ?>>قيد المراجعة</option>
                <option value="تم الشحن" <?php echo $status == 'تم الشحن' ? 'selected' : ''; ?>>تم الشحن</option>
                <option value="مكتمل" <?php echo $status == 'مكتمل' ? 'selected' : ''; ?>>مكتمل</option>
                <option value="ملغي" <?php echo $status == 'ملغي' ? 'selected' : ''; ?>>ملغي</option>
              </select>
              <button type="submit" class="btn-update"><i class="fa-solid fa-check"></i> تأكيد</button>
            </form>
          </td>
          <td>
            <div class="actions-flex">
                <button class="btn-view details-btn"
                  data-id="<?php echo $row['id']; ?>"
                  data-products="<?php echo $products_json; ?>"
                  data-address1="<?php echo htmlspecialchars($row['address_line1'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                  data-address2="<?php echo htmlspecialchars($row['address_line2'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                  data-city="<?php echo htmlspecialchars($row['city'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                  data-gov="<?php echo htmlspecialchars($row['governorate'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                  data-zip="<?php echo htmlspecialchars($row['zip_code'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                  data-date="<?php echo isset($row['created_at']) ? date('Y-m-d H:i', strtotime($row['created_at'])) : ''; ?>">
                  <i class="fa-solid fa-eye"></i> عرض
                </button>
                <?php if ($status === 'ملغي'): ?>
                  <form method="POST" action="/admin/orders/delete" style="display:inline-block;" onsubmit="return confirm('هل أنت متأكد من حذف هذا الطلب نهائياً من قاعدة البيانات؟');">
                    <?= CSRF::getField() ?>
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                    <button type="submit" class="btn-delete" style="border:none; cursor:pointer; font-family:inherit;"><i class="fa-solid fa-trash"></i> حذف</button>
                  </form>
                <?php endif; ?>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="7" style="text-align:center; padding:40px; color:#94a3b8; font-size:16px;"><i class="fa-solid fa-cart-shopping" style="font-size:40px; margin-bottom:15px; opacity:0.5;"></i><br>لا توجد طلبات حتى الآن.</td></tr>
      <?php endif; ?>
    </table>
  </div>

<div id="adminOrderModal" class="modal">
  <div class="modal-content" style="width: 700px; max-width: 95%;">
    <span class="close-modal" id="closeAdminModalBtn">&times;</span>
    <h3 style="margin-top:0; padding-bottom:15px; color: #0f172a; font-size:22px; border-bottom:2px solid #38bdf8; display: flex; align-items: center; gap: 10px;">
      <i class="fa-solid fa-receipt" style="color:#38bdf8;"></i> تفاصيل الطلب رقم #<span id="modalOrderId"></span>
    </h3>
    
    <div class="address-box" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; background: #f8fafc; padding: 20px; border-radius: 20px; margin: 20px 0; border: 1px solid #e2e8f0;">
      <div>
        <h4 style="margin:0 0 12px 0; color:#0f172a; font-size: 15px;"><i class="fa-solid fa-truck" style="margin-left:8px; color: #38bdf8;"></i>بيانات الشحن:</h4>
        <p style="margin:6px 0; font-size: 14px;"><strong>العنوان 1:</strong> <span id="modalAddr1" style="color: #475569;"></span></p>
        <p style="margin:6px 0; font-size: 14px;"><strong>العنوان 2:</strong> <span id="modalAddr2" style="color: #475569;"></span></p>
        <p style="margin:6px 0; font-size: 14px;"><strong>المدينة:</strong> <span id="modalCityGov" style="color: #475569;"></span></p>
      </div>
      <div style="border-right: 1px solid #e2e8f0; padding-right: 20px;">
        <h4 style="margin:0 0 12px 0; color:#0f172a; font-size: 15px;"><i class="fa-solid fa-calendar-day" style="margin-left:8px; color: #38bdf8;"></i>معلومات إضافية:</h4>
        <p style="margin:6px 0; font-size: 14px;"><strong>التاريخ:</strong> <span id="modalDate" style="color: #475569;"></span></p>
        <p style="margin:6px 0; font-size: 14px;"><strong>الرمز البريدي:</strong> <span id="modalZip" style="color: #475569;"></span></p>
      </div>
    </div>

    <h4 style="color: #0f172a; margin-bottom:15px; display: flex; align-items: center; gap: 8px;"><i class="fa-solid fa-box-open" style="color: #38bdf8;"></i>المنتجات المطلوبة:</h4>
    <div id="modalProductsList" style="display: flex; flex-direction: column; gap: 12px; max-height: 400px; overflow-y: auto; padding-left: 5px;"></div>
  </div>
</div>

<script>
document.querySelectorAll('.details-btn').forEach(btn => {
  btn.addEventListener('click', function() {
    const orderId = this.getAttribute('data-id');
    const productsJson = this.getAttribute('data-products');

    document.getElementById('modalOrderId').innerText = orderId;
    document.getElementById('modalDate').innerText = this.getAttribute('data-date');
    document.getElementById('modalAddr1').innerText = this.getAttribute('data-address1');
    document.getElementById('modalAddr2').innerText = this.getAttribute('data-address2');
    document.getElementById('modalCityGov').innerText = this.getAttribute('data-city') + ' - ' + this.getAttribute('data-gov');
    document.getElementById('modalZip').innerText = this.getAttribute('data-zip');

    const productsList = document.getElementById('modalProductsList');
    productsList.innerHTML = '';

    try {
      const products = JSON.parse(productsJson);
      if(products.length === 0) {
        productsList.innerHTML = '<div style="text-align:center; padding:20px; color:#94a3b8;">لا توجد تفاصيل للمنتجات.</div>';
      } else {
        products.forEach(product => {
          const rawSrc = product.src || 'images/logos/logo.png';
          const imgUrl = (rawSrc.startsWith('http') || rawSrc.startsWith('/')) ? rawSrc : '/' + rawSrc;
          const title = product.title || 'منتج غير معروف';
          const price = product.price || '0';
          const qty = product.number || product.quantity || product.qty || product.quantty || 1;
          const numericPrice = parseFloat(price.toString().replace(/[^\d.]/g, '')) || 0;
          const subtotal = (numericPrice * qty).toFixed(2);

          productsList.innerHTML += `
            <div style="display: flex; align-items: center; gap: 15px; padding: 15px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; transition: 0.2s;">
                <img src="${imgUrl}" style="width: 70px; height: 70px; object-fit: cover; border-radius: 12px; border: 1px solid #e2e8f0; background: #fff;">
                <div style="flex: 1;">
                    <h4 style="margin: 0 0 8px 0; color: #0f172a; font-size: 15px; font-weight: 700;">${title}</h4>
                    <div style="display: flex; gap: 20px; align-items: center;">
                        <span style="font-size: 13px; color: #64748b;"><i class="fa-solid fa-layer-group" style="margin-left: 5px; color: #38bdf8;"></i>الكمية: <strong style="color: #0f172a;">${qty}</strong></span>
                        <span style="font-size: 13px; color: #64748b;"><i class="fa-solid fa-tag" style="margin-left: 5px; color: #38bdf8;"></i>السعر: <strong style="color: #0f172a;">${numericPrice.toFixed(2)} ج.م</strong></span>
                    </div>
                </div>
                <div style="text-align: left; min-width: 110px; border-right: 1px solid #e2e8f0; padding-right: 15px;">
                    <div style="font-size: 11px; color: #94a3b8; margin-bottom: 4px; font-weight: 600; text-transform: uppercase;">الإجمالي</div>
                    <div style="font-weight: 800; color: #0ea5e9; font-size: 16px;">${subtotal} ج.م</div>
                </div>
            </div>
          `;
        });
      }
    } catch (e) {
      productsList.innerHTML = '<div style="text-align:center; padding:20px; color:#ef4444;">عذراً، حدث خطأ أثناء معالجة بيانات المنتجات.</div>';
    }

    document.getElementById('adminOrderModal').style.display = 'flex';
  });
});

document.getElementById('closeAdminModalBtn').addEventListener('click', () => {
  document.getElementById('adminOrderModal').style.display = 'none';
});
window.onclick = function(event) {
  const modal = document.getElementById('adminOrderModal');
  if (event.target == modal) modal.style.display = 'none';
}
</script>