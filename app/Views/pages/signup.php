<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>

<div class="form_account">
  <div class="form-container container">
    <div class="form-title">إنشاء حساب</div>

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

      <p class="separator"><span>أو</span></p>

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
          <input type="text" name="username" placeholder="أسم المستخدم" required class="input input-user" value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>">
          <i class="fa-solid fa-user icon_form"></i>
        </div>

        <div class="input-wrapper" >
          <input type="email" name="email" placeholder="عنوان البريد الألكتروني" required class="input input-mail" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
          <i class="fa-solid fa-envelope icon_form"></i>
        </div>

        <div class="input-wrapper">
          <input type="password" name="password" placeholder="كلمة المرور" required class="input">
          <i class="fa-regular fa-eye-slash icon_form showPss"></i>
        </div>

        <div class="input-wrapper m-none">
          <input type="password" name="confirm_password" placeholder="تأكيد كلمة المرور" required class="input input-pass">
          <i class="fa-regular fa-eye-slash icon_form showPss"></i>
        </div>

        <div class="box-accept">
          <input type="checkbox" name="accept" value="yes" id="accept" required>
          <label for="accept">أوافق على جميع الشروط والأحكام.</label>
        </div>

        <div class="cf-turnstile" data-sitekey="0x4AAAAAAE6g1Fdz_dXPnmd4" style="margin-bottom: 15px; display: flex; justify-content: center;"></div>

        <input type="submit" class="btn-submit" value="إنشاء حساب">
        <p class="signup-text">
          لديك حساب بالفعل؟
          <a href="/login"> تسجيل الدخول</a>
        </p>
      </form>
  </div>
</div>