
<div class="card">
    <h2 style="margin-top:0;"><i class="fa-solid fa-inbox"></i> الرسائل الواردة (جهات الاتصال)</h2>
    <table>
      <tr>
        <th>الاسم</th>
        <th>البريد الإلكتروني</th>
        <th>الرسالة</th>
        <th>الرد المسجل</th>
        <th>التاريخ</th>
        <th style="min-width:120px;">الإجراءات</th>
      </tr>
      <?php if (!empty($contact_messages)): ?>
        <?php foreach ($contact_messages as $row):
          $full_name = htmlspecialchars($row['first_name'] . ' ' . $row['last_name']);
          $reply_text = !empty($row['reply']) ? htmlspecialchars($row['reply']) : "";
          
          if (empty($reply_text)) {
              $display_reply = "<span class='badge-warning'><i class='fa-regular fa-clock'></i> بانتظار الرد</span>";
          } else {
              $display_reply = "<div class='admin-reply-box'><div style='font-weight: 700; font-size: 12px; margin-bottom: 6px; display: flex; align-items: center; gap: 5px; color: #0d9488;'><i class='fa-solid fa-headset'></i> رد الإدارة:</div>" . nl2br($reply_text) . "</div>";
          }
        ?>
        <tr>
          <td style="font-weight:600; color:#1e293b;"><?php echo $full_name; ?><br><small style="color:#64748b; font-weight:normal;"><i class="fa-solid fa-phone" style="font-size:10px;"></i> <?php echo empty($row['phone']) ? 'بدون هاتف' : htmlspecialchars($row['phone']); ?></small></td>
          <td><a href="mailto:<?php echo htmlspecialchars($row['email']); ?>" style="color:#3b82f6; text-decoration:none; font-weight:500;"><?php echo htmlspecialchars($row['email']); ?></a></td>
          <td><div class="message-content"><?php echo nl2br(htmlspecialchars($row['message'])); ?></div></td>
          <td><?php echo $display_reply; ?></td>
          <td>
            <span class="date-badge">
              <i class="fa-regular fa-calendar-days"></i>
              <?php echo isset($row['created_at']) ? date('Y-m-d', strtotime($row['created_at'])) : ''; ?>
            </span>
          </td>
          <td>
            <div class="actions-flex">
              <button type="button" class="btn-reply" onclick="openReplyModal(<?php echo $row['id']; ?>, 'contact', <?php echo htmlspecialchars(json_encode($reply_text)); ?>, <?php echo htmlspecialchars(json_encode($row['message'])); ?>)">
                <i class="fa-solid fa-pen-to-square"></i> <?php echo !empty($reply_text) ? 'تعديل الرد' : 'رد'; ?>
              </button>
              <a href="/admin/messages/delete?id=<?php echo $row['id']; ?>&type=contact" class="btn-delete" onclick="return confirm('هل أنت متأكد من حذف هذه الرسالة؟');">
                <i class="fa-solid fa-trash"></i> حذف
              </a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="6" style="text-align:center; padding:40px; color:#94a3b8; font-size:16px;"><i class="fa-solid fa-envelope-open" style="font-size:40px; margin-bottom:15px; opacity:0.5;"></i><br>لا توجد رسائل جديدة حتى الآن.</td></tr>
      <?php endif; ?>
    </table>
  </div>

<div class="card">
    <h2 style="margin-top:0;"><i class="fa-solid fa-comments"></i> رسائل المستخدمين</h2>
    <table>
      <tr>
        <th>المستخدم</th>
        <th>الموضوع</th>
        <th>الرسالة</th>
        <th>الرد المسجل</th>
        <th>التاريخ</th>
        <th style="min-width:120px;">الإجراءات</th>
      </tr>
      <?php if (!empty($user_messages)): ?>
        <?php foreach ($user_messages as $row):
          $user_reply_text = !empty($row['reply']) ? htmlspecialchars($row['reply']) : "";
          
          if (empty($user_reply_text)) {
              $user_display_reply = "<span class='badge-warning'><i class='fa-regular fa-clock'></i> بانتظار الرد</span>";
          } else {
              $user_display_reply = "<div class='admin-reply-box'><div style='font-weight: 700; font-size: 12px; margin-bottom: 6px; display: flex; align-items: center; gap: 5px; color: #0d9488;'><i class='fa-solid fa-headset'></i> رد الإدارة:</div>" . nl2br($user_reply_text) . "</div>";
          }
        ?>
        <tr>
          <td style="font-weight:600; color:#3b82f6;"><i class="fa-solid fa-user-astronaut" style="margin-left:5px; color:#94a3b8;"></i><?php echo htmlspecialchars($row['user_id']); ?></td>
          <td style="font-weight:600; color:#1e293b;"><?php echo htmlspecialchars($row['subject'] ?? ''); ?></td>
          <td><div class="message-content"><?php echo nl2br(htmlspecialchars($row['message'])); ?></div></td>
          <td><?php echo $user_display_reply; ?></td>
          <td>
            <span class="date-badge">
              <i class="fa-regular fa-calendar-days"></i>
              <?php echo isset($row['created_at']) ? date('Y-m-d', strtotime($row['created_at'])) : ''; ?>
            </span>
          </td>
          <td>
            <div class="actions-flex">
              <button type="button" class="btn-reply" onclick="openReplyModal(<?php echo $row['id']; ?>, 'user', <?php echo htmlspecialchars(json_encode($user_reply_text)); ?>, <?php echo htmlspecialchars(json_encode($row['message'])); ?>)">
                <i class="fa-solid fa-pen-to-square"></i> <?php echo !empty($user_reply_text) ? 'تعديل الرد' : 'رد'; ?>
              </button>
              <a href="/admin/messages/delete?id=<?php echo $row['id']; ?>&type=user" class="btn-delete" onclick="return confirm('هل أنت متأكد من حذف هذه الرسالة؟');">
                <i class="fa-solid fa-trash"></i> حذف
              </a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="6" style="text-align:center; padding:40px; color:#94a3b8; font-size:16px;"><i class="fa-solid fa-envelope-open" style="font-size:40px; margin-bottom:15px; opacity:0.5;"></i><br>لا توجد رسائل من المستخدمين.</td></tr>
      <?php endif; ?>
    </table>
  </div>

<div id="replyModal" class="modal-overlay">
  <div class="modal-content">
    <i class="fa-solid fa-xmark close-modal" onclick="closeReplyModal()"></i>
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
      <h3 style="color:#0f172a; margin:0; font-size:20px; display:flex; align-items:center; gap:8px;">
        <i class="fa-solid fa-reply" style="color:#38bdf8;"></i> إدارة الرد
      </h3>
      <button type="button" id="aiReplyBtn" class="btn-ai-reply">
        توليد الرد ذكياً <i class="fa-solid fa-wand-magic-sparkles"></i>
      </button>
    </div>
    <p style="color:#64748b; font-size:14px; margin-bottom:15px;">قم بكتابة الرد يدوياً أو استخدم المساعد الذكي.</p>
    
    <form method="POST" action="/admin/messages/reply">
      <?= CSRF::getField() ?>
      <input type="hidden" name="type" id="modal_msg_type" value="contact">
      <input type="hidden" name="msg_id" id="modal_msg_id">
      <textarea name="reply_text" id="modal_reply_text" placeholder="اكتب ردك للعميل هنا..." required></textarea>
      <button type="submit" name="update_reply" class="btn-save"><i class="fa-solid fa-paper-plane" style="margin-left:5px;"></i> إرسال الرد</button>
    </form>
  </div>
</div>

<script>
const modal = document.getElementById('replyModal');
const msgIdInput = document.getElementById('modal_msg_id');
const msgTypeInput = document.getElementById('modal_msg_type');
const replyTextInput = document.getElementById('modal_reply_text');
const aiReplyBtn = document.getElementById('aiReplyBtn');
let currentMessageText = "";

function openReplyModal(id, type, currentReply, messageText) {
  msgIdInput.value = id;
  msgTypeInput.value = type;
  replyTextInput.value = currentReply;
  currentMessageText = messageText;
  modal.style.display = 'flex';
}

function closeReplyModal() { modal.style.display = 'none'; }

window.onclick = function(event) {
  if (event.target == modal) closeReplyModal();
}

if (aiReplyBtn) {
  aiReplyBtn.addEventListener('click', async function() {
    const originalText = this.innerHTML;
    this.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> جاري التفكير...';
    this.disabled = true;
    
    const csrfToken = document.querySelector('input[name="csrf_token"]')?.value || '';
    const promptText = "قم بالرد باحترافية ولباقة كخدمة عملاء لمتجر إلكتروني على رسالة العميل التالية: " + currentMessageText;
    
    try {
      const formData = new FormData();
      formData.append('prompt', promptText);
      formData.append('csrf_token', csrfToken);

      const response = await fetch('/ai/message-reply', {
        method: 'POST',
        body: formData
      });
      
      const data = await response.json();
      
      if (data.reply) replyTextInput.value = data.reply;
      else if (data.description) replyTextInput.value = data.description; 
      else if (data.error) alert("حدث خطأ من الخادم: " + data.error);
    } catch(e) {
      alert('خطأ في الاتصال بالمساعد الذكي.');
    } finally {
      this.innerHTML = originalText;
      this.disabled = false;
    }
  });
}
</script>