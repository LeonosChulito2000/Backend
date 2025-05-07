<?php
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');
include "db.php";

$response = [
    "status" => "error",
    "message" => "Algo salió mal."
];

try {
    if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("No se recibió una imagen válida.");
    }

    $nombre = $_POST['nombre'] ?? '';
    $tallas = $_POST['tallas'] ?? '';
    $colores = $_POST['colores'] ?? '';
    $stock = intval($_POST['stock'] ?? 0);
    $fecha_subida = date('Y-m-d');
    $precio = intval($_POST['precio'] ?? 0);
    $material = $_POST['material'] ?? '';

    $nombreImagen = uniqid() . "_" . basename($_FILES['imagen']['name']);
    $ruta = "../Shoe_Track/public/NewZapatos/" . $nombreImagen;

    if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta)) {
        throw new Exception("Error al mover la imagen al directorio destino.");
    }

    $sql = "INSERT INTO zapatos (nombre, tallas, precio, colores, material, stock, fecha_subida, imagen) 
    VALUES ('$nombre', '$tallas', $precio, '$colores', '$material', $stock, '$fecha_subida', '$nombreImagen')";

    if ($conn->query($sql) !== TRUE) {
        throw new Exception("Error en la base de datos: " . $conn->error);
    }

    $response["status"] = "success";
    $response["message"] = "Zapato registrado con éxito.";
} catch (Exception $e) {
    $response["message"] = $e->getMessage();
}

echo json_encode($response);
?>
