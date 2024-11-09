<?php



session_start();

require 'funcs/conexion.php';


$id_cita_prenatal = $mysqli->real_escape_string($_POST['id_cita_prenatal']); 

$sqlid_tarjeta="SELECT id_tarjeta FROM datos_identificacion INNER JOIN citas_prenatales ON citas_prenatales.fk_id_tarjeta = datos_identificacion.id_tarjeta WHERE citas_prenatales.id_cita_prenatal = $id_cita_prenatal LIMIT 1";
$id_tarjeta = $mysqli->query($sqlid_tarjeta);
$row_id_tarjeta= $id_tarjeta->fetch_assoc();
$id_tarjeta=$row_id_tarjeta['id_tarjeta'];

$sql = "DELETE FROM citas_prenatales WHERE id_cita_prenatal='$id_cita_prenatal'";
if ($mysqli->query($sql)) {
    $_SESSION['color'] = "success";
    $_SESSION['msg'] = "Registro eliminado";
} 


  header("Location: citaprenatal.php?id_tarjeta=$id_tarjeta");

