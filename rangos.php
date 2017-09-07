
<!DOCTYPE html>
<!-- saved from url=(0035)http://ashobiz.asia/mac52/macadmin/ -->
<html lang="en"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">  
  <meta charset="utf-8">
		<script type="text/javascript" src="js/canvasjs.min.js"></script>
		<script type='text/javascript' src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js?ver=3.1.2"></script>
		<script type="text/javascript" src="js/genericas.js"></script>
	
  	<style type="text/css">		
		@media screen and (min-width: 768px) {
			#mapa_content {width:100%; height:150px;}
			#canvasJG, #canvasJD, #canvasDIP{height:175px; width:100%;}			
			#modalCaptura .modal-dialog  {width:40%;}
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
  
	<script type="text/javascript"> 
		var ventana;
		
		function validarCaptura(){
			if (document.all.txtAnio.selectedIndex==0)
            {
				alert("Debe Seleccionar el AÑO del rango.");
				document.all.txtAnio.focus();
				return false;
			}
			
			if (document.all.txtToken.value=="")
            {
				alert("Debe capturar el TOKEN del rango.");
				document.all.txtToken.focus();
				return false;
			}
			
			if (document.all.txtSiglas.value=="")
            {
				alert("Debe capturar las SIGLAS del rango.");
				document.all.txtSiglas.focus();
				return false;
			}			
			if (document.all.txtDescripcion.value=="")
            {
				alert("Debe capturar el NOMBRE del rango.");
				document.all.txtDescripcion.focus();
				return false;
			}			
			
			if (document.all.txtInicio.value=="")
            {
				alert("Debe capturar el número de INICIO del rango.");
				document.all.txtInicio.focus();
				return false;
			}
			if (document.all.txtFin.value=="")
            {
				alert("Debe capturar el número de FIN del rango.");
				document.all.txtFin.focus();
				return false;
			}			
			
			if (document.all.txtMinimo.value=="")
            {
				alert("Debe capturar el número de MÍNIMO del rango.");
				document.all.txtMinimo.focus();
				return false;
			}

			if (document.all.txtEstatus.selectedIndex==0)
            {
				alert("Debe seleccionar el ESTATUS del rango.");
				document.all.txtEstatus.focus();
				return false;
			}
			return true;
		}
		

		function limpiarCampos(){			
       		document.all.txtAnio.selectedIndex=0;
            document.all.txtDescripcion.value = '';
			document.all.txtToken.value = '';
			document.all.txtSiglas.value = '';			
            document.all.txtInicio.value = '';
            document.all.txtFin.value = '';			
			document.all.txtDisponible.value = '';
			document.all.txtSiguiente.value = '';
			document.all.txtMinimo.value = '';		
            document.all.txtEstatus.selectedIndex = 1;
		}


		var nUsr='<?php echo $_SESSION["idUsuario"];?>';
		var nCampana='<?php echo $_SESSION["idCuentaActual"];?>';
		
		$(document).ready(function(){
		
			if(nUsr!="" && nCampana!=""){
				cargarMenu( nCampana);			
			}else{
				if(nCampana=="")alert("Debe establecer una CUENTA PÚBLICA como PREDETERMINADA. Por favor consulte con su administrador del sistema.");
			}

			$( "#btnGuardar" ).click(function() {
				if (validarCaptura()){ document.all.formulario.submit(); }
			});

			
			$( "#btnAgregar" ).click(function() {
				document.all.txtOperacion.value='INS';
				$('#txtEstatus').prop('disabled', true);
				limpiarCampos();
				$('#modalCaptura').removeClass("invisible");
				$('#modalCaptura').modal('toggle');
				$('#modalCaptura').modal('show');
			});
			

			$( "#btnCancelar" ).click(function() {
				document.all.txtOperacion.value='';
				$('#modalCaptura').removeClass("invisible");
				$('#modalCaptura').modal('toggle');
				$('#modalCaptura').modal('hide');
			});			
			
			$( "#btnCargaDocto" ).click(function() {		
				$("#btnUpload").click();
			});							
		});

		function recuperaElemento(id)
		{			
			$.ajax(
			{
				type: 'GET', url: '/rango/' + id ,
				success: function(response) {
					var obj = JSON.parse(response);
					//HVS 06/12/2016//alert(response);
					limpiarCampos();
					document.all.txtOperacion.value= "UPD";
					$('#txtEstatus').prop('disabled', false);
					document.all.txtID.value='' + obj.id;
					seleccionarElemento(document.all.txtAnio, obj.anio);							
					document.all.txtDescripcion.value='' + obj.descripcion;
					document.all.txtToken.value='' + obj.token;
					document.all.txtSiglas.value='' + obj.siglas;										
					document.all.txtInicio.value='' + obj.inicio;				
					document.all.txtFin.value='' + obj.fin;				
					document.all.txtDisponible.value='' + obj.disponible;				
					document.all.txtSiguiente.value='' + obj.siguiente;				
					document.all.txtMinimo.value='' + obj.minimo;									
					seleccionarElemento(document.all.txtEstatus, obj.estatus);

					$('#modalCaptura').removeClass("invisible");
					$('#modalCaptura').modal('toggle');
					$('#modalCaptura').modal('show');								
			},
			error: function(xhr, textStatus, error)
			{
				alert('FUNCTION OBTENER DATOS   -> statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error + ' SECCION: ' + id);
			}			

			});		
		}	

	
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
  <title>Sistema Integral de Auditorias: Rangos / folios</title>
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
					<div class="col-xs-3"><h2>Rangos / Folios</h2></a></div>									
					<div class="col-xs-2">
						<ul class="nav navbar-nav "><li><a href="./notificaciones"><i class="fa fa-envelope-o"></i> Usted tiene <span class="badge">0</span> Mensaje(s).</a></li></ul>
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
								  <div class="pull-left"><h3 class="modal-title"><i class="fa fa-tasks"></i> Rangos / Folios</h3></div>
								  <div class="widget-icons pull-right">
								  	<button type="button" class="btn btn-primary  btn-xs" id="btnAgregar">Agregar Rango...</button> 	
								  	<!--
									<button onclick="nuevoInhabil();" type="button" class="btn btn-primary active btn-xs" ><i class="fa fa-floppy-o"></i> Agregar Días Inhabiles</button>
									-->
								  </div>  
								  <div class="clearfix"></div>
								</div>             
								<div class="widget-content">
									<div class="table-responsive" style="height: 300px; overflow: auto; overflow-x:hidden;">
										<table class="table table-striped table-bordered table-hover table-condensed">
										  <thead>
											<tr><th>ID</th><th>Token</th><th>Descripción</th><th>Nomenclatura</th><th>Rango</th><th>Siguiente</th><th>Disponibles</th><th>Mínimo</th><th>Estatus</th></tr>
										  </thead>
										  <tbody>				

											<?php foreach($datos as $key => $valor): ?>
											<tr onclick=<?php echo "javascript:recuperaElemento('" . $valor['id'] . "');"; ?>
												style="width: 100%; font-size: xx-small">

											<td><?php echo $valor['id']; ?></td>
											<td><?php echo $valor['token']; ?></td>											  
											  <td><?php echo $valor['descripcion']; ?></td>											  
											  <td><?php echo $valor['siglas']; ?></td>
											  <td><?php echo $valor['rango']; ?></td>
											  <td><?php echo $valor['siguiente']; ?></td>
											  <td><?php echo $valor['disponible']; ?></td>
											  <td><?php echo $valor['minimo']; ?></td>
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


<div id="modalCaptura" class="modal fade" role="dialog">
		<div class="modal-dialog">							
				<!-- Modal content-->
			<div class="modal-content">									
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h3 class="modal-title"><i class="fa fa-pencil-square-o"></i> Registrar Rango...</h3>
				</div>									
				<div class="modal-body">
							<form id="formulario" METHOD='POST' ACTION="/guardar/rango" role="form">
								<input type='HIDDEN' name='txtOperacion' value=''>						
								<br>
								<div class="form-group">						
									<label class="col-xs-2 control-label">Id</label>
									<div class="col-xs-4">
										<input type="text" class="form-control" name="txtID" readonly/>
									</div>														
									<label class="col-xs-2 control-label">Año</label>
									<div class="col-xs-4">
										<select class="form-control" name="txtAnio">
											<option value="">Seleccione...</option>
											<option value="2014">2014</option>
											<option value="2015">2015</option>											
											<option value="2016">2016</option>											
										</select>
									</div>	
								</div>
								<br>
								<div class="form-group">
									<label class="col-xs-2 control-label">Token </label>
									<div class="col-xs-4">
										<input type="text" class="form-control" name="txtToken"/>
									</div>
									<label class="col-xs-2 control-label">Nomenclatura</label>
									<div class="col-xs-4">
										<input type="text" class="form-control" name="txtSiglas"/>
									</div>									
									
								</div>	
								<br>
								<div class="form-group">
									<label class="col-xs-2 control-label">Descripción</label>
									<div class="col-xs-10">
										<input type="text" class="form-control" name="txtDescripcion"/>
									</div>						
								</div>						
								
								<br>
								<div class="form-group">
									
									<label class="col-xs-2 control-label">Rango(s)</label>
									<div class="col-xs-5">
										<input type="text" class="form-control" name="txtInicio" placeholder="Inicio"/>
									</div>
									<div class="col-xs-5">
										<input type="text" class="form-control" name="txtFin" placeholder="Fin"/>
									</div>
								</div>

								<br>
								<div class="form-group">
									<label class="col-xs-2 control-label">Disponibles</label>
									<div class="col-xs-2">
										<input type="text" class="form-control" name="txtDisponible" placeholder="0.00" readonly/>
									</div>
									<label class="col-xs-2 control-label">Mínimo</label>
									<div class="col-xs-2">
										<input type="text" class="form-control" name="txtMinimo" placeholder="0.00"/>
									</div>	
									<label class="col-xs-2 control-label">Siguiente</label>
									<div class="col-xs-2">
										<input type="text" class="form-control" name="txtSiguiente" placeholder="0.00" readonly/>
									</div>	
									
								</div>

								<br>
								<div class="form-group">									
									<label class="col-xs-2 control-label">Estatus</label>
									<div class="col-xs-10">
										<select class="form-control" name="txtEstatus" id="txtEstatus">
											<option value="">Ingrese el Estatus del Registro...</option>
											<option value="ACTIVO" selected>ACTIVO</option>
											<option value="INACTIVO">INACTIVO</option>
										</select>
									</div>
								</div>

								<div class="clearfix"></div>								
							</form>
					<div class="clearfix"></div>
				</div>
				
				<div class="modal-footer">			
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


