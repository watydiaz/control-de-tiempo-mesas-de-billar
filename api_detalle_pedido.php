<?php
include 'db.php';
header('Content-Type: application/json');
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$id) {
    echo json_encode(['ok'=>false, 'msg'=>'ID no válido']);
    exit;
}
$res = $conn->query("SELECT dp.cantidad, dp.precio_unitario, dp.total, p.Nombre as producto FROM detalle_pedido dp JOIN productos p ON dp.ID_Producto = p.ID_Producto WHERE dp.ID_Pedido = $id");
$detalle = [];
while($row = $res->fetch_assoc()) {
    $detalle[] = $row;
}
echo json_encode(['ok'=>true, 'detalle'=>$detalle]);
