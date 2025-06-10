<?php
session_start();
require 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_ID'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);
$id = $input['id'];
$status = $input['status'];

$sql = "UPDATE goals SET Status = ?, Achieved_Date = IF(? = 'Achieved', CURDATE(), NULL) WHERE Id = ? AND User_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssii", $status, $status, $id, $_SESSION['user_ID']);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Update failed']);
}
?>
