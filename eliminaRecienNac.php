<?php



session_start();

require 'funcs/conexion.php';


$id_cita_prenatal = $mysqli->real_escape_string($_POST['id_cita_prenatal']); 

$sqlid_tarjeta="SELECT id_tarjeta FROM recien_nacida INNER JOIN datos_identificacion ON recien_nacida.fk_id_tarjeta = datos_identificacion.id_tarjeta WHERE recien_nacida.id_recien_nacida = $id_recien_nacida LIMIT 1";
$id_tarjeta = $mysqli->query($sqlid_tarjeta);
$row_id_tarjeta= $id_tarjeta->fetch_assoc();
$id_tarjeta=$row_id_tarjeta['id_tarjeta'];

$sql = "DELETE FROM citas_prenatales WHERE id_cita_prenatal='$id_cita_prenatal'";
if ($mysqli->query($sql)) {
    $_SESSION['color'] = "success";
    $_SESSION['msg'] = "Registro eliminado";
} 


  header("Location: recienNacidos.php?id_tarjeta=$id_tarjeta");

  