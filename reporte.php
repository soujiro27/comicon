<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reporte</title>
	
<script type="text/javascript" src="/js/canvasjs.min.js"></script>

	
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
  <link rel="stylesheet" href="css/jquery.dataTables.css"> 
  <!-- Bootstrap toggle -->
  <link rel="stylesheet" href="css/jquery.onoff.css">
  <!-- Main stylesheet -->
  <link href="css/style-dashboard.css" rel="stylesheet">
  <!-- Widgets stylesheet -->
  <link href="css/widgets.css" rel="stylesheet">   
 
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
      
	<style type="text/css">		
		@media screen and (min-width: 768px) {
			#mapa_content {width:100%; height:450px;}
			#modalFlotante .modal-dialog  {width:40%;}
		}
		
		body{
			background:lightgray;
			text-align=center;
			font-family: Arial, Helvetica, sans-serif;
		}
		
		.hoja{
			border: 1px solid GRAY;
			padding: 2cm;
			margin: 15px;
			width:21.59cm;
			height:27.94cm;
			background:white;
		}			
		#canvasJG{height:250px; width:100%; padding: 0px;border: 1px solid lightgray;}
	</style>
	
	<script type="text/javascript"> 
	
		var chartJG;	
	
	var dps = [{y: 150, label: "PRI"}, {y: 190, label: "PAN"}, {y: 140, label: "PRD"}, {y: 210, label: "PT"}, {y: 160, label: "PH"}, {y: 20, label: "MORENA"}];

	
window.onload = function () {
		chartJG = new CanvasJS.Chart("canvasJG", {
			title:{ text: "INTENCIÓN DEL VOTO", fontColor: "#2f4f4f",fontSize: 10,verticalAlign: "top", horizontalAlign: "center" },
			axisX: {labelFontSize: 10,labelFontColor: "black", tickColor: "red",
        tickLength: 5,
        tickThickness: 2},		
			animationEnabled: true,
			//legend: {verticalAlign: "bottom", horizontalAlign: "center" },
			theme: "theme1", 
		  data: [
		  {        
			//color: "#B0D0B0",
			indexLabelFontSize: 12,	indexLabelFontColor:"black",type: "pie", bevelEnabled: true, 				
			//indexLabel: "{y}",
			showInLegend: false, legendMarkerColor: "gray",	
			legendText: "{indexLabel} {y}",			
			dataPoints: dps
		  }   
		  ]
		});
		 chartJG.render();
};

	function imprimir(){
		document.all.btnImprimir.style.display='none';	
		document.all.btnRegresar.style.display='none';	
			document.all.pagina.style.border="0px solid black";
			document.all.pagina.style.padding="0cm";
			document.all.pagina.style.margin="0px";
			document.all.pagina.style.background="lightgray";
		window.print();
			document.all.pagina.style.border="1px solid black";
			document.all.pagina.style.padding="2cm";
			document.all.pagina.style.margin="15px";
			document.all.pagina.style.background="white";
		document.all.btnImprimir.style.display='inline';
		//document.all.btnImprimir.style.text-align='center';
		
		document.all.btnRegresar.style.display='inline';	
		//document.all.btnRegresar.style.text-align='center';
	}
		 </script>
	

  </head>
  <body >
  
	<div class="hoja" style="text-align:center" id="pagina">
		<p style="text-align:right; width:100%" style="display:inline" id="btnImprimir">
			<button  onclick="imprimir();" class="btn btn-link btn-lg">
				<span class="glyphicon glyphicon-print"></span> Imprimir
			</button>
		</p>
		<br>
		<img src="/img/logoINE.png">
		<P style="text-align:center">
			<h2>PARTIDO NACIONAL DE SEGURIDAD</h2>
			<h4>REPORTE SEMANAL DE LA INTENCIÓN EL VOTO POR SECCIÓN ELECTORAL</h4>
			<h5>Del 01 al 15 de Septiembre de 2015</h5>
		</p>
		<br>
		<p><div id="canvasJG" ></div></p>
 		
		<table class="table" STYLE='font-size:small;'>
		  <thead>
			<tr>
			  <th>Distrito / Sección</th>
			  <th>Clave de Elector</th>							  
			  <th>Elector</th>
			  <th>Correo</th>
			  <th>Intención del Voto</th>
			  <th>Estatus</th>
			</tr>
		  </thead>
		  <tbody>									  
			<tr><td>III</td><td>COZA344556789</td><td>JAVIER ROSAS MONTERROSA</td><td>JAVIER@GMAIL.COM</td><td>PRD</td><td>TERMINADO</td></tr>
			<tr><td>III</td><td>OIST661003MDFRNR06</td><td>TERESA ORTÍZ SÁNCHEZ</td><td>TERESAXX@GMAIL.COM</td><td>PRI</td><td>TERMINADO</td></tr>
			<tr><td>III</td><td>VADA720913MDFZRM01</td><td>AMADA ELOISA VAZQUEZ DORANTES</td><td>AMADA@GMAIL.COM</td><td>PT</td><td>TERMINADO</td></tr>
			<tr><td>III</td><td>TEDI720513MDFYLM06</td><td>IMELDA TEYSSIER DEOLARTE</td><td>IMELDA@GMAIL.COM</td><td>PRD</td><td>TERMINADO</td></tr>
										
		  </tbody>
		</table>
		<br>
		<p style="text-align:right; width:100%" style="display:inline" id="btnRegresar">
			<button  onclick="window.location.href='/'" class="btn btn-link btn-lg">
				<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar
			</button>
			
			
			
		</p>
		
		
	
	</div>    
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>