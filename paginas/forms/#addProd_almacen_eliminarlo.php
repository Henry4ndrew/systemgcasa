<form action="actions/addCant_almacen.php" id="formCantidad" class="formStyle b-azul grande" method="POST">
    <div class="cabecera">
        <h2 id="txtFormCant">Agregar producto al almacen</h2>
        <button type="button" onclick="plop('formCantidad')">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
   <br>
    <div class="separador campoForm">
    <div class="mitad">
        <div class="elem2 column">
            <label class="f-peq f-white">Producto:<span class="a">*</span></label>
            <select id="productoSelect" name="codigo" onchange="cargarDetalles(this.value)" class="select pd" required>
                <option value="">Seleccione un Producto</option>
                <?php
                $sql = "SELECT codigo, nombre FROM lista_productos";
                $result = $conexion->query($sql);

                while ($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row['codigo'] . "'>" . $row['codigo'] . " - " . $row['nombre'] . "</option>";
                }
                ?>
            </select>
        </div>
        <div class="elem2 column">
            <label class="f-peq f-white">Detalle:<span class="a">*</span></label>
            <select id="detalleSelect" class="select pd"  name="id_detalle" required></select>
        </div>
        <div class="elem2 column">
            <label class="f-peq f-white" for="cantidad">Cantidad:<span class="a">*</span></label>
            <input type="number" class="input pd" id="cantidadProdAlm" name="cantidadProdAlm" required min="1">
        </div>
    </div>

    <div class="campoMostrar">
        <div id="detalle_img_encontrados"></div>
    </div>    
    </div>

    <div class="containerBtns">
        <button id="btnAddProdCant" type="submit" name="action" value="agregar" class="btn-load verde"><span>Agregar Producto</span></button>
        <button id="btnEditarProdCant" type="submit" name="action" value="editar" class="btn-load azul" style="display:none;"><span>Guardar edición</span></button>
    </div>
</form>




<style>
    .campoMostrar{
        width:50%;
        height:180px;
        overflow-y:auto;
        padding:10px;
        margin-top:10px;
        background: rgba(248, 239, 185, 0.6); 
        box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.5);
    }
    .productoEncontrado{
        display:flex;
        flex-direction:column;
        align-items:center;
        justify-content:center;
    }
    .img_almacen{
        width:100px;
        height:100px;
        object-fit:cover;
        display:flex;
        justify-content:center;
    }
</style>


