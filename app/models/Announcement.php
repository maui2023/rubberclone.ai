<?php
// app/models/Announcement.php

class Announcement {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Mengambil semua pekeliling aktif yang sudah tiba tarikh siaran (publish_at <= sekarang) dan belum luput (expires_at > sekarang)
    public function getActiveAnnouncements() {
        $nowMs = (int)(microtime(true) * 1000);
        
        $sql = "SELECT * FROM announcements 
                WHERE status = 'active' 
                  AND publish_at <= :now_publish 
                  AND (expires_at IS NULL OR expires_at > :now_expires) 
                ORDER BY publish_at DESC";
        
        $this->db->query($sql);
        $this->db->bind(':now_publish', $nowMs);
        $this->db->bind(':now_expires', $nowMs);
        
        return $this->db->resultSet();
    }

    // Mengambil semua pekeliling (untuk Pentadbir)
    public function getAll() {
        $this->db->query("SELECT * FROM announcements ORDER BY publish_at DESC");
        return $this->db->resultSet();
    }

    // Mengambil pekeliling mengikut ID
    public function getById($id) {
        $this->db->query("SELECT * FROM announcements WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    // Tambah pekeliling baharu
    public function create($data) {
        $sql = "INSERT INTO announcements (title, content, publish_at, expires_at, status, author) 
                VALUES (:title, :content, :publish_at, :expires_at, :status, :author)";
        
        $this->db->query($sql);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':content', $data['content']);
        $this->db->bind(':publish_at', $data['publish_at']);
        $this->db->bind(':expires_at', $data['expires_at']);
        $this->db->bind(':status', $data['status'] ?? 'active');
        $this->db->bind(':author', $data['author'] ?? 'RISDA Pentadbir');

        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    // Kemas kini pekeliling
    public function update($id, $data) {
        $sql = "UPDATE announcements 
                SET title = :title, 
                    content = :content, 
                    publish_at = :publish_at, 
                    expires_at = :expires_at, 
                    status = :status 
                WHERE id = :id";
        
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':content', $data['content']);
        $this->db->bind(':publish_at', $data['publish_at']);
        $this->db->bind(':expires_at', $data['expires_at']);
        $this->db->bind(':status', $data['status']);

        return $this->db->execute();
    }

    // Padam pekeliling
    public function delete($id) {
        $this->db->query("DELETE FROM announcements WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
