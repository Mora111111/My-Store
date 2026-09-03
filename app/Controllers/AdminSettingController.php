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
            try {
                $db = Database::getInstance()->getConnection();$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                // Check if row id=1 exists, if not, create it
                $check =$db->query("SELECT id FROM settings WHERE id = 1")->fetch();
                if (!$check) {$db->query("INSERT INTO settings (id, about_text, phone1, phone2, email, address, shipping_cost, facebook_link, maintenance_mode, global_discount) VALUES (1, 'متجرنا', '010', '', 'email@test.com', 'مصر', 0, '', 0, 0)");
                }
                
                $settingModel = new Setting();
                $success =$settingModel->update([
                    'about_text' => trim($_POST['about_text'] ?? ''),
                    'phone1' => trim($_POST['phone1'] ?? ''),
                    'phone2' => trim($_POST['phone2'] ?? ''),
                    'email' => trim($_POST['email'] ?? ''),
                    'address' => trim($_POST['address'] ?? ''),
                    'shipping_cost' => floatval($_POST['shipping_cost'] ?? 0),
                    'facebook_link' => trim($_POST['facebook_link'] ?? ''),
                    'maintenance_mode' => isset($_POST['maintenance_mode']) ? 1 : 0,
                    'global_discount' => floatval($_POST['global_discount'] ?? 0)                  ]);$_SESSION['toast_msg'] = 'تم حفظ الإعدادات بنجاح!';
                $_SESSION['toast_type'] = 'success';
                header('Location: /admin/settings');
                exit;
                
            } catch (PDOException $e) {
                die("<div style='direction:ltr; text-align:left; padding:20px; background:#f8d7da; color:#721c24; border:1px solid #f5c6cb; margin:20px; font-family:sans-serif;'><h3>Database Error!</h3><p><strong>Message:</strong> " . $e->getMessage() . "</p></div>");
            }
        }
        header('Location: /admin/settings');
        exit;
    }
}
