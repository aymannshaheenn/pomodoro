<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_ID'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_ID'];
$task_id = $_POST['task_id'] ?? null;
$session_type = $_POST['session_type'] ?? null;
$custom_work = $_POST['custom_work'] ?? null;
$custom_break = $_POST['custom_break'] ?? null;
$date = date("Y-m-d H:i:s");

if (!$task_id || !$session_type) {
    echo json_encode(['success' => false, 'message' => 'Missing required data']);
    exit;
}

$sql = "INSERT INTO sessions (user_ID, Task_ID, Session_Type, Duration, Custom_work, Custom_break, Date)
        VALUES (?, ?, ?, 0, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iisiss", $user_id, $task_id, $session_type, $custom_work, $custom_break, $date);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'session_id' => $stmt->insert_id]);
} else {
    echo json_encode(['success' => false, 'message' => 'DB Error']);
}

$stmt->close();
$conn->close();
?>
