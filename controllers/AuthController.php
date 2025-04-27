<?php
require_once 'controllers/BaseController.php';

class AuthController extends BaseController {
    private $userModel;
    protected $db;
    
    public function __construct() {
        require_once 'models/User.php';
        require_once 'config/config.php';
        $this->userModel = new User();
        $this->db = getDBConnection();
    }
    
    /**
     * Display login form
     */
    public function loginForm() {
        $this->view('auth/login');
    }
    
    /**
     * Display register form
     */
    public function registerForm() {
        $this->view('auth/register');
    }
    
    /**
     * Process login
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/login');
        }
        
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

  
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];
            
            setFlashMessage('Bon retour !');
            redirect('/dashboard');
        } else {
            setFlashMessage('Email ou mot de passe invalide', 'danger');
            redirect('/login');
        }
    }
    
    /**
     * Process registration
     */
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/register');
        }
        
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        // Validate input
        if (empty($name) || empty($email) || empty($password)) {
            setFlashMessage('Tous les champs sont obligatoires', 'danger');
            redirect('/register');
        }
        
        // Check if email exists
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            setFlashMessage('Cet email existe déjà', 'danger');
            redirect('/register');
        }
        
        // Create user
        $stmt = $this->db->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->execute([
            $name,
            $email,
            password_hash($password, PASSWORD_DEFAULT)
        ]);
        
        setFlashMessage('Inscription réussie ! Veuillez vous connecter.');
        redirect('/login');
    }
    
    /**
     * Process logout
     */
    public function logout() {
        session_destroy();
        redirect('/login');
    }
}
?>