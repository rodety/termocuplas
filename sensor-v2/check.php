<?php
	require("database.php");
	$m = parse_ini_file($config);
	$m['nsensores'] = $nsensores;
	$m['sensores_habilitados'] = $sensores_habilitados;
	print json_encode($m);
?>
