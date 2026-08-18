<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit();
}

$user_email = $_SESSION['user_email']; // تأكد أنك تخزن الإيميل في السيشن عند تسجيل الدخول
$query = "SELECT * FROM contact_messages WHERE email = '$user_email' ORDER BY id DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="css/all.min.css" />
  <link rel="stylesheet" href="style.css" />
  <link rel="icon" href="images/icons/shopping-cart_head.png">
  <title>MY Store - رسائلي</title>
  <style>
    /* تنسيقات بسيطة عشان شكل الرسائل يبقى شيك */
    .messages_container { padding: 120px 0 60px; min-height: 80vh; background: #f9f9f9; }
    .msg_card { background: white; border-radius: 10px; padding: 20px; margin-bottom: 25px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); border-right: 5px solid var(--main-color); }
    .msg_header { display: flex; justify-content: space-between; margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 10px; }
    .msg_content { color: #555; line-height: 1.6; margin-bottom: 15px; }
    .bot_reply_box { background: #e3f2fd; padding: 15px; border-radius: 8px; border-right: 4px solid #2196f3; color: #0d47a1; }
    .no_reply { font-style: italic; color: #999; }
    .reply_icon { margin-left: 8px; color: #2196f3; }
  </style>
</head>
<body>

  <header class="header" id="header">
    <nav class="nav container">
      <div class="nav_box">
        <div class="nav_btns">
          <div class="nav_toggle" id="nav-toggle"><i class="fa-solid fa-bars"></i></div>
          <div class="login_toggle profile-dropdown-container">
            <a href="javascript:void(0);" class="login_link profile-trigger" id="profile-btn">
                <i class="fa-solid fa-circle-user" style="color: var(--main-color); font-size: 26px;"></i>
            </a>
          </div>
        </div>
        <div class="nav_menu" id="nav-menu">
          <ul class="nav-list">
            <li class="nav-item"><a href="index.php" class="nav_link">الرئيسية</a></li>
            <li class="nav-item"><a href="products.php" class="nav_link">المنتجات</a></li>
            <li class="nav-item"><a href="dashboard.php" class="nav_link">الملف الشخصي</a></li>
            <li class="nav-item"><a href="contact_us.php" class="nav_link">اتصل بنا</a></li>
          </ul>
        </div>
      </div>
      <img src="images/logos/logo.png" alt="MY Store Logo" class="nav_logo" />
    </nav>
  </header>

  <section class="messages_container">
    <div class="container">
      <h2 style="margin-bottom: 30px; text-align: center;">صندوق الرسائل والردود الذكية</h2>

      <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while($row = mysqli_fetch_assoc($result)): ?>
          <div class="msg_card">
            <div class="msg_header">
              <strong><i class="fa-solid fa-envelope"></i> رسالتك المرسلة</strong>
              <small><?php echo date('Y-m-d', strtotime($row['id'])); // أو عمود التاريخ لو موجود ?></small>
            </div>
            
            <div class="msg_content">
              <?php echo nl2br(htmlspecialchars($row['message'])); ?>
            </div>

            <?php if (!empty($row['reply'])): ?>
              <div class="bot_reply_box">
                <strong><i class="fa-solid fa-robot reply_icon"></i> رد المساعد الذكي:</strong><br>
                <?php echo nl2br(htmlspecialchars($row['reply'])); ?>
              </div>
            <?php else: ?>
              <div class="no_reply">
                <i class="fa-solid fa-clock"></i> جاري مراجعة رسالتك من قبل فريق الدعم...
              </div>
            <?php endif; ?>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <div style="text-align: center; padding: 50px;">
            <i class="fa-regular fa-folder-open" style="font-size: 50px; color: #ccc;"></i>
            <p>لا توجد رسائل سابقة لديك.</p>
        </div>
      <?php endif; ?>
      
    </div>
  </section>

  <footer class="footer">
    <p class="copyright container">
      جميع الحقوق محفوظة.MY Store &copy; 2025 - 2026
    </p>
  </footer>

  <script src="Js/scroll.js"></script>
</body>
</html>