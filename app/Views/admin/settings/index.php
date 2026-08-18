<style>
  .form_row { margin-bottom: 20px; }
  .form_row label { display: block; font-weight: bold; margin-bottom: 8px; color: #334155; }
  .form_row input, .form_row textarea { width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 12px; font-family: inherit; font-size: 15px; box-sizing: border-box; }
  .form_row input:focus, .form_row textarea:focus { outline: none; border-color: #38bdf8; box-shadow: 0 0 0 4px rgba(56,189,248,0.1); }
  .save_btn { background: linear-gradient(135deg, #38bdf8, #2dd4bf); color: #0f172a; border: none; padding: 14px 28px; border-radius: 40px; cursor: pointer; font-size: 16px; font-weight: 700; box-shadow: 0 8px 16px rgba(56,189,248,0.2); width: 100%; transition: 0.3s; }
  .save_btn:hover { transform: translateY(-2px); box-shadow: 0 12px 20px rgba(56,189,248,0.3); }
</style>
<div class="card" style="max-width:800px; margin:auto;">
    <h2 style="text-align:center;"><i class="fa-solid fa-gear" style="color:#38bdf8;"></i> إعدادات الموقع</h2>

    <form action="/admin/settings/update" method="POST">
      <?= CSRF::getField() ?>
      <div class="form_row">
        <label>نص "معلومات عنا" (يظهر في الفوتر):</label>
        <textarea name="about_text" rows="4" required><?php echo htmlspecialchars($site_settings['about_text'] ?? ''); ?></textarea>
      </div>
      <div class="form_row">
        <label>رقم الهاتف الأول:</label>
        <input type="text" name="phone1" value="<?php echo htmlspecialchars($site_settings['phone1'] ?? ''); ?>" required>
      </div>
      <div class="form_row">
        <label>رقم الهاتف الثاني:</label>
        <input type="text" name="phone2" value="<?php echo htmlspecialchars($site_settings['phone2'] ?? ''); ?>">
      </div>
      <div class="form_row">
        <label>البريد الإلكتروني للتواصل:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($site_settings['email'] ?? ''); ?>" required>
      </div>
      <div class="form_row">
        <label>عنوان المتجر:</label>
        <input type="text" name="address" value="<?php echo htmlspecialchars($site_settings['address'] ?? ''); ?>" required>
      </div>
      <button type="submit" class="save_btn"><i class="fa-solid fa-floppy-disk"></i> حفظ التعديلات</button>
    </form>
  </div>
