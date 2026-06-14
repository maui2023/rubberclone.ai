<?php
// app/core/Controller.php

class Controller {
    // Memuatkan model
    public function model($model) {
        require_once __DIR__ . '/../models/' . $model . '.php';
        return new $model();
    }

    // Memaparkan pandangan (view)
    public function view($view, $data = []) {
        // Ekstrak data supaya boleh diakses sebagai pembolehubah tempatan di dalam view
        extract($data);
        
        $viewFile = __DIR__ . '/../views/' . $view . '.php';
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            die("Paparan (View) '$view' tidak ditemui.");
        }
    }

    // Memulangkan respons JSON (untuk API)
    public function jsonResponse($data, $statusCode = 200) {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($statusCode);
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
}
