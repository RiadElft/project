<?php
require_once 'controllers/BaseController.php';
require_once 'models/Flight.php';

class FlightController extends BaseController {
    private $flightModel;
    
    public function __construct() {
        parent::__construct();
        $this->flightModel = new Flight();
    }
    
    public function index() {
        $filters = [];
        if (!empty($_GET['departure'])) {
            $filters['departure_city'] = $_GET['departure'];
        }
        if (!empty($_GET['arrival'])) {
            $filters['arrival_city'] = $_GET['arrival'];
        }
        if (!empty($_GET['date'])) {
            $filters['departure_date'] = $_GET['date'];
        }
        if (!empty($_GET['max_price'])) {
            $filters['max_price'] = $_GET['max_price'];
        }
        if (!empty($_GET['min_seats'])) {
            $filters['min_seats'] = $_GET['min_seats'];
        }
        
        $flights = $this->flightModel->getAll($filters, 100, 0);
        $this->view('flights/index', ['flights' => $flights]);
    }
    
    // Add methods for search, booking details etc. later
} 