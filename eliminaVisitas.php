<?php



session_start();

require 'funcs/conexion.php';


$id_visita = $mysqli->real_escape_string($_POST['id_visita']); 

$sqlid_tarjeta="SELECT id_tarjeta FROM datos_identificacion INNER JOIN visitas ON visitas.fk_visita_tarjeta = datos_identificacion.id_tarjeta WHERE visitas.id_visita = $id_visita LIMIT 1";
$id_tarjeta = $mysqli->query($sqlid_tarjeta);
$row_id_tarjeta= $id_tarjeta->fetch_assoc();
$id_tarjeta=$row_id_tarjeta['id_tarjeta'];

$sql = "DELETE FROM visitas WHERE id_visita='$id_visita'";
if ($mysqli->query($sql)) {
    $_SESSION['color'] = "success";
    $_SESSION['msg'] = "Registro eliminado";
} 


  header("Location: visitas.php?id_tarjeta=$id_tarjeta");

