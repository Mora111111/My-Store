<?php
class UserProfileController {
    public function __construct() {
        if (!Session::get('user_id')) {
            header('Location: /login');
            exit;
        }
    }
    public function index(): void {
        $userModel = new User();
        $user = $userModel->findById(Session::get('user_id'));
        require_once APP_DIR . '/Views/layouts/header.php';
        require_once APP_DIR . '/Views/pages/profile.php';
        require_once APP_DIR . '/Views/layouts/footer.php';
    }
    public function update(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userModel = new User();
            $data = [
                'name' => $_POST['name'] ?? '',
                'email' => $_POST['email'] ?? ''
            ];
            $userModel->updateProfile(Session::get('user_id'), $data);
            Session::set('user_name', $data['name']);
            header('Location: /profile?success=1');
            exit;
        }
    }
    public function orders(): void {
        $orderModel = new Order();
        $orders = $orderModel->getByUserId(Session::get('user_id'));
        require_once APP_DIR . '/Views/layouts/header.php';
        require_once APP_DIR . '/Views/pages/my_orders.php';
        require_once APP_DIR . '/Views/layouts/footer.php';
    }

    public function cancelOrder(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!CSRF::validate($_POST['csrf_token'] ?? '')) {
                header('Location: /my-orders?cancel_error=Invalid CSRF Token');
                exit;
            }
            $orderId = (int)($_POST['order_id'] ?? 0);
            $userId = Session::get('user_id');
            if ($orderId && $userId) {
                $orderModel = new Order();
                $orderModel->cancelUserOrder($orderId, $userId);
            }
            header('Location: /my-orders');
            exit;
        }
    }

    public function hideOrder(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!CSRF::validate($_POST['csrf_token'] ?? '')) {
                header('Location: /my-orders?cancel_error=Invalid CSRF Token');
                exit;
            }
            $orderId = (int)($_POST['order_id'] ?? 0);
            $userId = Session::get('user_id');
            if ($orderId && $userId) {
                $orderModel = new Order();
                $orderModel->hideUserOrder($orderId, $userId);
            }
            header('Location: /my-orders');
            exit;
        }
    }
    public function messages(): void {
        $messageModel = new Message();
        $messages = $messageModel->getByUserId(Session::get('user_id'));
        require_once APP_DIR . '/Views/layouts/header.php';
        require_once APP_DIR . '/Views/pages/my_messages.php';
        require_once APP_DIR . '/Views/layouts/footer.php';
    }
    public function sendMessage(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $messageModel = new Message();
            $data = [
                'user_id' => Session::get('user_id'),
                'subject' => $_POST['subject'] ?? 'بدون عنوان',
                'message' => $_POST['message'] ?? ''
            ];
            $messageModel->create($data);
            header('Location: /my-messages?success=1');
            exit;
        }
    }

    public function toggleFavorite(): void {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            exit;
        }
        
        $userId = Session::get('user_id');
        if (!$userId) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $productId = $input['product_id'] ?? null;
        $csrfToken = $input['csrf_token'] ?? '';

        if (!CSRF::validate($csrfToken)) {
            http_response_code(403);
            echo json_encode(['error' => 'Invalid CSRF token']);
            exit;
        }

        if (!$productId) {
            http_response_code(400);
            echo json_encode(['error' => 'Product ID required']);
            exit;
        }

        $favoriteModel = new Favorite();
        $status = $favoriteModel->toggleFavorite((int)$userId, (int)$productId);
        
        echo json_encode(['success' => true, 'status' => $status]);
        exit;
    }
}
