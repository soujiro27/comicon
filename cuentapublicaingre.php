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
			.filtros{margin: 8px 0px 8px 0px ! important;}
			label {text-align:right;}
			#modalCuentaGeneral {width: 100%;}
		}
	</style>
  
  <script type="text/javascript"> 
	var mapa;
	var nZoom=10;
	var cb = [];

	function limpiarCampos(){

		document.all.txtSujeto.selectedIndex = 0;
		document.all.txtTipoingre.selectedIndex = 0;

	}

	function limpiaringre(){document.all.txtTipoingre.selectedIndex= 0;}

	
	function recuperarCuentaingre(lista,tbl){
				
		$.ajax({
			type: 'GET', url: '/'+ lista ,
			success: function(response) {
				$('#modalCuentaGeneral').modal('show');
				var jsonData = JSON.parse(response);
				
				document.all.tabCuentaEng.style.display='inline';
				document.all.exporta.style.display='inline';
				document.all.exporta_egresos.style.display='none';
				document.all.TabEGRESOSdeta.style.display='none';
	
				//Vacia la lista
				tbl.innerHTML="";

				//Limpia array
				//lstEmpleados = [];
				//Agregar renglones
				var renglon, columna; 	
				
				for (var i = 0; i < jsonData.datos.length; i++) {

					var dato = jsonData.datos[i];					
						if(dato.sbnombre==null){
							var subfuncion = '-2222';
						}else{
							var subfuncion = dato.sbnombre;
						}
						if(dato.funombre==null){var funcion = '-';}else{var funcion = dato.funombre;}

					renglon=document.createElement("TR");			
					renglon.innerHTML="<td>" + dato.idCuenta + "</td><td>" + dato.nombre + "</td><td>" + funcion + "</td><td>" + subfuncion + "</td><td>" + dato.actividad + "</td><td>" + dato.capitulo + "</td><td>" + dato.partida + "</td><td>" + dato.finalidad + "</td><td>" + dato.original + "</td><td>" + dato.modificado + "</td><td>" + dato.ejercido + "</td><td>" + dato.pagado + "</td><td>" + dato.pendiente + "</td>";

					tbl.appendChild(renglon);					
				}				
			},
			error: function(xhr, textStatus, error){
				alert('function recuperarTabla ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
			}			
		});	
	}


	function recuperarTablaIngresos(lista, valor,cuenta,tbl){

		var unidad = valor;
		var sector = unidad.substring(2, 0);
		var subsector = unidad.substring(2, 4);
		var uni = unidad.substring(4,6);
		var vista;

		$.ajax({
			type: 'GET', url: '/'+ lista + '/'+ sector + '/'+ subsector + '/'+ uni + '/'+ cuenta,
			success: function(response) {
				var jsonData = JSON.parse(response);
				
				document.all.exporta.style.display='inline';
				document.all.exporta_egresos.style.display='none';
				document.all.TabEGRESOSdeta.style.display='none';
				//document.all.Tipotipo.style.display='none';		
				//Vacia la lista
				tbl.innerHTML="";

				//Limpia array
				//lstEmpleados = [];
				//Agregar renglones
				var renglon, columna; 	
				
				for (var i = 0; i < jsonData.datos.length; i++) {

					var dato = jsonData.datos[i];					
						if(dato.sbnombre==null){var subfuncion = '-555';}else{var subfuncion = dato.sbnombre;}
						if(dato.funombre==null){var funcion = '-';}else{var funcion = dato.funombre;}

					renglon=document.createElement("TR");			
					renglon.innerHTML="<td>" + dato.idCuenta + "</td><td>" + dato.nombre + "</td><td>" + funcion + "</td><td>" + subfuncion + "</td><td>" + dato.actividad + "</td><td>" + dato.capitulo + "</td><td>" + dato.partida + "</td><td>" + dato.finalidad + "</td><td>" + dato.original + "</td><td>" + dato.modificado + "</td><td>" + dato.ejercido + "</td><td>" + dato.pagado + "</td><td>" + dato.pendiente + "</td>";

					tbl.appendChild(renglon);					
				}				
			},
			error: function(xhr, textStatus, error){
				alert('function recuperarTabla ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
			}			
		});	
	}





	function recuperarTablaIngresosDE(lista, valor,cuenta,tbl){
		var unidad = valor;
		var sector = unidad.substring(2, 0);
		var subsector = unidad.substring(2, 4);
		var uni = unidad.substring(4,6);
		$.ajax({
			type: 'GET', url: '/'+ lista + '/'+ sector + '/'+ subsector + '/'+ uni + '/'+ cuenta,
			success: function(response) {
				var jsonData = JSON.parse(response);
				//document.all.tabINGRESOS.style.display='none';
				document.all.exporta.style.display='none';
				document.all.exporta_egresos.style.display='inline';
				document.all.TabEGRESOSdeta.style.display='inline';
				document.all.Tipotipo.style.display='inline';
				//Vacia la lista
				tbl.innerHTML="";

				//Limpia array
				//lstEmpleados = [];
				//Agregar renglones
				var renglon, columna; 	
				
				for (var i = 0; i < jsonData.datos.length; i++) {
					var dato = jsonData.datos[i];					
						
					renglon=document.createElement("TR");			
					renglon.innerHTML="<td>" + dato.cuenta + "</td><td>" + dato.nombre + "</td><td>" + dato.estimado + "</td><td>" + dato.registrado + "</td><td>" + dato.importe + "</td>";

					tbl.appendChild(renglon);					
				}				
			},
			error: function(xhr, textStatus, error){
				alert('function recuperarTabla ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
			}			
		});	
	}


	function recuperarTablaEgresos(lista,cuenta,tbl){
		$.ajax({
			type: 'GET', url: '/'+ lista + '/'+ cuenta,
			success: function(response) {
				var jsonData = JSON.parse(response);
				document.all.TabEGRESOS.style.display='inline';
				document.all.exporta_egresos.style.display='inline';			
				document.all.exporta.style.display='none';
				document.all.TabEGRESOSdeta.style.display='none';
		
				//Vacia la lista
				tbl.innerHTML="";
				//Limpia array
				//lstEmpleados = [];
				//Agregar renglones
				var renglon, columna;
				
				for (var i = 0; i < jsonData.datos.length; i++) {
					var dato = jsonData.datos[i];	
				

					/*
					var original = '$'+ dato.original;
					var recaudado = '$'+ dato.recaudado;					
					
					renglon=document.createElement("TR");			
					renglon.innerHTML="<td>" + dato.idCuenta + "</td><td>" + dato.origen + "</td><td>" + dato.tipo + "</td><td style='text-align:rigth;'>" + dato.clave + "</td><td>" + dato.nivel + "</td><td>" + dato.nombre + "</td><td>" + original + "</td><td>" + recaudado + "</td>";
					*/

		                renglon       = document.createElement("TR");
						renglon.style = "font-size: small";

		                renglon.innerHTML = "" +
						'<td width="5%" > ' + dato.idCuenta         + "</td>"+
						'<td width="30%"> ' + dato.concepto         + "</td>"+
						'<td width="10%" align="right" > ' + dato.impEstimadoAnt   + "</td>"+
						'<td width="10%" align="right" > ' + dato.impRegistradoAnt + "</td>"+
						'<td width="10%" align="right" > ' + dato.impEstimado      + "</td>"+
						'<td width="10%" align="right" > ' + dato.impRegistrado    + "</td>"+
						'<td width="10%" align="right" > ' + dato.variacion        + "</td>"+
						'<td width="8%"  align="right" > ' + dato.variacionPor     + "</td>"+
						'<td width="8%"  align="right" > ' + dato.variacionReal    + "</td>"

						tbl.appendChild(renglon);					

				}				
			},
			error: function(xhr, textStatus, error){
				alert('function recuperarTabla ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
			}			
		});	
	}





	
	function selectsujetoTipo(valor,cuenta,sujeto){
		var CP = document.getElementById('CuenPu').value;
		if(valor==''){
			//document.all.tabINGRESOS.style.display='none';
			//document.all.Tipo.style.display='inline';
			//document.all.tipoingre.style.display='none';
			document.all.TabEGRESOS.style.display='none';
			document.all.TabEGRESOSdeta.style.display='none';
			document.all.exporta_egresos.style.display='none';
			document.all.exporta.style.display='none';
			//document.all.tabINGRESOS.style.display='none';			
			document.all.sujet.style.display='none';
			document.all.TabEGRESOS.style.display='none';
			
		}else{
			if(valor=='GENERAL'){
				var CP = document.getElementById('CuenPu').value;
				//recuperarTablaEgresos('tblCuentaByEgresos',CP,document.all.tablaCuentaEG);
				recuperarTablaEgresos('tblCuentaByIngreso_HVS',CP,document.all.tablaCuentaEG);
				document.all.TabEGRESOSdeta.style.display='none';
				document.all.sujet.style.display='none';
				
			}else{
				document.all.TabEGRESOS.style.display='none';
				recuperarListaLigada('lstSujetoingre', CP, document.all.txtSujeto);
				document.all.sujet.style.display='inline';
			}
		}
	}








	/*function selectsujeto(valor,sujeto){
		var CP = document.getElementById('CuenPu').value;
		if(valor=='INGRESOS'){
			$.ajax({
				type: 'GET', url: '/valsujeto/' + sujeto + '/' + CP,
				success: function(response) {
					var obj = JSON.parse(response);
					var res = obj.verdadero;
					if(res=='true'){
						limpiaringre();
						document.all.Tipotipo.style.display='inline';
						document.all.TabEGRESOS.style.display='none';
						document.all.tabINGRESOS.style.display='none';
						document.all.exporta.style.display='none';
					}else{
						var CP = document.getElementById('CuenPu').value;
						recuperarTablaEgresos('tblCuentaByEgresos',CP,document.all.tablaCuentaEG);
						document.getElementById('tipoingre').value = 'GENERAL';
					}
				},
				error: function(xhr, textStatus, error){
					alert('calporcentaje: statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
				}			
			});		
		}else{
			if(valor=='EGRESOS'){
				var id = document.getElementById('Sujet').value;
				
				document.all.sujet.style.display='inline';
				document.all.TabEGRESOS.style.display='none';
				document.all.exporta_egresos.style.display='none';
				recuperar(id,CP);
			}else{
				limpiaringre();
				
				document.all.Tipotipo.style.display='none';
				document.all.TabEGRESOS.style.display='none';
				document.all.exporta_egresos.style.display='none';
				document.all.exporta.style.display='none';
			}
		}
	}*/


	function recuperacuenta(id){
		limpiarCampos();
		//limpiartipo();
		recuperarListaLigada('lstSujetoingre', id, document.all.txtSujeto);

		document.all.txtCP.value = id;
		document.all.listacuenta.style.display='none';
		document.all.Cuentavisual.style.display='inline';
		document.all.btnRegresar.style.display='inline';
		document.all.sujet.style.display='none';
		document.all.exporta_egresos.style.display='none';
		document.all.exporta.style.display='none';

		document.all.TabEGRESOSdeta.style.display='none';

		document.all.TabEGRESOS.style.display='none';

		//document.all.TabEGRESOSdeta.style.display='none';
		document.all.TabEGRESOS.style.display='none';		




	}

	
	var nUsr='<?php echo $_SESSION["idUsuario"];?>';		
	var nCampana='<?php echo $_SESSION["idCuentaActual"];?>';	
	  
	$(document).ready(function(){
		//recuperarLista('lstFinalidadesByCuenta', document.all.txtFinalidad); 

		//alert('El valor de document.all.txtCapitulo es: ' + document.all.txtCapitulo);
		//recuperarLista('capitulo', document.all.txtCapitulo)

		$(".botonExcel").click(function(event) {
			$("#datos_a_enviar").val( $("<table>").append( $("#Exportar_a_Excel").eq(0).clone()).html());
			$("#FormularioExportacion").submit();
		});
		
		$(".botonExcelexport").click(function(event) {
			var btn = document.getElementById('tipoingre').value;
			if(btn=='GENERAL'){
				$("#datos_a_enviar").val( $("<table>").append( $("#Exportar_a_Egresos").eq(0).clone()).html());
				$("#FormularioExportacion").submit();
			}else{
				$("#datos_a_enviar").val( $("<table>").append( $("#Exportar_a_Egresos_Deta").eq(0).clone()).html());
				$("#FormularioExportacion").submit();
			}
		});		
	
		if(nUsr!="" && nCampana!=""){
			cargarMenu( nCampana);			
		}else{
			if(nCampana=="")alert("Debe establecer una CUENTA PÚBLICA como PREDETERMINADA. Por favor consulte con su administrador del sistema.");
		}
		
		$( "#btnConsultar" ).click(function() {
			var cuenta = document.getElementById('cuentaradio').value;
			recuperacuenta(cuenta);
		});

		$( "#btnRegresar" ).click(function() {
			document.all.listacuenta.style.display='inline';
			document.all.Cuentavisual.style.display='none';
			document.all.btnRegresar.style.display='none';
			//document.all.btnMostrarconsul.style.display='none';
			
			document.all.sujet.style.display='none';
			document.all.TabEGRESOS.style.display='none';		
			document.all.exporta_egresos.style.display='none';
			document.all.exporta.style.display='none';	
			//document.all.btnCuentapublica.style.display='none';

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
					<div class="col-xs-3"><h2>Cuentas Públicas Ingresos</h2></div>									
					
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
				  	<div class="btn-toolbar pull-right" role="toolbar">
				 	<!--<button type="button" id="btnCuentapublica" 	style="display: none;" 		class="btn btn-default  btn-xs"><i class="fa fa-undo"></i> Cuenta Publica</button>
				 	<button type="button" id="btnMostrarconsul" style="display: none;" 		class="btn btn-default  btn-xs"><i class="fa fa-undo"></i> Visualizar Consulta </button>-->
				 	<button type="button" id="btnRegresar" 	style="display: none;" 		class="btn btn-default  btn-xs"><i class="fa fa-undo"></i> Regresar</button>
				  	<div class="btn-group" style="display: none;" id="exporta">
					  	<form action="ficheroExcel.php"   method="post" target="_blank" id="FormularioExportacion">
					  		<button type="button" class="btn btn-default  btn-xs botonExcel"><i class="fa fa-file-text-o"></i> Exportar a Excel </button>
					  		<input type="hidden" idname="nombre">
							<input type="hidden" id="datos_a_enviar" name="datos_a_enviar" />
					  	</form>
				  	</div>

				  	<div class="btn-group" style="display: none;" id="exporta_egresos">
					  	<form action="ficheroExcel.php"   method="post" target="_blank" id="FormularioExportacion">
					  		<button type="button" class="btn btn-default  btn-xs botonExcelexport"><i class="fa fa-file-text-o"></i> Exportar a Excel </button>
					  		<input type="hidden" idname="nombre">
							<input type="hidden" id="datos_a_enviar" name="datos_a_enviar" />
					  	</form>
				  	</div>
				  </div>  
				  <div class="clearfix"></div>
				</div>             
				<div class="widget-content" id="listacuenta">						
						<div class="table-responsive" style="height: 400px; overflow: auto; overflow-x:hidden;">
							<table class="table table-striped table-bordered table-hover">
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
						<input id="CuenPu" type="text" name="txtCP" style="display:none;">		
						<div class="table-responsive" style="height: 680px; overflow: auto; overflow-x:hidden;">
							
							<div class="col-xs-12" style="margin: 2% 0 0 0 !important;">
								<div class="form-group">
									<div id="Tipotipo">
										<label class="col-xs-1 control-label" style="text-align: left; margin: 0px 0px 0px -1% ! important; height: 38px ! important; width: 10% ! important;">Tipo de Ingreso</label>
										<div class="col-xs-2">
											<select class="form-control" id="tipoingre" name="txtTipoingre" onchange="selectsujetoTipo(this.value,CuenPu.value,Sujet.value);">
												<option value="" Selected>Seleccione...</option>
												<option value="GENERAL" Selected>GENERAL</option>
												<option value="ESPECIFICO" Selected>ESPECIFICO</option>
											</select>
										</div>
									</div>	
									
									<div id="sujet" style="display: none;">	
										<label class="col-xs-1 control-label">Sujeto</label>
										<div class="col-xs-6">
												<select class="form-control" id="Sujet" name="txtSujeto" onchange="recuperarTablaIngresosDE('tblCuentaByIngresosDE',this.value,CuenPu.value,document.all.tablaCuentaEGDE);">
												<option value="" Selected>Seleccione un sujeto...</option>
											</select>
										</div>
									</div>
								</div>
								</div>										
							</div>

					

								<div id="linea">
								 <hr style="margin: -45% 0 0 -5px !important; height: 124% ! important; color: rgb(204, 204, 204) ! important; border-top: 6px solid ! important; ">
									
								</div>
						

							<div class="form-group" id="TabEGRESOS" style="display: none;">									
								<div class="col-xs-12">
									<table class="table table-striped table-bordered table-hover table-condensed" id="Exportar_a_Egresos">
										<caption style=" margin: 9px 0 0 !important;">INGRESO GENERAL</caption>
										<thead>
											<tr>
										 		<th>Cuenta Publica</th>
										 		<th>Concepto</th>
										 		<th>Importe Estimado (Cuenta Anterior)</th>
										 		<th>Importe Registrado (Cuenta Anterior)</th>
										 		<th>Importe Estmado</th>
										 		<th>Importe Registrado</th>
										 		<th>Variación</th>
										 		<th>Variación (%)</th>
										 		<th>Variación Real</th>
											</tr>
										</thead>
										<tbody id="tablaCuentaEG">					  
																														
										</tbody>
									</table>
								</div>									
							</div>

							<div class="form-group" id="TabEGRESOSdeta" style="display: none;">									
								<div class="col-xs-12">
									<table class="table table-striped table-bordered table-hover table-condensed" id="Exportar_a_Egresos_Deta">
										<caption style=" margin: 9px 0 0 !important;">INGRESO ESPECIFICO</caption>
										<thead>
										<tr style="font-size: xx-small"><th>Cuenta Publica</th><th>Nombre</th><th>Estimado</th><th>Registrado</th><th>Importe</th></tr>
										</thead>
										<tbody id="tablaCuentaEGDE" style="width: 100%; font-size: xx-small">					  
																														
										</tbody>
									</table>
								</div>									
							</div>


						</div>
				</div>

				<div id="modalCuentaGeneral" class="modal fade" role="dialog" style="z-index: 1600">
					<div class="modal-dialog">
						<input type='HIDDEN' name='txtOpera' value=''>									
						<!-- Modal content-->
						<div class="modal-content" style="width: 500% ! important; margin: 0px 0px 0px -69% ! important;">									
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
								<h3 class="modal-title"><i class="fa fa-home"></i> Capturando...</h3>
							</div>									
							<div class="modal-body">
								<div class="form-group" id="tabCuentaEng" style="display: none;">									
									<div class="col-xs-12">
										<table class="table table-striped table-bordered table-hover table-condensed"  id="Exportar_a_Excel">
											<caption style=" margin: 9px 0 0 !important;">Cuenta publica</caption>
											<thead>
											<tr style="font-size: xx-small"><th width="1%">Cuenta Publica</th><th width="1%">Sujeto</th><th width="1%">Función</th><th width="1%">Subfunción</th><th width="1%">Actividad</th><th width="1%">Capitulo</th><th width="1%">Partida</th><th width="1%">Finalidad</th><th width="1%">Original</th><th width="1%">Modificado</th><th width="1%">Ejercido</th><th width="1%">Pagado</th><th width="1%">Pendiente</th></tr>
											</thead>                                                            
											<tbody id="tablaCuentaGen" style="width: 100%; font-size: xx-small">					  
																															
											</tbody>
										</table>
									</div>									
								</div>
							</div>				
							<div class="modal-footer">
								<button  type="button" class="btn btn-primary active" id="btnGuardarCapturado"><i class="fa fa-floppy-o"></i> Guardar</button>
								<button  type="button" class="btn btn-default" id="btnCancelarCapturado"><i class="fa fa-undo"></i> Cerrar</button>	
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