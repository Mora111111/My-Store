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

    <!-- قسم رسائل النجاح والخطأ -->
    <?php if(isset($_GET['success'])): ?>
        <?php if($_GET['success'] === 'profile'): ?>
            <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> <?= lang('profile_update_success') ?></div>
        <?php elseif($_GET['success'] === 'password'): ?>
            <div class="alert alert-success"><i class="fa-solid fa-shield-check"></i> <?= lang('password_update_success') ?></div>
        <?php endif; ?>
    <?php endif; ?>

    <?php if(isset($_GET['error'])): ?>
        <div class="alert alert-error">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <?php 
                if($_GET['error'] === 'wrong_current_password') echo lang('wrong_current_password');
                elseif($_GET['error'] === 'short_password') echo lang('short_password');
                elseif($_GET['error'] === 'mismatch_password') echo lang('mismatch_password');
            ?>
        </div>
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
                <p><?= lang('view_my_orders') ?></p>
            </div>
        </div>
    </div>

    <!-- نموذج تعديل البيانات (الإيميل للقراءة فقط) -->
    <div class="profile-box">
        <h3><i class="fa-solid fa-user-gear"></i> <?= lang('edit_profile') ?></h3>
        <form method="POST" action="/profile/update">
            <?= CSRF::getField() ?>
            <div class="form-group">
                <label><?= lang('name_label') ?></label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label><?= lang('email_readonly_label') ?></label>
                <input type="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" readonly style="background-color: #f8fafc; color: #94a3b8; cursor: not-allowed; border-color: #e2e8f0;">
            </div>
            <button type="submit" class="btn-update"><i class="fa-solid fa-floppy-disk"></i> <?= lang('update_data_btn') ?></button>
        </form>
    </div>

    <!-- نموذج تغيير الباسورد (إضافة الباسورد الحالي) -->
    <div class="profile-box" style="margin-top: 30px;">
        <h3><i class="fa-solid fa-lock"></i> <?= lang('change_password_title') ?></h3>
        <form method="POST" action="/profile/update-password">
            <?= CSRF::getField() ?>
            <div class="form-group">
                <label><?= lang('current_password_label') ?></label>
                <input type="password" name="current_password" placeholder="<?= lang('current_password_placeholder') ?>" required>
            </div>
            <div class="form-group">
                <label><?= lang('new_password_label') ?></label>
                <input type="password" name="new_password" placeholder="<?= lang('new_password_placeholder') ?>" required minlength="8">
            </div>
            <div class="form-group">
                <label><?= lang('confirm_new_password_label') ?></label>
                <input type="password" name="confirm_password" placeholder="<?= lang('confirm_new_password_placeholder') ?>" required minlength="8">
            </div>
            <button type="submit" name="update_password" class="btn-update" style="background-color: #0f172a;"><i class="fa-solid fa-key"></i> <?= lang('update_password_btn') ?></button>
        </form>
    </div>

</div>