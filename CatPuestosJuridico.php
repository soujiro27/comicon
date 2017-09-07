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
			#modalPuestoJuridico .modal-dialog {width:60%;}

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

	function agregarPuestoJuridico() {

		document.all.txtOperacion.value = "INS";
		limpiarDatos();

		// Como es alta ocultar el elemento estatus
		document.getElementById("divEstatus").style.display="none";

		seleccionarElemento(document.all.txtEstatus,'ACTIVO');
		//document.all.txtRpe.disabled   = false;

		$('#modalPuestoJuridico').removeClass("invisible");
		$('#modalPuestoJuridico').modal('toggle');
		$('#modalPuestoJuridico').modal('show');

	}


	// ******************  MODIFICACION DE REGISTROS  *******************

	function actualizarPuestoJuridico(sIdPuesto){

		var sLiga = '/obtenerPuestoJuridico/' + sIdPuesto;

		$.ajax({ type: 'GET', url: sLiga ,
			success: function(response) {
	            //var jsonData = JSON.parse(response);   // Cuando se regresan mas de un registro
				var obj = JSON.parse(response);		// Cuando solo se regresa un registro

				limpiarDatos();

				document.all.txtOperacion.value = "UPD";
				// Como es actualización será necesario asegurarse de que se muestre elemento estatus

				document.getElementById("divEstatus").style.display="inline";

				//seleccionarElemento(document.all.txtTipoAuditoria, obj.idTipoAuditoria);

				document.all.txtIdPuesto.value = obj.idPuestoJuridico;
				document.all.txtIdArea.value   = obj.idArea;
				document.all.txtRpe.value      = obj.rpe;
				//document.all.txtRpe.disabled   = true;

				document.all.txtNombre.value           = obj.nombre;
				document.all.txtPaterno.value          = obj.paterno;
				document.all.txtMaterno.value          = obj.materno;
				document.all.txtPuesto.value           = obj.puesto;
				document.all.txtSaludo.value           = obj.saludo;
				//document.all,txtSiglas.value      = obj.siglas;
				//document.all.txtTitularArea.value = obj.titular;
				seleccionarElemento(document.all.txtAtiendeRecepcion, obj.recepcion);
				seleccionarElemento(document.all.txtTitularArea, obj.titular);
				seleccionarElemento(document.all.txtEstatus, obj.estatus);

				$('#modalPuestoJuridico').removeClass("invisible");
				$('#modalPuestoJuridico').modal('toggle');
				$('#modalPuestoJuridico').modal('show');

			},
				error: function(xhr, textStatus, error){
				alert(' Error en function actualizarPuestoJuridico()  TextStatus: ' + textStatus + ' Error: ' + error );
				return false;
			}
		});
	}

	function limpiarDatos(){

		document.all.txtIdPuesto.value                 = 0 ;
		document.all.txtIdArea.value                   = '';
		document.all.txtRpe.value                      = 0 ;
		document.all.txtNombre.value                   = '';
		document.all.txtPaterno.value                  = '';
		document.all.txtMaterno.value                  = '';
		document.all.txtPuesto.value                   = '';
		document.all.txtTitularArea.selectedIndex      = 0 ;
		document.all.txtSaludo.value                   = '';
		//document.all.txtSiglas.value                   = '';
		document.all.txtAtiendeRecepcion.selectedIndex = 0;
		document.all.txtEstatus.selectedIndex          = 0;
	}

	function validarDatos(){

		if (document.all.txtIdArea.value == '' ){
			alert("Debe ingresar una ÁREA del Puesto en la DGAJ.");
			document.all.txtIdArea.focus();
			return false;
		}

		if (document.all.txtRpe.value == 0 ){
			alert("Debe ingresar un RPE del empleado.");
			document.all.txtRpe.focus();
			return false;
		}

		if (document.all.txtNombre.value == ''){
			alert("Debe ingresar un NOMBRE del empleado para el PUESTO.");
			document.all.txtNombre.focus();
			return false;
		}

		if (document.all.txtPaterno.value == ''){
			alert("Debe ingresar un APELLIDO PATERNO del empleado para el PUESTO.");
			document.all.txtPaterno.focus();
			return false;
		}

		if (document.all.txtMaterno.value == ''){
			alert("Debe ingresar un APELLIDO MATERNO del empleado para el PUESTO.");
			document.all.txtMaterno.focus();
			return false;
		}

		if (document.all.txtPuesto.value == ''){
			alert("Debe ingresar un NOMBRE para el PUESTO.");
			document.all.txtPuesto.focus();
			return false;
		}

		if (document.all.txtTitularArea.selectedIndex == 0){
			alert("Debe indicar si el empleado es TITULAR del ÁREA.")
			document.all.txtTitularArea.focus();
			return false;
		}

		if (document.all.txtOperacion == 'UPD' ){
			if (document.all.txtEstatus.selectedIndex == 0){
				alert("Debe seleccionar el ESTADO del PUESTO.");
				document.all.txtEstatus.focus();
				return false;
			}
		}
		
		return true;
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
		// **************************** LLENADO DE COMBOS INICIALES *****************************************

		//recuperarLista("lstTiposAuditorias",document.all.txtTipoAuditoria);
		

		// **************************** DEFINICIONES DE BOTONES ***********************************************
		
		$( "#btnGuardar" ).click(function() {

			if ( validarDatos() ){

				var sLiga = '/validaExistePuestoJuridico/' + document.all.txtRpe.value;

				$.ajax({
					type: 'GET', url: sLiga ,
					success: function(response) {			
						var obj = JSON.parse(response);

						if (document.all.txtOperacion.value == "INS"){

							if (typeof obj.rpe == "undefined"){

								document.all.txtIdPuesto.disabled = false;
								document.all.formulario.submit();
								$('#modalPuestoJuridico').modal('hide');

							}else{
								alert("El RPE " + document.all.txtRpe.value + " Ya existe, Por favor verifique.");
							}
						} else {

							if ( obj.rpe != null && obj.rpe == document.all.txtRpe.value ){

								document.all.txtIdPuesto.disabled = false;
								document.all.formulario.submit();
								$('#modalPuestoJuridico').modal('hide');

							}else{
								alert("El RPE " + document.all.txtRpe.value + " Ya existe, Por favor verifique.");
							}
						}
			        },
			          error: function(xhr, textStatus, error){
			               alert('func: Guardar Puesto Juridico ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
			        }                                             
				}); 
			}
		});


		$( "#btnCancelar" ).click(function() {
			$('#modalPuestoJuridico').modal('hide');
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
							<div class="col-xs-3"><h2>Puestos Área Jurídica</h2></a></div>
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
							<div class="pull-left">Lista de Puestos del área Juríidica</div>
							<div widget-icons pull-right">
								<div class="widget-icons pull-right"><button id="btnagregarPuestoJuridico" onclick="agregarPuestoJuridico();" class="btn btn-primary btn-xs">Agregar Puesto</button></div>  
							</div>
							<div class="clearfix"></div>
						</div>

						<div class="widget-content">
							<div class="table-responsive" style="height: 400px; overflow: auto; overflow-x:hidden;">
								<table class="table table-striped table-bordered table-hover table-condensed ">
									<thead> 
										<tr>
									 		<th>Id. Área</th>
									 		<th>RPE</th>
											<th>Saludo</th>
									 		<th>Nombre</th>
									 		<th>Apellido Paterno</th>
									 		<th>Apellido Materno</th>
									 		<th>Nombre Puesto</th>
									 		<th>Atiende Recepción</th>
									 		<th>Titular</th>
									  		<th>Estatus</th>
										</tr>
								  	</thead>
								  	<tbody id="tblListaPuestoJuridico" >
										<?php foreach($datos as $key => $valor): ?>

											<tr onclick=<?php echo "javascript:actualizarPuestoJuridico('" . $valor['idPuestoJuridico'] . "');"; ?> style="width: 100%; font-size: xx-medium"> 

										  	<td width="5%"><?php  echo $valor['idArea']; ?></td> 
										  	<td width="5%"><?php  echo $valor['rpe']; ?></td>
										  	<td width="5%"><?php  echo $valor['saludo']; ?></td>
										  	<td width="15%"><?php  echo $valor['nombre']; ?></td>
										  	<td width="15%"><?php  echo $valor['paterno']; ?></td>
										  	<td width="15%"><?php  echo $valor['materno']; ?></td>
										  	<td width="20%"><?php  echo $valor['puesto']; ?></td>
										  	<td width="5%"><?php  echo $valor['recepcion']; ?></td>
										  	<td width="5%"><?php  echo $valor['titular']; ?></td>
										  	<td width="5%"><?php  echo $valor['estatus']; ?></td>
										</tr>
										<?php endforeach; ?>
								  	</tbody >
								</table>
							</div>
						</div>

						<div class="widget-foot">
							<div class="pull-left"></div>
						</div>

					</div>
				</div>
				<!-- Matter ends -->
			</div>
		   	<!-- Mainbar ends -->
		   <div class="clearfix"></div>
		</div>
		<!-- Content ends -->

		<div id="modalPuestoJuridico" class="modal fade" role="dialog">
			<div class="modal-dialog">
				<form id="formulario" METHOD='POST' action='/guardar/PuestosJuridico' role="form">
					<input type='HIDDEN' name='txtOperacion' value=''>
					<input type='HIDDEN' name='txtIdPuesto' value=''>
					<input type='HIDDEN' name='txtSiglas' value=''>
					
					<!-- Modal content-->
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4 class="modal-title">Registro de Puestos del Área Jurídica...</h4>
						</div>

						<div class="modal-body">
							<div class="container-fluid">

								<div class="form-group">
									<label class="col-xs-2 control-label">Clave de Area</label>
									<div class="col-xs-2">
										<input type="text" class="form-control" name="txtIdArea" id="txtIdArea" >
									</div>
								</div>
								<br>

								<div class="form-group">
									<label class="col-xs-2 control-label">Número de RPE</label>
									<div class="col-xs-1">
										<input type="text" class="form-control" name="txtRpe" id="txtRpe" >
									</div>
								</div>
								<br>

								<div class="form-group">
									<label class="col-xs-2 control-label">Saludo</label>
									<div class="col-xs-2">
										<input type="text" class="form-control" name="txtSaludo" id="txtSaludo" >
									</div>
								</div>
								<br>

								<div class="form-group">
									<label class="col-xs-2 control-label">Nombre Empleado</label>
									<div class="col-xs-5">
										<input type="text" class="form-control" name="txtNombre" id="txtNombre" >
									</div>
								</div>
								<br>

								<div class="form-group">
									<label class="col-xs-2 control-label">Apellido Paterno</label>
									<div class="col-xs-5">
										<input type="text" class="form-control" name="txtPaterno" id="txtPaterno">
									</div>
								</div>
								<br>

								<div class="form-group">
									<label class="col-xs-2 control-label">Apellido Materno</label>
									<div class="col-xs-5">
										<input type="text" class="form-control" name="txtMaterno" id="txtMaterno">
									</div>
								</div>
								<br>

								<div class="form-group">
									<label class="col-xs-2 control-label">Nombre de Puesto</label>
									<div class="col-xs-9">
										<input type="text" class="form-control" name="txtPuesto" id="txtPuesto">
									</div>
								</div>
								<br>

																
								<!--
								<div class="form-group">
									<label class="col-xs-2 control-label"> Siglas</label>
									<div class="col-xs-3">
										<input type="text" class="form-control" name="txtSiglas" id="txtSiglas">
									</div>
								</div>
								<br>
								-->

								<div class="form-group">
									<label class="col-xs-2 control-label">Atiende Recepción</label>
									<div class="col-xs-2" >
										<select name="txtAtiendeRecepcion" class="form-control" >
											<option value="">Seleccione...</option>
											<option value="SI" selected="">SI</option>
											<option value="NO">NO</option>
										</select>
									</div>
								</div>
								<br>
								
								<div class="form-group">
									<label class="col-xs-2 control-label">Titular del área</label>
									<div class="col-xs-2" >
										<select name="txtTitularArea" class="form-control" >
											<option value="">Seleccione...</option>
											<option value="SI" selected="">SI</option>
											<option value="NO">NO</option>
										</select>
									</div>
								</div>
								<br>

								<div class="form-group" id="divEstatus">
									<label class="col-xs-2 control-label">Estatus Parámetro</label>
									<div class="col-xs-2" >
										<select name="txtEstatus" class="form-control" >
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