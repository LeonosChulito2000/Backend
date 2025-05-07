<?php
// Encabezados para permitir CORS correctamente
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// Manejo de solicitud OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include("db.php");

// Decodifica el JSON recibido desde Angular
$data = json_decode(file_get_contents("php://input"), true);

// Validar y extraer los datos con valores por defecto si faltan
$nombres = $data['nombres'] ?? '';
$apellido_paterno = $data['apellido_paterno'] ?? '';
$apellido_materno = $data['apellido_materno'] ?? '';
$correo = $data['correo'] ?? '';
$telefono = $data['telefono'] ?? '';
$fecha_registro = $data['fecha_registro'] ?? date('Y-m-d');
$direccion_envio = $data['direccion_envio'] ?? '';
$numero_pedidos = $data['numero_pedidos'] ?? 1;

// Preparar y ejecutar la consulta de inserción
$stmt = $conn->prepare("INSERT INTO usuarios (nombres, apellido_paterno, apellido_materno, correo, telefono, fecha_registro, direccion_envio, numero_pedidos) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

if ($stmt) {
    $stmt->bind_param("sssssssi", $nombres, $apellido_paterno, $apellido_materno, $correo, $telefono, $fecha_registro, $direccion_envio, $numero_pedidos);
    
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Usuario registrado correctamente."]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al ejecutar: " . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Error al preparar consulta: " . $conn->error]);
}

$conn->close();
?>
