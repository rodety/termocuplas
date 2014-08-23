<?php
	require("database.php");

	//Dar permisos 777 a este archivo
	function write_ini($assoc_arr,$path)
	{
		$content = "";
	  
		foreach($assoc_arr as $key=>$elem)
		{
			$content .= "[".$key."]\n";
			foreach($elem as $key2=>$elem2)
			{
				$content .= $key2." = ".$elem2."\n";
			}
		}

		if(!$handle = fopen($path,'w'))
		{
			return false;
		}

		if(!fwrite($handle, $content))
		{
			return false;
		} 
		fclose($handle);
	}

	if(!empty($_POST))
	{
		try
		{
			$conn = new PDO("mysql:host=$host;dbname=$db",$user,$pass);

			$statement = $conn->prepare("select idSensor from Sensor");
			$statement->execute();
				
			while($row = $statement->fetch())
			{
				$idSensor=$row[0];
				$query = $conn->prepare("UPDATE Sensor SET habilitado=:habilitado WHERE idSensor=:idSensor");

				if(isset($_POST['sensor'.$row[0]]))
					$query->execute(array(':habilitado' => 1,':idSensor' => $idSensor));
				else
					$query->execute(array(':habilitado' => 0,':idSensor' => $idSensor));
			}
		}
		catch(PDOException $Exception)
		{
			echo $Exception->getMessage();
		}

		$um=isset($_POST['umbral']) ? (float)$_POST['umbral'] : 0.0;
		$pr=isset($_POST['porcentaje']) ? (float)$_POST['porcentaje'] : 0.0;
		$rf=isset($_POST['refresco']) ? (float)$_POST['refresco'] : 0.0;
		$arr = array('umbral'=>$um,'porcentaje'=>$pr,'refresco'=>$rf);
		$data = array('parametros_sensor'=>$arr);
		write_ini($data,'config.ini');
	}

	header('Location: config.php?msg=1');
?>
