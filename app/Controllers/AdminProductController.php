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
            $image_url = '';

            if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                $check = getimagesize($_FILES['image']['tmp_name']);
                if ($check !== false && in_array($ext, $allowed)) {
                    $new_name = time() . '_' . uniqid() . '.' . $ext;
                    $upload_dir = ROOT_DIR . '/uploads';
                    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
                    $dest = $upload_dir . '/' . $new_name;
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
                        $image_url = 'uploads/' . $new_name;
                    } else {
                        $_SESSION['toast_msg'] = 'حدث خطأ أثناء رفع الصورة.';
                        $_SESSION['toast_type'] = 'error';
                        header('Location: /admin/products');
                        exit;
                    }
                } else {
                    $_SESSION['toast_msg'] = 'صيغة الملف غير مدعومة.';
                    $_SESSION['toast_type'] = 'error';
                    header('Location: /admin/products');
                    exit;
                }
            } else {
                $_SESSION['toast_msg'] = 'يرجى اختيار صورة للمنتج.';
                $_SESSION['toast_type'] = 'error';
                header('Location: /admin/products');
                exit;
            }

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

            if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                $check = getimagesize($_FILES['image']['tmp_name']);
                if ($check !== false && in_array($ext, $allowed)) {
                    $new_name = time() . '_' . uniqid() . '.' . $ext;
                    $upload_dir = ROOT_DIR . '/uploads';
                    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
                    $dest = $upload_dir . '/' . $new_name;
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
                        if (!empty($existing['image_url']) && file_exists(ROOT_DIR . '/' . $existing['image_url'])) {
                            unlink(ROOT_DIR . '/' . $existing['image_url']);
                        }
                        $data['image_url'] = 'uploads/' . $new_name;
                    } else {
                        $_SESSION['toast_msg'] = 'خطأ في رفع الصورة الجديدة.';
                        $_SESSION['toast_type'] = 'error';
                        header('Location: /admin/products/edit?id=' . $id);
                        exit;
                    }
                } else {
                    $_SESSION['toast_msg'] = 'صيغة الملف غير مدعومة.';
                    $_SESSION['toast_type'] = 'error';
                    header('Location: /admin/products/edit?id=' . $id);
                    exit;
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
