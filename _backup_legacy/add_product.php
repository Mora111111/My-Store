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
                    $msg = "<div style='background-color:#d4edda; color:#155724; padding:10px; border-radius:5px; margin-bottom:15px;'><i class='fa-solid fa-check-circle'></i> تمت إضافة المنتج بنجاح!</div>";
                } else {
                    $msg = "<div style='background-color:#f8d7da; color:#721c24; padding:10px; border-radius:5px; margin-bottom:15px;'><i class='fa-solid fa-triangle-exclamation'></i> حدث خطأ في قاعدة البيانات.</div>";
                    unlink($image_url);
                }
            } else {
                $msg = "<div style='background-color:#f8d7da; color:#721c24; padding:10px; border-radius:5px; margin-bottom:15px;'><i class='fa-solid fa-triangle-exclamation'></i> حدث خطأ أثناء رفع الصورة.</div>";
            }
        } else {
            $msg = "<div style='background-color:#f8d7da; color:#721c24; padding:10px; border-radius:5px; margin-bottom:15px;'><i class='fa-solid fa-triangle-exclamation'></i> عذراً، الملف المرفوع ليس صورة صالحة.</div>";
        }
    } else {
        $msg = "<div style='background-color:#f8d7da; color:#721c24; padding:10px; border-radius:5px; margin-bottom:15px;'><i class='fa-solid fa-triangle-exclamation'></i> يرجى اختيار صورة للمنتج.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إدارة المنتجات</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background-color: #f4f4f4; display: flex; height: 100vh; overflow: hidden; }
        .sidebar { width: 250px; background-color: #2c3e50; color: #fff; padding-top: 20px; display: flex; flex-direction: column; }
        .sidebar h2 { text-align: center; margin-bottom: 30px; color: #18bc9c; font-size: 24px; }
        .sidebar a { display: block; color: #ecf0f1; padding: 15px 20px; text-decoration: none; border-bottom: 1px solid #34495e; transition: 0.3s; font-size: 16px; }
        .sidebar a i { margin-left: 10px; width: 20px; text-align: center; }
        .sidebar a:hover, .sidebar a.active { background-color: #1abc9c; color: #fff; padding-right: 30px; }
        .main-content { flex: 1; overflow-y: auto; display: flex; flex-direction: column; }
        .header { background-color: #fff; padding: 15px 30px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; }
        .content-area { padding: 30px; }
        .card { background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; }
        /* تمت إضافة textarea هنا للتنسيق */
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; font-family: inherit; }
        .btn-submit { background-color: #1abc9c; color: white; border: none; padding: 10px 20px; cursor: pointer; border-radius: 5px; font-size: 16px; transition: 0.3s; font-weight: bold; }
        .btn-submit:hover { background-color: #16a085; }
        .logout-btn { background-color: #e74c3c; color: white; border: none; padding: 10px 20px; cursor: pointer; border-radius: 5px; text-decoration: none; font-weight: bold; transition: 0.3s; }
        .logout-btn:hover { background-color: #c0392b; }
        .products-table { width: 100%; border-collapse: collapse; text-align: right; margin-top: 20px; }
        .products-table th, .products-table td { padding: 12px; border-bottom: 1px solid #dee2e6; }
        .products-table th { background-color: #f8f9fa; color: #333; }
        .action-btn { padding: 6px 12px; border-radius: 4px; text-decoration: none; color: #fff; font-size: 14px; margin-left: 5px; display:inline-block;}
        .btn-edit { background-color: #3498db; }
        .btn-delete { background-color: #e74c3c; }
        .badge { background-color: #17a2b8; color: white; padding: 4px 8px; border-radius: 12px; font-size: 12px; }

        .ai-magic-btn { background: linear-gradient(135deg, #6e8efb, #a777e3); color: white; border: none; border-radius: 50%; width: 45px; height: 45px; font-size: 20px; cursor: pointer; box-shadow: 0 4px 15px rgba(167, 119, 227, 0.4); transition: transform 0.3s ease, box-shadow 0.3s ease; display: flex; justify-content: center; align-items: center; }
        .ai-magic-btn:hover { transform: scale(1.1) rotate(5deg); box-shadow: 0 6px 20px rgba(167, 119, 227, 0.6); }
        .ai-modal-overlay { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.6); justify-content: center; align-items: center; }
        .ai-modal-content { background-color: #fff; padding: 30px; border-radius: 12px; width: 450px; max-width: 90%; box-shadow: 0 10px 25px rgba(0,0,0,0.2); position: relative; }
        .close-modal-btn { position: absolute; top: 15px; left: 15px; font-size: 22px; cursor: pointer; color: #95a5a6; transition: 0.3s; }
        .close-modal-btn:hover { color: #e74c3c; }
        .ai-prompt-input { width: 100%; padding: 15px; border: 2px solid #ecf0f1; border-radius: 8px; margin-top: 15px; margin-bottom: 20px; font-family: inherit; resize: vertical; min-height: 100px; box-sizing: border-box; font-size: 15px; }
        .ai-prompt-input:focus { outline: none; border-color: #a777e3; }
        .btn-generate-ai { background: linear-gradient(135deg, #6e8efb, #a777e3); color: white; border: none; padding: 12px 20px; cursor: pointer; border-radius: 8px; font-size: 16px; font-weight: bold; width: 100%; transition: 0.3s; }
        .btn-generate-ai:hover { opacity: 0.9; }
        .btn-generate-ai:disabled { background: #bdc3c7; cursor: not-allowed; opacity: 1; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>مدير المتجر</h2>
        <a href="dashboard.php"><i class="fa-solid fa-house"></i> الرئيسية</a>
        <a href="manage_products.php" class="active"><i class="fa-solid fa-box-open"></i> إدارة المنتجات</a>
        <a href="manage_orders.php"><i class="fa-solid fa-cart-shopping"></i> طلبات الشراء</a>
        <a href="manage_messages.php"><i class="fa-solid fa-envelope"></i> رسائل الزوار</a>
    </div>

    <div class="main-content">
        <div class="header">
            <h3><i class="fa-solid fa-box-open"></i> إدارة المنتجات</h3>
            <a href="logout.php" class="logout-btn"><i class="fa-solid fa-right-from-bracket"></i> تسجيل الخروج</a>
        </div>

        <div class="content-area">
            <?php echo $msg; ?>
            
            <div class="card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2 style="margin-top:0; margin-bottom:0; color: #2c3e50;"><i class="fa-solid fa-plus-circle"></i> إضافة منتج جديد</h2>
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
                        <input type="file" name="image" accept="image/png, image/jpeg, image/gif, image/webp" required>
                    </div>
                    
                    <button type="submit" name="add_product" class="btn-submit"><i class="fa-solid fa-plus"></i> إضافة المنتج</button>
                </form>
            </div>

            <div class="card">
                <h2 style="margin-top:0; color: #2c3e50;"><i class="fa-solid fa-list"></i> المنتجات الحالية</h2>
                <table class="products-table">
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
                            echo "<td><img src='{$safe_image}' width='60' height='60' style='border-radius:5px; object-fit:cover;'></td>";
                            echo "<td>{$safe_title}</td>";
                            echo "<td><span class='badge'>{$safe_category}</span></td>";
                            echo "<td>{$safe_price} ج.م</td>";
                            echo "<td>
                                    <a href='edit_product.php?id={$row['id']}' class='action-btn btn-edit'><i class='fa-solid fa-pen'></i> تعديل</a>
                                    <a href='manage_products.php?delete={$row['id']}' class='action-btn btn-delete' onclick='return confirm(\"هل أنت متأكد من حذف هذا المنتج نهائياً؟\");'><i class='fa-solid fa-trash'></i> حذف</a>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' style='text-align:center; color:#777;'>لا توجد منتجات مضافة حتى الآن.</td></tr>";
                    }
                    ?>
                </table>
            </div>
        </div>
    </div>

    <div id="aiModal" class="ai-modal-overlay">
        <div class="ai-modal-content">
            <i class="fa-solid fa-xmark close-modal-btn" id="closeAiModalBtn"></i>
            <h3 style="margin-top: 0; color: #2c3e50; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-robot" style="color: #6e8efb;"></i> المساعد الذكي
            </h3>
            <p style="color: #7f8c8d; font-size: 14px; line-height: 1.5;">
                اكتب وصفاً مختصراً للمنتج، وسيقوم الذكاء الاصطناعي بتوليد (الاسم، السعر، القسم، <b>ووصف تسويقي تفصيلي</b>) تلقائياً.
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
            if(promptText === '') { alert('يرجى كتابة وصف للمنتج أولاً.'); return; }

            const originalBtnText = startAiBtn.innerHTML;
            startAiBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> جاري المعالجة...';
            startAiBtn.disabled = true;

            try {
                const response = await fetch('ai_handler.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ prompt: promptText })
                });

                if (!response.ok) throw new Error('خطأ في الاتصال: ' + response.status);
                const data = await response.json();
                if (data.error) throw new Error(data.error);

                if (data.title) titleInput.value = data.title;
                if (data.price) priceInput.value = data.price;
                if (data.category) {
                    for (let i = 0; i < categorySelect.options.length; i++) {
                        if (categorySelect.options[i].value === data.category) {
                            categorySelect.selectedIndex = i; break;
                        }
                    }
                }
                if (data.description) {
                    descriptionInput.value = data.description;
                }

                modal.style.display = 'none';
                aiPromptInput.value = '';

            } catch (error) {
                alert('عذراً، حدث خطأ: ' + error.message);
            } finally {
                startAiBtn.innerHTML = originalBtnText;
                startAiBtn.disabled = false;
            }
        });
    </script>
</body>
</html>