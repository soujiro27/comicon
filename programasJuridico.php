<!DOCTYPE html>

<html lang="en"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"> 
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=Edge" />
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
			#modalUnidades .modal-dialog  {width:70%;}

			.auditor{background:#f4f4f4; font-size:7pt; padding:2px; display:inline; margin:2px; border:1px black solid;}
			label {text-align:right;}	
			caption {padding: .2em .8em;border-bottom: 1px solid #fFF;background:#f4f4f4; font-weight: bold;}					
		}
	</style>
  
  	<script type="text/javascript"> 
	var mapa;
	var nZoom = 10;
	var lstPartidas = new Array();
	var lstObjetosPartidas = new Array();
	var lstIngresosAll = new Array();
	var lstIngresos = new Array();
	var lstConsolidados = new Array();
	var lstConsolidadosAll = new Array();
	var lstUnidades = new Array();
	var controlObjetos = new Array();

	var lstRubros = new Array();

//	var NivelObjetoSel = new Array();
	var lstAuditoriasAutorizacion = new Array();
	
	var ventana;	

	var sProcesoActual="";
	var sEtapaActual="";
	var sProcesoNuevo="";
  	var sEtapaNueva="";

  	var rolAuditor       = false;
  	var rolLider         = false;
  	var rolDirector      = false;
  	var rolDGA           = false;
  	var rolUnidadTecnica = false;
  	var rolCaaaf         = false;

  	var etapaAuditorias = 'NULL';

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

	window.onload=function (){

	}


    function recuperarTablaIngresosFromArreglo(lista, valor, tbl){
		var sTatus= "";
		var liga;

		liga = '/' + lista + '/' + valor;

		//alert("El valor de la liga: " + liga);
		
        $.ajax({
           type: 'GET', url: liga ,
          success: function(response) {
               var jsonData = JSON.parse(response);                                 
               
               //alert("Registros: " + jsonData.datos.length);
                              
               //Vacia la lista
               tbl.innerHTML="";

               //Agregar renglones
               	var renglon, columna, claveNombre;

               	// Vaciar el arreglo
               	lstIngresos = [];
               	lstIngresosAll = [];
              		
               for (var i = 0; i < jsonData.datos.length; i++) {
                 	var dato = jsonData.datos[i];                                                                    

       				lstIngresosAll.push(new Array(dato.clave, dato.clave + " - " + dato.nombre));

                   	renglon=document.createElement("TR");    
 					
					sTatus= "";

					for (var j = 0; j < controlObjetos.length ; j++) {
						arrEle = controlObjetos[j];
						if (arrEle[4] == 'INGRESO'){
							if (arrEle[3] == dato.clave){
								sTatus= "checked=true";
								seleccionarIngreso(dato.clave, dato.nombre, dato.tipo, dato.origen, true);
								break;
							}
						}
					}

					claveNombre = "";
	              	for (var j = 0; j < dato.clave.length-1; j++) {
	              		claveNombre =  '\xa0' + claveNombre;
	               }

					claveNombre = claveNombre + dato.clave + " - " + dato.nombre;
					

					cadena = "<td><input type='checkbox' name='' "+sTatus + " onclick='seleccionarIngreso(" + '"' + dato.clave + '","' + dato.nombre+'","'+dato.tipo+'","'+dato.origen+'",'+"this.checked);'/></td><td>" + claveNombre + "</td><td>" + dato.tipo + "</td><td>" + dato.origen + "</td>";

					//alert("Cadena: "+ cadena);

					renglon.innerHTML= cadena;

                  	renglon.onclick= function() 
                   	{
                   	};
                   	tbl.appendChild(renglon);                                                                       
               }  	
                                                                          
           },
           error: function(xhr, textStatus, error){
                alert('function recuperarTablaIngresosFromArreglo ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
           }                                             

		});           
	}              


	function seleccionarIngreso(clave, nombreIngreso, tipo, origen, activo)
	{
		//alert("Clave a agregar :" + clave);
		if (activo==true){
			lstIngresos.push(new Array(clave, nombreIngreso, tipo, origen));
		}else{
			for( i = 0; i < lstIngresos.length; i++)
			{
				if(clave==lstIngresos[i][0]){
					lstIngresos.splice(i, 1);
				}
			}
		}
	}
	

    function recuperarTablaUnidadesFromArreglo(lista, valor, tbl){
		var sTatus= "";
		var liga;

		liga = '/' + lista + '/' + valor;

		//alert("El valor de la liga: " + liga);
		
        $.ajax({
           type: 'GET', url: liga ,
          success: function(response) {
               var jsonData = JSON.parse(response);                                 
               
               //alert("Registros: " + jsonData.datos.length);
                              
               //Vacia la lista
               tbl.innerHTML="";

               //Agregar renglones
               	var renglon, columna;

               	// Vaciar el arreglo
               	lstUnidades = [];
              		
               for (var i = 0; i < jsonData.datos.length; i++) {
                 	var dato = jsonData.datos[i];                                                                    

                   	renglon=document.createElement("TR");    
                   	sTatus = "";

                   	if (valor=="SIN_DEFINIR"){  // No se ha guardado la auditoria
                   		
                   		if (document.all.txtUnidades.length > 0){  //Se compara cada elemento del combo vs el registro recuperado

                   			for (var j = 0; j < document.all.txtUnidades.length; j++){
                   				if (document.all.txtUnidades.options[j].value == dato.id) {
			                   		sTatus= "checked=true";
			                   		seleccionarUnidad(dato.id, dato.texto, true);
                   				}
                   			}
                   			
                   		}
						
                   	}else{  // Los datos se filtraron de una auditoria y solo se validar si el query trae un valor de SI en el campo asignado

              			if(dato.asignado=="SI"){
               			 	sTatus= "checked=true";
	                   		seleccionarUnidad(dato.id, dato.texto, true);
	                   	}
    
                   	}


					cadena = "<td><input type='checkbox' name='' "+sTatus + " onclick='seleccionarUnidad(" + '"' + dato.id + '","' + dato.texto + '",'+"this.checked);'/></td><td>" + dato.texto + "</td>";

					//alert("Cadena: "+ cadena);

					renglon.innerHTML= cadena;

                  	renglon.onclick= function() 
                   	{
                   	};
                   	tbl.appendChild(renglon);                                                                       
               }  	
                                                                          
           },
           error: function(xhr, textStatus, error){
                alert('function recuperarTablaUnidadesFromArreglo ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
           }                                             

		});           
	}              


	function seleccionarUnidad(clave, nombreUnidad, activo)
	{
		//alert("Clave a agregar :" + clave);
		if (activo==true){
			lstUnidades.push(new Array(clave, nombreUnidad) );
		}else{
			for( i = 0; i < lstUnidades.length; i++)
			{
				if(clave==lstUnidades[i][0]){
					lstUnidades.splice(i, 1);
				}
			}
		}
	}

    function recuperarTablaConsolidadoFromArreglo(lista, valor, tbl, consolidado){
		var sTatus= "";
		var liga;
		var miImporte;

		liga = '/' + lista + '/' + valor;

		//alert("El valor de la liga: " + liga);
		
        $.ajax({
           type: 'GET', url: liga ,
          success: function(response) {
               var jsonData = JSON.parse(response);                                 
               
               //alert("Registros: " + jsonData.datos.length);
                              
               //Vacia la lista
               tbl.innerHTML="";

               //Agregar renglones
               	var renglon, columna, claveNombre;

               	// Vaciar el arreglo
               	lstConsolidados = [];
               	lstConsolidadosAll = [];
              		
              	//alert("Registros consolidados regresados: " + jsonData.datos.length);
               for (var i = 0; i < jsonData.datos.length; i++) {
                 	var dato = jsonData.datos[i];                                                                    
                  	
                 	lstConsolidadosAll.push(new Array(dato.consolidadoDetalle, dato.rubro_concepto));

                   	renglon=document.createElement("TR");    
 					
  			 		//var sTatus= "checked=false";
  			 		//alert(" Dato Asignado: "  + dato.asignado);

  			 		if (dato.importe == null ){ miImporte = 0;} else{ miImporte=dato.importe;}

					sTatus= "";

					for (var j = 0; j < controlObjetos.length ; j++) {
						arrEle = controlObjetos[j];
						if (arrEle[4] == 'CONSOLIDADO'){
							if ((arrEle[2].toString() == dato.consolidado.toString())){
								if (arrEle[3] == dato.consolidadoDetalle){
									sTatus= "checked=true";
									seleccionarConsolidado(dato.consolidadoDetalle, dato.rubro_concepto, dato.importe, consolidado, true);
									break;
								}
							}
						}
					}

					claveNombre = "";
	              	for (var j = 0; j < dato.nivel-1; j++) {
	              		claveNombre =  '\xa0' + '\xa0' + claveNombre;
	               }

					claveNombre = claveNombre + dato.rubro_concepto;
					
					cadena = "<td><input type='checkbox' name='' "+sTatus + " onclick='seleccionarConsolidado(" + '"' + dato.consolidadoDetalle + '","' + dato.rubro_concepto + '","' + miImporte + '","' + consolidado + '",' + "this.checked);'/></td><td>" + claveNombre + "</td><td>" + miImporte + "</td>";

					//alert("Cadena: "+ cadena);

					renglon.innerHTML= cadena;

                  	renglon.onclick= function() 
                   	{
                   	};
                   	tbl.appendChild(renglon);                                                                       
               }  	
                                                                          
           },
           error: function(xhr, textStatus, error){
                alert('function recuperarTablaConsolidadoFromArreglo ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
           }                                             

		});           
	}              


	function seleccionarConsolidado(consolidadoDetalle, rubro, importe, consolidado, activo)
	{
		//alert("consolidadoDetalle a agregar :" + consolidadoDetalle);
		if (activo==true){
			lstConsolidados.push(new Array(consolidadoDetalle, rubro, importe, consolidado));
		}else{
			for( i = 0; i < lstConsolidados.length; i++)
			{
				if(consolidadoDetalle==lstConsolidados[i][0]){
					lstConsolidados.splice(i, 1);
				}
			}
		}
	}


	// ************************************* SECCIONES DE GUARDADO DE DATOS *******************************************

	function agregarAuditoria(){
		// hvs nuevo código 2016/12/08
		validaOcultarClaves();
		// Fin hvs nuevo código 2016/12/08
		if (!rolCaaaf || !rolUnidadTecnica ){ document.getElementById("btnGuardar").disabled=false; }
		document.all.listasAuditoria.style.display='none';
		document.all.capturaAuditoria.style.display='inline';
		document.all.txtOperacion.value='INS';		
		limpiarCamposAuditoria();
		limpiaTabla(document.all.tblListaCriterios);
		limpiaTabla(document.all.tblListaArchivos);
		//asignarElemento(document.all.txtObjeto,"","");
	}
	

	// ************************************* SECCIONES DE RECUPERADO DE DATOS *******************************************

	function recupera_Auditoria(id){
	
		/////apagarBotones();
		//alert("Recuperando: " + id);
		if (rolCaaaf || rolUnidadTecnica){ 
			liga = '/lstAuditoriaByID_2/' + id;
			document.getElementById("btnGuardar").disabled=false; 
		}else{
			liga = '/lstAuditoriaByID_HVS/' + id;
		}

		//alert("El contenido de la liga es: " + liga);

		$.ajax({
			type: 'GET', url: liga , success: function(response) {
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
				
				//alert("El valor de Proceso: " + sProcesoActual + " Etapa: " + sEtapaActual);
				
				proximaEtapa(sProcesoActual, sEtapaActual);

				document.all.txtSector.value = obj.sector; 
				document.all.txtSubsector.value = obj.subSector;
				document.all.txtUnidad.value = obj.unidad;

				seleccionarElemento(document.all.txtUnidad, obj.sector + "|" + obj.subSector + "|" + obj.unidad);

				//recuperarListaSelected('lstObjetosByAuditoria', obj.auditoria, document.all.txtObjeto, obj.objeto);
				//seleccionarElemento(document.all.txtObjeto, obj.auditoria);
				//alert("Antes de mandar llenar el arreglo con la bd");
				llenarArregloConObjetos(obj.auditoria, document.all.txtObjeto);
				moverUnidadesBDaComboyArray(obj.auditoria, document.all.txtUnidades);


				document.all.txtObjetivo.value='' + obj.objetivo;
				document.all.txtAlcance.value='' + obj.alcance;			
				document.all.txtJustificacion.value='' + obj.justificacion;

				var miAuditoria = document.all.txtAuditoria.value;

				//alert("El valor de document.all.txtAuditoria.value: " + document.all.txtAuditoria.value);

				/*
				var miSujeto = obj.unidad;
				var miObjeto = obj.objeto;
				
				var aValores = miAuditoria + '|' + miSujeto + '|' + miObjeto;
				*/
				recuperarTablaCriterios('tblCriteriosByAuditoria', miAuditoria, document.all.tblListaCriterios);
				recuperarTablaArchivos('tblArchivosByAuditoria', miAuditoria, document.all.tblListaArchivos);


				// hvs nuevo código 2016/12/08
				validaOcultarClaves();
				// Fin hvs nuevo código 2016/12/08
				document.all.txtOperacion.value="UPD";
								
	
				document.all.listasAuditoria.style.display='none';
				document.all.capturaAuditoria.style.display='inline';

				if(!rolCaaaf || !rolUnidadTecnica ){
					document.all.btnGuardar.style.display='inline';
				}


		},
			error: function(xhr, textStatus, error){
				alert('function recuperaAuditoria()   -> statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error: ' + error + ' Proyecto de Auditoría: ' + id + ' Verique que tiene privilegios para ver el detalle de los proyectos de auditoría');
			}			
		});		
	}


	function recuperaFinalidades(valor){
			var liga = 'lstFinalidadesByCuenta';			
			recuperarLista(liga, document.all.txtFinalidad);
	}
	
	function recuperaFunciones(valor){

		if( document.all.txtFinalidad.selectedIndex>0){			
			var finalidad = document.all.txtFinalidad.options[document.all.txtFinalidad.selectedIndex].value;
			recuperarListaLigada('lstFuncionesByFinalidad', finalidad, document.all.txtFuncion);
		}
	}

	function recuperaSubFunciones(valor){
		
		if( document.all.txtFinalidad.selectedIndex>0 && document.all.txtFuncion.selectedIndex>0){			

			var finalidad = document.all.txtFinalidad.options[document.all.txtFinalidad.selectedIndex].value;
			var funcion = document.all.txtFuncion.options[document.all.txtFuncion.selectedIndex].value;
			var liga = 'lstSubFuncionesByFinalidadFuncion/' + finalidad;			
			recuperarListaLigada(liga, funcion, document.all.txtsubFuncion);
		}
	}

	function recuperaActividades(valor){
		
		if(	document.all.txtFinalidad.selectedIndex>0 && document.all.txtFuncion.selectedIndex>0 && document.all.txtsubFuncion.selectedIndex>0 ){			
			
			var finalidad = document.all.txtFinalidad.options[document.all.txtFinalidad.selectedIndex].value;
			var funcion = document.all.txtFuncion.options[document.all.txtFuncion.selectedIndex].value;
			var subfuncion = document.all.txtsubFuncion.options[document.all.txtsubFuncion.selectedIndex].value;
		
			var liga = 'lstActividadesByFinalidadFuncionSubfuncion/' + finalidad + '/' + funcion;
			recuperarListaLigada(liga, subfuncion, document.all.txtActividad);
		}
	
	}

	function recuperaPartidas(valor){
		
			var miCapitulo = document.all.txtCapitulo.options[document.all.txtCapitulo.selectedIndex].value;
			var miAuditoria = document.all.txtAuditoria.value;

			recuperarTablaGastoFromArreglo('tblPartidasByCapitulo/SIN_DEFINIR', miCapitulo, document.all.tblGasto);
	}

	function recuperaConsolidados(valor){
		
		if( document.all.txtTiposConsolidado.selectedIndex>0 ){			

			var tipoConsolidado= document.all.txtTiposConsolidado.options[document.all.txtTiposConsolidado.selectedIndex].value;
			recuperarListaLigada('lstConsolidadosByTipoConsolidado', tipoConsolidado, document.all.txtConsolidado);
		}
	}

	function recuperaLstConsolidados(valor){
		
			var miAuditoria = document.all.txtAuditoria.value;
			//var miSujeto = (document.all.txtUnidad.options[document.all.txtUnidad.selectedIndex].value).split("|");
			var miSujeto = (document.all.txtUnidades.options[0].value).split("|");
			//alert("Valor de miSujeto: " + miSujeto);

			if (miAuditoria.length = 0){
				var liga = 'tblConsolidadosDetalleByConsolidado/' + "SIN_DEFINIR" + '/' + miSujeto[0] + '/' + miSujeto[1] + '/' + miSujeto[2];
			}else{
				var liga = 'tblConsolidadosDetalleByConsolidado/' + miAuditoria + '/' + miSujeto[0] + '/' + miSujeto[1] + '/' + miSujeto[2];

			}
			//alert("Valor de Liga:" + liga);
			recuperarTablaConsolidadoFromArreglo(liga, valor, document.all.tblConsolidados, document.all.txtConsolidado.options[document.all.txtConsolidado.selectedIndex].value);
	}

// ============== MANEJO DEL ARREGLO PRINCIPAL

	function llenarArregloConObjetos(auditoria, cmb){

		liga = '/recuperaObjetosByAuditoria/' +  auditoria;

		$.ajax({ type: 'GET', url: liga ,
          success: function(response) {
            	var jsonData = JSON.parse(response);                                 
               
				while (cmb.length>=1){
						cmb.remove(cmb.length-1);
               	}

               for (var i = 0; i < jsonData.datos.length; i++) {
                 	var dato = jsonData.datos[i];                                                                    
       				var option = document.createElement("option");
                 	//alert("Id: " + dato.id + " Texto: " + dato.texto + " Nivel: " + dato.nivel + " Valor: " + dato.valor + " TipoObjeto: " + dato.tipoObjeto );

                 	controlObjetos.push(new Array(dato.id, dato.texto, dato.nivel, dato.valor, dato.tipoObjeto, dato.rubros) );
					option.value = dato.id;
					option.text = dato.texto;
					cmb.add(option, i+1);

  			 	}
               	//alert("En llenarArregloConObjetos la longitud de controlObjetos es: " + controlObjetos.length);
  			 }
		 });
	}

	function llenarObjetosConArreglo(cmb, id, text){
		var i;
		// Borra el combo y lo volvera a llenar con las opciones del arreglo
	    while (cmb.options.length > 0) {                
	        cmb.remove(0);
	    }      

		//Agregar elementos
		//alert("Llenando el txtObjetos(cmb) con controlObjetos(array): " + controlObjetos.length);

		for (i = 0; i < controlObjetos.length; i++) {
           	//alert("Contenido de controlObjetos: " + controlObjetos[i]);
			var dato = controlObjetos[i];
			var option = document.createElement("option");
			option.value = dato[0];
			option.text = dato[1];
			cmb.add(option, i+1);
		}				
	}

	function existeObjetoEnArreglo(id, text, nivel, valor, tipoObjeto){
		var arrEle, miTexto, regresa;
		var condi1, condi2
		// Primero valida que no exista en el arreglo
		for(var i = 0; i < controlObjetos.length ; i++ ){
			arrEle = controlObjetos[i];
			if(nivel=='PARTIDA'){
				miTexto = valor + " - " + text;
			}else{
				miTexto = text;
			}

			condi1 = arrEle[1]+arrEle[2]+arrEle[4];
			condi2 = miTexto+nivel+tipoObjeto;

			//alert(">Los valores a comparar son: " + condi1 + " vs \n" + condi2);
			if( (condi1 == condi2) ){
				//alert("El objeto seleccionado " + condi2 + ", ya ha sido seleccionado.");
				regresa = true;
				break;
			}else{
				regresa = false;
			}
		}
       	return regresa;

       	//controlObjetos.push(new Array(id, miTexto, nivel, valor, tipoObjeto) );
	}

	function actualizaPartidasEnArreglo(valor){
		var arrEle, idObj, textoObj, i=0;
		var nElementos = 0;
		var arrTemp=[];
		// Se inicia el recorrido del arreglo de control de objetos
		nElementos = controlObjetos.length;
		for( i = 0; i < nElementos; i++){
			// Se extrae el elemento (arreglo también)
			arrEle = controlObjetos[i];
			// Se validará que el arreglo contenga el tipo Partida y el valor (capitulo) indicado en el parametro 
			//alert("arrEle[1]: " + arrEle[1] + "\narrEle[2]: " + arrEle[2] + "\narrEle[3]: " + arrEle[3] + "\narrEle[4]: " + arrEle[4]);
			if(arrEle[2] == 'PARTIDA' && arrEle[3] == valor && arrEle[4] == 'EGRESO'){
			}else{
				arrTemp.push(new Array(arrEle[0], arrEle[1], arrEle[2], arrEle[3],arrEle[4]));
			}
		}
		//alert("El valor lstPartidas.length: " + lstPartidas.length);
		// Ahora agregara al arreglo controlObjetos las partidas seleccionadas en lstPartidas
		for(i=0; i < lstPartidas.length; i++){
			idObj = lstPartidas[i][0];
			textoObj = lstPartidas[i][1];
           	//controlObjetos.push(new Array('',idObj + " - " + textoObj , 'PARTIDA', valor, 'EGRESO'));
           	arrTemp.push(new Array('',idObj + " - " + textoObj , 'PARTIDA', valor, 'EGRESO'));
		}
		while (controlObjetos.length>=1){
			controlObjetos=[];
		}
		controlObjetos = arrTemp.slice();
		llenarObjetosConArreglo(document.all.txtObjeto, "", textoObj);
		ordenarListaByTexto(document.all.txtObjeto);
	}


	function actualizaConsolidadosEnArreglo(valor){
		var arrEle, idObj, textoObj, i=0;
		var nElementos = 0;
		var arrTemp=[];
		// Se inicia el recorrido del arreglo de control de objetos
		nElementos = controlObjetos.length;
		for( i = 0; i < nElementos; i++){
			// Se extrae el elemento (arreglo también)
			arrEle = controlObjetos[i];
			// Se validará que el arreglo contenga el tipo Partida y el valor (capitulo) indicado en el parametro 
			//alert(" actualizarConsolidadosEnArreglo==> arrEle[1]: " + arrEle[1] + "\narrEle[2]: " + arrEle[2] + "\narrEle[3]: " + arrEle[3] + "\narrEle[4]: " + arrEle[4] + " valor: " + valor);
			if(arrEle[2] == 'CONSOLIDADO' && arrEle[3] == valor && arrEle[4] == 'CONSOLIDADO'){
				/*
				alert("Borrara la entrada en controlObjeto");
				controlObjetos.splice(i,1);
				if (i>controlObjetos.length){
					break;
				}
				*/
			}else{
				arrTemp.push(new Array(arrEle[0], arrEle[1], arrEle[2], arrEle[3],arrEle[4]));
			}
		}
		//alert("El valor lstPartidas.length: " + lstPartidas.length);
		// Ahora agregara al arreglo controlObjetos las partidas seleccionadas en lstPartidas
		for(i=0; i < lstConsolidados.length; i++){
			idObj = lstConsolidados[i][0];
			textoObj = lstConsolidados[i][1];
			idConsolidado = lstConsolidados[i][3];
           	//controlObjetos.push(new Array('',idObj + " - " + textoObj , 'PARTIDA', valor, 'EGRESO'));
           	arrTemp.push(new Array(idObj, textoObj, idConsolidado, idObj, 'CONSOLIDADO'));
		}
		while (controlObjetos.length>=1){
			controlObjetos=[];
		}
		controlObjetos = arrTemp.slice();
		llenarObjetosConArreglo(document.all.txtObjeto, "", textoObj);
		ordenarListaByTexto(document.all.txtObjeto);
	}
	

	function actualizaIngresosEnArreglo(valor){
		var arrEle, idObj, textoObj, i=0;
		var nElementos = 0;
		var arrTemp=[];
		// Se inicia el recorrido del arreglo de control de objetos
		nElementos = controlObjetos.length;
		for( i = 0; i < nElementos; i++){
			// Se extrae el elemento (arreglo también)
			arrEle = controlObjetos[i];
			// Se validará que el arreglo contenga el tipo Partida y el valor (capitulo) indicado en el parametro 
			//alert("actualizaIngresosEnArreglo => arrEle[1]: " + arrEle[1] + "\narrEle[2]: " + arrEle[2] + "\narrEle[3]: " + arrEle[3] + "\narrEle[4]: " + arrEle[4]);
			if(arrEle[4] == 'INGRESO'){
				/*
				alert("Borrara la entrada en controlObjeto");
				controlObjetos.splice(i,1);
				if (i>controlObjetos.length){
					break;
				}
				*/
			}else{
				arrTemp.push(new Array(arrEle[0], arrEle[1], arrEle[2], arrEle[3],arrEle[4]));
			}
		}
		//alert("El valor lstPartidas.length: " + lstPartidas.length);
		// Ahora agregara al arreglo controlObjetos las partidas seleccionadas en lstPartidas
		for(i=0; i < lstIngresos.length; i++){
			idObj = lstIngresos[i][0];
			textoObj = lstIngresos[i][1];
           	//controlObjetos.push(new Array('',idObj + " - " + textoObj , 'PARTIDA', valor, 'EGRESO'));
           	arrTemp.push(new Array(idObj, textoObj, 'INGRESO', idObj, 'INGRESO'));
		}
		while (controlObjetos.length>=1){
			controlObjetos=[];
		}
		controlObjetos = arrTemp.slice();
		llenarObjetosConArreglo(document.all.txtObjeto, "", textoObj);
		ordenarListaByTexto(document.all.txtObjeto);
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

		document.all.txtSector.value='';
		document.all.txtSubsector.value='';

		// MUEVE LA LISTAS A LA OPCION SELECCIONE...

		document.all.txtTipoAuditoria.selectedIndex=0;
		document.all.txtResponsable.selectedIndex = 0;	
		document.all.txtPresupuesto.selectedIndex=0;
		document.all.txtEtapa.selectedIndex=0;
		document.all.txtUnidad.selectedIndex=0;

		// BORRA LOS ELEMENTOS DE LAS LISTAS Y POSICIONA EN LA OPCION SELECCIONE...

		asignarElemento_HVS(document.all.txtObjeto,"","",true,false);
		asignarElemento_HVS(document.all.txtUnidades,"","",true,false);

		// QUITA LA SELECCIÓN AL ÚNICO CHECK DE LA PANTALLA	

		document.all.chkConAsf.checked=0;

		// INICIALIZA LOS ARREGLOS GLOBALES

		lstPartidas = [];
		lstObjetosPartidas = [];
		lstIngresosAll = [];
		lstIngresos = [];
		lstConsolidados = [];
		lstConsolidadosAll = [];
		lstUnidades = [];
		controlObjetos = [];

	}	

	function limpiarCamposObjeto(){

		// MUEVE LA LISTAS A LA OPCION SELECCIONE...

		document.all.txtCentroGestor.selectedIndex=0;
		document.all.txtFinalidad.selectedIndex=0;
		document.all.txtCapitulo.selectedIndex=0;

		// BORRA LOS ELEMENTOS DE LAS LISTAS Y POSICIONA EN LA OPCION SELECCIONE...
		asignarElemento(document.all.txtFuncion,"","");
		asignarElemento(document.all.txtsubFuncion,"","");
		asignarElemento(document.all.txtActividad,"","");

		// Limpia la lista de las Auditorias

		limpiaTabla(document.all.tblGasto);
		limpiaTabla(document.all.tblIngreso);
		limpiaTabla(document.all.tblUnidades);

	}	


	// ************************************* SECCIONES DE VALIDACIONES ****************************************************

	function validarAuditoria(){
		  
		if (document.all.txtTipoAuditoria.selectedIndex==0){
			alert("Debe seleccionar el TIPO del Proyecto de Auditoría.");
			document.all.txtTipoAuditoria.focus();
			return false;
		}	
		/*
		if (document.all.txtUnidad.selectedIndex==0){
			alert("Debe seleccionar el SUJETO de la Auditoría.");
			document.all.txtUnidad.focus();
			return false;
		}
		*/
		if (document.all.txtUnidades.length == 0){
			alert("Debe seleccionar el campo SUJETOS del Proyecto de Auditoría.");
			document.all.txtUnidades.focus();
			return false;
		}	
		if (document.all.txtObjetivo.value==''){
			alert("Debe capturar el campo OBJETIVOS del Proyecto de Auditoría.");
			document.all.txtObjetivo.focus();
			return false;
		}	
		if (document.all.txtAlcance.value==''){
			alert("Debe capturar el campo ALCANCE del Proyecto de Auditoría.");
			document.all.txtAlcance.focus();
			return false;
		}			
		if (document.all.txtJustificacion.value==''){
			alert("Debe capturar el campo  JUSTIFICACIÓN del Proyecto de Auditoría.");
			document.all.txtJustificacion.focus();
			return false;
		}			
		if (document.all.txtPresupuesto.selectedIndex==0){
			alert("Debe seleccionar el TIPO DE PRESUPUESTO para el Proyecto de Auditoría.");
			document.all.txtPresupuesto.focus();
			return false;
		}	
		return true;		  
	}
	

	function actualizarSectorSubsectorByUnidad(sUnidad){

		var miSujeto = (document.all.txtUnidad.options[document.all.txtUnidad.selectedIndex].value).split("|");

		document.all.txtSector.value = miSujeto[0];
		document.all.txtSubsector.value = miSujeto[1];
	}

//----------------------------------------------------------------------------------------------------------------------------------------------
/// sección de funcionae especiales para el manejo de la tabla PARTIDAS
//----------------------------------------------------------------------------------------------------------------------------------------------

    function recuperarTablaGastoFromArreglo(lista, valor, tbl){
		var sTatus= "";

		var liga = '/' + lista + '/' + valor;

		//alert("Valor de la liga: " + liga);

        $.ajax({ type: 'GET', url: liga ,
          success: function(response) {
               var jsonData = JSON.parse(response);                                 
                              
               //Vacia la lista
               tbl.innerHTML="";

               //Agregar renglones
               var renglon, columna;

               	// Vaciar el arreglo
               	lstPartidas = [];

               for (var i = 0; i < jsonData.datos.length; i++) {
                 	var dato = jsonData.datos[i];                                                                    
                  	
                  	renglon=document.createElement("TR");    
 					
					sTatus= "";

					for (var j = 0; j < controlObjetos.length ; j++) {
						arrEle = controlObjetos[j];
						if (arrEle[2] == 'PARTIDA'){
							if (arrEle[1] == dato.idPartida + " - " + dato.nombre){
								sTatus= "checked=true";
								seleccionarPartida(dato.idPartida, dato.nombre, true);
								break;
							}
						}
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
			//miValor = document.all.txtCapitulo.options[document.all.txtCapitulo.selectedIndex].value;
			lstPartidas.push(new Array(partida, nombrePartida));
			//controlObjetos.push(new Array(0, partida + " - " + nombrePartida, 'PARTIDA',miValor,'EGRESO'));
		}else{
			for( i = 0; i < lstPartidas.length; i++)
			{
				if(partida==lstPartidas[i][0]){
					lstPartidas.splice(i, 1);
				}
				/*
				for( j = 0; j < controlObjetos.length; j++){
					arrEle = controlObjetos[j];
					if ( arrEle[2] == 'PARTIDA'){
						if(partida + " - " + nombrePartida == arrEle[1] ){
							controlObjetos.splice(j,1);
						}
					}

				}
				*/
			}
		}
	}

//----------------------------------------------------------------------------------------------------------------------------------------------
/// PARTIDAS
//----------------------------------------------------------------------------------------------------------------------------------------------


/// ********************************************************************************************************************************
/// *                                          SECCIÓN INICIAL (CARGA DE MÓDULO)                                                   *
///	********************************************************************************************************************************

		
	function inicializar() {
		var opciones = {zoom: nZoom,draggable: false,scrollwheel: true,	mapTypeId: google.maps.MapTypeId.ROADMAP};
		mapa = new google.maps.Map(document.getElementById('mapa_content'), { center: {lat: 19.4339249, lng: -99.1428964},zoom: nZoom});			
	}	
	
		window.onload = function () {
		var chart1; 
		
			setGrafica(chart1, "dpsProyectosByAreaAmplio", "pie", "Proyectos de Auditoría", "canvasJG" );
	
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
		var lstRegs;
		var rubros;

		// SE ALMACENAN LOS DATOS DE OBJETOS DE EGRESO, INGRESO Y CONSOLIDADO EN TXT ESPECIFICOS 
		
		// ***** OBJETOS DE EGRESOS
		
		d.txtLstObjetos.value = d.txtFuncion.options[d.txtFuncion.selectedIndex].value + '|' + d.txtFuncion.options[d.txtFuncion.selectedIndex].text + '*' + d.txtsubFuncion.options[d.txtsubFuncion.selectedIndex].value + '|' + d.txtsubFuncion.options[d.txtsubFuncion.selectedIndex].text  + '*' + d.txtActividad.options[d.txtActividad.selectedIndex].value + '|' + d.txtActividad.options[d.txtActividad.selectedIndex].text  + '*' + d.txtCapitulo.options[d.txtCapitulo.selectedIndex].value + '|' + d.txtCapitulo.options[d.txtCapitulo.selectedIndex].text;
		/*
		alert("El valor de todos los campos es: " + d.txtLstObjetos.value);


		rubros = d.txtFuncion.options[d.txtFuncion.selectedIndex].value + '-' + d.txtFuncion.options[d.txtFuncion.selectedIndex].text + '/' + d.txtsubFuncion.options[d.txtsubFuncion.selectedIndex].value + '-' + d.txtsubFuncion.options[d.txtsubFuncion.selectedIndex].text  + '/' + d.txtActividad.options[d.txtActividad.selectedIndex].value + '-' + d.txtActividad.options[d.txtActividad.selectedIndex].text  + '/' + d.txtCapitulo.options[d.txtCapitulo.selectedIndex].value + '-' + d.txtCapitulo.options[d.txtCapitulo.selectedIndex].text;

		*/
		rubros = "";
		rubros += ( (d.txtFinalidad.selectedIndex==0) ? "NULL" : d.txtFinalidad.options[d.txtFinalidad.selectedIndex].value) + "/" ; 
		rubros += ( (d.txtFuncion.selectedIndex==0) ? "NULL" : d.txtFuncion.options[d.txtFuncion.selectedIndex].value) + "/" ; 
		rubros += ( (d.txtsubFuncion.selectedIndex==0) ? "NULL" : d.txtsubFuncion.options[d.txtsubFuncion.selectedIndex].value) + "/" ; 
		rubros += ( (d.txtActividad.selectedIndex==0) ? "NULL" : d.txtActividad.options[d.txtActividad.selectedIndex].value) + "/" ; 
		rubros += ( (d.txtCapitulo.selectedIndex==0) ? "NULL" : d.txtCapitulo.options[d.txtCapitulo.selectedIndex].value) ; 

		//alert("El valor de rubros es: " + rubros);

		lstRegs = lstPartidas.length;

		if(lstRegs > 0){
			separador = "";
			sValor = "";
			for(i=0; i < lstPartidas.length; i++){
				sValor = sValor + separador + lstPartidas[i][0] + '|' + lstPartidas[i][1];
				separador = '*';
			}
		}
		//alert("sValor Partidas: " + sValor);
		document.all.txtLstPartidas.value = sValor;

	
		// ***** OBJETOS DE INGRESOS
		
		lstRegs = lstIngresos.length;
		//alert("Total de registros: " + lstRegs);

		sValor = "";
		if(lstRegs > 0){
			separador = "";
			for(i=0; i < lstIngresos.length; i++){
					sValor = sValor + separador + lstIngresos[i][0] + '|' + lstIngresos[i][1] + '|' + lstIngresos[i][2] + '|' + lstIngresos[i][3];
					separador = '*';
				}
		}
		//alert("sValor Ingresos: " + sValor);
		document.all.txtLstIngresos.value = sValor;

		
		// ***** OBJETOS DE CONSOLIDADOS

		lstRegs = lstConsolidados.length;
		//alert("Total de registros: " + lstRegs);

		sValor = "";
		if(lstRegs > 0){
			separador = "";
			for(i=0; i < lstConsolidados.length; i++){
					sValor = sValor + separador + lstConsolidados[i][0] + '|' + lstConsolidados[i][1] + '|' + lstConsolidados[i][2] + '|' + lstConsolidados[i][3];
					separador = '*';
				}
		}
		//alert("sValor Ingresos: " + sValor);
		document.all.txtLstConsolidados.value = sValor;


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
	                 	if(!(existeObjetoEnArreglo('',textoObj , 'FUNCIÓN',idObj, 'EGRESO')) ){
               		       	controlObjetos.push(new Array('',textoObj , 'FUNCIÓN',idObj, 'EGRESO', rubros) );
							llenarObjetosConArreglo(document.all.txtObjeto, "", textoObj);
							//asignarElemento_HVS(d.txtObjeto, idObj, textoObj, false, false);
							//seleccionarElemento(d.txtObjeto, idObj);
						}
					}
				}else{
					idObj = d.txtsubFuncion.options[d.txtsubFuncion.selectedIndex].value;
					textoObj = d.txtsubFuncion.options[d.txtsubFuncion.selectedIndex].text;
					//alert("En Subfuncion - > El valor de idObj: es " + idObj + " El valor de textoObj es: " + textoObj);
                 	if(!(existeObjetoEnArreglo('',textoObj , 'SUBFUNCIÓN',idObj, 'EGRESO')) ){
           		       	controlObjetos.push(new Array('',textoObj , 'SUBFUNCIÓN',idObj, 'EGRESO', rubros) );
						llenarObjetosConArreglo(document.all.txtObjeto, "", textoObj);
						//asignarElemento_HVS(d.txtObjeto, idObj, textoObj, false, false);
						//seleccionarElemento(d.txtObjeto, idObj);
					}
				}
			}else{
				idObj = d.txtActividad.options[d.txtActividad.selectedIndex].value;
				textoObj = d.txtActividad.options[d.txtActividad.selectedIndex].text;
				//alert("En Actividad - > El valor de idObj: es " + idObj + " El valor de textoObj es: " + textoObj);
               	if(!(existeObjetoEnArreglo('',textoObj , 'ACTIVIDAD',idObj, 'EGRESO')) ){
       		       	controlObjetos.push(new Array('',textoObj , 'ACTIVIDAD',idObj, 'EGRESO', rubros) );
					llenarObjetosConArreglo(document.all.txtObjeto, "", textoObj);
					//asignarElemento_HVS(d.txtObjeto, idObj, textoObj, false, false);
					//seleccionarElemento(d.txtObjeto, idObj);
				}
			}
		}else{
			if(document.all.txtLstPartidas.value == ""){
				idObj = d.txtCapitulo.options[d.txtCapitulo.selectedIndex].value;
				textoObj = d.txtCapitulo.options[d.txtCapitulo.selectedIndex].text;
				//alert(">En Capitulo - > El valor de idObj: es " + idObj + " El valor de textoObj es: " + textoObj);
               	if( !(existeObjetoEnArreglo('',textoObj , 'CAPITULO',idObj, 'EGRESO')) ){
       		       	controlObjetos.push(new Array('',textoObj , 'CAPITULO',idObj, 'EGRESO', rubros) );
					llenarObjetosConArreglo(document.all.txtObjeto, "", textoObj);
					//asignarElemento_HVS(d.txtObjeto, idObj, textoObj, false, false);
					//seleccionarElemento(d.txtObjeto, idObj);
				}
			}else{
				// Agregar las partidas al combobox
				actualizaPartidasEnArreglo(d.txtCapitulo.options[d.txtCapitulo.selectedIndex].value);
				/*
				for(i=0; i < lstPartidas.length; i++){
					idObj = lstPartidas[i][0];
					textoObj = lstPartidas[i][1];
	               	existeObjetoEnArreglo('',textoObj , 'PARTIDA', idObj, 'EGRESO');
					eliminaObjetoToArreglo(lstPartidas,'PARTIDA');
					llenarObjetosConArreglo(document.all.txtObjeto, "", textoObj);
					//asignarElemento_HVS(d.txtObjeto, lstPartidas[i][0], lstPartidas[i][0] + " - " + lstPartidas[i][1], false, false);
				}
				*/
			}
		}
		// Verifica cambios en las opciones de Ingresos
		if (lstIngresos.length > 0){
			/*
			// Primero: Quitar del combo de objetos los elementos que tenga de Ingresos.
			for(j=0; j < lstIngresosAll.length; j++){
				for(i=0; i < d.txtObjeto.options.length; i++){
					if ( d.txtObjeto[i].text == lstIngresosAll[j][1] ){
						d.txtObjeto[i] = null;
						break;
					}
				}
			}
			// Segundo: Ingresar los elementos ahora seleccionados de la tabla de Ingresos.
			for(i=0; i < lstIngresos.length; i++){
				asignarElemento_HVS(d.txtObjeto, lstIngresos[i][0], lstIngresos[i][0] + " - " + lstIngresos[i][1], false, false);
			}
			*/
			actualizaIngresosEnArreglo();
		}
		// Verifica cambios en las opciones de Consolidados
		//alert("lstConsolidados.length: " + lstConsolidados.length);
		if (lstConsolidados.length > 0){
			/*
			// Primero: Quitar del combo de objetos los elementos que tenga de Consolidados.
			for(j=0; j < lstConsolidadosAll.length; j++){
				for(i=0; i < d.txtObjeto.options.length; i++){
					if( d.txtObjeto[i].text == lstConsolidadosAll[j][1]) {
						d.txtObjeto[i] = null;
						break;
					}
				}	
			}
			// Segundo: Ingresar los elementos ahora seleccionados de la tabla de Consolidados.
			for(i=0; i < lstConsolidados.length; i++){
				asignarElemento_HVS(d.txtObjeto, lstConsolidados[i][0], lstConsolidados[i][1], false, false);
			}
			*/
			actualizaConsolidadosEnArreglo(document.all.txtConsolidado.options[document.all.txtConsolidado.selectedIndex].value);

		}
		ordenarListaByTexto(document.all.txtObjeto);
		$('#modalObjetos').modal('hide');			
	}


	function asignarElemento_HVS(cmb, valor, texto, borrar, agregarSeleccione){
		var option = document.createElement("option");

		if (borrar){
			//Vacia la lista
			while (cmb.length>=1){
				cmb.remove(cmb.length-1);
			}
				//Agregar elemento
			if (agregarSeleccione){
				option.text = "Seleccione...";
				option.value = '';
				cmb.add(option,0);
			}
		}

		if(valor!="" && texto!=""){
			option.text = texto;
			option.value = valor;
			cmb.add(option,1);		
			cmb.selectedIndex=1;
		}	
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

	function eliminarObjetoAsignado(sValor, sTexto){
		var auditoria;
		var liga;

		//alert("El valor de sValor es: " + sValor  );

		auditoria = document.all.txtAuditoria.value;

		if (auditoria==""){ auditoria = "SIN_DEFINIR"; }

		var liga = '/eliminarObjeto/' + sValor + '/' + auditoria;

		//alert("El valor de liga es: " + liga);

		$.ajax({
			type: 'GET', url: liga ,
			success: function(response) {
				alert("La eliminación del rubro se realizó de forma correcta.");				
				//--//recuperarListaLigada('lstObjetosByAuditoria', document.all.txtAuditoria.value, document.all.txtObjeto);
			},
			error: function(xhr, textStatus, error){
				alert('function eliminarObjetoAsignado()   -> statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error + ' Rubro: ' + sTexto);
			}			
		});		
	}


	function moverUnidadesBDaComboyArray(auditoria, cmb){

		liga = '/recuperaUnidadesByAuditoria/' +  auditoria;

		$.ajax({ type: 'GET', url: liga ,
          success: function(response) {
            	var jsonData = JSON.parse(response);                                 
               	lstUnidades = [];
                for (var i = 0; i < jsonData.datos.length; i++) {
                 	var dato = jsonData.datos[i];                                                                    

                	lstUnidades.push(new Array(dato.id, dato.texto) );
  			 	}
  			 	moverUnidadesSeleccionadasAtexto(cmb);
  			 }
		 });
	}


	function moverUnidadesSeleccionadasAtexto(cmb){
	    while (cmb.options.length > 0) {                
	        cmb.remove(0);
	    }      

		for (var i = 0; i < lstUnidades.length; i++) {
			var dato = lstUnidades[i];
			var option = document.createElement("option");
			option.value = dato[0];
			option.text = dato[1];
			cmb.add(option, i+1);
		}
		ordenarListaByTexto(cmb);
	}

	function validarBorrarUnidad(sAuditoria, sValor, sTexto){

		var liga = '/recuperaUnidadByAuditoria/' +  sAuditoria + '/' + sValor ;
		var liga2 = '/eliminarUnidadByAuditoria/' +  sAuditoria + '/' + sValor ;
		var confirmacion;

		$.ajax({ type: 'GET', url: liga ,
          success: function(response) {
				var jsonData = JSON.parse(response);
				var obj = JSON.parse(response);

               	if ( obj.fechaConfronta == null ){ 

					$.ajax({ type: 'GET', url: liga2 , success: function(response) { var obj = JSON.parse(response); } });
					
					for (var i = 0; i < lstUnidades.length; i++) {
						arrEle = lstUnidades[i];
						if(arrEle[0] == sValor){
							lstUnidades.splice(i,1);
							break;
						}
					}
               		//alert("Se quito la unidad de la auditoría correctamente.");
					moverUnidadesSeleccionadasAtexto(document.all.txtUnidades);


               	}else{
               		// confirmar el borrado del registro ya que el campo fConfronta tiene valor
					confirmacion = confirm("¿La unidad " + sTexto + "  tiene fecha de confronta definida\n\n¿Esta seguro que desea quitarla de la auditoria?" );
		
					if (confirmacion == true){ 	

						$.ajax({ type: 'GET', url: liga2 , success: function(response) { var obj = JSON.parse(response); } });

						for (var i = 0; i < lstUnidades.length; i++) {
							arrEle = lstUnidades[i];
		               		//alert(">arrEle[0]: " + arrEle[0] + " sValor: " + sValor);
							if(arrEle[0] == sValor){
								lstUnidades.splice(i,1);
								break;
							}
						}
	               		//alert("Se quito la unidad de la auditoría correctamente.");
						moverUnidadesSeleccionadasAtexto(document.all.txtUnidades);
               		}
               	}
  			 }
		 });
	}

	function SeleccionarCumplimiento(){
		
		var tipo = 'DE CUMPLIMIENTO';
		var liga = '/recuperaAuditriasTipo/' + tipo;

		alert("valor de liga: " + liga);
		
		$.ajax({ type: 'GET', url: liga ,
          success: function(response) {
				//var jsonData = JSON.parse(response);
				//var obj = JSON.parse(response);
  			 }
		 });
	}

	$(document).ready(function(){
		//setInterval(getMensaje('txtNoti'),60000);
		getMensaje('txtNoti',1);
		

		//alert("El valor de rolUnidadTecnica es: " + rolUnidadTecnica + " El valor de rolCaaaf es: " + rolCaaaf);

		/////document.getElementById("divElementoCriterio").style.display='none';
		document.all.lblConAsf.style.display='none';
		document.all.chkConAsf.style.display='none';

		recuperarLista('lstTiposAuditorias', document.all.txtTipoAuditoria);
		//recuperarLista('lstAreas', document.all.txtResponsable);
		//////recuperarLista('lstAreasResponsables', document.all.txtResponsable);
		recuperarLista('lstAreasResponsablesByArea', document.all.txtResponsable); ///// Modificado Por HVS el 03 de Feb del 2017

		//recuperarLista('lstSectores', document.all.txtSector);
		//alert("document.all.txtAuditoria.value: " + document.all.txtAuditoria.value);

		if(document.all.txtAuditoria.value == ''){
			//alert("Auditoria vacia ") ;
			//recuperarListaLigada('lstObjetosByAuditoria', 0, document.all.txtObjeto);
			controlObjetos = [];
		}else{
			//alert("Auditoria con valor ");
			//recuperarListaLigada('lstObjetosByAuditoria', document.all.txtAuditoria.value, document.all.txtObjeto);
			llenarArregloConObjetos(document.all.txtAuditoria.value, document.all.txtObjeto);
		}

		recuperarLista('lstFinalidadesByCuenta',document.all.txtFinalidad);

		///recuperalistaAuditoriasTotales('tblAuditorias',document.all.txtPrograma.value, document.all.tablaAuditoriasTotales);
		
		recuperarLista('lstEtapasByProceso',document.all.txtEtapa);
		recuperarLista('lstUnidadesRespByArea', document.all.txtUnidad);
		recuperarLista('lstUnidadesRespByArea', document.all.txtCentroGestor);

		recuperarLista('lstTiposConsolidados', document.all.txtTiposConsolidado );

		recuperarLista('lstUnidades_HVS', document.all.txtUnidadAdmin);

 		$("#divSectorSubsector").hide();
 		$("#divCentroGestor").hide();
 		$("#divSujeto").hide();


		// ************************************* PARA MODAL DE OBJETOS ****************************************************
		//alert('Antes de cargar las unidades');
		// ****************************************************************************************************************

		//$("#tblGasto").hide();

		$( "#btnGuardarAut" ).click(function() {
			//////apagarBotones();

			/////asignarEtapa(document.all.txtAuditoria.value, sProcesoNuevo, sEtapaNueva);
			
		   $('#modalAutorizar').modal('hide');			


		});

		// Botón que guarda la auditoría
		$( "#btnGuardar" ).click(function() {

			document.getElementById("txtEtapa").disabled=false;

			if (validarAuditoria()){
				// Inicio del poceso para poder comunicar el contenido del arreglo controlObjetos a el proceso de submit.
				sValoresObjetos = "";
				if(controlObjetos.length > 0){
					separador = "";
					for(i=0; i < controlObjetos.length; i++){
						sValoresObjetos = sValoresObjetos + separador + controlObjetos[i][0] + '|' + controlObjetos[i][1] + '|' + controlObjetos[i][2] + '|' + controlObjetos[i][3] + '|' + controlObjetos[i][4] + '|' + controlObjetos[i][5];
							separador = '*';
						}
				}
				//alert("sValoresObjetos controlObjetos: " + sValoresObjetos);
				document.all.txtControlObjetos.value = sValoresObjetos;
				//alert("document.all.txtControlObjetos.value: " + document.all.txtControlObjetos.value);
				// Proceso para alamcenar los valores de las unidades seleccionadas 
				// Fin del proceso para podern comunicar el contenido del areglo controlObjetros a el proceso de submit.

				sValoresUnidades = "";
				if(lstUnidades.length > 0){
					separador = "";
					for(i=0; i < lstUnidades.length; i++){
						sValoresUnidades = sValoresUnidades + separador + lstUnidades[i][0] + '|' + lstUnidades[i][1];
							separador = '*';
						}

				}
				document.all.txtUnidadesSeleccionadas.value = sValoresUnidades;
				//alert("document.all.txtUnidadesSeleccionadas.value: " + document.all.txtUnidadesSeleccionadas.value);

				if (!rolCaaaf || !rolUnidadTecnica){ document.getElementById("btnGuardar").disabled=true; }
				document.all.formulario.submit();
				document.getElementById("txtEtapa").disabled=true;
				}
			}
		);


		$( "#btnCancelar" ).click(function() {
			document.all.listasAuditoria.style.display='inline';
			document.all.capturaAuditoria.style.display='none';
		});
		

		$( "#btnQuitarObjeto" ).click(function() {		
			var objValor;
			var objTexto;
			var confirmacion;

			if(document.all.txtObjeto.selectedIndex >=0){
				
				objValor = document.all.txtObjeto.options[document.all.txtObjeto.selectedIndex].value;	
				objTexto = document.all.txtObjeto.options[document.all.txtObjeto.selectedIndex].text;	
				confirmacion = confirm("¿Esta seguro que desea eliminar el siguiente rubro?:\n \n" + objTexto);
				
				if (confirmacion == true){ 	

					/*
					if (document.all.txtAuditoria.value==""){
						eliminarElemento(document.all.txtObjeto,objValor,0);  // CMB
					}else{
						eliminarObjetoAsignado(objValor, objTexto); // BD
					}
					*/

					for (var i = 0; i < controlObjetos.length; i++) {
						arrEle = controlObjetos[i];
						if(arrEle[1] == objTexto){
							controlObjetos.splice(i,1);
							break;
						}
					}
				}
				llenarObjetosConArreglo(document.all.txtObjeto, "", objTexto);
				ordenarListaByTexto(document.all.txtObjeto);
			}
			
		});	


		$( "#btnVerObjeto" ).click(function() {		
			//var nEntidadSeleccionada= document.all.txtUnidad.options[document.all.txtUnidad.selectedIndex].value;
			var sector;
			var subsector;
			var sectorSubsectorUnidad;
			var miAuditoria;
			var miSujeto = (document.all.txtUnidad.options[document.all.txtUnidad.selectedIndex].value).split("|");
			
			document.all.txtSector.value = miSujeto[0];
			document.all.txtSubsector.value = miSujeto[1];

			sector = document.all.txtSector.value;
			subsector = document.all.txtSubsector.value;

			sectorSubsectorUnidad = document.all.txtUnidad.options[document.all.txtUnidad.selectedIndex].value;

			limpiarCamposObjeto();
			seleccionarElemento(document.all.txtCentroGestor, sectorSubsectorUnidad);

			var liga = 'lstCapituloByCuenta';			
			recuperarLista(liga, document.all.txtCapitulo);

			miAuditoria = document.all.txtAuditoria.value;

			if (miAuditoria==""){ miAuditoria = "SIN_DEFINIR"; }

			recuperarTablaIngresosFromArreglo('tblIngresosByAuditoria', "SIN_DEFINIR", document.all.tblIngreso );

			$('#modalObjetos').removeClass("invisible");
			$('#modalObjetos').modal('toggle');
			$('#modalObjetos').modal('show');

		});


		$( "#btnQuitarUnidades" ).click(function() {		
			var posElemento = -1;
			var objValor;
			var objTexto;
			var confirmacion;


			if(document.all.txtUnidades.selectedIndex >=0){
				
				objValor = document.all.txtUnidades.options[document.all.txtUnidades.selectedIndex].value;	
				objTexto = document.all.txtUnidades.options[document.all.txtUnidades.selectedIndex].text;	
				confirmacion = confirm("¿Esta seguro que desea eliminar el siguiente sujeto?:\n \n" + objTexto);
				
				if (confirmacion == true){ 	
					// Validará si existe un registro almacenado con los datos de la auditoría, si es así checara que las fechas de confronta
					// esten vácias solo así se podrá elimiar, no solo del combo (visualmente) sino también de la base de datos

					if( document.all.txtAuditoria.value.length == 0 ){
						for (var i = 0; i < lstUnidades.length; i++) {
							arrEle = lstUnidades[i];
							if(arrEle[1] == objTexto){
								lstUnidades.splice(i,1);
								break;
							}
							moverUnidadesSeleccionadasAtexto(document.all.txtUnidades);
						}

					}else{

						validarBorrarUnidad(document.all.txtAuditoria.value, objValor, objTexto); 
					}
				}
			}
			
		});	

		$( "#btnVerUnidades" ).click(function() {		
			//var nEntidadSeleccionada= document.all.txtUnidad.options[document.all.txtUnidad.selectedIndex].value;
			var sector;
			var subsector;
			var sectorSubsectorUnidad;
			var miAuditoria;
			var miSujeto = (document.all.txtUnidad.options[document.all.txtUnidad.selectedIndex].value).split("|");
			

			document.all.txtSector.value = miSujeto[0];
			document.all.txtSubsector.value = miSujeto[1];

			sector = document.all.txtSector.value;
			subsector = document.all.txtSubsector.value;

			sectorSubsectorUnidad = document.all.txtUnidad.options[document.all.txtUnidad.selectedIndex].value;

			//limpiarCamposObjeto();
			//seleccionarElemento(document.all.txtCentroGestor, sectorSubsectorUnidad);

			//var liga = 'lstCapituloByCuenta';			
			//recuperarLista(liga, document.all.txtCapitulo);

			miAuditoria = document.all.txtAuditoria.value;

			if (miAuditoria==""){ miAuditoria = "SIN_DEFINIR"; }

			//alert("Antes de Entrar..");
			recuperarTablaUnidadesFromArreglo('tblUnidadesByAuditoria', miAuditoria, document.all.tblUnidades);

			$('#modalUnidades').removeClass("invisible");
			$('#modalUnidades').modal('toggle');
			$('#modalUnidades').modal('show');

		});


		$( "#btnCancelarObjeto" ).click(function() {
			$('#modalObjetos').modal('hide');			
		});		

		$( "#btnCancelarUnidad" ).click(function() {
			$('#modalUnidades').modal('hide');			
		});		

		$( "#txtObjetivoEdit" ).click(function(){
			//alert("Texto deobjetivos" + document.all.txtObjetivo.value);
			document.all.txtTextoAmplio.value = document.all.txtObjetivo.value;
			$('#modalTextoLargo').modal('show');
			sEditando="O";
		});	
		
		$( "#txtAlcanceEdit" ).click(function(){
			document.all.txtTextoAmplio.value = document.all.txtAlcance.value;
			$('#modalTextoLargo').modal('show');
			sEditando="A";
		});		

		$( "#txtJustificacionEdit" ).click(function(){
			document.all.txtTextoAmplio.value = document.all.txtJustificacion.value;
			$('#modalTextoLargo').modal('show');
			sEditando="J";
		});				
		
		$( "#btnCancelarTexto" ).click(function() {
				$('#modalTextoLargo').modal('hide');				
		});				

		$( "#btnGuardarTexto" ).click(function() {
				$('#modalTextoLargo').modal('hide');				
				if(sEditando=="O") document.all.txtObjetivo.value=document.all.txtTextoAmplio.value;
				if(sEditando=="A") document.all.txtAlcance.value=document.all.txtTextoAmplio.value;
				if(sEditando=="J") document.all.txtJustificacion.value=document.all.txtTextoAmplio.value;
		});		


		$( "#btnGuardarObjeto" ).click(function() {
			guardarObjetosSeleccionados();
		});		

		$( "#btnGuardarUnidad" ).click(function() {
			moverUnidadesSeleccionadasAtexto(document.all.txtUnidades);
			$('#modalUnidades').modal('hide');			

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
	

<!-- Main content starts -->

<div class="content">
  	<div class="panel panel-default">
		<div class="panel-body">
			<div class="row" id="listasAuditoria">
				<div class="col-md-3">
					<div class="widget">
						<div class="widget-head">
							<div class="clearfix"> 
							  <div class="pull-left"><h3><i class="fa fa-bars"></i> Resumen </h3></div>
							</div>
						</div>
						<div class="widget-content">
							<div class="clearfix">
								<div class="col-md-12">
									<div id="canvasJG"></div>
									<hr>
									<table class="table table-striped table-bordered table-hover table-condensed">
										<thead style="width: 100%; font-size: xx-small;">
											<tr>
												<th style="text-align:center;">AUDITORIAS</th><th style="text-align:center;">CANTIDAD</th>
											</tr>
										</thead>	
										<tbody id="tblcanvasJG" style="text-align:center; font-size: xx-small;">	
										<!-- <tbody id="tablaAuditoriasTotales" style="text-align:center; font-size: xx-small;">-->

										</tbody>
									</table>
								</div>	
							</div>
						</div>
					</div>				
				</div>				
				<div class="col-md-9">
					<div class="widget">
						<div class="widget-head">
							<h3><i class="fa fa-search"></i> Lista de Proyectos de Auditoría.</h3>
							<div class="clearfix"></div>
						</div>             
						<div class="widget-content">
							<div class="table-responsive" style="height: 400px; overflow: auto; overflow-x:hidden;">
								<table class="table table-striped table-bordered table-hover table-condensed">
									<thead>
										<tr><th>Cve. Proyecto</th><th>En Etapa</th><th>Dirección General</th><th>Sujeto de Fiscalización</th><th>Rubro(s) de Fiscalización</th><th>Tipo de Auditoría</th></tr>
									</thead>
									<tbody>																			
										<?php foreach($datos as $key => $valor): ?>
											<tr onclick=<?php echo "javascript:recupera_Auditoria('" . $valor['auditoria'] . "');"; ?> style="width: 100%; font-size: xx-small">										  										  
												<!--  <td><?php echo $valor['auditoria']; ?></td> ##--> 
												<td width="5%"><?php echo $valor['claveAuditoria']; ?></td>
												<td width="10%"><?php echo $valor['etapa']; ?></td>
												<td width="20%"><?php echo $valor['area']; ?></td>
												<td width="30%"><?php echo $valor['sujeto']; ?></td>
												<td width="30%"><?php echo $valor['objeto']; ?></td>
												<td width="10%"><?php echo $valor['tipo']; ?></td>
											</tr>
										<?php endforeach; ?>	
									</tbody>
								</table>
							</div>
						</div>
						
						<div class="widget-foot">

							<div class="pull-left">
								<button onclick="SeleccionarCumplimiento();" type="button" class="btn btn-primary  btn-xs" name="btnSelAudiCump" id="btnSelAudiCump" ><i class="fa fa-file-text-o"></i> Recuperar Auditorías de Cumplimiento...</button>


								<button onclick="agregarAuditoria();" type="button" class="btn btn-primary  btn-xs" name="btnAgregarAudi" id="btnAgregarAudi" style="display:none;"><i class="fa fa-file-text-o"></i> Agregar Proyecto de Auditoría...</button>
								<button type="button" class="btn btn-warning active  btn-xs" name="btnENVIADA" id="btnENVIADA" style="display:none;"><i class="fa fa-check-square-o"></i> Enviar proyectos a Unidad Técnica </button>
								<button type="button" class="btn btn-warning active btn-xs" name= id="btnVALIDACION" id="btnVALIDACION" style="display:none;"><i class="fa fa-check-square-o"></i> Valida/Autoriza proyectos</button>
								<button type="button" class="btn btn-warning active  btn-xs" name="btnINTEGRACION" id="btnINTEGRACION" style="display:none;"><i class="fa fa-check-square-o"></i> Integrar proyectos a PGA</button>
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
					<input type='HIDDEN' name='txtLstIngresos' value=''>	
					<input type='HIDDEN' name='txtLstConsolidados' value=''>									
					<input type='HIDDEN' name='txtControlObjetos' value=''>									
					<input type='HIDDEN' name='txtUnidadesSeleccionadas' value=''>									

					<div class="col-md-12">				
						<div class="widget">
							<!-- Widget head -->
							<div class="widget-head">
								<div class="pull-left"><h3><i class="fa fa-pencil-square-o"> </i> Registro de Auditoria para Jurídico</h3></div>
								<div class="widget-icons pull-right">
									<!-- <button type="button" id="btnCriterios" class="btn btn-primary  btn-xs ">Asignación de Criterios</button>  -->
									<button type="button"  id="btnGuardar" class="btn btn-primary  btn-xs"><i class="fa fa-floppy-o"></i> Guardar</button>
									<button type="button" id="btnCancelar" class="btn btn-default  btn-xs"><i class="fa fa-undo"></i> Regresar</button> 
						  		</div>  
								<div class="clearfix"></div>
							</div>              

							<!-- Widget content -->
							<div class="widget-content">												
								<div class="col-xs-8">															
									<div class="form-group">									
										<div class="form-group" id="divAuditoria">									
											<label class="col-xs-2 control-label" id="lblNoAuditoria">No. Proyecto</label>
											<div class="col-xs-1"><input type="text" class="form-control" name="txtAuditoria" readonly/></div>

											<label class="col-xs-2 control-label" id="lblCveAuditoria">Clave Auditoría</label>
											<div class="col-xs-1"><input type="text" class="form-control" name="txtClaveAuditoria" /></div>													
										</div>								
										<div class="form-group">									
											<label class="col-xs-2 control-label">Tipo</label>
											<div class="col-xs-4">
												<select class="form-control" name="txtTipoAuditoria" id="txtTipoAuditoria">
													<option value="">Seleccione...</option>
												</select>
											</div>
										</div>	
									</div>								
									<div class="clearfix"></div>
									<br>

									<div class="form-group">
										<label class="col-xs-2 control-label">Dirección</label>
										<div class="col-xs-10">
											<select class="form-control" name="txtResponsable" id="txtResponsable">
												<option value="">Seleccione...</option>											
											</select>
										</div>
									</div>
									<br>
									
									<div class="form-group" id="divSectorSubsector">
										<label class="col-xs-2 control-label">Sector</label> 
											<div class="col-xs-4"> 
												<input type='text' name='txtSector' id='txtSector' value='' />									
												<!-- 
												<select class="form-control" name="txtSector" onChange="javascript:recuperarListaLigada('lstSubsectoresBySector', this.value, document.all.txtSubsector);">
													<option value="">Seleccione...</option>											
												</select>
												-->
											</div>	

											<label class="col-xs-2 control-label">Subsector</label> 
											<div class="col-xs-4"> 
												<input type='text' name='txtSubsector' id= name='txtSubsector' value='' />									
												<!-- 
												<select class="form-control" name="txtSubsector" id="txtSubsector">
													<option value="">Seleccione...</option>											
												</select>
												-->
											</div>
									</div>									
									<div class="clearfix"></div>

									<div class="form-group" id="divSujeto">
										<label class="col-xs-2 control-label " >Sujeto</label>
										<div class="col-xs-10">
											<select class="form-control" name="txtUnidad" id="txtUnidad" onChange="javascript:actualizarSectorSubsectorByUnidad(this.value);">
												<option value="">Seleccione...</option>											
											</select>
										</div>								
									</div>
									<br>
									
									<div class="form-group">
										<label class="col-xs-2 control-label" style=" margin: -2% 0 1% !important;">Sujetos(s)</label>
										<div class="col-xs-9">
											<select class="form-control" name="txtUnidades" id="txtUnidades" size=4 style=" margin: -3% 0 1% !important;">
												<!-- <option value="">Seleccione...</option> -->
											</select>										
										</div>	
										<div class="col-xs-1" style=" margin: -2% 0 1% !important;">
											<button  type="button" class="btn btn-default btn-xs" id="btnVerUnidades" style=" margin: 0% 0 6% !important;"> Agregar</button>
											<button  type="button" class="btn btn-default btn-xs" id="btnQuitarUnidades"> Quitar </button>				
										</div>
									</div>								

									<div class="form-group">
										<label class="col-xs-2 control-label">Rubro(s)</label>
										<div class="col-xs-9">
											<select class="form-control" name="txtObjeto" id="txtObjeto" size=4 style=" margin: 0 0 1% 0 !important;">
												<!-- <option value="">Seleccione...</option> -->
											</select>										
										</div>	
										<div class="col-xs-1">
											<button  type="button" class="btn btn-default btn-xs" id="btnVerObjeto" style=" margin: 0% 0 6% !important;"> Agregar</button>
											<button  type="button" class="btn btn-default btn-xs" id="btnQuitarObjeto"> Quitar </button>				
										</div>
									</div>								
									<br>	

									<div class="form-group">
										<label class="col-xs-2 control-label">Objetivo(s) <i class="fa fa-pencil"  id="txtObjetivoEdit"></i></label>
										<div class="col-xs-10"><textarea class="form-control" rows="3" name="txtObjetivo" id="txtObjetivo" style=" margin: 0 0 1% 0 !important;" ></textarea></div>
									</div>
									<br>	

									<div class="form-group">
										<label class="col-xs-2 control-label">Alcance(s) <i class="fa fa-pencil"  id="txtAlcanceEdit"></i></label>
										<div class="col-xs-10"><textarea class="form-control" rows="3" name="txtAlcance" id="txtAlcance" style=" margin: 0 0 1% 0 !important;"></textarea></div>
									</div>	
									<br>	

									<div class="form-group">
										<label class="col-xs-2 control-label">Justificación <i class="fa fa-pencil"  id="txtJustificacionEdit"></i></label>
										<div class="col-xs-10"><textarea class="form-control" rows="3" name="txtJustificacion" id="txtJustificacion" style=" margin: 0 0 1% 0 !important;"></textarea></div>
									</div>
									<br>	

									<div class="form-group">									
										<label class="col-xs-2">Tipo de Presupuesto</label>
										<div class="col-xs-3">
											<select class="form-control" name="txtPresupuesto" id="txtPresupuesto">
												<option value="">Seleccione...</option>
												<option value="LOCAL" selected>LOCAL</option>
												<option value="FEDERAL">FEDERAL</option>
												<option value="LOCFED">LOCAL Y FEDERAL</option>
											</select>
										</div>
										<!--
										<label class="col-xs-3"> Acompañamiento con la ASF </label>
										-->
										<div class="checkbox col-xs-1 " style="text-align:left; margin-top: 0px" id="lblConAsf">
												<input type="checkbox" name="chkConAsf" Id="chkConAsf">
										</div>

										<label class="col-xs-2 control-label">Etapa</label>
										<div class="col-xs-5">
											<select class="form-control" name="txtEtapa" id="txtEtapa" disabled="true" >
												<!--
												<option value="">Seleccione...</option>											
												-->
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
									</div>
							</div>
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

			<!-- Encabezado de la pantalla modal -->

			<div class="modal-content">									
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h3 class="modal-title"><i class="fa fa-home"></i> Rubro(s) de Fiscalización...</h3>
				</div>					

				<!-- Cuerpo de la pantalla modal -->

				<div class="modal-body">

					<form id="formularioObjetos" METHOD='POST' ACTION='/guardar/auditoriaObjetos' role="form">

						<!-- Definición de sección para el manejo de Tabs  -->
						<ul class="nav nav-tabs">																		
							<li class="active"><a href="#tab-egresos" data-toggle="tab"> Egresos <i class="fa"></i></a></li>
							<li><a href="#tab-ingresos" data-toggle="tab"> Ingresos <i class="fa"></i></a></li>
							<li><a href="#tab-consolidados" data-toggle="tab"> Consolidados <i class="fa"></i></a></li>
						</ul>								

						<div class="tab-content">

							<div class="tab-pane active" id="tab-egresos" >

								<!-- *************************************** CENTRO GESTOR  **********************************************-->
								<div class="form-group" id="divCentroGestor" >						
									<label class="col-xs-2 control-label">Centro gestor</label>
									<div class="col-xs-10">
										<!-- <select class="form-control" name="txtCentroGestor" onChange="javascript: recuperaFinalidades(this.value);"> -->
										<select class="form-control" style= "visible:false" name="txtCentroGestor">
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
								<br>
								<div class="form-group">						
									<label class="col-xs-2 control-label">Capítulo</label>
									<div class="col-xs-10">
										<select class="form-control" name="txtCapitulo" onChange="javascript: recuperaPartidas(this.value);">
											<option value="">Seleccione...</option>
										</select>
									</div>
								</div>

								<!-- ********************** ÁREA DE GRID VARIOS CAMPOS DEL OBJETO DE FISCALIZACIÓN ********************* -->
								<div class="table-responsive col-xs-12" style="height: 300px; overflow: auto; overflow-x:hidden;">	
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

							</div> 
							<div class="clearfix"></div>

							<div class="tab-pane" id="tab-ingresos">

								<!-- ************************ ÁREA DE GRID INGRESOS ********************* -->

								<div class="table-responsive col-xs-12" style="height: 300px; overflow: auto; overflow-x:hidden;">	
									<br>
									<table class="table table-striped table-bordered table-hover">
										<!-- <caption>Objetos de Fiscalización (INGRESO).</caption> -->
										<thead>
											<tr><th width="3%">Sel.</th><th width="77%">Nombre</th><th width="10%">Tipo</th><th width="10%">Origen</th></tr>
										</thead>
										<tbody id="tblIngreso">
										</tbody>
									</table>
								</div>
							</div>

							<div class="tab-pane" id="tab-consolidados">

								<div class="form-group">						
									<!-- ************************ TIPO CONSOLIDADO (CONCEPTO PADRE)  *********************************-->
									<label class="col-xs-2 control-label">Tipo Consolidado</label>
									<div class="col-xs-4">
										<!-- <select class="form-control" name="txtCentroGestor" onChange="javascript: recuperaFinalidades(this.value);"> -->
										<select class="form-control" name="txtTiposConsolidado" onChange="javascript: recuperaConsolidados(this.value);">
											<option value="">Seleccione...</option>
										</select>
									</div>
									<!-- ***************************** CONSOLIDADO (CONCEPTO HIJO)  ************************************-->
									<label class="col-xs-1 control-label">Consolidados</label>
									<div class="col-xs-5">
										<select class="form-control" name="txtConsolidado" onChange="javascript: recuperaLstConsolidados(this.value);">
											<option value="">Seleccione...</option>
										</select>
									</div>
								</div>
								<div class="clearfix"></div>
								<br>
								<!-- ************************* CONSOLIDADOS DETALLE (ÁREA DE GRID, CONCEPTOS NIETOS)  ********************** -->

								<div class="table-responsive col-xs-12" style="height: 300px; overflow: auto; overflow-x:hidden;">	
									<br>
									<table class="table table-striped table-bordered table-hover">
										<caption>INFORMACION CONSOLIDADA.</caption> 
										<thead>
											<tr><th width="3%">Sel.</th><th width="50%">Rubro</th><th width="15%">Importe</th></tr>
										</thead>
										<tbody id="tblConsolidados">
										</tbody>
									</table>
								</div>
							</div>
						</div> 
					</form>
				</div>				
				<div class="clearfix"></div>

				<!-- Sección de pie de pagina de la pantalla modal -->
				<div class="modal-footer">
					<button  type="button" class="btn btn-primary active" id="btnGuardarObjeto" style="display:inline;"><i class="fa fa-floppy-o"></i> Asignar Rubro(s)</button>	
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
								<div class="col-xs-20"><textarea class="form-control" rows="20" placeholder="Capture aqui" id="txtTextoAmplio" name="txtTextoAmplio"></textarea></div>
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
	        				<div class="col-xs-12"><textarea class="form-control" rows="5" placeholder="Notas..." id="txtTextoLargoAutorizar" name="txtTextoLargoAutorizar"></textarea></div>
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
	</div>

	<div id="modalUnidades" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<input type='HIDDEN' name='txtUnidadNueva' value=''>									

			<!-- Modal content-->

			<!-- Encabezado de la pantalla modal -->

			<div class="modal-content">									
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h3 class="modal-title"><i class="fa fa-home"></i> Sujetos de Auditoria...</h3>
				</div>					

				<!-- Cuerpo de la pantalla modal -->

				<div class="modal-body">

					<form id="formularioUnidades" METHOD='POST' ACTION='/guardar/auditoriaUnidades' role="form">
						<!-- ************************ ÁREA DE GRID DE ARAS o SUJETOS ********************* -->

						<div class="table-responsive col-xs-12" style="height: 300px; overflow: auto; overflow-x:hidden;">	
							<br>
							<table class="table table-striped table-bordered table-hover">
								<!-- <caption>Objetos de Fiscalización (INGRESO).</caption> -->
								<thead>
									<tr><th width="3%">Sel.</th><th width="90%">Sujeto</th></tr>
								</thead>
								<tbody id="tblUnidades">
								</tbody>
							</table>
						</div>

					</form>
				</div>				
				<div class="clearfix"></div>

				<!-- Sección de pie de pagina de la pantalla modal -->
				<div class="modal-footer">
					<button  type="button" class="btn btn-primary active" id="btnGuardarUnidad" style="display:inline;"><i class="fa fa-floppy-o"></i> Asignar Sujeto(s)</button>	
					<button  type="button" class="btn btn-default" id="btnCancelarUnidad" 		style="display:inline;"><i class="fa fa-undo"></i> Cancelar</button>	
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