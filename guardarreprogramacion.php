<?php
	include("../src/conexion.php");
	
	try{
		$db = new PDO("sqlsrv:Server={$hostname}; Database={$database}", $username, $password );
	}catch (PDOException $e) {
		print "ERROR: " . $e->getMessage() . "<br><br>HOSTNAME: " . $hostname . " BD:" . $database . " USR: " . $username . " PASS: " . $password . "<br><br>";
		die();
	}

			
	$usrActual = $_POST["idUsuario"];
	$cuenta    = $_POST["idCuentaActual"];
	$programa  = $_POST["idProgramaActual"];
	
	//$request=$app->request;
	
	$idAuditoria =$_POST["txtAuditorias"];
	$idFase = $_POST["txtFaActividad"];
	$idActividad = $_POST["txtIDActividad"];
	$fIniReprogramacion = $_POST["txtIncioActividad"];
	$fFinReprogramacion = $_POST["txtFiActividad"];
	$diasReprogramados = $_POST["txtDiasActividad"];
	$fIFA = $_POST["txtFReIFA"];
	$fIRA = $_POST["txtFReIRAC"];
	$unidades = $_POST["txtUniReprogramadas"];

	
	try{
		
		$reoper = explode('*', $unidades);
		foreach ($reoper as $reoper) {
			$reprounidades = explode('|',$reoper);

			$idSector = $reprounidades[0];
			$idSubsector = $reprounidades[1];
			$idUnidad = $reprounidades[2];

			// verificar si hay datos en la tabla de reprogramación.
			$sql="SELECT CASE WHEN idActividad IS null THEN 'INS' ELSE 'UPD' END oper FROM sia_AuditoriasReprogramaciones where idActividad=:idActividad and idSector = :idSector AND idSubsector = :idSubsector AND idUnidad = :idUnidad;";
			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':idActividad' => $idActividad,':idSector' => $idSector,':idSubsector' => $idSubsector,':idUnidad' => $idUnidad));				
			$result = $dbQuery->fetch(PDO::FETCH_ASSOC); 
			$oper = $result ['oper'];
					
			if($oper=='UPD'){
				$sql="UPDATE sia_AuditoriasReprogramaciones set fIniReprogramacion=:fIniReprogramacion , fFinReprogramacion=:fFinReprogramacion, diasReprogramados=:diasReprogramados, fModificacion= getdate(), usrModificacion=:usrActual WHERE idAuditoria = :idAuditoria AND idActividad = :idActividad AND idCuenta = :cuenta AND idPrograma = :programa";
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':fIniReprogramacion' => $fIniReprogramacion,':fFinReprogramacion' => $fFinReprogramacion,':diasReprogramados' => $diasReprogramados,':usrActual' => $usrActual,':idAuditoria' => $idAuditoria,':idActividad' => $idActividad, ':cuenta'=>$cuenta, ':programa'=> $programa));

																	
				$dbQuery = $db->prepare("SELECT case when idFase='EJECUCION' then 'SI' else 'NO' end validacion FROM sia_AuditoriasReprogramaciones where idFase=:idFase AND idAuditoria=:idAuditoria AND idActividad=:idActividad AND idSector = :idSector AND idSubsector = :idSubsector AND idUnidad = :idUnidad;");
				$dbQuery->execute(array(':idFase' => $idFase,':idAuditoria' => $idAuditoria,':idActividad' => $idActividad,':idSector' => $idSector,':idSubsector' => $idSubsector,':idUnidad' => $idUnidad));				
				$result = $dbQuery->fetch(PDO::FETCH_ASSOC); 
				$validacion = $result ['validacion'];
				
				if($validacion == 'SI'){
					$sql="UPDATE sia_auditoriasunidades SET fConfronta=:fFinReprogramacion, usrModificacion=:usrActual, fModificacion=getdate() WHERE idAuditoria=:idAuditoria;";
					$dbQuery = $db->prepare($sql);	
					$dbQuery->execute(array(':fFinReprogramacion'=> $fFinReprogramacion,':usrActual'=> $usrActual,':idAuditoria'=> $idAuditoria));	
					
					$sql="UPDATE sia_auditorias SET fModificacion=getdate(),usrModificacion=:usrActual,fIRA=:fIRA,fIFA=:fIFA WHERE idAuditoria=:idAuditoria;";
					$dbQuery = $db->prepare($sql);	
					$dbQuery->execute(array(':usrActual'=> $usrActual,':fIRA'=> $fIRA,':fIFA'=> $fIFA,':idAuditoria'=> $idAuditoria));	
					echo "OK";
				}else{
					echo "OK";
				}


				$sql="SELECT aa.idAuditoria auditoria, aa.idFase fase, aa.fFin fechaoriginal,ar.fFinReprogramacion fechareprogra FROM sia_auditoriasactividades aa 
					INNER JOIN sia_AuditoriasReprogramaciones ar on aa.idAuditoria = ar.idAuditoria AND aa.idFase = ar.idFase AND aa.idPrograma = ar.idPrograma AND aa.idActividad=ar.idActividad
					WHERE aa.idAuditoria=:idAuditoria AND aa.idFase=:idFase";
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':idAuditoria' => $idAuditoria,':idFase' => $idFase));
				$result ['datos']= $dbQuery->fetchAll(PDO::FETCH_ASSOC);
				if(!$result){
					$app->halt(404, "NO SE ENCONTRARON DATOS ");
				}else{
					echo json_encode($result);
				}



			}else{
				//se inserta el registro dentro de la tabla  sia_AuditoriasReprogramaciones
				$sql="INSERT INTO sia_AuditoriasReprogramaciones " .
				"(idCuenta,idPrograma,idAuditoria,idSector,idSubsector,idUnidad,idActividad,idFase,fIniReprogramacion,fFinReprogramacion,diasReprogramados,fAlta,usrAlta) " .
				"VALUES( " .
				":cuenta,:programa,:idAuditoria,:idSector,:idSubsector,:idUnidad,:idActividad,:idFase,:fIniReprogramacion,:fFinReprogramacion,:diasReprogramados,getdate(),:usrActual);";
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':cuenta' => $cuenta,':programa' => $programa,':idAuditoria' => $idAuditoria,':idSector' => $idSector, ':idSubsector' => $idSubsector, ':idUnidad'=>$idUnidad, ':idActividad'=>$idActividad,':idFase'=>$idFase,':fIniReprogramacion'=>$fIniReprogramacion,':fFinReprogramacion'=>$fFinReprogramacion,':diasReprogramados'=>$diasReprogramados,':usrActual'=>$usrActual));

				// verificar fase ejecucion se encuentgra el registro en fase de ejecucion en el a
				$dbQuery = $db->prepare("SELECT case when idFase='EJECUCION' then 'SI' else 'NO' end validacion FROM sia_AuditoriasReprogramaciones where idFase=:idFase AND idAuditoria=:idAuditoria AND idActividad=:idActividad AND idSector = :idSector AND idSubsector = :idSubsector AND idUnidad = :idUnidad;");
				$dbQuery->execute(array(':idFase' => $idFase,':idAuditoria' => $idAuditoria,':idActividad' => $idActividad,':idSector' => $idSector,':idSubsector' => $idSubsector,':idUnidad' => $idUnidad));				
				$result = $dbQuery->fetch(PDO::FETCH_ASSOC); 
				$validacion = $result ['validacion'];
				
				if($validacion=='SI'){
					$sql="UPDATE sia_auditoriasunidades SET fConfronta=:fFinReprogramacion, usrModificacion=:usrActual, fModificacion=getdate() WHERE idAuditoria=:idAuditoria;";
					$dbQuery = $db->prepare($sql);	
					$dbQuery->execute(array(':fFinReprogramacion'=> $fFinReprogramacion,':usrActual'=> $usrActual,':idAuditoria'=> $idAuditoria));	
				
					$sql="UPDATE sia_auditorias SET fModificacion=getdate(),usrModificacion=:usrActual,fIRA=:fIRA,fIFA=:fIFA WHERE idAuditoria=:idAuditoria;";
					$dbQuery = $db->prepare($sql);	
					$dbQuery->execute(array(':usrActual'=> $usrActual,':fIRA'=> $fIRA,':fIFA'=> $fIFA,':idAuditoria'=> $idAuditoria));	
					echo "OK";
				}else{
					echo "OK";
				}

				$sql="SELECT aa.idAuditoria auditoria, aa.idFase fase, aa.fFin fechaoriginal,ar.fFinReprogramacion fechareprogra FROM sia_auditoriasactividades aa 
					INNER JOIN sia_AuditoriasReprogramaciones ar on aa.idAuditoria = ar.idAuditoria AND aa.idFase = ar.idFase AND aa.idPrograma = ar.idPrograma AND aa.idActividad=ar.idActividad
					WHERE aa.idAuditoria=:idAuditoria AND aa.idFase=:idFase";
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':idAuditoria' => $idAuditoria,':idFase' => $idFase));
				$result ['datos']= $dbQuery->fetchAll(PDO::FETCH_ASSOC);
				if(!$result){
					$app->halt(404, "NO SE ENCONTRARON DATOS ");
				}else{
					echo json_encode($result);
				}
				
			}
		}

	}catch (Exception $e) {
			print "<br>¡Error en el TRY!: " . $e->getMessage();
			die();
		}                                     
?>