<!doctype html>

<html lang="es">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset="utf-8">
  <title>TOTAL DE CIUDADANOS CAPTURADOS POR COORDINADOR</title>
  <meta name="description" content="Reporte de Totales">
  <meta name="author" content="JOSE COTA">
 
  	
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
			background:transparent;
			text-align=center;
			font-family:Arial, Helvetica, sans-serif;
		}
		
	</style>

  <!--[if lt IE 9]>
  <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
  
  <script type="text/javascript"> 
	function imprimir(){
		document.all.btnImprimir.style.display='none';	
		document.all.btnRegresar.style.display='none';	
		window.print();
		document.all.btnImprimir.style.display='inline';
		//document.all.btnImprimir.style.text-align='center';
		
		document.all.btnRegresar.style.display='inline';	
		//document.all.btnRegresar.style.text-align='center';
	}  
  </script>
</head>
<body>

	<div class="col-md-6" style="padding:0px; text-align: left; border: 0px solid black;">
		<p style="text-align:right;">
			<div class="col-md-10 style="padding:0px"></div>			
			<div class="col-md-2 style="padding:0px">
				<button type="button" onclick="window.close();" id="btnRegresar" 			class="btn btn-default  btn-xs"><i class="fa fa-undo"></i> Salir</button>			
			</div>					
			<br>
		</p>

		
		<p style="text-align:center;">
			<object type="application/pdf" data="http://auditoria.josecota.com.mx/uploads/ejemplo1.pdf#toolbar=0" width="100%" height="800" id="pdf"> 
				<param name="src" value="http://auditoria.josecota.com.mx/uploads/ejemplo1.pdf#toolbar=0" /> 
				<p style="text-align:center; width: 60%;">Adobe Reader no se encuentra o la versi&oacute;n no es compatible, utiliza el icono para ir a la p&aacute;gina de descarga <br /> 
						<a href="http://get.adobe.com/es/reader/" onclick="this.target='_blank'">
						<img src="reader_icon_special.jpg" alt="Descargar Adobe Reader" width="32" height="32" style="border: none;" /></a> 
				</p> 
			</object> 
		</p>
	</div>
	<div class="col-md-6 style="padding:0px"></div>
</body>
</html>

