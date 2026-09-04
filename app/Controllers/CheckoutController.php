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
            
            $subtotal = 0;
            $specific_product_subtotal = 0;
            $productModel = new Product();
            $couponModel = new Coupon();
            
            $activeCouponsRaw = $couponModel->getActiveStrikethroughCoupons();
            usort($activeCouponsRaw, function($a, $b) {
                if ($a['discount_type'] === $b['discount_type']) return $b['discount_value'] <=> $a['discount_value'];
                return $a['discount_type'] === 'percentage' ? -1 : 1;
            });
            $activeCoupons = $activeCouponsRaw;
            
            $appliedCoupon = null;
            if (!empty($_POST['applied_promo_code'])) {
                $db = Database::getInstance()->getConnection();
                $stmt = $db->prepare("SELECT * FROM coupons WHERE code = ? AND status = 1 LIMIT 1");
                $stmt->execute([trim($_POST['applied_promo_code'])]);
                $appliedCoupon = $stmt->fetch(PDO::FETCH_ASSOC);
            }
            
            // مصفوفة جديدة ومؤمنة لبناء تفاصيل المنتجات
            $secureProductsArray = [];

            if (is_array($cartProducts)) {
                foreach ($cartProducts as $cartItem) {
                    $dbProduct = $productModel->findById((int)$cartItem['id']);
                    if ($dbProduct) {
                        $price = floatval($dbProduct['price']);
                        $qty = intval($cartItem['number'] ?? $cartItem['quantity'] ?? 1);
                        $has_coupon_discount = false;
                        
                        foreach($activeCoupons as $c) {
                            if($c['target_type'] === 'all' || ($c['target_type'] === 'specific_product' && $c['target_product_id'] == $dbProduct['id'])) {
                                $has_coupon_discount = true;
                                if($c['discount_type'] === 'percentage') {
                                    $price = $price - ($price * ($c['discount_value'] / 100));
                                } else {
                                    $price = $price - $c['discount_value'];
                                }
                                $price = max(0, $price);
                                break;
                            }
                        }
                        
                        if(!$has_coupon_discount && !empty($dbProduct['old_price']) && $dbProduct['old_price'] > $dbProduct['price']) {
                            $price = floatval($dbProduct['price']);
                        }
                        
                        $item_total = $price * $qty;
                        $subtotal += $item_total;
                        
                        if ($appliedCoupon && $appliedCoupon['target_type'] === 'specific_product' && $appliedCoupon['target_product_id'] == $dbProduct['id']) {
                            $specific_product_subtotal += $item_total;
                        }

                        // بناء المنتج الآمن للاحتفاظ به في الطلب
                        $secureProductsArray[] = [
                            'id' => $dbProduct['id'],
                            'src' => $dbProduct['image_url'], // أخذ الصورة من السيرفر
                            'title' => $dbProduct['title'], // أخذ الاسم الحقيقي
                            'price' => $price, // أخذ السعر الحقيقي بعد حساب الخصومات
                            'quantity' => $qty // الكمية المطلوبة
                        ];
                    }
                }
            }

            // تحويل المصفوفة الآمنة إلى JSON بدلاً من استخدام بيانات المتصفح
            $secureProductsJson = json_encode($secureProductsArray, JSON_UNESCAPED_UNICODE);

            $discount_total = 0;
            if ($appliedCoupon && $appliedCoupon['show_strikethrough'] == 0) {
                if ($appliedCoupon['target_type'] === 'all') {
                    if ($appliedCoupon['discount_type'] === 'percentage') {
                        $discount_total = $subtotal * ($appliedCoupon['discount_value'] / 100);
                    } else {
                        $discount_total = $appliedCoupon['discount_value'];
                    }
                } elseif ($appliedCoupon['target_type'] === 'specific_product' && $specific_product_subtotal > 0) {
                    if ($appliedCoupon['discount_type'] === 'percentage') {
                        $discount_total = $specific_product_subtotal * ($appliedCoupon['discount_value'] / 100);
                    } else {
                        $discount_total = $appliedCoupon['discount_value'];
                    }
                    if ($discount_total > $specific_product_subtotal) $discount_total = $specific_product_subtotal;
                }
            }
            
            $server_total = $subtotal - $discount_total;
            $server_total = max(0, $server_total);

            $settingModel = new Setting();
            $site_settings = $settingModel->getSettings();
            $shipping = floatval($site_settings['shipping_cost'] ?? 0);
            $server_total += $shipping;
            
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
                'products' => $secureProductsJson // الاعتماد على المصفوفة المؤمنة هنا
            ];

            $isAjax = !empty($_POST['ajax_checkout']) || (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');

            if ($orderModel->create($data)) {
                if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['success' => true, 'redirect' => '/my-orders']); exit; }
                header('Location: /?order_success=1'); exit;
            }
            
            if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['success' => false, 'error' => 'Database error']); exit; }
            header('Location: /checkout?error=1'); exit;
        }
    }
}