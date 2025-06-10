<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');


$user_id = $_SESSION['user_ID'] ?? null;
if (!$user_id) {
    echo json_encode(["success" => false, "message" => "User not logged in"]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        echo json_encode(["success" => false, "message" => "Invalid JSON input"]);
        exit;
    }

    $title = $data['title'] ?? '';
    $sessions = (int)($data['sessions'] ?? 0);
    $workMin = (int)($data['workMin'] ?? 0);
    $priority = $data['priority'] ?? 'Medium';
    $is_completed = 0;

    if (!$title || !$sessions || !$workMin || !$priority) {
        echo json_encode(["success" => false, "message" => "Missing required fields"]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO task (user_ID, Title, Estimated_sessions, Estimated_time, Priority, Is_completed) VALUES (?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        echo json_encode(["success" => false, "message" => "Prepare failed: " . $conn->error]);
        exit;
    }

    $stmt->bind_param("isissi", $user_id, $title, $sessions, $workMin, $priority, $is_completed);

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
        echo json_encode(["success" => false, "message" => $stmt->error]);
    }

    $stmt->close();
    $conn->close();
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $conn->prepare("SELECT * FROM task WHERE user_ID = ? ORDER BY 
        CASE Priority 
            WHEN 'High' THEN 1 
            WHEN 'Medium' THEN 2 
            WHEN 'Low' THEN 3 
            ELSE 4 
        END");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $tasks = [];
    while ($row = $result->fetch_assoc()) {
        $tasks[] = $row;
    }

    echo json_encode($tasks);
    $stmt->close();
    $conn->close();
    exit;
}


echo json_encode(["success" => false, "message" => "Unsupported HTTP method"]);
exit;
?>
