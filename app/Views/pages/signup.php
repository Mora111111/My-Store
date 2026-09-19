<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>

<div class="form_account">
  <div class="form-container container">
    <div class="form-title"><?= lang('create_account') ?></div>

      <div class="social-form">
          <a href="/auth/google/login" class="social-btn btn-google">
              Google
              <img src="/images/logos/google.svg" alt="google" class="social-icon">
          </a>
          <button type="button" class="social-btn">
              Apple
              <img src="/images/logos/apple.svg" alt="Apple" class="social-icon">
          </button>
      </div>

      <p class="separator"><span><?= lang('or_separator') ?></span></p>

      <?php if (!empty($error)): ?>
          <div style="color: #d9534f; text-align: center; margin-bottom: 15px; font-weight: bold; background: #ffe6e6; padding: 10px; border-radius: 5px;">
              <?php echo htmlspecialchars($error); ?>
          </div>
      <?php endif; ?>

      <?php if (!empty($success)): ?>
          <div style="color: #5cb85c; text-align: center; margin-bottom: 15px; font-weight: bold; background: #e6ffe6; padding: 10px; border-radius: 5px;">
              <?php echo htmlspecialchars($success); ?>
          </div>
      <?php endif; ?>

      <form action="/signup" method="POST" class="form">
        <?= CSRF::getField() ?>
        
        <div class="input-wrapper" >
          <input type="text" name="username" placeholder="<?= lang('username_placeholder') ?>" required class="input input-user" value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>">
          <i class="fa-solid fa-user icon_form"></i>
        </div>

        <div class="input-wrapper" >
          <input type="email" name="email" placeholder="<?= lang('email_placeholder') ?>" required class="input input-mail" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
          <i class="fa-solid fa-envelope icon_form"></i>
        </div>

        <div class="input-wrapper">
          <input type="password" name="password" placeholder="<?= lang('password_placeholder') ?>" required class="input">
          <i class="fa-regular fa-eye-slash icon_form showPss"></i>
        </div>

        <div class="input-wrapper m-none">
          <input type="password" name="confirm_password" placeholder="<?= lang('confirm_password_placeholder') ?>" required class="input input-pass">
          <i class="fa-regular fa-eye-slash icon_form showPss"></i>
        </div>

        <div class="box-accept">
          <input type="checkbox" name="accept" value="yes" id="accept" required>
          <label for="accept"><?= lang('agree_terms') ?></label>
        </div>

        <?php 
        require_once APP_DIR . '/Models/Setting.php';
        $turnstile_settings = (new Setting())->getSettings();
        if(!empty($turnstile_settings['turnstile_site_key'])): 
        ?>
            <div class="cf-turnstile" data-sitekey="<?= htmlspecialchars($turnstile_settings['turnstile_site_key']) ?>" style="margin-bottom: 15px; display: flex; justify-content: center;"></div>
        <?php endif; ?>

        <input type="submit" class="btn-submit" value="<?= lang('create_account') ?>">
        <p class="signup-text">
          <?= lang('already_have_account') ?>
          <a href="/login"> <?= lang('login') ?></a>
        </p>
      </form>
  </div>
</div>