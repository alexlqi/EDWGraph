<html>
<head>
	<title>EDW Graph Framework</title>
	<script type="text/javascript" src="js/jquery-1.11.2.min.js"></script>
	<script type="text/javascript" src="js/jquery-ui.min.js"></script>
	<script type="text/javascript" src="js/jquery.numeric.js"></script>
	<!--[if lt IE 9]><script language="javascript" type="text/javascript" src="excanvas.js"></script><![endif]-->
	<script type="text/javascript" src="jqplot/jquery.jqplot.min.js"></script>
	<link rel="stylesheet" type="text/css" href="css/jquery-ui.css" />
	<link rel="stylesheet" type="text/css" href="css/jquery-ui.structure.css" />
	<link rel="stylesheet" type="text/css" href="css/jquery-ui.theme.css" />
	<link rel="stylesheet" type="text/css" href="jqplot/jquery.jqplot.css" />
	<script type="text/javascript" src="js/edwgraph.js"></script>
	<script type="text/javascript">
		$(document).ready(function(e){
			// numerico es numeric
			$(".numerico").numeric();

			// fecha es datepicker
			$(".fecha").datepicker({
				dateFormat:"yy-mm-dd",
			});

			ajaxParams={
				searchParams:{
					fechaInicio:rangoFecha[0],
					fechaFin:rangoFecha[1],
				}
			};

			data2plot(ajaxParams);

			ploteo();

		});
	</script>
</head>
<body>
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
</body>
</html>
