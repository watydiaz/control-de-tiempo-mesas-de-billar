<?php
// Controlador para parar solo las mesas activas del día actual
class PararMesasController {
    public static function pararMesasActivasHoy($conn, $hora_corte = null) {
        // Asegurar zona horaria Colombia
        date_default_timezone_set('America/Bogota');
        if (!$hora_corte) {
            $hora_corte = date('Y-m-d') . ' 23:58:00'; // Default cut-off time
        }
        $fecha_hoy = date('Y-m-d');
        $res = $conn->query("SELECT * FROM mesas WHERE hora_fin IS NULL AND fecha <= '$fecha_hoy'");
        $count = 0;
        while ($row = $res->fetch_assoc()) {
            $id = $row['id'];
            // Hora de corte según fecha de inicio de la mesa
            $fecha_inicio_mesa = date('Y-m-d', strtotime($row['hora_inicio']));
            $hora_corte_mesa = $fecha_inicio_mesa . ' 23:58:00';
            $inicio = strtotime($row['hora_inicio']);
            $fin = strtotime($hora_corte_mesa);
            $minutos = round(($fin - $inicio) / 60);
            $total = $minutos * 7000 / 60;
            $stmt = $conn->prepare("UPDATE mesas SET hora_fin=?, minutos=?, total=? WHERE id=?");
            $stmt->bind_param("siii", $hora_corte_mesa, $minutos, $total, $id);
            $stmt->execute();
            $count++;
        }
        return $count;
    }
}
?>
