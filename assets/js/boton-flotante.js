var productos = [];

$(document).ready(function(){
  $.getJSON('api_productos.php', function(data){
    productos = data;
  });

  // Eliminar botón flotante
  $('#botonFlotanteCrearPedido').remove();

  // Abrir modal desde cada mesa y seleccionar la mesa
  $('.btn-crear-ronda').on('click', function(){
    var mesa = $(this).data('mesa');
    $('#modalCrearPedido').fadeIn(180);
    $('#tablaProductosRonda tbody').empty();
    $('#totalRonda').text('0');
    $('#inputMesaSeleccionada').val('Mesa ' + mesa).data('mesa', mesa);
  });

  $('#cerrarModalCrearPedido').on('click', function(){
    $('#modalCrearPedido').fadeOut(120);
  });
  $('#modalCrearPedido').on('click', function(e){
    if(e.target === this){
      $(this).fadeOut(120);
    }
  });

  $('#agregarProductoRonda').on('click', function(){
    agregarFilaProducto(productos);
  });

  // Delegación para cambios en producto, cantidad, precio
  $('#tablaProductosRonda').on('change', '.select-producto', function(){
    const precio = $(this).find('option:selected').data('precio') || 0;
    $(this).closest('tr').find('.precio').val(precio);
    $(this).closest('tr').find('.precio').trigger('change');
  });
  $('#tablaProductosRonda').on('input change', '.cantidad, .precio', function(){
    const tr = $(this).closest('tr');
    const cantidad = parseInt(tr.find('.cantidad').val()) || 0;
    const precio = parseFloat(tr.find('.precio').val()) || 0;
    const subtotal = cantidad * precio;
    tr.find('.subtotal').text(subtotal);
    actualizarTotalRonda();
  });
  $('#tablaProductosRonda').on('click', '.eliminar-producto', function(){
    $(this).closest('tr').remove();
    actualizarTotalRonda();
  });

  $('#formCrearPedido').on('submit', function(e){
    e.preventDefault();
    const mesa = $('#inputMesaSeleccionada').data('mesa');
    const cliente = $('#inputCliente').val();
    const observaciones = $('#inputObservaciones').val();
    let productos = [];
    $('#tablaProductosRonda tbody tr').each(function(){
      const id = $(this).find('.select-producto').val();
      const nombre = $(this).find('.select-producto option:selected').text();
      const cantidad = parseInt($(this).find('.cantidad').val()) || 0;
      const precio = parseFloat($(this).find('.precio').val()) || 0;
      if(id && cantidad > 0 && precio > 0) {
        productos.push({id, nombre, cantidad, precio});
      }
    });
    if(!mesa || productos.length === 0) {
      alert('Selecciona la mesa y al menos un producto válido.');
      return;
    }
    $.ajax({
      url: 'api_crear_pedido.php',
      method: 'POST',
      contentType: 'application/json',
      data: JSON.stringify({mesa, cliente, observaciones, productos}),
      success: function(resp) {
        if(resp.ok) {
          alert('Pedido creado exitosamente.');
          $('#modalCrearPedido').fadeOut(120);
        } else {
          alert('Error: ' + (resp.msg || 'No se pudo crear el pedido.'));
        }
      },
      error: function() {
        alert('Error de conexión al crear el pedido.');
      }
    });
  });

  $('.btn-ver-pedidos').on('click', function(){
    var mesa = $(this).data('mesa');
    $('#modalPedidosMesaNum').text(mesa);
    $('#modalPedidosMesaLista').html('Cargando...');
    $('#modalPedidosMesaTotal').text('0');
    $('#modalPedidosMesa').fadeIn(180);
    $.getJSON('api_pedidos_mesa.php?mesa=' + mesa, function(resp){
      if(resp.ok && resp.pedidos.length > 0) {
        let html = '<table style="width:100%;margin-bottom:8px;"><thead><tr><th>ID</th><th>Cliente</th><th>Fecha</th><th>Hora</th><th>Total</th><th>Acción</th></tr></thead><tbody>';
        resp.pedidos.forEach(function(p){
          html += `<tr><td>${p.ID_Pedido}</td><td>${p.cliente||''}</td><td>${p.fecha}</td><td>${p.hora}</td><td>$${parseInt(p.total).toLocaleString('es-CO')}</td><td><button class='btn-detalle-pedido' data-id='${p.ID_Pedido}' style='background:#2ecc40;color:#fff;border-radius:5px;padding:3px 10px;font-size:0.95em;'>Ver detalle</button></td></tr>`;
        });
        html += '</tbody></table>';
        $('#modalPedidosMesaLista').html(html);
        $('#modalPedidosMesaTotal').text(parseInt(resp.total).toLocaleString('es-CO'));
      } else {
        $('#modalPedidosMesaLista').html('No hay pedidos registrados para esta mesa.');
        $('#modalPedidosMesaTotal').text('0');
      }
    });
  });

  // Modal detalle de productos
  $(document).on('click', '.btn-detalle-pedido', function(){
    var id_pedido = $(this).data('id');
    $('#modalDetallePedidoLista').html('Cargando...');
    $('#modalDetallePedido').fadeIn(180);
    $.getJSON('api_detalle_pedido.php?id=' + id_pedido, function(resp){
      if(resp.ok && resp.detalle.length > 0) {
        let html = '<table style="width:100%;margin-bottom:8px;"><thead><tr><th>Producto</th><th>Cantidad</th><th>Precio</th><th>Subtotal</th></tr></thead><tbody>';
        resp.detalle.forEach(function(d){
          html += `<tr><td>${d.producto}</td><td>${d.cantidad}</td><td>$${parseInt(d.precio_unitario).toLocaleString('es-CO')}</td><td>$${parseInt(d.total).toLocaleString('es-CO')}</td></tr>`;
        });
        html += '</tbody></table>';
        $('#modalDetallePedidoLista').html(html);
      } else {
        $('#modalDetallePedidoLista').html('No hay productos en este pedido.');
      }
    });
  });
  $('#cerrarModalPedidosMesa').on('click', function(){
    $('#modalPedidosMesa').fadeOut(120);
  });
  $('#modalPedidosMesa').on('click', function(e){
    if(e.target === this){
      $(this).fadeOut(120);
    }
  });
  $('#cerrarModalDetallePedido').on('click', function(){
    $('#modalDetallePedido').fadeOut(120);
  });
  $('#modalDetallePedido').on('click', function(e){
    if(e.target === this){
      $(this).fadeOut(120);
    }
  });
});

function actualizarTotalRonda() {
  let total = 0;
  $('#tablaProductosRonda tbody tr').each(function(){
    const subtotal = parseFloat($(this).find('.subtotal').text()) || 0;
    total += subtotal;
  });
  $('#totalRonda').text(total.toLocaleString('es-CO'));
}

function agregarFilaProducto(productos) {
  const select = $('<select class="select-producto" required></select>');
  select.append('<option value="">Producto</option>');
  productos.forEach(p => {
    select.append(`<option value="${p.ID_Producto}" data-precio="${p.Precio_Venta}">${p.Nombre}</option>`);
  });
  const fila = $('<tr></tr>');
  fila.append($('<td></td>').append(select));
  fila.append('<td><input type="number" min="1" value="1" class="cantidad" style="width:60px;"></td>');
  fila.append('<td><input type="number" min="0" value="0" class="precio" style="width:80px;"></td>');
  fila.append('<td class="subtotal">0</td>');
  fila.append('<td><button type="button" class="eliminar-producto" style="color:#c00;font-weight:bold;">&times;</button></td>');
  $('#tablaProductosRonda tbody').append(fila);
}
