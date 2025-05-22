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

// Check if admin
if (!$user->isLoggedIn() || !$user->isAdmin()) {
    header('Location: ../login.php');
    exit();
}

$allLocations = $location->getAllLocations();
$availableLocations = $location->getAvailableLocations();
$activeChargings = $charging->getAllActiveChargings();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - EasyEV-Charging</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1>Admin Dashboard</h1>
        <p>Welcome, <?php echo $_SESSION['user_name']; ?>!</p>
        
        <nav class="main-nav">
            <a href="locations.php">Manage Locations</a>
            <a href="users.php">Manage Users</a>
            <a href="../logout.php">Logout</a>
        </nav>
        
        <div class="dashboard-stats">
            <div class="stat-card">
                <h3>Total Locations</h3>
                <p><?php echo count($allLocations); ?></p>
            </div>
            <div class="stat-card">
                <h3>Available Locations</h3>
                <p><?php echo count($availableLocations); ?></p>
            </div>
            <div class="stat-card">
                <h3>Active Chargings</h3>
                <p><?php echo count($activeChargings); ?></p>
            </div>
        </div>
        
        <div class="actions">
            <a href="add-location.php" class="btn">Add Location</a>
            <a href="check-in-user.php" class="btn">Check-in User</a>
        </div>
        
        <h2>Active Charging Sessions</h2>
        <?php if (count($activeChargings) > 0): ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Location</th>
                        <th>Check-in Time</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($activeChargings as $charging): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($charging['user_name']); ?></td>
                        <td><?php echo htmlspecialchars($charging['location_name']); ?></td>
                        <td><?php echo $charging['check_in_time']; ?></td>
                        <td>
                          <a href="check-out-user.php?user_id=<?php echo $charging['user_id']; ?>&user_name=<?php echo urlencode($charging['user_name']); ?>">Check-out</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No active charging sessions.</p>
        <?php endif; ?>
    </div>
</body>
</html>