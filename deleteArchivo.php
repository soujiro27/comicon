<?php
	$fileName = $_POST['archivoAborrar']; // The file name
	$confirmarBorrado = $_POST['confirmarBorrado'];
	if (unlink($fileName)){
		if ($confirmarBorrado){ echo "BORRADO"; }else{ echo ""; }
	}else{
		echo "ERROR";
	}
?>