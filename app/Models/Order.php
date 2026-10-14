<?php
class Order {
    private PDO $db;
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    public function create(array $data): int|false {
        $sql = "INSERT INTO orders (user_id, full_name, phone, address_line1, address_line2, city, governorate, zip_code, status, total_price, subtotal, shipping_fee, discount_amount, coupon_code, products, payment_method, payment_status, transaction_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
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
            $data['status'] ?? 'قيد المراجعة',
            $data['total_price'],
            $data['subtotal'] ?? $data['total_price'],
            $data['shipping_fee'] ?? 0,
            $data['discount_amount'] ?? 0,
            $data['coupon_code'] ?? null,
            $data['products'],
            $data['payment_method'] ?? 'cod',
            $data['payment_status'] ?? 'pending',
            $data['transaction_id'] ?? null
        ]);
        return $success ? (int)$this->db->lastInsertId() : false;
    }
    

    public function countAll(): int {
        return (int)$this->db->query("SELECT COUNT(*) FROM orders WHERE admin_hidden = 0 AND status != 'بانتظار الدفع'")->fetchColumn();
    }

    public function countPending(): int {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM orders WHERE status = 'قيد المراجعة' AND admin_hidden = 0");
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    public function getAll(): array {
        $stmt = $this->db->query("SELECT * FROM orders WHERE admin_hidden = 0 AND status != 'بانتظار الدفع' ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    public function findById(int $id) {
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateStatus(int $id, string $status, ?string $adminMessage = null, ?string $paymentStatus = null): bool {
        if ($paymentStatus !== null) {
            $stmt = $this->db->prepare("UPDATE orders SET status = ?, admin_message = ?, payment_status = ? WHERE id = ?");
            return $stmt->execute([$status, $adminMessage, $paymentStatus, $id]);
        } else {
            $stmt = $this->db->prepare("UPDATE orders SET status = ?, admin_message = ? WHERE id = ?");
            return $stmt->execute([$status, $adminMessage, $id]);
        }
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("UPDATE orders SET admin_hidden = 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function cancelUserOrder(int $id, int $userId): string {
        try {
            $this->db->beginTransaction();
            $lockStmt = $this->db->prepare("SELECT id, status, payment_method, payment_status, products FROM orders WHERE id = ? AND user_id = ? LIMIT 1 FOR UPDATE");
            $lockStmt->execute([$id, $userId]);
            $row = $lockStmt->fetch(PDO::FETCH_ASSOC);
            if (!$row || $row['status'] !== 'قيد المراجعة') {
                $this->db->rollBack();
                return 'invalid_status';
            }
            if (($row['payment_method'] ?? 'cod') !== 'cod' || ($row['payment_status'] ?? 'pending') === 'paid') {
                $this->db->rollBack();
                return 'online_paid_restriction';
            }
            $updateStmt = $this->db->prepare("UPDATE orders SET status = 'ملغي' WHERE id = ? AND user_id = ? AND status = 'قيد المراجعة' AND payment_method = 'cod' AND payment_status != 'paid'");
            $updateStmt->execute([$id, $userId]);
            if ($updateStmt->rowCount() === 0) {
                $this->db->rollBack();
                return 'failed';
            }
            $products = json_decode($row['products'] ?? '[]', true);
            if (is_array($products)) {
                $restock = $this->db->prepare("UPDATE products SET quantity = quantity + ? WHERE id = ?");
                foreach ($products as $item) {
                    if (!is_array($item)) {
                        continue;
                    }
                    $qty = (int)($item['quantity'] ?? $item['number'] ?? 1);
                    $prodId = (int)($item['id'] ?? 0);
                    if ($prodId > 0 && $qty > 0) {
                        $restock->execute([$qty, $prodId]);
                    }
                }
            }
            $this->db->commit();
            return 'cancelled';
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return 'failed';
        }
    }

    public function hideUserOrder(int $id, int $userId): bool {
        $stmt = $this->db->prepare("UPDATE orders SET user_hidden = 1 WHERE id = ? AND user_id = ?");
        return $stmt->execute([$id, $userId]);
    }

    public function cancelPendingPaymentOrdersForUser(int $userId): void {
        $stmt = $this->db->prepare("SELECT id, products FROM orders WHERE user_id = ? AND status = 'بانتظار الدفع' AND payment_status = 'pending' FOR UPDATE");
        $stmt->execute([$userId]);
        $pending = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($pending as $order) {
            $orderId = (int)($order['id'] ?? 0);
            if ($orderId <= 0) {
                continue;
            }
            $cancel = $this->db->prepare("UPDATE orders SET status = 'ملغي', user_hidden = 1, admin_hidden = 1 WHERE id = ? AND status = 'بانتظار الدفع'");
            $cancel->execute([$orderId]);
            if ($cancel->rowCount() > 0) {
                $products = json_decode($order['products'] ?? '[]', true);
                if (is_array($products)) {
                    $restock = $this->db->prepare("UPDATE products SET quantity = quantity + ? WHERE id = ?");
                    foreach ($products as $item) {
                        if (!is_array($item)) {
                            continue;
                        }
                        $qty = (int)($item['quantity'] ?? $item['number'] ?? 1);
                        $prodId = (int)($item['id'] ?? 0);
                        if ($prodId > 0 && $qty > 0) {
                            $restock->execute([$qty, $prodId]);
                        }
                    }
                }
            }
        }
    }

    public function getByUserId(int $userId): array {
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE user_id = ? AND user_hidden = 0 AND status != 'بانتظار الدفع' ORDER BY created_at DESC");
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

    public function getLastUserOrder(int $userId) {
        $stmt = $this->db->prepare("SELECT full_name, phone, address_line1, address_line2, city, governorate, zip_code FROM orders WHERE user_id = ? ORDER BY id DESC LIMIT 1");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByIdAndUser(int $id, int $userId): array|false {
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getRevenueSummary(string $start, string $end): array {
        $stmt = $this->db->prepare("SELECT COUNT(*) AS orders_count, COALESCE(SUM(subtotal), 0) AS gross, COALESCE(SUM(discount_amount), 0) AS discounts, COALESCE(SUM(shipping_fee), 0) AS shipping, COALESCE(SUM(total_price), 0) AS net FROM orders WHERE created_at BETWEEN ? AND ? AND admin_hidden = 0 AND ((payment_status = 'paid' AND status != 'ملغي') OR (payment_method = 'cod' AND status = 'مكتمل'))");
        $stmt->execute([$start, $end]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!is_array($row)) {
            $row = [];
        }
        $orders = (int)($row['orders_count'] ?? 0);
        $net = (float)($row['net'] ?? 0);
        return [
            'gross' => (float)($row['gross'] ?? 0),
            'discounts' => (float)($row['discounts'] ?? 0),
            'shipping' => (float)($row['shipping'] ?? 0),
            'net' => $net,
            'orders' => $orders,
            'aov' => $orders > 0 ? $net / $orders : 0
        ];
    }

    public function getDailyRevenue(string $start, string $end): array {
        $stmt = $this->db->prepare("SELECT DATE(created_at) AS day, COUNT(*) AS orders_count, COALESCE(SUM(total_price), 0) AS net FROM orders WHERE created_at BETWEEN ? AND ? AND admin_hidden = 0 AND ((payment_status = 'paid' AND status != 'ملغي') OR (payment_method = 'cod' AND status = 'مكتمل')) GROUP BY DATE(created_at) ORDER BY day ASC");
        $stmt->execute([$start, $end]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStatusBreakdown(string $start, string $end): array {
        $stmt = $this->db->prepare("SELECT status, COUNT(*) AS orders_count, COALESCE(SUM(total_price), 0) AS net FROM orders WHERE created_at BETWEEN ? AND ? AND admin_hidden = 0 AND status != 'بانتظار الدفع' GROUP BY status ORDER BY orders_count DESC");
        $stmt->execute([$start, $end]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function reclaimAbandonedOrders(int $timeoutMinutes = 30): int {
        $timeoutMinutes = max(1, $timeoutMinutes);
        $cutoff = date('Y-m-d H:i:s', strtotime('-' . $timeoutMinutes . ' minutes'));
        $stmt = $this->db->prepare("SELECT id FROM orders WHERE status = 'بانتظار الدفع' AND payment_status = 'pending' AND created_at < ? AND admin_hidden = 0");
        $stmt->execute([$cutoff]);
        $abandoned = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $reclaimed = 0;
        foreach ($abandoned as $order) {
            $orderId = (int)($order['id'] ?? 0);
            if ($orderId <= 0) {
                continue;
            }
            try {
                $this->db->beginTransaction();
                $check = $this->db->prepare("SELECT products FROM orders WHERE id = ? AND status = 'بانتظار الدفع' AND payment_status = 'pending' FOR UPDATE");
                $check->execute([$orderId]);
                $row = $check->fetch(PDO::FETCH_ASSOC);
                if (!is_array($row)) {
                    $this->db->rollBack();
                    continue;
                }
                $mark = $this->db->prepare("UPDATE orders SET status = 'ملغي', payment_status = 'failed', user_hidden = 1, admin_hidden = 1 WHERE id = ? AND status = 'بانتظار الدفع' AND payment_status = 'pending'");
                $mark->execute([$orderId]);
                if ($mark->rowCount() === 0) {
                    $this->db->rollBack();
                    continue;
                }
                $products = json_decode($row['products'] ?? '[]', true);
                if (is_array($products)) {
                    $restock = $this->db->prepare("UPDATE products SET quantity = quantity + ? WHERE id = ?");
                    foreach ($products as $item) {
                        if (!is_array($item)) {
                            continue;
                        }
                        $qty = (int)($item['quantity'] ?? $item['number'] ?? 1);
                        $prodId = (int)($item['id'] ?? 0);
                        if ($prodId > 0 && $qty > 0) {
                            $restock->execute([$qty, $prodId]);
                        }
                    }
                }
                $this->db->commit();
                $reclaimed++;
            } catch (Exception $e) {
                if ($this->db->inTransaction()) {
                    $this->db->rollBack();
                }
            }
        }
        return $reclaimed;
    }
}

if (!class_exists('App\Models\Order', false)) {
    class_alias('Order', 'App\Models\Order');
}
