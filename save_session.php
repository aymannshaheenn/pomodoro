<?php
session_start();
include 'db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_ID'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$data = $_POST;
if (empty($_POST)) {
   
    $data = json_decode(file_get_contents("php://input"), true);
}

$user_id = $_SESSION['user_ID'];
$task_id = $data['task_id'] ?? null;
$session_type = $data['session_type'] ?? null;
$duration = $data['duration'] ?? null;
$custom_work = isset($data['custom_work']) && $data['custom_work'] !== "" ? (int)$data['custom_work'] : null;
$custom_break = isset($data['custom_break']) && $data['custom_break'] !== "" ? (int)$data['custom_break'] : null;

if (!$task_id || !$session_type || !$duration) {
    echo json_encode(['success' => false, 'message' => 'Missing required data']);
    exit;
}

$date = date("Y-m-d H:i:s");


if ($custom_work === null && $custom_break === null) {
    $sql = "INSERT INTO sessions (user_ID, Task_ID, Session_Type, Duration, Custom_work, Custom_break, Date) VALUES (?, ?, ?, ?, NULL, NULL, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisis", $user_id, $task_id, $session_type, $duration, $date);
} elseif ($custom_work === null) {
    $sql = "INSERT INTO sessions (user_ID, Task_ID, Session_Type, Duration, Custom_work, Custom_break, Date) VALUES (?, ?, ?, ?, NULL, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisiss", $user_id, $task_id, $session_type, $duration, $custom_break, $date);
} elseif ($custom_break === null) {
    $sql = "INSERT INTO sessions (user_ID, Task_ID, Session_Type, Duration, Custom_work, Custom_break, Date) VALUES (?, ?, ?, ?, ?, NULL, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisiss", $user_id, $task_id, $session_type, $duration, $custom_work, $date);
} else {
    $sql = "INSERT INTO sessions (user_ID, Task_ID, Session_Type, Duration, Custom_work, Custom_break, Date) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisiiis", $user_id, $task_id, $session_type, $duration, $custom_work, $custom_break, $date);
}

$response = ['success' => false];

if ($stmt->execute()) {
  
    $insert = $conn->prepare("INSERT INTO history (user_ID, Task_ID, Session_type, Duration, Date) VALUES (?, ?, ?, ?, NOW())");
    $insert->bind_param("iisi", $user_id, $task_id, $session_type, $duration);
    if ($insert->execute()) {
        $response['success'] = true;
    } else {
        $response['message'] = 'Session saved, but failed to save history.';
    }
    $insert->close();
} else {
    $response['message'] = 'Failed to save session.';
}

$stmt->close();
$conn->close();
echo json_encode($response);
?>
