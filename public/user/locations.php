<?php
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../classes/Database.php';
require_once '../../classes/User.php';
require_once '../../classes/Location.php';

$user = new User();
$location = new Location();

// Check if logged in
if (!$user->isLoggedIn()) {
    header('Location: ../login.php');
    exit();
}

$locations = $location->getAvailableLocations();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Available Locations - EasyEV-Charging</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1>Available Charging Locations</h1>
        
        <a href="dashboard.php">Back to Dashboard</a>
        
        <div class="location-grid">
            <?php foreach ($locations as $loc): ?>
            <div class="location-card">
                <h3><?php echo htmlspecialchars($loc['description']); ?></h3>
                <p>Available Stations: <?php echo $loc['available_stations']; ?>/<?php echo $loc['number_of_stations']; ?></p>
                <p>Cost: $<?php echo number_format($loc['cost_per_hour'], 2); ?>/hour</p>
                <a href="check-in.php?location_id=<?php echo $loc['location_id']; ?>" class="btn">Check-in Here</a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>