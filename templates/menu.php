<?php  
	$menu='
	<div class="sidebar">
        <div class="sidebar-dropdown"><a href="/">Navigation</a></div>
		
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
	</div>';

echo $menu;
?>