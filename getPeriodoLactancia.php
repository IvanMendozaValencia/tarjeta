<?php

require 'funcs/conexion.php';

$id = $mysqli->real_escape_string($_POST['id_periodo_lactancia']);

$sql = "SELECT id_periodo_lactancia, pl_fecha, pl_peso, pl_lac_mat_exc, pl_leche_mat_b24x, pl_sig_sin, pl_medicamentos, pl_observaciones, fk_id_tarjeta FROM periodo_lactancia WHERE id_periodo_lactancia = $id LIMIT 1";
$resultado = $mysqli->query($sql);
$rows = $resultado->num_rows;

$lactancia = [];

if ($rows > 0) {
    $lactancia = $resultado->fetch_array();
}

echo json_encode($lactancia, JSON_UNESCAPED_UNICODE);