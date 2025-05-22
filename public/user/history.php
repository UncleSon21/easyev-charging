<?php
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../classes/Database.php';
require_once '../../classes/User.php';
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
$chargingHistory = $charging->getUserChargingHistory($userId);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Charging History - EasyEV-Charging</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1>Your Charging History</h1>
        
        <a href="dashboard.php">Back to Dashboard</a>
        
        <?php if (count($chargingHistory) > 0): ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Location</th>
                        <th>Check-in Time</th>
                        <th>Check-out Time</th>
                        <th>Duration (hours)</th>
                        <th>Total Cost</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($chargingHistory as $record): 
                        // Calculate duration
                        $checkInTime = strtotime($record['check_in_time']);
                        $checkOutTime = strtotime($record['check_out_time']);
                        $duration = ($checkOutTime - $checkInTime) / 3600;
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($record['location_name']); ?></td>
                        <td><?php echo $record['check_in_time']; ?></td>
                        <td><?php echo $record['check_out_time']; ?></td>
                        <td><?php echo round($duration, 2); ?></td>
                        <td>$<?php echo number_format($record['total_cost'], 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <?php
            // Calculate total statistics
            $totalCost = array_sum(array_column($chargingHistory, 'total_cost'));
            $totalSessions = count($chargingHistory);
            ?>
            
            <div class="statistics">
                <h2>Statistics</h2>
                <p>Total Sessions: <?php echo $totalSessions; ?></p>
                <p>Total Cost: $<?php echo number_format($totalCost, 2); ?></p>
                <p>Average Cost per Session: $<?php echo $totalSessions > 0 ? number_format($totalCost / $totalSessions, 2) : '0.00'; ?></p>
            </div>
        <?php else: ?>
            <p>No charging history found.</p>
        <?php endif; ?>
    </div>
</body>
</html>