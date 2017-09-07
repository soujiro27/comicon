<?php
$fileName = $_FILES["btnUpload"]["name"]; // The file name
$fileTmpLoc = $_FILES["btnUpload"]["tmp_name"]; // File in the PHP tmp folder
$fileType = $_FILES["btnUpload"]["type"]; // The type of file it is
$fileSize = $_FILES["btnUpload"]["size"]; // File size in bytes
$fileErrorMsg = $_FILES["btnUpload"]["error"]; // 0 for false... and 1 for true
if (!$fileTmpLoc) { // if file not chosen
    echo "ERROR: Por favor selecciona un archivo antes de dar clic al botón de carga."; 
    exit();
}
/*
//Obtener la extension del archivo
$datos = explode(".", $fileName);
$extension="";
foreach($datos as $parte) {
	$extension = "." . trim($parte);
}	
//Nuevo nombre
$nuevoNombre = "Cta" . date("YmdHis") . $extension;

//Mueve el tmp al nuevo nombre
if(move_uploaded_file($fileTmpLoc, "uploads/$nuevoNombre")){
	echo $nuevoNombre;
} 
else {
	echo "ERROR";
}
*/
if(move_uploaded_file($fileTmpLoc, "uploads/$fileName")){
    echo "$fileName carga completa";
} else {
    echo "move_uploaded_file Fallo la carga";
}

?>
