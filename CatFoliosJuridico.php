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
			#modalFolioJuridico .modal-dialog {width:60%;}

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
	var dejarReservados;

	function limpiarDatos(){
		//document.all.txtIdFolioJuridico.value        = 0;
		document.all.txtFolio.value                  = 0;
		document.all.txtIdArea.selectedIndex         = 0;
		document.all.txtIdDocumento.selectedIndex    = 0;
		document.all.txtIdSubDocumento.selectedIndex = 0;
		document.all.txtObservaciones.value          = '';
		document.all.txtEstado.value                 = '';
		document.all.txtUsrAutorizador				 = 0;
		document.all.txtEstatus.selectedIndex        = 0;
	}

	function validarDatos(){

		if (document.all.txtFolio.value == 0 ){
			alert("El valor del FOLIO no de debe ser cero.");
			document.all.txtFolio.focus();
			return false;
		}

		if (document.all.txtIdArea.selectedIndex == 0 ){
			alert("Debe seleccionar una ÁREA.");
			document.all.txtIdArea.focus();
			return false;
		}

		if (document.all.txtIdDocumento.selectedIndex == 0){
			alert("Debe seleccionar un TIPO DE DOCUMENTO.");
			document.all.txtIdDocumento.focus();
			return false;
		}
		/*
		if (document.all.txtIdSubDocumento.selectedIndex == 0){
			alert("Debe seleccionar un TIPO DE SUB-DOCUMENTO.");
			document.all.txtIdSubDocumento.focus();
			return false;
		}
		*/

		if (document.all.txtOperacion == 'UPD' ){
			if (document.all.txtEstatus.selectedIndex == 0){
				alert("Debe seleccionar el ESTADO del PUESTO.");
				document.all.txtEstatus.focus();
				return false;
			}
		}
		
		return true;
	}

	// ******************  ALTA DE REGISTROS  *********************

	function ObtenerUltimoFolioJuridico(){
		var folioSiguiente = 0;
		dejarReservados = "NO";

		$.ajax({ type: 'GET', url: '/recuperaUltimoFolioJuridico' ,
			success: function(response) {
				var obj = JSON.parse(response);		// Cuando solo se regresa un registro

				var ultimoFolio   = parseInt(obj.folio);
				var fechaUltFolio = new Date(obj.fechaFolio);

				//if ( typeof(fechaUltFolio) != "undefined" ){
				//alert("El valor de ultimoFolio es:" + ultimoFolio)	;
				//alert("El valor typeof(ultimoFolio) es:" + typeof(ultimoFolio) )	;
				//alert("El valor de ultimoFolio es :" + isNaN(ultimoFolio) )	;

				if ( ultimoFolio == null || isNaN(ultimoFolio) != true){

					var hoy = new Date();
					var diasDif = hoy.getTime() - fechaUltFolio.getTime();
					var diasDiferencia = Math.round(diasDif/(1000 * 60 * 60 * 24));

					if ( diasDiferencia > 0){
						dejarReservados = "SI";
						folioSiguiente = ultimoFolio + 4;
					}else{
						folioSiguiente = ultimoFolio + 1;
					}
					//alert("diasDiferencia>> " + diasDiferencia + "  folioSiguiente>> " + folioSiguiente + " ultimoFolio>> " + ultimoFolio);
				}else{
					dejarReservados = "SI";
					folioSiguiente = 4;
				}
			},
				error: function(xhr, textStatus, error){
				alert(' Error en function ObtenerUltimoFolioJuridico()  TextStatus: ' + textStatus + ' Error: ' + error );
				folioSiguiente = 0;
			}, async: false, cache: false 
		});
		return folioSiguiente;
	}

	// ******************  ALTA DE REGISTROS  *********************

	function agregarFolioJuridico() {
		var folioJuridicoSiguiente;

		limpiarDatos();
		document.all.txtFolio.value = ObtenerUltimoFolioJuridico();
		document.all.txtOperacion.value = "INS";

		// Como es alta ocultar el elemento estatus
		document.getElementById("divEstado").style.display="none";
		document.getElementById("divAutorizador").style.display="none";
		document.getElementById("divEstatus").style.display="none";
		seleccionarElemento(document.all.txtEstatus,'ACTIVO');

		$('#modalFolioJuridico').removeClass("invisible");
		$('#modalFolioJuridico').modal('toggle');
		$('#modalFolioJuridico').modal('show');

	}

	// ******************  MODIFICACION DE REGISTROS  *******************

	function actualizarFolioJuridico(sIdFolioJuridico){

		dejarReservados = "NO";

		var sLiga = '/obtenerFolioJuridico/' + sIdFolioJuridico;

		$.ajax({ type: 'GET', url: sLiga ,
			success: function(response) {
	            //var jsonData = JSON.parse(response);   // Cuando se regresan mas de un registro
				var obj = JSON.parse(response);		// Cuando solo se regresa un registro

				limpiarDatos();

				document.all.txtOperacion.value = "UPD";
				// Como es actualización será necesario asegurarse de que se muestre elemento estatus

				document.getElementById("divEstado").style.display="inline";
				document.getElementById("divAutorizador").style.display="inline";
				document.getElementById("divEstatus").style.display="inline";

				//seleccionarElemento(document.all.txtTipoAuditoria, obj.idTipoAuditoria);

				document.all.txtIdFolioJuridico.value      = obj.idFolioJuridico;
				document.all.txtFolio.value                = obj.folio;
				document.all.txtFolioAnterior.value        = obj.folio;
				document.all.txtObservaciones.value        = obj.observaciones;
				document.all.txtEstado.value 		       = obj.estado;
				document.all.txtUsrAutorizador.value       = obj.usrAutorizador;
				document.all.txtNombreUsrAutorizador.value = obj.nombreAutorizador;

				seleccionarElemento(document.all.txtIdArea, obj.idArea);
				seleccionarElemento(document.all.txtIdDocumento, obj.idTipoDocumento);

				actualizarSubDocumentosByTipoDocto(obj.idTipoDocumento);

				seleccionarElemento(document.all.txtIdSubDocumento, obj.idSubtipoDocumento);
				seleccionarElemento(document.all.txtEstatus, obj.estatus);

				/*
				if( obj.estado != "RESERVADO"){
					document.all.btnGuardar.disabled = true;
				}else{
					document.all.btnGuardar.disabled = false;
				}
				*/

				$('#modalFolioJuridico').removeClass("invisible");
				$('#modalFolioJuridico').modal('toggle');
				$('#modalFolioJuridico').modal('show');

			},
				error: function(xhr, textStatus, error){
				alert(' Error en function actualizarFolioJuridico()  TextStatus: ' + textStatus + ' Error: ' + error );
				return false;
			},
				async: false, // La petición es síncrona
				cache: false // No queremos usar la caché del navegador
		});
	}


	function validaExistenciaFolioJuridico(){
		var valorRegreso;
		var sLiga = '/validaExisteFolioJuridico/' + document.all.txtFolio.value;

		$.ajax({ type: 'GET', url: sLiga , 
			success: function(response) {		
				var obj = JSON.parse(response);
				if ( typeof(obj.folio) == "undefined"){
					valorRegreso = "NO";
					//return false;
				}else{ 
					valorRegreso = "SI";
					//return true;
				}
	        },
	          error: function(xhr, textStatus, error){
			  valorRegreso = "SI";
	          	//return true;
	        },
				async: false , // La petición es síncrona
				cache: false  // No queremos usar la caché del navegador
		}); 
		return valorRegreso;
	}

	function GuardarFolio(){
		//var yaExisteFolioJuridico = validaExistenciaFolioJuridico();
		//alert("El valor de yaExisteFolioJuridico es: " + yaExisteFolioJuridico);

		//if ( validaExistenciaFolioJuridico() == "NO" ){

			if (document.all.txtOperacion.value == "INS"){

				if ( dejarReservados == "SI"){ 
					guardaFoliosReservados(); 
				}
				document.all.txtFolio.disabled = false;
				document.all.formulario.submit();
				$('#modalFolioJuridico').modal('hide');

			}else{ // Operacion = UPD solo se actualizaran los folios reservados

				if (document.all.txtEstado.value == "RESERVADO"){

					document.all.txtEstado.value = "AUTORIZADO"
					document.all.txtUsrAutorizador.value = nUsr;

					document.all.txtFolio.disabled = false;
					document.all.formulario.submit();
					$('#modalFolioJuridico').modal('hide');

				}else{
					alert("El Folio de oficios ya ha sido asignado por lo tanto NO puede ser alterado.");
				}
			}	
		//} else {
		//	alert("El Folio de oficios número : " + document.all.txtFolio.value + " Ya existe, Por favor verifique.");
		//}
	}

	function guardaFoliosReservados(){
		//---- Iniciará agregando los tres registros vacios si fue el cambio de día
		var i = 3;
		while (i > 0 ){

			sLiga2 = "/guardar/FolioJuridicoReservados/" + (document.all.txtFolio.value - i );
			//alert("El valor de sLiga2 es: " + sLiga2);
			
			$.ajax({
				async:true, cache:false, type: 'GET', url: sLiga2 ,
				success: function(response) {			
			    },
						error: function(xhr, textStatus, error){
   					alert('func: Guardar Folio Jurídico ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
				}, async: false, cache: false
			})
			i--
		}
	}

	function  actualizarSubDocumentosByTipoDocto(idTipoDocto){
		recuperarListaSincrona("lstSubDocumentosByTipoDocto/" + idTipoDocto,document.all.txtIdSubDocumento);
	}

	function recuperarListaSincrona(lista, cmb){
		//alert("Recuperando Lista: " + lista);
		$.ajax({
				type: 'GET', url: '/'+ lista  ,
				success: function(response) {
					var jsonData = JSON.parse(response);
					
					//alert("Registros recuperados:" + jsonData.datos.length);

					//Vacia la lista
					while (cmb.length>1){
						cmb.remove(cmb.length-1);
					}
						
					//Agregar elementos
					for (var i = 0; i < jsonData.datos.length; i++) {
						var dato = jsonData.datos[i];
						var option = document.createElement("option");
						option.text = dato.texto;
						option.value = dato.id;
						cmb.add(option, i+1);
					}				
				},
				error: function(xhr, textStatus, error){
					alert("ERROR EN: function recuperarLista(lista, cmb)" + " Donde lista=" + lista );
				}, async: false, cache: false			
			});	
	} 
	// ********************************************* ZONA DE JQUERY ******************************************
	var nUsr='<?php echo $_SESSION["idUsuario"];?>';
	var nCampana='<?php echo $_SESSION["idCuentaActual"];?>';

	$(document).ready(function(){

		getMensaje('txtNoti',1);

		if(nUsr!="" && nCampana!=""){
			cargarMenu( nCampana);
		}else{
			if(nCampana=="")alert("Debe establecer una CAMPAÑA como PREDETERMINADA. Por favor consulte con su administrador del sistema.");
		}
		// **************************** LLENADO DE COMBOS INICIALES *****************************************

		//recuperarLista("lstTiposAuditorias",document.all.txtTipoAuditoria);

		recuperarLista("lstAreasByAreaSuperior/DGAJ",document.all.txtIdArea);
		
		recuperarLista("lstTiposDocumentosByTipo/JURIDICO",document.all.txtIdDocumento);

		// **************************** DEFINICIONES DE BOTONES ***********************************************
		
		$( "#btnGuardar" ).click(function() {
			if ( validarDatos() ){ GuardarFolio(); }
		});


		$( "#btnCancelar" ).click(function() {
			$('#modalFolioJuridico').modal('hide');
		});

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
							<div class="col-xs-3"><h2>Folios Área Jurídica</h2></a></div>
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
							<div class="pull-left">Lista de Folios del área Juríidica</div>
							<div widget-icons pull-right">
								<div class="widget-icons pull-right"><button id="btnagregarFolioJuridico" onclick="agregarFolioJuridico();" class="btn btn-primary btn-xs">Agregar Folio</button></div>  
							</div>
							<div class="clearfix"></div>
						</div>

						<div class="widget-content">
							<div class="table-responsive" style="height: 400px; overflow: auto; overflow-x:hidden;">
								<table class="table table-striped table-bordered table-hover table-condensed ">
									<thead> 
										<tr>
									 		<th>Cuenta</th>
									 		<th>Folio</th>
									 		<th>Área</th>
									 		<th>Tipo Documento</th>
									 		<th>Tipo Sub-Documento</th>
									 		<th>Estado Folio</th>
									  		<th>Estatus Registro</th>
										</tr>
								  	</thead>
								  	<tbody id="tblListaFolioJuridico" >
										<?php foreach($datos as $key => $valor): ?>

											<tr onclick=<?php echo "javascript:actualizarFolioJuridico('" . $valor['idFolioJuridico'] . "');"; ?> style="width: 100%; font-size: xx-medium"> 

										  	<td width="8%"><?php  echo $valor['idCuenta']; ?></td> 
										  	<td width="5%"><?php  echo $valor['folio']; ?></td> 
										  	<td width="30%"><?php  echo $valor['area']; ?></td>
										  	<td width="10%"><?php  echo $valor['tipoDocumento']; ?></td>
										  	<td width="10%"><?php  echo $valor['subTipoDocumento']; ?></td>
										  	<td width="8%"><?php  echo $valor['estado']; ?></td>
										  	<td width="8%"><?php  echo $valor['estatus']; ?></td>
										</tr>
										<?php endforeach; ?>
								  	</tbody >
								</table>
							</div>
						</div>

						<div class="widget-foot">
							<div class="pull-left"></div>
						</div>

					</div>
				</div>
				<!-- Matter ends -->
			</div>
		   	<!-- Mainbar ends -->
		   <div class="clearfix"></div>
		</div>
		<!-- Content ends -->

		<div id="modalFolioJuridico" class="modal fade" role="dialog">
			<div class="modal-dialog">
				<form id="formulario" METHOD='POST' action='/guardar/FolioJuridico' role="form">
					<input type='HIDDEN' name='txtOperacion' value=''>
					<input type='HIDDEN' name='txtIdFolioJuridico' value=''>
					<input type='HIDDEN' name='txtUsrAutorizador' value=''>
					<input type='HIDDEN' name='txtFolioAnterior' value=''>


					<!-- Modal content-->
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4 class="modal-title">Registro de Folio del Área Jurídica...</h4>
						</div>

						<div class="modal-body">
							<div class="container-fluid">


								<div class="form-group">
									<label class="col-xs-2 control-label">Número de Folio</label>
									<div class="col-xs-2">
										<input type="text" class="form-control" name="txtFolio" id="txtFolio" readonly />
									</div>
								</div>
								<br>

								<div class="form-group">
									<label class="col-xs-2 control-label">Area Solicitante</label>
									<div class="col-xs-7">
										<select class="form-control" name="txtIdArea" id="txtIdArea" >
											<option value="">Seleccione...</option>
										</select>
									</div>
								</div>
								<br>

								<div class="form-group">
									<label class="col-xs-2 control-label">Tipo de Documento</label>
									<div class="col-xs-7">
										<select class="form-control" name="txtIdDocumento" id="txtIdDocumento" onChange="javascript: actualizarSubDocumentosByTipoDocto(this.value);">
											<option value="">Seleccione...</option>
										</select>

									</div>
								</div>
								<br>

								<div class="form-group">
									<label class="col-xs-2 control-label">Tipo Sub-Documento</label>
									<div class="col-xs-7">
										<select class="form-control" name="txtIdSubDocumento" id="txtIdSubDocumento">
											<option value="">Seleccione...</option>
										</select>
									</div>
								</div>
								<br>

								<div class="form-group">
									<label class="col-xs-2 control-label">Observaciones</label>
									<div class="col-xs-7">
										<input type="text" class="form-control" name="txtObservaciones" id="txtObservaciones" >
									</div>
								</div>
								<br>

								<div class="form-group" id="divEstado">
									<div class="form-group">
										<label class="col-xs-2 control-label">Situacion de folio</label>
										<div class="col-xs-2">
											<input type="text" class="form-control" name="txtEstado" id="txtEstado" readonly />
										</div>
									</div>
								</div>
								<br>

								<div class="form-group" id="divAutorizador">
									<div class="form-group">
										<label class="col-xs-2 control-label">Autorizador</label>
										<div class="col-xs-5">
											<input type="text" class="form-control" name="txtNombreUsrAutorizador" id="txtNombreUsrAutorizador" readonly />
										</div>
									</div>
								</div>
								<br>

								<div class="form-group" id="divEstatus">
									<div class="form-group">
										<label class="col-xs-2 control-label">Estatus Registro</label>
										<div class="col-xs-2" >
											<select name="txtEstatus" class="form-control" >
												<option value="">Seleccione...</option>
												<option value="ACTIVO" selected="">ACTIVO</option>
												<option value="INACTIVO">INACTIVO</option>
											</select>
										</div>
									</div>
								</div>

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