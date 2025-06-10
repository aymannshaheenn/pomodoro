<?php
session_start();
header('Content-Type: application/json');


require_once 'db.php';


$input = json_decode(file_get_contents("php://input"), true);


if (!isset($input['username']) || !isset($input['password'])) {
    echo json_encode(["success" => false, "message" => "Username or password missing."]);
    exit();
}

$username = trim($input['username']);
$password = $input['password'];


$stmt = $conn->prepare("SELECT user_ID, password_hash FROM user WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    
    if (password_verify($password, $row['password_hash'])) {
        $_SESSION['user_ID'] = $row['user_ID']; 
        echo json_encode(["success" => true, "message" => "Login successful!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Incorrect password."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "User not found."]);
}

$stmt->close();
$conn->close();
?>
