<?php
class Notification {
    private static bool $tableChecked = false;
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function ensureTableExists(): void {
        if (self::$tableChecked) {
            return;
        }

        $this->db->exec("CREATE TABLE IF NOT EXISTS notifications (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            type VARCHAR(50) NOT NULL DEFAULT 'order',
            reference_id INT DEFAULT NULL,
            title_ar VARCHAR(255) NOT NULL,
            title_en VARCHAR(255) NOT NULL,
            message_ar TEXT DEFAULT NULL,
            message_en TEXT DEFAULT NULL,
            link VARCHAR(255) DEFAULT NULL,
            is_read TINYINT(1) NOT NULL DEFAULT 0,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_notif_user_read (user_id, is_read, created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
        self::$tableChecked = true;
    }

    public function create(int $userId, string $type, ?int $referenceId, string $titleAr, string $titleEn, string $messageAr, string $messageEn, string $link): bool {
        $this->ensureTableExists();
        $stmt = $this->db->prepare("INSERT INTO notifications (user_id, type, reference_id, title_ar, title_en, message_ar, message_en, link) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$userId, $type, $referenceId, $titleAr, $titleEn, $messageAr, $messageEn, $link]);
    }

    public function getRecentForUser(int $userId, int $limit = 15): array {
        $this->ensureTableExists();
        $stmt = $this->db->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC, id DESC LIMIT ?");
        $stmt->bindValue(1, $userId, PDO::PARAM_INT);
        $stmt->bindValue(2, max(0, $limit), PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUnreadCount(int $userId): int {
        $this->ensureTableExists();
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
        $stmt->execute([$userId]);
        return (int)$stmt->fetchColumn();
    }

    public function markAsRead(int $notificationId, int $userId): bool {
        return $this->markUnread($userId, ' AND id = ?', [$notificationId]);
    }

    public function markAllAsRead(int $userId): bool {
        return $this->markUnread($userId);
    }

    public function markByReferenceAsRead(int $userId, string $type, ?int $referenceId = null): bool {
        return $referenceId === null
            ? $this->markUnread($userId, ' AND type = ?', [$type])
            : $this->markUnread($userId, ' AND type = ? AND reference_id = ?', [$type, $referenceId]);
    }

    private function markUnread(int $userId, string $filter = '', array $parameters = []): bool {
        $this->ensureTableExists();
        $stmt = $this->db->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0" . $filter);
        return $stmt->execute([$userId, ...$parameters]);
    }
}
