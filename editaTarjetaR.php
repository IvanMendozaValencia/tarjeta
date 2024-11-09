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

$id = $mysqli->real_escape_string($_GET['id_tarjeta']);

$sqltarjeta = "SELECT id_tarjeta, fecha_ant_obste, aborto_ameu, aborto_lui, aborto_medicamento, parto_eutocico, parto_distocico, parto_cesarea, semanas_gestacion, metodo_anticoncep, metodo_especifica, ambulancia, tipo_transporte, vehículo_particular, transporte_ame, transporte_publico, ambulancia_aerea, atendido_en, atendido_por, complicaciones  FROM datos_identificacion  WHERE id_tarjeta = $id LIMIT 1";
$tarjeta = $mysqli->query($sqltarjeta);
$rowcita= $tarjeta->fetch_assoc();
$id_tarjeta = $rowcita['id_tarjeta']; 
///OBTENIENDO VALORES CVE
$mesp_concepto  = $rowcita['metodo_especifica'];
$atnen_concepto  = $rowcita['atendido_en'];
$atnpor_concepto  = $rowcita['atendido_por'];
$listcomp_concepto  = $rowcita['complicaciones'];

$sqlpaciente = "SELECT id_tarjeta, CONCAT_WS(' ', nombre, primer_apellido, segundo_apellido) AS nombre_paciente FROM datos_identificacion
WHERE id_tarjeta = $id_tarjeta LIMIT 1";
$nombre_paciente = $mysqli->query($sqlpaciente);
$rowpaciente = $nombre_paciente->fetch_assoc();

//consulta de catalogos
$queryANTESP= "SELECT id_anticonceptivoesp, antesp_concepto FROM anticonceptivoesp ORDER BY id_anticonceptivoesp";
$resultadoANTESP = $mysqli->query($queryANTESP);

$queryATNEN= "SELECT id_atendidoen, atnen_concepto FROM atendidoen ORDER BY id_atendidoen";
$resultadoATNEN = $mysqli->query($queryATNEN);

$queryATNPOR= "SELECT id_atendidopor, atnpor_concepto FROM atendidopor ORDER BY id_atendidopor";
$resultadoATNPOR = $mysqli->query($queryATNPOR);

$queryCOMPLIST= "SELECT id_complicacioneslist, compli_concepto FROM complicacioneslist ORDER BY id_complicacioneslist";
$resultadoCOMPLIST = $mysqli->query($queryCOMPLIST);

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
		

		<title>Tarjeta Reverso</title>
		
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

        <h4 class="text-center">Información-Tarjeta de Atención Integral del Embarazo, Puerperio y Período de Lactancia</h2>   
        
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
        <center><div class="bg-secondary p-0 text-white bg-opacity-90 modal-title fs-5">REVERSO</div></center>
        <br>
        <div class="row justify-content-end">          

            <div class="col-auto">             
                <form action="periodolactancia.php" method="GET"> 
                <input type="hidden"   name='id_tarjeta' value=<?= $rowcita['id_tarjeta']; ?>> 
              
                    <?php if($row_usuario['a_agregar']==1):  ?>
                    <Button type ="submit" class="btn btn-outline-info"><i class="fa-solid fa-person-breastfeeding"></i>  Periodo de Lactancia</Button>
                    <?php endif;?>

                    <?php if($row_usuario['a_agregar']==0):  ?>
                    <Button type ="submit" class="btn btn-outline-info disabled"><i class="fa-solid fa-person-breastfeeding"></i>  Periodo de Lactancia</Button>
                    <?php endif;?> 
                    
                </form> 
            </div>

            <div class="col-auto">
                <form action="atencionpuerperio.php" method="GET"> 

                    <input type="hidden"   name='id_tarjeta' value=<?= $rowcita['id_tarjeta']; ?>> 

                    <?php if($row_usuario['a_agregar']==1):  ?>
                    <Button type ="submit" class="btn btn-outline-success"><i class="fa-solid fa-person-dress"></i> Periodo de Puerperio</Button>
                    <?php endif;?>

                    <?php if($row_usuario['a_agregar']==0):  ?>
                    <Button type ="submit" class="btn btn-outline-success disabled"><i class="fa-solid fa-person-dress"></i> Periodo de Puerperio</Button>
                    <?php endif;?> 
                </form>     
            </div>
            <div class="col-auto">             
                <form action="recienNacidos.php" method="GET"> 

                    <input type="hidden"   name='id_tarjeta' value=<?= $rowcita['id_tarjeta']; ?>> 

                    <?php if($row_usuario['a_agregar']==1):  ?>
                    <Button type ="submit" class="btn btn-outline-warning"><i class="fa-solid fa-baby"></i> Recién Nacidos</Button>
                    <?php endif;?>

                    <?php if($row_usuario['a_agregar']==0):  ?>
                    <Button type ="submit" class="btn btn-outline-warning disabled"><i class="fa-solid fa-baby"></i> Recién Nacidos</Button>
                    <?php endif;?> 
                </form> 
            </div>
            <div class="col-auto">  
                <form action="editaTarjeta.php" method="GET"> 

                    <input type="hidden"   name='id' value=<?= $rowcita['id_tarjeta']; ?>>
                    <Button type ="submit" class="btn btn-dark"><i class="fa-solid fa-address-card"></i> Anverso</Button>

                </form>      
            </div>
        </div>
        
        <br>
        
        <form id="editatarjetaR" name="editatarjetaR" action="actualizaEditatarjetaR.php" method="POST">

        <input type="hidden" name="id_tarjeta" id="id_tarjeta" class="form-control" value="<?php echo $rowcita['id_tarjeta']; ?>" > 
        <input type="hidden" name="clues_id" id="clues_id" class="form-control" value="<?php echo $row_usuario['CLUES']; ?>" >    

        <!----INICIO---->
        <center><div class="bg-secondary p-2 text-white bg-opacity-90 modal-title fs-5">DATOS DE LA ATENCIÓN OBSTÉTRICA Y DE LA PERSONA RECIÉN NACIDA QUE ESTUVO EN ATENCIÓN  PRENATAL</div></center>
        <div class="container-fluid p-2 text-center">
        <div class="row justify-content-center text-start ">
                <div class="col-md-5 m-l">
                <div class="input-group mb-3">
                <span class="input-group-text">FECHA DE LA ATENCIÓN OBSTÉTRICA:</span>
                <label for="fecha_ant_obste" class="form-label"  ></label>
                <input type="date" name="fecha_ant_obste" id="fecha_ant_obste" class="form-control"  value="<?php echo $rowcita['fecha_ant_obste']; ?>" required >
                </div>
                </div>	
        </div>	
        </div>
        <!----FIN---->

        <!----INICIO---->
        <center><div class="bg-secondary p-2 text-white bg-opacity-90 modal-title fs-5">TIPO DE ATENCIÓN:</div>

        <br>

            <center> <left> <h2 class="modal-title fs-5" id="editaModalLabel">ABORTO</h2></left></center>  
             
                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-center text-start ">  

                    <div class="col-md-3 m-l">
                    <div class="input-group mb-3">
                        <span class="input-group-text">AMEU</span>
                        <select name="aborto_ameu" id="aborto_ameu" class="form-select" >
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowcita['aborto_ameu']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowcita['aborto_ameu']) { echo 'selected'; } ?>>NO</option>   
                        </select>
                    </div>	
                    </div>
                    <div class="col-md-3 m-l">
                    <div class="input-group mb-3">
                        <span class="input-group-text">LUI</span>
                        <select name="aborto_lui" id="aborto_lui" class="form-select" >
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowcita['aborto_lui']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowcita['aborto_lui']) { echo 'selected'; } ?>>NO</option>   
                        </select>
                    </div>	
                    </div>
                    <div class="col-md-3 m-l">
                    <div class="input-group mb-3">
                        <span class="input-group-text">MEDICAMENTOS</span>
                        <select name="aborto_medicamento" id="aborto_medicamento" class="form-select" >
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowcita['aborto_medicamento']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowcita['aborto_medicamento']) { echo 'selected'; } ?>>NO</option>   
                        </select>
                    </div>	
                    </div>
         
                </div>    
                </div>            
                
                    <center> <left> <h2 class="modal-title fs-5" id="editaModalLabel">PARTO</h2></left></center> 

                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-center text-start ">  

                    <div class="col-md-3 m-l">
                    <div class="input-group mb-3">
                        <span class="input-group-text">EUTÓCICO</span>
                        <select name="parto_eutocico" id="parto_eutocico" class="form-select" >
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowcita['parto_eutocico']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowcita['parto_eutocico']) { echo 'selected'; } ?>>NO</option>   
                        </select>
                    </div>	
                    </div>

                    <div class="col-md-3 m-l">
                    <div class="input-group mb-3">
                        <span class="input-group-text">DISTÓCICO</span>
                        <select name="parto_distocico" id="parto_distocico" class="form-select" >
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowcita['parto_distocico']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowcita['parto_distocico']) { echo 'selected'; } ?>>NO</option>   
                        </select>
                    </div>	
                    </div>

                    <div class="col-md-3 m-l">
                    <div class="input-group mb-3">
                        <span class="input-group-text">CESÁREA</span>
                        <select name="parto_cesarea" id="parto_cesarea" class="form-select" >
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowcita['parto_cesarea']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowcita['parto_cesarea']) { echo 'selected'; } ?>>NO</option>   
                        </select>
                    </div>	
                    </div>     

                </div>    
                </div>     
                <hr>
                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-center text-start ">  

                    <div class="col-md-3 m-l">
                        <label for="semanas_gestacion" class="form-label">SEMANAS DE GESTACIÓN</label><label class="form-label" style="color: red">*:</label>
                        <input type="number" name="semanas_gestacion" id="semanas_gestacion" class="form-control" required value="<?php echo $rowcita['semanas_gestacion']; ?>" >                        
                    </div>

                    <div class="col-md-4 m-l">
                    <label for="metodo_anticoncep" class="form-label">ANTICONCEPCIÓN POST EVENTO OBSTÉTRICO</label><label class="form-label" style="color: red">*:</label>
                    <select name="metodo_anticoncep" id="metodo_anticoncep" class="form-select" >
                        <option value="">Seleccionar...</option>
                        <option value="SI"<?php if('SI' == $rowcita['metodo_anticoncep']) { echo 'selected'; } ?>>SI</option>
                        <option value="NO"<?php if('NO' == $rowcita['metodo_anticoncep']) { echo 'selected'; } ?>>NO</option> 
                    </select>
                    </div>	
                    
                    <div class="col-md-4 m-l">
                    <label for="metodo_especifica" class="form-label">ESPECIFICAR:</label>
                    <select name="metodo_especifica" id="metodo_especifica" class="form-select" disabled required>
                        <option value="">Seleccionar...</option>
                        <?php while ($rowANTESP = $resultadoANTESP->fetch_assoc()) { ?>
                        <option value="<?php echo $rowANTESP["id_anticonceptivoesp"]; ?>"<?php if($rowANTESP['id_anticonceptivoesp']==$mesp_concepto) { echo 'selected'; } ?>><?php echo $rowANTESP['antesp_concepto']; ?></option>
                        <?php } ?>
                    </select>
                    </div>	

                </div> 
                </div>   
              
             <hr>
                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-center text-start ">
                <center> <left> <h2 class="modal-title fs-5" id="editaModalLabel">TIPO DE TRANSPORTE UTILIZADO</h2></left></center>

                    <div class="col-md-3 m-l">
                    <label for="ambulancia" class="form-label">AMBULANCIA:</label>
                    <select name="ambulancia" id="ambulancia" class="form-select" >
                        <option value="">Seleccionar...</option>
                        <option value="SI"<?php if('SI' == $rowcita['ambulancia']) { echo 'selected'; } ?>>SI</option>
                        <option value="NO"<?php if('NO' == $rowcita['ambulancia']) { echo 'selected'; } ?>>NO</option> 
                    </select>
                    </div>
                 
                    <div class="col-md-3 m-l">
                    <label for="vehículo_particular" class="form-label">VEHÍCULO PARTICULAR:</label>
                    <select name="vehículo_particular" id="vehículo_particular" class="form-select" >
                        <option value="">Seleccionar...</option>
                        <option value="SI"<?php if('SI' == $rowcita['vehículo_particular']) { echo 'selected'; } ?>>SI</option>
                        <option value="NO"<?php if('NO' == $rowcita['vehículo_particular']) { echo 'selected'; } ?>>NO</option> 
                    </select>
                    </div>

                    <div class="col-md-3 m-l">
                    <label for="transporte_ame" class="form-label">TRANSPORTE AME:</label>
                    <select name="transporte_ame" id="transporte_ame" class="form-select" >
                        <option value="">Seleccionar...</option>
                        <option value="SI"<?php if('SI' == $rowcita['transporte_ame']) { echo 'selected'; } ?>>SI</option>
                        <option value="NO"<?php if('NO' == $rowcita['transporte_ame']) { echo 'selected'; } ?>>NO</option> 
                    </select>
                    </div>

                </div>	
                </div>      

                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-center text-start ">

                    <div class="col-md-3 m-l">
                    <label for="transporte_publico" class="form-label">TRANSPORTE PÚBLICO:</label>
                    <select name="transporte_publico" id="transporte_publico" class="form-select" >
                        <option value="">Seleccionar...</option>
                        <option value="SI"<?php if('SI' == $rowcita['transporte_publico']) { echo 'selected'; } ?>>SI</option>
                        <option value="NO"<?php if('NO' == $rowcita['transporte_publico']) { echo 'selected'; } ?>>NO</option> 
                    </select>
                    </div>
                    
                    <div class="col-md-3 m-l">
                    <label for="ambulancia_aerea" class="form-label">AMBULANCIA AÉREA:</label>
                    <select name="ambulancia_aerea" id="ambulancia_aerea" class="form-select" >
                        <option value="">Seleccionar...</option>
                        <option value="SI"<?php if('SI' == $rowcita['ambulancia_aerea']) { echo 'selected'; } ?>>SI</option>
                        <option value="NO"<?php if('NO' == $rowcita['ambulancia_aerea']) { echo 'selected'; } ?>>NO</option> 
                    </select>
                    </div>
                </div>	
                </div>
           
                <hr>

                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-start text-start ">

                    <div class="col-md-12 m-l">
                        <div class="input-group mb-3">
                        <span class="input-group-text">ATENDIDO EN<label class="form-label" style="color: red">*:</label></span>
                        <select name="atendido_en" id="atendido_en" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <?php while ($rowATNEN = $resultadoATNEN->fetch_assoc()) { ?>
                            <option value="<?php echo $rowATNEN["id_atendidoen"]; ?>"<?php if($rowATNEN['id_atendidoen']==$atnen_concepto) { echo 'selected'; } ?>><?php echo $rowATNEN['atnen_concepto']; ?></option>
                            <?php } ?>
                        </select>
                        </div>
                    </div>
                </div>
                </div>

                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-start text-start ">

                    <div class="col-md-12 m-l">
                        <div class="input-group mb-3">   
                        <span class="input-group-text">ATENDIDO POR<label class="form-label" style="color: red">*:</label></span>
                        <select name="atendido_por" id="atendido_por" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <?php while ($rowATNPOR = $resultadoATNPOR->fetch_assoc()) { ?>
                            <option value="<?php echo $rowATNPOR["id_atendidopor"]; ?>"<?php if($rowATNPOR['id_atendidopor']==$atnpor_concepto) { echo 'selected'; } ?>><?php echo $rowATNPOR['atnpor_concepto']; ?></option>
                            <?php } ?>
                        </select>
                        </div>	
                    </div>
                </div>
                </div>

                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-start text-start ">

                    <div class="col-md-12 m-l">
                        <div class="input-group mb-3">
                        <span class="input-group-text">COMPLICACIONES<label class="form-label" style="color: red">*:</label></span>
                        <select name="complicaciones" id="complicaciones" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <?php while ($rowCOMPLIST = $resultadoCOMPLIST->fetch_assoc()) { ?>
                            <option value="<?php echo $rowCOMPLIST["id_complicacioneslist"]; ?>"<?php if($rowCOMPLIST['id_complicacioneslist']==$listcomp_concepto) { echo 'selected'; } ?>><?php echo $rowCOMPLIST['compli_concepto']; ?></option>
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

    <script>
         $(document).ready(function () {
         $('#metodo_anticoncep').change(function (e) {

            if ($(this).val() === "SI") {
                $('#metodo_especifica').prop("disabled", false);
                $('#metodo_especifica').val(''); 
                $('#metodo_especifica').focus();                          
            } else {
                $('#metodo_especifica').val('0'); 
                $('#metodo_especifica').prop("disabled", true);  
                $('#ambulancia').focus();  
            }
           
            })
        });
    </script>
    <script>
            $(document).ready(function () {
            $('#aborto_ameu').change(function (e) {
            if ($(this).val() === "SI") {
                $('#aborto_lui').val('NO'); 
                $('#aborto_medicamento').val('NO'); 
                $('#parto_eutocico').val('NO');
                $('#parto_distocico').val('NO'); 
                $('#parto_cesarea').val('NO');                           
            }           
            })
            $('#aborto_lui').change(function (e) {
            if ($(this).val() === "SI") {
                $('#aborto_ameu').val('NO'); 
                $('#aborto_medicamento').val('NO');   
                $('#parto_eutocico').val('NO');
                $('#parto_distocico').val('NO'); 
                $('#parto_cesarea').val('NO');                         
            }           
            })
            $('#aborto_medicamento').change(function (e) {
            if ($(this).val() === "SI") {
                $('#aborto_ameu').val('NO'); 
                $('#aborto_lui').val('NO');    
                $('#parto_eutocico').val('NO');
                $('#parto_distocico').val('NO'); 
                $('#parto_cesarea').val('NO');                        
            }           
            })

            $('#parto_eutocico').change(function (e) {
            if ($(this).val() === "SI") {
                $('#parto_distocico').val('NO'); 
                $('#parto_cesarea').val('NO'); 
                $('#aborto_ameu').val('NO');  
                $('#aborto_lui').val('NO'); 
                $('#aborto_medicamento').val('NO');                         
            }           
            })
            $('#parto_distocico').change(function (e) {
            if ($(this).val() === "SI") {
                $('#parto_eutocico').val('NO'); 
                $('#parto_cesarea').val('NO');   
                $('#aborto_ameu').val('NO');  
                $('#aborto_lui').val('NO'); 
                $('#aborto_medicamento').val('NO');                           
            }           
            })
            $('#parto_cesarea').change(function (e) {
            if ($(this).val() === "SI") {
                $('#parto_eutocico').val('NO'); 
                $('#parto_distocico').val('NO'); 
                $('#aborto_ameu').val('NO');  
                $('#aborto_lui').val('NO'); 
                $('#aborto_medicamento').val('NO');                             
            }           
            })

            $('#ambulancia').change(function (e) {
            if ($(this).val() === "SI") {
                $('#vehículo_particular').val('NO'); 
                $('#transporte_ame').val('NO');  
                $('#transporte_publico').val('NO'); 
                $('#ambulancia_aerea').val('NO');  
                $('#aborto_ameu').val('NO');  
                $('#aborto_lui').val('NO'); 
                $('#aborto_medicamento').val('NO');                            
            }           
            })
            $('#vehículo_particular').change(function (e) {
            if ($(this).val() === "SI") {
                $('#ambulancia').val('NO'); 
                $('#transporte_ame').val('NO');  
                $('#transporte_publico').val('NO'); 
                $('#ambulancia_aerea').val('NO');                          
            }           
            })
            $('#transporte_ame').change(function (e) {
            if ($(this).val() === "SI") {
                $('#vehículo_particular').val('NO'); 
                $('#ambulancia').val('NO');  
                $('#transporte_publico').val('NO'); 
                $('#ambulancia_aerea').val('NO');                          
            }           
            })
            $('#transporte_publico').change(function (e) {
            if ($(this).val() === "SI") {
                $('#vehículo_particular').val('NO'); 
                $('#transporte_ame').val('NO');  
                $('#ambulancia').val('NO'); 
                $('#ambulancia_aerea').val('NO');                          
            }           
            })
            $('#ambulancia_aerea').change(function (e) {
            if ($(this).val() === "SI") {
                $('#vehículo_particular').val('NO'); 
                $('#transporte_ame').val('NO');  
                $('#transporte_publico').val('NO'); 
                $('#ambulancia').val('NO');                          
            }           
            })


        });
    </script>
    <script>
        $(document).change(function () 
        {
            if (($('#aborto_ameu').val()!= 'SI')&&($('#aborto_lui').val()!= 'SI')&&($('#aborto_medicamento').val()!= 'SI')&&($('#parto_eutocico').val()!= 'SI')&&($('#parto_distocico').val()!= 'SI')&&($('#parto_cesarea').val()!= 'SI'))  {
                $('#enviar').prop("disabled", true);                      
            } else if (($('#ambulancia').val()!= 'SI')&&($('#vehículo_particular').val()!= 'SI')&&($('#transporte_ame').val()!= 'SI')&&($('#transporte_publico').val()!= 'SI')&&($('#ambulancia_aerea').val()!= 'SI'))  {
                $('#enviar').prop("disabled", true);  
            } else{
                     $('#enviar').prop("disabled", false); 
                
            }  
        });
    </script>
    
    <script>
        $(document).ready(function () {
        $('#enviar').on('click', function(e){
            $('#metodo_especifica').prop("disabled", false);
        })
        });     
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