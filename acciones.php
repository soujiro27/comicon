
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
			#modalHoja .modal-dialog  {width:50%;}
			label {text-align:right;}
			.auditor{background:#f4f4f4; font-size:6pt; padding:7px; display:inline; margin:1px; border:1px gray solid;}
			
.auditor[type=checkbox] {
    content: "\2713";
    text-shadow: 1px 1px 1px rgba(0, 0, 0, .2);
    font-size: 15px;
    color: #f3f3f3;
    text-align: center;
    line-height: 15px;
}
			
			
		}
	</style>
  
  <script type="text/javascript"> 
	var mapa;
	var nZoom=10;
	
	
	function guardarAuditoria(){
		document.all.listasAuditoria.style.display='inline';
		document.all.capturaAuditoria.style.display='none';
	}
	
	function agregarAuditoria(){
		document.all.listasAuditoria.style.display='none';
		document.all.capturaAuditoria.style.display='inline';
	}
	
	function cargarHoja(){
		$('#modalCargaHoja').removeClass("invisible");
		$('#modalCargaHoja').modal('toggle');
		$('#modalCargaHoja').modal('show');	
	}

	function agregaAccionesObservaciones(){
		$('#modalObservaciones').removeClass("invisible");
		$('#modalObservaciones').modal('toggle');
		$('#modalObservaciones').modal('show');	
	}
		

	
	
	
	window.onload = function () {
		var chart = new CanvasJS.Chart("canvasJG", {
			title:{ text: "TIPOS DE ACCIONES", fontColor: "#2f4f4f",fontSize: 10,verticalAlign: "top", horizontalAlign: "center" },
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
			{y: 59, indexLabel: "RECOMENDACIONES 59"}, {y: 21,  indexLabel: "OBSERVACIONES 21" }, {y: 31,  indexLabel: "HALLAZGOS 31" }
			]
		  }   
		  ]
		});
		chart.render();

		var chart2 = new CanvasJS.Chart("canvasJD", {
			title:{ text: "ESTATUS DE ACCIONES", fontColor: "#2f4f4f",fontSize: 10,verticalAlign: "top", horizontalAlign: "center" },
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
			{y: 59, indexLabel: "EN PROCESO 5"}, {y: 21,  indexLabel: "POR INICIAR 21"}, {y: 21,  indexLabel: "CONCLUIDAS 70"}
			]
		  }   
		  ]
		});
		chart2.render();			
	  };

	
	
	$(document).ready(function(){	
	
		$( "#btnGuardar" ).click(function() {
			$('#modalHoja').modal('hide');
		});
		
		
		$( "#btnRegresar" ).click(function() {
			document.all.listasAuditoria.style.display='inline';
			document.all.capturaAuditoria.style.display='none';		
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

			$('#modalHoja').modal('hide');
			
			

		});
		$( "#btnCancelar" ).click(function() {
			document.all.capturaDocto.style.display='inline';
			document.all.turnaDocto.style.display='none';

			document.all.btnGuardarEnviar.style.display='none';
			document.all.btnGuardar.style.display='inline';
			document.all.btnTurnar.style.display='inline';
			
			$('#modalHoja').modal('hide');
		});
		
		
		$( "#btnLigarDocto" ).click(function() {		
			$( "#btnUpload" ).click(); 
		});

		$( "#btnAnexarDocto" ).click(function() {		
			$( "#btnUpload" ).click(); 
		});	
		$( "#btnNuevoDocto" ).click(function() {
			$('#modalDocto').removeClass("invisible");
			$('#modalDocto').modal('toggle');
			$('#modalDocto').modal('show');						
		});
		$( "#btnCancelarDocto" ).click(function() {
			$('#modalDocto').modal('hide');
		});
		$( "#btnGuardarDocto" ).click(function() {
			$('#modalDocto').modal('hide');
		});		
		
		$( "#btnNuevoDictamen" ).click(function() {
			$('#modalDictamen').removeClass("invisible");
			$('#modalDictamen').modal('toggle');
			$('#modalDictamen').modal('show');						
		});
		$( "#btnCancelarDictamen" ).click(function() {
			$('#modalDictamen').modal('hide');
		});
		$( "#btnGuardarDictamen" ).click(function() {
			$('#modalDictamen').modal('hide');
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
    <nav class="navbar navbar-default navbar-fixed-top">
		<div class="container-fluid">
			<nav class="collapse navbar-collapse bs-navbar-collapse" role="navigation">			
				<div class="col-xs-12">
					<div class="col-xs-2"><a href="/"><img src="img/logo-top.png"></a></div>				
					<div class="col-xs-2">
						<ul class="nav navbar-nav "><li><a href="#"><i class="fa fa-th-list"></i> <?php echo $_SESSION["sCuentaActual"] ?></a></li></ul>
					</div>					
					<div class="col-xs-3"><h1>Cat. de Cuentas</h1></a></div>									
					<div class="col-xs-2">
						<ul class="nav navbar-nav "><li><a href="./notificaciones"><i class="fa fa-envelope-o"></i> Usted tiene <span class="badge">0</span> Mensaje(s).</a></li></ul>
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
  	<div class="panel panel-default">

			<div class="panel-body">
				<div class="row" id="listasAuditoria">
				<div class="col-md-3">

				  <!-- Widget -->
				  <div class="widget">
					<!-- Widget head -->
					<div class="widget-head">
					  <div class="pull-left">Acciones</div>
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
						<!-- 
						<Hr>
						<div id="canvasDIP" ></div><br>

						-->
					</div>
				  </div>
				</div>

				<div class="col-md-9">
				
				  <div class="widget">
					<div class="widget-head">
					  <div class="pull-left"><h2><i class="fa fa-cogs"></i> Promoción de Acciones</h2></div>
					  <div class="widget-icons pull-right">
						<a href="#" class="wminimize"><i class="fa fa-chevron-up"></i></a> 						
					  </div>  
					  <div class="clearfix"></div>
					</div>             
					<div class="widget-content">
							<div class="table-responsive" style="height: 350px; overflow: auto; overflow-x:hidden;">
								<table class="table table-striped table-bordered table-hover">
								  <thead>
									<tr><th>Clave Auditoría</th><th>Objeto de Fiscalización</th><th>Tipo de Auditoría</th><th>No.</th><th>Tipo de Acción</th><th>Estatus</th></tr>
								  </thead>
								  <tbody >									  
										<tr onclick=<?php echo "editarResultado('RESULT-0056');"; ?> style="width: 100%; font-size: xx-small">
											<td>ASCM-DGL-001/14</td><td>CAPÍTULO 6000 "INVERSIÓN PÚBLICA" (HOSPITAL VETERINARIO DELEGACIÓN IZTAPALAPA)</td><td>OBRA PÚBLICA</td><td>RESULT-0056</td><td>OBSERVACIÓN</td><td>CUMPLIDA</td>
										</tr>
										<tr onclick=<?php echo "editarResultado('RESULT-0057');"; ?> style="width: 100%; font-size: xx-small">
											<td>ASCM-DGL-001/14</td><td>CAPÍTULO 6000 "INVERSIÓN PÚBLICA" (HOSPITAL VETERINARIO DELEGACIÓN IZTAPALAPA)</td><td>OBRA PÚBLICA</td><td>RESULT-0057</td><td>RECOMENDACIÓN</td><td>CUMPLIDA</td>
										</tr>
										<tr onclick=<?php echo "editarResultado('RESULT-0058');"; ?> style="width: 100%; font-size: xx-small">
											<td>ASCM-DGL-001/14</td><td>CAPÍTULO 6000 "INVERSIÓN PÚBLICA" (HOSPITAL VETERINARIO DELEGACIÓN IZTAPALAPA)</td><td>OBRA PÚBLICA</td><td>RESULT-0058</td><td>ACCIÓN</td><td>CUMPLIDA</td>
										</tr>

										<tr onclick=<?php echo "editarResultado('RESULT-0059');"; ?> style="width: 100%; font-size: xx-small">
											<td>ASCM-DGL-001/14</td><td>CAPÍTULO 6000 "INVERSIÓN PÚBLICA" (HOSPITAL VETERINARIO DELEGACIÓN IZTAPALAPA)</td><td>OBRA PÚBLICA</td><td>RESULT-0059</td><td>OBSERVACIÓN</td><td>CUMPLIDA</td>
										</tr>

										<tr onclick=<?php echo "editarResultado('RESULT-0060');"; ?> style="width: 100%; font-size: xx-small">
											<td>ASCM-DGL-001/16</td><td>CAPÍTULO 9000 "INVERSIÓN PÚBLICA" CARRETERA FEDERAL</td><td>OBRA PÚBLICA</td><td>RESULT-0060</td><td>OBSERVACIÓN</td><td>CUMPLIDA</td>
										</tr>
										<tr onclick=<?php echo "editarResultado('RESULT-0057');"; ?> style="width: 100%; font-size: xx-small">
											<td>ASCM-DGL-001/16</td><td>CAPÍTULO 9000 "INVERSIÓN PÚBLICA" CARRETERA FEDERAL</td><td>OBRA PÚBLICA</td><td>RESULT-0057</td><td>RECOMENDACIÓN</td><td>CUMPLIDA</td>
										</tr>
										<tr onclick=<?php echo "editarResultado('RESULT-0058');"; ?> style="width: 100%; font-size: xx-small">
											<td>ASCM-DGL-001/16</td><td>CAPÍTULO 9000 "INVERSIÓN PÚBLICA" CARRETERA FEDERAL</td><td>OBRA PÚBLICA</td><td>RESULT-0058</td><td>ACCIÓN</td><td>CUMPLIDA</td>
										</tr>

										<tr onclick=<?php echo "editarResultado('RESULT-0059');"; ?> style="width: 100%; font-size: xx-small">
											<td>ASCM-DGL-001/16</td><td>CAPÍTULO 9000 "INVERSIÓN PÚBLICA" CARRETERA FEDERAL</td><td>OBRA PÚBLICA</td><td>RESULT-0059</td><td>OBSERVACIÓN</td><td>CUMPLIDA</td>
										</tr>										
										
									
								  </tbody>
								</table>
							</div>
					</div>
					<div class="widget-foot"><button onclick="agregarAuditoria();" type="button" class="btn btn-primary  btn-xs"><i class="fa fa-file-o"></i> Nueva Acción...</button></div>
					</div>
				</div>
			  </div>
			  
  			<div class="row" id="capturaAuditoria" style="display:none; padding:0px; margin:0px;">			
				<div class="col-xs-12">				
					<div class="widget">
						<!-- Widget head -->
						<div class="widget-head">
						  <div class="pull-left">Promoción de Acciones</div>
						  <div class="widget-icons pull-right"> 
							<button type="button" class="btn btn-warning  btn-xs" 	id="btnNuevoDictamen"><i class="fa fa-file-text-o"></i> Dictámen Técnico...</button>
							<button type="button" class="btn btn-primary  btn-xs"><i class="fa fa-floppy-o"></i> Guardar</button>
							<button type="button" id="btnImprimir" class="btn btn-default  btn-xs"><i class="fa fa-print"></i> Imprimir</button>
							<button type="button" id="btnRegresar" class="btn btn-default  btn-xs"><i class="fa fa-undo"></i> Regresar</button>							
						  </div>  
						  <div class="clearfix"></div>
						</div>              

						<!-- Widget content -->
						<div class="widget-content">
							<br>
							<div class="col-md-8">								
								<div class="form-group">
									<label class="col-xs-2 control-label">No.</label>
									<div class="col-xs-2">
										<input type="text" class="form-control" name="txtElector"/>
									</div>
									<label class="col-xs-2 control-label">Tipo de Acción</label>
									<div class="col-xs-6">
										<select class="form-control" name="txtNivel">
											<option value="">Seleccione...</option>
											<option value="">RECOMENDACION</option>
											<option value="" SELECTED>OBSERVACIÓN</option>
										</select>
									</div>
								</div>																					
							
								<br>
								<div class="form-group">
									<label class="col-xs-2 control-label">Sujeto de Fiscalización</label>
									<div class="col-xs-10">
										<select class="form-control" name="txtNivel">
											<option value="">Seleccione...</option>
											<option value="" SELECTED>SECRETARÍA DE OBRAS Y SERVICIOS</option>
										</select>
									</div>
								</div>								
								
								<br>
								<div class="form-group">									
									<label class="col-xs-2 control-label">Objeto de Fiscalización</label>
									<div class="col-xs-10">
										<select class="form-control" name="txtNivel">
											<option value="">Seleccione</option>
											<option value="" selected>CAPÍTULO 6000 "INVERSIÓN PÚBLICA" (HOSPITAL VETERINARIO DELEGACIÓN IZTAPALAPA)</option>
										</select>
									</div>									
								</div>
								<br>
								<div class="form-group">									
									<label class="col-xs-2 control-label">Etapa actual</label>
									<div class="col-xs-10">
										<select class="form-control" name="txtNivel">
											<option value="">Seleccione</option>
											<option value="" selected>DEMANDA</option>
											<option value="">RESOLUCIÓN</option>
											<option value="">AMPARO</option>
										</select>
									</div>									
								</div>								
								<br>
								<div class="form-group">
									<label class="col-xs-2 control-label">Descripcion</label>
									<div class="col-xs-10"></div>
									<div class="col-xs-12"><textarea class="form-control" rows="10" placeholder="Objetivo(s)"></textarea></div>
								</div>
								
							</div>
							<div class="col-xs-4">
								<ul class="nav nav-tabs">									
									<li  class="active"><a href="#tab-documentos" data-toggle="tab">Documentos / Gestión<i class="fa"></i></a></li>
								</ul>								
								<div class="tab-content">								
									<div class="tab-pane active" id="tab-documentos">
										<div class="table-responsive" style="height: 350px; overflow: auto; overflow-x:hidden;">
											<table class="table table-striped table-bordered table-hover">
											  <thead>
												<tr><th>No.</th><th>Tipo</th><th>Asunto</th><th>Origen</th><th>Destino</th><th>Estatus</th></tr>
											  </thead>
											  <tbody>									  
												<tr style="width: 100%; font-size: xx-small">
													<td>ASCM-2015/0001</td><td>OFICIO</td><td>AVISO DE AUDITORIAS</td><td>SHCP</td><td>ASCM</td><td>RECIBIDO</td>
												</tr>
												<tr style="width: 100%; font-size: xx-small">
													<td>ASCM-2015/0001</td><td>NOTA</td><td>SOLICITUD DE ESPACIO</td><td>SHCP</td><td>ASCM</td><td>ENVIADO</td>
												</tr>
												<tr style="width: 100%; font-size: xx-small">
													<td>ASCM-2015/0001</td><td>OFICIO</td><td>SOLICITUD DE INFORMACIÓN</td><td>SHCP</td><td>ASCM</td><td>RECIBIDO</td>
												</tr>
												<tr style="width: 100%; font-size: xx-small">
													<td>ASCM-2015/0001</td><td>OFICIO</td><td>ADEUNDUM</td><td>SHCP</td><td>ASCM</td><td>RECIBIDO</td>
												</tr>												
												<tr style="width: 100%; font-size: xx-small">
													<td>ASCM-2015/0001</td><td>OFICIO</td><td>OFICIO</td><td>SHCP</td><td>ASCM</td><td>RECIBIDO</td>
												</tr>
																								<tr style="width: 100%; font-size: xx-small">
													<td>ASCM-2015/0001</td><td>NOTA</td><td>AVISO DE AUDITORIAS</td><td>SHCP</td><td>ASCM</td><td>ENVIADO</td>
												</tr>
												<tr style="width: 100%; font-size: xx-small">
													<td>ASCM-2015/0001</td><td>OFICIO</td><td>AVISO DE AUDITORIAS</td><td>SHCP</td><td>ASCM</td><td>RECIBIDO</td>
												</tr>
											  </tbody>
											</table>											
										</div>
											<button type="button" class="btn btn-primary  btn-xs" 	id="btnNuevoDocto"><i class="fa fa-file-text-o"></i> Nuevo Documento...</button>
											<button type="button" class="btn btn-default  btn-xs" 	id="btnLigarDocto"><i class="fa fa-link"></i> Ligar Documento</button>
											<input type="file" name="pic" accept="image/*" style="display:none;" id="btnUpload">

											
									</div>									
								</div>																	
							</div>																									
							<div class="clearfix"></div>
						</div>
							
					</div>
				</div>


			</div>
			<div class="clearfix"></div>
			</div>
		</div>
   <div class="clearfix"></div>
</div>
<!-- Content ends -->



<div id="modalDocto" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<form id="formulario" METHOD='POST' role="form">
			<input type='HIDDEN' name='txtValores' value=''>								
			<!-- Modal content-->
			<div class="modal-content">									
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title"><i class="fa fa-file-o"></i> Registrar Documento...</h4>
				</div>									
				<div class="modal-body">				
					<div class="form-group">
						<label class="col-xs-2 control-label">Flujo</label>
						<div class="col-xs-4">
							<select class="form-control" name="txtNivel">
								<option value="">Seleccione...</option>
								<option value="" Selected>ENTRADA</option>
								<option value="">SALIDA</option>
							</select>
						</div>
						<label class="col-xs-2 control-label">Tipo</label>
						<div class="col-xs-4">
							<select class="form-control" name="txtNivel">
								<option value="">Seleccione...</option>
								<option value="" Selected>OFICIO</option>
								<option value="">ATENTA NOTA</option>
								<option value="">ADENDUM</option>
							</select>
						</div>
					</div>
					<br>
					<div class="form-group">
						<label class="col-xs-2 control-label">F. Docto</label>
						<div class="col-xs-2">
							<input type="text" class="form-control" name="txtElector"/>
						</div>
						<label class="col-xs-2 control-label">F. Recepción</label>
						<div class="col-xs-2">
							<input type="text" class="form-control" name="txtElector"/>
						</div>
						<label class="col-xs-2 control-label">F. Término</label>
						<div class="col-xs-2">
							<input type="text" class="form-control" name="txtElector"/>
						</div>						
					</div>
					<br>
					<div class="form-group">
						<label class="col-xs-2 control-label">Asunto</label>
						<div class="col-xs-10">
							<input type="text" class="form-control" name="txtElector"/>
						</div>						
					</div>						
					<br>					
					<div class="form-group">
						<label class="col-xs-2 control-label">Dependencia</label>
						<div class="col-xs-10">
							<select class="form-control" name="txtNivel">
								<option value="">Seleccione...</option>
								<option value="" Selected>DELEGACIÓN TLALPAN</option>
								<option value="">SALIDA</option>
							</select>
						</div>						
					</div>
					<br>
					<div class="form-group">
						<label class="col-xs-2 control-label">Prioridad</label>
						<div class="col-xs-4">
							<select class="form-control" name="txtNivel">
								<option value="">Seleccione...</option>
								<option value="" >BAJA</option>
								<option value="" Selected>MEDIA</option>
								<option value="" >ALTA</option>
							</select>
						</div>	
						<label class="col-xs-2 control-label">Impacto</label>
						<div class="col-xs-4">
							<select class="form-control" name="txtNivel">
								<option value="">Seleccione...</option>
								<option value="" >BAJO</option>
								<option value="" Selected>MEDIO</option>
								<option value="" >ALTO</option>
							</select>
						</div>						
					</div>	
					<br>
					<div class="form-group">
						<label class="col-xs-2 control-label">Resumen</label>
						<div class="col-xs-10"><textarea class="form-control" rows="4"></textarea></div>					
					</div>						
					<div class="clearfix"></div>
				</div>				
					<div class="modal-footer">
						<button  type="button" class="btn btn-warning btn-xs active" id="btnAnexarDocto" 		style="display:inline;">Anexar</button>	
						<button  type="button" class="btn btn-primary btn-xs active" id="btnGuardarDocto" 		style="display:inline;">Guardar</button>	
						<button  type="button" class="btn btn-default btn-xs" id="btnCancelarDocto" 		style="display:inline;">Cancelar</button>	
					</div>
			</div>		
		</form>
	</div>
</div>




<div id="modalDictamen" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<form id="formulario" METHOD='POST' role="form">
			<input type='HIDDEN' name='txtValores' value=''>								
			<!-- Modal content-->
			<div class="modal-content">									
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title"><i class="fa fa-file-o"></i> Registrar Dictámen...</h4>
				</div>									
				<div class="modal-body">				
					<div class="form-group">
						<label class="col-xs-2 control-label">Flujo</label>
						<div class="col-xs-4">
							<select class="form-control" name="txtNivel">
								<option value="">Seleccione...</option>
								<option value="" Selected>ENTRADA</option>
								<option value="">SALIDA</option>
							</select>
						</div>
						<label class="col-xs-2 control-label">Tipo</label>
						<div class="col-xs-4">
							<select class="form-control" name="txtNivel">
								<option value="">Seleccione...</option>
								<option value="" Selected>OFICIO</option>
								<option value="">ATENTA NOTA</option>
								<option value="">ADENDUM</option>
							</select>
						</div>
					</div>
					<br>
					<div class="form-group">
						<label class="col-xs-2 control-label">F. Docto</label>
						<div class="col-xs-2">
							<input type="text" class="form-control" name="txtElector"/>
						</div>
						<label class="col-xs-2 control-label">F. Recepción</label>
						<div class="col-xs-2">
							<input type="text" class="form-control" name="txtElector"/>
						</div>
						<label class="col-xs-2 control-label">F. Término</label>
						<div class="col-xs-2">
							<input type="text" class="form-control" name="txtElector"/>
						</div>						
					</div>
					<br>
					<div class="form-group">
						<label class="col-xs-2 control-label">Asunto</label>
						<div class="col-xs-10">
							<input type="text" class="form-control" name="txtElector"/>
						</div>						
					</div>						
					<br>					
					<div class="form-group">
						<label class="col-xs-2 control-label">Dependencia</label>
						<div class="col-xs-10">
							<select class="form-control" name="txtNivel">
								<option value="">Seleccione...</option>
								<option value="" Selected>DELEGACIÓN TLALPAN</option>
								<option value="">SALIDA</option>
							</select>
						</div>						
					</div>
					<br>
					<div class="form-group">
						<label class="col-xs-2 control-label">Prioridad</label>
						<div class="col-xs-4">
							<select class="form-control" name="txtNivel">
								<option value="">Seleccione...</option>
								<option value="" >BAJA</option>
								<option value="" Selected>MEDIA</option>
								<option value="" >ALTA</option>
							</select>
						</div>	
						<label class="col-xs-2 control-label">Impacto</label>
						<div class="col-xs-4">
							<select class="form-control" name="txtNivel">
								<option value="">Seleccione...</option>
								<option value="" >BAJO</option>
								<option value="" Selected>MEDIO</option>
								<option value="" >ALTO</option>
							</select>
						</div>						
					</div>	
					<br>
					<div class="form-group">
						<label class="col-xs-2 control-label">Resumen</label>
						<div class="col-xs-10"><textarea class="form-control" rows="4"></textarea></div>					
					</div>						
					<div class="clearfix"></div>
				</div>				
					<div class="modal-footer">
						<button  type="button" class="btn btn-warning btn-xs active" id="btnAnexarDocto" 		style="display:inline;">Anexar</button>	
						<button  type="button" class="btn btn-primary btn-xs active" id="btnGuardarDocto" 		style="display:inline;">Guardar</button>	
						<button  type="button" class="btn btn-default btn-xs" id="btnCancelarDocto" 		style="display:inline;">Cancelar</button>	
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



<div id="modalHoja" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<form id="formulario" METHOD='POST' action='/guardar/intencion' role="form" onsubmit="return validarEnvio();">
			<input type='HIDDEN' name='txtValores' value=''>								
			<!-- Modal content-->
			<div class="modal-content">									
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Registrar auditoría...</h4>
				</div>									
				<div class="modal-body">
					<div class="clearfix"></div>
				</div>
				
				<div class="modal-footer">
					<button  type="button" class="btn btn-primary active" id="btnGuardar" 		style="display:inline;">Guardar</button>	
					<button  type="button" class="btn btn-default active" id="btnCancelar" 		style="display:inline;">Cancelar</button>	
				</div>
			</div>		
		</form>
					
	</div>
</div>