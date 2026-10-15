<?php
class Product {
    private PDO $db;
    private static bool $checked = false;

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

    public function getDiscounted(int $limit = 8): array {
        $stmt = $this->db->prepare("SELECT *, (old_price - price) AS discount_amount FROM products WHERE old_price > price ORDER BY discount_amount DESC LIMIT ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getSliderProducts(string $mode = 'featured', array $activeCoupons = []): array {
        $allowedModes = ['featured', 'latest', 'discounted', 'all'];
        if (!in_array($mode, $allowedModes, true)) {
            $mode = 'featured';
        }
        $rows = [];
        if ($mode === 'all') {
            $rows = $this->db->query("SELECT * FROM products WHERE quantity > 0 ORDER BY RAND()")->fetchAll(PDO::FETCH_ASSOC);
        } elseif ($mode === 'featured') {
            $stmt = $this->db->prepare("SELECT * FROM products WHERE quantity > 0 ORDER BY rating DESC, RAND() LIMIT 30");
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            shuffle($rows);
        } elseif ($mode === 'latest') {
            $stmt = $this->db->prepare("SELECT * FROM products WHERE quantity > 0 ORDER BY id DESC LIMIT 25");
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            shuffle($rows);
        } else {
            $allRows = $this->db->query("SELECT * FROM products WHERE quantity > 0")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($allRows as $row) {
                $isDiscounted = (float)($row['old_price'] ?? 0) > (float)$row['price'];
                if (!$isDiscounted) {
                    foreach ($activeCoupons as $c) {
                        if (($c['target_type'] ?? '') === 'all'
                            || (($c['target_type'] ?? '') === 'specific_product' && (int)($c['target_product_id'] ?? 0) === (int)$row['id'])) {
                            $isDiscounted = true;
                            break;
                        }
                    }
                }
                if ($isDiscounted) {
                    $rows[] = $row;
                }
            }
            shuffle($rows);
        }
        if (empty($rows)) {
            $rows = $this->db->query("SELECT * FROM products ORDER BY RAND() LIMIT 30")->fetchAll(PDO::FETCH_ASSOC);
        }
        return $rows;
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
        $stmt = $this->db->prepare("INSERT INTO products (title, title_en, price, old_price, category_class, description, description_en, image_url, image_2, image_3, image_4, quantity) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['title'],
            $data['title_en'] ?? null,
            $data['price'],
            $data['old_price'] ?? 0,
            $data['category_class'],
            $data['description'] ?? '',
            $data['description_en'] ?? null,
            $data['image_url'] ?? '',
            $data['image_2'] ?? '',
            $data['image_3'] ?? '',
            $data['image_4'] ?? '',
            (int)($data['quantity'] ?? 10)
        ]);
    }

    public function update(int $id, array $data): bool {
        $fields = [];
        $values = [];
        $allowedFields = ['title', 'title_en', 'price', 'old_price', 'category_class', 'description', 'description_en', 'image_url', 'image_2', 'image_3', 'image_4', 'quantity'];
        
        foreach ($allowedFields as $col) {
            if (isset($data[$col])) {
                $fields[] = "$col = ?";
                $values[] = $col === 'quantity' ? (int)$data[$col] : $data[$col];
            }
        }
        
        if (empty($fields)) return false;
        
        $values[] = $id;
        $sql = "UPDATE products SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }

    public function deductStock(int $id, int $amount): bool {
        $stmt = $this->db->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ? AND quantity >= ?");
        $stmt->execute([$amount, $id, $amount]);
        return $stmt->rowCount() > 0;
    }

    // دالة إرجاع الكمية للمخزن عند إلغاء الطلب
    public function restock(int $id, int $amount): bool {
        $stmt = $this->db->prepare("UPDATE products SET quantity = quantity + ? WHERE id = ?");
        return $stmt->execute([$amount, $id]);
    }

    public function countOutOfStock(): int {
        $stmt = $this->db->query("SELECT COUNT(*) FROM products WHERE quantity <= 0");
        return (int)$stmt->fetchColumn();
    }

    public function getOutOfStockProducts(int $limit = 5): array {
        $stmt = $this->db->prepare("SELECT id, title, image_url, category_class, quantity FROM products WHERE quantity <= 0 ORDER BY id DESC LIMIT ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function calculateDiscount(array $product, array $activeCoupons = []): array {
        $basePrice = floatval($product['price'] ?? 0);
        $finalPrice = $basePrice;
        $discountValue = 0;

        foreach ($activeCoupons as $c) {
            if ($c['target_type'] === 'all' || ($c['target_type'] === 'specific_product' && (int)$c['target_product_id'] === (int)$product['id'])) {
                if ($c['discount_type'] === 'percentage') {
                    $discountValue = $basePrice * ($c['discount_value'] / 100);
                    $finalPrice = $basePrice - $discountValue;
                } else {
                    $discountValue = floatval($c['discount_value']);
                    $finalPrice = $basePrice - $discountValue;
                }
                break;
            }
        }

        $finalPrice = max(0, $finalPrice);
        return [
            'final_price' => $finalPrice,
            'discount' => $discountValue
        ];
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
   public static function ensureUploadTableExists(): void {
        if (self::$checked) {
            return;
        }
        self::$checked = true;
        try {
            $db = Database::getInstance()->getConnection();
            $db->exec("CREATE TABLE IF NOT EXISTS uploaded_files (file_path VARCHAR(255) NOT NULL PRIMARY KEY, mime_type VARCHAR(50) NOT NULL, file_data LONGBLOB NOT NULL, created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        } catch (Throwable $e) {
        }
    }

    public static function saveUploadedFileBackup(string $relativePath, string $mimeType, string $binaryData): void {
        self::ensureUploadTableExists();
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("INSERT INTO uploaded_files (file_path, mime_type, file_data) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE mime_type = VALUES(mime_type), file_data = VALUES(file_data)");
            $stmt->execute([ltrim($relativePath, '/'), $mimeType, $binaryData]);
        } catch (Throwable $e) {
        }
    }

    public static function deleteUploadedFileBackup(string $relativePath): void {
        self::ensureUploadTableExists();
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("DELETE FROM uploaded_files WHERE file_path = ?");
            $stmt->execute([ltrim($relativePath, '/')]);
        } catch (Throwable $e) {
        }
    }

   public static function getImageUrl(?string $path): string {
        if (empty($path)) {
            return BASE_URL . 'images/logos/logo.png';
        }
        
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, 'data:image/')) {
            return $path;
        }
        
        $cleanPath = ltrim($path, '/');
        $fullPath = ROOT_DIR . '/' . $cleanPath;
        
        if (file_exists($fullPath)) {
            return BASE_URL . $cleanPath;
        }
        
        if (str_starts_with($cleanPath, 'uploads/')) {
            try {
                self::ensureUploadTableExists();
                $db = Database::getInstance()->getConnection();
                $stmt = $db->prepare("SELECT file_data FROM uploaded_files WHERE file_path = ? LIMIT 1");
                $stmt->execute([$cleanPath]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($row && !empty($row['file_data'])) {
                    $dir = dirname($fullPath);
                    if (!is_dir($dir)) {
                        @mkdir($dir, 0775, true);
                    }
                    @file_put_contents($fullPath, $row['file_data']);
                    if (file_exists($fullPath)) {
                        return BASE_URL . $cleanPath;
                    }
                }
            } catch (Throwable $e) {
            }
        }
        
        return BASE_URL . 'images/logos/logo.png';
    } 
}
