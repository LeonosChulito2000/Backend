<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json');

require_once 'db.php';

$sql = "SELECT id, codigo_barras, nombre, tallas, precio, colores, material, stock, imagen FROM zapatos ORDER BY fecha_subida DESC";
$result = $conn->query($sql);

$zapatos = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $zapatos[] = [
            'id' => $row['id'],
            'codigo_barras' => $row['codigo_barras'],
            'nombre' => $row['nombre'],
            'tallas' => $row['tallas'],
            'precio' => $row['precio'],
            'colores' => $row['colores'],
            'material' => $row['material'], 
            'stock' => $row['stock'],
            'imagen' => $row['imagen']
        ];

    }
}

echo json_encode($zapatos);
$conn->close();
?>
