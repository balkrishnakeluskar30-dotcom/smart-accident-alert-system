<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
 
if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit();
}
 
$error = '';
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($conn, $_POST['email']);
    $password = $_POST['password'];
 
    $stmt = $conn->prepare("SELECT id, full_name, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
 
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['full_name'] = $user['full_name'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "No account found with that email.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login - Smart Accident Alert System</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="assets/css/style.css">
<?php include 'includes/pwa_head.php'; ?>
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-card">
        <h1 class="brand">🚨 Smart Accident Alert System</h1>
        <h2>Login</h2>
 
        <?php if ($error): ?><p class="alert-error"><?= $error ?></p><?php endif; ?>
 
        <form method="POST" action="login.php" class="auth-form">
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <p class="switch-link">Don't have an account? <a href="signup.php">Sign Up</a></p>
    </div>
</div>
</body>
</html>
 
