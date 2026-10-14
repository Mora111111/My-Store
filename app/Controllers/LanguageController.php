<?php
class LanguageController {
    public function switch(): void {
        $lang = $_GET['lang'] ?? 'ar';
        
        if (in_array($lang, ['ar', 'en'])) {
            $_SESSION['lang'] = $lang;
            
            if (Session::isLoggedIn()) {
                $db = Database::getInstance()->getConnection();
                $stmt = $db->prepare("UPDATE elogin SET preferred_lang = ? WHERE id = ?");
                $stmt->execute([$lang, Session::get('user_id')]);
            }
        }

        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        $redirect = '/';
        if (!empty($referer)) {
            $parsed = parse_url($referer);
            $refererHost = $parsed['host'] ?? '';
            $currentHost = $_SERVER['HTTP_HOST'] ?? '';
            if ($refererHost === '' || $refererHost === $currentHost) {
                $path = $parsed['path'] ?? '/';
                if ($path === '' || $path[0] !== '/' || strpos($path, '//') === 0) {
                    $path = '/';
                }
                $query = $parsed['query'] ?? '';
                $redirect = $path . ($query ? '?' . $query : '');
            }
        }
        header("Location: " . $redirect);
        exit;
    }
}