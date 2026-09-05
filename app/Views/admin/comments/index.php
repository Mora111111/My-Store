<div class="card">
    <h2 style="margin-top:0;"><i class="fa-solid fa-comments"></i> إدارة تعليقات وتقييمات العملاء</h2>
    <table>
      <thead>
        <tr>
          <th>المنتج</th>
          <th>العميل</th>
          <th>التعليق والتقييم</th>
          <th>الحالة</th>
          <th>التاريخ</th>
          <th>الإجراءات</th>
        </tr>
      </thead>
      <tbody>
      <?php if (!empty($comments)): ?>
        <?php foreach ($comments as $row):
          $safe_name = htmlspecialchars($row['customer_name']);
          $safe_comment = htmlspecialchars($row['comment_text']);
          $safe_reply = htmlspecialchars($row['admin_reply'] ?? '');
          $status_badge = !empty($row['admin_reply']) ? "<span class='badge-success'><i class='fa-solid fa-circle-check'></i> تم الرد</span>" : "<span class='badge-warning'><i class='fa-solid fa-clock'></i> معلق</span>";
          $imgPath = !empty($row['image_url']) ? $row['image_url'] : 'uploads/default.png';
          $u_rating = isset($row['user_rating']) ? (int)$row['user_rating'] : 5;
        ?>
        <tr>
          <td>
            <div style="display:flex; align-items:center; gap:12px;">
              <img src="/<?php echo ltrim($imgPath, '/'); ?>" width="50" height="50" style="border-radius:10px; object-fit:cover; border:1px solid #e2e8f0; background:#fff;"> 
              <span style="font-weight:700; color:#1e293b; font-size:14px;"><?php echo htmlspecialchars($row['product_title']); ?></span>
            </div>
          </td>
          <td><span class="user-badge"><i class="fa-solid fa-user"></i> <?php echo $safe_name; ?></span></td>
          <td>
            <div style="margin-bottom:6px; color:#f1c40f; font-size:12px;">
                <?php for($i=1; $i<=5; $i++) echo $i <= $u_rating ? '<i class="fa-solid fa-star"></i>' : '<i class="fa-regular fa-star"></i>'; ?>
            </div>
            <div class="comment-text" title="<?php echo $safe_comment; ?>"><?php echo $safe_comment; ?></div>
          </td>
          <td><?php echo $status_badge; ?></td>
          <td><span class="date-badge"><?php echo date('Y-m-d', strtotime($row['created_at'])); ?></span></td>
          <td>
            <div class="actions-flex">
              <button onclick="openReplyModal(<?php echo $row['id']; ?>, '<?php echo $safe_name; ?>', <?php echo htmlspecialchars(json_encode($safe_comment)); ?>, <?php echo htmlspecialchars(json_encode($safe_reply)); ?>)" class="btn-reply">
                <i class="fa-solid fa-reply"></i> <?php echo !empty($safe_reply) ? 'تعديل' : 'رد'; ?>
              </button>
              <form method="POST" action="/admin/comments/delete" style="display:inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذا التعليق؟');">
                <?= CSRF::getField() ?>
                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                <button type="submit" class="btn-delete"><i class="fa-solid fa-trash"></i></button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="6" style="text-align:center; padding:50px; color:#94a3b8;">لا توجد تعليقات حتى الآن.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
</div>

<div id="replyModal" class="modal-overlay">
  <div class="modal-content-modern">
    <div class="modal-header-modern">
      <h3 class="modal-title-modern">
        <div class="icon-wrapper-modern"><i class="fa-solid fa-comment-dots"></i></div>
        الرد على تعليق العميل
      </h3>
      <i class="fa-solid fa-xmark close-btn-modern" onclick="closeReplyModal()"></i>
    </div>
    <div class="modal-body-modern">
      <div style="background:#f8fafc; padding:15px; border-radius:12px; margin-bottom:20px; border-right:4px solid #3b82f6;">
        <strong style="color:#1e293b; font-size:13px;">تعليق العميل (<span id="display_customer"></span>):</strong>
        <p id="display_comment" style="margin:8px 0 0 0; color:#475569; line-height:1.6; font-size:14px;"></p>
        <input type="hidden" id="hidden_comment_text">
      </div>
      <form action="/admin/comments/reply" method="POST">
        <?= CSRF::getField() ?>
        <input type="hidden" name="comment_id" id="modal_comment_id">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
          <label style="font-weight:700; color:#1e293b;">رد الإدارة الرسمي:</label>
          <button type="button" id="aiSuggestBtn" class="btn-ai-reply" style="margin-bottom:0;">
            <i class="fa-solid fa-wand-magic-sparkles"></i> اقتراح رد ذكي
          </button>
        </div>
        <textarea name="admin_reply" id="modal_admin_reply" class="textarea-modern" placeholder="اكتب ردك للعميل هنا..." required></textarea>
        <div class="modal-footer-modern">
          <button type="button" class="btn-cancel-modern" onclick="closeReplyModal()">إلغاء</button>
          <button type="submit" class="btn-save-modern"><i class="fa-solid fa-paper-plane"></i> حفظ ونشر الرد</button>
        </div>
      </form>
    </div>
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

window.onclick = (e) => { if (e.target == modal) closeReplyModal(); }

aiBtn.addEventListener('click', async () => {
  const commentText = document.getElementById('hidden_comment_text').value;
  const csrfToken = document.querySelector('input[name="csrf_token"]').value;
  
  const originalHtml = aiBtn.innerHTML;
  aiBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> جاري التفكير...';
  aiBtn.disabled = true;
  
  try {
    const formData = new FormData();
    formData.append('comment', commentText);
    formData.append('csrf_token', csrfToken);

    const res = await fetch('/ai/comment', { method: 'POST', body: formData });
    const data = await res.json();
    
    if (data.reply) replyInput.value = data.reply;
    else if (data.error) alert(data.error);
  } catch (e) {
    alert('حدث خطأ في الاتصال بالذكاء الاصطناعي');
  } finally {
    aiBtn.innerHTML = originalHtml;
    aiBtn.disabled = false;
  }
});
</script>