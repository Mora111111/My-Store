<?php
class AdminProductController {
    public function __construct() {
        if (Session::get('user_role') !== 'admin') {
            header('Location: /');
            exit;
        }
    }

    public function index(): void {
        $productModel = new Product();
        $products = $productModel->getAll();
        $showSearch = true;
        $toast_msg = $_SESSION['toast_msg'] ?? '';
        $toast_type = $_SESSION['toast_type'] ?? '';
        unset($_SESSION['toast_msg'], $_SESSION['toast_type']);
        $pageIcon = 'fa-box-open';
        $pageTitle = 'إدارة المنتجات';
        require_once APP_DIR . '/Views/admin/layout_start.php';
        require_once APP_DIR . '/Views/admin/products/index.php';
        require_once APP_DIR . '/Views/admin/layout_end.php';
    }

    public function create(): void {
        $pageIcon = 'fa-plus-circle';
        $pageTitle = 'إضافة منتج جديد';
        require_once APP_DIR . '/Views/admin/layout_start.php';
        require_once APP_DIR . '/Views/admin/products/create.php';
        require_once APP_DIR . '/Views/admin/layout_end.php';
    }

    public function store(): void {
        $productModel = new Product();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $category_class = trim($_POST['category_class'] ?? '');
            $price = floatval($_POST['price'] ?? 0);
            $description = trim($_POST['description'] ?? '');
            $imagePath = '';
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $apiKey = 'e177b4ddf3cf1d337cd1dff5feaff484'; // Replace with actual key
                $imageTmpName = $_FILES['image']['tmp_name'];
                $imageData = base64_encode(file_get_contents($imageTmpName));
                
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://api.imgbb.com/1/upload');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, [
                    'key' => $apiKey,
                    'image' => $imageData
                ]);
                
                $response = curl_exec($ch);
                curl_close($ch);
                
                $json = json_decode($response, true);
                if (isset($json['data']['url'])) {
                    $imagePath = $json['data']['url'];
                } else {
                    // Handle API error silently or set a default placeholder
                    $imagePath = '/images/logos/logo.png'; 
                }
            } else {
                $_SESSION['toast_msg'] = 'يرجى اختيار صورة للمنتج.';
                $_SESSION['toast_type'] = 'error';
                header('Location: /admin/products');
                exit;
            }
            
            $image_url = $imagePath;

            if ($productModel->create([
                'title' => $title,
                'price' => $price,
                'category_class' => $category_class,
                'description' => $description,
                'image_url' => $image_url
            ])) {
                $_SESSION['toast_msg'] = 'تمت إضافة المنتج بنجاح!';
                $_SESSION['toast_type'] = 'success';
            } else {
                $_SESSION['toast_msg'] = 'حدث خطأ في قاعدة البيانات.';
                $_SESSION['toast_type'] = 'error';
            }
        }

        header('Location: /admin/products');
        exit;
    }

    public function edit(): void {
        $productModel = new Product();
        $id = intval($_GET['id'] ?? 0);
        $product = $productModel->findById($id);
        if (!$product) {
            header('Location: /admin/products');
            exit;
        }
        $toast_msg = $_SESSION['toast_msg'] ?? '';
        $toast_type = $_SESSION['toast_type'] ?? '';
        unset($_SESSION['toast_msg'], $_SESSION['toast_type']);
        $pageIcon = 'fa-pen-to-square';
        $pageTitle = 'تعديل المنتج';
        require_once APP_DIR . '/Views/admin/layout_start.php';
        require_once APP_DIR . '/Views/admin/products/edit.php';
        require_once APP_DIR . '/Views/admin/layout_end.php';
    }

    public function update(): void {
        $productModel = new Product();
        $id = intval($_POST['id'] ?? 0);
        $existing = $productModel->findById($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $existing) {
            $data = [
                'title' => trim($_POST['title'] ?? ''),
                'category_class' => trim($_POST['category_class'] ?? ''),
                'price' => floatval($_POST['price'] ?? 0),
                'description' => trim($_POST['description'] ?? '')
            ];

            $imagePath = '';
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $apiKey = 'e177b4ddf3cf1d337cd1dff5feaff484'; // Replace with actual key
                $imageTmpName = $_FILES['image']['tmp_name'];
                $imageData = base64_encode(file_get_contents($imageTmpName));
                
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://api.imgbb.com/1/upload');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, [
                    'key' => $apiKey,
                    'image' => $imageData
                ]);
                
                $response = curl_exec($ch);
                curl_close($ch);
                
                $json = json_decode($response, true);
                if (isset($json['data']['url'])) {
                    $imagePath = $json['data']['url'];
                } else {
                    // Handle API error silently or set a default placeholder
                    $imagePath = '/images/logos/logo.png'; 
                }
                
                if (!empty($existing['image_url']) && file_exists(ROOT_DIR . '/' . $existing['image_url'])) {
                    unlink(ROOT_DIR . '/' . $existing['image_url']);
                }
                $data['image_url'] = $imagePath;
            }

            if ($productModel->update($id, $data)) {
                $_SESSION['toast_msg'] = 'تم تعديل المنتج بنجاح!';
                $_SESSION['toast_type'] = 'success';
            } else {
                $_SESSION['toast_msg'] = 'حدث خطأ أثناء التحديث.';
                $_SESSION['toast_type'] = 'error';
                header('Location: /admin/products/edit?id=' . $id);
                exit;
            }
        }

        header('Location: /admin/products');
        exit;
    }

    public function delete(): void {
        $productModel = new Product();
        $id = intval($_GET['id'] ?? 0);
        $product = $productModel->findById($id);
        if ($product) {
            if (!empty($product['image_url']) && file_exists(ROOT_DIR . '/' . $product['image_url'])) {
                unlink(ROOT_DIR . '/' . $product['image_url']);
            }
            $productModel->delete($id);
        }
        header('Location: /admin/products');
        exit;
    }
}
