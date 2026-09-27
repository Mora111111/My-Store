<div class="card">
    <h2><i class="fa-solid fa-pen-to-square" style="color:#38bdf8; margin-left:8px;"></i><?= lang('title_edit_product') ?></h2>

    <?php if (!empty($product)): ?>
    <form action="/admin/products/update" method="POST" enctype="multipart/form-data">
      <?= CSRF::getField() ?>
      <input type="hidden" name="id" value="<?= $product['id']; ?>">

      <div class="form-group">
        <label><?= lang('lbl_product_name') ?></label>
        <input type="text" name="title" value="<?= htmlspecialchars($product['title']); ?>" required>
      </div>
      <div class="form-group">
        <label><?= lang('lbl_product_name_en') ?></label>
        <input type="text" name="title_en" value="<?= htmlspecialchars($product['title_en'] ?? ''); ?>" dir="ltr">
      </div>

      <div class="form-group">
        <label><?= lang('lbl_category') ?></label>
        <select name="category_class" required>
          <option value=""><?= lang('select_category') ?></option>
          <?php
          $cats = [
              "هواتف" => lang('cat_phones'),
              "جهاز لوحي" => lang('cat_tablets'),
              "لابتوب" => lang('cat_laptops'),
              "ساعات ذكية" => lang('cat_smartwatches'),
              "فلاشات" => lang('cat_flashdrives'),
              "كاميرات" => lang('cat_cameras'),
              "راوترات" => lang('cat_routers'),
              "اكسسوارات" => lang('cat_accessories'),
              "مستعمل" => lang('cat_used')
          ];
          foreach ($cats as $catValue => $catLabel) {
            $sel = ($product['category_class'] == $catValue) ? 'selected' : '';
            echo "<option value='$catValue' $sel>$catLabel</option>";
          }
          ?>
        </select>
      </div>

      <div class="form-group">
        <label><?= lang('lbl_price_egp') ?></label>
        <input type="number" name="price" step="0.01" min="0" value="<?= htmlspecialchars($product['price']); ?>" required>
      </div>
      <div class="form-group">
        <label><?= lang('lbl_old_price') ?></label>
        <input type="number" name="old_price" step="0.01" min="0" value="<?= htmlspecialchars($product['old_price'] ?? '0'); ?>">
      </div>
      <div class="form-group">
        <label><?= lang('lbl_quantity') ?></label>
        <input type="number" name="quantity" min="0" value="<?= htmlspecialchars($product['quantity'] ?? '10'); ?>" required>
      </div>
      <div class="form-group">
        <label><?= lang('lbl_description') ?></label>
        <textarea name="description" rows="8" required><?= htmlspecialchars($product['description'] ?? ''); ?></textarea>
      </div>
      <div class="form-group">
        <label><?= lang('lbl_description_en') ?></label>
        <textarea name="description_en" rows="8" dir="ltr"><?= htmlspecialchars($product['description_en'] ?? ''); ?></textarea>
      </div>

     <div class="form-group" style="margin-top: 20px;">
        <label><?= lang('lbl_manage_images') ?></label>
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-top: 10px;">
           <?php 
           $imgFields = [
               ['name' => 'image', 'db' => 'image_url', 'label' => lang('img_main')],
               ['name' => 'image_2', 'db' => 'image_2', 'label' => lang('img_2')],
               ['name' => 'image_3', 'db' => 'image_3', 'label' => lang('img_3')],
               ['name' => 'image_4', 'db' => 'image_4', 'label' => lang('img_4')]
           ];
           foreach($imgFields as $f):$currentImg = $product[$f['db']] ?? '';
           ?>
           <div style="border: 1px solid #e2e8f0; padding: 10px; border-radius: 8px; text-align: center; background: #f8fafc;">
               <p style="margin: 0 0 10px 0; font-size: 14px; font-weight: bold; color: #475569;"><?= $f['label']; ?></p>
               <div style="height: 100px; display: flex; align-items: center; justify-content: center; margin-bottom: 10px; background: #fff; border-radius: 4px; overflow: hidden;">
                   <?php if(!empty($currentImg)): ?>
                       <?php $displaySrc = strpos($currentImg, 'data:image') === 0 ? $currentImg : Product::getImageUrl($currentImg); ?>
                       <img src="<?= htmlspecialchars($displaySrc); ?>" onerror="this.onerror=null;this.src='<?= BASE_URL ?>images/logos/logo.png';" style="max-height: 100%; max-width: 100%; object-fit: contain;">
                   <?php else: ?>
                       <span style="color: #cbd5e1; font-size: 12px;"><?= lang('no_image') ?></span>
                   <?php endif; ?>
               </div>
               <input type="file" name="<?= $f['name']; ?>" accept="image/png, image/jpeg, image/webp" style="width: 100%; font-size: 12px;">
           </div>
           <?php endforeach; ?>
        </div>
     </div>

      <button type="submit" class="btn-submit" style="width:100%; display:block; margin-top:10px;"><i class="fa-solid fa-floppy-disk" style="margin-left:8px;"></i> <?= lang('btn_save_changes') ?></button>
      <a href="/admin/products" class="cancel-btn"><i class="fa-solid fa-arrow-right" style="margin-left:5px;"></i> <?= lang('btn_cancel_return') ?></a>
    </form>
    <?php endif; ?>
  </div>
</div>