<?php


require 'funcs/conexion.php';

$id = $mysqli->real_escape_string($_POST['id_recien_nacida']);

$sql = "SELECT id_recien_nacida, rn_vivo, rn_muerto, rn_sexo, rn_peso, rn_talla, rn_apgar, rn_silverman, rn_tamiz_metabolico, rn_tamiz_auditivo, fk_id_tarjeta FROM recien_nacida WHERE id_recien_nacida = $id LIMIT 1";
$resultado = $mysqli->query($sql);
$rows = $resultado->num_rows;

$reciennac = [];

if ($rows > 0) {
    $reciennac = $resultado->fetch_array();
}

echo json_encode($reciennac, JSON_UNESCAPED_UNICODE);
