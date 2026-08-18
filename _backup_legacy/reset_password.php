<?php session_start(); ?>
<?php
require_once 'db.php';
$email = $_GET['email'] ?? '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reset_btn'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $code = mysqli_real_escape_string($conn, $_POST['code']);
    $new_pass = password_hash($_POST['new_password'], PASSWORD_BCRYPT);

    $check = "SELECT * FROM elogin WHERE email = ? AND verification_code = ?";
    $stmt = mysqli_prepare($conn, $check);
    mysqli_stmt_bind_param($stmt, "si", $email, $code);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($res) > 0) {
        $update = "UPDATE elogin SET password = ?, failed_attempts = 0, verification_code = NULL WHERE email = ?";
        $u_stmt = mysqli_prepare($conn, $update);
        mysqli_stmt_bind_param($u_stmt, "ss", $new_pass, $email);
        if (mysqli_stmt_execute($u_stmt)) {
            echo "<script>alert('تم تغيير كلمة المرور بنجاح! يمكنك الآن تسجيل الدخول.'); window.location.href='signin.php';</script>";
        }
    } else {
        echo "<script>alert('كود التحقق غير صحيح!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تعيين كلمة مرور جديدة</title>
    <link rel="stylesheet" href="style.css">
</head>
<body style="font-family: Arial; background: #f4f4f4; text-align: center; padding-top: 50px;">
    <div style="max-width: 400px; margin: auto; background: #fff; padding: 20px; border-radius: 10px;">
        <h2>تعيين كلمة مرور جديدة</h2>
        <form method="POST">
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
            <input type="text" name="code" placeholder="كود التحقق" required style="width: 90%; padding: 10px; margin: 10px 0;">
            <input type="password" name="new_password" placeholder="كلمة المرور الجديدة" required style="width: 90%; padding: 10px; margin: 10px 0;">
            <button type="submit" name="reset_btn" style="width: 95%; padding: 10px; background: #28a745; color: white; border: none; cursor: pointer;">حفظ وتفعيل الحساب</button>
        </form>
    </div>
</body>
</html>