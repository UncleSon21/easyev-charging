<?php
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../classes/Database.php';
require_once '../../classes/User.php';
require_once '../../classes/Location.php';

$user = new User();
$location = new Location();

// Check if admin
if (!$user->isLoggedIn() || !$user->isAdmin()) {
    header('Location: ../login.php');
    exit();
}

// Handle search
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$locations = $searchTerm ? $location->searchLocations($searchTerm) : $location->getAllLocations();
?>
<!DOCTYPE html>
<html>
<head>
  <title>Manage Locations - EasyEV-Charging</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>
  <div class="container">
    <h1>Manage Locations</h1>
    <div class="actions">
      <a href="add-location.php" class="btn">Add new Location</a>
      <a href="dashboard.php" class="btn">Back to dashboard</a>
    </div>
    <form action="GET" class="search-form">
      <input type="text" name="search" placeholder="Search locations..." value="<?php echo $searchTerm; ?>">
      <button type="submit">Search</button>
    </form>
    <table class="data-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Description</th>
          <th>Stations</th>
          <th>Cost/Hour</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($locations as $loc): ?>
        <tr>
          <td><?php echo $loc['location_id']; ?></td>
          <td><?php echo htmlspecialchars($loc['description']); ?></td>
          <td><?php echo $loc['number_of_stations']; ?></td>
          <td>$<?php echo number_format($loc['cost_per_hour'], 2); ?></td>
          <td>
            <a href="edit-location.php?id=<?php echo $loc['location_id']; ?>">Edit</a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</body>
</html>