<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<title>	Sistema de Sensores </title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<script src="jquery-1.11.1.min.js"></script>

		<link rel="stylesheet" href="jquery-ui.css">
		<link rel="stylesheet" href="style.css">

		<script src="jquery-ui.js"></script>
		<script>
			var umbral;
			var nsensores;
			var sensores_habilitados;

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
			}

			$(function()
			{
				$.ajax(
				{
					url: 'check.php',
					dataType: 'json',
					async: false,
					success: function(data)
					{
						umbral=parseFloat(data.umbral);
						nsensores=parseInt(data.nsensores);
						sensores_habilitados=data.sensores_habilitados;
						$('#umbral').val(umbral);
					}
				});
			
				$( "input:text" ).addClass("ui-widget ui-widget-content ui-widget-header ui-corner-all");

				$( "input[type=submit], a, button" )
					.button()
					.click(function( event ) {
					//event.preventDefault();
				});

				$('#wrapper').bind('change',function()
				{
					checkVisible();
				});
			});
		</script>
	</head>
	<body>
		<div align="center" style="padding: 0px;">
			<span style="font-weight:bold; font-size:16px;">Configuración</span><br /><br />
			<form name="saveConfig" action="save.php" method="POST">
			<div style="border: 1px solid #d0d0d0; width:60%; padding: 20px 5px 5px 5px;">
				<span style="font-size:14px;">Seleccione los sensores que desee habilitar:</span><br /><br />
				<div id="wrapper">
				<?php
					require("database.php");

					try
					{
						$statement = $conn->prepare("select * from Sensor");
						$statement->execute();
						$data = array();

						$cont=1;
						while($row = $statement->fetch())
						{
							if($row[4])
								echo "&nbsp; <label for=\"check$row[0]\">Sensor $row[0]</label> <input type=\"checkbox\" id=\"check$row[0]\" name=\"sensor$row[0]\" checked>";
							else
								echo "&nbsp; <label for=\"check$row[0]\">Sensor $row[0]</label> <input type=\"checkbox\" id=\"check$row[0]\" name=\"sensor$row[0]\">";
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
				<p>
					<span style="font-size:14px;">Umbral:</span><br /><br />
					<label for="umbral">Valor:</label>
					<input type="text" id="umbral" name="umbral" style="text-align: right; padding-right:5px;" value=""><br />
				</p>
				<button>Guardar</button> 
			</div>
			</form>
			<br />
			<?php
				if(isset($_GET['msg']))
				{
					if($_GET['msg']=='1')
						echo "<span style=\"font-size:14px; color: blue;\">Configuración guardada correctamente!</span><br /><br />";
				}
			?>
			<button onclick="javascript:location.href='home.php'">Ir al inicio</button>
		</div>
	</body>
</html>
