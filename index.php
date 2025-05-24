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
    <style>
        @import url('https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap');
        body {
            font-family: 'Roboto', Arial, sans-serif;
            background: #fff;
            margin: 0;
            padding: 0;
        }
        header {
            width: 100%;
            background: #fbff14;
            color: #111;
            padding: 0;
            text-align: center;
            font-size: 2em;
            font-weight: bold;
            letter-spacing: 2px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            position: sticky;
            top: 0;
            z-index: 100;
            display: block;
            min-height: 70px;
        }
        .header-logo {
            height: 164px;
            width: 164px;
            margin: 0 auto 0 auto;
            background: transparent;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .header-title {
            text-align: center;
            font-size: 1.32em;
            font-family: 'Roboto', 'Bebas Neue', 'Oswald', 'Impact', 'Segoe UI', Arial, sans-serif;
            font-weight: 900;
            letter-spacing: 4px;
            color: #111;
            margin-top: 10px;
            margin-bottom: 0;
            text-shadow: 2px 2px 0 #fbff14, 0 0 2px #fff;
        }
        @media (max-width: 600px) {
            header {
                font-size: 1.1em;
                min-height: 60px;
            }
            .header-logo {
                margin: 8px auto 0 auto;
            }
            .header-title {
                font-size: 0.52em;
                margin: 8px 0 0 0;
            }
        }
        h2 {
            text-align: center;
            margin-top: 30px;
            color: #111;
            letter-spacing: 1px;
        }
        .mesas-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 24px;
            margin: 30px 0 10px 0;
        }
        .mesa {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.07);
            padding: 22px 18px 18px 18px;
            min-width: 420px;
            max-width: 540px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        .mesa.inactiva {
            border: 3px solid #1ed760; /* verde disponible en todo el contorno */
            background: #fff;
            color: #111;
        }
        .mesa.activa {
            border: 3px solid #e53935; /* rojo ocupado en todo el contorno */
            background: #fff;
            color: #111;
        }
        .mesa.activa h3,
        .mesa.activa .contador,
        .mesa.activa .costo,
        .mesa.activa > div,
        .mesa.activa form label,
        .mesa.activa form button {
            color: #111 !important;
        }
        .mesa.inactiva h3,
        .mesa.inactiva .contador,
        .mesa.inactiva .costo,
        .mesa.inactiva > div,
        .mesa.inactiva form label,
        .mesa.inactiva form button {
            color: #111 !important;
        }
        .mesa h3 {
            margin: 0 0 10px 0;
            font-size: 1.3em;
            color: #111;
            letter-spacing: 1px;
        }
        .mesa img {
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        .mesa > div,
        .mesa form {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .mesa form {
            margin: 10px 0 0 0;
        }
        .mesa button {
            /* Botón con contraste en ambos fondos */
            background: #fbff14;
            color: #111;
            border: none;
            border-radius: 8px;
            padding: 12px 28px;
            font-size: 1.08em;
            font-weight: 700;
            box-shadow: 0 2px 8px rgba(0,0,0,0.10);
            transition: background 0.2s, color 0.2s, transform 0.1s;
            margin-top: 10px;
            letter-spacing: 1px;
            outline: none;
            display: inline-block;
        }
        .mesa button:active {
            transform: scale(0.97);
        }
        .mesa .btn-iniciar {
            background: #1ed760;
            color: #fff;
        }
        .mesa .btn-iniciar:hover {
            background: #13b94d;
            color: #fff;
        }
        .mesa .btn-parar {
            background: #e53935;
            color: #fff;
        }
        .mesa .btn-parar:hover {
            background: #b71c1c;
            color: #fff;
        }
        .contador {
            font-size: 1.3em;
            font-weight: bold;
            color: #111;
            margin: 8px 0 2px 0;
            letter-spacing: 1px;
        }
        .mesa .costo {
            /* Ajuste para contraste en ambos fondos */
            text-shadow: 1px 1px 0 #111, 0 0 2px #fff;
        }
        .mesa.activa .costo {
            color: #fbff14;
            text-shadow: 1px 1px 0 #111, 0 0 2px #fff;
        }
        .mesa.inactiva .costo {
            color: #111;
            text-shadow: 1px 1px 0 #fbff14, 0 0 2px #fff;
        }
        .mesa > div {
            margin-bottom: 4px;
        }
        .filtros {
            margin: 30px auto 10px auto;
            text-align: center;
        }
        .filtros form {
            display: inline-block;
            background: #fff;
            padding: 12px 18px;
            border-radius: 10px;
            box-shadow: 0 1px 8px rgba(0,0,0,0.06);
            border: 2px solid #fbff14;
        }
        .filtros label {
            margin: 0 8px;
            font-size: 1em;
            color: #111;
        }
        .filtros input[type="date"] {
            padding: 4px 8px;
            border-radius: 5px;
            border: 1px solid #fbff14;
            font-size: 1em;
            margin-left: 2px;
            background: #fff;
            color: #111;
        }
        .filtros button {
            background: #fbff14;
            color: #111;
            border: none;
            border-radius: 5px;
            padding: 6px 16px;
            font-size: 1em;
            margin-left: 8px;
            cursor: pointer;
            transition: background 0.2s, color 0.2s;
        }
        .filtros button:hover {
            background: #111;
            color: #fbff14;
        }
        .filtros a button {
            background: #111;
            color: #fbff14;
            margin-left: 8px;
        }
        .filtros a button:hover {
            background: #fbff14;
            color: #111;
        }
        h3 {
            text-align: center;
            margin-top: 40px;
            color: #111;
            letter-spacing: 1px;
        }
        table {
            width: 98%;
            margin: 0 auto 30px auto;
            border-collapse: collapse;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 1px 8px rgba(0,0,0,0.06);
            border: 2px solid #111;
        }
        th {
            background: #fbff14;
            color: #111;
            font-weight: 600;
            padding: 10px 4px;
            font-size: 1em;
            border: 1px solid #111;
            text-align: left;
        }
        td {
            border: 1px solid #111;
            padding: 8px 4px;
            font-size: 1em;
            color: #111;
            text-align: left;
            background: #fff;
        }
        tr:nth-child(even) td {
            background: #f9f9f9;
        }
        @media (max-width: 900px) {
            .mesas-container {
                flex-direction: column;
                align-items: center;
            }
            .mesa {
                max-width: 98vw;
                min-width: 220px;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="header-logo">
            <img src="img/logo.jpg" alt="Logo" style="height:140px; width:auto; display:block; background:none; border:none;">
        </div>
        <div class="header-title">
            Terkko's Billiard's Club
        </div>
    </header>
    <h2>Control de Tiempo Mesas de Billar</h2>
    <div class="mesas-container">
    <?php for ($i = 1; $i <= 4; $i++): 
        $m = $mesas[$i];
        $en_uso = $m && !$m['hora_fin'];
        $clase = $en_uso ? 'activa' : 'inactiva';
        $img = $imagenes_mesas[$i];
    ?>
    <div class="mesa <?php echo $clase; ?>">
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
            <script>
            // Contador y costo en tiempo real
            (function(){
                var inicio = new Date("<?php echo $m['hora_inicio']; ?>").getTime();
                function actualizar() {
                    var ahora = new Date().getTime();
                    var diff = Math.floor((ahora - inicio)/1000);
                    var h = Math.floor(diff/3600);
                    var m = Math.floor((diff%3600)/60);
                    var s = diff%60;
                    document.getElementById('contador_<?php echo $i; ?>').textContent =
                        (h<10?'0':'')+h+":"+(m<10?'0':'')+m+":"+(s<10?'0':'')+s;
                    // Calcular costo actual
                    var minutos = diff / 60;
                    var costo = Math.round(minutos * 7000 / 60);
                    document.getElementById('costo_<?php echo $i; ?>').textContent = costo.toLocaleString('es-CO');
                }
                actualizar();
                setInterval(actualizar, 1000);
            })();
            </script>
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
        <form method="get">
            <label>Desde: <input type="date" name="fecha_inicio" value="<?php echo htmlspecialchars($fecha_inicio); ?>"></label>
            <label>Hasta: <input type="date" name="fecha_fin" value="<?php echo htmlspecialchars($fecha_fin); ?>"></label>
            <button type="submit">Filtrar</button>
            <a href="index.php"><button type="button">Hoy</button></a>
        </form>
    </div>
    <h3>Historial de Registros</h3>
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
    <script>
    // Confirmación al iniciar tiempo
    document.querySelectorAll('.form-iniciar').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            if (!confirm('¿Está seguro de INICIAR el tiempo de esta mesa?')) {
                e.preventDefault();
            }
        });
    });
    // Confirmación al parar tiempo
    document.querySelectorAll('.form-parar').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            if (!confirm('¿Está seguro de PARAR el tiempo de esta mesa?')) {
                e.preventDefault();
            }
        });
    });
    document.getElementById('btnVentaTotal').onclick = function() {
        var pin = prompt('Ingrese el PIN para ver la venta total:');
        if (pin === '8910') {
            // Obtener la suma de los totales de la tabla
            var suma = 0;
            var filas = document.querySelectorAll('table tr');
            for (var i = 1; i < filas.length; i++) {
                var celdas = filas[i].getElementsByTagName('td');
                if (celdas.length > 6) {
                    var valor = celdas[6].textContent.replace(/[^0-9]/g, '');
                    suma += parseInt(valor) || 0;
                }
            }
            var div = document.getElementById('ventaTotalDia');
            div.textContent = 'Venta total: $' + suma.toLocaleString('es-CO');
            div.style.display = 'block';
        } else if (pin !== null) {
            alert('PIN incorrecto');
        }
    };
    </script>
</body>
</html>
