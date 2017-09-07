<!DOCTYPE html>
<html lang="en"><head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=Edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<script type='text/javascript' src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js?ver=3.1.2"></script>
	<script type="text/javascript" src="js/genericas.js"></script>

  	<style type="text/css">
		@media screen and (min-width: 768px) {
			#mapa_content {width:100%; height:150px;}
			#modalUnidad .modal-dialog {width:80%;}
			#modalAsignacion .modal-dialog {width:50%;}

			.delimitado {
				height: 350px !important;
				overflow: scroll;
			}​
			.auditor{background:#f4f4f4; font-size:7pt; padding:2px; display:inline; margin:2px; border:1px black solid;}
			label {text-align:right;}
			caption {padding: .2em .8em;border-bottom: 1px solid #fFF;background:#f4f4f4; font-weight: bold;}
		}
	</style>


	<!-- **************************************  INICIA ÁREA DE JAVA SCRIPT ******************************* -->

	<script type="text/javascript">
	/*
	var mapa;
	var nZoom=10;
	var ventana;

	// ********************************************* ZONA DE FUNCIONES ***************************************

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

	function inicializar() {
		var opciones = {zoom: nZoom,draggable: false,scrollwheel: true,	mapTypeId: google.maps.MapTypeId.ROADMAP};
		mapa = new google.maps.Map(document.getElementById('mapa_content'), { center: {lat: 19.4339249, lng: -99.1428964},zoom: nZoom});
	}

		window.onload = function () {
		var chart1;

		//setGrafica(chart1, "dpsAuditoriasByArea", "pie", "Auditorias", "canvasJG" );
	};
	*/

	// ******************  ALTA DE REGISTROS  *********************

	function agregarUnidad() {

		document.all.txtOperacion.value = "INS";
		limpiarDatos();

		// Como es alta ocultar el elemento estatus
		document.getElementById("divEstatus").style.display="none";

		seleccionarElemento(document.all.txtEstatus,'ACTIVO');

		document.all.txtIdCuenta.disabled = false;
	    document.all.txtIdSector.disabled = false;
		document.all.txtIdSubsector.disabled = false;
		document.all.txtIdUnidad.disabled = false;

		$('#modalUnidad').removeClass("invisible");
		$('#modalUnidad').modal('toggle');
		$('#modalUnidad').modal('show');

	}

	function seleccionarUnidadesByCuenta() {

		sIdCuenta = document.all.txtCuentas.options[document.all.txtCuentas.selectedIndex].value;
		nPosicionCuenta = document.all.txtCuentas.selectedIndex;
		//alert("El valor de document.all.txtCuentas.selectedIndex es: " + document.all.txtCuentas.selectedIndex );

		if (nPosicionCuenta > 0){
			//* Se modificara la variable de ambiente en PHP, para que esta posteriormente se utilice en
			//* los llamados a la BD para extraer las unidades de la cuenta seleccionada
			liga = '/actualizaVarSession/' + sIdCuenta;
			$.ajax({ async: false, type: 'GET', url: liga , success: function(response) { /* alert(response); */ } });

			var liga = '/catEntidades/' + sIdCuenta;
			$.ajax({ type: 'GET', url: liga ,
				success: function(response) {
		            var jsonData = JSON.parse(response);

					document.getElementById('tblListaUnidades').innerHTML="";
	               	//Agregar renglones
	                var renglon, columna;

	                for (var i = 0; i < jsonData.datos.length; i++) {

	                    var dato = jsonData.datos[i];

	                    var sIdCuenta              = dato.idCuenta;
	                    var sIdSector              = dato.idSector;
	                    var sIdSubsector           = dato.idSubsector;
	                    var sCentroGestor		   = dato.centroGestor;
	                    var sIdUnidad              = dato.idUnidad;
	                    var sNombre                = dato.nombre;
	                    var sSiglas				   = dato.siglas;
	                    var sTitular               = dato.titular;
	                    var sEstatus               = dato.estatus;

	                    var sIdUnidadSector        = dato.idUnidadSector;
	                    var sUnidadSector          = dato.unidadSector;

	                    var sIdUnidadPoder 		   = dato.idUnidadPoder;
	                    var sUnidadPoder 		   = dato.unidadPoder;

	                    var sIdUnidadClasificacion = dato.idUnidadClasificacion;
	                    var sUnidadClasificacion   = dato.unidadClasificacion;

	                    miFuncion = " onclick='recuperarUnidad(\""+sIdCuenta+"\",\""+sIdSector+"\",\""+sIdSubsector+"\",\""+sIdUnidad+"\");'"

		                renglon       = document.createElement("TR");
						renglon.style = "font-size: xx-small";
		                renglon.innerHTML = "" +
						'<td width="6%"  ' + miFuncion + "> " + sIdCuenta     + "</td>" +
						/*
						'<td width="5%"  ' + miFuncion + "> " + sIdSector     + "</td>"+
						'<td width="5%"  ' + miFuncion + "> " + sIdSubsector  + "</td>"+
						'<td width="5%"  ' + miFuncion + "> " + sIdUnidad     + "</td>"+
						*/
						'<td width="5%"  ' + miFuncion + "> " + sCentroGestor        + "</td>"+
						'<td width="27%" ' + miFuncion + "> " + sNombre              + "</td>"+
						'<td width="5%" ' + miFuncion + "> " + sSiglas              + "</td>"+
						'<td width="27%" ' + miFuncion + "> " + sTitular             + "</td>"+
						'<td width="20%" ' + miFuncion + "> " + sUnidadSector        + "</td>"+
						'<td width="20%" ' + miFuncion + "> " + sUnidadPoder         + "</td>"+
						'<td width="10%" ' + miFuncion + "> " + sUnidadClasificacion + "</td>"+
						'<td width="8%"  ' + miFuncion + "> " + sEstatus             + "</td>";

	                    document.getElementById('tblListaUnidades').appendChild(renglon);
	                }
				},
				error: function(xhr, textStatus, error){
					alert(' Error en function seleccionarCuenta()  TextStatus: ' + textStatus + ' Error: ' + error );
					return false;
				}
			});
		}
	}

	    function recuperarTablaAreasAuditoras(lista, valor, tbl){
		var sTatus= "";

		var liga = '/' + lista + '/' + valor;

		//alert("Valor de la liga: " + liga);

        $.ajax({ type: 'GET', url: liga ,
          success: function(response) {
               var jsonData = JSON.parse(response);                                 
                              
               //Vacia la lista
               tbl.innerHTML="";

               //Agregar renglones
               var renglon, columna;

               for (var i = 0; i < jsonData.datos.length; i++) {
                 	var dato = jsonData.datos[i];                                                                    
                  	
                  	renglon=document.createElement("TR");    
 					
					renglon.innerHTML="<td>" + dato.idArea + "</td><td>" + dato.nombre + "</td>";

                  	renglon.onclick= function() { };
                  	tbl.appendChild(renglon);                                                                       
               }                                                             
           },
           error: function(xhr, textStatus, error){
                alert('function recuperarTablaAreasAuditoras ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
           }                                             
		});           
	}              


	// ******************  MODIFICACION DE REGISTROS  *******************

	function recuperarUnidad(sIdCuenta, sIdSector, idSubsector, sIdUnidad){
		var liga = '/obtenerUnidadByCentroGestor/' + sIdCuenta + '/' + sIdSector + '/' + idSubsector + '/' + sIdUnidad;

		$.ajax({ type: 'GET', url: liga ,
			success: function(response) {
	            //var jsonData = JSON.parse(response);   // Cuando se regresan mas de un registro
				var obj = JSON.parse(response);		// Cuando solo se regresa un registro

				limpiarDatos();

				document.all.txtOperacion.value = "UPD";
				// Como es actualización es necesario asegurarse de que se muestre elemento estatus
				document.getElementById("divEstatus").style.display="inline";

				seleccionarElemento(document.all.txtIdCuenta, obj.idCuenta);
				recuperarListaSelected('sectoresByCta', obj.idCuenta, document.all.txtIdSector, obj.idSector );
				recuperarListaSelected('subSectoresByCtaYsector/' + obj.idCuenta, obj.idSector, document.all.txtIdSubsector, obj.idSubsector );

				seleccionarElemento(document.all.txtIdUnidadSector, obj.idUnidadSector);
				recuperarListaSelected('poderesBySector', obj.idUnidadSector, document.all.txtIdUnidadPoder, obj.idUnidadPoder );
				recuperarListaSelected('clasificacionesByPoder', obj.idUnidadPoder, document.all.txtIdUnidadClasificacion, obj.idUnidadClasificacion );


				document.all.txtIdUnidad.value  = obj.idUnidad;
				document.all.txtNombre.value    = obj.nombre;
				document.all.txtSiglas.value    = obj.siglas;
				document.all.txtTitular.value   = obj.titular;
				seleccionarElemento(document.all.txtEstatus, obj.estatus);

				document.all.txtCentroGestor.value         = obj.idCuenta.trim() + obj.idSector.trim() + obj.idSubsector.trim() + obj.idUnidad.trim();
				document.all.txtCentroGestorAnterior.value = obj.idCuenta.trim() + obj.idSector.trim() + obj.idSubsector.trim() + obj.idUnidad.trim();

				document.all.txtJuridicoSiglas.value = obj.siglasJuridico;
				seleccionarElemento(document.all.txtJuridicoClasif, obj.idClasificacionJuridico);

				document.all.txtIdCuenta.disabled    = true;
		    	document.all.txtIdSector.disabled    = true;
				document.all.txtIdSubsector.disabled = true;
				document.all.txtIdUnidad.disabled    = true;

				recuperarTablaAreasAuditoras('tblAreasByUnidad', document.all.txtCentroGestor.value, document.all.tblAreasAuditoras);


				$('#modalUnidad').removeClass("invisible");
				$('#modalUnidad').modal('toggle');
				$('#modalUnidad').modal('show');

			},
			error: function(xhr, textStatus, error){
				alert(' Error en function recuperarUnidad()  TextStatus: ' + textStatus + ' Error: ' + error );
				return false;
			}
		});
	}

	function limpiarDatos(){

		document.all.txtIdCuenta.selectedIndex=0;
		document.all.txtIdSector.selectedIndex=0;
		document.all.txtIdSubsector.selectedIndex=0;
		document.all.txtIdUnidad.value="";
		document.all.txtNombre.value="";
		document.all.txtSiglas.value="";
		document.all.txtTitular.value="";
		document.all.txtEstatus.selectedIndex=0;
		document.all.txtIdUnidadSector.selectedIndex=0;
		document.all.txtIdUnidadPoder.selectedIndex=0;
		document.all.txtIdUnidadClasificacion.selectedIndex=0;
		document.all.txtJuridicoSiglas.value="";
		document.all.txtJuridicoClasif.selectedIndex=0;

		limpiaTabla(document.all.tblAreasAuditoras);

	}

	function validarDatos(){

		if (document.all.txtIdCuenta.selectedIndex == 0){
			alert("Debe seleccionar la CUENTA de la UNIDAD.");
			document.all.txtIdCuenta.focus();
			return false;
		}

		if (document.all.txtIdSector.selectedIndex == 0){
			alert("Debe seleccionar el SECTOR de la UNIDAD.");
			document.all.txtIdSector.focus();
			return false;
		}

		if (document.all.txtIdSubsector.selectedIndex == 0){
			alert("Debe seleccionar el SUB-SECTOR de la UNIDAD.");
			document.all.txtIdSubsector.focus();
			return false;
		}

		if (document.all.txtIdUnidad.value == ""){
			alert("Debe capturar la CLAVE de la UNIDAD.");
			document.all.txtIdUnidad.focus();
			return false;
		}

		if (document.all.txtNombre.value == ""){
			alert("Debe capturar el NOMBRE de la UNIDAD.");
			document.all.txtNombre.focus();
			return false;
		}
		/*
		if (document.all.txtSiglas.value == ""){
			alert("Debe capturar las SIGLAS de la UNIDAD.");
			document.all.txtSiglas.focus();
			return false;
		}
		
		if (document.all.txtTitular.value == ""){
			alert("Debe capturar el NOMBRE del TITULAR de la UNIDAD.");
			document.all.txtTitular.focus();
			return false;
		}
		*/
		if (document.all.txtEstatus.selectedIndex == 0){
			alert("Debe seleccionar el ESTADO del TIPO del CRITERIO.");
			document.all.txtEstatus.focus();
			return false;
		}
		
		if (document.all.txtIdUnidadSector.selectedIndex == 0){
			alert("Debe seleccionar el SECTOR CASIFICATORÍO del la UNIDAD.");
			document.all.txtIdUnidadSector.focus();
			return false;
		}
		
		if (document.all.txtIdUnidadPoder.selectedIndex == 0){
			alert("Debe seleccionar el PODER CASIFICATORÍO del la UNIDAD.");
			document.all.txtIdUnidadPoder.focus();
			return false;
		}
		if (document.all.txtIdUnidadClasificacion.selectedIndex == 0){
			alert("Debe seleccionar la AGRUPACIÓN del la UNIDAD.");
			document.all.txtIdUnidadClasificacion.focus();
			return false;
		}

		return true;
	}

	function validarDuplicidadCentroGestor(sCuenta, sSector, sSubsector, sUnidad){
		var regresa = false;
		if (document.all.txtOperacion == 'INS' || (document.all.txtCentroGestorAnterior.value != document.all.txtCentroGestor.value) ){

			//alert(liga);

			liga = '/obtenerTotalUnidadByCentroGestor/'+ sCuenta + '/' + sSector + '/' +  sSubsector + '/' + sUnidad;

			$.ajax({ async:false, cache:false, type: 'GET', url: liga ,
				success: function(response) {
		            //var jsonData = JSON.parse(response);   // Cuando se regresan mas de un registro
					var obj = JSON.parse(response);		// Cuando solo se regresa un registro
					if (obj.total == 0){ regresa = true; }else{ alert("Los datos de Sector, Subsector y Unidad ya existen, favor de validar."); regresa = false; }
				},
				error: function(xhr, textStatus, error){
					alert(' Error en function validarDuplicidadCentroGestor()  TextStatus: ' + textStatus + ' Error: ' + error );
					regresa = false;
				}
			});
		}else{
			regresa = true;
		}
		return regresa;
	}


	function duplicarUnidades(){
		var sCuentaActual='<?php echo $_SESSION["idCuentaActual"];?>';		
		seleccionarElemento(document.all.txtCuentaOrigen, sCuentaActual);

		$('#modalAsignacion').removeClass("invisible");
		$('#modalAsignacion').modal('toggle');
		$('#modalAsignacion').modal('show');

	}


	function ValidarExistenciaCuenta(sCuenta){

		var liga = '/validarExisteCuenta/'+ sCuenta;
		var resultado;

		$.ajax({ async:false, cache:false, type: 'GET', url: liga ,
			success: function(response) {
	            //var jsonData = JSON.parse(response);   // Cuando se regresan mas de un registro
				var obj = JSON.parse(response);		// Cuando solo se regresa un registro
				if (obj.total > 0){ 
					resultado = true; 
				}else{ 
					resultado =  false; 
				}
			},
			error: function(xhr, textStatus, error){
				alert(' Error en function validarDuplicidadCentroGestor()  TextStatus: ' + textStatus + ' Error: ' + error );
				resultado =  false;
			}
		});

		return resultado;

	}


	// ********************************************* ZONA DE JQUERY ******************************************
	var nUsr='<?php echo $_SESSION["idUsuario"];?>';
	var nCampana='<?php echo $_SESSION["idCuentaActual"];?>';

	$(document).ready(function(){

		getMensaje('txtNoti',1);

		recuperarLista('lstCuentas', document.all.txtIdCuenta);
		recuperarLista('lstCuentas', document.all.txtCuentas);
		recuperarLista('lstUnidadesSectores', document.all.txtIdUnidadSector);
		//seleccionarElemento(document.all.txtCuentas, nCampana);

		recuperarLista('lstCuentas', document.all.txtCuentaOrigen);
		recuperarLista('lstCuentas', document.all.txtCuentaDestino);

		if(nUsr!="" && nCampana!=""){
			cargarMenu( nCampana);
		}else{
			if(nCampana=="")alert("Debe establecer una CAMPAÑA como PREDETERMINADA. Por favor consulte con su administrador del sistema.");
		}

		// **************************** DEFINICIONES DE BOTONES ***********************************************
		$( "#btnGuardar" ).click(function() {

			if ( validarDatos() ){

				if ( validarDuplicidadCentroGestor(document.all.txtIdCuenta.options[document.all.txtIdCuenta.selectedIndex].value.trim(), document.all.txtIdSector.options[document.all.txtIdSector.selectedIndex].value.trim(), document.all.txtIdSubsector.options[document.all.txtIdSubsector.selectedIndex].value.trim(), document.all.txtIdUnidad.value.trim() ) ){

					document.all.txtIdCuenta.disabled = false;
					document.all.txtIdSector.disabled = false;
					document.all.txtIdSubsector.disabled = false;
					document.all.txtIdUnidad.disabled = false;

					//alert(document.all.txtIdCuenta.options[document.all.txtIdCuenta.selectedIndex].value.trim() + '-' + document.all.txtIdSector.options[document.all.txtIdSector.selectedIndex].value.trim() + '-' + document.all.txtIdSubsector.options[document.all.txtIdSubsector.selectedIndex].value.trim() + '-' + document.all.txtIdUnidad.value.trim() );

					//alert("Antes de guardar");

					alert("El valor de document.all.txtJuridicoSiglas es: " + document.all.txtJuridicoSiglas.value + " y el valor de document.all.txtJuridicoClasif es: " + document.all.txtJuridicoClasif.options[document.all.txtJuridicoClasif.selectedIndex].value );

					document.all.formulario.submit();
					$('#modalUnidad').modal('hide');
					seleccionarUnidadesByCuenta();
				}
			}
		});

		$( "#btnCancelar" ).click(function() {
			$('#modalUnidad').modal('hide');
		});


		$( "#btnGuardarAsignacion" ).click( function() {
			var cuentaInicial = document.all.txtCuentaOrigen.options[document.all.txtCuentaOrigen.selectedIndex].value;
			var cuentaFinal   = document.all.txtCuentaDestino.options[document.all.txtCuentaDestino.selectedIndex].value;

			var anioInicial = cuentaInicial.substr(4,4);
			var anioFinal   = cuentaFinal.substr(4,4);

			var proceder = false;
			//alert("El valor de anioInicial es: " + anioInicial + "\nnEl valor de anioFinal es: " + anioFinal);

			if(anioFinal == anioInicial | anioFinal < anioInicial ){
				alert("La Cuenta origen debe ser anterior a la Cuenta destino");
			}else{

				if ( (ValidarExistenciaCuenta(cuentaFinal)) ){

					if (confirm("La cuenta " + cuentaFinal + " ya cuenta con unidades asignadas, si continua Sobre-Escribirá dicha información\n\n ¿Desea continuar con la Asignación?.")){

						if(confirm("Por favor confirme que desea remplazar las unidades de la cuenta "+ cuentaFinal + "...")){
							proceder = true;
						}
					} 
				}else{
					proceder = true;
				}

				if (proceder){
					// Inicia proceso de integración de unidades a la cuenta seleccionada.

					var liga1 = '/asignarUnidadesToCuenta/'+ cuentaInicial + '/' + cuentaFinal;

					$.ajax({ async:false, cache:false, type: 'GET', url: liga1 ,
						success: function(response) {
				            //var jsonData = JSON.parse(response);   // Cuando se regresan mas de un registro
							var obj = JSON.parse(response);		// Cuando solo se regresa un registro
							if (obj.total > 0){ 
								resultado = true; 
							}else{ 
								resultado =  false; 
							}
						},
						error: function(xhr, textStatus, error){
							alert(' Error en function validarDuplicidadCentroGestor()  TextStatus: ' + textStatus + ' Error: ' + error );
							resultado =  false;
						}
					});



				}
			}
		})

		// **************************** FIN DE DEFINICIONES DE BOTONES ********************************************

	});

	</script>
	<!-- **************************************  FINALIZA ÁREA DE JAVA SCRIPT ******************************* -->

	<!-- *******************************************  CONTINUA ÁREA HTML ************************************ -->

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
							<div class="col-xs-3"><h2>Catálogo de Unidades</h2></a></div>
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

		<!-- Header starts -->
		<header>
		</header>
		<!-- Header ends -->

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
			</div>
			 <!-- Sidebar ends -->

		  	<!-- Main bar -->
		  	<div class="mainbar">

				<div class="col-md-12">
					<div class="widget">
						<div class="widget-head">
							<div class="pull-left"><h3 class="pull-left"><i class="fa fa-home"></i> Unidades registradas</h3></div>
							<div widget-icons pull-right">
								<div class="col-xs-10">
									<label class="col-xs-7 control-label">Seleccionar Cuenta</label>
									<div class="col-xs-3">
										<select name="txtCuentas" id="txtCuentas" class="form-control" onChange=" javascript:seleccionarUnidadesByCuenta();">
											<option value="">Seleccione...</option>
										</select>
									</div>
									<button onclick="agregarUnidad();" type="button" class="btn btn-primary  btn-xs">Agregar Unidad     </button>
								</div>

								<!-- <button onclick="seleccionarCuenta();" type="button" class="btn btn-primary  btn-xs">Seleccionar Cuenta </button>  -->
							</div>
							<div class="clearfix"></div>
						</div>

						<div class="widget-content">
							<div class="table-responsive" style="height: 400px; overflow: auto; overflow-x:hidden;">
								<table class="table table-striped table-bordered table-hover table-condensed ">
									<thead > 
										<tr>
									 		<th>Cuenta</th>
									 		<th>Centro Gestor</th>
									 		<th>Nombre Unidad</th>
									 		<th>Siglas</th>
									 		<th>Nombre Titular de Unidad</th>
									 		<th>Sector</th>
									 		<th>Poder</th>
									 		<th>Agrupación</th>
									  		<th>Estatus</th>
										</tr>
								  	</thead>
								  	<tbody id="tblListaUnidades" >
										<?php foreach($datos as $key => $valor): ?>
										<tr onclick=<?php echo "javascript:recuperarUnidad('" . $valor['idCuenta'] . "','" . $valor['idSector'] . "','" . $valor['idSubsector'] . "','" . $valor['idUnidad'] . "');"; ?> style="width: 100%; font-size: xx-small"> 

										  <td width="6%"><?php  echo $valor['idCuenta']; ?></td> 
										  <td width="5%"><?php  echo $valor['centroGestor']; ?></td>
										  <td width="27%"><?php echo $valor['nombre']; ?></td>
										  <td width="5%"><?php echo $valor['siglas']; ?></td>
										  <td width="27%"><?php echo $valor['titular']; ?></td>
										  <td width="20%"><?php echo $valor['unidadSector']; ?></td>
										  <td width="20%"><?php echo $valor['unidadPoder']; ?></td>
										  <td width="10%"><?php echo $valor['unidadClasificacion']; ?></td>
										  <td width="8%"><?php  echo $valor['estatus']; ?></td>
										</tr>
										<?php endforeach; ?>
								  	</tbody >
								</table>
							</div>
						</div>

						<div class="widget-foot">
							<div class="pull-left">
								<button onclick="duplicarUnidades();" type="button" class="btn btn-primary  btn-xs" name="btnDuplicarUnidades" id="btnDuplicarUnidades" ><i class="fa fa-file-text-o"></i> Asignar Unidades a Nueva Cuenta...</button>
							</div>
							<div class="clearfix"></div>
						</div>

					</div>
				</div>
				<!-- Matter ends -->
			</div>
		   	<!-- Mainbar ends -->
		   <div class="clearfix"></div>
		</div>
		<!-- Content ends -->

		<div id="modalUnidad" class="modal fade" role="dialog">
			<div class="modal-dialog">
				<form id="formulario" METHOD='POST' action='/guardar/unidad' role="form">
					<input type='HIDDEN' name='txtOperacion' value=''>
					<input type='HIDDEN' name='txtCentroGestor' value=''>
					<input type='HIDDEN' name='txtCentroGestorAnterior' value=''>

					<!-- Modal content-->
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4 class="modal-title">Registro del Unidad...</h4>
						</div>
						<div class="modal-body">
							<div class="container-fluid">

								<div class="form-group">

									<label class="col-xs-2 control-label">Cuenta</label>
									<div class="col-xs-2">
										<select name="txtIdCuenta" id="txtIdCuenta" class="form-control" onChange="javascript:recuperarListaLigada('sectoresByCta', this.value, document.all.txtIdSector);">
											<option value="">Seleccione...</option>
										</select>
									</div>

									<label class="col-xs-1 control-label">Sector</label>
									<div class="col-xs-3">
										<select name="txtIdSector" id="txtIdSector" class="form-control" onChange="javascript:recuperarListaLigada('subSectoresByCtaYsector/' + document.all.txtIdCuenta.value, this.value, document.all.txtIdSubsector);">
											<option value="">Seleccione...</option>
										</select>
									</div>

									<label class="col-xs-1 control-label">Subsector</label>
									<div class="col-xs-3">
										<select name="txtIdSubsector" id="txtIdSubsector" class="form-control" >
											<option value="">Seleccione...</option>
										</select>
									</div>

								</div>
								<br>

								<!--
								<div class="form-group">
								</div>
								<br>

								<div class="form-group">
								</div>
								<br>

								<div class="form-group">
								</div>
								<br>
								-->

								<div class="form-group">

									<label class="col-xs-2 control-label">Clave de Unidad</label>
									<div class="col-xs-1">
										<input type="text" class="form-control" name="txtIdUnidad" id="txtIdUnidad" />
									</div>

									<label class="col-xs-2 control-label">Nombre de Unidad</label>
									<div class="col-xs-7">
										<input type="text" class="form-control" name="txtNombre" id="txtNombre" />
									</div>

								</div>
								<br>

								<div class="form-group">
									<label class="col-xs-2 control-label">Siglas de Unidad</label>
									<div class="col-xs-1">
										<input type="text" class="form-control" name="txtSiglas" id="txtSiglas" />
									</div>

									<label class="col-xs-2 control-label">Nombre del Titular</label>
									<div class="col-xs-7">
										<input type="text" class="form-control" name="txtTitular" id="txtTitular" />
									</div>

								</div>
								<br>
								<br>

								<!--
								<div class="form-group">
									<label class="col-xs-2 control-label">Area Auditora</label>
									<div class="col-xs-4">
										<select name="txtIdArea" id="txtIdArea" class="form-control" onChange="">
											<option value="">Seleccione...</option>
										</select>
									</div>
								</div>
								<br>
								-->

								<div class="form-group">
									<label class="col-xs-2 control-label">Sectores</label>
									<div class="col-xs-2">
										<select name="txtIdUnidadSector" id="txtIdUnidadSector" class="form-control" onChange="									javascript:recuperarListaLigada('poderesBySector', this.value, document.all.txtIdUnidadPoder);">
											<option value="">Seleccione...</option>
										</select>
									</div>

									<label class="col-xs-1 control-label">Poderes</label>
									<div class="col-xs-3">
										<select name="txtIdUnidadPoder" id="txtIdUnidadPoder" class="form-control" onChange="javascript:recuperarListaLigada('clasificacionesByPoder', this.value, document.all.txtIdUnidadClasificacion);">
											<option value="">Seleccione...</option>
										</select>
									</div>

									<label class="col-xs-1 control-label">Agrupación</label>
									<div class="col-xs-3">
										<select name="txtIdUnidadClasificacion" id="txtIdUnidadClasificacion" class="form-control" >
											<option value="">Seleccione...</option>
										</select>
									</div>

								</div>
								<br>

								<!-- ********************** TABLA PARA REGISTRAR LAS AREAS QUE PUEDEN AUDITAR A LA UNIDAD ********************* -->
								<div class="table-responsive col-xs-12" style="height: 200px; overflow: auto; overflow-x:hidden;">	
									<br>
									<table class="table table-striped table-bordered table-hover">
										<caption>AREAS QUE AUDITAN A LA UNIDAD</caption>
										<thead>
											<tr><th width=5%">Id.Area</th><th width="70%">Nombre del Area Auditora</th></tr>
										</thead>
										<tbody id="tblAreasAuditoras">
										</tbody>
									</table>
								</div>

								<br>

								<!--
								<div class="form-group">
								</div>
								<br>

								<div class="form-group">
								</div>
								<br>

								<div class="form-group">
								</div>
								<br>
								-->

								<div class="form-group">
									<label class="col-xs-2 control-label">Siglas Jurídico</label>
									<div class="col-xs-1">
										<input type="text" class="form-control" name="txtJuridicoSiglas" id="txtJuridicoSiglas" />
									</div>

									<label class="col-xs-2 control-label">Clasificación Jurídico</label>
									<div class="col-xs-3" >
										<select name="txtJuridicoClasif" class="form-control" >
											<option value="">Seleccione...</option>
											<option value="DEPENDENCIAS">DEPENDENCIAS</option>
											<option value="DELEGACIONES">DELEGACIONES</option>
											<option value="DESCONCENTRADOS">ÓRGANOS DESCONCENTRADOS</option>
											<option value="ENTIDADES">ENTIDADES</option>
											<option value="GOBIERNO">ÓRGANOS DE GOBIERNO</option>
											<option value="AUTONOMOS">ÓRGANOS AUTÓNOMOS</option>
										</select>
									</div>
								</div>

								<div class="form-group" id="divEstatus">
									<label class="col-xs-2 control-label">Estatus Unidad</label>
									<div class="col-xs-2" >
										<select name="txtEstatus" class="form-control" >
											<option value="">Seleccione...</option>
											<option value="ACTIVO" selected="">ACTIVO</option>
											<option value="INACTIVO">INACTIVO</option>
										</select>
									</div>
								</div>
								<div class="clearfix"></div>


							</div>
							<div class="clearfix"></div>
						</div>

						<div class="modal-footer">
							<button  type="button" class="btn btn-primary active" id="btnGuardar" 	style="display:inline;">Guardar</button>
							<button  type="button" class="btn btn-default active" id="btnCancelar" 	style="display:inline;">Cancelar</button>
						</div>
					</div>
				</form>
			</div>
		</div>


		<div id="modalAsignacion" class="modal fade" role="dialog">
			<div class="modal-dialog">							
					<!-- Modal content-->
				<div class="modal-content">									

					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h3 class="modal-title"><i class="fa fa-pencil-square-o"></i>Asignación de Unidades a Cuenta Nueva...</h3>
					</div>

					<div class="modal-body">

							<br>
							<div class="form-group">
								<label class="col-xs-4 control-label" style="text-align:right">Replicar las Unidades con la Cuenta</label>
								<div class="col-xs-4">
									<select class="form-control" name="txtCuentaOrigen">
										<option value="">Seleccione...</option>														
									</select>
								</div>
							</div>
							<br>

							<div class="form-group">
								<label class="col-xs-4 control-label" style="text-align:right">Cuenta para las Unidades Replicadas</label>
								<div class="col-xs-4">
									<select class="form-control" name="txtCuentaDestino">
										<option value="">Seleccione...</option>														
									</select>
								</div>
							</div>
							<br>
							<br>
							<br>

						<div class="clearfix"></div>
					</div>
					
					<div class="modal-footer">
						<div class="pull-right">
							<button type="button" class="btn btn-primary active" id="btnGuardarAsignacion" type="submit"><i class="fa fa-floppy-o"></i> Iniciar Asignación de Unidades</button>
							<button type="button" class="btn btn-default active" id="btnCancelarAsignacion" data-dismiss="modal"><i class="fa fa-back"></i> Cancelar Asignación</button>
						</div>
						<div class="clearfix"></div>
					</div>

				</div>		
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

	</body>
</html>
