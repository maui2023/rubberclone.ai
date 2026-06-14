# Dokumentasi API Pengecaman Klon Getah RISDA (Rubber Clone AI)

Dokumentasi ini menerangkan cara-cara menggunakan dan menyambung API Backend **Rubber Clone AI** kepada aplikasi mudah alih (Mobile App). 

---

## 1. Maklumat Asas & Kawalan Keselamatan (Security Principles)

### URL Asas (Base URL)
* Pengujian Tempatan: `http://localhost/` atau `http://localhost/index.php?url=`
* Server Pengeluaran (Production): `https://rubberclone-ai.pats.my/` atau `https://rubberclone-ai.pats.my/index.php?url=`

### Pengesahan Pengguna (Authentication)
* Kebanyakan endpoint memerlukan pengepala (header) pengesahan menggunakan **JWT Bearer Token**.
* Token diperoleh setelah log masuk berjaya melalui endpoint `/api/auth/login`.
* Format Header:
  ```http
  Authorization: Bearer <token_jwt_anda>
  ```

### Standard Keselamatan (OWASP & PSR Compliant)
1. **Pencegahan Suntikan (Injection Prevention)**: Menggunakan PDO *Prepared Statements* untuk semua kueri pangkalan data.
2. **Sanitasi Input**: Semua input teks wajib ditapis bagi menghalang serangan XSS (Cross-Site Scripting).
3. **Keselamatan Fail**: Muat naik fail imej dihadkan kepada **JPEG, PNG, dan WebP** serta saiz maksimum **5MB**. Jenis MIME fail disahkan pada pelayan menggunakan `mime_content_type` untuk menghalang kemasukan skrip berbahaya.

---

## 2. Senarai Endpoints API

### A. Pendaftaran Pengguna (Register)
Pendaftaran akaun pegawai lapangan atau pekebun kecil RISDA baharu.

* **Laluan (Route)**: `api/auth/register` (atau `index.php?url=api/auth/register`)
* **Kaedah HTTP**: `POST`
* **Pengepala (Headers)**:
  * `Content-Type: application/json`
* **Format Body (JSON)**:
  ```json
  {
    "email": "pegawai_tapak@risda.gov.my",
    "username": "pegawai_tapak123",
    "password": "PasswordKuat2026!",
    "fullname": "Ahmad Subri bin Hashim",
    "agency": "RISDA Perak Tengah"
  }
  ```
  *(Kata laluan wajib sekurang-kurangnya 8 aksara. Username hanya boleh mengandungi huruf, nombor, dan garis bawah).*

* **Respons Berjaya (201 Created)**:
  ```json
  {
    "status": "success",
    "message": "Pengguna berjaya didaftarkan."
  }
  ```
* **Respons Ralat (400 Bad Request)**:
  ```json
  {
    "status": "error",
    "message": "Kata laluan mestilah sekurang-kurangnya 8 aksara."
  }
  ```

---

### B. Log Masuk & Dapatkan Token (Login)
Mengesahkan kredensial pengguna dan mengembalikan token JWT untuk akses API lain.

* **Laluan (Route)**: `api/auth/login` (atau `index.php?url=api/auth/login`)
* **Kaedah HTTP**: `POST`
* **Pengepala (Headers)**:
  * `Content-Type: application/json`
* **Format Body (JSON)**:
  ```json
  {
    "email": "pegawai_tapak@risda.gov.my",
    "password": "PasswordKuat2026!"
  }
  ```
* **Respons Berjaya (200 OK)**:
  ```json
  {
    "status": "success",
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
    "user": {
      "id": 5,
      "email": "pegawai_tapak@risda.gov.my",
      "username": "pegawai_tapak123",
      "fullname": "Ahmad Subri bin Hashim",
      "agency": "RISDA Perak Tengah",
      "role": "user",
      "status": "active"
    }
  }
  ```
* **Respons Ralat (401 Unauthorized)**:
  ```json
  {
    "status": "error",
    "message": "Alamat e-mel atau kata laluan adalah salah."
  }
  ```

---

### C. Muat Naik Hasil Imbasan Daun (Upload Scan)
Menghantar dan menyimpan hasil pengesanan daun pokok getah berserta koordinat GPS dan fail imej daun.

* **Laluan (Route)**: `api/analysis/upload` (atau `index.php?url=api/analysis/upload`)
* **Kaedah HTTP**: `POST`
* **Pengepala (Headers)**:
  * `Authorization: Bearer <token_jwt_anda>`
  * `Content-Type: multipart/form-data`
* **Format Body (Form-Data / Multipart)**:
  * `clone_name` (Teks, Wajib): Nama klon getah dikesan (cth: `RRIM 3001`)
  * `confidence` (Perpuluhan, Wajib): Kadar keyakinan AI (cth: `0.95` untuk 95%)
  * `timestamp` (Nombor, Wajib): Epoch timestamp dalam milisaat (cth: `1781425778157`)
  * `latitude` (Perpuluhan, Wajib): Koordinat latitud (cth: `4.5921`)
  * `longitude` (Perpuluhan, Wajib): Koordinat longitud (cth: `101.0901`)
  * `location_name` (Teks, Pilihan): Nama lokasi imbasan
  * `notes` (Teks, Pilihan): Nota ulasan tambahan
  * `soil_type` (Teks, Pilihan): Jenis tanah
  * `rainfall` (Teks, Pilihan): Purata taburan hujan
  * `elevation` (Teks, Pilihan): Ketinggian elevasi lokasi
  * `image` (Fail Fail, Pilihan): Gambar daun getah (Format JPG/PNG/WebP, Had 5MB)

* **Respons Berjaya (201 Created)**:
  ```json
  {
    "status": "success",
    "message": "Rekod analisis berjaya disimpan.",
    "data": {
      "id": 14,
      "clone_name": "RRIM 3001",
      "image_url": "https://rubberclone-ai.pats.my/uploads/scan_14.png"
    }
  }
  ```
* **Respons Ralat (400 Bad Request / MIME Ralat)**:
  ```json
  {
    "status": "error",
    "message": "Format fail tidak sah. Hanya imej JPG, PNG, dan WebP sahaja dibenarkan."
  }
  ```

---

### D. Papar Sejarah Imbasan (List Scans)
Mengambil senarai rekod imbasan bagi pengguna semasa. Jika pengguna adalah Admin, semua rekod imbasan global akan dipulangkan.

* **Laluan (Route)**: `api/analysis/list` (atau `index.php?url=api/analysis/list`)
* **Kaedah HTTP**: `GET`
* **Pengepala (Headers)**:
  * `Authorization: Bearer <token_jwt_anda>`
* **Respons Berjaya (200 OK)**:
  ```json
  {
    "status": "success",
    "data": [
      {
        "id": 14,
        "username": "pegawai_tapak123",
        "fullname": "Ahmad Subri bin Hashim",
        "clone_name": "RRIM 3001",
        "confidence": 0.95,
        "timestamp": 1781425778157,
        "latitude": 4.5921,
        "longitude": 101.0901,
        "location_name": "Tapak Semaian RISDA Ipoh, Perak",
        "notes": "Keadaan daun sihat.",
        "soil_type": "Tanah Liat Berpasir",
        "rainfall": "2,200 mm",
        "elevation": "120 meter",
        "image_url": "https://rubberclone-ai.pats.my/uploads/scan_14.png"
      }
    ]
  }
  ```

---

### E. Padam Rekod Imbasan Individu (Delete Scan)
Memadam rekod imbasan tertentu mengikut ID. Pengguna biasa hanya boleh memadam rekod miliknya sendiri, manakala Admin boleh memadam sebarang rekod.

* **Laluan (Route)**: `api/analysis/delete?id=<id_rekod>` (atau `index.php?url=api/analysis/delete&id=<id_rekod>`)
* **Kaedah HTTP**: `DELETE`
* **Pengepala (Headers)**:
  * `Authorization: Bearer <token_jwt_anda>`
* **Respons Berjaya (200 OK)**:
  ```json
  {
    "status": "success",
    "message": "Rekod berjaya dipadamkan."
  }
  ```
* **Respons Ralat (403 Forbidden - Tiada Kuasa)**:
  ```json
  {
    "status": "error",
    "message": "Gagal memadam rekod atau anda tiada hak akses."
  }
  ```

---

### F. Bersihkan Semua Sejarah Imbasan (Clear All)
Memadam semua sejarah imbasan bagi akaun pengguna semasa.

* **Laluan (Route)**: `api/analysis/clear` (atau `index.php?url=api/analysis/clear`)
* **Kaedah HTTP**: `POST`
* **Pengepala (Headers)**:
  * `Authorization: Bearer <token_jwt_anda>`
* **Respons Berjaya (200 OK)**:
  ```json
  {
    "status": "success",
    "message": "Semua sejarah imbasan anda telah dibersihkan."
  }
  ```

---

## 3. Kod Contoh Sambungan Android (Kotlin/Retrofit)

### Contoh Takrifan Retrofit Interface
```kotlin
interface RubberCloneApiService {
    @POST("api/auth/login")
    suspend fun login(
        @Body request: LoginRequest
    ): Response<LoginResponse>

    @Multipart
    @POST("api/analysis/upload")
    suspend fun uploadScan(
        @Header("Authorization") token: String,
        @Part("clone_name") cloneName: RequestBody,
        @Part("confidence") confidence: RequestBody,
        @Part("timestamp") timestamp: RequestBody,
        @Part("latitude") latitude: RequestBody,
        @Part("longitude") longitude: RequestBody,
        @Part image: MultipartBody.Part?
    ): Response<UploadResponse>
}
```
