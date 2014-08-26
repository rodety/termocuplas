<?php
/* 
Export MySQL to Excel using PHP
Author: Vlatko Zdrale, http://blog.zemoon.com

Look but don't touch :)
*/

	$dbHost	= 'localhost';			//	database host
	$dbUser	= 'root';		//	database user
	$dbPass	= 'blf278';		//	database password
	$dbName	= 'sensor'; 		//	database name
	$dbTable = 'Temperatura';

	$connection = @mysql_connect($dbHost, $dbUser, $dbPass) or die("Couldn't connect.");
	$db = mysql_select_db($dbName, $connection) or die("Couldn't select database.");

	$sql = "Select * from $dbTable";
	$result = @mysql_query($sql)	or die("Couldn't execute query:<br>".mysql_error().'<br>'.mysql_errno());

	header('Content-Type: application/vnd.ms-excel');	//define header info for browser
	header('Content-Disposition: attachment; filename='.$dbTable.'-'.date('Ymd').'.xls');
	header('Pragma: no-cache');
	header('Expires: 0');

	for ($i = 0; $i < mysql_num_fields($result); $i++)	 // show column names as names of MySQL fields
		echo mysql_field_name($result, $i)."\t";
	print("\n");

	while($row = mysql_fetch_row($result))
	{
		//set_time_limit(60); // you can enable this if you have lot of data
		$output = '';
		for($j=0; $j<mysql_num_fields($result); $j++)
		{
			if(!isset($row[$j]))
				$output .= "NULL\t";
			else
				$output .= "$row[$j]\t";
		}
		$output = preg_replace("/\r\n|\n\r|\n|\r/", ' ', $output);
		print(trim($output))."\t\n";
	}
?>

