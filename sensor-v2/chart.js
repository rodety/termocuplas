var avg = new Array(nvalues+1).join('0').split('').map(parseFloat);
var stddev = new Array(nvalues+1).join('0').split('').map(parseFloat);
var sensors_data;
var idx_data;
var umbral;
var porcentaje;
var nsensores;
var sensores_habilitados;

var ejex;
var titulo;

if(zoom=='dia')
{
	ejex='Horas';
	titulo='Promedio por hora';
}
else if(zoom=='hora')
{
	ejex='Minutos';
	titulo='Promedio por minuto';
}

function checkVisible()
{
	var i;
	for(i = 1; i < nsensores+1; ++i)
	{
		if(document.getElementById('check'+sensores_habilitados[i-1]).checked == true)
			$('#container'+sensores_habilitados[i-1]).css('display','inline');
		else
			$('#container'+sensores_habilitados[i-1]).css('display','none');
	}
	
	alert("Gráficos actualizados!");
}

function selectAll()
{
	var i;
	for(i = 1; i < nsensores+1; ++i)
	{
		document.getElementById('check'+sensores_habilitados[i-1]).checked = true;
	}
	checkVisible();
}

function selectNone()
{
	var i;
	for(i = 1; i < nsensores+1; ++i)
	{
		document.getElementById('check'+sensores_habilitados[i-1]).checked = false;
	}
	checkVisible();
}

function init()
{
	$.ajax(
	{
		url: 'avg.php',
		dataType: 'json',
		async: false,
		data:
		{
			fecha: $('#datepicker').datepicker({ dateFormat: 'dd-mm-yy' }).val(),
			zoom: zoom,
			hora: hora
		},
		success: function(data)
		{
			sensors_data=data.data;
			idx_data=data.idx;

			for(j = 0; j<data.avg.length; j++)
			{
				avg[j]=data.avg[j];
				stddev[j]=data.stddev[j];
			}
		}
	});

	$.ajax(
	{
		url: 'check.php',
		dataType: 'json',
		async: false,
		success: function(data)
		{
			umbral=parseFloat(data.umbral);
			porcentaje=parseFloat(data.porcentaje);
			nsensores=parseInt(data.nsensores);
			sensores_habilitados=data.sensores_habilitados;
		}
	});

	$("#spinHora").spinner({
        numberFormat: "d2",
        spin: function(event, ui)
        {
            if(ui.value > 23)
            {
                $(this).spinner("value", 0);
                return false;
            }
            else if (ui.value < 0)
            {
                $(this).spinner("value", 23);
                return false;
            }
        },
	});

	$("#spinSensor").spinner({
        numberFormat: "d2",
        spin: function(event, ui)
        {
            if(ui.value > nsensores)
            {
                $(this).spinner("value", 1);
                return false;
            }
            else if (ui.value < 1)
            {
                $(this).spinner("value", nsensores);
                return false;
            }
        },
	});

	var mainOptions={
		chart:{
			renderTo: 'container'
		},
		title: {
			text: 'Temperaturas por Sensor, día: '+$('#datepicker').datepicker({ dateFormat: 'dd-mm-yy' }).val(),
			x: -20 //center
		},
		subtitle: {
			text: titulo,
			x: -20
		},
		xAxis: {
			title:{
				text: ejex
			},
			gridLineWidth:0.5
		},
		yAxis: {
			title: {
				text: 'Grados Celsius (°C)'
			},
			plotLines: [{
				value: 0,
				width: 1,
				color: '#808080'
			}]
		},
		tooltip: {
			valueSuffix: '°C'
		},
		legend: {
			layout: 'vertical',
			align: 'right',
			verticalAlign: 'middle',
			borderWidth: 0
		}
	};

	var chart = new Highcharts.Chart(mainOptions);

	for(i = 1; i < nsensores+1; i++)
	{
		var data = sensors_data[i-1];
		var idx = idx_data[i-1];
		var dataAvg = [];

		for(j = 0; j < data.length; j++)
		{
			var mp = {};
			mp['x'] = idx[j];
			mp['y'] = data[j];
			dataAvg.push(mp);
		}
	
		chart.addSeries({
			name: 'Sensor '+sensores_habilitados[i-1],
			data: dataAvg
		});

		var dataColor = [];

		for(j = 0; j < data.length; j++)
		{
			var mp = {};
			mp['x'] = idx[j];
			mp['y'] = data[j];

			var marker = {};

			if(data[idx[j]]>(avg[j]+umbral+(porcentaje*avg[j])/100.0) || data[idx[j]]<(avg[j]-umbral-(porcentaje*avg[j])/100.0))
			{
				marker["fillColor"] = 'red';
			}
			else
			{
				marker["fillColor"] = 'blue';
			}

			mp["marker"] = marker;
			dataColor.push(mp);
		}

		var options = {
				title: {
					text: 'Sensor: #'+ sensores_habilitados[i-1] +', día: '+$('#datepicker').datepicker({ dateFormat: 'dd-mm-yy' }).val(),
					x: -20 //center
				},
				subtitle: {
					text: titulo,
					x: -20
				},
				xAxis: {
					title:{
						text: ejex
					},
					gridLineWidth:0.5
				},
				yAxis: {
					title: {
						text: 'Grados Celsius (°C)'
					},
					plotLines: [{
						value: 0,
						width: 1,
						color: '#808080'
					}]
				},
				tooltip: {
					valueSuffix: '°C'
				},
				legend: {
					layout: 'vertical',
					align: 'right',
					verticalAlign: 'middle',
					borderWidth: 0
				},
				series: [{
					name: 'Sensor '+sensores_habilitados[i-1],
					data: dataColor
					//,color: 'blue'
				},{
					name: 'Promedio',
					data: avg,
					color: '#CED8F6'
				}]
			};

		$('#container'+sensores_habilitados[i-1]).highcharts(options);
	}
}
