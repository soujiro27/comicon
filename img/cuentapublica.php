<!DOCTYPE html>
<html lang="en"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=Edge" />  
  	<meta charset="utf-8">
	<script type='text/javascript' src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js?ver=3.1.2"></script> 
	<script type="text/javascript" src="js/genericas.js"></script>

  	<style type="text/css">		
		@media screen and (min-width: 768px) {
			#mapa_content {width:100%; height:150px;}
			#canvasJG, #canvasJD, #canvasDIP{height:140px; width:100%;}			
			#modalFlotante .modal-dialog  {width:52%;}
			label {text-align:right;}
			
		}
	</style>
  
  <script type="text/javascript"> 
	var mapa;
	var nZoom=10;
	var cb = [];

	function limpiarCampos(){
		document.all.txtTipo.selectedIndex= 0;
		document.all.txtSujeto.selectedIndex = 0;
	}

	function recuperarTablaIngresos(lista, valor,cuenta,tbl){
		var unidad = valor;
		var sector = unidad.substring(2, 0);
		var subsector = unidad.substring(2, 4);
		var uni = unidad.substring(4,6);
		alert("Buscando cuenta: " + '/'+ lista + '/'+ sector + '/'+ subsector + '/'+ uni + '/' + cuenta);
		$.ajax({
			type: 'GET', url: '/'+ lista + '/'+ sector + '/'+ subsector + '/'+ uni + '/'+ cuenta,
			success: function(response) {
				var jsonData = JSON.parse(response);
				document.all.tabINGRESOS.style.display='inline';			
				//Vacia la lista
				tbl.innerHTML="";

				//Limpia array
				//lstEmpleados = [];
				
				//Agregar renglones
				var renglon, columna;
				
				for (var i = 0; i < jsonData.datos.length; i++) {
					var dato = jsonData.datos[i];					
					
					renglon=document.createElement("TR");			
					renglon.innerHTML="<td>" + dato.idCuenta + "</td><td>" + dato.nombre + "</td><td>" + dato.funombre + "</td><td>" + dato.sbnombre + "</td><td>" + dato.actividad + "</td><td>" + dato.capitulo + "</td><td>" + dato.partida + "</td><td>" + dato.finalidad + "</td><td>" + dato.original + "</td><td>" + dato.modificado + "</td><td>" + dato.ejercido + "</td><td>" + dato.pagado + "</td><td>" + dato.pendiente + "</td>";

					tbl.appendChild(renglon);					
				}				
			},
			error: function(xhr, textStatus, error){
				alert('function recuperarTabla ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
			}			
		});	
	}

	function recuperarTablaEgresos(lista,cuenta,tbl){
		
		alert("Buscando cuenta: " + '/'+ lista + '/' + cuenta);
		$.ajax({
			type: 'GET', url: '/'+ lista + '/'+ cuenta,
			success: function(response) {
				var jsonData = JSON.parse(response);
				document.all.TabEGRESOS.style.display='inline';			
				//Vacia la lista
				tbl.innerHTML="";

				//Limpia array
				//lstEmpleados = [];
				
				//Agregar renglones
				var renglon, columna;
				
				for (var i = 0; i < jsonData.datos.length; i++) {
					var dato = jsonData.datos[i];					
					
					renglon=document.createElement("TR");			
					renglon.innerHTML="<td>" + dato.idCuenta + "</td><td>" + dato.origen + "</td><td>" + dato.tipo + "</td><td>" + dato.clave + "</td><td>" + dato.nivel + "</td><td>" + dato.nombre + "</td><td>" + dato.original + "</td><td>" + dato.recaudado + "</td>";

					tbl.appendChild(renglon);					
				}				
			},
			error: function(xhr, textStatus, error){
				alert('function recuperarTabla ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
			}			
		});	
	}



	function recuperar(id,valor){
		alert("id: " + id + " valor: " + valor);
		recuperarTablaIngresos('tblCuentaByUnidad',id,valor,document.all.tablaCuentaIN)
	}





	function selectsujeto(valor){
		alert(valor);
		var CP = document.getElementById('CuenPu').value;
		alert("Cuenta PUBLICA seleccionada es:  " + CP);
		if(valor=='EGRESOS'){
			document.all.TabEGRESOS.style.display='inline';
			document.all.tabINGRESOS.style.display='none';
			document.all.sujet.style.display='none';
			recuperarTablaEgresos('tblCuentaByEgresos',CP,document.all.tablaCuentaEG)
		}else{
			if(valor=='INGRESOS'){
				document.all.tabINGRESOS.style.display='none';
				document.all.sujet.style.display='inline';
				document.all.TabEGRESOS.style.display='none';
				document.all.btnConsultar.style.display='none';
				recuperarListaLigada('lstSujeto', CP, document.all.txtSujeto);
			}else{
				document.all.tabINGRESOS.style.display='none';
				document.all.sujet.style.display='none';
				document.all.TabEGRESOS.style.display='none';
				document.all.btnConsultar.style.display='none';
			}
		}
	}

	function mensaje(condiciones,valor){

		var n=0,cuales="";
		var value =valor;
		cb = document.getElementsByName(condiciones);
			for (var i = 0; i < cb.length; i++){
			var e = parseInt(i);
				if(cb[i].checked == true){
				cuales += cb[i].value + ' ';
				n++;
				}
			}

	}	





	function recuperacuenta(id){
		limpiarCampos();
		alert(id);
		document.all.txtCP.value = id;
		document.all.listacuenta.style.display='none';
		document.all.Cuentavisual.style.display='inline';
		document.all.btnRegresar.style.display='inline';
		document.all.tabINGRESOS.style.display='none';
		document.all.sujet.style.display='none';
		document.all.TabEGRESOS.style.display='none';
		document.all.btnConsultar.style.display='none';

	}




	
	
	var nUsr='<?php echo $_SESSION["idUsuario"];?>';		
	var nCampana='<?php echo $_SESSION["idCuentaActual"];?>';	
	  
	$(document).ready(function(){

		$(".botonExcel").click(function(event) {
			$("#datos_a_enviar").val( $("<div>").append( $("#Exportar_a_Excel").eq(0).clone()).html());
			$("#FormularioExportacion").submit();
		});
		
	
		if(nUsr!="" && nCampana!=""){

			cargarMenu( nCampana);			
		}else{
			if(nCampana=="")alert("Debe establecer una CUENTA PÚBLICA como PREDETERMINADA. Por favor consulte con su administrador del sistema.");
		}
		
		
		$( "#btnConsultar" ).click(function() {
			var cuenta = document.getElementById('cuentaradio').value;
			alert(cuenta);
			recuperacuenta(cuenta);
		});

		$( "#btnRegresar" ).click(function() {
			document.all.listacuenta.style.display='inline';
			document.all.btnConsultar.style.display='inline';
			document.all.Cuentavisual.style.display='none';
			document.all.btnRegresar.style.display='none';		
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
					<div class="col-xs-3"><a href="/"><img src="img/logo-top.png"></a></div>				
					<div class="col-xs-3">
						<ul class="nav navbar-nav "><li><a href="#"><i class="fa fa-th-list"></i> <?php echo $_SESSION["sCuentaActual"] ?></a></li></ul>
					</div>					
					<div class="col-xs-3"><h2>Cuentas Públicas</h2></div>									
					
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
		<div class="container">

		</div>
	</header><!-- Header ends -->

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
			<a href=""><i class="fa fa-pencil-square-o"></i> Acciones<span class="pull-right"><i class="fa fa-chevron-right"></i></span></a>
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
				  	<div class="pull-left"><h3 class="pull-left"><i class="fa fa-home"></i> Cuenta Publica</h3></div>
				  	<div class="widget-icons pull-right">
				 	<button type="button" id="btnRegresar" 	style="display: none;" 		class="btn btn-default  btn-xs"><i class="fa fa-undo"></i> Regresar</button>
				  	<button type="button" id="btnConsultar"	style="display: none;" class="btn btn-default  btn-xs"><i class="fa fa-search"></i> Consultar</button>
				  	<form action="ficheroExcel.php" method="post" target="_blank" id="FormularioExportacion">
				  		<p>Exportar a Excel  <img src="./img/xls.gif" class="botonExcel" /></p>
						<input type="hidden" id="datos_a_enviar" name="datos_a_enviar" />
				  	</form>
				  </div>  
				  <div class="clearfix"></div>
				</div>             
				<div class="widget-content" id="listacuenta">						
						<div class="table-responsive" style="height: 400px; overflow: auto; overflow-x:hidden;">
							<table class="table table-striped table-bordered table-hover" id="Exportar_a_Excel">
							  <thead>
								<tr ><th>Cuenta Publica</th><th>Nombre</th><th>Año</th><th>Fecha Incio</th><th>Fecha Fin</th><th>Observaciones</th><th>Estatus</th></tr>
							  </thead>
							  <tbody >
							  <?php foreach($datos as $key => $valor): ?>
								<tr style="width: 100%; font-size: inherit">
								  <td onclick=<?php echo "javascript:recuperacuenta('" . $valor['cuenta'] . "');"; ?> width="10%;"><?php echo $valor['cuenta']; ?></td>
								  <td onclick=<?php echo "javascript:recuperacuenta('" . $valor['cuenta'] . "');"; ?> width="20%;"><?php echo $valor['nombre']; ?></td>
								  <td onclick=<?php echo "javascript:recuperacuenta('" . $valor['cuenta'] . "');"; ?> width="5%;"><?php echo $valor['anio']; ?></td>
								  <td onclick=<?php echo "javascript:recuperacuenta('" . $valor['cuenta'] . "');"; ?> width="15.83%;"><?php echo $valor['inicio']; ?></td>
								  <td onclick=<?php echo "javascript:recuperacuenta('" . $valor['cuenta'] . "');"; ?> width="15.83%;"><?php echo $valor['fin']; ?></td>
								  <td onclick=<?php echo "javascript:recuperacuenta('" . $valor['cuenta'] . "');"; ?> width="17%;"><?php echo $valor['obser']; ?></td>
								  <td onclick=<?php echo "javascript:recuperacuenta('" . $valor['cuenta'] . "');"; ?> width="11%;"><?php echo $valor['estatus']; ?></td>
								</tr>
								<?php endforeach; ?>
			                  </tbody>
							</table>
						</div>
				</div>

				<div class="row" id="Cuentavisual" style="display:none; padding:0px; margin:0px;">
						<input id="CuenPu" type="text" name="txtCP" style="display:none;">		<div class="table-responsive" style="height: 400px; overflow: auto; overflow-x:hidden;">
							
							<div class="col-xs-12" style="margin: 3% 0 0 5% !important;">
								<div class="form-group">

									<label class="col-xs-1 control-label">Tipo</label>
									<div class="col-xs-3">
										<select class="form-control" id="tipo" name="txtTipo" onchange="selectsujeto(this.value);">
											<option value="" Selected>Seleccione...</option>
											<option value="INGRESOS" Selected>INGRESOS</option>
											<option value="EGRESOS" Selected>EGRESOS</option>
										</select>
									</div>
								
									<div id="sujet" style="display: none;">	
										<label class="col-xs-1 control-label">Sujeto</label>
										<div class="col-xs-5">
											<select class="form-control" id="Sujet" name="txtSujeto" onchange="recuperar(this.value,CuenPu.value)">
												<option value="" Selected>Seleccione...</option>
											</select>
										</div>
									</div>
								</div>										
							</div>

							<div class="form-group" id="tabINGRESOS" style="display: none;">									
								<div class="col-xs-12">
									<table class="table table-striped table-bordered table-hover table-condensed">
										<caption style=" margin: 9px 0 0 !important;">INGRESOS</caption>
										<thead>
										<tr style="font-size: xx-small"><th>Cuenta Publica</th><th>Sujeto</th><th>Función</th><th>Subfunción</th><th>Actividad</th><th>Capitulo</th><th>Partida</th><th>Finalidad</th><th>Original</th><th>Modificado</th><th>Ejercido</th><th>Pagado</th><th>Pendiente</th></tr>
										</thead>
										<tbody id="tablaCuentaIN" style="width: 100%; font-size: xx-small">					  
																														
										</tbody>
									</table>
								</div>									
							</div>

							<div class="form-group" id="TabEGRESOS" style="display: none;">									
								<div class="col-xs-12">
									<table class="table table-striped table-bordered table-hover table-condensed">
										<caption style=" margin: 9px 0 0 !important;">EGRESOS</caption>
										<thead>
										<tr style="font-size: xx-small"><th>Cuenta Publica</th><th>Origen</th><th>Tipo</th><th>Clave</th><th>Nivel</th><th>Rubro</th><th>Original</th><th>Recaudado</th></tr>
										</thead>
										<tbody id="tablaCuentaEG" style="width: 100%; font-size: xx-small">					  
																														
										</tbody>
									</table>
								</div>									
							</div>
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
		<form id="formulario" METHOD='POST' action='/guardar/notifica' role="form">
			<!-- <input type='HIDDEN' name='txtoperacion' value=''>
			<input type='HIDDEN' name='txtValores' value=''>						 -->
<!-- Modal content-->
		<div class="modal-content">									
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Mensaje...</h4>
			</div>									
			<div class="modal-body">
			<div class="container-fluid">
				<div class="row"   style="margin: -22px 0 0 !important;">
					<br>
						<div class="form-group" style="margin: -5px 0 -22px 0!important;">
							<label class="col-xs-2 control-label">ID Notificación</label>
							<div class="col-xs-1">
							<input type="text" class="form-control" name="txtID" id="txtID" readonly />
							</div>
							
							<label class="col-xs-2 control-label">ID Mensaje</label>
							<div class="col-xs-1">
							<input type="text" class="form-control" name="txtIdMensaje" id="txtIdMensaje" readonly style="width: 62px ! important;"/>
							</div>
							
							<label class="col-xs-3 control-label" style="width: 25% !important;">Fecha Mensaje Abierto</label>
							<div class="col-xs-3">
							<input type="date" class="form-control" name="txtfeAbMensaje" readonly/>
							</div>
						</div>
					<br>
						<div class="form-group">													
							
						</div>									
					<br>
						<div class="form-group">													
							<label class="col-xs-2 control-label">Fecha Mensaje</label>
							<div class="col-xs-3">
							<input type="text" class="form-control" id="txtfeMensaje" name="txtfeMensaje" readonly/>
							</div>
							
							<label class="col-xs-2 control-label" style="width: 9.667% !important;">Prioridad</label>
							<div class="col-xs-2">
								<select class="form-control" name="txtprioridad" readonly>
									<option value="" selected>Seleccione...</option>
									<option value="ALTA">ALTA</option>
									<option value="MEDIA">MEDIA</option>
									<option value="BAJA">BAJA</option>
								</select>	
							</div>
							
							<label class="col-xs-2 control-label" style="width: 9% !important;">Impacto</label>
							<div class="col-xs-2">
								<select class="form-control" name="txtImpacto" readonly>
									<option value="" selected>Seleccione...</option>
									<option value="ALTO">ALTO</option>
									<option value="MEDIO">MEDIO</option>
									<option value="BAJO">BAJO</option>
								</select>
							</div>
						</div>
					<br>
						<div class="form-group">									
							<label class="col-xs-1 control-label">Mensaje</label>
							<div class="col-xs-11">
							<textarea class="form-control" rows="4" placeholder="Mensaje" id="txtDescripcion" name="txtDescripcion" style="resize:none; margin: 0 0 13px 0 !important;" readonly></textarea>
							</div>
						</div>	
					<br>
						<div class="form-group" style="display: none;">													
							<label class="col-xs-3 control-label">ELIMINAR NOTIFICACIÓN</label>
							<div class="col-xs-3">
								<select class="form-control" name="txtEstatusMensaje">
									<option value="">Seleccione...</option>
									<option value="ACTIVO" selected>NO</option>
									<option value="INACTIVO" onclick="validarnoti();">ELIMINAR</option>
								</select>
							</div>
						</div>
					<br>
				</div>

			</div>
			<div class="modal-footer">
				<div class="pull-left">
					<button  type="button" class="btn btn-primary  btn-xs" id="btnEliNoti" style="display:inline; background-color: #e02828;" onclick="validarnoti();">Eliminar Notificación</button>
				</div>				
				<div class="pull-right">
					<button  type="button" class="btn btn-primary  btn-xs" id="btnGuardar" style="display:inline;">Aceptar</button>
					<button  type="button" class="btn btn-default btn-xs" id="btnCancelar" style="display:none;">Cancelar</button>	
				</div>
				</div>

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