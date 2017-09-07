<!DOCTYPE html>

<?php
	require 'src/conexion.php';	
	$p1 = $_GET['param1'];
	$p2 = $_GET['param2'];
	$titulo = $_GET['titulo'];
	?>
	
<html lang="en"><head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=Edge" /> 
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
							DIRECCIÓN GENERAL DE AUDITORÍA DE CUMPLIMIENTO FINANCIERO "B"<br>
							<strong><?php echo $titulo ?></strong><br>
							(Cantidades en Miles de Pesos)<br><br>						
							EJERCICIOS <?php echo $p1 ?> Y <?php echo $p2 ?><br>
						</p>
					</div>												
		</div>
		<table class="table table-responsive table-striped table-bordered table-hover table-condensed">
		  <thead>
			<tr><th colspan=20>DIRECCIÓN GENERAL DE AUDITORÍA DE CUMPLIMIENTO FINANCIERO "B"</th></tr>
			<tr><th width="5%"></th><th colspan=5>EGRESOS <?php echo $p2 ?></th><th colspan=3>PROPORCIONES</th><th colspan=5>EGRESOS <?php echo $p1 ?></th><th colspan=2>PROPORCIONES</th><th>ORI <?php echo $p2 . '-'. $p1 ?></th><th>(VARORI*100/ ORI <?php echo $p2 ?></th><th>VAREJE<?php echo $p2 . '-' . $p1 ?></th><th>(VARORI*100/ ORI <?php echo $p2 ?></th></tr>			
			<tr><th width="5%">PARTIDA</th><th>ORIGINAL</th><th>MOD.NETA</th><th>MODIFICADO</th><th>EJERCIDO</th><th>ECONOMIA</th><th>CAPITAL</th><th>TOTAL</th><th>VAR. RELATIVA</th><th>ORIGINAL</th><th>MOD.NETA</th><th>MODIFICADO</th><th>EJERCIDO</th><th>ECONOMIA</th><th>TOTAL</th><th>VAR. RELATIVA</th><th>VAR.ORI</th><th>VAR.RELA</th><th>VAR.EJE</th><th>VAR.RELA</th></tr>
		  </thead>		  
		  <tbody>
			<?php
				$capitulo="";
				$nTotalRegistros=0;
				$CapOrgAct = 0;
				$CapNetAct = 0;
				$CapModAct = 0;
				$CapEjeAct = 0;
				$CapEcoAct = 0;
				$CapVarCop = 0;
				$CapOrgAnt = 0;
				$CapNetAnt = 0;
				$CapModAnt = 0;
				$CapEjeAnt = 0;
				$CapEcoAnt = 0;
				$CapVarAnt = 0;
				$CapVarOri = 0;
				$CapVarRel = 0;
				$CapVarEje = 0;
				$CapVarRela = 0;
				$SSUOrgAct = 0;
 	 			$SSUNetAct = 0;
 	 			$SSUModAct = 0;
 	 			$SSUEjeAct = 0;
 	 			$SSUEcoAct = 0;
 	 			$SSUVarCop = 0;
 	 			$SSUOrgAnt = 0;
 	 			$SSUNetAnt = 0;
 	 			$SSUModAnt = 0;
 	 			$SSUEjeAnt = 0;
 	 			$SSUEcoAnt = 0;
 	 			$SSUVarAnt = 0;
 	 			$SSUVarOri = 0;
 	 			$SSUVarRel = 0;
 	 			$SSUVarEje = 0;
 	 			$SSUVarRela = 0;

								///ESta es por Capitulo
			 	 $sql = "  SELECT  un.nombre nom, d.capitulo,     
						  format(sum(d.orgAct)/1000, '$#,##0.00;($#,##0.00)') orgAct, 
						  format(sum(d.ejeAct - d.orgAct)/1000, '$#,##0.00;($#,##0.00)') netAct, 
						  format(sum(d.modAct)/1000, '$#,##0.00;($#,##0.00)') modAct, 
						  format(sum(d.ejeAct)/1000, '$#,##0.00;($#,##0.00)') ejeAct, 
						  format(sum(d.ejeAct - d.modAct)/1000, '#,##0.00;(#,##0.00)') ecoAct, 
						  format(case when sum(d.orgAct)= 0 then 0 else(sum(d.ejeAct - d.orgAct)/sum(d.orgAct))*100 end, '#,##0.00;(#,##0.00)') VarCop,
						  format(sum(d.orgAnt)/1000, '$#,##0.00;($#,##0.00)') orgAnt,
						  format(sum(d.ejeAnt - d.orgAnt)/1000, '$#,##0.00;($#,##0.00)') netAnt, 
						  format(sum(d.modAnt)/1000, '$#,##0.00;($#,##0.00)') modAnt, 
						  format(sum(d.ejeAnt)/1000, '$#,##0.00;($#,##0.00)') ejeAnt, 
						  format(sum(d.ejeAnt - d.modAnt)/1000, '##0.00;(##0.00)') ecoAnt, 
						  format(case when sum(d.orgAnt)= 0 then 0 else(sum(d.ejeAnt - d.orgAnt)/sum(d.orgAnt))*100 end, '#,##0.00;(#,##0.00)') VarAnt,
						  format(sum(d.orgAct - d.orgAnt), '$#,##0.00;($#,##0.00)') varori, 
						  format(case when sum(d.orgAnt)= 0 then 0 else(sum((d.orgAct - d.orgAnt)*100))/sum(d.orgAnt) end, '#0.00;(#0.00)') varrel, 
						  format(sum(d.ejeAct - d.ejeAnt), '$#,##0.00;($#,##0.00)') vareje,  
						  format(case when sum(d.ejeAnt)= 0 then 0 else(sum((d.ejeAct - d.ejeAnt)*100))/sum(d.ejeAnt) end, '#0.00;(#0.00)') varrela
							FROM(
							SELECT  sector, subsector, unidad,capitulo,'0' orgAct,'0' modAct, '0' ejeAct, sum(CONVERT(DECIMAL(20,2), original)) orgAnt, sum(CONVERT(DECIMAL(20,2), modificado)) modAnt, sum(CONVERT(DECIMAL(20,2), ejercido)) ejeAnt FROM sia_cuentasdetalles WHERE idCuenta='CTA-2014' GROUP BY capitulo,  sector, subsector, unidad
							UNION  ALL
							SELECT sector, subsector, unidad,capitulo, sum(CONVERT(DECIMAL(20,2), original)) orgAct, sum(CONVERT(DECIMAL(20,2), modificado)) modAct, sum(CONVERT(DECIMAL(20,2), ejercido)) ejeAct,'0' orgAnt,'0' modAnt, '0' ejeAnt FROM sia_cuentasdetalles WHERE idCuenta='CTA-2015' GROUP BY capitulo,  sector, subsector, unidad
						  ) d
						  INNER JOIN sia_unidades un on d.sector=un.idSector and d.subsector=un.idSubsector and d.unidad=un.idUnidad 
							GROUP BY d.capitulo, un.nombre ORDER BY un.nombre;";

  				$db2 = new PDO("sqlsrv:Server={$hostname}; Database={$database}", $username, $password );			
	 	 		$dbQuery2 = $db2->prepare($sql);		
	 	 		$dbQuery2->execute();
	 	 		$res2= $dbQuery2->fetchAll(PDO::FETCH_ASSOC);
	 	 		foreach ($res2 as $row2) {
	 	 			$CapOrgAct = $row2['orgAct'];
	 	 			$CapNetAct = $row2['netAct'];
	 	 			$CapModAct = $row2['modAct'];
	 	 			$CapEjeAct = $row2['ejeAct'];
	 	 			$CapEcoAct = $row2['ecoAct'];
	 	 			$CapVarCop = $row2['VarCop'];
	 	 			$CapOrgAnt = $row2['orgAnt'];
	 	 			$CapNetAnt = $row2['netAnt'];
	 	 			$CapModAnt = $row2['modAnt'];
	 	 			$CapEjeAnt = $row2['ejeAnt'];
	 	 			$CapEcoAnt = $row2['ecoAnt'];
	 	 			$CapVarAnt = $row2['VarAnt'];
	 	 			$CapVarOri = $row2['varori'];
	 	 			$CapVarRel = $row2['varrel'];
	 	 			$CapVarEje = $row2['vareje'];
	 	 			$CapVarRela = $row2['varrela'];

	 	 		}
		 	 	


			 	 	///ESta es por sector,subsector y unidad
			 	 $sql = " SELECT  CONCAT(d.sector,d.subsector,d.unidad) concatenado, un.nombre nom,     
						  format(sum(d.orgAct)/1000, '$#,##0.00;($#,##0.00)') orgAct, 
						  format(sum(d.ejeAct - d.orgAct)/1000, '$#,##0.00;($#,##0.00)') netAct, 
						  format(sum(d.modAct)/1000, '$#,##0.00;($#,##0.00)') modAct, 
						  format(sum(d.ejeAct)/1000, '$#,##0.00;($#,##0.00)') ejeAct, 
						  format(sum(d.ejeAct - d.modAct)/1000, '#,##0.00;(#,##0.00)') ecoAct, 
						  format(case when sum(d.orgAct)= 0 then 0 else(sum(d.ejeAct - d.orgAct)/sum(d.orgAct))*100 end, '#,##0.00;(#,##0.00)') VarCop,
						  format(sum(d.orgAnt)/1000, '$#,##0.00;($#,##0.00)') orgAnt,
						  format(sum(d.ejeAnt - d.orgAnt)/1000, '$#,##0.00;($#,##0.00)') netAnt, 
						  format(sum(d.modAnt)/1000, '$#,##0.00;($#,##0.00)') modAnt, 
						  format(sum(d.ejeAnt)/1000, '$#,##0.00;($#,##0.00)') ejeAnt, 
						  format(sum(d.ejeAnt - d.modAnt)/1000, '##0.00;(##0.00)') ecoAnt, 
						  format(case when sum(d.orgAnt)= 0 then 0 else(sum(d.ejeAnt - d.orgAnt)/sum(d.orgAnt))*100 end, '#,##0.00;(#,##0.00)') VarAnt,
						  format(sum(d.orgAct - d.orgAnt), '$#,##0.00;($#,##0.00)') varori, 
						  format(case when sum(d.orgAnt)= 0 then 0 else(sum((d.orgAct - d.orgAnt)*100))/sum(d.orgAnt) end, '#0.00;(#0.00)') varrel, 
						  format(sum(d.ejeAct - d.ejeAnt), '$#,##0.00;($#,##0.00)') vareje,  
						  format(case when sum(d.ejeAnt)= 0 then 0 else(sum((d.ejeAct - d.ejeAnt)*100))/sum(d.ejeAnt) end, '#0.00;(#0.00)') varrela
							FROM(
							SELECT  sector, subsector, unidad,'0' orgAct,'0' modAct, '0' ejeAct, sum(CONVERT(DECIMAL(20,2), original)) orgAnt, sum(CONVERT(DECIMAL(20,2), modificado)) modAnt, sum(CONVERT(DECIMAL(20,2), ejercido)) ejeAnt FROM sia_cuentasdetalles WHERE idCuenta='CTA-2014' GROUP BY sector, subsector, unidad
							UNION  ALL
							SELECT sector, subsector, unidad, sum(CONVERT(DECIMAL(20,2), original)) orgAct, sum(CONVERT(DECIMAL(20,2), modificado)) modAct, sum(CONVERT(DECIMAL(20,2), ejercido)) ejeAct,'0' orgAnt,'0' modAnt, '0' ejeAnt FROM sia_cuentasdetalles WHERE idCuenta='CTA-2015' GROUP BY sector, subsector, unidad
						  ) d
						  INNER JOIN sia_unidades un on d.sector=un.idSector and d.subsector=un.idSubsector and d.unidad=un.idUnidad 
							GROUP BY un.nombre,d.sector, d.subsector,d.unidad ORDER BY un.nombre;";

  				$db3 = new PDO("sqlsrv:Server={$hostname}; Database={$database}", $username, $password );			
	 	 		$dbQuery3 = $db3->prepare($sql);		
	 	 		$dbQuery3->execute();
	 	 		$res3= $dbQuery3->fetchAll(PDO::FETCH_ASSOC);
	 	 		
	 	 		foreach ($res3 as $row3) {
	 	 			$SSUOrgAct = $row3['orgAct'];
	 	 			$SSUNetAct = $row3['netAct'];
	 	 			$SSUModAct = $row3['modAct'];
	 	 			$SSUEjeAct = $row3['ejeAct'];
	 	 			$SSUEcoAct = $row3['ecoAct'];
	 	 			$SSUVarCop = $row3['VarCop'];
	 	 			$SSUOrgAnt = $row3['orgAnt'];
	 	 			$SSUNetAnt = $row3['netAnt'];
	 	 			$SSUModAnt = $row3['modAnt'];
	 	 			$SSUEjeAnt = $row3['ejeAnt'];
	 	 			$SSUEcoAnt = $row3['ecoAnt'];
	 	 			$SSUVarAnt = $row3['VarAnt'];
	 	 			$SSUVarOri = $row3['varori'];
	 	 			$SSUVarRel = $row3['varrel'];
	 	 			$SSUVarEje = $row3['vareje'];
	 	 			$SSUVarRela = $row3['varrela'];

			 	}



					/// Los datos principales
				$sql = "SELECT  CONCAT(d.sector,d.subsector,d.unidad) concatenado, un.nombre nom, d.capitulo, d.partida, " . 
				"format(sum(d.orgAct)/1000, '$#,##0.00;($#,##0.00)') orgAct, format(sum(d.ejeAct - d.orgAct)/1000, '$#,##0.00;($#,##0.00)') netAct, format(sum(d.modAct)/1000, '$#,##0.00;($#,##0.00)') modAct, format(sum(d.ejeAct)/1000, '$#,##0.00;($#,##0.00)') ejeAct, format(sum(d.ejeAct - d.modAct)/1000, '$#,##0.00;($#,##0.00)') ecoAct, format(case when sum(d.orgAct)= 0 then 0 else(sum(d.ejeAct - d.orgAct)/sum(d.orgAct))*100 end, '#,##0.00;(#,##0.00)') VarCop,format(sum(d.orgAnt)/1000, '$#,##0.00;($#,##0.00)') orgAnt, format(sum(d.ejeAnt - d.orgAnt)/1000, '$#,##0.00;($#,##0.00)') netAnt, format(sum(d.modAnt)/1000, '$#,##0.00;($#,##0.00)') modAnt, format(sum(d.ejeAnt)/1000, '$#,##0.00;($#,##0.00)') ejeAnt, format(sum(d.ejeAnt - d.modAnt)/1000, '$##0.00;($##0.00)') ecoAnt, format(case when sum(d.orgAnt)= 0 then 0 else(sum(d.ejeAnt - d.orgAnt)/sum(d.orgAnt))*100 end, '#,##0.00;(#,##0.00)') VarAnt, format(sum(d.orgAct - d.orgAnt), '$#,##0.00;($#,##0.00)') varori, format(case when sum(d.orgAnt)= 0 then 0 else(sum((d.orgAct - d.orgAnt)*100))/sum(d.orgAnt) end, '#0.00;(#0.00)') varrel, format(sum(d.ejeAct - d.ejeAnt), '$#,##0.00;($#,##0.00)') vareje,  format(case when sum(d.ejeAnt)= 0 then 0 else(sum((d.ejeAct - d.ejeAnt)*100))/sum(d.ejeAnt) end, '#0.00;(#0.00)') varrela " .
				"FROM( " .
				"SELECT  sector, subsector, unidad,capitulo, partida,'0' orgAct,'0' modAct, '0' ejeAct, sum(CONVERT(DECIMAL(20,2), original)) orgAnt, sum(CONVERT(DECIMAL(20,2), modificado)) modAnt, sum(CONVERT(DECIMAL(20,2), ejercido)) ejeAnt FROM sia_cuentasdetalles WHERE idCuenta='" . $p1 . "' GROUP BY capitulo, partida, sector, subsector, unidad " .
				"UNION  ALL " .
				"SELECT sector, subsector, unidad,capitulo, partida, sum(CONVERT(DECIMAL(20,2), original)) orgAct, sum(CONVERT(DECIMAL(20,2), modificado)) modAct, sum(CONVERT(DECIMAL(20,2), ejercido)) ejeAct,'0' orgAnt,'0' modAnt, '0' ejeAnt FROM sia_cuentasdetalles WHERE idCuenta='" . $p2 . "' GROUP BY capitulo, partida, sector, subsector, unidad " . 
				") d " . 
				"INNER JOIN sia_unidades un on d.sector=un.idSector and d.subsector=un.idSubsector and d.unidad=un.idUnidad " .
				"left join sia_capitulos c on d.capitulo=c.idCapitulo " .
				"left join sia_partidas p on d.capitulo=p.idCapitulo and d.partida=p.idPartida " . 
				"GROUP BY d.capitulo, d.partida,un.nombre,d.sector, d.subsector,d.unidad ORDER BY un.nombre,d.capitulo, d.partida;";


				$db = new PDO("sqlsrv:Server={$hostname}; Database={$database}", $username, $password );			
				$dbQuery = $db->prepare($sql);		
				$dbQuery->execute();
				$result = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
					
				$nPagina=1;
			 	foreach($result as $row) {
			 	 if($capitulo!="" && $capitulo!=$row['capitulo']){					
				 echo "<tr><th>" . $row['concatenado']."</th><td colspan=18>".$row['nom']."</td></td></tr>";
				
			 	 }
							
			 		echo "<tr><td>".$row['partida']."</td>";
			 		echo "<td>".$row['orgAct']."</td>";
			 		echo "<td>".$row['netAct']."</td>";
			 		echo "<td>".$row['modAct']."</td>";
			 		echo "<td>".$row['ejeAct']."</td>";
			 		echo "<td>".$row['ecoAct']."</td>";
			 		echo "<td>100%</td>";
			 		echo "<td>100%</td>";
			 		echo "<td>".$row['VarCop']."</td>";
			 		echo "<td>".$row['orgAnt']."</td>";
			 		echo "<td>".$row['netAnt']."</td>";
			 		echo "<td>".$row['modAnt']."</td>";
			 		echo "<td>".$row['ejeAnt']."</td>";
			 		echo "<td>".$row['ecoAnt']."</td>";
			 		echo "<td>100%</td>";
			 		echo "<td>".$row['VarAnt']."</td>";
			 		echo "<td>".$row['varori']."</td>";
			 		echo "<td>".$row['varrel']."</td>";
			 		echo "<td>".$row['vareje']."</td>";
			 		echo "<td>".$row['varrela']."</td>";
			 		echo "</tr>";
			 		$capitulo=$row['capitulo'];
			 			//echo "<td>".$row['nom']."</td>";
			
			 	$nPagina++;
				}

			 	echo "<tr><td colspan=13></td></tr>";				





			 	//echo "<hr> SQL:<br>" . $sql . "<hr>";
				
				$db = null;
				
				
					
			?>
			
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
		<div class="col-md-4 style="padding:0px"></div>			

	</div>	

	<div class="col-md-4 style="padding:0px">
	
	</div>
  
  
</body>
</html>

