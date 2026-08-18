<?php
session_start();
include 'db.php';


if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}


$stmt = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");
$products = mysqli_fetch_all($stmt, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>لوحة التحكم | الإدارة</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --main-color: #1a8fb8; }
        body { font-family: Arial, sans-serif; background: #f8f9fa; margin: 0; padding: 20px; }
        .admin-container { max-width: 1100px; margin: auto; background: #fff; padding: 25px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .admin-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #eee; padding-bottom: 15px; margin-bottom: 25px; }
        .btn { padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: bold; display: inline-block; cursor: pointer; border: none; }
        .btn-add { background: var(--main-color); color: white; margin-bottom: 20px; }
        .btn-add:hover { background: #147396; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 15px; text-align: right; border-bottom: 1px solid #eee; }
        th { background: #f1f1f1; }
        .prod-img { width: 60px; height: 60px; object-fit: cover; border-radius: 8px; }
        .actions a { color: #666; font-size: 18px; margin: 0 5px; transition: 0.3s; }
        .actions .del-btn:hover { color: #dc3545; }
        .actions .edit-btn:hover { color: #28a745; }
    </style>
</head>
<body>

<div class="admin-container">
    <div class="admin-header">
        <h2><i class="fa-solid fa-user-shield"></i> مرحباً بك في لوحة الإدارة</h2>
        <a href="index.php" style="color: var(--main-color); text-decoration: none; font-weight: bold;">العودة للموقع <i class="fa-solid fa-arrow-left"></i></a>
    </div>

    <div class="stats-card">
        <p>إيميل المدير: <strong><?php echo $_SESSION['user_name']; ?></strong></p>
        <a href="add_product.php" class="btn btn-add"><i class="fa-solid fa-plus"></i> إضافة منتج جديد</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>الصورة</th>
                <th>اسم المنتج</th>
                <th>السعر</th>
                <th>القسم</th>
                <th>التحكم</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $p): ?>
            <tr>
                <td><img src="<?php echo $p['image_url']; ?>" class="prod-img"></td>
                <td><?php echo $p['title']; ?></td>
                <td><?php echo number_format($p['price'], 2); ?> ج.م</td>
                <td><?php echo $p['category_class']; ?></td>
                <td class="actions">
                    <a href="#" class="edit-btn"><i class="fa-solid fa-pen-to-square"></i></a>
                    <a href="delete_product.php?id=<?php echo $p['id']; ?>" class="del-btn" onclick="return confirm('هل أنت متأكد من حذف المنتج؟')"><i class="fa-solid fa-trash"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
            
            <?php if (empty($products)): ?>
                <tr>
                    <td colspan="5" style="text-align: center; padding: 40px; color: #888;">المخزن فاضي.. ضيف أول منتج دلوقتي!</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>