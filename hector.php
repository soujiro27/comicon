<?php
	include("src/conexion.php");
	include("midle/middle.php");

	 try{
		  $db = new PDO("sqlsrv:Server={$hostname}; Database={$database}", $username, $password );
	 }catch (PDOException $e) {
		print "ERROR: " . $e->getMessage();
	 }

	/* ***************
	  FUNCIONES GET
	 *****************/

	// ****  CATÁLOGO DE DÍAS INHABILES  *****

	$app->get('/catInhabiles',  $autenticacionrole, function()  use ($app, $db) {
		//$cuenta = $_SESSION ["idCuentaActual"];

		$sql="SELECT dh.idCuenta idCta, dh.idDia idDia, dh.tipo tipo, dh.nombre nombre, CONVERT(VARCHAR(12),dh.fInicio,101) fInicio, CONVERT(VARCHAR(12),dh.fFin,101) fFin, dh.estatus estatus FROM sia_diasinhabiles dh ORDER BY  dh.idCuenta, dh.idDia DESC";
		$dbQuery = $db->prepare($sql);
//		$dbQuery->execute(array(':cuenta' => $cuenta));
		$dbQuery->execute();
		$result ['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS.");
		}else{
			$app->render('catInhabiles.php', $result);
		}
	})->name('catInhabiles');


	// Obten los registro que cumplan con el día inhábil

	$app->get('/lstInhabilByID/:id', function($id)    use($app, $db) {
		$sql="SELECT idCuenta, idDia, tipo, nombre, fInicio, fFin, usrAlta, fAlta, estatus " .
		"FROM sia_diasinhabiles WHERE idDia=:id ";
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':id' => $id));
		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});

	// ****  CRITERIOS PARA AUDITORIAS   *****

	// Obten los registro que cumplan con el tipo de auditoría enviado

	$app->get('/lstCriteriosByTipoAuditoria/:id', function($id) use($app, $db) {

		$sql="SELECT idCriterio id, nombre texto FROM sia_criterios Where idTipoAuditoria=:id";

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':id' => $id));
		$result ['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
	
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON CRITERIOS PARA MOSTRAR. ");
		}else{
			echo json_encode($result);
		}
	});

	//Lista de todas las unidades
	$app->get('/lstUnidades', function()    use($app, $db) {
		$cuenta = $_SESSION ["idCuentaActual"];
		$area = $_SESSION ["idArea"];
		
		$sql="SELECT ltrim(idUnidad) id, nombre texto FROM sia_unidades WHERE idCuenta = :cuenta ORDER BY nombre;";
		
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':cuenta' => $cuenta ) );
		
		$result ['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});

	//Lista de Areas By usuario

	$app->get('/lstAreasByUsuario/', function()  use($app, $db) {

		$area = $_SESSION ["idArea"];

		$sql="SELECT a.idArea id, a.nombre texto FROM sia_areas a WHERE idArea = :area ";

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':area' => $area));
		$result ['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		echo json_encode($result);
	});

	//Listar objetos desde la tabla sia_objetos que no esten seleccionados tanto de EGRESO como de INGRESO para colocarl o en el campo TXTOBJETO.

	$app->get('/lstObjetosByAuditoria/:auditoria', function($auditoria)  use($app, $db) {

		$cuenta = $_SESSION ["idCuentaActual"];
		$programa = $_SESSION ["idProgramaActual"];

		$cuenta1 = $_SESSION ["idCuentaActual"];
		$programa1 = $_SESSION ["idProgramaActual"];

		$sql = "SELECT idObjeto id, nombre texto FROM sia_objetos WHERE IdCuenta = :cuenta AND idPrograma = :programa AND idAuditoria = :auditoria ORDER BY nombre";

		//" AND idObjeto not in (Select idObjeto from sia_auditorias where idCuenta = :cuenta1 AND idPrograma = :programa1) " .
		

		$dbQuery = $db->prepare($sql);

		$dbQuery->execute(array(':cuenta' => $cuenta, ':programa' => $programa, ':auditoria' => $auditoria));
//		$dbQuery->execute(array(':cuenta' => $cuenta, ':programa' => $programa, ':auditoria' => $auditoria, ':cuenta1' => $cuenta1, ':programa1' => $programa1));
		
		$result ['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON OBJETOS DE FISCALIZACIÓN. ");
		}else{
			echo json_encode($result);
		}
	});

	// Obtiene los criterios que corresponden a una auditoria.

    $app->get('/tblCriteriosByAuditoria/:id', function($id)  use($app, $db) {
            $sql="SELECT DISTINCT c.idCriterio criterio, c.nombre nombre FROM sia_auditoriascriterios ac INNER JOIN sia_criterios c ON ac.idCriterio=c.idCriterio WHERE ac.idAuditoria=:id";
            //$sql="Select c.idCriterio idCriterio, c.nombre texto from sia_auditoriascriterios ac inner join sia_criterios c on ac.idCriterio=c.idCriterio WHERE ac.idAuditoria=:id";
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':id' => $id));

		// USO DEL FETCH PARA CUANDO SE REGRESA UN GRUPO DE REGISTROS
		$result ['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});


	// Obtiene los archivos relacionados una auditoria.

    $app->get('/tblArchivosByAuditoria/:auditoria', function($auditoria)  use($app, $db) {

		$cuenta = $_SESSION ["idCuentaActual"];
		$programa = $_SESSION ["idProgramaActual"];
		/*
		$aValores = explode("|", $valores);
		$auditoria = $aValores [0];
		$sujeto = $aValores [1];
		$objeto = $aValores [2];
		*/

		$sql = "" .
		"SELECT * FROM ( 
        	SELECT DISTINCT d.idDocto, isnull(d.numeroDocto,'') numeroDocto
			    , isnull(d.idTipoDocto,'') idTipoDocto, isnull(td.nombre,'') tipo
				, isnull(d.flujoDocto,'') flujodocto
				, isnull(d.fDocto,'') fDocto, isnull(fRecepcion,'') fRecepcion, isnull(fTermino,'') fTermino
				, isnull(d.idRemitente,'') idRemitente, isnull(u.nombre,'') remitente
				, isnull(d.idDestinatario,'') idDestinatario, isnull(a.nombre,'') destinatario
				, isnull(d.idPrioridad,'') idPrioridad 
				, isnull(d.idImpacto,'') idImpacto
				, isnull(d.asunto,'') asunto
				, isnull(d.idRecibio,'') idrecibio
				, isnull(d.estatus,'') estatus
				, isnull(d.archivoOriginal,'') archivoOriginal
				, isnull(d.archivoFinal,'') archivoFinal
			FROM sia_documentos d
			INNER JOIN sia_documentosauditorias da ON d.idDocto = da.idDocto
			INNER JOIN sia_tiposdocumentos td ON d.idTipoDocto = td.idTipoDocto 
			INNER JOIN sia_unidades u ON d.idRemitente = u.idSector + u.idSubsector + u.idUnidad 
			INNER JOIN sia_areas a ON d.idDestinatario = a.idArea 
			WHERE da.idCuenta = :cuenta AND da.idPrograma = :programa AND da.idAuditoria = :auditoria
			) a  
		ORDER BY a.fDocto, a.idTipoDocto DESC ";

		$dbQuery = $db->prepare($sql);
		/*
		$dbQuery->execute(array(':cuenta' => $cuenta, ':programa' => $programa, ':auditoria' => $auditoria,  ':sujeto' => $sujeto, ':objeto' => $objeto));
		*/

		$dbQuery->execute(array(':cuenta' => $cuenta, ':programa' => $programa, ':auditoria' => $auditoria));

		// USO DEL FETCH PARA CUANDO SE REGRESA UN GRUPO DE REGISTROS
		$result ['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});


	// Obtiene el registro de auditoriasCriterios que corresponde a los ids de cuenta, programa, auditoria y criterio, que se le mandan.

    
    $app->get('/lstAuditoriaCriterioByIds/:auditoria/:criterio', function($auditoria, $criterio)  use($app, $db) {
    	
    	try
    	{ 
			$cuenta = $_SESSION ["idCuentaActual"];
			$programa = $_SESSION ["idProgramaActual"];

			//echo("DATOS-->" . $cuenta . " - " . $programa . " - " . $auditoria . " - " . $criterio);		
			
	        $sql="SELECT idCuenta cuenta, idPrograma programa, idAuditoria auditoria, idCriterio criterio, justificacion, elementos, estatus FROM sia_auditoriascriterios WHERE idCuenta = :cuenta AND idPrograma = :programa AND idAuditoria = :auditoria AND idCriterio = :criterio";
			
			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':cuenta' => $cuenta, ':programa' => $programa, ':auditoria' => $auditoria, ':criterio' => $criterio));

			// USO DEL FETCH PARA CUANDO SOLO SE REGRESA UN REGISTRO
			$result = $dbQuery->fetch(PDO::FETCH_ASSOC);
			if(!$result){
				$app->halt(404, "NO SE ENCONTRARON DATOS ");
			}else{
				echo json_encode($result);
			}
		 }catch (PDOException $e) {
		  	print "ERROR: " . $e->getMessage() . "<br><br> QUERY: " . $dbQuery ;
	  		die();
	  	}
	});


    $app->get('/lstAudCriByIds/:auditoria/:tipoAuditoria/:criterio', function($auditoria, $tipoAuditoria, $criterio)  use($app, $db) {
    	
    	try
    	{ 
			$cuenta = $_SESSION ["idCuentaActual"];
			$programa = $_SESSION ["idProgramaActual"];

	        $sql="SELECT ac.idCriterio criterio, c.nombre nombreCriterio, ac.justificacion justificacion, ac.elementos elementos , ac.estatus estatus FROM sia_auditoriascriterios ac INNER JOIN sia_criterios c ON ac.idCriterio = c.idCriterio AND c.idTipoAuditoria = :tipoAuditoria WHERE ac.idCuenta = :cuenta AND ac.idPrograma = :programa AND ac.idAuditoria = :auditoria AND ac.idCriterio = :criterio";

			$dbQuery = $db->prepare($sql);
			//$dbQuery->execute(array(':cuenta' => $cuenta, ':programa' => $programa, ':auditoria' => $auditoria, ':criterio' => $criterio));
			$dbQuery->execute(array(':tipoAuditoria' => $tipoAuditoria, ':cuenta' => $cuenta, ':programa' => $programa, ':auditoria' => $auditoria,  ':criterio' => $criterio));

			// USO DEL FETCH PARA CUANDO SOLO SE REGRESA UN REGISTRO
			$result= $dbQuery->fetch(PDO::FETCH_ASSOC);
			if(!$result){
				$app->halt(404, "NO SE ENCONTRARON DATOS ");
			}else{
				echo json_encode($result);
			}
		 }catch (Exception $e) {
		  	print "ERROR: " . $e->getMessage() . "<br><br> QUERY: " . $dbQuery ;
	  		die();
	  	}
	});

	//Guarda un nuevo criterio para la auditoria 

	$app->get('/guardar/auditoriaCriterios/:oper/:cadena', function($oper, $cadena)  use($app, $db) {
		$datos = $cadena;

		$usrActual = $_SESSION ["idUsuario"];
		$cuenta = $_SESSION ["idCuentaActual"];
		$programa = $_SESSION ["idProgramaActual"];
		

		try
		{
			if ($cadena<>"")
			{
				$campos = explode("|", $cadena);

				$auditoria=$campos [0];
				$criterio=$campos [1];
				$justificacion=$campos [2];
				$elementos=$campos [3];
				$criterioAnterior=($campos [4]);

				if($oper=='INS')
				{
					$sql="INSERT INTO sia_auditoriascriterios (idCuenta, idPrograma, idAuditoria, idCriterio, justificacion, elementos, usrAlta, fAlta, estatus) VALUES(:cuenta, :programa, :auditoria, :criterio, :justificacion, :elementos, :usrActual, getdate(), 'ACTIVO');";

					$dbQuery = $db->prepare($sql);

					$dbQuery->execute(array(':cuenta' => $cuenta, ':programa' => $programa, ':auditoria' => $auditoria, ':criterio' => $criterio, 
						':justificacion' => $justificacion, ':elementos' => $elementos, ':usrActual' => $usrActual));

				}
				else
				{
					$sql="UPDATE sia_auditoriascriterios SET idCriterio=:criterio, justificacion=:justificacion, elementos=:elementos, usrModificacion=:usrActual, fModificacion=getdate() WHERE idCuenta=:cuenta AND idPrograma=:programa AND idAuditoria=:auditoria AND idCriterio=:criterioAnterior";
					$dbQuery = $db->prepare($sql);

					$dbQuery->execute(array(':criterio' => $criterio, ':justificacion' => $justificacion, ':elementos' => $elementos, ':usrActual' => $usrActual, ':cuenta' => $cuenta, ':programa' => $programa, ':auditoria' => $auditoria, ':criterioAnterior' => $criterioAnterior));
				}
				echo("OK");
			}
			else
			{
				echo("NO");
			}

		}catch (Exception $e) {
			print "¡Error!: " . $e->getMessage() . "<br/>";
			die();
		}

				//$app->redirect($app->urlFor('listaAuditoriaCriterios'));
	});


	//Guarda un nuevo criterio para la auditoria 

	$app->get('/valAudCri/:auditoria/:criterio', function($auditoria, $criterio)  use($app, $db) {
		
		try
		{
			$cuenta = $_SESSION ["idCuentaActual"];
			$programa = $_SESSION ["idProgramaActual"];

			//echo($cuenta." - ".$programa." - ".$auditoria." - ".$criterio);		

			$sql = "SELECT COUNT(*) total FROM sia_auditoriascriterios WHERE idCuenta=:cuenta AND idPrograma=:programa AND idAuditoria=:auditoria AND idCriterio=:criterio";

			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':cuenta' => $cuenta, ':programa' => $programa, ':auditoria' => $auditoria, ':criterio' => $criterio ));

			$result = $dbQuery->fetch(PDO::FETCH_ASSOC);

			if(!$result){
				$app->halt(404, "NO SE ENCONTRARON DATOS ");
			}else{
				echo json_encode($result);
			}
		 }catch (Exception $e) {
		  	print "ERROR: " . $e->getMessage() . "<br><br> QUERY: " . $dbQuery ;
	  		die();
		}
	});

	/* ***************
	  FUNCIONES POST
	 ***************** */

	//Guarda un día inhábil

	$app->post('/guardar/inhabiles', function()  use($app, $db) {
		$usrActual = $_SESSION ["idUsuario"];
		$cuenta = $_SESSION ["idCuentaActual"];

		$request=$app->request;

		//$cuenta = $request->post('txtCuenta');
		$dia = $request->post('txtDia');
		$tipo = $request->post('txtTipo');
		$nombre = strtoupper($request->post('txtNombre'));

		//$fInicio = $request->post('txtFechaInicial');
		$fInicio = date_create(($request->post('txtFechaInicial')));
		$fInicio = $fInicio->format('Y-m-d');

		//$fFin = $request->post('txtFechaFinal');
		$fFin = date_create(($request->post('txtFechaFinal')));
		$fFin = $fFin->format('Y-m-d');

		$estatus = $request->post('txtEstatus');

		$oper = $request->post('txtOperacion');

		//echo "<br>$tipo:" . $tipo . "<br>$nombre:" . $nombre . "<br>$fInicio:" . $fInicio . "<br>$fFin:" . $fFin . "<br>$usrActual:" . $usrActual . "<br>$estatus:" . $estatus . "<br>$cuenta:" . $cuenta . "<br>$dia:" . $dia;
		try
		{
			if($oper=='INS')
			{
				$sql="INSERT INTO sia_diasinhabiles (idCuenta, tipo, nombre, fInicio, fFin, usrAlta, fAlta, estatus) " .
				"VALUES(:cuenta, :tipo, :nombre, :fInicio, :fFin, :usrAlta, getdate(), 'ACTIVO');";
				$dbQuery = $db->prepare($sql);

				$dbQuery->execute(array(':cuenta' => $cuenta, ':tipo' => $tipo, ':nombre' => $nombre, ':fInicio' => $fInicio, ':fFin' => $fFin, ':usrAlta' => $usrActual ));
				//echo "<br>INS OK<br>";

			}else{
				
				$sql="UPDATE sia_diasinhabiles SET tipo=:tipo, nombre=:nombre, fInicio=:fInicio, fFin=:fFin, usrModificacion=:usrModificacion, fModificacion=getdate(), estatus=:estatus WHERE idCuenta=:cuenta AND idDia =:dia";
				$dbQuery = $db->prepare($sql);

				$dbQuery->execute(array(':tipo' => $tipo, ':nombre' => $nombre, ':fInicio' => $fInicio, ':fFin' => $fFin, ':usrModificacion' => $usrActual, ':estatus' => $estatus, ':cuenta' => $cuenta, ':dia' => $dia));
				
				//echo "<br>UPD OK";
			}

			//echo nl2br("\nQuery Ejecutado : ".$sql);

		}catch (PDOException $e) {
			print "¡Error!: " . $e->getMessage() . "<br/>";
			die();
		}
		 $app->redirect($app->urlFor('catInhabiles'));
	});

	
	//Guardar una auditoria

	$app->post('/guardar/auditoria_HVS', function()  use($app, $db) {
		
		$cuenta = $_SESSION ["idCuentaActual"];
		$programa = $_SESSION ["idProgramaActual"];
		$usrActual = $_SESSION ["idUsuario"];
		$area = $_SESSION ["idArea"];

		$request=$app->request;
		
		try {
			//$cuenta = $request->post('txtCuenta');
			//$programa = $request->post('txtPrograma');

			$auditoria = $request->post('txtAuditoria');
			$claveAuditoria = $request->post('txtClaveAuditoria');

			$oper = $request->post('txtOperacion');
			$tipo = $request->post('txtTipoAuditoria');
			$responsable = $request->post('txtResponsable');
			$objeto = $request->post('txtObjeto');

			$objetivo = $request->post('txtObjetivo');
			//$objetivo = strtoupper($objetivo);
 			$objetivo = preg_replace('/\&(.)[^;]*;/', '\\1', $objetivo);

			$alcance = $request->post('txtAlcance');
			//$alcance = strtoupper($alcance);
 			$alcance = preg_replace('/\&(.)[^;]*;/', '\\1', $alcance);

			$justificacion = $request->post('txtJustificacion');
			//$justificacion = strtoupper($justificacion);
 			$justificacion = preg_replace('/\&(.)[^;]*;/', '\\1', $justificacion);

			//$sector = $request->post('txtSector');
			//$subsector = $request->post('txtSubsector');

			///$miUnidad = explode("|", $request->post('txtUnidad') );

			///$sector = $miUnidad [0];
			///$subsector = $miUnidad [1];
			///$unidad = $miUnidad [2];

			$sector = "";
			$subsector = "";
			$unidad = "";

			$presupuesto = $request->post('txtPresupuesto');

			$etapa = $request->post('txtEtapa');

			if ($request->post('chkConAsf')) { $acompanamiento = 'on'; } else { $acompanamiento = ''; }

			$controlObjetos = $request->post('txtControlObjetos');

			$listaUnidades = $request->post('txtUnidadesSeleccionadas');

			$datos = 'cuenta=' . $cuenta . '<br>programa=' . $programa . '<br>area=' . $area . '<br>responsable=' . $responsable . '<br>tipo=' . $tipo . '<br>usrActual=' . $usrActual . '<br>sector=' . $sector . '<br>subsector=' . $subsector . '<br>unidad=' . $unidad . '<br>presupuesto=' . $presupuesto . '<br>acompanamiento=' . $acompanamiento . '<br>etapa=' . $etapa ;

			/*
			//----------------------------
			//  SECCION TEMPORAL ANTES QUE SE DE CUENTA JOSÉ
				$sql = "INSERT INTO sia_temporal (idAuditoria, datos) VALUES (:auditoria, :datos); ";
				$datos = 'cuenta=' . $cuenta . ' programa=' . $programa . ' area=' . $area . ' responsable=' . $responsable . ' tipo=' . $tipo . ' usrActual=' . $usrActual . ' sector=' . $sector . ' subsector=' . $subsector . ' unidad=' . $unidad . ' presupuesto=' . $presupuesto . ' acompanamiento=' . $acompanamiento . ' etapa=' . $etapa ;

				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':auditoria' => $auditoria, ':datos' => $datos));
			//----------------------------

			//----------------------------
			//  SECCION TEMPORAL ANTES QUE SE DE CUENTA JOSÉ
				$sql = "INSERT INTO sia_temporal (idAuditoria, datos) VALUES (:auditoria, :datos); ";
				$datos = 'objetivo=' . substr($objetivo,1,950) . ' alcance=' . substr($alcance,1,3000) . ' justificacion=' . substr($justificacion,1,4000) ;
				
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':auditoria' => $auditoria, ':datos' => $datos));
			//----------------------------

					
			//----------------------------
			//  SECCION TEMPORAL ANTES QUE SE DE CUENTA JOSÉ
				$sql = "INSERT INTO sia_temporal (idAuditoria, datos) VALUES (:auditoria, :datos); ";
				$datos = '$controlObjetos: ' . $controlObjetos ;

				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':auditoria' => $auditoria, ':datos' => $datos));
			//----------------------------
			
			*/

			//echo( $datos);

			if($oper=='INS'){

				$sql="INSERT INTO sia_auditorias (idCuenta, idPrograma, idArea, idResponsable, tipoAuditoria, objetivo, alcance, justificacion, usrAlta, fAlta, estatus, idSector, idSubsector, idUnidad, tipoPresupuesto, acompanamiento, idEtapa) VALUES (:cuenta, :programa, :area, :responsable, :tipo, :objetivo, :alcance, :justificacion, :usrActual, getdate(), 'ACTIVO', :sector, :subsector, :unidad, :presupuesto, :acompanamiento, :etapa); ";

				$dbQuery = $db->prepare($sql);

				$dbQuery->execute(array(':cuenta' => $cuenta, ':programa' => $programa,':area' => $area, ':responsable' => $responsable, ':tipo' => $tipo, ':objetivo' => $objetivo, ':alcance' => $alcance, ':justificacion' => $justificacion, ':usrActual' => $usrActual, ':sector' => $sector , ':subsector' => $subsector, ':unidad' => $unidad, ':presupuesto' => $presupuesto, ':acompanamiento' => $acompanamiento, ':etapa' => $etapa));

			}else{

				$sql="UPDATE sia_auditorias SET tipoAuditoria=:tipo, idArea=:area, idResponsable=:responsable, objetivo=:objetivo, alcance=:alcance, justificacion=:justificacion, usrModificacion=:usrActual, fModificacion=getdate(), idSector=:sector, idSubsector=:subsector, idUnidad=:unidad, tipoPresupuesto=:presupuesto, acompanamiento=:acompanamiento, idEtapa=:etapa WHERE idAuditoria=:auditoria";
				
				$dbQuery = $db->prepare($sql);
				
				$dbQuery->execute(array(':tipo' => $tipo, ':area' => $area, ':responsable' => $responsable, ':objetivo' => $objetivo, ':alcance' => $alcance, ':justificacion' => $justificacion, ':usrActual' => $usrActual, ':sector' => $sector, ':subsector' => $subsector, ':unidad' => $unidad, ':presupuesto' => $presupuesto, ':acompanamiento' => $acompanamiento, ':etapa' => $etapa, ':auditoria' => $auditoria));
				
				//echo "<hr>ACTUALIZA AUDITORIA: " . $auditoria;
			}
	
			if ($oper=='INS'){

				// Recupera el ID de la auditoria recien ingresada.

				$sql="SELECT MAX(idAuditoria) id FROM sia_auditorias WHERE idCuenta = :cuenta AND idPrograma = :programa AND tipoAuditoria = :tipoAuditoria AND idArea = :area AND idResponsable = :responsable AND idSector = :sector AND idSubsector = :subSector AND idUnidad = :unidad AND usrAlta = :usrAlta AND objetivo = :objetivo AND tipoPresupuesto = :tipoPresupuesto ";

				$dbQuery = $db->prepare($sql);

				$dbQuery->execute(array(':cuenta' => $cuenta, ':programa' => $programa, ':tipoAuditoria' => $tipo, ':area' => $area, ':responsable' => $responsable, ':sector' => $sector, ':subSector' => $subsector, ':unidad' => $unidad, ':usrAlta' => $usrActual, ':objetivo' => $objetivo, ':tipoPresupuesto' => $presupuesto));
				
				$result = $dbQuery->fetch(PDO::FETCH_ASSOC); 
				
				$auditoria = $result ['id']; 

			}

			// NUEVA SECCIÓN PARA GUARDAR LOS OBJETOS DE CUALQUIERA DE LOS TIPOS PERMITIDOS, ESTOS SE GUARDARON EN UN ARREGLO GENERAL
			// LLAMADO controObjetos Y SE PASO DEL PROGRAMA programas.php A hector.php POR MEDIO DEL CAMPO txtControlObjetos del Form 
			// de auditorias.

			if ($controlObjetos!="") {

				$registrosObjetos = explode('*', $controlObjetos);

				//echo('<br>count($registrosObjetos)=' . count($registrosObjetos) );

				if ( count($registrosObjetos) > 0 ){

					// Se inicia eliminando todos los objetos que pueda contener la auditoria.

					$sql="DELETE FROM sia_objetos WHERE idCuenta=:cuenta AND idPrograma=:programa AND idAuditoria=:auditoria ";
					$dbQuery = $db->prepare($sql);
					$dbQuery->execute(array(':cuenta'=>$cuenta, ':programa'=>$programa, ':auditoria'=>$auditoria ));
				}

				// Ahora se agregan a la tabla sia_objetos los distintos tipos de objetos que se hayan seleccionado en el combo txtObjeto
				foreach ($registrosObjetos as $registroObjetos) {
			    	$camposObjeto = explode("|", $registroObjetos);

					$nombreObjeto = $camposObjeto [1];
					$nivelObjeto  = $camposObjeto [2];
					$valorObjeto  = $camposObjeto [3] ;
					$tipoObjeto   = $camposObjeto [4] ;
					$rubros       = $camposObjeto [5] ;

					//echo('<br>$nombreObjeto=' . $nombreObjeto);


					$sql="INSERT INTO sia_objetos (idCuenta, idPrograma, idAuditoria, nombre, nivel, usrAlta, fAlta, estatus, valor, tipoObjeto, rubros) VALUES (:cuenta, :programa, :auditoria, :texto, :nivel, :usrActual, getdate(), 'ACTIVO', :valor, :tipo, :rubros );";
					
					$dbQuery = $db->prepare($sql);
					$dbQuery->execute(array(':cuenta'=>$cuenta, ':programa'=>$programa, ':auditoria'=>$auditoria, ':texto'=>$nombreObjeto, ':nivel'=>$nivelObjeto, 'usrActual'=> $usrActual, ':valor'=> $valorObjeto, ':tipo' => $tipoObjeto, ':rubros' => $rubros));
				}
			}

			if ($listaUnidades!=""){
				$registrosUnidades = explode('*', $listaUnidades);
				
				if ( count($registrosUnidades) > 0 ){

					// Ahora se agregan a la tabla sia_auditoriasunidades lass distintas unidades que se hayan seleccionado en el combo txtUnidades

					//echo('<br>$cuenta=' . $cuenta . '   $programa=' . $programa .  '   $auditoria=' . $auditoria );
					foreach ($registrosUnidades as $registroUnidades) {
				    	$camposUnidad = explode("|", $registroUnidades);

						$idSector = $camposUnidad[0];
						$idSubsector = $camposUnidad[1];
						$idUnidad = $camposUnidad[2];
						$nombreUnidad = $camposUnidad[3];

						//echo('<br>$idSector=' . $idSector . '   $idSubsector=' . $idSubsector .  '   $idUnidad=' . $idUnidad );
						//echo('<br>$nombreUnidad=' . $nombreUnidad);

						// Se verifica si ya existe la unidad en la auditoría.
						$sql="SELECT COUNT(*) total FROM sia_auditoriasunidades WHERE idCuenta=:cuenta AND idPrograma=:programa AND idAuditoria=:auditoria AND idSector = :sector AND idSubsector = :subSector AND idUnidad = :unidad ";
						$dbQuery = $db->prepare($sql);
						$dbQuery->execute(array(':cuenta'=>$cuenta, ':programa'=>$programa, ':auditoria'=>$auditoria, ':sector'=>$idSector, ':subSector'=>$idSubsector, ':unidad'=>$idUnidad ));

						$result = $dbQuery->fetch(PDO::FETCH_ASSOC); 
						$cenGestor = $result ['total']; 

						if($cenGestor == 0){
							$sql="INSERT INTO sia_auditoriasunidades (idCuenta, idPrograma, idAuditoria, idSector, idSubsector, idUnidad, usrAlta, fAlta, estatus) VALUES (:cuenta, :programa, :auditoria, :sector, :subSector, :unidad, :usrActual, getdate(), 'ACTIVO' );";
							
							$dbQuery = $db->prepare($sql);
							$dbQuery->execute(array(':cuenta'=>$cuenta, ':programa'=>$programa, ':auditoria'=>$auditoria, ':sector'=>$idSector, ':subSector'=>$idSubsector, ':unidad'=>$idUnidad, 'usrActual'=> $usrActual ));
						}
					}
				}				
			}

		}catch (Exception $e) {
			print "¡Error!: " . $e->getMessage() . "<br/>";
			die();
		}

		$app->redirect($app->urlFor('programas'));
	});

	$app->get('/tblUnidadesByAuditoria/:auditoria', function($auditoria)    use($app, $db) {
		$area = $_SESSION ["idArea"];
		$cuenta = $_SESSION ["idCuentaActual"];
		$cuenta1 = $_SESSION ["idCuentaActual"];
		$cuenta2 = $_SESSION ["idCuentaActual"];
		$programa = $_SESSION ["idProgramaActual"];
		$programa1 = $_SESSION ["idProgramaActual"];
		$auditoria1 = $auditoria;

		if($auditoria=='SIN_DEFINIR'){
		
			$sql="SELECT 'NO' asignado, au.idSector + '|' + au.idSubsector + '|' + au.idUnidad id , u.nombre texto FROM sia_areasunidades au " .
				" LEFT JOIN sia_unidades u ON au.idCuenta = u.idCuenta AND au.idSector = u.idSector AND au.idSubsector = u.idSubsector AND au.idUnidad = u.idUnidad ".
				" WHERE au.idCuenta = :cuenta AND au.idArea = :area ORDER BY u.nombre ";

			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':cuenta' => $cuenta, ':area' => $area));

			/*
			$sql="".
			"SELECT CASE WHEN (a.asignado IS NULL) THEN 'NO' ELSE a.asignado END asignado, u.idSector + u.idSubsector + u.idUnidad clave, u.nombre
			 FROM sia_unidades u LEFT JOIN (select b.idSector, b.idSubsector, b.idUnidad, 'SI' asignado 
                               				from (select * from sia_auditoriasunidades 
                                     			   where idCuenta=:cuenta and idPrograma=:programa ) b
                               				where b.idCuenta=:cuenta1 and b.idPrograma=:programa1 ) a
                    			ON u.idSector = a.idSector AND u.idSubsector = a.idSubsector and u.idUnidad = a.idUnidad
			 WHERE u.idCuenta = :cuenta2 ORDER BY u.idSector, u.idsubsector, u.idUnidad";

			$dbQuery->execute(array(':cuenta' => $cuenta, ':programa' => $programa, ':cuenta1' => $cuenta1, ':programa1' => $programa1, ':cuenta2' => $cuenta2));
			 */
			

		}else{

			$sql="".
			"SELECT CASE WHEN (a.asignado IS NULL) THEN 'NO' ELSE a.asignado END asignado, u.idSector + '|' + u.idSubsector + '|' + u.idUnidad id, RTRIM(LTRIM(u.nombre)) texto
			 FROM sia_unidades u LEFT JOIN (select b.idSector, b.idSubsector, b.idUnidad, 'SI' asignado 
                               				from (select * from sia_auditoriasunidades 
                                     			   where idCuenta=:cuenta and idPrograma=:programa and idAuditoria=:auditoria) b
                               				where b.idCuenta=:cuenta1 and b.idPrograma=:programa1 and b.idAuditoria=:auditoria1) a
                    			ON u.idSector = a.idSector AND u.idSubsector = a.idSubsector and u.idUnidad = a.idUnidad
			 WHERE u.idCuenta = :cuenta2 ORDER BY RTRIM(LTRIM(u.nombre)) asc";

			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':cuenta' => $cuenta, ':programa' => $programa, ':auditoria' => $auditoria, ':cuenta1' => $cuenta1, ':programa1' => $programa1, ':auditoria1' => $auditoria1, ':cuenta2' => $cuenta2));
		}
		
		$result ['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
		
	});


	$app->get('/lstObjetosSeleccionado/:auditoria/:objeto', function($auditoria, $objeto)    use($app, $db) {
		$cuenta = $_SESSION ["idCuentaActual"];
		$programa = $_SESSION ["idProgramaActual"];
		//$area = $_SESSION ["idArea"];
		
		$sql="SELECT ltrim(idObjeto) id, nombre texto FROM sia_objetos WHERE idCuenta=:cuenta and idAuditoria=:auditoria and idPrograma=:programa and idObjeto=:objeto and tipoObjeto = 'EGRESO' ;";
	
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':cuenta' => $cuenta, ':auditoria' => $auditoria, ':programa' => $programa, ':objeto' => $objeto));
		$result ['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});



	//Lista de Finalidades by sector, subsector y unidad
	$app->get('/lstFinalidadesByCuenta', function()    use($app, $db) {
		$cuenta = $_SESSION ["idCuentaActual"];
		
		$sql="SELECT ltrim(idFinalidad) id, nombre texto FROM sia_finalidades WHERE idCuenta=:cuenta ORDER BY nombre;";

		$dbQuery = $db->prepare($sql);

		$dbQuery->execute(array(':cuenta' => $cuenta));
		$result ['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});

		//Lista de Funciones by area y finalidades
	$app->get('/lstFuncionesByFinalidad/:finalidad', function($finalidad)    use($app, $db) {
		$cuenta = $_SESSION ["idCuentaActual"];
		
		$sql="SELECT ltrim(idFuncion) id, nombre texto FROM sia_funciones WHERE idCuenta=:cuenta and idFinalidad=:finalidad ORDER BY nombre;";

		$dbQuery = $db->prepare($sql);

		$dbQuery->execute(array(':cuenta' => $cuenta, ':finalidad' => $finalidad));
		$result ['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});


	//Lista de Subfunciones by  unidad y funcion
	$app->get('/lstSubFuncionesByFinalidadFuncion/:finalidad/:funcion', function($finalidad, $funcion)    use($app, $db) {
		$cuenta = $_SESSION ["idCuentaActual"];
		
		$sql="SELECT ltrim(idSubfuncion) id, nombre texto FROM sia_subfunciones WHERE idCuenta=:cuenta and idFinalidad=:finalidad and idFuncion=:funcion ORDER BY nombre;";

		$dbQuery = $db->prepare($sql);

		$dbQuery->execute(array(':cuenta' => $cuenta, ':finalidad' => $finalidad, ':funcion' => $funcion));
		$result ['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}

	});

	//Lista de Actividades by funcion y subfuncion
	$app->get('/lstActividadesByFinalidadFuncionSubfuncion/:finalidad/:funcion/:subfuncion', function($finalidad, $funcion, $subfuncion)    use($app, $db) {
		$cuenta = $_SESSION ["idCuentaActual"];
		
		$sql="SELECT ltrim(idActividad) id, nombre texto FROM sia_actividades WHERE idCuenta=:cuenta  and idFinalidad=:finalidad and idFuncion=:funcion and idSubfuncion = :subfuncion ORDER BY nombre;";

		$dbQuery = $db->prepare($sql);

		$dbQuery->execute(array(':cuenta' => $cuenta, ':finalidad' => $finalidad, ':funcion' => $funcion, ':subfuncion' => $subfuncion));
		$result ['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});

	//Lista de Capitulos por Cuenta 
	$app->get('/lstCapituloByCuenta', function()    use($app, $db) {
		$cuenta = $_SESSION ["idCuentaActual"];
		
		$sql="SELECT ltrim(idCapitulo) id, ltrim(idCapitulo) + ' - ' + nombre texto FROM sia_capitulos WHERE idCuenta=:cuenta ORDER BY idCapitulo, nombre;";

		$dbQuery = $db->prepare($sql);

		$dbQuery->execute(array(':cuenta' => $cuenta));
		$result ['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});


	//Lista de Conceptos Hijo por Concepto Padre
	$app->get('/lstConceptosHijosByConceptoPadre/:conceptoPadre', function($conceptoPadre)    use($app, $db) {
	
		$cuenta = $_SESSION ["idCuentaActual"];
		
		$sql="SELECT ltrim(idConceptoHijo) id, nombre texto FROM sia_conceptosHijos WHERE idCuenta=:cuenta and idConceptoPadre=:$conceptoPadre ORDER BY nombre;";

		$dbQuery = $db->prepare($sql);

		$dbQuery->execute(array(':cuenta' => $cuenta, ':conceptoPadre' => $conceptoPadre));
		$result ['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}

	});

	// Obtiene la lista de Partidas por Cuenta y Capitulo
	$app->get('/tblPartidasByCapitulo/:auditoria/:capitulo', function($auditoria,$capitulo)  use($app, $db) {

		$cuenta = $_SESSION ["idCuentaActual"];
		$cuenta1 = $_SESSION ["idCuentaActual"];
		$cuenta2 = $_SESSION ["idCuentaActual"];
		$programa = $_SESSION ["idProgramaActual"];
		$programa1 = $_SESSION ["idProgramaActual"];
		$capitulo1 = $capitulo;
		
		
		if($auditoria=="SIN_DEFINIR"){

			$sql="SELECT DISTINCT 'NO' as asignado, ltrim(p.idPartida) idPartida, p.nombre nombre FROM sia_partidas p LEFT JOIN (select o.* from sia_objetos o where o.idCuenta = :cuenta and o.idPrograma = :programa and valor = :capitulo and o.tipoObjeto = 'EGRESO' and o.nivel = 'PARTIDA') ox ON p.idCuenta = ox.idCuenta AND p.idPartida = SUBSTRING(ox.nombre,1,5) WHERE p.idCapitulo = :capitulo1 ";
			/*
			$sql="SELECT distinct 'NO' as asignado, ltrim(p.idPartida) idPartida, p.nombre nombre FROM sia_partidas p LEFT JOIN (select od.* from sia_objetosDetalles od inner join sia_objetos o on od.idObjeto = o.idObjeto where od.idCuenta = :cuenta and o.tipoObjeto = 'EGRESO') od ON p.IdCuenta = od.idCuenta and p.idPartida = od.idObjetoDetalle and od.idCuenta = :cuenta1 and od.idPrograma = :programa WHERE p.idCuenta = :cuenta2 and p.idCapitulo = :capitulo ";
			*/

			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array( ':cuenta' => $cuenta, ':programa' => $programa, ':capitulo' => $capitulo, ':capitulo1' => $capitulo1));
		}else{

			$sql="SELECT CASE WHEN (a.asignado IS NULL) THEN 'NO' ELSE a.asignado END asignado, a.idPartida idPartida, a.nombre nombre FROM (SELECT ltrim(p.idPartida) idPartida, p.nombre nombre, ox.asignado asignado FROM sia_partidas p LEFT JOIN ( select b.idCuenta, b.nombre, 'SI' asignado from (select o.* from sia_objetos o where o.idCuenta = :cuenta and o.idPrograma = :programa and o.idAuditoria = :auditoria and valor = :capitulo and o.tipoObjeto = 'EGRESO' and o.nivel = 'PARTIDA') b where b.idCuenta = :cuenta1 and b.idPrograma= :programa1  ) ox ON p.IdCuenta = ox.idCuenta AND p.idPartida = SUBSTRING(ox.nombre,1,5) WHERE p.idCuenta = :cuenta2 AND p.idCapitulo = :capitulo1) a ORDER BY a.idPartida  ";
			/*			
			$sql="SELECT CASE WHEN (a.asignado IS NULL) THEN 'NO' ELSE a.asignado END asignado, a.idPartida idPartida, a.nombre nombre FROM (SELECT ltrim(p.idPartida) idPartida, p.nombre nombre, od.asignado asignado from sia_partidas p LEFT JOIN ( select b.idCuenta, b.idObjetoDetalle, 'SI' asignado from (select od.* from sia_objetosDetalles od inner join sia_objetos o on od.idObjeto = o.idObjeto where od.idCuenta = :cuenta and o.tipoObjeto = 'EGRESO') b where b.idCuenta = :cuenta1 and b.idPrograma= :programa and b.idAuditoria = :auditoria ) od ON p.IdCuenta = od.idCuenta AND p.idPartida = od.idObjetoDetalle WHERE p.idCuenta = :cuenta2 AND p.idCapitulo = :capitulo) a ORDER BY a.idPartida ";
			*/

			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':cuenta' => $cuenta, ':programa' => $programa, ':auditoria' => $auditoria, ':capitulo' => $capitulo, ':cuenta1' => $cuenta1, ':programa1' => $programa1, ':cuenta2' => $cuenta2, ':capitulo1' => $capitulo1));
		}
	
		//$dbQuery->execute(array(':programa' => $programa, ':auditoria' => $auditoria, ':cuenta' => $cuenta, ':capitulo' => $capitulo));
		$result ['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});

	//Obtener el ID de la auditoria en base a varios campos para asegurar la recuperación

	$app->get('/lstAuditoriaBySeveralData/:valores', function($valores)    use($app, $db) {
		try{
			// Valores de Session
			$usrActual = $_SESSION ["idUsuario"];
			$cuenta = $_SESSION ["idCuentaActual"];
			$programa = $_SESSION ["idProgramaActual"];
			
			// Valores del arreglo enviado como parametro
			$aValores = explode("|", $valores);

			$tipoAuditoria = $aValores [0];
			$area = $aValores [1];
			$sector = $aValores [2];
			$subSector = $aValores [3];
			$unidad = $aValores [4];
			$objetivo = strtoupper($aValores [5]);
			//$alcance = $aValores [6];
			//$justificacion = $aValores [7];
			$tipoPresupuesto = $avalores [6];

			$sql="SELECT MAX(idAuditoria) id FROM sia_auditorias WHERE idCuenta = :cuenta AND idPrograma = :programa AND tipoAuditoria = :tipoAuditoria AND idArea = :area AND idSector = :sector AND idSubsector = :subSector AND idUnidad = :unidad AND usrAlta = :usrAlta AND objetivo = :objetivo AND tipoPresupuesto = :tipoPresupuesto ";

			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':cuenta' => $cuenta, ':programa' => $programa, ':tipoAuditoria' => $tipoAuditoria, ':area' => $area, ':sector' => $sector, ':subSector' => $subSector, ':unidad' => $unidad, ':usrAlta' => $usrActual, ':objetivo' => $objetivo, ':tipoPresupuesto' => $tipoPresupuesto));

			$result = $dbQuery->fetch(PDO::FETCH_ASSOC);
			
			echo json_encode($result);

		}catch (PDOException $e) {
			print "<br>¡Error en el TRY!: " . $e->getMessage();
			die();
		}
	});

	$app->get('/lstCuentasDetalle', function()    use($app, $db) {
		try{

			$sql="SELECT idCuentaDetalle id, unidad texto FROM sia_cuentasdetalles ORDER BY idCuenta, idCuentaDetalle ";

			$dbQuery = $db->prepare($sql);

			$dbQuery->execute();

			$result = $dbQuery->fetch(PDO::FETCH_ASSOC);	
			
			echo json_encode($result);

		}catch (PDOException $e) {
			print "<br>¡Error en el TRY!: " . $e->getMessage();
			die();
		}
	});

	//Lista de Etapas by idProceso
	$app->get('/lstEtapasByProceso', function()    use($app, $db) {
		
		$cuenta = $_SESSION ["idCuentaActual"];
		
		$sql="SELECT ltrim(idEtapa) id, ltrim(fase) + ' / ' + ltrim(nombre) texto FROM sia_etapas WHERE idProceso = 'AUDITORIAS' ORDER BY orden;";

		$dbQuery = $db->prepare($sql);

		$dbQuery->execute();

		$result ['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});

	$app->get('/lstUnidadesRespByArea', function()  use($app, $db) {

		$cuenta = $_SESSION ["idCuentaActual"];
		$area = $_SESSION ["idArea"];
		
		$sql="SELECT au.idSector + '|' + au.idSubsector + '|' + au.idUnidad id , u.nombre texto FROM sia_areasunidades au " .
			" LEFT JOIN sia_unidades u ON au.idCuenta = u.idCuenta AND au.idSector = u.idSector AND au.idSubsector = u.idSubsector AND au.idUnidad = u.idUnidad ".
			" WHERE au.idCuenta = :cuenta AND au.idArea = :area ORDER BY u.nombre ";

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':cuenta' => $cuenta, ':area' => $area));
		
		$result ['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}

	});

	$app->get('/lstSectorSubsectorByUnidad/:unidad', function($unidad)  use($app, $db) {

		$cuenta = $_SESSION ["idCuentaActual"];
		$area = $_SESSION ["idArea"];

		try{
			$sql="SELECT au.idSector sector, au.idSubsector subsector FROM sia_areasunidades au " .
				" LEFT JOIN sia_unidades u ON au.idCuenta = u.idCuenta AND au.idSector = u.idSector AND au.idSubsector = u.idSubsector AND au.idUnidad = u.idUnidad ".
				" WHERE au.idCuenta = :cuenta AND au.idArea = :area AND u.idUnidad = :unidad ";

			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':cuenta' => $cuenta, ':area' => $area, ':unidad' => $unidad));
			
			$result = $dbQuery->fetch(PDO::FETCH_ASSOC);

			echo json_encode($result);

		}catch (PDOException $e) {
			print "<br>¡Error en el TRY!: " . $e->getMessage();
			die();
		}
	});

	/*
	$app->get('/eliminarObjeto/:id/:auditoria', function($id, $auditoria)  use($app, $db) {

		$programa = $_SESSION ["idProgramaActual"];
		$cuenta = $_SESSION ["idCuentaActual"];

		try {
			
			if($auditoria <> "SIN_DEFINIR"){

				$sql="SELECT idObjeto, nombre, nivel, valor FROM sia_objetos WHERE idCuenta=:cuenta AND idPrograma=:programa AND idAuditoria=:auditoria AND idObjeto=:objeto AND tipoObjeto = 'EGRESO' ";

				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':cuenta' => $cuenta, ':programa' => $programa, ':auditoria' => $auditoria, ':objeto' => $id));
				
				$result = $dbQuery->fetch(PDO::FETCH_ASSOC);

				$nivel    = $result ['nivel']; 
				$capitulo = $result ['valor']; 


				if($nivel=="CAPITULO"){
						// PRIMERO: se recuperaran de la tabla objetos, todos los registros que tengan el mismo contenido en el campo valor (id de Capitulo)y "PARTIDA" 
						// en el campo nivel, para recuperar uno a uno los idObjeto y eliminarlos de la tabla sia_objetosDetalles al finalizar el ciclo se borrará todos
						// los registros de la tabla sia_objetos que tengan el mismo conenido en el campo valor que corresponde en este caso al id del Capitulo.
						

						$sql="SELECT ltrim(idObjeto) idObj FROM sia_objetos WHERE idCuenta=:cuenta AND idPrograma=:programa AND idAuditoria=:auditoria AND valor=:capitulo AND nivel='PARTIDA' ORDER BY idObjeto;";

						$dbQuery = $db->prepare($sql);
						$dbQuery->execute(array(':cuenta' => $cuenta, ':programa' => $programa, ':auditoria' => $auditoria, ':capitulo' => $capitulo));
						$result = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
						
						// Se procede a borrar las partidas de capitulo en la tabla ObjetosDetalle.

						foreach ($result as $unDato) {
							
							$idObjetoEliminar = $unDato ['idObj'];

							$sql="DELETE FROM sia_objetosDetalles WHERE idCuenta=:cuenta AND idPrograma=:programa AND idAuditoria=:auditoria AND idObjeto=:idObjeto ";
							$dbQuery = $db->prepare($sql);
							$dbQuery->execute(array(':cuenta'=>$cuenta, ':programa'=>$programa, ':auditoria'=>$auditoria, ':idObjeto'=>$idObjetoEliminar ));
						}
						// Se procede a borra todos los registros de la tabla sia_objetos que contengan el capitulo en el campo valor, no importa su nivel es "CAPITULO" o "PARTIDA".

						$sql="DELETE FROM sia_objetos WHERE idCuenta=:cuenta AND idPrograma=:programa AND idAuditoria=:auditoria AND valor=:idCapitulo ";
						$dbQuery = $db->prepare($sql);
						$dbQuery->execute(array(':cuenta'=>$cuenta, ':programa'=>$programa, ':auditoria'=>$auditoria, ':idCapitulo'=>$capitulo ));
				}else{

					if($nivel=="PARTIDA"){
						
						// Borrar la partida de la tabla sia_objetosDetalles usando el id (idObjeto).

						$sql="DELETE FROM sia_objetosDetalles WHERE idCuenta=:cuenta AND idPrograma=:programa AND idAuditoria=:auditoria AND idObjeto=:idObjeto ";
						$dbQuery = $db->prepare($sql);
						$dbQuery->execute(array(':cuenta'=>$cuenta, ':programa'=>$programa, ':auditoria'=>$auditoria, ':idObjeto'=>$id ));


						$sql="DELETE FROM sia_objetos WHERE idCuenta=:cuenta AND idPrograma=:programa AND idAuditoria=:auditoria AND idObjeto=:idObjeto ";
						$dbQuery = $db->prepare($sql);
						$dbQuery->execute(array(':cuenta'=>$cuenta, ':programa'=>$programa, ':auditoria'=>$auditoria, ':idObjeto'=>$id ));

					}else{ 
						//if ($nivel == "FUNCIÓN" || $nivel == "SUBFUNCIÓN" || $nivel == "ACTIVIDAD" || $nivel == "PARTIDA" ){
							// El campo nivel contiene los valores "FUNCION" o "SUBFUNCIO" o "ACTIVIDAD" o "PARTIDA"
							// y solo se eliminara dicho regitro de la tabla sia_objetos en base al id (idObjeto)

							$sql="DELETE FROM sia_objetos WHERE idCuenta=:cuenta AND idPrograma=:programa AND idAuditoria=:auditoria AND idObjeto=:idObjeto ";
							$dbQuery = $db->prepare($sql);
							$dbQuery->execute(array(':cuenta'=>$cuenta, ':programa'=>$programa, ':auditoria'=>$auditoria, ':idObjeto'=>$id ));
						//}
					}
				}
			}

		} catch (PDOException $e) {
			print "<br>¡Error de TRY-CATCH, en proceso: eliminarObjeto!: " . $e->getMessage();
			die();
		}
	});
	*/

	$app->get('/eliminarObjeto/:id/:auditoria', function($id, $auditoria)  use($app, $db) {

		$programa = $_SESSION ["idProgramaActual"];
		$cuenta = $_SESSION ["idCuentaActual"];

		try {
			
			if($auditoria <> "SIN_DEFINIR"){

				$sql="DELETE FROM sia_objetos WHERE idCuenta=:cuenta AND idPrograma=:programa AND idAuditoria=:auditoria AND idObjeto=:idObjeto ";
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':cuenta'=>$cuenta, ':programa'=>$programa, ':auditoria'=>$auditoria, ':idObjeto'=>$id ));
						
			}

		} catch (PDOException $e) {
			print "<br>¡Error de TRY-CATCH, en proceso: eliminarObjeto!: " . $e->getMessage();
			die();
		}
	});



	// Obtiene la lista de Gastos por Cuenta, programa y auditoria
	$app->get('/tblIngresosByAuditoria/:auditoria', function($auditoria)  use($app, $db) {

		$cuenta = $_SESSION ["idCuentaActual"];
		$cuenta1 = $_SESSION ["idCuentaActual"];
		$programa = $_SESSION ["idProgramaActual"];
		
		if($auditoria == "SIN_DEFINIR"){


			$sql="SELECT 'NO' as asignado, ltrim(ci.clave) clave, ci.nombre nombre, ci.tipo tipo, ci.origen origen FROM sia_cuentasingresos ci where ci.idCuenta = :cuenta ORDER BY ci.clave, ci.nivel ";

			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array( ':cuenta' => $cuenta));
		}else{

			$sql="SELECT CASE WHEN (a.asignado IS NULL) THEN 'NO' ELSE a.asignado END asignado, a.clave, a.nombre nombre, a.tipo, a.origen FROM (SELECT ltrim(ci.clave) clave, ci.nombre nombre, ci.tipo tipo, ci.origen origen, x.asignado FROM sia_cuentasingresos ci LEFT JOIN (select idCuenta, valor, 'SI' asignado from sia_objetos WHERE idCuenta = :cuenta and idPrograma= :programa and idAuditoria = :auditoria and tipoObjeto = 'INGRESO') x ON ci.idCuenta = x.idCuenta and ci.clave = x.valor WHERE ci.idCuenta = :cuenta1) a ORDER BY a.clave";

			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':cuenta' => $cuenta, ':programa' => $programa, ':auditoria' => $auditoria, ':cuenta1' => $cuenta1));
		}
	
		$result ['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});

	//Lista de Finalidades by sector, subsector y unidad
	$app->get('/lstTiposConsolidados', function()    use($app, $db) {
		
		$sql="SELECT ltrim(idTipoConsolidado) id, nombre texto FROM sia_tiposconsolidados ORDER BY idTipoConsolidado, nombre ";

		$dbQuery = $db->prepare($sql);

		$dbQuery->execute();

		$result ['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});

	//Lista de Consolidados por Tipo de Consolidados
	$app->get('/lstConsolidadosByTipoConsolidado/:tipoConsolidado', function($tipoConsolidado)    use($app, $db) {

		$sql="SELECT c.idConsolidado id, c.nombre texto FROM sia_consolidados c WHERE c.idTipoConsolidado = :tipoConsolidado ";

		$dbQuery = $db->prepare($sql);

		$dbQuery->execute(array(':tipoConsolidado' => $tipoConsolidado));

		$result ['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});

	// Tabla de consolidados por consolidado y por cuenta 
	$app->get('/tblConsolidadosDetalleByConsolidado/:auditoria/:sector/:subSector/:unidad/:consolidado', function($auditoria, $sector, $subSector, $unidad, $consolidado)    use($app, $db) {
		$cuenta = $_SESSION ["idCuentaActual"];
		$cuenta1 = $_SESSION ["idCuentaActual"];
		$programa = $_SESSION ["idProgramaActual"];

		if($auditoria == "SIN_DEFINIR"){
			$sql="SELECT DISTINCT 'NO' as asignado, tc.nombre tipoInformacion, c.nombre tipoDocumento, u.nombre Unidad, cd.nivel, cd.nombre rubro_concepto, cd.importe, cd.idConsolidadoDetalle consolidadoDetalle, cd.idConsolidado consolidado FROM sia_consolidadosdetalles cd LEFT JOIN (select idCuenta, valor, 'SI' asignado from sia_objetos where idCuenta = :cuenta and idPrograma= :programa  and tipoObjeto = 'CONSOLIDADO') o
	 			ON cd.idCuenta = o.idCuenta AND cd.idConsolidadoDetalle = o.valor 
				INNER JOIN sia_unidades u ON cd.idCuenta = u.idCuenta AND cd.idSector = u.idSector and cd.idSubsector = u.idSubsector and cd.idUnidad=u.idUnidad 
				INNER JOIN sia_consolidados c on cd.idConsolidado = c.idConsolidado 
				INNER JOIN sia_tiposconsolidados tc  ON tc.idTipoConsolidado = c.idTipoConsolidado
				WHERE cd.idCuenta = :cuenta1 AND c.idConsolidado = :consolidado AND cd.idSector = :sector AND cd.idSubsector = :subSector AND cd.idUnidad = :unidad ORDER BY c.nombre, cd.nivel ";

				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':cuenta' => $cuenta, ':programa' => $programa, ':cuenta1' => $cuenta1, ':consolidado' => $consolidado, ':sector' => $sector, ':subSector' => $subSector, ':unidad' => $unidad ));
		}else{
			$sql="SELECT CASE WHEN (o.asignado IS NULL) THEN 'NO' ELSE o.asignado END asignado, tc.nombre tipoInformacion, c.nombre tipoDocumento, u.nombre Unidad, cd.nivel, cd.nombre rubro_concepto, cd.importe, cd.idConsolidadoDetalle consolidadoDetalle, cd.idConsolidado consolidado FROM sia_consolidadosdetalles cd LEFT JOIN (select idCuenta, valor, 'SI' asignado from sia_objetos where idCuenta = :cuenta and idPrograma= :programa and idAuditoria = :auditoria and tipoObjeto = 'CONSOLIDADO') o ON cd.idCuenta = o.idCuenta AND cd.idConsolidadoDetalle = o.valor 
				INNER JOIN sia_unidades u ON cd.idCuenta = u.idCuenta AND cd.idSector = u.idSector and cd.idSubsector = u.idSubsector and cd.idUnidad=u.idUnidad 
				INNER JOIN sia_consolidados c on cd.idConsolidado = c.idConsolidado 
				INNER JOIN sia_tiposconsolidados tc  ON tc.idTipoConsolidado = c.idTipoConsolidado
				WHERE cd.idCuenta = :cuenta1 AND c.idConsolidado = :consolidado AND cd.idSector = :sector AND cd.idSubsector = :subSector AND cd.idUnidad = :unidad ORDER BY c.nombre, cd.nivel ";

				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':cuenta' => $cuenta, ':programa' => $programa, ':auditoria' => $auditoria, ':cuenta1' => $cuenta1, ':consolidado' => $consolidado, ':sector' => $sector, ':subSector' => $subSector, ':unidad' => $unidad ));
		}

		$result ['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});

	$app->get('/recuperaObjetosByAuditoria/:auditoria', function($auditoria)  use($app, $db) {

		$cuenta = $_SESSION ["idCuentaActual"];
		$programa = $_SESSION ["idProgramaActual"];
		/*
		$sql = "SELECT idObjeto id, nombre texto, nivel, valor, tipoObjeto, rubros FROM sia_objetos WHERE IdCuenta = :cuenta AND idPrograma = :programa AND idAuditoria = :auditoria ORDER BY tipoObjeto, nombre, nivel ";
		*/
		$sql = "SELECT idObjeto id, nombre texto, nivel, '10,000,000.00' valor, '11,000,000.00' tipoObjeto, '11,500,000.00' rubros FROM sia_objetos WHERE IdCuenta = :cuenta AND idPrograma = :programa AND idAuditoria = :auditoria ORDER BY tipoObjeto, nombre, nivel ";

		$dbQuery = $db->prepare($sql);
		
		$dbQuery->execute(array(':cuenta' => $cuenta, ':programa' => $programa, ':auditoria' => $auditoria));
	
		$result ['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON OBJETOS DE FISCALIZACIÓN. ");
		}else{
			echo json_encode($result);
		}
		
	});



	$app->get('/recuperaUnidadesByAuditoria/:auditoria', function($auditoria)  use($app, $db) {

		$cuenta = $_SESSION ["idCuentaActual"];
		$programa = $_SESSION ["idProgramaActual"];

		$sql = "SELECT u.idSector + '|' + u.idSubsector + '|' + u.idUnidad id, u.nombre texto FROM sia_auditoriasunidades au INNER JOIN sia_unidades u ON au.idCuenta = u.idCuenta AND au.idSector = u.idSector AND au.idSubsector = u.idSubsector AND au.idUnidad = u.idUnidad WHERE au.idCuenta = :cuenta AND au.idPrograma = :programa AND au.idAuditoria = :auditoria ORDER BY u.nombre ASC ";
		
		$dbQuery = $db->prepare($sql);
		
		$dbQuery->execute(array(':cuenta' => $cuenta, ':programa' => $programa, ':auditoria' => $auditoria));
	
		$result ['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON OBJETOS DE FISCALIZACIÓN. ");
		}else{
			echo json_encode($result);
		}
		
	});

	$app->get('/recuperaUnidadByAuditoria/:auditoria/:centroGestor', function($auditoria, $centroGestor)  use($app, $db) {

		$cuenta = $_SESSION ["idCuentaActual"];
		$programa = $_SESSION ["idProgramaActual"];

		$sql = "SELECT au.idCuenta, au.idPrograma, au.idAuditoria, au.idSector, au.idSubsector, au.idUnidad, u.nombre texto, au.fConfronta fechaConfronta, au.resumenConfronta 
				FROM sia_auditoriasunidades au INNER JOIN sia_unidades u ON au.idCuenta = u.idCuenta AND au.idSector = u.idSector 
				AND au.idSubsector = u.idSubsector AND au.idUnidad = u.idUnidad 
				WHERE au.idCuenta = :cuenta AND au.idPrograma = :programa AND au.idAuditoria = :auditoria  
				AND au.idSector + '|' + au.idSubsector + '|' + au.idUnidad = :centroGestor ;";
				//AND au.fConfronta is not null;";
		
		$dbQuery = $db->prepare($sql);
		
		$dbQuery->execute(array(':cuenta' => $cuenta, ':programa' => $programa, ':auditoria' => $auditoria, ':centroGestor' => $centroGestor));
	
		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON OBJETOS DE FISCALIZACIÓN. ");
		}else{
			echo json_encode($result);
		}
		
	});

	$app->get('/eliminarUnidadByAuditoria/:auditoria/:centroGestor', function($auditoria, $centroGestor)  use($app, $db) {

		$cuenta = $_SESSION ["idCuentaActual"];
		$programa = $_SESSION ["idProgramaActual"];

		$sql = "DELETE FROM sia_auditoriasunidades WHERE idCuenta = :cuenta AND idPrograma = :programa AND idAuditoria = :auditoria  
				AND idSector + '|' + idSubsector + '|' + idUnidad = :centroGestor ";
		
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':cuenta' => $cuenta, ':programa' => $programa, ':auditoria' => $auditoria, ':centroGestor' => $centroGestor));
	
		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON OBJETOS DE FISCALIZACIÓN. ");
		}else{
			echo json_encode($result);
		}
		
	});


	// LAS SIGUIENTES FUNCIONES SON PARA LA ACTUALIZACION DEL CATALOGO DE USUARIOS 

		//Obtener un empleado
	$app->get('/empleadoByRPE_HVS/:id', function($id)    use($app, $db) {
		$sql="SELECT isnull(u.saludo,'') saludo, isnull(u.idUsuario,'') idUsuario, isnull(e.idEmpleado,'') idEmpleado, isnull(e.idNombramiento,'') tipo, isnull(e.nombre,'') nombre, isnull(e.paterno,'') paterno, isnull(e.materno,'') materno, isnull(e.iniciales,'') iniciales, isnull(e.idArea,'') idArea, isnull(e.idNivel, '') idNivel, isnull(u.correo,'') correo, isnull(u.telefono,'') telefono, isnull(u.usuario,'') usuario, isnull(u.pwd,'') pwd, isnull(e.estatus,'') estatus, (SELECT isnull(MAX(cu.idCuenta),'') cuenta from sia_cuentausuario cu WHERE cu.idUsuario = u.idUsuario and cu.predeterminada = 'SI') cuenta FROM sia_empleados e LEFT JOIN sia_usuarios u on u.idEmpleado=e.idEmpleado WHERE e.idEmpleado=:id ";

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':id' => $id));
		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);
		echo json_encode($result);		
	});	

	//Obtener un usuario
	$app->get('/usuario_HVS/:id',  function($id)  use($app, $db) {
		$id = (int)$id;
		$sql= "SELECT isnull(u.idUsuario,'') idUsuario, isnull(u.saludo,'') saludo, isnull(u.idEmpleado, '') idEmpleado, isnull(u.tipo,'') tipo, isnull(u.nombre,'') nombre, isnull(u.paterno,'') paterno, isnull(u.materno, '') materno, isnull(e.iniciales,'') iniciales, isnull(u.idArea,'') idArea, isnull(e.idNivel, '') idNivel, isnull(u.correo,'') correo, isnull(u.telefono, '') telefono, isnull(u.usuario,'') usuario, isnull(u.pwd,'') pwd, isnull(u.estatus,'') estatus, (SELECT isnull(MAX(cu.idCuenta),'') cuenta from sia_cuentausuario cu WHERE cu.idUsuario = u.idUsuario and cu.predeterminada = 'SI') cuenta FROM sia_usuarios u LEFT JOIN sia_empleados e ON u.idEmpleado = e.idEmpleado WHERE u.idUsuario=:id ";
		
		$dbQuery = $db->prepare($sql);		
		$dbQuery->execute(array(':id' => $id));
		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "RECURSO NO ENCONTRADA.");			
		}else{		
			echo json_encode($result);
		}		
	});

	$app->get('/lstCuentas', function()    use($app, $db) {
		$sql=" SELECT idCuenta id, nombre texto FROM sia_cuentas ORDER BY anio ";

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute();
		$result ['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});


	$app->get('/lstNiveles', function()    use($app, $db) {
		$sql="SELECT distinct idNivel id, concat('NIVEL ', idNivel) texto FROM sia_empleados WHERE idNivel <> '' ORDER BY  1";

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute();
		$result ['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});

	$app->get('/lstNombramientos', function()    use($app, $db) {
		$sql="SELECT idNombramiento id, nombre texto FROM sia_nombramientos ORDER BY  1";

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute();
		$result ['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});


	//Obtener los roles asignados a un usuario
	$app->get('/lstRolesByUsuario/:id',  function($id)  use($app, $db) {
		$id = (int)$id;
		$sql= "SELECT isnull(ur.idRol,'') id, isnull(r.nombre, '') texto FROM sia_usuariosroles ur INNER JOIN sia_roles r ON ur.idRol = r.idRol WHERE ur.idUsuario=:id ";
		
		$dbQuery = $db->prepare($sql);		
		$dbQuery->execute(array(':id' => $id));
		$result ['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "RECURSO NO ENCONTRADA.");			
		}else{		
			echo json_encode($result);
		}		
	});

	//Obtener los roles asignados a un usuario
	$app->get('/roleByUsuario/:usuario/:rol',  function($usuario, $rol)  use($app, $db) {
		$usuario = (int)$usuario;
		$sql= "SELECT CASE WHEN (usu.total > 0) THEN 'SI' ELSE 'NO' END encontrado FROM (SELECT count(*) total FROM sia_usuariosroles WHERE idUsuario=:usuario AND idRol = :rol) usu;";
		
		$dbQuery = $db->prepare($sql);		
		$dbQuery->execute(array(':usuario' => $usuario, ':rol' => $rol));
		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);

		echo( $result ['encontrado'] ); 
	});


	$app->get('/validaExisteRPE/:rpe', function($rpe)  use($app, $db) {
		try
		{
			$sql = "SELECT COUNT(*) total FROM sia_empleados WHERE idEmpleado=:rpe";

			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':rpe'=>$rpe ));

			$result = $dbQuery->fetch(PDO::FETCH_ASSOC);

			if(!$result){
				$app->halt(404, "NO SE ENCONTRARON DATOS ");
			}else{
				echo json_encode($result);
			}
		 }catch (Exception $e) {
		  	print "ERROR: " . $e->getMessage() . "<br><br> QUERY: " . $dbQuery ;
	  		die();
		}
	});

	$app->get('/validaExisteUsuarioByEmpleado/:rpe', function($rpe)  use($app, $db) {
		try
		{
			$sql = "SELECT COUNT(*) total FROM sia_usuarios WHERE idEmpleado=:rpe";

			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':rpe'=>$rpe ));

			$result = $dbQuery->fetch(PDO::FETCH_ASSOC);

			if(!$result){
				$app->halt(404, "NO SE ENCONTRARON DATOS ");
			}else{
				echo json_encode($result);
			}
		 }catch (Exception $e) {
		  	print "ERROR: " . $e->getMessage() . "<br><br> QUERY: " . $dbQuery ;
	  		die();
		}
	});


	$app->post('/guardar/usuario_HVS', function()  use($app, $db) {
		
		$request=$app->request;

		$usrActual = $_SESSION ["idUsuario"];

		//echo("txtNombre: " . strtoupper($request->post('txtNombre')) );
		
		try {

			$oper = $request->post('operacion');
			$id = $request->post('txtID');
			$tipo = $request->post('txtTipo');
			$empleado = $request->post('txtRPE');
			$saludo = strtoupper($request->post('txtSaludo'));
			$nombre = strtoupper($request->post('txtNombre'));
			$paterno = strtoupper($request->post('txtPaterno'));
			$materno = strtoupper($request->post('txtMaterno'));
			$iniciales = strtoupper($request->post('txtIniciales'));
			$area = $request->post('txtArea');
			$nivel = $request->post('txtNivel');
			$correo = $request->post('txtCorreo');
			$telefono = $request->post('txtTelefono');
			$usuario = $request->post('txtCuenta');
			$pwd = $request->post('txtPassword');
			$cuenta = $request->post('txtCuentaPublica');
			$estatus = $request->post('txtEstatus');

			$rolesUsuario = $request->post('txtRolesUsuario');

    		//$datos = 'oper: ' . $oper . '<br>id: ' . $id . '<br>tipo: ' . $tipo . '<br>empleado: '.$empleado.'<br>saludo: '.$saludo.'<br>nombre: ' . $nombre . '<br>paterno: ' . $paterno . '<br>materno: ' . $materno . '<br>area: ' . $iniciales . '<br>inifiales' . $area . '<br>correo: ' . $correo . '<br>Nivel: ' . $nivel . '<br>telefono: ' . $telefono . '<br>usuario: ' . $usuario . '<br>pwd: ' . $pwd . '<br>cuenta: ' . $cuenta . '<br>estatus: ' . $estatus . '<br>rolesUsuario: '. $rolesUsuario ;

			//echo("Datos: " . $datos);

			if ($oper=='INS'){
				
				// Con el cambio solicitado por José ahora hay que guardar siempre el empleado al igual que el usuario, pero el empleado 
				// primero 
				// Si el tipo de usuario es Honorarios o profis 
				if($tipo=='HS' || $tipo=='PR'){
					// Si es usuario tipo Honorarios o profis, también se deben agregar a la tabla empledos, solo que después del RPE (idEmpleado) 5000, por lo que será necesario buscar el último número mayor a 5000 almacenado en la tabla sia_empelados y sumarle uno )
					$dbQuery = $db->prepare("SELECT MAX(idEmpleado)+1 empleado from sia_empleados ");
					$dbQuery->execute();				
					$result = $dbQuery->fetch(PDO::FETCH_ASSOC); 
					$empleado = $result ['empleado']; 
				}

				$sql = "INSERT INTO sia_usuarios (tipo, saludo, idEmpleado, nombre, paterno, materno, idArea, correo, telefono, usuario, pwd, usrAlta, fAlta, estatus) ".
				"VALUES(:tipo, :saludo, :empleado, :nombre, :paterno, :materno, :area, :correo, :telefono, :usuario, :pwd, :usrActual, getdate(), :estatus); ";		
				$dbQuery = $db->prepare($sql);	
				
				$dbQuery->execute(array(':tipo'=> $tipo, ':saludo'=> $saludo, ':empleado'=> $empleado, ':nombre'=> $nombre, ':paterno'=> $paterno, ':materno'=> $materno, ':area' => $area, ':correo'=> $correo, ':telefono'=> $telefono, ':usuario'=> $usuario,  ':pwd'=> $pwd, ':usrActual'=> $usrActual, ':estatus'=> $estatus));		

				// Ahora inserta el empleado
				
				// Confirma si ya existe el empleado en la tabla sia_empleados.
				$dbQuery = $db->prepare("SELECT COUNT(*) total from sia_empleados WHERE idEmpleado = :empleado ");
				$dbQuery->execute(array(':empleado' => $empleado));				
				$result = $dbQuery->fetch(PDO::FETCH_ASSOC); 
				$total = $result ['total']; 

				if($total > 0){
					
					$dbQuery = $db->prepare("UPDATE sia_empleados SET idNivel=:nivel iniciales=:iniciales WHERE idEmpleado=:empleado ");	
					$dbQuery->execute(array(':nivel'=> $nivel, ':iniciales'=> $iniciales, ':empleado'=> $empleado));		

				}else{		

					$sql = "INSERT INTO sia_empleados (idEmpleado, nombre, paterno, materno, iniciales, idArea, idNivel, idNombramiento, usrAlta, fAlta, estatus) ".
					"VALUES(:empleado, :nombre, :paterno, :materno, :iniciales, :area, :nivel, :nombramiento, :usrActual, getdate(), :estatus); ";		
					$dbQuery = $db->prepare($sql);	
					
					$dbQuery->execute(array(':empleado'=> $empleado, ':nombre'=> $nombre, ':paterno'=> $paterno, ':materno'=> $materno, ':iniciales'=> $iniciales, ':area' => $area, ':nivel'=> $nivel, ':nombramiento' => $tipo , ':usrActual'=> $usrActual, ':estatus'=> $estatus));		
				}

			}else{

				$sql = "UPDATE sia_usuarios " . 
				"SET tipo=:tipo, saludo=:saludo, idEmpleado=:empleado, nombre=:nombre, paterno=:paterno, materno=:materno, idArea=:area, correo=:correo, telefono=:telefono, usuario=:usuario, pwd=:pwd, usrModificacion=:usrModificacion, fModificacion= getdate(), estatus=:estatus ".
				"WHERE idUsuario=:id";		
				$dbQuery = $db->prepare($sql);		
				$dbQuery->execute(array(':tipo'=> $tipo, ':saludo'=> $saludo, ':empleado'=> $empleado, ':nombre'=> $nombre,':paterno'=> $paterno, ':materno'=> $materno, ':area' => $area, ':correo' => $correo, ':telefono'=> $telefono, ':usuario'=> $usuario,':pwd'=> $pwd, ':usrModificacion'=> $usrActual,':estatus' => $estatus, ':id' => $id));				
				
				// Si el usuario esta ligado a un empleado, se actualizará a este último el nivel
				if($empleado>0){
					
					$dbQuery = $db->prepare("UPDATE sia_empleados SET idNivel=:nivel, idArea=:area, iniciales=:iniciales WHERE idEmpleado=:empleado ");	
					$dbQuery->execute(array(':nivel'=> $nivel, ':area'=> $area, 'iniciales'=> $iniciales ,':empleado'=> $empleado));		

				}
			}
			
			// Si fue insertar se debe recuperar el valor del campo idUsuario recien ingresado
			if ($oper=='INS'){
				$sql="SELECT MAX(idUsuario) id FROM sia_usuarios WHERE tipo=:tipo AND saludo=:saludo AND nombre=:nombre AND paterno=:paterno AND materno=:materno AND usuario=:usuario AND pwd=:pwd ";
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':tipo'=> $tipo, ':saludo'=> $saludo, ':nombre'=> $nombre,':paterno'=> $paterno, ':materno'=> $materno, ':usuario'=> $usuario,':pwd'=> $pwd));				
				$result = $dbQuery->fetch(PDO::FETCH_ASSOC); 
				$id = $result ['id']; 
			}

			// - Se debe revisar si existe el usuario con la cuenta pública ya dado de alta si no agregarlo
			// echo("Operacion: ".$oper." valor de id: ".$id);

			if ($id != ""){
				$sql = "SELECT COUNT(*) total FROM sia_cuentausuario WHERE idCuenta=:cuenta AND idUsuario=:id ";
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':cuenta'=> $cuenta, ':id'=> $id));				
				$result = $dbQuery->fetch(PDO::FETCH_ASSOC); 
				$total = $result ['total']; 

				//echo("Total: ".$total);

				if($total == 0){

					$sql = "INSERT INTO sia_cuentausuario (idCuenta, idUsuario, predeterminada, estatus, usrAlta, fAlta) ".
					"VALUES(:cuenta, :id, 'SI', 'ACTIVO', :usrActual, getdate()); ";			

					$dbQuery = $db->prepare($sql);	
				
					$dbQuery->execute(array(':cuenta'=> $cuenta, ':id'=> $id, ':usrActual'=> $usrActual));		
				}else{
					$sql = "UPDATE sia_cuentausuario SET predeterminada='NO', usrModificacion=:usrModificacion, fModificacion=GETDATE() WHERE predeterminada='SI' and idUsuario=:id;";			
					$dbQuery = $db->prepare($sql);	
					$dbQuery->execute( array(':usrModificacion'=>$usrActual, ':id'=>$id) );		

					$sql = "UPDATE sia_cuentausuario SET predeterminada='SI', usrModificacion=:usrModificacion, fModificacion=GETDATE() WHERE idCuenta=:cuenta and idUsuario=:id;";			
					$dbQuery = $db->prepare($sql);	
					$dbQuery->execute( array(':usrModificacion'=> $usrActual, ':cuenta'=> $cuenta, ':id'=> $id) );		
				}
			}

			// - Se debe verificar si se han seleccionado roles para el usuario, de ser así, Se deben borrar los roles que tiene asignanados
			//  el usuario y agregar los nuevos.


			if ($rolesUsuario != ""){

				$registrosRoles = explode('*', $rolesUsuario);

				///echo('<br>count($registrosObjetos)='+ count($registrosObjetos) );

				if ( count($registrosRoles) > 0 ){

					// Se inicia eliminando todos los roles que pueda tener el usuario.

					$sql = " DELETE FROM sia_usuariosroles WHERE idUsuario = :id ";
					$dbQuery = $db->prepare($sql);
					$dbQuery->execute(array(':id'=>$id ));

					// Ahora se agregan a la tabla sia_usuariosroles los roles que se hayan seleccionado para el usuario.
					foreach ($registrosRoles as $registroRoles) {
				    	$camposRol = explode("|", $registroRoles);

						$idRol 		= $camposRol [0];
						$nombreRol	= $camposRol [1];

						//echo("id: " . $id . "  idRol: " . $idRol );

						$sql = "INSERT INTO sia_usuariosroles (idUsuario, idRol, usrAlta, fAlta, estatus) VALUES (:id, :idRol, :usrActual, getdate(), 'ACTIVO') ;";

						$dbQuery = $db->prepare($sql);
						$dbQuery->execute(array(':id'=>$id, ':idRol'=>$idRol, ':usrActual'=> $usrActual));
					}
				}
			}

		}catch (Exception $e) {
			print "¡Error!: " . $e->getMessage() . "<br/>";
			die();
		}

		$app->redirect($app->urlFor('catUsuarios'));
	});	

	// LAS SIGUIENTES FUNCIONES SON PARA LA ACTUALIZACION DEL CATALOGO DE ROLES 

	$app->get('/catRoles',  $autenticacionrole, function()   use($app, $db) {
		$dbQuery = $db->prepare( "Select idRol id, nombre, estatus FROM sia_roles Order by idRol;" );		
		$dbQuery->execute();
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			$app->render('catRoles.php', $result);
		}	
	})->name('catRoles');
	

	//Obtener un rol 
	$app->get('/rol_HVS/:id',  function($id)  use($app, $db) {
		//$id = (int)$id;
		$sql= "SELECT idRol rol, isnull(nombre,'') nombre, isnull(estatus,'') estatus FROM sia_roles WHERE idRol=:id ";
		
		$dbQuery = $db->prepare($sql);		
		$dbQuery->execute(array(':id' => $id));
		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "RECURSO NO ENCONTRADA.");			
		}else{		
			echo json_encode($result);
		}		
	});

	$app->get('/validaExisteRol/:rol', function($rol)  use($app, $db) {
		try
		{
			$sql = "SELECT COUNT(*) total FROM sia_roles WHERE idRol=:rol";

			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':rol'=>$rol ));

			$result = $dbQuery->fetch(PDO::FETCH_ASSOC);

			if(!$result){
				$app->halt(404, "NO SE ENCONTRARON DATOS ");
			}else{
				echo json_encode($result);
			}
		 }catch (Exception $e) {
		  	print "ERROR: " . $e->getMessage() . "<br><br> QUERY: " . $dbQuery ;
	  		die();
		}
	});

	$app->get('/lstModulos_HVS',  function()  use($app, $db) {

		$sql= "SELECT idModulo id, isnull(panel + ' / ' + upper(nombre), '') texto FROM sia_MODULOS ORDER BY panel, idModulo";
		
		$dbQuery = $db->prepare($sql);		
		$dbQuery->execute();
		$result ['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "RECURSOS NO ENCONTRADOS.");			
		}else{		
			echo json_encode($result);
		}		
	});

	//Obtener los módulos asignados a un rol
	$app->get('/lstModulosByRol/:idRol',  function($idRol)  use($app, $db) {
		//$id = (int)$id;
		$sql= "SELECT rm.idModulo id, isnull(panel + ' / ' + upper(nombre), '') texto FROM sia_rolesmodulos rm INNER JOIN sia_modulos m ON rm.idModulo = m.idModulo WHERE rm.idRol=:idRol ";
		
		$dbQuery = $db->prepare($sql);		
		$dbQuery->execute(array(':idRol' => $idRol));
		$result ['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "RECURSO NO ENCONTRADA.");			
		}else{		
			echo json_encode($result);
		}		
	});


	//Obtener los reportes asignados a un rol
	$app->get('/lstReportesByRol/:idRol',  function($idRol)  use($app, $db) {
		//$id = (int)$id;
		$sql= "SELECT rr.idReporte id, isnull(r.nombre, '') texto FROM sia_rolesreportes rr INNER JOIN sia_reportes r ON rr.idReporte = r.idReporte WHERE rr.idRol=:idRol ";
		
		$dbQuery = $db->prepare($sql);		
		$dbQuery->execute(array(':idRol' => $idRol));
		$result ['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "RECURSO NO ENCONTRAO.");			
		}else{		
			echo json_encode($result);
		}		
	});

	$app->get('/lstReportes_HVS',  function()  use($app, $db) {

		$sql= "SELECT idReporte id, isnull(nombre, '') texto FROM sia_REPORTES ORDER BY nombre";
		
		$dbQuery = $db->prepare($sql);		
		$dbQuery->execute();
		$result ['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "RECURSOS NO ENCONTRADOS.");			
		}else{		
			echo json_encode($result);
		}		
	});


	$app->post('/guardar/rol_HVS', function()  use($app, $db) {
		
		$request=$app->request;

		$usrActual = $_SESSION ["idUsuario"];
		
		
		//echo("txtNombre: " . strtoupper($request->post('txtNombre')) );
		
		try {

			$oper = $request->post('txtOperacion');
			$id = strtoupper($request->post('txtRol'));
			$nombre = strtoupper($request->post('txtNombre'));
			$estatus = $request->post('txtEstatus');

			$modulosRol = $request->post('txtModulosRol');
			$reportesRol = $request->post('txtReportesRol');

    		$datos = 'oper: ' . $oper . '<br>id: ' . $id .'<br>nombre: ' . $nombre . '<br>estatus: ' . $estatus . '<br>modulosRol: '. $modulosRol . '<br>reportesRol: '. $reportesRol ;

			//echo("Datos: " . $datos);

			if ($oper=='INS'){
				$sql = "INSERT INTO sia_roles (idRol, nombre, usrAlta, fAlta, estatus) VALUES(:idRol, :nombre, :usrActual, getdate(), :estatus); ";		
				$dbQuery = $db->prepare($sql);	
				
				$dbQuery->execute(array(':idRol'=> $id, ':nombre'=> $nombre, ':usrActual'=> $usrActual, ':estatus'=> $estatus));		
				 
			}else{
				$sql = "UPDATE sia_roles SET nombre=:nombre, usrModificacion=:usrModificacion, fModificacion=getdate(), estatus=:estatus WHERE idRol=:idRol";		
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array( ':nombre'=> $nombre, ':usrModificacion'=> $usrActual,':estatus' => $estatus, ':idRol' => $id));
			}
			
			// - Se verificará si se han seleccionado módulos para el rol, de ser así, Se borraran los módulos que el rol tiene asignanados
			//   y agregar los nuevos módulos.

			//echo("<br>id: "+ $id);

			if ($modulosRol != ""){

				$registrosModulos = explode('*', $modulosRol);

				//echo('<br>count($registrosModulos)='+ count($registrosModulos) );

				if ( count($registrosModulos) > 0 ){

					// Se inicia eliminando todos los módulos que pueda tener el rol.

					$sql = " DELETE FROM sia_rolesmodulos WHERE idRol = :id ";
					$dbQuery = $db->prepare($sql);
					$dbQuery->execute(array(':id'=>$id ));

					// Ahora se agregan a la tabla sia_rolesmodulos los módulos que se hayan seleccionado para el rol.
					
					foreach ($registrosModulos as $registroModulos) {
				    	$camposModulo = explode("|", $registroModulos);

						$idModulo 		= $camposModulo [0];
						$nombreModulo	= $camposModulo [1];

						//echo("<br>id: " . $id . "  idModulo: " . $idModulo . " nombreModulo: " . $nombreModulo);

						$sql = "INSERT INTO sia_rolesmodulos (idRol, idModulo, usrAlta, fAlta, estatus) VALUES (:idRol, :idModulo, :usrActual, getdate(), 'ACTIVO') ;";

						$dbQuery = $db->prepare($sql);
						$dbQuery->execute(array(':idRol'=>$id, ':idModulo'=>$idModulo, ':usrActual'=> $usrActual));
					}
				}
			}

			// - Se verificará si se han seleccionado módulos para el rol, de ser así, Se borraran los módulos que el rol tiene asignanados
			//   y agregar los nuevos módulos.

			if ($reportesRol != ""){

				$registrosReportes = explode('*', $reportesRol);

				//echo('<br>count($registrosReportes)='+ count($registrosReportes) );

				if ( count($registrosReportes) > 0 ){

					// Se inicia eliminando todos los módulos que pueda tener el rol.

					$sql = " DELETE FROM sia_rolesreportes WHERE idRol = :id ";
					$dbQuery = $db->prepare($sql);
					$dbQuery->execute(array(':id'=>$id ));

					// Ahora se agregan a la tabla sia_rolesreportes los reportes que se hayan seleccionado para el rol.
					
					foreach ($registrosReportes as $registroReportes) {
				    	$camposReporte = explode("|", $registroReportes);

						$idReporte 		= $camposReporte [0];
						$nombreReporte	= $camposReporte [1];

						//echo("<br>id: " . $id . "  idReporte: " . $idReporte . " nombreReporte: " . $nombreReporte);

						$sql = "INSERT INTO sia_rolesreportes (idRol, idReporte, usrAlta, fAlta, estatus) VALUES (:idRol, :idReporte, :usrActual, getdate(), 'ACTIVO') ;";

						$dbQuery = $db->prepare($sql);
						$dbQuery->execute(array(':idRol'=>$id, ':idReporte'=>$idReporte, ':usrActual'=> $usrActual));
					}
				}
			}

		}catch (Exception $e) {
			print "¡Error!: " . $e->getMessage() . "<br/>";
			die();
		}

		$app->redirect($app->urlFor('catRoles'));
	});	

	// LAS SIGUIENTES FUNCIONES SON PARA LA ACTUALIZACION DEL CATALOGO DE MODULOS 

	$app->get('/catModulos',  $autenticacionrole, function()   use($app, $db) {
		$dbQuery = $db->prepare( "Select m.idmodulo id, isnull(upper(m.nombre),'') nombre , isnull(m.tipo,'') tipo, isnull(m.panel,'') panel, isnull(m.liga,'') liga, isnull(m.icono,'') icono, isnull(m.orden,'') orden, isnull(m.estatus,'') estatus FROM sia_modulos m Order by nombre;" );	
		$dbQuery->execute();
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			$app->render('catModulos.php', $result);
		}	
	})->name('catModulos');

	//Obtener un modulo
	$app->get('/modulo_HVS/:id',  function($id)  use($app, $db) {
		//$id = (int)$id;
		$sql= "SELECT idModulo modulo, isnull(nombre,'') nombre, isnull(tipo,'') tipo, isnull(panel,'') panel, isnull(liga,'') liga, isnull(icono,'') icono, isnull(orden,'') orden, isnull(estatus,'') estatus FROM sia_modulos WHERE idModulo=:id ";
		
		$dbQuery = $db->prepare($sql);		
		$dbQuery->execute(array(':id' => $id));
		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);
		
		if(!$result){
			$app->halt(404, "RECURSO NO ENCONTRADA.");			
		}else{		
			echo json_encode($result);
		}		
	});

	$app->get('/validaExisteModulo/:modulo', function($modulo)  use($app, $db) {
		try
		{
			$sql = "SELECT COUNT(*) total FROM sia_modulos WHERE idModulo=:modulo";

			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':modulo'=>$modulo ));

			$result = $dbQuery->fetch(PDO::FETCH_ASSOC);

			if(!$result){
				$app->halt(404, "NO SE ENCONTRARON DATOS ");
			}else{
				echo json_encode($result);
			}
		 }catch (Exception $e) {
		  	print "ERROR: " . $e->getMessage() . "<br><br> QUERY: " . $dbQuery ;
	  		die();
		}
	});

	$app->post('/guardar/modulo_HVS', function()  use($app, $db) {
		
		$request=$app->request;

		$usrActual = $_SESSION ["idUsuario"];
		
		
		//echo("txtNombre: " . strtoupper($request->post('txtNombre')) );
		
		try {

			$oper = $request->post('txtOperacion');
			$id = strtoupper($request->post('txtModulo'));
			$nombre = $request->post('txtNombre');
			$liga = $request->post('txtLiga');
			$icono = $request->post('txtIcono');
			$orden = $request->post('txtOrden');
			$tipo = $request->post('txtTipo');
			$panel = $request->post('txtPanel');
			$estatus = $request->post('txtEstatus');

    		$datos = 'oper: ' . $oper . '<br>id: ' . $id .'<br>nombre: ' . $nombre . '<br>liga: '. $liga . '<br>icono: '. $icono . "<br>orden: " . $orden . "<br>tipo: " . $tipo ."<br>panel: " . $panel . '<br>estatus: ' . $estatus ;

			//echo("Datos: " . $datos);

			if ($oper=='INS'){
				$sql = "INSERT INTO sia_modulos (idModulo, nombre, panel, liga, icono, orden, tipo, usrAlta, fAlta, estatus) VALUES(:idModulo, :nombre, :panel, :liga, :icono, :orden, :tipo, :usrActual, getdate(), :estatus); ";		
				$dbQuery = $db->prepare($sql);	
				
				$dbQuery->execute(array(':idModulo'=> $id, ':nombre'=> $nombre, ':panel'=> $panel, ':liga'=> $liga, ':icono'=> $icono, ':orden'=> $orden, ':tipo'=> $tipo, ':usrActual'=> $usrActual, ':estatus'=> $estatus));		
				 
			}else{
				$sql = "UPDATE sia_modulos SET nombre=:nombre, panel=:panel, liga=:liga, icono=:icono, orden=:orden, tipo=:tipo, usrModificacion=:usrModificacion, fModificacion=getdate(), estatus=:estatus WHERE idModulo=:idModulo";		
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array( ':nombre'=> $nombre, ':panel'=> $panel, ':liga'=> $liga, ':icono'=> $icono, ':orden'=> $orden, ':tipo'=> $tipo, ':usrModificacion'=> $usrActual,':estatus' => $estatus, ':idModulo' => $id));
			}
			
		}catch (Exception $e) {
			print "¡Error!: " . $e->getMessage() . "<br/>";
			die();
		}

		$app->redirect($app->urlFor('catModulos'));
	});	


	// LAS SIGUIENTES FUNCIONES SON PARA LA ACTUALIZACION DEL MÓDULO DE DOCUMENTOS 

	//Lista de documentos

	$app->get('/doctos',  $autenticacionrole, function() use($app, $db){
		$cuenta = $_SESSION["idCuentaActual"];
		$area = $_SESSION["idArea"];
		
		$usrActual = $_SESSION["idUsuario"];
		$global = $_SESSION["usrGlobal"];
		
		$sql= "SELECT DISTINCT d.idDocto, isnull(d.numeroDocto,'') numeroDocto
			    , isnull(d.idTipoDocto,'') idTipoDocto, isnull(td.nombre,'') tipo
				, isnull(d.flujoDocto,'') flujodocto
				, isnull(d.fDocto,'') fDocto, isnull(fRecepcion,'') fRecepcion, isnull(fTermino,'') fTermino
				, isnull(d.idRemitente,'') idRemitente, isnull(u.nombre,'') remitente
				, isnull(d.idDestinatario,'') idDestinatario, isnull(a.nombre,'') destinatario
				, isnull(d.idPrioridad,'') idPrioridad 
				, isnull(d.idImpacto,'') idImpacto
				, isnull(d.asunto,'') asunto
				, isnull(d.idRecibio,'') idrecibio
				, isnull(d.estatus,'') estatus
				, isnull(d.archivoOriginal,'') archivoOriginal
				, isnull(d.archivoFinal,'') archivoFinal
					FROM sia_documentos d
					INNER JOIN sia_tiposdocumentos td ON d.idTipoDocto = td.idTipoDocto 
					INNER JOIN sia_unidades u ON d.idRemitente = u.idSector + u.idSubsector + u.idUnidad 
					INNER JOIN sia_areas a ON d.idDestinatario = a.idArea ";
		$dbQuery = $db->prepare($sql);
		//$dbQuery->execute(array(':cuenta' => $cuenta, ':area' => $area));
		$dbQuery->execute();
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		$app->render('doctos.php', $result);

	})->name('doctos');


	$app->get('/lstTiposDoctos', function() use($app, $db){
		$cuenta = $_SESSION["idCuentaActual"];
		$area = $_SESSION["idArea"];
		
		$usrActual = $_SESSION["idUsuario"];
		$global = $_SESSION["usrGlobal"];
		
		$sql= "SELECT idTipoDocto id, isnull(nombre,'') texto FROM sia_tiposdocumentos WHERE estatus = 'ACTIVO' ORDER BY nombre";
		$dbQuery = $db->prepare($sql);

		$dbQuery->execute();
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "RECURSO NO ENCONTRADO.");			
		}else{		
			echo json_encode($result);
		}		
	});


	$app->get('/lstUnidades_HVS', function() use($app, $db){
		$cuenta = $_SESSION["idCuentaActual"];
		$area = $_SESSION["idArea"];
		
		$usrActual = $_SESSION["idUsuario"];
		$global = $_SESSION["usrGlobal"];
		
		$sql= "SELECT idSector+idSubsector+idunidad id, isnull(nombre,'') texto FROM sia_unidades WHERE idCuenta=:cuenta ORDER BY nombre";
		$dbQuery = $db->prepare($sql);

		$dbQuery->execute(array(':cuenta' => $cuenta));
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "RECURSO NO ENCONTRADO.");			
		}else{		
			echo json_encode($result);
		}		
	});

	$app->get('/lstAreas_HVS', function() use($app, $db){
		$cuenta = $_SESSION["idCuentaActual"];
		$area = $_SESSION["idArea"];
		
		$usrActual = $_SESSION["idUsuario"];
		$global = $_SESSION["usrGlobal"];
		
		$sql= "SELECT idArea id, isnull(nombre,'') texto FROM sia_areas ORDER BY nombre";
		$dbQuery = $db->prepare($sql);

		$dbQuery->execute();
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "RECURSO NO ENCONTRADO.");			
		}else{		
			echo json_encode($result);
		}		
	});

	$app->get('/lstAreas_HVS2(/:idArea)', function($idArea=NULL) use($app, $db){

		$aDatos = array();

		$sql= "SELECT idArea id, idArea + ' - ' + nombre texto FROM sia_areas WHERE idArea <> '' ";

		if ($idArea != NULL ){
			$sql .= " AND idArea = :idArea "; 
			$aDatos['idArea'] = $idArea;
		}
		$sql .= " order by nombre";

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute($aDatos);
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});

	$app->get('/lstAreas_HVS3', function()    use($app, $db) {

		$sql= "SELECT idArea id, idArea + ' - ' + nombre texto FROM sia_areas WHERE idArea <> '' AND idAreaSuperior in('UTSFFA','UTSFEAJ') order by nombre";

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute();
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});


	$app->get('/lstUsuariosRecibio', function() use($app, $db){
		$cuenta = $_SESSION["idCuentaActual"];
		$area = $_SESSION["idArea"];
		
		$usrActual = $_SESSION["idUsuario"];
		$global = $_SESSION["usrGlobal"];
		
		$sql= "SELECT idUsuario id, isnull(saludo + ' ' + nombre + ' ' + paterno + ' ' + materno, '') texto FROM sia_usuarios WHERE idArea = :area ORDER BY nombre + ' ' + paterno + ' ' + materno";
		$dbQuery = $db->prepare($sql);

		$dbQuery->execute(array(':area' => $area));
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "RECURSO NO ENCONTRADO.");			
		}else{		
			echo json_encode($result);
		}		
	});

	$app->get('/DocumentoByidDocto/:id', function($id) use($app, $db){
		$cuenta = $_SESSION["idCuentaActual"];
		$area = $_SESSION["idArea"];
		
		$usrActual = $_SESSION["idUsuario"];
		$global = $_SESSION["usrGlobal"];
		
		$sql= "SELECT DISTINCT d.idDocto, isnull(d.numeroDocto,'') numeroDocto
			    , isnull(d.idTipoDocto,'') idTipoDocto, isnull(td.nombre,'') tipo
				, isnull(d.flujoDocto,'') flujodocto
				, isnull(d.fDocto,'') fDocto, isnull(fRecepcion,'') fRecepcion, isnull(fTermino,'') fTermino
				, isnull(d.idRemitente,'') idRemitente, isnull(u.nombre,'') remitente
				, isnull(d.idDestinatario,'') idDestinatario, isnull(a.nombre,'') destinatario
				, isnull(d.idPrioridad,'') idPrioridad 
				, isnull(d.idImpacto,'') idImpacto
				, isnull(d.asunto,'') asunto
				, isnull(d.idRecibio,'') idrecibio
				, isnull(d.estatus,'') estatus
				, isnull(d.archivoOriginal,'') archivoOriginal
				, isnull(d.archivoFinal,'') archivoFinal
					FROM sia_documentos d
					INNER JOIN sia_tiposdocumentos td ON d.idTipoDocto = td.idTipoDocto 
					INNER JOIN sia_unidades u ON d.idRemitente = u.idSector + u.idSubsector + u.idUnidad 
					INNER JOIN sia_areas a ON d.idDestinatario = a.idArea 
				WHERE d.idDocto = :idDocto ";
		$dbQuery = $db->prepare($sql);
		//$dbQuery->execute(array(':cuenta' => $cuenta, ':area' => $area));
		$dbQuery->execute(array(':idDocto' =>$id));
		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "RECURSO NO ENCONTRADO.");			
		}else{		
			echo json_encode($result);
		}		
	});


	$app->get('/validaExisteNumeroDocto/:numero', function($numero)  use($app, $db) {
		try
		{
			$sql = "SELECT COUNT(*) total FROM sia_documentos WHERE numeroDocto=:numero";

			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':numero'=>$numero ));

			$result = $dbQuery->fetch(PDO::FETCH_ASSOC);

			if(!$result){
				$app->halt(404, "NO SE ENCONTRARON DATOS ");
			}else{
				echo json_encode($result);
			}
		 }catch (Exception $e) {
		  	print "ERROR: " . $e->getMessage() . "<br><br> QUERY: " . $dbQuery ;
	  		die();
		}
	});

	$app->post('/guardar/documento', function()  use($app, $db) {
		
		$programa = $_SESSION ["idProgramaActual"];
		$cuenta = $_SESSION["idCuentaActual"];
		$usrActual = $_SESSION ["idUsuario"];

		$request=$app->request;

		//echo("txtNombre: " . strtoupper($request->post('txtNombre')) );
		
		try {

			$oper = $request->post('txtOperacion');
			$docto = $request->post('txtDocto');
			$numero = strtoupper($request->post('txtNumero'));
			$tipo = $request->post('txtTipo');
			$flujo = $request->post('txtFlujo');
			$fDocto = $request->post('txtFecDocto');
			$fRecepcion = $request->post('txtFecRecepcion');
			$fTermino = $request->post('txtFecTermino');
			$remitente = $request->post('txtRemitente');
			$destinatario = $request->post('txtDestinatario');
			$prioridad = $request->post('txtPrioridad');
			$impacto = $request->post('txtImpacto');
			$asunto = strtoupper($request->post('txtAsunto'));
			$recibio = $request->post('txtRecibio');
			$estatus = $request->post('txtEstatus');
			$asignar = $request->post('chkAsignar');

			$archivoOriginal = $request->post('txtArchivoOriginal');
			$archivoFinal = $request->post('txtArchivoFinal');

			$lstAuditorias = $request->post('txtLstAuditorias');


    		//$datos = 'oper: ' . $oper . '<br>numero' . $numero . '<br>tipo' . $tipo . '<br>flujo' .  $flujo . '<br>fDocto' . $fDocto . '<br>fRecepcion' .  $fRecepcion .  '<br>fTermino' .  $fTermino .  '<br>remitente' . $remitente .  '<br>destinatario' .  $destinatario .  '<br>prioridad' .  $prioridad . '<br>impacto' .  $impacto .  '<br>asunto' .  $asunto .  '<br>recibio' .  $recibio .  '<br>usrActual' .  $usrActual . '<br>estatus' .  $estatus;

			//echo("Datos: " . $datos);

			if ($oper=='INS'){
				
	 			$sql = "INSERT INTO sia_documentos (numeroDocto, idTipoDocto, flujoDocto, fDocto, fRecepcion, fTermino, idRemitente, idDestinatario, idPrioridad, idImpacto, asunto, idRecibio, usrAlta, fAlta, estatus, archivoOriginal, archivoFinal )
  				VALUES (:numero, :tipo, :flujo, :fDocto, :fRecepcion, :fTermino, :remitente, :destinatario, :prioridad, :impacto, :asunto,
  				:recibio, :usrActual, getdate(), :estatus, :archivoOriginal, :archivoFinal); ";

				$dbQuery = $db->prepare($sql);	

				$dbQuery->execute(array(':numero'=> $numero, ':tipo'=> $tipo, ':flujo'=> $flujo, ':fDocto'=> $fDocto, ':fRecepcion'=> $fRecepcion, ':fTermino'=> $fTermino, ':remitente'=> $remitente, ':destinatario'=> $destinatario, ':prioridad'=> $prioridad, ':impacto'=> $impacto, ':asunto'=> $asunto, ':recibio'=> $recibio, ':usrActual'=> $usrActual, ':estatus'=> $estatus, ':archivoOriginal'=>$archivoOriginal, ':archivoFinal'=>$archivoFinal));	
				 
			}else{
				$sql = "UPDATE sia_documentos SET numeroDocto=:numero, idTipoDocto=:tipo, flujoDocto=:flujo, fDocto=:fDocto, fRecepcion=:fRecepcion, fTermino=:fTermino, idRemitente=:remitente, idDestinatario=:destinatario, idPrioridad=:prioridad, idImpacto=:impacto, asunto=:asunto, idRecibio=:recibio, usrModificacion=:usrActual, fModificacion=getdate(), estatus=:estatus, archivoOriginal=:archivoOriginal, archivoFinal=:archivoFinal WHERE idDocto=:idDocto";		
				$dbQuery = $db->prepare($sql);

				$dbQuery->execute(array(':numero'=>$numero, ':tipo'=>$tipo, ':flujo'=>$flujo, ':fDocto'=>$fDocto, ':fRecepcion'=>$fRecepcion, ':fTermino'=>$fTermino, ':remitente'=>$remitente, ':destinatario'=>$destinatario, ':prioridad'=>$prioridad, ':impacto'=>$impacto, ':asunto'=>$asunto, ':recibio'=>$recibio, ':usrActual'=>$usrActual, ':estatus'=>$estatus, ':archivoOriginal'=>$archivoOriginal, ':archivoFinal'=>$archivoFinal, ':idDocto'=>$docto));
			}

			if($lstAuditorias != ""){
				// Se dene recuperar el idDocto si fue INS por que esté no existía y se requiere para agregar datos a la tabla
				// sia_documentoauditorias
				if ($oper=='INS'){
		 			$sql = "SELECT idDocto FROM sia_documentos WHERE numeroDocto=:numero AND idTipoDocto=:tipo AND fDocto=:fDocto ";

					$dbQuery = $db->prepare($sql);
					$dbQuery->execute(array(':numero'=> $numero, ':tipo'=> $tipo, ':fDocto'=> $fDocto));				

					$result = $dbQuery->fetch(PDO::FETCH_ASSOC); 
					$docto = $result ['idDocto']; 
				}

				if ($docto > 0){
					$aAuditorias = explode("|", $lstAuditorias);

					if( count($aAuditorias) > 0 ){
						// Borrará todos los registros de la tabla sia_documentosauditorias que contengan el número de documento. 
						$sql = "DELETE FROM sia_documentosauditorias WHERE idDocto=:docto AND idCuenta=:cuenta AND idPrograma=:programa";	
						$dbQuery = $db->prepare($sql);
						$dbQuery->execute(array(':docto'=>$docto, ':cuenta'=>$cuenta, ':programa'=>$programa ) );
					}

					foreach ($aAuditorias as $aAuditoria=>$auditoria) {
						//echo("el valor es $auditoria" );
						// Insertará uno a uno los regstros de relacion entre el documento y las auditorías seleccionadas
						$sql="INSERT INTO sia_documentosauditorias (idDocto, idCuenta, idPrograma, idAuditoria, usrAlta, fAlta, estatus)
						VALUES (:idDocto, :idCuenta, :idPrograma, :idAuditoria, :usrAlta, GETDATE(), 'ACTIVO' )";
						$dbQuery = $db->prepare($sql);	

						$dbQuery->execute(array(':idDocto'=> $docto, ':idCuenta'=> $cuenta, ':idPrograma'=> $programa, ':idAuditoria'=> $auditoria, ':usrAlta'=> $usrActual));	
					}
				}
			}
			
		}catch (Exception $e) {
			print "¡Error!: " . $e->getMessage() . "<br/>";
			die();
		}

		$app->redirect($app->urlFor('doctos'));
	});	


	$app->get('/tblAuditoriasLeftDocto/:idDocto', function($idDocto) use($app, $db){

		$cuenta = $_SESSION["idCuentaActual"];
		$programa = $_SESSION ["idProgramaActual"];

		$area = $_SESSION["idArea"];
		$rpe = $_SESSION["idEmpleado"];
		$global = $_SESSION["usrGlobal"];
		$globalArea = $_SESSION["usrGlobalArea"];		

		$idDocto1 = $idDocto;

		if ($global=="SI"){
			$sql = "" .
			"SELECT DISTINCT CASE WHEN (a.idAuditoria = da.idAuditoria and da.idDocto = :idDocto) THEN 'SI' ELSE 'NO' END asignado
    			, a.idAuditoria idAuditoria
			    , COALESCE(convert(varchar(20),a.clave), convert(varchar(20),a.idAuditoria)) auditoria
			    , isnull(u.nombre,'') sujeto
    			, isnull(ta.nombre,'') tipo
    			, a.idProceso proceso
    			, a.idEtapa etapa 
			FROM sia_auditorias a
				INNER JOIN sia_unidades u on a.idCuenta = u.idCuenta and a.idSector=u.idSector and a.idSubsector = u.idSubsector and a.idUnidad=u.idUnidad
				LEFT JOIN sia_tiposauditoria ta on a.tipoAuditoria= ta.idTipoAuditoria 
  				LEFT JOIN sia_documentosauditorias da ON a.idCuenta = da.idCuenta AND a.idPrograma = da.idPrograma AND a.idAuditoria = da.idAuditoria AND da.idDocto = :idDocto1
				WHERE a.idCuenta=:cuenta AND a.idPrograma=:programa
			ORDER BY a.idAuditoria DESC" ;

			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':idDocto' => $idDocto, ':idDocto1' => $idDocto1, ':cuenta' => $cuenta, ':programa' => $programa));

		}else{
			if ($globalArea=="SI"){
				$sql = "" .
				"SELECT DISTINCT CASE WHEN (a.idAuditoria = da.idAuditoria and da.idDocto = :idDocto) THEN 'SI' ELSE 'NO' END asignado
	    			, a.idAuditoria idAuditoria
	    			, COALESCE(convert(varchar(20),a.clave), convert(varchar(20),a.idAuditoria)) auditoria
				    , isnull(u.nombre,'') sujeto
	    			, isnull(ta.nombre,'') tipo
	    			, a.idProceso proceso
		    		, a.idEtapa etapa 
				FROM sia_auditorias a
	  				LEFT JOIN sia_documentosauditorias da ON a.idCuenta = da.idCuenta AND a.idPrograma = da.idPrograma AND a.idAuditoria = da.idAuditoria AND da.idDocto = :idDocto1
					INNER JOIN sia_unidades u on a.idCuenta = u.idCuenta and a.idSector=u.idSector and a.idSubsector = u.idSubsector and a.idUnidad=u.idUnidad
					LEFT JOIN sia_tiposauditoria ta on a.tipoAuditoria= ta.idTipoAuditoria
				WHERE a.idCuenta=:cuenta AND a.idArea =:area AND a.idPrograma=:programa
				ORDER BY a.idAuditoria DESC " ;

				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':idDocto' => $idDocto, ':idDocto1' => $idDocto1, ':cuenta' => $cuenta, ':area' => $area, ':programa' => $programa));		
			}else{
				$sql = "" .
				"SELECT DISTINCT CASE WHEN (a.idAuditoria = da.idAuditoria and da.idDocto = :idDocto) THEN 'SI' ELSE 'NO' END asignado
	    			, a.idAuditoria idAuditoria
	    			, COALESCE(convert(varchar(20),a.clave), convert(varchar(20),a.idAuditoria)) auditoria
				    , isnull(u.nombre,'') sujeto
	    			, isnull(ta.nombre,'') tipo
	    			, a.idProceso proceso
		    		, a.idEtapa etapa 
				FROM sia_auditorias a
	  				LEFT JOIN sia_documentosauditorias da ON a.idCuenta = da.idCuenta AND a.idPrograma = da.idPrograma AND a.idAuditoria = da.idAuditoria AND da.idDocto = :idDocto1
	  				INNER JOIN  sia_auditoriasauditores aa ON a.idCuenta=aa.idCuenta and a.idAuditoria=aa.idAuditoria
					INNER JOIN sia_unidades u on a.idCuenta = u.idCuenta and a.idSector=u.idSector and a.idSubsector = u.idSubsector and a.idUnidad=u.idUnidad
					LEFT JOIN sia_tiposauditoria ta on a.tipoAuditoria= ta.idTipoAuditoria
				WHERE a.idCuenta=:cuenta AND aa.idAuditor=:auditor AND a.idArea =:area AND a.idPrograma=:programa
				ORDER BY a.idAuditoria DESC " ;

				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':idDocto' => $idDocto, ':idDocto1' => $idDocto1, ':cuenta' => $cuenta, ':auditor' => $rpe, ':area' => $area, ':programa' => $programa));		
			}	
		}

		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "RECURSO NO ENCONTRADO.");			
		}else{		
			echo json_encode($result);
		}		
	});

	$app->get('/lstAuditoriaByID_HVS/:id', function($id)    use($app, $db) {
		$cuenta = $_SESSION["idCuentaActual"];
		$area = $_SESSION["idArea"];
		
		$usrActual = $_SESSION["idUsuario"];
		$global = $_SESSION["usrGlobal"];
		$globalArea = $_SESSION["usrGlobalArea"];

		if ($global=="SI"){

			$sql="SELECT a.idCuenta cuenta, a.idPrograma programa, a.idAuditoria auditoria,  COALESCE(convert(varchar(20),a.clave),convert(varchar(20),a.idAuditoria)) claveAuditoria, isnull(a.clave,'') clave, a.tipoAuditoria tipo, a.idArea area, a.idSector sector, a.idSubsector subSector, a.idUnidad unidad, a.idObjeto objeto, a.objetivo, a.alcance, a.justificacion, a.tipoPresupuesto, a.acompanamiento, a.idProceso proceso, a.idEtapa etapa, REPLACE(ISNULL(CONVERT(VARCHAR(10),a.fInicio,105), ''), '1900-01-01', '') feInicio, REPLACE(ISNULL(CONVERT(VARCHAR(10),a.fFin,105), ''), '1900-01-01', '') feFin, a.latitud, a.longitud, a.tipoObservacion tipoObse, a.observacion,a.idResponsable responsable, a.idSubresponsable subresponsable, REPLACE(ISNULL(CONVERT(VARCHAR(10),a.fConfronta,105), ''), '1900-01-01', '') fcon, isnull(a.resumenConfronta,'') rconfron, REPLACE(ISNULL(CONVERT(VARCHAR(10),a.fIRA,105), ''), '1900-01-01', '') ira, REPLACE(ISNULL(CONVERT(VARCHAR(10),a.fIFA,105), ''), '1900-01-01', '') ifa
				FROM sia_auditorias a LEFT JOIN  sia_objetos o ON a.idObjeto = o.idObjeto WHERE  a.idCuenta=:cuenta AND a.idAuditoria =:id AND a.idEtapa in (SELECT idEtapa FROM sia_rolesetapas re INNER JOIN sia_usuariosroles ur ON ur.idRol = re.idRol  WHERE ur.idUsuario=:usrActual) ORDER BY a.idAuditoria ";
				
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':cuenta' => $cuenta, ':id' => $id, ':usrActual' => $usrActual));
		}else{

			if($globalArea=="SI"){

				$sql="SELECT a.idCuenta cuenta, a.idPrograma programa, a.idAuditoria auditoria,  COALESCE(convert(varchar(20),a.clave),convert(varchar(20),a.idAuditoria)) claveAuditoria, isnull(a.clave,'') clave, a.tipoAuditoria tipo, a.idArea area, a.idSector sector, a.idSubsector subSector, a.idUnidad unidad, a.idObjeto objeto, a.objetivo, a.alcance, a.justificacion, a.tipoPresupuesto, a.acompanamiento, a.idProceso proceso, a.idEtapa etapa, REPLACE(ISNULL(CONVERT(VARCHAR(10),a.fInicio,105), ''), '1900-01-01', '') feInicio, REPLACE(ISNULL(CONVERT(VARCHAR(10),a.fFin,105), ''), '1900-01-01', '') feFin, a.latitud, a.longitud, a.tipoObservacion tipoObse, a.observacion,a.idResponsable responsable, a.idSubresponsable subresponsable,  REPLACE(ISNULL(CONVERT(VARCHAR(10),a.fConfronta,105), ''), '1900-01-01', '') fcon, isnull(a.resumenConfronta,'') rconfron, REPLACE(ISNULL(CONVERT(VARCHAR(10),a.fIRA,105), ''), '1900-01-01', '') ira, REPLACE(ISNULL(CONVERT(VARCHAR(10),a.fIFA,105), ''), '1900-01-01', '') ifa
					FROM sia_auditorias a LEFT JOIN  sia_objetos o ON a.idObjeto = o.idObjeto WHERE a.idCuenta=:cuenta AND a.idAuditoria =:id AND a.idArea=:area ORDER BY a.idAuditoria desc; ";

				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':cuenta' => $cuenta, ':id' => $id, ':area' => $area));		

			}else{

				$sql="SELECT a.idCuenta cuenta, a.idPrograma programa, a.idAuditoria auditoria,  COALESCE(convert(varchar(20),a.clave),convert(varchar(20),a.idAuditoria)) claveAuditoria, isnull(a.clave,'') clave, a.tipoAuditoria tipo, a.idArea area, a.idSector sector, a.idSubsector subSector, a.idUnidad unidad, a.idObjeto objeto, a.objetivo, a.alcance, a.justificacion, a.tipoPresupuesto, a.acompanamiento, a.idProceso proceso, a.idEtapa etapa, REPLACE(ISNULL(CONVERT(VARCHAR(10),a.fInicio,105), ''), '1900-01-01', '') feInicio, REPLACE(ISNULL(CONVERT(VARCHAR(10),a.fFin,105), ''), '1900-01-01', '') feFin, a.latitud, a.longitud, a.tipoObservacion tipoObse, a.observacion,a.idResponsable responsable, a.idSubresponsable subresponsable,  REPLACE(ISNULL(CONVERT(VARCHAR(10),a.fConfronta,105), ''), '1900-01-01', '') fcon, isnull(a.resumenConfronta,'') rconfron, REPLACE(ISNULL(CONVERT(VARCHAR(10),a.fIRA,105), ''), '1900-01-01', '') ira, REPLACE(ISNULL(CONVERT(VARCHAR(10),a.fIFA,105), ''), '1900-01-01', '') ifa
					FROM sia_auditorias a LEFT JOIN  sia_objetos o ON a.idObjeto = o.idObjeto WHERE  a.idCuenta=:cuenta AND a.idArea =:area AND a.idAuditoria =:id AND a.idEtapa in (SELECT idEtapa FROM sia_rolesetapas re INNER JOIN sia_usuariosroles ur ON ur.idRol = re.idRol  WHERE ur.idUsuario=:usrActual) ORDER BY a.idAuditoria";
				
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':cuenta' => $cuenta, ':id' => $id, ':area' => $area, ':usrActual' => $usrActual));		
			}
		}

		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});

// ****  CATÁLOGO DE DÍAS INHABILES UNIDADES o ENTES *****


	$app->get('/catInhabilesUnidad',  $autenticacionrole, function()  use ($app, $db) {
		$cuenta = $_SESSION["idCuentaActual"];
		$area = $_SESSION["idArea"];
		
		$usrActual = $_SESSION["idUsuario"];
		$global = $_SESSION["usrGlobal"];

		$sql="SELECT A.* FROM
				(SELECT distinct udh.idUnidadDiaInhabil idDia, udh.idCuenta, u.nombre unidad
      				, concat(udh.idSector, '-', udh.idSubsector, '-', udh.idUnidad) id
					, udh.tipo tipo, udh.nombre 
					, CONVERT(VARCHAR(12),udh.fInicio,101) fInicio
					, CONVERT(VARCHAR(12),udh.fFin,101) fFin
      				, udh.estatus estatus 
				FROM dbo.sia_unidadesDiasInhabiles udh 
      			INNER JOIN sia_areasunidades au on udh.idCuenta = au.idCuenta and udh.idSector = au.idSector and udh.idSubsector = au.idSubsector 		and udh.idUnidad = au.idUnidad 
      			INNER JOIN sia_unidades u ON (udh.idSector = u.idSector and udh.idSubsector = u.idSubsector and udh.idUnidad = u.idUnidad) 
				WHERE udh.idCuenta = :cuenta AND au.idArea = :area ) A
				ORDER BY id, finicio DESC;";
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':cuenta' => $cuenta, ':area' => $area));
		$result ['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS.");
		}else{
			$app->render('catInhabilesUnidad.php', $result);
		}
	})->name('catInhabilesUnidad');

		// Obten los registro que cumplan con el día inhábil de Unidad indicado
 
	$app->get('/lstInhabilUnidadByID/:id', function($id)    use($app, $db) {
		
		$sql="SELECT idCuenta, concat(idSector, '|', idSubsector, '|', idUnidad) centroGestor
		, tipo, nombre, fInicio, fFin, usrAlta, fAlta, estatus FROM sia_unidadesDiasInhabiles WHERE idUnidadDiaInhabil=:id ";
		
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':id' => $id));
		
		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});

//Guarda un día inhábil

	$app->post('/guardar/inhabilesUnidad', function()  use($app, $db) {
		$usrActual = $_SESSION ["idUsuario"];
		$cuenta = $_SESSION ["idCuentaActual"];

		$request=$app->request;

		$dia = $request->post('txtDia');
		$centroGestor = explode('|', $request->post('txtUnidad') );
		$sector = $centroGestor[0];
		$subSector = $centroGestor[1];
		$unidad = $centroGestor[2];

 		$tipo = $request->post('txtTipo');
		$nombre = strtoupper($request->post('txtNombre'));

		//$fInicio = $request->post('txtFechaInicial');
		$fInicio = date_create(($request->post('txtFechaInicial')));
		$fInicio = $fInicio->format('Y-m-d');

		//$fFin = $request->post('txtFechaFinal');
		$fFin = date_create(($request->post('txtFechaFinal')));
		$fFin = $fFin->format('Y-m-d');

		$estatus = $request->post('txtEstatus');

		$oper = $request->post('txtOperacion');

		$valores = 'cuenta=> ' . $cuenta . ' dia=> ' . $dia . ' sector=> ' . $sector . ' subSector=> ' . $subSector .
		' unidad=> ' . $unidad . ' tipo=> ' . $tipo . ' nombre=> ' . $nombre . ' fInicio=> ' . $fInicio . ' fFin=> ' . $fFin . ' estatus=> ' . $estatus . ' oper=> ' .$oper;
		//echo( " Valor de valores: " . $valores );

		try
		{
			if($oper=='INS')
			{
				$sql="INSERT INTO sia_unidadesDiasInhabiles (idCuenta,idSector,idSubsector,idUnidad,tipo,nombre,fInicio,fFin,usrAlta,fAlta,estatus) 
				VALUES (:cuenta, :sector, :subSector, :unidad, :tipo, :nombre, :fInicio, :fFin, :usrActual, getdate(), 'ACTIVO');";
				$dbQuery = $db->prepare($sql);

				$dbQuery->execute(array(':cuenta' => $cuenta, ':sector' => $sector, ':subSector' => $subSector, ':unidad' => $unidad, ':tipo' => $tipo, ':nombre' => $nombre, ':fInicio' => $fInicio, ':fFin' => $fFin, ':usrActual' => $usrActual ));
				//echo "<br>INS OK<hr>";
			}else{

				$sql="UPDATE sia_unidadesDiasInhabiles SET " .
				"idCuenta=:cuenta, idSector=:sector, idSubsector=:subSector, idUnidad=:unidad, tipo=:tipo, nombre=:nombre, fInicio=:fInicio, fFin=:fFin, usrModificacion=:usrActual, " .
				" fModificacion=getdate(), estatus=:estatus WHERE idDia =:dia";
				$dbQuery = $db->prepare($sql);

				$dbQuery->execute(array(':cuenta'=>$cuenta, ':sector'=>$sector, ':subSector'=>$subSector, ':unidad'=>$unidad, ':tipo'=>$tipo, ':nombre'=>$nombre, ':fInicio'=>$fInicio, ':fFin'=>$fFin, ':usrActual'=>$usrActual, ':estatus'=>$estatus, ':dia'=>$dia));
				//echo "<br>UPD OK";
			}

			//echo nl2br("\nQuery Ejecutado : ".$sql);

		}catch (Exception $e) {
			print "¡Error!: " . $e->getMessage() . "<br/>";
			die();
		}
		$app->redirect($app->urlFor('catInhabilesUnidad'));
	});

	$app->get('/existeDiaInhabilesUnidad/:valores', function($valores)  use ($app, $db) {
		
		$cuenta = $_SESSION["idCuentaActual"];

		//echo( " Valor de valores: *" . $valores . "*" );

		try {

			$parametros = explode("|", $valores);
			
			$fecInicio = $parametros[0];
			$fecFin = $parametros[1];
			$sector = $parametros[2];
			$subSector = $parametros[3];
			$unidad = $parametros[4];
			/*
			$cuenta = 'CTA-2015';
			$fecInicio = '2016-10-14';
			$fecFin = '2016-10-14';
			$sector = '17' ;
			$subSector = 'L0';
			$unidad = '00';
			*/
			//$contenidos = 'cuenta=> ' . $cuenta . ' fecInicio=> ' . $fecInicio . ' fecFin=> ' . $fecFin . ' sector=> ' . $sector . ' subSector=> ' . $subSector . ' unidad=> ' . $unidad;
			
			//echo( " Valor de contenidos: " . $contenidos );

			$sql="SELECT COUNT(*) total FROM sia_unidadesDiasInhabiles udi WHERE (:fecInicio BETWEEN udi.fInicio AND udi.fFin) or (:fecFin BETWEEN udi.fInicio AND udi.fFin) AND udi.idCuenta=:cuenta AND udi.idSector=:sector AND udi.idSubsector=:subSector AND udi.idUnidad=:unidad AND udi.estatus = 'ACTIVO' ";


			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':fecInicio'=>$fecInicio,':fecFin'=>$fecFin, ':cuenta'=>$cuenta, ':sector'=>$sector, ':subSector'=>$subSector, ':unidad'=>$unidad ));

			$result = $dbQuery->fetch(PDO::FETCH_ASSOC);

			$total = $result ['total']; 
			if($total > 0){
				echo("SI");
			}else{
				echo("NO");
			}
			/*
			if(!$result){
				$app->halt(404, "RECURSO NO ENCONTRADO.");			
			}else{		
				echo json_encode($result);
			}		
			*/
		}catch (Exception $e) {
		  	echo("ERROR: " . $e->getMessage() . "<br><br> QUERY: " . $dbQuery) ;
	  		die();
		 }

	});

	// ------------------------------------------------------
	//      INICIA SENTENCIAS DEL PROCESO DE GESTION
	// ------------------------------------------------------

	$app->get('/lstAsuntos',  $autenticacionrole, function()  use ($app, $db) {
		$cuenta = $_SESSION["idCuentaActual"];
		$area = $_SESSION["idArea"];
		$usrActual = $_SESSION["idUsuario"];

		$sql="SELECT a.idAsunto asunto, a.folioAsunto folio, a.NumDocto numero, td.nombre tipoDocumento, SUBSTRING(a.descripcion,1,25) descripcion
  				, a.TipoOrigenDocto tipoOrigenDocto, SUBSTRING(UPPER(cd.nombre),1,25) clasificacion, a.idTipoAtencion tipoAtencion, tp.nombre prioridad
  				, a.idSituacion situacion, CONVERT(VARCHAR(12), a.fecDocto, 101) fechaDocto, CONVERT(VARCHAR(12), a.fecCompromiso , 101) fechaCompro, case when ( DATEDIFF(day, GetDate(), a.fecCompromiso) > 7) then 'VERDE' 
    				when ( DATEDIFF(day, GetDate(), a.fecCompromiso) > 1 and DATEDIFF(day, GetDate(), a.fecCompromiso) < 7) then 'AMARILLO' 
    				else 'ROJO' end estatus
			FROM sia_asuntos a inner join sia_tiposdocumentos td on a.idTipoDocto = td.idTipoDocto
  				left join sia_ClasificacionesDoctos cd ON a.idClasificacionDocto = cd.idClasificacionDocto
  				inner join sia_TiposPrioridades tp ON a.idTipoPrioridad = tp.idTipoPrioridad
			WHERE a.estatus = 'ACTIVO' and td.tipo = 'INTERNO' and a.idCuenta = :cuenta and a.idArea = :area and a.usrAlta = :usuario
			and a.idSituacion = 'EN REGISTRO' ORDER BY a.fecDocto DESC;";		

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':cuenta' => $cuenta, ':area' => $area, ':usuario' => $usrActual ));
		$result ['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS.");
		}else{
			$app->render('asuntos.php', $result);
		}
	})->name('lstAsuntos');


$app->get('/asuntosBySituacion/:situacion', function($situacion)  use ($app, $db) {
		$cuenta = $_SESSION["idCuentaActual"];
		$area = $_SESSION["idArea"];
		
		$usrActual = $_SESSION["idUsuario"];

		$sql="SELECT a.idAsunto asunto, a.folioAsunto folio, a.NumDocto numero, td.nombre tipoDocumento, SUBSTRING(a.descripcion,1,25) descripcion
  				, a.TipoOrigenDocto tipoOrigenDocto, SUBSTRING(UPPER(cd.nombre),1,25) clasificacion, a.idTipoAtencion tipoAtencion, tp.nombre prioridad
  				, a.idSituacion situacion, CONVERT(VARCHAR(12), a.fecDocto, 101) fechaDocto, CONVERT(VARCHAR(12), a.fecCompromiso , 101) fechaCompro, case when ( DATEDIFF(day, GetDate(), a.fecCompromiso) > 7) then 'VERDE' 
    				when ( DATEDIFF(day, GetDate(), a.fecCompromiso) > 1 and DATEDIFF(day, GetDate(), a.fecCompromiso) < 7) then 'AMARILLO' 
    				else 'ROJO' end estatus
			FROM sia_asuntos a inner join sia_tiposdocumentos td on a.idTipoDocto = td.idTipoDocto
  				left join sia_ClasificacionesDoctos cd ON a.idClasificacionDocto = cd.idClasificacionDocto
  				inner join sia_TiposPrioridades tp ON a.idTipoPrioridad = tp.idTipoPrioridad
			WHERE a.estatus = 'ACTIVO' and td.tipo = 'INTERNO' and a.idCuenta = :cuenta and a.idArea = :area and a.usrAlta = :usuario
			 and a.idSituacion = :situacion ORDER BY a.fecDocto DESC;";
		
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':cuenta' => $cuenta, ':area' => $area, ':usuario' => $usrActual, ':situacion' => $situacion ));
		$result ['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS.");
		}else{
			echo json_encode($result);
		}
	});

	$app->get('/lstTiposDocumentosByTipo/:tipo', function($tipo) use($app, $db){

		$sql= "SELECT idTipoDocto id, nombre texto FROM sia_tiposdocumentos WHERE tipo = :tipo AND estatus = 'ACTIVO' ORDER BY idTipoDocto;";
		$dbQuery = $db->prepare($sql);

		$dbQuery->execute(array(':tipo' => $tipo));
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "RECURSO NO ENCONTRADO.");			
		}else{		
			echo json_encode($result);
		}		
	});

	$app->get('/lstTiposPrioridades', function() use($app, $db){

		
		$sql= "SELECT idTipoPrioridad id, nombre texto FROM sia_tiposPrioridades WHERE estatus = 'ACTIVO' ORDER BY idTipoPrioridad;";
		$dbQuery = $db->prepare($sql);

		$dbQuery->execute();
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "RECURSO NO ENCONTRADO.");			
		}else{		
			echo json_encode($result);
		}		
	});


	$app->get('/lstClasificacionesDocto', function() use($app, $db){

		$sql= "SELECT idClasificacionDocto id, UPPER(nombre) texto FROM sia_clasificacionesDoctos WHERE estatus = 'ACTIVO' ORDER BY idClasificacionDocto;";
		$dbQuery = $db->prepare($sql);

		$dbQuery->execute();
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "RECURSO NO ENCONTRADO.");			
		}else{		
			echo json_encode($result);
		}		
	});

	$app->get('/lstTiposIndices', function() use($app, $db){

		$sql= "SELECT idTipoIndice id, UPPER(SUBSTRING(LTRIM(nombre),1,110)) texto FROM sia_tiposIndices WHERE estatus = 'ACTIVO' ORDER BY idTipoIndice;";
		$dbQuery = $db->prepare($sql);

		$dbQuery->execute();
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "RECURSO NO ENCONTRADO.");			
		}else{		
			echo json_encode($result);
		}		
	});

	$app->get('/lstIndicesBytipo/:tipo', function($tipo) use($app, $db){

		$sql= "SELECT idIndice id, UPPER(LTRIM(cveIndice) + ' ' + SUBSTRING(LTRIM(nombre),1,110)) texto FROM sia_Indices WHERE idTipoIndice = :tipo AND estatus = 'ACTIVO' ORDER BY idIndice;";
		$dbQuery = $db->prepare($sql);

		$dbQuery->execute(array(':tipo' => $tipo));
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "RECURSO NO ENCONTRADO.");			
		}else{		
			echo json_encode($result);
		}		
	});

$app->get('/lstAuditoriasToAsuntos', function()  use ($app, $db) {
		$cuenta = $_SESSION["idCuentaActual"];
		$area = $_SESSION["idArea"];
	
		$sql="SELECT a.idAuditoria id, COALESCE(a.clave, convert(varchar, a.idAuditoria)) + ' - ' + ta.nombre 
				+ ' - ' + SUBSTRING(dbo.lstSujetosByAuditoria(a.idAuditoria),1,90) texto
			FROM sia_programas p  INNER JOIN sia_auditorias a on p.idCuenta=a.idCuenta and p.idPrograma=a.idPrograma 
  			LEFT JOIN sia_areas ar on a.idArea=ar.idArea INNER JOIN sia_tiposauditoria ta on a.tipoAuditoria=ta.idTipoAuditoria 
			WHERE a.idCuenta=:cuenta AND a.idArea=:area ORDER BY a.idAuditoria desc ;";
		$dbQuery = $db->prepare($sql);

		$dbQuery->execute(array(':cuenta' => $cuenta, ':area' => $area));	
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "RECURSO NO ENCONTRADO.");			
		}else{		
			echo json_encode($result);
		}		
	});

$app->get('/lstUsuariosByArea/:laArea', function($laArea)  use ($app, $db) {
		$cuenta = $_SESSION["idCuentaActual"];
		$area = $_SESSION["idArea"];
	
		if( $laArea == "TODOS"){
			$sql="SELECT distinct u.idEmpleado id, COALESCE(u.saludo,u.saludo,'') + ' ' + u.nombre + ' ' + u.paterno + ' ' + u.materno  nombreUsuario, COALESCE(p.nombre, p.nombre, '') plaza, a.idArea idArea, a.nombre area
				FROM sia_usuarios u INNER JOIN sia_empleados e on u.idEmpleado = e.idEmpleado
					LEFT JOIN sia_areas a ON e.idArea = a.idArea
					LEFT JOIN sia_plazas p ON e.idPlaza = p.idPlaza
				WHERE u.estatus = 'ACTIVO'
				ORDER BY area, nombreUsuario;";
			$dbQuery = $db->prepare($sql);
			$dbQuery->execute();	
		}else{
			/*
			$sql="SELECT distinct u.idEmpleado id, COALESCE(u.saludo,u.saludo,'') + ' ' + u.nombre + ' ' + u.paterno + ' ' + u.materno + '  -  ' +
				 COALESCE(p.nombre, p.nombre, '') texto
				FROM sia_usuarios u INNER JOIN sia_empleados e on u.idEmpleado = e.idEmpleado
					LEFT JOIN sia_plazas p ON e.idPlaza = p.idPlaza
				WHERE e.idArea = :laArea AND u.estatus = 'ACTIVO'
				ORDER BY texto;";
			*/
			//$sql="SELECT x.id,  x.usuario + REPLICATE('.', 45-LEN(x.usuario)) + x.plaza  texto
			$sql="SELECT x.id,  x.usuario + '  -  (' + x.plaza + ')'  texto
				FROM ( SELECT distinct u.idEmpleado id, COALESCE(u.saludo,u.saludo,'') + ' ' + u.nombre + ' ' + u.paterno + ' ' + u.materno usuario, 
				 COALESCE(p.nombre, p.nombre, '') plaza
				FROM sia_usuarios u INNER JOIN sia_empleados e on u.idEmpleado = e.idEmpleado LEFT JOIN sia_plazas p ON e.idPlaza = p.idPlaza
				WHERE e.idArea = :laArea AND u.estatus = 'ACTIVO' ) x
				order by x.usuario;";
			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':laArea' => $laArea) );	
		}

		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "RECURSO NO ENCONTRADO.");			
		}else{		
			echo json_encode($result);
		}		
	});


$app->get('/lstUsuariosExternosByEntidadExt/:laArea', function($laArea)  use ($app, $db) {
		$cuenta = $_SESSION["idCuentaActual"];
		$area = $_SESSION["idArea"];
	
		$sql="SELECT distinct u.idUsuarioExterno id, COALESCE(u.saludo,u.saludo,'') + ' ' + u.nombre + ' ' + u.paterno + ' ' + u.materno + '  -  ' + COALESCE(u.cargo, u.cargo, '') texto
      	FROM sia_usuariosExternos u 
		 WHERE u.idEntidadExterna = :laArea AND u.estatus = 'ACTIVO'
		ORDER BY texto;";
		$dbQuery = $db->prepare($sql);

		$dbQuery->execute(array(':laArea' => $laArea) );	
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "RECURSO NO ENCONTRADO.");			
		}else{		
			echo json_encode($result);
		}		
	});

	$app->get('/lstEntidadesExternas', function() use($app, $db){
		
		$sql= "SELECT idEntidadExterna id, isnull(nombre,'') texto FROM sia_EntidadesExternas ORDER BY nombre";
		$dbQuery = $db->prepare($sql);

		$dbQuery->execute();
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "RECURSO NO ENCONTRADO.");			
		}else{		
			echo json_encode($result);
		}		
	});

	$app->post('/guardar/asunto', function()  use($app, $db) {
		
		$cuenta = $_SESSION ["idCuentaActual"];
		$programa = $_SESSION ["idProgramaActual"];
		$usrActual = $_SESSION ["idUsuario"];
		$area = $_SESSION ["idArea"];

		$request=$app->request;
		
		try {

			$oper = $request->post('txtOperacion');
			$accion = $request->post('txtAccion');

			$idAsunto = $request->post('txtFolio');

			$folioAsunto = $request->post('txtIndice');
			$tipoDocto = $request->post('txtTipoDocto');
			$numDocto = $request->post('txtNumeroDocto');

			// CAMPOS DE FECHA
			
			//$fecDocto = $request->post('txtFechaDocto');
			//$fecRecep = $request->post('txtFechaRecepcion');
			//$fecCompro = $request->post('txtFechaCompromiso');


			$fecDocto = date_create( ($request->post('txtFechaDocto')) );
			$fecDocto = $fecDocto->format('Y-m-d');

			$fecRecep = date_create(($request->post('txtFechaRecepcion')));
			$fecRecep = $fecRecep->format('Y-m-d');

			$fecCompro = date_create(($request->post('txtFechaCompromiso')));
			$fecCompro = $fecCompro->format('Y-m-d');

			// FIN CAMPOS DE FECHA

			$tipoOrigenDocto = $request->post('txtTipoOrigenDocto');
			$indice = $request->post('txtIndice');
			$clasificacion = $request->post('txtClasificacionDocto');
			
			$prioridad = $request->post('txtPrioridad');
			$impacto = $request->post('txtImpacto');
			$confidencial = $request->post('txtConfidencial');

			$auditoria = $request->post('txtAuditoria');

			$descripcion = $request->post('txtDescripcion');
 			$descripcion = preg_replace('/\&(.)[^;]*;/', '\\1', $descripcion);
			$descripcion = strtoupper($descripcion);

			$comentario = $request->post('txtComentario');
 			$comentario = preg_replace('/\&(.)[^;]*;/', '\\1', $comentario);
			$comentario = strtoupper($comentario);

			$origenRemitente = $request->post('txtOrigenRemitente');
			$areaRemitente = $request->post('txtAreaRemitente');
			$nombreAreaRemitente = $request->post('txtNombreAreaRemitente');
			$usuarioRemitente = $request->post('txtUsuarioRemitente');
			$nombreUsrRemitente = $request->post('txtNombreUsrRemitente');

			$expediente = $request->post('txtExpediente');
			$serieDocumental = $request->post('txtSerieDocumental');
			$claveBusqueda = $request->post('txtClaveBusqueda');

			$listaDestinatarios = $request->post('txtControlDestinatarios');
			$listaArchivosAnexos = $request->post('txtControlArchivosAnexos');
			/*
			$datos = '=> oper: ' . $oper . '<br>accion:' . $accion . '<br>idAsunto:' . $idAsunto . '<br>cuenta:' . $cuenta . '<br>programa:' . $programa . '<br>auditoria:' . $auditoria . '<br>area:' . $area . '<br>tipoDocto:' . $tipoDocto . '<br>folioAsunto:' .  $folioAsunto . '<br>clasificacionDocto:' . $clasificacion . '<br>numDocto:' . $numDocto . '<br>fecDocto:' . $fecDocto . '<br>indice:' . $indice . '<br>fecRecepcion:' . $fecRecep . '<br>fecCompromiso:' . $fecCompro . '<br>tipoPrioridad:' . $prioridad . '<br>tipoOrigenDocto:' . $tipoOrigenDocto .'<br>serieDocumental:' . $serieDocumental . '<br>descripcion:' . $descripcion . '<br>numExpediente:' . $expediente . '<br>comentario:' .  $comentario . '<br>claveBusqueda:' . $claveBusqueda . '<br>tipoConfidencial:' . $confidencial . '<br>idTipoImpacto:' . $impacto . '<br>usrActual:' . $usrActual . '<br>origenRemitente:' . $origenRemitente . '<br>areaRemitente:' . $areaRemitente . '<br>nombreAreaRemitente:' . $nombreAreaRemitente . '<br>usuarioRemitente:' . $usuarioRemitente . '<br>nombreUsrRemitente:' . $nombreUsrRemitente . '<br>listaDestinatarios:' . $listaDestinatarios . '<=' ;

			echo('El valor de $datos es:<br>' . $datos);

			//----------------------------
				$sql = "INSERT INTO sia_temporal (idAuditoria, datos) VALUES (:auditoria, :datos); ";
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':auditoria' => $idAsunto, ':datos' => $datos));
			//----------------------------
			*/

			if($oper=='INS'){

				if ($accion == 'SOLOGUARDAR' || $accion == 'GUARDARTURNAR' ){
								 
					if($accion == 'SOLOGUARDAR'){ $idSituacion = "EN REGISTRO"; }else{ if($accion == 'GUARDARTURNAR'){ $idSituacion = "TURNADO";
						}
					}

					/*
					$sql="INSERT INTO sia_Asuntos (idCuenta, idPrograma, idArea, idTipoDocto, folioAsunto, fecDocto, fecRecepcion, idTipoPrioridad, tipoOrigenDocto, idtipoAtencion, idTipoConfidencial, idTipoImpacto, fAlta, usrAlta)
	  					VALUES (:cuenta, :programa, :area, :tipoDocto, :folioAsunto, CONVERT(datetime,:fecDocto,120), CONVERT(datetime,:fecRecepcion,120), :tipoPrioridad, :tipoOrigenDocto, 'ATENCION', :tipoConfidencial, :idTipoImpacto, getdate(), :usrAlta); ";

					$dbQuery = $db->prepare($sql);

					$dbQuery->execute(array(':cuenta'=>$cuenta, ':programa'=>$programa, ':area'=>$area, ':tipoDocto' => $tipoDocto, ':folioAsunto'=>$folioAsunto, ':fecDocto' => $fecDocto, ':fecRecepcion' => $fecRecep, ':tipoPrioridad' =>$prioridad, ':tipoOrigenDocto'=>$tipoOrigenDocto, ':tipoConfidencial'=>$confidencial, ':idTipoImpacto'=>$impacto, ':usrAlta'=>$usrActual));


					$sql1 = "INSERT INTO sia_temporal (idAuditoria, datos) VALUES (:auditoria, :datos); ";
					$dbQuery = $db->prepare($sql1);
					$dbQuery->execute(array(':auditoria' => $auditoria, ':datos' => $sql));


					$sql2 = "INSERT INTO sia_temporal (idAuditoria, datos) VALUES (:auditoria, :datos); ";
					$valores = " VALUES('". $cuenta. "','" . $programa. "','" . $area . "','" . $tipoDocto . "','" . $folioAsunto . "',CONVERT(datetime,'". $fecDocto . "',120), CONVERT(datetime,'" .$fecRecep . "',120),'" . $prioridad . "','" . $tipoOrigenDocto . "','ATENCION', '" . $confidencial . "','" . $impacto ."', GetDate(), " . $usrActual . ');' ;

					$dbQuery = $db->prepare($sql2);
					$dbQuery->execute(array(':auditoria' => $auditoria, ':datos' => $valores));

					*/
					$sql="INSERT INTO sia_Asuntos (idCuenta, idPrograma, idAuditoria, idArea, idTipoDocto, folioAsunto, idClasificacionDocto, numDocto, fecDocto, idIndice, fecRecepcion, fecCompromiso, idTipoPrioridad, tipoOrigenDocto, tipoOrigenRemitente, idAreaRemitente, idUsrAreaRemitente, serieDocumental, descripcion, numExpediente, comentario, claveBusqueda, idtipoAtencion, idTipoConfidencial, idSituacion, idTipoImpacto, fAlta, usrAlta) VALUES (:cuenta, :programa, :auditoria, :area, :tipoDocto, :folioAsunto, :clasificacionDocto, :numDocto, CONVERT(DATETIME,:fecDocto,120), :indice, CONVERT(DATETIME,:fecRecepcion,120) , CONVERT(DATETIME,:fecCompromiso,120), :tipoPrioridad, :tipoOrigenDocto, :tipoOrigenRemitente, :idAreaRemitente, :idUsrAreaRemitente, :serieDocumental, :descripcion, :numExpediente, :comentario, :claveBusqueda, 'ATENCION', :tipoConfidencial, :idSituacion, :idTipoImpacto, getdate(), :usrAlta);";

					$dbQuery = $db->prepare($sql);

					$dbQuery->execute(array(':cuenta'=>$cuenta, ':programa'=>$programa, ':auditoria'=>$auditoria, ':area'=>$area, ':tipoDocto' =>$tipoDocto, ':folioAsunto'=>$folioAsunto, ':clasificacionDocto'=>$clasificacion, ':numDocto'=>$numDocto, ':fecDocto' =>$fecDocto, ':indice'=>$indice, ':fecRecepcion'=>$fecRecep, ':fecCompromiso'=>$fecCompro, ':tipoPrioridad'=>$prioridad, ':tipoOrigenDocto'=>$tipoOrigenDocto, ':tipoOrigenRemitente'=>$origenRemitente, ':idAreaRemitente'=> $areaRemitente, ':idUsrAreaRemitente'=>$usuarioRemitente, ':serieDocumental'=>$serieDocumental, ':descripcion'=>$descripcion, ':numExpediente'=>$expediente, ':comentario'=>$comentario, ':claveBusqueda'=>$claveBusqueda, ':tipoConfidencial'=>$confidencial, ':idSituacion'=>$idSituacion, ':idTipoImpacto'=>$impacto, ':usrAlta'=>$usrActual));
				}

				// Recupera el ID del asunto recien ingresado.
				
				$sql="SELECT MAX(idAsunto) id FROM sia_asuntos WHERE idCuenta = :cuenta AND idPrograma = :programa AND numDocto = :numDocto AND idArea = :area AND idTipoDocto = :tipoDocto AND folioAsunto = :folioAsunto AND usrAlta = :usrAlta ";

				$dbQuery = $db->prepare($sql);

				$dbQuery->execute(array(':cuenta' => $cuenta, ':programa' => $programa,  ':numDocto' => $numDocto, ':area' => $area, ':tipoDocto' => $tipoDocto, ':folioAsunto' => $folioAsunto, ':usrAlta' => $usrActual));
				
				$result = $dbQuery->fetch(PDO::FETCH_ASSOC); 

				$idAsunto = $result ['id']; 

				// Se borraran los registros de los documentos anexados previamente a la id del asunto y se daran de alta nuevamente a la BD
				// Este proceso es valido tanto para SOLOGUARDAR como para GUARDARTURNAR

				
				// ************************************************************************************
				//$valorAsunto = $idAsunto;
				//$sql1 = "INSERT INTO sia_temporal (idAuditoria, datos) VALUES (:auditoria, :datos); ";
				//$dbQuery = $db->prepare($sql1);
				//$dbQuery->execute(array(':auditoria' => $valorAsunto, ':datos' => $accion . ' ' .$listaArchivosAnexos));
				// ************************************************************************************
				
				$sql="DELETE FROM sia_AsuntosAnexos WHERE idAsunto=:idAsunto ";
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':idAsunto'=>$idAsunto ));

				if ($listaArchivosAnexos != "") {

					$registrosArchivosAnexos = explode('*', $listaArchivosAnexos);

					if ( count($registrosArchivosAnexos) > 0 ){

						foreach ($registrosArchivosAnexos as $registroArchivoAnexo) {

					    	$campoArchivoAnexo = explode("|", $registroArchivoAnexo);

							$idAsuntoAnexo = $campoArchivoAnexo[0];
							$tipoOrigenAnexo = $campoArchivoAnexo[1];
							// Como es insertar, es decir el asunto es nuevo, entonces vendra con 0, por lo tanto se usara la siguiente linea:
								//$idOrigenAnexo = $campoArchivoAnexo[2];
							$idOrigenAnexo = $idAsunto;
							$archivoOriginal = $campoArchivoAnexo[3];
							$archivoFinal = $campoArchivoAnexo[4];


							$sql="INSERT INTO sia_AsuntosAnexos ( tipoOrigenAnexo, idOrigenAnexo, archivoOriginal, archivoFinal, fAlta, usrAlta) VALUES (:tipoOrigenAnexo, :idOrigenAnexo, :archivoOriginal, :archivoFinal, GETDATE(), :usrAlta); ";

							$dbQuery = $db->prepare($sql);

							$dbQuery->execute(array(':tipoOrigenAnexo' => $tipoOrigenAnexo, ':idOrigenAnexo' => $idOrigenAnexo, ':archivoOriginal' => $archivoOriginal, ':archivoFinal' => $archivoFinal, ':usrAlta' => $usrActual));
						}
					}
				}
				/*
				// ************************************************************************************
				$valorAsunto = $idAsunto;
				$sql1 = "INSERT INTO sia_temporal (idAuditoria, datos) VALUES (:auditoria, :datos); ";
				$dbQuery = $db->prepare($sql1);
				$dbQuery->execute(array(':auditoria' => $valorAsunto, ':datos' => $accion . ' ' .$listaDestinatarios));
				// ************************************************************************************
				*/

				// Ahora se crearan los registros de la tabla temporal previo al proceso de turno, estos datos se guardaran hasta el turno
				// en la tabla sia_AsuntosRecepcion, se guardara un registro por cada destinatario.
				if( $accion == 'SOLOGUARDAR'){

					if ($listaDestinatarios != "") {

						$registrosDestinatarios = explode('*', $listaDestinatarios);

						/*
						// ************************************************************************************
						$valorAsunto = count($registrosDestinatarios);
						$sql1 = "INSERT INTO sia_temporal (idAuditoria, datos) VALUES (:auditoria, :datos); ";
						$dbQuery = $db->prepare($sql1);
						$dbQuery->execute(array(':auditoria' => $valorAsunto, ':datos' => '<= valor de count($registrosDestinatarios)' ));
						// ************************************************************************************
						*/
						if ( count($registrosDestinatarios) > 0 ){

							foreach ($registrosDestinatarios as $registroDestinatario) {

								$tipoAtencion = "";
						    	$camposDestinatario = explode("|", $registroDestinatario);

								/*
								// ************************************************************************************
								$valorAsunto =  count($camposDestinatario);
								$sql1 = "INSERT INTO sia_temporal (idAuditoria, datos) VALUES (:auditoria, :datos); ";
								$dbQuery = $db->prepare($sql1);
								$dbQuery->execute(array(':auditoria' => $valorAsunto, ':datos' => '<= valor de count($camposDestinatario)' . '=>' . $camposDestinatario[0] . '=>' . $camposDestinatario[1] . '=>' . $camposDestinatario[2] . '=>' . $camposDestinatario[3] . '=>' . $camposDestinatario[4] . '=>' . $camposDestinatario[5] ));
								// ************************************************************************************
								*/

								$idUsrDestinatario = $camposDestinatario[0];
								$idAreaDestinatario = $camposDestinatario[1];
								$nombreAreaDestinatario = $camposDestinatario[2];
								$nombreUsuarioDestinatario = $camposDestinatario[3];
								$nombrePlazaDestinatario = $camposDestinatario[4];
								$tipoAtencion = $camposDestinatario[5];
								// El siguiente campo se podría usar para mostrar indicaciones en el mensaje via correo o la aplicación 
								// por cada destinatario, esto también aplica para el campo fecCompromiso para limitar los tiempos de 
								// entrega por destinatario con la regla que esta fecha debe ser menor o igual a la fecha compromiso del 
								// asunto.
								$observaDestinatario = '';

								/*
								// ************************************************************************************
								$valoresAsunto = ':idAsunto => ' . $idAsunto . ' :tipoOrigenRemitente => ' . $origenRemitente . '  :idAreaRemitente => ' . $areaRemitente . ' :idUsrAreaRemitente => ' . $usuarioRemitente . ' :idAreaDestinatario => ' . $idAreaDestinatario . ' :idUsrDestinatario => ' . $idUsrDestinatario .  ' :tipoAtencion => ' . $tipoAtencion . ' :observaDestinatario => ' . $observaDestinatario . ' :fecCompromiso => ' . $fecCompro .  ' :usrAlta => ' . $usrActual;

								echo ($valoresAsunto);
								
								$sql1 = "INSERT INTO sia_temporal (idAuditoria, datos) VALUES (:auditoria, :datos); ";
								$dbQuery = $db->prepare($sql1);
								$dbQuery->execute(array(':auditoria' => $idAsunto, ':datos' => $valoresAsunto ));
								// ************************************************************************************
								*/

								$sql="INSERT INTO sia_AsuntosRecepcion ( idAsunto, tipoOrigenRemitente, idAreaRemitente, idUsrAreaRemitente, tipoOrigenDestinatario, idAreaDestinatario, idUsrDestinatario, tipoAtencion, observaciones, fecCompromiso, fAlta, usrAlta) VALUES (:idAsunto, :tipoOrigenRemitente, :idAreaRemitente, :idUsrAreaRemitente, 'INTERNO', :idAreaDestinatario, :idUsrDestinatario, :tipoAtencion, :observaDestinatario, CONVERT(datetime,:fecCompromiso,120), GETDATE(), :usrAlta); ";

								$dbQuery = $db->prepare($sql);

								$dbQuery->execute(array(':idAsunto' => $idAsunto, ':tipoOrigenRemitente' => $origenRemitente,  ':idAreaRemitente' => $areaRemitente, ':idUsrAreaRemitente' => $usuarioRemitente, ':idAreaDestinatario' => $idAreaDestinatario, ':idUsrDestinatario' => $idUsrDestinatario, ':tipoAtencion' => $tipoAtencion, ':observaDestinatario' => $observaDestinatario, ':fecCompromiso' => $fecCompro, ':usrAlta' => $usrActual));
							}
						}
					}
				}
			
				if( $accion == 'GUARDARTURNAR'){
					// Ahora se dara de alta el registro del turno que refiere al turno usando el ID del asunto
					// Se dara de alta el registo de la tabla de relación ASUNTOSTURNOS.

					$sql="INSERT INTO sia_AsuntosTurnos (idAsunto, tipoOrigenTurnador, idAreaTurnador, idUsrTurnador, fAlta, usrAlta) VALUES (:idAsunto, 'INTERNO', :idAreaTurnador, :idUsrTurnador, getdate(), :usrAlta) ";

					$dbQuery = $db->prepare($sql);
					$dbQuery->execute(array(':idAsunto' => $idAsunto, ':idAreaTurnador' => $area, ':idUsrTurnador' => $usrActual, ':usrAlta' => $usrActual));

					// Recupera el ID del registro asuntosturnos recien ingresado.

					$sql="SELECT MAX(idAsuntoTurno) id FROM sia_AsuntosTurnos WHERE idAsunto = :idAsunto AND tipoOrigenTurnador = 'INTERNO' AND idAreaTurnador = :idAreaTurnador AND idUsrTurnador = :idUsrTurnador AND usrAlta = :usrAlta ";

					$dbQuery = $db->prepare($sql);
					$dbQuery->execute(array(':idAsunto' => $idAsunto, ':idAreaTurnador' => $area, ':idUsrTurnador' => $usrActual, ':usrAlta' => $usrActual));
					$result = $dbQuery->fetch(PDO::FETCH_ASSOC); 
					
					$idAsuntoTurno = $result ['id']; 

					// Ahora se generaran los asuntos de atención para cada usuario que fue seleccionado como destinatario
					// También se lanzan notificaciones tipos correo electrónicos y mensajes de pantalla.
					// Se recuperara la lista de los destinararios

					if ($listaDestinatarios != "") {

						$registrosDestinatarios = explode('*', $listaDestinatarios);

						if ( count($registrosDestinatarios) > 0 ){

							foreach ($registrosDestinatarios as $registroDestinatario) {
						    	$camposDestinatario = explode("|", $registroDestinatario);

								$idUsrDestinatario = $camposDestinatario[0];
								$idAreaDestinatario = $camposDestinatario[1];
								$nombreAreaDestinatario = $camposDestinatario[2];
								$nombreUsuarioDestinatario = $camposDestinatario[3];
								$nombrePlazaDestinatario = $camposDestinatario[4];
								$tipoAtencion = $camposDestinatario[5];

								//if ($atencion) { $tipoAtencion = "ATENCION"; } else { $tipoAtencion = "CONOCIMIENTO"; }

								$mensaje = "Se le ha turnado un asunto para su " . $tipoAtencion . ", el remitente es: \n" . $nombreUsrRemitente . "\n del área \n" . $nombreAreaRemitente . "\n Seleccione la opción ASUNTOS que se encuentra bajo la opción ACCIONES en menú principal para ver el detalle de este turno";

								// Se agrega el registro de turno del usario destinatario.

								$sql="INSERT INTO sia_TurnosDestinatarios (idAsuntoTurno, tipoOrigenDestinatario, idAreaDestinatario, idUsrDestinatario, tipoAtencion, fAlta, usrAlta) VALUES (:idAsuntoTurno, 'INTERNO', :idAreaDestinatario, :idUsrDestinatario, :tipoAtencion, getdate(), :usrAlta);";
								
								$dbQuery = $db->prepare($sql);
								$dbQuery->execute(array(':idAsuntoTurno'=>$idAsuntoTurno, ':idAreaDestinatario'=>$idAreaDestinatario, ':idUsrDestinatario'=>$idUsrDestinatario, ':tipoAtencion'=>$tipoAtencion, 'usrAlta'=> $usrActual));

								// Se agrega el registro de notificación tipo pantalla de aplicación.

								/* Primero validar el funcionamiento y posteriormente habilitar estas lineas
								$sql="INSERT INTO sia_notificacionesmensajes (idNotificacion, idUsuario, mensaje, idPrioridad, idImpacto, usrAlta, fAlta, estatus, situacion) VALUES (11, :idUsuario, :mensaje, :idPrioridad, :idImpacto, :usrAlta, getdate(), 'ACTIVO', 'NUEVO');";
								
								$dbQuery = $db->prepare($sql);
								$dbQuery->execute(array(':idUsuario'=>$idUsrDestinatario, ':mensaje'=>$mensaje, ':idPrioridad'=>$prioridad, ':idImpacto'=>$impacto, 'usrAlta'=> $usrActual));
								*/

							}
						}
					}
				}
				
			}else{  // SECCIÓN DE UPDATE 

				if ($accion == 'SOLOGUARDAR' || $accion == 'GUARDARTURNAR' ){
								 
					if($accion == 'SOLOGUARDAR'){
						$idSituacion = "EN REGISTRO";
					}else{
						if($accion == 'GUARDARTURNAR'){
							$idSituacion = "TURNADO";
						}
					}

					$sql="UPDATE sia_Asuntos SET idAuditoria = :auditoria, idArea = :area, idTipoDocto = :tipoDocto, folioAsunto = :folioAsunto, idClasificacionDocto = :clasificacionDocto, numDocto = :numDocto, fecDocto = CONVERT(datetime,:fecDocto,120), idIndice = :indice, fecRecepcion = CONVERT(datetime,:fecRecepcion,120), fecCompromiso = CONVERT(datetime,:fecCompromiso,120), idTipoPrioridad = :tipoPrioridad, tipoOrigenDocto = :tipoOrigenDocto, tipoOrigenRemitente = :tipoOrigenRemitente, idAreaRemitente = :idAreaRemitente, idUsrAreaRemitente = :idUsrAreaRemitente, serieDocumental = :serieDocumental, descripcion = :descripcion, numExpediente = :numExpediente, comentario = :comentario, claveBusqueda = :claveBusqueda, idtipoAtencion = 'ATENCION', idTipoConfidencial = :tipoConfidencial, idSituacion = :idSituacion, idTipoImpacto =  :idTipoImpacto, fModificacion = getdate(), usrModificacion = :usrModificacion WHERE idAsunto = :idAsunto;";

					$dbQuery = $db->prepare($sql);

					/*
					$datosUpdate = ':auditoria=>' . $auditoria . ' :area=> '. $area . ' :tipoDocto =>' . $tipoDocto . ' :folioAsunto=>' . $folioAsunto . ' :clasificacionDocto=>' . $clasificacion . ' :numDocto=>' . $numDocto . ' :fecDocto =>' . $fecDocto . ' :indice =>' . $indice . ' :fecRecepcion =>' . $fecRecep . ' :fecCompromiso =>' . $fecCompro . ' :tipoPrioridad =>' . $prioridad . ' :tipoOrigenDocto =>' . $tipoOrigenDocto . ' :tipoOrigenRemitente =>' . $origenRemitente . ' :idAreaRemitente =>' . $areaRemitente . ' :idUsrAreaRemitente =>' . $usuarioRemitente . ' :serieDocumental =>' . $serieDocumental . ' :descripcion =>' . $descripcion . ' :numExpediente =>' . $expediente . ' :comentario =>' . $comentario . ' :claveBusqueda =>' . $claveBusqueda . ' :tipoConfidencial =>' . $confidencial . ' :idSituacion =>' . $idSituacion . ' :idTipoImpacto =>' . $impacto . ' :usrModificacion =>' . $usrActual . ' :idAsunto =>' . $idAsunto;

					echo($datosUpdate);
					*/

					$dbQuery->execute(array(':auditoria'=>$auditoria, ':area'=>$area, ':tipoDocto' => $tipoDocto, ':folioAsunto'=>$folioAsunto, ':clasificacionDocto'=>$clasificacion, ':numDocto'=>$numDocto, ':fecDocto' => $fecDocto, ':indice' => $indice, ':fecRecepcion' => $fecRecep, ':fecCompromiso' => $fecCompro, ':tipoPrioridad' =>$prioridad, ':tipoOrigenDocto'=>$tipoOrigenDocto, ':tipoOrigenRemitente' => $origenRemitente, ':idAreaRemitente' => $areaRemitente, ':idUsrAreaRemitente' => $usuarioRemitente, ':serieDocumental'=>$serieDocumental, ':descripcion'=>$descripcion, ':numExpediente'=>$expediente, ':comentario'=>$comentario, ':claveBusqueda'=>$claveBusqueda, ':tipoConfidencial'=>$confidencial, ':idSituacion'=>$idSituacion, ':idTipoImpacto'=>$impacto, ':usrModificacion'=>$usrActual, ':idAsunto'=>$idAsunto));

					// Se borraran los registros de los archivos anexos en la tabla sia_asuntosAnexos, para los archivos físicos no es necesario ya que el proceso de la captura los borra durante su ejecución, este proceso es validao para SOLOTURNAR y GUARDARTURNAR.

					$sql="DELETE FROM sia_AsuntosAnexos WHERE idAsunto=:idAsunto ";
					$dbQuery = $db->prepare($sql);
					$dbQuery->execute(array(':idAsunto'=>$idAsunto ));
	
					if ($listaArchivosAnexos != "") {

							$registrosArchivosAnexos = explode('*', $listaArchivosAnexos);

						if ( count($registrosArchivosAnexos) > 0 ){

							foreach ($registrosArchivosAnexos as $registroArchivoAnexo) {

						    	$campoArchivoAnexo = explode("|", $registroArchivoAnexo);

								$idAsuntoAnexo = $campoArchivoAnexo[0];
								$tipoOrigenAnexo = $campoArchivoAnexo[1];
								$idOrigenAnexo = $campoArchivoAnexo[2];
								$archivoOriginal = $campoArchivoAnexo[3];
								$archivoFinal = $campoArchivoAnexo[4];

								$sql="INSERT INTO sia_AsuntosAnexos ( tipoOrigenAnexo, idOrigenAnexo, archivoOriginal, archivoFinal, fAlta, usrAlta) VALUES (:tipoOrigenAnexo, :idOrigenAnexo, :archivoOriginal, :archivoFinal, GETDATE(), :usrAlta); ";

								$dbQuery = $db->prepare($sql);

								$dbQuery->execute(array(':tipoOrigenAnexo' => $tipoOrigenAnexo, ':idOrigenAnexo' => $idOrigenAnexo, ':archivoOriginal' => $archivoOriginal, ':archivoFinal' => $archivoFinal, ':usrAlta' => $usrActual));
							}
						}
					}
					
				}

				// Ahora se actualizaran los registros de la tabla temporal previo al proceso de turno, estos datos se guardaran hasta el turno
				// en la tabla sia_AsuntosRecepcion, se borraran los existentes para guardar nuevamente un registro por cada destinatario.
				if( $accion == 'SOLOGUARDAR'){

					// Iniciará borrando los registros guardados para este asunto, para remplazar por los actualizados
					$sql="DELETE FROM sia_AsuntosRecepcion WHERE idAsunto=:idAsunto ";
					$dbQuery = $db->prepare($sql);
					$dbQuery->execute(array(':idAsunto'=>$idAsunto ));

					if ($listaDestinatarios != "") {

						// ahora insertará los remitentes del combo de destinatarios.
						$registrosDestinatarios = explode('*', $listaDestinatarios);

						if ( count($registrosDestinatarios) > 0 ){

							foreach ($registrosDestinatarios as $registroDestinatario) {

								$tipoAtencion = "";
						    	$camposDestinatario = explode("|", $registroDestinatario);

								$idUsrDestinatario = $camposDestinatario[0];
								$idAreaDestinatario = $camposDestinatario[1];
								$nombreAreaDestinatario = $camposDestinatario[2];
								$nombreUsuarioDestinatario = $camposDestinatario[3];
								$nombrePlazaDestinatario = $camposDestinatario[4];
								$tipoAtencion = $camposDestinatario[5];
								// El siguiente campo se podría usar para mostrar indicaciones en el mensaje via correo o la aplicación 
								// por cada destinatario, esto también aplica para el campo fecCompromiso para limitar los tiempos de 
								// entrega por destinatario con la regla que esta fecha debe ser menor o igual a la fecha compromiso del 
								// asunto.
								$observaDestinatario = '';

								$sql="INSERT INTO sia_AsuntosRecepcion ( idAsunto, tipoOrigenRemitente, idAreaRemitente, idUsrAreaRemitente, tipoOrigenDestinatario, idAreaDestinatario, idUsrDestinatario, tipoAtencion, observaciones, fecCompromiso, fAlta, usrAlta) VALUES (:idAsunto, :tipoOrigenRemitente, :idAreaRemitente, :idUsrAreaRemitente, 'INTERNO', :idAreaDestinatario, :idUsrDestinatario, :tipoAtencion, :observaDestinatario, CONVERT(datetime,:fecCompromiso,120), GETDATE(), :usrAlta); ";

								$dbQuery = $db->prepare($sql);

								$dbQuery->execute(array(':idAsunto' => $idAsunto, ':tipoOrigenRemitente' => $origenRemitente,  ':idAreaRemitente' => $areaRemitente, ':idUsrAreaRemitente' => $usuarioRemitente, ':idAreaDestinatario' => $idAreaDestinatario, ':idUsrDestinatario' => $idUsrDestinatario, ':tipoAtencion' => $tipoAtencion, ':observaDestinatario' => $observaDestinatario, ':fecCompromiso' => $fecCompro, ':usrAlta' => $usrActual));
							}
						}
					}
				}
			
				if( $accion == 'GUARDARTURNAR'){
					// Ahora se dara de alta el registro del turno que refiere al turno usando el ID del asunto
					// Se dara de alta el registo de la tabla de relación ASUNTOSTURNOS.

					$sql="INSERT INTO sia_AsuntosTurnos (idAsunto, tipoOrigenTurnador, idAreaTurnador, idUsrTurnador, fAlta, usrAlta) VALUES (:idAsunto, 'INTERNO', :idAreaTurnador, :idUsrTurnador, getdate(), :usrAlta) ";

					$dbQuery = $db->prepare($sql);
					$dbQuery->execute(array(':idAsunto' => $idAsunto, ':idAreaTurnador' => $area, ':idUsrTurnador' => $usrActual, ':usrAlta' => $usrActual));

					// Recupera el ID del registro asuntosturnos recien ingresado.

					$sql="SELECT MAX(idAsuntoTurno) id FROM sia_AsuntosTurnos WHERE idAsunto = :idAsunto AND tipoOrigenTurnador = 'INTERNO' AND idAreaTurnador = :idAreaTurnador AND idUsrTurnador = :idUsrTurnador AND usrAlta = :usrAlta ";

					$dbQuery = $db->prepare($sql);
					$dbQuery->execute(array(':idAsunto' => $idAsunto, ':idAreaTurnador' => $area, ':idUsrTurnador' => $usrActual, ':usrAlta' => $usrActual));
					$result = $dbQuery->fetch(PDO::FETCH_ASSOC); 
					
					$idAsuntoTurno = $result ['id']; 

					// Ahora se generaran los asuntos de atención e informativo para cada usuario que fue seleccionado como destinatario
					// y se borrara los registros que en la tabla sia_AsuntosRecepcion existan de el idAsunto 
					// También se lanzan notificaciones tipos correo electrónicos y mensajes de pantalla.

					// Iniciará borrando los registros guardados para este asunto, para remplazar por los actualizados
					$sql="DELETE FROM sia_AsuntosRecepcion WHERE idAsunto=:idAsunto ";
					$dbQuery = $db->prepare($sql);
					$dbQuery->execute(array(':idAsunto'=>$idAsunto ));

					
					// Se recuperara la lista de los destinararios

					if ($listaDestinatarios != "") {

						$registrosDestinatarios = explode('*', $listaDestinatarios);

						if ( count($registrosDestinatarios) > 0 ){

							foreach ($registrosDestinatarios as $registroDestinatario) {
						    	$camposDestinatario = explode("|", $registroDestinatario);

								$idUsrDestinatario = $camposDestinatario[0];
								$idAreaDestinatario = $camposDestinatario[1];
								$nombreAreaDestinatario = $camposDestinatario[2];
								$nombreUsuarioDestinatario = $camposDestinatario[3];
								$nombrePlazaDestinatario = $camposDestinatario[4];
								$tipoAtencion = $camposDestinatario[5];

								//if ($atencion) { $tipoAtencion = "ATENCION"; } else { $tipoAtencion = "CONOCIMIENTO"; }

								$mensaje = "Se le ha turnado un asunto para su " . $tipoAtencion . ", el remitente es: \n" . $nombreUsrRemitente . "\n del área \n" . $nombreAreaRemitente . "\n Seleccione la opción ASUNTOS que se encuentra bajo la opción ACCIONES en menú principal para ver el detalle de este turno";

								// Se agrega el registro de turno del usario destinatario.

								$sql="INSERT INTO sia_TurnosDestinatarios (idAsuntoTurno, tipoOrigenDestinatario, idAreaDestinatario, idUsrDestinatario, tipoAtencion, fAlta, usrAlta) VALUES (:idAsuntoTurno, 'INTERNO', :idAreaDestinatario, :idUsrDestinatario, :tipoAtencion, getdate(), :usrAlta);";
								
								$dbQuery = $db->prepare($sql);
								$dbQuery->execute(array(':idAsuntoTurno'=>$idAsuntoTurno, ':idAreaDestinatario'=>$idAreaDestinatario, ':idUsrDestinatario'=>$idUsrDestinatario, ':tipoAtencion'=>$tipoAtencion, 'usrAlta'=> $usrActual));

								// Se agrega el registro de notificación tipo pantalla de aplicación.

								/* Primero validar el funcionamiento y posteriormente habilitar estas lineas
								$sql="INSERT INTO sia_notificacionesmensajes (idNotificacion, idUsuario, mensaje, idPrioridad, idImpacto, usrAlta, fAlta, estatus, situacion) VALUES (11, :idUsuario, :mensaje, :idPrioridad, :idImpacto, :usrAlta, getdate(), 'ACTIVO', 'NUEVO');";
								
								$dbQuery = $db->prepare($sql);
								$dbQuery->execute(array(':idUsuario'=>$idUsrDestinatario, ':mensaje'=>$mensaje, ':idPrioridad'=>$prioridad, ':idImpacto'=>$impacto, ':usrAlta'=> $usrActual));
								*/

							}
						}
					}
				}
			}
	
		}catch (Exception $e) {
			//print "¡Error!: " . $e->getMessage() . "<br/>";
			echo ("¡Error!: " . $e->getMessage() . "<br/>" );
			//die();
		}
		$app->redirect($app->urlFor('lstAsuntos'));
	});

	$app->get('/obtenerAsunto/:idAsunto', function($idAsunto)  use ($app, $db) {
		
		$sql="SELECT a.idAsunto, a.idCuenta, a.idPrograma, a.idAuditoria, a.idArea, a.idTipoDocto, a.folioAsunto, a.idClasificacionDocto, a.NumDocto, REPLACE(ISNULL(CONVERT(VARCHAR(10),a.fecDocto,105), ''),'1900-01-01', '') fecDocto, i.idTipoIndice, a.idIndice, REPLACE(ISNULL(CONVERT(VARCHAR(10),a.fecRecepcion,105), ''), '1900-01-01', '') fecRecepcion, REPLACE(ISNULL(CONVERT(VARCHAR(10), fecCompromiso,105), ''), '1900-01-01', '') fecCompromiso, a.idTipoPrioridad, a.tipoOrigenDocto, a.tipoOrigenRemitente, a.idAreaRemitente, a.idUsrAreaRemitente, a.serieDocumental, a.descripcion, a.NumExpediente, a.comentario, a.claveBusqueda, a.idTipoAtencion, a.idTipoConfidencial, a.idSituacion, a.fAlta, a.usrAlta, a.fModificacion, a.usrModificacion, a.estatus, a.idTipoImpacto FROM sia_Asuntos a inner join sia_Indices i ON a.idIndice = i.idIndice WHERE a.idAsunto = :idAsunto;";
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':idAsunto' => $idAsunto));
		//$result ['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS.");
		}else{		
			echo json_encode($result);
		}		

	});


	$app->get('/obtenerDestinatariosTemporales/:idAsunto', function($idAsunto)  use ($app, $db) {
		
		$sql= "SELECT asu.idAsuntoRecepcion, asu.idAsunto, asu.tipoOrigenRemitente, asu.idAreaRemitente, are.nombre nombreAreaRemitente
  				, asu.idUsrAreaRemitente, asu.tipoOrigenDestinatario, asu.idAreaDestinatario, are1.nombre nombreAreaDestinatario
  				, asu.idUsrDestinatario, COALESCE(usu.saludo,usu.saludo,'') + ' ' + usu.nombre + ' ' + usu.paterno + ' ' + usu.materno nombreDestinatario
          		, COALESCE(pla.nombre, pla.nombre, '') plazaDestinatario
          		, asu.tipoAtencion, asu.observaciones, asu.fecCompromiso, asu.estatus, asu.usrModificacion
  				, asu.fModificacion, asu.usrAlta, asu.fAlta
			FROM sia_AsuntosRecepcion asu 
  				INNER JOIN sia_areas are ON asu.idAreaRemitente = are.idArea 
  				INNER JOIN sia_areas are1 ON asu.idAreaDestinatario = are1.idArea 
  				LEFT JOIN sia_empleados emp ON asu.idUsrDestinatario = emp.idEmpleado
  				LEFT JOIN sia_usuarios usu ON emp.idEmpleado = usu.idEmpleado
  				LEFT JOIN sia_plazas pla ON emp.idPlaza = pla.idPlaza
			WHERE asu.idAsunto = :idAsunto 
			ORDER BY asu.tipoAtencion;";

		$dbQuery = $db->prepare($sql);		
		$dbQuery->execute(array(':idAsunto' => $idAsunto));
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "RECURSO NO ENCONTRADO.");			
		}else{		
			echo json_encode($result);
		}		
	});

	$app->get('/obtenerDestinatariosTurnados/:idAsunto', function($idAsunto)  use ($app, $db) {
		
		$sql= "SELECT asu.idTurnoDestinatario, asu.idAsuntoTurno, asu.tipoOrigenDestinatario, asu.idAreaDestinatario, are.nombre nombreAreaDestinatario
  				, asu.idUsrDestinatario, COALESCE(usu.saludo,usu.saludo,'') + ' ' + usu.nombre + ' ' + usu.paterno + ' ' + usu.materno nombreDestinatario
          		, COALESCE(pla.nombre, pla.nombre, '') plazaDestinatario
          		, asu.tipoAtencion, asu.observaciones, asu.fecCompromiso, asu.estatus, asu.usrModificacion
  				, asu.fModificacion, asu.usrAlta, asu.fAlta
			FROM sia_TurnosDestinatarios asu 
  				INNER JOIN sia_areas are ON asu.idAreaDestinatario = are.idArea 
  				LEFT JOIN sia_empleados emp ON asu.idUsrDestinatario = emp.idEmpleado
  				LEFT JOIN sia_usuarios usu ON emp.idEmpleado = usu.idEmpleado
  				LEFT JOIN sia_plazas pla ON emp.idPlaza = pla.idPlaza
			WHERE asu.idAsuntoTurno = :idAsunto 
			ORDER BY asu.tipoAtencion;";

		$dbQuery = $db->prepare($sql);		
		$dbQuery->execute(array(':idAsunto' => $idAsunto));
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "RECURSO NO ENCONTRADO.");			
		}else{		
			echo json_encode($result);
		}		
	});

	// Procesos de GESTION en expecificos de ARCHIVOS ANEXOS

	$app->get('/obtenerArchivosAnexosByOrigenIdOrigen/:origen/:idOrigen', function($origen, $idOrigen)  use ($app, $db) {
		
		$sql= "SELECT aa.idAsuntoAnexo id, aa.tipoOrigenAnexo origen, aa.idOrigenAnexo idOrigen, aa.archivoOriginal archivoOriginal
				, aa.archivoFinal archivoFinal, aa.fAlta, aa.usrAlta, aa.fModificacion, aa.usrModificacion, aa.estatus
				FROM sia_AsuntosAnexos aa WHERE aa.tipoOrigenAnexo = :origen AND aa.idOrigenAnexo = :idOrigen;";

		$dbQuery = $db->prepare($sql);		
		$dbQuery->execute(array(':origen' => $origen, ':idOrigen' => $idOrigen));
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "RECURSO NO ENCONTRADO.");			
		}else{		
			echo json_encode($result);
		}		
	});

	$app->get('/guardarArchivoAnexo/:origen/:idOrigen/:archivoOriginal/:archivoFinal', function($origen, $idOrigen, $archivoOriginal, $archivoFinal)  use ($app, $db) {
		
		$sql= "INSERT INTO sia_AsuntosAnexos (tipoOrigenAnexo, idOrigenAnexo, archivoOriginal, archivoFinal, fAlta, usrAlta) 
				VALUES (:origen, :idOrigen, :archivoOriginal, :archivoFinal, getdate(), :usrAlta);";

		$dbQuery = $db->prepare($sql);		
		$dbQuery->execute(array(':origen' => $origen, ':idOrigen' => $idOrigen, ':archivoOriginal' => $archivoOriginal, ':archivoFinal' => $archivoFinal, ':usrAlta' => $usrActual));

	});

	$app->get('/obtenerTipoAuditoria/:id', function($id)    use($app, $db) {
		$sql="SELECT idTipoAuditoria id, nombre, estatus FROM sia_tiposauditoria WHERE idTipoAuditoria=:id ";
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':id' => $id));

		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});

	$app->get('/obtenerTipoAuditoriaById/:idTipoAuditoria', function($idTipoAuditoria) use($app, $db) {

		$dbQuery = $db->prepare("SELECT COUNT(*) total FROM sia_tiposAuditoria WHERE idTipoAuditoria = :idTipoAuditoria;");  

		$dbQuery->execute(array(':idTipoAuditoria'=> $idTipoAuditoria));
		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		} 
	});

	$app->get('/obtenerTipoAuditoriaByNombre/:nombre', function($nombre) use($app, $db) {

		$dbQuery = $db->prepare("SELECT COUNT(*) total FROM sia_tiposAuditoria WHERE nombre = :nombre;");  

		$dbQuery->execute(array(':nombre'=> $nombre));
		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		} 
	});


	$app->post('/guardar/tipoAuditoria', function()  use($app, $db) {
		$usrActual = $_SESSION ["idUsuario"];
		$cuenta = $_SESSION ["idCuentaActual"];

		$request=$app->request;

		$oper = $request->post('txtOperacion');
		$id = $request->post('txtIdTipoAuditoria');
		$nombre = strtoupper($request->post('txtNombreTipoAuditoria'));
		$estatus = $request->post('txtEstatus');

		//echo ("<br>oper=>$oper <br>id=>$id <br>nombre=>$nombre <br>$estatus=>$estatus <br>$usrActual=>$usrActual" );
		try
		{
			if($oper=='INS')
			{
				$sql="INSERT INTO sia_tiposauditoria (idTipoAuditoria, nombre, usrAlta, fAlta, estatus) " .
				"VALUES(:id, :nombre, :usrActual, getdate(), 'ACTIVO');";
				$dbQuery = $db->prepare($sql);

				$dbQuery->execute(array(':id' => $id, ':nombre' => $nombre, ':usrActual' => $usrActual ));
			
			}else{
			
				$sql="UPDATE sia_tiposauditoria SET nombre=:nombre, usrModificacion=:usrActual, " .
				" fModificacion=getdate(), estatus=:estatus WHERE idtipoAuditoria=:id";
				$dbQuery = $db->prepare($sql);

				$dbQuery->execute(array(':nombre' => $nombre, ':usrActual' => $usrActual, ':estatus' => $estatus, ':id' => $id ));
			}

			//echo nl2br("\nQuery Ejecutado : ".$sql);

		}catch (DBException $e) {
			print "¡Error!: " . $e->getMessage() . "<br/>";
			die();
		}
		$app->redirect($app->urlFor('catTiposAuditorias'));
	});


	//Guarda un nuevo comentario para la auditoria 

	$app->get('/guardar/auditoriaComentarios/:valores', function($valores)  use($app, $db) {
		$usrActual = $_SESSION ["idUsuario"];
		$cuenta    = $_SESSION ["idCuentaActual"];
		$programa  = $_SESSION ["idProgramaActual"];


		$aValores = explode("|", $valores);

		$oper           	   = $aValores [0];
		$idAuditoria       	   = $aValores [1];
		$idAuditoriaComentario = $aValores [2];
		$comentario            = $aValores [3];
		$idPrioridad           = $aValores [4];
		$estatus 		       = $aValores [5];
		$seccion 		       = $aValores [6];

		//$valores = 'oper=>' . $oper . 'idCuenta=>' . $cuenta . ' idPrograma=>' . $programa . ' idAuditoria=>' . $idAuditoria . ' comentario=>' . $comentario . ' idAuditoriaComentario=>' . $idAuditoriaComentario . ' idPrioridad=>' . $idPrioridad . ' $estatus=>' .$estatus;
		//echo $valores;

		try
		{
			if($oper=='INS')
			{
				$sql="INSERT INTO sia_AuditoriasComentarios (idCuenta, idPrograma, idAuditoria, comentario, idPrioridad, usrAlta, fAlta, seccion) VALUES (:idCuenta, :idPrograma, :idAuditoria, :comentario, :idPrioridad, :usrAlta, getdate(), :seccion );";

				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':idCuenta'=>$cuenta, ':idPrograma'=>$programa, ':idAuditoria'=>$idAuditoria, ':comentario'=>$comentario, ':idPrioridad'=>$idPrioridad, ':usrAlta'=>$usrActual, ':seccion'=>$seccion));
			}
			else
			{
				$sql="UPDATE sia_AuditoriasComentarios SET comentario=:comentario, idPrioridad=:idPrioridad ,usrModificacion=:usrModificacion, fModificacion=getdate(), estatus=:estatus, seccion=:seccion WHERE idCuenta=:idCuenta AND idPrograma=:idPrograma AND idAuditoria=:idAuditoria AND idAuditoriaComentario=:idAuditoriaComentario";
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':comentario'=>$comentario, ':idPrioridad'=>$idPrioridad, ':usrModificacion'=>$usrActual, ':estatus'=>$estatus, ':seccion'=>$seccion, ':idCuenta'=>$cuenta, ':idPrograma'=>$programa, ':idAuditoria'=>$idAuditoria, ':idAuditoriaComentario'=>$idAuditoriaComentario));
			}
			echo("OK");
		}catch (Exception $e) {
			print "¡Error!: " . $e->getMessage() . "<br/>";
			die();
		}

				//$app->redirect($app->urlFor('listaAuditoriaCriterios'));
	});

	$app->get('/tblComentariosByAuditoria/:id/:seccion', function($id, $seccion)  use($app, $db) {
        $sql="SELECT DISTINCT 'Registrado el ' + CONVERT(VARCHAR(10), ac.fAlta, 103) + ' a las ' + SUBSTRING(CONVERT(VARCHAR(10), ac.fAlta, 108),1,5) fechayhora, ac.idAuditoriaComentario id, u.saludo + ' ' + u.nombre + ' ' + u.paterno + ' ' + u.materno usuario, ac.comentario comentario FROM sia_auditoriasComentarios ac INNER JOIN sia_usuarios u ON ac.usrAlta = u.idUsuario WHERE ac.idAuditoria=:id AND ac.Seccion = :seccion ORDER BY ac.idAuditoriaComentario DESC;";

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':id' => $id, ':seccion' => $seccion));

		// USO DEL FETCH PARA CUANDO SE REGRESA UN GRUPO DE REGISTROS
		$result ['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});


	$app->get('/catPuestos',  $autenticacionrole, function()   use($app, $db) {

		$dbQuery = $db->prepare("SELECT idPuesto id, nombre, nombreCorto, estatus FROM sia_puestos ORDER BY nombre;");  
		$dbQuery->execute();
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			$app->render('catPuestos.php', $result);
		} 
	})->name('catPuestos');


	$app->get('/obtenerPuesto/:idPuesto', function($idPuesto)   use($app, $db) {

		$dbQuery = $db->prepare("SELECT idPuesto id, nombre, nombreCorto nombreCorto, estatus FROM sia_puestos WHERE idPuesto=:idPuesto ORDER BY nombre;");  
		$dbQuery->execute(array(':idPuesto' => $idPuesto));

		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		} 
	});


	$app->get('/puestosEmpleados',  $autenticacionrole, function()   use($app, $db) {

		$dbQuery = $db->prepare("SELECT e.idEmpleado id, a.nombre area, concat(e.nombre, ' ', e.paterno, ' ', e.materno) empleado, p.nombre puesto, e.estatus FROM sia_empleados e INNER JOIN sia_areas a ON e.idArea = a.idArea LEFT JOIN sia_puestos p ON e.idPuesto = p.idPuesto  
		ORDER BY a.nombre, concat(e.nombre, ' ', e.paterno, ' ', e.materno);");  
		$dbQuery->execute();
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			$app->render('PuestosEmpleados.php', $result);
		} 
	})->name('puestosEmpleados');


	$app->post('/guardar/puesto', function()  use($app, $db) {

		$cuenta = $_SESSION ["idCuentaActual"];
		$programa = $_SESSION ["idProgramaActual"];
		$usrActual = $_SESSION ["idUsuario"];
		$area = $_SESSION ["idArea"];

		$request     = $app->request;
		$oper        = $request->post('txtOperacion');
		$idPuesto    = $request->post('txtIdPuesto');
		$nombre      = $request->post('txtNombre');
		$nombreCorto = $request->post('txtNombreCorto');
		$estatus     = $request->post('txtEstatus');

		//echo( "oper=>" . $oper . " idPuesto=>" . $idPuesto  . " nombre=>" . $nombre . " nombreCorto=>" . $nombreCorto . " estatus=>" . $estatus );

		try{

			if($oper == 'INS'){

				$sql="INSERT INTO sia_puestos (idPuesto, nombre, nombreCorto, usrAlta, fAlta, estatus) VALUES (:idPuesto, upper(:nombre), upper(:nombreCorto), :usrAlta, getdate(), 'ACTIVO');";
										
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':idPuesto'=>$idPuesto, ':nombre'=>$nombre, ':nombreCorto'=>$nombreCorto, ':usrAlta'=> $usrActual));
			
			}else{

				$sql="UPDATE sia_puestos SET nombre=:nombre, nombreCorto=:nombreCorto, usrModificacion=:usrModificacion, fModificacion=getdate(), estatus=:estatus WHERE idPuesto=:idPuesto";
										
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':nombre'=>$nombre, ':nombreCorto'=>$nombreCorto, ':usrModificacion'=>$usrActual, ':estatus'=>$estatus, ':idPuesto'=>$idPuesto));

			}

		}catch (Exception $e) {
		  	print "ERROR: " . $e->getMessage() . "<br><br> QUERY: " . $dbQuery ;
	  		die();
	 	}
		
		$app->redirect($app->urlFor('catPuestos'));
	});

	$app->get('/obtenerEmpleado/:idEmpleado', function($idEmpleado)   use($app, $db) {
		$dbQuery = $db->prepare("SELECT e.idEmpleado id, a.nombre area, concat(e.nombre, ' ', e.paterno, ' ', e.materno) empleado, isnull(p.idPuesto,' ') idPuesto, isnull(p.nombre,' ') puesto, isnull(e.estatus,' ') estatus FROM sia_empleados e INNER JOIN sia_areas a ON e.idArea = a.idArea LEFT JOIN sia_puestos p ON e.idPuesto = p.idPuesto  
		WHERE e.idEmpleado = :idEmpleado ORDER BY a.nombre, concat(e.nombre, ' ', e.paterno, ' ', e.materno);");  

		$dbQuery->execute(array(':idEmpleado'=>$idEmpleado));
		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		} 
	});

	$app->get('/lstPuestos', function()   use($app, $db) {

		$dbQuery = $db->prepare("SELECT idPuesto id, nombre texto FROM sia_puestos ORDER BY nombre;");  
		$dbQuery->execute();
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		} 
	});

	$app->get('/obtenerPuestosByNombre/:nombre', function($nombre) use($app, $db) {

		$dbQuery = $db->prepare("SELECT COUNT(*) total FROM sia_puestos WHERE nombre = :nombre;");  

		$dbQuery->execute(array(':nombre'=> $nombre));
		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		} 
	});

	$app->get('/obtenerPuestosById/:idPuesto', function($idPuesto) use($app, $db) {

		$dbQuery = $db->prepare("SELECT COUNT(*) total FROM sia_puestos WHERE idPuesto = :idPuesto;");  

		$dbQuery->execute(array(':idPuesto'=> $idPuesto));
		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		} 
	});

	$app->get('/obtenerPuestosByNombreCorto/:nombreCorto', function($nombreCorto) use($app, $db) {

		$dbQuery = $db->prepare("SELECT COUNT(*) total FROM sia_puestos WHERE nombreCorto = :nombreCorto;");  

		$dbQuery->execute(array(':nombreCorto'=> $nombreCorto));
		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		} 
	});


	$app->post('/guardar/puestoEmpleado', function()  use($app, $db) {

		$cuenta = $_SESSION ["idCuentaActual"];
		$programa = $_SESSION ["idProgramaActual"];
		$usrActual = $_SESSION ["idUsuario"];
		$area = $_SESSION ["idArea"];

		$request     = $app->request;
		$oper        = $request->post('txtOperacion');
		$idEmpleado  = $request->post('txtIdEmpleado');
		$idPuesto    = $request->post('txtPuestos');

		//echo( "oper=>" . $oper . " idEmpleado=>" . $idEmpleado  . " idPuesto=>" . $idPuesto );

		try{

			if($oper == 'UPD'){
			
				$sql="UPDATE sia_empleados SET idPuesto=:idPuesto, usrModificacion=:usrModificacion, fModificacion=getdate() WHERE idEmpleado=:idEmpleado";
										
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':idPuesto'=>$idPuesto, ':usrModificacion'=>$usrActual, ':idEmpleado'=>$idEmpleado));
			}

		}catch (Exception $e) {
		  	print "ERROR: " . $e->getMessage() . "<br><br> QUERY: " . $dbQuery ;
	  		die();
	 	}
	
		$app->redirect($app->urlFor('puestosEmpleados'));
	});

	$app->get('/catTiposCriterios',  $autenticacionrole, function()   use($app, $db) {

		$dbQuery = $db->prepare("SELECT idTipoCriterio id, nombre, relacionarEntes, estatus FROM sia_TiposCriterios ORDER BY nombre;");  
		$dbQuery->execute();
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			$app->render('catTiposCriterios.php', $result);
		} 
	})->name('catTiposCriterios');


	$app->get('/obtenerTipoCriterio/:idTipoCriterio', function($idTipoCriterio)   use($app, $db) {

		$dbQuery = $db->prepare("SELECT idTipoCriterio id, nombre, relacionarEntes, estatus FROM sia_TiposCriterios WHERE idTipoCriterio=:idTipoCriterio ORDER BY nombre;");  
		$dbQuery->execute(array(':idTipoCriterio' => $idTipoCriterio));

		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		} 
	});

	$app->post('/guardar/tipoCriterio', function()  use($app, $db) {

		$cuenta = $_SESSION ["idCuentaActual"];
		$programa = $_SESSION ["idProgramaActual"];
		$usrActual = $_SESSION ["idUsuario"];
		$area = $_SESSION ["idArea"];

		$request     = $app->request;
		$oper           = $request->post('txtOperacion');
		$idTipoCriterio = $request->post('txtIdTipoCriterio');
		$nombre         = $request->post('txtNombre');
		$estatus        = $request->post('txtEstatus');
		if ($request->post('chkRelEntidades')) { $relacionarEntes = 'SI'; } else { $relacionarEntes = 'NO'; }

		//echo( "oper=>" . $oper . " idTipoCriterio=>" . $idTipoCriterio  . " nombre=>" . $nombre . " estatus=>" . $estatus );

		try{

			if($oper == 'INS'){

				$sql="INSERT INTO sia_TiposCriterios (nombre, relacionarEntes, usrAlta, fAlta, estatus) VALUES (upper(:nombre), :relacionarEntes, :usrAlta, getdate(), 'ACTIVO');";
										
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':nombre'=>$nombre, ':relacionarEntes'=> $relacionarEntes, ':usrAlta'=> $usrActual));
			
			}else{

				$sql="UPDATE sia_TiposCriterios SET nombre=:nombre, relacionarEntes=:relacionarEntes, usrModificacion=:usrModificacion, fModificacion=getdate(), estatus=:estatus WHERE idTipoCriterio=:idTipoCriterio";
										
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':nombre'=>$nombre, ':relacionarEntes'=>$relacionarEntes, ':usrModificacion'=>$usrActual, ':estatus'=>$estatus, ':idTipoCriterio'=>$idTipoCriterio));
			}

		}catch (Exception $e) {
		  	print "ERROR: " . $e->getMessage() . "<br><br> QUERY: " . $dbQuery ;
	  		die();
	 	}
		
		$app->redirect($app->urlFor('catTiposCriterios'));
	});

	$app->get('/guardarNotificacion/:idAuditoria/:idUsuario/:mensaje/:idPrioridad/:idImpacto', function($idAuditoria, $idUsuario, $mensaje, $idPrioridad, $idImpacto )  use($app, $db) {

		$cuenta    = $_SESSION ["idCuentaActual"];
		$programa  = $_SESSION ["idProgramaActual"];
		$usrActual = $_SESSION ["idUsuario"];
		$area      = $_SESSION ["idArea"];

		$sql="INSERT INTO sia_notificacionesmensajes (idNotificacion, idUsuario, mensaje, idPrioridad, idImpacto, usrAlta, fAlta, estatus, situacion, idAuditoria) VALUES (11, :idUsuario, :mensaje, :idPrioridad, :idImpacto, :usrAlta, getdate(), 'ACTIVO', 'NUEVO', :idAuditoria);";
								
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':idUsuario'=>$idUsuario, ':mensaje'=>$mensaje, ':idPrioridad'=>$idPrioridad, ':idImpacto'=>$idImpacto, ':usrAlta'=> $usrActual, ':idAuditoria'=> $idAuditoria));
	});

	$app->get('/criteriosSeleccion',  $autenticacionrole, function() use($app, $db) {

		$dbQuery = $db->prepare("SELECT ca.idCriterioAuxiliar id, tc.nombre tipoCriterio, ca.fuente, CONVERT(VARCHAR(10), ca.fecInformacion, 101) fecInformacion, ca.estatus FROM sia_CriteriosAuxiliares ca INNER JOIN sia_tiposCriterios tc ON ca.idTipoCriterio = tc.idTipoCriterio ORDER BY ca.fecInformacion desc;");  
		$dbQuery->execute();
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			$app->render('criteriosSeleccion.php', $result);
		} 
	})->name('criteriosSeleccion');



	$app->get('/lstTiposCriterios', function() use($app, $db) {

		$dbQuery = $db->prepare("SELECT idTipoCriterio id, nombre texto FROM sia_TiposCriterios ORDER BY nombre;");  
		$dbQuery->execute();
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		} 
	});

	$app->get('/obtenerCriterioSeleccionById/:idCriterioAuxiliar', function($idCriterioAuxiliar) use($app, $db) {

		$dbQuery = $db->prepare("SELECT ca.idCriterioAuxiliar id, ca.idTipoCriterio, tc.nombre tipoCriterio, ca.fuente, ca.informacion, ca.fecInformacion, ca.estatus FROM sia_CriteriosAuxiliares ca INNER JOIN sia_tiposCriterios tc ON ca.idTipoCriterio = tc.idTipoCriterio WHERE ca.idCriterioAuxiliar = :idCriterioAuxiliar;");  
		$dbQuery->execute(array(':idCriterioAuxiliar'=> $idCriterioAuxiliar));
		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		} 
	});

	$app->get('/obtenerTiposCriterioByNombre/:nombreCriterio', function($nombreCriterio) use($app, $db) {

		$dbQuery = $db->prepare("SELECT COUNT(*) total FROM sia_tiposCriterios WHERE nombre = :nombre;");  

		$dbQuery->execute(array(':nombre'=> $nombreCriterio));
		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		} 
	});


	$app->get('/buscarNombreCriterio/:idCriterioAuxiliar', function($idCriterioAuxiliar) use($app, $db) {

		$dbQuery = $db->prepare("SELECT ca.idCriterioAuxiliar id, ca.idTipoCriterio, tc.nombre tipoCriterio, ca.fuente, ca.informacion, CONVERT(VARCHAR(10), ca.fecInformacion, 103) fecInformacion, ca.estatus FROM sia_CriteriosAuxiliares ca INNER JOIN sia_tiposCriterios tc ON ca.idTipoCriterio = tc.idTipoCriterio WHERE ca.idCriterioAuxiliar = :idCriterioAuxiliar;");  
		$dbQuery->execute(array(':idCriterioAuxiliar'=> $idCriterioAuxiliar));
		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		} 
	});



	$app->post('/guardar/criterioSeleccion', function()  use($app, $db) {

		$cuenta = $_SESSION ["idCuentaActual"];
		$programa = $_SESSION ["idProgramaActual"];
		$usrActual = $_SESSION ["idUsuario"];
		$area = $_SESSION ["idArea"];

		$request            = $app->request;
		$oper               = $request->post('txtOperacion');
		$idCriterioAuxiliar = $request->post('txtIdCriterio');
		$idTipoCriterio     = $request->post('txtTipoCriterio');
		$fecInformacion     = date_create(($request->post('txtFecInformacion')));
		//$fecInformacion = date_create(('19/01/2017'));
		$fecInformacion     = $fecInformacion->format('Y-m-d');

		$fuente    		= $request->post('txtFuenteInfo');
		$informacion 	= $request->post('txtInformacion');
		$estatus     	= $request->post('txtEstatus');

		$lstUnidadesRelacionadas = $request->post('txtListaUnidadesSeleccionadas');

		//echo( "oper=>" . $oper . " idCriterioAuxiliar=>" . $idCriterioAuxiliar  . " idTipoCriterio=>" . $idTipoCriterio  . " fecInformacion=>" . $fecInformacion  . " fuente=>" . $fuente . " informacion=>" . $informacion .  " estatus=>" . $estatus . " cuenta=>" . $cuenta . " programa=>" . $programa . " usrActual=>" . $usrActual);

		try{

			if($oper == 'INS'){
				// Primero guarda el nuevo citerio.
				$idCriterioAuxiliar = 0;
				$sql="INSERT INTO sia_CriteriosAuxiliares (idCuenta, idPrograma, idTipoCriterio, fuente, fecInformacion, informacion, fAlta, usrAlta) VALUES (:cuenta, :programa, :idTipoCriterio, :fuente, :fecInformacion, :informacion, getdate(), :usrAlta ) ;";
										
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':cuenta'=>$cuenta, ':programa'=>$programa, ':idTipoCriterio'=>$idTipoCriterio, ':fuente'=>$fuente, ':fecInformacion'=>$fecInformacion, ':informacion'=>$informacion, ':usrAlta'=> $usrActual));

				// Segundo recupera el idCriterioAuxiliar que recien se generó de manera automática (campo IDENTITY) con el insert anterior.
				
				//$sql="SELECT idCriterioAuxiliar FROM  sia_CriteriosAuxiliares WHERE idCuenta=:idCuenta AND idPrograma=:idPrograma AND idTipoCriterio=:idTipoCriterio AND fuente=:fuente AND informacion=:informacion AND fecInformacion=:fecInformacion AND usrAlta=:usrAlta";
				$dbQuery = $db->prepare("SELECT SCOPE_IDENTITY() as idCriterioAuxiliar; ");
				$dbQuery->execute();
				$result = $dbQuery->fetch(PDO::FETCH_ASSOC); 
				$idCriterioAuxiliar = $result ['idCriterioAuxiliar']; 

			}else{

				$sql="UPDATE sia_CriteriosAuxiliares SET idTipoCriterio=:idTipoCriterio, fuente=:fuente, informacion=:informacion, fecInformacion=:fecInformacion, usrModificacion=:usrModificacion, fModificacion=getdate(), estatus=:estatus WHERE idCuenta=:idCuenta AND idPrograma=:idPrograma AND idCriterioAuxiliar=:idCriterioAuxiliar";

					//echo( "oper=>" . $oper . " idCriterioAuxiliar=>" . $idCriterioAuxiliar  . " idTipoCriterio=>" . $idTipoCriterio . " fuente=>" . $fuente . " fecInformacion=>" . $fecInformacion . " estatus=>" . $estatus . " cuenta=>" . $cuenta . " programa=>" . $programa . " usrActual=>" . $usrActual . " Sql=>*" . $sql . "*");

										
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':idTipoCriterio'=>$idTipoCriterio, ':fuente'=>$fuente, ':informacion'=>$informacion, ':fecInformacion'=>$fecInformacion, ':usrModificacion'=>$usrActual, ':estatus'=>$estatus, ':idCuenta'=>$cuenta, ':idPrograma'=>$programa, ':idCriterioAuxiliar'=>$idCriterioAuxiliar));

			}

			if($idCriterioAuxiliar>0){

				// Borrará todos los registros de la tabla sia_criteriosUnidadess que contengan el número de idCriterioAuxiliar. 
				$sql = "DELETE FROM sia_CriteriosUnidades WHERE idCriterioAuxiliar=:idCriterioAuxiliar";
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':idCriterioAuxiliar'=>$idCriterioAuxiliar ) );

				if($lstUnidadesRelacionadas!=""){

					$aUnidades = explode("*", $lstUnidadesRelacionadas);
					if( count($aUnidades) > 0 ){

						foreach ($aUnidades as $aUnidad) {
							$aCampos = explode("|", $aUnidad);
							$idCuenta    = $aCampos[0]; 
							$idSector    = $aCampos[1]; 
							$idSubsector = $aCampos[2]; 
							$idUnidad    = $aCampos[3]; 

							// Insertará uno a uno los registros de relacion entre el criterio y las unidades relacionadas.
							$sql="INSERT INTO sia_CriteriosUnidades (idCriterioAuxiliar, idCuenta, idSector, idSubsector, idUnidad, usrAlta, fAlta, estatus)
							VALUES (:idCriterioAuxiliar, :idCuenta, :idSector, :idSubsector, :idUnidad, :usrAlta, GETDATE(), 'ACTIVO' )";
							$dbQuery = $db->prepare($sql);	

							$dbQuery->execute(array(':idCriterioAuxiliar'=> $idCriterioAuxiliar, ':idCuenta'=> $idCuenta, ':idSector'=> $idSector, ':idSubsector'=> $idSubsector, ':idUnidad'=> $idUnidad, ':usrAlta'=> $usrActual));	
						}
					}
				}
			}

		}catch (Exception $e) {

		  	print "ERROR: " . $e->getMessage() . "<br><br> QUERY: " . $sql ;
	  		die();
	 	}
		
		$app->redirect($app->urlFor('criteriosSeleccion'));
	});


	$app->get('/obtenerUnidadesByCriterioSel/:idCriterioAuxiliar', function($idCriterioAuxiliar) use($app, $db) {

		$dbQuery = $db->prepare("SELECT cu.idCriterioAuxiliar, cu.idSector, cu.idSubsector, cu.idUnidad, u.nombre nombre FROM sia_CriteriosUnidades cu INNER JOIN sia_unidades u ON cu.idCuenta = u.idCuenta AND cu.idSector = u.idSector AND cu.idUnidad = u.idUnidad WHERE cu.idCriterioAuxiliar = :idCriterioAuxiliar ORDER BY nombre;");  
		$dbQuery->execute();
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		} 
	});


	$app->get('/validaCriterioUnidad/:idCriterioAuxiliar/:cveUnidad', function($idCriterioAuxiliar, $cveUnidad) use($app, $db) {
		$cuenta = $_SESSION ["idCuentaActual"];

		$dbQuery = $db->prepare("SELECT count(*) nRegistros FROM sia_CriteriosUnidades WHERE idCriterioAuxiliar = :idCriterioAuxiliar  AND idSector + idSubsector + idUnidad = :cveUnidad AND idCuenta = :idCuenta; ");  

		$dbQuery->execute(array(':idCriterioAuxiliar'=> $idCriterioAuxiliar, ':cveUnidad'=> $cveUnidad, ':idCuenta'=> $cuenta));
		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);

		if($result['nRegistros'] > 0){
			echo("EXISTE");
		}else{
			echo("NOEXISTE");
		}
		/*
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		} 
		*/
	});

	$app->get('/listaUnidades/:idCriterioAuxiliar', function($idCriterioAuxiliar)    use($app, $db) {
		$cuenta = $_SESSION ["idCuentaActual"];
		
		$sql="SELECT ltrim(u.idSector+u.idSubsector+u.idUnidad) id, u.nombre texto FROM sia_unidades u LEFT OUTER JOIN sia_CriteriosUnidades cu ON u.idCuenta = u.idCuenta AND cu.idSector+cu.idSubsector+cu.idUnidad = u.idSector+u.idSubsector+u.idUnidad AND cu.idCriterioAuxiliar = :idCriterioAuxiliar WHERE u.idCuenta = :idCuenta AND cu.idSector+cu.idSubsector+cu.idUnidad is null ORDER BY nombre;";
		
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':idCriterioAuxiliar' => $idCriterioAuxiliar, ':idCuenta' => $cuenta));
		$result ['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});

	$app->get('/listaAllUnidades', function()    use($app, $db) {
		$cuenta = $_SESSION ["idCuentaActual"];
		
		$sql="SELECT ltrim(idSector+idSubsector+idUnidad) id, nombre texto FROM sia_unidades WHERE idCuenta = :idCuenta ORDER BY nombre;";
		
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':idCuenta' => $cuenta));
		$result ['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});

	$app->get('/obtenerUnidadesByCriterioSeleccion/:idCriterioAuxiliar', function($idCriterioAuxiliar)    use($app, $db) {
		$cuenta = $_SESSION ["idCuentaActual"];
		$cuenta1 = $_SESSION ["idCuentaActual"];
		
		//$sql="SELECT cu.idCriterioUnidad, cu.idCriterioAuxiliar, cu.idCuenta, ltrim(cu.idSector+cu.idSubsector+cu.idUnidad) cveUnidad, u.nombre FROM sia_CriteriosUnidades cu INNER JOIN sia_unidades u ON u.idCuenta = u.idCuenta AND cu.idSector+cu.idSubsector+cu.idUnidad = u.idSector+u.idSubsector+u.idUnidad AND cu.idCriterioAuxiliar = :idCriterioAuxiliar WHERE u.idCuenta = :idCuenta ORDER BY nombre;";

		$sql="SELECT CASE WHEN (a.relacionado IS NULL) THEN 'NO' ELSE a.relacionado END relacionado
		      , u.idSector + u.idSubsector + u.idUnidad cveUnidad, RTRIM(LTRIM(u.nombre)) nombreUnidad, a. idCriterioUnidad, a.idCriterioAuxiliar, u.idCuenta
			FROM sia_unidades u 
  			LEFT JOIN (SELECT 'SI' relacionado, idCriterioUnidad, idCriterioAuxiliar, idCuenta, ltrim(idSector+idSubsector+idUnidad) cveUnidad
            			FROM sia_CriteriosUnidades WHERE idCuenta = :idCuenta AND idCriterioAuxiliar = :idCriterioAuxiliar) a
						ON u.idCuenta = a.idCuenta AND u.idSector+u.idSubsector+u.idUnidad = a.cveUnidad
						WHERE u.idCuenta = :idCuenta1
			ORDER BY  relacionado desc, RTRIM(LTRIM(u.nombre)) asc;";
		
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':idCuenta' => $cuenta, ':idCriterioAuxiliar' => $idCriterioAuxiliar, ':idCuenta1' => $cuenta));
		$result ['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});


	$app->get('/guardar/criteriosUnidades/:valores', function($valores)  use($app, $db) {

		$cuenta = $_SESSION ["idCuentaActual"];
		$programa = $_SESSION ["idProgramaActual"];
		$usrActual = $_SESSION ["idUsuario"];

		$aValores = explode("|", $valores);

		$oper           		= $aValores [0];
		$idCriterioUnidad       = $aValores [1];
		$idCriterioAuxUnidad    = $aValores [2];
		$idSector               = $aValores [3];
		$idSubSector            = $aValores [4];
		$idUnidad    			= $aValores [5];
		$estatus 		    	= $aValores [6];

				
		//$request        		= $app->request;
		//$oper           		= $request->post('txtOperacionUnidad');
		//$idCriterioUnidad       = $request->post('txtIdCriterioUnidad');
		//$idCriterioAuxUnidad    = $request->post('txtIdCriterioAuxUnidad');
		//$idSector               = substr( $request->post('txtUnidad'), 0, 2 );
		//$idSubSector            = substr( $request->post('txtUnidad'), 2, 2 );
		//$idUnidad    			= substr( $request->post('txtUnidad'), 4, 2 );
		//$estatus 		    	= $request->post('txtEstatusUnidad');


		//echo( "oper=>" . $oper . " idCriterioUnidad=>" . $idCriterioUnidad  . " idCriterioAuxUnidad=>" . $idCriterioAuxUnidad . " cuenta=>" . $cuenta . " idSector=>" . $idSector . " idSubSector=>" . $idSubSector . " idUnidad=>" . $idUnidad . " usrActual=>" . $usrActual . " estatus=>" . $estatus);

		try{

			if($oper == 'INS'){

				$sql="INSERT INTO sia_criteriosUnidades (idCriterioAuxiliar, idCuenta, idSector, idSubSector, idUnidad, fAlta, usrAlta) VALUES (:idCriterioAuxUnidad, :cuenta, :idSector, :idSubSector, :idUnidad, getdate(), :usrAlta);";
										
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':idCriterioAuxUnidad'=>$idCriterioAuxUnidad, ':cuenta'=>$cuenta, ':idSector'=>$idSector, ':idSubSector'=>$idSubSector, ':idUnidad'=>$idUnidad, ':usrAlta'=>$usrActual) );
			}else{

				$sql="UPDATE sia_criteriosUnidades SET idCuenta=:idCuenta, idSector=:idSector, idSubSector=:idSubSector, idUnidad=:idUnidad, usrModificacion=:usrModificacion, fModificacion=getdate(), estatus=:estatus WHERE idCriterioUnidad=:idCriterioUnidad AND idCriterioAuxiliar=:idCriterioAuxiliar";

					//echo( "oper=>" . $oper . " idCriterio=>" . $idCriterio  . " idTipoCriterio=>" . $idTipoCriterio . " fuente=>" . $fuente . " fecInformacion=>" . $fecInformacion . " estatus=>" . $estatus . " cuenta=>" . $cuenta . " programa=>" . $programa . " usrActual=>" . $usrActual . " Sql=>*" . $sql . "*");
									
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':cuenta'=>$cuenta, ':idSector'=>$idSector, ':idSubSector'=>$idSubSector, ':idUnidad'=>$idUnidad, ':usrModificacion'=> $usrActual, ':estatus'=>$estatus, ':idCriterioUnidad'=>$idCriterioUnidad, ':idCriterioAuxUnidad'=>$idCriterioAuxUnidad ));
			}
           	echo("OK");

		}catch (Exception $e) {
		  	echo( "ERROR: " . $e->getMessage() . "<br><br> QUERY: " . $dbQuery ) ;
	  		die();
	 	}
		
		//$app->redirect($app->urlFor('criteriosSeleccion'));
	});


	$app->get('/eliminar/criterioUnidad/:idCriterioAuxUnidad/:idSector/:idSubsector/:idUnidad', function($idCriterioAuxUnidad, $idSector, $idSubsector, $idUnidad)  use($app, $db) {

		$cuenta = $_SESSION ["idCuentaActual"];
		$programa = $_SESSION ["idProgramaActual"];
		$usrActual = $_SESSION ["idUsuario"];

		try{

			$sql="DELETE sia_criteriosUnidades WHERE idCriterioAuxiliar=:idCriterioAuxUnidad AND idCuenta = :idCuenta AND idSector = :idSector AND idSubsector = :idSubsector AND idUnidad = :idUnidad";

			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':idCriterioAuxUnidad'=>$idCriterioAuxUnidad, ':idCuenta'=>$cuenta, ':idSector'=>$idSector, ':idSubsector'=>$idSubsector, ':idUnidad'=>$idUnidad));

           	echo("OK");

		}catch (Exception $e) {
		  	echo( "ERROR: " . $e->getMessage() . "<br><br> QUERY: " . $dbQuery ) ;
	  		die();
	 	}
		
	});

	$app->get('/obtenerDocumentosByCriterioSeleccion/:idCriterioAuxiliar', function($idCriterioAuxiliar)    use($app, $db) {
		$cuenta = $_SESSION ["idCuentaActual"];
		
		$sql="SELECT cd.idCriterioDocumento, cd.idCriterioAuxiliar, cd.archivoOriginal, cd.archivoFinal, CONVERT(VARCHAR(12), cd.falta,105) falta, cd.estatus FROM sia_CriteriosDocumentos cd WHERE cd.idCriterioAuxiliar = :idCriterioAuxiliar ORDER BY cd.falta;";
		
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':idCriterioAuxiliar' => $idCriterioAuxiliar));
		$result ['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});


	$app->get('/guardar/criteriosDocumentos/:valores', function($valores)  use($app, $db) {
		$usrActual = $_SESSION ["idUsuario"];
		$cuenta = $_SESSION ["idCuentaActual"];

		$aValores = explode("|", $valores);

		$oper           	 = $aValores [0];
		$idCriterioDocumento = $aValores [1];
		$idCriterioAuxiliar  = $aValores [2];
		$archivoOriginal     = $aValores [3];
		$archivoFinal        = $aValores [4];
		$estatus 		     = $aValores [5];

		try
		{
			if($oper=='INS')
			{
				$sql="INSERT INTO sia_criteriosDocumentos (idCriterioAuxiliar, archivoOriginal, archivoFinal, fAlta, usrAlta) VALUES (:idCriterioAuxiliar, :archivoOriginal, :archivoFinal, getdate(), :usrAlta);";
										
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':idCriterioAuxiliar'=>$idCriterioAuxiliar, ':archivoOriginal'=>$archivoOriginal, ':archivoFinal'=>$archivoFinal, ':usrAlta'=>$usrActual) );

	           	echo("OK");

			}else{

				$sql="UPDATE sia_criteriosDocumentos SET idCriterioAuxiliar=:idCriterioAuxiliar, archivoOriginal=:archivoOriginal, archivoFinal=:archivoFinal, usrModificacion=:usrModificacion, fModificacion=getdate(), estatus=:estatus WHERE idCriterioDocumento =:idCriterioDocumento";
				$dbQuery = $db->prepare($sql);

				$dbQuery->execute(array(':idCriterioAuxiliar'=>$idCriterioAuxiliar, ':archivoOriginal'=>$archivoOriginal, ':archivoFinal'=>$archivoFinal, ':usrModificacion'=>$usrActual, ':estatus'=>$estatus, ':idCriterioDocumento'=>$idCriterioDocumento ) );

	           	echo("OK");

			}

		}catch (Exception $e) {
			print "¡Error!: " . $e->getMessage() . "<br/>";
			die();
		}
	});

	$app->get('/eliminar/criterioDocumento/:idCriterioDocumento', function($idCriterioDocumento)  use($app, $db) {

		try{

			$sql="DELETE sia_criteriosDocumentos WHERE idCriterioDocumento=:idCriterioDocumento";

			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':idCriterioDocumento'=>$idCriterioDocumento));

           	echo("OK");

		}catch (Exception $e) {
		  	echo( "ERROR: " . $e->getMessage() . "<br><br> QUERY: " . $dbQuery ) ;
	  		die();
	 	}
		
	});

	// *********************  Rutinas para el Catálogo de Unidades **********************************

	$app->get('/catUnidades',  $autenticacionrole, function() use($app, $db) {
		//$cuenta = $_SESSION["idCuentaActual"];
		
		//if(!isset($_SESSION["idCuentaSeleccionada"])){ 	$_SESSION["idCuentaSeleccionada"]=$cuenta; }
		//$idCuentaSeleccionada = $_SESSION["idCuentaSeleccionada"];
		//$idCuentaSeleccionada = 'CTA-2015';
		$idCuentaSeleccionada = $_SESSION["idCuentaVariable"];
		
		$sql = "SELECT u.idCuenta, u.idSector, u.idSubsector, u.idUnidad, idSector + u.idSubsector + u.idUnidad centroGestor, u.nombre, isnull(u.siglas,'') siglas, isnull(u.titular,'') titular, isnull(u.estatus,'') estatus, u.idUnidadSector, isnull(us.nombre,'') unidadSector, u.idUnidadPoder, isnull(up.nombre,'') unidadPoder, u.idUnidadClasificacion, isnull(uc.nombre,'') unidadClasificacion
		FROM sia_unidades u 
  			LEFT JOIN sia_unidadesSectores us      ON u.idUnidadSector        = us.idUnidadSector
  			LEFT JOIN sia_unidadesPoderes up       ON u.idUnidadPoder         = up.idUnidadPoder
  			LEFT JOIN sia_UnidadesClasificacion uc ON u.idUnidadClasificacion = uc.idUnidadClasificacion 
		WHERE u.idCuenta = :idCuenta 
		ORDER BY u.idCuenta desc, u.idSector + u.idSubsector + u.idUnidad asc;" ;

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':idCuenta' => $idCuentaSeleccionada));

		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			$app->render('catUnidades.php', $result);
		} 
	})->name('catUnidades');

	
	$app->get('/catEntidades/:ctaSeleccionada', function($ctaSeleccionada) use($app, $db) {

		$sql = "SELECT u.idCuenta, u.idSector, u.idSubsector, u.idUnidad, idSector + u.idSubsector + u.idUnidad centroGestor, u.nombre, isnull(u.siglas,'') siglas, isnull(u.titular,'') titular, isnull(u.estatus,'') estatus, u.idUnidadSector, isnull(us.nombre,'') unidadSector, u.idUnidadPoder, isnull(up.nombre,'') unidadPoder, u.idUnidadClasificacion, isnull(uc.nombre,'') unidadClasificacion
		FROM sia_unidades u 
  			LEFT JOIN sia_unidadesSectores us      ON u.idUnidadSector        = us.idUnidadSector
  			LEFT JOIN sia_unidadesPoderes up       ON u.idUnidadPoder         = up.idUnidadPoder
  			LEFT JOIN sia_UnidadesClasificacion uc ON u.idUnidadClasificacion = uc.idUnidadClasificacion 
		WHERE u.idCuenta = :idCuenta 
		ORDER BY u.idCuenta desc, u.idSector + u.idSubsector + u.idUnidad asc;" ;

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':idCuenta' => $ctaSeleccionada));

		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		} 
	});
	

	$app->get('/actualizaVarSession/:cuentaSeleccionada', function($cuentaSeleccionada) use($app) {
 		$_SESSION["idCuentaVariable"]=$cuentaSeleccionada;
 		echo ($_SESSION["idCuentaVariable"]);
		//$app->redirect($app->urlFor('catUnidades'));
	});
	
	//$app->get('/catUnidades2', function () use ($app) { $app->redirect($app->urlFor('catUnidades',[,,,,]) );  });
	

	$app->get('/sectoresByCta/:idCuenta', function($idCuenta)    use($app, $db) {

		$sql="SELECT ltrim(idSector) id, ltrim(idSector) + '-' + ltrim(nombre) texto FROM sia_sectores Where idCuenta=:idCuenta ORDER BY nombre";
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':idCuenta' => $idCuenta));
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});

	//Lista de SUB-sectores
	$app->get('/subSectoresByCtaYsector/:idCuenta/:idSector', function($idCuenta, $idSector)    use($app, $db) {

		//echo '$idCuenta=>' .  $idCuenta . '  $idSector=>' . $idSector;

		$sql="SELECT ltrim(idSubsector) id, ltrim(idSector) + ltrim(idSubsector) + '-' + ltrim(nombre) texto FROM sia_subsectores Where idCuenta=:idCuenta and idSector=:idSector ORDER BY nombre";
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':idCuenta' => $idCuenta, ':idSector' => $idSector));
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

		//echo $result;

		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});

	
	//Lista de unidades by
	$app->get('/obtenerUnidadByCentroGestor/:idCuenta/:idSector/:idSubsector/:idUnidad', function($idCuenta, $idSector, $idSubsector, $idUnidad)    use($app, $db) {
		

		$sql="SELECT idCuenta, idSector, idSubsector, idUnidad, isnull(nombre,'') nombre, isnull(siglas,'') siglas, isnull(titular,'') titular, isnull(estatus,'') estatus, idUnidadSector, idUnidadPoder, idUnidadClasificacion, siglasJuridico, idClasificacionJuridico FROM sia_unidades WHERE idCuenta=:idCuenta AND idSector=:idSector AND idSubsector=:idSubsector AND idUnidad=:idUnidad ORDER BY nombre;";
		
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':idCuenta' => $idCuenta, ':idSector' => $idSector, ':idSubsector' => $idSubsector, ':idUnidad' => $idUnidad));
		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});


	$app->get('/obtenerTotalUnidadByCentroGestor/:idCuenta/:idSector/:idSubsector/:idUnidad', function($idCuenta, $idSector, $idSubsector, $idUnidad)    use($app, $db) {
		
		$sql="SELECT count(*) total FROM sia_unidades WHERE idCuenta=:idCuenta AND idSector=:idSector AND idSubsector=:idSubsector AND idUnidad=:idUnidad;";
		
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':idCuenta' => $idCuenta, ':idSector' => $idSector, ':idSubsector' => $idSubsector, ':idUnidad' => $idUnidad));
		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});

	$app->get('/lstUnidadesSectores', function()    use($app, $db) {
		
		$sql="SELECT idUnidadSector id, nombre texto FROM sia_unidadesSectores ORDER BY nombre;";
		
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute();
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});

	$app->get('/poderesBySector/:idUnidadSector', function($idUnidadSector)    use($app, $db) {
		
		$sql="SELECT idUnidadPoder id, nombre texto FROM sia_unidadesPoderes WHERE idUnidadSector=:idUnidadSector;";
		
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':idUnidadSector' => $idUnidadSector));
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});

	$app->get('/clasificacionesByPoder/:idUnidadPoder', function($idUnidadPoder)    use($app, $db) {
		
		$sql="SELECT idUnidadClasificacion id, nombre texto FROM sia_unidadesClasificacion WHERE idUnidadPoder=:idUnidadPoder;";
		
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':idUnidadPoder' => $idUnidadPoder));
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});

	//Registrar la unidad a la BD

	$app->post('/guardar/unidad', function()  use($app, $db) {
		$usrActual = $_SESSION ["idUsuario"];

		$request=$app->request;

		$oper        = $request->post('txtOperacion');
		$idCuenta    = $request->post('txtIdCuenta');
		$idSector    = $request->post('txtIdSector');
		$idSubsector = $request->post('txtIdSubsector');
		$idUnidad    = strtoupper($request->post('txtIdUnidad'));
		$nombre      = strtoupper($request->post('txtNombre'));
		$siglas		 = strtoupper($request->post('txtSiglas'));
		$titular     = strtoupper($request->post('txtTitular'));

		$idUnidadSector        = $request->post('txtIdUnidadSector');
		$idUnidadPoder         = $request->post('txtIdUnidadPoder');
		$idUnidadClasificacion = $request->post('txtIdUnidadClasificacion');

		$siglasJuridico        = strtoupper($request->post('txtJuridicoSiglas'));
		$juridicoClasif        = $request->post('txtJuridicoClasif');

		$estatus = $request->post('txtEstatus');

		if(is_null($idUnidadSector))       { $idUnidadSector=0;         }elseif(strlen($idUnidadSector)==0        ){ $idUnidadSector=0;       }
		if(is_null($idUnidadPoder))        { $idUnidadPoder=0;          }elseif(strlen($idUnidadPoder)==0         ){ $idUnidadPoder=0;        }
		if(is_null($idUnidadClasificacion)){ $idUnidadClasificacion = 0;}elseif(strlen($idUnidadClasificacion)== 0){ $idUnidadClasificacion=0;}

		//$tipoValor = gettype($idUnidadClasificacion);
		//$longitud = strlen($idUnidadClasificacion);

		//$datos = "<br>$oper <br>$idCuenta <br>$idSector <br>$idSubsector <br>$idUnidad <br>$nombre <br>$siglas <br><br>$titular <br>$idUnidadSector <br>$idUnidadPoder <br>$idUnidadClasificacion" ;

		//$datos = "<br>$oper <br>$idCuenta <br>$idSector <br>$idSector <br>$idSubsector <br>$idUnidad <br>$idUnidad <br>$nombre <br>$titular <br>$idUnidadSector <br>$idUnidadPoder <br>$idUnidadClasificacion <br>$tipoValor <br>$longitud" ;

		//$datos = " <br>$nombre <br>$titular <br>$usrActual <br>$estatus <br>$idUnidadSector <br>$idUnidadPoder <br>$idUnidadClasificacion <br>$siglas <br>$siglasJuridico <br>$juridicoClasif <br>$idCuenta <br>$idSector <br>$idSubsector <br>$idUnidad ";

		//echo $datos;

		try
		{
			if($oper=='INS')
			{
				$sql="INSERT INTO sia_unidades (idCuenta, idSector, idSubsector, idUnidad, nombre, titular, usrAlta, fAlta, estatus, idUnidadSector, idUnidadPoder, idUnidadClasificacion, siglas, siglasJuridico, idClasificacionJuridico) " .
				"VALUES(:idCuenta, :idSector, :idSubsector, :idUnidad, :nombre, :titular, :usrAlta, getdate(), 'ACTIVO', :idUnidadSector, :idUnidadPoder, :idUnidadClasificacion, :siglas, :siglasJuridico, :juridicoClasif);";
				$dbQuery = $db->prepare($sql);

				$dbQuery->execute(array(':idCuenta'=>$idCuenta, ':idSector'=>$idSector, ':idSubsector'=>$idSubsector, ':idUnidad'=>$idUnidad, ':nombre'=>$nombre, ':titular'=>$titular, ':usrAlta'=>$usrActual, ':idUnidadSector'=>$idUnidadSector, ':idUnidadPoder'=>$idUnidadPoder, ':idUnidadClasificacion'=>$idUnidadClasificacion, ':siglas'=>$siglas, ':siglasJuridico'=>$siglasJuridico, ':juridicoClasif'=>$juridicoClasif ));
				//echo "<br>INS OK<hr>";
			}else{

				$sql="UPDATE sia_unidades SET nombre=:nombre, titular=:titular, usrModificacion=:usrModificacion, fModificacion=getdate(), estatus=:estatus, idUnidadSector=:idUnidadSector, idUnidadPoder=:idUnidadPoder, idUnidadClasificacion=:idUnidadClasificacion, siglas=:siglas, siglasJuridico=:siglasJuridico, idClasificacionJuridico=:juridicoClasif WHERE idCuenta=:idCuenta AND idSector=:idSector AND idSubsector=:idSubsector AND idUnidad=:idUnidad ";
				$dbQuery = $db->prepare($sql);

				$dbQuery->execute(array(':nombre'=>$nombre, ':titular'=>$titular, ':usrModificacion'=>$usrActual, ':estatus'=>$estatus, ':idUnidadSector'=>$idUnidadSector, ':idUnidadPoder'=>$idUnidadPoder, ':idUnidadClasificacion'=>$idUnidadClasificacion, ':siglas'=>$siglas, ':siglasJuridico'=>$siglasJuridico, ':juridicoClasif'=>$juridicoClasif, ':idCuenta'=>$idCuenta, ':idSector'=>$idSector, ':idSubsector'=>$idSubsector, ':idUnidad'=>$idUnidad ));
				//echo "<br>UPD OK";
			}

			//echo nl2br("\nQuery Ejecutado : ".$sql);

		}catch (DBException $e) {
			echo "¡Error!: " . $e->getMessage() . "<br/>";
			die();
		}
		$app->redirect($app->urlFor('catUnidades'));
	});

	// **************************** Rutinas para la CONSULTA del PGA ********************************************

		$app->get('/consultapga',  $autenticacionrole, function()  use($app, $db) {
		$cuenta = $_SESSION["idCuentaActual"];
		$area = $_SESSION["idArea"];

		$aDatos = array(':cuenta' => $cuenta);

		$usrActual = $_SESSION["idUsuario"];
		$global = $_SESSION["usrGlobal"];
		//$globalArea = $_SESSION["usrGlobalArea"];		
		if ($area == 'CAAAF' || $global ) {

			$sql="SELECT a.idAuditoria auditoria, COALESCE(convert(varchar(20),a.clave),convert(varchar(20),a.idAuditoria)) claveAuditoria, isnull(a.clave,'') clave, ar.nombre area, dbo.lstSujetosByAuditoria(a.idAuditoria) sujeto, dbo.lstObjetosByAuditoria(a.idAuditoria) objeto, ta.nombre tipo, '0.00' avances FROM sia_programas p INNER JOIN sia_auditorias a on p.idCuenta=a.idCuenta and p.idPrograma=a.idPrograma INNER JOIN sia_areas ar on a.idArea=ar.idArea LEFT JOIN sia_tiposauditoria ta on a.tipoAuditoria= ta.idTipoAuditoria WHERE a.idCuenta=:cuenta ORDER BY a.idAuditoria desc";
		}else{

			$sql="SELECT a.idAuditoria auditoria, COALESCE(convert(varchar(20),a.clave),convert(varchar(20),a.idAuditoria)) claveAuditoria, isnull(a.clave,'') clave, ar.nombre area, dbo.lstSujetosByAuditoria(a.idAuditoria) sujeto, dbo.lstObjetosByAuditoria(a.idAuditoria) objeto, ta.nombre tipo, '0.00' avances FROM sia_programas p INNER JOIN sia_auditorias a on p.idCuenta=a.idCuenta and p.idPrograma=a.idPrograma 
	 			INNER JOIN sia_areas ar on a.idArea=ar.idArea  LEFT JOIN sia_tiposauditoria ta on a.tipoAuditoria= ta.idTipoAuditoria WHERE a.idCuenta=:cuenta AND a.idArea in (SELECT idArea from sia_areas where idAreaSuperior = :area) ORDER BY a.idAuditoria desc";
 			
 			$aDatos[':area'] = $area;
		}
			
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute($aDatos);

		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS.");
		}else{
			$app->render('consultapga.php', $result);
		}
	})->name('consultapga');

	$app->get('/lstAuditoriaByID_2/:id', function($id)    use($app, $db) {
		$cuenta = $_SESSION["idCuentaActual"];
		$area = $_SESSION["idArea"];
		
		$usrActual = $_SESSION["idUsuario"];
		$global = $_SESSION["usrGlobal"];

		$aValores = array(':cuenta' => $cuenta, ':id' => $id);

		$sql="SELECT a.idCuenta cuenta, a.idPrograma programa, a.idAuditoria auditoria,  COALESCE(convert(varchar(20),a.clave),convert(varchar(20),a.idAuditoria)) claveAuditoria, isnull(a.clave,'') clave, a.tipoAuditoria tipo, a.idArea area, a.idSector sector, a.idSubsector subSector, a.idUnidad unidad, a.idObjeto objeto, a.objetivo, a.alcance, a.justificacion, a.tipoPresupuesto, a.acompanamiento, a.idProceso proceso, a.idEtapa etapa, REPLACE(ISNULL(CONVERT(VARCHAR(10),a.fInicio,105), ''), '1900-01-01', '') feInicio, REPLACE(ISNULL(CONVERT(VARCHAR(10),a.fFin,105), ''), '1900-01-01', '') feFin, a.latitud, a.longitud, a.tipoObservacion tipoObse, a.observacion,a.idResponsable responsable, a.idSubresponsable subresponsable, REPLACE(ISNULL(CONVERT(VARCHAR(10),a.fConfronta,105), ''), '1900-01-01', '') fcon, isnull(a.resumenConfronta,'') rconfron, REPLACE(ISNULL(CONVERT(VARCHAR(10),a.fIRA,105), ''), '1900-01-01', '') ira, REPLACE(ISNULL(CONVERT(VARCHAR(10),a.fIFA,105), ''), '1900-01-01', '') ifa
			FROM sia_auditorias a LEFT JOIN  sia_objetos o ON a.idObjeto = o.idObjeto WHERE  a.idCuenta = :cuenta AND a.idAuditoria = :id ";

		if ( $area == 'UTSFFA' || $area == 'UTSFEAJ' ) {
			$sql = $sql . " AND a.idArea in (SELECT idArea from sia_areas WHERE idAreaSuperior = :area) "; 
			$aValores[':area'] = $area;
		}

		$sql = $sql . " ORDER BY a.idAuditoria desc";

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute( $aValores );

		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});

	$app->get('/dpsTodasAuditoriasByCta', function()  use($app, $db) {
		
		$cuenta = $_SESSION["idCuentaActual"];
		
		$sql="SELECT ta.nombre texto, count(*) valor " . 
		"FROM sia_auditorias a INNER JOIN sia_tiposauditoria ta  ON a.tipoAuditoria=ta.idTipoAuditoria " . 
		"WHERE a.idCuenta=:cuenta AND a.clave is null " .
		"GROUP BY ta.nombre ORDER BY ta.nombre";
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':cuenta' => $cuenta));				
		
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		echo json_encode($result);
	});		

	$app->get('/dpsProyectosByArea', function()  use($app, $db) {
		
		$cuenta     = $_SESSION["idCuentaActual"];
		$area       = $_SESSION["idArea"];
		$empleado   = $_SESSION["idEmpleado"];
		$usrActual  = $_SESSION["idUsuario"];
		$global     = $_SESSION["usrGlobal"];
		$globalArea = $_SESSION["usrGlobalArea"];		

		$aDatos     = array(':cuenta' => $cuenta, ':usrActual' => $usrActual);

		$sql = listaProyectosAditoria($global, $globalArea, $area, "SI");


		if ($global=="SI"){
		}else{
			if ($globalArea=="SI"){
				if ( $area != 'CAAAF') { $aDatos[':area'] = $area; 	}
			}else{
				$aDatos[':usrActual2'] = $usrActual;  // Línea nueva.
			}
		}

		
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute($aDatos);		

		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS.");
		}else{
			echo json_encode($result);
		}
		

	});		


	function listaProyectosAditoria($global, $gobalArea, $area, $acumulado){

		if ($acumulado=="SI"){
			$campos = " SELECT ta.nombre texto, count(*) valor FROM sia_programas p ";
			$ordenamiento = "";
			$agrupamiento = " GROUP BY ta.nombre ORDER BY ta.nombre";
		}else{
			$campos = "SELECT a.idAuditoria auditoria, e.nombre etapa, COALESCE(convert(varchar(20),a.clave),convert(varchar(20),a.idAuditoria)) claveAuditoria, isnull(a.clave,'') clave, ar.nombre area, dbo.lstSujetosByAuditoria(a.idAuditoria) sujeto, dbo.lstObjetosByAuditoria(a.idAuditoria) objeto, ta.nombre tipo, '0.00' avances ";
			$ordenamiento = " ORDER BY a.idAuditoria desc";
			$agrupamiento = "";
		}

		$relaciones =	" FROM sia_programas p " .
  						" INNER JOIN sia_auditorias a on p.idCuenta=a.idCuenta and p.idPrograma=a.idPrograma " .
						" INNER JOIN sia_areas ar on a.idArea=ar.idArea " .
						" LEFT  JOIN sia_tiposauditoria ta on a.tipoAuditoria= ta.idTipoAuditoria " .
						" LEFT  JOIN sia_etapas e on e.idProceso =a.idProceso and e.idEtapa=a.idEtapa " .
						" WHERE a.idCuenta=:cuenta ";

		if ($global=="SI"){
			$relaciones = $relaciones . " AND a.idEtapa in (Select idEtapa from sia_rolesetapas re inner join sia_usuariosroles ur on ur.idRol = re.idRol Where ur.idUsuario=:usrActual) ";		
		}else{
			if ($globalArea=="SI"){
				$relaciones = $relaciones . " AND a.idEtapa in (Select idEtapa from sia_rolesetapas re inner join sia_usuariosroles ur on ur.idRol = re.idRol Where ur.idUsuario=:usrActual) ";		

				if ( $area == 'UTSFFA' || $area == 'UTSFEAJ' ) {
					$relaciones = $relaciones . " AND a.idArea in (SELECT idArea from sia_areas where idAreaSuperior = :area) ";
				}elseif ( $area != 'CAAAF') {
					$relaciones = $relaciones . " AND a.idArea=:area ";  
				}
				$relaciones = $relaciones . " AND a.clave is null ";

			}else{

				$relaciones = $relaciones . " AND a.idEtapa in (Select idEtapa from sia_rolesetapas re inner join sia_usuariosroles ur on ur.idRol = re.idRol Where ur.idUsuario=:usrActual) ";		
				$relaciones = $relaciones . " AND a.usrAlta=:usrActual2 ";
				$relaciones = $relaciones . " AND a.clave is null ";
			}
		}
		return( $campos . $relaciones . $ordenamiento . $agrupamiento );
	};


	$app->get('/dpsProyectosByAreaAmplio', function()  use($app, $db) {
		$cuenta     = $_SESSION["idCuentaActual"];
		$area       = $_SESSION["idArea"];
		$empleado   = $_SESSION["idEmpleado"];
		$usrActual  = $_SESSION["idUsuario"];
		$global     = $_SESSION["usrGlobal"];
		$globalArea = $_SESSION["usrGlobalArea"];		

		$aDatos     = array(':cuenta' => $cuenta, ':usrActual' => $usrActual);

		if ($global=="SI"){

			$sql="SELECT ta.nombre texto, count(*) valor FROM sia_programas p " .
  			" INNER JOIN sia_auditorias a on p.idCuenta=a.idCuenta and p.idPrograma=a.idPrograma " .
			" INNER JOIN sia_areas ar on a.idArea=ar.idArea " .
			" LEFT  JOIN sia_tiposauditoria ta on a.tipoAuditoria= ta.idTipoAuditoria " .
			" LEFT  JOIN sia_etapas e on e.idProceso =a.idProceso and e.idEtapa=a.idEtapa " .
			" WHERE a.idCuenta=:cuenta AND a.idEtapa in (Select idEtapa from sia_rolesetapas re inner join sia_usuariosroles ur on ur.idRol = re.idRol Where ur.idUsuario=:usrActual) ".		
			"  GROUP BY ta.nombre ORDER BY ta.nombre";
				
		}else{

			if ($globalArea=="SI"){

				$sql="SELECT ta.nombre texto, count(*) valor FROM sia_programas p " .
	  			" INNER JOIN sia_auditorias a on p.idCuenta=a.idCuenta and p.idPrograma=a.idPrograma " .
				" INNER JOIN sia_areas ar on a.idArea=ar.idArea " .
				" LEFT  JOIN sia_tiposauditoria ta on a.tipoAuditoria= ta.idTipoAuditoria " .
				" LEFT  JOIN sia_etapas e on e.idProceso =a.idProceso and e.idEtapa=a.idEtapa" .
				" WHERE a.idCuenta=:cuenta AND a.idEtapa in (Select idEtapa from sia_rolesetapas re inner join sia_usuariosroles ur on ur.idRol = re.idRol Where ur.idUsuario=:usrActual) " ;

				if ( $area == 'UTSFFA' || $area == 'UTSFEAJ' ) {
					$sql = $sql . " AND a.idArea in (SELECT idArea from sia_areas where idAreaSuperior = :area) ";
					$aDatos[':area'] = $area;
				}elseif ( $area == 'CAAAF') {
				} else {
					$sql = $sql . " AND a.idArea=:area ";  
					$aDatos[':area'] = $area;
				}

				$sql = $sql . " AND a.clave is null ";
				$sql = $sql . "  GROUP BY ta.nombre ORDER BY ta.nombre";
					

			}else{

				$sql="SELECT ta.nombre texto, count(*) valor FROM sia_programas p " .
	  			" INNER JOIN sia_auditorias a on p.idCuenta=a.idCuenta and p.idPrograma=a.idPrograma " .
				" INNER JOIN sia_areas ar on a.idArea=ar.idArea " .
		        //" INNER JOIN sia_auditoriasauditores aa ON a.idCuenta=aa.idCuenta and a.idAuditoria=aa.idAuditoria " . // Se bloquea por que en PGA, aún no se tiene la relación de auditorias-auditores
				" LEFT  JOIN sia_etapas e on e.idProceso =a.idProceso and e.idEtapa=a.idEtapa" .
				" LEFT  JOIN sia_tiposauditoria ta on a.tipoAuditoria= ta.idTipoAuditoria " .
				" WHERE a.idCuenta=:cuenta AND a.idEtapa in (Select idEtapa from sia_rolesetapas re inner join sia_usuariosroles ur on ur.idRol = re.idRol  Where ur.idUsuario=:usrActual) " .
				//" AND aa.idAuditor=:auditor " . //Línea anterior.
				" AND a.usrAlta=:usrActual2 " .  // Línea nueva. 
				" AND a.clave is null " .
				" GROUP BY ta.nombre ORDER BY ta.nombre";
					
				//$aDatos[':auditor'] = $empleado;  // Línea anterior.
				$aDatos[':usrActual2'] = $usrActual;  // Línea nueva.

			}
		}

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute($aDatos);		

		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS.");
		}else{
			echo json_encode($result);
		}
	});

	$app->get('/lstAreasResponsablesConsulta', function()  use($app, $db) {
		$area = $_SESSION["idArea"];
		$global = $_SESSION["usrGlobal"];
		
		$sql="SELECT idResponsable id, nombre texto FROM sia_areasresponsables ORDER BY nombre asc";
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute();		

		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		echo json_encode($result);
	});	

	$app->get('/lstAreasResponsablesByArea', function()  use($app, $db) {
		$area = $_SESSION["idArea"];
		$global = $_SESSION["usrGlobal"];
		
		$sql="SELECT idResponsable id, nombre texto FROM sia_areasresponsables WHERE idArea = :area ORDER BY nombre asc";
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':area' => $area));		

		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		echo json_encode($result);
	});	

	$app->get('/recuperaRubrosEgresoByAuditoria/:idAuditoria', function($idAuditoria)  use($app, $db) {

		$cuenta = $_SESSION ["idCuentaActual"];
		$programa = $_SESSION ["idProgramaActual"];

		$sql = "SELECT idObjeto, nombre, nivel, valor, rubros FROM sia_objetos WHERE tipoObjeto = 'EGRESO' AND idCuenta = :idCuenta AND  idPrograma = :idPrograma AND idAuditoria = :idAuditoria ";
 		
		$dbQuery = $db->prepare($sql);
		
		$dbQuery->execute(array(':idCuenta' => $cuenta, ':idPrograma' => $programa, ':idAuditoria' => $idAuditoria));
	
		$result ['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON OBJETOS DE FISCALIZACIÓN. ");
		}else{
			echo json_encode($result);
		}
		
	});


	$app->get('/recuperaGastoByAuditoria/:aUnidad/:aRubro', function($aUnidad, $aRubro)  use($app, $db) {

		$cuenta = $_SESSION ["idCuentaActual"];
		$programa = $_SESSION ["idProgramaActual"];

		$idCuentaAnt = "CTA-" . (string)((SUBSTR($cuenta, -4)) - 1);

		$aValoresUnidad = explode("|", $aUnidad);		
		$sector         = $aValoresUnidad[0];
		$subSector      = $aValoresUnidad[1];
		$unidad         = $aValoresUnidad[2];

		$aValoresRubro = explode("|", $aRubro);		
		$objeto        = $aValoresRubro[0];
		$nivel         = $aValoresRubro[1];
		$finalidad     = $aValoresRubro[2];
		$funcion       = $aValoresRubro[3];
		$subFuncion    = $aValoresRubro[4];
		$actividad     = $aValoresRubro[5];
		$capitulo      = $aValoresRubro[6];
		$partida       = $aValoresRubro[7];

		$sql = "Vacio";

		try{

	    	$campos = array(':idCuenta' => $cuenta, ':idCuentaAnt' => $idCuentaAnt, ':sector' => $sector, ':subSector' => $subSector, ':unidad' => $unidad);

			$sql = "SELECT cd.idCuenta cuenta, u.nombre ente, x.nombre rubro, format(sum(CONVERT(DECIMAL(20,2),cd.original)), '$#,##0.00;($#,##0.00)') original, format(sum(CONVERT(DECIMAL(20,2),cd.modificado)), '$#,##0.00;($#,##0.00)') modificado, format(sum(CONVERT(DECIMAL(20,2),cd.ejercido)), '$#,##0.00;($#,##0.00)') ejercido, format(sum(CONVERT(DECIMAL(20,2),cd.pagado)), '$#,##0.00;($#,##0.00)') pagado 
					FROM sia_cuentasdetalles cd 
					LEFT JOIN sia_unidades u ON u.idCuenta = cd.idCuenta and u.idSector = cd.sector and u.idSubsector = cd.subsector and u.idUnidad = cd.unidad "; 

			switch ($nivel) {
			    case 'FUNCIÓN':
					$sql = $sql . " LEFT JOIN sia_funciones x ON x.idCuenta = cd.idCuenta and x.idFinalidad = cd.Finalidad and x.idFuncion = cd.Funcion
						WHERE (cd.idCuenta = :idCuenta OR cd.idCuenta = :idCuentaAnt) AND cd.Sector = :sector AND cd.Subsector = :subSector AND cd.Unidad = :unidad ";

						// AND cd.Finalidad = :finalidad AND cd.Funcion = :funcion
						//GROUP BY cd.idCuenta, cd.sector, cd.subsector, cd.unidad, u.nombre, x.nombre";
			        break;

			    case 'SUBFUNCIÓN':
					$sql = $sql . " LEFT JOIN sia_subfunciones x ON x.idCuenta = cd.idCuenta and x.idFinalidad = cd.Finalidad and x.idFuncion = cd.Funcion and x.idSubfuncion = cd.Subfuncion 
						WHERE (cd.idCuenta = :idCuenta OR cd.idCuenta = :idCuentaAnt) AND cd.Sector = :sector AND cd.Subsector = :subSector AND cd.Unidad = :unidad ";
						// AND cd.Finalidad = :finalidad AND cd.Funcion = :funcion AND cd.Subfuncion = :subfuncion
					    //GROUP BY cd.idCuenta, cd.sector, cd.subsector, cd.unidad, u.nombre, x.nombre";
			        break;

			    case 'ACTIVIDAD':
					$sql = $sql . " LEFT JOIN sia_actividades x ON x.idCuenta = cd.idCuenta and x.idFinalidad = cd.Finalidad and x.idFuncion = cd.Funcion and x.idSubfuncion = cd.Subfuncion and x.idActividad = cd.Actividad 
						WHERE (cd.idCuenta = :idCuenta OR cd.idCuenta = :idCuentaAnt) AND cd.Sector = :sector AND cd.Subsector = :subSector AND cd.Unidad = :unidad ";
						//AND cd.Finalidad = :finalidad AND cd.Funcion = :funcion AND cd.Subfuncion = :subfuncion  AND cd.actividad = :actividad 
						//GROUP BY cd.idCuenta, cd.sector, cd.subsector, cd.unidad, u.nombre, x.nombre"; 
			        break;

			    case 'CAPITULO':
					$sql = $sql . " LEFT JOIN sia_capitulos x ON x.idCuenta = cd.idCuenta and x.idCapitulo = cd.Capitulo 
						WHERE (cd.idCuenta = :idCuenta OR cd.idCuenta = :idCuentaAnt) AND cd.Sector = :sector AND cd.Subsector = :subSector AND cd.Unidad = :unidad ";
			        break;

			    case 'PARTIDA':
					$sql = $sql . " LEFT JOIN sia_partidas x ON x.idCuenta = cd.idCuenta and x.idCapitulo = cd.Capitulo and x.idPartida = cd.Partida 
						WHERE (cd.idCuenta = :idCuenta OR cd.idCuenta = :idCuentaAnt) AND cd.Sector = :sector AND cd.Subsector = :subSector AND cd.Unidad = :unidad ";
			        break;

			}

			if ( $finalidad  != 'NULL' ) { $sql = $sql . " AND cd.Finalidad  = :finalidad ";  $campos[':finalidad']  = $finalidad; } 
			if ( $funcion    != 'NULL' ) { $sql = $sql . " AND cd.funcion    = :funcion ";    $campos[':funcion']    = $funcion; 	}
			if ( $subFuncion != 'NULL' ) { $sql = $sql . " AND cd.subFuncion = :subFuncion "; $campos[':subFuncion'] = $subFuncion; }
			if ( $actividad  != 'NULL' ) { $sql = $sql . " AND cd.actividad  = :actividad ";  $campos[':actividad']  = $actividad;  }
			if ( $capitulo   != 'NULL' ) { $sql = $sql . " AND cd.capitulo   = :capitulo ";   $campos[':capitulo']   = $capitulo;  }
			if ( $partida    != 'NULL' ) { $sql = $sql . " AND cd.partida    = :partida ";    $campos[':partida']    = $partida;  }

			$sql = $sql . " GROUP BY cd.idCuenta, cd.sector, cd.subsector, cd.unidad, u.nombre, x.nombre ";
			$sql = $sql . " ORDER BY cd.idCuenta DESC;";

			//****
			//$sql1 = "INSERT INTO sia_temporal (idAuditoria, datos) VALUES (:auditoria, :datos) ";
			//$dbQuery = $db->prepare($sql1);
			//$dbQuery->execute( array(':auditoria' => $nivel, ':datos' => $sql ));
			//****

			$dbQuery = $db->prepare($sql);
			$dbQuery->execute( $campos );
			//$result = $dbQuery->fetch(PDO::FETCH_ASSOC);
			$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
			
			//$result = array('sql' => $sql);

			if(!$result){
				$app->halt(404, "NO SE ENCONTRARON OBJETOS DE FISCALIZACIÓN.");
			}else{
				echo json_encode($result);
			}
		
		}catch (PDOException $e) {
			print "¡Error!: " . $e->getMessage() . "<br/>";
			die();
		}
	});		


	$app->get('/recuperaGastosByUnidad/:sector/:subsector/:unidad', function($sector, $subsector, $unidad)  use($app, $db) {

		$cuenta = $_SESSION ["idCuentaActual"];
		$programa = $_SESSION ["idProgramaActual"];

		$cuentaAnterior = "CTA-" . (string)((SUBSTR($cuenta, -4)) - 1);

		$sql = "Vacio";

		try{

	    	$campos = array(':cuenta' => $cuenta, ':cuentaAnterior' => $cuentaAnterior, ':sector' => $sector, ':subsector' => $subsector, ':unidad' => $unidad);

	    	/* Queery sin agupar
			$sql = "SELECT cp.idCuenta, cp.idCuentaDetalle, cp.finalidad, ISNULL(fin.nombre,'') nom_finalidad, cp.funcion, ISNULL(fun.nombre,'') nom_funcion, cp.subfuncion, ISNULL(sfun.nombre,'') nom_subfuncion, cp.actividad, ISNULL(act.nombre,'') nom_actividad, cp.capitulo
				, ISNULL(cap.nombre,'') nom_capitulo, cp.partida, par.nombre nom_partida
  				, FORMAT(cp.original,'$#,##0.00;($#,##0.00)') original, FORMAT(cp.modificado,'$#,##0.00;($#,##0.00)') modificado
  				, FORMAT(cp.ejercido,'$#,##0.00;($#,##0.00)') ejercido, FORMAT(cp.pagado,'$#,##0.00;($#,##0.00)') pagado
  				, FORMAT(cp.pendiente,'$#,##0.00;($#,##0.00)') pendiente
  				, CASE (cp.original + cp.ejercido)
        			WHEN cp.original THEN FORMAT(0,'##0.00;(##0.00)')
        			WHEN cp.ejercido THEN FORMAT ( ((cp.ejercido*100)/cp.modificado),'##0.00;(##0.00)' )
        			ELSE FORMAT( ((cp.ejercido*100)/cp.original), '##0.00;(##0.00)')
    			END AS Diferencia_Porcentual
			FROM (
					SELECT cd.idCuenta, cd.idCuentaDetalle, cd.funcion, cd.subfuncion, cd.actividad, cd.capitulo, cd.partida, cd.finalidad
  						, CONVERT(decimal(18,2), cd.original) original, CONVERT(decimal(18,2), cd.modificado) modificado, CONVERT(decimal(18,2), cd.ejercido) ejercido, CONVERT(decimal(18,2), cd.pagado) pagado, CONVERT(decimal(18,2), cd.pendiente) pendiente
					FROM sia_cuentasdetalles cd WHERE cd.idcuenta in (:cuenta, :cuentaAnterior) AND cd.sector = :sector AND cd.subsector = :subsector AND cd.unidad = :unidad) cp 
					LEFT JOIN sia_finalidades fin ON fin.idCuenta = cp.idCuenta AND fin.idFinalidad = cp.finalidad
					LEFT JOIN sia_funciones fun ON fun.idCuenta = cp.idCuenta AND fun.idFinalidad = cp.finalidad AND fun.idFuncion = cp.funcion
					LEFT JOIN sia_subFunciones sfun ON sfun.idCuenta = cp.idCuenta AND sfun.idFinalidad = cp.finalidad AND sfun.idFuncion = cp.funcion AND sfun.idSubfuncion = cp.subfuncion
					LEFT JOIN sia_actividades act ON act.idCuenta = cp.idCuenta AND act.idFinalidad = cp.finalidad AND act.idFuncion = cp.funcion AND act.idSubfuncion = cp.subfuncion and act.idActividad = cp.actividad
					LEFT JOIN sia_capitulos cap ON cap.idCuenta = cp.idCuenta AND cap.idCapitulo = cp.capitulo
					LEFT JOIN sia_partidas par ON par.idCuenta = cp.idCuenta AND par.idCapitulo = cp.capitulo AND par.idPartida = cp.partida
			ORDER BY  cp.finalidad, cp.funcion, cp.subfuncion, cp.actividad, cp.capitulo, cp.partida, cp.idCuenta;"; 
			*/
			$sql = "SELECT cp.idCuenta, cp.finalidad, ISNULL(fin.nombre,'') nom_finalidad, cp.funcion, ISNULL(fun.nombre,'') nom_funcion, cp.subfuncion, ISNULL(sfun.nombre,'') nom_subfuncion, cp.actividad, ISNULL(act.nombre,'') nom_actividad, cp.capitulo
				  , ISNULL(cap.nombre,'') nom_capitulo, cp.partida, par.nombre nom_partida
  				, FORMAT(cp.original,'$#,##0.00;($#,##0.00)') original, FORMAT(cp.modificado,'$#,##0.00;($#,##0.00)') modificado
  				, FORMAT(cp.ejercido,'$#,##0.00;($#,##0.00)') ejercido, FORMAT(cp.pagado,'$#,##0.00;($#,##0.00)') pagado
  				, FORMAT(cp.pendiente,'$#,##0.00;($#,##0.00)') pendiente
  				, CASE (cp.original + cp.ejercido)
        			WHEN cp.original THEN FORMAT(0,'##0.00;(##0.00)')
        			WHEN cp.ejercido THEN FORMAT ( ((cp.ejercido*100)/cp.modificado),'##0.00;(##0.00)' )
        			ELSE FORMAT( ((cp.ejercido*100)/cp.original), '##0.00;(##0.00)')
    			END AS Diferencia_Porcentual
			FROM (
					SELECT cd.idCuenta, cd.funcion, cd.subfuncion, cd.actividad, cd.capitulo, cd.partida, cd.finalidad
          	, SUM(CONVERT(decimal(18,2), cd.original) ) original, SUM(CONVERT(decimal(18,2), cd.modificado)) modificado
            , SUM(CONVERT(decimal(18,2), cd.ejercido)) ejercido, SUM(CONVERT(decimal(18,2), cd.pagado)) pagado
            , SUM(CONVERT(decimal(18,2), cd.pendiente)) pendiente
          FROM sia_cuentasdetalles cd WHERE cd.idcuenta in (:cuenta, :cuentaAnterior) AND cd.sector = :sector AND cd.subsector = :subsector AND cd.unidad = :unidad
          GROUP BY cd.idCuenta, cd.finalidad, cd.funcion, cd.subfuncion, cd.actividad, cd.capitulo, cd.partida) cp 
			LEFT JOIN sia_finalidades fin ON fin.idCuenta = cp.idCuenta AND fin.idFinalidad = cp.finalidad
			LEFT JOIN sia_funciones fun ON fun.idCuenta = cp.idCuenta AND fun.idFinalidad = cp.finalidad AND fun.idFuncion = cp.funcion
			LEFT JOIN sia_subFunciones sfun ON sfun.idCuenta = cp.idCuenta AND sfun.idFinalidad = cp.finalidad AND sfun.idFuncion = cp.funcion AND sfun.idSubfuncion = cp.subfuncion
			LEFT JOIN sia_actividades act ON act.idCuenta = cp.idCuenta AND act.idFinalidad = cp.finalidad AND act.idFuncion = cp.funcion AND act.idSubfuncion = cp.subfuncion and act.idActividad = cp.actividad
			LEFT JOIN sia_capitulos cap ON cap.idCuenta = cp.idCuenta AND cap.idCapitulo = cp.capitulo
			LEFT JOIN sia_partidas par ON par.idCuenta = cp.idCuenta AND par.idCapitulo = cp.capitulo AND par.idPartida = cp.partida
			ORDER BY  cp.finalidad, cp.funcion, cp.subfuncion, cp.actividad, cp.capitulo, cp.partida, cp.idCuenta;";

			$dbQuery = $db->prepare($sql);
			$dbQuery->execute( $campos );
			$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

			if(!$result){
				$app->halt(404, "NO SE ENCONTRARON OBJETOS DE FISCALIZACIÓN.");
			}else{
				echo json_encode($result);
			}
		
		}catch (PDOException $e) {
			print "¡Error!: " . $e->getMessage() . "<br/>";
			die();
		}
	});		

	
	$app->get('/obtnerAuditoriasParaIntegracion/:idEtapa', function($idEtapa)  use($app, $db) {

		$cuenta     = $_SESSION["idCuentaActual"];
		$area       = $_SESSION["idArea"];
		$empleado   = $_SESSION["idEmpleado"];
		$usrActual  = $_SESSION["idUsuario"];
		$global     = $_SESSION["usrGlobal"];
		$globalArea = $_SESSION["usrGlobalArea"];		
		$idProceso  = "AUDITORIAS";

		$aDatos     = array(':cuenta' => $cuenta, ':usrActual' => $usrActual);

		$sql="SELECT a.idAuditoria auditoria, e.idProceso, e.idEtapa, e.nombre etapa, COALESCE(convert(varchar(20),a.clave),convert(varchar(20),a.idAuditoria)) claveAuditoria, isnull(a.clave,'') clave, ar.nombre area, dbo.lstSujetosByAuditoria(a.idAuditoria) sujeto, dbo.lstObjetosByAuditoria(a.idAuditoria) objeto, ta.nombre tipo, '0.00' avances " .
			" FROM sia_programas p " .
  			" INNER JOIN sia_auditorias a on p.idCuenta=a.idCuenta and p.idPrograma=a.idPrograma " .
			" INNER JOIN sia_areas ar on a.idArea=ar.idArea " .
			" LEFT  JOIN sia_tiposauditoria ta on a.tipoAuditoria= ta.idTipoAuditoria " .
			" LEFT  JOIN sia_etapas e on e.idProceso =a.idProceso and e.idEtapa=a.idEtapa" .
			" WHERE a.idCuenta=:cuenta AND a.idEtapa in (Select idEtapa from sia_rolesetapas re inner join sia_usuariosroles ur on ur.idRol = re.idRol Where ur.idUsuario=:usrActual) " ;

		if ( $area == 'UTSFFA' || $area == 'UTSFEAJ' ) {
			$sql = $sql . " AND a.idArea in (SELECT idArea from sia_areas where idAreaSuperior = :area) ";
			$sql = $sql . " AND e.idProceso = :idProceso and e.idEtapa = :idEtapa ";
			$aDatos[':area'] = $area;
			$aDatos[':idProceso'] = $idProceso;
			$aDatos[':idEtapa'] = $idEtapa;
		}elseif ( $area == 'CAAAF') {
			$sql = $sql . " AND e.idProceso = :idProceso and e.idEtapa = :idEtapa ";
			$aDatos[':idProceso'] = $idProceso;
			$aDatos[':idEtapa'] = $idEtapa;
		} else {
			$sql = $sql . " AND a.idArea=:area ";  
			$aDatos[':area'] = $area;
		}
		$sql = $sql . " ORDER BY a.idAuditoria desc";

 		
		$dbQuery = $db->prepare($sql);
		
		$dbQuery->execute($aDatos);
	
		$result ['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON OBJETOS DE FISCALIZACIÓN. ");
		}else{
			echo json_encode($result);
		}
		
	});


	// *********************  Rutinas para el la consulta de Cuenta Pública por Unidad **********************************

	$app->get('/lstUnidadesbycta',  $autenticacionrole, function() use($app, $db) {
		//$cuenta = $_SESSION["idCuentaActual"];

		$idCuenta = $_SESSION["idCuentaVariable"];

		$sql = "SELECT u.idCuenta, u.idSector, u.idSubsector, u.idUnidad, concat(u.idSector, u.idSubsector, u.idUnidad) centroGestor, u.nombre, isnull(u.siglas,'') siglas, isnull(u.titular,'') titular, isnull(u.estatus,'') estatus, u.idUnidadSector, isnull(us.nombre,'') unidadSector, u.idUnidadPoder, isnull(up.nombre,'') unidadPoder, u.idUnidadClasificacion, isnull(uc.nombre,'') unidadClasificacion
		FROM sia_unidades u 
  			LEFT JOIN sia_unidadesSectores us      ON u.idUnidadSector        = us.idUnidadSector
  			LEFT JOIN sia_unidadesPoderes up       ON u.idUnidadPoder         = up.idUnidadPoder
  			LEFT JOIN sia_UnidadesClasificacion uc ON u.idUnidadClasificacion = uc.idUnidadClasificacion 
		WHERE u.idCuenta = :idCuenta 
		ORDER BY u.idCuenta desc, u.idSector + u.idSubsector + u.idUnidad asc;" ;

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':idCuenta' => $idCuenta));

		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			$app->render('consultaCtaPubUnidades.php', $result);
			//$app->render('catUnidades.php', $result);
		} 
	})->name('lstUnidadesbycta');
	/*
	$app->get('/lstunidadesbycta', function () use ($app) {
		
		$cuenta = $_SESSION["idCuentaActual"];
		$url = $app->urlFor('lstUnidadesbycta', array('idCuenta' => $cuenta)); 
		$app->redirect($url, 301);
	});
	*/


	$app->get('/obtenArchivosUnidadesPorCta/:rutaServidor', function($rutaServidor) use($app) {

		try {
			//$archivos = array();
			$cara = "{";
			$archivos = "";
			$archivosContador = 0;
			$rutaServidor = str_replace("*", "/", $rutaServidor );

			if(substr($rutaServidor, -1) != "/") $rutaServidor .= "/";
			
			if (is_dir($rutaServidor)){
				if ($dh = opendir($rutaServidor)){
					while ( false != ($archivo = readdir($dh)) ){
						$archivo = trim($archivo);
						$archivo = preg_replace('/\&(.)[^;]*;/', '', $archivo);
						$archivo = preg_replace('[\n+]'        , '', $archivo);
						$archivo = preg_replace('[\r+]'        , '', $archivo);

						if ($archivo != "." and $archivo != ".." ) {
							//$archivos[$archivosContador++] = $archivo;

							$archivos .= $cara . $archivo;
							$cara = "*";
						}
					}
					closedir($dh);
				}
			}
			echo $archivos;
		}catch (Exception $e) {
			print "¡Error!: " . $e->getMessage() . "<br/>";
			die();
		}

 	});

	/*
	$app->get('/obtenerArchivosUnidadesPorCta/:directorio', function($directorio) use($app) {

	// Array en el que obtendremos los resultados
	$res = array();
	 
	// Agregamos la barra invertida al final en caso de que no exista
	if(substr($directorio, -1) != "/") $directorio .= "/";
	 
	// Creamos un puntero al directorio y obtenemos el listado de archivos
	$dir = @dir($directorio) or die("getFileList: Error abriendo el directorio $directorio para leerlo");
	while(($archivo = $dir->read()) !== false) {
	   	// Obviamos los archivos ocultos
	   	if($archivo[0] == ".") continue;
		if(is_dir($directorio . $archivo)) {
	   		//$res[]=array("Nombre"=>$directorio.$archivo."/","Tamaño"=>0,"Modificado"=>filemtime($directorio . $archivo)	);
	   		$res["Nombre"] = $directorio.$archivo."/" ;
		}else if (is_readable($directorio . $archivo)) {
	   		//$res[]=array("Nombre"=>$directorio.$archivo,"Tamaño"=>filesize($directorio.$archivo),"Modificado"=> filemtime($directorio.$archivo) );
	   		$res["Nombre"] = $directorio.$archivo;
		}	
	}
	$dir->close();
	echo $res;
 	});
	*/

	$app->get('guardarAuditoriaEtapa/:valores', function($valores)  use($app, $db) {

		$idCuenta = $_SESSION ["idCuentaActual"];
		$idPrograma = $_SESSION ["idProgramaActual"];
		$usrActual = $_SESSION ["idUsuario"];

		try
		{
			$campos = explode("|", $valores);

			$oper       = $campos [0];
			$idAuditoria= $campos [1];
			$idProceso  = $campos [2];
			$idEtapa    = $campos [3];
			$estatus    = $campos [4];
			$comentario = $campos [5];

			if($oper=='INS')
			{
				$sql="INSERT INTO sia_auditoriasetapas (idCuenta, idPrograma, idAuditoria, idProceso, idEtapa, usrAlta, fAlta, estatus, comentario) VALUES (:idCuenta, :idPrograma, :idAuditoria, :idProceso, :idEtapa, :usrAlta, getdate(), :estatus, :comentario);";

				$dbQuery = $db->prepare($sql);

				$dbQuery->execute(array(':idCuenta' => $idCuenta, ':idPrograma' => $idPrograma, ':idAuditoria' => $idAuditoria, ':idProceso' => $idProceso, ':idEtapa' => $idEtapa, ':usrAlta' => $usrActual, ':estatus' => $estatus, ':comentario' => $comentario));
			}
			else
			{
				$sql="UPDATE sia_auditoriasetapas SET comentario=:comentario, estatus=:estatus, usrModificacion=:usrModificacion, fModificacion=getdate() WHERE idCuenta=:idCuenta AND idPrograma=:idPrograma AND idAuditoria=:idAuditoria AND idProceso=:idProceso AND idEtapa=:idEtapa";
				$dbQuery = $db->prepare($sql);

				$dbQuery->execute(array(':comentario' => $comentario, ':estatus' => $estatus, ':usrModificacion' => $usrActual, ':idCuenta' => $idCuenta, ':idPrograma' => $idPrograma, ':idAuditoria' => $idAuditoria, ':idProceso' => $idProceso, ':idEtapa' => $idEtapa));
			}
			echo("OK");

		}catch (BDException $e) {
			print "¡Error!: " . $e->getMessage() . "<br/>";
			echo("NO");
		}

				//$app->redirect($app->urlFor('listaAuditoriaCriterios'));
	});
	// ---------------------- Rutinas del catálogo de plazas

		$app->get('/lstPlazas',  $autenticacionrole, function() use($app, $db) {
		$cuenta    = $_SESSION["idCuentaActual"];
		$usrActual = $_SESSION ["idUsuario"];
		$empleado   = $_SESSION["idEmpleado"];
		$area       = $_SESSION["idArea"];
		$global     = $_SESSION["usrGlobal"];
		$globalArea = $_SESSION["usrGlobalArea"];		


		// Primero recuperará el área del 

		if ($global=="SI"){
			
			$sql = "SELECT a.idArea, a.nombre area, p.idPuesto, pto.nombre puesto, p.idNivel, n.idNivel idNivel1, n.nombre nivel, nom.idNombramiento, nom.nombre nombramiento, p.idPlaza, p.nombre plaza, p.idPlazaSuperior, pp.nombre PlazaSuperior, e.idEmpleado, e.nombre + ' ' + e.paterno + ' ' + e.materno nombre 
					FROM sia_plazas p 
	  				LEFT JOIN sia_areas a on p.idArea = a.idArea 
	  				LEFT JOIN sia_puestos pto on pto.idPuesto = p.idPuesto
	  				LEFT JOIN sia_niveles n on n.idNivel = p.idNivel 
	  				LEFT JOIN sia_nombramientos nom on nom.idNombramiento = p.idNombramiento
	  				LEFT JOIN sia_plazas pp on pp.idPlaza = p.idPlazaSuperior
		            LEFT JOIN sia_empleados e on p.idPlaza = e.idPlaza
	  				ORDER BY p.idArea, p.idNivel desc;" ;

			$dbQuery = $db->prepare($sql);
			$dbQuery->execute();

		} else if ($globalArea=="SI")	{

			$sql = "SELECT a.idArea, a.nombre area, p.idPuesto, pto.nombre puesto, p.idNivel, n.idNivel idNivel1, n.nombre nivel, nom.idNombramiento, nom.nombre nombramiento, p.idPlaza, p.nombre plaza, p.idPlazaSuperior, pp.nombre PlazaSuperior, e.idEmpleado, e.nombre + ' ' + e.paterno + ' ' + e.materno nombre  
					FROM sia_plazas p 
	  				LEFT JOIN sia_areas a on p.idArea = a.idArea 
	  				LEFT JOIN sia_puestos pto on pto.idPuesto = p.idPuesto
	  				LEFT JOIN sia_niveles n on n.idNivel = p.idNivel 
	  				LEFT JOIN sia_nombramientos nom on nom.idNombramiento = p.idNombramiento
	  				LEFT JOIN sia_plazas pp on pp.idPlaza = p.idPlazaSuperior 
		            LEFT JOIN sia_empleados e on p.idPlaza = e.idPlaza
	  				WHERE a.idArea = :idArea 
	  				ORDER BY p.idNivel desc;" ;
			
			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':idArea' => $area));

		}

		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			$app->render('catPlazas.php', $result);
			//$app->render('catUnidades.php', $result);
		} 
	})->name('lstPlazas');

	$app->get('/obtenerPlaza/:idPlaza', function($idPlaza)    use($app, $db) {

		$idPlaza = str_replace('_','/',$idPlaza);

		$sql="SELECT idArea, idPuesto, idNivel, idNombramiento, idPlaza, Nombre, estatus, idPlazaSuperior FROM sia_plazas WHERE idPlaza=:idPlaza ";
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':idPlaza' => $idPlaza));

		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});

	$app->get('/lstTodasPlazas(/:idArea)', function($idArea=NULL)  use($app, $db) {
		try{

			$aDatos = array();

			//$sql="SELECT idPlaza id, idArea + '/' + idPuesto + '/' + idNivel + '/' + idNombramiento + '/' + idPlaza + '/' + Nombre  texto FROM sia_plazas ";
			$sql = "SELECT p.idPlaza id, p.idPlaza + ' - ' + p.nombre + ' - (' + e.nombre + ' ' + e.paterno + ' ' + e.materno + ')' texto FROM sia_plazas p LEFT JOIN sia_empleados e ON p.idPlaza = e.idPlaza ";

			if ($idArea != NULL){
				$sql .= " WHERE p.idArea = :idArea ";
				$aDatos['idArea'] = $idArea;
			}

			$sql .= " ORDER BY p.nombre, p.idPlaza ";

			$dbQuery = $db->prepare($sql);
			$dbQuery->execute($aDatos);

			$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

			if(!$result){
				$app->halt(404, "NO SE ENCONTRARON DATOS ");
			}else{
				echo json_encode($result);
			}
		}catch (BDException $e) {
			print "¡Error!: " . $e->getMessage() . "<br/>";
			echo($e->getMessage());
		}
	});

	$app->post('/guardar/plaza', function()  use($app, $db) {
		$usrActual = $_SESSION ["idUsuario"];
		$cuenta = $_SESSION ["idCuentaActual"];

		$request         = $app->request;
		$oper            = $request->post('txtOperacion');
		$idArea          = $request->post('txtArea');
		$idPuesto        = $request->post('txtPuesto');
		$idNivel         = $request->post('txtNivel');
		$idNombramiento  = $request->post('txtNombramiento');
		$idPlaza         = strtoupper($request->post('txtPlaza'));
		$nombre          = strtoupper($request->post('txtNombrePlaza'));
		$estatus         = $request->post('txtEstatus');
		$idPlazaSuperior = $request->post('txtPlazaSuperior');

		$aDatos = array(':idArea' => $idArea, ':idPuesto' => $idPuesto, ':idNivel' => $idNivel, ':idNombramiento' => $idNombramiento, ':idPlaza' => $idPlaza, ':nombre' => $nombre , ':usrActual' => $usrActual, ':idPlazaSuperior' => $idPlazaSuperior   );

		try
		{
			if($oper=='INS')
			{
				$sql="INSERT INTO sia_plazas (idArea, idPuesto, idNivel, idNombramiento, idPlaza, nombre, usrAlta, fAlta, estatus, idPlazaSuperior) VALUES(:idArea, :idPuesto, :idNivel, :idNombramiento, :idPlaza, :nombre, :usrActual, getdate(), 'ACTIVO', :idPlazaSuperior);";

			}else{
			
				$sql="UPDATE sia_plazas SET idArea=:idArea, idPuesto=:idPuesto, idNivel=:idNivel, idNombramiento=:idNombramiento, idPlaza=:idPlaza, nombre=:nombre, usrModificacion=:usrActual, fModificacion=getdate(), estatus=:estatus, idPlazaSuperior=:idPlazaSuperior WHERE idPlaza = :idPlaza" ;

			}

			$dbQuery = $db->prepare($sql);
			$dbQuery->execute($aDatos);

		}catch (DBException $e) {
			print "¡Error!: " . $e->getMessage() . "<br/>";
			die();
		}
		$app->redirect($app->urlFor('lstPlazas'));
	});

	$app->get('/obtenerPlazaById/:idPlaza', function($idPlaza) use($app, $db) {

		$idPlaza = str_replace('_','/',$idPlaza);

		$dbQuery = $db->prepare("SELECT COUNT(*) total FROM sia_plazas WHERE idPlaza = :idPlaza;");  

		$dbQuery->execute(array(':idPlaza'=> $idPlaza));
		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		} 
	});

	// ---------------------- Rutinas del catálogo de áreas

		$app->get('/lstAreasCat',  $autenticacionrole, function() use($app, $db) {
		$cuenta = $_SESSION["idCuentaActual"];

		$sql = "SELECT a.idArea, a.nombre, ISNULL(a.nombreCorto,'') nombreCorto, ISNULL(e.idEmpleado,'') idEmpleado, ISNULL(e.nombre + ' ' + e.paterno + ' ' + e.materno,'') empleadoTitular, ISNULL(a.idAreaSuperior, '') idAreaSuperior, ISNULL(aa.nombre,'') areaSuperior, a.estatus FROM sia_Areas a LEFT JOIN sia_empleados e on e.idEmpleado = a.idEmpleadoTitular LEFT JOIN sia_areas aa on aa.idArea = a.idAreaSuperior order by a.idArea;" ;

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute();

		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			$app->render('catAreas.php', $result);
			//$app->render('catUnidades.php', $result);
		} 
	})->name('lstAreasCat');

	$app->get('/lstEmpleados(/:idArea)', function($idArea=NULL)    use($app, $db) {
		try{

			$aDatos = array();

			$sql = "SELECT e.idEmpleado id, e.idArea + ' - ' + e.Nombre + ' ' + e.paterno + ' ' + e.materno + ' (' + isnull(p.nombre,'Sin Puesto') + ')' texto FROM sia_empleados e LEFT JOIN sia_puestos p ON p.idPuesto = e.idPuesto ";

			if ($idArea != NULL){
				$sql .= " WHERE idArea = :idArea ";
				$aDatos['idArea'] = $idArea;
			}

			$sql .= " ORDER BY texto ";

			$dbQuery = $db->prepare($sql);
			$dbQuery->execute($aDatos);

			$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

			if(!$result){
				$app->halt(404, "NO SE ENCONTRARON DATOS ");
			}else{
				echo json_encode($result);
			}
		}catch (BDException $e) {
			print "¡Error!: " . $e->getMessage() . "<br/>";
			echo($e->getMessage());
		}
	});

	$app->get('/obtenerArea/:idArea', function($idArea)    use($app, $db) {

		$sql="SELECT idArea, nombre, nombreCorto, idAreaSuperior, idEmpleadoTitular, estatus FROM sia_areas WHERE idArea=:idArea ";
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':idArea' => $idArea));

		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});

	
	$app->get('/obtenerAreaById/:idArea', function($idArea) use($app, $db) {

		$dbQuery = $db->prepare("SELECT COUNT(*) total FROM sia_areas WHERE idArea = :idArea;");  

		$dbQuery->execute(array(':idArea'=> $idArea));
		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		} 
	});

	$app->post('/guardar/area', function()  use($app, $db) {
		$usrActual = $_SESSION ["idUsuario"];
		$cuenta = $_SESSION ["idCuentaActual"];

		$request           = $app->request;
		$oper              = $request->post('txtOperacion');
		$idArea            = strtoupper($request->post('txtArea'));
		$nombre            = strtoupper($request->post('txtNombreArea'));
		$nombreCorto       = strtoupper($request->post('txtNombreCorto'));
		$idEmpleadoTitular = $request->post('txtEmpleadoTitular');
		$idAreaSuperior    = strtoupper($request->post('txtAreaSuperior'));
		$estatus           = $request->post('txtEstatus');
		$unidadesSeleccion = $request->post('txtUnidadesSeleccionadas');


		$aDatos = array(':idArea' => $idArea, ':nombre' => $nombre, ':nombreCorto' => $nombreCorto, ':idAreaSuperior' => $idAreaSuperior, ':usrActual' => $usrActual, ':idEmpleadoTitular' => $idEmpleadoTitular );
		try
		{
			if($oper=='INS')
			{

				$sql="INSERT INTO sia_areas (idArea, nombre, nombreCorto, idAreaSuperior, usrAlta, fAlta, estatus, idEmpleadoTitular) VALUES(:idArea, :nombre, :nombreCorto, :idAreaSuperior, :usrActual, getdate(), 'ACTIVO' , :idEmpleadoTitular);";
			}else{
			
				$aDatos[':estatus'] = $estatus;

				$sql="UPDATE sia_areas SET nombre=:nombre, nombreCorto=:nombreCorto, idAreaSuperior=:idAreaSuperior, usrModificacion=:usrActual, fModificacion=getdate(), estatus=:estatus, idEmpleadoTitular=:idEmpleadoTitular WHERE idArea = :idArea" ;

			}
			$dbQuery = $db->prepare($sql);
			$dbQuery->execute($aDatos);

			// Se actualizaran la tabla de relación de Areas y Unidades SIA_AREASUNIDADES con relación a la area y la cuenta

			if( strlen($unidadesSeleccion) > 0){

		    	$centrosGestores = explode("*", $unidadesSeleccion);

		    	if( count($centrosGestores) > 0 ){

					// Primero borrara de la tabla 
					$sql = "DELETE FROM sia_areasUnidades Where idCuenta = :idCuenta and idArea = :idArea";
					$dbQuery = $db->prepare($sql);
					$dbQuery->execute(array(':idCuenta'=> $cuenta, ':idArea'=> $idArea));


					// Ahora se agregan a la tabla sia_areasunidades cada unidad que se hayan seleccionado en la lista (tabla)
					foreach ($centrosGestores as $centroGestor) {

						$idSector     = substr($centroGestor,0,2);
						$idSubsector  = substr($centroGestor,2,2);
						$idUnidad     = substr($centroGestor,4,2);

						$sql="INSERT INTO sia_areasunidades (idCuenta, idSector, idSubsector, idUnidad, idArea, usrAlta, fAlta) VALUES (:idCuenta, :idSector, :idSubsector, :idUnidad, :idArea, :usrActual, getdate() );";
						
						$dbQuery = $db->prepare($sql);
						$dbQuery->execute(array(':idCuenta'=>$cuenta, ':idSector'=>$idSector, ':idSubsector'=>$idSubsector, ':idUnidad'=>$idUnidad, ':idArea'=>$idArea, ':usrActual'=> $usrActual) );
					}
				}
			}


		}catch (DBException $e) {
			print "¡Error!: " . $e->getMessage() . "<br/>";
			die();
		}
		$app->redirect($app->urlFor('lstAreasCat'));
	});


	// ---------------------------- Rutinas del Catálogo de Procesos ------------------
	
	$app->get('/catProcesos',  $autenticacionrole, function() use($app, $db) {
		$cuenta = $_SESSION["idCuentaActual"];

		$sql = "SELECT idProceso, nombre, estatus FROM sia_procesos;" ;

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute();

		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			$app->render('catProcesos.php', $result);
			//$app->render('catUnidades.php', $result);
		} 
	})->name('catProcesos');

	$app->get('/obtenerProceso/:idProceso', function($idProceso)    use($app, $db) {

		$sql="SELECT idProceso, nombre, estatus FROM sia_procesos WHERE idProceso=:idProceso;";
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':idProceso' => $idProceso));

		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});

	$app->get('/obtenerProcesoById/:idProceso', function($idProceso) use($app, $db) {

		$dbQuery = $db->prepare("SELECT COUNT(*) total FROM sia_procesos WHERE idProceso = :idProceso;");  

		$dbQuery->execute(array(':idProceso'=> $idProceso));
		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		} 
	});

	$app->get('/lstProcesos_HVS(/:idProceso)', function($idProceso=NULL) use($app, $db){
		$cuenta = $_SESSION["idCuentaActual"];
		$area = $_SESSION["idArea"];
		
		$usrActual = $_SESSION["idUsuario"];
		$global = $_SESSION["usrGlobal"];
		$aDatos = array();

		$sql= "SELECT idProceso id, isnull(nombre,'') texto FROM sia_procesos ";
		
		if ($idProceso!=NULL){

			$sql .= " WHERE idProceso = :idProceso ";
			$aDatos['idProceso'] = $idProceso;
		}

		$sql .= ' ORDER BY nombre ';

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute($aDatos);

		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "RECURSO NO ENCONTRADO.");			
		}else{		
			echo json_encode($result);
		}		
	});


	$app->post('/guardar/proceso', function()  use($app, $db) {
		$usrActual = $_SESSION ["idUsuario"];
		$cuenta = $_SESSION ["idCuentaActual"];

		$request           = $app->request;
		$oper              = $request->post('txtOperacion');
		$idProceso         = strtoupper($request->post('txtProceso'));
		$nombre            = strtoupper($request->post('txtNombreProceso'));
		$estatus           = $request->post('txtEstatus');


		$aDatos = array(':idProceso' => $idProceso, ':nombre' => $nombre, ':usrActual' => $usrActual );
		try
		{
			if($oper=='INS')
			{

				$sql="INSERT INTO sia_procesos (idProceso, nombre, usrAlta, fAlta, estatus) VALUES(:idProceso, :nombre, :usrActual, getdate(), 'ACTIVO');";
			}else{
			
				$aDatos[':estatus'] = $estatus;

				$sql="UPDATE sia_procesos SET nombre=:nombre, usrModificacion=:usrActual, fModificacion=getdate(), estatus=:estatus WHERE idProceso = :idProceso" ;

			}

			$dbQuery = $db->prepare($sql);
			$dbQuery->execute($aDatos);

		}catch (DBException $e) {
			print "¡Error!: " . $e->getMessage() . "<br/>";
			die();
		}
		$app->redirect($app->urlFor('catProcesos'));
	});


	// ---------------------------- Rutinas del Catálogo de Procesos ------------------
	
	$app->get('/catEtapas',  $autenticacionrole, function() use($app, $db) {
		$cuenta = $_SESSION["idCuentaActual"];

		$sql = "SELECT e.idEtapa, e.nombre, p.nombre proceso, e.orden, ISNULL(e.fase,'') fase, e.estatus from sia_etapas e INNER JOIN sia_procesos p ON p.idProceso = e.idProceso ORDER BY e.idProceso, e.orden;" ;

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute();

		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			$app->render('catEtapas.php', $result);
			//$app->render('catUnidades.php', $result);
		} 
	})->name('catEtapas');

	$app->get('/obtenerEtapa/:idEtapa', function($idEtapa)    use($app, $db) {

		$sql = "SELECT e.idEtapa, e.nombre etapa, e.idProceso, p.nombre proceso, e.orden, ISNULL(e.fase,'') fase, e.estatus from sia_etapas e INNER JOIN sia_procesos p ON p.idProceso = e.idProceso WHERE idEtapa = :idEtapa ORDER BY e.idProceso, e.orden;" ;

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':idEtapa' => $idEtapa));

		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});

	$app->get('/obtenerEtapaById/:idEtapa/:idProceso', function($idEtapa, $idProceso) use($app, $db) {

		$dbQuery = $db->prepare("SELECT COUNT(*) total FROM sia_etapas WHERE idEtapa=:idEtapa AND idProceso=:idProceso ;");  

		$dbQuery->execute(array(':idEtapa'=> $idEtapa, ':idProceso'=> $idProceso));
		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		} 
	});

	$app->post('/guardar/etapa', function()  use($app, $db) {
		$usrActual = $_SESSION ["idUsuario"];
		$cuenta = $_SESSION ["idCuentaActual"];

		$request    = $app->request;
		$oper       = $request->post('txtOperacion');
		$idEtapa    = strtoupper($request->post('txtEtapa'));
		$nombre     = strtoupper($request->post('txtNombreEtapa'));
		$idProceso  = $request->post('txtProceso');
		$orden      = $request->post('txtOrden');
		$fase       = strtoupper($request->post('txtFase'));
		$estatus    = $request->post('txtEstatus');


		$aDatos = array(':idProceso' => $idProceso, ':idEtapa' => $idEtapa, ':nombre' => $nombre, ':orden' => $orden, ':fase' => $fase, ':usrActual' => $usrActual );
		try
		{
			if($oper=='INS')
			{

				$sql="INSERT INTO sia_etapas (idProceso, idEtapa, nombre, orden, fase, usrAlta, fAlta, estatus) VALUES (:idProceso, :idEtapa, :nombre, :orden, :fase, :usrActual, getdate(), 'ACTIVO');";
			}else{
			
				$aDatos[':estatus'] = $estatus;

				$sql="UPDATE sia_etapas SET nombre=:nombre, orden=:orden, fase=:fase, usrModificacion=:usrActual, fModificacion=getdate(), estatus=:estatus WHERE idProceso=:idProceso AND idEtapa=:idEtapa " ;

			}

			$dbQuery = $db->prepare($sql);
			$dbQuery->execute($aDatos);

		}catch (DBException $e) {
			print "¡Error!: " . $e->getMessage() . "<br/>";
			die();
		}
		$app->redirect($app->urlFor('catEtapas'));
	});

	//------------------------------- RUTINAS PARA DUPLICIADA DE UNIDADES POR CUENTA --------------------------------------------------------
	/*
	$app->get('/lstCuentaSiguiente', function()    use($app, $db) {
		$sql=" SELECT idCuenta id, nombre texto FROM sia_cuentas ORDER BY anio ";

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute();
		$result ['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});
	*/

	$app->get('/validarExisteCuenta/:idCuenta', function($idCuenta)    use($app, $db) {
		$sql="SELECT COUNT(*) total FROM sia_unidades WHERE idCuenta = :idCuenta ";

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array( ':idCuenta' => $idCuenta ));
		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		} 
	});
/*
	$app->get('/asignarUnidadesToCuenta/:idCuentaOrigen/:idCuentaDestino', function($idCuentaOrigen, $idCuentaDestino)  use($app, $db) {

		$usrActual = $_SESSION ["idUsuario"];
		$cuenta = $_SESSION ["idCuentaActual"];
		$programa = $_SESSION ["idProgramaActual"];
		
		try
		{

			$Sql = " DELETE FROM sia_unidades WHERE idCuenta=:idCuenta ";

			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':idCuenta' => $idCuentaDestino));

			$sql="INSERT INTO sia_unidades (idCuenta, idSector, idSubsector, idUnidad, nombre, usrAlta, Falta, estatus, titular, idUnidadSector, idUnidadPoder, idUnidadClasificacion, siglas) SELECT :idCuentaDestino, u.idSector, u.idSubsector, u.idUnidad, u.nombre, :usrAlta, getdate(), 'ACTIVO', u.titular, u.idUnidadSector, u.idUnidadPoder, idUnidadClasificacion, u.siglas FROM sia_unidades u WHERE u.idCuenta = :idCuentaOrigen;";

			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':idCuentaDestino' => $idCuentaDestino, ':usrAlta' => $usrActual, ':idCuentaOrigen' => $idCuentaOrigen));
			echo("OK");


		}catch (DBException $e) {
			print "¡Error!: " . $e->getMessage() . "<br/>";
			die();
		}
		*/

	//---------------------- RUTINAS PARA DATOS DE CARGA ARCHIVOS BINARIOS	

		$app->get('/binarios',  $autenticacionrole, function() use($app, $db){
		$cuenta = $_SESSION["idCuentaActual"];
		$area = $_SESSION["idArea"];
		
		$usrActual = $_SESSION["idUsuario"];
		$global = $_SESSION["usrGlobal"];
		
		$sql= "SELECT DISTINCT d.idDocto, isnull(d.numeroDocto,'') numeroDocto
			    , isnull(d.idTipoDocto,'') idTipoDocto, isnull(td.nombre,'') tipo
				, isnull(d.flujoDocto,'') flujodocto
				, isnull(d.fDocto,'') fDocto, isnull(fRecepcion,'') fRecepcion, isnull(fTermino,'') fTermino
				, isnull(d.idRemitente,'') idRemitente, isnull(u.nombre,'') remitente
				, isnull(d.idDestinatario,'') idDestinatario, isnull(a.nombre,'') destinatario
				, isnull(d.idPrioridad,'') idPrioridad 
				, isnull(d.idImpacto,'') idImpacto
				, isnull(d.asunto,'') asunto
				, isnull(d.idRecibio,'') idrecibio
				, isnull(d.estatus,'') estatus
				, isnull(d.archivoOriginal,'') archivoOriginal
				, isnull(d.archivoFinal,'') archivoFinal
					FROM sia_documentos d
					INNER JOIN sia_tiposdocumentos td ON d.idTipoDocto = td.idTipoDocto 
					INNER JOIN sia_unidades u ON d.idRemitente = u.idSector + u.idSubsector + u.idUnidad 
					INNER JOIN sia_areas a ON d.idDestinatario = a.idArea ";
		$dbQuery = $db->prepare($sql);
		//$dbQuery->execute(array(':cuenta' => $cuenta, ':area' => $area));
		$dbQuery->execute();
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		$app->render('binarios.php', $result);

	})->name('binarios');
	
	
	$app->get('/guardarBinario/:contenidoBinario/:nombreBinario', function($contenidoBinario, $nombreBinario)  use($app, $db) {

		$usrActual = $_SESSION ["idUsuario"];
		$cuenta = $_SESSION ["idCuentaActual"];
		$programa = $_SESSION ["idProgramaActual"];

		$datos = explode(".", $nombreBinario);
		$extension="";
		foreach($datos as $parte) {
			$extension = "." . trim($parte);
		}	
		$contenidoBinario = str_replace('*','/',$contenidoBinario);

		$contenidoBinario1 = base64_encode(file_get_contents($contenidoBinario));

		//Nuevo nombre
		$nuevoNombre = "d" . date("YmdHis") . $extension;
		$moduloOrigen = 'BINARIOS';
		$idOrigen = 1;
		$usrActual = 9;

		try
		{

			$sql="INSERT INTO sia_auditoriascriterios (moduloOrigen, idOrigen, nombreOriginal, nombreFinal, archivo, fArchivo, usrAlta) VALUES(:moduloOrigen, :idOrigen, :nombreOriginal, :nombreFinal, :archivo, getdate(), :usrActual );";

			$dbQuery = $db->prepare($sql);

			$dbQuery->execute(array(':moduloOrigen' => $moduloOrigen, ':idOrigen' => $idOrigen, ':nombreOriginal' => $nombreBinario, ':nombreFinal' => $nuevoNombre, ':archivo' => $contenidoBinario1, ':usrActual' => $usrActual));

			echo "OK";

		}catch (Exception $e) {
			//print "¡Error!: " . $e->getMessage() . "<br/>";
			echo "¡Error!: " . $e->getMessage() . "<br/>";
			die("No se pudo insertar los datos en la base de datos.");
		}

	});


	// ----------------------------- RUTINAS PARA DATOS DE AUDITORIAS HISTORICAS -------------------------

	$app->get('/audihistoricos',  $autenticacionrole, function() use($app, $db){
		$cuenta = $_SESSION["idCuentaActual"];
		$area = $_SESSION["idArea"];
		
		$usrActual = $_SESSION["idUsuario"];
		$global = $_SESSION["usrGlobal"];

		$sql= "SELECT idAuditoriaHistorico, anio, idCuenta, idPrograma, isnull(idAuditoria,'') idAuditoria, cveAuditoria, idArea, nombreArea, nombreAreaAnterior, isnull(idTipoAuditoria,'') idTipoAuditoria, nombreTipoAuditoria, isnull(idSector,'') idSector, isnull(idSubsector,'') idsubsector, isnull(idUnidad,'') idUnidad, concat(isnull(idSector,''), isnull(idSubsector,''), isnull(idUnidad,'')) centroGestor, nombreUnidad, isnull(idObjeto,'') idObjeto, nombreObjeto, estatus FROM sia_auditoriasHistoricos ";

			$sql .= " ORDER BY nombreUnidad, anio";
 			$dbQuery = $db->prepare($sql);
			$dbQuery->execute();

		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		$app->render('catAuditoriasHistoricos.php', $result);

	})->name('audihistoricos');


	$app->get('/obtenerAuditoriaHistorica/:idAuditoriaHistorico', function($idAuditoriaHistorico)    use($app, $db) {

		$sql= "SELECT idAuditoriaHistorico, anio, idCuenta, idPrograma, isnull(idAuditoria,'') idAuditoria, cveAuditoria, idArea, nombreArea, nombreAreaAnterior, isnull(idTipoAuditoria,'') idTipoAuditoria, nombreTipoAuditoria, isnull(idSector,'') idSector, isnull(idSubsector,'') idsubsector, isnull(idUnidad,'') idUnidad, concat(isnull(idSector,''),'|' ,isnull(idSubsector,''), '|', isnull(idUnidad,'')) centroGestor, nombreUnidad, isnull(idObjeto,'') idObjeto, nombreObjeto, estatus FROM sia_auditoriasHistoricos WHERE idAuditoriaHistorico = :idAuditoriaHistorico ";

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':idAuditoriaHistorico' => $idAuditoriaHistorico));

		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});

	$app->get("/actualizaFiltro(/:campo(/:filtro))", function($campo=NULL, $filtro=NULL) use($app, $db) {

		$cuenta = $_SESSION["idCuentaActual"];
		$area = $_SESSION["idArea"];
		
		$usrActual = $_SESSION["idUsuario"];
		$global = $_SESSION["usrGlobal"];

		$sql= "SELECT idAuditoriaHistorico, anio, idCuenta, idPrograma, isnull(idAuditoria,'') idAuditoria, cveAuditoria, idArea, nombreArea, nombreAreaAnterior, isnull(idTipoAuditoria,'') idTipoAuditoria, nombreTipoAuditoria, isnull(idSector,'') idSector, isnull(idSubsector,'') idsubsector, isnull(idUnidad,'') idUnidad, concat(isnull(idSector,''), isnull(idSubsector,''), isnull(idUnidad,'')) centroGestor, nombreUnidad, isnull(idObjeto,'') idObjeto, nombreObjeto, estatus FROM sia_auditoriasHistoricos ";

		if ($campo != "sinFiltro"){
			$sql .= " WHERE " . $campo . " LIKE '%" . $filtro . "%' ";
		}

		$sql .= " ORDER BY nombreUnidad, anio ";
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute();
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	})->name('listaConFiltro');

	//Lista de sujetos
	$app->get('/lstSujetos_HVS', function()    use($app, $db) {
		$cuenta = $_SESSION["idCuentaActual"];

		$sql="SELECT concat(isnull(idSector,''),'|',isnull(idSubsector,''),'|',isnull(idUnidad,'')) id, nombre texto FROM sia_unidades WHERE idCuenta = :cuenta ORDER BY nombre";
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':cuenta'=>$cuenta));
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});

	/*	
	$app->post('/guardar/audiHist', function()  use($app, $db) {
		$usrActual = $_SESSION ["idUsuario"];
		$cuenta = $_SESSION ["idCuentaActual"];

		$request              = $app->request;
		$oper                 = $request->post('txtOperacion');
		$idAuditoriaHistorico = $request->post('txtIdAuditoriaHistorico');
		$idTipoAuditoria      = $request->post('txtTipoAuditoria');
		$centroGestor         = $request->post('txtCentroGestor');
		
		$aValores = explode("|", $centroGestor);
		$idSector    = $aValores [0];
		$idSubsector = $aValores [1];
		$idUnidad    = $aValores [2];
		//$estatus    = $request->post('txtEstatus');

		$campoFiltro          = $request->post('txtCampoFiltro');
		$datoFiltro           = $request->post('txtDatoFiltro');
		try
		{
			if($oper=='INS')
			{

				//$sql="INSERT INTO sia_etapas (idProceso, idEtapa, nombre, orden, fase, usrAlta, fAlta, estatus) VALUES (:idProceso, :idEtapa, :nombre, :orden, :fase, :usrActual, getdate(), 'ACTIVO');";
			}else{
			
				$sql="UPDATE sia_auditoriasHistoricos SET idSector=:idSector, idSubsector=:idSubsector, idUnidad=:idUnidad, idTipoAuditoria=:idTipoAuditoria, usrModificacion=:usrActual, fModificacion=getdate() WHERE idAuditoriaHistorico=:idAuditoriaHistorico" ;

				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':idSector'=>$idSector, ':idSubsector'=>$idSubsector, ':idUnidad'=>$idUnidad, ':idTipoAuditoria'=>$idTipoAuditoria, ':usrActual'=>$usrActual, ':idAuditoriaHistorico'=>$idAuditoriaHistorico));
			}

			//echo "OK";

		}catch (DBException $e) {
			print "¡Error!: " . $e->getMessage() . "<br/>";
			die();
		}
		$app->redirect($app->urlFor('audihistoricos'));
		//$app->redirect($app->urlFor('listaConFiltro'), [':campo'=>$campoFiltro, ':filtro'=>$datoFiltro ]);
	});
	*/

	$app->get('/guardar/audiHist/:sValores', function($sValores)  use($app, $db) {
		$usrActual = $_SESSION ["idUsuario"];
		$cuenta = $_SESSION ["idCuentaActual"];

		$aValores = explode("|", $sValores);

		$oper                 = $aValores [0];
		$idAuditoriaHistorico = $aValores [1];
		$idTipoAuditoria      = $aValores [2];
		$idSector             = $aValores [3];
		$idSubsector          = $aValores [4];
		$idUnidad             = $aValores [5];

		//echo $aValores;
		//echo $idAuditoriaHistorico;

		try
		{
			if($oper=='INS')
			{

				//$sql="INSERT INTO sia_etapas (idProceso, idEtapa, nombre, orden, fase, usrAlta, fAlta, estatus) VALUES (:idProceso, :idEtapa, :nombre, :orden, :fase, :usrActual, getdate(), 'ACTIVO');";
			}else{
			
				$sql="UPDATE sia_auditoriasHistoricos SET idSector=:idSector, idSubsector=:idSubsector, idUnidad=:idUnidad, idTipoAuditoria=:idTipoAuditoria, usrModificacion=:usrActual, fModificacion=getdate() WHERE idAuditoriaHistorico=:idAuditoriaHistorico" ;

				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':idSector'=>$idSector, ':idSubsector'=>$idSubsector, ':idUnidad'=>$idUnidad, ':idTipoAuditoria'=>$idTipoAuditoria, ':usrActual'=>$usrActual, ':idAuditoriaHistorico'=>$idAuditoriaHistorico));
			}
			echo "OK";

		}catch (DBException $e) {
			print "¡Error!: " . $e->getMessage() . "<br/>";
			die();
		}
	});

	$app->get('/recuperaHistoricoAuditoriasByUnidad/:sector/:subsector/:unidad', function($sector, $subsector, $unidad)  use($app, $db) {

		try{

	    	$campos = array(':sector' => $sector, ':subsector' => $subsector, ':unidad' => $unidad);

			$sql = "SELECT ah.idAuditoriaHistorico, ah.anio, ah.idCuenta, ah.idAuditoria, ah.cveAuditoria, ah.idArea, ah.nombreArea, 
					ah.nombreAreaAnterior, ah.idTipoAuditoria, ah.nombreTipoAuditoria, ta.nombre nombreCatTipoAuditoria, ah.nombreObjeto
					FROM sia_auditoriasHistoricos ah LEFT JOIN sia_tiposauditoria ta ON ta.idTipoAuditoria = ah.idTipoAuditoria
					WHERE idSector = :sector AND idSubsector = :subsector AND idUnidad = :unidad 
					ORDER BY anio DESC;"; 

			$dbQuery = $db->prepare($sql);
			$dbQuery->execute( $campos );
			$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

			if(!$result){
				$app->halt(404, "NO SE ENCONTRÓ INFORMACIÓN DE HISTORIAL DE AUDITORIAS.");
			}else{
				echo json_encode($result);
			}
		
		}catch (PDOException $e) {
			print "¡Error!: " . $e->getMessage() . "<br/>";
			die();
		}
	});		


	$app->get('/recuperaCriteriosDiversosByUnidad/:sector/:subsector/:unidad', function($sector, $subsector, $unidad)  use($app, $db) {

		try{

	    	$campos = array(':sector' => $sector, ':subsector' => $subsector, ':unidad' => $unidad);

			$sql = "SELECT cu.idCuenta, ca.idCuenta, tc.nombre TipoCriterio, ca.fuente, ca.informacion, ca.fecInformacion 
			FROM sia_criteriosUnidades cu 
			LEFT JOIN sia_CriteriosAuxiliares ca ON ca.idCriterioAuxiliar = cu.idCriterioAuxiliar
			LEFT JOIN sia_tiposCriterios tc ON tc.idTipoCriterio = ca.idTipoCriterio
			WHERE cu.idSector = :sector AND cu.idSubSector = :subsector AND  cu.idUnidad = :unidad
			ORDER BY ca.fecInformacion DESC;"; 

			$dbQuery = $db->prepare($sql);
			$dbQuery->execute( $campos );
			$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

			if(!$result){
				$app->halt(404, "NO SE ENCONTRÓ INFORMACIÓN DE HISTORIAL DE AUDITORIAS.");
			}else{
				echo json_encode($result);
			}
		
		}catch (PDOException $e) {
			print "¡Error!: " . $e->getMessage() . "<br/>";
			die();
		}
	});		

 	//---- PROGRAMAS (AUDITORIAS) PARA JURIDICO  $autenticacionrole,

	$app->get('/programasJuridico(/:tipoAuditoria)',  function($tipoAuditoria='NULL')  use($app, $db) {
	//$app->get('/programasJuridico',  function()  use($app, $db) {
		$cuenta     = $_SESSION["idCuentaActual"];
		$area       = $_SESSION["idArea"];
		$empleado   = $_SESSION["idEmpleado"];
		$usrActual  = $_SESSION["idUsuario"];
		$global     = $_SESSION["usrGlobal"];
		$globalArea = $_SESSION["usrGlobalArea"];		

		if($tipoAuditoria==NULL){ $tipoAuditoria='FINANCIERA'; }

		//$aDatos     = array(':cuenta' => $cuenta, ':usrActual' => $usrActual);
		$aDatos     = array(':cuenta' => $cuenta, ':tipoAuditoria' => $tipoAuditoria);


		$sql="SELECT a.idAuditoria auditoria, e.nombre etapa, COALESCE(convert(varchar(20),a.clave),convert(varchar(20),a.idAuditoria)) claveAuditoria, isnull(a.clave,'') clave, ar.nombre area, dbo.lstSujetosByAuditoria(a.idAuditoria) sujeto, dbo.lstObjetosByAuditoria(a.idAuditoria) objeto, ta.nombre tipo, '0.00' avances " .
		" FROM sia_programas p " .
			" INNER JOIN sia_auditorias a on p.idCuenta=a.idCuenta and p.idPrograma=a.idPrograma " .
		" INNER JOIN sia_areas ar on a.idArea=ar.idArea " .
		" LEFT  JOIN sia_tiposauditoria ta on a.tipoAuditoria= ta.idTipoAuditoria " .
		" LEFT  JOIN sia_etapas e on e.idProceso =a.idProceso and e.idEtapa=a.idEtapa " .
		" WHERE a.idCuenta=:cuenta AND a.tipoAuditoria=:tipoAuditoria" .
		//" AND a.idEtapa in (Select idEtapa from sia_rolesetapas re inner join sia_usuariosroles ur on ur.idRol = re.idRol Where ur.idUsuario=:usrActual) ".		
		" ORDER BY a.idAuditoria desc";
				

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute($aDatos);		

		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS.");
		}else{
			$app->render('programasJuridico.php', $result);
		}
	})->name('programasJuridico');


	$app->get('/recuperaAuditoriasTipo/:tipo',  function($tipo)  use($app, $db) {
		 $app->redirect($app->urlFor('programasJuridico', array('tipo' => $tipo) ) );
	});

	// ---- FUNCIONES PARA EL PROGRAMA catCriteriosAuditorias.php --------------

	$app->get('/criteriosauditorias',  $autenticacionrole, function() use($app, $db){
		$cuenta = $_SESSION["idCuentaActual"];
		$area = $_SESSION["idArea"];
		
		$usrActual = $_SESSION["idUsuario"];
		$global = $_SESSION["usrGlobal"];

		$sql= "SELECT c.idTipoAuditoria, ta.nombre nombreTipoAuditoria, c.idCriterio, c.nombre nombreCriterio, c.estatus 
			FROM sia_criterios c INNER JOIN sia_tiposAuditoria ta ON ta.idTipoAuditoria = c.idTipoAuditoria 
			ORDER BY c.idTipoAuditoria, c.idCriterio";
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute();

		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		$app->render('catCriteriosAuditorias.php', $result);

	})->name('criteriosauditorias');

	/*
	$app->get('/obtenerCriterioByTipoAuditoria/:idTipoAuditoria/:idCriterio', function($idTipoAuditoria, $idCriterio)  use($app, $db) {

		try{

	    	$campos = array(':idTipoAuditoria' => $idTipoAuditoria, ':idCriterio' => $idCriterio);

			$sql = "SELECT c.idTipoAuditoria, ta.nombre nombreTipoAuditoria, c.idCriterio, c.nombre nombreCriterio, c.estatus 
					FROM sia_criterios c INNER JOIN sia_tiposAuditoria ta ON ta.idTipoAuditoria = c.idTipoAuditoria 
					WHERE c.idTipoAuditoria = :idTipoAuditoria AND c.idCriterio = :idCriterio;"; 

			$dbQuery = $db->prepare($sql);
			$dbQuery->execute( $campos );
			$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

			if(!$result){
				$app->halt(404, "NO SE ENCONTRÓ INFORMACIÓN DE CRITERIOS DE TIPOS DE AUDITORIAS.");
			}else{
				echo json_encode($result);
			}
		
		}catch (PDOException $e) {
			print "¡Error!: " . $e->getMessage() . "<br/>";
			die();
		}
	});		
	*/

	$app->get('/obtenerCriterioByIdTipoAuditoria/:idTipoAuditoria/:idCriterio/:conteo', function($idTipoAuditoria, $idCriterio, $conteo) use($app, $db) {

		try{

			if ($conteo=='SI'){
				$sql = "SELECT COUNT(*) total FROM sia_criterios WHERE idTipoAuditoria=:idTipoAuditoria AND idCriterio=:idCriterio";
			}else{
				$sql = "SELECT c.idTipoAuditoria, ta.nombre nombreTipoAuditoria, c.idCriterio, c.nombre nombreCriterio, c.estatus 
						FROM sia_criterios c INNER JOIN sia_tiposAuditoria ta ON ta.idTipoAuditoria = c.idTipoAuditoria 
						WHERE c.idTipoAuditoria = :idTipoAuditoria AND c.idCriterio = :idCriterio;"; 
			}

			$dbQuery = $db->prepare($sql);  
			$dbQuery->execute(array(':idTipoAuditoria'=> $idTipoAuditoria, ':idCriterio'=> $idCriterio));

			$result = $dbQuery->fetch(PDO::FETCH_ASSOC);

			if(!$result){
					$app->halt(404, "NO SE ENCONTRÓ INFORMACIÓN DE CRITERIOS DE TIPOS DE AUDITORIAS.");
			}else{
				echo json_encode($result);
			} 
		}catch (PDOException $e) {
			print "¡Error!: " . $e->getMessage() . "<br/>";
			die();
		}
	});

	$app->post('/guardar/CriterioTipoAuditoria', function()  use($app, $db) {
		$usrActual = $_SESSION ["idUsuario"];
		$cuenta = $_SESSION ["idCuentaActual"];

		$request         = $app->request;
		$oper            = $request->post('txtOperacion');
		$idTipoAuditoria = strtoupper($request->post('txtTipoAuditoria'));
		$idCriterio      = $request->post('txtIdCriterio');
		$nombre          = strtoupper($request->post('txtNombreCriterio'));
		$estatus         = $request->post('txtEstatus');


		$aDatos = array(':idTipoAuditoria' => $idTipoAuditoria, ':idCriterio' => $idCriterio, ':nombre' => $nombre, ':usrActual' => $usrActual, ':estatus' => $estatus );
		try
		{
			if($oper=='INS')
			{

				$sql="INSERT INTO sia_criterios (idTipoAuditoria, idCriterio, nombre, usrAlta, fAlta, estatus) VALUES (:idTipoAuditoria, :idCriterio, :nombre, :usrActual, getdate(), 'ACTIVO');";
			}else{
			
				$aDatos[':estatus'] = $estatus;

				$sql="UPDATE sia_criterios SET nombre=:nombre, usrModificacion=:usrActual, fModificacion=getdate(), estatus=:estatus WHERE idTipoAuditoria=:idTipoAuditoria AND idCriterio=:idCriterio " ;
			}

			$dbQuery = $db->prepare($sql);
			$dbQuery->execute($aDatos);

		}catch (DBException $e) {
			print "¡Error!: " . $e->getMessage() . "<br/>";
			die();
		}
		$app->redirect($app->urlFor('criteriosauditorias'));
	});

	////// Funciones para el módulo de consulta de ingresos

	$app->get('/cuentaingresos', function()   use($app, $db) {

		$sql="SELECT cue.idCuenta cuenta, cue.nombre, cue.anio, cue.fInicio inicio, cue.fFin fin ,cue.observaciones obser, cue.estatus FROM sia_cuentas cue
			 LEFT JOIN sia_ingresosOrdParaNoFin ingre on cue.idCuenta=ingre.idCuenta
			 GROUP BY cue.idCuenta, cue.nombre, cue.anio, cue.fInicio, cue.observaciones, cue.fFin, cue.estatus 
			 ORDER BY cue.idCuenta desc;";

		$dbQuery = $db->prepare($sql);		
	 	$dbQuery->execute();
	 	$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
	 	$app->render('cuentapublicaingre.php', $result);
	})->name('listacuentapublicaingresosHVS');


		//tabla de Cuenta Publica.
	$app->get('/tblCuentaByIngreso_HVS/:cuenta', function($cuenta)    use($app, $db) {

		// obtener la clave de la cuenta publica anterior
		$cuentaAnterior = "CTA-" . (string)((SUBSTR($cuenta, -4)) - 1);

		$sql="SELECT ine.idCuenta idCuenta, ic.clave, ic.clavePadre, ic.orden, ic.concepto, format(ine2.importeEstimado,'#,##0.00;($#,##0.0)') impEstimadoAnt, format(ine2.importeRegistrado,'#,##0.00;($#,##0.0)') impRegistradoAnt, format(ine.importeEstimado,'#,##0.00;($#,##0.0)') impEstimado, format(ine.importeRegistrado,'#,##0.00;($#,##0.0)') impRegistrado, format((ine.importeRegistrado - ine2.importeRegistrado),'#,##0.00;($#,##0.0)') variacion, case ine2.importeRegistrado when 0 then '0.00' else format( (((ine.importeRegistrado - ine2.importeRegistrado)/ine2.importeRegistrado))*100,'#,##0.00;($#,##0.0)') end variacionPor, case ine2.importeRegistrado when 0 then '0.00' else  format( ( (((ine.importeRegistrado / 1.0282)- ine2.importeRegistrado) / ine2.importeRegistrado) * 100 ), '#,##0.00;($#,##0.00)') end variacionReal 
			FROM sia_ingresosConceptos ic left join sia_ingresosnetos ine on ine.idIngresoConcepto = ic.idIngresoConcepto and ine.idCuenta = :cuenta left join sia_ingresosnetos ine2 on ine2.idIngresoConcepto = ic.idIngresoConcepto and ine2.idCuenta = :cuentaAnterior order by ic.clave, ic.orden;";

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':cuenta' => $cuenta, ':cuentaAnterior' => $cuentaAnterior));

		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	}); 

	$app->get('/tblAreasByUnidad/:idCentroGestor', function($idCentroGestor)  use($app, $db) 
	{
        $sql="SELECT au.idArea, a.nombre from sia_areasunidades au LEFT JOIN sia_areas a ON au.idArea = a.idArea WHERE au.idCuenta + au.idSector + au.idSubsector + au.idUnidad = :idCentroGestor ORDER BY au.idArea DESC;";

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':idCentroGestor' => $idCentroGestor));
		$result ['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});

	/*
	$app->get('/validarUnidadByArea/:area/:idCentroGestor',  function($area,$idCentroGestor)  use($app, $db) {
		
		$sql= "SELECT COUNT(*) TOTAL FROM sia_areasunidades WHERE idArea=:area and idCuenta+idSector+idSubsector+idUnidad=:idCentroGestor;";
		
		$dbQuery = $db->prepare($sql);		
		$dbQuery->execute(array(':area' => $area, ':idCentroGestor' => $idCentroGestor));
		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);
		echo json_encode($result);
	});
	*/

	$app->get('/lstUnidadesByAreaIf(/:idCuenta) /:idArea (/:bloque)', function($idCuenta=NULL, $idArea, $bloque=NULL)    use($app, $db) {
		$cuenta = $_SESSION["idCuentaActual"];

		if($idCuenta == NULL){ $idCuenta = $_SESSION["idCuentaActual"]; }
		if($bloque   == NULL){ $bloque = "ALL"; }
		
		$sql = "SELECT CONCAT(u.idCuenta, u.idSector, u.idSubsector, u.idUnidad) id, u.nombre texto FROM sia_unidades u ";

		if($bloque != 'OUT'){
			$sql .= " INNER JOIN sia_areasunidades au on u.idCuenta = au.idCuenta and au.idSector = u.idSector and u.idSubsector = au.idSubsector and au.idUnidad = u.idUnidad WHERE au.idCuenta = :idCuenta AND au.idArea = :idArea ORDER BY u.nombre";
		}else{
			$sql .= " LEFT OUTER JOIN sia_areasunidades au on u.idCuenta+u.idSector+u.idSubsector+u.idUnidad = au.idCuenta + au.idSector + au.idSubsector+au.idUnidad WHERE au.idCuenta+au.idSector+au.idSubsector+au.idUnidad = NULL AND au.idCuenta = :idCuenta AND au.idArea = :idArea ORDER BY u.nombre";
 		}

		$dbQuery = $db->prepare($sql);		
		$dbQuery->execute(array(':idCuenta' => $idCuenta, ':idArea' => $idArea));
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		echo json_encode($result);	

	});

	$app->get('/lstUnidadesByCuentaByArea(/:idCuenta)/:idArea', function($idCuenta=NULL, $idArea)    use($app, $db) {

		$sql = "SELECT CASE WHEN (au.asignado IS NULL) THEN 'NO' ELSE au.asignado END asignado, u.idCuenta, u.idSector, u.idSubsector, u.idUnidad, u.nombre nombreUnidad";

		if($idCuenta == NULL)
		{ 
			$idCuenta = $_SESSION["idCuentaActual"]; 
			$sql .= ", CONCAT(u.idSector, u.idSubsector, u.idUnidad) centroGestor ";

		}else{

			$sql .= ", CONCAT(u.idCuenta, u.idSector, u.idSubsector, u.idUnidad) centroGestor ";
		}
		
		$sql .= " FROM sia_unidades u LEFT JOIN ( select idCuenta, idSector, idSubsector, idUnidad, 'SI' asignado 
                                from sia_areasunidades where idCuenta = :idCuenta and idArea = :idArea) au
                    ON u.idCuenta = :idCuenta1 AND u.idSector = au.idSector AND u.idSubsector = au.idSubsector and u.idUnidad = au.idUnidad 
				WHERE u.idCuenta = :idCuenta2 ORDER BY RTRIM(LTRIM(u.nombre)) asc";

		$dbQuery = $db->prepare($sql);		
		$dbQuery->execute(array(':idCuenta' => $idCuenta, ':idArea' => $idArea, ':idCuenta1' => $idCuenta, ':idCuenta2' => $idCuenta));
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		echo json_encode($result);	

	});

	// ---- FUNCIONES PARA EL PROGRAMA CatParametros.php --------------

	$app->get('/parametros',  $autenticacionrole, function() use($app, $db){
	//$app->get('/parametros',  function() use($app, $db){
		$cuenta = $_SESSION["idCuentaActual"];
		$area = $_SESSION["idArea"];
		
		$usrActual = $_SESSION["idUsuario"];
		$global = $_SESSION["usrGlobal"];

		$sql= "SELECT idParametro, clave, descripcion ,valor, estatus FROM sia_parametros ORDER BY clave";
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute();

		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		$app->render('CatParametros.php', $result);

	})->name('parametros');



	$app->get('/obtenerParametro/:idParametro(/:conteo)', function($idParametro, $conteo=NULL) use($app, $db) {

		try{

			if ($conteo!=NULL){
				$sql = "SELECT COUNT(*) total FROM sia_Parametros WHERE idParametro=:idParametro;";
			}else{
				$sql = "SELECT idParametro, clave, valor, descripcion, estatus FROM sia_parametros WHERE idParametro=:idParametro;"; 
			}

			$dbQuery = $db->prepare($sql);  
			$dbQuery->execute(array(':idParametro'=> $idParametro));

			$result = $dbQuery->fetch(PDO::FETCH_ASSOC);

			if(!$result){
					$app->halt(404, "NO SE ENCONTRÓ INFORMACIÓN DEL PARAMETRO INDICADO. " + $idParametro);
			}else{
				echo json_encode($result);
			} 
		}catch (PDOException $e) {
			print "¡Error!: " . $e->getMessage() . "<br/>";
			die();
		}
	});

	$app->get('/validaExisteParametroByClave/:clave', function($clave) use($app, $db) {

		try{

			$sql = "SELECT idParametro FROM sia_Parametros WHERE clave=:clave ;";

			$dbQuery = $db->prepare($sql);  
			$dbQuery->execute(array(':clave'=> $clave));

			$result = $dbQuery->fetch(PDO::FETCH_ASSOC);
			echo json_encode($result);
			/*
			if(!$result){
					$app->halt(404, "NO SE ENCONTRÓ INFORMACIÓN DE LA CLAVE DE PARÁMETRO INDICADA. " + $clave);
			}else{
				echo json_encode($result);
			} 
			*/
		}catch (PDOException $e) {
			print "¡Error!: " . $e->getMessage() . "<br/>";
			die();
		}
	});



	$app->post('/guardar/Parametro', function()  use($app, $db) {
		$usrActual = $_SESSION ["idUsuario"];
		$cuenta = $_SESSION ["idCuentaActual"];

		$request     = $app->request;

		$oper        = $request->post('txtOperacion');
		$idParametro = $request->post('txtIdParametro');
		$clave       = strtoupper($request->post('txtClaveParametro'));
		$descripcion = $request->post('txtDescripcionParametro');
		$valor       = $request->post('txtValorParametro');
		$estatus     = $request->post('txtEstatus');

		//echo " oper>>$oper idParametro>>$idParametro clave>>$clave descripcion>>$descripcion  valor>>$valor estatus>>$estatus";

		try
		{
			if($oper=='INS')
			{
				$sql="INSERT INTO sia_parametros (clave, valor, descripcion, usrAlta, fAlta) VALUES (:clave, :valor, :descripcion, :usrAlta, GETDATE() );";
				$dbQuery = $db->prepare($sql);

				$dbQuery->execute(array(':clave'=>$clave, ':valor'=>$valor, ':descripcion'=>$descripcion, ':usrAlta'=>$usrActual ));
			
			}else{
			
				$sql="UPDATE sia_Parametros SET clave=:clave, valor=:valor, descripcion=:descripcion, usrModificacion=:usrModificacion, fModificacion=GETDATE(), estatus=:estatus WHERE idParametro=:idParametro;";
				$dbQuery = $db->prepare($sql);

				$dbQuery->execute(array(':clave'=>$clave, ':valor'=>$valor, ':descripcion'=>$descripcion, ':usrModificacion'=>$usrActual, ':estatus'=>$estatus, ':idParametro'=>$idParametro ));
			}

		}catch (DBException $e) {
			print "¡Error!: " . $e->getMessage() . "<br/>";
			die();
		}
		$app->redirect($app->urlFor('parametros'));
	});

	//-------------------------------------------------------------------------------------------------------
	//    RUTINAS DEL CATALOGO DE PUESTOS PARA EL ÁREA DE JURÍDICO
	//-------------------------------------------------------------------------------------------------------

		$app->get('/catPuestosJuridico',  $autenticacionrole, function()  use ($app, $db) {

		//$cuenta = $_SESSION["idCuentaActual"];
		//$area = $_SESSION["idArea"];
		//$usrActual = $_SESSION["idUsuario"];
		//$global = $_SESSION["usrGlobal"];

		$sql="SELECT idPuestoJuridico, idArea, rpe, saludo, nombre, paterno, materno, siglas, recepcion, puesto, titular, estatus FROM sia_PuestosJuridico ORDER BY idArea, nombre, paterno, materno  DESC;";
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute();
		$result ['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS.");
		}else{
			$app->render('catPuestosJuridico.php', $result);
		}
	})->name('catPuestosJuridico');


	$app->get('/obtenerPuestoJuridico/:idPuesto', function($idPuesto) use($app, $db) {

		try{

			$sql = "SELECT idPuestoJuridico, idArea, rpe, saludo, nombre, paterno, materno, siglas, recepcion, puesto, titular, estatus FROM sia_PuestosJuridico WHERE idPuestoJuridico=:idPuesto;";

			$dbQuery = $db->prepare($sql);  
			$dbQuery->execute(array(':idPuesto'=> $idPuesto));

			$result = $dbQuery->fetch(PDO::FETCH_ASSOC);

			if(!$result){
					$app->halt(404, "NO SE ENCONTRÓ INFORMACIÓN DEL PUESTO JURÍDICO INDICADO. " + $idPuesto);
			}else{
				echo json_encode($result);
			} 
		}catch (PDOException $e) {
			print "¡Error!: " . $e->getMessage() . "<br/>";
			die();
		}
	});

	$app->get('/validaExistePuestoJuridico/:rpe', function($rpe) use($app, $db) {

		try{

			$sql = "SELECT rpe FROM sia_PuestosJuridico WHERE rpe=:rpe ;";

			$dbQuery = $db->prepare($sql);  
			$dbQuery->execute(array(':rpe'=> $rpe ));

			$result = $dbQuery->fetch(PDO::FETCH_ASSOC);
			echo json_encode($result);

		}catch (PDOException $e) {
			print "¡Error!: " . $e->getMessage() . "<br/>";
			die();
		}
	});

	$app->post('/guardar/PuestosJuridico', function()  use($app, $db) {
		$usrActual = $_SESSION ["idUsuario"];
		$cuenta = $_SESSION ["idCuentaActual"];

		$request     = $app->request;

		$oper        = $request->post('txtOperacion');
		$idPuesto    = $request->post('txtIdPuesto');
		$idArea      = $request->post('txtIdArea');
		$rpe         = $request->post('txtRpe');
		$saludo      = strtoupper($request->post('txtSaludo'));
		$nombre      = strtoupper($request->post('txtNombre'));
		$paterno     = strtoupper($request->post('txtPaterno'));
		$materno     = strtoupper($request->post('txtMaterno'));
		$siglas      = strtoupper($request->post('txtSiglas'));

		$recepcion   = $request->post('txtAtiendeRecepcion');
		$puesto      = $request->post('txtPuesto');
		$titular     = $request->post('txtTitularArea');
		$estatus     = $request->post('txtEstatus');

		//echo " oper>>$oper idPuesto>>$idPuesto  idArea>>$idArea  rpe>>$rpe  saludo>>$saludo  nombre>>$nombre  paterno>>$paterno  materno>>$materno  siglas>>$siglas  recepcion>>$recepcion  puesto>>$puesto  titular>>$titular estatus>>$estatus";

		try
		{
			if($oper=='INS')
			{
				$sql="INSERT INTO sia_PuestosJuridico (idArea, rpe, saludo, nombre, paterno, materno, siglas, recepcion, titular, puesto, usrAlta, fAlta) VALUES (:idArea, :rpe, :saludo, :nombre, :paterno, :materno, :siglas, :recepcion, :titular, :puesto, :usrAlta, GETDATE() );";
				$dbQuery = $db->prepare($sql);

				$dbQuery->execute(array(':idArea'=>$idArea, ':rpe'=>$rpe, ':saludo'=>$saludo, ':nombre'=>$nombre, ':paterno'=>$paterno, ':materno'=>$materno, ':siglas'=>$siglas, ':recepcion'=>$recepcion, ':titular'=>$titular, ':puesto'=>$puesto, ':usrAlta'=>$usrActual ));
			
			}else{
			
				$sql="UPDATE sia_PuestosJuridico SET idArea=:idArea, rpe=:rpe, saludo=:saludo, nombre=:nombre, paterno=:paterno, materno=:materno, siglas=:siglas, recepcion=:recepcion, puesto=:puesto, titular=:titular, usrModificacion=:usrModificacion, fModificacion=GETDATE(), estatus=:estatus WHERE idPuestoJuridico=:idPuesto;";
				$dbQuery = $db->prepare($sql);

				$dbQuery->execute(array(':idArea'=>$idArea, ':rpe'=>$rpe, ':saludo'=>$saludo, ':nombre'=>$nombre, ':paterno'=>$paterno, ':materno'=>$materno, 'siglas'=>$siglas, ':recepcion'=>$recepcion, ':puesto'=>$puesto, ':titular'=>$titular, ':usrModificacion'=>$usrActual, ':estatus'=>$estatus, ':idPuesto'=>$idPuesto ));
			}

		}catch (DBException $e) {
			print "¡Error!: " . $e->getMessage() . "<br/>";
			die();
		}
		$app->redirect($app->urlFor('catPuestosJuridico'));
	});

	//-------------------------------------------------------------------------------------------------------
	//    RUTINAS DEL CATALOGO DE FOLIOS PARA EL ÁREA DE JURÍDICO
	//-------------------------------------------------------------------------------------------------------

	$app->get('/catFoliosJuridico',  $autenticacionrole, function()  use ($app, $db) {

		$cuenta = $_SESSION["idCuentaActual"];
		$area = $_SESSION["idArea"];
		$usrActual = $_SESSION["idUsuario"];
		$global = $_SESSION["usrGlobal"];

		$sql="SELECT fj.idFolioJuridico, fj.idCuenta, fj.idArea, a.nombre area, fj.idTipoDocumento, td.nombre tipoDocumento, fj.folio, fj.observaciones, fj.estado, fj.usrAutorizador, fj.estatus FROM sia_FoliosJuridico fj left join sia_areas a on a.idArea = fj.idArea left join sia_tiposdocumentos td on td.idTipoDocto = fj.idTipoDocumento and td.tipo = 'JURIDICO' ORDER BY fj.folio DESC;";

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute();
		$result ['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS.");
		}else{
			$app->render('catFoliosJuridico.php', $result);
		}
	})->name('catFoliosJuridico');


	$app->get('/obtenerFolioJuridico/:idFolioJuridico', function($idFolioJuridico) use($app, $db) {

		try{

			$sql = "SELECT fj.idFolioJuridico, fj.idCuenta, fj.idArea, fj.idTipoDocumento, fj.folio, isnull(fj.observaciones,'') observaciones , fj.estado, fj.usrAutorizador, CONCAT(u.nombre,' ',u.paterno,' ',u.materno) nombreAutorizador, fj.estatus FROM sia_FoliosJuridico fj left join sia_usuarios u ON u.idUsuario = fj.usrAutorizador WHERE fj.idFolioJuridico=:idFolioJuridico;";

			$dbQuery = $db->prepare($sql);  
			$dbQuery->execute(array(':idFolioJuridico'=> $idFolioJuridico));

			$result = $dbQuery->fetch(PDO::FETCH_ASSOC);

			if(!$result){
					$app->halt(404, "NO SE ENCONTRÓ INFORMACIÓN DEL FOLIO JURÍDICO INDICADO. " + $idFolioJuridico);
			}else{
				echo json_encode($result);
			} 
		}catch (PDOException $e) {
			print "¡Error!: " . $e->getMessage() . "<br/>";
			die();
		}
	});

	$app->get('/validaExisteFolioJuridico/:idDocumento/:folio', function($idDocumento, $folio) use($app, $db) {

		try{

			$sql = "SELECT folio FROM sia_FoliosJuridico WHERE idTipoDocumento =:idDocumento AND folio=:folio ;";

			$dbQuery = $db->prepare($sql);  
			$dbQuery->execute(array(':idDocumento'=> $idDocumento, ':folio'=> $folio));

			$result = $dbQuery->fetch(PDO::FETCH_ASSOC);
			echo json_encode($result);

		}catch (PDOException $e) {
			print "¡Error!: " . $e->getMessage() . "<br/>";
			die();
		}
	});

	$app->get('/guardar/FolioJuridicoReservados/:idTipoDocumento/:folio', function($idTipoDocumento, $folio)  use($app, $db) {
		$usrActual = $_SESSION ["idUsuario"];
		$cuenta = $_SESSION ["idCuentaActual"];
		$estado = "RESERVADO";

		try
		{
			$sql="INSERT INTO sia_FoliosJuridico (idCuenta, idTipoDocumento, folio, estado, usrAlta) VALUES (:idCuenta, :idTipoDocumento, :folio, :estado, :usrAlta);";
			$dbQuery = $db->prepare($sql);

			$dbQuery->execute(array(':idCuenta'=>$cuenta, ':idTipoDocumento'=>$idTipoDocumento, ':folio'=>$folio, ':estado'=>$estado, ':usrAlta'=>$usrActual ));

			echo("OK");

		}catch (DBException $e) {
			print "¡Error!: " . $e->getMessage() . "<br/>";
			die();
		}
	});

	$app->post('/guardar/FolioJuridico', function()  use($app, $db) {
		$usrActual = $_SESSION ["idUsuario"];
		$cuenta = $_SESSION ["idCuentaActual"];

		$request            = $app->request;

		$oper               = $request->post('txtOperacion');
		$idFolioJuridico    = $request->post('txtIdFolioJuridico');
		$idArea             = $request->post('txtIdArea');
		$idTipoDocumento    = $request->post('txtIdDocumento');
		//$idSubTipoDocumento = $request->post('txtIdSubDocumento');
		$folio              = $request->post('txtFolio');
		$observaciones      = $request->post('txtObservaciones');
		$estado             = $request->post('txtEstado');
		$usrAutorizador     = $request->post('txtUsrAutorizador');
		$estatus            = $request->post('txtEstatus');

		//echo "oper>> $oper idFolioJuridico>> $idFolioJuridico idArea>> $idArea idTipoDocumento>> $idTipoDocumento folio>> $folio observaciones>> $observaciones estado>> $estado usrAutorizador>> $usrAutorizador estatus>> $estatus";

		try
		{
			if($oper=='INS')
			{
				$sql="INSERT INTO sia_FoliosJuridico (idCuenta, idArea, idTipoDocumento, folio, observaciones, usrAutorizador, usrAlta) VALUES (:idCuenta, :idArea, :idTipoDocumento, :folio, :observaciones, :usrAutorizador, :usrAlta);";
				$dbQuery = $db->prepare($sql);

				$dbQuery->execute(array(':idCuenta'=>$cuenta,':idArea'=>$idArea, ':idTipoDocumento'=>$idTipoDocumento, ':folio'=>$folio, ':observaciones'=>$observaciones, ':usrAutorizador'=>$usrAutorizador, ':usrAlta'=>$usrActual ));
			
			}else{
			
				$sql="UPDATE sia_FoliosJuridico SET idArea=:idArea, idTipoDocumento=:idTipoDocumento, folio=:folio, observaciones=:observaciones, estado=:estado, usrAutorizador=:usrAutorizador, usrModificacion=:usrModificacion, fModificacion=GETDATE(), estatus=:estatus WHERE idFolioJuridico=:idFolioJuridico;";
				$dbQuery = $db->prepare($sql);

				$dbQuery->execute(array(':idArea'=>$idArea, ':idTipoDocumento'=>$idTipoDocumento, ':folio'=>$folio, ':observaciones'=>$observaciones, ':estado'=>$estado, ':usrAutorizador'=>$usrAutorizador, ':usrModificacion'=>$usrActual, ':estatus'=>$estatus, ':idFolioJuridico'=>$idFolioJuridico ));
			}

		}catch (DBException $e) {
			print "¡Error!: " . $e->getMessage() . "<br/>";
			die();
		}
		$app->redirect($app->urlFor('catFoliosJuridico'));
	});

	$app->get('/lstAreasByAreaSuperior/:idAreaSuperior', function($idAreaSuperior) use($app, $db) {

		try{

			$sql = "SELECT idArea id, nombre texto FROM sia_areas WHERE idAreaSuperior=:idAreaSuperior;";

			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':idAreaSuperior'=> $idAreaSuperior));
			$result ['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
			if(!$result){
					$app->halt(404, "NO SE ENCONTRÓ INFORMACIÓN DE LAS AREAS PARA JURÍDICO. " + $idAreaSuperior);
			}else{
				echo json_encode($result);
			} 
		}catch (PDOException $e) {
			print "¡Error!: " . $e->getMessage() . "<br/>";
			die();
		}
	});

	$app->get('/lstTiposDocumentosByTipo/:tipo', function($tipo) use($app, $db) {

		try{

			$sql = "select idTipoDocto id, nombre texto FROM sia_tiposdocumentos where estatus = 'ACTIVO' and tipo = :tipo;";

			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':tipo'=> $tipo));
			$result ['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
			if(!$result){
					$app->halt(404, "NO SE ENCONTRÓ INFORMACIÓN DE LOS TIPOS DE DOCUMENTOS. " + $tipo);
			}else{
				echo json_encode($result);
			} 
		}catch (PDOException $e) {
			print "¡Error!: " . $e->getMessage() . "<br/>";
			die();
		}
	});

	$app->get('/lstSubDocumentosByTipoDocto/:idTipoDocto', function($idTipoDocto) use($app, $db) {

		try{

			$sql = "select idSubTipoDocumento id, nombre texto FROM sia_catSubTiposDocumentos where estatus = 'ACTIVO' and idTipoDocto = :idTipoDocto;";

			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':idTipoDocto'=> $idTipoDocto));
			$result ['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
			if(!$result){
					$app->halt(404, "NO SE ENCONTRÓ INFORMACIÓN DE LOS SUB-TIPOS DE DOCUMENTOS DEL TIPO DOCUMENTO: " + $idTipoDocto);
			}else{
				echo json_encode($result);
			} 
		}catch (PDOException $e) {
			print "¡Error!: " . $e->getMessage() . "<br/>";
			die();
		}
	});

	$app->get('/recuperaUltimoFolioJuridico/:idTipoDocumento', function($idTipoDocumento) use($app, $db) {

		try{
			
			$sql = "SELECT folio, convert(VARCHAR, convert(date, fAlta,101),101) fechaFolio FROM sia_FoliosJuridico WHERE folio = (SELECT max(folio) folio FROM sia_FoliosJuridico WHERE  idTipoDocumento = :idTipoDocumento and estatus = 'ACTIVO');";

			$dbQuery = $db->prepare($sql);
			$dbQuery->execute( array(":idTipoDocumento"=>$idTipoDocumento) );
			$result = $dbQuery->fetch(PDO::FETCH_ASSOC);

			if(!$result){
				$result = array("folio"=>null, "fechaFolio"=>null);
				echo json_encode(array( "success" => true, "data" => $result ));  
				//$app->halt(404, "NO SE LOCALIZÓ EL ÚLTIMO FOLIO PARA EL ÁREA JURÍDICA: " );
			}else{
				echo json_encode($result);
			} 
		}catch (PDOException $e) {
			print "¡Error!: " . $e->getMessage() . "<br/>";
			die();
		}
		
	});

?> 