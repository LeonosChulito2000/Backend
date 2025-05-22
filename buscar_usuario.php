<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json');

require_once 'db.php';

$input = json_decode(file_get_contents("php://input"), true);
$valor = isset($input['correo']) ? $input['correo'] : null;

if ($valor) {
    if (filter_var($valor, FILTER_VALIDATE_EMAIL)) {
        $query = "SELECT * FROM usuarios WHERE correo = ?";
    } elseif (preg_match('/^\d{10}$/', $valor)) {
        $query = "SELECT * FROM usuarios WHERE telefono = ?";
    } else {
        echo json_encode(['success' => false, 'mensaje' => 'Entrada inválida']);
        exit;
    }

    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $valor);
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
    echo json_encode(['success' => false, 'mensaje' => 'Valor no recibido']);
}
?>
