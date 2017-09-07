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
						PROGRAMA GENERAL DE AUDITORÍA DE LA CUENTA PÚBLICA <?php echo $p1 ?><br>
						
						<strong>PROGRAMA ESPECÍFICO DE AUDITORÍA (REPROGRAMACIÓN)</strong><br>
						</p>
					</div>												
		</div>
		<div class="container-fluid">
											
		<?php
			$capitulo="";
			$nTotalRegistros=0;
		
			$sql ="SELECT a.idAuditoria,COALESCE(a.clave, convert(varchar,a.idAuditoria)) claveAuditoria,ar.nombre sArea,isnull(aresp.nombre,'') sResponsable,isnull(asresp.nombre,'') sSubresponsable, " .
				"dbo.lstSujetosByAuditoria(a.idAuditoria) sSujeto, dbo.lstObjetosByAuditoria(a.idAuditoria) sObjeto, ta.nombre sTipo, a.objetivo sObjetivo,CONCAT(em.nombre,' ',em.paterno,' ', em.materno) nombre, au.observacion " .
				"FROM sia_programas p INNER JOIN sia_auditorias a on p.idCuenta=a.idCuenta and p.idPrograma=a.idPrograma " .
				"LEFT JOIN sia_areas ar on a.idArea=ar.idArea " .
				"LEFT JOIN sia_areasresponsables aresp on ar.idArea=aresp.idArea and a.idResponsable = aresp.idResponsable " .
				"LEFT JOIN sia_areassubresponsables asresp on aresp.idArea=asresp.idArea  and aresp.idResponsable = asresp.idResponsable   and a.idSubresponsable = asresp.idSubresponsable " .
				"LEFT JOIN sia_unidades u on a.idCuenta = u.idCuenta and a.idSector=u.idSector and a.idSubsector = u.idSubsector and a.idUnidad=u.idUnidad " .
				"LEFT  JOIN sia_objetos o on a.idObjeto=o.idObjeto and a.idCuenta=o.idCuenta and a.idPrograma=o.idPrograma and a.idAuditoria = o.idAuditoria " .
				"LEFT JOIN sia_tiposAuditoria ta on a.tipoAuditoria=ta.idTipoAuditoria " .
				"LEFT JOIN sia_auditoriasauditores aa on a.idAuditoria = aa.idAuditoria and a.idCuenta = aa.idCuenta and a.idPrograma = aa.idPrograma " .
	      		"LEFT JOIN sia_empleados em on aa.idAuditor=em.idEmpleado " .
	      		"LEFT JOIN sia_auditorias au on p.idCuenta = au.idCuenta AND p.idPrograma = au.idPrograma AND aa.idAuditoria=au.idAuditoria " .
				"WHERE a.idCuenta=:cuenta AND a.idAuditoria=:auditoria and aa.lider='SI' ORDER BY a.idAuditoria;";			
			
			$db = new PDO("sqlsrv:Server={$hostname}; Database={$database}", $username, $password );			
			$dbQuery = $db->prepare($sql);		
			$dbQuery->execute(array(':cuenta' => $p1, ':auditoria' => $p2));
			//$dbQuery->execute();
			$result = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
			
			$nPagina=1;
			foreach($result as $row) {

				echo "<table class='table table-striped table-bordered table-hover table-condensed'>";
				echo "<tr><td colspan=1><b>DIRECCIÓN GENERAL</b><br>".$row['sArea']."</td><td colspan=1><b>DIRECCIÓN ÁREA</b><br>".$row['sResponsable'] . "</td><td colspan=3><b>SUBDIRECCIÓN</b><br>".$row['sSubresponsable']."</td></tr>";		
				echo "<tr><td colspan=3><b>SUJETO FISCALIZADO</b><br>".$row['sSujeto']."</td><td colspan=2><b>TIPO DE AUDITORÍA</b><br>".$row['sTipo'] . "</td></tr>";
				echo "<tr><td colspan=3><b>RUBRO O FUNCIÓN DE GASTO (O SU EQUIVALENTE)</b><br><p>".$row['sObjeto']."</p></td><td colspan=2><b>CLAVE</b><br>".$row['claveAuditoria'] . "</td></tr>";								
				


				$sql = "SELECT sum(convert(INT,aa.diasEfectivos)) sumaoriginal, sum(convert(INT, are.diasReprogramados)) sumareprogramada
    					FROM sia_auditoriasactividades aa
    					left join sia_AuditoriasReprogramaciones are on aa.idAuditoria = are.idAuditoria and aa.idFase = are.idFase and aa.idPrograma = are.idPrograma
    					WHERE aa.idAuditoria=:auditoria AND ( aa.idFase='PLANEACION' OR aa.idFase='EJECUCION' OR aa.idFase='INFORMES');";
				$dbQuery = $db->prepare($sql);		
				$dbQuery->execute(array(':auditoria' => $p2));
				$rs = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

				foreach($rs as $row1) {
				 $diasoriginales = $row1['sumaoriginal'];
				 $diasreprogramados = $row1['sumareprogramada'];
				}
				
				echo "<tr><td rowspan=3  colspan=3><b>OBJETIVO</b><p>".$row['sObjetivo']."</p></td><td colspan=2><b>TOTAL DE DÍAS HÁBILES</b></td></tr>";
				echo "<tr><td><b>ORIGINAL</b></td><td><b>MODIFICADO</b></td></tr>";
				echo "<tr><td><b>".$diasoriginales."</b></td><td><b>".$diasreprogramados."</b></td></tr>";
				echo "</table>";
				


				$sql = "SELECT f.nombre sFase, orden, min(aa.fInicio ) originalini, Case WHEN ar.fIniReprogramacion is not null THEN CONVERT(VARCHAR(10),ar.fIniReprogramacion,103) ELSE '--' END inireprogramada, max(aa.fFin) originalfin, Case WHEN ar.fFinReprogramacion is not null THEN CONVERT(VARCHAR(10),ar.fFinReprogramacion,103) ELSE '--' END finreprogramada, sum(convert(int,aa.diasEfectivos)) diasoriginal, Case WHEN ar.diasReprogramados is not null THEN CONVERT(VARCHAR(10),ar.diasReprogramados,103) ELSE '--' END diasreprogramada " .
					"FROM sia_fases f " .
					"LEFT JOIN sia_auditoriasactividades aa on f.idFase = aa.idFase and  aa.idAuditoria=:auditoria " .
    				"LEFT JOIN sia_AuditoriasReprogramaciones ar on f.idFase=ar.idFase and aa.idAuditoria = ar.idAuditoria and aa.idActividad=ar.idActividad " .
    				"Group by f.nombre, orden,ar.fIniReprogramacion,fFinReprogramacion,ar.diasReprogramados order by f.orden";			
										
				$dbQuery = $db->prepare($sql);		
				$dbQuery->execute(array(':auditoria' => $p2));
				$rs2 = $dbQuery->fetchAll(PDO::FETCH_ASSOC);				

				echo "<table class='table table-striped table-bordered table-hover table-condensed'>";
				echo "<tr><th rowspan=2 width='40%'>FASE</th><th colspan=2 width='20%'>INICIO</th><th colspan=2 width='20%'>TÉRMINO</th><th colspan=2 width='20%'>TOTAL</th></tr>";
				echo "<tr><th width='10%'>ORIGINAL</th><th width='10%'>MODIFICADO</th><th width='10%'>ORIGINAL</th><th width='10%'>MODIFICADO</th><th width='10%'>ORIGINAL</th><th width='10%'>MODIFICADO</th></tr>";							
				foreach($rs2 as $row2) {
					$Fase = $row2['sFase'];
					$inioriginal = $row2['originalini'];
					$reprogramadaini = $row2['inireprogramada'];
					$finoriginal = $row2['originalfin'];
					$reprogramadafin = $row2['finreprogramada'];
					$originaldias = $row2['diasoriginal'];
					$reprogramadadias = $row2['diasreprogramada'];
				echo "<tr><td width='40%'><b>".$Fase."</b></td><td width='10%'><b>".$inioriginal."</b></td><td width='10%'>".$reprogramadaini."</td><td width='10%'>".$finoriginal."</td><td width='10%'>".$reprogramadafin."</td><td width='10%'>".$originaldias."</td><td width='10%'>".$reprogramadadias."</td></tr>";				
				}
				
				echo "<tr><td colspan=7><b>OBSERVACIONES</b><br></td></tr>";				
				echo "<tr><td colspan=7><p style='text-align:center'>".$row['observacion']."</p><br></td></tr>";
				echo "<tr>
						<td colspan=2>
							<b>ELABORÓ</b>
							<br>
							<p style='text-align:center'>".$row['nombre']."</p>
						</td>
						<td colspan=5>
							<b>VISTO BUENO</b>
							<p style='text-align:center'>HECTOR VIDAL</p>
						</td>
					</tr>";
				    
				echo "</table>";				
				
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

