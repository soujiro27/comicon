<?php

	//$usrActual = $_SESSION["idUsuario"];

	//Primero, arranca el bloque PHP y checkea si el archivo tiene nombre.  Si no fue asi, te remite de nuevo al formulario de inserción:
	// No se comprueba aqui si se ha subido correctamente.
	// archivo temporal (ruta y nombre).

	// Obtener del array FILES (superglobal) los datos del binario .. nombre, tabamo y tipo.
	$binario_nombre_temporal = $_FILES['btnUpload']['tmp_name'] ; // File in the PHP tmp folder
	$binario_nombre          = $_FILES["btnUpload"]["name"];     // The file name
	$binario_tipo            = $_FILES["btnUpload"]["type"];     // The type of file it is
	$binario_peso            = $_FILES["btnUpload"]["size"];     // File size in bytes
	$binario_ruta            = $_FILES["btnUpload"]["path"];     // File size in bytes
	$binario_error_Msg       = $_FILES["btnUpload"]["error"];    // 0 for false... and 1 for true

	/*
	if (empty($_FILES['btnUpload']['name'])){
		//header("location: binarios.php?proceso=upload_form"); //o como se llame el formulario ..
		header("location: binarios.php"); //o como se llame el formulario ..
		exit;
	}
	*/
	if (!$binario_nombre_temporal) { // Si no se selecciono archivo
		echo "ERROR: Debes seleccionar un archivo..";
		exit();
	}else{	

		// leer del archivo temporal .. el binario subido, "rb" para Windows .. Linux parece q con "r" sobra ...
		//$binario_contenido = addslashes(fread(fopen($binario_nombre_temporal, "rb"), filesize($binario_nombre_temporal)));
		$binario_contenido = base64_encode(file_get_contents($binario_nombre_temporal));

		//Obtener la extension del archivo
		$datos = explode(".", $binario_nombre);
		$extension="";
		foreach($datos as $parte) {
			$extension = "." . trim($parte);
		}	

		//Nuevo nombre
		$nuevoNombre = "d" . date("YmdHis") . $extension;
		$moduloOrigen = 'BINARIOS';
		$idOrigen = 1;
		$usrActual = 9;

		//echo $binario_contenido;
		echo $binario_ruta . '/' . $binario_nombre;

		/* ********************* SI SE MUEVE A UNA CARPETA ESPECIFICA CON EL NUEVO NOMBRE
		//Mueve el tmp al nuevo nombre
		if(move_uploaded_file($binario_nombre_temporal, "documentos/$nuevoNombre")){
			echo $nuevoNombre;
		} 
		else {
			echo "ERROR";
		}
		*/

		/* ********************* SI SE GUARDA A LA BD EJEMPLO MYSQL

		//insertamos los datos en la BD.
		$consulta_insertar = "INSERT INTO archivos (id, archivo_binario, archivo_nombre, archivo_peso, archivo_tipo) VALUES ('', '$binario_contenido', '$binario_nombre', '$binario_peso', '$binario_tipo')";
		mysql_query($consulta_insertar, $conexion) or die("No se pudo insertar los datos en la base de datos.");
		// Se mueve a un archivo especifico
		header("location: listar_imagenes.php");  // si ha ido todo bien
		exit;
		*/

	}

?>