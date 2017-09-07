<?php
	session_start();
	require 'src/conexion.php';
	require 'Slim/Slim.php';
	
	include 'Excel/simplexlsx.class.php';
	include 'midle/middle.php';


	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();

	require 'hector.php';
	require 'arian.php';
	require 'cota.php';
	require 'carlos.php';
	

	define("MAIN_ACCESS", true);

	$app->config(array('debug'=>true, 'templates.path'=>'./',));

	try{
		$db = new PDO("sqlsrv:Server={$hostname}; Database={$database}", $username, $password );
	}catch (PDOException $e) {
		print "ERROR: " . $e->getMessage();
		die();
	}
	if(!isset($_SESSION["logueado"])) $_SESSION["logueado"]=0;
	if(!isset($_SESSION["idUsuario"])) $_SESSION["idUsuario"]=0;
	if(!isset($_SESSION["idCuentaActual"])) $_SESSION["idCuentaActual"]=0;
	if(!isset($_SESSION["sCuentaActual"])) $_SESSION["sCuentaActual"]=0;
	if(!isset($_SESSION["aniocuenta"])) $_SESSION["aniocuenta"]=0;
	

	
	

	
	

	//ACCESO AL SISTEMA

	//Acceso al sitio
	$app->get('/', function() use($app){
		if($_SESSION["logueado"]==1){
			$result= array('idUsuario' => $_SESSION["idUsuario"] , 'nombre' => $_SESSION["sUsuario"] );
			$app->redirect($app->urlFor('listaDashboard'));
			
		}else{
			$app->render('login.html');
		}
	})->name('inicio');

	//Login
	$app->post('/login', function()  use($app, $db) {
		$request=$app->request;
		$cuenta = $request->post('txtUsuario');
		$pass = $request->post('txtPass');
		$latitud = $request->post('txtLatitud');
		$longitud = $request->post('txtLongitud');

		
		$sql = "SELECT u.idUsuario, u.idEmpleado, CONCAT(u.saludo,' ',u.nombre, ' ', u.paterno, ' ', u.materno) nombre, COALESCE(u.idArea, e.idArea) idArea, p.nombre sPlaza " .
		"FROM sia_usuarios u " .
		"left join sia_empleados e on u.idEmpleado=e.idEmpleado " . 
		"left join sia_plazas p on e.idArea = p.idArea and e.idNivel = p.idNivel and e.idNombramiento = p.idNombramiento and e.idPuesto = p.idPuesto and e.idPlaza = p.idPlaza " .
		" WHERE usuario=:cuenta and pwd=:pass";
		
		
		//$dbQuery = $db->prepare("SELECT idUsuario, rpe, CONCAT(nombre, ' ', paterno, ' ', materno) nombre FROM sia_usuarios WHERE usuario=:cuenta and pwd=:pass");
		$dbQuery = $db->prepare($sql);
		
		$dbQuery->execute(array(':cuenta' => $cuenta, ':pass' => $pass));
		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);
		
		$_SESSION["usrGlobal"]="NO";
		$_SESSION["usrGlobalArea"]="NO";
		if($result){

		$_SESSION["logueado"] =1;
		$_SESSION["idUsuario"] =$result['idUsuario'];
		$_SESSION["sUsuario"] =$result['nombre'];
		
		$_SESSION["idEmpleado"] =$result['idEmpleado'];
		$_SESSION["idArea"] =$result['idArea'];
		$_SESSION["sPlaza"] =$result['sPlaza'];


		//Obtener datos generales
		//$_SESSION["idEntidad"] =9;
		$sql="SELECT idEntidad  FROM sia_configuracion";
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute();
		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);
		if($result){
			$_SESSION["idEntidad"] = $result['idEntidad'];
		}else{
			$_SESSION["idEntidad"] =99;
		}

		//Registrar acceso
		$usrActual = $_SESSION["idUsuario"];
		$sql="INSERT INTO sia_accesos (idUsuario, fIngreso, latitud, longitud) VALUES(:usrActual, getdate(), :latitud, :longitud);";
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':usrActual' => $usrActual, ':latitud'=>$latitud, ':longitud'=>$longitud ));

		$sql="SELECT idAcceso FROM sia_accesos WHERE idUsuario= :usrActual ORDER BY idAcceso desc ";
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':usrActual'=> $usrActual));
		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);
		$id = $result['idAcceso'];


		//Obtener la campaña actual
		$sql = 	"SELECT c.idCuenta id, c.nombre, c.anio an FROM sia_cuentas c inner join sia_cuentausuario cu  on c.idCuenta=cu.idCuenta WHERE cu.predeterminada='SI' and cu.idUsuario =:idx order by c.idCuenta desc";
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':idx'=>$_SESSION["idUsuario"]));
		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);

		if($result){
			$_SESSION["sCuentaActual"] 		=$result['nombre'];
			$_SESSION["idCuentaActual"] 	=$result['id'];
			$_SESSION["idCuentaVariable"] 	=$result['id'];
			$_SESSION["aniocuenta"] 		=$result['an'];
			$tmpCta=$result['id'];

			//Obtener el PGA actual
			$sql = 	"SELECT idPrograma FROM sia_programas WHERE idCuenta=:cta";
			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':cta'=> $tmpCta ));
			$result = $dbQuery->fetch(PDO::FETCH_ASSOC);
			$_SESSION["idProgramaActual"] 	=$result['idPrograma'];
			
			
				//Determinar si es rol global
				$sql = 	"SELECT count(*) nRegistros  FROM sia_usuariosroles WHERE idUsuario=:usr and idRol='GLOBAL'";
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':usr'=> $usrActual ));
				$result = $dbQuery->fetch(PDO::FETCH_ASSOC);
				if($result['nRegistros']>0)$_SESSION["usrGlobal"]="SI";		
				
				//Determinar si es rol global
				$sql = 	"SELECT count(*) nRegistros  FROM sia_usuariosroles WHERE idUsuario=:usr and idRol='GLOBAL-AREA'";
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':usr'=> $usrActual ));
				$result = $dbQuery->fetch(PDO::FETCH_ASSOC);
				if($result['nRegistros']>0)$_SESSION["usrGlobalArea"]="SI";		


				
		}else{
			$_SESSION["sCuentaActual"] 		="";
			$_SESSION["idCuentaActual"] 	="";
			$_SESSION["aniocuenta"]         ="";
			$_SESSION["idProgramaActual"] 	="***";
			$_SESSION["idCuentaVariable"] = "";
			$_SESSION["idUsuario"] = 0;


		}
		$app->redirect($app->urlFor('listaDashboard'));
		
		
		}else{
			//$app->halt(404, "Usuario: " . $cuenta . " Pass: " . $pass . "<br>USUARIO NO ENCONTRADO.");
			$_SESSION["logueado"] =0;
			//$app->render('login.html');
			$app->render('temporal.html');
			
			
		}
	})->name('login');

	$app->get('/cerrar', function() use($app, $db){
		if(!isset($_SESSION["idUsuario"])){
			$sql="UPDATE sia_accesos SET fEgreso=getdate(), estatus='INACTIVO' WHERE idUsuario=:usrActual AND estatus='ACTIVO';";
			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':usrActual'=>$_SESSION["idUsuario"]));
			session_destroy();		
		}else{
			session_destroy();
		}
		$app->redirect($app->urlFor('inicio'));
	})->name('cerrar');


	

	$app->get('/dashboard',$dashboard ,function()  use($app, $db) {
		$cuenta = $_SESSION["idCuentaActual"];
		$area = $_SESSION["idArea"];
		$rpe = $_SESSION["idEmpleado"];
		$usrActual = $_SESSION["idUsuario"];
		$global = $_SESSION["usrGlobal"];
		$globalArea = $_SESSION["usrGlobalArea"];
		/*
		$sql="SELECT idCuenta cuenta, idPrograma programa, idAuditoria auditoria,  tipoAuditoria tipo, idArea area, idSector sector, idSubsector subSector, idUnidad unidad, idObjeto objeto, objetivo, alcance, justificacion, tipoPresupuesto, acompanamiento, idProceso proceso, idEtapa etapa  FROM sia_auditorias  WHERE idAuditoria=:id ";
		*/
		if ($global=="SI"){

			$sql="SELECT a.idAuditoria auditoria, COALESCE(a.clave, convert(varchar, a.idAuditoria)) claveAuditoria, ar.nombre area, dbo.lstSujetosByAuditoria(a.idAuditoria) sujeto,  dbo.lstObjetosByAuditoria(a.idAuditoria) objeto, ta.nombre tipo, '0.00' avances " .
			"FROM sia_programas p " .
  			"INNER JOIN sia_auditorias a on p.idCuenta=a.idCuenta and p.idPrograma=a.idPrograma " .
			"LEFT JOIN sia_areas ar on a.idArea=ar.idArea " .
			"inner join sia_tiposauditoria ta on a.tipoAuditoria=ta.idTipoAuditoria " .
			"Where a.idCuenta=:cuenta  " .

			//Linea nueva para filtrar por rol-etapa
			"and a.idEtapa in (Select idEtapa from sia_rolesetapas re inner join sia_usuariosroles ur on ur.idRol = re.idRol  Where ur.idUsuario=:usrActual) ".			
			"ORDER BY  a.idAuditoria desc ";
				
			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':cuenta' => $cuenta, ':usrActual' => $usrActual));
			
		}else{		
		
			
			if ($globalArea=="SI"){
			
				$sql="SELECT a.idAuditoria auditoria, COALESCE(a.clave, convert(varchar, a.idAuditoria)) claveAuditoria, ar.nombre area, dbo.lstSujetosByAuditoria(a.idAuditoria) sujeto, dbo.lstObjetosByAuditoria(a.idAuditoria) objeto, ta.nombre tipo " .
				"FROM sia_programas p " .
				"INNER JOIN sia_auditorias a on p.idCuenta=a.idCuenta and p.idPrograma=a.idPrograma " .
				"LEFT JOIN sia_areas ar on a.idArea=ar.idArea " .
				"inner join sia_tiposauditoria ta on a.tipoAuditoria=ta.idTipoAuditoria " .
				"Where a.idCuenta=:cuenta  and a.idArea=:area And a.clave is not null
					and a.idEtapa in (
						Select idEtapa from sia_rolesetapas re inner join sia_usuariosroles ur on ur.idRol = re.idRol  Where ur.idUsuario=:usrActual
					) " .
				"ORDER BY a.idAuditoria desc ";
					
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':cuenta' => $cuenta, ':area' => $area, ':usrActual' => $usrActual));	
			}
			else{

				$sql="SELECT a.idAuditoria auditoria, COALESCE(a.clave, convert(varchar, a.idAuditoria)) claveAuditoria, ar.nombre area, dbo.lstSujetosByAuditoria(a.idAuditoria) sujeto, dbo.lstObjetosByAuditoria(a.idAuditoria) objeto, ta.nombre tipo  
				FROM sia_programas p 
				INNER JOIN sia_auditorias a on p.idCuenta=a.idCuenta and p.idPrograma=a.idPrograma 
				INNER JOIN sia_areas ar on a.idArea=ar.idArea 
		        INNER JOIN  sia_auditoriasauditores aa ON a.idCuenta=aa.idCuenta and a.idAuditoria=aa.idAuditoria
				LEFT JOIN sia_tiposauditoria ta on a.tipoAuditoria= ta.idTipoAuditoria
				WHERE a.idCuenta=:cuenta and aa.idAuditor=:auditor And a.clave is not null and  a.idEtapa in (Select idEtapa from sia_rolesetapas re inner join sia_usuariosroles ur on ur.idRol = re.idRol  Where ur.idUsuario=:usrActual) 
				ORDER BY a.idAuditoria desc";

				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':cuenta' => $cuenta,  ':auditor' => $rpe,  ':usrActual' => $usrActual));				
			}	
		}
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		$app->render('dashboard.php', $result);
	})->name('listaDashboard');




	$app->get('/catFolios', function()  use ($app) {
		$app->render('catFolios.php');
	});

	$app->get('/catDocumentos', function()  use ($app) {
		$app->render('catDocumentos.php');
	});

	$app->get('/acciones', function()  use ($app) {
		$app->render('acciones.php');
	});

	$app->get('/catObjetos', function()  use ($app) {
		$app->render('catObjetos.php');
	});

	$app->get('/catTiposAuditorias', $autenticacionrole ,function()  use ($app, $db) {
		$sql="SELECT idTipoAuditoria id, nombre, estatus FROM sia_tiposauditoria ORDER BY nombre";
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute();
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS.");
		}else{
			$app->render('catTiposAuditorias.php', $result);
		}
	})->name('catTiposAuditorias');


	$app->get('/catCuentas',$autenticacionrole ,function()  use ($app, $db) {
		$sql="SELECT idCuenta id, nombre, fInicio inicio, fFin fin, estatus FROM sia_cuentas ORDER BY anio";
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute();
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS.");
		}else{
			$app->render('catCuentas.php', $result);
		}
	})->name('catCuentas');

	$app->get('/lstCuentasByID/:id', function($id)    use($app, $db) {
		$sql="SELECT idCuenta id, anio, isnull(nombre,'') nombre, fInicio inicio, fFin fin,  isnull(observaciones,'') observaciones, isnull(archivoOriginal,'') archivoOriginal, isnull(archivoFinal,'') archivoFinal, isnull(archivoOriginalIngreso,'') archivoOriginalIngreso, isnull(archivoFinalIngreso,'') archivoFinalIngreso, estatus FROM sia_cuentas  Where idCuenta=:id ORDER BY anio";
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':id' => $id));
		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});



	$app->get('/lstAuditoriaByID/:id', function($id)    use($app, $db) {
		$cuenta = $_SESSION["idCuentaActual"];
		$area = $_SESSION["idArea"];
		
		$usrActual = $_SESSION["idUsuario"];
		$global = $_SESSION["usrGlobal"];
		/*
		$sql="SELECT idCuenta cuenta, idPrograma programa, idAuditoria auditoria,  tipoAuditoria tipo, idArea area, idSector sector, idSubsector subSector, idUnidad unidad, idObjeto objeto, objetivo, alcance, justificacion, tipoPresupuesto, acompanamiento, idProceso proceso, idEtapa etapa  FROM sia_auditorias  WHERE idAuditoria=:id ";
		*/
		if ($global=="SI"){

			$sql="SELECT a.idCuenta cuenta,a.idPrograma programa, a.idAuditoria auditoria,COALESCE(convert(varchar(20),a.clave),convert(varchar(20),a.idAuditoria)) claveAuditoria, isnull(a.clave,'') clave, a.tipoAuditoria tipo, a.idArea area,a.objetivo, a.alcance, a.justificacion, a.tipoPresupuesto, a.acompanamiento, a.idProceso proceso, a.idEtapa etapa, REPLACE(ISNULL(CONVERT(VARCHAR(10),a.fInicio,105), ''), '1900-01-01', '') feInicio, REPLACE(ISNULL(CONVERT(VARCHAR(10),a.fFin,105), ''), '1900-01-01', '') feFin, a.tipoObservacion tipoObse, a.observacion,a.idResponsable responsable, isnull(a.idSubresponsable,'') subresponsable,REPLACE(ISNULL(CONVERT(VARCHAR(10),a.fIRA,105), ''), '1900-01-01', '') ira, REPLACE(ISNULL(CONVERT(VARCHAR(10),a.fIFA,105), ''), '1900-01-01', '') ifa
				FROM sia_auditorias a 
				LEFT JOIN  sia_objetos o ON a.idObjeto = o.idObjeto 
				WHERE  a.idCuenta=:cuenta AND a.idAuditoria =:id AND a.idEtapa in (SELECT idEtapa FROM sia_rolesetapas re INNER JOIN sia_usuariosroles ur ON ur.idRol = re.idRol  WHERE ur.idUsuario=:usrActual) ORDER BY a.idAuditoria ";
				
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':cuenta' => $cuenta, ':id' => $id, ':usrActual' => $usrActual));
		}else{

			$sql="SELECT a.idCuenta cuenta,a.idPrograma programa, a.idAuditoria auditoria,COALESCE(convert(varchar(20),a.clave),convert(varchar(20),a.idAuditoria)) claveAuditoria, isnull(a.clave,'') clave, a.tipoAuditoria tipo, a.idArea area,a.objetivo, a.alcance, a.justificacion, a.tipoPresupuesto, a.acompanamiento, a.idProceso proceso, a.idEtapa etapa, REPLACE(ISNULL(CONVERT(VARCHAR(10),a.fInicio,105), ''), '1900-01-01', '') feInicio, REPLACE(ISNULL(CONVERT(VARCHAR(10),a.fFin,105), ''), '1900-01-01', '') feFin, a.tipoObservacion tipoObse, a.observacion,a.idResponsable responsable, isnull(a.idSubresponsable,'') subresponsable,REPLACE(ISNULL(CONVERT(VARCHAR(10),a.fIRA,105), ''), '1900-01-01', '') ira, REPLACE(ISNULL(CONVERT(VARCHAR(10),a.fIFA,105), ''), '1900-01-01', '') ifa
				FROM sia_auditorias a 
				LEFT JOIN  sia_objetos o ON a.idObjeto = o.idObjeto 
				WHERE  a.idCuenta=:cuenta AND a.idArea =:area AND a.idAuditoria =:id AND a.idEtapa in (SELECT idEtapa FROM sia_rolesetapas re INNER JOIN sia_usuariosroles ur ON ur.idRol = re.idRol  WHERE ur.idUsuario=:usrActual) ORDER BY a.idAuditoria";
				
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':cuenta' => $cuenta, ':id' => $id, ':area' => $area, ':usrActual' => $usrActual));		
		}

		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});

	$app->get('/lstCriteriosByAuditoria/:id', function($id)    use($app, $db) {
	
		$sql="Select  c.idcriterio id, c.nombre from sia_auditoriascriterios ac inner join sia_criterios c on ac.idCriterio=c.idCriterio " .
		"WHERE ac.idAuditoria=:id ";


	$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':id' => $id));
		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});	



	$app->get('/tblSujetosByCuenta/:id', function($id)    use($app, $db) {
		$sql="SELECT  s.idSujeto id, ltrim(s.nombre) sujeto, s.estatus " .
		"FROM sia_cuentas c " .
		"INNER JOIN sia_sujetos s on c.idCuenta=s.idCuenta " .
		"WHERE c.idCuenta= :id " .
		"ORDER BY s.nombre";

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':id' => $id));
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});


	$app->get('/tblObjetosByCuenta/:id', function($id)    use($app, $db) {
		$sql="SELECT  ltrim(s.nombre) sujeto, o.nombre objeto, o.original, o.modificado, o.ejercido, o.pagado, o.pendiente " .
		"FROM sia_cuentas c " .
		"INNER JOIN sia_sujetos s on c.idCuenta=s.idCuenta " .
		"INNER JOIN sia_objetos o on s.idCuenta=o.idCuenta and s.idSujeto=o.idSujeto " .
		"WHERE c.idCuenta= :id " .
		"ORDER BY s.nombre, o.nombre";

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':id' => $id));
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});




	//Guarda un avanceActividad
$app->post('/guardar/avance', function()  use($app, $db) {
		$usrActual = $_SESSION["idUsuario"];

		$request=$app->request;
		$oper = $request->post('txtOperacion');
		$cuenta = $request->post('txtCuenta');
		$programa = $request->post('txtPrograma');
		$auditoria = $request->post('txtAuditoria');
		$sujeto = $request->post('txtSujeto');
		$objeto = $request->post('txtObjeto');
		$fase = $request->post('txtFase');
		$actividad = $request->post('txtActividad');


		$porcentaje = $request->post('txtPorcentaje');

		if($oper=='INS'){
			$sql="INSERT INTO sia_auditoriasavances (idCuenta, idPrograma, idAuditoria, idSujeto, idObjeto, idFase, idActividad, porcentaje,  usrAlta, fAlta, estatus) " .
			"VALUES(:cuenta, :programa, :auditoria, :sujeto, :objeto, :fase, :actividad, :porcentaje, :usrActual, getdate(), 'ACTIVO');";

			$dbQuery = $db->prepare($sql);

			$dbQuery->execute(array(':cuenta' => $cuenta, ':programa' => $programa, ':auditoria' => $auditoria,
			':sujeto' => $sujeto, ':objeto' => $objeto, ':fase' => $fase, ':actividad' => $actividad,':porcentaje' => $porcentaje, ':usrActual' => $usrActual ));
		}else{
			$avance = $request->post('txtAvance');
			$sql="UPDATE sia_auditoriasavances SET " .
			"idCuenta=:cuenta, idPrograma=:programa, idAuditoria=:auditoria, idSujeto=:sujeto, idObjeto=:objeto, idFase=:fase, idActividad=:actividad, porcentaje=:porcentaje, " .
			"usrModificacion=:usrActual, fModificacion=now() " .
			"WHERE idAvance=:avance";

			$dbQuery = $db->prepare($sql);

			$dbQuery->execute(array(':cuenta' => $cuenta, ':programa' => $programa, ':auditoria' => $auditoria, ':sujeto' => $sujeto, ':objeto' => $objeto,
			':fase' => $fase, ':actividad' => $actividad,':porcentaje' => $porcentaje, ':usrActual' => $usrActual, ':avance' => $avance ));
		}
		$app->redirect($app->urlFor('listaAvances'));
	});


	//Guardar una auditoria

	$app->post('/guardar/auditoria', function()  use($app, $db) {
		$usrActual = $_SESSION["idUsuario"];

		$request=$app->request;
		$cuenta = $request->post('txtCuenta');
		$programa = $request->post('txtPrograma');
		$auditoria = $request->post('txtAuditoria');

		$oper = $request->post('txtOperacion');
		$tipo = $request->post('txtTipoAuditoria');
		$area = $request->post('txtArea');
		$sujeto = $request->post('txtSujeto');
		$objeto = $request->post('txtObjeto');
		$objetivo = strtoupper($request->post('txtObjetivo'));
		$alcance = strtoupper($request->post('txtAlcance'));
		$justificacion = strtoupper($request->post('txtJustificacion'));

		if($oper=='INS'){

			$cuenta = $_SESSION["idCuentaActual"];


			$auditoria = 'ASCM-' . date("YmdHis");

			$sql="INSERT INTO sia_auditorias (idCuenta, idPrograma, idArea, idAuditoria, tipoAuditoria, idSujeto, idObjeto, objetivo, alcance, justificacion, usrAlta, fAlta, estatus) ".
			"VALUES(:cuenta, :programa, :area, :auditoria, :tipo, :sujeto, :objeto, :objetivo, :alcance, :justificacion,  :usrActual, getdate(), 'ACTIVO');";
			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':cuenta' => $cuenta, ':programa' => $programa,':area' => $area, ':auditoria' => $auditoria, ':tipo' => $tipo,
			':sujeto' => $sujeto, ':objeto' => $objeto,	 ':objetivo' => $objetivo, ':alcance' => $alcance, ':justificacion' => $justificacion, ':usrActual' => $usrActual ));

			echo "<hr>INSERTA AUDITORIA: " . $auditoria;

		}else{
			$sql="UPDATE sia_auditorias " .
			"SET tipoAuditoria=:tipo, idArea=:area,  idSujeto=:sujeto, idObjeto=:objeto, objetivo=:objetivo, alcance=:alcance, justificacion=:justificacion, usrModificacion=:usrActual, fModificacion=now() " .
			"WHERE idAuditoria=:auditoria";
			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':tipo' => $tipo, ':area' => $area, ':sujeto' => $sujeto, ':objeto' => $objeto,	':objetivo' => $objetivo, ':alcance' => $alcance, ':justificacion' => $justificacion, ':usrActual' => $usrActual, ':auditoria' => $auditoria ));
		}
		
		
		$app->redirect($app->urlFor('programas'));
	});


// DATO
	




	$app->post('/guardar/catCuentas', function()  use($app, $db) {
		$usrActual = $_SESSION["idUsuario"];

		$request=$app->request;
		$id = $request->post('txtID');
		$oper = $request->post('txtOperacion');
		$anio = $request->post('txtAnio');
		$nombre = strtoupper($request->post('txtNombre'));
		$inicio = date_create(($request->post('txtFechaInicio')));
		$inicio = $inicio->format('Y-m-d');
		$fin = date_create(($request->post('txtFechaFin')));
		$fin = $fin->format('Y-m-d');

		$obs = strtoupper($request->post('txtNotas'));

		//$datos = " id=>" . $id . " oper=>" . $oper . " anio=>" . $anio . " nombre=>" . $nombre . " inicio=>" . $inicio . " fin=>" . $fin . " usrActual=>" . $usrActual . " obs=>" . $obs;

		//echo $datos;

		if($oper=='INS'){
			$cuenta = "CTA-" . $anio ;
			$sql="INSERT INTO sia_cuentas (idCuenta, anio, nombre, fInicio, fFin, observaciones, usrAlta, fAlta, estatus) ".
			"VALUES(:cuenta, :anio, :nombre, :inicio, :fin, :obs, :usrActual, getdate(), 'ACTIVO');";
			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':cuenta' => $cuenta, ':anio' => $anio, ':nombre' => $nombre, ':inicio' => $inicio, ':fin' => $fin, ':obs' => $obs,':usrActual' => $usrActual ));

			//Crea un PGA nuevo para esta cuenta
			$programa = 'PGA-' . $cuenta;
			$sql="INSERT INTO sia_programas (idCuenta, idPrograma, fRegistro, usrAlta, fAlta, estatus) VALUES(:cuenta, :programa, getdate(), :usrActual, getdate(), 'ACTIVO');";
			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':cuenta' => $cuenta, ':programa' => $programa, ':usrActual' => $usrActual ));

		}else{
			$cuenta = $id;
			$sql="UPDATE sia_cuentas SET anio=:anio, nombre=:nombre, fInicio=:inicio, fFin=:fin, observaciones=:obs, usrModificacion=:usrActual, fModificacion=getdate() WHERE idCuenta=:cuenta";
			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':anio' => $anio, ':nombre' => $nombre, ':inicio' => $inicio, ':fin' => $fin, ':obs' => $obs,':usrActual' => $usrActual,':cuenta' => $cuenta ));
		}
		$app->redirect($app->urlFor('catCuentas'));
	});


	$app->get('/catSujetos', function()  use ($app) {
		$app->render('catSujetos.php');
	});


$app->get('/auditorias',$autenticacionrole ,function()  use ($app, $db) {
		$cuenta = $_SESSION["idCuentaActual"];
		$area = $_SESSION["idArea"];
		$rpe = $_SESSION["idEmpleado"];
		$usrActual = $_SESSION["idUsuario"];
		$global = $_SESSION["usrGlobal"];
		$globalArea = $_SESSION["usrGlobalArea"];
		
		if ($global=="SI"){
			$sql="SELECT a.idAuditoria auditoria, COALESCE(convert(varchar(20),a.clave),convert(varchar(20),a.idAuditoria)) claveAuditoria, isnull(a.clave,'') clave, ar.nombre area,dbo.lstSujetosByAuditoria(a.idAuditoria) sujeto, dbo.lstObjetosByAuditoria(a.idAuditoria) objeto,ta.nombre tipo, a.idProceso proceso, a.idEtapa etapa " .
				"FROM sia_programas p " .
				"INNER JOIN sia_auditorias a on p.idCuenta=a.idCuenta and p.idPrograma=a.idPrograma " .
				"INNER JOIN sia_areas ar on a.idArea=ar.idArea " .
				//"INNER JOIN sia_unidades u on a.idCuenta = u.idCuenta and a.idSector=u.idSector and a.idSubsector = u.idSubsector and a.idUnidad=u.idUnidad " .
				"LEFT JOIN sia_tiposauditoria ta on a.tipoAuditoria= ta.idTipoAuditoria " .
				"WHERE a.idCuenta=:cuenta and a.idEtapa in (Select idEtapa from sia_rolesetapas re inner join sia_usuariosroles ur on ur.idRol = re.idRol  Where ur.idUsuario=:usrActual) " .
				"ORDER BY a.idAuditoria";

			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':cuenta' => $cuenta, ':usrActual' => $usrActual));						
		}else{
			if ($globalArea=="SI"){
				$sql="SELECT a.idAuditoria auditoria, COALESCE(convert(varchar(20),a.clave),convert(varchar(20),a.idAuditoria)) claveAuditoria, isnull(a.clave,'') clave, ar.nombre area, dbo.lstSujetosByAuditoria(a.idAuditoria) sujeto, dbo.lstObjetosByAuditoria(a.idAuditoria) objeto,ta.nombre tipo, a.idProceso proceso, a.idEtapa etapa " .
					"FROM sia_programas p " .
					"INNER JOIN sia_auditorias a on p.idCuenta=a.idCuenta and p.idPrograma=a.idPrograma " .
					"INNER JOIN sia_areas ar on a.idArea=ar.idArea " .
					"LEFT JOIN sia_tiposauditoria ta on a.tipoAuditoria= ta.idTipoAuditoria " .
					"WHERE a.idCuenta=:cuenta and a.idArea =:area and a.idEtapa in (Select idEtapa from sia_rolesetapas re inner join sia_usuariosroles ur on ur.idRol = re.idRol  Where ur.idUsuario=:usrActual)  and a.clave is not null  ORDER BY a.idAuditoria;";
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':cuenta' => $cuenta, ':area' => $area, ':usrActual' => $usrActual));	

				//echo "GLOBAL-AREA <hr> $sql <br> <br>Cuenta: $cuenta  Area= $area   usrActual= $usrActual";
				
			}else{
				$sql="SELECT a.idAuditoria auditoria, COALESCE(convert(varchar(20),a.clave),convert(varchar(20),a.idAuditoria)) claveAuditoria, isnull(a.clave,'') clave, ar.nombre area, dbo.lstSujetosByAuditoria(a.idAuditoria) sujeto, dbo.lstObjetosByAuditoria(a.idAuditoria) objeto,ta.nombre tipo, a.idProceso proceso, a.idEtapa etapa " .
					"FROM sia_programas p " .
					"INNER JOIN sia_auditorias a on p.idCuenta=a.idCuenta and p.idPrograma=a.idPrograma " .
					"INNER JOIN sia_areas ar on a.idArea=ar.idArea " .
					"INNER JOIN  sia_auditoriasauditores aa ON a.idCuenta=aa.idCuenta and a.idAuditoria=aa.idAuditoria " .
					"LEFT JOIN sia_tiposauditoria ta on a.tipoAuditoria= ta.idTipoAuditoria " .
					"WHERE a.idCuenta=:cuenta and aa.idAuditor=:auditor and  a.idEtapa in (Select idEtapa from sia_rolesetapas re inner join sia_usuariosroles ur on ur.idRol = re.idRol  Where ur.idUsuario=:usrActual)  and a.clave is not null  ORDER BY a.idAuditoria;";
			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':cuenta' => $cuenta, ':auditor' => $rpe, ':usrActual' => $usrActual));
			
			}
		}
		
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		$app->render('auditorias.php', $result);
	})->name('auditorias');
 
 	//---- PROGRAMAS

	$app->get('/programas', $autenticacionrole,function()  use($app, $db) {
		$cuenta     = $_SESSION["idCuentaActual"];
		$area       = $_SESSION["idArea"];
		$empleado   = $_SESSION["idEmpleado"];
		$usrActual  = $_SESSION["idUsuario"];
		$global     = $_SESSION["usrGlobal"];
		$globalArea = $_SESSION["usrGlobalArea"];		

		$aDatos     = array(':cuenta' => $cuenta, ':usrActual' => $usrActual);

		if ($global=="SI"){

			$sql="SELECT a.idAuditoria auditoria, e.nombre etapa, COALESCE(convert(varchar(20),a.clave),convert(varchar(20),a.idAuditoria)) claveAuditoria, isnull(a.clave,'') clave, ar.nombre area, dbo.lstSujetosByAuditoria(a.idAuditoria) sujeto, dbo.lstObjetosByAuditoria(a.idAuditoria) objeto, ta.nombre tipo, '0.00' avances " .
			" FROM sia_programas p " .
  			" INNER JOIN sia_auditorias a on p.idCuenta=a.idCuenta and p.idPrograma=a.idPrograma " .
			" INNER JOIN sia_areas ar on a.idArea=ar.idArea " .
			" LEFT  JOIN sia_tiposauditoria ta on a.tipoAuditoria= ta.idTipoAuditoria " .
			" LEFT  JOIN sia_etapas e on e.idProceso =a.idProceso and e.idEtapa=a.idEtapa " .
			" WHERE a.idCuenta=:cuenta AND a.idEtapa in (Select idEtapa from sia_rolesetapas re inner join sia_usuariosroles ur on ur.idRol = re.idRol Where ur.idUsuario=:usrActual) ".		
			" ORDER BY a.idAuditoria desc";
				
		}else{

			if ($globalArea=="SI"){

				$sql="SELECT a.idAuditoria auditoria, e.nombre etapa, COALESCE(convert(varchar(20),a.clave),convert(varchar(20),a.idAuditoria)) claveAuditoria, isnull(a.clave,'') clave, ar.nombre area, dbo.lstSujetosByAuditoria(a.idAuditoria) sujeto, dbo.lstObjetosByAuditoria(a.idAuditoria) objeto, ta.nombre tipo, '0.00' avances " .
				" FROM sia_programas p " .
	  			" INNER JOIN sia_auditorias a on p.idCuenta=a.idCuenta and p.idPrograma=a.idPrograma " .
				" INNER JOIN sia_areas ar on a.idArea=ar.idArea " .
				" LEFT  JOIN sia_tiposauditoria ta on a.tipoAuditoria= ta.idTipoAuditoria " .
				" LEFT  JOIN sia_etapas e on e.idProceso =a.idProceso and e.idEtapa=a.idEtapa" .
				" WHERE a.idCuenta=:cuenta AND a.idEtapa in (Select idEtapa from sia_rolesetapas re inner join sia_usuariosroles ur on ur.idRol = re.idRol Where ur.idUsuario=:usrActual) " ;

				if ( $area == 'UTSFFA' || $area == 'UTSFEAJ' ) {
					$sql = $sql . " AND a.idArea in (SELECT idArea from sia_areas where antecesor = :area) ";
					$aDatos[':area'] = $area;
				}elseif ( $area == 'CAAAF') {
				} else {
					$sql = $sql . " AND a.idArea=:area ";  
					$aDatos[':area'] = $area;
				}

				$sql = $sql . " AND a.clave is null ";
				$sql = $sql . " ORDER BY a.idAuditoria desc";
					

			}else{

				$sql="SELECT a.idAuditoria auditoria, e.nombre etapa, COALESCE(convert(varchar(20),a.clave),convert(varchar(20),a.idAuditoria)) claveAuditoria, isnull(a.clave,'') clave, ar.nombre area, dbo.lstSujetosByAuditoria(a.idAuditoria) sujeto, dbo.lstObjetosByAuditoria(a.idAuditoria) objeto, ta.nombre tipo, '0.00' avances " .
				"FROM sia_programas p " .
	  			" INNER JOIN sia_auditorias a on p.idCuenta=a.idCuenta and p.idPrograma=a.idPrograma " .
				" INNER JOIN sia_areas ar on a.idArea=ar.idArea " .
		        //" INNER JOIN sia_auditoriasauditores aa ON a.idCuenta=aa.idCuenta and a.idAuditoria=aa.idAuditoria " . // Se bloquea por que en PGA, aún no se tiene la relación de auditorias-auditores
				" LEFT  JOIN sia_etapas e on e.idProceso =a.idProceso and e.idEtapa=a.idEtapa" .
				" LEFT  JOIN sia_tiposauditoria ta on a.tipoAuditoria= ta.idTipoAuditoria " .
				" WHERE a.idCuenta=:cuenta AND a.idEtapa in (Select idEtapa from sia_rolesetapas re inner join sia_usuariosroles ur on ur.idRol = re.idRol  Where ur.idUsuario=:usrActual) " .
				//" AND aa.idAuditor=:auditor " . //Línea anterior.
				" AND a.usrAlta=:usrActual2 " .  // Línea nueva. 
				//" AND a.clave is null " .
				" ORDER BY a.idAuditoria desc";
					
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
			$app->render('programas.php', $result);
		}
	})->name('programas');


/*

		$sql="SELECT a.idAuditoria auditoria, ar.nombre area, u.nombre sujeto, o.nombre objeto, a.tipoAuditoria tipo, '0.00' avances " .
			"FROM sia_programas p " .
  			"INNER JOIN sia_auditorias a on p.idCuenta=a.idCuenta and p.idPrograma=a.idPrograma " .
			"LEFT JOIN sia_areas ar on a.idArea=ar.idArea " .
			"LEFT JOIN sia_unidades u on a.idCuenta = u.idCuenta and a.idSector=u.idSector and a.idUnidad=u.idUnidad " .
			"LEFT JOIN sia_objetos o on a.idObjeto=o.idObjeto and a.idCuenta=o.idCuenta and a.idPrograma=o.idPrograma and a.idAuditoria = o.idAuditoria " .
			"ORDER BY ar.nombre, u.nombre, o.nombre, a.tipoAuditoria  ";
			

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute();
		*/

/*
	$app->get('/programas', function()  use($app, $db) {
		$sql="SELECT a.idAuditoria auditoria, ar.nombre area, u.nombre sujeto, o.nombre objeto, a.tipoAuditoria tipo, '0.00' avances " .
			"FROM sia_programas p " .
  			"INNER JOIN sia_auditorias a on p.idCuenta=a.idCuenta and p.idPrograma=a.idPrograma " .
			"LEFT JOIN sia_areas ar on a.idArea=ar.idArea " .
			"LEFT JOIN sia_unidades u on a.idCuenta = u.idCuenta and a.idSector=u.idSector and a.idUnidad=u.idUnidad " .
			"LEFT JOIN sia_objetos o on a.idObjeto=o.idObjeto and a.idCuenta=o.idCuenta and a.idPrograma=o.idPrograma and a.idAuditoria = o.idAuditoria " .
			"ORDER BY ar.nombre, u.nombre, o.nombre, a.tipoAuditoria  ";

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute();
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS.");
		}else{
			$app->render('programas.php', $result);
		}
	})->name('listaPrograma');

*/
		$app->get('/tblGastoByUnidad/:sector/:subsector/:unidad', function($sector, $subsector, $unidad)    use($app, $db) {
		$cuenta = $_SESSION["idCuentaActual"];
		try{
			$sql="SELECT  f.nombre funcion, subfuncion, actividad, capitulo, partida " .
			"FROM sia_cuentasdetalles cd " .
			"left join sia_funciones f on f.idCuenta=cd.idCuenta and cd.sector=f.idSector and cd.subsector=f.idSubsector and f.idUnidad=cd.unidad " .
			"WHERE cd.idCuenta=:cuenta and cd.sector=:sector and cd.subsector=:subsector and cd.unidad=:unidad " .
			"ORDER BY funcion, subfuncion, actividad, capitulo, partida";

			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':cuenta' => $cuenta, ':unidad' => $unidad));
			$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
			if(!$result){
				$app->halt(404, "NO SE ENCONTRARON DATOS ");
			}else{
				echo json_encode($result);
			}
		}catch (PDOException $e) {
			print "¡Error!: " . $e->getMessage() . "<br/>";
			die();
		}

	});

	//Lista de Areas
	$app->get('/lstAreas', function()    use($app, $db) {

		$sql="SELECT idArea id, nombre texto FROM sia_areas WHERE idArea <> '' order by nombre";

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute();
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});


	//Lista de sectores
	$app->get('/lstSectores', function()    use($app, $db) {
		$cuenta = $_SESSION["idCuentaActual"];

		$sql="SELECT ltrim(idSector) id, ltrim(nombre) texto FROM sia_sectores Where idCuenta=:cuenta ORDER BY nombre";
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':cuenta' => $cuenta));
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});

	//Lista de SUB-sectores
	$app->get('/lstSubsectoresBySector/:sector', function($sector)    use($app, $db) {
		$cuenta = $_SESSION["idCuentaActual"];

		$sql="SELECT ltrim(idSubsector) id, ltrim(nombre) texto FROM sia_subsectores Where idCuenta=:cuenta and idSector=:sector ORDER BY nombre";
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':cuenta' => $cuenta, ':sector' => $sector));
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});

	
	//Lista de unidades by
	$app->get('/lstUnidadesBySectorSubsector/:sector/:subsector', function($sector, $subsector)    use($app, $db) {
		$cuenta = $_SESSION["idCuentaActual"];
		$area = $_SESSION["idArea"];
		
		$sql="SELECT ltrim(idUnidad) id, nombre texto FROM sia_unidades WHERE idCuenta=:cuenta AND idSector=:sector AND idSubsector=:subsector ORDER BY nombre;";
		
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':cuenta' => $cuenta, ':sector' => $sector, ':subsector' => $subsector));
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});


	//Lista de sujetos
	$app->get('/lstSujetos', function()    use($app, $db) {
		$cuenta = $_SESSION["idCuentaActual"];

		$sql="SELECT ltrim(idUnidad) id, concat(ltrim(idUnidad), ' ', nombre) texto FROM sia_unidades WHERE idCuenta = :cuenta ORDER BY nombre";
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':cuenta'=>$cuenta));
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});

	//Lista de tipos de auditorias
	$app->get('/lstTiposAuditorias', function()    use($app, $db) {

		$empleado = $_SESSION["idEmpleado"];
		$area = $_SESSION["idArea"];

		$sql="SELECT idTipoAuditoria id, nombre texto FROM sia_tiposAuditoria order by nombre";
		
		/*//$sql="SELECT idTipoAuditoria id, nombre texto FROM sia_tiposAuditoria ".
				"WHERE  idTipoAuditoria in (SELECT idTipoAuditoria FROM sia_areastiposauditoria Where idArea=:area )";*/

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute();
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		echo json_encode($result);
	});

	//Listar objetos by sujeto
	$app->get('/lstObjetosBySujeto/:id', function($id)  use($app, $db) {

		$sql="SELECT idObjeto id, nombre texto FROM sia_objetos Where idSujeto=:id order by nombre";

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':id' => $id));
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON OBJETOS DE FISCALIZACIÓN. ");
		}else{
			echo json_encode($result);
		}
	});

	//Listar auditorias by sujeto
	$app->get('/lstAuditoriasBySujeto/:id', function($id)  use($app, $db) {
		$sql="SELECT idAuditoria id, concat(idAuditoria, ' ', tipoAuditoria) texto, idProceso proceso, idEtapa etapa  FROM sia_auditorias Where idSujeto=:id order by 2 asc";

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':id' => $id));
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON AUDITORIAS. ");
		}else{
			echo json_encode($result);
		}
	});

	//Listar auditorias by sujeto + objeto
	$app->get('/lstAuditoriasBySujetoObjeto/:suj/:obj', function($suj, $obj)  use($app, $db) {
		$cuenta = $_SESSION["idCuentaActual"];


		$sql="SELECT idAuditoria id, concat(idAuditoria, ' ', tipoAuditoria) texto, idProceso proceso, idEtapa etapa FROM sia_auditorias Where idCuenta=:cuenta and ltrim(idSujeto)=:suj and ltrim(idObjeto)=:obj order by 2 asc";

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':cuenta' => $cuenta, ':suj' => $suj, ':obj' => $obj));
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON ACTIVIDADES. ");
		}else{
			echo json_encode($result);
		}
	});







	///////////////////////////////////////////////////////////////////////////////////////////









	$app->get('/perfil', function()    use($app, $db) {
		$usrActual = $_SESSION["idUsuario"];
		$sql="SELECT idUsuario, nombre, paterno, materno, telefono, usuario FROM sia_usuarios WHERE idUsuario=:id";
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':id' => $usrActual));
		$result['datos'] = $dbQuery->fetch(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			$app->render('perfil.php', $result);
		}
	})->name('listaPerfiles');

//Guardar SupervisorApoyo
	$app->post('/guardar/perfil', function()  use($app, $db) {

		$request=$app->request;
		$id = $request->post('txtID');
		$nombre = $request->post('txtNombre');
		$paterno = $request->post('txtPaterno');
		$materno = $request->post('txtMaterno');
		$telefono = $request->post('txtTelefono');
		$correo = $request->post('txtCorreo');

		$campana = $request->post('txtCampana');


		$nueva = $request->post('txtContrasenaNueva');
		$cambiar = $request->post('txtCambiarPass');

		$sNuevaContrasena="";
		if($cambiar=="SI") $sNuevaContrasena =", pwd=:pass ";

		try{
			$sql = "UPDATE sia_usuarios SET nombre=:nombre, paterno=:paterno, materno=:materno, telefono=:telefono, usuario=:correo " . $sNuevaContrasena . " Where idUsuario=:usrActual ";
			$dbQuery = $db->prepare($sql);


			//Actualizar contrasena
			if($cambiar=="SI") {
				$dbQuery->execute(array(':nombre'=> $nombre,':paterno'=> $paterno, ':materno'=> $materno, ':telefono'=> $telefono, ':correo'=> $correo, ':pass'=> $nueva, ':usrActual'=> $id));
			}else{
				$dbQuery->execute(array(':nombre'=> $nombre,':paterno'=> $paterno, ':materno'=> $materno, ':telefono'=> $telefono, ':correo'=> $correo, ':usrActual'=> $id));
			}

			//Actualizar la campana
			$sql = "UPDATE sia_cuentausuario SET predeterminada='NO' Where idUsuario=:usrActual";
			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':usrActual'=> $id));

			$sql = "UPDATE sia_cuentausuario SET predeterminada='SI' Where idCuenta=:campana and idUsuario=:usrActual ";
			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':campana'=> $campana, ':usrActual'=> $id));


			$result= array('idUsuario' => $_SESSION["idUsuario"] , 'nombre' => $_SESSION["sUsuario"] );
			$app->redirect($app->urlFor('cerrar'));
		}catch (PDOException $e) {
			print "¡Error!: " . $e->getMessage() . "<br/>";
			die();
		}
	});

	$app->get('/configuracion', function()  use ($app) {
		$app->render('configuracion.php');
	});

	$app->get('/reportes',$autenticacionrole ,function()  use ($app, $db) {
		$usrActual = $_SESSION["idUsuario"];
		$sql="SELECT r.idReporte, r.nombre sReporte, r.archivo ".
		"FROM sia_usuarios u ".
		"INNER JOIN sia_usuariosRoles ur on u.idUsuario=ur.idUsuario ".
		"INNER JOIN sia_rolesReportes rr on ur.idRol=rr.idRol ".
		"INNER JOIN sia_reportes r on rr.idReporte=r.idReporte ".
		"WHERE u.idUsuario=:usrActual " .
		"ORDER BY r.nombre";

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':usrActual' => $usrActual));
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			//echo "Antes de REPORTES";
			$app->render('reportes.php', $result);
		}

	})->name('reportes');



	//Parametros del reporte
	$app->get('/reporteParametros/:id', function($id)    use($app, $db) {
		$usrActual = $_SESSION["idUsuario"];
		$sql="SELECT idParametro, tipo, etiqueta, globo, ancho, dominio, consulta, predeterminado FROM sia_reportesParametros WHERE idReporte=:id ORDER BY idParametro ";
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':id' => $id));
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});


	//Parametros del reporte
	$app->get('/expandirListaParametro/:idParametro', function($idParametro)    use($app, $db) {
		$usrActual = $_SESSION["idUsuario"];

		$sql="SELECT consulta FROM sia_reportesParametros WHERE idParametro=:idParametro";
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':idParametro' => $idParametro));
		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);
		$sql = $result['consulta'];

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute();
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}

	});


	//Listar lstCampanas by Usr
	$app->get('/lstCuentasByUsr/:id',  function($id=0)  use($app, $db) {
		$id = (int)$id;
		try{
			$sql = "SELECT c.idCuenta id, c.nombre texto ".
			"FROM sia_cuentas c INNER JOIN sia_cuentausuario cu on c.idCuenta=cu.idCuenta ".
			"WHERE cu.idUsuario= :id ".
			"ORDER BY c.idCuenta";

			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':id' => $id));
			$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

			if(!$result){
				$app->halt(404, "NO SE ENCONTRARON DATOS ");
			}else{
				echo json_encode($result);
			}
		}catch (PDOException $e) {
			print "¡Error!: " . $e->getMessage() . "<br/>";
			die();
		}
	});

//Lista de Módulos by Usuario
	$app->get('/lstModulosByUsuarioCampana/:id', function($id)    use($app, $db) {
		$usrActual = $_SESSION["idUsuario"];
		$sql="SELECT distinct m.idModulo, m.nombre, m.icono, m.panel, m.liga, m.orden, m.icono " .
		"FROM sia_rolesModulos rm " .
		"INNER JOIN sia_modulos m ON rm.idModulo=m.idModulo " .
		"WHERE rm.idRol in (Select idRol from sia_usuariosRoles Where idUsuario=:usrActual) " .
		"ORDER BY m.panel, m.orden ";

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':usrActual' => $usrActual));
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});

	$app->get('/lstAniosCuentaPublica', function()    use($app, $db) {
		$usrActual = $_SESSION["idUsuario"];

		$sql="SELECT anio id, anio texto FROM sia_cuentas WHERE estatus = 'ACTIVO' ORDER BY anio desc;";

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute();
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});

	function obtenerGif($archivo)
	{
		$gif = "";
		$pos = strpos($archivo, '.');
		$ext = trim(strtoupper(substr($archivo, $pos+1)));
		

		if ($ext=='XLS' || $ext=='XLSX'){
			$gif = '<img src="img/xls.gif" />';
		}else{ 
			if ($ext=='DOC' || $ext=='DOCX'){
				$gif = '<img src="img/doc.gif" />';
			}else{
				if ($ext=='PDF'){
					$gif = '<img src="img/pdf.gif"/>';
				}else{
					if ($ext=='ZIP'){
						$gif = '<img src="img/zip.gif"/>';
					}else{
						$gif = '<img src="img/xls.gif"/>';
					}
				}
			}
		}
		return $gif;
	}


	function obtenerGifEstatus($sColor)
	{
		$gif = "";
		if ($sColor=='VERDE'){
			$gif = '<img src="img/microesferaverde.gif" />';
		}else{ 
			if ($sColor=='ROJO'){
				$gif = '<img src="img/microesferaroja.gif" />';
			}else{
				$gif = '<img src="img/microesferaamarilla.gif" />';
			}
		}
	    return $gif;
	}

	$app->get('/lstAniosCtaPublica(/:operacion)', function($operacion=NULL)    use($app, $db) {
		$usrActual      = $_SESSION["idUsuario"];
		$cuenta         = $_SESSION["sCuentaActual"];
		$nextAnioCuenta = (string)( (SUBSTR($cuenta, -4)) + 1);

		if ($operacion == "INS"  || $operacion == NULL ){
			$sql="SELECT MAX(anio)+1 id, MAX(anio)+1 texto FROM sia_cuentas WHERE estatus = 'ACTIVO';";
			$dbQuery = $db->prepare($sql);
			$dbQuery->execute();
		}else{
			$sql="SELECT DISTINCT x.anio id, x.anio texto FROM 
			( SELECT :nextAnioCuenta anio from sia_cuentas 
			  UNION SELECT anio FROM sia_cuentas WHERE estatus = 'ACTIVO' 
			) x ORDER BY x.anio DESC;";
			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':nextAnioCuenta' => $nextAnioCuenta));
		}
	


		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});


	$app->get('/validarExisteAnio/:anio', function($anio)    use($app, $db) {
		$usrActual = $_SESSION["idUsuario"];

		$sql=" SELECT count(*) total FROM sia_cuentas WHERE anio = :anio";

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':anio' => $anio));

		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);
		
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});
   
	$app->run();
