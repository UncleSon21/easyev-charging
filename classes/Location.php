<?php
class Location{
  private $db;
  public function __construct(){
    $this->db = new Database();
  }

  public function addLocation($description, $numberOfStations, $costPerHour){
    $conn = $this->db->getConnection();
    $stmt = $conn->prepare("INSERT INTO locations (description, number_of_stations, cost_per_hour) VALUES (?, ?, ?)");
    $stmt->bind_param("sid", $description, $numberOfStations, $costPerHour);

    if($stmt->execute()){
      return ['success' => true, 'message' => 'Location added successfully'];
    } else{
      return ['success' => false, 'message' => 'Failed to add location'];
    }
  }
  public function updateLocation($locationId, $description, $numberOfStations, $costPerHour){
    $conn = $this->db->getConnection();
    $stmt = $conn->prepare("UPDATE locations SET description = ?, number_of_stations = ?, cost_per_hour = ? WHERE location_id=?");
    $stmt->bind_param("sidi", $description, $numberOfStations, $costPerHour, $locationId);

    if($stmt->execute()){
      return ['success' => true, 'message' => 'Location update successfully'];
    } else{
      return ['success' => false, 'message' => 'Failed to update location'];
    }
  }
  public function getAllLocations(){
    $conn = $this->db->getConnection();
    $result = $conn-query("SELECT * FROM locations ORDER BY description");
    return $result->fetch_all(MYSQLI_ASSOC);
  }
  public function getLocationById($locationId){
    $conn = $this->db->getConnection();
    $stmt = $conn->prepare("SELECT * FROM locations WHERE location_id = ?");
    $stmt->bind_param("i", $locationId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
  }
  public function getAvailableLocations(){
    $conn = $this->db->getConnection();
    $sql = "SELECT l.*,
            (l.number_of_stations - COALESCE(COUNT(c.id), 0) as available_stations
            FROM locations l
            LEFT JOIN chargings c ON l.location_id = c.location_id AND c.status = 'active'
            GROUP BY l.location_id
            HAVING available_stations > 0";
    $result = $conn->query($sql);
    return $result->fetch_all(MSQLI_ASSOC);
  }
  public function searchLocations($searchTerm){
    $conn = $this->db->getConnection();
    $searchTerm = "%" . $searchTerm . "%";
    $stmt = $conn->prepare("SELECT * FROM locations WHERE description LIKE ? OR location_id LIKE ?");
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
  }
}