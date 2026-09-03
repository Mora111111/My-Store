<?php
class HomeController {
    public function index(): void {
        $productModel = new Product();$featuredProducts = $productModel->getFeatured();$latestProducts = $productModel->getLatest();$globalCouponModel = new Coupon();
        $activeCouponsRaw =$globalCouponModel->getActiveStrikethroughCoupons();
        
        // Sort percentage first, then fixed, descending value to ensure best deal applies
        usort($activeCouponsRaw, function($a,$b) {
            if ($a['discount_type'] ===$b['discount_type']) return $b['discount_value'] <=>$a['discount_value'];
            return $a['discount_type'] === 'percentage' ? -1 : 1;
        });
        $activeCoupons =$activeCouponsRaw;
        
        require_once APP_DIR . '/Views/layouts/header.php';
        require_once APP_DIR . '/Views/pages/home.php';
        require_once APP_DIR . '/Views/layouts/footer.php';
    }
}
