<?php



session_start();

require 'funcs/conexion.php';

$id_tarjeta = $mysqli->real_escape_string($_POST['id_tarjeta']);
$fecha_ant_obste = $mysqli->real_escape_string($_POST['fecha_ant_obste']);
$aborto_ameu = $mysqli->real_escape_string($_POST['aborto_ameu']);
$aborto_lui = $mysqli->real_escape_string($_POST['aborto_lui']);
$aborto_medicamento = $mysqli->real_escape_string($_POST['aborto_medicamento']);
$parto_eutocico = $mysqli->real_escape_string($_POST['parto_eutocico']);
$parto_distocico = $mysqli->real_escape_string($_POST['parto_distocico']);
$parto_cesarea = $mysqli->real_escape_string($_POST['parto_cesarea']); 
$semanas_gestacion = $mysqli->real_escape_string($_POST['semanas_gestacion']); 
$metodo_anticoncep = $mysqli->real_escape_string($_POST['metodo_anticoncep']); 
$metodo_especifica = $mysqli->real_escape_string($_POST['metodo_especifica']); 
$ambulancia = $mysqli->real_escape_string($_POST['ambulancia']); 
$vehículo_particular = $mysqli->real_escape_string($_POST['vehículo_particular']); 
$transporte_ame = $mysqli->real_escape_string($_POST['transporte_ame']);
$transporte_publico = $mysqli->real_escape_string($_POST['transporte_publico']); 
$ambulancia_aerea = $mysqli->real_escape_string($_POST['ambulancia_aerea']); 
$atendido_en = $mysqli->real_escape_string($_POST['atendido_en']); 
$atendido_por = $mysqli->real_escape_string($_POST['atendido_por']); 
$complicaciones = $mysqli->real_escape_string($_POST['complicaciones']); 

$sql = "UPDATE datos_identificacion SET 

fecha_ant_obste = '$fecha_ant_obste', 
aborto_ameu = '$aborto_ameu', 
aborto_lui = '$aborto_lui', 
aborto_medicamento = '$aborto_medicamento', 
parto_eutocico = '$parto_eutocico', 
parto_distocico = '$parto_distocico', 
parto_cesarea = '$parto_cesarea', 
semanas_gestacion = '$semanas_gestacion', 
metodo_anticoncep = '$metodo_anticoncep', 
metodo_especifica = '$metodo_especifica', 
ambulancia = '$ambulancia', 
vehículo_particular = '$vehículo_particular',
transporte_ame = '$transporte_ame', 
transporte_publico = '$transporte_publico', 
ambulancia_aerea = '$ambulancia_aerea', 
atendido_en = '$atendido_en', 
atendido_por = '$atendido_por', 
complicaciones = '$complicaciones'
WHERE id_tarjeta = '$id_tarjeta'";

  if ($mysqli->query($sql)){

    $_SESSION['color'] = "success";
    $_SESSION['msg'] = "Registro actualizado";

  }

header("Location: editaTarjetaR.php?id_tarjeta=$id_tarjeta");


