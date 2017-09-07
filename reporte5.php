<!doctype html>

<html lang="es-MX">
<head>
  <meta charset="utf-8">
  <title>LISTA DE CIUDADANOS POR DELEGACION</title>
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
		@media screen and (min-width: 768px) {
			#mapa_content {width:100%; height:450px;}
			#modalFlotante .modal-dialog  {width:40%;}
		}
		
		body{
			background:transparent;
			text-align=center;
			font-family:Arial, Helvetica, sans-serif;
		}
		
	</style>

  <!--[if lt IE 9]>
  <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
  
  <script type="text/javascript"> 
	function imprimir(){
		document.all.btnImprimir.style.display='none';	
		document.all.btnRegresar.style.display='none';	
		window.print();
		document.all.btnImprimir.style.display='inline';
		//document.all.btnImprimir.style.text-align='center';
		
		document.all.btnRegresar.style.display='inline';	
		//document.all.btnRegresar.style.text-align='center';
	}  
  </script>
</head>
<body>
  <div class="col-md-6 style="padding:0px">
	<p style="text-align:center;">
		<img src="img/logo-top.png"><br>
		<strong>Organizaciòn sin ànimos de lucro.<br> Ignacio Conmonfort ·30, Del. Iztapalapa, D.F.</strong><br><br>
		TOTAL DE CIUDADANOS CAPTURADOS POR COORDINADOR
	</p>
		<?php	
			$p1 = $_GET['param1'];
		
			$nTotalRegistros=0;
			$sql = "SELECT d.nombre sDelegacion, c.nombre sColonia, concat(p.nombre, ' ', p.paterno, ' ', p.materno) operador, e.idSeccion,  e.idElector, concat(e.nombre, ' ', e.paterno, ' ', e.materno) ciudadano, e.genero  " .
			"FROM sce_electores e left join sce_personas p on e.referente=p.idPersona " .
			"INNER JOIN sce_delegaciones d ON e.idDelegacion=d.idDelegacion " .
			"INNER JOIN sce_colonias c ON e.colonia=c.idColonia " .
			"WHERE e.idDelegacion='" . $p1 . "' " .
			"ORDER By concat(p.nombre, ' ', p.paterno, ' ', p.materno), e.idSeccion, concat(e.nombre, ' ', e.paterno, ' ', e.materno) ";
			
							
			mysql_connect("localhost", "usrAudi", ".usraudiCota13.");
			mysql_select_db("ascm_sia");
			$rs = mysql_query($sql);
			
			//echo "<br>" . $sql . "<br>";

			echo "<table class='table table-striped table-bordered table-condensed'>";
			echo "<thead><tr><th>DELEGACIÓN</th><th>COLONIA</th><th>OPERADOR</th><th>SECCIÓN</th><th>CVE. ELECTOR</th><th>CIUDADANO</th><th>GENERO</th></thead><tbody>";
			while($row = mysql_fetch_array($rs)){
				echo "<tr>";
				echo "<td>".$row['sDelegacion']."</td>";
				echo "<td>".$row['sColonia']."</td>";
				echo "<td>".$row['operador']."</td>";
				echo "<td>".$row['idSeccion']."</td>";
				echo "<td>".$row['idElector']."</td>";
				echo "<td>".$row['ciudadano']."</td>";
				echo "<td>".$row['genero']."</td>";
				echo "</tr>";
				
				$nTotalRegistros = $nTotalRegistros + 1;
				
			}
			echo "<tr><td style='text-align:center' colspan=5><b>Total de Ciudadanos:". $nTotalRegistros . "</b></b></td></tr>".
			"</tbody></table>";
		?> 
		<br>
		<div class="col-md-4 style="padding:0px"></div>
		<div class="col-md-2 style="padding:0px">
			<p style="text-align:right; width:100%" style="display:inline" id="btnRegresar">
				<button  onclick="javascript:window.close();" class="btn btn-link btn-lg">
					<span class="glyphicon glyphicon-circle-arrow-left"></span> Cerrar
				</button>
			</p>			
		</div>
		<div class="col-md-2 style="padding:0px">
			<p style="text-align:right; width:100%" style="display:inline" id="btnImprimir">
				<button  onclick="javascript:imprimir();" class="btn btn-link btn-lg">
					<span class="glyphicon glyphicon-print"></span> Imprimir
				</button>
			</p>			
		</div>
		<div class="col-md-4 style="padding:0px"></div>			
		

	
	</div>	
	</div>
	<div class="col-md-6 style="padding:0px">
	
	</div>
  
  
</body>
</html>

