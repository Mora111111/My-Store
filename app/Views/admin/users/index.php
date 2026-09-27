<div class="modal-overlay" id="addUserModal">
  <div class="modal-content" style="width:450px;">
    <h2 style="margin-bottom:25px; color:#0f172a;"><i class="fa-solid fa-user-plus" style="margin-left:10px; color:#38bdf8;"></i><?= lang('title_add_user') ?></h2>
    <form method="POST" action="/admin/users/add">
      <?= CSRF::getField() ?>
      <div class="form-group">
        <label><?= lang('lbl_fname') ?></label>
        <input type="text" name="fname" required>
      </div>
      <div class="form-group">
        <label><?= lang('lbl_lname') ?></label>
        <input type="text" name="lname" required>
      </div>
      <div class="form-group">
        <label><?= lang('lbl_email') ?></label>
        <input type="email" name="email" required>
      </div>
      <div class="form-group">
        <label><?= lang('lbl_password') ?></label>
        <input type="password" name="password" required>
      </div>
      <div class="form-group">
        <label><?= lang('lbl_role') ?></label>
        <select name="role">
          <option value="user"><?= lang('role_user') ?></option>
          <option value="admin"><?= lang('role_admin') ?></option>
        </select>
      </div>
      <div style="display:flex; gap:10px; margin-top:25px;">
        <button type="submit" name="add_user" class="btn-success" style="flex:1;"><i class="fa-solid fa-check"></i> <?= lang('btn_add') ?></button>
        <button type="button" class="btn" onclick="closeAddModal()" style="flex:1; background:#f1f5f9; color:#475569;"><i class="fa-solid fa-xmark"></i> <?= lang('btn_cancel') ?></button>
      </div>
    </form>
  </div>
</div>

<div class="modal-overlay" id="deleteConfirmModal">
  <div class="modal-content" style="width:400px; text-align:center;">
    <i class="fa-solid fa-triangle-exclamation" style="font-size:50px; color:#ef4444; margin-bottom:20px;"></i>
    <h3 style="margin-bottom:10px;"><?= lang('title_confirm_delete') ?></h3>
    <p style="color:#64748b; margin-bottom:25px;"><?= lang('desc_confirm_delete') ?></p>
    <div style="display:flex; gap:10px;">
      <form method="POST" action="/admin/users/delete" style="flex:1; display:flex; margin:0;">
        <?= CSRF::getField() ?>
        <input type="hidden" name="id" id="deleteUserId">
        <button type="submit" class="btn-danger" style="width:100%; justify-content:center; border:none; cursor:pointer; font-family:inherit;"><?= lang('btn_yes_delete') ?></button>
      </form>
      <button onclick="closeDeleteModal()" class="btn" style="flex:1; background:#f1f5f9; color:#1e293b; border:none; border-radius:30px; cursor:pointer; font-weight:600; font-family:inherit;"><?= lang('btn_cancel') ?></button>
    </div>
  </div>
</div>

<div class="stats-container">
    <div class="stat-card">
      <i class="fa-solid fa-user-group" style="font-size:40px; color:#38bdf8;"></i>
      <div><h4 style="color:#64748b; margin-bottom:5px;"><?= lang('stat_total_users') ?></h4><span style="font-size:32px; font-weight:800;"><?php echo $totalUsers; ?></span></div>
    </div>
    <div class="stat-card">
      <i class="fa-solid fa-user-tie" style="font-size:40px; color:#f59e0b;"></i>
      <div><h4 style="color:#64748b; margin-bottom:5px;"><?= lang('stat_admins') ?></h4><span style="font-size:32px; font-weight:800;"><?php echo $totalAdmins; ?></span></div>
    </div>
  </div>

    <form method="GET" action="/admin/users" style="margin-bottom: 25px; display: flex; gap: 10px; background: #fff; padding: 20px; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
        <input type="text" name="search" placeholder="<?= lang('ph_search_users') ?>" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" style="flex: 1; padding: 12px 16px; border: 1.5px solid #e2e8f0; border-radius: 12px; outline: none; font-family: inherit;">
        <button type="submit" class="btn-submit" style="padding: 12px 24px; border-radius: 12px;"><i class="fa-solid fa-magnifying-glass"></i> <?= lang('btn_search') ?></button>
        <?php if (!empty($_GET['search'])): ?>
            <a href="/admin/users" class="cancel-btn" style="padding: 12px 24px; display: flex; align-items: center; border-radius: 12px; background: #f1f5f9;"><?= lang('btn_cancel') ?></a>
        <?php endif; ?>
    </form>

  <div class="card">
    <table>
      <thead>
        <tr><th><?= lang('th_user') ?></th><th><?= lang('th_email') ?></th><th><?= lang('th_role') ?></th><th><?= lang('th_change_role') ?></th><th><?= lang('th_actions') ?></th></tr>
      </thead>
      <tbody>
        <?php foreach ($users as $row):
          $row_fname = $row['fname'] ?? $row['Fname'] ?? $row['first_name'] ?? $row['name'] ?? lang('role_user');
          $row_lname = $row['lname'] ?? $row['Lname'] ?? $row['last_name'] ?? '';
          $initials = mb_substr($row_fname, 0, 1);
          $is_banned = isset($row['is_banned']) ? (int)$row['is_banned'] : 0;
        ?>
        <tr style="<?php echo $is_banned ? 'opacity: 0.7; background: #fef2f2;' : ''; ?>">
          <td style="display:flex; align-items:center;">
            <span class="user-avatar <?php echo $is_banned ? 'banned' : ''; ?>"><?php echo htmlspecialchars($initials); ?></span>
            <div>
              <?php echo htmlspecialchars($row_fname . ' ' . $row_lname); ?>
              <?php if($is_banned): ?><br><span style="background: #ef4444; color: white; padding: 2px 6px; border-radius: 4px; font-size: 11px;"><?= lang('badge_banned') ?></span><?php endif; ?>
            </div>
          </td>
          <td><?php echo htmlspecialchars($row['email'] ?? ''); ?></td>
          <td>
            <span style="padding:6px 14px; border-radius:40px; font-weight:600; font-size:13px; background:<?php echo (isset($row['role']) && $row['role']=='admin') ? '#fef3c7' : '#dbeafe'; ?>; color:<?php echo (isset($row['role']) && $row['role']=='admin') ? '#92400e' : '#1e40af'; ?>;">
              <?php echo (isset($row['role']) && $row['role']=='admin') ? lang('role_admin') : lang('role_user'); ?>
            </span>
          </td>
          <td>
            <?php if($row['id'] != Session::get('user_id')): ?>
            <form method="POST" action="/admin/users/update-role" class="role-update-form" style="display:inline;">
  <?= CSRF::getField() ?>
  <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
  <select name="new_role" style="padding:6px; border-radius:8px; border:1px solid #cbd5e1;">
    <option value="user" <?php echo (isset($row['role']) && $row['role']=='user' ? 'selected' : ''); ?>><?= lang('role_user') ?></option>
    <option value="admin" <?php echo (isset($row['role']) && $row['role']=='admin' ? 'selected' : ''); ?>><?= lang('role_admin') ?></option>
  </select>
  <button type="submit" name="update_role" class="btn-primary"><i class="fa-solid fa-pen"></i> <?= lang('btn_confirm') ?></button>
</form>
            <?php else: ?>
              <span style="color:#94a3b8;">______</span>
            <?php endif; ?>
          </td>
          <td>
            <?php if($row['id'] != Session::get('user_id')): ?>
              <div style="display: flex; gap: 5px; justify-content: flex-end;">
                <form method="POST" action="/admin/users/ban" style="display:inline;">
                  <?= CSRF::getField() ?>
                  <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                  <input type="hidden" name="current_status" value="<?php echo $is_banned; ?>">
                  <?php if($is_banned): ?>
                    <button type="submit" name="toggle_ban" class="btn-unban"><i class="fa-solid fa-unlock"></i> <?= lang('btn_unban') ?></button>
                  <?php else: ?>
                    <button type="submit" name="toggle_ban" class="btn-ban"><i class="fa-solid fa-ban"></i> <?= lang('btn_ban') ?></button>
                  <?php endif; ?>
                </form>
                <button onclick="openDeleteModal(<?php echo $row['id']; ?>)" class="btn-danger"><i class="fa-solid fa-trash"></i> <?= lang('btn_delete') ?></button>
              </div>
            <?php else: ?>
              <span style="color:#94a3b8;"><?= lang('lbl_your_account') ?></span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

<div id="otpModal" class="modal-overlay" style="display:none;">
  <div class="modal-content-modern" style="max-width:400px; background: white; padding: 25px; border-radius: 15px; text-align: center; position: relative;">
    <div class="modal-header-modern" style="margin-bottom: 20px;">
      <h3 class="modal-title-modern" style="margin: 0; color: #0f172a; display: flex; align-items: center; justify-content: center; gap: 10px;">
        <div class="icon-wrapper-modern" style="color: #38bdf8; font-size: 24px;"><i class="fa-solid fa-shield-halved"></i></div>
        <?= lang('title_otp') ?>
      </h3>
      <i class="fa-solid fa-xmark close-btn-modern" onclick="document.getElementById('otpModal').style.display='none'" style="position: absolute; top: 15px; left: 15px; cursor: pointer; font-size: 20px; color: #64748b;"></i>
    </div>
    <div class="modal-body-modern">
      <p class="modal-desc-modern" style="color: #64748b; margin-bottom: 20px;"><?= lang('desc_otp') ?></p>
      <form action="/admin/users/verify-role-otp" method="POST">
        <?= CSRF::getField() ?>
        <div class="form-group" style="margin-bottom: 20px;">
          <input type="text" name="otp_code" maxlength="6" placeholder="000000" 
                 style="text-align:center; font-size:28px; letter-spacing:8px; font-weight:800; border-radius:12px; border:2px solid #e2e8f0; width: 100%; padding: 15px; box-sizing: border-box;" required>
        </div>
        <div class="modal-footer-modern" style="display: flex; gap: 10px;">
          <button type="button" class="btn-cancel-modern" onclick="document.getElementById('otpModal').style.display='none'" style="flex: 1; padding: 12px; border: none; border-radius: 8px; background: #f1f5f9; color: #475569; font-weight: bold; cursor: pointer;"><?= lang('btn_cancel') ?></button>
          <button type="submit" class="btn-save-modern" style="flex: 1; padding: 12px; border: none; border-radius: 8px; background: #38bdf8; color: white; font-weight: bold; cursor: pointer; display: flex; justify-content:center;"><?= lang('btn_confirm_upgrade') ?></button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  const addModal = document.getElementById('addUserModal');
  function openAddModal() { addModal.style.display = 'flex'; }
  function closeAddModal() { addModal.style.display = 'none'; }
  const deleteModal = document.getElementById('deleteConfirmModal');
  function openDeleteModal(userId) {
    document.getElementById('deleteUserId').value = userId;
    deleteModal.style.display = 'flex';
  }
  function closeDeleteModal() { deleteModal.style.display = 'none'; }
  const otpModal = document.getElementById('otpModal');
  
  window.onclick = (e) => {
    if(e.target === addModal) closeAddModal();
    if(e.target === deleteModal) closeDeleteModal();
    if(e.target === otpModal) otpModal.style.display = 'none';
  }

  document.querySelectorAll('.role-update-form').forEach(form => {
    form.addEventListener('submit', async function(e) {
        const roleSelect = this.querySelector('select[name="new_role"]');
        if (roleSelect.value === 'admin') {
            e.preventDefault();
            const userId = this.querySelector('input[name="user_id"]').value;
            const csrfInput = this.querySelector('input[name^="csrf"]');
            const csrfToken = csrfInput ? csrfInput.value : '';
            
            const btn = this.querySelector('button');
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

            try {
                const formData = new FormData();
                formData.append('user_id', userId);
                if (csrfToken) formData.append(csrfInput.name, csrfToken);

                const res = await fetch('/admin/users/request-otp', { method: 'POST', body: formData });
                const data = await res.json();

                if (data.success) {
                    otpModal.style.display = 'flex';
                } else {
                    alert('<?= lang('alert_otp_failed') ?>');
                }
            } catch (err) {
                alert('<?= lang('alert_server_error') ?>');
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        }
    });
});
</script>