<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}
require_once 'db.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

$msg_type = $msg_text = "";

$check_ban_col = mysqli_query($conn, "SHOW COLUMNS FROM elogin LIKE 'is_banned'");
if(mysqli_num_rows($check_ban_col) == 0) {
    mysqli_query($conn, "ALTER TABLE elogin ADD COLUMN is_banned TINYINT(1) DEFAULT 0");
}

$columns = [];
$res_cols = mysqli_query($conn, "SHOW COLUMNS FROM elogin");
if($res_cols) {
    while($c = mysqli_fetch_assoc($res_cols)) {
        $columns[strtolower($c['Field'])] = $c['Field'];
    }
}
$col_role = $columns['role'] ?? 'role';
$col_email = $columns['email'] ?? 'email';
$col_pass = $columns['password'] ?? $columns['pass'] ?? 'password';

if (isset($_GET['delete'])) {
    $del_id = (int)$_GET['delete'];
    if ($del_id !== $_SESSION['user_id']) {
        if(mysqli_query($conn, "DELETE FROM elogin WHERE id = $del_id")){
            $msg_type = "success"; $msg_text = "تم حذف المستخدم بنجاح.";
        } else {
            $msg_type = "error"; $msg_text = "خطأ في الحذف: " . mysqli_error($conn);
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_user'])) {
    $fname = mysqli_real_escape_string($conn, $_POST['fname']);
    $lname = mysqli_real_escape_string($conn, $_POST['lname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    
    $check = mysqli_query($conn, "SELECT id FROM elogin WHERE $col_email = '$email'");
    if($check && mysqli_num_rows($check) > 0){
        $msg_type = "error";
        $msg_text = "البريد الإلكتروني مستخدم بالفعل.";
    } else {
        $insert_cols = [$col_email, $col_pass, $col_role];
        $insert_vals = ["'$email'", "'$pass'", "'$role'"];
        
        if (isset($columns['fname']) && isset($columns['lname'])) {
            $insert_cols[] = $columns['fname']; $insert_vals[] = "'$fname'";
            $insert_cols[] = $columns['lname']; $insert_vals[] = "'$lname'";
        } elseif (isset($columns['first_name']) && isset($columns['last_name'])) {
            $insert_cols[] = $columns['first_name']; $insert_vals[] = "'$fname'";
            $insert_cols[] = $columns['last_name']; $insert_vals[] = "'$lname'";
        } elseif (isset($columns['name'])) {
            $insert_cols[] = $columns['name']; $insert_vals[] = "'" . $fname . " " . $lname . "'";
        } else {
            $insert_cols[] = 'Fname'; $insert_vals[] = "'$fname'";
            $insert_cols[] = 'Lname'; $insert_vals[] = "'$lname'";
        }

        $cols_str = implode(", ", $insert_cols);
        $vals_str = implode(", ", $insert_vals);
        
        if(mysqli_query($conn, "INSERT INTO elogin ($cols_str) VALUES ($vals_str)")){
            $msg_type = "success"; $msg_text = "تم إضافة المستخدم بنجاح.";
        } else {
            $msg_type = "error"; $msg_text = "خطأ في الإضافة: " . mysqli_error($conn);
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_role'])) {
    $u_id = (int)$_POST['user_id'];
    $new_role = mysqli_real_escape_string($conn, $_POST['new_role']);
    if ($u_id !== $_SESSION['user_id']) {
        if(mysqli_query($conn, "UPDATE elogin SET $col_role = '$new_role' WHERE id = $u_id")){
            $msg_type = "success"; $msg_text = "تم تحديث صلاحية المستخدم.";
        } else {
            $msg_type = "error"; $msg_text = "خطأ في التعديل: " . mysqli_error($conn);
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['toggle_ban'])) {
    $u_id = (int)$_POST['user_id'];
    $current_status = (int)$_POST['current_status'];
    $new_status = $current_status ? 0 : 1;
    if ($u_id !== $_SESSION['user_id']) {
        if(mysqli_query($conn, "UPDATE elogin SET is_banned = $new_status WHERE id = $u_id")){
            $msg_type = "success"; 
            $msg_text = $new_status ? "تم حظر المستخدم بنجاح. لن يتمكن من تسجيل الدخول." : "تم فك الحظر عن المستخدم.";
        } else {
            $msg_type = "error"; $msg_text = "خطأ في تنفيذ الإجراء: " . mysqli_error($conn);
        }
    }
}

$stats = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total, SUM(IF($col_role='admin', 1, 0)) as admins FROM elogin"));
$totalUsers = $stats['total'] ?? 0;
$totalAdmins = $stats['admins'] ?? 0;

$result = mysqli_query($conn, "SELECT * FROM elogin ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إدارة المستخدمين - لوحة التحكم</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: 'Tajawal', sans-serif; background: #f8fafc; display:flex; height:100vh; overflow:hidden; color:#1e293b; }
        .sidebar { width:280px; background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%); color:#f1f5f9; padding:30px 0; display:flex; flex-direction:column; box-shadow:5px 0 20px rgba(0,0,0,0.08); }
        .sidebar h2 { text-align:center; margin-bottom:40px; font-size:26px; font-weight:700; background: linear-gradient(135deg, #38bdf8, #2dd4bf); -webkit-background-clip:text; background-clip:text; color:transparent; }
        .sidebar a { display:flex; align-items:center; color:#cbd5e1; padding:14px 25px; margin:4px 12px; text-decoration:none; border-radius:12px; transition:0.3s; font-size:16px; font-weight:500; }
        .sidebar a i { margin-left:15px; width:24px; text-align:center; font-size:18px; }
        .sidebar a:hover { background: rgba(56,189,248,0.15); color:#fff; transform:translateX(-5px); }
        .sidebar a.active { background: linear-gradient(135deg, #38bdf8, #2dd4bf); color:#0f172a; font-weight:700; }
        .main-content { flex:1; overflow-y:auto; background:#f1f5f9; }
        .header { background:#fff; padding:20px 40px; box-shadow:0 4px 20px rgba(0,0,0,0.02); display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #e2e8f0; }
        .header h3 { font-size:24px; font-weight:700; color:#0f172a; }
        .logout-btn { background:#fff; color:#ef4444; border:1.5px solid #fee2e2; padding:12px 24px; border-radius:40px; text-decoration:none; font-weight:600; transition:0.25s; }
        .logout-btn:hover { background:#ef4444; color:#fff; }
        .content-area { padding:35px 40px; }
        .card { background:#fff; padding:30px 35px; border-radius:28px; box-shadow:0 15px 30px -10px rgba(0,0,0,0.05); border:1px solid #f1f5f9; margin-bottom:25px; }
        .stats-container { display:grid; grid-template-columns:repeat(2,1fr); gap:20px; margin-bottom:25px; }
        .stat-card { background:#fff; padding:25px; border-radius:20px; box-shadow:0 10px 20px -5px rgba(0,0,0,0.03); display:flex; align-items:center; gap:20px; }
        table { width:100%; border-collapse:collapse; }
        th, td { padding:16px; text-align:right; border-bottom:1px solid #e2e8f0; vertical-align:middle; }
        th { background:#f8fafc; color:#475569; font-weight:600; }
        .btn { padding:8px 16px; border-radius:30px; border:none; cursor:pointer; font-weight:600; transition:0.2s; display:inline-flex; align-items:center; gap:8px; font-family: 'Tajawal'; }
        .btn-primary { background:#3b82f6; color:#fff; box-shadow:0 4px 6px rgba(59,130,246,0.2); }
        .btn-danger { background:#ef4444; color:#fff; box-shadow:0 4px 6px rgba(239,68,68,0.2); text-decoration:none; }
        .btn-success { background:#10b981; color:#fff; }
        .btn-ban { background:#475569; color:#fff; }
        .btn-unban { background:#10b981; color:#fff; }
        
        .toast-container { position:fixed; top:100px; left:40px; z-index:9999; }
        .toast { min-width:280px; padding:16px 20px; border-radius:16px; margin-bottom:10px; display:flex; align-items:center; gap:12px; box-shadow:0 15px 30px -8px rgba(0,0,0,0.2); animation:slideIn 0.3s ease; backdrop-filter:blur(10px); }
        .toast.success { background:#10b981; color:#fff; }
        .toast.error { background:#ef4444; color:#fff; }
        @keyframes slideIn { from { opacity:0; transform:translateX(-20px); } to { opacity:1; transform:translateX(0); } }

        .modal-overlay { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); backdrop-filter:blur(5px); z-index:1000; align-items:center; justify-content:center; }
        .modal { background:#fff; border-radius:32px; padding:30px; width:450px; max-width:90%; }
        .form-group { margin-bottom:20px; }
        .form-group label { display:block; margin-bottom:8px; font-weight:600; color:#334155; }
        .form-control { width:100%; padding:12px 16px; border:2px solid #e2e8f0; border-radius:16px; font-family:'Tajawal'; font-size:15px; transition:0.2s; }
        .form-control:focus { outline:none; border-color:#38bdf8; box-shadow:0 0 0 4px rgba(56,189,248,0.1); }
        
        .user-avatar { width:40px; height:40px; border-radius:50%; background:#38bdf8; color:#fff; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:16px; margin-left:10px; }
        .user-avatar.banned { background: #ef4444; }
        .search-wrapper { position:relative; }
        .search-wrapper i { position:absolute; right:20px; top:50%; transform:translateY(-50%); color:#94a3b8; }
        #searchInput { padding:14px 45px 14px 20px; border-radius:40px; border:1.5px solid #e2e8f0; width:100%; font-family:Tajawal; }
        #clearSearch { position:absolute; left:15px; top:50%; transform:translateY(-50%); color:#94a3b8; cursor:pointer; display:none; }
    </style>
</head>
<body>

<div class="toast-container" id="toastContainer"></div>

<div class="modal-overlay" id="addUserModal">
    <div class="modal">
        <h2 style="margin-bottom:25px; color:#0f172a;"><i class="fa-solid fa-user-plus" style="margin-left:10px; color:#38bdf8;"></i>إضافة مستخدم جديد</h2>
        <form method="POST" id="addUserForm">
            <div class="form-group">
                <label>الاسم الأول</label>
                <input type="text" name="fname" class="form-control" required>
            </div>
            <div class="form-group">
                <label>الاسم الأخير</label>
                <input type="text" name="lname" class="form-control" required>
            </div>
            <div class="form-group">
                <label>البريد الإلكتروني</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label>كلمة المرور</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="form-group">
                <label>الصلاحية</label>
                <select name="role" class="form-control">
                    <option value="user">مستخدم</option>
                    <option value="admin">مدير</option>
                </select>
            </div>
            <div style="display:flex; gap:10px; margin-top:25px;">
                <button type="submit" name="add_user" class="btn btn-success" style="flex:1;"><i class="fa-solid fa-check"></i> إضافة</button>
                <button type="button" class="btn" onclick="closeAddModal()" style="flex:1; background:#f1f5f9; color:#475569;"><i class="fa-solid fa-xmark"></i> إلغاء</button>
            </div>
        </form>
    </div>
</div>

<div class="modal-overlay" id="deleteConfirmModal">
    <div class="modal" style="width:400px; text-align:center;">
        <i class="fa-solid fa-triangle-exclamation" style="font-size:50px; color:#ef4444; margin-bottom:20px;"></i>
        <h3 style="margin-bottom:10px;">تأكيد الحذف</h3>
        <p style="color:#64748b; margin-bottom:25px;">هل أنت متأكد من حذف هذا المستخدم؟ لا يمكن التراجع عن هذا الإجراء.</p>
        <div style="display:flex; gap:10px;">
            <a href="#" id="confirmDeleteBtn" class="btn btn-danger" style="flex:1;">نعم، احذف</a>
            <button onclick="closeDeleteModal()" class="btn" style="flex:1; background:#f1f5f9; color:#1e293b;">إلغاء</button>
        </div>
    </div>
</div>

<div class="sidebar">
    <h2>مدير المتجر</h2>
    <a href="dashboard.php"><i class="fa-solid fa-house"></i><span>الرئيسية</span></a>
    <a href="manage_products.php"><i class="fa-solid fa-box-open"></i><span>إدارة المنتجات</span></a>
    <a href="manage_comments.php"><i class="fa-solid fa-comments"></i><span>تعليقات العملاء</span></a>
    <a href="manage_orders.php"><i class="fa-solid fa-cart-shopping"></i><span>طلبات الشراء</span></a>
    <a href="manage_messages.php"><i class="fa-solid fa-envelope"></i><span>رسائل الزوار</span></a>
    <a href="manage_users.php" class="active"><i class="fa-solid fa-users"></i><span>إدارة المستخدمين</span></a>
</div>

<div class="main-content">
    <div class="header">
        <h3><i class="fa-solid fa-users" style="margin-left:12px; color:#38bdf8;"></i>إدارة حسابات المستخدمين</h3>
        <div style="display:flex; gap:15px;">
            <button class="btn btn-success" onclick="openAddModal()"><i class="fa-solid fa-plus"></i> إضافة مستخدم</button>
            <a href="logout.php" class="logout-btn"><i class="fa-solid fa-right-from-bracket"></i> تسجيل الخروج</a>
        </div>
    </div>
    
    <div class="content-area">
        <div class="stats-container">
            <div class="stat-card">
                <i class="fa-solid fa-user-group" style="font-size:40px; color:#38bdf8;"></i>
                <div><h4 style="color:#64748b; margin-bottom:5px;">إجمالي المستخدمين</h4><span style="font-size:32px; font-weight:800;"><?= $totalUsers ?></span></div>
            </div>
            <div class="stat-card">
                <i class="fa-solid fa-user-tie" style="font-size:40px; color:#f59e0b;"></i>
                <div><h4 style="color:#64748b; margin-bottom:5px;">المدراء</h4><span style="font-size:32px; font-weight:800;"><?= $totalAdmins ?></span></div>
            </div>
        </div>
        
        <div class="card" style="padding:20px 30px;">
            <div class="search-wrapper">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="searchInput" placeholder="بحث عن مستخدم...">
                <i class="fa-solid fa-xmark" id="clearSearch"></i>
            </div>
        </div>

        <div class="card">
            <table>
                <thead>
                    <tr><th>المستخدم</th><th>البريد</th><th>الصلاحية</th><th>تغيير الدور</th><th>إجراءات</th></tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($result)): 
                        $row_fname = $row['fname'] ?? $row['Fname'] ?? $row['first_name'] ?? $row['name'] ?? 'مستخدم';
                        $row_lname = $row['lname'] ?? $row['Lname'] ?? $row['last_name'] ?? '';
                        $initials = mb_substr($row_fname, 0, 1);
                        $is_banned = isset($row['is_banned']) ? (int)$row['is_banned'] : 0;
                    ?>
                    <tr style="<?= $is_banned ? 'opacity: 0.7; background: #fef2f2;' : '' ?>">
                        <td style="display:flex; align-items:center;">
                            <span class="user-avatar <?= $is_banned ? 'banned' : '' ?>"><?= $initials ?></span>
                            <div>
                                <?= htmlspecialchars($row_fname . ' ' . $row_lname) ?>
                                <?php if($is_banned): ?>
                                    <br><span style="background: #ef4444; color: white; padding: 2px 6px; border-radius: 4px; font-size: 11px;">محظور</span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($row['email'] ?? '') ?></td>
                        <td>
                            <span style="padding:6px 14px; border-radius:40px; font-weight:600; font-size:13px; background:<?= (isset($row['role']) && $row['role']=='admin') ? '#fef3c7' : '#dbeafe' ?>; color:<?= (isset($row['role']) && $row['role']=='admin') ? '#92400e' : '#1e40af' ?>;">
                                <?= (isset($row['role']) && $row['role']=='admin') ? 'مدير' : 'مستخدم' ?>
                            </span>
                        </td>
                        <td>
                            <?php if($row['id'] != $_SESSION['user_id']): ?>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
                                <select name="new_role" style="padding:6px; border-radius:8px; border:1px solid #cbd5e1;">
                                    <option value="user" <?= (isset($row['role']) && $row['role']=='user' ? 'selected' : '') ?>>مستخدم</option>
                                    <option value="admin" <?= (isset($row['role']) && $row['role']=='admin' ? 'selected' : '') ?>>مدير</option>
                                </select>
                                <button type="submit" name="update_role" class="btn btn-primary"><i class="fa-solid fa-pen">تأكيد</i></button>
                            </form>
                            <?php else: ?>
                                <span style="color:#94a3b8;">______</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($row['id'] != $_SESSION['user_id']): ?>
                                <div style="display: flex; gap: 5px; justify-content: flex-end;">
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('<?= $is_banned ? 'هل تريد فك الحظر عن هذا المستخدم؟' : 'هل أنت متأكد من حظر هذا المستخدم؟ لن يتمكن من تسجيل الدخول.' ?>')">
                                        <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
                                        <input type="hidden" name="current_status" value="<?= $is_banned ?>">
                                        <?php if($is_banned): ?>
                                            <button type="submit" name="toggle_ban" class="btn btn-unban"><i class="fa-solid fa-unlock"></i> فك الحظر</button>
                                        <?php else: ?>
                                            <button type="submit" name="toggle_ban" class="btn btn-ban"><i class="fa-solid fa-ban"></i> حظر</button>
                                        <?php endif; ?>
                                    </form>
                                    <button onclick="openDeleteModal(<?= $row['id'] ?>)" class="btn btn-danger"><i class="fa-solid fa-trash"></i>حذف </button>
                                </div>
                            <?php else: ?>
                            <span style="color:#94a3b8;">حسابك</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function showToast(message, type = 'success') {
        const container = document.getElementById('toastContainer');
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.innerHTML = `<i class="fa-solid fa-${type === 'success' ? 'check-circle' : 'circle-exclamation'}"></i> ${message}`;
        container.appendChild(toast);
        setTimeout(() => toast.remove(), 4000);
    }
    
    <?php if($msg_text): ?>
    showToast('<?= addslashes($msg_text) ?>', '<?= $msg_type ?>');
    <?php endif; ?>

    const addModal = document.getElementById('addUserModal');
    function openAddModal() { addModal.style.display = 'flex'; }
    function closeAddModal() { addModal.style.display = 'none'; }
    
    const deleteModal = document.getElementById('deleteConfirmModal');
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    function openDeleteModal(userId) {
        confirmBtn.href = `manage_users.php?delete=${userId}`;
        deleteModal.style.display = 'flex';
    }
    function closeDeleteModal() { deleteModal.style.display = 'none'; }
    
    window.onclick = (e) => {
        if(e.target === addModal) closeAddModal();
        if(e.target === deleteModal) closeDeleteModal();
    }
    
    const searchInput = document.getElementById('searchInput');
    const clearBtn = document.getElementById('clearSearch');
    searchInput.addEventListener('input', function() {
        const filter = this.value.toLowerCase();
        const rows = document.querySelectorAll('tbody tr');
        rows.forEach(row => row.style.display = row.textContent.toLowerCase().includes(filter) ? '' : 'none');
        clearBtn.style.display = this.value ? 'block' : 'none';
    });
    clearBtn.addEventListener('click', () => {
        searchInput.value = '';
        searchInput.dispatchEvent(new Event('input'));
    });
</script>
</body>
</html>