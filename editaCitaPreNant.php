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



$sqlcita = "SELECT id_cita_prenatal, cp_fecha_ingreso_control, cp_fecha_consulta, cp_acompanante, cp_semana_gestacion, cp_peso, cp_talla, cp_imc, cp_pres_art, cp_fondo_uterino, cp_frec_cardiaca, cp_sig_sin_alarma, cp_medicamentos1, cp_medicamentos2, cp_medicamentos3, cp_qs_glucemia, cp_qs_emc_alter, cp_bh_plaquetas, cp_bh_leucocitos, cp_bh_hemoglobina, cp_bh_hematocrito, cp_egh_proteinuaria, cp_egh_hematuria, cp_egh_glucosuria, cp_egh_bacteriuria, cp_uo_sem_gesta, cp_uo_resultado, cp_ori_edu, cp_refrencia_a, cp_motiv_referencia, cp_eferemeda_presente, cp_plan_seguridad, fk_id_tarjeta  FROM citas_prenatales INNER JOIN datos_identificacion ON id_tarjeta = fk_id_tarjeta WHERE id_cita_prenatal = $id LIMIT 1";
$cita = $mysqli->query($sqlcita);
$rowcita= $cita->fetch_assoc();
$id_tarjeta = $rowcita['fk_id_tarjeta']; 

$a_concepto    = $rowcita['cp_acompanante'];
$ssa_concepto  = $rowcita['cp_sig_sin_alarma'];
$medi_concepto1  = $rowcita['cp_medicamentos1'];
$medi_concepto2  = $rowcita['cp_medicamentos2'];
$medi_concepto3  = $rowcita['cp_medicamentos3'];
$oe_concepto  = $rowcita['cp_ori_edu'];
$r_concepto  = $rowcita['cp_refrencia_a'];
$mr_concepto  = $rowcita['cp_motiv_referencia'];


$sqlpaciente = "SELECT CONCAT_WS(' ', nombre, primer_apellido, segundo_apellido) AS nombre_paciente FROM datos_identificacion
WHERE id_tarjeta = $id_tarjeta LIMIT 1";
$nombre_paciente = $mysqli->query($sqlpaciente);
$rowpaciente = $nombre_paciente->fetch_assoc();

///OBTENIENDO VALORES CVE
$queryAcom = "SELECT id_acompanante, a_concepto FROM acompanante ORDER BY id_acompanante";
$resultadoAcom = $mysqli->query($queryAcom);

$querySSA = "SELECT id_sig_sin_alarma, ssa_concepto FROM sig_sin_alarma ORDER BY id_sig_sin_alarma";
$resultadoSSA = $mysqli->query($querySSA);

$queryMedica1 = "SELECT id_medicamentos_emba, me_emba_concepto FROM medicamentos_emba ORDER BY id_medicamentos_emba";
$resultadoMedica1= $mysqli->query($queryMedica1);
$queryMedica2 = "SELECT id_medicamentos_emba, me_emba_concepto FROM medicamentos_emba ORDER BY id_medicamentos_emba";
$resultadoMedica2= $mysqli->query($queryMedica2);
$queryMedica3 = "SELECT id_medicamentos_emba, me_emba_concepto FROM medicamentos_emba ORDER BY id_medicamentos_emba";
$resultadoMedica3= $mysqli->query($queryMedica3);

$queryOE = "SELECT id_orientacion_edu, oe_concepto FROM orientacion_edu ORDER BY id_orientacion_edu";
$resultadoOE = $mysqli->query($queryOE);

$queryRef= "SELECT id_refenecia, re_concepto FROM refenecia ORDER BY id_refenecia";
$resultadoRef = $mysqli->query($queryRef);

$queryMRef= "SELECT id_motivo_referencia, mre_concepto FROM motivo_referencia ORDER BY id_motivo_referencia";
$resultadoMRef = $mysqli->query($queryMRef);             
////

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
		
 
		<title>Citas Prenatales</title>
		
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

        <h4 class="text-center">Información-Cita Prenatal</h2>   
        
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
        <h6 class="text-center"><?php echo 'PACIENTE: '.utf8_encode(utf8_decode($rowpaciente['nombre_paciente']));?></h6>
        <div class="row justify-content-end">
            <div class="col-auto">       
                
            <form action="citaprenatal.php" method="GET"> 
                <input type="hidden" name='id_tarjeta' value=<?= $rowcita['fk_id_tarjeta']; ?>> 
                <Button type ="submit" class="btn btn-dark">Regresar</Button>
            </form>

            </div>
        </div>
       <br>
        
        <form id="editatarjeta" name="editatarjeta" action="actualizaCitaPrenat.php" method="POST">

        <input type="hidden" name="fk_id_tarjeta" id="fk_id_tarjeta" class="form-control" value="<?php echo $rowcita['fk_id_tarjeta']; ?>" >    
        <input type="hidden" name="id_cita_prenatal" id="id_cita_prenatal" class="form-control" value="<?php echo $rowcita['id_cita_prenatal']; ?>" >    
        <!----INICIO---->        
                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-start text-start ">

                    <div class="col-md-2 m-l">
                    <label for="cp_fecha_consulta" class="form-label" value="<?php echo $rowcita['cp_fecha_consulta']; ?>">FECHA CONSULTA</label><label class="form-label" style="color: red">*:</label>
                    <input type="date" name="cp_fecha_consulta" id="cp_fecha_consulta" class="form-control" value="<?php echo $rowcita['cp_fecha_consulta']; ?>" required>
                    </div>	

                    <div class="col-md-4 m-l">
                    <label for="cp_acompanante" class="form-label">ACOMPAÑANTE</label><label class="form-label" style="color: red">*:</label>
                    <select name="cp_acompanante" id="cp_acompanante" class="form-select" required>
                        <option value="">Seleccionar...</option>
                        <?php while ($rowAcom = $resultadoAcom->fetch_assoc()) { ?>
                        <option value="<?php echo $rowAcom["id_acompanante"]; ?>"<?php if($rowAcom['id_acompanante']==$a_concepto) { echo 'selected'; } ?>><?php echo $rowAcom['a_concepto']; ?></option>
                        <?php } ?> 
                    </select>
                    </div>	

                    <div class="col-md-3 m-l">
                    <label for="cp_semana_gestacion" class="form-label">SEMANAS DE GESTACIÓN</label><label class="form-label" style="color: red">*:</label>
                    <input type="number" name="cp_semana_gestacion" id="cp_semana_gestacion" min="0"   class="form-control" value="<?php echo $rowcita['cp_semana_gestacion']; ?>"required>                        
                    </div>

                    <div class="col-md-1 m-l">
                    <label for="cp_peso" class="form-label">PESO KG</label>
                    <input type="text" style="text-align:right"name="cp_peso" id="cp_peso"  class="form-control" value="<?php echo $rowcita['cp_peso']; ?>"required>                        
                    </div>

                    <div class="col-md-1 m-l">
                    <label for="cp_talla" class="form-label">TALLA</label><label class="form-label" style="color: red">*:</label>
                    <input type="text" style="text-align:right" name="cp_talla" id="cp_talla"  class="form-control" value="<?php echo $rowcita['cp_talla']; ?>"required>                        
                    </div>

                    <div class="col-md-1 m-l">
                    <label for="cp_imc" class="form-label">IMC</label><label class="form-label" style="color: red">*:</label>
                    <input type="text" style="text-align:right" name="cp_imc" id="cp_imc"  class="form-control" value="<?php echo $rowcita['cp_imc']; ?>"required>                        
                    </div>
                </div>
                </div>

                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-start text-start ">

                    <div class="col-md-4 m-l">
                    <div class="input-group mb-3">
                    <span class="input-group-text">PRESIÓN ARTERIAL(Sist/Diast):</span>    
                        <input  type="text" name="cp_pres_art" id="cp_pres_art"  class="form-control" value="<?php echo $rowcita['cp_pres_art']; ?>">                        
                    </div>
                    </div>
                    <div class="col-md-4 m-l">
                    <div class="input-group mb-3">
                    <span class="input-group-text">FONDO UTERINO (cm):</span>
                        <input  type="text" name="cp_fondo_uterino" id="cp_fondo_uterino"  class="form-control" value="<?php echo $rowcita['cp_fondo_uterino']; ?>">                        
                    </div>
                    </div>
                    <div class="col-md-4 m-l">
                    <div class="input-group mb-3">
                    <span class="input-group-text">FRECUENCIA CARDIACA FETAL x Min:</span>
                        <input  type="text" name="cp_frec_cardiaca" id="cp_frec_cardiaca"  class="form-control" value="<?php echo $rowcita['cp_frec_cardiaca']; ?>">                        
                    </div>
                    </div>

                </div>
                </div>

                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-center text-start ">  
                    <div class="col-md-3 m-l">
                    <label for="cp_sig_sin_alarma" class="form-label">SIGNOS Y SÍNTOMAS DE ALARMA</label><label class="form-label" style="color: red">*:</label>
                    <select name="cp_sig_sin_alarma" id="cp_sig_sin_alarma" class="form-select" required >
                        <option value="">Seleccionar...</option>
                        <?php while ($rowSSA = $resultadoSSA->fetch_assoc()) { ?>
                        <option value="<?php echo $rowSSA["id_sig_sin_alarma"]; ?>"<?php if($rowSSA['id_sig_sin_alarma']==$ssa_concepto) { echo 'selected'; } ?>><?php echo $rowSSA['ssa_concepto']; ?></option>
                        <?php } ?> 
                    </select>
                    </div>	

                    <div class="col-md-3 m-l">
                    <label for="cp_medicamentos1" class="form-label">MEDICAMENTOS 1:</label>
                    <select name="cp_medicamentos1" id="cp_medicamentos1" class="form-select" >
                        <option value="">Seleccionar...</option>
                        <?php while ($rowMed = $resultadoMedica1->fetch_assoc()) { ?>
                        <option value="<?php echo $rowMed["id_medicamentos_emba"]; ?>"<?php if($rowMed['id_medicamentos_emba']==$medi_concepto1) { echo 'selected'; } ?>><?php echo $rowMed['me_emba_concepto']; ?></option>
                        <?php } ?>
                    </select>
                    </div>	

                    <div class="col-md-3 m-l">
                    <label for="cp_medicamentos2" class="form-label">MEDICAMENTOS 2:</label>
                    <select name="cp_medicamentos2" id="cp_medicamentos2" class="form-select" >
                        <option value="">Seleccionar...</option>
                        <?php while ($rowMed = $resultadoMedica2->fetch_assoc()) { ?>
                        <option value="<?php echo $rowMed["id_medicamentos_emba"]; ?>"<?php if($rowMed['id_medicamentos_emba']==$medi_concepto2) { echo 'selected'; } ?>><?php echo $rowMed['me_emba_concepto']; ?></option>
                        <?php } ?>
                    </select>
                    </div>	

                    <div class="col-md-3 m-l">
                    <label for="cp_medicamentos3" class="form-label">MEDICAMENTOS 3:</label>
                    <select name="cp_medicamentos3" id="cp_medicamentos3" class="form-select" >
                        <option value="">Seleccionar...</option>
                        <?php while ($rowMed = $resultadoMedica3->fetch_assoc()) { ?>
                        <option value="<?php echo $rowMed["id_medicamentos_emba"]; ?>"<?php if($rowMed['id_medicamentos_emba']==$medi_concepto3) { echo 'selected'; } ?>><?php echo $rowMed['me_emba_concepto']; ?></option>
                        <?php } ?>
                    </select>
                    </div>	

                </div>
                </div>

                <center><div class="bg-secondary p-2 text-white bg-opacity-90 modal-title fs-5">QUÍMICA SANGUÍNEA</div></center>

                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-center text-start ">    

                    <div class="col-md-4 m-l">
                    <div class="input-group mb-3">
                    <span class="input-group-text">GLUCEMIA:</span>
                        <input  type="text" name="cp_qs_glucemia" id="cp_qs_glucemia"  class="form-control" value="<?php echo $rowcita['cp_qs_glucemia']; ?>">                        
                    </div>
                    </div>

                    <div class="col-md-5 m-l">   
                    <div class="input-group mb-3">
                    <span class="input-group-text">ENZIMAS HEPÁTICAS CON ALTERACIONES:</span>                    
                        <select name="cp_qs_emc_alter" id="cp_qs_emc_alter" class="form-select" >
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowcita['cp_qs_emc_alter']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowcita['cp_qs_emc_alter']) { echo 'selected'; } ?>>NO</option> 
                        </select>
                    </div>
                    </div>
                   
                </div>                       
                </div>

                <center><div class="bg-secondary p-2 text-white bg-opacity-90 modal-title fs-5">BIOMETRÍA HEMÁTICA</div></center>

                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-center text-start "> 

                    <div class="col-md-3 m-l">
                    <div class="input-group mb-3">
                    <span class="input-group-text">PLAQUETAS:</span>
                        <input  type="text" name="cp_bh_plaquetas" id="cp_bh_plaquetas"  class="form-control" value="<?php echo $rowcita['cp_bh_plaquetas']; ?>">                        
                    </div>
                    </div>

                    <div class="col-md-3 m-l">
                    <div class="input-group mb-3">
                    <span class="input-group-text">LEUCOCITOS:</span>                     
                        <input  type="text" name="cp_bh_leucocitos" id="cp_bh_leucocitos"  class="form-control" value="<?php echo $rowcita['cp_bh_leucocitos']; ?>">                        
                    </div>
                    </div>

                    <div class="col-md-3 m-l">
                    <div class="input-group mb-3">
                    <span class="input-group-text">HEMOGLOBINA:</span>  
                        <input  type="text" name="cp_bh_hemoglobina" id="cp_bh_hemoglobina"  class="form-control" value="<?php echo $rowcita['cp_bh_hemoglobina']; ?>">                        
                    </div>
                    </div>

                    <div class="col-md-3 m-l">
                    <div class="input-group mb-3">
                    <span class="input-group-text">HEMATOCRITO:</span>  
                        <input  type="text" name="cp_bh_hematocrito" id="cp_bh_hematocrito"  class="form-control" value="<?php echo $rowcita['cp_bh_hematocrito']; ?>">                        
                    </div>
                    </div>

                </div>                       
                </div>

                <center><div class="bg-secondary p-2 text-white bg-opacity-90 modal-title fs-5">EXAMEN GENERAL DE ORINA</div></center>

                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-center text-start "> 

                    <div class="col-md-3 m-l">      
                    <div class="input-group mb-3">
                    <span class="input-group-text">PROTEINURIA:</span>                  
                        <select name="cp_egh_proteinuaria" id="cp_egh_proteinuaria" class="form-select" >
                            <option value="">Seleccionar...</option>
                            <option value="POSITIVO"<?php if('POSITIVO' == $rowcita['cp_egh_proteinuaria']) { echo 'selected'; } ?>>POSITIVO</option>
                            <option value="NEGATIVO"<?php if('NEGATIVO' == $rowcita['cp_egh_proteinuaria']) { echo 'selected'; } ?>>NEGATIVO</option> 
                        </select>
                    </div>
                    </div>
                    

                    <div class="col-md-3 m-l">  
                    <div class="input-group mb-3">
                    <span class="input-group-text">HEMATURIA:</span>                         
                        <select name="cp_egh_hematuria" id="cp_egh_hematuria" class="form-select" >
                            <option value="">Seleccionar...</option>
                            <option value="POSITIVO"<?php if('POSITIVO' == $rowcita['cp_egh_hematuria']) { echo 'selected'; } ?>>POSITIVO</option>
                            <option value="NEGATIVO"<?php if('NEGATIVO' == $rowcita['cp_egh_hematuria']) { echo 'selected'; } ?>>NEGATIVO</option> 
                        </select>
                    </div>
                    </div>

                    <div class="col-md-3 m-l">  
                    <div class="input-group mb-3">
                    <span class="input-group-text">GLUCOSURIA:</span>                         
                        <select name="cp_egh_glucosuria" id="cp_egh_glucosuria" class="form-select" >
                            <option value="">Seleccionar...</option>
                            <option value="POSITIVO"<?php if('POSITIVO' == $rowcita['cp_egh_glucosuria']) { echo 'selected'; } ?>>POSITIVO</option>
                            <option value="NEGATIVO"<?php if('NEGATIVO' == $rowcita['cp_egh_glucosuria']) { echo 'selected'; } ?>>NEGATIVO</option> 
                        </select>
                    </div>
                    </div>

                    <div class="col-md-3 m-l">   
                    <div class="input-group mb-3">
                    <span class="input-group-text">BACTERIURIA:</span>                        
                        <select name="cp_egh_bacteriuria" id="cp_egh_bacteriuria" class="form-select" >
                            <option value="">Seleccionar...</option>
                            <option value="POSITIVO"<?php if('POSITIVO' == $rowcita['cp_egh_bacteriuria']) { echo 'selected'; } ?>>POSITIVO</option>
                            <option value="NEGATIVO"<?php if('NEGATIVO' == $rowcita['cp_egh_bacteriuria']) { echo 'selected'; } ?>>NEGATIVO</option> 
                        </select>
                    </div>
                    </div>

                </div>                       
                </div>

                <center><div class="bg-secondary p-2 text-white bg-opacity-90 modal-title fs-5">ULTRASONIDO OBSTÉTRICO</div></center>

                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-center text-start ">

                    <div class="col-md-4 m-l">
                    <div class="input-group mb-3">
                    <span class="input-group-text">SEMANAS DE GESTACIÓN:</span> 
                    <input type="number" name="cp_uo_sem_gesta" id="cp_uo_sem_gesta" min="0"  class="form-control" value="<?php echo $rowcita['cp_uo_sem_gesta']; ?>">                        
                    </div>
                    </div>
                    
                    <div class="col-md-3 m-l">
                    <div class="input-group mb-3">
                    <span class="input-group-text">RESULTADO:</span> 
                        <input  type="text" name="cp_uo_resultado" id="cp_uo_resultado"  class="form-control" value="<?php echo $rowcita['cp_uo_resultado']; ?>">                        
                    </div>
                    </div>

                </div>                       
                </div>
                    <hr>
                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-center text-start ">

                    <div class="col-md-4 m-l">
                    <label for="cp_ori_edu" class="form-label">ORIENTACIÓN EDUCATIVA:</label>
                    <select name="cp_ori_edu" id="cp_ori_edu" class="form-select" >
                        <option value="">Seleccionar...</option>                      
                        <?php while ($rowOE = $resultadoOE->fetch_assoc()) { ?>
                        <option value="<?php echo $rowOE["id_orientacion_edu"]; ?>"<?php if($rowOE['id_orientacion_edu']==$oe_concepto) { echo 'selected'; } ?>><?php echo $rowOE['oe_concepto']; ?></option>
                        <?php } ?> 
                    </select>
                    </div>	
 
                    <div class="col-md-4 m-l">
                    <label for="cp_refrencia_a" class="form-label">REFERENCIA A:</label>
                    <select name="cp_refrencia_a" id="cp_refrencia_a" class="form-select" >
                        <option value="">Seleccionar...</option>
                        <?php while ($rowRef = $resultadoRef->fetch_assoc()) { ?>
                        <option value="<?php echo $rowRef["id_refenecia"]; ?>"<?php if($rowRef['id_refenecia']==$r_concepto) { echo 'selected'; } ?>><?php echo $rowRef['re_concepto']; ?></option>
                        <?php } ?> 
                    </select>
                    </div>	

                    <div class="col-md-4 m-l">
                    <label for="cp_motiv_referencia" class="form-label">MOTIVO DE REFERENCIA:</label>
                    <select name="cp_motiv_referencia" id="cp_motiv_referencia" class="form-select" >
                        <option value="">Seleccionar...</option>
                        <?php while ($rowMRef = $resultadoMRef->fetch_assoc()) { ?>
                        <option value="<?php echo $rowMRef["id_motivo_referencia"]; ?>"<?php if($rowMRef['id_motivo_referencia']==$mr_concepto) { echo 'selected'; } ?>><?php echo $rowMRef['mre_concepto']; ?></option>
                        <?php } ?> 
                    </select>
                    </div>	

                </div>                       
                </div>    

                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-center text-start ">

                    <div class="col-md-12 m-l">
                        <label for="cp_eferemeda_presente" class="form-label">ENFERMEDADES PRESENTESClave  CIE-10</label>
                        <input  type="text" name="cp_eferemeda_presente" id="cp_eferemeda_presente"  class="form-control" value="<?php echo $rowcita['cp_eferemeda_presente']; ?>">                        
                    </div>

                </div>                       
                </div>    

                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-center text-start ">

                    <div class="col-md-4 m-l">   
                    <div class="input-group mb-">
                    <span class="input-group-text">PLAN DE SEGURIDAD<label class="form-label" style="color: red">*:</label></span>                     
                        <select name="cp_plan_seguridad" id="cp_plan_seguridad" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowcita['cp_plan_seguridad']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowcita['cp_plan_seguridad']) { echo 'selected'; } ?>>NO</option> 
                        </select>
                    </div>
                    </div>

                </div>                       
                </div>  

                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-center text-center ">
                    <div cclass="col-md-3 m-l">
                    <button type="submit" class="btn btn-outline-primary" id="enviar" name="enviar" ><i class="fa-solid fa-floppy-disk"></i> Guardar</button>
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
<script>
    //CAMBIANDO EL COLOR DEL SEMAFORO TALLA 
    $(document).ready(function () {
            $('#cp_talla').ready(function (e) {
                $cp_talla = $('#cp_talla').val();     
                if( $cp_talla >=1.46 ){
                    v_cp_talla()
                }else{
                    a_cp_talla()
                }
            })
        }); 

    //CAMBIANDO EL COLOR DEL SEMAFORO IMC
        $(document).ready(function () {
            $('#cp_imc').ready(function (e) {
                $cp_imc = $('#cp_imc').val();     
                if(  $cp_imc >=18.5  && $cp_imc <=25 ){
                    v_cp_imc()
                }else{
                    a_cp_imc()
                }
            })
        });

    //CAMBIANDO EL COLOR DEL HEMOGLOBINA
    $(document).ready(function () {
            $('#cp_bh_hemoglobina').ready(function (e) {  
                $cp_bh_hemoglobina = $('#cp_bh_hemoglobina').val();       
        //SEMAFORO HEMOGLOBINA   
                if( $cp_bh_hemoglobina >= 9 ){
                    v_cp_bh_hemoglobina();
                }else{
                    a_cp_bh_hemoglobina();
                }
            })
        });       
        
    //CAMBIANDO EL COLOR DEL SEMAFORO OTROS GLUCOSA EN AYUNO
    $(document).ready(function () {
                $('#cp_qs_glucemia').ready(function (e) {
                    $cdg_ayuno = $('#cp_qs_glucemia').val();    
                    if( $cdg_ayuno >= '200' ){
                        r_cp_qs_glucemia();
                    }else if ( $cdg_ayuno >= '126'&& $cdg_ayuno <= '199' ){
                        a_cp_qs_glucemia();
                    }else{
                        v_cp_qs_glucemia();
                    }
                })
            });   

    //CAMBIANDO EL COLOR DEL SEMAFORO FRECUENCIA CARDIACA
    $(document).ready(function () {
                $('#cp_frec_cardiaca').ready(function (e) {
                    $cp_frec_cardiaca = $('#cp_frec_cardiaca').val();    
                    if( $cp_frec_cardiaca >= '120' && $cp_frec_cardiaca <= '160' ){
                        a_cp_frec_cardiaca();
                    }else{
                        r_cp_frec_cardiaca();
                    }
                })
    });  

    //CAMBIANDO EL COLOR DEL SEMAFORO PRESIÓN ARTERIAL
    $(document).ready(function () {
                $('#cp_pres_art').ready(function (e) {
                    $cp_pres_art = $('#cp_pres_art').val();    
                    if( $cp_pres_art <= '130/89' ){
                        v_cp_pres_art();
                    }else{
                        r_cp_pres_art();
                    }
                })
    });

    //CAMBIANDO EL COLOR DEL SEMAFORO SIGNOS Y SINTOMAS DE ALARMA
    $(document).ready(function () {
                $('#cp_sig_sin_alarma').ready(function (e) {
                    $cp_sig_sin_alarma = $('#cp_sig_sin_alarma').val();    
                    if($cp_sig_sin_alarma == '0'){
                        v_cp_sig_sin_alarma();
                    }else if ($cp_sig_sin_alarma == '6' || ($cp_sig_sin_alarma >='10' && $cp_sig_sin_alarma <='12') ){
                        a_cp_sig_sin_alarma();
                    }else{
                        r_cp_sig_sin_alarma();
                    }
                })
    });    
       
</script>

<script>
        $("#cp_talla").on("keyup", function() {
        var cp_peso = $("#cp_peso").val(); //CAPTURANDO EL VALOR DE INPUT 
        var cp_talla = $("#cp_talla").val(); //CAPTURANDO EL VALOR DE INPUT 
        var cp_talla2 = cp_talla*cp_talla;
        var cp_imc_cal = (cp_peso/cp_talla2).toFixed(2);
        $('#cp_imc').val(cp_imc_cal);
        //SEMAFORO TALLA
        $cp_talla = $('#cp_talla').val();     
                if( $cp_talla >= 1.46 ){
                    v_cp_talla();
                }else{
                    a_cp_talla();
                }

        //SEMAFORO IMC
        $cp_imc = $('#cp_imc').val();     
                if( $cp_imc >=18.5  && $cp_imc <=25 ){
                    v_cp_imc();
                }else{
                    a_cp_imc();
                }
        });    

        $("#cp_bh_hemoglobina").on("keyup", function() {
        $cp_bh_hemoglobina = $("#cp_bh_hemoglobina").val(); //CAPTURANDO EL VALOR DE INPUT 
        
        //SEMAFORO HEMOGLOBINA   
                if( $cp_bh_hemoglobina >= 9 ){
                    v_cp_bh_hemoglobina();
                }else{
                    a_cp_bh_hemoglobina();
                }
        });      
        
      
        //CAMBIANDO EL COLOR DEL SEMAFORO OTROS GLUCOSA EN AYUNO
        $('#cp_qs_glucemia').change(function (e) {
            $cp_qs_glucemia = $('#cp_qs_glucemia').val();    
            if( $cp_qs_glucemia >= 200 ){
                r_cp_qs_glucemia();
            }else if ( $cp_qs_glucemia >= '126' && $cp_qs_glucemia <= '199' ){
                a_cp_qs_glucemia();
            }else{
                v_cp_qs_glucemia();
            }
        })
  

        //CAMBIANDO EL COLOR DEL SEMAFORO OTROS GLUCOSA EN AYUNO
        $("#cp_qs_glucemia").on("keyup", function() {
            $cp_qs_glucemia = $('#cp_qs_glucemia').val();    
            if( $cp_qs_glucemia >= 200 ){
                r_cp_qs_glucemia();
            }else if ( $cp_qs_glucemia >= '126'&& $cp_qs_glucemia <= '199' ){
                a_cp_qs_glucemia();
            }else{
                v_cp_qs_glucemia();
            }
        });


        //CAMBIANDO EL COLOR DEL SEMAFORO FRECUENCIA CARDIACA
        $('#cp_frec_cardiaca').change(function (e) {
            $cp_frec_cardiaca = $('#cp_frec_cardiaca').val();    
            if( $cp_frec_cardiaca >= '120' && $cp_frec_cardiaca <= '160' ){
                a_cp_frec_cardiaca();
            }else{
                r_cp_frec_cardiaca();
            }
        })  

        $("#cp_frec_cardiaca").on("keyup", function() {
            $cp_frec_cardiaca = $('#cp_frec_cardiaca').val();    
            if( $cp_frec_cardiaca >= '120' && $cp_frec_cardiaca <= '160' ){
                a_cp_frec_cardiaca();
            }else{
                r_cp_frec_cardiaca();
            }
        });
      
        //CAMBIANDO EL COLOR DEL SEMAFORO PRESION ARTERIAL
        $('#cp_pres_art').change(function (e) {
            $cp_pres_art = $('#cp_pres_art').val();    
            if( $cp_pres_art <= '130/89' ){
                v_cp_pres_art();
            }else{
                r_cp_pres_art();
            }   
        }) 

        $("#cp_pres_art").on("keyup", function() {
            $cp_pres_art = $('#cp_pres_art').val();    
            if( $cp_pres_art <= '130/89' ){
                v_cp_pres_art();
            }else{
                r_cp_pres_art();
            }   
        });

        //CAMBIANDO EL COLOR DEL SEMAFORO SIGNOS Y SINTOMAS DE ALARMA
        $('#cp_sig_sin_alarma').change(function (e) {
            $cp_sig_sin_alarma = $('#cp_sig_sin_alarma').val();    
            if($cp_sig_sin_alarma == '0'){
                v_cp_sig_sin_alarma();
            }else if ($cp_sig_sin_alarma == '6' || ($cp_sig_sin_alarma >=10 & $cp_sig_sin_alarma <=12) ){
                a_cp_sig_sin_alarma();
            }else{
                r_cp_sig_sin_alarma();
            }
        }) 
</script>

<script>
function v_cp_talla() {
  document.getElementById("cp_talla").style.background="#9ACD32";
  document.getElementById("cp_talla").style.color = "white";
}
function a_cp_talla() {
  document.getElementById("cp_talla").style.background = "#FFFF00";
  document.getElementById("cp_talla").style.color = "red";
}
function v_cp_imc() {
  document.getElementById("cp_imc").style.background="#9ACD32";
  document.getElementById("cp_imc").style.color = "white";
}
function a_cp_imc() {
  document.getElementById("cp_imc").style.background = "#FFFF00";
  document.getElementById("cp_imc").style.color = "red";
}
function v_cp_bh_hemoglobina() {
  document.getElementById("cp_bh_hemoglobina").style.background="#9ACD32";
  document.getElementById("cp_bh_hemoglobina").style.color = "white";
}
function a_cp_bh_hemoglobina() {
  document.getElementById("cp_bh_hemoglobina").style.background = "#FFFF00";
  document.getElementById("cp_bh_hemoglobina").style.color = "red";
}
function v_cp_qs_glucemia() {
  document.getElementById("cp_qs_glucemia").style.background = "#9ACD32";
  document.getElementById("cp_qs_glucemia").style.color = "white";
}
function a_cp_qs_glucemia() {
  document.getElementById("cp_qs_glucemia").style.background = "#FFFF00";
  document.getElementById("cp_qs_glucemia").style.color = "red";
}
function r_cp_qs_glucemia() {
  document.getElementById("cp_qs_glucemia").style.background = "#FF333F";
  document.getElementById("cp_qs_glucemia").style.color = "white";
}
function a_cp_frec_cardiaca() {
  document.getElementById("cp_frec_cardiaca").style.background = "#FFFF00";
  document.getElementById("cp_frec_cardiaca").style.color = "red";
}
function r_cp_frec_cardiaca() {
  document.getElementById("cp_frec_cardiaca").style.background = "#FF333F";
  document.getElementById("cp_frec_cardiaca").style.color = "white";
}
function v_cp_pres_art() {
  document.getElementById("cp_pres_art").style.background = "#9ACD32";
  document.getElementById("cp_pres_art").style.color = "white";
}
function r_cp_pres_art() {
  document.getElementById("cp_pres_art").style.background = "#FF333F";
  document.getElementById("cp_pres_art").style.color = "white";
}
function v_cp_sig_sin_alarma() {
  document.getElementById("cp_sig_sin_alarma").style.background = "#9ACD32";
  document.getElementById("cp_sig_sin_alarma").style.color = "white";
}
function a_cp_sig_sin_alarma() {
  document.getElementById("cp_sig_sin_alarma").style.background = "#FFFF00";
  document.getElementById("cp_sig_sin_alarma").style.color = "red";
}
function r_cp_sig_sin_alarma() {
  document.getElementById("cp_sig_sin_alarma").style.background = "#FF333F";
  document.getElementById("cp_sig_sin_alarma").style.color = "white";
}

</script>
  </body>

</html>