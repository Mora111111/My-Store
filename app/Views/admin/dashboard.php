<style>
/* تصميم هادئ وأنيق للداش بورد */
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
    box-shadow: 0 6px 20px rgba(15, 23, 42, 0.06);
    border: 1px solid #eef2f7;
    transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
}

.b-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 16px 30px rgba(15, 23, 42, 0.10);
    border-color: #dbeafe;
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
    transition: transform 0.3s ease;
}

.b-card:hover .b-icon-wrapper {
    transform: scale(1.06);
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

/* ألوان مخصصة لكل كارت */
.c-products   .b-icon-wrapper { background: #e0f2fe; color: #0284c7; }
.c-categories .b-icon-wrapper { background: #ede9fe; color: #7c3aed; }
.c-orders     .b-icon-wrapper { background: #ffedd5; color: #ea580c; }
.c-comments   .b-icon-wrapper { background: #fef3c7; color: #d97706; }
.c-messages   .b-icon-wrapper { background: #ffe4e6; color: #e11d48; }
.c-visitors   .b-icon-wrapper { background: #dcfce7; color: #16a34a; }

/* كارت الترحيب */
.welcome-card {
    background: #ffffff;
    border: 1px solid #eef2f7;
    border-radius: 24px;
    padding: 28px 30px;
    box-shadow: 0 6px 20px rgba(15, 23, 42, 0.05);
    color: #334155;
    line-height: 1.9;
}

.welcome-card h2 {
    margin: 0 0 12px;
    font-size: 22px;
    font-weight: 900;
    color: #0f172a;
}

.welcome-card p {
    margin: 0 0 18px;
    color: #475569;
    font-size: 15px;
}

.welcome-card ul {
    margin: 0;
    padding: 0;
    list-style: none;
}

.welcome-card li {
    padding: 12px 16px;
    margin-bottom: 10px;
    background: #f8fafc;
    border: 1px solid #eef2f7;
    border-radius: 14px;
    color: #334155;
    transition: background 0.25s ease, border-color 0.25s ease;
}

.welcome-card li:hover {
    background: #ffffff;
    border-color: #dbeafe;
}

/* التجاوب مع الجوال */
@media (max-width: 640px) {
    .b-card { padding: 20px; gap: 16px; border-radius: 20px; }
    .b-icon-wrapper { width: 58px; height: 58px; border-radius: 18px; font-size: 24px; }
    .b-info p { font-size: 28px; }
    .welcome-card { padding: 22px; }
    .welcome-card h2 { font-size: 20px; }
}
</style>

<div class="stats-grid-beautiful">
  
  <div class="b-card c-products">
    <div class="b-icon-wrapper"><i class="fa-solid fa-boxes-stacked"></i></div>
    <div class="b-info">
      <h3><?= lang('total_products') ?></h3>
      <p><?= $productsCount ?></p>
    </div>
  </div>

  <div class="b-card c-categories">
    <div class="b-icon-wrapper"><i class="fa-solid fa-layer-group"></i></div>
    <div class="b-info">
      <h3><?= lang('active_categories') ?></h3>
      <p><?= $categoriesCount ?></p>
    </div>
  </div>

  <div class="b-card c-orders">
    <div class="b-icon-wrapper"><i class="fa-solid fa-bag-shopping"></i></div>
    <div class="b-info">
      <h3><?= lang('purchase_orders') ?></h3>
      <p><?= $ordersCount ?></p>
    </div>
  </div>

  <div class="b-card c-comments">
    <div class="b-icon-wrapper"><i class="fa-solid fa-comment-dots"></i></div>
    <div class="b-info">
      <h3><?= lang('customer_comments') ?></h3>
      <p><?= $commentsCount ?></p>
    </div>
  </div>

  <div class="b-card c-messages">
    <div class="b-icon-wrapper"><i class="fa-solid fa-envelope-open-text"></i></div>
    <div class="b-info">
      <h3><?= lang('visitor_messages') ?></h3>
      <p><?= $messagesCount ?></p>
    </div>
  </div>

  <div class="b-card c-visitors">
    <div class="b-icon-wrapper"><i class="fa-solid fa-globe"></i></div>
    <div class="b-info">
      <h3><?= lang('online_users') ?></h3>
      <p><?= $onlineUsersCount ?></p>
    </div>
  </div>

</div>

<div class="welcome-card">
  <h2><?= lang('store_tracking') ?> <?= htmlspecialchars(explode(' ', Session::get('user_name'))[0] ?? '') ?> 🛒</h2>
  <p><?= lang('dashboard_quick_stats') ?></p>
  <ul>
    <li><strong><?= lang('admin_products_title') ?></strong> <?= lang('admin_products_desc') ?></li>
    <li><strong><?= lang('admin_comments_title') ?></strong> <?= lang('admin_comments_desc') ?></li>
    <li><strong><?= lang('admin_orders_title') ?></strong> <?= lang('admin_orders_desc') ?></li>
    <li><strong><?= lang('admin_messages_title') ?></strong> <?= lang('admin_messages_desc') ?></li>
    <li><strong><?= lang('admin_users_title') ?></strong> <?= lang('admin_users_desc') ?></li>
    <li><strong><?= lang('admin_settings_title') ?></strong> <?= lang('admin_settings_desc') ?></li>
  </ul>
</div>