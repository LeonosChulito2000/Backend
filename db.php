<?php
// Backend/db.php

$host = "localhost";
$user = "root";
$password = "";
$database = "shoetrack";

// Crear conexión
$conn = new mysqli($host, $user, $password, $database);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
