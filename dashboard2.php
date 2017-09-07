<!DOCTYPE html>
<html lang="en">
<head>
    <title>Sistema Integral de Auditorías</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	
	<script type="text/javascript" src="js/canvasjs.min.js"></script>
	<script type="text/javascript" src="js/genericas.js"></script>
	<script type="text/javascript" src="js/fot/jquery.js"></script>
	<script type="text/javascript" src="js/fot/jquery.flot.js"></script>	
	
		
		<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?&sensor=true"></script>
		<script type="text/javascript" src="https://google-maps-utility-library-v3.googlecode.com/svn-history/r391/trunk/markerwithlabel/src/markerwithlabel.js"></script>
	
	
	    <!--Loading bootstrap css-->
    <link type="text/css" rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:400italic,400,300,700">
    <link type="text/css" rel="stylesheet" href="http://fonts.googleapis.com/css?family=Oswald:400,700,300">
    <link type="text/css" rel="stylesheet" href="styles/jquery-ui-1.10.4.custom.min.css">
    <link type="text/css" rel="stylesheet" href="styles/font-awesome.min.css">
    <link type="text/css" rel="stylesheet" href="styles/bootstrap.min.css">
    <link type="text/css" rel="stylesheet" href="styles/animate.css">
    <link type="text/css" rel="stylesheet" href="styles/all.css">
    <link type="text/css" rel="stylesheet" href="styles/main.css">
    <link type="text/css" rel="stylesheet" href="styles/style-responsive.css">
    <link type="text/css" rel="stylesheet" href="styles/zabuto_calendar.min.css">
    <link type="text/css" rel="stylesheet" href="styles/pace.css">
    
	
	
	<style type="text/css">		
		@media screen and (min-width: 768px) {
			#canvasMapa {width:100%; height:450px;}
			#modalFlotante .modal-dialog  {width:95%;}
			#canvasJG, #canvasJD, #canvasDIP{height:140px; width:100%;}			
		}
		.delimitado { height: 350px !important;overflow: scroll;}​
		.delimitadoIntenciones { height: 200px !important;overflow: scroll;}​
		.listBox {width:100%;border:0px solid lightgray;background-color:transparent;font-family: 'Arial';font-size:12px;padding: 1px 1px 1px 1px;cursor:pointer;-webkit-appearance: none;}
		label{text-align:right; text-valign:middle};
		
			
	</style>
	
<script src="script/jquery-1.10.2.min.js"></script>
    <script src="script/jquery-migrate-1.2.1.min.js"></script>
    <script src="script/jquery-ui.js"></script>
    <script src="script/bootstrap.min.js"></script>
    <script src="script/bootstrap-hover-dropdown.js"></script>
    <script src="script/html5shiv.js"></script>
    <script src="script/respond.min.js"></script>
    <script src="script/jquery.metisMenu.js"></script>
    <script src="script/jquery.slimscroll.js"></script>
    <script src="script/jquery.cookie.js"></script>
    <script src="script/icheck.min.js"></script>
    <script src="script/custom.min.js"></script>
    
    <script src="script/jquery.menu.js"></script>
    <script src="script/pace.min.js"></script>
    <script src="script/holder.js"></script>
    <script src="script/responsive-tabs.js"></script>
    
	<script src="script/jquery.flot.js"></script>
    <script src="script/jquery.flot.categories.js"></script>
    <script src="script/jquery.flot.pie.js"></script>
    <script src="script/jquery.flot.tooltip.js"></script>
    <script src="script/jquery.flot.resize.js"></script>
    <script src="script/jquery.flot.fillbetween.js"></script>
    <script src="script/jquery.flot.stack.js"></script>
    <script src="script/jquery.flot.spline.js"></script>
    <script src="script/zabuto_calendar.min.js"></script>

    <script src="script/index.js"></script>
    <!--LOADING SCRIPTS FOR CHARTS-->
    <script src="script/highcharts.js"></script>
    <script src="script/data.js"></script>
    <script src="script/drilldown.js"></script>
    <script src="script/exporting.js"></script>
    <script src="script/highcharts-more.js"></script>
    <script src="script/charts-highchart-pie.js"></script>
    <script src="script/charts-highchart-more.js"></script>
    <script src="script/charts-flotchart.js"></script>
    
	<!--CORE JAVASCRIPT-->
    <script src="script/main.js"></script>
    <script>        (function (i, s, o, g, r, a, m) {
            i['GoogleAnalyticsObject'] = r;
            i[r] = i[r] || function () {
                (i[r].q = i[r].q || []).push(arguments)
            }, i[r].l = 1 * new Date();
            a = s.createElement(o),
            m = s.getElementsByTagName(o)[0];
            a.async = 1;
            a.src = g;
            m.parentNode.insertBefore(a, m)
        })(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');
        ga('create', 'UA-145464-12', 'auto');
        ga('send', 'pageview');
		//alert("Aqui G Analis");
	</script>
	
	
	<script type="text/javascript"> 
	var mapa;
	var nZoom=12;	
	var nUsr='<?php echo $_SESSION["idUsuario"];?>';
	var nCampana='<?php echo $_SESSION["idCampanaActal"];?>';
	
	  
	$(document).ready(function(){
		
		
		
		//------------------------------------
		var d1 = [];
		for (var i = 0; i < 14; i += 0.5) {
			d1.push([i, Math.sin(i)]);
		}

		var d2 = [[0, 3], [4, 8], [8, 5], [9, 13]];

		// A null signifies separate line segments
		var d3 = [[0, 12], [7, 12], null, [7, 2.5], [12, 2.5]];
		$.plot("#placeholder", [ d1, d2, d3 ]);

		// Add the Flot version string to the footer
		$("#footer").prepend("Flot " + $.plot.version + " &ndash; ");		
		//------------------------------------
		
		if(nUsr!="" && nCampana!=""){
			//recuperarListaSelected('lstCampanasByUsr', nUsr, document.all.txtCampana,nCampana);
			//cargarMenu( nCampana);			
		}else{
			if(nCampana=="")alert("Debe establecer una CAMPAÑA como PREDETERMINADA. Por favor consulte con su administrador del sistema.");
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
	
	
	function pintarAuditoria(sAuditoria, lat, lng, cadena){

		var pos = new google.maps.LatLng(lat, lng);							
		var marker = new MarkerWithLabel({position: pos, draggable: false, raiseOnDrag: true,  map:  mapa,labelContent: " Auditoría " + sAuditoria,	
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
			zoom:nZoom,
			panControl:false,
			zoomControl:true,
			mapTypeControl:false,
			scaleControl:false,
			streetViewControl:false,
			overviewMapControl:false,
			rotateControl:false,    
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			mapTypeControlOptions: {
			  mapTypeIds: [google.maps.MapTypeId.ROADMAP, 'map_style']
			}
			,styles: [
				{"stylers": [{"hue": "#2c3e50"},{"saturation": 250}]},
				{"featureType": "road","elementType": "geometry","stylers": [{"lightness": 50},{"visibility": "simplified"}]},
				{"featureType": "road","elementType": "labels","stylers": [{"visibility": "off"}]}
			]			
		};
		
		mapa = new google.maps.Map(document.getElementById('canvasMapa'), opciones);
		//mapa = new google.maps.Map(document.getElementById('canvasMapa'), {center: {lat: 19.4339249, lng: -99.1428964},zoom: 8});		
		//alert("Mostrando mapa 1");		
	}	
		
	
	function cambiarModo(sModo){
		var stylesActual;
		
		//alert("Cambiando de modo");
		//alert(sModo);
		if (sModo=="M-AVANCE"){
			document.all.divAvances.style.display="block";
			document.all.divMapa.style.display="none";
			document.all.divGraficas.style.display="none";
		}
		
		if (sModo=="M-GRAFICAS"){
			document.all.divGraficas.style.display="block";
			document.all.divAvances.style.display="none";
			document.all.divMapa.style.display="none";			
		}			
				
		if (sModo=="M-REGIONES"){
			document.all.divAvances.style.display="none";
			document.all.divMapa.style.display="block";
			document.all.divGraficas.style.display="none";
			
			stylesActual = [
			  {stylers: [{ hue: "#2c3e50" },{ saturation: 250 }]},
			  {featureType: "road",elementType: "geometry",stylers: [{ lightness: 50 },{ visibility: "simplified" }]},
			  {featureType: "road",elementType: "labels",stylers: [{ visibility: "off" }]}
			];		
		//alert("Cambiando de modo3");
			inicializar();
			
			pintarAuditoria("ASCM/0123/2014", 19.3924428,-99.1681316, "50% Avance<br><br>2 Auditor(es) en sitio:<br>Alfonso Sosa, Juan Hernandez");
			pintarAuditoria("ASCM/0123/2014", 19.3781436,-99.133083, "20% Avance<br><br>1 Auditor(es) en sitio:<br>Alfonso Hernandez");
			pintarAuditoria("ASCM/0123/2014", 19.4525486,-99.1136091, "90% Avance<br><br>3 Auditor(es) en sitio:<br>Luis Sosa, Juan Cruz, Sofía Valenzuela Rojas");
			pintarAuditoria("ASCM/0123/2014", 19.4672405,-99.2059982, "50% Avance<br><br>4 Auditor(es) en sitio:<br>Alejadro Lujan, Oscar Garduño, Ricardo Peña, Alfredo Valez Ríos");
			pintarAuditoria("ASCM/0123/2014  (IEDF)", 19.2903742,-99.1376439, "35% Avance<br><br>2 Auditor(es) en sitio:<br>Alejandro  Garduño, Ricardo Ríos");
			pintarAuditoria("SEDE CENTRAL", 19.2624521,-99.1164637, "AUDITORÍA SUPERIOR DE LA CIUDAD DE MÉXICO");

		var styledMap = new google.maps.StyledMapType(stylesActual,{name: "Styled Map"});		
		mapa.mapTypes.set('map_style', styledMap);
		mapa.setMapTypeId('map_style');
		}		
	}
	
	
    </script>
	
	
    <link rel="shortcut icon" href="images/icons/favicon.ico">
    <link rel="apple-touch-icon" href="images/icons/favicon.png">
    <link rel="apple-touch-icon" sizes="72x72" href="images/icons/favicon-72x72.png">
    <link rel="apple-touch-icon" sizes="114x114" href="images/icons/favicon-114x114.png">
    <!--Loading bootstrap css-->
    <link type="text/css" rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:400italic,400,300,700">
    <link type="text/css" rel="stylesheet" href="http://fonts.googleapis.com/css?family=Oswald:400,700,300">
    <link type="text/css" rel="stylesheet" href="styles/jquery-ui-1.10.4.custom.min.css">
    <link type="text/css" rel="stylesheet" href="styles/font-awesome.min.css">
    <link type="text/css" rel="stylesheet" href="styles/bootstrap.min.css">
    <link type="text/css" rel="stylesheet" href="styles/animate.css">
    <link type="text/css" rel="stylesheet" href="styles/all.css">
    <link type="text/css" rel="stylesheet" href="styles/main.css">
    <link type="text/css" rel="stylesheet" href="styles/style-responsive.css">
    <link type="text/css" rel="stylesheet" href="styles/zabuto_calendar.min.css">
    <link type="text/css" rel="stylesheet" href="styles/pace.css">
	
    
</head>
<body>
        <!--BEGIN TOPBAR-->
		
		<div class="navbar navbar-fixed-top bs-docs-nav" role="banner">  
            <nav id="topbar" role="navigation" style="margin-bottom: 0;" data-step="10" class="navbar navbar-default navbar-static-top">
				<div class="navbar-header">
					<button type="button" data-toggle="collapse" data-target=".sidebar-collapse" class="navbar-toggle"><span class="sr-only">Toggle navigation</span><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span></button>
					<a href="/"><img src="img/logo-top.png"></a>
				</div>
				<div class="topbar-main"><a id="menu-toggle" href="#" class="hidden-xs"><i class="fa fa-bars"></i></a>


					<ul class="nav navbar navbar-top-links navbar-right mbn">
						<li class="dropdown">
							<a data-hover="dropdown" href="#" class="dropdown-toggle"></i><?php echo $_SESSION["sCampanaActal"] ?></a>
						</li>
					
						<li class="dropdown"><a data-hover="dropdown" href="#" class="dropdown-toggle"><i class="fa fa-bell fa-fw"></i><span class="badge badge-green">3</span></a></li>
						
						<li class="dropdown"><a data-hover="dropdown" href="#" class="dropdown-toggle"><i class="fa fa-envelope fa-fw"></i><span class="badge badge-orange">7</span></a></li>
						
						<li class="dropdown"><a data-hover="dropdown" href="#" class="dropdown-toggle"><i class="fa fa-tasks fa-fw"></i><span class="badge badge-yellow">8</span></a></li>
						
						<li class="dropdown topbar-user">
							<a data-hover="dropdown" href="#" class="dropdown-toggle">
								<img src="images/avatar/48.jpg" alt="" class="img-responsive img-circle"/>&nbsp;
								<span class="hidden-xs">
									C. <?php echo $_SESSION["sUsuario"] ?>
								</span>&nbsp;
								<span class="caret"></span>
							</a>
								<ul class="dropdown-menu dropdown-user pull-right">
									<li><a href="./perfil"><i class="fa fa-user"></i>Perfil</a></li>
									<li class="divider"></li>
									<li><a href="./cerrar"><i class="fa fa-key"></i>Salir</a></li>
								</ul>
						</li>
						<li id="topbar-chat" class="hidden-xs"><a href="javascript:void(0)" data-step="4" data-intro="&lt;b&gt;Form chat&lt;/b&gt; keep you connecting with other coworker" data-position="left" class="btn-chat"><i class="fa fa-comments"></i><span class="badge badge-info">3</span></a></li>
					</ul>
				</div>
			</nav>
        </div>
		
            <!--BEGIN MODAL CONFIG PORTLET-->
            <div id="modal-config" class="modal fade">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" data-dismiss="modal" aria-hidden="true" class="close">
                                &times;</button>
                            <h4 class="modal-title">
                                Modal title</h4>
                        </div>
                        <div class="modal-body">
                            <p>
                                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed eleifend et nisl eget
                                porta. Curabitur elementum sem molestie nisl varius, eget tempus odio molestie.
                                Nunc vehicula sem arcu, eu pulvinar neque cursus ac. Aliquam ultricies lobortis
                                magna et aliquam. Vestibulum egestas eu urna sed ultricies. Nullam pulvinar dolor
                                vitae quam dictum condimentum. Integer a sodales elit, eu pulvinar leo. Nunc nec
                                aliquam nisi, a mollis neque. Ut vel felis quis tellus hendrerit placerat. Vivamus
                                vel nisl non magna feugiat dignissim sed ut nibh. Nulla elementum, est a pretium
                                hendrerit, arcu risus luctus augue, mattis aliquet orci ligula eget massa. Sed ut
                                ultricies felis.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" data-dismiss="modal" class="btn btn-default">
                                Close</button>
                            <button type="button" class="btn btn-primary">
                                Save changes</button>
                        </div>
                    </div>
                </div>
            </div>
            <!--END MODAL CONFIG PORTLET-->		

		
       
        <!--END TOPBAR-->
		<br><br><br>
	<div id="wrapper">
            <!--BEGIN SIDEBAR MENU-->
            <nav id="sidebar" role="navigation" data-step="2" data-intro="Template has &lt;b&gt;many navigation styles&lt;/b&gt;" data-position="right" class="navbar-default navbar-static-side">
            <div class="sidebar-collapse menu-scroll">
                <ul id="side-menu" class="nav">                    
                     <div class="clearfix"></div>
					 
                    <li class="active"><a href="./dashboard"><i class="fa fa-tachometer fa-fw">
                        <div class="icon-bg bg-orange"></div>
                    </i><span class="menu-title">Dashboard</span></a></li>
                    <li><a href="Layout.html"><i class="fa fa-desktop fa-fw">
                        <div class="icon-bg bg-pink"></div>
                    </i><span class="menu-title">Programa General de Auditoría</span></a>
                       
                    </li>
                    <li><a href="UIElements.html"><i class="fa fa-search fa-fw">
                        <div class="icon-bg bg-green"></div>
                    </i><span class="menu-title">Auditorías</span></a>
                       
                    </li>
                    <li><a href="Forms.html"><i class="fa fa-edit fa-fw">
                        <div class="icon-bg bg-violet"></div>
                    </i><span class="menu-title">Papeles de Trabajo</span></a>
                      
                    </li>
                    <li><a href="Tables.html"><i class="fa fa-th-list fa-fw">
                        <div class="icon-bg bg-blue"></div>
                    </i><span class="menu-title">Avances Operativos</span></a>
                          
                    </li>
                    <li><a href="DataGrid.html"><i class="fa fa-database fa-fw">
                        <div class="icon-bg bg-red"></div>
                    </i><span class="menu-title">Auditores</span></a>
                      
                    </li>
                    <li><a href="Pages.html"><i class="fa fa-file-o fa-fw">
                        <div class="icon-bg bg-yellow"></div>
                    </i><span class="menu-title">Acopio</span></a>
                       
                    </li>
                    <li><a href="Extras.html"><i class="fa fa-gift fa-fw">
                        <div class="icon-bg bg-grey"></div>
                    </i><span class="menu-title">Promoción de Acciones</span></a>
                      
                    </li>
                    <li><a href="Dropdown.html"><i class="fa fa-sitemap fa-fw">
                        <div class="icon-bg bg-dark"></div>
                    </i><span class="menu-title">Catálogos</span></a>
                      
                    </li>
                    <li><a href="Email.html"><i class="fa fa-envelope-o">
                        <div class="icon-bg bg-primary"></div>
                    </i><span class="menu-title">Informes</span></a>
                      
                    </li>
                </ul>
            </div>
        </nav>

            <div id="page-wrapper">

                <!--BEGIN CONTENT-->
                <div class="page-content">
                    <div id="tab-general">
                        <div class="row mbl">
							<div  class="col-xs-12">                                       
								<div class="panel panel-default">
									<div class="panel-heading">
										<div class="pull-left"><h3><i class="fa fa-desktop"></i> Control de Mando</h2></div>
										<div class="pull-right">
											<div class="col-xs-12">
												<select class="form-control"  onchange="cambiarModo(this.value);">
													<option value="M-AVANCE">Modo Avances</option>
													<option value="M-REGIONES">Modo Regiones</option>
													<option value="M-GRAFICAS">Modo Gráficas</option>
												</select>
											</div>						
										</div>										
									</div>
									<div class="panel-body pan">

										<div  class="col-md-12" id="divGraficas"  style="display:none;">
											<div class="row">
												<div class="col-lg-6">
													<div class="portlet box">
														<div class="portlet-header">
															<div class="caption">Line Chart</div>
															<div class="tools"><i class="fa fa-chevron-up"></i><i data-toggle="modal" data-target="#modal-config" class="fa fa-cog"></i><i class="fa fa-refresh"></i><i class="fa fa-times"></i></div>
														</div>
														<div class="portlet-body">
															<div id="line-chart" style="width: 100%; height:300px"></div>
														</div>
													</div>
													<div class="portlet box">
														<div class="portlet-header">
															<div class="caption">Bar Chart</div>
															<div class="tools"><i class="fa fa-chevron-up"></i><i data-toggle="modal" data-target="#modal-config" class="fa fa-cog"></i><i class="fa fa-refresh"></i><i class="fa fa-times"></i></div>
														</div>
														<div class="portlet-body">
															<div id="bar-chart" style="width: 100%; height:300px"></div>
														</div>
													</div>
													<div class="portlet box">
														<div class="portlet-header">
															<div class="caption">Area Chart</div>
															<div class="tools"><i class="fa fa-chevron-up"></i><i data-toggle="modal" data-target="#modal-config" class="fa fa-cog"></i><i class="fa fa-refresh"></i><i class="fa fa-times"></i></div>
														</div>
														<div class="portlet-body">
															<div id="area-chart" style="width: 100%; height:300px"></div>
														</div>
													</div>
													<div class="portlet box">
														<div class="portlet-header">
															<div class="caption">Pie Chart</div>
															<div class="tools"><i class="fa fa-chevron-up"></i><i data-toggle="modal" data-target="#modal-config" class="fa fa-cog"></i><i class="fa fa-refresh"></i><i class="fa fa-times"></i></div>
														</div>
														<div class="portlet-body">
															<div id="pie-chart" style="width: 100%; height:300px"></div>
														</div>
													</div>
												</div>
												<div class="col-lg-6">
													<div class="portlet box">
														<div class="portlet-header">
															<div class="caption">Line Chart - Spline</div>
															<div class="tools"><i class="fa fa-chevron-up"></i><i data-toggle="modal" data-target="#modal-config" class="fa fa-cog"></i><i class="fa fa-refresh"></i><i class="fa fa-times"></i></div>
														</div>
														<div class="portlet-body">
															<div id="line-chart-spline" style="width: 100%; height:300px"></div>
														</div>
													</div>
													<div class="portlet box">
														<div class="portlet-header">
															<div class="caption">Bar Chart - Stack</div>
															<div class="tools"><i class="fa fa-chevron-up"></i><i data-toggle="modal" data-target="#modal-config" class="fa fa-cog"></i><i class="fa fa-refresh"></i><i class="fa fa-times"></i></div>
														</div>
														<div class="portlet-body"><h4 class="block-heading">Bar Chart - Stack</h4>

															<div id="bar-chart-stack" style="width: 100%; height:300px"></div>
														</div>
													</div>
													<div class="portlet box">
														<div class="portlet-header">
															<div class="caption">Area Chart - Spline</div>
															<div class="tools"><i class="fa fa-chevron-up"></i><i data-toggle="modal" data-target="#modal-config" class="fa fa-cog"></i><i class="fa fa-refresh"></i><i class="fa fa-times"></i></div>
														</div>
														<div class="portlet-body">
															<div id="Div1" style="width: 100%; height:300px"></div>
														</div>
													</div>
													<div class="portlet box">
														<div class="portlet-header">
															<div class="caption">Percentiles Chart</div>
															<div class="tools"><i class="fa fa-chevron-up"></i><i data-toggle="modal" data-target="#modal-config" class="fa fa-cog"></i><i class="fa fa-refresh"></i><i class="fa fa-times"></i></div>
														</div>
														<div class="portlet-body">
															<div id="percentiles-chart" style="width: 100%; height:300px"></div>
														</div>
													</div>
												</div>
											</div>
	
											<div class="clearfix"></div>						
										</div>
									
										<div  class="col-xs-12" id="divAvances">
											<div class="col-xs-3">
												<div class="demo-container">
													<div id="placeholder" class="demo-placeholder"> AQUI LA GRÁFICA</div>
												</div>					
											</div>
											<div class="col-xs-9">	                                       
												<div class="panel panel-default" style="border:1px solid lightgray;">
													<div class="panel-heading"><b><i class="fa fa-home"></i>Auditorías</b></div>
													<div class="panel-body pan">
														<div class="table-responsive" style="height: 350px; overflow: auto; overflow-x:hidden;">
															<table class="table table-striped table-bordered table-hover table-condensed">
															  <thead>
																<tr><th>No.</th><th>Sujetos de Fiscalización</th><th>Rubros o funciones de Gasto</th><th>Tipo de Auditoría</th></tr>
															  </thead>
															  <tbody>									  
																	<tr onclick=<?php echo "agregarAuditoria();"; ?> style="width: 100%; font-size: xx-small">
																		<td>01</td><td>SECRETARÍA DE GOBIERNO </td><td>CAPÍTULO 2000 “MATERIALES Y SUMINISTROS” Y PARTIDA 3221 “ARRENDAMIENTO DE EDIFICIOS” </td><td>FINANCIERA</td>
																	</tr>
																	<tr onclick=<?php echo "agregarAuditoria();"; ?> style="width: 100%; font-size: xx-small">
																		<td>02</td><td>SECRETARÍA DE GOBIERNO </td><td>CAPÍTULO 2000 “MATERIALES Y SUMINISTROS” Y PARTIDA 3221 “ARRENDAMIENTO DE EDIFICIOS” </td><td>FINANCIERA</td>
																	</tr>
																	<tr onclick=<?php echo "agregarAuditoria();"; ?> style="width: 100%; font-size: xx-small">
																		<td>03</td><td>SECRETARÍA DE GOBIERNO </td><td>CAPÍTULO 2000 “MATERIALES Y SUMINISTROS” Y PARTIDA 3221 “ARRENDAMIENTO DE EDIFICIOS” </td><td>FINANCIERA</td>
																	</tr>
																	<tr onclick=<?php echo "agregarAuditoria();"; ?> style="width: 100%; font-size: xx-small">
																		<td>04</td><td>SECRETARÍA DE GOBIERNO </td><td>CAPÍTULO 2000 “MATERIALES Y SUMINISTROS” Y PARTIDA 3221 “ARRENDAMIENTO DE EDIFICIOS” </td><td>FINANCIERA</td>
																	</tr>
																
																	<tr onclick=<?php echo "agregarAuditoria();"; ?> style="width: 100%; font-size: xx-small">
																		<td>09</td><td>SECRETARÍA DE GOBIERNO </td><td>CAPÍTULO 2000 “MATERIALES Y SUMINISTROS” Y PARTIDA 3221 “ARRENDAMIENTO DE EDIFICIOS” </td><td>FINANCIERA</td>
																	</tr>	
																	<tr onclick=<?php echo "agregarAuditoria();"; ?> style="width: 100%; font-size: xx-small">
																		<td>10</td><td>SECRETARÍA DE GOBIERNO </td><td>CAPÍTULO 2000 “MATERIALES Y SUMINISTROS” Y PARTIDA 3221 “ARRENDAMIENTO DE EDIFICIOS” </td><td>FINANCIERA</td>
																	</tr>
																	<tr onclick=<?php echo "agregarAuditoria();"; ?> style="width: 100%; font-size: xx-small">
																		<td>11</td><td>SECRETARÍA DE GOBIERNO </td><td>CAPÍTULO 2000 “MATERIALES Y SUMINISTROS” Y PARTIDA 3221 “ARRENDAMIENTO DE EDIFICIOS” </td><td>FINANCIERA</td>
																	</tr>
																	<tr onclick=<?php echo "agregarAuditoria();"; ?> style="width: 100%; font-size: xx-small">
																		<td>12</td><td>SECRETARÍA DE GOBIERNO </td><td>CAPÍTULO 2000 “MATERIALES Y SUMINISTROS” Y PARTIDA 3221 “ARRENDAMIENTO DE EDIFICIOS” </td><td>FINANCIERA</td>
																	</tr>
																	<tr onclick=<?php echo "agregarAuditoria();"; ?> style="width: 100%; font-size: xx-small">
																		<td>13</td><td>SECRETARÍA DE GOBIERNO </td><td>CAPÍTULO 2000 “MATERIALES Y SUMINISTROS” Y PARTIDA 3221 “ARRENDAMIENTO DE EDIFICIOS” </td><td>FINANCIERA</td>
																	</tr>											
																</tbody>
															</table>
														</div>
														<div class="clearfix"></div>                                               
													</div>
													<div class="panel-footer pan"></div>
												</div>
											</div>
										</div>
										<div class="col-xs-12" id="divMapa" style="display:none;">
											<div id="canvasMapa"><h2>mapa</h2></div>
											<div class="clearfix"></div>
										</div>									
									
								</div>
							</div>
						</div>
                        </div>
                    </div>
                </div>
                <!--END CONTENT-->
                <!--BEGIN FOOTER-->
                <div id="footer">
                    <div class="copyright">
                        <a href="http://www.ascm.gob.mx">2016 © Auditorìa Superior de la Ciudad de Mèxico</a></div>
                </div>
                <!--END FOOTER-->
            </div>
            <!--END PAGE WRAPPER-->
        </div>        
   





</body>
</html>
