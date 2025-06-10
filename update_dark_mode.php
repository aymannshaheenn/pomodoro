<?php
session_start();
header('Content-Type: application/json');

require 'db.php';

if (!isset($_SESSION['user_ID'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$user_ID = $_SESSION['user_ID'];

if (!isset($_POST['dark_mode'])) {
    echo json_encode(['success' => false, 'message' => 'Missing dark_mode value']);
    exit;
}

$dark_mode = intval($_POST['dark_mode']);

$stmt = $conn->prepare("UPDATE settings SET Dark_mode = ? WHERE User_ID = ?");
$stmt->bind_param("ii", $dark_mode, $user_ID);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database update failed']);
}

$stmt->close();
$conn->close();
?>
