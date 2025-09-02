<?php
include 'db.php';
include 'controllers/PararMesasController.php';

// Prueba: parar solo las mesas activas del día actual con hora de corte actual
// Prueba limpia: parar mesas activas usando la hora actual
// Obtener la fecha de hoy y simular el cierre a las 23:58:00
// El controlador ahora calcula la hora de corte por cada mesa
$mesas_paradas = PararMesasController::pararMesasActivasHoy($conn);
echo "Mesas paradas automáticamente: $mesas_paradas (cada una con su hora de corte según fecha de inicio)\n";
?>
