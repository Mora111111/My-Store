<style>
  .date-badge { color: #64748b; font-size: 13px; font-weight: 500; display: inline-flex; align-items: center; gap: 6px; background: #f8fafc; padding: 6px 12px; border-radius: 8px; border: 1px solid #e2e8f0; white-space: nowrap; }
  .user-badge { background: #e0f2fe; color: #0369a1; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; }
  .guest-badge { background: #f1f5f9; color: #64748b; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; }
</style>

<div class="card">
  <h2 style="margin-top:0;"><i class="fa-solid fa-globe"></i> الزوار المتصلين حالياً</h2>
  <table>
    <tr>
      <th>المستخدم</th>
      <th>عنوان الـ IP</th>
      <th>الدولة</th>
      <th>المدينة</th>
      <th>آخر نشاط</th>
    </tr>
    <?php if (!empty($onlineUsers)): ?>
      <?php foreach ($onlineUsers as $user): ?>
      <tr>
        <td>
            <?php if (!empty($user['user_name'])): ?>
                <span class="user-badge"><i class="fa-solid fa-user-check"></i> <?= htmlspecialchars($user['user_name']) ?></span>
            <?php else: ?>
                <span class="guest-badge"><i class="fa-solid fa-user-secret"></i> زائر مجهول</span>
            <?php endif; ?>
        </td>
        <td style="font-weight:600; color:#3b82f6;"><?= htmlspecialchars($user['ip_address'] ?? 'غير معروف') ?></td>
        <td style="font-weight:500; color:#1e293b;"><?= htmlspecialchars($user['country'] ?? 'غير محدد') ?></td>
        <td style="font-weight:500; color:#1e293b;"><?= htmlspecialchars($user['city'] ?? 'غير محدد') ?></td>
        <td>
          <span class="date-badge">
            <i class="fa-regular fa-clock"></i>
            <?= date('h:i A', $user['last_activity']) ?>
          </span>
        </td>
      </tr>
      <?php endforeach; ?>
    <?php else: ?>
      <tr>
        <td colspan="5" style="text-align:center; padding:40px; color:#94a3b8; font-size:16px;">
          <i class="fa-solid fa-user-slash" style="font-size:40px; margin-bottom:15px; opacity:0.5;"></i><br>
          لا يوجد زوار متصلين حالياً.
        </td>
      </tr>
    <?php endif; ?>
  </table>
</div>