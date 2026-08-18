<?php
session_start();
require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $f_name = mysqli_real_escape_string($conn, trim($_POST['first_name']));
    $l_name = mysqli_real_escape_string($conn, trim($_POST['last_name']));
    $phone  = mysqli_real_escape_string($conn, trim($_POST['phone']));
    $email  = mysqli_real_escape_string($conn, trim($_POST['email']));
    $msg    = trim($_POST['message']); 
    $msg_safe = mysqli_real_escape_string($conn, $msg);

    $sql = "INSERT INTO contact_messages (first_name, last_name, phone, email, message) 
            VALUES ('$f_name', '$l_name', '$phone', '$email', '$msg_safe')";

    if (mysqli_query($conn, $sql)) {
        $last_id = mysqli_insert_id($conn); 
        
        $uid = uniqid();
        $input_file = __DIR__ . "/temp_msg_" . $uid . ".txt";
        $output_file = __DIR__ . "/temp_reply_" . $uid . ".txt";
        $script_path = __DIR__ . "/chatbot_engine.py";
        
        file_put_contents($input_file, $msg);
        
        $command = "python \"$script_path\" \"$input_file\" \"$output_file\" 2>&1";
        $exec_output = shell_exec($command);
        
        $bot_reply = "";
        
        usleep(500000); 

        if (file_exists($output_file)) {
            $bot_reply = file_get_contents($output_file);
            unlink($output_file);
        } else {
            $bot_reply = "لم يتمكن النظام الذكي من الرد. الخطأ: " . substr($exec_output, 0, 100);
        }
        
        if (file_exists($input_file)) {
            unlink($input_file);
        }

        if (!empty($bot_reply)) {
            $safe_reply = mysqli_real_escape_string($conn, trim($bot_reply));
            $update_sql = "UPDATE contact_messages SET reply = '$safe_reply' WHERE id = '$last_id'";
            mysqli_query($conn, $update_sql);
        }

        if(isset($_SESSION['user_id'])) {
            echo "<script>alert('شكراً لتواصلك معنا! سيتم تحويلك لصفحة رسائلك.'); window.location.href='my_orders.php';</script>";
        } else {
            echo "<script>alert('شكراً لتواصلك معنا في MY Store. تم إرسال رسالتك بنجاح!'); window.location.href='contact_us.php';</script>";
        }
        
    } else {
        echo "<script>alert('حدث خطأ أثناء الإرسال، الرجاء المحاولة لاحقاً!'); window.location.href='contact_us.php';</script>";
    }
}
?>