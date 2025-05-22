<?php
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../classes/Database.php';
require_once '../../classes/User.php';
require_once '../../classes/Location.php';
require_once '../../classes/Charging.php';

$user = new User();
$charging = new Charging();

// Check if logged in
if (!$user->isLoggedIn()) {
    header('Location: ../login.php');
    exit();
}

// Redirect admin to admin dashboard
if ($user->isAdmin()) {
    header('Location: ../admin/dashboard.php');
    exit();
}

$userId = $_SESSION['user_id'];
$activeCharging = $charging->getActiveChargingByUser($userId);
$chargingHistory = $charging->getUserChargingHistory($userId);
?>
<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard - EasyEV-Charging</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1>User Dashboard</h1>
        <p>Welcome, <?php echo $_SESSION['user_name']; ?>!</p>
        
        <nav class="main-nav">
            <a href="locations.php">View Locations</a>
            <a href="history.php">View History</a>
            <a href="../logout.php">Logout</a>
        </nav>
        
        <?php if ($activeCharging): ?>
            <div class="active-charging">
                <h2>Current Charging Session</h2>
                <p>Location: <?php echo htmlspecialchars($activeCharging['location_name']); ?></p>
                <p>Check-in Time: <?php echo $activeCharging['check_in_time']; ?></p>
                
                <?php
                // Calculate current duration and cost
                $checkInTime = strtotime($activeCharging['check_in_time']);
                $currentDuration = time() - $checkInTime;
                $currentHours = $currentDuration / 3600;
                $currentCost = round($currentHours * $activeCharging['cost_per_hour'], 2);
                ?>
                
                <p>Current Duration: <?php echo round($currentHours, 2); ?> hours</p>
                <p>Current Cost: $<?php echo number_format($currentCost, 2); ?></p>
                <a href="check-out.php" class="btn">Check-out</a>
            </div>
        <?php else: ?>
            <div class="no-active-charging">
                <p>You have no active charging sessions.</p>
                <a href="locations.php" class="btn">Find a Charging Station</a>
            </div>
        <?php endif; ?>
        
        <h2>Recent Charging History</h2>
        <?php if (count($chargingHistory) > 0): ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Location</th>
                        <th>Check-in Time</th>
                        <th>Check-out Time</th>
                        <th>Total Cost</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Show only the most recent 5 records
                    $recentHistory = array_slice($chargingHistory, 0, 5);
                    foreach ($recentHistory as $record): 
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($record['location_name']); ?></td>
                        <td><?php echo $record['check_in_time']; ?></td>
                        <td><?php echo $record['check_out_time']; ?></td>
                        <td>$<?php echo number_format($record['total_cost'], 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <a href="history.php">View Full History</a>
        <?php else: ?>
            <p>No charging history found.</p>
        <?php endif; ?>
    </div>
</body>
</html>