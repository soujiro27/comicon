<?php
	include("src/conexion.php");
	
	try{
		$db = new PDO("sqlsrv:Server={$hostname}; Database={$database}", $username, $password );
	}catch (PDOException $e) {
		print "ERROR: " . $e->getMessage() . "<br><br>HOSTNAME: " . $hostname . " BD:" . $database . " USR: " . $username . " PASS: " . $password . "<br><br>";
		die();
	}

	$usrActual = $_POST["txtUsuario"];
	$idriesgo = strtoupper($_POST["txtIdriesgo"]);
	$oper = $_POST["txtOperariesgo"];
	$momentoriesgo = $_POST["txtmomenriesgo"];
	$Riesgo = $_POST["txtagreriesgo"];
	$estatus = $_POST["txtEstatusRiesgo"];


try{
		if($oper=='INS'){
			$sql="INSERT INTO sia_riesgos (idRiesgo,descripcion,usrAlta,fAlta,estatus) VALUES (:idriesgo,:Riesgo,:usrActual,getdate(),:estatus)";

			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':idriesgo' => $idriesgo,':Riesgo' => $Riesgo,':usrActual' => $usrActual,':estatus' => $estatus));

					
				// Ahora inserta el el momento-riesgo
		
				// Confirma si ya existe el empleado en la tabla sia_empleados.
				$dbQuery = $db->prepare("SELECT COUNT(*) total from sia_momentosriesgos WHERE idMomento=:momentoriesgo AND idRiesgo=:idriesgo; ");
				$dbQuery->execute(array(':momentoriesgo' => $momentoriesgo,':idriesgo' => $idriesgo));				
				$result = $dbQuery->fetch(PDO::FETCH_ASSOC); 
				$total = $result ['total']; 

				if($total > 0){
					$dbQuery = $db->prepare("UPDATE sia_momentosriesgos SET estatus =:estatus ,usrModificacion=:usrActual ,fModificacion=getdate() WHERE idMomento =:momentoriesgo AND idRiesgo =:idriesgo");	
					$dbQuery->execute(array(':estatus'=> $estatus, ':usrActual'=> $usrActual,':momentoriesgo'=> $momentoriesgo,':idriesgo'=> $idriesgo));

				}else{		
					$sql = "INSERT INTO sia_momentosriesgos (idMomento,idRiesgo,usrAlta,fAlta,estatus) " .
					"VALUES (:momentoriesgo,:idriesgo,:usrActual,getdate(),:estatus); ";		
					$dbQuery = $db->prepare($sql);	
					$dbQuery->execute(array(':momentoriesgo'=> $momentoriesgo, ':idriesgo'=> $idriesgo, ':usrActual'=> $usrActual, ':estatus'=> $estatus));	
				}

			echo "OK";		
		}else{
			$sql="UPDATE sia_riesgos SET descripcion = :Riesgo,usrModificacion = :usrActual,fModificacion = getdate(),estatus = :estatus " .
  				"WHERE idRiesgo =:idriesgo;";
			$dbQuery = $db->prepare($sql);
			$dbQuery->execute(array(':Riesgo' => $Riesgo,':usrActual' => $usrActual,':estatus' => $estatus,':idriesgo' => $idriesgo));

					
			echo "ACTUALIZAR <hr> $sql <br> <br> Riesgo=$Riesgo usrAlta=$usrActual  estatus=$estatus";
				//echo "OK";

					}
	}catch (PDOException $e) {
			echo  "<br>Error de BD: " . $e->getMessage();
			die();
		}   
                                     
?>