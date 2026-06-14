<?php
// app/controllers/HomeController.php

class HomeController extends Controller {
    private $cmsModel;
    private $blogModel;

    public function __construct() {
        $this->cmsModel = $this->model('CmsSetting');
        $this->blogModel = $this->model('BlogPost');
    }

    // Mengendalikan paparan Halaman Utama Dinamik (GET /)
    public function index() {
        $settings = $this->cmsModel->getAll();
        $blogs = $this->blogModel->getAll();

        $this->view('home', [
            'settings' => $settings,
            'blogs' => $blogs
        ]);
    }

    // API Awam mengambil senarai kisah kejayaan (GET /api/blog/list)
    public function getBlogPosts() {
        $blogs = $this->blogModel->getAll();
        
        $formatted = [];
        foreach ($blogs as $b) {
            $formatted[] = [
                'id' => (int)$b['id'],
                'title' => $b['title'],
                'content' => $b['content'],
                'image_url' => $b['image_url'],
                'author' => $b['author'],
                'created_at' => $b['created_at']
            ];
        }

        $this->jsonResponse([
            'status' => 'success',
            'data' => $formatted
        ], 200);
    }
}
