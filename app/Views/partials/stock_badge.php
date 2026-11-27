<?php
[$stockClass, $stockLabel] = match (true) {
    $stockQty <= 0 => ['stock-badge-out', lang('stock_out_of_stock')],
    $stockQty <= 5 => ['stock-badge-low', sprintf(lang('stock_low_urgency'), $stockQty)],
    default => ['stock-badge-in', sprintf(lang('stock_in_stock'), $stockQty)],
};
?>
<div class="stock-availability">
    <span class="stock-badge <?= $stockClass ?>"><?= htmlspecialchars($stockLabel, ENT_QUOTES, 'UTF-8') ?></span>
</div>
