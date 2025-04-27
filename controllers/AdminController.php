<?php
require_once 'controllers/BaseController.php';

class AdminController extends BaseController {
    public function __construct() {
        parent::__construct();
        
        // Check if user is admin
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            setFlashMessage('Unauthorized access', 'danger');
            redirect('/dashboard');
        }
    }
    
    public function index() {
        // Get statistics
        $stats = [
            'users' => $this->db->query("SELECT COUNT(*) FROM users")->fetchColumn(),
            'bookings' => $this->db->query("SELECT COUNT(*) FROM bookings")->fetchColumn(),
            'revenue' => $this->db->query("SELECT SUM(total_price) FROM bookings")->fetchColumn(),
            'recent_bookings' => $this->db->query("
                SELECT 
                    b.*,
                    u.name as user_name,
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
                JOIN users u ON b.user_id = u.id
                ORDER BY b.created_at DESC
                LIMIT 10
            ")->fetchAll(PDO::FETCH_ASSOC)
        ];
        
        $this->view('admin/dashboard', [
            'stats' => $stats
        ]);
    }
} 