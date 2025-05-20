<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json');

require_once 'db.php';
$input = json_decode(file_get_contents("php://input"), true);
$correo = $input['correo'];

$stmt = $conn->prepare("UPDATE usuarios SET numero_pedidos = numero_pedidos + 1 WHERE correo = ?");
$stmt->bind_param("s", $correo);
$stmt->execute();

echo json_encode(['success' => true]);
