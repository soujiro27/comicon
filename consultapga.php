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
			#modalComentarios .modal-dialog  {width:70%;}
			#modalAutorizar .modal-dialog  {width:30%;}
			#modalUnidades .modal-dialog  {width:70%;}
			#modalGastoCuentaPublica .modal-dialog  {width:75%;}

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

    function recuperarTablaCriterios(lista, valor, tbl){
    		//alert('/'+ lista + '/' + valor);
            $.ajax({
	           type: 'GET', url: '/'+ lista + '/' + valor ,
	           success: function(response) {
                   var jsonData = JSON.parse(response);                                 
                                  
                   //Vacia la lista
                   tbl.innerHTML="";

                   //Agregar renglones
                   var renglon, columna;
                   
                   for (var i = 0; i < jsonData.datos.length; i++) {
                      var dato = jsonData.datos[i];                                                                    
                      renglon=document.createElement("TR");     
                      renglon.innerHTML="<td>" + dato.criterio + "</td><td>" + dato.nombre + "</td>";
                      renglon.onclick= function() 
                      {
						document.all.txtOperacionCriterio.value = 'UPD';
						var miTipoAuditoria = document.all.txtTipoAuditoria.options[document.all.txtTipoAuditoria.selectedIndex].value;
						var miAuditoria = document.all.txtAuditoria.value;
						var miCriterio = dato.criterio;
						document.all.txtCriterioAnterior.value = dato.criterio;
						
						$('#modalCriterios').removeClass("invisible");
						$('#modalCriterios').modal('toggle');
						$('#modalCriterios').modal('show');
						recuperarListaLigada('lstCriteriosByTipoAuditoria', miTipoAuditoria, document.all.txtCriterio);
           				recuperaAuditoriaCriterio(miAuditoria, miTipoAuditoria, miCriterio);
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
                   
                   //Vacia la lista
                   tbl.innerHTML="";

                   //Agregar renglones
                   var renglon, columna;
                   
                   // Limpia el Arreglo de partidas
                   //lstPartidas = [];

                    for (var i = 0; i < jsonData.datos.length; i++) {
                      var dato = jsonData.datos[i];                                                                    
                      renglon=document.createElement("TR");     
                      renglon.innerHTML="<td>" + dato.numeroDocto + "</td><td>" + dato.tipo + "</td><td>" + dato.flujodocto + "</td><td>" + dato.fDocto + "</td><td>" + dato.idPrioridad + "</td><td>" + dato.idImpacto + "</td><td><a href='/documentos/" + dato.archivoFinal + "'><img src='img/xls.gif'></td>";

                      //<td><img onclick="modalWin('mostrarPDF.php');" src="img/xls.gif"></td>
                      renglon.onclick= function() 
                      {
                      	//<td><img onclick="modalWin('mostrarPDF.php');" src="img/xls.gif"></td>
                      	//alert(dato.archivoOriginal);
                      	//modalWin("/documentos/" + dato.archivoFinal);
                      };
                      tbl.appendChild(renglon);                                                                       
                   }                                                             
	           },
	           error: function(xhr, textStatus, error){
	                alert('function recuperarTablaArchivos ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
	           }                                             
		});           
     }
     /*
	function recuperarTablaGastoCtaPublica(lista, valores, tbl){

		var liga = '/recuperaObjetosByAuditoria/' +  valores;

		$.ajax({ type: 'GET', url: liga ,
            success: function(response) {
             	var jsonData = JSON.parse(response);                                 
               
                //Vacia la lista
                tbl.innerHTML="";

                //Agregar renglones
                var renglon, columna;

                for (var i = 0; i < jsonData.datos.length; i++) {
                 	var dato = jsonData.datos[i];                                                                    
       				var option = document.createElement("option");
                 	//alert("Id: " + dato.id + " Texto: " + dato.texto + " Nivel: " + dato.nivel + " Valor: " + dato.valor + " TipoObjeto: " + dato.tipoObjeto );

                	renglon=document.createElement("TR");     
                	renglon.innerHTML="<td>" + dato.id + "</td><td>" + dato.texto + "</td><td>" + dato.nivel + "</td><td>" + dato.valor + "</td><td>" + dato.tipoObjeto + "</td><td>" + dato.rubros + "</td>";

                	renglon.onclick= function()  {   };
                	tbl.appendChild(renglon);                                                                       
  			 	}
  			},
           	error: function(xhr, textStatus, error){
                alert('function recuperarTablaArchivos ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
           }                                             

		 });

		/*
		var liga = '/' + 'lstRubrosGastosCP' + '/' + valores;
			//	alert(liga);

        $.ajax({
           type: 'GET', url: liga,
           success: function(response) {
               var jsonData = JSON.parse(response);                                 
               
               //Vacia la lista
               tbl.innerHTML="";

               //Agregar renglones
               var renglon, columna;
               
               // Limpia el Arreglo de partidas
               //lstPartidas = [];

                for (var i = 0; i < jsonData.datos.length; i++) {
                  var dato = jsonData.datos[i];                                                                    
                  renglon=document.createElement("TR");     
                  renglon.innerHTML="<td>" + dato.numeroDocto + "</td><td>" + dato.tipo + "</td><td>" + dato.flujodocto + "</td><td>" + dato.fDocto + "</td><td>" + dato.idPrioridad + "</td><td>" + dato.idImpacto + "</td><td><a href='/documentos/" + dato.archivoFinal + "'><img src='img/xls.gif'></td>";

                  //<td><img onclick="modalWin('mostrarPDF.php');" src="img/xls.gif"></td>
                  renglon.onclick= function() 
                  {
                  	//<td><img onclick="modalWin('mostrarPDF.php');" src="img/xls.gif"></td>
                  	//alert(dato.archivoOriginal);
                  	//modalWin("/documentos/" + dato.archivoFinal);
                  };
                  tbl.appendChild(renglon);                                                                       
               }                                                             
           },
           error: function(xhr, textStatus, error){
                alert('function recuperarTablaArchivos ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
           }                                             
		});           
		*/
     //}


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

	function recuperarTablaComentarios(lista, valor, tbl){
    	//alert('/'+ lista + '/' + valor);
        $.ajax({ type: 'GET', url: '/'+ lista + '/' + valor + '/PGA' ,
           	success: function(response) {
	            var jsonData = JSON.parse(response);                                 
	                                  
	            //Vacia la lista
	            tbl.innerHTML="";

	            //Agregar renglones
	            var renglon, columna;
	                   
				for (var i = 0; i < jsonData.datos.length; i++) {
					var dato = jsonData.datos[i];                                                                    
					renglon=document.createElement("TR");     
					renglon.innerHTML="<td>" + dato.fechayhora + "</td><td>" + dato.usuario + "</td><td>" + dato.comentario + "</td>";
					renglon.onclick= function() 
				  	{

						//document.all.txtComentarioAuditoria.value = dato.comentario;
						//document.all.txtComentarioAuditoria.disabled = true;
						//document.getElementById("btnGuardarComentario").style.display="none";
					
				  	};
					  	tbl.appendChild(renglon);                                                                       
				}                                                             
           	},
           	error: function(xhr, textStatus, error){
                alert('function recuperarTablaComentarios ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
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

	function guardarComentario(){
		//sValor = document.all.txtDelegacionFiltro.options[document.all.txtDelegacionFiltro.selectedIndex].value;		
		var sOper=document.all.txtOperacionComentarios.value;
		var miIdAuditoriaComentario;
		var miComentario = document.all.txtComentarioAuditoria.value;
		var miAuditoria = document.all.txtAuditoria.value;

		if( document.all.txtIdAuditoriaComentario.value.trim() == "") {
			miIdAuditoriaComentario = 0;
		}else{
			miIdAuditoriaComentario = document.all.txtIdAuditoriaComentario.value;
		}

		alert("Los valores a guardar son: sOper=>*" + sOper + "* miIdAuditoriaComentario=>*" + miIdAuditoriaComentario + "* miComentario=>*" + miComentario + "* miAuditoria=> *" + miAuditoria + "*");

		$.ajax({ type: 'GET', url: '/guardar/auditoriaComentarios/' + sOper + '|' + miAuditoria + '|' + miIdAuditoriaComentario + '|' + miComentario + '|ALTA|ACTIVO|PGA',
			success: function(response) {
				sRespuesta = response.replace(/^\s+/g,'').replace(/\s+$/g,'');
				if(sRespuesta == "OK"){
					alert("salio de guardar comentario");

					recuperarTablaComentarios('tblComentariosByAuditoria', miAuditoria, document.all.tblListaComentarios);
					//alert("Se guardo el comentario correctamente.");
					document.all.divListaComentarios.style.display='inline';
					document.all.txtComentarioAuditoria.value = "";
					// **** OJO INCLUIR EL ALTA DE UN COMENTARIO SOLO DEFINIR A QUIEN
					return true;
				}else{
					alert("No fue posible guardar el comentario."+ response);
					return false;
				}
			},
			error: function(xhr, textStatus, error){
				alert(' Error en function guardarComentario()  TextStatus: ' + textStatus + ' Error: ' + error );
				return false;
			}			
		});
	}


	function agregarAuditoria(){
		// hvs nuevo código 2016/12/08
		validaOcultarClaves();
		// Fin hvs nuevo código 2016/12/08
		// Inabilitado por HVS en 2017/01/12 document.getElementById("btnGuardar").disabled=false;
		document.all.listasAuditoria.style.display='none';
		document.all.capturaAuditoria.style.display='inline';
		document.all.txtOperacion.value='INS';		
		document.all.botonAsignarCriterios.style.display='none';
		limpiarCamposAuditoria();
		limpiaTabla(document.all.tblListaCriterios);
		limpiaTabla(document.all.tblListaArchivos);
		//asignarElemento(document.all.txtObjeto,"","");
	}
	

	function validaOcultarClaves(){
		if( document.all.txtClaveAuditoria.value.indexOf('ASCM') == -1  ){
			document.getElementById("divAuditoria").style.display="none";
		}else{
			document.getElementById("divAuditoria").style.display="inline";
		}
	}

	// ************************************* SECCIONES DE RECUPERADO DE DATOS *******************************************

	function recuperaAuditoria(id){
		//alert("Recuperando: " + id);
		// Inabilitado por HVS 2017/01/12 document.getElementById("btnGuardar").disabled=false;

		$.ajax({
			type: 'GET', url: '/lstAuditoriaByID_2/' + id , success: function(response) {
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
				//recuperarTablaGastoCtaPublica('lstRubrosGastosCP', miAuditoria, document.all.tblRubroGastoCtaPublica);

				// hvs nuevo código 2016/12/08
				validaOcultarClaves();
				// Fin hvs nuevo código 2016/12/08
				// Inabilitado por HVS 2017/01/12 document.all.btnGuardar.style.display='inline';
				document.all.txtOperacion.value="UPD";
								
	
				document.all.listasAuditoria.style.display='none';
				document.all.capturaAuditoria.style.display='inline';
				////HVS document.all.botonAsignarCriterios.style.display='inline';

		},
			error: function(xhr, textStatus, error){
				alert('function recuperaAuditoria()   -> statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error: ' + error + ' Proyecto de Auditoría: ' + id + ' Verique que tiene privilegios para ver el detalle de los proyectos de auditoría');
			}			
		});		
	}


	function recuperaAuditoriaCriterio(auditoria, tipoAuditoria, criterio){

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
			var liga = 'lstFinalidadesByCuenta';			
			recuperarLista(liga, document.all.txtFinalidad);
	}
	
	function recuperaFunciones(valor){
		
		//alert("El valor de Centro Gestor es: " + document.all.txtCentroGestor.selectedIndex);

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

			/* --- SE COMENTARA ESTE CÓDIGO YA QUE AHORA SE LLENARA LA TABLA DE GASTO CON EL ARREGLO PUBLICO CONTROLOBJETOS
			if (miAuditoria==""){
				miAuditoria = "SIN_DEFINIR";
			}

			var liga = 'tblPartidasByCapitulo/' + miAuditoria;
			recuperarTablaGasto(liga, miCapitulo, document.all.tblGasto);
			---- */
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

                 	controlObjetos.push(new Array(dato.id, dato.texto, dato.nivel, dato.valor, dato.tipoObjeto) );
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
   		//while (cmb.length>=1){
		//	cmb.remove(cmb.length-1);
		//}

		//Agregar elementos
		//alert("Llenando el txtObjetos(cmb) con controlObjetos(array): " + controlObjetos.length);

		for (i = 0; i < controlObjetos.length; i++) {
           	//alert("Contenido de controlObjetos: " + controlObjetos[i]);
			var dato = controlObjetos[i];
			var option = document.createElement("option");
			option.value = dato[0];
			option.text = dato[1];
			/*
			if(id==""){
				if(text==""){
				}else{
					if (dato[1]==text) option.selected = true;
				}
			}else{
				if (dato[0]==id) option.selected = true;
			}
			*/
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
		/*
		if (document.getElementById("txtElementosCriterio").value==''){
			alert("Debe capturar los ELEMENTOS DE SELECCIÓN.");
			document.all.txtElementosCriterio.focus();
			return false;
		}
		*/			
		return true;		  
	}

	/*
	function validarComentario(){

		//alert("El valor de document.getElementById(txtComentarioAuditoria).value es: *" + document.getElementById("txtComentarioAuditoria").value + "*");

		var contenidoCometario = document.getElementById("txtComentarioAuditoria").value.trim();

		if ( contenidoCometario.length == 0){
			alert("Debe capturar el contenido del COMENTARIO.");
			document.all.txtComentarioAuditoria.focus();
			return false;
		}	
		return true;		  
	}
	*/

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
					} else{
						 alert("ATENCION: Ya existe el Criterio seleccionado " + " \n en el Proyecto de Auditoría.: " + sAuditoria + " \npor favor verifique.");
					}
			},
				error: function(xhr, textStatus, error){
					alert('function validaAuditoriaCriterio()   -> statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
				}
			});		
		}
	}

	function actualizarSectorSubsectorByUnidad(sUnidad){

		var miSujeto = (document.all.txtUnidad.options[document.all.txtUnidad.selectedIndex].value).split("|");

		document.all.txtSector.value = miSujeto[0];
		document.all.txtSubsector.value = miSujeto[1];
	}

	//------------------------------------------------------------------------------------------------------------------------------------------
	/// sección de funcionae especiales para el manejo de la tabla PARTIDAS
	//------------------------------------------------------------------------------------------------------------------------------------------

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
			}
		}
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
		
			
			setGrafica(chart1, "dpsTodasAuditoriasByCta", "pie", "Auditorias", "canvasJG" );
	
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

		// SE ALMACENAN LOS DATOS DE OBJETOS DE EGRESO, INGRESO Y CONSOLIDADO EN TXT ESPECIFICOS 
		
		// ***** OBJETOS DE EGRESOS
		
		d.txtLstObjetos.value = d.txtFuncion.options[d.txtFuncion.selectedIndex].value + '|' + d.txtFuncion.options[d.txtFuncion.selectedIndex].text + '*' + d.txtsubFuncion.options[d.txtsubFuncion.selectedIndex].value + '|' + d.txtsubFuncion.options[d.txtsubFuncion.selectedIndex].text  + '*' + d.txtActividad.options[d.txtActividad.selectedIndex].value + '|' + d.txtActividad.options[d.txtActividad.selectedIndex].text  + '*' + d.txtCapitulo.options[d.txtCapitulo.selectedIndex].value + '|' + d.txtCapitulo.options[d.txtCapitulo.selectedIndex].text;

		//alert("El valor de todos los campos es: " + d.txtLstObjetos.value);

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
               		       	controlObjetos.push(new Array('',textoObj , 'FUNCIÓN',idObj, 'EGRESO') );
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
           		       	controlObjetos.push(new Array('',textoObj , 'SUBFUNCIÓN',idObj, 'EGRESO') );
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
       		       	controlObjetos.push(new Array('',textoObj , 'ACTIVIDAD',idObj, 'EGRESO') );
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
       		       	controlObjetos.push(new Array('',textoObj , 'CAPITULO',idObj, 'EGRESO') );
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

	/*
	function eliminarElemento(cmb, valor, iniciaDesde){
		var ciclos = cmb.length;

		if(valor!="" ){
			// Recorre la lista
			for (i=iniciaDesde; i<=ciclos;i++){
				// compara cada elemento para localizar al deseado para eliminación
				//alert("cmb[i].value: " + cmb[i].value + " valor: " + valor);
				if (cmb[i].value == valor){
					cmb.remove(i);
					break;
				}
			}	
		}
	}
	*/

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


	function recuperarImportesGastoCuentaPublica(){
		var vSector;
		var vSubsector;
		var vUnidad;
		var vNombreUnidad;

		var vObjeto;
    	var vFinalidad;
    	var vFuncion;
    	var vSubfuncion;
    	var vActividad;
    	var vCapitulo;
    	var vPartida;
    	var vNombre;
    	var vNivel;

		var aUnidades = new Array();
		var aRubros   = new Array();

		// Primero:recupera las unidades o entes que estan definidos en la auditoria
		// Y los asigna a un arreglo.

		liga = '/recuperaUnidadesByAuditoria/' +  document.all.txtAuditoria.value ;

		//alert("El valor de la liga es:" + liga );

		$.ajax({ async: false, type: 'GET', url: liga ,
          	success: function(response) {
            	var jsonData = JSON.parse(response);                                 

                for (var i = 0; i < jsonData.datos.length; i++) {
                 	var dato = jsonData.datos[i];        

                 	var aUnidad = dato.id.split("|");

					vSector       = aUnidad[0];
					vSubsector    = aUnidad[1];
					vUnidad       = aUnidad[2];
					vNombreUnidad = dato.texto;

					//alert("El valor de id[" + i + "]: " + dato.id + "\n dato.texto: " + dato.texto + "\n vSector: " + vSector + "\n vSubsector: " + vSubsector + "\n vUnidad: " + vUnidad + "\n vNombreUnidad: " + vNombreUnidad);
 
                  	//aUnidades.push(new Array(aUnidad[0], aUnidad[1], aUnidad[2], dato.texto) );
                  	aUnidades.push(new Array(aUnidad[0] + '|' + aUnidad[1] + '|' + aUnidad[2] ) );
          			
          			//alert("El valor de [" + i + "] es: " + aUnidades[i] );
  			 	}
 			},
				error: function(xhr, textStatus, error){
				alert('function recuperarImportesGastoCuentaPublica(1)   -> statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
				}			
		});

    	// Segundo: Localizará los campos Finalidad/Función/Subfunción/Actividades/valor (Este último campo sirve para Capitulo y Partida)

		liga = '/recuperaRubrosEgresoByAuditoria/' +  document.all.txtAuditoria.value ;

		$.ajax({ async: false, type: 'GET', url: liga ,
          	success: function(response) {
            	var jsonData1 = JSON.parse(response);                                 
				var aNiveles;

                for (var j = 0; j < jsonData1.datos.length; j++) {
                 	var dato1 = jsonData1.datos[j];        

                	vObjeto     = "NULL";
                	vFinalidad  = "NULL";
                	vFuncion    = "NULL";
                	vSubfuncion = "NULL";
                	vActividad  = "NULL";
                	vCapitulo   = "NULL";
                	vPartida    = "NULL";
                	vNombre     = "NULL";
                	vNivel      = "NULL";

                	if (dato1.rubros != null){ 

                 		aNiveles = dato1.rubros.split("/");						

	                	if (aNiveles.length > 0){

		                	vFinalidad   = aNiveles[0];
		                	vFuncion     = aNiveles[1];
		                	vSubfuncion  = aNiveles[2];
		                	vActividad   = aNiveles[3];
	                	}
	                }


             		vObjeto = dato1.idObjeto;
             		vNombre = dato1.nombre;
             		vNivel  = dato1.nivel;

                 	if (dato1.nivel == 'CAPITULO' ){

                 		vCapitulo = dato1.valor;
                 		vNombre = dato1.nombre.substr(dato1.nombre.indexOf("-")+2, dato1.nombre.length);

                 	} else if (dato1.nivel == 'PARTIDA' ){

                 		vCapitulo = dato1.valor;
                 		vPartida = dato1.nombre.substr(1, dato1.nombre.indexOf("-")-2);
                 		vNombre = dato1.nombre.substr(dato1.nombre.indexOf("-")+2, dato1.nombre.length);
                 	}

                 	//aRubros.push(new Array(vObjeto, vNombre, vNivel, vFinalidad, vFuncion, vSubfuncion, vActividad, vCapitulo, vPartida) );

                 	aRubros.push(new Array(vObjeto +'|'+ vNivel +'|'+ vFinalidad +'|'+ vFuncion +'|'+ vSubfuncion +'|'+ vActividad +'|'+ vCapitulo +'|'+ vPartida) );
                	
                	//alert("vSector: " + vSector + "\n vSubsector: " + vSubsector + "\n vUnidad: " + vUnidad + "\n vNombreUnidad: " + vNombreUnidad + "\n vObjeto: " + vObjeto + "\n vFinalidad: " + vFinalidad + "\n vFuncion: " + vFuncion + "\n vSubfuncion: " + vSubfuncion + "\n vActividad: " + vActividad + "\n vCapitulo: " + vCapitulo + "\n vPartida;: " + vPartida + "\n vNombre: " + vNombre + "\n vNivel: " + vNivel);

                	////alert(" valor aRubros es en posicion [" + j + "]: " + aRubros[j]);

  			 	}
			},
				error: function(xhr, textStatus, error){
				alert('function recuperarImportesGastoCuentaPublica(2)   -> statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
				}
		});

    	// Tercero: Buscara los importes de gasto de los rubros que tenga seleccionados la auditoria, esto lo hara 
    	// por cada unidad en el arreglo aUnidades por cada rubro en el arreglo aRublos.


      	//alert(" La longitud aRubros es: " + aRubros.length);
      	//alert(" La longitud aUnidades (A) es: " + aUnidades.length);

      	//var aGastos = new Array();
		innerHTML="";

		limpiaTabla(document.all.tblRubroGastoCtaPublica);

        var renglon, columna;


		//alert("El tamaño de aUnidades (B) es: " + aUnidades.length);

        for (var i = 0; i < aUnidades.length; i++) {
           	//alert(" valor aUnidades es en posicion [" + i + "]: " + aUnidades[i]);
           	aUnidad = aUnidades[i];
	        for (var j = 0; j < aRubros.length; j++) {
	        	aRubro = aRubros[j];


				liga = '/recuperaGastoByAuditoria/' + aUnidad + "/" + aRubro ;
				//alert("El valor de Liga es : " + liga);

				$.ajax({ type: 'GET', url: liga ,
		          	success: function(response) {
		            	//var obj = JSON.parse(response);                                 
			          	var jsonData = JSON.parse(response);         	                        

			          	//alert(response);

                		for (var k = 0; k < jsonData.datos.length; k++) {
                 			var arr = jsonData.datos[k];        
		   				
		   					var option = document.createElement("option");

		            		renglon=document.createElement("TR");     
		            		renglon.innerHTML="<td>" + arr.cuenta + "</td><td>" + arr.ente + "</td><td>" + arr.rubro + "</td><td>" + arr.original + "</td><td>" + arr.modificado + "</td><td>" + arr.ejercido + "</td><td>" + arr.pagado + "</td>";

		            		renglon.onclick= function()  {   };
		            		document.all.tblRubroGastoCtaPublica.appendChild(renglon);                                                                       

    	               		//aGastos.push(new Array(obj.ente, obj.rubro, obj.original, obj.modificado, obj.ejercido, obj.pagado) );
    	               		//alert(aGastos);
    	               	}
		 			},
						error: function(xhr, textStatus, error){
						alert('function recuperarImportesGastoCuentaPublica(3)   -> statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
						}			
				});
               	//alert(" valor aRubros es en posicion [" + j + "]: " + aRubros[j]);
            }
        }
	}




	$(document).ready(function(){
		//setInterval(getMensaje('txtNoti'),60000);
		getMensaje('txtNoti',1);
		
		document.getElementById("divElementoCriterio").style.display='none';

		document.all.lblConAsf.style.display='none';
		document.all.chkConAsf.style.display='none';

		recuperarLista('lstTiposAuditorias', document.all.txtTipoAuditoria);
		//recuperarLista('lstAreas', document.all.txtResponsable);
		recuperarLista('lstAreasResponsablesConsulta', document.all.txtResponsable);
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

 		$("#divSectorSubsector").hide();
 		$("#divCentroGestor").hide();
 		$("#divSujeto").hide();


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

		// Botón que guarda la auditorías
		$( "#btnGuardar" ).click(function() {

			// Fin hvs nuevo código 2016/12/08 document.getElementById("txtEtapa").disabled=false;

			if (validarAuditoria()){
				// Inicio del poceso para poder comunicar el contenido del arreglo controlObjetos a el proceso de submit.
				sValoresObjetos = "";
				if(controlObjetos.length > 0){
					separador = "";
					for(i=0; i < controlObjetos.length; i++){
						sValoresObjetos = sValoresObjetos + separador + controlObjetos[i][0] + '|' + controlObjetos[i][1] + '|' + controlObjetos[i][2] + '|' + controlObjetos[i][3] + '|' + controlObjetos[i][4];
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

				// Inabilitado por HVS 2017/01/12 document.getElementById("btnGuardar").disabled=true;
				document.all.formulario.submit();
				document.getElementById("txtEtapa").disabled=true;
				}
			}
		);
		/*
		function guardaObjetosNuevaGarantia(){
			var nuevoIdGarantia;
			var d = document.all;
			var sValores;

			// Inicialmente es necesario recuperar el ID de la nueva garantía, esto se realizara en base a varios campos con los que se guardo la auditoria.

			miTipoAuditoria = document.all.txtTipoAuditoria.options[document.all.txtTipoAuditoria.selectedIndex].value;
			miresponsable = document.all.txtResponsable.options[document.all.txtResponsable.selectedIndex].value;
			
			var miSujeto = (document.all.txtUnidad.options[document.all.txtUnidad.selectedIndex].value).split("|");

			miSector = miSujeto[0];
			miSubsector = miSujeto[1];
			miUnidad = miSujeto[2];

			miObjetivo = (document.all.txtObjetivo.value).trim();
			miTipoPresupuesto = document.all.txtPresupuesto.options[document.all.txtPresupuesto.selectedIndex].value;
			
			sValores =  miTipoAuditoria + '|' +  miResponsable + '|' + miSector + '|' +  miSubsector + '|' + miUnidad + '|' +  miObjetivo + '|' + miTipoPresupuesto ;

			listaCompleta = '/lstAuditoriaBySeveralData/' + sValores;
			$.ajax({
				type: 'GET', url: listaCompleta , 
				success: function(response) {

					var obj = JSON.parse(response);	
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
		*/

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

		$( "#btnGuardarComentario" ).click(function() {
	
			if ((document.getElementById("txtComentarioAuditoria").value.trim()).length > 0){

				var sValores = document.all.txtOperacionComentarios.value; // Tipo de operación INS/UPD
				sValores = sValores + '|' + document.all.txtAuditoria.value; // Auditoria
				if( document.all.txtIdAuditoriaComentario.value.trim() == "") {
					sValores = sValores + '|0'; // Auditoria Comentario
				}else{
					sValores = sValores + '|' + document.all.txtIdAuditoriaComentario.value; // Auditoria Comentario
				}
				sValores = sValores + '|' +  document.all.txtComentarioAuditoria.value.trim(); // Comentario
				sValores = sValores + '|ALTA|ACTIVO|PGA'; // prioridad y estatus

				//alert("HVSLos valores a guardar son: TipoOperacion|Auditoria|AuditoriaComentario|comentario|prioridad|estatus|seccion=>" + sValores);

				$.ajax({ type: 'GET', url: '/guardar/auditoriaComentarios/' + sValores ,
					success: function(response) {
						sRespuesta = response.replace(/^\s+/g,'').replace(/\s+$/g,'');

						if(sRespuesta == "OK"){
							recuperarTablaComentarios('tblComentariosByAuditoria', document.all.txtAuditoria.value, document.all.tblListaComentarios);
							//alert("Se guardo el comentario correctamente.");
							document.all.divListaComentarios.style.display='inline';
							document.all.txtComentarioAuditoria.value = "";
							// **** OJO INCLUIR EL ALTA DE UN COMENTARIO SOLO DEFINIR A QUIEN
							return true;
						}else{
							alert("No fue posible guardar el comentario."+ response);
							return false;
						}
					},
					error: function(xhr, textStatus, error){
						alert(' Error en function guardarComentario()  TextStatus: ' + textStatus + ' Error: ' + error );
						return false;
					}			
				});

			}else{
				alert("Debe capturar el contenido del COMENTARIO.");
				document.all.txtComentarioAuditoria.focus();
				return false;
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


		$( "#btnCriterios" ).click(function() {		
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

		$( "#btnComentarios" ).click(function() {		
			$('#modalComentarios').removeClass("invisible");
			$('#modalComentarios').modal('toggle');
			$('#modalComentarios').modal('show');
			document.all.txtOperacionComentarios.value = 'INS';
			document.all.txtComentarioAuditoria.value = "";
			//var miTipoAuditoria = document.all.txtTipoAuditoria.options[document.all.txtTipoAuditoria.selectedIndex].value;
			var miAuditoria = document.all.txtAuditoria.value;
			recuperarTablaComentarios('tblComentariosByAuditoria', miAuditoria, document.all.tblListaComentarios);
		});

		$( "#btnConsultaGasto" ).click(function() {		

			//recuperarImportesGastoCuentaPublica('tblComentariosByAuditoria', miAuditoria, document.all.tblListaComentarios);
			recuperarImportesGastoCuentaPublica();

			$('#modalGastoCuentaPublica').removeClass("invisible");
			$('#modalGastoCuentaPublica').modal('toggle');
			$('#modalGastoCuentaPublica').modal('show');

		});


		$( "#btnCancelarObjeto" ).click(function() {
			$('#modalObjetos').modal('hide');			
		});		

		$( "#btnCancelarCriterio" ).click(function() {
			$('#modalCriterios').modal('hide');			
		});		

		$( "#btnCancelarUnidad" ).click(function() {
			$('#modalUnidades').modal('hide');			
		});		

		$( "#btnCancelarConsultarGasto" ).click(function() {
			$('#modalGastoCuentaPublica').modal('hide');			
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
					<div class="col-xs-3"><h2>Consulta P.G.A.</h2></div>									
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
							<h3><i class="fa fa-search"></i> Lista de Proyectos de Auditoría</h3>
							<div class="clearfix"></div>
						</div>             
						<div class="widget-content">
							<div class="table-responsive" style="height: 400px; overflow: auto; overflow-x:hidden;">
								<table class="table table-striped table-bordered table-hover table-condensed">
									<thead>
										<tr><th>Cve. Auditoria</th><th>Dirección General</th><th>Sujeto de Fiscalización</th><th>Rubro(s) de Fiscalización</th><th>Tipo de Auditoría</th></tr>
									</thead>
									<tbody>																			
										<?php foreach($datos as $key => $valor): ?>
											<tr onclick=<?php echo "javascript:recuperaAuditoria('" . $valor['auditoria'] . "');"; ?> style="width: 100%; font-size: xx-small">										  										  
												<!--  <td><?php echo $valor['auditoria']; ?></td> //##--> 
												<td width="10%"><?php echo $valor['claveAuditoria']; ?></td>
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
								<!--
								<button onclick="agregarAuditoria();" type="button" class="btn btn-primary  btn-xs"><i class="fa fa-search"></i> Agregar Proyecto de Auditoría...</button>
								-->
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
					<input type='HIDDEN' name='txtLstIngresos' value=''>	
					<input type='HIDDEN' name='txtLstConsolidados' value=''>									
					<input type='HIDDEN' name='txtControlObjetos' value=''>									
					<input type='HIDDEN' name='txtUnidadesSeleccionadas' value=''>									

					<div class="col-md-12">				
						<div class="widget">
							<!-- Widget head -->
							<div class="widget-head">
								<div class="pull-left"><h3><i class="fa fa-pencil-square-o"> </i> Detalle de Proyecto</h3></div>
									<div class="widget-icons pull-right">

									 	<!-- SE APAGAN LOS BOTONES DE GUARDAR POR SER UNA SECCION DE GUARDADO 2017/01/12

										<button type="button" id="btnCriterios" class="btn btn-primary  btn-xs" style="display:none;">Asignación de Criterios</button >
										<button type="button"  id="btnGuardar" class="btn btn-primary  btn-xs"><i class="fa fa-floppy-o" style="display:none;" ></i> Guardar</button> 
										-->

										<button type="button" id="btnCancelar" class="btn btn-default  btn-xs"><i class="fa fa-undo"></i> Regresar</button> 
							  		</div>  
								  	<div class="widget-icons pull-right" id="botonAsignarCriterios">
										<button type="button" class="btn btn-warning btn-xs " name="btnVOBO" id="btnVOBO" style="display:none;"><i class="fa fa-external-link"></i>Otorga VoBo...</button>
										<button type="button" class="btn btn-warning btn-xs " name="btnAUTORIZACION" id="btnAUTORIZACION" style="display:none;"><i class="fa fa-external-link"></i>Autoriza Projecto...</button>
										<!--
										<button type="button" class="btn btn-default  btn-xs"><i class="fa fa-external-link"></i>Cancelar </button>
										<button type="button" id="btnCriterios" class="btn btn-warning btn-xs "><i class="fa fa-external-link"></i> Asignación de Criterios...</button> 
										-->
										<button type="button" id="btnComentarios" class="btn btn-warning btn-xs "><i class="fa fa-comments"></i> Comentarios...</button> 

										<button type="button" id="btnConsultaGasto" class="btn btn-warning btn-xs "><i class="fa fa-comments"></i> Consulta Gasto...</button> 


										<label>  </label> 
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
											<div class="col-xs-1"><input type="text" class="form-control" name="txtClaveAuditoria" readonly/></div>													
										</div>								
										<div class="form-group">									
											<label class="col-xs-2 control-label">Tipo</label>
											<div class="col-xs-4">
												<select class="form-control" name="txtTipoAuditoria" id="txtTipoAuditoria" disabled="true" >
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
											<select class="form-control" name="txtResponsable" disabled="true" >
												<option value="">Seleccione...</option>											
											</select>
										</div>
									</div>
									<br>
									
									<div class="form-group" id="divSectorSubsector">
										<label class="col-xs-2 control-label">Sector</label> 
											<div class="col-xs-4"> 
												<input type='text' name='txtSector' id='txtSector' value=''  readonly/>									
												<!-- 
												<select class="form-control" name="txtSector" onChange="javascript:recuperarListaLigada('lstSubsectoresBySector', this.value, document.all.txtSubsector);">
													<option value="">Seleccione...</option>											
												</select>
												-->
											</div>	

											<label class="col-xs-2 control-label">Subsector</label> 
											<div class="col-xs-4"> 
												<input type='text' name='txtSubsector' id= name='txtSubsector' value=''  readonly/>									
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
											<select class="form-control" name="txtUnidad" id="txtUnidad" onChange="javascript:actualizarSectorSubsectorByUnidad(this.value);" disabled="true" >
												<option value="">Seleccione...</option>											
											</select>
										</div>								
									</div>
									<br>
									
									<div class="form-group">
										<label class="col-xs-2 control-label" style=" margin: -2% 0 1% !important;">Sujetos(s)</label>
										<div class="col-xs-9">
											<select class="form-control" name="txtUnidades" id="txtUnidades" size=4 style=" margin: -3% 0 1% !important;" >
												<!-- <option value="">Seleccione...</option> -->
											</select>										
										</div>	
										<div class="col-xs-1" style=" margin: -2% 0 1% !important;">
											<!-- // Inabilitado por HVS 2017/01/12 
											<button  type="button" class="btn btn-default btn-xs" id="btnVerUnidades" style=" margin: 0% 0 6% !important;"> Agregar</button>
											<button  type="button" class="btn btn-default btn-xs" id="btnQuitarUnidades"> Quitar </button>		
											-->
		
										</div>
									</div>								

									<div class="form-group">
										<label class="col-xs-2 control-label">Rubro(s)</label>
										<div class="col-xs-9">
											<select class="form-control" name="txtObjeto" id="txtObjeto" size=4 style=" margin: 0 0 1% 0 !important;" >
												<!-- <option value="">Seleccione...</option> -->
											</select>										
										</div>	
										<div class="col-xs-1">
											<!-- // Inabilitado por HVS 2017/01/12 
											<button  type="button" class="btn btn-default btn-xs" id="btnVerObjeto" style=" margin: 0% 0 6% !important;"> Agregar</button>
											<button  type="button" class="btn btn-default btn-xs" id="btnQuitarObjeto"> Quitar </button>		
											-->
										</div>
									</div>								
									<br>	

									<div class="form-group">
										<label class="col-xs-2 control-label">Objetivo(s) <i class="fa fa-pencil"  id="txtObjetivoEdit"></i></label>
										<div class="col-xs-10"><textarea class="form-control" rows="3" name="txtObjetivo" id="txtObjetivo" style=" margin: 0 0 1% 0 !important;" readonly ></textarea></div>
									</div>
									<br>	

									<div class="form-group">
										<label class="col-xs-2 control-label">Alcance(s) <i class="fa fa-pencil"  id="txtAlcanceEdit"></i></label>
										<div class="col-xs-10"><textarea class="form-control" rows="3" name="txtAlcance" id="txtAlcance" style=" margin: 0 0 1% 0 !important;" readonly></textarea></div>
									</div>	
									<br>	

									<div class="form-group">
										<label class="col-xs-2 control-label">Justificación <i class="fa fa-pencil"  id="txtJustificacionEdit"></i></label>
										<div class="col-xs-10"><textarea class="form-control" rows="3" name="txtJustificacion" id="txtJustificacion" style=" margin: 0 0 1% 0 !important;" readonly></textarea></div>
									</div>
									<br>	

									<div class="form-group">									
										<label class="col-xs-2">Tipo de Presupuesto</label>
										<div class="col-xs-3">
											<select class="form-control" name="txtPresupuesto" disabled="true">
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
										<div class="table-responsive" style="height: 175px; overflow: auto; overflow-x:hidden;" id="divListaCriterios">
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

										<div class="table-responsive" style="height: 175px; overflow: auto; overflow-x:hidden;" id="divListaArchivos">
											<table class="table table-striped table-bordered table-hover table-condensed table-responsive" >
												<caption>Documentos Asociados</caption>
												<thead>
													<tr><th>Docto.</th><th>Flujo</th><th>Tipo</th><th>Fecha</th><th>Prioridad</th><th>Impacto</th><th>Archivo</th></tr>
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
								<div class="col-xs-20"><textarea class="form-control" rows="20" placeholder="Capture aqui" id="txtTextoAmplio" name="txtTextoAmplio" readonly></textarea></div>
							</div>
						</div>
					<div class="clearfix"></div>
				</div>				
				<div class="modal-footer">
					<!-- // Inabilitado por HVS 2017/01/12 
					<button  type="button" class="btn btn-primary active" id="btnGuardarTexto"><i class="fa fa-floppy-o"></i> Guardar</button>	
					-->
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
								<select class="form-control" name="txtCriterio" disabled="true" onChange="javascript: validaAuditoriaCriterio(document.all.txtAuditoria.value, this.value, document.all.txtCriterioAnterior.value);>
									<option value="">Seleccione...</option>
								</select>
							</div>
						</div>

						<br>
						<div class="form-group">
							<label class="col-xs-2 control-label">Justificación</label>
							<div class="col-xs-10">
							<textarea class="form-control" rows="5" placeholder="Capture aqui" id="txtJustificacionCriterio" name="txtJustificacionCriterio" readonly>  </textarea>
							</div>						
						</div>						

						<br>
						<div class="form-group" id="divElementoCriterio">
							<label class="col-xs-2 control-label" >Elementos de Selección</label>
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
						<!-- Se bloquea por que esta pantalla es de solo consulta.
						<button type="button" class="btn btn-primary active" id="btnGuardarCriterio" type="submit"><i class="fa fa-floppy-o"></i> Guardar</button>
						-->
						<button type="button" class="btn btn-default active" id="btnCancelarCriterio" data-dismiss="modal"><i class="fa fa-back"></i> Cancelar</button>
					</div>
					<div class="clearfix"></div>
				</div>
			</div>		
		</div>
	</div>


	<div id="modalComentarios" class="modal fade" role="dialog">
		<div class="modal-dialog">							
				<!-- Modal content-->
			<div class="modal-content">									
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h3 class="modal-title"><i class="fa fa-pencil-square-o"></i>Asignación de Comentario...</h3>
				</div>									
				<div class="modal-body">
					<form id="formularioComentarios" METHOD='POST' ACTION='/guardar/auditoriaComentarios' role="form">
						<input type='HIDDEN' name='txtComentarioAnterior' value=''> 
						<input type='HIDDEN' name='txtOperacionComentarios' value=''>
						<input type='HIDDEN' name='txtIdAuditoriaComentario' value=''> 

						<div class="form-group">	
							<div class="table-responsive" style="height: 300px; overflow: auto; overflow-x:hidden;" id="divListaComentarios">
								<table class="table table-striped table-bordered table-hover table-condensed table-responsive" >
									<caption><h3>Comentarios Registrados</h3></caption>
									<thead>
										<tr><th width="20%">Fecha/Hora</th><th width="20%">Usuario</th><th width="60%">Comentario</th></tr>
									</thead>
									<tbody id="tblListaComentarios" style="width: 100%; font-size: xx-small">
									</tbody>
								</table >
							</div>
							<br>
						</div>

						<br>
						<div class="form-group">
							<label class="col-xs-1 control-label">COMENTARIO:</label>
						</div>						
						<div class="form-group">
							<div class="col-xs-12">
							<textarea class="form-control" rows="2" placeholder="Capture aqui su comentario..." id="txtComentarioAuditoria" name="txtComentarioAuditoria">  </textarea>
							</div>						
						</div>						
						<div class="clearfix"></div>								
					</form>
					<div class="clearfix"></div>
				</div>
				
				<div class="modal-footer">
					<div class="pull-right">
						<button type="button" class="btn btn-primary active" id="btnGuardarComentario" type="submit"><i class="fa fa-floppy-o"></i> Guardar Comentario</button>
						<button type="button" class="btn btn-default active" id="btnCancelarComentario" data-dismiss="modal"><i class="fa fa-back"></i> Cancelar</button>
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


	<div id="modalGastoCuentaPublica" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<!-- Modal content-->

			<!-- Encabezado de la pantalla modal -->

			<div class="modal-content">									
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h3 class="modal-title"><i class="fa fa-home"></i> Consulta de Gasto en Cuenta Pública...</h3>
				</div>					

				<!-- Cuerpo de la pantalla modal -->

				<div class="modal-body">

					<form id="formularioUnidades" METHOD='POST' ACTION='/guardar/auditoriaUnidades' role="form">
						<!-- ************************ ÁREA DE GRID  ********************* -->

						<div class="table-responsive col-xs-12" style="height: 300px; overflow: auto; overflow-x:hidden;">	
							<br>
							<table class="table table-striped table-bordered table-hover table-condensed table-responsive" >
								<!-- <caption>Objetos de Fiscalización (INGRESO).</caption> -->
								<thead>
									<tr>
										<th width="8%">Cuenta</th>
										<th width="25%">Rubro</th>
										<th width="25%">Nivel de Rubro</th>
										<th width="10%">Original</th>
										<th width="10%">Modificado</th>
										<th width="10%">Ejercido</th>
										<th width="10%">Pagado</th>
									</tr>
								</thead>
								<tbody id="tblRubroGastoCtaPublica" style="width: 100%; font-size: xx-small">
								</tbody>
							</table>
						</div>

					</form>
				</div>				
				<div class="clearfix"></div>

				<!-- Sección de pie de pagina de la pantalla modal -->
				<div class="modal-footer">
					<button  type="button" class="btn btn-default" id="btnCancelarConsultarGasto" style="display:inline;"><i class="fa fa-undo"></i> Regresar</button>	
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