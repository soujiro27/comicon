<!DOCTYPE html>
<?php
	require 'src/conexion.php';	
	$p1 = $_GET['param1']; //Cuenta
	$p2 = $_GET['param2']; //idAuditoria
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
  <link href="css/estilo.css" rel="stylesheet">
  
<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
      
	<style type="text/css">		
		@media screen and (min-width: 968px) {}		
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
						PROGRAMA GENERAL DE AUDITORÍA DE LA CUENTA PÚBLICA <?php echo $p1 ?><br>
						<strong>REPORTE DE PLANEACIÓN</strong><br>
						</p>
					</div>												
		</div>
		<div class="container-fluid">
											
		<?php



			$capitulo="";
			$nTotalRegistros=0;
		
			$sql = "SELECT   a.idAuditoria, COALESCE(a.clave, convert(varchar,a.idAuditoria)) claveAuditoria,  ar.nombre sArea, isnull(aresp.nombre,'') sResponsable,isnull(asresp.nombre,'') sSubresponsable, " .
			"isnull(dbo.lstSujetosByAuditoria(a.idAuditoria),'') sSujeto, dbo.lstObjetosByAuditoria(a.idAuditoria) sObjeto, ta.nombre sTipo, a.objetivo sObjetivo,CONCAT(em.nombre,' ',em.paterno,' ', em.materno) nombre,getdate() AS fecha " . 
			"FROM sia_programas p INNER JOIN sia_auditorias a on p.idCuenta=a.idCuenta and p.idPrograma=a.idPrograma  " . 
			"LEFT JOIN sia_areas ar on a.idArea=ar.idArea " .
			"LEFT JOIN sia_areasresponsables aresp on ar.idArea=aresp.idArea and a.idResponsable = aresp.idResponsable ".
			"LEFT JOIN sia_areassubresponsables asresp on aresp.idArea=asresp.idArea  and aresp.idResponsable = asresp.idResponsable   and a.idSubresponsable = asresp.idSubresponsable " .
			"LEFT JOIN sia_unidades u on a.idCuenta = u.idCuenta and a.idSector=u.idSector and a.idSubsector = u.idSubsector and a.idUnidad=u.idUnidad  " .
			"LEFT  JOIN sia_objetos o on a.idObjeto=o.idObjeto and a.idCuenta=o.idCuenta and a.idPrograma=o.idPrograma and a.idAuditoria = o.idAuditoria  " .
			"LEFT JOIN sia_tiposAuditoria ta on a.tipoAuditoria=ta.idTipoAuditoria " .
			"LEFT JOIN sia_auditoriasauditores aa on a.idAuditoria = aa.idAuditoria and a.idCuenta = aa.idCuenta and a.idPrograma = aa.idPrograma " .
      		"LEFT JOIN sia_empleados em on aa.idAuditor=em.idEmpleado " .
			"WHERE a.idCuenta=:cuenta AND a.idAuditoria=:auditoria and aa.lider='SI' ORDER BY a.idAuditoria;";			
			
			$db = new PDO("sqlsrv:Server={$hostname}; Database={$database}", $username, $password );			
			$dbQuery = $db->prepare($sql);		
			$dbQuery->execute(array(':cuenta' => $p1, ':auditoria' => $p2));
			//$dbQuery->execute();
			$result = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
			
			foreach($result as $row) {

			echo "<table class='table table-striped table-bordered table-hover table-condensed'>";
			echo "<tr><td colspan=2  width='75%'><b></b></td><td><b>FECHA: </b><td>".$row['fecha'] . "</td></tr>";
			echo "</table>";

			echo "<table class='table table-striped table-bordered table-hover table-condensed'>";
			echo "<tr><td width='14%'><b>DIRECCIÓN GENERAL: </b></td><td width='30%'>".$row['sArea']."</td><td width='22%'><b>Clave de la Auditoría: </b></td><td>".$row['claveAuditoria'] . "</td></tr>";
			echo "<tr><td width='14%'><b>DIRECCIÓN ÁREA: </b></td><td width='30%'>".$row['sResponsable']."</td><td width='22%'><b>RUBRO O FUNCIÓN DE GASTO: </b></td><td>".$row['sObjeto'] . "</td></tr>";
			echo "<tr><td width='14%'><b>SUJETO FISCALIZADO: </b></td><td width='30%'>".$row['sSujeto']."</td><td width='22%'><b>TIPO DE AUDITORÍA: </b></td><td>".$row['sTipo'] . "</td></tr>";
			echo "</table>";

			echo "<table class='table table-striped table-bordered table-hover table-condensed'>";
			echo "<tr><td width='18%'><b>OBJETIVO DE LA AUDITORIA: </b></td><td colspan='12'>".$row['sObjetivo']."</td></tr>";
			echo "<tr><td width='18%'><b>ALCANCE DE LA AUDITORIA: </b></td><td colspan='12'>".$row['sObjetivo']."</td></tr>";
			echo "</table>";
			
				$sql = "SELECT f.nombre sFase, orden, min(aa.fInicio ) desde, max(aa.fFin) hasta, sum(convert(int,aa.diasEfectivos)) cantidad " .
				"FROM sia_fases f " . 
				"LEFT JOIN sia_auditoriasactividades aa on f.idFase = aa.idFase and  aa.idAuditoria=:auditoria Group by f.nombre, orden order by f.orden";			
										
				$dbQuery = $db->prepare($sql);		
				$dbQuery->execute(array(':auditoria' => $p2));
				$rs2 = $dbQuery->fetchAll(PDO::FETCH_ASSOC);				

					echo "<table class='table table-striped table-bordered table-hover table-condensed'>";
					echo "<tr><th rowspan=2 width='30%'>FASE</th><th colspan=2>FECHA</th><th rowspan=2 width='20%'>Días Hábiles</th></tr>";
					echo "<tr><th width='20%'>INICIO</th><th width='20%'>TÉRMINO</th></tr>";							
				foreach($rs2 as $row2) {
					echo "<tr><td width='30%'><b>".$row2['sFase']."</b></td><td width='20%'>".$row2['desde']."</td><td width='20%'>".$row2['hasta']."</td><td align='center' width='20%'>".$row2['cantidad']."</td></tr>";
				}
					echo "</table>";

				$sql = "SELECT CONCAT(ap.orden,'.-  ',ap.nombre) nombre, aua.actividad " .
					"FROM sia_programas p " .
					"INNER JOIN sia_auditorias a on p.idCuenta=a.idCuenta and p.idPrograma=a.idPrograma " .
					"LEFT JOIN sia_auditoriasauditores aa on a.idAuditoria = aa.idAuditoria and a.idCuenta = aa.idCuenta and a.idPrograma = aa.idPrograma " .
					"LEFT JOIN sia_AuditoriasApartados aua on a.idAuditoria=aua.idAuditoria " .
					"LEFT JOIN sia_apartados ap on aua.idApartado=ap.idApartado " .
					"WHERE a.idCuenta=:cuenta AND a.idAuditoria=:auditoria and aa.lider='SI' ORDER BY ap.orden;";			
										
				$dbQuery = $db->prepare($sql);		
				$dbQuery->execute(array(':cuenta' => $p1, ':auditoria' => $p2));
				$rs3 = $dbQuery->fetchAll(PDO::FETCH_ASSOC);				

				
					echo "<table id='idrepla' class='table table-striped table-bordered table-hover table-condensed'>";
				$nPagina=1;
				foreach($rs3 as $row3) {
					echo "<tr><td><p align='left'><p><b>".$row3['nombre']."</b></p><p style='text-align: justify;'>".$row3['actividad']."</p></th></tr>";							
				$nPagina++;
				}
					echo "</table>";
				echo "<table class='table table-striped table-bordered table-hover table-condensed'>";
				echo "<tr><td colspan=2  width='50%'><b>ELABORÓ</b><br><br><br><br><br><br><br>".$row['nombre']."</td><td><b>VISTO BUENO</b><br><br><br><br><br><br><br>".$row['nombre'] . "</td></tr>";
				echo "</table>";				
				
			}
			
			
      
			
			
			$db = null;
		?>
		  
		  
		  </tbody>
		</table>
		<p class="pieReporte">
			Fuente: RP <?php echo $p1 ?><br>
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

