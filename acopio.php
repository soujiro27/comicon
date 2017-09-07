<!DOCTYPE html>
<html lang="en">
	<head>	
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">  
		<meta http-equiv="X-UA-Compatible" content="IE=Edge"/>
		<!-- Title and other stuffs -->
		<title>Sistema Integral de Auditorias</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="">
		<meta name="keywords" content="">
		<meta name="author" content="">		
		<script src="js/canvasjs.min.js"></script>
		<script type='text/javascript' src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js?ver=3.1.2"></script>
		<script type="text/javascript" src="js/genericas.js"></script>
	
		<style type="text/css">		
			@media screen and (min-width: 768px) {
				#cvGrafica1{height:250px; width:100%;}			
				#modalPapel .modal-dialog  {width:70%;}
				#CanvasGrafica{height:175px; width:100%;}		
				.auditor{background:#f4f4f4; font-size:6pt; padding:7px; display:inline; margin:1px; border:1px gray solid;}
				
				label {text-align:right;}
				th{
					background:#f4f4f4; color:black;
					font: normal bold 10px/15px Arial;
					text-align:center;
					vertical-align:middle;
				}
				
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
			var ventana;	
			function validarCaptura(){				
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
				
				if (document.all.txtClasificacion.selectedIndex==0){
					alert("Debe seleccionar la CLASIFICACIÓN.");
					document.all.txtClasificacion.focus();
					return false;
				}	
										
				if (document.all.txtArchivoOriginal.value=="" && document.all.txtOperacion.value=="INS"){
					alert("Debe seleccionar un ARCHIVO.");
					document.all.btnCargarArchivo.focus();
					return false;
				}				
				return true;		
			}			
			
			window.onload = function () {
				var chart; 				
				setGrafica(chart, "dpsDoctosByAuditoria", "bar", "", "cvGrafica1" );
			  };
				

			 function validarElimina(){
			 	if (confirm("¿Esta seguro que desea ELIMINAR este Acopio?:\n \n")){
	 			document.all.txtEstatusAcopio.value='INACTIVO';
	 			document.all.formulario.submit();
	 			}else{
	 			document.all.txtEstatusAcopio.selectedIndex= 1;
	 			}
			}


			function limpiarCampos(){
				document.all.txtID.value="";			
				document.all.txtAuditoria.selectedIndex=0;
				document.all.txtFase.selectedIndex=0;
				document.all.txtClasificacion.selectedIndex=0;
				document.all.txtObservaciones.value="";
				document.all.txtArchivoOriginal.value="";
				document.all.txtArchivoFinal.value="";
			}
			
			function limpiarArch(){
				document.all.txtArchivoOriginal.value="";
				document.all.txtArchivoFinal.value="";
			}
			
			function checkfile(sender) {
				var validExts = new Array(".xlsx", ".xls", ".pdf", ".doc", ".docx", ".zip");
				var fileExt = sender.value;
				fileExt = fileExt.substring(fileExt.lastIndexOf('.'));
				if (validExts.indexOf(fileExt) < 0) {
				  alert("El archivo seleccionado es inválido. Los formatos correctos son " +
						   validExts.toString() + ".");
				  return false;
				}
				else return true;
			}
			
			var sArchivoCarga;
			function abrirArchivo(){
				var formdata = new FormData();

				if(checkfile(this)){
					var file = this.files[0];
					//if (file.size>100000) alert("El tamaño del archivo no debe exceder 100,000 bytes.");	
					sArchivoCarga = file.name;
					formdata.append("btnUpload", file);
					var ajax = new XMLHttpRequest();
					ajax.upload.addEventListener("progress", progressHandler, false);
					ajax.addEventListener("load", completeHandler, false);
					ajax.addEventListener("error", errorHandler, false);
					ajax.addEventListener("abort", abortHandler, false);				
					ajax.open("POST", "uploadAcopio.php");
					ajax.send(formdata);			
				}
			}		
			
			function progressHandler(event){
				//document.all.progressBar.style="display:inline";
				//document.all.lblAvances.innerHTML="Cargando " + event.loaded + " bytes de " + event.total;
					
				var porcentaje = (event.loaded / event.total) * 100;
				document.all.progressBar.value= Math.round(porcentaje);
				document.all.status.innerHTML = Math.round(porcentaje) + "% cargando... Espere"; 				
			}
				
			function completeHandler(event){
				document.all.progressBar.style.display="none";
				document.all.progressBar.value= 0;			
				if(event.target.responseText!="ERROR"){				
					document.all.txtArchivoOriginal.value=sArchivoCarga;
					document.all.txtArchivoFinal.value=event.target.responseText;
					document.all.status.style.display='inline';
					document.all.status.innerHTML="<img src='img/xls.gif'> " + document.all.txtArchivoOriginal.value; 
					document.all.btnCargarArchivo.style.display="none";	
				}else{
					document.all.txtArchivoOriginal.value="";
					document.all.txtArchivoFinal.value="";
					alert("El archivo no subió correctamente.");
				}			
			}
			function errorHandler(event){document.all.status.innerHTML = "Falló la carga";}			
			function abortHandler(event){document.all.status.innerHTML = "Se abrotó la carga.";}					

			var nUsr='<?php echo $_SESSION["idUsuario"];?>';
			var nCampana='<?php echo $_SESSION["idCuentaActual"];?>';
			var sArea='<?php echo $_SESSION["idArea"];?>';
			
			$(document).ready(function(){
				getMensaje('txtNoti',1);
				document.getElementById('btnUpload').addEventListener('change', abrirArchivo, false);
			
				if(nUsr!="" && nCampana!=""){
					cargarMenu( nCampana);			
				}else{
					if(nCampana=="")alert("Debe establecer una CUENTA PÚBLICA como PREDETERMINADA. Por favor consulte con su administrador del sistema.");
				}			
			
				recuperarListaLigada('lstAuditoriasByAreaAuditor', sArea, document.all.txtAuditoria);						
				recuperarLista('lsttipoDocumento', document.all.txtClasificacion);
				//alert("Auditorias recuperadas: " + document.all.txtAuditoria.lenght);
				
				$( "#btnGuardar" ).click(function() {
					if (validarCaptura()){					
						document.all.formulario.submit();
					}
				});
				

				$( "#btnCancelarTexto" ).click(function() {
						$('#modalTextoLargo').modal('hide');
						$('#modalPapel').modal('show');				
				});
					
				$( "#btnGuardarTexto" ).click(function() {
						$('#modalTextoLargo').modal('hide');
						$('#modalPapel').modal('show');	
				});

				$( "#btnEliminarArchivo" ).click(function() {
					if (confirm("¿Esta seguro que desea ELIMINAR el ARCHIVO ADJUNTO\n \n")){
					limpiarArch();
					document.all.btnEliminarArchivo.style.display='none';
					document.all.status.style.display='none';
					document.all.btnCargarArchivo.style.display='inline';
				 	}

				});
					
				
				$( "#btnAgregar" ).click(function() {
					limpiarCampos();
					document.all.txtOperacion.value='INS';
					document.all.btnCargarArchivo.style.display="inline";
					document.getElementById("txtAuditoria").disabled = false;
					document.all.btnEliminarArchivo.style.display='none';
					document.all.status.style.display='none';
					document.all.btnEliminarRegistro.style.display='none';

					
					$('#modalPapel').removeClass("invisible");
					$('#modalPapel').modal('toggle');
					$('#modalPapel').modal('show');
					
				});

				$( "#btnCancelar" ).click(function() {
					$('#modalPapel').removeClass("invisible");
					$('#modalPapel').modal('toggle');
					$('#modalPapel').modal('hide');
					document.all.txtOperacion.value='';
					document.all.txtArchivoOriginal.value="";
					document.all.txtArchivoFinal.value="";
					document.all.btnCargarArchivo.style.display="none";
				});
				
				$( "#btnCargarArchivo" ).click(function() { $("#btnUpload").click();});
			});
			
			function obtenerElemento(id){
				$.ajax({
					type: 'GET', url: '/acopioByid/' + id ,
					success: function(response) {
						var obj = JSON.parse(response);				
						limpiarCampos();
						document.all.txtOperacion.value="UPD";
						document.getElementById("txtAuditoria").disabled = true;
						document.all.txtID.value=id;
						document.all.btnEliminarArchivo.style.display='inline';
						document.all.btnCargarArchivo.style.display='none';
						seleccionarElemento(document.all.txtAuditoria, obj.idAuditoria);
						seleccionarElemento(document.all.txtFase, obj.idFase);								
						seleccionarElemento(document.all.txtClasificacion, obj.idClasificacion);
						document.all.txtObservaciones.value='' + obj.observaciones;
						document.all.txtEstatusAcopio.value='' + obj.estatus;
						document.all.btnGuardar.style.display='inline';
						document.all.btnEliminarRegistro.style.display='inline';
						
						document.all.txtArchivoOriginal.value=obj.archivoOriginal;
						document.all.txtArchivoFinal.value=obj.archivoFinal;
						document.all.status.style.display='inline';
						document.all.status.innerHTML="<img src='img/xls.gif'> " + document.all.txtArchivoOriginal.value; 
						document.all.btnCargarArchivo.style.display="none";	
						
						$('#modalPapel').removeClass("invisible");
						$('#modalPapel').modal('toggle');
						$('#modalPapel').modal('show');
				},
					error: function(xhr, textStatus, error){
						alert('function obtenerElemento()   -> statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error + ' Auditoría: ' + id);
					}			
				});		
			}		
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
					<div class="col-xs-3"><h2><i class="fa fa-tasks"></i> Acopio de Información</h2></a></div>									
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
			<div class="col-xs-12">
				<div class="widget">
					<div class="widget-head">
					  <div class="pull-left"><h3 class="modal-title"><i class="fa fa-tasks"></i> Lista de Archivos</h3></div>
					  <div class="widget-icons pull-right">
						<button type="button" class="btn btn-primary active btn-xs" 	id="btnAgregar"><i class="fa fa-floppy-o"></i> Agregar</button>
					  </div>  
					  <div class="clearfix"></div>
					</div>             
					<div class="widget-content">
					<div class="col-xs-12">
						<div id="cvGrafica1"></div>
						<div class="clearfix"></div>
					</div>					
						<div class="col-xs-12">
							<div class="table-responsive" style="height: 400px; overflow: auto; overflow-x:hidden;">
								<table class="table table-striped table-bordered table-hover table-condensed">
								  <thead>
									<tr><th width="8%">Clave Auditoría</th><th width="25%">Sujeto de Fiscalización</th><th width="25%">Objeto de Fiscalización</th><th  width="10%">Tipo de Auditoría</th><th>ID</th><th>Fase</th><th>Tipo Docto</th><th>Estatus</th><th>Documento</th></tr>
								  </thead>
								  <tbody>				
									<?php foreach($datos as $key => $valor): ?>
									<tr  style="width: 100%; font-size: xx-small">										  										  
										<td onclick=<?php echo "obtenerElemento('" . $valor['idAcopio'] . "');"; ?>><?php echo $valor['claveAuditoria']; ?></td>
										<td onclick=<?php echo "obtenerElemento('" . $valor['idAcopio'] . "');"; ?>><?php echo $valor['sujeto']; ?></td>
										<td onclick=<?php echo "obtenerElemento('" . $valor['idAcopio'] . "');"; ?>><?php echo $valor['objeto']; ?></td>
										<td onclick=<?php echo "obtenerElemento('" . $valor['idAcopio'] . "');"; ?>><?php echo $valor['tipoAuditoria']; ?></td>
										<td onclick=<?php echo "obtenerElemento('" . $valor['idAcopio'] . "');"; ?>><?php echo $valor['idAcopio']; ?></td>									 
										<td onclick=<?php echo "obtenerElemento('" . $valor['idAcopio'] . "');"; ?>><?php echo $valor['idFase']; ?></td>
										<td onclick=<?php echo "obtenerElemento('" . $valor['idAcopio'] . "');"; ?>><?php echo $valor['idClasificacion']; ?></td>					  
										<td onclick=<?php echo "obtenerElemento('" . $valor['idAcopio'] . "');"; ?>><?php echo $valor['estatus']; ?></td>
										<td><a href="/acopio/<?php echo $valor['archivoFinal']; ?>"><?php $va = $valor['archivoFinal']; if($va=='' ){}else{echo obtenerGif($valor['archivoFinal']);} ?></a></td>
									</tr>
									<?php endforeach; ?>																						
								  </tbody>
								</table>
								<div class="clearfix"></div>
							</div>
						</div>
						<div class="clearfix"></div>
					</div>
					<div class="widget-foot"></div>
				</div>				
			</div>
		</div>
				
	</div>			
</div>

<div id="modalPapel" class="modal fade" role="dialog">
		<div class="modal-dialog">							
				<!-- Modal content-->
			<div class="modal-content">									
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h3 class="modal-title"><i class="fa fa-pencil-square-o"></i> Acopio de archivo electrónico por Auditoría...</h3>
				</div>									
				<div class="modal-body">
							<form id="formulario" METHOD='POST' ACTION="/guardar/acopio" role="form">
								<input type='HIDDEN' name='txtOperacion' value=''>
								<input type='HIDDEN' name='txtCuenta' value='<?php echo $_SESSION["idCuentaActual"];?>'>						
								<input type='HIDDEN' name='txtPrograma' value='<?php echo $_SESSION["idProgramaActual"];?>'>	
								<input type='HIDDEN' name='txtArchivoOriginal' value=''>
								<input type='HIDDEN' name='txtArchivoFinal' value=''>
								<input type='HIDDEN' name='txtID' value=''>
								<br>
								<div class="form-group">									
									<label class="col-xs-2 control-label">Auditoría</label>
									<div class="col-xs-10">
										<select class="form-control" id="txtAuditoria" name="txtAuditoria">
											<option value="">Seleccione</option>
										</select>
									</div>	
								</div>	
								<br>							
								<div class="form-group">										
									<label class="col-xs-2 control-label">Fase</label>
									<div class="col-xs-4">
										<select class="form-control" name="txtFase">
											<option value="" Selected>Seleccione...</option>
											<option value="PLANEACION">PLANEACIÓN</option>
											<option value="EJECUCION">EJECUCIÓN</option>
											<option value="INFORMES">ELABORACIÓN DE INFORMES</option>
										</select>
									</div>																						
									<label class="col-xs-2 control-label">Clasificación</label>
									<div class="col-xs-4">
										<select class="form-control" name="txtClasificacion">
											<option value="">Seleccione</option>
											<option value="CONTRATO" Selected>CONTRATO</option>											
											<option value="PROCEDIMIENTO">PROCEDIMIENTO</option>											
											<option value="NORMA">NORMA</option>											
											<option value="OTRO">OTRO</option>
										</select>
									</div>	
								</div>
								<br>							
								<div class="form-group">									
									<label class="col-xs-2 control-label">Observaciones</label>
									<div class="col-xs-10"><textarea class="form-control" name="txtObservaciones" id="txtObservaciones" rows="5" placeholder="Escriba aqui..."></textarea></div>	
								</div>	
								<div class="form-group" style="display: none;">													
									<label class="col-xs-3 control-label">ELIMINAR NOTIFICACIÓN</label>
									<div class="col-xs-3">
										<select class="form-control" name="txtEstatusAcopio">
											<option value="">Seleccione...</option>
											<option value="ACTIVO" selected>NO</option>
											<option value="INACTIVO" onclick="validarnoti();">ELIMINAR</option>
										</select>
									</div>
								</div>							
							</form>
					<div class="clearfix"></div>
				</div>
				
				<div class="modal-footer">
					<div class="pull-left">
						<form id="upload_form" enctype="multipart/form-data" method="post">
							<button  type="button" class="btn btn-danger" id="btnEliminarArchivo" style="display:none;"><i class="fa fa-trash-o fa-lg"></i></button>
							<button  type="button" class="btn btn-default" id="btnCargarArchivo"><i class="fa fa-link"></i> Anexar Archivo...</button>
							<input type="file" name="btnUpload" accept="application/pdf,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/x-zip-compressed" style="display:none;" id="btnUpload">
							<progress id="progressBar" value="0" max="100" style="width:'100%'; display:none;"></progress>
							<h4 id="status"></h4>
							<p id="lblAvances"></p>
						</form>
					</div>
											
					<div class="pull-right">
						<button style="display: inline; margin: 0px 23px 0px 0px ! important;" type="button" class="btn btn-danger" id="btnEliminarRegistro" onclick="validarElimina();"><i class="fa fa-times fa-lg"></i> Eliminar Acopio</button>
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


