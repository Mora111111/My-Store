<style>
    .my-orders-container { padding: 120px 20px 80px; min-height: 60vh; }
    .table-responsive { width: 100%; overflow-x: auto; background: #fff; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
    .orders-table { width: 100%; border-collapse: collapse; min-width: 600px; }
    .orders-table th, .orders-table td { padding: 15px; text-align: right; border-bottom: 1px solid #eee; }
    .orders-table th { background: var(--main-color); color: white; }
    .orders-table tr:hover { background: #f9f9f9; }

    .status-badge { padding: 6px 12px; border-radius: 20px; font-size: 13px; font-weight: bold; color: white; display: inline-block; }
    .status-pending { background: #ffc107; color: #000; }
    .status-shipped { background: #17a2b8; color: #fff; }
    .status-completed { background: #28a745; color: #fff; }
    .status-cancelled { background: #dc3545; color: #fff; }

    .btn-view { background: var(--main-color); color: white; padding: 6px 12px; border-radius: 4px; border: none; cursor: pointer; transition: 0.3s; display: inline-block;}
    .btn-view:hover { background: var(--color-hover); }

    .btn-delete-order { background: #dc3545; color: white; padding: 6px 12px; border-radius: 4px; border: none; cursor: pointer; text-decoration: none; display: inline-block; margin-right: 5px; transition: 0.3s; font-size: 14px;}
    .btn-delete-order:hover { background: #c82333; }

    .btn-hide-order { background: #6c757d; color: white; padding: 6px 12px; border-radius: 4px; border: none; cursor: pointer; text-decoration: none; display: inline-block; margin-right: 5px; transition: 0.3s; font-size: 14px;}
    .btn-hide-order:hover { background: #5a6268; }

    .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.6); }
    .modal-content { background-color: #fff; margin: 10% auto; padding: 20px; border-radius: 8px; width: 90%; max-width: 600px; position: relative; }
    .close-modal { position: absolute; top: 15px; right: 20px; font-size: 28px; font-weight: bold; cursor: pointer; color: #777; }
    .close-modal:hover { color: #d9534f; }
    .product-item { display: flex; align-items: center; border-bottom: 1px solid #eee; padding: 10px 0; }
    .product-item img { width: 60px; height: 60px; object-fit: contain; margin-left: 15px; border: 1px solid #ddd; border-radius: 5px; }
</style>

<main class="main">
    <section class="my-orders-container container">
      <h2 class="main_title">سجل طلباتي</h2>

      <?php if(isset($_GET['cancel_error'])): ?>
          <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px; text-align: center;">
              <?php echo htmlspecialchars($_GET['cancel_error']); ?>
          </div>
      <?php endif; ?>

      <div class="table-responsive">
        <table class="orders-table">
            <thead>
                <tr>
                    <th>رقم الطلب</th>
                    <th>تاريخ الطلب</th>
                    <th>الإجمالي</th>
                    <th>حالة الطلب</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
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
                        $status = $row['status'] ?? 'قيد المراجعة';
                        $status_class = $status_map[$status] ?? 'status-pending';
                        $products_json = htmlspecialchars($row['products'] ?? '[]', ENT_QUOTES, 'UTF-8');
                        $date = isset($row['created_at']) ? date('Y-m-d', strtotime($row['created_at'])) : '';
                    ?>
                    <tr>
                        <td>#<?php echo $row['id']; ?></td>
                        <td><?php echo $date; ?></td>
                        <td style="font-weight:bold;"><?php echo htmlspecialchars($row['total_price'] ?? '0'); ?> جنيه</td>
                        <td><span class="status-badge <?php echo $status_class; ?>"><?php echo $status; ?></span></td>
                        <td>
<button class="btn-view details-btn" 
                                data-id="<?php echo $row['id']; ?>" 
                                data-date="<?php echo isset($row['created_at']) ? date('Y-m-d h:i A', strtotime($row['created_at'])) : ''; ?>"
                                data-total="<?php echo htmlspecialchars($row['total_price'] ?? '0', ENT_QUOTES, 'UTF-8'); ?>"
                                data-address1="<?php echo htmlspecialchars($row['address_line1'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                data-address2="<?php echo htmlspecialchars($row['address_line2'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                data-city="<?php echo htmlspecialchars($row['city'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                data-gov="<?php echo htmlspecialchars($row['governorate'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                data-zip="<?php echo htmlspecialchars($row['zip_code'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                data-phone="<?php echo htmlspecialchars($row['phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                data-pay-method="<?php echo htmlspecialchars($row['payment_method'] ?? 'cod', ENT_QUOTES, 'UTF-8'); ?>"
                                data-pay-status="<?php echo htmlspecialchars($row['payment_status'] ?? 'pending', ENT_QUOTES, 'UTF-8'); ?>"
                                data-trx-id="<?php echo htmlspecialchars($row['transaction_id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                data-products='<?php echo $products_json; ?>'>
                                <i class="fa-solid fa-file-invoice"></i> الفاتورة
                            </button>                            
                            <?php if ($status === 'قيد المراجعة'): ?>
                                <form action="/my-orders/cancel" method="POST" style="display:inline;">
                                    <input type="hidden" name="csrf_token" value="<?php echo CSRF::generate(); ?>">
                                    <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" class="btn-delete-order" onclick="return confirm('هل أنت متأكد من إلغاء هذا الطلب؟');"><i class="fa-solid fa-xmark"></i> إلغاء الطلب</button>
                                </form>
                            <?php elseif ($status === 'ملغي' || $status === 'مكتمل'): ?>
                                <form action="/my-orders/hide" method="POST" style="display:inline;">
                                    <input type="hidden" name="csrf_token" value="<?php echo CSRF::generate(); ?>">
                                    <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" class="btn-hide-order" onclick="return confirm('هل أنت متأكد من مسح هذا الطلب من السجل؟');"><i class="fa-solid fa-trash-can"></i> مسح السجل</button>
                                </form>
                            <?php else: ?>
                                <span style="color: #999; font-size: 12px; margin-right: 10px;">لا يمكن الإلغاء</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan='5' style='text-align: center; padding: 30px; color:#777;'>لا يوجد لديك طلبات سابقة حتى الآن.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
      </div>
    </section>

    <section class="favorites-container container" style="margin-top: 40px; padding: 0 20px 80px;">
        <h2 class="main_title">المنتجات المفضلة</h2>
        <?php
        $favoriteModel = new Favorite();
        $favorites = $favoriteModel->getUserFavorites(Session::get('user_id'));
        ?>
        <div class="products-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px; margin-top: 20px;">
            <?php foreach ($favorites as $row): ?>
                <div class="product-card" style="border: 1px solid #eee; padding: 15px; border-radius: 8px; position: relative; text-align: center;">
                    <button class="heart-action-btn" data-product-id="<?= $row['id'] ?>" style="position:absolute; top:10px; right:10px; background:transparent; border:none; cursor:pointer; font-size:1.5rem; color:#ff4757;">
                        <i class="fa-solid fa-heart"></i>
                    </button>
                    <img src="<?= Product::getImageUrl($row['image_url']) ?>" alt="<?= htmlspecialchars($row['title']) ?>" style="width: 100%; max-height: 200px; object-fit: contain;">
                    <div class="card_title_wrapper" style="margin: 10px 0;">
                      <h3 style="font-size: 1.1rem; margin: 0; position:relative; z-index:2;"><?= htmlspecialchars($row['title']) ?></h3>
                    </div>
                    <p style="color: #ff4757; font-weight: bold; margin-bottom: 15px;"><?= htmlspecialchars($row['price']) ?>$</p>
                    <a href="/product?id=<?= $row['id'] ?>" style="display: inline-block; padding: 8px 15px; background: #333; color: #fff; text-decoration: none; border-radius: 4px;">عرض التفاصيل</a>
                </div>
            <?php endforeach; ?>
            <?php if (empty($favorites)): ?>
                <p style="grid-column: 1 / -1; text-align: center; color: #666;">لا توجد منتجات في المفضلة حالياً.</p>
            <?php endif; ?>
        </div>
    </section>
</main>

<div id="userOrderModal" class="modal">
  <div class="modal-content" style="width: 850px; max-width: 95%; padding: 0; border-radius: 16px; overflow: hidden; background: #f8fafc;">
    
    <!-- هيدر الفاتورة -->
    <div style="background: linear-gradient(135deg, var(--main-color), #0f6b8a); padding: 20px 30px; display: flex; justify-content: space-between; align-items: center;">
      <h3 style="margin: 0; color: #fff; font-size: 20px; display: flex; align-items: center; gap: 10px;">
        <i class="fa-solid fa-file-invoice-dollar" style="font-size: 24px;"></i> 
        فاتورة الطلب رقم #<span id="modalOrderIdUser" style="font-weight: 800;"></span>
      </h3>
      <span class="close-modal" id="closeUserModalBtn" style="color: #fff; opacity: 0.8; font-size: 28px; cursor: pointer; transition: 0.3s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.8'">&times;</span>
    </div>

    <!-- جسم الفاتورة (قابل للتمرير) -->
    <div style="padding: 25px; max-height: 75vh; overflow-y: auto;">
      
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; margin-bottom: 25px;">
        <!-- بيانات العميل -->
        <div style="background: #fff; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
          <h4 style="margin: 0 0 15px 0; color: #475569; font-size: 15px; display: flex; align-items: center; gap: 8px; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;">
            <div style="background: #e0f2fe; color: #0284c7; width: 30px; height: 30px; border-radius: 8px; display: flex; justify-content: center; align-items: center;"><i class="fa-solid fa-user"></i></div> بيانات العميل
          </h4>
          <p style="margin: 8px 0; font-size: 14px; color: #1e293b;"><strong>الهاتف:</strong> <span id="modalPhoneUser" style="direction: ltr; display: inline-block;"></span></p>
          <p style="margin: 8px 0; font-size: 14px; color: #1e293b;"><strong>التاريخ:</strong> <span id="modalDateUser"></span></p>
        </div>

        <!-- بيانات الشحن -->
        <div style="background: #fff; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
          <h4 style="margin: 0 0 15px 0; color: #475569; font-size: 15px; display: flex; align-items: center; gap: 8px; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;">
            <div style="background: #fce7f3; color: #db2777; width: 30px; height: 30px; border-radius: 8px; display: flex; justify-content: center; align-items: center;"><i class="fa-solid fa-truck-fast"></i></div> بيانات الشحن
          </h4>
          <p style="margin: 8px 0; font-size: 14px; color: #1e293b;"><strong>المنطقة:</strong> <span id="modalCityGovUser"></span></p>
          <p style="margin: 8px 0; font-size: 14px; color: #1e293b;"><strong>العنوان 1:</strong> <span id="modalAddr1User"></span></p>
          <p style="margin: 8px 0; font-size: 14px; color: #1e293b;"><strong>العنوان 2:</strong> <span id="modalAddr2User"></span></p>
        </div>
        
        <!-- بيانات الدفع -->
        <div style="background: #fff; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
          <h4 style="margin: 0 0 15px 0; color: #475569; font-size: 15px; display: flex; align-items: center; gap: 8px; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;">
            <div style="background: #fef3c7; color: #d97706; width: 30px; height: 30px; border-radius: 8px; display: flex; justify-content: center; align-items: center;"><i class="fa-solid fa-wallet"></i></div> بيانات الدفع
          </h4>
          <p style="margin: 8px 0; font-size: 14px; color: #1e293b;"><strong>الطريقة:</strong> <span id="modalPayMethodUser" style="font-weight:bold;"></span></p>
          <p style="margin: 8px 0; font-size: 14px; color: #1e293b;"><strong>الحالة:</strong> <span id="modalPayStatusUser"></span></p>
          <p style="margin: 8px 0; font-size: 14px; color: #1e293b;"><strong>رقم العملية:</strong> <span id="modalTrxIdUser" style="font-family: monospace; color: #64748b;"></span></p>
        </div>
      </div>

      <!-- المنتجات -->
      <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
        <h4 style="margin: 0; padding: 15px 20px; background: #f1f5f9; color: #1e293b; font-size: 16px; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; gap: 10px;">
          <i class="fa-solid fa-boxes-stacked" style="color: #64748b;"></i> المنتجات المطلوبة
        </h4>
        
        <!-- التعديل الجراحي: ستايل السكرول الاحترافي المخفي -->
        <style>
          #modalProductsListUser::-webkit-scrollbar { width: 6px; }
          #modalProductsListUser::-webkit-scrollbar-track { background: #f8fafc; }
          #modalProductsListUser::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
          #modalProductsListUser::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        </style>
        
        <!-- الحاوية القابلة للتمرير (تثبيت الارتفاع وإضافة Scroll) -->
        <div id="modalProductsListUser" style="padding: 10px 20px; display: flex; flex-direction: column; gap: 10px; max-height: 260px; overflow-y: auto;"></div>
        
        <!-- الإجمالي النهائي (ثابت بالأسفل) -->
        <div style="background: #f8fafc; padding: 20px; border-top: 2px dashed #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
          <span style="font-size: 18px; font-weight: 700; color: #475569;">إجمالي الطلب (شامل الشحن إن وجد):</span>
          <span id="modalGrandTotalUser" style="font-size: 24px; font-weight: 900; color: #059669;"></span>
        </div>
      </div>

    </div>
  </div>
</div>

<script>
document.querySelectorAll('.details-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('modalOrderIdUser').innerText = this.getAttribute('data-id');
        document.getElementById('modalDateUser').innerText = this.getAttribute('data-date');
        document.getElementById('modalAddr1User').innerText = this.getAttribute('data-address1');
        document.getElementById('modalAddr2User').innerText = this.getAttribute('data-address2') || 'لا يوجد';
        document.getElementById('modalCityGovUser').innerText = this.getAttribute('data-gov') + ' - ' + this.getAttribute('data-city');
        document.getElementById('modalPhoneUser').innerText = this.getAttribute('data-phone');
        document.getElementById('modalGrandTotalUser').innerText = parseFloat(this.getAttribute('data-total')).toFixed(2) + " ج.م";

        const methodVal = this.getAttribute('data-pay-method');
        const payMethod = (methodVal === 'online_card' || methodVal === 'online_wallet' || methodVal === 'online') ? '<span style="color:#3b82f6;">إلكتروني (فيزا/محفظة)</span>' : '<span style="color:#166534;">نقدي عند الاستلام (COD)</span>';
        const payStatus = this.getAttribute('data-pay-status') === 'paid' ? '<span style="color:#10b981;">مدفوع <i class="fa-solid fa-check"></i></span>' : '<span style="color:#f59e0b;">معلق <i class="fa-solid fa-clock"></i></span>';
        const trxId = this.getAttribute('data-trx-id') || '---';

        document.getElementById('modalPayMethodUser').innerHTML = payMethod;
        document.getElementById('modalPayStatusUser').innerHTML = payStatus;
        document.getElementById('modalTrxIdUser').innerText = trxId;

        const productsJson = this.getAttribute('data-products');
        const productsList = document.getElementById('modalProductsListUser');
        productsList.innerHTML = ''; 

        try {
            const products = JSON.parse(productsJson);
            if(products.length === 0) {
                productsList.innerHTML = '<p style="text-align: center; color: #64748b; padding: 20px;">لا توجد تفاصيل لهذا الطلب.</p>';
            } else {
                products.forEach(product => {
                    const rawSrc = product.src || 'images/logos/logo.png';
                    const imgUrl = rawSrc.startsWith('http') ? rawSrc : '<?= BASE_URL ?>' + rawSrc.replace(/^\/+/, '');
                    const title = product.title || 'منتج غير معروف';
                    const priceStr = product.price || '0';
                    const qty = parseInt(product.quantity || product.quantty || product.number || 1);
                    
                    const numericPrice = parseFloat(priceStr.toString().replace(/[^\d.]/g, '')) || 0;
                    const itemTotal = qty * numericPrice;

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
                                <div style="font-weight: 800; color: #f97316; font-size: 16px;">${itemTotal.toFixed(2)} ج.م</div>
                            </div>
                        </div>
                    `;
                });
            }
        } catch (e) {
            productsList.innerHTML = '<p style="color: #ef4444; text-align: center; padding: 20px;">عذراً، لا يمكن عرض المنتجات حالياً.</p>';
        }

        document.getElementById('userOrderModal').style.display = 'flex';
    });
});

const modal = document.getElementById('userOrderModal');
document.getElementById('closeUserModalBtn').addEventListener('click', () => { modal.style.display = 'none'; });
window.onclick = function(event) { if (event.target == modal) modal.style.display = 'none'; }
</script>
