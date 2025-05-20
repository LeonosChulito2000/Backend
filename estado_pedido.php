<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

include("db.php");

$data = json_decode(file_get_contents("php://input"), true);
$id_pedido = $data['id_pedido'] ?? '';

if (!$id_pedido) {
    echo json_encode(["success" => false, "message" => "ID de pedido faltante"]);
    exit();
}

// Obtener productos del pedido
$sql = "SELECT po.*, z.nombre AS nombre_producto, z.imagen 
        FROM pedidosOnline po
        JOIN zapatos z ON z.codigo_barras = po.sku
        WHERE po.id_pedido = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $id_pedido);
$stmt->execute();
$resultado = $stmt->get_result();

$productos = [];
$id_cliente = null;
$estado = null;

$fecha_compra = null;
$fecha_entrega = null;

while ($fila = $resultado->fetch_assoc()) {
    $productos[] = $fila;
    $id_cliente = $fila['id_cliente'];
    $estado = $fila['estado'];
    $fecha_compra = $fila['fecha_compra'];
    $fecha_entrega = $fila['fecha_entrega'];
}

if (empty($productos)) {
    echo json_encode(["success" => false, "message" => "Pedido no encontrado"]);
    exit();
}

// Obtener información del cliente
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id_cliente);
$stmt->execute();
$cliente = $stmt->get_result()->fetch_assoc();

echo json_encode([
    "success" => true,
    "estado" => $estado,
    "productos" => $productos,
    "cliente" => $cliente,
    "id_pedido" => $id_pedido,
    "fecha_compra" => $fecha_compra,
    "fecha_entrega" => $fecha_entrega
]);