<?php
// app/models/User.php

class User {
    private $db;
    private $table = 'users';

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Mendaftar pengguna baru
    public function create($data) {
        $query = "INSERT INTO " . $this->table . " 
                  (email, username, password_hash, fullname, agency, status, role, registration_date) 
                  VALUES (:email, :username, :password_hash, :fullname, :agency, :status, :role, :registration_date)";
        
        try {
            $stmt = $this->db->prepare($query);
            
            // Hash kata laluan menggunakan Bcrypt secara selamat
            $password_hash = password_hash($data['password'], PASSWORD_BCRYPT);
            
            // Nilai lalai (default values)
            $status = isset($data['status']) ? $data['status'] : 'active';
            $role = isset($data['role']) ? $data['role'] : 'user';
            $agency = isset($data['agency']) ? $data['agency'] : 'RISDA Pekebun Kecil';
            
            // Unix timestamp milisaat
            $registration_date = isset($data['registration_date']) ? $data['registration_date'] : round(microtime(true) * 1000);

            $stmt->bindParam(':email', $data['email']);
            $stmt->bindParam(':username', $data['username']);
            $stmt->bindParam(':password_hash', $password_hash);
            $stmt->bindParam(':fullname', $data['fullname']);
            $stmt->bindParam(':agency', $agency);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':role', $role);
            $stmt->bindParam(':registration_date', $registration_date);

            if ($stmt->execute()) {
                return $this->db->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error creating user: " . $e->getMessage());
            return false;
        }
    }

    // Mengambil rekod pengguna berdasarkan E-mel
    public function getByEmail($email) {
        $query = "SELECT * FROM " . $this->table . " WHERE email = :email LIMIT 1";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error fetching user by email: " . $e->getMessage());
            return false;
        }
    }

    // Mengambil rekod pengguna berdasarkan ID (tanpa password hash)
    public function getById($id) {
        $query = "SELECT id, email, username, fullname, agency, status, role, registration_date, created_at 
                  FROM " . $this->table . " WHERE id = :id LIMIT 1";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error fetching user by ID: " . $e->getMessage());
            return false;
        }
    }

    // Senarai lengkap semua pengguna (untuk kegunaan Pentadbir RISDA)
    public function getAllUsers() {
        $query = "SELECT u.id, u.email, u.username, u.fullname, u.agency, u.status, u.role, u.registration_date,
                         (SELECT COUNT(*) FROM analysis_records WHERE user_id = u.id) as total_scans 
                  FROM " . $this->table . " u 
                  ORDER BY u.created_at DESC";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error fetching all users: " . $e->getMessage());
            return false;
        }
    }

    // Mengemas kini status aktif/nyahaktif pengguna
    public function updateStatus($id, $status) {
        $query = "UPDATE " . $this->table . " SET status = :status WHERE id = :id";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error updating user status: " . $e->getMessage());
            return false;
        }
    }
}
