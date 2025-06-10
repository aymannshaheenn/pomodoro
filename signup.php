<?php
session_start();
header('Content-Type: application/json');
require_once 'db.php';

$input = json_decode(file_get_contents("php://input"), true);

if (!isset($input['username']) || !isset($input['password'])) {
    echo json_encode(["success" => false, "message" => "Username and password required."]);
    exit();
}

$username = trim($input['username']);
$password = $input['password'];
$password_hash = password_hash($password, PASSWORD_DEFAULT);


$stmt = $conn->prepare("SELECT user_ID FROM user WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "Username already taken."]);
    exit();
}
$stmt->close();


$stmt = $conn->prepare("INSERT INTO user (username, password_hash, signup_date) VALUES (?, ?, NOW())");
$stmt->bind_param("ss", $username, $password_hash);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Signup successful! You can now log in."]);
} else {
    echo json_encode(["success" => false, "message" => "Signup failed. Try again."]);
}

$stmt->close();
$conn->close();
?>
