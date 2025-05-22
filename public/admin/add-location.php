<?php
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../classes/Database.php';
require_once '../../classes/User.php';
require_once '../../classes/Location.php';

$user = new User();
$location = new Location();
$message = '';

// Check if admin
if (!$user->isLoggedIn() || !$user->isAdmin()) {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $description = $_POST['description'];
    $numberOfStations = $_POST['number_of_stations'];
    $costPerHour = $_POST['cost_per_hour'];
    
    $result = $location->addLocation($description, $numberOfStations, $costPerHour);
    $message = $result['message'];
    
    if ($result['success']) {
        header('Location: locations.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Location - EasyEV-Charging</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
  <div class="container">
    <h1>Add new Location</h1>
    <?php if($message): ?>
      <div class="alert"><?php echo $message ?></div>
    <?php endif; ?>

    <form method="POST">
      <input type="text" name="description" placeholder="Location Description" required>
      <input type="number" name="number_of_stations" placeholder="Number of Stations" min="1" required>
      <input type="number" name="cost_per_hour" placeholder="Cost per Hour" step="0.01" min="0.01" required>
      <button type="submit">Add Location</button>
    </form>
    <a href="locations.php">Back to Locations</a>
  </div>
</body>
</html>