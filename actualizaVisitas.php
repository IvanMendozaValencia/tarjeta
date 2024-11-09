<?php



session_start();

require 'funcs/conexion.php';

$id_visita = $mysqli->real_escape_string($_POST['id_visita']); 
$fecha_visita = $mysqli->real_escape_string($_POST['fecha_visita']); 
$result_visita = $mysqli->real_escape_string($_POST['result_visita']); 
$personal_visita = $mysqli->real_escape_string($_POST['personal_visita']);
$fk_visita_tarjeta = $mysqli->real_escape_string($_POST['fk_visita_tarjeta']); 



$sql = "UPDATE visitas SET fecha_visita='$fecha_visita', result_visita='$result_visita', personal_visita='$personal_visita' WHERE id_visita='$id_visita'";
  if ($mysqli->query($sql)){

    $_SESSION['color'] = "success";
    $_SESSION['msg'] = "Registro actualizado";

  }

  header("Location: visitas.php?id_tarjeta=$fk_visita_tarjeta");

