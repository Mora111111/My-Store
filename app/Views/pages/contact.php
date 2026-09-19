  <div class="contact_us">
    <div class="contact_box container">
      
      <div class="contact form">
        <h3 class="title"><?= lang('send_us_message') ?></h3>

        <form action="/contact" method="POST">
          <?= CSRF::getField() ?>
          <div class="form_box">
            <div class="row_50">
              <div class="input_box">
                <span><?= lang('first_name_label') ?></span>
                <input type="text" name="first_name" placeholder="<?= lang('first_name_placeholder') ?>" required />
              </div>
              <div class="input_box">
                <span><?= lang('last_name_label') ?></span>
                <input type="text" name="last_name" placeholder="<?= lang('last_name_placeholder') ?>" required />
              </div>
            </div>

            <div class="row_50">
              <div class="input_box">
                <span><?= lang('email_registered_only') ?></span>
                <input type="email" name="email" placeholder="<?= lang('email_placeholder') ?? 'البريد الإلكتروني' ?>" required />
              </div>
              <div class="input_box">
                <span><?= lang('phone_whatsapp') ?></span>
                <input type="text" name="phone" placeholder="<?= lang('phone_placeholder') ?>" required />
              </div>
            </div>

            <div class="row_100">
              <div class="input_box">
                <span><?= lang('message_label') ?></span>
                <textarea name="message" placeholder="<?= lang('message_placeholder') ?>" required></textarea>
              </div>
            </div>

            <div class="row_100">
              <div class="input_box">
                <input type="submit" value="<?= lang('send_btn') ?>" />
              </div>
            </div>
          </div>
        </form>
      </div>

      <div class="contact info">
        <h3 class="title"><?= lang('connect_with_us') ?></h3>
        <div>
          <i class="fa-solid fa-phone footer-icon"></i>
          <a href="javascript:void(0);"><?= lang('contact_number_1') ?></a>
        </div>
        <div>
          <i class="fa-solid fa-phone footer-icon"></i>
          <a href="javascript:void(0);"><?= lang('contact_number_2') ?></a>
        </div>
        <div>
          <i class="fa-solid fa-envelope footer-icon"></i>
          <a href="mailto:MY-Store@gmail.com">MY-Store@gmail.com</a>
        </div>
        <div>
          <i class="fa-solid fa-location-dot footer-icon"></i>
          <a href="javascript:void(0);"><?= lang('governorates_egypt') ?></a>
        </div>
      </div>

      <div class="contact map">
        <img src="/images/map.png" alt="Map Location" style="width:100%; height:100%; border-radius:10px; object-fit:cover;" />
      </div>
    </div>
  </div>