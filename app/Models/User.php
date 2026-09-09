<?php
class User {
    private PDO $db;
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    public function findByEmail(string $email) {
        $stmt = $this->db->prepare("SELECT * FROM elogin WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function create(array $data): int|false {
        $name = htmlspecialchars($data['name'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $stmt = $this->db->prepare("INSERT INTO elogin (name, full_name, email, password, role) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([
            $name,
            $name,
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['role'] ?? 'user'
        ]) ? (int)$this->db->lastInsertId() : false;
    }

    public function countAll(): int {
        return (int)$this->db->query("SELECT COUNT(*) FROM elogin")->fetchColumn();
    }

    public function getAll(): array {
        $stmt = $this->db->query("SELECT * FROM elogin ORDER BY id DESC");
        return $stmt->fetchAll();
    }

    public function toggleBan(int $id, int $status): bool {
        $stmt = $this->db->prepare("UPDATE elogin SET is_banned = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM elogin WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function findById(int $id) {
        $stmt = $this->db->prepare("SELECT * FROM elogin WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function updateProfile(int $id, array $data): bool {
        // قمنا بإزالة تحديث الإيميل من هنا نهائياً
        $stmt = $this->db->prepare("UPDATE elogin SET name = ? WHERE id = ?");
        return $stmt->execute([$data['name'], $id]);
    }

    public function updatePassword(int $id, string $hashedPassword): bool {
        $stmt = $this->db->prepare("UPDATE elogin SET password = ? WHERE id = ?");
        return $stmt->execute([$hashedPassword, $id]);
    }
}
