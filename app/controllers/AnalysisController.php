<?php
// app/controllers/AnalysisController.php

class AnalysisController extends Controller {
    private $analysisModel;

    public function __construct() {
        $this->analysisModel = $this->model('AnalysisRecord');
    }

    // Melakukan pengesahan token JWT dan memulangkan payload jika sah
    private function checkAuth() {
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

    // Simpan rekod imbasan baharu (POST /api/analysis/upload)
    public function upload() {
        $user = $this->checkAuth();

        // Menyokong pemprosesan multipart/form-data dan raw JSON
        $clone_name = $_POST['clone_name'] ?? null;
        $confidence = $_POST['confidence'] ?? null;
        $timestamp = $_POST['timestamp'] ?? null;
        $latitude = $_POST['latitude'] ?? null;
        $longitude = $_POST['longitude'] ?? null;
        $location_name = $_POST['location_name'] ?? 'Stesen RISDA, Malaysia';
        $notes = $_POST['notes'] ?? '';
        $soil_type = $_POST['soil_type'] ?? 'Tiada Maklumat';
        $rainfall = $_POST['rainfall'] ?? 'Tiada Maklumat';
        $elevation = $_POST['elevation'] ?? 'Tiada Maklumat';

        // Jika data tiada dalam $_POST, semak pemprosesan JSON input
        if (!$clone_name) {
            $input = json_decode(file_get_contents('php://input'), true);
            if ($input) {
                $clone_name = $input['clone_name'] ?? null;
                $confidence = $input['confidence'] ?? null;
                $timestamp = $input['timestamp'] ?? null;
                $latitude = $input['latitude'] ?? null;
                $longitude = $input['longitude'] ?? null;
                $location_name = $input['location_name'] ?? 'Stesen RISDA, Malaysia';
                $notes = $input['notes'] ?? '';
                $soil_type = $input['soil_type'] ?? 'Tiada Maklumat';
                $rainfall = $input['rainfall'] ?? 'Tiada Maklumat';
                $elevation = $input['elevation'] ?? 'Tiada Maklumat';
            }
        }

        // Semak kelayakan medan wajib
        if (!$clone_name || !$confidence || !$timestamp || !$latitude || !$longitude) {
            $this->jsonResponse(["status" => "error", "message" => "Sila isikan maklumat wajib: clone_name, confidence, timestamp, latitude, longitude."], 400);
        }

        // Sediakan data untuk pangkalan data
        $data = [
            'user_id' => $user['id'],
            'clone_name' => $clone_name,
            'confidence' => (float)$confidence,
            'timestamp' => (int)$timestamp,
            'latitude' => (float)$latitude,
            'longitude' => (float)$longitude,
            'location_name' => $location_name,
            'notes' => $notes,
            'soil_type' => $soil_type,
            'rainfall' => $rainfall,
            'elevation' => $elevation,
            'image_url' => null
        ];

        // Mulakan penyimpanan rekod (untuk mendapatkan ID rekod imbasan bagi fail imej)
        $recordId = $this->analysisModel->create($data);

        if (!$recordId) {
            $this->jsonResponse(["status" => "error", "message" => "Gagal menyimpan rekod analisis ke pangkalan data."], 500);
        }

        // Kendalikan muat naik fail imej jika ada
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['image']['tmp_path'] ?? $_FILES['image']['tmp_name'];
            $fileName = $_FILES['image']['name'];
            $fileSize = $_FILES['image']['size'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
            $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp'];

            // 1. Validasi Kaedah Muat Naik POST
            if (!is_uploaded_file($fileTmpPath)) {
                $this->analysisModel->delete($recordId, $user['id'], true);
                $this->jsonResponse(["status" => "error", "message" => "Ralat pemprosesan fail muat naik."], 400);
            }

            // 2. Validasi Saiz Fail (Had Maksimum 5MB)
            if ($fileSize > 5 * 1024 * 1024) {
                $this->analysisModel->delete($recordId, $user['id'], true);
                $this->jsonResponse(["status" => "error", "message" => "Saiz fail melebihi had maksimum yang dibenarkan (5MB)."], 400);
            }

            // 3. Validasi Jenis MIME Sebenar (OWASP File Upload Security)
            $mimeType = mime_content_type($fileTmpPath);
            if (!in_array($mimeType, $allowedMimeTypes) || !in_array($fileExtension, $allowedExtensions)) {
                $this->analysisModel->delete($recordId, $user['id'], true);
                $this->jsonResponse(["status" => "error", "message" => "Format fail tidak sah. Hanya imej JPG, PNG, dan WebP sahaja dibenarkan."], 400);
            }

            // Folder penyimpanan imej
            $uploadDir = __DIR__ . '/../../public/uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $newFileName = 'scan_' . $recordId . '.' . $fileExtension;
            $destPath = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                // Bina URL penuh imej
                $host = $_SERVER['HTTP_HOST'];
                $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
                $imageUrl = $protocol . "://" . $host . "/uploads/" . $newFileName;

                // Kemas kini URL imej dalam DB
                $database = new Database();
                $db = $database->getConnection();
                $stmt = $db->prepare("UPDATE analysis_records SET image_url = :image_url WHERE id = :id");
                $stmt->bindParam(':image_url', $imageUrl);
                $stmt->bindParam(':id', $recordId);
                $stmt->execute();

                $data['image_url'] = $imageUrl;
            } else {
                $this->analysisModel->delete($recordId, $user['id'], true);
                $this->jsonResponse(["status" => "error", "message" => "Gagal menyimpan fail muat naik di pelayan."], 500);
            }
        }

        $this->jsonResponse([
            "status" => "success",
            "message" => "Rekod analisis berjaya disimpan.",
            "data" => [
                "id" => (int)$recordId,
                "clone_name" => $clone_name,
                "image_url" => $data['image_url']
            ]
        ], 201);
    }

    // Mengambil sejarah imbasan (GET /api/analysis/list)
    public function list() {
        $user = $this->checkAuth();

        // Logik: Jika admin, pulangkan SEMUA. Jika pengguna biasa, pulangkan rekod miliknya sahaja.
        if ($user['role'] === 'admin') {
            $records = $this->analysisModel->getAllRecords();
        } else {
            $records = $this->analysisModel->getByUserId($user['id']);
        }

        if ($records === false) {
            $this->jsonResponse(["status" => "error", "message" => "Gagal mendapatkan data rekod analisis."], 500);
        }

        // Susun semula data mengikut jenis jenis data yang sesuai (Type Casting)
        $formattedRecords = [];
        foreach ($records as $row) {
            $formattedRecords[] = [
                "id" => (int)$row['id'],
                "username" => $row['username'] ?? $user['username'],
                "fullname" => $row['fullname'] ?? null,
                "clone_name" => $row['clone_name'],
                "confidence" => (float)$row['confidence'],
                "timestamp" => (int)$row['timestamp'],
                "latitude" => (float)$row['latitude'],
                "longitude" => (float)$row['longitude'],
                "location_name" => $row['location_name'],
                "notes" => $row['notes'],
                "soil_type" => $row['soil_type'],
                "rainfall" => $row['rainfall'],
                "elevation" => $row['elevation'],
                "image_url" => $row['image_url']
            ];
        }

        $this->jsonResponse([
            "status" => "success",
            "data" => $formattedRecords
        ], 200);
    }

    // Padam rekod imbasan individu (DELETE /api/analysis/delete?id={id})
    public function delete() {
        $user = $this->checkAuth();

        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->jsonResponse(["status" => "error", "message" => "ID rekod imbasan tidak dibekalkan."], 400);
        }

        $isAdmin = ($user['role'] === 'admin');
        
        // Padam rekod
        $result = $this->analysisModel->delete($id, $user['id'], $isAdmin);

        if ($result) {
            $this->jsonResponse(["status" => "success", "message" => "Rekod berjaya dipadamkan."], 200);
        } else {
            $this->jsonResponse(["status" => "error", "message" => "Gagal memadam rekod atau anda tiada hak akses."], 403);
        }
    }

    // Padam semua rekod milik pengguna semasa (POST /api/analysis/clear)
    public function clear() {
        $user = $this->checkAuth();

        $result = $this->analysisModel->clearByUserId($user['id']);

        if ($result) {
            $this->jsonResponse(["status" => "success", "message" => "Semua sejarah imbasan anda telah dibersihkan."], 200);
        } else {
            $this->jsonResponse(["status" => "error", "message" => "Gagal mengosongkan sejarah imbasan."], 500);
        }
    }
}
