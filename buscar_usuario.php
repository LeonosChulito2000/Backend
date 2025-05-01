<?php
// Permitir acceso desde cualquier origen
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json');

// Incluir la conexión a la base de datos
require_once 'db.php';

// Obtener los datos del cuerpo si es JSON
$input = json_decode(file_get_contents("php://input"), true);
$correo = isset($input['correo']) ? $input['correo'] : null;

if ($correo) {
    // Buscar usuario igual que antes...
    $stmt = $conn->prepare("SELECT id, nombres, apellido_paterno, apellido_materno, fecha_registro, numero_pedidos, direccion_envio, correo, telefono FROM usuarios WHERE correo = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();
        echo json_encode(['success' => true, 'usuario' => $usuario]);
    } else {
        echo json_encode(['success' => false, 'mensaje' => 'Usuario no encontrado']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'mensaje' => 'Correo no recibido o vacío']);
}

?>
