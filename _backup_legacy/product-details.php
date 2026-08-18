<?php 
session_start();
require_once 'db.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: products.php");
    exit();
}

$product_id = intval($_GET['id']);

 if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_comment'])) {
    $customer_name = mysqli_real_escape_string($conn, trim($_POST['customer_name']));
    $comment_text = mysqli_real_escape_string($conn, trim($_POST['comment_text']));
    
    if (!empty($customer_name) && !empty($comment_text)) {
        
        $ai_reply = "";
        
         $positive_words = ['ممتاز', 'روعة', 'جميل', 'حلو', 'تحفة', 'شكرا', 'عظيم', 'عجبني', 'سريع', 'اصلي', 'جيد', 'رائع', 'مذهل', 'خيالي', 'مضبوط', 'عاش', 'تسلم', 'مية مية', 'بيرفكت', 'اسطوري', 'جودة عالية', 'افضل', 'احسن', 'رهيب', 'بطل', 'تغليف ممتاز', 'محترمين', 'ثقة'];
        
        $negative_words = ['سيء', 'وحش', 'مكسور', 'تأخير', 'بطيء', 'غالي', 'مقلد', 'مش شغال', 'زفت', 'نصابين', 'مش كويس', 'بايظ', 'تالف', 'زبالة', 'أسوأ', 'عطلان', 'نصب', 'كدب', 'رديء', 'مضروب', 'غلط', 'مخدوع', 'مفيش مصداقية', 'تجربة سيئة', 'لا انصح', 'عيب'];
        
        $inquiry_words = ['بكام', 'سعر', 'امتى', 'توصيل', 'ضمان', 'متوفر', 'الوان', 'مقاس', 'كم', 'استفسار', 'فين', 'تفاصيل', 'شحن', 'مصاريف', 'إمتى', 'كام', 'مكانكم', 'فرع', 'ازاي', 'ممكن', 'هل يوجد', 'لو سمحت', 'طريقة الدفع', 'تقسيط'];

         $positive_score = 0;
        $negative_score = 0;
        $inquiry_score = 0;

         $lower_comment = mb_strtolower($comment_text);

        foreach ($positive_words as $word) {
            if (mb_strpos($lower_comment, $word) !== false) $positive_score++;
        }
        foreach ($negative_words as $word) {
            if (mb_strpos($lower_comment, $word) !== false) $negative_score++;
        }
        foreach ($inquiry_words as $word) {
            if (mb_strpos($lower_comment, $word) !== false) $inquiry_score++;
        }

         $positive_replies = [
            "أهلاً يا {$customer_name}، الشكر موصول لثقتك في متجرنا، ونتمنى دائماً تقديم الأفضل! 😊",
            "هذا التقييم يسعد إدارة المتجر جداً يا {$customer_name}، شرفنا التعامل معك ونتمنى لك تجربة استخدام موفقة.",
            "يا مرحباً بك، كلمات الإشادة نعتز بها، شكراً لاختيارك تكنو ستور.",
            "تسلم يا {$customer_name} على هذا الرأي الجميل، رضاك هو هدفنا الأول.",
            "تقييم رائع من عميل أروع، الإدارة تشكرك يا {$customer_name} على هذا الدعم المتواصل."
        ];

        $negative_replies = [
            "أهلاً يا {$customer_name}، نعتذر بشدة عن هذه التجربة غير المرضية، سيتم التواصل معك من الدعم الفني فوراً لحل المشكلة.",
            "يؤسفنا جداً هذا الخطأ يا فندم، فريق المتابعة سيقوم بالاتصال بك حالا لحل الموضوع بشكل يرضيك تماماً.",
            "اعتذار بالغ عن أي تقصير. يرجى ترك الأمر لنا وسيتم حله أو استبدال المنتج في أسرع وقت ممكن.",
            "شكواك يا {$customer_name} محل اهتمام كبير، تم تحويلها للإدارة وسيتم التواصل معك لإيجاد حل جذري.",
            "التجربة السيئة ليست من معايير المتجر، سنتواصل معك فوراً يا {$customer_name} لعمل اللازم وتعويضك."
        ];

        $inquiry_replies = [
            "أهلاً يا {$customer_name}، تم تحويل استفسارك لقسم المبيعات وسيتم الرد عليك بالتفاصيل حالا.",
            "مرحباً بك، استفساراتك تهمنا جداً، سيتم توضيح كل التفاصيل في رسالة خاصة قريباً.",
            "أهلاً بحضرتك، سيتم مراجعة طلبك والرد عليك بكل التفاصيل المطلوبة.",
            "سؤال ممتاز، خدمة العملاء ستقوم بتوضيح السعر وتفاصيل التوصيل والضمان في أقرب وقت.",
            "رسالتك قيد المراجعة يا {$customer_name}، دقائق وسيتم الرد بالمعلومات الكاملة."
        ];

        $neutral_replies = [
            "أهلاً يا {$customer_name}، تم تسجيل تعليقك بنجاح وسيتم مراجعته من الإدارة.",
            "مرحباً بك، تفاعلك في تكنو ستور محل تقدير كبير لدينا.",
            "شكراً لمرورك وتعليقك، يسعدنا تواصلك الدائم.",
            "تم استلام رسالتك يا {$customer_name} بنجاح، شكراً لوقتك."
        ];

         if ($negative_score > 0) {
            $ai_reply = $negative_replies[array_rand($negative_replies)];
        } elseif ($inquiry_score > 0) {
            $ai_reply = $inquiry_replies[array_rand($inquiry_replies)];
        } elseif ($positive_score > 0) {
            $ai_reply = $positive_replies[array_rand($positive_replies)];
        } else {
            $ai_reply = $neutral_replies[array_rand($neutral_replies)];
        }

         $insert_comment = "INSERT INTO product_comments (product_id, customer_name, comment_text, admin_reply) VALUES ('$product_id', '$customer_name', '$comment_text', '$ai_reply')";
        
        mysqli_query($conn, $insert_comment);
        
         header("Location: product-details.php?id=" . $product_id . "#comments-section");
        exit();
    }
}

 
$query = "SELECT * FROM products WHERE id = $product_id";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    header("Location: products.php");
    exit();
}

$product = mysqli_fetch_assoc($result);

$price_parts = explode('.', number_format($product['price'], 2, '.', ''));
$main_price = $price_parts[0];
$decimals = $price_parts[1];

$rating = isset($product['rating']) ? (float)$product['rating'] : 5;
$stars_html = '';
for ($i = 1; $i <= 5; $i++) {
    if ($rating >= $i) {
        $stars_html .= '<i class="fa-solid fa-star"></i>';
    } elseif ($rating >= $i - 0.5) {
        $stars_html .= '<i class="fa-regular fa-star-half-stroke fa-flip-horizontal"></i>';
    } else {
        $stars_html .= '<i class="fa-regular fa-star"></i>';
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="css/all.min.css" />
  <link rel="stylesheet" href="style.css" />
  <link rel="icon" href="images/icons/shopping-cart_head.png">
  <title>MY Store - <?php echo htmlspecialchars($product['title']); ?></title>
</head>

<body>
  <header class="header" id="header">
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
                      مرحباً، <span><?php echo explode(' ', $_SESSION['user_name'])[0]; ?></span> 👋
                  </div>
                  <ul class="profile-links">
                      <li>
                          <a href="dashboard.php">
                              <i class="fa-solid fa-id-badge"></i> الملف الشخصي
                          </a>
                      </li>
                      <li>
                          <a href="logout.php" class="logout-link">
                              <i class="fa-solid fa-arrow-right-from-bracket"></i> تسجيل خروج
                          </a>
                      </li>
                  </ul>
              </div>
            <?php else: ?>
              <a href="signin.php" class="login_link">
                <i class="fa-regular fa-user"></i>
              </a>
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
            <li class="nav-item"><a href="products.php" class="nav_link active">المنتجات</a></li>
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
      <div class="total_price">.جنية</div>
    </div>
    <a href="payment.php" class="btn_buy">شراء</a>
    <div class="cart_empty">
      <div><img src="images/Cart-img.png"></div>
      <p>عربة التسوق فارغة</p>
      <a href="products.php" class="btn_shopping">إستكشف المنتجات</a>
    </div>
    <i class="fa-solid fa-xmark" id="cart-close"></i>
  </div>

  <div class="product_details_section container">
    <div class="card" style="margin-top: 150px; margin-bottom: 50px; max-width: 600px; margin-left: auto; margin-right: auto; padding: 20px;">
      <div class="box_img" style="height: 400px; background: transparent;">
          <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['title']); ?>" class="card_image" style="max-height: 100%; object-fit: contain;" />
      </div>
      <div class="card_details" style="text-align: center; margin-top: 20px;">
        <h2 class="product_details_title card_title" style="font-size: 24px; color: var(--main-color); margin-bottom: 15px;"><?php echo htmlspecialchars($product['title']); ?></h2>
        
        <div class="rating" style="margin-bottom: 15px; font-size: 18px;">
          <?php echo $stars_html; ?>
        </div>
        
        <p class="product_details_price card_price" style="font-size: 28px; font-weight: bold; margin-bottom: 30px;">
          <?php echo $main_price; ?>.<small><?php echo $decimals; ?></small> <span>جنيه</span>
        </p>
        
        <?php if (!empty($product['description'])): ?>
        <div style="text-align: right; background: #f9f9f9; padding: 20px; border-radius: 8px; margin-bottom: 25px; line-height: 1.8; color: #555; border: 1px solid #eee;">
            <h3 style="margin-top:0; color:#2c3e50; border-bottom: 2px solid var(--main-color); display: inline-block; padding-bottom: 5px; font-size: 18px;"><i class="fa-solid fa-circle-info"></i> مواصفات وتفاصيل المنتج</h3>
            <div style="margin-top: 15px; font-size: 15px;">
                <?php 
                echo $product['description']; 
                ?>
            </div>
        </div>
        <?php endif; ?>
        <button class="card_btn" style="position: relative; left: 0; transform: none; margin: auto; padding: 12px 50px; font-size: 16px;">أضافة إلي العربة</button>
      </div>
    </div>
  </div>

  <div id="comments-section" class="container" style="margin-bottom: 60px;">
    <div class="card" style="max-width: 800px; margin: 0 auto; padding: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border-radius: 10px;">
        <h2 style="color: #2c3e50; margin-top:0; border-bottom: 2px solid #eee; padding-bottom: 15px;">آراء وتقييمات العملاء <i class="fa-solid fa-comments" style="color: var(--main-color);"></i></h2>
        
        <div class="comments-list" style="margin-top: 25px; margin-bottom: 40px;">
            <?php
            $comments_query = mysqli_query($conn, "SELECT * FROM product_comments WHERE product_id = $product_id ORDER BY id DESC");
            if (mysqli_num_rows($comments_query) > 0) {
                while ($c = mysqli_fetch_assoc($comments_query)) {
                    echo "<div style='background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 15px; border-right: 4px solid var(--main-color);'>";
                    echo "<h4 style='margin: 0 0 10px 0; color: #333; display: flex; justify-content: space-between; align-items: center;'>";
                    echo "<span><i class='fa-solid fa-circle-user' style='color:#bdc3c7;'></i> " . htmlspecialchars($c['customer_name']) . "</span>";
                    echo "<span style='font-size:12px; color:#999; font-weight:normal;'><i class='fa-regular fa-clock'></i> " . date('Y-m-d', strtotime($c['created_at'])) . "</span>";
                    echo "</h4>";
                    echo "<p style='margin: 0; color: #555; line-height: 1.6; font-size: 15px;'>" . nl2br(htmlspecialchars($c['comment_text'])) . "</p>";
                    
                    if (!empty($c['admin_reply'])) {
                        echo "<div style='margin-top: 15px; padding: 15px; background: #e8f4f8; border-radius: 5px; border-right: 4px solid #3498db;'>";
                        echo "<strong style='color: #2980b9; display:block; margin-bottom: 5px;'><i class='fa-solid fa-headset'></i> رد إدارة المتجر:</strong>";
                        echo "<p style='margin: 0; color: #444; line-height: 1.6; font-size: 14.5px;'>" . nl2br(htmlspecialchars($c['admin_reply'])) . "</p>";
                        echo "</div>";
                    }
                    echo "</div>";
                }
            } else {
                echo "<div style='text-align:center; padding: 30px; background: #f9f9f9; border-radius: 8px; border: 1px dashed #ddd;'>";
                echo "<i class='fa-regular fa-comment-dots' style='font-size: 40px; color: #ccc; margin-bottom: 10px;'></i>";
                echo "<p style='color:#777; margin: 0; font-size: 16px;'>لا توجد تعليقات حتى الآن. كن أول من يشاركنا رأيه وتجربته!</p>";
                echo "</div>";
            }
            ?>
        </div>

        <div style="background: #fff; padding: 25px; border-radius: 8px; border: 1px solid #eee;">
            <h3 style="margin-top:0; margin-bottom: 20px; color:#333;"><i class="fa-solid fa-pen"></i> أضف تعليقك على المنتج</h3>
            <form method="POST" action="product-details.php?id=<?php echo $product_id; ?>#comments-section">
                <div style="margin-bottom: 15px;">
                    <label style="display:block; margin-bottom:5px; color:#555; font-weight:bold;">الاسم :</label>
                    <input type="text" name="customer_name" required placeholder="أكتب اسمك هنا" style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 5px; font-family: inherit; font-size: 15px; box-sizing: border-box;">
                </div>
                <div style="margin-bottom: 20px;">
                    <label style="display:block; margin-bottom:5px; color:#555; font-weight:bold;">نص التعليق أو الاستفسار:</label>
                    <textarea name="comment_text" required placeholder="اكتب رأيك بصدق هنا ليفيد الآخرين..." rows="5" style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 5px; font-family: inherit; font-size: 15px; resize: vertical; box-sizing: border-box;"></textarea>
                </div>
                <button type="submit" name="submit_comment" style="background: var(--main-color); color: white; border: none; padding: 12px 30px; border-radius: 5px; cursor: pointer; font-size: 16px; font-family: inherit; font-weight: bold; transition: 0.3s; width: 100%;"><i class="fa-solid fa-paper-plane"></i> إرسال التعليق</button>
            </form>
        </div>
    </div>
  </div>
  <footer class="footer">
    <div class="footer_container container grid_content">
      <div class="footer_item">
        <h3 class="footer_title">معلومات عنا</h3>
        <p class="footer_p">نحن متجر على الإنترنت نقدم أفضل المنتجات الالكترونية ذات الجودة العالية والتسليم السريع</p>
        <img src="images/logos/logo-white.png" alt="" class="footer_img">
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
          <li class="footer_li"><a href="index.php#features" class="footer_link">المنتجات المميزة</a></li>
          <li class="footer_li"><a href="index.php#latest" class="footer_link">أحدث المنتجات</a></li>
          <li class="footer_li"><a href="contact_us.php" class="footer_link">اتصل بنا</a></li>
        </ul>
      </div>

      <div class="footer_item">
        <h3 class="footer_title">اتصل بنا</h3>
        <ul class="footer_list">
          <li class="footer_li"><i class="fa-solid fa-phone footer-icon"></i><span>+2001017******</span></li>
          <li class="footer_li"><i class="fa-solid fa-phone footer-icon"></i><span>+2001034******</span></li>
          <li class="footer_li"><i class="fa-solid fa-envelope footer-icon"></i><span>MY-Store@gmail.com</span></li>
          <li class="footer_li"><i class="fa-solid fa-location-dot footer-icon"></i><span>كفر الشيخ - مصر</span></li>
        </ul>
      </div>
    </div>
    <p class="copyright container">جميع الحقوق محفوظة MY store © 2025 - 2026</p>
  </footer>
  
  <script src="Js/app.js"></script>
  <script src="Js/scroll.js"></script>
</body>
</html>