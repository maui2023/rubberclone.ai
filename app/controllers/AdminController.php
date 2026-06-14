<?php
// app/controllers/AdminController.php

class AdminController extends Controller {
    private $userModel;
    private $analysisModel;

    public function __construct() {
        // Mulakan session PHP untuk pengesahan halaman web
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->userModel = $this->model('User');
        $this->analysisModel = $this->model('AnalysisRecord');
    }

    // Memastikan pengguna adalah admin sebelum memaparkan halaman web
    private function checkWebAuth() {
        if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
            // Jika ia request biasa, paparkan borang log masuk
            $this->view('admin/login', ['title' => 'Log Masuk Pentadbir - Rubber Clone AI']);
            exit;
        }
    }

    // Memastikan pengesahan untuk request API (Menyokong JWT Bearer Token ATAU Session Admin)
    private function checkApiAuth() {
        // 1. Cuba semak Session dahulu (untuk request AJAX dari portal)
        if (isset($_SESSION['admin_id']) && $_SESSION['admin_role'] === 'admin') {
            return [
                'id' => $_SESSION['admin_id'],
                'role' => 'admin'
            ];
        }

        // 2. Jika tiada Session, semak JWT Bearer Token (untuk Postman/Ujian luar)
        $token = JWT::getBearerToken();
        if (!$token) {
            $this->jsonResponse(["status" => "error", "message" => "Akses ditolak. Token tidak dibekalkan."], 401);
        }

        $payload = JWT::verify($token);
        if (!$payload || $payload['role'] !== 'admin') {
            $this->jsonResponse(["status" => "error", "message" => "Akses ditolak. Kebenaran pentadbir diperlukan."], 403);
        }

        return $payload;
    }

    // Mengendalikan log masuk web pentadbir (POST dari borang login portal)
    public function loginWeb() {
        if (isset($_POST['email']) && isset($_POST['password'])) {
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);

            $user = $this->userModel->getByEmail($email);
            if ($user && password_verify($password, $user['password_hash']) && $user['role'] === 'admin') {
                if ($user['status'] === 'inactive') {
                    $this->view('admin/login', [
                        'title' => 'Log Masuk Pentadbir - Rubber Clone AI',
                        'error' => 'Akaun anda telah dinyahaktifkan. Sila hubungi pembangun.'
                    ]);
                    exit;
                }

                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_username'] = $user['username'];
                $_SESSION['admin_fullname'] = $user['fullname'];
                $_SESSION['admin_role'] = $user['role'];

                header("Location: /admin/dashboard");
                exit;
            } else {
                $this->view('admin/login', [
                    'title' => 'Log Masuk Pentadbir - Rubber Clone AI',
                    'error' => 'E-mel atau kata laluan salah, atau anda bukan pentadbir.'
                ]);
                exit;
            }
        }
        
        header("Location: /admin/dashboard");
        exit;
    }

    // Mengendalikan log keluar web pentadbir (GET)
    public function logoutWeb() {
        unset($_SESSION['admin_id']);
        unset($_SESSION['admin_username']);
        unset($_SESSION['admin_fullname']);
        unset($_SESSION['admin_role']);
        session_destroy();
        header("Location: /admin/dashboard");
        exit;
    }

    // --- PANDANGAN PORTAL (WEB VIEWS) ---

    // Paparan Halaman Dashboard (GET /admin/dashboard)
    public function dashboard() {
        // Jika belum log masuk, checkWebAuth() akan memaparkan borang login
        if (empty($_SESSION['admin_id'])) {
            // Cuba auto-create akaun admin default jika jadual kosong untuk memudahkan pengujian pertama
            $this->createDefaultAdminIfNoUsers();
        }
        $this->checkWebAuth();
        
        $this->view('admin/dashboard', [
            'title' => 'Papan Pemuka - Rubber Clone AI',
            'active_tab' => 'dashboard'
        ]);
    }

    // Paparan Halaman Pengurusan Pengguna (GET /admin/users)
    public function usersView() {
        $this->checkWebAuth();
        $this->view('admin/users', [
            'title' => 'Direktori Pengguna - Rubber Clone AI',
            'active_tab' => 'users'
        ]);
    }

    // Paparan Halaman Sejarah Imbasan (GET /admin/history)
    public function historyView() {
        $this->checkWebAuth();
        $this->view('admin/history', [
            'title' => 'Audit Sejarah Imbasan - Rubber Clone AI',
            'active_tab' => 'history'
        ]);
    }

    // --- API PENTADBIRAN (ADMIN API ENDPOINTS) ---

    // Dapatkan data senarai pengguna (GET /api/admin/users)
    public function getUsers() {
        $this->checkApiAuth();
        $users = $this->userModel->getAllUsers();
        if ($users === false) {
            $this->jsonResponse(["status" => "error", "message" => "Gagal mendapatkan senarai pengguna."], 500);
        }

        $formatted = [];
        foreach ($users as $row) {
            $formatted[] = [
                "id" => (int)$row['id'],
                "email" => $row['email'],
                "username" => $row['username'],
                "fullname" => $row['fullname'],
                "agency" => $row['agency'],
                "status" => $row['status'],
                "role" => $row['role'],
                "total_scans" => (int)$row['total_scans'],
                "registration_date" => (int)$row['registration_date']
            ];
        }
        $this->jsonResponse(["status" => "success", "data" => $formatted]);
    }

    // Mengubah status akaun pengguna (POST /api/admin/toggle_user)
    public function toggleUserStatus() {
        $this->checkApiAuth();
        
        // Ambil dari $_POST atau JSON
        $userId = $_POST['user_id'] ?? null;
        $status = $_POST['status'] ?? null;

        if (!$userId || !$status) {
            $input = json_decode(file_get_contents('php://input'), true);
            if ($input) {
                $userId = $input['user_id'] ?? null;
                $status = $input['status'] ?? null;
            }
        }

        if (!$userId || !in_array($status, ['active', 'inactive'])) {
            $this->jsonResponse(["status" => "error", "message" => "Parameter user_id dan status (active/inactive) wajib disediakan."], 400);
        }

        $result = $this->userModel->updateStatus($userId, $status);
        if ($result) {
            $this->jsonResponse(["status" => "success", "message" => "Status akaun pengguna telah dikemas kini kepada {$status}."]);
        } else {
            $this->jsonResponse(["status" => "error", "message" => "Gagal mengemas kini status pengguna."], 500);
        }
    }

    // Dapatkan statistik dan maklumat geografi (GET /api/admin/stats)
    public function getStats() {
        $this->checkApiAuth();
        
        $summary = $this->analysisModel->getStatsSummary();
        $scans_by_clone = $this->analysisModel->getScansByClone();
        $scans_by_agency = $this->analysisModel->getScansByAgency();
        $all_records = $this->analysisModel->getAllRecords();

        // Format data geografi
        $scans_geographic = [];
        foreach ($all_records as $rec) {
            $scans_geographic[] = [
                "latitude" => (float)$rec['latitude'],
                "longitude" => (float)$rec['longitude'],
                "clone_name" => $rec['clone_name'],
                "confidence" => (float)$rec['confidence'],
                "location_name" => $rec['location_name'],
                "user" => $rec['fullname'] ?? $rec['username'],
                "timestamp" => (int)$rec['timestamp']
            ];
        }

        $this->jsonResponse([
            "status" => "success",
            "data" => [
                "summary" => $summary,
                "scans_by_clone" => $scans_by_clone,
                "scans_by_agency" => $scans_by_agency,
                "scans_geographic" => $scans_geographic
            ]
        ]);
    }

    // Pembantu untuk mencipta pentadbir awal secara automatik bagi pengujian tempatan
    private function createDefaultAdminIfNoUsers() {
        try {
            $database = new Database();
            $db = $database->getConnection();
            $stmt = $db->query("SELECT COUNT(*) as total FROM users");
            $total = $stmt->fetch()['total'];

            if ($total == 0) {
                // Tiada pengguna dalam DB, cipta pentadbir lalai: admin@demo.com / admin123
                $adminData = [
                    'email' => 'admin@demo.com',
                    'username' => 'admin_demo',
                    'password' => 'admin123',
                    'fullname' => 'Pentadbir Utama RISDA',
                    'agency' => 'RISDA Ibu Pejabat',
                    'role' => 'admin',
                    'status' => 'active'
                ];
                $this->userModel->create($adminData);
                
                // Tambah sedikit mock data imbasan daun getah untuk paparan peta & carta yang menarik
                $userId = 1; // Admin user id
                $mockScans = [
                    [
                        'user_id' => $userId, 'clone_name' => 'RRIM 3001', 'confidence' => 0.96, 'timestamp' => time() * 1000,
                        'latitude' => 4.5921, 'longitude' => 101.0901, 'location_name' => 'Tapak Semaian RISDA Ipoh, Perak',
                        'notes' => 'Keadaan daun sihat, urat sekata.', 'soil_type' => 'Tanah Liat Berpasir', 'rainfall' => '2,200 mm', 'elevation' => '120 meter'
                    ],
                    [
                        'user_id' => $userId, 'clone_name' => 'RRIM 600', 'confidence' => 0.89, 'timestamp' => (time() - 3600 * 2) * 1000,
                        'latitude' => 3.1390, 'longitude' => 101.6869, 'location_name' => 'Stesen Pertanian RISDA Cheras, Selangor',
                        'notes' => 'Lobus daun simetri, keyakinan tinggi.', 'soil_type' => 'Tanah Merah Laterit', 'rainfall' => '2,500 mm', 'elevation' => '85 meter'
                    ],
                    [
                        'user_id' => $userId, 'clone_name' => 'PB 260', 'confidence' => 0.94, 'timestamp' => (time() - 3600 * 24) * 1000,
                        'latitude' => 1.4854, 'longitude' => 103.7618, 'location_name' => 'Tapak Semai RISDA Kluang, Johor',
                        'notes' => 'Struktur daun matang.', 'soil_type' => 'Tanah Liat Aluvium', 'rainfall' => '2,100 mm', 'elevation' => '110 meter'
                    ]
                ];
                foreach ($mockScans as $mScan) {
                    $this->analysisModel->create($mScan);
                }
            }
        } catch (PDOException $e) {
            // Abaikan ralat ini, ia akan ditangkap di core/Database
        }
    }
}
