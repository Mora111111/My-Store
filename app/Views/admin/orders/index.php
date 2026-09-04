
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
    <button type="submit" class="btn-delete-order" style="border:none; cursor:pointer; font-family:inherit;"><i class="fa-solid fa-trash"></i> حذف</button>
  </form>
<?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="7" style="text-align:center; padding:40px; color:#94a3b8; font-size:16px;"><i class="fa-solid fa-cart-shopping" style="font-size:40px; margin-bottom:15px; opacity:0.5;"></i><br>لا توجد طلبات حتى الآن.</td></tr>
      <?php endif; ?>
    </table>
  </div>
<div id="adminOrderModal" class="modal">
  <div class="modal-content">
    <span class="close-modal" id="closeAdminModalBtn">&times;</span>
    <h3 style="margin-top:0; padding-bottom:15px; color: #0f172a; font-size:22px; border-bottom:2px solid #38bdf8;">
      <i class="fa-solid fa-receipt" style="margin-left:10px; color:#38bdf8;"></i> تفاصيل الطلب رقم #<span id="modalOrderId"></span>
    </h3>
    <div class="address-box">
      <h4 style="margin-top:0; margin-bottom:15px; color:#0f172a;"><i class="fa-solid fa-truck" style="margin-left:8px;"></i>بيانات الشحن:</h4>
      <p style="margin:8px 0;"><strong>التاريخ:</strong> <span id="modalDate"></span></p>
      <p style="margin:8px 0;"><strong>العنوان 1:</strong> <span id="modalAddr1"></span></p>
      <p style="margin:8px 0;"><strong>العنوان 2:</strong> <span id="modalAddr2"></span></p>
      <p style="margin:8px 0;"><strong>المدينة والمحافظة:</strong> <span id="modalCityGov"></span></p>
      <p style="margin:8px 0;"><strong>الرمز البريدي:</strong> <span id="modalZip"></span></p>
    </div>
    <h4 style="color: #0f172a; margin-bottom:15px;"><i class="fa-solid fa-box" style="margin-left:8px;"></i>المنتجات المطلوبة:</h4>
    <div id="modalProductsList"></div>
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
        productsList.innerHTML = '<p style="color:#64748b;">لا توجد تفاصيل للمنتجات.</p>';
      } else {
        products.forEach(product => {
          const imgUrl = product.src || '/images/logos/logo.png';
          const title = product.title || 'منتج غير معروف';
          const price = product.price || '0';
          const qty = product.number || product.quantity || product.qty || product.quantty || 1;

          productsList.innerHTML += `
            <div class="product-item">
              <img src="${imgUrl}" alt="">
              <div style="flex:1;">
                <h4 style="margin: 0 0 5px 0; color:#0f172a;">${title}</h4>
                <p style="margin: 0; color: #64748b; font-size: 14px;">الكمية: <strong>${qty}</strong> | السعر: <strong>${price} ج.م</strong></p>
              </div>
            </div>
          `;
        });
      }
    } catch (e) {
      productsList.innerHTML = '<p style="color:#ef4444;">عذراً، لا يمكن عرض المنتجات حالياً.</p>';
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
