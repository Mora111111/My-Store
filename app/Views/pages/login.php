    <div class="form_account">
        <div class="form-container container">
            <div class="form-title">تسجيل الدخول</div>

            <div class="social-form">
                <button class="social-btn">
                    Google
                    <img src="/images/logos/google.svg" alt="google" class="social-icon">
                </button>
                <button class="social-btn">
                    Apple
                    <img src="/images/logos/apple.svg" alt="Apple" class="social-icon">
                </button>
            </div>

            <p class="separator"><span>أو</span></p>

            <?php if (Session::get('login_error')): ?>
                <div style="color: #d9534f; text-align: center; margin-bottom: 15px; font-weight: bold;"><?php echo htmlspecialchars(Session::get('login_error')); ?></div>
            <?php endif; ?>

            <form action="/login" method="POST" class="form">
                <?= CSRF::getField() ?>
                <div class="input-wrapper" >
                    <input type="email" name="email" placeholder="عنوان البريد الألكتروني" required class="input input-mail">
                    <i class="fa-solid fa-envelope icon_form"></i>
                </div>

                <div class="input-wrapper m-none">
                    <input type="password" name="password" placeholder="كلمة المرور" required class="input input-mail">
                    <i class="fa-regular fa-eye-slash icon_form showPss"></i>
                </div>

                <a href="/forgot-password" class="forgot-pass-link">هل نسيت كلمة المرور؟</a>

                <input type="submit" class="btn-submit" value="تسجيل الدخول">

                <p class="signup-text">
                    ليس لديك حساب؟
                    <a href="/signup">إنشاء حساب</a>
                </p>
            </form>

        </div>
    </div>