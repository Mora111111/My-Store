<div class="card">
  <h2 style="margin-top:0;"><i class="fa-solid fa-globe"></i> <?= lang('online_visitors_title') ?></h2>
  <table>
    <tr>
      <th><?= lang('col_user') ?></th>
      <th><?= lang('col_ip') ?></th>
      <th><?= lang('col_country') ?></th>
      <th><?= lang('col_city') ?></th>
      <th><?= lang('col_last_activity') ?></th>
    </tr>
    <?php if (!empty($onlineUsers)): ?>
      <?php foreach ($onlineUsers as $user): ?>
      <tr>
        <td>
            <?php if (!empty($user['user_name'])): ?>
                <span class="user-badge"><i class="fa-solid fa-user-check"></i> <?= htmlspecialchars($user['user_name']) ?></span>
            <?php else: ?>
                <span class="guest-badge"><i class="fa-solid fa-user-secret"></i> <?= lang('guest_anonymous') ?></span>
            <?php endif; ?>
        </td>
        <td style="font-weight:600; color:#3b82f6;"><?= htmlspecialchars($user['ip_address'] ?? lang('unknown_ip')) ?></td>
        <td style="font-weight:500; color:#1e293b;"><?= htmlspecialchars($user['country'] ?? lang('undefined_location')) ?></td>
        <td style="font-weight:500; color:#1e293b;"><?= htmlspecialchars($user['city'] ?? lang('undefined_location')) ?></td>
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
          <?= lang('no_online_visitors') ?>
        </td>
      </tr>
    <?php endif; ?>
  </table>
</div>