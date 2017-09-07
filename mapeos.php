<!DOCTYPE html>
<html lang="en"><head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">	
	<script type='text/javascript' src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js?ver=3.1.2"></script> 
	
	<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?&sensor=true"></script>
	
	<script type="text/javascript" src="mapas.js"></script>
	
	
	<style type="text/css">		
		@media screen and (min-width: 768px) {
			#mapa_content {width:100%; height:500px;}
			#mapa_ppal {width:100%; height:500px;}
			#modalFlotante .modal-dialog  {width:80%;}
			
			.delimitado { height: 400px !important;overflow: scroll;  overflow-x:hidden; }​
			.delimitadoSeccion { height: 300px !important;overflow: scroll;}​
			.listaPuntos { height: 250px;overflow: scroll;}​				
		}
	</style>
  
  <script type="text/javascript"> 
  var nZoom=11;
	var nCapturarPosicion =false;

	window.onload = function(){
		var opciones = {
			center: new google.maps.LatLng(19.4339249,-99.1428964),
			zoom:nZoom,
			panControl:false,
			zoomControl:true,
			mapTypeControl:false,
			scaleControl:false,
			streetViewControl:false,
			overviewMapControl:false,
			rotateControl:false,    
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			styles: [
				{"stylers": [{"hue": "#2c3e50"},{"saturation": 250}]},
				{"featureType": "road","elementType": "geometry","stylers": [{"lightness": 50},{"visibility": "simplified"}]},
				{"featureType": "road","elementType": "labels","stylers": [{"visibility": "off"}]}
			]
			
		};

		
		
		mapa = new google.maps.Map(document.getElementById('mapa_ppal'),opciones);	
		
		// Agrega listener para mostrar latitud + longitud
			google.maps.event.addListener(mapa, 'click', 
				function(event) {
					var posActual = event.latLng;
					
					if (nCapturarPosicion==true){
						//infoWindow = new google.maps.InfoWindow();
						var sTituloInfo;
						
						
						
						if (sPanelActivo=="edicionDelegacion"){
							//sTituloInfo="<b>" + document.all.txtNombreDel.value + "</b>";
							sTituloInfo="<a href='javascript:pintarDistritos(" + document.all.txtIdDelegacion.value + ");'><b>" + document.all.txtNombreDel.value + "</b></a>";
							document.all.txtLatitudDel.value='' + posActual.lat();
							document.all.txtLongitudDel.value='' + posActual.lng();						
						}
						
						if (sPanelActivo=="edicionDistrito"){
							//sTituloInfo="<b>" + document.all.txtNombreDis.value + "</b>";
							sTituloInfo="<a href='javascript:pintarSecciones(" + document.all.txtIdDistrito.value + ");'><b>" + document.all.txtNombreDis.value + "</b></a>";
							document.all.txtLatitudDis.value='' + posActual.lat();
							document.all.txtLongitudDis.value='' + posActual.lng();						
						}
						
						if (sPanelActivo=="edicionSeccion"){
							//sTituloInfo="<b> Sección Electoral " + document.all.txtIdSeccion.value + "</b>";
							sTituloInfo="<a href='javascript:recuperaDatosSeccion(" + document.all.txtIdSeccion.value + ");'><b>Sección<br>" + document.all.txtIdSeccion.value + "</b></a>";
							document.all.txtLatitudSec.value='' + posActual.lat();
							document.all.txtLongitudSec.value='' + posActual.lng();						
						}
							
							
						var infoWindow = new google.maps.InfoWindow({});
						infoWindow.setContent(sTituloInfo);
						infoWindow.setPosition(posActual);
						infoWindow.open(mapa);	
						lstInfos.push(infoWindow);					
						mapa.setCenter(posActual);						
						
						bExisteInfo=true;						
						nCapturarPosicion =false;
					}
					
					if(nMarkando==true){
						//Agregar PIN
						nPinActual++;
						crearPin(posActual);
						
						puntos.push(new Array(seccionActual,posActual.lat(), posActual.lng() ));						
						crearRenglon(nPinActual, posActual.lat(), posActual.lng());
						//alert("Lat: " + posActual.lat() + " Lng: " + posActual.lng());
						
						document.all.btnLimpiar.style.display='inline';
						document.all.tblPuntos.style.display='block';
						
						triangleCoords.push(new google.maps.LatLng(posActual.lat(), posActual.lng()));
						
						//Dibuja el polígono
						if (triangleCoords.length>2) document.all.btnPoligono.style.display='inline';
						
					}
				}
			);					
			pintarDelegaciones(nEntidadActual);
	
	};
	
	var nEntidadActual=9;		
	$(document).ready(function(){
	
			$("#btnGuardar" ).click(function() {			
				if (validarEnvioSeccion()==true){				
					$.ajax({
						type: 'GET', url: '/guardar/seccion/' + document.all.txtValores.value ,
						success: function(response) {
							alert(response);						
						},
						error: function(xhr, textStatus, error){
							alert('statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
						}			
					});
				}
				activarPanel('');
			});
			
			$("#btnGuardarDistrito" ).click(function() {			
				if (validarEnvioDistrito()==true){				
					$.ajax({
						type: 'GET', url: '/guardar/distrito/' + document.all.txtValores.value ,
						success: function(response) {
							alert(response);						
						},
						error: function(xhr, textStatus, error){
							alert('statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
						}			
					});
				}
				activarPanel('');
			});

			$("#btnGuardarDelegacion" ).click(function() {			
				if (validarEnvioDelegacion()==true){				
					$.ajax({
						type: 'GET', url: '/guardar/delegacion/' + document.all.txtValores.value ,
						success: function(response) {
							alert(response);						
						},
						error: function(xhr, textStatus, error){
							alert('statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
						}			
					});
				}
				eliminarPuntos();
				activarPanel('');
				pintarDistritos(nUltimaDelegacion);
			});			
			
		
		$("#txtLatitudDel" ).click(function() {
			nCapturarPosicion=true;
			if(confirm("Deseas asignar una posicion?")==true){
				//Eliminar los infos
				for(var i=0; i<lstInfos.length; i++) lstInfos[i].close();
				lstInfos = [];
				document.all.txtLatitudDel.value="";
				document.all.txtLongitudDel.value="";
			}
		});
		
		$("#txtLatitudDis" ).click(function() {
			nCapturarPosicion=true;
			if(confirm("Deseas asignar una posicion?")==true){
				//Eliminar los infos
				for(var i=0; i<lstInfos.length; i++) lstInfos[i].close();
				lstInfos = [];
				document.all.txtLatitudDis.value="";
				document.all.txtLongitudDis.value="";
			}
		});

		$("#txtLatitudSec" ).click(function() {
			nCapturarPosicion=true;
			if(confirm("Deseas asignar una posicion?")==true){
				//Eliminar los infos
				for(var i=0; i<lstInfos.length; i++) lstInfos[i].close();
				lstInfos = [];
				document.all.txtLatitudSec.value="";
				document.all.txtLongitudSec.value="";
			}
		});		
		
		
		$("#btnVerDistritos" ).click(function() {pintarDistritos(document.all.txtIdDelegacion.value);});		
		$("#btnVerSecciones" ).click(function() {pintarSecciones(document.all.txtIdDistrito.value);	});		
		
		$("#btnRegresar" ).click(function() {
			alert("Panel Activo: " + sPanelActivo);
			if (sPanelActivo=="edicionDistrito"){
				document.all.btnRegresar.style.display="none";
				pintarDelegaciones(nEntidadActual);										
			}
			if (sPanelActivo=="edicionSeccion"){
				pintarDistritos(nUltimaDelegacion);
			}
		});
		
		$("#btnMarcar" ).click(function() {
		
			if(document.all.txtLatitudSec.value=="" && document.all.txtLongitudSec.value==""){
				alert("Antes de trazar la sección, debe de marcar el centro de la sección (Asignar coordenadas).");
				return;
			}
			eliminarPuntos();
			nMarkando=true;
			document.all.btnMarcar.style.display='none';
			document.all.btnPoligono.style.display='none';
			document.all.btnLimpiar.style.display='none';
			document.all.btnGuardar.style.display='none';
		});		
		
		$("#btnPoligono" ).click(function() {
			nMarkando=false;
			crearPoligono();
			document.all.btnMarcar.style.display='none';
			document.all.btnPoligono.style.display='none';
			document.all.btnLimpiar.style.display='inline';
			document.all.btnGuardar.style.display='inline';
		});	

		$("#btnLimpiar" ).click(function() {			
			nMarkando=false;
			eliminarPuntos();
			document.all.tblPuntos.style.display='none';			
			document.all.btnMarcar.style.display='inline';
			document.all.btnPoligono.style.display='none';
			document.all.btnLimpiar.style.display='none';
			document.all.btnGuardar.style.display='inline';
		});	
		
		$("#btnCancelar" ).click(function() {
			nMarkando=false;
			document.all.btnMarcar.style.display='inline';
			document.all.btnPoligono.style.display='none';
			document.all.btnLimpiar.style.display='none';
			document.all.btnGuardar.style.display='inline';
			eliminarPuntos();
			activarPanel('');
			pintarDistritos(nUltimaDelegacion);
		});	
				
		
		
	});
	
	
    </script>
  
  <!-- Title and other stuffs -->
  <title>Sistema para Política Inteligente</title>
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
				<div class="col-md-12 pull-left">
					<img src="img/somosmas-logo-peque.jpg">
				</div>		
			</div>
		</div>
	</header><!-- Header ends -->

<!-- Main content starts -->
<div class="container-fluid">

			  <!-- Table -->
				<div class="row">
					<div class="col-md-3" style="border:0px solid gray;position:absolute;background:transparent;padding:0px; margin:15px;opacity:1;z-index:10000">
						<div class="widget" id="divListados" style="display:block;">
							<div class="widget-head">
							  <div class="pull-left">Listado(s)</div>
							  <div class="widget-icons pull-right">								
								<button type="button" class="btn btn-default btn-xs" id="btnRegresar"><span class="fa fa-arrow-circle-left"></span></button>
							  </div>
							  <div class="clearfix"></div>
							</div>						
							<div class="widget-content">						
								<div class="delimitado">
										<table class="table table-striped table-bordered table-hover"   style='display:block; width:100%;'>
										  <thead>
											<tr>
											  <th>Nombre</th>
											  <th>Electores</th>							  
											  <th>Estatus</th>
											</tr>
										  </thead>
										  <tbody id="tablaElementos"></tbody>
										</table>																			
								</div>
							</div>
						</div>
						<div class="widget" id="divEdicionDelegacion" style="display:none;">
							<div class="widget-head">
							  <div class="pull-left">Datos de la Delegación</div>
							  <div class="widget-icons pull-right">
							  </div>
							  <div class="clearfix"></div>
							</div>						
							<div class="widget-content">						
								<div class="delimitado" id="edicionDelegaciones">	
									<br>
									<div class="form-group">
										<label class="col-xs-4 control-label" style="text-align:right">ID</label>
										<div class="col-xs-8">
											<input type="text" class="form-control" name="txtIdDelegacion" readonly />
										</div>
									</div>
									<br>
									
									<div class="form-group">
										<label class="col-xs-4 control-label" style="text-align:right">Nombre(s)</label>
										<div class="col-xs-8">
											<input type="text" class="form-control" name="txtNombreDel"  />
										</div>
									</div>
									<br>
									<div class="form-group">
										<label class="col-xs-4 control-label" style="text-align:right">Alias</label>
										<div class="col-xs-8">
											<input type="text" class="form-control" name="txtCortoDel"  />
										</div>
									</div>
									<br>
									<div class="form-group">
										<label class="col-xs-4 control-label" style="text-align:right">Electores</label>
										<div class="col-xs-8">
											<input type="text" class="form-control" name="txtHabitantesDel" />
										</div>
									</div>
									<br>
									<div class="form-group">
										<label class="col-xs-4 control-label" style="text-align:right">Lat/Lng</label>
										<div class="col-xs-4">									
											<input type="text" class="form-control" placeholder="Lat." name="txtLatitudDel" id="txtLatitudDel"/>
										</div>
										<div class="col-xs-4">
											<input type="text" class="form-control" placeholder="Lng." name="txtLongitudDel"/>
										</div>
									</div>
									<br>
								</div>

							</div>
							<div class="widget-foot">
								<button type="button" class="btn btn-warning btn-xs" id="btnVerDistritos">Ver Distritos</button>	
								<button type="button" class="btn btn-primary btn-xs" id="btnGuardarDelegacion"><span class="fa fa-floppy-o"></span> Guardar </button>	
								<button onclick="javascript:activarPanel('');" type="button" class="btn btn-default btn-xs">Cancelar</button>	
							</div>
							
						</div>
						<div class="widget" id="divEdicionDistrito" style="display:none;">
							<div class="widget-head">
							  <div class="pull-left">Datos del Distrito</div>
							  <div class="widget-icons pull-right">
							  </div>
							  <div class="clearfix"></div>
							</div>						
							<div class="widget-content">						
								<div class="delimitado">
									
									<br>
									<div class="form-group">
										<label class="col-xs-4 control-label" style="text-align:right">ID</label>
										<div class="col-xs-8">
											<input type="text" class="form-control" name="txtIdDistrito" readonly />
										</div>
									</div>
									<br>
									
									<div class="form-group">
										<label class="col-xs-4 control-label" style="text-align:right">Nombre(s)</label>
										<div class="col-xs-8">
											<input type="text" class="form-control" name="txtNombreDis"  />
										</div>
									</div>
									<br>
									<div class="form-group">
										<label class="col-xs-4 control-label" style="text-align:right">Electores</label>
										<div class="col-xs-8">
											<input type="text" class="form-control" name="txtHabitantesDis" />
										</div>
									</div>
									<br>
									<div class="form-group">
										<label class="col-xs-4 control-label" style="text-align:right">Lat/Lng</label>
										<div class="col-xs-4">									
											<input type="text" class="form-control" placeholder="Lat." name="txtLatitudDis" id="txtLatitudDis"/>
										</div>
										<div class="col-xs-4">
											<input type="text" class="form-control" placeholder="Lng." name="txtLongitudDis" id="txtLongitudDis"/>
										</div>
									</div>
									<br>

								</div>
							</div>
							<div class="widget-foot">
								<button type="button" class="btn btn-warning btn-xs" id="btnVerSecciones">Ver Secciones</button>	
								<button type="button" class="btn btn-primary btn-xs" id="btnGuardarDistrito"><span class="fa fa-floppy-o"></span> Guardar </button>
								<button onclick="javascript:activarPanel('');" type="button" class="btn btn-default btn-xs">Cancelar</button>	
							</div>
							
						</div>
						
						<input type='HIDDEN' name='txtValores' value=''>						
						<div class="widget" id="divEdicionSeccion" style="display:none;">
							<div class="widget-head">
							  <div class="pull-left">Edición</div>
							  <div class="widget-icons pull-right">
							  </div>
							  <div class="clearfix"></div>
							</div>						
							<div class="widget-content">						
								<div class="delimitado" id="edicionDelegaciones">									
									<br>
									<div class="form-group">
										<label class="col-xs-4 control-label" style="text-align:right">Sección</label>
										<div class="col-xs-8">
											<input type="text" class="form-control" name="txtIdSeccion" readonly />
										</div>
									</div>
									<br>
									<div class="form-group">
										<label class="col-xs-4 control-label" style="text-align:right">Electores</label>
										<div class="col-xs-8">
											<input type="text" class="form-control" name="txtHabitantesSec" />
										</div>
									</div>
									<br>
									<div class="form-group">
										<label class="col-xs-4 control-label" style="text-align:right">Metas</label>
										<div class="col-xs-4">									
											<input  type="text" class="form-control" placeholder="Total" name="txtMetaTotal" id="txtMetaTotal"/>
										</div>
										<div class="col-xs-4">
											<input  type="text" class="form-control" placeholder="Por dia" name="txtMetaDiaria" id="txtMetaDiaria"/>
										</div>
									</div>
									<br>

									<div class="form-group">
										<label class="col-xs-4 control-label" style="text-align:right">Ubicación</label>
										<div class="col-xs-4">									
											<input  type="text" class="form-control" placeholder="Lat." name="txtLatitudSec" id="txtLatitudSec"/>
										</div>
										<div class="col-xs-4">
											<input  type="text" class="form-control" placeholder="Lng." name="txtLongitudSec" id="txtLongitudSec"/>
										</div>
									</div>
									<br>
			
									<table class="table table-striped table-bordered table-hover" id="tblPuntos" style="display:none;">
									  <thead>
										<tr><th>Punto No.</th><th>Latitud</th><th>Longitud</th></tr>
									  </thead>
									  <tbody  id="tablaPuntos"></tbody>
									</table>


								</div>
							</div>
							<div class="widget-foot">
									<button type="button" class="btn btn-default btn-xs" id="btnMarcar"><span class="fa fa-map-marker"></span> Marcar </button>
									<button type="button" class="btn btn-primary btn-xs"  style="display:none" id="btnPoligono"><span class="fa fa-spinner"></span> Polígono </button>
									<button type="button" class="btn btn-default btn-xs" style="display:none" id="btnLimpiar"><span class="fa fa-trash-o"></span> Limpiar </button>
									<button type="button" class="btn btn-primary btn-xs" id="btnGuardar"><span class="fa fa-floppy-o"></span> Guardar </button>
									<button type="button" class="btn btn-default btn-xs" id="btnCancelar"><span class="fa fa-arrow-circle-left"></span> Cancelar </button>						
							</div>
							
						</div>
					</div>
					<div class="col-md-12"><pre><div id="mapa_ppal"></div></pre></div>


					
				</div>
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

<script src="https://maps.googleapis.com/maps/api/js?callback=initMap" async defer></script>


<!-- Charts & Graphs -->

<!-- 
<script src="./Dashboard - MacAdmin_files/charts.js"></script> 

<script src="./Dashboard - MacAdmin_files/moment.min.js"></script> 
<script src="./Dashboard - MacAdmin_files/fullcalendar.min.js"></script> 
<script src="./Dashboard - MacAdmin_files/jquery.rateit.min.js"></script> 
<script src="./Dashboard - MacAdmin_files/jquery.prettyPhoto.js"></script>
-->
</body></html>


