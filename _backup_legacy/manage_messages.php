<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

require_once 'db.php';

if (isset($_GET['delete'])) {
    $delete_id = mysqli_real_escape_string($conn, $_GET['delete']);
    mysqli_query($conn, "DELETE FROM contact_messages WHERE id = '$delete_id'");
    header("Location: manage_messages.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_reply'])) {
    $msg_id = intval($_POST['msg_id']);
    $new_reply = mysqli_real_escape_string($conn, trim($_POST['reply_text']));
    mysqli_query($conn, "UPDATE contact_messages SET reply = '$new_reply' WHERE id = '$msg_id'");
    header("Location: manage_messages.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>رسائل الزوار - لوحة التحكم</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Tajawal', sans-serif;
            background: #f8fafc;
            display: flex;
            height: 100vh;
            overflow: hidden;
            color: #1e293b;
        }

         .sidebar {
            width: 280px;
            background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
            color: #f1f5f9;
            padding: 30px 0;
            display: flex;
            flex-direction: column;
            box-shadow: 5px 0 20px rgba(0,0,0,0.08);
            position: relative;
            z-index: 10;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 40px;
            font-size: 26px;
            font-weight: 700;
            letter-spacing: 1px;
            background: linear-gradient(135deg, #38bdf8, #2dd4bf);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            padding: 0 15px;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            color: #cbd5e1;
            padding: 14px 25px;
            margin: 4px 12px;
            text-decoration: none;
            border-radius: 12px;
            transition: all 0.3s ease;
            font-size: 16px;
            font-weight: 500;
            position: relative;
            overflow: hidden;
        }

        .sidebar a i {
            margin-left: 15px;
            width: 24px;
            text-align: center;
            font-size: 18px;
            transition: 0.2s;
        }

        .sidebar a span {
            flex: 1;
        }

        .sidebar a:hover {
            background: rgba(56, 189, 248, 0.15);
            color: #ffffff;
            transform: translateX(-5px);
        }

        .sidebar a.active {
            background: linear-gradient(135deg, #38bdf8, #2dd4bf);
            color: #0f172a;
            font-weight: 700;
            box-shadow: 0 8px 16px rgba(56, 189, 248, 0.3);
        }

        .sidebar a.active i {
            color: #0f172a;
        }

         .main-content {
            flex: 1;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            background: #f1f5f9;
        }

         .header {
            background: #ffffff;
            padding: 20px 40px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.02);
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e2e8f0;
        }

        .header h3 {
            font-size: 24px;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: -0.5px;
        }

        .logout-btn {
            background: #ffffff;
            color: #ef4444;
            border: 1.5px solid #fee2e2;
            padding: 12px 24px;
            cursor: pointer;
            border-radius: 40px;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.25s;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 2px 6px rgba(239, 68, 68, 0.05);
        }

        .logout-btn i {
            font-size: 16px;
        }

        .logout-btn:hover {
            background: #ef4444;
            color: white;
            border-color: #ef4444;
            box-shadow: 0 8px 16px rgba(239, 68, 68, 0.2);
            transform: translateY(-2px);
        }

         .content-area {
            padding: 35px 40px;
        }

         .card {
            background: #ffffff;
            padding: 30px 35px;
            border-radius: 28px;
            box-shadow: 0 15px 30px -10px rgba(0,0,0,0.05);
            border: 1px solid #f1f5f9;
            margin-bottom: 35px;
            transition: 0.2s;
        }

        .card h2 {
            color: #0f172a;
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card h2 i {
            color: #38bdf8;
        }

         .messages-table {
            width: 100%;
            border-collapse: collapse;
            text-align: right;
            margin-top: 15px;
        }

        .messages-table th {
            background: #f8fafc;
            color: #475569;
            font-weight: 600;
            padding: 16px 12px;
            border-bottom: 2px solid #e2e8f0;
            font-size: 15px;
        }

        .messages-table td {
            padding: 16px 12px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
        }

        .action-btn {
            padding: 8px 16px;
            border-radius: 30px;
            text-decoration: none;
            color: #fff;
            font-size: 14px;
            margin-left: 8px;
            display: inline-block;
            font-weight: 500;
            transition: all 0.2s;
            cursor: pointer;
            border: none;
            font-family: 'Tajawal', sans-serif;
        }

        .btn-delete {
            background: #ef4444;
            box-shadow: 0 4px 8px rgba(239, 68, 68, 0.2);
        }

        .btn-delete:hover {
            background: #dc2626;
            transform: translateY(-1px);
        }

        .btn-edit {
            background: #10b981;
            box-shadow: 0 4px 8px rgba(16, 185, 129, 0.2);
        }

        .btn-edit:hover {
            background: #059669;
            transform: translateY(-1px);
        }

        .message-content {
            max-width: 250px;
            line-height: 1.6;
            color: #334155;
        }

        .reply-content {
            max-width: 250px;
            line-height: 1.6;
            color: #0f766e;
            background: #f0fdfa;
            padding: 10px;
            border-radius: 8px;
            border-right: 3px solid #0d9488;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
        }

        .modal-content {
            background-color: #fff;
            margin: 8% auto;
            padding: 30px;
            border-radius: 20px;
            width: 90%;
            max-width: 500px;
            position: relative;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .close-modal {
            position: absolute;
            top: 20px;
            left: 25px;
            font-size: 24px;
            cursor: pointer;
            color: #94a3b8;
            transition: 0.2s;
        }

        .close-modal:hover {
            color: #ef4444;
        }

        .modal textarea {
            width: 100%;
            height: 150px;
            padding: 15px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-family: 'Tajawal', sans-serif;
            font-size: 15px;
            resize: vertical;
            margin-top: 15px;
            outline: none;
            transition: 0.3s;
        }

        .modal textarea:focus {
            border-color: #38bdf8;
        }

        .btn-save {
            background: linear-gradient(135deg, #38bdf8, #2dd4bf);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 15px;
            width: 100%;
            font-family: 'Tajawal', sans-serif;
            transition: 0.3s;
        }

        .btn-save:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        .main-content::-webkit-scrollbar {
            width: 8px;
        }
        .main-content::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        .main-content::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 20px;
        }
        .main-content::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>مدير المتجر</h2>
        <a href="dashboard.php"><i class="fa-solid fa-house"></i><span>الرئيسية</span></a>
        <a href="manage_products.php"><i class="fa-solid fa-box-open"></i><span>إدارة المنتجات</span></a>
        <a href="manage_comments.php"><i class="fa-solid fa-comments"></i><span>تعليقات العملاء</span></a>
        <a href="manage_orders.php"><i class="fa-solid fa-cart-shopping"></i><span>طلبات الشراء</span></a>
        <a href="manage_messages.php" class="active"><i class="fa-solid fa-envelope"></i><span>رسائل الزوار</span></a>
        <a href="manage_users.php"><i class="fa-solid fa-users"></i><span>إدارة المستخدمين</span></a>

    </div>

    <div class="main-content">
        <div class="header">
            <h3><i class="fa-solid fa-envelope" style="margin-left: 12px; color: #38bdf8;"></i>رسائل الزوار والاستفسارات</h3>
            <a href="logout.php" class="logout-btn">
                <i class="fa-solid fa-right-from-bracket"></i>
                تسجيل الخروج
            </a>
        </div>

        <div class="content-area">
            <div class="card">
                <h2 style="margin-top:0;"><i class="fa-solid fa-inbox"></i> الرسائل الواردة</h2>
                <table class="messages-table">
                    <tr>
                        <th>الاسم</th>
                        <th>البريد الإلكتروني</th>
                        <th>الرسالة</th>
                        <th>الرد المسجل</th>
                        <th>التاريخ</th>
                        <th style="min-width: 180px;">الإجراءات</th>
                    </tr>
                    <?php
                    $messages_query = mysqli_query($conn, "SELECT * FROM contact_messages ORDER BY id DESC");
                    
                    if (mysqli_num_rows($messages_query) > 0) {
                        while ($row = mysqli_fetch_assoc($messages_query)) {

                            $full_name = htmlspecialchars($row['first_name'] . ' ' . $row['last_name']);
                            $reply_text = !empty($row['reply']) ? htmlspecialchars($row['reply']) : "";
                            $display_reply = !empty($reply_text) ? nl2br($reply_text) : '<span style="color:#94a3b8; font-style:italic;">لا يوجد رد حتى الآن</span>';
                            
                            echo "<tr>";
                            echo "<td style='font-weight:500;'>{$full_name}<br><small style='color:#64748b;'>" . (empty($row['phone']) ? 'بدون هاتف' : htmlspecialchars($row['phone'])) . "</small></td>";
                            echo "<td><a href='mailto:{$row['email']}' style='color:#3b82f6; text-decoration:none; font-weight:500;'>{$row['email']}</a></td>";
                            echo "<td class='message-content'>" . nl2br(htmlspecialchars($row['message'])) . "</td>";
                            
                            echo "<td><div class='reply-content'>{$display_reply}</div></td>";
                            
                            echo "<td><span style='color:#64748b; font-size:14px;'>" . date('Y-m-d', strtotime($row['created_at'])) . "</span></td>";
                            echo "<td>
<button class='action-btn btn-edit' onclick='openReplyModal({$row['id']}, " . json_encode($reply_text) . ", " . json_encode($row['message']) . ")'><i class='fa-solid fa-pen'></i> تعديل الرد</button>                                    <a href='manage_messages.php?delete={$row['id']}' class='action-btn btn-delete' onclick='return confirm(\"هل أنت متأكد من حذف هذه الرسالة؟\");'><i class='fa-solid fa-trash'></i> حذف</a>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6' style='text-align:center; padding:40px; color:#94a3b8; font-size:16px;'><i class='fa-solid fa-envelope-open' style='font-size:40px; margin-bottom:15px; opacity:0.5;'></i><br>لا توجد رسائل جديدة حتى الآن.</td></tr>";
                    }
                    ?>
                </table>
            </div>
        </div>
    </div>

<div id="replyModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeReplyModal()"><i class="fa-solid fa-xmark"></i></span>
            
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h3 style="color: #0f172a; margin: 0; font-size: 20px;"><i class="fa-solid fa-reply" style="color: #38bdf8; margin-left: 8px;"></i>إدارة الرد</h3>
                <button type="button" id="aiReplyBtn" class="action-btn" style="background: linear-gradient(135deg, #a78bfa, #c084fc); border: none; font-weight: bold; padding: 8px 16px;">
                    توليد الرد ذكياً <i class="fa-solid fa-wand-magic-sparkles"></i>
                </button>
            </div>
            
            <p style="color: #64748b; font-size: 14px;">قم بكتابة الرد يدوياً أو اضغط على الزر ليقوم المساعد الذكي بكتابته بدلاً منك.</p>
            
            <form method="POST" action="">
                <input type="hidden" name="msg_id" id="modal_msg_id">
                <textarea name="reply_text" id="modal_reply_text" placeholder="اكتب ردك هنا..." required></textarea>
                <button type="submit" name="update_reply" class="btn-save"><i class="fa-solid fa-check" style="margin-left: 5px;"></i> حفظ الرد</button>
            </form>
        </div>
    </div>

<script>
        const modal = document.getElementById('replyModal');
        const msgIdInput = document.getElementById('modal_msg_id');
        const replyTextInput = document.getElementById('modal_reply_text');
        const aiReplyBtn = document.getElementById('aiReplyBtn');
        let currentMessageText = "";

        function openReplyModal(id, currentReply, messageText) {
            msgIdInput.value = id;
            replyTextInput.value = currentReply;
            currentMessageText = messageText; // تخزين رسالة العميل
            modal.style.display = 'block';
        }

        function closeReplyModal() {
            modal.style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                closeReplyModal();
            }
        }

        // كود تشغيل الذكاء الاصطناعي
        if (aiReplyBtn) {
            aiReplyBtn.addEventListener('click', async function() {
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> جاري التفكير...';
                this.disabled = true;

                try {
                    const response = await fetch('ai_handler.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        // نرسل أمر للبايثون بالرد على رسالة العميل
                        body: JSON.stringify({ prompt: "قم بالرد باحترافية ولباقة كخدمة عملاء لمتجر إلكتروني على رسالة العميل التالية: " + currentMessageText })
                    });
                    
                    const data = await response.json();
                    
                    if (data.reply) {
                        replyTextInput.value = data.reply;
                    } else if (data.description) { 
                        replyTextInput.value = data.description;
                    } else if (data.error) {
                        alert("حدث خطأ من الخادم: " + data.error);
                    }
                } catch(e) {
                    alert('خطأ في الاتصال بالمساعد الذكي.');
                } finally {
                    this.innerHTML = originalText;
                    this.disabled = false;
                }
            });
        }
    </script>
</body>
</html>