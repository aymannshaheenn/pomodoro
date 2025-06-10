<?php
session_start();
header('Content-Type: application/json');

require 'db.php';

if (!isset($_SESSION['user_ID'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$user_ID = $_SESSION['user_ID'];

$stmt = $conn->prepare("SELECT Work_time, Short_break, Long_break, Dark_mode FROM settings WHERE User_ID = ?");
$stmt->bind_param("i", $user_ID);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $settings = $result->fetch_assoc();
    echo json_encode(['success' => true, 'settings' => $settings]);
} else {
    echo json_encode(['success' => false, 'message' => 'Settings not found']);
}

$stmt->close();
$conn->close();
?>
