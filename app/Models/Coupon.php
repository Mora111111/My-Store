<?php
class Coupon {
    private PDO $db;
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    public function getAll() {
        $stmt =$this->db->query("SELECT * FROM coupons ORDER BY id DESC");
        return $stmt->fetchAll();
    }
    public function create(array $data): bool {
        $stmt =$this->db->prepare("INSERT INTO coupons (code, discount_type, discount_value, target_type, target_product_id, show_strikethrough, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['code'],$data['discount_type'], $data['discount_value'],$data['target_type'], $data['target_product_id'] ?: null,$data['show_strikethrough'], 1
        ]);
    }
    public function delete(int $id): bool {
        $stmt =$this->db->prepare("DELETE FROM coupons WHERE id = ?");
        return $stmt->execute([$id]);
    }
    public function toggleStatus(int $id, int $current_status): bool {
        $new_status =$current_status ? 0 : 1;
        $stmt =$this->db->prepare("UPDATE coupons SET status = ? WHERE id = ?");
        return $stmt->execute([$new_status,$id]);
    }
}
?>
