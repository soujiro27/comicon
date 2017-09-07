<?php
	include("src/conexion.php");
	
	try{
		$db = new PDO("sqlsrv:Server={$hostname}; Database={$database}", $username, $password );
	}catch (PDOException $e) {
		print "ERROR: " . $e->getMessage() . "<br><br>HOSTNAME: " . $hostname . " BD:" . $database . " USR: " . $username . " PASS: " . $password . "<br><br>";
		die();
	}

	$usrActual = $_POST["txtUsuario"];
	$cuenta = $_POST["txtCuenta"];
	$programa = $_POST["txtPrograma"];
	$oper = $_POST["txtOperacion"];
	$auditoria = $_POST["txtaudi"]; // The file name
	$FaseActividad = $_POST["txtFaseActividadApartado"];
	$Apartado = $_POST["txtApartado"];
	$DescripcionActividad = $_POST["txtDescripcionActividad"];
	$proceso = $_POST["txtProceso"];
	$etapa = $_POST["txtetapa"];
		//$justificacion 	  = preg_replace('/\&(.)[^;]*;/', '\\1', $justificacion);

	

try{
		if($oper=='INS'){
			$sql="INSERT into sia_AuditoriasApartados (idCuenta,idPrograma,idAuditoria,idFase,idApartado,orden,actividad,fAlta,usrAlta,estatus) 
					VALUES (:cuenta,:programa,:auditoria,:FaseActividad,:Apartado,1,:DescripcionActividad,getdate(),:usrActual,'ACTIVO');";

					$dbQuery = $db->prepare($sql);

					$dbQuery->execute(array(':cuenta' => $cuenta,':programa' => $programa,':auditoria' => $auditoria,':FaseActividad' => $FaseActividad,':Apartado' => $Apartado,':DescripcionActividad' => $DescripcionActividad,':usrActual' => $usrActual));

					//echo "INSERTAR <hr> $sql <br> <br>:cuenta=$cuenta  programa=$programa auditoria=$auditoria FaseActividad=$FaseActividad inicioactividad=$inicioactividad Finactividad=$Finactividad Porcentaje=$Porcentaje  Prioridad=$Prioridad Impacto=$Impacto NotasActividad=$NotasActividad ResponsableActividad =$ResponsableActividad usrAlta=$usrActual estatus=$EstatusActividad diasactividad=$diasactividad DescripcionActividad=$DescripcionActividad Apartado=$Apartado DescripcionActividadCom=$DescripcionActividadCom";
					
					// Contar fases
					$dbQuery = $db->prepare("SELECT CASE WHEN COUNT(idFase)=(SELECT COUNT(orden) coun FROM sia_apartados) THEN COUNT(idFase) ELSE '0' END conteo FROM sia_AuditoriasApartados where idAuditoria=:auditoria;");
					$dbQuery->execute(array(':auditoria' => $auditoria));
					$result = $dbQuery->fetch(PDO::FETCH_ASSOC);
					$conteo = $result ['conteo'];
					//echo $conteo . " auditoria: " . $auditoria;

					// verificar que la fase se encuentre en informes 
					$dbQuery = $db->prepare("SELECT COUNT(orden) total FROM sia_apartados;");
					$dbQuery->execute();				
					$result = $dbQuery->fetch(PDO::FETCH_ASSOC); 
					$total = $result ['total'];


					// verificar que la fase se encuentre en informes 
					$dbQuery = $db->prepare("SELECT case when idApartado=(SELECT TOP 1 idApartado  FROM sia_apartados ORDER BY orden DESC) then 'SI' else 'NO' end apar FROM sia_AuditoriasApartados where idAuditoria=:auditoria and idApartado=:apartado;");
					$dbQuery->execute(array(':apartado' => $Apartado,':auditoria' => $auditoria));				
					$result = $dbQuery->fetch(PDO::FETCH_ASSOC); 
					$apar = $result ['apar'];

					//echo "conteo: " . $conteo . " apar: " . $apar . " Total: " . $total;

					if($apar == 'SI' && $conteo == $total){
						$sql="INSERT INTO sia_AuditoriaAutorizacion (idCuenta,idPrograma,idAuditoria,idProceso,idEtapa,fAlta,usrAlta) 
						VALUES (:idCuenta,:idPrograma,:idAuditoria,:idProceso,:idEtapa,getdate(),:usrAlta);";
						$dbQuery = $db->prepare($sql);
						$dbQuery->execute(array(':idCuenta' => $cuenta,':idPrograma' => $programa,':idAuditoria' => $auditoria,':idProceso' => $proceso,':idEtapa' => $etapa,':usrAlta' => $usrActual));

						echo "OK";
						//echo "   ------ ".$sql ."<hr>idCuenta: " . $cuenta ."<hr>idPrograma: " . $programa ."<hr>idAuditoria: " . $auditoria ."<hr>idProceso: " . $proceso ."<hr>idEtapa: " . $etapa  ."<hr>usrAlta: " . $usrActual ." --- ";

					}
						


					echo "OK";

					}else{
						$sql="UPDATE sia_AuditoriasApartados SET actividad = :DescripcionActividad,fModificacion = getdate(),usrModificacion = :usrActual 
							WHERE idAuditoria = :auditoria  AND idApartado = :Apartado;";


						$dbQuery = $db->prepare($sql);

						$dbQuery->execute(array(':DescripcionActividad' => $DescripcionActividad,':usrActual' => $usrActual,':auditoria' => $auditoria,':Apartado' => $Apartado));

					
						echo "ACTUALIZAR <hr> $sql <br> <br> DescripcionActividad=$DescripcionActividad usrAlta=$usrActual  auditoria=$auditoria  FaseActividad=$FaseActividad Apartado=$Apartado";
						//echo "OK";

					}
	}catch (PDOException $e) {
			echo  "<br>Error de BD: " . $e->getMessage();
			die();
		}   

                                    
	




?>