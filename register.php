<?php
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

   
    if (empty($username) || empty($password)) {
        echo json_encode(["status" => "error", "message" => "Username and password required."]);
        exit;
    }

   
    $stmt = $conn->prepare("SELECT User_ID FROM users WHERE UserName = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "Username already taken."]);
        exit;
    }
    $stmt->close();

 
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    
    $stmt = $conn->prepare("INSERT INTO users (UserName, Password_hash) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $password_hash);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Registration successful!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Registration failed."]);
    }

    $stmt->close();
    $conn->close();
}
?>
