<div class="card">
  <h2><i class="fa-solid fa-triangle-exclamation"></i> <?= lang('out_of_stock_title') ?> (<?= (int)($outOfStockCount ?? 0) ?>)</h2>
  <?php if (!empty($outOfStockProducts)): ?>
  <table>
    <tr>
      <th><?= lang('col_product') ?></th>
      <th><?= lang('col_category') ?></th>
      <th><?= lang('col_quantity') ?></th>
      <th><?= lang('btn_restock') ?></th>
    </tr>
    <?php foreach ($outOfStockProducts as $item): ?>
    <tr>
      <td>
        <span style="display: inline-flex; align-items: center; gap: 10px;">
          <img src="<?= htmlspecialchars($item['image_url']) ?>" width="40" height="40" style="border-radius:8px; object-fit:cover;">
          <span style="font-weight:500;"><?= htmlspecialchars($item['title']) ?></span>
        </span>
      </td>
      <td><span class="badge"><?= htmlspecialchars($item['category_class']) ?></span></td>
      <td style="font-weight:700; color:#ef4444;">0</td>
      <td>
        <a href="/admin/products/edit?id=<?= $item['id'] ?>" class="action-btn btn-edit"><i class="fa-solid fa-pen"></i> <?= lang('update_quantity') ?></a>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
  <?php else: ?>
  <div style="text-align:center; padding:40px; color:#059669; font-size:16px; font-weight:bold;"><i class="fa-solid fa-circle-check" style="font-size:40px; margin-bottom:15px; opacity:0.7;"></i><br><?= lang('stock_ok_msg') ?></div>
  <?php endif; ?>
</div>
