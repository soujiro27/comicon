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
			$sql="SELECT CASE WHEN idActividad IS null THEN 'INS' ELSE 'UPD' END oper FROM sia_AuditoriasReprogramaciones where idActividad=:idActividad and idAuditoria=:idAuditoria";
			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':idActividad' => $idActividad,':idAuditoria' => $idAuditoria));				
			$result = $dbQuery->fetch(PDO::FETCH_ASSOC); 
			$oper = $result ['oper'];
			echo $oper;
					
			if($oper=='UPD'){
				$sql="UPDATE sia_AuditoriasReprogramaciones set fIniReprogramacion=:fIniReprogramacion , fFinReprogramacion=:fFinReprogramacion, diasReprogramados=:diasReprogramados, fModificacion= getdate(), usrModificacion=:usrActual WHERE idAuditoria = :idAuditoria AND idActividad = :idActividad AND idCuenta = :cuenta AND idPrograma = :programa";
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':fIniReprogramacion' => $fIniReprogramacion,':fFinReprogramacion' => $fFinReprogramacion,':diasReprogramados' => $diasReprogramados,':usrActual' => $usrActual,':idAuditoria' => $idAuditoria,':idActividad' => $idActividad, ':cuenta'=>$cuenta, ':programa'=> $programa));


				if($idFase == 'PLANEACION' || $idFase == 'INFORMES'){
						
						if($idFase == 'PLANEACION'){
							$sql = "UPDATE sia_auditorias SET fInicio=:fIniReprogramacion,usrModificacion=:usrActual, fModificacion=getdate() WHERE idAuditoria=:idAuditoria;";
							$dbQuery = $db->prepare($sql);	
							$dbQuery-> execute(array(':fIniReprogramacion'=> $fIniReprogramacion,':usrActual'=> $usrActual,':idAuditoria'=> $idAuditoria));
							echo "OK";

						}else{
							$sql = "UPDATE sia_auditorias SET fFin=:fFinReprogramacion,usrModificacion=:usrActual, fModificacion=getdate() WHERE idAuditoria=:idAuditoria;";
							$dbQuery = $db->prepare($sql);	
							$dbQuery-> execute(array(':fFinReprogramacion'=> $fFinReprogramacion,':usrActual'=> $usrActual,':idAuditoria'=> $idAuditoria));
							echo "OK";
						}
					}
															
				$dbQuery = $db->prepare("SELECT case when idFase='EJECUCION' then 'SI' else 'NO' end validacion FROM sia_AuditoriasReprogramaciones where idFase=:idFase AND idAuditoria=:idAuditoria AND idActividad=:idActividad;");
				$dbQuery->execute(array(':idFase' => $idFase,':idAuditoria' => $idAuditoria,':idActividad' => $idActividad));				
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

			}else{
				//se inserta el registro dentro de la tabla  sia_AuditoriasReprogramaciones
				$sql="INSERT INTO sia_AuditoriasReprogramaciones " .
				"(idCuenta,idPrograma,idAuditoria,idActividad,idFase,fIniReprogramacion,fFinReprogramacion,diasReprogramados,fAlta,usrAlta) " .
				"VALUES( " .
				":cuenta,:programa,:idAuditoria,:idActividad,:idFase,:fIniReprogramacion,:fFinReprogramacion,:diasReprogramados,getdate(),:usrActual);";
				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':cuenta' => $cuenta,':programa' => $programa,':idAuditoria' => $idAuditoria,':idActividad'=>$idActividad,':idFase'=>$idFase,':fIniReprogramacion'=>$fIniReprogramacion,':fFinReprogramacion'=>$fFinReprogramacion,':diasReprogramados'=>$diasReprogramados,':usrActual'=>$usrActual));

				// verificar fase ejecucion se encuentgra el registro en fase de ejecucion en el a
				$dbQuery = $db->prepare("SELECT case when idFase='EJECUCION' then 'SI' else 'NO' end validacion FROM sia_AuditoriasReprogramaciones where idFase=:idFase AND idAuditoria=:idAuditoria AND idActividad=:idActividad");
				$dbQuery->execute(array(':idFase' => $idFase,':idAuditoria' => $idAuditoria,':idActividad' => $idActividad));				
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
			}
		}

	}catch (Exception $e) {
			print "<br>¡Error en el TRY!: " . $e->getMessage();
			die();
		}                                     
?>