<?php


require 'funcs/conexion.php';

$id = $mysqli->real_escape_string($_POST['id_puerperio']);

$sql = "SELECT id_puerperio, p_fecha, p_peso, p_sig_sin, p_medicamentos, p_enfermedades_pre, p_plan_seguridad, fk_id_tarjeta FROM puerperio WHERE id_puerperio = $id LIMIT 1";
$resultado = $mysqli->query($sql);
$rows = $resultado->num_rows;

$puerperio = [];

if ($rows > 0) {
    $puerperio = $resultado->fetch_array();
}

echo json_encode($puerperio, JSON_UNESCAPED_UNICODE);
