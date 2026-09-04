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
                <i class="fa-solid fa-pen-to-square"></i> <?php echo !empty($reply_text) ? 'عرض وتعديل' : 'قراءة ورد'; ?>
              </button>
              <form method="POST" action="/admin/messages/delete" style="display:inline-block;" onsubmit="return confirm('هل أنت متأكد من حذف هذه الرسالة؟');">
                <?= CSRF::getField() ?>
                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                <input type="hidden" name="type" value="contact">
                <button type="submit" class="btn-delete" style="border:none; cursor:pointer; font-family:inherit;">
                  <i class="fa-solid fa-trash"></i> حذف
                </button>
              </form>
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
                <i class="fa-solid fa-pen-to-square"></i> <?php echo !empty($user_reply_text) ? 'عرض وتعديل' : 'قراءة ورد'; ?>
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
      
      <div style="background:#f8fafc; padding:15px; border-radius:12px; margin-bottom:20px; border-right:4px solid #3b82f6;">
        <strong style="color:#1e293b; font-size:14px;"><i class="fa-solid fa-envelope-open-text"></i> نص الرسالة كاملاً:</strong>
        <p id="display_full_message" style="margin:8px 0 0 0; color:#334155; line-height:1.6; font-size:14px;"></p>
      </div>

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

function openReplyModal(id, type, currentReply, messageText) {
  msgIdInput.value = id;
  msgTypeInput.value = type;
  replyTextInput.value = currentReply;
  document.getElementById('display_full_message').innerText = messageText;
  modal.style.display = 'flex';
}

function closeReplyModal() { 
  modal.style.display = 'none'; 
}

window.onclick = function(event) {
  if (event.target == modal) closeReplyModal();
}
</script>