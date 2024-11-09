<?php



session_start();

require 'funcs/conexion.php';
$id_tarjeta = $mysqli->real_escape_string($_POST['fk_id_tarjeta']); 
$id_puerperio = $mysqli->real_escape_string($_POST['id_puerperio']); 
$p_fecha = $mysqli->real_escape_string($_POST['p_fecha']); 
$p_peso = $mysqli->real_escape_string($_POST['p_peso']); 
$p_sig_sin = $mysqli->real_escape_string($_POST['p_sig_sin']);
$p_medicamentos = $mysqli->real_escape_string($_POST['p_medicamentos']); 
$p_enfermedades_pre = $mysqli->real_escape_string($_POST['p_enfermedades_pre']);
$p_plan_seguridad = $mysqli->real_escape_string($_POST['p_plan_seguridad']); 

$sql = "UPDATE puerperio SET p_fecha='$p_fecha', p_peso='$p_peso', p_sig_sin='$p_sig_sin', p_medicamentos='$p_medicamentos', p_enfermedades_pre='$p_enfermedades_pre', p_plan_seguridad='$p_plan_seguridad' WHERE id_puerperio = '$id_puerperio'";

  if ($mysqli->query($sql)){

    $_SESSION['color'] = "success";
    $_SESSION['msg'] = "Registro actualizado";

  }

  header("Location: atencionpuerperio.php?id_tarjeta=$id_tarjeta");


