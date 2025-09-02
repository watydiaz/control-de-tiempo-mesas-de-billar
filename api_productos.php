<?php
// Devuelve la lista de productos en formato JSON para el modal de selección
include 'db.php';
header('Content-Type: application/json');
$res = $conn->query("SELECT ID_Producto, Nombre, Precio_Venta FROM productos WHERE Precio_Venta > 0");
$productos = [];
while($row = $res->fetch_assoc()) {
    $productos[] = $row;
}
echo json_encode($productos);
