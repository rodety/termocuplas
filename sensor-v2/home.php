<?php
	require("database.php");
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<title>	Sistema de Sensores </title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<script src="jquery-1.11.1.min.js"></script>
		<script src="highcharts-custom.js"></script>

		<link rel="stylesheet" href="jquery-ui.css">
		<link rel="stylesheet" href="style.css">

		<script src="jquery-ui.js"></script>
		<script>
			function refreshTime()
			{
				init();
				setInterval("init();",refresco*1000);
			}

			var intervalo;
			intervalo=<?php echo isset($_GET['intervalo']) ? $_GET['intervalo'] : 60; ?>;

			var nvalues;
			nvalues=24*(60/intervalo);
					
			$(function() {
				$.datepicker.regional['es'] = 
				{
					closeText: 'Cerrar', 
					prevText: 'Previo', 
					nextText: 'Próximo',

					monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
					'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
					monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun',
					'Jul','Ago','Sep','Oct','Nov','Dic'],
					monthStatus: 'Ver otro mes', yearStatus: 'Ver otro año',
					dayNames: ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'],
					dayNamesShort: ['Dom','Lun','Mar','Mie','Jue','Vie','Sáb'],
					dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sa'],
					dateFormat: 'dd/mm/yy', firstDay: 0, 
					initStatus: 'Selecciona la fecha', isRTL: false
				};

				$.datepicker.setDefaults($.datepicker.regional['es']);
 
				$("#datepicker").datepicker({
					dateFormat: 'yy-mm-dd',
					onSelect: function(dateText) {
						init();
					  }
					});

				$( "input:text" ).addClass("ui-widget ui-widget-content ui-widget-header ui-corner-all");
				
				$( "input[type=submit], a, button" )
					.button()
					.click(function( event ) {
					//event.preventDefault();
				});

				var today = new Date();
				var dd = today.getDate();
				var mm = today.getMonth()+1;
				var yyyy = today.getFullYear();

				if(dd<10) {
				    dd='0'+dd
				} 

				if(mm<10) {
				    mm='0'+mm
				} 

				today = yyyy+'-'+mm+'-'+dd;

				$('input[name=fecha]').val(today);

				init();
				
				$('#wrapper').bind('change',function()
				{
					checkVisible();
				});
			});
		</script>

		<script src="chart.js"></script>  
	</head>
	<body onload="javascript:refreshTime();">
		<span style="font-size:14px;">La página se refresca automáticamente cada <?php echo $refresco; ?> segundos.</span><br />
		<table WIDTH=75%><tr><td VALIGN=TOP>
		<img src="logo_ucsm.jpg" width="215px" height="235YYpx" alt="Logo UCSM"> </td>
		<td> <div align="center" style="padding: 0px;">
			<span style="font-weight:bold; font-size:16px;">Opciones de visualización</span><br /><br />
			<div style="border: 1px solid #d0d0d0; width:60%; padding: 5px 5px 5px 5px;">
				<div align="center">
					<div style="text-align: left; width: 50%">
					<form name="loadZoom" action="home.php" method="GET">
						<p>
							<label for="datepicker" class="labelui">Fecha:</label>
							<input type="text" id="datepicker" name='fecha' value="2014-01-01">
						</p>
						<p>
							<label for="spinIntervalo" class="labelui">Intervalo:</label>
							<input id="spinIntervalo" name="intervalo" value=<?php echo isset($_GET['intervalo']) ? $_GET['intervalo'] : 60; ?>> minutos
							<div align="center">
								<button>Aplicar</button>
								</form>
							</div>
						</p>
					</div>
					<br />
					<span style="font-size:14px;">Seleccione los sensores que desee visualizar:</span><br /><br />
					<div id="wrapper">
						<?php
							try
							{
								$statement = $conn->prepare("select * from Sensor where habilitado=true");
								$statement->execute();
								$data = array();

								$cont=1;
								while($row = $statement->fetch())
								{
									echo "&nbsp; <label for=\"check$row[0]\">Sensor $row[0]</label> <input type=\"checkbox\" id=\"check$row[0]\" checked>";
									if($cont%6==0)
										echo "<br /><br />";
									$cont++;
								}
							}
							catch(PDOException $Exception)
							{
								echo $Exception->getMessage();
							}
						?>
					</div>
					<br />
					<a onclick="selectAll();">Seleccionar todos</a> <a onclick="selectNone();">Deseleccionar todos</a>
					<br /><br />
						<form name="restart" action="home.php" method="GET">
							<button>Reiniciar</button>
						</form>
					</div>
				</div>
				<br />
				<button onclick="javascript:location.href='config.php'">Configuración</button>
			</div></td></tr></table>
		<br />
		<div id="container" style="min-width: 310px; height: 400px; width: 96%; align: center; margin: 0 auto"></div>
		<?php
			try
			{
				$statement = $conn->prepare("select * from Sensor where habilitado=true");
				$statement->execute();
				while($row = $statement->fetch())
				{
					print "<div id=\"container$row[0]\" style=\"min-width: 310px; height: 400px; width: 96%; align: center; margin: 0 auto\"></div>\n";
				}
			}
			catch(PDOException $Exception)
			{
				echo $Exception->getMessage();
			}
		?>
	</body>
</html>
