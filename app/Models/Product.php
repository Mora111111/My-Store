<?php
class Product {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll(): array {
        $stmt = $this->db->query("SELECT * FROM products ORDER BY id DESC");
        return $stmt->fetchAll();
    }

    public function search(string $keyword): array {
        $stmt =$this->db->prepare("SELECT * FROM products WHERE title LIKE ? ORDER BY id DESC");
        $stmt->execute(['\%' .$keyword . '%']);
        return $stmt->fetchAll();
    }

    public function countAll(): int {
        return (int)$this->db->query("SELECT COUNT(*) FROM products")->fetchColumn();
    }

    public function getFeatured(int $limit = 4): array {
        $stmt = $this->db->prepare("SELECT * FROM products ORDER BY rating DESC LIMIT ?");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public function getLatest(int $limit = 8): array {
        $stmt = $this->db->prepare("SELECT * FROM products ORDER BY id DESC LIMIT ?");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public function getCategories(): array {
        $stmt = $this->db->query("SELECT DISTINCT category_class FROM products WHERE category_class != ''");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function findById(int $id) {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create(array $data): bool {
        $stmt = $this->db->prepare("INSERT INTO products (title, price, category_class, description, image_url, image_2, image_3, image_4) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['title'],
            $data['price'],
            $data['category_class'],
            $data['description'] ?? '',
            $data['image_url'] ?? '',
            $data['image_2'] ?? '',
            $data['image_3'] ?? '',
            $data['image_4'] ?? ''
        ]);
    }

    public function update(int $id, array $data): bool {
        $fields = [];
        $values = [];
        foreach (['title', 'price', 'category_class', 'description', 'image_url', 'image_2', 'image_3', 'image_4'] as $col) {
            if (array_key_exists($col, $data)) {
                $fields[] = "$col = ?";
                $values[] = $data[$col];
            }
        }
        if (empty($fields)) return false;
        $values[] = $id;
        $stmt = $this->db->prepare("UPDATE products SET " . implode(', ', $fields) . " WHERE id = ?");
        return $stmt->execute($values);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM products WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
