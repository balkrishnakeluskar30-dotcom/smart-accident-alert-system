<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
 
$error = '';
$success = '';
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = sanitize($conn, $_POST['full_name']);
    $email     = sanitize($conn, $_POST['email']);
    $phone     = sanitize($conn, $_POST['phone']);
    $blood     = sanitize($conn, $_POST['blood_group']);
    $vehicle   = sanitize($conn, $_POST['vehicle_no']);
    $password  = $_POST['password'];
    $confirm   = $_POST['confirm_password'];
 
    if ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();
 
        if ($check->num_rows > 0) {
            $error = "An account with this email already exists.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (full_name, email, phone, password, blood_group, vehicle_no) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $full_name, $email, $phone, $hashed, $blood, $vehicle);
            if ($stmt->execute()) {
                $success = "Account created successfully. You can now log in.";
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Sign Up - Smart Accident Alert System</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="assets/css/style.css">
<?php include 'includes/pwa_head.php'; ?>
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-card">
        <h1 class="brand">🚨 Smart Accident Alert System</h1>
        <h2>Create Account</h2>
 
        <?php if ($error): ?><p class="alert-error"><?= $error ?></p><?php endif; ?>
        <?php if ($success): ?><p class="alert-success"><?= $success ?> <a href="login.php">Login here</a></p><?php endif; ?>
 
        <form method="POST" action="signup.php" class="auth-form">
            <input type="text" name="full_name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="text" name="phone" placeholder="Phone Number" required>
            <input type="text" name="blood_group" placeholder="Blood Group (optional)">
            <input type="text" name="vehicle_no" placeholder="Vehicle Number (optional)">
            <input type="password" name="password" placeholder="Password" required minlength="6">
            <input type="password" name="confirm_password" placeholder="Confirm Password" required minlength="6">
            <button type="submit">Sign Up</button>
        </form>
        <p class="switch-link">Already have an account? <a href="login.php">Login</a></p>
    </div>
</div>
</body>
</html>
 
