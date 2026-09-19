<?php
class LanguageController {
    public function switch(): void {
        $lang = $_GET['lang'] ?? 'ar';
        
        if (in_array($lang, ['ar', 'en'])) {
            $_SESSION['lang'] = $lang;
            
            // إذا كان المستخدم مسجل الدخول، احفظ لغته في قاعدة البيانات
            if (Session::isLoggedIn()) {
                $db = Database::getInstance()->getConnection();
                $stmt = $db->prepare("UPDATE elogin SET preferred_lang = ? WHERE id = ?");
                $stmt->execute([$lang, Session::get('user_id')]);
            }
        }
        
        // إعادته للصفحة التي كان فيها
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        header("Location: " . $referer);
        exit;
    }
}