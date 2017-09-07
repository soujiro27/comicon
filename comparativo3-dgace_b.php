<!DOCTYPE html>

<?php
	require 'src/conexion.php';	
	$p1 = $_GET['param1'];
	$titulo = $_GET['titulo'];
	?>
	
<html lang="en"><head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <title>SISTEMA INTEGRAL DE AUDITORIAS: <?php echo $titulo ?></title>
  <meta name="description" content="Reporte de Totales">
  <meta name="author" content="JOSE COTA">
  	
  <!-- Stylesheets -->
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <!-- Font awesome icon -->
  <link rel="stylesheet" href="css/font-awesome.min.css"> 
  <!-- jQuery UI -->
  <link rel="stylesheet" href="css/jquery-ui.css"> 
  <!-- Calendar -->
  <link rel="stylesheet" href="css/fullcalendar.css">
  <!-- prettyPhoto -->
  <link rel="stylesheet" href="css/prettyPhoto.css">  
  <!-- Star rating -->
  <link rel="stylesheet" href="css/rateit.css">
  <!-- Date picker -->
  <link rel="stylesheet" href="css/bootstrap-datetimepicker.min.css">
  <!-- CLEditor -->
  <link rel="stylesheet" href="css/jquery.cleditor.css"> 
  <!-- Data tables
	<link rel="stylesheet" href="css/jquery.dataTables.css"> 
   -->
  <!-- Bootstrap toggle -->
  <link rel="stylesheet" href="css/jquery.onoff.css">
  <!-- Main stylesheet -->
  <link href="css/style-dashboard.css" rel="stylesheet">
  <!-- Widgets stylesheet -->
  <link href="css/widgets.css" rel="stylesheet"> 
  
<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
      
	<style type="text/css">		
		@media screen and (min-width: 968px) {
		}
		
		body{
			background:transparent;
			text-align=center;
			font-family:Arial, Helvetica, sans-serif;
			 padding:20px;
		}
		.pieReporte{color: gray;    font-family: courier;    font-size: 70%;}
		.encabezado{font: normal bold 12px/15px Arial;}
		th{background:#f4f4f4; color:black;
			font: normal bold 10px/15px Arial;
			text-align:center;
			vertical-align:middle;
		}
		td{background:white; color:black;
			font: normal 10px/15px Arial;
			text-align:center;
		}

		
		
	</style>

  <!--[if lt IE 9]>
  <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
  
  <script type="text/javascript"> 
	function imprimir(){				
		document.all.divBotones.style.display='none';	
		window.print();
		document.all.divBotones.style.display='inline';					
	}
	
  </script>
</head>
<body>
  <div class="col-md-8" style="padding:0px">  
		<div class="container-fluid">
					<div class="col-xs-3"><img src="img/logo-top.png"></div>								
					<div class="col-xs-9">
						<p class="encabezado">
							<b>AUDITORÍA SUPERIOR DE LA CIUDAD DE MÉXICO</b><br>
							DIRECCIÓN GENERAL DE AUDITORÍA DE CUMPLIMIENTO FINANCIERO "A"<br>
							<strong><?php echo $titulo ?></strong><br>
							(Cantidades en Miles de Pesos)<br><br>						
							EJERCICIOS <?php echo $p1 ?><br>
						</p>
					</div>												
		</div>
		<table class="table table-responsive table-striped table-bordered table-hover table-condensed">
		  <thead>
			<tr><th colspan=9>DIRECCIÓN GENERAL DE AUDITORÍA DE CUMPLIMIENTO FINANCIERO "A"</th></tr>			
			<tr><th width="5%">CAPÍTULO</th><th>APROBADO(1)</th><th>MODIFICADO(2)</th><th>COMPROMETIDO(3)</th><th>DEVENGADO(4)</th><th>EJERCIDO(6)</th><th>PAGADO(6)</th><th>IMPORTE DE LA VARIACIÓN</th><th>%</th></tr>
		  </thead>		  
		  <tbody>
			<tr><td colspan=9></tr></tr>
			<tr><td colspan=9></tr></tr>
			<tr><td colspan=9></tr></tr>
			<tr><td colspan=9></tr></tr>
			<tr><td colspan=9></tr></tr>
			<tr><td colspan=9></tr></tr>
			<tr><td colspan=9></tr></tr>			
		  </tbody>
		</table>
		<p class="pieReporte">
			* Sin asignación original  **No aplica ejercido<br>
			Fuente: Informe de cuenta pública <?php echo $p1 ?>.
		</p>
		<br>
		<div class="col-md-4 style="padding:0px"></div>
		<div class="col-md-2 style="padding:0px">
			<p id="divBotones">
				<button  onclick="javascript:window.close();" class="btn btn-default btn-xs"></span> Cerrar</button>
				<button  class="btn btn-default btn-xs" onclick="imprimir();">Imprimir</button>
			</p>			
		</div>
		<div class="col-md-4 style="padding:0px"></div>			

	</div>	

	<div class="col-md-4 style="padding:0px">
	
	</div>
  
  
</body>
</html>

