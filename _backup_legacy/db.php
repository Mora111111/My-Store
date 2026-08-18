<?php
error_reporting(0);
ini_set('display_errors', 0);

$host = "localhost";
$user = "root"; 
$pass = ""; 
$dbname = "e-website";

$conn = mysqli_connect($host, $user, $pass, $dbname); 

if (!$conn) { 
    die("عذراً، يوجد مشكلة مؤقتة في الاتصال بقاعدة البيانات."); 
} 

mysqli_set_charset($conn, "utf8mb4"); 

$settings_query = mysqli_query($conn, "SELECT * FROM settings WHERE id = 1");
if($settings_query && mysqli_num_rows($settings_query) > 0) {
    $site_settings = mysqli_fetch_assoc($settings_query);
} else {
    $site_settings = [
        'about_text' => '',
        'phone1' => '',
        'phone2' => '',
        'email' => '',
        'address' => ''
    ];
}
?>