# Rubber Clone AI - Website & Sistem Backend

Sistem **Rubber Clone AI** ialah backend berpusat untuk menguruskan data pengecaman klon getah menggunakan Google Gemini AI. Sistem ini terdiri daripada **RESTful API** untuk aplikasi mudah alih Android, **Portal Pentadbir (Admin Web Portal)**, dan **Halaman Utama Awam (Landing Page)**.

Sistem backend ini dibangunkan menggunakan **PHP 8.2+ (menyokong sehingga PHP 8.5)** dengan pangkalan data **MariaDB**, menggunakan konsep **MVC (Model-View-Controller)** dan mengaktifkan URL yang bersih (menyembunyikan sambungan `.php`).

* **URL Ujian / Live Demo**: [https://rubberclone-ai.pats.my](https://rubberclone-ai.pats.my)

---

## 1. Ciri-ciri Utama

* **Seni Bina MVC**: Pemisahan yang jelas antara logik perniagaan (Model), antaramuka pengguna (View), dan pengawal laluan (Controller).
* **URL Bersih (Clean URLs)**: Laluan API dan portal menggunakan URL mesra pengguna tanpa sambungan `.php` (contoh: `/api/auth/login` berbanding `/api/auth/login.php`).
* **RESTful API**: Mengembalikan respons dalam format JSON dengan pengesahan JWT (JSON Web Token) / Bearer Token.
* **Peta Taburan Geografi**: Memaparkan lokasi geografi imbasan daun getah pada peta interaktif di Portal Pentadbir.
* **Landing Page Premium**: Halaman pendaratan mesra SEO untuk muat turun aplikasi mudah alih (APK).

---

## 2. Struktur Direktori Projek (MVC)

Berikut ialah cadangan struktur direktori untuk mengekalkan konsep MVC dan menyembunyikan fail `.php` daripada URL:

```text
rubberclone.ai/
├── app/                      # Logik Aplikasi
│   ├── config/               # Konfigurasi Sistem
│   │   └── database.php      # Sambungan MariaDB
│   ├── controllers/          # Pengawal (Controllers)
│   │   ├── AuthController.php
│   │   ├── AnalysisController.php
│   │   └── AdminController.php
│   ├── core/                 # Enjin Utama MVC
│   │   ├── App.php           # Front Controller / Routing
│   │   ├── Controller.php    # Base Controller
│   │   └── Database.php      # Kelas Wrapper PDO
│   ├── models/               # Model Pangkalan Data
│   │   ├── User.php
│   │   └── AnalysisRecord.php
│   └── views/                # Paparan Portal Pentadbir
│       ├── admin/
│       │   ├── dashboard.php
│       │   ├── users.php
│       │   └── history.php
│       └── layouts/
│           ├── header.php
│           └── footer.php
├── public/                   # Folder Akses Awam (Document Root)
│   ├── assets/               # Fail Statik
│   │   ├── css/              # Fail Stylesheet (Glassmorphism)
│   │   ├── js/               # Kod JavaScript (Chart.js & Leaflet)
│   │   └── images/           # Mockups & Gambar Daun
│   ├── uploads/              # Gambar yang dimuat naik oleh Android Client
│   ├── .htaccess             # Peraturan URL Rewriting (Apache)
│   └── index.php             # Fail Utama / Pintu Masuk Aplikasi
├── index.html                # Landing Page Awam (Download APK)
├── styles.css                # CSS untuk Landing Page Awam
├── PRD.md                    # Dokumen Keperluan Produk (PRD)
├── README.md                 # Dokumentasi Projek
└── LICENSE                   # Lesen Sumber Terbuka
```

---

## 3. Penyembunyian Sambungan `.php` (URL Rewriting)

Semua permintaan (requests) akan diarahkan ke `public/index.php` yang bertindak sebagai *Front Controller*. Enjin penghalaan (Routing Engine) akan menganalisis URL dan memanggil controller yang sepadan tanpa memaparkan nama fail `.php`.

### A. Konfigurasi Apache (`public/.htaccess`)
Sediakan fail `.htaccess` berikut di dalam folder `public/` (atau direktori root jika dihoskan terus):

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Hentikan proses jika fail atau direktori wujud secara fizikal
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    
    # Hantar semua permintaan ke index.php
    RewriteRule ^(.*)$ index.php?url=$1 [QSA,L]
</IfModule>
```

### B. Konfigurasi Nginx (`nginx.conf`)
Jika anda menggunakan pelayan Nginx, gunakan peraturan konfigurasi blok `location` berikut:

```nginx
server {
    listen 80;
    server_name rubberclone-ai.pats.my;
    root /home/maui/github/rubberclone.ai/public;

    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?url=$uri&$args;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.5-fpm.sock; # Sesuaikan dengan versi PHP aktif
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

---

## 4. Skema Pangkalan Data (MariaDB)

Import skema SQL berikut (atau rujuk fail [schema.sql](file:///home/maui/github/rubberclone.ai/schema.sql)) ke dalam pangkalan data MariaDB anda sebelum menjalankan aplikasi:

```sql
CREATE DATABASE IF NOT EXISTS `rubberclone` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `rubberclone`;

-- 1. Jadual Pengguna & Pentadbir
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(100) UNIQUE NOT NULL,
  `username` VARCHAR(50) UNIQUE NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `fullname` VARCHAR(150) NOT NULL,
  `agency` VARCHAR(100) DEFAULT 'RISDA Pekebun Kecil',
  `status` ENUM('active', 'inactive') DEFAULT 'active',
  `role` ENUM('user', 'admin') DEFAULT 'user',
  `registration_date` BIGINT NOT NULL, -- Unix timestamp (milisaat) daripada Android client
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Jadual Rekod Analisis Daun Getah
CREATE TABLE IF NOT EXISTS `analysis_records` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `clone_name` VARCHAR(100) NOT NULL,
  `confidence` FLOAT NOT NULL,
  `timestamp` BIGINT NOT NULL, -- Unix timestamp (milisaat)
  `latitude` DOUBLE NOT NULL,
  `longitude` DOUBLE NOT NULL,
  `location_name` VARCHAR(255) DEFAULT 'Stesen RISDA, Malaysia',
  `image_url` VARCHAR(255) DEFAULT NULL,
  `notes` TEXT,
  `soil_type` VARCHAR(100) DEFAULT 'Tiada Maklumat',
  `rainfall` VARCHAR(100) DEFAULT 'Tiada Maklumat',
  `elevation` VARCHAR(100) DEFAULT 'Tiada Maklumat',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Jadual Tetapan CMS Landing Page
CREATE TABLE IF NOT EXISTS `cms_settings` (
  `key` VARCHAR(50) PRIMARY KEY,
  `value` TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Jadual Cerita Blog / Kisah Kejayaan
CREATE TABLE IF NOT EXISTS `blog_posts` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `content` TEXT NOT NULL,
  `image_url` VARCHAR(255) DEFAULT NULL,
  `author` VARCHAR(100) DEFAULT 'RISDA Pentadbir',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

> [!NOTE]
> Sistem ini mempunyai mekanisme **Auto-Seeder** terbina dalam yang akan membina jadual dan memasukkan data tetapan lalai (CMS & sampel Blog Stories) secara automatik semasa melawat halaman buat kali pertama jika pangkalan data kosong.

---

## 5. Struktur Routing Utama

Sistem ini dibina berasaskan corak reka bentuk MVC ringkas di mana semua laluan dipetakan di dalam `app/core/App.php`:

| Laluan (Route) | Controller / Kaedah | Perihalan |
| :--- | :--- | :--- |
| `/` | `HomeController@index` | Paparan Landing Page Dinamik awam |
| `/stories` | `HomeController@storiesView` | Paparan Halaman Cerita Blog / Kisah Kejayaan (menyokong expand/collapse inline) |
| `/pentadbir` | `AdminController@dashboard` | Papan Pemuka Pentadbir / Borang Log Masuk (pintu masuk pentadbir rahsia, disembunyikan dari menu awam) |
| `/admin/users` | `AdminController@usersView` | Halaman Pengurusan Direktori Pengguna |
| `/admin/history` | `AdminController@historyView` | Halaman Sejarah Audit & Imbasan Geografi |
| `/admin/cms` | `AdminController@cmsView` | Halaman Pengurusan CMS (Ubah teks landing & Blog Stories) |
| `/api/auth/*` | `AuthController` | API pendaftaran & log masuk untuk Android client |
| `/api/analysis/*` | `AnalysisController` | API upload & senarai analisis daun getah |
| `/api/admin/*` | `AdminController` | API urus pengguna, CMS, stats dashboard & CRUD Blog Stories |

---

## 6. Panduan Pemasangan Tempatan (Local Setup)

### 6.1 Prasyarat
* Pasang **PHP 8.2** sehingga **PHP 8.5**
* Pasang **MariaDB**
* Pasang pelayan web **Apache** atau **Nginx**

### 6.2 Langkah Pemasangan
1. **Klon Repositori**:
   ```bash
   git clone https://github.com/maui2023/rubberclone.ai.git
   cd rubberclone.ai
   ```

2. **Sediakan Pangkalan Data**:
   * Log masuk ke konsol MariaDB:
     ```bash
     mysql -u root -p
     ```
   * Cipta pangkalan data `rubberclone` dan import skema SQL:
     ```sql
     CREATE DATABASE IF NOT EXISTS `rubberclone` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
     ```
   * Anda boleh mengimport skema terus dari fail `schema.sql`:
     ```bash
     mysql -u root -p rubberclone < schema.sql
     ```

3. **Konfigurasi Aplikasi**:
   * Ubah suai `app/config/database.php` mengikut hos, nama pengguna, dan kata laluan MariaDB anda:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_NAME', 'rubberclone');
     define('DB_USER', 'rubberclone');
     define('DB_PASS', '84b28ab2bee03');
     ```

4. **Konfigurasi Web Server**:
   * Jika menggunakan Apache, pastikan modul `mod_rewrite` diaktifkan:
     ```bash
     sudo a2enmod rewrite
     sudo systemctl restart apache2
     ```
   * Pastikan direktori utama (Document Root) hos maya (Virtual Host) anda dihalakan ke folder `public/`.

 5. **Akses Sistem**:
   * Lawati `/pentadbir` untuk membuka Papan Pemuka Pentadbir.
   * Akaun Pentadbir Lalai (Auto-Generated jika jadual kosong):
     * **E-mel**: `admin@demo.com`
     * **Kata Laluan**: `admin123`
