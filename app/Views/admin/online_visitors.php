<style>
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

.c-total .b-icon-wrapper { background: #e0f2fe; color: #0284c7; }
.c-egypt .b-icon-wrapper { background: #dcfce7; color: #16a34a; }
.c-foreign .b-icon-wrapper { background: #fff7ed; color: #ea580c; }

.geo-section {
    background: #ffffff;
    border: 1px solid #eef2f7;
    border-radius: 24px;
    padding: 24px;
    margin-bottom: 40px;
    box-shadow: 0 6px 20px rgba(15, 23, 42, 0.06);
}

.geo-section h3 {
    margin: 0 0 6px 0;
    font-size: 16px;
    font-weight: 800;
    color: #0f172a;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 10px;
}

.geo-section .geo-hint {
    margin: 0 0 16px 0;
    font-size: 13px;
    color: #94a3b8;
    font-weight: 600;
}

.geo-group {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 10px;
    margin-bottom: 12px;
}

.geo-group:last-child {
    margin-bottom: 0;
}

.geo-group-title {
    font-size: 12px;
    font-weight: 800;
    color: #475569;
    background: #f1f5f9;
    border-radius: 40px;
    padding: 5px 12px;
    white-space: nowrap;
}

.geo-pill {
    display: inline-flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 7px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 40px;
    padding: 6px 14px;
    font-size: 13px;
    font-weight: 700;
    color: #334155;
    box-sizing: border-box;
}

.geo-pill .geo-count {
    background: linear-gradient(135deg, #38bdf8, #2dd4bf);
    color: #0f172a;
    border-radius: 40px;
    min-width: 22px;
    text-align: center;
    padding: 1px 8px;
    font-size: 12px;
    font-weight: 800;
}

.geo-pill.is-egypt {
    background: #f0fdf4;
    border-color: #bbf7d0;
    color: #166534;
}

.geo-empty {
    font-size: 13px;
    color: #94a3b8;
    font-weight: 600;
}

.country-name {
    display: inline-flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 7px;
    font-weight: 700;
    color: #1e293b;
}

.country-code-badge {
    background: #e2e8f0;
    color: #334155;
    border-radius: 6px;
    padding: 2px 8px;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.5px;
    direction: ltr;
}

.gov-badge {
    display: inline-flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 6px;
    background: linear-gradient(135deg, #38bdf8, #2dd4bf);
    color: #0f172a;
    border-radius: 40px;
    padding: 4px 12px;
    font-size: 12px;
    font-weight: 800;
    box-shadow: 0 2px 8px rgba(56, 189, 248, 0.28);
}

.gov-city {
    display: block;
    font-size: 11px;
    color: #64748b;
    font-weight: 600;
    margin-top: 3px;
}

.page-chip {
    display: inline-flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 7px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 5px 10px;
    font-size: 12px;
    font-weight: 600;
    color: #475569;
    max-width: 100%;
    box-sizing: border-box;
}

.page-path {
    direction: ltr;
    unicode-bidi: embed;
    overflow-wrap: anywhere;
}

.device-chip {
    display: inline-flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    font-weight: 700;
    color: #475569;
}

.rel-time {
    display: block;
    font-size: 11px;
    font-weight: 700;
    color: #0ea5e9;
}

.ip-text {
    direction: ltr;
    display: inline-block;
    unicode-bidi: embed;
}
</style>

<div class="stats-grid-beautiful">
  <div class="b-card c-total">
    <div class="b-icon-wrapper"><i class="fa-solid fa-globe"></i></div>
    <div class="b-info">
      <h3><?= lang('stats_total_online') ?></h3>
      <p><?= (int)$totalOnline ?></p>
    </div>
  </div>
  <div class="b-card c-egypt">
    <div class="b-icon-wrapper"><i class="fa-solid fa-location-dot"></i></div>
    <div class="b-info">
      <h3><?= lang('stats_egypt_visitors') ?></h3>
      <p><?= (int)$egyptCount ?></p>
    </div>
  </div>
  <div class="b-card c-foreign">
    <div class="b-icon-wrapper"><i class="fa-solid fa-earth-americas"></i></div>
    <div class="b-info">
      <h3><?= lang('stats_foreign_visitors') ?></h3>
      <p><?= (int)$foreignCount ?></p>
    </div>
  </div>
</div>

<div class="geo-section">
  <h3><i class="fa-solid fa-chart-simple" style="color:#38bdf8;"></i> <?= lang('geo_breakdown_title') ?></h3>
  <p class="geo-hint"><?= lang('online_visitors_title') ?></p>
  <div class="geo-group">
    <span class="geo-group-title"><?= lang('stats_egypt_visitors') ?></span>
    <?php if (!empty($governorateBreakdown)): ?>
      <?php foreach ($governorateBreakdown as $govName => $govCount): ?>
        <span class="geo-pill is-egypt">
          <i class="fa-solid fa-map-pin" style="color:#16a34a;"></i>
          <span><?= htmlspecialchars($govName) ?></span>
          <span class="geo-count"><?= (int)$govCount ?></span>
        </span>
      <?php endforeach; ?>
    <?php else: ?>
      <span class="geo-empty"><?= lang('no_online_visitors') ?></span>
    <?php endif; ?>
  </div>
  <div class="geo-group">
    <span class="geo-group-title"><?= lang('stats_foreign_visitors') ?></span>
    <?php if (!empty($countryBreakdown)): ?>
      <?php foreach ($countryBreakdown as $countryName => $countryCount): ?>
        <span class="geo-pill">
          <i class="fa-solid fa-earth-americas" style="color:#ea580c;"></i>
          <span><?= htmlspecialchars($countryName) ?></span>
          <span class="geo-count"><?= (int)$countryCount ?></span>
        </span>
      <?php endforeach; ?>
    <?php else: ?>
      <span class="geo-empty"><?= lang('no_online_visitors') ?></span>
    <?php endif; ?>
  </div>
</div>

<div class="card">
  <h2 style="margin-top:0;"><i class="fa-solid fa-users-viewfinder"></i> <?= lang('online_visitors_title') ?></h2>
  <table>
    <tr>
      <th><?= lang('col_user') ?></th>
      <th><?= lang('col_country') ?></th>
      <th><?= lang('col_governorate') ?></th>
      <th><?= lang('col_page_device') ?></th>
      <th><?= lang('col_ip') ?></th>
      <th><?= lang('col_last_activity') ?></th>
    </tr>
    <?php if (!empty($onlineUsers)): ?>
      <?php foreach ($onlineUsers as $user): ?>
        <?php
        $code = strtoupper(trim((string)($user['country_code'] ?? '')));
        $country = trim((string)($user['country'] ?? ''));
        if ($country === '' || $country === 'غير محدد') {
            $country = lang('undefined_location');
        }
        $region = trim((string)($user['region'] ?? ''));
        if ($region === '' || $region === 'غير محدد') {
            $region = lang('undefined_location');
        }
        $city = trim((string)($user['city'] ?? ''));
        if ($city === '' || $city === $region || $city === 'غير محدد') {
            $city = '';
        }
        $isEgypt = ($code === 'EG') || ($country === 'مصر');
        $device = (string)($user['device_type'] ?? '');
        $isMobile = ($device === 'Mobile');
        $page = (string)($user['current_page'] ?? '/');
        if ($page === '') {
            $page = '/';
        }
        $isAdminPage = (strpos($page, '/admin') === 0);
        $pageLabel = $isAdminPage ? lang('admin_panel_page') : $page;
        $elapsed = time() - (int)($user['last_activity'] ?? 0);
        if ($elapsed < 60) {
            $relative = vsprintf(lang('time_seconds_ago'), $elapsed);
        } elseif ($elapsed < 3600) {
            $relative = vsprintf(lang('time_minutes_ago'), (int)floor($elapsed / 60));
        } else {
            $relative = date('h:i A', (int)($user['last_activity'] ?? 0));
        }
        ?>
      <tr>
        <td>
            <?php if (!empty($user['user_name'])): ?>
                <span class="user-badge"><i class="fa-solid fa-user-check"></i> <?= htmlspecialchars($user['user_name']) ?></span>
            <?php else: ?>
                <span class="guest-badge"><i class="fa-solid fa-user-secret"></i> <?= lang('guest_anonymous') ?></span>
            <?php endif; ?>
        </td>
        <td>
          <span class="country-name">
            <span><?= htmlspecialchars($country) ?></span>
            <?php if ($code !== ''): ?>
              <span class="country-code-badge"><?= htmlspecialchars($code) ?></span>
            <?php endif; ?>
          </span>
        </td>
        <td>
          <?php if ($isEgypt && $region !== lang('undefined_location')): ?>
            <span class="gov-badge"><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($region) ?></span>
            <?php if ($city !== ''): ?>
              <span class="gov-city"><?= htmlspecialchars($city) ?></span>
            <?php endif; ?>
          <?php else: ?>
            <span class="country-name">
              <span><?= htmlspecialchars($region) ?></span>
            </span>
            <?php if ($city !== '' && $city !== $region): ?>
              <span class="gov-city"><?= htmlspecialchars($city) ?></span>
            <?php endif; ?>
          <?php endif; ?>
        </td>
        <td>
          <span class="page-chip">
            <span class="device-chip">
              <i class="fa-solid <?= $isMobile ? 'fa-mobile-screen-button' : 'fa-desktop' ?>"></i>
              <span><?= $isMobile ? lang('device_mobile') : lang('device_desktop') ?></span>
            </span>
            <span class="page-path"><?= htmlspecialchars($pageLabel) ?></span>
          </span>
        </td>
        <td>
          <span class="ip-text" style="font-weight:600; color:#3b82f6;"><?= htmlspecialchars($user['ip_address'] ?? lang('unknown_ip')) ?></span>
        </td>
        <td>
          <span class="rel-time"><?= htmlspecialchars($relative) ?></span>
          <span class="date-badge">
            <i class="fa-regular fa-clock"></i>
            <?= date('h:i A', (int)($user['last_activity'] ?? 0)) ?>
          </span>
        </td>
      </tr>
      <?php endforeach; ?>
    <?php else: ?>
      <tr>
        <td colspan="6" style="text-align:center; padding:40px; color:#94a3b8; font-size:16px;">
          <i class="fa-solid fa-user-slash" style="font-size:40px; margin-bottom:15px; opacity:0.5;"></i><br>
          <?= lang('no_online_visitors') ?>
        </td>
      </tr>
    <?php endif; ?>
  </table>
</div>
