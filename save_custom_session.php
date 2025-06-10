<?php
session_start();
include 'db.php';
header('Content-Type: application/json');


$data = $_POST;
if (empty($data)) {
    $data = json_decode(file_get_contents("php://input"), true);
}

$user_id = $_SESSION['user_ID'] ?? null;
$task_id = $data['task_id'] ?? null;
$session_type = $data['session_type'] ?? 'work';
$duration = $data['duration'] ?? '';
$custom_work = isset($data['custom_work']) && $data['custom_work'] !== "" ? (int)$data['custom_work'] : null;
$custom_break = isset($data['custom_break']) && $data['custom_break'] !== "" ? (int)$data['custom_break'] : null;
$date = date('Y-m-d H:i:s');

$response = ['success' => false];

if ($user_id && $task_id && $session_type && $duration !== '') {
   
    if ($custom_work === null && $custom_break === null) {
        $sql = "INSERT INTO sessions (User_ID, Task_ID, Session_Type, Duration, Custom_work, Custom_break, Date) 
                VALUES (?, ?, ?, ?, NULL, NULL, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iisis", $user_id, $task_id, $session_type, $duration, $date);
    } elseif ($custom_work === null) {
        $sql = "INSERT INTO sessions (User_ID, Task_ID, Session_Type, Duration, Custom_work, Custom_break, Date) 
                VALUES (?, ?, ?, ?, NULL, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iissis", $user_id, $task_id, $session_type, $duration, $custom_break, $date);
    } elseif ($custom_break === null) {
        $sql = "INSERT INTO sessions (User_ID, Task_ID, Session_Type, Duration, Custom_work, Custom_break, Date) 
                VALUES (?, ?, ?, ?, ?, NULL, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iissis", $user_id, $task_id, $session_type, $duration, $custom_work, $date);
    } else {
        $sql = "INSERT INTO sessions (User_ID, Task_ID, Session_Type, Duration, Custom_work, Custom_break, Date) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iisiiis", $user_id, $task_id, $session_type, $duration, $custom_work, $custom_break, $date);
    }

    if ($stmt->execute()) {
        
        $insert = $conn->prepare("INSERT INTO history (User_ID, Task_ID, Session_Type, Duration, Date) VALUES (?, ?, ?, ?, NOW())");
        $insert->bind_param("iisi", $user_id, $task_id, $session_type, $duration);
        $insert->execute();
        $insert->close();

        $response['success'] = true;
        $response['message'] = "✅ Custom session saved!";
    } else {
        $response['message'] = "❌ Database Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    $response['message'] = "⚠️ Missing task ID, duration, or user not logged in!";
}

$conn->close();
echo json_encode($response);
?>
