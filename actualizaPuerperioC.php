<?php



session_start();

require 'funcs/conexion.php';

$id_tarjeta = $mysqli->real_escape_string($_POST['id_tarjeta']);
$primera_consul_8 = $mysqli->real_escape_string($_POST['primera_consul_8']);


$sql = "UPDATE datos_identificacion SET 
primera_consul_8 = '$primera_consul_8' 

WHERE id_tarjeta = '$id_tarjeta'";

  if ($mysqli->query($sql)){

    $_SESSION['color'] = "success";
    $_SESSION['msg'] = "Registro actualizado";

  }

header("Location: atencionpuerperio.php?id_tarjeta=$id_tarjeta");


