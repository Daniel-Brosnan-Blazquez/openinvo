
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
	<li><a href="facturacion.php?module=cliente&action=list">Clientes</a></li>
	<li><a href="facturacion.php?module=factura&action=list">Facturas</a></li>
	<li><a href="facturacion.php?module=presupuesto&action=list">Presupuestos</a></li>
      </ul> 
    </div>

    <!--Data-->
    <div class="data">
    </div>

    <!--Botton to go to init page-->
    <div class="back-init">
       <a href="index.php">Volver a la página de inicio</a>
    </div>
    
</div>
<!--</form>-->

</body>

</html>

