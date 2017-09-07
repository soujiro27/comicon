<?php
	session_start();	
	require 'src/conexion.php';
	require 'Slim/Slim.php';
	
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();

	define("MAIN_ACCESS", true);
		
	$app->config(array('debug'=>true, 'templates.path'=>'./',));				
	
	try{
		//$db = new PDO('mysql:host=localhost;dbname=contfisc_politicai;charset=utf8', 'contfisc_usrpi', 'usrpiCota13');

		
		$db = new PDO('mysql:host='. $hostname . ';dbname='. $database . ';charset=utf8',$username, $password );
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
			$app->render('kadmin/KAdmin-Light/dashboard.php', $result);			
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

		$dbQuery = $db->prepare("SELECT idUsuario, CONCAT(nombre, ' ', paterno, ' ', materno) nombre FROM sia_usuarios WHERE usuario=:cuenta and pwd=:pass LIMIT 1");		
		$dbQuery->execute(array(':cuenta' => $cuenta, ':pass' => $pass));
		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);
		if($result){
			
		$_SESSION["logueado"] =1;
		$_SESSION["idUsuario"] =$result['idUsuario'];		
		$_SESSION["sUsuario"] =$result['nombre'];
		
		
		//Obtener datos generales
		//$_SESSION["idEntidad"] =9;
		$sql="SELECT idEntidad  FROM sia_configuracion LIMIT 1;";	
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
		$sql="INSERT INTO sia_accesos (idUsuario, fIngreso, latitud, longitud) VALUES(:usrActual, now(), :latitud, :longitud);";
		$dbQuery = $db->prepare($sql);		
		$dbQuery->execute(array(':usrActual' => $usrActual, ':latitud'=>$latitud, ':longitud'=>$longitud ));
		
		$sql="SELECT idAcceso FROM sia_accesos WHERE idUsuario= :usrActual ORDER BY idAcceso desc  LIMIT 1";	
		$dbQuery = $db->prepare($sql);				
		$dbQuery->execute(array(':usrActual'=> $usrActual));				
		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);							
		$id = $result['idAcceso'];	

		
		//Obtener la campaña actual
		$sql = 	"SELECT c.idCampana, c.nombre, c.tipo ".
				"FROM sia_campanas c inner join sia_campanaUsuario cu  on c.idCampana=cu.idCampana " .
				"WHERE cu.predeterminada='SI' and cu.idUsuario =:idx  LIMIT 1";			
		$dbQuery = $db->prepare($sql);		
		$dbQuery->execute(array(':idx'=>$_SESSION["idUsuario"]));
		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);
		
		if($result){
			$_SESSION["sCampanaActal"] 		=$result['nombre'];
			$_SESSION["idCampanaActal"] 	=$result['idCampana'];
			$_SESSION["tipoCampanaActal"] 	=$result['tipo'];
		}else{
			$_SESSION["sCampanaActal"] 		="";
			$_SESSION["idCampanaActal"] 	="";
			$_SESSION["tipoCampanaActal"] 	="";			
		}
		$app->render('kadmin/KAdmin-Light/dashboard.php');		
		}else{
			$app->halt(404, "Usuario: " . $cuenta . " Pass: " . $pass . "<br>USUARIO NO ENCONTRADO.");			
			$_SESSION["logueado"] =0;
			$app->render('login.html');
		}	
	});
	
	$app->get('/cerrar', function() use($app, $db){
			
			$sql="UPDATE sia_accesos SET fEgreso=now(), estatus='INACTIVO' WHERE idUsuario=:usrActual AND estatus='ACTIVO';";
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
	
	////////////////////////////////////////////////////////////////////////////////////////////
	
	$app->get('/acopio', function()  use ($app) {
		$app->render('acopio.php');
	});		
	
	$app->get('/catAuditores', function()  use ($app) {
		$app->render('catAuditores.php');
	});		

	$app->get('/papeles', function()  use ($app) {
		$app->render('papeles.php');
	});		
	
	
	$app->get('/avances', function()  use ($app) {
		$app->render('avances.php');
	});		
	
	$app->get('/avanceActividad', function()  use ($app) {
		$app->render('avanceActividad.php');
	});	
	
	$app->get('/auditorias', function()  use ($app) {
		$app->render('auditorias.php');
	});	

	$app->get('/catUsuarios', function()  use ($app) {
		$app->render('catUsuarios.php');
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

	$app->get('/catCuentas', function()  use ($app) {
		$app->render('catCuentas.php');
	});	

	$app->get('/catSujetos', function()  use ($app) {
		$app->render('catSujetos.php');
	});	
	
	
	$app->get('/programas', function()  use ($app) {
		$app->render('programas.php');
	});	

	$app->get('/notificaciones', function()  use ($app) {
		$app->render('notificaciones.php');
	});	
			

	///////////////////////////////////////////////////////////////////////////////////////////
	

	

	




	$app->get('/perfil', function()    use($app, $db) {
		$usrActual = $_SESSION["idUsuario"];
		$sql="SELECT idUsuario, nombre, paterno, materno, telefono, usuario FROM sia_usuarios WHERE idUsuario=:id Limit 1";				
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
			$sql = "UPDATE sia_campanaUsuario SET predeterminada='NO' Where idUsuario=:usrActual";			
			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':usrActual'=> $id));	
			
			$sql = "UPDATE sia_campanaUsuario SET predeterminada='SI' Where idCampana=:campana and idUsuario=:usrActual ";			
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
			$app->render('reportes.php', $result);
		}
		
	});	
	

	
	//Parametros del reporte
	$app->get('/reporteParametros/:id', function($id)    use($app, $db) {
		$usrActual = $_SESSION["idUsuario"];
		
		$sql="SELECT idParametro, tipo, etiqueta, globo, ancho, dominio, consulta, predeterminado " .
		"FROM sia_reportesParametros " . 
		"WHERE idReporte=:id " .
		"ORDER BY idParametro ";
		
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
	$app->get('/lstCampanasByUsr/:id',  function($id=0)  use($app, $db) {
		$id = (int)$id;
		try{
			$sql = "SELECT c.idCampana id, c.nombre texto ".
			"FROM sia_campanas c INNER JOIN sia_campanaUsuario cu on c.idCampana=cu.idCampana ".
			"WHERE cu.idUsuario= :id ".
			"ORDER BY c.idCampana";
			
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
	
//Listar de Ciudadanos by Colonia
	$app->get('/lstModulosByUsuarioCampana/:id', function($id)    use($app, $db) {
		$usrActual = $_SESSION["idUsuario"];
		
		$sql="SELECT distinct  m.idModulo, m.nombre, m.icono, m.panel, m.liga " .
		"FROM sia_rolesModulos rm " .
		"INNER JOIN sia_modulos m ON rm.idModulo=m.idModulo " .
		"INNER JOIN sia_modulosTipos mt ON m.idModulo=mt.idModulo " .
		"INNER JOIN sia_campanas c on c.tipo=mt.idTipo " .
		"WHERE rm.idRol in (Select idRol from sia_usuariosRoles Where idUsuario=:usrActual) AND c.idCampana=:idCampana " .
		"ORDER BY m.panel, m.orden ";

		
		$dbQuery = $db->prepare($sql);		
		$dbQuery->execute(array(':usrActual' => $usrActual, ':idCampana' => $id));
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{			
			echo json_encode($result);
		}	
	});	
	


		

	$app->run();
