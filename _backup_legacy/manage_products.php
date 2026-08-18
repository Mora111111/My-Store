<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

require_once 'db.php';
$msg = "";

if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    
    $get_img_query = mysqli_query($conn, "SELECT image_url FROM products WHERE id = '$delete_id'");
    if ($row = mysqli_fetch_assoc($get_img_query)) {
        if (!empty($row['image_url']) && file_exists($row['image_url'])) {
            unlink($row['image_url']); 
        }
    }
    
    mysqli_query($conn, "DELETE FROM products WHERE id = '$delete_id'");
    header("Location: manage_products.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
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
            $image_url = 'uploads/' . $new_image_name; 
            
            if (!is_dir('uploads')) {
                mkdir('uploads', 0777, true);
            }
            
            if (move_uploaded_file($image_tmp, $image_url)) {
                $query = "INSERT INTO products (title, price, category_class, description, image_url) VALUES ('$title', '$price', '$category_class', '$description', '$image_url')";
                if (mysqli_query($conn, $query)) {
                    $msg = "<div style='background:#d1fae5; color:#065f46; padding:14px 18px; border-radius:16px; margin-bottom:25px; border:1px solid #a7f3d0; font-weight:500;'><i class='fa-solid fa-check-circle' style='margin-left:8px;'></i> تمت إضافة المنتج بنجاح!</div>";
                } else {
                    $msg = "<div style='background:#fee2e2; color:#991b1b; padding:14px 18px; border-radius:16px; margin-bottom:25px; border:1px solid #fecaca; font-weight:500;'><i class='fa-solid fa-triangle-exclamation' style='margin-left:8px;'></i> حدث خطأ في قاعدة البيانات.</div>";
                    unlink($image_url);
                }
            } else {
                $msg = "<div style='background:#fee2e2; color:#991b1b; padding:14px 18px; border-radius:16px; margin-bottom:25px; border:1px solid #fecaca; font-weight:500;'><i class='fa-solid fa-triangle-exclamation' style='margin-left:8px;'></i> حدث خطأ أثناء رفع الصورة.</div>";
            }
        } else {
            $msg = "<div style='background:#fee2e2; color:#991b1b; padding:14px 18px; border-radius:16px; margin-bottom:25px; border:1px solid #fecaca; font-weight:500;'><i class='fa-solid fa-triangle-exclamation' style='margin-left:8px;'></i> عذراً، الملف المرفوع ليس صورة صالحة.</div>";
        }
    } else {
        $msg = "<div style='background:#fee2e2; color:#991b1b; padding:14px 18px; border-radius:16px; margin-bottom:25px; border:1px solid #fecaca; font-weight:500;'><i class='fa-solid fa-triangle-exclamation' style='margin-left:8px;'></i> يرجى اختيار صورة للمنتج.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إدارة المنتجات - لوحة التحكم</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .search-box {
            position: relative;
            width: 300px;
        }
        .search-box i {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }
        .search-box input {
            width: 100%;
            padding: 10px 40px 10px 15px;
            border: 1.5px solid #e2e8f0;
            border-radius: 20px;
            font-family: 'Tajawal', sans-serif;
            font-size: 14px;
            outline: none;
            transition: all 0.2s;
            background: #fafbfc;
        }
        .search-box input:focus {
            border-color: #38bdf8;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.1);
        }
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

         .form-group {
            margin-bottom: 22px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #334155;
            font-size: 15px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 14px 16px;
            border: 1.5px solid #e2e8f0;
            border-radius: 16px;
            box-sizing: border-box;
            font-family: 'Tajawal', sans-serif;
            font-size: 15px;
            background: #fafbfc;
            transition: all 0.2s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #38bdf8;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.1);
        }

        .btn-submit {
            background: linear-gradient(135deg, #38bdf8, #2dd4bf);
            color: #0f172a;
            border: none;
            padding: 14px 28px;
            cursor: pointer;
            border-radius: 40px;
            font-size: 16px;
            font-weight: 700;
            transition: all 0.3s;
            box-shadow: 0 8px 16px rgba(56, 189, 248, 0.2);
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 20px rgba(56, 189, 248, 0.3);
        }

         .products-table {
            width: 100%;
            border-collapse: collapse;
            text-align: right;
            margin-top: 15px;
        }

        .products-table th {
            background: #f8fafc;
            color: #475569;
            font-weight: 600;
            padding: 16px 12px;
            border-bottom: 2px solid #e2e8f0;
            font-size: 15px;
        }

        .products-table td {
            padding: 16px 12px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }

        .action-btn {
            padding: 8px 16px;
            border-radius: 30px;
            text-decoration: none;
            color: #fff;
            font-size: 14px;
            margin-left: 8px;
            display: inline-block;
            font-weight: 500;
            transition: all 0.2s;
        }

        .btn-edit {
            background: #3b82f6;
            box-shadow: 0 4px 8px rgba(59, 130, 246, 0.2);
        }

        .btn-edit:hover {
            background: #2563eb;
            transform: translateY(-1px);
        }

        .btn-delete {
            background: #ef4444;
            box-shadow: 0 4px 8px rgba(239, 68, 68, 0.2);
        }

        .btn-delete:hover {
            background: #dc2626;
            transform: translateY(-1px);
        }

        .badge {
            background: #e0f2fe;
            color: #0369a1;
            padding: 6px 14px;
            border-radius: 40px;
            font-size: 13px;
            font-weight: 600;
        }

         .ai-magic-btn {
            background: linear-gradient(135deg, #a78bfa, #c084fc);
            color: white;
            border: none;
            border-radius: 50%;
            width: 48px;
            height: 48px;
            font-size: 20px;
            cursor: pointer;
            box-shadow: 0 8px 16px rgba(167, 139, 250, 0.3);
            transition: all 0.3s ease;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .ai-magic-btn:hover {
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 12px 20px rgba(167, 139, 250, 0.4);
        }

         .ai-modal-overlay {
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

        .ai-modal-content {
            background: #ffffff;
            padding: 35px;
            border-radius: 32px;
            width: 480px;
            max-width: 90%;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
            position: relative;
        }

        .close-modal-btn {
            position: absolute;
            top: 20px;
            left: 20px;
            font-size: 24px;
            cursor: pointer;
            color: #94a3b8;
            transition: 0.2s;
        }

        .close-modal-btn:hover {
            color: #ef4444;
        }

        .ai-prompt-input {
            width: 100%;
            padding: 16px;
            border: 1.5px solid #e2e8f0;
            border-radius: 20px;
            margin-top: 20px;
            margin-bottom: 25px;
            font-family: 'Tajawal', sans-serif;
            resize: vertical;
            min-height: 120px;
            box-sizing: border-box;
            font-size: 15px;
            background: #fafbfc;
        }

        .ai-prompt-input:focus {
            outline: none;
            border-color: #a78bfa;
            background: white;
        }

        .btn-generate-ai {
            background: linear-gradient(135deg, #a78bfa, #c084fc);
            color: white;
            border: none;
            padding: 14px 20px;
            cursor: pointer;
            border-radius: 40px;
            font-size: 16px;
            font-weight: 700;
            width: 100%;
            transition: 0.3s;
        }

        .btn-generate-ai:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        .btn-generate-ai:disabled {
            background: #cbd5e1;
            cursor: not-allowed;
            opacity: 1;
            transform: none;
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
        <a href="manage_products.php" class="active"><i class="fa-solid fa-box-open"></i><span>إدارة المنتجات</span></a>
        <a href="manage_comments.php"><i class="fa-solid fa-comments"></i><span>تعليقات العملاء</span></a>
        <a href="manage_orders.php"><i class="fa-solid fa-cart-shopping"></i><span>طلبات الشراء</span></a>
        <a href="manage_messages.php"><i class="fa-solid fa-envelope"></i><span>رسائل الزوار</span></a>
        <a href="manage_users.php"><i class="fa-solid fa-users"></i><span>إدارة المستخدمين</span></a>
    </div>

    <div class="main-content">
        <div class="header">
            <h3><i class="fa-solid fa-box-open" style="margin-left: 12px; color: #38bdf8;"></i>إدارة المنتجات</h3>
            <a href="logout.php" class="logout-btn">
                <i class="fa-solid fa-right-from-bracket"></i>
                تسجيل الخروج
            </a>
        </div>

        <div class="content-area">
            <?php echo $msg; ?>
            
            <div class="card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <h2 style="margin-bottom: 0;"><i class="fa-solid fa-plus-circle"></i> إضافة منتج جديد</h2>
                    <button type="button" id="openAiModalBtn" class="ai-magic-btn" title="التعبئة التلقائية بالذكاء الاصطناعي">
                        <i class="fa-solid fa-wand-magic-sparkles"></i>
                    </button>
                </div>
                
                <form action="manage_products.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>اسم المنتج:</label>
                        <input type="text" name="title" id="product_title" required placeholder="أدخل اسم المنتج">
                    </div>
                    
                    <div class="form-group">
                        <label>القسم (Category):</label>
                        <select name="category_class" id="product_category" required>
                            <option value="">-- اختر القسم --</option>
                            <option value="هواتف">هواتف</option>
                            <option value="جهاز لوحي">جهاز لوحي (تابلت)</option>
                            <option value="لابتوب">لابتوب</option>
                            <option value="ساعات ذكية">ساعات ذكية</option>
                            <option value="فلاشات">فلاشات</option>
                            <option value="كاميرات">كاميرات</option>
                            <option value="راوترات">راوترات</option>
                            <option value="اكسسوارات">اكسسوارات</option>
                            <option value="مستعمل">مستعمل</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>السعر (بالجنيه):</label>
                        <input type="number" name="price" id="product_price" step="0.01" min="0" required placeholder="مثال: 45000">
                    </div>

                    <div class="form-group">
                        <label>الوصف التفصيلي للمنتج:</label>
                        <textarea name="description" id="product_description" rows="6" required placeholder="أدخل وصفاً تسويقياً وتفصيلياً للمنتج..."></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>صورة المنتج:</label>
                        <input type="file" name="image" accept="image/png, image/jpeg, image/gif, image/webp" required style="padding: 12px;">
                    </div>
                    
                    <button type="submit" name="add_product" class="btn-submit"><i class="fa-solid fa-plus" style="margin-left: 8px;"></i> إضافة المنتج</button>
                </form>
            </div>

            <div class="card">
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2 style="margin-top:0; margin-bottom:0;"><i class="fa-solid fa-list"></i> المنتجات الحالية</h2>
                    <div class="search-box">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" id="searchInput" placeholder="ابحث باسم المنتج هنا...">
                    </div>
                </div>                <table class="products-table">
                    <tr>
                        <th>الصورة</th>
                        <th>اسم المنتج</th>
                        <th>القسم</th>
                        <th>السعر</th>
                        <th>الإجراءات</th>
                    </tr>
                    <?php
                    $products_query = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");
                    
                    if (mysqli_num_rows($products_query) > 0) {
                        while ($row = mysqli_fetch_assoc($products_query)) {
                            $safe_image = htmlspecialchars($row['image_url']);
                            $safe_title = htmlspecialchars($row['title']);
                            $safe_category = htmlspecialchars($row['category_class']);
                            $safe_price = htmlspecialchars($row['price']);
                            
                            echo "<tr>";
                            echo "<td><img src='{$safe_image}' width='60' height='60' style='border-radius:12px; object-fit:cover; box-shadow:0 4px 6px rgba(0,0,0,0.05);'></td>";
                            echo "<td style='font-weight:500;'>{$safe_title}</td>";
                            echo "<td><span class='badge'>{$safe_category}</span></td>";
                            echo "<td style='font-weight:700; color:#0f172a;'>{$safe_price} ج.م</td>";
                            echo "<td>
                                    <a href='edit_product.php?id={$row['id']}' class='action-btn btn-edit'><i class='fa-solid fa-pen'></i> تعديل</a>
                                    <a href='manage_products.php?delete={$row['id']}' class='action-btn btn-delete' onclick='return confirm(\"هل أنت متأكد من حذف هذا المنتج نهائياً؟\");'><i class='fa-solid fa-trash'></i> حذف</a>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' style='text-align:center; padding:40px; color:#94a3b8; font-size:16px;'><i class='fa-solid fa-box-open' style='font-size:40px; margin-bottom:15px; opacity:0.5;'></i><br>لا توجد منتجات مضافة حتى الآن.</td></tr>";
                    }
                    ?>
                </table>
            </div>
        </div>
    </div>

    <div id="aiModal" class="ai-modal-overlay">
        <div class="ai-modal-content">
            <i class="fa-solid fa-xmark close-modal-btn" id="closeAiModalBtn"></i>
            
            <h3 style="margin-top: 0; color: #0f172a; display: flex; align-items: center; gap: 10px; font-size:22px;">
                <i class="fa-solid fa-robot" style="color: #a78bfa;"></i> المساعد الذكي
            </h3>
            
            <p style="color: #64748b; font-size: 15px; line-height: 1.6;">
                اكتب وصفاً مختصراً للمنتج، وسيقوم الذكاء الاصطناعي باقتراح الاسم والسعر والقسم ووصف تسويقي وتعبئتها تلقائياً.
            </p>
            
            <textarea id="aiPromptInput" class="ai-prompt-input" placeholder="مثال: لابتوب ديل انسبايرون كور اي 7 مستعمل بحالة جيدة..."></textarea>
            
            <button type="button" id="startAiBtn" class="btn-generate-ai">
                توليد البيانات الآن <i class="fa-solid fa-bolt"></i>
            </button>
        </div>
    </div>

    <script>
        const modal = document.getElementById('aiModal');
        const openBtn = document.getElementById('openAiModalBtn');
        const closeBtn = document.getElementById('closeAiModalBtn');
        
        const titleInput = document.getElementById('product_title');
        const priceInput = document.getElementById('product_price');
        const categorySelect = document.getElementById('product_category');
        const descriptionInput = document.getElementById('product_description');
        
        const aiPromptInput = document.getElementById('aiPromptInput');
        const startAiBtn = document.getElementById('startAiBtn');

        openBtn.addEventListener('click', function() { modal.style.display = 'flex'; });
        closeBtn.addEventListener('click', function() { modal.style.display = 'none'; });
        window.addEventListener('click', function(event) { if (event.target === modal) modal.style.display = 'none'; });

        startAiBtn.addEventListener('click', async function() {
            const promptText = aiPromptInput.value.trim();
            
            if(promptText === '') {
                alert('يرجى كتابة وصف للمنتج أولاً.');
                return;
            }

            const originalBtnText = startAiBtn.innerHTML;
            startAiBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> جاري المعالجة...';
            startAiBtn.disabled = true;

            try {
                const response = await fetch('ai_handler.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ prompt: promptText })
                });

                if (!response.ok) {
                    throw new Error('حدث خطأ في الاتصال بالخادم، رمز الخطأ: ' + response.status);
                }

                const data = await response.json();

                if (data.error) {
                    throw new Error(data.error);
                }

                if (data.title) titleInput.value = data.title;
                if (data.price) priceInput.value = data.price;
                
                if (data.category) {
                    for (let i = 0; i < categorySelect.options.length; i++) {
                        if (categorySelect.options[i].value === data.category) {
                            categorySelect.selectedIndex = i;
                            break;
                        }
                    }
                }
                
                if (data.description) descriptionInput.value = data.description;

                modal.style.display = 'none';
                aiPromptInput.value = '';

            } catch (error) {
                alert('عذراً، حدث خطأ: ' + error.message);
            } finally {
                startAiBtn.innerHTML = originalBtnText;
                startAiBtn.disabled = false;
            }
        });
        const searchInput = document.getElementById('searchInput');
        const productsTable = document.querySelector('.products-table');
        
        if (searchInput && productsTable) {
            const rows = productsTable.querySelectorAll('tr:not(:first-child)'); // تحديد جميع الصفوف ما عدا رأس الجدول
            
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();
                
                rows.forEach(row => {
                    const titleCell = row.cells[1]; // الخلية الثانية التي تحتوي على اسم المنتج
                    if (titleCell) {
                        const titleText = titleCell.textContent.toLowerCase();
                        // إخفاء أو إظهار الصف بناءً على المطابقة
                        if (titleText.includes(searchTerm)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    }
                });
            });
        }
    </script>
</body>
</html>