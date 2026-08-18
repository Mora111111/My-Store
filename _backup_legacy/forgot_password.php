<?php session_start(); ?>
<?php
require_once 'db.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_code_btn'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $query = "SELECT * FROM elogin WHERE email = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $new_v_code = rand(100000, 999999);
        $update = "UPDATE elogin SET verification_code = ? WHERE email = ?";
        $u_stmt = mysqli_prepare($conn, $update);
        mysqli_stmt_bind_param($u_stmt, "is", $new_v_code, $email);
        mysqli_stmt_execute($u_stmt);
        echo "<script>alert('كود إعادة التعيين الجديد هو: $new_v_code'); window.location.href='reset_password.php?email=$email';</script>";
    } else {
        echo "<script>alert('هذا البريد الإلكتروني غير مسجل لدينا!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>نسيت كلمة المرور</title>
    <link rel="stylesheet" href="style.css">
</head>
<body style="font-family: Arial; background: #f4f4f4; text-align: center; padding-top: 100px;">
    <div style="max-width: 400px; margin: auto; background: #fff; padding: 20px; border-radius: 10px;">
        <h2>استعادة الحساب</h2>
        <form method="POST">
            <input type="email" name="email" placeholder="اكتب بريدك الإلكتروني" required style="width: 90%; padding: 10px; margin: 10px 0;">
            <button type="submit" name="send_code_btn" style="width: 95%; padding: 10px; background: #007bff; color: white; border: none; cursor: pointer;">إرسال كود التحقق</button>
        </form>
    </div>
</body>
</html>