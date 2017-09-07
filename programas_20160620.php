<!DOCTYPE html>

<html lang="en"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">  
	<meta charset="utf-8">
		<script type="text/javascript" src="js/canvasjs.min.js"></script>
		<script type='text/javascript' src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js?ver=3.1.2"></script> 
		<script type="text/javascript" src="js/genericas.js"></script>
	
  	<style type="text/css">		
		@media screen and (min-width: 768px) {
			#mapa_content {width:100%; height:150px;}
			#canvasJG, #canvasJD, #canvasDIP{height:175px; width:100%;}			
			#modalObjetos .modal-dialog  {width:80%;}
			#modalCriterios .modal-dialog  {width:80%;}
			#modalAutorizar .modal-dialog  {width:30%;}


			.auditor{background:#f4f4f4; font-size:7pt; padding:2px; display:inline; margin:2px; border:1px black solid;}
			label {text-align:right;}	
			caption {padding: .2em .8em;border-bottom: 1px solid #fFF;background:#f4f4f4; font-weight: bold;}					
		}
	</style>
  
  	<script type="text/javascript"> 
	var mapa;
	var nZoom=10;
	var lstPartidas = new Array();
	var lstObjetosPartidas = new Array();

//	var NivelObjetoSel = new Array();
	
	var ventana;	

	var sProcesoActual="";
	var sEtapaActual="";
	var sProcesoNuevo="";
  	var sEtapaNueva="";

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



	
	// ************************************* SECCIONES DE RECUPERACIONES DE LISTAS PARA TABLAS *******************************************
	
	function recuperarTabla(lista, valor, tbl){
		//alert("Buscando Actividades: " + '/'+ lista + '/' + valor);
		$.ajax({
			type: 'GET', url: '/'+ lista + '/' + valor ,
			success: function(response) {
				var jsonData = JSON.parse(response);			
				//alert("Registros: " + jsonData.datos.length);
					
				//Vacia la lista
				tbl.innerHTML="";

				//Agregar renglones
				var renglon, columna;
				
				for (var i = 0; i < jsonData.datos.length; i++) {
					var dato = jsonData.datos[i];					
					renglon=document.createElement("TR");				
					renglon.innerHTML="<td></td><td>" + dato.funcion + "</td><td>" + dato.subfuncion + "</td><td>" + dato.actividad + "</td><td>" + dato.capitulo  + "</td><td>" + dato.partida + "</td>";
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


    function recuperarTablaCriterios(lista, valor, tbl){
    		//alert('/'+ lista + '/' + valor);
            $.ajax({
	           type: 'GET', url: '/'+ lista + '/' + valor ,
	           success: function(response) {
                   var jsonData = JSON.parse(response);                                 
                   //alert("Registros: " + jsonData.datos.length);
                                  
                   //Vacia la lista
                   tbl.innerHTML="";

                   //Agregar renglones
                   var renglon, columna;
                   
                   for (var i = 0; i < jsonData.datos.length; i++) {
                      var dato = jsonData.datos[i];                                                                    
                      renglon=document.createElement("TR");     
                      //renglon.setAttribute('width','100%');
                      //renglon.setAttribute('font-size','xx-small');
                      //style="width: 100%; font-size: xx-small"
                      renglon.innerHTML="<td>" + dato.criterio + "</td><td>" + dato.nombre + "</td>";
                      renglon.onclick= function() 
                      {
						document.all.txtOperacionCriterio.value = 'UPD';
						var miTipoAuditoria = document.all.txtTipoAuditoria.options[document.all.txtTipoAuditoria.selectedIndex].value;
						var miAuditoria = document.all.txtAuditoria.value;
						var miCriterio = dato.criterio;
						document.all.txtCriterioAnterior.value = dato.criterio;
						//alert(miAuditoria + " - " + miCriterio + " - " + miTipoAuditoria);


           				//alert(miCriterio + " - " + miAuditoria + " - " + miCriterio + " - " + document.all.txtJustificacionCriterio.value + " - " + document.all.txtElementosCriterio.value);
						
						$('#modalCriterios').removeClass("invisible");
						$('#modalCriterios').modal('toggle');
						$('#modalCriterios').modal('show');
						recuperarListaLigada('lstCriteriosByTipoAuditoria', miTipoAuditoria, document.all.txtCriterio);
           				recuperaAuditoriaCriterio(miAuditoria, miTipoAuditoria, miCriterio);
                        //alert('En Construcción funcion: recuperarTablaCriterios');
                      };
                      tbl.appendChild(renglon);                                                                       
                   }                                                             
	           },
	           error: function(xhr, textStatus, error){
	                alert('function recuperarTablaCriterios ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
	           }                                             
		});           
    }              

    function recuperarTablaArchivos(lista, valores, tbl){
    			//	alert('/'+ lista + '/' + valor);
            $.ajax({
	           type: 'GET', url: '/'+ lista + '/' + valores,
	           success: function(response) {
                   var jsonData = JSON.parse(response);                                 
                   
                   //alert("Registros: " + jsonData.datos.length);
                                  
                   //Vacia la lista
                   tbl.innerHTML="";

                   //Agregar renglones
                   var renglon, columna;
                   
                   // Limpia el Arreglo de partidas
                   //lstPartidas = [];

                   for (var i = 0; i < jsonData.datos.length; i++) {
                      var dato = jsonData.datos[i];                                                                    
                      renglon=document.createElement("TR");     
                      renglon.innerHTML="<td>" + dato.fase + "</td><td>" + dato.clasificacion + "</td><td>" + dato.asunto + "</td><td><img src='img/xls.gif'></td>";
                      //<td><img onclick="modalWin('mostrarPDF.php');" src="img/xls.gif"></td>
                      renglon.onclick= function() 
                      {
                      	//<td><img onclick="modalWin('mostrarPDF.php');" src="img/xls.gif"></td>
                      	//alert(dato.archivoOriginal);
                      	modalWin(dato.archivoOriginal);
                      };
                      tbl.appendChild(renglon);                                                                       
                   }                                                             
	           },
	           error: function(xhr, textStatus, error){
	                alert('function recuperarTablaArchivos ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
	           }                                             
		});           
     }

	// ************************************* SECCIONES DE GUARDADO DE DATOS *******************************************

	function guardarCriterio(){
		//sValor = document.all.txtDelegacionFiltro.options[document.all.txtDelegacionFiltro.selectedIndex].value;		
		var sValor="";
		var sOper=document.all.txtOperacionCriterio.value;
		var sRespuesta = "";
		var miCriterio = document.all.txtCriterio.options[document.all.txtCriterio.selectedIndex].value;
		var miAuditoria = document.all.txtAuditoria.value;

		var d = document.all;
		
		sValor = d.txtAuditoria.value+ '|' +  miCriterio + '|' + d.txtJustificacionCriterio.value + '|' + d.txtElementosCriterio.value + '|' + d.txtCriterioAnterior.value;

		//alert(sOper + ' | ' + sValor);

		$.ajax({ type: 'GET', url: '/guardar/auditoriaCriterios/' + sOper + '/' + sValor,
			success: function(response) {
				//var obj = JSON.parse(response);
				sRespuesta = response.replace(/^\s+/g,'').replace(/\s+$/g,'');
				if(sRespuesta == "OK"){
					recuperarTablaCriterios('tblCriteriosByAuditoria', miAuditoria, document.all.tblListaCriterios);
					alert("Los datos se guardaron correctamente.");
					document.all.divListaCriterios.style.display='inline';
					
					//document.all.divCapturaActividad.style.display='none';
					//document.all.btnNuevaActividad.style.display='inline';
					//document.all.btnCancelarActividad.style.display='none';
					//document.all.btnGuardarActividad.style.display='none';
					//document.all.btnCancelarCronograma.style.display='inline';	

					return true;
				}else{
					alert("No fue posible guardar los datos."+ response);
					return false;
				}
			},
			error: function(xhr, textStatus, error){
				alert(' Error en function guardarCriterio()  TextStatus: ' + textStatus + ' Error: ' + error );
				return false;
			}			
		});
	}

	function agregarAuditoria(){
		document.all.listasAuditoria.style.display='none';
		document.all.capturaAuditoria.style.display='inline';
		document.all.txtOperacion.value='INS';		
		document.all.botonAsignarCriterios.style.display='none';
		limpiarCamposAuditoria();
		limpiaTabla(document.all.tblListaCriterios);
		limpiaTabla(document.all.tblListaArchivos);
		//asignarElemento(document.all.txtObjeto,"","");
	}
	


	function guardarRegistroObjeto(sAuditoria, sNivel, sTexto, sValor){

		var d = document.all;
		var liga = '/guardar/registroObjeto/' + sAuditoria + '/' + sNivel + '/' + sTexto + '/' + sValor;
		//alert(liga);
		
		$.ajax({ type: 'GET', url: liga,
			success: function(response) {
				//var obj = JSON.parse(response);
				sRespuesta = response.replace(/^\s+/g,'').replace(/\s+$/g,'');
				if(sRespuesta == "OK"){
					alert("Los datos se guardaron correctamente.");
					return true;
				}else{
					alert("No fue posible guardar los datos."+ response);
					return false;
				}
			},
			error: function(xhr, textStatus, error){
				alert(' Error en function guardarRegistroObjeto()  TextStatus: ' + textStatus + ' Error: ' + error );
				return false;
			}			
		});
	}


	// ************************************* SECCIONES DE RECUPERADO DE DATOS *******************************************

	function recuperaAuditoria(id){
		//alert("Recuperando: " + id);
		$.ajax({
			type: 'GET', url: '/lstAuditoriaByID/' + id ,
			success: function(response) {
				var obj = JSON.parse(response);
				//alert(response);
				limpiarCamposAuditoria();
				
				// Recupera los valores de la auditoría desde la base de datos.
				//alert(obj.tipo + " - " + obj.area + " - " + obj.sector + " - " + obj.subSector + " - " + obj.unidad +  " - " + obj.objeto);

				document.all.txtCuenta.value='' + obj.cuenta;
				document.all.txtPrograma.value='' + obj.programa;
				document.all.txtAuditoria.value='' + obj.auditoria;				
				//## document.all.txtClaveAuditoria.value = '' + obj.claveAuditoria;				
				document.all.txtClaveAuditoria.value = '' + obj.claveAuditoria;				

				seleccionarElemento(document.all.txtTipoAuditoria, obj.tipo);
				seleccionarElemento(document.all.txtResponsable, obj.responsable);

				//document.all.txtResponsable.selectedIndex = 1;

				seleccionarElemento(document.all.txtSector, obj.sector); 
				seleccionarElemento(document.all.txtPresupuesto, obj.tipoPresupuesto);

				seleccionarElemento(document.all.txtEtapa, obj.etapa);
				//alert(obj.acompanamiento);
				
				if (obj.acompanamiento == 'on'){
					document.all.chkConAsf.checked = true;
				}else{
					document.all.chkConAsf.checked = false;
				}

				sProcesoActual=obj.proceso;
				sEtapaActual=obj.etapa;
				
				//alert("Proceso: " + sProcesoActual + " Etapa: " + sEtapaActual);
				proximaEtapa(sProcesoActual, sEtapaActual);

				//seleccionarElemento(document.all.txtObjeto, obj.objeto);

				recuperarListaSelected('lstSubsectoresBySector', obj.sector, document.all.txtSubsector, obj.subSector);
				seleccionarElemento(document.all.txtSubsector, obj.subSector);

				var cadena = "lstUnidadesBySectorSubsector/" + obj.sector;
				recuperarListaSelected(cadena, obj.subSector, document.all.txtUnidad, obj.unidad);
								
				/*
				var cadena = "lstObjetosSeleccionado/" + obj.auditoria;
				recuperarListaSelected(cadena, obj.objeto, document.all.txtObjeto, obj.objeto);
				*/

				recuperarListaSelected('lstObjetosByEnables', obj.auditoria, document.all.txtObjeto, obj.objeto);
				seleccionarElemento(document.all.txtObjeto, obj.auditoria);



				document.all.txtObjetivo.value='' + obj.objetivo;
				document.all.txtAlcance.value='' + obj.alcance;			
				document.all.txtJustificacion.value='' + obj.justificacion;

				var miAuditoria = document.all.txtAuditoria.value;
				//var miSujeto = document.all.txtUnidad.options[document.all.txtUnidad.selectedIndex].value;
				//var miObjeto = document.all.txtObjeto.options[document.all.txtObjeto.selectedIndex].value;
				var miSujeto = obj.unidad;
				var miObjeto = obj.objeto;

				var aValores = miAuditoria + '|' + miSujeto + '|' + miObjeto;
				//alert(aValores);

				recuperarTablaCriterios('tblCriteriosByAuditoria', miAuditoria, document.all.tblListaCriterios);
				recuperarTablaArchivos('tblArchivosByAuditoria', aValores, document.all.tblListaArchivos);

				document.all.btnGuardar.style.display='inline';
				document.all.txtOperacion.value="UPD";
								
	
				document.all.listasAuditoria.style.display='none';
				document.all.capturaAuditoria.style.display='inline';
				document.all.botonAsignarCriterios.style.display='inline';

		},
			error: function(xhr, textStatus, error){
				alert('function recuperaAuditoria()   -> statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error + ' Auditoría: ' + id);
			}			
		});		
	}
	
	/*
	function recuperarListaLigada_HVS(lista, valor, cmb){
			alert("RecuperarlistaLigada_HVS -> Valor de lista: " + lista + " Valor de valor:" + valor + " Combo:" + cmb + "-");
			$.ajax({
				type: 'GET', url: '/'+ lista + '/' + valor ,
				success: function(response) {
					var jsonData = JSON.parse(response);
					alert("response" + response);
					//alert(jsonData.datos.length);
					
					//Vacia la lista
					while (cmb.length>1){
						cmb.remove(cmb.length-1);
					}
						
					//Agregar elementos
					for (var i = 0; i < jsonData.datos.length; i++) {
						var dato = jsonData.datos[i];
						var option = document.createElement("option");
						option.text = dato.texto;
						option.value = dato.id;
						cmb.add(option, i+1);
					}				
				},
				error: function(xhr, textStatus, error){
					alert("ERROR EN: function recuperarListaLigada_HVS(lista, valor, cmb)" + " Donde lista=" + lista );
				}
			});	
		}

	*/
	
	function recuperaAuditoriaCriterio(auditoria, tipoAuditoria, criterio){

		//alert("Recuperando: " + auditoria + " - " + tipoAuditoria + " - " + criterio);

		$.ajax({
			type: 'GET', url: '/lstAudCriByIds/' + auditoria + '/' + tipoAuditoria + '/' + criterio ,
			success: function(response) {
				var obj = JSON.parse(response);
				
				
				seleccionarElemento(document.all.txtCriterio, obj.criterio);
				document.all.txtJustificacionCriterio.value='' + obj.justificacion;
				document.all.txtElementosCriterio.value='' + obj.elementos;

		},
			error: function(xhr, textStatus, error){
				alert('function recuperaAuditoriaCriterio()   -> statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
			}			
		});		
	}


	function recuperaFinalidades(valor){
		
		if( document.all.txtCentroGestor.selectedIndex>0 && document.all.txtCentroGestor.selectedIndex>0){			

			//var sector = document.all.txtSector.options[document.all.txtSector.selectedIndex].value;
			//var subsector = document.all.txtSubsector.options[document.all.txtSubsector.selectedIndex].value;
			//var centroGestor = document.all.txtCentroGestor.options[document.all.txtCentroGestor.selectedIndex].value;

			//alert('sector: ' + sector);
			//alert('subsector: ' + subsector);
			//alert('unidad/txtCentroGestor: ' + centroGestor);

			var liga = 'lstFinalidadesByCuenta';			
			recuperarLista(liga, document.all.txtFinalidad);
		}
	}
	
	function recuperaFunciones(valor){
		
		if( document.all.txtCentroGestor.selectedIndex>0 && document.all.txtFinalidad.selectedIndex>0){			

			//var sector = document.all.txtSector.options[document.all.txtSector.selectedIndex].value;
			//var subsector = document.all.txtSubsector.options[document.all.txtSubsector.selectedIndex].value;
			//var centroGestor = document.all.txtCentroGestor.options[document.all.txtCentroGestor.selectedIndex].value;
			var finalidad = document.all.txtFinalidad.options[document.all.txtFinalidad.selectedIndex].value;

			//alert('sector: ' + sector);
			//alert('subsector: ' + subsector);
			//alert('unidad/txtCentroGestor: ' + centroGestor);

			//var liga = 'lstFuncionesByFinalidad/' + sector + '/' + subsector + '/'	+ centroGestor
			recuperarListaLigada('lstFuncionesByFinalidad', finalidad, document.all.txtFuncion);
		}
	}

	function recuperaSubFunciones(valor){
		
		if( document.all.txtCentroGestor.selectedIndex>0 && document.all.txtFinalidad.selectedIndex>0 && document.all.txtFuncion.selectedIndex>0){			

			//var sector = document.all.txtSector.options[document.all.txtSector.selectedIndex].value;
			//var subsector = document.all.txtSubsector.options[document.all.txtSubsector.selectedIndex].value;
			//var centroGestor = document.all.txtCentroGestor.options[document.all.txtCentroGestor.selectedIndex].value;
			var finalidad = document.all.txtFinalidad.options[document.all.txtFinalidad.selectedIndex].value;
			var funcion = document.all.txtFuncion.options[document.all.txtFuncion.selectedIndex].value;

			//alert('unidad/txtCentroGestor: ' + centroGestor);
			//alert('funcion: ' + funcion);

			//var liga = 'lstSubFuncionesByBySectorSubsectorUnidadFinalidadFuncion/' + sector + '/' + subsector + '/'	+ centroGestor + '/' + finalidad;			
			var liga = 'lstSubFuncionesByFinalidadFuncion/' + finalidad;			
			recuperarListaLigada(liga, funcion, document.all.txtsubFuncion);
		}
	}

	function recuperaActividades(valor){
		
		if(	document.all.txtCentroGestor.selectedIndex>0 && document.all.txtFinalidad.selectedIndex>0 && document.all.txtFuncion.selectedIndex>0 && document.all.txtsubFuncion.selectedIndex>0 ){			
			
			//var sector = document.all.txtSector.options[document.all.txtSector.selectedIndex].value;
			//var subsector = document.all.txtSubsector.options[document.all.txtSubsector.selectedIndex].value;
			//var centroGestor = document.all.txtCentroGestor.options[document.all.txtCentroGestor.selectedIndex].value;
			var finalidad = document.all.txtFinalidad.options[document.all.txtFinalidad.selectedIndex].value;
			var funcion = document.all.txtFuncion.options[document.all.txtFuncion.selectedIndex].value;
			var subfuncion = document.all.txtsubFuncion.options[document.all.txtsubFuncion.selectedIndex].value;
		
			//alert('unidad/txtCentroGestor: ' + centroGestor);
			//alert('funcion: ' + funcion);
			//alert('subfuncion: ' + subfuncion);
		
			//var liga = 'lstActividadesBySectorSubsectorUnidadFinalidadFuncionSubfuncion/' + sector + '/' + subsector + '/' + centroGestor + '/' + finalidad + '/' + funcion;
			var liga = 'lstActividadesByFinalidadFuncionSubfuncion/' + finalidad + '/' + funcion;
			recuperarListaLigada(liga, subfuncion, document.all.txtActividad);
		}
	
	}

	function recuperaPartidas(valor){
		
		if(document.all.txtCentroGestor.selectedIndex>0 && document.all.txtFinalidad.selectedIndex>0 && document.all.txtFuncion.selectedIndex>0 && document.all.txtsubFuncion.selectedIndex>0 && document.all.txtActividad.selectedIndex>0){			
			
			//var centroGestor = document.all.txtCentroGestor.options[document.all.txtCentroGestor.selectedIndex].value;
			//var funcion = document.all.txtFuncion.options[document.all.txtFuncion.selectedIndex].value;
			//var subfuncion = document.all.txtsubFuncion.options[document.all.txtsubFuncion.selectedIndex].value;
			var miCapitulo = document.all.txtCapitulo.options[document.all.txtCapitulo.selectedIndex].value;
			var miAuditoria = document.all.txtAuditoria.value;
			//alert('unidad/txtCentroGestor: ' + centroGestor);
			//alert('funcion: ' + funcion);
			//alert('subfuncion: ' + subfuncion);

			if (miAuditoria==""){
				//alert("Valor de la Auditoria: [" + miAuditoria + "]");
				miAuditoria = 0;
				//alert("Nuevo valor de la Auditoria: [" + miAuditoria + "]");
			}

			var liga = 'tblPartidasByCapitulo/' + miAuditoria;
			recuperarTablaGasto(liga, miCapitulo, document.all.tblGasto);
		}
	
	}

	
	// ************************************* SECCIONES DE LIMPIAR ELEMENTOS ****************************************************

	function limpiarCamposAuditoria(){
		//##document.all.txtClaveAuditoria.value='';		
		
		// LIMPIA CAMPOS TEXTO		

		document.all.txtAuditoria.value='';		
		document.all.txtClaveAuditoria.value = '';
		
		document.all.txtObjetivo.value='';
		document.all.txtAlcance.value='';			
		document.all.txtJustificacion.value='';

		// MUEVE LA LISTAS A LA OPCION SELECCIONE...

		document.all.txtTipoAuditoria.selectedIndex=0;
		//if (document.all.txtResponsable.options.length >0){
		//	document.all.txtResponsable.selectedIndex = 1;	
		//}
		document.all.txtResponsable.selectedIndex = 0;	

		document.all.txtSector.selectedIndex=0;
		document.all.txtPresupuesto.selectedIndex=0;
		document.all.txtEtapa.selectedIndex=0;

		// BORRA LOS ELEMENTOS DE LAS LISTAS Y POSICIONA EN LA OPCION SELECCIONE...

			//document.all.txtSubsector.selectedIndex=0;
			//document.all.txtUnidad.selectedIndex=0;
		asignarElemento(document.all.txtSubsector,"","");
		asignarElemento(document.all.txtUnidad,"","");

			//document.all.txtObjeto.selectedIndex=0;		
			//recuperarLista('lstObjetosByEnables',0,document.all.txtObjeto, 0);
		asignarElemento(document.all.txtObjeto,"","");

		// QUITA LA SELECCIÓN AL ÚNICO CHECK DE LA PANTALLA	

		document.all.chkConAsf.checked=0;

	}	

	function limpiarCamposCriterio(){
		//document.all.txtAuditoria.value='';		
		document.all.txtCriterio.selectedIndex=0;
		document.all.txtJustificacionCriterio.value='';		
		document.all.txtElementosCriterio.value='';		
	}	

	function limpiarCamposObjeto(){

		// MUEVE LA LISTAS A LA OPCION SELECCIONE...

		document.all.txtCentroGestor.selectedIndex=0;
		document.all.txtFinalidad.selectedIndex=0;
		document.all.txtCapitulo.selectedIndex=0;

		// BORRA LOS ELEMENTOS DE LAS LISTAS Y POSICIONA EN LA OPCION SELECCIONE...
		/*
		document.all.txtFuncion.selectedIndex=0;
		document.all.txtsubFuncion.selectedIndex=0;
		document.all.txtActividad.selectedIndex=0;
		*/
		asignarElemento(document.all.txtFuncion,"","");
		asignarElemento(document.all.txtsubFuncion,"","");
		asignarElemento(document.all.txtActividad,"","");

		// Limpia la lista de las Auditorias

		limpiaTabla(document.all.tblGasto);

	}	


	// ************************************* SECCIONES DE VALIDACIONES ****************************************************

	function validarAuditoria(){
		  
		if (document.all.txtTipoAuditoria.selectedIndex==0){
			alert("Debe seleccionar el TIPO de la Auditoría.");
			document.all.txtTipoAuditoria.focus();
			return false;
		}	

		if (document.all.txtResponsable.selectedIndex==0){
			alert("Debe seleccionar el AREA RESPONSABLE de la Auditoría.");
			document.all.txtResponsable.focus();
			return false;
		}	

		if (document.all.txtSector.selectedIndex==0){
			alert("Debe seleccionar el SECTOR de la Auditoría.");
			document.all.txtTipoAuditoria.focus();
			return false;
		}	

		if (document.all.txtSubsector.selectedIndex==0){
			alert("Debe seleccionar el SUBSECTOR de la Auditoría.");
			document.all.txtTipoAuditoria.focus();
			return false;
		}	

		if (document.all.txtUnidad.selectedIndex==0){
			alert("Debe seleccionar el SUJETO de la Auditoría.");
			document.all.txtUnidad.focus();
			return false;
		}			
		/*
		if (document.all.txtObjeto.selectedIndex==0){
			alert("Debe seleccionar el OBJETO de la Auditoría.");
			document.all.txtTipoAuditoria.focus();
			return false;
		}	
		*/
		if (document.all.txtObjetivo.value==''){
			alert("Debe capturar el campo OBJETIVOS de la auditoría.");
			document.all.txtObjetivo.focus();
			return false;
		}	
		if (document.all.txtAlcance.value==''){
			alert("Debe capturar el campo ALCANCE de la auditoría.");
			document.all.txtAlcance.focus();
			return false;
		}			
		if (document.all.txtJustificacion.value==''){
			alert("Debe capturar el campo  JUSTIFICACIÓN de la auditoría.");
			document.all.txtJustificacion.focus();
			return false;
		}			
		
		if (document.all.txtPresupuesto.selectedIndex==0){
			alert("Debe seleccionar el TIPO DE PRESUPUESTO para la Auditoría.");
			document.all.txtPresupuesto.focus();
			return false;
		}	
		return true;		  
	}
	
	
	function validarCriterio(){

		if (document.all.txtCriterio.selectedIndex==0){
			alert("Debe seleccionar un CRITERIO.");
			document.all.txtCriterio.focus();
			return false;
		}	

		if (document.getElementById("txtJustificacionCriterio").value==''){
			alert("Debe capturar la JUSTIFICACIÓN.");
			document.all.txtJustificacionCriterio.focus();
			return false;
		}	
		if (document.getElementById("txtElementosCriterio").value==''){
			alert("Debe capturar los ELEMENTOS DE SELECCIÓN.");
			document.all.txtElementosCriterio.focus();
			return false;
		}			
		
		return true;		  
	}


	function validaAuditoriaCriterio(sAuditoria, sCriterio, sCriterioAnterior){
		//var sRegresa = false;
		//alert('/validarAuditoriaCriterio/' + sAuditoria + "/" + sCriterio);
		if (sCriterio != sCriterioAnterior)
		{
			$.ajax({
				type: 'GET', url: '/valAudCri/' + sAuditoria + '/' + sCriterio ,
				success: function(response) {
					//alert(response);
					var obj = JSON.parse(response);
					//alert(obj.total);

					if (obj.total <1){
						//alert("Regresara true");
						 //sRegresa = true; 
					} else{
						 alert("ATENCION: Ya existe el Criterio seleccionado " + " \n en la auditoría: " + sAuditoria + " \npor favor verifique.");
						 //alert("ATENCION: Ya existe el Criterio seleccionado, por favor verifique y reintente "); 
						 //sRegresa = false; 
					}
			},
				error: function(xhr, textStatus, error){
					alert('function validaAuditoriaCriterio()   -> statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
					//sRegresa = false;
				}
			});		
			//return(sRegresa);
		}
	}


	function guardaAsignaRegObjeto(sAuditoria, sNivel, sValor, sTexto){
		//var sRegresa = false;
		//alert('validarExistRegObjeto: /' + sAuditoria + '/' + sNivel + '/' + sValor);
		//alert('Valor de sTexto: ' + sTexto);


		if (sAuditoria == ""){
			//  se debe recuperar el registro seleccionado y mostrarlo en el combo de objetos
			var option = document.createElement("option");
			option.text = sTexto;
			option.value = sValor;
			document.all.txtObjeto.add(option, 1);
			seleccionarElemento(document.all.txtObjeto, sValor);
			$('#modalObjetos').modal('hide');			

		}else{
		
			$.ajax({
				type: 'GET', url: '/validaExisteRegObjeto/' + sAuditoria + '/' + sNivel + '/' + sValor ,
				success: function(response) {
					var obj = JSON.parse(response);

					if (obj.objeto > 0 ){
						recuperarListaLigada('lstObjetosSeleccionado/' + sAuditoria, obj.objeto, document.all.txtObjeto);
					}else{
						guardarRegistroObjeto(sAuditoria, sNivel, sTexto, sValor);
					
						// ------- AHORA Recuperará el IdObjeto Recien creado ----------
						$.ajax({
							type: 'GET', url: '/validaExisteRegObjeto/' + sAuditoria + '/' + sNivel + '/' + sValor ,
							success: function(response) {
								var objNew = JSON.parse(response);

								if (objNew.objeto > 0){
									recuperarListaLigada('lstObjetosSeleccionado/' + sAuditoria, objNew.objeto, document.all.txtObjeto);
								}
						},
							error: function(xhr, textStatus, error){
								alert('function guardaAsignaRegObjeto()   -> statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + Error );
								return false;
							}
						});
						// ----------------------------------------------------------------
					}
					$('#modalObjetos').modal('hide');			
				},
					error: function(xhr, textStatus, error){
						alert('function guardaAsignaRegObjeto()   -> statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
						return false;
					}
			});

		}
	}

	function recuperalistaAuditoriasTotales(lista, valor, tbl){
			$.ajax({
			type: 'GET', url: '/'+ lista ,
			success: function(response) {
				var jsonData = JSON.parse(response);

				//Vacia la lista
				tbl.innerHTML="";
				
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
				alert('function recuperalistaAuditoriasTotales ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
			}			
		});	
	}

//----------------------------------------------------------------------------------------------------------------------------------------------
/// sección de funcionae especiales para el manejo de la tabla PARTIDAS
//----------------------------------------------------------------------------------------------------------------------------------------------


    function recuperarTablaGasto(lista, valor, tbl){
		var sTatus= "";

        $.ajax({
           type: 'GET', url: '/' + lista + '/' + valor ,
          success: function(response) {
               var jsonData = JSON.parse(response);                                 
               //alert("Registros: " + jsonData.datos.length);
                              
               //Vacia la lista
               tbl.innerHTML="";

               //Agregar renglones
               var renglon, columna;

               	// Vaciar el arreglo
               	lstPartidas = [];

               for (var i = 0; i < jsonData.datos.length; i++) {
                 	var dato = jsonData.datos[i];                                                                    
                  	
                  	renglon=document.createElement("TR");    
 					
  					//var sTatus= "checked=false";
  					//alert(" Dato Asignado: "  + dato.asignado);

					sTatus= "";
					if (dato.asignado=="SI"){
						sTatus= "checked=true";
						seleccionarPartida(dato.idPartida, dato.nombre, true);
					}

					renglon.innerHTML="<td><input type='checkbox' name='' "+sTatus+" onclick='seleccionarPartida("+dato.idPartida+',"'+dato.nombre+'",'+"this.checked);'/></td><td>" + dato.idPartida + "</td><td>" + dato.nombre + "</td>";

                  	renglon.onclick= function() 
                  	{
                  	  //obtenerNumeroPartidasSeleccionadas();
                   	  //alert('En Construcción funcion: recuperarTablaGasto');
                  	};
                  	tbl.appendChild(renglon);                                                                       
               }                                                             
           },
           error: function(xhr, textStatus, error){
                alert('function recuperarTablaGasto ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
           }                                             
		});           
	}              

	function seleccionarPartida(partida, nombrePartida, activo)
	{
		if (activo==true){
			lstPartidas.push(new Array(partida, nombrePartida));
		}else{
			for( i = 0; i < lstPartidas.length; i++)
			{
				if(partida==lstPartidas[i][0]){
					lstPartidas.splice(i, 1);
				}
			}
		}
	}


	function actualizaPartidas(auditoria){
		var miCapitulo = document.all.txtCapitulo.options[document.all.txtCapitulo.selectedIndex].value;

		/////if(auditoria==""){
			
		////	if(lstPartidas.length > 0){
			
		////		for(i=0; i < lstPartidas.length; i++){ 
		////	
		////				var option = document.createElement("option");
		////				option.value = lstPartidas[i][0];
		////				option.text = lstPartidas[i][1];
		////				document.all.txtObjeto.add(option, i+1);
		////		}
		////		seleccionarElemento(document.all.txtObjeto, 0);
		////		$('#modalObjetos').modal('hide');			
		////	}
		////}else{
			//actualizaInformacion('lstPartidasOnObjetosByCapitulo',auditoria, miCapitulo, false);
		////}
	}

	function actualizaInformacion(lista, auditoria, capitulo, tipo){
		var totalRegistros = 0;
		var registrosArray = 0;
		lstObjetosPartidas = [];

		$.ajax({
			type: 'GET', url: '/' + lista + '/' + auditoria + '/' + capitulo,
			success: function(response) {
				var jsonData = JSON.parse(response);			

				// Llenará el arreglo de lstObjetos con los idObjeto de los registros que contengan el capitulo seleccionado 
				
				registrosArray = jsonData.datos.length;

				for (var i = 0; i < jsonData.datos.length; i++) {
					var dato = jsonData.datos[i];					
					lstObjetosPartidas.push(new Array(dato.id, dato.texto));
					totalRegistros = totalRegistros + 1
				}				

				// Si existieron registros previos iniciara el proceso para borrarlos
				
				//alert("Número de localizados para procesar los borrados son en totalRegistros: " + totalRegistros + " En registrosArray: " + registrosArray);
					
				if (totalRegistros > 0){

					for (var i = 0; i < lstObjetosPartidas.length; i++) {
						var idObjeto = lstObjetosPartidas[i][0];		

						// Borrara cada registro del capitulo en sia_objetosDetalles

						//alert("El Valor de idObjeto a borrar en sia_objetosDetalle es: " + idObjeto);
						borrarPartidasObjetosDetalle('borrar/partidas/objetosDetalle',auditoria, idObjeto); 
					}				

					// Borrara todos los registro del capitulo en sia_objetos
					
					//alert("El Valor de capitulo a borrar en sia_objetos es: " + capitulo);
					borrarPartidasObjetosDetalle('borrar/partidas/objetos',auditoria, capitulo); 
				}

				// Ahora agregara los registro de las partidas que se encuentran en el arreglo lstPartidas
				guardarPartidasEnObjetos('guardar/partidas/objetos',auditoria, capitulo);
				// Ahora llenará nuevamente el arreglo lstOnketosPartidas con los nuevos idObjeto de la tabla sia_objetos para 
				// con ellos dar de alta en la tabla sia_objetosDetalle las partidas seleccionadas en la tabla de partidas
				lstObjetosPartidas = [];
				actualizaInformacion2('lstPartidasOnObjetosByCapitulo',auditoria, capitulo, true);
			},
			error: function(xhr, textStatus, error){
				alert('function actualizaInformacion ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
			}			
		});		
	}

	function actualizaInformacion2(lista, auditoria, capitulo, tipo){
		$.ajax({
			type: 'GET', url: '/' + lista + '/' + auditoria + '/' + capitulo,
			success: function(response) {
				var jsonData = JSON.parse(response);			

				for (var i = 0; i < jsonData.datos.length; i++) {
					var dato = jsonData.datos[i];					
					lstObjetosPartidas.push(new Array(dato.id, dato.texto));
				}
				// OJO modificar este proceso y no guardar en este momento.
				if (auditoria==""){
					auditoria = 0;
				}
				guardarPartidasEnObjetosDetalle('guardar/partidas/objetosDetalle',auditoria, capitulo); 
				var liga = 'tblPartidasByCapitulo/' + auditoria;
				recuperarTablaGasto(liga, miCapitulo, document.all.tblGasto);

			},
			error: function(xhr, textStatus, error){
				alert('function actualizaInformacion2 ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
			}			
		});		
	}

 	function guardarPartidasEnObjetos(lista, auditoria, dato){
				
		var sValor="";
		var d = document.all;
		var separador="";

		for(i=0; i < lstPartidas.length; i++){
			sValor = sValor + separador + lstPartidas[i][0];
			separador='|';
		}

		var ligaCompleta = '/' + lista + '/' + auditoria + '/' + dato + '/' + sValor ;
		//alert("Entro en guardarPartidasEnObjetos, número de entradas en lstPartidas: " + lstPartidas.length + ", - " + ligaCompleta);

		$.ajax({
			type: 'GET', url: ligaCompleta, 
			success: function(response) {
				//alert(response);
				//var obj = JSON.parse(response);

				// Ojo no se mostrara este mensaje para evitar que se muestren dos mensajes de guardado,
				// Ya que también se muestra el guardado de datos de los objetos principales
				// alert("Los datos se guardaron correctamente.");
				return true;
			},
			error: function(xhr, textStatus, error){
				alert(' Error en function guardarPartidasEnObjetos()\n\tstatusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error: ' + error );
				return false;
			}			
		});
		
	}


 	function guardarPartidasEnObjetosDetalle(lista, auditoria, dato){
		var sValor="";
		var d = document.all;

		var separador="";
		var separador2="";

		for(i=0; i < lstObjetosPartidas.length; i++){
			
			sValor = sValor + separador2 + lstObjetosPartidas[i][0] + '|' + lstObjetosPartidas[i][1];
			separador2='*';
		}

		var ligaCompleta = '/' + lista + '/' + auditoria + '/' + dato + '/' + sValor ;
		//alert("Entro en guardarPartidasEnObjetosDetalle: " + ligaCompleta);

		$.ajax({
			type: 'GET', url: ligaCompleta, 
			success: function(response) {
				//alert(response);
				//var obj = JSON.parse(response);
				alert("Los datos se guardaron correctamente.");
				return true;
			},
			error: function(xhr, textStatus, error){
				alert(' Error en function guardarPartidasEnObjetos()\n\tstatusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error: ' + error );
				return false;
			}			
		});
		
	}


	function borrarPartidasObjetosDetalle(lista, auditoria, dato){
		
		var ligaCompleta = '/' + lista + '/' + auditoria + '/' + dato;
		
		//alert("Entro en borrarPartidasObjetosDetalle " + ligaCompleta);
		
		$.ajax({
			type: 'GET', url: ligaCompleta,
			success: function(response) {
				// cuando regresa varios valores se usa:
				//var jsonData = JSON.parse(response);	
				// Cuando solo regresa un valor:		
				sRespuesta = response.replace(/^\s+/g,'').replace(/\s+$/g,'');
				if(sRespuesta == "OK"){
					//alert("Los datos se guardaron correctamente.");
					return true;
				}else{
					//alert("No fue posible guardar los datos."+ response);
					return false;
				}
			},
			error: function(xhr, textStatus, error){
				alert('function borrarPartidasObjetosDetalle ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
			}			
		});		
	}

//----------------------------------------------------------------------------------------------------------------------------------------------
/// PARTIDAS
//----------------------------------------------------------------------------------------------------------------------------------------------


/// ********************************************************************************************************************************
/// *                                          SECCIÓN INICIAL (CARGA DE MÓDULO)                                                   *
///	********************************************************************************************************************************

	function generarClaves(){
		var cuenta = document.all.txtCuenta.value;
		var programa = document.all.txtPrograma.value;
		
		alert("Se van a generar las CLAVES DE AUDITORÍA.\n\n\nEste procesos puede tardar unos segundos, por favor espere.\n\n ");			
		document.all.divBotones.style.display="none";
		document.all.divIndicador.style.display="inline";
		$.ajax({
			type: 'GET', url: '/generarClaves/' + cuenta + '/'+ programa,
			success: function(response) {
				document.all.divBotones.style.display="inline";
				document.all.divIndicador.style.display="none";
				alert("La asignación de folios se realizó de forma correcta.");				
				$('#modalAutorizar').modal('hide');
			},
			error: function(xhr, textStatus, error){
				alert('function generarClaves()   -> statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error + ' Papel: ' + id);
			}			
		});		
	}

	function apagarBotones(){			
		document.all.btnVOBO.style.display='none';
		document.all.btnAUTORIZACION.style.display='none';
		document.all.btnENVIADA.style.display='none';
		document.all.btnVALIDACION.style.display='none';
		document.all.btnINTEGRACION.style.display='none';
	}	
		
	function inicializar() {
		var opciones = {zoom: nZoom,draggable: false,scrollwheel: true,	mapTypeId: google.maps.MapTypeId.ROADMAP};
		mapa = new google.maps.Map(document.getElementById('mapa_content'), { center: {lat: 19.4339249, lng: -99.1428964},zoom: nZoom});			
	}	
	
		window.onload = function () {
		var chart1; 
		
			
			setGrafica(chart1, "dpsAuditoriasByArea", "pie", "Auditorias", "canvasJG" );
			
	
	};

	function agregaOpcion(cmb, valor, texto){
		var opt;
		var ele;

		opt = document.createElement('option');
		ele = cmb.length;

    	opt.value = valor;
    	opt.text = texto;
    	//opt.innerHTML = ;
    	cmb.appendChild(opt, ele+1);
	}


	function guardarObjetosSeleccionados(){
		var separador="";
		var sValor ="";
		var d = document.all;
		var idObj;
		var textoObj;
		/*
		document.all.txtLstObjetos.value = document.all.txtFuncion.options[document.all.txtFuncion.selectedIndex].value + '|' + document.all.txtFuncion.options[document.all.txtFuncion.selectedIndex].text + '*' + document.all.txtsubFuncion.options[document.all.txtsubFuncion.selectedIndex].value + '|' + document.all.txtsubFuncion.options[document.all.txtsubFuncion.selectedIndex].text  + '*' + document.all.txtActividad.options[document.all.txtActividad.selectedIndex].value + '|' + document.all.txtActividad.options[document.all.txtActividad.selectedIndex].text  + '*' + document.all.txtCapitulo.options[document.all.txtCapitulo.selectedIndex].value + '|' + document.all.txtCapitulo.options[document.all.txtCapitulo.selectedIndex].text;
		*/

		d.txtLstObjetos.value = d.txtFuncion.options[d.txtFuncion.selectedIndex].value + '|' + d.txtFuncion.options[d.txtFuncion.selectedIndex].text + '*' + d.txtsubFuncion.options[d.txtsubFuncion.selectedIndex].value + '|' + d.txtsubFuncion.options[d.txtsubFuncion.selectedIndex].text  + '*' + d.txtActividad.options[d.txtActividad.selectedIndex].value + '|' + d.txtActividad.options[d.txtActividad.selectedIndex].text  + '*' + d.txtCapitulo.options[d.txtCapitulo.selectedIndex].value + '|' + d.txtCapitulo.options[d.txtCapitulo.selectedIndex].text;

		//alert("El valor de todos los campos es: " + d.txtLstObjetos.value);

		for(i=0; i < lstPartidas.length; i++){
			sValor = sValor + separador + lstPartidas[i][0] + '|' + lstPartidas[i][1];
			separador = '*';
		}
		document.all.txtLstPartidas.value = sValor;
		// Debe mostrar en el control txtObjeto, el valor seleccionado de el último nivel de los siguientes controles pertenecientes al modal modalObjeto:

		if ( d.txtCapitulo.selectedIndex == 0){
			if ( d.txtActividad.selectedIndex == 0){
				if ( d.txtsubFuncion.selectedIndex== 0){
					if ( d.txtFuncion.selectedIndex == 0){
						// No hay selección de objeto y no se debe llenar el combo txtObjeto.
					}else{
						idObj = d.txtFuncion.options[d.txtFuncion.selectedIndex].value;
						textoObj = d.txtFuncion.options[d.txtFuncion.selectedIndex].text;
						//alert("En Funcion - > El valor de idObj: es " + idObj + " El valor de textoObj es: " + textoObj);
						asignarElemento(d.txtObjeto, idObj, textoObj)
						seleccionarElemento(d.txtObjeto, idObj);
					}
				}else{
					idObj = d.txtsubFuncion.options[d.txtsubFuncion.selectedIndex].value;
					textoObj = d.txtsubFuncion.options[d.txtsubFuncion.selectedIndex].text;
					//alert("En Subfuncion - > El valor de idObj: es " + idObj + " El valor de textoObj es: " + textoObj);
					asignarElemento(d.txtObjeto, idObj, textoObj)
					seleccionarElemento(d.txtObjeto, idObj);
				}
			}else{
				idObj = d.txtActividad.options[d.txtActividad.selectedIndex].value;
				textoObj = d.txtActividad.options[d.txtActividad.selectedIndex].text;
				//alert("En Actividad - > El valor de idObj: es " + idObj + " El valor de textoObj es: " + textoObj);
				asignarElemento(d.txtObjeto, idObj, textoObj)
				seleccionarElemento(d.txtObjeto, idObj);
			}
		}else{
			idObj = d.txtCapitulo.options[d.txtCapitulo.selectedIndex].value;
			textoObj = d.txtCapitulo.options[d.txtCapitulo.selectedIndex].text;
			//alert("En Capitulo - > El valor de idObj: es " + idObj + " El valor de textoObj es: " + textoObj);
			asignarElemento(d.txtObjeto, idObj, textoObj)
			seleccionarElemento(d.txtObjeto, idObj);
		}

		$('#modalObjetos').modal('hide');			

	}



	$(document).ready(function(){
		
		recuperarLista('lstTiposAuditorias', document.all.txtTipoAuditoria);
		//recuperarLista('lstAreas', document.all.txtResponsable);
		recuperarLista('lstAreasResponsables', document.all.txtResponsable);
		recuperarLista('lstSectores', document.all.txtSector);

		//alert("document.all.txtAuditoria.value: " + document.all.txtAuditoria.value);
		if(document.all.txtAuditoria.value == ''){
			recuperarListaLigada('lstObjetosByEnables', 0, document.all.txtObjeto);
		}else{
			recuperarListaLigada('lstObjetosByEnables', document.all.txtAuditoria.value, document.all.txtObjeto);
		}

		recuperarLista('lstFinalidadesByCuenta',document.all.txtFinalidad);
		recuperalistaAuditoriasTotales('tblAuditorias',document.all.txtPrograma.value, document.all.tablaAuditoriasTotales);
		recuperarLista('lstEtapasByProceso',document.all.txtEtapa);

		// ************************************* PARA MODAL DE OBJETOS ****************************************************
		//alert('Antes de cargar las unidades');
		// ****************************************************************************************************************

		//$("#tblGasto").hide();

		$( "#btnGuardarAut" ).click(function() {
			apagarBotones();
			asignarEtapa(document.all.txtAuditoria.value, sProcesoNuevo, sEtapaNueva);
			
			//alert("Anterior: " + sEtapaAnterior + "  Etapa Nueva: " + sEtapaNueva);
			if(sEtapaNueva=="INTEGRACION"){
				generarClaves();
			}else{
				$('#modalAutorizar').modal('hide');
			}
			
		});

		$( "#btnCancelarAut" ).click(function() {$('#modalAutorizar').modal('hide');});

		$( "#btnVOBO" ).click(function() { $('#modalAutorizar').modal('show');});
		$( "#btnAUTORIZACION" ).click(function() { $('#modalAutorizar').modal('show');});   
		$( "#btnENVIADA" ).click(function() { $('#modalAutorizar').modal('show');});
		$( "#btnVALIDACION" ).click(function() { $('#modalAutorizar').modal('show');});
		$( "#btnINTEGRACION" ).click(function() { $('#modalAutorizar').modal('show');});

		$("#txtSubsector" ).change(function() {
			var sector;
			var subsector;
			
			if(document.all.txtSector.selectedIndex>0 && document.all.txtSubsector.selectedIndex>0){			
				sector = document.all.txtSector.options[document.all.txtSector.selectedIndex].value;
				subsector = document.all.txtSubsector.options[document.all.txtSubsector.selectedIndex].value;
				var cadena = 'lstUnidadesBySectorSubsector/' + sector; 
				recuperarListaLigada(cadena, subsector, document.all.txtUnidad);
			}		
		});

		$( "#btnCargarArchivo" ).click(function() { $("#btnUpload").click();});

		$("#txtUnidad" ).change(function() {
			/*
			var sector;
			var subsector;
			var unidad;
			
			if(document.all.txtSector.selectedIndex>0 && document.all.txtSubsector.selectedIndex>0 && document.all.txtUnidad.selectedIndex>0){			
				sector = document.all.txtSector.options[document.all.txtSector.selectedIndex].value;
				subsector = document.all.txtSubsector.options[document.all.txtSubsector.selectedIndex].value;
				unidad = document.all.txtUnidad.options[document.all.txtUnidad.selectedIndex].value;

				var cadena = 'lstObjetosBySectorSubsectorUnidad/' +  sector + '/' + subsector;
				recuperarListaLigada(cadena, unidad, document.all.txtObjeto); 
			}		
			*/
		});

		
		$( "#btnGuardar" ).click(function() {

			document.getElementById("txtEtapa").disabled=false;
			if (validarAuditoria()){
				//alert("Valor operacion: " + document.all.txtOperacion.value + " De etapa: " + document.all.txtEtapa.options[document.all.txtEtapa.selectedIndex].value);

				document.all.formulario.submit();
				// Recupera la auditoria para ejecutr el proceos de guardado con el ID de la auditoria recien creado
				//alert("Regresa del Submit e iniciara el llenado de objetos y de objetosDetalle el valor de txtOperacion es: *" + document.all.txtOperacion.value + "*");
				
				//if(document.all.txtOperacion.value == "INS"){
				//	// Guardara los valores de los objetos y objetosDetalle y de las partidas seleccionadas
				//	guardaObjetosNuevaGarantia();
				document.getElementById("txtEtapa").disabled=true;
					
				}
			}
		);

		function guardaObjetosNuevaGarantia(){
			var nuevoIdGarantia;
			var d = document.all;
			var sValores;

			// Inicialmente es necesario recuperar el ID de la nueva garantía, esto se realizara en base a varios campos con los que se guardo la auditoria.

			miTipoAuditoria = document.all.txtTipoAuditoria.options[document.all.txtTipoAuditoria.selectedIndex].value;
			miresponsable = document.all.txtResponsable.options[document.all.txtResponsable.selectedIndex].value;
			miSector = document.all.txtSector.options[document.all.txtSector.selectedIndex].value;
			miSubsector = document.all.txtSubsector.options[document.all.txtSubsector.selectedIndex].value;
			miUnidad = document.all.txtUnidad.options[document.all.txtUnidad.selectedIndex].value;
			miObjetivo = (document.all.txtObjetivo.value).trim();
			//miAlcance = (document.all.txtAlcance.value).trim();
			//miJustificacion = (document.all.txtJustificacion.value).trim();
			miTipoPresupuesto = document.all.txtPresupuesto.options[document.all.txtPresupuesto.selectedIndex].value;
			
			sValores =  miTipoAuditoria + '|' +  miResponsable + '|' + miSector + '|' +  miSubsector + '|' + miUnidad + '|' +  miObjetivo + '|' + miTipoPresupuesto ;
			//miAlcance + '|' +  miJustificacion; 

			////alert("Los datos contenidos en sValores son: " + sValores);

			listaCompleta = '/lstAuditoriaBySeveralData/' + sValores;
			$.ajax({
				type: 'GET', url: listaCompleta , 
				success: function(response) {

					/////alert(response);

					var obj = JSON.parse(response);	
					/////alert("El valor de obj.id es: *" + obj.id + "*");
					document.all.txtAuditoria.value	= obj.id;
					
					if( document.all.txtAuditoria.value == ""){
					}else{
						guardarObjetosSeleccionados();
					}
				},
				error: function(xhr, textStatus, error){
					alert('function guardaObjetosNuevaGarantia ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
				}			
			});		
		}


		$( "#btnGuardarCriterio" ).click(function() {
			var sCriterio;
			var sAuditoria;
			var bRegreso;

			if (validarCriterio())
			{		
				guardarCriterio();
			   $('#modalCriterios').modal('hide');			
			}

		});

		$( "#btnCancelar" ).click(function() {
			document.all.listasAuditoria.style.display='inline';
			document.all.capturaAuditoria.style.display='none';
		});
		
		$( "#btnLigarDocto" ).click(function() {		
			$( "#btnUpload" ).click(); 
		});
		
		$( "#btnNuevoDocto" ).click(function() {		
			$( "#btnUpload" ).click(); 
		});	

		$( "#btnVerObjeto" ).click(function() {		
			var nEntidadSeleccionada= document.all.txtUnidad.options[document.all.txtUnidad.selectedIndex].value;
			var sector;
			var subsector;
			
			if(document.all.txtSector.selectedIndex>0 && document.all.txtSubsector.selectedIndex>0){			
				sector = document.all.txtSector.options[document.all.txtSector.selectedIndex].value;
				subsector = document.all.txtSubsector.options[document.all.txtSubsector.selectedIndex].value;
				//unidad = document.all.txtUnidad.options[document.all.txtUnidad.selectedIndex].value;

				limpiarCamposObjeto();
				//var cadena = 'tblGastoByUnidad/' + sector + '/' + subsector;			
				//recuperarTabla(cadena, nEntidadSeleccionada, document.all.tblGasto);

				var liga = 'lstUnidadesBySectorSubsector/' + sector;			
				recuperarListaLigada(liga, subsector, document.all.txtCentroGestor);

				var liga = 'lstCapituloByCuenta';			
				recuperarLista(liga, document.all.txtCapitulo);

				$('#modalObjetos').removeClass("invisible");
				$('#modalObjetos').modal('toggle');
				$('#modalObjetos').modal('show');

			}
			else {
				alert('Es necesario selecionar un SECTOR y un SUBSECTOR')
			}

		});


		$( "#btnCriterios" ).click(function() {		
			//var nEntidadSeleccionada= document.all.txtUnidad.options[document.all.txtUnidad.selectedIndex].value;
			limpiarCamposCriterio();
			$('#modalCriterios').removeClass("invisible");
			$('#modalCriterios').modal('toggle');
			$('#modalCriterios').modal('show');
			document.all.txtOperacionCriterio.value = 'INS';
			var miTipoAuditoria = document.all.txtTipoAuditoria.options[document.all.txtTipoAuditoria.selectedIndex].value;
			var miAuditoria = document.all.txtAuditoria.value;
			recuperarListaLigada('lstCriteriosByTipoAuditoria', miTipoAuditoria, document.all.txtCriterio);
			recuperarTablaCriterios('tblCriteriosByAuditoria', miAuditoria, document.all.tblListaCriterios);

		});


		$( "#btnCancelarObjeto" ).click(function() {
			$('#modalObjetos').modal('hide');			
		});		

		$( "#btnCancelarCriterio" ).click(function() {
			$('#modalCriterios').modal('hide');			
		});		

		
		$( "#txtObjetivoEdit" ).click(function(){
			$('#modalTextoLargo').modal('show');
			document.all.txtTextoLargo.value = document.all.txtObjetivo.value;
			sEditando="O";
		});	
		
		$( "#txtAlcanceEdit" ).click(function(){
			$('#modalTextoLargo').modal('show');
			document.all.txtTextoLargo.value = document.all.txtAlcance.value;
			sEditando="A";
		});		

		$( "#txtJustificacionEdit" ).click(function(){
			$('#modalTextoLargo').modal('show');
			document.all.txtTextoLargo.value = document.all.txtJustificacion.value;
			sEditando="J";
		});				
		
		$( "#btnCancelarTexto" ).click(function() {
				$('#modalTextoLargo').modal('hide');				
		});				

		$( "#btnGuardarTexto" ).click(function() {
				$('#modalTextoLargo').modal('hide');				
				if(sEditando=="O") document.all.txtObjetivo.value=document.all.txtTextoLargo.value;
				if(sEditando=="A") document.all.txtAlcance.value=document.all.txtTextoLargo.value;
				if(sEditando=="J") document.all.txtJustificacion.value=document.all.txtTextoLargo.value;
		});		


		$( "#btnGuardarObjeto" ).click(function() {
			guardarObjetosSeleccionados();
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
  <!-- Data tables 
  <link rel="stylesheet" href="jquery.dataTables.css"> -->
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
					<div class="col-xs-3"><h2>P.G.A.</h2></div>									
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
				<div class="col-md-4">
					<div class="widget">
						<div class="widget-head">
							<div class="clearfix"> 
							  <div class="pull-left"><h3><i class="fa fa-bars"></i> Resumen </h3></div>
							</div>
						</div>
						<div class="widget-content">
							<div class="clearfix">

								<div class="col-md-12">
									<div id="canvasJG" ></div>
								</div>

								<div class="col-md-12">
									<hr> 
									<table class="table table-striped table-bordered table-hover table-condensed">
							 			<thead style="width: 100%; font-size: xx-small;">
											<tr>
												<th style="text-align:center;">Unidad Administrativa</th><th style="text-align:center;">Financieras</th><th style="text-align:center;">Financiera y de Cumplimiento</th><th style="text-align:center;">De Cumplimiento</th><th style="text-align:center;">De Obra Pública</th><th style="text-align:center;">De Desempeño</th><th style="text-align:center;">Suma</th>
											</tr>							 			
										</thead>
							  			<tbody id="tablaAuditoriasTotales" style="text-align:center; font-size: xx-small;">
							 			</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>				
				</div>				
				<div class="col-md-8">
					<div class="widget">
						<div class="widget-head">
							<h3><i class="fa fa-search"></i> Lista de Auditorías</h3>
							<div class="clearfix"></div>
						</div>             
						<div class="widget-content">
							<div class="table-responsive" style="height: 350px; overflow: auto; overflow-x:hidden;">
								<table class="table table-striped table-bordered table-hover table-condensed">
									<thead>
										<tr><th>Auditoría</th><th>Ärea</th><th>Sujetos de Fiscalización</th><th>Objeto de Fiscalización</th><th>Tipo de Auditoría</th></tr>
									</thead>
									<tbody>																			
										<?php foreach($datos as $key => $valor): ?>
											<tr onclick=<?php echo "javascript:recuperaAuditoria('" . $valor['auditoria'] . "');"; ?> style="width: 100%; font-size: xx-small">										  										  
												<!--  <td><?php echo $valor['auditoria']; ?></td> //##--> 
												<td><?php echo $valor['claveAuditoria']; ?></td>
												<td><?php echo $valor['area']; ?></td>
												<td><?php echo $valor['sujeto']; ?></td>
												<td><?php echo $valor['objeto']; ?></td>
												<td><?php echo $valor['tipo']; ?></td>
											</tr>
										<?php endforeach; ?>	
									</tbody>
								</table>
							</div>
						</div>
						
						<div class="widget-foot">


											<div class="pull-right">
						<form id="upload_form" enctype="multipart/form-data" method="post">
							<button  type="button" class="btn btn-default  btn-xs" id="btnCargarArchivo"><i class="fa fa-link"></i> Anexar Archivo...</button>
							<input type="file" name="btnUpload" accept="application/pdf,application/vnd.ms-excel,,application/vnd.ms-word, " style="display:none;" id="btnUpload">
							<progress id="progressBar" value="0" max="100" style="width:'100%'; display:none;"></progress>
							<h4 id="status" style="display:none;"></h4>
							<p id="lblAvances" style="display:none;"></p>
						</form>
					</div>				
					<div class="pull-left">

							<button onclick="agregarAuditoria();" type="button" class="btn btn-primary  btn-xs"><i class="fa fa-search"></i> Agregar Auditoría...</button>
							<button type="button" class="btn btn-default  btn-xs" name="btnENVIADA" id="btnENVIADA" style="display:none;"><i class="fa fa-external-link"></i>Enviar Unidad Técnica </button>
							<button type="button" class="btn btn-default  btn-xs" name= id="btnVALIDACION" id="btnVALIDACION" style="display:none;"><i class="fa fa-external-link"></i>Valida/Autoriza </button>
							<button type="button" class="btn btn-default  btn-xs" name="btnINTEGRACION" id="btnINTEGRACION" style="display:none;"><i class="fa fa-external-link"></i>Integrar PGA</button>
							</div>
							<div class="clearfix"></div>

						</div>
					</div>
				</div>
			</div>
		  
			<div class="row" id="capturaAuditoria" style="display:none; padding:0px; margin:0px;">
				<form id="formulario" METHOD='POST' action='/guardar/auditoria_HVS' role="form">
					<input type='HIDDEN' name='txtOperacion'value=''>
					<input type='HIDDEN' name='txtCuenta' value=''>						
					<input type='HIDDEN' name='txtPrograma' value=''>
					<input type='HIDDEN' name='txtLstObjetos' value=''>									
					<input type='HIDDEN' name='txtLstPartidas' value=''>									

					
					<div class="col-md-12">				
						<div class="widget">
							<!-- Widget head -->
							<div class="widget-head">
								<div class="pull-left">Captura de auditoria</div>
									<div class="widget-icons pull-right">
										<!-- <button type="button" id="btnCriterios" class="btn btn-primary  btn-xs ">Asignación de Criterios</button>  -->
										<button type="button"  id="btnGuardar" class="btn btn-primary  btn-xs"><i class="fa fa-floppy-o"></i> Guardar</button>
										<button type="button" id="btnCancelar" class="btn btn-default  btn-xs"><i class="fa fa-undo"></i> Regresar</button> 
							  		</div>  
							  	<div class="widget-icons pull-right" id="botonAsignarCriterios">
									<button type="button" class="btn btn-default  btn-xs" name="btnVOBO" id="btnVOBO" style="display:none;"><i class="fa fa-external-link"></i>Otorga VoBo.</button>
									<button type="button" class="btn btn-default  btn-xs" name="btnAUTORIZACION" id="btnAUTORIZACION" style="display:none;"><i class="fa fa-external-link"></i>Autoriza Projecto</button>
									<button type="button" class="btn btn-default  btn-xs"><i class="fa fa-external-link"></i>Cancelar </button>
									<button type="button" id="btnCriterios" class="btn btn-primary  btn-xs ">Asignación de Criterios</button> 
									<label>  </label> 
							  	</div>  
								<div class="clearfix"></div>
							</div>              

							<!-- Widget content -->
							<div class="widget-content">												
								<br>
								<div class="col-xs-8">															
									<div class="form-group">									
										<label class="col-xs-2 control-label">No. Auditoría</label>
										<div class="col-xs-1"><input type="text" class="form-control" name="txtAuditoria" readonly/></div>													
										<label class="col-xs-2 control-label">Clave Auditoría</label>
										<div class="col-xs-2"><input type="text" class="form-control" name="txtClaveAuditoria" readonly/></div>													
										<label class="col-xs-1 control-label">Tipo</label>
										<div class="col-xs-4">
											<select class="form-control" name="txtTipoAuditoria">
												<option value="">Seleccione...</option>
											</select>
										</div>								
									</div>								
									<br>

									<div class="form-group">
										<label class="col-xs-2 control-label">Responsable</label>
										<div class="col-xs-10">
											<select class="form-control" name="txtResponsable">
												<option value="">Seleccione...</option>											
											</select>
										</div>
									</div>
									<br>

									<div class="form-group">
										<label class="col-xs-2 control-label">Sector</label>
										<div class="col-xs-4">
											<select class="form-control" name="txtSector" onChange="javascript:recuperarListaLigada('lstSubsectoresBySector', this.value, document.all.txtSubsector);">
												<option value="">Seleccione...</option>											
											</select>
										</div>								

										<label class="col-xs-2 control-label">Subsector</label>
										<div class="col-xs-4">
											<select class="form-control" name="txtSubsector" id="txtSubsector">
												<option value="">Seleccione...</option>											
											</select>
										</div>								
									</div>									
									<br>
									<div class="form-group">
										<label class="col-xs-2 control-label">Sujeto</label>
										<div class="col-xs-10">
										<!--
											<select class="form-control" name="txtUnidad" onChange="javascript:recuperarListaLigada('lstObjetosByUnidad', 
											this.value, document.all.txtObjeto);">
										-->
											<select class="form-control" name="txtUnidad" id="txtUnidad" );">
												<option value="">Seleccione...</option>											
											</select>
										</div>								
									</div>
									<br>
									<div class="form-group">
										<label class="col-xs-2 control-label">Objeto</label>
										<div class="col-xs-8">
											<select class="form-control" name="txtObjeto" id="txtObjeto">
												<option value="">Seleccione...</option>										
											</select>										
										</div>	
										<div class="col-xs-2">
											<button  type="button" class="btn btn-link" id="btnVerObjeto">Objetos</button>											
										</div>
									</div>								
									<br>	

									<div class="form-group">
										<label class="col-xs-2 control-label">Objetivo(s) <i class="fa fa-pencil"  id="txtObjetivoEdit"></i></label>
										<div class="col-xs-10"><textarea class="form-control" rows="3" name="txtObjetivo" id="txtObjetivo"></textarea></div>									
									</div>
									<br>

									<div class="form-group">
										<label class="col-xs-2 control-label">Alcance(s) <i class="fa fa-pencil"  id="txtAlcanceEdit"></i></label>
										<div class="col-xs-10"><textarea class="form-control" rows="3" name="txtAlcance" id="txtAlcance"></textarea></div>
									</div>	
									<br>

									<div class="form-group">
										<label class="col-xs-2 control-label">Justificación <i class="fa fa-pencil"  id="txtJustificacionEdit"></i></label>
										<div class="col-xs-10"><textarea class="form-control" rows="3" name="txtJustificacion" id="txtJustificacion"></textarea></div>
									</div>
									<br>

									<div class="form-group">									
										<label class="col-xs-2">Tipo de Presupuesto</label>
										<div class="col-xs-2">
											<select class="form-control" name="txtPresupuesto">
												<option value="">Seleccione...</option>
												<option value="LOCAL" selected>LOCAL</option>
												<option value="FEDERAL">FEDERAL</option>
											</select>
										</div>

										<label class="col-xs-3"> Acompañamiento con la ASF </label>
										<div class="checkbox col-xs-1 " style="text-align:left; margin-top: 0px">
												<input type="checkbox" name="chkConAsf" Id="chkConAsf">
										</div>

										<label class="col-xs-1 control-label">Etapa</label>
										<div class="col-xs-3">
											<select class="form-control" name="txtEtapa" id="txtEtapa" disabled="true" >
												<option value="">Seleccione...</option>											
											</select>
										</div>
										<br>
									</div>
									<div class="clearfix"></div>
									<br>
								</div>
								<br>								

								<div class="col-xs-4">							
									<div class="form-group">
										<div class="table-responsive" style="height: 150px; overflow: auto; overflow-x:hidden;" id="divListaCriterios">
											<table class="table table-striped table-bordered table-hover table-condensed table-responsive" >
												<caption>Criterios de Selección Aplicados</caption>
												<thead>
													<tr><th>Criterio</th><th>Descripción</th></tr>
												</thead>
												<tbody id="tblListaCriterios" style="width: 100%; font-size: xx-small">
												</tbody>
											</table >
										</div>
										<br>

										<div class="table-responsive" style="height: 150px; overflow: auto; overflow-x:hidden;" id="divListaArchivos">
											<table class="table table-striped table-bordered table-hover table-condensed table-responsive" >
												<caption>Documentos Asociados</caption>
												<thead>
													<tr><th>Fase</th><th>Clasificación</th><th>Asunto</th><th>Archivo</th></tr>
												</thead>
												<tbody id="tblListaArchivos" style="width: 100%; font-size: xx-small">
												</tbody>
											</table >
										</div>
										<div class="clearfix"></div>
									</div>
							<br>
							<div class="clearfix"></div>
						</div>
						<div class="clearfix"></div>
					</div>
				</form>
			</div>
		  
		  
			<div class="clearfix"></div>
		</div>
	</div>
   <div class="clearfix"></div>
</div>
<!-- Content ends -->

	<div id="modalObjetos" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<input type='HIDDEN' name='txtObjetoNuevo' value=''>									
			<!-- Modal content-->
			<div class="modal-content">									
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h3 class="modal-title"><i class="fa fa-home"></i> Objeto de Fiscalización...</h3>
				</div>									
				<div class="modal-body">

					<form id="formularioObjetos" METHOD='POST' ACTION='/guardar/auditoriaObjetos' role="form">

						<!-- *************************************** CENTRO GESTOR  **********************************************-->
						<br>
						<div class="form-group">						
							<label class="col-xs-2 control-label">Centro gestor</label>
							<div class="col-xs-10">
								<!-- <select class="form-control" name="txtCentroGestor" onChange="javascript: recuperaFinalidades(this.value);"> -->
								<select class="form-control" name="txtCentroGestor">
									<option value="">Seleccione...</option>
								</select>
							</div>
						</div>

						<!-- ***************************************** FINALIDAD  ************************************************-->
						<br>
						<div class="form-group">						
							<label class="col-xs-2 control-label">Finalidad</label>
							<div class="col-xs-10">
								<select class="form-control" name="txtFinalidad" onChange="javascript: recuperaFunciones(this.value);">
									<option value="">Seleccione...</option>
								</select>
							</div>
						</div>


						<!-- ******************************************* FUNCION  **************************************************-->
						<br>
						<div class="form-group">						
							<label class="col-xs-2 control-label">Función</label>
							<div class="col-xs-10">
								<select class="form-control" name="txtFuncion" onChange="javascript: recuperaSubFunciones(this.value);">
									<option value="">Seleccione...</option>
								</select>
							</div>
						</div>

						<!-- ***************************************** SUBFUNCION  ************************************************-->
						<br>
						<div class="form-group">						
							<label class="col-xs-2 control-label">Subfunción</label>
							<div class="col-xs-10">
								<select class="form-control" name="txtsubFuncion" onChange="javascript: recuperaActividades(this.value);">
									<option value="">Seleccione...</option>
								</select>
							</div>
						</div>

						<!-- ******************************************* ACTIVIDAD **************************************************-->
						<br>
						<div class="form-group">						
							<label class="col-xs-2 control-label">Actividad</label>
							<div class="col-xs-10">
								<select class="form-control" name="txtActividad" >
									<option value="">Seleccione...</option>
								</select>
							</div>
						</div>

						<!-- ***************************************** CAPITULO  ************************************************-->
						<br>
						<div class="form-group">						
							<label class="col-xs-2 control-label">Capítulo</label>
							<div class="col-xs-10">
								<select class="form-control" name="txtCapitulo" onChange="javascript: recuperaPartidas(this.value);">
									<option value="">Seleccione...</option>
								</select>
							</div>
						</div>

						<!-- <div class="container-fluid">  -->
							<!-- ************************ ÁREA DE GRID VARIOS CAMPOS DEL OBJETO DE FISCALIZACIÓN ********************* -->
						<!-- 	<div class="row">  -->
							<!--	<div class="table-responsive" style="height: 350px; overflow: auto; overflow-x:hidden;"> -->
						<div class="table-responsive col-xs-12" style="height: 350px; overflow: auto; overflow-x:hidden;">	
						<br>
									<table class="table table-striped table-bordered table-hover">
										<caption>PRESUPUESTO DE LA UNIDAD RESPONSABLE</caption>
										<thead>
											<tr><th width="3%">Sel.</th><th width="20%">Partida</th><th width="70%">Descripción</th></tr>
										</thead>
										<tbody id="tblGasto">
										</tbody>
										<!-- <tbody id="tblListaArchivos" style="width: 100%; font-size: xx-small"> -->
									</table>
								</div>

						<!-- 	</div>  -->
						<!-- </div> -->

					</form>
					
					<div class="clearfix"></div>

				</div>				
				<div class="modal-footer">
					<button  type="button" class="btn btn-primary active" id="btnGuardarObjeto" style="display:inline;"><i class="fa fa-floppy-o"></i> Seleccionar Objeto</button>	
					<button  type="button" class="btn btn-default" id="btnCancelarObjeto" 		style="display:inline;"><i class="fa fa-undo"></i> Cancelar</button>	
				</div>

			</div>
		</div>
	</div>
	
	<div id="modalTextoLargo" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<input type='HIDDEN' name='txtObjetoNuevo' value=''>									
			<!-- Modal content-->
			<div class="modal-content">									
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h3 class="modal-title"><i class="fa fa-home"></i> Capturando...</h3>
				</div>									
				<div class="modal-body">
						<div class="row">
							<div class="form-group">									
								<div class="col-xs-12"><textarea class="form-control" rows="15" placeholder="Capture aqui" id="txtTextoLargo" name="txtTextoLargo"></textarea></div>
							</div>
						</div>
					<div class="clearfix"></div>
				</div>				
				<div class="modal-footer">
					<button  type="button" class="btn btn-primary active" id="btnGuardarTexto"><i class="fa fa-floppy-o"></i> Guardar</button>	
					<button  type="button" class="btn btn-default" id="btnCancelarTexto"><i class="fa fa-undo"></i> Cancelar</button>	
				</div>
			</div>
		</div>
	</div>
	

	<div id="modalCriterios" class="modal fade" role="dialog">
		<div class="modal-dialog">							
				<!-- Modal content-->
			<div class="modal-content">									
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h3 class="modal-title"><i class="fa fa-pencil-square-o"></i>Asignación de Criterio...</h3>
				</div>									
				<div class="modal-body">
							<form id="formularioCriterios" METHOD='POST' ACTION='/guardar/auditoriaCriterios' role="form">
								
								<!-- <input type='HIDDEN' name='txtCuenta' value='<?php echo $_SESSION["idCuentaActual"];?>'>
								<input type='HIDDEN' name='txtPrograma' value=''> -->
								<input type='HIDDEN' name='txtCriterioAnterior' value=''> 
								<input type='HIDDEN' name='txtOperacionCriterio' value=''>
	
								<br>
								<div class="form-group">						
									<label class="col-xs-2 control-label">Tipo</label>
									<div class="col-xs-10">
										<select class="form-control" name="txtCriterio" onChange="javascript: validaAuditoriaCriterio(document.all.txtAuditoria.value, this.value, document.all.txtCriterioAnterior.value);">
											<option value="">Seleccione...</option>
										</select>
									</div>
								</div>

								<br>
								<div class="form-group">
									<label class="col-xs-2 control-label">Justificación</label>
									<div class="col-xs-10">
									<textarea class="form-control" rows="5" placeholder="Capture aqui" id="txtJustificacionCriterio" name="txtJustificacionCriterio">  </textarea>
									</div>						
								</div>						

								<br>
								<div class="form-group">
									<label class="col-xs-2 control-label">Elementos de Selección</label>
									<div class="col-xs-10">
									<textarea class="form-control" rows="5" placeholder="Capture aqui" id="txtElementosCriterio" name="txtElementosCriterio"> </textarea>
									</div>						
								</div>						

								<div class="clearfix"></div>								
							</form>
					<div class="clearfix"></div>
				</div>
				
				<div class="modal-footer">
					<div class="pull-right">
						<button type="button" class="btn btn-primary active" id="btnGuardarCriterio" type="submit"><i class="fa fa-floppy-o"></i> Guardar</button>
						<button type="button" class="btn btn-default active" id="btnCancelarCriterio" data-dismiss="modal"><i class="fa fa-back"></i> Cancelar</button>
					</div>
					<div class="clearfix"></div>
				</div>
			</div>		
		</div>
	</div>

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
					<div id="divIndicador" class="pull-left" style="display:none"><img src="img/giphy.gif"></div>
					<div id="divBotones" class="pull-right">
						<button  type="button" class="btn btn-warning active" id="btnGuardarAut"><i class="fa fa-floppy-o"></i> Autorizar</button>	
						<button  type="button" class="btn btn-default" id="btnCancelarAut"><i class="fa fa-undo"></i> Cancelar</button>	
					</div>
	   			</div>
	   		</div>
		</div>
	 </div>/

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