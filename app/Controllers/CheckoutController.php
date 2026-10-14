<?php
class CheckoutController {
   public function index(): void {
        if (!Session::get('user_id')) {
            header('Location: /login');
            exit;
        }
        $settingModel = new Setting();
        $site_settings = $settingModel->getSettings();
        
        $orderModel = new Order();
        $orderModel->reclaimAbandonedOrders(30);
        $lastOrder = $orderModel->getLastUserOrder(Session::get('user_id'));

        require_once APP_DIR . '/Views/layouts/header.php';
        require_once APP_DIR . '/Views/pages/payment.php';
        require_once APP_DIR . '/Views/layouts/footer.php';
    }

    public function process(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Session::get('user_id')) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'يرجى تسجيل الدخول لإتمام الطلب.']);
                exit;
            }
            if (isset($_SESSION['last_checkout_time']) && (time() - $_SESSION['last_checkout_time']) < 5) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'تم استلام طلبك بالفعل، يرجى الانتظار للحظات.']);
                exit;
            }
            $_SESSION['last_checkout_time'] = time();

            $orderModel = new Order();
            $productModel = new Product();
            $couponModel = new Coupon();
            $settingModel = new Setting();
            $db = Database::getInstance()->getConnection();

            $productsJson = $_POST['products'] ?? '[]';
            $cartProducts = json_decode($productsJson, true);
            $aggregatedCart = [];
            if (is_array($cartProducts)) {
                foreach ($cartProducts as $cartItem) {
                    if (!is_array($cartItem)) {
                        continue;
                    }
                    $pid = (int)($cartItem['id'] ?? 0);
                    if ($pid <= 0) {
                        continue;
                    }
                    $qty = max(1, intval($cartItem['number'] ?? $cartItem['quantity'] ?? 1));
                    if (!isset($aggregatedCart[$pid])) {
                        $aggregatedCart[$pid] = 0;
                    }
                    $aggregatedCart[$pid] += $qty;
                }
            }
            if (empty($aggregatedCart)) {
                unset($_SESSION['last_checkout_time']);
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'عربة التسوق فارغة!']);
                exit;
            }

            $activeCouponsRaw = $couponModel->getActiveStrikethroughCoupons();
            usort($activeCouponsRaw, function($a, $b) {
                if ($a['discount_type'] === $b['discount_type']) return $b['discount_value'] <=> $a['discount_value'];
                return $a['discount_type'] === 'percentage' ? -1 : 1;
            });
            $activeCoupons = $activeCouponsRaw;

            $appliedCoupon = null;
            if (!empty($_POST['applied_promo_code'])) {
                $stmt = $db->prepare("SELECT * FROM coupons WHERE code = ? AND status = 1 AND show_strikethrough = 0 LIMIT 1");
                $stmt->execute([trim($_POST['applied_promo_code'])]);
                $appliedCoupon = $stmt->fetch(PDO::FETCH_ASSOC);
            }

            $site_settings = $settingModel->getSettings();
            $payment_method = $_POST['payment_method'] ?? 'cod';
            if (in_array($payment_method, ['online_card', 'online_wallet'])) {
                $shipping = 0;
            } else {
                $shipping = floatval($site_settings['shipping_cost'] ?? 0);
            }

            try {
                $db->beginTransaction();
                $orderModel->cancelPendingPaymentOrdersForUser((int)Session::get('user_id'));

                $subtotal = 0;
                $grossSubtotal = 0;
                $secureProductsArray = [];
                foreach ($aggregatedCart as $pid => $qty) {
                    $lockStmt = $db->prepare("SELECT * FROM products WHERE id = ? LIMIT 1 FOR UPDATE");
                    $lockStmt->execute([$pid]);
                    $dbProduct = $lockStmt->fetch(PDO::FETCH_ASSOC);
                    if (!$dbProduct || (int)$dbProduct['quantity'] < $qty) {
                        $db->rollBack();
                        unset($_SESSION['last_checkout_time']);
                        $missingTitle = (is_array($dbProduct) && isset($dbProduct['title'])) ? $dbProduct['title'] : 'المنتج';
                        header('Content-Type: application/json');
                        echo json_encode(['success' => false, 'error' => 'عفواً، الكمية المطلوبة من "' . $missingTitle . '" غير متوفرة حالياً. قمنا بإعلام الإدارة وسنتواصل معك إذا توفرت كميات جديدة. شكراً لكم.']);
                        exit;
                    }

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
                        $finalPrice = $basePrice;
                        foreach($activeCoupons as $c) {
                            if($c['target_type'] === 'all' || ($c['target_type'] === 'specific_product' && $c['target_product_id'] == $dbProduct['id'])) {
                                if($c['discount_type'] === 'percentage') {
                                    $finalPrice = $basePrice - ($basePrice * ($c['discount_value'] / 100));
                                } else {
                                    $finalPrice = $basePrice - $c['discount_value'];
                                }
                                $finalPrice = max(0, $finalPrice);
                                break;
                            }
                        }
                    }

                    $subtotal += ($finalPrice * $qty);
                    $grossSubtotal += ($basePrice * $qty);

                    $secureProductsArray[] = [
                        'id' => $dbProduct['id'],
                        'src' => $dbProduct['image_url'],
                        'title' => $dbProduct['title'],
                        'price' => number_format($finalPrice, 2, '.', '') . ' ج.م',
                        'quantity' => $qty,
                        'number' => $qty
                    ];
                }

                $server_total = $subtotal + $shipping;
                $discountAmount = max(0, $grossSubtotal - $subtotal);
                $couponCode = $appliedCoupon['code'] ?? null;

                $orderStatus = 'قيد المراجعة';
                $transactionId = null;
                if ($payment_method === 'cod') {
                    $transactionId = 'COD-' . strtoupper(bin2hex(random_bytes(6)));
                } else {
                    $orderStatus = 'بانتظار الدفع';
                }

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
                    'subtotal' => $grossSubtotal,
                    'shipping_fee' => $shipping,
                    'discount_amount' => $discountAmount,
                    'coupon_code' => $couponCode,
                    'products' => json_encode($secureProductsArray, JSON_UNESCAPED_UNICODE),
                    'payment_method' => $payment_method,
                    'payment_status' => 'pending',
                    'status' => $orderStatus,
                    'transaction_id' => $transactionId
                ];

                if (empty($secureProductsArray)) {
                    $db->rollBack();
                    unset($_SESSION['last_checkout_time']);
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'error' => 'عربة التسوق فارغة!']);
                    exit;
                }

                foreach ($secureProductsArray as $item) {
                    if (!$productModel->deductStock((int)$item['id'], (int)$item['quantity'])) {
                        throw new RuntimeException('عفواً، الكمية المطلوبة من "' . $item['title'] . '" غير متوفرة حالياً. قمنا بإعلام الإدارة وسنتواصل معك إذا توفرت كميات جديدة. شكراً لكم.');
                    }
                }

                $orderId = $orderModel->create($data);
                if (!$orderId) {
                    throw new RuntimeException('Database error');
                }

                $redirectUrl = in_array($payment_method, ['online_card', 'online_wallet'])
                    ? '/payment/pay?order_id=' . $orderId
                    : '/my-orders';

                $db->commit();
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'redirect' => $redirectUrl]);
                exit;
            } catch (Throwable $e) {
                if ($db->inTransaction()) {
                    $db->rollBack();
                }
                unset($_SESSION['last_checkout_time']);
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
                exit;
            }
        }
    }
}