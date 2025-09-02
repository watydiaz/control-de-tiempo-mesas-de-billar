<?php
include 'db.php';
$fecha_hoy = date('Y-m-d');
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : $fecha_hoy;
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : $fecha_hoy;
$where = "WHERE fecha >= '$fecha_inicio' AND fecha <= '$fecha_fin'";
$registros = $conn->query("SELECT * FROM mesas $where ORDER BY id DESC");
?>
<div class="table-responsive">
<table>
    <tr>
        <th>ID</th>
        <th>Mesa</th>
        <th>Fecha</th>
        <th>Inicio</th>
        <th>Fin</th>
        <th>Minutos</th>
        <th>Total</th>
    </tr>
    <?php while($r = $registros->fetch_assoc()): ?>
    <tr>
        <td><?php echo $r['id']; ?></td>
        <td><?php echo $r['id_mesa']; ?></td>
        <td><?php echo $r['fecha']; ?></td>
        <td><?php echo $r['hora_inicio'] ? date("g:i:s A", strtotime($r['hora_inicio'])) : ''; ?></td>
        <td><?php echo $r['hora_fin'] ? date("g:i:s A", strtotime($r['hora_fin'])) : ''; ?></td>
        <td><?php echo $r['minutos']; ?></td>
        <td>$<?php echo number_format($r['total'], 0, ',', '.'); ?></td>
    </tr>
    <?php endwhile; ?>
</table>
</div>
