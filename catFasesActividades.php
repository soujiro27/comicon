<!DOCTYPE html>
<html lang="en"><head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">	
	<meta http-equiv="X-UA-Compatible" content="IE=Edge" />  
	<script type='text/javascript' src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js?ver=3.1.2"></script> 
	<script type="text/javascript" src="js/genericas.js"></script>
	
	<style type="text/css">		
		@media screen and (min-width: 768px) {
			#mapa_content {width:100%; height:450px;}
			#modalFase .modal-dialog  {width:50%;}
			.delimitado {  
				height: 350px !important;
				overflow: scroll;
			}​			
		}
	</style>
  
  <script type="text/javascript"> 
  
	
  
	function validarEnvio(){		
		var d = document.all;

		if(d.txtFase.value==0){
			alert("Debe de seleccionar la FASE");
			d.txtFase.focus();
			return false;
		}
		
		if(d.txtNomActividad.value==""){
			alert("Debe capturar el campo NOMBRE ACTIVIDAD");
			d.txtNomActividad.focus();
			return false;
		}
		
		if(d.txtEstatus.value==""){
			alert("Debe seleccionar el ESTATUS");
			d.txtEstatus.focus();
			return false;
		}

		return true;
	}



	 function validar(){
	 
	 	//confirmacion = confirm("¿Esta seguro que deseas cambiar de Dirección?:\n \n");
    
     	if (confirm("¿ESta seguro que desea desactivar la actividad?:\n \n")){
     		
	 	}

	}







	
	function limpiarCampos(){
		document.all.txtIDActividad.value="";
		document.all.txtFase.selectedIndex= 0 ;
		document.all.txtNomActividad.value="";
		document.all.txtEstatus.selectedIndex= 1;
	}
	
	function validarCaptura(){
		if (document.all.txtTipo.selectedIndex==0){alert("Debe seleccionar un TIPO DE USUARIO.");return false;}
		if (document.all.txtNombre.value==""){alert("Debe capturar el NOMBRE DEL USUARIO.");return false;}

		return true;
	}
	
	function obtenerDatos(id){
		$.ajax({
			type: 'GET', url: '/RecuperaFase/' + id ,
			success: function(response) {			
				var obj = JSON.parse(response);
				//alert("Response: " + response);
				document.all.btnGuardarEdicion.style.display='none';
				//document.all.btnGuardar.style.display='none';
				limpiarCampos();
				document.all.txtOperacion.value= "UPD";
				$('#txtFase').prop('disabled', true);
				document.all.txtIDActividad.value='' + obj.id;
				document.all.txtFase.value='' + obj.fase;
				document.all.txtNomActividad.value='' + obj.actividad;
				document.all.txtEstatus.value='' + obj.estatus;


				$('#modalFase').removeClass("invisible");
				$('#modalFase').modal('toggle');
				$('#modalFase').modal('show');
								

			},
			error: function(xhr, textStatus, error){
				alert('statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
			}			
		});			
	}		
	

	
		var nUsr='<?php echo $_SESSION["idUsuario"];?>';		
		var nCampana='<?php echo $_SESSION["idCuentaActual"];?>';	
	
	$(document).ready(function(){
		getMensaje('txtNoti', 1);
		if(nUsr!="" && nCampana!=""){
			cargarMenu( nCampana);			
		}else{
			if(nCampana=="")alert("Debe establecer una CAMPAÑA como PREDETERMINADA. Por favor consulte con su administrador del sistema.");
		}			
		
		
		recuperarLista('lstFasesActividad', document.all.txtFase);
		
		

		
		$("#btnAgregarUsuario" ).click(function() {
			document.all.txtOperacion.value='INS';
			document.all.btnGuardarEdicion.style.display='none';
			document.all.btnGuardar.style.display='inline';
			$('#txtFase').prop('disabled', false);
			limpiarCampos();		
			$('#modalFase').removeClass("invisible");
			$('#modalFase').modal('toggle');
			$('#modalFase').modal('show');
			
		});

		// $("#btnGuardarEdicion" ).click(function() {
		// 	if(validarEnvio())
		// 	{
		// 	ActualizarEdicion();
		// 	}
		// });	


		$("#btnGuardar" ).click(function() {
			if(validarEnvio())
			{
			document.all.formulario.submit();
			}
		});	

		$("#btnCancelar" ).click(function() {
			document.all.txtOperacion.value='';			
			$('#modalFase').removeClass('invisible');
			$('#modalFase').modal('toggle');
			$('#modalFase').modal('hide');	
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
  <!-- Data tables -->
  <link rel="stylesheet" href="jquery.dataTables.css"> 
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
					<div class="col-xs-3"><h2>Catálogo de Usuarios</h2></a></div>									
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
        <h2 class="pull-left"><i class="fa fa-table"></i> Catálogo de Actividades por Fase</h2>

        <!-- Breadcrumb -->
        <div class="bread-crumb pull-right">
          <a href="index.html"><i class="fa fa-home"></i> Home</a> 
          <!-- Divider -->
          <span class="divider">/</span> 
          <a href="#" class="bread-current">Usuarios</a>
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
							  <div class="pull-left">Lista de Actividades por Fase</div>
							  <div class="widget-icons pull-right">
							  </div>  
							  <div class="clearfix"></div>
							</div>
						
							<div class="widget-content" >	
								<div class="table-responsive" style="height: 500px; overflow: auto; overflow-x:hidden;">														
									<div class="">
										<table class="table table-striped table-bordered table-hover">
										  <thead>
											<tr>
											  <th>id</th>
											  <th>Fase</th>							  
											  <th>Actividad</th>							  
											  <th>Estatus</th>							  
											</tr>
										  </thead>
										  <tbody>
											<?php foreach($datos as $key => $valor): ?>
											<tr onclick=<?php echo "obtenerDatos(" . $valor['id'] . ");"; ?> >
											  <td><?php echo $valor['id']; ?></td>
											  <td><?php echo $valor['fase']; ?></td>
											  <td><?php echo $valor['actividad']; ?></td>
											  <td><?php echo $valor['estatus']; ?></td>
											</tr>
											<?php endforeach; ?>						                                                                    
										  </tbody>
										</table>
									</div>
								</div>
							</div>
							
							<div class="widget-foot">
								<button id="btnAgregarUsuario" class="btn btn-primary btn-xs">Agregar Actividad</button>
								<div class="clearfix"></div> 
							</div>
								
						</div>
					
			
						</div>
					</div>
				</div>
			</div>
		</div>
    </div>

   <!-- Mainbar ends -->
   <div class="clearfix"></div>
   								  						   
</div>








<div id="modalFase" class="modal fade" role="dialog">
		<div class="modal-dialog">							
				<!-- Modal content-->
			<div class="modal-content">									
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h3 class="modal-title"><i class="fa fa-pencil-square-o"></i> Registrar Actividad...</h3>
				</div>									
				<div class="modal-body">
							<form id="formulario" METHOD='POST' action='/guardar/faseactividad' role="form">
								<input type='HIDDEN' name='txtOperacion' value=''>						
								<br>
									<div class="form-group">
										<label class="col-xs-1 control-label">ID</label>
										<div class="col-xs-2">
											<input type="text" class="form-control" name="txtIDActividad"  id="idActividad" readonly="" />
										</div>

										<label class="col-xs-2 control-label">Tipo de Fase</label>
										<div class="col-xs-6">
											<select class="form-control" id="txtFase" name="txtFase" >
											<option value="" Selected>Selecione...</option>
											</select>
										</div>
									</div>
								<br>
								<br>
								<div class="form-group">
									<label class="col-xs-1 control-label">Nombre Actividad</label>
									<div class="col-xs-10">
										<input type="text" class="form-control" name="txtNomActividad"/>
									</div>
								</div>
								<br>
								<br>
								<div class="form-group">
									<label class="col-xs-1 control-label">Estatus</label>
									<div class="col-xs-2">
										<select class="form-control" name="txtEstatus" id="txtEstatus">
											<option value="">Seleccione...</option>
											<option value="ACTIVO" selected>ACTIVO</option>
											<option value="INACTIVO" onclick="validar(idActividad.value);">INACTIVO</option>
										</select>
									</div>
								</div>


								<div class="clearfix"></div>								
							</form>
					<div class="clearfix"></div>
				</div>
				
				<div class="modal-footer">
					<button id="btnGuardar" class="btn btn-primary active">Guardar</button>	
					<button id="btnGuardarEdicion" class="btn btn-primary active">Guardar</button>	
					<button id="btnCancelar" class="btn btn-default active">Cancelar</button>	

				</div>
			</div>		
		</div>
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