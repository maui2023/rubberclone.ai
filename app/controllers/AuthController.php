<?php
// app/controllers/AuthController.php

class AuthController extends Controller {
    private $userModel;

    public function __construct() {
        $this->userModel = $this->model('User');
    }

    // Pendaftaran Pengguna Baharu (POST /api/auth/register) - KINI DITUTUP (Hanya Admin boleh menambah pengguna)
    public function register() {
        $this->jsonResponse([
            "status" => "error",
            "message" => "Pendaftaran awam ditutup. Sila hubungi Pentadbir RISDA untuk pendaftaran akaun baharu."
        ], 403);
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
