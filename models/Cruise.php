<?php
class Cruise {
    private $db;
    
    public function __construct() {
        $this->db = dbConnect();
    }
    
    /**
     * Get cruise by ID
     * @param int $id Cruise ID
     * @return array|false Cruise data or false if not found
     */
    public function getById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM cruises WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            $cruise = $stmt->fetch();
            
            if ($cruise && isset($cruise['destination_ports'])) {
                $cruise['destination_ports'] = array_map('trim', explode(',', $cruise['destination_ports']));
            }
            
            return $cruise;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all cruises with optional filters
     * @param array $filters Search filters
     * @param int $limit Result limit
     * @param int $offset Pagination offset
     * @return array|false Cruises or false on failure
     */
    public function getAll($filters = [], $limit = 10, $offset = 0) {
        try {
            $sql = "SELECT * FROM cruises WHERE 1=1";
            $params = [];
            
            // Apply filters
            if (!empty($filters['departure_port'])) {
                $sql .= " AND departure_port LIKE :departure_port";
                $params[':departure_port'] = '%' . $filters['departure_port'] . '%';
            }
            
            if (!empty($filters['name'])) {
                $sql .= " AND name LIKE :name";
                $params[':name'] = '%' . $filters['name'] . '%';
            }
            
            if (!empty($filters['departure_date'])) {
                $sql .= " AND DATE(departure_date) >= :departure_date";
                $params[':departure_date'] = $filters['departure_date'];
            }
            
            if (isset($filters['max_price'])) {
                $sql .= " AND price <= :max_price";
                $params[':max_price'] = $filters['max_price'];
            }
            
            if (isset($filters['min_cabins'])) {
                $sql .= " AND available_cabins >= :min_cabins";
                $params[':min_cabins'] = $filters['min_cabins'];
            }
            
            // Filter by destination port if provided
            if (!empty($filters['destination_port'])) {
                $sql = "
                    SELECT c.* FROM cruises c
                    JOIN cruise_destinations cd ON c.id = cd.cruise_id
                    WHERE cd.port_name LIKE :destination_port
                ";
                $params[':destination_port'] = '%' . $filters['destination_port'] . '%';
                
                // Re-apply other filters
                if (!empty($filters['departure_port'])) {
                    $sql .= " AND c.departure_port LIKE :departure_port";
                }
                
                if (!empty($filters['name'])) {
                    $sql .= " AND c.name LIKE :name";
                }
                
                if (!empty($filters['departure_date'])) {
                    $sql .= " AND DATE(c.departure_date) >= :departure_date";
                }
                
                if (isset($filters['max_price'])) {
                    $sql .= " AND c.price <= :max_price";
                }
                
                if (isset($filters['min_cabins'])) {
                    $sql .= " AND c.available_cabins >= :min_cabins";
                }
                
                $sql .= " GROUP BY c.id";
            }
            
            // Add sorting
            $sql .= " ORDER BY departure_date ASC";
            
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
            $cruises = $stmt->fetchAll();
            
            // Convert destination_ports string to array for each cruise
            foreach ($cruises as &$cruise) {
                if (isset($cruise['destination_ports'])) {
                    $cruise['destination_ports'] = array_map('trim', explode(',', $cruise['destination_ports']));
                }
            }
            
            return $cruises;
            
        } catch (PDOException $e) {
            error_log('Cruise Query Error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Count total cruises with filters
     * @param array $filters Search filters
     * @return int|false Count or false on failure
     */
    public function countAll($filters = []) {
        try {
            $sql = "SELECT COUNT(*) FROM cruises c"; // Alias for potential joins
            $params = [];
            $joins = '';

            // Apply destination port filter join if necessary
            if (!empty($filters['destination_port'])) {
                $joins = " JOIN cruise_destinations cd ON c.id = cd.cruise_id";
                $sql = "SELECT COUNT(DISTINCT c.id) FROM cruises c" . $joins; // Use COUNT(DISTINCT)
            }
            
            $sql .= " WHERE 1=1";

            // Apply filters
            if (!empty($filters['departure_port'])) {
                $sql .= " AND c.departure_port LIKE :departure_port";
                $params[':departure_port'] = '%' . $filters['departure_port'] . '%';
            }
            
            if (!empty($filters['name'])) {
                $sql .= " AND c.name LIKE :name";
                $params[':name'] = '%' . $filters['name'] . '%';
            }
            
            if (!empty($filters['departure_date'])) {
                $sql .= " AND DATE(c.departure_date) >= :departure_date";
                $params[':departure_date'] = $filters['departure_date'];
            }
            
            if (isset($filters['max_price'])) {
                $sql .= " AND c.price <= :max_price";
                $params[':max_price'] = $filters['max_price'];
            }
            
            if (isset($filters['min_cabins'])) {
                $sql .= " AND c.available_cabins >= :min_cabins";
                $params[':min_cabins'] = $filters['min_cabins'];
            }
            
             // Apply destination port filter condition
            if (!empty($filters['destination_port'])) {
                $sql .= " AND cd.port_name LIKE :destination_port";
                 $params[':destination_port'] = '%' . $filters['destination_port'] . '%';
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
     * Create new cruise
     * @param array $data Cruise data
     * @return int|false Cruise ID or false on failure
     */
    public function create($data) {
        try {
            $this->db->beginTransaction();
            
            $stmt = $this->db->prepare("
                INSERT INTO cruises (
                    name, departure_port, destination_ports, departure_date, duration_days, price, available_cabins, cruise_line, image, return_date
                ) VALUES (
                    :name, :departure_port, :destination_ports, :departure_date, :duration_days, :price, :available_cabins, :cruise_line, :image, :return_date
                )
            ");
            
            $stmt->bindParam(':name', $data['name'], PDO::PARAM_STR);
            $stmt->bindParam(':departure_port', $data['departure_port'], PDO::PARAM_STR);
            $stmt->bindParam(':destination_ports', $data['destination_ports'], PDO::PARAM_STR);
            $stmt->bindParam(':departure_date', $data['departure_date'], PDO::PARAM_STR);
            $stmt->bindParam(':duration_days', $data['duration_days'], PDO::PARAM_INT);
            $stmt->bindParam(':price', $data['price'], PDO::PARAM_STR);
            $stmt->bindParam(':available_cabins', $data['available_cabins'], PDO::PARAM_INT);
            $stmt->bindParam(':cruise_line', $data['cruise_line'], PDO::PARAM_STR);
            $stmt->bindParam(':image', $data['image'], PDO::PARAM_STR);
            $stmt->bindParam(':return_date', $data['return_date'], PDO::PARAM_STR);
            
            $stmt->execute();
            $cruiseId = $this->db->lastInsertId();
            
            $this->db->commit();
            return $cruiseId;
            
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }
    
    /**
     * Update cruise
     * @param int $id Cruise ID
     * @param array $data Cruise data
     * @return bool Success status
     */
    public function update($id, $data) {
        try {
            $this->db->beginTransaction();
            
            $sql = "UPDATE cruises SET ";
            $updates = [];
            $params = [':id' => $id];
            
            $allowedFields = [
                'name', 'departure_port', 'departure_date', 'price', 'available_cabins', 'image'
            ];
            
            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $updates[] = "$field = :$field";
                    $params[":$field"] = $data[$field];
                }
            }
            
            if (!empty($updates)) {
                $sql .= implode(', ', $updates) . " WHERE id = :id";
                $stmt = $this->db->prepare($sql);
                $stmt->execute($params);
            }
            
            // Update destination ports if provided
            if (isset($data['destination_ports']) && is_array($data['destination_ports'])) {
                // Delete existing ports
                $deleteStmt = $this->db->prepare("DELETE FROM cruise_destinations WHERE cruise_id = :cruise_id");
                $deleteStmt->bindParam(':cruise_id', $id, PDO::PARAM_INT);
                $deleteStmt->execute();
                
                // Insert new ports
                $order = 1;
                $portStmt = $this->db->prepare("
                    INSERT INTO cruise_destinations (cruise_id, port_name, port_order)
                    VALUES (:cruise_id, :port_name, :port_order)
                ");
                
                foreach ($data['destination_ports'] as $port) {
                    $portStmt->bindParam(':cruise_id', $id, PDO::PARAM_INT);
                    $portStmt->bindParam(':port_name', $port, PDO::PARAM_STR);
                    $portStmt->bindParam(':port_order', $order, PDO::PARAM_INT);
                    $portStmt->execute();
                    $order++;
                }
            }
            
            $this->db->commit();
            return true;
            
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete cruise
     * @param int $id Cruise ID
     * @return bool Success status
     */
    public function delete($id) {
        try {
            $this->db->beginTransaction();

            // Only delete from cruises table
            $deleteCruiseStmt = $this->db->prepare("DELETE FROM cruises WHERE id = :id");
            $deleteCruiseStmt->bindParam(':id', $id, PDO::PARAM_INT);
            $deleteCruiseStmt->execute();

            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }
    
    /**
     * Update available cabins
     * @param int $id Cruise ID
     * @param int $cabinCount Number of cabins to reduce
     * @return bool Success status
     */
    public function updateCabins($id, $cabinCount = 1) {
        try {
            $stmt = $this->db->prepare("
                UPDATE cruises 
                SET available_cabins = available_cabins - :cabin_count 
                WHERE id = :id AND available_cabins >= :cabin_count
            ");
            
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':cabin_count', $cabinCount, PDO::PARAM_INT);
            
            return $stmt->execute() && $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
}
?>