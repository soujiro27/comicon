<!DOCTYPE html>
<html lang="es"><head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=Edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1">	
	<script type='text/javascript' src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js?ver=3.1.2"></script> 
	<script type="text/javascript" src="js/genericas.js"></script>
	<link rel="stylesheet" type="text/css" href="css/estilo.css">
	
  
  <script type="text/javascript"> 
  
	
	function limpiarCampos(){
		document.all.txtRol.selectedIndex=0;
		document.all.txtProceso.selectedIndex=0;		
		//Limpia campos de lista seleccionada
		document.getElementById('idetapa').options.length= 1;
		document.all.txtBoton.selectedIndex=0;
		document.all.rol.value='';
		document.all.proceso.value='';
		document.all.etapa.value='';
	}

	function validarCaptura(){
		if (document.all.txtRol.selectedIndex==0){alert("Debe seleccionar un ROL."); document.all.txtRol.focus();return false;}
		if (document.all.txtProceso.selectedIndex==0){alert("Debe seleccionar un Proceso.");document.all.txtProceso.focus();return false;}
		if (document.all.txtEtapa.selectedIndex==0){alert("Debe seleccionar una Etapa.");document.all.txtEtapa.focus();return false;}
		if (document.all.txtBoton.value==''){alert("Debe seleccionar un VALOR.");document.all.txtBoton.focus();return false;}
		return true;
	}

	/*function validarregis(){
		var rol = $("#idrol").val();
		var proceso = $("#idproceso").val();
		var etapa = $("#idetapa").val();
		var boton = $("#idboton").val();

		$.ajax({
		   type: 'GET',
		   url: 'valirolesetapas/' + $("#idrol").val() + '/' + $("#idproceso").val() + '/' + $("#idetapa").val() + '/' + $("#idboton").val(),
		   success: function(response){
			
			var obj = JSON.parse(response);   
			
			if(obj.validacion == 'true'){
				return true;
			}else{
				return false;
			}
			   
		   },
		   error: function(xhr, textStatus, error)
		   {
			   alert(textStatus);
		   }
	   });


	}*/


	
	function obtenroletapa(sUrl){
		$.ajax({
			type: 'GET', url: sUrl ,
			success: function(response) {			
				//alert("response: "+response);
				var obj = JSON.parse(response);
				document.all.txtOperacion.value='UPD';
				seleccionarElemento(document.all.txtRol, obj.rol);
				seleccionarElemento(document.all.txtProceso, obj.proceso);
				//recuperarListaLigada('recupeetapa',obj.proceso,document.all.txtEtapa);			
				recuperarListaSelected('recupeetapa', obj.proceso, document.all.txtEtapa, obj.etapa);
				document.all.txtBoton.value ='' + obj.boton;
				document.all.txtEstatus.value ='' + obj.estatus;
				document.getElementById('estatus').style.display='inline';
				document.all.rol.value= obj.rol;
				document.all.proceso.value= obj.proceso;
				document.all.etapa.value= obj.etapa;
					

				$('#modaletaparol').removeClass("invisible");
				$('#modaletaparol').modal('toggle');
				$('#modaletaparol').modal('show');
			},
			error: function(xhr, textStatus, error){
				alert('statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
			}			
		});			
	}		



	var nUsr='<?php echo $_SESSION["idUsuario"];?>';		
	var nCampana='<?php echo $_SESSION["idCuentaActual"];?>';	
	
	$(document).ready(function(){
		//$.ajaxSetup({cache: false});
		recuperarLista('recuperol', document.all.txtRol);
		recuperarLista('recupeProce', document.all.txtProceso);



		setInterval(getMensaje('txtNoti'),60000);


		if(nUsr!="" && nCampana!=""){
			cargarMenu( nCampana);			
		}else{
			if(nCampana=="")alert("Debe establecer una CAMPAÑA como PREDETERMINADA. Por favor consulte con su administrador del sistema.");
		}					
		
		
		
		$("#btnAgregarRol" ).click(function() {
			limpiarCampos();			
			document.getElementById('estatus').style.display='none';

			document.all.txtOperacion.value='INS';
			
			$('#modaletaparol').removeClass("invisible");
			$('#modaletaparol').modal('toggle');
			$('#modaletaparol').modal('show');
			
		});
		
		$("#btnGuardar" ).click(function() {
			var oper = document.all.txtOperacion.value;
			if(validarCaptura() && oper=='INS'){
				$.ajax({
				   type: 'GET',
				   url: 'valirolesetapas/' + $("#idrol").val() + '/' + $("#idproceso").val() + '/' + $("#idetapa").val() + '/' + $("#idboton").val(),
				   success: function(response){
					var obj = JSON.parse(response);   
					if(obj.validacion == 'SI'){
						alert("Error al insertar datos en la tabla, el REGISTRO esta duplicado ");
					}else{
						$.post("guardarolesetapas", $("#formulario").serialize(), function(resultados){resultados = $.parseJSON(resultados);if(resultados.estado == true){alert('Datos insertados correctamente.');$('#modaletaparol').modal('hide');window.location.reload();}else{alert("Error al insertar datos en la tabla.");}});
					}
				   },error: function(xhr, textStatus, error){alert(textStatus);}
			   });
			}else{
				$.ajax({
				   type: 'PUT',
				   url: 'updrolesetapas',
				   cache: false,
				   data: $("#formulario").serialize(),
				   dataType: "json",
				   success: function(resultados, textStatus, jqXHR)
				   {

					  alert(resultados.mensaje);
					  if(resultados.estado==true){
					  	$('#modaletaparol').modal('hide');window.location.reload();
					  }
				   },
				   error: function(jqXHR, textStatus, errorThrown)
				   {
					   alert(textStatus);
				   }
			   });
			}
		});	

		$("#btnCancelar").click(function() {
			document.all.txtOperacion.value='';			
			$('#modaletaparol').modal('hide');	
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
					<div class="col-xs-3"><h2>Catálogo de Roles</h2></a></div>									
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
  	<?php require '/templates/menu.php'; ?>
  		
  	  	<!-- Main bar -->
  	<div class="mainbar" id="masroleta">      
		<!-- Page heading -->
    	<!-- Page heading -->
    	<div class="page-head">
        	<h2 class="pull-left"><i class="fa fa-table"></i> Catálogo de Roles</h2>

        	<!-- Breadcrumb -->
        	<div class="bread-crumb pull-right">
         		 <a href="/dashboard"><i class="fa fa-home"></i> Home</a> 
          		<!-- Divider -->
          		<span class="divider">/</span> 
          		<a href="#" class="bread-current">Roles</a>
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
								<div class="pull-left">Lista de Roles Etapas</div>
								<div class="widget-icons pull-right"><button id="btnAgregarRol" class="btn btn-primary btn-xs">Agregar rol-etapa</button></div>  
							  <div class="clearfix"></div>
							</div>
						
							<div class="widget-content">	
								<div class="table-responsive" style="height: 400px; overflow: auto; overflow-x:hidden;">														
									<div class="">
										<table class="table table-striped table-bordered table-hover">
											<thead>
												<tr>
											 		<th>Rol</th>
											  		<th>proceso</th>							  
											  		<th>etapa</th>
											  		<th>Autoriza Etapa</th>
											  		<th>Estatus</th>
												</tr>
										 	</thead>
										  	<tbody>
												<?php foreach($datos as $key => $valor): ?>
												<tr onclick=<?php echo "obtenroletapa('/roletapa/" . $valor['id'] . "/" . $valor['idProceso'] . "/" .  $valor['idEtapa'] . "/" . $valor['boton'] . "');" ; ?> >
												  <td><?php echo $valor['rol']; ?></td>
												  <td><?php echo $valor['proceso']; ?></td>
												  <td><?php echo $valor['etapa']; ?></td>
												  <td><?php echo $valor['boton']; ?></td>
												  <td><?php echo $valor['estatus']; ?></td>

												</tr>
												<?php endforeach; ?>                                                                   
										  	</tbody>
										</table>
									</div>
								</div>
							</div>
							
							<div class="widget-foot">
								
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
<!-- Content ends -->

<!--modal-->
	<div id="modaletaparol" class="modal fade" role="dialog"> 
		<div class="modal-dialog">

			<!-- Modal content-->
			<div class="modal-content">	
				<!-- Modal header-->
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Datos ....</h4>
				</div>										
				<!-- Modal body-->
				<div class="modal-body">
					<!-- Inicio del Form-->
						<input type='HIDDEN' name='txtOperacion' value=''>

					<form id="formulario" method="POST" role="form">								
						<input type="HIDDEN" name="txtUsuario" value='<?php echo $_SESSION["idUsuario"];?>'>
						<input type="HIDDEN" name="rol" value=''>
						<input type="HIDDEN" name="proceso" value=''>
						<input type="HIDDEN" name="etapa" value=''>

						
						<div class="form-group">
							<label class="col-xs-2 control-label" id="parolrol">Rol</label>
							<div class="col-xs-9">
								<select class="form-control" name="txtRol" id="idrol">
									<option value="">Seleccione...</option>
								</select>	
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-2 control-label" id="parolpro">Proceso</label>
							<div class="col-xs-9" id="parolprodiv">
								<select class="form-control" name="txtProceso" id="idproceso" onchange="recuperarListaLigada('recupeetapa',this.value,document.all.txtEtapa);">
									<option value="">Seleccione...</option>
								</select>	
							</div>
							
						</div>
									
						<div class="form-group">

							<label class="col-xs-2 control-label" id="paroleta">Etapa</label>
							<div class="col-xs-9" id="paroletadiv">
								<select class="form-control" name="txtEtapa" id="idetapa">
									<option value="">Seleccione...</option>
								</select>	
							</div>
								<label class="col-xs-3 control-label" id="parolbot">Autoriza Etapa</label>
								<div class="col-xs-3" id="paroldos">
									<select class="form-control" name="txtBoton" id="idboton">
										<option value="">Seleccione...</option>
										<option value="SI">SI</option>
										<option value="NO">NO</option>
									</select>	
								</div>
						</div>

						<div class="form-group" id="estatus">
							<label class="col-xs-3 control-label" id="parolest">Estatus</label>
								<div class="col-xs-3" id="paroldesta">
									<select class="form-control" name="txtEstatus" id="idestatus">
										<option value="ACTIVO">ACTIVO</option>
										<option value="INACTIVO">INACTIVO</option>
									</select>
								</div>
						</div>
						
						<div class="clearfix"></div>
			
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


<!-- Footer starts -->
<footer>
	<?php require '/templates/footer.php'; ?>
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