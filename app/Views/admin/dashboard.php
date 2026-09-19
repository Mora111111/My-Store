<style>
/* تصميم هادئ وأنيق جداً للداش بورد */
.stats-grid-beautiful {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 24px;
    margin-bottom: 40px;
}
.b-card {
    background: #ffffff;
    border-radius: 24px;
    padding: 24px;
    display: flex;
    align-items: center;
    gap: 20px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.02);
    border: 1px solid #f1f5f9;
    transition: all 0.3s ease;
}
.b-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 25px rgba(0,0,0,0.06);
    border-color: #e2e8f0;
}
.b-icon-wrapper {
    width: 68px;
    height: 68px;
    border-radius: 20px;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 28px;
    flex-shrink: 0;
}
.b-info h3 {
    margin: 0 0 6px 0;
    font-size: 15px;
    color: #64748b;
    font-weight: 700;
}
.b-info p {
    margin: 0;
    font-size: 32px;
    font-weight: 900;
    color: #0f172a;
    line-height: 1;
}

/* ألوان مخصصة لكل كارت لراحة العين */
.c-products .b-icon-wrapper { background: #e0f2fe; color: #0284c7; }
.c-categories .b-icon-wrapper { background: #ede9fe; color: #7c3aed; }
.c-orders .b-icon-wrapper { background: #ffedd5; color: #ea580c; }
.c-comments .b-icon-wrapper { background: #fef3c7; color: #d97706; }
.c-messages .b-icon-wrapper { background: #fee2e2; color: #e11d48; }
.c-visitors .b-icon-wrapper { background: #dcfce7; color: #16a34a; }
</style>

<div class="stats-grid-beautiful">
  
  <div class="b-card c-products">
    <div class="b-icon-wrapper"><i class="fa-solid fa-boxes-stacked"></i></div>
    <div class="b-info">
      <h3>إجمالي المنتجات</h3>
      <p><?= $productsCount ?></p>
    </div>
  </div>

  <div class="b-card c-categories">
    <div class="b-icon-wrapper"><i class="fa-solid fa-layer-group"></i></div>
    <div class="b-info">
      <h3>الأقسام النشطة</h3>
      <p><?= $categoriesCount ?></p>
    </div>
  </div>

  <div class="b-card c-orders">
    <div class="b-icon-wrapper"><i class="fa-solid fa-bag-shopping"></i></div>
    <div class="b-info">
      <h3>طلبات الشراء</h3>
      <p><?= $ordersCount ?></p>
    </div>
  </div>

  <div class="b-card c-comments">
    <div class="b-icon-wrapper"><i class="fa-solid fa-comment-dots"></i></div>
    <div class="b-info">
      <h3>تعليقات العملاء</h3>
      <p><?= $commentsCount ?></p>
    </div>
  </div>

  <div class="b-card c-messages">
    <div class="b-icon-wrapper"><i class="fa-solid fa-envelope-open-text"></i></div>
    <div class="b-info">
      <h3>رسائل الزوار</h3>
      <p><?= $messagesCount ?></p>
    </div>
  </div>

  <div class="b-card c-visitors">
    <div class="b-icon-wrapper"><i class="fa-solid fa-globe"></i></div>
    <div class="b-info">
      <h3>المتصلين حالياً</h3>
      <p><?= $onlineUsersCount ?></p>
    </div>
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