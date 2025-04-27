<?php
class User {
    private $db;
    
    public function __construct() {
        $this->db = dbConnect();
    }
    
    /**
     * Get user by ID
     * @param int $id User ID
     * @return array|false User data or false if not found
     */
    public function getById($id) {
        try {
            $stmt = $this->db->prepare("SELECT id, email, name, role, created_at FROM users WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch();
        } catch (PDOException $e) {
            // Log error
            error_log($e->getMessage());
            return false;
        }
    }
    
    /**
     * Get user by email
     * @param string $email User email
     * @return array|false User data or false if not found
     */
    public function getByEmail($email) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            
            return $stmt->fetch();
        } catch (PDOException $e) {
            // Log error
            error_log($e->getMessage());
            return false;
        }
    }
    
    /**
     * Create new user
     * @param array $data User data
     * @return int|false User ID or false on failure
     */
    public function create($data) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO users (name, email, password, role, created_at) 
                VALUES (:name, :email, :password, :role, NOW())
            ");
            
            $hashedPassword = hashPassword($data['password']);
            $role = isset($data['role']) ? $data['role'] : 'client';
            
            $stmt->bindParam(':name', $data['name'], PDO::PARAM_STR);
            $stmt->bindParam(':email', $data['email'], PDO::PARAM_STR);
            $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
            $stmt->bindParam(':role', $role, PDO::PARAM_STR);
            
            $stmt->execute();
            return $this->db->lastInsertId();
            
        } catch (PDOException $e) {
            // Log error
            error_log($e->getMessage());
            return false;
        }
    }
    
    /**
     * Update user
     * @param int $id User ID
     * @param array $data User data to update
     * @return bool Success status
     */
    public function update($id, $data) {
        try {
            $setFields = [];
            $params = [':id' => $id];
            
            // Dynamically build update query based on provided data
            foreach ($data as $key => $value) {
                if ($key !== 'id' && $key !== 'password') {
                    $setFields[] = "$key = :$key";
                    $params[":$key"] = $value;
                }
            }
            
            // Handle password separately if provided
            if (isset($data['password']) && !empty($data['password'])) {
                $setFields[] = "password = :password";
                $params[':password'] = hashPassword($data['password']);
            }
            
            if (empty($setFields)) {
                return false; // Nothing to update
            }
            
            $sql = "UPDATE users SET " . implode(", ", $setFields) . " WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute($params);
            
        } catch (PDOException $e) {
            // Log error
            error_log($e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete user
     * @param int $id User ID
     * @return bool Success status
     */
    public function delete($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            // Log error
            error_log($e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all users
     * @param int $limit Limit results
     * @param int $offset Offset for pagination
     * @return array|false Users or false on failure
     */
    public function getAll($limit = 10, $offset = 0) {
        try {
            $stmt = $this->db->prepare("
                SELECT id, email, name, role, created_at 
                FROM users 
                ORDER BY created_at DESC 
                LIMIT :limit OFFSET :offset
            ");
            
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            // Log error
            error_log($e->getMessage());
            return false;
        }
    }
    
    /**
     * Count total users
     * @return int|false Count or false on failure
     */
    public function countAll() {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) FROM users");
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            // Log error
            error_log($e->getMessage());
            return false;
        }
    }
}
?>