<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json');

require_once 'db.php';

$codigo_barras = $_GET['codigo_barras'];

$stmt = $conn->prepare("SELECT * FROM zapatos WHERE codigo_barras = ?");
$stmt->bind_param("s", $codigo_barras);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode($row);
} else {
    echo json_encode(["error" => "Producto no encontrado"]);
}
?>
