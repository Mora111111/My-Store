<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center;">
      <h2 style="margin-bottom:0;"><i class="fa-solid fa-pen-to-square" style="color:#38bdf8; margin-left:8px;"></i><?= lang('title_edit_product') ?></h2>
      <button type="button" class="ai-magic-btn" id="openAiModal">
        <i class="fa-solid fa-wand-magic-sparkles"></i> Ai
      </button>
    </div>

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

  <div class="ai-modal-overlay" id="aiModal">
    <div class="ai-modal-content">
      <h3 style="margin-top:0;"><i class="fa-solid fa-robot" style="color:#8b5cf6;"></i> <?= lang('ai_assistant_title') ?></h3>
      <p style="font-size: 0.9rem; color:#64748b; margin-bottom: 5px;"><?= lang('ai_assistant_desc') ?></p>
      <input type="text" id="aiPrompt" value="<?= htmlspecialchars($product['title'] ?? ''); ?>" style="width:100%; padding:12px; margin:15px 0; border:1px solid #cbd5e1; border-radius:8px; box-sizing:border-box;">
      <div style="display:flex; gap:10px;">
        <button type="button" id="generateAiData" style="background:#8b5cf6; color:white; border:none; padding:10px 20px; border-radius:8px; cursor:pointer; font-weight:bold; flex:1;"><?= lang('btn_generate_data') ?></button>
        <button type="button" onclick="closeAiModal()" style="background:#e2e8f0; color:#475569; border:none; padding:10px 20px; border-radius:8px; cursor:pointer; font-weight:bold; flex:1;"><?= lang('btn_go_back') ?></button>
      </div>
    </div>
  </div>
<script>
function openAiModal() { document.getElementById('aiModal').style.display = 'flex'; }
function closeAiModal() { document.getElementById('aiModal').style.display = 'none'; }

document.getElementById('openAiModal').addEventListener('click', function() {
    document.getElementById('aiModal').style.display = 'flex';
});

document.getElementById('aiModal').addEventListener('click', function(e) {
    if (e.target === this) closeAiModal();
});

document.getElementById('generateAiData').addEventListener('click', async function() {
    const promptInput = document.getElementById('aiPrompt');
    const prompt = promptInput.value.trim();
    const btn = document.getElementById('generateAiData');
    
    const csrfToken = document.querySelector('input[name="csrf_token"]')?.value || '';
    
    if(!prompt) return;
    
    btn.textContent = '<?= lang('btn_generating') ?>';
    btn.disabled = true;
    
    try {
        const formData = new FormData();
        formData.append('prompt', prompt);
        formData.append('csrf_token', csrfToken);

        const response = await fetch('/ai/generate-product', {
            method: 'POST',
            body: formData 
        });
        
        const data = await response.json();
        
        if(data && !data.error) {
            const catVal = data.category_class || data.category;
            if(data.title) document.querySelector('input[name="title"]').value = data.title;
            if(data.title_en) document.querySelector('input[name="title_en"]').value = data.title_en;
            if(catVal) document.querySelector('select[name="category_class"]').value = catVal;
            const priceInput = document.querySelector('input[name="price"]');
            if(data.price && priceInput) {
                const currentPrice = parseFloat(priceInput.value);
                if (!currentPrice || currentPrice <= 0) {
                    priceInput.value = data.price;
                }
            }
            if(data.description) {
                const cleanText = data.description.replace(/<br\s*[\/]?>/gi, "\n").replace(/<\/p>/gi, "\n\n").replace(/<[^>]+>/ig, "");
                document.querySelector('textarea[name="description"]').value = cleanText.trim();
            }
            if(data.description_en) {
                const cleanTextEn = data.description_en.replace(/<br\s*[\/]?>/gi, "\n").replace(/<\/p>/gi, "\n\n").replace(/<[^>]+>/ig, "");
                document.querySelector('textarea[name="description_en"]').value = cleanTextEn.trim();
            }
            
            closeAiModal();
        } else {
            alert('<?= lang('alert_generation_failed') ?>' + (data.error || '<?= lang('alert_unknown_error') ?>'));
        }
    } catch (error) {
        alert('<?= lang('alert_server_error') ?>');
    } finally {
        btn.textContent = '<?= lang('btn_generate_data') ?>';
        btn.disabled = false;
    }
});
</script>
</div>