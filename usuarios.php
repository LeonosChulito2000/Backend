<?php
// Backend/usuarios.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

include("db.php");

$method = $_SERVER['REQUEST_METHOD'];

if ($method == "POST") {
    $data = json_decode(file_get_contents("php://input"), true);
    $accion = $data['accion'] ?? '';

    if ($accion == "buscar") {
        $correo = $data['correo'];
        $result = $conn->query("SELECT * FROM usuarios WHERE correo = '$correo'");

        if ($result->num_rows > 0) {
            $usuario = $result->fetch_assoc();
            echo json_encode(["success" => true, "usuario" => $usuario]);
        } else {
            echo json_encode(["success" => false, "message" => "No se encontró el usuario."]);
        }
    }

    if ($accion == "registrar") {
        $nombres = $data['nombres'];
        $apellido_paterno = $data['apellido_paterno'];
        $apellido_materno = $data['apellido_materno'];
        $correo = $data['correo'];
        $telefono = $data['telefono'];
        $fecha_registro = date('Y-m-d');
        $direccion_envio = $data['direccion_envio'];
        $numero_pedidos = 1;

        $sql = "INSERT INTO usuarios (nombres, apellido_paterno, apellido_materno, correo, telefono, fecha_registro, direccion_envio, numero_pedidos)
                VALUES ('$nombres', '$apellido_paterno', '$apellido_materno', '$correo', '$telefono', '$fecha_registro', '$direccion_envio', $numero_pedidos)";

        if ($conn->query($sql) === TRUE) {
            echo json_encode(["success" => true, "message" => "Usuario registrado exitosamente."]);
        } else {
            echo json_encode(["success" => false, "message" => "Error: " . $conn->error]);
        }
    }

    if ($accion == "actualizar") {
        $id = $data['id'];
        $nombres = $data['nombres'];
        $apellido_paterno = $data['apellido_paterno'];
        $apellido_materno = $data['apellido_materno'];
        $correo = $data['correo'];
        $telefono = $data['telefono'];
        $direccion_envio = $data['direccion_envio'];
        $numero_pedidos = $data['numero_pedidos'];

        $sql = "UPDATE usuarios SET 
                nombres = '$nombres',
                apellido_paterno = '$apellido_paterno',
                apellido_materno = '$apellido_materno',
                correo = '$correo',
                telefono = '$telefono',
                direccion_envio = '$direccion_envio',
                numero_pedidos = $numero_pedidos
                WHERE id = $id";

        if ($conn->query($sql) === TRUE) {
            echo json_encode(["success" => true, "message" => "Usuario actualizado exitosamente."]);
        } else {
            echo json_encode(["success" => false, "message" => "Error al actualizar: " . $conn->error]);
        }
    }
}

$conn->close();
?>
