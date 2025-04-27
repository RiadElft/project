<?php
class Hotel {
    private $db;
    
    public function __construct() {
        $this->db = dbConnect();
    }
    
    /**
     * Get hotel by ID
     * @param int $id Hotel ID
     * @return array|false Hotel data or false if not found
     */
    public function getById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM hotels WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all hotels with optional filters
     * @param array $filters Search filters
     * @param int $limit Result limit
     * @param int $offset Pagination offset
     * @return array|false Hotels or false on failure
     */
    public function getAll($filters = [], $limit = 10, $offset = 0) {
        try {
            $sql = "SELECT * FROM hotels WHERE 1=1";
            $params = [];
            
            // Apply filters
            if (!empty($filters['city'])) {
                $sql .= " AND city LIKE :city";
                $params[':city'] = '%' . $filters['city'] . '%';
            }
            if (!empty($filters['country'])) {
                $sql .= " AND country LIKE :country";
                $params[':country'] = '%' . $filters['country'] . '%';
            }
            if (!empty($filters['name'])) {
                $sql .= " AND name LIKE :name";
                $params[':name'] = '%' . $filters['name'] . '%';
            }
            if (isset($filters['min_rating'])) {
                $sql .= " AND rating >= :min_rating";
                $params[':min_rating'] = $filters['min_rating'];
            }
            if (isset($filters['max_price'])) {
                $sql .= " AND price_per_night <= :max_price";
                $params[':max_price'] = $filters['max_price'];
            }
            if (isset($filters['min_rooms'])) {
                $sql .= " AND available_rooms >= :min_rooms";
                $params[':min_rooms'] = $filters['min_rooms'];
            }
            // Add sorting
            $sql .= " ORDER BY price_per_night ASC";
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
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    
    /**
     * Count total hotels with filters
     * @param array $filters Search filters
     * @return int|false Count or false on failure
     */
    public function countAll($filters = []) {
        try {
            $sql = "SELECT COUNT(*) FROM hotels WHERE 1=1";
            $params = [];
            if (!empty($filters['city'])) {
                $sql .= " AND city LIKE :city";
                $params[':city'] = '%' . $filters['city'] . '%';
            }
            if (!empty($filters['country'])) {
                $sql .= " AND country LIKE :country";
                $params[':country'] = '%' . $filters['country'] . '%';
            }
            if (!empty($filters['name'])) {
                $sql .= " AND name LIKE :name";
                $params[':name'] = '%' . $filters['name'] . '%';
            }
            if (isset($filters['min_rating'])) {
                $sql .= " AND rating >= :min_rating";
                $params[':min_rating'] = $filters['min_rating'];
            }
            if (isset($filters['max_price'])) {
                $sql .= " AND price_per_night <= :max_price";
                $params[':max_price'] = $filters['max_price'];
            }
            if (isset($filters['min_rooms'])) {
                $sql .= " AND available_rooms >= :min_rooms";
                $params[':min_rooms'] = $filters['min_rooms'];
            }
            $stmt = $this->db->prepare($sql);
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
     * Create new hotel
     * @param array $data Hotel data
     * @return int|false Hotel ID or false on failure
     */
    public function create($data) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO hotels (
                    name, city, country, description, price_per_night, rating, 
                    available_rooms, image, status, amenities, room_types
                ) VALUES (
                    :name, :city, :country, :description, :price_per_night, :rating,
                    :available_rooms, :image, :status, :amenities, :room_types
                )
            ");
            $stmt->bindParam(':name', $data['name'], PDO::PARAM_STR);
            $stmt->bindParam(':city', $data['city'], PDO::PARAM_STR);
            $stmt->bindParam(':country', $data['country'], PDO::PARAM_STR);
            $stmt->bindParam(':description', $data['description'], PDO::PARAM_STR);
            $stmt->bindParam(':price_per_night', $data['price_per_night'], PDO::PARAM_STR);
            $stmt->bindParam(':rating', $data['rating'], PDO::PARAM_INT);
            $stmt->bindParam(':available_rooms', $data['available_rooms'], PDO::PARAM_INT);
            $stmt->bindParam(':image', $data['image'], PDO::PARAM_STR);
            $stmt->bindParam(':status', $data['status'], PDO::PARAM_STR);
            $stmt->bindParam(':amenities', $data['amenities'], PDO::PARAM_STR);
            $stmt->bindParam(':room_types', $data['room_types'], PDO::PARAM_STR);
            $stmt->execute();
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    
    /**
     * Update hotel
     * @param int $id Hotel ID
     * @param array $data Hotel data
     * @return bool Success status
     */
    public function update($id, $data) {
        try {
            $sql = "UPDATE hotels SET ";
            $updates = [];
            $params = [':id' => $id];
            
            $allowedFields = [
                'name', 'city', 'country', 'price_per_night', 'rating', 'available_rooms', 'image'
            ];
            
            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $updates[] = "$field = :$field";
                    $params[":$field"] = $data[$field];
                }
            }
            
            if (empty($updates)) {
                return false; // Nothing to update
            }
            
            $sql .= implode(', ', $updates) . " WHERE id = :id";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
            
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete hotel
     * @param int $id Hotel ID
     * @return bool Success status
     */
    public function delete($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM hotels WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    
    /**
     * Update available rooms
     * @param int $id Hotel ID
     * @param int $roomCount Number of rooms to reduce
     * @return bool Success status
     */
    public function updateRooms($id, $roomCount = 1) {
        try {
            $stmt = $this->db->prepare("
                UPDATE hotels 
                SET available_rooms = available_rooms - :room_count 
                WHERE id = :id AND available_rooms >= :room_count
            ");
            
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':room_count', $roomCount, PDO::PARAM_INT);
            
            return $stmt->execute() && $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
}
?>