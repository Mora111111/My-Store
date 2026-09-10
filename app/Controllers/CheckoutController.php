<?php
class CheckoutController {
    public function index(): void {
        if (!Session::get('user_id')) {
            header('Location: /login');
            exit;
        }
        $settingModel = new Setting();
        $site_settings = $settingModel->getSettings();
        require_once APP_DIR . '/Views/layouts/header.php';
        require_once APP_DIR . '/Views/pages/payment.php';
        require_once APP_DIR . '/Views/layouts/footer.php';
    }

    public function process(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $orderModel = new Order();
            $productsJson = $_POST['products'] ?? '[]';
            $cartProducts = json_decode($productsJson, true);

            $productModel = new Product();
            $couponModel = new Coupon();
            $settingModel = new Setting();

            $activeCouponsRaw = $couponModel->getActiveStrikethroughCoupons();
            usort($activeCouponsRaw, function($a, $b) {
                if ($a['discount_type'] === $b['discount_type']) return $b['discount_value'] <=> $a['discount_value'];
                return $a['discount_type'] === 'percentage' ? -1 : 1;
            });
            $activeCoupons = $activeCouponsRaw;

            $appliedCoupon = null;
            if (!empty($_POST['applied_promo_code'])) {
                $db = Database::getInstance()->getConnection();
                $stmt = $db->prepare("SELECT * FROM coupons WHERE code = ? AND status = 1 AND show_strikethrough = 0 LIMIT 1");
                $stmt->execute([trim($_POST['applied_promo_code'])]);
                $appliedCoupon = $stmt->fetch(PDO::FETCH_ASSOC);
            }

            $subtotal = 0;
            $secureProductsArray = [];

            if (is_array($cartProducts)) {
                foreach ($cartProducts as $cartItem) {
                    $dbProduct = $productModel->findById((int)$cartItem['id']);
                    if (!$dbProduct) continue;

                    $qty = max(1, intval($cartItem['number'] ?? $cartItem['quantity'] ?? 1));
                    $basePrice = floatval($dbProduct['price']);

                    $manualPrice = null;
                    if ($appliedCoupon) {
                        $matchesTarget = $appliedCoupon['target_type'] === 'all'
                            || ($appliedCoupon['target_type'] === 'specific_product' && $appliedCoupon['target_product_id'] == $dbProduct['id']);
                        if ($matchesTarget) {
                            $manualPrice = $appliedCoupon['discount_type'] === 'percentage'
                                ? $basePrice - ($basePrice * ($appliedCoupon['discount_value'] / 100))
                                : $basePrice - $appliedCoupon['discount_value'];
                            $manualPrice = max(0, $manualPrice);
                        }
                    }

                    if ($manualPrice !== null) {
                        $finalPrice = $manualPrice;
                    } else {
                        $discountResult = Product::calculateDiscount($dbProduct, $activeCoupons);
                        $finalPrice = $discountResult['final_price'];
                    }

                    $subtotal += ($finalPrice * $qty);

                    $secureProductsArray[] = [
                        'id' => $dbProduct['id'],
                        'src' => $dbProduct['image_url'],
                        'title' => $dbProduct['title'],
                        'price' => number_format($finalPrice, 2, '.', '') . ' ج.م',
                        'quantity' => $qty
                    ];
                }
            }

            $site_settings = $settingModel->getSettings();
            $shipping = floatval($site_settings['shipping_cost'] ?? 0);
            $server_total = $subtotal + $shipping;

            $data = [
                'user_id' => Session::get('user_id'),
                'full_name' => $_POST['full_name'] ?? '',
                'phone' => $_POST['phone'] ?? '',
                'address_line1' => $_POST['address_line1'] ?? '',
                'address_line2' => $_POST['address_line2'] ?? '',
                'city' => $_POST['city'] ?? '',
                'governorate' => $_POST['governorate'] ?? '',
                'zip_code' => $_POST['zip_code'] ?? '',
                'total_price' => $server_total,
                'products' => json_encode($secureProductsArray, JSON_UNESCAPED_UNICODE)
            ];

            if ($orderModel->create($data)) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'redirect' => '/my-orders']);
                exit;
            }

            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Database error']);
            exit;
        }
    }
}