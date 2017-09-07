	var cantllamadas=0;
	var mensajes;
	var cantllamadas2=0;
	var mensajes2;
	function getMensaje2(input, minutos){
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
				clearInterval(mensajes);
				mensajes = setTimeout(function(){getMensaje2(input, minutos)},minutos*60000);
				}
				
			}else{
				if (cantllamadas2 <= 5) 
					clearInterval(mensajes2);
				{
				clearInterval(mensajes2);
				mensajes2 = setTimeout(function(){getMensaje2(input, minutos)},300000);
				}
			}
		},
		error: function(xhr, textStatus, error){
		 alert(' Error en   function getMensaje    \n\nstatusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error );
		}   
	   }); 
  }
	
