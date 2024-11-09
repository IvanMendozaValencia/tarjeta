<?php



session_start();

require 'funcs/conexion.php';
$id_recien_nacida = $mysqli->real_escape_string($_POST['id_recien_nacida']); 
$id_tarjeta = $mysqli->real_escape_string($_POST['fk_id_tarjeta']); 
$rn_vivo = $mysqli->real_escape_string($_POST['rn_vivo1']); 
$rn_muerto = $mysqli->real_escape_string($_POST['rn_muerto1']); 
$rn_sexo = $mysqli->real_escape_string($_POST['rn_sexo']);
$rn_peso = $mysqli->real_escape_string($_POST['rn_peso']); 
$rn_talla = $mysqli->real_escape_string($_POST['rn_talla']);
$rn_apgar = $mysqli->real_escape_string($_POST['rn_apgar']); 
$rn_silverman = $mysqli->real_escape_string($_POST['rn_silverman']);
$rn_tamiz_metabolico = $mysqli->real_escape_string($_POST['rn_tamiz_metabolico']);
$rn_tamiz_auditivo = $mysqli->real_escape_string($_POST['rn_tamiz_auditivo']);

$sql = "UPDATE recien_nacida SET 
rn_vivo = '$rn_vivo', 
rn_muerto = '$rn_muerto', 
rn_sexo = '$rn_sexo', 
rn_peso = '$rn_peso', 
rn_talla = '$rn_talla', 
rn_apgar = '$rn_apgar', 
rn_silverman = '$rn_silverman', 
rn_tamiz_metabolico = '$rn_tamiz_metabolico',
rn_tamiz_auditivo ='$rn_tamiz_auditivo'
WHERE id_recien_nacida = '$id_recien_nacida'";


  if ($mysqli->query($sql)){

    $_SESSION['color'] = "success";
    $_SESSION['msg'] = "Registro actualizado";

  }

  header("Location: recienNacidos.php?id_tarjeta=$id_tarjeta");


