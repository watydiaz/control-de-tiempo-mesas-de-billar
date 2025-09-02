<?php
$fecha_hoy = date('Y-m-d');
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : $fecha_hoy;
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : $fecha_hoy;
?>
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
