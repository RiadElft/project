<?php
class BaseController {
    protected $db;

    public function __construct() {
        $this->db = getDBConnection();
    }

    protected function view($view, $data = []) {
        // Extract data to make variables available in view
        extract($data);
        
        // Start output buffering
        ob_start();
        
        // Include the view file
        require_once "views/{$view}.php";
        
        // Get contents and clean buffer
        $content = ob_get_clean();
        
        // Output the content
        echo $content;
    }

    protected function json($data, $status = 200) {
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode($data);
        exit;
    }
} 