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
						<strong>INTEGRACIÓN DEL PRESUPUESTO POR PARTIDA PRESUPUESTAL</strong><br>
						<div class="pull-right" style="background:black;color:white; padding:5px; border:1px solid gray;">ANEXO 3</div>
						<br>(Miles de Pesos)<br>
						</p>
					</div>												
		</div>
		<table class="table table-responsive table-striped table-bordered table-hover table-condensed">
		  <thead>
			<tr><th rowspan=3 style="vertical-align:middle" Width="5%">P.P</th><th rowspan=3  style="vertical-align:middle"  Width="30%">CONCEPTO</th><th><?php echo $p1 ?></th><th colspan=5><?php echo $p2 ?></th><th colspan=7>Variaciones</th></tr>
			<tr><th rowspan=2>EJERCIDO<br>(1)</th><th rowspan=2>ORIGINAL<br>(2)</th><th rowspan=2>MODIFICADO<br>(3)</th><th rowspan=2>EJERCIDO<br>(4)</th><th rowspan=2>PAGADO<br>(5)</th><th rowspan=2>% part</th><th colspan=2>EJERCIDO <?php echo $p1 . " - " . $p2 ?></th><th colspan=2>EJERCIDO ORIGINAL</th><th colspan=2>EJERCIDO MODIFICADO</th><th colspan=1>PAG-EJE</th></tr>
			<tr><th>5 = (4) - (1)</th><th>%</th><th>6 = (4) - (2)</th><th>%</th><th>7 = (4) - (3)</th><th>%</th><th>9=(5)-(4)</th></tr>
		  </thead>		  
		  <tbody>
			<tr><td COLSPAN=15></tr>			
			
			<?php
			$capitulo="";
			$nTotalRegistros=0;
			$totEje14 = 0;
			$totOri15 = 0;
			$totMod15 = 0;
			$totEje15 = 0;
			$totPag15 = 0;
			$totVarEje = 0;
			$totVarEjePor = 0;
			$totVarEjeOri = 0;
			$totVarEjeOriPor = 0;
			$totVarEjeMod = 0;
			$totVarEjeModPor = 0;
			$totVarEjePag = 0;
			$totVarEjePagPor = 0;


			// Incia obteniendo los totales globales de capitulos/partidas de los periodos (p1 y p2) ya que se necesitaran para cálculos posteriores.
			$sql = "".
			" SELECT format(x.eje14, '$#,##0.00;($#,##0.00)') eje14
				, format(x.ori15, '$#,##0.00;($#,##0.00)') ori15
				, format(x.mod15, '$#,##0.00;($#,##0.00)') mod15
				, format(x.eje15, '$#,##0.00;($#,##0.00)') eje15
				, format(x.pag15, '$#,##0.00;($#,##0.00)') pag15 
			    , format(x.varEje, '$#,##0.00;($#,##0.00)') varEje
			    , format(x.varEje * 100 / x.eje14, '##0.0;(##0.0)') varEjePor 
				, format(x.varEjeOri, '$#,##0.00;($#,##0.00)') varEjeOri
				, format(x.varEjeOri * 100 / x.ori15, '##0.0;(##0.0)') varEjeOriPor
				, format(x.varEjeMod, '$#,##0.00;($#,##0.00)') varEjeMod
				, format(x.varEjeMod * 100 / x.mod15, '##0.0;(##0.0)') varEjeModPor 			
				, format(x.varEjePag, '$#,##0.00;($#,##0.00)') varEjePag
				, format(x.varEjePag * 100 / x.eje15, '##0.0;(##0.0)') varEjePagPor		 
			FROM ( 	SELECT sum(d.eje14)/1000 eje14 
				    , sum(d.ori15)/1000 ori15 
					, sum(d.mod15)/1000 mod15 
			      	, sum(d.eje15)/1000 eje15 
				    , sum(d.pag15)/1000 pag15 
				    , sum(d.eje15 - d.eje14)/1000 varEje  
				    , sum(d.eje15 - d.ori15)/1000 varEjeOri 
				    , sum(d.eje15 - d.mod15)/1000 varEjeMod 			
				    , sum(d.pag15 - d.eje15)/1000 varEjePag 		
			 		FROM(SELECT  capitulo, partida, sum(CONVERT(DECIMAL(20,2), ejercido)) eje14 , '0' ori15, '0' mod15, '0' eje15, '0' pag15  
			      		FROM sia_cuentasdetalles WHERE idCuenta='" . $p1 . "' GROUP BY capitulo, partida 
  						UNION  ALL 
						SELECT capitulo, partida, '0' eje14 
	    	    			, sum(CONVERT(DECIMAL(20,2), original)) ori15 
							, sum(CONVERT(DECIMAL(20,2), modificado)) mod15 
							, sum(CONVERT(DECIMAL(20,2), ejercido)) eje15 
							, sum(CONVERT(DECIMAL(20,2), pagado)) pag15 
			      		FROM sia_cuentasdetalles WHERE idCuenta='" . $p2 . "' GROUP BY capitulo, partida 
				) d  
			 LEFT JOIN sia_capitulos c on d.capitulo=c.idCapitulo 
			 LEFT JOIN sia_partidas p on d.capitulo=p.idCapitulo and d.partida=p.idPartida) x; ";
			
			$db = new PDO("sqlsrv:Server={$hostname}; Database={$database}", $username, $password );
			$dbQuery = $db->prepare($sql);		
			$dbQuery->execute();
			$result = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
			foreach($result as $row) {
					$totEje14 = $row['eje14'];
					$totOri15 = $row['ori15'];
					$totMod15 = $row['mod15'];
					$totEje15 = $row['eje15'];
					$totPag15 = $row['pag15'];
					$totVarEje = $row['varEje'];
					$totVarEjePor = $row['varEjePor'];
					$totVarEjeOri = $row['varEjeOri'];
					$totVarEjeOriPor = $row['varEjeOriPor'];
					$totVarEjeMod = $row['varEjeMod'];
					$totVarEjeModPor = $row['varEjeModPor'];
					$totVarEjePag = $row['varEjePag'];
					$totVarEjePagPor = $row['varEjePagPor'];

			}
			//echo "<tr><td colspan=15></td></tr>";				

			// Inicia ciclo por capitulo obteniendo sus totales donde se suman todas sus partidas.

			$sql = "".
			"SELECT x.capitulo, x.sCapitulo  
				, format(x.eje14, '$#,##0.00;($#,##0.00)') eje14
				, format(x.ori15, '$#,##0.00;($#,##0.00)') ori15
				, format(x.mod15, '$#,##0.00;($#,##0.00)') mod15
				, format(x.eje15, '$#,##0.00;($#,##0.00)') eje15
				, format(x.pag15, '$#,##0.00;($#,##0.00)') pag15 
				, format(x.varEje, '$#,##0.00;($#,##0.00)') varEje   
				, format( CASE WHEN x.eje14=0 THEN 0 ELSE (varEje * 100 / x.eje14) END, '##0.0;(##0.0)') varEjePor 
				, format(x.varEjeOri, '$#,##0.00;($#,##0.00)') varEjeOri
				, format( CASE WHEN x.ori15=0 THEN 0 ELSE (x.varEjeOri * 100 / x.ori15) END, '##0.0;(##0.0)') varEjeOriPor 
				, format(x.varEjeMod, '$#,##0.00;($#,##0.00)') varEjeMod
				, format( CASE WHEN x.mod15=0 THEN 0 ELSE (x.varEjeMod * 100 / x.mod15) END, '##0.0;(##0.0)') varEjeModPor 
				, format(x.varEjePag, '$#,##0.00;($#,##0.00)') varEjePag
				, format( CASE WHEN x.eje15=0 THEN 0 ELSE (x.varEjePag * 100 / x.eje15) END, '##0.0;(##0.0)') varEjePagPor	
			FROM ( SELECT d.capitulo, c.nombre sCapitulo  
						, sum(d.eje14)/1000 eje14 
						, sum(d.ori15)/1000 ori15 
						, sum(d.mod15)/1000 mod15 
						, sum(d.eje15)/1000 eje15 
						, sum(d.pag15)/1000 pag15 
						, sum(d.eje15 - d.eje14)/1000 varEje  
						, sum(d.eje15 - d.ori15)/1000 varEjeOri 
		        		, sum(d.eje15 - d.mod15)/1000 varEjeMod 			
						, sum(d.pag15 - d.eje15)/1000 varEjePag 			
					FROM( SELECT  capitulo, partida, sum(CONVERT(DECIMAL(20,2), ejercido)) eje14 , '0' ori15, '0' mod15, '0' eje15, '0' pag15   
		          	--FROM sia_cuentasdetalles WHERE idCuenta='CTA-2014' GROUP BY capitulo, partida  
		          	FROM sia_cuentasdetalles WHERE idCuenta='". $p1."' GROUP BY capitulo, partida  
			        UNION  ALL  
		            SELECT capitulo, partida, '0' eje14  
      					, sum(CONVERT(DECIMAL(20,2), original)) ori15  
      					, sum(CONVERT(DECIMAL(20,2), modificado)) mod15  
      					, sum(CONVERT(DECIMAL(20,2), ejercido)) eje15  
      					, sum(CONVERT(DECIMAL(20,2), pagado)) pag15  
  					--FROM sia_cuentasdetalles WHERE idCuenta='CTA-2015' GROUP BY capitulo, partida  
  					FROM sia_cuentasdetalles WHERE idCuenta='". $p2 ."' GROUP BY capitulo, partida  
  					) d   
				LEFT JOIN sia_capitulos c on d.capitulo=c.idCapitulo  
				LEFT JOIN sia_partidas p on d.capitulo=p.idCapitulo and d.partida=p.idPartida
				GROUP BY  d.capitulo, c.nombre
				) x
			ORDER BY x.capitulo, x.sCapitulo; ";

			$db1 = new PDO("sqlsrv:Server={$hostname}; Database={$database}", $username, $password );
			$dbQuery1 = $db1->prepare($sql);		
			$dbQuery1->execute();
			$result1 = $dbQuery1->fetchAll(PDO::FETCH_ASSOC);

			foreach($result1 as $row1) {
				$capitulo =  $row1['capitulo'];
				$nombreCap =  $row1['sCapitulo'];
				$capEje14 = $row1['eje14'];
				$capOri15 = $row1['ori15'];
				$capMod15 = $row1['mod15'];
				$capEje15 = $row1['eje15'];
				$capPag15 = $row1['pag15'];
				$capVarEje = $row1['varEje'];
				$capVarEjePor = $row1['varEjePor'];
				$capVarEjeOri = $row1['varEjeOri'];
				$capVarEjeOriPor = $row1['varEjeOriPor'];
				$capVarEjeMod = $row1['varEjeMod'];
				$capVarEjeModPor = $row1['varEjeModPor'];
				$capVarEjePag = $row1['varEjePag'];
				$capVarEjePagPor = $row1['varEjePagPor'];

				$sql = "" .
				"SELECT x.capitulo, x.sCapitulo, x.partida, x.sPartida   
					  , format(x.eje14, '$#,##0.00;($#,##0.00)') eje14
					  , format(x.ori15, '$#,##0.00;($#,##0.00)') ori15 
					  , format(x.mod15, '$#,##0.00;($#,##0.00)') mod15
					  , format(x.eje15, '$#,##0.00;($#,##0.00)') eje15
					  , format(x.pag15, '$#,##0.00;($#,##0.00)') pag15
					  , format(x.varEje, '$#,##0.00;($#,##0.00)') varEje
					  , format( CASE WHEN x.eje14=0 THEN 0 ELSE (varEje * 100 / x.eje14) END, '##0.0;(##0.0)') varEjePor 
					  , format(x.varEjeOri, '$#,##0.00;($#,##0.00)') varEjeOri
					  , format( CASE WHEN x.ori15=0 THEN 0 ELSE (x.varEjeOri * 100 / x.ori15) END, '##0.0;(##0.0)') varEjeOriPor 
					  , format(x.varEjeMod, '$#,##0.00;($#,##0.00)') varEjeMod
					  , format( CASE WHEN x.mod15=0 THEN 0 ELSE (x.varEjeMod * 100 / x.mod15) END, '##0.0;(##0.0)') varEjeModPor 
					  , format(x.varEjePag, '$#,##0.00;($#,##0.00)') varEjePag
					  , format( CASE WHEN x.eje15=0 THEN 0 ELSE (x.varEjePag * 100 / x.eje15) END, '##0.0;(##0.0)') varEjePagPor	
					FROM ( SELECT d.capitulo, c.nombre sCapitulo, d.partida, p.nombre sPartida 
					        , sum(d.eje14)/1000 eje14 
					        , sum(d.ori15)/1000 ori15 
					        , sum(d.mod15)/1000 mod15 
					        , sum(d.eje15)/1000 eje15 
					        , sum(d.pag15)/1000 pag15 
					        , sum(d.eje15 - d.eje14)/1000 varEje  
					        , sum(d.eje15 - d.ori15)/1000 varEjeOri 
					        , sum(d.eje15 - d.mod15)/1000 varEjeMod 			
					        , sum(d.pag15 - d.eje15)/1000 varEjePag 			
					      FROM( SELECT  capitulo, partida, sum(CONVERT(DECIMAL(20,2), ejercido)) eje14 , '0' ori15, '0' mod15, '0' eje15, '0' 	pag15   
						        FROM sia_cuentasdetalles WHERE idCuenta='". $p1."' AND capitulo = '". $capitulo ."' GROUP BY capitulo, partida  
					            UNION  ALL  
					            SELECT capitulo, partida, '0' eje14  
					              , sum(CONVERT(DECIMAL(20,2), original)) ori15  
					              , sum(CONVERT(DECIMAL(20,2), modificado)) mod15  
					              , sum(CONVERT(DECIMAL(20,2), ejercido)) eje15  
					              , sum(CONVERT(DECIMAL(20,2), pagado)) pag15  
					          	FROM sia_cuentasdetalles WHERE idCuenta='". $p2."' AND capitulo = '". $capitulo ."' GROUP BY capitulo, partida  
					          ) d   
					    LEFT JOIN sia_capitulos c on d.capitulo=c.idCapitulo  
					    LEFT JOIN sia_partidas p on d.capitulo=p.idCapitulo and d.partida=p.idPartida
					    GROUP BY d.capitulo, c.nombre, d.partida, p.nombre 
					    ) x
					ORDER BY x.capitulo, x.sCapitulo, x.partida, x.sPartida; ";

					$db2 = new PDO("sqlsrv:Server={$hostname}; Database={$database}", $username, $password );
					$dbQuery2 = $db2->prepare($sql);		
					$dbQuery2->execute();
					$result_det = $dbQuery2->fetchAll(PDO::FETCH_ASSOC);

					foreach($result_det as $row_det) {
						echo "<tr><td>".$row_det['partida']."</td><td>".$row_det['sPartida'] . "</td>";
						echo "<td>".$row_det['eje14']."</td>";
						echo "<td>".$row_det['ori15']."</td>";
						echo "<td>".$row_det['mod15']."</td>";
						echo "<td>".$row_det['eje15']."</td>";
						echo "<td>".$row_det['pag15']."</td>";
						if ($capPag15 < 1){
							echo "<td>0.00</td>";
						}else{	
							echo "<td>".($row_det['pag15']*100)/$capPag15 . "</td>";
						}
						//echo "<td>".($row_det['pag15']*100)/$capPag15 . "</td>";
						echo "<td>".$row_det['varEje']."</td>";
						echo "<td>".$row_det['varEjePor']."</td>";
						echo "<td>".$row_det['varEjeOri']."</td>";
						echo "<td>".$row_det['varEjeOriPor']."</td>";
						echo "<td>".$row_det['varEjeMod']."</td>";
						echo "<td>".$row_det['varEjeModPor']."</td>";
						echo "<td>".$row_det['varEjePag']."</td>";
						echo "<td>".$row_det['varEjePagPor']."</td>";
						echo "</tr>";
					}
					echo "<tr><td></td><th>SUMA DEL CAPÍTULO " . $capitulo ."</th>";
					echo "<th>".$capEje14."</th>";
					echo "<th>".$capOri15."</th>";
					echo "<th>".$capMod15."</th>";
					echo "<th>".$capEje15."</th>";
					echo "<th>".$capPag15."</th>";
					if( $totEje14 < 1 ){
						echo "<th>0.00</th>";
					}else{
						echo "<th>".($capPag15*100)/$totEje14."</th>";
					}
					//echo "<td>".($capPag15*100)/$totEje14."</td>";
					echo "<th>".$capVarEje."</th>";
					echo "<th>".$capVarEjePor."</th>";
					echo "<th>".$capVarEjeOri."</th>";
					echo "<th>".$capVarEjeOriPor."</th>";
					echo "<th>".$capVarEjeMod."</th>";
					echo "<th>".$capVarEjeModPor."</th>";
					echo "<th>".$capVarEjePag."</th>";
					echo "<th>".$capVarEjePagPor."</th>";
					echo "</tr>";

					echo "<tr><td colspan=15></td></tr>";				
					//echo "<hr> SQL:<br>" . $sql . "<hr>";
			}

			echo "<tr><td></td><th>TOTALES</th>";
			echo "<th>".$totEje14."</th>";
			echo "<th>".$totOri15."</th>";
			echo "<th>".$totMod15."</th>";
			echo "<th>".$totEje15."</th>";
			echo "<th>".$totPag15."</th>";
			echo "<th>100%</th>";
			echo "<th>".$totVarEje."</th>";
			echo "<th>".$totVarEjePor."</th>";
			echo "<th>".$totVarEjeOri."</th>";
			echo "<th>".$totVarEjeOriPor."</th>";
			echo "<th>".$totVarEjeMod."</th>";
			echo "<th>".$totVarEjeModPor."</th>";
			echo "<th>".$totVarEjePag."</th>";
			echo "<th>".$totVarEjePagPor."</th>";
			echo "</tr>";

			/*	
			$sql = "SELECT d.capitulo, c.nombre sCapitulo, d.partida, p.nombre sPartida, " . 
			"format(sum(d.eje14)/1000, '$#,##0.00;($#,##0.00)') eje14, format(sum(d.ori15)/1000, '$#,##0.00;($#,##0.00)') ori15, " .
			"format(sum(d.mod15)/1000, '$#,##0.00;($#,##0.00)') mod15, format(sum(d.eje15)/1000, '$#,##0.00;($#,##0.00)') eje15, " . 
			"format(sum(d.eje15 - d.eje14)/1000, '$#,##0.00;($#,##0.00)') varEje, format(sum(d.eje15 - d.ori15)/1000, '$#,##0.00;($#,##0.00)') varEjeOri, format(sum(d.eje15 - d.mod15)/1000, '$#,##0.00;($#,##0.00)') varEjeMod " .			
			"FROM( " .
			"	SELECT  capitulo, partida, sum(CONVERT(DECIMAL(20,2), ejercido)) eje14 , '0' ori15, '0' mod15, '0' eje15  FROM sia_cuentasdetalles WHERE idCuenta='" . $p1 . "' GROUP BY capitulo, partida " .
			"UNION  ALL " .
			"	SELECT capitulo, partida, '0' eje14 , sum(CONVERT(DECIMAL(20,2), original)) ori15, sum(CONVERT(DECIMAL(20,2), modificado)) mod15, sum(CONVERT(DECIMAL(20,2), ejercido)) eje15 FROM sia_cuentasdetalles WHERE idCuenta='" . $p2 . "' GROUP BY capitulo, partida " . 
			") d " . 
			"left join sia_capitulos c on d.capitulo=c.idCapitulo	 " .
			"left join sia_partidas p on d.capitulo=p.idCapitulo and d.partida=p.idPartida " . 
			"GROUP BY d.capitulo, c.nombre, d.partida, p.nombre ORDER BY d.capitulo, c.nombre, d.partida, p.nombre ;";
			
						
			//$db = new PDO('mysql:host='. $hostname . ';dbname='. $database . ';charset=utf8',$username, $password );			
			//SQL Server
			$db = new PDO("sqlsrv:Server={$hostname}; Database={$database}", $username, $password );			
			$dbQuery = $db->prepare($sql);		
			$dbQuery->execute();
			$result = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
			//echo "<tr><td></td><th>CORRIENTES</th><td colspan=11></td></tr>";
			foreach($result as $row) {
				if($capitulo!="" && $capitulo!=$row['capitulo']){					
					echo "<tr><td></td><th>SUMA DEL CAPÍTULO " . $capitulo ."</th><td colspan=11></td></tr>";
				}
				echo "<tr><td>".$row['partida']."</td><td>".$row['sPartida'] . "</td>";
				echo "<td>".$row['eje14']."</td>";
				echo "<td>".$row['ori15']."</td>";
				echo "<td>".$row['mod15']."</td>";
				echo "<td>".$row['eje15']."</td>";
				echo "<td>100%</td>";
				echo "<td>".$row['varEje']."</td><td></td>";
				echo "<td>".$row['varEjeOri']."</td><td></td>";
				echo "<td>".$row['varEjeMod']."</td><td></td>";
				echo "</tr>";
				$capitulo=$row['capitulo'];
			}
			echo "<tr><td colspan=15></td></tr>";				
			//echo "<hr> SQL:<br>" . $sql . "<hr>";
			*/
			$db = null;
			
			
			
			?>

			<!-- 
			<tr><td></td><th>TOTAL</th><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>			 
			-->
			
			
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

