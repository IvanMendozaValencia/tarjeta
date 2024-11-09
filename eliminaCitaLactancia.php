<?php



session_start();

require 'funcs/conexion.php';


$id_periodo_lactancia = $mysqli->real_escape_string($_POST['id_periodo_lactancia']); 

$sqlid_tarjeta="SELECT id_tarjeta FROM periodo_lactancia INNER JOIN datos_identificacion ON periodo_lactancia.fk_id_tarjeta = datos_identificacion.id_tarjeta WHERE periodo_lactancia.id_periodo_lactancia = $id_periodo_lactancia LIMIT 1";
$id_tarjeta = $mysqli->query($sqlid_tarjeta);
$row_id_tarjeta= $id_tarjeta->fetch_assoc();
$id_tarjeta=$row_id_tarjeta['id_tarjeta'];

$sql = "DELETE FROM periodo_lactancia WHERE id_periodo_lactancia='$id_periodo_lactancia'";
if ($mysqli->query($sql)) {
    $_SESSION['color'] = "success";
    $_SESSION['msg'] = "Registro eliminado";
} 


  header("Location: periodolactancia.php?id_tarjeta=$id_tarjeta");

  