<?php


require 'funcs/conexion.php';

$id = $mysqli->real_escape_string($_POST['id_visita']);

$sql = "SELECT id_visita, fecha_visita, result_visita, personal_visita, fk_visita_tarjeta FROM visitas WHERE id_visita = $id LIMIT 1";
$resultado = $mysqli->query($sql);
$rows = $resultado->num_rows;

$visitas = [];

if ($rows > 0) {
    $visitas = $resultado->fetch_array();
}

echo json_encode($visitas, JSON_UNESCAPED_UNICODE);
