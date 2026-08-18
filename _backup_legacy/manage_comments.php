<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

require_once 'db.php';
$msg = "";

if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM product_comments WHERE id = '$delete_id'");
    header("Location: manage_comments.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_reply'])) {
    $comment_id = intval($_POST['comment_id']);
    $admin_reply = mysqli_real_escape_string($conn, trim($_POST['admin_reply']));
    
    $update_query = "UPDATE product_comments SET admin_reply = '$admin_reply' WHERE id = '$comment_id'";
    if (mysqli_query($conn, $update_query)) {
        $msg = "<div style='background:#d1fae5; color:#065f46; padding:14px 18px; border-radius:16px; margin-bottom:25px; border:1px solid #a7f3d0; font-weight:500;'><i class='fa-solid fa-check-circle' style='margin-left:8px;'></i> تم حفظ الرد بنجاح!</div>";
    } else {
        $msg = "<div style='background:#fee2e2; color:#991b1b; padding:14px 18px; border-radius:16px; margin-bottom:25px; border:1px solid #fecaca; font-weight:500;'><i class='fa-solid fa-triangle-exclamation' style='margin-left:8px;'></i> حدث خطأ أثناء الحفظ.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إدارة التعليقات - لوحة التحكم</title>
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

         .products-table {
            width: 100%;
            border-collapse: collapse;
            text-align: right;
            margin-top: 15px;
        }

        .products-table th {
            background: #f8fafc;
            color: #475569;
            font-weight: 600;
            padding: 16px 12px;
            border-bottom: 2px solid #e2e8f0;
            font-size: 15px;
        }

        .products-table td {
            padding: 16px 12px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
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
            border: none;
            cursor: pointer;
            font-family: inherit;
        }

        .btn-reply {
            background: #3b82f6;
            box-shadow: 0 4px 8px rgba(59, 130, 246, 0.2);
        }

        .btn-reply:hover {
            background: #2563eb;
            transform: translateY(-1px);
        }

        .btn-delete {
            background: #ef4444;
            box-shadow: 0 4px 8px rgba(239, 68, 68, 0.2);
        }

        .btn-delete:hover {
            background: #dc2626;
            transform: translateY(-1px);
        }

        .badge-success {
            background: #d1fae5;
            color: #065f46;
            padding: 6px 14px;
            border-radius: 40px;
            font-size: 13px;
            font-weight: 600;
        }

        .badge-warning {
            background: #fef3c7;
            color: #92400e;
            padding: 6px 14px;
            border-radius: 40px;
            font-size: 13px;
            font-weight: 600;
        }

         .modal-overlay {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            backdrop-filter: blur(4px);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: #ffffff;
            padding: 35px;
            border-radius: 32px;
            width: 520px;
            max-width: 90%;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
            position: relative;
        }

        .close-modal-btn {
            position: absolute;
            top: 20px;
            left: 20px;
            font-size: 24px;
            cursor: pointer;
            color: #94a3b8;
            transition: 0.2s;
        }

        .close-modal-btn:hover {
            color: #ef4444;
        }

        .form-group {
            margin-bottom: 22px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #334155;
            font-size: 15px;
        }

        .form-group textarea {
            width: 100%;
            padding: 14px 16px;
            border: 1.5px solid #e2e8f0;
            border-radius: 16px;
            box-sizing: border-box;
            font-family: 'Tajawal', sans-serif;
            font-size: 15px;
            background: #fafbfc;
            resize: vertical;
            transition: all 0.2s;
        }

        .form-group textarea:focus {
            outline: none;
            border-color: #38bdf8;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.1);
        }

        .btn-submit {
            background: linear-gradient(135deg, #38bdf8, #2dd4bf);
            color: #0f172a;
            border: none;
            padding: 14px 28px;
            cursor: pointer;
            border-radius: 40px;
            font-size: 16px;
            font-weight: 700;
            transition: all 0.3s;
            width: 100%;
            margin-top: 10px;
            box-shadow: 0 8px 16px rgba(56, 189, 248, 0.2);
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 20px rgba(56, 189, 248, 0.3);
        }

        .btn-ai-reply {
            background: linear-gradient(135deg, #a78bfa, #c084fc);
            color: white;
            border: none;
            padding: 10px 18px;
            cursor: pointer;
            border-radius: 40px;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 10px;
            transition: 0.3s;
            box-shadow: 0 4px 10px rgba(167, 139, 250, 0.3);
        }

        .btn-ai-reply:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .btn-ai-reply:disabled {
            background: #cbd5e1;
            cursor: not-allowed;
            opacity: 1;
            transform: none;
            box-shadow: none;
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
        <a href="manage_comments.php" class="active"><i class="fa-solid fa-comments"></i><span>تعليقات العملاء</span></a>
        <a href="manage_orders.php"><i class="fa-solid fa-cart-shopping"></i><span>طلبات الشراء</span></a>
        <a href="manage_messages.php"><i class="fa-solid fa-envelope"></i><span>رسائل الزوار</span></a>
        <a href="manage_users.php"><i class="fa-solid fa-users"></i><span>إدارة المستخدمين</span></a>

    </div>

    <div class="main-content">
        <div class="header">
            <h3><i class="fa-solid fa-comments" style="margin-left: 12px; color: #38bdf8;"></i>تعليقات العملاء</h3>
            <a href="logout.php" class="logout-btn">
                <i class="fa-solid fa-right-from-bracket"></i>
                تسجيل الخروج
            </a>
        </div>

        <div class="content-area">
            <?php echo $msg; ?>
            
            <div class="card">
                <h2 style="margin-top:0;"><i class="fa-solid fa-list"></i> أحدث التعليقات</h2>
                <table class="products-table">
                    <tr>
                        <th>المنتج</th>
                        <th>اسم العميل</th>
                        <th>التعليق</th>
                        <th>التاريخ</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                    <?php
                    $comments_query = mysqli_query($conn, "
                        SELECT c.*, p.title as product_title, p.image_url 
                        FROM product_comments c 
                        JOIN products p ON c.product_id = p.id 
                        ORDER BY c.created_at DESC
                    ");
                    
                    if (mysqli_num_rows($comments_query) > 0) {
                        while ($row = mysqli_fetch_assoc($comments_query)) {
                            $safe_name = htmlspecialchars($row['customer_name']);
                            $safe_comment = htmlspecialchars($row['comment_text']);
                            $safe_reply = htmlspecialchars($row['admin_reply'] ?? '');
                            
                            $status_badge = !empty($row['admin_reply']) ? "<span class='badge-success'>تم الرد</span>" : "<span class='badge-warning'>بانتظار الرد</span>";
                            
                            echo "<tr>";
                            echo "<td><img src='{$row['image_url']}' width='40' height='40' style='border-radius:10px; object-fit:cover; vertical-align:middle; margin-left:8px; box-shadow:0 4px 6px rgba(0,0,0,0.05);'> <span style='font-weight:500;'>{$row['product_title']}</span></td>";
                            echo "<td style='font-weight:500;'>{$safe_name}</td>";
                            echo "<td><div style='max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;' title='{$safe_comment}'>{$safe_comment}</div></td>";
                            echo "<td>" . date('Y-m-d', strtotime($row['created_at'])) . "</td>";
                            echo "<td>{$status_badge}</td>";
                            
                            $escaped_comment_js = addslashes($safe_comment);
                            $escaped_reply_js = addslashes($safe_reply);
                            
                            echo "<td>
                                    <button onclick=\"openReplyModal({$row['id']}, '{$safe_name}', '{$escaped_comment_js}', '{$escaped_reply_js}')\" class='action-btn btn-reply'><i class='fa-solid fa-reply'></i> رد</button>
                                    <a href='manage_comments.php?delete={$row['id']}' class='action-btn btn-delete' onclick='return confirm(\"هل أنت متأكد من حذف هذا التعليق؟\");'><i class='fa-solid fa-trash'></i>حذف</a>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6' style='text-align:center; padding:40px; color:#94a3b8; font-size:16px;'><i class='fa-solid fa-comment-slash' style='font-size:40px; margin-bottom:15px; opacity:0.5;'></i><br>لا توجد تعليقات حتى الآن.</td></tr>";
                    }
                    ?>
                </table>
            </div>
        </div>
    </div>

    <div id="replyModal" class="modal-overlay">
        <div class="modal-content">
            <i class="fa-solid fa-xmark close-modal-btn" onclick="closeReplyModal()"></i>
            <h3 style="margin-top: 0; color: #0f172a; font-size:22px; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-headset" style="color: #38bdf8;"></i> الرد على العميل
            </h3>
            
            <div style="background: #f8fafc; padding: 18px; border-radius: 20px; margin-bottom: 20px; border-right: 4px solid #3b82f6;">
                <strong style="color: #1e293b;">تعليق العميل (<span id="display_customer"></span>):</strong>
                <p id="display_comment" style="margin: 12px 0 0 0; color: #334155; line-height: 1.6; font-size: 15px;"></p>
                <input type="hidden" id="hidden_comment_text">
            </div>

            <form action="manage_comments.php" method="POST">
                <input type="hidden" name="comment_id" id="modal_comment_id">
                
                <div class="form-group">
                    <div style="display: flex; justify-content: space-between; align-items: flex-end;">
                        <label style="margin-bottom: 0;">رد الإدارة:</label>
                        <button type="button" id="aiSuggestBtn" class="btn-ai-reply">
                            <i class="fa-solid fa-wand-magic-sparkles"></i> اقتراح رد ذكي
                        </button>
                    </div>
                    <textarea name="admin_reply" id="modal_admin_reply" rows="4" placeholder="اكتب ردك للعميل هنا..." required></textarea>
                </div>
                
                <button type="submit" name="submit_reply" class="btn-submit"><i class="fa-solid fa-floppy-disk" style="margin-left:8px;"></i> حفظ ونشر الرد</button>
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

        function closeReplyModal() {
            modal.style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                closeReplyModal();
            }
        }

        aiBtn.addEventListener('click', async function() {
            const commentText = document.getElementById('hidden_comment_text').value;
            
            const originalBtnText = aiBtn.innerHTML;
            aiBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> جاري التفكير...';
            aiBtn.disabled = true;

            try {
                const response = await fetch('ai_comment_handler.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ comment: commentText })
                });

                if (!response.ok) throw new Error('خطأ في الاتصال بالسيرفر');
                const data = await response.json();
                
                if (data.error) throw new Error(data.error);

                if (data.reply) {
                    replyInput.value = data.reply;
                }

            } catch (error) {
                alert('حدث خطأ: ' + error.message);
            } finally {
                aiBtn.innerHTML = originalBtnText;
                aiBtn.disabled = false;
            }
        });
    </script>
</body>
</html>