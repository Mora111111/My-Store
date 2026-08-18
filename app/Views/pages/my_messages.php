<style>
    .messages_container { padding: 120px 0 60px; min-height: 80vh; background: #f9f9f9; }
    .msg_card { background: white; border-radius: 10px; padding: 20px; margin-bottom: 25px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); border-right: 5px solid var(--main-color); }
    .msg_header { display: flex; justify-content: space-between; margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 10px; }
    .msg_content { color: #555; line-height: 1.6; margin-bottom: 15px; }
    .bot_reply_box { background: #e3f2fd; padding: 15px; border-radius: 8px; border-right: 4px solid #2196f3; color: #0d47a1; }
    .no_reply { font-style: italic; color: #999; }
    .reply_icon { margin-left: 8px; color: #2196f3; }

    .new-message-card { background: white; border-radius: 10px; padding: 25px; margin-bottom: 25px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
    .new-message-card h3 { margin-top: 0; margin-bottom: 20px; color: var(--main-color); border-bottom: 2px solid #eee; padding-bottom: 10px; }
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; margin-bottom: 8px; font-weight: bold; color: #555; }
    .form-group input, .form-group textarea { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; outline: none; transition: 0.3s; font-family: inherit; }
    .form-group input:focus, .form-group textarea:focus { border-color: var(--main-color); }
    .btn-send { background: var(--main-color); color: #fff; border: none; padding: 12px 25px; border-radius: 5px; font-size: 16px; cursor: pointer; transition: 0.3s; }
    .btn-send:hover { opacity: 0.9; }
    .alert-success { background-color: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb; text-align: center; font-weight: bold; }
</style>

<section class="messages_container">
    <div class="container">
      <h2 style="margin-bottom: 30px; text-align: center;">صندوق الرسائل والردود</h2>

      <?php if(isset($_GET['success'])): ?>
          <div class="alert-success">تم إرسال رسالتك بنجاح! سيتم الرد عليها في أقرب وقت.</div>
      <?php endif; ?>

      <div class="new-message-card">
          <h3><i class="fa-solid fa-pen"></i> إرسال رسالة جديدة</h3>
          <form method="POST" action="/my-messages/send">
              <?= CSRF::getField() ?>
              <div class="form-group">
                  <label>الموضوع</label>
                  <input type="text" name="subject" placeholder="عنوان الرسالة" required>
              </div>
              <div class="form-group">
                  <label>الرسالة</label>
                  <textarea name="message" rows="4" placeholder="اكتب رسالتك هنا..." required></textarea>
              </div>
              <button type="submit" class="btn-send"><i class="fa-solid fa-paper-plane"></i> إرسال</button>
          </form>
      </div>

      <?php if (!empty($messages)): ?>
        <?php foreach ($messages as $row): ?>
          <div class="msg_card">
            <div class="msg_header">
              <strong><i class="fa-solid fa-envelope"></i> <?php echo htmlspecialchars($row['subject'] ?? 'بدون عنوان'); ?></strong>
              <small><?php echo isset($row['created_at']) ? date('Y-m-d', strtotime($row['created_at'])) : ''; ?></small>
            </div>

            <div class="msg_content">
              <?php echo nl2br(htmlspecialchars($row['message'])); ?>
            </div>

            <?php if (!empty($row['reply'])): ?>
              <div class="bot_reply_box">
                <strong><i class="fa-solid fa-robot reply_icon"></i> رد الدعم:</strong><br>
                <?php echo nl2br(htmlspecialchars($row['reply'])); ?>
              </div>
            <?php else: ?>
              <div class="no_reply">
                <i class="fa-solid fa-clock"></i> جاري مراجعة رسالتك من قبل فريق الدعم...
              </div>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div style="text-align: center; padding: 50px;">
            <i class="fa-regular fa-folder-open" style="font-size: 50px; color: #ccc;"></i>
            <p>لا توجد رسائل سابقة لديك.</p>
        </div>
      <?php endif; ?>

    </div>
</section>