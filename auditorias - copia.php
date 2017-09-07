<!DOCTYPE html>
<!-- saved from url=(0035)http://ashobiz.asia/mac52/macadmin/ -->
<html lang="en"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">  
  <meta charset="utf-8">
		<script type="text/javascript" src="js/canvasjs.min.js"></script>
		<script type='text/javascript' src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js?ver=3.1.2"></script> 
		<script type="text/javascript" src="js/genericas.js"></script>
  	<style type="text/css">		
		@media screen and (min-width: 768px) {
			#mapa_content {width:100%; height:150px;}
			#canvasJG, #canvasJD, #canvasDIP{height:175px; width:100%;}			
			#modalCronograma .modal-dialog  {width:70%;}
			#modalEquipo .modal-dialog  {width:90%;}
			#modalDocto .modal-dialog  {width:60%;}

			caption {padding: .2em .8em;border-bottom: 1px solid #fFF;background:#f4f4f4; font-weight: bold;}		
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
	
	function cajaResultados(tipo){
		if(tipo!="")
			document.all.divResumen.style.display='inline';	
		else 
			document.all.divResumen.style.display='none';	
	}
		
	
	function guardarAuditoria(){
		document.all.listasAuditoria.style.display='inline';
		document.all.capturaAuditoria.style.display='none';
	}
	
	function agregarAuditoria(){
		document.all.listasAuditoria.style.display='none';
		document.all.capturaAuditoria.style.display='inline';
	}
	
	
	function editarCronograma(){
		$('#modalCronograma').removeClass("invisible");
		$('#modalCronograma').modal('toggle');
		$('#modalCronograma').modal('show');
	}
	
	function recuperarTabla(lista, valor, tbl){
		//alert("Buscando Actividades: " + '/'+ lista + '/' + valor);
		$.ajax({
			type: 'GET', url: '/'+ lista + '/' + valor ,
			success: function(response) {
				var jsonData = JSON.parse(response);			
				//Vacia la lista
				tbl.innerHTML="";

				//Limpia array
				//intenciones = [];
				
				//Agregar renglones
				var renglon, columna;
				
				for (var i = 0; i < jsonData.datos.length; i++) {
					var dato = jsonData.datos[i];					
					renglon=document.createElement("TR");				
					renglon.innerHTML="<td>" + dato.fase + "</td><td>" + dato.actividad + "</td><td>" + dato.inicio + "</td><td>" + dato.fin  + "</td><td>" + dato.porcentaje + "</td><td>" + dato.prioridad + "</td>";
					renglon.onclick= function() {
						alert('En Construcción');
					};
					tbl.appendChild(renglon);					
				}				
			},
			error: function(xhr, textStatus, error){
				alert('function recuperarTabla ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
			}			
		});	
	}	


	function recupeAuditores(lista, valor, tbl){
		
			$.ajax({
			type: 'GET', url: '/'+ lista + '/' + valor ,
			success: function(response) {
				var jsonData = JSON.parse(response);
				//Vacia la lista
				tbl.innerHTML="";

				//Limpia array
				//intenciones = [];
				
				//Agregar renglones
				var renglon, columna, type;
				
				for (var i = 0; i < jsonData.datos.length; i++) {
					var dato = jsonData.datos[i];	
					

					//$("input:checkbox:checked").each(function(){
					
					renglon=document.createElement("TR");			
					renglon.innerHTML="<td>" + dato.nombre + "</td><td>" + dato.puesto + "</td>";
					//})	
					renglon.onclick= function() {
						alert('En Construcción');
					};
					tbl.appendChild(renglon);					
				}				
			},
			error: function(xhr, textStatus, error){
				alert('function recupeAuditores ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
			}			
		});	
	}	



	function recuperaAuditoria(id){
		
		$.ajax({
			type: 'GET', url: '/lstAuditoriaByID/' + id ,
			success: function(response) {
				var obj = JSON.parse(response);
				//alert(response);
				limpiarCamposAuditoria();
				document.all.txtOperacion.value='UPD';
				
				document.all.txtCuenta.value='' + obj.cuenta;
				document.all.txtPrograma.value='' + obj.programa;
				document.all.txtAuditoria.value='' + obj.auditoria;
								
				
				seleccionarElemento(document.all.txtArea, obj.area);
				seleccionarElemento(document.all.txtTipoAuditoria, obj.tipo);
				seleccionarElemento(document.all.txtSujeto, obj.sujeto);
				seleccionarElemento(document.all.txtObjeto, obj.objeto);
				recuperarListaSelected('lstObjetosBySujeto', obj.sujeto, document.all.txtObjeto, obj.objeto)
				
				
				document.all.txtObjetivo.value='' + obj.objetivo;
				recuperarTabla('tblActividadesByAuditoria', obj.auditoria, document.all.tablaActividades);
				document.all.listasAuditoria.style.display='none';
				document.all.capturaAuditoria.style.display='inline';

			
				recupeAuditores('auditoriasBYauditores', obj.auditoria, document.all.tablaAuditores);

//			
				recuperarLista('lstFasesActividad', document.all.txtFaseActividad);

				
				recuperarListaLigada('lstResponByauditor', obj.auditoria, document.all.txtResponsableActividad);

				recuperarListaLigada('lstActividaByPrevia', obj.auditoria, document.all.txtPreviaActividad);




				

				



		},

			error: function(xhr, textStatus, error){
				alert('function recuperaAuditoria()   -> statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error + ' Auditoría: ' + id);
			}			
		});		
	}	

	
	function limpiarCamposAuditoria(){
		document.all.txtAuditoria.value='';
		
		document.all.txtTipoAuditoria.selectedIndex=0;
		document.all.txtArea.selectedIndex=0;		
		document.all.txtSujeto.selectedIndex=0;
		document.all.txtObjeto.selectedIndex=0;		
		document.all.txtObjetivo.value='';
		//document.all.txtAlcance.value='';			
		//document.all.txtJustificacion.value='';
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
	  };
	  

	var ventana;
	
	function modalWin(sPagina) {
		var sDimensiones; 
		
		if (window.showModalDialog) {f
			sDimensiones= "dialogWidth:" + window.innerWidth + "px;dialogHeight:" + window.innerHeight + "px;";
			window.showModalDialog(sPagina,"Reporte",sDimensiones);
		} 
		else {
			sDimensiones= "width=" + window.innerWidth + ", height=" + window.innerHeight + ",location=no, titlebar=no, menubar=no,minimizable=no, resizable=no,  toolbar=no,directories=no,status=no,continued from previous linemenubar=no,scrollbars=no,resizable=no ,modal=yes";
			ventana = window.open(sPagina,'Reporte', sDimensiones);
			ventana.focus();
		}
	}
	
	
	
  

	function verificarDependencia(tipo){
		if(tipo=="SIMPLE")document.all.divActividadPrevia.style.display='none';
		if(tipo=="SERIADA")document.all.divActividadPrevia.style.display='inline';
	}
	  
function limpiarCamposActividad(){
		document.all.txtIDActividad.value='';
		document.all.txtDescripcionActividad.value='';
		document.all.txtPreviaActividad.selectedIndex=0;
		document.all.txtTipoActividad.selectedIndex=0;		
		document.all.txtFaseActividad.selectedIndex=0;		
		document.all.txtResponsableActividad.selectedIndex=0;		
		document.all.txtDiasActividad.value='';
		document.all.txtInicioActividad.value='';
		document.all.txtFinActividad.value='';			
		document.all.txtPorcentajeActividad.value='';
		document.all.txtNotasActividad.value='';				
	}
	
	function guardarActividad(){
		//sValor = document.all.txtDelegacionFiltro.options[document.all.txtDelegacionFiltro.selectedIndex].value;		
		var sValor="";
		var sOper=document.all.txtOperacion.value;
		var d = document.all;
		
		sValor = d.txtCuenta.value + '|' + d.txtPrograma.value + '|' + d.txtAuditoria.value + '|' +  d.txtIDActividad.value + '|' + d.txtTipoActividad.value;		
		sValor = sValor + '|' + d.txtFaseActividad.value + '|' + d.txtDescripcionActividad.value + '|' + d.txtPreviaActividad.value ;		
		sValor = sValor + '|' + d.txtInicioActividad.value + '|' + d.txtFinActividad.value + '|' + d.txtPorcentajeActividad.value;
		sValor = sValor + '|' + d.txtPrioridadActividad.value + '|' + d.txtImpactoActividad.value+ '|' + d.txtResponsableActividad.value;
		sValor = sValor + '|' + d.txtEstatusActividad.value + '|' + d.txtNotasActividad.value;

		alert(sValor);
		
		$.ajax({
			type: 'GET', url: '/guardar/auditoria/actividad/' + sOper + '/' + sValor,
			success: function(response) {
				//var obj = JSON.parse(response);
				alert("hola" + response);
				recuperarTabla('tblActividadesByAuditoria', d.txtAuditoria.value, document.all.tablaActividades);					
					alert("Los datos se guardaron correctamente. 5    " + d.txtAuditoria.value);

					document.all.divListaActividades.style.display='inline';
					document.all.divCapturaActividad.style.display='none';
					
					document.all.btnNuevaActividad.style.display='inline';
					document.all.btnCancelarActividad.style.display='none';
					
					document.all.btnGuardarActividad.style.display='none';
					document.all.btnCancelarCronograma.style.display='inline';	
				
			},
			error: function(xhr, textStatus, error){
				alert(' Error en function guardarActividad()\n\tstatusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error: ' + error );
				return false;
			}			
		});
	}
	
	function validarActividad(){
		if (document.all.txtTipoActividad.selectedIndex==0){
			alert("Debe seleccionar el TIPO DE ACTIVIDAD.");
			document.all.txtTipoActividad.focus();
			return false;
		}

		if (document.all.txtFaseActividad.selectedIndex==0){
			alert("Debe seleccionar la FASE.");
			document.all.txtFaseActividad.focus();
			return false;
		}		
		
		
		if (document.all.txtDescripcionActividad.value==''){
			alert("Debe capturar el campo ACTIVIDAD.");
			document.all.txtDescripcionActividad.focus();
			return false;
		}
		if (document.all.txtInicioActividad.value==''){
			alert("Debe capturar el campo FECHA DE INICIO.");
			document.all.txtInicioActividad.focus();
			return false;
		}			
		if (document.all.txtFinActividad.value==''){
			alert("Debe capturar el campo FECHA FIN.");
			document.all.txtFinActividad.focus();
			return false;
		}	
		if (document.all.txtPorcentajeActividad.value==''){
			alert("Debe capturar el campo PORCENTAJE.");
			document.all.txtPorcentajeActividad.focus();
			return false;
		}	

		if (document.all.txtPrioridadActividad.selectedIndex==0){
			alert("Debe seleccionar la PRIORIDAD.");
			document.all.txtPrioridadActividad.focus();
			return false;
		}		

		if (document.all.txtImpactoActividad.selectedIndex==0){
			alert("Debe seleccionar el IMPACTO.");
			document.all.txtImpactoActividad.focus();
			return false;
		}

		
		if (document.all.txtResponsableActividad.selectedIndex==0){
			alert("Debe seleccionar el RESPONSABLE.");
			document.all.txtResponsableActividad.focus();
			return false;
		}
		

		if (document.all.txtEstatusActividad.selectedIndex==0){
			alert("Debe seleccionar el ESTATUS.");
			document.all.txtEstatusActividad.focus();
			return false;
		}		

		return true;
	}
	
	
	$(document).ready(function(){		
		document.all.divActividadPrevia.style.display='inline';
		document.all.divActividadPrevia.style.display='none';
		
		recuperarLista('lstAreas', document.all.txtArea);
		recuperarLista('lstTiposAuditorias', document.all.txtTipoAuditoria);
		recuperarLista('lstSujetos', document.all.txtSujeto);

		

	
		$('#dp1').datepicker({format: "dd/mm/yyyy"});
		$('#dp2').datepicker({format: "dd/mm/yyyy"});
		$('#dp3').datepicker({format: "dd/mm/yyyy"});
		$('#dp4').datepicker({format: "dd/mm/yyyy"});
		$('#dp5').datepicker({format: "dd/mm/yyyy"});
		$('#dp6').datepicker({format: "dd/mm/yyyy"});
		
		$('#dp7').datepicker({format: "dd/mm/yyyy"});
		$('#dp8').datepicker({format: "dd/mm/yyyy"});
		
		$( "#btnEquipo" ).click(function() {
			$('#modalEquipo').removeClass("invisible");
			$('#modalEquipo').modal('toggle');
			$('#modalEquipo').modal('show');						
		});
		$( "#btnCancelarEquipo" ).click(function() {
			$('#modalEquipo').modal('hide');
		});
		$( "#btnGuardarEquipo" ).click(function() {
			alert("Guardando Equipo");
			$('#modalEquipo').modal('hide');
		});	
		

		$( "#btnLigarDocto" ).click(function() {		
			$( "#btnUpload" ).click(); 
		});
		
		
		
		
		

		$( "#btnAnexarDocto" ).click(function() {		
			$( "#btnUpload" ).click(); 
		});	


	$( "#btnNuevaActividad" ).click(function() {
		document.all.divListaActividades.style.display='none';
		document.all.divCapturaActividad.style.display='inline';
		
		document.all.txtOperacion.value='INS-ACT';
		
		document.all.btnNuevaActividad.style.display='none';
		document.all.btnGuardarActividad.style.display='inline';
		document.all.btnCancelarActividad.style.display='inline';
		
		//document.all.btnGuardarCronograma.style.display='none';
		document.all.btnCancelarCronograma.style.display='none';
		

		
	});

	$( "#btnGuardarActividad" ).click(function() {		
		if (validarActividad())
		{
			guardarActividad();
		
		}
	});
	
	$( "#btnCancelarActividad" ).click(function() {
		document.all.divListaActividades.style.display='inline';
		document.all.divCapturaActividad.style.display='none';
		
		document.all.btnNuevaActividad.style.display='inline';
		document.all.btnCancelarActividad.style.display='none';
		document.all.btnGuardarActividad.style.display='none';
		
		//document.all.btnGuardarCronograma.style.display='inline';
		document.all.btnCancelarCronograma.style.display='inline';
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
		

	
		$( "#btnCronograma" ).click(function() {
			$('#modalCronograma').removeClass("invisible");
			$('#modalCronograma').modal('toggle');
			$('#modalCronograma').modal('show');						
		});
		$( "#btnCancelarCronograma" ).click(function() {
			$('#modalCronograma').modal('hide');
		});
		
		//$( "#btnGuardarCronograma" ).click(function() {
		//	$('#modalCronograma').modal('hide');
		//});				

		$( "#btnRegresar" ).click(function() {
			document.all.listasAuditoria.style.display='inline';
			document.all.capturaAuditoria.style.display='none';		
		});	


		$("input[type=checkbox]:checked").each(function(){
			mycheck=input;
		alert($(this).val());
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
					<div class="col-xs-3"><h2>Progr. Espec. de Auditoria</h2></a></div>									
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


<!-- Main content starts -->

<div class="content">
  	<div class="panel panel-default">

			<div class="panel-body">
				<div class="row" id="listasAuditoria">
				<div class="col-md-12">
				
				  <div class="widget">
					<div class="widget-head">
					  <div class="pull-left"><h3><i class="fa fa-bars"></i> Lista de Auditorías</h3></div>
					  <div class="widget-icons pull-right">
						<a href="#" class="wminimize"><i class="fa fa-chevron-up"></i></a> 						
					  </div>  
					  <div class="clearfix"></div>
					</div>             
					<div class="widget-content">
							<div class="table-responsive" style="height: 350px; overflow: auto; overflow-x:hidden;">
								<table class="table table-striped table-bordered table-hover table-condensed">
									<thead>
										<tr><th>Auditoría</th><th>Ärea</th><th>Sujetos de Fiscalización</th><th>Objeto de Fiscalización</th><th>Tipo de Auditoría</th><th>% de Avance</th></tr>
									</thead>
										<tbody>										
											<?php foreach($datos as $key => $valor): ?>
											<tr onclick=<?php echo "javascript:recuperaAuditoria('" . $valor['auditoria'] . "');"; ?> style="width: 100%; font-size: xx-small">										  										  
											  <td><?php echo $valor['auditoria']; ?></td>
											  <td><?php echo $valor['area']; ?></td>
											  <td><?php echo $valor['sujeto']; ?></td>
											  <td><?php echo $valor['objeto']; ?></td>
											  <td><?php echo $valor['tipo']; ?></td>
											  <td><?php echo $valor['avances']; ?></td>
											</tr>
											<?php endforeach; ?>										
										</tbody>
								</table>
							</div>							
					</div>
					<div class="widget-foot"></div>
					</div>
				</div>
			  </div>
			  
  			<div class="row" id="capturaAuditoria" style="display:none; padding:0px; margin:0px;">			
				<div class="col-xs-12">				
					<div class="widget">
						<!-- Widget head -->
						<div class="widget-head">
						  <div class="pull-left"><h3><i class="fa fa-pencil-square-o"></i> Programa Específico de Auditoria</h3></div>
						  <div class="widget-icons pull-right"> 
							<button type="button" id="btnGuardarAuditoria"	class="btn btn-primary  btn-xs" style="display:none;"><i class="fa fa-floppy-o"></i> Guardar Auditoría</button>
							<button type="button" id="btnCronograma"		class="btn btn-warning  btn-xs"><i class="fa fa-calendar"></i> Cronograma de Trabajo...</button>
							<button type="button" id="btnEquipo"			class="btn btn-warning  btn-xs"><i class="fa fa-users"></i> Equipo de Trabajo...</button>
							<button type="button" id="btnImprimir" 			class="btn btn-default  btn-xs"><i class="fa fa-print"></i> Imprimir</button>
							<button type="button" id="btnRegresar" 			class="btn btn-default  btn-xs"><i class="fa fa-undo"></i> Regresar</button>
							
						  </div>  
						  <div class="clearfix"></div>
						</div>              

						<!-- Widget content -->
						<div class="widget-content">						
							<br>
							<div class="col-xs-6">
								<div class="form-group">									
									<label class="col-xs-2 control-label">No. Auditoría</label>
									<div class="col-xs-3"><input type="text" class="form-control" name="txtAuditoria" readonly/></div>													
									<label class="col-xs-2 control-label">Tipo</label>
									<div class="col-xs-5">
										<select class="form-control" name="txtTipoAuditoria" readonly>
											<option value="">Seleccione...</option>
										</select>
									</div>								
								</div>									
								<br>
								<div class="form-group">
									<label class="col-xs-2 control-label">Área</label>
									<div class="col-xs-10">
										<select class="form-control" name="txtArea" readonly>
											<option value="">Seleccione...</option>											
										</select>
									</div>
								</div>							
								<br>
								<div class="form-group">
									<label class="col-xs-2 control-label">Sujeto</label>
									<div class="col-xs-10">
										<select class="form-control" name="txtSujeto" onChange="javascript:recuperarListaLigada('lstObjetosBySujeto', this.value, document.all.txtObjeto);" readonly>
											<option value="">Seleccione...</option>											
										</select>
									</div>								
								</div>
								<br>
								<div class="form-group">
									<label class="col-xs-2 control-label">Objeto</label>
									<div class="col-xs-10">
										<select class="form-control" name="txtObjeto" readonly>
											<option value="">Seleccione...</option>											
										</select>
									</div>								
								</div>
								<br>
								<div class="form-group">
									<label class="col-xs-2 control-label"> Inicio</label>
									<div class="col-xs-4">
										<input type="date" class="form-control"  name="txtFechaInicio" readonly/>
									</div>
									<label class="col-xs-2 control-label">Termino</label>
									<div class="col-xs-4">
										<input type="date" class="form-control" name="txtFechaFin" readonly/>
									</div>
								</div>	
							<br>
							<div class="form-group">									
								<label class="col-xs-2 control-label">Etapa</label>
								<div class="col-xs-10">
									<select class="form-control" name="txtNivel" readonly>
										<option value="">Seleccione</option>
										<option value="" >DEFINICIÓN</option>
										<option value="" selected>EJECUCIÓN</option>
										<option value="">CONCLUIDA</option>
									</select>
								</div>									
							</div>							
							<br>
							<div class="form-group">									
								<label class="col-xs-2 control-label">Objetivo(s)</label>
								<div class="col-xs-10"><textarea class="form-control" rows="2" placeholder="Objetivo(s)" id="txtObjetivo" name="txtObjetivo" readonly></textarea></div>
							</div>
							<br>
							<div class="form-group">									
								<label class="col-xs-2 control-label">Alcance(s)</label>
								<div class="col-xs-10"><textarea class="form-control" rows="2" placeholder="Alcance(s)" id="txtAlcance" name="txtAlcance" readonly></textarea></div>
							</div>
							<br>
							<div class="form-group">									
								<label class="col-xs-2 control-label">Justificación</label>
								<div class="col-xs-10"><textarea class="form-control" rows="2" placeholder="Justificación" id="txtJustificacion" name="txtJustificacion" readonly></textarea></div>
							</div>							
							<div class="clearfix"></div>
							
							<div class="form-group">									
								<label class="col-xs-2 control-label">Tipo de Obs.</label>
								<div class="col-xs-10">
									<select class="form-control" name="txtTipoObs" onchange="cajaResultados(this.value);">
										<option value="">Seleccione</option>
										<option value="" Selected>NINGUNA OBSERVACION</option>											
										<option value="SIMPLE">OBSERVACIÓN SIMPLE</option>											
										<option value="GRAVE">PROBABLE FALTA GRAVE</option>											
									</select>
								</div>	
							</div>
							<br>							
							<div class="form-group" id="divResumen" style="display:none;">									
								<label class="col-xs-2 control-label">Resumen</label>
								<div class="col-xs-10"><textarea class="form-control" rows="5" placeholder="Escriba aqui el resumen o resultado obtenido..."></textarea></div>	
							</div>
							<div class="clearfix"></div>
							</div>
							<div class="col-xs-6">
								<div class="form-group">									
									<div class="col-xs-12">
										<table class="table table-striped table-bordered table-hover table-condensed">
											<caption>Cronograma de Trabajo por Fase</caption>
											<thead>
											<tr><th>Fase</th><th>Fecha Inicio</th><th>Fecha Fin</th><th>Cantidad de Días</th></tr>
											</thead>
											<tbody id="tablaFases">									  
												<tr onclick=<?php echo "editarCronograma();"; ?> style="width: 100%; font-size: xx-small">
													<td>1.-PLANEACIÓN</td><td>05/08/2014</td><td>15/09/2014</td><td>30</td>
												</tr>										
												<tr onclick=<?php echo "editarCronograma();"; ?> style="width: 100%; font-size: xx-small">
													<td>2.-EJECUCIÓN</td><td>01/01/2014</td><td>31/03/2016</td><td>52</td>
												</tr>										
												<tr onclick=<?php echo "editarCronograma();"; ?> style="width: 100%; font-size: xx-small">
													<td>3.-ELABORACIÓN DE INFORMES</td><td>02/12/2014</td><td>14/01/2014</td><td>20</td>
												</tr>										
											</tbody>
										</table>
									</div>									
								</div>
								<br>
								<div class="col-xs-12">	
										<br>
										<table class="table table-striped table-bordered table-hover table-condensed">
											<caption>Equipo de Trabajo</caption>
											<thead>
											<tr><th>Asignar</th><th>Nombre</th><th>Puesto</th><th>Lider</th></tr>
											</thead>
											<tbody id="tablaEquipo">	
												<tr style="width: 100%; font-size: xx-small"><td>Lider de Proyecto</td><td>
												Lic. Ricardo Santana Rodríguez</td><td>Subdirector de Auditoría</td>
												</tr>										
												<tr style="width: 100%; font-size: xx-small"><td></td><td>
												Lic. Humberto Jesús Gonzalez Maldonado</td><td>Jefe de Unidad Departamental de Auditoría</td>
												</tr>										
												<tr style="width: 100%; font-size: xx-small"><td></td>
													<td>Lic. Jorge Margarito Ugalde Serrano</td><td>Auditor de Carrera</td>
												</tr>										
											</tbody>
										</table>
										<br>								
								<div class="col-md-12">
									<ul class="nav nav-tabs">																		
										<li class="active"><a href="#tab-papeles" data-toggle="tab"> Papeles de Trabajo <i class="fa"></i></a></li>
										<li><a href="#tab-acciones" data-toggle="tab"> Acciones y recomendaciones <i class="fa"></i></a></li>
										<li><a href="#tab-documentos" data-toggle="tab"> Documentos <i class="fa"></i></a></li>
										<li><a href="#tab-plan" data-toggle="tab"> Avances por Actividad <i class="fa"></i></a></li>
									</ul>								
									<div class="tab-content">
										<div class="tab-pane" id="tab-documentos">
											<div class="table-responsive" style="height: 150px; overflow: auto; overflow-x:hidden;">
												<table class="table table-striped table-bordered table-hover">
												  <thead>
													<tr><th>No.</th><th>Tipo</th><th>Fecha Docto. </th><th>Asunto</th><th>Origen</th><th>Destino</th><th>Estatus</th></tr>
												  </thead>
												  <tbody id="tablaDoctos">								  
													<tr style="width: 100%; font-size: xx-small">
														<td>ASCM-2016/0035</td><td>OFICIO</td><td>16/01/2016</td></td><td>CÉDULA DE POTENCIALES</td><td>JURÍDICO</td><td>DIRECCIÓN GRAL DE AUDITORIAS "B"</td><td>RECIBIDO</td>
													</tr>
													<tr style="width: 100%; font-size: xx-small">
														<td>ASCM-2016/0001</td><td>NOTA</td><td>16/01/2016</td><td>SOLICTUD DE CREDITO</td><td>DIRECCIÓN GRAL DE AUDITORIAS "B"</td><td>JURÍDICO</td><td>ENVIADO</td>
													</tr>
													<tr style="width: 100%; font-size: xx-small">
														<td>IEDF-2015/0001</td><td>OFICIO</td><td>16/01/2016</td><td>SOLICTUD DE INFORMACIÓN</td><td>IEDF</td><td>ASCM</td><td>RECIBIDO</td>
													</tr>												
												  </tbody>
												</table>											
											</div>
											<br>
											<button type="button" class="btn btn-primary  btn-xs" 	id="btnNuevoDocto"><i class="fa fa-file-text-o"></i> Nuevo Documento...</button>
											<button type="button" class="btn btn-default  btn-xs" 	id="btnLigarDocto"><i class="fa fa-link"></i> Ligar Documento</button>
											<input type="file" name="pic" accept="image/*" style="display:none;" id="btnUpload">

											
										</div>
										<div class="tab-pane active" id="tab-papeles">
											<div class="table-responsive" style="height: 150px; overflow: auto; overflow-x:hidden;">
												<table class="table table-striped table-bordered table-hover">
												  <thead>
													<tr><th>No.</th><th>Tipo</th><th>Descripción</th><th>Auditor</th><th>Fecha</th><th>Estatus</th><th>Anexo(s)</th></tr>
												  </thead>
												  <tbody id="tablaPapeles">								  
													<tr style="width: 100%; font-size: xx-small">
														<td>PAPEL-0001</td><td>CÉDULA VERIFICACIÓN NORMATIVA-TÉCNICA</td><td>Cédula verificación de normatividad  Estandar ISO 2001</td><td>MARTIN GONZALEZ CRUZ</td><td>01/02/2016</td><td>PROCESO</td><td><img onclick="modalWin('mostrarPDF.php');" src="img/xls.gif"></td>
													</tr>
													<tr style="width: 100%; font-size: xx-small">
														<td>PAPEL-0002</td><td>CÉDULA VERIFICACIÓN NORMATIVA-TÉCNICA</td><td>Cédula verificación de normatividad  Estandar ISO 2001</td><td>MARTIN GONZALEZ CRUZ</td><td>01/02/2016</td><td>PROCESO</td><td><img onclick="modalWin('mostrarPDF.php');" src="img/xls.gif"></td>
													</tr>

													<tr style="width: 100%; font-size: xx-small">
														<td>PAPEL-0003</td><td>CÉDULA VERIFICACIÓN NORMATIVA-TÉCNICA</td><td>Cédula verificación de normatividad  Estandar ISO 2001</td><td>MARTIN GONZALEZ CRUZ</td><td>01/02/2016</td><td>PROCESO</td><td><img onclick="modalWin('mostrarPDF.php');" src="img/xls.gif"></td>
													</tr>
													
												  </tbody>
												</table>											
											</div>
										</div>
										<div class="tab-pane " id="tab-acciones">
											<div class="table-responsive" style="height: 150px; overflow: auto; overflow-x:hidden;">
												<table class="table table-striped table-bordered table-hover">
													<thead>
													<tr><th>No.</th><th>Tipo de Acción</th><th>Cédula de Potenciales</th><th>Dictámen Técnico</th><th>Estatus</th></tr>
													</thead>
													<tbody id="tablaAcciones">										  
														<tr onclick=<?php echo "editarResultado('RESULT-0056');"; ?> style="width: 100%; font-size: xx-small">
															<td>ACC-00345</td><td>RECOMENDACIÓN</td><td>DGL-00561/14</td><td>DGL-00565/14</td><td>CUMPLIDA</td>
														</tr>
														<tr onclick=<?php echo "editarResultado('RESULT-0056');"; ?> style="width: 100%; font-size: xx-small">
															<td>ACC-00346</td><td>OBSERVACIÓN</td><td>DGL-00611/14</td><td>DGL-00565/14</td><td>CUMPLIDA</td>
														</tr>
														<tr onclick=<?php echo "editarResultado('RESULT-0056');"; ?> style="width: 100%; font-size: xx-small">
															<td>ACC-00347</td><td>SANCIÓN</td><td>DGL-00501/14</td><td>DGL-00565/14</td><td>CUMPLIDA</td>
														</tr>
														<tr onclick=<?php echo "editarResultado('RESULT-0056');"; ?> style="width: 100%; font-size: xx-small">
															<td>ACC-00348</td><td>RECOMENDACIÓN</td><td>DGL-00461/14</td><td>DGL-00565/14</td><td>CUMPLIDA</td>
														</tr>									
													</tbody>
												</table>
											</div>									
										</div>
										<div class="tab-pane" id="tab-plan">
								<div class="table-responsive" style="height: 500px; overflow: auto; overflow-x:hidden;">
									<table class="table table-striped table-bordered table-hover">
									  <thead>
										<tr><th>Id</th><th>Fecha</th><th>Porcentaje</th><th>Estatus</th><th>Anexo(s)</th></tr>
									  </thead>
									  <tbody id="tablaAvances">										  
											<tr style="width: 100%; font-size: xx-small">
												<td>AV-0023</td><td>11/03/2016</td><td>100%</td><td>TERMINADA</td><td><img src="img/xls.gif"></td>
											</tr>
											<tr style="width: 100%; font-size: xx-small">
												<td>AV-0022</td><td>04/03/2016</td><td>90%</td><td>PROCESO</td><td><img src="img/pdf.gif"></td>
											</tr>
											<tr style="width: 100%; font-size: xx-small">
												<td>AV-0021</td><td>24/02/2016</td><td>70%</td><td>PROCESO</td><td><img src="img/pdf.gif"></td>
											</tr>
											<tr style="width: 100%; font-size: xx-small">
												<td>AV-0020</td><td>10/02/2016</td><td>55%</td><td>PROCESO</td><td><img src="img/xls.gif"></td>
											</tr>
											
											<tr style="width: 100%; font-size: xx-small">
												<td>AV-0019</td><td>01/02/2016</td><td>35%</td><td>PROCESO</td><td><img src="img/xls.gif"></td>
											</tr>

											<tr style="width: 100%; font-size: xx-small">
												<td>AV-0018</td><td>15/01/2016</td><td>15%</td><td>PROCESO</td><td><img src="img/xls.gif"></td>
											</tr>

											<tr style="width: 100%; font-size: xx-small">
												<td>AV-0017</td><td>10/01/2016</td><td>5%</td><td>PROCESO</td><td><img src="img/xls.gif"></td>
											</tr>																							
									  </tbody>
									</table>
								</div>
											<br>
										</div>										
									</div>
								</div>	
							
							</div>																									
							<div class="clearfix"></div>
							<br>								
							
														
							<div class="clearfix"></div>
						</div>
							<div class="clearfix"></div>
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
			</div>
		</div>
   <div class="clearfix"></div>
</div>
<!-- Content ends -->

<form id="formulario" METHOD='POST' role="form">
	<input type='HIDDEN' name='txtValores' value=''>
	<input type='HIDDEN' name='txtOperacion' value=''>
	<input type='HIDDEN' name='txtCuenta' value=''>						
	<input type='HIDDEN' name='txtPrograma' value=''>
	<input type='HIDDEN' name='txtNombre' value=''>
	<input type='HIDDEN' name='txtPuesto' value=''>
	
	<div id="modalCronograma" class="modal fade" role="dialog">
		<div class="modal-dialog">					
				<!-- Modal content-->
			<div class="modal-content">									
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h2 class="modal-title"><i class="fa fa-calendar"></i> Cronograma de Trabajo...</h2>
				</div>									
				<div class="modal-body">				
					<div class="table-responsive" style="height: 150px; overflow: auto; overflow-x:hidden;" id="divListaActividades">
						<table class="table table-striped table-bordered table-hover">
						  <thead>
							<tr><th>Fase</th><th>Actividad</th><th>Fecha Inicio</th><th>Fecha Fin</th><th>Porcentaje</th><th>Prioridad</th></tr>
						  </thead>
						  <tbody id="tablaActividades">								  							
							</tbody>
						</table>											
					</div>
					<div id="divCapturaActividad" style="display:none;">
						<div class="col-xs-12">
							<div class="form-group">
								<label class="col-xs-2 control-label">ID</label>
								<div class="col-xs-2">
									<input type="text" class="form-control" name="txtIDActividad" readonly/>
								</div>

								<label class="col-xs-2 control-label">Tipo de Actividad</label>
								<div class="col-xs-2">
									<select class="form-control" name="txtTipoActividad" id="txtTipoActividad" onchange="verificarDependencia(this.value);">
										<option value="">Seleccione...</option>									
										<option value="SIMPLE" Selected>SIMPLE</option>
										<option value="SERIADA">SERIADA</option>
									</select>
								</div>

								<label class="col-xs-2 control-label">Fase</label>
								<div class="col-xs-2">
									<select class="form-control" name="txtFaseActividad" onChange="javascript:recuperarListaLigada('lstFaseByDescripcion', this.value, document.all.txtDescripcionActividad);">
										<option value="" Selected>Seleccione...</option>
									</select>
								</div>

						</div>						
						
						<br>								
							<div class="form-group">
								<label class="col-xs-2 control-label">Actividad</label>
							 	<div class="col-xs-10">						
									<select class="form-control" name="txtDescripcionActividad">
										<option value="" Selected>Seleccione...</option>
									</select>																		
								</div>
							</div>
						<br>	

							<div class="form-group" id="divActividadPrevia" style="display:'inline';">							
								<label class="col-xs-2 control-label">Actividad Previa</label>
								<div class="col-xs-10">
									<select class="form-control" name="txtPreviaActividad"  id="txtPreviaActividad">
										<option value="">Seleccione...</option>
										
									</select>
								</div>
								<br>
								<br>
							</div>						
																								
							<div class="form-group">								
								<label class="col-xs-2 control-label">Fecha Inicio</label>
								<div class="col-xs-2">
									<input type="date" class="form-control" name="txtInicioActividad"/>
								</div>	
								<label class="col-xs-2 control-label">Fecha Término</label>
								<div class="col-xs-2">
									<input type="date" class="form-control" name="txtFinActividad"/>
								</div>		
								<label class="col-xs-2 control-label">Dias Efectivos</label>
								<div class="col-xs-2">
									<input type="text" class="form-control" name="txtDiasActividad" readonly/>
								</div>															
							</div>						
							
							<br>
							<div class="form-group">								
								<label class="col-xs-2 control-label">Porcentaje</label>
								<div class="col-xs-2">
									<input type="text" class="form-control" name="txtPorcentajeActividad"/>
								</div>	
								<label class="col-xs-2 control-label">Prioridad</label>
								<div class="col-xs-2">
									<select class="form-control" name="txtPrioridadActividad">
										<option value="">Seleccione...</option>
										<option value="BAJA">BAJA</option>
										<option value="MEDIA" SELECTED>MEDIA</option>
										<option value="ALTA">ALTA</option>
									</select>
								</div>															
								<label class="col-xs-2 control-label">Impacto</label>
								<div class="col-xs-2">
									<select class="form-control" name="txtImpactoActividad">
										<option value="">Seleccione...</option>
										<option value="BAJO">BAJO</option>
										<option value="MEDIO" SELECTED>MEDIO</option>
										<option value="ALTO">ALTO</option>
									</select>
								</div>								
							</div>	    
							<br>							
							<div class="form-group">
								<label class="col-xs-2 control-label">Responsable</label>
								<div class="col-xs-6">
									<select class="form-control" name="txtResponsableActividad">
										<option value="" selected>Seleccione...</option>
									</select>
								</div>																																											
								<label class="col-xs-2 control-label">Estatus</label>
								<div class="col-xs-2">
									<select class="form-control" name="txtEstatusActividad">
										<option value="">Seleccione...</option>
										<option value="ACTIVO" SELECTED>ACTIVO</option>
										<option value="INACTIVO">INACTIVO</option>
									</select>
								</div>								
							</div>	
							<br>
							<div class="form-group">								
								<label class="col-xs-2 control-label">Notas</label>
								<div class="col-xs-10"><textarea class="form-control" rows="3" placeholder="Nota(s)" id="txtNotasActividad" name="txtNotasActividad"></textarea></div>							
							</div>	
							<br>
						</div>										
					</div>
					<div class="clearfix"></div>
				</div>				
				<div class="modal-footer">
					<button type="button" class="btn btn-primary  btn-xs" 	id="btnNuevaActividad"><i class="fa fa-file-text-o"></i> Nueva Actividad...</button>
					<button  type="button" class="btn btn-primary btn-xs active" id="btnGuardarActividad" 		style="display:none;"><i class="fa fa-undo"></i> Guardar Actividad</button>	
					<button  type="button" class="btn btn-default btn-xs" id="btnCancelarActividad" 		style="display:none;"><i class="fa fa-undo"></i> Cancelar Actividad</button>						
					<button  type="button" class="btn btn-default btn-xs" id="btnCancelarCronograma"><i class="fa fa-undo"></i> Cancelar</button>	
				</div>
			</div>		
		</div>
	</div>


	<div id="modalEquipo" class="modal fade" role="dialog">
		<div class="modal-dialog">							
				<!-- Modal content-->
			<div class="modal-content">									
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h2 class="modal-title"><i class="fa fa-users"></i> Equipo de Trabajo...</h2>
				</div>									
				<div class="modal-body" style="height: 350px; overflow: auto; overflow-x:hidden;">
					<table class="table table-striped table-bordered table-hover table-condensed">
						<caption>Lista de Auditores</caption>
						<thead>
						<tr><th>Nombre<th>Puesto</th><th>Lider</th></tr>
						</thead>
						<tbody id="tablaAuditores">
							</tbody>
					</table>
					<div class="clearfix"></div>
				</div>
				
				<div class="modal-footer">
					<button  type="button" class="btn btn-primary btn-xs active" 	id="btnGuardarEquipo" 		style="display:inline;"><i class="fa fa-floppy-o"></i> Guardar</button>	
					<button  type="button" class="btn btn-default btn-xs" 			id="btnCancelarEquipo" 		style="display:inline;"><i class="fa fa-undo"></i> Cancelar</button>	
				</div>
			</div>		
		</div>
	</div>

	<div id="modalDocto" class="modal fade" role="dialog">
		<div class="modal-dialog">
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
		</div>
	</div>
</form>

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