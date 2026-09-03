<?php
class CheckoutController {
    public function index(): void {
        if (!Session::get('user_id')) {
            header('Location: /login');
            exit;
        }
        $settingModel = new Setting();
        $site_settings =$settingModel->getSettings();
        require_once APP_DIR . '/Views/layouts/header.php';
        require_once APP_DIR . '/Views/pages/payment.php';
        require_once APP_DIR . '/Views/layouts/footer.php';
    }
    public function process(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {$orderModel = new Order();
            
            // Server-Side Pricing Validation (NEVER trust client total_price)
            $productsJson = $_POST['products'] ?? '[]';$cartProducts = json_decode($productsJson, true);$server_total = 0;
            
            $productModel = new Product();$couponModel = new Coupon();
            $activeCoupons =$couponModel->getActiveStrikethroughCoupons();
            
            // Fetch applied manual coupon if any
            $appliedCoupon = null;
            if (!empty($_POST['applied_promo_code'])) {$db = Database::getInstance()->getConnection();
                $stmt =$db->prepare("SELECT * FROM coupons WHERE code = ? AND status = 1 LIMIT 1");
                $stmt->execute([trim($_POST['applied_promo_code'])]);
                $appliedCoupon =$stmt->fetch(PDO::FETCH_ASSOC);
            }
            
            if (is_array($cartProducts)) {
                foreach ($cartProducts as $cartItem) {$dbProduct = $productModel->findById((int)$cartItem['id']);
                    if ($dbProduct) {
                        $price = floatval($dbProduct['price']);
                        $qty = intval($cartItem['number']);
                        
                        // Apply dynamic discount logic server-side if exists
                        $has_coupon_discount = false;
                        foreach($activeCoupons as$c) {
                            if($c['target_type'] === 'all' || ($c['target_type'] === 'specific_product' &&$c['target_product_id'] == $dbProduct['id'])) {$has_coupon_discount = true;
                                if($c['discount_type'] === 'percentage') {
                                    $price =$price - ($price * ($c['discount_value'] / 100));
                                } else {
                                    $price = $price -$c['discount_value'];
                                }
                                $price = max(0,$price);
                                break;
                            }
                        }
                        
                        if(!$has_coupon_discount && !empty($dbProduct['old_price']) && $dbProduct['old_price'] >$dbProduct['price']) {
                            $price = floatval($dbProduct['price']);
                        }
                        
                        $item_total = $price * $qty;
                        
                        // Apply manual coupon if applicable to THIS item
                        if ($appliedCoupon &&$appliedCoupon['show_strikethrough'] == 0) {
                            if ($appliedCoupon['target_type'] === 'all' || ($appliedCoupon['target_type'] === 'specific_product' && $appliedCoupon['target_product_id'] ==$dbProduct['id'])) {
                                if ($appliedCoupon['discount_type'] === 'percentage') {$item_total -= ($item_total * ($appliedCoupon['discount_value'] / 100));
                                } else {
                                    $item_total -= ($appliedCoupon['discount_value'] *$qty);
                                }
                            }
                        }
                        
                        $server_total += max(0,$item_total);
                    }
                }
            }

            // Add shipping cost from settings
            $settingModel = new Setting();
            $site_settings =$settingModel->getSettings();
            $shipping = floatval($site_settings['shipping_cost'] ?? 0);
            $server_total +=$shipping;
            
            $data = [
                'user_id' => Session::get('user_id'),
                'full_name' => $_POST['full_name'] ?? '',
                'phone' => $_POST['phone'] ?? '',
                'address_line1' => $_POST['address_line1'] ?? '',
                'address_line2' => $_POST['address_line2'] ?? '',
                'city' => $_POST['city'] ?? '',
                'governorate' => $_POST['governorate'] ?? '',
                'zip_code' => $_POST['zip_code'] ?? '',
                'total_price' => $server_total, // SECURE OVERRIDE
                'products' => $productsJson
            ];

            $isAjax = !empty($_POST['ajax_checkout']) || (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');

            if ($orderModel->create($data)) {
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true, 'redirect' => '/my-orders']);
                    exit;
                }
                header('Location: /?order_success=1');
                exit;
            }
            
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Database error']);
                exit;
            }
            header('Location: /checkout?error=1');
            exit;
        }
    }
}
