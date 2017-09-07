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
	function agregarTipoCriterio(){
		document.all.txtOperacion.value = "INS";

		limpiarDatos();
		document.all.txtIdTipoCriterio.disabled = false;
		// En la alta se oculta el control del estatus
		document.getElementById("divEstatus").style.display="none";
		
		$('#modalFlotante').removeClass("invisible");
		$('#modalFlotante').modal('toggle');
		$('#modalFlotante').modal('show');
	}

	// ******************  MODIFICACION DE REGISTROS  *******************

	function recuperarTipoCriterio(sIdTipoCriterio){
		var liga = '/obtenerTipoCriterio/' + sIdTipoCriterio;

		$.ajax({ type: 'GET', url: liga ,
			success: function(response) {
	            //var jsonData = JSON.parse(response);   // Cuando se regresan mas de un registro    
				var obj = JSON.parse(response);		// Cuando solo se regresa un registro
				
				limpiarDatos();
				document.all.txtIdTipoCriterio.disabled = true;

				// En modificacion se asgura que se muestre el control del estatus
				document.getElementById("divEstatus").style.display="inline";

				document.all.txtOperacion.value="UPD";
				document.all.txtIdTipoCriterio.value = obj.id;
				document.all.txtNombre.value = obj.nombre;
				document.all.txtNombreAnterior.value = obj.nombre;

				if (obj.relacionarEntes == 'SI'){
					document.all.chkRelEntidades.checked = true;
				}else{
					document.all.chkRelEntidades.checked = false;
				}

				seleccionarElemento(document.all.txtEstatus, obj.estatus);

				$('#modalFlotante').removeClass("invisible");
				$('#modalFlotante').modal('toggle');
				$('#modalFlotante').modal('show');
		
			},
			error: function(xhr, textStatus, error){
				alert(' Error en function recuperarTipoCriterio()  TextStatus: ' + textStatus + ' Error: ' + error );
				return false;
			}			
		});												
	}

	function limpiarDatos(){

		document.all.txtIdTipoCriterio.value="";
		document.all.txtNombre.value="";
		document.all.txtEstatus.selectedIndex=0;
		document.all.chkRelEntidades.value=false;

	}

	function validarDatos(){

		//if (document.all.txtIdTipoCriterio.value == ""){
		//	alert("Debe ingresar la CLAVE del TIPO de CRITERIO.");
		//	document.all.txtIdTipoCriterio.focus();
		//	return false;
		//}	

		if (document.all.txtNombre.value == ""){
			alert("Debe ingresar el NOMBRE del TIPO DE CRITERIO.");
			document.all.txtNombre.focus();
			return false;
		}	

		if (document.all.txtOperacion.value == 'UPD' && document.all.txtEstatus.selectedIndex == 0){
			alert("Debe seleccionar el ESTADO del TIPO del CRITERIO.");
			document.all.txtEstatus.focus();
			return false;
		}	
		return true;
	}

	function validarDuplicidadNombreCriterio(sNombreCriterio){
		var regresa = false;
		if (document.all.txtOperacion == 'INS' || (document.all.txtNombreAnterior.value != document.all.txtNombre.value) ){

			liga = '/obtenerTiposCriterioByNombre/' + sNombreCriterio;

			$.ajax({ async:false, cache:false, type: 'GET', url: liga ,
				success: function(response) { 
		            //var jsonData = JSON.parse(response);   // Cuando se regresan mas de un registro    
					var obj = JSON.parse(response);		// Cuando solo se regresa un registro
					if (obj.total == 0){ regresa = true; }else{ alert("El nombre del criterio ya existe, favor de validar."); regresa = false; }
				},
				error: function(xhr, textStatus, error){
					alert(' Error en function validarDuplicidadNombreCriterio()  TextStatus: ' + textStatus + ' Error: ' + error );
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

		//Se oculta el ID de Tipo de Criterio ya que así lo solicitaron los usuarios.
		document.getElementById("DivIdTipoCriterio").style.display='none';

		// **************************** DEFINICIONES DE BOTONES ***********************************************
		$( "#btnGuardar" ).click(function() {

			if ( validarDatos() ){
				if ( validarDuplicidadNombreCriterio(document.all.txtNombre.value) ){

					//document.all.txtIdTipoAuditoria.disabled = false;
					//document.all.txtOperacion.disabled = false;

					document.all.txtIdTipoCriterio.disabled = false;
					document.all.formulario.submit();
					$('#modalFlotante').modal('hide');
				}
			}
		});

		$( "#btnCancelar" ).click(function() {
			//document.all.btnGuardarEnviar.style.display='none';
			//document.all.btnGuardar.style.display='inline';
			//document.all.btnTurnar.style.display='inline';
			$('#modalFlotante').modal('hide');
		});

		//$( "#btnCancelarAsignacion").click(function(){
		//	$('#modalFlotante').modal('hide');
		//});

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
							<div class="col-xs-3"><h2>Catálogo de Tipos de Criterios</h2></a></div>				                    <!--
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
							<div class="pull-left"><h3 class="pull-left"><i class="fa fa-home"></i> Tipos de Criterios registrados</h3></div>
							<div class="widget-icons pull-right">
								<button onclick="agregarTipoCriterio();" type="button" class="btn btn-primary  btn-xs">Agregar Tipo de Criterio </button> 
							</div>  
							<div class="clearfix"></div>
						</div>             

						<div class="widget-content">						
							<div class="table-responsive" style="height: 400px; overflow: auto; overflow-x:hidden;">
								<table class="table table-striped table-bordered table-hover">
									<thead>
										<tr>
									 		<!-- <th>Id. de Tipo de Criterio</th> -->
									 		<th>Nombre del Tipo de Criterio</th>
									 		<th>Relacionar Entidades</th>
									  		<th>Estatus del Tipo de Criterio</th>
										</tr>
								  	</thead>
								  	
								  	<tbody >					
											<?php foreach($datos as $key => $valor): ?>
											<tr onclick=<?php echo "javascript:recuperarTipoCriterio('" . $valor['id'] . "');"; ?> >
											  <!-- <td width="10%"><?php echo $valor['id']; ?></td> -->
											  <td width="30%"><?php echo $valor['nombre']; ?></td>
											  <td width="15%"><?php echo $valor['relacionarEntes']; ?></td>
											  <td width="15%"><?php echo $valor['estatus']; ?></td>
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
				<form id="formulario" METHOD='POST' action='/guardar/tipoCriterio' role="form">
					<input type='HIDDEN' name='txtOperacion' value=''>	
					<input type='HIDDEN' name='txtNombreAnterior' value=''>	
				
					<!-- Modal content-->
					<div class="modal-content">									
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4 class="modal-title">Registro del Tipo del Criterio...</h4>
						</div>									
						<div class="modal-body">
							<div class="container-fluid">
								<div class="form-group" id="DivIdTipoCriterio">
									<label class="col-xs-4 control-label">Id. Tipo de Criterio</label>
									<div class="col-xs-2">
										<input type="text" class="form-control" name="txtIdTipoCriterio" readonly />
									</div>
								</div>
								<br>
								<div class="form-group">
									<label class="col-xs-4 control-label">Nombre del Tipo del Criterio</label>
									<div class="col-xs-5">
										<input type="text" class="form-control" name="txtNombre" />									
									</div>
								</div>
								<br>
	
								<div class="form-group">
									<label class="col-xs-4"> Relacionar con Entidades </label>
									<div class="checkbox col-xs-1 " style="text-align:left; margin-top: 0px; margin-left: 10px" > 
										<input type="checkbox" name="chkRelEntidades" Id="chkRelEntidades">
									</div>
									<div class="col-xs-6" id="divEstatus">
										<label class="col-xs-4 control-label">Estatus Tipo Criterio</label>
										<div class="col-xs-4">
											<select name="txtEstatus" class="form-control" style="width: 93%">
												<option value="">Seleccione...</option>
												<option value="ACTIVO" selected="">ACTIVO</option>
												<option value="INACTIVO">INACTIVO</option>
											</select>
										</div>
									</div>
								</div>
								<div class="clearfix"></div>
<!--
								<div class="form-group">
									<label class="col-xs-3 control-label">Estatus del Tipo del Criterio</label>
									<div class="col-xs-2">
										<select name="txtEstatus" class="form-control">
											<option value="">Seleccione...</option>
											<option value="ACTIVO" selected="">ACTIVO</option>
											<option value="INACTIVO">INACTIVO</option>
										</select>
									</div>
								</div>
-->
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