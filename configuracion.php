<!DOCTYPE html>
<!-- saved from url=(0035)http://ashobiz.asia/mac52/macadmin/ -->
<html lang="en"><head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">	
	<script type='text/javascript' src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js?ver=3.1.2"></script> 
	<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?&sensor=true"></script>
	
	<style type="text/css">		
		@media screen and (min-width: 768px) {
			#mapa_content {width:100%; height:450px;}
			#modalFlotante .modal-dialog  {width:50%;}
		}
	</style>
  
  <script type="text/javascript"> 
	function validarEnvio(){		
		var d = Document.all;
		if(d.txtNombre.value=""){
			alert("Debe capturar el campo NOMBRE");
			d.txtNombre.focus();
			return false;
		}
		
		if(d.txtSiglas.value=""){
			alert("Debe capturar el campo SIGLAS");
			d.txtSiglas.focus();
			return false;
		}
		
		if(d.txtRepresentante.value=""){
			alert("Debe capturar el campo REPRESENTANTE");
			d.txtRepresentante.focus();
			return false;
		}

		if(d.txtDomicilio.value=""){
			alert("Debe capturar el campo DOMICILIO");
			d.txtDomicilio.focus();
			return false;
		}
		document.all.formulario.submit();
		return true;
	}
	
	function agregarDatos(){
		document.all.operacion.value='INS';
		$('#modalFlotante').removeClass("invisible");
		$('#modalFlotante').modal('toggle');
		$('#modalFlotante').modal('show');
		limpiarCampos();
	}
	
	function limpiarCampos(){
		document.all.txtID.value="";
		document.all.txtNombre.value="";
		document.all.txtSiglas.value="";
		document.all.txtRepresentante.value="";
		document.all.txtDomicilio.value="";
	}
	
	function obtenerDatos(id){	
		$('#modalFlotante').removeClass("invisible");
		$('#modalFlotante').modal('toggle');
		$('#modalFlotante').modal('show');		
		$.ajax({
			type: 'GET', url: '/red/' + id ,
			success: function(response) {
				var obj = JSON.parse(response);
				document.all.txtID.value='' + obj.id;
				document.all.txtNombre.value='' + obj.nombre;
				document.all.txtSiglas.value='' + obj.siglas;
				document.all.txtRepresentante.value='' + obj.representante;
				document.all.txtDomicilio.value='' + obj.domicilio;				
			},
			error: function(xhr, textStatus, error){
				alert('statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error + ' SECCION: ' + seccoin);
			}			
		});			
	}	
    </script>
  
  <!-- Title and other stuffs -->
  <title>Sistema de Campañas Electorales</title>
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
<style type="text/css">.jqstooltip { position: absolute;left: 0px;top: 0px;visibility: hidden;background: rgb(0, 0, 0) transparent;background-color: rgba(0,0,0,0.6);filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=#99000000, endColorstr=#99000000);-ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr=#99000000, endColorstr=#99000000)";color: white;font: 10px arial, san serif;text-align: left;white-space: nowrap;padding: 5px;border: 1px solid white;z-index: 10000;}.jqsfield { color: white;font: 10px arial, san serif;text-align: left;}</style></head>

<body>
	<div class="navbar navbar-fixed-top bs-docs-nav" role="banner">  
		<div class="container">
			<!-- Navigation starts -->
			<nav class="collapse navbar-collapse bs-navbar-collapse" role="navigation">         
				<ul class="nav navbar-nav pull-left">
					<br>
					<i class="fa fa-bullhorn"></i> <b> <?php echo $_SESSION["sCampanaActal"] ?></b>
				</ul>
				<ul class="nav navbar-nav pull-right">	
					<li class="dropdown pull-right">            
						<a data-toggle="dropdown" class="dropdown-toggle" href="/">
							<i class="fa fa-user"></i> <b>C. <?php echo $_SESSION["sUsuario"] ?></b> <b class="caret"></b> 							
						</a>
						<ul class="dropdown-menu">
						  <li><a href="/perfil"><i class="fa fa-user"></i> Perfil</a></li>
						  <li><a href="/configuracion"><i class="fa fa-cogs"></i> Configuración</a></li>
						  <li><a href="/respaldos"><i class="fa fa-cloud-upload"></i> Respaldar</a></li>
						  <li><a href="/cerrar"><i class="fa fa-sign-out"></i> Salir</a></li>
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
				<!-- Logo section -->
				<div class="col-md-6">
				  <div class="logo">
					<h1>SI<b>Campañas</b></h1>
					<p class="meta">Sistema Integral de Campañas</p>
				  </div>
				</div>
				<div class="col-md-6"></div>			
			</div>
		</div>
	</header><!-- Header ends -->

<!-- Main content starts -->
<div class="content">
  	<!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-dropdown"><a href="/">Navigation</a></div>
		<!--- Sidebar navigation -->
		<ul id="nav">
		  <li class="open"><a href="/"><i class="fa fa-home"></i> Inicio</a>
		  </li>
		  <li class="has_sub">
			<a href="#"><i class="fa fa-sitemap"></i> Estructura<span class="pull-right"><i class="fa fa-chevron-right"></i></span></a>
			<ul>
				<li><a href="./campanas">Campañas</a></li>
				<li><a href="./operativos">Promotores</a></li>
				<li><a href="./redes">Redes de Apoyo</a></li>
				<li><a href="./recursos">Recursos y Apoyos</a></li>
			  <li><a href="./mapeos">Cartografía</a></li>							  
			</ul>
		  </li>  
		  <li class="has_sub">
			<a href=""><i class="fa fa-bullhorn"></i> Campaña<span class="pull-right"><i class="fa fa-chevron-right"></i></span></a>
			<ul>
				<li><a href="./intenciones">Posicionamiento</a></li>				
				<li><a href="./digitales">Digitales</a></li>
			</ul>
		  </li>
			<li><a href="./eventos"><i class="fa fa-sitemap"></i> Eventos</a></li>
			<li><a href="./reportes"><i class="fa fa-file-text-o"></i> Reportes</a></li>					  
		</ul>
	</div>
	<!-- Sidebar ends -->
	
  	  	<!-- Main bar -->
  	<div class="mainbar">      
	    <!-- Page heading -->
      <!-- Page heading -->
      <div class="page-head">
        <h2 class="pull-left"><i class="fa fa-table"></i> Configuración</h2>

        <!-- Breadcrumb -->
        <div class="bread-crumb pull-right">
          <a href="index.html"><i class="fa fa-home"></i> Home</a> 
          <!-- Divider -->
          <span class="divider">/</span> 
          <a href="#" class="bread-current">Configuración</a>
        </div>

        <div class="clearfix"></div>

      </div>
	    <!-- Page heading ends -->	
		
		<div class="matter">
			<div class="container">

			  <!-- Table -->
				<div class="row">
					<div class="col-md-8">				
						<div class="widget">
							<div class="widget-head">
							  <div class="pull-left">Configuración</div>
							  <div class="widget-icons pull-right">
							  </div>  
							  <div class="clearfix"></div>
							</div>
						
							<div class="widget-content">								
								<br><br><br>
								<div class="clearfix"></div> 
							</div>
							
							<div class="widget-foot">
								<button type="submit" class="btn btn-primary btn-xs" readonly>Guardar</button>
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

<!-- Footer starts -->
<footer>
  <div class="container">
    <div class="row">
      <div class="col-md-12">
            <!-- Copyright info -->
            <p class="copy">Copyright © 2015 | Sistema de Campañas Electorales</p>
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