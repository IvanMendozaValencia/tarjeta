<?php



session_start();

require 'funcs/conexion.php';

$id_tarjeta = $mysqli->real_escape_string($_POST['id_tarjeta']);
$pl_fecha_baja = $mysqli->real_escape_string($_POST['pl_fecha_baja']);
$pl_termino_lact = $mysqli->real_escape_string($_POST['pl_termino_lact']);
$pl_otro = $mysqli->real_escape_string($_POST['pl_otro']);


$sql = "UPDATE datos_identificacion SET 
pl_fecha_baja='$pl_fecha_baja', pl_termino_lact='$pl_termino_lact', pl_otro='$pl_otro'

WHERE id_tarjeta = '$id_tarjeta'";

  if ($mysqli->query($sql)){

    $_SESSION['color'] = "success";
    $_SESSION['msg'] = "Registro actualizado";

  }

header("Location: periodolactancia.php?id_tarjeta=$id_tarjeta");


