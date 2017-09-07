<!DOCTYPE html>

<?php
	require 'src/conexion.php';	
	$p1 = $_GET['param1'];
	$p2 = $_GET['param2'];
	?>
	
<html lang="en"><head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <title>INTEGRACIÓN DEL PRESUPUESTO POR PARTIDA PRESUPUESTAL</title>
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
  <!-- Data tables -->
  <link rel="stylesheet" href="css/jquery.dataTables.css"> 
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
						DIRECCIÓN GENERAL DE AUDITORÍA A ENTIDADES PÚBLICAS Y ÓRGANOS AUTÓNOMOS<br>
						FIDEICOMISO DE RECUPERACIÓN CREDITICIA DE LA CIUDAD DE MÉXICO<br>
						CUENTA PÚBLICA <?php echo $p2 ?><br>
						<strong>INGRESOS DEL SECTOR GOBIERNO</strong><br>
						<div class="pull-right" style="background:black;color:white; padding:5px; border:1px solid gray;">ANEXO 4</div>
						<br>(Miles de Pesos)<br>
						</p>
					</div>												
		</div>
		<table class="table table-responsive table-striped table-bordered table-hover table-condensed">
		  <thead>
		  
		  
			<tr><th rowspan=4 style="vertical-align:middle" Width="40%">CONCEPTO</th><th colspan=2><?php echo $p1 ?></th><th colspan=4><?php echo $p2 ?></th><th colspan=4>Variaciones</th></tr>
				<tr>
					<th rowspan=3>RECAUDADO</th><th rowspan=3>%</th><th rowspan=3>ORIGINAL</th><th rowspan=3>%</th><th rowspan=3>RECAUDADO</th><th rowspan=3>%</th><th colspan=2>Absoluta</th><th colspan=2>Relativa</th>
				</tr>
				<tr><th><?php echo $p1 ?></th><th><?php echo $p1 . "-" . $p2 ?></th><th><?php echo $p1 ?></th><th><?php echo $p1 . "-" . $p2 ?></th></tr>
				<tr><th>Importe</th><th>Importe</th><th>%</th><th>%</th></tr>
			</thead>	
		  <tbody>
			<tr><td colspan=11></tr>			
			
		<?php
			$capitulo="";
			$origen="";
			$nTotalRegistros=0;
		
			$sql = "SELECT origen, tipo, clave, nombre, format(SUM(CAST(oriOld AS NUMERIC(15,2))), '$#,##0.00;($#,##0.00)') oriOld, format(SUM(CAST(recOld AS NUMERIC(15,2))), '$#,##0.00;($#,##0.00)') recOld, " .
			"format(SUM(CAST(oriNew AS NUMERIC(15,2))), '$#,##0.00;($#,##0.00)') oriNew, format(SUM(CAST(recNew AS NUMERIC(15,2))), '$#,##0.00;($#,##0.00)') recNew " . 
			"FROM( " . 
			"  SELECT origen, tipo, clave, nombre, original oriOld, recaudado recOld, '0' oriNew, '0' recNew FROM sia_cuentasingresos " . 
			"UNION ALL " .
			"  SELECT origen, tipo, clave, nombre, '0' oriOld, '0' recOld, original oriNew, recaudado recNew FROM sia_cuentasingresos " .
			") d " . 
			" GROUP BY origen, tipo, clave, nombre ORDER BY clave";
						
			//$db = new PDO('mysql:host='. $hostname . ';dbname='. $database . ';charset=utf8',$username, $password );			
			//SQL Server
			$db = new PDO("sqlsrv:Server={$hostname}; Database={$database}", $username, $password );			
			
			
			$dbQuery = $db->prepare($sql);		
			$dbQuery->execute();
			$result = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
			//echo "<tr><td></td><th>CORRIENTES</th><td colspan=11></td></tr>";
			foreach($result as $row) {	
			
				if($origen!= $row['origen']){					
					$origen=$row['origen'];
					echo "<tr><th  style='text-align: left;'>" . $origen ."</th><td colspan=10></tr>";					
				}
				if($capitulo!= $row['tipo']){					
					$capitulo=$row['tipo'];
					echo "<tr><th  style='text-align: left;'>&nbsp;&nbsp;&nbsp;&nbsp;" . $capitulo ."</th><td colspan=10></tr>";					
				}
				echo "<tr><td style='text-align: left;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$row['clave']. " " . " ".$row['nombre'] . 
				"</td><td>".$row['recOld']. "</td><td></td>" .
				"</td><td>".$row['oriNew']. "</td><td></td>" .
				"</td><td>".$row['recNew']. "</td><td></td>" .				
				"";
				echo "<td colspan=6></td>";
				echo "</tr>";
				
			}
			echo "<tr><td colspan=11></td></tr>";				
			//echo "<hr> SQL:<br>" . $sql . "<hr>";
			
			$db = null;
			
			
			
		?>

			<tr><td></td><th>TOTAL</th><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>			
			
			
		  </tbody>
		</table>
		<p class="pieReporte">
			n.a. No aplicable<br>
			Fuente: Informe de cuenta pública <?php echo $p2 ?> del Fideicomiso de Recuperación Crediticia del Distrito Federal (FIDERE III)
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

