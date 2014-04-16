<?php

/* include modules */
include ("lib/common.php");        /* Common operations */
include ("lib/dbconn.php");        /* Data base operations */
include ("src/clients.php");       // Client operations */
include ("src/invoices.php");
/* include ("presupuestos.php");*/
/* include ("logs.php"); */

?>

<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>OpenInvo</title>

<link rel="stylesheet" type="text/css" href="styles/style.css" />


<!--Include javascript funcions-->
<script src="lib/common.js">
</script>

</head>
<body>
<!--Global container-->
<div class="container">
    <!--Clock-->
    <div class="clock" id="clock">
      <script>
        updateClock();
      </script>
    </div>
  
    <!--Header menu-->
    <div class="header">    
      <ul class="menu">
	<li><a href="index.php?module=client&action=list">Clientes</a></li>
	<li><a href="index.php?module=invoice&action=list">Facturas</a></li>
	<li><a href="index.php?module=budget&action=list">Presupuestos</a></li>
      </ul> 
    </div>

    <!--Data-->
    <div class="data">

<?php
$module = get_value ("module");
$action = get_value ("action");

switch ($module) {

case "invoice":
	/* Call to the proper invoices operation */
	invoices_operation ($action);
	break;

case "budget":
	/* Call to the proper budgets operation */
	operacion_presupuesto ($action);
	break;

default:
	/* Call to the proper clients operation */
	clients_operation ($action);
	break;
}

?>

    </div>

    <!--Botton to go to init page-->
    <div class="back-init">
       <a href="index.php">Volver a la página de inicio</a>
    </div>
    
</div>
<!--</form>-->

</body>

</html>
