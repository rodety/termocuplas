<?php
	require("database.php");

	try
	{
		$statement = $conn->prepare("select count(*)-1 from Sensor");
		$statement->execute();
		$row = $statement->fetch();
		echo $row[0];
	}
	catch(PDOException $Exception)
	{
		echo $Exception->getMessage();
	}
?>
