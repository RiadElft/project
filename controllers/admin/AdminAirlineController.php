<?php
require_once 'controllers/BaseController.php'; 
require_once 'models/Airline.php';

class AdminAirlineController extends BaseController {
    private $airlineModel;
    private $uploadDir = 'public/images/airlines/'; // Define upload directory
    private $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    private $maxSize = 2 * 1024 * 1024; // 2 MB

    public function __construct() {
        parent::__construct();
        $this->airlineModel = new Airline();
        // Ensure upload directory exists
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
    }

    // List all airlines
    public function index() {
        $airlines = $this->airlineModel->getAll(['limit' => 100]); // Get first 100
        $this->view('admin/airlines/index', [
            'pageTitle' => 'Gérer les Compagnies Aériennes',
            'current_page' => 'airlines', // For sidebar highlighting
            'airlines' => $airlines
        ]);
    }

    // Show form to create a new airline
    public function create() {
        $this->view('admin/airlines/form', [
            'pageTitle' => 'Ajouter une Compagnie Aérienne',
            'current_page' => 'airlines',
            'airline' => null, 
            'action' => '/admin/airlines/store',
            'form_method' => 'POST'
        ]);
    }

    // Store a new airline
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/admin/airlines');
        }
        
        $data = [
            'name' => $_POST['name'] ?? '',
            'description' => $_POST['description'] ?? ''
            // logo_image will be handled by file upload
        ];

        if (empty($data['name'])) {
             setFlashMessage('Le nom de la compagnie est obligatoire.', 'danger');
             redirect('/admin/airlines/create');
             return;
        }

        // Handle file upload
        $uploadResult = $this->handleLogoUpload();
        if ($uploadResult['error']) {
            setFlashMessage($uploadResult['error'], 'danger');
            redirect('/admin/airlines/create');
            return;
        }
        $data['logo_image'] = $uploadResult['filename']; // May be null if no file uploaded

        if ($this->airlineModel->create($data)) {
            setFlashMessage('Compagnie ajoutée avec succès.', 'success');
        } else {
            setFlashMessage('Erreur lors de l\'ajout de la compagnie.', 'danger');
            // Optionally delete uploaded file if DB insert fails
            if (!empty($data['logo_image'])) {
                unlink($this->uploadDir . $data['logo_image']);
            }
        }
        redirect('/admin/airlines');
    }

    // Show form to edit an airline
    public function edit($id) {
        $airline = $this->airlineModel->getById($id);
        if (!$airline) {
            setFlashMessage('Compagnie non trouvée.', 'danger');
            redirect('/admin/airlines');
            return;
        }

        $this->view('admin/airlines/form', [
            'pageTitle' => 'Modifier la Compagnie Aérienne',
            'current_page' => 'airlines',
            'airline' => $airline,
            'action' => '/admin/airlines/update/' . $id,
            'form_method' => 'POST' 
        ]);
    }

    // Update an existing airline
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/admin/airlines');
        }

        $airline = $this->airlineModel->getById($id); 
        if (!$airline) {
            setFlashMessage('Compagnie non trouvée.', 'danger');
            redirect('/admin/airlines');
            return;
        }

        $data = [
            'name' => $_POST['name'] ?? '',
            'description' => $_POST['description'] ?? ''
        ];
        $currentLogo = $_POST['current_logo_image'] ?? null;

        if (empty($data['name'])) {
             setFlashMessage('Le nom de la compagnie est obligatoire.', 'danger');
             redirect('/admin/airlines/edit/' . $id);
             return;
        }

        // Handle file upload (only if a new file is provided)
        $newLogoFilename = null;
        if (isset($_FILES['logo_file']) && $_FILES['logo_file']['error'] !== UPLOAD_ERR_NO_FILE) {
             $uploadResult = $this->handleLogoUpload();
             if ($uploadResult['error']) {
                 setFlashMessage($uploadResult['error'], 'danger');
                 redirect('/admin/airlines/edit/' . $id);
                 return;
             }
             $newLogoFilename = $uploadResult['filename'];
             $data['logo_image'] = $newLogoFilename;
        } else {
            // No new file uploaded, keep the current one
            $data['logo_image'] = $currentLogo; 
        }

        if ($this->airlineModel->update($id, $data)) {
            setFlashMessage('Compagnie mise à jour avec succès.', 'success');
            // Delete old logo if a new one was uploaded successfully
            if ($newLogoFilename && $currentLogo && $newLogoFilename !== $currentLogo) {
                 $oldFilePath = $this->uploadDir . $currentLogo;
                 if (file_exists($oldFilePath)) {
                     unlink($oldFilePath);
                 }
            }
        } else {
            setFlashMessage('Erreur lors de la mise à jour de la compagnie.', 'danger');
            // Optionally delete newly uploaded file if DB update fails
             if ($newLogoFilename) {
                 unlink($this->uploadDir . $newLogoFilename);
             }
        }
        redirect('/admin/airlines');
    }

    // Delete an airline
    public function destroy($id) {
        $airline = $this->airlineModel->getById($id);
        if (!$airline) {
            setFlashMessage('Compagnie non trouvée.', 'danger');
            redirect('/admin/airlines');
            return;
        }
        
        $logoToDelete = $airline['logo_image'];

        if ($this->airlineModel->delete($id)) {
            setFlashMessage('Compagnie supprimée avec succès.', 'success');
            // Delete logo file if it exists
            if ($logoToDelete) {
                $filePath = $this->uploadDir . $logoToDelete;
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
        } else {
            setFlashMessage('Erreur lors de la suppression de la compagnie.', 'danger');
        }
        redirect('/admin/airlines');
    }

    /**
     * Handle logo file upload
     * @return array ['filename' => string|null, 'error' => string|null]
     */
    private function handleLogoUpload() {
        if (!isset($_FILES['logo_file']) || $_FILES['logo_file']['error'] === UPLOAD_ERR_NO_FILE) {
            return ['filename' => null, 'error' => null]; // No file uploaded
        }

        $file = $_FILES['logo_file'];

        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['filename' => null, 'error' => 'Erreur lors du téléchargement du fichier (code: ' . $file['error'] . ').'];
        }

        // Validate file type
        $fileType = mime_content_type($file['tmp_name']);
        if (!in_array($fileType, $this->allowedTypes)) {
            return ['filename' => null, 'error' => 'Type de fichier non autorisé (' . $fileType . '). Types autorisés: JPG, PNG, GIF, WebP.'];
        }

        // Validate file size
        if ($file['size'] > $this->maxSize) {
            return ['filename' => null, 'error' => 'Le fichier est trop volumineux (Max: ' . ($this->maxSize / 1024 / 1024) . 'MB).'];
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('logo_', true) . '.' . strtolower($extension);
        $targetPath = $this->uploadDir . $filename;

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return ['filename' => $filename, 'error' => null];
        } else {
            return ['filename' => null, 'error' => 'Échec du déplacement du fichier téléchargé.'];
        }
    }
} 