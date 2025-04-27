<?php
require_once 'controllers/BaseController.php';

class ProfileController extends BaseController {
    public function index() {
        $userId = $_SESSION['user_id'];
        
        // Get user details
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Get booking statistics
        // Total bookings
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM bookings WHERE user_id = ?");
        $stmt->execute([$userId]);
        $totalBookings = $stmt->fetchColumn();
        
        // Total spent
        $stmt = $this->db->prepare("SELECT SUM(total_price) FROM bookings WHERE user_id = ?");
        $stmt->execute([$userId]);
        $totalSpent = $stmt->fetchColumn();
        
        // Recent bookings
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
            LIMIT 5
        ");
        $stmt->execute([$userId]);
        $recentBookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $stats = [
            'total_bookings' => $totalBookings,
            'total_spent' => $totalSpent,
            'recent_bookings' => $recentBookings
        ];
        
        $this->view('profile/index', [
            'user' => $user,
            'stats' => $stats
        ]);
    }
    
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/profile');
        }
        
        $userId = $_SESSION['user_id'];
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        
        // Validate current password if trying to change password
        if ($newPassword) {
            $stmt = $this->db->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!password_verify($currentPassword, $user['password'])) {
                setFlashMessage('Current password is incorrect', 'danger');
                redirect('/profile');
            }
        }
        
        // Update user details
        $query = "UPDATE users SET name = ?, email = ?";
        $params = [$name, $email];
        
        if ($newPassword) {
            $query .= ", password = ?";
            $params[] = password_hash($newPassword, PASSWORD_DEFAULT);
        }
        
        $query .= " WHERE id = ?";
        $params[] = $userId;
        
        $stmt = $this->db->prepare($query);
        
        try {
            $stmt->execute($params);
            $_SESSION['user_name'] = $name;
            setFlashMessage('Profile updated successfully');
        } catch (PDOException $e) {
            setFlashMessage('Failed to update profile', 'danger');
        }
        
        redirect('/profile');
    }
} 