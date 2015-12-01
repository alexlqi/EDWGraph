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
	<script type="text/javascript" src="jqplot/plugins/jqplot.logAxisRenderer.min.js"></script>
	<script type="text/javascript" src="jqplot/plugins/jqplot.canvasTextRenderer.min.js"></script>
	<script type="text/javascript" src="jqplot/plugins/jqplot.canvasAxisLabelRenderer.min.js"></script>
	<script type="text/javascript" src="jqplot/plugins/jqplot.canvasAxisTickRenderer.min.js"></script>
	<script type="text/javascript" src="jqplot/plugins/jqplot.dateAxisRenderer.min.js"></script>
	<script type="text/javascript" src="jqplot/plugins/jqplot.categoryAxisRenderer.min.js"></script>
	<script type="text/javascript" src="jqplot/plugins/jqplot.barRenderer.min.js"></script>
	<script type="text/javascript" src="jqplot/plugins/jqplot.pointLabels.min.js"></script>
	<script type="text/javascript" src="jqplot/plugins/jqplot.highlighter.js"></script>
	<script type="text/javascript" src="jqplot/plugins/jqplot.cursor.js"></script>
	<script type="text/javascript">
		var date=new Date();

		var params={};
		var charts=[];
		var render="lineas";
		var options={};
		var arr=[];
		var xmin=0;
		var xmax=0;
		var stopAjax=false;
		var updateInterval=10000;
		var countDownInt;
		var cDownCtrl=1;
		var resizeTimeout;
		var animacion=false;
		var rangoFecha=[date.getFullYear()+"-01-01",date.getFullYear()+"-12-31"];
		
		$(window).resize(function(e){
			clearTimeout(resizeTimeout);
			resizeTimeout=setTimeout(function(){ ploteo(); }, 100);
		});
		$(document).ready(function(e){
			// numerico es numeric
			$(".numerico").numeric();

			// fecha es datepicker
			$(".fecha").datepicker({
				dateFormat:"yy-mm-dd",
			});

			//inicializar la variable objSeriesDefault
			selectRenderer();
			params={
				searchParams:{
					fechaInicio:rangoFecha[0],
					fechaFin:rangoFecha[1],
				}
			};

			// daemon to check if esc is pressed
			$(window).keyup(function(e){
				// checar la tecla presionada
				//alert(e.keyCode);
				switch(e.keyCode){
					case 27:
						stopAjax=true;
						clearInterval(_countDownInt);
					break;
					case 13:
						//console.log(e.target.nodeName);
						if(e.target.nodeName=="BODY"){
							stopAjax=false;
							setTimeout(ploteo(),100);
						}
					break;
				}
			});

			//activar/desactivar animación
			$(".animacion-btn").click(function(e){
				animacion=!animacion;
				
				//resetea todo el plot
				setTimeout(ploteo(),100);

				//alert(animacion);
			});

			//cambiar el intervalo de actualización
			$(".intervalo-btn").click(function(e){
				//se leen los parametros y se modifican las actualizaciones
				cant=($(".intervalo-cant").val())*1;
				unidad=($(".intervalo-unit").find("option:selected").val())*1;
				updateInterval=cant * unidad;
				//alert($(".intervalo-unit").find("option:selected").val());

				//resetea todo el plot
				setTimeout(ploteo(),100);
			});

			// select del tipo de graficas
			$(".select-renderer").change(function(e){
				render=$(this).val();
				//alert(rend);
				selectRenderer(render);
				setTimeout(ploteo(),100);
			});

			//cambiar parametros de fecha
			$(".filtro-fecha").click(function(e){
				//se leen los parametros y se modifican las actualizaciones

				rangoFecha[0]=$(".fechaInicio").val();
				rangoFecha[1]=$(".fechaFin").val();
				//render
				selectRenderer(render);
				//resetea todo el plot
				params.searchParams={
					fechaInicio:rangoFecha[0],
					fechaFin:rangoFecha[1],
				};
				setTimeout(ploteo(),100);
			});

			//creamos la primera grafica
			RTGraph(params);

			//el checador de tiempo entre el siguiente update
			countDown(updateInterval);

			//se crea el _updateFn
			_updateFn=setInterval(function(){RTGraph({});},updateInterval);
		});
	
	function selectRenderer(renderer){
		switch(renderer){
			case 'barras':
				options={
					objSeriesDefaults:{
						renderer:$.jqplot.BarRenderer,
	        			pointLabels: { show: true },
						rendererOptions: {
						    smooth: false,
						    animation: {
						        show: animacion,
						        speed: 600,
						    }
						},
						showMarker: true,
					},
					axes:{
						xaxis:{
							renderer: $.jqplot.CategoryAxisRenderer,
			                tickRenderer: $.jqplot.CanvasAxisTickRenderer,
			                tickOptions: {
			                    formatString: "%e %b %y",
			                    angle: -30,
			                },
			                min: rangoFecha[0],
			                max: rangoFecha[1],
						}
					},
				}
			break;
			default:
				options={
					objSeriesDefaults:{
	        			pointLabels: { show: false },
						rendererOptions: {
						    smooth: false,
						    animation: {
						        show: animacion,
						        speed: 600,
						    }
						},
						showMarker: true,
					},
					axes:{
						xaxis:{
							renderer: $.jqplot.DateAxisRenderer,
			                tickRenderer: $.jqplot.CanvasAxisTickRenderer,
			                tickOptions: {
			                    formatString: "%e %b %y",
			                    angle: -30,
			                },
			                min: rangoFecha[0],
			                max: rangoFecha[1],
						}
					}
				}
			break;
		}
	}

	function countDown(time){
		if(stopAjax){return false;}
		// aquí se escribe el tiempo en el que se actualizara en cuenta regresiva cada segundo
		strTime=time/1000; //en segundos
		cDownCtrl=1;
		$(".update-time").html(strTime);
		_countDownInt=setInterval(function(){
			if(strTime-cDownCtrl > 0){
				$(".update-time").html((strTime-cDownCtrl));
				cDownCtrl++;
			}else{
				$(".update-time").html((strTime-cDownCtrl));
				cDownCtrl=1;
			}
		},1000);

	}
	function RTGraph(){
		if(stopAjax){return false;}
		arr=[];
		xmin=0;
		xmax=0;
		//$(".grafica1-frame div").fadeOut(300,function(){$(".grafica1-frame").html('');});
		$(".grafica1-frame").html('');
		$.ajax({
			url:'scripts/data.php',
			type:'POST',
			async:true,
			data:params.searchParams,
			success:function(r){
				//console.log(r.data);
				//pasa todo los datos a las vairables arr, xAxisArr y yAxisArr
				$.each(r.data,function(plotId,v){
					arr=json2arr(v);
					//alert(plotId);
					$('<div id="'+plotId+'" ></div>').appendTo(".grafica1-frame");
					//console.log(arr);
					charts[plotId]=$.jqplot(plotId,arr,{
						// Turns on animatino for all series in this plot.
				        animate: animacion,
				        // Will animate plot on calls to plot1.replot({resetAxes:true})
				        animateReplot: animacion,
						seriesDefaults: options.objSeriesDefaults,
						axes:options.axes,
						highlighter: {
				            show: true, 
				            showLabel: true, 
				            tooltipAxes: 'x, y',
				            sizeAdjust: 7.5 , tooltipLocation : 'ne'
				        },
				        cursor: {
				            show: true,
				            zoom: true,
				            looseZoom: true,
				            showTooltip: false
				        },
   						series:[
       						{color: rndHexColor()},
       					],
       					title:v.nombre, // el nomnbre de la grafica desde los datos
					});
				});
			}
		});
	}

	function ploteo(){
		//graficamos para ver los cambios
		RTGraph();

		//limpiamos los intervalos para on crear otros
		clearInterval(_updateFn);
		clearInterval(_countDownInt);

		//creamos de nuevo los intervalos
		countDown(updateInterval);
		_updateFn=setInterval(function(){RTGraph();},updateInterval);
	}

	function json2arr(obj){ //para el bucle $.each
		arrTmp=[];
		$.each(obj.serie,function(i,v){
			arrTmp.push([i,v*1]);
		});
		xmin=obj.xmin;
		xmax=obj.xmax;
		ymin=obj.ymin;
		ymax=obj.ymax;
		return [arrTmp];
	}

	function json2arrTJ(obj){ //todo junto
		console.log(obj);
		arrTmp=[];
		$.each(obj,function(i,v){
			$.each(v.serie,function(ii,vv){
				arrTmp.push([ii*1,vv*1]);
			});
			xmin=v.xmin;
			xmax=v.xmax;
			ymin=v.ymin;
			ymax=v.ymax;
		});
		arr.push(arrTmp);
	}

	function rndHexColor(){
		return '#'+Math.floor(Math.random()*16777215).toString(16);
	}
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
		</select>
	</div>
	<div><label>Activar / Desactivar Animacion</label><button class="animacion-btn">Activar/Desactivar</button></div>
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
	<div class="grafica1-frame">
	</div>
</body>
</html>
