var bEncontrado;
var cantllamadas=0;
var mensajes;
var cantllamadas2=0;
var mensajes2;

	function proximaEtapa(proceso, etapa){
		liga = '/proximaEtapa/' + proceso + '/' + etapa;
		//alert("El valor de liga desde proximaEtapa es:" + liga);
		$.ajax({
			type: 'GET', url: liga ,
			success: function(response) {
				var obj = JSON.parse(response);
				if (typeof(obj.boton) !== "undefined") {
					if (obj.boton != null) {
						if (obj.boton.trim().length>0){
							if (document.getElementById(obj.boton)!='' ){
								document.getElementById(obj.boton).style.display = "inline";
								sProcesoNuevo = obj.proceso;
								sEtapaNueva = obj.etapa;
								sEtpaNombre = obj.nombre;		

							}
						}
					}				
				}
			},
			error: function(xhr, textStatus, error){
				alert('ERROR: En function proximaEtapa(proceso, etapa)  ->  statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
			}			
		});		
	}
		
	function asignarEtapa(auditoria, proceso, etapa){		
			$.ajax({
				type: 'GET', url: '/asignarEtapa/' + auditoria + '/' + proceso + '/' + etapa,
				success: function(response) {
					var obj = JSON.parse(response);
					proximaEtapa(proceso, etapa);
				},
				error: function(xhr, textStatus, error){
					alert('ERROR: En function asignarEtapa(auditoria, proceso, etapa)  ->  statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
				}			
			});		
		}

	//funcion nueva para asignación de la etapa, para la autorización
	function asignarAutorizacionEtapa(auditoria, proceso, etapa){		
		$.ajax({
			type: 'GET', url: '/asiganaciondeetapa/' + auditoria + '/' + proceso + '/' + etapa,
			success: function(response) {
				var obj = JSON.parse(response);
				proximaEtapa(proceso, etapa);
			},
			error: function(xhr, textStatus, error){
				alert('ERROR: En function asiganaciondeetapa(auditoria, proceso, etapa)  ->  statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
			}			
		});		
	}

		
	function pintarAuditorias(lista, mapa){		
			$.ajax({
				type: 'GET', url: '/' + lista,
				success: function(response) {				
					var jsonData = JSON.parse(response);
					for (var i = 0; i < jsonData.datos.length; i++) {
						dato = jsonData.datos[i];
						var posActual = new google.maps.LatLng(dato.latitud, dato.longitud);
						var infoWindow = new google.maps.InfoWindow({});
							infoWindow.setContent("<p class='cabezaInfo'>CLAVE AUDITORIA " + dato.claveAuditoria + "  </p><hr>" +  dato.tipoAuditoria +   "<br><br><br>");
							infoWindow.setPosition(posActual);
							infoWindow.open(mapa);
					}								
				},
				error: function(xhr, textStatus, error){
					alert('ERROR: En function  pintarAuditorias(lista, mapa)  ->  statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
				}			
			});		
		}	


	// ***********************************  
	function cargarMenu(sCampana){
		var sPanel ="";
		var nRenglon=0;
		var nTotal=0;
		var dato;
		console.log(sCampana);
		$.ajax({
			type: 'GET',
			url: '/lstModulosByUsuarioCampana/' + sCampana,
			success: function(response) {
				var jsonData = JSON.parse(response);				
				nTotal =jsonData.datos.length;
				
				for (var i = 0; i < jsonData.datos.length; i++) {
					dato = jsonData.datos[i];
					//modulos.push(dato.tipo, dato.panel, dato.modulo);
					
					sPanel = dato.panel;
					document.getElementById(sPanel).style.display="block";
					
					sPanel = sPanel + "-UL";

 					var ul = document.getElementById(sPanel);
						var li = document.createElement("li");						
							var ancla = document.createElement('a');							
							ancla.setAttribute('href', dato.liga);
								//PARA CARGAR UN ICONO
								var icono = document.createElement("li");
								icono.setAttribute("class", dato.icono);							
								ancla.appendChild(icono);								
								
								var texto = document.createTextNode( " " + dato.nombre);
								ancla.appendChild(texto);								
							nRenglon = nRenglon+1;							
						li.appendChild(ancla);
					ul.appendChild(li); 
				}
			},
			error: function(xhr, textStatus, error){
				alert('ERROR: En function cargarMenu(sCampana)  ->  statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
			}			
		});		
	}
	
	
	// ***********************************  
	function asignarElemento(cmb, valor, texto){
		//Vacia la lista
		while (cmb.length>=1){
			cmb.remove(cmb.length-1);
		}
		//Agregar elemento
		var option = document.createElement("option");
		option.text = "Seleccione...";
		option.value = '';
		cmb.add(option,0);
		
		if(texto!="" && texto!=""){
			option.text = texto;
			option.value = valor;
			cmb.add(option,1);		
			cmb.selectedIndex=1;
		}	
	}

	
	function prueba(){
		alert("Test desde genericas.js ");
	}
	
	// ***********************************  
	function recuperarListaLarga(lista, cmb){
		//alert("Recuperando Lista: " + lista);
		$.ajax({
				type: 'GET', url: '/'+ lista  ,
				success: function(response) {
					var jsonData = JSON.parse(response);
					
					//alert("Registros recuperados:" + jsonData.datos.length);

					//Vacia la lista
					while (cmb.length>=1){
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
					alert("ERROR EN: function recuperarLista(lista, cmb)" + " Donde lista=" + lista );
				}			
			});	
	} 


	// ***********************************  
	function recuperarLista(lista, cmb){
		//alert("Recuperando Lista: " + lista);
		$.ajax({
				type: 'GET', url: '/'+ lista  ,
				success: function(response) {
					var jsonData = JSON.parse(response);
					
					//alert("Registros recuperados:" + jsonData.datos.length);

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
					alert("ERROR EN: function recuperarLista(lista, cmb)" + " Donde lista=" + lista );
				}			
			});	
	} 
	
	// ***********************************  
	function recuperarListaLigada(lista, valor, cmb){
	
		if (valor=="") valor="***";
		
		//alert(' Cadena Enviada:       /'+ lista + '/' + valor );
		$.ajax({
			type: 'GET', url: '/'+ lista + '/' + valor ,
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
					cmb.add(option, i+1);
				}				
			},
			error: function(xhr, textStatus, error){
				alert("ERROR EN: function recuperarListaLigada(lista, valor, cmb)" + " Donde lista=" + lista );
			}
		});	
	}

	// ***********************************  
	function recuperarListaSelected(lista, parametro, cmb, id ){
		$.ajax({
			type: 'GET', url: '/'+ lista + '/' + parametro ,
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
				alert("ERROR EN: function recuperarListaSelected(lista, parametro, cmb, id)" + " Donde lista=" + lista );
			}			
		});	
	}	
	
	// ***********************************  
		function recuperarTablaLigada(lista, valor, tbl){
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
					renglon.innerHTML="<td>" + dato.distrito + "</td><td>" + dato.idSeccion + "</td><td>" + dato.idCasilla + "</td>	<td>" + dato.representante + "</td><td>" + dato.acreditado + "</td><td>" + dato.estatus + "</td>";
					renglon.onclick= function() {
						obtenerDatosCasilla(dato.idSeccion , dato.idCasilla ); 
					};
					tbl.appendChild(renglon);					
				}				
			},
			error: function(xhr, textStatus, error){
				alert("ERROR EN: function recuperarTablaLigada(lista, valor, tbl)" + " Donde lista=" + lista );
			}			
		});	
	}
	
	
	
	// ***********************************  
	function seleccionarElemento(cmb, value){		
		var nPosicion;
		nPosicion=0;
		bEncontrado=false;
		
		for(var i=0; i < cmb.options.length; i++){
			
			if(cmb[i].value == value) {
				cmb.selectedIndex = i;
				bEncontrado=true;
				nPosicion=i;
			}
		}
		
		if (bEncontrado==false){
			//alert("El Elemento " + value + " no se encontro en la lista.");
			cmb.selectedIndex = 0;
		}else{
			cmb.selectedIndex = nPosicion;
		}
		
  } 


	// ***************************************
		function limpiaTabla(tbl)
	{
        //Vacia la lista tipo tabla
        tbl.innerHTML="";
	}

	// ***********************************  
	function getMensaje(input, minutos){
		var objInput = document.getElementById(input);
		
	
	   $.ajax({
		type: 'GET', url: '/notifica' ,
		success: function(response) {
			var obj = JSON.parse(response);		 
			cantllamadas++;
			

			objInput.value='' + obj.valor;		 
			var asig = obj.valor;

			if (asig>0) {
				if (cantllamadas <= 5) 
					clearInterval(mensajes);
				{
				objInput.style.backgroundColor = '#FF9933';
				objInput.style.color = '#050505';			
				clearInterval(mensajes);
				mensajes = setTimeout(function(){getMensaje(input, minutos)},minutos*60000);
				}
				
			}else{
				if (cantllamadas2 <= 5) 
					clearInterval(mensajes2);
				{
				objInput.style.backgroundColor = '#8E8989';
				objInput.style.color = '#FFFFFF';	
				clearInterval(mensajes2);
				mensajes2 = setTimeout(function(){getMensaje(input, minutos)},120000);
				}
			}
		},
		error: function(xhr, textStatus, error){
		 alert(' Error en   function getMensaje    \n\nstatusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
		}   
	   }); 
	}
	

	// ***********************************  
	function setGrafica(instancia, datos, tipo,  titulo, canvas){
		var dps = [];
		var sNombreTabla = "tbl"+canvas;
		var tbl;
		$.ajax({
			type: 'GET', url:datos,
			success: function(response){
				var objs = JSON.parse(response);	
				var existeTblResumen=false;
				
				if (document.getElementById(sNombreTabla)!= null) {				
					existeTblResumen=true;
					tbl = document.getElementById("tbl"+canvas);
					tbl.innerHTML="";
				}
				
				//Cargar dataPoints
				var nMinimo=0;
				var nMaximo=0;
				for (var i = 0; i < objs.datos.length; i++) {
					dato = objs.datos[i];					
					if(tipo=="pie")dps.push({y:dato.valor, indexLabel: dato.texto});					
					if(tipo=="bar")dps.push({y: parseInt(dato.valor), label: dato.texto});					
					if(tipo=="line")dps.push({ x: "Jose",  y: 1000+ parseInt(dato.valor), indexLabel: dato.texto});
					
					if(parseInt(dato.valor)<nMinimo) nMinimo=parseInt(dato.valor);
					if(parseInt(dato.valor)>nMaximo) nMaximo=parseInt(dato.valor);
					
					if(existeTblResumen){
						var renglon;
						renglon=document.createElement("TR");			
						renglon.innerHTML="<td>" + dato.texto + "</td><td>" + dato.valor + "</td>";						
						renglon.onclick= function() {};
						tbl.appendChild(renglon);						
					}
				}
				
				

				if(tipo=="pie"){
					if(dps.lenght==0) dps.push({y:0, indexLabel: "Sin Valores"});
					instancia = new CanvasJS.Chart(canvas,
					{
						theme: "theme2",
						title:{text: titulo,fontSize: 12,verticalAlign: "top", horizontalAlign: "center"},
						data: [{type: "doughnut",showInLegend: true,toolTipContent: "Cantidad:{y} -> Porcentaje: #percent %", yValueFormatString: "#0.#", legendText: "{indexLabel}",dataPoints: dps}]
					});						
				}
				
				//Definir grafica
				if(tipo=="bar"){
					if(dps.lenght==0) dps.push({y:0, indexLabel: "Sin Valores"});
					instancia = new CanvasJS.Chart(canvas,
					{
						theme: "theme2",
						title:{text: titulo,fontSize: 12,verticalAlign: "top", horizontalAlign: "center"},
						  animationEnabled: true,
						  axisY:{
							title: "Cantidad",
							minimum: nMinimo,
							//maximum: nMaximo + (nMaximo/5),
							maximum: nMaximo,
						  },						  
						data: [{type: "column", dataPoints: dps}]
					});	
				}	

				if(tipo=="line"){
					instancia = new CanvasJS.Chart(canvas,
					{
						theme: "theme2",
						title:{text: titulo,fontSize: 12,verticalAlign: "top", horizontalAlign: "center"},
						animationEnabled: true,						  
						axisX: {interval:1,intervalType: "String",includeZero: true},
						axisY:{includeZero: true},						  
						data: [{type: "line",  dataPoints: dps}]
					});	
				}				
				
				instancia.render();					
			},
			error: function(xhr, textStatus, error){
				alert('function graficaPie()   -> statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error + ' Papel: ');
			}			
		});	
	}


	// ***********************************  
	function recuperarGif(archivo)
	{
		gif = "";
		pos = archivo.indexOf('.');
		ext = archivo.substr(pos+1);
		ext = ext.toUpperCase();
		
		if (ext=='XLS' || ext=='XLSX'){
			gif = '<img src="img/xls.gif" />';
		}else{ 
			if (ext=='DOC' || ext=='DOCX'){
				gif = '<img src="img/doc.gif" />';
			}else{
				if (ext=='PDF'){
					gif = '<img src="img/pdf.gif"/>';
				}else{
					if (ext=='ZIP'){
						gif = '<img src="img/zip.gif"/>';
					}else{
						gif = '<img src="img/xls.gif"/>';
					}
				}
			}
		}
		return gif;
	}

	// ***********************************  
	function checkfile(sender) {
		var validExts = new Array(".xlsx", ".xls", ".pdf", ".doc", ".docx", ".zip", ".rar");
		var fileExt = sender.value;
		fileExt = fileExt.substring(fileExt.lastIndexOf('.'));
		if (validExts.indexOf(fileExt) < 0) {
		  alert("El archivo seleccionado es inválido. Los formatos correctos son " +
				   validExts.toString() + ".");
		  return false;
		}
		else return true;
	}

    // ***********************************  
	function esTeclaNumerica(evt){
		evt = (evt) ? evt : window.event
         var charCode = (evt.which) ? evt.which : evt.keyCode
         if (charCode > 31 && (charCode < 48 || charCode > 57))
            return false;
 
         return true;
    }
	