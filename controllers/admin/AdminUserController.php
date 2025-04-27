<?php
require_once 'controllers/BaseController.php'; 
require_once 'models/User.php';

class AdminUserController extends BaseController {
    private $userModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
    }

    // List all users
    public function index() {
        $users = $this->userModel->getAll(); // Assuming getAll exists
        $this->view('admin/users/index', [
            'pageTitle' => 'Gérer les Utilisateurs',
            'current_page' => 'users',
            'users' => $users
        ]);
    }

    // Show form to edit a user (e.g., change role)
    public function edit($id) {
        $user = $this->userModel->getById($id);
        if (!$user) {
            setFlashMessage('Utilisateur non trouvé.', 'danger');
            redirect('/admin/users');
            return;
        }

        // Remove password hash from data passed to view
        unset($user['password']);

        $this->view('admin/users/form', [
            'pageTitle' => 'Modifier l\'Utilisateur',
            'current_page' => 'users',
            'user' => $user,
            'action' => '/admin/users/update/' . $id,
            'form_method' => 'POST' 
        ]);
    }

    // Update user data (e.g., role)
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/admin/users');
        }

        $user = $this->userModel->getById($id); 
        if (!$user) {
            setFlashMessage('Utilisateur non trouvé.', 'danger');
            redirect('/admin/users');
            return;
        }

        $data = [
            'name' => $_POST['name'] ?? $user['name'], // Keep old if not provided
            'email' => $_POST['email'] ?? $user['email'],
            'role' => $_POST['role'] ?? $user['role']
            // Password update should be handled separately and securely
        ];

        // Basic validation
        if (empty($data['name']) || empty($data['email']) || !in_array($data['role'], ['admin', 'client'])) {
             setFlashMessage('Données invalides (Nom, Email, Rôle).', 'danger');
             redirect('/admin/users/edit/' . $id);
             return;
        }

        if ($this->userModel->update($id, $data)) {
            setFlashMessage('Utilisateur mis à jour avec succès.', 'success');
        } else {
            setFlashMessage('Erreur lors de la mise à jour.', 'danger');
        }
        redirect('/admin/users');
    }

    // Delete a user
    public function destroy($id) {
        // Prevent deleting the currently logged-in admin?
        if (isset($_SESSION['user_id']) && $id == $_SESSION['user_id']) {
            setFlashMessage('Vous ne pouvez pas supprimer votre propre compte administrateur.', 'danger');
            redirect('/admin/users');
            return;
        }
        
        $user = $this->userModel->getById($id);
        if (!$user) {
            setFlashMessage('Utilisateur non trouvé.', 'danger');
            redirect('/admin/users');
            return;
        }

        if ($this->userModel->delete($id)) {
            setFlashMessage('Utilisateur supprimé avec succès.', 'success');
        } else {
            setFlashMessage('Erreur lors de la suppression.', 'danger');
        }
        redirect('/admin/users');
    }
}
?> 