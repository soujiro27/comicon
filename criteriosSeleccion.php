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
			#mapa_content {width:100%; height:150px;}
			#modalFlotante .modal-dialog  {width:60%;}
			#modalUnidades .modal-dialog  {width:60%;}
			#modalArchivos .modal-dialog  {width:40%;}
			#tblListaEntidades td { font-size: xx-small;}

			.delimitado {  
				height: 350px !important;
				overflow: scroll;
			}​			
			.auditor{background:#f4f4f4; font-size:7pt; padding:2px; display:inline; margin:2px; border:1px black solid;}
			label {text-align:right;}	
			caption {padding: .2em .8em;border-bottom: 1px solid #fFF;background:#f4f4f4; font-weight: bold;}					
		}
	</style>
	

	<!-- **************************************  INICIA ÁREA DE JAVA SCRIPT ******************************* -->

	<script type="text/javascript"> 
	var mapa;
	var nZoom=10;
	var lstArchivosAnexos = new Array();
	var lstUnidadesSeleccionadas = new Array()

	// *************************** FUNCIÓN PARA EL CONTROL DE CAMPOS DE FECHA *****************************
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
	        isRTL: false,
	        showMonthAfterYear: false,
	        yearSuffix: ''
	    };

		$.datepicker.setDefaults($.datepicker.regional["es"]);
		
		$("#datepicker").datepicker({
		dateFormat:'yy-mm-dd',
		firstDay: 1,
		numberOfMonths: 2
		});
	});

	// ************************ FUNCIONES PARA LA CARGA Y BORRADO DE ARCHIVOS *****************************

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
				if ( sArchivoCarga == lstArchivosAnexos[i][1]){
					lExiste = true;
					//alert("El archivo que desea anexar ya existe.");
					break;
				}
			}
			if (lExiste == false){  
				// Ahora se movera el archivo de su localidad original a la carpeta asignada (/criteriosDoctos) en el servidor
				formdata.append("btnUpload", file);
				var ajax = new XMLHttpRequest();
				ajax.upload.addEventListener("progress", progressHandler, false);
				ajax.addEventListener("load", completeHandler, false);
				ajax.addEventListener("error", errorHandler, false);
				ajax.addEventListener("abort", abortHandler, false);				
				ajax.open("POST", "uploadCriteriosDoctos.php");
				ajax.send(formdata);
			}
		}
	}		

	function completeHandler(event){
		document.all.progressBar.style="display:none";
		document.all.progressBar.value= 0;		
		if(event.target.responseText!="ERROR"){				

			vArchivoOriginal=sArchivoCarga;
			vArchivoFinal=event.target.responseText;

			vUrl = '/guardar/criteriosDocumentos/INS|0|' + document.all.txtIdCriterio.value + "|" + vArchivoOriginal + "|" + vArchivoFinal + "|ACTIVO" ;

			$.ajax({ type: 'GET', url: vUrl ,
				success: function(response) {
					sRespuesta = response.replace(/^\s+/g,'').replace(/\s+$/g,'');
					if(sRespuesta == "OK"){
		  				recuperarTablaDocumentos('obtenerDocumentosByCriterioSeleccion', document.all.txtIdCriterio.value, tblListaArchivos);
						document.all.divListaArchivos.style.display='inline';
					}else{
						alert("No fue posible guardar el documento."+ response);
					}
				},
				error: function(xhr, textStatus, error){
					alert(' Error en function para asignar el nuevo documento al critero,  TextStatus: ' + textStatus + ' Error: ' + error );
				}			
			});

		}else{

			vArchivoOriginal="";
			vArchivoFinal="";
			alert("El archivo no subió correctamente.");
		}			
	}

	function errorHandler(event){document.all.status.innerHTML = "Falló la carga";}			
	function abortHandler(event){document.all.status.innerHTML = "Se abrotó la carga.";}					
	
	//// PROCESOS PARA BORRADO DE ARCHIVO
	
	function borrarArchivoAnexo(file, confirmarBorrado){
		var formdata = new FormData();
		file = "criteriosDoctos/" + file;
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

	// ************************ FUNCIONES PARA LA CARGA Y BORRADO DE ARCHIVOS *****************************


	// ********************************************* ZONA DE FUNCIONES ***************************************
	function inicializar() {
		var opciones = {zoom: nZoom,draggable: false,scrollwheel: true,	mapTypeId: google.maps.MapTypeId.ROADMAP};
		mapa = new google.maps.Map(document.getElementById('mapa_content'), { center: {lat: 19.4339249, lng: -99.1428964},zoom: nZoom});			
	}	
	
		window.onload = function () {
		var chart1; 

		//setGrafica(chart1, "dpsAuditoriasByArea", "pie", "Auditorias", "canvasJG" );
		};

	// ******************* FUNCIONES DE DESPLIEGE DE FUNCIONES DE TABLAS *****************************

    function recuperarTablaUnidades(lista, valor, tbl){
    	var sTatus;
		//alert('/'+ lista + '/' + valor);
        $.ajax({ async: false, 
           type: 'GET', url: '/'+ lista + '/' + valor ,
           success: function(response) {
               var jsonData = JSON.parse(response);                                 
                              
               //Vacia la lista
               tbl.innerHTML="";

               //Agregar renglones
               var renglon, columna;
			   lstUnidadesSeleccionadas = [];
               //alert("El número de registros es de: " + jsonData.datos.length);

               for (var i = 0; i < jsonData.datos.length; i++) {
					var dato = jsonData.datos[i];                                        
					
					var relacionado = dato.relacionado;
					var criUnidad   = dato.idCriterioUnidad;
					var criAuxiliar = dato.idCriterioAuxiliar;
					var cuenta      = dato.idCuenta;
					var cveUnidad   = dato.cveUnidad;
					var nomUnidad   = dato.nombreUnidad;

					// Se eliminan los elementos que pueda contener el arreglo de control sobre las unidades seleccionadas.
					
					sTatus = "";
 					if(relacionado == "SI"){
               			sTatus= "checked=true";
	                   	seleccionarUnidad(cuenta, cveUnidad, nomUnidad, criUnidad, criAuxiliar, true);
	                }

	                renglon       = document.createElement("TR");
					renglon.style = "font-size: xx-small";
					//renglon.setAttribute('onclick', "seleccionarUnidad(\""+cuenta+"\",\""+cveUnidad+"\",\""+nomUnidad+"\", "+criUnidad+","+criAuxiliar+", this.checked);" );

	                //renglon.innerHTML = "" +
	                //"<td style='width:5% !important;'> <input type='checkbox' name='' " + sTatus      + "</td>" +
					//"<td style='width:10% !important;' >" + cveUnidad   + "</td>" +
					//"<td style='width:75% !important;' >" + nomUnidad   + "</td>" ;

	                renglon.innerHTML = "" +
					"<td style='width:5% !important;'> <input type='checkbox' name='' "+sTatus + " onclick='seleccionarUnidad(" + '"' + cuenta + '","' + cveUnidad + '",'  + '"' + nomUnidad + '","' + criUnidad + '",'  + '"' + criAuxiliar + '",' + "this.checked);'/></td>" +
					"<td style='width:10% !important;' >" + cveUnidad   + "</td>" +
					"<td style='width:75% !important;' >" + nomUnidad   + "</td>" ;


					//renglon.innerHTML="<td onclick='recuperaCriterioUnidad("+criUnidad+","+criAuxiliar+",\""+cuenta+"\",\""+cveUnidad+"\",\""+nomUnidad+"\");' style='width:10% !important;'>" + dato.cveUnidad + "</td><td onclick='recuperaCriterioUnidad("+criUnidad+","+criAuxiliar+",\""+cuenta+"\",\""+cveUnidad+"\",\""+nomUnidad+"\");' style='width:60% !important;'>" + dato.nombre + "</td>";

                 	 tbl.appendChild(renglon);                                                                       
               }                                                             
           },
           error: function(xhr, textStatus, error){
                alert('function recuperarTablaUnidades ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
           }                                             
		});           
    }              

   	function seleccionarUnidad(cuenta, claveUnidad, nombreUnidad, criUnidad, criAuxiliar, activo)
	{
		if (activo==true){

			lstUnidadesSeleccionadas.push(new Array(cuenta, claveUnidad.substr(0,2), claveUnidad.substr(2,2), claveUnidad.substr(4,2), nombreUnidad, criUnidad, criAuxiliar));
		}else{
			for( i = 0; i < lstUnidadesSeleccionadas.length; i++)
			{
				if(claveUnidad==lstUnidadesSeleccionadas[i][1]+lstUnidadesSeleccionadas[i][2]+lstUnidadesSeleccionadas[i][3]){
					lstUnidadesSeleccionadas.splice(i, 1);
				}
			}
		}
	}


    function recuperarTablaDocumentos(lista, valores, tbl){
			//	alert('/'+ lista + '/' + valor);
        $.ajax({
           type: 'GET', url: '/'+ lista + '/' + valores,
           success: function(response) {
               var jsonData = JSON.parse(response);                                 
               
               //Vacia la lista
               tbl.innerHTML="";

               //Agregar renglones
               var renglon, columna;
               
               // Limpia el Arreglo que tendra los datos de los archivos anexos del criterio en porceso, lo lleneara en el siguiente FOR
               lstArchivosAnexos = [];

                for (var i = 0; i < jsonData.datos.length; i++) {

                    var dato = jsonData.datos[i];
                    var myIdCriterioDocumento = dato.idCriterioDocumento;
                    var myIdCriterioAuxiliar = dato.idCriterioAuxiliar;
                    var myArchivoOriginal = dato.archivoOriginal;
                    var myArchivoFinal = dato.archivoFinal;
                    var myFalta = dato.falta;
                    var myEstatus = dato.estatus;

	                renglon=document.createElement("TR");     
					renglon.style = "width: 100%; font-size: xx-small";
	                renglon.innerHTML= "" +
					"<td width=\"20%\"onclick='recuperaCriterioArchivo(" + myIdCriterioDocumento + "," + myIdCriterioAuxiliar + ",\"" +myArchivoOriginal + "\",\"" + myArchivoFinal + "\",\"" + myFalta + "\",\"" + myEstatus + "\");'>" + myArchivoOriginal + "</td>" + 
					'<td width="20%">' + myFalta + "</td>" +
					'<td width="20%">' + myEstatus + "</td>" +
					"<td width=\"20%\">" + "<a href='/criteriosDoctos/" + myArchivoFinal + "'>"  + recuperarGif(myArchivoOriginal); + "</a></td>";

                    tbl.appendChild(renglon);
	   				lstArchivosAnexos.push(new Array(dato.idCriterioDocumento, dato.archivoOriginal, dato.archivoFinal, dato.falta, dato.estatus));
                }                                                             
            },
            error: function(xhr, textStatus, error){
                 alert('function recuperarTablaDocumentos ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
            }                                             
		});           
     }


	// ******************* FIN DE FUNCIONES DE DESPLIEGE DE FUNCIONES DE TABLAS *****************************

	/*
	function recuperaCriterioUnidad(sIdCriterioUnidad, sIdCriterioAuxiliar, sIdCuenta, sCveUnidad, sNombreUnidad){

		//alert("El valor de sIdCuenta=>" + sIdCuenta + "    sIdCriterioAuxiliar=>" + sIdCriterioAuxiliar + "  sIdCriterioUnidad=>" + sIdCriterioUnidad + "  sCveUnidad=>" + sCveUnidad + "   sNombreUnidad=>" + sNombreUnidad) ;

		document.all.txtOperacionUnidad.value = "UPD";

		//document.all.txtIdCriterio.value = sIdCriterioAuxiliar;
		//document.all.txtIdCriterioUnidad.value = sIdCriterioUnidad;

		////recuperarLista('listaAllUnidades', document.all.txtUnidad);

		document.all.btnAsignarUnidad.style.display='none';
		document.all.btnDesasignarUnidad.style.display='inline';

		seleccionarElemento(document.all.txtUnidad, sCveUnidad);

		$('#modalUnidades').removeClass("invisible");
		$('#modalUnidades').modal('toggle');
		$('#modalUnidades').modal('show');
	}
	*/


	function recuperaCriterioArchivo(sIdCriterioDocumento, sIdCriterioAuxiliar, sArchivoOriginal, sArchivoFinal, sFalta, sEstatus){

		document.all.txtOperacionArchivo.value = "UPD";

		document.all.txtIdCriterioDocto.value    = sIdCriterioDocumento;
		document.all.txtIdCriterioAuxDocto.value = sIdCriterioAuxiliar
		document.all.txtArchivoOriginal.value    = sArchivoOriginal;
		document.all.txtArchivoFinal.value       = sArchivoFinal;
		document.all.txtFechaArchivo.value       = sFalta;
		seleccionarElemento(document.all.txtEstatusArchivo, sEstatus);
		
		$('#modalArchivos').removeClass("invisible");
		$('#modalArchivos').modal('toggle');
		$('#modalArchivos').modal('show');
	}


	// ******************  ALTA DE REGISTROS  *********************

	// Pantalla para agregar un nuevo criterio, muestra campos para captura de criterios y muestra tbl de unidades y documentos
	function agregarCriterioSeleccion(){
		document.all.txtOperacion.value = "INS";


		// Se borran los contenidos de los campos
		limpiarDatos();

		// Como es alta ocultar el elemento estatus y se oculta tambien el ID de criterio
		document.getElementById("divEstatus").style.display="none";
		document.getElementById("divIdCriterio").style.display="none";
		document.all.txtTipoCriterio.disabled = false;


		// Permite la modificación del id de criterio.
		document.all.txtIdCriterio.disabled = false;
		// Oculta la lista de criterios selccionados
		document.all.listaCriteriosSeleccion.style.display='none';
		// Oculta los botones de asignar unidades o entidades y documentos.
		//document.getElementById('btnUnidades').style.display='none';
		document.getElementById('btnDocumentos').style.display='none';

		// Depliega la pantalla de captura de criterios de selección
		document.all.capturaCriteriosSeleccion.style.display='inline';

	}

	// ******************  MODIFICACION DE REGISTROS  *******************

	// Pantalla para modificar un criterio, muestra campos para captura de criterios y muestra tbl de unidades y documentos
	function recuperarCriterioSeleccion(sIdCriterioSel){

		var liga = '/obtenerCriterioSeleccionById/' + sIdCriterioSel;

		$.ajax({ type: 'GET', url: liga ,
			success: function(response) {
	            //var jsonData = JSON.parse(response);   // Cuando se regresan mas de un registro    
				var obj = JSON.parse(response);		// Cuando solo se regresa un registro
				
				// Se borran los contenidos de los campos
				limpiarDatos();

				// Como es actualización es necesario asegurarse de que se muestre elemento estatus y también se oculta el Id Criterio
				document.getElementById("divEstatus").style.display="inline";
				document.getElementById("divIdCriterio").style.display="none";

				// Inhabilita el campo de id de criterio y el tipo de criterio 
				document.all.txtIdCriterio.disabled = true;
				document.all.txtTipoCriterio.disabled = true;

				//alert(response);
				document.all.txtOperacion.value = "UPD";
				document.all.txtIdCriterio.value = obj.id;
				document.all.txtFuenteInfo.value = obj.fuente;
				document.all.txtInformacion.value = obj.informacion;
				document.all.txtFecInformacion.value = obj.fecInformacion;
				seleccionarElemento(document.all.txtTipoCriterio, obj.idTipoCriterio);
				seleccionarElemento(document.all.txtEstatus, obj.estatus);

				document.all.listaCriteriosSeleccion.style.display='none';
				document.all.capturaCriteriosSeleccion.style.display='inline';

				// Impide la modificación del id de criterio.
				document.all.txtIdCriterio.disabled = true;
				// Permite la visualización de los botones de asignación de unidades y documentos por si se hubieran apagado en una alta.
				//document.getElementById('btnUnidades').style.display='inline';
				document.getElementById('btnDocumentos').style.display='inline';

				// Recupera la tabla de unidades relacionadas a la clave de criterio auxiliar 
   				recuperarTablaUnidades('obtenerUnidadesByCriterioSeleccion', document.all.txtIdCriterio.value, tblListaEntidades);
   				recuperarTablaDocumentos('obtenerDocumentosByCriterioSeleccion', document.all.txtIdCriterio.value, tblListaArchivos);

   				validaDisponibilidadTablaUnidades(obj.idTipoCriterio);


			},
			error: function(xhr, textStatus, error){
				alert(' Error en function recuperarTipoCriterio()  TextStatus: ' + textStatus + ' Error: ' + error );
				return false;
			}			
		});												
	}

	function limpiarDatos(){

		document.all.txtIdCriterio.value = '';
		document.all.txtTipoCriterio.selectedIndex=0;
		document.all.txtFuenteInfo.value="";
		document.all.txtFecInformacion.value="";
		document.all.txtInformacion.value="";
		document.getElementById('tblListaEntidades').innerHTML = "";
		document.getElementById('tblListaArchivos').innerHTML = "";
	}

	function validarDatos(){

		if (document.all.txtTipoCriterio.selectedIndex == 0){
			alert("Debe seleccionar un TIPO de CRITERIO de SELECCIÓN.");
			document.all.txtTipoCriterio.focus();
			return false;
		}	

		if (document.all.txtFuenteInfo.value == ""){
			alert("Debe ingresar la FUENTE/TÍTULO de la INFORMACIÓN.");
			document.all.txtFuenteInfo.focus();
			return false;
		}	

		if (document.all.txtFecInformacion.value == "" ){
			alert("Debe ingresar la FECHA de la INFORMACIÓN.");
			document.all.txtFecInformacion.focus();
			return false;
		}	

		if (document.all.txtInformacion.value == ""){
			alert("Debe ingresar el CONTENIDO de la INFORMACIÓN.");
			document.all.txtInformacion.focus();
			return false;
		}	


		return true;
	}


	function validarDatosUnidad(){

		if (document.all.txtUnidad.selectedIndex == 0){
			alert("Debe seleccionar una UNIDAD.");
			document.all.txtUnidad.focus();
			return false;
		}	

		return true;
	}


	function validaCriterioUnidad(sIdCriterio, sCveUnidad){

		//alert( "El valor de sIdCriterio=>" + sIdCriterio + " El de sCveUnidad=>" + sCveUnidad);

		$.ajax({
			type: 'GET', url: '/validaCriterioUnidad/' + sIdCriterio + '/' + sCveUnidad ,
			success: function(response) {
				//alert(response);
				//var obj = JSON.parse(response);
				sRespuesta = response.replace(/^\s+/g,'').replace(/\s+$/g,'');

				//alert(sRespuesta);

				if(sRespuesta == "EXISTE"){
					alert("ATENCION: Ya existe la unidad seleccionada para el criterio actual, por favor verifique.");
					return false;
				}else{
					return true;
				}
		},
			error: function(xhr, textStatus, error){
				alert('function validaCriterioUnidad()   -> statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
				return false;
			}
		});		
	}

	function validaDisponibilidadTablaUnidades(pIdTipoCriterio){
		//  Debe recuperar en base al tipo de criterio el campo que indica si la sección de unidades relacionadas estará habilitada.

		$.ajax({
			type: 'GET', url: '/obtenerTipoCriterio/' + pIdTipoCriterio ,
			success: function(response) {
				//alert(response);
				var obj = JSON.parse(response);

				if(obj.relacionarEntes == "SI"){
					document.getElementById("divListaEntidades").style.visibility = "visible";
					document.getElementById("divListaEntidades").style.display = "inline-block";
				}else{
					document.getElementById("divListaEntidades").style.visibility = "collapse";
					document.getElementById("divListaEntidades").style.display = "none";

				}
		},
			error: function(xhr, textStatus, error){
				alert('function validaDisponibilidadTablaUnidades()   -> statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
			}
		});		

	}

	// ********************************************* ZONA DE JQUERY ******************************************

	var nUsr='<?php echo $_SESSION["idUsuario"];?>';		
	var nCampana='<?php echo $_SESSION["idCuentaActual"];?>';	

	$(document).ready(function(){

		getMensaje('txtNoti',1);

		if(nUsr!="" && nCampana!=""){
			cargarMenu( nCampana);			
		}else{
			if(nCampana=="")alert("Debe establecer una CAMPAÑA como PREDETERMINADA. Por favor consulte con su administrador del sistema.");
		}					

		// ******************************* ÁREA DE CARGA DE CATALOGOS A COMBOS *******************************

		recuperarLista('lstTiposCriterios', document.all.txtTipoCriterio);
		recuperarLista('listaAllUnidades', document.all.txtUnidad);

		document.getElementById('btnUpload').addEventListener('change', abrirArchivoAnexo, false);

		// **************************** DEFINICIONES DE BOTONES ***********************************************
		$( "#btnGuardar" ).click(function() {

			if ( validarDatos() ){

				document.all.txtIdCriterio.disabled = false;
				document.all.txtListaUnidadesSeleccionadas.value = "";

				sUnidadesSeleccionadas = "";
				sSeparador = "";

				//alert("Se guardara " + lstUnidadesSeleccionadas.length + " unidades relacionadas");

				if(lstUnidadesSeleccionadas.length > 0){
					for(i=0; i < lstUnidadesSeleccionadas.length; i++){
						sUnidadesSeleccionadas += sSeparador + lstUnidadesSeleccionadas[i][0] + '|' + lstUnidadesSeleccionadas[i][1] +  '|' +lstUnidadesSeleccionadas[i][2] + '|' + lstUnidadesSeleccionadas[i][3] + '|' + lstUnidadesSeleccionadas[i][4] +  '|' +lstUnidadesSeleccionadas[i][5] + '|' + lstUnidadesSeleccionadas[i][6];
						sSeparador = '*';
					}
				}

				document.all.txtListaUnidadesSeleccionadas.value = sUnidadesSeleccionadas;

				// Ahora se asegura que este habilitado el campo de tipo de criterio, el cual en un UPD esta apagado y por lo tanto
				// no seré enviado en el submit y el post no lo reconocera, si es un INS no importa si lo habilita ya que ese es su valor en un insert.
				
				document.all.txtTipoCriterio.disabled = false;
				document.all.formulario.submit();
				//document.all.botonAsignarCriterios.style.display='none';
				//limpiarCamposAuditoria();
				//limpiaTabla(document.all.tblListaCriterios);
				//limpiaTabla(document.all.tblListaArchivos);
			}
		});

		$( "#btnCancelar" ).click(function() {
			document.all.listaCriteriosSeleccion.style.display='inline';
			document.all.capturaCriteriosSeleccion.style.display='none';
		});

		//$( "#btnCancelarAsignacion").click(function(){
		//	$('#modalFlotante').modal('hide');
		//});

		$( "#txtInformacionEdit" ).click(function(){
			document.all.txtTextoAmplio.value = document.all.txtInformacion.value;
			$('#modalTextoLargo').modal('show');
			sEditando="I";
		});	

		$( "#btnGuardarTexto" ).click(function() {
			$('#modalTextoLargo').modal('hide');				
			if(sEditando=="I") document.all.txtInformacion.value=document.all.txtTextoAmplio.value;
		});		

		/*
			--Para inhabilitar el datepicker
			$('CAMPO ID').datepicker("option", "disabled", false);
			---Para habilitar el datepicker.
			$('CAMPO ID').datepicker("option", "disabled", true);
		*/

		$( "#btnUnidades" ).click(function() {
			
			//alert("El valor de document.all.txtIdCriterio.value: " + document.all.txtIdCriterio.value);
			document.all.txtOperacionUnidad.value = 'INS';
			document.all.txtUnidad.selectedIndex = 0;
			//recuperarListaLigada('listaUnidades', document.all.txtIdCriterio.value, document.all.txtUnidad);
			//recuperarListaLigada('listaSAllUnidades', document.all.txtUnidad);
	
			// Oculta el botón de quitar la asignación de una unidad
 			document.all.btnAsignarUnidad.style.display='inline';
			document.all.btnDesasignarUnidad.style.display='none';

			$('#modalUnidades').modal('show');
		});		

		$( "#btnAsignarUnidad" ).click(function() {
			var lstValores = "";
			var claveUnidad = "";

			if (validarDatosUnidad()){

				// Inciará validando que no exista preseleccionada el área a el criterio en cuestioón.
				
				//alert( "El valor de sIdCriterio=>" + sIdCriterio + " El de sCveUnidad=>" + sCveUnidad);

				$.ajax({
					type: 'GET', url: '/validaCriterioUnidad/' + document.all.txtIdCriterio.value + '/' + document.all.txtUnidad.options[document.all.txtUnidad.selectedIndex].value ,
					success: function(response) {
						//alert(response);
						sRespuesta = response.replace(/^\s+/g,'').replace(/\s+$/g,'');
						if(sRespuesta == "EXISTE"){
							alert("ATENCION: Ya existe la unidad seleccionada para el criterio actual, por favor verifique.");
						}else{

							document.all.txtIdCriterioAuxUnidad.value = document.all.txtIdCriterio.value;

							claveUnidad = document.all.txtUnidad.options[document.all.txtUnidad.selectedIndex].value;
							
							lstValores = document.all.txtOperacionUnidad.value + "|" + document.all.txtIdCriterioUnidad.value + "|" + document.all.txtIdCriterioAuxUnidad.value + "|" + claveUnidad.substr( 0, 2 ) + "|" + claveUnidad.substr( 2, 2 ) + "|" + claveUnidad.substr( 4, 2 ) + "|" + document.all.txtEstatusUnidad.value;

							$.ajax({ type: 'GET', url: '/guardar/criteriosUnidades/' + lstValores,
								success: function(response) {
									sRespuesta = response.replace(/^\s+/g,'').replace(/\s+$/g,'');
									if(sRespuesta == "OK"){
						  				recuperarTablaUnidades('obtenerUnidadesByCriterioSeleccion', document.all.txtIdCriterio.value, tblListaEntidades);
										document.all.divListaEntidades.style.display='inline-block';
										return true;
									}else{
										alert("No fue posible guardar la unidad."+ response);
										return false;
									}
								},
								error: function(xhr, textStatus, error){
									alert(' Error en function para asignar la unidad al critero,  TextStatus: ' + textStatus + ' Error: ' + error );
									return false;
								}			
							});

							$('#modalUnidades').modal('hide');
						}
				},
					error: function(xhr, textStatus, error){
						alert('function validaCriterioUnidad()   -> statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
						return false;
					}
				});		

				/*
				if (validaCriterioUnidad(document.all.txtIdCriterio.value, document.all.txtUnidad.options[document.all.txtUnidad.selectedIndex].value) ) {
					//alert("El valor de document.all.txtIdCriterio.value: " + document.all.txtIdCriterio.value);
					document.all.txtIdCriterioAuxUnidad.value = document.all.txtIdCriterio.value;

					//alert("El valor de document.all.txtIdCriterio.value: " + document.all.txtIdCriterio.value + " Y el valor de document.all.txtIdCriterioAuxUnidad.value: " + document.all.txtIdCriterioAuxUnidad.value);

					claveUnidad = document.all.txtUnidad.options[document.all.txtUnidad.selectedIndex].value;
					lstValores = document.all.txtOperacionUnidad.value + "|" + document.all.txtIdCriterioUnidad.value + "|" + document.all.txtIdCriterioAuxUnidad.value + "|" + claveUnidad.substr( 0, 2 ) + "|" + claveUnidad.substr( 2, 2 ) + "|" + claveUnidad.substr( 4, 2 ) + "|" + document.all.txtEstatusUnidad.value;

					$.ajax({ type: 'GET', url: '/guardar/criteriosUnidades/' + lstValores,
						success: function(response) {
							sRespuesta = response.replace(/^\s+/g,'').replace(/\s+$/g,'');
							if(sRespuesta == "OK"){
				  				recuperarTablaUnidades('obtenerUnidadesByCriterioSeleccion', document.all.txtIdCriterio.value, tblListaEntidades);
								//alert("Se guardo el comentario correctamente.");
								document.all.divListaEntidades.style.display='inline';
								return true;
							}else{
								alert("No fue posible guardar la unidad."+ response);
								return false;
							}
						},
						error: function(xhr, textStatus, error){
							alert(' Error en function para asignar la unidad al critero,  TextStatus: ' + textStatus + ' Error: ' + error );
							return false;
						}			
					});

					//document.all.formularioUnidades.submit();

					//document.all.listaCriteriosSeleccion.style.display='none';
					//document.all.capturaCriteriosSeleccion.style.display='inline';

					$('#modalUnidades').modal('hide');
				}
				*/
			}
		});		

		$( "#btnDesasignarUnidad" ).click(function() {

			confirmacion = confirm("¿Esta seguro que desea quitar la relación de la unidad al criterio de selección?" );
	
			if (confirmacion == true){ 	

				var claveUnidad = document.all.txtUnidad.options[document.all.txtUnidad.selectedIndex].value;
							
				 var sector    = claveUnidad.substr( 0, 2 ) ;
				 var subSector = claveUnidad.substr( 2, 2 ) ;
				 var unidad    = claveUnidad.substr( 4, 2 ) ;

				 //alert("El valor de txtIdCriterio=>" + document.all.txtIdCriterio.value + " sector=>" + sector + " subSector=>" +  subSector + " unidad=>" + unidad);

				 var liga = '/eliminar/criterioUnidad/' + document.all.txtIdCriterio.value + "/" + sector + "/" + subSector + "/" + unidad;

				$.ajax({ type: 'GET', url: liga ,
					success: function(response) {
						sRespuesta = response.replace(/^\s+/g,'').replace(/\s+$/g,'');
						if(sRespuesta == "OK"){
			  				recuperarTablaUnidades('obtenerUnidadesByCriterioSeleccion', document.all.txtIdCriterio.value, tblListaEntidades);
							document.all.divListaEntidades.style.display='inline-block';
							return true;
						}else{
							alert("No fue posible quitar la relación de la unidad."+ response);
							return false;
						}
					},
					error: function(xhr, textStatus, error){
						alert(' Error en la function que quitar la relación de la unidad al critero,  TextStatus: ' + textStatus + ' Error: ' + error );
						return false;
					}			
				});

				$('#modalUnidades').modal('hide');

			}
		});		

		// Procesos para la carga y borrado de archivos anexos

		$( "#btnAgregarArchivosAnexos" ).click(function() { $("#btnUpload").click(); });

		$( "#btnDesasociarArchivo" ).click(function() { 

			confirmacion = confirm("¿Esta seguro que desea desasociar el siguiente archivo?:\n \n" + document.all.txtArchivoOriginal.value);
			if (confirmacion == true){
				var liga = 'eliminar/criterioDocumento/' + document.all.txtIdCriterioDocto.value;

				//alert("El valor de liga es: " + liga);

				$.ajax({ type: 'GET', url: liga ,
					success: function(response) {
						sRespuesta = response.replace(/^\s+/g,'').replace(/\s+$/g,'');
						if(sRespuesta == "OK"){
			  				recuperarTablaDocumentos('obtenerDocumentosByCriterioSeleccion', document.all.txtIdCriterio.value, tblListaArchivos);
							document.all.divListaArchivos.style.display='inline';
							borrarArchivoAnexo(document.all.txtArchivoFinal.value, true);

							return true;
						}else{
							alert("No fue posible guardar el documento."+ response);
							return false;
						}
					},
					error: function(xhr, textStatus, error){
						alert(' Error en function para registrar el documento al critero,  TextStatus: ' + textStatus + ' Error: ' + error );
						return false;
					}			
				});

				$('#modalArchivos').modal('hide');			
			}

		});

		$( "#btnDocumentos" ).click(function() { 
			$("#btnUpload").click();
		});

		$( "#btnCancelarArchivo" ).click(function() {
			$('#modalArchivos').modal('hide');			
		});		

		$( "#btnGuardarArchivo" ).click(function() {

			var lstValores = document.all.txtOperacionArchivo.value + "|" + document.all.txtIdCriterioDocto.value + "|" + document.all.txtIdCriterioAuxDocto.value + "|" +  document.all.txtArchivoOriginal.value + "|" +  document.all.txtArchivoFinal.value + "|" + document.all.txtEstatusArchivo.value;

			$.ajax({ type: 'GET', url: '/guardar/criteriosDocumentos/' + lstValores,
				success: function(response) {
					sRespuesta = response.replace(/^\s+/g,'').replace(/\s+$/g,'');
					if(sRespuesta == "OK"){
		  				recuperarTablaDocumentos('obtenerDocumentosByCriterioSeleccion', document.all.txtIdCriterio.value, tblListaArchivos);
						document.all.divListaArchivos.style.display='inline';
						return true;
					}else{
						alert("No fue posible guardar el documento."+ response);
						return false;
					}
				},
				error: function(xhr, textStatus, error){
					alert(' Error en function para registrar el documento al critero,  TextStatus: ' + textStatus + ' Error: ' + error );
					return false;
				}			
			});
			$('#modalArchivos').modal('hide');			
		});		

		// **************************** FIN DE DEFINICIONES DE BOTONES ********************************************

	});

	</script>

	<!-- **************************************  FINALIZA ÁREA DE JAVA SCRIPT ******************************* -->

	<!-- *******************************************  CONTINUA ÁREA HTML ************************************ -->


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
 	<!-- SECCCION DE ENCABEZADO PRINCIPAL -->
    <nav class="navbar navbar-default navbar-fixed-top">
		<div class="container-fluid">
			<nav class="collapse navbar-collapse bs-navbar-collapse" role="navigation">			
				<div class="col-xs-12">
					<div class="col-xs-2"><a href="/"><img src="img/logo-top.png"></a></div>				
					<div class="col-xs-2">
						<ul class="nav navbar-nav "><li><a href="#"><i class="fa fa-th-list"></i> <?php echo $_SESSION["sCuentaActual"] ?></a></li></ul>
					</div>					
					<div class="col-xs-3"><h2>Criterios de Selección</h2></div>									
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

	<div class="content">
		<!-- DIVISION DEL IZQUIERDA CONTENEDORA DEL MENU-->
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
		</div>
		 <!-- Sidebar ends -->

		<!-- Main content starts -->

		<div class="mainbar"> 	  	
		<!--
	  	<div class="panel panel-default">
			<div class="panel-body">
		-->			
				<div class="row" id="listaCriteriosSeleccion" style=" padding:0px; margin:0px;">
					<div class="col-md-12">
						<div class="widget">
							<div class="widget-head">
								<div class="pull-left"><h3 class="pull-left"><i class="fa fa-home"></i> Criterios de Selección registrados</h3></div>
								<div class="widget-icons pull-right">
									<button onclick="agregarCriterioSeleccion();" type="button" class="btn btn-primary  btn-xs">Agregar Criterio </button> 
								</div>  
								<div class="clearfix"></div>
							</div>             

							<div class="widget-content">						
								<div class="table-responsive" style="height: 400px; overflow: auto; overflow-x:hidden;">
									<table class="table table-striped table-bordered table-hover">
										<thead>
											<tr>
										 		<!-- <th>Id. Criterio Selección</th> -->
										 		<th>Tipo de Selección</th>
										 		<th>Fuente/Título de Información</th>
										 		<th>Fecha de Información</th>
										  		<th>Estatus del Criterio</th>
											</tr>
									  	</thead>

									  	<tbody >					
												<?php foreach($datos as $key => $valor): ?>
												<tr onclick=<?php echo "javascript:recuperarCriterioSeleccion('" . $valor['id'] . "');"; ?> >
												  <!-- <td width="10%"><?php echo $valor['id']; ?></td> -->
												  <td width="30%"><?php echo $valor['tipoCriterio']; ?></td>
												  <td width="30%"><?php echo $valor['fuente']; ?></td>
												  <td width="10%"><?php echo $valor['fecInformacion']; ?></td>
												  <td width="10%"><?php echo $valor['estatus']; ?></td>
												</tr>
												<?php endforeach; ?>                                                                   
									  	</tbody>
									</table>
								</div>
							</div>


							<div class="widget-foot">
								<!--
								<div class="pull-left">
									<button onclick="agregarAuditoria();" type="button" class="btn btn-primary  btn-xs"><i class="fa fa-search"></i> Agregar Auditoría...</button>
								</div>
								<div class="clearfix"></div>
								-->
							</div>
						</div>
					</div>
				</div>
			  
				<div class="row" id="capturaCriteriosSeleccion" style="display:none; padding:0px; margin:0px;">
					<form id="formulario" METHOD='POST' action='/guardar/criterioSeleccion' role="form">
						<input type='HIDDEN' name='txtOperacion'value=''>
						<input type='HIDDEN' name='txtCuenta' value=''>						
						<input type='HIDDEN' name='txtPrograma' value=''>
						<input type='HIDDEN' name='txtListaUnidadesSeleccionadas' value=''>

						<div class="col-md-12">				
							<div class="widget">
								<!-- Widget head -->
								<div class="widget-head">
									<div class="pull-left"><h3>
										<i class="fa fa-pencil-square-o"> </i> Registro de Criterio de Selección</h3>
									</div>
									<div class="widget-icons pull-right">
										<!-- <button type="button" id="btnCriterios" class="btn btn-primary  btn-xs ">Asignación de Criterios</button>  -->
										<button type="button"  id="btnGuardar" class="btn btn-primary  btn-xs"><i class="fa fa-floppy-o"></i> Guardar</button>
										<button type="button" id="btnCancelar" class="btn btn-default  btn-xs"><i class="fa fa-undo"></i> Regresar</button> 
							  		</div>  
								  	<div class="widget-icons pull-right" id="botonera">
								  		<!--
										<button type="button" id="btnUnidades" class="btn btn-warning btn-xs "><i class="fa fa-external-link"></i> Relacionar Unidades...</button> 
										-->
										<button type="button" id="btnDocumentos" class="btn btn-warning btn-xs "><i class="fa fa-file-o"></i> Asociar Documentos...</button> 

										<input type="file" name="btnUpload" accept="application/pdf,application/vnd.ms-excel,,application/vnd.ms-word, " style="display:none;" id="btnUpload">

										<progress id="progressBar" value="0" max="100" style="width:'100%'; display:none;"></progress>

										<label>  </label> 
								  	</div>  
									<div class="clearfix"></div>
								</div>              

								<!-- Widget content -->
								<div class="widget-content">												
									<br>
									<div class="col-xs-7" >															
										<div class="form-group" id="divIdCriterio">									
											<label class="col-xs-3 control-label" id="">Id. Criterio de Selección</label>
											<div class="col-xs-1">
												<input type="text" class="form-control" name="txtIdCriterio" readonly/>
											</div>	
										</div>								
										<br>
										<div class="clearfix"></div>

										<div class="form-group">									
											<label class="col-xs-3 control-label">Tipo de Criterio</label>
											<div class="col-xs-5">
												<select class="form-control" name="txtTipoCriterio" id="txtTipoCriterio"  onChange="javascript: validaDisponibilidadTablaUnidades(this.value);">
													<option value="">Seleccione...</option>
												</select>
											</div>								
										</div>								
										<br>

										<div class="form-group">									
											<label class="col-xs-3 control-label" id="">Fuente/Título de Información</label>
											<div class="col-xs-8">
												<input type="text" class="form-control" name="txtFuenteInfo" />
											</div>	
										</div>								
										<br>

										<div class="form-group" id="">
											<label class="col-xs-3 control-label">Fecha de Información</label> 
											<div class="col-xs-2"> 
												<input type="text" id="datepicker" class="form-control" name="txtFecInformacion" />
											</div>	
										</div>									
										<br>

										<div class="form-group">
											<label class="col-xs-3 control-label">Información <i class="fa fa-pencil"  id="txtInformacionEdit"></i></label>
											<div class="col-xs-8"><textarea class="form-control" rows="5" name="txtInformacion" id="txtInformacion" style=" margin: 0 0 1% 0 !important;" ></textarea>
											</div>
										</div>
										<div class="clearfix"></div>

										<div class="form-group" id="divEstatus">									
											<label class="col-xs-3 control-label">Estatus del Criterio</label>
											<div class="col-xs-2">
												<select class="form-control" name="txtEstatus" id="txtEstatus" >
													<option value="">Seleccione...</option>
													<option value="ACTIVO" selected="">ACTIVO</option>
													<option value="INACTIVO">INACTIVO</option>
												</select>
											</div>								
										</div>								
										<br>

									</div>
									<br>								

									<div class="col-xs-5">							
										<div class="form-group">
											<div class="table-responsive" style="height: 300px; overflow: scroll; overflow-x:scroll;" id="divListaEntidades">
												<table class="table table-striped table-bordered table-hover table-condensed table-responsive" >
													<caption>Unidades Relacionadas</caption>
													<thead>
														<tr><th style="width: 5%" >Sel.</th><th style="width: 15%" >Centro Gestor</th><th style="width: 80%">Nombre de Unidad</th></tr>
													</thead>
													<tbody id="tblListaEntidades" >
													</tbody>
													<!--
													<tbody id="tblListaEntidades" style="width: 150%; height: 175px; font-size: xx-small overflow: 		scroll; overflow-x:hidden;">
													</tbody>
													-->
												</table >
											</div>
											<br>

											<div class="table-responsive" style="height: 175px; overflow: auto; overflow-x:hidden; " id="divListaArchivos">
												<table class="table table-striped table-bordered table-hover table-condensed table-responsive" >
													<caption>Documentos Asociados</caption>
													<thead>
														<tr><th>Nombre de Documento</th><th>Fecha de Documento</th><th>Estatus</th><th>Archivo</th></tr>
													</thead>
													<tbody id="tblListaArchivos" style="width: 150%; font-size: xx-small">
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
							</div>
						</div>
					</form>
				</div>
			<!--
				<div class="clearfix"></div>
			</div>
			-->
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
	

	<div id="modalUnidades" class="modal fade" role="dialog">
		<div class="modal-dialog">							
				<!-- Modal content-->
			<div class="modal-content">									
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h3 class="modal-title"><i class="fa fa-pencil-square-o"></i>Relación de Unidades...</h3>
				</div>									
				<div class="modal-body">
					<form id="formularioUnidades" METHOD='POST' ACTION='/guardar/criteriosUnidades' role="form">
						<input type='HIDDEN' name='txtCriterioUnidadAnterior' value=''> 
						<input type='HIDDEN' name='txtOperacionUnidad' value=''>
						<input type='HIDDEN' name='txtIdCriterioUnidad' value=''>
						<input type='HIDDEN' name='txtIdCriterioAuxUnidad' value=''>

						<br>
						<div class="form-group">						
							<label class="col-xs-3 control-label">Unidades</label>
							<div class="col-xs-8">
								<!--
								<select class="form-control" name="txtUnidad" onChange="javascript: validaCriterioUnidad(document.all.txtIdCriterio.value, this.value, document.all.txtCriterioAnterior.value);">
								-->	
								<select class="form-control" name="txtUnidad">
									<option value="">Seleccione...</option>
								</select>
							</div>
						</div>
						<br>

						<div class="form-group">									
							<label class="col-xs-3 control-label">Estatus de Unidad Asignada</label>
							<div class="col-xs-2">
								<select class="form-control" name="txtEstatusUnidad" id="txtEstatusUnidad" >
									<option value="">Seleccione...</option>
									<option value="ACTIVO" selected="">ACTIVO</option>
									<option value="INACTIVO">INACTIVO</option>
								</select>
							</div>								
						</div>								
						<br>

						<div class="clearfix"></div>								
					</form>
					<div class="clearfix"></div>
				</div>
				
				<div class="modal-footer">
					<div class="pull-right">
						<button type="button" class="btn btn-primary active" id="btnDesasignarUnidad" ><i class="fa fa-eraser"></i> Quitar Relación</button>
						<button type="button" class="btn btn-primary active" id="btnAsignarUnidad" ><i class="fa fa-floppy-o"></i> Relacionar</button>
						<button type="button" class="btn btn-default active" id="btnCancelarUnidad" data-dismiss="modal"><i class="fa fa-back"></i> Cancelar</button>
					</div>
					<div class="clearfix"></div>
				</div>
			</div>		
		</div>
	</div>


	<div id="modalArchivos" class="modal fade" role="dialog">
		<div class="modal-dialog">							
				<!-- Modal content-->
			<div class="modal-content">									
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h3 class="modal-title"><i class="fa fa-pencil-square-o"></i>Detalle de Documento...</h3>
				</div>									
				<div class="modal-body">
					<form id="formularioArchivos" METHOD='POST' ACTION='/actualiza/criteriosDocumentos' role="form">
						<input type='HIDDEN' name='txtOperacionArchivo' value=''>
						<input type='HIDDEN' name='txtIdCriterioDocto' value=''>
						<input type='HIDDEN' name='txtIdCriterioAuxDocto' value=''>
						<input type='HIDDEN' name='txtArchivoFinal' value=''>

						<div class="form-group">						
							<label class="col-xs-4 control-label">Nombre de Documento</label>
							<div class="col-xs-6">
								<input type="text" class="form-control" name="txtArchivoOriginal" id="txtArchivoOriginal" readonly />
							</div>
						</div>
						<br>

						<div class="form-group">						
							<label class="col-xs-4 control-label">Fecha del Documento</label>
							<div class="col-xs-6">
								<input type="text" class="form-control" name="txtFechaArchivo" id="txtFechaArchivo" readonly />
							</div>
						</div>
						<br>

						<div class="form-group">									
							<label class="col-xs-4 control-label">Estatus de Documento</label>
							<div class="col-xs-6">
								<select class="form-control" name="txtEstatusArchivo" id="txtEstatusArchivo" >
									<option value="">Seleccione...</option>
									<option value="ACTIVO" selected="">ACTIVO</option>
									<option value="INACTIVO">INACTIVO</option>
								</select>
							</div>								
						</div>								
						<br>

					</form>
					<div class="clearfix"></div>
				</div>
				
				<div class="modal-footer">
					<div class="pull-right">
						<button type="button" class="btn btn-primary active" id="btnDesasociarArchivo" ><i class="fa fa-eraser"></i> Desasociar Documento</button>

						<button type="button" class="btn btn-primary active" id="btnGuardarArchivo" type="submit"><i class="fa fa-eraser"></i> Guardar</button>

						<button type="button" class="btn btn-default active" id="btnCancelarArchivo" data-dismiss="modal"><i class="fa fa-back">
							
						</i> Cancelar</button>
					</div>
					<div class="clearfix"></div>
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
