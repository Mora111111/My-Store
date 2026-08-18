<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

require_once 'db.php';
$error_msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = mysqli_real_escape_string($conn, $_POST['new_status']);
    
    mysqli_query($conn, "UPDATE orders SET status = '$new_status' WHERE id = $order_id");
    header("Location: manage_orders.php");
    exit();
}

if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $check_query = mysqli_query($conn, "SELECT status FROM orders WHERE id = $delete_id");
    
    if (mysqli_num_rows($check_query) > 0) {
        $order_data = mysqli_fetch_assoc($check_query);
        if ($order_data['status'] === 'ملغي') {
            mysqli_query($conn, "DELETE FROM orders WHERE id = $delete_id");
            header("Location: manage_orders.php");
            exit();
        } else {
            $error_msg = "لا يمكن حذف الطلب نهائياً إلا إذا كان في حالة (ملغي).";
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
    <meta charset="UTF-8">
    <title>إدارة الطلبات - لوحة التحكم</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Tajawal', sans-serif;
            background: #f8fafc;
            display: flex;
            height: 100vh;
            overflow: hidden;
            color: #1e293b;
        }

         .sidebar {
            width: 280px;
            background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
            color: #f1f5f9;
            padding: 30px 0;
            display: flex;
            flex-direction: column;
            box-shadow: 5px 0 20px rgba(0,0,0,0.08);
            position: relative;
            z-index: 10;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 40px;
            font-size: 26px;
            font-weight: 700;
            letter-spacing: 1px;
            background: linear-gradient(135deg, #38bdf8, #2dd4bf);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            padding: 0 15px;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            color: #cbd5e1;
            padding: 14px 25px;
            margin: 4px 12px;
            text-decoration: none;
            border-radius: 12px;
            transition: all 0.3s ease;
            font-size: 16px;
            font-weight: 500;
            position: relative;
            overflow: hidden;
        }

        .sidebar a i {
            margin-left: 15px;
            width: 24px;
            text-align: center;
            font-size: 18px;
            transition: 0.2s;
        }

        .sidebar a span {
            flex: 1;
        }

        .sidebar a:hover {
            background: rgba(56, 189, 248, 0.15);
            color: #ffffff;
            transform: translateX(-5px);
        }

        .sidebar a.active {
            background: linear-gradient(135deg, #38bdf8, #2dd4bf);
            color: #0f172a;
            font-weight: 700;
            box-shadow: 0 8px 16px rgba(56, 189, 248, 0.3);
        }

        .sidebar a.active i {
            color: #0f172a;
        }

         .main-content {
            flex: 1;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            background: #f1f5f9;
        }

         .header {
            background: #ffffff;
            padding: 20px 40px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.02);
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e2e8f0;
        }

        .header h3 {
            font-size: 24px;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: -0.5px;
        }

        .logout-btn {
            background: #ffffff;
            color: #ef4444;
            border: 1.5px solid #fee2e2;
            padding: 12px 24px;
            cursor: pointer;
            border-radius: 40px;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.25s;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 2px 6px rgba(239, 68, 68, 0.05);
        }

        .logout-btn i {
            font-size: 16px;
        }

        .logout-btn:hover {
            background: #ef4444;
            color: white;
            border-color: #ef4444;
            box-shadow: 0 8px 16px rgba(239, 68, 68, 0.2);
            transform: translateY(-2px);
        }

         .content-area {
            padding: 35px 40px;
        }

         .card {
            background: #ffffff;
            padding: 30px 35px;
            border-radius: 28px;
            box-shadow: 0 15px 30px -10px rgba(0,0,0,0.05);
            border: 1px solid #f1f5f9;
            margin-bottom: 35px;
            transition: 0.2s;
        }

        .card h2 {
            color: #0f172a;
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card h2 i {
            color: #38bdf8;
        }

         .orders-table {
            width: 100%;
            border-collapse: collapse;
            text-align: right;
            margin-top: 15px;
        }

        .orders-table th {
            background: #f8fafc;
            color: #475569;
            font-weight: 600;
            padding: 16px 12px;
            border-bottom: 2px solid #e2e8f0;
            font-size: 15px;
        }

        .orders-table td {
            padding: 16px 12px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }

        .status-badge {
            padding: 6px 14px;
            border-radius: 40px;
            font-size: 13px;
            font-weight: 600;
            display: inline-block;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-shipped {
            background: #dbeafe;
            color: #1e40af;
        }

        .status-completed {
            background: #d1fae5;
            color: #065f46;
        }

        .status-cancelled {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-form {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .status-select {
            padding: 8px 12px;
            border-radius: 30px;
            border: 1.5px solid #e2e8f0;
            font-family: 'Tajawal', sans-serif;
            font-size: 14px;
            background: #fafbfc;
            cursor: pointer;
        }

        .status-select:focus {
            outline: none;
            border-color: #38bdf8;
        }

        .btn-update {
            background: #3b82f6;
            color: white;
            border: none;
            padding: 8px 14px;
            border-radius: 30px;
            cursor: pointer;
            font-weight: 500;
            font-size: 13px;
            transition: 0.2s;
        }

        .btn-update:hover {
            background: #2563eb;
        }

        .btn-view {
            background: #38bdf8;
            color: #0f172a;
            border: none;
            padding: 8px 16px;
            border-radius: 30px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-weight: 500;
            font-size: 13px;
            transition: 0.2s;
        }

        .btn-view:hover {
            background: #2dd4bf;
            transform: translateY(-1px);
        }

        .btn-delete-order {
            background: #ef4444;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 30px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-right: 5px;
            font-weight: 500;
            font-size: 13px;
            transition: 0.2s;
        }

        .btn-delete-order:hover {
            background: #dc2626;
        }

         .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            backdrop-filter: blur(4px);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: #ffffff;
            margin: 5% auto;
            padding: 30px;
            border-radius: 32px;
            width: 90%;
            max-width: 800px;
            position: relative;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
        }

        .close-modal {
            position: absolute;
            top: 20px;
            left: 20px;
            font-size: 24px;
            cursor: pointer;
            color: #94a3b8;
            transition: 0.2s;
        }

        .close-modal:hover {
            color: #ef4444;
        }

        .product-item {
            display: flex;
            align-items: center;
            border-bottom: 1px solid #e2e8f0;
            padding: 15px 0;
        }

        .product-item img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            margin-left: 15px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }

        .address-box {
            background: #f8fafc;
            padding: 18px;
            border-radius: 20px;
            margin-bottom: 20px;
            border: 1px solid #e2e8f0;
        }

         .main-content::-webkit-scrollbar {
            width: 8px;
        }
        .main-content::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        .main-content::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 20px;
        }
        .main-content::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>مدير المتجر</h2>
        <a href="dashboard.php"><i class="fa-solid fa-house"></i><span>الرئيسية</span></a>
        <a href="manage_products.php"><i class="fa-solid fa-box-open"></i><span>إدارة المنتجات</span></a>
        <a href="manage_comments.php"><i class="fa-solid fa-comments"></i><span>تعليقات العملاء</span></a>
        <a href="manage_orders.php" class="active"><i class="fa-solid fa-cart-shopping"></i><span>طلبات الشراء</span></a>
        <a href="manage_messages.php"><i class="fa-solid fa-envelope"></i><span>رسائل الزوار</span></a>
        <a href="manage_users.php"><i class="fa-solid fa-users"></i><span>إدارة المستخدمين</span></a>

    </div>

    <div class="main-content">
        <div class="header">
            <h3><i class="fa-solid fa-cart-shopping" style="margin-left: 12px; color: #38bdf8;"></i>طلبات الشراء</h3>
            <a href="logout.php" class="logout-btn">
                <i class="fa-solid fa-right-from-bracket"></i>
                تسجيل الخروج
            </a>
        </div>

        <div class="content-area">
            <?php if(!empty($error_msg)): ?>
                <div style="background:#fee2e2; color:#991b1b; padding:14px 18px; border-radius:16px; margin-bottom:25px; border:1px solid #fecaca; font-weight:500;">
                    <i class="fa-solid fa-triangle-exclamation" style="margin-left:8px;"></i> <?php echo $error_msg; ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <h2 style="margin-top:0;"><i class="fa-solid fa-list"></i> سجل جميع الطلبات</h2>
                <table class="orders-table">
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
                    $orders_query = mysqli_query($conn, "SELECT * FROM orders ORDER BY FIELD(status, 'قيد المراجعة', 'تم الشحن', 'مكتمل', 'ملغي'), id DESC");
                    
                    if (mysqli_num_rows($orders_query) > 0) {
                        while ($row = mysqli_fetch_assoc($orders_query)) {
                            $status = $row['status'];
                            $status_class = isset($status_map[$status]) ? $status_map[$status] : 'status-pending';
                            
                            $products_json = htmlspecialchars($row['products'], ENT_QUOTES, 'UTF-8');
                            
                            echo "<tr>";
                            echo "<td><span style='font-weight:700;'>#{$row['id']}</span></td>";
                            echo "<td style='font-weight:500;'>" . htmlspecialchars($row['full_name']) . "</td>";
                            echo "<td><a href='tel:{$row['phone']}' style='color:#3b82f6; text-decoration:none;'>{$row['phone']}</a></td>";
                            echo "<td style='font-weight:700; color:#0f172a;'>{$row['total_price']} ج.م</td>";
                            echo "<td><span class='status-badge {$status_class}'>{$status}</span></td>";
                            echo "<td>
                                    <form method='POST' class='status-form'>
                                        <input type='hidden' name='order_id' value='{$row['id']}'>
                                        <select name='new_status' class='status-select'>
                                            <option value='قيد المراجعة' " . ($status == 'قيد المراجعة' ? 'selected' : '') . ">قيد المراجعة</option>
                                            <option value='تم الشحن' " . ($status == 'تم الشحن' ? 'selected' : '') . ">تم الشحن</option>
                                            <option value='مكتمل' " . ($status == 'مكتمل' ? 'selected' : '') . ">مكتمل</option>
                                            <option value='ملغي' " . ($status == 'ملغي' ? 'selected' : '') . ">ملغي</option>
                                        </select>
                                        <button type='submit' name='update_status' class='btn-update'><i class='fa-solid fa-check'></i>تأكيد</button>
                                    </form>
                                  </td>";
                            echo "<td>
                                    <button class='btn-view details-btn' 
                                        data-id='{$row['id']}' 
                                        data-products='{$products_json}'
                                        data-address1='" . htmlspecialchars($row['address_line1'], ENT_QUOTES, 'UTF-8') . "'
                                        data-address2='" . htmlspecialchars($row['address_line2'], ENT_QUOTES, 'UTF-8') . "'
                                        data-city='" . htmlspecialchars($row['city'], ENT_QUOTES, 'UTF-8') . "'
                                        data-gov='" . htmlspecialchars($row['governorate'], ENT_QUOTES, 'UTF-8') . "'
                                        data-zip='" . htmlspecialchars($row['zip_code'], ENT_QUOTES, 'UTF-8') . "'
                                        data-date='" . date('Y-m-d H:i', strtotime($row['created_at'])) . "'>
                                        <i class='fa-solid fa-eye'></i> عرض
                                    </button>";
                            
                            if ($status === 'ملغي') {
                                echo "<a href='manage_orders.php?delete_id={$row['id']}' class='btn-delete-order' onclick='return confirm(\"هل أنت متأكد من حذف هذا الطلب نهائياً من قاعدة البيانات؟\");'><i class='fa-solid fa-trash'></i> حذف</a>";
                            }
                            
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7' style='text-align:center; padding:40px; color:#94a3b8; font-size:16px;'><i class='fa-solid fa-cart-shopping' style='font-size:40px; margin-bottom:15px; opacity:0.5;'></i><br>لا توجد طلبات حتى الآن.</td></tr>";
                    }
                    ?>
                </table>
            </div>
        </div>
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
                            const imgUrl = product.src || 'images/logos/logo.png';
                            const title = product.title || 'منتج غير معروف';
                            const price = product.price || '0';
                            const qty = product.quantty || 1; 
                            
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

        const modal = document.getElementById('adminOrderModal');
        document.getElementById('closeAdminModalBtn').addEventListener('click', () => {
            modal.style.display = 'none';
        });
        window.onclick = function(event) {
            if (event.target == modal) modal.style.display = 'none';
        }
    </script>

</body>
</html>