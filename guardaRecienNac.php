<?php



session_start();

require 'funcs/conexion.php';
$id_tarjeta = $mysqli->real_escape_string($_POST['id_tarjeta']); 

$rn_vivo = $mysqli->real_escape_string($_POST['rn_vivo']); 
$rn_muerto = $mysqli->real_escape_string($_POST['rn_muerto']); 
$rn_sexo = $mysqli->real_escape_string($_POST['rn_sexo']);
$rn_peso = $mysqli->real_escape_string($_POST['rn_peso']); 
$rn_talla = $mysqli->real_escape_string($_POST['rn_talla']);
$rn_apgar = $mysqli->real_escape_string($_POST['rn_apgar']); 
$rn_silverman = $mysqli->real_escape_string($_POST['rn_silverman']);
$rn_tamiz_metabolico = $mysqli->real_escape_string($_POST['rn_tamiz_metabolico']);
$rn_tamiz_auditivo = $mysqli->real_escape_string($_POST['rn_tamiz_auditivo']);


$sql = "INSERT INTO recien_nacida (rn_vivo, rn_muerto, rn_sexo, rn_peso, rn_talla, rn_apgar, rn_silverman, rn_tamiz_metabolico, rn_tamiz_auditivo, fk_id_tarjeta) VALUES ('$rn_vivo', '$rn_muerto', '$rn_sexo', '$rn_peso', '$rn_talla', '$rn_apgar', '$rn_silverman', '$rn_tamiz_metabolico', '$rn_tamiz_auditivo', '$id_tarjeta')";

  if ($mysqli->query($sql)){

    $_SESSION['color'] = "success";
    $_SESSION['msg'] = "Registro guardado";

  }

  header("Location: recienNacidos.php?id_tarjeta=$id_tarjeta");

