<!DOCTYPE html>
<?php
	require 'src/conexion.php';	
	$area = $_GET['area'];
	$cuenta = $_GET['param1']; 	//Cuenta
	$p1 = $_GET['param2'];	//F1
	$p2 = $_GET['param3'];	//F2
	
	$p1 = strtr($p1, "/", "-");
	$p2 = strtr($p2, "/", "-");
	
			
	$f1 = Date_create($p1);
	$f1 = Date_format($f1, "d/m/Y");
	
	$f2 = Date_create($p2);
	$f2 = Date_format($f2, "d/m/Y");			
	
	?>	
<html lang="en">
	<head>
		<!-- Title and other stuffs -->
		<title>Sistema Integral de Auditorias</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="">
		<meta name="keywords" content="">
		<meta name="author" content="">	
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"> 
		<meta http-equiv="X-UA-Compatible" content="IE=Edge" />  
  	
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
  
	<script type='text/javascript' src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js?ver=3.1.2"></script>
	<script src="js/canvasjs.min.js"></script>	
	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">	
	<script type="text/javascript" src="js/genericas.js"></script>	
      
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
			text-align:justify;
		}
		#cvGrafica{height:250px; width:100%;}
		
		
	</style>

  <!--[if lt IE 9]>
  <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
  
  <script type="text/javascript">
  
  var f1, f2;
  
  <?php echo "\n f1='" . $p1 . "';"?>
  <?php echo "\n f2='" . $p2 . "';"?>
  
	function imprimir(){				
		document.all.divBotones.style.display='none';	
		window.print();
		document.all.divBotones.style.display='inline';					
	}
		
	window.onload = function () {
		var chart1; 
		var sRuta ="dpsMonitorAuditoresByDireccion/" + f1 + "/" + f2;				
			setGrafica(chart1, sRuta  , "bar", "ACCESOS POR AUDITOR", "cvGrafica" );
	};	
	
  </script>
</head>
<body>
  <div class="col-md-8" style="padding:0px">  
		<div class="container-fluid">
					<div class="col-xs-3"><img src="img/logo-top.png"></div>								
					<div class="col-xs-9">
						<p class="encabezado">
						AUDITORÍA SUPERIOR DE LA CIUDAD DE MÉXICO<br>
						DIRECCIÓN GENERAL DE AUDITORÍA<br>DE CUMPLIMIENTO FINANCIERO "C"<br>
						<strong>AVANCES POR AUDITOR DEL <?php echo $f1 . " AL " . $f2 ?><BR>CUENTA PÚBLICA <?php echo $cuenta ?></strong><br>						
						</p>
					</div>												
		</div>						
		<br>
		<div class="col-xs-12"><div id="cvGrafica"></div></div>
		<br>
		<table class="table table-responsive table-striped  table-hover table-condensed">
		  <thead>
			<th width="30%">DATOS DEL AUDITOR</th><th width="15%">PAPELES DE TRABAJO</th><th width="15%">ARCHIVOS / EVIDENCIAS</th><th width="15%">AVANCES</th><th width="15%">ACCESOS</th>
		  </thead>
			<tbody>
		<?php
			$capitulo="";
			$nTotalRegistros=0;
			

			$sql = "SELECT distinct concat(e.idEmpleado, ' ',e.nombre, ' ', e.paterno, ' ', e.materno) empleado, " .
				"(Select count(*) FROM sia_papeles p WHERE p.usrAlta=u.idUsuario  AND p.fAlta >= convert(datetime, '$f1') and p.fAlta<=convert(datetime, '$f2')) papeles, " .
				"(Select count(*) FROM sia_accesos p WHERE p.idUsuario=u.idUsuario AND p.fIngreso >= convert(datetime, '$f1') and p.fIngreso<=convert(datetime, '$f2')) accesos, " .
				"(Select count(*) FROM sia_acopio ac  WHERE ac.usrAlta=u.idUsuario AND ac.fAlta >= convert(datetime, '$f1') and ac.fAlta<=convert(datetime, '$f2')) acopios,  " .
				"(Select count(*) FROM sia_auditoriasavances x  WHERE u.idUsuario=x.usrAlta AND x.fAlta >= convert(datetime, '$f1') and x.fAlta<=convert(datetime, '$f2')) avances  " .
			"FROM sia_auditoriasauditores aa  " .
			"INNER JOIN sia_empleados e ON aa.idAuditor=e.idEmpleado " .
			"LEFT JOIN sia_usuarios u ON e.idEmpleado = u.idEmpleado  " .
			"WHERE aa.idCuenta=:cuenta and e.idArea=:area " .
			"ORDER BY concat(e.idEmpleado, ' ',e.nombre, ' ', e.paterno, ' ', e.materno);";
				
				//echo "<hr>$sql<hr>";

			$db = new PDO("sqlsrv:Server={$hostname}; Database={$database}", $username, $password );			
			$dbQuery = $db->prepare($sql);		
			//$dbQuery->execute();
			$dbQuery->execute(array(':cuenta' => $cuenta, ':area' => $area));
			$result = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
			foreach($result as $row) {
				echo "<tr><td style='text-align:center'><b>".$row['empleado']."</b></td><td style='text-align:center'>".$row['papeles'].
				"</td><td style='text-align:center'>".$row['acopios']."</td><td style='text-align:center'>".$row['avances']."</td><td style='text-align:center'>".$row['accesos']."</td></tr>";
			}
			$db = null;
		?>
		  
		  
		  </tbody>
		</table>
		<p class="pieReporte">
			Fuente: PGA <?php echo $p1 ?><br>
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

	<div class="col-md-4 style="padding:0px"></div>
  
  
</body>
</html>

