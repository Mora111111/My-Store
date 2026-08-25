<?php
class Comment {
    private PDO $db;
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    public function countAll(): int {
        return (int)$this->db->query("SELECT COUNT(*) FROM product_comments")->fetchColumn();
    }

    public function getAll(): array {
        $stmt = $this->db->query("
            SELECT c.*, p.title as product_title, p.image_url
            FROM product_comments c
            JOIN products p ON c.product_id = p.id
            ORDER BY c.created_at DESC
        ");
        return $stmt->fetchAll();
    }
    public function reply(int $id, string $reply): bool {
        $stmt = $this->db->prepare("UPDATE product_comments SET admin_reply = ? WHERE id = ?");
        return $stmt->execute([$reply, $id]);
    }
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM product_comments WHERE id = ?");
        return $stmt->execute([$id]);
    }
    public function getByProductId(int $productId): array {
        $stmt = $this->db->prepare("SELECT * FROM product_comments WHERE product_id = ? ORDER BY id DESC");
        $stmt->execute([$productId]);
        return $stmt->fetchAll();
    }
    public function createComment(int $productId, string $customerName, string $commentText, string $adminReply = '', int $userRating = 5): bool {
        $stmt = $this->db->prepare("INSERT INTO product_comments (product_id, customer_name, comment_text, admin_reply, user_rating) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$productId, $customerName, $commentText, $adminReply, $userRating]);
    }

    public function updateProductAverageRating(int $productId): void {
        $stmt = $this->db->prepare("SELECT AVG(user_rating) FROM product_comments WHERE product_id = ?");
        $stmt->execute([$productId]);
        $avg = round((float)$stmt->fetchColumn(), 1);
        if ($avg > 0) {
            $updateStmt = $this->db->prepare("UPDATE products SET rating = ? WHERE id = ?");
            $updateStmt->execute([$avg, $productId]);
        }
    }
}
