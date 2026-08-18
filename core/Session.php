<?php
class Session {
    public static function set(string $key, $value): void { $_SESSION[$key] = $value; }
    public static function get(string $key) { return $_SESSION[$key] ?? null; }
    public static function remove(string $key): void { unset($_SESSION[$key]); }
    public static function destroy(): void { session_unset(); session_destroy(); }
    public static function isLoggedIn(): bool { return isset($_SESSION['user_id']); }
    public static function trackOnline(): void {
        $db = Database::getInstance()->getConnection();
        $sessionId = session_id();
        $time = time();
        $stmt = $db->prepare("INSERT INTO online_users (session_id, last_activity) VALUES (?, ?) ON DUPLICATE KEY UPDATE last_activity = VALUES(last_activity)");
        $stmt->execute([$sessionId, $time]);
        
        $timeout = $time - 300;
        $db->prepare("DELETE FROM online_users WHERE last_activity < ?")->execute([$timeout]);
    }

    public static function getOnlineCount(): int {
        $db = Database::getInstance()->getConnection();
        return (int)$db->query("SELECT COUNT(*) FROM online_users")->fetchColumn();
    }
}
