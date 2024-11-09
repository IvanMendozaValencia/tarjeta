<?php



session_start();

require 'funcs/conexion.php';
$id_periodo_lactancia = $mysqli->real_escape_string($_POST['id_periodo_lactancia']);
$id_tarjeta = $mysqli->real_escape_string($_POST['fk_id_tarjeta']); 
$pl_fecha = $mysqli->real_escape_string($_POST['pl_fecha']); 
$pl_peso = $mysqli->real_escape_string($_POST['pl_peso']); 
$pl_lac_mat_exc = $mysqli->real_escape_string($_POST['pl_lac_mat_exc']);
$pl_leche_mat_b24x = $mysqli->real_escape_string($_POST['pl_leche_mat_b24x']); 
$pl_sig_sin = $mysqli->real_escape_string($_POST['pl_sig_sin']);
$pl_medicamentos = $mysqli->real_escape_string($_POST['pl_medicamentos']); 
$pl_observaciones = $mysqli->real_escape_string($_POST['pl_observaciones']); 

$sql = "UPDATE periodo_lactancia SET pl_fecha='$pl_fecha', pl_peso='$pl_peso', pl_lac_mat_exc='$pl_lac_mat_exc', pl_leche_mat_b24x='$pl_leche_mat_b24x', pl_sig_sin='$pl_sig_sin', pl_medicamentos='$pl_medicamentos', pl_observaciones='$pl_observaciones' WHERE id_periodo_lactancia = '$id_periodo_lactancia'";

  if ($mysqli->query($sql)){

    $_SESSION['color'] = "success";
    $_SESSION['msg'] = "Registro actualizado";

  }

  header("Location: periodolactancia.php?id_tarjeta=$id_tarjeta");


