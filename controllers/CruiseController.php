<?php
require_once 'controllers/BaseController.php';
require_once 'models/Cruise.php';

class CruiseController extends BaseController {
    public function __construct() {
        parent::__construct();
        // Load Cruise model here in a real app
    }
    
    public function index() {
        $filters = [];
        if (!empty($_GET['destination'])) {
            $filters['destination_port'] = $_GET['destination'];
        }
        if (!empty($_GET['departure_port'])) {
            $filters['departure_port'] = $_GET['departure_port'];
        }
        if (!empty($_GET['date'])) {
            $filters['departure_date'] = $_GET['date'];
        }
        if (!empty($_GET['name'])) {
            $filters['name'] = $_GET['name'];
        }
        if (!empty($_GET['max_price'])) {
            $filters['max_price'] = $_GET['max_price'];
        }
        if (!empty($_GET['min_cabins'])) {
            $filters['min_cabins'] = $_GET['min_cabins'];
        }
        
        $cruiseModel = new Cruise();
        $cruises = $cruiseModel->getAll($filters, 100, 0);
        $this->view('cruises/index', ['cruises' => $cruises]);
    }
} 