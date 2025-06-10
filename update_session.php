<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_ID'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$session_id = $_POST['session_id'] ?? null;
$duration = $_POST['duration'] ?? null;

if (!$session_id || !$duration) {
    echo json_encode(['success' => false, 'message' => 'Missing required data']);
    exit;
}

$sql = "UPDATE sessions SET Duration = ? WHERE Session_ID = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $duration, $session_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'DB Error']);
}

$stmt->close();
$conn->close();
?>
