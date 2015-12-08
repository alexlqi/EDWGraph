<script type="text/javascript" src="<?php echo __DIR__; ?>js/jquery-1.11.2.min.js"></script>
<script type="text/javascript" src="<?php echo __DIR__; ?>js/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?php echo __DIR__; ?>js/jquery.numeric.js"></script>
<!--[if lt IE 9]><script language="javascript" type="text/javascript" src="<?php echo __DIR__; ?>excanvas.js"></script><![endif]-->
<script type="text/javascript" src="<?php echo __DIR__; ?>jqplot/jquery.jqplot.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo __DIR__; ?>css/jquery-ui.css" />
<link rel="stylesheet" type="text/css" href="<?php echo __DIR__; ?>css/jquery-ui.structure.css" />
<link rel="stylesheet" type="text/css" href="<?php echo __DIR__; ?>css/jquery-ui.theme.css" />
<link rel="stylesheet" type="text/css" href="<?php echo __DIR__; ?>jqplot/jquery.jqplot.css" />
<script type="text/javascript" src="<?php echo __DIR__; ?>js/edwgraph.js"></script>
<style type="text/css">
.grafica1-frame{
	width:90%;
	margin: auto;
	min-width: 300px;
}
.grafica1-frame > div {
	width: 100%;
}
</style>
<div class="edwgraph-ctrl">
	<div><label>Tipo de gráfica</label>
		<select class="select-renderer">
			<option disabled="disabled" selected="selected">--elige un tipo de grafica--</option>
			<option value="lineas">lineas</option>
			<option value="barras">barras</option>
			<option value="barrasylinea">barras y linea</option>
		</select>
	</div>
	<div><label>Activar / Desactivar Animacion</label><button class="animacion-btn">Activar/Desactivar</button></div>
	<div><label>Activar / Desactivar Linea de tendencia</label><button class="trendline-btn">Activar/Desactivar</button></div>
	<div>
		<label>Intervalo de actualizacion:</label>
		<input type="text" class="numerico intervalo-cant" />
		<select class="intervalo-unit">
			<option selected="selected" value="1000">segundos</option>
			<option value="60000">minutos</option>
			<option value="3600000">horas</option>
		</select>
		<button class="intervalo-btn">Actualizar</button>
		<div>
			<label>Siguiente actualizacion:</label><span class="update-time"></span><span> Segundos</span>
		</div>
	</div>
	<div class="filtros">
		<div class="fechas">
			<div>
				Del <input type="text" class="fechaInicio fecha" /> al: <input type="text" class="fechaFin fecha" />
				<button class="filtro-fecha">Actualizar Rango</button>
			</div>
		</div>
	</div>
	<div class="grafica-frame">
	</div>
</div>