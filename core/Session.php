<?php
class Session {
    public static function set(string $key, $value): void { $_SESSION[$key] = $value; }
    public static function get(string $key) { return $_SESSION[$key] ?? null; }
    public static function remove(string $key): void { unset($_SESSION[$key]); }
    public static function destroy(): void { session_unset(); session_destroy(); }
    public static function isLoggedIn(): bool { return isset($_SESSION['user_id']); }
    
    private static function getVisitorLocation(): array {
        if (isset($_SESSION['visitor_location'])) {
            return $_SESSION['visitor_location'];
        }

        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '';
if (strpos($ip, ',') !== false) {
    $ip = trim(explode(',', $ip)[0]);
}

if ($ip === '127.0.0.1' || $ip === '::1' || empty($ip)) {
    $location = ['ip' => $ip, 'country' => 'Localhost', 'city' => 'Localhost'];
    $_SESSION['visitor_location'] = $location;
    return $location;
}

        $ctx = stream_context_create(['http' => ['timeout' => 2]]);
        $geoData = @file_get_contents("http://ip-api.com/json/{$ip}?fields=status,country,city", false, $ctx);
        
        $country = 'غير محدد';
        $city = 'غير محدد';

        if ($geoData) {
            $geo = json_decode($geoData, true);
            if (isset($geo['status']) && $geo['status'] === 'success') {
                $country = $geo['country'];
                $city = $geo['city'];
            }
        }

        $location = ['ip' => $ip, 'country' => $country, 'city' => $city];
        $_SESSION['visitor_location'] = $location;
        
        return $location;
    }

    public static function trackOnline(): void {
        $db = Database::getInstance()->getConnection();
        $sessionId = session_id();
        $time = time();
        
        $location = self::getVisitorLocation();
        
        $stmt = $db->prepare("INSERT INTO online_users (session_id, last_activity, ip_address, country, city) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE last_activity = VALUES(last_activity)");
        $stmt->execute([$sessionId, $time, $location['ip'], $location['country'], $location['city']]);
        
        $timeout = $time - 300;
        $db->prepare("DELETE FROM online_users WHERE last_activity < ?")->execute([$timeout]);
    }

    public static function getOnlineCount(): int {
        $db = Database::getInstance()->getConnection();
        return (int)$db->query("SELECT COUNT(*) FROM online_users")->fetchColumn();
    }
}