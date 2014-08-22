<?php
	$host = 'localhost';
	$user = 'root';
	$pass = 'blf278';
	$db = 'sensor';

	$config = 'config.ini';
	$nsensores = 0;
	$sensores_habilitados = array();

	$m = parse_ini_file($config);
	$refresco = $m['refresco'];

	try
	{
		$conn = new PDO("mysql:host=$host;dbname=$db",$user,$pass);

		$statement = $conn->prepare("select * from Sensor where habilitado=true");
		$statement->execute();
			
		while($row = $statement->fetch())
		{
			$sensores_habilitados[] = $row[0];
		}
		
		$nsensores = $statement->rowCount();
	}
	catch(PDOException $Exception)
	{
		echo $Exception->getMessage();
	}
?>
