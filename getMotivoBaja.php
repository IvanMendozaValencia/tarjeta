<?php


require 'funcs/conexion.php';

$id = $mysqli->real_escape_string($_POST['id_tarjeta']);

$sql = "SELECT id_tarjeta, fecha_baja_emb, motivo_baja_emb FROM datos_identificacion WHERE id_tarjeta = $id LIMIT 1";
$resultado = $mysqli->query($sql);
$rows = $resultado->num_rows;

$bajas = [];

if ($rows > 0) {
    $bajas = $resultado->fetch_array();
}

echo json_encode($bajas, JSON_UNESCAPED_UNICODE);
