<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../classes/Database.php';
require_once '../classes/User.php';

$user = new User();
$message = '';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
  $name = $_POST['name'];
  $phone = $_POST['phone'];
  $email = $_POST['email'];
  $password = $_POST['password'];
  $type = $_POST['type'];

  $result = $user->register($name, $phone, $email, $password, $type);
  $message = $result['message'];

  if($result['success']){
    header('Location: login.php');
    exit();
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Register - EasyEV-Charging</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <div class="Container">
    <h2>Register</h2>
    <?php if($message): ?>
      <div class="alert"><?php echo $message; ?></div>
    <?php endif; ?>
    <form method="POST">
      <input type="text" name="name" placeholder="Name" required><br>
      <input type="text" name="phone" placeholder="Phone" required><br>
      <input type="email" name="email" placeholder="Email" required><br>
      <input type="password" name="password" placeholder="Password" required><br>
      <select name="type" required>
          <option value="">Select Type</option>
          <option value="User">User</option>
          <option value="Administrator">Administrator</option>
      </select><br>
      <button type="submit">Register</button>
    </form>
    <p>already have an account? <a href="login.php">Login here</a>
  </div>
</body>
</html>