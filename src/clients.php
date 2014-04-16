/* TODO:
- Make posible to download budgets and invoices related to clients
- Build tests
- Translate variables to english
- Set a dictionary for the words shown in the web page in different lenguages
*/

<?

function show_clients ($query) {
    echo "<table class='item-list'>\n";
    echo "<tr class='table-header'><td>Cliente</td><td>Acciones</td></tr>";
    $even = 0;
    $numero_linea = 1;
    while ($client = next_row ($query)) {
        $class = ($even ? "fila-par" : "fila-impar");
        $even   = ($even ? 0 : 1);
        echo "<tr class='" . $class  .  "'>";	
        echo "<td>" . $client[0] . "</td>";
        echo "<td class='colum_der'><a href='index.php?module=cliente&action=delete&name=" . $client[0] . "'>Borrar</a> ";
        echo "|<a href='index.php?module=cliente&action=edit&name=" . $client[0] . "'>Editar</a> ";
	  echo "|<a href='index.php?module=cliente&action=invoices&id=" . $client[1] . "'>Facturas</a> ";
	  echo "|<a href='index.php?module=cliente&action=budgets&id=" . $client[1] . "'>Presupestos</a> ";
        echo "</tr>";
    }
    echo "</table>\n";
}

function list_clients () {

    /* Get connection to the data base */
    $link = get_connection ();
    do_query ("SET CHARACTER SET UTF8;", $link);
    /* List created clients */
    $query = do_query ("SELECT nombre, id, CHAR_LENGTH(nombre) from clientes ORDER BY nombre", $link);

    /* Show results */
    show_clients ($query);

    /* Close connection */
    close_conn ($link);

    return;
}

function form_new_client () {

?>

<div class="caja-new-login">
<form name="cliente_input" action="index.php?module=cliente&action=do_create" method="post" >
  <table>
    <tr>
      <td> Nombre </td>
      <td> <input type="text" name="nombre" id="nombre" /> </td>
    </tr>
    <tr>
      <td> Nombre Factura </td>
      <td> <input type="text" name="nombre_factura" /> </td>
    </tr>
    <tr>
      <td> CIF </td>
      <td> <input type="text" name="cif" id="cif" />  </td>
    </tr>
    <tr>
      <td> Dirección </td>
      <td>
	<input type="text" name="direccion" id="direccion" />       </td>
    </tr>
	<tr>
      <td> CP-Localidad-Provincia </td>
      <td>
      	<input type="text" name="cp" id="cp" size="6" /> 
        <input type="text" name="localidad" id="localidad" /> 
        <input type="text" name="provincia" id="provincia" />
      </td>
    </tr>
    <tr>
      <td> Teléfono </td>
      <td>
      	<input type="text" name="telefono" id="telefono" /> 
      </td>
    </tr>
    <tr>
      <td> Fax </td>
      <td>
      	<input type="text" name="fax" id="fax" /> 
      </td>
    </tr>
    <tr>
      <td> </td>
      <td> <input type="submit" value="validar registro" name="enviar" /> </td>
    </tr>
  </table>
</form>
</div>


<?
}


function form_edit_client ($user) {

    /* Get connection to the data base */
    $link = get_connection ();
    do_query ("SET CHARACTER SET UTF8;", $link);
    /* list current data of the client */
    $query = do_query ("SELECT nombre,cif, direccion, cp, localidad, ".
		       " provincia, observaciones, telefono, fax, nombre_factura " .
                       "FROM clientes WHERE nombre='$user'", $link);
    
    /* cerramos la conexión */
    close_conn ($link);
    
    /* Accedemos a los datos del cliente */
    $user_data = next_row ($query);
?>

<div class="caja-login-edit">
<form name="edit_form" action="index.php?module=user&action=do_edit&name=<?=$user?>" method="post" >
  <table>
    <tr>
      <td> Nombre </td>
      <td> <input type="text" name="nombre" value="<?=$user_data[0]?>" /> </td>
    </tr>
    <tr>
      <td> Nombre Factura </td>
      <td> <input type="text" name="nombre_factura" value="<?=$user_data[9]?>" /> </td>
    </tr>
    <tr>
      <td> CIF </td>
      <td>
      	<input type="text" name="cif" id="cif" value="<?=$user_data[1]?>"/> 
      </td>
    </tr>
    <tr>
      <td> Dirección </td>
      <td>
      	<input type="text" name="direccion" id="direccion" value="<?=$user_data[2]?>"/> 
      </td>
    </tr>
    <tr>
      <td> CP-Localidad-Provincia </td>
      <td>
      	<input type="text" name="cp" id="cp" size="6" value="<?=$user_data[3]?>"/> 
        <input type="text" name="localidad" id="localidad" value="<?=$user_data[4]?>"/> 
        <input type="text" name="provincia" id="provincia" value="<?=$user_data[5]?>"/>
      </td>
    </tr>
    <tr>
      <td> Teléfono </td>
      <td>
      	<input type="text" name="telefono" id="telefono" value="<?=$user_data[7]?>"/> 
      </td>
    </tr>
    <tr>
      <td> Fax </td>
      <td>
      	<input type="text" name="fax" id="fax" value="<?=$user_data[8]?>"/> 
      </td>
    </tr>
    <tr>
      <td> Observaciones </td>
      <td> <textarea cols="30" name="observaciones"><?=$user_data[6]?></textarea> </td>
    </tr>
    <tr>
      <td> </td>
      <td> <input type="submit" value="Actualizar datos" name="enviar"/> </td>
    </tr>
  </table>
</form>
</div>


<?
}

function edit_client ($user) {

    $nombre = $_POST["nombre"];
    $cif = $_POST["cif"];
    $cp = $_POST["cp"];
    $localidad = $_POST["localidad"];
    $provincia = $_POST["provincia"];
    $telefono = $_POST["telefono"];
    $direccion = $_POST["direccion"];
    $fax = $_POST["fax"];
    $observaciones = $_POST["observaciones"];
    $nombre_factura = $_POST["nombre_factura"];


    /* Get connection to the data base */
    $link = get_connection ();
    $codificacion = do_query ("SET CHARACTER SET UTF8;", $link);
    /* Update data base */
    $query = do_query ("UPDATE clientes " .
                       "SET nombre='$nombre', cif='$cif', observaciones='$observaciones', " .
		       " cp='$cp', localidad='$localidad', provincia='$provincia', " .
		       " telefono='$telefono', fax='$fax', direccion='$direccion', " .
                       " nombre_factura='$nombre_factura' WHERE nombre='$user'", $link);

    /* cerramos la conexión */
    close_conn ($link);

    /* Show clients */
    list_clients ();

}

function del_client ($user) {
    /* Get connection to the data base */
    $link = get_connection ();
    $codificacion = do_query ("SET CHARACTER SET UTF8;", $link);
    /* Delete client */
    $result = do_query ("DELETE FROM clientes WHERE nombre='$user'", $link);

    /* cerramos la conexión */
    close_conn ($link);

    return;
}


function new_client () {
    $nombre = $_POST["nombre"];
    $cif = $_POST["cif"];
    $direccion = $_POST["direccion"];
	$cp = $_POST["cp"];
	$localidad = $_POST["localidad"];
	$provincia = $_POST["provincia"];
	$telefono = $_POST["telefono"];
	$fax = $_POST["fax"];
    $nombre_factura = $_POST["nombre_factura"];

    /* Get connection to the data base */
    $link = get_connection ();
	do_query ("SET CHARACTER SET UTF8;", $link);

    /* Insert data in the data base */
    $result = do_new_user_query("INSERT INTO " .
                                " clientes (nombre, cif, direccion, cp, localidad, ". 
                                " provincia, telefono, fax, nombre_factura) " .
                                " VALUES " .
                                " ('$nombre', '$cif', '$direccion', '$cp', '$localidad', ". 
                                " '$provincia', '$telefono', '$fax', '$nombre_factura');", $link);

    /* Close connection */
    close_conn ($link);
    
    list_clients ();
	
    return;
    
}

function show_invoices_rel ($query,$id) {
    /* Get connection to the data base */
    $link = get_connection ();
    $codificacion = do_query ("SET CHARACTER SET UTF8;", $link);
    /* Obtain the name of the client */
    $concept = do_query ("SELECT nombre from clientes WHERE id='$id';", $link);
    $concept = next_row ($concept);
    echo "<table class='item-list'>\n";
    echo "<tr class='table-header'>Facturas relacionadas a: <strong>" . $concept[0] .  "</strong></tr>";
    echo "<tr class='table-header'><td>Factura</td><td>Fecha</td></tr>";
    $par = 0;
    while ($fila = next_row ($query)) {

    /* Obtain the number and the date of the invoice */
    $factura = do_query ("SELECT numero, fecha from facturas WHERE id='$fila[0]';", $link);
    $factura = next_row ($factura);

    /* añadimos ceros al numero para enlazar con el archivo */
	if (strlen ($factura[0]) == 1) {
	    $factura[0] = '0' . '0' . $factura[0];
	}
	else if (strlen ($factura[0]) == 2){
	    $factura[0] = '0' . $factura[0];
	}
	/* concatenamos fecha (dos ultimos digitos del año) y numero de factura */       
    $nombre_factura = substr ($factura[1],2,2) .'/'. $factura[0];
    $class = ($par ? "fila-par" : "fila-impar");
    $par   = ($par ? 0 : 1);
    echo "<tr class='" . $class  .  "'>";
      echo "<td class='colum_der'>" . $nombre_factura . "</td>";
      echo "<td class='colum_der'>" . $factura[1];
	echo "</td>";
        echo "</tr>";
    }
    echo "</table>\n";
}

function show_budgets_rel ($query,$id) {
    /* Get connection to the data base */
    $link = get_connection ();
    $codificacion = do_query ("SET CHARACTER SET UTF8;", $link);
    /* Obtain the name of the client */
    $concept = do_query ("SELECT nombre from clientes WHERE id='$id';", $link);
    $concept = next_row ($concept);
    echo "<table class='item-list'>\n";
    echo "<tr class='table-header'>Presupuestos relacionados a: <strong>" . $concept[0] .  "</strong></tr>";
    echo "<tr class='table-header'><td>Presupuesto</td><td>Fecha</td></tr>";
    $par = 0;
    while ($fila = next_row ($query)) {

    /* Obtain the number and the date of the invoice */
    $presupuesto = do_query ("SELECT numero, fecha from presupuestos WHERE id='$fila[0]';", $link);
    $presupuesto = next_row ($presupuesto);

    /* añadimos ceros al numero para enlazar con el archivo */
	if (strlen ($presupuesto[0]) == 1) {
	    $presupuesto[0] = '0' . '0' . $presupuesto[0];
	}
	else if (strlen ($presupuesto[0]) == 2){
	    $presupuesto[0] = '0' . $presupuesto[0];
	}
	/* concatenamos fecha (dos ultimos digitos del año) y numero de presupuesto */       
    $nombre_presupuesto = substr ($presupuesto[1],2,2) .'/'. $presupuesto[0];
    $class = ($par ? "fila-par" : "fila-impar");
    $par   = ($par ? 0 : 1);
    echo "<tr class='" . $class  .  "'>";
      echo "<td class='colum_der'>" . $nombre_presupuesto . "</td>";
      echo "<td class='colum_der'>" . $presupuesto[1];
	echo "</td>";
	echo "</tr>";
    }
    echo "</table>\n";
}

function invoices ($id) {
    /* Get connection to the data base */
    $link = get_connection ();
    $codificacion = do_query ("SET CHARACTER SET UTF8;", $link);
    /* Select invoices related to the client */
    $query = do_query ("SELECT factura from cliente_factura WHERE cliente='$id'", $link);

    /* show results */
    show_invoices_rel ($query,$id);

    /* Close connection */
    close_conn ($link);

    return;
}

function budgets ($id) {
    /* Get connection to the data base */
    $link = get_connection ();
    $codificacion = do_query ("SET CHARACTER SET UTF8;", $link);
    /* Obtain all the budgets associated to the client */
    $query = do_query ("SELECT presupuesto from cliente_presupuesto WHERE cliente='$id'", $link);

    /* show results */
    show_budgets_rel ($query,$id); 

    /* Close connection */
    close_conn ($link);

    return;
}


function clients_operation($action) {

?>  <div class="caja-menu">
      <ul class="menu">
        <li><a href="index.php?module=cliente&action=list">Listar</a></li>
        <li><a href="index.php?module=cliente&action=create">Crear</a></li>	
      </ul>
    </div>
    <div class="caja-titulo">
      CLIENTES
    </div>

    <div class="caja-com">
<?

    switch ($action) {
    case "list":
    list_clients ();
    break;
    
    case "create":
    form_new_client ();
    break;
    
    case "do_create":
    new_client ();
    break;
    
    case "delete":
    del_client ($_GET{"name"});
    list_clients ();
    break;
    
    case "edit":
    form_edit_client ($_GET{"name"});
    break;
    
    case "do_edit":
    edit_client ($_GET{"name"});
    break;    

    case "invoices":
    invoices ($_GET{"id"});
    break;

    case "budgets":
    budgets ($_GET{"id"});
    break;

    default:
    list_clients ();
    break;
    }
?> </div> <?

    return;
}

?>
