// JS para la gestión de mesas
// ...aquí irán funciones relacionadas con las mesas...

$(document).ready(function() {
    // Contador y costo en tiempo real para cada mesa activa
    $('.mesa.activa').each(function() {
        var mesaDiv = $(this);
        var id = mesaDiv.find('.contador').attr('id');
        var costoId = mesaDiv.find('.costo span').attr('id');
        var inicioStr = mesaDiv.attr('data-hora-inicio');
        if (!inicioStr) return;
        var inicio = new Date(inicioStr).getTime();
        function actualizar() {
            var ahora = new Date().getTime();
            var diff = Math.floor((ahora - inicio)/1000);
            var h = Math.floor(diff/3600);
            var m = Math.floor((diff%3600)/60);
            var s = diff%60;
            $('#' + id).text((h<10?'0':'')+h+":"+(m<10?'0':'')+m+":"+(s<10?'0':'')+s);
            var minutos = diff / 60;
            var costo = Math.round(minutos * 7000 / 60);
            $('#' + costoId).text(costo.toLocaleString('es-CO'));
        }
        actualizar();
        setInterval(actualizar, 1000);
    });
});
