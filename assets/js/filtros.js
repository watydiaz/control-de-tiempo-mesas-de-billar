// JS para filtros y búsqueda
// ...aquí irán funciones relacionadas con los filtros...

$(document).ready(function() {
    // Selector de año dinámico (últimos 5 años y actual)
    var anioSel = $('#selectAnio');
    if (anioSel.length) {
        var actual = new Date().getFullYear();
        for (var y = actual; y >= actual-5; y--) {
            anioSel.append($('<option>', {value: y, text: y}));
        }
    }
    // Mostrar/ocultar selector de mes (corregido)
    $('#btnMes').on('click', function() {
        var ms = $('#mesSelector');
        if (ms.css('display') === 'none' || ms.css('display') === '') {
            ms.css('display', 'flex');
        } else {
            ms.css('display', 'none');
        }
    });
    // Al seleccionar año y mes, poner fechas en los inputs
    $('#selectMes, #selectAnio').on('change', function() {
        var anio = $('#selectAnio').val();
        var mes = $('#selectMes').val();
        if (anio && mes) {
            var primerDia = anio + '-' + mes + '-01';
            var ultimoDia;
            if (mes === '02') {
                var esBisiesto = (anio % 4 === 0 && (anio % 100 !== 0 || anio % 400 === 0));
                ultimoDia = anio + '-' + mes + '-' + (esBisiesto ? '29' : '28');
            } else if (["04","06","09","11"].includes(mes)) {
                ultimoDia = anio + '-' + mes + '-30';
            } else {
                ultimoDia = anio + '-' + mes + '-31';
            }
            $('#fecha_inicio').val(primerDia);
            $('#fecha_fin').val(ultimoDia);
        }
    });
    // Botón 'Ayer'
    $('#btnAyer').on('click', function() {
        var ayer = new Date();
        ayer.setDate(ayer.getDate() - 1);
        var yyyy = ayer.getFullYear();
        var mm = ('0' + (ayer.getMonth() + 1)).slice(-2);
        var dd = ('0' + ayer.getDate()).slice(-2);
        var fecha = yyyy + '-' + mm + '-' + dd;
        $('#fecha_inicio').val(fecha);
        $('#fecha_fin').val(fecha);
    });
});
