

<div class="stats-container">
  <div class="stat-card">
    <div class="stat-info">
      <h3>إجمالي المنتجات</h3>
      <p><?= $productsCount ?></p>
    </div>
    <i class="fa-solid fa-boxes-stacked stat-icon"></i>
  </div>
  <div class="stat-card">
    <div class="stat-info">
      <h3>طلبات الشراء</h3>
      <p><?= $ordersCount ?></p>
    </div>
    <i class="fa-solid fa-bag-shopping stat-icon"></i>
  </div>
  <div class="stat-card">
    <div class="stat-info">
      <h3>تعليقات العملاء</h3>
      <p><?= $commentsCount ?></p>
    </div>
    <i class="fa-solid fa-comment-dots stat-icon"></i>
  </div>
  <div class="stat-card">
    <div class="stat-info">
      <h3>رسائل الزوار</h3>
      <p><?= $messagesCount ?></p>
    </div>
    <i class="fa-solid fa-envelope-open-text stat-icon"></i>
  </div>
  <div class="stat-card">
    <div class="stat-info">
      <h3>المتصلين حالياً</h3>
      <p><?= $onlineUsersCount ?></p>
    </div>
    <i class="fa-solid fa-globe stat-icon"></i>
  </div>
</div>

<div class="welcome-card">
  <h2>متابعة المتجر <?= htmlspecialchars(explode(' ', Session::get('user_name'))[0]) ?> 🛒</h2>
  <p>شاشة الإحصائيات السريعة الخاصة بالمتجر يمكنك من خلال القائمة الجانبية التحكم الكامل في كل أجزاء الموقع :</p>
  <ul>
    <li><strong>إدارة المنتجات:</strong> إضافة منتجات جديدة وتعديل أو حذف المنتجات الحالية.</li>
    <li><strong>تعليقات العملاء:</strong> متابعة آراء العملاء على المنتجات والرد عليها باحترافية.</li>
    <li><strong>طلبات الشراء:</strong> متابعة الطلبات الجديدة التي قام بها العملاء وتجهيزها.</li>
    <li><strong>رسائل الزوار:</strong> قراءة استفسارات ورسائل العملاء الواردة من صفحة "اتصل بنا".</li>
    <li><strong>إدارة المستخدمين:</strong> إمكانية التحكم في الحسابات "حظر-فك الحظر-تحويل لمدير".</li>
    <li><strong>الإعدادات:</strong> التحكم في إعدادات الموقع الأساسية.</li>
  </ul>
</div>