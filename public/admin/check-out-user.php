<?php
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../classes/Database.php';
require_once '../../classes/User.php';
require_once '../../classes/Charging.php';

$user = new User();
$charging = new Charging();
$message = '';

// Check if admin
if (!$user->isLoggedIn() || !$user->isAdmin()) {
    header('Location: ../login.php');
    exit();
}

$userId = isset($_GET['user_id']) ? $_GET['user_id'] : 0;
$activeCharging = null;

if ($userId > 0) {
    $activeCharging = $charging->getActiveChargingByUser($userId);
}

// Get all active chargings
$activeChargings = $charging->getAllActiveChargings();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $chargingId = $_POST['charging_id'];
    
    $result = $charging->checkOut($chargingId);
    $message = $result['message'];
    
    if ($result['success']) {
        header('Location: dashboard.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Check-out User - EasyEV-Charging</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1>Check-out User</h1>
        
        <?php if ($message): ?>
            <div class="alert"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if ($activeCharging): ?>
            <div class="charging-info">
                <h2>Active Charging Session</h2>
                <p>User: <?php echo $_GET['user_name'] ?? 'User'; ?></p>
                <p>Location: <?php echo htmlspecialchars($activeCharging['location_name']); ?></p>
                <p>Check-in Time: <?php echo $activeCharging['check_in_time']; ?></p>
                
                <form method="POST">
                    <input type="hidden" name="charging_id" value="<?php echo $activeCharging['id']; ?>">
                    <button type="submit" class="btn">Check-out User</button>
                </form>
            </div>
        <?php else: ?>
            <form method="POST">
                <div class="form-group">
                    <label>Select Active Charging:</label>
                    <select name="charging_id" required>
                        <option value="">-- Select Charging Session --</option>
                        <?php foreach ($activeChargings as $charging): ?>
                            <option value="<?php echo $charging['id']; ?>">
                                <?php echo htmlspecialchars($charging['user_name'] . ' at ' . $charging['location_name'] . ' (' . $charging['check_in_time'] . ')'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <button type="submit" class="btn">Check-out User</button>
                <a href="dashboard.php" class="btn">Cancel</a>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>