<?php
require_once 'controllers/BaseController.php';

class BookingApiController extends BaseController {
    public function index() {
        $userId = $_SESSION['user_id'];
        
        // Get all bookings for the user
        $stmt = $this->db->prepare("
            SELECT 
                b.*,
                CASE 
                    WHEN b.booking_type = 'flight' THEN (
                        SELECT CONCAT(departure_city, ' to ', arrival_city)
                        FROM flights WHERE id = b.item_id
                    )
                    WHEN b.booking_type = 'hotel' THEN (
                        SELECT name FROM hotels WHERE id = b.item_id
                    )
                    WHEN b.booking_type = 'cruise' THEN (
                        SELECT name FROM cruises WHERE id = b.item_id
                    )
                END as item_name
            FROM bookings b
            WHERE b.user_id = ?
            ORDER BY b.created_at DESC
        ");
        
        $stmt->execute([$userId]);
        $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $this->json($bookings);
    }
    
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Method not allowed'], 405);
        }
        
        $userId = $_SESSION['user_id'];
        $data = json_decode(file_get_contents('php://input'), true);
        
        $type = $data['type'] ?? '';
        $itemId = $data['item_id'] ?? '';
        $date = $data['date'] ?? date('Y-m-d');
        
        // Validate booking type
        if (!in_array($type, ['flight', 'hotel', 'cruise'])) {
            $this->json(['error' => 'Invalid booking type'], 400);
        }
        
        // Get price based on type
        $price = $this->getPriceForItem($type, $itemId);
        if (!$price) {
            $this->json(['error' => 'Invalid item selected'], 400);
        }
        
        // Create booking
        $stmt = $this->db->prepare("
            INSERT INTO bookings (user_id, booking_type, item_id, booking_date, total_price)
            VALUES (?, ?, ?, ?, ?)
        ");
        
        try {
            $stmt->execute([$userId, $type, $itemId, $date, $price]);
            $bookingId = $this->db->lastInsertId();
            
            // Get the created booking
            $stmt = $this->db->prepare("
                SELECT 
                    b.*,
                    CASE 
                        WHEN b.booking_type = 'flight' THEN (
                            SELECT CONCAT(departure_city, ' to ', arrival_city)
                            FROM flights WHERE id = b.item_id
                        )
                        WHEN b.booking_type = 'hotel' THEN (
                            SELECT name FROM hotels WHERE id = b.item_id
                        )
                        WHEN b.booking_type = 'cruise' THEN (
                            SELECT name FROM cruises WHERE id = b.item_id
                        )
                    END as item_name
                FROM bookings b
                WHERE b.id = ?
            ");
            $stmt->execute([$bookingId]);
            $booking = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $this->json($booking, 201);
        } catch (PDOException $e) {
            $this->json(['error' => 'Failed to create booking'], 500);
        }
    }
    
    public function show($id) {
        $userId = $_SESSION['user_id'];
        
        $stmt = $this->db->prepare("
            SELECT 
                b.*,
                CASE 
                    WHEN b.booking_type = 'flight' THEN (
                        SELECT CONCAT(departure_city, ' to ', arrival_city)
                        FROM flights WHERE id = b.item_id
                    )
                    WHEN b.booking_type = 'hotel' THEN (
                        SELECT name FROM hotels WHERE id = b.item_id
                    )
                    WHEN b.booking_type = 'cruise' THEN (
                        SELECT name FROM cruises WHERE id = b.item_id
                    )
                END as item_name
            FROM bookings b
            WHERE b.id = ? AND b.user_id = ?
        ");
        
        $stmt->execute([$id, $userId]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$booking) {
            $this->json(['error' => 'Booking not found'], 404);
        }
        
        $this->json($booking);
    }
    
    public function cancel($id) {
        $userId = $_SESSION['user_id'];
        
        // Check if booking exists and belongs to user
        $stmt = $this->db->prepare("SELECT * FROM bookings WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $userId]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$booking) {
            $this->json(['error' => 'Booking not found'], 404);
        }
        
        // Update booking status
        $stmt = $this->db->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ?");
        
        try {
            $stmt->execute([$id]);
            $this->json(['message' => 'Booking cancelled successfully']);
        } catch (PDOException $e) {
            $this->json(['error' => 'Failed to cancel booking'], 500);
        }
    }
    
    private function getPriceForItem($type, $itemId) {
        $query = match($type) {
            'flight' => "SELECT price FROM flights WHERE id = ?",
            'hotel' => "SELECT price_per_night FROM hotels WHERE id = ?",
            'cruise' => "SELECT price FROM cruises WHERE id = ?",
            default => null
        };
        
        if (!$query) return false;
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([$itemId]);
        return $stmt->fetchColumn();
    }
} 