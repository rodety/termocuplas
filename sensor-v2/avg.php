<?php		   
	require("database.php");
	$arr = parse_ini_file($config);

	$zoom=isset($_GET['zoom']) ? $_GET['zoom'] : 'dia';
	$fecha=isset($_GET['fecha']) ? $_GET['fecha'] : '2014-01-01';
	$hora=isset($_GET['hora']) ? $_GET['hora'] : 0;
	
	$nvalues=24;
	if($zoom=='hora')
		$nvalues=60;

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
		$idx = array();

		for($i = 0; $i<$nsensores; $i++)
		{
			if($habilitados[$i]==true)
			{
				$idSensor=$i+1;

				if($zoom=='dia')
				{
					$statement = $conn->prepare("select avg(valor) as Valor,hour(fecha) from Temperatura where Sensor_idSensor=:idSensor and fecha >= :fecha and fecha < :fecha + INTERVAL 1 DAY group by hour(fecha),Sensor_idSensor");
					$statement->execute(array(':idSensor' => $idSensor,':fecha' => $fecha));
				}
				else if($zoom=='hora')
				{
					$statement = $conn->prepare("select avg(valor) as Valor,minute(fecha) from 
					Temperatura where Sensor_idSensor=:idSensor and fecha >= DATE_ADD(:fecha, INTERVAL :hora HOUR) and 
					fecha < DATE_ADD(:fecha, INTERVAL :hora+1 HOUR) group by minute(fecha),Sensor_idSensor");
					$statement->execute(array(':idSensor' => $idSensor,':fecha' => $fecha,':hora' => $hora));
				}

				$rows = array();
				$idxs = array();

				while($row = $statement->fetch())
				{
					$rows[] = floatval($row[0]);
					$idxs[] = intval($row[1]);
				}

				while(count($rows)<$nvalues)
				{
					array_push($rows,0.0);
					array_push($idxs,$nvalues-1);
				}

				$data[$i]=$rows;
				$idx[$i]=$idxs;

				for($j = 0; $j<$nvalues; $j++)
				{
					$rowsavg[$idxs[$j]]+=$rows[$j];
				}
			}
		}

		for($j = 0; $j<$nvalues; $j++)
		{
			$rowsavg[$idxs[$j]]/=$numHabilitados;
		}
		
		for($i = 0; $i<$nsensores; $i++)
		{
			for($j = 0; $j<$numHabilitados; $j++)
			{
				$stddev[$idxs[$j]]+=($data[$i][$j]-$rowsavg[$idxs[$j]])*($data[$i][$j]-$rowsavg[$idxs[$j]]);
			}
		}

		for($j = 0; $j<$numHabilitados; $j++)
		{
			$stddev[$idxs[$j]]*=(1/floatval($numHabilitados));
			$stddev[$idxs[$j]]=sqrt($stddev[$idxs[$j]]);
		}

		$response = array('avg'=>$rowsavg,'stddev'=>$stddev,'data'=>$data,'idx'=>$idx);
		print json_encode($response);
	}
	catch(PDOException $Exception)
	{
		echo $Exception->getMessage();
	}
?>
