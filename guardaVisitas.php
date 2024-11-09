<?php



session_start();

require 'funcs/conexion.php';

$fecha_visita = $mysqli->real_escape_string($_POST['fecha_visita']); 
$result_visita = $mysqli->real_escape_string($_POST['result_visita']); 
$personal_visita = $mysqli->real_escape_string($_POST['personal_visita']);
$id_tarjeta = $mysqli->real_escape_string($_POST['id_tarjeta']); 

$sql = "INSERT INTO visitas (fecha_visita,result_visita,personal_visita,fk_visita_tarjeta) VALUES ('$fecha_visita','$result_visita','$personal_visita','$id_tarjeta')";

  if ($mysqli->query($sql)){

    $_SESSION['color'] = "success";
    $_SESSION['msg'] = "Registro guardado";

  }

  header("Location: visitas.php?id_tarjeta=$id_tarjeta");

