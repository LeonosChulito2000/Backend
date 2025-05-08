<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

// Suprime la salida de errores en la respuesta (solo modo desarrollo)
error_reporting(0);

// Conexión a la base de datos
include 'db.php';  // Asegúrate de que bd.php no imprima nada

if (isset($_GET['query'])) {
    $busqueda = $_GET['query'];

    // Construir la consulta
    $sql = "SELECT * FROM zapatos ";
    if ($busqueda !== "") {
        $busqueda = $conn->real_escape_string($busqueda);
        $sql .= "WHERE nombre LIKE '%$busqueda%' ";
    }
    $sql .= "ORDER BY fecha_subida DESC";

    $resultado = $conn->query($sql);
    $zapatos = array();
    while ($fila = $resultado->fetch_assoc()) {
        $zapatos[] = $fila;
    }
    echo json_encode($zapatos);
}
?>
