<?php


session_start();


require 'funcs/conexion.php';
require 'funcs/funcs.php';



if(!isset($_SESSION["id_usuario"]))
{
    header("Location: index.php");
}

$idUsusario = $_SESSION['id_usuario'];
$sql = "SELECT id, usuario, nombre, correo, last_session, id_tipo, a_usuarios, a_consultas, a_estadisticas, a_agregar, a_modificar, a_eliminar FROM usuarios WHERE id ='$idUsusario'";
$result = $mysqli->query($sql);
$row_usuario  = $result->fetch_assoc();

$id = $mysqli->real_escape_string($_GET['id']);

$sqltarjeta = "SELECT id_tarjeta, curp, nombre, primer_apellido, segundo_apellido, entidad_nacimiento, fecha_nacimiento, edad, derechohabiencia, estado_conyugal, escolaridad, tipo, expediente, peso_ant_emb, talla, imc, afromexicana, migragrante, nacional, internacional, indigena, lengua_indigena, lengua_habla, habla_espanol, calle, numero_int, numero_ext, colonia, cp, localidad, telefono, ap_personal_comunitario, ap_fecha, ap_trimestre, ap_puerperio, ap_lactancia, ap_prueba_serologica_embarazo, ap_se_ignora, ap_ulima_mestruacion, ap_confiable, ap_fecha_ultrasonido_obstretico, ap_semana_gestacion_usg, ap_fecha_probable_parto, af_ninguno, af_tuberculosis, af_hipertencion, af_diabetes, af_ef_hiper_embarazo, af_otro, af_otro_especifique, app_ninguno, app_diabetes_geostacional, app_diabetes, app_hipertencio_arterial, app_preeclampsia_enclampsia, app_nefropatia, app_cardiopatia, app_hemorragia_obstetrica, app_padecimiento_oncologico, app_b24x, app_a539, app_enfermedad_trnas_vector, app_tuberculosis, app_sars_cov2, app_otro_antecedente, app_grupo_sanguinieo, app_rh, app_prueba_coombs, app_tratamiento, de_violencia_fam, devf_b24x_fecha_inmuno, devf_b24x_imnuno, devf_b24x_fecha_enzimo, devf_b24x_enzimo, devf_a539_fecha_inmuno, devf_a539_imnuno, devf_a539_fecha_enzimo, devf_a539_enzimo, de_depresion_prenatal, dedp_b24x_fecha_rapida, dedp_b24x_rapida, dedp_b24x_fecha_lab, dedp_b24x_lab, dedp_a539_fecha_rapidaa, dedp_a539_rapida, dedp_a539_fecha_lab, dedp_a539_lab, ag_atencio_preges, ag_riesgos, ag_gestas, ag_partos, ag_cesarias, ag_abortos, ag_ectopico, ag_mola, ag_emb_mul, ag_hijos_nac_viv, ag_ag_hijos_nac_mue, ag_resol_ult_emba, ag_resol_fecha_ul_emb, ag_uso_prev_antico_tipo, ag_uso_prev_antico_tiempo_uso, ag_uso_prev_fecha_suspe, ag_uso_prev_falla, ag_otro_ante_ginecoobs, ag_otro_ante_ginecoobs_esp, bio_fecha_td_primera, bio_fecha_td_segunda, bio_fecha_td_tercera, bio_fecha_tdpa, bio_fecha_influenza, bio_fecha_covid19, adicciones, adic_tabaco, adic_antidepre, adic_alcohol, adic_ansioliticos, adic_otros, adic_consumo_act, adic_años, adic_meses, sbde_rev_odont, sbde_atn_caries, sbde_atn_periodonitis, sbde_atn_otro, v_nuticional, pc_lab_otr_b24x_fecha, pc_lab_otr_b24x_detectable, pc_lab_otr_a539_fecha, pc_lab_otr_a539_posneg, cdg_fecha, cdg_ayuno, cdg_60, cdg_120, CONCAT_WS ('-',region_geografica_clave, region_geografica_nombre) AS lug_nac  FROM datos_identificacion INNER JOIN region_geografica ON region_geografica.region_geografica_clave = datos_identificacion.entidad_nacimiento  WHERE id_tarjeta = $id LIMIT 1";
$tarjeta = $mysqli->query($sqltarjeta);
$rowcita= $tarjeta->fetch_assoc();
$localidad = $rowcita['localidad']; 

$querylocalidad = "SELECT  localidad.l_cve_municipio,  region.cve_regiones FROM localidad  inner join municipio ON municipio.cve_municipios = localidad.l_cve_municipio inner join region ON region.cve_regiones = municipio.m_cve_region WHERE  cve_localidad = '$localidad'";

$resultadolocalidad = $mysqli->query($querylocalidad);
$rowlocalidad = $resultadolocalidad->fetch_array(MYSQLI_ASSOC);
$region = $rowlocalidad['cve_regiones'];
$municipio = $rowlocalidad['l_cve_municipio'];

$queryR = "SELECT cve_regiones, region FROM region ORDER BY region";
$resultadoR = $mysqli->query($queryR);

$queryM = "SELECT cve_municipios, municipio FROM municipio WHERE m_cve_region = '$region' ORDER BY municipio";
$resultadoM = $mysqli->query($queryM);

$queryL = "SELECT cve_localidad, localidad FROM localidad WHERE l_cve_municipio = '$municipio' ORDER BY localidad";
$resultadoL = $mysqli->query($queryL);

 
?>


<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" href="icono/ico.ico">
        <link href="assets/css/bootstrap.min.css" rel="stylesheet">
        <link href="assets/css/all.min.css" rel="stylesheet">

    	<!-- Required meta tags -->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		
		<!-- Bootstrap CSS 
		<link rel="stylesheet" href="css/bootstrap.min.css">-->
		<link rel="stylesheet" href="css/jquery.dataTables.min.css">
		
		<!-- Optional JavaScript -->
		<!-- jQuery first, then Popper.js, then Bootstrap JS -->
		<script src="js/jquery-3.4.1.min.js" ></script>
		<script src="js/bootstrap.min.js" ></script>
		<script src="js/jquery.dataTables.min.js" ></script>

        <script language="javascript" src="js/code.jquery.com_jquery-3.7.1.min"></script>
		
        <script language="javascript">
			$(document).ready(function(){
				$("#b_region").change(function () {

					$('#b_localidad').find('option').remove().end().append('<option value="whatever"></option>').val('whatever');
					
					$("#b_region option:selected").each(function () {
						cve_regiones = $(this).val();
						$.post("includes/getMunicipio.php", { cve_regiones: cve_regiones }, function(data){
							$("#b_municipio").html(data);
						});            
					});
				})
			});
			

            $(document).ready(function(){
				$("#b_municipio").change(function () {
					$("#b_municipio option:selected").each(function () {
						cve_municipios = $(this).val();
						$.post("includes/getLocalidad.php", { cve_municipios: cve_municipios }, function(data){
							$("#b_localidad").html(data);
						});            
					});
				})
			});
		</script>

        <script language="javascript">
			$(document).ready(function(){
				$("#etiquetado").change(function () {
				
					$("#etiquetado option:selected").each(function () {
						id_etiquetado = $(this).val();
						$.post("includes/getFuentefinanciamiento.php", { id_etiquetado: id_etiquetado }, function(data){
							$("#fuente_financiamiento").html(data);
						});            
					});
				})
			});
			

		</script>

<script language="javascript">
			$(document).ready(function(){
				$("#esquemageneral").change(function () {
				
					$("#esquemageneral option:selected").each(function () {
						id_esquema_general = $(this).val();
						$.post("includes/getFondosfinanciamiento.php", { id_esquema_general: id_esquema_general }, function(data){
							$("#fondo_financiamiento").html(data);
						});            
					});
				})
			});
			

		</script>


 
		<title>Tarjeta</title>
		
		<script>
			$(document).ready(function() {
			$('#tabla').DataTable();
			} );
			
		</script>
		
		<style>
			body {
			background: white;
			}
		</style>
</head>

<body class="d-flex flex-column h-100">

    <div class="container py-3">

        <h4 class="text-center">TARJETA DE ATENCIÓN INTEGRAL DEL EMBARAZO, PUERPERIO Y PERIODO DE LACTANCIA</h2>   
        
        <hr>
        <?php echo 'Usuari@: '.utf8_encode(utf8_decode($row_usuario['nombre'])); ?>
        <?php if (isset($_SESSION['msg']) && isset($_SESSION['color'])) { ?>
            <div class="alert alert-<?= $_SESSION['color']; ?> alert-dismissible fade show" role="alert">
                <?= $_SESSION['msg']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>

        <?php
            unset($_SESSION['color']);
            unset($_SESSION['msg']);
        } ?>

        <div class="row justify-content-end">
            <div class="col-auto">             
                <a href="../registrostarjeta.php" class="btn btn-dark" ></i>Regresar</a>
            </div>
        </div>
       <br>

       <ul class="nav nav-fill nav-tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="fill-tab-0" data-bs-toggle="tab" href="#fill-tabpanel-0" role="tab" aria-controls="fill-tabpanel-0" aria-selected="true">DATOS DE IDENTIFICACIÓN</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="fill-tab-1" data-bs-toggle="tab" href="#fill-tabpanel-1" role="tab" aria-controls="fill-tabpanel-1" aria-selected="false">INICIO DE ATENCIÓN PRENATAL</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="fill-tab-2" data-bs-toggle="tab" href="#fill-tabpanel-2" role="tab" aria-controls="fill-tabpanel-2" aria-selected="false">ANTECEDENTES FAMILIARES</a>
            </li>

            <li class="nav-item" role="presentation">
                <a class="nav-link" id="fill-tab-3" data-bs-toggle="tab" href="#fill-tabpanel-3" role="tab" aria-controls="fill-tabpanel-3" aria-selected="false">ANTECEDENTES PERSONALES PATOLOGICOS</a>
            </li>

            <li class="nav-item" role="presentation">
                <a class="nav-link" id="fill-tab-4" data-bs-toggle="tab" href="#fill-tabpanel-4" role="tab" aria-controls="fill-tabpanel-4" aria-selected="false">DETECCIONES EN EL EMBARAZO</a>
            </li>

            <li class="nav-item" role="presentation">
                <a class="nav-link" id="fill-tab-5" data-bs-toggle="tab" href="#fill-tabpanel-5" role="tab" aria-controls="fill-tabpanel-5" aria-selected="false">ANTECEDENTES GINECOOBSTÉTRICOS</a>
            </li>

            <li class="nav-item" role="presentation">
                <a class="nav-link" id="fill-tab-6" data-bs-toggle="tab" href="#fill-tabpanel-6" role="tab" aria-controls="fill-tabpanel-6" aria-selected="false">BIOLÓGICOS FECHA DE APLICACIÓN</a>
            </li>

            <li class="nav-item" role="presentation">
                <a class="nav-link" id="fill-tab-7" data-bs-toggle="tab" href="#fill-tabpanel-7" role="tab" aria-controls="fill-tabpanel-7" aria-selected="false">ADICCIONES</a>
            </li>

            <li class="nav-item" role="presentation">
                <a class="nav-link" id="fill-tab-8" data-bs-toggle="tab" href="#fill-tabpanel-8" role="tab" aria-controls="fill-tabpanel-8" aria-selected="false">SALUD BUCAL DURANTE EL EMBARAZO</a>
            </li>

            <li class="nav-item" role="presentation">
                <a class="nav-link" id="fill-tab-9" data-bs-toggle="tab" href="#fill-tabpanel-9" role="tab" aria-controls="fill-tabpanel-9" aria-selected="false">VALORACIÓN NUTRICIONAL</a>
            </li>

            <li class="nav-item" role="presentation">
                <a class="nav-link" id="fill-tab-10" data-bs-toggle="tab" href="#fill-tabpanel-10" role="tab" aria-controls="fill-tabpanel-10" aria-selected="false">PRUEBAS CONFIRMATORIAS LABORATORIO Y OTRAS SUPLEMENTARIAS</a>
            </li>

            <li class="nav-item" role="presentation">
                <a class="nav-link" id="fill-tab-11" data-bs-toggle="tab" href="#fill-tabpanel-11" role="tab" aria-controls="fill-tabpanel-11" aria-selected="false">CONFIRMACIÓN DE DIABETES GESTACIONAL</a>
            </li>
        </ul>

    <div class="tab-content pt-5" id="tab-content">
        <!----INICIO---->
            <div class="tab-pane active" id="fill-tabpanel-0" role="tabpanel" aria-labelledby="fill-tab-0">

                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-start text-start ">
        
                    <div class="col-md-3 m-l">
                    <label for="curp" class="form-label">CURP:</label>
                    <input  type="text" name="curp" id="curp"  class="form-control"  value="<?php echo $rowcita['curp']; ?>" required>                        
                    </div>

                    <div class="col-md-3 m-l">
                    <label for="nombre" class="form-label">NOMBRE:</label>
                    <input  type="text" name="nombre" id="nombre"  class="form-control"  value="<?php echo $rowcita['nombre']; ?>" required>                        
                    </div>

                    <div class="col-md-3 m-l">
                    <label for="primer_apellido" class="form-label">PRIMER APELLIDO:</label>
                    <input  type="text" name="primer_apellido" id="primer_apellido"  class="form-control"  value="<?php echo $rowcita['primer_apellido']; ?>" required>                        
                    </div>
            
                    <div class="col-md-3 m-l">
                    <label for="segundo_apellido" class="form-label">SEGUNDO APELLIDO:</label>
                    <input  type="text" name="segundo_apellido" id="segundo_apellido"  class="form-control"  value="<?php echo $rowcita['segundo_apellido']; ?>" required>                        
                    </div>

                </div>	
                </div>      

                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-start text-start ">
      

                    <div class="col-md-3 m-l">
                    <label for="entidad_nacimiento" class="form-label">ENTIDAD DE NACIMIENTO:</label>
                    <input  type="text" name="entidad_nacimiento" id="entidad_nacimiento"  class="form-control"  value="<?php echo $rowcita['lug_nac']; ?>" required>                        
                    </div>

                    <div class="col-md-2 m-l">
                    <label for="fecha_nacimiento" class="form-label" value="<?php echo $rowcita['fecha_nacimiento']; ?>">FECHA NACIMIENTO:</label>
                    <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" class="form-control" value="<?php echo $rowcita['fecha_nacimiento']; ?>" required>
                    </div>	

                    <div class="col-md-1 m-l">
                    <label for="edad" class="form-label">EDAD:</label>
                    <input  type="text" name="edad" id="edad"  class="form-control"  value="<?php echo $rowcita['edad']; ?>" required>                        
                    </div>

                    <div class="col-md-3 m-l">
                    <label for="derechohabiencia" class="form-label">DERECHOHABIENCIA:</label>
                    <select name="derechohabiencia" id="derechohabiencia" class="form-select" required>
                        <option value="">Seleccionar...</option>
                        <option value="1"<?php if('1' == $rowcita['derechohabiencia']) { echo 'selected'; } ?>>1.NINGUNA</option>
                        <option value="2"<?php if('2' == $rowcita['derechohabiencia']) { echo 'selected'; } ?>>2.IMSS</option>
                        <option value="3"<?php if('3' == $rowcita['derechohabiencia']) { echo 'selected'; } ?>>3.ISSSTE</option>
                        <option value="4"<?php if('4' == $rowcita['derechohabiencia']) { echo 'selected'; } ?>>4.PEMEX</option>
                        <option value="5"<?php if('5' == $rowcita['derechohabiencia']) { echo 'selected'; } ?>>5.SEDENA</option>  
                        <option value="6"<?php if('6' == $rowcita['derechohabiencia']) { echo 'selected'; } ?>>6.SEMAR</option>
                        <option value="10"<?php if('10' == $rowcita['derechohabiencia']) { echo 'selected'; } ?>>10.IMSS BIENESTAR</option>
                        <option value="11"<?php if('11' == $rowcita['derechohabiencia']) { echo 'selected'; } ?>>11.ISSFAM</option>
                        <option value="14"<?php if('14' == $rowcita['derechohabiencia']) { echo 'selected'; } ?>>14. OPD IMSS BIENESTAR</option>
                        <option value="8"<?php if('8' == $rowcita['derechohabiencia']) { echo 'selected'; } ?>>8.OTRA</option>      
                        <option value="99"<?php if('99' == $rowcita['derechohabiencia']) { echo 'selected'; } ?>>99.SE IGNORA</option>         
                    </select>
                    </div>	
           
                    <div class="col-md-3 m-l">
                    <label for="estado_conyugal" class="form-label">ESTADO CONYUGAL:</label>
                    <select name="estado_conyugal" id="estado_conyugal" class="form-select" required>
                        <option value="">Seleccionar...</option>
                        <option value="1"<?php if('1' == $rowcita['estado_conyugal']) { echo 'selected'; } ?>>1.SOLTERA</option>
                        <option value="2"<?php if('2' == $rowcita['estado_conyugal']) { echo 'selected'; } ?>>2.CASADA</option>
                        <option value="3"<?php if('3' == $rowcita['estado_conyugal']) { echo 'selected'; } ?>>3.DIVORCIADA</option>
                        <option value="4"<?php if('4' == $rowcita['estado_conyugal']) { echo 'selected'; } ?>>4.VIUDA</option>
                        <option value="5"<?php if('5' == $rowcita['estado_conyugal']) { echo 'selected'; } ?>>5.UNIÓN LIBRE</option>  
                        <option value="6"<?php if('6' == $rowcita['estado_conyugal']) { echo 'selected'; } ?>>6.SEPARADA</option>
                        <option value="9"<?php if('9' == $rowcita['estado_conyugal']) { echo 'selected'; } ?>>9.NO ESPECIFICADO</option>     
                    </select>
                    </div>	

                </div>	
                </div>

                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-start text-start ">

                    <div class="col-md-3 m-l">
                    <label for="escolaridad" class="form-label">ESCOLARIDAD:</label>
                    <select name="escolaridad" id="escolaridad" class="form-select" required>
                        <option value="">Seleccionar...</option>
                        <option value="0"<?php if('0' == $rowcita['escolaridad']) { echo 'selected'; } ?>>0.NINGUNA</option>
                        <option value="1"<?php if('1' == $rowcita['escolaridad']) { echo 'selected'; } ?>>1.PRIMARIA</option>
                        <option value="2"<?php if('2' == $rowcita['escolaridad']) { echo 'selected'; } ?>>2.SECUNDARIA</option>
                        <option value="3"<?php if('3' == $rowcita['escolaridad']) { echo 'selected'; } ?>>3.PREPARATORIA</option>
                        <option value="4"<?php if('4' == $rowcita['escolaridad']) { echo 'selected'; } ?>>4.LICENCIATURA</option>  
                        <option value="5"<?php if('5' == $rowcita['escolaridad']) { echo 'selected'; } ?>>5.POSGRADO</option>     
                    </select>
                    </div>	

                    <div class="col-md-3 m-l">
                    <label for="tipo" class="form-label">TIPO:</label>
                    <select name="tipo" id="tipo" class="form-select" required>
                        <option value="">Seleccionar...</option>
                        <option value="1"<?php if('1' == $rowcita['tipo']) { echo 'selected'; } ?>>1.COMPLETO</option>
                        <option value="2"<?php if('2' == $rowcita['tipo']) { echo 'selected'; } ?>>2.INCOMPLETO</option>   
                    </select>
                    </div>	

                    <div class="col-md-2 m-l">
                    <label for="expediente" class="form-label">EXPEDIENTE:</label>
                    <input  type="text" name="expediente" id="expediente"  class="form-control"  value="<?php echo $rowcita['expediente']; ?>" required>                        
                    </div>

                    <div class="col-md-2 m-l">
                    <label for="peso_ant_emb" class="form-label">PESO:</label>
                    <input  type="text" name="peso_ant_emb" id="peso_ant_emb"  class="form-control"  value="<?php echo $rowcita['peso_ant_emb']; ?>" required>                        
                    </div>

                    <div class="col-md-2 m-l">
                    <label for="talla" class="form-label">TALLA:</label>
                    <input  type="text" name="talla" id="talla"  class="form-control"  value="<?php echo $rowcita['talla']; ?>" required>                        
                    </div>

                </div>	
                </div>

                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-start text-start ">
                        
                    <div class="col-md-3 m-l">
                    <label for="imc" class="form-label">IMC:</label>
                    <input  type="text" name="imc" id="imc"  class="form-control"  value="<?php echo $rowcita['imc']; ?>" required>                        
                    </div>

                    <div class="col-md-3 m-l">
                    <label for="afromexicana" class="form-label">SE CONSIDERA AFROMEXICANA:</label>
                    <select name="afromexicana" id="afromexicana" class="form-select" required>
                        <option value="">Seleccionar...</option>
                        <option value="SI"<?php if('SI' == $rowcita['afromexicana']) { echo 'selected'; } ?>>SI</option>
                        <option value="NO"<?php if('NO' == $rowcita['afromexicana']) { echo 'selected'; } ?>>NO</option>   
                    </select>
                    </div>	

                    <div class="col-md-2 m-l">
                    <label for="migragrante" class="form-label">MIGRANTE:</label>
                    <select name="migragrante" id="migragrante" class="form-select" required>
                        <option value="">Seleccionar...</option>
                        <option value="SI"<?php if('SI' == $rowcita['migragrante']) { echo 'selected'; } ?>>SI</option>
                        <option value="NO"<?php if('NO' == $rowcita['migragrante']) { echo 'selected'; } ?>>NO</option>   
                    </select>
                    </div>	

                    <div class="col-md-2 m-l">
                    <label for="nacional" class="form-label">NACIONAL:</label>
                    <select name="nacional" id="nacional" class="form-select" required>
                        <option value="">Seleccionar...</option>
                        <option value="SI"<?php if('SI' == $rowcita['nacional']) { echo 'selected'; } ?>>SI</option>
                        <option value="NO"<?php if('NO' == $rowcita['nacional']) { echo 'selected'; } ?>>NO</option>   
                    </select>
                    </div>	

                    <div class="col-md-2 m-l">
                    <label for="internacional" class="form-label">INTERNACIONAL:</label>
                    <select name="internacional" id="internacional" class="form-select" required>
                        <option value="">Seleccionar...</option>
                        <option value="SI"<?php if('SI' == $rowcita['internacional']) { echo 'selected'; } ?>>SI</option>
                        <option value="NO"<?php if('NO' == $rowcita['internacional']) { echo 'selected'; } ?>>NO</option>   
                    </select>
                    </div>	

                </div>	
                </div>

                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-start text-start ">

                    <div class="col-md-3 m-l">
                    <label for="indigena" class="form-label">¿ES O SE CONSIDERA INDÍGENA?:</label>
                    <select name="indigena" id="indigena" class="form-select" required>
                        <option value="">Seleccionar...</option>
                        <option value="SI"<?php if('SI' == $rowcita['indigena']) { echo 'selected'; } ?>>SI</option>
                        <option value="NO"<?php if('NO' == $rowcita['indigena']) { echo 'selected'; } ?>>NO</option>   
                    </select>
                    </div>	

                    <div class="col-md-3 m-l">
                    <label for="lengua_indigena" class="form-label">HABLA LENGUA INDÍGENA COMO LENGUA MATERNA</label>
                    <select name="lengua_indigena" id="lengua_indigena" class="form-select" required>
                        <option value="">Seleccionar...</option>
                        <option value="SI"<?php if('SI' == $rowcita['lengua_indigena']) { echo 'selected'; } ?>>SI</option>
                        <option value="NO"<?php if('NO' == $rowcita['lengua_indigena']) { echo 'selected'; } ?>>NO</option>   
                    </select>
                    </div>	

                    <div class="col-md-3 m-l">
                    <label for="lengua_habla" class="form-label">CUÁL LENGUA INDÍGENA HABLA:</label>
                    <input  type="text" name="lengua_habla" id="lengua_habla"  class="form-control"  value="<?php echo $rowcita['expediente']; ?>" required>                        
                    </div>

                    <div class="col-md-3 m-l">
                    <label for="habla_espanol" class="form-label">¿HABLA ESPAÑOL?:</label>
                    <select name="habla_espanol" id="habla_espanol" class="form-select" required>
                        <option value="">Seleccionar...</option>
                        <option value="SI"<?php if('SI' == $rowcita['habla_espanol']) { echo 'selected'; } ?>>SI</option>
                        <option value="NO"<?php if('NO' == $rowcita['habla_espanol']) { echo 'selected'; } ?>>NO</option>   
                    </select>
                    </div>	

                </div>	
                </div>
         
                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-start text-start ">

                    <div class="col-md-3 m-l">
                    <label for="calle" class="form-label">CALLE:</label>
                    <input  type="text" name="calle" id="calle"  class="form-control"  value="<?php echo $rowcita['calle']; ?>" required>                        
                    </div>

                    <div class="col-md-2 m-l">
                    <label for="numero_int" class="form-label">NÚMERO INT.:</label>
                    <input  type="text" name="numero_int" id="numero_int"  class="form-control"  value="<?php echo $rowcita['numero_int']; ?>" required>                        
                    </div>

                    <div class="col-md-2 m-l">
                    <label for="numero_ext" class="form-label">NÚMERO EXT.:</label>
                    <input  type="text" name="numero_ext" id="numero_ext"  class="form-control"  value="<?php echo $rowcita['numero_ext']; ?>" required>                        
                    </div>

                    <div class="col-md-3 m-l">
                    <label for="colonia" class="form-label">COLONIA:</label>
                    <input  type="text" name="colonia" id="colonia"  class="form-control"  value="<?php echo $rowcita['colonia']; ?>" required>                        
                    </div>

                    <div class="col-md-2 m-l">
                    <label for="cp" class="form-label">C.P.:</label>
                    <input  type="text" name="cp" id="cp"  class="form-control"  value="<?php echo $rowcita['cp']; ?>" required>                        
                    </div>
        
                </div>	
                </div>
        
                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-start text-start ">

                    <div class="col-md-3 m-l">
                    <label for="b_region" class="form-label">REGIÓN:</label>
                    <select name="b_region" id="b_region" class="form-select" required>
                        <option value="">Selecciona la región</option>
                        <?php while($rowR = $resultadoR->fetch_assoc()) { ?>
                            <option value="<?php echo $rowR['cve_regiones']; ?>" <?php if($rowR['cve_regiones']==$region) { echo 'selected'; } ?>><?php echo $rowR['region']; ?></option>
                        <?php } ?>
                    </select>
                    </div>

                    <div class="col-md-3 m-l">   
                    <label for="b_municipio" class="form-label">MUNICIPIO:</label>
                    <select name="b_municipio" id="b_municipio" class="form-select" required>
                        <?php while($rowM = $resultadoM->fetch_assoc()) { ?>
                            <option value="<?php echo $rowM['cve_municipios']; ?>" <?php if($rowM['cve_municipios']==$municipio) { echo 'selected'; } ?>><?php echo $rowM['municipio']; ?></option>
                        <?php } ?>
                    </select>
                    </div>

                    <div class="col-md-4 m-l">
                    <label for="b_localidad" class="form-label">LOCALIDAD:</label>
                    <select name="b_localidad" id="b_localidad" class="form-select" required> 
                    <?php while($rowL = $resultadoL->fetch_assoc()) { ?>
                            <option value="<?php echo $rowL['cve_localidad']; ?>" <?php if($rowL['cve_localidad']==$localidad) { echo 'selected'; } ?>><?php echo $rowL['localidad']; ?></option>
                        <?php } ?>
                    </select>
                    </div>

                    <div class="col-md-2 m-l">
                    <label for="telefono" class="form-label">TELÉFONO:</label>
                    <input  type="text" name="telefono" id="telefono"  class="form-control"  value="<?php echo $rowcita['telefono']; ?>" required>                        
                    </div>
        
                </div>
                </div> 

            </div> 
        <!----FIN---->    

        <!----INICIO---->
            <div class="tab-pane active" id="fill-tabpanel-1" role="tabpanel" aria-labelledby="fill-tab-1">
            
                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-start text-start ">
            
                    <div class="col-md-4 m-l">
                    <label for="ap_personal_comunitario" class="form-label">REFERIDA POR PERSONAL COMUNITARIO</label>
                    <select name="ap_personal_comunitario" id="ap_personal_comunitario" class="form-select" required>
                        <option value="">Seleccionar...</option>
                        <option value="0"<?php if('0' == $rowcita['ap_personal_comunitario']) { echo 'selected'; } ?>>0.NINGUNO</option>
                        <option value="1"<?php if('1' == $rowcita['ap_personal_comunitario']) { echo 'selected'; } ?>>1.BRIGADISTA Y/O PROMOTOR(A) DE SALUD</option>   
                        <option value="2"<?php if('2' == $rowcita['ap_personal_comunitario']) { echo 'selected'; } ?>>2.AUXILIAR DE SALUD</option>
                        <option value="3"<?php if('3' == $rowcita['ap_personal_comunitario']) { echo 'selected'; } ?>>3.ENFERMERÍA</option>   
                        <option value="4"<?php if('4' == $rowcita['ap_personal_comunitario']) { echo 'selected'; } ?>>4.PARTERÍA PROFESIONAL</option>
                        <option value="5"<?php if('5' == $rowcita['ap_personal_comunitario']) { echo 'selected'; } ?>>5.PARTERÍA TRADICIONAL</option>   
                        <option value="6"<?php if('6' == $rowcita['ap_personal_comunitario']) { echo 'selected'; } ?>>6.SUPERVISOR(A) DE AUXILIARES DE SALUD</option>
                        <option value="7"<?php if('7' == $rowcita['ap_personal_comunitario']) { echo 'selected'; } ?>>7.MÉDICA(O)</option>   
                        <option value="8"<?php if('8' == $rowcita['ap_personal_comunitario']) { echo 'selected'; } ?>>8.OTRO</option>
                    </select>
                    </div>	
    
                    <div class="col-md-2 m-l">
                    <label for="ap_fecha" class="form-label" value="<?php echo $rowcita['ap_fecha']; ?>">FECHA NACIMIENTO:</label>
                    <input type="date" name="ap_fecha" id="ap_fecha" class="form-control" value="<?php echo $rowcita['ap_fecha']; ?>" required>
                    </div>	
        
                    <div class="col-md-2 m-l">
                    <label for="ap_trimestre" class="form-label">TRIMESTRE</label>
                    <select name="ap_trimestre" id="ap_trimestre" class="form-select" required>
                        <option value="">Seleccionar...</option>
                        <option value="1"<?php if('1' == $rowcita['ap_trimestre']) { echo 'selected'; } ?>>1° TRIMESTRE</option>
                        <option value="2"<?php if('2' == $rowcita['ap_trimestre']) { echo 'selected'; } ?>>2° TRIMESTRE</option>   
                        <option value="3"<?php if('3' == $rowcita['ap_trimestre']) { echo 'selected'; } ?>>3° TRIMESTRE</option>
                    </select>
                    </div>	
    
                    <div class="col-md-2 m-l">
                    <label for="ap_puerperio" class="form-label">PUERPERIO</label>
                    <select name="ap_puerperio" id="ap_puerperio" class="form-select" required>
                        <option value="">Seleccionar...</option>
                        <option value="SI"<?php if('SI' == $rowcita['ap_puerperio']) { echo 'selected'; } ?>>SI</option>
                        <option value="NO"<?php if('NO' == $rowcita['ap_puerperio']) { echo 'selected'; } ?>>NO</option> 
                    </select>
                    </div>	
    
                    <div class="col-md-2 m-l">
                    <label for="ap_lactancia" class="form-label">LACTANCIA</label>
                    <select name="ap_lactancia" id="ap_lactancia" class="form-select" required>
                        <option value="">Seleccionar...</option>
                        <option value="SI"<?php if('SI' == $rowcita['ap_lactancia']) { echo 'selected'; } ?>>SI</option>
                        <option value="NO"<?php if('NO' == $rowcita['ap_lactancia']) { echo 'selected'; } ?>>NO</option> 
                    </select>
                    </div>	

                </div>	
                </div>	
            
                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-start text-start ">
    
                    <div class="col-md-4 m-l">
                    <label for="ap_prueba_serologica_embarazo" class="form-label">PRUEBA SEROLÓGICA DE EMBARAZO:</label>
                    <select name="ap_prueba_serologica_embarazo" id="ap_prueba_serologica_embarazo" class="form-select" required>
                        <option value="">Seleccionar...</option>
                        <option value="SI"<?php if('SI' == $rowcita['ap_prueba_serologica_embarazo']) { echo 'selected'; } ?>>SI</option>
                        <option value="NO"<?php if('NO' == $rowcita['ap_prueba_serologica_embarazo']) { echo 'selected'; } ?>>NO</option> 
                    </select>
                    </div>	
    
                    <div class="col-md-4 m-l">
                    <label for="ap_se_ignora" class="form-label">ÚLTIMA MENSTRUACIÓN SE IGNORA:</label>
                    <select name="ap_se_ignora" id="ap_se_ignora" class="form-select" required>
                        <option value="">Seleccionar...</option>
                        <option value="SI"<?php if('SI' == $rowcita['ap_se_ignora']) { echo 'selected'; } ?>>SI</option>
                        <option value="NO"<?php if('NO' == $rowcita['ap_se_ignora']) { echo 'selected'; } ?>>NO</option> 
                    </select>
                    </div>	
    
                    <div class="col-md-4 m-l">
                    <label for="ap_ulima_mestruacion" class="form-label" value="<?php echo $rowcita['ap_ulima_mestruacion']; ?>">FECHA:</label>
                    <input type="date" name="ap_ulima_mestruacion" id="ap_ulima_mestruacion" class="form-control" value="<?php echo $rowcita['ap_ulima_mestruacion']; ?>" required>
                    </div>	
    
                </div>	
                </div>    
    
                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-start text-start ">
    
                    <div class="col-md-4 m-l">
                    <label for="ap_confiable" class="form-label">CONFIABLE:</label>
                    <select name="ap_confiable" id="ap_confiable" class="form-select" required>
                        <option value="">Seleccionar...</option>
                        <option value="SI"<?php if('SI' == $rowcita['ap_confiable']) { echo 'selected'; } ?>>SI</option>
                        <option value="NO"<?php if('NO' == $rowcita['ap_confiable']) { echo 'selected'; } ?>>NO</option> 
                    </select>
                    </div>	
    
                    <div class="col-md-4 m-l">
                    <label for="ap_fecha_ultrasonido_obstretico" class="form-label" value="<?php echo $rowcita['ap_fecha_ultrasonido_obstretico']; ?>">FECHA DE ULTRASONIDO OBSTÉTRICO:</label>
                    <input type="date" name="ap_fecha_ultrasonido_obstretico" id="ap_fecha_ultrasonido_obstretico" class="form-control" value="<?php echo $rowcita['ap_fecha_ultrasonido_obstretico']; ?>" required>
                    </div>	
    
                    <div class="col-md-4 m-l">
                    <label for="ap_semana_gestacion_usg" class="form-label">SEMANAS DE GESTACIÓN POR USG:</label>
                    <input  type="text" name="ap_semana_gestacion_usg" id="ap_semana_gestacion_usg"  class="form-control"  value="<?php echo $rowcita['ap_semana_gestacion_usg']; ?>" required>                        
                    </div>   
                                    
                </div>	
                </div>
    
                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-start text-start ">
    
                    <div class="col-md-4 m-l">
                    <label for="ap_fecha_probable_parto" class="form-label" value="<?php echo $rowcita['ap_fecha_probable_parto']; ?>"> FECHA PROBABLE DE PARTO:</label>
                    <input type="date" name="ap_fecha_probable_parto" id="ap_fecha_probable_parto" class="form-control" value="<?php echo $rowcita['ap_fecha_probable_parto']; ?>" required>
                    </div>	
    
                </div>	
                </div>  

            </div> 
        <!----FIN----> 
        <!----INICIO---->
            <div class="tab-pane active" id="fill-tabpanel-2" role="tabpanel" aria-labelledby="fill-tab-2">
    
                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-start text-start ">
        
                    <div class="col-md-3 m-l">
                    <label for="af_ninguno" class="form-label">NINGUNO:</label>
                    <select name="af_ninguno" id="af_ninguno" class="form-select" required>
                        <option value="">Seleccionar...</option>
                        <option value="SI"<?php if('SI' == $rowcita['af_ninguno']) { echo 'selected'; } ?>>SI</option>
                        <option value="NO"<?php if('NO' == $rowcita['af_ninguno']) { echo 'selected'; } ?>>NO</option> 
                    </select>
                    </div>       
    
                    <div class="col-md-3 m-l">
                    <label for="af_tuberculosis" class="form-label">TUBERCULOSIS:</label>
                    <select name="af_tuberculosis" id="af_tuberculosis" class="form-select" required>
                        <option value="">Seleccionar...</option>
                        <option value="SI"<?php if('SI' == $rowcita['af_tuberculosis']) { echo 'selected'; } ?>>SI</option>
                        <option value="NO"<?php if('NO' == $rowcita['af_tuberculosis']) { echo 'selected'; } ?>>NO</option> 
                    </select>
                    </div>
    
                    <div class="col-md-3 m-l">
                    <label for="af_hipertencion" class="form-label">HIPERTENSIÓN:</label>
                    <select name="af_hipertencion" id="af_hipertencion" class="form-select" required>
                        <option value="">Seleccionar...</option>
                        <option value="SI"<?php if('SI' == $rowcita['af_hipertencion']) { echo 'selected'; } ?>>SI</option>
                        <option value="NO"<?php if('NO' == $rowcita['af_hipertencion']) { echo 'selected'; } ?>>NO</option> 
                    </select>
                    </div>
        
                    <div class="col-md-3 m-l">
                    <label for="af_diabetes" class="form-label">DIABETES:</label>
                    <select name="af_diabetes" id="af_diabetes" class="form-select" required>
                        <option value="">Seleccionar...</option>
                        <option value="SI"<?php if('SI' == $rowcita['af_diabetes']) { echo 'selected'; } ?>>SI</option>
                        <option value="NO"<?php if('NO' == $rowcita['af_diabetes']) { echo 'selected'; } ?>>NO</option> 
                    </select>
                    </div>
                    
                </div>	
                </div>      
    
                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-start text-start ">    
    
                    <div class="col-md-4 m-l">
                    <label for="af_ef_hiper_embarazo" class="form-label">ENF. HIPERTENSIVA DEL EMBARAZO:</label>
                    <select name="af_ef_hiper_embarazo" id="af_ef_hiper_embarazo" class="form-select" required>
                        <option value="">Seleccionar...</option>
                        <option value="SI"<?php if('SI' == $rowcita['af_ef_hiper_embarazo']) { echo 'selected'; } ?>>SI</option>
                        <option value="NO"<?php if('NO' == $rowcita['af_ef_hiper_embarazo']) { echo 'selected'; } ?>>NO</option> 
                    </select>
                    </div>
    
                    <div class="col-md-3 m-l">
                    <label for="af_otro" class="form-label">OTRO:</label>
                    <select name="af_otro" id="af_otro" class="form-select" required>
                        <option value="">Seleccionar...</option>
                        <option value="SI"<?php if('SI' == $rowcita['af_otro']) { echo 'selected'; } ?>>SI</option>
                        <option value="NO"<?php if('NO' == $rowcita['af_otro']) { echo 'selected'; } ?>>NO</option> 
                    </select>
                    </div>
            
                    <div class="col-md-5 m-l">
                    <label for="af_otro_especifique" class="form-label">ESPECIFIQUE:</label>
                    <input  type="text" name="af_otro_especifique" id="af_otro_especifique"  class="form-control"  value="<?php echo $rowcita['af_otro_especifique']; ?>" required>                        
                    </div>   
            
                </div>	
                </div> 

            </div> 
        <!----FIN---->    

        <!----INICIO---->
            <div class="tab-pane active" id="fill-tabpanel-3" role="tabpanel" aria-labelledby="fill-tab-3">

                    <div class="container-fluid p-2 text-center">
                    <div class="row justify-content-start text-start ">  

                        <div class="col-md-2 m-l">
                        <label for="app_ninguno" class="form-label">NINGUNO:</label>
                        <select name="app_ninguno" id="app_ninguno" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowcita['app_ninguno']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowcita['app_ninguno']) { echo 'selected'; } ?>>NO</option> 
                        </select>
                        </div>   
                        
                        <div class="col-md-4 m-l">
                        <label for="app_diabetes_geostacional" class="form-label">DIABETES GESTACIONAL:</label>
                        <select name="app_diabetes_geostacional" id="app_diabetes_geostacional" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowcita['app_diabetes_geostacional']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowcita['app_diabetes_geostacional']) { echo 'selected'; } ?>>NO</option> 
                        </select>
                        </div>       

                        <div class="col-md-2 m-l">
                        <label for="app_diabetes" class="form-label">DIABETES:</label>
                        <select name="app_diabetes" id="app_diabetes" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowcita['app_diabetes']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowcita['app_diabetes']) { echo 'selected'; } ?>>NO</option> 
                        </select>
                        </div>       

                        <div class="col-md-4 m-l">
                        <label for="app_hipertencio_arterial" class="form-label">HIPERTENSIÓN ARTERIAL SISTÉMICA:</label>
                        <select name="app_hipertencio_arterial" id="app_hipertencio_arterial" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowcita['app_hipertencio_arterial']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowcita['app_hipertencio_arterial']) { echo 'selected'; } ?>>NO</option> 
                        </select>
                        </div>       
                        
                    </div>	
                    </div> 

                    <div class="container-fluid p-2 text-center">
                    <div class="row justify-content-start text-start ">  

                        <div class="col-md-4 m-l">
                        <label for="app_preeclampsia_enclampsia" class="form-label">PREECLAMPSIA/ECLAMPSIA:</label>
                        <select name="app_preeclampsia_enclampsia" id="app_preeclampsia_enclampsia" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowcita['app_preeclampsia_enclampsia']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowcita['app_preeclampsia_enclampsia']) { echo 'selected'; } ?>>NO</option> 
                        </select>
                        </div>   

                        <div class="col-md-2 m-l">
                        <label for="app_nefropatia" class="form-label">NEFROPATÍA:</label>
                        <select name="app_nefropatia" id="app_nefropatia" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowcita['app_nefropatia']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowcita['app_nefropatia']) { echo 'selected'; } ?>>NO</option> 
                        </select>
                        </div>

                        <div class="col-md-2 m-l">
                        <label for="app_cardiopatia" class="form-label">CARDIOPATÍA:</label>
                        <select name="app_cardiopatia" id="app_cardiopatia" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowcita['app_cardiopatia']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowcita['app_cardiopatia']) { echo 'selected'; } ?>>NO</option> 
                        </select>
                        </div>

                        <div class="col-md-4 m-l">
                        <label for="app_hemorragia_obstetrica" class="form-label">HEMORRAGIA OBSTÉTRICA:</label>
                        <select name="app_hemorragia_obstetrica" id="app_hemorragia_obstetrica" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowcita['app_hemorragia_obstetrica']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowcita['app_hemorragia_obstetrica']) { echo 'selected'; } ?>>NO</option> 
                        </select>
                        </div>

                    </div>	
                    </div>      

                    <div class="container-fluid p-2 text-center">
                    <div class="row justify-content-start text-start ">  
                        
                        <div class="col-md-4 m-l">
                        <label for="app_padecimiento_oncologico" class="form-label">PADECIMIENTO ONCOLÓGICO:</label>
                        <select name="app_padecimiento_oncologico" id="app_padecimiento_oncologico" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowcita['app_padecimiento_oncologico']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowcita['app_padecimiento_oncologico']) { echo 'selected'; } ?>>NO</option> 
                        </select>
                        </div>

                        <div class="col-md-2 m-l">
                        <label for="app_b24x" class="form-label">B24X:</label>
                        <select name="app_b24x" id="app_b24x" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowcita['app_b24x']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowcita['app_b24x']) { echo 'selected'; } ?>>NO</option> 
                        </select>
                        </div>

                        <div class="col-md-2 m-l">
                        <label for="app_a539" class="form-label">A539:</label>
                        <select name="app_a539" id="app_a539" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowcita['app_a539']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowcita['app_a539']) { echo 'selected'; } ?>>NO</option> 
                        </select>
                        </div>

                        <div class="col-md-4 m-l">
                        <label for="app_enfermedad_trnas_vector" class="form-label">ENFERMEDAD TRANSMITIDA POR VECTOR:</label>
                        <select name="app_enfermedad_trnas_vector" id="app_enfermedad_trnas_vector" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <option value="1"<?php if('1' == $rowcita['app_enfermedad_trnas_vector']) { echo 'selected'; } ?>>1.DENGUE</option>
                            <option value="2"<?php if('2' == $rowcita['app_enfermedad_trnas_vector']) { echo 'selected'; } ?>>2.ZIKA</option> 
                            <option value="3"<?php if('3' == $rowcita['app_enfermedad_trnas_vector']) { echo 'selected'; } ?>>3.CHIKUNGUNYA</option> 
                            <option value="4"<?php if('4' == $rowcita['app_enfermedad_trnas_vector']) { echo 'selected'; } ?>>4.CHAGAS</option> 
                        </select>
                        </div>

                    </div>
                    </div>

                    <div class="container-fluid p-2 text-center">
                    <div class="row justify-content-start text-start "> 

                        <div class="col-md-4 m-l">
                        <label for="app_tuberculosis" class="form-label">TUBERCULOSIS:</label>
                        <select name="app_tuberculosis" id="app_tuberculosis" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowcita['app_tuberculosis']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowcita['app_tuberculosis']) { echo 'selected'; } ?>>NO</option> 
                        </select>
                        </div>

                        <div class="col-md-4 m-l">
                        <label for="app_sars_cov2" class="form-label">SARS-COV2:</label>
                        <select name="app_sars_cov2" id="app_sars_cov2" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowcita['app_sars_cov2']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowcita['app_sars_cov2']) { echo 'selected'; } ?>>NO</option> 
                        </select>
                        </div>           

                        <div class="col-md-4 m-l">
                        <label for="app_padecimiento_oncologico" class="form-label">OTRO ANTECEDENTE:</label>
                        <select name="app_padecimiento_oncologico" id="app_padecimiento_oncologico" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <option value="1"<?php if('1' == $rowcita['app_padecimiento_oncologico']) { echo 'selected'; } ?>>1.ENDOCRINOPATÍA</option>
                            <option value="2"<?php if('2' == $rowcita['app_padecimiento_oncologico']) { echo 'selected'; } ?>>2.EPILEPSIA</option> 
                            <option value="3"<?php if('3' == $rowcita['app_padecimiento_oncologico']) { echo 'selected'; } ?>>3.LUPUS</option> 
                            <option value="4"<?php if('4' == $rowcita['app_padecimiento_oncologico']) { echo 'selected'; } ?>>4.ARTRITIS REUMATOIDE</option> 
                            <option value="5"<?php if('5' == $rowcita['app_padecimiento_oncologico']) { echo 'selected'; } ?>>5.TOXOPLASMOSIS</option> 
                            <option value="6"<?php if('6' == $rowcita['app_padecimiento_oncologico']) { echo 'selected'; } ?>>6.ITS</option> 
                            <option value="7"<?php if('7' == $rowcita['app_padecimiento_oncologico']) { echo 'selected'; } ?>>7.HEPATITIS B</option> 
                            <option value="8"<?php if('8' == $rowcita['app_padecimiento_oncologico']) { echo 'selected'; } ?>>8.HEPATITIS C</option> 
                            <option value="9"<?php if('9' == $rowcita['app_padecimiento_oncologico']) { echo 'selected'; } ?>>9.INSUFICIENCIA VENOSA</option> 
                            <option value="10"<?php if('10' == $rowcita['app_padecimiento_oncologico']) { echo 'selected'; } ?>>10.TROMBOSIS VENOSA PROFUNDA</option> 
                            <option value="11"<?php if('11' == $rowcita['app_padecimiento_oncologico']) { echo 'selected'; } ?>>11.CAPACIDAD MENTAL DISMINUIDA</option>  
                        </select>
                        </div>  

                    </div>	
                    </div> 

                    <div class="container-fluid p-2 text-center">
                    <div class="row justify-content-start text-start ">

                        <div class="col-md-3 m-l">
                        <label for="app_padecimiento_oncologico" class="form-label">GRUPO SANGUÍNEO:</label>
                        <select name="app_padecimiento_oncologico" id="app_padecimiento_oncologico" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <option value="A+"<?php if('A+' == $rowcita['app_padecimiento_oncologico']) { echo 'selected'; } ?>>A+</option>
                            <option value="B+"<?php if('B+' == $rowcita['app_padecimiento_oncologico']) { echo 'selected'; } ?>>B+</option> 
                            <option value="AB+"<?php if('AB+' == $rowcita['app_padecimiento_oncologico']) { echo 'selected'; } ?>>AB+</option>
                            <option value="AB-"<?php if('AB-' == $rowcita['app_padecimiento_oncologico']) { echo 'selected'; } ?>>AB-</option> 
                            <option value="A-"<?php if('A-' == $rowcita['app_padecimiento_oncologico']) { echo 'selected'; } ?>>A-</option>
                            <option value="B-"<?php if('B-' == $rowcita['app_padecimiento_oncologico']) { echo 'selected'; } ?>>B-</option> 
                            <option value="O+"<?php if('O+' == $rowcita['app_padecimiento_oncologico']) { echo 'selected'; } ?>>O+</option>
                            <option value="O-"<?php if('O-' == $rowcita['app_padecimiento_oncologico']) { echo 'selected'; } ?>>O-</option> 
                        </select>
                        </div>

                        <div class="col-md-3 m-l">
                        <label for="app_padecimiento_oncologico" class="form-label">Rh:</label>
                        <select name="app_padecimiento_oncologico" id="app_padecimiento_oncologico" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <option value="POSITIVO"<?php if('POSITIVO' == $rowcita['app_padecimiento_oncologico']) { echo 'selected'; } ?>>POSITIVO</option>
                            <option value="NEGATIVO"<?php if('NEGATIVO' == $rowcita['app_padecimiento_oncologico']) { echo 'selected'; } ?>>NEGATIVO</option> 
                        </select>
                        </div>

                        <div class="col-md-3 m-l">
                        <label for="app_padecimiento_oncologico" class="form-label">¿SE REALIZÓ PRUEBA COOMBS?</label>
                        <select name="app_padecimiento_oncologico" id="app_padecimiento_oncologico" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowcita['app_padecimiento_oncologico']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowcita['app_padecimiento_oncologico']) { echo 'selected'; } ?>>NO</option> 
                        </select>
                        </div>

                        <div class="col-md-3 m-l">
                        <label for="app_padecimiento_oncologico" class="form-label">¿TRATAMIENTO?</label>
                        <select name="app_padecimiento_oncologico" id="app_padecimiento_oncologico" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowcita['app_padecimiento_oncologico']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowcita['app_padecimiento_oncologico']) { echo 'selected'; } ?>>NO</option> 
                        </select>
                        </div>

                    </div>	
                    </div>

            </div> 
        <!----FIN---->    
        
        <!----INICIO---->
            <div class="tab-pane active" id="fill-tabpanel-4" role="tabpanel" aria-labelledby="fill-tab-4">

                <center> <left> <h2 class="modal-title fs-5" id="editaModalLabel">ITS 1a DETECCIÓN</h2></left></center>    

                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-start text-start ">  
                    <center>
                        <div class="col-md-3 m-l">
                        <label for="de_violencia_fam" class="form-label">VIOLENCIA FAMILIAR</label>
                        <select name="de_violencia_fam" id="de_violencia_fam" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowcita['de_violencia_fam']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowcita['de_violencia_fam']) { echo 'selected'; } ?>>NO</option> 
                        </select>
                        </div>     
                    </center>
                    <center> <left> <h3 class="modal-title fs-5" id="editaModalLabel">B24X</h3></left></center>
                </div>	
                </div> 

                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-start text-start ">  

                    <div class="col-md-2 m-l">
                    <label for="devf_b24x_fecha_inmuno" class="form-label" value="<?php echo $rowcita['devf_b24x_fecha_inmuno']; ?>">FECHA :</label>
                    <input type="date" name="devf_b24x_fecha_inmuno" id="devf_b24x_fecha_inmuno" class="form-control" value="<?php echo $rowcita['devf_b24x_fecha_inmuno']; ?>" required>
                    </div>

                    <div class="col-md-4 m-l">
                    <label for="devf_b24x_imnuno" class="form-label">PRUEBA RÁPIDA (INMUNOCROMATOGRAFÍA)</label>
                    <select name="devf_b24x_imnuno" id="devf_b24x_imnuno" class="form-select" required>
                        <option value="">Seleccionar...</option>
                        <option value="NEGATIVO"<?php if('NEGATIVO' == $rowcita['devf_b24x_imnuno']) { echo 'selected'; } ?>>NEGATIVO</option>
                        <option value="POSITIVA"<?php if('POSITIVA' == $rowcita['devf_b24x_imnuno']) { echo 'selected'; } ?>>POSITIVA</option> 
                    </select>
                    </div>

                    <div class="col-md-2 m-l">
                    <label for="devf_b24x_fecha_enzimo" class="form-label" value="<?php echo $rowcita['devf_b24x_fecha_enzimo']; ?>">FECHA :</label>
                    <input type="date" name="devf_b24x_fecha_enzimo" id="devf_b24x_fecha_enzimo" class="form-control" value="<?php echo $rowcita['devf_b24x_fecha_enzimo']; ?>" required>
                    </div>

                    <div class="col-md-4 m-l">
                    <label for="devf_b24x_enzimo" class="form-label">ENZIMOINMUNOENSAYO (ELISA)</label>
                    <select name="devf_b24x_enzimo" id="devf_b24x_enzimo" class="form-select" required>
                        <option value="">Seleccionar...</option>
                        <option value="NEGATIVO"<?php if('NEGATIVO' == $rowcita['devf_b24x_enzimo']) { echo 'selected'; } ?>>NEGATIVO</option>
                        <option value="POSITIVA"<?php if('POSITIVA' == $rowcita['devf_b24x_enzimo']) { echo 'selected'; } ?>>POSITIVA</option> 
                    </select>
                    </div>

                </div>	
                </div> 
                    <center><left> <h3 class="modal-title fs-5" id="editaModalLabel">A539</h3></left></center>

                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-start text-start ">  

                    <div class="col-md-2 m-l">
                    <label for="devf_a539_fecha_inmuno" class="form-label" value="<?php echo $rowcita['devf_a539_fecha_inmuno']; ?>">FECHA :</label>
                    <input type="date" name="devf_a539_fecha_inmuno" id="devf_a539_fecha_inmuno" class="form-control" value="<?php echo $rowcita['devf_a539_fecha_inmuno']; ?>" required>
                    </div>

                    <div class="col-md-4 m-l">
                    <label for="devf_a539_imnuno" class="form-label">PRUEBA RÁPIDA</label>
                    <select name="devf_a539_imnuno" id="devf_a539_imnuno" class="form-select" required>
                        <option value="">Seleccionar...</option>
                        <option value="NEGATIVO"<?php if('NEGATIVO' == $rowcita['devf_a539_imnuno']) { echo 'selected'; } ?>>NEGATIVO</option>
                        <option value="POSITIVA"<?php if('POSITIVA' == $rowcita['devf_a539_imnuno']) { echo 'selected'; } ?>>POSITIVA</option> 
                    </select>
                    </div>

                    <div class="col-md-2 m-l">
                    <label for="devf_a539_fecha_enzimo" class="form-label" value="<?php echo $rowcita['devf_a539_fecha_enzimo']; ?>">FECHA :</label>
                    <input type="date" name="devf_a539_fecha_enzimo" id="devf_a539_fecha_enzimo" class="form-control" value="<?php echo $rowcita['devf_a539_fecha_enzimo']; ?>" required>
                    </div>

                    <div class="col-md-4 m-l">
                    <label for="devf_a539_enzimo" class="form-label">LABORTATORIO:</label>
                    <select name="devf_a539_enzimo" id="devf_a539_enzimo" class="form-select" required>
                        <option value="">Seleccionar...</option>
                        <option value="NEGATIVO"<?php if('NEGATIVO' == $rowcita['devf_a539_enzimo']) { echo 'selected'; } ?>>NEGATIVO</option>
                        <option value="POSITIVA"<?php if('POSITIVA' == $rowcita['devf_a539_enzimo']) { echo 'selected'; } ?>>POSITIVA</option> 
                    </select>
                    </div>

                </div>	
                </div>

                    <center> <left> <h2 class="modal-title fs-5" id="editaModalLabel">ITS 2a DETECCIÓN</h2></left></center>    
                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-start text-start ">  
                    <center>
                        <div class="col-md-3 m-l">
                        <label for="de_depresion_prenatal" class="form-label">DEPRESIÓN PRENATAL</label>
                        <select name="de_depresion_prenatal" id="de_depresion_prenatal" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowcita['de_depresion_prenatal']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowcita['de_depresion_prenatal']) { echo 'selected'; } ?>>NO</option> 
                        </select>
                        </div>     
                    </center>
                    <center> <left> <h3 class="modal-title fs-5" id="editaModalLabel">B24X</h3></left></center>
                </div>	
                </div> 

                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-start text-start ">  

                    <div class="col-md-2 m-l">
                        <label for="dedp_b24x_fecha_rapida" class="form-label" value="<?php echo $rowcita['dedp_b24x_fecha_rapida']; ?>">FECHA :</label>
                        <input type="date" name="dedp_b24x_fecha_rapida" id="dedp_b24x_fecha_rapida" class="form-control" value="<?php echo $rowcita['dedp_b24x_fecha_rapida']; ?>" required>
                    </div>

                    <div class="col-md-4 m-l">
                    <label for="dedp_b24x_rapida" class="form-label">PRUEBA RÁPIDA (INMUNOCROMATOGRAFÍA)</label>
                    <select name="dedp_b24x_rapida" id="dedp_b24x_rapida" class="form-select" required>
                        <option value="">Seleccionar...</option>
                        <option value="NEGATIVO"<?php if('NEGATIVO' == $rowcita['dedp_b24x_rapida']) { echo 'selected'; } ?>>NEGATIVO</option>
                        <option value="POSITIVA"<?php if('POSITIVA' == $rowcita['dedp_b24x_rapida']) { echo 'selected'; } ?>>POSITIVA</option> 
                    </select>
                    </div>

                    <div class="col-md-2 m-l">
                        <label for="dedp_b24x_fecha_lab" class="form-label" value="<?php echo $rowcita['dedp_b24x_fecha_lab']; ?>">FECHA :</label>
                        <input type="date" name="dedp_b24x_fecha_lab" id="dedp_b24x_fecha_lab" class="form-control" value="<?php echo $rowcita['dedp_b24x_fecha_lab']; ?>" required>
                    </div>

                    <div class="col-md-4 m-l">
                        <label for="dedp_b24x_lab" class="form-label">ENZIMOINMUNOENSAYO (ELISA)</label>
                        <select name="dedp_b24x_lab" id="dedp_b24x_lab" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <option value="NEGATIVO"<?php if('NEGATIVO' == $rowcita['dedp_b24x_lab']) { echo 'selected'; } ?>>NEGATIVO</option>
                            <option value="POSITIVA"<?php if('POSITIVA' == $rowcita['dedp_b24x_lab']) { echo 'selected'; } ?>>POSITIVA</option> 
                        </select>
                    </div>

                </div>	
                </div> 
                     <center><left> <h3 class="modal-title fs-5" id="editaModalLabel">A539</h3></left></center>
        
                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-start text-start ">  

                    <div class="col-md-2 m-l">
                        <label for="dedp_a539_fecha_rapidaa" class="form-label" value="<?php echo $rowcita['dedp_a539_fecha_rapidaa']; ?>">FECHA :</label>
                        <input type="date" name="dedp_a539_fecha_rapidaa" id="dedp_a539_fecha_rapidaa" class="form-control" value="<?php echo $rowcita['dedp_a539_fecha_rapidaa']; ?>" required>
                    </div>

                    <div class="col-md-4 m-l">
                    <label for="dedp_a539_rapida" class="form-label">PRUEBA RÁPIDA</label>
                    <select name="dedp_a539_rapida" id="dedp_a539_rapida" class="form-select" required>
                        <option value="">Seleccionar...</option>
                        <option value="NEGATIVO"<?php if('NEGATIVO' == $rowcita['dedp_a539_rapida']) { echo 'selected'; } ?>>NEGATIVO</option>
                        <option value="POSITIVA"<?php if('POSITIVA' == $rowcita['dedp_a539_rapida']) { echo 'selected'; } ?>>POSITIVA</option> 
                    </select>
                    </div>

                    <div class="col-md-2 m-l">
                        <label for="dedp_a539_fecha_lab" class="form-label" value="<?php echo $rowcita['dedp_a539_fecha_lab']; ?>">FECHA :</label>
                        <input type="date" name="dedp_a539_fecha_lab" id="dedp_a539_fecha_lab" class="form-control" value="<?php echo $rowcita['dedp_a539_fecha_lab']; ?>" required>
                    </div>

                    <div class="col-md-4 m-l">
                        <label for="dedp_a539_lab" class="form-label">LABORTATORIO:</label>
                        <select name="dedp_a539_lab" id="dedp_a539_lab" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <option value="NEGATIVO"<?php if('NEGATIVO' == $rowcita['dedp_a539_lab']) { echo 'selected'; } ?>>NEGATIVO</option>
                            <option value="POSITIVA"<?php if('POSITIVA' == $rowcita['dedp_a539_lab']) { echo 'selected'; } ?>>POSITIVA</option> 
                        </select>
                    </div>

                </div> 
                </div>           
            </div> 
        <!----FIN---->    

        <!----INICIO---->
            <div class="tab-pane active" id="fill-tabpanel-5" role="tabpanel" aria-labelledby="fill-tab-5">

                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-start text-start "> 
                    
                    <div class="col-md-4 m-l">
                    <label for="ag_atencio_preges" class="form-label">¿TUVO ATENCIÓN PREGESTACIONAL?</label>
                    <select name="ag_atencio_preges" id="ag_atencio_preges" class="form-select" required>
                        <option value="">Seleccionar...</option>
                        <option value="SI"<?php if('SI' == $rowcita['ag_atencio_preges']) { echo 'selected'; } ?>>SI</option>
                        <option value="NO"<?php if('NO' == $rowcita['ag_atencio_preges']) { echo 'selected'; } ?>>NO</option> 
                    </select>
                    </div> 

                    <div class="col-md-4 m-l">
                    <label for="ag_riesgos" class="form-label">RIESGOS:</label>
                    <select name="ag_riesgos" id="ag_riesgos" class="form-select" required>
                        <option value="">Seleccionar...</option>
                        <option value="1"<?php if('1' == $rowcita['ag_riesgos']) { echo 'selected'; } ?>>1.PATOLOGÍA CRÓNICA ÓRGANO FUNCIONAL</option>
                        <option value="2"<?php if('2' == $rowcita['ag_riesgos']) { echo 'selected'; } ?>>2.PATOLOGÍA CRÓNICA INFECCIOSA</option> 
                        <option value="3"<?php if('3' == $rowcita['ag_riesgos']) { echo 'selected'; } ?>>3.PATOLOGÍA MORBILIDAD MATERNA EXTREMA</option>
                        <option value="4"<?php if('4' == $rowcita['ag_riesgos']) { echo 'selected'; } ?>>4.CON FACTORES DE RIESGO SOCIALES</option> 
                        <option value="5"<?php if('5' == $rowcita['ag_riesgos']) { echo 'selected'; } ?>>5.ANTECEDENTES OBSTÉTRICOS DE RIESGO</option>
                        <option value="0"<?php if('0' == $rowcita['ag_riesgos']) { echo 'selected'; } ?>>0.NINGUNA</option> 
                    </select>
                    </div> 

                    <div class="col-md-1 m-l">
                        <label for="ag_gestas" class="form-label">GESTAS:</label>
                        <input type="number" name="ag_gestas" id="ag_gestas" class="form-control"value="<?php echo $fila['ag_gestas']; ?>" >                        
                    </div>

                    <div class="col-md-1 m-l">
                        <label for="ag_partos" class="form-label">PARTOS:</label>
                        <input type="number" name="ag_partos" id="ag_partos" class="form-control"value="<?php echo $fila['ag_partos']; ?>" >                        
                    </div>

                    <div class="col-md-1 m-l">
                        <label for="ag_cesarias" class="form-label">CESÁREAS:</label>
                        <input type="number" name="ag_cesarias" id="ag_cesarias" class="form-control"value="<?php echo $fila['ag_cesarias']; ?>" >                        
                    </div>

                </div>
                </div>

                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-start text-start "> 

                    <div class="col-md-1 m-l">
                        <label for="ag_abortos" class="form-label">ABORTOS:</label>
                        <input type="number" name="ag_abortos" id="ag_abortos" class="form-control"value="<?php echo $fila['ag_abortos']; ?>" >                        
                    </div>

                    <div class="col-md-1 m-l">
                        <label for="ag_ectopico" class="form-label">ECTÓPICO:</label>
                        <input type="number" name="ag_ectopico" id="ag_ectopico" class="form-control"value="<?php echo $fila['ag_ectopico']; ?>" >                        
                    </div>

                    <div class="col-md-1 m-l">
                        <label for="ag_mola" class="form-label">MOLA:</label>
                        <input type="number" name="ag_mola" id="ag_mola" class="form-control"value="<?php echo $fila['ag_mola']; ?>" >                        
                    </div>

                    <div class="col-md-4 m-l">
                        <label for="ag_emb_mul" class="form-label">EMBARAZOS MÚLTIPLES:</label>
                        <input type="number" name="ag_emb_mul" id="ag_emb_mul" class="form-control"value="<?php echo $fila['ag_emb_mul']; ?>" >                        
                    </div>

                    <div class="col-md-3 m-l">
                        <label for="ag_hijos_nac_viv" class="form-label">HIJOS NACIDOS VIVOS:</label>
                        <input type="number" name="ag_hijos_nac_viv" id="ag_hijos_nac_viv" class="form-control"value="<?php echo $fila['ag_hijos_nac_viv']; ?>" >                        
                    </div>

                    <div class="col-md-3 m-l">
                        <label for="ag_ag_hijos_nac_mue" class="form-label">HIJOS NACIDOS MUERTOS:</label>
                        <input type="number" name="ag_ag_hijos_nac_mue" id="ag_ag_hijos_nac_mue" class="form-control"value="<?php echo $fila['ag_ag_hijos_nac_mue']; ?>" >                        
                    </div>

                </div>
                </div>

                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-start text-start "> 

                    <div class="col-md-4 m-l">
                        <label for="ag_resol_ult_emba" class="form-label">RESOLUCIÓN DEL ÚLTIMO EMBARAZO:</label>
                        <select name="ag_resol_ult_emba" id="ag_resol_ult_emba" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <option value="1"<?php if('1' == $rowcita['ag_resol_ult_emba']) { echo 'selected'; } ?>>1.ABORTO</option>
                            <option value="2"<?php if('2' == $rowcita['ag_resol_ult_emba']) { echo 'selected'; } ?>>2.CESÁREA</option> 
                            <option value="3"<?php if('3' == $rowcita['ag_resol_ult_emba']) { echo 'selected'; } ?>>3.MOLA</option>
                            <option value="4"<?php if('4' == $rowcita['ag_resol_ult_emba']) { echo 'selected'; } ?>>4.PARTO EUTÓCICO O DISTÓCICO</option> 
                        </select>
                    </div> 

                    <div class="col-md-4 m-l">
                        <label for="ag_resol_fecha_ul_emb" class="form-label" value="<?php echo $rowcita['ag_resol_fecha_ul_emb']; ?>">FECHA RESOLUCIÓN DEL ÚLTIMO EMBARAZO:</label>
                        <input type="date" name="ag_resol_fecha_ul_emb" id="ag_resol_fecha_ul_emb" class="form-control" value="<?php echo $rowcita['ag_resol_fecha_ul_emb']; ?>" required>
                    </div>

                    <div class="col-md-4 m-l">
                        <label for="ag_uso_prev_antico_tipo" class="form-label">USO PREVIO DE ANTICONCEPTIVOS:</label>
                        <select name="ag_uso_prev_antico_tipo" id="ag_uso_prev_antico_tipo" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowcita['ag_uso_prev_antico_tipo']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowcita['ag_uso_prev_antico_tipo']) { echo 'selected'; } ?>>NO</option> 
                        </select>
                    </div>  

                </div>
                </div>  

                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-start text-start ">     
                    
                    <div class="col-md-4 m-l">
                        <label for="ag_resol_ult_emba" class="form-label">TIPO:</label>
                        <select name="ag_resol_ult_emba" id="ag_resol_ult_emba" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <option value="0"<?php if('0' == $rowcita['ag_resol_ult_emba']) { echo 'selected'; } ?>>0.NINGUNO</option>
                            <option value="1"<?php if('1' == $rowcita['ag_resol_ult_emba']) { echo 'selected'; } ?>>1.HORMONAL</option>
                            <option value="2"<?php if('2' == $rowcita['ag_resol_ult_emba']) { echo 'selected'; } ?>>2.DIU</option> 
                            <option value="3"<?php if('3' == $rowcita['ag_resol_ult_emba']) { echo 'selected'; } ?>>3.PRESERVATIVO</option>
                            <option value="4"<?php if('4' == $rowcita['ag_resol_ult_emba']) { echo 'selected'; } ?>>4.OTRO MÉTODO</option>
                        </select>
                    </div> 

                    <div class="col-md-4 m-l">
                        <label for="ag_uso_prev_antico_tiempo_uso" class="form-label">TIEMPO DE USO:</label>
                        <input  type="text" name="ag_uso_prev_antico_tiempo_uso" id="ag_uso_prev_antico_tiempo_uso"  class="form-control"  value="<?php echo $rowcita['ag_uso_prev_antico_tiempo_uso']; ?>" required>                        
                    </div>

                    <div class="col-md-4 m-l">
                        <label for="ag_uso_prev_fecha_suspe" class="form-label" value="<?php echo $rowcita['ag_uso_prev_fecha_suspe']; ?>">FECHA DE SUSPENSIÓN:</label>
                        <input type="date" name="ag_uso_prev_fecha_suspe" id="ag_uso_prev_fecha_suspe" class="form-control" value="<?php echo $rowcita['ag_uso_prev_fecha_suspe']; ?>" required>
                    </div>
                
                </div>
                </div>  
                
                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-start text-start ">      

                    <div class="col-md-4 m-l">
                    <label for="ag_uso_prev_falla" class="form-label">FALLA DEL MÉTODO ANTICONCEPTIVO:</label>
                    <select name="ag_uso_prev_falla" id="ag_uso_prev_falla" class="form-select" required>
                        <option value="">Seleccionar...</option>
                        <option value="SI"<?php if('SI' == $rowcita['ag_uso_prev_falla']) { echo 'selected'; } ?>>SI</option>
                        <option value="NO"<?php if('NO' == $rowcita['ag_uso_prev_falla']) { echo 'selected'; } ?>>NO</option> 
                    </select>
                    </div>   

                    <div class="col-md-4 m-l">
                    <label for="ag_otro_ante_ginecoobs" class="form-label">OTRO ANTECEDENTE GINECOOBSTÉTRICO:</label>
                    <select name="ag_otro_ante_ginecoobs" id="ag_otro_ante_ginecoobs" class="form-select" required>
                        <option value="">Seleccionar...</option>
                        <option value="SI"<?php if('SI' == $rowcita['ag_otro_ante_ginecoobs']) { echo 'selected'; } ?>>SI</option>
                        <option value="NO"<?php if('NO' == $rowcita['ag_otro_ante_ginecoobs']) { echo 'selected'; } ?>>NO</option> 
                    </select>
                    </div>  

                    <div class="col-md-4 m-l">
                        <label for="ag_otro_ante_ginecoobs_esp" class="form-label">ESPECIFIQUE:</label>
                        <select name="ag_otro_ante_ginecoobs_esp" id="ag_otro_ante_ginecoobs_esp" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <option value="1"<?php if('1' == $rowcita['ag_otro_ante_ginecoobs_esp']) { echo 'selected'; } ?>>1.CIRUGÍA UTERINA PREVIA O MIOMATOSIS</option>
                            <option value="2"<?php if('2' == $rowcita['ag_otro_ante_ginecoobs_esp']) { echo 'selected'; } ?>>2.TRANSFUSIÓN SANGUÍNEA EN EVENTO OBSTÉTRICO</option> 
                            <option value="3"<?php if('3' == $rowcita['ag_otro_ante_ginecoobs_esp']) { echo 'selected'; } ?>>3.POLIHIDRAMNIOS</option>
                            <option value="4"<?php if('4' == $rowcita['ag_otro_ante_ginecoobs_esp']) { echo 'selected'; } ?>>4.PARTO PRETÉRMINO</option> 
                            <option value="5"<?php if('5' == $rowcita['ag_otro_ante_ginecoobs_esp']) { echo 'selected'; } ?>>5.PRODUCTO MACROSÓMICO</option>
                            <option value="6"<?php if('6' == $rowcita['ag_otro_ante_ginecoobs_esp']) { echo 'selected'; } ?>>6.PRODUCTO BAJO PESO</option> 
                            <option value="7"<?php if('7' == $rowcita['ag_otro_ante_ginecoobs_esp']) { echo 'selected'; } ?>>7.INCOMPETENCIA ÍSTMICO-CERVICAL</option>                
                        </select>
                    </div>  

                </div>
                </div>
        
            </div> 
        <!----FIN---->              

        <!----INICIO---->
            <div class="tab-pane active" id="fill-tabpanel-6" role="tabpanel" aria-labelledby="fill-tab-6">

                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-start text-start ">

                    <div class="col-md-4 m-l">
                        <label for="bio_fecha_td_primera" class="form-label" value="<?php echo $rowcita['bio_fecha_td_primera']; ?>">TD PRIMERA:</label>
                        <input type="date" name="bio_fecha_td_primera" id="bio_fecha_td_primera" class="form-control" value="<?php echo $rowcita['bio_fecha_td_primera']; ?>" required>
                    </div>

                    <div class="col-md-4 m-l">
                        <label for="bio_fecha_td_segunda" class="form-label" value="<?php echo $rowcita['bio_fecha_td_segunda']; ?>">TD SEGUNDA:</label>
                        <input type="date" name="bio_fecha_td_segunda" id="bio_fecha_td_segunda" class="form-control" value="<?php echo $rowcita['bio_fecha_td_segunda']; ?>" required>
                    </div>

                    <div class="col-md-4 m-l">
                        <label for="bio_fecha_td_tercera" class="form-label" value="<?php echo $rowcita['bio_fecha_td_tercera']; ?>">TD TERCERA:</label>
                        <input type="date" name="bio_fecha_td_tercera" id="bio_fecha_td_tercera" class="form-control" value="<?php echo $rowcita['bio_fecha_td_tercera']; ?>" required>
                    </div>

                </div>	
                </div> 

                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-start text-start ">

                    <div class="col-md-4 m-l">
                        <label for="bio_fecha_tdpa" class="form-label" value="<?php echo $rowcita['bio_fecha_tdpa']; ?>">Tdpa:</label>
                        <input type="date" name="bio_fecha_tdpa" id="bio_fecha_tdpa" class="form-control" value="<?php echo $rowcita['bio_fecha_tdpa']; ?>" required>
                    </div>

                    <div class="col-md-4 m-l">
                        <label for="bio_fecha_influenza" class="form-label" value="<?php echo $rowcita['bio_fecha_influenza']; ?>">INFLUENZA:</label>
                        <input type="date" name="bio_fecha_influenza" id="bio_fecha_influenza" class="form-control" value="<?php echo $rowcita['bio_fecha_influenza']; ?>" required>
                    </div>

                    <div class="col-md-4 m-l">
                        <label for="bio_fecha_covid19" class="form-label" value="<?php echo $rowcita['bio_fecha_covid19']; ?>">ANTICOVID-19:</label>
                        <input type="date" name="bio_fecha_covid19" id="bio_fecha_covid19" class="form-control" value="<?php echo $rowcita['bio_fecha_covid19']; ?>" required>
                    </div>
                    
                </div>	
                </div>  
                    
            </div>
        <!----FIN---->

        <!----INICIO---->
            <div class="tab-pane active" id="fill-tabpanel-7" role="tabpanel" aria-labelledby="fill-tab-7">

                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-start text-start ">

                    <div class="col-md-2 m-l">
                        <label for="adicciones" class="form-label">ADICCIONES:</label>
                        <select name="adicciones" id="adicciones" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowcita['adicciones']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowcita['adicciones']) { echo 'selected'; } ?>>NO</option>   
                        </select>
                    </div>	

                    <div class="col-md-2 m-l">
                        <label for="adic_tabaco" class="form-label">TABACO:</label>
                        <select name="adic_tabaco" id="adic_tabaco" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowcita['adic_tabaco']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowcita['adic_tabaco']) { echo 'selected'; } ?>>NO</option>   
                        </select>
                    </div>	

                    <div class="col-md-2 m-l">
                        <label for="adic_antidepre" class="form-label">ANTIDEPRESIVOS:</label>
                        <select name="adic_antidepre" id="adic_antidepre" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowcita['adic_antidepre']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowcita['adic_antidepre']) { echo 'selected'; } ?>>NO</option>   
                        </select>
                    </div>	

                    <div class="col-md-2 m-l">
                        <label for="adic_alcohol" class="form-label">ALCOHOL:</label>
                        <select name="adic_alcohol" id="adic_alcohol" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowcita['adic_alcohol']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowcita['adic_alcohol']) { echo 'selected'; } ?>>NO</option>   
                        </select>
                    </div>	

                    <div class="col-md-2 m-l">
                        <label for="adic_ansioliticos" class="form-label">ANSIOLÍTICOS:</label>
                        <select name="adic_ansioliticos" id="adic_ansioliticos" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowcita['adic_ansioliticos']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowcita['adic_ansioliticos']) { echo 'selected'; } ?>>NO</option>   
                        </select>
                    </div>	

                    <div class="col-md-2 m-l">
                        <label for="adic_otros" class="form-label">OTROS:</label>
                        <select name="adic_otros" id="adic_otros" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowcita['adic_otros']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowcita['adic_otros']) { echo 'selected'; } ?>>NO</option>   
                        </select>
                    </div>	                

                </div>	
                </div> 
            
                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-start text-start ">

                    <div class="col-md-2 m-l">
                        <label for="adic_consumo_act" class="form-label">¿CONSUMO ACTUAL?</label>
                        <select name="adic_consumo_act" id="adic_consumo_act" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowcita['adic_consumo_act']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowcita['adic_consumo_act']) { echo 'selected'; } ?>>NO</option>   
                        </select>
                    </div>
                
                    <div class="col-md-1 m-l">
                        <label for="adic_años" class="form-label">AÑOS:</label>
                        <input type="number" name="adic_años" id="adic_años" class="form-control"value="<?php echo $fila['adic_años']; ?>" >                        
                    </div>

                    <div class="col-md-1 m-l">
                        <label for="adic_meses" class="form-label">MESES:</label>
                        <input type="number" name="adic_meses" id="adic_meses" class="form-control"value="<?php echo $fila['adic_meses']; ?>" >                        
                    </div>

                </div>	
                </div>  

            </div>
        <!----FIN---->

        <!----INICIO---->
            <div class="tab-pane active" id="fill-tabpanel-8" role="tabpanel" aria-labelledby="fill-tab-8">

                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-start text-start ">
                        <center> <h5 class="modal-title fs-5" id="editaModalLabel">ATENCIÓN A:</h5></center>
                    <div class="col-md-3 m-l">
                        <label for="sbde_rev_odont" class="form-label">REVISIÓN ODONTOLÓGICA:</label>
                        <select name="sbde_rev_odont" id="sbde_rev_odont" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowcita['sbde_rev_odont']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowcita['sbde_rev_odont']) { echo 'selected'; } ?>>NO</option>   
                        </select>
                    </div>	

                    <div class="col-md-3 m-l">
                        <label for="sbde_atn_caries" class="form-label">CARIES:</label>
                        <select name="sbde_atn_caries" id="sbde_atn_caries" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowcita['sbde_atn_caries']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowcita['sbde_atn_caries']) { echo 'selected'; } ?>>NO</option>   
                        </select>
                    </div>	

                    <div class="col-md-3 m-l">
                        <label for="sbde_atn_periodonitis" class="form-label">PERIODONTITIS:</label>
                        <select name="sbde_atn_periodonitis" id="sbde_atn_periodonitis" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowcita['sbde_atn_periodonitis']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowcita['sbde_atn_periodonitis']) { echo 'selected'; } ?>>NO</option>   
                        </select>
                    </div>	

                    <div class="col-md-3 m-l">
                        <label for="sbde_atn_otro" class="form-label">OTRO:</label>
                        <select name="sbde_atn_otro" id="sbde_atn_otro" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowcita['sbde_atn_otro']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowcita['sbde_atn_otro']) { echo 'selected'; } ?>>NO</option>   
                        </select>
                    </div>	
                    
                </div>	
                </div> 

            </div>
        <!----FIN---->       
        <!----INICIO---->
            <div class="tab-pane active" id="fill-tabpanel-9" role="tabpanel" aria-labelledby="fill-tab-9">

                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-start text-start ">
                        <center>
                    <div class="col-md-3 m-l">
                        <label for="v_nuticional" class="form-label">VALORACIÓN NUTRICIONAL</label>
                        <select name="v_nuticional" id="v_nuticional" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowcita['v_nuticional']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowcita['v_nuticional']) { echo 'selected'; } ?>>NO</option>   
                        </select>
                    </div>	
                        </center>    
                </div>	
                </div> 

            </div>
        <!----FIN---->       

        <!----INICIO---->
            <div class="tab-pane active" id="fill-tabpanel-10" role="tabpanel" aria-labelledby="fill-tab-10">

                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-start text-start ">
                    <label>  </label>
                                       
                        <div class="col-md-3 m-l">
                            <label for="pc_lab_otr_b24x_fecha" class="form-label" value="<?php echo $rowcita['pc_lab_otr_b24x_fecha']; ?>">B24X - FECHA:</label>
                            <input type="date" name="pc_lab_otr_b24x_fecha" id="pc_lab_otr_b24x_fecha" class="form-control" value="<?php echo $rowcita['pc_lab_otr_b24x_fecha']; ?>" required>
                        </div>

                        <div class="col-md-3 m-l">
                            <label for="pc_lab_otr_b24x_detectable" class="form-label">B24X - CARGA VIRAL:</label>
                            <select name="pc_lab_otr_b24x_detectable" id="pc_lab_otr_b24x_detectable" class="form-select" required>
                                <option value="">Seleccionar...</option>
                                <option value="INDETECTABLE"<?php if('INDETECTABLE' == $rowcita['pc_lab_otr_b24x_detectable']) { echo 'selected'; } ?>>INDETECTABLE</option>
                                <option value="DETECTABLE"<?php if('DETECTABLE' == $rowcita['pc_lab_otr_b24x_detectable']) { echo 'selected'; } ?>>DETECTABLE</option> 
                            </select>
                        </div>


                
                        <div class="col-md-3 m-l">
                            <label for="pc_lab_otr_a539_fecha" class="form-label" value="<?php echo $rowcita['pc_lab_otr_a539_fecha']; ?>">539 - FECHA :</label>
                            <input type="date" name="pc_lab_otr_a539_fecha" id="pc_lab_otr_a539_fecha" class="form-control" value="<?php echo $rowcita['pc_lab_otr_a539_fecha']; ?>" required>
                        </div>

                        <div class="col-md-3 m-l">
                            <label for="pc_lab_otr_a539_posneg" class="form-label">539 - CARGA VIRAL:</label>
                            <select name="pc_lab_otr_a539_posneg" id="pc_lab_otr_a539_posneg" class="form-select" required>
                                <option value="">Seleccionar...</option>
                                <option value="INDETECTABLE"<?php if('INDETECTABLE' == $rowcita['pc_lab_otr_a539_posneg']) { echo 'selected'; } ?>>NEGATIVO</option>
                                <option value="DETECTABLE"<?php if('DETECTABLE' == $rowcita['pc_lab_otr_a539_posneg']) { echo 'selected'; } ?>>POSITIVO</option> 
                            </select>
                        </div>
                    
                </div>	
                </div> 


            </div>
        <!----FIN---->   

        <!----INICIO---->
            <div class="tab-pane active" id="fill-tabpanel-11" role="tabpanel" aria-labelledby="fill-tab-11">

                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-start text-start ">
                    
                            <center> <h5 class="modal-title fs-5" id="editaModalLabel">CURVA DE TOLERANCIA A LA GLUCOSA</h5></center>

                                    <div class="col-md-3 m-l">
                                        <label for="cdg_fecha" class="form-label" value="<?php echo $rowcita['cdg_fecha']; ?>">FECHA:</label>
                                        <input type="date" name="cdg_fecha" id="cdg_fecha" class="form-control" value="<?php echo $rowcita['cdg_fecha']; ?>" required>
                                    </div>

                                    <div class="col-md-3 m-l">
                                        <label for="cdg_ayuno" class="form-label">Ayuno: mg/dl:</label>
                                        <input  type="text" name="cdg_ayuno" id="cdg_ayuno"  class="form-control"  value="<?php echo $rowcita['cdg_ayuno']; ?>" required>                        
                                    </div>
                                    <div class="col-md-3 m-l">
                                        <label for="cdg_60" class="form-label">60: mg/dl:</label>
                                        <input  type="text" name="cdg_60" id="cdg_60"  class="form-control"  value="<?php echo $rowcita['cdg_60']; ?>" required>                        
                                    </div>
                                    <div class="col-md-3 m-l">
                                        <label for="cdg_120" class="form-label">120: mg/dl:</label>
                                        <input  type="text" name="cdg_120" id="cdg_120"  class="form-control"  value="<?php echo $rowcita['cdg_120']; ?>" required>                        
                                    </div>

                </div>	
                </div> 


                </div>
                <!----FIN---->   

            </div>

       <form id="editaprograma" name="editaprograma" action="actualizaProgramas.php" method="POST">

       <input type="hidden" name="id_programa" id="id_programa" class="form-control" value="<?php echo $fila['id_programa']; ?>" >  

        <div class="container-fluid p-2 text-center">
        <div class="row justify-content-center text-center ">
            <div cclass="col-md-3 m-l">
            <button type="submit" class="btn btn-primary" id="enviar" name="enviar" ><i class="fa-solid fa-floppy-disk"></i> Guardar</button>
            </div>
        </div>
        </div>	

    </form>

    <script src="assets/js/bootstrap.bundle.min.js"></script>

    <script>
      function inhabilitar(){
          alert ("Esta función está inhabilitada.\n\n SSA")
          return false
      }
      document.oncontextmenu = inhabilitar
    </script>
  </body>

</html>