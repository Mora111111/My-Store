<style>
/* فرض استجابة حديثة لشبكة الإحصائيات تتكيف مع جميع الشاشات */
.modern-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 25px;
    margin-bottom: 40px;
}
.stat-card-modern {
    background: #ffffff;
    padding: 25px;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.03);
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: all 0.3s ease;
    border: 1px solid #f1f5f9;
    position: relative;
    overflow: hidden;
}
.stat-card-modern:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.08);
}
.stat-card-modern::after {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 5px;
    height: 100%;
    background: var(--card-color);
    border-radius: 5px 0 0 5px;
}
.stat-info-modern h3 {
    margin: 0 0 8px 0;
    color: #64748b;
    font-size: 15px;
    font-weight: 700;
}
.stat-info-modern p {
    margin: 0;
    font-size: 38px;
    font-weight: 900;
    color: #0f172a;
    line-height: 1;
}
.stat-icon-modern {
    font-size: 45px;
    color: var(--card-color);
    opacity: 0.15;
    transition: 0.3s;
    position: absolute;
    left: 20px;
}
.stat-card-modern:hover .stat-icon-modern {
    transform: scale(1.1) rotate(-5deg);
    opacity: 0.8;
}
</style>

<div class="modern-stats-grid">
  <!-- كارت المنتجات -->
  <div class="stat-card-modern" style="--card-color: #10b981;">
    <div class="stat-info-modern">
      <h3>إجمالي المنتجات</h3>
      <p><?= $productsCount ?></p>
    </div>
    <i class="fa-solid fa-boxes-stacked stat-icon-modern"></i>
  </div>

  <!-- كارت الأقسام (الجديد) -->
  <div class="stat-card-modern" style="--card-color: #8b5cf6;">
    <div class="stat-info-modern">
      <h3>الأقسام النشطة</h3>
      <p><?= $categoriesCount ?></p>
    </div>
    <i class="fa-solid fa-layer-group stat-icon-modern"></i>
  </div>

  <!-- كارت الطلبات -->
  <div class="stat-card-modern" style="--card-color: #3b82f6;">
    <div class="stat-info-modern">
      <h3>طلبات الشراء</h3>
      <p><?= $ordersCount ?></p>
    </div>
    <i class="fa-solid fa-bag-shopping stat-icon-modern"></i>
  </div>

  <!-- كارت التعليقات -->
  <div class="stat-card-modern" style="--card-color: #f59e0b;">
    <div class="stat-info-modern">
      <h3>تعليقات العملاء</h3>
      <p><?= $commentsCount ?></p>
    </div>
    <i class="fa-solid fa-comment-dots stat-icon-modern"></i>
  </div>

  <!-- كارت الرسائل -->
  <div class="stat-card-modern" style="--card-color: #ef4444;">
    <div class="stat-info-modern">
      <h3>رسائل الزوار</h3>
      <p><?= $messagesCount ?></p>
    </div>
    <i class="fa-solid fa-envelope-open-text stat-icon-modern"></i>
  </div>

  <!-- كارت المتصلين -->
  <div class="stat-card-modern" style="--card-color: #06b6d4;">
    <div class="stat-info-modern">
      <h3>المتصلين حالياً</h3>
      <p><?= $onlineUsersCount ?></p>
    </div>
    <i class="fa-solid fa-globe stat-icon-modern"></i>
  </div>
</div>

<div class="welcome-card">
  <h2>متابعة المتجر <?= htmlspecialchars(explode(' ', Session::get('user_name'))[0] ?? '') ?> 🛒</h2>
  <p>شاشة الإحصائيات السريعة الخاصة بالمتجر يمكنك من خلال القائمة الجانبية التحكم الكامل في كل أجزاء الموقع :</p>
  <ul>
    <li><strong>إدارة المنتجات والأقسام:</strong> إضافة منتجات جديدة للأقسام المتاحة أو حذفها والتعديل عليها.</li>
    <li><strong>تعليقات العملاء:</strong> متابعة آراء العملاء على المنتجات والرد عليها باحترافية لتفعيل الثقة.</li>
    <li><strong>طلبات الشراء:</strong> متابعة الطلبات الجديدة التي قام بها العملاء وتحديث حالتها.</li>
    <li><strong>رسائل الزوار:</strong> قراءة استفسارات ورسائل العملاء الواردة من صفحة "اتصل بنا".</li>
    <li><strong>إدارة المستخدمين:</strong> إمكانية التحكم في الحسابات (حظر، فك الحظر، ترقية مدير).</li>
    <li><strong>الإعدادات:</strong> التحكم في إعدادات الموقع الأساسية ومظهر المتجر وبوابات الدفع.</li>
  </ul>
</div>