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
			#modalDestinatarios .modal-dialog {width:70%;}
			#modalCargarArchivos .modal-dialog {width:30%;}


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
	//-----------------------------------------------
	var lstDestinatariosInternos = new Array();
	var lstArchivosAnexos = new Array();
	var ArchivoFinal = "";

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


	$(function () {
	 	$.datepicker.regional['es'] = {
	        closeText: 'Cerrar',
	        prevText: '&#x3c;Ant',
	        nextText: 'Sig&#x3e;',
	        currentText: 'Hoy',
	        monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
	        'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
	        monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun',
	        'Jul','Ago','Sep','Oct','Nov','Dic'],
	        dayNames: ['Domingo','Lunes','Martes','Mi&eacute;rcoles','Jueves','Viernes','S&aacute;bado'],
	        dayNamesShort: ['Dom','Lun','Mar','Mi&eacute;','Juv','Vie','S&aacute;b'],
	        dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','S&aacute;'],
	        weekHeader: 'Sm',
	        dateFormat: 'dd-mm-yy',
	        firstDay: 1,
	        isRTL: false,
	        showMonthAfterYear: false,
	        yearSuffix: ''
	    };

		$.datepicker.setDefaults($.datepicker.regional["es"]);
		
		$("#datepicker").datepicker({
		dateFormat:"dd-mm-yy",
		firstDay: 1,
		numberOfMonths: 2,
		onClose: function( selectedDate ) { $( "#datepicker2" ).datepicker( "option", "minDate", selectedDate ); }
		});

		$("#datepicker2").datepicker({
		dateFormat:"dd-mm-yy",
		firstDay: 1,
		numberOfMonths: 2,
		onClose: function( selectedDate ) { $( "#datepicker3" ).datepicker( "option", "minDate", selectedDate ); }
		});

		$("#datepicker3").datepicker({
		dateFormat:"dd-mm-yy",
		firstDay: 1,
		numberOfMonths: 2,
		onClose: function( selectedDate ) { $( "#datepicker2" ).datepicker( "option", "maxDate", selectedDate ); }
		});
	});

	
	// ***** FUNCIONES PARA LA CARGA DE ARCHIVOS

	function abrirArchivoAnexo(){
		var formdata = new FormData();
		if(checkfile(this)){
			var file = this.files[0];
			//if (file.size>100000) alert("El tamaño del archivo no debe exceder 100,000 bytes.");	
			sArchivoCarga = file.name;
			sArchivoTamano = file.size;
			sArchivoTipo = file.type;
			sArchivoModif = file.lastModified;

			//	console.log("name xx : " + file.name);
			//	console.log("size : " + file.size + " byte(s).  " + ((file.size/1024)/1024) + " MB  ");
			//	console.log("type : " + file.type);
			//	console.log("date : " + file.lastModified);

			// Proceso agregado aqui por caracteristicas de sincronización
			// Valida que no se haya registrado ya el archivo
			lExiste = false;
			for(var i = 0; i < lstArchivosAnexos.length; i++){
				if ( sArchivoCarga == lstArchivosAnexos[i][3]){
					lExiste = true;
					alert("El archivo que desea anexar ya existe.");
					break;
				}
			}
			if (lExiste == false){  
				// Ahora se movera el archivo de su localidad original a la carpeta asignada (/asuntos) en el servidor
				formdata.append("btnUpload", file);
				var ajax = new XMLHttpRequest();
				ajax.upload.addEventListener("progress", progressHandler, false);
				ajax.addEventListener("load", completeHandler, false);
				ajax.addEventListener("error", errorHandler, false);
				ajax.addEventListener("abort", abortHandler, false);				
				ajax.open("POST", "uploadAsunto.php");
				ajax.send(formdata);
			}
		}
	}		

	function completeHandler(event){
		document.all.progressBar.style="display:none";
		document.all.progressBar.value= 0;		
		if(event.target.responseText!="ERROR"){				
			ArchivoFinal=event.target.responseText;
			document.all.txtArchivoOriginal.value=sArchivoCarga;
			document.all.txtArchivoFinal.value=event.target.responseText;
			//alert("En completeHandler el valor de ArchivoFinal es: [" + ArchivoFinal + "]");
			//document.all.status.style.display='inline';
			//document.all.status.innerHTML="<img src='img/xls.gif'> " + document.all.txtArchivoOriginal.value; 
			//document.all.btnCargarArchivo.style="display:none";	

			//Agregara el archivo al arreglo 
			lstArchivosAnexos.push(new Array(0, 'ASUNTO', document.all.txtFolio.value, sArchivoCarga, ArchivoFinal));
			// Ahora se limpiara el combobox 
			while (document.all.txtArchivosAnexos.length>0){
				document.all.txtArchivosAnexos.remove(document.all.txtArchivosAnexos.length-1);
			}
			// Se vuelve a llenar el combobox con los elementos del arreglo
			for(var i = 0; i < lstArchivosAnexos.length; i++){
				var option = document.createElement("option");
				option.text = lstArchivosAnexos[i][3];
				option.value = lstArchivosAnexos[i][0];
				document.all.txtArchivosAnexos.add(option, i);
			}
		}else{
			//ArchivoFinal="";
			document.all.txtArchivoOriginal.value="";
			document.all.txtArchivoFinal.value="";
			alert("El archivo no subió correctamente.");
		}			
	}

	function errorHandler(event){document.all.status.innerHTML = "Falló la carga";}			
	function abortHandler(event){document.all.status.innerHTML = "Se abrotó la carga.";}					
	
	//// PROCESOS PARA BORRADO DE ARCHIVO
	
	function borrarArchivoAnexo(file, confirmarBorrado){
		var formdata = new FormData();
		file = "asuntos/" + file;
		//alert("El valor de file para borrar es: " + file);

		formdata.append("archivoAborrar", file);
		formdata.append("confirmarBorrado", confirmarBorrado);
		var ajax = new XMLHttpRequest();
		ajax.upload.addEventListener("progress", progressHandler, false);
		ajax.addEventListener("load", completeHandlerDel, false);
		ajax.addEventListener("error", errorHandlerDel, false);
		ajax.addEventListener("abort", abortHandlerDel, false);				
		ajax.open("POST", "deleteArchivo.php");
		ajax.send(formdata);			
	}		

	function completeHandlerDel(event){
		document.all.progressBar.style="display:none";
		document.all.progressBar.value= 0;		
		if(event.target.responseText!="ERROR"){				
			if(event.target.responseText == "BORRADO"){ alert("El archivo se quitó correctamente."); }
		}else{
			alert("El archivo " + event.target.responseText + "no se quitó correctamente.");
		}			
	}

	function errorHandlerDel(event){document.all.status.innerHTML = "Falló el borrado";}			
	function abortHandlerDel(event){document.all.status.innerHTML = "Se abrotó el borrado.";}					


	///// PROCESOS COMUN PARA CARGA Y BORRADO DE ARCHIVO

	function progressHandler(event){
		//document.all.progressBar.style="display:inline";
		//document.all.lblAvances.innerHTML="Cargando " + event.loaded + " bytes de " + event.total;
			
		var porcentaje = (event.loaded / event.total) * 100;
		document.all.progressBar.value= Math.round(porcentaje);
		//document.all.status.innerHTML = Math.round(porcentaje) + "% cargando... Espere"; 				
	}

	// ************************************* SECCIONES DE RECUPERACIONES DE LISTAS PARA TABLAS *******************************************
	//****************************************************************************************************************************

	function recuperarTablaDestinatariosFromArreglo(lista, valor, tbl){
		var sTatus;
		var sTatus1;
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
               	//lstDestinatariosInternos = [];
              		
               for (var i = 0; i < jsonData.datos.length; i++) {
                 	var dato = jsonData.datos[i];                                                                    

                   	renglon=document.createElement("TR");    
                   	sTatus = "";
                   	sTatus1 = "";

                   	//alert("Entro total de registros: " + jsonData.datos.length );

          			if(dato.asignado=="SI"){
          				if (dato.atencion){
	           			 	sTatus = "checked=true";
          				}else{
	           			 	sTatus = "checked=false";
          				}
          				if (dato.conocimiento){
	           			 	sTatus1 = "checked=true";
          				}else{
	           			 	sTatus1 = "checked=false";
          				}
                   		seleccionarDestinatarioInterno(dato.id, dato.idArea, dato.area, dato.nombreUsuario, dato.plaza, dato.atencion, dato.conocimiento);
                   	}

					cadena = "<tr>";
					cadena += "<td> <input type = 'checkbox' name = '' " + sTatus ;
					cadena += " onclick='seleccionarDestinatarioInterno(" + '"' + dato.id + '","' + dato.idArea + '","' + dato.area + '","' + dato.nombreUsuario +  '","' + dato.plaza + '",' + " this.checked, false);' /> </td> ";
					cadena += "<td> <input type = 'checkbox' name = '' " + sTatus1 ;
					cadena += " onclick='seleccionarDestinatarioInterno(" + '"' + dato.id + '","' + dato.idArea +  '","' + dato.area +  '","' + dato.nombreUsuario + '","' + dato.plaza + '",' + " false, this.checked);' /> </td> ";
					cadena += "<td> " + dato.nombreUsuario + "</td> ";
					cadena += "<td> " + dato.plaza + "</td> ";
					cadena += "<td> " + dato.area + "</td> ";
					cadena += "</tr>";

					//alert("Cadena: "+ cadena);

					renglon.innerHTML= cadena;

                  	renglon.onclick= function() { };
                   	tbl.appendChild(renglon);                                                                       
               }  	
                                                                          
           },
           error: function(xhr, textStatus, error){
                alert('function recuperarDestinatariosFromArreglo ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
           }                                             

		});           
	}              


	function seleccionarDestinatarioInterno(clave, idArea, nombreArea, nombreDestinatario, plaza, atencion, conocimiento)
	{
		var ExisteConocimiento = false;
		var ExisteAtencion = false;


		if(atencion==true || conocimiento==true ){

			//alert("Datos a Asignar: CLAVE: " + clave + "NOMBREDESTINATARIO: " + nombreDestinatario + " ATENCION: " + atencion + " CONOCIMIENTO: " + conocimiento );

			if (atencion==true){   // Se seleccionó atención 
				// Verificara si no ya existe la clave en el arreglo, consecuencia de haber seleccionado "Conocimiento" previmante.
				for( i = 0; i < lstDestinatariosInternos.length; i++)
				{
					if(clave==lstDestinatariosInternos[i][0]){
						// Si ya existe la clave (por que se habia seleccionado "Conocimiento" previamente) entonce se pone en falso la casilla de 
						// "Conocimento" del arreglo y se le asigna el de "Atención".
						lstDestinatariosInternos[i][5] = atencion;
						lstDestinatariosInternos[i][6] = conocimiento;
						ExisteConocimiento = true;
						break;
					}
				}
				if(!ExisteConocimiento) {
					lstDestinatariosInternos.push(new Array(clave, idArea, nombreArea, nombreDestinatario, plaza, atencion, conocimiento) );
				}
				//alert("El tamaño de lstDestinatariosInternos es de : " + lstDestinatariosInternos.length);
			} else{  // conocimiento es igual a true
				// Verificara si no ya existe la clave en el arreglo, consecuencia de haber seleccionado "Atención" previmante.
				for( i = 0; i < lstDestinatariosInternos.length; i++)
				{
					if(clave==lstDestinatariosInternos[i][0]){
						// Si ya existe la clave (por que se habia seleccionado "Atención" previamente) entonce se pone en falso la casilla de 
						// "Atención" del arreglo y se le asigna el de "Conocimiento". 
						lstDestinatariosInternos[i][5] = atencion;
						lstDestinatariosInternos[i][6] = conocimiento;
						ExisteAtencion = true;
						break;
					}
				}
				if(!ExisteAtencion) {
					lstDestinatariosInternos.push(new Array(clave, idArea, nombreArea, nombreDestinatario, plaza, atencion, conocimiento) );
				}
			}
		}else{
			for( i = 0; i < lstDestinatariosInternos.length; i++)
			{
				if(clave==lstDestinatariosInternos[i][0]){
					lstDestinatariosInternos.splice(i, 1);
				}
			}
		}
	}

	//***************************************************************************************************************************
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

	
	// ************************************* SECCIONES DE RECUPERADO DE DATOS *******************************************

	function recuperaAuditoria(id){
		//alert("Recuperando: " + id);
		document.getElementById("btnGuardar").disabled=false;

		$.ajax({
			type: 'GET', url: '/lstAuditoriaByID_HVS/' + id ,
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
				/*
				var miSujeto = obj.unidad;
				var miObjeto = obj.objeto;
				
				var aValores = miAuditoria + '|' + miSujeto + '|' + miObjeto;
				*/
				recuperarTablaCriterios('tblCriteriosByAuditoria', miAuditoria, document.all.tblListaCriterios);
				recuperarTablaArchivos('tblArchivosByAuditoria', miAuditoria, document.all.tblListaArchivos);

				document.all.btnGuardar.style.display='inline';
				document.all.txtOperacion.value="UPD";
								
	
				document.all.listaAsuntos.style.display='none';
				document.all.capturaAsunto.style.display='inline';
				document.all.botonAsignarCriterios.style.display='inline';

		},
			error: function(xhr, textStatus, error){
				alert('function recuperaAuditoria()   -> statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error + ' Auditoría: ' + id);
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


	function limpiarCamposAsuntos(){
		//##document.all.txtClaveAuditoria.value='';		
		
		// LIMPIA CAMPOS TEXTO		
		//alert("Iniciará la limpieza");

		document.all.txtFolio.value='';		
		document.all.txtNumeroDocto.value = '';
		
		document.all.txtDescripcion.value='';
		document.all.txtComentario.value='';			
		document.all.txtExpediente.value='';

		document.all.txtSerieDocumental.value='';
		document.all.txtClaveBusqueda.value='';

		document.all.txtTipoDocto.selectedIndex=0;
		document.all.txtClasIndices.selectedIndex=0;	
		asignarElemento_HVS(document.all.txtIndice,"","",true,true);

		seleccionaOpcionTexto(document.all.txtPrioridad, "NORMAL");
		seleccionaOpcionTexto(document.all.txtImpacto, "BAJO");
		seleccionaOpcionTexto(document.all.txtConfidencial, "NO");

		document.all.txtClasificacionDocto.selectedIndex=0;
		document.all.txtAuditoria.selectedIndex=0;

		//seleccionaOpcionTexto(document.all.txtOrigenRemitente, "INTERNO");
		asignarElemento_HVS(document.all.txtAreaRemitente,"","",true,true);
		asignarElemento_HVS(document.all.txtUsuarioRemitente,"","",true,true);

		asignarElemento_HVS(document.all.txtDestinatarios,"","",true,false);
		asignarElemento_HVS(document.all.txtArchivosAnexos,"","",true,false);

		document.all.txtFechaDocto.value = null;
		document.all.txtFechaRecepcion.value = null;
		document.all.txtFechaCompromiso.value = null;
	}	

	function limpiarCamposAuditoria(){
		//##document.all.txtClaveAuditoria.value='';		
		
		// LIMPIA CAMPOS TEXTO		

		document.all.txtFolio.value='';		
		document.all.txtNumeroDocto.value = '';
		
		document.all.txtDescripcion.value='';
		document.all.txtComentario.value='';			
		document.all.txtExpediente.value='';

		document.all.txtSerieDocumental.value='';
		document.all.txtClaveBusqueda.value='';

		// MUEVE LA LISTAS A LA OPCION SELECCIONE...

		document.all.txtTipoDocto.selectedIndex=0;
		document.all.txtClasIndices.selectedIndex = 0;	
		document.all.txtPrioridad.selectedIndex=2;
		document.all.txtIndices.selectedIndex=0;
		document.all.txtImpacto.selectedIndex=1;

		document.all.txtClasificacionDocto.selectedIndex=1;
		document.all.txtConfidencial.selectedIndex=1;
		document.all.txtAuditoria.selectedIndex=0;
		document.all.txtOrigenRemitente.selectedIndex=0;

		document.all.txtAreaRemitente.selectedIndex=0;
		document.all.txtUsuarioRemitente.selectedIndex=0;
		document.all.txtOrigenRemitente.selectedIndex=0;
		asignarElemento_HVS(document.all.txtOrigenRemitente,"","",true,false);

		// BORRA LOS ELEMENTOS DE LAS LISTAS Y POSICIONA EN LA OPCION SELECCIONE...

		asignarElemento_HVS(document.all.txtObjeto,"","",true,false);
		asignarElemento_HVS(document.all.txtUnidades,"","",true,false);

		// QUITA LA SELECCIÓN AL ÚNICO CHECK DE LA PANTALLA	

		//document.all.chkConAsf.checked=0;

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

	function validarAsunto(){

		if (document.all.txtTipoDocto.selectedIndex==0){ 
			alert("Debe seleccionar el TIPO de Documento.");
			document.all.txtTipoDocto.focus();
			return false;
		}	
		if (document.all.txtNumeroDocto.value == ""){
			alert("Debe ingresar el NÚMERO del DOCUMENTO.");
			document.all.txtNumeroDocto.focus();
			return false;
		}	
		if (document.all.txtFechaDocto.value == "" ){
			alert("Debe ingresar la fecha DOCUMENTO.");
			document.all.txtFechaDocto.focus();
			return false;
		}	
		if (document.all.txtFechaRecepcion.value == "" ){
			alert("Debe ingresar la fecha de RECEPCIÓN.");
			document.all.txtFechaRecepcion.focus();
			return false;
		}	
		if (document.all.txtFechaCompromiso.value == "" ){
			alert("Debe ingresar la fecha COMPROMISO.");
			document.all.txtFechaCompromiso.focus();
			return false;
		}	
		if (document.all.txtClasIndices.selectedIndex==0){ 
			alert("Debe seleccionar una CLASIFICACIÓN de Indices.");
			document.all.txtClasIndices.focus();
			return false;
		}	
		if (document.all.txtIndice.selectedIndex==0){ 
			alert("Debe seleccionar un INDICE.");
			document.all.txtIndice.focus();
			return false;
		}	
		if (document.all.txtPrioridad.selectedIndex==0){ 
			alert("Debe seleccionar una PRIORIDAD.");
			document.all.txtPrioridad.focus();
			return false;
		}	
		if (document.all.txtImpacto.selectedIndex==0){ 
			alert("Debe seleccionar un IMPACTO.");
			document.all.txtImpacto.focus();
			return false;
		}	
		if (document.all.txtConfidencial.selectedIndex==0){ 
			alert("Debe seleccionar la CONFIDENCIALIDAD.");
			document.all.txtConfidencial.focus();
			return false;
		}	
		if (document.all.txtOrigenRemitente.selectedIndex==0){ 
			alert("Debe seleccionar el Origen del REMITENTE.");
			document.all.txtOrigenRemitente.focus();
			return false;
		}	
		if (document.all.txtAreaRemitente.selectedIndex==0){ 
			alert("Debe seleccionar el Área del REMITENTE.");
			document.all.txtAreaRemitente.focus();
			return false;
		}	
		if (document.all.txtUsuarioRemitente.selectedIndex==0){ 
			alert("Debe seleccionar el Usuario REMITENTE.");
			document.all.txtUsuarioRemitente.focus();
			return false;
		}	
		if (document.all.txtDestinatarios.length == 0){
			alert("Debe seleccionar a los DESTINATARIOS.");
			document.all.btnVerDestinatarios.focus();
			return false;
		}	
		return true;
	}

	function validarAuditoria(){
		  
		
		if (document.all.txtTipoDoctoAuditoria.selectedIndex==0){ 
			alert("Debe seleccionar el TIPO de la Auditoría.");
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
			alert("Debe seleccionar el campo SUJETOS de la auditoría.");
			document.all.txtUnidades.focus();
			return false;
		}	
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
					} else{
						 alert("ATENCION: Ya existe el Criterio seleccionado " + " \n en la auditoría: " + sAuditoria + " \npor favor verifique.");
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
		
			
			setGrafica(chart1, "dpsAuditoriasByArea", "pie", "Auditorias", "canvasJG" );
			//setGrafica(grAvances1, "dpsAuditoriasByArea", "pie", "Auditorias en la Dir. Gral.", "CanvasGrafica1" );			

		
	
	};

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
			}
		}
		// Verifica cambios en las opciones de Ingresos
		if (lstIngresos.length > 0){
			actualizaIngresosEnArreglo();
		}
		// Verifica cambios en las opciones de Consolidados
		//alert("lstConsolidados.length: " + lstConsolidados.length);
		if (lstConsolidados.length > 0){
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

	function recuperarListaSelected_HVS(lista, parametro, cmb, id ){
		var listaLocal = '';
		if (parametro.length > 0) { listaLocal = '/' + lista + '/' + parametro; }else{ listaLocal = '/' + lista;}
		$.ajax({
			type: 'GET', url: listaLocal ,
			success: function(response) {
				var jsonData = JSON.parse(response);
			
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
					if (dato.id==id) option.selected = true;
					cmb.add(option, i+1);
				}				
			},
			error: function(xhr, textStatus, error){
				alert("ERROR EN: function recuperarListaSelected_HVS(lista, parametro, cmb, id)" + " Donde lista=" + lista );
			}			
		});	
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

	//******************** Procesos para Asuntos
	function recuperaIndices(valor){
		
		if( document.all.txtClasIndices.selectedIndex>0 ){			
			recuperarListaLigada('lstIndicesBytipo', valor, document.all.txtIndice);
		}
	}

	function recuperaAreasRemitente(valor){
		//alert("El valor de valor es: " + valor);
		if( document.all.txtOrigenRemitente.selectedIndex>0 ){			
			if (document.all.txtOrigenRemitente.options[document.all.txtOrigenRemitente.selectedIndex].value == "INTERNO"){
				recuperarLista('lstAreas_HVS', document.all.txtAreaRemitente);
			}else{
				recuperarLista('lstEntidadesExternas', document.all.txtAreaRemitente);
			}
		}
	}

	function recuperaUsuariosRemitente(valor){
		//alert("El valor de valor es: " + valor);
		if( document.all.txtAreaRemitente.selectedIndex>0 ){			
			if (document.all.txtOrigenRemitente.options[document.all.txtOrigenRemitente.selectedIndex].value == "INTERNO"){
				recuperarListaLigada('lstUsuariosByArea', valor, document.all.txtUsuarioRemitente);
			}else{
				recuperarListaLigada('lstUsuariosExternosByEntidadExt', valor, document.all.txtUsuarioRemitente);
			}
		}
	}


	function recuperaDestinatariosInternos(valor){
		//alert("El valor de valor es: " + valor);
		
		if( document.all.txtAreasDestinatariasInternas.selectedIndex>0 ){			
			// Llena la tabla con la listas de usuarios-empleados del áreas seleccionada y que se encuentra su id en parametro valor.

			recuperarTablaDestinatariosFromArreglo('lstUsuariosByArea', valor, document.all.tblDestinatariosInternos);
		    //recuperarListaLigada('lstUsuariosByArea', valor, document.all.txtUsuarioDestinatarioInterno);
		}
	}


	function mueveDestinatariosSeleccionados(cmb){
		var nEleCombo = cmb.options.length;
		var nEleArreglo = lstDestinatariosInternos.length;

		//alert("El número de elementos en el arreglo lstDestinatariosInternos es de :" + nEleArreglo );
		// Elimna los elementos del combo de destinatarios internos.
		if (nEleCombo > 0){
			// Elimina los elementos del combo destino
		    while (cmb.options.length > 0) {                
		        cmb.remove(0);
		    }	
		}
		      
	    // ahora movera los elementos del arreglo al combo de destinatarios seleccionados
		for( var i= 0; i < nEleArreglo; i++){

			valor = lstDestinatariosInternos[i][0];
			if (lstDestinatariosInternos[i][5] == true){
				texto = "Atención     - " + lstDestinatariosInternos[i][3] + " - " + lstDestinatariosInternos[i][4] + " - " + lstDestinatariosInternos[i][2];
			}else{
				texto = "Conocimiento - " + lstDestinatariosInternos[i][3] + " - " + lstDestinatariosInternos[i][4] + " - " + lstDestinatariosInternos[i][2];
			}

			//alert( "El valor de valor es: " + valor + " El valor de texto es: " + texto);

			var option = document.createElement("option");
			option.value = valor;
			option.text = texto;
			cmb.add(option, i+1);
		}
	}
	
	function mueveDestinatariosInternos(cmb){
		// Agregara al combo uno a uno los destinatarios seleccionados de la tabla de destinatarios 
		// previo validara que no existan antes en el combo
		var nElementos = lstDestinatariosInternos.length;
		var nElementosCmb = cmb.length;
		var valorElemento = 0;
		var bYaExiste = false;

		// PRIMERO AGREGA LOS ELEMENTOS DEL TABLE AL COMBO, VALIDANDO QUE NO EXISTAN PREVIAMENTE
		if (nElementos > 0 ){

			for (var i = 0; i < nElementos; i++) {
				arrEle = lstDestinatariosInternos[i];
				valorElemento = arrEle[0];

				//alert("El valor de arrEle[4] es: " + arrEle[4]);
				//alert("El valor de arrEle[5] es: " + arrEle[5]);

				if (nElementosCmb > 0){
					bYaExiste = false;
					for(var x = 0; x < nElementosCmb; x++){
						arrEleCmb = cmb.options[x].value;
						if (valorElemento == arrEleCmb){
							bYaExiste = true;
							break;
						}
					}
					if (!bYaExiste){
						var option = document.createElement("option");
						option.value = arrEle[0];

						if (arrEle[5]){
							option.text = "ATENCIÓN : " + arrEle[3] + " - " + arrEle[4] + " - " + arrEle[2];
						}else{
							option.text = "CONOCIMIENTO : " + arrEle[3] + " - " + arrEle[4] + " - " + arrEle[2];
						}
						cmb.add(option, nElementosCmb+1);
					}
				}else{
					var option = document.createElement("option");
					option.value = arrEle[0];
					if (arrEle[5]){
						option.text = "ATENCIÓN : " + arrEle[3] + " - " + arrEle[4] + " - " + arrEle[2];
					}else{
						option.text = "CONOCIMIENTO : " + arrEle[3] + " - " + arrEle[4] + " - " + arrEle[2];
					}
					cmb.add(option, nElementosCmb+1);
				}
			}
			ordenarListaByTexto(cmb);
		}
		/*
		// SEGUNDO QUITARA DEL COMBO LOS ELEMENTOS DEL TABLE QUE YA NO TENGAN LA SELECCIÓN DE ATENCION Y CONOCIMIENTO
		if (nElementosCmb > 0){
			for (var i = 0; i < nElementosCmb; i++){
				valorElemento = cmb.options[i].value;
				if (nElementos > 0){
					bYaExiste = false;
					for (var x = 0; x < nElementos; x++){
						arrEle = lstDestinatariosInternos[x];
						if (valorElemento == arrEle[0]){
							bYaExiste = true;
							break;
						}
					}
					if (!bYaExiste){  // El elemento del combo ya no esta seleccionado en el arreglo, entonces se quitara.
						lstDestinatariosInternos.splice(i, 1);
					}
				}
			}
			ordenarListaByTexto(cmb);
		}*/
	}

	function copiarCombos(cmbOrigen, cmbDestino){
		var nElementosCmbOrigen =  cmbOrigen.options.length;
		var nElementosCmbDestino = cmbDestino.options.length;
		var texto;
		var valor;

		//alert("El valor de nElementosCmbOrigen es: " + nElementosCmbOrigen);
		//alert("El valor de nElementosCmbDestino es: " + nElementosCmbDestino);

		if (nElementosCmbOrigen > 0 ){
			// Elimina los elementos del combo destino
		    while (cmbDestino.options.length > 0) {                
		        cmbDestino.remove(0);
		    }      
		    // ahora movera los elementos del combo origen al destino
			for( var i= 0; i< nElementosCmbOrigen; i++){

					valor = cmbOrigen.options[i].value;
					texto = cmbOrigen.options[i].text;

					//alert( "El valor de valor es: " + valor + " El valor de texto es: " + texto);

					var option = document.createElement("option");
					option.value = valor;
					option.text = texto;
					cmbDestino.add(option, i+1);
			}
		}
	}

	function  FiltraDestinatariosInternos(){

		var tablaAsuntos = document.getElementById("tblDestinatariosInternos");
		var nRenglones;

		//var textoBuscado = document.getElementById('txtAuditoria').value.toLowerCase();
		var textoBuscado = document.all.txtAreasDestinatariasInternas.options[document.all.txtAreasDestinatariasInternas.selectedIndex].text.toLowerCase();
		var celdas=""
		var localizado=false;
		var found=false;
		var comparaCon="";

		if (textoBuscado == "Todas las Áreas...".toLowerCase() ) { textoBuscado = "e"; }

		//alert("El valor de textoBuscado es: " + textoBuscado);

		//alert("Numero de renglones: " + tablaAsuntos.rows.length);

		for (var i = 0; i < tablaAsuntos.rows.length; i++){
			celdas = tablaAsuntos.rows[i].getElementsByTagName('td');
			localizado = false;
			// Recorremos todas las celdas
			for (var j = 0; j < celdas.length && !localizado; j++){
				comparaCon = celdas[j].innerHTML.toLowerCase();
				// Buscamos el texto en el contenido de la celda
				if (textoBuscado.length == 0 || (comparaCon.indexOf(textoBuscado) > -1))
				{
					localizado = true;
				}
			}

			if(localizado){
				tablaAsuntos.rows[i].style.display = '';
			} else {
				// si no ha encontrado ninguna coincidencia, esconde la
				// fila de la tabla
				tablaAsuntos.rows[i].style.display = 'none';
			}
		}
	}

	function seleccionaOpcionTexto(combo,val)
	{
	    for(var indice=0; indice < combo.length; indice++)
	    {
	        if (combo.options[indice].text==val )
	            combo.selectedIndex =indice;
	    }     
	}

	function validarFormatoFecha(campo) {
		var RegExPattern = /^\d{1,2}\/\d{1,2}\/\d{2,4}$/;
		if ((campo.match(RegExPattern)) && (campo!='')) {
		    return true;
		} else {
		    return false;
		}
	}

	function existeFecha(fecha) {
    	var fechaf = fecha.split("/");
    	var day = fechaf[0];
    	var month = fechaf[1];
    	var year = fechaf[2];
    	var date = new Date(year,month,'0');
    	if((day-0)>(date.getDate()-0)){
        	return false;
      	}
      	return true;
  	}

  	function FnBorrarCombo(cmb){
		while (cmb.length>=1){
			cmb.remove(cmb.length-1);
       	}
  	}

  	function fnAgregarEnComboDesdeArreglo(cmb, aLst, nId, nTexto){
		for(var i = 0; i < aLst; i++){
			var option = document.createElement("option");
			option.text = aLst[i][nTexto];
			option.value = aLst[i][nId];
			cmb.add(option, i);
		}

  	}

  	function GuardarAsunto(sAccion){
  		if(sAccion == 'SOLOGUARDAR') 
  		{ 
  		  document.all.txtAccion.value = sAccion;  
  		}else{

	  		if(sAccion == 'GUARDARTURNAR') 
	  		{ 
	  		  document.all.txtAccion.value = sAccion;  
	  		}else{
				sAccion = 'SOLOTURNAR';	  			
	  		}
  		}
  		////////alert('El valor de sAccion es: ' + sAccion + ' el tipo de operación es: ' + document.all.txtOperacion.value);

		if (validarAsunto()){
			// Inicio del poceso para poder comunicar el contenido del arreglo lstDestinatariosInternos en el proceso submit.
			var sListaDestinatarios = "";
			var sListaArchivosAnexos = "";
			var sTipoAtencion;
			///////alert("El numero de registros en lstDestinatariosInternos es: " + lstDestinatariosInternos.length);

			if(lstDestinatariosInternos.length > 0){ 
				separador = "";
				for(i=0; i < lstDestinatariosInternos.length; i++){
					sTipoAtencion = "";
					if(lstDestinatariosInternos[i][5] == true){ sTipoAtencion = "ATENCION"; } else { sTipoAtencion = "CONOCIMIENTO"; }
					sListaDestinatarios = sListaDestinatarios + separador + lstDestinatariosInternos[i][0] + '|' + lstDestinatariosInternos[i][1] + '|' + lstDestinatariosInternos[i][2] + '|' + lstDestinatariosInternos[i][3] + '|' + lstDestinatariosInternos[i][4] + '|' + sTipoAtencion;
						separador = '*';
					}
			}
			document.all.txtControlDestinatarios.value = sListaDestinatarios;

			////////alert(" El valor de sListaDestinatarios: " + sListaDestinatarios);
			document.getElementById("btnGuardar").disabled=true;
			document.all.txtNombreUsrRemitente.value = document.all.txtUsuarioRemitente.options[document.all.txtUsuarioRemitente.selectedIndex].text;
			document.all.txtNombreAreaRemitente.value = document.all.txtAreaRemitente.options[document.all.txtAreaRemitente.selectedIndex].text;

			document.all.txtTipoOrigenDocto.value = document.all.txtOrigenRemitente.options[document.all.txtOrigenRemitente.selectedIndex].value;
			document.all.txtAccion = 'SOLOGUARDAR';

			if(lstArchivosAnexos.length > 0){ 
				separador = "";
				for(i=0; i < lstArchivosAnexos.length; i++){
					sListaArchivosAnexos = sListaArchivosAnexos + separador + lstArchivosAnexos[i][0] + '|' + lstArchivosAnexos[i][1] + '|' + lstArchivosAnexos[i][2] + '|' + lstArchivosAnexos[i][3] + '|' + lstArchivosAnexos[i][4];
						separador = '*';
					}
			}
			document.all.txtControlArchivosAnexos.value = sListaArchivosAnexos;

			document.all.formulario.submit();
		}
  	}


	function agregarAsunto(){
		//document.getElementById("btnGuardar").disabled=false;
		document.getElementById('btnGuardarAsunto').style.display = 'inline';	
		document.getElementById('btnGuardar').style.display = 'inline';					
		document.getElementById('btnTurnarAsunto').style.display = 'none';					
		document.getElementById('btnQuitarDestinatarios').style.display = 'inline';			
		document.getElementById('btnLimpiarDestinatarios').style.display = 'inline';	
		document.getElementById('btnVerDestinatarios').style.display = 'inline';		
		document.getElementById('btnCargarArchivo').style.display = 'inline';	
		document.getElementById('btnAgregarArchivosAnexos').style.display = 'inline';	
		document.getElementById('btnQuitarArchivosAnexos').style.display = 'inline';	
	
		document.all.listaAsuntos.style.display='none';
		document.all.capturaAsunto.style.display='inline';
		document.all.txtOperacion.value='INS';		
       	lstDestinatariosInternos = [];
       	lstArchivosAnexos = [];
		limpiarCamposAsuntos();
		/////document.all.botonAsignarCriterios.style.display='none';
		/////limpiarCamposAuditoria();
		/////limpiaTabla(document.all.tblListaCriterios);
		/////limpiaTabla(document.all.tblListaArchivos);
		//asignarElemento(document.all.txtObjeto,"","");
	}

	function recuperaAsunto(id){

		var listaDestina = "";
		lstDestinatariosInternos = [];
		lstArchivosAnexos = [];
		$.ajax({
			type: 'GET', url: '/obtenerAsunto/' + id ,
			success: function(response) {
				var obj = JSON.parse(response);

				limpiarCamposAsuntos();
				// Recupera los valores del asunto desde la base de datos.
				document.all.txtFolio.value = '' + obj.idAsunto;
				seleccionarElemento(document.all.txtTipoDocto, obj.idTipoDocto);
				document.all.txtNumeroDocto.value = '' + obj.NumDocto;

				document.all.txtFechaDocto.value = obj.fecDocto;
				seleccionarElemento(document.all.txtClasIndices, obj.idTipoIndice);
				seleccionarElemento(document.all.txtPrioridad, obj.idTipoPrioridad);

				document.all.txtFechaRecepcion.value = obj.fecRecepcion;
				recuperarListaSelected('lstIndicesBytipo', obj.idTipoIndice, document.all.txtIndice, obj.idIndice);
				//seleccionarElemento(document.all.txtIndice, obj.idIndice);
				seleccionarElemento(document.all.txtImpacto, obj.idTipoImpacto);

				document.all.txtFechaCompromiso.value = obj.fecCompromiso;
				seleccionarElemento(document.all.txtClasificacionDocto, obj.idClasificacionDocto);
				seleccionarElemento(document.all.txtConfidencial, obj.idTipoConfidencial);

				seleccionarElemento(document.all.txtAuditoria, obj.idAuditoria);
				document.all.txtDescripcion.value = obj.descripcion;
				document.all.txtComentario.value = obj.comentario;

				seleccionarElemento(document.all.txtOrigenRemitente, obj.tipoOrigenRemitente);
				//alert(" El valor de tipoOrigenRemitente: " + obj.tipoOrigenRemitente);
				/*
				recuperaAreasRemitente(obj.tipoOrigenRemitente);
				seleccionarElemento(document.all.txtAreaRemitente, obj.idAreaRemitente);
				recuperaUsuariosRemitente(obj.idAreaRemitente);
				seleccionarElemento(document.all.txtUsuarioRemitente, obj.idUsrAreaRemitente);
				*/


				if(obj.tipoOrigenRemitente == 'INTERNO' ){
					recuperarListaSelected_HVS('lstAreas_HVS', "", document.all.txtAreaRemitente, obj.idAreaRemitente);
					recuperarListaSelected('lstUsuariosByArea', obj.idAreaRemitente, document.all.txtUsuarioRemitente, obj.idUsrAreaRemitente);
					//alert("El valor de obj.idUsrAreaRemitente es:" + obj.idUsrAreaRemitente);
				}else{
					recuperarListaSelected('lstEntidadesExternas', obj.tipoOrigenRemitente, document.all.txtAreaRemitente, obj.idAreaRemitente);
					//recuperarListaSelected('lstIndicesBytipo', obj.idAreaRemitente, document.all.txtUsuarioRemitente, obj.idUsrAreaRemitente);
				}
				

				document.all.txtExpediente.value = obj.NumExpediente;
				document.all.txtSerieDocumental.value = obj.serieDocumental;
				document.all.txtClaveBusqueda.value = obj.claveBusqueda;
				document.all.txtSituacion.value = obj.idSituacion;

				// Recuperación de Destinatarios si existen y dependiendo de la situacion del asunto.
				if( obj.idSituacion == 'TURNADO'){
					document.getElementById('btnAgregarArchivosAnexos').style.display = 'none';	
					document.getElementById('btnQuitarArchivosAnexos').style.display = 'none';	
					listaDestina = '/obtenerDestinatariosTurnados/';
				}else{
					document.getElementById('btnAgregarArchivosAnexos').style.display = 'inline';	
					document.getElementById('btnQuitarArchivosAnexos').style.display = 'inline';	
					listaDestina = '/obtenerDestinatariosTemporales/';
				}

				$.ajax({
					type: 'GET', url: listaDestina + obj.idAsunto ,
					success: function(response) {
						var jsonData = JSON.parse(response);

					//alert( "Response=> " + response);

					for (var i = 0; i < jsonData.datos.length; i++) {
						var dato = jsonData.datos[i];
						var option = document.createElement("option");
						option.text = dato.tipoAtencion + " : " + dato.nombreDestinatario + "  -  (" +  dato.plazaDestinatario + ")" ;
						option.value = dato.idUsrDestinatario;
						//if (dato.id==id) option.selected = true;
						document.all.txtDestinatarios.add(option, i+1);
						
						if(dato.tipoAtencion == "ATENCION") { 
							atencion = true; 
							conocimiento = false; 
						}else{ 
							atencion = false;
							 conocimiento = true; 
						}
						
						////////alert(">> " + dato.idUsrDestinatario + ">> " + dato.idAreaDestinatario + ">> " + dato.nombreAreaDestinatario + ">> " + dato.nombreDestinatario + ">> " + dato.plazaDestinatario + ">> " + atencion + ">> " + conocimiento);

						lstDestinatariosInternos.push(new Array(dato.idUsrDestinatario, dato.idAreaDestinatario, dato.nombreAreaDestinatario, dato.nombreDestinatario, dato.plazaDestinatario, atencion, conocimiento) );							
					}				
				},
					error: function(xhr, textStatus, error){
						alert('function ' + listaDestina + ' -> statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error + ' Asunto: ' + obj.idAsunto);
					}			
				});		

				document.getElementById("btnGuardar").disabled=false;
		       	lstDestinatariosInternos = [];
				document.all.txtOperacion.value='UPD';

				// Control de botones dependiendo del usuario, la situación del asunto y si el usuario es generador del asunto o es quien debe
				// atender el asunto.

				var idUsrFirmado = "<?php echo $_SESSION ["idUsuario"]; ?>";
				//alert("El identidicador del usuario firmado es: " + identificador);

				if (obj.usrAlta == idUsrFirmado){
					if( obj.idSituacion == 'TURNADO'){
						document.getElementById('btnGuardarAsunto').style.display = 'none';					
						document.getElementById('btnGuardar').style.display = 'none';					
						document.getElementById('btnTurnarAsunto').style.display = 'none';	
						document.getElementById('btnQuitarDestinatarios').style.display = 'none';			
						document.getElementById('btnLimpiarDestinatarios').style.display = 'none';	
						document.getElementById('btnVerDestinatarios').style.display = 'none';		
						document.getElementById('btnCargarArchivo').style.display = 'inline';		
					}else{
						document.getElementById('btnGuardarAsunto').style.display = 'inline';	
						document.getElementById('btnGuardar').style.display = 'inline';					
						document.getElementById('btnTurnarAsunto').style.display = 'none';	// No se debe poder turnar si no se ha guardado
						document.getElementById('btnQuitarDestinatarios').style.display = 'inline';			
						document.getElementById('btnLimpiarDestinatarios').style.display = 'inline';	
						document.getElementById('btnVerDestinatarios').style.display = 'inline';		
						document.getElementById('btnCargarArchivo').style.display = 'inline';		
					}
				}

				document.all.listaAsuntos.style.display='none';
				document.all.capturaAsunto.style.display='inline';
		},
			error: function(xhr, textStatus, error){
				alert('function recuperaAsunto()   -> statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error + ' Asunto: ' + id);
			}			
		});		
	}

	
    function recuperarTablaAsuntosTurnados(lista, valor, tbl){
		var liga;

		if (valor != "") { liga = '/' + lista + '/' + valor; }else{ liga = '/' + lista; }

		///alert("El valor de la liga: " + liga);

        $.ajax({
	        type: 'GET', url: liga ,
	        success: function(response) {
               var jsonData = JSON.parse(response);                                 
                              
                //Vacia la lista
                tbl.innerHTML="";

                //Agregar renglones
                var renglon, columna;
               
                for (var i = 0; i < jsonData.datos.length; i++) {
	                var dato = jsonData.datos[i];                                                                    

	                renglon=document.createElement("TR");     
					renglon.style = "width: 100%; font-size: xx-small";
	                renglon.innerHTML= "" +
					'<td width="08%">' + dato.folio + "</td>" +
					'<td width="08%">' + dato.numero + "</td>" +
					'<td width="08%">' + dato.tipoDocumento + "</td>" +
					'<td width="15%">' + dato.descripcion + "</td>" +
					'<td width="08%">' + dato.tipoOrigenDocto + "</td>" +
					'<td width="15%">' + dato.clasificacion + "</td>" +
					'<td width="08%">' + dato.tipoAtencion + "</td>" +
					'<td width="08%">' + dato.prioridad + "</td>" +
					'<td width="08%">' + dato.situacion + "</td>" +
					'<td width="08%">' + dato.fechaDocto + "</td>" +
					'<td width="08%">'  + dato.fechaCompro + "</td>" +
					"<td><a href=''>"  + '<?php echo obtenerGifEstatus("  + dato.estatus + "); ?>' + "</a></td>";
					//"<td><a href=''>"  + rutaImagen + "</a></td>";
					 

	                renglon.onclick= function() 
	                {
	                	recuperaAsunto(dato.asunto);
	                	/*
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
	       				*/

	                };
	                tbl.appendChild(renglon);                                                                       
                }                                                              
           },
           error: function(xhr, textStatus, error){
                alert('function recuperarTablaCriterios ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
           }                                             
		});           
    }              

	function mostrarAsuntos(situacion){
		if(situacion=="EN REGISTRO"){
  			document.getElementById('btnAgregarAsunto').style.display = 'inline';

  		}else{
	  		document.getElementById('btnAgregarAsunto').style.display = 'none';
  		}
	}

	function llenarCmbArchivosAnexos(){

		if(document.all.txtFolio.value.length > 0){

			liga = '/obtenerArchivosAnexosByOrigenIdOrigen/ASUNTO/';
			valor = document.all.txtFolio.value;
		
			//alert("El valor de liga + valor es: " + liga + valor);

			$.ajax({
				type: 'GET', url: liga + valor ,
				success: function(response) {
					var jsonData = JSON.parse(response);

				//alert( "Response=> " + response);
				//alert("El Valor de lstArchivosAnexos.length es: " + lstArchivosAnexos.length);

				if (lstArchivosAnexos.length > 0){

					for (var i = 0; i < jsonData.datos.length; i++) {
						var dato = jsonData.datos[i];
						lEncontro = false;

						for (j = 0; j < lstArchivosAnexos.length; j++){
							arrEle = lstArchivosAnexos[j];

							//alert("dato.archivoOriginal => " + dato.archivoOriginal + " arrEle[3] => " + arrEle[3] + "dato.archivoFinal => " + dato.archivoFinal + " arrEle[3] => " + arrEle[3] );
							
							if(dato.archivoOriginal==arrEle[3] && dato.archivoFinal==arrEle[4]){
								lEncontro = true;
							}
						}
						if (!lEncontro){
							var option = document.createElement("option");
							option.text = dato.archivoOriginal;
							option.value = dato.id;
							document.all.txtArchivosAnexos.add(option, i+1);
							// Agregar a lista de archivos anexos
							lstArchivosAnexos.push(new Array(dato.id, dato.origen, dato.idOrigen, dato.archivoOriginal, dato.archivoFinal));
						}
					}				

				}else{

					for (var i = 0; i < jsonData.datos.length; i++) {
						var dato = jsonData.datos[i];
						var option = document.createElement("option");
						option.text = dato.archivoOriginal;
						option.value = dato.id;
						document.all.txtArchivosAnexos.add(option, i+1);
						// Agregar a lista de archivos anexos
						lstArchivosAnexos.push(new Array(dato.id, dato.origen, dato.idOrigen, dato.archivoOriginal, dato.archivoFinal));
					}				
				}

			},
				error: function(xhr, textStatus, error){
					alert('function llenarCmbArchivosAnexos -> statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error + ' Asunto: ' + dato.idOrigen);
				}			
			});		
		}
	}


	function registrarArchivosAnexos(){
		// Esta función se mantiene para que se muestre como controlar la opcion de cerrado de una
		// ventana modal, verificar de donde se llamo.
	}

	function CancelarGuardarAsunto(){

		if (document.all.txtOperacion.value == "INS"){
			for (i = 0; i < lstArchivosAnexos.length; i++){

				archivoFinal = lstArchivosAnexos[i][4];
				alert("El valor de archivoFinal es: " + archivoFinal);
				borrarArchivoAnexo(archivoFinal, false);
			}
		}else{
			if (document.all.txtOperacion.value == "UPD"){
				// Debido a que se cancela el guardado se validara si de los archivos en el arreglo lstArchivosAnexos ya estan 
				// registrados para el asunto, si es así, dejará dichos archivos en la carpeta física /asuntos de los contrario, 
				// de dicha carpeta se borraran los archivos no registrados
				if (lstArchivosAnexos.length > 0){

					liga = '/obtenerArchivosAnexosByOrigenIdOrigen/ASUNTO/';
					valor = document.all.txtFolio.value;
				
					//alert("El valor de liga + valor es: " + liga + valor);

					$.ajax({
						type: 'GET', url: liga + valor ,
						success: function(response) {
						var jsonData = JSON.parse(response);

						//alert("El valor de lstArchivosAnexos.length es: " + lstArchivosAnexos.length  + " y de jsonData.datos.length es: " + jsonData.datos.length);

						for (i = 0; i < lstArchivosAnexos.length; i++){
							arrEle = lstArchivosAnexos[i];
							lEncontro = false;

							for (j = 0; j < jsonData.datos.length; j++) {
								var dato = jsonData.datos[j];
							
								if(arrEle[4] == dato.archivoFinal){
									lEncontro = true;
									break;
								}
							}
							if (!lEncontro) { borrarArchivoAnexo(arrEle[4], false); }
						}
					},
						error: function(xhr, textStatus, error){
							alert('function llenarCmbArchivosAnexos -> statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error + ' Asunto: ' + dato.idOrigen);
						}			
					});		
				}

			}
		}
	}

	//*******************************************

	$(document).ready(function(){

		getMensaje('txtNoti',1);

		document.getElementById('btnUpload').addEventListener('change', abrirArchivoAnexo, false);

		document.getElementById('btnAgragarArchivosX').addEventListener('click', function(ev) {
           $("#btnContinuarArchivosAnexos").click(); 

		});


		document.getElementById('capturaAsunto').addEventListener('onBlur', function(ev) {
           alert("Es a punto de perder los cambios, desea continuar?"); 

		});


		//document.getElementById('btnDelete').addEventListener('change', borrarArchivoAnexo, false);

		recuperarListaLigada('lstTiposDocumentosByTipo','INTERNO',document.all.txtTipoDocto);

		recuperarLista('lstTiposPrioridades',document.all.txtPrioridad);

		recuperarLista('lstClasificacionesDocto', document.all.txtClasificacionDocto);
		seleccionarElemento(document.all.txtClasificacionDocto, 'DOCUMENTO GENERAL');

		recuperarLista('lstTiposIndices', document.all.txtClasIndices);

		recuperarLista('lstAuditoriasToAsuntos', document.all.txtAuditoria);

		recuperarLista('lstAreas_HVS', document.all.txtAreasDestinatariasInternas);

		recuperarTablaAsuntosTurnados('asuntosBySituacion','TURNADO',document.all.tblAsuntosTurnados);


		// ************************************* PARA MODAL DE OBJETOS ****************************************************
		//alert('Antes de cargar las unidades');
		// ****************************************************************************************************************

		//$("#tblGasto").hide();
		/*
		( "#tblAsuntosTurnados" ).click(function() {
			alert("#tblAsuntosTurnados");
			document.getElementById('btnAgregarAsunto').style.display = 'none';
		});
		*/

		// -------- BOTONES SE SECCIÓN DE ARCHIVOS ANEXOS
		$( "#btnAgregarArchivosAnexos" ).click(function() { 
			$("#btnUpload").click();
		});

		$( "#btnQuitarArchivosAnexos" ).click(function() { 
			if( lstArchivosAnexos.length > 0){
				if(document.all.txtArchivosAnexos.selectedIndex > -1 ){
					arcValor = document.all.txtArchivosAnexos.options[document.all.txtArchivosAnexos.selectedIndex].value;	
					arcTexto = document.all.txtArchivosAnexos.options[document.all.txtArchivosAnexos.selectedIndex].text;	

					confirmacion = confirm("¿Esta seguro que desea quitar el siguiente archivo?:\n \n" + arcTexto);
					if (confirmacion == true){ 	
						//$("#btnDelete").click();
						for (var i = 0; i < lstArchivosAnexos.length; i++) {
							arrEle = lstArchivosAnexos[i];
							if( arrEle[3] == arcTexto ){
								archivoFinal = arrEle[4];
								//alert("El archivo a borrar es posicion:" + i + " Nombre Original: " + arrEle[3] + " Nombre Final es: " + arrEle[4] +  " Nombre Final es: " + archivoFinal);
								borrarArchivoAnexo(archivoFinal, true);
								lstArchivosAnexos.splice(i,1);
								// Ahora se limpiara el combobox 
								while (document.all.txtArchivosAnexos.length>0){
									document.all.txtArchivosAnexos.remove(document.all.txtArchivosAnexos.length-1);
								}
								// Se vuelve a llenar el combobox con los elementos del arreglo
								for(var y = 0; y < lstArchivosAnexos.length; y++){
									var option = document.createElement("option");
									option.text = lstArchivosAnexos[y][3];
									option.value = lstArchivosAnexos[y][0];
									document.all.txtArchivosAnexos.add(option, y);
								}
								break;
							}
						}
					}

				}
			}
		});

		$( "#btnCargarArchivo" ).click(function() { 
			//lstArchivosAnexos = [];
			llenarCmbArchivosAnexos();
			// Carga la pantalla modal con los datos para los archivos anexos
			$('#modalCargarArchivos').removeClass("invisible");
			$('#modalCargarArchivos').modal('toggle');
			$('#modalCargarArchivos').modal('show');
		});

		$( "#btnCancelarArchivosAnexos" ).click(function() {
			$('#modalCargarArchivos').modal('hide');			
		});		

		$( "#btnContinuarArchivosAnexos" ).click(function() {
			registrarArchivosAnexos();
			$('#modalCargarArchivos').modal('hide');			
		});		


		// -------- BOTONES SE SECCIÓN DE ARCHIVOS ANEXOS

		$( "#btnGuardarDestinatarios" ).click(function() {
			////copiarCombos(document.all.txtUsuariosDestinatariosInternos, document.all.txtDestinatarios);
			mueveDestinatariosInternos(document.all.txtDestinatarios);
			$('#modalDestinatarios').modal('hide');			
		});

		$( "#btnCancelarDestinatarios" ).click(function() {
			$('#modalDestinatarios').modal('hide');			
		});		


		$( "#btnQuitarDestinatarios" ).click(function() {
			var desValor;
			var desTexto;
			var confirmacion;

			if(document.all.txtDestinatarios.selectedIndex >=0){
				
				desValor = document.all.txtDestinatarios.options[document.all.txtDestinatarios.selectedIndex].value;	
				desTexto = document.all.txtDestinatarios.options[document.all.txtDestinatarios.selectedIndex].text;	
				confirmacion = confirm("¿Esta seguro que desea quitar el siguiente destinatario?:\n \n" + desTexto);
				
				if (confirmacion == true){ 	

					for (var i = 0; i < lstDestinatariosInternos.length; i++) {
						arrEle = lstDestinatariosInternos[i];

						//alert("El valor desValor es: " + desValor + " El valor arrEle[0] es: " + arrEle[0]);

						if( arrEle[0] == desValor ){
							//alert("Se borrara el dato de posición No. " + i);
							//alert("Quien es :" + lstDestinatariosInternos[i][2]);
							//alert("El número de elementos en el arreglo es de: " + lstDestinatariosInternos.length );
							lstDestinatariosInternos.splice(i,1);
							//alert("El número de elementos en el arreglo es de: " + lstDestinatariosInternos.length );
							FnBorrarCombo(document.all.txtDestinatarios);
							mueveDestinatariosInternos(document.all.txtDestinatarios);
							break;
						}
					}
				}
			}
		});		

		$( "#btnLimpiarDestinatarios" ).click(function() {
			var desValor;
			var desTexto;
			var confirmacion;

			if(document.all.txtDestinatarios.options.length > 0)
			{
				confirmacion = confirm("¿Esta seguro que desea quitar la lista completa de destinatarios seleccionados?:\n");
				
				if (confirmacion == true){ 	

					lstDestinatariosInternos=[];
					FnBorrarCombo(document.all.txtDestinatarios);
					mueveDestinatariosInternos(document.all.txtDestinatarios);
				}
			}
		});		

		/*
		// Botón que guarda el Asunto
		$( "#btnGuardar" ).click(function() {

			//alert("Antes de validar");
			if (validarAsunto()){
				//alert("Despues de validar");

				// Inicio del poceso para poder comunicar el contenido del arreglo lstDestinatariosInternos en el proceso submit.
				var sListaDestinatarios = "";

				alert("El numero de registros en lstDestinatariosInternos es: " + lstDestinatariosInternos.length);

				if(lstDestinatariosInternos.length > 0){ 
					separador = "";
					for(i=0; i < lstDestinatariosInternos.length; i++){
						sListaDestinatarios = sListaDestinatarios + separador + lstDestinatariosInternos[i][0] + '|' + lstDestinatariosInternos[i][1] + '|' + lstDestinatariosInternos[i][2] + '|' + lstDestinatariosInternos[i][3] + '|' + lstDestinatariosInternos[i][4];
							separador = '*';
						}
				}
				document.all.txtControlDestinatarios.value = sListaDestinatarios;
				document.getElementById("btnGuardar").disabled=true;
				document.all.txtNombreUsrRemitente.value = document.all.txtUsuarioRemitente.options[document.all.txtUsuarioRemitente.selectedIndex].text;
				document.all.txtNombreAreaRemitente.value = document.all.txtAreaRemitente.options[document.all.txtAreaRemitente.selectedIndex].text;

				document.all.txtTipoOrigenDocto.value = document.all.txtOrigenRemitente.options[document.all.txtOrigenRemitente.selectedIndex].value;
				document.all.txtAccion = 'SOLOGUARDAR';
				document.all.formulario.submit();
			}
		});
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

		$( "#btnCancelar" ).click(function() {
			document.all.listaAsuntos.style.display='inline';
			document.all.capturaAsunto.style.display='none';
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


		$( "#btnVerDestinatarios" ).click(function() {
			document.all.txtAreasDestinatariasInternas.selectedIndex = 0;
			limpiaTabla(document.all.tblDestinatariosInternos);

		    //while (document.all.txtUsuariosDestinatariosInternos.options.length > 0) {                
		    //    document.all.txtUsuariosDestinatariosInternos.remove(0);
		    //}      
		    //copiarCombos(document.all.txtDestinatarios, document.all.txtUsuariosDestinatariosInternos);

			//alert("Antes del cargar la tabla");
			recuperarTablaDestinatariosFromArreglo('lstUsuariosByArea', "TODOS", document.all.tblDestinatariosInternos);
			//alert("Despues del cargar la tabla");

			$('#modalDestinatarios').removeClass("invisible");
			$('#modalDestinatarios').modal('toggle');
			$('#modalDestinatarios').modal('show');
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


		$( "#btnCancelarObjeto" ).click(function() {
			$('#modalObjetos').modal('hide');			
		});		

		$( "#btnCancelarCriterio" ).click(function() {
			$('#modalCriterios').modal('hide');			
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
				// Elementos de asuntos 
				if(sEditando=="D") document.all.txtDescripcion.value=document.all.txtTextoAmplio.value;
				if(sEditando=="C") document.all.txtComentario.value=document.all.txtTextoAmplio.value;
		});		


		$( "#btnGuardarObjeto" ).click(function() {
			guardarObjetosSeleccionados();
		});		

		$( "#btnGuardarUnidad" ).click(function() {
			moverUnidadesSeleccionadasAtexto(document.all.txtUnidades);
			$('#modalUnidades').modal('hide');			

		});		

		// Elementos de asuntos  
		$( "#txtDescripcionEdit" ).click(function(){
			document.all.txtTextoAmplio.value = document.all.txtDescripcion.value;
			$('#modalTextoLargo').modal('show');
			sEditando="D";
		});	

		$( "#txtComentarioEdit" ).click(function(){
			document.all.txtTextoAmplio.value = document.all.txtComentario.value;
			$('#modalTextoLargo').modal('show');
			sEditando="C";
		});	
		// *** Fin de Elementso de Asuntos

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
			    <div class="col-xs-12"> <div class="col-xs-2"><a href="/"><img src="img/logo-top.png"></a></div>
					<div class ="col-xs-2"> 
					    <ul class="nav navbar-nav ">
					    	<li><a href="#"><i class="fa fa-th-list"></i> <?php echo $_SESSION["sCuentaActual"] ?></a>
					    	</li>
					    </ul>
					</div>
					<div class="col-xs-3"><h2>ASUNTOS</h2></div> 
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
			<div class="row" id="listaAsuntos">

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
												<th style="text-align:center;">Asuntos</th><th style="text-align:center;">CANTIDAD</th>
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
							<h3><i class="fa fa-search"></i> Lista de Asuntos</h3> 
							  <!-- <div class="pull-left"><h3><i class="fa fa-pencil-square-o"> </i>  Lista de Asuntos</h3></div> -->
							<div class="clearfix"></div>
						</div>             
																<!-- Definición de sección para el manejo de Tabs  -->


						<ul class="nav nav-tabs">																		
							<li class="active"><a href="#tab-recepcion" data-toggle="tab" onclick="mostrarAsuntos('EN REGISTRO');"><i class="fa fa-bars"></i> Recepción/Registro</a></li>

							<li><a href="#tab-tramite" data-toggle="tab" onclick="mostrarAsuntos('TURNADO');"><i class="fa fa-bars"></i> En Trámite</a></li>
							<li><a href="#tab-terminados" data-toggle="tab"  onclick="mostrarAsuntos('TERMINADO');"><i class="fa fa-bars"></i> Terminados</a></li>
							<li><a href="#tab-cancelados" data-toggle="tab"  onclick="mostrarAsuntos('CANCELADO');"><i class="fa fa-bars"></i> Cancelados</a></li>
						</ul>								

						<div class="tab-content">

							<div class="tab-pane active" id="tab-recepcion" >
								<div class="widget-content">
									<div class="table-responsive" style="height: 400px; overflow: auto; overflow-x:hidden;">


										<table class="table table-striped table-bordered table-hover table-condensed">
											<thead>
												<tr><th>Folio Asunto</th><th>No.Documento</th><th>Tipo Documento</th><th>Descripción</th><th>Origen</th><th>Clasificación</th><th>Tipo Atención</th><th>Prioridad</th><th>Situación</th><th>Fecha Documento</th><th>Fecha Compromiso</th><th>Estatus</th></tr>
											</thead>
											<tbody>																			
												<?php foreach($datos as $key => $valor): ?>
													<tr onclick=<?php echo "javascript:recuperaAsunto('" . $valor['asunto'] . "');"; ?> style="width: 100%; font-size: xx-small">										  										  
														<td width="08%"><?php echo $valor['folio']; ?></td>
														<td width="08%"><?php echo $valor['numero']; ?></td>
														<td width="08%"><?php echo $valor['tipoDocumento']; ?></td>
														<td width="15%"><?php echo $valor['descripcion']; ?></td>
														<td width="08%"><?php echo $valor['tipoOrigenDocto']; ?></td>
														<td width="15%"><?php echo $valor['clasificacion']; ?></td>
														<td width="08%"><?php echo $valor['tipoAtencion']; ?></td>
														<td width="08%"><?php echo $valor['prioridad']; ?></td>
														<td width="08%"><?php echo $valor['situacion']; ?></td>
														<td width="08%"><?php echo $valor['fechaDocto']; ?></td>
														<td width="08%"><?php echo $valor['fechaCompro']; ?></td>
														<td><a href=''><?php echo obtenerGifEstatus($valor['estatus']); ?></a></td>
													</tr>
												<?php endforeach; ?>	
											</tbody>
										</table>
									</div>
								</div>
							</div>

							<div class="tab-pane" id="tab-tramite" >
								<div class="table-responsive" style="height: 400px; overflow: auto; overflow-x:hidden;">
									<table class="table table-striped table-bordered table-hover table-condensed" >
										<thead>
											<tr><th>Folio Asunto</th><th>No.Documento</th><th>Tipo Documento</th><th>Descripción</th><th>Origen</th><th>Clasificación</th><th>Tipo Atención</th><th>Prioridad</th><th>Avance</th><th>Fecha Documento</th><th>Fecha Compromiso</th><th>Estatus</th></tr>
										</thead>
										<tbody id="tblAsuntosTurnados" >
										</tbody>
									</table>
								</div>
							</div>

						</div>
						
						<div class="widget-foot">
							<div class="pull-left">
								<button onclick="agregarAsunto();" type="button" class="btn btn-primary  btn-xs" id="btnAgregarAsunto" ><i class="fa fa-search"></i> Agregar Asunto...</button>
								<button type="button" class="btn btn-default  btn-xs" name="btnENVIADA" id="btnENVIADA" style="display:none;"><i class="fa fa-external-link"></i>Enviar Unidad Técnica </button>
								<button type="button" class="btn btn-default  btn-xs" name= id="btnVALIDACION" id="btnVALIDACION" style="display:none;"><i class="fa fa-external-link"></i>Valida/Autoriza </button>
								<button type="button" class="btn btn-default  btn-xs" name="btnINTEGRACION" id="btnINTEGRACION" style="display:none;"><i class="fa fa-external-link"></i>Integrar PGA</button>
							</div>
							<div class="clearfix"></div>
						</div>
						
					</div>
				</div>
			</div>
		  
			<!-- <div class="row" id="capturaAsunto" style="display:none; padding:0px; margin:0px;"> -->
			<div class="row" id="capturaAsunto" style="display:none; padding:0px; margin: 0% 0% 0% -50% !important;">  
				<form id="formulario" METHOD='POST' action='/guardar/asunto' role="form">
					<input type='HIDDEN' name='txtOperacion' value=''>
					<input type='HIDDEN' name='txtCuenta' value=''>						
					<input type='HIDDEN' name='txtPrograma' value=''>
					<input type='HIDDEN' name='txtControlDestinatarios' value=''>				
					<input type='HIDDEN' name='txtNombreAreaRemitente' value=''>
					<input type='HIDDEN' name='txtNombreUsrRemitente' value=''>
					<input type='HIDDEN' name='txtTipoOrigenDocto' value=''>
					<input type='HIDDEN' name='txtAccion' value=''>
					<input type='HIDDEN' name='txtArchivoOriginal' value=''>
					<input type='HIDDEN' name='txtArchivoFinal' value=''>
					<input type='HIDDEN' name='txtControlArchivosAnexos' value=''>
					<input type='HIDDEN' name='txtSituacion' value=''>


	
					<!-- <div class="col-md-11" style="margin: 0px 0px 0px 15% !important; width: 71% !important; height: 84% !important;">	-->	
					<div class="col-md-11" style='width: 85%'>				
						<div class="widget">
							<!-- Widget head -->
							<div class="widget-head">
								<div class="pull-left"><h3><i class="fa fa-pencil-square-o"> </i> Captura de Asunto</h3></div>

									<div class="widget-icons pull-right">
										<button type="button" id="btnCargarArchivo" class="btn btn-primary  btn-xs" ><i class="fa fa-paperclip"></i> Archivos Anexos...</button>
										<button type="button"  id="btnTurnarAsunto" class="btn btn-primary  btn-xs" onclick="GuardarAsunto('SOLOTURNAR');"><i class="fa fa-floppy-o"></i> Turnar</button>
										<button type="button"  id="btnGuardarAsunto" class="btn btn-primary  btn-xs"  onclick="GuardarAsunto('GUARDARTURNAR');"><i class="fa fa-floppy-o"></i> Guardar y Turnar</button>
										<button type="button"  id="btnGuardar" class="btn btn-primary  btn-xs" onclick="GuardarAsunto('SOLOGUARDAR');"><i class="fa fa-floppy-o"></i> Guardar</button>
										<button type="button" id="btnCancelar" class="btn btn-default  btn-xs" onclick="CancelarGuardarAsunto();"><i class="fa fa-undo"></i> Regresar</button> 
							  		</div>  
								<div class="clearfix"></div>
							</div>    

							<!-- Widget content -->
							<!--<div class="widget-content" style="width: 120%;">-->
							<div class="widget-content">
								<br>
								<!--
								<div class="form-group">									
									<label class="col-xs-1 control-label" style="width: 10% !important;" >Folio Asunto</label>
									<div class="col-xs-1"><input name="txtFolio" class="form-control" type="text" readonly=""></div>			

									<label class="col-xs-1" style="width: 10% !important;">Tipo Documento</label>
									<div class="col-xs-2">
										<select name="txtTipoDocto" class="form-control">
											<option value="">Seleccione...</option>
										</select>
									</div>

									<label class="col-xs-1 control-label" style="width: 10% !important;" >No.Documento</label>
									<div class="col-xs-2"><input name="txtNumeroDocto" class="form-control" type="text"></div>	
								</div>								
								<br>
								-->
								<div class="form-group">									
									<!-- <label class="col-xs-1 control-label" style="width: 9% !important;">Folio Asunto</label> -->
									<label class="col-xs-1 control-label" style="width: 11% !important;" >Folio Asunto</label>
									<div class="col-xs-1"><input name="txtFolio" class="form-control" type="text" readonly=""></div>			

									<label class="col-xs-2 control-label">Tipo Documento</label>
									<div class="col-xs-2">
										<select name="txtTipoDocto" class="form-control">
											<option value="">Seleccione...</option>
										</select>
									</div>

									<label class="col-xs-3 control-label">No. Documento</label>
									<div class="col-xs-2"><input name="txtNumeroDocto" class="form-control" type="text"></div>	
								</div>								
								<br>
								<!--
								<div class="form-group">									
									<label class="col-xs-1 control-label" style="width: 10% !important;">Fecha Documento</label>
									<div class="col-xs-1">
										<input type="text" id="datepicker" class="form-control" name="txtFechaDocto" placeholder="Fecha Documento"/>
									</div>
									<label class="col-xs-1" style="width: 10% !important;">Clasificación Indices</label>
									<div class="col-xs-3">
										<select name="txtClasIndices" class="form-control" onChange="javascript: recuperaIndices(this.value);" >
											<option value="">Seleccione...</option>
										</select>
									</div>
									<label class="col-xs-1 control-label" style="width: 10% !important;">Prioridad</label>
									<div class="col-xs-1">
										<select name="txtPrioridad" class="form-control">
											<option value="">Seleccione...</option>
											<option value="BAJA" selected="">BAJA</option>
											<option value="MEDIA">MEDIA</option>
											<option value="ALTA">ALTA</option>
										</select>
									</div>								
								</div>
								<br>	
								<div class="clearfix"></div>
								-->
								<div class="form-group">									
									<label class="col-xs-1 control-label" style="width: 11% !important;">Fecha Documento</label>
									<div class="col-xs-1">
										<input type="text" id="datepicker" class="form-control" name="txtFechaDocto" placeholder="Fecha Documento"/>
									</div>
									<label class="col-xs-2" >Clasificación Indices</label>
									<div class="col-xs-5">
										<select name="txtClasIndices" class="form-control" onChange="javascript: recuperaIndices(this.value);" >
											<option value="">Seleccione...</option>
										</select>
									</div>
									<label class="col-xs-1 control-label" >Prioridad</label>
									<div class="col-xs-1">
										<select name="txtPrioridad" class="form-control">
											<option value="">Seleccione...</option>
											<option value="BAJA" selected="">BAJA</option>
											<option value="MEDIA">MEDIA</option>
											<option value="ALTA">ALTA</option>
										</select>
									</div>								
								</div>
								<br>	
								<!--
								<div class="clearfix"></div>
								<div class="form-group">									
									<label class="col-xs-1 control-label" style="width: 10% !important;">Fecha Recepción</label>
									<div class="col-xs-1">
										<input type="text" id="datepicker2" class="form-control" name="txtFechaRecepcion" placeholder="Fecha Recepción"/>
									</div>
									<label class="col-xs-1" style="width: 10% !important;">Indices</label>
									<div class="col-xs-3">
										<select name="txtIndice" class="form-control" id="txtIndice" >
											<option value="">Seleccione...</option>
										</select>
									</div>
									<label class="col-xs-1 control-label" style="width: 10% !important;">Impacto</label>
									<div class="col-xs-1">
										<select name="txtImpacto" class="form-control">
											<option value="">Seleccione...</option>
											<option value="BAJO" selected="">BAJO</option>
											<option value="MEDIO">MEDIO</option>
											<option value="ALTO">ALTO</option>
										</select>
									</div>								
								</div>
								<br>	
								<div class="clearfix"></div>
								-->

								<div class="clearfix"></div>
								<div class="form-group">									
									<label class="col-xs-1 control-label" style="width: 11% !important;">Fecha Recepción</label>
									<div class="col-xs-1">
										<input type="text" id="datepicker2" class="form-control" name="txtFechaRecepcion" placeholder="Fecha Recepción"/>
									</div>
									<label class="col-xs-2">Indices</label>
									<div class="col-xs-5">
										<select name="txtIndice" class="form-control" id="txtIndice" >
											<option value="">Seleccione...</option>
										</select>
									</div>
									<label class="col-xs-1 control-label">Impacto</label>
									<div class="col-xs-1">
										<select name="txtImpacto" class="form-control">
											<option value="">Seleccione...</option>
											<option value="BAJO" selected="">BAJO</option>
											<option value="MEDIO">MEDIO</option>
											<option value="ALTO">ALTO</option>
										</select>
									</div>								
								</div>
								<br>	
								<div class="clearfix"></div>
								<!--
								<div class="form-group">									
									<label class="col-xs-2 control-label" style="width: 10% !important;" >Fecha Compromiso</label>
									<div class="col-xs-1">
										<input type="text" id="datepicker3" class="form-control" name="txtFechaCompromiso" placeholder="Fecha Compromiso"/>
									</div>
									<label class="col-xs-1 control-label" style="width: 10% !important;">Clasificación</label>
									<div class="col-xs-3">
										<select name="txtClasificacionDocto" class="form-control">
											<option value="">Seleccione...</option>
										</select>
									</div>
									<label class="col-xs-1 control-label" style="width: 10% !important;" >Confidencial</label>
									<div class="col-xs-1">
										<select name="txtConfidencial" class="form-control">
											<option value="" selected="">Seleccione...</option>
											<option value="NO" selected="">NO</option>
											<option value="SI">SI</option>
										</select>
									</div>								
								</div>
								<br>	
								<div class="clearfix"></div>
								-->

								<div class="form-group">									
									<label class="col-xs-2 control-label" style="width: 11% !important;" >Fecha Compromiso</label>
									<div class="col-xs-1">
										<input type="text" id="datepicker3" class="form-control" name="txtFechaCompromiso" placeholder="Fecha Compromiso"/>
									</div>
									<label class="col-xs-2 control-label">Clasificación</label>
									<div class="col-xs-5">
										<select name="txtClasificacionDocto" class="form-control">
											<option value="">Seleccione...</option>
										</select>
									</div>
									<!-- <label class="col-xs-1 control-label" style="margin: 0px 0px 0px 1% !important;">Confidencial</label> -->
									<label class="col-xs-1 control-label">Confidencial</label>
									<div class="col-xs-1">
										<select name="txtConfidencial" class="form-control">
											<option value="" selected="">Seleccione...</option>
											<option value="NO" selected="">NO</option>
											<option value="SI">SI</option>
										</select>
									</div>								
								</div>
								<br>	
								<div class="clearfix"></div>

								<div class="form-group">									
									<label class="col-xs-1" style="width: 11% !important;">Auditoria</label>
									<div class="col-xs-10">
										<select name="txtAuditoria" class="form-control">
											<option value="">Seleccione...</option>
										</select>
									</div>

								</div>								
								<br>
								<div class="clearfix"></div>
								<!--
								<div class="form-group">									
									<label class="col-xs-2 control-label" style="margin: 1% 08px 1% 0% !important; width: 10% !important;">Descripción <i class="fa fa-pencil" id="txtDescripcionEdit"></i></label>
									<div class="col-xs-3"><textarea name="txtDescripcion" class="form-control" id="txtDescripcion" style="margin: 3% 0px 4% -2% !important;" rows="2"></textarea></div>

									<label class="col-xs-2 control-label" style="margin: 1% 0px 1% 0% !important; width: 10% !important;">Comentario <i class="fa fa-pencil" id="txtComentarioEdit"></i></label>
									<div class="col-xs-3"><textarea name="txtComentario" class="form-control" id="txtComentario" style="margin: 3% 0px 1% 3% !important;" rows="2"></textarea></div>
								</div>								
								<br>
								<div class="clearfix"></div>
								-->
								<div class="form-group">									
									<label class="col-xs-1 control-label" style="margin: 1% 08px 1% 0% !important; width: 11% !important;">Descripción <i class="fa fa-pencil" id="txtDescripcionEdit"></i></label>
									<div class="col-xs-5"><textarea name="txtDescripcion" class="form-control" id="txtDescripcion" style="width: 100% !important; margin: 3% 0px 4% -2% !important;" rows="1"></textarea></div>

									<label class="col-xs-1 control-label" style="margin: 1% 08px 1% 0% !important;">Comentario <i class="fa fa-pencil" id="txtComentarioEdit"></i></label>
									<div class="col-xs-4"><textarea name="txtComentario" class="form-control" id="txtComentario" style="width: 93% !important; margin: 3% 0px 1% 3% !important;" rows="1"></textarea></div>
								</div>								
								<br>
								<div class="clearfix"></div>
								<!--
								<div class="form-group">
									<label class="col-xs-1" style="width: 10% !important;">Origen Remitente</label>
									<div class="col-xs-1">
										<select name="txtOrigenRemitente" class="form-control" onChange="javascript: recuperaAreasRemitente(this.value);">
											<option value="">Seleccione...</option>
											<option value="INTERNO">INTERNO</option>
											<option value="EXTERNO">EXTERNO</option>
										</select>
									</div>
									<label class="col-xs-1" style="width: 10% !important;">Area Remitente</label>
									<div class="col-xs-5">
										<select name="txtAreaRemitente" class="form-control" style="width: 103% !important;" onChange="javascript: recuperaUsuariosRemitente(this.value);">
											<option value="">Seleccione...</option>
										</select>
									</div>
								</div>								
								<br>	
								<div class="clearfix"></div>
								-->
								<div class="form-group">
									<label class="col-xs-1" style="width: 11% !important;">Origen Remitente</label>
									<div class="col-xs-1">
										<select name="txtOrigenRemitente" class="form-control" style="width: 110% !important;" onChange="javascript: recuperaAreasRemitente(this.value);">
											<option value="">Seleccione...</option>
											<option value="INTERNO">INTERNO</option>
											<option value="EXTERNO">EXTERNO</option>
										</select>
									</div>
									<label class="col-xs-2">Area Remitente</label>
									<div class="col-xs-7">
										<select name="txtAreaRemitente" class="form-control" onChange="javascript: recuperaUsuariosRemitente(this.value);">
											<option value="">Seleccione...</option>
										</select>
									</div>
								</div>								
								<br>	
								<div class="clearfix"></div>
								<!--
								<div class="form-group">
									<label class="col-xs-1" style="width: 10% !important;">Remitente</label>
									<div class="col-xs-7">
										<select name="txtUsuarioRemitente" class="form-control" style="width: 105% !important; font-size: small">
											<option value="">Seleccione...</option>
										</select>
									</div>
								</div>								
								<br>	
								<div class="clearfix"></div>
								-->
								<div class="form-group">
									<label class="col-xs-1" style="width: 11% !important;">Remitente</label>
									<div class="col-xs-10">
										<select name="txtUsuarioRemitente" class="form-control" font-size: small">
											<option value="">Seleccione...</option>
										</select>
									</div>
									<!--
									<div class="col-xs-1">
										<button class="btn btn-default btn-xs" id="btnSelRemitente" type="button" style="margin: 5px 0px 0% 0% !important; width: 80%;"><i class="fa fa-check-circle"></i> Seleccionar</button>
									</div>
									-->
								</div>								
								<br>	
								<div class="clearfix"></div>

								<div class="form-group">
									<label class="col-xs-2 control-label" style="width: 11% !important;">Destinatario(s)</label>
									<div class="col-xs-9">
										<select name="txtDestinatarios" class="form-control" id="txtDestinatarios"  style="font-size: x-small" size="7">
											<!-- <option value="">Seleccione...</option> -->
										</select>										
									</div>	
									<div class="col-xs-1">
										<button class="btn btn-warning btn-xs" id="btnVerDestinatarios"  type="button" style="margin: 0px 0px -20% 0% !important; width: 100%;"><i class="fa fa-plus-circle"></i> Agregar</button>

										<button class="btn btn-warning btn-xs" id="btnQuitarDestinatarios" type="button" style="margin: 0px 0px -40% 0% !important; width: 100%;"><i class="fa fa-minus-circle"></i> Quitar</button>

										<button class="btn btn-warning btn-xs" id="btnLimpiarDestinatarios" type="button" style="margin: 0px 0px -60% 0% !important; width: 100%;"><i class="fa fa-eraser"></i> Limpiar</button>

									</div>
								</div>								
								<div class="clearfix"></div> 
								<br>

								<div class="form-group">									
									<label class="col-xs-1 control-label" style="width: 11% !important;" >No. Expediente</label>
									<div class="col-xs-2"><input name="txtExpediente" class="form-control" type="text" ></div>			
									<label class="col-xs-2 control-label">Serie Documental</label>
									<div class="col-xs-2"><input name="txtSerieDocumental" class="form-control" type="text" ></div>
									<label class="col-xs-2 control-label">Clave Busqueda</label>
									<div class="col-xs-2"><input name="txtClaveBusqueda" class="form-control" type="text" ></div>
								</div>								
								<div class="clearfix"></div>
								<br>
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
	
	<!--  INICIO SECCIÓN PARA DESTINATARIOS -->

	<div id="modalDestinatarios" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog">							
				<!-- Modal content-->
			<div class="modal-content">									
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h3 class="modal-title"><i class="fa fa-pencil-square-o"></i>Selección Destinatarios...</h3>
				</div>									
				<div class="modal-body">
					<form id="formularioDestinatarios" METHOD='POST' ACTION='/guardar/destinatariosInternos' role="form">
						<input type='HIDDEN' name='txtOperacionDestinatarioInterno' value=''>

						<div class="form-group">						
							<label class="col-xs-2 control-label">Filtro por Area</label>
							<div class="col-xs-9">
								<!--<select class="form-control" name="txtAreasDestinatariasInternas" onChange="javascript: recuperaDestinatariosInternos(this.value);">  
								-->
								<select class="form-control" name="txtAreasDestinatariasInternas" onChange="javascript: FiltraDestinatariosInternos();">
									<option value="">Todas las Áreas...</option>
								</select>
							</div>
						</div>
						<br>
						<div class="clearfix"></div>								

						<!-- ************************ ÁREA DE GRID DE USUARIOS (EMPLEADOS POR UNIDAD) ********************* -->

						<div class="table-responsive col-xs-12" style="height: 500px; overflow: auto; overflow-x:hidden;">	
							<br>
							<table class="table table-striped table-bordered table-hover table-condensed table-responsive">
								<!-- <caption>Destinatarios</caption> -->
								<thead>
									<tr><th width="5%">Atenc.</th><th width="5%">Conocim.</th><th width="25%">Destinatario</th><th width="30%">Plaza</th><th width="35%">Area</th></tr>
								</thead>
								<tbody id="tblDestinatariosInternos" style="width: 100%; font-size: xx-small">
								</tbody>
							</table>
						</div>
						<div class="clearfix"></div>
						<br>
						<!--
						<div class="form-group">
							<label class="col-xs-3 control-label">Destinatarios Seleccionados</label>
							<div class="pull-right">
							
								<button style="display: inline; margin: 0px 23px 0px 0px ! important;" type="button" class="btn btn-warning btn-xs" id="btnSelDestinatarios" onclick="mueveDestinatariosInternos(document.all.txtUsuariosDestinatariosInternos);"><i class="fa fa-plus-circle"></i> Seleccionar Destinatarios</button>
							
								<button style="display: inline; margin: 0px 23px 0px 0px ! important;" type="button" class="btn btn-warning btn-xs" id="btnSelDestinatarios" onclick="mueveDestinatariosSeleccionados(document.all.txtUsuariosDestinatariosInternos);"><i class="fa fa-plus-circle"></i> Seleccionar Destinatarios</button>
							</div>
							<div class="col-xs-12">
								<select class="form-control" name="txtUsuariosDestinatariosInternos" size=7 style="width: 100%; font-size: xx-small" >
								</select>
							</div>						
						</div>						
						-->
					</form>
					<div class="clearfix"></div>
				</div>
				
				<div class="modal-footer">
					<div class="pull-right">
					<!--
						<button type="button" class="btn btn-primary active" id="btnGuardarDestinatarios" type="submit"><i class="fa fa-floppy-o"></i> Asignar Destinatarios</button>
						-->
						<button type="button" class="btn btn-primary active" id="btnGuardarDestinatarios" type="submit"><i class="fa fa-floppy-o"></i> Asignar Destinatarios</button>
						<button type="button" class="btn btn-default active" id="btnCancelarDestinatarios" data-dismiss="modal"><i class="fa fa-back"></i> Cancelar</button>
					</div>
					<div class="clearfix"></div>
				</div>
			</div>		
		</div>
	</div>

	<!--  FIN DE SECCIÓN PARA DESTINATARIOS -->

	<!--  INICIO SECCIÓN PARA ARCHIVOS ANEXOS -->

	<div id="modalCargarArchivos" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog" >							
				<!-- Modal content-->
			<div class="modal-content">									
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" id="btnAgragarArchivosX">&times;</button>
					<h3 class="modal-title"><i class="fa fa-pencil-square-o"></i>Selección de Archivos Anexos...</h3>
				</div>									
				<div class="modal-body">
					<form id="formularioArchivosAnexos" METHOD='POST' ACTION='/guardar/archivosAnexos' role="form">
						<input type='HIDDEN' name='txtOperacionArchivoAnexo' value=''>

						<!--
						<div class="form-group">
							<div class="col-xs-10">
								<label class="col-xs-3 control-label">Archivos Anexo(s)</label>
							</div>						
						</div>						
						<br>
						-->
						<div class="form-group">
							<div class="col-xs-8">
								<select name="txtArchivosAnexos" class="form-control" id="txtArchivosAnexos"  style="" size="11">
								</select>										
							</div>	
							<div class="col-xs-3">
								<button class="btn btn-warning btn-xs" id="btnAgregarArchivosAnexos"  type="button" style="width: 100%;"><i class="fa fa-plus-circle"></i> Agregar</button>

								<input type="file" name="btnUpload" accept="application/pdf,application/vnd.ms-excel,,application/vnd.ms-word, " style="display:none;" id="btnUpload">
								<progress id="progressBar" value="0" max="100" style="width:'100%'; display:none;"></progress>
								<!--
								<h4 id="status"></h4>
								<p id="lblAvances"></p>
								-->
								<input type="file" name="btnDelete" style="display:none;" id="btnDelete">

								<button class="btn btn-warning btn-xs" id="btnQuitarArchivosAnexos" type="button" style="margin: 7px 0px 0% 0% !important; width: 100%;"><i class="fa fa-minus-circle"></i> Quitar</button>
								<!--
								<button class="btn btn-warning btn-xs" id="btnLimpiarArchivosAnexos" type="button" style="margin: 7px 0px 0% 0% !important; width: 100%;"><i class="fa fa-eraser"></i> Limpiar</button>
								-->

							</div>
						</div>								
						<br>

					</form>
					<div class="clearfix"></div>
				</div>
				
				<div class="modal-footer">
					<div class="pull-right">
						<!--
						<button type="button" class="btn btn-primary active" id="btnGuardarArchivosAnexos" type="submit"><i class="fa fa-floppy-o">	</i> Guardar Archivos Anexos</button>
						-->
						<button type="button" class="btn btn-default active" id="btnContinuarArchivosAnexos" data-dismiss="modal"><i class="fa fa-chevron-right"></i> Continuar</button>
					</div>
					<div class="clearfix"></div>
				</div>
			</div>		
		</div>
	</div>

	<!--  FIN DE SECCIÓN PARA ARCHIVOS ANEXOS -->


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