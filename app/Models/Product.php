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
     $words = array_filter(explode(' ', trim($keyword)));

     if (empty($words)) {
         return $this->getAll();
     }

     $conditions = [];
     $params = [];

     foreach ($words as $word) {
         $conditions[] = "(title LIKE ? OR category_class LIKE ?)";
         $params[] = '%' . $word . '%';
         $params[] = '%' . $word . '%';
     }

     $sql = "SELECT * FROM products WHERE " . implode(' AND ', $conditions) . " ORDER BY id DESC";
     $stmt = $this->db->prepare($sql);
     $stmt->execute($params);
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
        $stmt = $this->db->prepare("INSERT INTO products (title, price, old_price, category_class, description, image_url, image_2, image_3, image_4) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['title'],
            $data['price'],
            $data['old_price'] ?? 0,
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
        foreach (['title', 'price', 'old_price', 'category_class', 'description', 'image_url', 'image_2', 'image_3', 'image_4'] as $col) {
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
        // 1. تنظيف التعليقات المرتبطة بالمنتج أولاً
        $stmtComments = $this->db->prepare("DELETE FROM product_comments WHERE product_id = ?");
        $stmtComments->execute([$id]);

        // 2. تنظيف المفضلة الخاصة بالعملاء المرتبطة بهذا المنتج
        $stmtFavorites = $this->db->prepare("DELETE FROM favorites WHERE product_id = ?");
        $stmtFavorites->execute([$id]);

        // 3. أخيراً، حذف المنتج نفسه بأمان
        $stmt = $this->db->prepare("DELETE FROM products WHERE id = ?");
        return $stmt->execute([$id]);
    }
   public static function getImageUrl(?string $path): string {
        if (empty($path)) {
            return BASE_URL . 'images/logos/logo.png';
        }
        
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }
        
        $cleanPath = ltrim($path, '/');
        
        if (file_exists(ROOT_DIR . '/' . $cleanPath)) {
            return BASE_URL . $cleanPath;
        }
        
        return BASE_URL . 'images/logos/logo.png';
    } 
    public static function calculateDiscount($product, $activeCoupons) {
        $final_price = $product['price'];
        $has_coupon_discount = false;
        $discount_pct_badge = 0;
        $original_price = $product['price'];

        foreach($activeCoupons as $c) {
            if($c['target_type'] === 'all' || ($c['target_type'] === 'specific_product' && $c['target_product_id'] == $product['id'])) {
                $has_coupon_discount = true;
                if($c['discount_type'] === 'percentage') {
                    $discount_amount = ($original_price * ($c['discount_value'] / 100));
                    $final_price = $original_price - $discount_amount;
                    $discount_pct_badge = round($c['discount_value']);
                } else {
                    $final_price = $original_price - $c['discount_value'];
                    $discount_pct_badge = round(($c['discount_value'] / $original_price) * 100);
                }
                $final_price = max(0, $final_price);
                break;
            }
        }

        if(!$has_coupon_discount && !empty($product['old_price']) && $product['old_price'] > $original_price) {
            $has_coupon_discount = true;
            $final_price = $original_price;
            $original_price = $product['old_price']; 
            $discount_pct_badge = round((($original_price - $final_price) / $original_price) * 100);
        }

        return [
            'final_price' => $final_price,
            'original_price' => $original_price,
            'has_discount' => $has_coupon_discount,
            'discount_pct' => $discount_pct_badge
        ];
    }
}
