<?php
$footerSettingModel = new Setting();
$footerSettings =$footerSettingModel->getSettings();
?>
<div class="cart">
  <h2 class="cart_title">عربة التسوق</h2>
  <div class="cart_content"></div>
  <div class="promo-code-section" id="promo-section" style="display: none; padding: 15px 20px; border-top: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0; margin-bottom: 15px; background: #f8fafc;">
      <div style="display: flex; gap: 8px;">
          <input type="text" id="promo_code_input" placeholder="لديك كود خصم؟" style="flex: 1; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none; font-family: inherit; font-size: 14px; text-transform: uppercase;">
          <button id="apply_promo_btn" style="background: #0f172a; color: #fff; border: none; padding: 0 15px; border-radius: 6px; cursor: pointer; font-family: inherit; font-weight: bold; transition: 0.3s;">تطبيق</button>
      </div>
      <p id="promo_message" style="margin: 8px 0 0 0; font-size: 13px; font-weight: bold;"></p>
  </div>
  <div class="total">
    <div class="total_title">الاجمالي</div>
    <div class="total_price">0.00 ج.م</div>
  </div>
  <a href="/checkout" class="btn_buy">شراء</a>
  <div class="cart_empty">
    <div><img src="/images/Cart-img.png" alt="Empty Cart"></div>
    <p>عربة التسوق فارغة</p>
    <a href="/products" class="btn_shopping">إستكشف المنتجات</a>
  </div>
  <i class="fa-solid fa-xmark" id="cart-close"></i>
</div>

<footer class="footer">
  <div class="footer_container container grid_content">
    <div class="footer_item">
      <h3 class="footer_title">معلومات عنا</h3>
      <p class="footer_p"><?php echo !empty($footerSettings['about_text']) ? htmlspecialchars($footerSettings['about_text']) : 'نحن متجر على الإنترنت نقدم أفضل المنتجات ذات الجودة العالية والتسليم السريع'; ?></p>
      <img src="/images/logos/logo-white.png" alt="" class="footer_img">
    </div>
    <div class="footer_item">
      <h3 class="footer_title">الحساب</h3>
      <ul class="footer_list">
        <li class="footer_li">
          <a href="/login" class="footer_link">تسجيل الدخول</a>
        </li>
        <li class="footer_li">
          <a href="/signup" class="footer_link">إنشاء حساب</a>
        </li>
        <li class="footer_li">
          <a href="/" class="footer_link">الرئيسية</a>
        </li>
        <li class="footer_li">
          <a href="/products" class="footer_link">المنتجات</a>
        </li>
      </ul>
    </div>
    <div class="footer_item">
      <h3 class="footer_title">الروابط</h3>
      <ul class="footer_list">
        <li class="footer_li">
          <a href="/services" class="footer_link">الخدمات</a>
        </li>
        <li class="footer_li">
          <a href="/about" class="footer_link">من نحن</a>
        </li>
        <li class="footer_li">
          <a href="/#features" class="footer_link">المنتجات المميزة</a>
        </li>
        <li class="footer_li">
          <a href="/#latest" class="footer_link">أحدث المنتجات</a>
        </li>
        <li class="footer_li">
          <a href="/contact" class="footer_link">اتصل بنا</a>
        </li>
      </ul>
    </div>
    <div class="footer_item">
      <h3 class="footer_title">اتصل بنا</h3>
      <ul class="footer_list">
        <li class="footer_li">
          <i class="fa-solid fa-phone footer-icon"></i>
          <span style="direction: ltr; display: inline-block;"><?php echo !empty($footerSettings['phone1']) ? htmlspecialchars($footerSettings['phone1']) : 'رقم التواصل الاول'; ?></span>
        </li>
        <?php if(!empty($footerSettings['phone2'])): ?>
        <li class="footer_li">
          <i class="fa-solid fa-phone footer-icon"></i>
          <span style="direction: ltr; display: inline-block;"><?php echo htmlspecialchars($footerSettings['phone2']); ?></span>
        </li>
        <?php endif; ?>
        <li class="footer_li">
          <i class="fa-solid fa-envelope footer-icon"></i>
          <span><?php echo !empty($footerSettings['email']) ? htmlspecialchars($footerSettings['email']) : 'MY-Store@gmail.com'; ?></span>
        </li>
        <li class="footer_li">
          <i class="fa-solid fa-location-dot footer-icon"></i>
          <span><?php echo !empty($footerSettings['address']) ? htmlspecialchars($footerSettings['address']) : 'المحافظات - مصر'; ?></span>
        </li>
      </ul>
    </div>
  </div>
  <p class="copyright container">جميع الحقوق محفوظة.MY Store &copy; 2025 - 2026</p>
</footer>

<script src="/Js/app.js"></script>
<script src="/Js/scroll.js"></script>
<script src="/Js/account.js"></script>

<script>
document.addEventListener('click', async function(e) {
    const btn = e.target.closest('.heart-action-btn');
    if (!btn) return;
    
    e.preventDefault();
    e.stopPropagation();
    
    const productId = btn.getAttribute('data-product-id');
    const icon = btn.querySelector('i');
    
    btn.style.transform = 'scale(1.3)';
    setTimeout(() => btn.style.transform = 'scale(1)', 200);

    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';
    
    try {
        const response = await fetch('/toggle-favorite', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ product_id: productId, csrf_token: csrfToken })
        });
        
        if (response.status === 401 || response.status === 403) {
            window.location.href = '/login';
            return;
        }
        
        const data = await response.json();
        
        if (data.success) {
            if (data.status === 'added') {
                icon.classList.remove('fa-regular');
                icon.classList.add('fa-solid');
            } else {
                icon.classList.remove('fa-solid');
                icon.classList.add('fa-regular');
            }
        }
    } catch (error) {
        console.error(error);
    }
});
</script>
<script>
document.getElementById('apply_promo_btn')?.addEventListener('click', async function() {
    const codeInput = document.getElementById('promo_code_input');
    const msgEl = document.getElementById('promo_message');
    const code = codeInput.value.trim();

    if (!code) {
        msgEl.textContent = 'يرجى إدخال الكود';
        msgEl.style.color = '#ef4444';
        return;
    }
    
    let cartIds = [];
    try {
        const cards = JSON.parse(localStorage.getItem("cards")) || [];
        cartIds = cards.map(c => parseInt(c.id));
    } catch(e) {}

    this.disabled = true;
    this.textContent = '...';

    try {
        const response = await fetch('/api/validate-coupon', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ code: code, cart_ids: cartIds })
        });
        const data = await response.json();

        if (data.success) {
            localStorage.setItem('activeCoupon', JSON.stringify(data.coupon));
            msgEl.textContent = data.message;
            msgEl.style.color = '#10b981';
            if (typeof updateTotalPrice === 'function') updateTotalPrice();
        } else {
            localStorage.removeItem('activeCoupon');
            msgEl.textContent = data.message;
            msgEl.style.color = '#ef4444';
            if (typeof updateTotalPrice === 'function') updateTotalPrice();
        }
    } catch (error) {
        msgEl.textContent = 'خطأ في الاتصال';
    } finally {
        this.disabled = false;
        this.textContent = 'تطبيق';
    }
});
</script>
</body>
</html>