// codigo javascript para inicializar las gráficas
(function(){
	$.getScript("jqplot/plugins/jqplot.logAxisRenderer.min.js");
	$.getScript("jqplot/plugins/jqplot.canvasTextRenderer.min.js");
	$.getScript("jqplot/plugins/jqplot.canvasAxisLabelRenderer.min.js");
	$.getScript("jqplot/plugins/jqplot.canvasAxisTickRenderer.min.js");
	$.getScript("jqplot/plugins/jqplot.dateAxisRenderer.min.js");
	$.getScript("jqplot/plugins/jqplot.categoryAxisRenderer.min.js");
	$.getScript("jqplot/plugins/jqplot.barRenderer.min.js");
	$.getScript("jqplot/plugins/jqplot.pointLabels.min.js");
	$.getScript("jqplot/plugins/jqplot.highlighter.js");
	$.getScript("jqplot/plugins/jqplot.cursor.js");
	$.getScript("jqplot/plugins/jqplot.trendline.js");
}());

var mes=["01","02","03","04","05","06","07","08","09","10","11","12"];
var date = new Date();
var mesini = new Date(date.getFullYear(), date.getMonth() - 2, 1);
var mesfin = new Date(date.getFullYear(), date.getMonth() + 1, 0);
fini=mesini.getFullYear()+"-"+mes[mesini.getMonth()]+"-"+poneCeros(mesini.getDate(),2);
ffin=mesfin.getFullYear()+"-"+mes[mesfin.getMonth()]+"-"+poneCeros(mesfin.getDate(),2);
var rangoFecha=[fini,ffin];
var date=new Date();
var charts=[];
var seriesCnt=0;
var dataCharts;
var ajaxParams;
var render="lineas";
var options={series:[],objSeriesDefaults:{},axes:{}};
var arr=[];
var xmin=0;
var xmax=0;
var graficar;
var updateInterval=10000;
var countDownInt;
var cDownCtrl=1;
var resizeTimeout;
var animacion=false;
var dataSet=false;
var _countDownInt,_updateFn;
var trendline=false;

/*Sección de las funciones*/

function poneCeros(numero,posiciones){
	tmpStr=digitos=numero.toString();
	digitos=digitos.length;	
	for (var i = 0; i < (digitos-posiciones); i++) {
		//se coloca un 0 al principio por n posiciones
		tmpStr="0"+tmpStr;
	};

	return tmpStr;
}

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
		case 'barrasylinea':
			options={
				series:[
					{
						renderer: $.jqplot.BarRenderer,
						color:rndHexColor(),
					},
					{
						color:rndHexColor(),
					}
				],
				objSeriesDefaults:{
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
			seriesTmp=[];
			for (var i = 0; i <= seriesCnt; i++) {
				
			};
			options={
				series:[
					{
						color:rndHexColor(),
					},
					{
						color:rndHexColor(),
					}
				],
				objSeriesDefaults:{
        			pointLabels: { show: false },
					rendererOptions: {
					    smooth: false,
					    animation: {
					        show: animacion,
					        speed: 1200,
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

function activatePlot(gParam){
	clearInterval(graficar);
	gParam=(typeof gParam != 'undefined') ? gParam : {};
	graficar=setInterval(function(){if(dataSet){ploteo(gParam);}},500);
}

function stopPlot(){
	clearInterval(graficar);
}

function RTGraph(params){
	if(!dataSet){return false;}

	dataSet=false; //inmediatamente cambia a dataSet false para que no vuelva a graficar rapidamente
	params=(typeof params != 'undefined') ? params : {};
	cfg={
		gTarget:"."+((typeof params.gTarget != 'undefined') ? params.gTarget : 'grafica-frame'),
		tipo:render,
	}

	if(!$(".grafica-frame")){$('<div class="grafica-frame"></div>').appendTo("body");}

	arr=[];
	xmin=0;
	xmax=0;
	

	$(cfg.gTarget).html('');
	$.each(dataCharts,function(plotId,v){
		arr=json2arr(v);

		$.jqplot.config.enablePlugins = trendline;
		selectRenderer(render);

		$('<div id="'+plotId+'" ></div>').appendTo(cfg.gTarget);
		//console.log(arr);
		charts[plotId]=$.jqplot(plotId,arr.data,{
			title:v.nombre, // el nomnbre de la grafica desde los datos
			axes:options.axes,
	        seriesDefaults: options.objSeriesDefaults,
			series:options.series,
			// Turns on animatino for all series in this plot.
	        animate: animacion,
	        // Will animate plot on calls to plot1.replot({resetAxes:true})
	        animateReplot: animacion,
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
		});
	});
}

function data2plot(params){
	$.ajax({
		url:'scripts/data.php',
		type:'POST',
		data:params.searchParams,
		success:function(r){
			//console.log(r.data);
			if(!r.err){
				dataCharts=r.data;
				dataSet=true;
				activatePlot();
			}
		}
	});
}

function ploteo(gParam){
	gParam=(typeof gParam != 'undefined') ? gParam : {};
	//graficamos para ver los cambios
	RTGraph(gParam);

	//limpiamos los intervalos para on crear otros
	clearInterval(_updateFn);
	clearInterval(_countDownInt);

	//creamos de nuevo los intervalos
	countDown(updateInterval);
	_updateFn=setInterval(function(){RTGraph(gParam);},updateInterval);
}

function json2arr(obj){ //para el bucle $.each
	plotData={};
	serieTmp=[];
	serieOpts=[];
	series=obj.serie;
	selectRenderer(obj.tipo);
	//titulo=obj.titulo;
	//xmin=obj.xmin;
	//xmax=obj.xmax;
	//ymin=obj.ymin;
	//ymax=obj.ymax;
	seriesCnt=series.length;
	$.each(series,function(i,v){
		//como siempre tendrá un indice la serie para indicar por cada plot el id de la serie
		arrTmp=[];
		$.each(v,function(ii,vv){			
			arrTmp.push([ii,vv*1]);
		});
		serieTmp.push(arrTmp);
	});	
	plotData.data=serieTmp;
	return plotData;
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

$(window).resize(function(e){
	clearTimeout(resizeTimeout);
	resizeTimeout=setTimeout(function(){ ploteo(); }, 100);
});

$(document).ready(function(e){

	// daemon to check if esc is pressed
	$(window).keyup(function(e){
		// checar la tecla presionada
		//alert(e.keyCode);
		switch(e.keyCode){
			case 27:
				clearInterval(_countDownInt);
			break;
			case 13:
				//console.log(e.target.nodeName);
				if(e.target.nodeName=="BODY"){
					data2plot(ajaxParams);
					ploteo();
				}
			break;
		}
	});

	//activar/desactivar animación
	$(".animacion-btn").click(function(e){
		animacion=!animacion;
		dataSet=true;
		//resetea todo el plot
		setTimeout(ploteo(),100);

		//alert(animacion);
	});

	//activar/desactivar animación
	$(".trendline-btn").click(function(e){
		trendline=!trendline;
		dataSet=true;
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
		dataSet=true;
		//resetea todo el plot
		setTimeout(ploteo(),100);
	});

	// select del tipo de graficas
	$(".select-renderer").change(function(e){
		render=$(this).val();
		//alert(rend);
		dataSet=true;
		setTimeout(ploteo(),100);
	});

	//cambiar parametros de fecha
	$(".filtro-fecha").click(function(e){
		//se leen los parametros y se modifican las actualizaciones

		rangoFecha[0]=$(".fechaInicio").val();
		rangoFecha[1]=$(".fechaFin").val();

		//resetea todo el plot
		params.searchParams={
			fechaInicio:rangoFecha[0],
			fechaFin:rangoFecha[1],
		};
		dataSet=true;
		setTimeout(ploteo(),100);
	});
});