<?php 
session_start();
require_once 'db.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit();
}

$user_id = intval($_SESSION['user_id']);
$error_msg = "";

if (isset($_GET['cancel_id'])) {
    $cancel_id = intval($_GET['cancel_id']);
    $check_query = mysqli_query($conn, "SELECT status FROM orders WHERE id = $cancel_id AND user_id = $user_id");
    
    if (mysqli_num_rows($check_query) > 0) {
        $order_data = mysqli_fetch_assoc($check_query);
        if ($order_data['status'] === 'قيد المراجعة') {
            mysqli_query($conn, "UPDATE orders SET status = 'ملغي' WHERE id = $cancel_id");
            header("Location: my_orders.php");
            exit();
        } else {
            $error_msg = "لا يمكن إلغاء الطلب لأنه قيد التنفيذ أو تم شحنه.";
        }
    }
}

if (isset($_GET['hide_id'])) {
    $hide_id = intval($_GET['hide_id']);
    $check_query = mysqli_query($conn, "SELECT status FROM orders WHERE id = $hide_id AND user_id = $user_id");
    
    if (mysqli_num_rows($check_query) > 0) {
        $order_data = mysqli_fetch_assoc($check_query);
        if ($order_data['status'] === 'ملغي' || $order_data['status'] === 'مكتمل') {
            mysqli_query($conn, "UPDATE orders SET user_hidden = 1 WHERE id = $hide_id");
            header("Location: my_orders.php");
            exit();
        } else {
            $error_msg = "لا يمكن حذف الطلب من السجل إلا إذا كان مكتملاً أو ملغياً.";
        }
    }
}

$status_map = [
    'قيد المراجعة' => 'status-pending',
    'تم الشحن' => 'status-shipped',
    'مكتمل' => 'status-completed',
    'ملغي' => 'status-cancelled'
];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="css/all.min.css" />
  <link rel="stylesheet" href="style.css" />
  <link rel="icon" href="images/icons/shopping-cart_head.png">
  <title>MY Store - سجل طلباتي</title>
  <style>
    .my-orders-container { padding: 150px 20px 80px; min-height: 60vh; }
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
</head>
<body>
  
  <header class="header" id="header" style="background: #fff; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
    <nav class="nav container">
      <div class="nav_box">
        <div class="nav_btns">
          <div class="nav_toggle" id="nav-toggle">
            <i class="fa-solid fa-bars"></i>
          </div>

          <div class="login_toggle profile-dropdown-container">
            <?php if(isset($_SESSION['user_id'])): ?>
              <a href="javascript:void(0);" class="login_link profile-trigger" id="profile-btn">
                <i class="fa-solid fa-circle-user" style="color: var(--main-color); font-size: 26px;"></i>
              </a>
              <div class="profile-menu" id="profile-menu">
                  <div class="profile-header">
                      مرحباً، <span><?php echo htmlspecialchars(explode(' ', $_SESSION['user_name'])[0]); ?></span> 👋
                  </div>
                  <ul class="profile-links">
                      <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                          <li><a href="dashboard.php"><i class="fa-solid fa-gauge"></i> لوحة الإدارة</a></li>
                      <?php else: ?>
                          <li><a href="my_orders.php" style="color: var(--main-color); font-weight: bold;"><i class="fa-solid fa-box-open"></i> طلباتي</a></li>
                      <?php endif; ?>
                      <li>
                          <a href="logout.php" class="logout-link"><i class="fa-solid fa-arrow-right-from-bracket"></i> تسجيل خروج</a>
                      </li>
                  </ul>
              </div>
            <?php else: ?>
              <a href="signin.php" class="login_link"><i class="fa-regular fa-user"></i></a>
            <?php endif; ?>
          </div>

          <div class="nav_shop" id="cart-shop">
            <img src="images/icons/cart.png" alt="">
            <span class="cart_count">0</span>
          </div>
        </div>

        <div class="nav_menu" id="nav-menu">
          <i class="fa-solid fa-xmark nav_menu_close" id="menu-close"></i>
          <ul class="nav-list">
            <li class="nav-item"><a href="index.php" class="nav_link">الرئيسية</a></li>
            <li class="nav-item"><a href="products.php" class="nav_link">المنتجات</a></li>
            <li class="nav-item"><a href="services.php" class="nav_link">الخدمات</a></li>
            <li class="nav-item"><a href="about_us.php" class="nav_link">من نحن</a></li>
            <li class="nav-item"><a href="contact_us.php" class="nav_link">اتصل بنا</a></li>
          </ul>
        </div>
      </div>
      <img src="images/logos/logo.png" alt="MY Store Logo" class="nav_logo" />
    </nav>
  </header>

  <div class="cart">
    <h2 class="cart_title">سلة التسوق</h2>
    <div class="cart_content"></div>
    <div class="total">
      <div class="total_title">الاجمالي</div>
      <div class="total_price">. جنية</div>
    </div>
    <a href="payment.php" class="btn_buy">شراء</a>
    <i class="fa-solid fa-xmark" id="cart-close"></i>
  </div>

  <main class="main">
    <section class="my-orders-container container">
      <h2 class="main_title">سجل طلباتي</h2>
      
      <?php if(!empty($error_msg)): ?>
          <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px; text-align: center;">
              <?php echo $error_msg; ?>
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
                $query = "SELECT * FROM orders WHERE user_id = $user_id AND user_hidden = 0 ORDER BY id DESC";
                $result = mysqli_query($conn, $query);

                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $status = isset($row['status']) ? $row['status'] : 'قيد المراجعة';
                        $status_class = isset($status_map[$status]) ? $status_map[$status] : 'status-pending';
                        
                        $products_json = htmlspecialchars($row['products'], ENT_QUOTES, 'UTF-8');
                        $date = date('Y-m-d', strtotime($row['created_at']));
                        
                        $action_buttons = "<button class='btn-view details-btn' data-id='{$row['id']}' data-products='{$products_json}'><i class='fa-solid fa-eye'></i> تفاصيل</button>";
                        
                        if ($status === 'قيد المراجعة') {
                            $action_buttons .= "<a href='my_orders.php?cancel_id={$row['id']}' class='btn-delete-order' onclick='return confirm(\"هل أنت متأكد من إلغاء هذا الطلب؟\");'><i class='fa-solid fa-ban'></i> إلغاء الطلب</a>";
                        } elseif ($status === 'ملغي' || $status === 'مكتمل') {
                            $action_buttons .= "<a href='my_orders.php?hide_id={$row['id']}' class='btn-hide-order' onclick='return confirm(\"هل أنت متأكد من إزالة هذا الطلب من سجلك؟ (سيظل مسجلاً لدى الإدارة)\");'><i class='fa-solid fa-trash-can'></i> إزالة من السجل</a>";
                        } else {
                            $action_buttons .= "<span style='color: #999; font-size: 12px; margin-right: 10px;'>لا يمكن الإلغاء</span>";
                        }

                        echo "<tr>
                                <td>#{$row['id']}</td>
                                <td>{$date}</td>
                                <td style='font-weight:bold;'>{$row['total_price']} جنيه</td>
                                <td><span class='status-badge {$status_class}'>{$status}</span></td>
                                <td>{$action_buttons}</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' style='text-align: center; padding: 30px; color:#777;'>لا يوجد لديك طلبات سابقة حتى الآن.</td></tr>";
                }
                ?>
            </tbody>
        </table>
      </div>
    </section>

    <div class="user-messages-section container" style="margin-top: 50px; background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
        <h3 style="margin-bottom: 25px; color: var(--main-color, #1e293b); border-bottom: 2px solid #eee; padding-bottom: 10px;">
            <i class="fa-solid fa-headset"></i> استفساراتي وردود الدعم
        </h3>

        <?php
        $user_email = "";
        $email_query = "SELECT email FROM elogin WHERE id = $user_id";
        $email_result = mysqli_query($conn, $email_query);
        
        if ($email_result && mysqli_num_rows($email_result) > 0) {
            $email_data = mysqli_fetch_assoc($email_result);
            $user_email = trim($email_data['email']);
        }
        
        if (!empty($user_email)) {
            $msg_query = "SELECT * FROM contact_messages WHERE LOWER(TRIM(email)) = LOWER('$user_email') ORDER BY id DESC";
            $msg_result = mysqli_query($conn, $msg_query);

            if ($msg_result && mysqli_num_rows($msg_result) > 0) {
                while ($row = mysqli_fetch_assoc($msg_result)) {
                    ?>
                    <div style="border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 20px; padding: 20px;">
                        <div style="margin-bottom: 15px;">
                            <strong style="color: #475569;"><i class="fa-regular fa-comment-dots"></i> سؤالك:</strong>
                            <p style="margin: 8px 0 0 0; color: #333; line-height: 1.6;">
                                <?php echo nl2br(htmlspecialchars($row['message'])); ?>
                            </p>
                        </div>

                        <?php if (!empty($row['reply'])): ?>
                            <div style="background: #f0fdfa; padding: 15px; border-radius: 6px; border-right: 4px solid #0d9488;">
                                <strong style="color: #0f766e;"><i class="fa-solid fa-robot"></i> رد المساعد الذكي:</strong>
                                <p style="margin: 8px 0 0 0; color: #115e59; line-height: 1.6;">
                                    <?php echo nl2br(htmlspecialchars($row['reply'])); ?>
                                </p>
                            </div>
                        <?php else: ?>
                            <div style="background: #f8fafc; padding: 15px; border-radius: 6px; color: #64748b; font-style: italic;">
                                <i class="fa-solid fa-clock"></i> جاري مراجعة استفسارك...
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php
                }
            } else {
                echo "<p style='text-align:center; color:#94a3b8;'>لا توجد استفسارات سابقة لك (تأكد من استخدام نفس إيميل حسابك <strong style='color:#333;'>$user_email</strong> عند إرسال رسالة من صفحة اتصل بنا).</p>";
            }
        } else {
            echo "<p style='text-align:center; color:#94a3b8;'>الرجاء تسجيل الدخول لعرض استفساراتك.</p>";
        }
        ?>
    </div>

  </main>

<footer class="footer">
    <div class="footer_container container grid_content">
      <div class="footer_item">
        <h3 class="footer_title">معلومات عنا</h3>
        <p class="footer_p">نحن متجر على الإنترنت نقدم أفضل المنتجات ذات الجودة العالية والتسليم السريع</p>
        <img src="images/logos/logo-white.png" alt="" class="footer_img" />
      </div>
      <div class="footer_item">
        <h3 class="footer_title">الحساب</h3>
        <ul class="footer_list">
          <li class="footer_li"><a href="signin.php" class="footer_link">تسجيل الدخول</a></li>
          <li class="footer_li"><a href="signup.php" class="footer_link">إنشاء حساب</a></li>
          <li class="footer_li"><a href="#" class="footer_link"></a></li>
          <li class="footer_li"><a href="products.php" class="footer_link">المنتجات</a></li>
        </ul>
      </div>
      <div class="footer_item">
        <h3 class="footer_title">الروابط</h3>
        <ul class="footer_list">
          <li class="footer_li"><a href="services.php" class="footer_link">الخدمات</a></li>
          <li class="footer_li"><a href="about_us.php" class="footer_link">من نحن</a></li>
          <li class="footer_li"><a href="index.php#features" class="footer_link">المنتجات المميزة </a></li>
          <li class="footer_li"><a href="index.php#latest" class="footer_link">أحدث المنتجات</a></li>
          <li class="footer_li"><a href="contact_us.php" class="footer_link">اتصل بنا</a></li>
        </ul>
      </div>
      <div class="footer_item">
        <h3 class="footer_title">اتصل بنا</h3>
        <ul class="footer_list">
          <li class="footer_li"><i class="fa-solid fa-phone footer-icon"></i><span>01017******</span></li>
          <li class="footer_li"><i class="fa-solid fa-phone footer-icon"></i><span>01034******</span></li>
          <li class="footer_li"><i class="fa-solid fa-envelope footer-icon"></i><span>MY-Store@gmail.com</span></li>
          <li class="footer_li"><i class="fa-solid fa-location-dot footer-icon"></i><span>المحافظات - مصر</span></li>
        </ul>
      </div>
    </div>
  <p class="copyright container">
  جميع الحقوق محفوظة. MY Store &copy; 2025 - 2026
</p>

  </footer>

  <div id="userOrderModal" class="modal">
    <div class="modal-content">
      <span class="close-modal" id="closeUserModalBtn">&times;</span>
      <h3 style="margin-top:0; border-bottom:2px solid var(--main-color); padding-bottom:10px; color: var(--main-color);">محتويات الطلب رقم #<span id="modalOrderIdUser"></span></h3>
      <div id="modalProductsListUser"></div>
    </div>
  </div>

  <script src="Js/app.js"></script>
  <script src="Js/scroll.js"></script>
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
</body>
</html>