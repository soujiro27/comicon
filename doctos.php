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
		<script src="jquery.ui.datepicker-es.js"></script>

	
		<style type="text/css">		
			@media screen and (min-width: 768px) {
				#cvGrafica1{height:250px; width:100%;}			
				#modalDocto .modal-dialog  {width:70%;}
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
			var lstAuditorias = new Array();


			window.onload = function () {
				var chart; 				
				setGrafica(chart, "dpsDoctosByAuditoria", "bar", "", "cvGrafica1" );
			  };


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
			        dateFormat: 'dd/mm/yy',
			        firstDay: 1,
			        isRTL: false,
			        showMonthAfterYear: false,
			        yearSuffix: ''
			    };

				$.datepicker.setDefaults($.datepicker.regional["es"]);
				
				$("#datepicker").datepicker({
				dateFormat:"yy-mm-dd",
				firstDay: 1,
				numberOfMonths: 2,
				onClose: function( selectedDate ) { $( "#datepicker2" ).datepicker( "option", "minDate", selectedDate ); }
				});

				$("#datepicker2").datepicker({
				dateFormat:"yy-mm-dd",
				firstDay: 1,
				numberOfMonths: 2,
				onClose: function( selectedDate ) { $( "#datepicker3" ).datepicker( "option", "minDate", selectedDate ); }
				});


				$("#datepicker3").datepicker({
				dateFormat:"yy-mm-dd",
				firstDay: 1,
				numberOfMonths: 2,
				onClose: function( selectedDate ) { $( "#datepicker2" ).datepicker( "option", "maxDate", selectedDate ); }
				});

			});



			function validarCaptura(){				

				if (document.all.txtNumero.value==""){ alert("Debe capturar el NÚMERO del DOCUMENTO.");
					 document.all.txtNumero.focus(); return false; 	}

				if (document.all.txtTipo.selectedIndex==0){ alert("Debe seleccionar el TIPO del DOCUMENTO.");
					 document.all.txtTipo.focus(); return false; 	}

				if (document.all.txtFlujo.selectedIndex==0){ alert("Debe seleccionar el FLUJO del DOCUMENTO.");
					 document.all.txtFlujo.focus(); return false; 	}

				if (document.all.txtFecDocto.value==""){ alert("Debe seleccionar la FECHA del DOCUMENTO.");
					 document.all.txtFecDocto.focus(); return false; 	}

				if (document.all.txtFecRecepcion.value==""){ alert("Debe seleccionar la FECHA de RECEPCIÓN DEL DOCUMENTO.");
					 document.all.txtFecRecepcion.focus(); return false; 	}

				if (document.all.txtFecTermino.value==""){ alert("Debe seleccionar la FECHA de TERMINO del DOCUMENTO.");
					 document.all.txtFecTermino.focus(); return false; 	}

				if (document.all.txtFecDocto.value !="" && document.all.txtFecRecepcion.value != ""){
					var f1 = new Date(document.all.txtFecDocto.value); 
					var f2 = new Date(document.all.txtFecRecepcion.value);
					if (f1>f2){ alert("La FECHA de RECEPCIÓN debe ser mayor o igual a la FECHA del DOCUMENTO"); document.all.txtFecDocto.focus(); return false; }
				}				

				if (document.all.txtFecTermino.value !="" && document.all.txtFecRecepcion.value != ""){
					var f1 = new Date(document.all.txtFecRecepcion.value); 
					var f2 = new Date(document.all.txtFecTermino.value);
					if (f1>f2){ alert("La FECHA de TERMINO debe ser mayor o igual a la FECHA del RECEPCIÓN"); document.all.txtFecRecepcion.focus(); return false; }
				}				

				if (document.all.txtRemitente.selectedIndex==0){ alert("Debe seleccionar el REMITENTE del DOCUMENTO.");
					document.all.txtRemitente.focus(); return false;	}

				if (document.all.txtDestinatario.selectedIndex==0){ alert("Debe seleccionar el DESTINATARIO del DOCUMENTO.");
					document.all.txtDestinatario.focus(); return false; 	}
				
				if (document.all.txtPrioridad.selectedIndex==0){ alert("Debe seleccionar una PRIORIDAD del DOCUMENTO.");
					document.all.txtPrioridad.focus(); return false;	}

				if (document.all.txtImpacto.selectedIndex==0){ alert("Debe seleccionar el IMPACTO del DOCUMENTO.");
					document.all.txtImpacto.focus(); return false; 	}

				if (document.all.txtAsunto.value==""){ alert("Debe capturar el ASUNTO del DOCUMENTO.");
					 document.all.txtAsunto.focus(); return false; 	}

				if (document.all.txtRecibio.selectedIndex==0){ alert("Debe seleccionar el RECEPTOR del DOCUMENTO.");
					document.all.txtRecibio.focus(); return false;	}

				if (document.all.txtEstatus.selectedIndex==0){ alert("Debe seleccionar el ESTATUS del DOCUMENTO.");
					document.all.txtEstatus.focus(); return false; 	}

				if (document.all.txtArchivoOriginal.value=="" && document.all.txtOperacion.value=="INS"){
					alert("Debe seleccionar un ARCHIVO.");
					document.all.btnCargarArchivo.focus();
					return false;
				}

				return true;		
			}			
			

			function recuperarTablaAuditorias(valor, tbl){
				var sTatus= "";

				var liga = '/' + 'tblAuditoriasLeftDocto' + '/' + valor;

				//alert("Valor de la liga: " + liga);

		        $.ajax({ type: 'GET', url: liga ,
		          success: function(response) {
		               var jsonData = JSON.parse(response);                                 

		                // Inicializa el arreglo que contendra las auditorias seleccionadas y lo llenara en base al resultado del query
		                // con respecto a los registros que tienen el campo "asignado" con el valor "SI"

		               	// Vaciar el arreglo
		               	lstAuditorias = [];

     					for (var i = 0; i < jsonData.datos.length; i++) {
				    		var dato = jsonData.datos[i];
						    if(dato.asignado=="SI")	 lstAuditorias.push(dato.idAuditoria);
						}
						//alert("Elementos en Lista de Auditorias:" + lstAuditorias.length);
						// Ahora llenara la tabla con todos los registros regresados por el query
		               	
		               	//Vacia la lista
		               	tbl.innerHTML="";
		               	
		               	//Define variables 
		               var renglon, columna;

		               for (var i = 0; i < jsonData.datos.length; i++) {
		                 	var dato = jsonData.datos[i];                                                                    
		                  	
		                  	renglon=document.createElement("TR");    
		 					
							sTatus= "";
							if (dato.asignado=="SI") { sTatus= "checked=true"; }

							renglon.innerHTML="<td><input type='checkbox' name='' "+sTatus+" onclick='seleccionaAuditoria(" + '"' + dato.idAuditoria + '",' + "this.checked);'/></td><td>" + dato.auditoria + "</td><td>" + dato.sujeto + "</td><td>" + dato.tipo + "</td><td>" + dato.etapa + "</td>";

		                  	renglon.onclick= function() 
		                  	{
		                  	  //obtenerNumeroPartidasSeleccionadas();
		                   	  //alert('En Construcción funcion: recuperarTablaGasto');
		                  	};
		                  	tbl.appendChild(renglon);                                                                       
		               }                                                             
		           },
		           error: function(xhr, textStatus, error){
		                alert('function recuperarTablaAuditorias ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
		           }                                             
				});           
			}              


			function seleccionaAuditoria(idAuditoria, activo)
			{
				if (activo==true){
					lstAuditorias.push(idAuditoria);
				}else{
					for( i = 0; i < lstAuditorias.length; i++)
					{
						if(idAuditoria==lstAuditorias[i]){
							lstAuditorias.splice(i, 1);
						}
					}
				}
			}

			function limpiarCampos(){
				document.all.txtDocto.value="";
				document.all.txtNumero.value="";
				document.all.txtTipo.selectedIndex=0;
				document.all.txtFlujo.selectedIndex=0;
				document.all.txtFecDocto.value="";
				document.all.txtFecRecepcion.value="";
				document.all.txtFecTermino.value="";
				document.all.txtRemitente.selectedIndex=0;
				document.all.txtDestinatario.selectedIndex=0;
				document.all.txtPrioridad.selectedIndex=0;
				document.all.txtImpacto.selectedIndex=0;
				document.all.txtAsunto.value="";
				document.all.txtRecibio.selectedIndex=0;
				document.all.txtEstatus.selectedIndex=0;
				document.all.chkAsignar.checked=0;

  				document.all.txtArchivoOriginal.value="";
				document.all.txtArchivoFinal.value="";

			}
			
			function checkfile(sender) {
				var validExts = new Array(".xlsx", ".xls", ".pdf", ".doc", ".docx");
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
					ajax.open("POST", "uploadDocumento.php");
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
					document.all.status.innerHTML="<img src='img/xls.gif'> " + document.all.txtArchivoOriginal.value; 
					document.all.btnCargarArchivo.style="display:none";	
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

				visualizarTablaAuditorias(document.all.chkAsignar);
				recuperarLista('lstTiposDoctos', document.all.txtTipo);
				recuperarLista('lstUnidades_HVS', document.all.txtRemitente);
				recuperarLista('lstAreas_HVS', document.all.txtDestinatario);
				recuperarLista('lstUsuariosRecibio', document.all.txtRecibio);


				///HVS recuperarListaLigada('lstAuditoriasByAreaAuditor', sArea, document.all.txtAuditoria);						
				//alert("Auditorias recuperadas: " + document.all.txtAuditoria.lenght);
				
				$( "#btnGuardar" ).click(function() {

					if (validarCaptura()){	

						if(lstAuditorias.length > 0){
							var sValor = "";

							for(i=0; i < lstAuditorias.length; i++){
								sValor = sValor + lstAuditorias[i] + '|';
							}
						}
						//alert("sValor:" + sValor);
						document.all.txtLstAuditorias.value = sValor;
						document.all.formulario.submit();
					}
				});
				

				$( "#btnCancelarTexto" ).click(function() {
						$('#modalTextoLargo').modal('hide');
						$('#modalDocto').modal('show');				
				});
					
				$( "#btnGuardarTexto" ).click(function() {
						document.all.txtAsunto.value = document.all.txtTextoAmplio.value;
						$('#modalTextoLargo').modal('hide');
						$('#modalDocto').modal('show');	
				});

			
				
				$( "#btnAgregar" ).click(function() {
					limpiarCampos();
					document.all.txtOperacion.value='INS';
					document.all.btnCargarArchivo.style.display='inline';
					//document.all.status.innerHTML="<img src=''> "; 
					document.all.status.style.display='none';
					visualizarTablaAuditorias(document.all.chkAsignar);
					
					recuperarTablaAuditorias(0, document.all.tblAuditorias);
					//HVS document.getElementById("txtAuditoria").disabled = false;
					//alert("Antes de llamar al modal");

					$('#modalDocto').removeClass("invisible");
					$('#modalDocto').modal('toggle');
					$('#modalDocto').modal('show');
					
				});

				$( "#btnCancelar" ).click(function() {
					$('#modalDocto').removeClass("invisible");
					$('#modalDocto').modal('toggle');
					$('#modalDocto').modal('hide');
					document.all.txtOperacion.value='';
					document.all.txtArchivoOriginal.value="";
					document.all.txtArchivoFinal.value="";
					document.all.btnCargarArchivo.style.display='none';
				});
				
				$( "#btnCargarArchivo" ).click(function() { $("#btnUpload").click();});

				$( "#txtAsuntoEdit" ).click(function(){
					document.all.txtTextoAmplio.value = document.all.txtAsunto.value;
					$('#modalDocto').modal('hide');
					$('#modalTextoLargo').modal('show');
				});	

				$("#btnLimpiar").click(function() { document.all.txtAuditoria.value = ""; document.all.txtAuditoria.focus(); })


				$( "#btnBuscar" ).click(function() {
					//if (document.all.txtAuditoria.value != ""){
						if(lstAuditorias.length>0){
							var tablaAuditorias = document.getElementById("tblAuditorias");
							var nRenglones;

							var textoBuscado = document.getElementById('txtAuditoria').value.toLowerCase();
							var celdas=""
							var localizado=false;
							var found=false;
							var comparaCon="";

							//alert("Numero de renglones: " + tablaAuditorias.rows.length);

							for (var i = 0; i < tablaAuditorias.rows.length; i++){
								celdas = tablaAuditorias.rows[i].getElementsByTagName('td');
								localizado = false;
								// Recorremos todas las celdas
								for (var j = 0; j < celdas.length && !localizado; j++){
									comparaCon = celdas[j].innerHTML.toLowerCase();
									// Buscamos el texto en el contenido de la celda
									if (textoBuscado.length == 0 || (comparaCon.indexOf(textoBuscado) > -1))
									{
										localizado = true;
									}
								}

								if(localizado){
									tablaAuditorias.rows[i].style.display = '';
								} else {
									// si no ha encontrado ninguna coincidencia, esconde la
									// fila de la tabla
									tablaAuditorias.rows[i].style.display = 'none';
								}


							}

						}
				//	}
				});

			});
			
			function obtenerDocumento(id){
				$.ajax({
					type: 'GET', url: '/DocumentoByidDocto/' + id ,
					success: function(response) {
						var obj = JSON.parse(response);				
						limpiarCampos();
						document.all.txtOperacion.value="UPD";

						document.all.txtDocto.value=obj.idDocto;
						document.all.txtNumero.value=obj.numeroDocto;
						seleccionarElemento(document.all.txtTipo, obj.idTipoDocto);
						seleccionarElemento(document.all.txtFlujo, obj.flujodocto);								

						document.all.txtFecDocto.value=obj.fDocto;
						document.all.txtFecRecepcion.value=obj.fRecepcion;
						document.all.txtFecTermino.value=obj.fTermino;

						seleccionarElemento(document.all.txtRemitente, obj.idRemitente);
						seleccionarElemento(document.all.txtDestinatario, obj.idDestinatario);

						seleccionarElemento(document.all.txtPrioridad, obj.idPrioridad);
						seleccionarElemento(document.all.txtImpacto, obj.idImpacto);

						document.all.txtAsunto.value='' + obj.asunto;
					
						seleccionarElemento(document.all.txtRecibio, obj.idrecibio);
						seleccionarElemento(document.all.txtEstatus, obj.estatus);

						document.all.btnGuardar.style.display='inline';
						
						document.all.txtArchivoOriginal.value=obj.archivoOriginal;
						document.all.txtArchivoFinal.value=obj.archivoFinal;
						document.all.status.style.display='inline';
						document.all.status.innerHTML="<img src='img/xls.gif'> " + document.all.txtArchivoOriginal.value; 
						document.all.btnCargarArchivo.style="display:none";	
						
						recuperarTablaAuditorias(obj.idDocto, document.all.tblAuditorias);

						$('#modalDocto').removeClass("invisible");
						$('#modalDocto').modal('toggle');
						$('#modalDocto').modal('show');
				},
					error: function(xhr, textStatus, error){
						alert('function obtenerDocumento()   -> statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error + ' Auditoría: ' + id);
					}			
				});		
			}		
			/*
			function obtenerGif(sArchivo){
				alert("Valor de sArchivo: " + sArchivo);

				var gif ="", ext="";
				ext = sArchivo.substring(sArchivo.lastIndexOf('.'));
				if (ext=='XLS' || ext=='XLSX'){
					gif = 'img/xls.gif';
				}else{ 
					if (ext=='DOC' || ext=='DOCX'){
						gif = 'img/doc.gif';
					}else{
						if (ext=='PDF'){
							gif = 'img/pdf.gif';
						}else{
							gif = 'img/xls.gif';
						}
					}
				}
				return gif
			}
			*/
			function visualizarTablaAuditorias(chk){
				if((chk.checked)){
					document.getElementById("divTablaAuditorias").style.display = "block";
					document.all.txtAuditoria.readonly = false;
					document.all.btnBuscar.disabled = false;
					document.all.btnLimpiar.disabled = false;
				}
				else{
					document.getElementById("divTablaAuditorias").style.display = "none";
					document.all.txtAuditoria.readonly = true;
					document.all.btnBuscar.disabled = true;
					document.all.btnLimpiar.disabled = true;
				}
			}
			

			function validaExisteNumeroDocto(sId){
				//alert("document.all.operacion: " + document.all.operacion.value);
				if(sId!=""){
					
					if(document.all.txtOperacion.value == "INS"){
						//sId = sId.toUpperCase();
						$.ajax({
							type: 'GET', url: '/validaExisteNumeroDocto/' + sId ,
							success: function(response) {
								//alert(response);
								var obj = JSON.parse(response);
								//alert(obj.total);

								if (obj.total > 0 ){
									alert("ATENCION: Ya existe registrado un documento con el número: " + sId + "\npor favor verifique.");
									document.all.txtNumero.value = "";
									document.all.txtNumero.focus();
								}
						},
							error: function(xhr, textStatus, error){
								alert('function validaExisteNumeroDocto()   -> statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
							}
						});		
						//obtenerDatos('/empleadoByRPE_HVS/' + document.all.txtRPE.value);
					}
				}
			}

			function validaCargaArchivo(sFlujo){
				//alert("sFlujos.value: " + sFlujo.value + "document.all.txtOperacion.value: " + document.all.txtOperacion.value);
				if (document.all.txtOperacion.value == "INS"){
					if (sFlujo.value == 'SALIDA'){
						document.all.btnCargarArchivo.disabled = true;
					}else{
						document.all.btnCargarArchivo.disabled = false;
					}
				}
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
	  <!-- Data tables -->
	  <link rel="stylesheet" href="css/jquery.dataTables.css"> 
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
					<div class="col-xs-3"><h2><i class="fa fa-tasks"></i>Documentos</h2></a></div>									
					<!--
					<div class="col-xs-2">
						<ul class="nav navbar-nav "><li><a href="./notificaciones"><i class="fa fa-envelope-o"></i> Usted tiene <span class="badge">0</span> Mensaje(s).</a></li></ul>
					</div>					
					-->
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
									<tr>
										<!--<th width="8%">Id.</th> -->
										<th width="8%">Número</th>
										<th width="8%">Tipo</th>
										<th  width="8%">Flujo</th>
										<th  width="8%">Fecha</th>
										<th  width="25%">Remitente</th>
										<th  width="25%">Destinatario</th>
										<th  width="8%">Prioridad</th>
										<th  width="8%">Impacto</th>
										<th  width="8%">Estatus</th>
										<th  width="8%">Documento</th>
									</tr>
								  </thead>
								  <tbody>				
									<?php foreach($datos as $key => $valor): ?>
										<!-- FUNCION A NIVEL RENGLON, SE CAMBIO A FUNCION POR CAMPO, PARA QUE EL ÚLTIMO CAMPO NO LA EJECUTARA
										<tr onclick=<?php echo "obtenerDocumento('" . $valor['idDocto'] . "');"; ?> style="width: 100%; font-size: xx-small">
										-->
										<?php 
										$gif = "";
										$pos = strpos($valor['archivoFinal'], '.');
										$ext = trim(strtoupper(substr($valor['archivoFinal'], $pos+1)));
										if ($ext=='XLS' || $ext=='XLSX'){
											$gif = 'xls.gif';
										}else{ 
											if ($ext=='DOC' || $ext=='DOCX'){
												$gif = 'doc.gif';
											}else{
												if ($ext=='PDF'){
													$gif = 'pdf.gif';
												}else{
													$gif = 'xls.gif';
												}
											}
										}
										?>
										<tr style="width: 100%; font-size: xx-small">										  									
											<td onclick=<?php echo "obtenerDocumento('" . $valor['idDocto'] . "');"; ?> ><?php echo $valor['numeroDocto']; ?></td>
											<td onclick=<?php echo "obtenerDocumento('" . $valor['idDocto'] . "');"; ?> ><?php echo $valor['tipo']; ?></td>
											<td onclick=<?php echo "obtenerDocumento('" . $valor['idDocto'] . "');"; ?> ><?php echo $valor['flujodocto']; ?></td>
											<td onclick=<?php echo "obtenerDocumento('" . $valor['idDocto'] . "');"; ?> ><?php echo $valor['fDocto']; ?></td>
											<td onclick=<?php echo "obtenerDocumento('" . $valor['idDocto'] . "');"; ?> ><?php echo $valor['remitente']; ?></td>	
											<td onclick=<?php echo "obtenerDocumento('" . $valor['idDocto'] . "');"; ?> ><?php echo $valor['destinatario']; ?></td>	
											<td onclick=<?php echo "obtenerDocumento('" . $valor['idDocto'] . "');"; ?> ><?php echo $valor['idPrioridad']; ?></td>
											<td onclick=<?php echo "obtenerDocumento('" . $valor['idDocto'] . "');"; ?> ><?php echo $valor['idImpacto']; ?></td>
											<td onclick=<?php echo "obtenerDocumento('" . $valor['idDocto'] . "');"; ?> ><?php echo $valor['estatus']; ?></td>
											<td>
												<a href="/documentos/<?php echo $valor['archivoFinal']; ?>"><?php echo obtenerGif($valor['archivoFinal']); ?></a>
											</td>

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
							<div class="col-xs-20"><textarea class="form-control" rows="20" placeholder="Capture aqui" id="txtTextoAmplio" name="txtTextoAmplio"></textarea></div>
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


	<div id="modalDocto" class="modal fade" role="dialog">
		<div class="modal-dialog">							
				<!-- Modal content-->
			<div class="modal-content">									
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h3 class="modal-title"><i class="fa fa-pencil-square-o"></i> Documentos por Auditoría...</h3>
				</div>									
				<div class="modal-body">
					<form id="formulario" METHOD='POST' ACTION="/guardar/documento" role="form">
						<input type='HIDDEN' name='txtOperacion' value=''>
						<input type='HIDDEN' name='txtLstAuditorias' value=''>	
						<input type='HIDDEN' name='txtArchivoOriginal' value=''>
						<input type='HIDDEN' name='txtArchivoFinal' value=''>

						<br>
						<div class="form-group">									
							<label class="col-xs-1 control-label" style="text-align:right">ID</label>
							<div class="col-xs-1">
								<input type="text" class="form-control" name="txtDocto" placeholder="Id. de Documento." readonly />
							</div>
							<label class="col-xs-1 control-label" style="text-align:right">No.</label>
							<div class="col-xs-2">
								<input type="text" class="form-control" name="txtNumero" placeholder="Número Documento." Onblur="validaExisteNumeroDocto(this.value)" />
							</div>
							<label class="col-xs-1 control-label">Tipo</label>
							<div class="col-xs-2">
								<select class="form-control" id="txtTipo" name="txtTipo">
									<option value="">Seleccione...</option>
								</select>
							</div>	
							<label class="col-xs-2 control-label">Flujo Documento</label>
							<div class="col-xs-2">
								<select class="form-control" id="txtFlujo" name="txtFlujo" onChange="javascript: validaCargaArchivo(this);">
									<option value="">Seleccione...</option>
									<option value="ENTRADA" selected>ENTRADA</option>
									<option value="SALIDA">SALIDA</option>
								</select>
							</div>	
						</div>	
						<br>							

						<div class="form-group">									
							<label class="col-xs-1 control-label" style="text-align:right">Fec.Docto.</label>
							<div class="col-xs-2">
								<input type="text" id="datepicker" class="form-control" name="txtFecDocto" placeholder="Fecha de Documento."/>
							</div>
							<label class="col-xs-3 control-label" style="text-align:right">Fecha Recepción</label>
							<div class="col-xs-2">
								<input type="text" id="datepicker2" class="form-control" name="txtFecRecepcion" placeholder="Fecha de Recepción."/>
							</div>
							<label class="col-xs-2 control-label" style="text-align:right">Fecha Termino</label>
							<div class="col-xs-2">
								<input type="text" id="datepicker3" class="form-control" name="txtFecTermino" placeholder="Fecha de Termino."/>
							</div>
						</div>	
						<br>							

						<div class="form-group">										
							<label class="col-xs-1 control-label">Remintente</label>
							<div class="col-xs-11">
								<select class="form-control" name="txtRemitente">
									<option value="" Selected>Seleccione...</option>
								</select>
							</div>																						
						</div>
						<br>

						<div class="form-group">										
							<label class="col-xs-1 control-label">Destinatario</label>
							<div class="col-xs-11">
								<select class="form-control" name="txtDestinatario">
									<option value="">Seleccione</option>
								</select>
							</div>	
						</div>
						<br>

						<div class="form-group" >										
							<label class="col-xs-1 control-label">Prioridad</label>
							<div class="col-xs-5">
								<select class="form-control" name="txtPrioridad">
									<option value="" Selected>Seleccione...</option>
									<option value="BAJA" selected>BAJA</option>
									<option value="MEDIA">MEDIA</option>
									<option value="ALTA">ALTA</option>
								</select>
							</div>																						
							<label class="col-xs-1 control-label">Impacto</label>
							<div class="col-xs-5">
								<select class="form-control" name="txtImpacto">
									<option value="">Seleccione</option>
									<option value="BAJO" selected>BAJO</option>
									<option value="MEDIO">MEDIO</option>
									<option value="ALTO">ALTO</option>
								</select>
							</div>	
						</div>
						<br>							

						<div class="form-group">									
							<label class="col-xs-1 control-label">Asunto <i class="fa fa-pencil"  id="txtAsuntoEdit"></i></label>
							<div class="col-xs-11"><textarea class="form-control" name="txtAsunto" id="txtAsunto" rows="3" placeholder="Escriba aqui..." style="resize:none; margin: 0 0 13px 0 !important;" ></textarea></div>	

						</div>								
						<div class="clearfix"></div>

						<div class="form-group">										
							<label class="col-xs-1 control-label">Recibió</label>
							<div class="col-xs-6">
								<select class="form-control" name="txtRecibio">
									<option value="" Selected>Seleccione...</option>
								</select>
							</div>
							<label class="col-xs-1 control-label">Estatus</label>
							<div class="col-xs-4">
								<select class="form-control" name="txtEstatus">
									<option value="">Seleccione...</option>
									<option value="ACTIVO" selected>ACTIVO</option>
									<option value="INACTIVO">INACTIVO</option>
								</select>
							</div>	
						</div>
						<br>
						<div class="clearfix"></div>
						<br>

						<div class="form-group">										
							<label class="col-xs-3"> 
								<input type="checkbox" name="chkAsignar" Id="chkAsignar" onChange="javascript: visualizarTablaAuditorias(this);"/> 
								Asignar a Auditorías 
							</label>

							<label class="col-xs-2 control-label" style="text-align:right">Tópico</label>
							<div class="col-xs-5">
								<input type="text" class="form-control" name="txtAuditoria" id="txtAuditoria" placeholder="Dato a buscar."/>
							</div>
							<button type="button" class="btn btn-primary active "	id="btnBuscar"><i class="fa fa-search"></i> Buscar</button>
							<button type="button" class="btn btn-default active "	id="btnLimpiar"><i class="fa fa-eraser"></i> Limpiar</button>
						</div>
 
 						<div class="table-responsive col-xs-12" id="divTablaAuditorias" style="height: 180px; overflow: auto; overflow-x:hidden;">	
							<table class="table table-striped table-bordered table-hover table-condensed table-responsive " >
							<!--
								<button type="button" class="btn btn-primary  btn-xs" 	id="btnNuevoDocto"><i class="fa fa-file-text-o"></i> Nuevo Documento...</button>
								<button type="button" class="btn btn-default  btn-xs" 	id="btnLigarDocto"><i class="fa fa-link"></i> Ligar Documento</button>
							-->								

								<!-- <caption>AUDITORÍAS</caption> -->
								<thead>
									<tr><th width="3%">Sel.</th><th width="10%">Auditoría</th><th width="50%">Sujeto</th><th width="25%">Tipo</th><th width="10%">Estatus</th></tr>
								</thead>
								<tbody id="tblAuditorias">
								</tbody>
								<!-- <tbody id="tblListaArchivos" style="width: 100%; font-size: xx-small"> -->
							</table>
						</div>

					</form>
					<div class="clearfix"></div>
				</div>
				
				<div class="modal-footer">
					<div class="pull-left">
						<form id="upload_form" enctype="multipart/form-data" method="post">
							<button  type="button" class="btn btn-default" id="btnCargarArchivo" style="display:none;"><i class="fa fa-link"></i> Anexar Archivo...</button>
							<input type="file" name="btnUpload" accept="application/pdf,application/vnd.ms-excel,,application/vnd.ms-word, " style="display:none;" id="btnUpload">
							<progress id="progressBar" value="0" max="100" style="width:'100%'; display:none;"></progress>
							<h4 id="status"></h4>
							<p id="lblAvances"></p>
						</form>
					</div>				
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


