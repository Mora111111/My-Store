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
            
            $activeCoupons = $couponModel->getActiveStrikethroughCoupons();
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
                    if ($dbProduct) {
                        $basePrice = floatval($dbProduct['price']);
                        $qty = intval($cartItem['number'] ?? $cartItem['quantity'] ?? 1);
                        $finalPrice = $basePrice;
                        $promoAppliedToItem = false;

                        // 1. الأولوية للكوبون اليدوي (يلغي الشطب التلقائي)
                        if ($appliedCoupon) {
                            if ($appliedCoupon['target_type'] === 'all' || ($appliedCoupon['target_type'] === 'specific_product' && $appliedCoupon['target_product_id'] == $dbProduct['id'])) {
                                $promoAppliedToItem = true;
                                if ($appliedCoupon['discount_type'] === 'percentage') {
                                    $finalPrice = $basePrice - ($basePrice * ($appliedCoupon['discount_value'] / 100));
                                } else {
                                    $finalPrice = $basePrice - $appliedCoupon['discount_value'];
                                }
                            }
                        }

                        // 2. الخصم التلقائي (يطبق فقط إذا لم يكن هناك كوبون يدوي لهذا المنتج)
                        if (!$promoAppliedToItem) {
                            foreach ($activeCoupons as $c) {
                                if ($c['target_type'] === 'all' || ($c['target_type'] === 'specific_product' && $c['target_product_id'] == $dbProduct['id'])) {
                                    if ($c['discount_type'] === 'percentage') {
                                        $finalPrice = $basePrice - ($basePrice * ($c['discount_value'] / 100));
                                    } else {
                                        $finalPrice = $basePrice - $c['discount_value'];
                                    }
                                    break;
                                }
                            }
                        }

                        $finalPrice = max(0, $finalPrice);
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