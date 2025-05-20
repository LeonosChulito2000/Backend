<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json');

require_once 'db.php';

$data = json_decode(file_get_contents("php://input"), true);

$correo = $data["correo"];
$productos = $data["productos"];

$respuesta = ["success" => false];

// 1. Obtener id_cliente a partir del correo
$sqlCliente = "SELECT id FROM usuarios WHERE correo = ?";
$stmtCliente = $conn->prepare($sqlCliente);
$stmtCliente->bind_param("s", $correo);
$stmtCliente->execute();
$resultCliente = $stmtCliente->get_result();

if ($resultCliente->num_rows === 0) {
    $respuesta["error"] = "Usuario no encontrado.";
    echo json_encode($respuesta);
    exit;
}

$cliente = $resultCliente->fetch_assoc();
$id_cliente = $cliente['id'];

// 2. Crear id_pedido random
$id_pedido = strval(rand(10000, 99999));

// 3. Fechas
$fecha_compra = date('Y-m-d');
$fecha_entrega = date('Y-m-d', strtotime('+2 days'));

// 4. Insertar productos
$conn->begin_transaction();

try {
    foreach ($productos as $item) {
        $sku = $item["sku"];
        $color = $item["color"];
        $material = $item["material"];
        $talla = $item["talla"];
        $cantidad = $item["cantidad"];
        $precio = $item["precio"];

        $sqlInsert = "INSERT INTO pedidosOnline 
            (id_pedido, id_cliente, sku, color, material, talla, cantidad, precio, fecha_compra, fecha_entrega, estado)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'preparacion')";

        $stmtInsert = $conn->prepare($sqlInsert);
        $stmtInsert->bind_param("sissssidss", $id_pedido, $id_cliente, $sku, $color, $material, $talla, $cantidad, $precio, $fecha_compra, $fecha_entrega);

        if (!$stmtInsert->execute()) {
            throw new Exception($stmtInsert->error);
        }
    }

    $conn->commit();
    $respuesta["success"] = true;
    $respuesta["id_pedido"] = $id_pedido;

} catch (Exception $e) {
    $conn->rollback();
    $respuesta["error"] = $e->getMessage();
}

echo json_encode($respuesta);
?>
