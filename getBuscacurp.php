<?php

require 'funcs/conexion.php';
$curp    = $_REQUEST['curp'];

//Verificando si existe algun cliente en bd ya con dicha cedula asignada
//Preparamos un arreglo que es el que contendrá toda la información
$jsonData = array();
$sql   = ("SELECT curp FROM datos_identificacion WHERE curp='".$curp."' ");
$resultado = $mysqli->query($sql);
$rows = $resultado->num_rows;
$total = $resultado->fetch_array();

  //Validamos que la consulta haya retornado información
  if( $total <= 0 ){
    $jsonData['success'] = 0;
    $jsonData['message'] = '';
} else{
    //Si hay datos entonces retornas algo
    $jsonData['success'] = 1;
    $jsonData['message'] = '<p style="color:red;">Ya existe la CURP <strong>('.$curp.')<strong></p>';
  }

//Mostrando mi respuesta en formato Json
header('Content-type: application/json; charset=utf-8');
echo json_encode( $jsonData );
?>