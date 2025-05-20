<?php
// Permitir acceso desde cualquier origen
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json');

// Incluir conexión a la base de datos
require_once 'db.php';

// Obtener datos JSON enviados
$input = json_decode(file_get_contents("php://input"), true);

$correo = isset($input['correo']) ? $input['correo'] : null;
$telefono = isset($input['telefono']) ? $input['telefono'] : null;
$direccion_envio = isset($input['direccion_envio']) ? $input['direccion_envio'] : null;

if (!$correo || !$telefono || !$direccion_envio) {
    echo json_encode([
        'success' => false,
        // 'message' => 'Faltan campos requeridos (correo, teléfono o dirección).'
    ]);
    exit;
}

// Preparar y ejecutar actualización
$stmt = $conn->prepare("UPDATE usuarios SET telefono = ?, direccion_envio = ? WHERE correo = ?");
if (!$stmt) {
    echo json_encode([
        'success' => false,
        // 'message' => 'Error en la preparación de la consulta.'
    ]);
    exit;
}

$stmt->bind_param("sss", $telefono, $direccion_envio, $correo);
if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode([
            'success' => true,
            // 'message' => 'Usuario actualizado correctamente.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            // 'message' => 'No se encontró usuario con ese correo o no hubo cambios.'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        // 'message' => 'Error al ejecutar la actualización.'
    ]);
}

$stmt->close();
$conn->close();
?>
