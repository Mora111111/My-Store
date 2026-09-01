<?php
class Setting {
    private PDO $db;
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    public function getSettings() {
        $stmt = $this->db->query("SELECT * FROM settings WHERE id = 1");
        return $stmt->fetch();
    }
    public function update(array $data): bool {
        $stmt =$this->db->prepare("UPDATE settings SET about_text = ?, phone1 = ?, phone2 = ?, email = ?, address = ?, shipping_cost = ?, facebook_link = ?, maintenance_mode = ? WHERE id = 1");
        return $stmt->execute([
            $data['about_text'],$data['phone1'],
            $data['phone2'],$data['email'],
            $data['address'],$data['shipping_cost'],
            $data['facebook_link'],$data['maintenance_mode']
        ]);
    }
}
