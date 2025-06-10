<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_ID'])) {
    echo json_encode([
        "status" => "error",
        "message" => "USER NOT LOGGED IN"
    ]);
    exit;
}

require 'db.php';

$user_id = $_SESSION['user_ID'];

$sql = "
    SELECT 
        h.Session_type AS type, 
        h.Duration AS duration, 
        h.Date AS date, 
        t.Title AS title
    FROM history h
    INNER JOIN task t ON h.Task_ID = t.Task_ID
    WHERE h.user_ID = ?
    ORDER BY h.Date DESC, h.Id DESC
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode([
        "status" => "error",
        "message" => "SQL error: " . $conn->error
    ]);
    exit;
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();


$history = [];
while ($row = $result->fetch_assoc()) {
    $history[] = [
        "title" => htmlspecialchars($row['title']),
        "type" => ucfirst(str_replace('_', ' ', $row['type'])),
        "duration" => round($row['duration'] / 60),  // Convert seconds to minutes
        "date" => date("Y-m-d H:i", strtotime($row['date']))  // Format date nicely
    ];
}

$stmt->close();
$conn->close();

echo json_encode([
    "status" => "success",
    "history" => $history
]);
?>
