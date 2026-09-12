<div class="card">
    <h2 style="margin-top:0;"><i class="fa-solid fa-list"></i> سجل جميع الطلبات</h2>
    <table>
      <tr>
        <th>رقم الطلب</th>
        <th>العميل</th>
        <th>الهاتف</th>
        <th>الإجمالي</th>
        <th>الدفع</th>
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
          
          $pay_method = in_array($row['payment_method'] ?? 'cod', ['online', 'online_card', 'online_wallet']) ? '<span class="badge" style="background:#eff6ff; color:#3b82f6;"><i class="fa-regular fa-credit-card"></i> إلكتروني</span>' : '<span class="badge" style="background:#f0fdf4; color:#166534;"><i class="fa-solid fa-money-bill"></i> كاش (COD)</span>';
          $pay_status = ($row['payment_status'] ?? 'pending') === 'paid' ? '<span style="color:#10b981; font-size:12px; font-weight:bold;"><i class="fa-solid fa-check"></i> مدفوع</span>' : '<span style="color:#f59e0b; font-size:12px; font-weight:bold;"><i class="fa-solid fa-clock"></i> معلق</span>';
        ?>
        <tr>
          <td><span style="font-weight:700;">#<?php echo $row['id']; ?></span></td>
          <td style="font-weight:500;"><?php echo htmlspecialchars($row['full_name']); ?></td>
          <td><a href="tel:<?php echo htmlspecialchars($row['phone']); ?>" style="color:#3b82f6; text-decoration:none;"><?php echo htmlspecialchars($row['phone']); ?></a></td>
          <td style="font-weight:700; color:#0f172a;"><?php echo htmlspecialchars($row['total_price']); ?> ج.م</td>
          <td>
            <div style="display:flex; flex-direction:column; gap:5px;">
                <?php echo $pay_method; ?>
                <?php echo $pay_status; ?>
            </div>
          </td>
          <td><span class="status-badge <?php echo $status_class; ?>"><?php echo $status; ?></span></td>
          <td>
            <form method="POST" action="/admin/orders/update" class="status-form" style="margin:0;">
              <?= CSRF::getField() ?>
              <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
              <select name="new_status" class="status-select">
                <option value="قيد المراجعة" <?php echo $status == 'قيد المراجعة' ? 'selected' : ''; ?>>قيد المراجعة</option>
                <option value="تم الشحن" <?php echo $status == 'تم الشحن' ? 'selected' : ''; ?>>تم الشحن</option>
                <option value="مكتمل" <?php echo $status == 'مكتمل' ? 'selected' : ''; ?>>مكتمل</option>
                <option value="ملغي" <?php echo $status == 'ملغي' ? 'selected' : ''; ?>>ملغي</option>
              </select>
              <button type="submit" class="btn-update"><i class="fa-solid fa-check"></i></button>
            </form>
          </td>
          <td>
            <div class="actions-flex">
                <button class="btn-view details-btn"
                  data-id="<?php echo $row['id']; ?>"
                  data-customer="<?php echo htmlspecialchars($row['full_name'], ENT_QUOTES, 'UTF-8'); ?>"
                  data-total="<?php echo htmlspecialchars($row['total_price'], ENT_QUOTES, 'UTF-8'); ?>"
                  data-products="<?php echo $products_json; ?>"
                  data-address1="<?php echo htmlspecialchars($row['address_line1'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                  data-address2="<?php echo htmlspecialchars($row['address_line2'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                  data-city="<?php echo htmlspecialchars($row['city'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                  data-gov="<?php echo htmlspecialchars($row['governorate'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                  data-zip="<?php echo htmlspecialchars($row['zip_code'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                  data-phone="<?php echo htmlspecialchars($row['phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                  data-date="<?php echo isset($row['created_at']) ? date('Y-m-d h:i A', strtotime($row['created_at'])) : ''; ?>"
                  data-pay-method="<?php echo htmlspecialchars($row['payment_method'] ?? 'cod', ENT_QUOTES, 'UTF-8'); ?>"
                  data-pay-status="<?php echo htmlspecialchars($row['payment_status'] ?? 'pending', ENT_QUOTES, 'UTF-8'); ?>"
                  data-trx-id="<?php echo htmlspecialchars($row['transaction_id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                  >
                  <i class="fa-solid fa-eye"></i> عرض
                </button>
                <?php if ($status === 'ملغي'): ?>
                  <form method="POST" action="/admin/orders/delete" style="display:inline-block; margin:0;" onsubmit="return confirm('هل أنت متأكد من حذف هذا الطلب نهائياً من قاعدة البيانات؟');">
                    <?= CSRF::getField() ?>
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                    <button type="submit" class="btn-delete" style="border:none; cursor:pointer; font-family:inherit;"><i class="fa-solid fa-trash"></i></button>
                  </form>
                <?php endif; ?>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="8" style="text-align:center; padding:40px; color:#94a3b8; font-size:16px;"><i class="fa-solid fa-cart-shopping" style="font-size:40px; margin-bottom:15px; opacity:0.5;"></i><br>لا توجد طلبات حتى الآن.</td></tr>
      <?php endif; ?>
    </table>
</div>

<div id="adminOrderModal" class="modal-overlay">
  <div class="modal-content-modern" style="width: 850px; max-width: 95%; padding: 0;">
    
    <div style="background: linear-gradient(135deg, #0f172a, #1e293b); padding: 20px 30px; display: flex; justify-content: space-between; align-items: center; border-radius: 16px 16px 0 0;">
      <h3 style="margin: 0; color: #fff; font-size: 20px; display: flex; align-items: center; gap: 10px;">
        <i class="fa-solid fa-file-invoice-dollar" style="color: #38bdf8; font-size: 24px;"></i> 
        تفاصيل الطلب رقم <span id="modalOrderId" style="color: #38bdf8; font-weight: 800; margin-right: 5px;"></span>
      </h3>
      <i class="fa-solid fa-xmark" id="closeAdminModalBtn" style="color: #cbd5e1; font-size: 24px; cursor: pointer; transition: 0.3s;" onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='#cbd5e1'"></i>
    </div>

    <div style="padding: 30px; max-height: 80vh; overflow-y: auto; background: #f8fafc;">
      
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 25px;">
        
        <div style="background: #fff; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
          <h4 style="margin: 0 0 15px 0; color: #475569; font-size: 15px; display: flex; align-items: center; gap: 8px; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;">
            <div style="background: #e0f2fe; color: #0284c7; width: 30px; height: 30px; border-radius: 8px; display: flex; justify-content: center; align-items: center;"><i class="fa-solid fa-user"></i></div> بيانات العميل
          </h4>
          <p style="margin: 8px 0; font-size: 14px; color: #1e293b;"><strong>الاسم:</strong> <span id="modalCustomer"></span></p>
          <p style="margin: 8px 0; font-size: 14px; color: #1e293b;"><strong>الهاتف:</strong> <span id="modalPhone" style="color: #0284c7; font-weight: bold; direction: ltr; display: inline-block;"></span></p>
          <p style="margin: 8px 0; font-size: 14px; color: #1e293b;"><strong>التاريخ:</strong> <span id="modalDate"></span></p>
        </div>

        <div style="background: #fff; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
          <h4 style="margin: 0 0 15px 0; color: #475569; font-size: 15px; display: flex; align-items: center; gap: 8px; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;">
            <div style="background: #fce7f3; color: #db2777; width: 30px; height: 30px; border-radius: 8px; display: flex; justify-content: center; align-items: center;"><i class="fa-solid fa-truck-fast"></i></div> بيانات الشحن
          </h4>
          <p style="margin: 8px 0; font-size: 14px; color: #1e293b;"><strong>المنطقة:</strong> <span id="modalCityGov"></span></p>
          <p style="margin: 8px 0; font-size: 14px; color: #1e293b;"><strong>العنوان 1:</strong> <span id="modalAddr1"></span></p>
          <p style="margin: 8px 0; font-size: 14px; color: #1e293b;"><strong>العنوان 2:</strong> <span id="modalAddr2"></span></p>
          <p style="margin: 8px 0; font-size: 14px; color: #1e293b;"><strong>الرمز البريدي:</strong> <span id="modalZip"></span></p>
        </div>
        
        <div style="background: #fff; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
          <h4 style="margin: 0 0 15px 0; color: #475569; font-size: 15px; display: flex; align-items: center; gap: 8px; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;">
            <div style="background: #fef3c7; color: #d97706; width: 30px; height: 30px; border-radius: 8px; display: flex; justify-content: center; align-items: center;"><i class="fa-solid fa-wallet"></i></div> بيانات الدفع
          </h4>
          <p style="margin: 8px 0; font-size: 14px; color: #1e293b;"><strong>الطريقة:</strong> <span id="modalPayMethod" style="font-weight:bold;"></span></p>
          <p style="margin: 8px 0; font-size: 14px; color: #1e293b;"><strong>الحالة:</strong> <span id="modalPayStatus"></span></p>
          <p style="margin: 8px 0; font-size: 14px; color: #1e293b;"><strong>رقم العملية:</strong> <span id="modalTrxId" style="font-family: monospace; color: #64748b;"></span></p>
        </div>

      </div>

      <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
        <h4 style="margin: 0; padding: 15px 20px; background: #f1f5f9; color: #1e293b; font-size: 16px; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; gap: 10px;">
          <i class="fa-solid fa-boxes-stacked" style="color: #64748b;"></i> المنتجات المطلوبة
        </h4>
        <div id="modalProductsList" style="padding: 10px 20px; display: flex; flex-direction: column; gap: 10px;"></div>
        
        <div style="background: #f8fafc; padding: 20px; border-top: 2px dashed #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
          <span style="font-size: 18px; font-weight: 700; color: #475569;">إجمالي الطلب (شامل الشحن إن وجد):</span>
          <span id="modalGrandTotal" style="font-size: 24px; font-weight: 900; color: #059669;"></span>
        </div>
      </div>

    </div>
  </div>
</div>

<script>
document.querySelectorAll('.details-btn').forEach(btn => {
  btn.addEventListener('click', function() {
    const orderId = this.getAttribute('data-id');
    const productsJson = this.getAttribute('data-products');

    document.getElementById('modalOrderId').innerText = "#" + orderId;
    document.getElementById('modalCustomer').innerText = this.getAttribute('data-customer');
    document.getElementById('modalDate').innerText = this.getAttribute('data-date');
    document.getElementById('modalAddr1').innerText = this.getAttribute('data-address1');
    document.getElementById('modalAddr2').innerText = this.getAttribute('data-address2') || 'لا يوجد';
    document.getElementById('modalCityGov').innerText = this.getAttribute('data-gov') + ' - ' + this.getAttribute('data-city');
    document.getElementById('modalPhone').innerText = this.getAttribute('data-phone');
    document.getElementById('modalZip').innerText = this.getAttribute('data-zip') || 'لا يوجد';
    document.getElementById('modalGrandTotal').innerText = this.getAttribute('data-total') + " ج.م";

    const methodVal = this.getAttribute('data-pay-method');
const payMethod = (methodVal === 'online_card' || methodVal === 'online_wallet' || methodVal === 'online') ? '<span style="color:#3b82f6;">إلكتروني (فيزا/محفظة)</span>' : '<span style="color:#166534;">نقدي عند الاستلام (COD)</span>';
    const payStatus = this.getAttribute('data-pay-status') === 'paid' ? '<span style="color:#10b981;">مدفوع <i class="fa-solid fa-check"></i></span>' : '<span style="color:#f59e0b;">معلق <i class="fa-solid fa-clock"></i></span>';
    const trxId = this.getAttribute('data-trx-id') || '---';

    document.getElementById('modalPayMethod').innerHTML = payMethod;
    document.getElementById('modalPayStatus').innerHTML = payStatus;
    document.getElementById('modalTrxId').innerText = trxId;

    const productsList = document.getElementById('modalProductsList');
    productsList.innerHTML = '';

    try {
      const products = JSON.parse(productsJson);
      if(products.length === 0) {
        productsList.innerHTML = '<div style="text-align:center; padding:20px; color:#94a3b8;">لا توجد تفاصيل للمنتجات.</div>';
      } else {
        products.forEach(product => {
          const rawSrc = product.src || 'images/logos/logo.png';
          const imgUrl = rawSrc.startsWith('http') ? rawSrc : '<?= BASE_URL ?>' + rawSrc.replace(/^\/+/, '');
          const title = product.title || 'منتج غير معروف';
          const price = product.price || '0';
          const qty = product.number || product.quantity || product.qty || product.quantty || 1;
          const numericPrice = parseFloat(price.toString().replace(/[^\d.]/g, '')) || 0;
          const subtotal = (numericPrice * qty).toFixed(2);

          productsList.innerHTML += `
            <div style="display: flex; align-items: center; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #f1f5f9;">
                <div style="display: flex; align-items: center; gap: 15px; flex: 1;">
                    <img src="${imgUrl}" style="width: 60px; height: 60px; object-fit: contain; border-radius: 8px; border: 1px solid #e2e8f0; padding: 4px; background: #fff;">
                    <div>
                        <h5 style="margin: 0 0 5px 0; color: #0f172a; font-size: 15px; font-weight: 700;">${title}</h5>
                        <div style="display: flex; gap: 15px; font-size: 13px; color: #64748b;">
                            <span>الكمية: <strong style="color: #1e293b;">${qty}</strong></span>
                            <span>سعر الوحدة: <strong style="color: #1e293b;">${numericPrice.toFixed(2)} ج.م</strong></span>
                        </div>
                    </div>
                </div>
                <div style="text-align: left; min-width: 100px;">
                    <div style="font-weight: 800; color: #f97316; font-size: 16px;">${subtotal} ج.م</div>
                </div>
            </div>
          `;
        });
        
        if(productsList.lastElementChild) {
            productsList.lastElementChild.style.borderBottom = 'none';
        }
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