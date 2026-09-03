<!DOCTYPE html>
<?php 
$currentUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); 

function isActive($path) {
    $currentUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if ($path === '/admin') {
        return ($currentUri === '/admin') ? 'active' : '';
    }
    return ($currentUri === $path || strpos($currentUri, $path . '/') === 0) ? 'active' : '';
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
    .card { background: #fff; padding: 30px 35px; border-radius: 28px; box-shadow: 0 15px 30px -10px rgba(0,0,0,0.05); border: 1px solid #f1f5f9; margin-bottom: 35px; overflow-x: auto; }
    .card h2 { color: #0f172a; font-size: 24px; font-weight: 700; margin-bottom: 25px; display: flex; align-items: center; gap: 10px; }
    .card h2 i { color: #38bdf8; }
    table { width: 100%; border-collapse: collapse; text-align: right; margin-top: 15px; }
    th, td { padding: 16px 12px; border-bottom: 1px solid #e2e8f0; vertical-align: middle; }
    th { background: #f8fafc; color: #475569; font-weight: 600; border-bottom: 2px solid #e2e8f0; font-size: 15px; }
    .action-btn { padding: 8px 16px; border-radius: 30px; text-decoration: none; color: #fff; font-size: 14px; margin-left: 8px; display: inline-block; font-weight: 500; transition: all 0.2s; border: none; cursor: pointer; font-family: 'Tajawal', sans-serif; }
    .action-btn:hover { transform: translateY(-1px); }
    
    .btn-submit { background: linear-gradient(135deg, #38bdf8, #2dd4bf); color: #0f172a; border: none; padding: 14px 28px; cursor: pointer; border-radius: 40px; font-size: 16px; font-weight: 700; transition: all 0.3s; box-shadow: 0 8px 16px rgba(56,189,248,0.2); display: inline-block; text-decoration: none; }
    .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 12px 20px rgba(56,189,248,0.3); }
    .btn-update { background: #3b82f6; color: white; border: none; padding: 8px 14px; border-radius: 30px; cursor: pointer; font-weight: 500; font-size: 13px; transition: 0.2s; }
    .btn-update:hover { background: #2563eb; }
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
    .badge-success { background-color: #d1fae5; color: #065f46; padding: 6px 14px; border-radius: 50px; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 5px; }
    .badge-warning { background-color: #fef3c7; color: #92400e; padding: 6px 14px; border-radius: 50px; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 5px; }
    .main-content::-webkit-scrollbar { width: 8px; }
    .main-content::-webkit-scrollbar-track { background: #f1f5f9; }
    .main-content::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 20px; }
    .btn-ai-reply { background: linear-gradient(135deg, #a78bfa, #c084fc); color: white; border: none; padding: 10px 18px; cursor: pointer; border-radius: 40px; font-size: 14px; font-weight: 600; display: flex; align-items: center; gap: 8px; margin-bottom: 10px; transition: 0.3s; box-shadow: 0 4px 10px rgba(167,139,250,0.3); font-family: 'Tajawal', sans-serif; }
    .btn-ai-reply:hover { opacity: 0.9; transform: translateY(-1px); }
    .btn-ai-reply:disabled { background: #cbd5e1; cursor: not-allowed; opacity: 1; transform: none; box-shadow: none; }
    .date-badge { color: #64748b; font-size: 13px; font-weight: 500; display: inline-flex; align-items: center; gap: 6px; background: #f8fafc; padding: 6px 12px; border-radius: 8px; border: 1px solid #e2e8f0; white-space: nowrap; }
 
    /* Product & Modal Specific Styles */
    .ai-magic-btn { background: linear-gradient(135deg, #8b5cf6, #3b82f6); color: white; border: none; padding: 6px 14px; border-radius: 20px; cursor: pointer; font-weight: bold; font-size: 0.9rem; transition: 0.3s; display: inline-flex; align-items: center; gap: 5px; }
    .ai-magic-btn:hover { transform: scale(1.05); box-shadow: 0 4px 12px rgba(139, 92, 246, 0.4); }
    .btn-cancel { background: #e2e8f0; color: #475569; border: none; padding: 10px 20px; border-radius: 30px; cursor: pointer; font-weight: bold; transition: 0.2s; }
    .btn-cancel:hover { background: #cbd5e1; }
    .ai-modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 1100; justify-content: center; align-items: center; }
    .ai-modal-content { background: white; padding: 25px; border-radius: 12px; width: 90%; max-width: 400px; text-align: center; }

    /* Users Stats & Avatars */
    .stats-container { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 25px; }
    .stat-card { background: #fff; padding: 25px; border-radius: 20px; box-shadow: 0 10px 20px -5px rgba(0,0,0,0.03); display: flex; align-items: center; gap: 20px; }
    .user-avatar { width: 40px; height: 40px; border-radius: 50%; background: #38bdf8; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 16px; margin-left: 10px; flex-shrink: 0; }
    .user-avatar.banned { background: #ef4444; }

    /* Orders Details */
    .product-item { display: flex; align-items: center; border-bottom: 1px solid #e2e8f0; padding: 15px 0; }
    .product-item img { width: 60px; height: 60px; object-fit: cover; margin-left: 15px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
    .address-box { background: #f8fafc; padding: 18px; border-radius: 20px; margin-bottom: 20px; border: 1px solid #e2e8f0; }
    .status-select { padding: 8px 12px; border-radius: 30px; border: 1.5px solid #e2e8f0; font-family: 'Tajawal', sans-serif; font-size: 14px; background: #fafbfc; cursor: pointer; }
    .status-select:focus { outline: none; border-color: #38bdf8; }

    /* Settings Form */
    .form_row { margin-bottom: 20px; }
    .form_row label { display: block; font-weight: bold; margin-bottom: 8px; color: #334155; }
    .form_row input, .form_row textarea { width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 12px; font-family: inherit; font-size: 15px; box-sizing: border-box; }
    .form_row input:focus, .form_row textarea:focus { outline: none; border-color: #38bdf8; box-shadow: 0 0 0 4px rgba(56,189,248,0.1); }

    /* تقييد النصوص الطويلة فقط دون الضغط على الأزرار */
    td { word-wrap: break-word; overflow-wrap: break-word; }
    td:last-child { min-width: 210px; } /* ضمان مساحة كافية لعمود الأزرار */
    
    .message-content, .comment-text, .admin-reply-box { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis; white-space: normal; line-height: 1.6; }

    /* توحيد مقاسات جميع الأزرار في كل صفحات لوحة الإدارة */
    .actions-flex { display: flex; align-items: center; justify-content: flex-end; gap: 8px; flex-wrap: wrap; }
    .btn-reply, .btn-edit, .btn-view, .btn-delete, .btn-primary, .btn-ban, .btn-unban, .btn-success, .btn-danger { flex: 1; min-width: 95px; justify-content: center; color: white; border: none; padding: 8px 14px; border-radius: 8px; cursor: pointer; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; transition: 0.3s; text-decoration: none; white-space: nowrap; font-family: 'Tajawal', sans-serif; }
    
    .btn-primary, .btn-reply, .btn-edit, .btn-view { background: #3b82f6; box-shadow: 0 4px 8px rgba(59,130,246,0.15); }
    .btn-primary:hover, .btn-reply:hover, .btn-edit:hover, .btn-view:hover { background: #2563eb; transform: translateY(-1px); }
    
    .btn-danger, .btn-delete { background: #ef4444; box-shadow: 0 4px 8px rgba(239,68,68,0.15); }
    .btn-danger:hover, .btn-delete:hover { background: #dc2626; transform: translateY(-1px); }
    
    .btn-ban { background: #475569; box-shadow: 0 4px 8px rgba(71,85,105,0.15); }
    .btn-ban:hover { background: #334155; transform: translateY(-1px); }
    
    .btn-unban, .btn-success { background: #10b981; box-shadow: 0 4px 8px rgba(16,185,129,0.15); }
    .btn-unban:hover, .btn-success:hover { background: #059669; transform: translateY(-1px); }
  </style>
</head>
<body>
<div class="sidebar">
    <h2>مدير المتجر</h2>
    <a href="/admin" class="<?= isActive('/admin') ?>">
        <i class="fa-solid fa-house"></i><span>الرئيسية</span>
    </a>
    <a href="/admin/products" class="<?= isActive('/admin/products') ?>">
        <i class="fa-solid fa-box-open"></i><span>إدارة المنتجات</span>
    </a>
    <a href="/admin/orders" class="<?= isActive('/admin/orders') ?>">
        <i class="fa-solid fa-cart-shopping"></i><span>طلبات الشراء</span>
    </a>
    <a href="/admin/messages" class="<?= isActive('/admin/messages') ?>">
        <i class="fa-solid fa-envelope"></i><span>رسائل الزوار</span>
    </a>
    <a href="/admin/comments" class="<?= isActive('/admin/comments') ?>">
        <i class="fa-solid fa-comments"></i><span>تعليقات العملاء</span>
    </a>
    <a href="/admin/users" class="<?= isActive('/admin/users') ?>">
        <i class="fa-solid fa-users"></i><span>إدارة المستخدمين</span>
    </a>
    <a href="/admin/online-visitors" class="<?= isActive('/admin/online-visitors') ?>">
        <i class="fa-solid fa-globe"></i><span>الزوار المتصلين</span>
    </a>
    <a href="/admin/coupons" class="<?= isActive('/admin/coupons') ?>">
        <i class="fa-solid fa-tags"></i><span>الخصومات</span>
    </a>
    <a href="/admin/settings" class="<?= isActive('/admin/settings') ?>">
        <i class="fa-solid fa-gear"></i><span>الإعدادات</span>
    </a>
  </div>

  <div class="main-content">
    <div class="header">
      <h3><i class="fa-solid <?= $pageIcon ?? 'fa-cog' ?>" style="color:#38bdf8;"></i><?= $pageTitle ?? 'لوحة الإدارة' ?></h3>
      <a href="/logout" class="logout-btn"><i class="fa-solid fa-right-from-bracket"></i>تسجيل الخروج</a>
    </div>
    <div class="content-area">