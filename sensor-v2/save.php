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

	/*foreach($_POST as $key=>$value)
	{
		if(substr($key,0,6) == 'sensor')
			echo substr($key,6)." ".$value." <br>";
		else if(substr($key,0,6) == 'umbral')
			echo $key." ".$value." <br>";
		else if(substr($key,0,6) == 'porcen')
			echo $key." ".$value." <br>";
	}*/
	
	if(!empty($_POST))
	{
		try
		{
			$conn = new PDO("mysql:host=$host;dbname=$db",$user,$pass);

			$statement = $conn->prepare("select idSensor from Sensor");
			$statement->execute();
				
			while($row = $statement->fetch())
			{
				$idSensor=(int)$row[0];
				$query = $conn->prepare("UPDATE Sensor SET habilitado=? WHERE idSensor=?");

				if(isset($_POST['sensor'.$row[0]]))
					$query->execute(array(true,$idSensor));
				else
					$query->execute(array(false,$idSensor));
			}
		}
		catch(PDOException $Exception)
		{
			echo $Exception->getMessage();
		}

		$um=isset($_POST['umbral']) ? (float)$_POST['umbral'] : 0.0;
		$pr=isset($_POST['porcentaje']) ? (float)$_POST['porcentaje'] : 0.0;
		$arr = array('umbral'=>$um,'porcentaje'=>$pr);
		$data = array('parametros_sensor'=>$arr);
		write_ini($data,'config.ini');
	}

	header('Location: config.php?msg=1');
?>
