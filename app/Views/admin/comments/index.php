<style>
  .btn-reply { background: #3b82f6; color: white; border: none; padding: 8px 14px; border-radius: 8px; cursor: pointer; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; transition: 0.3s; box-shadow: 0 4px 8px rgba(59,130,246,0.15); font-family: 'Tajawal', sans-serif; }
  .btn-reply:hover { background: #2563eb; transform: translateY(-1px); }
  .btn-delete { background: #ef4444; color: white; border: none; padding: 8px 14px; border-radius: 8px; cursor: pointer; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; transition: 0.3s; box-shadow: 0 4px 8px rgba(239,68,68,0.15); font-family: 'Tajawal', sans-serif; text-decoration: none; }
  .btn-delete:hover { background: #dc2626; transform: translateY(-1px); }
  .btn-ai-reply { background: linear-gradient(135deg, #a78bfa, #c084fc); color: white; border: none; padding: 10px 18px; cursor: pointer; border-radius: 40px; font-size: 14px; font-weight: 600; display: flex; align-items: center; gap: 8px; margin-bottom: 10px; transition: 0.3s; box-shadow: 0 4px 10px rgba(167,139,250,0.3); font-family: 'Tajawal', sans-serif; }
  .btn-ai-reply:hover { opacity: 0.9; transform: translateY(-1px); }
  .btn-ai-reply:disabled { background: #cbd5e1; cursor: not-allowed; opacity: 1; transform: none; box-shadow: none; }
  .comment-text { color: #334155; line-height: 1.6; font-size: 14px; margin-bottom: 5px; }
  .admin-reply-box { font-size: 14px; color: #0f766e; background: #ccfbf1; padding: 12px 15px; border-radius: 12px 0 12px 12px; border-right: 4px solid #0d9488; margin-top: 10px; display: block; box-shadow: 0 4px 6px -1px rgba(13, 148, 136, 0.1); line-height: 1.6; position: relative; }
  .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center; backdrop-filter: blur(3px); }
  .modal-content { background: white; padding: 25px; border-radius: 12px; width: 90%; max-width: 500px; position: relative; box-shadow: 0 10px 25px rgba(0,0,0,0.2); }
  .close-modal { position: absolute; top: 15px; left: 15px; font-size: 20px; cursor: pointer; color: #94a3b8; transition: 0.2s; }
  .close-modal:hover { color: #ef4444; }
  #modal_admin_reply { width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-family: 'Tajawal', sans-serif; resize: vertical; box-sizing: border-box; }
  #modal_admin_reply:focus { outline: none; border-color: #38bdf8; }
  
  .badge-success { background-color: #d1fae5; color: #065f46; padding: 6px 14px; border-radius: 50px; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 5px; }
  .badge-warning { background-color: #fef3c7; color: #92400e; padding: 6px 14px; border-radius: 50px; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 5px; }
  .date-badge { color: #64748b; font-size: 13px; font-weight: 500; display: inline-flex; align-items: center; gap: 6px; background: #f8fafc; padding: 6px 12px; border-radius: 8px; border: 1px solid #e2e8f0; white-space: nowrap; }
  .actions-flex { display: flex; items-content: center; gap: 8px; }
</style>

<div class="card">
    <h2 style="margin-top:0;"><i class="fa-solid fa-list"></i> أحدث التعليقات</h2>
    <table>
      <tr>
        <th>المنتج</th>
        <th>اسم العميل</th>
        <th>التعليق والرد</th>
        <th>التاريخ</th>
        <th>الحالة</th>
        <th>الإجراءات</th>
      </tr>
      <?php if (!empty($comments)): ?>
        <?php foreach ($comments as $row):
          $safe_name = htmlspecialchars($row['customer_name']);
          $safe_comment = htmlspecialchars($row['comment_text']);
          $safe_reply = htmlspecialchars($row['admin_reply'] ?? '');
          $status_badge = !empty($row['admin_reply']) ? "<span class='badge-success'><i class='fa-solid fa-circle-check'></i> تم الرد</span>" : "<span class='badge-warning'><i class='fa-solid fa-circle-minus'></i> بانتظار الرد</span>";
          $escaped_comment_js = addslashes($safe_comment);
          $escaped_reply_js = addslashes($safe_reply);
          
          $imgPath = !empty($row['image_url']) ? $row['image_url'] : 'uploads/default.png';
        ?>
        <tr>
          <td>
            <div style="display:flex; align-items:center; gap:10px;">
              <img src="/<?php echo ltrim($imgPath, '/'); ?>" width="45" height="45" style="border-radius:10px; object-fit:cover; box-shadow:0 4px 6px rgba(0,0,0,0.05);"> 
              <span style="font-weight:600; color:#1e293b;"><?php echo htmlspecialchars($row['product_title']); ?></span>
            </div>
          </td>
          <td style="font-weight:600; color:#3b82f6;"><?php echo $safe_name; ?></td>
          <td>
            <div class="comment-text"><?php echo nl2br($safe_comment); ?></div>
            <?php if (!empty($safe_reply)): ?>
              <div class="admin-reply-box">
                <div style="font-weight: 700; font-size: 12px; margin-bottom: 6px; display: flex; align-items: center; gap: 5px; color: #0d9488;">
                  <i class="fa-solid fa-headset"></i> رد الإدارة:
                </div>
                <?php echo nl2br($safe_reply); ?>
              </div>
            <?php endif; ?>
          </td>
          <td>
            <span class="date-badge">
              <i class="fa-regular fa-calendar-days"></i>
              <?php echo isset($row['created_at']) ? date('Y-m-d', strtotime($row['created_at'])) : ''; ?>
            </span>
          </td>
          <td><?php echo $status_badge; ?></td>
          <td>
            <div class="actions-flex">
              <button onclick="openReplyModal(<?php echo $row['id']; ?>, '<?php echo $safe_name; ?>', '<?php echo $escaped_comment_js; ?>', '<?php echo $escaped_reply_js; ?>')" class="btn-reply">
                <i class="fa-solid fa-pen-to-square"></i> <?php echo !empty($safe_reply) ? 'تعديل الرد' : 'رد'; ?>
              </button>
              <a href="/admin/comments/delete?id=<?php echo $row['id']; ?>" class="btn-delete" onclick="return confirm('هل أنت متأكد من حذف هذا التعليق؟');">
                <i class="fa-solid fa-trash"></i> حذف
              </a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="6" style="text-align:center; padding:40px; color:#94a3b8; font-size:16px;"><i class="fa-solid fa-comment-slash" style="font-size:40px; margin-bottom:15px; opacity:0.5;"></i><br>لا توجد تعليقات حتى الآن.</td></tr>
      <?php endif; ?>
    </table>
  </div>

<div id="replyModal" class="modal-overlay">
  <div class="modal-content">
    <i class="fa-solid fa-xmark close-modal" onclick="closeReplyModal()"></i>
    <h3 style="margin-top:0; color:#0f172a; font-size:20px; display:flex; align-items:center; gap:10px;">
      <i class="fa-solid fa-headset" style="color:#38bdf8;"></i> الرد على العميل
    </h3>
    <div style="background:#f8fafc; padding:15px; border-radius:12px; margin-bottom:20px; border-right:4px solid #3b82f6;">
      <strong style="color:#1e293b; font-size:14px;">تعليق العميل (<span id="display_customer"></span>):</strong>
      <p id="display_comment" style="margin:8px 0 0 0; color:#334155; line-height:1.6; font-size:14px;"></p>
      <input type="hidden" id="hidden_comment_text">
    </div>
    <form action="/admin/comments/reply" method="POST">
      <?= CSRF::getField() ?>
      <input type="hidden" name="comment_id" id="modal_comment_id">
      <div class="form-group">
        <div style="display:flex; justify-content:space-between; align-items:flex-end;">
          <label style="margin-bottom:0; font-weight:600; color:#334155;">رد الإدارة:</label>
          <button type="button" id="aiSuggestBtn" class="btn-ai-reply">
            <i class="fa-solid fa-wand-magic-sparkles"></i> اقتراح رد ذكي
          </button>
        </div>
        <textarea name="admin_reply" id="modal_admin_reply" rows="4" placeholder="اكتب ردك للعميل هنا..." required></textarea>
      </div>
      <button type="submit" name="submit_reply" class="btn-submit" style="width:100%; margin-top:15px;"><i class="fa-solid fa-floppy-disk" style="margin-left:8px;"></i> حفظ ونشر الرد</button>
    </form>
  </div>
</div>

<script>
const modal = document.getElementById('replyModal');
const aiBtn = document.getElementById('aiSuggestBtn');
const replyInput = document.getElementById('modal_admin_reply');

function openReplyModal(id, customerName, commentText, currentReply) {
  document.getElementById('modal_comment_id').value = id;
  document.getElementById('display_customer').innerText = customerName;
  document.getElementById('display_comment').innerText = commentText;
  document.getElementById('hidden_comment_text').value = commentText;
  replyInput.value = currentReply;
  modal.style.display = 'flex';
}

function closeReplyModal() { modal.style.display = 'none'; }

window.onclick = function(event) {
  if (event.target == modal) closeReplyModal();
}

aiBtn.addEventListener('click', async function() {
  const commentText = document.getElementById('hidden_comment_text').value;
  const csrfToken = document.querySelector('input[name="csrf_token"]')?.value || '';
  
  const originalBtnText = aiBtn.innerHTML;
  aiBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> جاري التفكير...';
  aiBtn.disabled = true;
  
  try {
    const formData = new FormData();
    formData.append('comment', commentText);
    formData.append('csrf_token', csrfToken);

    const response = await fetch('/ai/comment', {
      method: 'POST',
      body: formData
    });
    
    if (!response.ok) throw new Error('حدث خطأ في الاتصال بالسيرفر');
    const data = await response.json();
    
    if (data.error) throw new Error(data.error);
    if (data.reply) replyInput.value = data.reply;
    
  } catch (error) {
    alert(error.message);
  } finally {
    aiBtn.innerHTML = originalBtnText;
    aiBtn.disabled = false;
  }
});
</script>