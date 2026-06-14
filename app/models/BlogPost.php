<?php
// app/models/BlogPost.php

class BlogPost {
    private $db;
    private $table = 'blog_posts';

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->checkAndSeed();
    }

    // Cipta kisah blog baharu
    public function create($data) {
        $query = "INSERT INTO " . $this->table . " (title, content, image_url, author) 
                  VALUES (:title, :content, :image_url, :author)";
        try {
            $stmt = $this->db->prepare($query);
            
            $author = isset($data['author']) ? $data['author'] : 'RISDA Pentadbir';
            $image_url = isset($data['image_url']) ? $data['image_url'] : null;

            $stmt->bindParam(':title', $data['title']);
            $stmt->bindParam(':content', $data['content']);
            $stmt->bindParam(':image_url', $image_url);
            $stmt->bindParam(':author', $author);

            if ($stmt->execute()) {
                return $this->db->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error creating blog post: " . $e->getMessage());
            return false;
        }
    }

    // Mengambil semua kisah blog mengikut tarikh terkini
    public function getAll() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY created_at DESC";
        try {
            $stmt = $this->db->query($query);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting all blog posts: " . $e->getMessage());
            return [];
        }
    }

    // Memadam blog post berdasarkan ID
    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error deleting blog post: " . $e->getMessage());
            return false;
        }
    }

    // Membina jadual dan auto-memasukkan data permulaan (Auto-Seeder)
    private function checkAndSeed() {
        try {
            $this->db->exec("CREATE TABLE IF NOT EXISTS `blog_posts` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `title` VARCHAR(255) NOT NULL,
                `content` TEXT NOT NULL,
                `image_url` VARCHAR(255) DEFAULT NULL,
                `author` VARCHAR(100) DEFAULT 'RISDA Pentadbir',
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

            $stmt = $this->db->query("SELECT COUNT(*) as total FROM " . $this->table);
            $total = $stmt->fetch()['total'];

            if ($total == 0) {
                // Sampel blog stories agro-kesesuaian
                $blogs = [
                    [
                        'title' => 'Klon Getah RRIM 3001: Revolusi Hasil Lateks Tinggi RISDA',
                        'content' => 'Klon getah siri RRIM 3001 kini menjadi pilihan utama dalam projek penanaman semula RISDA kerana potensinya untuk menghasilkan susu getah berkualiti tinggi dan ketahanan terhadap penyakit luruhan daun. Melalui penggunaan kecerdasan buatan Rubber Clone AI, verifikasi keaslian baka tapak semaian kini dapat dilakukan serta-merta tanpa ralat manual.',
                        'image_url' => 'assets/images/rubber_clone_mockup.png',
                        'author' => 'Dr. Ahmad Subri, Unit Agronomi RISDA'
                    ],
                    [
                        'title' => 'Bagaimana AI Membantu Pekebun Kecil RISDA Mengoptimumkan Hasil Lateks',
                        'content' => 'Penggunaan aplikasi pengecaman klon daun membolehkan pegawai lapangan RISDA memetakan jenis klon mengikut topografi tanah di seluruh Malaysia. Gabungan analisis Gemini AI serta metadata agro-kesesuaian (elevasi, taburan hujan, tanah) memastikan pekebun kecil mendapat pulangan pelaburan pertanian yang optimum.',
                        'image_url' => null,
                        'author' => 'Unit Komunikasi RISDA'
                    ]
                ];

                foreach ($blogs as $b) {
                    $this->create($b);
                }
            }
        } catch (PDOException $e) {
            error_log("Auto-seeder failed for blog_posts: " . $e->getMessage());
        }
    }
}
