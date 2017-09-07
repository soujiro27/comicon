<!DOCTYPE html>
<html lang="en"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=Edge" />  
  	<meta charset="utf-8">
	<script type='text/javascript' src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js?ver=3.1.2"></script> 
	<script type="text/javascript" src="js/genericas.js"></script>
	<script type="text/javascript" src="js/gene.js"></script>
	<link rel="stylesheet" type="text/css" href="css/estilo.css" />

  	<style type="text/css">		
		@media screen and (min-width: 768px) {
			#mapa_content {width:100%; height:150px;}
			#canvasJG, #canvasJD, #canvasDIP{height:140px; width:100%;}
			#modalAprobacion .modal-dialog  {width:50%;}			
			#modalFlotante .modal-dialog  {width:52%;}
			label {text-align:right;}
			
		}
	</style>
  
  <script type="text/javascript"> 
	var mapa;
	var nZoom=10;
	var cb = [];

	function limpiarCampos(){
		document.all.txtfeMensaje.value="";
		document.all.txtEstatusMensaje.selectedIndex= 1;
		document.all.txtprioridad.selectedIndex = 0;
		document.all.txtImpacto.selectedIndex = 0;
		document.all.txtDescripcion.value="";
		document.all.txtcomen.value="";
		document.all.txtpriorimensa.selectedIndex = 0;

	}
	

	function descomen(form){
		alert(form.txtcheck.checked);
		if (form.txtcheck.checked==true) {
			document.all.Comenta.style.display='inline';
		}else{
			document.all.Comenta.style.display='none';
		}
	}



	function mensaje(condiciones,valor){
		//document.all.btnelminiar.disabled = false;
		document.all.btnelminiar.style.display='inline';

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

			if(n==0){
				document.all.btnelminiar.style.display='none';
				//document.all.btnelminiar.disabled = true;				
			}
	}

	function guardavobo(){
		var sValor="";
		var d = document.all;
		var coment = document.getElementById('comen').value;
		var estado="APROBADO";
		if(coment==''){
			var def = "Visto Bueno Aprobado";
			document.all.txtcomen.value = def;
			var mensaje = document.getElementById('comen').value; 
			alert(mensaje);
		}else{
			var mensaje = document.all.txtcomen.value;
			alert(mensaje);
		}
		
		sValor = d.txtuser.value + '|' + mensaje + '|' + d.txtpriorimensa.value + '|' + d.txtreferencia.value + '|' + d.txtauditoria.value + '|' + d.txtIdMensaje.value + '|' + estado ;		
		
		alert("Valor aguardar:  " + sValor);
		//alert("Operador de actividades:  " + sOper);
		$.ajax({
			type: 'GET', url: '/vistoaprobado/' + sValor,
			success: function(response) {
				alert("Los datos se guardaron correctamente");
				$('#modalAprobacion').modal('hide');
				$('#modalPapel').modal('hide');
				document.all.formulario.submit();
				return true;
			},
			error: function(xhr, textStatus, error){
				alert(' Error en function guardarActividad()\n\tstatusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error: ' + error );
				return false;
			}			
		});
	}


	function rechazarvobo(){
		var sValor="";
		var d = document.all;
		var coment = document.getElementById('comen').value;
		var estado="RECHAZADO";
		if(coment==''){
			var def = "Se rechazo :  "
			document.all.txtcomen.value = def;
			var mensaje = document.getElementById('comen').value;
			var auditoria = document.all.txtauditoria.value; 
			var cedula = document.all.txtreferencia.value;
			var mensacom = mensaje + cedula + " de la auditoria numero: " + auditoria;
			alert(mensacom);
		}else{
			var def = "Se rechazo la cedula con numero:  "
			var auditoria = document.all.txtauditoria.value; 
			var cedula = document.all.txtreferencia.value;
			var mensaje = document.all.txtcomen.value;
			var mensacom = def + cedula + " de la auditoria numero: " + auditoria + " con la observación:  " + mensaje;
			alert(mensacom);
		}
		
		sValor = d.txtuser.value + '|' + mensacom + '|' + d.txtpriorimensa.value + '|' + d.txtreferencia.value + '|' + d.txtauditoria.value + '|' + d.txtIdMensaje.value + '|' + estado ;		
		
		alert("Valor aguardar:  " + sValor);
		//alert("Operador de actividades:  " + sOper);
		$.ajax({
			type: 'GET', url: '/rechazovobo/' + sValor,
			success: function(response) {
				//alert("Los datos se guardaron correctamente");
				$('#modalAprobacion').modal('hide');
				$('#modalPapel').modal('hide');
				//document.all.formulario.submit();
				return true;
			},
			error: function(xhr, textStatus, error){
				alert(' Error en function guardarActividad()\n\tstatusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error: ' + error );
				return false;
			}			
		});
	}


	function eliminarnoti(){
		var sValor="";
		var d = document.all;
		var separador="";
		var separador2="";
		for (var i = 0; i < cb.length; i++){
			var e = parseInt(i);
				if(cb[i].checked == true){
				sValor = sValor + separador2  + cb[i].value;
				}
				separador2='*';
			}
		$.ajax({
			type: 'GET', url: '/guardar/notificaciones/' + sValor,
			success: function(response) {
					alert("Se ELIMINARON las NOTIFICACIONES correctamente.");
					window.location.reload()
					//return true;
			},
			error: function(xhr, textStatus, error){
				alert(' Error en function guardarEquipo()\n\tstatusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error: ' + error );
				return false;
			}			
		});
	}



	function validarnoti(){
	 	if (confirm("¿Esta seguro que desea ELIMINAR este NOTIFICACIÓN?:\n \n")){
	 		document.all.txtEstatusMensaje.value='INACTIVO';
	 		document.all.formulario.submit();
	 	}else{
	 		document.all.txtEstatusMensaje.selectedIndex= 1;
	 	}
	}


	function recuperaNoti(id){
		
		$.ajax({
			type: 'GET', url: '/lstMensaByID/' + id ,
			success: function(response) {
				var obj = JSON.parse(response);
				limpiarCampos();
				//alert(obj.estado)
				if(obj.estado==null  || obj.estado=='APROBADO' || obj.estado=='RECHAZADO'){
					document.all.btvistobueno.style.display='none';
					document.all.btnRechazarvobo.style.display='none';
				}else{
					document.all.btvistobueno.style.display='inline';
					document.all.btnRechazarvobo.style.display='inline';
				}
				
				document.all.txtID.value='' + obj.noti;
				document.all.txtIdMensaje.value='' + obj.mensa;
				document.all.txtfeMensaje.value='' + obj.fecha;
				document.all.txtDescripcion.value='' + obj.mensaje;
				document.all.txtprioridad.value='' + obj.prioridad;
				document.all.txtImpacto.value='' + obj.impacto;
				document.all.txtreferencia.value='' + obj.refe;
				document.all.txtuser.value='' + obj.usuario;
				document.all.txtauditoria.value='' + obj.audi
				document.all.txtNenvio.value=obj.emisor;
				document.all.txtcheck.checked=false;
				
				//var flect = obj.lectura;
				var flect = new Date(obj.lectura);
				var lect = flect.getDate() + '/' + (flect.getMonth()+1) + '/' + flect.getFullYear() + '  ' + flect.getHours() + ':' + flect.getMinutes();

				//alert("lect:" + lect);
				//alert("obj.lectura= " + obj.lectura);

				if(obj.lectura =='1900-01-01 00:00:00.000' || obj.lectura == null){
				//	alert("if");
					var ahora = new Date();
					var fecha =ahora.getDate() + '/' + (ahora.getMonth()+1) + '/' + ahora.getFullYear() + '  ' + ahora.getHours() + ':' + ahora.getMinutes();
				//	alert("fecha: " + fecha);
					document.all.txtfeAbMensaje.value='' + fecha;
				}else{
				//	alert("else");
					//document.all.txtfeAbMensaje.value='' + obj.lectura;
					document.all.txtfeAbMensaje.value='' + lect;
				}

				document.all.txtEstatusMensaje.value='' + obj.estatus;

				$('#modalFlotante').removeClass("invisible");
				$('#modalFlotante').modal('toggle');
				$('#modalFlotante').modal('show');
			},
			error: function(xhr, textStatus, error){
				alert('statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
			}			
		});			
	}

	function boton(){
		$.ajax({
		type: 'GET', url: '/notific',
		success: function(response) {
			var obj = JSON.parse(response);		 
			var not = obj.valor;	

				if(not>0){
					
					document.all.btnelminiar.style.display='inline';
				}
		},
		error: function(xhr, textStatus, error){
		 alert(' Error en   function getMensaje    \n\nstatusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
		}   
	   });
	}


	
	
	var nUsr='<?php echo $_SESSION["idUsuario"];?>';		
	var nCampana='<?php echo $_SESSION["idCuentaActual"];?>';	
	var volante = 'volante' 


	$(document).ready(function(){
		
		if(nUsr!="" && nCampana!=""){
			cargarMenu( nCampana);			
		}else{
			if(nCampana=="")alert("Debe establecer una CUENTA PÚBLICA como PREDETERMINADA. Por favor consulte con su administrador del sistema.");
		}
		
		$( "#btnGuardar" ).click(function() {document.all.formulario.submit();});

		$( "#btnelminiar" ).click(function() {eliminarnoti();});
		

		$( "#btnCancelar" ).click(function() {$('#modalFlotante').modal('hide');});

		$( "#btvistobueno" ).click(function() {
			document.all.btnRechazovobo.style.display='none';
			document.all.btnVobo.style.display='inline';
			$('#modalAprobacion').modal('show');});

		$( "#btnRechazarvobo" ).click(function() {
			document.all.btnRechazovobo.style.display='inline';
			document.all.btnVobo.style.display='none';
			$('#modalAprobacion').modal('show');});

		$( "#btnRechazovobo" ).click(function(){

			var comenta = document.getElementById('comen').value;
			alert("comentario:  " + comenta);
			 document.all.txtAproba.value=document.getElementById('comen').value;

			var pru = document.getElementById('Aproba').value;
	 		alert("prueba:  " + pru);
	 		rechazarvobo();
	 		//envionotificacion(pru);
		});


		$( "#btnVobo" ).click(function(){
			var comenta = document.getElementById('comen').value;
			alert("comentario:  " + comenta);
			 document.all.txtAproba.value=document.getElementById('comen').value;

			var pru = document.getElementById('Aproba').value;
	 		alert("prueba:  " + pru);
	 		guardavobo();
	 		//envionotificacion(pru);
		});
		$( "#btnCancelarVobo" ).click(function(){$('#modalAprobacion').modal('hide');});
	
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
  <!-- Data tables
  <link rel="stylesheet" href="jquery.dataTables.css">  -->
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
					<div class="col-xs-3"><h2>Notificaciones</h2></div>									
					
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
  	<aside>
    <?php require '/templates/menu.php'; ?>
  	</aside>
  	  	<!-- Main bar -->
  	<div class="mainbar">
		<div class="col-md-12">
			<div class="widget">
				<div class="widget-head">
					<div class="pull-left"><h3 class="pull-left"><i class="fa fa-home"></i> Notificaciones</h3></div>
				 	<div class="widget-icons pull-right">
						<button id="btnelminiar" type="button" style="display: none" class="btn btn-warning  btn-xs">Eliminar Notificaciones...</button> 						
				  	</div>  
				  <div class="clearfix"></div>
				</div>             
				<div class="widget-content">						
					<div class="table-responsive" style="height: 400px; overflow: auto; overflow-x:hidden;">
						<table class="table table-bordered cur">
							<thead>
								<tr ><th></th><th class="sorting">Situación</th><th>Área</th><th>Fecha de mensaje</th><th>Mensaje</th><th>Prioridad</th><th>Impacto</th><th>Mensaje Visto</th></tr>
						  	</thead>
						  	<tbody >
							  	<?php foreach($datos as $key => $valor): ?>
									<tr style="width: 100%; font-size: inherit; <?php echo $valor['le']; ?>">
										<td><input type="checkbox"  name="condiciones"  value=<?php echo $valor['id']; ?> onclick=<?php echo "javascript:mensaje('condiciones',this.value);";?> ></td>
										<td width="11%;" onclick=<?php echo "javascript:recuperaNoti('" . $valor['id'] . "');"; ?>><?php echo $valor['situacion']; ?></td>
										<td width="11%;" onclick=<?php echo "javascript:recuperaNoti('" . $valor['id'] . "');"; ?>><?php echo $valor['area']; ?></td>
										<td width="11%;" onclick=<?php echo "javascript:recuperaNoti('" . $valor['id'] . "');"; ?>><?php echo $valor['fecha']; ?></td>
									  	<td width="36.83%;" onclick=<?php echo "javascript:recuperaNoti('" . $valor['id'] . "');"; ?>><?php echo $valor['mensaje']; ?></td>
									  	<td width="15.83%;" onclick=<?php echo "javascript:recuperaNoti('" . $valor['id'] . "');"; ?>><?php echo $valor['import']; ?></td>
									  	<td width="15.83%;" onclick=<?php echo "javascript:recuperaNoti('" . $valor['id'] . "');"; ?>><?php echo $valor['impacto']; ?></td>
									  	<td width="15.83%;" onclick=<?php echo "javascript:recuperaNoti('" . $valor['id'] . "');"; ?>><?php echo $valor['lectura']; ?></td>
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
				<div class="row">
					<br>
						<div class="form-group">
							<div style="display: none;">
							<input type="text" class="form-control" name="txtID" id="txtID" readonly />
							</div>
							<label class="col-xs-2 control-label">ID Mensaje</label>
							<div class="col-xs-2">
							<input type="text" class="form-control" name="txtIdMensaje" id="txtIdMensaje" readonly/>
							</div>

							<label class="col-xs-3 control-label">Nombre de quien envía</label>
							<div class="col-xs-5">
							<input type="text" class="form-control" name="txtNenvio" id="txtNenvio" readonly />
							</div>
						</div>
					<br>
						<div class="form-group">													
							<label class="col-xs-2 control-label">Mensaje Leido</label>
							<div class="col-xs-3">
							<input type="date" class="form-control" name="txtfeAbMensaje" readonly/>
							</div>
																				
							<label class="col-xs-2 control-label">Fecha Mensaje</label>
							<div class="col-xs-3">
							<input type="text" class="form-control" id="txtfeMensaje" name="txtfeMensaje" readonly/>
							</div>
						</div>									
					<br>
						<div class="form-group">
							<label class="col-xs-2 control-label">Prioridad</label>
							<div class="col-xs-2">
								<select class="form-control" name="txtprioridad" readonly>
									<option value="" selected>Seleccione...</option>
									<option value="ALTA">ALTA</option>
									<option value="MEDIA">MEDIA</option>
									<option value="BAJA">BAJA</option>
								</select>	
							</div>
							<div class="col-xs-1"></div>
							
							<label class="col-xs-2 control-label">Impacto</label>
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
							<label class="col-xs-2 control-label">Mensaje</label>
							<div class="col-xs-10">
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
					<button  type="button" class="btn btn-warning  btn-xs" id="btvistobueno" style="display:none;"> Vo.Bo </button>
					<button  type="button" class="btn btn-danger  btn-xs" id="btnRechazarvobo" style="display:none;">Rechazar</button>
					<button  type="button" class="btn btn-primary  btn-xs" id="btnGuardar" style="display:inline;">Aceptar</button>
					<button  type="button" class="btn btn-default btn-xs" id="btnCancelar" style="display:none;">Cancelar</button>	
				</div>
				</div>

			</div>
		</div>
		</form>
	</div>
</div>

<div id="modalAprobacion" class="modal fade" role="dialog" style="display: none;">
		<input type='text' name='txtreferencia' value=''>
		<input type="HIDDEN" id="Aproba" name="txtAproba" value=''>
		<input type='text' name='txtuser' value=''>
		<input type='text' name='txtauditoria' value=''>
	<div class="modal-dialog">
		<div class="modal-content">									
		
			<div class="modal-header">
				<h3 class="modal-title"><i class="fa fa-home"></i> Visto Bueno</h3>
			</div>		
			<div class="modal-body">
				<div class="form-group">
					<label class="col-xs-2 control-label">Prioridaad</label>
					<div class="col-xs-4">
						<select class="form-control" id="priorimensa" name="txtpriorimensa">
							<option value="">Seleccione...</option>
							<option value="ALTA">ALTA</option>
							<option value="MEDIA">MEDIA</option>
							<option value="BAJA">BAJA</option>
						</select>
							
					</div>
					<form name="che">
						<div class="form-group">									
							<label class="col-xs-4"><input type="checkbox" name="txtcheck" value="ON" onclick="descomen(this.form)"> Deseas Agregar un comentario</label>
							<label class="col-xs-11"></label>
						</div>
					</form>
				</div>

				
					<div class="form-group" id="Comenta" style="display: none;">									
					<label class="col-xs-2 control-label">Comentario</label>
					<div class="col-xs-10">
						<textarea class="form-control" rows="5" placeholder="Comentario" id="comen" name="txtcomen" style="resize:none;"></textarea>
						
					</div>	
				</div>							
				<div class="clearfix"></div>
			</div>

			<div class="modal-footer">
				<button  type="button" class="btn btn-primary active" id="btnVobo"><i class="fa fa-floppy-o"></i> Dar Vo.Bo.</button>
				<button  type="button" class="btn btn-primary active" style="display: none" id="btnRechazovobo"><i class="fa fa-floppy-o"></i> Enviar</button>

				<button  type="button" class="btn btn-default" id="btnCancelarVobo"><i class="fa fa-undo"></i> Cancelar</button>	
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