<?php



session_start();

require 'funcs/conexion.php';


$id_puerperio = $mysqli->real_escape_string($_POST['id_puerperio']); 

$sqlid_tarjeta="SELECT id_tarjeta FROM puerperio INNER JOIN datos_identificacion ON puerperio.fk_id_tarjeta = datos_identificacion.id_tarjeta WHERE puerperio.id_puerperio = $id_puerperio LIMIT 1";
$id_tarjeta = $mysqli->query($sqlid_tarjeta);
$row_id_tarjeta= $id_tarjeta->fetch_assoc();
$id_tarjeta=$row_id_tarjeta['id_tarjeta'];

$sql = "DELETE FROM puerperio WHERE id_puerperio='$id_puerperio'";
if ($mysqli->query($sql)) {
    $_SESSION['color'] = "success";
    $_SESSION['msg'] = "Registro eliminado";
} 


  header("Location: atencionpuerperio.php?id_tarjeta=$id_tarjeta");

  