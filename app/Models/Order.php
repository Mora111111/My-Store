<?php
class Order {
    private PDO $db;
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    public function create(array $data): int|false {
        $sql = "INSERT INTO orders (user_id, full_name, phone, address_line1, address_line2, city, governorate, zip_code, total_price, products) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $success = $stmt->execute([
            $data['user_id'] ?? null,
            $data['full_name'],
            $data['phone'],
            $data['address_line1'],
            $data['address_line2'] ?? null,
            $data['city'],
            $data['governorate'],
            $data['zip_code'] ?? null,
            $data['total_price'],
            $data['products']
        ]);
        return $success ? (int)$this->db->lastInsertId() : false;
    }

    public function countAll(): int {
        return (int)$this->db->query("SELECT COUNT(*) FROM orders")->fetchColumn();
    }

    public function getAll(): array {
        $stmt = $this->db->query("SELECT * FROM orders ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    public function updateStatus(int $id, string $status): bool {
        $stmt = $this->db->prepare("UPDATE orders SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM orders WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function cancelUserOrder(int $id, int $userId): bool {
        $stmt = $this->db->prepare("UPDATE orders SET status = 'ملغي' WHERE id = ? AND user_id = ? AND status = 'قيد المراجعة'");
        return $stmt->execute([$id, $userId]);
    }

    public function hideUserOrder(int $id, int $userId): bool {
        $stmt = $this->db->prepare("UPDATE orders SET user_hidden = 1 WHERE id = ? AND user_id = ?");
        return $stmt->execute([$id, $userId]);
    }

    public function getByUserId(int $userId): array {
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE user_id = ? AND user_hidden = 0 ORDER BY created_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function hasPurchasedProduct(int $userId, string $productTitle): bool {
        $stmt = $this->db->prepare("SELECT products FROM orders WHERE user_id = ? AND status = 'مكتمل'");
        $stmt->execute([$userId]);
        $orders = $stmt->fetchAll(PDO::FETCH_COLUMN);
        foreach ($orders as $productsJson) {
            $products = json_decode($productsJson, true);
            if (is_array($products)) {
                foreach ($products as $item) {
                    if (isset($item['title']) && trim($item['title']) === trim($productTitle)) {
                        return true;
                    }
                }
            }
        }
        return false;
    }
}
