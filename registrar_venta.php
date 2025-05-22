<?php
// registrar_venta.php

header('Content-Type: application/json');
require_once 'db.php';

// Obtener datos JSON
$data = json_decode(file_get_contents("php://input"), true);

// Validación básica
if (!isset($data['numero_venta'], $data['productos'], $data['precio_total'])) {
    echo json_encode(['error' => 'Datos incompletos']);
    exit;
}

$numero_venta = $data['numero_venta'];
$productos = implode(',', $data['productos']); // Guardar como CSV
$precio_total = $data['precio_total'];
$id_cliente = isset($data['id_cliente']) ? $data['id_cliente'] : null;
$fecha_venta = date('Y-m-d');

// Insertar venta
$stmt = $conn->prepare("INSERT INTO ventas (numero_venta, productos, precio_total, fecha_venta, id_cliente) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("ssdsi", $numero_venta, $productos, $precio_total, $fecha_venta, $id_cliente);
$stmt->execute();
$stmt->close();

echo json_encode(['success' => true]);
$conn->close();
?>
