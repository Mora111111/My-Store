<?php
class CouponApiController {
    public function validate(): void {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request']); exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $code = strtoupper(trim($data['code'] ?? ''));

        if (empty($code)) {
            echo json_encode(['success' => false, 'message' => 'الرجاء إدخال كود الخصم.']); exit;
        }

        $db = Database::getInstance()->getConnection();
        $stmt =$db->prepare("SELECT * FROM coupons WHERE code = ? AND status = 1 LIMIT 1");
        $stmt->execute([$code]);
        $coupon =$stmt->fetch(PDO::FETCH_ASSOC);

        if (!$coupon) {
            echo json_encode(['success' => false, 'message' => 'الكود غير صالح أو منتهي الصلاحية.']); exit;
        }

        if ($coupon['show_strikethrough'] == 1) {
            echo json_encode(['success' => false, 'message' => 'هذا العرض مطبق بالفعل تلقائياً على أسعار المنتجات ولا يحتاج لإدخال الكود.']); exit;
        }

        echo json_encode([
            'success' => true,
            'coupon' => [
                'code' => $coupon['code'],
                'type' => $coupon['discount_type'],
                'value' => floatval($coupon['discount_value']),
                'target' => $coupon['target_type'],
                'product_id' => intval($coupon['target_product_id'])
            ],
            'message' => 'تم تطبيق الكود بنجاح!'
        ]);
        exit;
    }
}
?>
