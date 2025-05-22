<?php
class Charging {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    public function checkIn($userId, $locationId) {
        $conn = $this->db->getConnection();
        
        // Check if user already has an active charging
        if ($this->hasActiveCharging($userId)) {
            return ['success' => false, 'message' => 'You already have an active charging session'];
        }
        
        // Check if location has available stations
        $location = new Location();
        $locationDetails = $location->getLocationById($locationId);
        
        if (!$locationDetails) {
            return ['success' => false, 'message' => 'Invalid location'];
        }
        
        // Get count of active charges at this location
        $stmt = $conn->prepare("SELECT COUNT(*) as active_count FROM chargings WHERE location_id = ? AND status = 'active'");
        $stmt->bind_param("i", $locationId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        if ($row['active_count'] >= $locationDetails['number_of_stations']) {
            return ['success' => false, 'message' => 'No available stations at this location'];
        }
        
        // Perform check-in
        $checkInTime = date('Y-m-d H:i:s');
        $stmt = $conn->prepare("INSERT INTO chargings (user_id, location_id, check_in_time, status) VALUES (?, ?, ?, 'active')");
        $stmt->bind_param("iis", $userId, $locationId, $checkInTime);
        
        if ($stmt->execute()) {
            return [
                'success' => true, 
                'message' => 'Check-in successful',
                'check_in_time' => $checkInTime,
                'cost_per_hour' => $locationDetails['cost_per_hour']
            ];
        } else {
            return ['success' => false, 'message' => 'Check-in failed'];
        }
    }
    
    public function checkOut($chargingId, $userId = null) {
        $conn = $this->db->getConnection();
        
        // Get charging details
        $stmt = $conn->prepare("SELECT c.*, l.cost_per_hour 
                                FROM chargings c 
                                JOIN locations l ON c.location_id = l.location_id 
                                WHERE c.id = ? AND c.status = 'active'");
        $stmt->bind_param("i", $chargingId);
        $stmt->execute();
        $result = $stmt->get_result();
        $charging = $result->fetch_assoc();
        
        if (!$charging) {
            return ['success' => false, 'message' => 'Invalid charging session'];
        }
        
        // If userId provided, verify it matches
        if ($userId !== null && $charging['user_id'] != $userId) {
            return ['success' => false, 'message' => 'Unauthorized'];
        }
        
        // Calculate total cost
        $checkOutTime = date('Y-m-d H:i:s');
        $checkInTime = strtotime($charging['check_in_time']);
        $duration = time() - $checkInTime;
        $hours = $duration / 3600; // Convert seconds to hours
        $totalCost = round($hours * $charging['cost_per_hour'], 2);
        
        // Update charging record
        $stmt = $conn->prepare("UPDATE chargings SET check_out_time = ?, total_cost = ?, status = 'completed' WHERE id = ?");
        $stmt->bind_param("sdi", $checkOutTime, $totalCost, $chargingId);
        
        if ($stmt->execute()) {
            return [
                'success' => true,
                'message' => 'Check-out successful',
                'total_cost' => $totalCost,
                'duration_hours' => round($hours, 2)
            ];
        } else {
            return ['success' => false, 'message' => 'Check-out failed'];
        }
    }
    
    public function getActiveChargingByUser($userId) {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("SELECT c.*, l.description as location_name, l.cost_per_hour 
                                FROM chargings c 
                                JOIN locations l ON c.location_id = l.location_id 
                                WHERE c.user_id = ? AND c.status = 'active'");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    public function hasActiveCharging($userId) {
        return $this->getActiveChargingByUser($userId) ? true : false;
    }
    
    public function getUserChargingHistory($userId) {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("SELECT c.*, l.description as location_name 
                                FROM chargings c 
                                JOIN locations l ON c.location_id = l.location_id 
                                WHERE c.user_id = ? AND c.status = 'completed' 
                                ORDER BY c.check_out_time DESC");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getAllActiveChargings() {
        $conn = $this->db->getConnection();
        $result = $conn->query("SELECT c.*, u.name as user_name, l.description as location_name 
                              FROM chargings c 
                              JOIN users u ON c.user_id = u.id 
                              JOIN locations l ON c.location_id = l.location_id 
                              WHERE c.status = 'active'");
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>