<!DOCTYPE html>

<?php
	require 'src/conexion.php';	
	$p1 = $_GET['param1'];
	$p2 = $_GET['param2'];
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
							EJERCICIOS <?php echo $p1 ?> Y <?php echo $p2 ?><br>
						</p>
					</div>												
		</div>
		<table class="table table-responsive table-striped table-bordered table-hover table-condensed">
		  <thead>
			<tr><th colspan=12>DIRECCIÓN GENERAL DE AUDITORÍA DE CUMPLIMIENTO FINANCIERO "A"</th></tr>
			<tr><th width="5%"></th><th colspan=3>EGRESOS <?php echo $p2 ?></th><th colspan=3>PROPORCIONES</th><th>EGRESOS <?php echo $p1 ?></th><th colspan=2>PROPORCIONES</th><th>EJE<?php echo $p2 ?>-EJE<?php echo $p1 ?></th><th>VAREJE<?php echo $p2 ?>-EJE<?php echo $p1 ?></th></tr>			
			<tr><th width="5%">PARTIDA</th><th>ORIGINAL</th><th>MOD.NETA</th><th>EJERCIDO</th><th>CAPITAL</th><th>TOTAL</th><th>VAR. RELATIVA</th><th>EJERCIDO</th><th>TOTAL</th><th>VAR. RELATIVA</th><th>VAR. EJE</th><th>VAR. RELATIVA</th></tr>
		  </thead>		  
		  <tbody>
			<tr><td COLSPAN=13></tr>			
			
			<?php
				$capitulo="";
				$nTotalRegistros=0;
			
				$sql = "SELECT  d.capitulo, d.partida, " . 
				"format(sum(d.egor)/1000, '$#,##0.00;($#,##0.00)') egor, format(sum(d.egej - d.egor)/1000, '$#,##0.00;($#,##0.00)') egmod, format(sum(d.egej)/1000, '$#,##0.00;($#,##0.00)') egej, format(sum(d.ejeg)/1000, '$#,##0.00;($#,##0.00)') ejeg, format(sum(d.egej - d.ejeg)/1000, '$#,##0.00;($#,##0.00)') vareje,  format(case when sum(d.ejeg)= 0 then 0 else(sum((d.egej - d.ejeg)*100))/sum(d.ejeg) end, '#0.00;(#0.00)') varela " .
				"FROM( " .
				"	SELECT  capitulo, partida, '0' egor , '0' egej, sum(CONVERT(DECIMAL(20,2), ejercido)) ejeg FROM sia_cuentasdetalles WHERE idCuenta='" . $p1 . "' GROUP BY capitulo, partida " .
				"UNION  ALL " .
				"	SELECT capitulo, partida, sum(CONVERT(DECIMAL(20,2), original)) egor , sum(CONVERT(DECIMAL(20,2), ejercido)) egej, '0' ejeg FROM sia_cuentasdetalles WHERE idCuenta='" . $p2 . "' GROUP BY capitulo, partida " . 
				") d " . 
				"left join sia_capitulos c on d.capitulo=c.idCapitulo " .
				"left join sia_partidas p on d.capitulo=p.idCapitulo and d.partida=p.idPartida " . 
				"GROUP BY d.capitulo, d.partida ORDER BY d.capitulo, d.partida;";
				
							
				//$db = new PDO('mysql:host='. $hostname . ';dbname='. $database . ';charset=utf8',$username, $password );			
				//SQL Server
				$db = new PDO("sqlsrv:Server={$hostname}; Database={$database}", $username, $password );			
				$dbQuery = $db->prepare($sql);		
				$dbQuery->execute();
				$result = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
	



	
				foreach($result as $row) {
					if($capitulo!="" && $capitulo!=$row['capitulo']){					
						echo "<tr><td></td><th>SUMA DEL CAPÍTULO " . $capitulo ."</th><td colspan=11></td></tr>";
					}
					echo "<tr><td>".$row['partida']."</td>";
					echo "<td>".$row['egor']."</td>";
					echo "<td>".$row['egmod']."</td>";
					echo "<td>".$row['egej']."</td>";
					echo "<td>100%</td>";
					echo "<td>100%</td>";
					echo "<td>100%</td>";
					echo "<td>".$row['ejeg']."</td>";
					echo "<td>100%</td>";
					echo "<td>100%</td>";
					echo "<td>".$row['vareje']."</td>";
					echo "<td>".$row['varela']."</td>";
					// echo "<td>".$row['varEjeMod']."</td><td></td>";
					echo "</tr>";
					$capitulo=$row['capitulo'];
					
				}
				echo "<tr><td colspan=13></td></tr>";				
				//echo "<hr> SQL:<br>" . $sql . "<hr>";
				
				$db = null;
				
				
					
			?>

			<tr><td><th>TOTAL</th></td><th></th><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>			
			
			
		 </tbody>
		</table>
		<p class="pieReporte">
			* Sin asignación original  **No aplica ejercido<br>
			Fuente: Informe de cuenta pública <?php echo $p1 ?> y <?php echo $p2 ?>.
		</p>
		<br>
		<div class="col-md-4" style="padding:0px"></div>
		<div class="col-md-2" style="padding:0px">
			<p id="divBotones">
				<button  onclick="javascript:window.close();" class="btn btn-default btn-xs"></span> Cerrar</button>
				<button  class="btn btn-default btn-xs" onclick="imprimir();">Imprimir</button>
			</p>			
		</div>
		<div class="col-md-4" style="padding:0px"></div>			

	</div>	

	<div class="col-md-4" style="padding:0px">
	
	</div>
  
  
</body>
</html>

