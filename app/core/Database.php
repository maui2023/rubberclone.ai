<?php
// app/core/Database.php

class Database {
    private $host = DB_HOST;
    private $port = DB_PORT;
    private $db_name = DB_NAME;
    private $username = DB_USER;
    private $password = DB_PASS;
    private $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $dsn = "mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
        } catch(PDOException $exception) {
            error_log("Database connection error: " . $exception->getMessage());
            
            // Mengembalikan respons JSON jika dipanggil semasa request API
            if (isset($_SERVER['CONTENT_TYPE']) && $_SERVER['CONTENT_TYPE'] === 'application/json' || 
                (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
                header('Content-Type: application/json');
                http_response_code(500);
                echo json_encode([
                    "status" => "error",
                    "message" => "Pangkalan data tidak bersambung. Sila pastikan MariaDB berjalan."
                ]);
            } else {
                // Respons HTML mesra pengguna jika dipanggil dari Web Portal
                echo "<div style='font-family: sans-serif; padding: 2rem; max-width: 600px; margin: 4rem auto; border: 1px solid #ffccd5; background: #fff5f5; border-radius: 8px; color: #900;'>";
                echo "<h2>Ralat Sambungan Pangkalan Data</h2>";
                echo "<p>Sistem tidak dapat berhubung dengan pangkalan data MariaDB. Sila pastikan konfigurasi pangkalan data adalah betul dan pelayan MariaDB sedang berjalan.</p>";
                echo "</div>";
            }
            exit;
        }

        return $this->conn;
    }
}
