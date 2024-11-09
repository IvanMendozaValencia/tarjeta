<?php


require 'funcs/conexion.php';

$id = $mysqli->real_escape_string($_POST['id_tarjeta']);


$sql = "SELECT id_tarjeta FROM datos_identificacion WHERE id_tarjeta = $id limit 1";
$resultadoidetarjeta = $mysqli->query($sql);
$rows = $resultadoidetarjeta->num_rows;


$idtarjeta = [];

if ($rows > 0) {
    $idtarjeta = $resultadoidetarjeta->fetch_array();
}else{
    $sql = "SELECT id_tarjeta FROM datos_identificacion WHERE id_tarjeta = $id limit 1";
    $resultadoidetarjeta = $mysqli->query($sql);
    $rows = $resultadoidetarjeta->num_rows;
    $idtarjeta = $resultadoidetarjeta->fetch_array();
}

echo json_encode($idtarjeta, JSON_UNESCAPED_UNICODE);
