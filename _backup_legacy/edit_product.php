<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

require_once 'db.php';
$msg = "";

if (!isset($_GET['id'])) {
    header("Location: manage_products.php");
    exit();
}
$product_id = intval($_GET['id']);

$query = "SELECT * FROM products WHERE id = '$product_id'";
$result = mysqli_query($conn, $query);
$product = mysqli_fetch_assoc($result);

if (!$product) {
    header("Location: manage_products.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_product_btn'])) {
    $title = mysqli_real_escape_string($conn, trim($_POST['title']));
    $category_class = mysqli_real_escape_string($conn, trim($_POST['category_class']));
    $price = floatval($_POST['price']);
    $description = mysqli_real_escape_string($conn, trim($_POST['description']));

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $image_name = $_FILES['image']['name'];
        $image_tmp = $_FILES['image']['tmp_name'];
        
        $file_ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
        $check_image = getimagesize($image_tmp);

        if ($check_image !== false && in_array($file_ext, $allowed_exts)) {
            $new_image_name = time() . '_' . uniqid() . '.' . $file_ext;
            $image_folder = 'uploads/' . $new_image_name;

            if (move_uploaded_file($image_tmp, $image_folder)) {
                if (!empty($product['image_url']) && file_exists($product['image_url'])) {
                    unlink($product['image_url']);
                }
                $update_query = "UPDATE products SET title='$title', category_class='$category_class', price='$price', description='$description', image_url='$image_folder' WHERE id='$product_id'";
            } else {
                $msg = "<div style='background:#fee2e2; color:#991b1b; padding:14px 18px; border-radius:16px; margin-bottom:25px; border:1px solid #fecaca; font-weight:500; text-align:center;'><i class='fa-solid fa-triangle-exclamation'></i> خطأ في رفع الصورة الجديدة.</div>";
            }
        } else {
            $msg = "<div style='background:#fee2e2; color:#991b1b; padding:14px 18px; border-radius:16px; margin-bottom:25px; border:1px solid #fecaca; font-weight:500; text-align:center;'><i class='fa-solid fa-triangle-exclamation'></i> صيغة الملف غير مدعومة. يرجى رفع صورة صالحة.</div>";
        }
    } else {
        $update_query = "UPDATE products SET title='$title', category_class='$category_class', price='$price', description='$description' WHERE id='$product_id'";
    }

    if (isset($update_query)) {
        if (mysqli_query($conn, $update_query)) {
            echo "<script>alert('تم تعديل المنتج بنجاح!'); window.location.href='manage_products.php';</script>";
            exit();
        } else {
            $msg = "<div style='background:#fee2e2; color:#991b1b; padding:14px 18px; border-radius:16px; margin-bottom:25px; border:1px solid #fecaca; font-weight:500; text-align:center;'><i class='fa-solid fa-triangle-exclamation'></i> حدث خطأ أثناء التحديث في قاعدة البيانات.</div>";
        }
    }
}

$clean_description = $product['description'] ?? '';
$clean_description = str_replace(array('</p>', '<br>', '<br/>', '</li>'), "\n", $clean_description);
$clean_description = strip_tags($clean_description);
$clean_description = preg_replace("/\n\s*\n/", "\n\n", trim($clean_description));
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تعديل المنتج - لوحة التحكم</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Tajawal', sans-serif; background: #f8fafc; display: flex; height: 100vh; overflow: hidden; color: #1e293b; }
        
        /* السايد بار الموحد */
        .sidebar { width: 280px; background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%); color: #f1f5f9; padding: 30px 0; display: flex; flex-direction: column; box-shadow: 5px 0 20px rgba(0,0,0,0.08); position: relative; z-index: 10; }
        .sidebar h2 { text-align: center; margin-bottom: 40px; font-size: 26px; font-weight: 700; letter-spacing: 1px; background: linear-gradient(135deg, #38bdf8, #2dd4bf); -webkit-background-clip: text; background-clip: text; color: transparent; padding: 0 15px; }
        .sidebar a { display: flex; align-items: center; color: #cbd5e1; padding: 14px 25px; margin: 4px 12px; text-decoration: none; border-radius: 12px; transition: all 0.3s ease; font-size: 16px; font-weight: 500; position: relative; overflow: hidden; }
        .sidebar a i { margin-left: 15px; width: 24px; text-align: center; font-size: 18px; transition: 0.2s; }
        .sidebar a span { flex: 1; }
        .sidebar a:hover { background: rgba(56, 189, 248, 0.15); color: #ffffff; transform: translateX(-5px); }
        .sidebar a.active { background: linear-gradient(135deg, #38bdf8, #2dd4bf); color: #0f172a; font-weight: 700; box-shadow: 0 8px 16px rgba(56, 189, 248, 0.3); }
        .sidebar a.active i { color: #0f172a; }

        /* المحتوى والهيدر الموحد */
        .main-content { flex: 1; overflow-y: auto; display: flex; flex-direction: column; background: #f1f5f9; }
        .header { background: #ffffff; padding: 20px 40px; box-shadow: 0 4px 20px rgba(0,0,0,0.02); display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; }
        .header h3 { font-size: 24px; font-weight: 700; color: #0f172a; letter-spacing: -0.5px; }
        
        .logout-btn { background: #ffffff; color: #ef4444; border: 1.5px solid #fee2e2; padding: 12px 24px; cursor: pointer; border-radius: 40px; text-decoration: none; font-weight: 600; font-size: 15px; transition: all 0.25s; display: flex; align-items: center; gap: 8px; box-shadow: 0 2px 6px rgba(239, 68, 68, 0.05); }
        .logout-btn i { font-size: 16px; }
        .logout-btn:hover { background: #ef4444; color: white; border-color: #ef4444; box-shadow: 0 8px 16px rgba(239, 68, 68, 0.2); transform: translateY(-2px); }

        .content-area { padding: 35px 40px; }
        .card { background: #ffffff; padding: 30px 35px; border-radius: 28px; box-shadow: 0 15px 30px -10px rgba(0,0,0,0.05); border: 1px solid #f1f5f9; max-width: 700px; margin: auto; }
        
        /* الفورم الموحد */
        .form-group { margin-bottom: 22px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #334155; font-size: 15px; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 14px 16px; border: 1.5px solid #e2e8f0; border-radius: 16px; box-sizing: border-box; font-family: 'Tajawal', sans-serif; font-size: 15px; background: #fafbfc; transition: all 0.2s; resize: vertical; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: #38bdf8; background: #ffffff; box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.1); }
        
        .btn-submit { background: linear-gradient(135deg, #38bdf8, #2dd4bf); color: #0f172a; border: none; padding: 14px 28px; cursor: pointer; border-radius: 40px; font-size: 16px; font-weight: 700; transition: all 0.3s; box-shadow: 0 8px 16px rgba(56, 189, 248, 0.2); width: 100%; display: block; margin-top: 10px; }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 12px 20px rgba(56, 189, 248, 0.3); }
        
        .cancel-btn { display: block; text-align: center; margin-top: 20px; color: #64748b; text-decoration: none; font-weight: 600; transition: 0.2s; }
        .cancel-btn:hover { color: #ef4444; }

        .main-content::-webkit-scrollbar { width: 8px; }
        .main-content::-webkit-scrollbar-track { background: #f1f5f9; }
        .main-content::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 20px; }
        .main-content::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>مدير المتجر</h2>
        <a href="dashboard.php"><i class="fa-solid fa-house"></i><span>الرئيسية</span></a>
        <!-- علامة الأكتيف هنا عشان إحنا جوه إدارة المنتجات -->
        <a href="manage_products.php" class="active"><i class="fa-solid fa-box-open"></i><span>إدارة المنتجات</span></a>
        <a href="manage_comments.php"><i class="fa-solid fa-comments"></i><span>تعليقات العملاء</span></a>
        <a href="manage_orders.php"><i class="fa-solid fa-cart-shopping"></i><span>طلبات الشراء</span></a>
        <a href="manage_messages.php"><i class="fa-solid fa-envelope"></i><span>رسائل الزوار</span></a>
    </div>

    <div class="main-content">
        <div class="header">
            <h3><i class="fa-solid fa-pen-to-square" style="margin-left: 12px; color: #38bdf8;"></i>تعديل المنتج</h3>
            <a href="logout.php" class="logout-btn">
                <i class="fa-solid fa-right-from-bracket"></i>
                تسجيل الخروج
            </a>
        </div>

        <div class="content-area">
            <div class="card">
                <h2 style="margin-top:0; text-align:center; color:#0f172a;"><i class="fa-solid fa-pen-to-square" style="color:#38bdf8; margin-left:8px;"></i>تعديل بيانات المنتج</h2>
                <?php echo $msg; ?>
                
                <div style="text-align:center; margin-bottom:25px;">
                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" width="120" height="120" style="border-radius:16px; object-fit:cover; box-shadow: 0 8px 16px rgba(0,0,0,0.1);">
                </div>

                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>اسم المنتج:</label>
                        <input type="text" name="title" value="<?php echo htmlspecialchars($product['title']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>القسم (Category):</label>
                        <select name="category_class" required>
                            <option value="">-- اختر القسم --</option>
                            <?php
                            $categories = ["هواتف", "جهاز لوحي", "لابتوب", "ساعات ذكية", "فلاشات", "كاميرات", "راوترات", "اكسسوارات", "مستعمل"];
                            foreach ($categories as $cat) {
                                $selected = ($product['category_class'] == $cat) ? 'selected' : '';
                                echo "<option value='$cat' $selected>$cat</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>السعر (بالجنيه):</label>
                        <input type="number" name="price" step="0.01" min="0" value="<?php echo htmlspecialchars($product['price']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>الوصف التفصيلي للمنتج:</label>
                        <textarea name="description" rows="10" required placeholder="أدخل وصفاً تسويقياً وتفصيلياً للمنتج..."><?php echo htmlspecialchars($clean_description); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>صورة المنتج (اتركها فارغة إذا لم ترد تغييرها):</label>
                        <input type="file" name="image" accept="image/png, image/jpeg, image/gif, image/webp" style="padding: 12px;">
                    </div>
                    
                    <button type="submit" name="edit_product_btn" class="btn-submit"><i class="fa-solid fa-floppy-disk" style="margin-left: 8px;"></i> حفظ التعديلات</button>
                    <a href="manage_products.php" class="cancel-btn"><i class="fa-solid fa-arrow-right" style="margin-left: 5px;"></i> إلغاء والعودة للقائمة</a>
                </form>
            </div>
        </div>
    </div>

</body>
</html>