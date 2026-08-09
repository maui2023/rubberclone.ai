<?php
// app/controllers/CloneSampleController.php

class CloneSampleController extends Controller {
    private $cloneModel;

    public function __construct() {
        $this->cloneModel = $this->model('CloneSample');
    }

    // Melakukan pengesahan token JWT atau Session Admin dan memulangkan payload jika sah
    private function checkAuth() {
        // 1. Cuba semak Session dahulu (untuk request AJAX dari portal)
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['admin_id']) && $_SESSION['admin_role'] === 'admin') {
            return [
                'id' => $_SESSION['admin_id'],
                'username' => $_SESSION['admin_username'] ?? 'admin',
                'fullname' => $_SESSION['admin_fullname'] ?? 'Pentadbir',
                'role' => 'admin'
            ];
        }

        // 2. Jika tiada Session, semak JWT Bearer Token (untuk Postman/Flutter app)
        $token = JWT::getBearerToken();
        if (!$token) {
            $this->jsonResponse(["status" => "error", "message" => "Token pengesahan tidak disediakan."], 401);
        }

        $payload = JWT::verify($token);
        if (!$payload) {
            $this->jsonResponse(["status" => "error", "message" => "Token tidak sah atau telah luput tempoh."], 401);
        }

        return $payload;
    }

    // Memastikan pengguna adalah admin
    private function checkAdminAuth() {
        $user = $this->checkAuth();

        if ($user['role'] !== 'admin') {
            $this->jsonResponse(["status" => "error", "message" => "Akses ditolak. Kebenaran pentadbir diperlukan."], 403);
        }

        return $user;
    }

    // Senarai semua sampel klon aktif (GET /api/clone-samples/list)
    public function list() {
        $this->checkAuth();

        $records = $this->cloneModel->getAllActive();

        if ($records === false) {
            $this->jsonResponse(["status" => "error", "message" => "Gagal mendapatkan data sampel klon."], 500);
        }

        // Susun semula data mengikut jenis data yang sesuai (Type Casting)
        $formattedRecords = [];
        foreach ($records as $row) {
            $formattedRecords[] = [
                "id" => (int)$row['id'],
                "clone_name" => $row['clone_name'],
                "warisan" => $row['warisan'],
                "potensi_hasil" => $row['potensi_hasil'],
                "anggaran_kayu" => $row['anggaran_kayu'],
                "bentuk_daun" => $row['bentuk_daun'],
                "bentuk_hujung_daun" => $row['bentuk_hujung_daun'],
                "bentuk_pangkal_daun" => $row['bentuk_pangkal_daun'],
                "kedudukan_lai_daun" => $row['kedudukan_lai_daun'],
                "bentuk_tepi_daun" => $row['bentuk_tepi_daun'],
                "warna_daun_kilauan" => $row['warna_daun_kilauan'],
                "permukaan_daun" => $row['permukaan_daun'],
                "pandangan_memanjang" => $row['pandangan_memanjang'],
                "pandangan_melintang" => $row['pandangan_melintang'],
                "saiz_gagang_daun" => $row['saiz_gagang_daun'],
                "saiz_anak_gagang" => $row['saiz_anak_gagang'],
                "warna_lateks" => $row['warna_lateks'],
                "status" => $row['status']
            ];
        }

        $this->jsonResponse([
            "status" => "success",
            "data" => $formattedRecords
        ], 200);
    }

    // Tambah sampel klon baharu (POST /api/clone-samples/create)
    public function create() {
        $this->checkAdminAuth();

        // Ambil data dari POST atau JSON input
        $input = $_POST;
        if (empty($input['clone_name'])) {
            $input = json_decode(file_get_contents('php://input'), true);
        }

        if (!$input) {
            $this->jsonResponse(["status" => "error", "message" => "Format data input tidak sah."], 400);
        }

        // Semak medan wajib
        if (empty($input['clone_name'])) {
            $this->jsonResponse(["status" => "error", "message" => "Nama klon (clone_name) adalah wajib."], 400);
        }

        $data = [
            'clone_name' => trim($input['clone_name']),
            'warisan' => trim($input['warisan'] ?? ''),
            'potensi_hasil' => trim($input['potensi_hasil'] ?? ''),
            'anggaran_kayu' => trim($input['anggaran_kayu'] ?? ''),
            'bentuk_daun' => trim($input['bentuk_daun'] ?? ''),
            'bentuk_hujung_daun' => trim($input['bentuk_hujung_daun'] ?? ''),
            'bentuk_pangkal_daun' => trim($input['bentuk_pangkal_daun'] ?? ''),
            'kedudukan_lai_daun' => trim($input['kedudukan_lai_daun'] ?? ''),
            'bentuk_tepi_daun' => trim($input['bentuk_tepi_daun'] ?? ''),
            'warna_daun_kilauan' => trim($input['warna_daun_kilauan'] ?? ''),
            'permukaan_daun' => trim($input['permukaan_daun'] ?? ''),
            'pandangan_memanjang' => trim($input['pandangan_memanjang'] ?? ''),
            'pandangan_melintang' => trim($input['pandangan_melintang'] ?? ''),
            'saiz_gagang_daun' => trim($input['saiz_gagang_daun'] ?? ''),
            'saiz_anak_gagang' => trim($input['saiz_anak_gagang'] ?? ''),
            'warna_lateks' => trim($input['warna_lateks'] ?? '')
        ];

        $recordId = $this->cloneModel->create($data);

        if ($recordId) {
            $this->jsonResponse([
                "status" => "success",
                "message" => "Sampel klon berjaya ditambah.",
                "data" => [
                    "id" => (int)$recordId,
                    "clone_name" => $data['clone_name']
                ]
            ], 201);
        } else {
            $this->jsonResponse(["status" => "error", "message" => "Gagal menyimpan sampel klon ke pangkalan data."], 500);
        }
    }

    // Kemas kini sampel klon sedia ada (POST /api/clone-samples/update)
    public function update() {
        $this->checkAdminAuth();

        // Ambil data dari POST atau JSON input
        $input = $_POST;
        if (empty($input['id'])) {
            $input = json_decode(file_get_contents('php://input'), true);
        }

        if (!$input) {
            $this->jsonResponse(["status" => "error", "message" => "Format data input tidak sah."], 400);
        }

        // Semak medan wajib
        $id = $input['id'] ?? null;
        if (!$id) {
            $this->jsonResponse(["status" => "error", "message" => "ID sampel klon wajib dibekalkan."], 400);
        }

        if (empty($input['clone_name'])) {
            $this->jsonResponse(["status" => "error", "message" => "Nama klon (clone_name) adalah wajib."], 400);
        }

        // Semak kewujudan rekod
        $existing = $this->cloneModel->getById($id);
        if (!$existing) {
            $this->jsonResponse(["status" => "error", "message" => "Sampel klon dengan ID tersebut tidak ditemui."], 404);
        }

        $data = [
            'clone_name' => trim($input['clone_name']),
            'warisan' => trim($input['warisan'] ?? ''),
            'potensi_hasil' => trim($input['potensi_hasil'] ?? ''),
            'anggaran_kayu' => trim($input['anggaran_kayu'] ?? ''),
            'bentuk_daun' => trim($input['bentuk_daun'] ?? ''),
            'bentuk_hujung_daun' => trim($input['bentuk_hujung_daun'] ?? ''),
            'bentuk_pangkal_daun' => trim($input['bentuk_pangkal_daun'] ?? ''),
            'kedudukan_lai_daun' => trim($input['kedudukan_lai_daun'] ?? ''),
            'bentuk_tepi_daun' => trim($input['bentuk_tepi_daun'] ?? ''),
            'warna_daun_kilauan' => trim($input['warna_daun_kilauan'] ?? ''),
            'permukaan_daun' => trim($input['permukaan_daun'] ?? ''),
            'pandangan_memanjang' => trim($input['pandangan_memanjang'] ?? ''),
            'pandangan_melintang' => trim($input['pandangan_melintang'] ?? ''),
            'saiz_gagang_daun' => trim($input['saiz_gagang_daun'] ?? ''),
            'saiz_anak_gagang' => trim($input['saiz_anak_gagang'] ?? ''),
            'warna_lateks' => trim($input['warna_lateks'] ?? '')
        ];

        $result = $this->cloneModel->update($id, $data);

        if ($result) {
            $this->jsonResponse([
                "status" => "success",
                "message" => "Sampel klon berjaya dikemas kini.",
                "data" => [
                    "id" => (int)$id,
                    "clone_name" => $data['clone_name']
                ]
            ], 200);
        } else {
            $this->jsonResponse(["status" => "error", "message" => "Gagal mengemas kini sampel klon."], 500);
        }
    }

    // Padam sampel klon (DELETE /api/clone-samples/delete?id={id})
    public function delete() {
        $this->checkAdminAuth();

        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->jsonResponse(["status" => "error", "message" => "ID sampel klon wajib dibekalkan."], 400);
        }

        // Semak kewujudan rekod
        $existing = $this->cloneModel->getById($id);
        if (!$existing) {
            $this->jsonResponse(["status" => "error", "message" => "Sampel klon dengan ID tersebut tidak ditemui."], 404);
        }

        $result = $this->cloneModel->delete($id);

        if ($result) {
            $this->jsonResponse(["status" => "success", "message" => "Sampel klon berjaya dipadamkan."], 200);
        } else {
            $this->jsonResponse(["status" => "error", "message" => "Gagal memadam sampel klon."], 500);
        }
    }
}
