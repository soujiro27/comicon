<!DOCTYPE html>
<html lang="en"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">  
  <meta charset="utf-8">
		<script type='text/javascript' src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js?ver=3.1.2"></script> 


	<script type="text/javascript" src="js/genericas.js"></script>

  	<style type="text/css">		
		@media screen and (min-width: 768px) {
			#mapa_content {width:100%; height:150px;}
			#canvasJG, #canvasJD, #canvasDIP{height:140px; width:100%;}			
			#modalFlotante .modal-dialog  {width:70%;}
			label {text-align:right;}
		}
	</style>
  
  <script type="text/javascript"> 
	var mapa;
	var nZoom=10;
	var lstEmpleados = new Array();
	
	
	function agregarDocumento(){
		$('#modalFlotante').removeClass("invisible");
		$('#modalFlotante').modal('toggle');
		$('#modalFlotante').modal('show');
	}
		
	
	var nUsr='<?php echo $_SESSION["idUsuario"];?>';
	var nCampana='<?php echo $_SESSION["idCuentaActual"];?>';
	  
	$(document).ready(function(){

		getMensaje('txtNoti', 1);
		
		if(nUsr!="" && nCampana!=""){
			//recuperarListaSelected('lstCuentasByUsr', nUsr, document.all.txtCampana,nCampana);
			cargarMenu( nCampana);			
		}else{
			if(nCampana=="")alert("Debe establecer una CUENTA PÚBLICA como PREDETERMINADA. Por favor consulte con su administrador del sistema.");
		}

		recuperarLista('xxx', document.all.txtPuesto);
		//recuperarLista('estatus', document.all.txtEstatus);



	


		$( "#btnNuevoAuditor" ).click(function() {
			limpiarCamposAudi();
			document.getElementById("txtEmpleado").disabled = false;
			document.getElementById("txtNombre").disabled = false;
			document.getElementById("txtPaterno").disabled = false;
			document.getElementById("txtMaterno").disabled = false;
			document.getElementById("txtPuesto").disabled = false;
			document.all.txtOperacionAuditor.value='INS';
			agregarDocumento();
		});		
			
		
		$( "#btnGuardar" ).click(function() {
			//document.all.txtEmpleado.value='' + obj.empleado;
			guardarEquipo();			
		});
	

		$( "#btnCancelar" ).click(function() {
			$('#modalFlotante').modal('hide');
		});
	
	});


function validarDatos(){
		if (document.all.txtEmpleado.value==''){
			alert("Debe capturar el campo RPE.");
			document.all.txtEmpleado.focus();
			return false;
		}

		if (document.all.txtNombre.value==''){
			alert("Debe capturar el campo NOMBRE.");
			document.all.txtNombre.focus();
			return false;
		}

		if (document.all.txtPaterno.value==''){
			alert("Debe capturar el campo PATERNO.");
			document.all.txtPaterno.focus();
			return false;
		}

		if (document.all.txtMaterno.value==''){
			alert("Debe capturar el campo MATERNO.");
			document.all.txtMaterno.focus();
			return false;
		}

		if (document.all.txtPuesto.selectedIndex==0){
			alert("Debe seleccionar el PUESTO.");
			document.all.txtPuesto.focus();
			return false;
		}

		if (document.all.txtEstatus.selectedIndex==0){
			alert("Debe seleccionar la ESTATUS.");
			document.all.txtEstatus.focus();
			return false;
		}		
						

		return true;
	}





	function recuperaAuditores(id){
		//alert("Recuperando elemento: " + id);
		$.ajax({
			type: 'GET', url: '/AuditoresById/' + id ,
			success: function(response) {
				//alert(response);
				var obj = JSON.parse(response);
				limpiarCamposAudi();
				document.all.txtOperacionAuditor.value='INS';
				//document.all.operacion.value='UPD';
				document.getElementById("txtEmpleado").disabled = true;
				document.getElementById("txtNombre").disabled = true;
				document.getElementById("txtPaterno").disabled = true;
				document.getElementById("txtMaterno").disabled = true;
				document.getElementById("txtPuesto").disabled = true;
				document.all.txtAuditoria.value='' + obj.auditoria;
				document.all.txtEmpleado.value='' + obj.empleado;
				document.all.txtNombre.value='' + obj.nombre;
				document.all.txtPaterno.value='' + obj.paterno;
				document.all.txtMaterno.value='' + obj.materno;

				seleccionarElemento(document.all.txtPuesto, obj.puesto);

				seleccionarElemento(document.all.txtEstatus, obj.estatus);



				

				recupeAuditores('lstAsignadasByAuditores', obj.empleado, document.all.tablaAuditores);

						
							
							

				$('#modalFlotante').removeClass("invisible");
				$('#modalFlotante').modal('toggle');
				$('#modalFlotante').modal('show');		

				
		},
			error: function(xhr, textStatus, error){
				alert(' statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error + ' Avance: ' + id);
			}			
		});		
	}

	function limpiarCamposAudi(){
		document.all.txtEmpleado.value='';
		document.all.txtNombre.value='';
		document.all.txtPaterno.value='';
		document.all.txtMaterno.value='';			
		document.all.txtPuesto.selectedIndex=0;		
		document.all.txtEstatus.selectedIndex=1;
	}




	function recupeAuditores(lista, valor, tbl){
 				
		    //alert("Buscando Actividades: " + '/'+ lista + '/' + valor);		
		$.ajax({
			type: 'GET', url: '/'+ lista + '/' + valor ,
			success: function(response) {
				var jsonData = JSON.parse(response);
				//Vacia la lista
				tbl.innerHTML="";

				//Limpia array
				lstEmpleados = [];

				 for (var i = 0; i < jsonData.datos.length; i++) {
				    var dato = jsonData.datos[i];
		
				    if(dato.asignado=="SI")	 lstEmpleados.push(new Array(dato.idAuditor,dato.lider));
		   			       
				}

				//alert("asignadoS:" + lstEmpleados.length);



				//Agregar renglones
				var renglon, columna, type;
				
				for (var i = 0; i < jsonData.datos.length; i++) {
					var dato = jsonData.datos[i];	

									

					renglon=document.createElement("TR");					
								

					sTatus= "";
					if (dato.asignado=="SI")
						sTatus= "checked=true";
					
					

					

					
					renglon.innerHTML="<td><input type='checkbox' name='' "+sTatus+" onclick='asignarAuditor("+ dato.idEmpleado +", this.checked);'/></td><td>" + dato.claveAuditoria + "</td><td>" + dato.sujeto + "</td><td>" + dato.objeto + "</td><td>" + dato.tipo + "</td>";




					tbl.appendChild(renglon);					
				}
					

				
			},
			error: function(xhr, textStatus, error){
				alert('function recupeAuditores ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
			}			
		});	
	}	
	

	//asignar  checkbox
function asignarAuditor(empleado, activo){
		
		alert("lstEmpleados:" + empleado + " checked= " + activo);
		if (activo==true){
			lstEmpleados.push(new Array(empleado,""));
			alert("los seleccionados son: " + lstEmpleados.length);
		}else{
			for( i = 0; i < lstEmpleados.length; i++)
			{
				alert("los deseleccionados son: " + lstEmpleados.length);
				if(empleado==lstEmpleados[i][0]){
					lstEmpleados.splice(i, 1);
					lstEmpleados[i];
				}

			}
		}
	}


function guardarEquipo(){
				
		var sValor="";
		var sOper=document.all.txtOperacionAuditor.value;
		var d = document.all;
		

		var separador="";
		var separador2="";
		//alert("regitro:" +lstEmpleados.length);
		for(i=0; i < lstEmpleados.length; i++){
			
			sValor = sValor + separador2 + d.txtCuenta.value + '|' + d.txtPrograma.value + '|' + d.txtAuditoria.value + '|' + lstEmpleados[i][0];
			separador2='*';
			alert("indice=" + lstEmpleados[i][0]);
		}
		alert("esto es oper:" + sOper);
		alert(sValor);
		
		$.ajax({
			type: 'GET', url: '/guardar/auditorias/auditor/' + sOper + '/' + sValor,
			success: function(response) {
				alert(response);
				//var obj = JSON.parse(response);
				//recuperarTabla('tblActividadesByAuditoria', d.txtAuditoria.value, document.all.tablaActividades);					
					
					alert("Los datos se guardaron correctamente.");

					
	
					return true;
				
			},
			error: function(xhr, textStatus, error){
				alert(' Error en function guardarEquipo()\n\tstatusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error: ' + error );
				return false;
			}			
		});
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
					<div class="col-xs-3"><h2>Auditores</h2></a></div>									
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
			<a href=""><i class="fa fa-pencil-square-o"></i> Prom. Acciones<span class="pull-right"><i class="fa fa-chevron-right"></i></span></a>
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
					  <div class="pull-left"><h3 class="pull-left"><i class="fa fa-home"></i> Catálogo de Auditores</h3></div>
					  <div class="widget-icons pull-right">
						<button type="button" class="btn btn-primary  btn-xs" id="btnNuevoAuditor"><i class="fa fa-user"></i> Nuevo Auditor...</button>
					  </div>  
					  <div class="clearfix"></div>
					</div>             
					<div class="widget-content">						
							<div class="table-responsive" style="height: 500px; overflow: auto; overflow-x:hidden;">
								<table class="table table-striped table-bordered table-hover">
								  <thead>
									<tr><th>RPE</th><th>Nombre(s)</th><th>Área</th><th>Puesto</th><th>Estatus</th></tr>
								  </thead>
								  <tbody >
								 	<?php foreach($datos as $key => $valor): ?>
											<tr  onclick=<?php echo"recuperaAuditores('" . $valor['id'] . "');"; ?> style="width: 100%; font-size: xx-small">										  										  
											  <td><?php echo $valor['id']; ?></td>
											  <td><?php echo $valor['nombre']; ?></td>
											  <td><?php echo $valor['area']; ?></td>
											  <td><?php echo $valor['puesto']; ?></td>
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
							<form id="formulario" METHOD='POST' role="form">
							<input type='HIDDEN' name='txtOperacionAuditor' value=''>
							<input type='HIDDEN' name='txtCuenta' value='<?php echo $_SESSION["idCuentaActual"];?>'>						
							<input type='HIDDEN' name='txtPrograma' value='<?php echo $_SESSION["idProgramaActual"];?>'>
							<input type='HIDDEN' name='txtAuditoria' value=''>
							
							<!-- Modal content-->
							<div class="modal-content">									
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h3 class="modal-title"><i class="fa fa-user"></i> Datos del Auditor</h3>
								</div>									
								<div class="modal-body">
									<div class="container-fluid">
										<div class="row col-md-12">
												
											<ul class="nav nav-tabs">																												
												<li class="active"><a href="#tab-personales" data-toggle="tab">Datos Personales<i class="fa"></i></a></li>
												<li><a href="#tab-laboral" data-toggle="tab">Auditorías Asignadas<i class="fa"></i></a></li>
												<!-- <li><a href="#tab-academico" data-toggle="tab">Perfil Académico<i class="fa"></i></a></li> -->
											</ul>									
											<div class="tab-content">
												<div class="tab-pane" id="tab-laboral">
													<table class="table table-striped table-bordered table-hover table-condensed">
														<caption>Lista de Auditorías</caption>
														<thead>
														<tr><th>Asignar</th><th>Clave</th><th>Sujeto</th><th>Objeto</th><th>Tipo</th></tr>
														</thead>

														<tbody id="tablaAuditores" style="width: 100%; font-size: xx-small;">
														</tbody>

													</table>																								
												</div>
													


											<!--<div class="tab-pane" id="tab-academico">													
														<table class="table table-striped table-bordered table-hover table-responsive">
														  <thead>
															<tr><th>No.</th><th>Tipo</th><th>Descripción</th><th>Fecha</th><th>Comprobante</th><th>Anexo(s)</th></tr>
														  </thead>
														  <tbody>									  
															<tr style="width: 100%; font-size: xx-small">
																<td>01</td><td>LICENCIATURA</td><td>LICENCIADO EN CONTADURÍA</td><td>15/09/1998 - 31/07/2001</td><td>TÍTULO</td><td><img src="img/xls.gif"></td>
															</tr>
															<tr style="width: 100%; font-size: xx-small">
																<td>02</td><td>DIPLOMADO</td><td>AUDITORÍAS FINANCIERAS</td><td>10/03/2005 - 31/05/2005</td><td>CONSTANCIA</td><td><img src="img/pdf.gif"></td>
															</tr>
															<tr style="width: 100%; font-size: xx-small">
																<td>03</td><td>CURSO</td><td>DATOS PERSONALES (CURSO EN LÍNEA)</td><td>10/03/2014 - 10/03/2014</td><td>CONSTANCIA</td><td><img src="img/pdf.gif"></td>
															</tr>
														  </tbody>
														</table>											
														<button  type="button" class="btn btn-warning  btn-xs"><i class="fa fa-link"></i> Agregar dato académico</button>																							
												</div> -->

												<div class="tab-pane active" id="tab-personales">
													<br>
													<div class="form-group">
														<label class="col-xs-2 control-label">RPE</label>
														<div class="col-xs-2">
															<input type="text" class="form-control" id="txtEmpleado" name="txtEmpleado" />
														</div>

														<!-- <label class="col-xs-2 control-label">Tipo</label>
														<div class="col-xs-6">
															<select class="form-control" id="txtNivel" name="txtNivel">
																<option value="">Seleccione...</option>
																
																
															</select>
														</div>
 -->													</div>	

													<br>
													<div class="form-group">
														<label class="col-xs-2 control-label">Nombre(s)</label>
														<div class="col-xs-4">
															<input type="text" class="form-control" id="txtNombre" name="txtNombre"  placeholder="Nombre"/>
														</div>
														<div class="col-xs-3">
															<input type="text" class="form-control" id="txtPaterno" name="txtPaterno"  placeholder="Paterno"/>
														</div>
														<div class="col-xs-3">
															<input type="text" class="form-control" id="txtMaterno" name="txtMaterno" placeholder="Materno"/>
														</div>												
													</div>

													<br>
													<div class="form-group">
														<label class="col-xs-2 control-label">Puesto</label>
														<div class="col-xs-7">
															<select class="form-control" id="txtPuesto" name="txtPuesto">
																<option value="">Seleccione...</option>

															</select>
														</div>
														<label class="col-xs-1 control-label">Estatus</label>
														<div class="col-xs-2">
															<select class="form-control" name="txtEstatus">
																<option value="">Seleccione...</option>
																<option value="ACTIVO" selected>ACTIVO</option>
																<option value="INACTIVO">INACTIVO</option>
															</select>
														</div>
													</div>	
												</div>
											</div>												
										</div>
									</div>
									<div class="clearfix"></div>
								</div>
								<div class="modal-footer">
									<button  type="button" class="btn btn-primary active" id="btnGuardar" 		style="display:inline;"><i class="fa fa-floppy-o"></i> Guardar</button>	
									<button  type="button" class="btn btn-default " id="btnCancelar" 		style="display:inline;"><i class="fa fa-undo"></i> Salir</button>	
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