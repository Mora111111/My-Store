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
                                <span style="color: #999; font-size: 12px; margin-right: 10px;">قيد المراجعة</span>
                            <?php elseif ($status === 'ملغي' || $status === 'مكتمل'): ?>
                                <span style="color: #999; font-size: 12px; margin-right: 10px;">مكتمل/ملغي</span>
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
                    <img src="<?= htmlspecialchars($row['image_url'] ?? '') ?>" alt="<?= htmlspecialchars($row['title']) ?>" style="width: 100%; max-height: 200px; object-fit: contain;">
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
    <div class="modal-content">
      <span class="close-modal" id="closeUserModalBtn">&times;</span>
      <h3 style="margin-top:0; border-bottom:2px solid var(--main-color); padding-bottom:10px; color: var(--main-color);">محتويات الطلب رقم #<span id="modalOrderIdUser"></span></h3>
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
                productsList.innerHTML = '<p>لا توجد تفاصيل.</p>';
            } else {
                products.forEach(product => {
                    const imgUrl = product.src || 'images/logos/logo.png';
                    const title = product.title || 'منتج غير معروف';
                    const price = product.price || '0';
                    const qty = product.quantity || product.quantty || product.number || 1; 
                    
                    productsList.innerHTML += `
                        <div class="product-item">
                            <img src="${imgUrl}" alt="">
                            <div style="flex:1;">
                                <h4 style="margin: 0 0 5px 0;">${title}</h4>
                                <p style="margin: 0; color: #666; font-size: 14px;">الكمية: <strong>${qty}</strong> | السعر: <strong>${price}</strong></p>
                            </div>
                        </div>
                    `;
                });
            }
        } catch (e) {
            productsList.innerHTML = '<p style="color:red;">عذراً، لا يمكن عرض المنتجات حالياً.</p>';
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
