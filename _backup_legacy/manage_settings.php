<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: signin.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $about = mysqli_real_escape_string($conn, trim($_POST['about_text']));
    $phone1 = mysqli_real_escape_string($conn, trim($_POST['phone1']));
    $phone2 = mysqli_real_escape_string($conn, trim($_POST['phone2']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $address = mysqli_real_escape_string($conn, trim($_POST['address']));

    mysqli_query($conn, "UPDATE settings SET about_text='$about', phone1='$phone1', phone2='$phone2', email='$email', address='$address' WHERE id=1");
    
    $settings_query = mysqli_query($conn, "SELECT * FROM settings WHERE id = 1");
    $site_settings = mysqli_fetch_assoc($settings_query);
    $success_msg = "تم حفظ الإعدادات بنجاح!";
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إعدادات الموقع - لوحة التحكم</title>
    <link rel="stylesheet" href="css/all.min.css" />
    <link rel="stylesheet" href="style.css" />
    <link rel="icon" href="images/icons/shopping-cart_head.png">
    <style>
        .settings_wrapper { max-width: 800px; margin: 50px auto; padding: 30px; background: #fff; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .settings_title { color: var(--main-color); margin-bottom: 20px; text-align: center; font-size: 24px; }
        .form_row { margin-bottom: 20px; }
        .form_row label { display: block; font-weight: bold; margin-bottom: 8px; color: #333; }
        .form_row input, .form_row textarea { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-family: inherit; font-size: 15px; }
        .form_row input:focus, .form_row textarea:focus { outline: none; border-color: var(--main-color); }
        .save_btn { background: var(--main-color); color: #fff; border: none; padding: 12px 25px; border-radius: 5px; cursor: pointer; font-size: 16px; font-family: inherit; transition: 0.3s; width: 100%; }
        .save_btn:hover { opacity: 0.9; }
        .alert_success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; text-align: center; font-weight: bold; border: 1px solid #c3e6cb; }
        .back_link { display: inline-flex; align-items: center; gap: 8px; color: var(--main-color); text-decoration: none; font-weight: bold; margin-bottom: 20px; }
    </style>
</head>
<body style="background: #f8f9fa;">
    <div class="settings_wrapper">
        <a href="dashboard.php" class="back_link"><i class="fa-solid fa-arrow-right"></i> العودة للوحة التحكم</a>
        
        <h2 class="settings_title"><i class="fa-solid fa-gear"></i> إعدادات الموقع العامة</h2>
        
        <?php if(isset($success_msg)): ?>
            <div class="alert_success"><?php echo $success_msg; ?></div>
        <?php endif; ?>

        <form action="manage_settings.php" method="POST">
            <div class="form_row">
                <label>نص "معلومات عنا" (يظهر في الفوتر):</label>
                <textarea name="about_text" rows="4" required><?php echo htmlspecialchars($site_settings['about_text'] ?? ''); ?></textarea>
            </div>
            
            <div class="form_row">
                <label>رقم الهاتف الأول:</label>
                <input type="text" name="phone1" value="<?php echo htmlspecialchars($site_settings['phone1'] ?? ''); ?>" required>
            </div>
            
            <div class="form_row">
                <label>رقم الهاتف الثاني:</label>
                <input type="text" name="phone2" value="<?php echo htmlspecialchars($site_settings['phone2'] ?? ''); ?>">
            </div>
            
            <div class="form_row">
                <label>البريد الإلكتروني للتواصل:</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($site_settings['email'] ?? ''); ?>" required>
            </div>
            
            <div class="form_row">
                <label>عنوان المتجر:</label>
                <input type="text" name="address" value="<?php echo htmlspecialchars($site_settings['address'] ?? ''); ?>" required>
            </div>
            
            <button type="submit" class="save_btn"><i class="fa-solid fa-floppy-disk"></i> حفظ التعديلات</button>
        </form>
    </div>
</body>
</html>