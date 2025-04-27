<?php
require_once 'config/config.php'; // For dbConnect

class Airline {
    private $db;

    public function __construct() {
        $this->db = dbConnect(); 
    }

    /**
     * Get all airlines (e.g., for dropdowns)
     * @param array $options Optional parameters like limit, offset, order_by
     * @return array|false List of airlines or false on failure
     */
    public function getAll($options = []) {
        try {
            $sql = "SELECT * FROM airlines";
            
            // Basic ordering
            $orderBy = isset($options['order_by']) ? $options['order_by'] : 'name';
            $orderDir = isset($options['order_dir']) ? $options['order_dir'] : 'ASC';
            $sql .= " ORDER BY " . $orderBy . " " . $orderDir;

            // Pagination
            if (isset($options['limit'])) {
                 $sql .= " LIMIT :limit";
                 if (isset($options['offset'])) {
                     $sql .= " OFFSET :offset";
                 }
            }

            $stmt = $this->db->prepare($sql);

            if (isset($options['limit'])) {
                 $stmt->bindParam(':limit', $options['limit'], PDO::PARAM_INT);
                 if (isset($options['offset'])) {
                     $stmt->bindParam(':offset', $options['offset'], PDO::PARAM_INT);
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
     * Get airline by ID
     * @param int $id Airline ID
     * @return array|false Airline data or false if not found
     */
    public function getById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM airlines WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Count all airlines
     * @return int|false Count or false on failure
     */
    public function countAll() {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) FROM airlines");
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Create new airline
     * @param array $data Airline data (name, logo_image, description)
     * @return int|false Airline ID or false on failure
     */
    public function create($data) {
        try {
            $sql = "INSERT INTO airlines (name, logo_image, description) VALUES (:name, :logo_image, :description)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':name', $data['name'], PDO::PARAM_STR);
            $stmt->bindParam(':logo_image', $data['logo_image'], PDO::PARAM_STR);
            $stmt->bindParam(':description', $data['description'], PDO::PARAM_STR);
            $stmt->execute();
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Update airline
     * @param int $id Airline ID
     * @param array $data Airline data
     * @return bool Success status
     */
    public function update($id, $data) {
        try {
            $sql = "UPDATE airlines SET name = :name, logo_image = :logo_image, description = :description WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':name', $data['name'], PDO::PARAM_STR);
            $stmt->bindParam(':logo_image', $data['logo_image'], PDO::PARAM_STR);
            $stmt->bindParam(':description', $data['description'], PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Delete airline
     * @param int $id Airline ID
     * @return bool Success status
     */
    public function delete($id) {
        try {
            // Consider potential foreign key constraints (e.g., flights.airline_id)
            // The current constraint is ON DELETE SET NULL, so this should be safe.
            // If it was RESTRICT, you might need to check for associated flights first.
            $stmt = $this->db->prepare("DELETE FROM airlines WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    // Add create, update, delete methods here if needed for admin management

} 