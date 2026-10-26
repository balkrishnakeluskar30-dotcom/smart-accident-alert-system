<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
requireLogin();
 
$user_id = $_SESSION['user_id'];
 
// Add contact
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_contact'])) {
    $name = sanitize($conn, $_POST['contact_name']);
    $type = sanitize($conn, $_POST['contact_type']);
    $phone = sanitize($conn, $_POST['phone']);
    $email = sanitize($conn, $_POST['email']);
 
    $stmt = $conn->prepare("INSERT INTO emergency_contacts (user_id, contact_name, contact_type, phone, email) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $user_id, $name, $type, $phone, $email);
    $stmt->execute();
    header("Location: contacts.php");
    exit();
}
 
// Delete contact
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM emergency_contacts WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();
    header("Location: contacts.php");
    exit();
}
 
$contacts = $conn->prepare("SELECT * FROM emergency_contacts WHERE user_id = ? ORDER BY created_at DESC");
$contacts->bind_param("i", $user_id);
$contacts->execute();
$contactList = $contacts->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Emergency Contacts - Smart Accident Alert System</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="assets/css/style.css">
<?php include 'includes/pwa_head.php'; ?>
</head>
<body>
<?php include 'includes/navbar.php'; ?>
 
<div class="container">
    <h2>Emergency Contact & Management</h2>
    <p class="muted">Family members, hospitals, and ambulance services who will be alerted automatically.</p>
 
    <div class="card">
        <h3>Add New Contact</h3>
        <form method="POST" class="inline-form">
            <input type="text" name="contact_name" placeholder="Name" required>
            <select name="contact_type">
                <option value="Family">Family</option>
                <option value="Friend">Friend</option>
                <option value="Hospital">Hospital</option>
                <option value="Ambulance">Ambulance</option>
                <option value="Other">Other</option>
            </select>
            <input type="text" name="phone" placeholder="Phone Number" required>
            <input type="email" name="email" placeholder="Email (optional)">
            <button type="submit" name="add_contact">Add Contact</button>
        </form>
    </div>
 
    <div class="card">
        <h3>Your Contacts</h3>
        <table class="data-table">
            <tr><th>Name</th><th>Type</th><th>Phone</th><th>Email</th><th>Action</th></tr>
            <?php while ($row = $contactList->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['contact_name']) ?></td>
                <td><span class="tag"><?= htmlspecialchars($row['contact_type']) ?></span></td>
                <td><?= htmlspecialchars($row['phone']) ?></td>
                <td><?= htmlspecialchars($row['email'] ?: '-') ?></td>
                <td><a href="contacts.php?delete=<?= $row['id'] ?>" onclick="return confirm('Remove this contact?')" class="btn-danger">Remove</a></td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</div>
</body>
</html>
 
