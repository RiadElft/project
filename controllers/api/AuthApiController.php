<?php
class AuthApiController {
    private $userModel;
    
    public function __construct() {
        require_once 'models/User.php';
        $this->userModel = new User();
    }
    
    /**
     * Process API login
     */
    public function login() {
        // Set content type
        header('Content-Type: application/json');
        
        // Check request method
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed. Use POST.']);
            exit;
        }
        
        // Get JSON input
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid JSON data.']);
            exit;
        }
        
        // Validate input
        $email = isset($data['email']) ? sanitize($data['email']) : '';
        $password = isset($data['password']) ? $data['password'] : '';
        
        if (empty($email) || empty($password)) {
            http_response_code(400);
            echo json_encode(['error' => 'Email and password are required.']);
            exit;
        }
        
        if (!validateEmail($email)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid email format.']);
            exit;
        }
        
        // Check if user exists
        $user = $this->userModel->getByEmail($email);
        
        if (!$user || !verifyPassword($password, $user['password'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid credentials.']);
            exit;
        }
        
        // Login successful
        $payload = [
            'user_id' => $user['id'],
            'email' => $user['email'],
            'role' => $user['role']
        ];
        
        $token = generateJWT($payload);
        
        // Return user data (excluding password) and token
        unset($user['password']);
        
        echo json_encode([
            'user' => $user,
            'token' => $token
        ]);
    }
    
    /**
     * Process API registration
     */
    public function register() {
        // Set content type
        header('Content-Type: application/json');
        
        // Check request method
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed. Use POST.']);
            exit;
        }
        
        // Get JSON input
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid JSON data.']);
            exit;
        }
        
        // Validate input
        $name = isset($data['name']) ? sanitize($data['name']) : '';
        $email = isset($data['email']) ? sanitize($data['email']) : '';
        $password = isset($data['password']) ? $data['password'] : '';
        $passwordConfirm = isset($data['password_confirm']) ? $data['password_confirm'] : '';
        
        $errors = [];
        
        if (empty($name)) {
            $errors[] = 'Name is required.';
        }
        
        if (empty($email)) {
            $errors[] = 'Email is required.';
        } elseif (!validateEmail($email)) {
            $errors[] = 'Please enter a valid email address.';
        }
        
        if (empty($password)) {
            $errors[] = 'Password is required.';
        } elseif (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters.';
        }
        
        if ($password !== $passwordConfirm) {
            $errors[] = 'Passwords do not match.';
        }
        
        // Check if email already exists
        $existingUser = $this->userModel->getByEmail($email);
        if ($existingUser) {
            $errors[] = 'Email is already registered.';
        }
        
        // If there are errors, return them
        if (!empty($errors)) {
            http_response_code(400);
            echo json_encode(['errors' => $errors]);
            exit;
        }
        
        // Create user
        $userData = [
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'role' => 'client' // Default role
        ];
        
        $userId = $this->userModel->create($userData);
        
        if (!$userId) {
            http_response_code(500);
            echo json_encode(['error' => 'Registration failed. Please try again.']);
            exit;
        }
        
        // Registration successful
        http_response_code(201); // Created
        echo json_encode([
            'success' => true,
            'message' => 'Registration successful! You can now log in.'
        ]);
    }
}
?>