<!DOCTYPE html>
<!-- saved from url=(0035)http://ashobiz.asia/mac52/macadmin/ -->
<html lang="es"><head>
<meta http-equiv="X-UA-Compatible" content="IE=Edge" />  
 <meta charset="UTF-8">
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.1/themes/base/jquery-ui.css" />
<script type="text/javascript" src="js/canvasjs.min.js"></script>
<script type='text/javascript' src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js?ver=3.1.2"></script> 
<script type="text/javascript" src="js/genericas.js"></script>
<script type="text/javascript" src="js/yo.js"></script>
<script type="text/javascript" src="js/moment.js"></script>	
<script src="https://maps.googleapis.com/maps/api/js?sensor=false&libraries=geometry,places&ext=.js"></script>
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>
<script src="ckeditor/ckeditor.js" charset="UTF-8"></script>
<link rel="stylesheet" type="text/css" href="css/jquery-ui-1.7.2.custom.css" />
<link rel="stylesheet" type="text/css" href="css/estilo.css" />

<script src="jquery.ui.datepicker-es.js"></script>

<style type="text/css">

	
	@media screen and (min-width: 768px) {
		#mapa_content {width:100%; height:150px;}
		#canvasMapa {width:100%; height:350px;}
		#canvasJD, #canvasDIP{height:175px; width:100%;}
		#canvasJG{height:175px; width:100%;}			
		#modalCronograma .modal-dialog  {width:70%;}
		#modalComentarios .modal-dialog  {width:70%;}		
		#modalConfronta .modal-dialog  {width:70%;}
		#modaltrabajofase .modal-dialog  {width:70%;}
		#modalEquipo .modal-dialog  {width:70%;}
		#modalDocto .modal-dialog  {width:60%;}
		#modalAutorizar .modal-dialog  {width:30%;}
		#modalLocalizacion .modal-dialog  {width:60%;}
		#modalCapturado{width: 100%;}
		#modalActividad{width: 100%}

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
	var controlObjetos = new Array();
	var IFAcache = "";

	

	function inicializar() {
		var opciones = {
			center: new google.maps.LatLng(19.4339249,-99.1428964),
			zoom:nZoom, panControl:false,zoomControl:true,mapTypeControl:false,scaleControl:false,streetViewControl:false,overviewMapControl:false,rotateControl:false,    
			mapTypeId: google.maps.MapTypeId.ROADMAP,mapTypeControlOptions: {mapTypeIds: [google.maps.MapTypeId.ROADMAP, 'map_style']}
			,styles: [
				{"stylers": [{"hue": "#2c3e50"},{"saturation": 250}]},
				{"featureType": "road","elementType": "geometry","stylers": [{"lightness": 50},{"visibility": "simplified"}]},
				{"featureType": "road","elementType": "labels","stylers": [{"visibility": "off"}]}
			]			
		};		
		mapa = new google.maps.Map(document.getElementById('canvasMapa'), opciones);
		mapa.setCenter(new google.maps.LatLng(19.4339249,-99.1428964));	
		
		// Agrega listener para mostrar latitud + longitud
		google.maps.event.addListener(mapa, 'click', 
			function(event) {				
				var posActual = event.latLng;
				var sTituloInfo = "Auditoría " + document.all.txtID.value;							
				var infoWindow = new google.maps.InfoWindow({});
					
					infoWindow.setContent(sTituloInfo);
					infoWindow.setPosition(posActual);
					infoWindow.open(mapa);							
					mapa.setCenter(posActual);												
						
					document.all.txtLatitud.value=posActual.lat();;
					document.all.txtLongitud.value=posActual.lng();
				}
			);	
	};
	
	//modificar
		function modificarfeconfro(){
			if (confirm("¿Esta seguro que desea Modificar la FECHA\n \n")){
				document.getElementById('FConfronta').readOnly = false;
				document.getElementById('btnGuardarConfronta').style.display='inline';
			}
		}

		function modificarfecha(fecha){
			document.all.btnGuardarObjeto.style.display='inline';
			
			if(fecha =='modirac'){
				if (confirm("¿Esta seguro que desea Modificar la FECHA IRAC\n \n")){
					document.getElementById('FIRA').readOnly = false;
					$('#FIRA').datepicker("option", "disabled", false);
					document.getElementById('modirac').style.display='none';
				}
			}else{
				if(fecha=='modifa'){
					if (confirm("¿Esta seguro que desea Modificar la FECHA IFA\n \n")){
						document.getElementById('FIFA').readOnly = false;
						$('#FIFA').datepicker("option", "disabled", false);
						document.getElementById('modifa').style.display='none';
					}
				}else{
					if(fecha=='modfain'){
						if (confirm("¿Esta seguro que desea Modificar la FECHA DE INICIO\n \n")){
							//document.getElementById('finicio').readOnly = false;
							$('#finicio').datepicker("option", "disabled", false);
							document.getElementById('modfain').style.display='none';
						}
					}else{
						if (confirm("¿Esta seguro que desea Modificar la FECHA FINAL\n \n")){
							//document.getElementById('finicio').readOnly = false;
							$('#fFin').datepicker("option", "disabled", false);
							document.getElementById('modfafi').style.display='none';
						}
					}

				}
				
			}
		}

		function modificardirsub(dirsub){
			if(dirsub=='modirec'){
				if (confirm("¿Esta seguro que desea Modificar la Dirección Auditora?\n \n")){
					document.getElementById('resp').disabled=false;
					document.all.btnGuardarObjeto.style.display='inline';
					document.getElementById('idmodirec').style.display='none';
				}
			}else{
				if (confirm("¿Esta seguro que desea Modificar la Subdirección Auditora?\n \n")){
					document.getElementById('arsubre').disabled=false;
					document.all.btnGuardarObjeto.style.display='inline';
					document.getElementById('idmodsub').style.display='none';
				}
			}
		}

		function cajaResultados(tipo){
			if(tipo=='' || tipo == 'NINGUNA'){
				document.all.divResumen.style.display='none';
				document.all.txtObserva.value = '';	
			}else{
				document.all.divResumen.style.display='inline';
				document.all.divResumen.value ="";			
			}
		}

		function editarCronograma(){
			$('#modalCronograma').removeClass("invisible");
			$('#modalCronograma').modal('toggle');
			$('#modalCronograma').modal('show');
		}

		function modificarcronograma(id,aud){

			$.ajax({
				type: 'GET', url: '/modifcronograma/' + id + '/' + aud,
				success: function(response) {			
					var obj = JSON.parse(response);
					document.all.txtOpera.value='UPD';
					limpiarCamposActividad();
					$("#modalCapturado").modal('show');
					
					seleccionarElemento(document.all.txtFaseActividad, obj.fase);
					//$('.txtFaseActividad').prop('readonly', true);
					document.all.txtFaseActividad.disabled	 = true;
					if(obj.act2==''){
						document.all.Complementaria.style.display='none';
					}else{
						document.all.Complementaria.style.display='inline';
						document.all.txtDescripcionActividadCom.value='' + obj.act2;
					}
					
					if(obj.inicio=='1900-01-01'){
						document.all.txtInicioActividad.value='';
						document.getElementById('modfain').style.display='none';
					}else{
						$('#finicio').datepicker("option", "disabled", true);
						document.getElementById('modfain').style.display='inline';
						document.all.txtInicioActividad.value='' + obj.inicio;
					}


					if(obj.inicio=='1900-01-01'){
						document.all.txtFinActividad.value='';
						document.getElementById('modfafi').style.display='none';
					}else{
						document.getElementById('modfafi').style.display='inline';
						document.all.txtFinActividad.value='' + obj.fin;
						$('#fFin').datepicker("option", "disabled", true);
					}

					document.all.txtDiasActividad.value='' + obj.defec;
				},
				error: function(xhr, textStatus, error){
					alert('statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
				}			
			});			
		}

		function modificarapartado(audi,apar){
			limpiarcamposapartado();
			//limpiar el editor 
			var editor = CKEDITOR.instances["DescripcionActividad"];
			if (editor) { editor.destroy(true); }
			
			$.ajax({
				type: 'GET', url: '/modifapartado/' + audi + '/' + apar ,
				success: function(response) {
					var obj = JSON.parse(response);
					document.all.txtOperacion.value='UPD';
					$('#modalActividad').modal('show');
					var aparta = obj.apartado;
					document.getElementById('Apartado2').value = aparta;
					document.getElementById('audito').value = obj.auditoria;
					document.all.txtApartado.readOnly=true;
					seleccionarElemento(document.all.txtApartado, obj.apartado);
					document.all.txtDescripcionActividad.value='' + obj.actividad;
					
					CKEDITOR.replace('DescripcionActividad',{
					    on: {
					        change: function( evt ) {
					            CKEDITOR.document.getBody();
					        }
					        
					    }
					});
				},
				error: function(xhr, textStatus, error){
					alert('statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error);
				}			
			});
		}
		
	// recuperar secciones	
	
		function recuperarTabla(lista, valor, tbl){
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
						if(dato.inicio=='1900-01-01'){
						var inicio = '';
						}else{
						var inicio = dato.inicio;
						}
						if(dato.fin=='1900-01-01'){
						var fin = '';
						}else{
						var fin = dato.fin;
						}
						renglon=document.createElement("TR");			
						renglon.innerHTML="<td onclick='modificarcronograma("+dato.idA+","+dato.audi+");'>" + dato.fase + "</td><td onclick='modificarcronograma("+dato.idA+","+dato.audi+");'>" + inicio + "</td><td onclick='modificarcronograma("+dato.idA+","+dato.audi+");'>" + fin  + "</td><td onclick='modificarcronograma("+dato.idA+","+dato.audi+");'>" + dato.efectivos  + "</td>";
						tbl.appendChild(renglon);					
					}				
				},
				error: function(xhr, textStatus, error){
					alert('function recuperarTabla ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
				}			
			});	
		}

		function recuperarApartado(lista, valor, tbl){
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
						var apar = 	dato.id;
						var sOperaciones="<button type='button' value="+apar+" onclick='modificarapartado("+dato.auditoria+",this.value);'><i class='fa fa-pencil-square-o'></i></button>";
						renglon=document.createElement("TR");			
						renglon.innerHTML="<td>"+ sOperaciones +"</td><td>" + dato.apartado + "</td><td>" + dato.actividad + "</td>";
						tbl.appendChild(renglon);					
					}				
				},
				error: function(xhr, textStatus, error){
					alert('function recuperarTabla ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
				}			
			});	
		}

		function recupeAuditores(lista, valor, tbl){
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
						renglon.innerHTML="<td><input type='checkbox' name='' "+sTatus+" onclick='asignarAuditor("+ dato.idEmpleado +", this.checked);'/></td><td>" + dato.auditor + "</td><td>" + dato.puesto + "</td><td><input type='radio' name='selec' "+Slider+" onclick='asignarLider("+ dato.idEmpleado +", this.checked);'/></td>";

						tbl.appendChild(renglon);					
					}
				},
				error: function(xhr, textStatus, error){
					alert('function recupeAuditores ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
				}			
			});	
		}

	//asignar
		//Lider
		function asignarLider(empleado, activo){
			var bEncontrado=false;
			for( i = 0; i < lstEmpleados.length; i++)
				{
					lstEmpleados[i][1]="";
				}
			for( i = 0; i < lstEmpleados.length; i++)
				{
					if(empleado==lstEmpleados[i][0]){
						lstEmpleados[i][1]="SI";
						bEncontrado=true;
					}
				}
			if(bEncontrado==false){ 
				alert("Para ser LIDER, ésta persona de ser incluida al equipo de trabajo");
				activo=false;
				bEncontrado=false;
			}
		}

		//checkbox
		function asignarAuditor(empleado, activo){
			if (activo==true){
				lstEmpleados.push(new Array(empleado,""));
			}else{
				for( i = 0; i < lstEmpleados.length; i++)
				{
					if(empleado==lstEmpleados[i][0]){
						lstEmpleados.splice(i, 1);
					}
				}
			}
		}

	//recuperar
		function recuperarTablaComentario(lista, valor, tbl){
    		var seccion = 'PGA';
    		
	        $.ajax({ type: 'GET', url: '/'+ lista + '/' + valor + '/' + seccion ,
	           	success: function(response) {
		            var jsonData = JSON.parse(response);                                 
		                                  
		            //Vacia la lista
		            tbl.innerHTML="";

		            //Agregar renglones
		            var renglon, columna;
		                   
					for (var i = 0; i < jsonData.datos.length; i++) {
						var dato = jsonData.datos[i];                                                                    
						renglon=document.createElement("TR");     
						renglon.innerHTML="<td>" + dato.nombre + "</td><td>" + dato.fechayhora + "</td><td>" + dato.usuario + "</td><td>" + dato.comentario + "</td>";
						renglon.onclick= function() 
					  	{

					  	};
						  	tbl.appendChild(renglon);                                                                       
					}                                                             
	           	},
	           	error: function(xhr, textStatus, error){
	                alert('function recuperarTablaComentarios ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
	           }                                             
			});           
	    }

		function recupeLider(lista, valor, tbl){
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
						renglon.innerHTML="<td>" + dato.lider + "</td><td>" + dato.texto + "</td><td>" + dato.puesto + "</td>";
						tbl.appendChild(renglon);					
					}
			},
			error: function(xhr, textStatus, error){
				alert('function recupeLider ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
			}			
			});	
		}	

		function recupeFases(lista, valor, tbl){
			
			$.ajax({
				type: 'GET', url: '/'+ lista + '/' + valor ,
				success: function(response) {
					var jsonData = JSON.parse(response);
					if(jsonData.datos.length==0){
						document.all.confron.style.display='none';
						document.all.btnConfronta.style.display='none';
					}
					//Vacia la lista
					for(var j = 0;j < jsonData.datos.length; j++){
						var dato = jsonData.datos[j];
						if(dato.id == 'EJECUCION'){
							document.all.confron.style.display='inline';
							document.all.btnConfronta.style.display='inline';
							break;
						}else{
							document.all.confron.style.display='none';
							document.all.btnConfronta.style.display='none';
						}
					}

					tbl.innerHTML="";

					var renglon, columna, type;
					
					for (var i = 0; i < jsonData.datos.length; i++) {
						var dato = jsonData.datos[i];

					if(dato.desde == '1900-01-01'){
						var desde = '';
					}else{
						var desde = dato.desde;
					}

					if(dato.hasta == '1900-01-01'){
						var hasta = '';
					}else{
						var hasta = dato.hasta;
					}

					if (dato.desde == '1900-01-01' && dato.hasta == '1900-01-01'){
						var dia = 0 ; 
					}else{
						var dia = dato.dia;
					}						
						
						renglon=document.createElement("TR");			
						renglon.innerHTML="<td>" + dato.texto + "</td><td>" + desde + "</td><td>" + hasta + "</td><td>" + dia + "</td>";
							
						renglon.onclick= function() {
							document.all.txtOperacion.value='UPD';



						};
						tbl.appendChild(renglon);					
					}				
				},
				error: function(xhr, textStatus, error){
					alert('function recupeFases ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
				}			
			});	
		}

		function RecuperaConfronta(lista, valor, tbl){
			$.ajax({
				type: 'GET', url: '/'+ lista + '/' + valor ,
				success: function(response) {
					var jsonData = JSON.parse(response);
					//Vacia la lista
					tbl.innerHTML="";
					var renglon, columna, type;
					for (var i = 0; i < jsonData.datos.length; i++) {
						var dato = jsonData.datos[i];	
						var valr = dato.val
						renglon=document.createElement("TR");			
						renglon.innerHTML="<td onclick='llenarConfronta2("+dato.audi+",\""+valr+"\");' style='width:95% !important;'>" + dato.nombre + "</td><td onclick='llenarConfronta2("+dato.audi+",\""+valr+"\");' style='width:20% !important;'>" + dato.fecha + "</td><td onclick='llenarConfronta2("+dato.audi+",\""+valr+"\");'>" + dato.hcon + "</td><td onclick='llenarConfronta2("+dato.audi+",\""+valr+"\");' style='width:20% !important;'>" + dato.irac + "</td><td onclick='llenarConfronta2("+dato.audi+",\""+valr+"\");' style='width:20% !important;'>" + dato.ifa + "</td>";
						tbl.appendChild(renglon);					
						}
				},
				error: function(xhr, textStatus, error){
					alert('function RecuperaConfronta() ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
				}			
			});	
		}	

		function recuperarTablaArchivos(lista, valores, tbl){
	    	$.ajax({
		        type: 'GET', url: '/'+ lista + '/' + valores,
		        success: function(response) {
		        var jsonData = JSON.parse(response);                                 
		        tbl.innerHTML="";
		        var renglon, columna;
				for (var i = 0; i < jsonData.datos.length; i++) {
		        	var dato = jsonData.datos[i];                                                                    
		            renglon=document.createElement("TR");     
		            renglon.innerHTML="<td>" + dato.numeroDocto + "</td><td>" + dato.tipo + "</td><td>" + dato.flujodocto + "</td><td>" + dato.fDocto + "</td><td>" + dato.idPrioridad + "</td><td>" + dato.idImpacto + "</td><td><a href='/documentos/" + dato.archivoFinal + "'><img src='img/xls.gif'></td>";
		            tbl.appendChild(renglon);                                                                       
		        }                                                             
		    },
		    error: function(xhr, textStatus, error){
		    alert('function recuperarTablaArchivos ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
		    }                                             
			});           
	    }

	    function recupepapeles(lista, valor, tbl){
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
						renglon.innerHTML="<td>" + dato.claveAuditoria + "</td><td>" + dato.papel + "</td><td>" + dato.sPapel + "</td><td>" + dato.fase + "</td><td>" + dato.fecha + "</td><td>" + dato.tresultado + "</td><td>" + dato.resul + "</td><td>" + dato.estatus + "</td><td><a href='/papeles/"+ dato.aerchivo +"'>" + "<img src='img/xls.gif'>" + "</a></td>";
						
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

		function recupeAvances(lista, valor, tbl){
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
						renglon.innerHTML="<td>" + dato.claveAuditoria + "</td><td>" + dato.sujeto + "</td><td>" + dato.tipoAuditoria + "</td><td>" + dato.id + "</td><td>" + dato.fase + "</td><td>" + dato.actividad + "</td><td>" + dato.porcen + "</td>";

						renglon.onclick= function() {document.all.txtOperacion.value='UPD';};
						tbl.appendChild(renglon);					
					}				
				},
				error: function(xhr, textStatus, error){
					alert('function recupeAvances ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
				}			
			});	
		}

		function recuperalistaAuditores(lista, valor, tbl){
			$.ajax({
				type: 'GET', url: '/'+ lista ,
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
						renglon.innerHTML="<td>" + "SUMA" + "</td><td>" + dato.total + "</td>";
							
						renglon.onclick= function() {document.all.txtOperacion.value='UPD';};
						tbl.appendChild(renglon);					
					}				
				},
				error: function(xhr, textStatus, error){
					alert('function recuperalistaAuditores ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
				}			
			});	
		}

		function recuperarTablaFase(lista, valor, tbl){
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
						tbl.appendChild(renglon);					
					}				
				},
				error: function(xhr, textStatus, error){
					alert('function recuperarTablaFase ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
				}			
			});	
		}

		function recuperaAuditoria(id){
			$.ajax({
				type: 'GET', url: '/lstAuditoriaByID/' + id ,
				success: function(response) {
					var obj = JSON.parse(response);
					var respon = obj.responsable;
					var sunres = obj.subresponsable;
					
					if(respon == ''){
						document.getElementById('resp').disabled = false;
						document.all.btnConfronta.style.display='none';
						document.all.btnCronograma.style.display='none';
						document.all.btnApartado.style.display='none';
						document.all.btnEquipo.style.display='none';
						document.all.btnGuardarObjeto.style.display='none';
						document.getElementById('menudesple').style.display='none';
						document.getElementById('idmodirec').style.display='none';
					}else{
						document.all.btnConfronta.style.display='inline';
						document.all.btnCronograma.style.display='inline';
						document.all.btnApartado.style.display='inline';
						document.all.btnEquipo.style.display='inline';
						document.all.btnGuardarObjeto.style.display='none';
						document.getElementById('resp').disabled = true;
						document.getElementById('menudesple').style.display='inline';
						document.getElementById('idmodirec').style.display='inline';
					}

					if(sunres == ''){
						document.getElementById('arsubre').disabled = false;
						document.all.btnGuardarObjeto.style.display='inline';
						document.getElementById('idmodsub').style.display='none';
					}else{
						document.all.btnGuardarObjeto.style.display='none';
						document.getElementById('idmodsub').style.display='inline';
						document.getElementById('arsubre').disabled = true;	
					}

					//valida si existe fases completas, si es verdadero oculta el boton de fechas fases
					valifases(obj.auditoria);

					//valida si esta completa las fases para mostrar el boton de Reporte
					valiRP(obj.auditoria);

					validaPEA(obj.auditoria);

					limpiarCamposAuditoria();
					$('#FIRA').datepicker("option", "disabled", true);
					$('#FIFA').datepicker("option", "disabled", true);
					$('#FAInicio').datepicker("option", "disabled", true);
					$('#FAFinal').datepicker("option", "disabled", true);
					document.getElementById('modifa').style.display='inline';
					document.getElementById('modirac').style.display='inline';
					document.all.txtCuenta.value='' + obj.cuenta;
					document.all.txtPrograma.value='' + obj.programa;
					document.all.txtAuditoria.value='' + obj.auditoria;
					document.all.txtclaveAuditoria.value='' + obj.clave;
					
					if(obj.responsable!=''){seleccionarElemento(document.all.txtResponsable, obj.responsable);recuperarListaLigada('lstAreasSubResponsablesByResponsable', obj.responsable, document.all.txtAreaSubresponsable);recuperarListaSelected('lstAreasResponsablesByID', obj.responsable, document.all.txtAreaSubresponsable, obj.subresponsable);}
					
					seleccionarElemento(document.all.txtTipoAuditoria, obj.tipo);
					llenarArregloConObjetos(obj.auditoria, document.all.txtObjeto);
					llenarArregloConUnidades(obj.auditoria, document.all.txtSujeto);
					llenarArregloConUnidades2(obj.auditoria, document.all.txtUnidad);
					document.all.txtObjetivo.value='' + obj.objetivo;
					document.all.txtAlcance.value='' + obj.alcance;	
					document.all.txtJustificacion.value='' + obj.justificacion;
					document.all.txtFechaInicio.value='' + obj.feInicio;
					document.all.txtFechaFin.value='' + obj.feFin;
					
					if(txtTipoObs=obj.tipoObse){seleccionarElemento(document.all.txtTipoObs, obj.tipoObse);cajaResultados(obj.tipoObse);}else{document.all.txtTipoObs.selectedIndex=0;document.all.divResumen.style.display='none';}

					if(txtObserva=obj.observacion){document.all.txtObserva.value='' + obj.observacion;}else{document.all.txtObserva.value='';}
					
					if(obj.ira==''){document.getElementById('modirac').style.display='none';}else{document.getElementById('modirac').style.display='inline';}

					if(obj.ifa==''){document.getElementById('modifa').style.display='none';}else{document.getElementById('modifa').style.display='inline';
					}

					document.all.txtObjetivo.value='' + obj.objetivo;
					recuperarTabla('tblActividadesByAuditoria', obj.auditoria, document.all.tablaActividades);
					document.all.listasAuditoria.style.display='none';
					document.all.capturaAuditoria.style.display='inline';
					document.all.txtFIRA.value='' + obj.ira;
					document.all.txtFIFA.value='' + obj.ifa;
					obj.ifa = IFAcache;
					recupeLider('auditoriasByLider', obj.auditoria, document.all.tablaEquipo);
					recupeAuditores('auditoriasBYauditores', obj.auditoria, document.all.tablaAuditores);
					recuperarLista('lstFasesActividad', document.all.txtFaseActividad);
					recuperarLista('lstApartados', document.all.txtApartado);
					recuperarListaLigada('lstResponByauditor', obj.auditoria, document.all.txtResponsableActividad);
					recupeFases('lstauditoriaByFases', obj.auditoria, document.all.tablaFases);
					RecuperaConfronta('tlbaudiuni', obj.auditoria, document.all.tablaConfronta);				
					recupepapeles('lstpapeles', obj.auditoria, document.all.tablaPapeles);
					recuperarTablaArchivos('tblArchivosByAuditoria', obj.auditoria, document.all.tblListaArchivos);
					recupeAvances('lstActividades', obj.auditoria, document.all.tablaAvances);
					proximaEtapa(obj.proceso, obj.etapa);



			},
				error: function(xhr, textStatus, error){
					alert('function recuperaAuditoria()   -> statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error + ' Auditoría: ' + id);
				}			
			});		
		}

	//llenado arreglo
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
	                 	
	                 	controlObjetos.push(new Array(dato.id, dato.texto, dato.nivel, dato.valor, dato.tipoObjeto) );
						option.value = dato.id;
						option.text = dato.texto;
						cmb.add(option, i+1);

	  			 	}
	            }
			 });
		}

		function llenarArregloConUnidades(auditoria, cmb){
			liga = '/lstAudiUnida/' +  auditoria;
			$.ajax({ type: 'GET', url: liga ,
	          	success: function(response) {
	            	var jsonData = JSON.parse(response);                                 
	            	while (cmb.length>=1){
							cmb.remove(cmb.length-1);
	               	}
			    for (var i = 0; i < jsonData.datos.length; i++) {
	                 	var dato = jsonData.datos[i];                                                                    
	       				var option = document.createElement("option");
	                 	controlObjetos.push(new Array(dato.id, dato.texto));
						option.value = dato.id;
						option.text = dato.texto;
						cmb.add(option, i+1);
	  			 	}
	        	}
			});
		}

		function llenarArregloConUnidades2(auditoria, cmb){
			liga = '/lstAudiUnida/' +  auditoria;
			$.ajax({ type: 'GET', url: liga ,
	          	success: function(response) {
	            	var jsonData = JSON.parse(response);                                 
	               
					while (cmb.length>1){
							cmb.remove(cmb.length-1);
	               	}

	               for (var i = 0; i < jsonData.datos.length; i++) {
	                 	var dato = jsonData.datos[i];                                                                    
	       				var option = document.createElement("option");
	                 	
	                 	controlObjetos.push(new Array(dato.id, dato.texto));
	                 	//option.selectedIndex = i[1];
						option.value = dato.id;
						option.text = dato.texto;
						cmb.add(option, i+1);
	  			 	}
	  			}
			});
		}

		function llenarConfronta(audi,valor){
			if(valor==''){
				document.getElementById('btnGuardarConfronta').style.display='none';
				document.all.modconfro.style.display='none';
				document.all.txtFConfronta.value='';
				document.all.txtFNotifica.value='';
				document.all.txtFIfa.value='';
				document.all.txtHConfronta.value='';
			}else{		
				$.ajax({ 
					type: 'GET', url: '/lstAudiUnidades/' +  audi +'/' + valor,
		          	success: function(response) {
		          		var obj = JSON.parse(response);
		          		document.all.modconfro.style.display='inline';
		            	document.all.txtFConfronta.value='' +obj.FeCon;
		            	document.all.txtFNotifica.value='' + obj.irac;
		            	document.all.txtFIfa.value='' + obj.ifa;
		            	document.all.txtHConfronta.value=obj.hcon;
		            	if(obj.hcon == ''){
		            		document.getElementById('btnGuardarConfronta').style.display='inline';
		            	}else{
		            		document.getElementById('btnGuardarConfronta').style.display='none';
		            	}
					},
						error: function(xhr, textStatus, error){
							alert('function llenarConfronta()   -> statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error + ' Auditoría: ' + audi+ ' ' + valor);
						}
		    	});
			}
		}

		function llenarConfronta2(audi,valor){
			$.ajax({
				type: 'GET', url: '/lstAudiUnidades/' +  audi +'/' + valor,
	          	success: function(response) {
	            	var obj = JSON.parse(response);
	      			$('#modalConfronta').modal('show');
	        		document.all.txtUnidad.value='' +obj.ssuu;
	            	document.all.txtFConfronta.value='' +obj.FeCon;
	            	document.all.modconfro.style.display='inline';
	            	document.all.txtFNotifica.value='' + obj.irac;
	            	document.all.txtFIfa.value='' + obj.ifa;
	            	document.all.txtHConfronta.value=obj.hcon;
	            },
		  			error: function(xhr, textStatus, error){
						alert('function llenarConfronta2()   -> statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error + ' Auditoría: ' + audi+ ' ' + valor);
					}
	    	});
		}

		function botonactu(){
			document.all.btnGuardarObjeto.style.display='inline';
		}

	//Limpiar campos
		
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
			document.all.txtResponsable.selectedIndex = 0;	
			document.all.txtSujeto.selectedIndex=0;
			document.all.txtObjeto.selectedIndex=0;		
			document.all.txtObjetivo.value='';
			document.all.txtTipoObs.selectedIndex=0;
		}

		function limpiarCon(){
			document.all.txtUnidad.selectedIndex=0;
			document.all.txtFConfronta.value='';
			document.all.txtHConfronta.value='';
			document.all.txtFNotifica.value='';
			document.all.txtFIfa.value='';
		}
		
		function limpiarcamposapartado(){
			document.all.txtApartado.selectedIndex=0;
			document.all.txtDescripcionActividad.value='';
		}

		function limpiarCamposActividad(){
			document.all.txtDescripcionActividad.value='';
			document.all.txtFaseActividad.selectedIndex=0;		
			document.all.txtDiasActividad.value='';
			document.all.txtInicioActividad.value='';
			document.all.txtFinActividad.value='';			
		}

	//varios
		function calporcentaje(){
			
			var sValor="";
			var d = document.all;

			sValor = d.txtAuditoria.value + '|' + d.txtFaseActividad.value + '|' + d.txtDiasActividad.value;

			$.ajax({
				type: 'GET', url: '/porecentaje/' + sValor,
				success: function(response) {
					
					if(response=='false'){
						
						unico="100";
						document.all.txtPorcentajeActividad.value="" + unico;
					}else{
						var obj = JSON.parse(response);

						document.all.txtPorcentajeActividad.value='' + obj.total;	
					}
		
				},
				error: function(xhr, textStatus, error){
					alert('calporcentaje: statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
				}			
			});			
		}	

		var ventana;
		var nCampana='<?php echo $_SESSION["idCuentaActual"];?>';
		
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

		function addWine() {
			console.log('addWine');
			$.ajax({
				type: 'POST',
				contentType: 'application/json',
				url: rootURL,
				dataType: "json",
				data: formToJSON(),
				success: function(data, textStatus, jqXHR){
					$('#btnDelete').show();
					$('#wineId').val(data.id);
				},
				error: function(jqXHR, textStatus, errorThrown){
					alert('addWine error: ' + textStatus);
				}
			});
		}

		function formatDate(value){
			return value.getDate()+1  + "-" + value.getMonth() + "-" +  value.getYear();
		}

		function formatodiames(value){
			return (value.getDate()+1)  + "-" + (value.getMonth()+1);
		}

		function formatDate2(value){
			var mes = value.getMonth() + 1;
			return value.getDate()+1  + "-" + mes + "-" +  value.getFullYear();
		}

		String.prototype.replaceAll = function(str1, str2, ignore){
			return this.replace(new RegExp(str1.replace(/([\/\,\!\\\^\$\{\}\[\]\(\)\.\*\+\?\|\<\>\-\&])/g,"\\$&"),(ignore?"gi":"g")),(typeof(str2)=="string")?str2.replace(/\$/g,"$$$$"):str2);
		}	

		function getDiasLaborale(fInicio, fFin){
			var sCadena="FECHA INICIO:" + fInicio + "         FECHA FIN: " + fFin + "\n****************************************";
			var lstDiasHabiles=[];
			var days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
		
			var f1 = new Date(fInicio);
			f1.setDate(f1.getDate() + 1);	
			

			var f1Ori = new Date(fInicio);	
			f1Ori.setDate(f1Ori.getDate() + 1);		 // Agregar 1 dia
			
			var f2 = new Date(fFin);
			f2.setDate(f2.getDate() + 1);
					
			while (f1<=f2){											
				//Si es L-V, agregarlo
				if (f1.getDay()!=0 && f1.getDay()!=6) {
				lstDiasHabiles.push( formatDate(f1));			
				} else {
					sCadena = sCadena + "\n\n\tDia inhabil:" + f1 ; 
				}		
				f1.setDate(f1.getDate() + 1);		 // Agregar 1 dia
			}
			//f1Ori.setDate(f1Ori.getDate() - 1);

			//f2.setDate(f2.getDate() - 1);
			var cadena = '/lstInhabilesByRango1/' + f1Ori.toISOString().substr(0, 10) + '/' + f2.toISOString().substr(0, 10);
				$.ajax({
					type: 'GET', url: cadena,
					success: function(response) {
						var jsonData = JSON.parse(response);
						
						for (var i = 0; i < jsonData.datos.length; i++) {
							rango = jsonData.datos[i];				
							var fTmp1 = new Date(rango.fInicio);
							fTmp1.setDate(fTmp1.getDate() + 1);	
							
							var fTmp2 = new Date(rango.fFin);
							fTmp2.setDate(fTmp2.getDate() + 1);	
									
							//Crea las fechas
							while (fTmp1<=fTmp2){		
								//Esta nueva fecha buscarla en vector												
								for(var i=0; i < lstDiasHabiles.length; i++){		
									// Si la encuentra, la elimina del vectos
									if(lstDiasHabiles[i]==formatDate(fTmp1)){ 
										lstDiasHabiles.splice(i,1);
										sCadena = sCadena + "\n\n\tDia Feriado:" + fTmp1 ; 								
										break;
									}						
								}
								fTmp1.setDate(fTmp1.getDate() + 1);		 // Agregar 1 dia
							}
						}
						
						//alert(fInicio + "  -> " + fFin  + " = " + lstDiasHabiles.length + " Dias efectivos");
						sCadena = sCadena + "\n******************************\nDIAS EFECTIVOS EN TOTAL:" + lstDiasHabiles.length ; 

						dias = lstDiasHabiles.length;
						document.all.txtDiasActividad.value='' + dias;
						//alert(" --:  " + sCadena + " -- ");
					},
					error: function(xhr, textStatus, error){
						alert('ERROR: En function  getDiasLaborale(lista, mapa)  ->  statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
					}			
				});		
			return lstDiasHabiles.length;	
		}

		function sumarDias(fecha, dias){
			
 			fecha.setDate(fecha.getDate() + dias);
  			return fecha;
		}

		function getDias(fin, dias){
			var total;
			var fines;
			var finha;
			var f3 = new Date(fin);
			var f1 = new Date(fin);
			f1.setDate(f1.getDate() + 1);
			var fe1 = new Date(fin);
			fe1.setDate(fe1.getDate() + 1);
			var sCadena="FECHA FIN: " + fin + "\n****************************************";
			var lstfines=[];
			var arrprue = new Array();
			var arrecon = new Array();
			var days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];

			if(dias <= 0){
				var firac = sumarDias(f3,dias);
				while (firac<=f1){
					//Si es L-V, agregarlo
					if (firac.getDay()!=0 && firac.getDay()!=6) {
						lstfines.push( formatDate2(firac));			
					} else {
						sCadena = sCadena + "\n\n\tDia inhabil:" + firac ;
						arrprue.push(sCadena); 
					}		
					firac.setDate(firac.getDate() + 1);		 // Agregar 1 dia
				}

				fe1.setDate(fe1.getDate() - 1);	
				var cadena = '/lstInhabilesByRango1/' + firac.toISOString().substr(0, 10) + '/' + fe1.toISOString().substr(0, 10);
			}else{
				var feifa = sumarDias(f3,dias);
				while (f1<=feifa){											
					//Si es L-V, agregarlo
					if (f1.getDay()!=0 && f1.getDay()!=6) {
						lstfines.push( formatDate2(f1));			
					} else {
						sCadena = sCadena + "\n\n\tDia inhabil:" + f1 ;
						arrprue.push(sCadena); 
					}		
					f1.setDate(f1.getDate() + 1);		 // Agregar 1 dia

				}
				//fe1.setDate(fe1.getDate() - 1);
				var cadena = '/lstInhabilesByRango1/' + fe1.toISOString().substr(0, 10) + '/' + feifa.toISOString().substr(0, 10);	
			}

			$.ajax({
				type: 'GET', url: cadena,
				success: function(response) {
					async:false;
					var jsonData = JSON.parse(response);
					for (var i = 0; i < jsonData.datos.length; i++) {
						rango = jsonData.datos[i];
						
						var fTmp1 = new Date(rango.fInicio);
						fTmp1.setDate(fTmp1.getDate() + 1);	

						var fTmp2 = new Date(rango.fFin);
						fTmp2.setDate(fTmp2.getDate() + 1);	
						//Crea las fechas
						while (fTmp1<=fTmp2){		
							for(var i=0; i < lstfines.length; i++){
								// Si la encuentra, la elimina del vectos
								fTmp1.setDate(fTmp1.getDate());
								if(lstfines[i]==formatDate2(fTmp1)){
									arrecon.push(sCadena);
									lstfines.splice(i,1);
									sCadena = sCadena + "\n\n\tDia Feriado:" + fTmp1;
									console.log(sCadena);
									break;
								}						
							}
							fTmp1.setDate(fTmp1.getDate() + 1);		 // Agregar 1 dia
						}
					}
					sCadena = sCadena + "\n******************************\nDIAS EFECTIVOS EN TOTAL:" + lstfines.length ; 
					
				},
				error: function(xhr, textStatus, error){
					alert('ERROR: En function  getDias()  ->  statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
				}			
			});
		

			fines = arrprue.length;

			finha = arrecon.length;
			total = (parseInt(fines) + parseInt(arrecon.length));
			
			console.log(total);
			return total;
		}

		window.onload = function () {
			var chart1; 
			setGrafica(chart1, "dpsAuditoriasByArea", "pie", "Auditorias", "canvasJG" );
			inicializar();
		};

		function dessub(){
			document.getElementById('arsubre').disabled=false;
			document.all.btnGuardarObjeto.style.display='inline';
			document.getElementById('idmodsub').style.display='none';
		}

	//Guardar campos
		function guardaConfron(){
			var sValor="";
			var sOper=document.all.txtOperacionObjeto.value;
			var d = document.all;
			var unidad = document.all.txtUnidad.value;
			var sector = unidad.substring(2, 0);
			var subsector = unidad.substring(2, 4);
			var uni = unidad.substring(4,6);
			sValor = d.txtAuditoria.value + '|' + sector + '|' + subsector + '|' + uni + '|' + d.txtFConfronta.value + '|' + d.txtHConfronta.value;
			//alert("cadena:" + sValor);
			$.ajax({
				type: 'GET', url: '/guardar/confron/' + sValor,
				success: function(response) {
					RecuperaConfronta('tlbaudiuni',document.all.txtAuditoria.value, document.all.tablaConfronta);
					alert("Los datos se guardaron correctamente");
					$('#modalConfronta').modal('hide');
				return true;
				},
				error: function(xhr, textStatus, error){
					alert(' Error en function guardaConfron()\n\tstatusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error: ' + error );
					return false;
				}			
			});
		}
		
		function guardarAudi(){
			var sValor="";
			var sOper=document.all.txtOperacionObjeto.value;
			var d = document.all;



			 

			var tipoobser = document.getElementById('TipoObs').value;
			if(tipoobser == ''){
				var tipobser = document.all.txtTipoObs.value;
				tipobser = '';
			}else{
				tipobser = document.all.txtTipoObs.value;
			}

			var textobserva = document.getElementById('TipoObs').value
			if(textobserva == '' || textobserva == 'NINGUNA'){
				var textoobser = document.all.txtObserva.value;
				textoobser = '';
			}else{
				textoobser = document.all.txtObserva.value;
			}


			var inicio = document.getElementById('FAInicio').value;
			var final  = document.getElementById('FAFinal').value;
			var IRAC   = document.getElementById('FIRA').value;
			var IFA    = document.getElementById('FIFA').value;
			var fain;
			var fafi;
			var fra;
			var ffa;

			//alert("inicio:  " + inicio + " final: " + final + " IRAC: " + IRAC + " IFA: " + IFA);

			if(inicio == ''){fain = null;}else{fain = inicio;}

			if(final == ''){fafi =  null;}else{fafi =  final;}

			if(IRAC == ''){fra = null;}else{fra = IRAC;}

			if(IFA == ''){ffa = null;}else{ffa = IFA;}

		
			sValor = d.txtAuditoria.value + '|' + fra + '|' +  ffa + '|' + tipobser + '|' +  textoobser + '|' + fain + '|' +  fafi + '|' + d.txtResponsable.value + '|' + d.txtAreaSubresponsable.value;

			//alert(sValor);
			$.ajax({
				type: 'GET', url: '/guardar/auditoria/Notas/Place/' + sValor,
				success: function(response) {
					document.all.btnConfronta.style.display='inline';
					document.all.btnCronograma.style.display='inline';
					document.all.btnApartado.style.display='inline';
					document.all.btnEquipo.style.display='inline';
					document.getElementById('menudesple').style.display='inline';				
					alert("Los datos se guardaron correctamente");
					document.all.btnGuardarObjeto.style.display='none';
					document.getElementById('idmodsub').style.display='inline';
					document.getElementById('idmodirec').style.display='inline';
					document.getElementById('arsubre').disabled = true;
					document.getElementById('resp').disabled = true;
					$('#FIRA').datepicker("option", "disabled", true);
					$('#FIFA').datepicker("option", "disabled", true);

					var modifa = document.getElementById('modifa').value;
					if(modifa == ''){document.getElementById('modifa').style.display='none';}else{document.getElementById('modifa').style.display='inline';}

					var modirac = document.getElementById('modirac').value;
					if(modirac == ''){document.getElementById('modirac').style.display='none';}else{document.getElementById('modirac').style.display='inline';}

					return true;
				},
				error: function(xhr, textStatus, error){
					alert(' Error en function guardaraudi()\n\tstatusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error: ' + error );
					return false;
				}			
			});
		}

		function guardarFecha(){
			var sValor="";
			var sOper=document.all.txtOpera.value;
			var d = document.all;
			var dias1 = 11;
			var dias2 = -10;
			var feIFA;
			var FIFA = document.all.txtFIFA.value;
			var FIRAC = document.all.txtFIRA.value;
			var fin = document.all.txtFinActividad.value
			var feIFA = getDias(fin, dias1);
			var sum = ((feIFA + dias1)+2);
			var f3 = new Date(fin);
			var final = sumarDias(f3,sum);
			var proceso = 'PEA';
			var etapa = 'ELABORADA';

			if(final.getDay()!=0 && final.getDay()!=6){var final2 = sumarDias(final,3);FIFA = formatDate2(final2);}
			else{var fifecha = sumarDias(final,3);FIFA = formatDate2(fifecha);}
			
			var feIRAC = getDias(fin,dias2);
			var sumirac = ((feIRAC + 11));
			var f4 = new Date(fin);
			var finirac = sumarDias(f4,-sumirac);
			
			if(finirac.getDay()!=0 && finirac.getDay()!=6){var finirac2 = sumarDias(finirac,-1);FIRAC = formatDate2(finirac2);}
			else{var finirac2 = sumarDias(finirac,1);FIRAC = formatDate2(finirac2);}

			sValor = d.txtCuenta.value + '|' + d.txtAuditoria.value + '|' + d.txtPrograma.value + '|' + d.txtFaseActividad.value + '|' + d.txtInicioActividad.value + '|' +  d.txtFinActividad.value + '|' + d.txtDiasActividad.value + '|' + FIFA + '|' + FIRAC + '|' + sOper + '|' + proceso + '|' + etapa;
			$.ajax({
				type: 'GET', url: '/guardar/fecha/' + sValor,
				success: function(response) {
					alert("Los datos se guardaron correctamente");
					$("#modalCapturado").modal('hide');
					recuperarTabla('tblActividadesByAuditoria', document.all.txtAuditoria.value, document.all.tablaActividades);
					RecuperaConfronta('tlbaudiuni',document.all.txtAuditoria.value, document.all.tablaConfronta);
					recuperaAuditoria(document.all.txtAuditoria.value);
					document.getElementById('modfain').style.display='inline';
					document.getElementById('modfafi').style.display='inline';
					return true;
				},
				error: function(xhr, textStatus, error){
				alert(' Error en function guardarFecha()\n\tstatusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error: ' + error );
					return false;
				}			
			});
		}
		
		function guardarEquipo(){
			var sValor="";
			var sOper=document.all.txtOperacionEquipo.value;
			var d = document.all;
			var separador="";
			var separador2="";
		
			for(i=0; i < lstEmpleados.length; i++){
				
				sValor = sValor + separador2 + d.txtCuenta.value + '|' + d.txtPrograma.value + '|' + d.txtAuditoria.value + '|' + lstEmpleados[i][0] + '|' + lstEmpleados[i][1];
				separador2='*';
			}

			$.ajax({
				type: 'GET', url: '/guardar/equipo/lider/' + sOper + '/' + sValor,
				success: function(response) {
					alert("Los datos se guardaron correctamente.");
					return true;
				},
				error: function(xhr, textStatus, error){
					alert(' Error en function guardarEquipo()\n\tstatusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error: ' + error );
					return false;
				}			
			});
		}	

	//validar
		
		function fechamayor(fecha){
			var comparar = new Date(fecha);
			var fecha = formatDate2(comparar);
			var limite = new Date("2018-04-30");
			
			limite = formatDate2(limite);
			
			if(fecha > limite){
				alert("Estas revasando de la fecha limite la fase de EJECUCIÓN");
			}else{
				//alert("estamos vien.. y es viernes..");
			}


			
	    }

		function validarreprograma(){
			lista = 'btnreprogra';
    	    $.ajax({ type: 'GET', url: '/'+ lista ,
	           	success: function(response) {
		          var obj = JSON.parse(response);                                 
		          if(obj.rol=='DGA'){
		          	document.getElementById('btnReprograma').style.display='inline';
		          }
		                                                                      
	           	},
	           	error: function(xhr, textStatus, error){
	                alert('function validarreprograma ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
	           }                                             
			});           
	    }

	    function valiRP(auditoria){
			lista = 'btnFormatoPlaneacion' + '/' + auditoria;
		
			$.ajax({ type: 'GET', url: '/'+ lista ,
	           	success: function(response) {
		        var obj = JSON.parse(response);


		        if(obj.numero=='SI'){
		        	document.all.btnFormatoPlaneacion.style.display='inline';
		        	document.all.btnNuevaActividad.style.display='none';
		        }else{
		        	document.all.btnFormatoPlaneacion.style.display='none';
		        	document.all.btnNuevaActividad.style.display='inline';
		        }
	
		       	},
	           	error: function(xhr, textStatus, error){
	                alert('function valiRP ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
	           }                                             
			});           
	    }
 


	    function validaPersonal(auditoria){
			lista = 'btnEquipo' + '/' + auditoria;
		
			$.ajax({ type: 'GET', url: '/'+ lista ,
	           	success: function(response) {
		        var obj = JSON.parse(response);


		        if(obj.numero > 1){
		        	document.all.percomi.style.display='inline';
		        	
		        }else{
		        	document.all.percomi.style.display='none';
		        }
	
		       	},
	           	error: function(xhr, textStatus, error){
	                alert('function validaPersonal ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
	           }                                             
			});           
	    }



	    function validaPEA(auditoria){
			lista = 'btnFormatoPEA' + '/' + auditoria;
		
			$.ajax({ type: 'GET', url: '/'+ lista ,
	           	success: function(response) {
		        var obj = JSON.parse(response);
		        
		        if(obj.numero=='SI'){
		        	document.all.btnFormatoPEA.style.display='inline';
		        }else{
		        	document.all.btnFormatoPEA.style.display='none';
		        }

		        if(obj.total > 0){
		        	document.all.fechasfase.style.display='inline';
		        }else{
		        	document.all.fechasfase.style.display='none';
		        }
	
		       	},
	           	error: function(xhr, textStatus, error){
	                alert('function validaPEA ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
	           }                                             
			});           
	    }



	    function valifases(auditoria){
			lista = 'btnfases' + '/' + auditoria;
		
			var lstarrefases = new Array();
			$.ajax({ type: 'GET', url: '/'+ lista ,
	           	success: function(response) {
		        var jsonData = JSON.parse(response);
		        var fas = 0;
		        fas = jsonData.datos;

	          	for(var i=0;i<fas.length;i++){
					lstarrefases[i] = new Array(fas.length);
					lstarrefases[i] = jsonData.datos[i];
					if(fas.length == 3){
						document.getElementById('btnCronograma').style.display='none';
						validarreprograma();
					}else{
						document.getElementById('btnCronograma').style.display='inline';
						document.getElementById('btnReprograma').style.display='none';
					}	
				}
		       	},
	           	error: function(xhr, textStatus, error){
	                alert('function valifases ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
	           }                                             
			});           
	    }

		function validafase(fase){
			var idcuen = document.getElementById('auditoria').value;
			
			$.ajax({
				type: 'GET', url: '/validarfase/' + fase+ '/' + idcuen ,
				success: function(response) {			
					var obj = JSON.parse(response);
					if(obj.valida=='true'){
			    		alert("Ya se tiene registro de esa fase");
			    		document.all.txtFaseActividad.value="";
			    		document.all.txtFaseActividad.focus();
					}
				},
				error: function(xhr, textStatus, error){
					alert('statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
				}			
			});	
		}

		function validarapartado(apartado){
			var auditoria = document.getElementById('auditoria').value;
			
			$.ajax({
				type: 'GET', url: '/validarApartado/' + apartado + '/' + auditoria ,
				success: function(response) {			
					var obj = JSON.parse(response);
					if(apartado==obj.valida){
			    		alert("Ya se tiene registro de este Apartado");
			    		document.all.txtApartado.value="";
			    		document.all.txtApartado.focus();
					}
				},
				error: function(xhr, textStatus, error){
					alert('statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
				}			
			});
		}

		function validarguardalo(){
				
			if (document.all.txtFechaInicio.value==''){
	 			alert("Debe ingresar una Fecha Inicio.");
	 			document.all.txtFechaInicio.focus();
	 			return false;
	 		}
			if (document.all.txtFechaFin.value==''){
	 			alert("Debe ingresar una Fecha Termino.");
	 			document.all.txtFechaFin.focus();
	 			return false;
	 		}

	 		return true;
	 	}

		function validarConfron(){
				
			if (document.all.txtUnidad.value==''){
	 			alert("Debe seleccionar un SUJETO.");
	 			document.all.txtUnidad.focus();
	 			return false;
	 		}
			if (document.all.txtFConfronta.value==''){
	 			alert("Debe ingresar una FECHA DE CONFRONTA.");
	 			document.all.txtFConfronta.focus();
	 			return false;
	 		}
	 		return true;
	 	}

	 	function validarFases(){
				
			if(document.all.txtFaseActividad.value==''){
				alert("Debe de ingresar la FASE");
				document.all.txtFaseActividad.focus();
				return false;
			}	
			if (document.all.txtInicioActividad.value==''){
	 			alert("Debe seleccionar una FECHA DE INICIO.");
	 			document.all.txtInicioActividad.focus();
	 			return false;
	 		}
			if (document.all.txtFinActividad.value==''){
	 			alert("Debe seleccionar una FECHA FIN.");
	 			document.all.txtFinActividad.focus();
	 			return false;
	 		}
	 		return true;
	 	}


	  	function formatodia(valor){
	 	 	var fechafin;
	 	 	fecha = valor;
	 	 	tmp = fecha.split('-');
	 	 	d= tmp[0];
	 	 	fecha = valor;
	 	 	tmp = fecha.split('-');
	 	 	m= tmp[1];
	 	 	fecha = valor;
	 	 	tmp = fecha.split('-');
	 	 	y= tmp[2];
	   		return fechafin = y + '-' + m + '-' + d;
		}

		function formatodia2(valor){
	 	 	var fechafin;
	 	 	fecha = valor;
	 	 	tmp = fecha.split('-');
	 	 	y= tmp[0];
	 	 	fecha = valor;
	 	 	tmp = fecha.split('-');
	 	 	m= tmp[1];
	 	 	fecha = valor;
	 	 	tmp = fecha.split('-');
	 	 	d= tmp[2];
	   		return fechafin = d + '-' + m + '-' + y;
		}

		function validarfechainicial(finicio){
		    var dtFechaActual = finicio;
		    
		 	FAInicio = document.getElementById('FAInicio').value;
			tmp = FAInicio.split('-');
			FID = tmp[0];
			FAInicio = document.getElementById('FAInicio').value;
			tmp = FAInicio.split('-');
			FIM = tmp[1];
			FAInicio = document.getElementById('FAInicio').value;
			tmp = FAInicio.split('-');
			FIY = tmp[2];
			var dtFechaInicio = FIY + '-' + FIM + '-' + FID;

			FAFinal = document.getElementById('FAFinal').value;
			tmp = FAFinal.split('-');
			FFD = tmp[0];
			FAFinal = document.getElementById('FAFinal').value;
			tmp = FAFinal.split('-');
			FFM = tmp[1];
			FAFinal = document.getElementById('FAFinal').value;
			tmp = FAFinal.split('-');
			FFY = tmp[2];
			var dtFechaFinal = FFY + '-' + FFM + '-' + FFD;
		    
		    if(Date.parse(dtFechaInicio) > Date.parse(dtFechaActual)){
		        alert("Estas ingresando una fecha menor a la Fecha de Inicio de la Auditoria");
		        document.all.txtInicioActividad.value='';
		        return false;
		    }else{
		    	if(Date.parse(dtFechaFinal) < Date.parse(dtFechaActual)){
		        alert("Estas ingresando una fecha Mayor a la Fecha de Final de Auditoria");
		        document.all.txtInicioActividad.value='';
		        return false;
		    }
			}
		 
		    return true;
		}

	 	function validarfechafinal(fFin){
	 		var dtFechaActual = fFin;
	 		FAFinal = document.getElementById('FAFinal').value;
			tmp = FAFinal.split('-');
			FFD = tmp[0];
			FAFinal = document.getElementById('FAFinal').value;
			tmp = FAFinal.split('-');
			FFM = tmp[1];
			FAFinal = document.getElementById('FAFinal').value;
			tmp = FAFinal.split('-');
			FFY = tmp[2];
			var dtFechaFinal = FFY + '-' + FFM + '-' + FFD;
			
		    if(Date.parse(dtFechaActual) > Date.parse(dtFechaFinal)){
		        alert("Estas ingresando una fecha mayor a la Fecha de Final de la Auditoria");
		        document.all.txtFinActividad.value='';
		        return false;
		    }
		 
		    return true;
		}

		function validarfechaConfronta(fconfron,fira,fifa){
		    var Fechacon = formatodia(fconfron)
		    var FechaIRA = formatodia(fira);
		    var FechaIFA = formatodia(fifa);

		    if(Date.parse(Fechacon) < Date.parse(FechaIRA)){
		        alert("Estas ingresando una fecha menor a la Fecha de IRA");
		        document.all.txtFConfronta.value='';
		        return false;
		    }else{
		    	if(Date.parse(Fechacon) > Date.parse(FechaIFA)){
		        alert("Estas ingresando una fecha Mayor a la Fecha de IFA");
		        document.all.txtFConfronta.value='';
		        return false;
			    }
			}
		    return true;
		}

		function valfeIFA(fecha){
			$.ajax({
				type: 'GET', url: '/feIFA/' + fecha ,
				success: function(response) {			
					var obj = JSON.parse(response);
		 	 	
			 	 	var valor = obj.mas;

	 			return valor;  
				},
				error: function(xhr, textStatus, error){
					alert('statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
				}			
			});
	 	}

	 	function validarfechaIRA(fira){
	 		var auditoria = document.getElementById('auditoria').value;
	 		var fase = 'EJECUCION'
			$.ajax({
				type: 'GET', url: '/valira/' + auditoria + '/' + fase ,
				success: function(response) {			
					var obj = JSON.parse(response);
			 	 	var Feira =formatodia(fira);
			 	 	if(response==false){
//			 	 		alert(" --- ");

			 	 	}else{
				 	 	if(Date.parse(Feira) >= Date.parse(obj.fin)){
							alert("Estas ingresando una fecha mayor a la fecha Final de Ejecución.");
							var fechaIRAC = formatodia2(obj.IRA);
							document.all.txtFIRA.value='' + fechaIRAC;
							return false;
						}
			 		   return true;
			 	 	}
				},
				error: function(xhr, textStatus, error){
					alert('statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
				}			
			});
		}

		function validarfechaIFA(fifa){
			var auditoria = document.getElementById('auditoria').value;
	 		var fase = 'EJECUCION'
	 		var dias = 20;
			$.ajax({
				type: 'GET', url: '/valira/' + auditoria + '/' + fase ,
				success: function(response) {			
					var obj = JSON.parse(response);
			 	 	
				 	 	var Feifa =formatodia(fifa);

				 	 	var valiIFA =  formatodia2(obj.fin)
				 	 	var valor = valfeIFA(valiIFA);
						if(Date.parse(Feifa) <= Date.parse(obj.fin)){
							alert("Estas ingresando una fecha menor a la fecha Final de Ejecución.");
							var fechaIFA = formatodia2(obj.IFA);
							document.all.txtFIFA.value='' + fechaIFA;
							return false;
						}
			 		 	return true;

			 	 	  
				},
				error: function(xhr, textStatus, error){
					alert('statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
				}			
			});
		}

		function validarActividad(){
			if (document.all.txtFaseActividad.selectedIndex==0){alert("Debe seleccionar la FASE.");document.all.txtFaseActividad.focus();return false;}	
			if (document.all.txtApartado.selectedIndex==0){alert("Debe seleccionar el APARTADO.");document.all.txtApartado.focus();return false;}		
			if (document.all.txtDescripcionActividad.value==''){alert("Debe capturar el campo ACTIVIDAD.");document.all.txtDescripcionActividad.focus();return false;}
			if (document.all.txtPrioridadActividad.selectedIndex==0){alert("Debe seleccionar la PRIORIDAD.");document.all.txtPrioridadActividad.focus();return false;}		
			if (document.all.txtImpactoActividad.selectedIndex==0){alert("Debe seleccionar el IMPACTO.");document.all.txtImpactoActividad.focus();return false;}
			if (document.all.txtResponsableActividad.selectedIndex==0){alert("Debe seleccionar el RESPONSABLE.");document.all.txtResponsableActividad.focus();return false;}
			if (document.all.txtEstatusActividad.selectedIndex==0){alert("Debe seleccionar el ESTATUS.");document.all.txtEstatusActividad.focus();return false;}		
			return true;
		}

		function validaraudi(){
			if (document.all.txtResponsable.selectedIndex==0){alert("Debe seleccionar la DIRECCIÓN.");document.all.txtResponsable.focus();return false;}
			if (document.all.txtAreaSubresponsable.selectedIndex==0){alert("Debe seleccionar el SUBRESPONSABLE.");document.all.txtAreaSubresponsable.focus();return false;}
			return true;
		}
	
	/// fecha
		$(function(){
			$.datepicker.regional['es'] = {
		        closeText: 'Cerrar',
		        prevText: '&#x3c;Ant',
		        nextText: 'Sig&#x3e;',
		        currentText: 'Hoy',
		        monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
		        monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'],
		        dayNames: ['Domingo','Lunes','Martes','Mi&eacute;rcoles','Jueves','Viernes','S&aacute;bado'],
		        dayNamesShort: ['Dom','Lun','Mar','Mi&eacute;','Juv','Vie','S&aacute;b'],
		        dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','S&aacute;'],
		        weekHeader: 'Sm',
		        isRTL: false,
		        showMonthAfterYear: false,
		        yearSuffix: ''
			};

			$.datepicker.setDefaults($.datepicker.regional["es"]);

			$("#finicio").datepicker({
				dateFormat:'yy-mm-dd',
				numberOfMonths: 2,
				beforeShowDay: $.datepicker.noWeekends,
				onClose: function ( selectedDate ) {
				 	$( "#fFin" ).datepicker( "option", "minDate", selectedDate);
				 	$('#finicio').datepicker("option", "disabled", true);
					document.getElementById('modfain').style.display='inline';
				}
			});

			$("#fFin").datepicker({
				minDate: null,
		    	maxDate: null,
				dateFormat:'yy-mm-dd',
				firstDay: 1,
				numberOfMonths: 2,
				beforeShowDay: $.datepicker.noWeekends,
				onClose:function(selectedDate){
					$('#fFin').datepicker("option", "disabled", true);
					document.getElementById('modfafi').style.display='inline';
				} 
			});

			$("#FAInicio").datepicker({
				minDate: null,
		    	maxDate: null,
				dateFormat:'dd-mm-yy',
				firstDay: 1,
				numberOfMonths: 2,
				beforeShowDay: $.datepicker.noWeekends,
				onClose: function( selectedDate ) {
					$('#FAInicio').datepicker("option", "disabled", true);
				 	$( "#FAFinal" ).datepicker( "option", "minDate", selectedDate );
			 	}
			});

			$("#FAFinal").datepicker({
				minDate: null,
	        	maxDate: null,
				dateFormat:'dd-mm-yy',
				firstDay: 1,
				numberOfMonths: 2,
				beforeShowDay: $.datepicker.noWeekends,
				onClose: function( selectedDate ) {
					$( "#finicio" ).datepicker( "option", "minDate", selectedDate);
					$('#FAInicio').datepicker("option", "disabled", true);
				}
			});

			$("#FIRA").datepicker({
				minDate: null,
	        	maxDate: null,
				dateFormat:'dd-mm-yy',
				firstDay: 1,
				numberOfMonths: 2,
				beforeShowDay: $.datepicker.noWeekends
			});

			$("#FIFA").datepicker({
				minDate: null,
	        	maxDate: null,
				dateFormat:'dd-mm-yy',
				firstDay: 1,
				numberOfMonths: 2,
				beforeShowDay: $.datepicker.noWeekends
			});

			$("#FConfronta").datepicker({
				minDate: null,
	        	maxDate: null,
				dateFormat:'dd-mm-yy',
				firstDay: 1,
				numberOfMonths: 1,
				beforeShowDay: $.datepicker.noWeekends
			});
		});

	// document
		$(document).ready(function(){	
			
			getMensaje('txtNoti',1);
			console.log(moment().format('HH:mm:ss'));
			/*$('body').mouseover(function(){
				setTimeout('location.href="./cerrar"',1200000);
			});*/

			recuperarLista('lstAreasResponsables', document.all.txtResponsable);
			recuperarLista('lstSubResponsables', document.all.txtAreaSubresponsable);
			recuperarLista('lstTiposAuditorias', document.all.txtTipoAuditoria);
			recuperarLista('lstSectores', document.all.txtSector);
			recuperarLista('lstObjetosByEnables',document.all.txtObjeto);
			recuperarLista('lstResponByauditores', document.all.txtResponsableActividad);

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
					sValores = sValores + '|ALTA|ACTIVO'; // prioridad y estatus
					sValores = sValores + '|AUDI';

					//alert("Los valores a guardar son: TipoOperacion|Auditoria|AuditoriaComentario|comentario|prioridad|estatus=>" + sValores);

					$.ajax({ type: 'GET', url: '/guardar/auditoriaComentario/' + sValores ,
						success: function(response) {
							sRespuesta = response.replace(/^\s+/g,'').replace(/\s+$/g,'');

							if(sRespuesta == "OK"){
								recuperarTablaComentario('tblComentariosByAuditoriasaudi', document.all.txtAuditoria.value, document.all.tblListaComentarios);
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

			$( "#btnComentarios" ).click(function() {		
				$('#modalComentarios').removeClass("invisible");
				$('#modalComentarios').modal('toggle');
				$('#modalComentarios').modal('show');
				document.all.txtOperacionComentarios.value = 'INS';
				document.all.txtComentarioAuditoria.value = "";
				//var miTipoAuditoria = document.all.txtTipoAuditoria.options[document.all.txtTipoAuditoria.selectedIndex].value;
				var miAuditoria = document.all.txtAuditoria.value;
				recuperarTablaComentario('tblComentariosByAuditoriasaudi', miAuditoria,document.all.tblListaComentarios);
			});				
		
			
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
				limpiarcamposapartado();
				var editor = CKEDITOR.instances["DescripcionActividad"];
				if (editor) { editor.destroy(true); }
				CKEDITOR.replace('DescripcionActividad');


				document.all.txtOperacion.value='INS';
				document.getElementById('audito').value = document.getElementById('auditoria').value;
				//document.all.btnNuevaActividad.style.display='inline';
				$('#modalActividad').modal('show');
				document.all.btnCancelar2Actividad.style.display='none';
				document.all.btnCancelarCronograma.style.display='inline';
				limpiarCamposActividad();
			});

			$( "#btnNuevaFecha" ).click(function() {
				document.all.txtOpera.value='INS';
				$('#modalCapturado').modal('show');
				document.all.txtFaseActividad.disabled=false;
				$('#finicio').datepicker("option","disabled",false);	
				$('#fFin').datepicker("option","disabled", false);
				document.getElementById('modfain').style.display='none';
				document.getElementById('modfafi').style.display='none';
				
				limpiarCamposActividad();
			});

			$( "#btnGuardarActividad" ).click(function() {		
				var url = "croactivi.php";
				var operacion = document.all.txtOperacion.value;
				var actividad = CKEDITOR.instances['DescripcionActividad'].getData();
				var proceso = 'RP';
				var etapa = 'ELABORADA';



				var dataString = "";
				dataString = 'txtUsuario='+document.all.txtUsuario.value+'&txtCuenta='+document.all.txtCuenta.value+'&txtPrograma='+document.all.txtPrograma.value+'&txtOperacion='+document.all.txtOperacion.value+'&txtaudi='+document.all.txtaudi.value+'&txtFaseActividadApartado='+document.all.txtFaseActividadApartado.value+'&txtApartado='+document.all.txtApartado.value+'&txtDescripcionActividad='+actividad+'&txtProceso='+proceso+'&txtetapa='+etapa;
				
				//alert(dataString);
				$.ajax({                        
		           type: "POST",                 
		           url: url,                     
		           data: dataString, 
		           success: function(data)  {
		           		recuperarApartado('tblApartadoByAuditoria', document.all.txtAuditoria.value, document.all.tablaApartado);
		           		document.all.btnNuevaActividad.style.display='inline';

						$('#modalActividad').modal('hide');
		           		return true;
					},
						error: function( jqXhr, textStatus, errorThrown ){
							console.log( errorThrown );
						}			
				});
			});


			$( "#btnCancelarActividad" ).click(function() {
				$('#modalActividad').modal('hide');
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
			
			$( "#btnConfronta" ).click(function() {
				limpiarCon();
				document.getElementById('FConfronta').readOnly = true;
				document.getElementById('btnGuardarConfronta').style.display='none';
				document.all.modconfro.style.display='none';
				$('#modalConfronta').removeClass("invisible");
				$('#modalConfronta').modal('toggle');
				$('#modalConfronta').modal('show');
			});

			$( "#btnCancelarConfronta" ).click(function() {
				limpiarCon();
				$('#modalConfronta').modal('hide');

				RecuperaConfronta('tlbaudiuni', document.all.txtAuditoria.value, document.all.tablaConfronta);
			});

			$( "#btnGuardarCapturado" ).click(function() {
				if(validarFases()){
					guardarFecha();
				}
				
			});

			$( "#btnCancelarCapturado" ).click(function() {
				$('#modalCapturado').modal('hide');			
			});

			$( "#btnCronograma" ).click(function() {
				titCedula.innerHTML="<h3><i class='fa fa-pencil-square-o'></i>" + 'Fechas Por Fase..' + "</h3>";
				recuperarTabla('tblActividadesByAuditoria', document.all.txtAuditoria.value, document.all.tablaActividades);
				$('#modalCronograma').removeClass("invisible");
				$('#modalCronograma').modal('toggle');
				$('#modalCronograma').modal('show');
				document.all.divListaActividades.style.display='inline';
				document.all.divListaApartado.style.display='none';
				document.all.btnNuevaActividad.style.display='none';
				document.all.btnNuevaFecha.style.display='inline'
			});

			$( "#btnReprograma" ).click(function() {
				titCedula.innerHTML="<h3><i class='fa fa-pencil-square-o'></i>" + 'Reprogrmación de fechas' + "</h3>";
				recuperarTabla('tblActividadesByAuditoria', document.all.txtAuditoria.value, document.all.tablaActividades);
				$('#modalCronograma').removeClass("invisible");
				$('#modalCronograma').modal('toggle');
				$('#modalCronograma').modal('show');
				document.all.divListaActividades.style.display='inline';
				document.all.divListaApartado.style.display='none';
				document.all.btnNuevaActividad.style.display='none';
				document.all.btnNuevaFecha.style.display='inline';

			});

			$( "#btnCancelarCronograma" ).click(function() {
				$('#modalCronograma').modal('hide');
				recupeFases('lstauditoriaByFases', document.all.txtAuditoria.value, document.all.tablaFases);
			});

			$( "#btnCancelar2Actividad" ).click(function() {
				document.all.divListaActividades.style.display='inline';
				document.all.divCapturaActividad.style.display='none';
				document.all.btnNuevaActividad.style.display='inline';
				document.all.btnCancelarCronograma.style.display='inline';
				document.all.btnCancelarActividad.style.display='none';
				document.all.btnGuardarActividad.style.display='none';
				document.all.btnCancelar2Actividad.style.display='none';
				document.all.btnGuardarCronograma.style.display='inline';
			});


			$( "#btnGuardarCronograma" ).click(function() {
				$('#modalCronograma').modal('hide');
			});				

			$( "#btnRegresar" ).click(function() {
				document.all.listasAuditoria.style.display='inline';
				document.all.capturaAuditoria.style.display='none';		
			});	

			$( "#btnGeneraResul" ).click(function() {
				Audit = document.getElementById('auditoria').value;
				modalWin("/rptFormatoPEA.php" + "?param1="+ nCampana + "&param2=" + Audit);
			});

			$( "#btnGeneraIFA" ).click(function() {
				Audit = document.getElementById('auditoria').value;
				modalWin("/ifa.php" + "?param1="+ nCampana + "&param2=" + Audit);
			});

			$( "#btnGeneraIRA" ).click(function() {
				AudiNota = document.getElementById('auditoria').value;
				modalWin("/ira.php?area=" + "&param1="+ AudiNota);
			});

			$( "#btnFormatoPEA" ).click(function() {
				Audit = document.getElementById('auditoria').value;
				modalWin("/rptFormatoPEA.php" + "?param1="+ nCampana + "&param2=" + Audit);
			});

			$( "#btnFormatoPlaneacion" ).click(function() {
				Audit = document.getElementById('auditoria').value;
				modalWin("/rptReportePlaneacion.php" + "?param1="+ nCampana + "&param2=" + Audit);
			});

			$( "#btnNota" ).click(function() {
				AudiNota = document.getElementById('auditoria').value;
				modalWin("/notaEjecutiva.php?area=" + "&param1="+ AudiNota + "&titulo=NOTA EJECUTIVA DE AUDITORÍA");
			});

			$( "#btnGuardarConfronta" ).click(function() {
				if(validarConfron()){guardaConfron();}			
			});

			$( "#btnGuardarObjeto" ).click(function() {
				  if(validaraudi()){ guardarAudi();}
				/*if(validarguardalo()){document.all.txtOperacionObjeto.value='UPD';}*/
			});


			$( "#btnGuardarAut" ).click(function() {
				apagarBotones();
				asignarEtapa(document.all.txtAuditoria.value, sProcesoNuevo, sEtapaNueva);
				$('#modalAutorizar').modal('hide');
			});

			$( "#btnAUTORIZACION" ).click(function() { $('#modalAutorizar').modal('show');});

			$( "#btnGuardarLoc" ).click(function() {
				$('#modalLocalizacion').modal('hide');
			});

			$( "#btnLocalizacion" ).click(function() {
				inicializar();
				$('#modalLocalizacion').modal('show');	
			});	


			$( "#btnCancelarAut" ).click(function() {
				$('#modalAutorizar').modal('hide');
			});

			$( "#btnApartado" ).click(function() {
				$('#modalCronograma').modal('show');
				titCedula.innerHTML="<h3><i class='fa fa-pencil-square-o'></i>" + 'Apartado..' + "</h3>";
				recuperarApartado('tblApartadoByAuditoria', document.all.txtAuditoria.value, document.all.tablaApartado);
				document.all.divListaApartado.style.display='inline';
				document.all.divListaActividades.style.display='none';
				//document.all.divCapturaActividad.style.display='inline';
				document.all.divListaActividades.style.display='none';
				//document.all.btnNuevaActividad.style.display='inline';
				document.all.btnNuevaFecha.style.display='none';

				valiRP($("#auditoria").val());

				
			});
			
			$( "#btnCancelarLoc" ).click(function() {
				$('#modalLocalizacion').modal('hide');
			});

			$( "#txtObjetivoEdit" ).click(function(){
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

			$( "#txtReConfronEdit" ).click(function(){
				document.all.txtTextoAmplio2.value = document.all.txtReConfron.value;
				$('#modalTextoLargo2').modal('show');
				sEditando="C";
			});

			$( "#txtActividadEdit" ).click(function(){
				document.all.txtTextoAmplio2.value = document.all.txtDescripcionActividad.value;
				$('#modalTextoLargo2').modal('show');
				sEditando="A";
			});	

			$( "#txtActividadComEdit" ).click(function(){
				document.all.txtTextoAmplio2.value = document.all.txtDescripcionActividad.value;
				$('#modalTextoLargo2').modal('show');
				sEditando="Ab";
			});			

			$( "#btnCancelarTexto" ).click(function() {
				$('#modalTextoLargo').modal('hide');				
			});

			$( "#btnCancelarTexto2" ).click(function() {
				$('#modalTextoLargo2').modal('hide');				
			});

			$( "#btnGuardarTexto2" ).click(function() {
				$('#modalTextoLargo2').modal('hide');				
				if(sEditando=="A"){document.all.txtDescripcionActividad.value=document.all.txtTextoAmplio2.value;}
				if(sEditando=="Ab") document.all.txtReConfron.value=document.all.txtTextoAmplio2.value;
				if(sEditando=="C") document.all.txtReConfron.value=document.all.txtTextoAmplio2.value;
			});	
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
					<div class="col-xs-3"><h2>P. E</h2></a></div>
					<?php require '/templates/noti.php'; ?>
					<div class="col-xs-3">
						<ul class="nav navbar-nav  pull-right">
							<li class="dropdown pull-right">            
								<a data-toggle="dropdown" class="dropdown-toggle" href="/">
									<i class="fa fa-user"></i> <b><?php echo $_SESSION["sUsuario"] ?></b> <b class="caret"></b>
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
				<div class="col-md-3">
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
							<table class="table table-striped table-bordered table-hover table-condensed">
									<thead style="width: 100%; font-size: xx-small;">
										<tr><th style="text-align:center;">AUDITORIAS</th><th style="text-align:center;">CANTIDAD</th></tr>
									</thead>
									<tbody id="tblcanvasJG" style="text-align:center; font-size: xx-small;">									
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
					  <div class="pull-left"><h3><i class="fa fa-bars"></i> Lista de Auditorías</h3></div>
					  <div class="widget-icons pull-right">
					  	<a href="#" class="wminimize"><i class="fa fa-chevron-up"></i></a> 						
					  </div>  
					  <div class="clearfix"></div>
					</div>             
					<div class="widget-content">
							<div class="table-responsive" style="height: 570px; overflow: auto; overflow-x:hidden;">
								<table class="table table-striped table-bordered table-hover table-condensed cur">
									<thead>  
										<tr style="font-size: xx-small"><th>Cve. Auditoria</th><th>Dirección General</th><th>Sujeto de Fiscalización</th><th>Rubro(s) de Fiscalización</th><th>Tipo de Auditoría</th></tr>
									</thead>
										<tbody>										
											<?php foreach($datos as $key => $valor): ?>
											<tr onclick=<?php echo "javascript:recuperaAuditoria('" . $valor['auditoria'] . "');"; ?> style="width: 100%; font-size: xx-small">							  	<td width="10%;"><?php echo $valor['claveAuditoria']; ?></td>
											  <td width="20%;"><?php echo $valor['area']; ?></td>
											  <td width="30%;"><?php echo $valor['sujeto']; ?></td>
											  <td width="30%;"><?php echo $valor['objeto']; ?></td>
											  <td width="10%;"><?php echo $valor['tipo']; ?></td>
											</tr>
											<?php endforeach; ?>										
										</tbody>
								</table>
							</div>							
					</div>
				
					</div>
				</div>
			  </div>
			  
  			<div class="row" id="capturaAuditoria" style="display:none; padding:0px; margin:0px;">			
				<div class="col-xs-12">				
					<div class="widget">
						<!-- Widget head -->
						<div class="widget-head">
						  <div class="pull-left"><h3><i class="fa fa-pencil-square-o"> </i> Planeación Específica</h3></div>
						  <!--<div class="widget-icons pull-right">-->
						  <div class="btn-toolbar pull-right" role="toolbar"> 
							<button type="button" id="btnCronograma"	class="btn btn-warning  btn-xs"><i class="fa fa-calendar"></i> Fechas Por Fase...</button>
							<button type="button" id="btnApartado"	class="btn btn-warning  btn-xs"><i class="fa fa-calendar"></i> Apartado...</button>
							<button type="button" id="btnEquipo"	class="btn btn-warning  btn-xs"><i class="fa fa-users"></i> Personal Comisionado...</button>
							<button type="button" id="btnConfronta"		class="btn btn-warning  btn-xs"><i class="fa fa-calendar"> </i> Confronta...</button>
							<button type="button" class="btn btn-default  btn-xs" name="btnAUTORIZACION" id="btnAUTORIZACION" style="display:none;"><i class="fa fa-external-link"></i>Autoriza Projecto</button>

							<button type="button" class="btn btn-default  btn-xs" name="btnAUTORIZACION" id="btnAUTORIZACION" style="display:none;"><i class="fa fa-external-link"></i>Autoriza Projecto</button>							
							
							<button type="button" id="btnComentarios" class="btn btn-warning btn-xs "><i class="fa fa-comments"></i> Comentarios...</button>
							<button type="button" id="btnReprograma" class="btn btn-warning btn-xs" style="display: none;"><i class="fa fa-calendar"></i> Reprogramación...</button>
							<div id="menudesple" class="btn-group">
								<button type="button" class="btn btn btn-success btn-xs">Formatos</button>
        						<button type="button" class="btn btn-default dropdown-toggle btn-xs" data-toggle="dropdown"><span class="fa fa-chevron-down"></span><span class="sr-only">Desplegar menú</span></button> 
        						<ul class="dropdown-menu" role="menu">
						          <li class="btn-link" style="display: none;"><a href="#" id="btnGeneraResul"><i class="fa fa-file"></i> Generar Resultado</a></li>
						          <li class="btn-link" style="display: none;"><a href="#" id="btnGeneraIFA"><i class="fa  fa-file"></i>	Generar IFA</a></li>
						          <li class="btn-link" style="display: none;"><a href="#" id="btnGeneraIRA"><i class="fa fa-file"></i>	Generar IRAC</a></li>
						          <li class="btn-link"><a href="#" id="btnNota"><i class="fa fa-file"></i>	Nota Ejecutiva</a></li>
						          <li class="btn-link"><a href="#" id="btnFormatoPEA"><i class="fa fa-file"></i>	Formato PEA</a></li>
						          <li class="btn-link"><a href="#" id="btnFormatoPlaneacion"><i class="fa fa-file"></i>	Formato RP</a></li>
						        </ul>
						     </div>
			
							<button type="button" id="btnGuardarObjeto"	 class="btn btn-primary btn-xs"><i class="fa fa-floppy-o"></i> Actualizar</button>
							<button type="button" id="btnRegresar" class="btn btn-default  btn-xs"><i class="fa fa-undo"></i> Regresar</button>

						  </div>  
						  <div class="clearfix"></div>
						</div>              

						<!-- Widget content -->
						<div class="widget-content">						
							<br>
							<div class="col-xs-7">
								<div class="form-group">									
									<label class="col-xs-2" style="display: none;">No. Auditoría</label>
									<div class="col-xs-1" style="display: none;"><input type="text" class="form-control"  id="auditoria" name="txtAuditoria" readonly/></div>
									<label class="col-xs-2 control-label">Clave Auditoría</label>
									<div class="col-xs-2"><input type="text" class="form-control" name="txtclaveAuditoria" readonly/></div>													
									<label class="col-xs-3 control-label">Tipo</label>
									<div class="col-xs-5">
										<select class="form-control" name="txtTipoAuditoria" readonly>
											<option value="">Seleccione...</option>
										</select>
									</div>								
								</div>									
								<br>
								<div class="form-group">
									<label class="col-xs-2">Dirección</label>
									<div class="col-xs-9  control-label">
										<select class="form-control" name="txtResponsable" id="resp" onchange="recuperarListaLigada('lstAreasSubResponsablesByResponsable', this.value, document.all.txtAreaSubresponsable); dessub();">
											<option value="">Seleccione...</option>											
										</select>
									</div>
									<div class="col-xs-1" id="idmodirec"><button type='button' id="modirec" onclick="modificardirsub('modirec');"><i class='fa fa-pencil-square-o'></i></button></div>
								</div>							
								<br>
									<div class="form-group">
									<label class="col-xs-2">Subdirección</label>
									<div class="col-xs-9  control-label">
										<select id="arsubre" class="form-control" name="txtAreaSubresponsable">
											<option value="">Seleccione...</option>											
										</select>
									</div>
									<div id="idmodsub" class="col-xs-1"><button type='button' id="modsub" onclick="modificardirsub('modsub');"><i class='fa fa-pencil-square-o'></i></button></div>
								</div>	
								<br>
								<div class="form-group">
									<label class="col-xs-2 control-label">Sujetos(s)</label>
									<div class="col-xs-10">
										<select class="form-control" name="txtSujeto" readonly size=4 style=" margin: 0 0 5px !important;">
											 
										</select>
									</div>								
								</div>
								<br>
								<div class="form-group">
									<label class="col-xs-2 control-label">Rubro(s)</label>
									<div class="col-xs-10">
										<select class="form-control" name="txtObjeto" readonly size=4 style=" margin: 0 0 5px !important;">
											<!--<option value="">Seleccione...</option>-->											
										</select>
									</div>								
								</div>
								<br>
								<div class="form-group">
									<label class="col-xs-2 control-label"> Inicio</label>
									<div class="col-xs-4">
										<input type="text" id="FAInicio" class="form-control" onchange="botonactu();" name="txtFechaInicio" onkeydown="return false;"/>
									</div>
									<label class="col-xs-2 control-label">Termino</label>
									<div class="col-xs-4" style="margin: 0px 0px 5px !important;">
										<input type="text" id="FAFinal" onchange="botonactu();" class="form-control" name="txtFechaFin" onkeydown="return false;"/>
									</div>
								</div>	
							<br>
							<div class="form-group">									
								<label class="col-xs-2 control-label">Objetivo(s) <i class="fa fa-pencil"  id="txtObjetivoEdit" readonly></i></label>
								<div class="col-xs-2"><textarea class="form-control" rows="3" placeholder="Objetivo(s)" id="txtObjetivo" name="txtObjetivo" readonly style="width: 147px !important; height: 35px !important; resize:none;margin: 0 0 0 -1px !important;"></textarea></div>
													
																
								<label class="col-xs-2 control-label" style="margin: 0px 0px 0px -13px ! important;">Alcance(s) <i class="fa fa-pencil"  id="txtAlcanceEdit"></i></label>
								<div class="col-xs-2"><textarea class="form-control" rows="3" placeholder="Alcance(s)" id="txtAlcance" name="txtAlcance" readonly style="width: 147px !important; height: 35px !important; resize:none;margin: 0 0 0 -11px !important"></textarea></div>
							
							
																
								<label class="col-xs-2 control-label" style="margin: 0px 0px 0px -12px ! important;">Justificación <i class="fa fa-pencil"  id="txtJustificacionEdit"></i></label>
								<div class="col-xs-2"><textarea class="form-control" rows="3" placeholder="Justificación" id="txtJustificacion" name="txtJustificacion" readonly style=" width: 147px !important; height: 35px !important;resize:none; margin: 0 0 6px -11px !important;"></textarea></div>
							</div>
							<br>

							<div class="clearfix"></div>
							
							<div class="form-group">									
								<label class="col-xs-2 control-label"> Fecha IRAC</label>
								<div class="col-xs-2"><input type="text" id="FIRA" class="form-control" readonly onchange="validarfechaIRA(FIRA.value)" name="txtFIRA" onkeydown="return false;"/></div>
								<div class="col-xs-2"><button type='button' id="modirac" onclick="modificarfecha('modirac');"><i class='fa fa-pencil-square-o'></i></button></div>

								<label class="col-xs-3 control-label"> Fecha IFA</label>
								<div class="col-xs-2"><input type="text" id="FIFA" class="form-control" readonly onchange="validarfechaIFA(FIFA.value)" name="txtFIFA" onkeydown="return false;" style="resize:none; margin: 0 0 6px 0 !important;"/></div>
								<div class="col-xs-2" style="width:1% !important;"><button type='button' id="modifa"  onclick="modificarfecha('modifa');"><i class='fa fa-pencil-square-o'></i></button></div>
							</div>
							<br>

							<div class="form-group">									
								<label class="col-xs-2 control-label">Tipo de Obs.</label>
								<div class="col-xs-10">
									<select class="form-control cur" id="TipoObs" name="txtTipoObs" onchange="cajaResultados(this.value); botonactu();" style="resize:none; margin: 0 0 6px 0 !important;">
										<option value="">Seleccione</option>
										<option value="NINGUNA" Selected>NINGUNA OBSERVACION</option>											
										<option value="SIMPLE">OBSERVACIÓN SIMPLE</option>											
										<option value="GRAVE">PROBABLE FALTA GRAVE</option>											
									</select>
								</div>	
							</div>
							<br>							
							<div class="form-group" id="divResumen" style="display:none;">									
								<label class="col-xs-2 control-label">Observación</label>
								<div class="col-xs-10"><textarea class="form-control" onclick="botonactu();" rows="5" id="txtObserva" name="txtObserva" placeholder="Escriba aqui el resumen o resultado obtenido..."></textarea></div>	
							</div>
							<div class="clearfix"></div>
							</div>
							<div class="col-xs-5">
							 	<div class="form-group" id="fechasfase">									
									<div class="col-xs-12">
										<div class="widget-head">
											<div class="pull-left"><h3><i class="fa fa-bars"></i> Fechas por Fase</h3></div>
											<div class="widget-icons pull-right">
												<a href="#" class="wminimize"><i class="fa fa-chevron-down"></i></a> 						
											</div>  
										  	<div class="clearfix"></div>
										</div>
										<div class="widget-content" style="display: none;">
											<table class="table table-striped table-bordered table-hover table-condensed">
												<thead>
												<tr style="font-size: small"><th>Fase</th><th>Fecha Inicio</th><th>Fecha Fin</th><th>Días Efectivos</th></tr>
												</thead>
												<!--<tbody id="tablaFases" onclick="editarCronograma();" style="width: 100%; font-size: xx-small"></tbody>-->
												<tbody id="tablaFases" style="width: 100%; font-size: small"></tbody>
											</table>
										</div>
									</div>									
								</div>
								<div class="form-group" id="percomi">
									<div class="col-xs-12">	
											<div class="widget-head">
												<div class="pull-left"><h3><i class="fa fa-bars"></i> Personal Comisionado</h3></div>
												<div class="widget-icons pull-right">
													<a href="#" class="wminimize"><i class="fa fa-chevron-down"></i></a> 						
												</div>  
											  	<div class="clearfix"></div>
											</div>
											<div class="widget-content" style="display: none;">
												<table class="table table-striped table-bordered table-hover table-condensed">
													<thead>
													<tr style="font-size: small"><th>Lider</th><th>Nombre</th><th>Puesto</th></tr>
													</thead>
													<tbody id="tablaEquipo" style="width: 100%; font-size: small">	
													</tbody>
												</table>
											</div>
									</div>
								</div>

								<div class="form-group" id="confron">									
									<div class="col-xs-12">
											<div class="widget-head">
												<div class="pull-left"><h3><i class="fa fa-bars"></i> Confronta</h3></div>
												<div class="widget-icons pull-right">
													<a href="#" class="wminimize"><i class="fa fa-chevron-down"></i></a> 						
												</div>  
											  	<div class="clearfix"></div>
											</div>
											<div class="widget-content" style="display: none;"">
												<table class="table table-striped table-bordered table-hover table-condensed">
													<thead>
													<tr style="font-size: small"><th>Unidad</th><th class="center">Fecha<br>confronta</th><th class="center">Hora<br>confronta</th><th class="center">Fecha<br>IRAC</th><th class="center">Fecha<br>IFA</th></tr>
													</thead>
													<tbody id="tablaConfronta" style="width: 100%; font-size: small">					  
																																	
													</tbody>
												</table>
											</div>							
									</div>									
								</div>	


								<br>
										
								<div class="clearfix" id="anexos"></div>											
								<div class="col-md-12">
									<div class="widget-head">
											<div class="pull-left"><h3><i class="fa fa-bars"></i> Anexos</h3></div>
											<div class="widget-icons pull-right">
												<a href="#" class="wminimize"><i class="fa fa-chevron-down"></i></a> 						
											</div>  
										  	<div class="clearfix"></div>
									</div>
									<div class="widget-content" style="display: none;">
										<ul class="nav nav-tabs">				
											<li class="active"><a href="#tab-papeles" data-toggle="tab">Papeles de Trabajo<i class="fa"></i></a></li>
											<li><a href="#tab-acciones" data-toggle="tab">Acciones y recomendaciones<i class="fa"></i></a></li>
											<li><a href="#tab-documentos" data-toggle="tab">Documentos<i class="fa"></i></a></li>
											<!--<li><a href="#tab-plan" data-toggle="tab">Avances por Actividad<i class="fa"></i></a></li>-->
										</ul>								
										<div class="tab-content">
											<div class="tab-pane" id="tab-documentos">
												<div class="table-responsive" style="height: 150px; overflow: auto; overflow-x:hidden;">
													<table class="table table-striped table-bordered table-hover">
													  <thead>
														<tr style="font-size: small"><th>Docto.</th><th>Flujo</th><th>Tipo</th><th>Fecha</th><th>Prioridad</th><th>Impacto</th><th>Archivo</th></tr>
													  </thead>
													  <tbody id="tblListaArchivos" style="width: 100%; font-size: small">								  

													  </tbody>
													</table>											
												</div>
												<br>
											</div>
											<div class="tab-pane active" id="tab-papeles">
												<div class="table-responsive" style=" width: 100%; height: 150px; overflow: auto; overflow-x:auto; font-size: xx-small">
													<table class="table table-striped table-bordered table-hover">
													  <thead>
														<tr style="font-size: small"><th>Clave Auditoria</th><th>ID Papel</th><th>Tipo Papel</th><th>Fase</th><th>Fecha Papel</th><th>Tipo Resultado</th><th>Resultado</th><th>Estatus</th><th>Documento</th></tr>

													  </thead>
													  <tbody id="tablaPapeles" style="width: 100%; font-size: small">								  
							
													  </tbody>
													</table>											
												</div>
											</div>
											<div class="tab-pane " id="tab-acciones">
												<div class="table-responsive" style="height: 150px; overflow: auto; overflow-x:hidden;">
													<table class="table table-striped table-bordered table-hover">
														<thead>
														<tr style="font-size: small"><th>No.</th><th>Tipo de Acción</th><th>Cédula de Potenciales</th><th>Dictámen Técnico</th><th>Estatus</th></tr>
														</thead>
														<tbody id="tablaAcciones" style="width: 100%; font-size: small">										  
										
														</tbody>
													</table>
												</div>									
											</div>
											<div class="tab-pane" id="tab-plan">
												<div class="table-responsive" style=" width: 100%; height: 150px; overflow: auto; overflow-x:auto; font-size: xx-small">
													<table class="table table-striped table-bordered table-hover">
													  <thead>
														<tr style="font-size: small"><th>Clave Auditoria</th><th>Sujeto de Fiscalización</th><th>Tipo de Auditoría</th><th>ID</th><th>Fase</th><th>Actividad</th><th>Porcentaje</th></tr>
													  </thead>
													  <tbody id="tablaAvances" style="width: 100%; font-size: small">										  
																										
													  </tbody>
													</table>
												</div>
												<br>
											</div>										
										</div>
									</div>
								</div>	
							
							</div>		
						<br>
																						
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

	<div id="modalTextoLargo2" class="modal fade" role="dialog" style="z-index: 1601">
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
								<div class="col-xs-20"><textarea class="ckeditor" rows="20"  id="txtTextoAmplio2" name="txtTextoAmplio2" style=" height: 288px !important;
								 width: 583px !important; resize:none;"></textarea></div>
							</div>
						</div>
					<div class="clearfix"></div>
				</div>				
				<div class="modal-footer">
					<button  type="button" class="btn btn-primary active" id="btnGuardarTexto2"><i class="fa fa-floppy-o"></i> Guardar</button>	
					<button  type="button" class="btn btn-default" id="btnCancelarTexto2"><i class="fa fa-undo"></i> Cerrar</button>	
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
								<div class="col-xs-20"><textarea class="form-control" rows="20"  id="txtTextoAmplio" name="txtTextoAmplio" style=" height: 288px !important;
								 width: 583px !important; resize:none;"></textarea></div>
							</div>
						</div>
					<div class="clearfix"></div>
				</div>				
				<div class="modal-footer">
					<!--<button  type="button" class="btn btn-primary active" id="btnGuardarTexto"><i class="fa fa-floppy-o"></i> Guardar</button>	-->
					<button  type="button" class="btn btn-default" id="btnCancelarTexto"><i class="fa fa-undo"></i> Cerrar</button>	
				</div>
			</div>
		</div>
	</div>


	<div id="modalCapturado" class="modal fade" role="dialog" style="z-index: 1600">
		<div class="modal-dialog">
			<input type='HIDDEN' name='txtOpera' value=''>									
			<!-- Modal content-->
			<div class="modal-content" style="width: 115% ! important;">									
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h3 class="modal-title"><i class="fa fa-home"></i> Capturando...</h3>
				</div>									
				<div class="modal-body">
						<div class="form-group">

							<label class="col-xs-2 control-label" style="display: none;">ID</label>
							<div class="col-xs-1" style="display: none;>"
								<input type="text" class="form-control" id="IDActividad" name="txtIDActividad" readonly/>
							</div>
							
							<label class="col-xs-2 control-label">Fase</label>
							<div class="col-xs-3">
								<select class="form-control" id="FaseActividad" name="txtFaseActividad" onchange="validafase(this.value);">
									<option value="" Selected>Seleccione...</option>
								</select>
							</div>
							<div class="col-xs-11" type="HIDDEN" style="margin: 0 0 4%  0 !important"></div>
						</div>

						<div class="form-group">								
							<label class="col-xs-2 control-label">Fecha Inicio</label>
							<div class="col-xs-2">
								<input type="text" id="finicio" class="form-control" name="txtInicioActividad" onkeydown="return false;"/>
							</div>
							<div class="col-xs-2" id="btnmodfain"><button id="modfain" type="button" onclick="modificarfecha('modfain');"><i class='fa fa-pencil-square-o'></i></button></div>

							<label class="col-xs-2 control-label">Fecha Término</label>
							<div class="col-xs-2">
								<input type="text" id="fFin" class="form-control" name="txtFinActividad" onchange="getDiasLaborale(finicio.value,fFin.value); fechamayor(this.value); " onkeydown="return false;"/>
							</div>
							<div class="col-xs-2" id="btnmodfafi">
							<button type='button' id="modfafi" onclick="modificarfecha('modfafi');"><i class="fa fa-pencil-square-o"></i></button></div>		
						</div>

						<div class="form-group" >
							<div style="margin: 101px  0 0 0 !important;">
								<label class="col-xs-2 control-label">Dias Efectivos</label>
								<div class="col-xs-2" >
									<input type="text" id="diasefec" class="form-control" name="txtDiasActividad" readonly/>
								</div>															
							</div>
						</div>
					<div class="clearfix"></div>
				</div>				
				<div class="modal-footer">
					<button  type="button" class="btn btn-primary active" id="btnGuardarCapturado"><i class="fa fa-floppy-o"></i> Guardar</button>
					<button  type="button" class="btn btn-default" id="btnCancelarCapturado"><i class="fa fa-undo"></i> Cerrar</button>	
				</div>
			</div>
		</div>
	</div>

	<div id="modalActividad" class="modal fade" role="dialog" style="z-index: 1600">
		<div class="modal-dialog">
			<input type='HIDDEN' name='txtObjetoNuevo' value=''>
			<input type='HIDDEN' id="Aparta" name='txtAparta' value=''>									
			<!-- Modal content-->
			<div class="modal-content"  style="width: 145% ! important; margin: 0px 0px 0px -119px ! important;">									
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h3 class="modal-title"><i class="fa fa-home"></i> Capturando...</h3>
				</div>									
				<div class="modal-body">
						<form id="formulario" METHOD='POST' role="form">
								<input type='HIDDEN' name='txtOperacion' value=''>
								<input type="HIDDEN" name="txtUsuario" value='<?php echo $_SESSION["idUsuario"];?>'>
								<input type='HIDDEN' name='txtCuenta' value='<?php echo $_SESSION["idCuentaActual"];?>'>						
								<input type='HIDDEN' name='txtPrograma' value='<?php echo $_SESSION["idProgramaActual"];?>'>
								<input id="audito" type="HIDDEN" name="txtaudi" value=''>
								<input type='HIDDEN' name='txtFaseActividadApartado' value='PLANEACION'>
								
								<input type="HIDDEN" id="Apartado2" name="txtApartado2" value=''>

								<div class="col-xs-12" >
									<div class="form-group">

										<!--<label class="col-xs-2 control-label">ID</label>
										<div class="col-xs-1">
											<input type="text" class="form-control" id="IDActividad" name="txtIDActividad" readonly/>
										</div>

										<label class="col-xs-1 control-label">Fase</label>
										<div class="col-xs-2">
											<select class="form-control" id="faseActividadApartado" name="txtFaseActividadApartado">
												<option value="" Selected>Seleccione...</option>
											</select>
										</div>-->

										<label class="col-xs-2 control-label">Apartado</label>
										<div class="col-xs-5">
											<select class="form-control" id="Apartado" name="txtApartado" onchange="validarapartado(this.value);">
												<option value="" Selected>Seleccione...</option>
											</select>
										</div>

								</div>						
								
								<br>								
									<div class="form-group">
										<label class="col-xs-2 control-label">Actividad<i class="fa fa-pencil" id="txtActividadEdit"></i></label>
									 	<div class="col-xs-10">
									 	
									 	<textarea  name="txtDescripcionActividad" id="DescripcionActividad" style="margin: 2px -10px 0 !important; display:inline;"></textarea>

									 	<!--<textarea name="txtDescripcionActividad2" id="DescripcionActividad2" style="margin: 2px -10px 0 !important; display:inline;"></textarea>-->
									 	
										</div>

									</div>	
									<div class="form-group" id="Complementaria" style="display: none">	
										<label class="col-xs-2 control-label">Actividad Complementaria<i class="fa fa-pencil"  id="txtActividadComEdit"></i></label>
									 	<div class="col-xs-10">
									 	<textarea class="form-control" rows="3" placeholder="Actividad" maxLength= "8000" name="txtDescripcionActividadCom" id="DescripcionActividadCom" style="resize:none;margin: 0 0 1% 0 !important;"></textarea>						
										</div>
									</div>
								<br>	

									<!--<div class="form-group" id="divActividadPrevia" style="display:'inline';">							
										<label class="col-xs-2 control-label">Actividad Previa</label>
										<div class="col-xs-10">
											<select class="form-control" name="txtPreviaActividad"  id="txtPreviaActividad">
												<option value="">Seleccione...</option>
												
											</select>
										</div>
										<br>
										<br>
									</div>-->						
																										
									<!--<div class="form-group">								
										<label class="col-xs-2 control-label">Fecha Inicio</label>
										<div class="col-xs-2">
											<input type="text" id="finicio" class="form-control" name="txtInicioActividad" onkeydown="return false;"/>
										</div>	
										<label class="col-xs-2 control-label">Fecha Término</label>
										<div class="col-xs-2">
											<input type="text" id="fFin" class="form-control" name="txtFinActividad" onchange="getDiasLaborale(finicio.value,fFin.value);" onkeydown="return false;"/>
										</div>		
										<label class="col-xs-2 control-label">Dias Efectivos</label>
										<div class="col-xs-2">
											<input type="text" id="diasefec" class="form-control" name="txtDiasActividad" readonly style="margin: 0 0 6% !important;"/>
										</div>															
									</div>-->						
									
									<!--<br>
									<div class="form-group">								
										<label class="col-xs-2 control-label">Porcentaje</label>
										<div class="col-xs-1">
											<input type="range"  id="porcen" class="form-control" name="txtPorcentajeActividad"  min="0" max="100" style="width: 220% !important;"/>
										</div>	
										<label class="col-xs-3 control-label">Prioridad</label>
										<div class="col-xs-2">
											<select class="form-control" id="PrioridadActividad" name="txtPrioridadActividad">
												<option value="">Seleccione...</option>
												<option value="BAJA">BAJA</option>
												<option value="MEDIA" SELECTED>MEDIA</option>
												<option value="ALTA">ALTA</option>
											</select>
										</div>															
										<label class="col-xs-2 control-label">Impacto</label>
										<div class="col-xs-2">
											<select class="form-control" id="tImpactoActividad" name="txtImpactoActividad" style="margin: 0 0 6% !important;">
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
											<select class="form-control" id="ResponsableActividad" name="txtResponsableActividad" >
												<option value="" selected>Seleccione...</option>
											</select>
										</div>																																											
										<label class="col-xs-2 control-label">Estatus</label>
										<div class="col-xs-2">
											<select class="form-control" id="EstatusActividad" name="txtEstatusActividad" style="margin: 0 0 6% !important;">
												<option value="">Seleccione...</option>
												<option value="ACTIVO" SELECTED>ACTIVO</option>
												<option value="INACTIVO">INACTIVO</option>
											</select>
										</div>								
									</div>	
									<br>
									<div class="form-group">								
										<label class="col-xs-2 control-label">Notas</label>
										<div class="col-xs-10"><textarea class="form-control" rows="3" placeholder="Nota(s)" id="NotasActividad" name="txtNotasActividad"></textarea></div>							
									</div>-->	
									<br>
								</div>										
							</form>
					<div class="clearfix"></div>
				</div>				
				<div class="modal-footer">
					<button  type="button" class="btn btn-primary btn-xs active" id="btnGuardarActividad" ><i class="fa fa-undo"></i> Guardar Actividad</button>	
					<button  type="button" class="btn btn-default btn-xs" id="btnCancelarActividad" 	><i class="fa fa-undo"></i> Regresar</button>
				</div>
			</div>
		</div>
	</div>

<!--<form id="formulario" METHOD='POST' role="form">-->
	
	<input type='HIDDEN' name='txtOperacionEquipo' value=''>
	<input type='HIDDEN' name='txtOperacionObjeto' value=''>
	<input type='HIDDEN' name='txtId' value=''>
	<!--<input type='HIDDEN' name='txtCuenta' value=''>-->						
	
	<div id="modalCronograma" class="modal fade" role="dialog">
		<div class="modal-dialog">					
				<!-- Modal content-->
			<div class="modal-content">									
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<div class="panel-heading">
						<div   id="titCedula" class="pull-left"></div>
							<div class="clearfix"></div>
						</div>
					<!--<h2 class="modal-title"><i class="fa fa-calendar"></i> Calendario de trabajo...</h2>-->
				</div>									
				<div class="modal-body" style="height: 355px; overflow: auto; overflow-x:hidden;">				
					<div class="table-responsive" id="divListaActividades">
						<table class="table table-striped table-bordered table-hover">
						  <thead>
							<tr><th>Fase</th><th>Fecha Inicio</th><th>Fecha Fin</th><th>Dias Efectivos</th></tr>
						  </thead>
						  <tbody id="tablaActividades" >								  							
							</tbody>

							
						</table>											
					</div>

					<div class="table-responsive" id="divListaApartado">
						<table class="table table-striped table-bordered table-hover">
						  <thead>
							<tr><th>Modificar</th><th>Apartado</th><th>Descripción</th></tr>
						  </thead>
						  <tbody id="tablaApartado" >								  							
							</tbody>
						</table>											
					</div>					
						



					<div class="clearfix"></div>
				</div>				
				<div class="modal-footer">
					<button type="button" class="btn btn-primary  btn-xs" 	id="btnNuevaActividad"><i class="fa fa-file-text-o"></i> Nueva Apartado...</button>
					<button type="button" class="btn btn-primary  btn-xs" 	id="btnNuevaFecha"><i class="fa fa-file-text-o"></i> Nueva Fecha</button>
					<!--<button  type="button" class="btn btn-primary btn-xs active" id="btnGuardarActividad" style="display:none;"><i class="fa fa-undo"></i> Guardar Actividad</button>	
					<button  type="button" class="btn btn-default btn-xs" id="btnCancelarActividad" 		style="display:none;"><i class="fa fa-undo"></i> Regresar</button>-->


					<button  type="button" class="btn btn-default btn-xs" id="btnCancelar2Actividad" 		style="display:none;"><i class="fa fa-undo"></i> Cancelar</button>								
					<button  type="button" class="btn btn-default btn-xs" id="btnCancelarCronograma"><i class="fa fa-undo"></i> Salir</button>
				</div>
			</div>		
		</div>
	</div>


	<div id="modalConfronta" class="modal fade" role="dialog">
		<div class="modal-dialog">							

			<div class="modal-content">									
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h2 class="modal-title"><i class="fa fa-users"></i> Confronta...</h2>
				</div>

				<div class="modal-body" style="overflow: auto; overflow-x:hidden;">
					<div class="form-group">
						<label class="col-xs-2 control-label">Unidad</label>
						<div class="col-xs-10">
							<select class="form-control" name="txtUnidad" id="ConUnidad" onchange="llenarConfronta(auditoria.value,this.value);">
								<option value="" selected>Seleccione...</option>
							</select>
						</div>
					</div>
					<br>
					<div class="form-group">
						<label class="col-xs-1 control-label confrontaxs1"> Fecha Confronta</label>
						<div class="col-xs-2 confrontaxs2"><input type="text" style="resize:none; margin: 0 0 6px 0 !important;" onkeydown="return false;" name="txtFConfronta" onchange="validarfechaConfronta(FConfronta.value,FIRA.value,FIFA.value);" readonly="" class="form-control hasDatepicker" id="FConfronta"></div>
						<div style="width:1% !important;" class="col-xs-1"><button onclick="modificarfeconfro();" style="display: inline;" id="modconfro" type="button"><i class="fa fa-pencil-square-o"></i></button></div>
						<label class="col-xs-1 control-label confrontaxs1"> Hora Confronta</label>
						<div class="col-xs-2 confrontaxs2">

						<input type="time" required="required" pattern="([0-1]{1}[0-9]{1}|20|21|22|23):[0-5]{1}[0-9]{1}"  name="txtHConfronta" class="form-control hasDatepicker" id="HConfronta" placeholder="00:00" title="Formato de 24 horas 00:00">

						</div>

						<!--<input type="time" id="hRecepcion" name="hRecepcion" required="required" pattern="([0-1]{1}[0-9]{1}|20|21|22|23):[0-5]{1}[0-9]{1}" placeholder="00:00" title="Formato de 24 horas 00:00" class="form-control">-->



						<label class="col-xs-1 control-label confrontaxs1"> Fecha de IRAC</label>
						<div class="col-xs-2 confrontaxs2"><input type="text" style="resize:none; margin: 0 0 6px 0 !important;" onkeydown="return false;" name="txtFNotifica" readonly="" class="form-control" id="FNotifica">
						</div>

						<label style="width: 14.3% !important;" class="col-xs-2 control-label confrontaxs1"> Fecha de IFA</label>
						<div class="col-xs-2 confrontaxs2"><input type="text" style="resize:none; margin: 0 0 6px 0 !important;" onkeydown="return false;" name="txtFIfa" readonly="" class="form-control" id="FIfa">
						</div>
						
					</div>
					<!--<div class="form-group">
						<label class="col-xs-3 control-label" style="margin: 2% 0 0 -49% !important;">Resumen Confronta <i class="fa fa-pencil"  id="txtReConfronEdit"></i></label>
						<div class="col-xs-9" style="margin: 0 0 0 23% !important;"><textarea class="form-control" rows="5" placeholder="Resumen Confronta" id="txtReConfron" name="txtReConfron" style="resize:none; width: 112%; margin: -4% 0 0 -9% !important;"></textarea></div>	
					</div>-->
				</div>
				
				<div class="modal-footer">
					<button  type="button" class="btn btn-primary btn-xs active" 	id="btnGuardarConfronta" 		style="display:none;"><i class="fa fa-floppy-o"></i> Guardar</button>	
					<button  type="button" class="btn btn-default btn-xs" 			id="btnCancelarConfronta" 		style="display:inline;"><i class="fa fa-undo"></i> Salir</button>	
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
					<h2 class="modal-title"><i class="fa fa-users"></i> Personal Comisionado...</h2>
				</div>									
				<div class="modal-body" style="height: 350px; overflow: auto; overflow-x:hidden;">
					<table class="table table-striped table-bordered table-hover table-condensed">
						<caption>Lista de Personal</caption>
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


	<div id="modalLocalizacion" class="modal fade" role="dialog">
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
								<div class="col-xs-12">
									<div id="canvasMapa" ></div>
								</div>								
							</div>
						</div>
					<div class="clearfix"></div>
				</div>				
				<div class="modal-footer">
					<div id="divIndicador" class="pull-left" style="display:none"><img src="img/giphy.gif"></div>
					<div id="divBotones" class="pull-right">
						<button  type="button" class="btn btn-warning active" id="btnGuardarLoc"><i class="fa fa-floppy-o"></i> Autorizar</button>	
						<button  type="button" class="btn btn-default" id="btnCancelarLoc"><i class="fa fa-undo"></i> Cancelar</button>	
					</div>
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
					<form id="formularioComentarios" METHOD='POST' ACTION='/guardar/auditoriaComentario' role="form">
						<input type='HIDDEN' name='txtComentarioAnterior' value=''> 
						<input type='HIDDEN' name='txtOperacionComentarios' value=''>
						<input type='HIDDEN' name='txtIdAuditoriaComentario' value=''> 

						<div class="form-group">	
							<div class="table-responsive" style="height: 300px; overflow: auto; overflow-x:hidden;" id="divListaComentarios">
								<table class="table table-striped table-bordered table-hover table-condensed table-responsive" >
									<caption><h3>Comentarios Registrados</h3></caption>
									<thead>
										<tr><th width="15%">Fase</th><th width="15%">Fecha/Hora</th><th width="15%">Usuario</th><th width="60%">Comentario</th></tr>
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

	<div id="modalFlotante" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<form id="formulariom" METHOD='POST' action='' role="form">								
				<input type='HIDDEN' name='operacion' value=''>
				<input type='HIDDEN' name='txtID' value='10 '>
				<input type='HIDDEN' name='txtParametro1' value=''>
				<input type='HIDDEN' name='txtParametro2' value=''>
				<input type='HIDDEN' name='txtParametro3' value=''>		

				
				<!-- Modal content-->
				<div class="modal-content">

					
					<div class="modal-body">
						<div class="container-fluid">
							<div class="row">					
										<br>
										<div class="form-group">
											<label class="col-xs-3 control-label">Id</label>
											<div class="col-xs-1">
												<input type="text" class="form-control" name="txtID"  readonly />
											</div>
											<label class="col-xs-2 control-label">Módulo</label>
											<div class="col-xs-6">
												<input type="text" class="form-control" name="txtModulo"  readonly />
											</div>																
										</div>
										<br>
										<div class="form-group">
											<label class="col-xs-3 control-label">Reporte</label>
											<div class="col-xs-9">
												<input type="text" class="form-control" name="txtNombre" readonly/>
											</div>															
										</div>

										<div class="form-group" id="PARAM1" style="display:none;">
										<br>															
											<label class="col-xs-3 control-label"  id="PARAM1-LBL"></label>
											<div class="col-xs-9"  id="PARAM1-CTRL"></div>															
										</div>															


										<div class="form-group" id="PARAM2" style="display:none;">
										<br>															
											<label class="col-xs-3 control-label"  id="PARAM2-LBL">Param2</label>
											<div class="col-xs-9"  id="PARAM2-CTRL"></div>															
										</div>

										<div class="form-group" id="PARAM3" style="display:none;">
										<br>															
											<label class="col-xs-3 control-label"  id="PARAM3-LBL">Param3</label>
											<div class="col-xs-9"  id="PARAM3-CTRL"></div>															
										</div>															

							</div>
							</div>
					</div>
						<!--   COMENTARIOS  : AQUI VA EL FOOTER MODAL -->
					<div class="modal-footer">
						<button type="button" class="btn btn-primary active" id="btnGenerar">Generar Informe</button>	
						<button onclick="$('#modalFlotante').modal('hide');" type="button" class="btn btn-default">Cancelar</button>	

					</div>
				</div>
				
			</form>
		</div>
	 </div>
<!--</form>-->

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