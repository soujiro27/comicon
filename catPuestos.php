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
			#modalFlotante .modal-dialog  {width:60%;}

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
	var mapa;
	var nZoom=10;

	// ********************************************* ZONA DE FUNCIONES ***************************************
	function inicializar() {
		var opciones = {zoom: nZoom,draggable: false,scrollwheel: true,	mapTypeId: google.maps.MapTypeId.ROADMAP};
		mapa = new google.maps.Map(document.getElementById('mapa_content'), { center: {lat: 19.4339249, lng: -99.1428964},zoom: nZoom});			
	}	
	
		window.onload = function () {
		var chart1; 

		//setGrafica(chart1, "dpsAuditoriasByArea", "pie", "Auditorias", "canvasJG" );
	};

	// ******************  ALTA DE REGISTROS  *********************
	function agregarPuesto(){
		document.all.txtOperacion.value = "INS";

		limpiarDatos();
		document.all.txtIdPuesto.disabled = false;

		// En la alta se oculta el control del estatus
		document.getElementById("divEstatus").style.display="none";

		$('#modalFlotante').removeClass("invisible");
		$('#modalFlotante').modal('toggle');
		$('#modalFlotante').modal('show');
	}

	// ******************  MODIFICACION DE REGISTROS  *******************

	function recuperarPuesto(sIdPuesto){
		var liga = '/obtenerPuesto/' + sIdPuesto;

		$.ajax({ type: 'GET', url: liga ,
			success: function(response) {
	            //var jsonData = JSON.parse(response);   // Cuando se regresan mas de un registro    
				var obj = JSON.parse(response);		// Cuando solo se regresa un registro
				
				limpiarDatos();
				document.all.txtIdPuesto.disabled = true;

				// En modificacion se asgura que se muestre el control del estatus
				document.getElementById("divEstatus").style.display="inline";

				document.all.txtOperacion.value="UPD";
				document.all.txtIdPuesto.value = obj.id;
				document.all.txtIdPuestoAnterior.value = obj.id;
				document.all.txtNombre.value = obj.nombre;
				document.all.txtNombreAnterior.value = obj.nombre;
				document.all.txtNombreCorto.value = obj.nombreCorto;
				document.all.txtNombreCortoAnterior.value = obj.nombreCorto;
				seleccionarElemento(document.all.txtEstatus, obj.estatus);

				$('#modalFlotante').removeClass("invisible");
				$('#modalFlotante').modal('toggle');
				$('#modalFlotante').modal('show');
		
			},
			error: function(xhr, textStatus, error){
				alert(' Error en function recuperarPuesto()  TextStatus: ' + textStatus + ' Error: ' + error );
				return false;
			}			
		});												
	}

	function limpiarDatos(){

		document.all.txtIdPuesto.value="";
		document.all.txtNombre.value="";
		document.all.txtNombreCorto.value="";
		document.all.txtEstatus.selectedIndex=0;

	}

	function validarDatos(){

		if (document.all.txtIdPuesto.value == ""){
			alert("Debe ingresar la CLAVE del PUESTO.");
			document.all.txtIdPuesto.focus();
			return false;
		}	

		if (document.all.txtNombre.value == ""){
			alert("Debe ingresar el NOMBRE del PUESTO.");
			document.all.txtNombre.focus();
			return false;
		}	

		if (document.all.txtNombreCorto.value == ""){
			alert("Debe ingresar el NOMBRE CORTO del PUESTO.");
			document.all.txtNombreCorto.focus();
			return false;
		}	
		if (document.all.txtEstatus.selectedIndex == 0){
			alert("Debe seleccionar el ESTADO del PUESTO.");
			document.all.txtEstatus.focus();
			return false;
		}	

		return true;
	}

	function validarDuplicidadNombrePuesto(sNombrePuesto){
		var regresa = false;
		if (document.all.txtOperacion == 'INS' || (document.all.txtNombreAnterior.value != document.all.txtNombre.value) ){

			liga = '/obtenerPuestosByNombre/' + sNombrePuesto.trim();

			$.ajax({ async:false, cache:false, type: 'GET', url: liga ,
				success: function(response) { 
		            //var jsonData = JSON.parse(response);   // Cuando se regresan mas de un registro    
					var obj = JSON.parse(response);		// Cuando solo se regresa un registro
					if (obj.total == 0){ regresa = true; }else{ alert("El nombre del puesto ya existe, favor de validar."); regresa = false; }
				},
				error: function(xhr, textStatus, error){
					alert(' Error en function validarDuplicidadNombrePuesto()  TextStatus: ' + textStatus + ' Error: ' + error );
					regresa = false;
				}			
			});												
		}else{
			regresa = true;
		}
		return regresa;
	}

	function validarDuplicidadIdPuesto(sIdPuesto){
		var regresa = false;
		if (document.all.txtOperacion == 'INS' || (document.all.txtIdPuestoAnterior.value != document.all.txtIdPuesto.value) ){

			liga = '/obtenerPuestosById/' + sIdPuesto.trim();

			$.ajax({ async:false, cache:false, type: 'GET', url: liga ,
				success: function(response) { 
		            //var jsonData = JSON.parse(response);   // Cuando se regresan mas de un registro    
					var obj = JSON.parse(response);		// Cuando solo se regresa un registro
					if (obj.total == 0){ regresa = true; }else{ alert("El identificador del puesto ya existe, favor de validar."); regresa = false; }
				},
				error: function(xhr, textStatus, error){
					alert(' Error en function validarDuplicidadIdPuesto()  TextStatus: ' + textStatus + ' Error: ' + error );
					regresa = false;
				}			
			});												
		}else{
			regresa = true;
		}
		return regresa;
	}

	function validarDuplicidadNombreCorto(sNombreCorto){
		var regresa = false;
		if (document.all.txtOperacion == 'INS' || (document.all.txtNombreCortoAnterior.value != document.all.txtNombreCorto.value) ){

			liga = '/obtenerPuestosByNombreCorto/' + sNombreCorto.trim();

			$.ajax({ async:false, cache:false, type: 'GET', url: liga ,
				success: function(response) { 
		            //var jsonData = JSON.parse(response);   // Cuando se regresan mas de un registro    
					var obj = JSON.parse(response);		// Cuando solo se regresa un registro
					if (obj.total == 0){ regresa = true; }else{ alert("El nombre corto del puesto ya existe, favor de validar."); regresa = false; }
				},
				error: function(xhr, textStatus, error){
					alert(' Error en function validarDuplicidadNombreCorto()  TextStatus: ' + textStatus + ' Error: ' + error );
					regresa = false;
				}			
			});												
		}else{
			regresa = true;
		}
		return regresa;
	}

	// ********************************************* ZONA DE JQUERY ******************************************
	var nUsr='<?php echo $_SESSION["idUsuario"];?>';		
	var nCampana='<?php echo $_SESSION["idCuentaActual"];?>';	

	$(document).ready(function(){

		getMensaje('txtNoti',1);

		if(nUsr!="" && nCampana!=""){
			cargarMenu( nCampana);			
		}else{
			if(nCampana=="")alert("Debe establecer una CAMPAÑA como PREDETERMINADA. Por favor consulte con su administrador del sistema.");
		}					

		// **************************** DEFINICIONES DE BOTONES ***********************************************
		$( "#btnGuardar" ).click(function() {

			if ( validarDatos() ){

				if ( validarDuplicidadNombrePuesto(document.all.txtNombre.value) ){
					if ( validarDuplicidadIdPuesto(document.all.txtIdPuesto.value) ){
						if ( validarDuplicidadNombreCorto(document.all.txtNombreCorto.value) ){
							//document.all.txtIdTipoAuditoria.disabled = false;
							//document.all.txtOperacion.disabled = false;
							document.all.txtIdPuesto.disabled = false;
							document.all.formulario.submit();
							$('#modalFlotante').modal('hide');

						}
					}
				}
			}
		});

		$( "#btnCancelar" ).click(function() {
			//document.all.btnGuardarEnviar.style.display='none';
			//document.all.btnGuardar.style.display='inline';
			//document.all.btnTurnar.style.display='inline';
			$('#modalFlotante').modal('hide');
		});

		$( "#btnCancelarAsignacion").click(function(){
			$('#modalFlotante').modal('hide');
		});

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
							<div class="col-xs-3"><h2>Catálogo de Puestos</h2></a></div>				                    <!--
							<div class="col-xs-2">
							    <ul class="nav navbar-nav "><li><a href="./notificaciones"><i class="fa fa-envelope-o"></i> Usted tiene <span class="badge">0</span> Mensaje(s).</a></li></ul>
							</div>
							-->
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
							<div class="pull-left"><h3 class="pull-left"><i class="fa fa-home"></i> Puestos registrados</h3></div>
							<div class="widget-icons pull-right">
								<button onclick="agregarPuesto();" type="button" class="btn btn-primary  btn-xs">Agregar Puesto </button> 
							</div>  
							<div class="clearfix"></div>
						</div>             

						<div class="widget-content">						
							<div class="table-responsive" style="height: 400px; overflow: auto; overflow-x:hidden;">
								<table class="table table-striped table-bordered table-hover">
									<thead>
										<tr>
									 		<th>Id. de Puesto</th>
									 		<th>Nombre de Puesto</th>
									  		<th>Nombre Corto</th>							  
									  		<th>Estatus Puesto</th>
										</tr>
								  	</thead>
								  	
								  	<tbody >					
											<?php foreach($datos as $key => $valor): ?>
											<tr onclick=<?php echo "javascript:recuperarPuesto('" . $valor['id'] . "');"; ?> >
											  <td width="10%"><?php echo $valor['id']; ?></td>
											  <td width="40%"><?php echo $valor['nombre']; ?></td>
											  <td width="40%"><?php echo $valor['nombreCorto']; ?></td>
											  <td width="10%"><?php echo $valor['estatus']; ?></td>
											</tr>
											<?php endforeach; ?>                                                                   
								  	</tbody>
								</table>
							</div>
						</div>

						<div class="widget-foot">
							<!--
							<div class="widget-foot">
								<button id="btnAsignarPuesto" class="btn btn-primary btn-xs">Asignar Puesto...</button>
								<div class="clearfix"></div> 
							</div>
							-->
						</div>

					</div>
				</div>
				<!-- Matter ends -->
			</div>
		   	<!-- Mainbar ends -->
		   <div class="clearfix"></div>
		</div>
		<!-- Content ends -->

		<div id="modalFlotante" class="modal fade" role="dialog">
			<div class="modal-dialog">
				<form id="formulario" METHOD='POST' action='/guardar/puesto' role="form">
					<input type='HIDDEN' name='txtIdEmpleado' value=''>						
					<input type='HIDDEN' name='txtOperacion' value=''>						
					<input type='HIDDEN' name='txtNombreAnterior' value=''>						
					<input type='HIDDEN' name='txtNombreCortoAnterior' value=''>
					<input type='HIDDEN' name='txtIdPuestoAnterior' value=''>

					<!-- Modal content-->
					<div class="modal-content">									
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4 class="modal-title">Registro de Puesto...</h4>
						</div>									
						<div class="modal-body">
							<div class="container-fluid">
								<div class="form-group">
									<label class="col-xs-3 control-label">Identificador Puesto</label>
									<div class="col-xs-2">
										<input type="text" class="form-control" name="txtIdPuesto" />
									</div>
								</div>
								<br>
								<div class="form-group">
									<label class="col-xs-3 control-label">Nombre del Puesto</label>
									<div class="col-xs-5">
										<input type="text" class="form-control" name="txtNombre" />									
									</div>
								</div>
								<br>
								<div class="form-group">
									<label class="col-xs-3 control-label">Nombre Corto del Puesto</label>
									<div class="col-xs-3">
										<input type="text" class="form-control" name="txtNombreCorto" />									
									</div>
								</div>
								<br>
								<div class="form-group" id="divEstatus">
									<label class="col-xs-3 control-label">Estatus del Puesto</label>
									<div class="col-xs-2">
										<select name="txtEstatus" class="form-control">
											<option value="">Seleccione...</option>
											<option value="ACTIVO" selected="">ACTIVO</option>
											<option value="INACTIVO">INACTIVO</option>
										</select>
									</div>
								</div>
							</div>
							<div class="clearfix"></div>
						</div>
						
						<div class="modal-footer">
							<button  type="button" class="btn btn-primary active" id="btnGuardar" 	style="display:inline;">Guardar</button>
							<button  type="button" class="btn btn-default active" id="btnCancelar" 	style="display:inline;">Cancelar</button>	
						</div>
					</div>
				</form>
			</div>
		</div>

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