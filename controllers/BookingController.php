<?php
require_once 'controllers/BaseController.php';

class BookingController extends BaseController {
    public function __construct() {
        parent::__construct();
    }
    
    public function process() {
        $bookingType = $_GET['type'] ?? '';
        $itemId = $_GET['id'] ?? '';
        
        if (empty($bookingType) || empty($itemId)) {
            setFlashMessage('Information de réservation invalide', 'danger');
            redirect('/');
        }
        
        // Get item details based on type
        $item = null;
        switch ($bookingType) {
            case 'flight':
                $stmt = $this->db->prepare("
                    SELECT f.*, a.name as airline_name, a.logo_image 
                    FROM flights f 
                    JOIN airlines a ON f.airline_id = a.id 
                    WHERE f.id = ?
                ");
                $stmt->execute([$itemId]);
                $item = $stmt->fetch(PDO::FETCH_ASSOC);
                break;
                
            case 'hotel':
                $stmt = $this->db->prepare("SELECT * FROM hotels WHERE id = ?");
                $stmt->execute([$itemId]);
                $item = $stmt->fetch(PDO::FETCH_ASSOC);
                break;
                
            case 'cruise':
                $stmt = $this->db->prepare("SELECT * FROM cruises WHERE id = ?");
                $stmt->execute([$itemId]);
                $item = $stmt->fetch(PDO::FETCH_ASSOC);
                break;
                
            default:
                setFlashMessage('Type de réservation invalide', 'danger');
                redirect('/');
        }
        
        if (!$item) {
            setFlashMessage('Item non trouvé', 'danger');
            redirect('/');
        }
        
        $this->view('booking/process', [
            'type' => $bookingType,
            'item' => $item
        ]);
    }
    
    public function confirm() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/');
        }
        
        $userId = $_SESSION['user_id'];
        $bookingType = $_POST['booking_type'] ?? '';
        $itemId = $_POST['item_id'] ?? '';
        $passengers = $_POST['passengers'] ?? 1;
        $totalPrice = $_POST['total_price'] ?? 0;
        
        // Additional booking details
        $contactName = $_POST['contact_name'] ?? '';
        $contactEmail = $_POST['contact_email'] ?? '';
        $contactPhone = $_POST['contact_phone'] ?? '';
        
        // Validate required fields
        if (empty($bookingType) || empty($itemId) || empty($contactName) || empty($contactEmail)) {
            setFlashMessage('Veuillez remplir tous les champs obligatoires', 'danger');
            redirect("/book/{$bookingType}/{$itemId}");
        }
        
        try {
            $this->db->beginTransaction();
            
            // Create booking record
            $stmt = $this->db->prepare("
                INSERT INTO bookings (
                    user_id, booking_type, item_id, booking_date, total_price, status, created_at
                ) VALUES (
                    ?, ?, ?, ?, ?, 'confirmed', NOW()
                )
            ");
            
            $stmt->execute([
                $userId, $bookingType, $itemId, date('Y-m-d'), $totalPrice
            ]);
            
            $bookingId = $this->db->lastInsertId();
            
            // Update availability if needed (e.g., flight seats, hotel rooms)
            switch ($bookingType) {
                case 'flight':
                    $stmt = $this->db->prepare("
                        UPDATE flights 
                        SET available_seats = available_seats - ? 
                        WHERE id = ? AND available_seats >= ?
                    ");
                    $stmt->execute([$passengers, $itemId, $passengers]);
                    break;
                    
                case 'hotel':
                    $stmt = $this->db->prepare("
                        UPDATE hotels 
                        SET available_rooms = available_rooms - 1 
                        WHERE id = ? AND available_rooms > 0
                    ");
                    $stmt->execute([$itemId]);
                    break;
                    
                case 'cruise':
                    $stmt = $this->db->prepare("
                        UPDATE cruises 
                        SET available_cabins = available_cabins - 1 
                        WHERE id = ? AND available_cabins > 0
                    ");
                    $stmt->execute([$itemId]);
                    break;
            }
            
            $this->db->commit();
            setFlashMessage('Réservation confirmée avec succès!');
            redirect('/bookings'); // Redirect to bookings list
            
        } catch (Exception $e) {
            $this->db->rollBack();
            setFlashMessage('Erreur lors de la réservation: ' . htmlspecialchars($e->getMessage()), 'danger');
            redirect("/book/{$bookingType}/{$itemId}");
        }
    }

    public function index() {
        if (!isset($_SESSION['user_id'])) {
            setFlashMessage('Vous devez être connecté pour voir vos réservations.', 'danger');
            redirect('/login');
        }
        require_once 'models/Booking.php';
        $bookingModel = new Booking();
        $bookings = $bookingModel->getByUserId($_SESSION['user_id'], 100, 0);
        $this->view('booking/index', [
            'pageTitle' => 'Mes Réservations',
            'bookings' => $bookings
        ]);
    }
} 