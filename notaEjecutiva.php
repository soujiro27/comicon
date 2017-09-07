<!DOCTYPE html>

<?php
	require 'src/conexion.php';	
	$p1 = $_GET['param1'];
	//$p2 = $_GET['param2'];
	?>
	
<html lang="en"><head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <title>NOTA EJECUTIVA DE AUDITORÍA</title>
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
		}
		td{background:#f4f4f4; color:black;
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
						UNIDAD TÉCNICA SUSTANTIVA DE FISCALIZACIÓN<br>
						FINANCIERA Y ADMINISTRACIÓN<br>
						DIRECCIÓN GENERAL DE AUDITORÍA<br>DE CUMPLIMIENTO FINANCIERO<br>
						<strong>NOTA EJECUTIVA DE AUDITORÍA</strong><br>						
						</p>
					</div>												
		</div>
		<table class="table table-responsive table-striped table-bordered table-hover table-condensed">
		  <thead></thead>		  
		  <tbody>
			<tr>
				<td><b>ENTE SUJETO DE FISCALIZACIÓN</b><br>
					SERVICIOS DE SALUD PÚBLICA DEL DISTRITO FEDERAL
				</td>
				<td><b>CLAVE DE LA AUDITORÍA:</b><br>
					ASCM-453434356
				</td>				
				<td>
					<b>TOTAL DE RESULTADOS:</b> 22<br>
					<b>TOTAL DE RECOMENDACIONES:</b> 17
				</td>				
			</tr>		  
			<tr>
				<td><b>FECHA DE INICIO:</b><br>
					SERVICIOS DE SALUD PÚBLICA DEL DISTRITO FEDERAL
				</td>
				<td><b>FECHA DE ENTREGA DEL ENTE:</b><br>
					ASCM-453434356
				</td>				
				<td>
					<b>POTENCIALES PROMOCIONES DE ACCIONES:</b> 3<br>
				</td>				
			</tr>	
			<tr>
				<td><b>TIPO DE AUDITORÍA:</b><br>
					FINANCIERA
				</td>
				<td colspan=2><b>RUBRO O VERTIENTE DEL GASTO:</b><br>
					FONDO DE APORTACIONES PARA LOS SERVICIOS DE SALUD
				</td>								
			</tr>
			<tr>
				<td colspan=3><b>OBJETIVO DE AUDITORÍA:</b><br>
					FONDO DE APORTACIONES PARA LOS SERVICIOS DE SALUD APORTACIONES PARA LOS SERVICIOS DE SALUD APORTACIONES PARA LOS SERVICIOS DE SALUD APORTACIONES PARA LOS SERVICIOS DE SALUD APORTACIONES PARA LOS SERVICIOS DE SALUD 
				</td>								
			</tr>
			<tr>
				<td><b>MONTO DEL ENTE:</b><br>
					3,440,054.7 MILES DE PESOS
				</td>
				<td><b>MONTO DE CAPÍTULO O PARTIDA:</b><br>
					3,440,054.7 MILES DE PESOS
				</td>				
				<td><b>MONTO DEL MUESTRA:</b><br>
					21,250.8 MILES DE PESOS
				</td>				
			</tr>	
			<tr><td colspan=3><b>RESULTADOS RELEVANTES</b><br><br><br><br><br><br><br><br><br></td></tr>						
			<tr><td colspan=3><b>OBSERVACIONES (EN SU CASO)</b><br><br><br><br><br><br><br><br><br></td></tr>
			<tr>
				<td colspan=3><b>PROPUESTAS LEGISLATIVAS</b><br><b>PROPUESTAS NORMATIVAS</b><br><b>PROPUESTAS DE COLABORACIÓN Y COORDINACIÓN INTERINSTITUCIONAL</b><br><b>PROPUESTAS DE MEJORAS DE LA GESTIÓN</b></td>
			</tr>
		  </tbody>
		</table>
		<p class="pieReporte">
			n.a. No aplicable<br>
			Fuente: Informe de cuenta pública 2014 del Fideicomiso de Recuperación Crediticia del Distrito Federal (FIDERE III)
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

