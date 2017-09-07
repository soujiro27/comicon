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
			#mapa_content {width:100%; height:450px;}
			#modalRol .modal-dialog  {width:70%;}
			.delimitado {  
				height: 350px !important;
				overflow: scroll;
			}​			
		}
	</style>
  
  <script type="text/javascript"> 
  
	
	function limpiarCampos(){
		document.all.txtRol.value="";
		document.all.txtNombre.value="";
		document.all.txtEstatus.selectedIndex=1	;
		document.all.txtModulosDestino.options.length = 0;
		document.all.txtReportesDestino.options.length = 0;
		// Limpiar MódulosAsignados
	}
	/*
	function bloquearCampos(){
		document.all.txtRol.readOnly=true;
		document.all.txtNombre.readOnly=true;
	}

	function bloquearControles(){
		//document.all.btnRecuperar.readOnly=true;
		document.all.txtEstatus.disabled=true;
		document.all.txtModulosOrigen.disabled=true;
		document.all.txtModulosDestino.disabled=true;
		document.all.txtReportesOrigen.disabled=true;
		document.all.txtReportesDestino.disabled=true;
		document.all.btnAgregarModulo.disabled=true;
		document.all.btnQuitarModulo.disabled=true;
		document.all.btnAgregarReporte.disabled=true;
		document.all.btnQuitarReporte.disabled=true;
	}

	function desBloquearCampos(){
		document.all.txtRol.readOnly=false;
		document.all.txtNombre.readOnly=false;
	}
	
	function desBloquearControles(){
		document.all.txtEstatus.disabled=false;
		document.all.txtModulosOrigen.disabled=false;
		document.all.txtModulosDestino.disabled=false;
		document.all.txtReportesOrigen.disabled=false;
		document.all.txtReportesDestino.disabled=false;
		document.all.btnAgregarModulo.disabled=false;
		document.all.btnQuitarModulo.disabled=false;
		document.all.btnAgregarReporte.disabled=false;
		document.all.btnQuitarReporte.disabled=false;
	}
	*/
	function validarCaptura(){
		if (document.all.txtRol.value==""){alert("Debe seleccionar un IDENTIFICADOR PARA EL ROL.");return false;}
		if (document.all.txtNombre.value==""){alert("Debe capturar el NOMBRE DEL ROL.");return false;}
		if (document.all.txtEstatus.selectedIndex==0){alert("Debe seleccionar un ESTATUS PARA EL ROL.");return false;}
		return true;
	}
	
	function obtenerDatos(sUrl){
		//alert("Valor de sUrl es: " + sUrl);
		recuperarListaLarga('lstModulos_HVS', document.all.txtModulosOrigen);
		recuperarListaLarga('lstReportes_HVS', document.all.txtReportesOrigen);
		$.ajax({
			type: 'GET', url: sUrl ,
			success: function(response) {			
				//alert("response: "+response);
				var obj = JSON.parse(response);

				document.all.txtOperacion.value='UPD';
				document.all.txtRol.readOnly=true;

				document.all.txtRol.value='' + obj.rol;
				document.all.txtNombre.value='' + obj.nombre;
				seleccionarElemento(document.all.txtEstatus, obj.estatus);	

				// Recuperará los módulos que tiene asignados

            	var cadena = '/lstModulosByRol/' + obj.rol;
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


				// Recuperará los reportes que tiene asignados el rol

            	var cadena = '/lstReportesByRol/' + obj.rol;
            	//alert("Valor de cadena: "+ cadena);

       			document.all.txtReportesDestino.options.length = 0;

	            $.ajax({
		           type: 'GET', url: cadena ,
		           success: function(response) {
	                   var jsonData = JSON.parse(response);                                 
	                                  
                   		//alert("response: " + response);

	                   	if(jsonData.datos.length > 0){

	               			// Agregará los Reportes que tiene asignado el rol a el combo destino
		                   	for (var i = 0; i < jsonData.datos.length; i++) {
								var dato = jsonData.datos[i];  
		                   		//alert("Valor de dato.id: " + dato.id + " - dato.texto: " + dato.texto);
								var option = document.createElement("option");
								option.value = dato.id;
								option.text = dato.texto;
								document.all.txtReportesDestino.add(option, document.all.txtReportesDestino.length);
								// Quitara del combo origen los reportes que tiene asignado el rol.
			                   	for (var j = 0; j < document.all.txtReportesOrigen.options.length; j++) {
			                   		if (document.all.txtReportesOrigen[j].value == dato.id){
			                   			document.all.txtReportesOrigen.remove(j);
			                   		}
								}
			                }
	                   }
			        },
			          error: function(xhr, textStatus, error){
			               alert('func: obtenerDatos ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
			        }                                             
				});      

     			ordenarListaByTexto_HVS(document.all.txtModulosOrigen);
				ordenarListaByTexto_HVS(document.all.txtModulosDestino);
				//ordenaOptions(document.all.txtModulosDestino);

     			ordenarListaByTexto_HVS(document.all.txtReportesOrigen);
				ordenarListaByTexto_HVS(document.all.txtReportesDestino);
				//ordenaOptions(document.all.txtReportesDestino);

				$('#modalRol').removeClass("invisible");
				$('#modalRol').modal('toggle');
				$('#modalRol').modal('show');				
				
	            //habilitarEmpleados(obj.tipo);
			},
			error: function(xhr, textStatus, error){
				alert('statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
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

	function ordenarListaByTexto_HVS(cmb) {
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


//	function comparaOptionTexto(a,b) {
//	/* * regresa > 0 si a>b o 0 si a=b o <0 if a<b */
//	// comparación textual
//	return a.text!=b.text ? a.text
//	// comparación numerica 
//	// return a.text - b.text;
//	}
//
//	function ordenaOptions(cmb) {
//		var items = cmb.options.length;
//		// creata un arreglo copia del combobox: cmb
//		var tmpArray = new Array(items);
//		for ( i=0; i tmpArray[i] = new Option(cmb.options[i].text,cmb.options[i].value);
//		// ordena los Options usando la función =>comparaOptionsTexto<=
//		tmpArray.sort(comparaOptionTexto);
//		// hace copias de los Options ordenados y los regresa al combobox cmb
//		for ( i=0; i cmb.options[i] = new Option(tmpArray[i].text,tmpArray[i].value);
//	}





	function moverRight(){
		var c1 = document.all.txtModulosOrigen;
		var c2 = document.all.txtModulosDestino;
			
		if (moverRol(c1, c1.selectedIndex, c2)==false){
			alert("No se pudo asignar el Módulo.");
		}else{
			ordenarListaByTexto_HVS(document.all.txtModulosOrigen);
			ordenarListaByTexto_HVS(document.all.txtModulosDestino);
			//alert("Rol Asignado!");
		}
	}

	function moverRightRep(){
		var c1 = document.all.txtReportesOrigen;
		var c2 = document.all.txtReportesDestino;
			
		if (moverRol(c1, c1.selectedIndex, c2)==false){
			alert("No se pudo asignar el Reporte.");
		}else{
			ordenarListaByTexto_HVS(document.all.txtReportesOrigen);
			ordenarListaByTexto_HVS(document.all.txtReportesDestino);
			//alert("Rol Asignado!");
		}
	}

	function moverLeft(){
		var c1 = document.all.txtModulosDestino;
		var c2 = document.all.txtModulosOrigen;			
		if (moverRol(c1, c1.selectedIndex, c2)==false){
			alert("No se pudo asignar el Módulo.");
		}else{
			ordenarListaByTexto_HVS(document.all.txtModulosOrigen);
			ordenarListaByTexto_HVS(document.all.txtModulosDestino);
		}
	}

	function moverLeftRep(){
		var c1 = document.all.txtReportesDestino;
		var c2 = document.all.txtReportesOrigen;			
		if (moverRol(c1, c1.selectedIndex, c2)==false){
			alert("No se pudo asignar el Reporte.");
		}else{
			ordenarListaByTexto_HVS(document.all.txtReportesOrigen);
			ordenarListaByTexto_HVS(document.all.txtReportesDestino);
		}
	}

	function ValidaExisteRol(sRol){
		//alert("document.all.txtOperacion: " + document.all.txtOperacion.value);
		if(sRol!=""){

			if(document.all.txtOperacion.value == "INS"){
				sRol = sRol.toUpperCase();

				$.ajax({
					type: 'GET', url: '/validaExisteRol/' + sRol ,
					success: function(response) {
						//alert(response);
						var obj = JSON.parse(response);
						//alert(obj.total);

						if (obj.total > 0 ){
							alert("ATENCION: Ya existe el Rol: " + sRol + "\npor favor verifique.");
							document.all.txtRol.value = "";
							document.all.txtRol.focus();
						}
				},
					error: function(xhr, textStatus, error){
						alert('function ValidaExistenciaRol()   -> statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
					}
				});		
			}
		}

	};


	var nUsr='<?php echo $_SESSION["idUsuario"];?>';		
	var nCampana='<?php echo $_SESSION["idCuentaActual"];?>';	
	
	$(document).ready(function(){
		setInterval(getMensaje('txtNoti'),60000);

		if(nUsr!="" && nCampana!=""){
			cargarMenu( nCampana);			
		}else{
			if(nCampana=="")alert("Debe establecer una CAMPAÑA como PREDETERMINADA. Por favor consulte con su administrador del sistema.");
		}					
		
		$("#btnAgregarModulo" ).click(function() {
			moverRight();
		});	


		$("#btnAgregarReporte" ).click(function() {
			moverRightRep();
		});	

		$("#btnQuitarModulo" ).click(function() {			
			moverLeft();
		});	
		

		$("#btnQuitarReporte" ).click(function() {			
			moverLeftRep();
		});	
		/*
		$("#btnRecuperar" ).click(function() {
			//extraeDatos(document.all.txtRPE.value);
			obtenerDatos('/rolByIdRol_HVS/' + document.all.txtRPE.value)
		});		
		*/
		
		$("#btnAgregarRol" ).click(function() {
			recuperarListaLarga('lstModulos_HVS', document.all.txtModulosOrigen);
			recuperarListaLarga('lstReportes_HVS', document.all.txtReportesOrigen);

			document.all.txtOperacion.value='INS';
			limpiarCampos();			
			//bloquearCampos();
			//document.all.txtTipo.selectedIndex=0;
			document.all.txtEstatus.selectedIndex=1	;
			document.all.txtRol.readOnly=false;
			$('#modalRol').removeClass("invisible");
			$('#modalRol').modal('toggle');
			$('#modalRol').modal('show');
			
		});

		$("#btnGuardar" ).click(function() {
			if(validarCaptura()){
				var sModulos = "";
				var sReportes = "";
				var separador;

				if(document.all.txtModulosDestino.length > 0){
					separador = "";
					for(i=0; i < document.all.txtModulosDestino.length; i++){
						sModulos = sModulos + separador + document.all.txtModulosDestino[i].value + '|' + document.all.txtModulosDestino[i].text ;
						separador = '*';
						}
				}
				document.all.txtModulosRol.value = sModulos;

				if(document.all.txtReportesDestino.length > 0){
					separador = "";
					for(i=0; i < document.all.txtReportesDestino.length; i++){
						sReportes = sReportes + separador + document.all.txtReportesDestino[i].value + '|' + document.all.txtReportesDestino[i].text ;
						separador = '*';
						}
				}
				document.all.txtReportesRol.value = sReportes;

				//alert("txtNombre: " + document.all.txtNombre.value);
				//alert(" txtOperacion: " + document.all.txtOperacion.value);
				document.all.formulario.submit();
			}

		});	

		$("#btnCancelar" ).click(function() {
			document.all.txtOperacion.value='';			
			$('#modalRol').modal('hide');	
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
					<div class="col-xs-3"><h2>Catálogo de Roles</h2></a></div>									
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
        	<h2 class="pull-left"><i class="fa fa-table"></i> Roles del Sistema</h2>

        	<!-- Breadcrumb -->
        	<div class="bread-crumb pull-right">
         		 <a href="index.html"><i class="fa fa-home"></i> Home</a> 
          		<!-- Divider -->
          		<span class="divider">/</span> 
          		<a href="#" class="bread-current">Roles</a>
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
								<div class="pull-left">Lista de Roles del Sistema</div>
								<div class="widget-icons pull-right"></div>  
							  <div class="clearfix"></div>
							</div>
						
							<div class="widget-content">	
								<div class="table-responsive" style="height: 400px; overflow: auto; overflow-x:hidden;">														
									<div class="">
										<table class="table table-striped table-bordered table-hover">
											<thead>
												<tr>
											 		<th>Rol</th>
											  		<th>Nombre</th>							  
											  		<th>Estatus</th>
												</tr>
										 	</thead>
										  	<tbody>
												<?php foreach($datos as $key => $valor): ?>
												<tr onclick=<?php echo "obtenerDatos('/rol_HVS/" . $valor['id'] . "');" ; ?> >
												  <td><?php echo $valor['id']; ?></td>
												  <td><?php echo $valor['nombre']; ?></td>
												  <td><?php echo $valor['estatus']; ?></td>
												</tr>
												<?php endforeach; ?>                                                                   
										  	</tbody>
										</table>
									</div>
								</div>
							</div>
							
							<div class="widget-foot">
								<button id="btnAgregarRol" class="btn btn-primary btn-xs">Agregar Rol</button>
								<div class="clearfix"></div> 
							</div>
						</div>
						<!-- 
						<form id="formulario" METHOD='POST' action='/guardar/Rol_HVS' role="form" onsubmit="return validarEnvio();">	
						-->
							<div id="modalRol" class="modal fade" role="dialog"> 
								<div class="modal-dialog">

									<!-- Modal content-->
									<div class="modal-content">	
										<!-- Modal header-->
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal">&times;</button>
											<h4 class="modal-title">Datos del Rol...</h4>
										</div>										
										<!-- Modal body-->
										<div class="modal-body">
											<!-- Inicio del Form-->

											<form id="formulario" METHOD='POST' action='/guardar/rol_HVS' role="form">								
												<input type='HIDDEN' name='txtOperacion' value=''>
												<input type='HIDDEN' name='txtModulosRol' value=''>
												<input type='HIDDEN' name='txtReportesRol' value=''>

												<div class="form-group">
													<label class="col-xs-1 control-label" style="text-align:right">Rol</label>
													<div class="col-xs-2">
														<input type="text" class="form-control" name="txtRol" OnBlur="ValidaExisteRol(this.value)" placeholder="Id. del Rol."/>
													</div>

													<label class="col-xs-1 control-label" style="text-align:right">Nombre</label>
													<div class="col-xs-4">
														<input type="text" class="form-control" name="txtNombre" id="txtNombre" placeholder="Nombre del Rol"/>
													</div>

													<label class="col-xs-1 control-label" style="text-align:right">Estatus</label>
													<div class="col-xs-2">
														<select class="form-control" name="txtEstatus">
															<option value="">Seleccione...</option>
															<option value="ACTIVO" selected>ACTIVO</option>
															<option value="SUSPENDIDO">SUSPENDIDO</option>
															<option value="INACTIVO">INACTIVO</option>
														</select>
													</div>											

												</div>
												<div class="clearfix"></div>

											</form>

											<div class="form-group">
												<label class="col-xs-1 control-label"></label>
												<div class="col-xs-5">
													<strong>Módulos Disponibles</strong><br>
													<select class="form-control" name="txtModulosOrigen" size=10 onDblClick="moverRight();" ></select>
													<div  class="pull-right"><button id="btnAgregarModulo" class="btn btn-warning btn-xs">Agregar >></button>	
													</div>
												</div>
												<div class="col-xs-5">
													<strong>Módulos Asignados</strong><br>
													<select class="form-control" name="txtModulosDestino" size=10 onDblClick="moverLeft();" ></select>
													<button id="btnQuitarModulo" class="btn btn-warning btn-xs"><< Quitar</button>	
												</div>
												<div class="clearfix"></div>												
											</div>

											<div class="form-group">
												<label class="col-xs-1 control-label"></label>
												<div class="col-xs-5">
													<strong>Reportes Disponibles</strong><br>
													<select class="form-control" name="txtReportesOrigen" size=10 onDblClick="moverRightRep();" ></select>
													<div  class="pull-right"><button id="btnAgregarReporte" class="btn btn-warning btn-xs">Agregar >></button>	
													</div>
												</div>
												<div class="col-xs-5">
													<strong>Reportes Asignados</strong><br>
													<select class="form-control" name="txtReportesDestino" size=10 onDblClick="moverLeftRep();" ></select>
													<button id="btnQuitarReporte" class="btn btn-warning btn-xs"><< Quitar</button>	
												</div>
												<div class="clearfix"></div>												
											</div>

										</div>
										<!--   COMENTARIOS  : AQUI VA EL FOOTER MODAL -->
										<div class="modal-footer">
											<button id="btnGuardar" class="btn btn-primary">Guardar datos</button>	
											<button id="btnCancelar" class="btn btn-default">Cancelar</button>	
										</div>
									</div>
								</div>
							</div>
							<!-- Aqui  va el form si se requiere volverlo a sacar -->
						</div>
					</div>
				</div>
			</div>
		</div>
    </div>

   <!-- Mainbar ends -->
   <div class="clearfix"></div>
   								  						   
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
<span class="totop" style="display: none;"><a href="http://ashobiz.asia/mac52/macadmin/#"><i class="fa fa-chevron-up"></i></a></span> 

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
<script src="./Dashboard - MacAdmin_files/custom.js"></script> <!-- Custom codes -->


<!-- Charts & Graphs -->

<!-- 
<script src="./Dashboard - MacAdmin_files/charts.js"></script> 

<script src="./Dashboard - MacAdmin_files/moment.min.js"></script> 
<script src="./Dashboard - MacAdmin_files/fullcalendar.min.js"></script> 
<script src="./Dashboard - MacAdmin_files/jquery.rateit.min.js"></script> 
<script src="./Dashboard - MacAdmin_files/jquery.prettyPhoto.js"></script>
-->
</body></html>