<?php
// Renderiza el grid de mesas y sus botones
include 'db.php';
$imagenes_mesas = [
    1 => 'img/mesa1.jpg',
    2 => 'img/mesa2.jpg',
    3 => 'img/mesa3.jpg',
    4 => 'img/mesa4.jpg'
];
$mesas = [];
for ($i = 1; $i <= 4; $i++) {
    $res = $conn->query("SELECT * FROM mesas WHERE id_mesa=$i ORDER BY id DESC LIMIT 1");
    $mesas[$i] = $res->fetch_assoc();
}
?>
<div class="mesas-container" id="mesasSlider">
<?php for ($i = 1; $i <= 4; $i++): 
    $m = $mesas[$i];
    $en_uso = $m && !$m['hora_fin'];
    $clase = $en_uso ? 'activa' : 'inactiva';
    $img = $imagenes_mesas[$i];
?>
<div class="mesa <?php echo $clase; ?>"<?php if ($en_uso) echo ' data-hora-inicio="' . htmlspecialchars($m['hora_inicio']) . '"'; ?> >
    <h3>Mesa <?php echo $i; ?></h3>
    <img src="<?php echo htmlspecialchars($img); ?>" alt="Imagen Mesa <?php echo $i; ?>">
    <?php if (!$en_uso): ?>
        <form method="post" class="form-iniciar">
            <input type="hidden" name="id_mesa" value="<?php echo $i; ?>">
            <input type="hidden" name="accion" value="iniciar">
            <button type="submit" class="btn-iniciar">Iniciar Tiempo</button>
        </form>
        <button class="btn-pedidos" data-mesa="<?php echo $i; ?>" style="background:#fffbe6;color:#111;border:2px solid #fbff14;border-radius:7px;padding:7px 18px;font-weight:bold;cursor:pointer;margin-bottom:6px;">Ver pedidos</button>
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
        <button class="btn-pedidos" data-mesa="<?php echo $i; ?>" style="background:#fffbe6;color:#111;border:2px solid #fbff14;border-radius:7px;padding:7px 18px;font-weight:bold;cursor:pointer;margin-bottom:6px;">Ver pedidos</button>
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
