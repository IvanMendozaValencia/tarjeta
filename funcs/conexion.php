<?php
	//$mysqli=new mysqli("localhost","id22398391_ivan","130613Iv@n","id22398391_ssa");
	$mysqli=new mysqli("localhost","root","","cnatal");
	if(mysqli_connect_errno()){
		echo 'Conexion Fallida : ', mysqli_connect_error();
		exit();
	}
	/*
	<?php
	$mysqli=new mysqli("localhost","id21251916_sistimv","Iv@n0369","id21251916_bd_mujer");
	if(mysqli_connect_errno()){
		echo 'Conexion Fallida : ', mysqli_connect_error();
		exit();
	}
	
?>

	*/ 
?>