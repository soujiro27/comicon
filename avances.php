<!DOCTYPE html>
<!-- saved from url=(0035)http://ashobiz.asia/mac52/macadmin/ -->
<html lang="en"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">  
  <meta charset="utf-8">
		<script type="text/javascript" src="js/canvasjs.min.js"></script>
		<script type='text/javascript' src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js?ver=3.1.2"></script> 
	
  	<style type="text/css">		
		@media screen and (min-width: 768px) {
			#mapa_content {width:100%; height:150px;}
			#canvasJG, #canvasJD, #canvasDIP{height:175px; width:100%;}			
			#modalFlotante .modal-dialog  {width:50%;}
			.auditor{background:#f4f4f4; font-size:7pt; padding:2px; display:inline; margin:2px; border:1px black solid;}
			
			
		}
	</style>
  
  <script type="text/javascript"> 
	var mapa;
	var nZoom=10;
	
	function agregarAuditoria(){
		document.all.listasAuditoria.style.display='none';
		document.all.capturaAuditoria.style.display='inline';
	}
	
	function recuperaDocto(){
		$('#modalFlotante').removeClass("invisible");
		$('#modalFlotante').modal('toggle');
		$('#modalFlotante').modal('show');
		
		document.all.btnGuardarEnviar.style.display='none';
		document.all.btnGuardar.style.display='inline';
		document.all.btnTurnar.style.display='inline';		
	}
	
	
	
	function inicializar() {
		var opciones = {zoom: nZoom,draggable: false,scrollwheel: true,	mapTypeId: google.maps.MapTypeId.ROADMAP};
		mapa = new google.maps.Map(document.getElementById('mapa_content'), { center: {lat: 19.4339249, lng: -99.1428964},zoom: nZoom});			
	}	
	
	window.onload = function () {
		var chart = new CanvasJS.Chart("canvasJG", {
			title:{ text: "TIPOS DE AUDITORIAS", fontColor: "#2f4f4f",fontSize: 10,verticalAlign: "top", horizontalAlign: "center" },
			axisX: {labelFontSize: 10,labelFontColor: "black", tickColor: "red",tickLength: 5,tickThickness: 2},		
			animationEnabled: true,
			//legend: {verticalAlign: "bottom", horizontalAlign: "center" },
			theme: "theme1", 
		  data: [
		  {        
			//color: "#B0D0B0",
			indexLabelFontSize: 10,indexLabelFontColor:"black",type: "pie",bevelEnabled: true,				
			//indexLabel: "{y}",
			showInLegend: false,legendMarkerColor: "gray",legendText: "{indexLabel} {y}",			
			dataPoints: [  				
			{y: 59, indexLabel: "FINANCIERAS 59"}, {y: 21,  indexLabel: "ADMINISTRATIVAS 21" }, {y: 31,  indexLabel: "OBRA 31" }
			]
		  }   
		  ]
		});
		chart.render();

		var chart2 = new CanvasJS.Chart("canvasJD", {
			title:{ text: "ESTATUS DE AUDITORIAS", fontColor: "#2f4f4f",fontSize: 10,verticalAlign: "top", horizontalAlign: "center" },
			axisX: {labelFontSize: 10,labelFontColor: "black", tickColor: "red",tickLength: 5,tickThickness: 2},		
			animationEnabled: true,
			//legend: {verticalAlign: "bottom", horizontalAlign: "center" },
			theme: "theme1", 
		  data: [
		  {        
			//color: "#B0D0B0",
			indexLabelFontSize: 10,indexLabelFontColor:"black",type: "pie",bevelEnabled: true,				
			//indexLabel: "{y}",
			showInLegend: false,legendMarkerColor: "gray",legendText: "{indexLabel} {y}",			
			dataPoints: [  				
			{y: 59, indexLabel: "EN PROCESO 59"}, {y: 21,  indexLabel: "POR INICIAR 21"}, {y: 21,  indexLabel: "CONCLUIDAS 10"}
			]
		  }   
		  ]
		});
		chart2.render();
	
		inicializar();		
	  };

	function actualizarInforme(sInforme){		
		document.all.divListaAvances.style.display='none';
		document.all.divCapturaAvance.style.display='inline';
		document.all.btnNuevoInforme.style.display='none';	
		alert('Actualizando: ' + sInforme);
	}

	
	
	$(document).ready(function(){	

	$( "#btnAvances" ).click(function() {
		$('#modalFlotante').removeClass("invisible");
		$('#modalFlotante').modal('toggle');
		$('#modalFlotante').modal('show');
	});
	

	$( "#btnInforme" ).click(function() {
		document.all.listasAuditoria.style.display='none';
		document.all.capturaAuditoria.style.display='inline';
	});	
	
	
	
	$( "#btnGuardar" ).click(function() {
		document.all.listasAuditoria.style.display='inline';
		document.all.capturaAuditoria.style.display='none';
	});
		
		$( "#btnCancelar" ).click(function() {
			document.all.listasAuditoria.style.display='inline';
			document.all.capturaAuditoria.style.display='none';
		});
		
	$( "#btnNuevoInforme" ).click(function() {
		document.all.divListaAvances.style.display='none';
		document.all.divCapturaAvance.style.display='inline';
		document.all.btnNuevoInforme.style.display='none';
	});

	$( "#btnGuardarInforme" ).click(function() {
		document.all.divListaAvances.style.display='inline';
		document.all.divCapturaAvance.style.display='none';
		document.all.btnNuevoInforme.style.display='inline';
	});	
		
	$( "#btnCancelarInforme" ).click(function() {
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
				<ul class="nav navbar-nav pull-left">
					<a href="/"><img src="img/logo-top.png" width="100%"></a>
				</ul>			
				<ul class="nav navbar-nav  style="text-align:center">
					<br>
					<i class="fa fa-bullhorn"></i><b> <?php echo $_SESSION["sCampanaActal"] ?></b>
				</ul>
				<ul class="nav navbar-nav pull-right">
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
			</nav>
		</div>
	</div>



<!-- Main content starts -->

<div class="content">
  	<div class="panel panel-default">
		<div class="panel-body">	
			<div class="panel-body">
				<div class="row" id="listasAuditoria">
				<div class="col-md-3">

				  <!-- Widget -->
				  <div class="widget">
					<!-- Widget head -->
					<div class="widget-head">
					  <div class="pull-left">Avances</div>
					  <div class="widget-icons pull-right">
						<a href="#" class="wminimize"><i class="fa fa-chevron-up"></i></a> 
					  </div>  
					  <div class="clearfix"></div>
					</div>              

					<!-- Widget content -->
					<div class="widget-content">
						<div id="canvasJG" ></div>
						<Hr>
						<div id="canvasJD" ></div>
						<br>
						<div class="clearfix"></div>
					</div>
				  </div>
				</div>
				<div class="col-md-9">				
				  <div class="widget">
					<div class="widget-head">
					  <div class="pull-left"><h2><i class="fa fa-tasks"></i> Informe de Avances</h2></div>
					  <div class="clearfix"></div>
					</div>             
					<div class="widget-content">
						<div class="table-responsive" style="height: 350px; overflow: auto; overflow-x:hidden;">
							<table class="table table-striped table-bordered table-hover table-condensed">
							  <thead>
								<tr><th>No.</th><th>Sujetos de Fiscalización</th><th>Rubros o funciones de Gasto</th><th>Tipo de Auditoría</th></tr>
							  </thead>
							  <tbody >									  
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
										<td>05</td><td>SECRETARÍA DE GOBIERNO </td><td>CAPÍTULO 2000 “MATERIALES Y SUMINISTROS” Y PARTIDA 3221 “ARRENDAMIENTO DE EDIFICIOS” </td><td>FINANCIERA</td>
									</tr>
									<tr onclick=<?php echo "agregarAuditoria();"; ?> style="width: 100%; font-size: xx-small">
										<td>06</td><td>SECRETARÍA DE GOBIERNO </td><td>CAPÍTULO 2000 “MATERIALES Y SUMINISTROS” Y PARTIDA 3221 “ARRENDAMIENTO DE EDIFICIOS” </td><td>FINANCIERA</td>
									</tr>
									<tr onclick=<?php echo "agregarAuditoria();"; ?> style="width: 100%; font-size: xx-small">
										<td>07</td><td>SECRETARÍA DE GOBIERNO </td><td>CAPÍTULO 2000 “MATERIALES Y SUMINISTROS” Y PARTIDA 3221 “ARRENDAMIENTO DE EDIFICIOS” </td><td>FINANCIERA</td>
									</tr>
									<tr onclick=<?php echo "agregarAuditoria();"; ?> style="width: 100%; font-size: xx-small">
										<td>08</td><td>SECRETARÍA DE GOBIERNO </td><td>CAPÍTULO 2000 “MATERIALES Y SUMINISTROS” Y PARTIDA 3221 “ARRENDAMIENTO DE EDIFICIOS” </td><td>FINANCIERA</td>
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
					</div>
					<div class="widget-foot">
						<button type="button"  id="btnInforme" class="btn btn-primary  btn-xs"><i class="fa fa-floppy-o"></i> Agregar Informe de Avance...</button>
					</div>
					</div>
				</div>
			</div>
		  
			<div class="row" id="capturaAuditoria" style="display:none; padding:0px; margin:0px;">			
				<div class="col-md-12">				
					<div class="widget">
						<!-- Widget head -->
						<div class="widget-head">
						  <div class="pull-left"><i class="fa fa-tasks"></i> Informe de Avance de Auditoría</div>
						  <div class="widget-icons pull-right">
							<button type="button"  id="btnAvances" class="btn btn-warning  btn-xs"><i class="fa fa-floppy-o"></i> Capturar Avance...</button>
							<button type="button"  id="btnGuardar" class="btn btn-primary  btn-xs"><i class="fa fa-floppy-o"></i> Guardar</button>
							<button type="button" id="btnImprimir" class="btn btn-default  btn-xs"><i class="fa fa-print"></i> Imprimir</button>
							<button type="button" id="btnCancelar" class="btn btn-default  btn-xs"><i class="fa fa-undo"></i> Regresar</button> 
						  </div>  
						  <div class="clearfix"></div>
						</div>              

						<!-- Widget content -->
						<div class="widget-content">						
							<div class="col-xs-7">	
								<br>							
								<div class="form-group">
									<label class="col-xs-2 control-label">Clave</label>
									<div class="col-xs-1">
										<input type="text" class="form-control" name="txtElector"/>
									</div>
									<label class="col-xs-1 control-label">Tipo</label>
									<div class="col-xs-3">
										<select class="form-control" name="txtNivel">
											<option value="">Seleccione...</option>
											<option value="" SELECTED>DESEMPEÑO</option>
										</select>
									</div>									
									<label class="col-xs-1 control-label">Fecha</label>
									<div class="col-xs-2">
										<input type="text" class="form-control" name="txtElector"/>
									</div>
									<label class="col-xs-1 control-label">Semanas</label>
									<div class="col-xs-1">
										<input type="text" class="form-control" name="txtElector"/>
									</div>										
								</div>
								<br>
								<div class="form-group">
									<label class="col-xs-2 control-label">Dirección Gral.</label>
									<div class="col-xs-10">
										<select class="form-control" name="txtNivel">
											<option value="">Seleccione...</option>
											<option value="" SELECTED>DIRECCIÓN GENERAL DE AUDITORÍA PROGRAMÁTICA-PRESUPUESTAL Y DE DESEMPEÑO</option>
										</select>
									</div>																	
								</div>	
								<br>
								<div class="form-group">
									<label class="col-xs-2 control-label">Dirección Área</label>
									<div class="col-xs-10">
										<select class="form-control" name="txtNivel">
											<option value="">Seleccione...</option>
											<option value="" SELECTED> DIRECCIÓN AREA DE AUDITORÍA PROGRAMÁTICA-PRESUPUESTAL Y DE DESEMPEÑO</option>
										</select>
									</div>																	
								</div>	
								<br>
							</div>
							<div class="col-xs-5">
								<br>							
								<div class="form-group">
									<label class="col-xs-2 control-label">Sujeto</label>
									<div class="col-xs-10">
										<select class="form-control" name="txtNivel">
											<option value="">Seleccione...</option>
											<option value="" SELECTED>SECRETARÍA DE DESARROLLO SOCIAL</option>
										</select>
									</div>																	
								</div>	
								
								<br>
								<div class="form-group">
									<label class="col-xs-2 control-label">Objeto</label>
									<div class="col-xs-10">
										<select class="form-control" name="txtNivel">
											<option value="">Seleccione...</option>
											<option value="" SELECTED>3.1 ASUNTOS ECONÓMICOS, COMERCIALES Y LABORALES EN GENERAL</option>
										</select>
									</div>																	
								</div>	
								<br>
								<div class="form-group">
									<label class="col-xs-2 control-label">Actividad</label>
									<div class="col-xs-10">
										<select class="form-control" name="txtNivel">
											<option value="">Seleccione...</option>
											<option value="" SELECTED>342 DESARROLLO Y FOMENTO ECONÓMICO</option>
										</select>
									</div>																	
								</div>
								<br>
							</div>

					<br>
					<hr>
					<table class="table table-striped table-bordered table-hover  table-condensed">
					  <thead>
						<tr><th>Fase</th><th></th><th>Fecha</th><th>Días Hábiles</th><th>Avance en Tiempo</th><th>Tiempo superior al programado</th><th>Tiempo inferior al programado</th></tr>
					  </thead>
					  <tbody >									  
							<tr style="width: 100%; font-size: xx-small">
								<td></td><td></td>
								<td>
									<div class="form-group">
										<label class="col-xs-6 control-label">Inicio</label>
										<label class="col-xs-6 control-label">Término</label>
									</div>										
								</td>								
								<td>R-/Pr.</td>
								<td>R-/Pr.</td>

								<td>
									<div class="form-group">
										<label class="col-xs-6 control-label">Porcentual.</label>
										<label class="col-xs-6 control-label">Absoluta</label>
									</div>										
								</td>

								<td>
									<div class="form-group">
										<label class="col-xs-6 control-label">Porcentual.</label>
										<label class="col-xs-6 control-label">Absoluta</label>
									</div>										
								</td>									
							</tr>
							
							<tr style="width: 100%; font-size: xx-small">
								<th rowspan=2>Avance general en tiempo de la auditoría</th><td>Pr.</td>
								<td>
									<div class="form-group">
										<label class="col-xs-6 control-label">18/01/2016</label>
										<label class="col-xs-6 control-label">31/03/2018</label>
									</div>										
								</td>								
								<td>102</td><td rowspan=2>29.4</td>

								<td rowspan=2>
									<div class="form-group">
										<label class="col-xs-6 control-label"></label>
										<label class="col-xs-6 control-label"></label>
									</div>										
								</td>
								<td rowspan=2>
									<div class="form-group">
										<label class="col-xs-6 control-label"></label>
										<label class="col-xs-6 control-label"></label>
									</div>										
								</td>									
							</tr>
							
							<tr style="width: 100%; font-size: xx-small">
								<td>R.</td>
								<td>
									<div class="form-group">
										<label class="col-xs-6 control-label">18/01/2016</label>
										<label class="col-xs-6 control-label"></label>
									</div>										
								</td>								
								<td>30</td>								
							</tr>	

							
							
							<tr style="width: 100%; font-size: xx-small">
								<th rowspan=2>Planeación</th><td>Pr.</td>
								<td>
									<div class="form-group">
										<label class="col-xs-6 control-label">18/01/2016</label>
										<label class="col-xs-6 control-label">31/03/2018</label>
									</div>										
								</td>								
								<td>102</td><td rowspan=2>29.4</td>

								<td rowspan=2>
									<div class="form-group">
										<label class="col-xs-6 control-label"></label>
										<label class="col-xs-6 control-label"></label>
									</div>										
								</td>
								<td rowspan=2>
									<div class="form-group">
										<label class="col-xs-6 control-label"></label>
										<label class="col-xs-6 control-label"></label>
									</div>										
								</td>									
							</tr>							
							<tr style="width: 100%; font-size: xx-small">
								<td>R.</td>
								<td>
									<div class="form-group">
										<label class="col-xs-6 control-label">18/01/2016</label>
										<label class="col-xs-6 control-label"></label>
									</div>										
								</td>								
								<td>30</td>								
							</tr>

							
							<tr style="width: 100%; font-size: xx-small">
								<th rowspan=2>Ejecución</th><td>Pr.</td>
								<td>
									<div class="form-group">
										<label class="col-xs-6 control-label">18/01/2016</label>
										<label class="col-xs-6 control-label">31/03/2018</label>
									</div>										
								</td>								
								<td>102</td><td rowspan=2>29.4</td>

								<td rowspan=2>
									<div class="form-group">
										<label class="col-xs-6 control-label"></label>
										<label class="col-xs-6 control-label"></label>
									</div>										
								</td>
								<td rowspan=2>
									<div class="form-group">
										<label class="col-xs-6 control-label"></label>
										<label class="col-xs-6 control-label"></label>
									</div>										
								</td>									
							</tr>							
							<tr style="width: 100%; font-size: xx-small">
								<td>R.</td>
								<td>
									<div class="form-group">
										<label class="col-xs-6 control-label">18/01/2016</label>
										<label class="col-xs-6 control-label"></label>
									</div>										
								</td>								
								<td>30</td>								
							</tr>

							<tr style="width: 100%; font-size: xx-small">
								<th rowspan=2>Elaboración</th><td>Pr.</td>
								<td>
									<div class="form-group">
										<label class="col-xs-6 control-label">18/01/2016</label>
										<label class="col-xs-6 control-label">31/03/2018</label>
									</div>										
								</td>								
								<td>102</td><td rowspan=2>29.4</td>

								<td rowspan=2>
									<div class="form-group">
										<label class="col-xs-6 control-label"></label>
										<label class="col-xs-6 control-label"></label>
									</div>										
								</td>
								<td rowspan=2>
									<div class="form-group">
										<label class="col-xs-6 control-label"></label>
										<label class="col-xs-6 control-label"></label>
									</div>										
								</td>									
							</tr>							
							<tr style="width: 100%; font-size: xx-small">
								<td>R.</td>
								<td>
									<div class="form-group">
										<label class="col-xs-6 control-label">18/01/2016</label>
										<label class="col-xs-6 control-label"></label>
									</div>										
								</td>								
								<td>30</td>								
							</tr>							
						</tbody>
					</table>							

					<div class="form-group">
						<label class="col-xs-4 control-label">Observaciones</label>
						<label class="col-xs-4 control-label">Medidas adoptadas</label>
						<label class="col-xs-4 control-label">Notas</label>
					</div>	

					<div class="form-group">
						<div class="col-xs-4"><textarea class="form-control" rows="3"></textarea></div>
						<div class="col-xs-4"><textarea class="form-control" rows="3"></textarea></div>
						<div class="col-xs-4"><textarea class="form-control" rows="3"></textarea></div>
					</div>													
					<div class="clearfix"></div>
					<br>
				</div>
			</div>

										
					
				</div>

			</div>
		  
		  
			<div class="clearfix"></div>
		</div>

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
						<h4 class="modal-title">Informe de Avance de Auditoría..</h4>
					</div>									
					<div class="modal-body">
						<div class="col-md-12">
							<div id="divListaAvances">
							<ul class="nav nav-tabs">
								<li class="active"><a href="#tab-documentos" data-toggle="tab">Informes<i class="fa"></i></a></li>
							</ul>								
							<div class="tab-content">
								<div class="tab-pane active" id="tab-documentos">
										<table class="table table-striped table-bordered table-hover table-condensed table-responsive">
										  <thead>
											<tr><th>No.Informe</th><th>Clave</th><th>Dirección de Área</th><th>Fase</th><th>Fecha</th></tr>
										  </thead>
										  <tbody >									  
												<tr onclick=<?php echo "actualizarInforme('004')"; ?> style="width: 100%; font-size: xx-small">
													<td>004</td><td>ASCM/001/14</td><td>DIRECCIÓN DE AUDITORIAS "B" </td><td>PLANEACIÓN</td><td>08-Marzo-2016</td>
												</tr>										  
												<tr onclick=<?php echo "actualizarInforme('003')"; ?> style="width: 100%; font-size: xx-small">
													<td>003</td><td>ASCM/001/14</td><td>DIRECCIÓN DE AUDITORIAS "B" </td><td>EJECUCIÓN</td><td>08-Marzo-2016</td>
												</tr>
												
												<tr onclick=<?php echo "actualizarInforme('002')"; ?> style="width: 100%; font-size: xx-small">
													<td>002</td><td>ASCM/001/14</td><td>DIRECCIÓN DE AUDITORIAS "B" </td><td>ELABORACIÓN</td><td>08-Marzo-2016</td>
												</tr>
											</tbody>
										</table>
								</div>								
							</div>
							</div>
							<div id="divCapturaAvance" style="display:none;">
								<div class="col-xs-12">								
									<div class="form-group">
										<label class="col-xs-3 control-label">ID</label>
										<div class="col-xs-3">
											<input type="text" class="form-control" name="txtElector" readonly/>
										</div>
									</div>
									
									<br>							
									<div class="form-group">
										<label class="col-xs-3 control-label">Dirección de área</label>
										<div class="col-xs-9">
											<select class="form-control" name="txtNivel">
												<option value="">Seleccione...</option>
												<option value="" SELECTED>DIRECCIÓN DE AUDITORÍA PROGRAMÁTICA-PRESUPUESTAL Y DE DESEMPEÑO</option>
											</select>
										</div>																		
									</div>								

									<br>							
									<div class="form-group">
										<label class="col-xs-3 control-label">Fase</label>
										<div class="col-xs-9">
											<select class="form-control" name="txtNivel">
												<option value="">Seleccione...</option>
												<option value="" SELECTED>PLANEACIÓN</option>
												<option value="" SELECTED>EJECUCIÓN</option>
												<option value="" SELECTED>ELABORACIÓN DE INFORMES</option>
											</select>
										</div>																		
									</div>										
									<br>
									<div class="form-group">									
										<label class="col-xs-3 control-label">Cantidad de Semanas</label>
										<div class="col-xs-3">
											<input type="text" class="form-control" name="txtElector"/>
										</div>										
									</div>

									<br>							
									<div class="form-group">
										<label class="col-xs-3 control-label">Elaboró</label>
										<div class="col-xs-9">
											<select class="form-control" name="txtNivel">
												<option value="">Seleccione...</option>
												<option value="" SELECTED>FERNANDO ROMERO LUCAS</option>
											</select>
										</div>																		
									</div>									
								</div>
							</div>							
						</div>								
						<div class="clearfix"></div>												
					</div>
					
					<div class="modal-footer">
						<button  type="button" class="btn btn-warning" id="btnNuevoInforme"><i class="fa fa-pencil-square-o"></i> Capturar Fase ...</button>	
						<button  type="button" class="btn btn-primary" id="btnGuardarInforme"><i class="fa fa-floppy-o"></i> Guardar</button>	
						<button  type="button" class="btn btn-default active" id="btnCancelarInforme"><i class="fa fa-undo"></i> Cancelar</button>	
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