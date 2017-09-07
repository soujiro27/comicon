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
			#modalNoti .modal-dialog  {width:80%;}
			.delimitado {  
				height: 350px !important;
				overflow: scroll;
			}​			
		}
	</style>
  
  <script type="text/javascript"> 
  


  
	
	function limpiarCampos(){
		document.all.txtIDNotificacion.value="";
		document.all.txtNombreNotifi.value="";
		document.all.txtDescripcion.value="";
		document.all.txtTipoNoti.selectedIndex= 2;
		document.all.txtConsulta.value="";
		document.all.txtEstatus.selectedIndex= 1;
		document.all.txtModulosDestino.options.length = 0;
	}


	function validarCaptura(){
		//if (document.all.txtIDNotificacion.value==""){alert("Debe de capturar un ID ."); document.all.txtIDNotificacion.focus();return false;}
		if (document.all.txtNombreNotifi.value==""){alert("Debe capturar el NOMBRE DE LA NOTIFICACIÓN.");document.all.txtNombreNotifi.focus();return false;}
		if (document.all.txtDescripcion.value==""){alert("Debe de capturar una DESCRIPCIÓN.");document.all.txtDescripcion.focus();return false;}
		if (document.all.txtTipoNoti.value==""){alert("Debe de seleccionar un TIPO.");document.all.txtTipoNoti.focus();return false;}
		if (document.all.txtConsulta.value==""){alert("Debe de capturar una CONSULTA.");document.all.txtConsulta.focus();return false;}
		if (document.all.txtEstatus.value==""){alert("Debe de seleccionar un ESTATUS.");document.all.txtEstatus.focus();return false;}
		//if (document.all.txtModulosDestino.value==""){alert("Debe Asignar las FASE(S) al tipo de papel.");document.all.txtModulosDestino.focus();return false;}

		return true;
	}


	function validarNOTI(){
	 	if (confirm("¿Esta seguro que desea desactivar este NOTIFICACIÓN?:\n \n")){
	 	}else{
	 		document.all.txtEstatus.selectedIndex= 1;
	 	}
	}



	function recuperaNoti(id){

		recuperarListaLarga('lstRoles', document.all.txtModulosOrigen);
		//alert("sUrl:  "+ sUrl);
		$.ajax({
			type: 'GET', url: '/lstNotifiByID/' + id ,
			success: function(response) {
			 var obj = JSON.parse(response);
				//alert("Response: " + response);
				limpiarCampos();
				document.all.txtoperacion.value= "UPD";
				document.all.txtIDNotificacion.value='' + obj.idt;
				document.all.txtNombreNotifi.value='' + obj.nombre;
				document.all.txtDescripcion.value='' + obj.descripcion;
				document.all.txtConsulta.value='' + obj.consulta;

				//alert(obj.idt);
				seleccionarElemento(document.all.txtTipoNoti, obj.tipo);
				//document.all.txtProgramadaPapel.value='' + obj.programada;

				document.all.txtEstatus.value='' + obj.estatus;


            	var cadena = '/lstRolesBynotifica/' + obj.idt;
            	//alert("Valor de cadena: "+ cadena);
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

     			ordenarListaByTexto(document.all.txtModulosOrigen);
				ordenarListaByTexto(document.all.txtModulosDestino);

				$('#modalNoti').removeClass("invisible");
				$('#modalNoti').modal('toggle');
				$('#modalNoti').modal('show');
								

			},
			error: function(xhr, textStatus, error){
				alert('statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
			}			
		});			
	}		
		
	function validar(IDNotificacion){

	$.ajax({
	    type: 'GET', url: '/validnoti/' + IDNotificacion ,
	    success: function(response) {
        	var obj = JSON.parse(response);                              

		    	if(IDNotificacion==obj.noti){
		    		alert("EL ID, se encuentra asignado otra ");
		    		document.all.txtTipoPapel.value="";
		    		document.all.txtTipoPapel.focus()

		    	}
            
	    },
	    error: function(xhr, textStatus, error){
	    alert('func: validar ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
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
			alert("Ya existe la Rol.");
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
			alert("Ya existe la Rol.");
		}else{
			ordenarListaByTexto(document.all.txtModulosOrigen);
			ordenarListaByTexto(document.all.txtModulosDestino);
		}
	}

				
		


	
	var nUsr='<?php echo $_SESSION["idUsuario"];?>';		
	var nCampana='<?php echo $_SESSION["idCuentaActual"];?>';	
	
	$(document).ready(function(){
		getMensaje('txtNoti', 1);
		if(nUsr!="" && nCampana!=""){
			cargarMenu( nCampana);			
		}else{
			if(nCampana=="")alert("Debe establecer una CAMPAÑA como PREDETERMINADA. Por favor consulte con su administrador del sistema.");
		}			
		
		
		
		$("#btnAgregarNoti" ).click(function() {
			getMensaje('txtNoti', 1);
			
			recuperarListaLarga('lstRoles', document.all.txtModulosOrigen);
			 limpiarCampos();	
			 document.all.txtoperacion.value='INS';
			$('#modalNoti').removeClass("invisible");
			$('#modalNoti').modal('toggle');
			$('#modalNoti').modal('show');
		});


		$("#btnAgregarRol" ).click(function() {
			moverRight();
		});	


		$("#btnQuitarRol" ).click(function() {			
			moverLeft();
		});	



		$("#btnGuardar" ).click(function() {
			if(validarCaptura())
			{
				var sRoles = "";
				if(document.all.txtModulosDestino.length > 0){
					var separador = "";
					for(i=0; i < document.all.txtModulosDestino.length; i++){
						sRoles = sRoles + separador + document.all.txtModulosDestino[i].value + '|' + document.all.txtModulosDestino[i].text ;
						separador = '*';
					}
				}
				document.all.txtNotificaciones.value = sRoles;
				//alert("txtNombre: " + document.all.txtNotificaciones.value);
			document.all.formulario.submit();
			}

		});	


		$("#btnCancelar" ).click(function() {
			document.all.txtoperacion.value='';			
			$('#modalNoti').removeClass('invisible');
			$('#modalNoti').modal('toggle');
			$('#modalNoti').modal('hide');	
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
					<div class="col-xs-3"><h2>Catálogo de Notificaciones</h2></div>									
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
        <h2 class="pull-left"><i class="fa fa-table"></i> Catálogo de Notificaciones</h2>

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
							  <div class="pull-left">Lista de Tipos de Notificaciones</div>
							  <div class="widget-icons pull-right">
							  </div>  
							  <div class="clearfix"></div>
							</div>
						
							<div class="widget-content">	
								<div class="table-responsive" style="height: 450px; overflow: auto; overflow-x:hidden;">														
									<div class="">
										<table class="table table-striped table-bordered table-hover">
										  <thead>
											<tr>
											  <th>ID</th>
											  <th>Nombre</th>
											  <th>Tipo</th>
											  <th>Descripción</th>
											  <th>Consulta</th>
											  <th>Estatus</th>
											 </tr>
										  </thead>
										  <tbody>
										  <?php foreach($datos as $key => $valor): ?>
											<tr onclick=<?php echo "javascript:recuperaNoti('" . $valor['id'] . "');"; ?> style="width: 100%; font-size: xx-small;">										  										  
											  <td width="5%;"><?php echo $valor['id']; ?></td>
											  <td width="20%;"><?php echo $valor['nombre']; ?></td>
											  <td width="8%;"><?php echo $valor['tipo']; ?></td>
											  <td width="50%;"><?php echo $valor['descripcion']; ?></td>
											  <td width="30%;"><?php echo $valor['consulta']; ?></td>
											  <td width="30%;"><?php echo $valor['estatus']; ?></td>
											</tr>
											<?php endforeach; ?>
						                                                                    
										  </tbody>
										</table>
									</div>
								</div>
							</div>
							
							<div class="widget-foot">
								<button id="btnAgregarNoti" class="btn btn-primary btn-xs">Agregar Notificacion</button>
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
   								  						   



<div id="modalNoti" class="modal fade" role="dialog"> 
	<div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">	
			<!-- Modal header-->
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Datos de la Notificación..</h4>
			</div>										
			<!-- Modal body-->
			<div class="modal-body">
				<!-- Inicio del Form-->

				<form id="formulario" METHOD='POST' action='/guardar/notificacion' role="form">								
					<input type='HIDDEN' name='txtoperacion' value=''>
					<input type='HIDDEN' name='txtNotificaciones' value=''>

					<div class="clearfix"></div>
					<div class="form-group">
						<label class="col-xs-1 control-label" style="text-align:right">ID</label>
						<div class="col-xs-2">
							<input type="text" class="form-control" id="IDNotificacion" name="txtIDNotificacion" readonly/>
						</div>		
						<label class="col-xs-2 control-label" style="text-align:right">Nombre Notificación</label>
						<div class="col-xs-7">
							<input type="text" class="form-control" name="txtNombreNotifi" placeholder="Nombre"/>
						</div>													
					</div>
					<br>
					<div class="form-group">									
						<label class="col-xs-1 control-label">Descripción</label>
						<div class="col-xs-11">
						<textarea class="form-control" rows="2" placeholder="Descripcion" id="txtDescripcion" name="txtDescripcion" style="resize:none; margin: 0 0 13px 0 !important;"></textarea>
						</div>
					</div>			
					<br>

					<div class="form-group" style="margin-bottom: 11px !important;">
						<label class="col-xs-1 control-label" style="text-align:right;">Tipo</label>
						<div class="col-xs-2">
							<select class="form-control" name="txtTipoNoti">
								<option value="" selected>Seleccione...</option>
								<option value="SISTEMA">SISTEMA</option>
								<option value="DATOS">DATOS</option>
								<option value="ALARMA">ALARMA</option>
								</select>
						</div>

						<label class="col-xs-2 control-label" style="text-align:right">Consulta</label>
						<div class="col-xs-7">
							<textarea class="form-control"  rown="2" name="txtConsulta" style="resize:none;"></textarea> 
						</div>												
					</div>	

					<br>
					<div class="form-cgroup">
					<label class="col-xs-1 control-label" style="text-align:right">Estatus</label>
						<div class="col-xs-2" style="margin: -4px 370px 14px 0 !important;">
							<select class="form-control" name="txtEstatus">
								<option value="">Seleccione...</option>
								<option value="ACTIVO" selected>ACTIVO</option>
								<option value="INACTIVO" onclick="validarNOTI();">INACTIVO</option>
							</select>
						</div>	
					</div>



				</form>
					
				<br>
				<div class="form-group">
					<label class="col-xs-1 control-label"></label>
					<div class="col-xs-6">
						<strong>Roles Disponibles</strong><br>
						<select class="form-control" name="txtModulosOrigen" size=10 onDblClick="moverRight();" ></select>
						<div  class="pull-right"><button id="btnAgregarModulo" class="btn btn-warning btn-xs">Agregar >></button>	
						</div>
					</div>
					<div class="col-xs-6">
						<strong>Roles Asignados</strong><br>
						<select class="form-control" name="txtModulosDestino" size=10 onDblClick="moverLeft();" ></select>
						<button id="btnQuitarModulo" class="btn btn-warning btn-xs"><< Quitar</button>	
					</div>
					<div class="clearfix"></div>												
				</div>

					<div class="clearfix"></div>


			</div>
			<!--   COMENTARIOS  : AQUI VA EL FOOTER MODAL -->
			<div class="modal-footer">
				
				<button id="btnGuardar" class="btn btn-primary btn-xs active">Guardar datos</button>	
				<button id="btnCancelar" class="btn btn-default btn-xs">Cancelar</button>	
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