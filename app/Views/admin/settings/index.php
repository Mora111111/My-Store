<div class="card" style="max-width:800px; margin:auto;">
    <h2 style="text-align:center;"><i class="fa-solid fa-gear" style="color:#38bdf8;"></i> <?= lang('title_site_settings') ?></h2>

    <form action="/admin/settings/update" method="POST">
      <?= CSRF::getField() ?>
      <div class="form_row">
        <label><?= lang('lbl_about_text') ?></label>
        <textarea name="about_text" rows="4"><?php echo htmlspecialchars($site_settings['about_text'] ?? ''); ?></textarea>
      </div>
      <div class="form_row">
        <label><?= lang('lbl_phone1') ?></label>
        <input type="text" name="phone1" value="<?php echo htmlspecialchars($site_settings['phone1'] ?? ''); ?>">
      </div>
      <div class="form_row">
        <label><?= lang('lbl_phone2') ?></label>
        <input type="text" name="phone2" value="<?php echo htmlspecialchars($site_settings['phone2'] ?? ''); ?>">
      </div>
      <div class="form_row">
        <label><?= lang('lbl_contact_email') ?></label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($site_settings['email'] ?? ''); ?>">
      </div>
      <div class="form_row">
        <label><?= lang('lbl_store_address') ?></label>
        <input type="text" name="address" value="<?php echo htmlspecialchars($site_settings['address'] ?? ''); ?>">
      </div>
      <div class="form_row">
        <label><?= lang('lbl_timezone') ?></label>
        <select name="timezone" class="form_input">
            <option value="Africa/Cairo" <?php echo ($site_settings['timezone'] ?? '') == 'Africa/Cairo' ? 'selected' : ''; ?>><?= lang('tz_egypt') ?></option>
            <option value="Asia/Riyadh" <?php echo ($site_settings['timezone'] ?? '') == 'Asia/Riyadh' ? 'selected' : ''; ?>><?= lang('tz_ksa') ?></option>
            <option value="Asia/Dubai" <?php echo ($site_settings['timezone'] ?? '') == 'Asia/Dubai' ? 'selected' : ''; ?>><?= lang('tz_uae') ?></option>
            <option value="UTC" <?php echo ($site_settings['timezone'] ?? '') == 'UTC' ? 'selected' : ''; ?>><?= lang('tz_utc') ?></option>
        </select>
      </div>
      <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 25px 0;">
      <h3 style="color:#0f172a; margin-bottom: 15px;"><i class="fa-solid fa-truck-fast" style="color:#38bdf8;"></i> <?= lang('title_shipping_settings') ?></h3>
      <div class="form_row">
        <label><?= lang('lbl_shipping_cost') ?></label>
        <input type="number" name="shipping_cost" step="0.01" min="0" value="<?php echo htmlspecialchars($site_settings['shipping_cost'] ?? '0'); ?>">
      </div>

      <h3 style="color:#0f172a; margin-bottom: 15px;"><i class="fa-solid fa-link" style="color:#38bdf8;"></i> <?= lang('title_social_links') ?></h3>
      <div class="form_row">
        <label><?= lang('lbl_facebook_link') ?></label>
        <input type="url" name="facebook_link" value="<?php echo htmlspecialchars($site_settings['facebook_link'] ?? ''); ?>" placeholder="https://facebook.com/...">
      </div>

      <h3 style="color:#0f172a; margin-bottom: 15px;"><i class="fa-solid fa-power-off" style="color:#ef4444;"></i> <?= lang('title_store_status') ?></h3>
      <div class="form_row" style="display: flex; align-items: center; gap: 10px; background: #fee2e2; padding: 15px; border-radius: 12px; border: 1px solid #fca5a5;">
        <input type="checkbox" name="maintenance_mode" id="maintenance_mode" value="1" <?php echo (!empty($site_settings['maintenance_mode'])) ? 'checked' : ''; ?> style="width: 20px; height: 20px; cursor: pointer;">
        <label for="maintenance_mode" style="margin: 0; color: #991b1b; cursor: pointer; font-weight: bold;"><?= lang('lbl_maintenance_mode') ?></label>
      </div>
      <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 25px 0;">
      <h3 style="color:#0f172a; margin-bottom: 15px;"><i class="fa-solid fa-credit-card" style="color:#38bdf8;"></i> <?= lang('title_payment_gateway') ?></h3>
      
      <div class="form_row" style="display: flex; align-items: center; gap: 10px; background: #f0fdf4; padding: 15px; border-radius: 12px; border: 1px solid #bbf7d0; margin-bottom: 15px;">
        <input type="checkbox" name="enable_online_payment" id="enable_online_payment" value="1" <?php echo (!empty($site_settings['enable_online_payment'])) ? 'checked' : ''; ?> style="width: 20px; height: 20px; cursor: pointer;">
        <label for="enable_online_payment" style="margin: 0; color: #166534; cursor: pointer; font-weight: bold;"><?= lang('lbl_enable_online_payment') ?></label>
      </div>

      <div class="form_row">
        <label><?= lang('lbl_api_key') ?></label>
        <input type="text" name="gateway_api_key" value="<?php echo htmlspecialchars($site_settings['gateway_api_key'] ?? ''); ?>">
      </div>
      <div class="form_row">
        <label><?= lang('lbl_hmac_secret') ?></label>
        <input type="text" name="gateway_hmac_secret" value="<?php echo htmlspecialchars($site_settings['gateway_hmac_secret'] ?? ''); ?>">
      </div>
      <div class="form_row">
        <label><?= lang('lbl_card_integration_id') ?></label>
        <input type="text" name="gateway_integration_id" value="<?php echo htmlspecialchars($site_settings['gateway_integration_id'] ?? ''); ?>">
      </div>
      <div class="form_row">
        <label><?= lang('lbl_wallet_integration_id') ?></label>
        <input type="text" name="gateway_integration_id_wallet" value="<?php echo htmlspecialchars($site_settings['gateway_integration_id_wallet'] ?? ''); ?>">
      </div>
      <div class="form_row">
        <label><?= lang('lbl_iframe_id') ?></label>
        <input type="text" name="gateway_iframe_id" value="<?php echo htmlspecialchars($site_settings['gateway_iframe_id'] ?? ''); ?>">
      </div>
      <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 25px 0;">
      <h3 style="color:#0f172a; margin-bottom: 15px;"><i class="fa-solid fa-shield-halved" style="color:#38bdf8;"></i> <?= lang('title_login_security') ?></h3>
      
      <div class="form_row">
        <label><?= lang('lbl_google_client_id') ?></label>
        <input type="text" name="google_client_id" value="<?php echo htmlspecialchars($site_settings['google_client_id'] ?? ''); ?>">
      </div>
      <div class="form_row">
        <label><?= lang('lbl_google_client_secret') ?></label>
        <input type="password" name="google_client_secret" value="<?php echo htmlspecialchars($site_settings['google_client_secret'] ?? ''); ?>">
      </div>
      <div class="form_row">
        <label><?= lang('lbl_turnstile_site_key') ?></label>
        <input type="text" name="turnstile_site_key" value="<?php echo htmlspecialchars($site_settings['turnstile_site_key'] ?? ''); ?>">
      </div>
      <div class="form_row">
        <label><?= lang('lbl_turnstile_secret_key') ?></label>
        <input type="password" name="turnstile_secret_key" value="<?php echo htmlspecialchars($site_settings['turnstile_secret_key'] ?? ''); ?>">
      </div>
      <div class="form_row">
        <label><?= lang('lbl_admin_otp_secret') ?></label>
        <input type="text" name="admin_otp_secret" value="<?php echo htmlspecialchars($site_settings['admin_otp_secret'] ?? ''); ?>" placeholder="<?= lang('ph_admin_otp_secret') ?>">
      </div>
      <br>
      <button type="submit" class="btn-submit"><i class="fa-solid fa-floppy-disk"></i> <?= lang('btn_save_settings') ?></button>
    </form>
  </div>