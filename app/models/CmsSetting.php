<?php
// app/models/CmsSetting.php

class CmsSetting {
    private $db;
    private $table = 'cms_settings';

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->checkAndSeed();
    }

    // Mengambil semua konfigurasi CMS landing page
    public function getAll() {
        $query = "SELECT * FROM " . $this->table;
        try {
            $stmt = $this->db->query($query);
            $results = $stmt->fetchAll();
            
            $settings = [];
            foreach ($results as $row) {
                $settings[$row['key']] = $row['value'];
            }
            return $settings;
        } catch (PDOException $e) {
            error_log("Error getting CMS settings: " . $e->getMessage());
            return [];
        }
    }

    // Mengambil satu tetapan CMS berdasarkan kunci
    public function getByKey($key) {
        $query = "SELECT value FROM " . $this->table . " WHERE `key` = :key LIMIT 1";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':key', $key);
            $stmt->execute();
            $result = $stmt->fetch();
            return $result ? $result['value'] : null;
        } catch (PDOException $e) {
            error_log("Error getting CMS setting by key: " . $e->getMessage());
            return null;
        }
    }

    // Menyimpan atau mengemas kini nilai tetapan CMS
    public function updateKey($key, $value) {
        $query = "INSERT INTO " . $this->table . " (`key`, `value`) 
                  VALUES (:key, :value) 
                  ON DUPLICATE KEY UPDATE `value` = :value_update";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':key', $key);
            $stmt->bindParam(':value', $value);
            $stmt->bindParam(':value_update', $value);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error updating CMS setting: " . $e->getMessage());
            return false;
        }
    }

    // Menyemak dan memasukkan data lalai awal (Auto-Seeder)
    private function checkAndSeed() {
        try {
            // Cipta jadual sekiranya belum wujud
            $this->db->exec("CREATE TABLE IF NOT EXISTS `cms_settings` (
                `key` VARCHAR(50) PRIMARY KEY,
                `value` TEXT NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

            $stmt = $this->db->query("SELECT COUNT(*) as total FROM " . $this->table);
            $total = $stmt->fetch()['total'];

            if ($total == 0) {
                $defaults = [
                    'hero_title' => 'Pengecaman Klon Getah RISDA Pintar Menggunakan Kuasa AI',
                    'hero_desc' => 'Inisiatif pintar digital untuk pekebun kecil RISDA dan pegawai lapangan. Kenalpasti klon pokok getah dengan tepat dalam beberapa saat melalui imbasan morfologi daun secara masa nyata.',
                    'stat_scans' => '1,800+',
                    'stat_clones' => '150+',
                    'stat_officers' => '500+'
                ];

                foreach ($defaults as $key => $val) {
                    $this->updateKey($key, $val);
                }
            }
        } catch (PDOException $e) {
            error_log("Auto-seeder failed for cms_settings: " . $e->getMessage());
        }
    }
}
