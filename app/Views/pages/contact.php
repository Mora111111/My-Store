  <div class="contact_us">
    <div class="contact_box container">
      
      <div class="contact form">
        <h3 class="title">أرسل لنا رسالة</h3>

        <form action="/contact" method="POST">
          <?= CSRF::getField() ?>
          <div class="form_box">
            <div class="row_50">
              <div class="input_box">
                <span>الأسم الأول</span>
                <input type="text" name="first_name" placeholder="الاسم الأول" required />
              </div>
              <div class="input_box">
                <span>الأسم الأخير</span>
                <input type="text" name="last_name" placeholder="الاسم الأخير" required />
              </div>
            </div>

            <div class="row_50">
              <div class="input_box">
                <span>الإيميل/المسجل به فقط</span>
                <input type="email" name="email" placeholder="البريد الإلكتروني" required />
              </div>
              <div class="input_box">
                <span>رقم الهاتف / واتساب</span>
                <input type="text" name="phone" placeholder="رقم الهاتف" required />
              </div>
            </div>

            <div class="row_100">
              <div class="input_box">
                <span>الرسالة</span>
                <textarea name="message" placeholder="اكتب رسالتك أو استفسارك هنا..." required></textarea>
              </div>
            </div>

            <div class="row_100">
              <div class="input_box">
                <input type="submit" value="ارسال" />
              </div>
            </div>
          </div>
        </form>
      </div>

      <div class="contact info">
        <h3 class="title">تواصل معنا</h3>
        <div>
          <i class="fa-solid fa-phone footer-icon"></i>
          <a href="#">01017******</a>
        </div>
        <div>
          <i class="fa-solid fa-phone footer-icon"></i>
          <a href="#">01034******</a>
        </div>
        <div>
          <i class="fa-solid fa-envelope footer-icon"></i>
          <a href="#">MY-Store@gmail.com</a>
        </div>
        <div>
          <i class="fa-solid fa-location-dot footer-icon"></i>
          <a href="#">المحافظات - مصر</a>
        </div>
      </div>

      <div class="contact map">
        <img src="/images/map.png" alt="Map Location" style="width:100%; height:100%; border-radius:10px; object-fit:cover;" />
      </div>
    </div>
  </div>