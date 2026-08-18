<?php
class ServiceController {
    public function index(): void {
        require_once APP_DIR . '/Views/layouts/header.php';
        require_once APP_DIR . '/Views/pages/services.php';
        require_once APP_DIR . '/Views/layouts/footer.php';
    }
}
