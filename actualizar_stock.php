<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json');

require_once 'db.php';
$input = json_decode(file_get_contents("php://input"), true);
$sku = $input['sku'];
$cantidad = intval($input['cantidad']);

$stmt = $conn->prepare("UPDATE zapatos SET stock = GREATEST(stock - ?, 0) WHERE codigo_barras = ?");
$stmt->bind_param("is", $cantidad, $sku);
$stmt->execute();

echo json_encode(['success' => true]);
