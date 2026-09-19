    <div class="form_account">
        <div class="form-container container">
            <div class="form-title"><?= lang('login') ?></div>

            <div class="social-form">
                <a href="/auth/google/login" class="social-btn btn-google">
                    Google
                    <img src="/images/logos/google.svg" alt="google" class="social-icon">
                </a>
            </div>

            <p class="separator"><span><?= lang('or_separator') ?></span></p>

            <?php if (Session::get('login_error')): ?>
                <div style="color: #d9534f; text-align: center; margin-bottom: 15px; font-weight: bold;"><?php echo htmlspecialchars(Session::get('login_error')); ?></div>
            <?php endif; ?>

            <form action="/login" method="POST" class="form">
                <?= CSRF::getField() ?>
                <div class="input-wrapper" >
                    <input type="email" name="email" placeholder="<?= lang('email_placeholder') ?>" required class="input input-mail">
                    <i class="fa-solid fa-envelope icon_form"></i>
                </div>

                <div class="input-wrapper m-none">
                    <input type="password" name="password" placeholder="<?= lang('password_placeholder') ?>" required class="input input-mail">
                    <i class="fa-regular fa-eye-slash icon_form showPss"></i>
                </div>

                <a href="/forgot-password" class="forgot-pass-link"><?= lang('forgot_password') ?></a>

                <input type="submit" class="btn-submit" value="<?= lang('login') ?>">

                <p class="signup-text">
                    <?= lang('dont_have_account') ?>
                    <a href="/signup"><?= lang('create_account') ?></a>
                </p>
            </form>

        </div>
    </div>