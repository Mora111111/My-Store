<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit();
}

$user_id = intval($_SESSION['user_id']);
$msg = "";
$msg_type = ""; 

$query_user = "SELECT username, email FROM elogin WHERE id = $user_id";
$result_user = mysqli_query($conn, $query_user);
$user_data = mysqli_fetch_assoc($result_user);

$query_orders = "SELECT COUNT(id) as total_orders FROM orders WHERE user_id = $user_id";
$result_orders = mysqli_query($conn, $query_orders);
$orders_data = mysqli_fetch_assoc($result_orders);
$total_orders = $orders_data['total_orders'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_password'])) {
    $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    if (empty($new_password) || empty($confirm_password)) {
        $msg = "يرجى تعبئة جميع حقول كلمة المرور.";
        $msg_type = "error";
    } elseif ($new_password !== $confirm_password) {
        $msg = "كلمتا المرور غير متطابقتين.";
        $msg_type = "error";
    } elseif (strlen($new_password) < 6) {
        $msg = "كلمة المرور يجب أن تكون 6 أحرف على الأقل.";
        $msg_type = "error";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_query = "UPDATE elogin SET password = '$hashed_password' WHERE id = $user_id";
        
        if (mysqli_query($conn, $update_query)) {
            $msg = "تم تغيير كلمة المرور بنجاح.";
            $msg_type = "success";
        } else {
            $msg = "حدث خطأ أثناء تحديث كلمة المرور.";
            $msg_type = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الملف الشخصي - MY Store</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Cairo', sans-serif;
            padding-top: 100px;
        }
        .profile-container {
            max-width: 800px;
            margin: 0 auto 50px;
            padding: 0 20px;
        }
        .profile-header {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            text-align: center;
            margin-bottom: 30px;
        }
        .profile-avatar {
            width: 100px;
            height: 100px;
            background: var(--main-color, #1abc9c);
            color: #fff;
            font-size: 40px;
            line-height: 100px;
            border-radius: 50%;
            margin: 0 auto 15px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .profile-name {
            font-size: 24px;
            color: #333;
            margin-bottom: 5px;
        }
        .profile-email {
            color: #777;
            font-size: 16px;
        }
        .stats-box {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }
        .stat-item {
            background: #f1f1f1;
            padding: 15px 30px;
            border-radius: 8px;
            text-align: center;
        }
        .stat-item h4 {
            margin: 0;
            font-size: 22px;
            color: var(--main-color, #1abc9c);
        }
        .stat-item p {
            margin: 5px 0 0;
            color: #555;
            font-size: 14px;
        }
        .profile-box {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .profile-box h3 {
            margin-top: 0;
            color: #333;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            outline: none;
            transition: 0.3s;
        }
        .form-group input:focus {
            border-color: var(--main-color, #1abc9c);
        }
        .btn-update {
            background: var(--main-color, #1abc9c);
            color: #fff;
            border: none;
            padding: 12px 25px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            font-family: 'Cairo', sans-serif;
            transition: 0.3s;
        }
        .btn-update:hover {
            opacity: 0.9;
        }
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>

    <header class="header" id="header" style="background:#fff; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
        <nav class="nav container">
            <a href="index.php" style="color:var(--main-color); font-weight:bold; font-size:20px; text-decoration:none;">
                <i class="fa-solid fa-arrow-right"></i> العودة للمتجر
            </a>
            <img src="images/logos/logo.png" alt="MY Store Logo" class="nav_logo" />
        </nav>
    </header>

    <div class="profile-container">
        
        <?php if($msg != ""): ?>
            <div class="alert alert-<?php echo $msg_type; ?>">
                <?php echo $msg; ?>
            </div>
        <?php endif; ?>

        <div class="profile-header">
            <div class="profile-avatar">
                <i class="fa-solid fa-user"></i>
            </div>
            <h2 class="profile-name"><?php echo htmlspecialchars($user_data['username']); ?></h2>
            <p class="profile-email"><?php echo htmlspecialchars($user_data['email']); ?></p>
            
            <div class="stats-box">
                <div class="stat-item">
                    <h4><?php echo $total_orders; ?></h4>
                    <p>إجمالي الطلبات</p>
                </div>
                <div class="stat-item">
                    <h4><a href="profile.php" class="menu-item"><i class="fa-solid fa-user-gear"></i> ملفي الشخصي</a><a href="my_orders.php
" style="color:var(--main-color); text-decoration:none;"><i class="fa-solid fa-box-open"></i></a></h4>
                    <p>عرض طلباتي</p>
                </div>
            </div>
        </div>

        <div class="profile-box">
            <h3><i class="fa-solid fa-lock"></i> تغيير كلمة المرور</h3>
            <form method="POST" action="">
                <div class="form-group">
                    <label>كلمة المرور الجديدة</label>
                    <input type="password" name="new_password" placeholder="أدخل كلمة المرور الجديدة" required>
                </div>
                <div class="form-group">
                    <label>تأكيد كلمة المرور الجديدة</label>
                    <input type="password" name="confirm_password" placeholder="أعد إدخال كلمة المرور" required>
                </div>
                <button type="submit" name="update_password" class="btn-update">تحديث البيانات</button>
            </form>
        </div>

    </div>

</body>
</html>