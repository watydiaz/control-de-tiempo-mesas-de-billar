<?php
include 'db.php';
header('Content-Type: application/json');
$mesa = isset($_GET['mesa']) ? intval($_GET['mesa']) : 0;
if (!$mesa) {
    echo json_encode(['ok'=>false, 'msg'=>'Mesa no válida']);
    exit;
}
$res = $conn->query("SELECT ID_Pedido, cliente, fecha, hora, total FROM pedidos WHERE id_mesa=$mesa ORDER BY ID_Pedido DESC");
$pedidos = [];
$total = 0;
while($row = $res->fetch_assoc()) {
    $pedidos[] = $row;
    $total += intval($row['total']);
}
echo json_encode(['ok'=>true, 'pedidos'=>$pedidos, 'total'=>$total]);
