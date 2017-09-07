<!DOCTYPE html>
<html lang="en">
	<head>
		<!-- Title and other stuffs -->
		<title>Sistema Integral de Auditorias</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="">
		<meta name="keywords" content="">
		<meta name="author" content="">	
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"> 
		<meta http-equiv="X-UA-Compatible" content="IE=Edge" />  
		<script type="text/javascript" src="js/canvasjs.min.js"></script>
		<script type='text/javascript' src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js?ver=3.1.2"></script> 	
		<script src="https://maps.googleapis.com/maps/api/js?sensor=false&libraries=geometry,places&ext=.js"></script>
		<script src="js/canvasjs.min.js"></script>
		<script type="text/javascript" src="js/genericas.js"></script>

		<style type="text/css">		
			@media screen and (min-width: 768px) {
				#canvasMapa {width:100%; height:500px;}			
				
				#CanvasGrafica1, #CanvasGrafica2, #CanvasGrafica3{height:225px; width:100%;
				//box-shadow: 5px 5px 3px #c4c4c4; border: 1px solid #f4f4f4;
				}
				
				#tblCanvasGrafica1, #tblCanvasGrafica2, #tblCanvasGrafica3{box-shadow: 5px 5px 3px #c4c4c4; border: 1px solid #f4f4f4;}
				
								
				#CanvasGrafica4, #CanvasGrafica5, #CanvasGrafica6, #CanvasGrafica7, #CanvasGrafica8, #CanvasGrafica9, #CanvasGrafica10, #CanvasGrafica11, #CanvasGrafica12{height:150px; width:100%;}						
				#modalFlotante .modal-dialog  {width:50%;}
				
				.cabezaInfo{
					border: 0px;background-color:#D9D9F3;color: black;font-weight: Normal;
					text-align: center;font-family:'Arial';font-size:9px;padding:0px;margin:0px;cursor:pointer;-webkit-appearance: none;white-space:normal;
				}
				
			}
		</style>
		
		<script type="text/javascript"> 
			var mapa;
			var nZoom=10;
			var nUsr='<?php echo $_SESSION["idUsuario"];?>';
			var nCampana='<?php echo $_SESSION["idCuentaActual"];?>';
			var noti="";
			  
			$(document).ready(function(){

			//setInterval(getMensaje('txtNoti'),10);
			
			getMensaje('txtNoti', 1);

				if(nUsr!="" && nCampana!=""){
					cargarMenu( nCampana);			
				}else{
					if(nCampana=="")alert("Debe establecer una CUENTA PÚBLICA como PREDETERMINADA. Por favor consulte con su administrador del sistema.");
				}
			});

			



			function pintarAuditoria(sAuditoria, lat, lng, cadena){
				var pos = new google.maps.LatLng(lat, lng);							
				var marker = new MarkerWithLabel(
				{
					position: pos, 
					draggable: false, 
					raiseOnDrag: true,  
					map:  mapa,
					labelContent: " Auditoría " + sAuditoria,	
					labelAnchor: new google.maps.Point(22, 0),			
					labelClass: "labels", // the CSS class for the label
					labelStyle: {opacity: 0.99}
				});
				//markers.push(marker);
				
				var infoWindow = new google.maps.InfoWindow({});
				infoWindow.setContent('<p class="cabezaInfo">AUDITORÍA<BR>' + sAuditoria + '</p>' + cadena);
				google.maps.event.addListener(marker, 'click', function() {	infoWindow.open(mapa,marker); 	});		
				google.maps.event.addListener(mapa, 'click', function() {   infoWindow.close(); });
			}

			function inicializar() {
				var opciones = {
					center: new google.maps.LatLng(19.4339249,-99.1428964),
					zoom:nZoom, panControl:false,zoomControl:true,mapTypeControl:false,scaleControl:false,streetViewControl:false,overviewMapControl:false,rotateControl:false,    
					mapTypeId: google.maps.MapTypeId.ROADMAP,mapTypeControlOptions: {mapTypeIds: [google.maps.MapTypeId.ROADMAP, 'map_style']}
					,styles: [
						{"stylers": [{"hue": "#2c3e50"},{"saturation": 250}]},
						{"featureType": "road","elementType": "geometry","stylers": [{"lightness": 50},{"visibility": "simplified"}]},
						{"featureType": "road","elementType": "labels","stylers": [{"visibility": "off"}]}
					]			
				};		
				mapa = new google.maps.Map(document.getElementById('canvasMapa'), opciones);
			}	


			window.onload = function () {
			
				var grAvances1, grAvances2, grAvances3;				
				
				setGrafica(grAvances1, "dpsAuditoriasByArea", "pie", "Auditorias en la Dir. Gral.", "CanvasGrafica1" );			
				
				setGrafica(grAvances2, "dpsTipoPapeles", "pie", "Cédulas en la Dir. Gral.", "CanvasGrafica2" );			
				setGrafica(grAvances3, "dpsTipoAcopio", "pie", "Documentos en la Dir. Gral.", "CanvasGrafica3" );
			  };	
		</script>




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
		<!-- Data tables 
		<link rel="stylesheet" href="jquery.dataTables.css"> 
		-->
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
					<div class="col-xs-3"><h2><i class="fa fa-desktop"></i> Dashboard</h2></a></div>									
					<div class="col-xs-2">
						<ul class="nav navbar-nav "><li><a href="./notificaciones"><i class="fa fa-envelope-o"></i> Tiene <span><input type="text"  class="noti"  id="txtNoti"></input></span> Mensaje(s).</a></li></ul>
					</div>					
					<div class="col-xs-3">
						<ul class="nav navbar-nav  pull-right">
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
        <div class="sidebar-dropdown"><a href="/">Menú</a></div>
		<!--- Sidebar navigation -->
		<ul id="nav">
		  <li class="has_sub"><a href="./"><i class="fa fa-home"></i> Inicio</a></li>
		  
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
		  
		  <li class="has_sub"><a href="./cerrar"><i class="fa fa-sign-out"></i> Salir</a></li>		  
		  
		</ul>
	</div> <!-- Sidebar ends -->

  	  	<!-- Main bar -->
  	<div class="mainbar">
		<div class="col-md-12">
			<div class="widget">
				<div class="widget-head">
				  <div class="pull-left"><h3 class="pull-left"><i class="fa fa-desktop	"></i> Avances</h3></div>
				  <div class="widget-icons pull-right"><div class="form-group"><div class="col-xs-12"></div></div></div> 
				  <div class="clearfix"></div>
				</div>             
				<div class="widget-content">
					<div  class="col-md-12" id="divMapas"  style="display:none;">
						<div class="form-group">						
							<div class="col-md-4"><div id="CanvasGrafica4"></div></div>
							<div class="col-md-4"><div id="CanvasGrafica5"></div></div>
							<div class="col-md-4"><div id="CanvasGrafica6"></div></div>
						</div>
						<br>
						<div class="form-group">						
							<div class="col-md-4"><div id="CanvasGrafica7"></div></div>
							<div class="col-md-4"><div id="CanvasGrafica8"></div></div>
							<div class="col-md-4"><div id="CanvasGrafica9"></div></div>
						</div>
						<br>
						<div class="form-group">						
							<div class="col-md-4"><div id="CanvasGrafica10"></div></div>
							<div class="col-md-4"><div id="CanvasGrafica11"></div></div>
							<div class="col-md-4"><div id="CanvasGrafica12"></div></div>
						</div>						
	
						<div class="clearfix"></div>						
					</div>
					
					<div  class="col-xs-12" id="divAvances">
						<div class="col-xs-12">						
							<div class="col-md-4">
								<br>
								<div id="CanvasGrafica1"></div>
								<table class="table table-striped table-bordered table-hover table-condensed">
									<thead style="width: 100%; font-size: xx-small;">
										<tr><th style="text-align:center;">AUDITORIAS</th><th style="text-align:center;">CANTIDAD</th></tr>
									</thead>
									<tbody id="tblCanvasGrafica1" style="text-align:center; font-size: xx-small;">									
									</tbody>
								</table>								
							</div>	
							<div class="col-md-4">
								<br>
								<div id="CanvasGrafica2"></div>
								<table class="table table-striped table-bordered table-hover table-condensed">
									<thead style="width: 100%; font-size: xx-small;">
										<tr><th style="text-align:center;">CÉDULAS</th><th style="text-align:center;">CANTIDAD</th></tr>
									</thead>
									<tbody id="tblCanvasGrafica2" style="text-align:center; font-size: xx-small;">									
									</tbody>
								</table>
							</div>	

							<div class="col-md-4">
								<br>
								<div id="CanvasGrafica3"></div>
								<table class="table table-striped table-bordered table-hover table-condensed">
									<thead style="width: 100%; font-size: xx-small;">
										<tr><th style="text-align:center;">DOCUMENTOS</th><th style="text-align:center;">CANTIDAD</th></tr>
									</thead>
									<tbody id="tblCanvasGrafica3" style="text-align:center; font-size: xx-small;">									
									</tbody>
								</table>
							</div>	

							
						
						
						</div>
						<div class="col-xs-12">				
							<div class="widget">
								<div class="widget-head">
								  <div class="pull-left">Auditorias</div>
								  <div class="clearfix"></div>
								</div>             
								<div class="widget-content">
									<div class="table-responsive" style="height: 350px; overflow: auto; overflow-x:hidden;">
										<table class="table table-striped table-bordered table-hover table-condensed">
											<thead>
												<tr><th width="10%">Auditoría</th><th width="20%">Dirección General</th><th width="30%">Sujeto de Fiscalización</th><th width="30%">Objeto de Fiscalización</th><th width="10%">Tipo de Auditoría</th></tr>
											</thead>
												<tbody>																			
													<?php foreach($datos as $key => $valor): ?>
														<tr style="width: 100%; font-size: xx-small">
																			  										  
														  <td><?php echo $valor['claveAuditoria']; ?></td>
														  <td><?php echo $valor['area']; ?></td>
														  <td><?php echo $valor['sujeto']; ?></td>
														  <td><?php echo $valor['objeto']; ?></td>
														  <td><?php echo $valor['tipo']; ?></td>
														</tr>
													<?php endforeach; ?>	
											</tbody>
										</table>
									</div>
								</div>
								<!--<div class="widget-foot"></div>-->
							</div>
						</div>
					</div>
					<div class="col-xs-12" id="divMapa" style="display:none;">
						<div id="canvasMapa" ></div>
					</div>
					
					<div class="clearfix"></div>
				</div>
				<!--<div class="widget-foot"></div>-->
			</div>
				</div>
		<!-- Matter ends -->
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

</body></html>


