<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// API para crear pedido y detalle_pedido
include 'db.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
if (!$data || !isset($data['mesa']) || !isset($data['productos']) || !is_array($data['productos'])) {
    echo json_encode(['ok'=>false, 'msg'=>'Datos incompletos']);
    exit;
}

$mesa = intval($data['mesa']);
$cliente = isset($data['cliente']) ? $conn->real_escape_string($data['cliente']) : '';
$observaciones = isset($data['observaciones']) ? $conn->real_escape_string($data['observaciones']) : '';
$productos = $data['productos'];
$total = 0;
foreach ($productos as $p) {
    $total += floatval($p['cantidad']) * floatval($p['precio']);
}

// Crear pedido (ronda)
$stmt = $conn->prepare("INSERT INTO pedidos (id_mesa, fecha, hora, cliente, estado, observaciones, total) VALUES (?, CURDATE(), CURTIME(), ?, 'pendiente', ?, ?)");
$stmt->bind_param("issi", $mesa, $cliente, $observaciones, $total);
$stmt->execute();
$id_pedido = $conn->insert_id;

// Crear detalle_pedido
$stmt_det = $conn->prepare("INSERT INTO detalle_pedido (ID_Pedido, ID_Producto, cantidad, precio_unitario, total) VALUES (?, ?, ?, ?, ?)");
foreach ($productos as $p) {
    $id_producto = intval($p['id']);
    $cantidad = intval($p['cantidad']);
    $precio = floatval($p['precio']);
    $subtotal = $cantidad * $precio;
    $stmt_det->bind_param("iiidd", $id_pedido, $id_producto, $cantidad, $precio, $subtotal);
    $stmt_det->execute();
}

echo json_encode(['ok'=>true, 'msg'=>'Pedido creado', 'id_pedido'=>$id_pedido]);
