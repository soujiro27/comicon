<!DOCTYPE html>

<?php
	require 'src/conexion.php';	
	$p1 = $_GET['param1'];
	$p2 = $_GET['param2'];
	?>
	
<html lang="en"><head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <title>INTEGRACIÓN DEL PRESUPUESTO POR CAPÍTULO DEL GASTO</title>
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
						<strong>INTEGRACIÓN DEL PRESUPUESTO POR CAPÍTULO DEL GASTO</strong><br>
						<div class="pull-right" style="background:black;color:white; padding:5px; border:1px solid gray;">ANEXO 2</div>
						<br>(Miles de Pesos)<br>
						</p>
					</div>												
		</div>
		<table class="table table-responsive table-striped table-bordered table-hover table-condensed">
		  <thead>
			<tr><th rowspan=3 style="vertical-align:middle">CAPÍTULO</th><th rowspan=3  style="vertical-align:middle">CONCEPTO</th><th><?php echo $p1 ?></th><th colspan=6><?php echo $p2 ?></th><th colspan=6>VARIACIONES</th></tr>
			<tr><th rowspan=2>EJERCIDO<br>(1)</th><th rowspan=2>ORIGINAL<br>(2)</th><th rowspan=2>MODIFICADO<br>(3)</th><th rowspan=2>DEVENGADO<br>( )</th><th rowspan=2>EJERCIDO<br>(4)</th><th rowspan=2>% PART</th><th rowspan=2>PAGADO<br></th><th colspan=2>EJERCIDO <?php echo $p1 . " - " . $p2 ?></th><th colspan=2>EJERCIDO ORIGINAL</th><th colspan=2>EJERCIDO MODIFICADO</th></tr>
			<tr><th>5 = (4) - (1)</th><th>%</th><th>6 = (4) - (2)</th><th>%</th><th>7 = (4) - (3)</th><th>%</th></tr>
		  </thead>		  
		  <tbody>
			<tr><td COLSPAN=13></tr>			
			
<?php
			
			$nTotalRegistros=0;
			$sql = "SELECT format(sum(d.ejeP1)/1000, '$#,##0.00;($#,##0.00)') ejeP1 ".
    					", format(sum(d.oriP2)/1000, '$#,##0.00;($#,##0.00)') oriP2 ".
    					", format(sum(d.modP2)/1000, '$#,##0.00;($#,##0.00)') modP2 ".
    					", format(sum(d.modP2)/1000, '$#,##0.00;($#,##0.00)') devP2 ".
    					", format(sum(d.ejeP2)/1000, '$#,##0.00;($#,##0.00)') ejeP2 ".
    					", format(((sum(d.ejeP2)/1000) * 100) / sum(d.modP2) , '##0.00;(##0.00)') part ".
    					", format(sum(d.pagP2)/1000, '$#,##0.00;($#,##0.00)') pagP2 ".
						", format(sum(d.ejeP2 - d.ejeP1)/1000, '$#,##0.00;($#,##0.00)') varEje ".
						", format( CASE WHEN sum(d.ejeP1)=0 THEN 0 ELSE ( ((sum(d.ejeP2-d.ejeP1)/1000)*100) / (sum(d.ejeP1)/1000) ) END , '##0.00;(##0.00)') varEjePor ".
    					", format(sum(d.ejeP2 - d.oriP2)/1000, '$#,##0.00;($#,##0.00)') varEjeOri ".
						", format( CASE WHEN sum(d.ejeP2)=0 THEN 0 ELSE ( ((sum(d.ejeP2-d.oriP2)/1000)*100) / (sum(d.ejeP2)/1000) ) END , '##0.00;(##0.00)') varEjeOriPor ".
    					", format(sum(d.ejeP2 - d.modP2)/1000, '$#,##0.00;($#,##0.00)') varEjeMod ".
						", format( CASE WHEN sum(d.ejeP2)=0 THEN 0 ELSE ( ((sum(d.ejeP2-d.modP2)/1000)*100) / (sum(d.ejeP2)/1000) ) END, '##0.00;(##0.00)') varEjeModPor ".
    				"FROM( 	SELECT sum(convert(DECIMAL(20,2), ejercido)) ejeP1 , '0' oriP2, '0' modP2, '0' ejeP2, '0' pagP2 ".
            				"FROM sia_cuentasdetalles WHERE idCuenta='" . $p1 . "' ".
		    			    "UNION  ALL ".
				    		"SELECT '0' ejeP1, sum(convert(DECIMAL(20,2), original)) oriP2, sum(convert(DECIMAL(20,2), modificado)) modP2 ".
            					"  , sum(convert(DECIMAL(20,2), ejercido)) ejeP2, sum(convert(DECIMAL(20,2), pagado)) pagP2 ".
		        			"FROM sia_cuentasdetalles WHERE idCuenta='" . $p2 . "' ) d;";
			
			//echo "<hr> SQL:<br>" . $sql . "<hr>";
					
				//MySQL
				//$db = new PDO('mysql:host='. $hostname . ';dbname='. $database . ';charset=utf8',$username, $password );

				//SQL Server
				$db = new PDO("sqlsrv:Server={$hostname}; Database={$database}", $username, $password );
				
			$dbQuery = $db->prepare($sql);		
			//$dbQuery->execute(array(':cuenta' => $cuenta, ':pass' => $pass));
			$dbQuery->execute();
			$result = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
			foreach($result as $row) {
				echo "<tr><td></td><th>TOTAL DE EGRESOS</th>";
				echo "<td>".$row['ejeP1']."</td>";
				echo "<td>".$row['oriP2']."</td>";
				echo "<td>".$row['modP2']."</td>";
				echo "<td>".$row['devP2']."</td>";
				echo "<td>".$row['ejeP2']."</td>";
				echo "<td>".$row['part']."</td>";
				echo "<td>".$row['pagP2']."</td>";
				echo "<td>".$row['varEje']."</td>";
				echo "<td>".$row['varEjePor']."</td>";
				echo "<td>".$row['varEjeOri']."</td>";
				echo "<td>".$row['varEjeOriPor']."</td>";
				echo "<td>".$row['varEjeMod']."</td>";
				echo "<td>".$row['varEjeModPor']."</td>";
				echo "</tr>";	
			}
			//echo "<tr><td colspan=13></td></tr>";	
			
			$sql = "SELECT d.capitulo, c.nombre ".
						",format(sum(d.ejeP1)/1000, '$#,##0.00;($#,##0.00)') ejeP1 ".
      					",format(sum(d.oriP2)/1000, '$#,##0.00;($#,##0.00)') oriP2 ".
						",format(sum(d.modP2)/1000, '$#,##0.00;($#,##0.00)') modP2 ".
						",format(sum(d.devP2)/1000, '$#,##0.00;($#,##0.00)') devP2 ".
      					",format(sum(d.ejeP2)/1000, '$#,##0.00;($#,##0.00)') ejeP2 ".
      					",format((sum(d.ejeP2)/1000) * 100 / (SELECT sum(convert(DECIMAL(20,2), modificado))/1000 FROM sia_cuentasdetalles WHERE idCuenta='" . $p2 . "'), '##0.00;(##0.00)') part ".
      					",format(sum(d.pagP2)/1000, '$#,##0.00;($#,##0.00)') pagP2 ".
						",format(sum(d.ejeP2 - d.ejeP1)/1000, '$#,##0.00;($#,##0.00)') varEje ".
						",format( CASE WHEN sum(d.ejeP1)=0 THEN 0 ELSE ( ((sum(d.ejeP2-d.ejeP1)/1000)*100) / (sum(d.ejeP1)/1000) ) END , '##0.00;(##0.00)') varEjePor ".
      					",format(sum(d.ejeP2 - d.oriP2)/1000, '$#,##0.00;($#,##0.00)') varEjeOri ".
						",format( CASE WHEN sum(d.ejeP2)=0 THEN 0 ELSE ( ((sum(d.ejeP2-d.oriP2)/1000)*100) / (sum(d.ejeP2)/1000) ) END , '##0.00;(##0.00)') varEjeOriPor ".
    					",format(sum(d.ejeP2 - d.modP2)/1000, '$#,##0.00;($#,##0.00)') varEjeMod ".
						",format( CASE WHEN sum(d.ejeP2)=0 THEN 0 ELSE ( ((sum(d.ejeP2-d.modP2)/1000)*100) / (sum(d.ejeP2)/1000) ) END, '##0.00;(##0.00)') varEjeModPor ".
					"FROM(	SELECT capitulo, sum(convert(DECIMAL(20,2), ejercido)) ejeP1 , '0' oriP2, '0' modP2, '0' devP2, '0' ejeP2, '0' pagP2  ".
            				"FROM sia_cuentasdetalles WHERE idCuenta='" . $p1 . "' GROUP BY capitulo ".
			      			"UNION  ALL ".
				    		"SELECT capitulo, '0' ejeP1 ".
            					", sum(convert(DECIMAL(20,2), original)) oriP2 ".
            					", sum(convert(DECIMAL(20,2), modificado)) modP2 ".
            					", sum(convert(DECIMAL(20,2), modificado)) devP2 ".
            					", sum(convert(DECIMAL(20,2), ejercido)) ejeP2 ".
            					", sum(convert(DECIMAL(20,2), pagado)) pagP2 ".
            				"FROM sia_cuentasdetalles WHERE idCuenta='" . $p2 . "' GROUP BY capitulo ) d ".
      				"LEFT JOIN sia_capitulos c ON d.capitulo=c.idCapitulo  GROUP BY d.capitulo, c.nombre ".
      				"ORDER BY d.capitulo, c.nombre; ";
						
			//$db = new PDO('mysql:host='. $hostname . ';dbname='. $database . ';charset=utf8',$username, $password );			
			$db = new PDO("sqlsrv:Server={$hostname}; Database={$database}", $username, $password );
			$dbQuery = $db->prepare($sql);		
			//$dbQuery->execute(array(':cuenta' => $cuenta, ':pass' => $pass));
			$dbQuery->execute();
			$result = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
			echo "<tr><td></td><th>CORRIENTES</th><td colspan=13></td></tr>";
			foreach($result as $row) {
				echo "<tr><td>".$row['capitulo']."</td><td>".$row['nombre']."</td>";
				echo "<td>".$row['ejeP1']."</td>";
				echo "<td>".$row['oriP2']."</td>";
				echo "<td>".$row['modP2']."</td>";
				echo "<td>".$row['devP2']."</td>";
				echo "<td>".$row['ejeP2']."</td>";
				echo "<td>".$row['part']."</td>";
				echo "<td>".$row['pagP2']."</td>";
				echo "<td>".$row['varEje']."</td>";
				echo "<td>".$row['varEjePor']."</td>";
				echo "<td>".$row['varEjeOri']."</td>";
				echo "<td>".$row['varEjeOriPor']."</td>";
				echo "<td>".$row['varEjeMod']."</td>";
				echo "<td>".$row['varEjeModPor']."</td>";
				echo "</tr>";	
			}
			echo "<tr><td colspan=15></td></tr>";	
			
			$db = null;
			
			?>

			<tr><td></td><th>DE CAPITAL</th><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>			
			
			
		  </tbody>
		</table>
		<p class="pieReporte">
			n.a. No aplicable<br>
			Fuente: Informe de cuenta pública 2014 del Fideicomiso de Recuperación Crediticia del Distrito Federal (FIDERE III)
		</p>
		<br>
		<div class="col-md-4" style="padding:0px"></div>
		<div class="col-md-2" style="padding:0px">
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

