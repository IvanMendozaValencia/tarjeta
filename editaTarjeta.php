<?php

session_start();

require 'funcs/conexion.php';
require 'funcs/funcs.php';


if(!isset($_SESSION["id_usuario"]))
{
    header("Location: index.php");
}

$idUsusario = $_SESSION['id_usuario'];
$sql = "SELECT usuarios.id, usuario, nombre, correo, clues_id, last_session, id_tipo, a_usuarios,  a_tarjeta, a_configuracion, a_consultas, a_estadisticas, a_agregar, a_modificar, a_eliminar, CLUES,  NOMBRE_DE_LA_INSTITUCION, ENTIDAD,  MUNICIPIO,  LOCALIDAD, CLAVE_DE_LA_JURISDICCION, JURISDICCION, NOMBRE_DE_TIPOLOGIA,NOMBRE_DE_LA_UNIDAD FROM usuarios inner join clues on clues.clues = usuarios.clues_id WHERE id  ='$idUsusario'";
$result = $mysqli->query($sql);
$row_usuario = $result->fetch_assoc();
$id_tipo_usuario=$row_usuario['id_tipo'];

$id = $mysqli->real_escape_string($_GET['id']);

$sqltarjeta = "SELECT id_tarjeta, curp, nombre, primer_apellido, segundo_apellido, entidad_nacimiento, fecha_nacimiento, edad, derechohabiencia, estado_conyugal, escolaridad, tipo, expediente, peso_ant_emb, peso_emb, talla, imc, afromexicana, migragrante, nacional, internacional, indigena, lengua_indigena, lengua_habla, habla_espanol, calle, numero_int, numero_ext, colonia, cp, localidad, telefono, ap_personal_comunitario, ap_fecha, ap_trimestre, ap_puerperio, ap_lactancia, ap_prueba_serologica_embarazo, ap_se_ignora, ap_ulima_mestruacion, ap_confiable, ap_fecha_ultrasonido_obstretico, ap_semana_gestacion_usg, ap_fecha_probable_parto, af_ninguno, af_tuberculosis, af_hipertencion, af_diabetes, af_ef_hiper_embarazo, af_otro, af_otro_especifique, app_ninguno, app_diabetes_geostacional, app_diabetes, app_hipertencio_arterial, app_preeclampsia_enclampsia, app_nefropatia, app_cardiopatia, app_hemorragia_obstetrica, app_padecimiento_oncologico, app_b24x, app_a539, app_enfermedad_trnas_vector, app_tuberculosis, app_sars_cov2, app_otro_antecedente, app_grupo_sanguinieo, app_rh, app_prueba_coombs, app_tratamiento, de_violencia_fam, devf_b24x_fecha_inmuno, devf_b24x_imnuno, devf_b24x_fecha_enzimo, devf_b24x_enzimo, devf_a539_fecha_inmuno, devf_a539_imnuno, devf_a539_fecha_enzimo, devf_a539_enzimo, de_depresion_prenatal, dedp_b24x_fecha_rapida, dedp_b24x_rapida, dedp_b24x_fecha_lab, dedp_b24x_lab, dedp_a539_fecha_rapidaa, dedp_a539_rapida, dedp_a539_fecha_lab, dedp_a539_lab, ag_atencio_preges, ag_riesgos, ag_gestas, ag_partos, ag_cesarias, ag_abortos, ag_ectopico, ag_mola, ag_emb_mul, ag_hijos_nac_viv, ag_ag_hijos_nac_mue, ag_resol_ult_emba, ag_resol_fecha_ul_emb, ag_uso_prev_antico_tipo, ag_uso_prev_antico_tiempo_uso, ag_uso_prev_fecha_suspe, ag_uso_prev_falla, ag_otro_ante_ginecoobs, ag_otro_ante_ginecoobs_esp, bio_fecha_td_primera, bio_fecha_td_segunda, bio_fecha_td_tercera, bio_fecha_tdpa, bio_fecha_influenza, bio_fecha_covid19, adicciones, adic_tabaco, adic_antidepre, adic_alcohol, adic_ansioliticos, adic_otros, adic_consumo_act, adic_años, adic_meses, sbde_rev_odont, sbde_atn_caries, sbde_atn_periodonitis, sbde_atn_otro, v_nuticional, pc_lab_otr_b24x_fecha, pc_lab_otr_b24x_detectable, pc_lab_otr_a539_fecha, pc_lab_otr_a539_posneg, cdg_fecha, cdg_ayuno, cdg_60, cdg_120, e_concepto AS lug_nac, tipo_personal, servicio, folio, nombre_pre_ser, curp_pre_ser, fecha_baja_emb, motivo_baja_emb  FROM datos_identificacion INNER JOIN entidad ON entidad.id_entidad = datos_identificacion.entidad_nacimiento  WHERE id_tarjeta = $id LIMIT 1";
$tarjeta = $mysqli->query($sqltarjeta);
$rowcita= $tarjeta->fetch_assoc();
$localidad = $rowcita['localidad']; 
$id_tarjeta = $rowcita['id_tarjeta']; 
///OBTENIENDO VALORES CVE
$tp_concepto  = $rowcita['tipo_personal'];
$s_concepto   = $rowcita['servicio'];
$e_concepto   = $rowcita['entidad_nacimiento'];
$der_concepto = $rowcita['derechohabiencia'];
$ec_concepto  = $rowcita['estado_conyugal'];
$esc_concepto = $rowcita['escolaridad'];
$len_concepto = $rowcita['lengua_habla'];
$pc_concepto  = $rowcita['ap_personal_comunitario'];
$tri_concepto  = $rowcita['ap_trimestre'];
$tv_concepto  = $rowcita['app_enfermedad_trnas_vector'];
$oa_concepto  = $rowcita['app_otro_antecedente'];
$gs_concepto  = $rowcita['app_grupo_sanguinieo'];
$r_concepto  = $rowcita['ag_riesgos'];
$rue_concepto  = $rowcita['ag_resol_ult_emba'];
$anti_concepto  = $rowcita['ag_uso_prev_antico_tipo'];
$agineco_concepto  = $rowcita['ag_otro_ante_ginecoobs_esp'];
$be_concepto  = $rowcita['motivo_baja_emb'];


//

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
//consulta de catalogos
$queryTP = "SELECT id_tipo_personal, tp_concepto FROM tipo_personal ORDER BY id_tipo_personal";
$resultadoTP = $mysqli->query($queryTP);

$queryServ = "SELECT id_servicio, s_concepto FROM servicio ORDER BY id_servicio";
$resultadoServ = $mysqli->query($queryServ);

$queryEnti = "SELECT id_entidad, e_concepto FROM entidad ORDER BY id_entidad";
$resultadoEnti = $mysqli->query($queryEnti);

$queryDer = "SELECT id_derechohabiencia, der_concepto FROM derechohabiencia ORDER BY id_derechohabiencia";
$resultadoDer = $mysqli->query($queryDer);

$queryEc = "SELECT id_edo_conyugal, ec_concepto FROM edo_conyugal ORDER BY id_edo_conyugal";
$resultadoEc = $mysqli->query($queryEc);

$queryEsc = "SELECT id_escolaridad, esc_concepto FROM escolaridad ORDER BY id_escolaridad";
$resultadoEsc = $mysqli->query($queryEsc);

$queryLenH = "SELECT id_lengua_habla, len_concepto FROM lengua_habla ORDER BY id_lengua_habla";
$resultadoLenH = $mysqli->query($queryLenH);

$queryPC = "SELECT id_personal_com, pc_concepto FROM personal_com ORDER BY id_personal_com";
$resultadoPC = $mysqli->query($queryPC);

$queryTri = "SELECT id_trimestre, tri_concepto FROM trimestre ORDER BY id_trimestre";
$resultadoTri = $mysqli->query($queryTri);

$queryTV = "SELECT id_trans_vector, tv_concepto FROM trans_vector ORDER BY id_trans_vector";
$resultadoTV = $mysqli->query($queryTV);

$queryOA = "SELECT id_o_antecedente, oa_concepto FROM o_antecedente ORDER BY id_o_antecedente";
$resultadoOA = $mysqli->query($queryOA);

$queryGS = "SELECT id_gpo_sanguineo, gs_concepto FROM gpo_sanguineo ORDER BY id_gpo_sanguineo";
$resultadoGS = $mysqli->query($queryGS);

$queryRi = "SELECT id_riesgos, r_concepto FROM riesgos ORDER BY id_riesgos";
$resultadoRi = $mysqli->query($queryRi);

$queryUemb = "SELECT id_res_ult_emb, rue_concepto FROM res_ult_emb ORDER BY id_res_ult_emb";
$resultadoUemb = $mysqli->query($queryUemb);

$queryAnti = "SELECT id_anticonceptivos, anti_concepto FROM anticonceptivos ORDER BY id_anticonceptivos";
$resultadoAnti = $mysqli->query($queryAnti);

$queryAgi = "SELECT id_ant_gineco_otro, agineco_concepto FROM ant_gineco_otro ORDER BY id_ant_gineco_otro";
$resultadoAgi = $mysqli->query($queryAgi);

$queryBE = "SELECT id_baja_embarazo, be_concepto FROM baja_embarazo ORDER BY id_baja_embarazo";
$resultadoBE = $mysqli->query($queryBE);

//
$sqlpaciente = "SELECT id_tarjeta, CONCAT_WS(' ', nombre, primer_apellido, segundo_apellido) AS nombre_paciente  FROM datos_identificacion
WHERE id_tarjeta = $id_tarjeta LIMIT 1";
$nombre_paciente = $mysqli->query($sqlpaciente);
$rowpaciente = $nombre_paciente->fetch_assoc();

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
        <script 
        src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    	<!--  meta tags -->
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
      
       <!-- <script src="js/edad.js"></script> -->

		
        <script language="javascript">
			$(document).ready(function(){
				$("#b_region").change(function () {

					$('#localidad').find('option').remove().end().append('<option value="whatever"></option>').val('whatever');
					
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
							$("#localidad").html(data);
						});            
					});
				})
			});
		</script>
		

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

        <style>
      /*   div {
        margin-bottom: 5px;
        position: relative;
        }
        input + span {
        padding-right: 10px;
        }
        input:invalid + span::after {
        position: absolute;
        content: "✖";
        padding-left: 5px;
        }
        input:valid + span::after {
        position: absolute;
        content: "✓";
        padding-left: 5px;
        }
         <span class="validity"></span>
        */
        {
        border-color: red;    
        }

        </style>
</head>

<body class="d-flex flex-column h-100">

    <div class="container py-3">

        <h4 class="text-center">Información-Tarjeta de Atención Integral del Embarazo, Puerperio y Período de Lactancia</h2>   
        
        <hr>
        <?php echo 'Usuari@: '.utf8_encode(utf8_decode($row_usuario['nombre'])); ?>
        <?php if (isset($_SESSION['msg']) && isset($_SESSION['color'])) { ?>
            <div class="alert alert-<?= $_SESSION['color']; ?>   alert-dismissible fade show" role="alert">
               <?= $_SESSION['msg']; ?>         
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php
            unset($_SESSION['color']);
            unset($_SESSION['msg']);
        } ?>

        <h6 class="text-center"><?php echo 'PACIENTE: '.utf8_encode(utf8_decode($rowpaciente['nombre_paciente']));?></h6>
        <center><div class="bg-secondary p-0 text-white bg-opacity-90 modal-title fs-5">ANVERSO</div></center>
        <br>
        <div class="row justify-content-end">

        
            <div class="col-auto">             
                <form action="editaTarjetaR.php" method="GET"> 
                     
                    <input type="hidden"   name='id_tarjeta' value=<?= $rowcita['id_tarjeta']; ?>> 

                    <?php if($row_usuario['a_agregar']==1):  ?>
                    <Button type ="submit" class="btn btn-outline-secondary"><i class="fa-solid fa-file-medical"></i> Reverso</Button>
                    <?php endif;?>

                    <?php if($row_usuario['a_agregar']==0):  ?>
                    <Button type ="submit" class="btn btn-outline-secondary disabled"><i class="fa-solid fa-file-medical"></i> Reverso</Button>
                    <?php endif;?> 
                </form> 
            </div>

             <div class="col-auto">             
                <form action="citaprenatal.php" method="GET"> 

                        <input type="hidden"   name='id_tarjeta' value=<?= $rowcita['id_tarjeta']; ?>> 
               
                       <?php if($row_usuario['a_agregar']==1):  ?>
                       <a href="#" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#editaModalBajaemb" data-bs-id="<?= $rowpaciente['id_tarjeta']; ?>"><i class="fa-solid fa-heart-circle-exclamation"></i> Baja de embarazo</a> </td>
                       <?php endif;?>
           
                       <?php if($row_usuario['a_agregar']==0):  ?>
                       <a href="#" class="btn btn-outline-danger disabled" data-bs-toggle="modal" data-bs-target="#editaModalBajaemb" data-bs-id="<?= $rowpaciente['id_tarjeta']; ?>" ><i class="fa-solid fa-heart-circle-exclamation"></i> Baja de embarazo</a> </td>
                       <?php endif;?>
                </form> 
            </div>

            <div class="col-auto">
                <form action="visitas.php" method="GET"> 

                    <input type="hidden"   name='id_tarjeta' value=<?= $rowcita['id_tarjeta']; ?>> 

                    <?php if($row_usuario['a_agregar']==1):  ?>
                    <Button type ="submit" class="btn btn-outline-success"><i class="fa-solid fa-user-nurse"></i> Lista de Visitas</Button>
                    <?php endif;?>

                    <?php if($row_usuario['a_agregar']==0):  ?>
                    <Button type ="submit" class="btn btn-outline-success disabled"><i class="fa-solid fa-user-nurse"></i> Lista de Visitas</Button>
                    <?php endif;?> 
                </form>     
            </div>
            <div class="col-auto">             
                <form action="citaprenatal.php" method="GET"> 

                    <input type="hidden"   name='id_tarjeta' value=<?= $rowcita['id_tarjeta']; ?>> 

                    <?php if($row_usuario['a_agregar']==1):  ?>
                    <Button type ="submit" class="btn btn-outline-warning"><i class="fa-solid fa-receipt"></i> Lista de Citas</Button>
                    <?php endif;?>

                    <?php if($row_usuario['a_agregar']==0):  ?>
                    <Button type ="submit" class="btn btn-outline-warning disabled"><i class="fa-solid fa-receipt"></i> Lista de Citas</Button>
                    <?php endif;?> 
                </form> 
            </div>
            <div class="col-auto">  
            <a href="../Salud/registrostarjeta.php" class="btn btn-dark"></i>Regresar</a>
            </div>
        </div>

        <br>
        
        <form id="editatarjeta" name="editatarjeta" action="actualizaEditatarjeta.php" method="POST">

        <input type="hidden" name="id_tarjeta" id="id_tarjeta" class="form-control" value="<?php echo $rowcita['id_tarjeta']; ?>" > 
        <input type="hidden" name="clues_id" id="clues_id" class="form-control" value="<?php echo $row_usuario['CLUES']; ?>" >    
        
        <!----INICIO---->        
        <center><div class="bg-black p-2 text-white bg-opacity-90 modal-title fs-5">IDENTIFICACIÓN DE LA UNIDAD</div></center>
                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-start text-start ">

                    <div class="col-md-2 m-l">
                        <label for="clues" class="form-label">CLUES:</label>
                        <input  type="text" name="clues" id="clues"  class="form-control" disabled value="<?php echo $row_usuario['CLUES']; ?>">                        
                    </div>
                    <div class="col-md-4 m-l">
                        <label for="NOMBRE_DE_LA_UNIDAD" class="form-label">NOMBRE DE LA UNIDAD:</label>
                        <input  type="text" name="NOMBRE_DE_LA_UNIDAD" id="NOMBRE_DE_LA_UNIDAD"  class="form-control" disabled value="<?php echo $row_usuario['NOMBRE_DE_LA_UNIDAD']; ?>">                        
                    </div>
                    <div class="col-md-6 m-l">
                        <label for="NOMBRE_DE_LA_INSTITUCION" class="form-label">NOMBRE DE LA UNIDAD:</label>
                        <input  type="text" name="NOMBRE_DE_LA_INSTITUCION" id="NOMBRE_DE_LA_INSTITUCION"  class="form-control" disabled value="<?php echo $row_usuario['NOMBRE_DE_LA_INSTITUCION']; ?>">                        
                    </div>

                </div>
                </div>

                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-start text-start ">

                    <div class="col-md-4 m-l">
                        <label for="LOCALIDAD" class="form-label">LOCALIDAD:</label>
                        <input  type="text" name="LOCALIDAD" id="LOCALIDAD"  class="form-control" disabled value="<?php echo $row_usuario['LOCALIDAD']; ?>">                        
                    </div>
                    <div class="col-md-3 m-l">
                        <label for="MUNICIPIO" class="form-label">MUNICIPIO:</label>
                        <input  type="text" name="MUNICIPIO" id="MUNICIPIO"  class="form-control" disabled value="<?php echo $row_usuario['MUNICIPIO']; ?>">                        
                    </div>
                    <div class="col-md-3 m-l">
                        <label for="JURISDICCION" class="form-label">JURISDICCION:</label>
                        <input  type="text" name="JURISDICCION" id="JURISDICCION"  class="form-control" disabled value="<?php echo $row_usuario['JURISDICCION']; ?>">                        
                    </div>

                    <div class="col-md-2 m-l">
                        <label for="ENTIDAD" class="form-label">ENTIDAD:</label>
                        <input  type="text" name="ENTIDAD" id="ENTIDAD"  class="form-control" disabled value="<?php echo $row_usuario['ENTIDAD']; ?>">                        
                    </div>

                </div>
                </div>
                <center><div class="bg-black p-2 text-white bg-opacity-90 modal-title fs-5">TIPO DE PERSONAL Y SERVICIO</div></center>
                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-center text-start ">    

                    <div class="col-md-4 m-l">
                    <label for="tipo_personal" class="form-label">TIPO PERSONAL</label><label class="form-label" style="color: red">*:</label>
                    <select name="tipo_personal" id="tipo_personal" class="form-select" required>
                        <option value="">Seleccionar...</option>
                        <?php while ($rowTP = $resultadoTP->fetch_assoc()) { ?>
                        <option value="<?php echo $rowTP["id_tipo_personal"]; ?>"<?php if($rowTP['id_tipo_personal']==$tp_concepto) { echo 'selected'; } ?>><?php echo $rowTP['tp_concepto']; ?></option>
                        <?php } ?>                
                    </select>
                    </div>	

                    <div class="col-md-4 m-l">
                    <label for="servicio" class="form-label">SERVICIO</label><label class="form-label" style="color: red">*:</label>
                    <select name="servicio" id="servicio" class="form-select" required>
                        <option value="">Seleccionar...</option>s_concepto
                        <?php while ($rowServ = $resultadoServ->fetch_assoc()) { ?>
                        <option value="<?php echo $rowServ["id_servicio"]; ?>"<?php if($rowServ['id_servicio']==$s_concepto) { echo 'selected'; } ?>><?php echo $rowServ['s_concepto']; ?></option>
                        <?php } ?>
                    </select> 
                    </div>	
                    
                    <div class="col-md-2 m-l">
                        <label for="folio" class="form-label">FOLIO</label><label class="form-label" style="color: red">*:</label>
                        <input  type="text" name="folio" id="folio"  class="form-control" required value="<?php echo $rowcita['folio']; ?>">                           
                    </div>
                   
                </div>
                </div>

                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-center text-start "> 

                    <div class="col-md-6 m-l">
                        <label for="nombre_pre_ser" class="form-label">NOMBRE PRESTADOR DE SERVICIOS</label><label class="form-label" style="color: red">*:</label>
                        <input  type="text" name="nombre_pre_ser" id="nombre_pre_ser"  class="form-control" required value="<?php echo $rowcita['nombre_pre_ser']; ?>">                                        
                    </div>
                    
                    <div class="col-md-3 m-l">
                        <label for="curp_pre_ser" class="form-label">CURP</label><label class="form-label" style="color: red">*:</label>
                        <input  type="text" name="curp_pre_ser" id="curp_pre_ser"  class="form-control" required value="<?php echo $rowcita['curp_pre_ser']; ?>">
                    </div>
                    

                </div>	
                </div> 
        <!----FIN---->
        <!----INICIO---->
        <center><div class="bg-info p-2 text-black bg-opacity-90 modal-title fs-5">DATOS DE IDENTIFICACIÓN DE LA EMBARAZADA</div>
        </center>
                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-start text-start ">
        
                    <div class="col-md-3 m-l">
                    <label for="curp" class="form-label" >CURP</label><label class="form-label" style="color: red">*:</label>
                    <input  type="text"  name="curp" id="curp" class="form-control"  required value="<?php echo $rowcita['curp']; ?>" >     
                    </div> 

                    <div class="col-md-3 m-l">
                    <label for="nombre" class="form-label">NOMBRE</label><label class="form-label" style="color: red">*:</label>
                    <input  type="text" name="nombre" id="nombre"  class="form-control" required value="<?php echo $rowcita['nombre']; ?>" >                        
                    </div>

                    <div class="col-md-3 m-l">
                    <label for="primer_apellido" class="form-label">PRIMER APELLIDO</label><label class="form-label" style="color: red">*:</label>
                    <input  type="text" name="primer_apellido" id="primer_apellido"  class="form-control" required value="<?php echo $rowcita['primer_apellido']; ?>" >                        
                    </div>
            
                    <div class="col-md-3 m-l">
                    <label for="segundo_apellido" class="form-label">SEGUNDO APELLIDO:</label>
                    <input  type="text" name="segundo_apellido" id="segundo_apellido"  class="form-control"  value="<?php echo $rowcita['segundo_apellido']; ?>" >                        
                    </div>

                </div>	
                </div>      

                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-start text-start ">

                    <div class="col-md-3 m-l">
                    <label for="entidad_nacimiento" class="form-label">ENTIDAD DE NACIMIENTO</label><label class="form-label" style="color: red">*:</label>
                    <select name="entidad_nacimiento" id="entidad_nacimiento" class="form-select" required>
                        <option value="">Seleccionar...</option>
                        <?php while ($rowEnti = $resultadoEnti->fetch_assoc()) { ?>
                        <option value="<?php echo $rowEnti["id_entidad"]; ?>"<?php if($rowEnti['id_entidad']==$e_concepto) { echo 'selected'; } ?>><?php echo $rowEnti['e_concepto']; ?></option>
                        <?php } ?>
                    </select>
                    </div>

                    <div class="col-md-2 m-l">
                    <label for="fecha_nacimiento" class="form-label" value="<?php echo $rowcita['fecha_nacimiento']; ?>">FECHA NACIMIENTO</label><label class="form-label" style="color: red">*:</label>
                    <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" class="form-control" required value="<?php echo $rowcita['fecha_nacimiento']; ?>" >
                    </div>	

                    <div class="col-md-1 m-l">
                    <label for="edad" class="form-label">EDAD</label><label class="form-label" style="color: red">*:</label>
                    <input  type="text"  style="text-align:right" name="edad" id="edad"  class="form-control" required value="<?php echo $rowcita['edad']; ?>" >                        
                    </div>
                    
                    <div class="col-md-3 m-l">
                    <label for="derechohabiencia" class="form-label">DERECHOHABIENCIA</label><label class="form-label" style="color: red">*:</label>
                    <select name="derechohabiencia" id="derechohabiencia" class="form-select" required >
                        <option value="">Seleccionar...</option>
                        <?php while ($rowDer = $resultadoDer->fetch_assoc()) { ?>
                        <option value="<?php echo $rowDer["id_derechohabiencia"]; ?>"<?php if($rowDer['id_derechohabiencia']==$der_concepto) { echo 'selected'; } ?>><?php echo $rowDer['der_concepto']; ?></option>
                        <?php } ?>       
                    </select>
                    </div>
           
                    <div class="col-md-3 m-l">
                    <label for="estado_conyugal" class="form-label">ESTADO CONYUGAL</label><label class="form-label" style="color: red">*:</label>
                    <select name="estado_conyugal" id="estado_conyugal" class="form-select" required >
                        <option value="">Seleccionar...</option>
                        <?php while ($rowEc = $resultadoEc->fetch_assoc()) { ?>
                        <option value="<?php echo $rowEc["id_edo_conyugal"]; ?>"<?php if($rowEc['id_edo_conyugal']==$ec_concepto) { echo 'selected'; } ?>><?php echo $rowEc['ec_concepto']; ?></option>
                        <?php } ?>    
                    </select>
                    </div>	

                </div>	
                </div>

                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-start text-start ">

                    <div class="col-md-3 m-l">
                    <label for="escolaridad" class="form-label">ESCOLARIDAD</label><label class="form-label" style="color: red">*:</label>
                    <select name="escolaridad" id="escolaridad" class="form-select" required >
                        <option value="">Seleccionar...</option>
                        <?php while ($rowEsc = $resultadoEsc->fetch_assoc()) { ?>
                        <option value="<?php echo $rowEsc["id_escolaridad"]; ?>"<?php if($rowEsc['id_escolaridad']==$esc_concepto) { echo 'selected'; } ?>><?php echo $rowEsc['esc_concepto']; ?></option>
                        <?php } ?>
                    </select>
                    </div>	
                    
                    <div class="col-md-3 m-l">
                    <label for="tipo" class="form-label">TIPO</label><label class="form-label" style="color: red">*:</label>
                    <select name="tipo" id="tipo" class="form-select" required >
                        <option value="">Seleccionar...</option>
                        <option value="0"<?php if('0' == $rowcita['tipo']) { echo 'selected'; } ?>>0.NO APLICA</option>
                        <option value="1"<?php if('1' == $rowcita['tipo']) { echo 'selected'; } ?>>1.COMPLETO</option>
                        <option value="2"<?php if('2' == $rowcita['tipo']) { echo 'selected'; } ?>>2.INCOMPLETO</option>   
                    </select>
                    </div>	

                    <div class="col-md-2 m-l">
                    <label for="expediente" class="form-label">EXPEDIENTE:</label>
                    <input  type="text" name="expediente" id="expediente"  class="form-control"  value="<?php echo $rowcita['expediente']; ?>" >                        
                    </div>

                    <div class="col-md-2 m-l">
                    <label for="peso_ant_emb" class="form-label">PESO ANT. AL EMB.</label><label class="form-label" style="color: red">*:</label>
                    <input  type="text" style="text-align:right" placeholder="Ej.(51.50) ó (999)" name="peso_ant_emb" id="peso_ant_emb"  class="form-control" required value="<?php echo $rowcita['peso_ant_emb']; ?>" >                        
                    </div>

                    <div class="col-md-2 m-l">
                    <label for="peso_emb" class="form-label">PESO ACTUAL</label><label class="form-label" style="color: red">*:</label>
                    <input  type="text" style="text-align:right" placeholder="Ej.(51.50)"  name="peso_emb" id="peso_emb"  class="form-control" required value="<?php echo $rowcita['peso_emb']; ?>" >                        
                    </div>



                </div>	
                </div>

                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-start text-start ">
                    <div class="col-md-2 m-l">
                    <label for="talla" class="form-label">TALLA</label><label class="form-label" style="color: red">*:</label>
                    <input  type="text" style="text-align:right" placeholder="Ej.(1.50)"  name="talla" id="talla"  class="form-control" required value="<?php echo $rowcita['talla']; ?>" >                        
                    </div>  

                    <div class="col-md-1 m-l">
                    <label for="imc" class="form-label">IMC:</label>
                    <input  type="text" style="text-align:right" name="imc" id="imc"  class="form-control" value="<?php echo $rowcita['imc']; ?>" >                        
                    </div>

                    <div class="col-md-3 m-l">
                    <label for="afromexicana" class="form-label">¿SE CONSIDERA AFROMEXICANA?:</label>
                    <select name="afromexicana" id="afromexicana" class="form-select" >
                        <option value="">Seleccionar...</option>
                        <option value="SI"<?php if('SI' == $rowcita['afromexicana']) { echo 'selected'; } ?>>SI</option>
                        <option value="NO"<?php if('NO' == $rowcita['afromexicana']) { echo 'selected'; } ?>>NO</option>   
                    </select>
                    </div>	

                    <div class="col-md-2 m-l">
                    <label for="migragrante" class="form-label">MIGRANTE</label><label class="form-label" style="color: red">*:</label>
                    <select name="migragrante" id="migragrante" class="form-select" required>
                        <option value="">Seleccionar...</option>
                        <option value="SI"<?php if('SI' == $rowcita['migragrante']) { echo 'selected'; } ?>>SI</option>
                        <option value="NO"<?php if('NO' == $rowcita['migragrante']) { echo 'selected'; } ?>>NO</option>   
                    </select>
                    </div>	
                    
                    <div class="col-md-2 m-l">
                    <label for="nacional" class="form-label">NACIONAL</label><label class="form-label" style="color: red">*:</label>
                    <select name="nacional" id="nacional" class="form-select" disabled required>
                        <option value="">Seleccionar...</option>
                        <option value="SI"<?php if('SI' == $rowcita['nacional']) { echo 'selected'; } ?>>SI</option>
                        <option value="NO"<?php if('NO' == $rowcita['nacional']) { echo 'selected'; } ?>>NO</option>   
                    </select>
                    </div>	

                    <div class="col-md-2 m-l">
                    <label for="internacional" class="form-label">INTERNACIONAL:</label>
                    <select name="internacional" id="internacional" class="form-select" disabled >
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
                    <label for="indigena" class="form-label">¿ES O SE CONSIDERA INDÍGENA?</label><label class="form-label" style="color: red">*:</label>
                    <select name="indigena" id="indigena" class="form-select" required >
                        <option value="">Seleccionar...</option>
                        <option value="SI"<?php if('SI' == $rowcita['indigena']) { echo 'selected'; } ?>>SI</option>
                        <option value="NO"<?php if('NO' == $rowcita['indigena']) { echo 'selected'; } ?>>NO</option>   
                    </select>
                    </div>	

                    <div class="col-md-3 m-l">
                    <label for="lengua_indigena" class="form-label">HABLA LENGUA INDÍGENA COMO LENGUA MATERNA<label class="form-label" style="color: red">*:</label></label>
                    <select name="lengua_indigena" id="lengua_indigena" class="form-select" required>
                        <option value="">Seleccionar...</option>
                        <option value="SI"<?php if('SI' == $rowcita['lengua_indigena']) { echo 'selected'; } ?>>SI</option>
                        <option value="NO"<?php if('NO' == $rowcita['lengua_indigena']) { echo 'selected'; } ?>>NO</option>   
                    </select>
                    </div>	

                    <div class="col-md-3 m-l">
                    <label for="lengua_habla" class="form-label">CUÁL LENGUA INDÍGENA HABLA</label><label class="form-label" style="color: red">*:</label>
                        <select id="lengua_habla" name="lengua_habla" class="form-select" >
                        <option value="">Seleccionar</option>
                        <?php while ($rowLen = $resultadoLenH->fetch_assoc()) { ?>
                        <option value="<?php echo $rowLen["id_lengua_habla"]; ?>"<?php if($rowLen['id_lengua_habla']==$len_concepto) { echo 'selected'; } ?>><?php echo $rowLen['len_concepto']; ?></option>
                        <?php } ?>
                        </select>                      
                    </div>

                    <div class="col-md-3 m-l">
                    <label for="habla_espanol" class="form-label">¿HABLA ESPAÑOL?</label><label class="form-label" style="color: red">*:</label>
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
                    <label for="calle" class="form-label">CALLE</label><label class="form-label" style="color: red">*:</label>
                    <input  type="text" name="calle" id="calle"  class="form-control" required  value="<?php echo $rowcita['calle']; ?>" >                        
                    </div>

                    <div class="col-md-2 m-l">
                    <label for="numero_int" class="form-label">NÚMERO INT.:</label>
                    <input  type="text"  name="numero_int" id="numero_int"  class="form-control"  value="<?php echo $rowcita['numero_int']; ?>" >                        
                    </div>

                    <div class="col-md-2 m-l">
                    <label for="numero_ext" class="form-label">NÚMERO EXT.</label><label class="form-label" style="color: red">*:</label>
                    <input  type="text" name="numero_ext" id="numero_ext"  class="form-control" required  value="<?php echo $rowcita['numero_ext']; ?>" >                        
                    </div>

                    <div class="col-md-3 m-l">
                    <label for="colonia" class="form-label">COLONIA</label><label class="form-label" style="color: red">*:</label>
                    <input  type="text" name="colonia" id="colonia"  class="form-control" required  value="<?php echo $rowcita['colonia']; ?>" >                        
                    </div>

                    <div class="col-md-2 m-l">
                    <label for="cp" class="form-label">C.P.:</label>
                    <input  type="text" name="cp" id="cp"  class="form-control"  value="<?php echo $rowcita['cp']; ?>" >                        
                    </div>
        
                </div>	
                </div>
        
                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-start text-start ">

                    <div class="col-md-3 m-l">
                    <label for="b_region" class="form-label">REGIÓN</label><label class="form-label" style="color: red">*:</label>
                    <select name="b_region" id="b_region" class="form-select" required>
                        <option value="">Selecciona la región</option>
                        <?php while($rowR = $resultadoR->fetch_assoc()) { ?>
                            <option value="<?php echo $rowR['cve_regiones']; ?>" <?php if($rowR['cve_regiones']==$region) { echo 'selected'; } ?>><?php echo $rowR['region']; ?></option>
                        <?php } ?>
                    </select>
                    </div>

                    <div class="col-md-3 m-l">   
                    <label for="b_municipio" class="form-label">MUNICIPIO</label><label class="form-label" style="color: red">*:</label>
                    <select name="b_municipio" id="b_municipio" class="form-select" required>
                        <?php while($rowM = $resultadoM->fetch_assoc()) { ?>
                            <option value="<?php echo $rowM['cve_municipios']; ?>" <?php if($rowM['cve_municipios']==$municipio) { echo 'selected'; } ?>><?php echo $rowM['municipio']; ?></option>
                        <?php } ?>
                    </select>
                    </div>

                    <div class="col-md-4 m-l">
                    <label for="localidad" class="form-label">LOCALIDAD</label><label class="form-label" style="color: red">*:</label>
                    <select name="localidad" id="localidad" class="form-select" required > 
                    <?php while($rowL = $resultadoL->fetch_assoc()) { ?>
                            <option value="<?php echo $rowL['cve_localidad']; ?>" <?php if($rowL['cve_localidad']==$localidad) { echo 'selected'; } ?>><?php echo $rowL['localidad']; ?></option>
                        <?php } ?>
                    </select>
                    </div>

                    <div class="col-md-2 m-l">
                    <label for="telefono" class="form-label">TELÉFONO:</label>
                    <input  type="text" name="telefono" id="telefono"  class="form-control"  value="<?php echo $rowcita['telefono']; ?>" >  
                    </div>       
                </div>
                </div>  
        <!----FIN---->    

        <!----INICIO---->
        <center> <div class="bg-secondary p-2 text-white bg-opacity-90 modal-title fs-5">INICIO DE LA ATENCIÓN PRENATAL</div></center>   
                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-start text-start ">
            
                    <div class="col-md-4 m-l">
                    <label for="ap_personal_comunitario" class="form-label">REFERIDA POR PERSONAL COMUNITARIO</label><label class="form-label" style="color: red">*:</label>
                    <select name="ap_personal_comunitario" id="ap_personal_comunitario" class="form-select" required>
                        <option value="">Seleccionar...</option>
                        <?php while ($rowPC = $resultadoPC->fetch_assoc()) { ?>
                        <option value="<?php echo $rowPC["id_personal_com"]; ?>"<?php if($rowPC['id_personal_com']==$pc_concepto) { echo 'selected'; } ?>><?php echo $rowPC['pc_concepto']; ?></option>
                        <?php } ?>
                    </select>
                    </div>	
    
                    <div class="col-md-2 m-l">
                    <label for="ap_fecha" class="form-label" value="<?php echo $rowcita['ap_fecha']; ?>">FECHA DE INGRESO</label><label class="form-label" style="color: red">*:</label>
                    <input type="date" name="ap_fecha" id="ap_fecha" class="form-control" value="<?php echo $rowcita['ap_fecha']; ?>" required>
                    </div>	
        
                    <div class="col-md-2 m-l">
                    <label for="ap_trimestre" class="form-label">TRIMESTRE</label><label class="form-label" style="color: red">*:</label>
                    <select name="ap_trimestre" id="ap_trimestre" class="form-select" required>
                        <option value="">Seleccionar...</option>
                        <?php while ($rowTri = $resultadoTri->fetch_assoc()) { ?>
                        <option value="<?php echo $rowTri["id_trimestre"]; ?>"<?php if($rowTri['id_trimestre']==$tri_concepto) { echo 'selected'; } ?>><?php echo $rowTri['tri_concepto']; ?></option>
                        <?php } ?> 
                    </select>
                    </div>	
    
                    <div class="col-md-2 m-l">
                    <label for="ap_puerperio" class="form-label">PUERPERIO:</label>
                    <select name="ap_puerperio" id="ap_puerperio" class="form-select" >
                        <option value="">Seleccionar...</option>
                        <option value="SI"<?php if('SI' == $rowcita['ap_puerperio']) { echo 'selected'; } ?>>SI</option>
                        <option value="NO"<?php if('NO' == $rowcita['ap_puerperio']) { echo 'selected'; } ?>>NO</option> 
                    </select>
                    </div>	
    
                    <div class="col-md-2 m-l">
                    <label for="ap_lactancia" class="form-label">LACTANCIA:</label>
                    <select name="ap_lactancia" id="ap_lactancia" class="form-select" >
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
                    <label for="ap_prueba_serologica_embarazo" class="form-label">PRUEBA SEROLÓGICA DE EMBARAZO</label><label class="form-label" style="color: red">*:</label>
                    <select name="ap_prueba_serologica_embarazo" id="ap_prueba_serologica_embarazo" class="form-select" required>
                        <option value="">Seleccionar...</option>
                        <option value="SI"<?php if('SI' == $rowcita['ap_prueba_serologica_embarazo']) { echo 'selected'; } ?>>SI</option>
                        <option value="NO"<?php if('NO' == $rowcita['ap_prueba_serologica_embarazo']) { echo 'selected'; } ?>>NO</option> 
                    </select>
                    </div>	
    
                    <div class="col-md-4 m-l">
                    <label for="ap_se_ignora" class="form-label">ÚLTIMA MENSTRUACIÓN SE IGNORA</label><label class="form-label" style="color: red">*:</label>
                    <select name="ap_se_ignora" id="ap_se_ignora" class="form-select" required >
                        <option value="">Seleccionar...</option>
                        <option value="SI"<?php if('SI' == $rowcita['ap_se_ignora']) { echo 'selected'; } ?>>SI</option>
                        <option value="NO"<?php if('NO' == $rowcita['ap_se_ignora']) { echo 'selected'; } ?>>NO</option> 
                    </select>
                    </div>	
    
                    <div class="col-md-4 m-l">
                    <label for="ap_ulima_mestruacion" class="form-label"  value="<?php echo $rowcita['ap_ulima_mestruacion']; ?>">FECHA</label><label class="form-label" style="color: red">*:</label>
                    <input type="date" name="ap_ulima_mestruacion" id="ap_ulima_mestruacion" class="form-control"  value="<?php echo $rowcita['ap_ulima_mestruacion']; ?>" >
                    </div>	
    
                </div>	
                </div>    
    
                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-start text-start ">
    
                    <div class="col-md-4 m-l">
                    <label for="ap_confiable" class="form-label">CONFIABLE</label><label class="form-label" style="color: red">*:</label>
                    <select name="ap_confiable" id="ap_confiable" class="form-select" >
                        <option value="">Seleccionar...</option>
                        <option value="SI"<?php if('SI' == $rowcita['ap_confiable']) { echo 'selected'; } ?>>SI</option>
                        <option value="NO"<?php if('NO' == $rowcita['ap_confiable']) { echo 'selected'; } ?>>NO</option> 
                    </select>
                    </div>	
    
                    <div class="col-md-4 m-l">
                    <label for="ap_fecha_ultrasonido_obstretico" class="form-label" value="<?php echo $rowcita['ap_fecha_ultrasonido_obstretico']; ?>">FECHA DE ULTRASONIDO OBSTÉTRICO</label><label class="form-label" style="color: red">*:</label>
                    <input type="date" name="ap_fecha_ultrasonido_obstretico" id="ap_fecha_ultrasonido_obstretico" class="form-control" value="<?php echo $rowcita['ap_fecha_ultrasonido_obstretico']; ?>" required >
                    </div>	
    
                    <div class="col-md-4 m-l">
                    <label for="ap_semana_gestacion_usg" class="form-label">SEMANAS DE GESTACIÓN POR USG</label><label class="form-label" style="color: red">*:</label>
                    <input  type="text" name="ap_semana_gestacion_usg" id="ap_semana_gestacion_usg"  class="form-control" required value="<?php echo $rowcita['ap_semana_gestacion_usg']; ?>" >                        
                    </div>   
                                    
                </div>	
                </div>
    
                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-start text-start ">
    
                    <div class="col-md-4 m-l">
                    <label for="ap_fecha_probable_parto" class="form-label" value="<?php echo $rowcita['ap_fecha_probable_parto']; ?>"> FECHA PROBABLE DE PARTO</label><label class="form-label" style="color: red">*:</label>
                    <input type="date" name="ap_fecha_probable_parto" id="ap_fecha_probable_parto" class="form-control" required value="<?php echo $rowcita['ap_fecha_probable_parto']; ?>" >
                    </div>	
    
                </div>	
                </div>  
        <!----FIN----> 

        <!----INICIO---->
        <center> <div class="bg-secondary p-2 text-white bg-opacity-90 modal-title fs-5">ANTECEDENTES FAMILIARES</div></center>
                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-start text-start ">
                
                    <div class="col-md-3 m-l">
                    <label for="af_ninguno" class="form-label">NINGUNO</label><label class="form-label" style="color: red">*:</label>
                    <select name="af_ninguno" id="af_ninguno" class="form-select" required >
                        <option value="">Seleccionar...</option>
                        <option value="SI"<?php if('SI' == $rowcita['af_ninguno']) { echo 'selected'; } ?>>SI</option>
                        <option value="NO"<?php if('NO' == $rowcita['af_ninguno']) { echo 'selected'; } ?>>NO</option> 
                    </select>
                    </div>       
    
                    <div class="col-md-3 m-l">
                    <label for="af_tuberculosis" class="form-label">TUBERCULOSIS:</label>
                    <select name="af_tuberculosis" id="af_tuberculosis" class="form-select" >
                        <option value="">Seleccionar...</option>
                        <option value="SI"<?php if('SI' == $rowcita['af_tuberculosis']) { echo 'selected'; } ?>>SI</option>
                        <option value="NO"<?php if('NO' == $rowcita['af_tuberculosis']) { echo 'selected'; } ?>>NO</option> 
                    </select>
                    </div>
    
                    <div class="col-md-3 m-l">
                    <label for="af_hipertencion" class="form-label">HIPERTENSIÓN:</label>
                    <select name="af_hipertencion" id="af_hipertencion" class="form-select" >
                        <option value="">Seleccionar...</option>
                        <option value="SI"<?php if('SI' == $rowcita['af_hipertencion']) { echo 'selected'; } ?>>SI</option>
                        <option value="NO"<?php if('NO' == $rowcita['af_hipertencion']) { echo 'selected'; } ?>>NO</option> 
                    </select>
                    </div>
        
                    <div class="col-md-3 m-l">
                    <label for="af_diabetes" class="form-label">DIABETES:</label>
                    <select name="af_diabetes" id="af_diabetes" class="form-select" >
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
                    <select name="af_ef_hiper_embarazo" id="af_ef_hiper_embarazo" class="form-select" >
                        <option value="">Seleccionar...</option>
                        <option value="SI"<?php if('SI' == $rowcita['af_ef_hiper_embarazo']) { echo 'selected'; } ?>>SI</option>
                        <option value="NO"<?php if('NO' == $rowcita['af_ef_hiper_embarazo']) { echo 'selected'; } ?>>NO</option> 
                    </select>
                    </div>

                    <div class="col-md-3 m-l">
                    <label for="af_otro" class="form-label">OTRO:</label>
                    <select name="af_otro" id="af_otro" class="form-select" >
                        <option value="">Seleccionar...</option>
                        <option value="SI"<?php if('SI' == $rowcita['af_otro']) { echo 'selected'; } ?>>SI</option>
                        <option value="NO"<?php if('NO' == $rowcita['af_otro']) { echo 'selected'; } ?>>NO</option> 
                    </select>
                    </div>
            
                    <div class="col-md-5 m-l">
                    <label for="af_otro_especifique" class="form-label">ESPECIFIQUE:</label>
                    <input  type="text" name="af_otro_especifique" id="af_otro_especifique"  class="form-control"  value="<?php echo $rowcita['af_otro_especifique']; ?>" >                        
                    </div>   
            
                </div>	
                </div> 
        <!----FIN---->    

        <!----INICIO---->
        <center> <div class="bg-success p-2 text-white bg-opacity-90 modal-title fs-5">ANTECEDENTES PERSONALES PATOLOGICOS</div></center>
                     <div class="container-fluid p-2 text-center">
                    <div class="row justify-content-start text-start ">  

                        <div class="col-md-2 m-l">
                        <label for="app_ninguno" class="form-label">NINGUNO</label><label class="form-label" style="color: red">*:</label>
                        <select name="app_ninguno" id="app_ninguno" class="form-select" required >
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowcita['app_ninguno']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowcita['app_ninguno']) { echo 'selected'; } ?>>NO</option> 
                        </select>
                        </div>   
                        
                        <div class="col-md-4 m-l">
                        <label for="app_diabetes_geostacional" class="form-label">DIABETES GESTACIONAL:</label>
                        <select name="app_diabetes_geostacional" id="app_diabetes_geostacional" class="form-select" >
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowcita['app_diabetes_geostacional']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowcita['app_diabetes_geostacional']) { echo 'selected'; } ?>>NO</option> 
                        </select>
                        </div>       
                        
                        <div class="col-md-2 m-l">
                        <label for="app_diabetes" class="form-label">DIABETES:</label>
                        <select name="app_diabetes" id="app_diabetes" class="form-select" >
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowcita['app_diabetes']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowcita['app_diabetes']) { echo 'selected'; } ?>>NO</option> 
                        </select>
                        </div>       

                        <div class="col-md-4 m-l">
                        <label for="app_hipertencio_arterial" class="form-label">HIPERTENSIÓN ARTERIAL SISTÉMICA:</label>
                        <select name="app_hipertencio_arterial" id="app_hipertencio_arterial" class="form-select" >
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
                        <select name="app_preeclampsia_enclampsia" id="app_preeclampsia_enclampsia" class="form-select" >
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowcita['app_preeclampsia_enclampsia']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowcita['app_preeclampsia_enclampsia']) { echo 'selected'; } ?>>NO</option> 
                        </select>
                        </div>   
                        
                        <div class="col-md-2 m-l">
                        <label for="app_nefropatia" class="form-label">NEFROPATÍA:</label>
                        <select name="app_nefropatia" id="app_nefropatia" class="form-select" >
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowcita['app_nefropatia']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowcita['app_nefropatia']) { echo 'selected'; } ?>>NO</option> 
                        </select>
                        </div>

                        <div class="col-md-2 m-l">
                        <label for="app_cardiopatia" class="form-label">CARDIOPATÍA:</label>
                        <select name="app_cardiopatia" id="app_cardiopatia" class="form-select" >
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowcita['app_cardiopatia']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowcita['app_cardiopatia']) { echo 'selected'; } ?>>NO</option> 
                        </select>
                        </div>
                        
                        <div class="col-md-4 m-l">
                        <label for="app_hemorragia_obstetrica" class="form-label">HEMORRAGIA OBSTÉTRICA:</label>
                        <select name="app_hemorragia_obstetrica" id="app_hemorragia_obstetrica" class="form-select" >
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
                        <select name="app_padecimiento_oncologico" id="app_padecimiento_oncologico" class="form-select" >
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowcita['app_padecimiento_oncologico']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowcita['app_padecimiento_oncologico']) { echo 'selected'; } ?>>NO</option> 
                        </select>
                        </div>

                        <div class="col-md-2 m-l">
                        <label for="app_b24x" class="form-label">B24X:</label>
                        <select name="app_b24x" id="app_b24x" class="form-select" >
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowcita['app_b24x']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowcita['app_b24x']) { echo 'selected'; } ?>>NO</option> 
                        </select>
                        </div>
                        
                        <div class="col-md-2 m-l">
                        <label for="app_a539" class="form-label">A539:</label>
                        <select name="app_a539" id="app_a539" class="form-select" >
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowcita['app_a539']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowcita['app_a539']) { echo 'selected'; } ?>>NO</option> 
                        </select>
                        </div>

                        <div class="col-md-4 m-l">
                        <label for="app_enfermedad_trnas_vector" class="form-label">ENFERMEDAD TRANSMITIDA POR VECTOR:</label>
                        <select name="app_enfermedad_trnas_vector" id="app_enfermedad_trnas_vector" class="form-select" >
                            <option value="">Seleccionar...</option>
                            <?php while ($rowTV = $resultadoTV->fetch_assoc()) { ?>
                            <option value="<?php echo $rowTV["id_trans_vector"]; ?>"<?php if($rowTV['id_trans_vector']==$tv_concepto) { echo 'selected'; } ?>><?php echo $rowTV['tv_concepto']; ?></option>
                            <?php } ?> 
                        </select>
                        </div>

                    </div>
                    </div>

                    <div class="container-fluid p-2 text-center">
                    <div class="row justify-content-start text-start "> 

                        <div class="col-md-4 m-l">
                        <label for="app_tuberculosis" class="form-label">TUBERCULOSIS:</label>
                        <select name="app_tuberculosis" id="app_tuberculosis" class="form-select" >
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowcita['app_tuberculosis']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowcita['app_tuberculosis']) { echo 'selected'; } ?>>NO</option> 
                        </select>
                        </div>
                        
                        <div class="col-md-4 m-l">
                        <label for="app_sars_cov2" class="form-label">SARS-COV2:</label>
                        <select name="app_sars_cov2" id="app_sars_cov2" class="form-select" >
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowcita['app_sars_cov2']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowcita['app_sars_cov2']) { echo 'selected'; } ?>>NO</option> 
                        </select>
                        </div>           
                        
                        <div class="col-md-4 m-l">
                        <label for="app_otro_antecedente" class="form-label">OTRO ANTECEDENTE:</label>
                        <select name="app_otro_antecedente" id="app_otro_antecedente" class="form-select" >
                            <option value="">Seleccionar...</option>
                            <?php while ($rowOA = $resultadoOA->fetch_assoc()) { ?>
                            <option value="<?php echo $rowOA["id_o_antecedente"]; ?>"<?php if($rowOA['id_o_antecedente']==$oa_concepto) { echo 'selected'; } ?>><?php echo $rowOA['oa_concepto']; ?></option>
                            <?php } ?>  
                        </select>
                        </div>  

                    </div>	
                    </div> 
        <!----FIN---->    
        <!----INICIO---->
            <center> <div class="bg-secondary p-2 text-white bg-opacity-90 modal-title fs-5">GRUPO SANGUÍNEO Y RH</div></center>
                <div class="container-fluid p-2 text-center">
                    <div class="row justify-content-start text-start ">

                        <div class="col-md-3 m-l">
                        <label for="app_grupo_sanguinieo" class="form-label">GRUPO SANGUÍNEO</label><label class="form-label" style="color: red">*:</label>
                        <select name="app_grupo_sanguinieo" id="app_grupo_sanguinieo" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <?php while ($rowGS = $resultadoGS->fetch_assoc()) { ?>
                            <option value="<?php echo $rowGS["id_gpo_sanguineo"]; ?>"<?php if($rowGS['id_gpo_sanguineo']==$gs_concepto) { echo 'selected'; } ?>><?php echo $rowGS['gs_concepto']; ?></option>
                            <?php } ?>
                        </select>
                        </div>

                        <div class="col-md-3 m-l">
                        <label for="app_rh" class="form-label">Rh</label><label class="form-label" style="color: red">*:</label>
                        <select name="app_rh" id="app_rh" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <option value="POSITIVO"<?php if('POSITIVO' == $rowcita['app_rh']) { echo 'selected'; } ?>>POSITIVO</option>
                            <option value="NEGATIVO"<?php if('NEGATIVO' == $rowcita['app_rh']) { echo 'selected'; } ?>>NEGATIVO</option> 
                        </select>
                        </div>

                        <div class="col-md-3 m-l">
                        <label for="app_prueba_coombs" class="form-label">¿SE REALIZÓ PRUEBA COOMBS?</label><label class="form-label" style="color: red">*:</label>
                        <select name="app_prueba_coombs" id="app_prueba_coombs" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowcita['app_prueba_coombs']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowcita['app_prueba_coombs']) { echo 'selected'; } ?>>NO</option> 
                        </select>
                        </div>

                        <div class="col-md-3 m-l">
                        <label for="app_tratamiento" class="form-label">¿TRATAMIENTO?</label><label class="form-label" style="color: red">*:</label>
                        <select name="app_tratamiento" id="app_tratamiento" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowcita['app_tratamiento']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowcita['app_tratamiento']) { echo 'selected'; } ?>>NO</option> 
                        </select>
                        </div>

                    </div>	
                    </div>
         <!----FIN---->                      
        
        <!----INICIO---->
        <center> <div class="bg-warning p-2 text-black bg-opacity-90 modal-title fs-5">DETECCIONES EN EL EMBARAZO</div></center>
                  
        <br>
                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-center text-start ">  
                
                        <div class="col-md-4 m-l">
                            <div class="input-group mb-3">
                            <span class="input-group-text">VIOLENCIA FAMILIAR<label class="form-label" style="color: red">*:</label></span>
                            <label for="de_violencia_fam" class="form-label"></label>
                            <select name="de_violencia_fam" id="de_violencia_fam" class="form-select" required>
                                <option value="">Seleccionar...</option>
                                <option value="POSITIVO"<?php if('POSITIVO' == $rowcita['de_violencia_fam']) { echo 'selected'; } ?>>POSITIVO</option>
                                <option value="NEGATIVO"<?php if('NEGATIVO' == $rowcita['de_violencia_fam']) { echo 'selected'; } ?>>NEGATIVO</option> 
                            </select>
                            </div>
                        </div>                 
                
                </div>	
                </div> 
                    <center> <left> <h2 class="modal-title fs-5" id="editaModalLabel">ITS 1a DETECCIÓN</h2></left></center>  
                    <center> <left> <h3 class="modal-title fs-5" id="editaModalLabel">B24X</h3></left></center>
                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-start text-start ">  

                    <div class="col-md-2 m-l">
                    <label for="devf_b24x_fecha_inmuno" class="form-label" value="<?php echo $rowcita['devf_b24x_fecha_inmuno']; ?>">FECHA :</label>
                    <input type="date" name="devf_b24x_fecha_inmuno" id="devf_b24x_fecha_inmuno" class="form-control" value="<?php echo $rowcita['devf_b24x_fecha_inmuno']; ?>" >
                    </div>

                    <div class="col-md-4 m-l">
                    <label for="devf_b24x_imnuno" class="form-label">PRUEBA RÁPIDA (INMUNOCROMATOGRAFÍA):</label>
                    <select name="devf_b24x_imnuno" id="devf_b24x_imnuno" class="form-select" >
                        <option value="">Seleccionar...</option>
                        <option value="NEGATIVO"<?php if('NEGATIVO' == $rowcita['devf_b24x_imnuno']) { echo 'selected'; } ?>>NEGATIVO</option>
                        <option value="POSITIVA"<?php if('POSITIVA' == $rowcita['devf_b24x_imnuno']) { echo 'selected'; } ?>>POSITIVA</option> 
                    </select>
                    </div>

                    <div class="col-md-2 m-l">
                    <label for="devf_b24x_fecha_enzimo" class="form-label" value="<?php echo $rowcita['devf_b24x_fecha_enzimo']; ?>">FECHA :</label>
                    <input type="date" name="devf_b24x_fecha_enzimo" id="devf_b24x_fecha_enzimo" class="form-control" value="<?php echo $rowcita['devf_b24x_fecha_enzimo']; ?>" >
                    </div>

                    <div class="col-md-4 m-l">
                    <label for="devf_b24x_enzimo" class="form-label">ENZIMOINMUNOENSAYO (ELISA):</label>
                    <select name="devf_b24x_enzimo" id="devf_b24x_enzimo" class="form-select" >
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
                    <input type="date" name="devf_a539_fecha_inmuno" id="devf_a539_fecha_inmuno" class="form-control" value="<?php echo $rowcita['devf_a539_fecha_inmuno']; ?>" >
                    </div>

                    <div class="col-md-4 m-l">
                    <label for="devf_a539_imnuno" class="form-label">PRUEBA RÁPIDA:</label>
                    <select name="devf_a539_imnuno" id="devf_a539_imnuno" class="form-select" >
                        <option value="">Seleccionar...</option>
                        <option value="NEGATIVO"<?php if('NEGATIVO' == $rowcita['devf_a539_imnuno']) { echo 'selected'; } ?>>NEGATIVO</option>
                        <option value="POSITIVA"<?php if('POSITIVA' == $rowcita['devf_a539_imnuno']) { echo 'selected'; } ?>>POSITIVA</option> 
                    </select>
                    </div>
                    
                    <div class="col-md-2 m-l">
                    <label for="devf_a539_fecha_enzimo" class="form-label" value="<?php echo $rowcita['devf_a539_fecha_enzimo']; ?>">FECHA :</label>
                    <input type="date" name="devf_a539_fecha_enzimo" id="devf_a539_fecha_enzimo" class="form-control" value="<?php echo $rowcita['devf_a539_fecha_enzimo']; ?>" >
                    </div>
                    
                    <div class="col-md-4 m-l">
                    <label for="devf_a539_enzimo" class="form-label">LABORTATORIO:</label>
                    <select name="devf_a539_enzimo" id="devf_a539_enzimo" class="form-select" >
                        <option value="">Seleccionar...</option>
                        <option value="NEGATIVO"<?php if('NEGATIVO' == $rowcita['devf_a539_enzimo']) { echo 'selected'; } ?>>NEGATIVO</option>
                        <option value="POSITIVA"<?php if('POSITIVA' == $rowcita['devf_a539_enzimo']) { echo 'selected'; } ?>>POSITIVA</option> 
                    </select>
                    </div>

                </div>	
                </div>
                <br>
                <hr>
                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-center text-start ">  
                        <div class="col-md-4 m-l">
                            <div class="input-group mb-3">
                            <span class="input-group-text">DEPRESIÓN PRENATAL<label class="form-label" style="color: red">*:</label></span>
                            <label for="de_depresion_prenatal" class="form-label"></label>
                            <select name="de_depresion_prenatal" id="de_depresion_prenatal" class="form-select" required>
                                <option value="">Seleccionar...</option>
                                <option value="POSITIVO"<?php if('POSITIVO' == $rowcita['de_depresion_prenatal']) { echo 'selected'; } ?>>POSITIVO</option>
                                <option value="NEGATIVO"<?php if('NEGATIVO' == $rowcita['de_depresion_prenatal']) { echo 'selected'; } ?>>NEGATIVO</option> 
                            </select>
                            </div> 
                        </div>    
                </div>    
                </div>            
                    <center> <left> <h2 class="modal-title fs-5" id="editaModalLabel">ITS 2a DETECCIÓN</h2></left></center>   
                    <center> <left> <h3 class="modal-title fs-5" id="editaModalLabel">B24X</h3></left></center>
                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-start text-start ">  
                
                    <div class="col-md-2 m-l">
                        <label for="dedp_b24x_fecha_rapida" class="form-label" value="<?php echo $rowcita['dedp_b24x_fecha_rapida']; ?>">FECHA :</label>
                        <input type="date" name="dedp_b24x_fecha_rapida" id="dedp_b24x_fecha_rapida" class="form-control" value="<?php echo $rowcita['dedp_b24x_fecha_rapida']; ?>" >
                    </div>

                    <div class="col-md-4 m-l">
                    <label for="dedp_b24x_rapida" class="form-label">PRUEBA RÁPIDA (INMUNOCROMATOGRAFÍA):</label>
                    <select name="dedp_b24x_rapida" id="dedp_b24x_rapida" class="form-select" >
                        <option value="">Seleccionar...</option>
                        <option value="NEGATIVO"<?php if('NEGATIVO' == $rowcita['dedp_b24x_rapida']) { echo 'selected'; } ?>>NEGATIVO</option>
                        <option value="POSITIVA"<?php if('POSITIVA' == $rowcita['dedp_b24x_rapida']) { echo 'selected'; } ?>>POSITIVA</option> 
                    </select>
                    </div>
                    
                    <div class="col-md-2 m-l">
                        <label for="dedp_b24x_fecha_lab" class="form-label" value="<?php echo $rowcita['dedp_b24x_fecha_lab']; ?>">FECHA :</label>
                        <input type="date" name="dedp_b24x_fecha_lab" id="dedp_b24x_fecha_lab" class="form-control" value="<?php echo $rowcita['dedp_b24x_fecha_lab']; ?>" >
                    </div>

                    <div class="col-md-4 m-l">
                        <label for="dedp_b24x_lab" class="form-label">ENZIMOINMUNOENSAYO (ELISA):</label>
                        <select name="dedp_b24x_lab" id="dedp_b24x_lab" class="form-select" >
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
                        <input type="date" name="dedp_a539_fecha_rapidaa" id="dedp_a539_fecha_rapidaa" class="form-control" value="<?php echo $rowcita['dedp_a539_fecha_rapidaa']; ?>" >
                    </div>

                    <div class="col-md-4 m-l">
                    <label for="dedp_a539_rapida" class="form-label">PRUEBA RÁPIDA:</label>
                    <select name="dedp_a539_rapida" id="dedp_a539_rapida" class="form-select" >
                        <option value="">Seleccionar...</option>
                        <option value="NEGATIVO"<?php if('NEGATIVO' == $rowcita['dedp_a539_rapida']) { echo 'selected'; } ?>>NEGATIVO</option>
                        <option value="POSITIVA"<?php if('POSITIVA' == $rowcita['dedp_a539_rapida']) { echo 'selected'; } ?>>POSITIVA</option> 
                    </select>
                    </div>

                    <div class="col-md-2 m-l">
                        <label for="dedp_a539_fecha_lab" class="form-label" value="<?php echo $rowcita['dedp_a539_fecha_lab']; ?>">FECHA :</label>
                        <input type="date" name="dedp_a539_fecha_lab" id="dedp_a539_fecha_lab" class="form-control" value="<?php echo $rowcita['dedp_a539_fecha_lab']; ?>" >
                    </div>

                    <div class="col-md-4 m-l">
                        <label for="dedp_a539_lab" class="form-label">LABORTATORIO:</label>
                        <select name="dedp_a539_lab" id="dedp_a539_lab" class="form-select" >
                            <option value="">Seleccionar...</option>
                            <option value="NEGATIVO"<?php if('NEGATIVO' == $rowcita['dedp_a539_lab']) { echo 'selected'; } ?>>NEGATIVO</option>
                            <option value="POSITIVA"<?php if('POSITIVA' == $rowcita['dedp_a539_lab']) { echo 'selected'; } ?>>POSITIVA</option> 
                        </select>
                    </div>

                </div> 
                </div>   
                         <br>
                <hr>
        <!----FIN---->    
        <!----INICIO---->
        <center> <div class="bg-secondary p-2 text-white bg-opacity-90 modal-title fs-5">PRUEBAS CONFIRMATORIAS LABORATORIO Y OTRAS SUPLEMENTARIAS</div></center>
        <div class="container-fluid p-2 text-center">
        <div class="row justify-content-start text-start ">
            <label>  </label>
                                
                <div class="col-md-3 m-l">
                    <label for="pc_lab_otr_b24x_fecha" class="form-label" value="<?php echo $rowcita['pc_lab_otr_b24x_fecha']; ?>">B24X - FECHA:</label>
                    <input type="date" name="pc_lab_otr_b24x_fecha" id="pc_lab_otr_b24x_fecha" class="form-control" value="<?php echo $rowcita['pc_lab_otr_b24x_fecha']; ?>" >
                </div>

                <div class="col-md-3 m-l">
                    <label for="pc_lab_otr_b24x_detectable" class="form-label">B24X - CARGA VIRAL:</label>
                    <select name="pc_lab_otr_b24x_detectable" id="pc_lab_otr_b24x_detectable" class="form-select" >
                        <option value="">Seleccionar...</option>
                        <option value="INDETECTABLE"<?php if('INDETECTABLE' == $rowcita['pc_lab_otr_b24x_detectable']) { echo 'selected'; } ?>>INDETECTABLE</option>
                        <option value="DETECTABLE"<?php if('DETECTABLE' == $rowcita['pc_lab_otr_b24x_detectable']) { echo 'selected'; } ?>>DETECTABLE</option> 
                    </select>
                </div>


        
                <div class="col-md-3 m-l">
                    <label for="pc_lab_otr_a539_fecha" class="form-label" value="<?php echo $rowcita['pc_lab_otr_a539_fecha']; ?>">539 - FECHA :</label>
                    <input type="date" name="pc_lab_otr_a539_fecha" id="pc_lab_otr_a539_fecha" class="form-control" value="<?php echo $rowcita['pc_lab_otr_a539_fecha']; ?>" >
                </div>

                <div class="col-md-3 m-l">
                    <label for="pc_lab_otr_a539_posneg" class="form-label">539 - CARGA VIRAL:</label>
                    <select name="pc_lab_otr_a539_posneg" id="pc_lab_otr_a539_posneg" class="form-select" >
                        <option value="">Seleccionar...</option>
                        <option value="INDETECTABLE"<?php if('INDETECTABLE' == $rowcita['pc_lab_otr_a539_posneg']) { echo 'selected'; } ?>>NEGATIVO</option>
                        <option value="DETECTABLE"<?php if('DETECTABLE' == $rowcita['pc_lab_otr_a539_posneg']) { echo 'selected'; } ?>>POSITIVO</option> 
                    </select>
                </div>
            
        </div>	
        </div> 
        <!----FIN---->   

        <!----INICIO---->
        <center> <div class="bg-secondary p-2 text-white bg-opacity-90 modal-title fs-5">CONFIRMACIÓN DE DIABETES GESTACIONAL</div></center>


        <div class="container-fluid p-2 text-center">
        <div class="row justify-content-start text-start ">
            <div class="d-flex justify-content-center">CURVA DE TOLERANCIA A LA GLUCOSA</div>
                                        
                <div class="col-md-3 m-l">
                    <div class="input-group mb-3">
                    <span class="input-group-text">FECHA:</span>
                        <label for="cdg_fecha" class="form-label" value="<?php echo $rowcita['cdg_fecha']; ?>"></label>
                        <input type="date" name="cdg_fecha" id="cdg_fecha" class="form-control" value="<?php echo $rowcita['cdg_fecha']; ?>" >
                    </div>
                </div>
                <br>

                <div class="col-md-3 m-l">
                    <div class="input-group mb-3">
                    <span class="input-group-text">AYUNO:</span>
                    <input type="text" name="cdg_ayuno" id="cdg_ayuno" class="form-control" aria-label="Amount (to the nearest dollar)"  value="<?php echo $rowcita['cdg_ayuno']; ?>">
                    <span class="input-group-text">mg/dl</span>
                    </div>
                </div>

                <div class="col-md-3 m-l">
                    <div class="input-group mb-3">
                    <span class="input-group-text">60:</span>
                    <input type="text" name="cdg_60" id="cdg_60" class="form-control" aria-label="Amount (to the nearest dollar)"  value="<?php echo $rowcita['cdg_60']; ?>">
                    <span class="input-group-text">mg/dl</span>
                    </div>
                </div>

                <div class="col-md-3 m-l">
                    <div class="input-group mb-3">
                    <span class="input-group-text">120:</span>
                    <input type="text" name="cdg_120" id="cdg_120" class="form-control" aria-label="Amount (to the nearest dollar)"  value="<?php echo $rowcita['cdg_120']; ?>">
                    <span class="input-group-text">mg/dl</span>
                    </div>
                </div>                                   
        </div>	
        </div> 
        <!----FIN---->   

        <!----INICIO---->
        <center> <div class="bg-secondary p-2 text-white bg-opacity-90 modal-title fs-5">ADICCIONES</div></center>
                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-start text-start ">

                    <div class="col-md-3 m-l">
                    <div class="input-group mb-3">
                    <span class="input-group-text">ADICCIONES<label class="form-label" style="color: red">*:</label></span>
                        <select name="adicciones" id="adicciones" class="form-select" >
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowcita['adicciones']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowcita['adicciones']) { echo 'selected'; } ?>>NO</option>   
                        </select>
                    </div>	
                    </div>

                    <div class="col-md-3 m-l">
                    <div class="input-group mb-3">
                        <span class="input-group-text">TABACO:</span>
                        <select name="adic_tabaco" id="adic_tabaco" class="form-select" >
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowcita['adic_tabaco']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowcita['adic_tabaco']) { echo 'selected'; } ?>>NO</option>   
                        </select>
                    </div>	
                    </div>

                    <div class="col-md-3 m-l">
                    <div class="input-group mb-3">
                        <span class="input-group-text">ANTIDEPRESIVOS:</span>
                        <select name="adic_antidepre" id="adic_antidepre" class="form-select" >
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowcita['adic_antidepre']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowcita['adic_antidepre']) { echo 'selected'; } ?>>NO</option>   
                        </select>
                    </div>	
                    </div>
                    
                    <div class="col-md-3 m-l">
                    <div class="input-group mb-3">
                        <span class="input-group-text">ALCOHOL:</span>
                        <select name="adic_alcohol" id="adic_alcohol" class="form-select" >
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowcita['adic_alcohol']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowcita['adic_alcohol']) { echo 'selected'; } ?>>NO</option>   
                        </select>
                    </div>	
                    </div>
                    
                </div>	
                </div> 

                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-center text-start ">

                    <div class="col-md-3 m-l">
                    <div class="input-group mb-3">
                        <span class="input-group-text">ANSIOLÍTICOS:</span>
                        <select name="adic_ansioliticos" id="adic_ansioliticos" class="form-select" >
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowcita['adic_ansioliticos']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowcita['adic_ansioliticos']) { echo 'selected'; } ?>>NO</option>   
                        </select>
                    </div>	
                    </div>

                    <div class="col-md-3 m-l">
                    <div class="input-group mb-3">
                        <span class="input-group-text">OTROS:</span>
                        <select name="adic_otros" id="adic_otros" class="form-select" >
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowcita['adic_otros']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowcita['adic_otros']) { echo 'selected'; } ?>>NO</option>   
                        </select>
                    </div>	
                    </div>                

                </div>	
                </div> 
            
                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-center text-start ">

                    <div class="col-md-4 m-l">
                    <div class="input-group mb-3">
                        <span class="input-group-text">¿CONSUMO ACTUAL?</span>
                        <select name="adic_consumo_act" id="adic_consumo_act" class="form-select" >
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowcita['adic_consumo_act']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowcita['adic_consumo_act']) { echo 'selected'; } ?>>NO</option>   
                        </select>
                    </div>
                    </div>
                
                    <div class="col-md-2 m-l">
                    <div class="input-group mb-3">
                        <span class="input-group-text">AÑOS:</span>
                        <input type="number" name="adic_años" min="0" max="30" id="adic_años" class="form-control"value="<?php echo $rowcita['adic_años']; ?>" >                        
                    </div>
                    </div>

                    <div class="col-md-2 m-l">
                    <div class="input-group mb-3">
                        <span class="input-group-text">MESES:</span>
                        <input type="number" name="adic_meses" min="0" max="12" id="adic_meses" class="form-control"value="<?php echo $rowcita['adic_meses']; ?>" >                        
                    </div>
                    </div>

                </div>	
                </div>  
        <!----FIN---->

        <!----INICIO---->
        <center> <div class="bg-secondary p-2 text-white bg-opacity-90 modal-title fs-5">SALUD BUCAL DURANTE EL EMBARAZO</div></center>
                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-start text-start ">
                        <div class="d-flex justify-content-center">ATENCIÓN A:</div>
                    <div class="col-md-3 m-l">
                        <label for="sbde_rev_odont" class="form-label">REVISIÓN ODONTOLÓGICA</label><label class="form-label" style="color: red">*:</label>
                        <select name="sbde_rev_odont" id="sbde_rev_odont" class="form-select" required >
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowcita['sbde_rev_odont']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowcita['sbde_rev_odont']) { echo 'selected'; } ?>>NO</option>   
                        </select>
                    </div>	

                    <div class="col-md-3 m-l">
                        <label for="sbde_atn_caries" class="form-label">CARIES</label><label class="form-label" style="color: red">*:</label>
                        <select name="sbde_atn_caries" id="sbde_atn_caries" class="form-select" required >
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowcita['sbde_atn_caries']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowcita['sbde_atn_caries']) { echo 'selected'; } ?>>NO</option>   
                        </select>
                    </div>	

                    <div class="col-md-3 m-l">
                        <label for="sbde_atn_periodonitis" class="form-label">PERIODONTITIS</label><label class="form-label" style="color: red">*:</label>
                        <select name="sbde_atn_periodonitis" id="sbde_atn_periodonitis" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowcita['sbde_atn_periodonitis']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowcita['sbde_atn_periodonitis']) { echo 'selected'; } ?>>NO</option>   
                        </select>
                    </div>	

                    <div class="col-md-3 m-l">
                        <label for="sbde_atn_otro" class="form-label">OTRO</label><label class="form-label" style="color: red">*:</label>
                        <select name="sbde_atn_otro" id="sbde_atn_otro" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowcita['sbde_atn_otro']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowcita['sbde_atn_otro']) { echo 'selected'; } ?>>NO</option>   
                        </select>
                    </div>	
                    
                </div>	
                </div> 
        <!----FIN---->  

        <!----INICIO---->
        <center> <div class="bg-secondary p-2 text-white bg-opacity-90 modal-title fs-5">VALORACIÓN NUTRICIONAL</div></center>
                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-start text-start ">
                        <center>
                    <div class="col-md-3 m-l">
                        <label for="v_nuticional" class="form-label">VALORACIÓN NUTRICIONAL</label><label class="form-label" style="color: red">*:</label>
                        <select name="v_nuticional" id="v_nuticional" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowcita['v_nuticional']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowcita['v_nuticional']) { echo 'selected'; } ?>>NO</option>   
                        </select>
                    </div>	
                        </center>    
                </div>	
                </div> 
        <!----FIN----> 

        <!----INICIO---->
        <center> <div class="bg-primary p-2 text-white bg-opacity-90 modal-title fs-5">ANTECEDENTES GINECOOBSTÉTRICOS/ATENCIÓN PREGESTACIONAL</div></center>
                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-start text-start "> 
                    
                    <div class="col-md-4 m-l">
                    <label for="ag_atencio_preges" class="form-label">¿TUVO ATENCIÓN PREGESTACIONAL?</label><label class="form-label" style="color: red">*:</label>
                    <select name="ag_atencio_preges" id="ag_atencio_preges" class="form-select" required>
                        <option value="">Seleccionar...</option>
                        <option value="SI"<?php if('SI' == $rowcita['ag_atencio_preges']) { echo 'selected'; } ?>>SI</option>
                        <option value="NO"<?php if('NO' == $rowcita['ag_atencio_preges']) { echo 'selected'; } ?>>NO</option> 
                    </select>
                    </div> 

                    <div class="col-md-5 m-l">
                    <label for="ag_riesgos" class="form-label">RIESGOS</label><label class="form-label" style="color: red">*:</label>
                    <select name="ag_riesgos" id="ag_riesgos" class="form-select" required>
                        <option value="">Seleccionar...</option>
                        <?php while ($rowRi = $resultadoRi->fetch_assoc()) { ?>
                        <option value="<?php echo $rowRi["id_riesgos"]; ?>"<?php if($rowRi['id_riesgos']==$r_concepto) { echo 'selected'; } ?>><?php echo $rowRi['r_concepto']; ?></option>
                        <?php } ?>  
                    </select>
                    </div> 

                    <div class="col-md-1 m-l">
                        <label for="ag_gestas" class="form-label">GESTAS</label><label class="form-label" style="color: red">*:</label>
                        <input type="number" name="ag_gestas" min="0" max="20"   id="ag_gestas" class="form-control" disabled  value="<?php echo $rowcita['ag_gestas']; ?>" >                        
                    </div>
                    
                    <div class="col-md-1 m-l">
                        <label for="ag_partos" class="form-label">PARTOS:</label>
                        <input type="number" name="ag_partos" min="0" max="10"   id="ag_partos" class="form-control"value="<?php echo $rowcita['ag_partos']; ?>" required>                        
                    </div>

                    <div class="col-md-1 m-l">
                        <label for="ag_cesarias" class="form-label">CESÁREAS:</label>
                        <input type="number" name="ag_cesarias" min="0" max="10"   id="ag_cesarias" class="form-control"value="<?php echo $rowcita['ag_cesarias']; ?>" required>                        
                    </div>

                </div>
                </div>

                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-start text-start "> 

                    <div class="col-md-1 m-l">
                        <label for="ag_abortos" class="form-label">ABORTOS:</label>
                        <input type="number" name="ag_abortos" min="0" max="10"  id="ag_abortos" class="form-control"value="<?php echo $rowcita['ag_abortos']; ?>" required>                        
                    </div>

                    <div class="col-md-1 m-l">
                        <label for="ag_ectopico" class="form-label">ECTÓPICO:</label>
                        <input type="number" name="ag_ectopico" min="0" max="10" id="ag_ectopico" class="form-control"value="<?php echo $rowcita['ag_ectopico']; ?>" required>                        
                    </div>

                    <div class="col-md-1 m-l">
                        <label for="ag_mola" class="form-label">MOLA:</label>
                        <input type="number" name="ag_mola" id="ag_mola" min="0" max="10" class="form-control"value="<?php echo $rowcita['ag_mola']; ?>" >                        
                    </div>

                    <div class="col-md-3 m-l">
                        <label for="ag_emb_mul" class="form-label">EMBARAZOS MÚLTIPLES:</label>
                        <input type="number" name="ag_emb_mul" id="ag_emb_mul" min="0" max="10" class="form-control"value="<?php echo $rowcita['ag_emb_mul']; ?>" >                        
                    </div>

                    <div class="col-md-3 m-l">
                        <label for="ag_hijos_nac_viv" class="form-label">HIJOS NACIDOS VIVOS:</label>
                        <input type="number" name="ag_hijos_nac_viv" id="ag_hijos_nac_viv" min="0" max="10" class="form-control"value="<?php echo $rowcita['ag_hijos_nac_viv']; ?>" >                        
                    </div>

                    <div class="col-md-3 m-l">
                        <label for="ag_ag_hijos_nac_mue" class="form-label">HIJOS NACIDOS MUERTOS:</label>
                        <input type="number" name="ag_ag_hijos_nac_mue" id="ag_ag_hijos_nac_mue" min="0" max="10" class="form-control"value="<?php echo $rowcita['ag_ag_hijos_nac_mue']; ?>" >                        
                    </div>

                </div>
                </div>

                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-start text-start "> 

                    <div class="col-md-4 m-l">
                        <label for="ag_resol_ult_emba" class="form-label">RESOLUCIÓN DEL ÚLTIMO EMBARAZO:</label>
                        <select name="ag_resol_ult_emba" id="ag_resol_ult_emba" class="form-select" >
                            <option value="">Seleccionar...</option>
                            <?php while ($rowUemb = $resultadoUemb->fetch_assoc()) { ?>
                            <option value="<?php echo $rowUemb["id_res_ult_emb"]; ?>"<?php if($rowUemb['id_res_ult_emb']==$rue_concepto) { echo 'selected'; } ?>><?php echo $rowUemb['rue_concepto']; ?></option>
                            <?php } ?> 
                        </select>
                    </div> 

                    <div class="col-md-4 m-l">
                        <label for="ag_resol_fecha_ul_emb" class="form-label" value="<?php echo $rowcita['ag_resol_fecha_ul_emb']; ?>">FECHA RESOLUCIÓN DEL ÚLTIMO EMBARAZO:</label>
                        <input type="date" name="ag_resol_fecha_ul_emb" id="ag_resol_fecha_ul_emb" class="form-control" value="<?php echo $rowcita['ag_resol_fecha_ul_emb']; ?>" >
                    </div>

                    <div class="col-md-4 m-l">
                        <label for="ag_uso_prev_antico_tipo" class="form-label">USO PREVIO DE ANTICONCEPTIVOS TIPO</label><label class="form-label" style="color: red">*:</label>
                        <select name="ag_uso_prev_antico_tipo" id="ag_uso_prev_antico_tipo" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <?php while ($rowAnti = $resultadoAnti->fetch_assoc()) { ?>
                            <option value="<?php echo $rowAnti["id_anticonceptivos"]; ?>"<?php if($rowAnti['id_anticonceptivos']==$anti_concepto) { echo 'selected'; } ?>><?php echo $rowAnti['anti_concepto']; ?></option>
                            <?php } ?> 
                        </select>
                    </div> 

                </div>
                </div>  

                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-start text-start ">     
                    
                    <div class="col-md-4 m-l">
                        <label for="ag_uso_prev_antico_tiempo_uso" class="form-label">TIEMPO DE USO:</label>
                        <input  type="text" name="ag_uso_prev_antico_tiempo_uso" id="ag_uso_prev_antico_tiempo_uso"  class="form-control" disabled placeholder="Ej.(8 M = 8 meses o con 2 A = 2 años)" value="<?php echo $rowcita['ag_uso_prev_antico_tiempo_uso']; ?>" >                        
                    </div>

                    <div class="col-md-4 m-l">
                        <label for="ag_uso_prev_fecha_suspe" class="form-label" >FECHA DE SUSPENSIÓN:</label>
                        <input type="date" name="ag_uso_prev_fecha_suspe" id="ag_uso_prev_fecha_suspe" class="form-control" disabled value="<?php echo $rowcita['ag_uso_prev_fecha_suspe']; ?>" >
                    </div>

                    <div class="col-md-4 m-l">
                    <label for="ag_uso_prev_falla" class="form-label">FALLA DEL MÉTODO ANTICONCEPTIVO:</label>
                    <select name="ag_uso_prev_falla" id="ag_uso_prev_falla" class="form-select" disabled >
                        <option value="">Seleccionar...</option>
                        <option value="SI"<?php if('SI' == $rowcita['ag_uso_prev_falla']) { echo 'selected'; } ?>>SI</option>
                        <option value="NO"<?php if('NO' == $rowcita['ag_uso_prev_falla']) { echo 'selected'; } ?>>NO</option> 
                    </select>
                    </div> 
                
                </div>
                </div>  
                
                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-center text-start ">        

                    <div class="col-md-4 m-l">
                    <label for="ag_otro_ante_ginecoobs" class="form-label">OTRO ANTECEDENTE GINECOOBSTÉTRICO:</label>
                    <select name="ag_otro_ante_ginecoobs" id="ag_otro_ante_ginecoobs" class="form-select" >
                        <option value="">Seleccionar...</option>
                        <option value="SI"<?php if('SI' == $rowcita['ag_otro_ante_ginecoobs']) { echo 'selected'; } ?>>SI</option>
                        <option value="NO"<?php if('NO' == $rowcita['ag_otro_ante_ginecoobs']) { echo 'selected'; } ?>>NO</option> 
                    </select>
                    </div>  

                    <div class="col-md-4 m-l">
                        <label for="ag_otro_ante_ginecoobs_esp" class="form-label">ESPECIFIQUE:</label>
                        <select name="ag_otro_ante_ginecoobs_esp" id="ag_otro_ante_ginecoobs_esp" class="form-select"disabled >
                            <option value="">Seleccionar...</option>
                            <?php while ($rowAgi = $resultadoAgi->fetch_assoc()) { ?>
                            <option value="<?php echo $rowAgi["id_ant_gineco_otro"]; ?>"<?php if($rowAgi['id_ant_gineco_otro']==$agineco_concepto) { echo 'selected'; } ?>><?php echo $rowAgi['agineco_concepto']; ?></option>
                            <?php } ?> 
                        </select>
                    </div>  

                </div>
                </div>
        <!----FIN---->  

        <!----INICIO---->
        <center> <div class="bg-dark p-2 text-danger bg-opacity-90 modal-title fs-5">APLICACIÓN DE BIOLÓGICOS</div></center>
                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-start text-start ">

                    <div class="col-md-4 m-l">
                        <div class="input-group mb-3">
                        <span class="input-group-text">TD PRIMERA:</span>
                        <label for="bio_fecha_td_primera" class="form-label" value="<?php echo $rowcita['bio_fecha_td_primera']; ?>"></label>
                        <input type="date" name="bio_fecha_td_primera" id="bio_fecha_td_primera" class="form-control" value="<?php echo $rowcita['bio_fecha_td_primera']; ?>" >
                        </div>
                    </div>

                    <div class="col-md-4 m-l">
                        <div class="input-group mb-3">
                        <span class="input-group-text">TD SEGUNDA:</span>
                        <label for="bio_fecha_td_segunda" class="form-label" value="<?php echo $rowcita['bio_fecha_td_segunda']; ?>"></label>
                        <input type="date" name="bio_fecha_td_segunda" id="bio_fecha_td_segunda" class="form-control" value="<?php echo $rowcita['bio_fecha_td_segunda']; ?>" >
                        </div>
                    </div>

                    <div class="col-md-4 m-l">
                        <div class="input-group mb-3">
                        <span class="input-group-text">TD TERCERA:</span>
                        <label for="bio_fecha_td_tercera" class="form-label" value="<?php echo $rowcita['bio_fecha_td_tercera']; ?>"></label>
                        <input type="date" name="bio_fecha_td_tercera" id="bio_fecha_td_tercera" class="form-control" value="<?php echo $rowcita['bio_fecha_td_tercera']; ?>" >
                        </div>
                    </div>

                </div>	
                </div> 

                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-start text-start ">

                    <div class="col-md-4 m-l">
                        <div class="input-group mb-3">
                        <span class="input-group-text">Tdpa:</span>
                        <label for="bio_fecha_tdpa" class="form-label" value="<?php echo $rowcita['bio_fecha_tdpa']; ?>"></label>
                        <input type="date" name="bio_fecha_tdpa" id="bio_fecha_tdpa" class="form-control" value="<?php echo $rowcita['bio_fecha_tdpa']; ?>" >
                        </div>
                    </div>

                    <div class="col-md-4 m-l">
                        <div class="input-group mb-3">
                        <span class="input-group-text">INFLUENZA:</span>
                        <label for="bio_fecha_influenza" class="form-label" value="<?php echo $rowcita['bio_fecha_influenza']; ?>"></label>
                        <input type="date" name="bio_fecha_influenza" id="bio_fecha_influenza" class="form-control" value="<?php echo $rowcita['bio_fecha_influenza']; ?>" >
                        </div>
                    </div>

                    <div class="col-md-4 m-l">
                        <div class="input-group mb-3">
                        <span class="input-group-text">ANTICOVID-19:</span>
                        <label for="bio_fecha_covid19" class="form-label" value="<?php echo $rowcita['bio_fecha_covid19']; ?>"></label>
                        <input type="date" name="bio_fecha_covid19" id="bio_fecha_covid19" class="form-control" value="<?php echo $rowcita['bio_fecha_covid19']; ?>" >
                        </div>
                    </div>
                    
                </div>	
                </div>                      
        <!----FIN---->   

        <!----INICIO---->
        <center> <div class="bg-danger p-2 text-white bg-opacity-90 modal-title fs-5">BAJA DE EMBARAZO</div></center>
        <div class="container-fluid p-2 text-center">
        <div class="row justify-content-center text-start ">
                
            <div class="col-md-3 m-l">
            <div class="input-group mb-3">
                <span class="input-group-text">FECHA:</span>
                <label for="fecha_baja_emb" class="form-label"  ></label>
                <input type="date" name="fecha_baja_emb" id="fecha_baja_emb" class="form-control" disabled value="<?php echo $rowcita['fecha_baja_emb']; ?>" >
            </div>
            </div>	

            <div class="col-md-4 m-l">
            <div class="input-group mb-3">
                <span class="input-group-text">MOTIVO:</span>
                <select name="motivo_baja_emb" id="motivo_baja_emb" class="form-select" disabled>
                    <option value="">Seleccionar...</option>
                    <?php while ($rowBE = $resultadoBE->fetch_assoc()) { ?>
                    <option value="<?php echo $rowBE["id_baja_embarazo"]; ?>"<?php if($rowBE['id_baja_embarazo']==$be_concepto) { echo 'selected'; } ?>><?php echo $rowBE['be_concepto']; ?></option>
                    <?php } ?> 
                </select>
            </div>
            </div>
                   
        </div>	
        </div> 
        <!----FIN---->  

        <div class="container-fluid p-2 text-center">
        <div class="row justify-content-center text-center ">
            <div cclass="col-md-3 m-l">
            <button type="submit" class="btn btn-outline-primary" id="enviar" name="enviar" ><i class="fa-solid fa-floppy-disk"></i> Guardar</button>
            </div>
        </div>
        </div>	


    </form>

    <script src="assets/js/bootstrap.bundle.min.js"></script>

    <?php
    $sqlListabaja = "SELECT id_baja_embarazo, be_concepto FROM baja_embarazo ORDER BY id_baja_embarazo";
    $listabaja = $mysqli->query($sqlListabaja);
    ?>

    <?php include 'editaModalBajaemb.php'; ?>

    <?php $listabaja->data_seek(0); ?>

<script>

    let editaModal = document.getElementById('editaModalBajaemb')

        editaModal.addEventListener('hide.bs.modal', event => {
        editaModal.querySelector('.modal-body #fecha_baja_emb').value = ""
        editaModal.querySelector('.modal-body #motivo_baja_emb').value = ""                   
    })

        editaModal.addEventListener('shown.bs.modal', event => {
        let button = event.relatedTarget
        let id = button.getAttribute('data-bs-id')

        let inputId = editaModal.querySelector('.modal-body #id_tarjeta')
        let inputFecha_baja_emb= editaModal.querySelector('.modal-body #fecha_baja_emb')
        let inputMotivo_baja_emb = editaModal.querySelector('.modal-body #motivo_baja_emb')
        let url = "getMotivoBaja.php"
        let formData = new FormData()
        formData.append('id_tarjeta', id)

        fetch(url, {
                method: "POST",
                body: formData
            }).then(response => response.json())
            .then(data => {

                inputId.value = data.id_tarjeta                    
                inputFecha_baja_emb.value = data.fecha_baja_emb
                inputMotivo_baja_emb.value = data.motivo_baja_emb
                
            }).catch(err => console.log(err))

    })
</script> 

<script>
 

</script>

<!--Inicio validando Factores de riesgo -->
    <script>
    //CAMBIANDO EL COLOR DEL SEMAFORO EDAD
        $(document).ready(function () {
            $('#edad').ready(function (e) {
                $edad = $('#edad').val();     
                if( $edad >=16  && $edad <=39 ){
                    v_edad()
                }else{
                    a_edad()
                }
            })
        }); 

    //CAMBIANDO EL COLOR DEL SEMAFORO TALLA
        $(document).ready(function () {
            $('#talla').ready(function (e) {
                $talla = $('#talla').val();     
                if( $talla >=1.46 ){
                    v_talla()
                }else{
                    a_talla()
                }
            })
        }); 

    //CAMBIANDO EL COLOR DEL SEMAFORO IMC
        $(document).ready(function () {
            $('#imc').ready(function (e) {
                $imc = $('#imc').val();     
                if(  $imc >=18.5  && $imc <=29.9 ){
                    v_imc()
                }else{
                    a_imc()
                }
            })
        })    

    //CAMBIANDO EL COLOR DEL SEMAFORO RH NEGATIVO
        $(document).ready(function () {
                $('#app_rh').ready(function (e) {
                    $app_rh = $('#app_rh').val();     
                    if( $app_rh === "NEGATIVO" ){
                        v_app_rh()
                    }else{
                        a_app_rh()
                    }
                })
            })  
            
    //CAMBIANDO EL COLOR DEL SEMAFORO HIPERTENSIÓN ARTERIAL NO 
    $(document).ready(function () {
                $('#app_hipertencio_arterial').ready(function (e) {
                    $app_hipertencio_arterial = $('#app_hipertencio_arterial').val();     
                    if( $app_hipertencio_arterial === "NO" ){
                        v_app_hipertencio_arterial()
                    }else{
                        a_app_hipertencio_arterial()
                    }
                })
            })  

    //CAMBIANDO EL COLOR DEL SEMAFORO NEFROPATIA NO 
    $(document).ready(function () {
                $('#app_nefropatia').ready(function (e) {
                    $app_nefropatia = $('#app_nefropatia').val();     
                    if( $app_nefropatia === "NO" ){
                        v_app_nefropatia()
                    }else{
                        a_app_nefropatia()
                    }
                })
            }) 

    //CAMBIANDO EL COLOR DEL SEMAFORO CARDIOPATIA NO 
    $(document).ready(function () {
                $('#app_cardiopatia').ready(function (e) {
                    $app_cardiopatia = $('#app_cardiopatia').val();     
                    if( $app_cardiopatia === "NO" ){
                        v_app_cardiopatia()
                    }else{
                        a_app_cardiopatia()
                    }
                })
            }) 

    //CAMBIANDO EL COLOR DEL SEMAFORO DEBATESGESTACIONAL NO 
    $(document).ready(function () {
                $('#app_diabetes_geostacional').ready(function (e) {
                    $app_diabetes_geostacional = $('#app_diabetes_geostacional').val();     
                    if( $app_diabetes_geostacional === "NO" ){
                        v_app_diabetes_geostacional()
                    }else{
                        a_app_diabetes_geostacional()
                    }
                })
            }) 

    //CAMBIANDO EL COLOR DEL SEMAFORO PREECLAMPSIA/ECLAMPSIA NO 
    $(document).ready(function () {
                $('#app_preeclampsia_enclampsia').ready(function (e) {
                    $app_preeclampsia_enclampsia = $('#app_preeclampsia_enclampsia').val();     
                    if( $app_preeclampsia_enclampsia === "NO" ){
                        v_app_preeclampsia_enclampsia()
                    }else{
                        a_app_preeclampsia_enclampsia()
                    }
                })
            }) 

    //CAMBIANDO EL COLOR DEL SEMAFORO HEMORRAGIA OBSTETRICA NO 
    $(document).ready(function () {
                $('#app_hemorragia_obstetrica').ready(function (e) {
                    $app_hemorragia_obstetrica = $('#app_hemorragia_obstetrica').val();     
                    if( $app_hemorragia_obstetrica === "NO" ){
                        v_app_hemorragia_obstetrica()
                    }else{
                        a_app_hemorragia_obstetrica()
                    }
                })
            }) 

    //CAMBIANDO EL COLOR DEL SEMAFORO B24X NO
    $(document).ready(function () {
                $('#app_b24x').ready(function (e) {
                    $app_b24x = $('#app_b24x').val();     
                    if( $app_b24x === "NO" ){
                        v_app_b24x()
                    }else{
                        a_app_b24x()
                    }
                })
            }) 

    //CAMBIANDO EL COLOR DEL SEMAFORO A539 NO 
    $(document).ready(function () {
                $('#app_a539').ready(function (e) {
                    $app_a539 = $('#app_a539').val();     
                    if( $app_a539 === "NO" ){
                        v_app_a539()
                    }else{
                        a_app_a539()
                    }
                })
            }) 

    //CAMBIANDO EL COLOR DEL SEMAFORO TUBERCULOSIS NO 
    $(document).ready(function () {
                $('#app_tuberculosis').ready(function (e) {
                    $app_tuberculosis = $('#app_tuberculosis').val();     
                    if( $app_tuberculosis === "NO" ){
                        v_app_tuberculosis()
                    }else{
                        a_app_tuberculosis()
                    }
                })
            })    

    //CAMBIANDO EL COLOR DEL SEMAFORO OTRO ANTECEDENTE 0 
    $(document).ready(function () {
                $('#app_otro_antecedente').ready(function (e) {
                    $app_otro_antecedente = $('#app_otro_antecedente').val();     
                    if( $app_otro_antecedente === "0" ){
                        v_app_otro_antecedente()
                    }else{
                        a_app_otro_antecedente()
                    }
                })
            }) 
           
    //CAMBIANDO EL COLOR DEL SEMAFORO ADICCION AL TABACO
    $(document).ready(function () {
                $('#adic_tabaco').ready(function (e) {
                    $adic_tabaco = $('#adic_tabaco').val();     
                    if( $adic_tabaco === "NO" ){
                        v_adic_tabaco()
                    }else{
                        a_adic_tabaco()
                    }
                })
            }) 

    //CAMBIANDO EL COLOR DEL SEMAFORO ADICCION AL ANTIDREPRESIVOS
    $(document).ready(function () {
                $('#adic_antidepre').ready(function (e) {
                    $adic_antidepre = $('#adic_antidepre').val();     
                    if( $adic_antidepre === "NO" ){
                        v_adic_antidepre()
                    }else{
                        a_adic_antidepre()
                    }
                })
            }) 

    //CAMBIANDO EL COLOR DEL SEMAFORO ADICCION AL ALCOHOL
    $(document).ready(function () {
                $('#adic_alcohol').ready(function (e) {
                    $adic_alcohol = $('#adic_alcohol').val();     
                    if( $adic_alcohol === "NO" ){
                        v_adic_alcohol()
                    }else{
                        a_adic_alcohol()
                    }
                })
            }) 

    //CAMBIANDO EL COLOR DEL SEMAFORO ADICCION AL ANSIOLITICOS
    $(document).ready(function () {
                $('#adic_ansioliticos').ready(function (e) {
                    $adic_ansioliticos = $('#adic_ansioliticos').val();     
                    if( $adic_ansioliticos === "NO" ){
                        v_adic_ansioliticos()
                    }else{
                        a_adic_ansioliticos()
                    }
                })
            }) 

    //CAMBIANDO EL COLOR DEL SEMAFORO GESTAS
    $(document).ready(function () {
                $('#ag_gestas').ready(function (e) {
                    $ag_gestas = $('#ag_gestas').val();     
                    if( $ag_gestas >=0 && $ag_gestas <=3 ){
                        v_ag_gestas()
                    }else{
                        a_ag_gestas()
                    }
                })
            })   
           
    //CAMBIANDO EL COLOR DEL SEMAFORO PARTOS
    $(document).ready(function () {
                $('#ag_partos').ready(function (e) {
                    $ag_partos = $('#ag_partos').val();     
                    if( $ag_partos >=0 && $ag_partos <=3 ){
                        v_ag_partos()
                    }else{
                        a_ag_partos()
                    }
                })
            })   

    //CAMBIANDO EL COLOR DEL SEMAFORO CESAREAS
    $(document).ready(function () {
                $('#ag_cesarias').ready(function (e) {
                    $ag_cesarias = $('#ag_cesarias').val();     
                    if( $ag_cesarias >=0 && $ag_cesarias <=1 ){
                        v_ag_cesarias()
                    }else{
                        a_ag_cesarias()
                    }
                })
            }) 
            
    //CAMBIANDO EL COLOR DEL SEMAFORO ABORTOS
    $(document).ready(function () {
                $('#ag_abortos').ready(function (e) {
                    $ag_abortos = $('#ag_abortos').val();     
                    if( $ag_abortos >=0 && $ag_abortos <=1 ){
                        v_ag_abortos()
                    }else{
                        a_ag_abortos()
                    }
                })
            })   
            
    //CAMBIANDO EL COLOR DEL SEMAFORO NACIDOS MUERTOS ÓBITOS
    $(document).ready(function () {
                $('#ag_ag_hijos_nac_mue').ready(function (e) {
                    $ag_ag_hijos_nac_mue = $('#ag_ag_hijos_nac_mue').val();     
                    if( $ag_ag_hijos_nac_mue <1){
                        v_ag_ag_hijos_nac_mue()
                    }else{
                        a_ag_ag_hijos_nac_mue()
                    }
                })
            })  
            
    //CAMBIANDO EL COLOR DEL SEMAFORO OTROS ANTECEDENTES
    $(document).ready(function () {
                $('#ag_otro_ante_ginecoobs_esp').ready(function (e) {
                    $ag_otro_ante_ginecoobs_esp = $('#ag_otro_ante_ginecoobs_esp').val();     
                    if( $ag_otro_ante_ginecoobs_esp === '0' ){
                        $('#ag_otro_ante_ginecoobs_esp').prop("disabled", true);
                        v_ag_otro_ante_ginecoobs_esp()
                    }else{
                        $('#ag_otro_ante_ginecoobs_esp').prop("disabled", false ); 
                        a_ag_otro_ante_ginecoobs_esp()
                    }
                })
            })     
            
    //CAMBIANDO EL COLOR DEL SEMAFORO OTROS GLUCOSA EN AYUNO
    $(document).ready(function () {
                $('#cdg_ayuno').ready(function (e) {
                    $cdg_ayuno = $('#cdg_ayuno').val();    
                    if( $cdg_ayuno >= '200' ){
                        r_cdg_ayuno();
                    }else if ( $cdg_ayuno >= '126'&& $cdg_ayuno <= '199' ){
                        a_cdg_ayuno();
                    }else{
                        v_cdg_ayuno();
                    }
                })
            })              

    //CAMBIANDO EL COLOR DEL SEMAFORO RH NEGATIVO
    $(document).ready(function () {
                $('#app_rh').change(function (e) {
                    $app_rh = $('#app_rh').val();     
                    if( $app_rh === "NEGATIVO" ){
                        v_app_rh()
                    }else{
                        a_app_rh()
                    }
                })
            })  
</script><!--Fin validando Factores de riesgo -->

    <script>
        //Codigo para limitar la cantidad maxima que tendra dicho Input
        $('input#curp_pre_ser').keypress(function (event) {
        // if (event.which < 48 || event.which > 57 || this.value.length === 18) {
        if (this.value.length === 18) {
        return false;
        }
        });
    </script>

    <script>
        $(document).ready(function () {
            $('#escolaridad').change(function (e) {
                if($(this).val()==="0"){
                    $('#tipo').val('0'); 
                    $('#expediente').focus();
                }else{
                    $('#tipo').focus();
                }

            })

        });
    </script>


    <script>      
    
        $("#ag_partos").on("mousedown", function() { //CAPTURANDO EL VALOR DE INPUT  ag_gestas ag_partos ag_cesarias ag_abortos ag_ectopico
        var partos   = $("#ag_partos").val();
        var cesarias = $("#ag_cesarias").val();
        var abortos  = $("#ag_abortos").val();
        var ectopico = $("#ag_ectopico").val();
        var total = parseInt(partos)+parseInt(cesarias)+parseInt(abortos)+parseInt(ectopico);  
                    if( partos >=0 && partos <=3 ){
                        v_ag_partos();
                    }else{
                        a_ag_partos();
                    }
        $('#ag_gestas').val(total); 
                    if( total >=0 && total <=3 ){
                        v_ag_gestas();
                    }else{
                        a_ag_gestas();
                    }
        if (total===0){
            $('#ag_resol_ult_emba').val('0'); 
            $('#ag_resol_fecha_ul_emb').val(''); 
            $('#ag_resol_ult_emba').prop("required", false);
            $('#ag_resol_fecha_ul_emb').prop("required", false);  
        }else{
            $('#ag_resol_ult_emba').val('');
            $('#ag_resol_ult_emba').prop("required", true);
            $('#ag_resol_fecha_ul_emb').prop("required", true);
        }
        });

        $("#ag_cesarias").on("mousedown", function() { //CAPTURANDO EL VALOR DE INPUT  ag_gestas ag_partos ag_cesarias ag_abortos ag_ectopico
        var partos   = $("#ag_partos").val();
        var cesarias = $("#ag_cesarias").val();
        var abortos  = $("#ag_abortos").val();
        var ectopico = $("#ag_ectopico").val();
        var total = parseInt(partos)+parseInt(cesarias)+parseInt(abortos)+parseInt(ectopico);
                    if( cesarias >=0 && cesarias <=1 ){
                        v_ag_cesarias();
                    }else{
                        a_ag_cesarias();
                    }
        $('#ag_gestas').val(total);
  
                    if( total >=0 && total <=3 ){
                        v_ag_gestas();
                    }else{
                        a_ag_gestas();
                    }
        if (total===0){
            $('#ag_resol_ult_emba').val('0'); 
            $('#ag_resol_fecha_ul_emb').val(''); 
            $('#ag_resol_ult_emba').prop("required", false);
            $('#ag_resol_fecha_ul_emb').prop("required", false); 
        }else{
            $('#ag_resol_ult_emba').val('');
            $('#ag_resol_ult_emba').prop("required", true);
            $('#ag_resol_fecha_ul_emb').prop("required", true);
        }
        });

        $("#ag_abortos").on("mousedown", function() { //CAPTURANDO EL VALOR DE INPUT  ag_gestas ag_partos ag_cesarias ag_abortos ag_ectopico
        var partos   = $("#ag_partos").val();
        var cesarias = $("#ag_cesarias").val();
        var abortos  = $("#ag_abortos").val();
        var ectopico = $("#ag_ectopico").val();
        var total = parseInt(partos)+parseInt(cesarias)+parseInt(abortos)+parseInt(ectopico);
                    if( abortos >=0 && abortos <=1 ){
                        v_ag_abortos();
                    }else{
                        a_ag_abortos();
                    }
        $('#ag_gestas').val(total);
    
                    if( total >=0 && total <=3 ){
                        v_ag_gestas();
                    }else{
                        a_ag_gestas();
                    }
        if (total===0){
            $('#ag_resol_ult_emba').val('0'); 
            $('#ag_resol_fecha_ul_emb').val(''); 
            $('#ag_resol_ult_emba').prop("required", false);
            $('#ag_resol_fecha_ul_emb').prop("required", false); 
        }else{
            $('#ag_resol_ult_emba').val('');
            $('#ag_resol_ult_emba').prop("required", true);
            $('#ag_resol_fecha_ul_emb').prop("required", true);
        }
        });

        $("#ag_ectopico").on("mousedown", function() { //CAPTURANDO EL VALOR DE INPUT  ag_gestas ag_partos ag_cesarias ag_abortos ag_ectopico
        var partos   = $("#ag_partos").val();
        var cesarias = $("#ag_cesarias").val();
        var abortos  = $("#ag_abortos").val();
        var ectopico = $("#ag_ectopico").val();
        var total = parseInt(partos)+parseInt(cesarias)+parseInt(abortos)+parseInt(ectopico);
        $('#ag_gestas').val(total);
    
                    if( total >=0 && total <=3 ){
                        v_ag_gestas();
                    }else{
                        a_ag_gestas();
                    }
        if (total===0){
            $('#ag_resol_ult_emba').val('0'); 
            $('#ag_resol_fecha_ul_emb').val(''); 
            $('#ag_resol_ult_emba').prop("required", false);
            $('#ag_resol_fecha_ul_emb').prop("required", false); 
        }else{
            $('#ag_resol_ult_emba').val('');
            $('#ag_resol_ult_emba').prop("required", true);
            $('#ag_resol_fecha_ul_emb').prop("required", true);
        }
        });

        $("#ag_ag_hijos_nac_mue").on("mousedown", function() { //CAPTURANDO EL VALOR DE INPUT  NACIDOS MUERTOS
        var ag_ag_hijos_nac_mue   = $("#ag_ag_hijos_nac_mue").val();    
        if( ag_ag_hijos_nac_mue <1 ){
                        v_ag_ag_hijos_nac_mue();
                    }else{
                        a_ag_ag_hijos_nac_mue();
                    }
        });
    </script>

    <script>      
    
        $("#ag_partos").on("mouseup", function() { //CAPTURANDO EL VALOR DE INPUT  ag_gestas ag_partos ag_cesarias ag_abortos ag_ectopico
        var partos   = $("#ag_partos").val();
        var cesarias = $("#ag_cesarias").val();
        var abortos  = $("#ag_abortos").val();
        var ectopico = $("#ag_ectopico").val();
        var total = parseInt(partos)+parseInt(cesarias)+parseInt(abortos)+parseInt(ectopico);
                    if( partos >=0 && partos <=3 ){
                        v_ag_partos();
                    }else{
                        a_ag_partos();
                    }
        $('#ag_gestas').val(total);
    
                    if( total >=0 && total <=3 ){
                        v_ag_gestas();
                    }else{
                        a_ag_gestas();
                    }
        if (total===0){
            $('#ag_resol_ult_emba').val('0'); 
            $('#ag_resol_fecha_ul_emb').val(''); 
            $('#ag_resol_ult_emba').prop("required", false);
            $('#ag_resol_fecha_ul_emb').prop("required", false); 
        }else{
            $('#ag_resol_ult_emba').val('');
            $('#ag_resol_ult_emba').prop("required", true);
            $('#ag_resol_fecha_ul_emb').prop("required", true);
        } 
        });

        $("#ag_cesarias").on("mouseup", function() { //CAPTURANDO EL VALOR DE INPUT  ag_gestas ag_partos ag_cesarias ag_abortos ag_ectopico
        var partos   = $("#ag_partos").val();
        var cesarias = $("#ag_cesarias").val();
        var abortos  = $("#ag_abortos").val();
        var ectopico = $("#ag_ectopico").val();
        var total = parseInt(partos)+parseInt(cesarias)+parseInt(abortos)+parseInt(ectopico);
                    if( cesarias >=0 && cesarias <=1 ){
                        v_ag_cesarias();
                    }else{
                        a_ag_cesarias();
                    }
        $('#ag_gestas').val(total);
    
                    if( total >=0 && total <=3 ){
                        v_ag_gestas();
                    }else{
                        a_ag_gestas();
                    }
        if (total===0){
            $('#ag_resol_ult_emba').val('0'); 
            $('#ag_resol_fecha_ul_emb').val(''); 
            $('#ag_resol_ult_emba').prop("required", false);
            $('#ag_resol_fecha_ul_emb').prop("required", false); 
        }else{
            $('#ag_resol_ult_emba').val('');
            $('#ag_resol_ult_emba').prop("required", true);
            $('#ag_resol_fecha_ul_emb').prop("required", true);
        }
        });

        $("#ag_abortos").on("mouseup", function() { //CAPTURANDO EL VALOR DE INPUT  ag_gestas ag_partos ag_cesarias ag_abortos ag_ectopico
        var partos   = $("#ag_partos").val();
        var cesarias = $("#ag_cesarias").val();
        var abortos  = $("#ag_abortos").val();
        var ectopico = $("#ag_ectopico").val();
        var total = parseInt(partos)+parseInt(cesarias)+parseInt(abortos)+parseInt(ectopico);
                    if( abortos >=0 && abortos <=1 ){
                        v_ag_abortos();
                    }else{
                        a_ag_abortos();
                    }
        $('#ag_gestas').val(total);
                    if( total >=0 && total <=3 ){
                        v_ag_gestas();
                    }else{
                        a_ag_gestas();
                    }
        if (total===0){
            $('#ag_resol_ult_emba').val('0'); 
            $('#ag_resol_fecha_ul_emb').val(''); 
            $('#ag_resol_ult_emba').prop("required", false);
            $('#ag_resol_fecha_ul_emb').prop("required", false); 
        }else{
            $('#ag_resol_ult_emba').val('');
            $('#ag_resol_ult_emba').prop("required", true);
            $('#ag_resol_fecha_ul_emb').prop("required", true);
        }
        });

        $("#ag_ectopico").on("mouseup", function() { //CAPTURANDO EL VALOR DE INPUT  ag_gestas ag_partos ag_cesarias ag_abortos ag_ectopico
        var partos   = $("#ag_partos").val();
        var cesarias = $("#ag_cesarias").val();
        var abortos  = $("#ag_abortos").val();
        var ectopico = $("#ag_ectopico").val();
        var total = parseInt(partos)+parseInt(cesarias)+parseInt(abortos)+parseInt(ectopico);
        $('#ag_gestas').val(total);
   
                    if( total >=0 && total <=3 ){
                        v_ag_gestas();
                    }else{
                        a_ag_gestas();
                    }
        if (total===0){
            $('#ag_resol_ult_emba').val('0'); 
            $('#ag_resol_fecha_ul_emb').val(''); 
            $('#ag_resol_ult_emba').prop("required", false);
            $('#ag_resol_fecha_ul_emb').prop("required", false); 
        }else{
            $('#ag_resol_ult_emba').val('');
            $('#ag_resol_ult_emba').prop("required", true);
            $('#ag_resol_fecha_ul_emb').prop("required", true);
        }
        });

        $("#ag_ag_hijos_nac_mue").on("mouseup", function() { //CAPTURANDO EL VALOR DE INPUT  NACIDOS MUERTOS
        var ag_ag_hijos_nac_mue   = $("#ag_ag_hijos_nac_mue").val();    
        if( ag_ag_hijos_nac_mue <1 ){
                        v_ag_ag_hijos_nac_mue();
                    }else{
                        a_ag_ag_hijos_nac_mue();
                    }
        });
    </script>
    <script>      
  
        $("#ag_partos").on("keyup", function() { //CAPTURANDO EL VALOR DE INPUT  ag_gestas ag_partos ag_cesarias ag_abortos ag_ectopico
         var partos   = $("#ag_partos").val();
         var cesarias = $("#ag_cesarias").val();
         var abortos  = $("#ag_abortos").val();
         var ectopico = $("#ag_ectopico").val();
         var total = parseInt(partos)+parseInt(cesarias)+parseInt(abortos)+parseInt(ectopico);
                    if( partos >=0 && partos <=3 ){
                        v_ag_partos();
                    }else{
                        a_ag_partos();
                    }
         $('#ag_gestas').val(total);
                    if( total >=0 && total <=3 ){
                        v_ag_gestas();
                    }else{
                        a_ag_gestas();
                    }
         if (total===0){
            $('#ag_resol_ult_emba').val(0); 
            $('#ag_resol_fecha_ul_emb').val(''); 
            $('#ag_resol_ult_emba').prop("required", false);
            $('#ag_resol_fecha_ul_emb').prop("required", false); 
        }else{
            $('#ag_resol_ult_emba').val('');
            $('#ag_resol_ult_emba').prop("required", true);
            $('#ag_resol_fecha_ul_emb').prop("required", true);
        } 
        });

        $("#ag_cesarias").on("keyup", function() { //CAPTURANDO EL VALOR DE INPUT  ag_gestas ag_partos ag_cesarias ag_abortos ag_ectopico
         var partos   = $("#ag_partos").val();
         var cesarias = $("#ag_cesarias").val();
         var abortos  = $("#ag_abortos").val();
         var ectopico = $("#ag_ectopico").val();
         var total = parseInt(partos)+parseInt(cesarias)+parseInt(abortos)+parseInt(ectopico);
                    if( cesarias >=0 && cesarias <=1 ){
                        v_ag_cesarias();
                    }else{
                        a_ag_cesarias();
                    }
         $('#ag_gestas').val(total);
   
                    if( total >=0 && total <=3 ){
                        v_ag_gestas();
                    }else{
                        a_ag_gestas();
                    }
         if (total===0){
            $('#ag_resol_ult_emba').val('0'); 
            $('#ag_resol_fecha_ul_emb').val(''); 
            $('#ag_resol_ult_emba').prop("required", false);
            $('#ag_resol_fecha_ul_emb').prop("required", false); 
        }else{
            $('#ag_resol_ult_emba').val('');
            $('#ag_resol_ult_emba').prop("required", true);
            $('#ag_resol_fecha_ul_emb').prop("required", true);
        }
        });

        $("#ag_abortos").on("keyup", function() { //CAPTURANDO EL VALOR DE INPUT  ag_gestas ag_partos ag_cesarias ag_abortos ag_ectopico
         var partos   = $("#ag_partos").val();
         var cesarias = $("#ag_cesarias").val();
         var abortos  = $("#ag_abortos").val();
         var ectopico = $("#ag_ectopico").val();
         var total = parseInt(partos)+parseInt(cesarias)+parseInt(abortos)+parseInt(ectopico);
                    if( abortos >=0 && abortos <=1 ){
                        v_ag_abortos();
                    }else{
                        a_ag_abortos();
                    }
         $('#ag_gestas').val(total);
   
                    if( total >=0 && total <=3 ){
                        v_ag_gestas();
                    }else{
                        a_ag_gestas();
                    }
         if (total===0){
            $('#ag_resol_ult_emba').val('0'); 
            $('#ag_resol_fecha_ul_emb').val(''); 
            $('#ag_resol_ult_emba').prop("required", false);
            $('#ag_resol_fecha_ul_emb').prop("required", false); 
        }else{
            $('#ag_resol_ult_emba').val('');
            $('#ag_resol_ult_emba').prop("required", true);
            $('#ag_resol_fecha_ul_emb').prop("required", true);
        }
        });

        $("#ag_ectopico").on("keyup", function() { //CAPTURANDO EL VALOR DE INPUT  ag_gestas ag_partos ag_cesarias ag_abortos ag_ectopico
         var partos   = $("#ag_partos").val();
         var cesarias = $("#ag_cesarias").val();
         var abortos  = $("#ag_abortos").val();
         var ectopico = $("#ag_ectopico").val();
         var total = parseInt(partos)+parseInt(cesarias)+parseInt(abortos)+parseInt(ectopico);
         $('#ag_gestas').val(total);
    
                    if( total >=0 && total <=3 ){
                        v_ag_gestas();
                    }else{
                        a_ag_gestas();
                    }
         if (total===0){
            $('#ag_resol_ult_emba').val('0'); 
            $('#ag_resol_fecha_ul_emb').val(''); 
            $('#ag_resol_ult_emba').prop("required", false);
            $('#ag_resol_fecha_ul_emb').prop("required", false); 
        }else{
            $('#ag_resol_ult_emba').val('');
            $('#ag_resol_ult_emba').prop("required", true);
            $('#ag_resol_fecha_ul_emb').prop("required", true);
        }
        });

        $("#ag_ag_hijos_nac_mue").on("keyup", function() { //CAPTURANDO EL VALOR DE INPUT  NACIDOS MUERTOS
        var ag_ag_hijos_nac_mue   = $("#ag_ag_hijos_nac_mue").val();    
        if( ag_ag_hijos_nac_mue <1 ){
                        v_ag_ag_hijos_nac_mue();
                    }else{
                        a_ag_ag_hijos_nac_mue();
                    }
        });
    </script>

    <script> 
        $(document).ready(function () {
         $('#migragrante').ready(function (e) {

            if ($(this).val() === "SI") {
                $('#nacional').prop("disabled", false);
                $('#nacional').val('');                       
            } else {
                $('#nacional').prop("disabled", true);  
                $('#nacional').val('NO'); 
                $('#internacional').val('NO');    
            }
           
            })
        });
    </script>

    <script> 
        $(document).ready(function () {
         $('#migragrante').change(function (e) {

            if ($(this).val() === "SI") {
                $('#nacional').prop("disabled", false);
                $('#nacional').val('');
                $('#nacional').focus();                          
            } else {
                $('#nacional').prop("disabled", true);  
                $('#nacional').val('NO'); 
                $('#internacional').val('NO');    
                $('#indigena').focus();  
            }
           
            })
        });
    
        $(document).ready(function () {
         $('#nacional').change(function (e) {

            if ($(this).val() === "NO") {
                $('#internacional').prop("disabled", true);
                $('#internacional').val('SI');
                $('#indigena').focus();                                             
            } else { 
                $('#internacional').prop("disabled", false);
                $('#internacional').val('NO');   
                $('#indigena').focus();  
            }
           
            })
        });

        $(document).ready(function () {
         $('#lengua_indigena').change(function (e) {

            if ($(this).val() === "SI") {
                $('#lengua_habla').prop("disabled", false);
                $('#lengua_habla').val(''); 
                $('#lengua_habla').focus();                                             
            } else { 
                $('#lengua_habla').prop("disabled", true);
                $('#lengua_habla').val('NINGUNA');  
                $('#habla_espanol').focus();  
            }
           
            })
        });

        //CAMBIANDO EL COLOR DEL SEMAFORO OTROS GLUCOSA EN AYUNO
            $(document).ready(function () {
                $('#cdg_ayuno').keyup(function (e) {
                    $cdg_ayuno = $('#cdg_ayuno').val();    
                    if( $cdg_ayuno >= 200 ){
                        r_cdg_ayuno();
                    }else if ( $cdg_ayuno >= '126'&& $cdg_ayuno <= '199' ){
                        a_cdg_ayuno();
                    }else{
                        v_cdg_ayuno();
                    }
                })
            })  

        //CAMBIANDO EL COLOR DEL SEMAFORO OTROS GLUCOSA EN AYUNO
            $(document).ready(function () {
                $('#cdg_ayuno').change(function (e) {
                    $cdg_ayuno = $('#cdg_ayuno').val();    
                    if( $cdg_ayuno >= 200 ){
                        r_cdg_ayuno();
                    }else if ( $cdg_ayuno >= '126'&& $cdg_ayuno <= '199' ){
                        a_cdg_ayuno();
                    }else{
                        v_cdg_ayuno();
                    }
                })
            })



        $("#talla").on("keyup", function() {
        var peso = $("#peso_emb").val(); //CAPTURANDO EL VALOR DE INPUT 
        var talla = $("#talla").val(); //CAPTURANDO EL VALOR DE INPUT 
        var talla2 = talla*talla;
        var imc_cal = (peso/talla2).toFixed(2);
        $('#imc').val(imc_cal);
        //SEMAFORO TALLA
        $talla = $('#talla').val();     
                if( $talla >= 1.46 ){
                    v_talla()
                }else{
                    a_talla()
                }

        //SEMAFORO IMC
        $imc = $('#imc').val();     
                if(  $imc >=18.5  && $imc <=29.9 ){
                    v_imc()
                }else{
                    a_imc()
                }

        });

        $(document).ready(function () {
         $('#ap_se_ignora').change(function (e) {

            if ($(this).val() === "NO") {
                $('#ap_ulima_mestruacion').prop("disabled", false);
                $('#ap_ulima_mestruacion').prop("required", true);
                $('#ap_confiable').prop("disabled", false);
                $('#ap_confiable').prop("required", true);
                $('#ap_ulima_mestruacion').val('');
                $('#ap_ulima_mestruacion').focus();                          
            } else {
                $('#ap_ulima_mestruacion').prop("disabled", true);  
                $('#ap_ulima_mestruacion').prop("required", false);
                $('#ap_confiable').prop("disabled", true);  
                $('#ap_confiable').prop("required", false);
                $('#ap_ulima_mestruacion').val(''); 
                $('#ap_confiable').val('');    
                $('#ap_fecha_ultrasonido_obstretico').focus();  
            }
           
            })
        });

        $(document).ready(function () {
         $('#ag_uso_prev_antico_tipo').change(function (e) {

            if ($(this).val() != "0") {
                $('#ag_uso_prev_antico_tiempo_uso').prop("disabled", false);
                $('#ag_uso_prev_fecha_suspe').prop("disabled", false);
                $('#ag_uso_prev_falla').prop("disabled", false);                
                $('#ag_uso_prev_antico_tiempo_uso').focus();                           
            } else {
                $('#ag_uso_prev_antico_tiempo_uso').prop("disabled", true);
                $('#ag_uso_prev_antico_tiempo_uso').val('');  
                $('#ag_uso_prev_fecha_suspe').prop("disabled", true); 
                $('#ag_uso_prev_falla').prop("disabled", true);  
                $('#ag_otro_ante_ginecoobs').focus();  
            }
           
            })
        });


        $(document).ready(function () {
         $('#ag_otro_ante_ginecoobs').change(function (e) {

            if ($(this).val() === "NO") {
                $('#ag_otro_ante_ginecoobs_esp').prop("disabled", true);
                $('#ag_otro_ante_ginecoobs_esp').val('0'); 
                    v_ag_otro_ante_ginecoobs_esp();
                $('#bio_fecha_td_primera').focus();
            } else {
                $('#ag_otro_ante_ginecoobs_esp').prop("disabled", false ); 
                $('#ag_otro_ante_ginecoobs_esp').focus();
            }
           
            })
        });
        

        //CAMBIANDO EL COLOR DEL SEMAFORO OTROS ANTECEDENTES
            $(document).ready(function () {
                $('#ag_otro_ante_ginecoobs_esp').change(function (e) {
                    $ag_otro_ante_ginecoobs_esp = $('#ag_otro_ante_ginecoobs_esp').val();     
                    if( $ag_otro_ante_ginecoobs_esp === '0' ){
                        $('#ag_otro_ante_ginecoobs').val('NO'); 
                        v_ag_otro_ante_ginecoobs_esp();
                        $('#ag_otro_ante_ginecoobs_esp').prop("disabled", true);
                    }else{
                        a_ag_otro_ante_ginecoobs_esp();
                    }
                })
            }) 

        $(document).ready(function () {
         $('#af_ninguno').change(function (e) {
             
            if ($(this).val() === 'NO' ) {
                $('#af_tuberculosis').val('NO');
                $('#af_hipertencion').val('NO');
                $('#af_diabetes').val('NO');
                $('#af_ef_hiper_embarazo').val('NO');
                $('#af_otro').val('NO');
                $('#af_ninguno').val('NO');
                $('#af_otro_especifique').val('NINGUNO');
                $('#app_ninguno').focus();                          
                }
            })
        });
        

        $(document).ready(function () {
         $('#de_violencia_fam').change(function (e) {
                if ($(this).val() === 'NEGATIVO' ) {
                    $('#devf_b24x_fecha_inmuno').val('');
                    $('#devf_b24x_imnuno').val('');
                    $('#devf_b24x_fecha_enzimo').val('');
                    $('#devf_b24x_enzimo').val('');
                    $('#devf_a539_fecha_inmuno').val('');
                    $('#devf_a539_imnuno').val('');
                    $('#devf_a539_fecha_enzimo').val('');
                    $('#devf_a539_enzimo').val('');
                    $('#devf_b24x_fecha_inmuno').prop("disabled", true);
                    $('#devf_b24x_imnuno').prop("disabled", true);
                    $('#devf_b24x_fecha_enzimo').prop("disabled", true);
                    $('#devf_b24x_enzimo').prop("disabled", true);
                    $('#devf_a539_fecha_inmuno').prop("disabled", true);
                    $('#devf_a539_imnuno').prop("disabled", true);
                    $('#devf_a539_fecha_enzimo').prop("disabled", true);
                    $('#devf_a539_enzimo').prop("disabled", true);
                    $('#de_depresion_prenatal').focus();                          
                }else{
                    $('#devf_b24x_fecha_inmuno').prop("disabled", false);
                    $('#devf_b24x_imnuno').prop("disabled", false);
                    $('#devf_b24x_fecha_enzimo').prop("disabled", false);
                    $('#devf_b24x_enzimo').prop("disabled", false);
                    $('#devf_a539_fecha_inmuno').prop("disabled", false);
                    $('#devf_a539_imnuno').prop("disabled", false);
                    $('#devf_a539_fecha_enzimo').prop("disabled", false);
                    $('#devf_a539_enzimo').prop("disabled", false);
                    $('#devf_b24x_fecha_inmuno').focus(); 
                }
            })
        });

        $(document).ready(function () {
         $('#de_depresion_prenatal').change(function (e) {
                if ($(this).val() === 'NEGATIVO' ) {
                    $('#dedp_b24x_fecha_rapida').val('');
                    $('#dedp_b24x_rapida').val('');
                    $('#dedp_b24x_fecha_lab').val('');
                    $('#dedp_b24x_lab').val('');
                    $('#dedp_a539_fecha_rapidaa').val('');
                    $('#dedp_a539_rapida').val('');
                    $('#dedp_a539_fecha_lab').val('');
                    $('#dedp_a539_lab').val('');
                    $('#dedp_b24x_fecha_rapida').prop("disabled", true);
                    $('#dedp_b24x_rapida').prop("disabled", true);
                    $('#dedp_b24x_fecha_lab').prop("disabled", true);
                    $('#dedp_b24x_lab').prop("disabled", true);
                    $('#dedp_a539_fecha_rapidaa').prop("disabled", true);
                    $('#dedp_a539_rapida').prop("disabled", true);
                    $('#dedp_a539_fecha_lab').prop("disabled", true);
                    $('#dedp_a539_lab').prop("disabled", true);
                    $('#pc_lab_otr_b24x_fecha').focus();                                
                }else{
                    $('#dedp_b24x_fecha_rapida').prop("disabled", false);
                    $('#dedp_b24x_rapida').prop("disabled", false);
                    $('#dedp_b24x_fecha_lab').prop("disabled", false);
                    $('#dedp_b24x_lab').prop("disabled", false);
                    $('#dedp_a539_fecha_rapidaa').prop("disabled", false);
                    $('#dedp_a539_rapida').prop("disabled", false);
                    $('#dedp_a539_fecha_lab').prop("disabled", false);
                    $('#dedp_a539_lab').prop("disabled", false);
                    $('#dedp_b24x_fecha_rapida').focus(); 
                }
            })
        });
        
        $(document).ready(function () {
         $('#adicciones').change(function (e) {
             
            if ($(this).val() === 'NO' ) {
                $('#adic_tabaco').val('NO');
                        v_adic_tabaco();
                $('#adic_antidepre').val('NO');
                        v_adic_antidepre();
                $('#adic_alcohol').val('NO');
                        v_adic_alcohol();
                $('#adic_ansioliticos').val('NO');
                        v_adic_ansioliticos();
                $('#adic_otros').val('NO');
                $('#adic_consumo_act').val('NO');
                $('#adic_meses').val('0');
                $('#adic_años').val('0');
                $('#sbde_rev_odont').focus();                          
                }else{
                    $('#adic_tabaco').focus();   
                }
            })
        });
        
        $(document).ready(function () {
         $('#adic_tabaco').change(function (e) {
            if ($(this).val() === 'SI' )
                $('#adicciones').val('SI');   

                $adic_tabaco = $('#adic_tabaco').val();     
                    if( $adic_tabaco === "NO" ){
                        v_adic_tabaco()
                    }else{
                        a_adic_tabaco()
                    }              
            })
        });
        $(document).ready(function () {
         $('#adic_antidepre').change(function (e) {
            if ($(this).val() === 'SI' )
                $('#adicciones').val('SI');  

                $adic_antidepre = $('#adic_antidepre').val();     
                    if( $adic_antidepre === "NO" ){
                        v_adic_antidepre()
                    }else{
                        a_adic_antidepre()
                    }

            })
        });
        $(document).ready(function () {
         $('#adic_alcohol').change(function (e) {
            if ($(this).val() === 'SI' )
                $('#adicciones').val('SI'); 

                $adic_alcohol = $('#adic_alcohol').val();     
                    if( $adic_alcohol === "NO" ){
                        v_adic_alcohol()
                    }else{
                        a_adic_alcohol()
                    }                  
            })
        });
        $(document).ready(function () {
         $('#adic_ansioliticos').change(function (e) {
            if ($(this).val() === 'SI' )
                $('#adicciones').val('SI');   

                $adic_ansioliticos = $('#adic_ansioliticos').val();     
                    if( $adic_ansioliticos === "NO" ){
                        v_adic_ansioliticos()
                    }else{
                        a_adic_ansioliticos()
                    }                  
            })
        });
        $(document).ready(function () {
         $('#adic_otros').change(function (e) {
            if ($(this).val() === 'SI' )
                $('#adicciones').val('SI');                   
            })
        });
        $(document).ready(function () {
         $('#adic_consumo_act').change(function (e) {
            if ($(this).val() === 'SI' )
                $('#adicciones').val('SI');                   
            })
        });
        $(document).ready(function () {
         $('#adic_meses').change(function (e) {
            if ($(this).val() != 0 )
                $('#adicciones').val('SI');                   
            })
        });
        $(document).ready(function () {
         $('#adic_años').change(function (e) {
            if ($(this).val() != 0 )
                $('#adicciones').val('SI');                   
            })
        });

        $(document).ready(function () {
         $('#af_tuberculosis').change(function (e) {
            if ($(this).val() === 'SI' )
                $('#af_ninguno').val('SI');                   
            })
        });
        $(document).ready(function () {
         $('#af_hipertencion').change(function (e) {
            if ($(this).val() === 'SI' )
                $('#af_ninguno').val('SI');                   
            })
        });
        $(document).ready(function () {
         $('#af_diabetes').change(function (e) {
            if ($(this).val() === 'SI' )
                $('#af_ninguno').val('SI');                   
            })
        });
        $(document).ready(function () {
         $('#af_ef_hiper_embarazo').change(function (e) {
            if ($(this).val() === 'SI' )
                $('#af_ninguno').val('SI');                   
            })
        });
        $(document).ready(function () {
         $('#af_otro').change(function (e) {
            if ($(this).val() === 'SI' )
                $('#af_ninguno').val('SI');                   
            })
        });

     
        $(document).ready(function () {
         $('#af_otro').change(function (e) {
            
            if ($(this).val() === 'NO' ) {               
                $('#af_otro_especifique').val('NINGUNO');
            } else {
                $('#af_otro_especifique').val('');
                $('#af_otro_especifique').focus();  
            }
           
            })
        });

        $(document).ready(function () {
         $('#app_diabetes_geostacional').change(function (e) {
            if ($(this).val() === 'SI' )
                $('#app_ninguno').val('SI');  
            //CAMBIANDO EL SEMAFORO DIABETES GEOSTACIONAL
            $app_diabetes_geostacional = $('#app_diabetes_geostacional').val();     
                    if( $app_diabetes_geostacional === "NO" ){
                        v_app_diabetes_geostacional()
                    }else{
                        a_app_diabetes_geostacional()
                    }                   
            })
        });
        $(document).ready(function () {
         $('#app_diabetes').change(function (e) {
            if ($(this).val() === 'SI' )
                $('#app_ninguno').val('SI');                   
            })
        });
        $(document).ready(function () {
         $('#app_hipertencio_arterial').change(function (e) {
            if ($(this).val() === 'SI' )
                $('#app_ninguno').val('SI'); 
             //CAMBIANDO EL SEMAFORO HIPERTENSIÓN
                $app_hipertencio_arterial = $('#app_hipertencio_arterial').val();     
                    if( $app_hipertencio_arterial === "NO" ){
                        v_app_hipertencio_arterial()
                    }else{
                        a_app_hipertencio_arterial()
                    }                 
            })
        });
        $(document).ready(function () {
         $('#app_preeclampsia_enclampsia').change(function (e) {
            if ($(this).val() === 'SI' )
                $('#app_ninguno').val('SI');     
            //CAMBIANDO EL SEMAFORO PREECLAMPSIA/ECLAMPSIA
            $app_preeclampsia_enclampsia = $('#app_preeclampsia_enclampsia').val();     
                    if( $app_preeclampsia_enclampsia === "NO" ){
                        v_app_preeclampsia_enclampsia()
                    }else{
                        a_app_preeclampsia_enclampsia()
                    }                                             
            })
        });
        $(document).ready(function () {
         $('#app_nefropatia').change(function (e) {
            if ($(this).val() === 'SI' )
                $('#app_ninguno').val('SI');   
            //CAMBIANDO EL SEMAFORO NEFROPATIA
            $app_nefropatia = $('#app_nefropatia').val();     
                    if( $app_nefropatia === "NO" ){
                        v_app_nefropatia()
                    }else{
                        a_app_nefropatia()
                    }                                                                         
            })
        });                
        $(document).ready(function () {
         $('#app_cardiopatia').change(function (e) {
            if ($(this).val() === 'SI' )
                $('#app_ninguno').val('SI');  
            //CAMBIANDO EL SEMAFORO CARDIOPATIA
            $app_cardiopatia = $('#app_cardiopatia').val();     
                    if( $app_cardiopatia === "NO" ){
                        v_app_cardiopatia()
                    }else{
                        a_app_cardiopatia()
                    }                              
            })
        });
        $(document).ready(function () {
         $('#app_hemorragia_obstetrica').change(function (e) {
            if ($(this).val() === 'SI' )
                $('#app_ninguno').val('SI');  
            //CAMBIANDO EL SEMAFORO HEMORRAGIA OBSTETRICA 
            $app_hemorragia_obstetrica = $('#app_hemorragia_obstetrica').val();     
                    if( $app_hemorragia_obstetrica === "NO" ){
                        v_app_hemorragia_obstetrica()
                    }else{
                        a_app_hemorragia_obstetrica()
                    }                              
            })
        });
        $(document).ready(function () {
         $('#app_padecimiento_oncologico').change(function (e) {
            if ($(this).val() === 'SI' )
                $('#app_ninguno').val('SI');                   
            })
        });
        $(document).ready(function () {
         $('#app_b24x').change(function (e) {
            if ($(this).val() === 'SI' )
                $('#app_ninguno').val('SI');  
            //CAMBIANDO EL SEMAFORO B24X 
            $app_b24x = $('#app_b24x').val();     
                    if( $app_b24x === "NO" ){
                        v_app_b24x()
                    }else{
                        a_app_b24x()
                    }                              
            })
        });   
        $(document).ready(function () {
         $('#app_a539').change(function (e) {
            if ($(this).val() === 'SI' )
                $('#app_ninguno').val('SI');    
            //CAMBIANDO EL SEMAFORO B24X 
            $app_a539 = $('#app_a539').val();     
                    if( $app_a539 === "NO" ){
                        v_app_a539()
                    }else{
                        a_app_a539()
                    }                            
            })
        });
        $(document).ready(function () {
         $('#app_enfermedad_trnas_vector').change(function (e) {
            if ($(this).val() != 0 ){
                $('#app_ninguno').val('');                   
            } else {
                $('#app_ninguno').val(0);
               }                 
            })
        });
        $(document).ready(function () {
         $('#app_tuberculosis').change(function (e) {
            if ($(this).val() === 'SI' )
                $('#app_ninguno').val('SI'); 
            //CAMBIANDO EL SEMAFORO TUBERCULOSIS 
            $app_tuberculosis = $('#app_tuberculosis').val();     
                    if( $app_tuberculosis === "NO" ){
                        v_app_tuberculosis()
                    }else{
                        a_app_tuberculosis()
                    }                               
            })
        });
        $(document).ready(function () {
         $('#app_sars_cov2').change(function (e) {
            if ($(this).val() === 'SI' )
                $('#app_ninguno').val('SI');                   
            })
        });
        $(document).ready(function () {
         $('#app_otro_antecedente').change(function (e) {
            if ($(this).val() != 0 ){
                $('#app_ninguno').val('SI'); 
                //CAMBIANDO EL SEMAFORO OTRO ANTECEDENTE 
                 a_app_otro_antecedente()                    
            } else {
               // $('#app_ninguno').val(0);
                v_app_otro_antecedente() 
               }                 
            })
        });



        $(document).ready(function () {
         $('#app_ninguno').change(function (e) {
            
            if ($(this).val() === 'NO' ) {
                $('#app_diabetes_geostacional').val('NO');
                    v_app_diabetes_geostacional();
                $('#app_diabetes').val('NO');
                $('#app_hipertencio_arterial').val('NO');
                    v_app_hipertencio_arterial();
                $('#app_preeclampsia_enclampsia').val('NO');
                    v_app_preeclampsia_enclampsia();
                $('#app_nefropatia').val('NO');
                    v_app_nefropatia();
                $('#app_cardiopatia').val('NO');
                    v_app_cardiopatia();
                $('#app_hemorragia_obstetrica').val('NO');
                    v_app_hemorragia_obstetrica();
                $('#app_padecimiento_oncologico').val('NO');
                $('#app_b24x').val('NO');
                    v_app_b24x();
                $('#app_a539').val('NO');
                    v_app_a539();
                $('#app_enfermedad_trnas_vector').val(0);
                $('#app_tuberculosis').val('NO');
                    v_app_tuberculosis();
                $('#app_sars_cov2').val('NO');
                $('#app_otro_antecedente').val(0);
                    v_app_otro_antecedente();
                $('#app_grupo_sanguinieo').focus();                          
            } else {
                $('#app_ninguno').val('SI');
                $('#app_diabetes_geostacional').focus(); 
            }
           
           
            })
        });


        $(document).ready(function () {
            $('#enviar').on('click', function(e){
                $('#nacional').prop("disabled", false);
                $('#internacional').prop("disabled", false);
                $('#lengua_habla').prop("disabled", false);
                
                $('#ap_ulima_mestruacion').prop("disabled", false);
                $('#ap_confiable').prop("disabled", false);                
                $('#ap_semana_gestacion_usg').prop("disabled", false);

                $('#ag_gestas').prop("disabled", false);

                $('#af_tuberculosis').prop("disabled", false);
                $('#af_hipertencion').prop("disabled", false);
                $('#af_diabetes').prop("disabled", false);
                $('#af_ef_hiper_embarazo').prop("disabled", false);
                $('#af_otro').prop("disabled", false);
                $('#af_otro_especifique').prop("disabled", false);

                $('#devf_b24x_fecha_inmuno').prop("disabled", false);
                $('#devf_b24x_imnuno').prop("disabled", false);
                $('#devf_b24x_fecha_enzimo').prop("disabled", false);
                $('#devf_b24x_enzimo').prop("disabled", false);
                $('#devf_a539_fecha_inmuno').prop("disabled", false);
                $('#devf_a539_imnuno').prop("disabled", false);
                $('#devf_a539_fecha_enzimo').prop("disabled", false);
                $('#devf_a539_enzimo').prop("disabled", false);
                $('#dedp_b24x_fecha_rapida').prop("disabled", false);
                $('#dedp_b24x_rapida').prop("disabled", false);
                $('#dedp_b24x_fecha_lab').prop("disabled", false);
                $('#dedp_b24x_lab').prop("disabled", false);
                $('#dedp_a539_fecha_rapidaa').prop("disabled", false);
                $('#dedp_a539_rapida').prop("disabled", false);
                $('#dedp_a539_fecha_lab').prop("disabled", false);
                $('#dedp_a539_lab').prop("disabled", false);
            
                $('#app_diabetes_geostacional').prop("disabled", false);
                $('#app_diabetes').prop("disabled", false);
                $('#app_hipertencio_arterial').prop("disabled", false);
                $('#app_preeclampsia_enclampsia').prop("disabled", false);
                $('#app_nefropatia').prop("disabled", false);
                $('#app_cardiopatia').prop("disabled", false);
                $('#app_hemorragia_obstetrica').prop("disabled", false);
                $('#app_padecimiento_oncologico').prop("disabled", false);
                $('#app_b24x').prop("disabled", false);
                $('#app_a539').prop("disabled", false);
                $('#app_enfermedad_trnas_vector').prop("disabled", false);
                $('#app_tuberculosis').prop("disabled", false);
                $('#app_sars_cov2').prop("disabled", false);
                $('#app_otro_antecedente').prop("disabled", false);

                $('#ag_uso_prev_antico_tiempo_uso').prop("disabled", false);
                $('#ag_uso_prev_fecha_suspe').prop("disabled", false);
                $('#ag_uso_prev_falla').prop("disabled", false);
                $('#ag_otro_ante_ginecoobs_esp').prop("disabled", false);
                

                $('#adic_tabaco').prop("disabled", false );
                $('#adic_antidepre').prop("disabled", false);
                $('#adic_alcohol').prop("disabled", false);
                $('#adic_ansioliticos').prop("disabled", false);
                $('#adic_otros').prop("disabled", false);
                $('#adic_consumo_act').prop("disabled", false);
                $('#adic_meses').prop("disabled", false);
                $('#adic_años').prop("disabled", false);
           
            })
        });     
</script>
<script>
    $(document).ready(function () {
        $('#fecha_nacimiento').change(function (e) {

            $fecha_nacimiento = $(this).val();

            $fechaActual = new Date();
            $anoActual = parseInt($fechaActual.getFullYear());
            $mesActual = parseInt($fechaActual.getMonth()) + 1;
            $diaActual = parseInt($fechaActual.getDate());

            $anoNacimiento = parseInt(String($fecha_nacimiento).substring(0, 4));
            $mesNacimiento = parseInt(String($fecha_nacimiento).substring(5, 7));
            $diaNacimiento = parseInt(String($fecha_nacimiento).substring(8, 10));

            $edad = $anoActual - $anoNacimiento;
            
            if ($mesActual < $mesNacimiento) {
                $edad--;
            } else if ($mesActual === $mesNacimiento) {
                if ($diaActual < $diaNacimiento) {
                    $edad--;
                }
            }
            $('#edad').val($edad);  

            //CAMBIANDO EL COLOR DEL SEMAFORO
            if( $edad >=16  && $edad <=39  ){
                    v_edad()   
                }else{
                    a_edad()
                }

        })

    });
</script>
<script>
function v_edad() {
  document.getElementById("edad").style.background="#9ACD32";
  document.getElementById("edad").style.color = "white";
  //document.getElementById("edad").style.color = "white";
  // "background-color: #CD5C5C" "background-color: #FFFF00" "background-color: #9ACD32"
}
function a_edad() {
  document.getElementById("edad").style.background = "#FFFF00";
  document.getElementById("edad").style.color = "red";
}
function v_talla() {
  document.getElementById("talla").style.background="#9ACD32";
  document.getElementById("talla").style.color = "white";
}
function a_talla() {
  document.getElementById("talla").style.background = "#FFFF00";
  document.getElementById("talla").style.color = "red";
}
function v_imc() {
  document.getElementById("imc").style.background="#9ACD32";
  document.getElementById("imc").style.color = "white";
}
function a_imc() {
  document.getElementById("imc").style.background = "#FFFF00";
  document.getElementById("imc").style.color = "red";
}
function v_app_rh() {
  document.getElementById("app_rh").style.background="#9ACD32";
  document.getElementById("app_rh").style.color = "white";
}
function a_app_rh() {
  document.getElementById("app_rh").style.background = "#FFFF00";
  document.getElementById("app_rh").style.color = "red";
}
function v_app_hipertencio_arterial() {
  document.getElementById("app_hipertencio_arterial").style.background="#9ACD32";
  document.getElementById("app_hipertencio_arterial").style.color = "white";
}
function a_app_hipertencio_arterial() {
  document.getElementById("app_hipertencio_arterial").style.background = "#FFFF00";
  document.getElementById("app_hipertencio_arterial").style.color = "red";
}
function v_adic_tabaco() {
  document.getElementById("adic_tabaco").style.background="#9ACD32";
  document.getElementById("adic_tabaco").style.color = "white";
}
function a_adic_tabaco() {
  document.getElementById("adic_tabaco").style.background = "#FFFF00";
  document.getElementById("adic_tabaco").style.color = "red";
}
function v_adic_antidepre() { 
  document.getElementById("adic_antidepre").style.background="#9ACD32";
  document.getElementById("adic_antidepre").style.color = "white";
}
function a_adic_antidepre() {
  document.getElementById("adic_antidepre").style.background = "#FFFF00";
  document.getElementById("adic_antidepre").style.color = "red";
}
function v_adic_alcohol() {
  document.getElementById("adic_alcohol").style.background="#9ACD32";
  document.getElementById("adic_alcohol").style.color = "white";
}
function a_adic_alcohol() {
  document.getElementById("adic_alcohol").style.background = "#FFFF00";
  document.getElementById("adic_alcohol").style.color = "red";
}
function v_adic_ansioliticos() {
  document.getElementById("adic_ansioliticos").style.background="#9ACD32";
  document.getElementById("adic_ansioliticos").style.color = "white";
}
function a_adic_ansioliticos() {
  document.getElementById("adic_ansioliticos").style.background = "#FFFF00";
  document.getElementById("adic_ansioliticos").style.color = "red";
}
function v_app_diabetes_geostacional() {
  document.getElementById("app_diabetes_geostacional").style.background="#9ACD32";
  document.getElementById("app_diabetes_geostacional").style.color = "white";
}
function a_app_diabetes_geostacional() {
  document.getElementById("app_diabetes_geostacional").style.background = "#FFFF00";
  document.getElementById("app_diabetes_geostacional").style.color = "red";
}
function v_app_preeclampsia_enclampsia() {
  document.getElementById("app_preeclampsia_enclampsia").style.background="#9ACD32";
  document.getElementById("app_preeclampsia_enclampsia").style.color = "white";
}
function a_app_preeclampsia_enclampsia() {
  document.getElementById("app_preeclampsia_enclampsia").style.background = "#FFFF00";
  document.getElementById("app_preeclampsia_enclampsia").style.color = "red";
}
function v_app_nefropatia() {
  document.getElementById("app_nefropatia").style.background="#9ACD32";
  document.getElementById("app_nefropatia").style.color = "white";
}
function a_app_nefropatia() {
  document.getElementById("app_nefropatia").style.background = "#FFFF00";
  document.getElementById("app_nefropatia").style.color = "red";
}
function v_app_cardiopatia() {
  document.getElementById("app_cardiopatia").style.background="#9ACD32";
  document.getElementById("app_cardiopatia").style.color = "white";
}
function a_app_cardiopatia() {
  document.getElementById("app_cardiopatia").style.background = "#FFFF00";
  document.getElementById("app_cardiopatia").style.color = "red";
}
function v_app_hemorragia_obstetrica() {
  document.getElementById("app_hemorragia_obstetrica").style.background="#9ACD32";
  document.getElementById("app_hemorragia_obstetrica").style.color = "white";
}
function a_app_hemorragia_obstetrica() {
  document.getElementById("app_hemorragia_obstetrica").style.background = "#FFFF00";
  document.getElementById("app_hemorragia_obstetrica").style.color = "red";
}
function v_app_b24x() {
  document.getElementById("app_b24x").style.background="#9ACD32";
  document.getElementById("app_b24x").style.color = "white";
}
function a_app_b24x() {
  document.getElementById("app_b24x").style.background = "#FFFF00";
  document.getElementById("app_b24x").style.color = "red";
}
function v_app_a539() {
  document.getElementById("app_a539").style.background="#9ACD32";
  document.getElementById("app_a539").style.color = "white";
}
function a_app_a539() {
  document.getElementById("app_a539").style.background = "#FFFF00";
  document.getElementById("app_a539").style.color = "red";
}
function v_app_tuberculosis() {
  document.getElementById("app_tuberculosis").style.background="#9ACD32";
  document.getElementById("app_tuberculosis").style.color = "white";
}
function a_app_tuberculosis() {
  document.getElementById("app_tuberculosis").style.background = "#FFFF00";
  document.getElementById("app_tuberculosis").style.color = "red";
}
function v_app_otro_antecedente() {
  document.getElementById("app_otro_antecedente").style.background="#9ACD32";
  document.getElementById("app_otro_antecedente").style.color = "white";
}
function a_app_otro_antecedente() {
  document.getElementById("app_otro_antecedente").style.background = "#FFFF00";
  document.getElementById("app_otro_antecedente").style.color = "red";
}
function v_ag_gestas() {
  document.getElementById("ag_gestas").style.background="#9ACD32";
  document.getElementById("ag_gestas").style.color = "white";
}
function a_ag_gestas() {
  document.getElementById("ag_gestas").style.background = "#FFFF00";
  document.getElementById("ag_gestas").style.color = "red";
}
function v_ag_partos() {
  document.getElementById("ag_partos").style.background="#9ACD32";
  document.getElementById("ag_partos").style.color = "white";
}
function a_ag_partos() {
  document.getElementById("ag_partos").style.background = "#FFFF00";
  document.getElementById("ag_partos").style.color = "red";
}
function v_ag_cesarias() {
  document.getElementById("ag_cesarias").style.background="#9ACD32";
  document.getElementById("ag_cesarias").style.color = "white";
}
function a_ag_cesarias() {
  document.getElementById("ag_cesarias").style.background = "#FFFF00";
  document.getElementById("ag_cesarias").style.color = "red";
}
function v_ag_abortos() {
  document.getElementById("ag_abortos").style.background="#9ACD32";
  document.getElementById("ag_abortos").style.color = "white";
}
function a_ag_abortos() {
  document.getElementById("ag_abortos").style.background = "#FFFF00";
  document.getElementById("ag_abortos").style.color = "red";
}
function v_ag_ag_hijos_nac_mue() {
  document.getElementById("ag_ag_hijos_nac_mue").style.background="#9ACD32";
  document.getElementById("ag_ag_hijos_nac_mue").style.color = "white";
}
function a_ag_ag_hijos_nac_mue() {
  document.getElementById("ag_ag_hijos_nac_mue").style.background = "#FFFF00";
  document.getElementById("ag_ag_hijos_nac_mue").style.color = "red";
}
function v_ag_otro_ante_ginecoobs_esp() {
  document.getElementById("ag_otro_ante_ginecoobs_esp").style.background="#9ACD32";
  document.getElementById("ag_otro_ante_ginecoobs_esp").style.color = "white";
}
function a_ag_otro_ante_ginecoobs_esp() {
  document.getElementById("ag_otro_ante_ginecoobs_esp").style.background = "#FFFF00";
  document.getElementById("ag_otro_ante_ginecoobs_esp").style.color = "red";
}
function v_cdg_ayuno() {
  document.getElementById("cdg_ayuno").style.background = "#9ACD32";
  document.getElementById("cdg_ayuno").style.color = "white";
}
function a_cdg_ayuno() {
  document.getElementById("cdg_ayuno").style.background = "#FFFF00";
  document.getElementById("cdg_ayuno").style.color = "red";
}
function r_cdg_ayuno() {
  document.getElementById("cdg_ayuno").style.background = "#FF333F";
  document.getElementById("cdg_ayuno").style.color = "white";
}
/*cdg_ayuno 
function v_() {
  document.getElementById("").style.background="#9ACD32";
  document.getElementById("").style.color = "white";
}
function a_() {
  document.getElementById("").style.background = "#FFFF00";
  document.getElementById("").style.color = "red";
}
  app_diabetes_geostacional app_diabetes app_preeclampsia_enclampsia app_nefropatia app_cardiopatia app_hemorragia_obstetrica
*/
</script>
<script>

    /*
        <form id="editatarjeta" name="editatarjeta" action="actualizaEditatarjeta.php" method="POST" class="row g-3 needs-validation" novalidate>

        <!--Comentarios de validación--> <div class="invalid-tooltip">Por favor seleccione una opción valida.</div>
        <!--Comentarios de validación--> <div class="invalid-tooltip">Por favor seleccione una opción valida.</div>
        <!--Comentarios de validación--> <div class="invalid-tooltip">Por favor introduzca el folio.</div> 
        <!--Comentarios de validación--> <div class="invalid-tooltip">Por favor introduzca el nombre.</div>  
        <!--Comentarios de validación--> <div class="invalid-tooltip">Por favor introduzca la CURP.</div> 
    */
    // Example starter JavaScript for disabling form submissions if there are invalid fields
    /*  (function () {
    'use strict'

    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    var forms = document.querySelectorAll('.needs-validation')

    // Loop over them and prevent submission
    Array.prototype.slice.call(forms)
        .forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
            event.preventDefault()
            event.stopPropagation()
            }

            form.classList.add('was-validated')
        }, false)
        })
    })()
</script>


    <script>
      function inhabilitar(){
          alert ("Esta función está inhabilitada.\n\n SSA")
          return false
      }
      document.oncontextmenu = inhabilitar
    </script>

  </body>

</html>