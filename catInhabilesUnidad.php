<!DOCTYPE html>
<!-- saved from url=(0035)http://ashobiz.asia/mac52/macadmin/ -->
<html lang="en"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">  
  <meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=Edge" />
   		<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.1/themes/base/jquery-ui.css" />

		<script type="text/javascript" src="js/canvasjs.min.js"></script>
		<script type='text/javascript' src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js?ver=3.1.2"></script>
		<script type="text/javascript" src="js/genericas.js"></script>
		<!--
		<script src="https://maps.googleapis.com/maps/api/js?sensor=false&libraries=geometry,places&ext=.js"></script>
		 -->
		<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
		<script src="http://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>
		<script src="jquery.ui.datepicker-es.js"></script>



  	<style type="text/css">		
		@media screen and (min-width: 768px) {
			#mapa_content {width:100%; height:150px;}
			#canvasJG, #canvasJD, #canvasDIP{height:175px; width:100%;}			
			#modalInhabil .modal-dialog  {width:70%;}
			.auditor{background:#f4f4f4; font-size:6pt; padding:7px; display:inline; margin:1px; border:1px gray solid;}
			
			label {text-align:right;}
			
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
  
	<!--
	*****************************
  	INICIO DE CÓDIGO JAVASCRIPT
	*****************************
	HVS 2016/05/17 Falta validar los siguiente:
	Que en la alta al momento de guardar el primer campo de fecha que se autollene el segundo con la misma fecha
	Que en la alta no se muestre en la vista modal el campo estatus, pero en la actualización sí.
	Que el Nombre del día no se repita para la misma cuenta revisar uno a uno cuando son rangos de fecha
	Que no se empalmen el/los días si ya estan dados de alta
	Que los días inhabiles no se repitan
	Que No sean fin de semana

	-->
	<script type="text/javascript"> 
		var mapa;
		var nZoom=10;
		

		function cajaResultados(tipo){
			if(tipo!="")
				document.all.divResumen.style.display='inline';	
			else 
				document.all.divResumen.style.display='none';	
		}	
		
		window.onload = function () 
		{
		};

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
			onClose: function( selectedDate ) {
				$( "#datepicker2" ).datepicker( "option", "minDate", selectedDate );
			}
			});



			$("#datepicker2").datepicker({
			dateFormat:"yy-mm-dd",
			firstDay: 1,
			numberOfMonths: 2,
			onClose: function( selectedDate ) {
				$( "#datepicker" ).datepicker( "option", "maxDate", selectedDate );
			}
			});


			});


		function validarInhabiles(){

			if (document.all.txtUnidad.selectedIndex==0){ alert("Debe seleccionar una UNIDAD."); document.all.txtUnidad.focus(); return false; }
				
			if (document.all.txtTipo.value=="") { alert("Debe capturar el TIPO de la fecha inhábil."); document.all.txtTipo.focus(); return false; }
			if (document.all.txtNombre.value==0) { alert("Debe capturar el NOMBRE de la fecha inhábil."); document.all.txtNombre.focus(); return false; }
			if (document.all.txtFechaInicial.value=="") { alert("Debe capturar la FECHA INICIAL inhábil."); document.all.txtFechaInicial.focus(); return false; }
			if (document.all.txtFechaFinal.value=="") { alert("Debe capturar la FECHA FINAL inhábil."); document.all.txtFechaFinal.focus(); return false; }
			if (document.all.txtEstatus.value=="") { alert("Debe capturar el ESTATUS."); document.all.txtFechaFinal.focus(); return false; }

			if (document.all.txtUnidad.selectedIndex==0){ alert("Debe seleccionar una UNIDAD."); document.all.txtUnidad.focus(); return false; }
				

			// Agregar validaciones de rangos: FechaInicial menor a FechaFinal
			// Agregar validación de no guardar sábados o domingos

			
			if (document.all.txtFechaInicial != "" && document.all.txtFechaFinal != "")
			{
				/*
				var fech1 = document.getElementById("txtFechaInicial").value;
				var fech2 = document.getElementById("txtFechaFinal").value;

				//document.write('El valor de fech1 es: '+ fech1);
				//document.write('El valor de fech2 es: '+ fech2);


				if( (Date.parse(fech1)) > (Date.parse(fech2)) )
				{
					alert(‘La fecha inicial no puede ser mayor que la fecha final’);
					return false;
				}
				

					//Comprobamos que tenga formato correcto
				var Fecha_aux = document.getElementById("txtFechaInicial").value.split("/");
 				var Fecha1 = new Date(parseInt(fecha_aux[2]),parseInt(fecha_aux[1]-1),parseInt(fecha_aux[0]));
				Fecha_aux = document.getElementById("txtFechaFinal").value.split("/");
 				var Fecha2 = new Date(parseInt(fecha_aux[2]),parseInt(fecha_aux[1]-1),parseInt(fecha_aux[0]));
  
				 	//Comprobamos si existe la fecha
				if (isNaN(Fecha1)){
					alert("Fecha Inicial introducida incorrecta");
					return false;
				}
				else{
					alert("La fecha Inicial que has introducido es "+Fecha1);
				}
			 	//Comprobamos si existe la fecha
				if (isNaN(Fecha2)){
					alert("Fecha Final introducida incorrecta");
					return false;
				}
				else{
					alert("La fecha Final que has introducido es "+Fecha2);
				}
				*/
			}
			return true;
		}
		
		function nuevoInhabil(){
			document.all.txtOperacion.value="INS";
			limpiarCampos();
			seleccionarElemento(document.all.txtEstatus, 'ACTIVO');
			
			$('#modalInhabil').removeClass("invisible");
			$('#modalInhabil').modal('toggle');
			$('#modalInhabil').modal('show');
			//document.all.tabSujetos.style.display='none';		
			//document.all.btnCargaMasiva.style.display='none';
		}


		function recuperaInhabil(id)
		{
			//alert("Valor de id: " + id);
			$.ajax(
			{
				type: 'GET', url: '/lstInhabilUnidadByID/' + id ,
				success: function(response) {
					var obj = JSON.parse(response);

					//alert(response);
					//alert(obj.idDia);
					limpiarCampos();
					//document.all.txtOperacion.value='UPD';
					document.all.txtOperacion.value= "UPD";
					
					document.all.txtCuenta.value='' + obj.idCuenta;
					document.all.txtDia.value='' + obj.idDia;
					document.all.txtTipo.value='' + obj.tipo;
					seleccionarElemento(document.all.txtUnidad, obj.centroGestor);		
					document.all.txtNombre.value='' + obj.nombre;				
					document.all.txtFechaInicial.value='' + obj.fInicio;				
					document.all.txtFechaFinal.value='' + obj.fFin;				
					////seleccionarElemento(document.all.txtEstatus, obj.estatus);		
					document.all.txtEstatus.value='' + obj.estatus;		

					//seleccionarElemento(document.all.txtArea, obj.area);
					//seleccionarElemento(document.all.txtTipoAuditoria, obj.tipo);
					//seleccionarElemento(document.all.txtSujeto, obj.sujeto);
					//seleccionarElemento(document.all.txtObjeto, obj.objeto);
					//recuperarListaSelected('lstObjetosBySujeto', obj.sujeto, document.all.txtObjeto, obj.objeto)
					
					
					//document.all.txtObjetivo.value='' + obj.objetivo;
					//recuperarTabla('tblActividadesByAuditoria', obj.auditoria, document.all.tablaActividades);
					//document.all.listasAuditoria.style.display='none';
					//document.all.capturaAuditoria.style.display='inline';
					
					//document.write('recuperarInhabil - Valor de txtOperacion: '.document.all.txtOperacion.value);

					$('#modalInhabil').removeClass("invisible");
					$('#modalInhabil').modal('toggle');
					$('#modalInhabil').modal('show');								
			},
			error: function(xhr, textStatus, error)
			{
				alert('FUNCTION OBTENER DATOS   -> statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error + ' SECCION: ' + id);
			}			

			});		
		}	

		function limpiarCampos(){
       		document.all.txtUnidad.selectedIndex=0;
			document.all.txtDia.value = 0;
       		document.all.txtTipo.selectedIndex=0;
            document.all.txtNombre.value = '';
            document.all.txtFechaInicial.value = '';
            document.all.txtFechaFinal.value = '';
            document.all.txtEstatus.selectedIndex = 0;
		}


		var nUsr='<?php echo $_SESSION["idUsuario"];?>';
		var nCampana='<?php echo $_SESSION["idCuentaActual"];?>';
		
		$(document).ready(function(){
			getMensaje("txtNoti",1);

			recuperarLista('lstUnidadesRespByArea', document.all.txtUnidad);
		
			if(nUsr!="" && nCampana!=""){
				cargarMenu( nCampana);			
			}else{
				if(nCampana=="")alert("Debe establecer una CUENTA PÚBLICA como PREDETERMINADA. Por favor consulte con su administrador del sistema.");
			}
			

			$( "#btnGuardar" ).click(function() {

				if (validarInhabiles())
				{ 
					var liga = '/existeDiaInhabilesUnidad/' + document.all.txtFechaInicial.value + "|" + document.all.txtFechaFinal.value + "|" + document.all.txtUnidad.value;

					$.ajax({ type: 'GET', url: liga , success: function(response) 
    	    		{
						//var obj = JSON.parse(response);

						if(response == "SI"){
							alert("Una de las fechas del periodo ingresado ya existe\n como día inhábil para la unidad seleccionada.");
               			}else{
							//alert("Nnguna de las fechas del periodo ingresado ya existe\n como día inhábil para la unidad seleccionada.");
							document.all.formulario.submit(); 
               			}
					},
						error: function(xhr, textStatus, error)
					{
						alert('FUNCTION :validaFechasInabiles-> statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
					}			
					});		
				}
			});

			/*
			$( "#btnAgregar" ).click(function() {
				document.all.txtOperacion.value='INS';
				limpiarCampos();
				$('#modalInhabil').removeClass("invisible");
				$('#modalInhabil').modal('toggle');
				$('#modalInhabil').modal('show');
			});
			*/

			$( "#btnCancelar" ).click(function() {
				document.all.txtOperacion.value='';
				$('#modalInhabil').removeClass("invisible");
				$('#modalInhabil').modal('toggle');
				$('#modalInhabil').modal('hide');
			});
			
		});


	
	</script>

<!--
*****************************
FIN DE CÓDIGO JAVASCRIPT
*****************************

****************************************************************************************************************************************
INCIO DE SECCIÓN DE CÓDIGO QUE NO SE MODIFICA CON EXCEPCION DE EL TITULO DE LA VENTANA O SE INCLUYA UNA OPCIÓN NUEVA DEL MENÚ PRINCIPAL
 ****************************************************************************************************************************************
  -->

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
					<div class="col-xs-3"><h2>Días Inhabiles por Unidad</h2></a></div>									
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

	<!--
    ****************************************************************************************************************************************
    FIN DE SECCIÓN DE CÓDIGO QUE NO SE MODIFICA
    ****************************************************************************************************************************************
  -->

 	  	<!-- Main bar -->
  	<div class="mainbar">
					<div class="row">
						<div class="col-xs-12">
							<div class="widget">
								<div class="widget-head">
								  <div class="pull-left"><h3 class="modal-title"><i class="fa fa-tasks"></i> Días Inhabiles de Unidades</h3></div>
								  <div class="widget-icons pull-right">
								  	<button onclick="nuevoInhabil();" type="button" class="btn btn-primary  btn-xs">Agregar Días Inhabiles</button> 	
								  	<!--
									<button onclick="nuevoInhabil();" type="button" class="btn btn-primary active btn-xs" ><i class="fa fa-floppy-o"></i> Agregar Días Inhabiles</button>
									-->
								  </div>  
								  <div class="clearfix"></div>
								</div>             
								<div class="widget-content">
									<div class="table-responsive" style="height: 500px; overflow: auto; overflow-x:hidden;">
										<table class="table table-striped table-bordered table-hover table-condensed">
										  <thead>
												<tr><th width="8%">Tipo Inábil</th><th width="20%">Nombre</th><th width="20%">Fecha Inicial</th width="20%"><th>Fecha Final</th><th width="20%">Unidad</th><th width="20%">Estatus</th></tr>
										  </thead>
										  <tbody>				

											<?php foreach($datos as $key => $valor): ?>
											<tr onclick=<?php echo "javascript:recuperaInhabil('" . $valor['idDia'] . "');"; ?>
												style="width: 100%; font-size: xx-small">

											  <td><?php echo $valor['tipo']; ?></td>
											  <td><?php echo $valor['nombre']; ?></td>
											  <td><?php echo $valor['fInicio']; ?></td>
											  <td><?php echo $valor['fFin']; ?></td>
											  <td><?php echo $valor['unidad']; ?></td>
											  <td><?php echo $valor['estatus']; ?></td>
											</tr>
											<?php endforeach; ?>
																						
										  </tbody>
										</table>
									</div>
								</div>
								<div class="widget-foot">
								</div>
							</div>				
						</div>
					</div>
				
	</div>			
</div>
<!--
***************************
INICIO DE VENTANA FLOTANTE 
***************************
-->
<div id="modalInhabil" class="modal fade" role="dialog">
		<div class="modal-dialog">							
				<!-- Modal content-->
			<div class="modal-content">									
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h3 class="modal-title"><i class="fa fa-pencil-square-o"></i> Registrar Fechas Inhabiles...</h3>
				</div>									
				<div class="modal-body">
							<form id="formulario" METHOD='POST' ACTION="/guardar/inhabilesUnidad" role="form">
								<input type='HIDDEN' name='txtCuenta' value='<?php echo $_SESSION["idCuentaActual"];?>'>
								<input type='HIDDEN' name='txtDia' value=''>					
								<input type='HIDDEN' name='txtOperacion' value=''>						
								<br>
								<div class="form-group">						
									<label class="col-xs-2 control-label">Unidad</label>
									<div class="col-xs-10">
										<select class="form-control" name="txtUnidad">
											<option value="">Seleccione la Unidad...</option>
										</select>
									</div>
								</div>
								<br>
								<div class="form-group">						
									<label class="col-xs-2 control-label">Tipo</label>
									<div class="col-xs-10">
										<select class="form-control" name="txtTipo">
											<option value="">Ingrese el Tipo Inhábil...</option>
											<option value="INHABIL">INHÁBIL</option>
											<option value="INSTITUCIONAL">INSTITUCIONAL</option>
											<option value="VACACIONES">VACACIONES</option>
										</select>
									</div>
								</div>

								<br>
								<div class="form-group">
									<label class="col-xs-2 control-label">Nombre </label>
									<div class="col-xs-10">
										<input type="text" class="form-control" name="txtNombre" id="txtNombre" />
									</div>						
								</div>						
								
								<br>
								<div class="form-group">
									<label class="col-xs-2 control-label">Fecha Inicio</label>
									<div class="col-xs-2">
									    <!--
										<input type="text" class="form-control" name="txtFechaInicial" id="txtFechaInicial"/>
										-->
										<input type="text" id="datepicker" class="form-control" name="txtFechaInicial"/>
									</div>

									<label class="col-xs-2 control-label">Fecha Término</label>
									<div class="col-xs-2">
									    <!--
										<input type="text" class="form-control" name="txtFechaFinal"  id="txtFechaFinal" />
										-->
										<input type="text" id="datepicker2" class="form-control" name="txtFechaFinal"/>
									</div>
								</div>						
								
								<br>
								<div class="form-group">						
									<label class="col-xs-2 control-label">Estatus</label>
									<div class="col-xs-10">
										<select class="form-control" name="txtEstatus">
											<option value="">Ingrese el Estatus del Registro...</option>
											<option value="ACTIVO">ACTVO</option>
											<option value="INACTIVO">INACTIVO</option>
										</select>
									</div>
								</div>

								<div class="clearfix"></div>								
							</form>
					<div class="clearfix"></div>
				</div>
				
				<div class="modal-footer">
					<div class="pull-left">
						<form id="upload_form" enctype="multipart/form-data" method="post">
							<button  type="button" class="btn btn-default" id="btnCargarArchivo" style="display:none;"><i class="fa fa-link"></i> Anexar Archivo...</button>
							<input type="file" name="btnUpload" accept="application/pdf,application/vnd.ms-excel,,application/vnd.ms-word, " style="display:none;" id="btnUpload">
							<progress id="progressBar" value="0" max="100" style="width:'100%'; display:none;"></progress>
							<h3 id="status"></h3>
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

<!--
************************
FIN DE VENTANA FLOTANTE 
************************
-->

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


