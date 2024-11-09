<?php

require 'funcs/conexion.php';
$clues    = $_REQUEST['clues_id'];

//Verificando si existe algun cliente en bd ya con dicha cedula asignada
//Preparamos un arreglo que es el que contendrá toda la información
$jsonData = array();
$sql   = ("SELECT clues FROM clues WHERE clues='".$clues."' ");
$resultado = $mysqli->query($sql);
$rows = $resultado->num_rows;
$total = $resultado->fetch_array();


  //Validamos que la consulta haya retornado información
  if( $total <= 0 ){
    $jsonData['success'] = 1;
    $jsonData['message'] = '<p style="color:red;">La clave CLUES no existe <strong>('.$clues.')<strong></p>';
} else{
    //Si hay datos entonces retornas algo
    $jsonData['success'] = 0;
    $jsonData['message'] = '';
  }

//Mostrando mi respuesta en formato Json
header('Content-type: application/json; charset=utf-8');
echo json_encode( $jsonData );
?>
