<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
header('Content-Type: application/json');
 
if (!isLoggedIn()) {
    echo json_encode(['success' => false]);
    exit();
}
 
$data = json_decode(file_get_contents('php://input'), true);
$user_id = $_SESSION['user_id'];
$lat = $data['latitude'] ?? null;
$lng = $data['longitude'] ?? null;
$speed = $data['speed_kmh'] ?? null;
 
$stmt = $conn->prepare("INSERT INTO driving_logs (user_id, speed_kmh, latitude, longitude) VALUES (?, ?, ?, ?)");
$stmt->bind_param("iddd", $user_id, $speed, $lat, $lng);
$stmt->execute();
 
echo json_encode(['success' => true]);
 
