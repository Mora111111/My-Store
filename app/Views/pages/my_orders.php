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
                            <button class="btn-view details-btn" data-id="<?php echo $row['id']; ?>" data-products='<?php echo $products_json; ?>'><i class="fa-solid fa-eye"></i> تفاصيل</button>
                            
                            <?php if ($status === 'قيد المراجعة'): ?>
                                <form action="/my-orders/cancel" method="POST" style="display:inline;">
                                    <input type="hidden" name="csrf_token" value="<?php echo CSRF::getToken(); ?>">
                                    <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" class="btn-delete-order" onclick="return confirm('هل أنت متأكد من إلغاء هذا الطلب؟');"><i class="fa-solid fa-xmark"></i> إلغاء الطلب</button>
                                </form>
                            <?php elseif ($status === 'ملغي' || $status === 'مكتمل'): ?>
                                <form action="/my-orders/hide" method="POST" style="display:inline;">
                                    <input type="hidden" name="csrf_token" value="<?php echo CSRF::getToken(); ?>">
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
                    <img src="/<?= ltrim($row['image_url'] ?? '', '/') ?>" alt="<?= htmlspecialchars($row['title']) ?>" style="width: 100%; max-height: 200px; object-fit: contain;">
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
    <div class="modal-content" style="border-radius: 20px; padding: 25px; max-width: 650px;">
      <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid var(--main-color); padding-bottom: 15px; margin-bottom: 20px;">
          <h3 style="margin: 0; color: var(--main-color); display: flex; align-items: center; gap: 10px;">
              <i class="fa-solid fa-box-open"></i>
              محتويات الطلب رقم #<span id="modalOrderIdUser"></span>
          </h3>
          <span class="close-modal" id="closeUserModalBtn" style="position: static; font-size: 30px; line-height: 1; margin-top: -5px;">&times;</span>
      </div>
      <div id="modalProductsListUser"></div>
    </div>
</div>

<script>
document.querySelectorAll('.details-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const orderId = this.getAttribute('data-id');
        const productsJson = this.getAttribute('data-products');
        
        document.getElementById('modalOrderIdUser').innerText = orderId;
        const productsList = document.getElementById('modalProductsListUser');
        productsList.innerHTML = ''; 

        try {
            const products = JSON.parse(productsJson);
            if(products.length === 0) {
                productsList.innerHTML = '<p style="text-align: center; color: #64748b; padding: 20px;">لا توجد تفاصيل لهذا الطلب.</p>';
            } else {
                products.forEach(product => {
                    const rawSrc = product.src || 'images/logos/logo.png';
                    const imgUrl = (rawSrc.startsWith('http') || rawSrc.startsWith('/')) ? rawSrc : '/' + rawSrc;
                    const title = product.title || 'منتج غير معروف';
                    const priceStr = product.price || '0';
                    const qty = parseInt(product.quantity || product.quantty || product.number || 1);
                    
                    const numericPrice = parseFloat(priceStr.toString().replace(/[^\d.]/g, '')) || 0;
                    const itemTotal = qty * numericPrice;

                    productsList.innerHTML += `
                        <div class="product-item" style="display: flex; align-items: center; justify-content: space-between; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; padding: 15px; margin-bottom: 12px; border-bottom: none;">
                            <div style="display: flex; align-items: center; gap: 15px; flex: 1;">
                                <img src="${imgUrl}" alt="${title}" style="width: 70px; height: 70px; object-fit: contain; border-radius: 8px; background: #fff; padding: 5px; border: 1px solid #e2e8f0; margin-left: 0;">
                                <div>
                                    <h4 style="margin: 0 0 8px 0; color: #0f172a; font-size: 16px;">${title}</h4>
                                    <p style="margin: 0; color: #64748b; font-size: 14px; display: flex; gap: 12px;">
                                        <span><i class="fa-solid fa-layer-group" style="color: #94a3b8; margin-left: 5px;"></i>الكمية: <strong style="color: #334155;">${qty}</strong></span>
                                        <span><i class="fa-solid fa-tag" style="color: #94a3b8; margin-left: 5px;"></i>السعر: <strong style="color: #334155;">${numericPrice.toFixed(2)} ج.م</strong></span>
                                    </p>
                                </div>
                            </div>
                            <div style="text-align: left; min-width: 100px;">
                                <span style="display: block; font-size: 12px; color: #64748b; margin-bottom: 4px;"><i class="fa-solid fa-calculator" style="margin-left: 4px;"></i>المجموع</span>
                                <strong style="color: #f97316; font-size: 18px;">${itemTotal.toFixed(2)} ج.م</strong>
                            </div>
                        </div>
                    `;
                });
            }
        } catch (e) {
            productsList.innerHTML = '<p style="color: #ef4444; text-align: center; padding: 20px;">عذراً، لا يمكن عرض المنتجات حالياً.</p>';
        }

        document.getElementById('userOrderModal').style.display = 'block';
    });
});

const modal = document.getElementById('userOrderModal');
document.getElementById('closeUserModalBtn').addEventListener('click', () => {
    modal.style.display = 'none';
});
window.onclick = function(event) {
    if (event.target == modal) modal.style.display = 'none';
}
</script>
