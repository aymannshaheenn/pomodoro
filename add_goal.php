<?php
session_start();
require 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_ID'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);
$goal = $input['goal'];

if (!$goal) {
    echo json_encode(['status' => 'error', 'message' => 'Goal text is empty']);
    exit;
}


$userID = $_SESSION['user_ID']; 
$date = date('Y-m-d');

$sql = "INSERT INTO goals (User_ID, Goal_Text, Status, Created_Date) VALUES (?, ?, 'Active', ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iss", $userID, $goal, $date);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to add goal']);
}
?>
