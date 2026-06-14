<?php
// app/config/database.php (Salinan Templat / Template Copy)
// Sila salin fail ini ke 'database.php' dan sesuaikan mengikut hos pangkalan data anda.

// Pangkalan Data MariaDB - Tetapan Sambungan
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'nama_db_anda');
define('DB_USER', 'username_anda');
define('DB_PASS', 'password_anda');

// Kunci Rahsia JWT untuk token keselamatan
define('JWT_SECRET', 'kunci_rahsia_jwt_anda_di_sini_12345!');
define('JWT_EXPIRY', 86400 * 7); // Sah selama 7 hari
