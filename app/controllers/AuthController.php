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
            if (empty($input[$field])) {
                $this->jsonResponse(["status" => "error", "message" => "Medan '$field' adalah wajib."], 400);
            }
        }

        // Semak kewujudan e-mel pendua
        if ($this->userModel->getByEmail($input['email'])) {
            $this->jsonResponse(["status" => "error", "message" => "Alamat e-mel ini telah berdaftar dalam sistem."], 400);
        }

        // Proses pendaftaran
        $userId = $this->userModel->create($input);

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

        // Mengambil data pengguna
        $user = $this->userModel->getByEmail($input['email']);

        if (!$user || !password_verify($input['password'], $user['password_hash'])) {
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
