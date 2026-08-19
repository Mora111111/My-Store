<?php
class Message {
    private PDO $db;
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    public function countAllContactMessages(): int {
        return (int)$this->db->query("SELECT COUNT(*) FROM contact_messages")->fetchColumn();
    }

    public function getAllContactMessages(): array {
        $stmt = $this->db->query("SELECT * FROM contact_messages ORDER BY id DESC");
        return $stmt->fetchAll();
    }
    public function replyContactMessage(int $id, string $reply): bool {
        $stmt = $this->db->prepare("UPDATE contact_messages SET reply = ? WHERE id = ?");
        return $stmt->execute([$reply, $id]);
    }
    public function getAllUserMessages(): array {
        $stmt = $this->db->query("SELECT * FROM messages ORDER BY id DESC");
        return $stmt->fetchAll();
    }
    public function replyUserMessage(int $id, string $reply): bool {
        $stmt = $this->db->prepare("UPDATE messages SET reply = ? WHERE id = ?");
        return $stmt->execute([$reply, $id]);
    }

    public function getByUserId(int $userId): array {
        $stmt = $this->db->prepare("SELECT * FROM messages WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function create(array $data): bool {
        $stmt = $this->db->prepare("INSERT INTO messages (user_id, subject, message) VALUES (?, ?, ?)");
        return $stmt->execute([$data['user_id'], $data['subject'], $data['message']]);
    }
    public function createContactMessage(array $data): bool {
        $stmt = $this->db->prepare("INSERT INTO contact_messages (first_name, last_name, phone, email, message) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$data['first_name'], $data['last_name'], $data['phone'], $data['email'], $data['message']]);
    }
    public function deleteMessage(int $id, string $type): bool {
        if ($type === 'user') {
            $stmt = $this->db->prepare("DELETE FROM messages WHERE id = ?");
        } else {
            $stmt = $this->db->prepare("DELETE FROM contact_messages WHERE id = ?");
        }
        return $stmt->execute([$id]);
    }
}
