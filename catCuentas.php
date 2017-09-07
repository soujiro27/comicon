<!DOCTYPE html>
<html lang="en"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">  
  <meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=Edge" />
		<script type='text/javascript' src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js?ver=3.1.2"></script> 
		<script type="text/javascript" src="js/genericas.js"></script>

  	<style type="text/css">		
		@media screen and (min-width: 768px) {
			#mapa_content {width:100%; height:150px;}
			#canvasJG, #canvasJD, #canvasDIP{height:140px; width:100%;}			
			#modalFlotante .modal-dialog  {width:60%;}
			
			
		label{text-align:right; text-valign:middle};
		.delimitado { height: 150px !important;overflow: scroll;}​		
		.listBox {width:100%;border:0px solid lightgray;background-color:transparent;font-family: 'Arial';font-size:12px;padding: 1px 1px 1px 1px;cursor:pointer;-webkit-appearance: none;}
				
			
		}
	</style>
  
  <script type="text/javascript"> 
	var sOperacionActual="";	
	var tipoCarga="";
	
	// -------------------------------- RUTINAS DE MANEJO DE FECHAS ------------------------------------
	$(function () {
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
	        dateFormat: 'dd-mm-yy',
	        firstDay: 1,
	        isRTL: false,
	        showMonthAfterYear: false,
	        yearSuffix: ''
	    };

		$.datepicker.setDefaults($.datepicker.regional["es"]);
		
		$("#datepicker").datepicker({
		minDate: null,
       	maxDate: null,
		dateFormat:"dd-mm-yy",
		firstDay: 1,	
		numberOfMonths: 2,
		//onClose: function( selectedDate ) { $( "#datepicker2" ).datepicker( "option", "minDate", selectedDate ); }
		});

		$("#datepicker2").datepicker({
		minDate: null,
       	maxDate: null,
		dateFormat:"dd-mm-yy",
		firstDay: 1,
		numberOfMonths: 2,
		//onClose: function( selectedDate ) { $( "#datepicker" ).datepicker( "option", "minDate", selectedDate ); }
		});
	});

	// ------------- INICIA RUTINAS DE PROCESOS DE CARGA DE ARCHIVOS ---------------------------------------------

	var sArchivoCarga;
			
	// Rutina que recupera un archivo desde la pantalla de archivos de windows, llama mediante POST el archivo file_upload_parser.php
	// mediante un objeto FormData, esta funciòn es invocada desde los botones de btnUpload a los que se les asigno el evento para hacerlo
	// asu vez se utilizan otros eventos (load, error, abort) para situaciones especificas.

	function abrirArchivo(){
		var file = this.files[0];
		var formdata = new FormData();
		
		if (checkfile(this)==false) { return; }
		
		//if (file.size>100000) alert("El tamaño del archivo no debe exceder 100,000 bytes.");	
		
		sArchivoCarga = file.name;
		formdata.append("btnUpload", file);
		var ajax = new XMLHttpRequest();
		ajax.upload.addEventListener("progress", progressHandler, false);
		ajax.addEventListener("load", completeHandler, false);
		ajax.addEventListener("error", errorHandler, false);
		ajax.addEventListener("abort", abortHandler, false);
		ajax.open("POST", "file_upload_parser.php");
		ajax.send(formdata);
	}

	function progressHandler(event){
		// Rutina que usando un elemento DOM (progressBar), que debe estar definido en el HTML, 
		// permite mostrar un avence progresivo de la carga.
		
		/*
		document.all.progressBar.style="display:inline";
		document.all.avances.innerHTML="Cargando " + event.loaded + " bytes de " + event.total;
		var porcentaje = (event.loaded / event.total) * 100;
		document.all.progressBar.value= Math.round(porcentaje);
		document.all.status.innerHTML = Math.round(porcentaje) + "% cargando... Espere"; 				
		*/

	}
			
	function completeHandler(event){

		//document.all.status.innerHTML=event.target.responseText; 
		//document.all.status.innerHTML="Archivo " + sArchivoCarga + " disponible para cargar en base de datos.";
		
		var sCuentaActual = document.all.txtID.value;
		var liga = "";
		var liga2 = '/actualizaArchivosByCta/' + sCuentaActual + '/' ;
		
		if(event.target.responseText!="ERROR"){				


			if(confirm('Se ha recuperado el archivo: "' + sArchivoCarga + '".\n\n¿Deseas integrar la información del archivo a la base de datos?\n\nNOTA: Tome en cuenta que si ya existe información de la cuenta "' + document.all.txtNombre.value + '"\nEn la Base de datos datos, esta se eliminará para integrar la información del archivo recuperado.\n\nSeleccione ACEPTAR para iniciar proceso de integración\nSeleccione CANCELAR para abortar el proceso"')==true){
				
				document.all.imgCargando.style="display:inline";
				document.all.btnsCarga.style.display='none';
				
				liga = '/cargarArchivo/' + tipoCarga + '/' + sArchivoCarga + '/' + sCuentaActual;

				$.ajax({
					type: 'GET', url: liga ,
					success: function(response) {							
						
						document.all.imgCargando.style="display:none";
						document.all.status.innerHTML="";
						document.all.status.style="display:none";
						alert("Registros Cargados:\n" + response);
						document.all.btnsCarga.style.display='inline';

						if(tipoCarga=='Egreso'){
							document.all.txtArchivoOriginal.value = sArchivoCarga;
							liga2 += sArchivoCarga + '/' + ( (document.all.txtArchivoOriginalIngreso.value == '') ? 'NULL' : document.all.txtArchivoOriginalIngreso.value);
						}else{
							document.all.txtArchivoOriginalIngreso.value = sArchivoCarga;
							liga2 += ( (document.all.txtArchivoOriginal.value == '') ? 'NULL' : document.all.txtArchivoOriginal.value) +  '/' + sArchivoCarga ;
						}

						//alert("El valor de liga2 es:" + liga2 );

						$.ajax({
							type: 'GET', url: liga2 ,
							success: function(response) {							
								if(tipoCarga=='Egreso'){ 
									document.all.btnCargaEgreso.style.display='none'; 
								}else{
									document.all.btnCargaIngreso.style.display='none'; 
								}
							},
							error: function(xhr, textStatus, error){
								alert('NO SE ACTUALIZARON LOS NOMBRES DE LOS ARCHIVOS. function completeHandler ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
							}			

						});
					},
					error: function(xhr, textStatus, error){
						alert('NO SE REALIZO LA CARGA. function completeHandler ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
					}			
				});
			}				
		}else{
			document.all.txtArchivoOriginal.value="";
			document.all.txtArchivoFinal.value="";
			document.all.txtArchivoOriginalIngreso.value="";
			document.all.txtArchivoFinalIngreso.value="";
			alert("El archivo no subió correctamente.");
		}
	}

	function errorHandler(event){document.all.status.innerHTML = "Falló la carga";}			
	function abortHandler(event){document.all.status.innerHTML = "Se abrotó la carga.";}					

	function checkfile(sender) {
		var validExts = new Array(".xlsx", ".xls", ".csv");
		var fileExt = sender.value;
		fileExt = fileExt.substring(fileExt.lastIndexOf('.'));
		if (validExts.indexOf(fileExt) < 0) {
			alert("Archivo Inválido. Las extensiones válidas son " + validExts.toString());
			return false;
		}
		else return true;
	}		


	// ------------- FINALIZA RUTINAS DE PROCESOS DE CARGA DE ARCHIVOS -------------------------------------------

	function activaDetalles(tmpSeccionActual){			
	
//		if (sOperacionActual==""){
		
			//alert("Pestaña= " + tmpSeccionActual + "  sOper= " + sOperacionActual);
			
			if(tmpSeccionActual==1){
				//document.all.btnAgregarObjeto.style.display='none';
				//document.all.btnAgregarSujeto.style.display='none';
				
				document.all.btnGuardar.style.display='inline';
				document.all.btnCancelar.style.display='inline';						
				document.all.btnCargaEgreso.style.display='inline';
				document.all.btnCargaIngreso.style.display='inline';

			}
			
			if(tmpSeccionActual==2) {	
				//document.all.btnAgregarSujeto.style.display='inline';
				
				document.all.btnGuardar.style.display='none';
				document.all.btnCancelar.style.display='none';
				document.all.btnCargaEgreso.style.display='none';
				document.all.btnCargaIngreso.style.display='none';				
			}
			


//		}else{
//			alert("Operación en proceso: " + sOperacionActual);
//		}
	}
	
	
	function nuevaCuenta(){
		$('#modalFlotante').removeClass("invisible");
		$('#modalFlotante').modal('toggle');
		$('#modalFlotante').modal('show');
		document.all.txtOperacion.value="INS";
		//////document.all.tabSujetos.style.display='none';		

		recuperarLista('lstAniosCtaPublica/' + document.all.txtOperacion.value, document.all.txtAnio);

		document.all.btnCargaEgreso.style.display='none';
		document.all.btnCargaIngreso.style.display='none';
		limpiarCamposCuentas();
	}
	
	function recuperaCuenta(id){	
		$.ajax({
			type: 'GET', url: '/lstCuentasByID/' + id ,
			success: function(response) {
				var obj = JSON.parse(response);
				limpiarCamposCuentas();

				document.all.txtID.value          ='' + obj.id;
				document.all.txtNombre.value      ='' + obj.nombre;
				document.all.txtFechaInicio.value ='' + obj.inicio;
				document.all.txtFechaFin.value    ='' + obj.fin;			
				document.all.txtNotas.value       ='' + obj.observaciones;

				document.all.txtArchivoOriginal.value        ='' +  obj.archivoOriginal;
				document.all.txtArchivoFinal.value           ='' +  obj.archivoFinal;
				document.all.txtArchivoOriginalIngreso.value ='' + obj.archivoOriginalIngreso;
				document.all.txtArchivoFinalIngreso.value    ='' +  obj.archivoFinalIngreso;

				document.all.btnGuardar.style.display = 'inline';
				document.all.txtOperacion.value       = "UPD";

				//recuperarLista('lstAniosCtaPublica/' + document.all.txtOperacion.value, document.all.txtAnio);
				//seleccionarElemento(document.all.txtAnio, obj.anio);

				recuperarListaSelected('lstAniosCtaPublica', document.all.txtOperacion.value, document.all.txtAnio, obj.anio);

				////HVS recuperarTablaSujetos('tblSujetosByCuenta', id, document.all.tablaSujetos);
				//recuperarTablaObjetos('tblObjetosByCuenta', id, document.all.tablaObjetos);
				
				////HVS document.all.tabSujetos.style.display='block';

				//alert("El valor de document.all.txtArchivoOriginal.value es: " + document.all.txtArchivoOriginal.value );

				if (obj.archivoOriginal.length == 0){ 
					document.all.btnCargaEgreso.style.display='inline'; 
				}else{ 
					document.all.btnCargaEgreso.style.display='none'; 
				}
				
				if (obj.archivoOriginalIngreso.length == 0){
					document.all.btnCargaIngreso.style.display='inline'; 
				}else{  
					document.all.btnCargaIngreso.style.display='none'; 
				}
				
				/*
				if(obj.archivoFinal == ""){
					document.all.btnsCarga.style.display='inline';
					document.all.imgArchivoFinal.style.display='none';
				}else {
					document.all.btnsCarga.style.display='none';
					document.all.imgArchivoFinal.style.display='inline';
				}
				*/

				$('#modalFlotante').removeClass("invisible");
				$('#modalFlotante').modal('toggle');
				$('#modalFlotante').modal('show');								
			},
			error: function(xhr, textStatus, error){
				alert('FUNCTION OBTENER DATOS   -> statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error + ' SECCION: ' + id);
			}			
		});			
	}

	/* 
	// HVS SE Bloquea este código para dejar solamente este archivo como la carga de la cuenta publica, verificar si se requiere cargar 
	// Sujetos y Objetos desde archivos externos.

	function recuperarTablaSujetos(lista, valor, tbl){
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
					renglon.innerHTML="<td>" + dato.id + "</td><td>" + dato.sujeto + "</td><td>" + dato.estatus + "</td>";
					renglon.onclick= function() {
						alert('En Construcción');
					};
					tbl.appendChild(renglon);					
				}				
			},
			error: function(xhr, textStatus, error){
				alert('function recuperarTablaSujetos ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
			}			
		});	
	}	

	function recuperarTablaObjetos(lista, valor, tbl){
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
					renglon.innerHTML="<td>" + dato.sujeto + "</td><td>" + dato.objeto + "</td><td>" + dato.original + "</td><td>" + dato.modificado  + "</td><td>" + dato.ejercido + "</td><td>" + dato.pagado + "</td><td>" + dato.pendiente + "</td>";
					renglon.onclick= function() {
						alert('En Construcción');
					};
					tbl.appendChild(renglon);					
				}				
			},
			error: function(xhr, textStatus, error){
				alert('function recuperarTablaObjetos ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
			}			
		});	
	}		
	
	*/


	function limpiarCamposCuentas(){
		document.all.txtID.value='';
		document.all.txtNombre.value='';
		document.all.txtAnio.selectedIndex=0;
		document.all.txtFechaInicio.value='';
		document.all.txtFechaFin.value='';			
		document.all.txtNotas.value='';		
		document.all.txtArchivoOriginal.value="";
		document.all.txtArchivoFinal.value="";
		document.all.txtArchivoOriginalIngreso.value="";
		document.all.txtArchivoFinalIngreso.value="";
	}
		
	function validarCuenta(){
		
		if (document.all.txtNombre.value==''){
			alert("Debe capturar el campo NOMBRE de la cuenta.");
			document.all.txtNombre.focus();
			return false;
		}	
		if (document.all.txtFechaInicio.value==''){
			alert("Debe capturar el campo FECHA DE INICIO de la cuenta.");
			document.all.txtFechaInicio.focus();
			return false;
		}			
		if (document.all.txtFechaFin.value==''){
			alert("Debe capturar el campo FECHA FIN de la cuenta.");
			document.all.txtFechaFin.focus();
			return false;
		}			
		
		if (document.all.txtAnio.selectedIndex==0){
			alert("Debe seleccionar el AÑO de la cuenta.");
			document.all.txtAnio.focus();
			return false;
		}		
		
		if( document.all.txtOperacion.value == 'INS'){
			var liga = "/validarExisteAnio/" + document.all.txtAnio.value;

			$.ajax({
				type: 'GET', url: liga ,
				success: function(response) {
					var obj = JSON.parse(response);
				
					if( obj.total > 0 ){ 
						alert("Ya existe una cuenta con el año seleccionado, verifique por favor.");
						 return false;
					}else{ 
						return true; 
					}
				},
				error: function(xhr, textStatus, error){
					alert('function validarAnio ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
					return false;
				}			
			});	
		}

		return true;
	}

	
	var nUsr='<?php echo $_SESSION["idUsuario"];?>';
	var nCampana='<?php echo $_SESSION["idCuentaActual"];?>';
	  
	$(document).ready(function(){

		getMensaje('txtNoti',1);
		
		if(nUsr!="" && nCampana!=""){
			//recuperarListaSelected('lstCuentasByUsr', nUsr, document.all.txtCampana,nCampana);
			cargarMenu( nCampana);			
		}else{
			if(nCampana=="")alert("Debe establecer una CUENTA PÚBLICA como PREDETERMINADA. Por favor consulte con su administrador del sistema.");
		}
		
		//recuperarLista('lstSujetos', document.all.txtSujeto);

		document.getElementById('btnUpload').addEventListener('change', abrirArchivo, false);
	
		$( "#btnGuardar" ).click(function() {
			if (validarCuenta()){
			 document.all.formulario.submit();
			}			
		});

		$( "#btnCancelar" ).click(function() {
			document.all.txtArchivoOriginal.value="";
			document.all.txtArchivoFinal.value="";
			document.all.txtArchivoOriginalIngreso.value="";
			document.all.txtArchivoFinalIngreso.value="";

			document.all.capturaDocto.style.display='inline';
			document.all.btnGuardar.style.display='inline';			
			$('#modalFlotante').modal('hide');
		});
		
/*
	$( "#btnAgregarObjeto" ).click(function() {		
		sOperacionActual="Registrando un Objeto de Fiscalización";
		document.all.divListaRegistrosAnalitico.style.display='none';
		document.all.divCapturaRegistroObjeto.style.display='inline';
		
		document.all.btnAgregarObjeto.style.display='none';
		document.all.btnGuardarObjeto.style.display='inline';
		document.all.btnCancelarObjeto.style.display='inline';		
	});
	*/
	
	$( "#btnGuardarObjeto" ).click(function() {
		sOperacionActual="";
		document.all.divListaRegistrosAnalitico.style.display='inline';
		document.all.divListaRegistrosAnalitico.style="height:350px; overflow: auto; overflow-x:hidden;"	
		document.all.divCapturaRegistroObjeto.style.display='none';
		
		//document.all.btnAgregarObjeto.style.display='inline';
		document.all.btnGuardarObjeto.style.display='none';
		document.all.btnCancelarObjeto.style.display='none';	
	});	
	

	$( "#btnCancelarObjeto" ).click(function() {
		sOperacionActual="";
		document.all.divListaRegistrosAnalitico.style.display='inline';
		document.all.divListaRegistrosAnalitico.style="height:350px; overflow: auto; overflow-x:hidden;"		
		document.all.divCapturaRegistroObjeto.style.display='none';
		
		//document.all.btnAgregarObjeto.style.display='inline';
		document.all.btnGuardarObjeto.style.display='none';
		document.all.btnCancelarObjeto.style.display='none';	
	});	
	
	$( "#btnCargaEgreso" ).click(function() {
		tipoCarga="Egreso";
		$("#btnUpload").click();
	});		
	
	$( "#btnCargaIngreso" ).click(function() {
		tipoCarga="Ingreso";
		$("#btnUpload").click();
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
			  
			  <li class="open"><a href="/cerrar"><i class="fa fa-sign-out"></i> Salir</a></li>		  
			  
			</ul>
		</div> <!-- Sidebar ends -->

			<!-- Main bar -->
		<div class="mainbar">      
			<div class="col-md-12">
				<div class="widget">
					<div class="widget-head">
					  <div class="pull-left"><h3 class="pull-left"><i class="fa fa-home"></i> Catálogo de Cuentas  Públicas</h3></div>
					  <div class="widget-icons pull-right">
						<button onclick="nuevaCuenta();" type="button" class="btn btn-primary  btn-xs">Nueva Cuenta Pública...</button> 						
					  </div>  
					  <div class="clearfix"></div>
					</div>             
					<div class="widget-content">						
						<div class="table-responsive" style="height: 400px; overflow: auto; overflow-x:hidden;">
							<table class="table table-striped table-bordered table-hover">
								<thead>
									<tr><th>ID</th><th>Descripción</th><th>Desde</th><th>Hasta</th><th>Estatus</th></tr>
								</thead>
								<tfoot><tr><td colspan=5></td></tr></tfoot>
								<tbody >									  
									<?php foreach($datos as $key => $valor): ?>
									<tr onclick=<?php echo "javascript:recuperaCuenta('" . $valor['id'] . "');"; ?> style="width: 100%; font-size: small;">
									  <td><?php echo $valor['id']; ?></td>
									  <td><?php echo $valor['nombre']; ?></td>
									  <td><?php echo $valor['inicio']; ?></td>
									  <td><?php echo $valor['fin']; ?></td>
									  <td><?php echo $valor['estatus']; ?></td>
									</tr>
									<?php endforeach; ?>	
									
								</tbody>
							</table>
						</div>							
					</div>
					<div class="widget-foot"></div>
				</div>
			</div>
			<!-- Matter ends -->
		</div>
	   <!-- Mainbar ends -->
	   <div class="clearfix"></div>
	</div>
<!-- Content ends -->

	<div id="modalFlotante" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<form id="formulario" METHOD='POST' action='/guardar/catCuentas' role="form">
			<input type='HIDDEN' name='txtValores' value=''>						
			<input type='HIDDEN' name='txtOperacion' value=''>
			<input type='HIDDEN' name='txtArchivoFinal' value=''>
			<input type='HIDDEN' name='txtArchivoFinalIngreso' value=''>

			<!-- Modal content-->
			<div class="modal-content">									
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Registrar Cuenta Pública...</h4>
				</div>									
				<div class="modal-body">
					<div class="container-fluid">
						<div class="row">
						
							<div class="col-md-12">
								<ul class="nav nav-tabs">																		
									<li class="active"><a href="#tab-generales" data-toggle="tab"  onclick="activaDetalles(1);"> Datos Generales <i class="fa"></i></a></li>
									<!--
									<li><a href="#tab-sujetos" data-toggle="tab" onclick="activaDetalles(2);" style="display:none;" id="tabSujetos"> Sujetos<i class="fa"></i></a></li>
									-->
								</ul>								
								<div class="tab-content">
									<div class="tab-pane active" id="tab-generales">
										<div id="capturaDocto">
											<br>
											<div class="form-group">
												<label class="col-xs-2 control-label" style="text-align:right">ID</label>
												<div class="col-xs-2">
													<input type="text" class="form-control" name="txtID" readonly="readonly" />
												</div>																							
											</div>
											<br>
				
											<div class="form-group">
												<label class="col-xs-2 control-label" style="text-align:right">Año</label>
												<div class="col-xs-2">
													<select class="form-control" name="txtAnio">
														<option value="">Seleccione...</option>														
													</select>
												</div>
											</div>
											<br>

											<div class="form-group">
												<label class="col-xs-2 control-label" style="text-align:right">Nombre</label>
												<div class="col-xs-9"><input type="text" class="form-control" name="txtNombre"/></div>				
											</div>
											<br>


											<div class="form-group">
												<label class="col-xs-2 control-label" style="text-align:right">Inicia</label>
												<div class="col-xs-2">
													<input type="text" id="datepicker" class="form-control" name="txtFechaInicio" placeholder="Fecha Inicio"/>
												</div>
												<label class="col-xs-5 control-label" style="text-align:right">Termina</label>	
												<div class="col-xs-2">
													<input type="text" id="datepicker2" class="form-control" name="txtFechaFin" placeholder="Fecha de Término"/>
												</div>
											</div>
											<br>
											<div class="clearfix"></div>

											<div class="form-group">
												<label class="col-xs-2 control-label" style="text-align:right">Notas</label>						<div class="col-xs-9">
													<textarea class="form-control" rows="3" placeholder="Resumen" name="txtNotas" id="txtNotas"></textarea>
												</div>													
											</div>
											<div class="clearfix"></div>
											<br>

											<div class="form-group">
												<label class="col-xs-2 control-label" style="text-align:right">Archivo Egresos <img src="img/xls.gif"></label>
												<div class="col-xs-9">
													<input type="text" class="form-control" name="txtArchivoOriginal" readonly="readonly"/>
												</div>	
											</div>	
											<br>

											<div class="form-group">
												<label class="col-xs-2 control-label" style="text-align:right">Archivo Ingresos <img src="img/xls.gif"></label>
												<div class="col-xs-9">
													<input type="text" class="form-control" name="txtArchivoOriginalIngreso" readonly="readonly" />
												</div>	
											</div>					
										</div>
									</div>
									<!--
									<div class="tab-pane " id="tab-sujetos">
										<div class="table-responsive" style="height: 350px; overflow: auto; overflow-x:hidden;" 		id="divListaRegistrosSujetos">
											<table class="table table-striped table-bordered table-hover table-condensed">
												<thead>
													<tr><th>Id</th><th>Sujeto</th><th>Estatus</th></tr>
												</thead>
												<tbody id="tablaSujetos"></tbody>
											</table>											
										</div>
										<div class="pull-right"><button  type="button" class="btn btn-primary active btn-xs" id="btnAgregarSujeto" 		style="display:none;"><i class="fa fa-file-text-o"></i> Agregar Sujeto</button>
										</div>				
									</div>
									-->
								</div>
							</div>
						</div>
						<div class="clearfix"></div>
					</div>
				</div>
				
				<div class="modal-footer">
					<div class="pull-left"  id="imgCargando"  style="display:none;"><h3><img src="img/giphy.gif"> Cargando...</h3></div>
					<div class="pull-left"  id="imgArchivoFinal"  style="display:none;"><img src="img/xls.gif"> Cuenta Pública.xlsx  <a><i class="fa fa-download"></i></a></div>					
					<div id="btnsCarga">
						<div class="pull-left">					
							<form id="upload_form" enctype="multipart/form-data" method="post">								
									<button  type="button" class="btn btn-default" id="btnCargaEgreso" style="display:none;background-color:#DDC873"><i class="fa fa-link"></i> Cargar Egreso...</button>
									<button  type="button" class="btn btn-default" id="btnCargaIngreso" style="display:none;background-color:#DDC873"><i class="fa fa-link"></i> Cargar Ingreso...</button>									
									<input type="file" name="btnUpload" accept="application/xlsx,application/vnd.ms-excel,application/pdf," style="display:none;" id="btnUpload" value="Cargar #1">
									<h3 id="status"></h3>									
							</form>
						</div>							
						<div class="pull-right">
							<button  type="button" class="btn btn-primary btn-xs" id="btnGuardarObjeto" 	style="display:none;"><i class="fa fa-floppy-o"></i> Guardar Objeto</button>
							<button  type="button" class="btn btn-default btn-xs" id="btnCancelarObjeto" 	style="display:none;"><i class="fa fa-undo"></i> Cancelar Objeto</button>

							<button  type="button" class="btn btn-primary btn-xs" id="btnGuardarSujeto" 	style="display:none;"><i class="fa fa-floppy-o"></i> Guardar Sujeto</button>
							<button  type="button" class="btn btn-default btn-xs" id="btnCancelarSujeto" 	style="display:none;"><i class="fa fa-undo"></i> Cancelar Sujeto</button>								
					
						</div>
					</div>
					
					<button  type="button" class="btn btn-primary active" id="btnGuardar" 	style="display:inline;"><i class="fa fa-floppy-o"></i> 
					Guardar Cuenta</button>	
					<button  type="button" class="btn btn-default active" id="btnCancelar" 	style="display:inline;"><i class="fa fa-undo"></i> Cancelar</button>

					<div class="clearfix"></div>									
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