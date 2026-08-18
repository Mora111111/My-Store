<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php"); 
    exit();
}

require_once 'db.php';

$products_query = mysqli_query($conn, "SELECT COUNT(*) as count FROM products");
$products_count = mysqli_fetch_assoc($products_query)['count'];

$orders_query = mysqli_query($conn, "SELECT COUNT(*) as count FROM orders");
$orders_count = mysqli_fetch_assoc($orders_query)['count'];

$messages_query = mysqli_query($conn, "SELECT COUNT(*) as count FROM contact_messages");
$messages_count = mysqli_fetch_assoc($messages_query)['count'];

$comments_query = mysqli_query($conn, "SELECT COUNT(*) as count FROM product_comments");
$comments_count = mysqli_fetch_assoc($comments_query)['count'];

$first_name = explode(' ', $_SESSION['user_name'])[0];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>لوحة تحكم الإدارة - الرئيسية</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
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

        .stats-container {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 25px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: #ffffff;
            padding: 28px 20px;
            border-radius: 24px;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05), 0 8px 10px -6px rgba(0,0,0,0.02);
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            border: 1px solid #f1f5f9;
            position: relative;
            overflow: hidden;
        }

        .stat-card::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 6px;
            height: 100%;
            background: currentColor;
            opacity: 0.7;
            border-radius: 6px 0 0 6px;
        }

        .stat-card:nth-child(1) { color: #10b981; }   
        .stat-card:nth-child(2) { color: #3b82f6; }  
        .stat-card:nth-child(3) { color: #f59e0b; }      
        .stat-card:nth-child(4) { color: #ef4444; }  

        .stat-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 25px 30px -12px rgba(0,0,0,0.15);
            border-color: #e2e8f0;
        }

        .stat-info h3 {
            margin: 0 0 10px 0;
            color: #64748b;
            font-size: 16px;
            font-weight: 600;
            letter-spacing: -0.2px;
            text-transform: uppercase;
        }

        .stat-info p {
            margin: 0;
            font-size: 42px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.2;
        }

        .stat-icon {
            font-size: 48px;
            opacity: 0.85;
            transition: 0.3s;
            color: currentColor;
            filter: drop-shadow(0 4px 6px rgba(0,0,0,0.05));
        }

        .stat-card:hover .stat-icon {
            transform: scale(1.05);
            opacity: 1;
        }

        .welcome-card {
            background: #ffffff;
            padding: 35px 40px;
            border-radius: 28px;
            box-shadow: 0 15px 30px -10px rgba(0,0,0,0.05);
            border: 1px solid #f1f5f9;
            transition: 0.2s;
        }

        .welcome-card h2 {
            margin-top: 0;
            color: #0f172a;
            font-size: 30px;
            font-weight: 700;
            margin-bottom: 20px;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .welcome-card h2::after {
            content: '';
            flex: 1;
            height: 3px;
            background: linear-gradient(90deg, #38bdf8, transparent);
            border-radius: 10px;
        }

        .welcome-card p {
            color: #334155;
            font-size: 18px;
            margin-bottom: 25px;
            line-height: 1.7;
            font-weight: 500;
        }

        .welcome-card ul {
            list-style: none;
            padding: 0;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 18px 25px;
        }

        .welcome-card li {
            color: #1e293b;
            font-size: 17px;
            padding: 8px 0;
            border-bottom: 1px dashed #e2e8f0;
            display: flex;
            align-items: center;
        }

        .welcome-card li::before {
            content: "✨";
            margin-left: 12px;
            font-size: 18px;
            opacity: 0.9;
        }

        .welcome-card li strong {
            color: #0f172a;
            font-weight: 700;
            margin-left: 5px;
        }

        @media (max-width: 1200px) {
            .stats-container {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 90px;
                padding: 20px 0;
            }
            .sidebar h2 {
                font-size: 0;
            }
            .sidebar h2::before {
                content: "MT";
                font-size: 24px;
                font-weight: bold;
                color: #38bdf8;
            }
            .sidebar a span {
                display: none;
            }
            .sidebar a i {
                margin-left: 0;
                font-size: 24px;
            }
            .sidebar a {
                justify-content: center;
                padding: 16px 0;
            }
            .main-content {
                width: calc(100% - 90px);
            }
            .content-area {
                padding: 25px 20px;
            }
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
        <a href="dashboard.php" class="active">
            <i class="fa-solid fa-house"></i>
            <span>الرئيسية</span>
        </a>
        <a href="manage_products.php">
            <i class="fa-solid fa-box-open"></i>
            <span>إدارة المنتجات</span>
        </a>
        <a href="manage_comments.php">
            <i class="fa-solid fa-comments"></i>
            <span>تعليقات العملاء</span>
        </a>
        <a href="manage_orders.php">
            <i class="fa-solid fa-cart-shopping"></i>
            <span>طلبات الشراء</span>
        </a>
        <a href="manage_messages.php">
            <i class="fa-solid fa-envelope"></i>
            <span>رسائل الزوار</span>
        </a>
                <a href="manage_users.php">
            <i class="fa-solid fa-users"></i>
            <span>إدارة المستخدمين</span>
        </a>

    </div>

    <div class="main-content">
        <div class="header">
            <h3>لوحة التحكم - نظرة عامة</h3>
            <a href="logout.php" class="logout-btn">
                <i class="fa-solid fa-right-from-bracket"></i>
                تسجيل الخروج
            </a>
        </div>

        <div class="content-area">
            
            <div class="stats-container">
                <div class="stat-card">
                    <div class="stat-info">
                        <h3>إجمالي المنتجات</h3>
                        <p><?php echo $products_count; ?></p>
                    </div>
                    <i class="fa-solid fa-boxes-stacked stat-icon"></i>
                </div>
                
                <div class="stat-card">
                    <div class="stat-info">
                        <h3>طلبات الشراء</h3>
                        <p><?php echo $orders_count; ?></p>
                    </div>
                    <i class="fa-solid fa-bag-shopping stat-icon"></i>
                </div>

                <div class="stat-card">
                    <div class="stat-info">
                        <h3>تعليقات العملاء</h3>
                        <p><?php echo $comments_count; ?></p>
                    </div>
                    <i class="fa-solid fa-comment-dots stat-icon"></i>
                </div>
                
                <div class="stat-card">
                    <div class="stat-info">
                        <h3>رسائل الزوار</h3>
                        <p><?php echo $messages_count; ?></p>
                    </div>
                    <i class="fa-solid fa-envelope-open-text stat-icon"></i>
                </div>
            </div>

            <div class="welcome-card">
                <h2>متابعة المتجر<?php echo htmlspecialchars($first_name); ?> 🛒</h2>
                <p> شاشة الإحصائيات السريعة الخاصة بالمتجر يمكنك من خلال القائمة الجانبية التحكم الكامل في كل أجزاء الموقع :</p>
                <ul>
                    <li><strong>إدارة المنتجات:</strong> إضافة منتجات جديدة  وتعديل أو حذف المنتجات الحالية.</li>
                    <li><strong>تعليقات العملاء:</strong> متابعة آراء العملاء على المنتجات والرد عليها باحترافية.</li>
                    <li><strong>طلبات الشراء:</strong> متابعة الطلبات الجديدة التي قام بها العملاء وتجهيزها.</li>
                    <li><strong>رسائل الزوار:</strong> قراءة استفسارات ورسائل العملاء الواردة من صفحة "اتصل بنا".</li>
                    <li><strong>إدارة المستخدمين:</strong> "إمكانية التحكم في الحسابات "حظر-فك الحظر-تحويل لمدير".</li>

                </ul>
            </div>

        </div>
    </div>

</body>
</html>