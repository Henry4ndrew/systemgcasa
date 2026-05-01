<?php
session_start();
date_default_timezone_set('America/La_Paz');
require '../includes/conexion.php';


//realizar con transaccion para revertir si no se insertan los datos correctamente

//SECCION CLIENTES
   <input type="hidden" name="id_cliente" id="id_cliente" readonly> //id_liente dato opcional que puede ser nulo

   si no existe id_cliente:
   Insertamos los datos:
    <input class="input pd" type="text" name="nameCliente" id="nameCliente" required>
    <input class="input pd" type="text" id="empresaCliente" name="empresaCliente">
    <input class="input pd" type="text" name="nit" id="nit">
    <input class="input pd" type="text" name="carnetCliente" id="carnetCliente" placeholder="Documento de identidad">
    <select class="select pd" id="departamento" name="departamento" required>
    <input class="input pd" type="email" id="correo" name="correo" placeholder="Ej: correo_@gmail.com">
    <input class="input pd" type="number" name="celCliente" id="celCliente"> //Se ingresa en la columna celular
    <input class="input pd" type="number" name="celEmpresa" id="celEmpresa">
    <textarea class="input pd rad7" id="detailCLienteProd" name="note_client" placeholder="Nota acerca del cliente" rows="6" maxlength="1000"></textarea>
    //en la columna fecha_registro se inserta la fecha actual
      en la tabla cartera_clientes (id_cliente, nombre, nit, carnet_ci, departamento, celular, cel_empresa, correo, empresa, nota, fecha_registro
    si existe id_cliente 
    actualizamos todos los datos recibidos en la fila de id_cliente recibido

//SECCION COTIZACION

  <input class="input pd" type="text" id="tituloCotizacion" name="tituloCotizacion" placeholder="Título de cotización">           
  <input class="input pd" type="date" id="fechaEntrega" name="fechaCaducidad"> //puede ser un dato nulo o vacio 
  <select class="select pd" name="cuenta_bancaria" id="cuenta_bancaria"> //dato numerico entero que puede ser nuelo o vacio
  $aprobado = "no" //es un dato por defecto que siempre sera "no"
  fecha_cotizacion //la fecha actual
  <select class="select pd" name="piePagina" id="opcionesCotiz" required> //se inserta en la columna id_dataPiePag

  en la tabla cotizaciones (id_cotizacion, titulo, fecha_caducidad, cuenta_bancaria, aprobado, fecha_cotizacion, id_dataPiePag)

//SEECION VENTA
$idCotizacion = el id generado al crear la nueva fila en la seccion de cotizacion
 <input type="hidden" name="idVendedor" id="idVendedor" readonly> //Se inserta en la columna id_user
<textarea class="input pd rad7" rows="4" name="nota" id="nota-cotiz" placeholder="Aparece en la cotización"></textarea>
<input class="input-none f-white f-med" type="text" name="total_venta" id="totalConDescuento" value="0" readonly>

insertamos en la tabla ventas (id_venta, id_cotizacion, id_user, nota, total_venta)     


si se ejecuta correctamente la venta insertamos los detalles de la venta (insertamos el id_venta generado)
            // Insertar datos en la tabla detalle_venta
            if (isset($_POST['codigo']) && is_array($_POST['codigo'])) {
                foreach ($_POST['codigo'] as $index => $codigoProducto) {
                    $idDetalle = $_POST['idDetalle'][$index];
                    $precio = $_POST['precio'][$index];
                    $cantidad = $_POST['cantidad'][$index];
                    $subtotal = $_POST['subtotal'][$index];
                    $detallesAdicionales = $_POST['descripcion'][$index] ?? '';
                    $caracteristicaAdicional = $_POST['caracteristica'][$index] ?? '';

                    $sqlDetalle = "INSERT INTO detalle_venta (id_venta, codigo, id_detalle, precio_venta, cantidad, sub_total, newDetail, newCaracteristic) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmtDetalle = $conexion->prepare($sqlDetalle);
                    $stmtDetalle->bind_param("isiidiss", $idVenta, $codigoProducto, $idDetalle, $precio, $cantidad, $subtotal,  $detallesAdicionales, $caracteristicaAdicional);

                    if (!$stmtDetalle->execute()) {
                        $_SESSION['mensaje'] = "Error al registrar el detalle de la venta: " . $stmtDetalle->error;
                        header("Location: ../b1t.php?p=ventas_registro.php");
                        exit;
                    }
                }
            }

            $_SESSION['mensaje'] = "Cotizacoin registrada correctamente.";
            header("Location: ../b1t.php?p=cotiz_crear.php");
            exit;

            //cerrar conexion

   



?>


























<?php
session_start();
date_default_timezone_set('America/La_Paz');
require '../includes/conexion.php';


//imprmir en pantalla los datos recibidos

//SECCION CLIENTES
   <input type="hidden" name="id_cliente" id="id_cliente" readonly> 
    <input class="input pd" type="text" name="nameCliente" id="nameCliente" required>
    <input class="input pd" type="text" id="empresaCliente" name="empresaCliente">
    <input class="input pd" type="text" name="nit" id="nit">
    <input class="input pd" type="text" name="carnetCliente" id="carnetCliente" placeholder="Documento de identidad">
    <select class="select pd" id="departamento" name="departamento" required>
    <input class="input pd" type="email" id="correo" name="correo" placeholder="Ej: correo_@gmail.com">
    <input class="input pd" type="number" name="celCliente" id="celCliente">
    <input class="input pd" type="number" name="celEmpresa" id="celEmpresa">
    <textarea class="input pd rad7" id="detailCLienteProd" name="note_client" placeholder="Nota acerca del cliente" rows="6" maxlength="1000"></textarea>


//SECCION COTIZACION

  <input class="input pd" type="text" id="tituloCotizacion" name="tituloCotizacion" placeholder="Título de cotización">           
  <input class="input pd" type="date" id="fechaEntrega" name="fechaCaducidad">
  <select class="select pd" name="cuenta_bancaria" id="cuenta_bancaria">
  fecha_cotizacion 
  <select class="select pd" name="piePagina" id="opcionesCotiz" required> 


//SEECION VENTA
$idCotizacion = el id generado al crear la nueva fila en la seccion de cotizacion
 <input type="hidden" name="idVendedor" id="idVendedor" readonly> 
<textarea class="input pd rad7" rows="4" name="nota" id="nota-cotiz" placeholder="Aparece en la cotización"></textarea>
<input class="input-none f-white f-med" type="text" name="total_venta" id="totalConDescuento" value="0" readonly>



//SECCION DETALLE DE VENTA (son varios productos agreados dinamicamente al fomrulario)
  div.innerHTML = `
    <div class="item-agregado flex-between">
      <div class="w20 column centrar">
        <img src="${escapeHtml(imagenSrc)}" alt="${escapeHtml(nombreProducto)}" class="img-product-carro">
      </div>
      <div class="column w60 content-list-prod" style="padding-left:7px;">
          <h4 class="f-center f-plomo">${escapeHtml(nombreProducto)}</h4>
          <div class="gap05"><b class="f-peq f-999">Detalle:</b><p class="f-peq">${escapeHtml(nombreDetalle)}</p></div>
          <div class="gap05"><b class="f-peq f-999">Código:</b><input class="input-none" type="text" name="codigo[]" value="${escapeHtml(codigo)}" readonly></div>
          <input type="hidden" name="idDetalle[]" value="${escapeHtml(idDetalle)}" readonly>
          <div class="gap05"><b class="f-peq f-999">Precio:</b><input class="input-none" type="text" name="precio[]" value="${escapeHtml(precioFormateado)}" oninput="calcularSubTotal(this)" readonly></div>
          <div class="centrar">
              <div class="w60 column">
                <p class="f-peq f-999">Detalle:</p>
                <textarea class="txtArea-none" name="descripcion[]" rows="3" readonly>${escapeHtml(descripcion)}</textarea>
              </div>
              <div class="w40 column">
                <p class="f-peq f-999">Caract. modif:</p>
                <textarea class="txtArea-none" name="caracteristica[]" rows="3" readonly>${escapeHtml(caracteristica)}</textarea>
              </div>
          </div>
      </div>

        <div class="w20 column gap10 centrar">
          <button type="button" class="btn-remove" onclick="deleteProdList(this)"><i class="fa-solid fa-trash eliminar-icono"></i></button>
          <div class="pad0-5 f-peq">SubTotal: <input class="input-none" style="width:50px;" value="${precioFormateado}" name="subtotal[]" readonly> </div>
           <div>
              <button type="button" class="btn-load azul" onclick="cantidad(this, 'suma')"><span><i class="fas fa-plus"></i></span></button>
              <input type="text" name="cantidad[]" value="1" oninput="soloNumInt(this); calcularSubTotal(this)" class="soloInput f-center input-cantidad">
              <button type="button" class="btn-load azul" onclick="cantidad(this, 'resta')"><span><i class="fas fa-minus"></i></span></button>
           </div>
        </div>
    </div>
  `;





            $_SESSION['mensaje'] = "Cotizacoin registrada correctamente.";
            header("Location: ../b1t.php?p=cotiz_crear.php");
            exit;

            //cerrar conexion

   



?>