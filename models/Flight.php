<?php
class Flight {
    private $db;
    
    public function __construct() {
        $this->db = dbConnect();
    }
    
    /**
     * Get flight by ID
     * @param int $id Flight ID
     * @return array|false Flight data or false if not found
     */
    public function getById($id) {
        try {
            // JOIN with airlines table
            $stmt = $this->db->prepare("
                SELECT f.*, a.name as airline_name, a.logo_image as airline_logo 
                FROM flights f
                LEFT JOIN airlines a ON f.airline_id = a.id
                WHERE f.id = :id
            ");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all flights with optional filters
     * @param array $filters Search filters
     * @param int $limit Result limit
     * @param int $offset Pagination offset
     * @return array|false Flights or false on failure
     */
    public function getAll($filters = [], $limit = 10, $offset = 0) {
        try {
            // Select specific columns and JOIN with airlines
            $select = "SELECT f.id, f.airline_id, f.flight_number, f.departure_city, f.arrival_city, 
                           f.departure_time, f.arrival_time, f.price, f.stops, f.available_seats, 
                           f.created_at, 
                           a.name as airline_name, a.logo_image as airline_logo";
            $from = " FROM flights f LEFT JOIN airlines a ON f.airline_id = a.id";
            $where = " WHERE 1=1";
            $params = [];
            
            // Apply filters (adjust filter keys if needed)
            if (!empty($filters['departure_city'])) {
                $where .= " AND f.departure_city LIKE :departure_city";
                $params[':departure_city'] = '%' . $filters['departure_city'] . '%';
            }
            
            if (!empty($filters['arrival_city'])) {
                $where .= " AND f.arrival_city LIKE :arrival_city";
                $params[':arrival_city'] = '%' . $filters['arrival_city'] . '%';
            }
            
            if (!empty($filters['departure_date'])) {
                $where .= " AND DATE(f.departure_time) = :departure_date";
                $params[':departure_date'] = $filters['departure_date'];
            }
            
            if (isset($filters['max_price'])) {
                $where .= " AND f.price <= :max_price";
                $params[':max_price'] = $filters['max_price'];
            }
            
            if (isset($filters['min_seats'])) {
                $where .= " AND f.available_seats >= :min_seats";
                $params[':min_seats'] = $filters['min_seats'];
            }
            
            // Add sorting
            $orderBy = " ORDER BY f.departure_time ASC";
            
            // Add pagination
            $limitOffset = " LIMIT :limit OFFSET :offset";
            $params[':limit'] = (int)$limit;
            $params[':offset'] = (int)$offset;
            
            $sql = $select . $from . $where . $orderBy . $limitOffset;
            
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
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    
    /**
     * Count total flights with filters
     * @param array $filters Search filters
     * @return int|false Count or false on failure
     */
    public function countAll($filters = []) {
        try {
            // Basic count for now, add filters/joins if needed for admin filtering
            $sql = "SELECT COUNT(*) FROM flights";
            $stmt = $this->db->query($sql);
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    
    /**
     * Create new flight
     * @param array $data Flight data
     * @return int|false Flight ID or false on failure
     */
    public function create($data) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO flights (
                    airline_id, flight_number, departure_city, arrival_city, 
                    departure_time, arrival_time, price, stops, available_seats
                ) VALUES (
                    :airline_id, :flight_number, :departure_city, :arrival_city,
                    :departure_time, :arrival_time, :price, :stops, :available_seats
                )
            ");
            
            // Bind using airline_id now
            $stmt->bindParam(':airline_id', $data['airline_id'], PDO::PARAM_INT);
            $stmt->bindParam(':flight_number', $data['flight_number'], PDO::PARAM_STR);
            $stmt->bindParam(':departure_city', $data['departure_city'], PDO::PARAM_STR);
            $stmt->bindParam(':arrival_city', $data['arrival_city'], PDO::PARAM_STR);
            $stmt->bindParam(':departure_time', $data['departure_time'], PDO::PARAM_STR);
            $stmt->bindParam(':arrival_time', $data['arrival_time'], PDO::PARAM_STR);
            $stmt->bindParam(':price', $data['price'], PDO::PARAM_STR);
            $stmt->bindParam(':stops', $data['stops'], PDO::PARAM_INT);
            $stmt->bindParam(':available_seats', $data['available_seats'], PDO::PARAM_INT);
            
            $stmt->execute();
            return $this->db->lastInsertId();
            
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    
    /**
     * Update flight
     * @param int $id Flight ID
     * @param array $data Flight data
     * @return bool Success status
     */
    public function update($id, $data) {
        try {
            $sql = "UPDATE flights SET ";
            $updates = [];
            $params = [':id' => $id];
            
            // Include airline_id in allowed fields
            $allowedFields = [
                'airline_id', 'flight_number', 'departure_city', 'arrival_city',
                'departure_time', 'arrival_time', 'price', 'stops', 'available_seats'
            ];
            
            foreach ($allowedFields as $field) {
                 if (isset($data[$field])) {
                    $updates[] = "$field = :$field";
                    // Determine param type (simplified)
                    $paramType = (in_array($field, ['airline_id', 'stops', 'available_seats'])) ? PDO::PARAM_INT : PDO::PARAM_STR;
                    if ($field === 'price') $paramType = PDO::PARAM_STR; // Or PARAM_DECIMAL
                    
                    $params[":$field"] = $data[$field]; 
                }
            }

            if (empty($updates)) {
                return false; // Nothing to update
            }

            $sql .= implode(", ", $updates) . " WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            
            // Bind parameters with determined types (more robust binding)
            foreach ($params as $key => &$value) {
                $paramType = PDO::PARAM_STR;
                if ($key === ':id' || $key === ':airline_id' || $key === ':stops' || $key === ':available_seats') {
                     $paramType = PDO::PARAM_INT;
                }
                // Note: Price might need special handling depending on DB type & PDO driver
                 $stmt->bindParam($key, $value, $paramType);
            }
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete flight
     * @param int $id Flight ID
     * @return bool Success status
     */
    public function delete($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM flights WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    
    /**
     * Update available seats
     * @param int $id Flight ID
     * @param int $seatCount Number of seats to reduce
     * @return bool Success status
     */
    public function updateSeats($id, $seatCount = 1) {
        try {
            $stmt = $this->db->prepare("
                UPDATE flights 
                SET available_seats = available_seats - :seat_count 
                WHERE id = :id AND available_seats >= :seat_count
            ");
            
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':seat_count', $seatCount, PDO::PARAM_INT);
            
            return $stmt->execute() && $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
}
?>