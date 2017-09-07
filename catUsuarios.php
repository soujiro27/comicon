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
			#mapa_content {width:100%; height:450px;}
			#modalUsuario .modal-dialog  {width:75%;}


			.delimitado {  
				height: 350px !important;
				overflow: scroll;
			}​			
		}
	</style>
  
  <script type="text/javascript"> 
  var bBusquedaPorEmpleado = false;
	
	function limpiarCampos(){
		document.all.txtID.value="";
		document.all.txtRPE.value = "";
		document.all.txtSaludo.value="";
		document.all.txtNombre.value="";
		document.all.txtPaterno.value="";		
		document.all.txtMaterno.value="";
		document.all.txtIniciales.value="";
		document.all.txtArea.selectedIndex=0;
		document.all.txtNivel.selectedIndex=0;
		document.all.txtCorreo.value="";
		document.all.txtTelefono.value="";
		document.all.txtCuenta.value="";
		document.all.txtPassword.value="";		
		document.all.txtPassword2.value="";		
		document.all.txtCuentaPublica.selectedIndex=0;
		document.all.txtEstatus.selectedIndex=1	;
		document.all.txtRolesDestino.options.length = 0;
		// Limpiar RolesAsignados
	}

	function bloquearCampos(){
		document.all.txtID.readOnly=true;
		document.all.txtRPE.readOnly=true;
		document.all.txtSaludo.readOnly=true;
		document.all.txtNombre.readOnly=true;
		document.all.txtPaterno.readOnly=true;
		document.all.txtMaterno.readOnly=true;
		document.all.txtCorreo.readOnly=true;
		document.all.txtTelefono.readOnly=true;
		document.all.txtCuenta.readOnly=true;
		document.all.txtPassword.readOnly=true;		
		document.all.txtPassword2.readOnly=true;		
	}

	function bloquearControles(){
		//document.all.btnRecuperar.disabled=true;
		document.all.txtArea.disabled=true;
		document.all.txtNivel.disabled=true;
		document.all.txtCuentaPublica.disabled=true;
		document.all.txtEstatus.disabled=true;
		document.all.txtRolesOrigen.disabled=true;
		document.all.txtRolesDestino.disabled=true;
		document.all.btnAgregarRol.disabled=true;
		document.all.btnQuitarRol.disabled=true;
	}

	function desBloquearCampos(){
		document.all.txtID.readOnly=false;
		document.all.txtRPE.readOnly=false;
		document.all.txtSaludo.readOnly=false;
		document.all.txtNombre.readOnly=false;
		document.all.txtPaterno.readOnly=false;
		document.all.txtMaterno.readOnly=false;
		document.all.txtCorreo.readOnly=false;
		document.all.txtTelefono.readOnly=false;
		document.all.txtCuenta.readOnly=false;
		document.all.txtPassword.readOnly=false;		
		document.all.txtPassword2.readOnly=false;		
	}
	
	function desBloquearControles(){
		//document.all.btnRecuperar.disabled=false;
		document.all.txtArea.disabled=false;
		document.all.txtNivel.disabled=false;
		document.all.txtCuentaPublica.disabled=false;
		document.all.txtEstatus.disabled=false;
		document.all.txtRolesOrigen.disabled=false;
		document.all.txtRolesDestino.disabled=false;
		document.all.btnAgregarRol.disabled=false;
		document.all.btnQuitarRol.disabled=false;
	}

	function validarCaptura(){
		if (document.all.txtTipo.options[document.all.txtTipo.selectedIndex].value=='CF' || document.all.txtTipo.options[document.all.txtTipo.selectedIndex].value=='TE'){
			if (document.all.txtRPE.selectedIndex==0){alert("Debe capturar un RPE.");return false;}
		}
		if (document.all.txtTipo.selectedIndex==0){alert("Debe seleccionar un TIPO DE USUARIO.");return false;}
		if (document.all.txtNombre.value==""){alert("Debe capturar el NOMBRE DEL USUARIO.");return false;}
		if (document.all.txtPaterno.value==""){alert("Debe capturar el APELLIDO PATERNO DEL USUARIO.");return false;}
		if (document.all.txtMaterno.value==""){alert("Debe capturar el APELLIDO MATERNO DEL USUARIO.");return false;}
		if (document.all.txtArea.selectedIndex==0){alert("Debe seleccionar una ÁREA.");return false;}
		if (document.all.txtCorreo.value==""){alert("Debe capturar el CORREO DEL USUARIO.");return false;}
		if (document.all.txtTelefono.value==""){alert("Debe capturar el TELEFONO DEL USUARIO.");return false;}
		if (document.all.txtCuenta.value==""){alert("Debe capturar una CUENTA DE USUARIO.");return false;}
		if (document.all.txtPassword.value==""){alert("Debe capturar la CONTRASEÑA DEL USUARIO.");return false;}
		if (document.all.txtCuentaPublica.selectedIndex==0){alert("Debe seleccionar una CUENTA PÚBLICA.");return false;}
		if (document.all.txtEstatus.selectedIndex==0){alert("Debe seleccionar un ESTATUS DE USUARIO.");return false;}
		if (document.all.txtNivel.selectedIndex==0){alert("Debe seleccionar un NIVEL DE USUARIO.");return false;}
		return true;
	}
	
	function obtenerDatos(sUrl){

		if ( sUrl.indexOf("ByRPE")>=0){ bBusquedaPorEmpleado = true; }else{ bBusquedaPorEmpleado = false; }
		//alert("Valor de sUrl es: " + sUrl);
		$.ajax({
			type: 'GET', url: sUrl ,
			success: function(response) {			
				
				var obj = JSON.parse(response);

				//alert(" Response: "+response);

				 //alert("sUrl.indexOf('ByRPE') " + sUrl.indexOf("ByRPE") + "\nTipo de idUsuario: " + typeof(obj.idUsuario)  + "\nTipo de idEmpleado: " + typeof(obj.idEmpleado) );
				
				if ( ( bBusquedaPorEmpleado==true && typeof(obj.idEmpleado) != "undefined") || ( bBusquedaPorEmpleado==false && typeof(obj.idUsuario) != "undefined") ){


					if (document.all.operacion.value != 'INS'){
						document.all.operacion.value='UPD';
					}

					//alert("bBusquedaPorEmpleado: " + bBusquedaPorEmpleado);
					//alert("typeof(obj.idEmpleado): " + typeof(obj.idEmpleado));
					//alert("document.all.operacion.value: " + document.all.operacion.value);

					document.all.txtID.value='' + obj.idUsuario;
					seleccionarElemento(document.all.txtTipo, obj.tipo);	
					document.all.txtRPE.value = '' + obj.idEmpleado;
					document.all.txtSaludo.value='' + obj.saludo;
					document.all.txtNombre.value='' + obj.nombre;
					document.all.txtPaterno.value='' + obj.paterno;
					document.all.txtMaterno.value='' + obj.materno;
					document.all.txtIniciales.value= '' + obj.iniciales;
					seleccionarElemento(document.all.txtArea, obj.idArea);				
					seleccionarElemento(document.all.txtNivel, obj.idNivel);				
					document.all.txtCorreo.value='' + obj.correo;				
					document.all.txtTelefono.value='' + obj.telefono;				
					document.all.txtCuenta.value='' + obj.usuario;				
					document.all.txtPassword.value='' + obj.pwd;					
					seleccionarElemento(document.all.txtCuentaPublica, obj.cuenta);				
					seleccionarElemento(document.all.txtEstatus, obj.estatus);	

					document.all.txtTipoUsrActual.value = obj.tipo;
					document.all.txtRpeActual.value = obj.idEmpleado;

					// Recuperará los roles que tiene asignados

	            	var cadena = '/lstRolesByUsuario/' + obj.idUsuario;
	            	//alert("Valor de cadena: "+ cadena);

		            $.ajax({
			           type: 'GET', url: cadena ,
			           success: function(response) {
		                   var jsonData = JSON.parse(response);                                 
		                                  
	                   		//alert("Valor de jsonData.datos.length: " + jsonData.datos.length);
		                   	if(jsonData.datos.length > 0){

		               			document.all.txtRolesDestino.options.length = 0;
		               			// Agregara los roles que tiene asignado el usuario a el combo destino
			                   	for (var i = 0; i < jsonData.datos.length; i++) {
									var dato = jsonData.datos[i];  
			                   		//alert("Valor de dato.id: " + dato.id + " - dato.texto: " + dato.texto);
									var option = document.createElement("option");
									option.value = dato.id;
									option.text = dato.texto;
									document.all.txtRolesDestino.add(option, document.all.txtRolesDestino.length);
									// Quitara del combo origen los roles que tiene asignado el usuario.
				                   	for (var j = 0; j < document.all.txtRolesOrigen.options.length; j++) {
				                   		if (document.all.txtRolesOrigen[j].value == dato.id){
				                   			document.all.txtRolesOrigen.remove(j);
				                   		}
									}
				                }
		                   }
				        },
				          error: function(xhr, textStatus, error){
				               alert('func: obtenerDatos ->statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
				        }                                             
					});           

		            if( bBusquedaPorEmpleado==false ){
						$('#modalUsuario').removeClass("invisible");
						$('#modalUsuario').modal('toggle');
						$('#modalUsuario').modal('show');				
		            }

		            //alert("El Valor de bBusquedaPorEmpleado es: "+bBusquedaPorEmpleado);
		            //alert("El valor de document.all.operacion.value es: " + document.all.operacion.value);


		            habilitarEmpleados(obj.tipo);

		        }else{
		        	alert("No se localizó información con el RPE proporcionado");
		        }
			},
			error: function(xhr, textStatus, error){
				alert('statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
			}			
		});			
	}		

	function moverRol(cmb1, indice, cmb2){
		//alert("Dentro de Mover ROL              Indice: " + indice);
		
		var bDuplicado=false;
		var valorBuscado = cmb1.options[indice].value;
			
		//Revisar duplicados
		var nPos=0;
		while (nPos <cmb2.length){
			if(cmb2.options[nPos].value==valorBuscado){
				bDuplicado=true;
				break;
			}	
			nPos++;
		}
		
		//Transferir el elemento
		if(bDuplicado==true){
			alert("Duplicado.");
			return  false;
		}else{
			//Agregar elemento a cmb2
			var option = document.createElement("option");
			option.text = cmb1.options[indice].text;
			option.value = cmb1.options[indice].value;
			cmb2.add(option, cmb2.length);
			
			//Eliminar elemento del cmb1
			cmb1.remove(indice);
		}
		return true;
	}	

	function ordenarListaByTexto_HVS(cmb) {
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

	function moverRight(){
		var c1 = document.all.txtRolesOrigen;
		var c2 = document.all.txtRolesDestino;
			
		if (moverRol(c1, c1.selectedIndex, c2)==false){
			alert("No se pudo asignar el ROL.");
		}else{
			ordenarListaByTexto_HVS(document.all.txtRolesOrigen);
			ordenarListaByTexto_HVS(document.all.txtRolesDestino);
			//alert("Rol Asignado!");
		}
	}

	function moverLeft(){
		var c1 = document.all.txtRolesDestino;
		var c2 = document.all.txtRolesOrigen;			
		if (moverRol(c1, c1.selectedIndex, c2)==false){
			alert("No se pudo asignar el ROL.");
		}else{
			ordenarListaByTexto_HVS(document.all.txtRolesOrigen);
			ordenarListaByTexto_HVS(document.all.txtRolesDestino);
		}
	}


	var nUsr='<?php echo $_SESSION["idUsuario"];?>';		
	var nCampana='<?php echo $_SESSION["idCuentaActual"];?>';	
	
	$(document).ready(function(){
		getMensaje('txtNoti',1);

		if(nUsr!="" && nCampana!=""){
			cargarMenu( nCampana);			
		}else{
			if(nCampana=="")alert("Debe establecer una CAMPAÑA como PREDETERMINADA. Por favor consulte con su administrador del sistema.");
		}					

		recuperarListaLarga('lstRoles', document.all.txtRolesOrigen);
		recuperarLista('lstAreas_HVS2', document.all.txtArea);
		recuperarLista('lstNiveles', document.all.txtNivel);
		recuperarLista('lstNombramientos', document.all.txtTipo)
		recuperarLista('lstCuentas', document.all.txtCuentaPublica);


		$("#btnAgregarRol" ).click(function() {
			moverRight();
			
		});	

		$("#btnQuitarRol" ).click(function() {			
			moverLeft();
		});	
		
		
		/*
		$("#btnRecuperar" ).click(function() {
			//extraeDatos(document.all.txtRPE.value);
			obtenerDatos('/empleadoByRPE_HVS/' + document.all.txtRPE.value);
		});		
		*/
		
		$("#btnAgregarUsuario" ).click(function() {
			document.all.operacion.value='INS';
			document.all.txtTipoUsrActual.value="";
			limpiarCampos();
			bloquearCampos();
			bloquearControles();
			document.all.txtTipo.selectedIndex=0;
			document.all.txtEstatus.selectedIndex=1;
			bBusquedaPorEmpleado=false;
			$('#modalUsuario').removeClass("invisible");
			$('#modalUsuario').modal('toggle');
			$('#modalUsuario').modal('show');
			
		});

		$("#btnGuardar" ).click(function() {
			if(validarCaptura()){
				//alert("Aqui: X2");
				var sRoles = "";
				if(document.all.txtRolesDestino.length > 0){
					var separador = "";
					for(i=0; i < document.all.txtRolesDestino.length; i++){
						sRoles = sRoles + separador + document.all.txtRolesDestino[i].value + '|' + document.all.txtRolesDestino[i].text ;
						separador = '*';
					}
				}
				document.all.txtRolesUsuario.value = sRoles;
				//alert("Valor de txtRolesUsuario: " + document.all.txtRolesUsuario.value);
				//alert("Valor de idEmpleado: " + document.all.txtRPE.value);
				document.all.formulario.submit();
			}
		});	

		$("#btnCancelar" ).click(function() {
			document.all.operacion.value='';
			bBusquedaPorEmpleado = false;
			$('#modalUsuario').modal('hide');
		});	
	});
	
	function habilitarEmpleados(sTipo){
		var tipoUsuarioModificado = document.all.txtTipo.options[document.all.txtTipo.selectedIndex].value;
		var textoTipoUsrActual = document.all.txtTipo.options[document.all.txtTipo.selectedIndex].text;
		//switch (document.all.txtTipoUsrActual.value){
		//	case "CF": textoTipoUsrActual = "ESTRUCTURA"; break;
		//	case "TE": textoTipoUsrActual = "EVENTUAL"; break;
		//	case "HS": textoTipoUsrActual = "HONORARIOS"; break;
		//	case "PR": textoTipoUsrActual = "PROFIS"; break;
		//}

		//alert("Tipo: " + sTipo);
		//document.all.txtArea.selectedIndex=0;
		
		//alert("Operación: " + document.all.operacion.value + " \n busquedaPorEmpleado: " + bBusquedaPorEmpleado + "\n tipoUsuarioModificado: " + tipoUsuarioModificado + "\n textoTipoUsrActual: " + textoTipoUsrActual + "\n document.all.txtTipo.selectedIndex: " + document.all.txtTipo.selectedIndex + "\n sTipo: " + sTipo);

		if (bBusquedaPorEmpleado==true ){
			document.all.txtID.readOnly=true;
			document.all.txtRPE.readOnly=true;
			document.all.txtNombre.readOnly=true;
			document.all.txtPaterno.readOnly=true;
			document.all.txtMaterno.readOnly=true;
			//document.all.btnRecuperar.disabled=true;
			//// Modificado por hvs 26 Abril 2017 document.all.txtArea.disabled=true;
			//document.all.txtNivel.disabled=true;				
		}else{
			if( document.all.operacion.value == 'INS') {
				//limpiarCampos();

				if(document.all.txtTipo.selectedIndex==0){
					bloquearCampos();
					bloquearControles();
				}else{
					desBloquearCampos();
					desBloquearControles();
					document.all.txtID.readOnly=true;
					if(sTipo=='CF' || sTipo=='TE' ){
						document.all.txtRPE.focus();
							
					}else{ 
						document.all.txtRPE.readOnly=true;
						document.all.txtSaludo.focus();
					}			
				}
			}else{
				document.all.txtID.readOnly=true;
				document.all.txtRPE.readOnly=true;
				document.all.txtNombre.readOnly=true;
				document.all.txtPaterno.readOnly=true;
				document.all.txtMaterno.readOnly=true;
				//// Modificado por hvs 26 Abril 2017 document.all.txtArea.readOnly=false;

				if(document.all.txtTipoUsrActual.value != ''){
					if(document.all.txtTipoUsrActual.value != tipoUsuarioModificado ){
						confirmacion = confirm("¿Esta seguro que desea modificar el tipo de usuario?:\n \n" + "De: " + textoTipoUsrActual + "\nA: " + document.all.txtTipo.options[document.all.txtTipo.selectedIndex].text);
						if (confirmacion == true){ 	
							if(tipoUsuarioModificado == 'CF' || tipoUsuarioModificado == 'TE'){
								//alert("Valor de RPE: " + document.all.txtRPE.value );
								if(document.all.txtRPE.value == "" || document.all.txtRPE.value== "0"){
									desBloquearCampos();
									desBloquearControles();
									document.all.txtID.readOnly=true;
									document.all.txtNombre.readOnly=true;
									document.all.txtPaterno.readOnly=true;
									document.all.txtMaterno.readOnly=true;
									document.all.txtRPE.readOnly=false;
									document.all.txtRPE.value="";
									document.all.txtRPE.focus();
								}
							}
						}
					}
				}
			}
		}
	}	

	function ValidaContrasenas(sPsw2){
		var sPsw1 = document.all.txtPassword.value;
		//alert("Valor de sPsw1: " + sPsw1 + " sPsw2: " + sPsw2);
		//if(document.all.operacion.value == "INS"){
			if(sPsw1 != sPsw2){
				alert("Las contraseñas no coinciden, valide por favor");
				document.all.txtPassword.value="";
				document.all.txtPassword2.value="";
				document.all.txtPassword1.focus();
			}
		//}
	}
	
	function ValidaIniciales(sIniciales){
		var iniciales = "";
		var cara = "";
		var nomCompleto = document.all.txtNombre.value + " " + document.all.txtPaterno.value + " " + document.all.txtMaterno.value ; 

		if (sIniciales==""){
  			iniciales += nomCompleto.substr(0,1);
         	for (var i = 0; i < nomCompleto.length ; i++) {
         		cara = nomCompleto.substr(i,1);
	         	//alert("El valor de cara es: " + cara);
         		if (cara == " "){
         			iniciales += nomCompleto.substr(i+1,1);
         		}
         	}
         	if (iniciales.length > 0){
         		document.all.txtIniciales.value = iniciales;
         	}
		}
	}

	function ValidaExisteUsuarioByEmpleado(sId){
		//alert("document.all.operacion: " + document.all.operacion.value);
		//alert("sId: " + sId);
		if(sId!=""){
			
			if(document.all.operacion.value == "INS"){
				//sId = sId.toUpperCase();
				$.ajax({
					type: 'GET', url: '/validaExisteUsuarioByEmpleado/' + sId ,
					success: function(response) {
						//alert(response);
						var obj = JSON.parse(response);
						//alert(obj.total);

						if (obj.total > 0 ){
							alert("ATENCION: Ya existe el RPE: " + sId + "\npor favor verifique.");
							document.all.txtRPE.value = "";
							document.all.txtRPE.focus();
						}else{
							// No existe el usuario lo buscara por empleado
							$.ajax({
								type: 'GET', url: '/validaExisteRPE/' + sId ,
								success: function(response) {
									//alert(response);
									var obj = JSON.parse(response);
									//alert(obj.total);

									if (obj.total == 0 ){
										/*
										alert("ATENCION: No existe el RPE: " + sId + "\npor favor verifique.");
										document.all.txtRPE.value = "";
										document.all.txtRPE.focus();
										*/
										confirmacion = confirm("¿El RPE ingresado no Existe.\n\n¿Desea darlo de alta?" );
		
										if (confirmacion == true){ 	
										}else{
											document.all.txtRPE.value = "";
											document.all.txtRPE.focus();
										}


									}else{
										// Localizo el el empleado en la tabla empleados y lo Recuperará
										//document.all.txtSaludo.value = obj.saludo;
										obtenerDatos('/empleadoByRPE_HVS/' + document.all.txtRPE.value);
									}
							},
								error: function(xhr, textStatus, error){
									alert('function ValidaExisteUsuarioByEmpleado()   -> statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
								}
							});		
						}
				},
					error: function(xhr, textStatus, error){
						alert('function ValidaExisteUsuarioByEmpleado()   -> statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
					}
				});		
				//obtenerDatos('/empleadoByRPE_HVS/' + document.all.txtRPE.value);
			}else{
				if (document.all.txtRPE.value != document.all.txtRpeActual.value){
					$.ajax({
						type: 'GET', url: '/validaExisteUsuarioByEmpleado/' + sId ,
						success: function(response) {
							//alert(response);
							var obj = JSON.parse(response);
							//alert(obj.total);

							if (obj.total > 0 ){
								alert("ATENCION: Ya existe el RPE: " + sId + "\npor favor verifique.");
								document.all.txtRPE.value = "";
								document.all.txtRPE.readonly=false;
								document.all.txtRPE.focus();
							}else{

								$.ajax({
									type: 'GET', url: '/validaExisteRPE/' + sId ,
									success: function(response) {
										//alert(response);
										var obj = JSON.parse(response);
										//alert(obj.total);

										if (obj.total > 0 ){
											alert("ATENCION: Ya existe el RPE: " + sId + "\npor favor verifique.");
											document.all.txtRPE.value = "";
											document.all.txtRPE.readonly=false;
											document.all.txtRPE.focus();
										}
								},
									error: function(xhr, textStatus, error){
										alert('function ValidaExisteUsuarioByEmpleado()   -> statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
									}
								});		
							}
					},
						error: function(xhr, textStatus, error){
							alert('function ValidaExisteUsuarioByEmpleado()   -> statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
						}
					});		
				}
			}
		}
	}
	
    </script>
  
  <!-- Title and other stuffs -->
  <title>Sistema Integral de Auditoría</title>
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
					<div class="col-xs-3"><h2>Catálogo de Usuarios</h2></a></div>									
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

<!-- Main content starts -->
<div class="content">
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
	</div> <!-- Sidebar ends -->
	
  	  	<!-- Main bar -->
  	<div class="mainbar">      
		<!-- Page heading -->
    	<!-- Page heading -->
    	<div class="page-head">
        	<h2 class="pull-left"><i class="fa fa-table"></i> Usuarios del Sistema</h2>

        	<!-- Breadcrumb -->
        	<div class="bread-crumb pull-right">
         		 <a href="index.html"><i class="fa fa-home"></i> Home</a> 
          		<!-- Divider -->
          		<span class="divider">/</span> 
          		<a href="#" class="bread-current">Usuarios</a>
        	</div>
        	<div class="clearfix"></div>
    	</div>
	    <!-- Page heading ends -->	
		
		<div class="matter">
			<div class="container">

				<!-- Table -->
				<div class="row">
					<div class="col-md-12">				
						<div class="widget">
							<div class="widget-head">
								<div class="pull-left">Lista de Usuarios del Sistema</div>
								<div class="widget-icons pull-right"></div>  
							  <div class="clearfix"></div>
							</div>
						
							<div class="widget-content">	
								<div class="table-responsive" style="height: 400px; overflow: auto; overflow-x:hidden;">
									<table class="table table-striped table-bordered table-hover">
										<thead>
											<tr>
										 		<th>RPE</th>
										  		<th>Nombre del Usuario</th>							  
										  		<th>Correo Electrónico</th>							  
										  		<th>Teléfono</th>							  
										  		<th>Tipo</th>							  
										  		<th>Cuenta</th>
										  		<th>Estatus</th>
											</tr>
									 	</thead>
									  	<tbody>
											<?php foreach($datos as $key => $valor): ?>
											<tr onclick=<?php echo "obtenerDatos('/usuario_HVS/" . $valor['id'] . "');" ; ?> >
											  <td><?php echo $valor['idEmpleado']; ?></td>
											  <td><?php echo $valor['usuario']; ?></td>
											  <td><?php echo $valor['correo']; ?></td>
											  <td><?php echo $valor['telefono']; ?></td>
											  <td><?php echo $valor['tipo']; ?></td>
											  <td><?php echo $valor['cuenta']; ?></td>
											  <td><?php echo $valor['estatus']; ?></td>
											</tr>
											<?php endforeach; ?>                                                                   
									  	</tbody>
									</table>
								</div>
							</div>
							
							<div class="widget-foot">
								<button id="btnAgregarUsuario" class="btn btn-primary btn-xs">Agregar Usuario</button>
								<div class="clearfix"></div> 
							</div>
						</div>
						<!-- 
						<form id="formulario" METHOD='POST' action='/guardar/usuario_HVS' role="form" onsubmit="return validarEnvio();">	
						-->
							<div id="modalUsuario" class="modal fade" role="dialog"> 
								<div class="modal-dialog">

									<!-- Modal content-->
									<div class="modal-content">	
										<!-- Modal header-->
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal">&times;</button>
											<h4 class="modal-title">Datos del Usuario...</h4>
										</div>										
										<!-- Modal body-->
										<div class="modal-body">
											<!-- Inicio del Form-->

											<form id="formulario" METHOD='POST' action='/guardar/usuario_HVS' role="form">								
												<input type='HIDDEN' name='operacion' value=''>
												<input type='HIDDEN' name='txtRolesUsuario' value=''>
												<input type='HIDDEN' name='txtTipoUsrActual' value=''>
												<input type='HIDDEN' name='txtRpeActual' value=''>


												<div class="form-group">
													<label class="col-xs-2 control-label" style="text-align:right">Id</label>
													<div class="col-xs-1">
														<input type="text" class="form-control" name="txtID" readonly />
													</div>
													<label class="col-xs-2 control-label" style="text-align:right">Tipo de Usuario</label>																								
													<div class="col-xs-3">
														<select class="form-control" name="txtTipo" onchange="habilitarEmpleados(this.value);" ;>
															<option value="">Seleccione...</option>
														<!--
															<option value="CF">ESTRUCTURA</option>
															<option value="TE">EVENTUAL</option>
															<option value="HS">HONORARIOS</option>
															<option value="PR">PROFIS</option>
															-->
														</select>
													</div>													

													<label class="col-xs-1 control-label" style="text-align:right">RPE</label>
													<div class="col-xs-3">
														<input type="text" class="form-control" name="txtRPE" Onblur="ValidaExisteUsuarioByEmpleado(this.value)" />
													</div>
													<!--
													<div class="col-xs-2">
														<button id="btnRecuperar" class="btn btn-info" disabled>Obtener Empleado</button>
													</div>											
													-->
												</div>
												<div class="clearfix"></div>
												<br>

												<div class="form-group">												
													<label class="col-xs-2 control-label" style="text-align:right">Nombre(s)</label>
													<div class="col-xs-1">
														<input type="text" class="form-control" name="txtSaludo" placeholder="C." />
													</div>													
													<div class="col-xs-3">
														<input type="text" class="form-control" name="txtNombre" id="txtNombre" placeholder="Nombre(s)"/>
													</div>
													<div class="col-xs-3">
														<input type="text" class="form-control" name="txtPaterno" placeholder="Paterno" />
													</div>
													<div class="col-xs-3">
														<input type="text" class="form-control" name="txtMaterno" placeholder="Materno" />
													</div>
												</div>											
												<br>

												<div class="clearfix"></div>
												<div class="form-group">
													<label class="col-xs-2 control-label" style="text-align:right">Área</label>
													<div class="col-xs-6">
														<select class="form-control" name="txtArea" >
															<option value="">Seleccione...</option>
														</select>
													</div>
													<label class="col-xs-2 control-label" style="text-align:right">Iniciales</label>
													<div class="col-xs-2">
														<input type="text" class="form-control" id="txtIniciales" name="txtIniciales" onfocus="ValidaIniciales(this.value)"/>
													</div>

												</div>												
												<br>
												<div class="clearfix"></div>

												<div class="form-group">
													<label class="col-xs-2 control-label" style="text-align:right">Correo Electrónico</label>
													<div class="col-xs-6">
														<input type="text" class="form-control" name="txtCorreo" />
													</div>
													<label class="col-xs-2 control-label" style="text-align:right">Teléfono</label>
													<div class="col-xs-2">
														<input type="text" class="form-control" name="txtTelefono" />
													</div>												
												</div>												
												<br>
												<div class="clearfix"></div>

												<div class="form-group">
													<label class="col-xs-2 control-label" style="text-align:right">Cuenta</label>
													<div class="col-xs-2">
														<input type="text" class="form-control" name="txtCuenta"/>
													</div>
													<label class="col-xs-2 control-label" style="text-align:right">Introduzca Password </label>
													<div class="col-xs-2">
														<input type="password" class="form-control" name="txtPassword" onblur="ValidaContrasenas(this.value)" />
													</div>		
													<label class="col-xs-2 control-label" style="text-align:right">Confirmar Password</label>
													<div class="col-xs-2">
														<input type="password" class="form-control" name="txtPassword2" onblur="ValidaContrasenas(this.value)" />
													</div>													
												</div>			
												<br>
												<div class="clearfix"></div>

												<div class="form-group">
													<label class="col-xs-2 control-label" style="text-align:right">Cuenta Pública</label>
													<div class="col-xs-3">
														<select class="form-control" name="txtCuentaPublica">
															<option value="">Seleccione...</option>
														</select>
													</div>												

													<label class="col-xs-1 control-label" style="text-align:right">Nivel</label>
													<div class="col-xs-2">
														<select class="form-control" name="txtNivel" >
															<option value="">Seleccione...</option>
														</select>
													</div>

													<label class="col-xs-2 control-label" style="text-align:right">Estatus</label>
													<div class="col-xs-2">
														<select class="form-control" name="txtEstatus">
															<option value="">Seleccione...</option>
															<option value="ACTIVO" selected>ACTIVO</option>
															<option value="SUSPENDIDO">SUSPENDIDO</option>
															<option value="INACTIVO">INACTIVO</option>
														</select>
													</div>											
												</div>											

											</form>

												<div class="form-group">
													<label class="col-xs-2 control-label"></label>
													<div class="col-xs-5">
														<strong>Roles Disponibles</strong><br>
														<select class="form-control" name="txtRolesOrigen" size=8 onDblClick="moverRight();" ></select>
														<div  class="pull-right"><button id="btnAgregarRol" class="btn btn-warning btn-xs">Agregar >></button>	
														</div>
													</div>
													<div class="col-xs-5">
														<strong>Roles Asignados</strong><br>
														<select class="form-control" name="txtRolesDestino" size=8 onDblClick="moverLeft();" ></select>
														<button id="btnQuitarRol" class="btn btn-warning btn-xs"><< Quitar</button>	
													</div>
													<div class="clearfix"></div>												
												</div>

												<div class="clearfix"></div>


										</div>
										<!--   COMENTARIOS  : AQUI VA EL FOOTER MODAL -->
										<div class="modal-footer">
											<!-- <button id="btnRecuperar" class="btn btn-info" disabled>Obtener Empleado</button> -->
											<button id="btnGuardar" class="btn btn-primary">Guardar datos</button>	
											<button id="btnCancelar" class="btn btn-default">Cancelar</button>	
										</div>
									</div>
								</div>
							</div>
							<!-- Aqui  va el form si se requiere volverlo a sacar -->
						</div>
					</div>
				</div>
			</div>
		</div>
    </div>

   <!-- Mainbar ends -->
   <div class="clearfix"></div>
   								  						   
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
<span class="totop" style="display: none;"><a href="http://ashobiz.asia/mac52/macadmin/#"><i class="fa fa-chevron-up"></i></a></span> 

<!-- JS -->
<script src="./Dashboard - MacAdmin_files/jquery.js"></script> <!-- jQuery -->
<script src="./Dashboard - MacAdmin_files/bootstrap.min.js"></script> <!-- Bootstrap -->
<script src="./Dashboard - MacAdmin_files/jquery-ui.min.js"></script> <!-- jQuery UI -->
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


<!-- Charts & Graphs -->

<!-- 
<script src="./Dashboard - MacAdmin_files/charts.js"></script> 

<script src="./Dashboard - MacAdmin_files/moment.min.js"></script> 
<script src="./Dashboard - MacAdmin_files/fullcalendar.min.js"></script> 
<script src="./Dashboard - MacAdmin_files/jquery.rateit.min.js"></script> 
<script src="./Dashboard - MacAdmin_files/jquery.prettyPhoto.js"></script>
-->
</body></html>