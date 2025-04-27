<?php
require_once 'controllers/BaseController.php'; 
require_once 'models/Booking.php';

class AdminBookingController extends BaseController {
    private $bookingModel;

    public function __construct() {
        parent::__construct();
        $this->bookingModel = new Booking();
    }

    // List all bookings
    public function index() {
        // Fetch all bookings (adjust limit as needed, e.g., add pagination later)
        $bookings = $this->bookingModel->getAll([], 200, 0); // Get up to 200 bookings for now
        
        $this->view('admin/bookings/index', [
            'pageTitle' => 'Gérer les Réservations',
            'current_page' => 'bookings',
            'bookings' => $bookings
        ]);
    }

    // Cancel a booking
    public function cancel($id) {
        require_once 'models/Booking.php';
        $bookingModel = new Booking();
        if ($bookingModel->updateStatus($id, 'cancelled')) {
            setFlashMessage('Réservation annulée avec succès.', 'success');
        } else {
            setFlashMessage('Erreur lors de l\'annulation de la réservation.', 'danger');
        }
        redirect('/admin/bookings');
    }

    // TODO: Add methods for viewing details, changing status, or deleting if needed
}
?> 