<div class="card">
  <div style="display: flex; justify-content: space-between; align-items: center;">
    <h2 style="margin-bottom:0;"><i class="fa-solid fa-plus-circle"></i> <?= lang('title_add_new_product') ?></h2>
    <button onclick="openAddModal()" class="btn-submit" style="text-decoration:none; border:none; cursor:pointer;"><i class="fa-solid fa-plus"></i> <?= lang('btn_add_product') ?></button>
  </div>
</div>

<div class="card">
  <h2><i class="fa-solid fa-list"></i> <?= lang('title_current_products') ?></h2>
  <div style="margin-bottom:15px;"><input type="text" id="searchInput" placeholder="<?= lang('ph_search_product') ?>" style="padding:12px; width:100%; max-width:400px; border:1px solid #cbd5e1; border-radius:8px; outline:none; font-family:inherit;"></div>
  <table>
    <tr>
      <th><?= lang('col_image') ?></th>
      <th><?= lang('col_product_name') ?></th>
      <th><?= lang('col_category') ?></th>
      <th><?= lang('col_price') ?></th>
      <th><?= lang('col_actions') ?></th>
    </tr>
    <?php if (!empty($products)): ?>
      <?php foreach ($products as $row): ?>
      <tr>
        <td><img src="<?= Product::getImageUrl($row['image_url']) ?>" width="60" height="60" style="border-radius:12px; object-fit:cover; box-shadow:0 4px 6px rgba(0,0,0,0.05);"></td>        <td style="font-weight:500;"><?= htmlspecialchars($row['title']); ?></td>
        <td><span class="badge"><?= htmlspecialchars($row['category_class']); ?></span></td>
        <td style="font-weight:700; color:#0f172a;"><?= htmlspecialchars($row['price']); ?> <?= lang('currency_egp') ?></td>
        <td>
          <a href="/admin/products/edit?id=<?= $row['id']; ?>" class="action-btn btn-edit"><i class="fa-solid fa-pen"></i> <?= lang('btn_edit') ?></a>
          <form method="POST" action="/admin/products/delete" style="display:inline-block;" onsubmit="return confirm('<?= lang('confirm_delete_product') ?>');">
            <?= CSRF::getField() ?>
            <input type="hidden" name="id" value="<?= $row['id']; ?>">
            <button type="submit" class="action-btn btn-delete" style="cursor:pointer;"><i class="fa-solid fa-trash"></i> <?= lang('btn_delete') ?></button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
    <?php else: ?>
      <tr><td colspan="5" style="text-align:center; padding:40px; color:#94a3b8; font-size:16px;"><i class="fa-solid fa-box-open" style="font-size:40px; margin-bottom:15px; opacity:0.5;"></i><br><?= lang('no_products_added') ?></td></tr>
    <?php endif; ?>
  </table>
</div>

<div class="modal-overlay" id="addProductModal">
  <div class="modal-content" style="max-width: 900px; width: 95%; max-height: 90vh; overflow-y: auto;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
      <h2 style="margin:0; color:#0f172a;"><i class="fa-solid fa-plus-circle" style="color:#38bdf8; margin-left:8px;"></i><?= lang('title_add_new_product') ?></h2>
      <button type="button" class="ai-magic-btn" id="openAiModal">
        <i class="fa-solid fa-wand-magic-sparkles"></i> Ai
      </button>
    </div>

    <form action="/admin/products/store" method="POST" enctype="multipart/form-data">
      <?= CSRF::getField() ?>
      
      <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-bottom: 15px;">
      <div class="form-group">
        <label><?= lang('lbl_product_name') ?></label>
        <input type="text" name="title" required placeholder="<?= lang('ph_product_name') ?>">
      </div>

      <div class="form-group">
        <label><?= lang('lbl_category') ?></label>
        <select name="category_class" required>
          <option value=""><?= lang('select_category') ?></option>
          <option value="هواتف"><?= lang('cat_phones') ?></option>
          <option value="جهاز لوحي"><?= lang('cat_tablets') ?></option>
          <option value="لابتوب"><?= lang('cat_laptops') ?></option>
          <option value="ساعات ذكية"><?= lang('cat_smartwatches') ?></option>
          <option value="فلاشات"><?= lang('cat_flashdrives') ?></option>
          <option value="كاميرات"><?= lang('cat_cameras') ?></option>
          <option value="راوترات"><?= lang('cat_routers') ?></option>
          <option value="اكسسوارات"><?= lang('cat_accessories') ?></option>
          <option value="مستعمل"><?= lang('cat_used') ?></option>
        </select>
      </div>

      <div class="form-group">
        <label><?= lang('lbl_price_egp') ?></label>
        <input type="number" name="price" step="0.01" min="0" required placeholder="<?= lang('ph_price') ?>">
      </div>
      <div class="form-group">
        <label><?= lang('lbl_old_price') ?></label>
        <input type="number" name="old_price" step="0.01" min="0" value="0" placeholder="<?= lang('ph_old_price') ?>">
      </div>
      <div class="form-group">
        <label><?= lang('lbl_quantity') ?></label>
        <input type="number" name="quantity" min="0" value="10" required>
      </div>
      </div>

      <div class="form-group">
        <label><?= lang('lbl_description') ?></label>
        <textarea name="description" rows="6" required placeholder="<?= lang('ph_description') ?>"></textarea>
      </div>

      <div class="form-group">
        <label><?= lang('lbl_product_image') ?></label>
        <input type="file" name="image" accept="image/png, image/jpeg, image/gif, image/webp" required style="padding: 5px;">
      </div>
      <div class="form-group">
        <label><?= lang('lbl_additional_images') ?></label>
        <input type="file" name="image_2" accept="image/png, image/jpeg, image/gif, image/webp" style="padding: 5px; margin-bottom: 5px;">
        <input type="file" name="image_3" accept="image/png, image/jpeg, image/gif, image/webp" style="padding: 5px; margin-bottom: 5px;">
        <input type="file" name="image_4" accept="image/png, image/jpeg, image/gif, image/webp" style="padding: 5px;">
      </div>

      <div style="display:flex; gap:10px; margin-top:25px;">
        <button type="submit" class="btn-submit" style="flex:2;"><i class="fa-solid fa-plus" style="margin-left: 8px;"></i> <?= lang('btn_save_product') ?></button>
        <button type="button" class="btn-cancel" style="flex:1;" onclick="closeAddModal()"><?= lang('cancel_btn') ?></button>
      </div>
    </form>
  </div>
</div>

<div class="ai-modal-overlay" id="aiModal">
  <div class="ai-modal-content">
    <h3 style="margin-top:0;"><i class="fa-solid fa-robot" style="color:#8b5cf6;"></i> <?= lang('ai_assistant_title') ?></h3>
    <p style="font-size: 0.9rem; color: #64748b; margin-bottom: 5px;"><?= lang('ai_assistant_desc') ?></p>
    <input type="text" id="aiPrompt" placeholder="<?= lang('ph_ai_prompt') ?>" style="width:100%; padding:12px; margin:15px 0; border:1px solid #cbd5e1; border-radius:8px; box-sizing:border-box;">
    <div style="display:flex; gap:10px;">
      <button type="button" id="generateAiData" style="background:#8b5cf6; color:white; border:none; padding:10px 20px; border-radius:8px; cursor:pointer; font-weight:bold; flex:1;"><?= lang('btn_generate_data') ?></button>
      <button type="button" onclick="closeAiModal()" style="background:#e2e8f0; color:#475569; border:none; padding:10px 20px; border-radius:8px; cursor:pointer; font-weight:bold; flex:1;"><?= lang('btn_go_back') ?></button>
    </div>
  </div>
</div>
<script>
function openAddModal() { document.getElementById('addProductModal').style.display = 'flex'; }
function closeAddModal() { document.getElementById('addProductModal').style.display = 'none'; }

document.getElementById('openAiModal').addEventListener('click', () => {
    document.getElementById('aiModal').style.display = 'flex';
});
function closeAiModal() { document.getElementById('aiModal').style.display = 'none'; }

document.getElementById('generateAiData').addEventListener('click', async () => {
    const prompt = document.getElementById('aiPrompt').value.trim();
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
            if(data.title) document.querySelector('input[name="title"]').value = data.title;
            if(data.category_class) document.querySelector('select[name="category_class"]').value = data.category_class;
            if(data.price) document.querySelector('input[name="price"]').value = data.price;
            if(data.description) {
                let cleanText = data.description.replace(/<br\s*[\/]?>/gi, "\n").replace(/<\/p>/gi, "\n\n").replace(/<[^>]+>/ig, "");
                document.querySelector('textarea[name="description"]').value = cleanText.trim();
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

const searchInput = document.getElementById('searchInput');
if(searchInput) {
    searchInput.addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('table tr:not(:first-child)');
        rows.forEach(row => {
            let titleCell = row.querySelector('td:nth-child(2)');
            if(titleCell) {
                let text = titleCell.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            }
        });
    });
}
</script>