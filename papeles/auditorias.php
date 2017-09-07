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
			#modalCronograma .modal-dialog  {width:70%;}
			#modaltrabajofase .modal-dialog  {width:70%;}
			#modalEquipo .modal-dialog  {width:70%;}
			#modalDocto .modal-dialog  {width:60%;}
			#modalAutorizar .modal-dialog  {width:30%;}

			caption {padding: .2em .8em;border-bottom: 1px solid #fFF;background:#f4f4f4; font-weight: bold;}		
			label {text-align:right;}
			.auditor{background:#f4f4f4; font-size:6pt; padding:7px; display:inline; margin:1px; border:1px gray solid;}
			
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
	var mapa;
	var nZoom=10;
	var lstEmpleados = new Array();

	var sProcesoActual="";
	var sEtapaActual="";


	var sProcesoNuevo="";
	var sEtapaNueva="";

	
	
	function cajaResultados(tipo){
		if(tipo!="")
			document.all.divResumen.style.display='inline';	
		else 
			document.all.divResumen.style.display='none';	
	}
		
	window.onload = function () {
		var chart1; 
		
			
			setGrafica(chart1, "dpsAuditoriasByArea/:area", "pie", "Auditorias", "canvasJG" );
			
	
	};
	
	function guardarAuditoria(){
		document.all.listasAuditoria.style.display='inline';
		document.all.capturaAuditoria.style.display='none';
	}
	
	function agregarAuditoria(){
		document.all.listasAuditoria.style.display='none';
		document.all.capturaAuditoria.style.display='inline';
	}
	
	
	function editarCronograma(){
	 							
		$('#modalCronograma').removeClass("invisible");
		$('#modalCronograma').modal('toggle');
		$('#modalCronograma').modal('show');
	}
	
	function recuperarTabla(lista, valor, tbl){
		//alert("Buscando Actividades: " + '/'+ lista + '/' + valor);
		$.ajax({
			type: 'GET', url: '/'+ lista + '/' + valor ,
			success: function(response) {
				var jsonData = JSON.parse(response);			
				//Vacia la lista
				tbl.innerHTML="";

				//Limpia array
				//lstEmpleados = [];
				
				//Agregar renglones
				var renglon, columna;
				
				for (var i = 0; i < jsonData.datos.length; i++) {
					var dato = jsonData.datos[i];					
					renglon=document.createElement("TR");			
					renglon.innerHTML="<td>" + dato.fase + "</td><td>" + dato.actividad + "</td><td>" + dato.inicio + "</td><td>" + dato.fin  + "</td><td>" + dato.porcentaje + "</td><td>" + dato.prioridad + "</td>";
					renglon.onclick= function() {
						
					};
					tbl.appendChild(renglon);					
				}				
			},
			error: function(xhr, textStatus, error){
				alert('function recuperarTabla ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
			}			
		});	
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
					
					Slider= "";
					if(dato.lider=="SI")
						Slider= "checked=true";

					

					
					renglon.innerHTML="<td><input type='checkbox' name='' "+sTatus+" onclick='asignarAuditor("+ dato.idEmpleado +", this.checked);'/></td><td>" + dato.auditor + "</td><td>" + dato.plaza + "</td><td><input type='radio' name='selec' "+Slider+" onclick='asignarLider("+ dato.idEmpleado +", this.checked);'/></td>";




					tbl.appendChild(renglon);					
				}
					

				
			},
			error: function(xhr, textStatus, error){
				alert('function recupeAuditores ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
			}			
		});	
	}	



//asignar  radio
	function asignarLider(empleado, activo){
		var bEncontrado=false;

		//alert("lstEmpleados:" + empleado + " checked= " + activo);
		for( i = 0; i < lstEmpleados.length; i++)
			{

				lstEmpleados[i][1]="";
				
			}

		for( i = 0; i < lstEmpleados.length; i++)
			{
				if(empleado==lstEmpleados[i][0]){
					lstEmpleados[i][1]="SI";
					//alert(lstEmpleados[i][0]);
					bEncontrado=true;
				}

			}

			if(bEncontrado==false){ 
				alert("Para ser LIDER, ésta persona de ser incluida al equipo de trabajo");
				activo=false;
				bEncontrado=false;
			}


	}


//asignar  checkbox
function asignarAuditor(empleado, activo){
		
		//alert("lstEmpleados:" + empleado + " checked= " + activo);
		if (activo==true){
			lstEmpleados.push(new Array(empleado,""));
			//alert("los seleccionados son: " + lstEmpleados.length);
		}else{
			for( i = 0; i < lstEmpleados.length; i++)
			{
				if(empleado==lstEmpleados[i][0]){
					lstEmpleados.splice(i, 1);
				//alert("los deseleccionados son: " + lstEmpleados.length);
				}

			}
		}
	}



	function guardarEquipo(){
				
		var sValor="";
		var sOper=document.all.txtOperacionEquipo.value;
		var d = document.all;

		var separador="";
		var separador2="";
		//alert("regitro:" +lstEmpleados.length);
		for(i=0; i < lstEmpleados.length; i++){
			
			sValor = sValor + separador2 + d.txtCuenta.value + '|' + d.txtPrograma.value + '|' + d.txtAuditoria.value + '|' + lstEmpleados[i][0] + '|' + lstEmpleados[i][1];
			separador2='*';
			//alert("indice=" + lstEmpleados[i][0]);
		}
		// alert(sOper);
		// alert(sValor);
		
		$.ajax({
			type: 'GET', url: '/guardar/equipo/lider/' + sOper + '/' + sValor,
			success: function(response) {
				//alert(response);
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



function recupeLider(lista, valor, tbl){
		    //alert("Buscando Actividades: " + '/'+ lista + '/' + valor);		
			$.ajax({
			type: 'GET', url: '/'+ lista + '/' + valor ,
			success: function(response) {
				var jsonData = JSON.parse(response);
				//Vacia la lista
				tbl.innerHTML="";

				//Limpia array
				//lstEmpleados = [];
				
				//Agregar renglones
				var renglon, columna, type;
				
				for (var i = 0; i < jsonData.datos.length; i++) {
					var dato = jsonData.datos[i];	
									
					renglon=document.createElement("TR");					

					

					renglon.innerHTML="<td>" + dato.lider + "</td><td>" + dato.texto + "</td><td>" + dato.plaza + "</td>";
						
					renglon.onclick= function() {
						

						
					};
					tbl.appendChild(renglon);					
				}

				
			},
			error: function(xhr, textStatus, error){
				alert('function recupeLider ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
			}			
		});	
	}	

	function recupeFases(lista, valor, tbl){
		//alert("Buscando Actividades: " + '/'+ lista + '/' + valor);

			$.ajax({
			type: 'GET', url: '/'+ lista + '/' + valor ,
			success: function(response) {
				var jsonData = JSON.parse(response);

				//Vacia la lista
				tbl.innerHTML="";

				//Limpia array
				//lstEmpleados = [];
				
				//Agregar renglones
				var renglon, columna, type;
				
				for (var i = 0; i < jsonData.datos.length; i++) {
					var dato = jsonData.datos[i];	
					

					
					
					renglon=document.createElement("TR");			
					renglon.innerHTML="<td>" + dato.texto + "</td><td>" + dato.desde + "</td><td>" + dato.hasta + "</td><td>" + dato.dia + "</td>";
						
					renglon.onclick= function() {
						document.all.txtOperacion.value='UPD';



					};
					tbl.appendChild(renglon);					
				}				
			},
			error: function(xhr, textStatus, error){
				alert('function recupeAuditores ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
			}			
		});	
	}	


	function recupepapeles(lista, valor, tbl){
		//alert("Buscando Actividades: " + '/'+ lista + '/' + valor);

			$.ajax({
			type: 'GET', url: '/'+ lista + '/' + valor ,
			success: function(response) {
				var jsonData = JSON.parse(response);

				//Vacia la lista
				tbl.innerHTML="";

				//Limpia array
				//lstEmpleados = [];
				
				//Agregar renglones
				var renglon, columna, type;
				
				for (var i = 0; i < jsonData.datos.length; i++) {
					var dato = jsonData.datos[i];	
					

					
					
					renglon=document.createElement("TR");			
					renglon.innerHTML="<td>" + dato.numero + "</td><td>" + dato.tipo + "</td><td>" + dato.resul + "</td><td>" + dato.texto + "</td><td>" + dato.fecha + "</td><td>" + dato.estatus + "</td><td>" + "<img src='img/xls.gif'> " + "</td>";
					
					renglon.onclick= function() {
						document.all.txtOperacion.value='UPD';



					};
					tbl.appendChild(renglon);					
				}				
			},
			error: function(xhr, textStatus, error){
				alert('function recupeAuditores ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
			}			
		});	
	}


	function recuperalistaAuditores(lista, valor, tbl){
		//alert("Buscando Actividades: " + '/'+ lista + '/' + valor);

			$.ajax({
			type: 'GET', url: '/'+ lista ,
			success: function(response) {
				//alert("entre texto audi:" + response);
				var jsonData = JSON.parse(response);

				//Vacia la lista
				tbl.innerHTML="";

				//Limpia array
				//lstEmpleados = [];
				
				//Agregar renglones
				var renglon, columna, type;
				
				for (var i = 0; i < jsonData.datos.length; i++) {
					var dato = jsonData.datos[i];	
					

					
					
					renglon=document.createElement("TR");			
					renglon.innerHTML="<td>" + dato.area + "</td><td>" + dato.finan + "</td><td>" + dato.fincum + "</td><td>" + dato.cumpli + "</td><td>" + dato.obrapub + "</td><td>" + dato.desem + "</td><td>" + dato.total + "</td>";
						
					renglon.onclick= function() {
						document.all.txtOperacion.value='UPD';



					};
					tbl.appendChild(renglon);					
				}				
			},
			error: function(xhr, textStatus, error){
				alert('function recuperalistaAuditores ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
			}			
		});	
	}




function recuperarTablaFase(lista, valor, tbl){
		//alert("Buscando Actividades: " + '/'+ lista + '/' + valor);
		$.ajax({
			type: 'GET', url: '/'+ lista + '/' + valor ,
			success: function(response) {
				var jsonData = JSON.parse(response);			
				//Vacia la lista
				tbl.innerHTML="";

				//Limpia array
				//lstEmpleados = [];
				
				//Agregar renglones
				var renglon, columna;
				
				for (var i = 0; i < jsonData.datos.length; i++) {
					var dato = jsonData.datos[i];					
					renglon=document.createElement("TR");				
					renglon.innerHTML="<td>" + dato.fase + "</td><td>" + dato.actividad + "</td><td>" + dato.inicio + "</td><td>" + dato.fin  + "</td><td>" + dato.porcentaje + "</td><td>" + dato.prioridad + "</td>";
					renglon.onclick= function() {
						alert('En Construcción');
					};
					tbl.appendChild(renglon);					
				}				
			},
			error: function(xhr, textStatus, error){
				alert('function recuperarTabla ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
			}			
		});	
	}	





	function recuperaAuditoria(id){
		
		$.ajax({
			type: 'GET', url: '/lstAuditoriaByID/' + id ,
			success: function(response) {
				//alert(response);
				var obj = JSON.parse(response);
				limpiarCamposAuditoria();
				//document.all.txtOperacion.value='UPD';
				
				document.all.txtCuenta.value='' + obj.cuenta;
				document.all.txtPrograma.value='' + obj.programa;
				document.all.txtAuditoria.value='' + obj.auditoria;
				
				document.all.txtclaveAuditoria.value='' + "Proy-Aud-" + obj.auditoria;
									
				
				seleccionarElemento(document.all.txtArea, obj.area);
				seleccionarElemento(document.all.txtTipoAuditoria, obj.tipo);
				
			

				seleccionarElemento(document.all.txtSector, obj.sector);

				recuperarListaSelected('lstSubsectoresBySector', obj.sector, document.all.txtSubsector, obj.subSector);
				seleccionarElemento(document.all.txtSubsector, obj.subSector);
							
				var cadena = "lstUnidadesBySectorSubsector/" + obj.sector;
				recuperarListaSelected(cadena, obj.subSector, document.all.txtSujeto, obj.unidad);

			


				var cadena = "lstObjetosSeleccionado/" + obj.auditoria;
				recuperarListaSelected(cadena, obj.objeto, document.all.txtObjeto, obj.objeto);
				

				document.all.txtObjetivo.value='' + obj.objetivo;
				document.all.txtAlcance.value='' + obj.alcance;	
				document.all.txtJustificacion.value='' + obj.justificacion;
				document.all.txtPresupuesto.value='' + obj.tipoPresupuesto;

				Sacompa="";
				if (obj.acompanamiento=="on"){
					Sacompa="SI";
					document.all.txtAcompanamiento.value='' + Sacompa;
				}else{
					Sacompa="NO";
					document.all.txtAcompanamiento.value='' + Sacompa;

				}
				
		

				
				
				document.all.txtObjetivo.value='' + obj.objetivo;
				recuperarTabla('tblActividadesByAuditoria', obj.auditoria, document.all.tablaActividades);
				document.all.listasAuditoria.style.display='none';
				document.all.capturaAuditoria.style.display='inline';


				recupeLider('auditoriasByLider', obj.auditoria, document.all.tablaEquipo);

			
				recupeAuditores('auditoriasBYauditores', obj.auditoria, document.all.tablaAuditores);

			
				recuperarLista('lstFasesActividad', document.all.txtFaseActividad);

				
				recuperarListaLigada('lstResponByauditor', obj.auditoria, document.all.txtResponsableActividad);

				recuperarListaLigada('lstActividaByPrevia', obj.auditoria, document.all.txtPreviaActividad);

				recupeFases('lstauditoriaByFases', obj.auditoria, document.all.tablaFases);

				recupepapeles('lstpapeles', obj.auditoria, document.all.tablaPapeles);

				//recupepapeles('lstpapeles', obj.auditoria, document.all.tablaAvances);


				proximaEtapa(obj.proceso, obj.etapa);

		},

			error: function(xhr, textStatus, error){
				alert('function recuperaAuditoria()   -> statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error + ' Auditoría: ' + id);
			}			
		});		
	}	


	
	function limpiarCamposFase(){
					
		document.all.txtFaseActividad.selectedIndex=0;		
		document.all.txtDiasActividad.value='';
		document.all.txtInicioActividad.value='';
		document.all.txtFinActividad.value='';			
		document.all.txtPorcentajeActividad.value='';
		document.all.txtPrioridadActividad.value='';
	}



	function limpiarCamposAuditoria(){
		document.all.txtAuditoria.value='';
		
		document.all.txtTipoAuditoria.selectedIndex=0;
		document.all.txtArea.selectedIndex=0;		
		document.all.txtSujeto.selectedIndex=0;
		document.all.txtObjeto.selectedIndex=0;		
		document.all.txtObjetivo.value='';
		//document.all.txtAlcance.value='';			
		//document.all.txtJustificacion.value='';
	}	
	
 

	var ventana;
	
	function modalWin(sPagina) {
		var sDimensiones; 
		
		if (window.showModalDialog) {f
			sDimensiones= "dialogWidth:" + window.innerWidth + "px;dialogHeight:" + window.innerHeight + "px;";
			window.showModalDialog(sPagina,"Reporte",sDimensiones);
		} 
		else {
			sDimensiones= "width=" + window.innerWidth + ", height=" + window.innerHeight + ",location=no, titlebar=no, menubar=no,minimizable=no, resizable=no,  toolbar=no,directories=no,status=no,continued from previous linemenubar=no,scrollbars=no,resizable=no ,modal=yes";
			ventana = window.open(sPagina,'Reporte', sDimensiones);
			ventana.focus();
		}
	}
	
	
	
  

	function verificarDependencia(tipo){
		if(tipo=="SIMPLE")document.all.divActividadPrevia.style.display='none';0
		if(tipo=="SERIADA")document.all.divActividadPrevia.style.display='inline';
	}
	  
function limpiarCamposActividad(){
		document.all.txtIDActividad.value='';
		document.all.txtDescripcionActividad.value='';
		document.all.txtPreviaActividad.selectedIndex=0;
		document.all.txtTipoActividad.selectedIndex=0;		
		document.all.txtFaseActividad.selectedIndex=0;		
		document.all.txtResponsableActividad.selectedIndex=0;		
		document.all.txtDiasActividad.value='';
		document.all.txtInicioActividad.value='';
		document.all.txtFinActividad.value='';			
		document.all.txtPorcentajeActividad.value='';
		document.all.txtNotasActividad.value='';				
	}
	
	function guardarActividad(){
		//sValor = document.all.txtDelegacionFiltro.options[document.all.txtDelegacionFiltro.selectedIndex].value;		
		var sValor="";
		var sOper=document.all.txtOperacion.value;
		var d = document.all;
		
		sValor = d.txtCuenta.value + '|' + d.txtPrograma.value + '|' + d.txtAuditoria.value + '|' +  d.txtIDActividad.value + '|' + d.txtTipoActividad.value;		
		sValor = sValor + '|' + d.txtFaseActividad.value + '|' + d.txtDescripcionActividad.value + '|' + d.txtPreviaActividad.value ;		
		sValor = sValor + '|' + d.txtInicioActividad.value + '|' + d.txtFinActividad.value + '|' + d.txtPorcentajeActividad.value;
		sValor = sValor + '|' + d.txtPrioridadActividad.value + '|' + d.txtImpactoActividad.value+ '|' + d.txtResponsableActividad.value;
		sValor = sValor + '|' + d.txtEstatusActividad.value + '|' + d.txtNotasActividad.value;

			
		$.ajax({
			type: 'GET', url: '/guardar/auditoria/actividad/' + sOper + '/' + sValor,
			success: function(response) {
				//var obj = JSON.parse(response);
				//alert("hola" + response);
				recuperarTabla('tblActividadesByAuditoria', d.txtAuditoria.value, document.all.tablaActividades);					
					alert("Los datos se guardaron correctamente.");

					document.all.divListaActividades.style.display='inline';
					document.all.divCapturaActividad.style.display='none';
					
					document.all.btnNuevaActividad.style.display='inline';
					document.all.btnCancelarActividad.style.display='none';
				
					
					document.all.btnGuardarActividad.style.display='none';
					document.all.btnCancelarCronograma.style.display='inline';


					
					return true;
				
			},
			error: function(xhr, textStatus, error){
				alert(' Error en function guardarActividad()\n\tstatusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error: ' + error );
				return false;
			}			
		});
	}
	








	function validarActividad(){
		if (document.all.txtTipoActividad.selectedIndex==0){
			alert("Debe seleccionar el TIPO DE ACTIVIDAD.");
			document.all.txtTipoActividad.focus();
			return false;
		}

		if (document.all.txtFaseActividad.selectedIndex==0){
			alert("Debe seleccionar la FASE.");
			document.all.txtFaseActividad.focus();
			return false;
		}		
		
		
		if (document.all.txtDescripcionActividad.value==''){
			alert("Debe capturar el campo ACTIVIDAD.");
			document.all.txtDescripcionActividad.focus();
			return false;
		}
		if (document.all.txtInicioActividad.value==''){
			alert("Debe capturar el campo FECHA DE INICIO.");
			document.all.txtInicioActividad.focus();
			return false;
		}			
		if (document.all.txtFinActividad.value==''){
			alert("Debe capturar el campo FECHA FIN.");
			document.all.txtFinActividad.focus();
			return false;
		}	
		if (document.all.txtPorcentajeActividad.value==''){
			alert("Debe capturar el campo PORCENTAJE.");
			document.all.txtPorcentajeActividad.focus();
			return false;
		}	

		if (document.all.txtPrioridadActividad.selectedIndex==0){
			alert("Debe seleccionar la PRIORIDAD.");
			document.all.txtPrioridadActividad.focus();
			return false;
		}		

		if (document.all.txtImpactoActividad.selectedIndex==0){
			alert("Debe seleccionar el IMPACTO.");
			document.all.txtImpactoActividad.focus();
			return false;
		}

		
		if (document.all.txtResponsableActividad.selectedIndex==0){
			alert("Debe seleccionar el RESPONSABLE.");
			document.all.txtResponsableActividad.focus();
			return false;
		}
		

		if (document.all.txtEstatusActividad.selectedIndex==0){
			alert("Debe seleccionar el ESTATUS.");
			document.all.txtEstatusActividad.focus();
			return false;
		}		

		return true;
	}
	
	
	$(document).ready(function(){		
		document.all.divActividadPrevia.style.display='inline';
		document.all.divActividadPrevia.style.display='none';
		
		recuperarLista('lstAreas', document.all.txtArea);
		recuperarLista('lstTiposAuditorias', document.all.txtTipoAuditoria);
		recuperarLista('lstSectores', document.all.txtSector);
		recuperarLista('lstObjetosByEnables',document.all.txtObjeto);
		recuperalistaAuditores('tblAuditorias',document.all.txtPrograma.value, document.all.tablaAuditorias);
		
					

	
		$('#dp1').datepicker({format: "dd/mm/yyyy"});
		$('#dp2').datepicker({format: "dd/mm/yyyy"});
		$('#dp3').datepicker({format: "dd/mm/yyyy"});
		$('#dp4').datepicker({format: "dd/mm/yyyy"});
		$('#dp5').datepicker({format: "dd/mm/yyyy"});
		$('#dp6').datepicker({format: "dd/mm/yyyy"});
		
		$('#dp7').datepicker({format: "dd/mm/yyyy"});
		$('#dp8').datepicker({format: "dd/mm/yyyy"});
		
		$( "#btnEquipo" ).click(function() {

			$('#modalEquipo').removeClass("invisible");
			$('#modalEquipo').modal('toggle');
			$('#modalEquipo').modal('show');						
		document.all.txtOperacionEquipo.value='INS';
		});



		$( "#btnCancelarEquipo" ).click(function() {
			$('#modalEquipo').modal('hide');

		recupeLider('auditoriasByLider', document.all.txtAuditoria.value, document.all.tablaEquipo);


		});

		$("input:checkbox:checked").each(function(){
						//cada elemento seleccionado
					//alert($(this).val());
		});

		$( "#btnGuardarEquipo" ).click(function() {
			
			guardarEquipo();
			$('#modalEquipo').modal('hide');
			recupeLider('auditoriasByLider', document.all.txtAuditoria.value, document.all.tablaEquipo);			
		});	
		

		$( "#btnLigarDocto" ).click(function() {		
			$( "#btnUpload" ).click(); 
		});
		
		
		

		$( "#btnAnexarDocto" ).click(function() {		
			$( "#btnUpload" ).click(); 
		});	


	$( "#btnNuevaActividad" ).click(function() {
		document.all.divListaActividades.style.display='none';
		document.all.divCapturaActividad.style.display='inline';
		
		document.all.txtOperacion.value='INS-ACT';
		
		document.all.btnNuevaActividad.style.display='none';
		document.all.btnGuardarActividad.style.display='inline';
		document.all.btnCancelarActividad.style.display='inline';
		
		//document.all.btnGuardarCronograma.style.display='none';
		document.all.btnCancelarCronograma.style.display='none';
		limpiarCamposActividad();
				
	});

	$( "#btnGuardarActividad" ).click(function() {		
		if (validarActividad())
		{
			guardarActividad();
			
			limpiarCamposActividad();			
			
		
		}
	});
	
	$( "#btnCancelarActividad" ).click(function() {
		document.all.divListaActividades.style.display='inline';
		document.all.divCapturaActividad.style.display='none';
		
		document.all.btnNuevaActividad.style.display='inline';
		document.all.btnCancelarActividad.style.display='none';
		document.all.btnGuardarActividad.style.display='none';
		
		document.all.btnGuardarCronograma.style.display='inline';
		document.all.btnCancelarCronograma.style.display='inline';
		



	});		

		
		$( "#btnNuevoDocto" ).click(function() {
			$('#modalDocto').removeClass("invisible");
			$('#modalDocto').modal('toggle');
			$('#modalDocto').modal('show');						
		});
		$( "#btnCancelarDocto" ).click(function() {
			$('#modalDocto').modal('hide');
		});
		$( "#btnGuardarDocto" ).click(function() {
			$('#modalDocto').modal('hide');
		});		
		

	
		$( "#btnCronograma" ).click(function() {
			$('#modalCronograma').removeClass("invisible");
			$('#modalCronograma').modal('toggle');
			$('#modalCronograma').modal('show');
									
		});
		$( "#btnCancelarCronograma" ).click(function() {
			$('#modalCronograma').modal('hide');

			recupeFases('lstauditoriaByFases', document.all.txtAuditoria.value, document.all.tablaFases);
		});
		
		$( "#btnGuardarCronograma" ).click(function() {
			$('#modalCronograma').modal('hide');
		});				

		$( "#btnRegresar" ).click(function() {
			document.all.listasAuditoria.style.display='inline';
			document.all.capturaAuditoria.style.display='none';		
		});	

		$( "#btnGuardarObjeto" ).click(function() {
			modificaraudi();
			
		});



		$( "#btnGuardarAut" ).click(function() {
				apagarBotones();
				asignarEtapa(document.all.txtAuditoria.value, sProcesoNuevo, sEtapaNueva);
				$('#modalAutorizar').modal('hide');
			});

		$( "#btnAUTORIZACION" ).click(function() { $('#modalAutorizar').modal('show');});


		

			
		
	});


	function apagarBotones(){
		document.all.btnAUTORIZACION.style.display='none';			
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
					<div class="col-xs-3"><h2>Progr. Espec. de Auditoria</h2></a></div>									
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


<!-- Main content starts -->

<div class="content">
  	<div class="panel panel-default">
		<div class="panel-body">
			<div class="row" id="listasAuditoria">
				<!-- -->
				<div class="col-md-5">
				 <div class="widget">
					<div class="widget-head">
					<div class="clearfix"> 
					  <div class="pull-left"><h3><i class="fa fa-bars"></i>Resumen de Auditorias</h3></div>
					</div>
					</div>
					<div class="widget-content">
					<div class="clearfix">
						<div class="col-md-12">
							<div id="canvasJG"></div>
						</div>           
							<div class="col-md-12">
						<hr>  
								<table class="table table-striped table-bordered table-hover table-condensed">
										<thead style="width: 100%; font-size: xx-small;">
											<tr><th style="text-align:center;">Unidad Administrativa</th><th style="text-align:center;">Financieras</th><th style="text-align:center;">Financieras y de Cumplimiento</th><th style="text-align:center;">De Cumplimiento</th><th style="text-align:center;">De Obra Pública</th><th style="text-align:center;">De Desempeño</th><th style="text-align:center;">Suma</th></tr>
										</thead>
										<tbody id="tablaAuditorias" style="text-align:center; font-size: xx-small;">
											
										</tbody>
									</table>
							</div>								
					<!--<div class="widget-foot"></div>-->
					</div>
					</div>
					</div>
				</div>
				
				<div class="col-md-7">
				<div class="widget">
					<div class="widget-head">
					  <div class="pull-left"><h3><i class="fa fa-bars"></i> Lista de Auditorías</h3></div>
					  <div class="widget-icons pull-right">
					  	<a href="#" class="wminimize"><i class="fa fa-chevron-up"></i></a> 						
					  </div>  
					  <div class="clearfix"></div>
					</div>             
					<div class="widget-content">
							<div class="table-responsive" style="height: 350px; overflow: auto; overflow-x:hidden;">
								<table class="table table-striped table-bordered table-hover table-condensed">
									<thead>
										<tr><th>Clave Auditoría</th><th>Àrea</th><th>Sujetos de Fiscalización</th><th>Objeto de Fiscalización</th><th>Tipo de Auditoría</th><th>% de Avance</th></tr>
									</thead>
										<tbody>										
											<?php foreach($datos as $key => $valor): ?>
											<tr onclick=<?php echo "javascript:recuperaAuditoria('" . $valor['auditoria'] . "');"; ?> style="width: 100%; font-size: xx-small">										  										  
											  <td><?php echo $valor['claveAuditoria']; ?></td>
											  <td><?php echo $valor['area']; ?></td>
											  <td><?php echo $valor['sujeto']; ?></td>
											  <td><?php echo $valor['objeto']; ?></td>
											  <td><?php echo $valor['tipo']; ?></td>
											  <td><?php echo $valor['avances']; ?></td>
											</tr>
											<?php endforeach; ?>										
										</tbody>
								</table>
							</div>							
					</div>
					<div class="widget-foot"></div>
					</div>
				</div>
			  </div>
			  
  			<div class="row" id="capturaAuditoria" style="display:none; padding:0px; margin:0px;">			
				<div class="col-xs-12">				
					<div class="widget">
						<!-- Widget head -->
						<div class="widget-head">
						  <div class="pull-left"><h3><i class="fa fa-pencil-square-o"></i> Programa Específico de Auditoria</h3></div>
						  <div class="widget-icons pull-right"> 
							<button type="button" id="btnGuardarAuditoria"	class="btn btn-primary  btn-xs" style="display:none;"><i class="fa fa-floppy-o"></i> Guardar Auditoría</button>
							<button type="button" id="btnCronograma"		class="btn btn-warning  btn-xs"><i class="fa fa-calendar"></i> Cronograma de Trabajo...</button>
							<button type="button" id="btnEquipo"			class="btn btn-warning  btn-xs"><i class="fa fa-users"></i> Equipo de Trabajo...</button>
							<button type="button" class="btn btn-default  btn-xs" name="btnAUTORIZACION" id="btnAUTORIZACION" style="display:none;"><i class="fa fa-external-link"></i>Autoriza Projecto</button>
							<button type="button" id="btnImprimir" 			class="btn btn-default  btn-xs"><i class="fa fa-print"></i> Nota Ejecutiva</button>
							<button type="button" id="btnGuardarObjeto"	class="btn btn-primary  btn-xs"><i class="fa fa-floppy-o"></i> Guardar Tipo Observacion</button>
							<button type="button" id="btnRegresar" 			class="btn btn-default  btn-xs"><i class="fa fa-undo"></i> Regresar</button>
							
						  </div>  
						  <div class="clearfix"></div>
						</div>              

						<!-- Widget content -->
						<div class="widget-content">						
							<br>
							<div class="col-xs-6">
								<div class="form-group">									
									<label class="col-xs-2">No. Auditoría</label>
									<div class="col-xs-1"><input type="text" class="form-control" name="txtAuditoria" readonly/></div>
									<label class="col-xs-2 control-label">Clave Auditoría</label>
									<div class="col-xs-2"><input type="text" class="form-control" name="txtclaveAuditoria" readonly/></div>													
									<label class="col-xs-1 control-label">Tipo</label>
									<div class="col-xs-4">
										<select class="form-control" name="txtTipoAuditoria" readonly>
											<option value="">Seleccione...</option>
										</select>
									</div>								
								</div>									
								<br>
								<div class="form-group">
									<label class="col-xs-2">Área</label>
									<div class="col-xs-10  control-label">
										<select class="form-control" name="txtArea" readonly>
											<option value="">Seleccione...</option>											
										</select>
									</div>
								</div>							
								<br>
								<div class="form-group">
									<label class="col-xs-2 control-label">Sector</label>
									<div class="col-xs-4">
										<select class="form-control" name="txtSector" readonly>
											<option value="">Seleccione...</option>											
										</select>
									</div>								
									<label class="col-xs-2 control-label">Subsector</label>
									<div class="col-xs-4">
										<select class="form-control" name="txtSubsector" readonly>
										<option value="">Seleccione...</option>			
										</select>
									</div>								
								</div>
								<br>
								<div class="form-group">
									<label class="col-xs-2 control-label">Unidades</label>
									<div class="col-xs-10">
										<select class="form-control" name="txtSujeto" readonly>
											<option value="">Seleccione...</option>											
										</select>
									</div>								
								</div>
								<br>
								<div class="form-group">
									<label class="col-xs-2 control-label">Objeto</label>
									<div class="col-xs-10">
										<select class="form-control" name="txtObjeto" readonly>
											<option value="">Seleccione...</option>											
										</select>
									</div>								
								</div>
								<br>
								<div class="form-group">
									<label class="col-xs-2 control-label"> Inicio</label>
									<div class="col-xs-4">
										<input type="date" class="form-control"  name="txtFechaInicio" readonly/>
									</div>
									<label class="col-xs-2 control-label">Termino</label>
									<div class="col-xs-4">
										<input type="date" class="form-control" name="txtFechaFin" readonly/>
									</div>
								</div>	
							<br>
							<div class="form-group">									
								<label class="col-xs-2 control-label">Objetivo(s)</label>
								<div class="col-xs-10"><textarea class="form-control" rows="2" placeholder="Objetivo(s)" id="txtObjetivo" name="txtObjetivo" readonly></textarea></div>
							</div>
							<br>
							<div class="form-group">									
								<label class="col-xs-2 control-label">Alcance(s)</label>
								<div class="col-xs-10"><textarea class="form-control" rows="2" placeholder="Alcance(s)" id="txtAlcance" name="txtAlcance" readonly></textarea></div>
							</div>
							<br>
							<div class="form-group">									
								<label class="col-xs-2 control-label">Justificación</label>
								<div class="col-xs-10"><textarea class="form-control" rows="2" placeholder="Justificación" id="txtJustificacion" name="txtJustificacion" readonly></textarea></div>
							</div>
							<br>

							<div class="form-group">									
								<label class="col-xs-2">Tipo de Presupuesto</label>
								<div class="col-xs-4"><input type="text" class="form-control" name="txtPresupuesto" readonly/></div>
								<label class="col-xs-2 control-label">Acompañamiento</label>
								<div class="col-xs-4"><input type="text" class="form-control" name="txtAcompanamiento" readonly/></div>
							</div>
															

							<div class="clearfix"></div>
							
							<div class="form-group">									
								<label class="col-xs-2 control-label">Tipo de Obs.</label>
								<div class="col-xs-10">
									<select class="form-control" name="txtTipoObs" onchange="cajaResultados(this.value);">
										<option value="">Seleccione</option>
										<option value="" Selected>NINGUNA OBSERVACION</option>											
										<option value="SIMPLE">OBSERVACIÓN SIMPLE</option>											
										<option value="GRAVE">PROBABLE FALTA GRAVE</option>											
									</select>
								</div>	
							</div>
							<br>							
							<div class="form-group" id="divResumen" style="display:none;">									
								<label class="col-xs-2 control-label">Resumen</label>
								<div class="col-xs-10"><textarea class="form-control" rows="5" placeholder="Escriba aqui el resumen o resultado obtenido..."></textarea></div>	
							</div>
							<div class="clearfix"></div>
							</div>
							<div class="col-xs-6">
								<div class="form-group">									
									<div class="col-xs-12">
										<table class="table table-striped table-bordered table-hover table-condensed">
											<caption>Cronograma de Trabajo por Fase</caption>
											<thead>
											<tr><th>Fase</th><th>Fecha Inicio</th><th>Fecha Fin</th><th>Cantidad de Días</th></tr>
											</thead>
											<tbody id="tablaFases" onclick="editarCronograma();" style="width: 100%; font-size: xx-small">					  
																															
											</tbody>
										</table>
									</div>									
								</div>
								<br>
								<div class="col-xs-12">	
										<br>
										<table class="table table-striped table-bordered table-hover table-condensed">
											<caption>Equipo de Trabajo</caption>
											<thead>
											<tr><th>Lider de proyecto</th><th>Nombre</th><th>Puesto</th></tr>
											</thead>
											<tbody id="tablaEquipo" style="width: 100%; font-size: xx-small">	
																						
											</tbody>
										</table>
										<br>								
								<div class="col-md-12">
									<ul class="nav nav-tabs">																		
										<li class="active"><a href="#tab-papeles" data-toggle="tab"> Papeles de Trabajo <i class="fa"></i></a></li>
										<li><a href="#tab-acciones" data-toggle="tab"> Acciones y recomendaciones <i class="fa"></i></a></li>
										<li><a href="#tab-documentos" data-toggle="tab"> Documentos <i class="fa"></i></a></li>
										<li><a href="#tab-plan" data-toggle="tab"> Avances por Actividad <i class="fa"></i></a></li>
									</ul>								
									<div class="tab-content">
										<div class="tab-pane" id="tab-documentos">
											<div class="table-responsive" style="height: 150px; overflow: auto; overflow-x:hidden;">
												<table class="table table-striped table-bordered table-hover">
												  <thead>
													<tr><th>No.</th><th>Tipo</th><th>Fecha Docto. </th><th>Asunto</th><th>Origen</th><th>Destino</th><th>Estatus</th></tr>
												  </thead>
												  <tbody id="tablaDoctos">								  
													<!-- <tr style="width: 100%; font-size: xx-small">
														<td>ASCM-2016/0035</td><td>OFICIO</td><td>16/01/2016</td></td><td>CÉDULA DE POTENCIALES</td><td>JURÍDICO</td><td>DIRECCIÓN GRAL DE AUDITORIAS "B"</td><td>RECIBIDO</td>
													</tr>
													<tr style="width: 100%; font-size: xx-small">
														<td>ASCM-2016/0001</td><td>NOTA</td><td>16/01/2016</td><td>SOLICTUD DE CREDITO</td><td>DIRECCIÓN GRAL DE AUDITORIAS "B"</td><td>JURÍDICO</td><td>ENVIADO</td>
													</tr>
													<tr style="width: 100%; font-size: xx-small">
														<td>IEDF-2015/0001</td><td>OFICIO</td><td>16/01/2016</td><td>SOLICTUD DE INFORMACIÓN</td><td>IEDF</td><td>ASCM</td><td>RECIBIDO</td>
													</tr> -->
												  </tbody>
												</table>											
											</div>
											<br>
											<button type="button" class="btn btn-primary  btn-xs" 	id="btnNuevoDocto"><i class="fa fa-file-text-o"></i> Nuevo Documento...</button>
											<button type="button" class="btn btn-default  btn-xs" 	id="btnLigarDocto"><i class="fa fa-link"></i> Ligar Documento</button>
											<input type="file" name="pic" accept="image/*" style="display:none;" id="btnUpload">

											
										</div>
										<div class="tab-pane active" id="tab-papeles">
											<div class="table-responsive" style="height: 150px; overflow: auto; overflow-x:hidden;">
												<table class="table table-striped table-bordered table-hover">
												  <thead>
													<tr><th>No.</th><th>Tipo</th><th>Resultado</th><th>Auditor</th><th>Fecha</th><th>Estatus</th><th>Anexo(s)</th></tr>
												  </thead>
												  <tbody id="tablaPapeles" style="width: 100%; font-size: xx-small">								  
						
												  </tbody>
												</table>											
											</div>
										</div>

										<div class="tab-pane " id="tab-acciones">
											<div class="table-responsive" style="height: 150px; overflow: auto; overflow-x:hidden;">
												<table class="table table-striped table-bordered table-hover">
													<thead>
													<tr><th>No.</th><th>Tipo de Acción</th><th>Cédula de Potenciales</th><th>Dictámen Técnico</th><th>Estatus</th></tr>
													</thead>
													<tbody id="tablaAcciones">										  
														<!-- <tr onclick=<?php echo "editarResultado('RESULT-0056');"; ?> style="width: 100%; font-size: xx-small">
															<td>ACC-00345</td><td>RECOMENDACIÓN</td><td>DGL-00561/14</td><td>DGL-00565/14</td><td>CUMPLIDA</td>
														</tr>
														<tr onclick=<?php echo "editarResultado('RESULT-0056');"; ?> style="width: 100%; font-size: xx-small">
															<td>ACC-00346</td><td>OBSERVACIÓN</td><td>DGL-00611/14</td><td>DGL-00565/14</td><td>CUMPLIDA</td>
														</tr>
														<tr onclick=<?php echo "editarResultado('RESULT-0056');"; ?> style="width: 100%; font-size: xx-small">
															<td>ACC-00347</td><td>SANCIÓN</td><td>DGL-00501/14</td><td>DGL-00565/14</td><td>CUMPLIDA</td>
														</tr>
														<tr onclick=<?php echo "editarResultado('RESULT-0056');"; ?> style="width: 100%; font-size: xx-small">
															<td>ACC-00348</td><td>RECOMENDACIÓN</td><td>DGL-00461/14</td><td>DGL-00565/14</td><td>CUMPLIDA</td>
														</tr> -->									
													</tbody>
												</table>
											</div>									
										</div>
										<div class="tab-pane" id="tab-plan">
								<div class="table-responsive" style="height: 500px; overflow: auto; overflow-x:hidden;">
									<table class="table table-striped table-bordered table-hover">
									  <thead>
										<tr><th>Id</th><th>Fecha</th><th>Porcentaje</th><th>Estatus</th></tr>
									  </thead>
									  <tbody>										  
											<!--<tr style="width: 100%; font-size: xx-small">
												<td>AV-0017</td><td>10/01/2016</td><td>5%</td><td>PROCESO</td><td><img src="img/xls.gif"></td>
											</tr>-->																							
									  </tbody>
									</table>
								</div>
											<br>
										</div>										
									</div>
								</div>	
							
							</div>																									
							<div class="clearfix"></div>
							<br>								
							
														
							<div class="clearfix"></div>
						</div>
							<div class="clearfix"></div>
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
			</div>
		</div>
   <div class="clearfix"></div>
</div>
<!-- Content ends -->

<div id="modalAutorizar" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<input type='HIDDEN' name='txtObjetoNuevo' value=''>									
			<!-- Modal content-->
			<div class="modal-content">									
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h3 class="modal-title"><i class="fa fa-home"></i> Autorizar...</h3>
				</div>									
				<div class="modal-body">
						<div class="row">
							<div class="form-group">									
								<div class="col-xs-12"><textarea class="form-control" rows="5" placeholder="Notas..." id="txtTextoLargo" name="txtTextoLargo"></textarea></div>
							</div>
						</div>
					<div class="clearfix"></div>
				</div>				
				<div class="modal-footer">
					<button  type="button" class="btn btn-warning active" id="btnGuardarAut"><i class="fa fa-floppy-o"></i> Autorizar</button>	
					<button  type="button" class="btn btn-default" id="btnCancelarAut"><i class="fa fa-undo"></i> Cancelar</button>	
				</div>
			</div>
		</div>
</div>

<form id="formulario" METHOD='POST' role="form">
	<input type='HIDDEN' name='txtValores' value=''>
	<input type='HIDDEN' name='txtOperacion' value=''>
	<input type='HIDDEN' name='txtOperacionEquipo' value=''>
	<input type='HIDDEN' name='txtCuenta' value=''>						
	<input type='HIDDEN' name='txtPrograma' value=''>
	<input type='HIDDEN' name='txtNombre' value=''>
	<input type='HIDDEN' name='txtPuesto' value=''>
	<input type='HIDDEN' name='txtActFase' value=''>
	<input type='HIDDEN' name='txtempleados' value=''>
	<input type='HIDDEN' name='txtlider' value=''>
	<input type='HIDDEN' name='txtId' value=''>

	
	<div id="modalCronograma" class="modal fade" role="dialog">
		<div class="modal-dialog">					
				<!-- Modal content-->
			<div class="modal-content">									
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h2 class="modal-title"><i class="fa fa-calendar"></i> Cronograma de Trabajo...</h2>
				</div>									
				<div class="modal-body">				
					<div class="table-responsive" style="height: 150px; overflow: auto; overflow-x:hidden;" id="divListaActividades">
						<table class="table table-striped table-bordered table-hover">
						  <thead>
							<tr><th>Fase</th><th>Actividad</th><th>Fecha Inicio</th><th>Fecha Fin</th><th>Porcentaje</th><th>Prioridad</th></tr>
						  </thead>
						  <tbody id="tablaActividades" >								  							
							</tbody>

							
						</table>											
					</div>
					<div id="divCapturaActividad" style="display:none;">
						<div class="col-xs-12">
							<div class="form-group">
								<label class="col-xs-2 control-label">ID</label>
								<div class="col-xs-2">
									<input type="text" class="form-control" name="txtIDActividad" readonly/>
								</div>

								<label class="col-xs-2 control-label">Tipo de Actividad</label>
								<div class="col-xs-2">
									<select class="form-control" name="txtTipoActividad" id="txtTipoActividad" onchange="verificarDependencia(this.value);">
										<option value="">Seleccione...</option>									
										<option value="SIMPLE" Selected>SIMPLE</option>
										<option value="SERIADA">SERIADA</option>
									</select>
								</div>

								<label class="col-xs-2 control-label">Fase</label>
								<div class="col-xs-2">
									<select class="form-control" name="txtFaseActividad" onChange="javascript:recuperarListaLigada('lstFaseByDescripcion', this.value, document.all.txtDescripcionActividad);">
										<option value="" Selected>Seleccione...</option>
									</select>
								</div>

						</div>						
						
						<br>								
							<div class="form-group">
								<label class="col-xs-2 control-label">Actividad</label>
							 	<div class="col-xs-10">						
									<select class="form-control" name="txtDescripcionActividad">
										<option value="" Selected>Seleccione...</option>
									</select>																		
								</div>
							</div>
						<br>	

							<div class="form-group" id="divActividadPrevia" style="display:'inline';">							
								<label class="col-xs-2 control-label">Actividad Previa</label>
								<div class="col-xs-10">
									<select class="form-control" name="txtPreviaActividad"  id="txtPreviaActividad">
										<option value="">Seleccione...</option>
										
									</select>
								</div>
								<br>
								<br>
							</div>						
																								
							<div class="form-group">								
								<label class="col-xs-2 control-label">Fecha Inicio</label>
								<div class="col-xs-2">
									<input type="date" class="form-control" name="txtInicioActividad"/>
								</div>	
								<label class="col-xs-2 control-label">Fecha Término</label>
								<div class="col-xs-2">
									<input type="date" class="form-control" name="txtFinActividad"/>
								</div>		
								<label class="col-xs-2 control-label">Dias Efectivos</label>
								<div class="col-xs-2">
									<input type="text" class="form-control" name="txtDiasActividad" readonly/>
								</div>															
							</div>						
							
							<br>
							<div class="form-group">								
								<label class="col-xs-2 control-label">Porcentaje</label>
								<div class="col-xs-2">
									<input type="number" class="form-control" name="txtPorcentajeActividad"/>
								</div>	
								<label class="col-xs-2 control-label">Prioridad</label>
								<div class="col-xs-2">
									<select class="form-control" name="txtPrioridadActividad">
										<option value="">Seleccione...</option>
										<option value="BAJA">BAJA</option>
										<option value="MEDIA" SELECTED>MEDIA</option>
										<option value="ALTA">ALTA</option>
									</select>
								</div>															
								<label class="col-xs-2 control-label">Impacto</label>
								<div class="col-xs-2">
									<select class="form-control" name="txtImpactoActividad">
										<option value="">Seleccione...</option>
										<option value="BAJO">BAJO</option>
										<option value="MEDIO" SELECTED>MEDIO</option>
										<option value="ALTO">ALTO</option>
									</select>
								</div>								
							</div>	    
							<br>							
							<div class="form-group">
								<label class="col-xs-2 control-label">Responsable</label>
								<div class="col-xs-6">
									<select class="form-control" name="txtResponsableActividad">
										<option value="" selected>Seleccione...</option>
									</select>
								</div>																																											
								<label class="col-xs-2 control-label">Estatus</label>
								<div class="col-xs-2">
									<select class="form-control" name="txtEstatusActividad">
										<option value="">Seleccione...</option>
										<option value="ACTIVO" SELECTED>ACTIVO</option>
										<option value="INACTIVO">INACTIVO</option>
									</select>
								</div>								
							</div>	
							<br>
							<div class="form-group">								
								<label class="col-xs-2 control-label">Notas</label>
								<div class="col-xs-10"><textarea class="form-control" rows="3" placeholder="Nota(s)" id="txtNotasActividad" name="txtNotasActividad"></textarea></div>							
							</div>	
							<br>
						</div>										
					</div>
					<div class="clearfix"></div>
				</div>				
				<div class="modal-footer">
					<button type="button" class="btn btn-primary  btn-xs" 	id="btnNuevaActividad"><i class="fa fa-file-text-o"></i> Nueva Actividad...</button>
					<button  type="button" class="btn btn-primary btn-xs active" id="btnGuardarActividad" 		style="display:none;"><i class="fa fa-undo"></i> Guardar Actividad</button>	
					<button  type="button" class="btn btn-default btn-xs" id="btnCancelarActividad" 		style="display:none;"><i class="fa fa-undo"></i> Cancelar Actividad</button>						
					<button  type="button" class="btn btn-default btn-xs" id="btnCancelarCronograma"><i class="fa fa-undo"></i> Salir</button>	
				</div>
			</div>		
		</div>
	</div>




	<div id="modalEquipo" class="modal fade" role="dialog">
		<div class="modal-dialog">							
				<!-- Modal content-->
			<div class="modal-content">									
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h2 class="modal-title"><i class="fa fa-users"></i> Equipo de Trabajo...</h2>
				</div>									
				<div class="modal-body" style="height: 350px; overflow: auto; overflow-x:hidden;">
					<table class="table table-striped table-bordered table-hover table-condensed">
						<caption>Lista de Auditores</caption>
						<thead>
						<tr><th>Asignar</th><th>Nombre<th>Puesto</th><th>Lider</th></tr>
						</thead>
						<tbody id="tablaAuditores" style="width: 100%; font-size: xx-small;">
							</tbody>
					</table>
					<div class="clearfix"></div>
				</div>
				
				<div class="modal-footer">
					<button  type="button" class="btn btn-primary btn-xs active" 	id="btnGuardarEquipo" 		style="display:inline;"><i class="fa fa-undo"></i> Guardar</button>	
					<button  type="button" class="btn btn-default btn-xs" 			id="btnCancelarEquipo" 		style="display:inline;"><i class="fa fa-undo"></i> Salir</button>	
				</div>
			</div>		
		</div>
	</div>

	<div id="modalDocto" class="modal fade" role="dialog">
		<div class="modal-dialog">
				<!-- Modal content-->
			<div class="modal-content">									
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title"><i class="fa fa-file-o"></i> Registrar Documento...</h4>
				</div>									
				<div class="modal-body">				
					<div class="form-group">
						<label class="col-xs-2 control-label">Flujo</label>
						<div class="col-xs-4">
							<select class="form-control" name="txtNivel">
								<option value="">Seleccione...</option>
								<option value="" Selected>ENTRADA</option>
								<option value="">SALIDA</option>
							</select>
						</div>
						<label class="col-xs-2 control-label">Tipo</label>
						<div class="col-xs-4">
							<select class="form-control" name="txtNivel">
								<option value="">Seleccione...</option>
								<option value="" Selected>OFICIO</option>
								<option value="">ATENTA NOTA</option>
								<option value="">ADENDUM</option>
							</select>
						</div>
					</div>
					<br>
					<div class="form-group">
						<label class="col-xs-2 control-label">F. Docto</label>
						<div class="col-xs-2">
							<input type="text" class="form-control" name="txtElector"/>
						</div>
						<label class="col-xs-2 control-label">F. Recepción</label>
						<div class="col-xs-2">
							<input type="text" class="form-control" name="txtElector"/>
						</div>
						<label class="col-xs-2 control-label">F. Término</label>
						<div class="col-xs-2">
							<input type="text" class="form-control" name="txtElector"/>
						</div>						
					</div>
					<br>
					<div class="form-group">
						<label class="col-xs-2 control-label">Asunto</label>
						<div class="col-xs-10">
							<input type="text" class="form-control" name="txtElector"/>
						</div>						
					</div>						
					<br>					
					<div class="form-group">
						<label class="col-xs-2 control-label">Dependencia</label>
						<div class="col-xs-10">
							<select class="form-control" name="txtNivel">
								<option value="">Seleccione...</option>
								<option value="" Selected>DELEGACIÓN TLALPAN</option>
								<option value="">SALIDA</option>
							</select>
						</div>						
					</div>
					<br>
					<div class="form-group">
						<label class="col-xs-2 control-label">Prioridad</label>
						<div class="col-xs-4">
							<select class="form-control" name="txtNivel">
								<option value="">Seleccione...</option>
								<option value="" >BAJA</option>
								<option value="" Selected>MEDIA</option>
								<option value="" >ALTA</option>
							</select>
						</div>	
						<label class="col-xs-2 control-label">Impacto</label>
						<div class="col-xs-4">
							<select class="form-control" name="txtNivel">
								<option value="">Seleccione...</option>
								<option value="" >BAJO</option>
								<option value="" Selected>MEDIO</option>
								<option value="" >ALTO</option>
							</select>
						</div>						
					</div>	
					<br>
					<div class="form-group">
						<label class="col-xs-2 control-label">Resumen</label>
						<div class="col-xs-10"><textarea class="form-control" rows="4"></textarea></div>					
					</div>						
					<div class="clearfix"></div>
				</div>				
				<div class="modal-footer">
					<button  type="button" class="btn btn-warning btn-xs active" id="btnAnexarDocto" 		style="display:inline;">Anexar</button>	
					<button  type="button" class="btn btn-primary btn-xs active" id="btnGuardarDocto" 		style="display:inline;">Guardar</button>	
					<button  type="button" class="btn btn-default btn-xs" id="btnCancelarDocto" 		style="display:inline;">Cancelar</button>	
				</div>
			</div>		
		</div>
	</div>
</form>

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