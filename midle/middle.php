<?php
include("src/conexion.php");

 try{
		  $db = new PDO("sqlsrv:Server={$hostname}; Database={$database}", $username, $password );
	 }catch (PDOException $e) {
	  	print "ERROR: " . $e->getMessage(); 
	  	die();
	 }

$autenticacionrole = function(\Slim\Route $route) use($db){
	$app = \Slim\Slim::getInstance();
	if($_SESSION["idUsuario"] =='Undefined'){
		$app->redirect('/');
	}else{
		$role = "./".$route->getName();
		$usrActual = $_SESSION["idUsuario"];	

		$dbQuery = $db->prepare("SELECT TOP 1 rm.idModulo modulo FROM sia_rolesmodulos rm  inner join sia_usuariosroles ur on rm.idRol = ur.idRol inner join sia_modulos mo on rm.idModulo = mo.idModulo where ur.idUsuario=:usrActual and mo.liga=:liga;");
		$dbQuery->execute(array(':usrActual' => $usrActual,':liga' => $role));		
		$result = $dbQuery->fetch(PDO::FETCH_ASSOC); 
		$modulo = $result ['modulo'];
		try{
			if($modulo == '')
			{
				throw new Exception("No tienes Acceso al modulo");
			}
		}catch(Exception $e){
				$app->redirect('/error');
				$app->status(404);
				echo json_encode(array('status' => 'error', 'message' => $e->getMessage() , $modulo,$e->getLine(), 'ruta' => $role, 'usuario' => $usrActual));
				$app->stop();

		}
	}
};


$dashboard = function(){
	$app = \Slim\Slim::getInstance();
	if($_SESSION["idCuentaActual"] =='Undefined'){
		$app->redirect('/');
	}
};

$authentica = function(){
	$app = \Slim\Slim::getInstance();
	if($_SESSION["sCuentaActual"] =='Undefined'){
		$app->redirect('/');
	}
};

?>

