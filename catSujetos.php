<!DOCTYPE html>
<html lang="en"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">  
  <meta charset="utf-8">
		<script type='text/javascript' src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js?ver=3.1.2"></script> 


	<script type="text/javascript" src="js/genericas.js"></script>

  	<style type="text/css">		
		@media screen and (min-width: 768px) {
			#mapa_content {width:100%; height:150px;}
			#canvasJG, #canvasJD, #canvasDIP{height:140px; width:100%;}			
			#modalFlotante .modal-dialog  {width:50%;}
			
		}
	</style>
  
  <script type="text/javascript"> 
	var mapa;
	var nZoom=10;
	
	
	function agregarDocumento(){
		$('#modalFlotante').removeClass("invisible");
		$('#modalFlotante').modal('toggle');
		$('#modalFlotante').modal('show');
	}
	
	function recuperaDocto(){
		$('#modalFlotante').removeClass("invisible");
		$('#modalFlotante').modal('toggle');
		$('#modalFlotante').modal('show');
		
		document.all.btnGuardarEnviar.style.display='none';
		document.all.btnGuardar.style.display='inline';
		document.all.btnTurnar.style.display='inline';		
	}
	

	
	
	var nUsr='<?php echo $_SESSION["idUsuario"];?>';
	var nCampana='<?php echo $_SESSION["idCampanaActal"];?>';
	  
	$(document).ready(function(){
		if(nUsr!="" && nCampana!=""){
			recuperarListaSelected('lstCampanasByUsr', nUsr, document.all.txtCampana,nCampana);
			cargarMenu( nCampana);			
		}else{
			if(nCampana=="")alert("Debe establecer una CUENTA PÚBLICA como PREDETERMINADA. Por favor consulte con su administrador del sistema.");
		}
		
		
	
		$( "#btnGuardar" ).click(function() {
			alert("Guardando");
			$('#modalFlotante').modal('hide');
		});
		$( "#btnTurnar" ).click(function() {
			document.all.capturaDocto.style.display='none';
			document.all.turnaDocto.style.display='inline';
			
			document.all.btnGuardarEnviar.style.display='inline';
			document.all.btnGuardar.style.display='none';
			document.all.btnTurnar.style.display='none';
		});
		
		$( "#btnGuardarEnviar" ).click(function() {
			alert("Guardando y enviando");
			document.all.capturaDocto.style.display='inline';
			document.all.turnaDocto.style.display='none';
			
			document.all.btnGuardarEnviar.style.display='none';
			document.all.btnGuardar.style.display='inline';
			document.all.btnTurnar.style.display='inline';

			$('#modalFlotante').modal('hide');
		});
		$( "#btnCancelar" ).click(function() {
			document.all.capturaDocto.style.display='inline';
			document.all.turnaDocto.style.display='none';

			document.all.btnGuardarEnviar.style.display='none';
			document.all.btnGuardar.style.display='inline';
			document.all.btnTurnar.style.display='inline';
			
			$('#modalFlotante').modal('hide');
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
					<div class="col-xs-12">
						<div class="col-xs-3"><a href="/"><img src="img/logo-top.png"></a></div>				
						<div class="col-xs-3">
							<ul class="nav navbar-nav "><li><a href="#"><i class="fa fa-th-list"></i> <?php echo $_SESSION["sCampanaActal"] ?></a></li></ul>
						</div>
						<div class="col-xs-3">
							<ul class="nav navbar-nav "><li><a href="./notificaciones"><i class="fa fa-envelope-o"></i> Tienes (3) Mensajes nuevos</a></li></ul>
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
    <div class="sidebar">
        <div class="sidebar-dropdown"><a href="/">Navigation</a></div>
		<!--- Sidebar navigation -->
		<ul id="nav">
		  <li class="open"><a href="/"><i class="fa fa-home"></i> Inicio</a></li>
		  
		  <li class="has_sub"  id="GESTION" style="display:none;">
			<a href=""><i class="fa fa-pencil-square-o"></i> Gestión<span class="pull-right"><i class="fa fa-chevron-right"></i></span></a>
			<ul id="GESTION-UL"></ul>
		  </li>
		  
		  <li class="has_sub"  id="PROGRAMA" style="display:none;">
			<a href=""><i class="fa fa-pencil-square-o"></i> Programas<span class="pull-right"><i class="fa fa-chevron-right"></i></span></a>
			<ul id="PROGRAMA-UL"></ul>
		  </li>

		  <li class="has_sub"  id="AUDITORIA" style="display:none;">
			<a href=""><i class="fa fa-pencil-square-o"></i> Auditorías<span class="pull-right"><i class="fa fa-chevron-right"></i></span></a>
			<ul id="AUDITORIA-UL"></ul>
		  </li>
		  
		  <li class="has_sub"  id="OBSERVACIONES" style="display:none;">
			<a href=""><i class="fa fa-pencil-square-o"></i> Observaciones<span class="pull-right"><i class="fa fa-chevron-right"></i></span></a>
			<ul id="OBSERVACIONES-UL"></ul>
		  </li>
		  
		  <li class="has_sub"  id="CONFIGURACION" style="display:none;">
			<a href=""><i class="fa fa-pencil-square-o"></i> Configuración<span class="pull-right"><i class="fa fa-chevron-right"></i></span></a>
			<ul id="CONFIGURACION-UL"></ul>
		  </li>
		  

		  <li class="has_sub"  id="REPORTEADOR" style="display:none;">
			<a href=""><i class="fa fa-file-text-o"></i> Informes<span class="pull-right"><i class="fa fa-chevron-right"></i></span></a>
			<ul id="REPORTEADOR-UL"></ul>
		  </li>	
		  
		  <li class="open"><a href="/cerrar"><i class="fa fa-sign-out"></i> Salir</a></li>		  
		  
		</ul>
	</div> <!-- Sidebar ends -->

  	  	<!-- Main bar -->
  	<div class="mainbar">
      
<div class="col-md-12">
				  <div class="widget">
					<div class="widget-head">
					  <div class="pull-left"><h3 class="pull-left"><i class="fa fa-home"></i> Catálogo de Sujetos de Auditoría</h3></div>
					  <div class="widget-icons pull-right">
						<button onclick="agregarDocumento();" type="button" class="btn btn-primary  btn-xs">Nuevo Sujetos...</button> 						
					  </div>  
					  <div class="clearfix"></div>
					</div>             
					<div class="widget-content">						
							<div class="table-responsive" style="height: 400px; overflow: auto; overflow-x:hidden;">
								<table class="table table-striped table-bordered table-hover">
								  <thead>
									<tr><th>ID</th><th>Descripción</th><th>Vigencia</th><th>Estatus</th></tr>
								  </thead>
								  <tbody >									  
									<tr onclick=<?php echo "agregarDocumento();"; ?> style="width: 100%; font-size: xx-small">
										<td>F-0001</td><td>Folio Docto 1</td><td>01/01/2016 al 31/12/2016</td><td>ACTIVO</td>
									</tr>
									<tr onclick=<?php echo "agregarDocumento();"; ?> style="width: 100%; font-size: xx-small">
										<td>F-0002</td><td>Folios R3</td><td>01/01/2016 al 31/12/2016</td><td>ACTIVO</td>
									</tr>
									<tr onclick=<?php echo "agregarDocumento();"; ?> style="width: 100%; font-size: xx-small">
										<td>F-0003</td><td>Folios X</td><td>01/01/2016 al 31/12/2016</td><td>ACTIVO</td>
									</tr>																		
								  </tbody>
								</table>
							</div>
					</div>
					<div class="widget-foot"></div>
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
							<form id="formulario" METHOD='POST' action='/guardar/intencion' role="form" onsubmit="return validarEnvio();">
							<input type='HIDDEN' name='txtValores' value=''>						
							
							<!-- Modal content-->
							<div class="modal-content">									
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h4 class="modal-title">Registrando...</h4>
								</div>									
								<div class="modal-body">
									<div class="container-fluid">
										<div class="row">
												<div id="capturaDocto">
												<br>
												<div class="form-group">
													<div class="col-xs-6">
														<select class="form-control" name="txtTipoDocto">
															<option value="">Tipo de Documento</option>
														</select>
													</div>
													<div class="col-xs-3"><input type="text" class="form-control" name="txtNumeroDocto" placeholder="No. de Documento" /></div>												
													<div class="col-xs-3"><input type="text" class="form-control" name="txtNumeroDocto" placeholder="Fecha del Documento" /></div>												
												</div>
												<br>
												<div class="form-group">													
													<div class="col-xs-12">
														<textarea class="form-control" rows="3" placeholder="Resumen"></textarea>
													</div>													
												</div>
												<br>
												<div class="form-group">													
													<div class="col-xs-6">
														<select class="form-control" name="txtPartido" >
															<option value="">Remitente</option>
														</select>
													</div>
													<div class="col-xs-6">
														<select class="form-control" name="txtPartido" >
															<option value="">Destinatario</option>
														</select>
													</div>
												</div>
											</div>
												
												<div id="turnaDocto" style="display: none;">
													<div class="form-group">													
														<label class="col-xs-3 control-label" style="text-align:right">Remitente</label>
														<div class="col-xs-9">
															<select class="form-control" name="txtPartido" >
																<option value="">Remitente</option>
															</select>
														</div>
													</div>
													<br>
													<div class="form-group">													
														<label class="col-xs-3 control-label" style="text-align:right">Destino</label>
														<div class="col-xs-9">
															<select class="form-control" name="txtPartido" >
																<option value="">Destinatario</option>
															</select>
														</div>
													</div>
													<br>
													<div class="form-group">
														<label class="col-xs-3 control-label" style="text-align:right">Asunto</label>
														<div class="col-xs-9">
														<input type="text" class="form-control" name="txtNombre" readonly="readonly" />
														</div>
													</div>
													<br>
												</div>
										</div>
									</div>
									<div class="clearfix"></div>
								</div>
								
								<div class="modal-footer">
									<button  type="button" class="btn btn-primary active" id="btnGuardar" 		style="display:inline;">Guardar</button>	
									<button  type="button" class="btn btn-warning active" id="btnTurnar" 		style="display:inline;">Turnar</button>	
									<button  type="button" class="btn btn-primary active" id="btnGuardarEnviar" style="display: none;">Guardar y Enviar</button>
									<button  type="button" class="btn btn-default active" id="btnCancelar" 		style="display:inline;">Cancelar</button>	
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

</body></html>