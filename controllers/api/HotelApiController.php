<?php
require_once 'controllers/BaseController.php';
require_once 'models/Hotel.php';

class HotelApiController extends BaseController {
    private $hotelModel;
    
    public function __construct() {
        parent::__construct();
        $this->hotelModel = new Hotel();
    }

    public function index() {
        $search = $_GET['search'] ?? '';
        $checkIn = $_GET['check_in'] ?? '';
        $checkOut = $_GET['check_out'] ?? '';
        
        $query = "SELECT * FROM hotels WHERE 1=1";
        $params = [];
        
        if ($search) {
            $query .= " AND (name LIKE ? OR city LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        if ($checkIn && $checkOut) {
            $query .= " AND id NOT IN (
                SELECT hotel_id FROM hotel_bookings 
                WHERE (check_in <= ? AND check_out >= ?)
            )";
            $params[] = $checkOut;
            $params[] = $checkIn;
        }
        
        $query .= " ORDER BY name ASC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $hotels = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $this->json($hotels);
    }
    
    public function show($id) {
        $stmt = $this->db->prepare("SELECT * FROM hotels WHERE id = ?");
        $stmt->execute([$id]);
        $hotel = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$hotel) {
            $this->json(['error' => 'Hotel not found'], 404);
        }
        
        $this->json($hotel);
    }
} 