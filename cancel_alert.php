<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
header('Content-Type: application/json');
 
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}
 
$data = json_decode(file_get_contents('php://input'), true);
$alert_id = intval($data['alert_id'] ?? 0);
$user_id = $_SESSION['user_id'];
 
$stmt = $conn->prepare("UPDATE alerts SET status = 'Cancelled' WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $alert_id, $user_id);
$stmt->execute();
 
echo json_encode(['success' => true]);
 
