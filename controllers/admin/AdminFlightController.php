<?php
require_once 'controllers/BaseController.php'; 
require_once 'models/Flight.php';
require_once 'models/Airline.php'; // Require the Airline model

class AdminFlightController extends BaseController {
    private $flightModel;
    private $airlineModel; // Add airline model property

    public function __construct() {
        parent::__construct();
        $this->flightModel = new Flight();
        $this->airlineModel = new Airline(); // Instantiate airline model
    }

    // List all flights
    public function index() {
        $limit = 10; // Items per page
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($currentPage < 1) $currentPage = 1;
        $offset = ($currentPage - 1) * $limit;

        // Get total count for pagination
        $totalFlights = $this->flightModel->countAll(); // Assuming countAll exists and works
        $totalPages = ceil($totalFlights / $limit);

        // Fetch paginated flights
        $flights = $this->flightModel->getAll([], $limit, $offset);

        $this->view('admin/flights/index', [
            'pageTitle' => 'Gérer les Vols',
            'current_page' => 'flights',
            'flights' => $flights,
            'totalPages' => $totalPages,
            'currentPage' => $currentPage
        ]);
    }

    // Show form to create a new flight
    public function create() {
        $airlines = $this->airlineModel->getAll(); // Fetch airlines for dropdown
        $this->view('admin/flights/form', [
            'pageTitle' => 'Ajouter un Vol',
            'current_page' => 'flights',
            'flight' => null, 
            'airlines' => $airlines, // Pass airlines to the view
            'action' => '/admin/flights/store',
            'form_method' => 'POST'
        ]);
    }

    // Store a new flight in the database
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/admin/flights');
        }
        
        $data = [
            'airline_id' => $_POST['airline_id'] ?? null, // Use airline_id
            'flight_number' => $_POST['flight_number'] ?? '',
            'departure_city' => $_POST['departure_city'] ?? '',
            'arrival_city' => $_POST['arrival_city'] ?? '',
            'departure_time' => $_POST['departure_time'] ?? null,
            'arrival_time' => $_POST['arrival_time'] ?? null,
            'price' => $_POST['price'] ?? 0,
            'stops' => $_POST['stops'] ?? 0,
            'available_seats' => $_POST['available_seats'] ?? 0
        ];

        // Add validation for airline_id
        if (empty($data['airline_id']) || empty($data['flight_number']) || empty($data['departure_city']) || empty($data['arrival_city'])) {
             setFlashMessage('Veuillez remplir tous les champs obligatoires (Compagnie, N° Vol, Villes).', 'danger');
             redirect('/admin/flights/create'); // Consider repopulating form
             return;
        }

        if ($this->flightModel->create($data)) {
            setFlashMessage('Vol ajouté avec succès.', 'success');
        } else {
            setFlashMessage('Erreur lors de l\'ajout du vol.', 'danger');
        }
        redirect('/admin/flights');
    }

    // Show form to edit an existing flight
    public function edit($id) {
        $flight = $this->flightModel->getById($id); // Fetches joined data
        if (!$flight) {
            setFlashMessage('Vol non trouvé.', 'danger');
            redirect('/admin/flights');
            return;
        }
        $airlines = $this->airlineModel->getAll(); // Fetch airlines for dropdown

        $this->view('admin/flights/form', [
            'pageTitle' => 'Modifier le Vol',
            'current_page' => 'flights',
            'flight' => $flight,
            'airlines' => $airlines, // Pass airlines to the view
            'action' => '/admin/flights/update/' . $id,
            'form_method' => 'POST' 
        ]);
    }

    // Update an existing flight in the database
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/admin/flights');
        }

        $flight = $this->flightModel->getById($id); // Check if flight exists
        if (!$flight) {
            setFlashMessage('Vol non trouvé.', 'danger');
            redirect('/admin/flights');
            return;
        }

        $data = [
             'airline_id' => $_POST['airline_id'] ?? null, // Use airline_id
            'flight_number' => $_POST['flight_number'] ?? '',
            'departure_city' => $_POST['departure_city'] ?? '',
            'arrival_city' => $_POST['arrival_city'] ?? '',
            'departure_time' => $_POST['departure_time'] ?? null,
            'arrival_time' => $_POST['arrival_time'] ?? null,
            'price' => $_POST['price'] ?? 0,
            'stops' => $_POST['stops'] ?? 0,
            'available_seats' => $_POST['available_seats'] ?? 0
        ];

         // Add validation for airline_id
        if (empty($data['airline_id']) || empty($data['flight_number']) || empty($data['departure_city']) || empty($data['arrival_city'])) {
             setFlashMessage('Veuillez remplir tous les champs obligatoires (Compagnie, N° Vol, Villes).', 'danger');
             redirect('/admin/flights/edit/' . $id); // Redirect back to edit form
             return;
        }

        if ($this->flightModel->update($id, $data)) {
            setFlashMessage('Vol mis à jour avec succès.', 'success');
        } else {
            setFlashMessage('Erreur lors de la mise à jour du vol.', 'danger');
        }
        redirect('/admin/flights');
    }

    // Delete a flight
    public function destroy($id) {
        // Add confirmation step in a real application (e.g., via POST or JS confirmation)
        $flight = $this->flightModel->getById($id);
        if (!$flight) {
            setFlashMessage('Vol non trouvé.', 'danger');
            redirect('/admin/flights');
            return;
        }
        
        if ($this->flightModel->delete($id)) {
            setFlashMessage('Vol supprimé avec succès.', 'success');
        } else {
            setFlashMessage('Erreur lors de la suppression du vol.', 'danger');
        }
        redirect('/admin/flights');
    }
} 