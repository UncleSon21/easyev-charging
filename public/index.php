<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../classes/Database.php';

// Test database connection
$db = new Database();
?>
<!DOCTYPE html>
<html>
<head>
    <title>EasyEV-Charging</title>
</head>
<body>
    <h1>Welcome to EasyEV-Charging</h1>
    <p>Database connection successful!</p>
    <a href="login.php">Login</a> | <a href="register.php">Register</a>
</body>
</html>