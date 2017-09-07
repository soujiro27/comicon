<?php

	include("midle/middle.php");
	include("src/conexion.php");


	try{
		$db = new PDO("sqlsrv:Server={$hostname}; Database={$database}", $username, $password );
	}catch (PDOException $e) {
		print "ERROR: " . $e->getMessage();
		die();
	}
	
	
	$app->get('/cargarArchivo/Universo/:nombre/:cuenta', function($nombre, $cuenta)    use($app, $db) {	
		try{
			$usrActual = $_SESSION["idUsuario"];
			$archivo ='uploads/' . $nombre;

			//Elimina cuenta pública
			$sql="DELETE FROM sia_cuentasingresos WHERE idCuenta= :cuenta ;";
			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':cuenta' => $cuenta));

			//Abrir archivo de XLS
			$xlsx = new SimpleXLSX($archivo);
			list($num_cols, $num_rows) = $xlsx->dimension();			

			//insertar 
			$sql="INSERT INTO sia_cuentasuniversos (idCuenta, origen, tipo, clave, nivel, nombre,  original, recaudado, usrAlta, fAlta, estatus) " .
			"values(:cuenta,:origen, :tipo, :clave, :nivel, :nombre, :original, :recaudado, :usrActual, getdate(), 'ACTIVO')";

			$dbQuery = $db->prepare($sql);
			$nRegistros=0;
			
			error_reporting(E_ALL ^ E_NOTICE);
			
			foreach( $xlsx->rows() as $row ) {				
				$origen = $row[0];
				$tipo =  "" . $row[1];
				$clave =  "" . $row[2];
				$nivel =  "" . $row[3];
				$nombre =  "" . $row[4];
				$original =  "" . $row[5];
				$recaudado =  "" . $row[6];
								
				if ($nRegistros>0){
					//$dbQuery->execute(array(':cuenta' => $cuenta, ':origen' => $origen, ':tipo' => $tipo,':clave' => $clave, ':nivel' => $nivel, ':nombre' => $nombre, 
					//':original' => $original, ':recaudado' => $recaudado,':usrActual' => $usrActual));								
				}				
				$nRegistros++;	
			}
			echo "Se cargaron " . $nRegistros . " registro(s).";
		}catch (PDOException $e) {
			echo  "<br>¡Error en el TRY!: " . $e->getMessage();
			die();
		}
	});	

	
	
	
	
	
	
	$app->get('/cargarArchivo/Ingreso/:nombre/:cuenta', function($nombre, $cuenta)    use($app, $db) {	
		try{
			$usrActual = $_SESSION["idUsuario"];
			$archivo ='uploads/' . $nombre;

			//Elimina cuenta pública
			$sql="DELETE FROM sia_cuentasingresos WHERE idCuenta= :cuenta ;";
			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':cuenta' => $cuenta));

			//Abrir archivo de XLS
			$xlsx = new SimpleXLSX($archivo);
			list($num_cols, $num_rows) = $xlsx->dimension();			

			//insertar 
			$sql="INSERT INTO sia_cuentasingresos (idCuenta, origen, tipo, clave, nivel, nombre,  original, recaudado, usrAlta, fAlta, estatus) " .
			"values(:cuenta,:origen, :tipo, :clave, :nivel, :nombre, :original, :recaudado, :usrActual, getdate(), 'ACTIVO')";

			$dbQuery = $db->prepare($sql);
			$nRegistros=0;
			
			error_reporting(E_ALL ^ E_NOTICE);
			
			foreach( $xlsx->rows() as $row ) {				
				$origen = $row[0];
				$tipo =  "" . $row[1];
				$clave =  "" . $row[2];
				$nivel =  "" . $row[3];
				$nombre =  "" . $row[4];
				$original =  "" . $row[5];
				$recaudado =  "" . $row[6];
								
				if ($nRegistros>0){
					$dbQuery->execute(array(':cuenta' => $cuenta, ':origen' => $origen, ':tipo' => $tipo,':clave' => $clave, ':nivel' => $nivel, ':nombre' => $nombre, ':original' => $original, ':recaudado' => $recaudado,':usrActual' => $usrActual));								
				}				
				$nRegistros++;	
			}
			echo "Se cargaron " . $nRegistros . " registro(s).";
		}catch (PDOException $e) {
			echo  "<br>¡Error en el TRY!: " . $e->getMessage();
			die();
		}
	});

	$app->get('/cargarArchivo/Egreso/:nombre/:cuenta', function($nombre, $cuenta)    use($app, $db) {	
		try{
			$usrActual = $_SESSION["idUsuario"];
			$archivo ='uploads/' . $nombre;
			$esEncabezado = true;
			$estatus = "";

			//////Elimina cuenta pública
			//// $sql="DELETE FROM sia_cuentasdetalles WHERE idCuenta= :cuenta ;";
			//// $dbQuery = $db->prepare($sql);
			//// $dbQuery->execute(array(':cuenta' => $cuenta));

			//Elimina archivo base para almacenar el archivo de egresos de la cuenta pública
			$sql="DELETE FROM sia_CtaPublicaBase ;";
			$dbQuery = $db->prepare($sql);
			$dbQuery->execute();

			//Abrir archivo de XLS
			$xlsx = new SimpleXLSX($archivo);
			list($num_cols, $num_rows) = $xlsx->dimension();		

			//$estatus = ' El valor de $num_cols es:	' . $num_cols . ' El valor de $num_rows es:	' . $num_rows;		

			//insertar 
			$sql="INSERT INTO sia_CtaPublicaBase (IDCUENTA, CENTRO_GESTOR, SECTOR, SUBSECTOR, UNIDAD, URG, AREA_FUNCIONAL, IDEJE, NOMEJE, EJE, IDFINALIDAD, NOMFINALIDAD, FINALIDAD, IDFUNCION, NOMFUNCION, FUNCION, IDSUBFUNCION, NOMSUBFUNCION, SUBFUNCION, IDACTIVIDAD, NOMACTIVIDAD, ACTIVIDAD, FONDO, FF, FG, FFFG, FE, FFFGFE, AD, ORIGEN_REC, FUENTE_FINANCIAMIENTO, FUENTE_GENERICA, FUENTE_ESPECIFICA, ORIGEN_RECURSO, POSICION_PRESUPUESTAL, CAPITULO, PTDA, DESCRIPCION_PARTIDA, TG, TIPO_GASTO, DI, DIGITO_IDENT, DG, DESTINO_GASTO, PROYECTO_INVERSION, ORIGINAL, MODIFICADO, EJERCIDO) VALUES (:idCuenta, :centroGestor, :sector, :subsector, :unidad, :nombreEnte, :areaFuncional, :idEje, :nomEje, :eje, :idFinalidad, SUBSTRING(:nomFinalidad, 4, LEN(:nomFinalidad1)-4), :finalidad, :idFuncion, SUBSTRING(:nomFuncion, 4, LEN(:nomFuncion1)-4), :funcion, :idSubfuncion, SUBSTRING(:nomSubfuncion, 4, LEN(:nomSubfuncion1)-4), :subfuncion, :idActividad, :nomActividad, :actividad, :fondo, :ff, :fg, :fffg, :fe, :fffgfe, :ad, :origen_rec, :fuenteFinanciamiento, :fuenteGenerica, :fuenteEspecifica, :origenRecurso, :posicionPresup, :capitulo, :partida, :partidaDescripcion, :tg, :tipoGasto, :di, :digito, :dg, :destinoGasto, :proyectoInversion, :original, :modificado, :ejercido);";


			$dbQuery = $db->prepare($sql);
			$nRegistros=0;
			
			//$caracteres = array(char(34), char(147), char(148));
			error_reporting(E_ALL ^ E_NOTICE);


			foreach( $xlsx->rows() as $row ) {		
				$centroGestor   	  = $row[0];
				
				$sector               = substr($row[0], 0,2);
				$subsector            = substr($row[0], 2,2);
				$unidad               = substr($row[0], 4,2);
				$nombreEnte           = $row[1];
				$areaFuncional        = $row[2];

				$EjeTemp              = str_replace('EJE','', $row[3]);
				$EjeTemp              = trim( str_replace(':','', $EjeTemp ) );
				$idEje                = substr($EjeTemp, 0, 1);
				$EjeTemp			  = substr($EjeTemp, 1);
				$nomEje               = trim($EjeTemp);
				$eje                  = $row[3];

				$idFinalidad          = substr(ltrim($row[4]),0,1);
				$nomFinalidad         = $row[4];
				$nomFinalidad1        = $row[4];
				$finalidad            = $row[4];

				$idFuncion 		      = substr(ltrim($row[5]),0,1);
				$nomFuncion           = $row[5];
				$nomFuncion1          = $row[5];
				$funcion          	  = $row[5];

				$idSubfuncion 		  = substr(ltrim($row[6]),0,1);
				$nomSubfuncion        = $row[6];
				$nomSubfuncion1       = $row[6];
				$subfuncion           = $row[6];

				$idActividad 		  = substr(ltrim($row[7]),0,3);
				$nomActividad         = substr(ltrim($row[7]),3);
				$actividad            = $row[7];

				$fondo                = $row[8];
				$ff                   = $row[9];
				$fg                   = $row[10];
				$fffg                 = $row[11];
				$fe                   = $row[12];
				$fffgfe               = $row[13];
				$ad                   = $row[14];
				$origen_rec           = $row[15];
				$fuenteFinanciamiento = $row[16];
				$fuenteGenerica       = $row[17];
				$fuenteEspecifica     = $row[18];
				$origenRecurso        = $row[19];
				$posicionPresup       = $row[20];
				$capitulo			  = (int)substr(ltrim($row[21]),0,1) * 1000;
				$partida              = $row[21];
				$partidaDescripcion   = $row[22];
				$tg            		  = $row[23];
				$tipoGasto            = $row[24];
				$di                   = $row[25];
				$digito               = $row[26];
				$dg                   = $row[27];
				$destinoGasto         = $row[28];
				$proyectoInversion    = $row[29];
				$original             = $row[30];
				$modificado           = $row[31];
				$ejercido             = $row[32];
				

				//$estatus = $estatus . "  El valor del centro gestor es: $centroGestor, El Valor de esEncabezado es: $esEncabezado";
								
				//$estatus = $estatus . ' >>> centroGestor: ' . $centroGestor . ' nombreEnte: ' . $nombreEnte . ' areaFuncional: ' . $areaFuncional . ' eje: ' . $eje . ' idFinalidad: ' . $idFinalidad . ' nomFinalidad: ' . $nomFinalidad . ' finalidad: ' . $finalidad . ' idFuncion: ' . $idFuncion . ' nomFuncion: ' . $nomFuncion . ' funcion: ' . $funcion . ' idSubfuncion: ' . $idSubfuncion . ' nomSubfuncion: ' . $nomSubfuncion . ' subfuncion: ' . $subfuncion . ' idActividad: ' . $idActividad . ' nomActividad: ' . $nomActividad . ' actividad: ' . $actividad . ' fondo: ' . $fondo . ' ff: ' . $ff . ' fg: ' . $fg . ' fffg: ' . $fffg . ' fe: ' . $fe . ' fffgfe: ' . $fffgfe . ' ad: ' . $ad . ' origen_rec: ' . $origen_rec . ' fuenteFinanciamiento: ' . $fuenteFinanciamiento . ' fuenteGenerica: ' . $fuenteGenerica . ' fuenteEspecifica: ' . $fuenteEspecifica . ' origenRecurso: ' . $origenRecurso . ' posicionPresup: ' . $posicionPresup . ' partida: ' . $partida . ' partidaDescripcion: ' . $partidaDescripcion . ' tg: ' . $tg . ' tipoGasto: ' . $tipoGasto . ' di: ' . $di . ' digito: ' . $digito . ' dg: ' . $dg . ' destinoGasto: ' . $destinoGasto . ' proyectoInversion: ' . $proyectoInversion . ' original: ' . $original . ' modificado: ' . $modificado . ' ejercido: ' . $ejercido . '<<< ';

				if ($esEncabezado == false){   // Esta  condición es para saltarse los titulos de las columnas del archivo de EXCEL.

					if (ltrim(rtrim($centroGestor)) != "" ){

						try{
							
							$dbQuery->execute(array(':idCuenta' => $cuenta, ':centroGestor' => $centroGestor, ':sector' => $sector,':subsector' => $subsector,':unidad' => $unidad, ':nombreEnte' => $nombreEnte, ':areaFuncional' => $areaFuncional,':idEje' => $idEje,':nomEje' => $nomEje,':eje' => $eje, ':idFinalidad' => $idFinalidad, ':nomFinalidad' => $nomFinalidad, ':nomFinalidad1' => $nomFinalidad1, ':finalidad' => $finalidad, ':idFuncion' => $idFuncion, ':nomFuncion' => $nomFuncion, ':nomFuncion1' => $nomFuncion1, ':funcion' => $funcion, ':idSubfuncion' => $idSubfuncion, ':nomSubfuncion' => $nomSubfuncion, ':nomSubfuncion1' => $nomSubfuncion1, ':subfuncion' => $subfuncion, ':idActividad' => $idActividad, ':nomActividad' => $nomActividad, ':actividad' => $actividad, ':fondo' => $fondo, ':ff' => $ff, ':fg' => $fg, ':fffg' => $fffg, ':fe' => $fe, ':fffgfe' => $fffgfe, ':ad' => $ad, ':origen_rec' => $origen_rec, ':fuenteFinanciamiento' => $fuenteFinanciamiento, ':fuenteGenerica' => $fuenteGenerica, ':fuenteEspecifica' => $fuenteEspecifica, ':origenRecurso' => $origenRecurso, ':posicionPresup' => $posicionPresup, ':capitulo' => $capitulo, ':partida' => $partida, ':partidaDescripcion' => $partidaDescripcion, ':tg' => $tg, ':tipoGasto' => $tipoGasto, ':di' => $di, ':digito' => $digito, ':dg' => $dg, ':destinoGasto' => $destinoGasto, ':proyectoInversion' => $proyectoInversion, ':original' => $original, ':modificado' => $modificado, ':ejercido' => $ejercido));
							

						}catch (Exception $e) {
							
						  	echo "ERROR: " . $e->getMessage() . "<br><br> QUERY: " . $dbQuery ;

					  	}

						$nRegistros++;	
					}

				}else{
					$esEncabezado = false;
				}	

				// Quitar este código despues
				//if ($nRegistros == 4){ break; }

			}

			//* Quitar despues

			if($nRegistros > 0){

				$estatus =  $estatus . "Se recupero la información del archivo de Excel al archivo temporal ";

				// ahora se llenaran los catalogos en base a el archivo de la cuenta publica que se esta cargando 

 				// Se inicia la carga de la tabla de Ejes

				$sql="DELETE FROM sia_ejes WHERE idCuenta = :idCuenta;";
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':idCuenta' => $cuenta));

				$sql = "INSERT INTO sia_ejes (idCuenta, idEje, nombre, usrAlta) SELECT :idCuenta, h.idEje, h.nomEje, 1 usuario FROM (select distinct a.idEje as idEje, a.nomEje from (select substring(ltrim(x.IdEje),1,1) idEje, x.nomEje from (Select IdEje, nomEje from sia_CtaPublicabase) x) a group by a.idEje , a.nomEje) h ORDER BY h.idEje;";

				$dbQuery = $db->prepare($sql);
				$dbQuery->execute( array(':idCuenta' => $cuenta) );
				$estatus =  $estatus . " => Se cargó la información del Catálogo: EJES. ";

				// Se inicia la carga de la tabla de Finalidades

				$sql="DELETE FROM sia_finalidades WHERE idCuenta = :idCuenta;";
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':idCuenta' => $cuenta));

				$sql = "INSERT INTO sia_finalidades(idCuenta, idFinalidad, nombre, usrAlta, fAlta, estatus) SELECT :idCuenta, a.idFinalidad, a.nombre, a.usrAlta, a.falta, a.estatus FROM (select distinct idFinalidad, upper(nomfinalidad) nombre, 1 usrAlta, getdate() fAlta, 'ACTIVO' estatus from sia_ctaPublicaBase group by idFinalidad, nomfinalidad) a;";

				$dbQuery = $db->prepare($sql);
				$dbQuery->execute( array(':idCuenta' => $cuenta) );
				$estatus =  $estatus . " => Se cargó la información del Catálogo: FINALIDADES. ";

				// Se inicia la carga de la tabla de Funciones

				$sql="DELETE FROM sia_funciones WHERE idCuenta = :idCuenta;";
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':idCuenta' => $cuenta));

				$sql = "INSERT INTO sia_funciones (idCuenta, idFinalidad,idFuncion,nombre, usrAlta, fAlta, estatus) SELECT :idCuenta, a.idFinalidad, a.idfuncion, a.nombre, a.usrAlta, a.fAlta, a.estatus FROM (select distinct idFinalidad, idfuncion, upper(nomFuncion) nombre, 1 usrAlta, getdate() fAlta, 'ACTIVO' estatus from sia_ctapublicabase group by idFinalidad, idfuncion, nomFuncion) a;";

				$dbQuery = $db->prepare($sql);
				$dbQuery->execute( array(':idCuenta' => $cuenta) );
				$estatus =  $estatus . " => Se cargó la información del Catálogo: FUNCIONES. ";

				// Se inicia la carga de la tabla de Subfunciones

				$sql="DELETE FROM sia_subfunciones WHERE idCuenta = :idCuenta;";
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':idCuenta' => $cuenta));

				$sql = "INSERT INTO sia_subfunciones (idCuenta, idFinalidad, idFuncion, idSubfuncion, nombre, usrAlta, fAlta, estatus) SELECT :idCuenta, a.idFinalidad, a.idfuncion, a.idSubfuncion, a.nombre, a.usrAlta, a.fAlta, a.estatus FROM (select distinct idFinalidad, idfuncion, idSubfuncion, upper(nomSubFuncion) nombre, 1 usrAlta, getdate() fAlta, 'ACTIVO' estatus from sia_ctapublicabase group by idFinalidad, idfuncion,idSubfuncion, nomSubFuncion) a;";

				$dbQuery = $db->prepare($sql);
				$dbQuery->execute( array(':idCuenta' => $cuenta) );
				$estatus =  $estatus . " => Se cargó la información del Catálogo: SUBFUNCIONES. ";

				// Se inicia la carga de la tabla de Sectores

				$sql="DELETE FROM sia_sectores WHERE idCuenta = :idCuenta;";
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':idCuenta' => $cuenta));


				$sql = "INSERT INTO sia_sectores(idCuenta,idSector, nombre, usrAlta, fAlta, estatus) SELECT :idCuenta, cp.sector, s.nombre, 1, GETDATE(), 'ACTIVO' from (select distinct Sector from sia_CtaPublicaBase group by Sector) cp left join (select distinct idSector, nombre from sia_sectores group by idSector, nombre) s on s.idSector = cp.Sector;";

				$dbQuery = $db->prepare($sql);
				$dbQuery->execute( array(':idCuenta' => $cuenta) );
				$estatus =  $estatus . " => Se cargó la información del Catálogo: SECTORES. ";

				// Se inicia la carga de la tabla de Actividades

				$sql="DELETE FROM sia_actividades WHERE idCuenta = :idCuenta;";
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':idCuenta' => $cuenta));

				$sql = "INSERT INTO sia_actividades(idCuenta, idFinalidad, idFuncion,idSubfuncion, idActividad, nombre, usrAlta, fAlta, estatus)
					SELECT :idCuenta, a.idFinalidad, a.idfuncion, a.idSubfuncion, a.idActividad, a.nombre, a.usrAlta, a.fAlta, a.estatus 
					FROM (select distinct idFinalidad, idfuncion, idSubfuncion, idActividad, upper(nomActividad) nombre, 1 usrAlta, getdate() fAlta, 'ACTIVO' estatus from sia_ctapublicabase group by idFinalidad, idfuncion, idSubfuncion, idActividad, nomActividad) a;";

				$dbQuery = $db->prepare($sql);
				$dbQuery->execute( array(':idCuenta' => $cuenta) );
				$estatus =  $estatus . " => Se cargó la información del Catálogo: ACTIVIDADES. ";

				// Se inicia la carga de la tabla de Capitulos

				$sql="DELETE FROM sia_capitulos WHERE idCuenta = :idCuenta;";
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':idCuenta' => $cuenta));

				$sql = "INSERT INTO sia_capitulos (idCuenta, idCapitulo, nombre, usrAlta, fAlta, estatus) SELECT :idCuenta idCuenta, cap.capitulo idCapitulo, cap.nombre nombre , 1 usrAlta, GETDATE() fAlta, 'ACTIVO' estatus FROM (select distinct cp.capitulo, c.nombre from sia_CtaPublicaBase cp, sia_capitulos c where cp.capitulo = c.idCapitulo) cap;";

				$dbQuery = $db->prepare($sql);
				$dbQuery->execute( array(':idCuenta' => $cuenta) );
				$estatus =  $estatus . " => Se cargó la información del Catálogo: CAPITULOS. ";

				// Se inicia la carga de la tabla de Partidas

				$sql="DELETE FROM sia_partidas WHERE idCuenta = :idCuenta;";
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':idCuenta' => $cuenta));

				$sql = "INSERT INTO sia_partidas(idCuenta, idCapitulo, idPartida, nombre, usrAlta, fAlta, estatus)
					SELECT :idCuenta, CONCAT((substring(CAST(a.idPartida AS varchar),1,1)),'000') idCapitulo, a.idPartida, a.nombre, 1, GETDATE(), 'ACTIVO' FROM (SELECT distinct PTDA idPartida, DESCRIPCION_PARTIDA nombre from sia_CtaPublicaBase group by PTDA, DESCRIPCION_PARTIDA) a;";

				$dbQuery = $db->prepare($sql);
				$dbQuery->execute( array(':idCuenta' => $cuenta) );
				$estatus =  $estatus . " => Se cargó la información del Catálogo: PARTIDAS. ";

				// Se inicia la carga de la tabla de Subssectores

				$sql="DELETE FROM sia_subsectores WHERE idCuenta = :idCuenta;";
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':idCuenta' => $cuenta));


				$sql = "INSERT INTO sia_subsectores(idCuenta, idSector, idSubsector, nombre, usrAlta, fAlta, estatus) SELECT :idCuenta, cp.sector, cp.subsector, isnull(s.nombre,'SECTOR '+ cp.sector + '- SUBSECTOR ' + cp.subsector), 1, GETDATE(), 'ACTIVO' from (select distinct Sector, subSector from sia_CtaPublicaBase group by Sector, subsector) cp left join (select distinct idSector, idSubsector, nombre from sia_subsectores group by idSector, idSubsector, nombre) s on s.idSector = cp.Sector and s.idSubsector = cp.subsector;";

				$dbQuery = $db->prepare($sql);
				$dbQuery->execute( array(':idCuenta' => $cuenta) );
				$estatus =  $estatus . " => Se cargó la información del Catálogo: SUBSECTORES. ";

				//  Se inicia la carga de la tabla de Fuente de financiamiento

				$sql="DELETE FROM sia_FuentesFinanciamientos WHERE idCuenta = :idCuenta;";
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':idCuenta' => $cuenta));

				$sql = "INSERT INTO sia_FuentesFinanciamientos (idCuenta, idFuenteFinanciamiento, nombre, usrAlta) SELECT idCuenta, ff, fuente_financiamiento, 1 usrAlta FROM sia_CtaPublicaBase WHERE idCuenta = :idCuenta GROUP BY idCuenta,ff,fuente_financiamiento ORDER BY ff;";

				$dbQuery = $db->prepare($sql);
				$dbQuery->execute( array(':idCuenta' => $cuenta) );
				$estatus =  $estatus . " => Se cargó la información del Catálogo: FUENTES_FINANCIAMIENTO. ";

				//  Se inicia la carga de la tabla de Fuentes Genericas

				$sql="DELETE FROM sia_FuentesGenericas WHERE idCuenta = :idCuenta;";
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':idCuenta' => $cuenta));

				$sql = "INSERT INTO sia_FuentesGenericas (idCuenta, idFuenteFinanciamiento, idFuenteGenerica, nombre, usrAlta) SELECT idCuenta, ff, fg, fuente_generica, 1 usrAlta FROM sia_CtaPublicaBase WHERE idCuenta = :idCuenta GROUP BY idCuenta,ff,fg,fuente_generica ORDER BY ff,fg;";

				$dbQuery = $db->prepare($sql);
				$dbQuery->execute( array(':idCuenta' => $cuenta) );
				$estatus =  $estatus . " => Se cargó la información del Catálogo: FUENTES_GENERICAS.";

				//  Se inicia la carga de la tabla de Fuentes Especificas

				$sql="DELETE FROM sia_FuentesEspecificas WHERE idCuenta = :idCuenta;";
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':idCuenta' => $cuenta));

				$sql = "INSERT INTO sia_FuentesEspecificas (idCuenta, idFuenteFinanciamiento, idFuenteGenerica, idFuenteEspecifica, nombre, usrAlta) SELECT idCuenta, ff, fg, replace(fe,char(34),'') fe, fuente_especifica, 1 usrAlta FROM sia_CtaPublicaBase WHERE idCuenta = :idCuenta GROUP BY idCuenta,ff,fg,fe,fuente_especifica ORDER BY ff,fg,fe;";

				$dbQuery = $db->prepare($sql);
				$dbQuery->execute( array(':idCuenta' => $cuenta) );
				$estatus =  $estatus . " => Se cargó la información del Catálogo: FUENTES_ESPECIFICAS.";


				//  Se inicia la carga de la tabla de Origenes Recursos

				$sql="DELETE FROM sia_OrigenesRecursos WHERE idCuenta = :idCuenta;";
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':idCuenta' => $cuenta));

				$sql = "INSERT INTO sia_OrigenesRecursos (idCuenta, idOrigenRecurso, nombre, usrAlta) SELECT idCuenta, origen_rec, origen_recurso, 1 usrAlta FROM sia_CtaPublicaBase WHERE idCuenta = :idCuenta GROUP BY idCuenta, origen_rec, origen_recurso ORDER BY idCuenta, origen_rec;";

				$dbQuery = $db->prepare($sql);
				$dbQuery->execute( array(':idCuenta' => $cuenta) );
				$estatus =  $estatus . " => Se cargó la información del Catálogo: ORIGENES_RECURSOS.";

				//  Se inicia la carga de la tabla de Destino de Gasto

				$sql="DELETE FROM sia_DestinosGastos WHERE idCuenta = :idCuenta;";
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':idCuenta' => $cuenta));

				$sql = "INSERT INTO sia_DestinosGastos (idCuenta, idDestinoGasto, nombre, usrAlta) Select b.idCuenta, b.dg, b.nombre, 1 From (Select idCuenta, Case WHEN a.dg < 10 THEN '0' + cast(a.dg as varchar(5)) ELSE cast(a.dg as varchar(5)) END dg, a.nombre From ( select idCuenta, cast(replace(dg,char(34),'') as Integer) dg, Destino_Gasto nombre from sia_CtaPublicaBase where idCuenta = :idCuenta) a) b Group By b.idCuenta, b.dg, b.nombre order by b.dg;";

				$dbQuery = $db->prepare($sql);
				$dbQuery->execute( array(':idCuenta' => $cuenta) );
				$estatus =  $estatus . " => Se cargó la información del Catálogo: DESTINOS_GASTOS.";

				// Se inicia la carga de la tabla de Unidades

				$sql="DELETE FROM sia_unidades WHERE idCuenta = :idCuenta;";
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':idCuenta' => $cuenta));

				$sql = "INSERT INTO sia_unidades (idCuenta, idSector, idSubsector, idUnidad, nombre, usrAlta, fAlta, estatus, titular, idUnidadSector, idUnidadPoder, idUnidadClasificacion, siglas) select distinct :idCuenta idCuenta, cp.sector, cp.subsector, cp.unidad, cp.nombre, 1 usrAlta, getdate() fAlta, 'ACTIVO' estatus, u.titular , u.idUnidadSector, u.idUnidadPoder, u.idUnidadClasificacion, u.siglas  FROM (select distinct sector, subsector, unidad, urg nombre from sia_ctaPublicaBase group by sector, subsector, unidad, urg) cp left Join (SELECT idSector, idSubsector, idUnidad, nombre, titular, idUnidadSector, idUnidadPoder, idUnidadClasificacion, siglas from sia_unidades where idCuenta = 'CTA-' + convert(varchar, (convert(int, substring(:idCuenta1,5,4))-1))) u on cp.sector = u.idSector and cp.subsector = u.idSubsector and cp.unidad = u.idUnidad;";

				$dbQuery = $db->prepare($sql);
				$dbQuery->execute( array(':idCuenta' => $cuenta, ':idCuenta1' => $cuenta) );
				$estatus =  $estatus . " => Se cargó la información del Catálogo: UNIDADES. ";

				// Se inicia la carga de la tabla sia_CtaPublicaBase a la tabla sia_cuentasdetalles

				$sql="DELETE FROM sia_cuentasdetalles WHERE idCuenta = :idCuenta;";
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':idCuenta' => $cuenta));


				$sql = "INSERT INTO sia_cuentasdetalles (idCuenta, sector, subsector, unidad, funcion, subfuncion, actividad, capitulo, partida, finalidad, progPres, fuenteFinanciamiento,fuenteGenerica, fuenteEspecifica, origenRecurso, tipoGasto, digito, proyecto, destinoGasto, original, modificado, ejercido, pagado, pendiente, usrAlta, fAlta, estatus, idEje,anioDocto) SELECT idCuenta, sector, subsector, unidad, idFuncion, idSubfuncion, idActividad, capitulo, ptda partida, idFinalidad, '' progpres, FF fuenteFinanciamiento, FG fuenteGenerica, REPLACE(FE,char(34),'') fuenteEspecifica, ORIGEN_REC origenRecurso, TG tipoGasto, DI digito, REPLACE(PROYECTO_INVERSION,char(34),'') proyecto, CASE WHEN LEN(REPLACE(DG,char(34),'')) = 1 THEN '0'+REPLACE(DG,char(34),'') ELSE REPLACE(convert(VARCHAR,DG),char(34),'') END destinoGasto, CONVERT(varchar(20), CAST(original AS decimal(20,2))) original, CONVERT(varchar(20), CAST(modificado AS decimal(20,2))) modificado, CONVERT(varchar(20), CAST(ejercido AS decimal(20,2))) ejercido, '0' pagado, '0' pendiente, 1 usrAlta, GETDATE() fAlta, 'ACTIVO' estatus, CONVERT(int, idEje) idEje, ad FROM sia_CtaPublicaBase WHERE idCuenta = :idCuenta ORDER BY sector, subsector, unidad, idFinalidad, idFuncion, idSubfuncion, idActividad, capitulo, partida;";

				$dbQuery = $db->prepare($sql);
				$dbQuery->execute( array(':idCuenta' => $cuenta) );
				$estatus =  $estatus . " => Se cargó la información a la base de datos de EGRESOS ";

			}
			
			//*/

			echo " => Se cargaron " . $nRegistros . " registro(s).";

		}catch (PDOException $e) {
			echo  "<br>¡Error en el TRY!: " . $e->getMessage();
			die();
		}
	});
	

	$app->get('/actualizaArchivosByCta/:idCuenta/:archivoOriginal/:archivoOriginalIngreso', function($idCuenta,$archivoOriginal,$archivoOriginalIngreso)    use($app, $db) {	
		try{
			$usrActual = $_SESSION["idUsuario"];
			
			$archivoOriginal        = str_replace('NULL','', $archivoOriginal);
			$archivoOriginalIngreso = str_replace('NULL','', $archivoOriginalIngreso);

			//Actualizar los nombres de los archivos de la cuenta pública
			$sql="UPDATE sia_cuentas SET archivoOriginal=:archivoOriginal, archivoOriginalIngreso=:archivoOriginalIngreso, usrModificacion=:usrModificacion, fModificacion=GETDATE() WHERE idCuenta=:idCuenta";

			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':archivoOriginal' => $archivoOriginal, ':archivoOriginalIngreso' => $archivoOriginalIngreso, ':usrModificacion' => $usrActual, ':idCuenta' => $idCuenta ));								
		}catch (PDOException $e) {
			echo  "<br>¡Error en el TRY!: " . $e->getMessage();
			die();
		}
	});
	
	$app->get('/normatividades',$autenticacionrole ,function()  use ($app, $db) {
		$cuenta = $_SESSION["idCuentaActual"];
		$sql="SELECT idNormatividad id, nombre, tipo, acceso, concat(fInicio, ' al ', fFin) vigencia, estatus   FROM sia_normatividades   WHERE idCuenta=:cuenta ORDER BY tipo, nombre ";
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':cuenta' => $cuenta));
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS.");
		}else{
			$app->render('normatividad.php', $result);
		}
	})->name('normatividades');


	//Guarda un papel
	$app->post('/guardar/normatividad', function()  use($app, $db) {
	
		try{
			$usrActual = $_SESSION["idUsuario"];
			$cuenta = $_SESSION["idCuentaActual"];
			
			$request=$app->request;
			$oper = $request->post('txtOperacion');
			$id = $request->post('txtID');
			$nombre = strtoupper($request->post('txtNombre'));
			$tipo = $request->post('txtTipo');
			$acceso = $request->post('txtAcceso');
			
			$inicio = date_create($request->post('txtFechaInicial'));
			$inicio = $inicio->format('Y-m-d');
			
			$fin = date_create($request->post('txtFechaFinal'));
			$fin = $fin->format('Y-m-d');
			
			$estatus = $request->post('txtEstatus');

			if($oper=='INS'){
				$sql="INSERT INTO sia_normatividades (idCuenta, nombre, tipo, acceso, fInicio, fFin, usrAlta, fAlta, estatus) " .
				"VALUES(:cuenta, :nombre, :tipo, :acceso, :inicio, :fin, :usrActual, getdate(), :estatus);";

				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':cuenta' => $cuenta, ':nombre' => $nombre, ':tipo' => $tipo, ':acceso' => $acceso, ':inicio' => $inicio, ':fin' => $fin, ':usrActual' => $usrActual, ':estatus' => $estatus ));
				
				//echo "<br>$sql <hr>INS 100%";
			}else{
				$sql="UPDATE sia_normatividades SET " .
				"idCuenta=:cuenta, nombre=:nombre, tipo=:tipo, acceso=:acceso, fInicio=:inicio, fFin=:fin, usrModificacion=:usrActual, fModificacion=getdate(), estatus=:estatus " .
				"WHERE idNormatividad=:id";

				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':cuenta' => $cuenta, ':nombre' => $nombre, ':tipo' => $tipo, ':acceso' => $acceso, ':inicio' => $inicio, ':fin' => $fin, ':usrActual' => $usrActual, ':estatus' => $estatus, ':id' => $id ));
				//echo "UPD 100%";
			}
			$app->redirect($app->urlFor('normatividades'));
			
		}catch (PDOException $e) {
			echo  "<br>¡Error en el TRY!: " . $e->getMessage();
			die();
		}
	});


	$app->get('/normatividad/:id', function($id)    use($app, $db) {
		$cuenta = $_SESSION["idCuentaActual"];
		$sql="SELECT idNormatividad id, tipo, acceso, nombre, fInicio, fFin,  estatus FROM sia_normatividades  Where idCuenta=:cuenta and idNormatividad=:id";
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':cuenta' => $cuenta, ':id' => $id));
		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});

$app->get('/rangos', $autenticacionrole,function()  use ($app, $db) {
		$sql="SELECT idRango id, descripcion, anio, token, siglas, concat(inicio, ' al ', fin) rango, siguiente, disponible, minimo, estatus   FROM sia_rangos ORDER BY descripcion ";
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute();
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS.");
		}else{
			$app->render('rangos.php', $result);
		}
	})->name('rangos');


	//Guarda un rango
	$app->post('/guardar/rango', function()  use($app, $db) {
	
		try{
			$usrActual = $_SESSION["idUsuario"];
			$cuenta = $_SESSION["idCuentaActual"];
			
			$request=$app->request;
			$oper = $request->post('txtOperacion');
			$id = $request->post('txtID');
			$descripcion = strtoupper($request->post('txtDescripcion'));			
			$anio = $request->post('txtAnio');
			$token = strtoupper($request->post('txtToken'));
			$siglas = strtoupper($request->post('txtSiglas'));
			$inicio = $request->post('txtInicio');
			$siguiente = $request->post('txtSiguiente');
			$fin = $request->post('txtFin');
			$minimo = $request->post('txtMinimo');
			$estatus = $request->post('txtEstatus');
			

			if($oper=='INS'){
				$disponible = $fin - $inicio +  1;
				$siguiente = $inicio;				
				$sql="INSERT INTO sia_rangos (descripcion, anio, token, siglas, inicio, fin, siguiente, disponible, minimo, usrAlta, fAlta, estatus) " .
				"VALUES(:descripcion, :anio, :token, :siglas, :inicio, :fin, :siguiente, :disponible, :minimo, :usrActual, getdate(), :estatus);";

				$dbQuery = $db->prepare($sql);
				
				$dbQuery->execute(array(':descripcion' => $descripcion, ':anio' => $anio, ':token' => $token, ':siglas' => $siglas, ':inicio' => $inicio, ':fin' => $fin, 
				 ':siguiente' => $siguiente,':disponible' => $disponible, ':minimo' => $minimo, ':usrActual' => $usrActual, ':estatus' => $estatus ));	


				//echo "SQL:<hr>" . $sql . "<hr> Token: " . $token;
				 
			}else{
				
				$disponible = $fin - $siguiente +  1;
				
				$sql="UPDATE sia_rangos SET " .
				"descripcion=:descripcion, anio=:anio, token=:token, siglas=:siglas, inicio=:inicio, fin=:fin, disponible=:disponible, minimo=:minimo, " . 
				"usrModificacion=:usrActual, fModificacion=getdate(), estatus=:estatus " .
				"WHERE idRango=:id";

				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':descripcion' => $descripcion, ':anio' => $anio, ':token' => $token, ':siglas' => $siglas, ':inicio' => $inicio, ':fin' => $fin, 
				':disponible' => $disponible, ':minimo' => $minimo, ':usrActual' => $usrActual, ':estatus' => $estatus, ':id' => $id ));
			}
			$app->redirect($app->urlFor('rangos'));
			
		}catch (PDOException $e) {
			echo  "<br>¡Error en el TRY!: " . $e->getMessage();
			die();
		}
	});

	$app->get('/rango/:id', function($id)    use($app, $db) {
		$cuenta = $_SESSION["idCuentaActual"];
		$sql="SELECT idRango id, descripcion, anio, token, siglas, inicio, fin, siguiente, disponible, minimo, estatus FROM sia_rangos  Where idRango=:id";
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':id' => $id));
		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});
	
 $app->get('/catUsuarios', $autenticacionrole ,function()   use($app, $db) {
  $dbQuery = $db->prepare("SELECT idUsuario id, isnull(idEmpleado,'') idEmpleado, concat(saludo, ' ', nombre, ' ', paterno, ' ', " ."materno) usuario, isnull(correo,'') correo, isnull(telefono,'') telefono," .
   "CASE WHEN tipo='CF' THEN 'ESTRUCTURA' WHEN tipo='TE' THEN 'EVENTUAL'  WHEN tipo='HS' THEN 'HONORARIOS' WHEN tipo='PR' THEN 'PROFIS' ELSE '' END AS tipo," . 
   "usuario cuenta,  estatus FROM sia_usuarios ORDER BY concat(nombre, ' ', paterno, ' ', materno);");  
  $dbQuery->execute();
  $result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
  if(!$result){
   $app->halt(404, "NO SE ENCONTRARON DATOS ");
  }else{
   $app->render('catUsuarios.php', $result);
  } 
 })->name('catUsuarios');

	
	
	
	//Guardar un USUARIO
	$app->post('/guardar/usuario', function()  use($app, $db) {
		$request=$app->request;
		$oper = $request->post('operacion');
		$id = $request->post('txtID');
		$tipo = $request->post('txtTipo');
		$empleado = strtoupper($request->post('txtEmpleado'));
		$saludo = strtoupper($request->post('txtSaludo'));
		$nombre = strtoupper($request->post('txtNombre'));
		$paterno = strtoupper($request->post('txtPaterno'));
		$materno = strtoupper($request->post('txtMaterno'));
		$usuario = $request->post('txtCorreo');
		
		$pwd = $request->post('txtPassword');
		$telefono = $request->post('txtTelefono');
		$estatus = $request->post('txtEstatus');
		$usrActual = $_SESSION["idUsuario"];
		
		if ($oper=='INS'){
			$sql = "INSERT INTO sia_usuarios (tipo,saludo, idEmpleado, nombre, paterno, materno, telefono, usuario, pwd, usrAlta, fAlta, estatus) ".
			"VALUES(:tipo, :saludo, :empleado, :nombre, :paterno, :materno,:telefono, :usuario, :pwd, :usrActual, now(), :estatus); ";		
			$dbQuery = $db->prepare($sql);	
			
			$dbQuery->execute(array(':tipo'=> $tipo, ':saludo'=> $saludo, ':empleado'=> $empleado, ':nombre'=> $nombre, ':paterno'=> $paterno, ':materno'=> $materno, ':telefono'=> $telefono,
			 ':usuario'=> $usuario,  ':pwd'=> $pwd, ':usrActual'=> $usrActual, ':estatus'=> $estatus));		
			 
		}else{
			$sql = "UPDATE sia_usuarios " . 
			"SET tipo=:tipo, saludo=:saludo, idEmpleado=:empleado, nombre=:nombre, paterno=:paterno, materno=:materno, telefono=:telefono, usuario=:usuario, pwd=:pwd, " . 
			"usrModificacion=:usrModificacion, fModificacion= now(), estatus=:estatus ".
			"WHERE idUsuario=:id";		
			$dbQuery = $db->prepare($sql);		
			$dbQuery->execute(array(':tipo'=> $tipo, ':saludo'=> $saludo, ':empleado'=> $empleado, ':nombre'=> $nombre,':paterno'=> $paterno, ':materno'=> $materno, 
			':telefono'=> $telefono, ':usuario'=> $usuario,':pwd'=> $pwd, ':usrModificacion'=> $usrActual,':estatus' => $estatus, ':id' => $id));				
		}
		$app->redirect($app->urlFor('catUsuarios'));
	});	
	
	//Obtener un usuario
	$app->get('/usuario/:id',  function($id)  use($app, $db) {
		$id = (int)$id;
		$dbQuery = $db->prepare("SELECT idUsuario id, saludo, isnull(idEmpleado, '') idEmpleado, nombre, tipo, paterno, materno, isnull(telefono, '') telefono, usuario, pwd,  estatus FROM sia_usuarios WHERE idUsuario=:id ");		
		$dbQuery->execute(array(':id' => $id));
		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "RECURSO NO ENCONTRADA.");			
		}else{		
			echo json_encode($result);
		}		
	});
	

	
	
$app->get('/lstRoles', function()   use($app, $db) {
		$dbQuery = $db->prepare("Select idRol id, nombre texto From sia_roles order by nombre  ");		
		$dbQuery->execute();
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		echo json_encode($result);	
	});	
	
$app->get('/lstApartados', function()   use($app, $db) {
		$dbQuery = $db->prepare("Select idApartado id, nombre texto From sia_apartados order by nombre  ");		
		$dbQuery->execute();
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		echo json_encode($result);	
	});		
	
	
	// Obtener centro gestor
$app->get('/lstUnidadesByArea/:area', function($area)   use($app, $db) {
	$cuenta = $_SESSION["idCuentaActual"];
	
	$sql="Select concat(u.idSector, '-', u.idSubsector, '-', u.idUnidad) id, u.nombre texto  " .
	"From sia_unidades u inner join sia_areasunidades au on u.idCuenta = au.idCuenta and au.idSector = u.idSector and u.idSubsector = au.idSubsector and au.idUnidad = u.idUnidad ".
	"Where u.idCuenta =:cuenta and au.idArea=:area";

		$dbQuery = $db->prepare($sql);		
		$dbQuery->execute(array(':cuenta' => $cuenta, ':area' => $area));
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		echo json_encode($result);	
	});

	// Obtener centro gestor
$app->get('/lstObjetosByCentro/:area', function($area)   use($app, $db) {
	$cuenta = $_SESSION["idCuentaActual"];
	
	$sql="Select concat(u.idSector, '-', u.idSubsector, '-', u.idUnidad) id, u.nombre texto  " .
	"From sia_unidades u inner join sia_areasunidades au on u.idCuenta = au.idCuenta and au.idSector = u.idSector and u.idSubsector = au.idSubsector and au.idUnidad = u.idUnidad ".
	"Where u.idCuenta =:cuenta and au.idArea=:area";

		$dbQuery = $db->prepare($sql);		
		$dbQuery->execute(array(':cuenta' => $cuenta, ':area' => $area));
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		echo json_encode($result);	
	});
	
	
		//Listar auditorias by area responsable
	$app->get('/lstAuditoriasByArea/:area', function($area)  use($app, $db) {
		$cuenta = $_SESSION["idCuentaActual"];
		$global = $_SESSION["usrGlobal"];
		
		if ($global=="SI"){
			$sql="SELECT idAuditoria id, concat(COALESCE(clave, concat('PROY-',idAuditoria)), ' ', tipoAuditoria) texto FROM sia_auditorias Where idCuenta=:cuenta order by 2 asc";
			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':cuenta' => $cuenta));
		}else{
			$sql="SELECT idAuditoria id, concat(COALESCE(clave, concat('PROY-',idAuditoria)), ' ', tipoAuditoria) texto FROM sia_auditorias Where idCuenta=:cuenta and idArea=:area order by 2 asc";
			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':cuenta' => $cuenta, ':area' => $area));		
		}		
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		echo json_encode($result);
	});
	
	
//Listar auditorias by area responsable y auditor
	$app->get('/lstAuditoriasByAreaAuditor/:area', function($area)  use($app, $db) {
		$cuenta = $_SESSION["idCuentaActual"];
		//$area = $_SESSION["idArea"];
		$global = $_SESSION["usrGlobal"];
		$globalArea = $_SESSION["usrGlobalArea"];
		$rpe = $_SESSION["idEmpleado"];
		
		if ($global=="SI"){
			$sql="SELECT a.idAuditoria id, concat(COALESCE(clave, convert(varchar,a.idAuditoria)), ' ', u.nombre,  ' / TIPO:', ta.nombre) texto  " .
			"FROM sia_auditorias a " .
			"LEFT JOIN sia_unidades u ON a.idCuenta=u.idCuenta and a.idSector=u.idSector and a.idSubsector=u.idSubsector and a.idUnidad=u.idUnidad " .
			//"INNER JOIN  sia_auditoriasauditores aa ON a.idCuenta=aa.idCuenta and a.idAuditoria=aa.idAuditoria " .
			"INNER JOIN sia_tiposauditoria ta on a.tipoAuditoria=ta.idTipoAuditoria " .
			"WHERE a.idCuenta=:cuenta AND a.clave is not null order by 2 asc ";

			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':cuenta' => $cuenta));
		}else{

			if ($globalArea=="SI"){
				$sql=" SELECT a.idAuditoria id, concat(COALESCE(clave, convert(varchar,a.idAuditoria)), ' ', u.nombre,  ' / TIPO:', ta.nombre) texto " .
				"FROM sia_auditorias a " .
				"LEFT JOIN sia_unidades u ON a.idCuenta=u.idCuenta and a.idSector=u.idSector and a.idSubsector=u.idSubsector and a.idUnidad=u.idUnidad " .
				"INNER JOIN sia_tiposauditoria ta on a.tipoAuditoria=ta.idTipoAuditoria " .
				"WHERE a.idCuenta=:cuenta and a.idArea=:area AND a.clave is not null order by id asc ";
				
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':cuenta' => $cuenta, ':area' => $area));	
			}else{

				$sql="SELECT a.idAuditoria id, concat(COALESCE(clave, convert(varchar,a.idAuditoria)), ' ', u.nombre,  ' / TIPO: ', ta.nombre) texto  " .
				"FROM sia_auditorias a " .
				"LEFT JOIN sia_unidades u ON a.idCuenta=u.idCuenta and a.idSector=u.idSector and a.idSubsector=u.idSubsector and a.idUnidad=u.idUnidad " .
				"INNER JOIN  sia_auditoriasauditores aa ON a.idCuenta=aa.idCuenta and a.idAuditoria=aa.idAuditoria " .
				"INNER JOIN sia_tiposauditoria ta on a.tipoAuditoria=ta.idTipoAuditoria " .
				"WHERE a.idCuenta=:cuenta and a.idArea=:area and aa.idAuditor=:auditor AND a.clave is not null order by 2 asc ";
					
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':cuenta' => $cuenta, ':area' => $area, ':auditor' => $rpe));		
			}		
		}	
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		echo json_encode($result);
	});	
	
	
	
$app->get('/cedulas', $autenticacionrole ,function()  use ($app, $db) {
		$cuenta = $_SESSION["idCuentaActual"];
		$area = $_SESSION["idArea"];
		$auditor = $_SESSION["idEmpleado"];
		
		$usrActual = $_SESSION["idUsuario"];
		
		$global = $_SESSION["usrGlobal"];		
		
		$globalArea = $_SESSION["usrGlobalArea"];
		
		if ($global=="SI"){
			$sql="Select a.idAuditoria, COALESCE(clave, convert(varchar,a.idAuditoria)) claveAuditoria, dbo.lstSujetosByAuditoria(a.idAuditoria) sujeto, dbo.lstObjetosByAuditoria(a.idAuditoria) objeto, ta.nombre tipoAuditoria, p.idPapel, tp.nombre sPapel, p.fPapel, " . 
			"p.idFase, p.tipoResultado, p.archivoOriginal, p.archivoFinal,  p.estatus " .
			"FROM sia_papeles p  " .
			"inner join  sia_auditorias a on p.idCuenta=a.idCuenta and p.idPrograma = a.idPrograma and p.idAuditoria = a.idAuditoria " .
			"inner join sia_tipospapeles tp on p.tipoPapel=tp.idTipoPapel " .
			"inner join sia_tiposauditoria ta on a.tipoAuditoria=ta.idTipoAuditoria " .
			"Where a.idCuenta=:cuenta " .
			"and p.estatus='ACTIVO' and a.idEtapa in (Select idEtapa from sia_rolesetapas re inner join sia_usuariosroles ur on ur.idRol = re.idRol  Where ur.idUsuario=:usrActual) " . 
			"Order by p.fAlta desc ";
			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':cuenta' => $cuenta, ':usrActual' => $usrActual));
		}else{
		
			if ($globalArea=="SI"){
				$sql="Select a.idAuditoria, COALESCE(clave, convert(varchar,a.idAuditoria)) claveAuditoria, dbo.lstSujetosByAuditoria(a.idAuditoria) sujeto, dbo.lstObjetosByAuditoria(a.idAuditoria) objeto, ta.nombre tipoAuditoria, p.idPapel, tp.nombre sPapel, p.fPapel, " . 
				"p.idFase, p.tipoResultado, p.archivoOriginal, p.archivoFinal,  p.estatus " .
				"FROM sia_papeles p  " .
				"inner join  sia_auditorias a on p.idCuenta=a.idCuenta and p.idPrograma = a.idPrograma and p.idAuditoria = a.idAuditoria " .
				"inner join sia_tipospapeles tp on p.tipoPapel=tp.idTipoPapel " .
				"inner join sia_tiposauditoria ta on a.tipoAuditoria=ta.idTipoAuditoria " .
				"Where a.idCuenta=:cuenta  and a.idArea=:area and p.estatus='ACTIVO' " . 
				"Order by p.fAlta desc ";
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':cuenta' => $cuenta, ':area' => $area));	
			}else{
				$sql="Select a.idAuditoria, COALESCE(clave, convert(varchar,a.idAuditoria)) claveAuditoria, dbo.lstSujetosByAuditoria(a.idAuditoria) sujeto, dbo.lstObjetosByAuditoria(a.idAuditoria) objeto, ta.nombre tipoAuditoria, p.idPapel, tp.nombre sPapel, p.fPapel, " . 
				"p.idFase, p.tipoResultado, p.archivoOriginal, p.archivoFinal,  p.estatus " .
				"FROM sia_papeles p  " .
				"inner join  sia_auditorias a on p.idCuenta=a.idCuenta and p.idPrograma = a.idPrograma and p.idAuditoria = a.idAuditoria " .
				"inner join  sia_auditoriasauditores aa on a.idAuditoria=aa.idAuditoria " .
				"inner join sia_tipospapeles tp on p.tipoPapel=tp.idTipoPapel " .
				"inner join sia_tiposauditoria ta on a.tipoAuditoria=ta.idTipoAuditoria " .
				"Where a.idCuenta=:cuenta  and aa.idAuditor=:auditor " . 
				"and p.estatus='ACTIVO' and a.idEtapa in (Select idEtapa from sia_rolesetapas re inner join sia_usuariosroles ur on ur.idRol = re.idRol  Where ur.idUsuario=:usrActual) " . 
				"Order by p.fAlta desc ";
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':cuenta' => $cuenta, ':auditor' => $auditor, ':usrActual' => $usrActual));	
			}
		
	
		}
		//$dbQuery->execute(array(':cuenta' => $cuenta, ':area' => $area));
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		$app->render('papeles.php', $result);
	})->name('cedulas');	
	
	
$app->get('/lstPapeles', function()    use($app, $db) {
		$sql="SELECT tp.idTipoPapel id, tp.nombre texto FROM sia_papelesfases pf INNER JOIN sia_tipospapeles tp on pf.idTipoPapel= tp.idTipoPapel ORDER BY tp.nombre";
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array());
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});

	$app->get('/lstPapelesByFase/:id', function($id)    use($app, $db) {
		$area = $_SESSION["idArea"];
		$sql="SELECT tp.idTipoPapel id, tp.nombre texto 
			  FROM sia_papelesfases pf 
  			  INNER JOIN sia_tipospapeles tp on pf.idTipoPapel= tp.idTipoPapel 
  			  INNER JOIN sia_areastipospapeles atp on tp.idTipoPapel=atp.idTipoPapel
  			  WHERE atp.idArea=:area and pf.idFase=:id and tp.estatus='ACTIVO' ORDER BY tp.nombre";
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':id' => $id,':area' => $area));
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});
	

	$app->get('/papel/:id', function($id)    use($app, $db) {
		$sql="SELECT p.idPapel, p.idAuditoria, COALESCE(clave, concat('Proy-Aud-',a.idAuditoria)) claveAuditoria, p.idFase, p.tipoPapel, p.fPapel, tp.programada, " .
		"p.tipoResultado, p.resultado, p.archivoOriginal, p.archivoFinal, a.idProceso proceso, a.idEtapa etapa,p.estatus, tp.nomenclatura nomen,aa.idAuditor lider,us.idUsuario usuario " . 
		"FROM sia_papeles p inner join sia_auditorias a on a.idCuenta = p.idCuenta AND a.idPrograma = p.idPrograma AND a.idAuditoria = p.idAuditoria " .
		"inner join sia_tipospapeles tp on p.tipoPapel=tp.idTipoPapel " .
		"inner join sia_auditoriasauditores aa  on p.idAuditoria=aa.idAuditoria AND aa.lider='SI' " .
		"inner join sia_usuarios us on aa.idAuditor=us.idEmpleado " .
		"WHERE p.idPapel=:id";
		
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':id' => $id));
		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);
		echo json_encode($result);		
	});
			
	
		
	
//Guarda un papel
	$app->post('/guardar/papel', function()  use($app, $db) {
		try{
			$usrActual = $_SESSION["idUsuario"];
			$request=$app->request;
			$id = $request->post('txtID');
			$oper = $request->post('txtOperacion');
			$cuenta = $request->post('txtCuenta');
			$programa = $request->post('txtPrograma');
			$auditoria = $request->post('txtAuditoria');
			$fase = $request->post('txtFase');
			$tipoPapel = $request->post('txtTipoPapel');
			
			$fPapel = date_create($request->post('txtFechaPapel'));
			$fPapel = $fPapel->format('Y-m-d');
			
			$tipoResultado = $request->post('txtTipoRes');
			$resultado = strtoupper($request->post('txtResultado'));
			
			$original = $request->post('txtArchivoOriginal');
			$final = $request->post('txtArchivoFinal');

			if($oper=='INS'){
				$sql="INSERT INTO sia_papeles (idCuenta, idPrograma, idAuditoria, idFase, tipoPapel, fPapel, tipoResultado, resultado, archivoOriginal, archivoFinal, usrAlta, fAlta, estatus) " .
				"VALUES(:cuenta, :programa, :auditoria, :fase, :tipoPapel, :fPapel, :tipoResultado, :resultado, :original, :final, :usrActual, getdate(), 'ACTIVO');";

				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':cuenta' => $cuenta, ':programa' => $programa, ':auditoria' => $auditoria, ':fase' => $fase, 
				':tipoPapel' => $tipoPapel, ':fPapel' => $fPapel, ':tipoResultado' => $tipoResultado,':resultado' => $resultado, ':original' => $original, ':final' => $final, ':usrActual' => $usrActual ));				
			}else{
				$sql="UPDATE sia_papeles SET " .
				"idFase=:fase, tipoPapel=:tipoPapel, fPapel=:fPapel, tipoResultado=:tipoResultado, resultado=:resultado, usrModificacion=:usrActual, fModificacion=getdate() " .
				"WHERE idPapel=:id";

				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':fase' => $fase, ':tipoPapel' => $tipoPapel, ':fPapel' => $fPapel,':tipoResultado' => $tipoResultado,':resultado' => $resultado, ':usrActual' => $usrActual, ':id' => $id  ));
			}
			//echo "SQL=<br>$sql<br><hr> id: " . $id . " fase: " . $fase . " tipoPapel: " . $tipoPapel . " fPapel: " . $fPapel . " tipoResultado: " . $tipoResultado . " resultado: " . $resultado . " usrActual: " . $usrActual;
			$app->redirect($app->urlFor('cedulas'));
		}catch (PDOException $e) {
			echo  "<br>Error de BD: " . $e->getMessage();
			die();
		}		
	});


	

///////////////////////////////////A C O P I O S/////////////////////////////////////////////////////////
		
	//Lista de acopios
	$app->get('/acopios',$autenticacionrole ,function() use($app, $db){
		$cuenta = $_SESSION["idCuentaActual"];
		$area = $_SESSION["idArea"];
		$auditor = $_SESSION["idEmpleado"];
		$usrActual = $_SESSION["idUsuario"];
		$global = $_SESSION["usrGlobal"];
		
		$globalArea = $_SESSION["usrGlobalArea"];
		
		if ($global=="SI"){		
			$sql="SELECT a.idAuditoria,  COALESCE(clave, convert(varchar, a.idAuditoria)) claveAuditoria, dbo.lstSujetosByAuditoria(a.idAuditoria) sujeto, dbo.lstObjetosByAuditoria(a.idAuditoria) objeto, ta.nombre tipoAuditoria, ac.idAcopio, ac.idClasificacion, ac.idFase, ac.archivoFinal, ac.archivoOriginal, ac.estatus " .
			"FROM sia_acopio ac  " .
			"inner join  sia_auditorias a on ac.idCuenta=a.idCuenta and ac.idPrograma = a.idPrograma and ac.idAuditoria = a.idAuditoria " .
			"inner join sia_tiposauditoria ta on a.tipoAuditoria=ta.idTipoAuditoria " .			
			"WHERE a.idCuenta=:cuenta and ac.estatus='ACTIVO' Order by ac.fAlta desc ";
			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':cuenta' => $cuenta));
		}else{		
			if ($globalArea=="SI"){
				$sql="SELECT a.idAuditoria,  COALESCE(clave, convert(varchar, a.idAuditoria)) claveAuditoria, dbo.lstSujetosByAuditoria(a.idAuditoria) sujeto, dbo.lstObjetosByAuditoria(a.idAuditoria) objeto, a.tipoAuditoria, ac.idAcopio, ac.idClasificacion, ac.idFase, ac.archivoFinal, ac.archivoOriginal, ac.estatus " .
				"FROM sia_acopio ac  " .
				"inner join  sia_auditorias a on ac.idCuenta=a.idCuenta and ac.idPrograma = a.idPrograma and ac.idAuditoria = a.idAuditoria " .
				"WHERE a.idCuenta=:cuenta  and a.idArea=:area and ac.estatus='ACTIVO' Order by ac.fAlta desc ";
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':cuenta' => $cuenta, ':area' => $area));			
			}else{
				$sql="SELECT a.idAuditoria,  COALESCE(clave, convert(varchar, a.idAuditoria)) claveAuditoria, dbo.lstSujetosByAuditoria(a.idAuditoria) sujeto, dbo.lstObjetosByAuditoria(a.idAuditoria) objeto, a.tipoAuditoria, ac.idAcopio, ac.idClasificacion, ac.idFase, ac.archivoFinal, ac.archivoOriginal, ac.estatus " .
				"FROM sia_acopio ac  " .
				"inner join  sia_auditorias a on ac.idCuenta=a.idCuenta and ac.idPrograma = a.idPrograma and ac.idAuditoria = a.idAuditoria " .
				"inner join  sia_auditoriasauditores aa on a.idAuditoria=aa.idAuditoria " .
				"WHERE a.idCuenta=:cuenta  and aa.idAuditor=:auditor and ac.estatus='ACTIVO' Order by ac.fAlta desc ";
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':cuenta' => $cuenta, ':auditor' => $auditor));
			
			}
		}
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		$app->render('acopio.php', $result);
	})->name('acopios');
	
//Guarda un acopio
	$app->post('/guardar/acopio', function()  use($app, $db) {
		try{
			$usrActual = $_SESSION["idUsuario"];
			$request=$app->request;
			$id = $request->post('txtID');
			$oper = $request->post('txtOperacion');
			$cuenta = $request->post('txtCuenta');
			$programa = $request->post('txtPrograma');
			$auditoria = $request->post('txtAuditoria');
			$fase = $request->post('txtFase');
			$clasificacion = $request->post('txtClasificacion');
			$observaciones = strtoupper($request->post('txtObservaciones'));
			$original = $request->post('txtArchivoOriginal');
			$final = $request->post('txtArchivoFinal');
			$est = $request->post('txtEstatusAcopio');

			if($oper=='INS'){
				$sql="INSERT INTO sia_acopio (idCuenta, idPrograma, idAuditoria, idFase, idClasificacion, observaciones, archivoOriginal, archivoFinal, usrAlta, fAlta, estatus) " .
				"VALUES(:cuenta, :programa, :auditoria, :fase, :clasificacion, :observaciones, :original, :final, :usrActual, getdate(), 'ACTIVO');";

				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':cuenta' => $cuenta, ':programa' => $programa, ':auditoria' => $auditoria, ':fase' => $fase, ':clasificacion' => $clasificacion, ':observaciones' => $observaciones, 
				':original' => $original, ':final' => $final, ':usrActual' => $usrActual ));				
			}else{
				if($est=='INACTIVO'){
					$sql="UPDATE sia_acopio SET usrModificacion=:usrActual, fModificacion=getdate(),estatus=:est " .
						"WHERE idAcopio=:id";

					$dbQuery = $db->prepare($sql);
					$dbQuery->execute(array(':usrActual' => $usrActual,':est' => $est, ':id' => $id  ));

				}else{
					$sql="UPDATE sia_acopio SET idFase=:fase, idClasificacion=:clasificacion, observaciones=:observaciones, archivoOriginal=:original, archivoFinal=:final, usrModificacion=:usrActual, fModificacion=getdate() " .
					"WHERE idAcopio=:id";

					$dbQuery = $db->prepare($sql);
					$dbQuery->execute(array(':fase' => $fase, ':clasificacion' => $clasificacion, ':observaciones' => $observaciones, ':original' => $original, ':final' => $final, ':usrActual' => $usrActual, ':id' => $id  ));
				}
			}
			//echo "SQL=<br>$sql<br><hr> id: " . $id . " Cuenta: " . $cuenta .  " Programa: " . $programa .  " Auditoria: " . $auditoria ." fase: " . $fase . " clasificacion: " . $clasificacion . " observaciones: " . $observaciones . " usrActual: " . $usrActual;
			$app->redirect($app->urlFor('acopios'));
		}catch (PDOException $e) {
			echo  "<br>Error de BD: " . $e->getMessage();
			die();
		}
	});
	
	//Obtener un acopio
	$app->get('/acopioByid/:id', function($id)    use($app, $db) {
		$sql="SELECT idAcopio, idAuditoria, idFase, idClasificacion, observaciones, archivoOriginal, archivoFinal,estatus FROM sia_acopio WHERE idAcopio=:id";
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':id' => $id));
		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);
		echo json_encode($result);		
	});
	
/*	
	$app->get('/acopio/:id', function($id)    use($app, $db) {
		$sql="SELECT idAcopio, idAuditoria, idFase, idClasificacion, observaciones, archivoOriginal, archivoFinal FROM sia_acopio WHERE idAcopio=:id";
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':id' => $id));
		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);
		echo json_encode($result);		
	});
*/

///////////////////////////////////A V A N C E S/////////////////////////////////////////////////////////
		
	//Lista de avances
	$app->get('/avanceActividad', $autenticacionrole,function() use($app, $db){
		$cuenta = $_SESSION["idCuentaActual"];
		$area = $_SESSION["idArea"];
		$global = $_SESSION["usrGlobal"];
		
		if ($global=="SI"){			
			$sql="SELECT a.idAuditoria, COALESCE(clave, convert(varchar,a.idAuditoria)) claveAuditoria, dbo.lstSujetosByAuditoria(a.idAuditoria) sujeto, ta.nombre tipoAuditoria, concat(e.nombre, ' ', e.paterno, ' ', e.materno) auditor, aa.idAvance, f.nombre fase, concat(left(aact.actividad, 100), '...') actividad, aa.porcentaje  " .
			"FROM sia_auditoriasavances aa " .
				"inner join sia_auditorias a  on aa.idCuenta=a.idCuenta and aa.idPrograma = a.idPrograma and aa.idAuditoria = a.idAuditoria " .
				"left join sia_empleados e on aa.idAuditor=e.idEmpleado " .
				"inner join sia_fases f on f.idFase=aa.idFase " .
				"inner join sia_auditoriasactividades aact  on aa.idActividad = aact.idActividad " .
				"inner join sia_tiposauditoria ta on a.tipoAuditoria=ta.idTipoAuditoria " .
			"Where a.idCuenta=:cuenta " .
			"order by aa.idAvance desc";

			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':cuenta' => $cuenta));
		}else{
			$sql="SELECT a.idAuditoria, COALESCE(clave, convert(varchar,a.idAuditoria)) claveAuditoria, dbo.lstSujetosByAuditoria(a.idAuditoria) sujeto, ta.nombre tipoAuditoria, concat(e.nombre, ' ', e.paterno, ' ', e.materno) auditor, aa.idAvance, f.nombre fase, concat(left(aact.actividad, 100), '...') actividad, aa.porcentaje  " .
			"FROM sia_auditoriasavances aa " .
				"inner join sia_auditorias a  on aa.idCuenta=a.idCuenta and aa.idPrograma = a.idPrograma and aa.idAuditoria = a.idAuditoria " .
				"left join sia_empleados e on aa.idAuditor=e.idEmpleado " .
				"inner join sia_fases f on f.idFase=aa.idFase " .
				"inner join sia_auditoriasactividades aact  on aa.idActividad = aact.idActividad " .
				"inner join sia_tiposauditoria ta on a.tipoAuditoria=ta.idTipoAuditoria " .
			"Where a.idCuenta=:cuenta and a.idArea=:area " .
			"order by aa.idAvance desc";

			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':cuenta' => $cuenta, ':area' => $area));		
		}
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		$app->render('avanceActividad.php', $result);
	})->name('avanceActividad');
	
//Guarda un avance
	$app->post('/guardar/avance', function()  use($app, $db) {
		try{
			$usrActual = $_SESSION["idUsuario"];
			$request=$app->request;
			$id = $request->post('txtID');
			$oper = $request->post('txtOperacion');
			$cuenta = $request->post('txtCuenta');
			$programa = $request->post('txtPrograma');
			$auditoria = $request->post('txtAuditoria');
			$fase = $request->post('txtFase');
			$actividad = $request->post('txtActividad');
			$porcentaje = $request->post('txtPorcentaje');
			$auditor = $request->post('txtAuditor');
			
			$apartado = $request->post('txtApartado');
			
			$inicio = date_create($request->post('txtFechaInicio'));
			$inicio = $inicio->format('d-m-Y');
			
			
			$fin = date_create($request->post('txtFechaFin'));
			$fin = $fin->format('d-m-Y');
			
			
			
			
			$obs = $request->post('txtObservaciones');

			if($oper=='INS'){
				$sql="INSERT INTO sia_auditoriasavances (idCuenta, idPrograma, idAuditoria, idFase, idActividad, porcentaje, idAuditor, idApartado, fInicio, fFin, observaciones, usrAlta, fAlta, estatus) " .
				"VALUES(:cuenta, :programa, :auditoria, :fase, :actividad, :porcentaje, :auditor, :apartado, :inicio, :fin, :obs,:usrActual, getdate(), 'ACTIVO');";

				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':cuenta' => $cuenta, ':programa' => $programa, ':auditoria' => $auditoria, ':fase' => $fase, 
				':actividad' => $actividad, ':porcentaje' => $porcentaje, ':auditor' => $auditor, ':apartado' => $apartado, ':inicio' => $inicio, ':fin' => $fin, ':obs' => $obs,':usrActual' => $usrActual ));										
			}else{
				$sql="UPDATE sia_auditoriasavances SET idFase=:fase, idActividad=:actividad, porcentaje=:porcentaje, idAuditor=:auditor, idApartado=:apartado, fInicio=:inicio, fFin=:fin, observaciones=:obs,   
				usrModificacion=:usrActual, fModificacion=getdate() " .
				"WHERE idAvance=:id";

				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':fase' => $fase, ':actividad' => $actividad, ':porcentaje' => $porcentaje, ':auditor' => $auditor,':apartado' => $apartado, 
				':inicio' => $inicio, ':fin' => $fin, ':obs' => $obs, ':usrActual' => $usrActual, ':id' => $id  ));
			}
			
			//echo "SQL=<br>$sql<br><br><hr><br><br> id:  $id  Cuenta:  $cuenta  Programa:  $programa  Auditoria:  $auditoria fase:  $fase  actividad:$actividad  porcentaje:$porcentaje idAuditor=$auditor Apartado= $apartado Inicio= $inicio Fin= $fin  Obs=$obs  usrActual= $usrActual";			
			$app->redirect($app->urlFor('avanceActividad'));
		}catch (PDOException $e) {
			echo  "<br>Error de BD: " . $e->getMessage();
			die();
		}
	});
	
	//Obtener un avance
	$app->get('/avanceById/:id', function($id)    use($app, $db) {
		$sql="SELECT idAvance, idAuditoria, idFase, idActividad, porcentaje, idAuditor, estatus FROM sia_auditoriasavances WHERE idAvance=:id";
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':id' => $id));
		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);
		echo json_encode($result);		
	});
	
	
	///////////////////////////////////consolidados/////////////////////////////////////////////////////////
		
	//Lista de Consolidados
	$app->get('/consolidados',$autenticacionrole ,function() use($app, $db){
		$cuenta = $_SESSION["idCuentaActual"];
		$area = $_SESSION["idArea"];
		
		$usrActual = $_SESSION["idUsuario"];
		$global = $_SESSION["usrGlobal"];
		
		if ($global=="SI"){		
			$sql="Select tc.idTipoConsolidado tipo, tc.nombre sTipoConsolidado, c.idConsolidado docto, c.nombre sDocto, concat(u.idSector, u.idSubsector,u.idUnidad) gestor, concat(u.idSector, u.idSubsector,u.idUnidad, ' ', u.nombre)  sGestor, count(*) cantidad " .
			"FROM sia_consolidadosdetalles cd INNER JOIN sia_unidades u ON cd.idCuenta = u.idCuenta AND cd.idSector = u.idSector and cd.idSubsector = u.idSubsector and cd.idUnidad=u.idUnidad " .
			"INNER JOIN sia_consolidados c on cd.idConsolidado = c.idConsolidado " .
			"INNER JOIN sia_tiposconsolidados tc  ON tc.idTipoConsolidado = c.idTipoConsolidado " .
			"INNER JOIN sia_areasunidades au ON cd.idCuenta = au.idCuenta and cd.idSector = au.idSector and cd.idSubsector=au.idSubsector and cd.idUnidad = au.idUnidad " .
			"Where cd.idCuenta=:cuenta and au.idArea=:area " .
			"Group by tc.idTipoConsolidado, tc.nombre, c.idConsolidado, c.nombre, concat(u.idSector, u.idSubsector,u.idUnidad), concat(u.idSector, u.idSubsector, u.idUnidad, ' ', u.nombre) ";
			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':cuenta' => $cuenta));
		}else{
			$sql="Select tc.idTipoConsolidado tipo, tc.nombre sTipoConsolidado, c.idConsolidado docto, c.nombre sDocto, concat(u.idSector, u.idSubsector,u.idUnidad) gestor, concat(u.idSector, u.idSubsector,u.idUnidad, ' ', u.nombre)  sGestor, count(*) cantidad " .
			"FROM sia_consolidadosdetalles cd INNER JOIN sia_unidades u ON cd.idCuenta = u.idCuenta AND cd.idSector = u.idSector and cd.idSubsector = u.idSubsector and cd.idUnidad=u.idUnidad " .
			"INNER JOIN sia_consolidados c on cd.idConsolidado = c.idConsolidado " .
			"INNER JOIN sia_tiposconsolidados tc  ON tc.idTipoConsolidado = c.idTipoConsolidado " .
			"INNER JOIN sia_areasunidades au ON cd.idCuenta = au.idCuenta and cd.idSector = au.idSector and cd.idSubsector=au.idSubsector and cd.idUnidad = au.idUnidad " .
			"Where cd.idCuenta=:cuenta and au.idArea=:area " .
			"Group by tc.idTipoConsolidado, tc.nombre, c.idConsolidado, c.nombre, concat(u.idSector, u.idSubsector,u.idUnidad), concat(u.idSector, u.idSubsector, u.idUnidad, ' ', u.nombre) ";
			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':cuenta' => $cuenta, ':area' => $area));
		
		}
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		$app->render('catConsolidados.php', $result);
	})->name('consolidados');
	
//Guarda un consolidado
	$app->get('/guardar/consolidado/:oper/:cadena', function($oper, $cadena)  use($app, $db) {
		try{
			$usrActual = $_SESSION["idUsuario"];
			$cuenta = $_SESSION["idCuentaActual"];
			
			
			$dato = explode("|", $cadena);
			$id = $dato[0];
			$sector = $dato[1];
			$subsector = $dato[2];
			$unidad = $dato[3];
			$tipo = $dato[4];
			$consolidado = $dato[5];
			$nivel = $dato[6];
			$nombre = strtoupper($dato[7]);
			$importe = $dato[8];			

			if($oper=='INS'){
				$sql="INSERT INTO sia_consolidadosdetalles (idCuenta, idSector, idSubsector, idUnidad, idConsolidado, nivel, nombre, importe,  usrAlta, fAlta, estatus) " .
				"VALUES(:cuenta, :sector, :subsector, :unidad, :consolidado, :nivel, :nombre, :importe, :usrActual, getdate(), 'ACTIVO');";

				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':cuenta' => $cuenta, ':sector' => $sector, ':subsector' => $subsector, ':unidad' => $unidad, ':consolidado' => $consolidado, ':nivel' => $nivel, 
				':nombre' => $nombre, ':importe' => $importe, ':usrActual' => $usrActual ));
				
				//echo "<br>$sql   <br><br>Cuenta: $cuenta  Sector:$sector  Subsector:$subsector  Unidad:$unidad  Consolidado:$consolidado  Nivel: $nivel   Importe:$importe  Nombre: $nombre  usrActual:$usrActual <hr>INS 100%";
			}else{
				$sql="UPDATE sia_consolidadosdetalles SET " .
				"idSector=:sector, idSubsector=:subsector, idUnidad=:unidad, idConsolidado=:consolidado, nivel=:nivel, nombre=:nombre, importe=:importe, usrModificacion=:usrActual, fModificacion=getdate() " .
				"WHERE idConsolidadoDetalle=:id";

				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array( ':sector' => $sector, ':subsector' => $subsector, ':unidad' => $unidad, ':consolidado' => $consolidado, ':nivel' => $nivel,':nombre' => $nombre, ':importe' => $importe,  ':usrActual' => $usrActual,':id' => $id ));
				//echo "UPD 100%";
			}
			echo "OK";
			
		}catch (PDOException $e) {
			echo  "<br>¡Error en el TRY!: " . $e->getMessage();
			die();
		}
	});	
	

	$app->get('/lstCentrosByArea', function()  use($app, $db) {

		$cuenta = $_SESSION["idCuentaActual"];
		$area = $_SESSION["idArea"];
		
		$sql="SELECT au.idSector + au.idSubsector + au.idUnidad id , u.nombre texto " . 
		"FROM sia_areasunidades au " .
		"INNER JOIN sia_unidades u ON au.idCuenta = u.idCuenta AND au.idSector = u.idSector AND au.idSubsector = u.idSubsector AND au.idUnidad = u.idUnidad ".
		"WHERE au.idCuenta = :cuenta AND au.idArea = :area ORDER BY u.nombre" ;

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':cuenta' => $cuenta, ':area' => $area));		
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		echo json_encode($result);

	});
	
	
	
	//Obtenerlista de consolidados
	$app->get('/tblRubrosByCentroDocto/:centro/:docto', function($centro, $docto)    use($app, $db) {
		$cuenta = $_SESSION["idCuentaActual"];
		$centro= str_replace('|', '', $centro);	
		
		$sql="Select cd.idConsolidadoDetalle id, tc.nombre sTipoConsolidado,  c.nombre sDocto, concat(u.idSector, u.idSubsector,u.idUnidad, ' ', u.nombre)  sGestor, cd.nombre rubro, cd.nivel, isnull(cd.importe,'') importe " .
		"FROM sia_consolidadosdetalles cd " . 
		"INNER JOIN sia_unidades u ON cd.idCuenta = u.idCuenta AND cd.idSector = u.idSector and cd.idSubsector = u.idSubsector and cd.idUnidad=u.idUnidad " .
		"INNER JOIN sia_consolidados c on cd.idConsolidado = c.idConsolidado " .
		"INNER JOIN sia_tiposconsolidados tc  ON tc.idTipoConsolidado = c.idTipoConsolidado " .
		//"INNER JOIN sia_areasunidades au ON cd.idCuenta = au.idCuenta and cd.idSector = au.idSector and cd.idSubsector=au.idSubsector and cd.idUnidad = au.idUnidad " .
		"Where cd.idCuenta=:cuenta and  concat(cd.idSector,cd.idSubsector,cd.idUnidad)=:centro and cd.idConsolidado=:docto;";
		
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':cuenta' => $cuenta, ':centro' => $centro, ':docto' => $docto));
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		echo json_encode($result);		
	});	
	
	
//Obtener lista de consolidados
	$app->get('/tblRubroByID/:id', function($id)    use($app, $db) {
		$cuenta = $_SESSION["idCuentaActual"];
		
		
		$sql="Select cd.idConsolidadoDetalle id, tc.idTipoConsolidado, tc.nombre sTipoConsolidado, c.idConsolidado,  c.nombre sDocto, " . 
		"concat(u.idSector, u.idSubsector,u.idUnidad) gestor, concat(u.idSector, u.idSubsector,u.idUnidad, ' ', u.nombre)  sGestor, cd.nombre rubro, cd.nivel, isnull(cd.importe,'') importe " .
		"FROM sia_consolidadosdetalles cd " . 
		"INNER JOIN sia_unidades u ON cd.idCuenta = u.idCuenta AND cd.idSector = u.idSector and cd.idSubsector = u.idSubsector and cd.idUnidad=u.idUnidad " .
		"INNER JOIN sia_consolidados c on cd.idConsolidado = c.idConsolidado " .
		"INNER JOIN sia_tiposconsolidados tc  ON tc.idTipoConsolidado = c.idTipoConsolidado " .
		"Where cd.idCuenta=:cuenta and  cd.idConsolidadoDetalle=:id;";
		
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':cuenta' => $cuenta, ':id' => $id));
		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);
		echo json_encode($result);		
	});		
	
	
	
	
//Eliminar lista de consolidados
	$app->get('/tblEliminarRubroByID/:id', function($id)    use($app, $db) {
		$cuenta = $_SESSION["idCuentaActual"];
		
		//Obtiene los datos a eliminar
		$sql="Select c.idConsolidado docto, concat(u.idSector, u.idSubsector,u.idUnidad) gestor " .
		"FROM sia_consolidadosdetalles cd " . 
		"INNER JOIN sia_unidades u ON cd.idCuenta = u.idCuenta AND cd.idSector = u.idSector and cd.idSubsector = u.idSubsector and cd.idUnidad=u.idUnidad " .
		"INNER JOIN sia_consolidados c on cd.idConsolidado = c.idConsolidado " .
		"INNER JOIN sia_tiposconsolidados tc  ON tc.idTipoConsolidado = c.idTipoConsolidado " .
		"Where cd.idCuenta=:cuenta and  cd.idConsolidadoDetalle=:id;";		
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':cuenta' => $cuenta, ':id' => $id));
		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);

		//Elimina
		$sql="DELETE FROM sia_consolidadosdetalles WHERE idConsolidadoDetalle=:id;";
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':id' => $id));
		
		//Regresa los datos eliminados		
		echo json_encode($result);		
	});		
	
	
	
	
	
	
	
	
	
	
	
	
	
	//Obtener proxima etapa
	$app->get('/proximaEtapa/:proceso/:etapa', function($proceso, $etapa)    use($app, $db) {
		$usrActual = $_SESSION ["idUsuario"];
		/*
		$sql="SELECT TOP 1 re.idProceso proceso, re.idEtapa etapa, concat('btn',e.idEtapa) boton " .
		"FROM sia_rolesetapas re inner join sia_etapas e on e.idProceso=re.idProceso and e.idEtapa=re.idEtapa " .
		"WHERE e.idProceso=:proceso and e.idEtapa<>:etapa  and orden > (Select orden from sia_etapas Where idProceso=:proceso2 and idEtapa=:etapa2 )  and re.idRol in (select ur.idRol from sia_usuariosroles ur where idUsuario = :usrActual) ORDER BY e.orden";
		*/
		$sql = "SELECT TOP 1 re.idProceso proceso, re.idEtapa etapa, case re.autorizarEtapa when 'SI' THEN concat('btn',e.idEtapa) ELSE '' END boton, re.idrol FROM sia_rolesetapas re inner join sia_etapas e on e.idProceso=re.idProceso and e.idEtapa=re.idEtapa WHERE e.idProceso=:proceso and e.idEtapa<>:etapa  and orden > (Select orden from sia_etapas Where idProceso=:proceso2 and idEtapa=:etapa2 )  
			and re.idRol in (select ur.idRol from sia_usuariosroles ur where idUsuario = :usrActual and (idRol <> 'GLOBAL-AREA' and idRol <> 'GLOBAL') ) ORDER BY e.orden";

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':proceso' => $proceso, ':etapa' => $etapa, ':proceso2' => $proceso, ':etapa2' => $etapa, ':usrActual' => $usrActual));
		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);
		echo json_encode($result);		
	});
	
	//Asignar etapa
	$app->get('/asignarEtapa/:auditoria/:proceso/:etapa', function($auditoria, $proceso, $etapa)    use($app, $db) {
		$sql="UPDATE sia_auditorias SET idProceso=:proceso, idEtapa=:etapa WHERE idAuditoria=:auditoria";
		
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':proceso' => $proceso, ':etapa' => $etapa, ':auditoria' => $auditoria));
		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);
		echo json_encode($result);		
	});
	
	// Generar claves de auditoría
	
	//Obtener un avance
	$app->get('/generarClaves/:cuenta/:programa', function($cuenta, $programa)    use($app, $db) {
		$usrActual = $_SESSION["idUsuario"];
		
		//Obtener lista de auditorias a Integrar
		$sql="SELECT idAuditoria FROM sia_auditorias WHERE idCuenta=:cuenta and idPrograma=:programa and idEtapa='INTEGRACION' and clave is null;";
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':cuenta' => $cuenta, ':programa' => $programa));
		$audis = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
				
		//Obtener un folio inicial
		$sql = "SELECT TOP 1 idRango, siguiente folio, siglas, disponible FROM sia_rangos WHERE token='NUM-AUDITORIAS' and estatus='ACTIVO';";
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute();
		$rsFolios = $dbQuery->fetch(PDO::FETCH_ASSOC);			
		$folioActual = $rsFolios['folio'];
		$rango = $rsFolios['idRango'];
		$siglas = $rsFolios['siglas'];
		$disponible = $rsFolios['disponible'];
		
		foreach ($audis as $audi) {			
			$auditoriaActual = $audi['idAuditoria'];

			//Asignar el folio
			$clave = $siglas . $folioActual;					
			$sql = "UPDATE sia_auditorias SET clave=:clave, usrModificacion=:usrActual, fModificacion=getdate() Where idCuenta=:cuenta and idPrograma=:programa and idAuditoria=:auditoria ";
			$dbQuery = $db->prepare($sql);			
			$dbQuery->execute(array( ':usrActual' => $usrActual, ':clave' => $clave,':cuenta' => $cuenta, ':programa' => $programa, ':auditoria' => $auditoriaActual));
						
			//Registrar bitacora del folio
			$sql = "INSERT INTO sia_rangosfolios (idRango, folio, fFolio, idDocumento, usrAlta, fAlta, estatus) values(:rango, :folio, getdate(), 1, :usrActual, getdate(), 'ACTIVO'); ";
			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':rango' => $rango, ':folio' => $folioActual, ':usrActual' => $usrActual));
			$folioActual = $folioActual + 1; 			
		}
		//Registrar ultimo folio
		$disponible = $disponible - $folioActual + 1;
		$sql = "UPDATE sia_rangos SET siguiente=:folio, disponible=:disponible, usrModificacion=:usrActual, fModificacion=getdate() WHERE idRango=:rango;";
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':folio' => $folioActual, ':disponible' => $disponible, ':usrActual' => $usrActual, ':rango' => $rango));	
		echo "OK";
	});	


//Obtener lista de tipo de consolidadospor area
	$app->get('/lstTiposConsolidados', function()    use($app, $db) {
		$sql="Select idTipoConsolidado id, nombre texto from sia_tiposconsolidados Where idArea=:area order by 2 asc";
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':area' => $area));
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		echo json_encode($result);		
	});
	
//Obtener lista de tipo de consolidados by Tipo
	$app->get('/lstConsolidadosByTipo/:tipo', function($tipo)    use($app, $db) {
		$sql="Select idConsolidado id, nombre texto from sia_consolidados Where idTipoConsolidado=:tipo order by 2 asc";
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':tipo' => $tipo));
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		echo json_encode($result);		
	});

	
	
//Obtener una lista de audotores por area
	$app->get('/lstAuditoresByArea/:area', function($area)    use($app, $db) {
		$sql="Select idEmpleado id, concat(nombre, ' ', paterno, ' ', materno) texto from sia_empleados Where idArea=:area and idNivel not in ('45.0', '40.0', '31.0') order by 2 asc";
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':area' => $area));
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		echo json_encode($result);		
	});
	
	

	//Listar actividades by auditoria + fase
	$app->get('/lstActividadesByAuditoriaFase/:audi/:fase', function($audi, $fase)  use($app, $db) {
		$sql="SELECT aa.idActividad id, concat(aa.idActividad, ' ', f.nombre, left(actividad, 100), '...') texto 
		FROM sia_auditoriasactividades aa 
		LEFT join sia_fases f on aa.idFase = f.idFase
		WHERE aa.idAuditoria=:audi     and aa.idFase=:fase 
		ORDER BY 2 asc";

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':audi' => $audi, ':fase' => $fase));
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		echo json_encode($result);
	});	
	
	//Obtener un avance
	$app->get('/buscarTipoPapel/:id', function($id)    use($app, $db) {
		$sql="SELECT idTipoPapel, nombre papel, programada FROM sia_tipospapeles WHERE idTipoPapel=:id";
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':id' => $id));
		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);
		echo json_encode($result);		
	});	
	
	//Obtener un empleado
	$app->get('/empleadoByRPE/:id', function($id)    use($app, $db) {
		$sql="SELECT u.saludo, u.idUsuario, e.idEmpleado, u.tipo, e.nombre, e.paterno, e.materno, e.idArea, u.telefono, u.usuario, u.pwd, u.estatus " . 
		"FROM sia_empleados e left join sia_usuarios u on u.idEmpleado=e.idEmpleado " . 
		"WHERE e.idEmpleado=:id";
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':id' => $id));
		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);
		echo json_encode($result);		
	});	
	
	
	//Listar actividades by auditoria 
	$app->get('/dpsAuditoriasByArea', function()  use($app, $db) {
		
		$area = $_SESSION["idArea"];
		$cuenta = $_SESSION["idCuentaActual"];
		$rpe = $_SESSION["idEmpleado"];
		$global = $_SESSION["usrGlobal"];
		$globalArea = $_SESSION["usrGlobalArea"];
		$usrActual = $_SESSION["idUsuario"];
		
		if ($global=="SI"){
			$sql="SELECT ta.nombre texto, count(*) valor
				FROM sia_auditorias a 
  				INNER JOIN sia_tiposauditoria ta  ON a.tipoAuditoria=ta.idTipoAuditoria
				WHERE a.idCuenta=:cuenta and a.idEtapa in (Select idEtapa from sia_rolesetapas re inner join sia_usuariosroles ur on ur.idRol = re.idRol  Where ur.idUsuario=:usrActual)
				GROUP BY ta.nombre ORDER BY ta.nombre";
			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':cuenta' => $cuenta,':usrActual' => $usrActual));				
		}else{

			if($globalArea=='SI'){
				$sql="SELECT ta.nombre texto, count(*) valor
					FROM sia_auditorias a INNER JOIN sia_tiposauditoria ta  ON a.tipoAuditoria=ta.idTipoAuditoria
					WHERE a.idCuenta=:cuenta  AND a.idArea=:area AND a.clave is not null and a.idEtapa in (Select idEtapa from sia_rolesetapas re inner join sia_usuariosroles ur on ur.idRol = re.idRol  Where ur.idUsuario=:usrActual)
					GROUP BY ta.nombre ORDER BY ta.nombre ";
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':cuenta' => $cuenta, ':area' => $area, ':usrActual' => $usrActual));					
			}else{
				$sql="SELECT ta.nombre texto, count(*) valor
					FROM sia_auditorias a 
  					INNER JOIN sia_tiposauditoria ta  ON a.tipoAuditoria=ta.idTipoAuditoria
  					INNER JOIN  sia_auditoriasauditores aa ON a.idCuenta=aa.idCuenta and a.idAuditoria=aa.idAuditoria
					WHERE a.idCuenta=:cuenta  AND aa.idAuditor=:auditor AND a.clave is not null AND a.idEtapa in (Select idEtapa from sia_rolesetapas re inner join sia_usuariosroles ur on ur.idRol = re.idRol  Where ur.idUsuario=:usrActual)
					GROUP BY ta.nombre ORDER BY ta.nombre ";
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':cuenta' => $cuenta,  ':auditor' => $rpe,  ':usrActual' => $usrActual));
			}

		}
		
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		echo json_encode($result);
	});		
	
	
	//Listar actividades by auditoria + fase
	$app->get('/dpsEmpleadosByArea', function()  use($app, $db) {
		$sql="Select idArea texto, count(*) valor from sia_empleados group by idArea  order by idArea";

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute();
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		echo json_encode($result);
	});		
	
//dpsCedulasByAuditoria	
	$app->get('/dpsCedulasByAuditoria', function()  use($app, $db) {
		$cuenta = $_SESSION["idCuentaActual"];
		$area = $_SESSION["idArea"];
		$global = $_SESSION["usrGlobal"];
		
		if ($global=="SI"){	
			$sql="SELECT a.idAuditoria id, COALESCE(a.clave, concat('PROY-',a.idAuditoria)) texto, " . 
			"(SELECT count(*) FROM sia_papeles p WHERE p.idAuditoria=a.idAuditoria ) valor " . 
			"FROM sia_auditorias a " .
			"WHERE a.idCuenta=:cuenta  " .
			"ORDER BY COALESCE(a.clave, concat('PROY-',a.idAuditoria)) ";
			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':cuenta' => $cuenta));	
		}else{
			$sql="SELECT a.idAuditoria id, COALESCE(a.clave, concat('PROY-',a.idAuditoria)) texto, " . 
			"(SELECT count(*) FROM sia_papeles p WHERE p.idAuditoria=a.idAuditoria ) valor " . 
			"FROM sia_auditorias a " .
			"WHERE a.idCuenta=:cuenta and a.idArea=:area  " .
			"ORDER BY COALESCE(a.clave, concat('PROY-',a.idAuditoria)) ";
			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':cuenta' => $cuenta, ':area' => $area));						
		}
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		echo json_encode($result);
	});	
	
//dpsDoctosByAuditoria	
	$app->get('/dpsDoctosByAuditoria', function()  use($app, $db) {
		$cuenta = $_SESSION["idCuentaActual"];
		$area = $_SESSION["idArea"];
		$global = $_SESSION["usrGlobal"];
		
		if ($global=="SI"){	
			$sql="SELECT a.idAuditoria id, COALESCE(a.clave, concat('PROY-',a.idAuditoria)) texto, " . 
			"(SELECT count(*) FROM sia_acopio ac WHERE ac.idAuditoria=a.idAuditoria ) valor " . 
			"FROM sia_auditorias a " .
			"WHERE a.idCuenta=:cuenta  " .
			"ORDER BY COALESCE(a.clave, concat('PROY-',a.idAuditoria)) ";
			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':cuenta' => $cuenta));	
		}else{
			$sql="SELECT a.idAuditoria id, COALESCE(a.clave, concat('PROY-',a.idAuditoria)) texto, " . 
			"(SELECT count(*) FROM sia_acopio ac WHERE ac.idAuditoria=a.idAuditoria ) valor " . 
			"FROM sia_auditorias a " .
			"WHERE a.idCuenta=:cuenta and a.idArea=:area  " .
			"ORDER BY COALESCE(a.clave, concat('PROY-',a.idAuditoria)) ";
			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':cuenta' => $cuenta, ':area' => $area));						
		}
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		echo json_encode($result);
	});		
	
	
	
//dps tipos de papeles
	$app->get('/dpsTipoPapeles', function()  use($app, $db) {
		$area = $_SESSION["idArea"];
		$cuenta = $_SESSION["idCuentaActual"];
		$rpe = $_SESSION["idEmpleado"];
		$global = $_SESSION["usrGlobal"];
		$globalArea = $_SESSION["usrGlobalArea"];
		$usrActual = $_SESSION["idUsuario"];
		
		if ($global=="SI"){
			$sql="SELECT tp.nombre texto, count(*) valor
				FROM sia_papeles p INNER JOIN sia_tipospapeles tp ON p.tipoPapel=tp.idTipoPapel
				INNER JOIN sia_auditorias a on p.idAuditoria=a.idAuditoria
				WHERE p.idCuenta=:cuenta and a.idEtapa in (Select idEtapa from sia_rolesetapas re inner join sia_usuariosroles ur on ur.idRol = re.idRol  Where ur.idUsuario=:usrActual)
				GROUP BY tp.nombre  ORDER BY tp.nombre";
			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':cuenta' => $cuenta,':usrActual' => $usrActual));				
		}else{

			if($globalArea=='SI'){
				$sql="SELECT tp.nombre texto, count(*) valor
					FROM sia_papeles p INNER JOIN sia_tipospapeles tp ON p.tipoPapel=tp.idTipoPapel
					INNER JOIN sia_auditorias a on p.idAuditoria=a.idAuditoria
					WHERE p.idCuenta=:cuenta  AND a.idArea=:area AND a.clave is not null and a.idEtapa in (Select idEtapa from sia_rolesetapas re inner join sia_usuariosroles ur on ur.idRol = re.idRol  Where ur.idUsuario=:usrActual)
					GROUP BY tp.nombre  ORDER BY tp.nombre ";
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':cuenta' => $cuenta, ':area' => $area, ':usrActual' => $usrActual));					
			}else{
				$sql="SELECT tp.nombre texto, count(*) valor
					FROM sia_papeles p INNER JOIN sia_tipospapeles tp ON p.tipoPapel=tp.idTipoPapel
					INNER JOIN sia_auditorias a on p.idAuditoria=a.idAuditoria
				  	INNER JOIN  sia_auditoriasauditores aa ON a.idCuenta=aa.idCuenta and a.idAuditoria=aa.idAuditoria
					WHERE p.idCuenta=:cuenta  and aa.idAuditor=:auditor AND a.clave is not null AND a.idEtapa in (Select idEtapa from sia_rolesetapas re inner join sia_usuariosroles ur on ur.idRol = re.idRol  Where ur.idUsuario=:usrActual)
					GROUP BY tp.nombre  ORDER BY tp.nombre ";
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':cuenta' => $cuenta,  ':auditor' => $rpe,  ':usrActual' => $usrActual));
			}

		}
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		echo json_encode($result);
	});		

//Listar actividades by auditoria + fase
	$app->get('/dpsTipoAcopio', function()  use($app, $db) {
		$area = $_SESSION["idArea"];
		$cuenta = $_SESSION["idCuentaActual"];
		$rpe = $_SESSION["idEmpleado"];
		$global = $_SESSION["usrGlobal"];
		$globalArea = $_SESSION["usrGlobalArea"];
		$usrActual = $_SESSION["idUsuario"];
		
		if ($global=="SI"){
			$sql="SELECT ac.idClasificacion texto, count(*) valor
				FROM sia_acopio ac
				INNER JOIN  sia_auditorias a on ac.idCuenta=a.idCuenta and ac.idPrograma = a.idPrograma and ac.idAuditoria = a.idAuditoria
				Where ac.idCuenta=:cuenta and a.idEtapa in (Select idEtapa from sia_rolesetapas re inner join sia_usuariosroles ur on ur.idRol = re.idRol  Where ur.idUsuario=:usrActual)
			  	Group by idClasificacion  Order by idClasificacion;";
			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':cuenta' => $cuenta,':usrActual' => $usrActual));				
		}else{

			if($globalArea=='SI'){
				$sql="SELECT ac.idClasificacion texto, count(*) valor
					FROM sia_acopio ac
					INNER JOIN  sia_auditorias a on ac.idCuenta=a.idCuenta and ac.idPrograma = a.idPrograma and ac.idAuditoria = a.idAuditoria
					Where ac.idCuenta=:cuenta AND a.idArea=:area AND a.clave is not null and a.idEtapa in (Select idEtapa from sia_rolesetapas re inner join sia_usuariosroles ur on ur.idRol = re.idRol  Where ur.idUsuario=:usrActual)
  					Group by idClasificacion  Order by idClasificacion;";
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':cuenta' => $cuenta, ':area' => $area, ':usrActual' => $usrActual));					
			}else{
				$sql="SELECT ac.idClasificacion texto, count(*) valor
					FROM sia_acopio ac
					INNER JOIN  sia_auditorias a on ac.idCuenta=a.idCuenta and ac.idPrograma = a.idPrograma and ac.idAuditoria = a.idAuditoria
					INNER JOIN  sia_auditoriasauditores aa ON a.idCuenta=aa.idCuenta and a.idAuditoria=aa.idAuditoria
					Where ac.idCuenta=:cuenta and aa.idAuditor=:auditor AND a.clave is not null AND a.idEtapa in (Select idEtapa from sia_rolesetapas re inner join sia_usuariosroles ur on ur.idRol = re.idRol  Where ur.idUsuario=:usrActual)
					Group by idClasificacion  Order by idClasificacion;";
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':cuenta' => $cuenta,  ':auditor' => $rpe,  ':usrActual' => $usrActual));
			}

		}
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		echo json_encode($result);
	});		
	
//Listar Avance por cada auditoria
	$app->get('/dpsAvanceByAuditorias', function()  use($app, $db) {
		$cuenta = $_SESSION["idCuentaActual"];
		$area = $_SESSION["idArea"];
		$global = $_SESSION["usrGlobal"];
		
		if ($global=="SI"){		
			$sql="Select distinct a.idAuditoria, COALESCE(clave, concat('PROY-',a.idAuditoria)) text, u.nombre sujeto, " .
			"isnull((Select top 1 porcentaje from sia_auditoriasavances aa2 Where a.idAuditoria=aa2.idAuditoria order by porcentaje desc ), 0) valor " .
			"from sia_auditorias a " .
			"left join sia_auditoriasavances aa on a.idAuditoria=aa.idAuditoria " .
			"inner join sia_unidades u on concat(a.idCuenta,a.idSector, a.idSubsector, a.idUnidad)=concat(u.idCuenta,u.idSector, u.idSubsector, u.idUnidad) " .
			"Where a.idCuenta=:cuenta  ";
			$dbQuery = $db->prepare($sql);		
			$dbQuery->execute(array(':cuenta' => $cuenta));
		}else{
			$sql="Select distinct a.idAuditoria, COALESCE(clave, concat('PROY-',a.idAuditoria)) texto, u.nombre sujeto, " .
			"isnull((Select top 1 porcentaje from sia_auditoriasavances aa2 Where a.idAuditoria=aa2.idAuditoria order by porcentaje desc ), 0) valor " .
			"from sia_auditorias a " .
			"left join sia_auditoriasavances aa on a.idAuditoria=aa.idAuditoria " .
			"inner join sia_unidades u on concat(a.idCuenta,a.idSector, a.idSubsector, a.idUnidad)=concat(u.idCuenta,u.idSector, u.idSubsector, u.idUnidad) " .
			"Where a.idCuenta=:cuenta and a.idArea=:area And a.clave is not null";
			$dbQuery = $db->prepare($sql);		
			$dbQuery->execute(array(':cuenta' => $cuenta, ':area' => $area));	
		}
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		echo json_encode($result);
	});		
		

//Listar Avance por cada auditoria
	$app->get('/dpsMonitorAuditoresByDireccion/:f1/:f2', function($f1, $f2)  use($app, $db) {
		$cuenta = $_SESSION["idCuentaActual"];
		$area = $_SESSION["idArea"];
		$global = $_SESSION["usrGlobal"];
		
		if ($global=="SI"){		
			$sql="Select CONVERT(VARCHAR,fIngreso,105) texto, count(*) valor from sia_accesos ". 
			"Where fIngreso between CONVERT(VARCHAR,:f1,105) and CONVERT(VARCHAR,:f2,105) ".
			"GROUP BY CONVERT(VARCHAR,fIngreso,105) ".
			"ORDER BY CONVERT(VARCHAR,fIngreso,105) ";
			$dbQuery = $db->prepare($sql);		
			$dbQuery->execute(array(':f1' => $f1, ':f2' => $f2));
			//$dbQuery->execute();	
		}else{
			$sql="Select CONVERT(VARCHAR,a.fIngreso,105) texto, count(*) valor " . 
			"FROM sia_accesos  a inner join sia_usuarios u ON a.idUsuario=u.idUsuario " . 
			"Where a.fIngreso between CONVERT(VARCHAR,:f1,105) and CONVERT(VARCHAR,:f2,105) AND u.idArea=:area ".
			"GROUP BY CONVERT(VARCHAR,a.fIngreso,105) ".
			"ORDER BY CONVERT(VARCHAR,a.fIngreso,105) ";
			
			$dbQuery = $db->prepare($sql);		
			$dbQuery->execute(array(':area' => $area, ':f1' => $f1, ':f2' => $f2));
			//$dbQuery->execute();	
		}
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		echo json_encode($result);
	});

		
	
	//Listar localizacion de las auditorias
	$app->get('/ptsAuditoriasByArea', function()  use($app, $db) {
		$cuenta = $_SESSION["idCuentaActual"];
		$area = $_SESSION["idArea"];
		$sql="SELECT a.idAuditoria, COALESCE(a.clave, concat('PROY-',a.idAuditoria)) claveAuditoria, ta.nombre tipoAuditoria,  a.latitud, a.longitud " . 
		"FROM sia_auditorias a inner join sia_tiposauditoria ta  on a.tipoAuditoria=ta.idTipoAuditoria  " . 
		"WHERE a.idCuenta=:cuenta and a.idArea=:area AND a.latitud  IS NOT NULL AND a.longitud IS NOT NULL ";

		$dbQuery = $db->prepare($sql);		
		$dbQuery->execute(array(':cuenta' => $cuenta, ':area' => $area));
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		echo json_encode($result);
	});	
	
	//Listar responsables de cada area
	$app->get('/lstAreasResponsables', function()  use($app, $db) {
		$area = $_SESSION["idArea"];
		$global = $_SESSION["usrGlobal"];
		$globalArea = $_SESSION["usrGlobalArea"];
		
		if ($global=="SI" || $globalArea=="SI"){			
			$sql="SELECT idResponsable id, nombre texto FROM sia_areasresponsables ORDER BY nombre asc";
			$dbQuery = $db->prepare($sql);
			$dbQuery->execute();		
		}else{
			$sql="SELECT idResponsable id, nombre texto FROM sia_areasresponsables WHERE idArea=:area ORDER BY nombre asc";
			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':area' => $area));	
		}
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		echo json_encode($result);
	});	
	
	
	//Listar responsables de cada area
	$app->get('/lstAreasResponsablesByID/:id', function($id)  use($app, $db) {		
		$sql="SELECT idSubresponsable id, nombre texto FROM sia_areassubresponsables WHERE idResponsable=:id ORDER BY nombre asc";
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':id' => $id));	
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		echo json_encode($result);
	});	
		
	//Listar rangos de fechas inhabiles
	$app->get('/lstInhabilesByRango/:f1/:f2', function($f1, $f2)  use($app, $db) {	
		
		$f1 = date_create($f1);
		$f1 = $f1->format('Y-m-d');
		
		$f2 = date_create($f2);
		$f2 = $f2->format('Y-m-d');		

		try{
		$sql="Select 'A' caso , fInicio, fFin from sia_diasinhabiles Where :par1<fInicio and fInicio<:par2 and :par3<fFin " . 
			"Union all " . 
			"Select 'B' caso , fInicio, fFin from sia_diasinhabiles Where fInicio<:par4 and :par5<fFin " . 
			"union all " . 
			"Select 'C' caso , fInicio, fFin from sia_diasinhabiles Where fInicio<:par6 and :par7<fFin and fFin<:par8 " . 
			"union all " . 
			"Select 'D' caso , fInicio, fFin from sia_diasinhabiles Where :par9<fInicio and fFin<:par10 ";
		
			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(
			array(':par1'=> $f1, ':par2'=> $f2, ':par3'=> $f2, 
			':par4'=> $f1, ':par5'=> $f2, 
			':par6'=> $f1, ':par7'=> $f1, ':par8'=> $f2, 
			':par9'=> $f1,':par10'=> $f1 ));	
			$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
			//echo "F1:" . $f1 . " F2:" . $f2;
			echo json_encode($result);			
			
			}catch (OException $e) {
			echo  "<br>¡Error en el TRY!: " . $e->getMessage();
			die();
		}
			
			
		
	});		




	
	
	
?>