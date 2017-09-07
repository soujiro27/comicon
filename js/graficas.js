
var dps = [];

function graficaPie(grafica, datos, tipo,  titulo, canvas){
				dps = [];
				$.ajax({
					type: 'GET', url:datos,
					success: function(response){
						var objs = JSON.parse(response);	
						
						for (var i = 0; i < objs.datos.length; i++) {
							dato = objs.datos[i];
							dps.push({indexLabel:dato.texto, y:dato.valor});
						}

						grafica = new CanvasJS.Chart(canvas,
						{
							theme: "theme1",
							title:{text: titulo,fontSize: 10,verticalAlign: "top", horizontalAlign: "center"},
							data: [{type: "pie",showInLegend: true,toolTipContent: "{y} - #percent %", yValueFormatString: "#0.#,,", legendText: "{indexLabel}",dataPoints: dps}]
						});
						grafica.render();					
					},
					error: function(xhr, textStatus, error){
						alert('function graficaPie()   -> statusText: ' + xhr.statusText + ' TextStatus: ' + textStatus + ' Error:' + error + ' Papel: ' + id);
					}			
				});	






}



