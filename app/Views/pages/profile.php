<style>

    .profile-container { max-width: 800px; margin: 0 auto 50px; padding: 120px 20px 0; }
    .profile-header { background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); text-align: center; margin-bottom: 30px; }
    .profile-avatar { width: 100px; height: 100px; background: var(--main-color, #1abc9c); color: #fff; font-size: 40px; line-height: 100px; border-radius: 50%; margin: 0 auto 15px; display: flex; align-items: center; justify-content: center; }
    .profile-name { font-size: 24px; color: #333; margin-bottom: 5px; }
    .profile-email { color: #777; font-size: 16px; }
    .stats-box { display: flex; justify-content: center; gap: 20px; margin-top: 20px; }
    .stat-item { background: #f1f1f1; padding: 15px 30px; border-radius: 8px; text-align: center; }
    .stat-item h4 { margin: 0; font-size: 22px; color: var(--main-color, #1abc9c); }
    .stat-item p { margin: 5px 0 0; color: #555; font-size: 14px; }
    .profile-box { background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
    .profile-box h3 { margin-top: 0; color: #333; border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 20px; }
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; margin-bottom: 8px; font-weight: bold; color: #555; }
    .form-group input { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; outline: none; transition: 0.3s; }
    .form-group input:focus { border-color: var(--main-color, #1abc9c); }
    .btn-update { background: var(--main-color, #1abc9c); color: #fff; border: none; padding: 12px 25px; border-radius: 5px; font-size: 16px; cursor: pointer; font-family: 'Cairo', sans-serif; transition: 0.3s; }
    .btn-update:hover { opacity: 0.9; }
    .alert { padding: 15px; border-radius: 5px; margin-bottom: 20px; font-weight: bold; }
    .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .alert-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
</style>

<div class="profile-container">

    <?php if(isset($_GET['success'])): ?>
        <div class="alert alert-success">تم تحديث الملف الشخصي بنجاح.</div>
    <?php endif; ?>

    <div class="profile-header">
        <div class="profile-avatar">
            <i class="fa-solid fa-user"></i>
        </div>
        <h2 class="profile-name"><?php echo htmlspecialchars($user['name'] ?? ''); ?></h2>
        <p class="profile-email"><?php echo htmlspecialchars($user['email'] ?? ''); ?></p>

        <div class="stats-box">
            <div class="stat-item">
                <h4><a href="/my-orders" style="color:var(--main-color); text-decoration:none;"><i class="fa-solid fa-box-open"></i></a></h4>
                <p>عرض طلباتي</p>
            </div>
        </div>
    </div>

    <div class="profile-box">
        <h3><i class="fa-solid fa-user-gear"></i> تعديل الملف الشخصي</h3>
        <form method="POST" action="/profile/update">
            <?= CSRF::getField() ?>
            <div class="form-group">
                <label>الاسم</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label>البريد الإلكتروني</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
            </div>
            <button type="submit" class="btn-update">تحديث البيانات</button>
        </form>
    </div>

    <div class="profile-box" style="margin-top: 30px;">
        <h3><i class="fa-solid fa-lock"></i> تغيير كلمة المرور</h3>
        <form method="POST" action="">
            <?= CSRF::getField() ?>
            <div class="form-group">
                <label>كلمة المرور الجديدة</label>
                <input type="password" name="new_password" placeholder="أدخل كلمة المرور الجديدة" required>
            </div>
            <div class="form-group">
                <label>تأكيد كلمة المرور الجديدة</label>
                <input type="password" name="confirm_password" placeholder="أعد إدخال كلمة المرور" required>
            </div>
            <button type="submit" name="update_password" class="btn-update">تحديث كلمة المرور</button>
        </form>
    </div>

</div>