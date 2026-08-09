<?php
// app/models/CloneSample.php

class CloneSample {
    private $db;
    private $table = 'clone_samples';

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Mengambil semua sampel klon yang aktif
    public function getAllActive() {
        $query = "SELECT * FROM " . $this->table . " WHERE status = 'active' ORDER BY clone_name ASC";
        try {
            $stmt = $this->db->query($query);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error fetching active clone samples: " . $e->getMessage());
            return false;
        }
    }

    // Mengambil sampel klon berdasarkan ID
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error fetching clone sample by ID: " . $e->getMessage());
            return false;
        }
    }

    // Menyimpan sampel klon baharu ke pangkalan data
    public function create($data) {
        $query = "INSERT INTO " . $this->table . " 
                  (clone_name, warisan, potensi_hasil, anggaran_kayu, bentuk_daun, bentuk_hujung_daun, bentuk_pangkal_daun, kedudukan_lai_daun, bentuk_tepi_daun, warna_daun_kilauan, permukaan_daun, pandangan_memanjang, pandangan_melintang, saiz_gagang_daun, saiz_anak_gagang, warna_lateks) 
                  VALUES (:clone_name, :warisan, :potensi_hasil, :anggaran_kayu, :bentuk_daun, :bentuk_hujung_daun, :bentuk_pangkal_daun, :kedudukan_lai_daun, :bentuk_tepi_daun, :warna_daun_kilauan, :permukaan_daun, :pandangan_memanjang, :pandangan_melintang, :saiz_gagang_daun, :saiz_anak_gagang, :warna_lateks)";

        try {
            $stmt = $this->db->prepare($query);

            $stmt->bindParam(':clone_name', $data['clone_name']);
            $stmt->bindParam(':warisan', $data['warisan']);
            $stmt->bindParam(':potensi_hasil', $data['potensi_hasil']);
            $stmt->bindParam(':anggaran_kayu', $data['anggaran_kayu']);
            $stmt->bindParam(':bentuk_daun', $data['bentuk_daun']);
            $stmt->bindParam(':bentuk_hujung_daun', $data['bentuk_hujung_daun']);
            $stmt->bindParam(':bentuk_pangkal_daun', $data['bentuk_pangkal_daun']);
            $stmt->bindParam(':kedudukan_lai_daun', $data['kedudukan_lai_daun']);
            $stmt->bindParam(':bentuk_tepi_daun', $data['bentuk_tepi_daun']);
            $stmt->bindParam(':warna_daun_kilauan', $data['warna_daun_kilauan']);
            $stmt->bindParam(':permukaan_daun', $data['permukaan_daun']);
            $stmt->bindParam(':pandangan_memanjang', $data['pandangan_memanjang']);
            $stmt->bindParam(':pandangan_melintang', $data['pandangan_melintang']);
            $stmt->bindParam(':saiz_gagang_daun', $data['saiz_gagang_daun']);
            $stmt->bindParam(':saiz_anak_gagang', $data['saiz_anak_gagang']);
            $stmt->bindParam(':warna_lateks', $data['warna_lateks']);

            if ($stmt->execute()) {
                return $this->db->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error creating clone sample: " . $e->getMessage());
            return false;
        }
    }

    // Mengemas kini sampel klon sedia ada
    public function update($id, $data) {
        $query = "UPDATE " . $this->table . " SET 
                  clone_name = :clone_name,
                  warisan = :warisan,
                  potensi_hasil = :potensi_hasil,
                  anggaran_kayu = :anggaran_kayu,
                  bentuk_daun = :bentuk_daun,
                  bentuk_hujung_daun = :bentuk_hujung_daun,
                  bentuk_pangkal_daun = :bentuk_pangkal_daun,
                  kedudukan_lai_daun = :kedudukan_lai_daun,
                  bentuk_tepi_daun = :bentuk_tepi_daun,
                  warna_daun_kilauan = :warna_daun_kilauan,
                  permukaan_daun = :permukaan_daun,
                  pandangan_memanjang = :pandangan_memanjang,
                  pandangan_melintang = :pandangan_melintang,
                  saiz_gagang_daun = :saiz_gagang_daun,
                  saiz_anak_gagang = :saiz_anak_gagang,
                  warna_lateks = :warna_lateks
                  WHERE id = :id";

        try {
            $stmt = $this->db->prepare($query);

            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':clone_name', $data['clone_name']);
            $stmt->bindParam(':warisan', $data['warisan']);
            $stmt->bindParam(':potensi_hasil', $data['potensi_hasil']);
            $stmt->bindParam(':anggaran_kayu', $data['anggaran_kayu']);
            $stmt->bindParam(':bentuk_daun', $data['bentuk_daun']);
            $stmt->bindParam(':bentuk_hujung_daun', $data['bentuk_hujung_daun']);
            $stmt->bindParam(':bentuk_pangkal_daun', $data['bentuk_pangkal_daun']);
            $stmt->bindParam(':kedudukan_lai_daun', $data['kedudukan_lai_daun']);
            $stmt->bindParam(':bentuk_tepi_daun', $data['bentuk_tepi_daun']);
            $stmt->bindParam(':warna_daun_kilauan', $data['warna_daun_kilauan']);
            $stmt->bindParam(':permukaan_daun', $data['permukaan_daun']);
            $stmt->bindParam(':pandangan_memanjang', $data['pandangan_memanjang']);
            $stmt->bindParam(':pandangan_melintang', $data['pandangan_melintang']);
            $stmt->bindParam(':saiz_gagang_daun', $data['saiz_gagang_daun']);
            $stmt->bindParam(':saiz_anak_gagang', $data['saiz_anak_gagang']);
            $stmt->bindParam(':warna_lateks', $data['warna_lateks']);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error updating clone sample: " . $e->getMessage());
            return false;
        }
    }

    // Memadam sampel klon berdasarkan ID
    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error deleting clone sample: " . $e->getMessage());
            return false;
        }
    }
}
