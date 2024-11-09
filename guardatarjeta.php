<?php



session_start();

require 'funcs/conexion.php';

$clues_id = $mysqli->real_escape_string($_POST['clues_id']);
$tipo_personal = $mysqli->real_escape_string($_POST['tipo_personal']);//Requerido
$servicio = $mysqli->real_escape_string($_POST['servicio']);//Requerido
$folio = $mysqli->real_escape_string($_POST['folio']);//Requerido
$nombre_pre_ser = $mysqli->real_escape_string($_POST['nombre_pre_ser']);//Requerido
$curp_pre_ser = $mysqli->real_escape_string($_POST['curp_pre_ser']);//Requerido
$curp = $mysqli->real_escape_string($_POST['curp']); //Requerido
$nombre = $mysqli->real_escape_string($_POST['nombre']);//Requerido
$primer_apellido = $mysqli->real_escape_string($_POST['primer_apellido']); //Requerido
if ($_POST['segundo_apellido']==""){
  $segundo_apellido = 'XX'; //Asignado
}else{
  $segundo_apellido = $mysqli->real_escape_string($_POST['segundo_apellido']); 
}
$entidad_nacimiento = $mysqli->real_escape_string($_POST['entidad_nacimiento']); //Requerido
if ($_POST['fecha_nacimiento']==""){
  $fecha_nacimiento = '0000-00-00'; 
}else{
  $fecha_nacimiento = $mysqli->real_escape_string($_POST['fecha_nacimiento']); //Requerido
}
$edad = $mysqli->real_escape_string($_POST['edad']); //Requerido
$derechohabiencia = $mysqli->real_escape_string($_POST['derechohabiencia']);//Requerido
$estado_conyugal = $mysqli->real_escape_string($_POST['estado_conyugal']); //Requerido
$escolaridad = $mysqli->real_escape_string($_POST['escolaridad']); //Requerido
$tipo = $mysqli->real_escape_string($_POST['tipo']); //Requerido
if ($_POST['expediente']==""){
  $expediente = 'SIN EXPEDIENTE'; //Asignado
}else{
  $expediente = $mysqli->real_escape_string($_POST['expediente']); //Requerido
}
$peso_ant_emb = $mysqli->real_escape_string($_POST['peso_ant_emb']); //Requerido
$peso_emb = $mysqli->real_escape_string($_POST['peso_emb']); //Requerido
$talla = $mysqli->real_escape_string($_POST['talla']); //Requerido
$imc = $mysqli->real_escape_string($_POST['imc']);//Requerido
$afromexicana = $mysqli->real_escape_string($_POST['afromexicana']); //Requerido
$migragrante = $mysqli->real_escape_string($_POST['migragrante']); //Requerido
$nacional = $mysqli->real_escape_string($_POST['nacional']); //Requerido
$internacional = $mysqli->real_escape_string($_POST['internacional']); //Requerido
$indigena = $mysqli->real_escape_string($_POST['indigena']); //Requerido
$lengua_indigena = $mysqli->real_escape_string($_POST['lengua_indigena']); //Requerido
$lengua_habla = $mysqli->real_escape_string($_POST['lengua_habla']); //Requerido
$habla_espanol = $mysqli->real_escape_string($_POST['habla_espanol']); //Requerido
$calle = $mysqli->real_escape_string($_POST['calle']); //Requerido
if ($_POST['numero_int']==""){
  $numero_int = 'S/N'; //Asignado
}else{
  $numero_int = $mysqli->real_escape_string($_POST['numero_int']);
}
$numero_ext = $mysqli->real_escape_string($_POST['numero_ext']); //Requerido
$colonia = $mysqli->real_escape_string($_POST['colonia']); //Requerido
if ($_POST['cp']==""){
  $cp = '00000'; //Asignado
}else{
  $cp = $mysqli->real_escape_string($_POST['cp']); 
}
$localidad = $mysqli->real_escape_string($_POST['localidad']); //Requerido
if ($_POST['telefono']==""){
  $telefono = '0000000000'; //Asignado
}else{
  $telefono = $mysqli->real_escape_string($_POST['telefono']); 
}
$ap_personal_comunitario = $mysqli->real_escape_string($_POST['ap_personal_comunitario']); //Requerido
$ap_fecha = $mysqli->real_escape_string($_POST['ap_fecha']); //Requerido
$ap_trimestre = $mysqli->real_escape_string($_POST['ap_trimestre']); //Requerido
$ap_puerperio = $mysqli->real_escape_string($_POST['ap_puerperio']); //Opcional
$ap_lactancia = $mysqli->real_escape_string($_POST['ap_lactancia']); //Opcional
$ap_prueba_serologica_embarazo = $mysqli->real_escape_string($_POST['ap_prueba_serologica_embarazo']); //Requerido
$ap_se_ignora = $mysqli->real_escape_string($_POST['ap_se_ignora']); 
if ($_POST['ap_ulima_mestruacion']==""){
  $ap_ulima_mestruacion = '0000-00-00'; //Asignado
}else{
  $ap_ulima_mestruacion = $mysqli->real_escape_string($_POST['ap_ulima_mestruacion']); 
}
$ap_confiable = $mysqli->real_escape_string($_POST['ap_confiable']); //Opcional
if ($_POST['ap_fecha_ultrasonido_obstretico']==""){
  $ap_fecha_ultrasonido_obstretico = '0000-00-00'; 
}else{
  $ap_fecha_ultrasonido_obstretico = $mysqli->real_escape_string($_POST['ap_fecha_ultrasonido_obstretico']); //Requerido
}
if ($_POST['ap_semana_gestacion_usg']==""){
  $ap_semana_gestacion_usg = 0; 
}else{
  $ap_semana_gestacion_usg = $mysqli->real_escape_string($_POST['ap_semana_gestacion_usg']);  //Requerido
}
if ($_POST['ap_fecha_probable_parto']==""){
  $ap_fecha_probable_parto = '0000-00-00'; 
}else{
  $ap_fecha_probable_parto = $mysqli->real_escape_string($_POST['ap_fecha_probable_parto']); //Requerido
}
$af_ninguno = $mysqli->real_escape_string($_POST['af_ninguno']); //Requerido
if ($_POST['af_tuberculosis']==""){
  $af_tuberculosis = 'NO'; 
}else{
  $af_tuberculosis = $mysqli->real_escape_string($_POST['af_tuberculosis']); //Asignado si es NO y opcional si es SI
}
if ($_POST['af_hipertencion']==""){
  $af_hipertencion = 'NO'; 
}else{
  $af_hipertencion = $mysqli->real_escape_string($_POST['af_hipertencion']);  //Asignado si es NO y opcional si es SI
}
if ($_POST['af_diabetes']==""){
  $af_diabetes = 'NO'; 
}else{
  $af_diabetes = $mysqli->real_escape_string($_POST['af_diabetes']); //Asignado si es NO y opcional si es SI
}
if ($_POST['af_ef_hiper_embarazo']==""){
  $af_ef_hiper_embarazo = 'NO'; 
}else{
  $af_ef_hiper_embarazo = $mysqli->real_escape_string($_POST['af_ef_hiper_embarazo']); //Asignado si es NO y opcional si es SI
}
if ($_POST['af_otro']==""){
  $af_otro = 'NO'; 
}else{
  $af_otro = $mysqli->real_escape_string($_POST['af_otro']); //Asignado si es NO y opcional si es SI
}
if ($_POST['af_otro_especifique']==""){
  $af_otro_especifique = 'NO'; 
}else{
  $af_otro_especifique = $mysqli->real_escape_string($_POST['af_otro_especifique']); //Asignado si es NO y opcional si es SI
}
if ($_POST['app_ninguno']==""){
  $app_ninguno = 'NO'; 
}else{
  $app_ninguno = $mysqli->real_escape_string($_POST['app_ninguno']); //Requerido
}
if ($_POST['app_diabetes_geostacional']==""){
  $app_diabetes_geostacional = 'NO'; 
}else{
  $app_diabetes_geostacional = $mysqli->real_escape_string($_POST['app_diabetes_geostacional']); //Asignado si es NO y opcional si es SI
}
if ($_POST['app_diabetes']==""){
  $app_diabetes = 'NO'; 
}else{
  $app_diabetes = $mysqli->real_escape_string($_POST['app_diabetes']); //Asignado si es NO y opcional si es SI
}
if ($_POST['app_hipertencio_arterial']==""){
  $app_hipertencio_arterial = 'NO'; 
}else{
  $app_hipertencio_arterial = $mysqli->real_escape_string($_POST['app_hipertencio_arterial']); //Asignado si es NO y opcional si es SI
}
if ($_POST['app_preeclampsia_enclampsia']==""){
  $app_preeclampsia_enclampsia = 'NO'; 
}else{
  $app_preeclampsia_enclampsia = $mysqli->real_escape_string($_POST['app_preeclampsia_enclampsia']); //Asignado si es NO y opcional si es SI
}
if ($_POST['app_nefropatia']==""){
  $app_nefropatia = 'NO'; 
}else{
  $app_nefropatia = $mysqli->real_escape_string($_POST['app_nefropatia']); //Asignado si es NO y opcional si es SI
}
if ($_POST['app_cardiopatia']==""){
  $app_cardiopatia = 'NO'; 
}else{
  $app_cardiopatia = $mysqli->real_escape_string($_POST['app_cardiopatia']); //Asignado si es NO y opcional si es SI
}
if ($_POST['app_hemorragia_obstetrica']==""){
  $app_hemorragia_obstetrica = 'NO'; 
}else{
  $app_hemorragia_obstetrica = $mysqli->real_escape_string($_POST['app_hemorragia_obstetrica']); //Asignado si es NO y opcional si es SI
}
if ($_POST['app_padecimiento_oncologico']==""){
  $app_padecimiento_oncologico = 'NO'; 
}else{
  $app_padecimiento_oncologico = $mysqli->real_escape_string($_POST['app_padecimiento_oncologico']);//Asignado si es NO y opcional si es SI
}
if ($_POST['app_b24x']==""){
  $app_b24x = 'NO'; 
}else{
  $app_b24x = $mysqli->real_escape_string($_POST['app_b24x']); //Asignado si es NO y opcional si es SI
}
if ($_POST['app_a539']==""){
  $app_a539 = 'NO'; 
}else{
  $app_a539 = $mysqli->real_escape_string($_POST['app_a539']); //Asignado si es NO y opcional si es SI
}
if ($_POST['app_enfermedad_trnas_vector']==""){
  $app_enfermedad_trnas_vector = 0; 
}else{
  $app_enfermedad_trnas_vector = $mysqli->real_escape_string($_POST['app_enfermedad_trnas_vector']); //Asignado si es NO y opcional si es SI
}
if ($_POST['app_tuberculosis']==""){
  $app_tuberculosis = 'NO'; 
}else{
  $app_tuberculosis = $mysqli->real_escape_string($_POST['app_tuberculosis']); //Asignado si es NO y opcional si es SI
}
if ($_POST['app_sars_cov2']==""){
  $app_sars_cov2 = 'NO'; 
}else{
  $app_sars_cov2 = $mysqli->real_escape_string($_POST['app_sars_cov2']); //Asignado si es NO y opcional si es SI
}
if ($_POST['app_otro_antecedente']==""){
  $app_otro_antecedente = 0; 
}else{
  $app_otro_antecedente = $mysqli->real_escape_string($_POST['app_otro_antecedente']); //Asignado si es NO y opcional si es SI
}
$app_grupo_sanguinieo = $mysqli->real_escape_string($_POST['app_grupo_sanguinieo']); //Requerido
$app_rh = $mysqli->real_escape_string($_POST['app_rh']); //Requerido
$app_prueba_coombs = $mysqli->real_escape_string($_POST['app_prueba_coombs']); //Requerido
$app_tratamiento = $mysqli->real_escape_string($_POST['app_tratamiento']); //Requerido
$de_violencia_fam = $mysqli->real_escape_string($_POST['de_violencia_fam']); //Requerido
if ($_POST['devf_b24x_fecha_inmuno']==""){
  $devf_b24x_fecha_inmuno = '0000-00-00'; 
}else{
  $devf_b24x_fecha_inmuno = $mysqli->real_escape_string($_POST['devf_b24x_fecha_inmuno']);//Asignado si es NO y opcional si es SI
}
if ($_POST['devf_b24x_imnuno']==""){
  $devf_b24x_imnuno = ''; 
}else{
  $devf_b24x_imnuno = $mysqli->real_escape_string($_POST['devf_b24x_imnuno']);//Asignado si es NO y opcional si es SI
}
if ($_POST['devf_b24x_fecha_enzimo']==""){
  $devf_b24x_fecha_enzimo = '0000-00-00'; 
}else{
  $devf_b24x_fecha_enzimo = $mysqli->real_escape_string($_POST['devf_b24x_fecha_enzimo']);//
}
if ($_POST['devf_b24x_enzimo']==""){
  $devf_b24x_enzimo = ''; 
}else{
  $devf_b24x_enzimo = $mysqli->real_escape_string($_POST['devf_b24x_enzimo']);//Asignado si es NO y opcional si es SI
}
if ($_POST['devf_a539_fecha_inmuno']==""){
  $devf_a539_fecha_inmuno = '0000-00-00'; 
}else{
  $devf_a539_fecha_inmuno = $mysqli->real_escape_string($_POST['devf_a539_fecha_inmuno']);//
}
if ($_POST['devf_a539_imnuno']==""){
  $devf_a539_imnuno = ''; 
}else{
  $devf_a539_imnuno = $mysqli->real_escape_string($_POST['devf_a539_imnuno']); //Asignado si es NO y opcional si es SI
}
if ($_POST['devf_a539_fecha_enzimo']==""){
  $devf_a539_fecha_enzimo = '0000-00-00'; 
}else{
  $devf_a539_fecha_enzimo = $mysqli->real_escape_string($_POST['devf_a539_fecha_enzimo']); //
}
if ($_POST['devf_a539_enzimo']==""){
  $devf_a539_enzimo = ''; 
}else{
  $devf_a539_enzimo = $mysqli->real_escape_string($_POST['devf_a539_enzimo']); //Asignado si es NO y opcional si es SI
}

$de_depresion_prenatal = $mysqli->real_escape_string($_POST['de_depresion_prenatal']);//Requerido
if ($_POST['dedp_b24x_fecha_rapida']==""){
  $dedp_b24x_fecha_rapida = '0000-00-00'; 
}else{
  $dedp_b24x_fecha_rapida = $mysqli->real_escape_string($_POST['dedp_b24x_fecha_rapida']);//Asignado si es NO y opcional si es SI
  }
if ($_POST['dedp_b24x_rapida']==""){
  $dedp_b24x_rapida = ''; 
}else{
  $dedp_b24x_rapida = $mysqli->real_escape_string($_POST['dedp_b24x_rapida']); //Asignado si es NO y opcional si es SI
}
if ($_POST['dedp_b24x_fecha_lab']==""){
  $dedp_b24x_fecha_lab = '0000-00-00'; 
}else{
  $dedp_b24x_fecha_lab = $mysqli->real_escape_string($_POST['dedp_b24x_fecha_lab']);//Asignado si es NO y opcional si es SI
} 
if ($_POST['dedp_b24x_lab']==""){
  $dedp_b24x_lab = ''; 
}else{
  $dedp_b24x_lab = $mysqli->real_escape_string($_POST['dedp_b24x_lab']); //Asignado si es NO y opcional si es SI
}
if ($_POST['dedp_a539_fecha_rapidaa']==""){
  $dedp_a539_fecha_rapidaa = '0000-00-00'; 
}else{
  $dedp_a539_fecha_rapidaa = $mysqli->real_escape_string($_POST['dedp_a539_fecha_rapidaa']);//Asignado si es NO y opcional si es SI
} 
if ($_POST['dedp_a539_rapida']==""){
  $dedp_a539_rapida = ''; 
}else{
  $dedp_a539_rapida = $mysqli->real_escape_string($_POST['dedp_a539_rapida']);  //Asignado si es NO y opcional si es SI
}
if ($_POST['dedp_a539_fecha_lab']==""){
  $dedp_a539_fecha_lab = '0000-00-00'; 
}else{
  $dedp_a539_fecha_lab = $mysqli->real_escape_string($_POST['dedp_a539_fecha_lab']); //Asignado si es NO y opcional si es SI
} 
if ($_POST['dedp_a539_lab']==""){
  $dedp_a539_lab = ''; 
}else{
  $dedp_a539_lab = $mysqli->real_escape_string($_POST['dedp_a539_lab']);  //Asignado si es NO y opcional si es SI
}

$ag_atencio_preges = $mysqli->real_escape_string($_POST['ag_atencio_preges']);//Requerido
$ag_riesgos = $mysqli->real_escape_string($_POST['ag_riesgos']);//Requerido
$ag_gestas = $mysqli->real_escape_string($_POST['ag_gestas']); //Requerido
$ag_partos = $mysqli->real_escape_string($_POST['ag_partos']); //Asignado 0 y opcional 
$ag_cesarias = $mysqli->real_escape_string($_POST['ag_cesarias']); //Asignado 0 y opcional 
$ag_abortos = $mysqli->real_escape_string($_POST['ag_abortos']); //Asignado 0 y opcional 
$ag_ectopico = $mysqli->real_escape_string($_POST['ag_ectopico']);//Asignado 0 y opcional 
$ag_mola = $mysqli->real_escape_string($_POST['ag_mola']); //Asignado 0 y opcional 
$ag_emb_mul = $mysqli->real_escape_string($_POST['ag_emb_mul']); //Asignado 0 y opcional 
$ag_hijos_nac_viv = $mysqli->real_escape_string($_POST['ag_hijos_nac_viv']); //Asignado 0 y opcional 
$ag_ag_hijos_nac_mue = $mysqli->real_escape_string($_POST['ag_ag_hijos_nac_mue']); //Asignado 0 y opcional 
$ag_resol_ult_emba = $mysqli->real_escape_string($_POST['ag_resol_ult_emba']); 
if ($_POST['ag_resol_fecha_ul_emb']==""){
  $ag_resol_fecha_ul_emb = '0000-00-00'; 
}else{
  $ag_resol_fecha_ul_emb = $mysqli->real_escape_string($_POST['ag_resol_fecha_ul_emb']); //
} 
$ag_uso_prev_antico_tipo = $mysqli->real_escape_string($_POST['ag_uso_prev_antico_tipo']); 
$ag_uso_prev_antico_tiempo_uso = $mysqli->real_escape_string($_POST['ag_uso_prev_antico_tiempo_uso']); 
if ($_POST['ag_uso_prev_fecha_suspe']==""){
  $ag_uso_prev_fecha_suspe = '0000-00-00'; 
}else{
  $ag_uso_prev_fecha_suspe = $mysqli->real_escape_string($_POST['ag_uso_prev_fecha_suspe']);//
} 
$ag_uso_prev_falla = $mysqli->real_escape_string($_POST['ag_uso_prev_falla']);
$ag_otro_ante_ginecoobs = $mysqli->real_escape_string($_POST['ag_otro_ante_ginecoobs']);
$ag_otro_ante_ginecoobs_esp = $mysqli->real_escape_string($_POST['ag_otro_ante_ginecoobs_esp']);
if ($_POST['bio_fecha_td_primera']==""){
  $bio_fecha_td_primera = '0000-00-00'; 
}else{
  $bio_fecha_td_primera = $mysqli->real_escape_string($_POST['bio_fecha_td_primera']); //
} 
if ($_POST['bio_fecha_td_segunda']==""){
  $bio_fecha_td_segunda = '0000-00-00'; 
}else{
  $bio_fecha_td_segunda = $mysqli->real_escape_string($_POST['bio_fecha_td_segunda']);//
} 
if ($_POST['bio_fecha_td_tercera']==""){
  $bio_fecha_td_tercera = '0000-00-00'; 
}else{
  $bio_fecha_td_tercera = $mysqli->real_escape_string($_POST['bio_fecha_td_tercera']);//
} 
if ($_POST['bio_fecha_tdpa']==""){
  $bio_fecha_tdpa = '0000-00-00'; 
}else{
  $bio_fecha_tdpa = $mysqli->real_escape_string($_POST['bio_fecha_tdpa']);//
} 
if ($_POST['bio_fecha_influenza']==""){
  $bio_fecha_influenza = '0000-00-00'; 
}else{
  $bio_fecha_influenza = $mysqli->real_escape_string($_POST['bio_fecha_influenza']);//
} 
if ($_POST['bio_fecha_covid19']==""){
  $bio_fecha_covid19 = '0000-00-00'; 
}else{
  $bio_fecha_covid19 = $mysqli->real_escape_string($_POST['bio_fecha_covid19']);//
} 

if ($_POST['adicciones']==""){
  $adicciones = 'NO'; 
}else{
  $adicciones = $mysqli->real_escape_string($_POST['adicciones']); //Requerido
}
if ($_POST['adic_tabaco']==""){
  $adic_tabaco = 'NO'; 
}else{
  $adic_tabaco = $mysqli->real_escape_string($_POST['adic_tabaco']); //Asignado si es NO y opcional si es SI
}
if ($_POST['adic_antidepre']==""){
  $adic_antidepre = 'NO'; 
}else{
  $adic_antidepre = $mysqli->real_escape_string($_POST['adic_antidepre']); //Asignado si es NO y opcional si es SI
}
if ($_POST['adic_alcohol']==""){
  $adic_alcohol = 'NO'; 
}else{
  $adic_alcohol = $mysqli->real_escape_string($_POST['adic_alcohol']); //Asignado si es NO y opcional si es SI
}
if ($_POST['adic_ansioliticos']==""){
  $adic_ansioliticos = 'NO'; 
}else{
  $adic_ansioliticos = $mysqli->real_escape_string($_POST['adic_ansioliticos']);//Asignado si es NO y opcional si es SI
}
if ($_POST['adic_otros']==""){
  $adic_otros = 'NO'; 
}else{
  $adic_otros = $mysqli->real_escape_string($_POST['adic_otros']); //Asignado si es NO y opcional si es SI
}
if ($_POST['adic_consumo_act']==""){
  $adic_consumo_act = 'NO'; 
}else{
  $adic_consumo_act = $mysqli->real_escape_string($_POST['adic_consumo_act']); //Asignado si es NO y opcional si es SI
}
$adic_años = $mysqli->real_escape_string($_POST['adic_años']); //Asignado si es NO y opcional si es SI
$adic_meses = $mysqli->real_escape_string($_POST['adic_meses']); //Asignado si es NO y opcional si es SI
$sbde_rev_odont = $mysqli->real_escape_string($_POST['sbde_rev_odont']);//Requerido
$sbde_atn_caries = $mysqli->real_escape_string($_POST['sbde_atn_caries']);//Requerido
$sbde_atn_periodonitis = $mysqli->real_escape_string($_POST['sbde_atn_periodonitis']); //Requerido
$sbde_atn_otro = $mysqli->real_escape_string($_POST['sbde_atn_otro']); //Requerido
$v_nuticional = $mysqli->real_escape_string($_POST['v_nuticional']); //Requerido
if ($_POST['pc_lab_otr_b24x_fecha']==""){
  $pc_lab_otr_b24x_fecha = '0000-00-00'; 
}else{
  $pc_lab_otr_b24x_fecha = $mysqli->real_escape_string($_POST['pc_lab_otr_b24x_fecha']);//
} 
$pc_lab_otr_b24x_detectable = $mysqli->real_escape_string($_POST['pc_lab_otr_b24x_detectable']);
if ($_POST['pc_lab_otr_a539_fecha']==""){
  $pc_lab_otr_a539_fecha = '0000-00-00'; 
}else{
  $pc_lab_otr_a539_fecha = $mysqli->real_escape_string($_POST['pc_lab_otr_a539_fecha']);//
} 
$pc_lab_otr_a539_posneg = $mysqli->real_escape_string($_POST['pc_lab_otr_a539_posneg']); 
if ($_POST['cdg_fecha']==""){
  $cdg_fecha = '0000-00-00'; 
}else{
  $cdg_fecha = $mysqli->real_escape_string($_POST['cdg_fecha']); //Asignado si es NO y opcional si es SI
} 
if ($_POST['cdg_ayuno']==""){
  $cdg_ayuno = 0; 
}else{
  $cdg_ayuno = $mysqli->real_escape_string($_POST['cdg_ayuno']); //Asignado si es NO y opcional si es SI
} 
if ($_POST['cdg_60']==""){
  $cdg_60 = 0; 
}else{
  $cdg_60 = $mysqli->real_escape_string($_POST['cdg_60']); //Asignado si es NO y opcional si es SI
} 
if ($_POST['cdg_120']==""){
  $cdg_120= 0; 
}else{
  $cdg_120 = $mysqli->real_escape_string($_POST['cdg_120']); //Asignado si es NO y opcional si es SI
}



$sqlbusca = "SELECT curp FROM datos_identificacion WHERE curp = '$curp' LIMIT 1";
$resultado = $mysqli->query($sqlbusca);
$rows = $resultado->num_rows;

if ($rows > 0) {
    
  $_SESSION['color'] = "danger";
  $_SESSION['msg'] = "CURP $curp ya Existe";
}else{
  $sql = "INSERT INTO datos_identificacion (clues_id, tipo_personal, servicio, folio,nombre_pre_ser, curp_pre_ser, curp, nombre, primer_apellido, segundo_apellido, entidad_nacimiento, fecha_nacimiento, edad, derechohabiencia, estado_conyugal, escolaridad, tipo, expediente, peso_ant_emb, peso_emb, talla, imc, afromexicana, migragrante, nacional, internacional, indigena, lengua_indigena, lengua_habla, habla_espanol, calle, numero_int, numero_ext, colonia, cp, localidad, telefono, ap_personal_comunitario, ap_fecha, ap_trimestre, ap_puerperio, ap_lactancia, ap_prueba_serologica_embarazo, ap_se_ignora, ap_ulima_mestruacion, ap_confiable, ap_fecha_ultrasonido_obstretico, ap_semana_gestacion_usg, ap_fecha_probable_parto, af_ninguno, af_tuberculosis,af_hipertencion, af_diabetes, af_ef_hiper_embarazo, af_otro, af_otro_especifique, app_ninguno, app_diabetes_geostacional, app_diabetes, app_hipertencio_arterial, app_preeclampsia_enclampsia, app_nefropatia, app_cardiopatia, app_hemorragia_obstetrica, app_padecimiento_oncologico, app_b24x, app_a539, app_enfermedad_trnas_vector, app_tuberculosis, app_sars_cov2, app_otro_antecedente, app_grupo_sanguinieo, app_rh, app_prueba_coombs, app_tratamiento, de_violencia_fam, devf_b24x_fecha_inmuno, devf_b24x_imnuno, devf_b24x_fecha_enzimo, devf_b24x_enzimo, devf_a539_fecha_inmuno, devf_a539_imnuno, devf_a539_fecha_enzimo, devf_a539_enzimo, de_depresion_prenatal, dedp_b24x_fecha_rapida, dedp_b24x_rapida, dedp_b24x_fecha_lab, dedp_b24x_lab, dedp_a539_fecha_rapidaa, dedp_a539_rapida, dedp_a539_fecha_lab, dedp_a539_lab, ag_atencio_preges, ag_riesgos, ag_gestas, ag_partos, ag_cesarias, ag_abortos, ag_ectopico, ag_mola, ag_emb_mul, ag_hijos_nac_viv, ag_ag_hijos_nac_mue, ag_resol_ult_emba, ag_resol_fecha_ul_emb, ag_uso_prev_antico_tipo, ag_uso_prev_antico_tiempo_uso, ag_uso_prev_fecha_suspe, ag_uso_prev_falla, ag_otro_ante_ginecoobs, ag_otro_ante_ginecoobs_esp, bio_fecha_td_primera, bio_fecha_td_segunda, bio_fecha_td_tercera, bio_fecha_tdpa, bio_fecha_influenza, bio_fecha_covid19, adicciones, adic_tabaco, adic_antidepre, adic_alcohol, adic_ansioliticos, adic_otros, adic_consumo_act, adic_años, adic_meses, sbde_rev_odont, sbde_atn_caries, sbde_atn_periodonitis, sbde_atn_otro, v_nuticional, pc_lab_otr_b24x_fecha, pc_lab_otr_b24x_detectable, pc_lab_otr_a539_fecha,pc_lab_otr_a539_posneg, cdg_fecha, cdg_ayuno, cdg_60, cdg_120) VALUES ('$clues_id', '$tipo_personal','$servicio','$folio','$nombre_pre_ser','$curp_pre_ser','$curp', '$nombre', '$primer_apellido', '$segundo_apellido', '$entidad_nacimiento', '$fecha_nacimiento', '$edad', '$derechohabiencia','$estado_conyugal', '$escolaridad', '$tipo', '$expediente', '$peso_ant_emb', '$peso_emb', '$talla', '$imc', '$afromexicana', '$migragrante', '$nacional','$internacional','$indigena','$lengua_indigena','$lengua_habla','$habla_espanol', '$calle','$numero_int','$numero_ext','$colonia','$cp','$localidad', '$telefono', '$ap_personal_comunitario', '$ap_fecha', '$ap_trimestre', '$ap_puerperio', '$ap_lactancia','$ap_prueba_serologica_embarazo', '$ap_se_ignora', '$ap_ulima_mestruacion', '$ap_confiable', '$ap_fecha_ultrasonido_obstretico','$ap_semana_gestacion_usg', '$ap_fecha_probable_parto', '$af_ninguno', '$af_tuberculosis', '$af_hipertencion','$af_diabetes','$af_ef_hiper_embarazo', '$af_otro', '$af_otro_especifique', '$app_ninguno', '$app_diabetes_geostacional', '$app_diabetes','$app_hipertencio_arterial', '$app_preeclampsia_enclampsia', '$app_nefropatia', '$app_cardiopatia', '$app_hemorragia_obstetrica','$app_padecimiento_oncologico', '$app_b24x','$app_a539', '$app_enfermedad_trnas_vector', '$app_tuberculosis', '$app_sars_cov2','$app_otro_antecedente', '$app_grupo_sanguinieo', '$app_rh', '$app_prueba_coombs', '$app_tratamiento', '$de_violencia_fam','$devf_b24x_fecha_inmuno', '$devf_b24x_imnuno', '$devf_b24x_fecha_enzimo', '$devf_b24x_enzimo', '$devf_a539_fecha_inmuno', '$devf_a539_imnuno', '$devf_a539_fecha_enzimo', '$devf_a539_enzimo', '$de_depresion_prenatal', '$dedp_b24x_fecha_rapida', '$dedp_b24x_rapida', '$dedp_b24x_fecha_lab', '$dedp_b24x_lab', '$dedp_a539_fecha_rapidaa', '$dedp_a539_rapida', '$dedp_a539_fecha_lab','$dedp_a539_lab','$ag_atencio_preges', '$ag_riesgos','$ag_gestas', '$ag_partos', '$ag_cesarias', '$ag_abortos','$ag_ectopico', '$ag_mola','$ag_emb_mul', '$ag_hijos_nac_viv', '$ag_ag_hijos_nac_mue', '$ag_resol_ult_emba', '$ag_resol_fecha_ul_emb', '$ag_uso_prev_antico_tipo', '$ag_uso_prev_antico_tiempo_uso', '$ag_uso_prev_fecha_suspe', '$ag_uso_prev_falla', '$ag_otro_ante_ginecoobs', '$ag_otro_ante_ginecoobs_esp', '$bio_fecha_td_primera', '$bio_fecha_td_segunda', '$bio_fecha_td_tercera', '$bio_fecha_tdpa', '$bio_fecha_influenza', '$bio_fecha_covid19', '$adicciones', '$adic_tabaco', '$adic_antidepre', '$adic_alcohol', '$adic_ansioliticos', '$adic_otros','$adic_consumo_act', '$adic_años', '$adic_meses', '$sbde_rev_odont', '$sbde_atn_caries', '$sbde_atn_periodonitis', '$sbde_atn_otro', '$v_nuticional', '$pc_lab_otr_b24x_fecha','$pc_lab_otr_b24x_detectable', '$pc_lab_otr_a539_fecha','$pc_lab_otr_a539_posneg', '$cdg_fecha', '$cdg_ayuno','$cdg_60', '$cdg_120')";

    if ($mysqli->query($sql)){

      $_SESSION['color'] = "success";
      $_SESSION['msg'] = "Registro guardado";

    }  
}

header('Location: registrostarjeta.php');
