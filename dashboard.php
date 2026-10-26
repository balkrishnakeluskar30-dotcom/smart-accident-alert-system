<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
requireLogin();
 
$user_id = $_SESSION['user_id'];
$countStmt = $conn->prepare("SELECT COUNT(*) as cnt FROM emergency_contacts WHERE user_id = ?");
$countStmt->bind_param("i", $user_id);
$countStmt->execute();
$contactCount = $countStmt->get_result()->fetch_assoc()['cnt'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Dashboard - Smart Accident Alert System</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="assets/css/style.css">
<?php include 'includes/pwa_head.php'; ?>
</head>
<body>
<?php include 'includes/navbar.php'; ?>
 
<div class="container">
 
    <?php if ($contactCount == 0): ?>
    <div class="banner-warning">
        ⚠ You haven't added any emergency contacts yet.
        <a href="contacts.php">Add contacts now</a> so alerts can actually be sent.
    </div>
    <?php endif; ?>
 
    <div class="dashboard-grid">
 
        <!-- Live status card -->
        <div class="card status-card">
            <h3>Live Monitoring</h3>
            <div class="status-row"><span>GPS Status:</span> <span id="gpsStatus" class="tag tag-pending">Waiting...</span></div>
            <div class="status-row"><span>Current Speed:</span> <span id="speedValue">0</span> km/h</div>
            <div class="status-row"><span>Latitude:</span> <span id="latValue">-</span></div>
            <div class="status-row"><span>Longitude:</span> <span id="lngValue">-</span></div>
            <div class="status-row"><span>Risk Level:</span>
                <div class="progress-bar"><div id="riskBar" class="progress-fill"></div></div>
                <span id="riskValue">0%</span>
            </div>
            <div class="status-row"><span>Monitoring:</span>
                <label class="switch">
                    <input type="checkbox" id="monitorToggle">
                    <span class="slider"></span>
                </label>
            </div>
        </div>
 
        <!-- SOS card -->
        <div class="card sos-card">
            <h3>Emergency SOS</h3>
            <p class="muted">Press in case of an emergency. This immediately alerts all your saved contacts, hospitals and ambulance services with your live location.</p>
            <button id="sosBtn" class="sos-button">SOS</button>
            <p class="muted small">Automatic detection is active while monitoring is ON — sudden speed drops, heavy impact, or no-movement after high speed will start a 15-second countdown before an alert is sent. Keep the screen on and this page open while travelling.</p>
        </div>
 
        <!-- Driving behaviour -->
        <div class="card">
            <h3>Driving Behaviour Analysis</h3>
            <canvas id="speedChart" height="120"></canvas>
            <p class="muted small" id="behaviourSummary">Start monitoring to analyse your driving pattern.</p>
        </div>
 
        <!-- Detection log -->
        <div class="card">
            <h3>Detection Log</h3>
            <ul id="detectionLog" class="log-list">
                <li class="muted">No events yet.</li>
            </ul>
        </div>
    </div>
</div>
 
<!-- Countdown / confirmation modal for false-alert reduction -->
<div id="confirmModal" class="modal-overlay hidden">
    <div class="modal-box">
        <h2>⚠ Possible Accident Detected</h2>
        <p id="modalReason">Unusual driving pattern detected.</p>
        <p>Sending emergency alert in <span id="countdownNum">15</span> seconds...</p>
        <button id="cancelAlertBtn" class="btn-secondary">I'm OK — Cancel Alert</button>
    </div>
</div>
 
<div id="toast" class="toast hidden"></div>
 
<script src="assets/js/detection.js"></script>
</body>
</html>
 
