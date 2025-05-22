<?php
class User{
  private $db;
  public function __construct(){
    $this->db = new Database();
  }
  public function register($name, $phone, $email, $password, $type){
    // check if email exists already
    $conn = $this->db->getConnection();
    $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){
      return ['success' => false, 'message' => 'Email already exists'];
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (name, phone, email, password, type) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $phone, $email, $hashedPassword, $type);

    if($stmt->execute()){
      return ['success' => true, 'message' => 'Registration successful'];
    } else {
      return ['success' => false, 'message' => 'Registration failed'];
    }
  }
  public function login($email, $password){
    $conn = $this->db->getConnection();
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows == 0){
      return ['success' => false, 'message' => 'Invalid credentials'];
    }
    $user = $result->fetch_assoc();

    if (password_verify($password, $user['password'])){
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['user_name'] = $user['name'];
      $_SESSION['user_type'] = $user['type'];
      return ['success' => true, 'user_type' => $user['type']];
    } else{
      return ['success' => false, 'message' => 'Invalid credentials'];
    }
  }

  public function isLoggedIn(){
    return isset($_SESSION['user_id']);
  }

  public function isAdmin(){
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'Administrator';
  }

  public function logout(){
    session_destroy();
  }
}
?>