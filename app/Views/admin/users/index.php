<div class="modal-overlay" id="addUserModal">
  <div class="modal-content" style="width:450px;">
    <h2 style="margin-bottom:25px; color:#0f172a;"><i class="fa-solid fa-user-plus" style="margin-left:10px; color:#38bdf8;"></i>إضافة مستخدم جديد</h2>
    <form method="POST" action="/admin/users/add">
      <?= CSRF::getField() ?>
      <div class="form-group">
        <label>الاسم الأول</label>
        <input type="text" name="fname" required>
      </div>
      <div class="form-group">
        <label>الاسم الأخير</label>
        <input type="text" name="lname" required>
      </div>
      <div class="form-group">
        <label>البريد الإلكتروني</label>
        <input type="email" name="email" required>
      </div>
      <div class="form-group">
        <label>كلمة المرور</label>
        <input type="password" name="password" required>
      </div>
      <div class="form-group">
        <label>الصلاحية</label>
        <select name="role">
          <option value="user">مستخدم</option>
          <option value="admin">مدير</option>
        </select>
      </div>
      <div style="display:flex; gap:10px; margin-top:25px;">
        <button type="submit" name="add_user" class="btn-success" style="flex:1;"><i class="fa-solid fa-check"></i> إضافة</button>
        <button type="button" class="btn" onclick="closeAddModal()" style="flex:1; background:#f1f5f9; color:#475569;"><i class="fa-solid fa-xmark"></i> إلغاء</button>
      </div>
    </form>
  </div>
</div>

<div class="modal-overlay" id="deleteConfirmModal">
  <div class="modal-content" style="width:400px; text-align:center;">
    <i class="fa-solid fa-triangle-exclamation" style="font-size:50px; color:#ef4444; margin-bottom:20px;"></i>
    <h3 style="margin-bottom:10px;">تأكيد الحذف</h3>
    <p style="color:#64748b; margin-bottom:25px;">هل أنت متأكد من حذف هذا المستخدم؟ لا يمكن التراجع عن هذا الإجراء.</p>
    <div style="display:flex; gap:10px;">
      <form method="POST" action="/admin/users/delete" style="flex:1; display:flex; margin:0;">
        <?= CSRF::getField() ?>
        <input type="hidden" name="id" id="deleteUserId">
        <button type="submit" class="btn-danger" style="width:100%; justify-content:center; border:none; cursor:pointer; font-family:inherit;">نعم، احذف</button>
      </form>
      <button onclick="closeDeleteModal()" class="btn" style="flex:1; background:#f1f5f9; color:#1e293b; border:none; border-radius:30px; cursor:pointer; font-weight:600; font-family:Tajawal;">إلغاء</button>
    </div>
  </div>
</div>

<div class="stats-container">
    <div class="stat-card">
      <i class="fa-solid fa-user-group" style="font-size:40px; color:#38bdf8;"></i>
      <div><h4 style="color:#64748b; margin-bottom:5px;">إجمالي المستخدمين</h4><span style="font-size:32px; font-weight:800;"><?php echo $totalUsers; ?></span></div>
    </div>
    <div class="stat-card">
      <i class="fa-solid fa-user-tie" style="font-size:40px; color:#f59e0b;"></i>
      <div><h4 style="color:#64748b; margin-bottom:5px;">المدراء</h4><span style="font-size:32px; font-weight:800;"><?php echo $totalAdmins; ?></span></div>
    </div>
  </div>

  <div class="card">
    <table>
      <thead>
        <tr><th>المستخدم</th><th>البريد</th><th>الصلاحية</th><th>تغيير الدور</th><th>إجراءات</th></tr>
      </thead>
      <tbody>
        <?php foreach ($users as $row):
          $row_fname = $row['fname'] ?? $row['Fname'] ?? $row['first_name'] ?? $row['name'] ?? 'مستخدم';
          $row_lname = $row['lname'] ?? $row['Lname'] ?? $row['last_name'] ?? '';
          $initials = mb_substr($row_fname, 0, 1);
          $is_banned = isset($row['is_banned']) ? (int)$row['is_banned'] : 0;
        ?>
        <tr style="<?php echo $is_banned ? 'opacity: 0.7; background: #fef2f2;' : ''; ?>">
          <td style="display:flex; align-items:center;">
            <span class="user-avatar <?php echo $is_banned ? 'banned' : ''; ?>"><?php echo htmlspecialchars($initials); ?></span>
            <div>
              <?php echo htmlspecialchars($row_fname . ' ' . $row_lname); ?>
              <?php if($is_banned): ?><br><span style="background: #ef4444; color: white; padding: 2px 6px; border-radius: 4px; font-size: 11px;">محظور</span><?php endif; ?>
            </div>
          </td>
          <td><?php echo htmlspecialchars($row['email'] ?? ''); ?></td>
          <td>
            <span style="padding:6px 14px; border-radius:40px; font-weight:600; font-size:13px; background:<?php echo (isset($row['role']) && $row['role']=='admin') ? '#fef3c7' : '#dbeafe'; ?>; color:<?php echo (isset($row['role']) && $row['role']=='admin') ? '#92400e' : '#1e40af'; ?>;">
              <?php echo (isset($row['role']) && $row['role']=='admin') ? 'مدير' : 'مستخدم'; ?>
            </span>
          </td>
          <td>
            <?php if($row['id'] != Session::get('user_id')): ?>
            <form method="POST" action="/admin/users/update-role" class="role-update-form" style="display:inline;">
  <?= CSRF::getField() ?>
  <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
  <select name="new_role" style="padding:6px; border-radius:8px; border:1px solid #cbd5e1;">
    <option value="user" <?php echo (isset($row['role']) && $row['role']=='user' ? 'selected' : ''); ?>>مستخدم</option>
    <option value="admin" <?php echo (isset($row['role']) && $row['role']=='admin' ? 'selected' : ''); ?>>مدير</option>
  </select>
  <button type="submit" name="update_role" class="btn-primary"><i class="fa-solid fa-pen"></i> تأكيد</button>
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
                    <button type="submit" name="toggle_ban" class="btn-unban"><i class="fa-solid fa-unlock"></i> فك الحظر</button>
                  <?php else: ?>
                    <button type="submit" name="toggle_ban" class="btn-ban"><i class="fa-solid fa-ban"></i> حظر</button>
                  <?php endif; ?>
                </form>
                <button onclick="openDeleteModal(<?php echo $row['id']; ?>)" class="btn-danger"><i class="fa-solid fa-trash"></i> حذف</button>
              </div>
            <?php else: ?>
              <span style="color:#94a3b8;">حسابك</span>
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
        تأكيد الهوية
      </h3>
      <i class="fa-solid fa-xmark close-btn-modern" onclick="document.getElementById('otpModal').style.display='none'" style="position: absolute; top: 15px; left: 15px; cursor: pointer; font-size: 20px; color: #64748b;"></i>
    </div>
    <div class="modal-body-modern">
      <p class="modal-desc-modern" style="color: #64748b; margin-bottom: 20px;">تم إرسال رمز التحقق إلى بريد المطور. يرجى إدخال الرمز للمتابعة.</p>
      <form action="/admin/users/verify-role-otp" method="POST">
        <?= CSRF::getField() ?>
        <div class="form-group" style="margin-bottom: 20px;">
          <input type="text" name="otp_code" maxlength="6" placeholder="000000" 
                 style="text-align:center; font-size:28px; letter-spacing:8px; font-weight:800; border-radius:12px; border:2px solid #e2e8f0; width: 100%; padding: 15px; box-sizing: border-box;" required>
        </div>
        <div class="modal-footer-modern" style="display: flex; gap: 10px;">
          <button type="button" class="btn-cancel-modern" onclick="document.getElementById('otpModal').style.display='none'" style="flex: 1; padding: 12px; border: none; border-radius: 8px; background: #f1f5f9; color: #475569; font-weight: bold; cursor: pointer;">إلغاء</button>
          <button type="submit" class="btn-save-modern" style="flex: 1; padding: 12px; border: none; border-radius: 8px; background: #38bdf8; color: white; font-weight: bold; cursor: pointer; display: flex; justify-content:center;">تأكيد الترقية</button>
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
            // Handle cases where CSRF token might be dynamically named or missing
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
                    alert('فشل إرسال رمز التحقق');
                }
            } catch (err) {
                alert('خطأ في الاتصال بالسيرفر');
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        }
    });
});
</script>