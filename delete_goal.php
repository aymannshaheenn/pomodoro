<?php
session_start();
require 'db.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
$id = isset($data['id']) ? intval($data['id']) : null;


$user_id = $_SESSION['user_ID'] ?? null; 

if (!$user_id) {
    echo json_encode(["status" => "error", "message" => "Not logged in"]);
    exit;
}

if (!$id) {
    echo json_encode(["status" => "error", "message" => "Invalid goal ID"]);
    exit;
}

$stmt = $conn->prepare("DELETE FROM goals WHERE Id = ? AND user_ID = ?");
if (!$stmt) {
    echo json_encode(["status" => "error", "message" => "Prepare failed: " . $conn->error]);
    exit;
}

$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => "No goal deleted"]);
}

$stmt->close();
$conn->close();
?>
