<!DOCTYPE html>
<html lang="en"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">  
	<meta charset="utf-8">
	<script type="text/javascript" src="js/canvasjs.min.js"></script>
	<script type='text/javascript' src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js?ver=3.1.2"></script> 
	<script type="text/javascript" src="js/genericas.js"></script>
	
  	<style type="text/css">		
		@media screen and (min-width: 768px) {
			#mapa_content {width:100%; height:150px;}
			#canvasJG, #canvasJD, #canvasDIP{height:175px; width:100%;}			
			#modalObjetos .modal-dialog  {width:80%;}
			.auditor{background:#f4f4f4; font-size:7pt; padding:2px; display:inline; margin:2px; border:1px black solid;}
			label {text-align:right;}	
			caption {padding: .2em .8em;border-bottom: 1px solid #fFF;background:#f4f4f4; font-weight: bold;}					
		}
	</style>
  
  <script type="text/javascript"> 
	var mapa;
	var nZoom=10;
	
	
	var ventana;	
	function modalWin(sPagina) {
		var sDimensiones; 
		
		if (window.showModalDialog) {
			sDimensiones= "dialogWidth:" + window.innerWidth + "px;dialogHeight:" + window.innerHeight + "px;";
			window.showModalDialog(sPagina,"Reporte",sDimensiones);
		} 
		else {
			sDimensiones= "width=" + window.innerWidth + ", height=" + window.innerHeight + ",location=no, titlebar=no, menubar=no,minimizable=no, resizable=no,  toolbar=no,directories=no,status=no,continued from previous linemenubar=no,scrollbars=no,resizable=no ,modal=yes";
			ventana = window.open(sPagina,'Reporte', sDimensiones);
			ventana.focus();
		}
	}
	
	
	function recuperarTabla(lista, valor, tbl){
		//alert("Buscando Actividades: " + '/'+ lista + '/' + valor);
		$.ajax({
			type: 'GET', url: '/'+ lista + '/' + valor ,
			success: function(response) {
				var jsonData = JSON.parse(response);			
				alert("Registros: " + jsonData.datos.length);
					
				//Vacia la lista
				tbl.innerHTML="";

				//Agregar renglones
				var renglon, columna;				
				for (var i = 0; i < jsonData.datos.length; i++) {
					var dato = jsonData.datos[i];					
					renglon=document.createElement("TR");				
					renglon.innerHTML="<td><input type='checkbox'></td><td>" + dato.funcion + "</td><td>" + dato.subfuncion + "</td><td>" + dato.actividad + "</td><td>" + dato.capitulo  + "</td><td>" + dato.partida + "</td>";
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
		
	
	function agregarAuditoria(){
		document.all.listasAuditoria.style.display='none';
		document.all.capturaAuditoria.style.display='inline';
		document.all.txtOperacion.value='INS';		
		limpiarCamposAuditoria();
	}
	
	function recuperaAuditoria(id){
		//alert("Recuperando: " + id);
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
				//seleccionarElemento(document.all.txtObjeto, obj.objeto);
				recuperarListaSelected('lstObjetosBySujeto', obj.sujeto, document.all.txtObjeto, obj.objeto)
				
				
				document.all.txtObjetivo.value='' + obj.objetivo;
				document.all.txtAlcance.value='' + obj.alcance;			
				document.all.txtJustificacion.value='' + obj.justificacion;

				document.all.btnGuardar.style.display='inline';
				document.all.txtOperacion.value="UPD";
								
				document.all.listasAuditoria.style.display='none';
				document.all.capturaAuditoria.style.display='inline';
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
		//document.all.txtObjeto.selectedIndex=0;		
		document.all.txtObjetivo.value='';
		document.all.txtAlcance.value='';			
		document.all.txtJustificacion.value='';
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
	
		inicializar();		
	  };

	  function validarAuditoria(){
		  
		if (document.all.txtTipoAuditoria.selectedIndex==0){
			alert("Debe seleccionar el TIPO de la Auditoría.");
			document.all.txtTipoAuditoria.focus();
			return false;
		}	

		if (document.all.txtArea.selectedIndex==0){
			alert("Debe seleccionar el AREA de la Auditoría.");
			document.all.txtArea.focus();
			return false;
		}	

		if (document.all.txtSujeto.selectedIndex==0){
			alert("Debe seleccionar el SUJETO de la Auditoría.");
			document.all.txtSujeto.focus();
			return false;
		}			
		
		if (document.all.txtObjetivo.value==''){
			alert("Debe capturar el campo OBJETIVOS de la auditoría.");
			document.all.txtObjetivo.focus();
			return false;
		}	
		if (document.all.txtAlcance.value==''){
			alert("Debe capturar el campo ALCANCE de la auditoría.");
			document.all.txtAlcance.focus();
			return false;
		}			
		if (document.all.txtJustificacion.value==''){
			alert("Debe capturar el campo  JUSTIFICACIÓN de la auditoría.");
			document.all.txtJustificacion.focus();
			return false;
		}			
		
		return true;		  
	  }
	
	
	$(document).ready(function(){
		recuperarLista('lstAreas', document.all.txtArea);
		recuperarLista('lstTiposAuditorias', document.all.txtTipoAuditoria);
		
		recuperarLista('lstSectores', document.all.txtSector);
		//recuperarLista('lstSujetos', document.all.txtSujeto);
		
	$("#txtSubsector" ).change(function() {
		var sector;
		var subsector
		
		if(document.all.txtSector.selectedIndex>0 && document.all.txtSubsector.selectedIndex>0){			
			sector = document.all.txtSector.options[document.all.txtSector.selectedIndex].value;
			subsector = document.all.txtSubsector.options[document.all.txtSubsector.selectedIndex].value;
			var cadena = 'lstUnidadesBySectorSubsector/' + sector ;
			recuperarListaLigada(cadena, subsector, document.all.txtSujeto);
		}		
	});


	
		
	
	$( "#btnGuardar" ).click(function() {
		if (validarAuditoria()){
			document.all.formulario.submit();
		}

		document.all.listasAuditoria.style.display='inline';
		document.all.capturaAuditoria.style.display='none';
	});
		
		$( "#btnCancelar" ).click(function() {
			document.all.listasAuditoria.style.display='inline';
			document.all.capturaAuditoria.style.display='none';
		});
		
		$( "#btnLigarDocto" ).click(function() {		
			$( "#btnUpload" ).click(); 
		});
		
		$( "#btnNuevoDocto" ).click(function() {		
			$( "#btnUpload" ).click(); 
		});	

		$( "#btnVerObjeto" ).click(function() {		
			var nEntidadSeleccionada= document.all.txtSujeto.options[document.all.txtSujeto.selectedIndex].value;
			var sector;
			var subsector;
			
			if(document.all.txtSector.selectedIndex>0 && document.all.txtSubsector.selectedIndex>0){			
				$('#modalObjetos').removeClass("invisible");
				$('#modalObjetos').modal('toggle');
				$('#modalObjetos').modal('show');						
				sector = document.all.txtSector.options[document.all.txtSector.selectedIndex].value;
				subsector = document.all.txtSubsector.options[document.all.txtSubsector.selectedIndex].value;
				unidad = document.all.txtSujeto.options[document.all.txtSubsector.selectedIndex].value;
				var cadena = 'tblGastoByUnidad/' + sector + '/' + subsector;			
				recuperarTabla(cadena, nEntidadSeleccionada, document.all.tblGasto);
			
			}else{
				alert("Debe seleccionar un SECTOR, SUBSECTOR Y SUJETO DE FISCALIZACIÓN.");
			}
			
		});
		$( "#btnCancelarObjeto" ).click(function() {
			$('#modalObjetos').modal('hide');			
		});		
		
		$( "#txtObjetivoEdit" ).click(function(){
			$('#modalTextoLargo').modal('show');
			document.all.txtTextoLargo.value = document.all.txtObjetivo.value;
			sEditando="O";
		});	
		
		$( "#txtAlcanceEdit" ).click(function(){
			$('#modalTextoLargo').modal('show');
			document.all.txtTextoLargo.value = document.all.txtAlcance.value;
			sEditando="A";
		});					
		$( "#txtJustificacionEdit" ).click(function(){
			$('#modalTextoLargo').modal('show');
			document.all.txtTextoLargo.value = document.all.txtJustificacion.value;
			sEditando="J";
		});				
		
		$( "#btnCancelarTexto" ).click(function() {
				$('#modalTextoLargo').modal('hide');				
			});				

		$( "#btnGuardarTexto" ).click(function() {
				$('#modalTextoLargo').modal('hide');				
				if(sEditando=="O") document.all.txtObjetivo.value=document.all.txtTextoLargo.value;
				if(sEditando=="A") document.all.txtAlcance.value=document.all.txtTextoLargo.value;
				if(sEditando=="J") document.all.txtJustificacion.value=document.all.txtTextoLargo.value;
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
					<div class="col-xs-3"><h2>Programa General de Auditorías</h2></a></div>									
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
				<div class="col-md-4">
					<div class="col-md-12">
						<div id="canvasJG" ></div>
					</div>
					<div class="col-md-12">
						<table class="table table-striped table-hover table-responsive">
						  <thead>
							<tr><th>Unidad Admiva.</th><th>Financiera</th><th>Obra</th><th>Desempeño</th><th>Suma</th></tr>
						  </thead>
						  <tbody >									  
								<tr style="width: 100%; font-size: xx-small">
									<td>DIRECCIÓN GENERAL DE AUDITORÍA AL SECTOR CENTRAL </td><td>0</td><td>0</td><td>0</td><td>0</td>
								</tr>
								<tr  style="width: 100%; font-size: xx-small">
									<td>DIRECCIÓN GENERAL DE AUDITORÍA A ENTIDADES PÚBLICAS Y ÓRGANOS AUTÓNOMOS </td><td>0</td><td>0</td><td>0</td><td>0</td>
								</tr>
								<tr style="width: 100%; font-size: xx-small">
									<td>DIRECCIÓN GENERAL DE AUDITORÍA A OBRA PÚBLICA Y SU EQUIPAMIENTO </td><td>0</td><td>0</td><td>0</td><td>0</td>
								</tr>
								<tr  style="width: 100%; font-size: xx-small">
									<td>DIRECCIÓN GENERAL DE AUDITORÍA PROGRAMÁTICA-PRESUPUESTAL Y DE DESEMPEÑO </td><td>0</td><td>0</td><td>0</td><td>0</td>
								</tr>
								<tr  style="width: 100%; font-size: xx-small">
									<td>TOTAL</td><td>83</td><td>0</td><td>0</td><td>83</td>
								</tr>
						  </tbody>
						</table>
					</div>
				</div>				

				<div class="col-md-8">
					<div class="widget">
						<div class="widget-head">
							<i class="fa fa-search"></i> Programa General de Auditorías
							<div class="clearfix"></div>
						</div>             
						<div class="widget-content">
							<div class="table-responsive" style="height: 350px; overflow: auto; overflow-x:hidden;">
								<table class="table table-striped table-bordered table-hover table-condensed">
									<thead>
										<tr><th>Auditoría</th><th>Ärea</th><th>Sujetos de Fiscalización</th><th>Objeto de Fiscalización</th><th>Tipo de Auditoría</th></tr>
									</thead>
										<tbody>																			
											<?php foreach($datos as $key => $valor): ?>
											<tr onclick=<?php echo "javascript:recuperaAuditoria('" . $valor['auditoria'] . "');"; ?> style="width: 100%; font-size: xx-small">										  										  
											  <td><?php echo $valor['auditoria']; ?></td>
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
						<div class="widget-foot">
						<button onclick="agregarAuditoria();" type="button" class="btn btn-primary  btn-xs"><i class="fa fa-search"></i> Agregar Auditoría...</button>
						<button type="button" class="btn btn-warning  btn-xs"><i class="fa fa-external-link"></i> Enviar PGA  ...</button>
						</div>
					</div>
				</div>
			</div>
		  
			<div class="row" id="capturaAuditoria" style="display:none; padding:0px; margin:0px;">
				<form id="formulario" METHOD='POST' action='/guardar/auditoria' role="form">
					<input type='HIDDEN' name='txtOperacion' value=''>
					<input type='HIDDEN' name='txtCuenta' value=''>						
					<input type='HIDDEN' name='txtPrograma' value=''>
					
					
					<div class="col-md-12">				
						<div class="widget">
							<!-- Widget head -->
							<div class="widget-head">
							  <div class="pull-left">Captura de auditoria</div>
							  <div class="widget-icons pull-right">
								<button type="button"  id="btnGuardar" class="btn btn-primary  btn-xs"><i class="fa fa-floppy-o"></i> Guardar</button>
								<button type="button" id="btnCancelar" class="btn btn-default  btn-xs"><i class="fa fa-undo"></i> Cancelar</button> 
							  </div>  
							  <div class="clearfix"></div>
							</div>              

							<!-- Widget content -->
							<div class="widget-content">												
								<br>
								<div class="col-xs-6">															
									<div class="form-group">									
										<label class="col-xs-2 control-label">No.</label>
										<div class="col-xs-4"><input type="text" class="form-control" name="txtAuditoria" readonly/></div>													
										<label class="col-xs-2 control-label">Tipo</label>
										<div class="col-xs-4">
											<select class="form-control" name="txtTipoAuditoria">
												<option value="">Seleccione...</option>
											</select>
										</div>								
									</div>								
									<br>
									<div class="form-group">
										<label class="col-xs-2 control-label">Responsable</label>
										<div class="col-xs-10">
											<select class="form-control" name="txtArea">
												<option value="">Seleccione...</option>											
											</select>
										</div>
									</div>
									<br>
									<div class="form-group">
										<label class="col-xs-2 control-label">Sector</label>
										<div class="col-xs-4">
											<select class="form-control" name="txtSector" onChange="javascript:recuperarListaLigada('lstSubsectoresBySector', this.value, document.all.txtSubsector);">
												<option value="">Seleccione...</option>											
											</select>
										</div>								

										<label class="col-xs-2 control-label">Subsector</label>
										<div class="col-xs-4">
											<select class="form-control" name="txtSubsector" id="txtSubsector">
												<option value="">Seleccione...</option>											
											</select>
										</div>								
									</div>									
									<br>
									<div class="form-group">
										<label class="col-xs-2 control-label">Sujeto</label>
										<div class="col-xs-10">
											<select class="form-control" name="txtSujeto" onChange="javascript:recuperarListaLigada('lstObjetosBySujeto', this.value, document.all.txtObjeto);">
												<option value="">Seleccione...</option>											
											</select>
										</div>								
									</div>
									<br>
									<div class="form-group">
										<label class="col-xs-2 control-label">Objeto</label>
										<div class="col-xs-8">
											<select class="form-control" name="txtObjeto">
												<option value="">Seleccione...</option>										
											</select>										
										</div>	
										<div class="col-xs-2">
											<button  type="button" class="btn btn-link" id="btnVerObjeto">Ver Objetos</button>											
										</div>
									</div>								
									<br>	
									<div class="form-group">
										<label class="col-xs-2 control-label">Objetivo(s) <i class="fa fa-pencil"  id="txtObjetivoEdit"></i></label>
										<div class="col-xs-10"><textarea class="form-control" rows="3" name="txtObjetivo" id="txtObjetivo"></textarea></div>									
									</div>
									<br>
									<div class="form-group">
										<label class="col-xs-2 control-label">Alcance(s) <i class="fa fa-pencil"  id="txtAlcanceEdit"></i></label>
										<div class="col-xs-10"><textarea class="form-control" rows="3" name="txtAlcance" id="txtAlcance"></textarea></div>
									</div>	
									<br>
									<div class="form-group">
										<label class="col-xs-2 control-label">Justificación <i class="fa fa-pencil"  id="txtJustificacionEdit"></i></label>
										<div class="col-xs-10"><textarea class="form-control" rows="3" name="txtJustificacion" id="txtJustificacion"></textarea></div>
									</div>
									<br>
									<div class="form-group">									
										<label class="col-xs-2 control-label">Tipo de Presupuesto</label>
										<div class="col-xs-10">
											<select class="form-control" name="txtPresupuesto">
												<option value="">Seleccione</option>
												<option value="" selected>LOCAL</option>
												<option value="">FEDERAL</option>
											</select>
										</div>									
									</div>
									<br>
									<div class="form-group">
										<label class="col-xs-2 control-label"></label>
										<div class="col-xs-10" style="text-align:left">
											<div class="checkbox"><label><input type="checkbox" name="chkMilitante"> Acompañamiento con la ASF</label></div>
										</div>
									</div>										
								</div>								
								
								<div class="col-xs-6">							
									<div class="form-group">
										<table class="table table-striped table-bordered table-hover table-condensed table-responsive">
											<caption>Criterios de Selección Aplicados</caption>
											<thead>
											<tr><th>Asignar</th><th>Criterio</th><th>Descripción</th></tr>
											</thead>
											<tbody>									  
												<tr style="width: 100%; font-size: xx-small"><td><input type="checkbox" value="Cat" /></td><td>Criterio Social</td><td>Este criterio se basa en las cuestiones aplicadas a la sociedad y al las secciones electorales.</td></tr>										
												<tr style="width: 100%; font-size: xx-small"><td><input type="checkbox" value="Cat" /></td><td>Criterio Económico</td><td>Este criterio se basa en las cuestiones aplicadas a la sociedad y al las secciones electorales.</td></tr>																		
												<tr style="width: 100%; font-size: xx-small"><td><input type="checkbox" value="Cat" /></td><td>Criterio Especial</td><td>Este criterio se basa en las cuestiones aplicadas a la sociedad y al las secciones electorales.</td></tr>																																
											</tbody>
										</table>
										<div class="table-responsive" style="height: 300px; overflow: auto; overflow-x:hidden;">
											<table class="table table-striped table-bordered table-hover">
												<caption>Documentos Asociados</caption>
													<thead>
														<tr><th>No.</th><th>Tipo</th><th>F Docto. </th><th>Asunto</th><th>Origen</th><th>Destino</th><th>Estatus</th></tr>
													</thead>
													<tbody id="tablaDoctos">								  
														<tr style="width: 100%; font-size: xx-small">
														<td>ASCM-2016/0035</td><td>OFICIO</td><td>16/01/2016</td></td><td>CÉDULA DE POTENCIALES</td><td>JURÍDICO</td><td>DIRECCIÓN GRAL DE AUDITORIAS "B"</td><td><img onclick="modalWin('mostrarPDF.php');" src="img/xls.gif"></td>
														</tr>
														<tr style="width: 100%; font-size: xx-small">
														<td>ASCM-2016/0001</td><td>NOTA</td><td>16/01/2016</td><td>SOLICTUD DE CREDITO</td><td>DIRECCIÓN GRAL DE AUDITORIAS "B"</td><td>JURÍDICO</td><td><img onclick="modalWin('mostrarPDF.php');" src="img/xls.gif"></td>
														</tr>
														<tr style="width: 100%; font-size: xx-small">
														<td>IEDF-2015/0001</td><td>OFICIO</td><td>16/01/2016</td><td>SOLICTUD DE INFORMACIÓN</td><td>IEDF</td><td>ASCM</td><td><img onclick="modalWin('mostrarPDF.php');" src="img/xls.gif"></td>
														</tr>												
													</tbody>
											</table>											
										</div>													

										<div class="clearfix"></div>
									</div>
							<br>
							<div class="clearfix"></div>
						</div>
						<div class="clearfix"></div>
					</div>
				</form>
			</div>
		  
		  
			<div class="clearfix"></div>
		</div>
	</div>
   <div class="clearfix"></div>
</div>
<!-- Content ends -->

	<div id="modalObjetos" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<input type='HIDDEN' name='txtObjetoNuevo' value=''>									
			<!-- Modal content-->
			<div class="modal-content">									
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h3 class="modal-title"><i class="fa fa-home"></i> Objeto de Fiscalización...</h3>
				</div>									
				<div class="modal-body">
					<div class="container-fluid">
						<div class="row">
							<div class="table-responsive" style="height: 400px; overflow: auto; overflow-x:hidden;">
								<table class="table table-striped table-bordered table-hover table-condensed">
									<caption>PRESUPUESTO DE LA UNIDAD RESPONSABLE</caption>
									<thead>
									<tr><th width="3%"></th><th  width="15%">Función</th><th>Subfunción</th><th>Actividad Institucional</th><th>Cuenta(s)</th><th>Partida Presupuestal</th></tr>
									</thead>
									<tbody id="tblGasto"></tbody>
								</table>
							</div>
						</div>
					</div>
					<div class="clearfix"></div>
				</div>				
				<div class="modal-footer">
					<button  type="button" class="btn btn-primary active" id="btnGuardarObjeto" style="display:inline;"><i class="fa fa-floppy-o"></i> Guardar</button>	
					<button  type="button" class="btn btn-default" id="btnCancelarObjeto" 		style="display:inline;"><i class="fa fa-undo"></i> Cancelar</button>	
				</div>
			</div>
		</div>
	</div>
	
	<div id="modalTextoLargo" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<input type='HIDDEN' name='txtObjetoNuevo' value=''>									
			<!-- Modal content-->
			<div class="modal-content">									
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h3 class="modal-title"><i class="fa fa-home"></i> Capturando...</h3>
				</div>									
				<div class="modal-body">
						<div class="row">
							<div class="form-group">									
								<div class="col-xs-12"><textarea class="form-control" rows="15" placeholder="Capture aqui" id="txtTextoLargo" name="txtTextoLargo"></textarea></div>
							</div>
						</div>
					<div class="clearfix"></div>
				</div>				
				<div class="modal-footer">
					<button  type="button" class="btn btn-primary active" id="btnGuardarTexto"><i class="fa fa-floppy-o"></i> Guardar</button>	
					<button  type="button" class="btn btn-default" id="btnCancelarTexto"><i class="fa fa-undo"></i> Cancelar</button>	
				</div>
			</div>
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