<?php



session_start();

require 'funcs/conexion.php';

$id_tarjeta = $mysqli->real_escape_string($_POST['id_tarjeta']);
$fecha_baja_emb = $mysqli->real_escape_string($_POST['fecha_baja_emb']);
$motivo_baja_emb = $mysqli->real_escape_string($_POST['motivo_baja_emb']);

$sql = "UPDATE datos_identificacion SET 
fecha_baja_emb = '$fecha_baja_emb',
motivo_baja_emb = '$motivo_baja_emb'

WHERE id_tarjeta = '$id_tarjeta'";

  if ($mysqli->query($sql)){

    $_SESSION['color'] = "success";
    $_SESSION['msg'] = "Registro actualizado";

  }


  header("Location: editaTarjeta.php?id=$id_tarjeta");
