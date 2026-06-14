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

    // Pembantu untuk mengesan jika pelawat menggunakan peranti mudah alih (mobile)
    private function isMobileDevice() {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        return (bool)preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $userAgent);
    }

    // Memastikan pengguna adalah admin sebelum memaparkan halaman web (Menghalang Mobile)
    private function checkWebAuth() {
        if ($this->isMobileDevice()) {
            $this->view('admin/mobile_block', ['title' => 'Akses Dihalang - Rubber Clone AI']);
            exit;
        }
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
        if ($this->isMobileDevice()) {
            $this->view('admin/mobile_block', ['title' => 'Akses Dihalang - Rubber Clone AI']);
            exit;
        }
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

                $this->redirect('pentadbir');
            } else {
                $this->view('admin/login', [
                    'title' => 'Log Masuk Pentadbir - Rubber Clone AI',
                    'error' => 'E-mel atau kata laluan salah, atau anda bukan pentadbir.'
                ]);
                exit;
            }
        }
        
        $this->redirect('pentadbir');
    }

    // Mengendalikan log keluar web pentadbir (GET)
    public function logoutWeb() {
        unset($_SESSION['admin_id']);
        unset($_SESSION['admin_username']);
        unset($_SESSION['admin_fullname']);
        unset($_SESSION['admin_role']);
        session_destroy();
        $this->redirect('pentadbir');
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
                // Cipta pentadbir lalai menggunakan ketetapan daripada fail konfigurasi
                if (defined('ADMIN_EMAIL') && defined('ADMIN_PASS') && defined('ADMIN_USERNAME')) {
                    $adminData = [
                        'email' => ADMIN_EMAIL,
                        'username' => ADMIN_USERNAME,
                        'password' => ADMIN_PASS,
                        'fullname' => 'Pentadbir Utama RISDA',
                        'agency' => 'RISDA Ibu Pejabat',
                        'role' => 'admin',
                        'status' => 'active'
                    ];
                    $this->userModel->create($adminData);
                }
                
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
        } catch (Exception $e) {
            // Abaikan jika pangkalan data atau jadual belum wujud
        }
    }

    // Paparan Halaman Urus CMS & Blog (GET /admin/cms)
    public function cmsView() {
        $this->checkWebAuth();
        
        $cmsModel = $this->model('CmsSetting');
        $blogModel = $this->model('BlogPost');
        
        $settings = $cmsModel->getAll();
        $blogs = $blogModel->getAll();

        $this->view('admin/cms', [
            'title' => 'Urus Portal & CMS - Rubber Clone AI',
            'active_tab' => 'cms',
            'settings' => $settings,
            'blogs' => $blogs
        ]);
    }

    // Kemas kini konfigurasi CMS (POST /api/admin/update_cms)
    public function updateCms() {
        $this->checkApiAuth();

        $cmsModel = $this->model('CmsSetting');
        
        $hero_title = $_POST['hero_title'] ?? null;
        $hero_desc = $_POST['hero_desc'] ?? null;
        $stat_scans = $_POST['stat_scans'] ?? null;
        $stat_clones = $_POST['stat_clones'] ?? null;
        $stat_officers = $_POST['stat_officers'] ?? null;

        if (!$hero_title) {
            $input = json_decode(file_get_contents('php://input'), true);
            if ($input) {
                $hero_title = $input['hero_title'] ?? null;
                $hero_desc = $input['hero_desc'] ?? null;
                $stat_scans = $input['stat_scans'] ?? null;
                $stat_clones = $input['stat_clones'] ?? null;
                $stat_officers = $input['stat_officers'] ?? null;
            }
        }

        if (!$hero_title || !$hero_desc || !$stat_scans || !$stat_clones || !$stat_officers) {
            $this->jsonResponse(["status" => "error", "message" => "Semua medan CMS wajib diisi."], 400);
        }

        $cmsModel->updateKey('hero_title', $hero_title);
        $cmsModel->updateKey('hero_desc', $hero_desc);
        $cmsModel->updateKey('stat_scans', $stat_scans);
        $cmsModel->updateKey('stat_clones', $stat_clones);
        $cmsModel->updateKey('stat_officers', $stat_officers);

        $this->jsonResponse(["status" => "success", "message" => "Kandungan landing page berjaya dikemas kini."]);
    }

    // Membina Blog Story baharu (POST /api/admin/blog/create)
    public function createBlogPost() {
        $this->checkApiAuth();

        $blogModel = $this->model('BlogPost');

        $title = $_POST['title'] ?? null;
        $content = $_POST['content'] ?? null;
        $author = $_POST['author'] ?? 'RISDA Pentadbir';

        if (!$title || !$content) {
            $this->jsonResponse(["status" => "error", "message" => "Tajuk dan kandungan blog wajib diisi."], 400);
        }

        $data = [
            'title' => $title,
            'content' => $content,
            'author' => $author,
            'image_url' => null
        ];

        // Kendalikan muat naik fail imej jika disediakan
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['image']['tmp_name'];
            $fileName = $_FILES['image']['name'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
            if (in_array($fileExtension, $allowedExtensions)) {
                $uploadDir = __DIR__ . '/../../public/uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $newFileName = 'blog_' . time() . '.' . $fileExtension;
                $destPath = $uploadDir . $newFileName;

                if (move_uploaded_file($fileTmpPath, $destPath)) {
                    $data['image_url'] = "uploads/" . $newFileName;
                }
            }
        }

        $resultId = $blogModel->create($data);
        if ($resultId) {
            $this->jsonResponse([
                "status" => "success", 
                "message" => "Kisah kejayaan berjaya diterbitkan.", 
                "id" => (int)$resultId,
                "image_url" => $data['image_url']
            ]);
        } else {
            $this->jsonResponse(["status" => "error", "message" => "Gagal menerbitkan kisah blog."], 500);
        }
    }

    // Memadam Blog Story (DELETE /api/admin/blog/delete?id={id})
    public function deleteBlogPost() {
        $this->checkApiAuth();

        $blogModel = $this->model('BlogPost');

        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->jsonResponse(["status" => "error", "message" => "ID kisah blog wajib dibekalkan."], 400);
        }

        $result = $blogModel->delete($id);
        if ($result) {
            $this->jsonResponse(["status" => "success", "message" => "Kisah blog berjaya dipadamkan."]);
        } else {
            $this->jsonResponse(["status" => "error", "message" => "Gagal memadam kisah blog."], 500);
        }
    }

    // Cipta Pengguna Baharu oleh Admin (POST /api/admin/create_user)
    public function createUser() {
        // Hanya admin boleh akses
        $this->checkApiAuth();

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

        // 2. Semak Had Panjang & Kekuatan Kata Laluan
        $password = $input['password'];
        if (strlen($password) < 8) {
            $this->jsonResponse(["status" => "error", "message" => "Kata laluan mestilah sekurang-kurangnya 8 aksara."], 400);
        }

        // 3. Sanitasi Input Teks untuk Mencegah Serangan XSS (OWASP A03:2021)
        $username = trim(htmlspecialchars(strip_tags($input['username']), ENT_QUOTES, 'UTF-8'));
        $fullname = trim(htmlspecialchars(strip_tags($input['fullname']), ENT_QUOTES, 'UTF-8'));
        $agency = isset($input['agency']) ? trim(htmlspecialchars(strip_tags($input['agency']), ENT_QUOTES, 'UTF-8')) : 'RISDA Pekebun Kecil';
        $role = isset($input['role']) ? trim(htmlspecialchars(strip_tags($input['role']), ENT_QUOTES, 'UTF-8')) : 'user';
        $status = isset($input['status']) ? trim(htmlspecialchars(strip_tags($input['status']), ENT_QUOTES, 'UTF-8')) : 'active';

        // Validasi aksara Username (hanya alfanumerik dan underscore dibenarkan)
        if (!preg_match('/^[a-zA-Z0-9_]{3,30}$/', $username)) {
            $this->jsonResponse(["status" => "error", "message" => "Nama pengguna (username) hanya boleh mengandungi huruf, nombor, garis bawah (_) dan panjang antara 3 hingga 30 aksara."], 400);
        }

        // Validasi peranan (role)
        if ($role !== 'admin' && $role !== 'user') {
            $this->jsonResponse(["status" => "error", "message" => "Peranan pengguna tidak sah."], 400);
        }

        // Validasi status
        if ($status !== 'active' && $status !== 'inactive') {
            $this->jsonResponse(["status" => "error", "message" => "Status pengguna tidak sah."], 400);
        }

        // Semak kewujudan e-mel pendua
        if ($this->userModel->getByEmail($email)) {
            $this->jsonResponse(["status" => "error", "message" => "Alamat e-mel ini telah berdaftar dalam sistem."], 400);
        }

        // Sediakan data tersanitasi
        $userData = [
            'email' => $email,
            'username' => $username,
            'password' => $password,
            'fullname' => $fullname,
            'agency' => $agency,
            'role' => $role,
            'status' => $status
        ];

        // Proses pendaftaran
        $userId = $this->userModel->create($userData);

        if ($userId) {
            $this->jsonResponse(["status" => "success", "message" => "Pengguna baharu berjaya didaftarkan."]);
        } else {
            $this->jsonResponse(["status" => "error", "message" => "Ralat pelayan semasa mendaftar akaun."], 500);
        }
    }
}
