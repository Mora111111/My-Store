<div class="card">
    <h2 style="margin-top:0;"><i class="fa-solid fa-inbox"></i> الرسائل الواردة (الزوار)</h2>
    <table>
      <thead>
        <tr>
          <th>المرسل</th>
          <th>التواصل</th>
          <th>الرسالة</th>
          <th>الرد</th>
          <th>التاريخ</th>
          <th>الإجراءات</th>
        </tr>
      </thead>
      <tbody>
      <?php if (!empty($contact_messages)): ?>
        <?php foreach ($contact_messages as $row):
          $full_name = htmlspecialchars($row['first_name'] . ' ' . $row['last_name']);
          $reply_text = $row['reply'] ?? '';
          $status_badge = !empty($reply_text) ? '<span class="badge-success"><i class="fa-solid fa-check"></i> تم الرد</span>' : '<span class="badge-warning"><i class="fa-solid fa-clock"></i> معلق</span>';
        ?>
        <tr>
          <td><div style="font-weight:700; color:#1e293b;"><?= $full_name ?></div></td>
          <td>
            <div style="font-size:13px; color:#64748b;">
                <i class="fa-solid fa-envelope" style="width:15px;"></i> <?= htmlspecialchars($row['email']) ?><br>
                <i class="fa-solid fa-phone" style="width:15px;"></i> <?= htmlspecialchars($row['phone'] ?: '---') ?>
            </div>
          </td>
          <td><div class="message-content" title="<?= htmlspecialchars($row['message']) ?>"><?= nl2br(htmlspecialchars($row['message'])) ?></div></td>
          <td><?= $status_badge ?></td>
          <td><span class="date-badge"><?= date('Y-m-d', strtotime($row['created_at'])) ?></span></td>
          <td>
            <div class="actions-flex">
              <button type="button" class="btn-reply" onclick="openReplyModal(<?= $row['id'] ?>, 'contact', <?= htmlspecialchars(json_encode($reply_text)) ?>, <?= htmlspecialchars(json_encode($row['message'])) ?>)">
                <i class="fa-solid fa-reply"></i> <?= !empty($reply_text) ? 'تعديل' : 'رد' ?>
              </button>
              <form method="POST" action="/admin/messages/delete" style="display:inline;" onsubmit="return confirm('حذف هذه الرسالة؟');">
                <?= CSRF::getField() ?>
                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                <input type="hidden" name="type" value="contact">
                <button type="submit" class="btn-delete"><i class="fa-solid fa-trash"></i></button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="6" style="text-align:center; padding:40px; color:#94a3b8;">لا توجد رسائل زوار.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
</div>

<div class="card">
    <h2 style="margin-top:0;"><i class="fa-solid fa-user-tag"></i> تذاكر المستخدمين المسجلين</h2>
    <table>
      <thead>
        <tr>
          <th>المستخدم</th>
          <th>الموضوع</th>
          <th>الرسالة</th>
          <th>الرد</th>
          <th>التاريخ</th>
          <th>الإجراءات</th>
        </tr>
      </thead>
      <tbody>
      <?php if (!empty($user_messages)): ?>
        <?php foreach ($user_messages as $row):
          $reply_text = $row['reply'] ?? '';
          $status_badge = !empty($reply_text) ? '<span class="badge-success"><i class="fa-solid fa-check"></i> تم الرد</span>' : '<span class="badge-warning"><i class="fa-solid fa-clock"></i> معلق</span>';
        ?>
        <tr>
          <td><span class="user-badge"><i class="fa-solid fa-user"></i> ID: <?= $row['user_id'] ?></span></td>
          <td style="font-weight:600;"><?= htmlspecialchars($row['subject'] ?? 'بدون عنوان') ?></td>
          <td><div class="message-content"><?= nl2br(htmlspecialchars($row['message'])) ?></div></td>
          <td><?= $status_badge ?></td>
          <td><span class="date-badge"><?= date('Y-m-d', strtotime($row['created_at'])) ?></span></td>
          <td>
            <div class="actions-flex">
              <button type="button" class="btn-reply" onclick="openReplyModal(<?= $row['id'] ?>, 'user', <?= htmlspecialchars(json_encode($reply_text)) ?>, <?= htmlspecialchars(json_encode($row['message'])) ?>)">
                <i class="fa-solid fa-reply"></i> <?= !empty($reply_text) ? 'تعديل' : 'رد' ?>
              </button>
              <form method="POST" action="/admin/messages/delete" style="display:inline;" onsubmit="return confirm('حذف هذه التذكرة؟');">
                <?= CSRF::getField() ?>
                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                <input type="hidden" name="type" value="user">
                <button type="submit" class="btn-delete"><i class="fa-solid fa-trash"></i></button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="6" style="text-align:center; padding:40px; color:#94a3b8;">لا توجد تذاكر مستخدمين.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
</div>

<div id="replyModal" class="modal-overlay">
  <div class="modal-content-modern">
    <div class="modal-header-modern">
      <h3 class="modal-title-modern">
        <div class="icon-wrapper-modern"><i class="fa-solid fa-reply"></i></div>
        إدارة الرد على الرسالة
      </h3>
      <i class="fa-solid fa-xmark close-btn-modern" onclick="closeReplyModal()"></i>
    </div>
    <div class="modal-body-modern">
      <div style="background:#f8fafc; padding:15px; border-radius:12px; margin-bottom:20px; border-right:4px solid #3b82f6;">
        <strong style="color:#1e293b; font-size:13px;"><i class="fa-solid fa-envelope-open-text"></i> نص الرسالة الواردة:</strong>
        <p id="display_full_message" style="margin:8px 0 0 0; color:#475569; line-height:1.6; font-size:14px;"></p>
      </div>
      <form method="POST" action="/admin/messages/reply">
        <?= CSRF::getField() ?>
        <input type="hidden" name="type" id="modal_msg_type">
        <input type="hidden" name="msg_id" id="modal_msg_id">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
            <label style="font-weight:700; color:#1e293b;">اكتب ردك هنا:</label>
            <button type="button" id="aiMessageBtn" class="btn-ai-reply" style="margin-bottom:0;">
                <i class="fa-solid fa-wand-magic-sparkles"></i> اقتراح رد ذكي
            </button>
        </div>
        <textarea name="reply_text" id="modal_reply_text" class="textarea-modern" placeholder="اكتب الرد الرسمي للمتجر..." required></textarea>
        <div class="modal-footer-modern">
          <button type="button" class="btn-cancel-modern" onclick="closeReplyModal()">إلغاء</button>
          <button type="submit" class="btn-save-modern"><i class="fa-solid fa-paper-plane"></i> إرسال وحفظ الرد</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
const modal = document.getElementById('replyModal');
const aiBtn = document.getElementById('aiMessageBtn');

function openReplyModal(id, type, currentReply, messageText) {
  document.getElementById('modal_msg_id').value = id;
  document.getElementById('modal_msg_type').value = type;
  document.getElementById('modal_reply_text').value = currentReply;
  document.getElementById('display_full_message').innerText = messageText;
  modal.style.display = 'flex';
}

function closeReplyModal() { modal.style.display = 'none'; }

window.onclick = (e) => { if (e.target == modal) closeReplyModal(); }

aiBtn.addEventListener('click', async () => {
  const msgText = document.getElementById('display_full_message').innerText;
  const replyInput = document.getElementById('modal_reply_text');
  const csrfToken = document.querySelector('input[name="csrf_token"]').value;
  
  const originalHtml = aiBtn.innerHTML;
  aiBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> جاري التوليد...';
  aiBtn.disabled = true;
  
  try {
    const formData = new FormData();
    formData.append('prompt', msgText);
    formData.append('csrf_token', csrfToken);

    const res = await fetch('/ai/message-reply', { method: 'POST', body: formData });
    const data = await res.json();
    if (data.reply) replyInput.value = data.reply;
    else if (data.error) alert(data.error);
  } catch (e) {
    alert('خطأ في الاتصال بالذكاء الاصطناعي');
  } finally {
    aiBtn.innerHTML = originalHtml;
    aiBtn.disabled = false;
  }
});
</script>