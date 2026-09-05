<?php
class CouponApiController {
   public function validate(): void {
    header('Content-Type: application/json');
    $data = json_decode(file_get_contents('php://input'), true);
    $code = strtoupper(trim($data['code'] ?? ''));

    if (empty($code)) {
        echo json_encode(['success' => false, 'message' => 'الرجاء إدخال كود الخصم.']); exit;
    }

    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT * FROM coupons WHERE code = ? AND status = 1 AND show_strikethrough = 0 LIMIT 1");
    $stmt->execute([$code]);
    $coupon = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$coupon) {
        echo json_encode(['success' => false, 'message' => 'الكود غير صالح أو منتهي الصلاحية.']); exit;
    }

    echo json_encode([
        'success' => true,
        'coupon' => [
            'code' => $coupon['code'],
            'discount_type' => $coupon['discount_type'],
            'discount_value' => floatval($coupon['discount_value']),
            'target_type' => $coupon['target_type'],
            'target_product_id' => $coupon['target_product_id']
        ],
        'message' => 'تم تطبيق الكود بنجاح!'
    ]);
    exit;
}
}
?>
