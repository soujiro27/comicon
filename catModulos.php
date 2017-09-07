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
			#mapa_content {width:100%; height:450px;}
			#modalModulo .modal-dialog  {width:55%;}
			.delimitado {  
				height: 350px !important;
				overflow: scroll;
			}​			
		}
	</style>
  
  <script type="text/javascript"> 
  
 
	function limpiarCampos(){
		document.all.txtTipo.selectedIndex=0;
		document.all.txtModulo.value="";
		document.all.txtNombre.value="";
		document.all.txtPanel.selectedIndex=0;
		document.all.txtLiga.value="";
		document.all.txtIcono.value="";
		document.all.txtOrden.value="";
		document.all.txtEstatus.selectedIndex=1;
		// Limpiar MódulosAsignados
	}

	function bloquearCampos(){

		document.all.txtModulo.readOnly=true;
		document.all.txtNombre.readOnly=true;
		document.all.txtLiga.readOnly=true;
		document.all.txtIcono.readOnly=true;
		document.all.txtOdenreadOnly=true;
	}

	function bloquearControles(){
		document.all.txtTipo.disabled=true;
		document.all.txtPanel.disabled=true;
		document.all.txtEstatus.disabled=true;
	}


	function desBloquearCampos(){
		document.all.txtModulo.readOnly=false;
		document.all.txtNombre.readOnly=false;
		document.all.txtLiga.readOnly=false;
		document.all.txtIcono.readOnly=false;
		document.all.txtOrden.readOnly=false;
	}
	
	function bloquearControles(){
		document.all.txtTipo.disabled=false;
		document.all.txtPanel.disabled=false;
		document.all.txtEstatus.disabled=false;
	}

	function validarCaptura(){
		if (document.all.txtModulo.value==""){alert("Debe capturar un MÓDULO.");return false;}
		if (document.all.txtNombre.value==""){alert("Debe capturar el NOMBRE DEL MÓDULO.");return false;}
		if (document.all.txtLiga.value==""){alert("Debe capturar la LIGA DEL MÓDULO.");return false;}
		//if (document.all.txtIcono.value==""){alert("Debe capturar el ICONO DEL MÓDULO.");return false;}
		if (document.all.txtOrden.value==""){alert("Debe capturar el ORDEN DEL MÓDULO.");return false;}
		if (document.all.txtTipo.selectedIndex==0){alert("Debe seleccionar un TIPO PARA EL MÓDULO.");return false;}
		if (document.all.txtPanel.selectedIndex==0){alert("Debe seleccionar un PANEL PARA EL MÓDULO.");return false;}
		if (document.all.txtEstatus.selectedIndex==0){alert("Debe seleccionar un ESTATUS PARA EL MÓDULO.");return false;}
		return true;
	}
	
	function obtenerDatos(sUrl){
		//alert("Valor de sUrl es: " + sUrl);
		$.ajax({
			type: 'GET', url: sUrl ,
			success: function(response) {			
				//alert("response: "+response);
				var obj = JSON.parse(response);

				document.all.txtOperacion.value='UPD';
				document.all.txtModulo.readOnly=true;


				document.all.txtModulo.value='' + obj.modulo;
				document.all.txtNombre.value='' + obj.nombre;
				document.all.txtLiga.value='' + obj.liga;
				document.all.txtIcono.value='' + obj.icono;
				document.all.txtOrden.value='' + obj.orden;
				seleccionarElemento(document.all.txtTipo, obj.tipo);	
				seleccionarElemento(document.all.txtPanel, obj.panel);	
				seleccionarElemento(document.all.txtEstatus, obj.estatus);	
				//recuperarLista('lstModulos_HVS', document.all.txtModulosOrigen);

				$('#modalModulo').removeClass("invisible");
				$('#modalModulo').modal('toggle');
				$('#modalModulo').modal('show');				
				
	            //habilitarEmpleados(obj.tipo);
			},
			error: function(xhr, textStatus, error){
				alert('statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
			}			
		});			
	}


	function ValidaExisteModulo(sModulo){
		//alert("document.all.txtOperacion: " + document.all.txtOperacion.value);
		if(sModulo!=""){
			
			if(document.all.txtOperacion.value == "INS"){
				sModulo = sModulo.toUpperCase();

				$.ajax({
					type: 'GET', url: '/validaExisteModulo/' + sModulo ,
					success: function(response) {
						//alert(response);
						var obj = JSON.parse(response);
						//alert(obj.total);

						if (obj.total > 0 ){
							alert("ATENCION: Ya existe el Módulo: " + sModulo + "\npor favor verifique.");
							document.all.txtModulo.value = "";
							document.all.txtModulo.focus();
						}
				},
					error: function(xhr, textStatus, error){
						alert('function ValidaExistenciaModulo()   -> statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
					}
				});		
			}
		}
	}

	var nUsr='<?php echo $_SESSION["idUsuario"];?>';		
	var nCampana='<?php echo $_SESSION["idCuentaActual"];?>';	
	
	$(document).ready(function(){
		getMensaje("txtNoti",1);
		
		if(nUsr!="" && nCampana!=""){
			cargarMenu( nCampana);			
		}else{
			if(nCampana=="")alert("Debe establecer una CAMPAÑA como PREDETERMINADA. Por favor consulte con su administrador del sistema.");
		}					
		

		$("#btnAgregar" ).click(function() {
			//recuperarLista('lstModulos_HVS', document.all.txtModulosOrigen);
			document.all.txtOperacion.value='INS';
			document.all.txtModulo.readOnly=false;
			limpiarCampos();			
			//bloquearCampos();
			$('#modalModulo').removeClass("invisible");
			$('#modalModulo').modal('toggle');
			$('#modalModulo').modal('show');
			
		});


		$("#btnGuardar" ).click(function() {
			if(validarCaptura()){
				//alert("txtNombre: " + document.all.txtNombre.value);
				//alert(" txtOperacion: " + document.all.txtOperacion.value);
				document.all.formulario.submit();
			}
		});	

		$("#btnCancelar" ).click(function() {
			document.all.txtOperacion.value='';			
			$('#modalModulo').modal('hide');	
		});	
	});
	
	
    </script>
  
  <!-- Title and other stuffs -->
  <title>Sistema Integral de Auditoría</title>
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
  </head>

<body>
	<nav class="navbar navbar-default navbar-fixed-top">
		<div class="container-fluid">
			<nav class="collapse navbar-collapse bs-navbar-collapse" role="navigation">			
				<div class="col-xs-12">
					<div class="col-xs-2"><a href="/"><img src="img/logo-top.png"></a></div>				
					<div class="col-xs-2">
						<ul class="nav navbar-nav "><li><a href="#"><i class="fa fa-th-list"></i> <?php echo $_SESSION["sCuentaActual"] ?></a></li></ul>
					</div>					
					<div class="col-xs-3"><h2>Catálogo de Módulos</h2></a></div>									
					<!--
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
	<header></header>

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
	</div> <!-- Sidebar ends -->
	
  	  	<!-- Main bar -->
  	<div class="mainbar">      
		<!-- Page heading -->
    	<!-- Page heading -->
    	<div class="page-head">
        	<h2 class="pull-left"><i class="fa fa-table"></i> Módulos del Sistema</h2>

        	<!-- Breadcrumb -->
        	<div class="bread-crumb pull-right">
         		 <a href="index.html"><i class="fa fa-home"></i> Home</a> 
          		<!-- Divider -->
          		<span class="divider">/</span> 
          		<a href="#" class="bread-current">Módulos</a>
        	</div>
        	<div class="clearfix"></div>
    	</div>
	    <!-- Page heading ends -->	
		
		<div class="matter">
			<div class="container">

				<!-- Table -->
				<div class="row">
					<div class="col-md-12">				
						<div class="widget">
							<div class="widget-head">
								<div class="pull-left">Lista de Módulos del Sistema</div>
								<div class="widget-icons pull-right"></div>  
							  <div class="clearfix"></div>
							</div>
						
							<div class="widget-content">	
								<div class="table-responsive" style="height: 400px; overflow: auto; overflow-x:hidden;">														
									<div class="">
										<table class="table table-striped table-bordered table-hover">
											<thead>
												<tr>
											 		<th>Módulo</th>
											  		<th>Nombre</th>							  
											 		<th>Tipo</th>
											  		<th>Panel</th>
											  		<th>Liga</th>
											  		<th>Icono</th>
											  		<th>Orden</th>
											  		<th>Estatus</th>
												</tr>
										 	</thead>
										  	<tbody>
												<?php foreach($datos as $key => $valor): ?>
												<tr onclick=<?php echo "obtenerDatos('/modulo_HVS/" . $valor['id'] . "');" ; ?> >
												  <td><?php echo $valor['id']; ?></td>
												  <td><?php echo $valor['nombre']; ?></td>
												  <td><?php echo $valor['tipo']; ?></td>
												  <td><?php echo $valor['panel']; ?></td>
												  <td><?php echo $valor['liga']; ?></td>
												  <td><?php echo $valor['icono']; ?></td>
												  <td><?php echo $valor['orden']; ?></td>
												  <td><?php echo $valor['estatus']; ?></td>
												</tr>
												<?php endforeach; ?>                                                                   
										  	</tbody>
										</table>
									</div>
								</div>
							</div>
							
							<div class="widget-foot">
								<button id="btnAgregar" class="btn btn-primary btn-xs">Agregar Módulo</button>
								<div class="clearfix"></div> 
							</div>
						</div>
						<!-- 
						<form id="formulario" METHOD='POST' action='/guardar/Modulo_HVS' role="form" onsubmit="return validarEnvio();">	
						-->
							<div id="modalModulo" class="modal fade" role="dialog"> 
								<div class="modal-dialog">

									<!-- Modal content-->
									<div class="modal-content">	
										<!-- Modal header-->
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal">&times;</button>
											<h4 class="modal-title">Datos del Módulo...</h4>
										</div>										
										<!-- Modal body-->
										<div class="modal-body">
											<!-- Inicio del Form-->

											<form id="formulario" METHOD='POST' action='/guardar/modulo_HVS' role="form">								
												<input type='HIDDEN' name='txtOperacion' value=''>

												<div class="form-group">
													<label class="col-xs-1 control-label" style="text-align:right">Módulo</label>
													<div class="col-xs-3">
														<input type="text" class="form-control" name="txtModulo" OnBlur="ValidaExisteModulo(this.value)" placeholder="Id. del Módulo."/>
													</div>

													<label class="col-xs-2 control-label" style="text-align:right">Nombre del Módulo</label>
													<div class="col-xs-5">
														<input type="text" class="form-control" name="txtNombre" id="txtNombre" placeholder="Nombre del Módulo"/>
													</div>
												</div>
												<div class="clearfix"></div>
												<br>

												<div class="form-group">
													<label class="col-xs-1 control-label" style="text-align:right">Tipo</label>
													<div class="col-xs-3">
														<select class="form-control" name="txtTipo">
															<option value="">Seleccione...</option>
															<option value="BOTON"  selected>BOTON</option>
															<option value="CAMPO">CAMPO</option>
															<option value="MENU">MENÚ</option>
															<option value="PANEL">PANEL</option>
														</select>
													</div>
													<label class="col-xs-2 control-label" style="text-align:right">Panel</label>
													<div class="col-xs-3">
														<select class="form-control" name="txtPanel">
															<option value="">Seleccione...</option>
															<option value="AUDITORIA" selected>AUDITORIA</option>
															<option value="CONFIGURACION">CONFIGURACION</option>
															<option value="OBSERVACIONES">OBSERVACIONES</option>
															<option value="PROGRAMA">PROGRAMA</option>
															<option value="REPORTEADOR">REPORTEADOR</option>


														</select>
													</div>											
												</div>					
												<div class="clearfix"></div>	
												<br>											

												<div class="form-group">
													<label class="col-xs-1 control-label" style="text-align:right">Orden</label>
													<div class="col-xs-2">
														<input type="text" class="form-control" name="txtOrden" id="txtOrden" placeholder="Orden" />
													</div>
													<label class="col-xs-3 control-label" style="text-align:right">Liga</label>
													<div class="col-xs-5">
														<input type="text" class="form-control" name="txtLiga" id="txtLiga" placeholder="Liga del Módulo"/>
													</div>
												</div>					
												<div class="clearfix"></div>	
												<br>											

												<div class="form-group">
													<label class="col-xs-1 control-label" style="text-align:right">Icono</label>
													<div class="col-xs-3">
														<input type="text" class="form-control" name="txtIcono" />
													</div>
													<label class="col-xs-2 control-label" style="text-align:right">Estatus</label>
													<div class="col-xs-2">
														<select class="form-control" name="txtEstatus">
															<option value="">Seleccione...</option>
															<option value="ACTIVO" selected>ACTIVO</option>
															<option value="SUSPENDIDO">SUSPENDIDO</option>
															<option value="INACTIVO">INACTIVO</option>
														</select>
													</div>											
													<div class="clearfix"></div>												
												</div>					
												<br>						

											</form>

										</div>
										<!--   COMENTARIOS  : AQUI VA EL FOOTER MODAL -->
										<div class="modal-footer">
											<button id="btnGuardar" class="btn btn-primary">Guardar datos</button>	
											<button id="btnCancelar" class="btn btn-default">Cancelar</button>	
										</div>
									</div>
								</div>
							</div>
							<!-- Aqui  va el form si se requiere volverlo a sacar -->
						</div>
					</div>
				</div>
			</div>
		</div>
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
            <!-- Copyright info -->
            <p class="copy">Copyright © 2016 | Auditoría Superior de la Ciudad de México</p>
      </div>
    </div>
  </div>
</footer> 	

<!-- Footer ends -->

<!-- Scroll to top -->
<span class="totop" style="display: none;"><a href="http://ashobiz.asia/mac52/macadmin/#"><i class="fa fa-chevron-up"></i></a></span> 

<!-- JS -->
<script src="./Dashboard - MacAdmin_files/jquery.js"></script> <!-- jQuery -->
<script src="./Dashboard - MacAdmin_files/bootstrap.min.js"></script> <!-- Bootstrap -->
<script src="./Dashboard - MacAdmin_files/jquery-ui.min.js"></script> <!-- jQuery UI -->
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


<!-- Charts & Graphs -->

<!-- 
<script src="./Dashboard - MacAdmin_files/charts.js"></script> 

<script src="./Dashboard - MacAdmin_files/moment.min.js"></script> 
<script src="./Dashboard - MacAdmin_files/fullcalendar.min.js"></script> 
<script src="./Dashboard - MacAdmin_files/jquery.rateit.min.js"></script> 
<script src="./Dashboard - MacAdmin_files/jquery.prettyPhoto.js"></script>
-->
</body></html>