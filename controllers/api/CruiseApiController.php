<?php
require_once 'controllers/BaseController.php';

class CruiseApiController extends BaseController {
    public function index() {
        $search = $_GET['search'] ?? '';
        $departureDate = $_GET['departure_date'] ?? '';
        
        $query = "SELECT * FROM cruises WHERE 1=1";
        $params = [];
        
        if ($search) {
            $query .= " AND (name LIKE ? OR destination LIKE ? OR departure_port LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        if ($departureDate) {
            $query .= " AND departure_date >= ? AND available_cabins > 0";
            $params[] = $departureDate;
        }
        
        $query .= " ORDER BY departure_date ASC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $cruises = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $this->json($cruises);
    }
    
    public function show($id) {
        $stmt = $this->db->prepare("SELECT * FROM cruises WHERE id = ?");
        $stmt->execute([$id]);
        $cruise = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$cruise) {
            $this->json(['error' => 'Cruise not found'], 404);
        }
        
        $this->json($cruise);
    }
} 