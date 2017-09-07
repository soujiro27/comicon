<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">  
		<meta http-equiv="X-UA-Compatible" content="IE=Edge"/>
		
		<script src="js/canvasjs.min.js"></script>				
		<script type='text/javascript' src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js?ver=3.1.2"></script>
		<script type="text/javascript" src="js/genericas.js"></script>		
	
		<style type="text/css">		
			@media screen and (min-width: 768px) {
				#mapa_content {width:100%; height:150px;}
				
				
				#cvGrafica1, #cvGrafica2{height:250px; width:100%;}			
				#modalPapel .modal-dialog  {width:70%;
					position: relative;   
					z-index: 0;

				}

				#modalTextoLargo .modal-dialog  {width:40%;
					position: relative;
					z-index: 1;

				}				
				
				.auditor{background:#f4f4f4; font-size:6pt; padding:7px; display:inline; margin:1px; border:1px gray solid;}			
				label {text-align:right;}
				td {text-align:center;}			
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
			var sProcesoNuevo="";
			var sEtapaNueva="";
			var sProcesoActual="";
			var sEtapaAnterior="";
			var lstRiesgos = new Array();
			var cb = new Array();


			function validaAudi(idpapel){

				if (idpapel==''){
					if (document.all.txtFase.value==""){alert("Debe de seleccionar una AUDITORIA."); document.all.txtAuditoria.focus();return false;}
					return true;
				}
			}

			function validaFase(fase){

				if (fase==''){
					if (document.all.txtTipoPapel.value==""){alert("Debe de seleccionar una FASE."); document.all.txtFase.focus();return false;}
					return true;
				}
			}

			function recuriesgo(lista, valor, tbl){

					$.ajax({
					type: 'GET', url: '/'+ lista + '/' + valor ,
					success: function(response) {
						var jsonData = JSON.parse(response);

						//Vacia la lista
						tbl.innerHTML="";
						lstRiesgos = [];

						for (var i = 0; i < jsonData.datos.length; i++) {
						    var dato = jsonData.datos[i];
						    if(dato.asignado=="SI")	 lstRiesgos.push(new Array(dato.id));
						}
								
						//Agregar renglones
						var renglon, columna, type;
						
						for (var i = 0; i < jsonData.datos.length; i++) {
							var dato = jsonData.datos[i];	

							renglon=document.createElement("TR");
							
							sTatus= "";

							if (dato.asignado=="SI")
								sTatus= "checked=true";			
							
							var riesgo = dato.id;
							renglon.innerHTML="<td><input type='checkbox' value="+riesgo+" "+sTatus+" onclick='asignarRiesgo(this.value,this.checked);'/></td><td>" + dato.id + "</td><td>" + dato.riesgo + "</td>";

									
							renglon.onclick= function() {
								//document.all.txtOperacion.value='UPD';
							};
							tbl.appendChild(renglon);					
						}				

					},
					error: function(xhr, textStatus, error){
						alert('function recupeAuditores ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
					}			
				});	
			}		

		//asignar  checkbox
		function asignarRiesgo(riesgo,activo){
			
			//alert("lstEmpleados:" + riesgo + " checked= " + activo);
			if (activo==true){
				lstRiesgos.push(new Array(riesgo));
				var select = document.getElementById("Riesgo");
 	
					for(var i = 0; i < lstRiesgos.length; i++) {
						var option = document.createElement("option");
						option.value = riesgo;
						option.text = riesgo;
						document.all.txtRiesgo.add(option, document.all.txtRiesgo.length);
					}
				//alert("los seleccionados son: " + lstRiesgos.length);
			}else{
				for( i = 0; i < lstRiesgos.length; i++)
				{
					if(riesgo==lstRiesgos[i][0]){
						lstRiesgos.splice(i, 1);
						}
					//	for (var j = 0; j < lstRiesgos.length; j++) {
           			//		if (document.all.txtRiesgo[j].value == riesgo){
           						document.all.txtRiesgo.remove(j);
           			//		}
					//alert("los deseleccionados son: " + lstRiesgos.length);
					//}
				}
			}
		}





		function aparece(tipo){
			$.ajax({
				type: 'GET', url: '/btndescar/' +  tipo,
				success: function(response){
					var obj = JSON.parse(response);
					if (obj.pro=="NO"){
						document.all.btndescarga.style.display='inline';
					}else{
						document.all.btndescarga.style.display='none';			
					}		
				},
				error: function(xhr, textStatus, error){
					alert('function #txtTipoPapel  -> statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error + ' Papel: ' + id);
				}			
			});			

		}



		function decarga(valor){

				var tipo = document.all.txtTipoPapel;	
				if (tipo.selectedIndex>0){
					$.ajax({
						type: 'GET', url: '/DescarTipoPapel/' +  valor,
						success: function(response){
							var obj = JSON.parse(response);
							if (obj.final==null){
								alert("No se encontro formato disponible");
							}else{
								window.location.href= "/plantillas/"+obj.final;				
							}		
						},
						error: function(xhr, textStatus, error){
							alert('function #txtTipoPapel  -> statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error + ' Papel: ' + id);
						}			
					});
				}else return 0;				
		}




			function cajaResultados(tipo){
				if(tipo!="" && tipo!="NINGUNA"){
					document.all.divResumen.style.display='inline';	
				}
				else {
					document.all.divResumen.style.display='none';	
				}
			}	
				
			window.onload = function () {
				var chart1, chart2; 		
				setGrafica(chart1, "dpsTipoPapeles", "pie", "Cédulas en el Área", "cvGrafica1" );
				setGrafica(chart2, "dpsCedulasByAuditoria", "bar", "Papeles por Auditoría", "cvGrafica2" );
			};
			var ventana;
			
			function validarPapel(){
				
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

				if (document.all.txtTipoPapel.selectedIndex==0){
					alert("Debe seleccionar el TIPO DE PAPEL.");
					document.all.txtTipoPapel.focus();
					return false;
				}
				
				 if (document.all.txtFechaPapel.value==""){
					 alert("Debe seleccionar una FECHA DEL PAPEL.");
					 document.all.txtFechaPapel.focus();
					 return false;
				 }			

				if (document.all.txtTipoRes.selectedIndex==0){
					alert("Debe seleccionar el TIPO DE OBSERVACION.");
					document.all.txtTipoRes.focus();
					return false;
				} else{
					if (document.all.txtResultado.value=="" && document.all.txtTipoRes.selectedIndex>1){
						alert("Debe capturar el campo de RESULTADO(S).");
						document.all.txtResultado.focus();
						return false;
					}			
				}
				
				
				if (document.all.txtArchivoOriginal.value=="" && document.all.txtOperacion.value=="INS"){
					alert("Debe seleccionar un ARCHIVO.");
					document.all.btnCargarArchivo.focus();
					return false;
				}
				
				return true;		
			}
				
			function limpiarCampos(){
				document.all.txtID.value="";			
				document.all.txtAuditoria.selectedIndex=0;
				document.all.txtFase.selectedIndex=0;
				document.all.txtTipoPapel.selectedIndex=0;
				document.all.txtFechaPapel.value="";
				document.all.txtTipoRes.selectedIndex=0;
				document.all.txtResultado.value="";
				document.all.txtArchivoOriginal.value="";
				document.all.txtArchivoFinal.value="";
			}

			function limpiarArch(){
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
					ajax.open("POST", "uploadPapel.php");
					ajax.send(formdata);			
				}
			}		
				
			function progressHandler(event){
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
			        isRTL: false,
			        showMonthAfterYear: false,
			        yearSuffix: ''
				};

				$.datepicker.setDefaults($.datepicker.regional["es"]);

				$("#FPapel").datepicker({
					dateFormat:'dd-mm-yy',
					numberOfMonths: 2,
				});
			});






			var nUsr='<?php echo $_SESSION["idUsuario"];?>';
			var nCampana='<?php echo $_SESSION["idCuentaActual"];?>';
			var sArea='<?php echo $_SESSION["idArea"];?>';
			
			$(document).ready(function(){
				setInterval(getMensaje('txtNoti'),60000);
			
				document.getElementById('btnUpload').addEventListener('change', abrirArchivo, false);					
				if(nUsr!="" && nCampana!=""){
					cargarMenu( nCampana);			
				}else{
					if(nCampana=="")alert("Debe establecer una CUENTA PÚBLICA como PREDETERMINADA. Por favor consulte con su administrador del sistema.");
				}								
				//recuperarListaLigada('lstAuditoriasByArea', sArea, document.all.txtAuditoria);
				recuperarListaLigada('lstAuditoriasByAreaAuditor', sArea, document.all.txtAuditoria);
				recuperarLista('lstTipoPapel', document.all.txtTipoPapel);
				

				$( "#btnGuardar" ).click(function() {
					if (validarPapel()){					
						document.all.formulario.submit();
					}
				});

				$( "#btnRiesgos" ).click(function(){						
					document.all.divCatRiesgos.style.display='inline';
					document.all.divLstControles.style.display='none';
					document.all.divCptControles.style.display='none';
					
				});	

				$( "#btnControlesCancel" ).click(function(){						
					document.all.divCatRiesgos.style.display='none';
					document.all.divLstControles.style.display='inline';
					document.all.divCptControles.style.display='none';
				});	
				
				$( "#btnRiesgosCancel" ).click(function(){						
					document.all.divCatRiesgos.style.display='none';
					document.all.divLstControles.style.display='inline';
					document.all.divCptControles.style.display='none';

				});	


				$( "#btnRegresarCtrl" ).click(function(){						
					document.all.divCatRiesgos.style.display='none';
					document.all.divLstControles.style.display='inline';
					document.all.divCptControles.style.display='none';
				});					
				
				
				$( "#btnNuevoCtrl" ).click(function(){											
					document.all.divCatRiesgos.style.display='none';
					document.all.divLstControles.style.display='none';
					document.all.divCptControles.style.display='inline';
				});		


						

				$( "#TipoPapel" ).change(function(){	
					var tipo=document.all.txtTipoPapel;	
					if (tipo.selectedIndex>0){
						$.ajax({
							type: 'GET', url: '/buscarTipoPapel/' +  tipo.value,
							success: function(response){
								var obj = JSON.parse(response);						
								document.all.divProgramada.style.display="none";
								recuperarLista('lstMomentos', document.all.txtMomento);
								recuperarLista('lstProced', document.all.txtMomentoPRO);
									
								if(obj.programada=="SI"){
									document.all.divProgramada.style.display="inline";
									titCedula.innerHTML="<h3><i class='fa fa-pencil-square-o'></i> CÉDULA " + obj.papel + "</h3>";									
									document.all.btnCargarArchivo.style.display="none";
								}else{									
									document.all.btnCargarArchivo.style.display="inline";
								}					
							},
							error: function(xhr, textStatus, error){
								alert('function #txtTipoPapel  -> statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error + ' Papel: ' + id);
							}			
						});			
					}else return 0;
				});			
				
				$( "#btnCancelarTexto" ).click(function() {
						$('#modalTextoLargo').modal('hide');
						$('#modalPapel').modal('show');				
				});

				$( "#btnEliminarArchivo" ).click(function() {
					alert("Estamos trabajando en ello...")
					if (confirm("¿Esta seguro que desea ELIMINAR el ARCHIVO ADJUNTO\n \n")){
						limpiarArch();

						document.all.btnEliminarArchivo.style.display='none';
						document.all.status.style.display='none';
						document.all.btnCargarArchivo.style.display='inline';
						// $.ajax({
						// 	type: 'GET', url: '/buscarTipoPapel/' +  tipo.value,
						// 	success: function(response){
						// 		var obj = JSON.parse(response);						
								
						// 	},
						// 	error: function(xhr, textStatus, error){
						// 		alert('function #btnEliminarArchivo  -> statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error);
						// 	}			
						// });			
	 			 	}else{

	 		
	 				}

				});

					
				$( "#btnGuardarTexto" ).click(function() {
						$('#modalTextoLargo').modal('hide');
						$('#modalPapel').modal('show');	
				});

				
				$( "#btnAgregar" ).click(function() {
					limpiarCampos();
					document.all.btndescarga.style.display='none';
					document.all.btnEliminarArchivo.style.display='none';
					document.getElementById("TipoPapel").readonly=true;					
					document.all.txtOperacion.value='INS';
					document.all.btnCargarArchivo.style.display='inline';
					document.all.status.style.display='none';
					document.getElementById("txtAuditoria").disabled = false;
					document.all.divProgramada.style.display="none";
					
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
					//document.all.btnCargarArchivo.style="display:none";
				});
					
				$( "#btnCargarArchivo" ).click(function() { $("#btnUpload").click();});
			});

			function format(valor){
		 	 	var fechafin;
		 	 	fecha = valor;
		 	 	tmp = fecha.split('-');
		 	 	d= tmp[0];
		 	 	fecha = valor;
		 	 	tmp = fecha.split('-');
		 	 	m= tmp[1];
		 	 	fecha = valor;
		 	 	tmp = fecha.split('-');
		 	 	y= tmp[2];
		   		return fechafin = d + '-' + m + '-' + y;
			}

			
			function obtenerPapel(id){
				$.ajax({
					type: 'GET', url: '/papel/' + id ,
					success: function(response) {				
						var obj = JSON.parse(response);				
						//limpiarCampos();
						document.all.txtOperacion.value='UPD';
						document.all.txtID.value=id;
						seleccionarElemento(document.all.txtAuditoria,obj.idAuditoria);
						seleccionarElemento(document.all.txtFase, obj.idFase);
						seleccionarElemento(document.all.txtTipoPapel, obj.tipoPapel);

						document.all.txtFechaPapel.value='' + format(obj.fPapel);


						seleccionarElemento(document.all.txtTipoRes, obj.tipoResultado);
						cajaResultados(obj.tipoResultado);
						document.all.txtResultado.value='' + obj.resultado;
						var momen = recuperarLista('lstMomentos', document.all.txtMomento);
						document.all.btnGuardar.style.display='inline';
						document.all.btnEliminarArchivo.style.display='inline';
						document.all.divProgramada.style.display="none";


						document.all.txtArchivoOriginal.value=obj.archivoOriginal;
						document.all.txtArchivoFinal.value=obj.archivoFinal;
						document.all.status.style.display='inline';
						document.all.status.innerHTML="<img src='img/xls.gif'> " + document.all.txtArchivoOriginal.value; 
						document.all.btnCargarArchivo.style.display='none';	
						sProcesoActual=obj.proceso;
						sEtapaAnterior=obj.etapa;
						proximaEtapa(obj.proceso, obj.etapa);
						
						$('#modalPapel').removeClass("invisible");
						$('#modalPapel').modal('toggle');
						$('#modalPapel').modal('show');
					},
					error: function(xhr, textStatus, error){
						alert('function obtenerPapel()   -> statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error + ' Papel: ' + id);
					}			
				});		
			}		
				
			function recuperarTipoPapeles(fase){
				recuperarListaLigada('lstPapelesByFase', fase, document.all.txtTipoPapel);
				if (fase=="") {
					document.getElementById('TipoPapel').readOnly=true;					
				}else{
					document.getElementById("TipoPapel").readOnly=false;
				}						
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
					<div class="col-xs-3"><h2>Cédulas de Trabajo</h2></a></div>									
					<div class="col-xs-2">
						<ul class="nav navbar-nav "><li><a href="./notificaciones"><i class="fa fa-envelope-o"></i> Tiene <span><input type="text"  class="noti" id="txtNoti"></input></span> Mensaje(s).</a></li></ul>
					</div>					
					<div class="col-xs-3">
						<ul class="nav navbar-nav  pull-right">
							<li class="dropdown pull-right">            
								<a data-toggle="dropdown" class="dropdown-toggle" href="/">
									<i class="fa fa-user"></i> <b>C. <?php echo $_SESSION["idUsuario"] . ' -> ' .$_SESSION["sUsuario"] ?></b> <b class="caret"></b> 							
								</a>
								<ul class="dropdown-menu">
								  <li><a href="./perfil"><i class="fa fa-user"></i> Perfil **</a></li>
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
					  <div class="pull-left"><h3 class="modal-title"><i class="fa fa-tasks"></i> Cédulas de Trabajo</h3></div>
					  <div class="widget-icons pull-right">					  
						<button type="button" class="btn btn-primary active btn-xs" 	id="btnAgregar"><i class="fa fa-floppy-o"></i> Agregar...</button>
						
					  </div>  
					  <div class="clearfix"></div>
					</div>             
					<div class="widget-content">
						<div class="col-xs-12">							
							<div class="col-xs-3">
								<div id="cvGrafica1"></div>
							</div>
								<div class="col-xs-9">
									<div id="cvGrafica2"></div>						
								</div>
						</div>	
						<div class="clearfix"></div>
						<hr>
						<table class="table table-striped table-bordered table-hover table-condensed">
						  <thead>
							<tr><th width="8%">Clave<br>Auditoría</th><th width="20%">Sujeto de Fiscalización</th><th width="30%">Objeto de Fiscalización</th><th width="8%">Tipo de Auditoría</th><th>ID Papel</th><th width="5%">Fecha Papel</th><th>Fase</th><th>Tipo Papel</th><th>Tipo Resultado</th><th>Estatus</th><th>Documento</th></tr>
						  </thead>
						  <tbody>		
							<?php foreach($datos as $key => $valor): ?>
							<tr onclick=<?php echo "obtenerPapel('" . $valor['idPapel'] . "');"; ?> style="width: 100%; font-size:xx-small">  
							  <td><?php echo $valor['claveAuditoria']; ?></td>
							  <td><?php echo $valor['sujeto']; ?></td>
							  <td><?php echo $valor['objeto']; ?></td>
							  <td><?php echo $valor['tipoAuditoria']; ?></td>
							  <td><?php echo $valor['idPapel']; ?></td>
							  <td><?php echo $valor['fPapel']; ?></td>
							  <td><?php echo $valor['idFase']; ?></td>
							  <td><?php echo $valor['sPapel']; ?></td>
							  <td><?php echo $valor['tipoResultado']; ?></td>											 										 
							  <td><?php echo $valor['estatus']; ?></td>	
							  <td><a href="/papeles/<?php echo $valor['archivoFinal']; ?>"><?php echo obtenerGif($valor['archivoFinal']); ?></a></td>
							</tr>
							<?php endforeach; ?>																						
						  </tbody>
						</table>
					</div>
				</div>				
			</div>
		</div>
				
	</div>			
</div>

	<div id="modalTextoLargo" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<input type='HIDDEN' name='txtObjetoNuevo00' value=''>									
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
	


	

<div id="modalPapel" class="modal fade" role="dialog">
		<div class="modal-dialog">							
				<!-- Modal content-->
			<div class="modal-content">									
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h3 class="modal-title"><i class="fa fa-pencil-square-o"></i> Registrar Cédula de Trabajo...</h3>
				</div>									
				<div class="modal-body">
							<form id="formulario" METHOD='POST' ACTION="/guardar/pa" role="form">
								<input type='HIDDEN' name='txtOperacion' value=''>
								<input type='HIDDEN' name='txtCuenta' value='<?php echo $_SESSION["idCuentaActual"];?>'>						
								<input type='HIDDEN' name='txtPrograma' value='<?php echo $_SESSION["idProgramaActual"];?>'>	
								<input type='HIDDEN' name='txtArchivoOriginal' value=''>
								<input type='HIDDEN' name='txtArchivoFinal' value=''>									
								<input type='HIDDEN' name='txtID' value=''>
								
								<br>
								<div class="form-group">									
									<label class="col-xs-1 control-label">Auditoría</label>
									<div class="col-xs-11">
										<select class="form-control" id="txtAuditoria" name="txtAuditoria">
											<option value="">Seleccione</option>
										</select>
									</div>	
								</div>	
								<br>							
								<div class="form-group">											
									<label class="col-xs-1 control-label">Fase</label>
									<div class="col-xs-3">
										<select class="form-control" name="txtFase" id="txtFase" onchange="recuperarTipoPapeles(this.value);" onclick="validaAudi(txtAuditoria.value);">
											<option value="" Selected>Seleccione...</option>
											<option value="PLANEACION">PLANEACIÓN</option>
											<option value="EJECUCION">EJECUCIÓN</option>
											<option value="INFORMES">ELABORACIÓN DE INFORMES</option>
										</select>
									</div>
						
									<label class="col-xs-2 control-label">Tipo Cédula / Papel</label>
									<div class="col-xs-5">
										<select class="form-control" id="TipoPapel" name="txtTipoPapel" onfocus="validaFase(txtFase.value);" onchange="aparece(this.value);">
											<option value="">Seleccione...</option>
										</select>
									</div>
									<div class="col-xs-1">
									<button type="button" id="btndescarga" class="btn btn-primary btn-xs" onclick="decarga(TipoPapel.value)" style="display: none"><i class="fa fa-download"></i>Descarga</button>
									<!--<button type="button" id="btnConfronta"		class="btn btn-warning  btn-xs"><i class="fa fa-calendar"> </i> Confronta...</button>-->
									</div>
								</div>									
								<br>														
								<div class="form-group">	
									<label class="col-xs-1 control-label"> Fecha</label>
									<div class="col-xs-2">
										<input type="date" class="form-control"  name="txtFechaPapel" id="FPapel"/>
									</div>									
									
									<label class="col-xs-2 control-label">Tipo de Resultado</label>
									<div class="col-xs-7">
										<select class="form-control" name="txtTipoRes" onchange="cajaResultados(this.value);">
											<option value="">Seleccione</option>
											<option value="NINGUNA" Selected>NINGUNA OBSERVACION</option>											
											<option value="SIMPLE">OBSERVACIÓN SIMPLE</option>											
											<option value="GRAVE">PROBABLE FALTA GRAVE</option>											
										</select>
									</div>	
								</div>
								<br>							
								<div class="form-group" id="divResumen" style="display:none;">									
									<label class="col-xs-1 control-label">Resultado</label>
									<div class="col-xs-11"><textarea class="form-control" name="txtResultado" id="txtResultado" rows="5" placeholder="Escriba aqui el resumen o resultado obtenido..."></textarea></div>	
								</div>								
								<div class="form-group" id="divProgramada" style="display:none">
									<label class="col-xs-1 control-label"></label>
									<div class="col-xs-11">										
										  <div class="panel  panel-default">
											<div class="panel-heading">
												<div   id="titCedula" class="pull-left"></div>
												<div class="clearfix"></div>
											</div>
											<div class="panel-body"  style="height:300px; overflow: auto; overflow-x:hidden;">
												<div class="form-group" id="divCatRiesgos" style="display:none">									
													<h1>Catalogo de Riesgos</h1>
													<table class="table table-striped table-bordered table-hover table-condensed table-responsive">
													  <thead>
														<tr><th></th><th>Id</th><th>Riesgo</th></tr>
													  </thead>
													  <tfoot>
														<tr><th colspan=3>Totales</th></tr>
													  </tfoot>
													  <tbody id="tablamomento" style="width: 100%; font-size: xx-small" >
													  </tbody>
													</table>
													<button type='button' class="btn btn-default active btn-xs" 	id="btnRiesgosCancel"><i class="fa fa-floppy-o"></i> Regresar</button>													
												</div>												
												<div id="divLstControles">													
													<div class="form-group">									
														<label class="col-xs-3 control-label">Momento del Gasto</label>
														<div class="col-xs-5">
															<select class="form-control" id="txtMomento" name="txtMomento" onChange="javascript:recuriesgo('lstpapelesbymomento', this.value, document.all.tablamomento);">
																<option value="">Seleccione...</option>
															</select>
														</div>
														<div class="col-xs-2">
															<button type='button' class="btn btn-warning active btn-xs" 	id="btnRiesgos"><i class="fa fa-floppy-o"></i> Riesgos</button>
														</div>
														<div class="col-xs-2">															
															<button type='button' class="btn btn-warning active btn-xs" 	id="btnNuevoCtrl"><i class="fa fa-floppy-o"></i> Agregar Control</button>																										
														</div>
													</div>
													<br><br>
													<div class="form-group">									
														<table class="table table-striped table-bordered table-hover table-condensed table-responsive">
														  <thead>
															<tr>
																<th width="5%">ID</th>
																<th width="15%">Procedimiento</th>
																<th width="15%">Elemento</th>
																<th width="65%">Control</th>
															</tr>
														  </thead>
														  <tfoot>
															<!--<tr><th>Totales</th><td>4</td><td>5</td><td>3</td><td>1</td></tr>-->
														  </tfoot>
														  <tbody id="tablapacontrol" style="width: 100%; font-size: xx-small">
														  </tbody>
														</table>
													</div>
												</div>
												<div id="divCptControles" style="display:none">
												
													<div class="form-group">
														<label style="margin: 0px -17px 0px -18px ! important;" class="col-xs-1 control-label">Riesgo</label>
														<div class="col-xs-3">
															<select class="form-control" id="Riesgo" name="txtRiesgo">
																<option value="">Seleccione...</option>
															</select>
														</div>										
														<label style="margin: 0px 20px 0px -13px ! important;" class="col-xs-1 control-label">Procedimiento</label>
														<div class="col-xs-3">
															<select class="form-control" id="txtMomentoPRO" name="txtMomentoPRO" onChange="javascript:recuperarListaLigada('lstElem', this.value,document.all.txtMomentoEle);;">
																<option value="">Seleccione...</option>
															</select>
														</div>
														<label class="col-xs-1 control-label">Elemento</label>
														<div class="col-xs-3">
															<select class="form-control" id="txtMomentoEle" name="txtMomentoEle">
																<option value="">Seleccione...</option>
															</select>
														</div>														
													</div>
													<br>
													<div class="form-group">									
														<label style="margin: 7px -15px 0px -20px ! important;" class="col-xs-1 control-label">Control</label>
														<div class="col-xs-10">
															<textarea class="form-control" rows="15" placeholder="Capture aqui" id="txtControl" name="txtControl" style="margin: 10px 0 10px -1px !important; height: 153px; width: 753px;"></textarea>
														</div>													
													</div>
													<br>
													<div class="pull-right">
														<button type="button" class="btn btn-primary  active btn-xs" 	id="btnAgregarCtrl"><i class="fa fa-floppy-o"></i> Agregar</button>
														<button type="button" class="btn btn-default  active btn-xs" 	id="btnRegresarCtrl"><i class="fa fa-back"></i> Regresar</button>
													</div>													
												</div>												
											</div>
										  </div>									
										</h1>
									</div>								
								</div>
							</form>
					<div class="clearfix"></div>
				</div>
				
				<div class="modal-footer">
					<div class="pull-left" id="divBtnsAnexar" >
						<form id="upload_form" enctype="multipart/form-data" method="post">
							<button  type="button" class="btn btn-default" id="btnEliminarArchivo" style="display: none;"><i class="fa fa-trash-o fa-fw"></i> Eliminar Archivo...</button>
							<button  type="button" class="btn btn-default" id="btnCargarArchivo"><i class="fa fa-link"></i> Anexar Archivo...</button>
							<input type="file" name="btnUpload" accept="application/pdf,application/vnd.ms-excel, application/vnd.ms-word" style="display:none;" id="btnUpload">
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


