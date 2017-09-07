<!DOCTYPE html>
<html lang="en">
	<head>	
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">  
		<meta http-equiv="X-UA-Compatible" content="IE=Edge"/>
		<!-- Title and other stuffs -->
		<title>Sistema Integral de Auditorias</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="SISTEMA INTEGRAL DE AUDITORÍAS">
		<meta name="keywords" content="SIA, AUDITORÍAS, ASCM">
		<meta name="author" content="JOSE AURELIO COTA ZAZUETA">
		<script type="text/javascript" src="js/canvasjs.min.js"></script>
		<script type='text/javascript' src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js?ver=3.1.2"></script>
		<script type="text/javascript" src="js/genericas.js"></script>
	
  	<style type="text/css">		
		@media screen and (min-width: 768px) {
			#mapa_content {width:100%; height:150px;}

			#cvGrafica1, #cvGrafica2{height:250px; width:100%;}						
			
			#CanvasGrafica1, #CanvasGrafica2{height:150px; width:100%;}		
			
			#modalAvance .modal-dialog  {width:60%;}
			.auditor{background:#f4f4f4; font-size:6pt; padding:7px; display:inline; margin:1px; border:1px gray solid;}
			
			label {text-align:right;}
			
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
		
		function recuperarActividades(){
			var param="";
			if (document.all.txtAuditoria.selectedIndex>0 && document.all.txtFase.selectedIndex>0){			
				param = ""  + document.all.txtAuditoria.options[document.all.txtAuditoria.selectedIndex].value;
				param = param + "/"  +  document.all.txtFase.options[document.all.txtFase.selectedIndex].value;				
				recuperarListaLigada('lstActividadesByAuditoriaFase', param, document.all.txtActividad);
				document.getElementById("txtActividad").disabled = false;
				
			}else{
				document.all.txtActividad.selectedIndex=0;		
				document.getElementById("txtActividad").disabled = true;				
			}
		}
		
		window.onload = function () {
			var chart1, chart2; 
			setGrafica(chart1, "dpsTipoAcopio", "pie", "", "CanvasGrafica1" );			
			setGrafica(chart2, "dpsTipoAcopio", "pie", "", "CanvasGrafica2" );			

			var chart3, chart4; 		
			//setGrafica(chart3, "dpsTipoPapeles", "pie", "Cédulas en el Área", "cvGrafica1" );
			//setGrafica(chart4, "dpsAvanceByAuditorias", "bar", "Avance por Auditoría", "cvGrafica2" );		
		};
		var ventana;
	
		function validaAvance(){			
			if (document.all.txtAuditoria.selectedIndex==0){
				alert("Debe seleccionar la AUDITORÍA.");
				document.all.txtAuditoria.focus();
				return false;
			}

			if (document.all.txtFase.selectedIndex==0){
				alert("Debe seleccionar la FASE.");
				document.all.txtFase.focus();
				return false;
			}

			if (document.all.txtApartado.selectedIndex==0){
				alert("Debe seleccionar un APARTADO.");
				document.all.txtApartado.focus();
				return false;
			}
			
			if (document.all.txtActividad.selectedIndex==0){
				alert("Debe seleccionar una ACTIVIDAD.");
				document.all.txtActividad.focus();
				return false;
			}


			
			
			if (document.all.txtPorcentaje.value==""){
				alert("Debe capturar un PORCENTAJE.");
				document.all.txtPorcentaje.focus();
				return false;
			}	

			if (document.all.txtFechaInicio.value==""){
				alert("Debe capturar una FECHA DE INICIO.");
				document.all.txtFechaInicio.focus();
				return false;
			}
			if (document.all.txtFechaFin.value==""){
				alert("Debe capturar una FECHA DE INICIO.");
				document.all.txtFechaFin.focus();
				return false;
			}			

			if (document.all.txtAuditor.selectedIndex==0){
				alert("Debe seleccionar el AUDITOR RESPONSABLE.");
				document.all.txtAuditor.focus();
				return false;
			}			
			
			return true;		
		}
		
		function limpiarCampos(){
			document.all.txtID.value="";			
			document.all.txtAuditoria.selectedIndex=0;
			document.all.txtFase.selectedIndex=0;
			document.all.txtActividad.selectedIndex=0;
			document.all.txtPorcentaje.value="";
			document.all.txtAuditor.selectedIndex=0;
		}
		var nUsr='<?php echo $_SESSION["idUsuario"];?>';
		var nAuditor='<?php echo $_SESSION["idEmpleado"];?>';
		
		var nCampana='<?php echo $_SESSION["idCuentaActual"];?>';
		var sArea='<?php echo $_SESSION["idArea"];?>';
		
	$(function(){
		$.datepicker.regional['es'] = {
	        closeText: 'Cerrar',
	        prevText: '&#x3c;Ant',
	        nextText: 'Sig&#x3e;',
	        currentText: 'Hoy',
	        monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
	        'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
	        monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun',
	        'Jul','Ago','Sep','Oct','Nov','Dic'],
	        dayNames: ['Domingo','Lunes','Martes','Mi&eacute;rcoles','Jueves','Viernes','S&aacute;bado'],
	        dayNamesShort: ['Dom','Lun','Mar','Mi&eacute;','Juv','Vie','S&aacute;b'],
	        dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','S&aacute;'],
	        weekHeader: 'Sm',
	        //dateFormat: 'yy-mm-dd',
	        //firstDay: 1,
	        isRTL: false,
	        showMonthAfterYear: false,
	        yearSuffix: ''
		};

		$.datepicker.setDefaults($.datepicker.regional["es"]);

		$("#FechaInicio").datepicker({
			dateFormat:'dd-mm-yy',
			numberOfMonths: 2,
			onClose: function ( selectedDate ) {
			 	$( "#FechaFin" ).datepicker( "option", "minDate", selectedDate);
			}

		});

		$("#FechaFin").datepicker({
			minDate: null,
	    	maxDate: null,
			dateFormat:'dd-mm-yy',
			firstDay: 1,
			numberOfMonths: 2,
		});
	});		


		$(document).ready(function(){
			getMensaje('txtNoti',1);
			if(nUsr!="" && nCampana!=""){
				cargarMenu( nCampana);			
			}else{
				if(nCampana=="")alert("Debe establecer una CUENTA PÚBLICA como PREDETERMINADA. Por favor consulte con su administrador del sistema.");
			}			
			
			recuperarListaLigada('lstAuditoriasByAreaAuditor', sArea, document.all.txtAuditoria);								
			recuperarListaLigada('lstAuditoresByArea', sArea, document.all.txtAuditor);					
			recuperarLista('lstApartados', document.all.txtApartado);
						
			$( "#btnGuardar" ).click(function() {
				if (validaAvance()){document.all.formulario.submit();}
			});			
			
			$( "#chkTerminada" ).click(function() {
				if (document.all.chkTerminada.checked==true){
					document.all.txtPorcentaje.value="100";
					document.getElementById("txtPorcentaje").disabled = true;
				} else{
					document.all.txtPorcentaje.value="0";
					document.getElementById("txtPorcentaje").disabled = false;
				}
			});			
			

		
			
			$( "#btnAgregar" ).click(function() {
				limpiarCampos();
				seleccionarElemento(document.all.txtAuditor, nAuditor);
				document.all.txtOperacion.value='INS';
				document.getElementById("txtAuditoria").disabled = false;
				
				$('#modalAvance').removeClass("invisible");
				$('#modalAvance').modal('toggle');
				$('#modalAvance').modal('show');
				
			});

			$( "#btnCancelar" ).click(function() {
				$('#modalAvance').removeClass("invisible");
				$('#modalAvance').modal('toggle');
				$('#modalAvance').modal('hide');
				document.all.txtOperacion.value='';
			});
			

		});
		
		
		
	function obtenerElemento(id){
		$.ajax({
			type: 'GET', url: '/avanceById/' + id ,
			success: function(response) {
				var obj = JSON.parse(response);				
				limpiarCampos();
				document.all.txtOperacion.value="UPD";
				document.getElementById("txtAuditoria").disabled = false;
				document.all.txtID.value=id;
				seleccionarElemento(document.all.txtAuditoria, obj.idAuditoria);
				seleccionarElemento(document.all.txtFase, obj.idFase);	
				
		
				var param = ""  + obj.idAuditoria + "/"  +  obj.idFase;
				recuperarListaSelected('lstActividadesByAuditoriaFase', param, document.all.txtActividad, obj.idActividad);
				document.getElementById("txtActividad").disabled = false;				
				
				document.all.txtPorcentaje.value='' + obj.porcentaje;
				
				seleccionarElemento(document.all.txtAuditor, obj.idAuditor);
				
				document.all.btnGuardar.style.display='inline';
				
				
				$('#modalAvance').removeClass("invisible");
				$('#modalAvance').modal('toggle');
				$('#modalAvance').modal('show');
		},
			error: function(xhr, textStatus, error){
				alert('function obtenerElemento()   -> statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error + ' Avance: ' + id);
			}			
		});		
	}		
		
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
  <!-- Data tables
  <link rel="stylesheet" href="css/jquery.dataTables.css"> 
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
<body>
    <nav class="navbar navbar-default navbar-fixed-top">
		<div class="container-fluid">
			<nav class="collapse navbar-collapse bs-navbar-collapse" role="navigation">			
				<div class="col-xs-12">
					<div class="col-xs-2"><a href="/"><img src="img/logo-top.png"></a></div>				
					<div class="col-xs-2">
						<ul class="nav navbar-nav "><li><a href="#"><i class="fa fa-th-list"></i> <?php echo $_SESSION["sCuentaActual"] ?></a></li></ul>
					</div>					
					<div class="col-xs-3"><h2>Avances por Actividad</h2></a></div>									
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
		  
		  <li class="has_sub"><a href="/cerrar"><i class="fa fa-sign-out"></i> Salir</a></li>		  
		  
		</ul>
	</div> <!-- Sidebar ends -->

  	  	<!-- Main bar -->
  	<div class="mainbar">
		<div class="row">
			<div class="col-md-3">
				<div class="widget">
					<div class="widget-head">
					<div class="clearfix"> 
					  <div class="pull-left"><h4><i class="fa fa-bars"></i> Avances por Auditoria del Usuario</h4></div>
					</div>
					</div>
					<div class="widget-content">
					<div class="clearfix">
						<div class="col-md-12">
							<div id="CanvasGrafica1"></div>
							<hr>
							<table class="table table-striped table-bordered table-hover table-condensed">
								<thead style="width: 100%; font-size: xx-small;">
									<tr><th style="text-align:center;">Tipo de Auditoría</th><th style="text-align:center;">Cantidad</th></tr>
								</thead>
								<tbody id="tblCanvasGrafica1" style="text-align:center; font-size: xx-small;">									
								</tbody>
							</table>
						</div>								
					<!--<div class="widget-foot"></div>-->
					</div>
					</div>
				</div>
				
				<div class="widget">
					<div class="widget-head">
					<div class="clearfix"> 
					  <div class="pull-left"><h4><i class="fa fa-bars"></i> Avance por Actividades del Usuario</h4></div>
					</div>
					</div>
					<div class="widget-content">
					<div class="clearfix">
						<div class="col-md-12">
							<div id="CanvasGrafica2"></div>
							<hr>
							<table class="table table-striped table-bordered table-hover table-condensed">
								<thead style="width: 100%; font-size: xx-small;">
									<tr><th style="text-align:center;">Actividades</th><th style="text-align:center;">Cantidad</th></tr>
								</thead>
								<tbody id="tblCanvasGrafica2" style="text-align:center; font-size: xx-small;">									
								</tbody>
							</table>
						</div>								
					<!--<div class="widget-foot"></div>-->
					</div>
					</div>
				</div>				
			</div>
			
			<div class="col-xs-9">
				<div class="widget">
					<div class="widget-head">
					  <div class="pull-left"><h3 class="modal-title"><i class="fa fa-tasks"></i> Lista de Avances</h3></div>
					  <div class="widget-icons pull-right">
						<button type="button" class="btn btn-primary active btn-xs" 	id="btnAgregar"><i class="fa fa-floppy-o"></i> Agregar</button>
					  </div>  
					  <div class="clearfix"></div>
					</div>             
					<div class="widget-content">					
						<div class="col-xs-12">							
							<div id="cvGrafica2"></div>						
						</div>	
						<div class="clearfix"></div>
						<hr>					
		
					
					
					
							<div class="table-responsive" style="height: 450px; overflow: auto; overflow-x:hidden;">
								<table class="table table-striped table-bordered table-hover table-condensed">
								  <thead>
									<tr><th>Clave Auditoría</th><th>Sujeto de Fiscalización</th><th>Tipo de Auditoría</th><th>Auditor</th><th>ID</th><th>Fase</th><th>Actividad</th><th>Porcentaje</th></tr>
								  </thead>
								  <tbody>				
									<?php foreach($datos as $key => $valor): ?>
									<tr onclick=<?php echo "obtenerElemento('" . $valor['idAvance'] . "');"; ?> style="width: 100%; font-size: xx-small">										  										  
									  <td><?php echo $valor['claveAuditoria']; ?></td>
									  <td><?php echo $valor['sujeto']; ?></td>									  
									  <td><?php echo $valor['tipoAuditoria']; ?></td>
									  <td><?php echo $valor['auditor']; ?></td>
									  <td><?php echo $valor['idAvance']; ?></td>									  
									  <td><?php echo $valor['fase']; ?></td>
									  <td><?php echo $valor['actividad']; ?></td>
									  <td><?php echo $valor['porcentaje']; ?></td>
									</tr>
									<?php endforeach; ?>																						
								  </tbody>
								</table>
								<div class="clearfix"></div>
							</div>

						<div class="clearfix"></div>
					</div>
					<div class="widget-foot">
					<div class="clearfix"></div>
					</div>
				</div>				
			</div>
		</div>
				
	</div>			
</div>


<div id="modalAvance" class="modal fade" role="dialog">
		<div class="modal-dialog">							
				<!-- Modal content-->
			<div class="modal-content">									
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h3 class="modal-title"><i class="fa fa-pencil-square-o"></i> Registrar avances por actividad...</h3>
				</div>									
				<div class="modal-body">
							<form id="formulario" METHOD='POST' ACTION="/guardar/avance" role="form">
								<input type='HIDDEN' name='txtOperacion' value=''>
								<input type='HIDDEN' name='txtCuenta' value='<?php echo $_SESSION["idCuentaActual"];?>'>						
								<input type='HIDDEN' name='txtPrograma' value='<?php echo $_SESSION["idProgramaActual"];?>'>	
								<input type='HIDDEN' name='txtID' value=''>
								<br>
								<div class="form-group">									
									<label class="col-xs-2 control-label">Auditoría</label>
									<div class="col-xs-10">
										<select class="form-control" id="txtAuditoria" name="txtAuditoria"   onchange="recuperarActividades();">
											<option value="">Seleccione</option>
										</select>
									</div>	
								</div>	
								<br>							
								<div class="form-group">											
									<label class="col-xs-2 control-label">Fase</label>
									<div class="col-xs-3">
										<select class="form-control" name="txtFase"  onchange="recuperarActividades();">
											<option value="" Selected>Seleccione...</option>
											<option value="PLANEACION">PLANEACIÓN</option>
											<option value="EJECUCION">EJECUCIÓN</option>
											<option value="INFORMES">ELABORACIÓN DE INFORMES</option>
										</select>
									</div>
							
									<label class="col-xs-2 control-label">Apartado</label>
									<div class="col-xs-5">
										<select class="form-control" name="txtApartado" id="txtApartado">
											<option value="">Seleccione...</option>
										</select>
									</div>																		
								</div>	
								<br>							
								<div class="form-group">											
									<label class="col-xs-2 control-label">Actividad</label>
									<div class="col-xs-10">
										<select class="form-control" name="txtActividad" id="txtActividad">
											<option value="">Seleccione...</option>
										</select>
									</div>																		
								</div>
								
								<br>
								<div class="form-group">								
									<label class="col-xs-2 control-label">Porcentaje</label>
									<div class="col-xs-1">
										<input type="number" min="0" max="100" value='0.00' step="5" class="form-control" name="txtPorcentaje"/>
									</div>							
									<label class="col-xs-3 control-label" style="margin: 0px -3% 0px -1% !important; width: 20%; text-align: left;"><input type="checkbox" id="chkTerminada" name="chkTerminada"/> Actividad Terminada</label>									
								
									<label class="col-xs-3 control-label">Fecha Inicio / Termino</label>
									<div class="col-xs-2">
										<input type="text"  class="form-control" id="FechaInicio" name="txtFechaInicio" style="margin: 0 1% 0 -10% !important;"/>
									</div>										
									<div class="col-xs-2">
										<input type="text" class="form-control" id="FechaFin" name="txtFechaFin" style="margin: 0 0 0 5% !important;"/>
									</div>																
								</div>	
								
								<br>
								<div class="form-group">																	
									<label class="col-xs-2 control-label">Auditor</label>
									<div class="col-xs-6">
										<select class="form-control" name="txtAuditor">
											<option value="">Seleccione...</option>
										</select>
									</div>
									<label class="col-xs-2 control-label">Estatus</label>
									<div class="col-xs-2">
										<select class="form-control" name="txtEstatus">
											<option value="ACTIVO" selected>ACTIVO</option>
											<option value="INACTIVO">INACTIVO</option>
										</select>
									</div>									
								</div>	
							<br>
							<div class="form-group">									
								<label class="col-xs-2 control-label">Observaciones</label>
								<div class="col-xs-10"><textarea class="form-control" rows="4" placeholder="Escriba aqui las observaciones(s)" id="txtObservaciones" name="txtObservaciones"></textarea></div>
							</div>
							<br>
							</form>
					<div class="clearfix"></div>
				</div>
				
				<div class="modal-footer">								
					<div class="pull-right">
						<button type="button" class="btn btn-primary active" 	id="btnGuardar"><i class="fa fa-floppy-o"></i> Guardar</button>
						<button type="button" class="btn btn-default active" 	id="btnCancelar"><i class="fa fa-back"></i> Cancelar</button>
					</div>
					<div class="clearfix"></div>
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


