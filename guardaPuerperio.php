<?php



session_start();

require 'funcs/conexion.php';
$id_tarjeta = $mysqli->real_escape_string($_POST['id_tarjeta']); 

$p_fecha = $mysqli->real_escape_string($_POST['p_fecha']); 
$p_peso = $mysqli->real_escape_string($_POST['p_peso']); 
$p_sig_sin = $mysqli->real_escape_string($_POST['p_sig_sin']);
$p_medicamentos = $mysqli->real_escape_string($_POST['p_medicamentos']); 
$p_enfermedades_pre = $mysqli->real_escape_string($_POST['p_enfermedades_pre']);
$p_plan_seguridad = $mysqli->real_escape_string($_POST['p_plan_seguridad']); 

$sql = "INSERT INTO puerperio (p_fecha, p_peso, p_sig_sin, p_medicamentos, p_enfermedades_pre, p_plan_seguridad, fk_id_tarjeta) VALUES ('$p_fecha', '$p_peso', '$p_sig_sin', '$p_medicamentos', '$p_enfermedades_pre', '$p_plan_seguridad', '$id_tarjeta')";

  if ($mysqli->query($sql)){

    $_SESSION['color'] = "success";
    $_SESSION['msg'] = "Registro guardado";

  }

  header("Location: atencionpuerperio.php?id_tarjeta=$id_tarjeta");

