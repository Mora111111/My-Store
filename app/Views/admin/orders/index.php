<div class="card">
    <h2 style="margin-top:0;"><i class="fa-solid fa-list"></i> <?= lang('orders_log_heading') ?></h2>
    <table>
      <tr>
        <th><?= lang('col_order_id') ?></th>
        <th><?= lang('col_customer') ?></th>
        <th><?= lang('col_phone') ?></th>
        <th><?= lang('col_total') ?></th>
        <th><?= lang('col_payment') ?></th>
        <th><?= lang('col_current_status') ?></th>
        <th><?= lang('col_change_status') ?></th>
        <th><?= lang('col_actions') ?></th>
      </tr>
      <?php
      $status_map = [
        'قيد المراجعة' => 'status-pending',
        'تم الشحن' => 'status-shipped',
        'مكتمل' => 'status-completed',
        'ملغي' => 'status-cancelled'
      ];
      $status_lang_map = [
        'قيد المراجعة' => lang('status_pending'),
        'تم الشحن' => lang('status_shipped'),
        'مكتمل' => lang('status_completed'),
        'ملغي' => lang('status_cancelled')
      ];
      ?>
      <?php if (!empty($orders)): ?>
        <?php foreach ($orders as $row):$status = $row['status'];$status_class = $status_map[$status] ?? 'status-pending';
          $display_status =$status_lang_map[$status] ?? $status;
          $products_json = htmlspecialchars($row['products'], ENT_QUOTES, 'UTF-8');
          
          $pay_method = in_array($row['payment_method'] ?? 'cod', ['online', 'online_card', 'online_wallet']) ? '<span class="badge" style="background:#eff6ff; color:#3b82f6;"><i class="fa-regular fa-credit-card"></i> ' . lang('pay_online_badge') . '</span>' : '<span class="badge" style="background:#f0fdf4; color:#166534;"><i class="fa-solid fa-money-bill"></i> ' . lang('pay_cod_badge') . '</span>';
          $pay_status = ($row['payment_status'] ?? 'pending') === 'paid' ? '<span style="color:#10b981; font-size:12px; font-weight:bold;"><i class="fa-solid fa-check"></i> ' . lang('pay_status_paid') . '</span>' : '<span style="color:#f59e0b; font-size:12px; font-weight:bold;"><i class="fa-solid fa-clock"></i> ' . lang('pay_status_pending') . '</span>';
        ?>
        <tr>
          <td><span style="font-weight:700;">#<?= $row['id']; ?></span></td>
          <td style="font-weight:500;"><?= htmlspecialchars($row['full_name']); ?></td>
          <td><a href="tel:<?= htmlspecialchars($row['phone']); ?>" style="color:#3b82f6; text-decoration:none;"><?= htmlspecialchars($row['phone']); ?></a></td>
          <td style="font-weight:700; color:#0f172a;"><?= htmlspecialchars($row['total_price']); ?> <?= lang('currency_egp') ?></td>
          <td>
            <div style="display:flex; flex-direction:column; gap:5px;">
                <?= $pay_method; ?>
                <?= $pay_status; ?>
            </div>
          </td>
          <td><span class="status-badge <?= $status_class; ?>"><?= $display_status; ?></span></td>
          <td>
            <?php if ($status === 'ملغي'): ?>
                <span style="color:#ef4444; font-weight:bold; font-size:14px;"><i class="fa-solid fa-ban"></i> <?= lang('permanently_cancelled') ?></span>
            <?php else: ?>
                <button type="button" class="btn-update" style="background:#8b5cf6;" onclick="openStatusModal(<?= $row['id']; ?>, '<?=$status; ?>')">
                    <i class="fa-solid fa-pen"></i> <?= lang('btn_change_status') ?>
                </button>
            <?php endif; ?>
          </td>
          <td>
            <div class="actions-flex">
                <button class="btn-view details-btn"
                  data-id="<?= $row['id']; ?>"
                  data-customer="<?= htmlspecialchars($row['full_name'], ENT_QUOTES, 'UTF-8'); ?>"
                  data-total="<?= htmlspecialchars($row['total_price'], ENT_QUOTES, 'UTF-8'); ?>"
                  data-products="<?= $products_json; ?>"
                  data-address1="<?= htmlspecialchars($row['address_line1'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                  data-address2="<?= htmlspecialchars($row['address_line2'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                  data-city="<?= htmlspecialchars($row['city'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                  data-gov="<?= htmlspecialchars($row['governorate'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                  data-zip="<?= htmlspecialchars($row['zip_code'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                  data-phone="<?= htmlspecialchars($row['phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                  data-date="<?= isset($row['created_at']) ? date('Y-m-d h:i A', strtotime($row['created_at'])) : ''; ?>"
                  data-pay-method="<?= htmlspecialchars($row['payment_method'] ?? 'cod', ENT_QUOTES, 'UTF-8'); ?>"
                  data-pay-status="<?= htmlspecialchars($row['payment_status'] ?? 'pending', ENT_QUOTES, 'UTF-8'); ?>"
                  data-trx-id="<?= htmlspecialchars($row['transaction_id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                  >
                  <i class="fa-solid fa-eye"></i> <?= lang('btn_view') ?>
                </button>
                <?php if ($status === 'ملغي'): ?>
                  <form method="POST" action="/admin/orders/delete" style="display:inline-block; margin:0;" onsubmit="return confirm('<?= lang('confirm_delete_order') ?>');">
                    <?= CSRF::getField() ?>
                    <input type="hidden" name="id" value="<?= $row['id']; ?>">
                    <button type="submit" class="btn-delete" style="border:none; cursor:pointer; font-family:inherit;"><i class="fa-solid fa-trash"></i></button>
                  </form>
                <?php endif; ?>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="8" style="text-align:center; padding:40px; color:#94a3b8; font-size:16px;"><i class="fa-solid fa-cart-shopping" style="font-size:40px; margin-bottom:15px; opacity:0.5;"></i><br><?= lang('no_orders_yet') ?></td></tr>
      <?php endif; ?>
    </table>
</div>

<div id="adminOrderModal" class="modal-overlay">
  <div class="modal-content-modern" style="width: 850px; max-width: 95%; padding: 0;">
    
    <div style="background: linear-gradient(135deg, #0f172a, #1e293b); padding: 20px 30px; display: flex; justify-content: space-between; align-items: center; border-radius: 16px 16px 0 0;">
      <h3 style="margin: 0; color: #fff; font-size: 20px; display: flex; align-items: center; gap: 10px;">
        <i class="fa-solid fa-file-invoice-dollar" style="color: #38bdf8; font-size: 24px;"></i> 
        <?= lang('modal_order_details_title') ?> <span id="modalOrderId" style="color: #38bdf8; font-weight: 800; margin-right: 5px;"></span>
      </h3>
      <i class="fa-solid fa-xmark" id="closeAdminModalBtn" style="color: #cbd5e1; font-size: 24px; cursor: pointer; transition: 0.3s;" onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='#cbd5e1'"></i>
    </div>

    <div style="padding: 30px; max-height: 80vh; overflow-y: auto; background: #f8fafc;">
      
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 25px;">
        
        <div style="background: #fff; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
          <h4 style="margin: 0 0 15px 0; color: #475569; font-size: 15px; display: flex; align-items: center; gap: 8px; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;">
            <div style="background: #e0f2fe; color: #0284c7; width: 30px; height: 30px; border-radius: 8px; display: flex; justify-content: center; align-items: center;"><i class="fa-solid fa-user"></i></div> <?= lang('customer_details') ?>
          </h4>
          <p style="margin: 8px 0; font-size: 14px; color: #1e293b;"><strong><?= lang('lbl_name') ?></strong> <span id="modalCustomer"></span></p>
          <p style="margin: 8px 0; font-size: 14px; color: #1e293b;"><strong><?= lang('lbl_phone') ?></strong> <span id="modalPhone" style="color: #0284c7; font-weight: bold; direction: ltr; display: inline-block;"></span></p>
          <p style="margin: 8px 0; font-size: 14px; color: #1e293b;"><strong><?= lang('lbl_date') ?></strong> <span id="modalDate"></span></p>
        </div>

        <div style="background: #fff; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
          <h4 style="margin: 0 0 15px 0; color: #475569; font-size: 15px; display: flex; align-items: center; gap: 8px; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;">
            <div style="background: #fce7f3; color: #db2777; width: 30px; height: 30px; border-radius: 8px; display: flex; justify-content: center; align-items: center;"><i class="fa-solid fa-truck-fast"></i></div> <?= lang('shipping_details') ?>
          </h4>
          <p style="margin: 8px 0; font-size: 14px; color: #1e293b;"><strong><?= lang('lbl_region') ?></strong> <span id="modalCityGov"></span></p>
          <p style="margin: 8px 0; font-size: 14px; color: #1e293b;"><strong><?= lang('lbl_address1') ?></strong> <span id="modalAddr1"></span></p>
          <p style="margin: 8px 0; font-size: 14px; color: #1e293b;"><strong><?= lang('lbl_address2') ?></strong> <span id="modalAddr2"></span></p>
          <p style="margin: 8px 0; font-size: 14px; color: #1e293b;"><strong><?= lang('lbl_zip') ?></strong> <span id="modalZip"></span></p>
        </div>
        
        <div style="background: #fff; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
          <h4 style="margin: 0 0 15px 0; color: #475569; font-size: 15px; display: flex; align-items: center; gap: 8px; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;">
            <div style="background: #fef3c7; color: #d97706; width: 30px; height: 30px; border-radius: 8px; display: flex; justify-content: center; align-items: center;"><i class="fa-solid fa-wallet"></i></div> <?= lang('payment_details') ?>
          </h4>
          <p style="margin: 8px 0; font-size: 14px; color: #1e293b;"><strong><?= lang('lbl_method') ?></strong> <span id="modalPayMethod" style="font-weight:bold;"></span></p>
          <p style="margin: 8px 0; font-size: 14px; color: #1e293b;"><strong><?= lang('lbl_status') ?></strong> <span id="modalPayStatus"></span></p>
          <p style="margin: 8px 0; font-size: 14px; color: #1e293b;"><strong><?= lang('lbl_trx_id') ?></strong> <span id="modalTrxId" style="font-family: monospace; color: #64748b;"></span></p>
        </div>

      </div>

      <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
        <h4 style="margin: 0; padding: 15px 20px; background: #f1f5f9; color: #1e293b; font-size: 16px; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; gap: 10px;">
          <i class="fa-solid fa-boxes-stacked" style="color: #64748b;"></i> <?= lang('ordered_products') ?>
        </h4>
        <div id="modalProductsList" style="padding: 10px 20px; display: flex; flex-direction: column; gap: 10px;"></div>
        
        <div style="background: #f8fafc; padding: 20px; border-top: 2px dashed #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
          <span style="font-size: 18px; font-weight: 700; color: #475569;"><?= lang('grand_total') ?></span>
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
    document.getElementById('modalAddr2').innerText = this.getAttribute('data-address2') || '<?= lang('none_provided') ?>';
    document.getElementById('modalCityGov').innerText = this.getAttribute('data-gov') + ' - ' + this.getAttribute('data-city');
    document.getElementById('modalPhone').innerText = this.getAttribute('data-phone');
    document.getElementById('modalZip').innerText = this.getAttribute('data-zip') || '<?= lang('none_provided') ?>';
    document.getElementById('modalGrandTotal').innerText = this.getAttribute('data-total') + " <?= lang('currency_egp') ?>";

    const methodVal = this.getAttribute('data-pay-method');
    const payMethod = (methodVal === 'online_card' || methodVal === 'online_wallet' || methodVal === 'online') ? '<span style="color:#3b82f6;"><?= lang('pay_method_online_long') ?></span>' : '<span style="color:#166534;"><?= lang('pay_method_cod_long') ?></span>';
    const payStatus = this.getAttribute('data-pay-status') === 'paid' ? '<span style="color:#10b981;"><?= lang('pay_status_paid') ?> <i class="fa-solid fa-check"></i></span>' : '<span style="color:#f59e0b;"><?= lang('pay_status_pending') ?> <i class="fa-solid fa-clock"></i></span>';
    const trxId = this.getAttribute('data-trx-id') || '---';

    document.getElementById('modalPayMethod').innerHTML = payMethod;
    document.getElementById('modalPayStatus').innerHTML = payStatus;
    document.getElementById('modalTrxId').innerText = trxId;

    const productsList = document.getElementById('modalProductsList');
    productsList.innerHTML = '';

    try {
      const products = JSON.parse(productsJson);
      if(products.length === 0) {
        productsList.innerHTML = '<div style="text-align:center; padding:20px; color:#94a3b8;"><?= lang('no_product_details') ?></div>';
      } else {
        products.forEach(product => {
          const rawSrc = product.src || 'images/logos/logo.png';
          const imgUrl = rawSrc.startsWith('http') ? rawSrc : '<?= BASE_URL ?>' + rawSrc.replace(/^\/+/, '');
          const title = product.title || '<?= lang('unknown_product') ?>';
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
                            <span><?= lang('lbl_qty') ?> <strong style="color: #1e293b;">${qty}</strong></span>
                            <span><?= lang('lbl_unit_price') ?> <strong style="color: #1e293b;">${numericPrice.toFixed(2)} <?= lang('currency_egp') ?></strong></span>
                        </div>
                    </div>
                </div>
                <div style="text-align: left; min-width: 100px;">
                    <div style="font-weight: 800; color: #f97316; font-size: 16px;">${subtotal} <?= lang('currency_egp') ?></div>
                </div>
            </div>
          `;
        });
        
        if(productsList.lastElementChild) {
            productsList.lastElementChild.style.borderBottom = 'none';
        }
      }
    } catch (e) {
      productsList.innerHTML = '<div style="text-align:center; padding:20px; color:#ef4444;"><?= lang('error_processing_products') ?></div>';
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
  const statusModal = document.getElementById('statusModal');
  if (event.target == statusModal) statusModal.style.display = 'none';
}
</script>

<div id="statusModal" class="modal-overlay">
  <div class="modal-content-modern" style="width: 450px;">
    <div class="modal-header-modern">
      <h3 class="modal-title-modern"><i class="fa-solid fa-pen-to-square" style="color:#38bdf8;"></i> <?= lang('update_order_status_title') ?><span id="statusModalOrderIdTxt"></span></h3>
      <i class="fa-solid fa-xmark close-btn-modern" onclick="closeStatusModal()"></i>
    </div>
    <div class="modal-body-modern">
      <form method="POST" action="/admin/orders/update">
        <?= CSRF::getField() ?>
        <input type="hidden" name="order_id" id="statusModalOrderId">
        
        <div class="form-group" style="margin-bottom:15px;">
            <label style="display:block; font-weight:bold; margin-bottom:6px;"><?= lang('lbl_new_status') ?></label>
            <select name="new_status" id="statusModalSelect" class="form_input" required style="width:100%; padding:10px; border-radius:8px; border:1px solid #cbd5e1;">
                <option value="قيد المراجعة"><?= lang('status_pending') ?></option>
                <option value="تم الشحن"><?= lang('status_shipped') ?></option>
                <option value="مكتمل"><?= lang('status_completed') ?></option>
                <option value="ملغي"><?= lang('status_cancelled') ?></option>
            </select>
        </div>
        
        <div class="form-group" style="margin-bottom:15px;">
            <label style="display:block; font-weight:bold; margin-bottom:6px;"><?= lang('lbl_customer_msg') ?></label>
            <textarea name="admin_message" class="textarea-modern" style="min-height: 100px;" placeholder="<?= lang('ph_customer_msg') ?>"></textarea>
        </div>

        <div class="modal-footer-modern">
          <button type="button" class="btn-cancel-modern" onclick="closeStatusModal()"><?= lang('cancel_btn') ?></button>
          <button type="submit" class="btn-save-modern"><i class="fa-solid fa-check"></i> <?= lang('btn_save_update') ?></button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function openStatusModal(orderId, currentStatus) {
    document.getElementById('statusModalOrderIdTxt').innerText = orderId;
    document.getElementById('statusModalOrderId').value = orderId;
    document.getElementById('statusModalSelect').value = currentStatus;
    document.getElementById('statusModal').style.display = 'flex';
}
function closeStatusModal() {
    document.getElementById('statusModal').style.display = 'none';
}
</script>