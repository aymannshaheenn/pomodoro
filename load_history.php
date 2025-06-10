<?php
include 'db.php';

$user_id = 1; 

$sql = "SELECT t.Title AS Task, s.Session_Type, s.Duration, s.Date
        FROM sessions s
        JOIN tasks t ON s.Task_ID = t.Task_ID
        WHERE s.User_ID = ?
        ORDER BY s.Date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$history = [];

while ($row = $result->fetch_assoc()) {
    $history[] = $row;
}

echo json_encode($history);

$stmt->close();
$conn->close();
?>
