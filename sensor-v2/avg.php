<?php		   
	require("database.php");
	$arr = parse_ini_file($config);

	$fecha=isset($_GET['fecha']) ? $_GET['fecha'] : '2014-01-01';
	$intervalo=isset($_GET['intervalo']) ? $_GET['intervalo'] : 60;
	$nvalues=24*(60/$intervalo);

	try
	{
		$statement = $conn->prepare("select * from Sensor where habilitado=true");
		$statement->execute();
		$habilitados = array();

		$numHabilitados=0;
		while($row = $statement->fetch())
		{
			if($row[4])
			{
				$habilitados[] = true;
				$numHabilitados++;
			}
			else
				$habilitados[] = false;
		}
							
		$rowsavg=array_fill(0,$nvalues,null);
		$stddev=array_fill(0,$nvalues,null);

		$data = array();

		for($i = 0; $i<$nsensores; $i++)
		{
			if($habilitados[$i]==true)
			{
				$idSensor=$i+1;

				if($intervalo==60)
				{
					$statement = $conn->prepare("select floor(UNIX_TIMESTAMP(CONVERT_TZ(DATE_ADD(:fecha,INTERVAL hour(fecha) hour),'+5:00','GMT'))*1000) as UTC, avg(valor) as Valor from Temperatura where Sensor_idSensor=:idSensor and fecha >= :fecha and fecha < :fecha + INTERVAL 1 DAY group by hour(fecha),Sensor_idSensor");
					$statement->execute(array(':idSensor' => $idSensor,':fecha' => $fecha));
				}
				else
				{
					$statement = $conn->prepare("select floor(UNIX_TIMESTAMP(CONVERT_TZ(DATE_ADD(:fecha,INTERVAL :intervalo*((60/:intervalo)*HOUR(fecha)+FLOOR(MINUTE(fecha)/:intervalo)) MINUTE), '+5:00', 'GMT'))*1000) as axtitle,avg(valor) as Valor from Temperatura where Sensor_idSensor=:idSensor and fecha >= :fecha and fecha < :fecha + INTERVAL 1 DAY group by axtitle");
					$statement->execute(array(':idSensor' => $idSensor,':fecha' => $fecha,':intervalo' => $intervalo));
				}

				$rows = array();

				while($row = $statement->fetch())
				{
					$rows[] = array($row[0],floatval($row[1]));
				}

				$data[$i]=$rows;

				for($j = 0; $j<$nvalues; $j++)
				{
					$rowsavg[$j]+=$rows[$j][1];
				}
			}
		}

		for($j = 0; $j<$nvalues; $j++)
		{
			$rowsavg[$j]/=$numHabilitados;
		}
		
		for($i = 0; $i<$nsensores; $i++)
		{
			for($j = 0; $j<$numHabilitados; $j++)
			{
				$stddev[$j]+=($data[$i][$j][1]-$rowsavg[$j])*($data[$i][$j][1]-$rowsavg[$j]);
			}
		}

		for($j = 0; $j<$numHabilitados; $j++)
		{
			$stddev[$j]*=(1/floatval($numHabilitados));
			$stddev[$j]=sqrt($stddev[$j]);
		}

		$response = array('avg'=>$rowsavg,'stddev'=>$stddev,'data'=>$data);
		print json_encode($response);
	}
	catch(PDOException $Exception)
	{
		echo $Exception->getMessage();
	}
?>
