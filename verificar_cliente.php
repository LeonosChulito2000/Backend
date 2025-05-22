<?php
// verificar_cliente.php

header('Content-Type: application/json');
require_once 'db.php';

// Validar que se haya enviado el correo
if (!isset($_GET['correo'])) {
    echo json_encode(['error' => 'Correo no proporcionado']);
    exit;
}

$correo = trim($_GET['correo']);

// Validar formato de correo
if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['error' => 'Formato de correo inválido']);
    exit;
}

// Preparar y ejecutar la consulta
$stmt = $conn->prepare("SELECT id FROM usuarios WHERE correo = ?");
$stmt->bind_param("s", $correo);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode([
        'existe' => true,
        'id_cliente' => $row['id']
    ]);
} else {
    echo json_encode([
        'existe' => false
    ]);
}

$stmt->close();
$conn->close();
?>
