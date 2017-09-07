
function usuario(cuenta,cuentadiv,nUsr){
	var cuen = document.getElementById(cuenta);
	var divcuen = document.getElementById(cuentadiv);
	$.ajax({
		type: 'GET', url: '/usuario/' + nUsr ,
		success: function(response) {
			var obj = JSON.parse(response);
			console.log(obj.tipousr);
			if(obj.tipousr =='admin'){
				cuen.style.display='inline';
				divcuen.style.display='inline';
			}else{
				cuen.style.display='none';
				divcuen.style.display='none';
			}
		},
		error: function(xhr, textStatus, error){
			alert('ERROR: En function asignarEtapa(auditoria, proceso, etapa)  ->  statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
		}			
	});		
}


function usuariovolante(valor){
	$.ajax({
		type: 'GET', url: '/usuparametro/' + valor ,
		success: function(response) {
			var obj = JSON.parse(response);
			var volante = obj.usuario;
			return volante;
		}
				
	});		
}