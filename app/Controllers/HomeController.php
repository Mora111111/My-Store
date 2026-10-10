<?php
class HomeController {
    public function landing(): void {
        require_once APP_DIR . '/Views/pages/landing.php';
    }
    public function store(): void {
        $productModel = new Product();$featuredProducts = $productModel->getFeatured();$latestProducts = $productModel->getLatest();$globalCouponModel = new Coupon();
        $activeCouponsRaw =$globalCouponModel->getActiveStrikethroughCoupons();
        
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
