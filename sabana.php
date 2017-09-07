<!doctype html>
<html lang="es-mx">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset="utf-8">
  <title>LISTA DE CIUDADANOS CAPTURADOS</title>
  <meta name="description" content="Reporte de Totales">
  <meta name="author" content="JOSE COTA">
<?
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=sabana.xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
 	
      
	<style type="text/css">		
		
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
		<h2>ASCM</h2><br><br>
		<strong>Organizaciòn sin ànimos de lucro.<br, > Ignacio Conmonfort ·30, Del. Iztapalapa, D.F.</strong><br><br>
		LISTA DE CIUDADANOS  POR OPERADOR
	</p>
		<?php		
		
		
		try{
			$nTotalRegistros=0;
			$sql = "SELECT c.idElector, e.nombre sEntidad, d.nombre sDelegacion, col.nombre sColonia, c.idSeccion,  c.nombre, c.paterno, c.materno, c.genero, ".
			"c.fNacimiento, c.curp,  idSeccionReal, c.telefono, concat(p.nombre, ' ', p.paterno,' ', p.materno) sReferente, ".
			"c.calle,uh, super,c.exterior, c.interior, casa, manzana, lote,   andador, c.edificio, c.depto, c.postal,fachada,  ".
			"concat(u.nombre, ' ', u.paterno, ' ', u.materno) sCoordinador, c.fAlta, c.folio, c.telefono, celular1, celular2, vialidad, numeroCalle, tipoCalle " .
			"FROM sce_electores c ".
			"left join sce_personas p on c.referente=p.idPersona  ".
			"left join sce_usuarios u on c.usrAlta=u.idUsuario  ".
			"left join sce_entidades e on c.idEntidad=e.idEntidad ".
			"left join sce_delegaciones d on c.idDelegacion=d.idDelegacion ".
			"left join sce_colonias col on c.idDelegacion=col.idDelegacion and c.colonia=col.idColonia " .
			"ORDER By  c.nombre, c.paterno, c.materno ";
			
			//echo "<br> SQL: <br>" . $sql . "<br>";
							
			mysql_connect("localhost", "contfisc_usrpi", "usrpiCota13");
			mysql_select_db("contfisc_politicai");
			$query = mysql_query($sql);

			echo "<table>";
			echo "<thead><tr><th>CVE. ELECTOR</th><th>NOMBRE</th><th>PATERNO</th><th>MATERNO</th><th>GENERO</th>
			<th>F. NACIMIENTO</th><th>CURP</th><th>FOLIO FAMILIAR</th><th>DELEGACION</th><th>COLONIA</th><th>SECCIÓN</th><th>SECCION REAL</th>
			<th>TELÉFONO FIJO</th><th>TELÉFONO MÓVIL 1</th><th>TELÉFONO MÓVIL 2</th>
			<th>CALLE</th><th>NO. CALLE</th><th>TIPO DE VIALIDAD</th><th>TIPO CALLE</th><th>U.H.</th><th>SUPER MANZANA</th><th>NO. EXTERIOR</th><th>NO. INTERIOR</th><th>CASA</th><th>MANZANA</th><th>LOTE</th>
			<th>ANDADOR</th><th>EDIFICIO</th><th>DEPTO.</th><th>C.P.</th><th>FACHADA</th><th>REFERENTE</th><th>FECHA CAPTURA</th><th>COORDINADOR</th>
			</thead><tbody>";
			while($row = mysql_fetch_array($query)){
				echo "<tr>";
				echo "<td>".$row['idElector']."</td>";
				echo "<td>".$row['nombre']."</td>";
				echo "<td>".$row['paterno']."</td>";
				echo "<td>".$row['materno']."</td>";
				echo "<td>".$row['genero']."</td>";
				
				echo "<td>".$row['fNacimiento']."</td>";
				echo "<td>".$row['curp']."</td>";
				
				echo "<td>".$row['folio']."</td>";
				echo "<td>".$row['sDelegacion']."</td>";
				echo "<td>".$row['sColonia']."</td>";
				echo "<td>".$row['idSeccion']."</td>";
				echo "<td>".$row['idSeccionReal']."</td>";
				
				echo "<td>".$row['telefono']."</td>";
				echo "<td>".$row['celular1']."</td>";
				echo "<td>".$row['celular2']."</td>";
			
				echo "<td>".$row['calle']."</td>";
				echo "<td>".$row['numeroCalle']."</td>";
				echo "<td>".$row['vialidad']."</td>";
				echo "<td>".$row['tipoCalle']."</td>";
				echo "<td>".$row['uh']."</td>";
				echo "<td>".$row['super']."</td>";
				echo "<td>".$row['exterior']."</td>";
				echo "<td>".$row['interior']."</td>";
				echo "<td>".$row['casa']."</td>";
				echo "<td>".$row['manzana']."</td>";
				echo "<td>".$row['lote']."</td>";
				echo "<td>".$row['andador']."</td>";
				echo "<td>".$row['edificio']."</td>";
				echo "<td>".$row['depto']."</td>";
				echo "<td>".$row['postal']."</td>";
				echo "<td>".$row['fachada']."</td>";
				echo "<td>".$row['sReferente']."</td>";
				
				echo "<td>".$row['fAlta']."</td>";
				echo "<td>".$row['sCoordinador']."</td>";
				echo "</tr>";				
				
				$nTotalRegistros = $nTotalRegistros + 1;
				
			}
			echo "<tr><td style='text-align:center' colspan=5><b>Total de Ciudadanos:". $nTotalRegistros . "</b></b></td></tr>".
			"</tbody></table>";
			
			} catch (Exception $e) {
    echo 'Excepción capturada: ',  $e->getMessage(), "\n";
}
		?> 
	
	</div>	
	</div>
	<div class="col-md-6 style="padding:0px">
	
	</div>
  
  
</body>
</html>

