<?php
// app/models/Announcement.php

class Announcement {
    private $db;
    private $table = 'announcements';

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Mengambil semua pekeliling aktif yang sudah tiba tarikh siaran (publish_at <= sekarang) dan belum luput (expires_at > sekarang)
    public function getActiveAnnouncements() {
        $nowMs = (int)(microtime(true) * 1000);
        
        $sql = "SELECT * FROM " . $this->table . " 
                WHERE status = 'active' 
                  AND publish_at <= :now_publish 
                  AND (expires_at IS NULL OR expires_at > :now_expires) 
                ORDER BY publish_at DESC";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':now_publish', $nowMs);
            $stmt->bindParam(':now_expires', $nowMs);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error fetching active announcements: " . $e->getMessage());
            return false;
        }
    }

    // Mengambil semua pekeliling (untuk Pentadbir)
    public function getAll() {
        $sql = "SELECT * FROM " . $this->table . " ORDER BY publish_at DESC";
        try {
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error fetching all announcements: " . $e->getMessage());
            return false;
        }
    }

    // Mengambil pekeliling mengikut ID
    public function getById($id) {
        $sql = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error fetching announcement by ID: " . $e->getMessage());
            return false;
        }
    }

    // Tambah pekeliling baharu
    public function create($data) {
        $sql = "INSERT INTO " . $this->table . " (title, content, publish_at, expires_at, status, author) 
                VALUES (:title, :content, :publish_at, :expires_at, :status, :author)";
        
        try {
            $stmt = $this->db->prepare($sql);
            $status = $data['status'] ?? 'active';
            $author = $data['author'] ?? 'RISDA Pentadbir';

            $stmt->bindParam(':title', $data['title']);
            $stmt->bindParam(':content', $data['content']);
            $stmt->bindParam(':publish_at', $data['publish_at']);
            $stmt->bindParam(':expires_at', $data['expires_at']);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':author', $author);

            if ($stmt->execute()) {
                return $this->db->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error creating announcement: " . $e->getMessage());
            return false;
        }
    }

    // Kemas kini pekeliling
    public function update($id, $data) {
        $sql = "UPDATE " . $this->table . " 
                SET title = :title, 
                    content = :content, 
                    publish_at = :publish_at, 
                    expires_at = :expires_at, 
                    status = :status 
                WHERE id = :id";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':title', $data['title']);
            $stmt->bindParam(':content', $data['content']);
            $stmt->bindParam(':publish_at', $data['publish_at']);
            $stmt->bindParam(':expires_at', $data['expires_at']);
            $stmt->bindParam(':status', $data['status']);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error updating announcement: " . $e->getMessage());
            return false;
        }
    }

    // Padam pekeliling
    public function delete($id) {
        $sql = "DELETE FROM " . $this->table . " WHERE id = :id";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error deleting announcement: " . $e->getMessage());
            return false;
        }
    }
}
