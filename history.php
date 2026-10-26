<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
requireLogin();
 
$user_id = $_SESSION['user_id'];
$conn->query("SET time_zone = '+05:30'"); // show alert times in Indian Standard Time
$stmt = $conn->prepare("SELECT * FROM alerts WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$alerts = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Alert History - Smart Accident Alert System</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="assets/css/style.css">
<?php include 'includes/pwa_head.php'; ?>
</head>
<body>
<?php include 'includes/navbar.php'; ?>
 
<div class="container">
    <h2>Accident & SOS Alert History</h2>
    <div class="card">
        <table class="data-table">
            <tr>
                <th>Date/Time</th><th>Type</th><th>Speed (km/h)</th>
                <th>Risk Score</th><th>Location</th><th>Status</th>
            </tr>
            <?php while ($row = $alerts->fetch_assoc()): ?>
            <tr>
                <td><?= $row['created_at'] ?></td>
                <td><span class="tag"><?= htmlspecialchars($row['alert_type'] === 'AI Auto-Detected' ? 'Speed Drop Detected' : $row['alert_type']) ?></span></td>
                <td><?= $row['speed_kmh'] !== null ? $row['speed_kmh'] : '-' ?></td>
                <td><?= $row['ai_confidence'] !== null ? $row['ai_confidence'] . '%' : '-' ?></td>
                <td>
                    <?php if ($row['latitude'] && $row['longitude']): ?>
                        <a href="https://maps.google.com/?q=<?= $row['latitude'] ?>,<?= $row['longitude'] ?>" target="_blank">View Map</a>
                    <?php else: ?> - <?php endif; ?>
                </td>
                <td>
                    <span class="status status-<?= strtolower($row['status']) ?>"><?= $row['status'] ?></span>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</div>
</body>
</html>
 
