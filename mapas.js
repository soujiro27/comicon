			var nUltimoDistrito;
			var nUltimaDelegacion;
		var lstInfos = [];
		var sContexto="";
		var nMarkando=false;
		var triangleCoords = [];				
		var bCrearPoligono=false;
		
		var nPinActual=0;
		
		var puntos = new Array();	
		var markers = [];
		
		
		var bermudaTriangle;
		

		var seccionActual="";
		var posicionInicial;
		var infoWindow;	

	var sPanelActivo="";		
	var mapa, mapaPpal;
	
	var bExisteInfo=false;		
	
	var opcionesMapa = {
		center: new google.maps.LatLng(19.4339249,-99.1428964),zoom:9,
		panControl:false	,zoomControl:true,mapTypeControl:false,scaleControl:false,streetViewControl:false,overviewMapControl:false,rotateControl:false,	
		mapTypeId: google.maps.MapTypeId.ROADMAP,
		styles: [
			{"stylers": [{"hue": "#2c3e50"},{"saturation": 250}]},
			{"featureType": "road","elementType": "geometry","stylers": [{"lightness": 50},{"visibility": "simplified"}]},
			{"featureType": "road","elementType": "labels","stylers": [{"visibility": "off"}]}
		]		
	};
	

	// ***********************************  
		function pintarInfo(texto, lat, lng){
		var iW;
			var posActual= new google.maps.LatLng(lat, lng);
			
			/*
			iW = new google.maps.InfoWindow();					
			iW.setContent('<p><b>' + texto + '</b></p>');
			iW.setPosition(pos1);
			iW.open(mapa);	
			mapa.setCenter(pos1);
			*/
			
			var infoWindow = new google.maps.InfoWindow({});
			infoWindow.setContent('<p><b>' + texto + '</b></p>');
			infoWindow.setPosition(posActual);
			infoWindow.open(mapa);	
			lstInfos.push(infoWindow);					
			mapa.setCenter(posActual);
			
			
			
		}

		// ***********************************  
		function pintarSecciones(distrito){	
			nUltimoDistrito=distrito;
			document.all.btnRegresar.style.display="none";
			var tbl = document.all.tablaElementos;
			$.ajax({
				type: 'GET', url: '/lstSeccionesGeoByDistrito/' + distrito ,
				success: function(response) {
					var nSeccionesGeolocalizadas=0;
					var jsonData = JSON.parse(response);
					if(jsonData.datos.length==0){alert("No se encontraron secciones.");return;}
					activarPanel('');
					
					//Vacia la lista
					tbl.innerHTML="";
					
					//Eliminar los infos
					for(var i=0; i<lstInfos.length; i++) lstInfos[i].close();
					lstInfos = [];
						
						
					//Agregar renglones
					var renglon, columna;
					for (var i = 0; i < jsonData.datos.length; i++) {
						var dato = jsonData.datos[i];
						if (dato.latitud==null) dato.latitud="";
						if (dato.longitud==null) dato.longitud="";
						
						var sPin="";
						
						if (dato.latitud!="" && dato.longitud!="") {
							sPin = "  <span class='fa fa-map-marker'> ";							
							var sTituloInfo="<a href='javascript:recuperaDatosSeccion(" + dato.id + ");'><b>Sección Electoral<br>" + dato.id + "</b></a>";						
							pintarInfo(sTituloInfo, dato.latitud, dato.longitud);							
							//alert(dato.latitud);	
							nSeccionesGeolocalizadas++;							
						}
						
						renglon=document.createElement("TR");				
						renglon.innerHTML="<td onclick='recuperaDatosSeccion(" + dato.id + ");'>" + dato.texto +  sPin  +  "</td><td>" + dato.habitantes + "</td><td>" + dato.estatus + "</td>";						
						tbl.appendChild(renglon);
					}
						if(nSeccionesGeolocalizadas==0) alert("Ninguna sección se ha Geolocalizado.");					
				},
				error: function(xhr, textStatus, error){
					alert("ERROR EN: function pintarSecciones()" );
				}			
			});	
		}
		
		
		
		// ***********************************  
		function pintarDistritos(delegacion){
			nUltimaDelegacion=delegacion;
			document.all.btnRegresar.style.display="inline";
			var tbl = document.all.tablaElementos;
		
			$.ajax({
				type: 'GET', url: '/lstDistritosGeoByDelegacion/' + delegacion ,
				success: function(response) {
					var jsonData = JSON.parse(response);
					
					if(jsonData.datos.length==0){alert("No se encontraron distritos.");return;}
					activarPanel('');
					
					//Vacia la lista
					tbl.innerHTML="";
					
					//Eliminar los infos
					for(var i=0; i<lstInfos.length; i++) lstInfos[i].close();
					lstInfos = [];
						
					//Agregar renglones
					var renglon, columna;
					for (var i = 0; i < jsonData.datos.length; i++) {
						var dato = jsonData.datos[i];
						if (dato.latitud==null) dato.latitud="";
						if (dato.longitud==null) dato.longitud="";

						var sPin="";						
						if (dato.latitud!="" && dato.longitud!="") sPin = "  <span class='fa fa-map-marker'> ";
						
						renglon=document.createElement("TR");				
						renglon.innerHTML="<td onclick='recuperaDatosDistrito(" + dato.id + ");'>" + dato.id + ' ' + dato.texto + sPin  +  "</td><td>" + dato.habitantes + "</td><td>" + dato.estatus + "</td>";

						if (dato.latitud!="" && dato.longitud!=""){
							var sTituloInfo="<a href='javascript:pintarSecciones(" + dato.id + ");'><b>" + dato.texto + "</b></a>";
							pintarInfo(sTituloInfo, dato.latitud, dato.longitud);
						}						

						tbl.appendChild(renglon);
					}				
				},
				error: function(xhr, textStatus, error){
					alert("ERROR EN: function pintarDistritos()" );
				}			
			});	
		}
		
		
	
		// ***********************************  
		function pintarDelegaciones(entidad){			
			var tbl = document.all.tablaElementos;
			document.all.btnRegresar.style.display="none";
			
			$.ajax({
				type: 'GET', url: '/lstDelegacionesGeoByEntidad/' + entidad,
				success: function(response) {
					var jsonData = JSON.parse(response);
					
					if(jsonData.datos.length==0){alert("No se encontraron municipios / delegaciones.");return;}
					activarPanel('');
					
					//Vacia la lista
					tbl.innerHTML="";
					
					//Eliminar los infos
					for(var i=0; i<lstInfos.length; i++) lstInfos[i].close();
					lstInfos = [];
					
						
					//Agregar renglones
					var renglon, columna;
					for (var i = 0; i < jsonData.datos.length; i++) {
						var dato = jsonData.datos[i];
						if (dato.latitud==null) dato.latitud="";
						if (dato.longitud==null) dato.longitud="";

						var sPin="";						
						if (dato.latitud!="" && dato.longitud!="") sPin = "  <span class='fa fa-map-marker'> ";
						
						
						renglon=document.createElement("TR");				
						renglon.innerHTML="<td onclick='recuperaDatosDelegacion(" + dato.id + ");'>" + ' ' + dato.id + ' ' + dato.texto + sPin  + "</td><td>" + dato.habitantes + "</td><td>" + dato.estatus + "</td>";
						if (dato.latitud!="" && dato.longitud!=""){
							var sTituloInfo="<a href='javascript:pintarDistritos(" + dato.id + ");'><b>" + dato.texto + "</b></a>";
							pintarInfo(sTituloInfo, dato.latitud, dato.longitud);
						}						
						tbl.appendChild(renglon);
					}				
				},
				error: function(xhr, textStatus, error){
					alert("ERROR EN: function pintarDelegaciones" );
				}			
			});	
		}
		//**************************************
		function activarPanel(seccion){		
			if (seccion=="") {
				document.all.divListados.style.display="block";
				document.all.divEdicionDelegacion.style.display="none";
				document.all.divEdicionDistrito.style.display="none";
				document.all.divEdicionSeccion.style.display="none";
			}
			else{
				sPanelActivo=seccion;
				document.all.divListados.style.display="none";				
				if (sPanelActivo=="edicionDelegacion")document.all.divEdicionDelegacion.style.display="block";					
				if (sPanelActivo=="edicionDistrito")document.all.divEdicionDistrito.style.display="block";					
				if (sPanelActivo=="edicionSeccion")document.all.divEdicionSeccion.style.display="block";					
			}			
		}
	
		function recuperaDatosDelegacion(delegacion){
			$.ajax({
				type: 'GET', url: '/lstDelegacion/' + delegacion ,
				success: function(response) {
					var obj = JSON.parse(response);
					document.all.txtIdDelegacion.value='' + obj.id;
					document.all.txtNombreDel.value='' + obj.nombre;				
					document.all.txtCortoDel.value='' + obj.nombreCorto;
					document.all.txtHabitantesDel.value='' + obj.habitantes;
					if (obj.latitud==null) obj.latitud="";
					if (obj.longitud==null) obj.longitud="";
					
					document.all.txtLatitudDel.value='' + obj.latitud;
					document.all.txtLongitudDel.value='' + obj.longitud;	
					
				},
				error: function(xhr, textStatus, error){
					alert('statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error + ' SECCION: ' + seccoin);
				}			
			});
				activarPanel("edicionDelegacion");
		}
		
		function recuperaDatosDistrito(distrito){
			$.ajax({
				type: 'GET', url: '/lstDistrito/' + distrito ,
				success: function(response) {
					var obj = JSON.parse(response);
					document.all.txtIdDistrito.value='' + obj.id;
					document.all.txtNombreDis.value='' + obj.nombre;				
					document.all.txtHabitantesDis.value='' + obj.habitantes;

					if (obj.latitud==null) obj.latitud="";
					if (obj.longitud==null) obj.longitud="";
					
					document.all.txtLatitudDis.value='' + obj.latitud;
					document.all.txtLongitudDis.value='' + obj.longitud;					
				},
				error: function(xhr, textStatus, error){
					alert('statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error + ' SECCION: ' + seccoin);
				}			
			});
			activarPanel("edicionDistrito");
		}		
		
		function recuperaDatosSeccion(seccion){
			
			$.ajax({
				type: 'GET', url: '/lstSeccion/' + seccion ,
				success: function(response) {
					var obj = JSON.parse(response);
					eliminarPuntos();
					document.all.tblPuntos.style.display='none';			
			document.all.btnMarcar.style.display='inline';
			document.all.btnPoligono.style.display='none';
			document.all.btnLimpiar.style.display='none';
			document.all.btnGuardar.style.display='inline';
					
					document.all.txtIdSeccion.value='' + obj.id;
					document.all.txtHabitantesSec.value='' + obj.habitantes;				
					
					document.all.txtMetaTotal.value='' + obj.metaTotal;				
					document.all.txtMetaDiaria.value='' + obj.metaDiaria;				
					
					if (obj.latitud==null) obj.latitud="";
					if (obj.longitud==null) obj.longitud="";

					document.all.txtLatitudSec.value='' + obj.latitud;
					document.all.txtLongitudSec.value='' + obj.longitud;
					

					// Crear INFOWINDOWS
					
					//Eliminar los infos
						for(var i=0; i<lstInfos.length; i++) lstInfos[i].close();
						lstInfos = [];
					if (obj.latitud!="" && obj.longitud!=""){
						var pos1= new google.maps.LatLng(obj.latitud, obj.longitud);
						infoWindow = new google.maps.InfoWindow();
						infoWindow.setContent('<p style="text-align:center;"><b>Sección Electoral<br>' + obj.id + '</b></p>');
						infoWindow.setPosition(pos1);
						lstInfos.push(infoWindow);
						infoWindow.open(mapa);	
						mapa.setCenter(pos1);
						nZoom = nZoom+2;
						mapa.setZoom(nZoom);						
					}
					getPuntosBySeccion(obj.id);
				},
				error: function(xhr, textStatus, error){
					alert('statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error + ' SECCION: ' + seccoin);
				}			
			});
			activarPanel("edicionSeccion");
		}
		
		
		
		
		
		function crearPoligono(){
			
			bermudaTriangle = new google.maps.Polygon({
				paths: triangleCoords,
				strokeColor: '#000000',
				strokeOpacity: 0.8,
				strokeWeight: 1,
				fillColor: '#FF0000',fillOpacity: 0.15
			});
			//bermudaTriangle.setMap(null);
			bermudaTriangle.setMap(mapa);
		}
		
		function eliminarPuntos(){
			var tPuntos = document.getElementById("tablaPuntos");
			
			var nRenglon=0;
			
			// Limpia vector 
			while(triangleCoords.length){
				
				//Elimina posicion del vector
				triangleCoords.pop();
				//puntos.pop();
				
				// Alimina renglon de la tabla
				if(tPuntos.rows.length>=1) tPuntos.deleteRow(0);
			}			
			
			//Elimina poligono
			if(triangleCoords.length>0) bermudaTriangle.setMap(null);
			
			// Elimina Pines
			for (var i = 0; i < markers.length; i++) {markers[i].setMap(null);}
			markers = [];
			puntos=[];
			
			nPinActual=0;
			//document.all.btnLimpiar.style.display='none';
			//document.all.btnCrear.style.display='none';
			
		}
		
		
	function crearPin(posicion){
		// Crea Pin		
		var marker = new google.maps.Marker({position: posicion, map: mapa,title: ''});
		markers.push(marker);
	}

	
		function crearRenglon(id, lat, lon) {
			var tPuntos = document.getElementById("tablaPuntos");
			
			//alert("CREANDO ---> ID:" + id + " Lat: " + lat + " Lng: " + lon);
			
			var row = tPuntos.insertRow();
			var cell1 = row.insertCell();
			var cell2 = row.insertCell();
			var cell3 = row.insertCell();
			
			cell1.innerHTML = id;
			cell2.innerHTML = lat;
			cell3.innerHTML = lon;			
		}		
		
		
//***********************************************************************
		function getPuntosBySeccion(seccion){
				$.ajax({
					type: 'GET',
					url: '/puntosBySeccion/' + seccion ,
					success: function(response) {
						var jsonData = JSON.parse(response);
						puntos =[];
						triangleCoords=[];
						
						if(jsonData.datos.length>0) document.all.tblPuntos.style.display='inline';
						
						for (var i = 0; i < jsonData.datos.length; i++) {
							dato = jsonData.datos[i];
							var posMarker = new google.maps.LatLng(dato.latitud, dato.longitud);
							
							crearRenglon(dato.id, dato.latitud, dato.longitud);
							puntos.push(new Array(dato.idSeccion,dato.latitud, dato.longitud));
							triangleCoords.push(new google.maps.LatLng(dato.latitud, dato.longitud));
							crearPin(posMarker);
						}
						
						if(puntos.length>2) {
							crearPoligono();
							document.all.btnLimpiar.style.display='inline';
						}
					},
					error: function(xhr, textStatus, error){
						alert('statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error + ' SECCION: ' + seccoin);
					}			
				});
		}		
		
//*****************************************************************************
		function validarEnvioSeccion(){
			var d =document.all;
		
			if(d.txtIdSeccion.value==""){
				alert("El campo ID está vacío. No es posible guardar.");
				return false;
			}
			
			if(d.txtHabitantesSec.value==""){
				alert("El campo HABITANTES está vacío. No es posible guardar.");
				d.txtHabitantesSec.focus();
				return false;
			}
			
			if(d.txtMetaTotal.value==""){
				alert("El campo META TOTAL está vacío. No es posible guardar.");
				d.txtMetaTotal.focus();
				return false;
			}
			
			if(d.txtMetaDiaria.value==""){
				alert("El campo META DIARIA está vacío. No es posible guardar.");
				d.txtMetaDiaria.focus();
				return false;
			}
			
			//Concatena los campos
				d.txtValores.value=d.txtIdSeccion.value + '|' + d.txtHabitantesSec.value + '|' + d.txtMetaTotal.value + '|' + d.txtMetaDiaria.value + '|' + d.txtLatitudSec.value + '|' + d.txtLongitudSec.value + '*';
		
			//codifica los puntos
			//document.all.txtValores.value="";
			var separador ="";
			for(i=0; i< puntos.length; i++){			
				d.txtValores.value = d.txtValores.value + separador + puntos[i][0] + ':' + puntos[i][1] + ':' + puntos[i][2];
				separador ="|"
			}
			//alert("DATOS A ENVIAR: " + d.txtValores.value);
			
			return true;
		}
		
//*****************************************************************************
		function validarEnvioDistrito(){
			var d =document.all;
		
			if(d.txtIdDistrito.value==""){
				alert("El campo ID está vacío. No es posible guardar.");
				return false;
			}
			
			if(d.txtHabitantesDis.value==""){
				alert("El campo HABITANTES está vacío. No es posible guardar.");
				d.txtHabitantesSec.focus();
				return false;
			}
		
			//Concatena los campos
				d.txtValores.value=d.txtIdDistrito.value + '|' + d.txtNombreDis.value + '|' + d.txtHabitantesDis.value + '|' + d.txtLatitudDis.value + '|' + d.txtLongitudDis.value;	
			
			//alert("DATOS A ENVIAR del DISTRITO: " + d.txtValores.value);
			
			return true;
		}		
		
		
//*****************************************************************************
		function validarEnvioDelegacion(){
			var d =document.all;
		
			if(d.txtIdDelegacion.value==""){
				alert("El campo ID está vacío. No es posible guardar.");
				return false;
			}
			
			if(d.txtNombreDel.value==""){
				alert("El campo HABITANTES está vacío. No es posible guardar.");
				d.txtHabitantesSec.focus();
				return false;
			}
			
			if(d.txtCortoDel.value==""){
				alert("El campo HABITANTES está vacío. No es posible guardar.");
				d.txtHabitantesSec.focus();
				return false;
			}

			if(d.txtHabitantesDel.value==""){
				alert("El campo HABITANTES está vacío. No es posible guardar.");
				d.txtHabitantesSec.focus();
				return false;
			}			
		
			//Concatena los campos
				d.txtValores.value=d.txtIdDelegacion.value + '|' + d.txtNombreDel.value + '|' + d.txtCortoDel.value + '|' + d.txtHabitantesDel.value + '|' + d.txtLatitudDel.value + '|' + d.txtLongitudDel.value;	
			
			//alert("DATOS A ENVIAR dela DELEGACION: " + d.txtValores.value);
			
			return true;
		}			
		
		
		