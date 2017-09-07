<?php

 try{
		  $db = new PDO("sqlsrv:Server={$hostname}; Database={$database}", $username, $password );
	 }catch (PDOException $e) {
	  	print "ERROR: " . $e->getMessage() . "<br><br>HOSTNAME: " . $hostname . " BD:" . $database . " USR: " . $username . " PASS: " . $password . "<br><br>";
	  	die();
	 }

// auditorias ACCH 23-05-2016 INICIO

	//lista de auditoria
		$app->get('/auditoriasBYauditores/:auditoria', function($auditoria)    use($app, $db) {
		$area = $_SESSION["idArea"];

		$sql="SELECT Case When e.idEmpleado=aa.idAuditor  Then 'SI' Else 'NO' End As asignado, e.idEmpleado, aa.idAuditor, " .
		"e.idArea, concat(e.nombre, ' ', e.paterno, ' ', e.materno) auditor, ISNULL(p.nombre,'') plaza, Case When aa.lider='SI' Then aa.lider Else '' End As lider
		  FROM sia_empleados e " .
		  "LEFT JOIN sia_usuarios us on e.idEmpleado = us.idEmpleado " .
		  "LEFT JOIN sia_auditoriasauditores aa on e.idEmpleado=aa.idAuditor  and aa.idAuditoria=:auditoria " .
		  "LEFT JOIN sia_plazas p on e.idPlaza=p.idPlaza " .
		  "WHERE e.idArea=:area and e.idNivel not in ('45.0', '40.0', '31.0') " .
		  "ORDER BY concat(e.nombre, ' ', e.paterno, ' ', e.materno)";
	

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':area' => $area, ':auditoria' => $auditoria));
		$result ['datos']= $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});


 //Modificar Cronograma
 	$app->get('/modifcronograma/:id/:aud',  function($id,$aud)  use($app, $db) {


 		$sql="SELECT  aa.idActividad id, isnull(aa.idApartado,'') apartado , aa.idFase fase, isnull(aa.actividad,'') actividad,aa.fInicio inicio, aa.fFin fin, aa.diasEfectivos defec, aa.porcentaje por, aa.idPrioridad priori, aa.idImpacto impact, CONCAT(us.nombre,' ',us.paterno,' ',us.materno) nombre, aa.idResponsable respon, aa.estatus esta, aa.notas nota
			FROM sia_auditoriasactividades aa  
	    	INNER JOIN sia_fases f on aa.idFase=f.idFase
        	INNER JOIN sia_apartados ap on aa.idApartado = ap.idApartado 
			INNER JOIN sia_usuarios us on aa.idResponsable=us.idEmpleado
 			WHERE aa.idAuditoria=:aud and aa.idActividad=:id;";

		
 		$dbQuery = $db->prepare($sql);		
 		$dbQuery->execute(array(':id' => $id, ':aud' => $aud));
 		$result= $dbQuery->fetch(PDO::FETCH_ASSOC);
 		if(!$result){
 			$app->halt(404, "RECURSO NO ENCONTRADA.");			
 		}else{		
 			echo json_encode($result);
 		}		
 	});




	

		//lista de equipo de trabajo
		$app->get('/yyy/:auditoria/:objeto', function($auditoria, $objeto)    use($app, $db) {
		$area = $_SESSION["idArea"];
		
		$sql="SELECT e.idEmpleado id, aa.idAuditor, e.idArea, aa.lider lider,concat(e.nombre, ' ', e.paterno, ' ', e.paterno) texto, p.nombre plaza
		from sia_empleados e
		left join sia_auditoriasauditores aa on e.idEmpleado=aa.idAuditor 
		left join sia_plazas p on e.idPlaza=p.idPlaza
		Where e.idArea=:area and   aa.idAuditoria=:auditoria
		ORDER BY lider DESC";
		

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':area' => $area, ':auditoria' => $auditoria));
		$result ['datos']= $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});


		//lista de equipo de trabajo
		$app->get('/auditoriasByLider/:auditoria', function($auditoria)    use($app, $db) {
		$area = $_SESSION["idArea"];
		
		$sql="SELECT e.idEmpleado id, aa.idAuditor, e.idArea, isnull(aa.lider,'') lider,concat(e.nombre, ' ', e.paterno, ' ', e.materno) texto, isnull(p.nombre,'') plaza
		from sia_empleados e
		left join sia_auditoriasauditores aa on e.idEmpleado=aa.idAuditor 
		left join sia_plazas p on e.idPlaza=p.idPlaza
		Where e.idArea=:area and   aa.idAuditoria=:auditoria
		ORDER BY lider DESC";
		

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':area' => $area, ':auditoria' => $auditoria));
		$result ['datos']= $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});



	//lista de auditoria fases
	$app->get('/lstauditoriaByFases/:id', function($id)    use($app, $db) {
		$sql="SELECT f.nombre texto, orden, min(aa.fInicio ) desde, max(aa.fFin) hasta, sum(convert(int,aa.diasEfectivos)) dia " .
			"FROM sia_auditoriasactividades aa " .
			"INNER JOIN sia_fases f on aa.idFase = f.idFase " .
			"WHERE aa.idAuditoria=:id " .
			"Group by f.nombre, orden " .
			"order by f.orden";

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':id' => $id));
		$result ['datos']= $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});




	//lista de papeles
	$app->get('/lstpapeles/:id', function($id)    use($app, $db) {

		$cuenta = $_SESSION["idCuentaActual"];
		$area = $_SESSION["idArea"];
		$rpe = $_SESSION["idEmpleado"];		
		$usrActual = $_SESSION["idUsuario"];
		$global = $_SESSION["usrGlobal"];
		
		if ($global=="SI"){
			$sql="Select a.idAuditoria, COALESCE(clave, convert(varchar,a.idAuditoria)) claveAuditoria, u.nombre sujeto, dbo.lstObjetosByAuditoria(a.idAuditoria) objeto, ta.nombre tipoAuditoria, p.idPapel papel, tp.nombre sPapel, p.fPapel fecha,
			p.idFase fase, p.tipoResultado tresultado, p.resultado resul, p.archivoOriginal, p.archivoFinal aerchivo,  p.estatus estatus " .
			"FROM sia_papeles p  " .
			"inner join  sia_auditorias a on p.idCuenta=a.idCuenta and p.idPrograma = a.idPrograma and p.idAuditoria = a.idAuditoria " .
			"inner join sia_unidades u on concat(a.idCuenta,a.idSector, a.idSubsector, a.idUnidad)=concat(u.idCuenta,u.idSector, u.idSubsector, u.idUnidad) " .
			"inner join sia_tipospapeles tp on p.tipoPapel=tp.idTipoPapel " .
			"inner join sia_tiposauditoria ta on a.tipoAuditoria=ta.idTipoAuditoria " .
			"Where a.idCuenta=:cuenta " .

			//Linea nueva para filtrar por rol-etapa
			"and a.idEtapa in (Select idEtapa from sia_rolesetapas re inner join sia_usuariosroles ur on ur.idRol = re.idRol  Where ur.idUsuario=:usrActual) " . 
			
			"Order by p.fAlta desc ";
			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':cuenta' => $cuenta, ':usrActual' => $usrActual));
		}else{
			$sql="Select a.idAuditoria, COALESCE(clave, convert(varchar,a.idAuditoria)) claveAuditoria, u.nombre sujeto, dbo.lstObjetosByAuditoria(a.idAuditoria) objeto, ta.nombre tipoAuditoria, p.idPapel papel, tp.nombre sPapel, p.fPapel fecha,
			p.idFase fase, p.tipoResultado tresultado, p.resultado resul, p.archivoOriginal, p.archivoFinal aerchivo,  p.estatus estatus " .
			"FROM sia_papeles p  " .
			"inner join  sia_auditorias a on p.idCuenta=a.idCuenta and p.idPrograma = a.idPrograma and p.idAuditoria = a.idAuditoria " .
			"inner join sia_unidades u on concat(a.idCuenta,a.idSector, a.idSubsector, a.idUnidad)=concat(u.idCuenta,u.idSector, u.idSubsector, u.idUnidad) " .
			"inner join sia_tipospapeles tp on p.tipoPapel=tp.idTipoPapel " .
			"inner join sia_tiposauditoria ta on a.tipoAuditoria=ta.idTipoAuditoria " .
			"Where a.idCuenta=:cuenta  and a.idArea=:area and aa.idAuditor=:audito " . 
			
			//Linea nueva para filtrar por rol-etapa
			"and a.idEtapa in (Select idEtapa from sia_rolesetapas re inner join sia_usuariosroles ur on ur.idRol = re.idRol  Where ur.idUsuario=:usrActual) " . 
			
			"Order by p.fAlta desc ";
			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':cuenta' => $cuenta, ':area' => $area, ':usrActual' => $usrActual, ':audito' => $rpe));
		}








		$result ['datos']= $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});


//lista de actividades
	$app->get('/lstActividades/:id', function($id)    use($app, $db) {
		$rpe = $_SESSION["idEmpleado"];
		$cuenta = $_SESSION["idCuentaActual"];
		$area = $_SESSION["idArea"];
		$global = $_SESSION["usrGlobal"];
		
		if ($global=="SI"){			
			$sql=" SELECT a.idAuditoria, COALESCE(clave, convert(varchar,a.idAuditoria)) claveAuditoria, u.nombre sujeto, ta.nombre tipoAuditoria, concat(e.nombre, ' ', e.paterno, ' ', e.materno) auditor, aa.idAvance id, f.nombre fase, fa.nombre actividad, aa.porcentaje porcen " .
			"FROM sia_auditoriasavances aa " .
				"inner join sia_auditorias a  on aa.idCuenta=a.idCuenta and aa.idPrograma = a.idPrograma and aa.idAuditoria = a.idAuditoria " .
				//"left join sia_auditoriasauditores aaud  on aaud.idCuenta=a.idCuenta and aaud.idPrograma = a.idPrograma and aaud.idAuditoria = a.idAuditoria and aaud.idAuditor=aa.idAuditor " .
				"inner join sia_unidades u on concat(a.idCuenta,a.idSector, a.idSubsector, a.idUnidad)=concat(u.idCuenta,u.idSector, u.idSubsector, u.idUnidad) " .
				"left join sia_empleados e on aa.idAuditor=e.idEmpleado " .
				"inner join sia_fases f on f.idFase=aa.idFase " .
				"inner join sia_auditoriasactividades aact  on aa.idActividad = aact.idAuditoriasAuditores " .
				"inner join sia_fasesactividades fa  on aact.idFase=fa.idFase and aact.idActividad=fa.idActividad " .
				"inner join sia_tiposauditoria ta on a.tipoAuditoria=ta.idTipoAuditoria " .
			"Where a.idCuenta=:cuenta " .
			"order by aa.idAvance desc";

			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':cuenta' => $cuenta));
		}else{
			$sql="SELECT a.idAuditoria, COALESCE(clave, convert(varchar,a.idAuditoria)) claveAuditoria, u.nombre sujeto, ta.nombre tipoAuditoria, concat(e.nombre, ' ', e.paterno, ' ', e.materno) auditor, aa.idAvance id, f.nombre fase, fa.nombre actividad, aa.porcentaje porcen " .
			"FROM sia_auditoriasavances aa " .
				"inner join sia_auditorias a  on aa.idCuenta=a.idCuenta and aa.idPrograma = a.idPrograma and aa.idAuditoria = a.idAuditoria " .
				"left join sia_auditoriasauditores aaud  on aaud.idCuenta=a.idCuenta and aaud.idPrograma = a.idPrograma and aaud.idAuditoria = a.idAuditoria and aaud.idAuditor=aa.idAuditor " .
				"inner join sia_unidades u on concat(a.idCuenta,a.idSector, a.idSubsector, a.idUnidad)=concat(u.idCuenta,u.idSector, u.idSubsector, u.idUnidad) " .
				"left join sia_empleados e on aa.idAuditor=e.idEmpleado " .
				"inner join sia_fases f on f.idFase=aa.idFase " .
				"inner join sia_auditoriasactividades aact  on aa.idActividad = aact.idAuditoriasAuditores " .
				"inner join sia_fasesactividades fa  on aact.idFase=fa.idFase and aact.idActividad=fa.idActividad " .
				"inner join sia_tiposauditoria ta on a.tipoAuditoria=ta.idTipoAuditoria " .
			"Where a.idCuenta=:cuenta and a.idArea=:area and aa.idAuditor=:audito " .
			"order by aa.idAvance desc";

			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':cuenta' => $cuenta, ':area' => $area, ':audito' => $rpe));		
		}
		$result ['datos']= $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});













//lista de presuúesto
	$app->get('/lstpresupuesto/:id', function($id)    use($app, $db) {
		$sql="SELECT idAuditoria, tipoPresupuesto from sia_auditorias " .
		"WHERE pa.idAuditoria=:id";

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':id' => $id));
		$result ['datos']= $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});


	//lista de acompañamiento
	$app->get('/lstacompa/:id', function($id)    use($app, $db) {
		$sql="SELECT tp.idTipoPapel numero, tp.nombre tipo, pa.resultado resul, CONCAT(us.nombre,' ',us.paterno,' ',us.materno) texto, pa.fAlta fecha, pa.estatus estatus,pa.archivoOriginal anexo " .
		"FROM sia_papeles pa " .
		"LEFT JOIN sia_tipospapeles tp on pa.tipoPapel=tp.idTipoPapel " .
		"LEFT JOIN sia_usuarios us on pa.usrAlta=us.idUsuario " .
		"WHERE pa.idAuditoria=:id";

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':id' => $id));
		$result ['datos']= $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});



	$app->get('/lstAvancesActividad/:id', function($id) use($app, $db){
		$cuenta = $_SESSION["idCuentaActual"];
		//$area = $_SESSION["idArea"];
		
		$sql="SELECT a.idAuditoria, u.nombre sujeto, a.tipoAuditoria, concat(e.nombre, ' ', e.paterno, ' ', e.materno) auditor, aa.idAvance, f.nombre fase, fa.nombre actividad, aa.porcentaje " .
		"FROM sia_auditoriasavances aa " .
			"left join sia_auditorias a  on aa.idCuenta=a.idCuenta and aa.idPrograma = a.idPrograma and aa.idAuditoria = a.idAuditoria " .
			"left join sia_auditoriasauditores aaud  on aaud.idCuenta=a.idCuenta and aaud.idPrograma = a.idPrograma and aaud.idAuditoria = a.idAuditoria " .
			"left join sia_unidades u on concat(a.idCuenta,a.idSector, a.idSubsector, a.idUnidad)=concat(u.idCuenta,u.idSector, u.idSubsector, u.idUnidad) " .
			"left join sia_empleados e on aaud.idAuditor=e.idEmpleado " .
			"left join sia_fases f on f.idFase=aa.idFase " .
			"left join sia_auditoriasactividades aact  on aa.idActividad = aact.idAuditoriasAuditores " .
			"left join sia_fasesactividades fa  on aact.idFase=fa.idFase and aact.idActividad=fa.idActividad " .
		"Where a.idCuenta=:cuenta and a.idArea=:id " .
		"order by aa.idAvance desc";

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':cuenta' => $cuenta, ':id' => $id));
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}		
	});



	$app->get('/dashauditorias', function()  use($app, $db) {
		$area = $_SESSION["idArea"];
		$sql="SELECT a.idAuditoria auditoria, ar.nombre area, u.nombre sujeto, o.nombre objeto, a.tipoAuditoria tipo, '0.00' avances, a.idProceso proceso, a.idEtapa etapa  
				FROM sia_programas p 
				INNER JOIN sia_auditorias a on p.idCuenta=a.idCuenta and p.idPrograma=a.idPrograma 
				LEFT JOIN sia_areas ar on a.idArea=ar.idArea 
				LEFT JOIN sia_unidades u on a.idCuenta = u.idCuenta and a.idSector=u.idSector and a.idUnidad=u.idUnidad
				LEFT JOIN sia_objetos o on a.idObjeto=o.idObjeto and a.idCuenta=o.idCuenta and a.idPrograma=o.idPrograma and a.idAuditoria=o.idAuditoria
				WHERE ar.idArea=:area
				ORDER BY ar.nombre, u.nombre, o.nombre, a.tipoAuditoria ";

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':area' => $area));
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS.");
		}else{
			$app->render('auditorias.php', $result);
		}
	});


	

	//lista de auditoria-FASE
	$app->get('/lstFasesActividad', function()    use($app, $db) {
			$cuenta = $_SESSION["idCuentaActual"];
		$sql="SELECT idFase id, nombre texto FROM sia_fases WHERE idCuenta=:cuenta ORDER BY  orden";

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':cuenta' => $cuenta));
		$result ['datos']= $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});
	




			//Lista de DES-FASE
	$app->get('/lstAuByfase/:fase', function($fase)    use($app, $db) {
		
		$sql=" SELECT aa.idFase fase, fa.nombre actividad, aa.fInicio inicio, aa.fFin fin, aa.porcentaje, aa.idPrioridad prioridad
			FROM sia_auditoriasactividades aa inner join sia_fasesactividades fa on aa.idFase = fa.idFase and aa.idActividad = fa.idActividad
			WHERE aa.idAuditoria=:auditoria and aa.idFase=:fase";
	

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':auditoria' => $auditoria, ':fase' => $fase));
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});




			//Lista de DES-FASE
	$app->get('/lstFaseByDescripciones', function()    use($app, $db) {
		$cuenta = $_SESSION["idCuentaActual"];

		$sql="SELECT idActividad id , nombre texto FROM sia_fasesactividades WHERE  idCuenta=:cuenta  and estatus='ACTIVO' ORDER BY idActividad";
	

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':cuenta' => $cuenta));
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});







		$app->get('/lstFaseByDescripcion/:fase', function($fase)    use($app, $db) {
		$cuenta = $_SESSION["idCuentaActual"];

		$sql="SELECT idActividad id , nombre texto FROM sia_fasesactividades WHERE  idCuenta=:cuenta AND  idFase=:fase and estatus='ACTIVO' ORDER BY idActividad";
	

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':cuenta' => $cuenta, ':fase' => $fase));
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});



	//lista de responsable-auditor
		$app->get('/lstResponByauditor/:area', function($area)    use($app, $db) {
		$cuenta = $_SESSION["idCuentaActual"];
		$area = $_SESSION["idArea"];


		$sql="SELECT idEmpleado id, idArea, concat(nombre, ' ', paterno, ' ', materno) texto
  			FROM sia_empleados WHERE idArea=:area 
  			ORDER BY concat(nombre, ' ', paterno, ' ', materno);";
		
		// $sql=" SELECT e.idEmpleado id, e.idArea, concat(e.nombre, ' ', e.paterno, ' ', e.materno) texto
		// FROM sia_empleados e
		// LEFT JOIN sia_plazas p on e.idPlaza=p.idPlaza
		// WHERE e.idArea=:area AND e.idNivel in ('1.1', '2.1', '3.1', '4.1', '5.1')
		// ORDER BY concat(e.nombre, ' ', e.paterno, ' ', e.materno) ";

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':area' => $area));
		$result ['datos']= $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});


		//lista de responsable-auditor
		$app->get('/lstResponByauditores', function()    use($app, $db) {
		
		
		$sql=" SELECT e.idEmpleado id, e.idArea, concat(e.nombre, ' ', e.paterno, ' ', e.materno) texto
		FROM sia_empleados e
		LEFT JOIN sia_plazas p on e.idPlaza=p.idPlaza
		WHERE e.idNivel in ('1.1', '2.1', '3.1', '4.1', '5.1')
		ORDER BY concat(e.nombre, ' ', e.paterno, ' ', e.materno) ";

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute();
		$result = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});


		//lista de actividades por auditoria
	$app->get('/tblActividadesByAuditoria/:id', function($id)    use($app, $db) {
		$sql="SELECT aa.idAuditoria audi,aa.idFase fase, aa.actividad, ap.nombre ,aa.fInicio inicio, aa.fFin fin, aa.diasEfectivos efectivos,aa.porcentaje, aa.idPrioridad prioridad, aa.idActividad idA " .
			"FROM sia_auditoriasactividades aa " .
  			"INNER JOIN sia_apartados ap on aa.idApartado = ap.idApartado " .
  			"INNER JOIN sia_fases f on aa.idFase=f.idFase " .
			"WHERE aa.idAuditoria=:id ORDER BY f.orden";
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':id' => $id));
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	}); 

	

	//lista de actividades por auditoria
	$app->get('/lstActividades/:id', function($id)    use($app, $db) {
		$sql=" SELECT aa.idAuditoria audi,aa.idFase fase, fa.nombre actividad, aa.fInicio inicio, aa.fFin fin, aa.diasEfectivos efectivos,aa.porcentaje, aa.idPrioridad prioridad, aa.idActividad idA
			FROM sia_auditoriasactividades aa 
      		INNER JOIN sia_fasesactividades fa on aa.idFase = fa.idFase and aa.idActividad = fa.idActividad 
      		INNER JOIN sia_fases f on fa.idFase=f.idFase
      		WHERE fa.idActividad='1'";
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':id' => $id));
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	}); 








	//lista de actividades por auditoria
	$app->get('/tblActividadesByFases/:fase', function($fase)    use($app, $db) {
		$sql="SELECT aa.idFase texto, fa.nombre actividad, aa.fInicio inicio, aa.fFin fin, aa.porcentaje, aa.idPrioridad prioridad  " .
			"FROM sia_auditoriasactividades aa inner join sia_fasesactividades fa on aa.idFase = fa.idFase and aa.idActividad = fa.idActividad ".
			"WHERE aa.idFase=:fase";
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':fase' => $fase));
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	}); 


	//Lista actividad actividadPrevia
	$app->get('/lstActividaByPrevia/:auditoria', function($auditoria)    use($app, $db) {
		$cuenta = $_SESSION["idCuentaActual"];
		


		$sql="SELECT aa.idActividad id, fa.nombre texto FROM sia_auditoriasactividades aa " .
		"inner join sia_fasesactividades fa on aa.idFase = fa.idFase and aa.idActividad = fa.idActividad " .
			"WHERE fa.idCuenta=:cuenta and aa.idAuditoria=:auditoria";
		

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':cuenta' => $cuenta, ':auditoria' => $auditoria));
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});


//lista sectores-auditorias
	$app->get('/lstSectoresaudi', function()    use($app, $db) {
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





	//Lista de subsector-unidad
	$app->get('/lstSubsectoresByunidad/:sector', function($sector)    use($app, $db) {
		$cuenta = $_SESSION["idCuentaActual"];

		$sql="SELECT ltrim(idSubsector) id, ltrim(nombre) texto FROM sia_Unidades Where idCuenta=:cuenta and idSector=:sector ORDER BY nombre";
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':cuenta' => $cuenta, ':sector' => $sector));
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});






$app->get('/lstAuditoriaByAr/:id', function($id)    use($app, $db) {
		$dbQuery = $db->prepare($sql);
		$sql="SELECT idCuenta cuenta, idPrograma programa, idAuditoria auditoria,  tipoAuditoria tipo, idArea area, CONCAT(idSector,' ', idSubsector,' ', idUnidad) unidad, idObjeto objeto, objetivo, alcance, justificacion, idProceso proceso, idEtapa etapa  FROM sia_auditorias  WHERE idAuditoria=:id";


		$dbQuery->execute(array(':id' => $id));
		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});

	//lista de Subsectores
	$app->get('/lstSubsectores', function()    use($app, $db) {
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

//lista de objetos
		$app->get('/lstObjetosByEnables/', function()  use($app, $db) {

		$sql = "SELECT idObjeto id, nombre texto FROM sia_objetos Where idObjeto not in (Select idObjeto from sia_auditorias) order by nivel, nombre";

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute();
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON OBJETOS DE FISCALIZACIÓN. ");
		}else{
			echo json_encode($result);
		}
	});


	//Listar objetos by unidad
	$app->get('/lstObjetosByUnidad/:id', function($id)  use($app, $db) {

		$sql="SELECT idObjeto id, nombre texto FROM sia_objetos Where idUnidad=:id order by nombre";

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':id' => $id));
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON OBJETOS DE FISCALIZACIÓN. ");
		}else{
			echo json_encode($result);
		}
	});

//guardar auditorias-auditores
$app->get('/guardar/equipo/lider/:oper/:datos',  function($oper, $datos)  use($app, $db) {
		$usrActual = $_SESSION["idUsuario"];
		$cuenta = $_SESSION["idCuentaActual"];
		$programa = $_SESSION["idProgramaActual"];
		$ciclo=0;
		try{
			if($datos<>""){
			
			$registros = explode("*", $datos);
			foreach($registros as $registro) {     
		     	$campo = explode("|", $registro);
				$cuenta=$campo[0];
				$programa=$campo[1];
				$auditoria=$campo[2];
				$empleado=$campo[3];
				$lider=$campo[4];
				echo $datos;

				if($ciclo==0) {
					//Elimina
					$sql="DELETE FROM sia_auditoriasauditores WHERE idCuenta=:cuenta and idPrograma=:programa and idAuditoria=:auditoria";
					$dbQuery = $db->prepare($sql);
					$dbQuery->execute(array(':cuenta' => $cuenta,':programa' => $programa,':auditoria' => $auditoria));
					$ciclo=1;


				}
				//Inserta		
				$sql="INSERT INTO sia_auditoriasauditores (idCuenta, idPrograma, idAuditoria, idAuditor, lider, usrAlta, fAlta, estatus) " .
						"values(:cuenta, :programa, :auditoria, :empleado,:lider,:usrAlta,getdate(),'ACTIVO')";
				$dbQuery = $db->prepare($sql);

				$dbQuery->execute(array(':cuenta' => $cuenta,':programa' => $programa,':auditoria' => $auditoria,':empleado' => $empleado,':lider' => $lider,':usrAlta' => $usrActual));
    
		    }			

		    echo "OK";

		}
		else{
			echo ("NO");
		}
	}catch (PDOException $e) {
			print "<br>¡Error en el TRY!: " . $e->getMessage();
			die();
		}

	});



//guardar actividad auditor
$app->get('/guardar/auditoria/actividad/:oper/:cadena',  function($oper, $cadena)  use($app, $db) {
		$datos= $cadena;
		$usrActual = $_SESSION["idUsuario"];
		
		try{
			if($datos<>""){
				$dato = explode("|", $datos);
				$cuenta=$dato[0];
				$programa=$dato[1];
				$auditoria=$dato[2];
				$actividad=$dato[3];
				$fase=$dato[4];
				$idapartado=$dato[5];
				$activida=$dato[6];
				
				$inicio = date_create($dato[7]);
				$inicio = $inicio->format('Y-m-d');

				$fin = date_create($dato[8]);
				$fin = $fin->format('Y-m-d');

				$activi=$dato[9];


				$porcentaje=$dato[10];
				$prioridad=$dato[11];
				$impacto=$dato[12];
				$responsable=$dato[13];
				$estatus=$dato[14];
				$notas=strtoupper($dato[15]);





				if ($oper=='INS-ACT')
				{

					if($inicio=='' || $fin==''){
						$sql="INSERT INTO sia_auditoriasactividades (" .
						"idCuenta, idPrograma, idAuditoria, idFase, idTipo, fInicio,fFin, porcentaje, idPrioridad, idImpacto, notas, idResponsable, usrAlta, fAlta, estatus,diasEfectivos, actividad,idApartado) " .
						"values(:cuenta, :programa, :auditoria, :fase,'SIMPLE','', '',:porcentaje, :prioridad, :impacto,:notas,:responsable,:usrAlta,getdate(),'ACTIVO',:activi,:activida,:idapartado)";

						$dbQuery = $db->prepare($sql);

						$dbQuery->execute(array(':cuenta' => $cuenta,':programa' => $programa,':auditoria' => $auditoria,':fase' => $fase,':porcentaje' => $porcentaje,':prioridad' => $prioridad,':impacto' => $impacto,':notas' => $notas,':responsable' => $responsable,':usrAlta' => $usrActual,':activi' => $activi,':activida' => $activida,':idapartado' => $idapartado));

					echo "GLOBAL-AREA <hr> $sql <br> <br>:cuenta=$cuenta  programa=$programa auditoria=$auditoria fase=$fase inicio=$inicio fin=$fin porcentaje=$porcentaje  prioridad=$prioridad impacto=$impacto notas=$notas responsable =$responsable usrAlta=$usrActual activi=$activi actividad=$actividad idapartado=$idapartado";

					}else{

					$sql="INSERT INTO sia_auditoriasactividades (" .
					"idCuenta, idPrograma, idAuditoria, idFase, idTipo, fInicio,fFin, porcentaje, idPrioridad, idImpacto, notas, idResponsable, usrAlta, fAlta, estatus,diasEfectivos, actividad,idApartado) " .
					"values(:cuenta, :programa, :auditoria, :fase,'SIMPLE',:inicio, :fin,:porcentaje, :prioridad, :impacto,:notas,:responsable,:usrAlta,getdate(),'ACTIVO',:activi,:activida,:idapartado)";

					$dbQuery = $db->prepare($sql);

					$dbQuery->execute(array(':cuenta' => $cuenta,':programa' => $programa,':auditoria' => $auditoria,':fase' => $fase,':inicio' => $inicio,':fin' => $fin,':porcentaje' => $porcentaje,':prioridad' => $prioridad,':impacto' => $impacto,':notas' => $notas,':responsable' => $responsable,':usrAlta' => $usrActual,':activi' => $activi,':activida' => $activida,':idapartado' => $idapartado));

					echo "GLOBAL <hr> $sql <br> <br>:cuenta=$cuenta  programa=$programa auditoria=$auditoria fase=$fase inicio=$inicio fin=$fin porcentaje=$porcentaje  prioridad=$prioridad impacto=$impacto notas=$notas responsable =$responsable usrAlta=$usrActual activi=$activi actividad=$actividad idapartado=$idapartado";
					}

					//echo "OK";
				}
			}
			else{
				echo ("NO");
			}
		}catch (Exception $e) {
				print "<br>¡Error en el TRY!: " . $e->getMessage();
				die();
			}



	});










/*	//guardar actividad auditor
	$app->get('/guardar/auditoria/actividad/:oper/:cadena',  function($oper, $cadena)  use($app, $db) {
		$datos= $cadena;
		$usrActual = $_SESSION["idUsuario"];
		
		try{
			if($datos<>""){
				$dato = explode("|", $datos);
				$cuenta=$dato[0];
				$programa=$dato[1];
				$auditoria=$dato[2];
				$actividad=$dato[3];
				$fase=$dato[4];
				$idapartado=$dato[5];
				$activida=$dato[6];
				
				$inicio = date_create($dato[7]);
				$inicio = $inicio->format('Y-m-d');

				$fin = date_create($dato[8]);
				$fin = $fin->format('Y-m-d');

				$activi=$dato[9];


				$porcentaje=$dato[10];
				$prioridad=$dato[11];
				$impacto=$dato[12];
				$responsable=$dato[13];
				$estatus=$dato[14];
				$notas=strtoupper($dato[15]);





				if ($oper=='INS-ACT')
				{

					if($inicio=='' || $fin==''){
						$sql="INSERT INTO sia_auditoriasactividades (" .
						"idCuenta, idPrograma, idAuditoria, idFase, idTipo, fInicio,fFin, porcentaje, idPrioridad, idImpacto, notas, idResponsable, usrAlta, fAlta, estatus,diasEfectivos, actividad,idApartado) " .
						"values(:cuenta, :programa, :auditoria, :fase,'SIMPLE','', '',:porcentaje, :prioridad, :impacto,:notas,:responsable,:usrAlta,getdate(),'ACTIVO',:activi,:activida,:idapartado)";

						$dbQuery = $db->prepare($sql);

						$dbQuery->execute(array(':cuenta' => $cuenta,':programa' => $programa,':auditoria' => $auditoria,':fase' => $fase,':porcentaje' => $porcentaje,':prioridad' => $prioridad,':impacto' => $impacto,':notas' => $notas,':responsable' => $responsable,':usrAlta' => $usrActual,':activi' => $activi,':activida' => $activida,':idapartado' => $idapartado));

					echo "GLOBAL-AREA <hr> $sql <br> <br>:cuenta=$cuenta  programa=$programa auditoria=$auditoria fase=$fase inicio=$inicio fin=$fin porcentaje=$porcentaje  prioridad=$prioridad impacto=$impacto notas=$notas responsable =$responsable usrAlta=$usrActual activi=$activi actividad=$actividad idapartado=$idapartado";

					}else{

					$sql="INSERT INTO sia_auditoriasactividades (" .
					"idCuenta, idPrograma, idAuditoria, idFase, idTipo, fInicio,fFin, porcentaje, idPrioridad, idImpacto, notas, idResponsable, usrAlta, fAlta, estatus,diasEfectivos, actividad,idApartado) " .
					"values(:cuenta, :programa, :auditoria, :fase,'SIMPLE',:inicio, :fin,:porcentaje, :prioridad, :impacto,:notas,:responsable,:usrAlta,getdate(),'ACTIVO',:activi,:activida,:idapartado)";

					$dbQuery = $db->prepare($sql);

					$dbQuery->execute(array(':cuenta' => $cuenta,':programa' => $programa,':auditoria' => $auditoria,':fase' => $fase,':inicio' => $inicio,':fin' => $fin,':porcentaje' => $porcentaje,':prioridad' => $prioridad,':impacto' => $impacto,':notas' => $notas,':responsable' => $responsable,':usrAlta' => $usrActual,':activi' => $activi,':activida' => $activida,':idapartado' => $idapartado));

					echo "GLOBAL <hr> $sql <br> <br>:cuenta=$cuenta  programa=$programa auditoria=$auditoria fase=$fase inicio=$inicio fin=$fin porcentaje=$porcentaje  prioridad=$prioridad impacto=$impacto notas=$notas responsable =$responsable usrAlta=$usrActual activi=$activi actividad=$actividad idapartado=$idapartado";
					}

					//echo "OK";
				}
			}
			else{
				echo ("NO");
			}
		}catch (Exception $e) {
				print "<br>¡Error en el TRY!: " . $e->getMessage();
				die();
			}
	});*/




	$app->get('/actualizar/auditoria/actividad/:oper/:cadena',  function($oper, $cadena)  use($app, $db) {
		$datos= $cadena;
		$usrActual = $_SESSION["idUsuario"];
		
		try{
			if($datos<>""){
				$dato = explode("|", $datos);
				$cuenta=$dato[0];
				$programa=$dato[1];
				$auditoria=$dato[2];
				$actividad=$dato[3];
				$fase=$dato[4];
				$idapartado=$dato[5];
				$activida=$dato[6];
				
				$inicio = date_create($dato[7]);
				$inicio = $inicio->format('Y-m-d');

				$fin = date_create($dato[8]);
				$fin = $fin->format('Y-m-d');

				$activi=$dato[9];
				$porcentaje=$dato[10];
				$prioridad=$dato[11];
				$impacto=$dato[12];
				$responsable=$dato[13];
				$estatus=$dato[14];
				$notas=strtoupper($dato[15]);
			

				if ($oper=='UPD')
				{
					
					$sql="UPDATE sia_auditoriasactividades SET idFase=:fase, fInicio=:inicio,fFin=:fin, porcentaje=:porcentaje, idPrioridad=:prioridad,idImpacto=:impacto, notas=:notas, idResponsable=:responsable, usrModificacion=:usrAlta, fModificacion=getdate() ,estatus=:estatus,diasEfectivos=:activi, actividad=:activida,idApartado=:idapartado WHERE idActividad =:actividad;";


					$dbQuery = $db->prepare($sql);

					$dbQuery->execute(array(':fase' => $fase,':inicio' => $inicio,':fin' => $fin,':porcentaje' => $porcentaje,':prioridad' => $prioridad,':impacto' => $impacto,':notas' => $notas,':responsable' => $responsable,':usrAlta' => $usrActual,':estatus' => $estatus,':activi' => $activi,':actividad' => $actividad,':activida' => $activida,':idapartado' => $idapartado));
					//echo "GLOBAL-AREA <hr> $sql <br> <br>:cuenta=$cuenta  programa=$programa auditoria=$auditoria fase=$fase inicio=$inicio fin=$fin porcentaje=$porcentaje  prioridad=$prioridad impacto=$impacto notas=$notas responsable =$responsable usrAlta=$usrActual activi=$activi actividad=$actividad idapartado=$idapartado";
					echo "OK";
				}
				else {

				}
			}
			else{
				echo ("NO");
			}
		}catch (Exception $e) {
				print "<br>¡Error en el TRY!: " . $e->getMessage();
				die();
			}



	});








//guardar Notas, placeholder
$app->get('/guardar/auditoria/Notas/Place/:cadena',  function($cadena)  use($app, $db) {
		$datos= $cadena;
		
		try{
			if($datos<>""){
				$dato = explode("|", $datos);
				$auditoria=$dato[0];
				
				$FConfron = date_create($dato[1]);
				$FConfron = $FConfron->format('Y-m-d');
				
				$ReConfron=$dato[2];				

				$FIRA = date_create($dato[3]);
				$FIRA = $FIRA->format('Y-m-d');

				$FIFA = date_create($dato[4]);
				$FIFA = $FIFA->format('Y-m-d');

				$latitud=$dato[5];
				$longitud=$dato[6];
				$TipoObs=$dato[7];
				$observacion=strtoupper($dato[8]);
				
				$FAInicio = date_create($dato[9]);
				$FAInicio = $FAInicio->format('Y-m-d');

				$FAFinal = date_create($dato[10]);
				$FAFinal = $FAFinal->format('Y-m-d');

				$Dirrespon = $dato[11];
				//$respon = $dato[8];
				//echo $dato;
			


				$sql="UPDATE sia_auditorias SET fModificacion=getdate(), latitud=:latitud, longitud=:longitud, tipoObservacion=:TipoObs, observacion=:observacion, fInicio=:FAInicio,fFin=:FAFinal,fConfronta=:FConfron, resumenConfronta=:ReConfron, fIRA=:FIRA, fIFA=:FIFA
					WHERE idAuditoria=:auditoria";

					$dbQuery = $db->prepare($sql);

					$dbQuery->execute(array(':latitud' => $latitud,':longitud' => $longitud, ':TipoObs' => $TipoObs, ':observacion' => $observacion,':auditoria' => $auditoria, ':FAInicio' => $FAInicio,':FAFinal' => $FAFinal, ':FConfron' => $FConfron,':ReConfron' => $ReConfron, ':FIRA' => $FIRA,':FIFA' => $FIFA));

					echo "OK";
				
			}
			else{
				echo ("NO");
			}

		
		}catch (Exception $e) {
				print "<br>¡Error en el TRY!: " . $e->getMessage();
				die();
			}
	});









 $app->get('/tblAuditorias', function()    use($app, $db) {
 		$cuenta = $_SESSION["idCuentaActual"];
 		$area = $_SESSION["idArea"];
		
 		$usrActual = $_SESSION["idUsuario"];
 		$global = $_SESSION["usrGlobal"];
 		$programa = $_SESSION["idProgramaActual"];

 		if ($global=="SI"){
			
 			$sql="SELECT a.area, a.finan, a.fincum, a.cumpli, a.obrapub, a.desem, (a.finan + a.fincum + a.cumpli + a.obrapub + a.desem) as total FROM (" .
					"SELECT a.idArea, ar.nombre area, " .
       				"finan= ISNULL((Select COUNT(*) from sia_auditorias where tipoAuditoria='FINANCIERA' and idArea=a.idArea),0), " .
       				"fincum= ISNULL((Select COUNT(*) from sia_auditorias where tipoAuditoria='FINAN-CUMPL' and idArea=a.idArea),0), " .
       				"cumpli= ISNULL((Select COUNT(*) from sia_auditorias where tipoAuditoria='CUMPLIMIENTO' and idArea=a.idArea),0), " .
       				"obrapub= ISNULL((Select COUNT(*) from sia_auditorias where tipoAuditoria='OBRA' and idArea=a.idArea),0), " .
       				"desem= ISNULL((Select COUNT(*) from sia_auditorias where tipoAuditoria='DESEMPENO' and idArea=a.idArea),0) " .
       				"FROM  sia_auditorias a LEFT JOIN sia_areas ar on a.idArea = ar.idArea " .
       				"WHERE a.idCuenta=:cuenta and a.idprograma=:programa and a.idEtapa in (Select idEtapa from sia_rolesetapas re inner join sia_usuariosroles ur on ur.idRol = re.idRol  Where ur.idUsuario=:usrActual) " .
   					"GROUP BY a.idArea, ar.nombre) a";
				
 				$dbQuery = $db->prepare($sql);
 				$dbQuery->execute(array(':cuenta' => $cuenta, ':programa' => $programa, ':usrActual' => $usrActual));
 		}else{

 			$sql="SELECT a.area, a.finan, a.fincum, a.cumpli, a.obrapub, a.desem, (a.finan + a.fincum + a.cumpli + a.obrapub + a.desem) as total FROM (" .
					"SELECT a.idArea, ar.nombre area, " .
       				"finan= ISNULL((Select COUNT(*) from sia_auditorias where tipoAuditoria='FINANCIERA' and idArea=a.idArea),0), " .
       				"fincum= ISNULL((Select COUNT(*) from sia_auditorias where tipoAuditoria='FINAN-CUMPL' and idArea=a.idArea),0), " .
       				"cumpli= ISNULL((Select COUNT(*) from sia_auditorias where tipoAuditoria='CUMPLIMIENTO' and idArea=a.idArea),0), " .
       				"obrapub= ISNULL((Select COUNT(*) from sia_auditorias where tipoAuditoria='OBRA' and idArea=a.idArea),0), " . 
       				"desem= ISNULL((Select COUNT(*) from sia_auditorias where tipoAuditoria='DESEMPENO' and idArea=a.idArea),0) " .
       				"FROM  sia_auditorias a LEFT JOIN sia_areas ar on a.idArea = ar.idArea " .
       				"WHERE a.idCuenta=:cuenta and a.idprograma=:programa and a.idArea=:area and a.idEtapa in (Select idEtapa from sia_rolesetapas re inner join sia_usuariosroles ur on ur.idRol = re.idRol  Where ur.idUsuario=:usrActual) " .
   					"GROUP BY a.idArea, ar.nombre) a";
				
 				$dbQuery = $db->prepare($sql);
 				$dbQuery->execute(array(':cuenta' => $cuenta, ':programa' => $programa, ':area' => $area,':usrActual' => $usrActual));		
 		}

 		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
 		echo json_encode($result);
 	});



	$app->get('/lstAuditoriaByIds/:id', function($id)    use($app, $db) {
		$cuenta = $_SESSION["idCuentaActual"];
		$area = $_SESSION["idArea"];
		
		$usrActual = $_SESSION["idUsuario"];
		$global = $_SESSION["usrGlobal"];
		/*
		$sql="SELECT idCuenta cuenta, idPrograma programa, idAuditoria auditoria,  tipoAuditoria tipo, idArea area, idSector sector, idSubsector subSector, idUnidad unidad, idObjeto objeto, objetivo, alcance, justificacion, tipoPresupuesto, acompanamiento, idProceso proceso, idEtapa etapa  FROM sia_auditorias  WHERE idAuditoria=:id ";
		*/
		if ($global=="SI"){

			$sql="SELECT a.idCuenta cuenta, a.idPrograma programa, a.idAuditoria auditoria,  COALESCE(convert(varchar(20),a.clave),convert(varchar(20),a.idAuditoria)) claveAuditoria, isnull(a.clave,'') clave, a.tipoAuditoria tipo, a.idArea area, a.idSector sector, a.idSubsector subSector, a.idUnidad unidad, a.idObjeto objeto, a.objetivo, a.alcance, a.justificacion, a.tipoPresupuesto, a.acompanamiento, a.idProceso proceso, a.idEtapa etapa, REPLACE(ISNULL(CONVERT(VARCHAR(10),a.fInicio,103), ''), '1900-01-01', '') feInicio, REPLACE(ISNULL(CONVERT(VARCHAR(10),a.fFin,103), ''), '1900-01-01', '') feFin, a.latitud, a.longitud, a.tipoObservacion tipoObse, a.observacion,a.idResponsable responsable, a.idSubresponsable subresponsable 
				FROM sia_auditorias a INNER JOIN sia_unidades u ON concat(a.idCuenta,a.idSector, a.idSubsector, a.idUnidad) = CONCAT(u.idCuenta,u.idSector, u.idSubsector, u.idUnidad) LEFT JOIN  sia_objetos o ON a.idObjeto = o.idObjeto WHERE  a.idCuenta=:cuenta AND a.idAuditoria =:id AND a.idEtapa in (SELECT idEtapa FROM sia_rolesetapas re INNER JOIN sia_usuariosroles ur ON ur.idRol = re.idRol  WHERE ur.idUsuario=:usrActual) ";
				
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':cuenta' => $cuenta, ':id' => $id, ':usrActual' => $usrActual));
		}else{

			$sql="SELECT a.idCuenta cuenta, a.idPrograma programa, a.idAuditoria auditoria,  COALESCE(convert(varchar(20),a.clave),convert(varchar(20),a.idAuditoria)) claveAuditoria, isnull(a.clave,'') clave, a.tipoAuditoria tipo, a.idArea area, a.idSector sector, a.idSubsector subSector, a.idUnidad unidad, a.idObjeto objeto, a.objetivo, a.alcance, a.justificacion, a.tipoPresupuesto, a.acompanamiento, a.idProceso proceso, a.idEtapa etapa, REPLACE(ISNULL(CONVERT(VARCHAR(10),a.fInicio,103), ''), '1900-01-01', '') feInicio, REPLACE(ISNULL(CONVERT(VARCHAR(10),a.fFin,103), ''), '1900-01-01', '') feFin, a.latitud, a.longitud, a.tipoObservacion tipoObse, a.observacion,a.idResponsable responsable, a.idSubresponsable subresponsable 
				FROM sia_auditorias a INNER JOIN sia_unidades u ON concat(a.idCuenta,a.idSector, a.idSubsector, a.idUnidad) = CONCAT(u.idCuenta,u.idSector, u.idSubsector, u.idUnidad) LEFT JOIN  sia_objetos o ON a.idObjeto = o.idObjeto WHERE  a.idCuenta=:cuenta AND a.idArea =:area AND a.idAuditoria =:id AND a.idEtapa in (SELECT idEtapa FROM sia_rolesetapas re INNER JOIN sia_usuariosroles ur ON ur.idRol = re.idRol  WHERE ur.idUsuario=:usrActual) ";
				
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






	$app->get('/lstObjetos/:auditoria/:objeto', function($auditoria, $objeto)    use($app, $db) {
		$cuenta = $_SESSION["idCuentaActual"];
		$programa = $_SESSION["idProgramaActual"];
		//$area = $_SESSION["idArea"];
		
		$sql="SELECT ltrim(idObjeto) id, nombre texto FROM sia_objetos WHERE idCuenta=:cuenta and idAuditoria=:auditoria and idPrograma=:programa and idObjeto=:objeto";
	
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':cuenta' => $cuenta, ':auditoria' => $auditoria, ':programa' => $programa, ':objeto' => $objeto));
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});



$app->get('/porecentaje/:datos',  function($datos)    use($app, $db) {
	

	if($datos<>""){
			$dato = explode("|",$datos);
			$auditoria =$dato[0];
			$fase = strtoupper ($dato[1]);
			$efectivos =(float)$dato[2];
			$efectivos1 =(float)$dato[2];
			//echo $datos;		

		
   
			$sql="SELECT format(((convert(DECIMAL(20,2),:efectivos)/sum(a.suma))*100),'N','en-us') total FROM (
			SELECT  idAuditoria, idFase, (sum(convert(DECIMAL(20,2),diasEfectivos)) + convert(DECIMAL(20,2),:efectivos1)) suma FROM sia_auditoriasactividades  GROUP BY idAuditoria, idFase) a WHERE a.idAuditoria=:auditoria  AND a.idFase=:fase GROUP BY a.idFase, a.suma,a.idAuditoria";

			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':efectivos' => $efectivos,':efectivos1' => $efectivos1,':auditoria' => $auditoria, ':fase' => $fase));
			$result = $dbQuery->fetch(PDO::FETCH_ASSOC);
			echo json_encode($result);
	}
		
});





$app->get('/reportenota/:id', function($id)    use($app, $db) {

	$sql="SELECT r.idReporte, r.nombre sReporte, r.idModulo sModulo, r.archivo

		from sia_reportes r  WHERE r.idReporte=:id";

	$dbQuery = $db->prepare($sql);
	$dbQuery->execute(array(':id' => $id));
	$result = $dbQuery->fetch(PDO::FETCH_ASSOC);
	echo json_encode($result);		
});




$app->get('/lstAreasSubResponsablesByResponsable/:res', function($res)  use($app, $db) {
	$area = $_SESSION["idArea"];

	$sql="SELECT idSubresponsable id, nombre texto FROM sia_areassubresponsables WHERE idArea=:area AND idResponsable=:res;";

	$dbQuery = $db->prepare($sql);
	$dbQuery->execute(array(':area' => $area,':res' => $res));
	$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
	echo json_encode($result);		

});



	//Listar responsables de cada area
	$app->get('/lstSubResponsables', function()  use($app, $db) {
		$area = $_SESSION["idArea"];
		$global = $_SESSION["usrGlobal"];
		
		if ($global=="SI"){			
			$sql="SELECT idSubresponsable id, nombre texto FROM sia_areassubresponsables ORDER BY nombre asc";
			$dbQuery = $db->prepare($sql);
			$dbQuery->execute();		
		}else{
			$sql="SELECT idSubresponsable id, nombre texto FROM sia_areassubresponsables WHERE idArea=:area ORDER BY nombre asc";
			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':area' => $area));	
		}
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		echo json_encode($result);
	});	

        




// CatAuditores///////////////////////////////////////


$app->get('/catAuditores', function()    use($app, $db) {
 		$cuenta = $_SESSION["idCuentaActual"];
 		$area = $_SESSION["idArea"];
		
 		$usrActual = $_SESSION["idUsuario"];
 		$global = $_SESSION["usrGlobal"];
 		$programa = $_SESSION["idProgramaActual"];

 		if ($global=="SI"){
			
 			$sql="SELECT em.idEmpleado id, us.idUsuario,  CONCAT(em.nombre,' ',em.paterno,' ',em.materno) nombre, ar.nombre area, pl.nombre puesto, em.estatus
					FROM sia_empleados em
    				LEFT JOIN sia_usuarios us on em.idEmpleado = us.idEmpleado 
					LEFT JOIN sia_areas ar on em.idArea = ar.idArea
					LEFT JOIN sia_plazas pl on em.idPlaza = pl.idPlaza
    				WHERE em.idNivel in ('1.1', '2.1', '3.1', '4.1', '5.1'); ";
 
				
 				$dbQuery = $db->prepare($sql);
 				$dbQuery->execute();
 		}else{

 			$sql="SELECT em.idEmpleado  id, us.idUsuario,  CONCAT(em.nombre,' ',em.paterno,' ',em.materno) nombre, ar.nombre area, pl.nombre puesto, em.estatus
					FROM sia_empleados em
    				LEFT JOIN sia_usuarios us on em.idEmpleado = us.idEmpleado 
					LEFT JOIN sia_areas ar on em.idArea = ar.idArea
					LEFT JOIN sia_plazas pl on em.idPlaza = pl.idPlaza
    				WHERE em.idArea=:area and em.idNivel in ('1.1', '2.1', '3.1', '4.1', '5.1'); ";
				
 				$dbQuery = $db->prepare($sql);
 				$dbQuery->execute(array(':area' => $area));		
 		}

 		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
	$app->render('catAuditores.php', $result);

})->name('listaAuditores');




// obtener auditores
$app->get('/AuditoresById/:id', function($id)    use($app, $db) {

	$sql="SELECT a.idAuditoria auditoria, em.idEmpleado empleado, em.nombre, em.paterno, em.materno, em.idPlaza puesto, em.estatus FROM sia_empleados em  LEFT JOIN sia_auditorias a on em.idArea = a.idArea WHERE em.idEmpleado=:id";
	$dbQuery = $db->prepare($sql);
	$dbQuery->execute(array(':id' => $id));
	$result = $dbQuery->fetch(PDO::FETCH_ASSOC);
	echo json_encode($result);		
});



//Lista de Auditores
$app->get('/xxx', function()    use($app, $db) {

	$sql="SELECT idPlaza id, nombre texto FROM sia_Plazas order by nombre";

	$dbQuery = $db->prepare($sql);
	$dbQuery->execute();
	$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
	if(!$result){
		$app->halt(404, "NO SE ENCONTRARON DATOS ");
	}else{
		echo json_encode($result);
	}
});

$app->get('/estatus', function()    use($app, $db) {

	$sql="SELECT idEmpleado id, estatus texto FROM sia_empleados";

	$dbQuery = $db->prepare($sql);
	$dbQuery->execute();
	$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
	if(!$result){
		$app->halt(404, "NO SE ENCONTRARON DATOS ");
	}else{
		echo json_encode($result);
	}
});


//lista de auditoria
$app->get('/lstAsignadasByAuditores/:empleado', function( $empleado)    use($app, $db) {
	$area = $_SESSION["idArea"];
	$cuenta = $_SESSION["idCuentaActual"];

	  $sql="SELECT a.idAuditoria auditoria, Case When e.idEmpleado=aa.idAuditor  Then 'SI' Else 'NO' End As asignado, e.idEmpleado, COALESCE(clave, concat('Proy-Aud-',a.idAuditoria)) claveAuditoria, isnull(u.nombre,'') sujeto, isnull(o.nombre,'') objeto, a.tipoAuditoria tipo 
			from sia_empleados e 
			inner join  sia_auditorias a on e.idArea=a.idArea 
			inner join sia_unidades u on concat(a.idCuenta,a.idSector, a.idSubsector, a.idUnidad)=concat(u.idCuenta,u.idSector, u.idSubsector, u.idUnidad) and a.idCuenta = u.idCuenta 
			left join sia_objetos o on a.idObjeto=o.idObjeto 
			left join sia_auditoriasauditores aa on a.idAuditoria = aa.idAuditoria and e.idEmpleado=aa.idAuditor 
			Where a.idCuenta=:cuenta and e.idArea=:area and e.idNivel in ('1.1', '2.1', '3.1', '4.1', '5.1') and e.idEmpleado=:empleado order by a.idAuditoria asc;";

	  	


	$dbQuery = $db->prepare($sql);
	$dbQuery->execute(array(':area' => $area,':cuenta' => $cuenta,':empleado' => $empleado));
	$result ['datos']= $dbQuery->fetchAll(PDO::FETCH_ASSOC);
	if(!$result){
		$app->halt(404, "NO SE ENCONTRARON DATOS ");
	}else{
		echo json_encode($result);
	}
});



//guardar auditorias-auditores
$app->get('/guardar/auditorias/auditor/:oper/:datos',  function($oper, $datos)  use($app, $db) {
		$usrActual = $_SESSION["idUsuario"];
		$cuenta = $_SESSION["idCuentaActual"];
		$programa = $_SESSION["idProgramaActual"];
		$ciclo=0;
		try{
			if($datos<>""){
			


			$registros = explode("*", $datos);
			foreach($registros as $registro) {     
		     	$campo = explode("|", $registro);
				$cuenta=$campo[0];
				$programa=$campo[1];
				$auditoria=$campo[2];
				$empleado=$campo[3];
				echo $datos;

				if($ciclo==0) {
					//Elimina
					$sql="DELETE FROM sia_auditoriasauditores WHERE idCuenta=:cuenta and idPrograma=:programa and idAuditoria=:auditoria and idAuditor=:empleado";
					$dbQuery = $db->prepare($sql);
					$dbQuery->execute(array(':cuenta' => $cuenta,':programa' => $programa,':auditoria' => $auditoria,':empleado' => $empleado));
					$ciclo=1;
				}

				
				

				//Inserta		
				$sql="INSERT INTO sia_auditoriasauditores (idCuenta, idPrograma, idAuditoria, idAuditor, lider, usrAlta, fAlta, estatus) " .
						"values(:cuenta, :programa, :auditoria, :empleado,'',:usrAlta,getdate(),'ACTIVO')";
				$dbQuery = $db->prepare($sql);

				$dbQuery->execute(array(':cuenta' => $cuenta,':programa' => $programa,':auditoria' => $auditoria,':empleado' => $empleado,':usrAlta' => $usrActual));
    
		    }			

		    echo "OK";

		}
		else{
			echo ("NO");
		}
	}catch (PDOException $e) {
			print "<br>¡Error en el TRY!: " . $e->getMessage();
			die();
		}

	});


$app->get('/lstTiposAuditoria/:aud', function($aud)    use($app, $db) {

		$sql="SELECT idTipoAuditoria id, nombre texto FROM sia_tiposAuditoria ta INNER JOIN sia_auditorias au on ta.idTipoAuditoria=au.tipoAuditoria where au.idAuditoria=:aud";
		

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':aud' => $aud));
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		echo json_encode($result);
	});




	
	$app->get('/lstAudiUnida/:audi', function($audi)    use($app, $db) {
		$cuenta = $_SESSION["idCuentaActual"];
		$area = $_SESSION["idArea"];
		
		$sql="SELECT au.idAuditoria audi, CONCAT(un.idsector,un.idSubsector,un.idUnidad) id , un.nombre texto FROM sia_auditoriasunidades au " .
			"INNER JOIN sia_unidades un on au.idSector = un.idSector and au.idSubsector = un.idSubsector and au.idUnidad = un.idUnidad and au.idCuenta = un.idCuenta " .
			"WHERE au.idCuenta=:cuenta and au.idAuditoria=:audi ORDER BY un.nombre;";
		
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':cuenta' => $cuenta, ':audi' => $audi));
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});

	$app->get('/lstAudiUnidades/:audi/:valor', function($audi,$valor)    use($app, $db) {
		$cuenta = $_SESSION["idCuentaActual"];
		
		$sql="SELECT au.idAuditoria id, CONCAT(un.idsector,un.idSubsector,un.idUnidad) ssuu, un.nombre nombre, REPLACE(ISNULL(CONVERT(VARCHAR(10),au.fConfronta,105), ''), '1900-01-01', '') FeCon, isnull(au.resumenConfronta,'') RCon FROM sia_auditoriasunidades au
			INNER JOIN sia_unidades un on  au.idSector = un.idSector and au.idSubsector = un.idSubsector and au.idUnidad = un.idUnidad and au.idCuenta = un.idCuenta and CONCAT(un.idsector,un.idSubsector,un.idUnidad)=:valor
			WHERE au.idCuenta=:cuenta and au.idAuditoria=:audi;";
		
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':cuenta' => $cuenta, ':audi' => $audi, ':valor' => $valor));
		$result= $dbQuery->fetch(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});

	$app->get('/lstmodifUnidades/:audi/:valor', function($audi,$valor)    use($app, $db) {
		$cuenta = $_SESSION["idCuentaActual"];
		
		$sql="SELECT au.idAuditoria id, CONCAT(un.idsector,un.idSubsector,un.idUnidad) ssuu, un.nombre nombre, REPLACE(ISNULL(CONVERT(VARCHAR(10),au.fConfronta,105), ''), '1900-01-01', '') FeCon, isnull(au.resumenConfronta,'') RCon FROM sia_auditoriasunidades au
			INNER JOIN sia_unidades un on  au.idSector = un.idSector and au.idSubsector = un.idSubsector and au.idUnidad = un.idUnidad and au.idCuenta = un.idCuenta and CONCAT(un.idsector,un.idSubsector,un.idUnidad)=:valor
			WHERE au.idCuenta=:cuenta and au.idAuditoria=:audi";
		
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':cuenta' => $cuenta, ':audi' => $audi, ':valor' => $valor));
		$result= $dbQuery->fetch(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});	







	//lista de auditoria-FASE
	$app->get('/lstApartados', function()    use($app, $db) {
		
		$sql="SELECT idApartado id, nombre texto FROM sia_apartados ORDER BY orden asc";

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute();
		$result ['datos']= $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});



	$app->get('/guardar/confron/:cadena',  function($cadena)  use($app, $db) {
		$datos= $cadena;
		$usrActual = $_SESSION["idUsuario"];
		try{
			if($datos<>""){
				$dato = explode("|", $datos);
				$auditoria=$dato[0];
				$sector=$dato[1];
				$sub=$dato[2];
				$unidad=$dato[3];
				$FConfron = date_create($dato[4]);
				$FConfron = $FConfron->format('Y-m-d');

				$ReConfron=$dato[5];				
					
				
				$sql="UPDATE sia_auditoriasunidades SET fConfronta=:FConfron, resumenConfronta=:ReConfron, usrModificacion=:usrActual, fModificacion=getdate() WHERE idSector=:sector and idSubsector=:sub and idUnidad=:unidad and idAuditoria=:auditoria;";

					$dbQuery = $db->prepare($sql);
					$dbQuery->execute(array(':FConfron' => $FConfron,':ReConfron' => $ReConfron,'usrActual' =>$usrActual,':sector' => $sector,':sub' => $sub,':unidad' => $unidad,':auditoria' => $auditoria));

				echo "OK";
				
			}
			else{
				echo ("NO");
			}

		
		}catch (Exception $e) {
				print "<br>¡Error en el TRY!: " . $e->getMessage();
				die();
			}
	});	



	//lista de auditoria fases
	$app->get('/tlbaudiuni/:id', function($id)    use($app, $db) {
		$sql="SELECT au.idAuditoria audi,CONCAT(au.idSector, au.idSubsector, au.idUnidad) val,un.nombre,REPLACE(ISNULL(CONVERT(VARCHAR(10),au.fConfronta,105), ''), '1900-01-01', '') fecha, isnull(au.resumenConfronta,'') resumen FROM sia_auditoriasunidades au " .
 			"INNER JOIN sia_unidades un on au.idCuenta = un.idCuenta and au.idSector=un.idSector and au.idSubsector = un.idSubsector and au.idUnidad = un.idUnidad " .
 			"WHERE au.idAuditoria=:id ORDER BY un.nombre;";

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':id' => $id));
		$result ['datos']= $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});















// auditorias ACCH  FIN


	//Listar rangos de fechas inhabiles
	$app->get('/lstInhabilesByRangos/:f1/:f2', function($f1, $f2)  use($app, $db) {	
		
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
	



$app->get('/lstInhabilesByRango1/:f1/:f2', function($f1, $f2)  use($app, $db) {	
		
		$f1 = date_create($f1);
		$f1 = $f1->format('Y-m-d');

		
		$f2 = date_create($f2);
		$f2 = $f2->format('Y-m-d');		

		try{
			//echo "F1= " . $f1 . " F2= " . $f2;
		$sql="Select 'A' caso , fInicio, fFin from sia_diasinhabiles Where :par1<=fInicio and fInicio<=:par2 and :par3<=fFin " . 
			"Union all " . 
			"Select 'B' caso , fInicio, fFin from sia_diasinhabiles Where fInicio<=:par4 and :par5<=fFin " . 
			"union all " . 
			"Select 'C' caso , fInicio, fFin from sia_diasinhabiles Where fInicio<=:par6 and :par7<=fFin and fFin<:par8 " . 
			"union all " . 
			"Select 'D' caso , fInicio, fFin from sia_diasinhabiles Where :par9<=fInicio and fFin<=:par10 ";
		
			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(
			array(':par1'=> $f1, ':par2'=> $f2, ':par3'=> $f2, 
			':par4'=> $f1, ':par5'=> $f2, 
			':par6'=> $f1, ':par7'=> $f1, ':par8'=> $f2, 
			':par9'=> $f1,':par10'=> $f2 ));
			$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
			//echo "F1:" . $f1 . " F2:" . $f2;
			echo json_encode($result);			
			
			}catch (OException $e) {
			echo  "<br>¡Error en el TRY!: " . $e->getMessage();
			die();
		}
			
			
		
	});		
	
////////////////////////////Fases Actividades


$app->get('/catFasesActividades', function()   use($app, $db) {
		$dbQuery = $db->prepare("SELECT fa.idCuenta, fs.idFase fase, fs.orden ,fa.idActividad id, fa.nombre actividad, fa.estatus estatus FROM sia_fasesactividades fa INNER JOIN sia_fases fs on fa.idFase=fs.idFase ORDER BY fs.orden;");		
		$dbQuery->execute();
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		
		$app->render('catFasesActividades.php', $result);
		
	})->name('listaFasesActividades');





//Obtener un usuario
	$app->get('/RecuperaFase/:id',  function($id)  use($app, $db) {
		$sql="SELECT idCuenta, idActividad id, idFase fase,  nombre actividad,estatus FROM sia_fasesactividades WHERE idActividad=:id";

		$dbQuery = $db->prepare($sql);		
		$dbQuery->execute(array(':id' => $id));
		$result= $dbQuery->fetch(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "RECURSO NO ENCONTRADA.");			
		}else{		
			echo json_encode($result);
		}		
	});




	$app->post('/guardar/faseactividad', function()  use($app, $db) {
			$usrActual = $_SESSION["idUsuario"];
			$cuenta = $_SESSION["idCuentaActual"];
			$request=$app->request;
			$oper = $request->post('txtOperacion');
			$idactividad = $request->post('txtIDActividad');
			$fase = strtoupper($request->post('txtFase'));
			$actividad = strtoupper($request->post('txtNomActividad'));			
			$estatus = $request->post('txtEstatus');
		
		try{
	
			if($oper=='INS'){
				$sql="INSERT INTO sia_fasesactividades (idCuenta, idFase, nombre, usrAlta, fAlta, estatus) " .
						"VALUES (:cuenta, :fase, :actividad, :usrActual, getdate(), :estatus);";
	
					$dbQuery = $db->prepare($sql);

					$dbQuery->execute(array(':cuenta' => $cuenta,':fase' => $fase,':actividad' => $actividad,':usrActual' => $usrActual, ':estatus' => $estatus));
				 
			}else
			{
				$sql="UPDATE sia_fasesactividades SET " .
				"nombre=:actividad, usrModificacion=:usrActual, fModificacion=getdate(), estatus=:estatus " .
				"WHERE idActividad=:idactividad;";
					
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':actividad' => $actividad,':usrActual' => $usrActual, ':estatus' => $estatus,':idactividad' => $idactividad));
					
			}

		$app->redirect($app->urlFor('listaFasesActividades'));
			
		}catch (PDOException $e) {
			echo  "<br>¡Error en el TRY!: " . $e->getMessage();
			die();
		}
	});






////////////////// catTipoPapeles///////////////////////////////////////////////////////////////////////////////////////////////////////////


	$app->get('/tipoPapeles', function()   use($app, $db) {

		$sql="SELECT idTipoPapel id, nombre , programada, estatus, archivoFinal FROM sia_tipospapeles;";

		$dbQuery = $db->prepare($sql);		
	 	$dbQuery->execute();
	 	$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		
	 	$app->render('catTipoPapeles.php', $result);
		
	})->name('listacatTipoPapeles');





	$app->get('/ActiviPapeles/:papel',  function($papel)  use($app, $db) {

		$sql="SELECT idTipoPapel idt, nombre, programada, estatus, archivoOriginal ,archivoFinal FROM sia_tipospapeles  WHERE idTipoPapel=:papel;";

		$dbQuery = $db->prepare($sql);		
		$dbQuery->execute(array(':papel' => $papel));
		$result= $dbQuery->fetch(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "RECURSO NO ENCONTRADA.");			
		}else{		
			echo json_encode($result);
		}		
	});

	
	$app->get('/lstpapelesbyfases/:fase', function($fase)    use($app, $db) {
		
		$sql="SELECT  fa.idFase id, tp.idTipoPapel, fa.nombre texto FROM sia_tipospapeles tp INNER JOIN sia_papelesfases pf on tp.idTipoPapel=pf.idTipoPapel INNER JOIN sia_fases fa on pf.idFase=fa.idFase WHERE tp.idTipoPapel=:fase";
		

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':fase' => $fase));
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		echo json_encode($result);
	
	});


	
	$app->get('/validpapel/:papel', function($papel)    use($app, $db) {
		
		$sql="SELECT idTipoPapel pap FROM sia_tipospapeles WHERE idTipoPapel=:papel";
		

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':papel' => $papel));
		$result= $dbQuery->fetch(PDO::FETCH_ASSOC);
		echo json_encode($result);
	
	});


	$app->post('/guardar/papeles', function()  use($app, $db) {
			$usrActual = $_SESSION["idUsuario"];
			$area = $_SESSION["idArea"];			
			$request=$app->request;
			$oper = $request->post('txtoperacion');
			$archivoOriginal = $request->post('txtArchivoOriginal');
			$archivoFinal = $request->post('txtArchivoFinal');
			$idpapel = strtoupper($request->post('txtTipoPapel'));
			$nmpapel = strtoupper($request->post('txtNombrePapel'));
			$programa = $request->post('txtProgramadaPapel');			
			$estatus = $request->post('txtEstausPapel');
			$fasdiasi = $request->post('txtFases');
			$fareas = $request->post('txtAreas');


		try{
	
			if($oper=='INS'){
				$sql="INSERT INTO sia_tipospapeles (idTipoPapel, nombre, programada, usrAlta, fAlta, estatus,archivoOriginal, archivoFinal) " .
						"VALUES (:idpapel, :nmpapel, :programa, :usrActual, getdate(), :estatus, :archivoOriginal, :archivoFinal);";
	
					$dbQuery = $db->prepare($sql);

					$dbQuery->execute(array(':idpapel' => $idpapel,':nmpapel' => $nmpapel,':programa' => $programa,':usrActual' => $usrActual, ':estatus' => $estatus, ':archivoOriginal'=>$archivoOriginal, ':archivoFinal'=>$archivoFinal));

				 
			}else{
				$sql="UPDATE sia_tipospapeles SET " .
				"nombre=:nmpapel, programada=:programa,usrModificacion=:usrActual, fModificacion=getdate(), estatus=:estatus " .
				"WHERE idTipoPapel=:idpapel;";
					
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':nmpapel' => $nmpapel,':programa' => $programa,':usrActual' => $usrActual, ':estatus' => $estatus,':idpapel' => $idpapel));
					
			}

			echo("<br>idpapel: " . $idpapel . " usrActual: " . $usrActual);

			if($fareas != ""){
				$registrosAreas = explode('*', $fareas);

				if ( count($registrosAreas) > 0 ){

					// Se inicia eliminando todos los módulos que pueda tener el rol.
					
					$sql ="DELETE FROM sia_areastipospapeles WHERE idTipoPapel= :idpapel";
					$dbQuery = $db->prepare($sql);
					$dbQuery->execute(array(':idpapel'=>$idpapel));
					
					// Ahora se agregan a la tabla sia_rolesmodulos los módulos que se hayan seleccionado para el rol.

					foreach ($registrosAreas as $registroAreas) {
					    	$camposAreas = explode("|", $registroAreas);

							$idAreas 		= $camposAreas [0];
							$nombreAreas	= $camposAreas [1];


						$sql="INSERT INTO sia_areastipospapeles (idArea,idTipoPapel,usrAlta,fAlta,estatus) " .
							"VALUES (:idAreas, :idpapel,:usrActual,getdate(),'ACTIVO');";
		
						$dbQuery = $db->prepare($sql);

						$dbQuery->execute(array(':idAreas' => $idAreas,':idpapel' => $idpapel,':usrActual' => $usrActual));
					}
				}
 			}
 			echo("<br>idpapel: " . $idpapel . " usrActual: " . $usrActual);
 			//echo("<br>idpapel: "+ $idpapel);



			if ($fasdiasi != ""){

				$registrosModulos = explode('*', $fasdiasi);

				//echo('<br>count($registrosModulos)='+ count($registrosModulos) );

				if ( count($registrosModulos) > 0 ){

					// Se inicia eliminando todos los módulos que pueda tener el rol.
					
					$sql ="DELETE FROM sia_papelesfases WHERE idTipoPapel= :idpapel";
					$dbQuery = $db->prepare($sql);
					$dbQuery->execute(array(':idpapel'=>$idpapel ));
					
					// Ahora se agregan a la tabla sia_rolesmodulos los módulos que se hayan seleccionado para el rol.
					
					foreach ($registrosModulos as $registroModulos) {
				    	$camposModulo = explode("|", $registroModulos);

						$idModulo 		= $camposModulo [0];
						$nombreModulo	= $camposModulo [1];

						//echo("<br>id: " . $id . "  idModulo: " . $idModulo . " nombreModulo: " . $nombreModulo);

						$sql = "INSERT INTO sia_papelesfases (idFase, idTipoPapel, usrAlta, fAlta, estatus) VALUES (:idModulo, :idpapel, :usrActual, getdate(), 'ACTIVO');";

						$dbQuery = $db->prepare($sql);
						$dbQuery->execute(array(':idModulo'=>$idModulo, ':idpapel'=>$idpapel, ':usrActual'=> $usrActual));
					}
				}
			}

			
		}catch (PDOException $e) {
			echo  "<br>¡Error en el TRY!: " . $e->getMessage();
			die();
		}
		$app->redirect($app->urlFor('listacatTipoPapeles'));

	});



/////////////////////FIN DE catTipoPapeles /////////////////////////////////////////////////////////////////////////////////////////////////////////////


/////////////////////INICIO catnotificaciones 21/07/2016////////////////////////////////////////////////////////////////////////////////////////////////

	$app->get('/catNotificaciones', function()   use($app, $db) {

		$sql="SELECT idNotificacion id, nombre, descripcion,tipo,consulta,estatus FROM sia_notificaciones;";

		$dbQuery = $db->prepare($sql);		
	 	$dbQuery->execute();
	 	$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		
	 	$app->render('catNotificaciones.php', $result);
		
	})->name('listaCatNotificaciones');


	$app->get('/lstNotifiByID/:id',  function($id)  use($app, $db) {

		$sql="SELECT idNotificacion idt, nombre, descripcion,tipo,consulta,estatus FROM sia_notificaciones  WHERE idNotificacion=:id;";

		$dbQuery = $db->prepare($sql);		
		$dbQuery->execute(array(':id' => $id));
		$result= $dbQuery->fetch(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "RECURSO NO ENCONTRADA.");			
		}else{		
			echo json_encode($result);
		}		
	});


	$app->get('/validnoti/:noti', function($noti)    use($app, $db) {
	
		$sql="SELECT idNotificacion noti FROM sia_notificaciones WHERE idNotificacion=:noti";
		

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':noti' => $noti));
		$result= $dbQuery->fetch(PDO::FETCH_ASSOC);
		echo json_encode($result);

	});





	$app->post('/guardar/notificacion', function()  use($app, $db) {
			$usrActual = $_SESSION["idUsuario"];
			$request=$app->request;
			$oper = $request->post('txtoperacion');

			$id = $request->post('txtIDNotificacion');
			$nombre = strtoupper($request->post('txtNombreNotifi'));
			$descrip = $request->post('txtDescripcion');			
			$tipo = $request->post('txtTipoNoti');
			$consulta = $request->post('txtConsulta');
			$esta = $request->post('txtEstatus');
			$rolnoti = $request->post('txtNotificaciones');


		try{
	
			if($oper=='INS'){
				$sql="INSERT INTO sia_notificaciones (nombre, descripcion,tipo,consulta, usrAlta, fAlta, estatus) " .
						"VALUES (:nombre, :descrip,:tipo,:consulta ,:usrActual, getdate(),:esta);";
	
					$dbQuery = $db->prepare($sql);

					$dbQuery->execute(array(':nombre' => $nombre,':descrip' => $descrip,':tipo' => $tipo,':consulta' => $consulta,':usrActual' => $usrActual, ':esta' => $esta));
				 
			}else{
				$sql="UPDATE sia_notificaciones SET " .
				"nombre=:nombre, descripcion=:descrip,tipo=:tipo,consulta=:consulta,usrModificacion=:usrActual, fModificacion=getdate(), estatus=:esta " .
				"WHERE idNotificacion=:id;";
					
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':nombre' => $nombre,':descrip' => $descrip,':tipo' => $tipo,':consulta' => $consulta,':usrActual' => $usrActual, ':esta' => $esta,':id' => $id));
					
			}


 				// Si fue insertar se debe recuperar el valor del campo idUsuario recien ingresado
			if ($oper=='INS'){
				$sql="SELECT MAX(idNotificacion) id FROM sia_notificaciones WHERE nombre=:nombre AND descripcion=:descrip AND tipo=:tipo AND consulta=:consulta AND usrAlta=:usrActual AND estatus=:estatus;";
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':nombre' => $nombre,':descrip' => $descrip,':tipo' => $tipo,':consulta' => $consulta,':usrActual' => $usrActual,':estatus' => $estatus));				
				$result = $dbQuery->fetch(PDO::FETCH_ASSOC); 
				$id = $result ['id']; 
			}

 			echo("<br>id: "+ $id);

			if ($rolnoti != ""){

				$registrosModulos = explode('*', $rolnoti);

				//echo('<br>count($registrosModulos)='+ count($registrosModulos) );

				if ( count($registrosModulos) > 0 ){

					// Se inicia eliminando todos los módulos que pueda tener el rol.
					
					$sql ="DELETE FROM sia_notificacionesroles WHERE idNotificacion= :id";
					$dbQuery = $db->prepare($sql);
					$dbQuery->execute(array(':id'=>$id));
					
					// Ahora se agregan a la tabla sia_rolesmodulos los módulos que se hayan seleccionado para el rol.
					
					foreach ($registrosModulos as $registroModulos) {
				    	$camposModulo = explode("|", $registroModulos);

						$idModulo 		= $camposModulo [0];
						$nombreModulo	= $camposModulo [1];

						//echo("<br>id: " . $id . "  idModulo: " . $idModulo . " nombreModulo: " . $nombreModulo);

						$sql = "INSERT INTO sia_notificacionesroles (idNotificacion, idRol, usrAlta, fAlta, estatus) VALUES (:id, :idModulo, :usrActual, getdate(), 'ACTIVO');";

						$dbQuery = $db->prepare($sql);
						$dbQuery->execute(array(':id'=>$id, ':idModulo'=>$idModulo, ':usrActual'=> $usrActual));
					}
				}
			}

			
		}catch (PDOException $e) {
			echo  "<br>¡Error en el TRY!: " . $e->getMessage();
			die();
		}
		$app->redirect($app->urlFor('listaCatNotificaciones'));
	});





	//Obtener los roles asignados a un usuario
	$app->get('/lstRolesBynotifica/:id',  function($id)  use($app, $db) {
		//$id = (int)$id;
		$sql= "SELECT isnull(nr.idRol,'') id, isnull(r.nombre, '') texto FROM sia_notificacionesroles nr INNER JOIN sia_roles r ON nr.idRol = r.idRol WHERE nr.idNotificacion=:id ";
		
		$dbQuery = $db->prepare($sql);		
		$dbQuery->execute(array(':id' => $id));
		$result ['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);

		if(!$result){
			$app->halt(404, "RECURSO NO ENCONTRADA.");			
		}else{		
			echo json_encode($result);
		}		
	});

/////////////////////FIN catnotificaciones 21/07/2016//////////////////////////////////////////////////////////////////////////////////////////////////
	
/////////////////////INICIO notificaciones.php 01/08/2016//////////////////////////////////////////////////////////////////////////////////////////////

	$app->get('/notificaciones', function()   use($app, $db) {
			$usrActual = $_SESSION["idUsuario"];


		$sql="SELECT Case WHEN estatus='INACTIVO' THEN 'true' ELSE 'false' END  AS asignado, idMensaje noti, idMensaje id,CONVERT(VARCHAR(10),fAlta,103) fecha, mensaje, CONVERT(VARCHAR(10),fLectura,103) lectura, idPrioridad import, idImpacto impacto, situacion FROM sia_notificacionesmensajes WHERE estatus='ACTIVO' and idUsuario=:usrActual ORDER BY situacion DESC;";


		$dbQuery = $db->prepare($sql);		
	 	$dbQuery->execute(array(':usrActual' => $usrActual));
	 	$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		
	 	$app->render('notificaciones.php', $result);
		
	})->name('listaNotificaciones');




	$app->get('/lstMensaByID/:id',  function($id)  use($app, $db) {

	$sql="SELECT idNotificacion noti, idMensaje mensa, idUsuario usua, CONVERT(VARCHAR(10),fAlta,103) fecha, mensaje, Case WHEN fLectura is null THEN '' ELSE fLectura END AS lectura, idPrioridad prioridad, idImpacto impacto,estatus  FROM sia_notificacionesmensajes WHERE idMensaje=:id";

		$dbQuery = $db->prepare($sql);		
		$dbQuery->execute(array(':id' => $id));
		$result= $dbQuery->fetch(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "RECURSO NO ENCONTRADA.");			
		}else{		
			echo json_encode($result);
		}		
	});



////guargar post de notificación

	$app->post('/guardar/notifica', function()  use($app, $db) {
		$usrActual = $_SESSION["idUsuario"];
		$request=$app->request;
		//$oper = $request->post('txtoperacion');

		$idnoti = $request->post('txtID');
		$idMensaje = $request->post('txtIdMensaje');
		$fabierto = $request->post('txtfeAbMensaje');			
		
		$esta = $request->post('txtEstatusMensaje');

		try{
	
			// if($oper=='UPD'){
				if($esta=="ACTIVO"){
				$sql="UPDATE sia_notificacionesmensajes SET fLectura =:fabierto,usrModificacion =:usrActual ,fModificacion = getdate(), situacion='LEIDO' WHERE idNotificacion =:idnoti AND idMensaje =:idMensaje;";
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':fabierto' => $fabierto,':usrActual' => $usrActual,':idnoti' => $idnoti, ':idMensaje' => $idMensaje));

				}else{

				$sql="UPDATE sia_notificacionesmensajes SET fLectura =:fabierto ,fEliminado = getdate() ,usrModificacion =:usrActual ,fModificacion = getdate() ,estatus ='INACTIVO' WHERE idNotificacion =:idnoti AND idMensaje =:idMensaje;";
	
					$dbQuery = $db->prepare($sql);

					$dbQuery->execute(array(':fabierto' => $fabierto,':usrActual' => $usrActual,':idnoti' => $idnoti, ':idMensaje' => $idMensaje));
				}
			//}

			
		}catch (PDOException $e) {
			echo  "<br>¡Error en el TRY!: " . $e->getMessage();
			die();
		}
		$app->redirect($app->urlFor('listaNotificaciones'));
	});



$app->get('/guardar/notificaciones/:datos',  function($datos)  use($app, $db) {
		$usrActual = $_SESSION["idUsuario"];
		//$cuenta = $_SESSION["idCuentaActual"];
		$programa = $_SESSION["idProgramaActual"];
		$ciclo=0;
		try{
			if($datos<>""){
			
			$registros = explode("*", $datos);
			foreach($registros as $registro) {     
		     	$campo = explode("|", $registro);
				$idMensaje=$campo[0];

				echo $datos;

				//Inserta		
				$sql="UPDATE sia_notificacionesmensajes SET fEliminado = getdate() ,usrModificacion =:usrActual ,fModificacion = getdate() ,estatus ='INACTIVO' WHERE idMensaje =:idMensaje;";
	
					$dbQuery = $db->prepare($sql);

					$dbQuery->execute(array(':usrActual' => $usrActual,':idMensaje' => $idMensaje));
    
		    }			

		    echo "OK";

		}
		else{
			echo ("NO");
		}
	}catch (PDOException $e) {
			print "<br>¡Error en el TRY!: " . $e->getMessage();
			die();
		}

	});

	$app->get('/notifica',  function()  use($app, $db) {
		$usrActual = $_SESSION["idUsuario"];
		
		$sql=" SELECT Case WHEN  count(*)=0 THEN '0' ELSE count(*)  END  AS valor  FROM sia_notificacionesmensajes WHERE  situacion='NUEVO' AND estatus='ACTIVO' AND idUsuario=:usrActual;";


		$dbQuery = $db->prepare($sql);		
		$dbQuery->execute(array(':usrActual' => $usrActual));
		$result= $dbQuery->fetch(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "RECURSO NO ENCONTRADA.");			
		}else{		
			echo json_encode($result);
		}		
	});


	$app->get('/notific',  function()  use($app, $db) {
		$usrActual = $_SESSION["idUsuario"];
		
		$sql=" SELECT Case WHEN  count(*)=0 THEN '0' ELSE count(*)  END  AS valor  FROM sia_notificacionesmensajes WHERE  estatus='ACTIVO' AND idUsuario=:usrActual;";


		$dbQuery = $db->prepare($sql);		
		$dbQuery->execute(array(':usrActual' => $usrActual));
		$result= $dbQuery->fetch(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "RECURSO NO ENCONTRADA.");			
		}else{		
			echo json_encode($result);
		}		
	});

////////////////////////////////////////cedulas 17/08/16 seguimeinto INICIO////////////////////////////////////////////////////
	//Lista de TIPO-PAPEL
	$app->get('/lstTipoPapel', function()    use($app, $db) {
		$sql="SELECT idTipoPapel id,nombre texto FROM sia_tipospapeles";
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute();
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});

	//Lista de TIPO-PAPEL
	$app->get('/lstMomentos', function()    use($app, $db) {
		$sql="SELECT idMomento id, nombre texto FROM sia_momentos;";
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute();
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});



	$app->get('/lstpapelesbymomento/:momen', function($momen)    use($app, $db) {
		
		$sql="SELECT Case when pc.idRiesgo=ri.idRiesgo Then 'SI' Else 'NO' END AS asignado,mr.idRiesgo ,ri.idRiesgo id, ri.descripcion riesgo FROM sia_riesgos ri " .
			"INNER JOIN sia_momentosriesgos mr on ri.idRiesgo=mr.idRiesgo " .
			"INNER JOIN sia_momentos mo on mr.idMomento=mo.idMomento " .
			"LEFT JOIN sia_papelescontroles pc on ri.idRiesgo=pc.idRiesgo " .
			"where mo.idMomento=:momen;";
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':momen' => $momen));
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		echo json_encode($result);
	
	});


		//Lista de TIPO-PAPEL
	$app->get('/lstProced', function()    use($app, $db) {
		$sql="SELECT idProcedimiento id, nombre texto FROM sia_procedimientos;";
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute();
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});

	//Lista de TIPO-PAPEL
	$app->get('/lstElem/:pro', function($pro)    use($app, $db) {
		$sql="SELECT pe.idProcedimiento, pe.idElemento id, pe.nombre texto FROM sia_procedimientoselementos pe " .
			"INNER JOIN sia_procedimientos pr on pe.idProcedimiento=pr.idProcedimiento  " .
			"WHERE pr.idProcedimiento=:pro;";
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':pro' => $pro));
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});


//Guarda un papel
	$app->post('/guardar/pa', function()  use($app, $db) {
		try{
			$usrActual = $_SESSION["idUsuario"];
			$request=$app->request;
			$id = $request->post('txtID');
			$oper = $request->post('txtOperacion');
			$cuenta = $request->post('txtCuenta');
			$programa = $request->post('txtPrograma');
			$auditoria = $request->post('txtAuditoria');
			$fase = $request->post('txtFase');
			$tipoPapel = $request->post('txtTipPoapel');
			
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
				if($tipoResultado=='NINGUNA'){
				$sql="UPDATE sia_papeles SET " .
				"idFase=:fase, tipoPapel=:tipoPapel, fPapel=:fPapel, tipoResultado=:tipoResultado, resultado='', usrModificacion=:usrActual, fModificacion=getdate(), archivoOriginal=:original, archivoFinal=:final " .
				"WHERE idPapel=:id";

				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':fase' => $fase, ':tipoPapel' => $tipoPapel, ':fPapel' => $fPapel,':tipoResultado' => $tipoResultado,':usrActual' => $usrActual,  ':original' => $original, ':final' => $final,':id' => $id  ));
				
				echo "GLOBAL-AREA <hr>  <br>fase: $fase  tipoPapel= $tipoPapel   fPapel= $fPapel tipoResultado: $tipoResultado  resultado= $resultado   usrActual= $usrActual final= $final Original= $original id= $id";					
				}else{
				$sql="UPDATE sia_papeles SET " .
				"idFase=:fase, tipoPapel=:tipoPapel, fPapel=:fPapel, tipoResultado=:tipoResultado, resultado=:resultado, usrModificacion=:usrActual, fModificacion=getdate(), archivoOriginal=:original, archivoFinal=:final " .
				"WHERE idPapel=:id";

				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':fase' => $fase, ':tipoPapel' => $tipoPapel, ':fPapel' => $fPapel,':tipoResultado' => $tipoResultado,':resultado' => $resultado, ':usrActual' => $usrActual, ':original' => $original, ':final' => $final ,':id' => $id  ));
				
				echo "GLOBAL-AREA <hr>  <br>fase: $fase  tipoPapel= $tipoPapel   fPapel= $fPapel tipoResultado: $tipoResultado  resultado= $resultado   usrActual= $usrActual final= $final Original= $original id= $id";
				}
			}
			echo "SQL=<br>$sql<br><hr> id: " . $id . " fase: " . $fase . " tipoPapel: " . $tipoPapel . " fPapel: " . $fPapel . " tipoResultado: " . $tipoResultado . " esultado: " . $resultado . " usrActual: " . $usrActual . "final" . $final ."Original" . $original;
			$app->redirect($app->urlFor('listaPapeles'));
		}catch (PDOException $e) {
			echo  "<br>Error de BD: " . $e->getMessage();
			die();
		}		
	});

		//Obtener un avance
	$app->get('/DescarTipoPapel/:id', function($id)    use($app, $db) {
		$sql="SELECT idTipoPapel id,archivoFinal final FROM sia_tipospapeles WHERE idTipoPapel=:id;";
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':id' => $id));
		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);
		echo json_encode($result);		
	});


	$app->get('/btndescar/:id', function($id)    use($app, $db) {
		$sql="SELECT idTipoPapel, programada pro FROM sia_tipospapeles WHERE idTipoPapel=:id;";
		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':id' => $id));
		$result = $dbQuery->fetch(PDO::FETCH_ASSOC);
		echo json_encode($result);		
	});

	//lista de PAPELES-FASE
	$app->get('/lstPapelesFases', function()    use($app, $db) {
			$cuenta = $_SESSION["idCuentaActual"];
		$sql="SELECT idArea id, nombre texto FROM sia_areas ORDER BY id";

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute();
		$result ['datos']= $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		if(!$result){
			$app->halt(404, "NO SE ENCONTRARON DATOS ");
		}else{
			echo json_encode($result);
		}
	});	

		$app->get('/lstAreasbyPapeles/:papel', function($papel)    use($app, $db) {
		
		$sql="SELECT atp.idArea id, ar.nombre texto FROM sia_areastipospapeles atp
  			INNER JOIN sia_areas ar on atp.idArea = ar.idArea
  			INNER JOIN sia_tipospapeles tp on atp.idTipoPapel = tp.idTipoPapel
  			WHERE tp.idTipoPapel=:papel;";
		

		$dbQuery = $db->prepare($sql);
		$dbQuery->execute(array(':papel' => $papel));
		$result['datos'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
		echo json_encode($result);
	
	});



?>