<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
header('Content-Type: application/json');
date_default_timezone_set('Asia/Kolkata'); // alert emails show Indian Standard Time
 
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}
 
$data = json_decode(file_get_contents('php://input'), true);
 
$user_id      = $_SESSION['user_id'];
$alert_type   = $data['alert_type']   ?? 'Manual SOS';
$latitude     = $data['latitude']     ?? null;
$longitude    = $data['longitude']    ?? null;
$speed        = $data['speed_kmh']    ?? null;
$ai_confidence= $data['ai_confidence']?? null;
 
$allowedTypes = ['Manual SOS','AI Auto-Detected','Impact Detected','No Movement'];
if (!in_array($alert_type, $allowedTypes)) {
    $alert_type = 'Manual SOS';
}
 
$stmt = $conn->prepare("INSERT INTO alerts (user_id, alert_type, latitude, longitude, speed_kmh, ai_confidence, status) VALUES (?, ?, ?, ?, ?, ?, 'Sent')");
$stmt->bind_param("isdddi", $user_id, $alert_type, $latitude, $longitude, $speed, $ai_confidence);
$stmt->execute();
$alertId = $conn->insert_id;
 
// Fetch emergency contacts and "send" notifications
$contactsStmt = $conn->prepare("SELECT contact_name, contact_type, phone, email FROM emergency_contacts WHERE user_id = ?");
$contactsStmt->bind_param("i", $user_id);
$contactsStmt->execute();
$contacts = $contactsStmt->get_result()->fetch_all(MYSQLI_ASSOC);
 
// Automatically look up hospitals near the accident location (OpenStreetMap)
$nearbyHospitals = findNearbyHospitals($latitude, $longitude, 5000, 3);
 
$mapsLink = ($latitude && $longitude)
    ? "https://maps.google.com/?q=$latitude,$longitude"
    : "location unavailable";
 
// Tell the contact WHO needs help
$userStmt = $conn->prepare("SELECT full_name, phone, blood_group, vehicle_no FROM users WHERE id = ?");
$userStmt->bind_param("i", $user_id);
$userStmt->execute();
$userRow = $userStmt->get_result()->fetch_assoc();
 
$who = !empty($userRow['full_name']) ? $userRow['full_name'] : ('user #' . $user_id);
$details = [];
if (!empty($userRow['phone']))       $details[] = 'Phone: ' . $userRow['phone'];
if (!empty($userRow['blood_group'])) $details[] = 'Blood group: ' . $userRow['blood_group'];
if (!empty($userRow['vehicle_no']))  $details[] = 'Vehicle: ' . $userRow['vehicle_no'];
$detailsText = $details ? ' (' . implode(', ', $details) . ')' : '';
 
$message = "EMERGENCY ALERT ($alert_type) for " . $who . $detailsText . ". Live location: $mapsLink. Date and time: " . date('Y-m-d H:i:s');
 
$notified = [];
foreach ($contacts as $c) {
    sendEmergencyNotification($c, $message);
    $notified[] = $c['contact_name'] . ' (' . $c['contact_type'] . ')';
}
 
$hospitalsNotified = [];
foreach ($nearbyHospitals as $h) {
    sendEmergencyNotification($h, $message);
    $hospitalsNotified[] = $h['contact_name'] . ' (' . round($h['distance_m']/1000, 1) . ' km away)'
        . (empty($h['email']) ? ' — no email on record, logged only' : ' — emailed');
}
 
echo json_encode([
    'success' => true,
    'alert_id' => $alertId,
    'notified_contacts' => $notified,
    'nearby_hospitals' => $hospitalsNotified,
    'message' => 'Alert logged, emergency contacts notified, nearby hospitals located.'
]);
 
