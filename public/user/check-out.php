<?php
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../classes/Database.php';
require_once '../../classes/User.php';
require_once '../../classes/Location.php';
require_once '../../classes/Charging.php';

$user = new User();
$charging = new Charging();
$message = '';

// Check if logged in
if (!$user->isLoggedIn()) {
    header('Location: ../login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$activeCharging = $charging->getActiveChargingByUser($userId);

if (!$activeCharging) {
    header('Location: dashboard.php');
    exit();
}

$completed = false;
$totalCost = 0;
$durationHours = 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $result = $charging->checkOut($activeCharging['id'], $userId);
    $message = $result['message'];
    
    if ($result['success']) {
        $completed = true;
        $totalCost = $result['total_cost'];
        $durationHours = $result['duration_hours'];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Check-out - EasyEV-Charging</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1>Charging Check-out</h1>
        
        <?php if ($completed): ?>
            <div class="success-message">
                <h2>Check-out Successful!</h2>
                <p>Location: <?php echo htmlspecialchars($activeCharging['location_name']); ?></p>
                <p>Check-in Time: <?php echo $activeCharging['check_in_time']; ?></p>
                <p>Duration: <?php echo $durationHours; ?> hours</p>
                <p>Total Cost: $<?php echo number_format($totalCost, 2); ?></p>
                <p>Thank you for using EasyEV-Charging!</p>
                <a href="dashboard.php" class="btn">Back to Dashboard</a>
            </div>
        <?php else: ?>
            <div class="charging-info">
                <h2>Current Charging Session</h2>
                <p>Location: <?php echo htmlspecialchars($activeCharging['location_name']); ?></p>
                <p>Check-in Time: <?php echo $activeCharging['check_in_time']; ?></p>
                <p>Cost per Hour: $<?php echo number_format($activeCharging['cost_per_hour'], 2); ?></p>
                
                <?php
                // Calculate current duration and cost
                $checkInTime = strtotime($activeCharging['check_in_time']);
                $currentDuration = time() - $checkInTime;
                $currentHours = $currentDuration / 3600;
                $currentCost = round($currentHours * $activeCharging['cost_per_hour'], 2);
                ?>
                
                <p>Current Duration: <?php echo round($currentHours, 2); ?> hours</p>
                <p>Current Cost: $<?php echo number_format($currentCost, 2); ?></p>
                
                <form method="POST">
                    <button type="submit" class="btn">Check-out Now</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>