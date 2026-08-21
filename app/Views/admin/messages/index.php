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
              <button type="button" class="btn-reply" onclick="openReplyModal(<?php echo $row['id']; ?>, 'contact', <?php echo htmlspecialchars(json_encode($reply_text)); ?>)">
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
              <button type="button" class="btn-reply" onclick="openReplyModal(<?php echo $row['id']; ?>, 'user', <?php echo htmlspecialchars(json_encode($user_reply_text)); ?>)">
                <i class="fa-solid fa-pen-to-square"></i> <?php echo !empty($user_reply_text) ? 'تعديل الرد' : 'رد'; ?>
              </button>
              <form method="POST" action="/admin/messages/delete" style="display:inline-block;" onsubmit="return confirm('هل أنت متأكد من حذف هذه الرسالة؟');">
                <?= CSRF::getField() ?>
                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                <input type="hidden" name="type" value="user">
                <button type="submit" class="btn-delete" style="border:none; cursor:pointer; font-family:inherit;">
                  <i class="fa-solid fa-trash"></i> حذف
                </button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="6" style="text-align:center; padding:40px; color:#94a3b8; font-size:16px;"><i class="fa-solid fa-envelope-open" style="font-size:40px; margin-bottom:15px; opacity:0.5;"></i><br>لا توجد رسائل من المستخدمين.</td></tr>
      <?php endif; ?>
    </table>
</div>

<style>
.modal-overlay {
  display: none;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(15, 23, 42, 0.65);
  backdrop-filter: blur(5px);
  z-index: 9999;
  justify-content: center;
  align-items: center;
}
.modal-content-modern {
  background: #ffffff;
  width: 92%;
  max-width: 550px;
  border-radius: 16px;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
  animation: slideUpModal 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
  overflow: hidden;
  direction: rtl;
}
.modal-header-modern {
  padding: 20px 24px;
  background: #f8fafc;
  border-bottom: 1px solid #e2e8f0;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.modal-title-modern {
  margin: 0;
  font-size: 18px;
  color: #0f172a;
  display: flex;
  align-items: center;
  gap: 12px;
  font-weight: 700;
}
.icon-wrapper-modern {
  background: #e0f2fe;
  color: #0284c7;
  width: 38px;
  height: 38px;
  border-radius: 50%;
  display: flex;
  justify-content: center;
  align-items: center;
  font-size: 16px;
}
.close-btn-modern {
  color: #64748b;
  font-size: 20px;
  cursor: pointer;
  transition: all 0.2s;
  padding: 5px;
}
.close-btn-modern:hover {
  color: #ef4444;
  transform: rotate(90deg);
}
.modal-body-modern {
  padding: 24px;
}
.modal-desc-modern {
  color: #475569;
  font-size: 14px;
  margin-top: 0;
  margin-bottom: 20px;
}
.textarea-modern {
  width: 100%;
  min-height: 150px;
  padding: 16px;
  border: 2px solid #e2e8f0;
  border-radius: 12px;
  font-family: inherit;
  font-size: 14px;
  color: #1e293b;
  background: #f8fafc;
  resize: vertical;
  outline: none;
  transition: all 0.2s ease;
  box-sizing: border-box;
}
.textarea-modern:focus {
  border-color: #38bdf8;
  background: #ffffff;
  box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.15);
}
.modal-footer-modern {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  margin-top: 24px;
}
.btn-cancel-modern {
  padding: 12px 24px;
  background: #f1f5f9;
  color: #475569;
  border: none;
  border-radius: 10px;
  font-family: inherit;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: 0.2s;
}
.btn-cancel-modern:hover {
  background: #e2e8f0;
  color: #0f172a;
}
.btn-save-modern {
  padding: 12px 24px;
  background: linear-gradient(135deg, #0ea5e9, #0284c7);
  color: #ffffff;
  border: none;
  border-radius: 10px;
  font-family: inherit;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
  transition: all 0.2s;
  box-shadow: 0 4px 10px rgba(2, 132, 199, 0.3);
}
.btn-save-modern:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 15px rgba(2, 132, 199, 0.4);
}
@keyframes slideUpModal {
  from { opacity: 0; transform: translateY(30px) scale(0.95); }
  to { opacity: 1; transform: translateY(0) scale(1); }
}
</style>

<div id="replyModal" class="modal-overlay">
  <div class="modal-content-modern">
    <div class="modal-header-modern">
      <h3 class="modal-title-modern">
        <div class="icon-wrapper-modern">
          <i class="fa-solid fa-reply"></i>
        </div>
        إدارة الرد
      </h3>
      <i class="fa-solid fa-xmark close-btn-modern" onclick="closeReplyModal()"></i>
    </div>
    
    <div class="modal-body-modern">
      <p class="modal-desc-modern">قم بكتابة الرد يدوياً للعميل. سيتم إرسال هذا الرد وحفظه مباشرة.</p>
      
      <form method="POST" action="/admin/messages/reply" style="margin:0;">
        <?= CSRF::getField() ?>
        <input type="hidden" name="type" id="modal_msg_type" value="contact">
        <input type="hidden" name="msg_id" id="modal_msg_id">
        
        <textarea name="reply_text" id="modal_reply_text" class="textarea-modern" placeholder="اكتب ردك للعميل هنا..." required></textarea>
        
        <div class="modal-footer-modern">
          <button type="button" class="btn-cancel-modern" onclick="closeReplyModal()">إلغاء</button>
          <button type="submit" name="update_reply" class="btn-save-modern">
            <i class="fa-solid fa-paper-plane"></i> إرسال الرد
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
const modal = document.getElementById('replyModal');
const msgIdInput = document.getElementById('modal_msg_id');
const msgTypeInput = document.getElementById('modal_msg_type');
const replyTextInput = document.getElementById('modal_reply_text');

function openReplyModal(id, type, currentReply) {
  msgIdInput.value = id;
  msgTypeInput.value = type;
  replyTextInput.value = currentReply;
  modal.style.display = 'flex';
}

function closeReplyModal() { 
  modal.style.display = 'none'; 
}

window.onclick = function(event) {
  if (event.target == modal) closeReplyModal();
}
</script>