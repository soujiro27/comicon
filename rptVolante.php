<!DOCTYPE html>
<?php
	require 'src/conexion.php';	
	$p1 = $_GET['param1']; //idVolante
	$p2 = $_GET['param2']; //folio
	?>
	
<html lang="es">
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
  <link href="css/estilo.css" rel="stylesheet"> 
  
<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
      
	<style type="text/css">		
		@media screen and (min-width: 968px) {}		
		body{
			background:transparent;
			text-align:center;
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
		p{font: normal 10px/15px Arial;
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
					<div class="col-xs-3"><img src="img/logo-top.png"></div>								
					<div class="col-xs-9">
						<p class="encabezado">
						AUDITORÍA SUPERIOR DE LA CIUDAD DE MÉXICO<br>
						<strong>DIRECCIÓN GENERAL DE ASUNTOS JURÍDICOS</strong><br>
						</p>
					</div>												
		</div>
		<div class="container-fluid">
											
		<?php
			$capitulo="";
			$nTotalRegistros=0;
		
			$sql ="SELECT vo.folio, case when vo.fModificacion is NULL then CONVERT(VARCHAR(10),vo.fAlta, 103) else CONVERT(VARCHAR(10),vo.fModificacion, 103) END fecha, td.nombre docu,vo.numDocumento numdoc,vo.fDocumento fedocu, vo.anexos, concat(CONVERT(VARCHAR(10),vo.fRecepcion, 103),' ',CONVERT(VARCHAR(10),vo.hRecepcion, 108)) ferecep,concat(cre.siglas,' ',cre.nombre) nombre,cre.cargo cargo,cre.procedencia procedencia,vo.destinatario destina,vo.asunto asunto,cca.nombre caracter,ctu.nombre turnado,cac.nombre accion FROM sia_Volantes vo 
				INNER JOIN sia_tiposdocumentos td on vo.idDocumento=td.idTipoDocto
				INNER JOIN sia_CatRemitentes cre on vo.idRemitente=cre.idRemitente
				INNER JOIN sia_CatCaracteres cca on vo.idCaracter = cca.idCaracter
				INNER JOIN sia_CatTurnados ctu on vo.idTurnado = ctu.idTurnado
				INNER JOIN sia_CatAcciones cac on vo.idAccion = cac.idAccion
				WHERE vo.idVolante=:volante and vo.folio=:folio;";			
			
			$db = new PDO("sqlsrv:Server={$hostname}; Database={$database}", $username, $password );			
			$dbQuery = $db->prepare($sql);		
			$dbQuery->execute(array(':volante' => $p1, ':folio' => $p2));
			//$dbQuery->execute();
			$result = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
			
			$nPagina=1;
			foreach($result as $row) {

		echo "<table class='table table-striped table-bordered table-hover table-condensed'>";
		echo "<tr><td colspan=3><b></b></td><td width='10%'><b>FOLIO: </b><td>".$row['folio'] . "</td></tr>";
		echo "<tr><td colspan=3><b align='left'>DATOS DEL DOCUMENTO</b></td><td><b>FECHA: </b></td><td>".$row['fecha'] . "</td></tr>";
		
		echo "<tr><td><b>TIPO</b></td><td><b>NÚM. DOCUMENTO</b></td><td><b>FECHA DE DOCUMENTO</b></td><td><b>ANEXO</b></td><td><b>FECHA DE RECEPCION</b></td></tr>";

		echo "<tr><td><b>".$row['docu']."</b></td><td><b>".$row['numdoc']."</b></td><td><b>".$row['fedocu']."</b></td><td><b>".$row['anexos']."</b></td><td><b>".$row['ferecep']."</b></td></tr>";

		echo "</table>";
		
		echo "<table class='table table-striped table-bordered table-hover table-condensed' id='tabla2'>";
		echo "<tr><td colspan=1 width='25%'><b>REMITENTE</b></td><td colspan=1 width='75%'></td></tr>";
		echo "</table>";

		echo "<table class='table table-striped table-bordered table-hover table-condensed;' id='remitente'>";
		echo "<tr><td><b>NOMBRE</b><br>".$row['nombre']."</td><td><b>CARGO</b><br>".$row['cargo']."</td></tr>";
		echo "<tr><td><b>DEPENDENCIA</b><br>".$row['procedencia']."</td><td><b>DESTINATARIO</b><br>".$row['destina']."</td></tr>";
		echo "</table>";

		echo "<table class='table table-striped table-bordered table-hover table-condensed' id='tabla2'>";
		echo "<tr><td colspan=1 width='25%'><b>ASUNTO</b></td><td colspan=1 width='75%'></td></tr>";
		echo "</table>";

		echo "<table class='table table-striped table-bordered table-hover table-condensed;'>";
		echo "<tr><td id='asunt'>".$row['asunto']."</td</tr>";
		echo "</table>";

		echo "<table class='table table-striped table-bordered table-hover table-condensed;'>";
		echo "<tr><td><b>CARACTER</b></td><td><b>TURNADO A</b></td><td><b>INSTRUCCIÓN</b></td></tr>";
		echo "<tr><td>".$row['caracter']."</td><td>".$row['turnado']."</td><td>".$row['accion']."</td></tr>";
		echo "</table>";


		echo "<table class='table table-striped table-bordered table-hover table-condensed' id='tabla2'>";
		echo "<tr><td colspan=1 width='25%'><b>CONTROL DE GESTIÓN</b></td><td colspan=1 width='75%'></td></tr>";
		echo "</table>";

		echo "<table class='table table-striped table-bordered table-hover table-condensed;' id='control'>";
		echo "<tr><td>ACUSE DE RECIBO: <br>NOMBRE Y FIRMA: </td</tr>";
		echo "</table>";

		echo "<table class='table table-striped table-bordered table-hover table-condensed' id='tabla2'>";
		echo "<tr><td colspan=1 width='25%'><b>ACCIONES IMPLEMENTADAS</b></td><td colspan=1 width='75%'></td></tr>";
		echo "</table>";

		echo "<table class='table table-striped table-bordered table-hover table-condensed;'>";
		echo "<tr><td></td></tr>";
		echo "</table>";		


		echo "<table class='table table-striped table-bordered table-hover table-condensed' id='tabla2'>";
		echo "<tr><td colspan=1 width='25%'><b>EXPEDIENTE</b></td><td colspan=1 width='75%'></td></tr>";
		echo "</table>";

		echo "<table class='table table-striped table-bordered table-hover table-condensed;'>";
		echo "<tr><td></td></tr>";
		echo "</table>";


		echo "<table class='table table-striped table-bordered table-hover table-condensed;' id='footer'>";
		echo "<tr><td COLSPAN=3></td><td>___________________________________</td></tr>";
		echo "<tr><td width='25%'></td><td width='25%'></td><td width='25%'>PENDIENTE</td><td width='25%'>DESAHOGO</td></tr>";
		echo "</table>";
			
				$nPagina++;
			}
			
			
      
			
			
			$db = null;
		?>
		  
		  
		  </tbody>
		</table>
		<p class="pieReporte">
			Fuente: JURIDICO <br>
		</p>
		<br>
		<div class="col-md-4 style="padding:0px"></div>
		<div class="col-md-2 style="padding:0px">
			<p id="divBotones">
				<!--<button  onclick="javascript:window.location.assign('');" class="btn btn-default btn-xs"></span> Cerrar</button>-->
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

