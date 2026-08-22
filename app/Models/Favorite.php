<?php
class Favorite {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function toggleFavorite(int $userId, int $productId): string {
        $stmt = $this->db->prepare("SELECT id FROM favorites WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$userId, $productId]);
        
        if ($stmt->fetch()) {
            $deleteStmt = $this->db->prepare("DELETE FROM favorites WHERE user_id = ? AND product_id = ?");
            $deleteStmt->execute([$userId, $productId]);
            return 'removed';
        }
        
        $insertStmt = $this->db->prepare("INSERT INTO favorites (user_id, product_id) VALUES (?, ?)");
        $insertStmt->execute([$userId, $productId]);
        return 'added';
    }

    public function getUserFavorites(int $userId): array {
        $stmt = $this->db->prepare("SELECT p.* FROM products p JOIN favorites f ON p.id = f.product_id WHERE f.user_id = ? ORDER BY f.created_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getUserFavoriteIds(int $userId): array {
        $stmt = $this->db->prepare("SELECT product_id FROM favorites WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}