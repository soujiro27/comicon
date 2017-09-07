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

	<script type='text/javascript' src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js?ver=3.1.2"></script> 	
	<script type="text/javascript" src="js/genericas.js"></script>
	
	<style type="text/css">		
		@media screen and (min-width: 768px) {
			#mapa_content {width:100%; height:450px;}
			#mdlConsolidado .modal-dialog  {width:90%;}
			.delimitado {  
				height: 350px !important;
				overflow: scroll;
			}​			
		}
	</style>
  
  <script type="text/javascript"> 
  
	var nRegistrosGuardados=0;
	var ultimoDocto;
	var ultimoGestor;
	var sOperacionGeneral="";
  	
	function limpiarCampos(){
		document.all.txtID.value="";
		document.all.txtCentro.selectedIndex=0;
		document.all.txtTipo.selectedIndex=0;
		document.all.txtDocumento.selectedIndex=0;
		document.all.txtNivel.value="";		
		document.all.txtRubro.value="";
		document.all.txtImporte.value="";
	}
	
	function validarCaptura(){
		if (document.all.txtCentro.selectedIndex==0){alert("Debe seleccionar un CENTRO GESTOR.");return false;}
		if (document.all.txtTipo.selectedIndex==0){alert("Debe seleccionar un TIPO DE USUARIO.");return false;}
		if (document.all.txtDocumento.selectedIndex==0){alert("Debe seleccionar un TIPO DE DOCUMENTO.");return false;}
		if (document.all.txtRubro.value==""){alert("Debe capturar el RUBRO.");return false;}
		if (document.all.txtNivel.value==""){alert("Debe capturar el NIVEL.");return false;}
		if (document.all.txtImporte.value==""){
			if(confirm("El campo IMPORTE, no ha sido capturado. Para capturarlo y cancelar el guardado presione el botón CANCELAR.")==false){return false;}
		}
		return true;
	}
	
		
		function obtenerDatos( sDocto, sGestor){
			ultimoDocto = sDocto;
			ultimoGestor = sGestor;
			sOperacionGeneral="UPD";
			recuperarTabla('tblRubrosByCentroDocto', sGestor + '/'+ sDocto , document.all.tablaRubros);					
			$('#mdlConsolidado').removeClass("invisible");
			$('#mdlConsolidado').modal('toggle');
			$('#mdlConsolidado').modal('show');			
			return;			
	}

	function guardarRubro(){
		var sValor="";
		var sOper=document.all.txtOperacion.value;
		var d = document.all;
		var separador2="";
		
		
		//sValor = d.txtCentro.value + '|'  + d.txtTipo.value + '|' + d.txtDocumento.value + '|' + d.txtNivel.value  + '|' + d.txtRubro.value + '|' + d.txtImporte.value;		
		sValor = d.txtID.value + '|' +  d.txtCentro.value.substring(0, 2) + '|'  + d.txtCentro.value.substring(2, 4) + '|'  + d.txtCentro.value.substring(4, 6) + '|'  + d.txtTipo.value + '|' + d.txtDocumento.value + '|' + d.txtNivel.value  + '|' + d.txtRubro.value + '|' + d.txtImporte.value;		
		//return false;
		
		alert("Valor: " + sValor  + "    Operacion: " + sOper);

		
		$.ajax({
			type: 'GET', url: '/guardar/consolidado/' + sOper + '/' + sValor,
			success: function(response) {
				
				if(response=="OK"){
					var sParam = d.txtCentro.value + '/'+ d.txtDocumento.value					
					alert("Los datos se guardaron correctamente.");	
					nRegistrosGuardados=1;
					recuperarTabla('tblRubrosByCentroDocto', sParam, document.all.tablaRubros);									
				} else{
					alert("Los datos NO se guardaron correctamente.");
				}				
				return true;				
			},
			error: function(xhr, textStatus, error){
				alert(' Error en function guardarRubro()\n\tstatusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error: ' + error );
				return false;
			}			
		});		
	}	
	
	function editarRubro(id){
		//alert("Editar Renglon: " + id);
			document.all.txtOperacion.value='UPD';			
			document.all.divListaRegistros.style.display='none';
			document.all.divCapturaRegistro.style.display='inline';			
			document.all.btnGuardarRubro.style.display='inline';
			document.all.btnCancelarRubro.style.display='inline';
			document.all.btnNuevoRubro.style.display='none';
			document.all.btnRegresar.style.display='none';
			limpiarCampos();
			
		$.ajax({
			type: 'GET', url: 'tblRubroByID/' + id ,
			success: function(response) {	
				var obj = JSON.parse(response);
				document.all.txtOperacion.value='UPD';				
				document.all.txtID.value='' + obj.id;				
				seleccionarElemento(document.all.txtCentro, obj.gestor);	
				seleccionarElemento(document.all.txtTipo, obj.idTipoConsolidado);				
				recuperarListaSelected('lstConsolidadosByTipo', obj.idTipoConsolidado, document.all.txtDocumento, obj.idConsolidado);
				document.all.txtRubro.value='' + obj.rubro;					
				document.all.txtNivel.value='' + obj.nivel;					
				document.all.txtImporte.value='' + obj.importe;					
			},
			error: function(xhr, textStatus, error){
				alert('statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
			}			
		});				
			

		
	}

	function eliminarRubro(id){
		if(confirm("¿Esta Ud. seguro de eliminar el rubro?")==true){
				
		$.ajax({
			type: 'GET', url: 'tblEliminarRubroByID/' + id ,
			success: function(response) {	
				var obj = JSON.parse(response);	
				recuperarTabla('tblRubrosByCentroDocto', obj.gestor + '/'+ obj.docto , document.all.tablaRubros);
				nRegistrosGuardados=1;
				alert("Se eliminó correctamente.");
			},
			error: function(xhr, textStatus, error){
				alert('statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
			}			
		});			
		
		}
	}
	
	
	function recuperarTabla(lista, valor, tbl){
		$.ajax({
			type: 'GET', url: '/'+ lista + '/' + valor ,
			success: function(response) {
				var jsonData = JSON.parse(response);			
				//Vacia la lista
				tbl.innerHTML="";
				
				//Agregar renglones
				var renglon, columna;
				
				for (var i = 0; i < jsonData.datos.length; i++) {
					var dato = jsonData.datos[i];					
					renglon=document.createElement("TR");			

					var sOperaciones="<i class='fa fa-pencil-square-o' onclick='editarRubro(" +  dato.id + ");' ></i>  <i class='fa fa-trash-o' onclick='eliminarRubro(" +  dato.id + ");'></i>";
					
					
					renglon.innerHTML="<td>" + dato.sGestor + "</td><td>" + dato.sDocto + "</td><td>" + dato.rubro + "</td><td>" + dato.nivel + "</td><td>" + dato.importe  + "</td><td>" + sOperaciones + "</td>";
					renglon.onclick= function() {
						//alert("Editar Registro: " + dato.id);
					};
					tbl.appendChild(renglon);					
				}				
			},
			error: function(xhr, textStatus, error){
				alert('function recuperarTabla ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
			}			
		});	
	}	
	
	
	

	
	function getListaDoctos(sTipo){
		recuperarListaLigada('lstConsolidadosByTipo', sTipo, document.all.txtDocumento);
	}
	
	
	
	
		var nUsr='<?php echo $_SESSION["idUsuario"];?>';		
		var nCampana='<?php echo $_SESSION["idCuentaActual"];?>';	
	
	$(document).ready(function(){
		if(nUsr!="" && nCampana!=""){
			cargarMenu( nCampana);			
		}else{
			if(nCampana=="")alert("Debe establecer una CAMPAÑA como PREDETERMINADA. Por favor consulte con su administrador del sistema.");
		}			
		
		recuperarLista('lstCentrosByArea', document.all.txtCentro);
		recuperarLista('lstTiposConsolidados', document.all.txtTipo);
		
		
		$("#btnAgregar" ).click(function() {		
			document.all.txtOperacion.value='INS';
			sOperacionGeneral="INS";
			$('#mdlConsolidado').removeClass("invisible");
			$('#mdlConsolidado').modal('toggle');
			$('#mdlConsolidado').modal('show');
			
			recuperarTabla('tblRubrosByCentroDocto', '-/-', document.all.tablaRubros);					
			
			document.all.divListaRegistros.style.display='inline';
			document.all.divCapturaRegistro.style.display='none';			
			document.all.btnGuardarRubro.style.display='none';
			document.all.btnNuevoRubro.style.display='inline';
			document.all.btnCancelarRubro.style.display='none';
			document.all.btnRegresar.style.display='inline';
		
		});
		
		$("#btnNuevoRubro" ).click(function() {		
			document.all.txtOperacion.value='INS';			
			document.all.divListaRegistros.style.display='none';
			document.all.divCapturaRegistro.style.display='inline';			
			document.all.btnGuardarRubro.style.display='inline';
			document.all.btnCancelarRubro.style.display='inline';
			document.all.btnNuevoRubro.style.display='none';
			document.all.btnRegresar.style.display='none';
			limpiarCampos();
			
			if (sOperacionGeneral=="UPD"){
				seleccionarElemento(document.all.txtCentro, ultimoGestor);
				document.all.txtCentro.disabled=true;
			}else{
				document.all.txtCentro.disabled=false;
			}
			
		});	


		
		
		$("#btnCancelarRubro" ).click(function() {		
			document.all.txtOperacion.value='';			
			document.all.divListaRegistros.style.display='inline';
			document.all.divCapturaRegistro.style.display='none';			
			document.all.btnGuardarRubro.style.display='none';
			document.all.btnNuevoRubro.style.display='inline';
			document.all.btnCancelarRubro.style.display='none';
			document.all.btnRegresar.style.display='inline';
		});	
		
		

		$("#btnGuardarRubro" ).click(function() {
			if(validarCaptura()){
				document.all.divListaRegistros.style.display='inline';
				document.all.divCapturaRegistro.style.display='none';			
				document.all.btnGuardarRubro.style.display='none';
				document.all.btnNuevoRubro.style.display='inline';		
				document.all.btnCancelarRubro.style.display='none';
				document.all.btnRegresar.style.display='inline';				
				guardarRubro();
			}
		});	
		
		$("#btnRegresar" ).click(function() {
			document.all.txtOperacion.value='';	
			$('#mdlConsolidado').removeClass("invisible");
			$('#mdlConsolidado').modal('toggle');			
			$('#mdlConsolidado').modal('hide');
			
			if (nRegistrosGuardados==1){				
				nRegistrosGuardados=0;
				document.location.reload();
			}
			
		});
		

	});
	
	
	
	
    </script>
  
  <!-- Title and other stuffs -->
  <title>Sistema Integral de Auditoría</title>
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
	<style type="text/css">.jqstooltip { position: absolute;left: 0px;top: 0px;visibility: hidden;background: rgb(0, 0, 0) transparent;background-color: rgba(0,0,0,0.6);filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=#99000000, endColorstr=#99000000);-ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr=#99000000, endColorstr=#99000000)";color: white;font: 10px arial, san serif;text-align: left;white-space: nowrap;padding: 5px;border: 1px solid white;z-index: 10000;}.jqsfield { color: white;font: 10px arial, san serif;text-align: left;}</style>
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
					<div class="col-xs-3"><h2>Información Consolidada</h2></a></div>									
					<div class="col-xs-2">
						<ul class="nav navbar-nav "><li><a href="./notificaciones"><i class="fa fa-envelope-o"></i> Usted tiene <span class="badge">3</span> Mensaje(s).</a></li></ul>
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
		  
		  <li class="has_sub"  id="NORMATIVIDAD" style="display:none;">
			<a href=""><i class="fa fa-pencil-square-o"></i> Normatividad<span class="pull-right"><i class="fa fa-chevron-right"></i></span></a>
			<ul id="NORMATIVIDAD-UL"></ul>
		  </li>		  
		  
		  <li class="has_sub"><a href="/cerrar"><i class="fa fa-sign-out"></i> Salir</a></li>		  
		  
		</ul>
	</div> <!-- Sidebar ends -->
	
  	  	<!-- Main bar -->
  	<div class="mainbar">      
	    <!-- Page heading -->
      <!-- Page heading -->
      <div class="page-head">
        <h2 class="pull-left"><i class="fa fa-table"></i> Consolidados</h2>

        <!-- Breadcrumb -->
        <div class="bread-crumb pull-right">
          <a href="index.html"><i class="fa fa-home"></i> Home</a> 
          <!-- Divider -->
          <span class="divider">/</span> 
          <a href="#" class="bread-current">Consolidados</a>
        </div>

        <div class="clearfix"></div>

      </div>
	    <!-- Page heading ends -->	
		
		<div class="matter">
			<div class="container">

			  <!-- Table -->
				<div class="row">
					<div class="col-md-12">				
						<div class="widget">
							<div class="widget-head">
							  <div class="pull-left">Informes Consolidados Financieros</div>
							  <div class="widget-icons pull-right">
							  </div>  
							  <div class="clearfix"></div>
							</div>
						
							<div class="widget-content">	
								<div class="table-responsive" style="height: 350px; overflow: auto; overflow-x:hidden;">														
									<div class="">
										<table class="table table-striped table-bordered table-hover">
										  <thead>
											<tr>
											  <th>Tipo</th>
											  <th>Centro Gestor</th>											  
											  <th>Documento</th>							  											  							  
											  <th>Cantidad<br>Conceptos</th>							  
											</tr>
										  </thead>
										  <tbody>
											<?php foreach($datos as $key => $valor): ?>
											<tr onclick=<?php echo "\"obtenerDatos('" . $valor['docto'] . "', '" . $valor['gestor'] . "');\""; ?> >
											  <td><?php echo $valor['sTipoConsolidado']; ?></td>
											  <td><?php echo $valor['sGestor']; ?></td>
											  <td><?php echo $valor['sDocto']; ?></td>
											  <td><?php echo $valor['cantidad']; ?></td>
											</tr>
											<?php endforeach; ?>						                                                                    
										  </tbody>
										</table>
									</div>
								</div>
							</div>
							
							<div class="widget-foot">
								<button id="btnAgregar" class="btn btn-primary btn-xs">Agregar Rubro</button>
								<div class="clearfix"></div> 
							</div>
								
						</div>
					
						<form id="formulario" METHOD='POST'>								
							<input type='HIDDEN' name='txtOperacion' value=''>
							<input type='HIDDEN' name='txtID' value=''>
							<div id="mdlConsolidado" class="modal fade" role="dialog">
								<div class="modal-dialog">
									
										<!-- Modal content-->
										<div class="modal-content">										
											<div class="modal-header">
												<button type="button" class="close" data-dismiss="modal">&times;</button>
												<h4 class="modal-title">Registrar Rubro...</h4>
											</div>										
											<div class="modal-body">												
												<div class="table-responsive" style="height: 350px; overflow: auto; overflow-x:hidden;" id="divListaRegistros">
													<table class="table table-striped table-bordered table-hover">																										
													  <thead><tr><th>Centro Gestor</th><th>Documento</th><th>Rubro</th><th>Nivel</th><th>Importe</th><th></th></tr></thead>
													  <tbody id="tablaRubros"></tbody>
													</table>														
												</div>
												<div id="divCapturaRegistro" style="display:none;">					
													<div class="form-group">
														<label class="col-xs-2 control-label" style="text-align:right">Centro Gestor</label>
														<div class="col-xs-6">
															<select class="form-control" name="txtCentro">
																<option value="">Seleccione...</option>
															</select>
														</div>
														<label class="col-xs-1 control-label" style="text-align:right">Tipo</label>
														<div class="col-xs-3">
															<select class="form-control" name="txtTipo" onchange="getListaDoctos(this.value);">
																<option value="">Seleccione...</option>
															</select>
														</div>
													</div>
													<br>											
													<div class="form-group">
														<label class="col-xs-2 control-label" style="text-align:right">Documento</label>
														<div class="col-xs-8">
															<select class="form-control" name="txtDocumento">
																<option value="">Seleccione...</option>
															</select>
														</div>
														<label class="col-xs-1 control-label" style="text-align:right">Nivel</label>
														<div class="col-xs-1">
															<input type="number" class="form-control" name="txtNivel" placeholder="1 - 9"/>
														</div>													
													</div>
													<br>
													<div class="form-group">												
														<label class="col-xs-2 control-label" style="text-align:right">Rubro</label>
														<div class="col-xs-8">
															<input type="text" class="form-control" name="txtRubro"/>
														</div>
														<label class="col-xs-1 control-label" style="text-align:right">Importe</label>
														<div class="col-xs-1">
															<input type="text" class="form-control" name="txtImporte" placeholder="0.00"/>
														</div>																								
													</div>											
													<br>											
													<div class="form-group">
														<label class="col-xs-2 control-label" style="text-align:right">Rubro Anterior</label>
														<div class="col-xs-10">
															<select class="form-control" name="txtRubroAnterior">
																<option value="">Seleccione...</option>
															</select>
														</div>												
													</div>
													<div class="clearfix"></div>
												</div>
											</div>
												<!--   COMENTARIOS  : AQUI VA EL FOOTER MODAL -->
											<div class="modal-footer">
												<button type="button" class="btn btn-warning active" id="btnNuevoRubro"><i class="fa fa-file-text-o"></i> Nuevo Rubro...</button>
												<button type="button" class="btn btn-primary" id="btnGuardarRubro"><i class="fa fa-floppy-o"></i> Guardar datos</button>	
												<button type="button" class="btn btn-default" id="btnCancelarRubro"><i class="fa fa-floppy-o"></i> Cancelar Rubro</button>	
												<button type="button" class="btn btn-default" id="btnRegresar"><i class="fa fa-undo"></i> Regresar</button>													
											</div>
										</div>
										
									
								</div>
							</div>
						</form>
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
            <p class="copy">Copyright © 2016 | Auditoría Superior de la Ciudad de México</p>
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