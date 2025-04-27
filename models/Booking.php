<?php
class Booking {
    private $db;
    
    public function __construct() {
        $this->db = dbConnect();
    }
    
    /**
     * Get booking by ID
     * @param int $id Booking ID
     * @return array|false Booking data or false if not found
     */
    public function getById($id) {
        try {
            $stmt = $this->db->prepare("
                SELECT b.*, 
                       u.name as user_name, u.email as user_email
                FROM bookings b
                JOIN users u ON b.user_id = u.id
                WHERE b.id = :id
            ");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            $booking = $stmt->fetch();
            
            if ($booking) {
                // Get related flight, hotel, or cruise details
                $booking['details'] = $this->getBookingDetails($booking['booking_type'], $booking['item_id']);
            }
            
            return $booking;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    
    /**
     * Get booking details based on type and item ID
     * @param string $type Booking type (flight, hotel, cruise)
     * @param int $itemId Item ID
     * @return array|false Item details or false if not found
     */
    private function getBookingDetails($type, $itemId) {
        try {
            $table = '';
            
            switch ($type) {
                case 'flight':
                    $table = 'flights';
                    break;
                case 'hotel':
                    $table = 'hotels';
                    break;
                case 'cruise':
                    $table = 'cruises';
                    break;
                default:
                    return false;
            }
            
            $stmt = $this->db->prepare("SELECT * FROM $table WHERE id = :id");
            $stmt->bindParam(':id', $itemId, PDO::PARAM_INT);
            $stmt->execute();
            
            $details = $stmt->fetch();
            
            // Get destination ports for cruises
            if ($type === 'cruise' && $details) {
                $stmt = $this->db->prepare("
                    SELECT port_name FROM cruise_destinations 
                    WHERE cruise_id = :cruise_id
                    ORDER BY port_order ASC
                ");
                $stmt->bindParam(':cruise_id', $itemId, PDO::PARAM_INT);
                $stmt->execute();
                
                $ports = $stmt->fetchAll(PDO::FETCH_COLUMN);
                $details['destination_ports'] = $ports;
            }
            
            return $details;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    
    /**
     * Get bookings by user ID
     * @param int $userId User ID
     * @param int $limit Result limit
     * @param int $offset Pagination offset
     * @return array|false Bookings or false on failure
     */
    public function getByUserId($userId, $limit = 10, $offset = 0) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM bookings 
                WHERE user_id = :user_id
                ORDER BY created_at DESC
                LIMIT :limit OFFSET :offset
            ");
            
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            $bookings = $stmt->fetchAll();
            
            // Get details for each booking
            foreach ($bookings as &$booking) {
                $booking['details'] = $this->getBookingDetails($booking['booking_type'], $booking['item_id']);
            }
            
            return $bookings;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    
    /**
     * Count bookings by user ID
     * @param int $userId User ID
     * @return int|false Count or false on failure
     */
    public function countByUserId($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) FROM bookings 
                WHERE user_id = :user_id
            ");
            
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all bookings with optional filters
     * @param array $filters Search filters
     * @param int $limit Result limit
     * @param int $offset Pagination offset
     * @return array|false Bookings or false on failure
     */
    public function getAll($filters = [], $limit = 10, $offset = 0) {
        try {
            $sql = "
                SELECT b.*, 
                       u.name as user_name, u.email as user_email
                FROM bookings b
                JOIN users u ON b.user_id = u.id
                WHERE 1=1
            ";
            $params = [];
            
            // Apply filters
            if (!empty($filters['user_id'])) {
                $sql .= " AND b.user_id = :user_id";
                $params[':user_id'] = $filters['user_id'];
            }
            
            if (!empty($filters['booking_type'])) {
                $sql .= " AND b.booking_type = :booking_type";
                $params[':booking_type'] = $filters['booking_type'];
            }
            
            if (!empty($filters['status'])) {
                $sql .= " AND b.status = :status";
                $params[':status'] = $filters['status'];
            }
            
            if (!empty($filters['date_from'])) {
                $sql .= " AND b.created_at >= :date_from";
                $params[':date_from'] = $filters['date_from'] . ' 00:00:00';
            }
            
            if (!empty($filters['date_to'])) {
                $sql .= " AND b.created_at <= :date_to";
                $params[':date_to'] = $filters['date_to'] . ' 23:59:59';
            }
            
            // Add sorting
            $sql .= " ORDER BY b.created_at DESC";
            
            // Add pagination
            $sql .= " LIMIT :limit OFFSET :offset";
            $params[':limit'] = $limit;
            $params[':offset'] = $offset;
            
            $stmt = $this->db->prepare($sql);
            
            // Bind all parameters
            foreach ($params as $key => &$value) {
                if (is_int($value)) {
                    $stmt->bindParam($key, $value, PDO::PARAM_INT);
                } else {
                    $stmt->bindParam($key, $value, PDO::PARAM_STR);
                }
            }
            
            $stmt->execute();
            $bookings = $stmt->fetchAll();
            
            // Get details for each booking
            foreach ($bookings as &$booking) {
                $booking['details'] = $this->getBookingDetails($booking['booking_type'], $booking['item_id']);
            }
            
            return $bookings;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    
    /**
     * Count all bookings with filters
     * @param array $filters Search filters
     * @return int|false Count or false on failure
     */
    public function countAll($filters = []) {
        try {
            $sql = "
                SELECT COUNT(*) 
                FROM bookings b
                JOIN users u ON b.user_id = u.id
                WHERE 1=1
            ";
            $params = [];
            
            // Apply filters (same as getAll)
            if (!empty($filters['user_id'])) {
                $sql .= " AND b.user_id = :user_id";
                $params[':user_id'] = $filters['user_id'];
            }
            
            if (!empty($filters['booking_type'])) {
                $sql .= " AND b.booking_type = :booking_type";
                $params[':booking_type'] = $filters['booking_type'];
            }
            
            if (!empty($filters['status'])) {
                $sql .= " AND b.status = :status";
                $params[':status'] = $filters['status'];
            }
            
            if (!empty($filters['date_from'])) {
                $sql .= " AND b.created_at >= :date_from";
                $params[':date_from'] = $filters['date_from'] . ' 00:00:00';
            }
            
            if (!empty($filters['date_to'])) {
                $sql .= " AND b.created_at <= :date_to";
                $params[':date_to'] = $filters['date_to'] . ' 23:59:59';
            }
            
            $stmt = $this->db->prepare($sql);
            
            // Bind all parameters
            foreach ($params as $key => &$value) {
                if (is_int($value)) {
                    $stmt->bindParam($key, $value, PDO::PARAM_INT);
                } else {
                    $stmt->bindParam($key, $value, PDO::PARAM_STR);
                }
            }
            
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    
    /**
     * Create booking
     * @param array $data Booking data
     * @return int|false Booking ID or false on failure
     */
    public function create($data) {
        try {
            $this->db->beginTransaction();
            
            // Insert booking
            $stmt = $this->db->prepare("
                INSERT INTO bookings (
                    user_id, booking_type, item_id, status, price, created_at
                ) VALUES (
                    :user_id, :booking_type, :item_id, :status, :price, NOW()
                )
            ");
            
            $status = isset($data['status']) ? $data['status'] : 'pending';
            
            $stmt->bindParam(':user_id', $data['user_id'], PDO::PARAM_INT);
            $stmt->bindParam(':booking_type', $data['booking_type'], PDO::PARAM_STR);
            $stmt->bindParam(':item_id', $data['item_id'], PDO::PARAM_INT);
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);
            $stmt->bindParam(':price', $data['price'], PDO::PARAM_STR);
            
            $stmt->execute();
            $bookingId = $this->db->lastInsertId();
            
            // Update availability based on booking type
            switch ($data['booking_type']) {
                case 'flight':
                    require_once 'Flight.php';
                    $flightModel = new Flight();
                    $flightModel->updateSeats($data['item_id'], 1);
                    break;
                    
                case 'hotel':
                    require_once 'Hotel.php';
                    $hotelModel = new Hotel();
                    $hotelModel->updateRooms($data['item_id'], 1);
                    break;
                    
                case 'cruise':
                    require_once 'Cruise.php';
                    $cruiseModel = new Cruise();
                    $cruiseModel->updateCabins($data['item_id'], 1);
                    break;
            }
            
            $this->db->commit();
            return $bookingId;
            
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }
    
    /**
     * Update booking status
     * @param int $id Booking ID
     * @param string $status New status
     * @return bool Success status
     */
    public function updateStatus($id, $status) {
        try {
            $stmt = $this->db->prepare("
                UPDATE bookings 
                SET status = :status 
                WHERE id = :id
            ");
            
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete booking
     * @param int $id Booking ID
     * @return bool Success status
     */
    public function delete($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM bookings WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    
    /**
     * Get booking statistics
     * @return array Statistics
     */
    public function getStats() {
        try {
            $stats = [];
            
            // Total bookings
            $stmt = $this->db->query("SELECT COUNT(*) FROM bookings");
            $stats['total'] = $stmt->fetchColumn();
            
            // Bookings by type
            $stmt = $this->db->query("
                SELECT booking_type, COUNT(*) as count 
                FROM bookings 
                GROUP BY booking_type
            ");
            $stats['by_type'] = $stmt->fetchAll();
            
            // Bookings by status
            $stmt = $this->db->query("
                SELECT status, COUNT(*) as count 
                FROM bookings 
                GROUP BY status
            ");
            $stats['by_status'] = $stmt->fetchAll();
            
            // Recent bookings (last 30 days)
            $stmt = $this->db->query("
                SELECT COUNT(*) 
                FROM bookings 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            ");
            $stats['recent'] = $stmt->fetchColumn();
            
            // Total revenue
            $stmt = $this->db->query("
                SELECT SUM(price) 
                FROM bookings 
                WHERE status = 'confirmed'
            ");
            $stats['revenue'] = $stmt->fetchColumn();
            
            return $stats;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
}
?>