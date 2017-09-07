<!DOCTYPE html>
<!-- saved from url=(0035)http://ashobiz.asia/mac52/macadmin/ -->
<html lang="es"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=Edge" />  
 <meta charset="utf-8">
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.1/themes/base/jquery-ui.css" />
<script type="text/javascript" src="js/canvasjs.min.js"></script>
<script type='text/javascript' src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js?ver=3.1.2"></script> 
<script type="text/javascript" src="js/genericas.js"></script>
<script type="text/javascript" src="js/yo.js"></script>		
<script src="https://maps.googleapis.com/maps/api/js?sensor=false&libraries=geometry,places&ext=.js"></script>
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>
<script src="ckeditor/ckeditor.js" charset="UTF-8"></script>
<link rel="stylesheet" type="text/css" href="css/jquery-ui-1.7.2.custom.css" />
<script src="jquery.ui.datepicker-es.js"></script>

<style type="text/css">		
	@media screen and (min-width: 768px) {
		#mapa_content {width:100%; height:150px;}
		#canvasMapa {width:100%; height:350px;}
		#canvasJD, #canvasDIP{height:175px; width:100%;}
		#canvasJG{height:175px; width:100%;}			
		#modalCronograma .modal-dialog  {width:70%;}
		#modalConfronta .modal-dialog  {width:70%;}
		#modaltrabajofase .modal-dialog  {width:70%;}
		#modalEquipo .modal-dialog  {width:70%;}
		#modalComentarios .modal-dialog  {width:70%;}		
		#modalDocto .modal-dialog  {width:60%;}
		#modalAutorizar .modal-dialog  {width:30%;}
		#modalLocalizacion .modal-dialog  {width:60%;}
		#modalCapturado{width: 100%;}
		#modalActividad{width: 100%;}
		#modalTextoLargo2{width: 80%}

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
					//alert("Response: " + response);
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
					}else{
						document.all.txtInicioActividad.value='' + obj.inicio;
					}

					if(obj.inicio=='1900-01-01'){
						document.all.txtFinActividad.value='';
					}else{
						document.all.txtFinActividad.value='' + obj.fin;
					}

					document.all.txtDiasActividad.value='' + obj.defec;
				},
				error: function(xhr, textStatus, error){
					alert('statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
				}			
			});			
		}

		function modificarapartado(audi,apar){
			$.ajax({
				type: 'GET', url: '/modifapartado/' + audi + '/' + apar ,
				success: function(response) {			
					var obj = JSON.parse(response);
					//alert("Response: " + response);
					document.all.txtOperacion.value='UPD';
					$('#modalActividad').modal('show');
					var aparta = obj.apartado;
					//alert("Apartado:  " + aparta);
					document.getElementById('Apartado2').value = aparta;
					document.getElementById('audito').value = obj.auditoria;

					document.all.txtApartado.readOnly=true;
					seleccionarElemento(document.all.txtApartado, obj.apartado);
					//seleccionarElemento(document.all.txtFaseActividadApartado, obj.fase);	
					document.all.txtDescripcionActividad.value='' + obj.actividad;
				},
				error: function(xhr, textStatus, error){
					alert('statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
				}			
			});			
		}
		
	//recuperar secciones	
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

						var apar = 	dato.apartado;

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

	//asignar
		//Lider
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

		//checkbox
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

	//recuperar
	

		function recupeFases(lista, valor, tbl){
			
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
					alert('function recupeAuditores ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
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
					alert('function recuperarTabla ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
				}			
			});	
		}

		function recuperaAuditoria(id){
			proceso = 'RP';
			$.ajax({
				type: 'GET', url: '/lstAuditoriaByrp/' + id + '/' + proceso ,
				success: function(response) {
					var obj = JSON.parse(response);
					var respon = obj.responsable;
					var sunres = obj.subresponsable;
					

					if(respon == ''){
						document.getElementById('resp').disabled = false;
						document.getElementById('menudesple').style.display='none';
					}else{
						document.getElementById('resp').disabled = true;
						document.getElementById('menudesple').style.display='inline';
						
					}

					if(sunres == ''){
						document.getElementById('arsubre').disabled = false;
						
					}else{
						document.getElementById('arsubre').disabled = true;	
					}

					limpiarCamposAuditoria();
					limpiarapartados();

					$('#FAInicio').datepicker("option", "disabled", true);
					$('#FAFinal').datepicker("option", "disabled", true);
					$('#FIRA').datepicker("option", "disabled", true);
					$('#FIFA').datepicker("option", "disabled", true);
					document.all.txtCuenta.value='' + obj.cuenta;
					document.all.txtPrograma.value='' + obj.programa;
					document.all.txtAuditoria.value='' + obj.auditoria;
					document.all.txtclaveAuditoria.value='' + obj.clave;
					document.getElementById('TipoObs').disabled=true; 
					document.getElementById('txtObserva').disabled=true;
					
					recupeApartados(obj.auditoria);
					
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
					
					

					document.all.txtObjetivo.value='' + obj.objetivo;
					recuperarTabla('tblActividadesByAuditoria', obj.auditoria, document.all.tablaActividades);
					document.all.listasAuditoria.style.display='none';
					document.all.capturaAuditoria.style.display='inline';
					
					recuperarLista('lstFasesActividad', document.all.txtFaseActividad);
					recuperarLista('lstApartados', document.all.txtApartado);
					recuperarListaLigada('lstResponByauditor', obj.auditoria, document.all.txtResponsableActividad);
					recupeFases('lstauditoriaByFases', obj.auditoria, document.all.tablaFases);
					proximaEtapa(obj.proceso, obj.etapa);

			},
				error: function(xhr, textStatus, error){
					alert('function recuperaAuditoria()   -> statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error + ' Auditoría: ' + id);
				}			
			});		
		}

		function recupeApartados(auditoria){

			var lstApartadosrep = new Array();
			$.ajax({
				type: 'GET', url: '/lstApartadosrep/' + auditoria ,
				success: function(response) {
					var jsonData = JSON.parse(response);
					var apa = 0;
					apa = jsonData.datos;

					
						

					if(apa == ''){
						var editor = CKEDITOR.instances["idPEA"];
						if (editor) { editor.destroy(true);}

						var editor1 = CKEDITOR.instances["idMuestra"];
						if (editor1) { editor1.destroy(true);}

						var editor2 = CKEDITOR.instances["idPruebas"];
						if (editor2) { editor2.destroy(true);}

						var editor3 = CKEDITOR.instances["idProcedimiento"];
						if (editor3) { editor3.destroy(true);}

						var editor4 = CKEDITOR.instances["idObservacion"];
						if (editor4) { editor4.destroy(true);}
					
					}else{
							
						for(var i=0;i<apa.length;i++){
							lstApartadosrep[i] = new Array(apa.length);
							lstApartadosrep[i] = jsonData.datos[i];
						
							var editor = CKEDITOR.instances["idPEA"];
							if (editor) { editor.destroy(true); }
							if(lstApartadosrep[i]=lstApartadosrep[0])
							{
								var enca = lstApartadosrep[i].nombre;
								document.all.txtlbEstudio.value='' +"1.- "+ enca;
								var estudio = lstApartadosrep[i].actividad;
								document.all.txtEstudio.value ='' + estudio;
								CKEDITOR.replace('idPEA',{
								    on: {
								        change: function( evt ) {
								            CKEDITOR.document.getBody();
								        }
								        
								    }
								});
							}
						}

						for(var i=0;i<apa.length;i++){
							var editor = CKEDITOR.instances["idMuestra"];
							if (editor) { editor.destroy(true); }
							lstApartadosrep[i] = new Array(apa.length);
							lstApartadosrep[i] = jsonData.datos[i];
							
							if(lstApartadosrep[i]=lstApartadosrep[1])
							{ 
								var enca = lstApartadosrep[i].nombre;
								document.all.txtlbMuestra.value='' +"2.- "+ enca;
								var muestras = lstApartadosrep[i].actividad;
								document.all.txtMuestra.value ='' + muestras;
								CKEDITOR.replace('idMuestra',{
								    on: {
								        change: function( evt ) {
								            CKEDITOR.document.getBody();
								        }
								        
								    }
								});
							}
						}

						for(var i=0;i<apa.length;i++){
							var editor = CKEDITOR.instances["idPruebas"];
							if (editor) { editor.destroy(true); }

							lstApartadosrep[i] = new Array(apa.length);
							lstApartadosrep[i] = jsonData.datos[i];
							
							if(lstApartadosrep[i]=lstApartadosrep[2])
							{ 
								var enca = lstApartadosrep[i].nombre;
								document.all.txtlbPruebas.value='' +"3.- "+ enca;
								var pruebas = lstApartadosrep[i].actividad;
								document.all.txtPruebas.value ='' + pruebas;
								CKEDITOR.replace('idPruebas',{
								    on: {
								        change: function( evt ) {
								            CKEDITOR.document.getBody();
								        }
								        
								    }
								});
							}
						}
						
						for(var i=0;i<apa.length;i++){
							var editor = CKEDITOR.instances["idProcedimiento"];
							if (editor) { editor.destroy(true); }
							lstApartadosrep[i] = new Array(apa.length);
							lstApartadosrep[i] = jsonData.datos[i];
							
							if(lstApartadosrep[i]=lstApartadosrep[3])
							{ 
								var enca = lstApartadosrep[i].nombre;
								document.all.txtlbProced.value='' +"4.- "+ enca;
								var procedi = lstApartadosrep[i].actividad;
								document.all.txtProcedimiento.value ='' + procedi;
								CKEDITOR.replace('idProcedimiento',{
								    on: {
								        change: function( evt ) {
								            CKEDITOR.document.getBody();
								        }
								        
								    }
								});
							}
						}

						for(var i=0;i<apa.length;i++){
							var editor = CKEDITOR.instances["idObservacion"];
							if (editor) { editor.destroy(true); }
							lstApartadosrep[i] = new Array(apa.length);
							lstApartadosrep[i] = jsonData.datos[i];
							
							if(lstApartadosrep[i]=lstApartadosrep[4])
							{ 
								var enca = lstApartadosrep[i].nombre;
								document.all.txtlbObserva.value='' +"5.- "+ enca;
								var observa = lstApartadosrep[i].actividad;
								document.all.txtObservacion.value ='' + observa;
								CKEDITOR.replace('idObservacion',{
								    on: {
								        change: function( evt ) {
								            CKEDITOR.document.getBody();
								        }
								        
								    }
								});
							}else{
								//alert("entra aqui..");
							}
						}
					}

				
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
			}else{		
				$.ajax({ 
					type: 'GET', url: '/lstAudiUnidades/' +  audi +'/' + valor,
		          	success: function(response) {
		          		var obj = JSON.parse(response);
		          		document.all.modconfro.style.display='inline';
		            	document.all.txtFConfronta.value='' +obj.FeCon;
		            	document.all.txtFNotifica.value='' + obj.irac;
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
	            },
		  			error: function(xhr, textStatus, error){
						alert('function llenarConfronta()   -> statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error + ' Auditoría: ' + audi+ ' ' + valor);
					}
	    	});
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
			document.all.txtFNotifica.value='';
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

		function limpiarapartados(){
			document.all.txtEstudio.value='';
			document.all.txtMuestra.value='';
			document.all.txtPruebas.value='';
			document.all.txtProcedimiento.value='';
			document.all.txtObservacion.value='';
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
					alert('Wine created successfully');
					$('#btnDelete').show();
					$('#wineId').val(data.id);
				},
				error: function(jqXHR, textStatus, errorThrown){
					alert('addWine error: ' + textStatus);
				}
			});
		}

		function formatDate(value){
			//alert(value);
			return value.getDate()+1  + "-" + value.getMonth() + "-" +  value.getYear();
		}

		function formatDate2(value){
			//alert(value);
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
						//alert("Response: " + response);
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
										//alert("Encontrado: " + fTmp1);
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
			//alert("sumarDias: fecha: " + fecha + " dias: " + dias);
			//var d = new Date(fecha);
			
 			fecha.setDate(fecha.getDate() + dias);
  			//alert("segundo:  " +formatDate2(d));
  			//alert(formatodia(d));
  			//alert("fecha.setDate(fecha.getDate() + dias);  " + fecha)
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
				//alert("valor negativo");
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
				//alert("valor positivo");
				var feifa = sumarDias(f3,dias);
				//alert("fecha despues del feifa: " + formatDate2(feifa));
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
									//alert("Encontrado: " + fTmp1);
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
					//alert(fInicio + "  -> " + fFin  + " = " + lstfines.length + " Dias efectivos");
					sCadena = sCadena + "\n******************************\nDIAS EFECTIVOS EN TOTAL:" + lstfines.length ; 
					//alert(sCadena);

				},
				error: function(xhr, textStatus, error){
					alert('ERROR: En function  getDiasLaborale(lista, mapa)  ->  statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
				}			
			});
		

			fines = arrprue.length;

			finha = arrecon.length;
			//alert("arrecon.length:  "  + arrecon.length);
			total = (parseInt(fines) + parseInt(arrecon.length));
			//alert(" finha: " +finha +" arrprue: " + fines + " arrecon: " +  arrecon.length +" total: " + total);

			console.log(total);
			return total;
		}

		window.onload = function () {
			var chart1; 
			setGrafica(chart1, "dpsAuditorias", "pie", "Auditorias", "canvasJG" );
			inicializar();
		};

		function dessub(){
			document.getElementById('arsubre').disabled=false;
			
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
			sValor = d.txtAuditoria.value + '|' + sector + '|' + subsector + '|' + uni + '|' + d.txtFConfronta.value;
		
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
		

		function guardarFecha(){
			var sValor="";
			var sOper=document.all.txtOpera.value;
			var d = document.all;
			var dias1 = 11;
			var dias2 = -10;
			var feIFA;
			var fin = document.all.txtFinActividad.value

			var feIFA = getDias(fin, dias1);
			var sum = ((feIFA + dias1)+2);
			var f3 = new Date(fin);
			var final = sumarDias(f3,sum);
		
			if(final.getDay()!=0 && final.getDay()!=6){
				var final2 = sumarDias(final,3);
				FIFA = formatDate2(final2);
				//alert("final: 1 -- " + FIFA);
				//alert("dentro del if: "+ formatDate2(final));
			}else{
				//alert("dentro del else: "+ formatDate2(final));
				var fifecha = sumarDias(final,3);
				//alert("fifecha: "+ formatDate2(fifecha));
				FIFA = formatDate2(fifecha);
				//alert("final: 2 -- " + FIFA);
			}


			var feIRAC = getDias(fin,dias2);
			//alert("feIRAC: " +feIRAC);
			var sumirac = ((feIRAC + 11));
			var f4 = new Date(fin);
			var finirac = sumarDias(f4,-sumirac);
			
			if(finirac.getDay()!=0 && finirac.getDay()!=6){
				var finirac2 = sumarDias(finirac,-1);
				FIRAC = formatDate2(finirac2);
			}else{
				//alert("dentro del else:  "  + formatDate2(finirac));
				var finirac2 = sumarDias(finirac,1);
				//alert("fifeirac: " + formatDate2(fifeirac));
				FIRAC = formatDate2(finirac2);
			}

			
			sValor = d.txtCuenta.value + '|' + d.txtAuditoria.value + '|' + d.txtPrograma.value + '|' + d.txtFaseActividad.value + '|' + d.txtInicioActividad.value + '|' +  d.txtFinActividad.value + '|' + d.txtDiasActividad.value + '|' + FIFA + '|' + FIRAC + '|' + sOper;

			//alert(sValor);
			//alert(sOper);
			$.ajax({
				type: 'GET', url: '/guardar/fecha/' + sValor,
				success: function(response) {
					
					alert("Los datos se guardaron correctamente");
					$("#modalCapturado").modal('hide');
					recuperarTabla('tblActividadesByAuditoria', document.all.txtAuditoria.value, document.all.tablaActividades);
					RecuperaConfronta('tlbaudiuni',document.all.txtAuditoria.value, document.all.tablaConfronta);
					recuperaAuditoria(document.all.txtAuditoria.value);
					
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
			alert(apartado);
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

	  	function formatodia(valor){
	 	 	var fechafin;
	 	 	//alert("formatodia:  " + valor);
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
	 	 	//alert("formatodia:  " + valor);
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
		        //alert(dtFechaFinal);
		   
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
	 			//alert(dtFechaActual);
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

					if(Date.parse(Feira) >= Date.parse(obj.fin)){
						alert("Estas ingresando una fecha mayor a la fecha Final de Ejecución.");
						var fechaIRAC = formatodia2(obj.IRA);
						return false;
					}
		 		   return true;
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
			 	 	alert("valiIFA:  "  + valiIFA);
			 	 	var valor = valfeIFA(valiIFA);
			 	 	alert("veamos:" + valor);


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
		
		function recuperarTablaComentarios(lista, valor, tbl){
    	 //alert('/'+ lista + '/' + valor);
    		var seccion = 'RP';
    		var seccion2 = 'AUDI'
	        $.ajax({ type: 'GET', url: '/'+ lista + '/' + valor + '/' + seccion + '/' + seccion2 ,
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
	
	//fecha
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
				//defaultdate:'2016-07-07',
				beforeShowDay: $.datepicker.noWeekends,
				onClose: function ( selectedDate ) {
				 	$( "#fFin" ).datepicker( "option", "minDate", selectedDate);
				}
			});

			$("#fFin").datepicker({
				minDate: null,
		    	maxDate: null,
				dateFormat:'yy-mm-dd',
				firstDay: 1,
				numberOfMonths: 2,
				beforeShowDay: $.datepicker.noWeekends 
			});

			$("#FAInicio").datepicker({
				minDate: null,
		    	maxDate: null,
				dateFormat:'dd-mm-yy',
				firstDay: 1,
				numberOfMonths: 2,
				beforeShowDay: $.datepicker.noWeekends,
				onClose: function( selectedDate ) {
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

			/*$('body').mouseover(function(){
				setTimeout('location.href="./cerrar"',1200000);
			});*/

			//document.all.divActividadPrevia.style.display='inline';
			//document.all.divActividadPrevia.style.display='none';
			
		
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
					sValores = sValores + '|RP';

					alert("Los valores a guardar son: TipoOperacion|Auditoria|AuditoriaComentario|comentario|prioridad|estatus=>" + sValores);

					$.ajax({ type: 'GET', url: '/guardar/auditoriaComentario/' + sValores ,
						success: function(response) {
							sRespuesta = response.replace(/^\s+/g,'').replace(/\s+$/g,'');

							if(sRespuesta == "OK"){
								recuperarTablaComentarios('tblComentariosByAuditoriaspea', document.all.txtAuditoria.value, document.all.tblListaComentarios);
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

			$( "#btnCancelarComentario" ).click(function() {		
				$( "#modalComentarios" ).modal('hide'); 
			});

		
			$("#btnENVIADA").click(function(){apagarBotones();asignarAutorizacionEtapa(document.all.txtAuditoria.value, sProcesoNuevo, sEtapaNueva);});
			$("#btnAPROBADA").click(function(){apagarBotones();asignarAutorizacionEtapa(document.all.txtAuditoria.value, sProcesoNuevo, sEtapaNueva);});
			$("#btnVOBO").click(function(){apagarBotones();asignarAutorizacionEtapa(document.all.txtAuditoria.value, sProcesoNuevo, sEtapaNueva);});
			$("#btnAUTORIZADA").click(function(){apagarBotones();asignarAutorizacionEtapa(document.all.txtAuditoria.value, sProcesoNuevo, sEtapaNueva);});


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

				document.getElementById('audito').value = document.getElementById('auditoria').value;
				//document.all.divCapturaActividad.style.display='inline';
				document.all.txtOperacion.value='INS';
				document.all.btnNuevaActividad.style.display='inline';
				//document.all.btnGuardarActividad.style.display='none';
				//document.all.btnCancelarActividad.style.display='none';
				$('#modalActividad').modal('show');
				document.all.btnCancelar2Actividad.style.display='none';
				document.all.btnCancelarCronograma.style.display='inline';
				limpiarCamposActividad();
			});

			$( "#btnNuevaFecha" ).click(function() {

				$('#modalCapturado').modal('show');
				document.all.txtFaseActividad.disabled=false;
				document.all.txtOpera.value='INS';

				/*document.all.btnCancelar2Actividad.style.display='none';
				document.all.btnCancelarCronograma.style.display='inline';*/
				limpiarCamposActividad();
			});

			$( "#btnGuardarActividad" ).click(function() {		
				var url = "croactivi.php";
				var dataString = $('#formulario').serialize();
				alert('Datos serializados3.1416: '+dataString);
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
			


			$( "#btnGuardarCapturado" ).click(function() {
				guardarFecha();
			});

			$( "#btnCancelarCapturado" ).click(function() {
				$('#modalCapturado').modal('hide');			
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
				window.location.reload(true);					
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


			
			$( "#btnCancelarLoc" ).click(function() {
				$('#modalLocalizacion').modal('hide');
			});

			$( "#txtObjetivoEdit" ).click(function(){
				document.all.txtTextoAmplio.value = document.all.txtObjetivo.value;
				$('#modalTextoLargo').modal('show');
			});	
			
			$( "#txtAlcanceEdit" ).click(function(){
				document.all.txtTextoAmplio.value = document.all.txtAlcance.value;
				$('#modalTextoLargo').modal('show');
			});		

			$( "#txtJustificacionEdit" ).click(function(){
				document.all.txtTextoAmplio.value = document.all.txtJustificacion.value;
				$('#modalTextoLargo').modal('show');
			});	

			$( "#txtReConfronEdit" ).click(function(){
				document.all.txtTextoAmplio2.value = document.all.txtReConfron.value;
				$('#modalTextoLargo2').modal('show');
			});

			$( "#txtActividadEdit" ).click(function(){
				document.all.txtTextoAmplio2.value = document.all.txtEstudio.value;
				$('#modalTextoLargo2').modal('show');
				var editor = CKEDITOR.instances["txtTextoAmplio2"];
				if (editor) { editor.destroy(true); }
				CKEDITOR.replace('txtTextoAmplio2');
			});	

			$( "#txtActividadMuestra" ).click(function(){
				document.all.txtTextoAmplio2.value = document.all.txtMuestra.value;
				$('#modalTextoLargo2').modal('show');
				var editor = CKEDITOR.instances["txtTextoAmplio2"];
				if (editor) { editor.destroy(true); }
				CKEDITOR.replace('txtTextoAmplio2');
			});	

			$( "#txtActividadPruebas" ).click(function(){
				document.all.txtTextoAmplio2.value = document.all.txtPruebas.value;
				$('#modalTextoLargo2').modal('show');
				var editor = CKEDITOR.instances["txtTextoAmplio2"];
				if (editor) { editor.destroy(true); }
				CKEDITOR.replace('txtTextoAmplio2');
			});

			$( "#txtActividadProcedimiento" ).click(function(){
				document.all.txtTextoAmplio2.value = document.all.txtProcedimiento.value;
				$('#modalTextoLargo2').modal('show');
				var editor = CKEDITOR.instances["txtTextoAmplio2"];
				if (editor) { editor.destroy(true); }
				CKEDITOR.replace('txtTextoAmplio2');
			});

			$( "#txtActividadObservacion" ).click(function(){
				document.all.txtTextoAmplio2.value = document.all.txtObservacion.value;
				$('#modalTextoLargo2').modal('show');
				var editor = CKEDITOR.instances["txtTextoAmplio2"];
				if (editor) { editor.destroy(true); }
				CKEDITOR.replace('txtTextoAmplio2');
			});
			



			$( "#btnCancelarTexto" ).click(function() {
				$('#modalTextoLargo').modal('hide');				
			});

			$( "#btnCancelarTexto2" ).click(function() {
				$('#modalTextoLargo2').modal('hide');				
			});

			$( "#btnComentarios" ).click(function() {		
				$('#modalComentarios').removeClass("invisible");
				$('#modalComentarios').modal('toggle');
				$('#modalComentarios').modal('show');
				document.all.txtOperacionComentarios.value = 'INS';
				document.all.txtComentarioAuditoria.value = "";
				//var miTipoAuditoria = document.all.txtTipoAuditoria.options[document.all.txtTipoAuditoria.selectedIndex].value;
				var miAuditoria = document.all.txtAuditoria.value;
				recuperarTablaComentarios('tblComentariosByAuditoriaspea', miAuditoria,document.all.tblListaComentarios);
			});	

				
		});


		function apagarBotones(){
			document.all.btnAUTORIZACION.style.display='none';
			document.all.btnENVIADA.style.display='none';
			document.all.btnAPROBADA.style.display='none';
			document.all.btnVOBO.style.display='none';
			document.all.btnAUTORIZADA.style.display='none';	
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
					<div class="col-xs-3"><h2>R.P</h2></a></div>									
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
								<table class="table table-striped table-bordered table-hover table-condensed">
									<thead>  
										<tr style="font-size: xx-small"><th>Cve. Auditoria</th><th>Dirección General</th><th>Sujeto de Fiscalización</th><th>Rubro(s) de Fiscalización</th><th>Tipo de Auditoría</th></tr>
									</thead>
										<tbody>										
											<?php foreach($datos as $key => $valor): ?>
											<tr onclick=<?php echo "javascript:recuperaAuditoria('" . $valor['auditoria'] . "');"; ?> style="width: 100%; font-size: xx-small">							  
												<td width="10%;"><?php echo $valor['claveAuditoria']; ?></td>
												<td width="10%;"><?php echo $valor['nombre']; ?></td>
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
						  <div class="pull-left"><h3><i class="fa fa-pencil-square-o"> </i> Reporte de Planeación</h3></div>
						  <!--<div class="widget-icons pull-right">-->
						  <div class="btn-toolbar pull-right" role="toolbar"> 
							<button type="button" class="btn btn-default  btn-xs" name="btnAUTORIZACION" id="btnAUTORIZACION" style="display:none;"><i class="fa fa-external-link"></i>Autoriza Projecto</button>
							<button type="button" class="btn btn-default  btn-xs" name="btnENVIADA" id="btnENVIADA" style="display:none;"><i class="fa fa-external-link"></i>ENVIAR A UNIDAD</button>

							<button type="button" class="btn btn-default  btn-xs" name="btnAPROBADA" id="btnAPROBADA" style="display:none;"><i class="fa fa-external-link"></i>ENVIAR A CAAAF</button>

							<button type="button" class="btn btn-default  btn-xs" name="btnVOBO" id="btnVOBO" style="display:none;"><i class="fa fa-external-link"></i>ENVIAR A AUTORIZACIÓN</button>

							<button type="button" class="btn btn-default  btn-xs" name="btnAUTORIZADA" id="btnAUTORIZADA" style="display:none;"><i class="fa fa-external-link"></i>AUTORIZADA</button>

							<button type="button" id="btnComentarios" class="btn btn-warning btn-xs "><i class="fa fa-comments"></i> Comentarios...</button>
							<div id="menudesple" class="btn-group">
								<button type="button" class="btn btn btn-success btn-xs">Formatos</button>
        						<button type="button" class="btn btn-default dropdown-toggle btn-xs" data-toggle="dropdown"><span class="fa fa-chevron-down"></span><span class="sr-only">Desplegar menú</span></button> 
						        <ul class="dropdown-menu" role="menu">
						          <li class="btn-link"><a href="#" id="btnFormatoPlaneacion"><i class="fa fa-file"></i>	Formato REP</a></li>
						        </ul>
						     </div>
			
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
									<div class="col-xs-10  control-label">
										<select class="form-control" name="txtResponsable" id="resp" onchange="recuperarListaLigada('lstAreasSubResponsablesByResponsable', this.value, document.all.txtAreaSubresponsable); dessub();">
											<option value="">Seleccione...</option>											
										</select>
									</div>
									
								</div>							
								<br>
									<div class="form-group">
									<label class="col-xs-2">Subdirección</label>
									<div class="col-xs-10  control-label">
										<select id="arsubre" class="form-control" name="txtAreaSubresponsable">
											<option value="">Seleccione...</option>											
										</select>
									</div>
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
								<label class="col-xs-2 control-label">Tipo de Obs.</label>
								<div class="col-xs-10">
									<select class="form-control" id="TipoObs" name="txtTipoObs" onchange="cajaResultados(this.value); botonactu();" style="resize:none; margin: 0 0 6px 0 !important;">
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
								<div class="col-xs-10"><textarea class="form-control" style="resize:none;" onclick="botonactu();" rows="2" id="txtObserva" name="txtObserva" placeholder="Escriba aqui el resumen o resultado obtenido..."></textarea></div>	
							</div>
							<div class="clearfix"></div>
							<div class="form-group">									
								<div class="col-xs-12" style="margin: 0 0 10px 0 !important;">
									<table class="table table-striped table-bordered table-hover table-condensed">
										<caption style=" margin: 9px 0 0 !important;">Fechas por fase</caption>
										<thead>
										<tr style="font-size: xx-small"><th>Fase</th><th>Fecha Inicio</th><th>Fecha Fin</th><th>Días Efectivos</th></tr>
										</thead>
										<!--<tbody id="tablaFases" onclick="editarCronograma();" style="width: 100%; font-size: xx-small"></tbody>-->
										<tbody id="tablaFases" style="width: 100%; font-size: xx-small"></tbody>
									</table>
								</div>									
							</div>
							
							</div>
							<div class="col-xs-5">
								<label > Apartados</label>

 								<div class="form-group">									
									<div class="row">
										<div class="form-group">
											<div id='laidEstudio'><input name="txtlbEstudio" style="border: medium none; margin: 0px 95px 0px 0px ! important; width: 60% ! important;"></div>	
											<div class="col-xs-12" id="txtActividad"><textarea readonly style="resize:none;" rows="3" id="idPEA" name="txtEstudio"></textarea></div>
										</div>
									</div> 		
								</div>							

 								<div class="form-group">									
									<div class="row">
										<div class="form-group">
											<div id='laidEstudio'><input name="txtlbMuestra" style="border: medium none; margin: 0px 95px 0px 0px ! important; width: 60% ! important;"></div>									
											<div class="col-xs-12" id="txtActiviMuestra"><textarea readonly style="resize:none;" rows="3" id="idMuestra" name="txtMuestra"></textarea></div>
										</div>
									</div> 		
								</div>

 								<div class="form-group">									
									<div class="row">
										<div class="form-group">				
											<div id='laidEstudio'><input name="txtlbPruebas" style="border: medium none; margin: 0px 95px 0px 0px ! important; width: 60% ! important;"></div>					
											<div class="col-xs-12" id="txtActividadPrue"><textarea readonly style="resize:none;" rows="3" id="idPruebas" name="txtPruebas"></textarea></div>
										</div>
									</div> 		
								</div>

 								<div class="form-group">									
									<div class="row">
										<div class="form-group">
											<div id='laidEstudio'><input name="txtlbProced" style="border: medium none; margin: 0px 95px 0px 0px ! important; width: 60% ! important;"></div>									
											<div class="col-xs-12" id="txtActividadProce"><textarea readonly style="resize:none;" rows="3" id="idProcedimiento" name="txtProcedimiento"></textarea></div>
										</div>
									</div> 		
								</div>

 								<div class="form-group">									
									<div class="row">
										<div class="form-group">	
											<div id='laidEstudio'><input name="txtlbObserva" style="border: medium none; margin: 0px 95px 0px 0px ! important; width: 60% ! important;"></div>								
											<div class="col-xs-12" id="txtActividadObse"><textarea readonly style="resize:none;" rows="3" id="idObservacion" name="txtObservacion"></textarea></div>
										</div>
									</div> 		
								</div>
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
								<div class="col-xs-20"><textarea rows="20" disabled id="txtTextoAmplio2" name="txtTextoAmplio2" style=" height: 288px !important; width: 583px !important; resize:none;"></textarea></div>
							</div>
						</div>
					<div class="clearfix"></div>
				</div>				
				<div class="modal-footer">
					<!--<button  type="button" class="btn btn-primary active" id="btnGuardarTexto2"><i class="fa fa-floppy-o"></i> Guardar</button>	-->
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
								<div class="col-xs-20"><textarea class="form-control" rows="20" disabled id="txtTextoAmplio" name="txtTextoAmplio" style=" height: 288px !important; width: 583px !important; resize:none;"></textarea></div>
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
							<label class="col-xs-2 control-label">Fecha Término</label>
							<div class="col-xs-2">
								<input type="text" id="fFin" class="form-control" name="txtFinActividad" onchange="getDiasLaborale(finicio.value,fFin.value); " onkeydown="return false;"/>
							</div>		
							<label class="col-xs-2 control-label">Dias Efectivos</label>
							<div class="col-xs-2">
								<input type="text" id="diasefec" class="form-control" name="txtDiasActividad" readonly style="margin: 0 0 6% !important;"/>
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

										<label class="col-xs-2 control-label">Apartado</label>
										<div class="col-xs-5">
											<select class="form-control" id="Apartado" name="txtApartado" onchange="validarapartado(this.value);">
												<option value="" Selected>Seleccione...</option>
											</select>
										</div>

								</div>						
								
								<br>								
									<div class="form-group">
										<label class="col-xs-2 control-label">Actividad <i class="fa fa-pencil"  id="txtActividadEdit"></i></label>
									 	<div class="col-xs-10">
									 	<textarea class="form-control" rows="15" placeholder="Actividad"  name="txtDescripcionActividad" id="DescripcionActividad" style="resize:none;margin: 2px -10px 0 !important;"></textarea>						
										</div>

									</div>	
									<div class="form-group" id="Complementaria" style="display: none">	
										<label class="col-xs-2 control-label">Actividad Complementaria<i class="fa fa-pencil"  id="txtActividadComEdit"></i></label>
									 	<div class="col-xs-10">
									 	<textarea class="form-control" rows="3" placeholder="Actividad" maxLength= "8000" name="txtDescripcionActividadCom" id="DescripcionActividadCom" style="resize:none;margin: 0 0 1% 0 !important;"></textarea>						
										</div>
									</div>
								<br>	

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
					<button type="button" class="btn btn-primary  btn-xs" 	id="btnNuevaFecha"><i class="fa fa-file-text-o"></i> Nuevo Fecha</button>

					<button  type="button" class="btn btn-default btn-xs" id="btnCancelar2Actividad" 		style="display:none;"><i class="fa fa-undo"></i> Cancelar</button>								
					<button  type="button" class="btn btn-default btn-xs" id="btnCancelarCronograma"><i class="fa fa-undo"></i> Salir</button>
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


	<div id="modalFlotante" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<form id="formulario" METHOD='POST' action='' role="form">								
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