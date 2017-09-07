<html lang="en"><head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=Edge" />  
	<meta name="viewport" content="width=device-width, initial-scale=1">	
	<script type='text/javascript' src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js?ver=3.1.2"></script> 
	<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?&sensor=true"></script>
	<script type="text/javascript" src="js/genericas.js"></script>
	<script type="text/javascript" src="js/gene.js"></script>
	
	<style type="text/css">		
		@media screen and (min-width: 768px;) {
			#mapa_content {width:100%; height:450px;}
			#modalFlotante .modal-dialog  {width:50%;}
		}
	</style>
  
  <script type="text/javascript"> 
  
  	function cambiarPass(activo){
		document.all.txtContrasenaActual.value="";
		document.all.txtContrasenaNueva.value="";
		document.all.txtContrasenaConfirmar.value="";
		if(activo==true){
			document.all.idContrasenaActual.style.display="inline";
			document.all.idContrasenaNueva.style.display="inline";
			document.all.idContrasenaConfirmar.style.display="inline";
		}
		else{
			document.all.idContrasenaActual.style.display="none";
			document.all.idContrasenaNueva.style.display="none";
			document.all.idContrasenaConfirmar.style.display="none";
		}
		
	}
  


	
	function validarEnvio(){		
		
		if (document.all.txtNombre.value==''){
			alert("Debe capturar el NOMBRE DEL USUARIO.");
			document.all.txtNombre.focus();
			return false;
		}	

		if (document.all.txtPaterno.value==''){
			alert("Debe capturar el APELLIDO PATERNO.");
			document.all.txtPaterno.focus();
			return false;
		}
		if (document.all.txtMaterno.value==''){
			alert("Debe capturar el APELLIDO MATERNO.");
			document.all.txtMaterno.focus();
			return false;
		}
		
		if (document.all.txtTelefono.value ==''){
			alert("Debe capturar el TELÉFONO.");
			document.all.txtTelefono.focus();
			return false;
		}
		
		if (document.all.txtCorreo.value ==''){
			alert("Debe capturar el CORREO.");
			document.all.txtCorreo.focus();
			return false;
		}		
			
		if(document.all.chkCambiarPass.checked ==true) {	document.all.txtCambiarPass.value="SI";}else{document.all.txtCambiarPass.value="NO";}
		
		if(document.all.txtCampana.selectedIndex==0){
			alert("Debe establecer una CAMPAÑA como predeterminada.");
			document.all.txtCampana.focus();
			return false;			
		}

		return true;
	}	

	var nUsr='<?php echo $_SESSION["idUsuario"];?>';
	var nCampana='<?php echo $_SESSION["idCuentaActual"];?>';
	  
	$(document).ready(function(){
		if(nUsr!="" && nCampana!=""){
			recuperarListaSelected('lstCuentasByUsr', nUsr, document.all.txtCampana,nCampana);
			usuario('cuenta','cuentadiv',nUsr);
			cargarMenu( nCampana);			
		}else{
			if(nCampana=="")alert("Debe establecer una CAMPAÑA como PREDETERMINADA. Por favor consulte con su administrador del sistema.");
		}
	
		$( "#btnGuardar" ).click(function() {
			if (confirm("¿Se cerrara la sesiòn, al guardar los cambios 	\n \n")){
				if(validarEnvio()){
					document.all.formulario.submit();
				}		

			}
		});	
	});

	
    </script>
  
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
<body>
	<div class="navbar navbar-fixed-top bs-docs-nav" role="banner">  
		<div class="container">
			<!-- Navigation starts -->
			<nav class="collapse navbar-collapse bs-navbar-collapse" role="navigation">
				<ul class="nav navbar-nav pull-left">
					<a href="/"><img src="img/logo-top.png" width="100%"></a>
				</ul>			
				<ul class="nav navbar-nav"  style="text-align:center">
					<br>
					<i class="fa fa-bullhorn"></i><b> <?php echo $_SESSION["sCuentaActual"] ?></b>
				</ul>
				<ul class="nav navbar-nav pull-right">
					<li class="dropdown pull-right">            
						<a data-toggle="dropdown" class="dropdown-toggle" href="/">
							<i class="fa fa-user"></i> <b><?php echo $_SESSION["sUsuario"] ?></b> <b class="caret"></b> 							
						</a>
						<ul class="dropdown-menu">
						  <li><a href="./perfil"><i class="fa fa-user"></i> Perfil</a></li>
						  <li><a href="./cerrar"><i class="fa fa-sign-out"></i> Salir</a></li>
						</ul>
					</li>
				</ul>
			</nav>
		</div>
	</div>
	
		<!-- Header starts -->
	<header>
		<div class="container">
			<div class="row">
	
			</div>
		</div>
	</header><!-- Header ends -->

<!-- Main content starts -->

<div class="content">
  	<!-- Sidebar -->
    <?php require '/templates/menu.php'; ?>
	
  	  	<!-- Main bar -->
  	<div class="mainbar">      
	    <!-- Page heading -->
      <!-- Page heading -->
      <div class="page-head">
        <h2 class="pull-left"><i class="fa fa-table"></i> Perfil de Usuario</h2>

        <!-- Breadcrumb -->
        <div class="bread-crumb pull-right">
          <a href="index.html"><i class="fa fa-home"></i> Home</a> 
          <!-- Divider -->
          <span class="divider">/</span> 
          <a href="#" class="bread-current">Perfil</a>
        </div>

        <div class="clearfix"></div>

      </div>
	    <!-- Page heading ends -->	
		
		<div class="matter">
			<div class="container">

			  <!-- Table -->
				<div class="row">
					<div class="col-xs-1"></div>
					<div class="col-md-10">				
						<div class="widget">
							<div class="widget-head">
							  <div class="pull-left">Perfil del Usuario x</div>
							  <div class="widget-icons pull-right">
							  </div>  
							  <div class="clearfix"></div>
							</div>						
							<div class="widget-content">
							<form id="formulario" METHOD='POST' action='/guardar/perfil' role="form">								
								<input type='HIDDEN' name='txtCambiarPass' value='NO'>

								<br>
								<div class="form-group">
									<label class="col-xs-2 control-label" style="text-align:right">Identificador</label>
									<div class="col-xs-2">
										<input type="text" class="form-control" name="txtID" value="<?php echo $datos['idUsuario']; ?>"  readonly />
									</div>
								</div>
								<br>
								<div class="form-group">
									<label class="col-xs-2 control-label" style="text-align:right">Nombre(s)</label>
									<div class="col-xs-10">
										<input type="text" class="form-control" name="txtNombre" value="<?php echo $datos['nombre']; ?>"/>
									</div>
								</div>
								<br>
								<div class="form-group">
									<label class="col-xs-2 control-label" style="text-align:right">Apellido Paterno</label>
									<div class="col-xs-4">
										<input type="text" class="form-control" name="txtPaterno" value="<?php echo $datos['paterno']; ?>"/>
									</div>									
									<label class="col-xs-2 control-label" style="text-align:right">Apellido Materno</label>
									<div class="col-xs-4">
										<input type="text" class="form-control" name="txtMaterno" value="<?php echo $datos['materno']; ?>"/>
									</div>
								</div>								
								<br>
								<div class="form-group">
									<label class="col-xs-2 control-label" style="text-align:right">Teléfono(s)</label>
									<div class="col-xs-4">
										<input type="text" class="form-control" name="txtTelefono" value="<?php echo $datos['telefono']; ?>"/>
									</div>								
									<label class="col-xs-2 control-label" style="text-align:right">Correo </label>
									<div class="col-xs-4">
										<input type="text" class="form-control" name="txtCorreo" value="<?php echo $datos['usuario']; ?>"/>
									</div>
								</div>										
								<br>
								<div class="form-group">
									<label id="cuenta" class="col-xs-2" control-label" style="text-align:right">Cuenta Pública</label>
									<div class="col-xs-7" id="cuentadiv">
											<select  class="form-control" name="txtCampana">
												<option value="">Seleccione...</option>
											</select>
									</div>
									<label class="col-xs-3 control-label" style="text-align:right">
										<div  style="text-align:left" class="checkbox">
											<label><input name="chkCambiarPass" type="checkbox" onclick="javascript:cambiarPass(this.checked);">Cambiar Contraseña</label>
										</div>
									</label>																											
								</div>														

								<div class="form-group">								
									<div class="col-xs-2"></div>
									<div class="col-xs-4" id="idContrasenaActual" style="display:none;">
										<input type="password" class="form-control" name="txtContrasenaActual" placeholder="Contraseña Actual">
									</div>
									<div class="col-xs-3" id="idContrasenaNueva" style="display:none;">
										<input type="password" class="form-control" name="txtContrasenaNueva" placeholder="Contraseña Nueva">
									</div>
									<div class="col-xs-3" id="idContrasenaConfirmar" style="display:none;">
										<input type="password" class="form-control" name="txtContrasenaConfirmar" placeholder="Confirmar Contraseña">
										<br>
									</div>
								</div>

								
							</form>
								<div class="clearfix"></div> 
							</div>
							
							<div class="widget-foot">
								<button type="button" class="btn btn-primary btn-xs" id="btnGuardar">Guardar</button>
								<div class="clearfix"></div> 
							</div>
								
						</div>

					</div>
					<div class="col-xs-1"></div>
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