<?php
require_once 'controllers/BaseController.php'; // Assuming admin controllers might share some base logic
require_once 'models/Flight.php'; // Load necessary models
require_once 'models/Hotel.php';
require_once 'models/Cruise.php';
require_once 'models/User.php';
require_once 'models/Airline.php'; // Add Airline model
// require_once 'models/Booking.php'; // Future: Load Booking model

class AdminDashboardController extends BaseController {
    
    private $flightModel;
    private $hotelModel;
    private $cruiseModel;
    private $userModel;
    private $airlineModel; // Add Airline model property
    // private $bookingModel;

    public function __construct() {
        parent::__construct();
        // Instantiate models
        $this->flightModel = new Flight();
        $this->hotelModel = new Hotel();
        $this->cruiseModel = new Cruise();
        $this->userModel = new User();
        $this->airlineModel = new Airline(); // Instantiate Airline model
        // $this->bookingModel = new Booking(); // Future
    }

    public function index() {
        // Fetch counts for stats cards
        $stats = [
            'flights' => $this->flightModel->countAll() ?: 0, 
            'hotels' => $this->hotelModel->countAll() ?: 0,
            'cruises' => $this->cruiseModel->countAll() ?: 0,
            'bookings' => 0, // Placeholder
            'users' => $this->userModel->countAll() ?: 0,
            'airlines' => $this->airlineModel->countAll() ?: 0 
        ];
        
        // Removed fetching $airlines data

        $this->view('admin/dashboard/index', [
            'pageTitle' => 'Tableau de bord Admin',
            'current_page' => 'dashboard',
            'stats' => $stats 
            // Removed passing $airlines to the view
        ]);
    }
} 