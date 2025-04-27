<?php
// Ensure BaseController and Hotel model are required
require_once 'controllers/BaseController.php'; 
require_once 'models/Hotel.php';

class AdminHotelController extends BaseController
{
    private $hotelModel;
    private $uploadDir = 'public/images/hotels/'; // Define upload directory
    private $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    private $maxSize = 2 * 1024 * 1024; // 2 MB

    public function __construct()
    {
        parent::__construct();
        $this->hotelModel = new Hotel();
        // Ensure upload directory exists
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
    }

    public function index()
    {
        $hotels = $this->hotelModel->getAll([], 100, 0); // Fetch first 100 hotels
        $this->view('admin/hotels/index', [
            'pageTitle' => 'Gérer les Hôtels',
            'current_page' => 'hotels',
            'hotels' => $hotels
        ]);
    }

    public function create()
    {
        $this->view('admin/hotels/form', [
            'pageTitle' => 'Ajouter un Hôtel',
            'current_page' => 'hotels',
            'hotel' => null, 
            'action' => '/admin/hotels/store',
            'form_method' => 'POST'
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/admin/hotels');
        }

        // Verify CSRF token
        if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
            setFlashMessage('Invalid CSRF token.', 'danger');
            redirect('/admin/hotels/create');
            return;
        }
        
        // Basic data retrieval
        $data = [
            'name' => $_POST['name'] ?? '',
            'city' => $_POST['city'] ?? '',
            'country' => $_POST['country'] ?? '',
            'description' => $_POST['description'] ?? '',
            'price_per_night' => $_POST['price_per_night'] ?? 0,
            'rating' => $_POST['rating'] ?? 3,
            'available_rooms' => $_POST['available_rooms'] ?? 0,
            'status' => $_POST['status'] ?? 'active',
            'amenities' => $_POST['amenities'] ?? '',
            'room_types' => $_POST['room_types'] ?? ''
        ];

        // Basic validation
        if (empty($data['name']) || empty($data['city']) || empty($data['price_per_night'])) {
             setFlashMessage('Veuillez remplir les champs obligatoires (Nom, Ville, Prix).', 'danger');
             redirect('/admin/hotels/create');
             return;
        }

        // Handle image upload
        $uploadResult = $this->handleImageUpload();
        if ($uploadResult['error']) {
            setFlashMessage($uploadResult['error'], 'danger');
            redirect('/admin/hotels/create');
            return;
        }
        $data['image'] = $uploadResult['filename']; // Filename or null

        try {
            if ($this->hotelModel->create($data)) {
                setFlashMessage('Hôtel ajouté avec succès.', 'success');
                redirect('/admin/hotels');
                return;
            }
        } catch (Exception $e) {
            error_log('Error creating hotel: ' . $e->getMessage());
            if (!empty($data['image'])) {
                unlink($this->uploadDir . $data['image']);
            }
        }

        setFlashMessage('Erreur lors de l\'ajout de l\'hôtel.', 'danger');
        redirect('/admin/hotels/create');
    }

    public function edit($id)
    {
        $hotel = $this->hotelModel->getById($id);
        if (!$hotel) {
            setFlashMessage('Hôtel non trouvé.', 'danger');
            redirect('/admin/hotels');
            return;
        }

        $this->view('admin/hotels/form', [
            'pageTitle' => 'Modifier l\'Hôtel',
            'current_page' => 'hotels',
            'hotel' => $hotel,
            'action' => '/admin/hotels/update/' . $id,
            'form_method' => 'POST' 
        ]);
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/admin/hotels');
        }

        $hotel = $this->hotelModel->getById($id); 
        if (!$hotel) {
            setFlashMessage('Hôtel non trouvé.', 'danger');
            redirect('/admin/hotels');
            return;
        }

        $data = [
            'name' => $_POST['name'] ?? '',
            'city' => $_POST['city'] ?? '',
            'country' => $_POST['country'] ?? '',
            'description' => $_POST['description'] ?? '',
            'price_per_night' => $_POST['price_per_night'] ?? 0,
            'rating' => $_POST['rating'] ?? 3,
            'available_rooms' => $_POST['available_rooms'] ?? 0,
            'status' => $_POST['status'] ?? 'active',
            'amenities' => $_POST['amenities'] ?? '',
            'room_types' => $_POST['room_types'] ?? ''
        ];
        $currentImage = $_POST['current_image'] ?? null;

        if (empty($data['name']) || empty($data['city']) || empty($data['price_per_night'])) {
             setFlashMessage('Veuillez remplir les champs obligatoires (Nom, Ville, Prix).', 'danger');
             redirect('/admin/hotels/edit/' . $id);
             return;
        }

        // Handle potential new image upload
        $newImageFilename = null;
        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] !== UPLOAD_ERR_NO_FILE) {
             $uploadResult = $this->handleImageUpload();
             if ($uploadResult['error']) {
                 setFlashMessage($uploadResult['error'], 'danger');
                 redirect('/admin/hotels/edit/' . $id);
                 return;
             }
             $newImageFilename = $uploadResult['filename'];
             $data['image'] = $newImageFilename;
        } else {
            $data['image'] = $currentImage; // Keep current if no new file
        }

        if ($this->hotelModel->update($id, $data)) {
            setFlashMessage('Hôtel mis à jour avec succès.', 'success');
            // Delete old image if new one uploaded
            if ($newImageFilename && $currentImage && $newImageFilename !== $currentImage) {
                 $oldFilePath = $this->uploadDir . $currentImage;
                 if (file_exists($oldFilePath)) {
                     unlink($oldFilePath);
                 }
            }
        } else {
            setFlashMessage('Erreur lors de la mise à jour de l\'hôtel.', 'danger');
             if ($newImageFilename) {
                 unlink($this->uploadDir . $newImageFilename);
             }
        }
        redirect('/admin/hotels');
    }

    public function destroy($id)
    {
        $hotel = $this->hotelModel->getById($id);
        if (!$hotel) {
            setFlashMessage('Hôtel non trouvé.', 'danger');
            redirect('/admin/hotels');
            return;
        }
        
        $imageToDelete = $hotel['image'];

        if ($this->hotelModel->delete($id)) {
            setFlashMessage('Hôtel supprimé avec succès.', 'success');
            if ($imageToDelete) {
                $filePath = $this->uploadDir . $imageToDelete;
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
        } else {
            setFlashMessage('Erreur lors de la suppression de l\'hôtel.', 'danger');
        }
        redirect('/admin/hotels');
    }

    // Reusable image upload handler
    private function handleImageUpload() {
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
        $filename = uniqid('hotel_', true) . '.' . strtolower($extension);
        $targetPath = $this->uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return ['filename' => $filename, 'error' => null];
        }

        return ['filename' => null, 'error' => 'Échec déplacement fichier.'];
    }
} 