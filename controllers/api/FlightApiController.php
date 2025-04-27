<?php
require_once 'controllers/BaseController.php';

class FlightApiController extends BaseController {
    public function index() {
        $search = $_GET['search'] ?? '';
        $date = $_GET['date'] ?? '';
        
        $query = "SELECT * FROM flights WHERE 1=1";
        $params = [];
        
        if ($search) {
            $query .= " AND (departure_city LIKE ? OR arrival_city LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        if ($date) {
            $query .= " AND DATE(departure_time) = ?";
            $params[] = $date;
        }
        
        $query .= " ORDER BY departure_time ASC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $flights = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $this->json($flights);
    }
    
    public function show($id) {
        $stmt = $this->db->prepare("SELECT * FROM flights WHERE id = ?");
        $stmt->execute([$id]);
        $flight = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$flight) {
            $this->json(['error' => 'Flight not found'], 404);
        }
        
        $this->json($flight);
    }
} 