<?php
// Script para parar automáticamente todas las mesas activas a las 11:58 pm
date_default_timezone_set('America/Bogota');
include 'db.php';

$hora_corte = date('Y-m-d') . ' 23:58:00';

// Seleccionar todas las mesas activas (sin hora_fin)
$res = $conn->query("SELECT * FROM mesas WHERE hora_fin IS NULL");
while ($row = $res->fetch_assoc()) {
    $id = $row['id'];
    $inicio = strtotime($row['hora_inicio']);
    $fin = strtotime($hora_corte);
    $minutos = round(($fin - $inicio) / 60);
    $total = $minutos * 7000 / 60;
    $stmt = $conn->prepare("UPDATE mesas SET hora_fin=?, minutos=?, total=? WHERE id=?");
    $stmt->bind_param("siii", $hora_corte, $minutos, $total, $id);
    $stmt->execute();
}
// Opcional: log o mensaje
// echo "Mesas activas paradas automáticamente a las 11:58 pm";
?>
