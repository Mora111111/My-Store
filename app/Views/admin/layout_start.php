<!DOCTYPE html>
<?php 
// ده كود بيعرفنا إحنا في أي صفحة حالياً
$currentUri = $_SERVER['REQUEST_URI']; 

// دالة بسيطة بتشوف هل الرابط ده هو اللي إحنا واقفين عليه ولا لا
function isActive($path) {
    global $currentUri;
    return (strpos($currentUri, $path) !== false) ? 'active' : '';
}
?>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="<?= CSRF::generate() ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <title>لوحة الإدارة - MY Store</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Tajawal', sans-serif; background: #f8fafc; color: #1e293b; }
    .sidebar { width: 280px; background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%); color: #f1f5f9; padding: 30px 0; display: flex; flex-direction: column; position: fixed; top: 0; right: 0; height: 100vh; z-index: 10; box-shadow: -5px 0 20px rgba(0,0,0,0.08); }
    .sidebar h2 { text-align: center; margin-bottom: 40px; font-size: 26px; font-weight: 700; background: linear-gradient(135deg, #38bdf8, #2dd4bf); -webkit-background-clip: text; background-clip: text; color: transparent; padding: 0 15px; }
    .sidebar a { display: flex; align-items: center; color: #cbd5e1; padding: 14px 25px; margin: 4px 12px; text-decoration: none; border-radius: 12px; font-size: 16px; font-weight: 500; transition: all 0.3s; }
    .sidebar a i { margin-left: 15px; width: 24px; text-align: center; font-size: 18px; }
    .sidebar a:hover { background: rgba(56, 189, 248, 0.15); color: #fff; transform: translateX(-5px); }
    .sidebar a.active { background: linear-gradient(135deg, #38bdf8, #2dd4bf); color: #0f172a; font-weight: 700; box-shadow: 0 8px 16px rgba(56, 189, 248, 0.3); }
    .main-content { margin-right: 280px; min-height: 100vh; background: #f1f5f9; }
    .header { background: #fff; padding: 20px 40px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; }
    .header h3 { font-size: 24px; font-weight: 700; color: #0f172a; display: flex; align-items: center; }
    .header h3 i { margin-left: 12px; }
    .logout-btn { background: #fff; color: #ef4444; border: 1.5px solid #fee2e2; padding: 12px 24px; border-radius: 40px; text-decoration: none; font-weight: 600; font-size: 15px; display: flex; align-items: center; gap: 8px; transition: all 0.25s; }
    .logout-btn:hover { background: #ef4444; color: #fff; border-color: #ef4444; }
    .content-area { padding: 35px 40px; }
    .card { background: #fff; padding: 30px 35px; border-radius: 28px; box-shadow: 0 15px 30px -10px rgba(0,0,0,0.05); border: 1px solid #f1f5f9; margin-bottom: 35px; }
    .card h2 { color: #0f172a; font-size: 24px; font-weight: 700; margin-bottom: 25px; display: flex; align-items: center; gap: 10px; }
    .card h2 i { color: #38bdf8; }
    table { width: 100%; border-collapse: collapse; text-align: right; margin-top: 15px; }
    th, td { padding: 16px 12px; border-bottom: 1px solid #e2e8f0; vertical-align: middle; }
    th { background: #f8fafc; color: #475569; font-weight: 600; border-bottom: 2px solid #e2e8f0; font-size: 15px; }
    .action-btn { padding: 8px 16px; border-radius: 30px; text-decoration: none; color: #fff; font-size: 14px; margin-left: 8px; display: inline-block; font-weight: 500; transition: all 0.2s; border: none; cursor: pointer; font-family: 'Tajawal', sans-serif; }
    .action-btn:hover { transform: translateY(-1px); }
    .btn-edit { background: #3b82f6; box-shadow: 0 4px 8px rgba(59,130,246,0.2); }
    .btn-edit:hover { background: #2563eb; }
    .btn-delete { background: #ef4444; box-shadow: 0 4px 8px rgba(239,68,68,0.2); }
    .btn-delete:hover { background: #dc2626; }
    .btn-submit { background: linear-gradient(135deg, #38bdf8, #2dd4bf); color: #0f172a; border: none; padding: 14px 28px; cursor: pointer; border-radius: 40px; font-size: 16px; font-weight: 700; transition: all 0.3s; box-shadow: 0 8px 16px rgba(56,189,248,0.2); display: inline-block; text-decoration: none; }
    .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 12px 20px rgba(56,189,248,0.3); }
    .btn-update { background: #3b82f6; color: white; border: none; padding: 8px 14px; border-radius: 30px; cursor: pointer; font-weight: 500; font-size: 13px; transition: 0.2s; }
    .btn-update:hover { background: #2563eb; }
    .btn-view { background: #38bdf8; color: #0f172a; border: none; padding: 8px 16px; border-radius: 30px; cursor: pointer; text-decoration: none; display: inline-block; font-weight: 500; font-size: 13px; transition: 0.2s; }
    .btn-view:hover { background: #2dd4bf; }
    .btn-danger { background: #ef4444; color: #fff; box-shadow: 0 4px 6px rgba(239,68,68,0.2); text-decoration: none; display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; border-radius: 30px; border: none; cursor: pointer; font-weight: 600; transition: 0.2s; font-family: 'Tajawal'; }
    .btn-success { background: #10b981; color: #fff; display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; border-radius: 30px; border: none; cursor: pointer; font-weight: 600; transition: 0.2s; font-family: 'Tajawal'; }
    .modal-overlay, .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); backdrop-filter: blur(4px); justify-content: center; align-items: center; }
    .modal-content { background: #fff; padding: 35px; border-radius: 32px; width: 520px; max-width: 90%; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); position: relative; }
    .close-modal { position: absolute; top: 20px; left: 20px; font-size: 24px; cursor: pointer; color: #94a3b8; transition: 0.2s; }
    .close-modal:hover { color: #ef4444; }
    .form-group { margin-bottom: 22px; }
    .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #334155; font-size: 15px; }
    .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 14px 16px; border: 1.5px solid #e2e8f0; border-radius: 16px; box-sizing: border-box; font-family: 'Tajawal', sans-serif; font-size: 15px; background: #fafbfc; transition: all 0.2s; }
    .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: #38bdf8; background: #fff; box-shadow: 0 0 0 4px rgba(56,189,248,0.1); }
    .status-badge { padding: 6px 14px; border-radius: 40px; font-size: 13px; font-weight: 600; display: inline-block; }
    .badge { background: #e0f2fe; color: #0369a1; padding: 6px 14px; border-radius: 40px; font-size: 13px; font-weight: 600; }
    .badge-success { background: #d1fae5; color: #065f46; padding: 6px 14px; border-radius: 40px; font-size: 13px; font-weight: 600; }
    .badge-warning { background: #fef3c7; color: #92400e; padding: 6px 14px; border-radius: 40px; font-size: 13px; font-weight: 600; }
    .main-content::-webkit-scrollbar { width: 8px; }
    .main-content::-webkit-scrollbar-track { background: #f1f5f9; }
    .main-content::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 20px; }
  </style>
</head>
<body>
<div class="sidebar">
    <h2>مدير المتجر</h2>
    <a href="/admin" class="<?php echo ($currentUri === '/admin') ? 'active' : ''; ?>">
        <i class="fa-solid fa-house"></i><span>الرئيسية</span>
    </a>
    <a href="/admin/products" class="<?php echo isActive('/admin/products'); ?>">
        <i class="fa-solid fa-box-open"></i><span>إدارة المنتجات</span>
    </a>
    <a href="/admin/orders" class="<?php echo isActive('/admin/orders'); ?>">
        <i class="fa-solid fa-cart-shopping"></i><span>طلبات الشراء</span>
    </a>
    <a href="/admin/messages" class="<?php echo isActive('/admin/messages'); ?>">
        <i class="fa-solid fa-envelope"></i><span>رسائل الزوار</span>
    </a>
    <a href="/admin/comments" class="<?php echo isActive('/admin/comments'); ?>">
        <i class="fa-solid fa-comments"></i><span>تعليقات العملاء</span>
    </a>
    <a href="/admin/users" class="<?php echo isActive('/admin/users'); ?>">
        <i class="fa-solid fa-users"></i><span>إدارة المستخدمين</span>
    </a>
    <a href="/admin/settings" class="<?php echo isActive('/admin/settings'); ?>">
        <i class="fa-solid fa-gear"></i><span>الإعدادات</span>
    </a>
  </div>

  <div class="main-content">
    <div class="header">
      <h3><i class="fa-solid <?= $pageIcon ?? 'fa-cog' ?>" style="color:#38bdf8;"></i><?= $pageTitle ?? 'لوحة الإدارة' ?></h3>
      <a href="/logout" class="logout-btn"><i class="fa-solid fa-right-from-bracket"></i>تسجيل الخروج</a>
    </div>
    <div class="content-area">
