<!DOCTYPE html>
<html lang="en"><head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">	
	<meta http-equiv="X-UA-Compatible" content="IE=Edge" />  
	<script type='text/javascript' src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js?ver=3.1.2"></script> 
	<script type="text/javascript" src="js/genericas.js"></script>
	
	<style type="text/css">		
		@media screen and (min-width: 768px) {
			#mapa_content {width:100%; height:450px;}
			#modalPapel .modal-dialog  {width:80%;}
			.delimitado {  
				height: 350px !important;
				overflow: scroll;
			}​			
		}
	</style>

  <script type="text/javascript"> 
  


	function limpiarCampos(){
		document.all.txtTipoPapel.value="";
		document.all.txtProgramadaPapel.selectedIndex= 0;
		document.all.txtNombrePapel.value="";
		document.all.txtEstausPapel.selectedIndex= 1;
		document.all.txtModulosDestino.options.length = 0;
		document.all.txtArchivoOriginal.value="";
		document.all.txtNomcla.value="";
		
	}
	
	function limpiarArch(){
		document.all.txtArchivoOriginal.value="";
		document.all.txtArchivoFinal.value="";
	}
				


	function validarCaptura(){
		if (document.all.txtTipoPapel.value==""){alert("Debe de capturar un ID ."); document.all.txtTipoPapel.focus();return false;}
		if (document.all.txtNombrePapel.value==""){alert("Debe capturar el NOMBRE DEL PAPEL.");document.all.txtNombrePapel.focus();return false;}
		if (document.all.txtProgramadaPapel.value==""){alert("Debe de seleccionar si va a ser PROGRAMADA.");document.all.txtProgramadaPapel.focus();return false;}
		if (document.all.txtEstausPapel.value==""){alert("Debe de seleccionar un ESTATUS.");document.all.txtEstausPapel.focus();return false;}
		//if (document.all.txtModulosDestino.value==""){alert("Debe Asignar las FASE(S) al tipo de papel.");document.all.txtModulosDestino.focus();return false;}
		if (document.all.txtArchivoOriginal.value=="" && document.all.txtProgramadaPapel.value=="NO"){alert("Debe seleccionar un ARCHIVO.");document.all.btnCargarArchivo.focus();return false;}
		//if (document.all.txtProgramadaPapel.value=="SI"){alert("Debe seleccionar un ARCHIVO.");document.all.btnCargarArchivo.focus();return false;}
		return true;
	}


	function validarEsta(){
	 	if (confirm("¿Esta seguro que desea desactivar este PAPEL DE TRABAJO?:\n \n")){
	 	}
	}
	

	function validarID(idpapel){

		if (idpapel==''){
			if (document.all.txtNombrePapel.value==""){alert("Debe de capturar un ID PAPEL ."); document.all.txtTipoPapel.focus();return false;}
			return true;
		}else{
			validar(idpapel);
		}
	}

	function validarbtn(valor){

		if (valor=="SI"){
			document.all.btnCargarArchivo.style.display='none';
		}else{
			document.all.btnCargarArchivo.style.display='inline';
		}
	}

	function validar(idpapel){

	$.ajax({
	    type: 'GET', url: '/validpapel/' + idpapel ,
	    success: function(response) {
        	var obj = JSON.parse(response);                              

		    	if(idpapel==obj.pap){
		    		alert("EL ID que tecleaste, se encuentra asignado a otro PAPEL DE TRABAJO");
		    		document.all.txtTipoPapel.value="";
		    		document.all.txtTipoPapel.focus()

		    	}
            
	    },
	    error: function(xhr, textStatus, error){
	    alert('func: validar ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
	    }                                             
	});      
	}



	function desactivar(valor){
		if(valor==''){
			document.getElementById("idpapel").readOnly=true;
		}else{
			document.getElementById("idpapel").readOnly=false;
		}


	}


	function obtenerActi(sUrl){

		recuperarListaLarga('lstFasesActividad', document.all.txtModulosOrigen);
		recuperarListaLarga('lstPapelesFases', document.all.txtAreasOrigen)
		//alert("sUrl:  "+ sUrl);
		$.ajax({
			type: 'GET', url: sUrl ,
			success: function(response) {
			 var obj = JSON.parse(response);
	

				limpiarCampos();
				document.all.txtoperacion.value= "UPD";
				document.all.txtTipoPapel.value='' + obj.idt;
				document.all.txtNombrePapel.value='' + obj.nombre;
				document.all.btnEliminarArchivo.style.display='inline';
				document.all.btnCargarArchivo.style.display='none';
	
				seleccionarElemento(document.all.txtProgramadaPapel, obj.programada);

				document.all.txtArchivoOriginal.value=obj.archivoOriginal;
				document.all.txtArchivoFinal.value=obj.archivoFinal;
				document.all.txtEstausPapel.value='' + obj.estatus;
				document.all.txtNomcla.value='' +obj.nomenclatura;
				document.all.nom.value='' +obj.nomenclatura;
            	document.getElementById('idpapel').readOnly=true;
            	//document.getElementById('Nomcla').readOnly=true;
            	document.getElementById('NombrePapel').readOnly=true;
            	

            	var esta = obj.programada;
            	
            	if(esta=='SI'){
            		document.all.status.style.display='none';
            		document.all.btnEliminarArchivo.style.display='none';
            	}




            	if(obj.programada=="NO"){
					document.all.status.style.display='inline';
					//var arch = document.all.txtArchivoOriginal.value
					//alert("veamos: " + arch);
					//document.all.status.innerHTML= "<img src='img/obtenerGifs(document.all.txtArchivoOriginal.value);'>" + document.all.txtArchivoOriginal.value;
					document.all.status.innerHTML="<img src='img/xls.gif'>" + document.all.txtArchivoOriginal.value; 
					
            	}else{
            		document.all.status.style.display='none';	
            	}

            	var cadena = '/lstpapelesbyfases/' + obj.idt;

            	document.all.txtModulosDestino.options.length = 0;
 				$.ajax({
		           type: 'GET', url: cadena ,
		           success: function(response) {
	                   var jsonData = JSON.parse(response);                                 
	                                  
                   		//alert("response: " + response);

	                   	if(jsonData.datos.length > 0){

	               			// Agregara los Módulos que tiene asignado el rol a el combo destino
		                   	for (var i = 0; i < jsonData.datos.length; i++) {
								var dato = jsonData.datos[i];  
		                   		//alert("Valor de dato.id: " + dato.id + " - dato.texto: " + dato.texto);
								var option = document.createElement("option");
								option.value = dato.id;
								option.text = dato.texto;
								document.all.txtModulosDestino.add(option, document.all.txtModulosDestino.length);
								// Quitara del combo origen los modulos que tiene asignado el rol.
			                   	for (var j = 0; j < document.all.txtModulosOrigen.options.length; j++) {
			                   		if (document.all.txtModulosOrigen[j].value == dato.id){
			                   			document.all.txtModulosOrigen.remove(j);
			                   		}
								}
			                }
	                   }
			        },
			          error: function(xhr, textStatus, error){
			               alert('func: obtenerDatos ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
			        }                                             
				});


            	var cadena = '/lstAreasbyPapeles/' + obj.idt;
            	//alert("Valor de cadena: "+ cadena);
            	document.all.txtAreasDestino.options.length = 0;

 				$.ajax({
		           type: 'GET', url: cadena ,
		           success: function(response) {
	                   var jsonData = JSON.parse(response);                                 
	                                  
                   		//alert("response: " + response);

	                   	if(jsonData.datos.length > 0){

	               			// Agregara los Módulos que tiene asignado el rol a el combo destino
		                   	for (var i = 0; i < jsonData.datos.length; i++) {
								var dato = jsonData.datos[i];  
		                   		//alert("Valor de dato.id: " + dato.id + " - dato.texto: " + dato.texto);
								var option = document.createElement("option");
								option.value = dato.id;
								option.text = dato.texto;
								document.all.txtAreasDestino.add(option, document.all.txtAreasDestino.length);
								// Quitara del combo origen los modulos que tiene asignado el rol.
			                   	for (var j = 0; j < document.all.txtAreasOrigen.options.length; j++) {
			                   		if (document.all.txtAreasOrigen[j].value == dato.id){
			                   			document.all.txtAreasOrigen.remove(j);
			                   		}
								}
			                }
	                   }
			        },
			          error: function(xhr, textStatus, error){
			               alert('func: obtenerDatos ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
			        }                                             
				});


 				ordenarListaByTexto(document.all.txtAreasOrigen);
				ordenarListaByTexto(document.all.txtAreasDestino);

     			ordenarListaByTexto(document.all.txtModulosOrigen);
				ordenarListaByTexto(document.all.txtModulosDestino);

				$('#modalPapel').removeClass("invisible");
				$('#modalPapel').modal('toggle');
				$('#modalPapel').modal('show');
								

			},
			error: function(xhr, textStatus, error){
				alert('statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
			}			
		});			
	}		
		

	function moverRol(cmb1, indice, cmb2){
		
		var bDuplicado=false;
		var valorBuscado = cmb1.options[indice].value;
			
		//Revisar duplicados
		var nPos=0;
		while (nPos <cmb2.length){
			if(cmb2.options[nPos].value==valorBuscado){
				bDuplicado=true;
				break;
			}	
			nPos++;
		}
		
		//Transferir el elemento
		if(bDuplicado==true){
			alert("Duplicado.");
			return  false;
		}else{
			//Agregar elemento a cmb2
			var option = document.createElement("option");
			option.text = cmb1.options[indice].text;
			option.value = cmb1.options[indice].value;
			cmb2.add(option, cmb2.length);
			
			//Eliminar elemento del cmb1
			cmb1.remove(indice);
		}
		return true;
	}	

	function ordenarListaByTexto(cmb) {
	    var tmpAry = new Array();
	    for (var i=0;i<cmb.options.length;i++) {
	        tmpAry[i] = new Array();
	        tmpAry[i][0] = cmb.options[i].text;
	        tmpAry[i][1] = cmb.options[i].value;
	    }
	    tmpAry.sort();
	    while (cmb.options.length > 0) {
	        cmb.options[0] = null;
	    }
	    for (var i=0;i<tmpAry.length;i++) {
	        var op = new Option(tmpAry[i][0], tmpAry[i][1]);
	        cmb.options[i] = op;
	    }
	    return;
	}

	function moverRight(){
		var c1 = document.all.txtModulosOrigen;
		var c2 = document.all.txtModulosDestino;
			
		if (moverRol(c1, c1.selectedIndex, c2)==false){
			alert("Ya existe la fase.");
		}else{
			ordenarListaByTexto(document.all.txtModulosOrigen);
			ordenarListaByTexto(document.all.txtModulosDestino);
			//alert("Rol Asignado!");
		}
	}

	function moverLeft(){
		var c1 = document.all.txtModulosDestino;
		var c2 = document.all.txtModulosOrigen;			
		if (moverRol(c1, c1.selectedIndex, c2)==false){
			alert("Ya existe la fase.");
		}else{
			ordenarListaByTexto(document.all.txtModulosOrigen);
			ordenarListaByTexto(document.all.txtModulosDestino);
		}
	}


	function moverRightpAr(){
		var c1 = document.all.txtAreasOrigen;
		var c2 = document.all.txtAreasDestino;
			
		if (moverRol(c1, c1.selectedIndex, c2)==false){
			alert("Ya existe el Área.");
		}else{
			ordenarListaByTexto(document.all.txtAreasOrigen);
			ordenarListaByTexto(document.all.txtAreasDestino);
		}
	}

	function moverLeftAr(){
		var c1 = document.all.txtAreasDestino;
		var c2 = document.all.txtAreasOrigen;			
		if (moverRol(c1, c1.selectedIndex, c2)==false){
			alert("Ya existe el Área.");
		}else{
			ordenarListaByTexto(document.all.txtAreasOrigen);
			ordenarListaByTexto(document.all.txtAreasDestino);
		}
	}


	/*function obtenerGifs(sender) {
		alert(sender);
		var validExts = new Array(".xlsx", ".xls", ".pdf", ".doc", ".docx");
		var fileExt = sender;
		//var gif ="";
		fileExt = fileExt.substring(fileExt.lastIndexOf('.'));
		alert(fileExt);
		if (fileExt == ".docx") {
		  alert("El ARCHIVO ES " +	fileExt);
		  	gif =  "doc.gif";
		  	alert(gif);
		  //return true;
		}
		else return true;

		return gif;
	}
*/

	function validarNomen(nomen){

	$.ajax({
	    type: 'GET', url: '/validnomen/' + nomen ,
	    success: function(response) {
        	var obj = JSON.parse(response);                              

		    	if(nomen==obj.nomen){
		    		alert("La nomenclatura que tecleaste, se encuentra asignado a otro PAPEL DE TRABAJO");
		    		document.all.txtNomcla.value="";
		    		document.all.txtNomcla.focus()

		    	}
            
	    },
	    error: function(xhr, textStatus, error){
	    alert('func: validar ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
	    }                                             
	});      
	}





	function checkfile(sender) {
		var validExts = new Array(".xlsx", ".xls", ".doc", ".docx");
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
	var nomarc;
	function abrirArchivo(){
		var formdata = new FormData();
		
		if(checkfile(this)){
			var file = this.files[0];
			//if (file.size>100000) alert("El tamaño del archivo no debe exceder 100,000 bytes.");	
			sArchivoCarga = file.name;
			nomarc = document.getElementById('nom2').value;
			//alert (nomarc);
			formdata.append("btnUpload", file);
			formdata.append("nom2",nomarc);
			var ajax = new XMLHttpRequest();
			ajax.upload.addEventListener("progress", progressHandler, false);
			ajax.addEventListener("load", completeHandler, false);
			ajax.addEventListener("error", errorHandler, false);
			ajax.addEventListener("abort", abortHandler, false);				
			ajax.open("POST", "uploadCatalogoPa.php");
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
		document.all.progressBar.style="display:none";
		document.all.progressBar.value= 0;			
		if(event.target.responseText!="ERROR"){				
			document.all.txtArchivoOriginal.value=sArchivoCarga;
			document.all.txtArchivoFinal.value=event.target.responseText;
			document.all.status.style.display='inline';
			document.all.status.innerHTML="<img src='img/xls.gif'>" + document.all.txtArchivoOriginal.value; 
			document.all.btnCargarArchivo.style.display="none";	
		}else{
			document.all.txtArchivoOriginal.value="";
			document.all.txtArchivoFinal.value="";
			alert("El archivo no subió correctamente.");
		}			
	}
	function errorHandler(event){document.all.status.innerHTML = "Falló la carga";}			
	function abortHandler(event){document.all.status.innerHTML = "Se abrotó la carga.";}	

				
	function valtex(){
		document.getElementById('nom2').value = document.getElementById('Nomcla').value;

	}
		


	
	var nUsr='<?php echo $_SESSION["idUsuario"];?>';		
	var nCampana='<?php echo $_SESSION["idCuentaActual"];?>';	
	
	$(document).ready(function(){
		setInterval(getMensaje('txtNoti'),60000);

		document.getElementById('btnUpload').addEventListener('change', abrirArchivo, false);

		if(nUsr!="" && nCampana!=""){
			cargarMenu( nCampana);			
		}else{
			if(nCampana=="")alert("Debe establecer una CAMPAÑA como PREDETERMINADA. Por favor consulte con su administrador del sistema.");
		}			
		
		
		
		$("#btnAgregarPapel" ).click(function() {
			recuperarListaLarga('lstFasesActividad', document.all.txtModulosOrigen);
			recuperarListaLarga('lstPapelesFases', document.all.txtAreasOrigen)
			document.getElementById('idpapel').readOnly=false;
			document.getElementById('Nomcla').readOnly=false;
			document.getElementById('NombrePapel').readOnly=false;

			document.all.btnEliminarArchivo.style.display='none';
			
			document.all.status.style.display='none';
			
			limpiarCampos();	
			document.all.txtoperacion.value='INS';
			$('#modalPapel').removeClass("invisible");
			$('#modalPapel').modal('toggle');
			$('#modalPapel').modal('show');
		});


		$("#btnAgregarRol" ).click(function() {
			moverRight();
		});	

		$("#btnAgregarArea" ).click(function() {
			moverRightAr();
		});


		$("#btnQuitarRol" ).click(function() {			
			moverLeft();
		});

		$("#btnQuitarArea" ).click(function() {			
			moverLeftAr();
		});	

		$( "#btnEliminarArchivo" ).click(function() {
			if (confirm("¿Esta seguro que desea ELIMINAR el ARCHIVO ADJUNTO\n \n")){
				limpiarArch();
				document.all.btnEliminarArchivo.style.display='none';
				document.all.status.style.display='none';
				document.all.btnCargarArchivo.style.display='inline';
			 	}

		});


		$( "#btnCargarArchivo" ).click(function() { $("#btnUpload").click();});

		$("#btnGuardar" ).click(function() {
			if(validarCaptura())
			{
				var sRoles = "";
				var sAreas = "";
				if(document.all.txtModulosDestino.length > 0){
					var separador = "";
					for(i=0; i < document.all.txtModulosDestino.length; i++){
						sRoles = sRoles + separador + document.all.txtModulosDestino[i].value + '|' + document.all.txtModulosDestino[i].text ;
						separador = '*';
					}
				}
				document.all.txtFases.value = sRoles;

				if(document.all.txtAreasDestino.length > 0){
					var separador = "";
					for(i=0; i < document.all.txtAreasDestino.length; i++){
						sAreas = sAreas + separador + document.all.txtAreasDestino[i].value + '|' + document.all.txtAreasDestino[i].text ;
						separador = '*';
					}
				}
				document.all.txtAreas.value = sAreas;

				//alert("txtNombre: " + document.all.txtFases.value);

				//alert("txtNombre: " + document.all.txtAreas.value);
			document.all.formulario.submit();
			}
		});	


		$("#btnCancelar" ).click(function() {
			document.all.txtoperacion.value='';			
			$('#modalPapel').removeClass('invisible');
			$('#modalPapel').modal('toggle');
			$('#modalPapel').modal('hide');	
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
  <!--<link rel="stylesheet" href="css/fullcalendar.css">-->
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
					<div class="col-xs-3"><h2>Catálogo de Papeles</h2></div>									
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
        <h2 class="pull-left"><i class="fa fa-table"></i> Catálogo de Papeles de Trabajo</h2>

        <!-- Breadcrumb -->
        <div class="bread-crumb pull-right">
          <a href="index.html"><i class="fa fa-home"></i> Home</a> 
          <!-- Divider -->
          <span class="divider">/</span> 
          <a href="#" class="bread-current">Usuarios</a>
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
							  <div class="pull-left">Lista de Tipos de Papel</div>
							  <div class="widget-icons pull-right">
							  </div>  
							  <div class="clearfix"></div>
							</div>
						
							<div class="widget-content" >	
								<div class="table-responsive" style="height: 500px; overflow: auto; overflow-x:hidden;">														
									<div class="">
										<table class="table table-striped table-bordered table-hover">
										  <thead>
											<tr>
											  <th>ID</th>
											  <th>Nombre</th>							  
											  <th>Programada</th>							  
											  <th>Estatus</th>
											  <th>Documento</th>  
											</tr>
										  </thead>
										  <tbody>
											<?php foreach($datos as $key => $valor): ?>
											<tr>
											
											  	<td onclick=<?php echo "obtenerActi('/ActiviPapeles/" . $valor['id'] . "');"; ?>><?php echo $valor['id']; ?></td>
												<td onclick=<?php echo "obtenerActi('/ActiviPapeles/" . $valor['id'] . "');"; ?>><?php echo $valor['nombre']; ?></td>
												<td onclick=<?php echo "obtenerActi('/ActiviPapeles/" . $valor['id'] . "');"; ?>><?php echo $valor['programada']; ?></td>
												<td onclick=<?php echo "obtenerActi('/ActiviPapeles/" . $valor['id'] . "');"; ?>><?php echo $valor['estatus']; ?></td>
												<td>
													<a href="/plantillas/<?php echo $valor['archivoFinal']; ?>"><?php $va = $valor['archivoFinal']; if($va=='' ){}else{echo obtenerGif($valor['archivoFinal']);} ?></a>
												</td>

											 
											</tr>
											<?php endforeach; ?>						                                                                    
										  </tbody>
										</table>
									</div>
								</div>
							</div>
							
							<div class="widget-foot">
								<button id="btnAgregarPapel" class="btn btn-primary btn-xs">Agregar Papel</button>
								<div class="clearfix"></div> 
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
   								  						   



<div id="modalPapel" class="modal fade" role="dialog"> 
	<div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">	
			<!-- Modal header-->
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Datos del Papel de Trabajo..</h4>
			</div>										
			<!-- Modal body-->
			<div class="modal-body">
				<!-- Inicio del Form-->

				<form id="formulario" METHOD='POST' action='/guardar/papeles' role="form">								
					<input type='HIDDEN' name='txtoperacion' value=''>
					<input type='HIDDEN' name='txtFases' value=''>
					<input type='HIDDEN' name='txtAreas' value=''>					
					<input type='HIDDEN' name='txtArchivoOriginal' value=''>
					<input type='HIDDEN' name='txtArchivoFinal' value=''>

					<div class="clearfix"></div>
					<div class="form-group">
						<label class="col-xs-1 control-label" style="text-align:right">ID Papel </label>
						<div class="col-xs-2">
							<input type="text" class="form-control" id="idpapel" name="txtTipoPapel" />
							<!--<input type="text" class="form-control" id="idpapel" name="txtTipoPapel" onblur="validar(idpapel.value)" />-->
						</div>
						<label class="col-xs-1 control-label" style="text-align:right">Nomenclatura </label>
						<div class="col-xs-1">
							<input type="text" class="form-control" id="Nomcla" name="txtNomcla" onchange="valtex(); validarNomen(this.value);" />
						</div>		
						<label class="col-xs-2 control-label" style="text-align:right">Nombre Papel</label>
						<div class="col-xs-5">
							<input type="text" class="form-control" name="txtNombrePapel" id="NombrePapel" onfocus="validarID(idpapel.value);" />
						</div>													
					</div>			
					<br>
					<div class="clearfix"></div>

					<div class="form-group">
						<label class="col-xs-1 control-label" style="text-align:right">Programada</label>
						<div class="col-xs-2">
							<select class="form-control" name="txtProgramadaPapel" onchange="validarbtn(this.value);">
								<option value="">Seleccione...</option>
								<option value="SI" selected>SI</option>
								<option value="NO">NO</option>
							</select>
						</div>
						<label class="col-xs-5"></label>
						<label class="col-xs-2 control-label" style="text-align:right">Estatus</label>
						<div class="col-xs-2">
							<select class="form-control" name="txtEstausPapel">
								<option value="">Seleccione...</option>
								<option value="ACTIVO" selected>ACTIVO</option>
								<option value="INACTIVO" onclick="validarEsta();">INACTIVO</option>
							</select>
						</div>											
					</div>											

				</form>
					<br>
				<br>
				<div class="form-group">
					<label class="col-xs-1 control-label"></label>
					<div class="col-xs-5">
						<strong>Fases Disponibles</strong><br>
						<select class="form-control" name="txtModulosOrigen" size=4 onDblClick="moverRight();" ></select>
						<div  class="pull-right"><button id="btnAgregarRol" class="btn btn-warning btn-xs">Agregar >></button>	
						</div>
					</div>
					<div class="col-xs-5">
						<strong>Fases Asignados</strong><br>
						<select class="form-control" name="txtModulosDestino" size=4 onDblClick="moverLeft();" ></select>
						<button id="btnQuitarRol" class="btn btn-warning btn-xs"><< Quitar</button>	
					</div>
					<div class="clearfix"></div>												
				</div>

				<div class="form-group">
					
					<div class="col-xs-6">
						<strong>Áreas Disponibles</strong><br>
						<select class="form-control" name="txtAreasOrigen" size=10 onDblClick="moverRightpAr();" ></select>
						<div  class="pull-right"><button id="btnAgregarArea" class="btn btn-warning btn-xs">Agregar >></button>	
						</div>
					</div>
					<div class="col-xs-6">
						<strong>Áreas Asignadas</strong><br>
						<select class="form-control" name="txtAreasDestino" size=10 onDblClick="moverLeftAr();" ></select>
						<button id="btnQuitarArea" class="btn btn-warning btn-xs"><< Quitar</button>	
					</div>
					<div class="clearfix"></div>												
				</div>


					<div class="clearfix"></div>


			</div>
			<!--   COMENTARIOS  : AQUI VA EL FOOTER MODAL -->
			<div class="modal-footer">
 				<div class="pull-left">
					<form id="upload_form" enctype="multipart/form-data" method="post">
						<input type="text"  style="display:none;" name="nom" id="nom2"></input>
						<button  type="button" class="btn btn-danger" id="btnEliminarArchivo" style="display:none;"><i class="fa fa-trash-o fa-lg"></i></button>
						<button  type="button" class="btn btn-default" id="btnCargarArchivo" style="display:none;"><i class="fa fa-link"></i> Anexar Archivo...</button>
						<input type="file" name="btnUpload" accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document" style="display:none;" id="btnUpload">
						<progress id="progressBar" value="0" max="100" style="width:'100%'; display:none;"></progress>
						<h4 id="status"></h4>
						<p id="lblAvances"></p>
					</form>
				</div>
				<div class="pull-right">
					<button id="btnGuardar" class="btn btn-primary btn-xs active">Guardar datos</button>	
					<button id="btnCancelar" class="btn btn-default btn-xs">Cancelar</button>	
				</div>
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
<script src="./Dashboard - MacAdmin_files/custom.js"></script>  <!--Custom codes -->


<!-- Charts & Graphs -->

<!-- 
<script src="./Dashboard - MacAdmin_files/charts.js"></script> 

<script src="./Dashboard - MacAdmin_files/moment.min.js"></script> 
<script src="./Dashboard - MacAdmin_files/fullcalendar.min.js"></script> 
<script src="./Dashboard - MacAdmin_files/jquery.rateit.min.js"></script> 
<script src="./Dashboard - MacAdmin_files/jquery.prettyPhoto.js"></script>
-->
</body></html>