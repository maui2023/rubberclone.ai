<?php
// app/controllers/AuthController.php

class AuthController extends Controller {
    private $userModel;

    public function __construct() {
        $this->userModel = $this->model('User');
    }

    // Pendaftaran Pengguna Baharu (POST /api/auth/register)
    public function register() {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            $this->jsonResponse(["status" => "error", "message" => "Format data input tidak sah."], 400);
        }

        // Ujian kesahihan input wajib
        $requiredFields = ['email', 'username', 'password', 'fullname'];
        foreach ($requiredFields as $field) {
            if (empty($input[$field]) || !is_string($input[$field])) {
                $this->jsonResponse(["status" => "error", "message" => "Medan '$field' adalah wajib dan mestilah dalam format teks."], 400);
            }
        }

        // 1. Validasi Format E-mel (OWASP A03:2021)
        $email = trim($input['email']);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->jsonResponse(["status" => "error", "message" => "Format alamat e-mel tidak sah."], 400);
        }

        // 2. Semak Had Panjang & Kekuatan Kata Laluan (Bypass password re-use/brute-force)
        $password = $input['password'];
        if (strlen($password) < 8) {
            $this->jsonResponse(["status" => "error", "message" => "Kata laluan mestilah sekurang-kurangnya 8 aksara."], 400);
        }

        // 3. Sanitasi Input Teks untuk Mencegah Serangan XSS (OWASP A03:2021)
        $username = trim(htmlspecialchars(strip_tags($input['username']), ENT_QUOTES, 'UTF-8'));
        $fullname = trim(htmlspecialchars(strip_tags($input['fullname']), ENT_QUOTES, 'UTF-8'));
        $agency = isset($input['agency']) ? trim(htmlspecialchars(strip_tags($input['agency']), ENT_QUOTES, 'UTF-8')) : 'RISDA Pekebun Kecil';

        // Validasi aksara Username (hanya alfanumerik dan underscore dibenarkan)
        if (!preg_match('/^[a-zA-Z0-9_]{3,30}$/', $username)) {
            $this->jsonResponse(["status" => "error", "message" => "Nama pengguna (username) hanya boleh mengandungi huruf, nombor, garis bawah (_) dan panjang antara 3 hingga 30 aksara."], 400);
        }

        // Semak kewujudan e-mel pendua
        if ($this->userModel->getByEmail($email)) {
            $this->jsonResponse(["status" => "error", "message" => "Alamat e-mel ini telah berdaftar dalam sistem."], 400);
        }

        // Sediakan data tersanitasi
        $sanitizedData = [
            'email' => $email,
            'username' => $username,
            'password' => $password,
            'fullname' => $fullname,
            'agency' => $agency
        ];

        // Proses pendaftaran
        $userId = $this->userModel->create($sanitizedData);

        if ($userId) {
            $this->jsonResponse(["status" => "success", "message" => "Pengguna berjaya didaftarkan."], 201);
        } else {
            $this->jsonResponse(["status" => "error", "message" => "Ralat pelayan semasa mendaftar akaun."], 500);
        }
    }

    // Log Masuk Pengguna & Jana Token (POST /api/auth/login)
    public function login() {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input || empty($input['email']) || empty($input['password'])) {
            $this->jsonResponse(["status" => "error", "message" => "E-mel dan kata laluan diperlukan."], 400);
        }

        $email = trim($input['email']);
        $password = trim($input['password']);

        // Semak format e-mel sebelum membuat pertanyaan DB
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->jsonResponse(["status" => "error", "message" => "Format alamat e-mel tidak sah."], 400);
        }

        // Mengambil data pengguna
        $user = $this->userModel->getByEmail($email);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            // OWASP: Mesej ralat generik untuk mengelakkan penuaian nama pengguna/e-mel (Username enumeration prevention)
            $this->jsonResponse(["status" => "error", "message" => "Alamat e-mel atau kata laluan adalah salah."], 401);
        }

        // Sekatan log masuk jika status pengguna dinyahaktifkan (inactive)
        if ($user['status'] === 'inactive') {
            $this->jsonResponse([
                "status" => "error",
                "message" => "Akaun anda telah dinyahaktifkan oleh Pentadbir RISDA. Sila hubungi pihak pengurusan."
            ], 403);
        }

        // Maklumat payload JWT
        $payload = [
            'id' => $user['id'],
            'email' => $user['email'],
            'username' => $user['username'],
            'role' => $user['role']
        ];

        // Menjana Token Bearer
        $token = JWT::generate($payload);

        $this->jsonResponse([
            "status" => "success",
            "token" => $token,
            "user" => [
                "id" => (int)$user['id'],
                "email" => $user['email'],
                "username" => $user['username'],
                "fullname" => $user['fullname'],
                "agency" => $user['agency'],
                "role" => $user['role'],
                "status" => $user['status']
            ]
        ], 200);
    }
}
