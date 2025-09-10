<?php
// Login page for Toko Tsabita
session_start();

// If user is already logged in, redirect to main page
if (isset($_SESSION['user'])) {
    header('Location: index.php');
    exit();
}

require_once 'includes/db_config.php';

$error = '';

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    if (!empty($username) && !empty($password)) {
        $user = $pdo->authenticateUser($username, $password);
        
        if ($user) {
            // Store user info in session
            $_SESSION['user'] = $user;
            // Redirect to main page
            header('Location: index.php');
            exit();
        } else {
            $error = 'Username atau password salah!';
        }
    } else {
        $error = 'Harap isi semua field!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Toko Tsabita</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Toko Tsabita</h1>
            <p>Sistem Manajemen Kasir</p>
        </header>
        
        <div class="login-container">
            <div class="login-form">
                <h2>Login</h2>
                
                <?php if ($error): ?>
                <div class="error-message">
                    <p><?php echo $error; ?></p>
                </div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    
                    <button type="submit" name="login">Login</button>
                </form>
                
                <div class="login-info">
                    <p>Silakan login dengan akun yang telah dibuat oleh administrator.</p>
                </div>
            </div>
        </div>
        
        <footer>
            <p>&copy; 2025 Toko Tsabita. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>