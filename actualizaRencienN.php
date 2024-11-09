<?php



session_start();

require 'funcs/conexion.php';

$id_tarjeta = $mysqli->real_escape_string($_POST['id_tarjeta']);
$rn_unico = $mysqli->real_escape_string($_POST['rn_unico']);
$rn_gemelar = $mysqli->real_escape_string($_POST['rn_gemelar']);
$rn_tres_mas = $mysqli->real_escape_string($_POST['rn_tres_mas']);
$rn_apego_seno_amt = $mysqli->real_escape_string($_POST['rn_apego_seno_amt']);
$rn_egreso_lac_mat_exc = $mysqli->real_escape_string($_POST['rn_egreso_lac_mat_exc']);
$rn_sucedaneo_leche_mat_b24x = $mysqli->real_escape_string($_POST['rn_sucedaneo_leche_mat_b24x']);
$rn_tratamiento_b24x = $mysqli->real_escape_string($_POST['rn_tratamiento_b24x']); 
$rn_tratamiento_a539 = $mysqli->real_escape_string($_POST['rn_tratamiento_a539']); 

$sql = "UPDATE datos_identificacion SET 
rn_unico = '$rn_unico', 
rn_gemelar = '$rn_gemelar', 
rn_tres_mas = '$rn_tres_mas', 
rn_apego_seno_amt = '$rn_apego_seno_amt', 
rn_egreso_lac_mat_exc = '$rn_egreso_lac_mat_exc', 
rn_sucedaneo_leche_mat_b24x = '$rn_sucedaneo_leche_mat_b24x', 
rn_tratamiento_b24x = '$rn_tratamiento_b24x', 
rn_tratamiento_a539 = '$rn_tratamiento_a539'

WHERE id_tarjeta = '$id_tarjeta'";

  if ($mysqli->query($sql)){

    $_SESSION['color'] = "success";
    $_SESSION['msg'] = "Registro actualizado";

  }

header("Location: recienNacidos.php?id_tarjeta=$id_tarjeta");


