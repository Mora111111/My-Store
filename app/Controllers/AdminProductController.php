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
            $data = [
                'title' => trim($_POST['title'] ?? ''),
                'title_en' => trim($_POST['title_en'] ?? ''),
                'price' => floatval($_POST['price'] ?? 0),
                'old_price' => floatval($_POST['old_price'] ?? 0),
                'category_class' => trim($_POST['category_class'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'description_en' => trim($_POST['description_en'] ?? ''),
                'quantity' => intval($_POST['quantity'] ?? 10)
            ];

            $imageFields = ['image' => 'image_url', 'image_2' => 'image_2', 'image_3' => 'image_3', 'image_4' => 'image_4'];
            $uploadDir = ROOT_DIR . '/uploads/';
            
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            foreach ($imageFields as $fileKey => $dbField) {
                $securePath = $this->handleSecureImageUpload($fileKey, $uploadDir);
                if ($securePath !== null) {
                    $data[$dbField] = $securePath;
                }
            }

            if (empty($data['image_url'])) {
                $_SESSION['toast_msg'] = 'يرجى اختيار صورة للمنتج.';
                $_SESSION['toast_type'] = 'error';
                header('Location: /admin/products');
                exit;
            }

            if ($productModel->create($data)) {
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

    private function handleSecureImageUpload(string $fileKey, string $uploadDir): ?string {
        if (!isset($_FILES[$fileKey]) || $_FILES[$fileKey]['error'] !== UPLOAD_ERR_OK) {
            return null;
        }
        $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $allowedMime = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $ext = strtolower(pathinfo($_FILES[$fileKey]['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExt, true)) {
            return null;
        }
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo === false) {
            return null;
        }
        $mime = finfo_file($finfo, $_FILES[$fileKey]['tmp_name']);
        finfo_close($finfo);
        if (!in_array($mime, $allowedMime, true)) {
            return null;
        }
        if (@getimagesize($_FILES[$fileKey]['tmp_name']) === false) {
            return null;
        }
        $tmpName = $_FILES[$fileKey]['tmp_name'];
        if (defined('IMGBB_API_KEY') && !empty(IMGBB_API_KEY)) {
            try {
                $imageBinary = @file_get_contents($tmpName);
                if ($imageBinary !== false) {
                    $chImgbb = curl_init('https://api.imgbb.com/1/upload');
                    curl_setopt($chImgbb, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($chImgbb, CURLOPT_POST, true);
                    curl_setopt($chImgbb, CURLOPT_TIMEOUT, 15);
                    curl_setopt($chImgbb, CURLOPT_POSTFIELDS, [
                        'key' => IMGBB_API_KEY,
                        'image' => base64_encode($imageBinary)
                    ]);
                    $imgbbResponse = curl_exec($chImgbb);
                    curl_close($chImgbb);
                    $imgbbData = json_decode((string)$imgbbResponse, true);
                    $imgbbUrl = $imgbbData['data']['url'] ?? '';
                    if (is_string($imgbbUrl) && str_starts_with($imgbbUrl, 'http')) {
                        return $imgbbUrl;
                    }
                }
            } catch (Throwable $e) {
            }
        }
        $fileName = time() . '_' . uniqid() . '.' . $ext;
        $targetPath = $uploadDir . $fileName;
        $binaryData = @file_get_contents($tmpName);
        if (!move_uploaded_file($tmpName, $targetPath)) {
            return null;
        }
        $relativePath = 'uploads/' . $fileName;
        if ($binaryData !== false) {
            Product::saveUploadedFileBackup($relativePath, $mime, $binaryData);
        }
        return $relativePath;
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
                'title_en' => trim($_POST['title_en'] ?? ''),
                'price' => floatval($_POST['price'] ?? 0),
                'old_price' => floatval($_POST['old_price'] ?? 0),
                'category_class' => trim($_POST['category_class'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'description_en' => trim($_POST['description_en'] ?? ''),
                'quantity' => intval($_POST['quantity'] ?? 10)
            ];

            $imageFields = ['image' => 'image_url', 'image_2' => 'image_2', 'image_3' => 'image_3', 'image_4' => 'image_4'];
            $uploadDir = ROOT_DIR . '/uploads/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            foreach ($imageFields as $fileKey => $dbField) {
                $securePath = $this->handleSecureImageUpload($fileKey, $uploadDir);
                if ($securePath !== null) {
                    $data[$dbField] = $securePath;

                    if (!empty($existing[$dbField]) && file_exists(ROOT_DIR . '/' . ltrim($existing[$dbField], '/'))) {
                        unlink(ROOT_DIR . '/' . ltrim($existing[$dbField], '/'));
                    }
                    if (!empty($existing[$dbField]) && !str_starts_with($existing[$dbField], 'http')) {
                        Product::deleteUploadedFileBackup($existing[$dbField]);
                    }
                }
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
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !CSRF::validate($_POST['csrf_token'])) {
                $_SESSION['toast_msg'] = 'فشل التحقق من أمان الطلب.';
                $_SESSION['toast_type'] = 'error';
                header('Location: /admin/products');
                exit;
            }
            
            $productModel = new Product();
            $id = intval($_POST['id'] ?? 0);
            $product = $productModel->findById($id);
            
            if ($product) {
                $imageFields = ['image_url', 'image_2', 'image_3', 'image_4'];
                foreach ($imageFields as $field) {
                    if (!empty($product[$field]) && file_exists(ROOT_DIR . '/' . ltrim($product[$field], '/'))) {
                        unlink(ROOT_DIR . '/' . ltrim($product[$field], '/'));
                    }
                    if (!empty($product[$field]) && !str_starts_with($product[$field], 'http')) {
                        Product::deleteUploadedFileBackup($product[$field]);
                    }
                }
                
                $productModel->delete($id);
                $_SESSION['toast_msg'] = 'تم حذف المنتج بنجاح.';
                $_SESSION['toast_type'] = 'success';
            }
        }
        header('Location: /admin/products');
        exit;
    }
}