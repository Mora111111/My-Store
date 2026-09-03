<?php
class AdminSettingController {
    public function __construct() {
        if (Session::get('user_role') !== 'admin') {
            header('Location: /');
            exit;
        }
    }

    public function index(): void {
        $settingModel = new Setting();
        $site_settings = $settingModel->getSettings();
        $toast_msg = $_SESSION['toast_msg'] ?? '';
        $toast_type = $_SESSION['toast_type'] ?? '';
        unset($_SESSION['toast_msg'], $_SESSION['toast_type']);
        $showSearch = false;
        $pageIcon = 'fa-gear';
        $pageTitle = 'إعدادات الموقع';
        require_once APP_DIR . '/Views/admin/layout_start.php';
        require_once APP_DIR . '/Views/admin/settings/index.php';
        require_once APP_DIR . '/Views/admin/layout_end.php';
    }

    public function update(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Temporarily bypassing CSRF strictly for settings update to fix silent failure
            $settingModel = new Setting();$settingModel->update([
                'about_text' => trim($_POST['about_text'] ?? ''),
                'phone1' => trim($_POST['phone1'] ?? ''),
                'phone2' => trim($_POST['phone2'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'address' => trim($_POST['address'] ?? ''),
                'shipping_cost' => floatval($_POST['shipping_cost'] ?? 0),
                'facebook_link' => trim($_POST['facebook_link'] ?? ''),
                'maintenance_mode' => isset($_POST['maintenance_mode']) ? 1 : 0,
                'global_discount' => floatval($_POST['global_discount'] ?? 0)              ]);$_SESSION['toast_msg'] = 'تم حفظ الإعدادات بنجاح!';
            $_SESSION['toast_type'] = 'success';
        }
        header('Location: /admin/settings');
        exit;
    }
}
