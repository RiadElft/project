<?php
require_once 'controllers/BaseController.php'; 
require_once 'models/Cruise.php';

class AdminCruiseController extends BaseController
{
    private $cruiseModel;
    private $uploadDir = 'public/images/cruises/'; // Define upload directory
    private $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    private $maxSize = 2 * 1024 * 1024; // 2 MB

    public function __construct()
    {
        parent::__construct();
        $this->cruiseModel = new Cruise();
        // Ensure upload directory exists
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
    }

    public function index()
    {
        $cruises = $this->cruiseModel->getAll([], 100, 0); // Fetch first 100
        $this->view('admin/cruises/index', [
            'pageTitle' => 'Gérer les Croisières',
            'current_page' => 'cruises',
            'cruises' => $cruises
        ]);
    }

    public function create()
    {
        $this->view('admin/cruises/form', [
            'pageTitle' => 'Ajouter une Croisière',
            'current_page' => 'cruises',
            'cruise' => null, 
            'action' => '/admin/cruises/store',
            'form_method' => 'POST'
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/admin/cruises');
        }
        
        // Basic data retrieval
        $destinationPortsRaw = $_POST['destination_ports'] ?? '';
        $destinationPortsArray = array_filter(array_map('trim', explode(',', $destinationPortsRaw)));
        $data = [
            'name' => $_POST['name'] ?? '',
            'cruise_line' => $_POST['cruise_line'] ?? '',
            'ship_name' => $_POST['ship_name'] ?? '',
            'departure_port' => $_POST['departure_port'] ?? '',
            'destination_ports' => $destinationPortsRaw, // for main table
            'destination_ports_array' => $destinationPortsArray, // for cruise_destinations
            'duration_days' => $_POST['duration_days'] ?? 0,
            'departure_date' => $_POST['departure_date'] ?? null,
            'price' => $_POST['price'] ?? 0,
            'available_cabins' => $_POST['available_cabins'] ?? 0,
            'description' => $_POST['description'] ?? '',
            'status' => $_POST['status'] ?? 'active'
        ];

        // Basic validation
        if (empty($data['name']) || empty($data['departure_port']) || empty($data['duration_days']) || empty($data['price'])) {
             setFlashMessage('Veuillez remplir les champs obligatoires (Nom, Port Départ, Durée, Prix).', 'danger');
             redirect('/admin/cruises/create');
             return;
        }

        // Handle image upload
        $uploadResult = $this->handleImageUpload();
        if ($uploadResult['error']) {
            setFlashMessage($uploadResult['error'], 'danger');
            redirect('/admin/cruises/create');
            return;
        }
        $data['image'] = $uploadResult['filename'];

        if ($this->cruiseModel->create($data)) {
            setFlashMessage('Croisière ajoutée avec succès.', 'success');
        } else {
            setFlashMessage('Erreur lors de l\'ajout de la croisière.', 'danger');
            if (!empty($data['image'])) {
                unlink($this->uploadDir . $data['image']);
            }
        }
        redirect('/admin/cruises');
    }

    public function edit($id)
    {
        $cruise = $this->cruiseModel->getById($id);
        if (!$cruise) {
            setFlashMessage('Croisière non trouvée.', 'danger');
            redirect('/admin/cruises');
            return;
        }

        $this->view('admin/cruises/form', [
            'pageTitle' => 'Modifier la Croisière',
            'current_page' => 'cruises',
            'cruise' => $cruise,
            'action' => '/admin/cruises/update/' . $id,
            'form_method' => 'POST' 
        ]);
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/admin/cruises');
        }

        $cruise = $this->cruiseModel->getById($id); 
        if (!$cruise) {
            setFlashMessage('Croisière non trouvée.', 'danger');
            redirect('/admin/cruises');
            return;
        }

        $data = [
            'name' => $_POST['name'] ?? '',
            'cruise_line' => $_POST['cruise_line'] ?? '',
            'ship_name' => $_POST['ship_name'] ?? '',
            'departure_port' => $_POST['departure_port'] ?? '',
            'destination_ports' => $_POST['destination_ports'] ?? '',
            'duration_days' => $_POST['duration_days'] ?? 0,
            'departure_date' => $_POST['departure_date'] ?? null,
            'price' => $_POST['price'] ?? 0,
            'available_cabins' => $_POST['available_cabins'] ?? 0,
            'description' => $_POST['description'] ?? '',
            'status' => $_POST['status'] ?? 'active'
        ];
        $currentImage = $_POST['current_image'] ?? null;

         if (empty($data['name']) || empty($data['departure_port']) || empty($data['duration_days']) || empty($data['price'])) {
             setFlashMessage('Veuillez remplir les champs obligatoires (Nom, Port Départ, Durée, Prix).', 'danger');
             redirect('/admin/cruises/edit/' . $id);
             return;
        }

        // Handle image upload
        $newImageFilename = null;
        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] !== UPLOAD_ERR_NO_FILE) {
             $uploadResult = $this->handleImageUpload();
             if ($uploadResult['error']) {
                 setFlashMessage($uploadResult['error'], 'danger');
                 redirect('/admin/cruises/edit/' . $id);
                 return;
             }
             $newImageFilename = $uploadResult['filename'];
             $data['image'] = $newImageFilename;
        } else {
            $data['image'] = $currentImage; 
        }

        if ($this->cruiseModel->update($id, $data)) {
            setFlashMessage('Croisière mise à jour avec succès.', 'success');
            if ($newImageFilename && $currentImage && $newImageFilename !== $currentImage) {
                 $oldFilePath = $this->uploadDir . $currentImage;
                 if (file_exists($oldFilePath)) {
                     unlink($oldFilePath);
                 }
            }
        } else {
            setFlashMessage('Erreur lors de la mise à jour de la croisière.', 'danger');
             if ($newImageFilename) {
                 unlink($this->uploadDir . $newImageFilename);
             }
        }
        redirect('/admin/cruises');
    }

    public function delete($id)
    {
        $cruise = $this->cruiseModel->getById($id);
        if (!$cruise) {
            setFlashMessage('Croisière non trouvée.', 'danger');
            redirect('/admin/cruises');
            return;
        }
        
        $imageToDelete = $cruise['image'];

        if ($this->cruiseModel->delete($id)) {
            setFlashMessage('Croisière supprimée avec succès.', 'success');
            if ($imageToDelete) {
                $filePath = $this->uploadDir . $imageToDelete;
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
        } else {
            setFlashMessage('Erreur lors de la suppression de la croisière.', 'danger');
        }
        redirect('/admin/cruises');
    }

    private function handleImageUpload()
    {
        if (!isset($_FILES['image_file']) || $_FILES['image_file']['error'] === UPLOAD_ERR_NO_FILE) {
            return ['filename' => null, 'error' => null]; 
        }
        $file = $_FILES['image_file'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['filename' => null, 'error' => 'Erreur upload (code: ' . $file['error'] . ').'];
        }
        $fileType = mime_content_type($file['tmp_name']);
        if (!in_array($fileType, $this->allowedTypes)) {
            return ['filename' => null, 'error' => 'Type de fichier non autorisé.'];
        }
        if ($file['size'] > $this->maxSize) {
            return ['filename' => null, 'error' => 'Fichier trop volumineux (Max 2MB).'];
        }
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('cruise_', true) . '.' . strtolower($extension);
        $targetPath = $this->uploadDir . $filename;
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return ['filename' => $filename, 'error' => null];
        } else {
            return ['filename' => null, 'error' => 'Échec déplacement fichier.'];
        }
    }
} 