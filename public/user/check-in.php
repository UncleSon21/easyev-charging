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

// Check if logged in
if (!$user->isLoggedIn()) {
    header('Location: ../login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$locationId = isset($_GET['location_id']) ? $_GET['location_id'] : 0;

if ($locationId > 0) {
    // Get location details
    $locationData = $location->getLocationById($locationId);
    
    if (!$locationData) {
        header('Location: locations.php');
        exit();
    }
    
    // Perform check-in
    $result = $charging->checkIn($userId, $locationId);
    $message = $result['message'];
    
    if ($result['success']) {
        $checkInTime = $result['check_in_time'];
        $costPerHour = $result['cost_per_hour'];
    }
} else {
    header('Location: locations.php');
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Check-in - EasyEV-Charging</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1>Charging Check-in</h1>
        
        <?php if ($result['success']): ?>
            <div class="success-message">
                <h2>Check-in Successful!</h2>
                <p>Location: <?php echo htmlspecialchars($locationData['description']); ?></p>
                <p>Check-in Time: <?php echo $checkInTime; ?></p>
                <p>Cost per Hour: $<?php echo number_format($costPerHour, 2); ?></p>
                <p>Your charging session has started. Don't forget to check-out when you're finished.</p>
                <a href="dashboard.php" class="btn">Go to Dashboard</a>
            </div>
        <?php else: ?>
            <div class="alert">
                <p><?php echo $message; ?></p>
                <a href="locations.php" class="btn">Back to Locations</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>