<?php
class NotificationController {
    public function markRead(): void {
        $this->mark(false);
    }

    public function markAllRead(): void {
        $this->mark(true);
    }

    private function mark(bool $all): void {
        header('Content-Type: application/json');
        $userId = (int)Session::get('user_id');
        if ($userId <= 0) {
            $this->respond(401, ['success' => false]);
            return;
        }

        $body = json_decode(file_get_contents('php://input'), true);
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $body['csrf_token'] ?? null;
        if (!is_string($token) || $token === '' || !CSRF::validate($token)) {
            $this->respond(403, ['success' => false]);
            return;
        }

        $notificationId = $all ? null : filter_var($body['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($notificationId === false) {
            $this->respond(400, ['success' => false]);
            return;
        }

        try {
            $notifications = new Notification();
            $saved = $all
                ? $notifications->markAllAsRead($userId)
                : $notifications->markAsRead($notificationId, $userId);
            if ($saved) {
                $this->respond(200, ['success' => true, 'unread_count' => $all ? 0 : $notifications->getUnreadCount($userId)]);
                return;
            }
        } catch (Throwable $e) {
            error_log('Notification read update failed: ' . $e->getMessage());
        }

        $this->respond(500, ['success' => false]);
    }

    private function respond(int $status, array $data): void {
        http_response_code($status);
        echo json_encode($data);
    }
}
