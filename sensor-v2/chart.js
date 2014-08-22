var avg = new Array(nvalues+1).join('0').split('').map(parseFloat);
var stddev = new Array(nvalues+1).join('0').split('').map(parseFloat);
var sensors_data;
var umbral;
var porcentaje;
var refresco;
var nsensores;
var sensores_habilitados;

var ejex;
var titulo;

if(intervalo==60)
{
	ejex='Horas';
	titulo='Promedio por hora';
}
else
{
	ejex='Minutos';
	titulo='Promedio por intervalo';
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
			intervalo: intervalo,
		},
		success: function(data)
		{
			sensors_data=data.data;

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
			refresco=parseInt(data.refresco);
			nsensores=parseInt(data.nsensores);
			sensores_habilitados=data.sensores_habilitados;
		}
	});

	$("#spinIntervalo").spinner({
        numberFormat: "d2",
        step: 5,
        spin: function(event, ui)
        {
           if(ui.value > 60)
            {
                $(this).spinner("value",5);
                return false;
            }
            else if(ui.value < 5)
            {
                $(this).spinner("value",30);
                return false;
            }
            else if(60%ui.value!=0)
            {
				var c=ui.value;
				while(60%c!=0)
					c+=5;

				$(this).spinner("value",c);
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
			renderTo: 'container',
			zoomType: 'x'
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
			type: 'datetime',
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
			headerFormat: '<b>{series.name}</b><br>',
			pointFormat: 'Hora: {point.x:%H:%M}, <b>{point.y:.2f} °C</b>'
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
		var dataData = [];
		
		for(j = 0; j < data.length; j++)
		{
			var mp = {};
			mp['x'] = data[j][0];
			mp['y'] = data[j][1];
			dataData.push(mp);
		}
		
		var d = $("#datepicker").val();	
		chart.addSeries({
			name: 'Sensor '+sensores_habilitados[i-1],
			pointStart: Date.UTC((Number(d.split("-")[0])), (Number(d.split("-")[1]) - 1), (Number(d.split("-")[2]))),
			pointInterval: 3600 * 1000,
			data: dataData
		});

		var dataColor = [];
		var dataAvg = [];

		for(j = 0; j < data.length; j++)
		{
			var mp = {};
			mp['x'] = data[j][0];
			mp['y'] = data[j][1];
			
			var mpavg = {};
			mpavg['x'] = data[j][0];
			mpavg['y'] = avg[j];

			var marker = {};

			if(data[j][1]>(avg[j]+umbral+(porcentaje*avg[j])/100.0) || data[j][1]<(avg[j]-umbral-(porcentaje*avg[j])/100.0))
			{
				marker["fillColor"] = 'red';
			}
			else
			{
				marker["fillColor"] = 'blue';
			}

			mp["marker"] = marker;
			dataColor.push(mp);
			dataAvg.push(mpavg);
		}

		var options = {
				chart:{
					zoomType: 'x'
				},
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
					type: 'datetime',
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
					headerFormat: '<b>{series.name}</b><br>',
					pointFormat: 'Hora: {point.x:%H:%M}, <b>{point.y:.2f} °C</b>'
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
				},{
					name: 'Promedio',
					data: dataAvg,
					pointStart: Date.UTC((Number(d.split("-")[0])), (Number(d.split("-")[1]) - 1), (Number(d.split("-")[2]))),
					pointInterval: 3600 * 1000,
					color: '#CED8F6'
				}]
			};

		$('#container'+sensores_habilitados[i-1]).highcharts(options);
	}
}
