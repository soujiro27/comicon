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
						AUDITORÍA SUPERIOR DE LA CIUDAD DE MÉXICO<br>
						DIRECCIÓN GENERAL DE AUDITORÍA<br>DE CUMPLIMIENTO FINANCIERO "C"<br>
						<strong>PROGRAMA GENERAL DE AUDITORÍA.<BR>CUENTA PÚBLICA <?php echo $p1 ?></strong><br>						
						</p>
					</div>												
		</div>
		<table class="table table-responsive table-striped  table-hover table-condensed">
		  <thead>
			<th width="10%">NUM</th><th width="30%">SUJETOS DE FISCALIZACION</th><th>RUBROS O FUNCIONES DE GASTO</th><th width="15%">TIPO DE AUDITORÍA</th>
		  </thead>
			<tbody>
		<?php
			$capitulo="";
			$nTotalRegistros=0;
		
			$sql = "SELECT COALESCE(a.clave, convert(varchar,a.idAuditoria)) claveAuditoria,  isnull(u.nombre,'') sujeto, dbo.lstObjetosByAuditoria(a.idAuditoria) objetos, ta.nombre tipo " .
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
			foreach($result as $row) {
				echo "<tr><td style='text-align:center'><b>".$row['claveAuditoria']."</b></td><td>".$row['sujeto'] . "</td><td>".$row['objetos']."</td><td>".$row['tipo']."</td></tr>";
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

	<div class="col-md-4 style="padding:0px">
	
	</div>
  
  
</body>
</html>

