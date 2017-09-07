<!DOCTYPE html>
<html lang="en"><head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=Edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<script type='text/javascript' src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js?ver=3.1.2"></script>
	<script type="text/javascript" src="js/genericas.js"></script>

  	<style type="text/css">
		@media screen and (min-width: 768px) {
			#mapa_content {width:100%; height:150px;}
			#modalUnidad .modal-dialog {width:60%;}

			.delimitado {
				height: 350px !important;
				overflow: scroll;
			}​
			.auditor{background:#f4f4f4; font-size:7pt; padding:2px; display:inline; margin:2px; border:1px black solid;}
			label {text-align:right;}
			caption {padding: .2em .8em;border-bottom: 1px solid #fFF;background:#f4f4f4; font-weight: bold;}
		}
	</style>


	<!-- **************************************  INICIA ÁREA DE JAVA SCRIPT ******************************* -->

	<script type="text/javascript">



	// ******************  ALTA DE REGISTROS  *********************

	function seleccionarUnidadesByCuenta() {

		sIdCuenta = document.all.txtCuentas.options[document.all.txtCuentas.selectedIndex].value;
		nPosicionCuenta = document.all.txtCuentas.selectedIndex;
		//alert("El valor de document.all.txtCuentas.selectedIndex es: " + document.all.txtCuentas.selectedIndex );

		if (nPosicionCuenta > 0){
			//* Se modificara la variable de ambiente en PHP, para que esta posteriormente se utilice en
			//* los llamados a la BD para extraer las unidades de la cuenta seleccionada
			liga = '/actualizaVarSession/' + sIdCuenta;
			$.ajax({ async: false, type: 'GET', url: liga , success: function(response) { /* alert(response); */ } });

			var liga = '/catEntidades/' + sIdCuenta;
			$.ajax({ type: 'GET', url: liga ,
				success: function(response) {
		            var jsonData = JSON.parse(response);

		            // borra el contenido de la tabla
					document.getElementById('tblListaUnidades').innerHTML="";
	               	
	               	//Define las variables requeridas y llena la tabla
	                var renglon, columna;

	                for (var i = 0; i < jsonData.datos.length; i++) {

	                    var dato = jsonData.datos[i];

	                    var sIdCuenta              = dato.idCuenta;
	                    var sIdSector              = dato.idSector;
	                    var sIdSubsector           = dato.idSubsector;
	                    var sCentroGestor		   = dato.centroGestor;
	                    var sIdUnidad              = dato.idUnidad;
	                    var sNombre                = dato.nombre;
	                    var sSiglas				   = dato.siglas;
	                    var sTitular               = dato.titular;

	                    var sIdUnidadSector        = dato.idUnidadSector;
	                    var sUnidadSector          = dato.unidadSector;

	                    var sIdUnidadPoder 		   = dato.idUnidadPoder;
	                    var sUnidadPoder 		   = dato.unidadPoder;

	                    var sIdUnidadClasificacion = dato.idUnidadClasificacion;
	                    var sUnidadClasificacion   = dato.unidadClasificacion;

	                    // Crea y asigna atributos al elemento TR, incluyendo el estilo, la función onclick y sus datos.
		                renglon       = document.createElement("TR");
						renglon.style = "font-size: xx-small";
						renglon.setAttribute('onclick', "recuperarCPUnidad(\""+sIdCuenta+"\",\""+sIdSector+"\",\""+sIdSubsector+"\",\""+sIdUnidad+"\",\""+sNombre+"\");" );

		                renglon.innerHTML = "" +
						//'<td width="5%" > ' + sIdCuenta     	   + "</td>"+
						'<td width="5%" > ' + sCentroGestor        + "</td>"+
						'<td width="25%"> ' + sNombre              + "</td>"+
						'<td width="25%"> ' + sTitular             + "</td>"+
						'<td width="15%"> ' + sUnidadSector        + "</td>"+
						'<td width="15%"> ' + sUnidadPoder         + "</td>"+
						'<td width="10%"> ' + sUnidadClasificacion + "</td>"+
						"<td width='5%'><img src='img/pdf.gif'></td>";

	                    document.getElementById('tblListaUnidades').appendChild(renglon);
	                }
				},
				error: function(xhr, textStatus, error){
					alert(' Error en function seleccionarCuenta()  TextStatus: ' + textStatus + ' Error: ' + error );
					return false;
				}
			});
		}
	}

	// ******************  MODIFICACION DE REGISTROS  *******************

	//<?
	//$ruta = '/anexos/cuentas/cta2015/unidades/'
	//function listar_directorios_ruta($ruta){
	//  // abrir un directorio y listarlo recursivo
	//   if (is_dir($ruta)) {
	//      if ($dh = opendir($ruta)) {
	//         while (($file = readdir($dh)) !== false) {
	//            //esta línea la utilizaríamos si queremos listar todo lo que hay en el directorio
	//            //mostraría tanto archivos como directorios
	//            //echo "<br>Nombre de archivo: $file : Es un: " . filetype($ruta . $file);
	//            if (is_dir($ruta . $file) && $file!="." && $file!=".."){
	//               //solo si el archivo es un directorio, distinto que "." y ".."
	//               echo "<br>Directorio: $ruta$file";
	//               listar_directorios_ruta($ruta . $file . "/");
	//            }
	//         }
	//      closedir($dh);
	//      }
	//   }else
	//      echo "<br>No es ruta valida";
	//} 
	//?>

	function fileExists(fileLocation) {
	    var response = $.ajax({
	        url: fileLocation,
	        type: 'HEAD',
	        async: false
	    }).status;
	    alert(response);
	}

	function recuperarCPUnidad(sIdCuenta, sIdSector, sidSubsector, sIdUnidad, sNombre){
		var rutaServidor  = 'anexos*' + sIdCuenta + '*unidades';
		var rutaServer    = 'anexos/' + sIdCuenta + '/unidades/';
		var liga = 'obtenArchivosUnidadesPorCta/' + rutaServidor;
		var aArchivos;
		var centroGestorUnidad;
		var nombreCompleto;
		var resultado;
		var posicion;

		//alert("El valor de liga es: " + liga );

		$.ajax({ type: 'GET', url: liga,
			success: function(response) {
				//response = response.replace(/^\s*|\s*$/g*, '');
				posicion = response.indexOf("{");
				resultado = response.substr(posicion+1);
				aArchivos = resultado.split("*");

            	centroGestorUnidad = sIdSector+sidSubsector+sIdUnidad;
	            nombreCompleto = "";

	            for (var i = aArchivos.length - 1; i >= 0; i--) {

	            	centroGestorArchivo = aArchivos[i].substr(0,6);
	            	centroGestorArchivo = centroGestorArchivo.replace("\n","");
	            	centroGestorArchivo = centroGestorArchivo.replace("\r","");

	            	//alert(" Buscando en " + liga + " => (" + i + ") Comparando: SI: " + centroGestorArchivo + " ES IGUAL A " + centroGestorUnidad);

	            	if ( centroGestorArchivo == centroGestorUnidad ){
	            		nombreCompleto = rutaServer + aArchivos[i];
	            		break;
	            	}
	            }
            	if ( nombreCompleto.length > 0 ){
            		//alert("Se localizo el archivo en la posición de aArchivos[" + i + "] tiene el siguiente nombre: " + aArchivos[i]);
            		alert("Ubicación: " + rutaServer + aArchivos[i]);
            		window.open( rutaServer + aArchivos[i], '_blank' );
            	}else{
            		alert("No se localizo el archivo correspondiente a: \nUnidad " + sNombre + "\nCentro Gestor " + centroGestorUnidad + "\nCuenta " + sIdCuenta);
            	}

			},
			error: function(xhr, textStatus, error){
				alert(' Error en function recuperarCPUnidad()  TextStatus: ' + textStatus + ' Error: ' + error );
				//return false;
			}			
		});
	}

	// ********************************************* ZONA DE JQUERY ******************************************
	var nUsr='<?php echo $_SESSION["idUsuario"];?>';
	var nCampana='<?php echo $_SESSION["idCuentaActual"];?>';

	$(document).ready(function(){

		getMensaje('txtNoti',1);

		recuperarLista('lstCuentas', document.all.txtCuentas);

		if(nUsr!="" && nCampana!=""){
			cargarMenu( nCampana);
		}else{
			if(nCampana=="")alert("Debe establecer una CAMPAÑA como PREDETERMINADA. Por favor consulte con su administrador del sistema.");
		}

		// **************************** DEFINICIONES DE BOTONES ***********************************************
		// **************************** FIN DE DEFINICIONES DE BOTONES ********************************************

	});

	</script>
	<!-- **************************************  FINALIZA ÁREA DE JAVA SCRIPT ******************************* -->

	<!-- *******************************************  CONTINUA ÁREA HTML ************************************ -->

	<!-- Title and other stuffs -->
	<title>Sistema Integral de Auditorias</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="">
	<meta name="keywords" content="">
	<meta name="author" content="">


	<!-- Stylesheets -->
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<!-- Font awesome icon -->
	<link rel="stylesheet" href="css/font-awesome.min.css">
	<!-- jQuery UI -->
	<link rel="stylesheet" href="css/jquery-ui.css">
	<!-- Calendar -->
	<link rel="stylesheet" href="css/fullcalendar.css">
	<!-- prettyPhoto -->
	<link rel="stylesheet" href="css/prettyPhoto.css">
	<!-- Star rating -->
	<link rel="stylesheet" href="css/rateit.css">
	<!-- Date picker -->
	<link rel="stylesheet" href="css/bootstrap-datetimepicker.min.css">
	<!-- CLEditor -->
	<link rel="stylesheet" href="css/jquery.cleditor.css">
	<!-- Bootstrap toggle -->
	<link rel="stylesheet" href="css/jquery.onoff.css">
	<!-- Main stylesheet -->
	<link href="css/style-dashboard.css" rel="stylesheet">
	<!-- Widgets stylesheet -->
	<link href="css/widgets.css" rel="stylesheet">

	<script src="./Dashboard - MacAdmin_files/respond.min.js"></script>
	<!--[if lt IE 9]>
	<script src="js/html5shiv.js"></script>
	<![endif]-->

	<!-- Favicon -->
	<link rel="shortcut icon" href="img/favicon.png">

	<body>
		<nav class="navbar navbar-default navbar-fixed-top">
			<div class="container-fluid">
				<nav class="collapse navbar-collapse bs-navbar-collapse" role="navigation">
				    <div class="col-xs-12">
						<div class="col-xs-2"><a href="/"><img src="img/logo-top.png"></a></div>
							<div class="col-xs-2">
								<ul class="nav navbar-nav "><li><a href="#"><i class="fa fa-th-list"></i> <?php echo $_SESSION["sCuentaActual"] ?></a></li></ul>
							</div>
							<div class="col-xs-3"><h2>Cuenta Pública por Unidades</h2></a></div>
	                    <div class="col-xs-2">
	                    	<ul class="nav navbar-nav "><li><a href="./notificaciones"><i class="fa fa-envelope-o"></i> Tiene <span><input type="text"  class="noti" id="txtNoti"></input></span> Mensaje(s).</a></li></ul>
	                   	</div>

						<div class="col-xs-3">
							<ul class="nav navbar-nav  pull-right">
								<li class="dropdown pull-right">
									<a data-toggle="dropdown" class="dropdown-toggle" href="/">
										<i class="fa fa-user"></i> <b>C. <?php echo $_SESSION["sUsuario"] ?></b> <b class="caret"></b>
									</a>
									<ul class="dropdown-menu">
									  <li><a href="./perfil"><i class="fa fa-user"></i> Perfil</a></li>
									  <li><a href="./cerrar"><i class="fa fa-sign-out"></i> Salir</a></li>
									</ul>
								</li>
							</ul>
						</div>
					</div>
				</nav>
			</div>
		</nav>

		<!-- Header starts -->
		<header>
		</header>
		<!-- Header ends -->

		<!-- Main content starts -->

		<div class="content">

		  	<!-- Sidebar -->
		    <div class="sidebar">
		        <div class="sidebar-dropdown"><a href="/">Navigation</a></div>
				<!--- Sidebar navigation -->
				<ul id="nav">
				  <li class="has_sub"><a href="/"><i class="fa fa-home"></i> Inicio</a></li>

				  <li class="has_sub"  id="GESTION" style="display:none;">
					<a href=""><i class="fa fa-pencil-square-o"></i> Gestión<span class="pull-right"><i class="fa fa-chevron-right"></i></span></a>
					<ul id="GESTION-UL"></ul>
				  </li>

				  <li class="has_sub"  id="PROGRAMA" style="display:none;">
					<a href=""><i class="fa fa-bars"></i> Programas<span class="pull-right"><i class="fa fa-chevron-right"></i></span></a>
					<ul id="PROGRAMA-UL"></ul>
				  </li>

				  <li class="has_sub"  id="AUDITORIA" style="display:none;">
					<a href=""><i class="fa fa-search"></i> Auditorías<span class="pull-right"><i class="fa fa-chevron-right"></i></span></a>
					<ul id="AUDITORIA-UL"></ul>
				  </li>

				  <li class="has_sub"  id="OBSERVACIONES" style="display:none;">
					<a href=""><i class="fa fa-cogs"></i> Acciones<span class="pull-right"><i class="fa fa-chevron-right"></i></span></a>
					<ul id="OBSERVACIONES-UL"></ul>
				  </li>

				  <li class="has_sub"  id="CONFIGURACION" style="display:none;">
					<a href=""><i class="fa fa-pencil-square-o"></i> Catálogos<span class="pull-right"><i class="fa fa-chevron-right"></i></span></a>
					<ul id="CONFIGURACION-UL"></ul>
				  </li>

				  <li class="has_sub"  id="REPORTEADOR" style="display:none;">
					<a href=""><i class="fa fa-file-text-o"></i> Informes<span class="pull-right"><i class="fa fa-chevron-right"></i></span></a>
					<ul id="REPORTEADOR-UL"></ul>
				  </li>

				  <li class="has_sub"  id="NORMATIVIDAD" style="display:none;">
					<a href=""><i class="fa fa-pencil-square-o"></i> Normatividad<span class="pull-right"><i class="fa fa-chevron-right"></i></span></a>
					<ul id="NORMATIVIDAD-UL"></ul>
				  </li>

				  <li class="has_sub"><a href="/cerrar"><i class="fa fa-sign-out"></i> Salir</a></li>

				</ul>
			</div>
			 <!-- Sidebar ends -->

		  	<!-- Main bar -->
		  	<div class="mainbar">

				<div class="col-md-12">
					<div class="widget">
						<div class="widget-head">
							<div class="pull-left"><h3 class="pull-left"><i class="fa fa-home"></i> Unidades registradas</h3></div>
							<div widget-icons pull-right">
								<div class="col-xs-10">
									<label class="col-xs-7 control-label">Seleccionar Cuenta</label>
									<div class="col-xs-3">
										<select name="txtCuentas" id="txtCuentas" class="form-control" onChange=" javascript:seleccionarUnidadesByCuenta();">
											<option value="">Seleccione...</option>
										</select>
									</div>
								</div>
							</div>
							<div class="clearfix"></div>
						</div>

						<div class="widget-content">
							<div class="table-responsive" style="height: 400px; overflow: auto; overflow-x:hidden;">
								<table class="table table-striped table-bordered table-hover table-condensed ">
									<thead > 
										<tr>
											<!-- <th>Cuenta</th> -->
									 		<th>Centro Gestor</th>
									 		<th>Nombre Unidad</th>
									 		<th>Nombre Titular de Unidad</th>
									 		<th>Sector</th>
									 		<th>Poder</th>
									 		<th>Agrupación</th>
									 		<th>Archivo</th>
										</tr>
								  	</thead>
								  	<tbody id="tblListaUnidades" >
										<?php foreach($datos as $key => $valor): ?>
										<?php 
											$miFuncion = "javascript:recuperarCPUnidad('" . $valor['idCuenta'] . "','" . $valor['idSector'] . "','" . $valor['idSubsector'] . "','" . $valor['idUnidad'] .  "','" . $valor['nombre'] . "')"
										?>

										<tr onclick="<?php echo $miFuncion  ?>" style="width: 100%; font-size: xx-small">	
											<!--
										    <td width="5%"><?php  echo $valor['idCuenta'] ?></td>
										    -->
										    <td width="5%"><?php  echo $valor['centroGestor'] ?></td>
										    <td width="25%"><?php echo $valor['nombre'] ?></td>
										    <td width="25%"><?php echo $valor['titular'] ?></td>
										    <td width="15%"><?php echo $valor['unidadSector'] ?></td>
										    <td width="15%"><?php echo $valor['unidadPoder'] ?></td>
										    <td width="10%"><?php echo $valor['unidadClasificacion'] ?></td>
											<td width='5%'><img src='img/pdf.gif'></td>
										</tr>
										<?php endforeach; ?>
								  	</tbody >
								</table>
							</div>
						</div>

						<div class="widget-foot">
						</div>

					</div>
				</div>
				<!-- Matter ends -->
			</div>
		   	<!-- Mainbar ends -->
		   <div class="clearfix"></div>
		</div>
		<!-- Content ends -->

		<!-- Footer starts -->
		<footer>
		  <div class="container">
		    <div class="row">
		      <div class="col-md-12">
				<p class="copy">Copyright © 2016 | Auditoría Superior de la Ciudad de México</p>
		      </div>
		    </div>
		  </div>
		</footer>
		<!-- Footer ends -->

		<!-- Scroll to top -->
		<span class="totop" style="display: none;"><a href="#"><i class="fa fa-chevron-up"></i></a></span>

		<!-- JS -->
		<script src="./Dashboard - MacAdmin_files/jquery.js"></script> <!-- jQuery -->
		<script src="./Dashboard - MacAdmin_files/bootstrap.min.js"></script> <!-- Bootstrap -->
		<script src="./Dashboard - MacAdmin_files/jquery-ui.min.js"></script> <!-- jQuery UI -->
		<script src="./Dashboard - MacAdmin_files/moment.min.js"></script> <!-- Moment js for full calendar -->
		<script src="./Dashboard - MacAdmin_files/fullcalendar.min.js"></script> <!-- Full Google Calendar - Calendar -->
		<script src="./Dashboard - MacAdmin_files/jquery.rateit.min.js"></script> <!-- RateIt - Star rating -->
		<script src="./Dashboard - MacAdmin_files/jquery.prettyPhoto.js"></script> <!-- prettyPhoto -->
		<script src="./Dashboard - MacAdmin_files/jquery.slimscroll.min.js"></script> <!-- jQuery Slim Scroll -->
		<script src="./Dashboard - MacAdmin_files/jquery.dataTables.min.js"></script> <!-- Data tables -->

		<!-- jQuery Flot -->
		<script src="./Dashboard - MacAdmin_files/excanvas.min.js"></script>
		<script src="./Dashboard - MacAdmin_files/jquery.flot.js"></script>
		<script src="./Dashboard - MacAdmin_files/jquery.flot.resize.js"></script>
		<script src="./Dashboard - MacAdmin_files/jquery.flot.pie.js"></script>
		<script src="./Dashboard - MacAdmin_files/jquery.flot.stack.js"></script>

		<!-- jQuery Notification - Noty -->
		<script src="./Dashboard - MacAdmin_files/jquery.noty.js"></script> <!-- jQuery Notify -->
		<script src="./Dashboard - MacAdmin_files/default.js"></script> <!-- jQuery Notify -->
		<script src="./Dashboard - MacAdmin_files/bottom.js"></script> <!-- jQuery Notify -->
		<script src="./Dashboard - MacAdmin_files/topRight.js"></script> <!-- jQuery Notify -->
		<script src="./Dashboard - MacAdmin_files/top.js"></script> <!-- jQuery Notify -->
		<!-- jQuery Notification ends -->

		<script src="./Dashboard - MacAdmin_files/sparklines.js"></script> <!-- Sparklines -->
		<script src="./Dashboard - MacAdmin_files/jquery.cleditor.min.js"></script> <!-- CLEditor -->
		<script src="./Dashboard - MacAdmin_files/bootstrap-datetimepicker.min.js"></script> <!-- Date picker -->
		<script src="./Dashboard - MacAdmin_files/jquery.onoff.min.js"></script> <!-- Bootstrap Toggle -->
		<script src="./Dashboard - MacAdmin_files/filter.js"></script> <!-- Filter for support page -->
		<script src="./Dashboard - MacAdmin_files/custom.js"></script> <!-- Custom codes -->
		<script src="./Dashboard - MacAdmin_files/charts.js"></script> <!-- Charts & Graphs -->

	</body>
</html>
