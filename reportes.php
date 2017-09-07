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
			#modalFlotante .modal-dialog  {width:60%;}
		}
		label{text-align:right};
	</style>
  
  <script type="text/javascript"> 
	
	function agregarDatos(){
		document.all.operacion.value='INS';
		$('#modalFlotante').removeClass("invisible");
		$('#modalFlotante').modal('toggle');
		$('#modalFlotante').modal('show');
		limpiarCampos();
	}
	
	function limpiarCampos(){
		document.all.txtID.value="";
		document.all.txtNombre.value="";
		document.all.txtSiglas.value="";
		document.all.txtRepresentante.value="";
		document.all.txtDomicilio.value="";
	}
	
	var nCantidadParametros;
	var sArchivoActual;
	
	
	function obtenerDatos(id, nombre, archivo){	
		nCantidadParametros=0;
		sArchivoActual=archivo;
		$.ajax({
			type: 'GET', url: '/reporteParametros/' + id,
			success: function(response) {
				var jsonData = JSON.parse(response);
			
				if(jsonData.datos.length>0) {
					nCantidadParametros=jsonData.datos.length;
					
					$('#modalFlotante').removeClass("invisible");
					$('#modalFlotante').modal('toggle');
					$('#modalFlotante').modal('show');
					
					document.all.txtID.value='' + id;
					document.all.txtNombre.value='' + nombre;
					//document.all.txtModulo.value='' + modulo;
					
					//Cargar los parametros					
					var nRenglon=0;
					var sParametro;
					var sEtiqueta;
					var sCtrl;
					for (var i = 0; i < jsonData.datos.length; i++) {
						dato = jsonData.datos[i];
						nRenglon++;
						
						sParametro = "PARAM" + nRenglon;
						sEtiqueta = sParametro + "-LBL";
						sCtrl = sParametro + "-CTRL";
						
						//Mostrar area del parametro
						document.getElementById(sParametro).style.display="block";
						
						//Agregar la etiqueta del parametro
						var texto = document.createTextNode(dato.etiqueta);						
						var et = document.getElementById(sEtiqueta);
						while (et.firstChild) et.removeChild(et.firstChild);						
						et.appendChild(texto);
						
						//Agregar ctrl
						
						if(dato.tipo=="CMB"){
							
							var divCtrl = document.getElementById(sCtrl);
							
							//Create and append select list
							var selectList = document.createElement("select");
							
							selectList.setAttribute("class", "form-control");
							
							selectList.id = "lstParametro" + nRenglon;
							while (divCtrl.firstChild) divCtrl.removeChild(divCtrl.firstChild);						
							divCtrl.appendChild(selectList);
							var lstActual = document.getElementById("lstParametro" + nRenglon);
							recuperarLista('expandirListaParametro/'+ dato.idParametro, lstActual);
						}
						
						if(dato.tipo=="CLD"){							
							var divCtrl = document.getElementById(sCtrl);
							
							//Create and append select list
							var selectList = document.createElement("input");
							selectList.setAttribute("type", "date");
							//selectList.setAttribute("class", "datepicker");	
							selectList.setAttribute("data-date-format", "dd/mm/yyyy");	
							selectList.setAttribute("placeholder", "dd/mm/yyyy");	
						
							selectList.id = "lstParametro" + nRenglon;
							
							while (divCtrl.firstChild) divCtrl.removeChild(divCtrl.firstChild);						
							divCtrl.appendChild(selectList);
							var lstActual = document.getElementById("lstParametro" + nRenglon);
							//alert("Creando campo: " + "lstParametro" + nRenglon);
							//recuperarLista('expandirListaParametro/'+ dato.idParametro, lstActual);
						}						
						
						
						//alert("Parametro:" + dato.idParametro + " Tipo Control: " + dato.tipo + " Etiqueta:" + dato.etiqueta + " dominio: " + dato.dominio + " SQL: " + dato.consulta);
					}										
				}
				else{
					modalWin("/" + archivo + "?area="+ sArea);
				}		
			},
			error: function(xhr, textStatus, error){
				alert('statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
			}			
		});			
	}	

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
	
	var nUsr='<?php echo $_SESSION["idUsuario"];?>';
	var nCampana='<?php echo $_SESSION["idCuentaActual"];?>';
	var sArea='<?php echo $_SESSION["idArea"];?>';
	  
	$(document).ready(function(){
		setInterval(getMensaje('txtNoti'),60000);
		if(nUsr!="" && nCampana!=""){
			recuperarListaSelected('lstCuentasByUsr', nUsr, document.all.txtCampana,nCampana);
			cargarMenu( nCampana);			
		}else{
			if(nCampana=="")alert("Debe establecer una CUENTA PÚBLICA como PREDETERMINADA. Por favor consulte con su administrador del sistema.");
		}
		
		
		
  $.datepicker.setDefaults($.datepicker.regional["es"]);
  
  $('#cldParametro1').click(function() {	alert("CLDpARAMETRO1"); });
  $('#cldParametro2').click(function() {	alert("CLDpARAMETRO2"); });
  $('#cldParametro3').click(function() {	alert("CLDpARAMETRO3"); });
  

		
		$('#btnGenerar').click(function() {
			//alert("Generando");
			var nRenglon=0;
			var sParametros;
			//var sPagina = "http://172.16.6.33:8000/" + sArchivoActual + "?";
			//var sPagina = "http://localhost/" + sArchivoActual + "?";
			var sPagina = "/" + sArchivoActual + "?area="+ sArea ;
			
			var separador="&";
			for (var i = 0; i < nCantidadParametros; i++) {
				nRenglon++;				
				var parActual = document.getElementById("lstParametro" + nRenglon);				
				sPagina = sPagina + separador + "param"+ nRenglon + "=" + parActual.value;				
			}
			sPagina = sPagina + separador + "titulo=" + document.all.txtNombre.value;
			modalWin(sPagina);
		});

		
	});		
	
	
    </script>
  
  <!-- Title and other stuffs -->
  <title>Sistema Integral de Auditorías</title>
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
    <nav class="navbar navbar-default navbar-fixed-top">
		<div class="container-fluid">
			<nav class="collapse navbar-collapse bs-navbar-collapse" role="navigation">			
				<div class="col-xs-12">
					<div class="col-xs-2"><a href="/"><img src="img/logo-top.png"></a></div>				
					<div class="col-xs-2">
						<ul class="nav navbar-nav "><li><a href="#"><i class="fa fa-th-list"></i> <?php echo $_SESSION["sCuentaActual"] ?></a></li></ul>
					</div>						
					<div class="col-xs-3"><h1>Reporteador</h1></a></div>									
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
			<a href=""><i class="fa fa-pencil-square-o"></i> Acciones<span class="pull-right"><i class="fa fa-chevron-right"></i></span></a>
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
		  
		  <li class="open"><a href="/cerrar"><i class="fa fa-sign-out"></i> Salir</a></li>		  
		  
		</ul>
	</div> <!-- Sidebar ends -->
	
  	  	<!-- Main bar -->
  	<div class="mainbar">      
	    <!-- Page heading -->
      <!-- Page heading -->
      <div class="page-head">
        <h2 class="pull-left"><i class="fa fa-table"></i> Reporteador</h2>

        <!-- Breadcrumb -->
        <div class="bread-crumb pull-right">
          <a href="index.html"><i class="fa fa-home"></i> Home</a> 
          <!-- Divider -->
          <span class="divider">/</span> 
          <a href="#" class="bread-current">Reporteador</a>
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
							  <div class="pull-left">Lista de Informes</div>
							  <div class="widget-icons pull-right">
							  </div>  
							  <div class="clearfix"></div>
							</div>
						
							<div class="widget-content">						
								<div class="table-responsive">
									<table class="table table-striped table-bordered table-hover">
									  <thead>
										<tr><th>Reporte</th></tr>
									  </thead>
									  <tbody>
									  
									<?php foreach($datos as $key => $valor): ?>
									<tr onclick="<?php echo "obtenerDatos(" . $valor['idReporte'] . ", '" . $valor['sReporte'] . "', '" . $valor['archivo'] . "');"; ?>">										
										<td><?php echo $valor['sReporte']; ?></td>									  
									</tr>
									<?php endforeach; ?>						                                                                    
									  </tbody>
									</table>
								</div>
							</div>
							
							<div class="widget-foot"></div>
								
						</div>
					
					
						<div id="modalFlotante" class="modal fade" role="dialog">
							<div class="modal-dialog">
								<form id="formulario" METHOD='POST' action='' role="form">								
									<input type='HIDDEN' name='operacion' value=''>
									<input type='HIDDEN' name='txtParametro1' value=''>
									<input type='HIDDEN' name='txtParametro2' value=''>
									<input type='HIDDEN' name='txtParametro3' value=''>								
									
									<!-- Modal content-->
									<div class="modal-content">

										
										<div class="modal-body">
											<div class="container-fluid">
												<div class="row">					
															<br>
															<div class="form-group">
																<label class="col-xs-3 control-label">Id</label>
																<div class="col-xs-2">
																	<input type="text" class="form-control" name="txtID"  readonly />
																</div>											
																<label class="col-xs-7 control-label"></label>																
															</div>
															<br>
															<div class="form-group">
																<label class="col-xs-3 control-label">Reporte</label>
																<div class="col-xs-9">
																	<input type="text" class="form-control" name="txtNombre" readonly/>
																</div>															
															</div>

															<div class="form-group" id="PARAM1" style="display:none;">
															<br>															
																<label class="col-xs-3 control-label"  id="PARAM1-LBL"></label>
																<div class="col-xs-9"  id="PARAM1-CTRL"></div>															
															</div>															


															<div class="form-group" id="PARAM2" style="display:none;">
															<br>															
																<label class="col-xs-3 control-label"  id="PARAM2-LBL">Param2</label>
																<div class="col-xs-9"  id="PARAM2-CTRL"></div>															
															</div>

															<div class="form-group" id="PARAM3" style="display:none;">
															<br>															
																<label class="col-xs-3 control-label"  id="PARAM3-LBL">Param3</label>
																<div class="col-xs-9"  id="PARAM3-CTRL"></div>															
															</div>															
  
												</div>
												</div>
										</div>
											<!--   COMENTARIOS  : AQUI VA EL FOOTER MODAL -->
										<div class="modal-footer">
											<button type="button" class="btn btn-primary active" id="btnGenerar">Generar Informe</button>	
											<button onclick="$('#modalFlotante').modal('hide');" type="button" class="btn btn-default">Cancelar</button>	

										</div>
									</div>
									
								</form>
							</div>
						  </div>
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


<!-- Charts & Graphs -->

<!-- 
<script src="./Dashboard - MacAdmin_files/charts.js"></script> 

<script src="./Dashboard - MacAdmin_files/moment.min.js"></script> 
<script src="./Dashboard - MacAdmin_files/fullcalendar.min.js"></script> 
<script src="./Dashboard - MacAdmin_files/jquery.rateit.min.js"></script> 
<script src="./Dashboard - MacAdmin_files/jquery.prettyPhoto.js"></script>
-->

	
</body></html>