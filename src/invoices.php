<?php 

function show_invoices ($query, $consulta) {
    /* Funtion to show the list of invoices */
    
    echo "<table class='item-list'>\n";
    echo "<tr class='table-header'><td>Factura</td><td>Cliente asociado</td><td>Total Factura</td><td>Estado</td><td>Acciones</td></tr>";
    
    /* Get connection to the data base */
    $link = get_connection ();
    $codificacion = do_query ("SET CHARACTER SET UTF8;", $link);
    /* pintamos lineas pares e impares con $par y $class */ 
    $par = 0;
    
    /* vamos a utilizar esta variable para ver la fecha de la siguiente factura */
    $fecha_next_factura = next_row ($consulta);
    $total_euros = 0;

    /* accedemos a cada una de las lineas de la tabla seleccionada */
    while ($factura = next_row ($query)) {
		$fecha_next_factura = next_row ($consulta);
        /* seleccionamos el id del cliente en la tabla cliente_factura */
        $id = do_query ("SELECT cliente FROM cliente_factura WHERE ".
                        " factura='$factura[2]';", $link);
        $numero = substr ($factura[0],5,2);
        /* seleccionamos el nombre del mes para el enlace */
        $mes = do_query ("SELECT nombre FROM mes WHERE ".
                         " numero='$numero';", $link);
        /* Nos vamos a la primera linea de datos */
        $id = next_row($id);
        $mes = next_row($mes);
        /* seleccionamos el nombre del cliente */
        $nombre_cliente = do_query ("SELECT nombre FROM clientes WHERE id='$id[0]';", $link);
        /* Nos vamos a la primera linea de datos */
        $nombre_cliente = next_row($nombre_cliente);
        /* añadimos ceros al numero enlazar con el archivo */
        if (strlen ($factura[1]) == 1) {
            $factura[1] = '0' . '0' . $factura[1];
        }
        else if (strlen ($factura[1]) == 2){
            $factura[1] = '0' . $factura[1];
        }
        /* concatenamos fecha (dos ultimos digitos del año) y numero de factura */       
        $anno = substr ($factura[0],0,4);
        $nombre = substr ($factura[0],2,2) .'/'. $factura[1];
        $documento = substr ($factura[0],2,2) .'_'. $factura[1];
        $class = ($par ? "fila-par" : "fila-impar");
        $par   = ($par ? 0 : 1);
        
        echo "<tr class='" . $class  .  "'>";
        /* Enlace a la factura */
        if ($factura[1] > 21){
            echo "<td><a href='index.php?module=invoice&action=show&id=" . 
                $factura[2] . "'>" . $nombre . "</a></td>";
        }
        else {
            echo '<td> <a href="empresa/facturas/'. $anno . "/" . $documento . '.odt">' . $nombre . "</td>";
        }  
        echo "<td class='colum_der'>" . $nombre_cliente[0]  . "</td>";
        echo "<td class='colum_der'>" . $factura[3] . "</td>";
        $color = 'colum_pagado';
        if ($factura[4] == 'pendiente'){
            $color = 'colum_pendiente';
        }
        echo "<td class='$color'>" . $factura[4] . "</td>";
        $total_euros = $factura[3] + $total_euros;
        echo "<td class='colum_der'><a href='index.php?module=invoice&action=delete&name=".
            $factura[2] . "'>Borrar</a> ";
        echo "|<a href='index.php?module=invoice&action=edit&name=" . 
            $factura[2] . "'>Editar</a> ";
        echo "|<a href='index.php?module=invoice&action=rel&id_factura=" . $factura[2] . "'>Estado</a>";
        echo "|<a href='index.php?module=invoice&action=show&id=" . $factura[2] . "'>Visualizar</a></td>";
        echo "</tr>";
        $anno_total_final = substr ($factura[0], 0, 4);
        if (substr ($fecha_next_factura[0], 0, 4) != substr ($factura[0], 0, 4) &&  substr ($fecha_next_factura[0], 0, 4) != NULL) {
            
            echo "<tr class='total'>";
            echo "<td class='colum_der'>Total año: " . substr ($factura[0], 0, 4) . "</td>";
            echo "<td> </td>";
            echo "<td class='colum_der'>" . $total_euros . "</td>";
            echo "<td> </td>";
            echo "<td> </td>";
            echo "</tr>";	    
            $total_euros = 0;
        }
    }  
    echo "<tr class='total'>";
    echo "<td class='colum_der'>Total año: " . $anno_total_final . "</td>";
    echo "<td> </td>";
    echo "<td class='colum_der'>" . $total_euros . "</td>";
    echo "<td> </td>";
    echo "<td> </td>";
    echo "</tr>";
    echo "</table>\n";
}

	
function total_trimestral ($anno, $trimestre) {

    if ($trimestre == 1) {
	$primero = "0" + "1";
	$segundo = "0" + "2";
	$tercero = "0" + "3";
    }
    else if ($trimestre == 4) {
	$primero = "0" + "4";
	$segundo = "0" + "5";
	$tercero = "0" + "6";
    }
    else if ($trimestre == 7) {
	$primero = "0" + "7";
	$segundo = "0" + "8";
	$tercero = "0" + "9";
    }
    else {
	$primero = 10;
	$segundo = 11;
	$tercero = 12;
    }
	
    
    /* nos conectamos a la base de datos */
    $link = get_connection ();
    $facturas = do_query ("SELECT fecha, numero, id, total " .
			  "FROM facturas where SUBSTRING(fecha, 1, 4)=$anno " .
			  " AND " .
			  "(SUBSTRING(fecha, 6, 2)=$primero OR " .
			  "SUBSTRING(fecha, 6, 2)=$segundo OR " .
			  "SUBSTRING(fecha, 6, 2)=$tercero) ORDER BY numero;" , $link);    

    echo "<table class='item-list'>\n";
    echo "<tr class='table-header'><td>Factura</td><td>Cliente asociado</td><td>Total Factura</td><td>Fecha</td></tr>";
    
    /* pintamos lineas pares e impares con $par y $class */ 
    $par = 0;

    /* vamos a utilizar esta variable para ver la fecha de la siguiente factura */
    $total_euros = 0;
    /* accedemos a cada una de las lineas de la tabla seleccionada */
    while ($factura = next_row ($facturas)) {
	    
	/* seleccionamos el id del cliente en la tabla cliente_factura */
	$id = do_query ("SELECT cliente FROM cliente_factura WHERE ".
			" factura='$factura[2]';", $link);
	$numero = substr ($factura[0],5,2);
	/* seleccionamos el nombre del mes para el enlace */
	$mes = do_query ("SELECT nombre FROM mes WHERE ".
			" numero='$numero';", $link);
	/* Nos vamos a la primera linea de datos */
	$id = next_row($id);
	$mes = next_row($mes);
	/* seleccionamos el nombre del cliente */
	$nombre_cliente = do_query ("SELECT nombre FROM clientes WHERE id='$id[0]';", $link);
	/* Nos vamos a la primera linea de datos */
	$nombre_cliente = next_row($nombre_cliente);
	/* añadimos ceros al numero enlazar con el archivo */
	if (strlen ($factura[1]) == 1) {
	    $factura[1] = '0' . '0' . $factura[1];
	}
	else if (strlen ($factura[1]) == 2){
	    $factura[1] = '0' . $factura[1];
	}
	/* concatenamos fecha (dos ultimos digitos del año) y numero de factura */       
	$anno = substr ($factura[0],0,4);
	$nombre = substr ($factura[0],2,2) .'/'. $factura[1];
	$documento = substr ($factura[0],2,2) .'_'. $factura[1];
	$class = ($par ? "fila-par" : "fila-impar");
	$par   = ($par ? 0 : 1);
	
	echo "<tr class='" . $class  .  "'>";
	/* Enlace a la factura */
	if ($factura[1] > 21){
        echo "<td><a href='index.php?module=invoice&action=show&id=" . 
	    $factura[2] . "'>" . $nombre . "</a></td>";
	}
	else {
		echo '<td> <a href="empresa/facturas/'. $anno . "/" . $documento . '.odt">' . $nombre . "</td>";
	}  
	echo "<td class='colum_der'>" . $nombre_cliente[0]  . "</td>";
	echo "<td class='colum_der'>" . $factura[3] . "</td>";
	echo "<td class='colum_der'>" . $factura[0] . "</td>";
        echo "</tr>";
	$total_euros = $total_euros + $factura[3];
	
    }  
    echo "<tr class='total'>";
    echo "<td>Total trimestre: </td>";
    echo "<td> </td>";
    echo "<td class='colum_der'>" . $total_euros . "</td>";
    echo "<td> </td>";
    echo "</tr>";
    echo "</table>\n";
    
}



function list_invoices () {
    /* Get connection to the data base */
    $link = get_connection ();
    $codificacion = do_query ("SET CHARACTER SET UTF8;", $link);

    /* Obtain invoices */
    $query = do_query ("SELECT fecha, numero, id, total, estado ".
		       " FROM facturas ORDER BY numero;", $link);

    $consulta = do_query ("SELECT fecha ".
		       " FROM facturas ORDER BY numero;", $link);

    /* mostramos los resultados */
    show_invoices ($query, $consulta);
    
    /* cerramos la conexión */
    close_conn ($link);

    return;
}

$concepto = 0; 

function form_new_invoice () {
    /* Get connection to the data base */
    $link = get_connection ();
    $codificacion = do_query ("SET CHARACTER SET UTF8;", $link);

    /* listamos los facturas creadas */
    $query = do_query ("SELECT id, nombre from clientes ORDER BY nombre;", $link);
    $id = do_query ("SELECT COALESCE(MAX(numero)+1,1), DATE_FORMAT(now(),'%d/%m/%Y') ".    
		    "FROM facturas " .
		    "WHERE EXTRACT(year FROM (fecha))=EXTRACT(year from DATE(now()));",
		    $link);
    
    /* cerramos la conexión */
    close_conn ($link);

    /* Asignamos el id de la factura a la variable veces */
    $data = next_row ($id);
    $veces = $data[0];
    
    /* La variable concepto guarda el ultimo numero de concepto */ 
?>
    
  <div class="caja-nueva-factura">
    <form name="factura_input" method="post" 
       action="index.php?module=invoice&action=do_create">
    <table>
      <tr>
	<td> Número factura </td>
	<td> <input type="text" name="numero" id="numero" value="<?=$veces?>" /> </td>
      </tr>
      <tr>
	<td> Fecha </td>
	<td> <input type="text" name="fecha" id="fecha" value="<?=$data[1]?>" />  </td>
      </tr>
      <tr>
	<td> Nombre cliente </td>
	<td>
	  <select name="cliente">
	    <option selected/>
	      <?
	      while ($cliente = next_row ($query)) {
		  echo '<option value="'.$cliente[0].'">' . $cliente [1] . "</option>";
	      }
	      ?>
	  </select>
	</td>
      </tr>	
      <tr>
	<td> Observaciones </td>      
        <td> <textarea cols="50" name="observaciones" WRAP=SOFT></textarea> </td>
      </tr>	
     </table>
	<?
		echo "<table>";
                echo "<tr>";		
		echo "<td> Cantidad </td>";
		echo "<td> Concepto </td>";
		echo "<td> Precio ud </td>";
		echo "<tr>";
		echo "<td>";
		echo "<input type='text' size='6' name='cantidad' id='cantidad' />";
		echo "</td>";
		echo "<td>";
		echo "<textarea cols=54 rows=1 name='concepto' id='concepto' WRAP=SOFT></textarea>";
		echo "</td>";
		echo "<td>";
		echo "<input type='text' size='6' name='precio_unidad' id='precio_unidad' />";
		echo "</td>";
		echo "</tr>";	
      ?>
      <tr>
        <td> </td>
	<td> Añadir Concepto 	
	    <input type="submit" value="Nuevo Concepto" name="enviar" onclick="index.php?module=invoice&action=do_create"  /> 
	</td>
        <td> </td>
      </tr>      
    </table>
  </form>
 </div>


<?
}



function formulario_introducir_conceptos ($id_factura, $numero, $fecha, $cliente, $observaciones) {
    
    /* nos conectamos a la base de datos */
    $link = get_connection ();
    $codificacion = do_query ("SET CHARACTER SET UTF8;", $link);

    /* <-- obtenemos la longitud (CHAR_LENGHT(concepto)) del concepto */
    /* para determinar el numero de filas del textarea--> */
    /* obtenemos de la base de datos la informacion de los conceptos */
    $consulta = do_query ("SELECT concepto, cantidad, precio_unidad, importe, id, CHAR_LENGTH(concepto) from conceptos where factura='$id_factura' ORDER BY id;", $link);
    
    /* cerramos la conexión */
    close_conn ($link);

?>
    
  <div class="caja-nueva-factura">
    <form name="factura_input" method="post" 
       action="index.php?module=invoice&action=crear_conceptos">
    <table>
      <tr>
	<td> Número factura </td>
	<td> <input type="text" name="numero" id="numero" value="<?=$numero?>" /> </td>
      </tr>
      <tr>
	<td> Fecha </td>
	<td> <input type="text" name="fecha" id="fecha" value="<?=$fecha?>" />  </td>
      </tr>
      <tr>
	<td> Nombre cliente </td>
	<td> <input type="text" name="cliente" id="cliente" value="<?=$cliente?>" />  </td
      </tr>
       <tr>
        <td> Observaciones </td> 
        <td> <textarea cols="50" name="observaciones" WRAP=SOFT><?=$observaciones?></textarea> </td>
      </tr>
       
    </table>
       <?     		   
       echo "<table>";
    	   echo "<tr>";
	   echo "<td> Cantidad </td>";
	   echo "<td> Concepto </td>";
	   echo "<td> Precio ud. </td>";
	   echo "<td>  Importe </td>";
	   echo "<td>   </td>";
	   echo "</tr>";
	   
       while ($item = next_row ($consulta)) {	   	   	   
	   if ($item[5] == 0 || $item[5] < 55) { 
	       $numero_filas = 1;
	   }
	   else {
	       $numero_filas = intval($item[5] / 54);
	       if (($item[5] % ($numero_filas * 54)) != 0) {
		   $numero_filas = $numero_filas + 1;
	       }
	   }	   
	   echo "<tr>";
	   echo "<td>";
	   echo "<input type='text' name='cantidad$item[4]' id='cantidad$item[4]' size='6' value='$item[1]' />";
	   echo "</td>";	   
	   echo "<td>";
	   echo "<textarea cols='54' rows='$numero_filas' name='concepto$item[4]' id='concepto$item[4]' WRAP=SOFT>$item[0]</textarea>";
	   echo "</td>";
	   echo "<td>";
	   echo "<input type='text' name='precio_unidad$item[4]' id='precio_unidad$item[4]' size='7' value='$item[2]' />";
	   echo "</td>";
	   echo "<td>";
	   echo "<input type='text' size='6' value='$item[3]' />";
	   echo "</td>";
	   echo "<td>";
	   echo "<a href='index.php?module=invoice&action=form_edit_concept&name=" . $item[4] . "&id_factura=" . $id_factura . "&cliente=" . $cliente . "&fecha=" . $fecha . "&numero=" . $numero . "&observaciones=" . $observaciones . "'>Editar</a> | ";
	   echo "<a href='index.php?module=invoice&action=delete_concept&name=" . $item[4] . "&id_factura=" . $id_factura . "&cliente=" . $cliente . "&fecha=" . $fecha . "&numero=" . $numero . "'>Borrar</a>";
	   echo "</td>";
	   echo "</tr>"; 
       }
       echo "</table>";
    echo "<table>";
    echo "<tr>";
    echo "<td> Cantidad </td>";
    echo "<td> Concepto </td>";
    echo "<td> Precio ud. </td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td>";
    echo "<input type='text' size='6' name='cantidad' id='cantidad' />";
    echo "</td>";
    echo "<td>";
    echo "<textarea cols='54' rows='1' name='concepto' id='concepto'></textarea>";
    echo "</td>";    
    echo "<td>";
    echo "<input type='text' size='6' name='precio_unidad' id='precio_unidad' />";
    echo "</td>";
    echo "</tr>";
    ?>
      <tr>
        <td> </td>
	<td> Añadir Concepto
	    <input type="submit" value="añadir" name="enviar" onclick="index.php?module=invoice&action=do_create'"  /> 
	</td>
        <td> </td>
      </tr>      
    </table>
  </form>
  <a href='index.php?module=invoice&action=show&id=<?=$id_factura?>'><input type="submit" value="Generar Documento" name="generar_factura"  /> </a>
 </div>

<?

}

function formulario_editar_concepto ($id_concepto, $id_factura, $cliente, $numero, $fecha, $observaciones) {
   
    /* nos conectamos a la base de datos */
    $link = get_connection ();
    $codificacion = do_query ("SET CHARACTER SET UTF8;", $link);
    /* obtenemos de la base de datos la informacion de los conceptos */
    $concepto = do_query ("SELECT concepto, cantidad, precio_unidad, CHAR_LENGTH(concepto) from conceptos where id='$id_concepto';", $link);
    
    /* cerramos la conexión */
    close_conn ($link);
    
    /* Obtenemos los valores */
    $concepto = next_row ($concepto);

?>    
<div class="caja-nueva-factura">
  <form name="editando_concepto" method="post" action="index.php?module=invoice&action=edit_concept&name=<?=$id_concepto?>&id_factura=<?=$id_factura?>&cliente=<?=$cliente?>&fecha=<?=$fecha?>&numero=<?=$numero?>&observaciones=<?=$observaciones?>">
    <table>
      <tr>
	<td> Cantidad </td>
	<td> Concepto </td>
	<td> Precio ud. </td>
      </tr>
      <tr>
	<td>
	<input type='text' size='6' name='cantidad' id='cantidad' value='<?=$concepto[1]?>' />
	</td>
     <?
     
     if ($concepto[3] == 0 || $concepto[3] < 55) { 
	 $numero_filas = 1;
     }
     else {
	 $numero_filas = intval($concepto[3] / 54);
	 if (($concepto[3] % ($numero_filas * 54)) != 0) {
	     $numero_filas = $numero_filas + 1;
	 }
     }    
    echo "<td>";
    echo "<textarea cols='54' rows='$numero_filas' name='concepto' id='concepto' WRAP=SOFT>$concepto[0]</textarea>";
    echo "</td>";
    ?>	
	<td>
	<input type='text' size='6' name='precio_unidad' id='precio_unidad' value='<?=$concepto[2]?>' />
	</td>
      </tr>
      <tr>
        <td> </td>
	<td> Añadir Concepto
	<input type="submit" value="enviar" name="enviar" /> 
	</td>
        <td> </td>
      </tr>      
    </table>
  </form>
<?
}

function formulario_editar_factura ($id) {

    /* nos conectamos a la base de datos */
    $link = get_connection ();
    $codificacion = do_query ("SET CHARACTER SET UTF8;", $link);
    /* listamos los facturas creados */
    /* Si al hacer un select de varias tablas una de ellas no contiene un valor, en la tabla resultado no aparecera dicha fila */
    $query = do_query ("SELECT f.numero, DATE_FORMAT(f.fecha,'%d/%m/%Y'), " .
		       " f.total, f.estado, f.observaciones, cf.cliente " .
                       "FROM facturas f, cliente_factura cf WHERE f.id='$id' ".
		       " AND cf.factura=f.id", $link);
    $peticion = do_query ("SELECT id, nombre from clientes ORDER BY nombre;", $link);
    
    /* cerramos la conexión */
    close_conn ($link);
    
    /* Accedemos a los datos del factura */
    $user_data = next_row ($query);

?>

<div class="caja-login-edit">
<form name="edit_form" action="index.php?module=invoice&action=do_edit&name=<?=$id?>" method="post" >
  <table>
    <tr>
      <td> Número </td>
      <td> <input type="text" name="numero" value="<?=$user_data[0]?>" /> </td>
    </tr>
    <tr>
      <td> Fecha </td>
      <td>
      	<input type="text" name="fecha" id="fecha" value="<?=$user_data[1]?>"/> 
      </td>
    </tr>
    <tr>
      <td> Total </td>
      <td>
      	<input type="text" name="total" id="total" value="<?=$user_data[2]?>"/> 
      </td>
    </tr>
    <tr>
      <td> Estado </td>
      <td>
      	<input type="text" name="estado" id="estado" value="<?=$user_data[3]?>"/> 
      </td>
    </tr>
    <tr>
      <td> Nombre cliente </td>
      <td>
        <select name="cliente">
          <?
          while ($cliente = next_row ($peticion)) {
	      if ($user_data[5] == $cliente[0]) {
		  echo '<option selected value="'.$cliente[0].'">' . $cliente [1] . "</option>";
	      } else {
		  echo '<option value="'.$cliente[0].'">' . $cliente [1] . "</option>";
	      }
          }
          ?>
        </select>
      </td>
    </tr>
      <tr>
      <td> Observaciones </td>  
      <td> <textarea cols="50" name="observaciones" WRAP=SOFT><?=$user_data[4]?></textarea> </td>
    </tr>
     </table>
     <table>
    <tr><center><input type="submit" value="Ir a editar conceptos" name="enviar"/> </center>
    </tr>
  </table>
</form>
</div>


<?
	      
}

function pagina_editar_concepto ($id, $id_factura) {

    /* nos conectamos a la base de datos */
    $link = get_connection ();
    $codificacion = do_query ("SET CHARACTER SET UTF8;", $link);
    /* listamos los conceptos creados */
    
    $concepto = do_query ("SELECT concepto, cantidad, precio_unidad, importe, " .
			  "CHAR_LENGTH(concepto) " .
			  " FROM conceptos WHERE id='$id';", $link);
    /* cerramos la conexión */
    close_conn ($link);

    /* nos vamos a la linea de datos */
    $concepto = next_row($concepto);

?>

<div class="caja-nueva-factura">
  <form name="editando_concepto" method="post" action="index.php?module=invoice&action=do_edit_concept&name=<?=$id?>&id_factura=<?=$id_factura?>">
    <table>
      <tr>
	<td> Cantidad </td>
	<td> Concepto </td>
	<td> Precio ud. </td>
      </tr>
      <tr>
	<td>
	<input type='text' size='6' name='cantidad' id='cantidad' value='<?=$concepto[1]?>' />
	</td>
     <?
     if ($concepto[4] == 0 || $concepto[4] < 55) { 
	 $numero_filas = 1;
	   }
     else {
	 $numero_filas = intval($concepto[4] / 54);
	 if (($concepto[4] % ($numero_filas * 54)) != 0) {
	     $numero_filas = $numero_filas + 1;
	 }
     }	   	   	   
    echo "<td>";
    echo "<textarea cols='54' rows='$numero_filas' name='concepto' id='concepto' WRAP=SOFT>$concepto[0]</textarea>";
    echo "</td>";
    ?>
	<td>
	<input type='text' size='6' name='precio_unidad' id='precio_unidad' value='<?=$concepto[2]?>' />
	</td>
     	<td>
	<input type='text' size='6' name='importe' id='importe' value='<?=$concepto[3]?>' />
	</td>
      </tr>
      <tr>
        <td> </td>
	<td> Editar Concepto
	<input type="submit" value="editar" name="enviar" /> 
	</td>
        <td> </td>
      </tr>      
    </table>
  </form>
</div>
<?
	      
}

function formulario_editar_conceptos ($id) {

    /* nos conectamos a la base de datos */
    $link = get_connection ();
    $codificacion = do_query ("SET CHARACTER SET UTF8;", $link);
    /* obtenemos de la base de datos la informacion de los conceptos */
    $concepto = do_query ("SELECT concepto, cantidad, precio_unidad, importe, " . 
			  " id, CHAR_LENGTH(concepto) from conceptos where factura='$id' ORDER BY id;", $link);
    
    /* cerramos la conexión */
    close_conn ($link);
    
?> 

<div class="caja-login-edit">
<form name="edit_form" method="post" action="index.php?module=invoice&action=add_concept&id=<?=$id?>">
      <?       
      echo "<table>";
      echo "<tr>";
      echo "<td> Cantidad </td>";
      echo "<td> Concepto </td>";
      echo "<td> Precio ud. </td>";
      echo "<td> </td>";
      echo "</tr>";
      while ($item = next_row ($concepto)) {	   	   
	  
	  echo "<tr>";
	  echo "<td>";
	  echo "<input type='text' name='cantidad' id='cantidad' size='6' value='$item[1]' />";
	  echo "</td>";
	  if ($item[5] == 0 || $item[5] < 55) { 
	      $numero_filas = 1;
	  }
	  else {
	      $numero_filas = intval($item[5] / 54);
	      if (($item[5] % ($numero_filas * 54)) != 0) {
		  $numero_filas = $numero_filas + 1;
	      }
	  }	  
	  echo "<td>";
	  echo "<textarea cols='54' rows='$numero_filas' name='concepto' id='concepto' WRAP=SOFT>$item[0]</textarea>";
	  echo "</td>";	  	   
	  echo "<td>";
	  echo "<input type='text' name='precio_unidad' id='precio_unidad' size='7' value='$item[2]' />";
	  echo "</td>";
	  echo "<td>";
	  echo "<input type='text' name='importe' id='importe' size='7' value='$item[3]' />";
	  echo "</td>";
	  echo "<td>";
	  echo "<a href='index.php?module=invoice&action=pagina_editar_concepto&name=" . $item[4] . "&factura_id=" . $id . "'>Editar</a> | ";
	  echo "<a href='index.php?module=invoice&action=suprimir_concepto&name=" . $item[4] . "&id_factura=" . $id . "'>Borrar</a>";
	  echo "</td>";
	  echo "</tr>"; 	   	   
      }
      ?>
      <tr>
        <td> </td>
	<td> Añadir Concepto
	<input type="submit" value="Nuevo concepto" name="enviar" /> 
	</td>
        <td> </td>
      </tr>      
    </table>
</form>
</div>
<?
}

function formulario_editar_nuevo_concepto ($id) {

    /* nos conectamos a la base de datos */
    $link = get_connection ();
    $codificacion = do_query ("SET CHARACTER SET UTF8;", $link);
    /* obtenemos de la base de datos la informacion de los conceptos */
    $concepto = do_query ("SELECT concepto, cantidad, precio_unidad, " .
			  " id, CHAR_LENGTH(concepto) from conceptos where factura='$id';", $link);
    
    /* cerramos la conexión */
    close_conn ($link);
    
?> 

<div class="caja-login-edit">
<form name="edit_form" method="post" action="index.php?module=invoice&action=do_add_concept&id=<?=$id?>">
      <?       
       echo "<table>";
       echo "<tr>";
       echo "<td> Cantidad </td>";
       echo "<td> Concepto </td>";
       echo "<td> Precio ud. </td>";
       echo "<td> </td>";
       echo "</tr>";
       while ($item = next_row ($concepto)) {	   	   
	   
	   echo "<tr>";
	   echo "<td>";
	   echo "<input type='text' size='6' value='$item[1]' />";
	   echo "</td>";
	   if ($item[4] == 0 || $item[4] < 55) { 
	       $numero_filas = 1;
	   }
	   else {
	       $numero_filas = intval($item[4] / 54);
	       if (($item[4] % ($numero_filas * 54)) != 0) {
		   $numero_filas = $numero_filas + 1;
	       }
	   }	   	   	   
	   echo "<td>";
	   echo "<textarea cols='54' rows='$numero_filas' WRAP=SOFT>$item[0]</textarea>";
	   echo "</td>";
	   echo "<td>";
	   echo "<input type='text' size='7' value='$item[2]' />";
	   echo "</td>";
	   echo "<td>";
	   echo "<a href='index.php?module=invoice&action=pagina_editar_concepto&name=" . $item[3] . "&factura_id=" . $id . "'>Editar</a> | ";
	   echo "<a href='index.php?module=invoice&action=suprimir_concepto&name=" . $item[3] . "&id_factura=" . $id . "'>Borrar</a>";
	   echo "</td>";
	   echo "</tr>"; 	   	   
       }
    ?>
     <?
		echo "<table>";
		echo "<tr>";		
		echo "<td> Cantidad </td>";
		echo "<td> Concepto </td>";
		echo "<td> Precio ud </td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>";
		echo "<input type='text' size='6' name='cantidad' id='cantidad' />";
		echo "</td>";
		echo "<td>";
		echo "<textarea cols='54' rows='1' name='concepto' id='concepto' WRAP=SOFT>$item[0]</textarea>";
		echo "</td>";	  		
		echo "<td>";
		echo "<input type='text' size='6' name='precio_unidad' id='precio_unidad' />";
		echo "</td>";
		echo "</tr>";
      ?>
      <tr>
        <td> </td>
	<td> Añadir Concepto
	<input type="submit" value="enviar" name="enviar" /> 
	</td>
        <td> </td>
      </tr>      
    </table>
</form>
</div>
<?
}

function suprimir_concepto ($id_concepto, $id_factura) {

    /* nos conectamos a la base de datos */
    $link = get_connection();
    $codificacion = do_query ("SET CHARACTER SET UTF8;", $link);
    /* listamos los usuarios creados */
    $consulta = do_query ("DELETE from conceptos WHERE id='$id_concepto';", $link);

    /* Llamamos a la funcion que muestra el formulario de conceptos editado */
    formulario_editar_conceptos($id_factura);
    
    return;
}

function nuevo_concepto ($id) {

    $concepto = $_POST{"concepto"};
    $cantidad = $_POST{"cantidad"};
    $precio_unidad = $_POST{"precio_unidad"};
    $importe = $cantidad * $precio_unidad;
    
        /* conectar a la base de datos */
        $link = get_connection ();
	$codificacion = do_query ("SET CHARACTER SET UTF8;", $link);
	/* Seleccionamos el nombre del cliente */
	    
	$item = do_query("INSERT INTO " .
			 " conceptos (factura, concepto, cantidad, precio_unidad, importe) " .
			 "VALUES " .
			 " ('$id', '$concepto', '$cantidad', '$precio_unidad', '$importe');",
			 $link);
        
	/* las comillas sirven para que identifique que es una cadena de texto y no una columna */
        close_conn ($link);
        
	/* Pasamos el id de la factura, la fecha y el numero que nos serviran para el formulario */	

	formulario_editar_conceptos ($id);
	
        return result;

}

function editar_concepto_creado ($id, $id_factura) {

$cantidad = $_POST["cantidad"];
$concepto = $_POST["concepto"];
$precio_unidad = $_POST["precio_unidad"];
$importe = $precio_unidad * $cantidad;

    /* nos conectamos a la base de datos */
    $link = get_connection ();
    $codificacion = do_query ("SET CHARACTER SET UTF8;", $link);
    
    /* Actualizamos el dato del concepto */
    $query = do_query ("UPDATE conceptos " .
                       " SET concepto='$concepto', cantidad='$cantidad', " .
		       " precio_unidad='$precio_unidad', importe='$importe' " .
                       " WHERE id='$id'; ", $link);
	   
    /* cerramos la conexión */
    close_conn ($link);

    /* Mostramos formulario */
    formulario_editar_conceptos ($id_factura);

}

function editar_factura ($id) {

$numero = $_POST["numero"];
$fecha = $_POST["fecha"];
$total = $_POST["total"];
$estado = $_POST["estado"];
$cliente = $_POST["cliente"];
$observaciones = $_POST["observaciones"];


/* nos conectamos a la base de datos */
    $link = get_connection ();
    $codificacion = do_query ("SET CHARACTER SET UTF8;", $link);
    /* listamos los facturas creados */
    /* La funcion STR_TO_DATE() guarda la fecha con el formato dd/mm/aaaa */
    $query = do_query ("UPDATE facturas f, cliente_factura cf " .
                       " SET f.numero='$numero', f.fecha=STR_TO_DATE" .
		       "('$fecha','%d/%m/%Y'), " .
		       " f.total='$total', f.estado='$estado' " .
		       " , f.observaciones='$observaciones'," .
                       " cf.cliente='$cliente' WHERE f.id='$id' " .
		       " and cf.factura='$id';", $link);
	   
    /* cerramos la conexión */
    close_conn ($link);

    /* Mostramos formulario */
    formulario_editar_conceptos ($id);

}

function borrar_factura ($id) {
    /* nos conectamos a la base de datos */
    $link = get_connection ();
    $codificacion = do_query ("SET CHARACTER SET UTF8;", $link);
    /* listamos los facturas creados */
    $result = do_query ("DELETE FROM facturas WHERE id='$id';", $link);
    $borrado = do_query ("DELETE FROM cliente_factura WHERE factura='$id';", $link);
    $borrar_conceptos = do_query ("DELETE FROM conceptos WHERE factura='$id';", $link);
    /* cerramos la conexión */
    close_conn ($link);

    return;
}

function verificacion_datos ($query, $numero, $fecha) {
    echo "<table>";
    echo "<tr>";
    echo "<td colspan='2'><strong>Los datos de la factura son los siguientes:</strong></td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td>Nombre cliente: </td>";
    echo "<td>" . $query . ".";
    echo "</tr>";
    echo "<tr>";
    echo "<td>Numero de factura: </td>";
    echo "<td>" . $numero . ".</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td>Fecha: </td>";
    echo "<td>" . $fecha . ".</td>";
    echo " <tr>";
    echo " <td> </td>";
    echo " <td><a href='empresa/facturas/plantillas/plantilla_" . $query . ".odt'><input type='submit' value='Abrir archivo' name='enviar'/> </a> </td>";
    echo "</tr>";
    echo "</table>";
}

function nueva_factura ($id_cliente, $numero, $fecha) {
    
    $observaciones = $_POST{"observaciones"};
    $concepto = $_POST{"concepto"};
    $cantidad = $_POST{"cantidad"};
    $precio_unidad = $_POST{"precio_unidad"};
    $importe = $cantidad * $precio_unidad;
            
        /* conectar a la base de datos */
        $link = get_connection ();
	$codificacion = do_query ("SET CHARACTER SET UTF8;", $link);
	if ($number = 1) {
	    /* obtenemos el nombre del cliente para la verificacion de datos */
	    $query = do_query("SELECT nombre FROM clientes WHERE id='$id_cliente';",$link);
	    /* Insertamos la factura en la base de datos tabla facturas */
	    /* La funcion STR_TO_DATE() guarda la fecha con el formato dd/mm/aaaa */
	    $result = do_query("INSERT INTO " .
			       " facturas (numero, fecha, observaciones) " .
			       "VALUES " .
			       " ('$numero', STR_TO_DATE('$fecha','%d/%m/%Y'), '$observaciones');",
			       $link);
	/* Insertamos el id de cliente y el id de factura en cliente_factura */
	    $insert =  do_query("INSERT INTO " .
				" cliente_factura (cliente, factura) " .
				"VALUES " .
				" ('$id_cliente', (SELECT MAX(id) FROM facturas));",
				$link);
	}
	$item = do_query("INSERT INTO " .
			 " conceptos (factura, concepto, cantidad, precio_unidad, importe) " .
			 "VALUES " .
			 " ((SELECT MAX(id) FROM facturas), '$concepto', '$cantidad', '$precio_unidad', '$importe');",
			 $link);
	$id_factura = do_query("SELECT MAX(id) FROM facturas;",$link);
        
	/* las comillas sirven para que identifique que es una cadena de texto y no una columna */
        close_conn ($link);
        
	/* Pasamos el id de la factura, la fecha y el numero que nos serviran para el formulario */
	$query = next_row($query);

	$id_factura = next_row($id_factura);	

	formulario_introducir_conceptos ($id_factura[0], $numero, $fecha, $query[0], $observaciones);
	
        return result;
    
}

function introducir_conceptos ($cliente, $numero, $fecha) {

    $observaciones = $_POST{"observaciones"};
    $concepto = $_POST{"concepto"};
    $cantidad = $_POST{"cantidad"};
    $precio_unidad = $_POST{"precio_unidad"};
    $importe = $cantidad * $precio_unidad;
    
        /* conectar a la base de datos */
        $link = get_connection ();
	$codificacion = do_query ("SET CHARACTER SET UTF8;", $link);
	/* Seleccionamos el nombre del cliente */
	    
	$item = do_query("INSERT INTO " .
			 " conceptos (factura, concepto, cantidad, precio_unidad, importe) " .
			 "VALUES " .
			 " ((SELECT MAX(id) FROM facturas), '$concepto', '$cantidad', '$precio_unidad', '$importe');",
			 $link);
	$id_factura = do_query("SELECT MAX(id) FROM facturas;",$link);
        
	/* las comillas sirven para que identifique que es una cadena de texto y no una columna */
        
        
	/* Pasamos el id de la factura, la fecha y el numero que nos serviran para el formulario */

	$id_factura = next_row($id_factura);	

	formulario_introducir_conceptos ($id_factura[0], $numero, $fecha, $cliente, $observaciones);
	
        return;
    
}

/* Relacionamos factura con cliente mediante el numero de factura y el id del cliente */
/* la variable $nombre hace referencia al nombre de la factura (002safety,p.e.) */
/* la variable $id_factura se va a usar para insertar el id de la factura en la base de datos */

function rel_form_estado ($id_factura) {
    
    /* Nos conectamos a la base de datos */
    $link = get_connection();
    /* Seleccionamos el estado de la factura */
    $query = do_query("SELECT estado from facturas where" .
		      " id='$id_factura';", $link);
    
    close_conn ($link);
    
    $estado_actual = next_row($query);
?>
<div class="caja-titulo">
     <!-- aqui usamos la variable $nombre para que nos muestre que factura vamos a relacionar -->
     Cambiando estado de la factura:
</div> 
<div class="caja-rel">
  <form name="rel_form" method="post" action="index.php?module=invoice&action=do_rel&id_factura=<?=$id_factura?>"> 
  <table>
    <tr>
     <td> Estado </td>
     <td>
     <?
    echo "<select name='estado'>";
    echo "<option selected value=$estado_actual[0]> $estado_actual[0] </option>";
    if ($estado_actual[0] == pagado) {
	echo "<option value='pendiente'> pendiente </option>";
    }
    
    else {
	echo "<option value='pagado'> pagado </option>";	     
    }
    ?>
	</select>
	  </td>
	</tr>
	<tr>
	  <td></td>
	  <td>
	    <input type="submit" value="Cambiar estado" name="button">
	  </td>
	</tr>
      </table>
      </form>  
    </div>

<?
}

function formulario_total_trimestral () {
	
?>
<div class="caja-rel">
  <form name="rel_form" method="post" action="index.php?module=invoice&action=do_total_trimestral"> 
  <table>
    <tr>
      <td> Año </td>
	<td>
	  <input type="text" name="anno" size="22">
	  </td>
	</tr>
	<tr>
	<td> Trimestre </td>
		<td>
	<select name="trimestre">
	<option selected value="1">Primer trimestre (01-03)</option>	      
	<option value="4">Segundo trimestre (04-06)</option>
	<option value="7">Tercer trimestre (07-09)</option>
	<option value="10">Cuarto trimestre (10-12)</option>
	    </select>
	  </td>
	<tr>
	  <td></td>
	  <td>
	    <input type="submit" value="Ver lista" name="button">
	  </td>
	</tr>
      </table>
      </form>  
    </div>

<?
}

function mostrar_lista_de_relacion ($query) {
    echo "<table class='item-list'>\n";
    echo "<tr class='table-header'><td>Usuario</td><td>Máquina</td><td>Acciones</td></tr>";
    $par = 0;
    while ($concept = next_row ($query)) {
        $class = ($par ? "fila-par" : "fila-impar");
        $par   = ($par ? 0 : 1);
        echo "<tr class='" . $class  .  "'>";
        echo "<td>" . $concept[0] . "</td>";
        echo "<td class='colum_der'>" . $concept[1] . "</td>";
	echo "<td class='colum_der'><a href='admin.php?module=user&action=delete_rel&name=" . $concept[0] . "&maq=" . $concept[1] . "&id=" . $concept[2] . "'>Borrar relación</a> ";
	echo "| <a href='admin.php?module=user&action=show_machines&name=" . $concept[0] . "&maq=" . $concept[1] . "&id=" . $concept[2] . "'>Equipos</a> ";
	echo "</td>";
        echo "</tr>";
    }
    echo "</table>\n";
}

function lista_de_relacion () {
    /* nos conectamos a la base de datos */
    $link = get_connection ();
    $codificacion = do_query ("SET CHARACTER SET UTF8;", $link);
    /* listamos los usuarios creados */
    $query = do_query ("SELECT usuario, maquina, id from maquina_usuario ORDER BY usuario", $link);

    /* mostramos los resultados */
    mostrar_lista_de_relacion($query);

    /* cerramos la conexión */
    close_conn ($link);

    return;
}

  function rel_estado ($estado, $id_factura) {    

      /* nos conectamos a la base de datos */
      $link = get_connection ();
      $codificacion = do_query ("SET CHARACTER SET UTF8;", $link);
      /* listamos los usuarios creados */
      $result = do_query("UPDATE facturas " .
			 "SET estado='$estado' WHERE id='$id_factura'; ", $link);


    /* cerramos la conexión */
    close_conn ($link);

    /* Mostramos formulario */

    list_invoices ();
}

function borrar_relacion ($id) {

    /* nos conectamos a la base de datos */
    $link = get_connection ();
    $codificacion = do_query ("SET CHARACTER SET UTF8;", $link);
    /* listamos los usuarios creados */
    $result = do_query ("DELETE FROM maquina_usuario WHERE id='$id'", $link);

    /* cerramos la conexión */
    close_conn ($link);
    

    return;
}

function editar_concepto ($id_concepto, $id_factura, $numero, $cliente, $fecha, $observaciones) {

    $concepto = $_POST{"concepto"};
    $cantidad = $_POST{"cantidad"};
    $precio_unidad = $_POST{"precio_unidad"};
    $importe = $cantidad * $precio_unidad;

    /* nos conectamos a la base de datos */
    $link = get_connection ();
    $codificacion = do_query ("SET CHARACTER SET UTF8;", $link);
    /* listamos los usuarios creados */
    $result = do_query ("UPDATE conceptos set concepto='$concepto', cantidad='$cantidad', precio_unidad='$precio_unidad', importe='$importe'  WHERE id='$id_concepto';", $link);

    /* Llamamos a la funcion que muestra el formulario de conceptos editado */
    formulario_introducir_conceptos($id_factura, $numero, $fecha, $cliente, $observaciones);

    /* cerramos la conexión */
    
    return;
}

function borrar_concepto ($id_concepto, $id_factura, $numero, $cliente, $fecha) {

    /* nos conectamos a la base de datos */
    $link = get_connection ();
    $codificacion = do_query ("SET CHARACTER SET UTF8;", $link);
    /* listamos los usuarios creados */
    $result = do_query ("DELETE from conceptos WHERE id='$id_concepto'", $link);

    /* Llamamos a la funcion que muestra el formulario de conceptos editado */
    formulario_introducir_conceptos($id_factura, $numero, $fecha, $cliente);
    
    return;
}

function show ($id) {

    $result = exec("python /home/dani/workspace/openinvo_aux/Programa_facturacion/factura.py " . $id . " > /tmp/prueba-py.log");

    $filename = "factura" . $id . ".odt";

    /* configuramos la cabecera con el nombre del fichero */
    header("Content-Disposition:  filename={$filename}");
 
    /* Tell the browser that the content that is coming is an xpinstall */
    header('Content-type: application/vnd.oasis.opendocument.text');
 
    /* Also send the content length */
    header('Content-Length: ' . filesize($filename));
 
    /* readfile reads the file content and echos it to the output */
    readfile($filename);

    /* borramos el fichero */
    unlink ($filename);

    exit (0);


}

function invoices_operation ($action) {

?>  <div class="caja-menu">
      <ul class="menu">
        <li><a href="index.php?module=invoice&action=list">Listar</a></li>
        <li><a href="index.php?module=invoice&action=create">Crear</a></li>
	<li><a href="index.php?module=invoice&action=total_trimestral">Total trimestral</a></li>
      </ul>
    </div>
    <div class="caja-titulo">
      FACTURAS
    </div>

    <div class="caja-com">
<?

    switch ($action) {
    case "list":
    list_invoices ();
    break;

    case "create":
    form_new_invoice ();
    break;
    
    case "do_create":
    nueva_factura ($_POST{"cliente"}, $_POST{"numero"}, $_POST{"fecha"});
    break;

    case "crear_conceptos":
    introducir_conceptos ($_POST{"cliente"}, $_POST{"numero"}, $_POST{"fecha"});
    break;

    case "form_edit_concept":
    formulario_editar_concepto ($_GET{"name"}, $_GET{"id_factura"}, $_GET{"cliente"}, $_GET{"numero"}, $_GET{"fecha"}, $_GET{"observaciones"});
    break;

    case "edit_concept":
    editar_concepto ($_GET{"name"}, $_GET{"id_factura"}, $_GET{"numero"}, $_GET{"cliente"}, $_GET{"fecha"}, $_GET{"observaciones"});
    break;

    case "do_edit_concept":
    editar_concepto_creado ($_GET{"name"}, $_GET{"id_factura"});
    break;

    case "delete_concept":
    borrar_concepto ($_GET{"name"}, $_GET{"id_factura"}, $_GET{"numero"}, $_GET{"cliente"}, $_GET{"fecha"});
    break;
    
    case "suprimir_concepto":
    suprimir_concepto ($_GET{"name"}, $_GET{"id_factura"});
    break;

    case "delete":
    borrar_factura ($_GET{"name"});
    list_invoices ();
    break;
    
    case "edit":
    formulario_editar_factura ($_GET{"name"});
    break;

    case "add_concept":
    formulario_editar_nuevo_concepto ($_GET{"id"});
    break;

    case "do_add_concept":
    nuevo_concepto ($_GET{"id"});
    break;
    
    case "do_edit":
    editar_factura ($_GET{"name"});
    break; 
   
    case "rel":
    rel_form_estado ($_GET{"id_factura"});
    break;

    case "total_trimestral":
    formulario_total_trimestral();
    break;
    
    case "pagina_editar_concepto":
    pagina_editar_concepto ($_GET{"name"}, $_GET{"factura_id"});
    break;
    
    case "do_rel":
    rel_estado ($_POST{"estado"}, $_GET{"id_factura"} );
    break;

    case "table_rel":
    lista_de_relacion ();
    break;
    
    case "borrar_relacion":
    borrar_relacion ($_GET{"id"});
    lista_de_relacion ();
    break;

    case "show_machines":
    lista_de_equipos ($_GET{"name"});
    break;

    case "show":
    show_doc ($_GET{"id"});
    break;

    case "do_total_trimestral":
    total_trimestral ($_POST{"anno"}, $_POST{"trimestre"});
    break;

    default:
    list_invoices ();
    break;
    }
?> </div> <?

    return;
}
 
/* cerramos la conexión */
close_conn ($enlace);

?>
