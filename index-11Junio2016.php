<?php
	session_start();
	require 'src/conexion.php';
	require 'Slim/Slim.php';
	
	include 'Excel/simplexlsx.class.php';


	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();

	require 'hector.php';
	require 'arian.php';
	require 'cota.php';
	

	define("MAIN_ACCESS", true);

	$app->config(array('debug'=>true, 'templates.path'=>'./',));

	try{
		$db = new PDO("sqlsrv:Server={$hostname}; Database={$database}", $username, $password );
	}catch (PDOException $e) {
		print "ERROR: " . $e->getMessage() . "<br><br>HOSTNAME: " . $hostname . " BD:" . $database . " USR: " . $username . " PASS: " . $password . "<br><br>";
		die();
	}
	if(!isset($_SESSION["logueado"])) $_SESSION["logueado"]=0;

	//ACCESO AL SISTEMA

	//Acceso al sitio
	$app->get('/', function() use($app){
		if($_SESSION["logueado"]==1){
			$result= array('idUsuario' => $_SESSION["idUsuario"] , 'nombre' => $_SESSION["sUsuario"] );
			$app->render('dashboard.php', $result);
		}else{
			$app->render('login.html');
		}
	});

	//Login
	$app->post('/login', function()  use($app, $db) {
		$request=$app->request;
		$cuenta = $request->post('txtUsuario');
		$pass = $request->post('txtPass');
		$latitud = $request->post('txtLatitud');
		$longitud = $request->post('txtLongitud');

		
		$sql = "SELECT u.idUsuario, u.idEmpleado, CONCAT(u.nombre, ' ', u.paterno, ' ', u.materno) nombre, e.idArea, p.nombre sPlaza " .
		"FROM sia_usuarios u " .
		"inner join sia_empleados e on u.idEmpleado=e.idEmpleado " . 
		"inner join sia_plazas p on e.idArea = p.idArea and e.idNivel = p.idNivel and e.idNombramiento = p.idNombramiento and e.idPuesto = p.idPuesto and e.idPlaza = p.idPlaza " .
		" WHERE usuario=:cuenta and pwd=:pass";
		
		
		//$dbQuery = $db->prepare("SELECT idUsuario, rpe, CONCAT(nombre, ' ', paterno, ' ', materno) nombre FROM sia_usuarios WHERE usuario=:cuenta and pwd=:pass");
		$dbQuery = $db->prepare($sql);
		
		$dbQuery->execute(array(':cuenta' => $cuenta, ':pass' => $pass));
		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);
		
		$_SESSION["usrGlobal"]="NO";
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
		$sql = 	"SELECT c.idCuenta id, c.nombre FROM sia_cuentas c inner join sia_cuentausuario cu  on c.idCuenta=cu.idCuenta WHERE cu.predeterminada='SI' and cu.idUsuario =:idx";
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':idx'=>$_SESSION["idUsuario"]));
		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);

		if($result){
			$_SESSION["sCuentaActual"] 		=$result['nombre'];
			$_SESSION["idCuentaActual"] 	=$result['id'];
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
		}else{
			$_SESSION["sCuentaActual"] 		="";
			$_SESSION["idCuentaActual"] 	="";
			$_SESSION["idProgramaActual"] 	="***";

		}
		$app->render('dashboard.php');
		}else{
			$app->halt(404, "Usuario: " . $cuenta . " Pass: " . $pass . "<br>USUARIO NO ENCONTRADO.");
			$_SESSION["logueado"] =0;
			$app->render('login.html');
		}
	});

	$app->get('/cerrar', function() use($app, $db){

			$sql="UPDATE sia_accesos SET fEgreso=getdate(), estatus='INACTIVO' WHERE idUsuario=:usrActual AND estatus='ACTIVO';";
			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':usrActual'=>$_SESSION["idUsuario"]));


		//unset($_SESSION["idUsuario"]);
		//unset($_SESSION["sUsuario"]);
		session_destroy();
		$app->render('login.html');
	});
	
	
	


	

	$app->get('/dashboard', function()  use ($app) {
		$app->render('dashboard.php');
	});



	



	$app->get('/catProcesos', function()  use ($app) {
		$app->render('catProcesos.php');
	});

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

	$app->get('/catCuentas', function()  use ($app, $db) {
		$sql="SELECT idCuenta id, nombre, fInicio inicio, fFin fin, estatus FROM sia_cuentas ORDER BY anio";
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute();
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS.");
		}else{
			$app->render('catCuentas.php', $result);
		}
	})->name('listaCuentas');

	$app->get('/lstCuentasByID/:id', function($id)    use($app, $db) {
		$sql="SELECT idCuenta id, anio, nombre, fInicio inicio, fFin fin,  observaciones, estatus FROM sia_cuentas  Where idCuenta=:id ORDER BY anio";
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':id' => $id));
		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});



	
/*
	$app->get('/lstAuditoriaByID/:id', function($id)    use($app, $db) {
		$sql="SELECT idCuenta cuenta, idPrograma programa, idAuditoria auditoria,  tipoAuditoria tipo, idArea area, idSujeto sujeto, idObjeto objeto, objetivo, alcance, justificacion " .
		"FROM sia_auditorias  WHERE idAuditoria=:id ";
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':id' => $id));
		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});

*/
	$app->get('/lstAuditoriaByID/:id', function($id)    use($app, $db) {
		$cuenta = $_SESSION["idCuentaActual"];
		$area = $_SESSION["idArea"];
		
		$usrActual = $_SESSION["idUsuario"];
		$global = $_SESSION["usrGlobal"];
		/*
		$sql="SELECT idCuenta cuenta, idPrograma programa, idAuditoria auditoria,  tipoAuditoria tipo, idArea area, idSector sector, idSubsector subSector, idUnidad unidad, idObjeto objeto, objetivo, alcance, justificacion, tipoPresupuesto, acompanamiento, idProceso proceso, idEtapa etapa  FROM sia_auditorias  WHERE idAuditoria=:id ";
		*/
		if ($global=="SI"){

			$sql="SELECT a.idCuenta cuenta, a.idPrograma programa, a.idAuditoria auditoria, COALESCE(a.clave, concat('PROY-',a.idAuditoria)) claveAuditoria, a.tipoAuditoria tipo, a.idArea area, a.idSector sector, a.idSubsector subSector, a.idUnidad unidad, a.idObjeto objeto, a.objetivo, a.alcance, a.justificacion, a.tipoPresupuesto, a.acompanamiento, a.idProceso proceso, a.idEtapa etapa FROM sia_auditorias a INNER JOIN sia_unidades u ON concat(a.idCuenta,a.idSector, a.idSubsector, a.idUnidad) = CONCAT(u.idCuenta,u.idSector, u.idSubsector, u.idUnidad) LEFT JOIN  sia_objetos o ON a.idObjeto = o.idObjeto WHERE  a.idCuenta=:cuenta AND a.idAuditoria =:id AND a.idEtapa in (SELECT idEtapa FROM sia_rolesetapas re INNER JOIN sia_usuariosroles ur ON ur.idRol = re.idRol  WHERE ur.idUsuario=:usrActual) ";
				
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':cuenta' => $cuenta, ':id' => $id, ':usrActual' => $usrActual));
		}else{

			$sql="SELECT a.idCuenta cuenta, a.idPrograma programa, a.idAuditoria auditoria, COALESCE(a.clave, concat('PROY-',a.idAuditoria)) claveAuditoria, a.tipoAuditoria tipo, a.idArea area, a.idSector sector, a.idSubsector subSector, a.idUnidad unidad, a.idObjeto objeto, a.objetivo, a.alcance, a.justificacion, a.tipoPresupuesto, a.acompanamiento, a.idProceso proceso, a.idEtapa etapa FROM sia_auditorias a INNER JOIN sia_unidades u ON concat(a.idCuenta,a.idSector, a.idSubsector, a.idUnidad) = CONCAT(u.idCuenta,u.idSector, u.idSubsector, u.idUnidad) LEFT JOIN  sia_objetos o ON a.idObjeto = o.idObjeto WHERE  a.idCuenta=:cuenta AND a.idArea =:area AND a.idAuditoria =:id AND a.idEtapa in (SELECT idEtapa FROM sia_rolesetapas re INNER JOIN sia_usuariosroles ur ON ur.idRol = re.idRol  WHERE ur.idUsuario=:usrActual) ";
				
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
		
		
		$app->redirect($app->urlFor('listaPrograma'));
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
			$sql="UPDATE sia_cuentas SET anio=:anio, nombre=:nombre, fInicio=:inicio, fFin=:fin, observaciones=:obs, usrModificacion=:usrActual, fModificacion=now() WHERE idCuenta=:cuenta";
			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':anio' => $anio, ':nombre' => $nombre, ':inicio' => $inicio, ':fin' => $fin, ':obs' => $obs,':usrActual' => $usrActual,':cuenta' => $cuenta ));
		}
		$app->redirect($app->urlFor('listaCuentas'));
	});


	$app->get('/catSujetos', function()  use ($app) {
		$app->render('catSujetos.php');
	});



	 $app->get('/auditorias', function()  use($app, $db) {
		$area = $_SESSION["idArea"];
		$sql="SELECT a.idAuditoria auditoria, COALESCE(a.clave, concat('PROY-',a.idAuditoria)) claveAuditoria, ar.nombre area, u.nombre sujeto, o.nombre objeto, a.tipoAuditoria tipo, '0.00' avances, a.idProceso proceso, a.idEtapa etapa FROM sia_programas p INNER JOIN sia_auditorias a on p.idCuenta=a.idCuenta and p.idPrograma=a.idPrograma INNER JOIN sia_areas ar on a.idArea=ar.idArea INNER JOIN sia_unidades u on a.idCuenta = u.idCuenta and a.idSector=u.idSector and a.idSubsector = u.idSubsector and a.idUnidad=u.idUnidad LEFT JOIN sia_objetos o on a.idObjeto=o.idObjeto and a.idCuenta=o.idCuenta and a.idPrograma=o.idPrograma and a.idAuditoria=o.idAuditoria WHERE ar.idArea=:area ORDER BY ar.nombre, u.nombre, o.nombre, a.tipoAuditoria ";

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':area' => $area));
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS.");
		}else{
			$app->render('auditorias.php', $result);
		}
	})->name('listaAuditorias');


	$app->get('/programas', function()  use($app, $db) {
		$cuenta = $_SESSION["idCuentaActual"];
		$area = $_SESSION["idArea"];


		$usrActual = $_SESSION["idUsuario"];
		$global = $_SESSION["usrGlobal"];
		/*
		$sql="SELECT idCuenta cuenta, idPrograma programa, idAuditoria auditoria,  tipoAuditoria tipo, idArea area, idSector sector, idSubsector subSector, idUnidad unidad, idObjeto objeto, objetivo, alcance, justificacion, tipoPresupuesto, acompanamiento, idProceso proceso, idEtapa etapa  FROM sia_auditorias  WHERE idAuditoria=:id ";
		*/
		if ($global=="SI"){

			$sql="SELECT a.idAuditoria auditoria, COALESCE(a.clave, concat('PROY-',a.idAuditoria)) claveAuditoria, ar.nombre area, u.nombre sujeto, o.nombre objeto, a.tipoAuditoria tipo, '0.00' avances " .
			"FROM sia_programas p " .
  			"INNER JOIN sia_auditorias a on p.idCuenta=a.idCuenta and p.idPrograma=a.idPrograma " .
			"INNER JOIN sia_areas ar on a.idArea=ar.idArea " .
			"INNER JOIN sia_unidades u on a.idCuenta = u.idCuenta and a.idSector=u.idSector and a.idSubsector = u.idSubsector and a.idUnidad=u.idUnidad " .
			"LEFT JOIN sia_objetos o on a.idObjeto=o.idObjeto and a.idCuenta=o.idCuenta and a.idPrograma=o.idPrograma and a.idAuditoria = o.idAuditoria " .
			"Where a.idCuenta=:cuenta " .

			//Linea nueva para filtrar por rol-etapa
			"and a.idEtapa in (Select idEtapa from sia_rolesetapas re inner join sia_usuariosroles ur on ur.idRol = re.idRol  Where ur.idUsuario=:usrActual) ".			
			"ORDER BY ar.nombre, u.nombre, o.nombre, a.tipoAuditoria ";
				
				$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':cuenta' => $cuenta, ':usrActual' => $usrActual));
		}else{

			$sql="SELECT a.idAuditoria auditoria, COALESCE(a.clave, concat('PROY-',a.idAuditoria)) claveAuditoria, ar.nombre area, u.nombre sujeto, o.nombre objeto, a.tipoAuditoria tipo, '0.00' avances " .
			"FROM sia_programas p " .
  			"INNER JOIN sia_auditorias a on p.idCuenta=a.idCuenta and p.idPrograma=a.idPrograma " .
			"INNER JOIN sia_areas ar on a.idArea=ar.idArea " .
			"INNER JOIN sia_unidades u on a.idCuenta = u.idCuenta and a.idSector=u.idSector and a.idSubsector = u.idSubsector and a.idUnidad=u.idUnidad " .
			"LEFT JOIN sia_objetos o on a.idObjeto=o.idObjeto and a.idCuenta=o.idCuenta and a.idPrograma=o.idPrograma and a.idAuditoria = o.idAuditoria " .
			"Where a.idCuenta=:cuenta  and a.idArea=:area " . 
			
			//Linea nueva para filtrar por rol-etapa
			"and a.idEtapa in (Select idEtapa from sia_rolesetapas re inner join sia_usuariosroles ur on ur.idRol = re.idRol  Where ur.idUsuario=:usrActual) " .

			"ORDER BY ar.nombre, u.nombre, o.nombre, a.tipoAuditoria ";
				
				$dbQuery = $db->prepare($sql);
				//$dbQuery->execute(array(':cuenta' => $cuenta, ':usrActual' => $usrActual));
			$dbQuery->execute(array(':cuenta' => $cuenta, ':area' => $area, ':usrActual' => $usrActual));		
		}

		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS.");
		}else{
			$app->render('programas.php', $result);
		}
	})->name('listaPrograma');


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

		$sql="SELECT idArea id, nombre texto FROM sia_areas order by nombre";

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
		$sql="SELECT ltrim(idUnidad) id, concat(ltrim(idUnidad), ' ', nombre) texto FROM sia_unidades order by nombre";
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute();
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

		//$sql="SELECT idTipoAuditoria id, nombre texto FROM sia_tiposAuditoria order by nombre";
		
		$sql="SELECT idTipoAuditoria id, nombre texto FROM sia_tiposAuditoria ".
				"WHERE  idTipoAuditoria in (SELECT at.idTipoAuditoria FROM sia_areastiposauditoria at INNER JOIN sia_empleados e ON e.idArea = at.idArea WHERE idEmpleado = :empleado )";

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':empleado' => $empleado));
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
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




	$app->get('/notificaciones', function()  use ($app) {
		$app->render('notificaciones.php');
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
			$app->render('./dashboard.php', $result);



		}catch (PDOException $e) {
			print "¡Error!: " . $e->getMessage() . "<br/>";
			die();
		}
	});

	$app->get('/configuracion', function()  use ($app) {
		$app->render('configuracion.php');
	});

	$app->get('/reportes', function()  use ($app, $db) {
		$usrActual = $_SESSION["idUsuario"];
		$sql="SELECT r.idReporte, r.nombre sReporte, r.idModulo sModulo, r.archivo ".
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

	});



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
		$sql="SELECT m.idModulo, m.nombre, m.icono, m.panel, m.liga " .
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




   
	$app->run();
