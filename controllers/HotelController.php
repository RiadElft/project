<?php
require_once 'controllers/BaseController.php';
require_once 'models/Hotel.php';

class HotelController extends BaseController {
    private $hotelModel;
    public function __construct() {
        parent::__construct();
        $this->hotelModel = new Hotel();
    }
    
    public function index() {
        $filters = [];
        if (!empty($_GET['location'])) {
            $filters['city'] = $_GET['location'];
        }
        if (!empty($_GET['stars'])) {
            $filters['min_rating'] = $_GET['stars'];
        }
        if (!empty($_GET['country'])) {
            $filters['country'] = $_GET['country'];
        }
        if (!empty($_GET['name'])) {
            $filters['name'] = $_GET['name'];
        }
        if (!empty($_GET['max_price'])) {
            $filters['max_price'] = $_GET['max_price'];
        }
        if (!empty($_GET['min_rooms'])) {
            $filters['min_rooms'] = $_GET['min_rooms'];
        }
        
        $hotels = $this->hotelModel->getAll($filters, 100, 0);
        $this->view('hotels/index', ['hotels' => $hotels]);
    }
} 