<?php
date_default_timezone_set('America/Bogota'); // Zona horaria Colombia
include 'db.php';

$imagenes_mesas = [
    1 => 'img/mesa1.jpg',
    2 => 'img/mesa2.jpg',
    3 => 'img/mesa3.jpg',
    4 => 'img/mesa4.jpg'
];

// Filtros de fechas
$fecha_hoy = date('Y-m-d');
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : $fecha_hoy;
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : $fecha_hoy;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_mesa = intval($_POST['id_mesa']);
    $accion = $_POST['accion'];

    if ($accion === 'iniciar') {
        $fecha = date('Y-m-d');
        $hora_inicio = date('Y-m-d H:i:s');
        $stmt = $conn->prepare("INSERT INTO mesas (id_mesa, fecha, hora_inicio) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $id_mesa, $fecha, $hora_inicio);
        $stmt->execute();
        // Redirigir para evitar reenvío de formulario y limpiar campos
        header("Location: " . strtok($_SERVER['REQUEST_URI'], '?'));
        exit;
    } elseif ($accion === 'parar') {
        $id = intval($_POST['id']);
        $hora_fin = date('Y-m-d H:i:s');
        $res = $conn->query("SELECT hora_inicio, hora_fin FROM mesas WHERE id=$id");
        $row = $res->fetch_assoc();
        // Solo actualizar si no tiene hora_fin
        if ($row && empty($row['hora_fin'])) {
            $inicio = strtotime($row['hora_inicio']);
            $fin = strtotime($hora_fin);
            $minutos = round(($fin - $inicio) / 60);
            $total = $minutos * 7000 / 60;
            $stmt = $conn->prepare("UPDATE mesas SET hora_fin=?, minutos=?, total=? WHERE id=?");
            $stmt->bind_param("siii", $hora_fin, $minutos, $total, $id);
            $stmt->execute();
        }
        // Redirigir para evitar reenvío de formulario y limpiar campos
        header("Location: " . strtok($_SERVER['REQUEST_URI'], '?'));
        exit;
    }
}

// Obtener estado actual de las mesas
$mesas = [];
for ($i = 1; $i <= 4; $i++) {
    $res = $conn->query("SELECT * FROM mesas WHERE id_mesa=$i ORDER BY id DESC LIMIT 1");
    $mesas[$i] = $res->fetch_assoc();
}

// Obtener registros filtrados por fecha
$where = "WHERE fecha >= '$fecha_inicio' AND fecha <= '$fecha_fin'";
$registros = $conn->query("SELECT * FROM mesas $where ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Control de Mesas de Billar</title>
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#fbff14">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="Terkko's Billar">
    <link rel="apple-touch-icon" href="img/logo.jpg">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="msapplication-TileImage" content="img/logo.jpg">
    <meta name="msapplication-TileColor" content="#fbff14">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/mesas.css">
    <link rel="stylesheet" href="assets/css/filtros.css">
    <link rel="stylesheet" href="assets/css/historial.css">
        <!-- Enlaces a los JS modularizados -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="assets/js/main.js"></script>
        <script src="assets/js/mesas.js"></script>
        <script src="assets/js/filtros.js"></script>
        <script src="assets/js/historial.js"></script>
    </head>
<body>
    <header>
        <div class="header-logo">
            <img src="img/logo.jpg" alt="Logo" style="height:140px; width:auto; display:block; background:none; border:none;">
        </div>
        <div class="header-title">
            Terkko's Billiard's Club
        </div>
        <nav class="menu-principal">
            <a href="index.php">Inicio</a>
            <a href="#mesasSlider">Mesas</a>
            <a href="#ventaTotalDia">Venta Total</a>
            <a href="#">Contacto</a>
        </nav>
    </header>
    <h2>Control de Tiempo Mesas de Billar</h2>
    <div class="slider-controls" id="sliderControls" style="display:none">
        <button class="slider-btn" id="sliderPrev" aria-label="Anterior">&#8592;</button>
        <button class="slider-btn" id="sliderNext" aria-label="Siguiente">&#8594;</button>
    </div>
    <div class="mesas-container" id="mesasSlider">
    <?php for ($i = 1; $i <= 4; $i++): 
        $m = $mesas[$i];
        $en_uso = $m && !$m['hora_fin'];
        $clase = $en_uso ? 'activa' : 'inactiva';
        $img = $imagenes_mesas[$i];
    ?>
    <div class="mesa <?php echo $clase; ?>"<?php if ($en_uso) echo ' data-hora-inicio="' . htmlspecialchars($m['hora_inicio']) . '"'; ?>>
        <h3>Mesa <?php echo $i; ?></h3>
        <img src="<?php echo htmlspecialchars($img); ?>" alt="Imagen Mesa <?php echo $i; ?>">
        <?php if (!$en_uso): ?>
            <form method="post" class="form-iniciar">
                <input type="hidden" name="id_mesa" value="<?php echo $i; ?>">
                <input type="hidden" name="accion" value="iniciar">
                <button type="submit" class="btn-iniciar">Iniciar Tiempo</button>
            </form>
        <?php else: ?>
            <div>Inicio: <?php echo date("g:i:s A", strtotime($m['hora_inicio'])); ?></div>
            <div class="contador" id="contador_<?php echo $i; ?>">00:00:00</div>
            <div class="costo">
                Costo actual: $<span id="costo_<?php echo $i; ?>">0</span>
            </div>
            <form method="post" class="form-parar">
                <input type="hidden" name="id" value="<?php echo $m['id']; ?>">
                <input type="hidden" name="id_mesa" value="<?php echo $i; ?>">
                <input type="hidden" name="accion" value="parar">
                <button type="submit" class="btn-parar">Parar Tiempo</button>
            </form>
            <!-- El contador y costo en tiempo real se moverán a mesas.js -->
        <?php endif; ?>
        <?php if ($m && $m['hora_fin']): ?>
            <div>Fin: <?php echo date("g:i:s A", strtotime($m['hora_fin'])); ?></div>
            <div>Minutos: <?php echo $m['minutos']; ?></div>
            <div style="font-size:1.1em;font-weight:bold;color:#111;background:#fbff14;padding:4px 0;border-radius:5px;">
                Total: $<?php echo number_format($m['total'], 0, ',', '.'); ?>
            </div>
        <?php endif; ?>
    </div>
    <?php endfor; ?>
    </div>
    <div class="filtros">
        <form method="get" class="filtros-form bg-light p-3 rounded shadow-sm d-flex flex-wrap align-items-center justify-content-center gap-3" style="max-width:700px;margin:0 auto 18px auto;">
            <div class="filtros-leyenda text-center mb-3 p-2 rounded" style="font-size:1.08em;color:#111;font-weight:500;background:#fffbe6;border:1px solid #fbff14;box-shadow:0 1px 6px #fbff14;max-width:95%;margin:0 auto 10px auto;">
                Utiliza los filtros para ver registros por fecha, día, mes o año.<br>
                Elige un rango de fechas y dale filtrar o usa los botones rápidos para ver los registros de <b>Hoy</b>/<b>Ayer</b>, o selecciona un mes específico con el botón <b>Mes</b>, y luego dale filtrar.
            </div>
            <div class="filtro-fecha d-flex flex-column align-items-start">
                <label for="fecha_inicio" class="fw-bold mb-1">Desde</label>
                <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" value="<?php echo htmlspecialchars($fecha_inicio); ?>">
            </div>
            <div class="filtro-fecha d-flex flex-column align-items-start">
                <label for="fecha_fin" class="fw-bold mb-1">Hasta</label>
                <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" value="<?php echo htmlspecialchars($fecha_fin); ?>">
            </div>
            <div class="d-flex flex-column align-items-center w-100 justify-content-center" style="margin-bottom:10px;">
                <button type="submit" class="btn btn-warning fw-bold btn-filtrar" style="box-shadow:0 2px 12px #fbff14; font-size:1.15em; padding:12px 36px; border-width:3px;">Filtrar</button>
            </div>
            <div class="btn-group justify-content-center align-items-center w-100" style="margin-bottom:10px; display:flex;">
                <a href="index.php" class="btn btn-outline-dark fw-bold">Hoy</a>
                <button type="button" id="btnAyer" class="btn btn-outline-dark fw-bold">Ayer</button>
                <button type="button" id="btnMes" class="btn btn-info fw-bold">Mes</button>
            </div>
            <div class="mes-selector-container d-flex flex-column align-items-center justify-content-center w-100" style="margin-bottom:10px;">
                <span id="mesSelector" style="display:none;">
                    <div class="d-flex flex-row align-items-center justify-content-center gap-2" style="margin:0 auto;">
                        <select id="selectAnio" class="form-select mb-1" style="min-width:90px;"></select>
                        <select id="selectMes" class="form-select" style="min-width:90px;">
                            <option value="">Mes</option>
                            <option value="01">Enero</option>
                            <option value="02">Febrero</option>
                            <option value="03">Marzo</option>
                            <option value="04">Abril</option>
                            <option value="05">Mayo</option>
                            <option value="06">Junio</option>
                            <option value="07">Julio</option>
                            <option value="08">Agosto</option>
                            <option value="09">Septiembre</option>
                            <option value="10">Octubre</option>
                            <option value="11">Noviembre</option>
                            <option value="12">Diciembre</option>
                        </select>
                    </div>
                </span>
            </div>
        </form>
    </div>
    <h3>Historial de Registros</h3>
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
    <div style="text-align:center; margin: 30px 0;">
        <button id="btnVentaTotal" style="
            background:#fbff14;
            color:#111;
            border:none;
            border-radius:7px;
            font-size:1.1em;
            font-weight:bold;
            padding:12px 32px;
            cursor:pointer;
            box-shadow:0 1px 4px rgba(0,0,0,0.08);
            transition:background 0.2s, color 0.2s;
        ">Mostrar venta total</button>
        <div id="ventaTotalDia" style="margin-top:18px; font-size:1.3em; font-weight:bold; color:#111; display:none;">
            <!-- Aquí se mostrará la venta total -->
        </div>
    </div>
    <!-- JS modularizado: main.js, mesas.js, filtros.js, historial.js -->
    <footer class="sticky-footer">
        Desarrollado por Karol Diaz - Diaztecnologia | Todos los derechos reservados &copy; 2025
    </footer>
</body>
</html>
