<?php
require_once 'controllers/BaseController.php';

class DashboardController extends BaseController {
    private $userModel;
    private $bookingModel;
    
    public function __construct() {
        parent::__construct();
        require_once 'models/User.php';
        $this->userModel = new User();
        // require_once 'models/Booking.php'; // Uncomment when Booking model is ready
        // $this->bookingModel = new Booking();
    }
    
    public function index() {
        // Check if user is logged in (redundant due to index.php check, but good practice)
        if (!isset($_SESSION['user_id'])) {
            redirect('/login');
        }
        
        // Featured destinations data with French descriptions
        $featured_destinations = [
            [
                'city' => 'Paris',
                'country' => 'France',
                'description' => 'Experience the city of love', // Keep original for potential fallback/other languages
                'description_fr' => 'Découvrez la ville de l\'amour',
                'image' => 'paris.jpg'
            ],
            [
                'city' => 'Tokyo',
                'country' => 'Japan',
                'description' => 'Discover the blend of tradition and innovation',
                'description_fr' => 'Découvrez le mélange de tradition et d\'innovation',
                'image' => 'tokyo.jpg'
            ],
            [
                'city' => 'New York',
                'country' => 'USA',
                'description' => 'The city that never sleeps',
                'description_fr' => 'La ville qui ne dort jamais',
                'image' => 'newyork.jpg'
            ]
        ];
        
        // // Future implementation: Get actual user stats and bookings
        // $userId = $_SESSION['user_id'];
        // $stats = [
        //     'bookings' => $this->bookingModel->countByUser($userId),
        //     'upcoming' => $this->bookingModel->countUpcomingByUser($userId),
        //     'total_spent' => $this->bookingModel->getTotalSpentByUser($userId)
        // ];
        // $recent_bookings = $this->bookingModel->getRecentByUser($userId, 5);
        
        $this->view('dashboard/index', [
            'featured_destinations' => $featured_destinations
            // 'stats' => $stats, // Pass actual data when available
            // 'recent_bookings' => $recent_bookings // Pass actual data when available
        ]);
    }
} 