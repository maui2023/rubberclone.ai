<?php
// app/models/AnalysisRecord.php

class AnalysisRecord {
    private $db;
    private $table = 'analysis_records';

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Menyimpan rekod imbasan baharu ke pangkalan data
    public function create($data) {
        $query = "INSERT INTO " . $this->table . " 
                  (user_id, clone_name, confidence, timestamp, latitude, longitude, location_name, image_url, notes, soil_type, rainfall, elevation) 
                  VALUES (:user_id, :clone_name, :confidence, :timestamp, :latitude, :longitude, :location_name, :image_url, :notes, :soil_type, :rainfall, :elevation)";
        
        try {
            $stmt = $this->db->prepare($query);

            $notes = isset($data['notes']) ? $data['notes'] : '';
            $soil_type = isset($data['soil_type']) ? $data['soil_type'] : 'Tiada Maklumat';
            $rainfall = isset($data['rainfall']) ? $data['rainfall'] : 'Tiada Maklumat';
            $elevation = isset($data['elevation']) ? $data['elevation'] : 'Tiada Maklumat';
            $image_url = isset($data['image_url']) ? $data['image_url'] : null;

            $stmt->bindParam(':user_id', $data['user_id']);
            $stmt->bindParam(':clone_name', $data['clone_name']);
            $stmt->bindParam(':confidence', $data['confidence']);
            $stmt->bindParam(':timestamp', $data['timestamp']);
            $stmt->bindParam(':latitude', $data['latitude']);
            $stmt->bindParam(':longitude', $data['longitude']);
            $stmt->bindParam(':location_name', $data['location_name']);
            $stmt->bindParam(':image_url', $image_url);
            $stmt->bindParam(':notes', $notes);
            $stmt->bindParam(':soil_type', $soil_type);
            $stmt->bindParam(':rainfall', $rainfall);
            $stmt->bindParam(':elevation', $elevation);

            if ($stmt->execute()) {
                return $this->db->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error creating analysis record: " . $e->getMessage());
            return false;
        }
    }

    // Mengambil sejarah imbasan bagi pengguna tertentu sahaja (untuk peranti mudah alih)
    public function getByUserId($user_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE user_id = :user_id ORDER BY timestamp DESC";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error fetching user analysis records: " . $e->getMessage());
            return false;
        }
    }

    // Mengambil semua sejarah imbasan dengan maklumat lengkap penyumbang (untuk Papan Pemuka Pentadbir)
    public function getAllRecords() {
        $query = "SELECT r.*, u.username, u.fullname, u.agency 
                  FROM " . $this->table . " r 
                  JOIN users u ON r.user_id = u.id 
                  ORDER BY r.timestamp DESC";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error fetching all analysis records: " . $e->getMessage());
            return false;
        }
    }

    // Memadam rekod imbasan tertentu
    public function delete($id, $user_id = null, $is_admin = false) {
        if ($is_admin) {
            $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        } else {
            $query = "DELETE FROM " . $this->table . " WHERE id = :id AND user_id = :user_id";
        }

        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            if (!$is_admin) {
                $stmt->bindParam(':user_id', $user_id);
            }
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error deleting analysis record: " . $e->getMessage());
            return false;
        }
    }

    // Memadam semua rekod imbasan bagi pengguna tertentu
    public function clearByUserId($user_id) {
        $query = "DELETE FROM " . $this->table . " WHERE user_id = :user_id";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error clearing user analysis records: " . $e->getMessage());
            return false;
        }
    }

    // --- KAEDAH LOGIK ANALITIK DASHBOARD ---

    // Dapatkan data ringkasan metrik dashboard
    public function getStatsSummary() {
        try {
            // Jumlah imbasan keseluruhan
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM " . $this->table);
            $total_scans = $stmt->fetch()['total'];

            // Jumlah imbasan pada hari ini
            $today_start = strtotime('today') * 1000;
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM " . $this->table . " WHERE timestamp >= :today_start");
            $stmt->bindParam(':today_start', $today_start);
            $stmt->execute();
            $scans_today = $stmt->fetch()['total'];

            // Jumlah akaun pengguna berdaftar
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM users WHERE role = 'user'");
            $total_users = $stmt->fetch()['total'];

            // Jumlah akaun pengguna berstatus aktif
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM users WHERE role = 'user' AND status = 'active'");
            $active_users = $stmt->fetch()['total'];

            return [
                'total_users' => (int)$total_users,
                'active_users' => (int)$active_users,
                'total_scans' => (int)$total_scans,
                'scans_today' => (int)$scans_today
            ];
        } catch (PDOException $e) {
            error_log("Error getting stats summary: " . $e->getMessage());
            return [];
        }
    }

    // Dapatkan kekerapan imbasan mengikut nama klon getah (Top 5)
    public function getScansByClone() {
        $query = "SELECT clone_name, COUNT(*) as count 
                  FROM " . $this->table . " 
                  GROUP BY clone_name 
                  ORDER BY count DESC 
                  LIMIT 5";
        try {
            $stmt = $this->db->query($query);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting scans by clone: " . $e->getMessage());
            return [];
        }
    }

    // Dapatkan kekerapan imbasan mengikut agensi RISDA
    public function getScansByAgency() {
        $query = "SELECT u.agency, COUNT(r.id) as count 
                  FROM " . $this->table . " r 
                  JOIN users u ON r.user_id = u.id 
                  GROUP BY u.agency 
                  ORDER BY count DESC";
        try {
            $stmt = $this->db->query($query);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting scans by agency: " . $e->getMessage());
            return [];
        }
    }
}
