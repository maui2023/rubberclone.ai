<?php
// app/core/App.php

class App {
    protected $controller = 'HomeController';
    protected $method = 'index';
    protected $params = [];

    public function __construct() {
        $url = $this->parseUrl();

        // Pemetaan Laluan (Routing Map)
        // Format: 'path' => ['Controller', 'Method', 'HTTP_METHOD']
        $routes = [
            // 0. Laluan Awam (Public Views & Blog API)
            ''                      => ['HomeController', 'index', 'GET'],
            'api/blog/list'         => ['HomeController', 'getBlogPosts', 'GET'],

            // 1. Laluan API Pengesahan (Auth Endpoints)
            'api/auth/register'     => ['AuthController', 'register', 'POST'],
            'api/auth/login'        => ['AuthController', 'login', 'POST'],
            
            // 2. Laluan API Analisis Daun (Analysis Endpoints)
            'api/analysis/upload'   => ['AnalysisController', 'upload', 'POST'],
            'api/analysis/list'     => ['AnalysisController', 'list', 'GET'],
            'api/analysis/delete'   => ['AnalysisController', 'delete', 'DELETE'],
            'api/analysis/clear'    => ['AnalysisController', 'clear', 'POST'],
            
            // 3. Laluan API Pentadbir (Admin Endpoints)
            'api/admin/users'       => ['AdminController', 'getUsers', 'GET'],
            'api/admin/toggle_user' => ['AdminController', 'toggleUserStatus', 'POST'],
            'api/admin/stats'       => ['AdminController', 'getStats', 'GET'],
            'api/admin/update_cms'  => ['AdminController', 'updateCms', 'POST'],
            'api/admin/blog/create' => ['AdminController', 'createBlogPost', 'POST'],
            'api/admin/blog/delete' => ['AdminController', 'deleteBlogPost', 'DELETE'],
            
            // 4. Halaman Web Portal Pentadbir (Admin Web Views)
            'pentadbir'             => ['AdminController', 'dashboard', 'GET'],
            'admin/users'           => ['AdminController', 'usersView', 'GET'],
            'admin/history'         => ['AdminController', 'historyView', 'GET'],
            'admin/cms'             => ['AdminController', 'cmsView', 'GET'],
            'admin/loginWeb'        => ['AdminController', 'loginWeb', 'POST'],
            'admin/logoutWeb'       => ['AdminController', 'logoutWeb', 'GET']
        ];

        // Jika melawat root folder '/' pada public, hala ke halaman utama dinamik
        if (empty($url)) {
            $url = '';
        }

        if (array_key_exists($url, $routes)) {
            $route = $routes[$url];
            $controllerName = $route[0];
            $methodName = $route[1];
            $allowedMethod = isset($route[2]) ? $route[2] : 'GET';

            // Semak kaedah HTTP (HTTP Method Verification)
            if ($_SERVER['REQUEST_METHOD'] !== $allowedMethod) {
                header('Content-Type: application/json; charset=utf-8');
                http_response_code(405);
                echo json_encode([
                    "status" => "error",
                    "message" => "Kaedah HTTP {$_SERVER['REQUEST_METHOD']} tidak dibenarkan. Sila gunakan kaedah {$allowedMethod}."
                ]);
                exit;
            }

            // Muat dan mulakan controller
            $controllerFile = __DIR__ . '/../controllers/' . $controllerName . '.php';
            if (file_exists($controllerFile)) {
                require_once $controllerFile;
                $this->controller = new $controllerName();
                $this->method = $methodName;
                
                // Panggil function dalam controller
                call_user_func_array([$this->controller, $this->method], $this->params);
            } else {
                http_response_code(500);
                echo "Fail controller '$controllerName' tidak ditemui.";
                exit;
            }
        } else {
            // Respons ralat 404
            if (strpos($url, 'api/') === 0) {
                header('Content-Type: application/json; charset=utf-8');
                http_response_code(404);
                echo json_encode(["status" => "error", "message" => "Laluan (Route) API tidak ditemui."]);
            } else {
                http_response_code(404);
                echo "<div style='font-family: sans-serif; text-align: center; padding: 4rem; color: #333;'>";
                echo "<h1>404 - Halaman Tidak Ditemui</h1>";
                echo "<p>Maaf, halaman yang anda cari tiada di dalam sistem.</p>";
                echo "<p><a href='/pentadbir' style='color: #10B981; font-weight: bold;'>Kembali ke Papan Pemuka Pentadbir</a></p>";
                echo "</div>";
            }
            exit;
        }
    }

    private function parseUrl() {
        if (isset($_GET['url'])) {
            return rtrim($_GET['url'], '/');
        }

        // Fallback jika URL rewriting tidak memasukkan parameter ?url=
        $requestUri = $_SERVER['REQUEST_URI'] ?? '';
        $path = parse_url($requestUri, PHP_URL_PATH);
        $path = trim($path, '/');

        // Jika terdapat index.php di permulaan path (cth: /index.php/pentadbir -> pentadbir)
        if (strpos($path, 'index.php') === 0) {
            $path = trim(substr($path, 9), '/');
        }
        // Jika terdapat public/ di permulaan path
        if (strpos($path, 'public/') === 0) {
            $path = trim(substr($path, 7), '/');
        }

        return $path;
    }
}
