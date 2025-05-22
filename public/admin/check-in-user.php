<?php
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../classes/Database.php';
require_once '../../classes/User.php';
require_once '../../classes/Location.php';
require_once '../../classes/Charging.php';

$user = new User();
$location = new Location();
$charging = new Charging();
$message = '';

if(!$user->isLoggedIn() || !$user->isAdmint()){
  header('Location: ../login.php');
  exit();
}

$allUsers = $user->getAllUsers();
$availableLocations = $location->getAvailableLocations();

if($_SERVER['REQUEST_METHOD'] == 'POST'){
  $userId = $_POST['user_id'];
  $locationId = $_POST['location_id'];

  $result = $charging->checkIn($userId, $locationId);
  $message = $result['message'];
  if($result['success']){
    header('Location: dashboard.php');
    exit();
  }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Check-in User - EasyEV-Charging</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
  <div class="container">
    <h1>Check-in User</h1>
    <?php if($message): ?>
      <div class="alert"><?php echo $message; ?></div>
    <?php endif; ?>
    <form method="POST">
      <div class="form-group">
        <label>Select User:</label>
        <select name="user_id" required>
          <option value="">-- Select User --</option>
          <?php foreach ($allUsers as $userOption): ?>
            <?php
            if ($charging->hasActiveChargin($userOption['id'])) continue;
            ?>
            <option value="<?php echo $userOption['id']; ?>">
              <?php echo htmlspecialchars($userOption['name']. ' (' . $userOption['email'] . ')');?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label>Select Location:</label>
        <select name="location_id" required>
            <option value="">-- Select Location --</option>
            <?php foreach ($availableLocations as $loc): ?>
              <option value="<?php echo $loc['location_id']; ?>">
                <?php echo htmlspecialchars($loc['description'] . ' (Available: '. $loc['available_stations'] . ')'); ?>
              </option>
            <?php endforeach; ?>
        </select>
      </div>
      <button type="submit" class="btn">Check-in User</button>
      <a href="dashboard.php" class="btn">Cancel</a>
    </form>
  </div>
</body>
</html>