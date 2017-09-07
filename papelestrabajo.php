<?php
	include("src/conexion.php");
	$usrActual = $_POST["txtUsuario"];
	$cuenta = $_POST["txtCuenta"];
	$programa = $_POST["txtPrograma"];
	$id = $_POST["txtID"];
	$oper = $_POST["txtOperacion"];
	$auditoria = $_POST["txtAuditoria"]; // The file name
	$fase = $_POST["txtFase"];
	$tipoPapel = $_POST["txtTipoPapel"];
	$fPapel = $_POST["txtFechaPapel"];
	$tipoResultado = $_POST["txtTipoRes"];
	$resultado = $_POST["txtResultado"];
	$original  = $_POST["txtArchivoOriginal"];
	$final = $_POST["txtArchivoFinal"];

	$est = $_POST['txtEstatusRegistro'];

	try{
		$db = new PDO("sqlsrv:Server={$hostname}; Database={$database}", $username, $password );
	}catch (PDOException $e) {
		print "ERROR: " . $e->getMessage() . "<br><br>HOSTNAME: " . $hostname . " BD:" . $database . " USR: " . $username . " PASS: " . $password . "<br><br>";
		die();
	}

	try{
		if($oper=='INS'){
				$sql="INSERT INTO sia_papeles (idCuenta, idPrograma, idAuditoria, idFase, tipoPapel, fPapel, tipoResultado, resultado, archivoOriginal, archivoFinal, usrAlta, fAlta, estatus) " .
				"VALUES(:cuenta, :programa, :auditoria, :fase, :tipoPapel, :fPapel, :tipoResultado, :resultado, :original, :final, :usrActual, getdate(), 'ACTIVO');";

				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':cuenta' => $cuenta, ':programa' => $programa, ':auditoria' => $auditoria, ':fase' => $fase, 
				':tipoPapel' => $tipoPapel, ':fPapel' => $fPapel, ':tipoResultado' => $tipoResultado,':resultado' => $resultado, ':original' => $original, ':final' => $final, ':usrActual' => $usrActual ));				
				echo "INS <hr>  <br>fase: $fase  tipoPapel= $tipoPapel   fPapel= $fPapel tipoResultado: $tipoResultado  resultado= $resultado   usrActual= $usrActual final= $final Original= $original";
			}else{
				if($tipoResultado=='NINGUNA' && $est=='ACTIVO'){				
				$sql="UPDATE sia_papeles SET " .
				"idFase=:fase, tipoPapel=:tipoPapel, fPapel=:fPapel, tipoResultado=:tipoResultado, resultado='', usrModificacion=:usrActual, fModificacion=getdate(), archivoOriginal=:original, archivoFinal=:final " .
				"WHERE idPapel=:id";

				$dbQuery = $db->prepare($sql);
				$dbQuery->execute(array(':fase' => $fase, ':tipoPapel' => $tipoPapel, ':fPapel' => $fPapel,':tipoResultado' => $tipoResultado,':usrActual' => $usrActual,  ':original' => $original, ':final' => $final,':id' => $id  ));
				
				//echo "GLOBAL-AREA <hr>  <br>fase: $fase  tipoPapel= $tipoPapel   fPapel= $fPapel tipoResultado: $tipoResultado  resultado= $resultado   usrActual= $usrActual final= $final Original= $original id= $id";					
				}else{
					if($est=='INACTIVO'){
						$sql="UPDATE sia_papeles SET " .
						"usrModificacion=:usrActual, fModificacion=getdate(), estatus=:est " .
						"WHERE idPapel=:id";

						$dbQuery = $db->prepare($sql);
						$dbQuery->execute(array(':usrActual' => $usrActual,':est' => $est ,':id' => $id  ));
						
						echo "INACTIVO <hr>  <br>usrActual= $usrActual est= $est id= $id";

					}else{
						$sql="UPDATE sia_papeles SET " .
						"idFase=:fase, tipoPapel=:tipoPapel, fPapel=:fPapel, tipoResultado=:tipoResultado, resultado=:resultado, usrModificacion=:usrActual, fModificacion=getdate(), archivoOriginal=:original, archivoFinal=:final " .
						"WHERE idPapel=:id";

						$dbQuery = $db->prepare($sql);
						$dbQuery->execute(array(':fase' => $fase, ':tipoPapel' => $tipoPapel, ':fPapel' => $fPapel,':tipoResultado' => $tipoResultado,':resultado' => $resultado, ':usrActual' => $usrActual, ':original' => $original, ':final' => $final ,':id' => $id  ));
						
						echo "GLOBAL-AREA <hr>  <br>fase: $fase  tipoPapel= $tipoPapel   fPapel= $fPapel tipoResultado: $tipoResultado  resultado= $resultado   usrActual= $usrActual final= $final Original= $original id= $id";
					}
				}
			}
			//echo "SQL=<br>$sql<br><hr> id: " . $id . " fase: " . $fase . " tipoPapel: " . $tipoPapel . " fPapel: " . $fPapel . " tipoResultado: " . $tipoResultado . " esultado: " . $resultado . " usrActual: " . $usrActual . "  final" . $final ."   Original" . $original;
			//$app->redirect($app->urlFor('listaPapeles'));
		}catch (PDOException $e) {
			echo  "<br>Error de BD: " . $e->getMessage();
			die();
		}		
	




?>