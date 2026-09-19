<?php
class Language {
    private static array $translations = [];

    public static function load(): void {
        $lang = $_SESSION['lang'] ?? 'ar';
        $file = APP_DIR . "/Language/{$lang}.php";
        
        if (file_exists($file)) {
            self::$translations = require $file;
        } else {
            self::$translations = require APP_DIR . "/Language/ar.php"; // لغة افتراضية
        }
    }

    public static function get(string $key): string {
        return self::$translations[$key] ?? $key;
    }
}

// دالة مساعدة (Helper) نستخدمها مباشرة في واجهات الـ HTML
function lang(string $key): string {
    return Language::get($key);
}