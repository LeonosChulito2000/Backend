<?php
// backend/login_admin.php

include 'db.php'; // Conexión inmutable

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"));

$username = $conn->real_escape_string($data->username);
$password = $conn->real_escape_string($data->password);

$query = "SELECT * FROM admin_users WHERE username = BINARY '$username' AND password = BINARY '$password'";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Usuario o contraseña incorrectos"]);
}

$conn->close();
?>
