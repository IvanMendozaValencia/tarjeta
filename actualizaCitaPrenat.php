<?php



session_start();

require 'funcs/conexion.php';


$id_cita_prenatal = $mysqli->real_escape_string($_POST['id_cita_prenatal']);
//$cp_fecha_ingreso_control = $mysqli->real_escape_string($_POST['cp_fecha_ingreso_control']);
$cp_fecha_consulta = $mysqli->real_escape_string($_POST['cp_fecha_consulta']);
$cp_acompanante = $mysqli->real_escape_string($_POST['cp_acompanante']);
$cp_semana_gestacion = $mysqli->real_escape_string($_POST['cp_semana_gestacion']);
$cp_peso = $mysqli->real_escape_string($_POST['cp_peso']);
$cp_talla = $mysqli->real_escape_string($_POST['cp_talla']);
$cp_imc = $mysqli->real_escape_string($_POST['cp_imc']);
$cp_pres_art = $mysqli->real_escape_string($_POST['cp_pres_art']);
$cp_fondo_uterino = $mysqli->real_escape_string($_POST['cp_fondo_uterino']); 
$cp_frec_cardiaca = $mysqli->real_escape_string($_POST['cp_frec_cardiaca']); 
$cp_sig_sin_alarma = $mysqli->real_escape_string($_POST['cp_sig_sin_alarma']); 
if ($_POST['cp_medicamentos1']==""){
  $cp_medicamentos1 = '0'; 
}else{
  $cp_medicamentos1 = $mysqli->real_escape_string($_POST['cp_medicamentos1']); 
}
if ($_POST['cp_medicamentos2']==""){
  $cp_medicamentos2 = '0'; 
}else{
  $cp_medicamentos2 = $mysqli->real_escape_string($_POST['cp_medicamentos2']); 
}
if ($_POST['cp_medicamentos3']==""){
  $cp_medicamentos3 = '0'; 
}else{
  $cp_medicamentos3 = $mysqli->real_escape_string($_POST['cp_medicamentos3']); 
} 
$cp_qs_glucemia = $mysqli->real_escape_string($_POST['cp_qs_glucemia']); 
$cp_qs_emc_alter = $mysqli->real_escape_string($_POST['cp_qs_emc_alter']);
$cp_bh_plaquetas = $mysqli->real_escape_string($_POST['cp_bh_plaquetas']); 
$cp_bh_leucocitos = $mysqli->real_escape_string($_POST['cp_bh_leucocitos']); 
$cp_bh_hemoglobina = $mysqli->real_escape_string($_POST['cp_bh_hemoglobina']); 
$cp_bh_hematocrito = $mysqli->real_escape_string($_POST['cp_bh_hematocrito']); 
$cp_egh_proteinuaria = $mysqli->real_escape_string($_POST['cp_egh_proteinuaria']); 
$cp_egh_hematuria = $mysqli->real_escape_string($_POST['cp_egh_hematuria']); 
$cp_egh_glucosuria = $mysqli->real_escape_string($_POST['cp_egh_glucosuria']);
$cp_egh_bacteriuria = $mysqli->real_escape_string($_POST['cp_egh_bacteriuria']); 
$cp_uo_sem_gesta = $mysqli->real_escape_string($_POST['cp_uo_sem_gesta']); 
$cp_uo_resultado = $mysqli->real_escape_string($_POST['cp_uo_resultado']); 
if ($_POST['cp_ori_edu']==""){
  $cp_ori_edu = '0'; 
}else{
  $cp_ori_edu = $mysqli->real_escape_string($_POST['cp_ori_edu']); 
}
if ($_POST['cp_refrencia_a']==""){
  $cp_refrencia_a = '0'; 
}else{
  $cp_refrencia_a = $mysqli->real_escape_string($_POST['cp_refrencia_a']);
}
if ($_POST['cp_motiv_referencia']==""){
  $cp_motiv_referencia = '0'; 
}else{
  $cp_motiv_referencia = $mysqli->real_escape_string($_POST['cp_motiv_referencia']);
}
$cp_eferemeda_presente = $mysqli->real_escape_string($_POST['cp_eferemeda_presente']); 
$cp_plan_seguridad = $mysqli->real_escape_string($_POST['cp_plan_seguridad']); 
$fk_id_tarjeta = $mysqli->real_escape_string($_POST['fk_id_tarjeta']); 

//cp_fecha_ingreso_control = '$cp_fecha_ingreso_control', 
$sql = "UPDATE citas_prenatales SET 

cp_fecha_consulta = '$cp_fecha_consulta', 
cp_acompanante = '$cp_acompanante', 
cp_semana_gestacion = '$cp_semana_gestacion', 
cp_peso = '$cp_peso', 
cp_talla = $cp_talla,
cp_imc = $cp_imc,
cp_pres_art = '$cp_pres_art', 
cp_fondo_uterino = '$cp_fondo_uterino', 
cp_frec_cardiaca = '$cp_frec_cardiaca', 
cp_sig_sin_alarma = '$cp_sig_sin_alarma', 
cp_medicamentos1 = '$cp_medicamentos1', 
cp_medicamentos2 = '$cp_medicamentos2', 
cp_medicamentos3 = '$cp_medicamentos3', 
cp_qs_glucemia = '$cp_qs_glucemia',
cp_qs_emc_alter = '$cp_qs_emc_alter', 
cp_bh_plaquetas = '$cp_bh_plaquetas', 
cp_bh_leucocitos = '$cp_bh_leucocitos', 
cp_bh_hemoglobina = '$cp_bh_hemoglobina', 
cp_bh_hematocrito = '$cp_bh_hematocrito', 
cp_egh_proteinuaria = '$cp_egh_proteinuaria', 
cp_egh_hematuria = '$cp_egh_hematuria',
cp_egh_glucosuria = '$cp_egh_glucosuria', 
cp_egh_bacteriuria = '$cp_egh_bacteriuria', 
cp_uo_sem_gesta = '$cp_uo_sem_gesta', 
cp_uo_resultado = '$cp_uo_resultado', 
cp_ori_edu = '$cp_ori_edu', 
cp_refrencia_a = '$cp_refrencia_a', 
cp_motiv_referencia = '$cp_motiv_referencia', 
cp_eferemeda_presente = '$cp_eferemeda_presente', 
cp_plan_seguridad = '$cp_plan_seguridad', 
fk_id_tarjeta = '$fk_id_tarjeta'

WHERE id_cita_prenatal = '$id_cita_prenatal'";

  if ($mysqli->query($sql)){

    $_SESSION['color'] = "success";
    $_SESSION['msg'] = "Registro actualizado";

  }
header("Location: citaprenatal.php?id_tarjeta=$fk_id_tarjeta");

