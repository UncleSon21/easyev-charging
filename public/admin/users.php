<?php
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../classes/Database.php';
require_once '../../classes/User.php';
require_once '../../classes/Charging.php';

$user = new User();
$charging = new Charging();

// Check if admin
if (!$user->isLoggedIn() || !$user->isAdmin()) {
    header('Location: ../login.php');
    exit();
}

// Method to get all users
function getAllUsers() {
    $db = new Database();
    $conn = $db->getConnection();
    $result = $conn->query("SELECT * FROM users ORDER BY name");
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Search functionality
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
if (!empty($searchTerm)) {
    $db = new Database();
    $conn = $db->getConnection();
    $searchTerm = "%{$searchTerm}%";
    $stmt = $conn->prepare("SELECT * FROM users WHERE name LIKE ? OR email LIKE ? OR phone LIKE ? ORDER BY name");
    $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
    $users = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $users = getAllUsers();
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Users - EasyEV-Charging</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1>Manage Users</h1>
        
        <div class="actions">
            <a href="dashboard.php" class="btn">Back to Dashboard</a>
        </div>
        
        <form method="GET" class="search-form">
            <input type="text" name="search" placeholder="Search users..." value="<?php echo htmlspecialchars($searchTerm); ?>">
            <button type="submit">Search</button>
        </form>
        
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $userItem): ?>
                <tr>
                    <td><?php echo $userItem['id']; ?></td>
                    <td><?php echo htmlspecialchars($userItem['name']); ?></td>
                    <td><?php echo htmlspecialchars($userItem['email']); ?></td>
                    <td><?php echo htmlspecialchars($userItem['phone']); ?></td>
                    <td><?php echo $userItem['type']; ?></td>
                    <td>
                        <?php 
                        $activeCharging = $charging->getActiveChargingByUser($userItem['id']);
                        if ($activeCharging): 
                        ?>
                            <span class="status-active">Charging</span>
                        <?php else: ?>
                            <span class="status-inactive">Not Charging</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($activeCharging): ?>
                            <a href="check-out-user.php?user_id=<?php echo $userItem['id']; ?>&user_name=<?php echo urlencode($userItem['name']); ?>">Check-out</a>
                        <?php else: ?>
                            <a href="check-in-user.php?user_id=<?php echo $userItem['id']; ?>">Check-in</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>