<style>
.stats-container {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 25px;
  margin-bottom: 40px;
}
.stat-card {
  background: #ffffff;
  padding: 28px 20px;
  border-radius: 24px;
  box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05), 0 8px 10px -6px rgba(0,0,0,0.02);
  display: flex;
  align-items: center;
  justify-content: space-between;
  transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
  border: 1px solid #f1f5f9;
  position: relative;
  overflow: hidden;
}
.stat-card::after {
  content: '';
  position: absolute;
  top: 0;
  right: 0;
  width: 6px;
  height: 100%;
  background: currentColor;
  opacity: 0.7;
  border-radius: 6px 0 0 6px;
}
.stat-card:nth-child(1) { color: #10b981; }
.stat-card:nth-child(2) { color: #3b82f6; }
.stat-card:nth-child(3) { color: #f59e0b; }
.stat-card:nth-child(4) { color: #ef4444; }
.stat-card:nth-child(5) { color: #8b5cf6; }
.stat-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 25px 30px -12px rgba(0,0,0,0.15);
  border-color: #e2e8f0;
}
.stat-info h3 {
  margin: 0 0 10px 0;
  color: #64748b;
  font-size: 16px;
  font-weight: 600;
  text-transform: uppercase;
}
.stat-info p {
  margin: 0;
  font-size: 42px;
  font-weight: 800;
  color: #0f172a;
  line-height: 1.2;
}
.stat-icon {
  font-size: 48px;
  opacity: 0.85;
  transition: 0.3s;
  color: currentColor;
  filter: drop-shadow(0 4px 6px rgba(0,0,0,0.05));
}
.stat-card:hover .stat-icon {
  transform: scale(1.05);
  opacity: 1;
}
.welcome-card {
  background: #ffffff;
  padding: 35px 40px;
  border-radius: 28px;
  box-shadow: 0 15px 30px -10px rgba(0,0,0,0.05);
  border: 1px solid #f1f5f9;
}
.welcome-card h2 {
  margin-top: 0;
  color: #0f172a;
  font-size: 30px;
  font-weight: 700;
  margin-bottom: 20px;
  letter-spacing: -0.5px;
  display: flex;
  align-items: center;
  gap: 10px;
}
.welcome-card h2::after {
  content: '';
  flex: 1;
  height: 3px;
  background: linear-gradient(90deg, #38bdf8, transparent);
  border-radius: 10px;
}
.welcome-card p {
  color: #334155;
  font-size: 18px;
  margin-bottom: 25px;
  line-height: 1.7;
  font-weight: 500;
}
.welcome-card ul {
  list-style: none;
  padding: 0;
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 18px 25px;
}
.welcome-card li {
  color: #1e293b;
  font-size: 17px;
  padding: 8px 0;
  border-bottom: 1px dashed #e2e8f0;
  display: flex;
  align-items: center;
}
.welcome-card li::before {
  content: "\2728";
  margin-left: 12px;
  font-size: 18px;
  opacity: 0.9;
}
.welcome-card li strong {
  color: #0f172a;
  font-weight: 700;
  margin-left: 5px;
}
@media (max-width: 1200px) {
  .stats-container { grid-template-columns: repeat(3, 1fr); }
}
@media (max-width: 800px) {
  .stats-container { grid-template-columns: repeat(2, 1fr); }
}
</style>

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