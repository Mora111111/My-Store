<?php
class HomeController {
    public function index(): void {
        $productModel = new Product();
        $featuredProducts = $productModel->getFeatured();
        $latestProducts = $productModel->getLatest();
        require_once APP_DIR . '/Views/layouts/header.php';
        require_once APP_DIR . '/Views/pages/home.php';
        require_once APP_DIR . '/Views/layouts/footer.php';
    }
}
