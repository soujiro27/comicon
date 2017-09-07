<!DOCTYPE html>
<html lang="es"><head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=Edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1">	
	<script type='text/javascript' src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js?ver=3.1.2"></script> 
	<script type="text/javascript" src="js/genericas.js"></script>
	<link rel="stylesheet" type="text/css" href="css/estilo.css">
	<style type="text/css">		
		@media screen and (min-width: 768px) {
			#modalEtapaRolAreas .modal-dialog  {width:65%;}
		}
	</style>
	
  
  <script type="text/javascript"> 
  
	
	function limpiarCampos(){
		document.all.txtArea.selectedIndex=0;
		document.all.txtNombre.value='';
		document.all.txtEstatus.selectedIndex=1;
		document.all.txtModulosDestino.options.length = 0;

	}

	function validarCaptura(){
		if (document.all.txtArea.selectedIndex==0){alert("Debe seleccionar un Área."); document.all.txtArea.focus();return false;}
		if (document.all.txtEstatus.selectedIndex==0){alert("Debe seleccionar una Estatus.");document.all.txtEtapa.focus();return false;}
		return true;
	}

	function Recupetipoauditoria(sUrl){
		recuperarListaLarga('lstTiposAuditoria', document.all.txtModulosOrigen);
		document.getElementById('tipestatus').display='inline';
	
		$.ajax({
			type: 'GET', url: sUrl ,
			success: function(response) {			
				//alert("response: "+response);
				var obj = JSON.parse(response);

				document.all.txtArea.value=obj.area;
				document.all.txtNombre.value=obj.nomare;
				
				document.all.txtEstatus.value=obj.estatus;
				document.all.txtNombre.readOnly=true;
				document.getElementById('idArea').disabled=true;
				document.getElementById('tipestatus').display='inline';
				// Recuperará los Tipos de auditorias que tiene asignados

            	var cadena = '/lstAreabyTipo/' + obj.area;
            	document.all.txtModulosDestino.options.length = 0;

	            $.ajax({
		           type: 'GET', url: cadena ,
		           success: function(response) {
	                   var jsonData = JSON.parse(response);                                 
	                                  
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

	
				$('#modalEtapaRolAreas').removeClass("invisible");
				$('#modalEtapaRolAreas').modal('toggle');
				$('#modalEtapaRolAreas').modal('show');
			},
			error: function(xhr, textStatus, error){
				alert('statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
			}			
		});			
	}

	function moverTipo(cmb1, indice, cmb2){
		
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
	

	function ordenarLista(cmb) {
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
			
		if (moverTipo(c1, c1.selectedIndex, c2)==false){
			alert("No se pudo asignar.");
		}else{
			ordenarLista(document.all.txtModulosOrigen);
			ordenarLista(document.all.txtModulosDestino);
			//alert("Rol Asignado!");
		}
	}

	function moverLeft(){
		var area = document.getElementById('idArea').value;
		var modul = document.getElementById('ModulosDestino').value;
		var url = '/comprobar/' + area + '/' +modul;
		
	    $.ajax({
           type: 'GET', url: url ,
           success: function(response) {
               var obj = JSON.parse(response);
               if(obj.opcion == 'SI' ){
               	alert("Este tipo de auditoria ya se encuentra asiganado a una auditoria.");
               }else{
				var c1 = document.all.txtModulosDestino;
				var c2 = document.all.txtModulosOrigen;			
				if (moverTipo(c1, c1.selectedIndex, c2)==false){
					alert("No se pudo asignar.");
				}else{
					ordenarLista(document.all.txtModulosOrigen);
					ordenarLista(document.all.txtModulosDestino);
				}
               }
           		
	        },
	          error: function(xhr, textStatus, error){
	               alert('func: obtenerDatos ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
	        }                                             
		}); 
	}

	function nombretipo(nombre){

		var url = "/comprobararea" + '/' + nombre;
	    $.ajax({
           type: 'GET', url: url ,
           success: function(response) {
               var obj = JSON.parse(response);

               if(obj.valor == 'SI'){
               		alert("ATENCIóN: EL área que selecionasta ya se encuentra registrada");
               		document.all.txtArea.value="";
               		document.all.txtArea.focus();
               		document.all.txtNombre.value="";
               }else{
					var url = "/lstAreas" + '/' + nombre;
				    $.ajax({
			           type: 'GET', 
			           url: url ,
			           success: function(response) {
			               var obj = JSON.parse(response);
			               document.all.txtNombre.value=obj.nombre;
			               
           		
				        },
				          error: function(xhr, textStatus, error){
				               alert('func: obtenerDatos ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
				        }                                             
					}); 
               }
               
           		
	        },
	          error: function(xhr, textStatus, error){
	               alert('func: obtenerDatos ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
	        }                                             
		});




	}


	var nUsr='<?php echo $_SESSION["idUsuario"];?>';		
	var nCampana='<?php echo $_SESSION["idCuentaActual"];?>';	
	
	$(document).ready(function(){
		//$.ajaxSetup({cache: false});
		recuperarLista('lstAreas', document.all.txtArea);
		recuperarLista('recupeProce', document.all.txtProceso);




		setInterval(getMensaje('txtNoti'),60000);


		if(nUsr!="" && nCampana!=""){
			cargarMenu( nCampana);			
		}else{
			if(nCampana=="")alert("Debe establecer una CAMPAÑA como PREDETERMINADA. Por favor consulte con su administrador del sistema.");
		}					
		
		
		
		$("#btnAgregarArea" ).click(function() {
			limpiarCampos();
			document.all.txtNombre.readOnly=true;
			document.getElementById('tipestatus').display='none';
			document.getElementById('idArea').disabled=false;
			document.getElementById('tipestatus').style.display='none';
			recuperarListaLarga('lstTiposAuditoria', document.all.txtModulosOrigen);			
			$('#modalEtapaRolAreas').removeClass("invisible");
			$('#modalEtapaRolAreas').modal('toggle');
			$('#modalEtapaRolAreas').modal('show');
			
		});
		
		$("#btnGuardar" ).click(function() {
			if(validarCaptura()){
				var sModulos = "";
				var sReportes = "";
				var separador;

				document.getElementById('idArea').disabled=false;

				if(document.all.txtModulosDestino.length > 0){
					separador = "";
					for(i=0; i < document.all.txtModulosDestino.length; i++){
						sModulos = sModulos + separador + document.all.txtModulosDestino[i].value + '|' + document.all.txtModulosDestino[i].text ;
						separador = '*';
						}
				}
				document.all.txtModulostipoaudi.value = sModulos;
				document.all.formulario.submit();
			}

		});	




		$("#btnCancelar").click(function() {
			$('#modalEtapaRolAreas').modal('hide');	
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
					<div class="col-xs-3"><h2>Catálogo Areas tipo de auditoria</h2></a></div>									
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
  	<?php require '/templates/menu.php'; ?>
  		
  	  	<!-- Main bar -->
  	<div class="mainbar" id="masroleta">      
		<!-- Page heading -->
    	<!-- Page heading -->
    	<div class="page-head">
        	<h2 class="pull-left"><i class="fa fa-table"></i> Catálogo de Areas por Tipos de Auditoria</h2>

        	<!-- Breadcrumb -->
        	<div class="bread-crumb pull-right">
         		 <a href="/dashboard"><i class="fa fa-home"></i> Home</a> 
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
								<div class="pull-left">Lista de Areas por Tipos de Auditoria</div>
								<div class="widget-icons pull-right"><button id="btnAgregarArea" class="btn btn-primary btn-xs">Agregar area-tipo</button></div>  
							  <div class="clearfix"></div>
							</div>
						
							<div class="widget-content">	
								<div class="table-responsive" style="height: 400px; overflow: auto; overflow-x:hidden;">														
									<div class="">
										<table class="table table-striped table-bordered table-hover">
											<thead>
												<tr>
											 		<th>Area</th>
											  		<th>Nombre</th>							  
											  		<th>Estatus</th>
												</tr>
										 	</thead>
										  	<tbody>
												<?php foreach($datos as $key => $valor): ?>
												  <tr onclick=<?php echo "Recupetipoauditoria('/areatipoaudi/" . $valor['id'] . "');" ; ?> >
								
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
   								  						   
</div>
<!-- Content ends -->

<!--modal-->
	<div id="modalEtapaRolAreas" class="modal fade" role="dialog"> 
		<div class="modal-dialog">

			<!-- Modal content-->
			<div class="modal-content">	
				<!-- Modal header-->
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Datos del ...</h4>
				</div>										
				<!-- Modal body-->
				<div class="modal-body">
					<!-- Inicio del Form-->

					<form id="formulario" METHOD='POST' action='/guardar/artiaudi' role="form">								
						<input type='HIDDEN' name='txtModulostipoaudi' value=''>
						
						<div class="form-group">
							<label class="col-xs-1 control-label" style="text-align:right">Area</label>
							<div class="col-xs-2">
								<select class="form-control" name="txtArea" id="idArea" onchange="nombretipo(this.value);">
									<option value="">Seleccione...</option>
								</select>
							</div>

							<label class="col-xs-1 control-label" style="text-align:right">Nombre</label>
							<div class="col-xs-7">
								<input type="text" class="form-control" name="txtNombre" id="txtNombre" placeholder="Nombre del Área"/>
							</div>
						</div>

						<div class="clearfix"></div>
						
						<div class="form-group tipoestaus" id="tipestatus">
							<label class="col-xs-1 control-label" style="text-align:right;">Estatus</label>
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
						<div class="clearfix"></div>

					</form>

					<div class="form-group tipomodu">
						<label class="col-xs-1 control-label"></label>
						<div class="col-xs-5">
							<strong>Tipos Disponibles</strong><br>
							<select class="form-control" name="txtModulosOrigen" size=10 onDblClick="moverRight();" ></select>
							<div  class="pull-right"><button id="btnAgregarModulo" class="btn btn-warning btn-xs">Agregar >></button>	
							</div>
						</div>
						<div class="col-xs-5">
							<strong>Tipos Asignados</strong><br>
							<select class="form-control" name="txtModulosDestino" size=10 id="ModulosDestino" onDblClick="moverLeft();" ></select>
							<button id="btnQuitarModulo" class="btn btn-warning btn-xs"><< Quitar</button>	
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
	



<!-- Footer starts -->
<footer>
	<?php require '/templates/footer.php'; ?>
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

</body></html>