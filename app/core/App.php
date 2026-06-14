<?php
// app/core/App.php

class App {
    protected $controller = 'AdminController';
    protected $method = 'dashboard';
    protected $params = [];

    public function __construct() {
        $url = $this->parseUrl();

        // Pemetaan Laluan (Routing Map)
        // Format: 'path' => ['Controller', 'Method', 'HTTP_METHOD']
        $routes = [
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
            
            // 4. Halaman Web Portal Pentadbir (Admin Web Views)
            'admin/dashboard'       => ['AdminController', 'dashboard', 'GET'],
            'admin/users'           => ['AdminController', 'usersView', 'GET'],
            'admin/history'         => ['AdminController', 'historyView', 'GET']
        ];

        // Jika melawat root folder '/' pada public, hala ke login/dashboard
        if (empty($url)) {
            $url = 'admin/dashboard';
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
                echo "<p><a href='/admin/dashboard' style='color: #10B981; font-weight: bold;'>Kembali ke Papan Pemuka Pentadbir</a></p>";
                echo "</div>";
            }
            exit;
        }
    }

    private function parseUrl() {
        if (isset($_GET['url'])) {
            return rtrim($_GET['url'], '/');
        }
        return '';
    }
}
