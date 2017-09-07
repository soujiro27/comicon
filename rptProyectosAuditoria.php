<!DOCTYPE html>

<?php
	require 'src/conexion.php';	
	$area = $_GET['area'];
	$p1 = $_GET['param1'];
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
			text-align:justify;
		}
		
		.pageBreak {page-break-after: always;font: normal bold 8px Arial;}
		
		
		
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
											
		<?php
			$capitulo="";
			$nTotalRegistros=0;
		
			$sql = "SELECT COALESCE(a.clave, convert(varchar,a.idAuditoria)) claveAuditoria,  isnull(u.nombre,'') sujeto, dbo.lstObjetosByAuditoria(a.idAuditoria) objetos, ta.nombre sTipo, ar.nombre sArea, a.objetivo, a.alcance, a.justificacion " .
			"FROM sia_programas p " .
			"INNER JOIN sia_auditorias a on p.idCuenta=a.idCuenta and p.idPrograma=a.idPrograma  " .
			"INNER JOIN sia_areas ar on a.idArea=ar.idArea  " .
			"INNER JOIN sia_unidades u on a.idCuenta = u.idCuenta and a.idSector=u.idSector and a.idSubsector = u.idSubsector and a.idUnidad=u.idUnidad  " .
			"LEFT JOIN sia_objetos o on a.idObjeto=o.idObjeto and a.idCuenta=o.idCuenta and a.idPrograma=o.idPrograma and a.idAuditoria = o.idAuditoria  " .
			"inner join sia_tiposAuditoria ta on a.tipoAuditoria=ta.idTipoAuditoria " .
			"Where a.idCuenta=:cuenta  and a.idArea=:area  " .
			"ORDER BY a.idAuditoria;";
			

			$db = new PDO("sqlsrv:Server={$hostname}; Database={$database}", $username, $password );			
			$dbQuery = $db->prepare($sql);		
			//$dbQuery->execute();
			$dbQuery->execute(array(':cuenta' => $p1, ':area' => $area));
			$result = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
			
			$nPagina=1;
			foreach($result as $row) {
			
				echo "<div class='container-fluid'><div class='col-xs-4'><img src='img/logo-top.png'></div><div class='col-xs-8'><p class='encabezado'>AUDITORÍA SUPERIOR DE LA CIUDAD DE MÉXICO<br>
								PROGRAMA GENERAL DE AUDITORÍA DE LA CUENTA PÚBLICA $p1<br><strong>PROYECTO DE AUDITORÍA</strong></p></div></div><br>";
								
				echo "<table class='table table-striped table-bordered table-hover table-condensed'>";
				echo "<thead><th width='25%'>DIRECCIÓN GENERAL</th><th width='25%'>TIPO DE AUDITORÍA</th><th width='25%'>SUJETO FISCALIZADO</th><th width='25%'>RUBRO O FUNCIÓN DE GASTO (O SU EQUIVALENTE)</th></thead><tbody>";			
				echo "<tr><td style='text-align:left' width='25%'>".$row['sArea']."</td><td>".$row['sTipo'] . "</td><td>".$row['sujeto']."</td><td>".$row['objetos']."</td></tr>";
				echo "<tr><td colspan='4'><b>OBJETIVO:</b><br>".$row['objetivo']."</td></tr>";				
				echo "<tr><td colspan='4'><b>ALCANCE:</b><br>".$row['alcance']."</td></tr>";				
				echo "<tr><td colspan='4'><b>JUSTIFICACION:</b><br>".$row['justificacion']."</td></tr>";
				echo "<tr><td style='text-align:center' colspan='2'><b>ELABORÓ:</b><br><br><br><br><br></td><td style='text-align:center' colspan='2'><b>VISTO BUENO:</b><br><br><br><br><br></td></tr>";				
				echo "</table><p class='pageBreak'>Página $nPagina</p>";
				$nPagina++;
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
	</div>	

	<div class="col-md-4 style="padding:0px">
	
	</div>
  
  
</body>
</html>

