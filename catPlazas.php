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
			#modalFlotante .modal-dialog  {width:80%;}

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
	function agregarPlaza(){
		document.all.txtOperacion.value = "INS";

		limpiarDatos();
		document.all.txtPlaza.disabled = false;
		document.all.txtNombramiento.disabled = false;

		// En la alta se oculta el control del estatus
		document.getElementById("divEstatus").style.display="none";
		
		$('#modalFlotante').removeClass("invisible");
		$('#modalFlotante').modal('toggle');
		$('#modalFlotante').modal('show');
	}

	// ******************  MODIFICACION DE REGISTROS  *******************

	function recuperarPlaza(sIdPlaza){
		
		var sIdPlaza = sIdPlaza.replace('/','_');

		var liga = '/obtenerPlaza/' + sIdPlaza;

		$.ajax({ type: 'GET', url: liga ,
			success: function(response) {
				var obj = JSON.parse(response);		// Cuando solo se regresa un registro
				
				limpiarDatos();
				document.all.txtPlaza.disabled = true;
				document.all.txtNombramiento.disabled = true;

				// En modificacion se asgura que se muestre el control del estatus
				document.getElementById("divEstatus").style.display="inline";

				document.all.txtOperacion.value="UPD";

				document.all.txtPlaza.value=obj.idPlaza;
				document.all.txtPlazaAnterior=obj.idPlaza;
				document.all.txtNombrePlaza.value=obj.Nombre;

				seleccionarElemento(document.all.txtArea, obj.idArea);
				seleccionarElemento(document.all.txtPuesto, obj.idPuesto);
				seleccionarElemento(document.all.txtNivel, obj.idNivel);
				seleccionarElemento(document.all.txtNombramiento, obj.idNombramiento);

				seleccionarElemento(document.all.txtEstatus, obj.estatus);
				seleccionarElemento(document.all.txtPlazaSuperior, obj.idPlazaSuperior);

				$('#modalFlotante').removeClass("invisible");
				$('#modalFlotante').modal('toggle');
				$('#modalFlotante').modal('show');
		
			},
			error: function(xhr, textStatus, error){
				alert(' Error en function recuperarPlaza()  TextStatus: ' + textStatus + ' Error: ' + error );
				return false;
			}			
		});												
	}

	function limpiarDatos(){

		document.all.txtArea.selectedIndex = 0;
		document.all.txtPuesto.selectedIndex = 0;
		document.all.txtNivel.selectedIndex = 0;
		document.all.txtNombramiento.selectedIndex = 0;

		document.all.txtPlaza.value="";
		document.all.txtNombrePlaza.value="";

		document.all.txtPlazaSuperior.selectedIndex=0;
		document.all.txtEstatus.selectedIndex=0;
	}

	function validarDatos(){

		if (document.all.txtArea.selectedIndex == 0){
			alert("Debe seleccionar el AREA.");
			document.all.txtArea.focus();
			return false;
		}	

		if (document.all.txtPuesto.selectedIndex == 0){
			alert("Debe seleccionar el PUESTO.");
			document.all.txtPuesto.focus();
			return false;
		}	

		if (document.all.txtNivel.selectedIndex == 0){
			alert("Debe seleccionar el NIVEL.");
			document.all.txtNivel.focus();
			return false;
		}	

		if (document.all.txtNombramiento.selectedIndex == 0){
			alert("Debe seleccionar el NOMBRAMIENTO.");
			document.all.txtNombramiento.focus();
			return false;
		}	

		if (document.all.txtPlaza.value == ""){
			alert("Debe ingresar la CLAVE de la PLAZA.");
			document.all.txtPlaza.focus();
			return false;
		}	

		if (document.all.txtNombrePlaza.value == ""){
			alert("Debe ingresar el NOMBRE de la PLAZA.");
			document.all.txtNombrePlaza.focus();
			return false;
		}	

		if (document.all.txtPlazaSuperior.selectedIndex == 0){
			alert("Debe seleccionar la PLAZA del SUPERIOR.");
			document.all.txtPlazaSuperior.focus();
			return false;
		}	

		if (document.all.txtEstatus.selectedIndex == 0 & document.all.txtOperacion == 'UPD'){
			alert("Debe seleccionar el ESTADO de la PLAZA.");
			document.all.txtEstatus.focus();
			return false;
		}	

		return true;
	}

	function validarDuplicidadPlaza(sIdPlaza){
		var regresa = false;
		if (document.all.txtOperacion == 'INS' || (document.all.txtPlazaAnterior.value != document.all.txtPlaza.value) ){

			var sIdPlaza = sIdPlaza.replace('/','_');

			liga = '/obtenerPlazaById/' + sIdPlaza;

			$.ajax({ async:false, cache:false, type: 'GET', url: liga ,
				success: function(response) { 
		            //var jsonData = JSON.parse(response);   // Cuando se regresan mas de un registro    
					var obj = JSON.parse(response);		// Cuando solo se regresa un registro
					if (obj.total == 0){ regresa = true; }else{ alert("La Plaza ingresada ya existe, favor de validar."); regresa = false; }
				},
				error: function(xhr, textStatus, error){
					alert(' Error en function validarDuplicidadPlaza()  TextStatus: ' + textStatus + ' Error: ' + error );
					regresa = false;
				}			
			});												
		}else{
			regresa = true;
		}
		return regresa;
	}

	function actualizarLstPLazasSuperior(sidPlaza){
		recuperarLista('lstTodasPlazas/' + sidPlaza, document.all.txtPlazaSuperior);

	}


	// ********************************************* ZONA DE JQUERY ******************************************
	var nUsr       = '<?php echo $_SESSION["idUsuario"];?>';		
	var nCampana   = '<?php echo $_SESSION["idCuentaActual"];?>';	
	var sGlobal    = '<?php echo $_SESSION["usrGlobal"];?>';	
	var sGlobalArea= '<?php echo $_SESSION["usrGlobalArea"];?>';
	var sArea      = '<?php echo $_SESSION["idArea"];?>';

	$(document).ready(function(){

		getMensaje('txtNoti',1);

		if(nUsr!="" && nCampana!=""){
			cargarMenu( nCampana);			
		}else{
			if(nCampana=="")alert("Debe establecer una CAMPAÑA como PREDETERMINADA. Por favor consulte con su administrador del sistema.");
		}					

		// **************************** CARGA INICIAL DE CATÁLOGOS ***********************************************
		if ( sGlobal == "SI" ){
			recuperarLista('lstAreas_HVS2', document.all.txtArea);
		} else if ( sGlobalArea == "SI" ){
			recuperarLista('lstAreas_HVS2/' + sArea, document.all.txtArea);
		}
		recuperarLista('lstPuestos', document.all.txtPuesto);
		recuperarLista('lstNiveles', document.all.txtNivel);
		recuperarLista('lstNombramientos', document.all.txtNombramiento);

		if ( sGlobal == "SI" ){
			recuperarLista('lstTodasPlazas', document.all.txtPlazaSuperior);
		} else if ( sGlobalArea == "SI" ){
			recuperarLista('lstTodasPlazas/' + sArea, document.all.txtPlazaSuperior);
		}

		// **************************** DEFINICIONES DE BOTONES ***********************************************
		$( "#btnGuardar" ).click(function() {

			if ( validarDatos() ){
				if ( validarDuplicidadPlaza(document.all.txtPlaza.value) ){
					document.all.formulario.submit();
					$('#modalFlotante').modal('hide');
				}
			}
		});

		$( "#btnCancelar" ).click(function() {
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
							<div class="col-xs-3"><h2>Catálogo de Plazas</h2></a></div>				                    <!--
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
							<div class="pull-left"><h3 class="pull-left"><i class="fa fa-home"></i> Plazas registradas</h3></div>
							<div class="widget-icons pull-right">
								<button onclick="agregarPlaza();" type="button" class="btn btn-primary  btn-xs">Agregar Plaza </button> 
							</div>  
							<div class="clearfix"></div>
						</div>             

						<div class="widget-content">						
							<div class="table-responsive" style="height: 400px; overflow: auto; overflow-x:hidden;">
								<table class="table table-striped table-bordered table-hover">
									<thead>
										<tr>
									 		<!-- <th>Id. de Tipo de Criterio</th> -->
									 		<th>Area</th>
									 		<th>Puesto</th>
									 		<th>Nivel</th>
									 		<th>Nombramiento</th>
									 		<th>Plaza</th>
									 		<th>Empleado</th>|
									 		<th>Plaza Superior</th>
										</tr>
								  	</thead>

								  	<tbody>					
										<?php foreach($datos as $key => $valor): ?>
											<tr onclick=<?php echo "javascript:recuperarPlaza('" . $valor['idPlaza'] . "');"; ?> style="width: 100%; font-size: xx-small">
											  <td width="20%"><?php echo $valor['idArea']          . ' - ' . $valor['area']; ?></td>
											  <td width="8%"><?php echo $valor['idPuesto']        . ' - ' . $valor['puesto']; ?></td>
											  <td width="8%"><?php  echo $valor['idNivel']         . ' - ' . $valor['nivel']; ?></td>
											  <td width="8%"><?php  echo $valor['idNombramiento']  . ' - ' . $valor['nombramiento']; ?></td>
											  <td width="20%"><?php  echo $valor['idPlaza']         . ' - ' . $valor['plaza']; ?></td>
											  <td width="20%"><?php  echo $valor['nombre']; ?></td>
											  <td width="20%"><?php  echo $valor['idPlazaSuperior'] . ' - ' . $valor['PlazaSuperior']; ?></td>
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
				<form id="formulario" METHOD='POST' action='/guardar/plaza' role="form">
					<input type='HIDDEN' name='txtOperacion' value=''>	
					<input type='HIDDEN' name='txtPlazaAnterior' value=''>	
				
					<!-- Modal content-->
					<div class="modal-content">									
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4 class="modal-title">Registro de Plaza...</h4>	
						</div>									
						<div class="modal-body">
							<div class="container-fluid">

								<div class="form-group">
									<label class="col-xs-2 control-label">Plaza</label>
									<div class="col-xs-3">
										<input type="text" class="form-control" name="txtPlaza" />									
									</div>
								</div>
								<br>
	
								<div class="form-group">
									<label class="col-xs-2 control-label">Nombre</label>
									<div class="col-xs-9">
										<input type="text" class="form-control" name="txtNombrePlaza" />									
									</div>
								</div>
								<br>

								<div class="form-group">
									<label class="col-xs-2 control-label">Area</label>
									<div class="col-xs-9">
										<select name="txtArea" class="form-control" onChange="javascript:actualizarLstPLazasSuperior(this.value);">
											<option value="">Seleccione...</option>
										</select>
									</div>
								</div>
								<br>

								<div class="form-group">
									<label class="col-xs-2 control-label">Puesto</label>
									<div class="col-xs-9">
										<select name="txtPuesto" class="form-control">
											<option value="">Seleccione...</option>
										</select>
									</div>
								</div>
								<br>

								<div class="form-group">
									<label class="col-xs-2 control-label">Nivel</label>
									<div class="col-xs-3">
										<select name="txtNivel" class="form-control">
											<option value="">Seleccione...</option>
										</select>
									</div>
								</div>
								<br>

								<div class="form-group">
									<label class="col-xs-2 control-label">Nombramiento</label>
									<div class="col-xs-3">
										<select name="txtNombramiento" class="form-control">
											<option value="">Seleccione...</option>
										</select>
									</div>
								</div>
								<br>

								<div class="form-group">
									<label class="col-xs-2 control-label">Plaza Superior</label>
									<div class="col-xs-9">
										<select name="txtPlazaSuperior" class="form-control">
											<option value="">Seleccione...</option>
										</select>
									</div>
								</div>
								<br>
								<!--
								<div class="col-xs-6" >
									<label class="col-xs-4 control-label">Estatus</label>
									<div class="col-xs-4">
										<select name="txtEstatus" class="form-control" style="width: 93%">
											<option value="">Seleccione...</option>
											<option value="ACTIVO" selected="">ACTIVO</option>
											<option value="INACTIVO">INACTIVO</option>
										</select>
									</div>
								</div>
								-->

								<div class="form-group" id="divEstatus">									
									<label class="col-xs-2 control-label">Estatus de Plaza</label>
									<div class="col-xs-3">
										<select class="form-control" name="txtEstatus" id="txtEstatus">
											<option value="">Seleccione...</option>
											<option value="ACTIVO" selected="">ACTIVO</option>
											<option value="INACTIVO">INACTIVO</option>
										</select>
									</div>								
								</div>								
								<br>

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