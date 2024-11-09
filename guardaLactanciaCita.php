<?php



session_start();

require 'funcs/conexion.php';
$id_tarjeta = $mysqli->real_escape_string($_POST['id_tarjeta']); 

$pl_fecha = $mysqli->real_escape_string($_POST['pl_fecha']); 
$pl_peso = $mysqli->real_escape_string($_POST['pl_peso']); 
$pl_lac_mat_exc = $mysqli->real_escape_string($_POST['pl_lac_mat_exc']);
$pl_leche_mat_b24x = $mysqli->real_escape_string($_POST['pl_leche_mat_b24x']); 
$pl_sig_sin = $mysqli->real_escape_string($_POST['pl_sig_sin']);
$pl_medicamentos = $mysqli->real_escape_string($_POST['pl_medicamentos']); 
$pl_observaciones = $mysqli->real_escape_string($_POST['pl_observaciones']); 

$sql = "INSERT INTO periodo_lactancia (pl_fecha, pl_peso, pl_lac_mat_exc, pl_leche_mat_b24x, pl_sig_sin, pl_medicamentos, pl_observaciones, fk_id_tarjeta) VALUES ('$pl_fecha', '$pl_peso', '$pl_lac_mat_exc', '$pl_leche_mat_b24x', '$pl_sig_sin', '$pl_medicamentos', '$pl_observaciones', '$id_tarjeta')";

  if ($mysqli->query($sql)){

    $_SESSION['color'] = "success";
    $_SESSION['msg'] = "Registro guardado";

  }

  header("Location: periodolactancia.php?id_tarjeta=$id_tarjeta");

