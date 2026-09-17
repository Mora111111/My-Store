<?php

class Setting {
    private PDO $db;
    protected string $table = 'settings';

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // جلب كافة الإعدادات (ستعود كمصفوفة تمثل الصف الأول والوحيد)
    public function getSettings() {
        $stmt = $this->db->query("SELECT * FROM {$this->table} WHERE id = 1 LIMIT 1");
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    // تحديث الإعدادات
    public function update(array $data): bool {
        $fields = '';
        foreach ($data as $key => $value) {
            $fields .= "{$key} = :{$key}, ";
        }
        $fields = rtrim($fields, ', ');

        $stmt = $this->db->prepare("UPDATE {$this->table} SET {$fields} WHERE id = 1");
        return $stmt->execute($data);
    }

    public function updateSettings(array $data): bool {
        return $this->update($data);
    }
}