<?php
header('Content-Type: application/json');
ob_start(); 
session_start();
include 'db_connection.php';
session_start();
include 'db_connection.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

$user_id = $_SESSION['user_ID'] ?? null;
if (!$user_id) {
  echo json_encode(["success" => false, "error" => "User not logged in"]);
  exit;
}

$data = json_decode(file_get_contents("php://input"));

$title = $data->title ?? "";
$sessions = $data->sessions ?? 0;
$workMin = $data->workMin ?? 0;
$priority = $data->priority ?? 0;
$is_completed = 0;

$sql = "INSERT INTO tasks (User_ID, Title, Estimated_sessions, Estimated_time, Priority, Is_completed)
        VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
if (!$stmt) {
  echo json_encode(["success" => false, "error" => $conn->error]);
  exit;
}

$stmt->bind_param("isiisi", $user_id, $title, $sessions, $workMin, $priority, $is_completed);

if ($stmt->execute()) {
  echo json_encode([
    "success" => true, 
    "task" => [
      "id" => $conn->insert_id,
      "title" => $title,
      "sessions" => $sessions,
      "workMin" => $workMin,
      "priority" => $priority,
      "completed" => false
    ]
  ]);
} else {
  echo json_encode(["success" => false, "error" => $stmt->error]);
}
ob_end_flush(); 

?>
