<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../classes/Database.php';
require_once '../classes/User.php';

$user = new User();
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $result = $user->login($email, $password);
    
    if ($result['success']) {
        if ($result['user_type'] == 'Administrator') {
            header('Location: admin/dashboard.php');
        } else {
            header('Location: user/dashboard.php');
        }
        exit();
    } else {
        $message = $result['message'];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - EasyEV-Charging</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <?php if ($message): ?>
            <div class="alert"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <input type="email" name="email" placeholder="Email" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <button type="submit">Login</button>
        </form>
        
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</body>
</html>