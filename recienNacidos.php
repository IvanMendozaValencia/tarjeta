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

$sqllistarn = "SELECT id_recien_nacida, rn_vivo, rn_muerto, rn_sexo, rn_peso, rn_talla, rn_apgar, rn_silverman, rn_tamiz_metabolico, rn_tamiz_auditivo, fk_id_tarjeta, srn_concepto  FROM recien_nacida INNER JOIN datos_identificacion ON id_tarjeta =fk_id_tarjeta INNER JOIN sexo_rn ON sexo_rn.id_sexo_rn = recien_nacida.rn_sexo WHERE id_tarjeta = $id ";
$listarn = $mysqli->query($sqllistarn);


$sqltarjeta = "SELECT id_tarjeta, rn_unico, rn_gemelar, rn_tres_mas, rn_apego_seno_amt, rn_egreso_lac_mat_exc, rn_sucedaneo_leche_mat_b24x, rn_tratamiento_b24x, rn_tratamiento_a539  FROM datos_identificacion  WHERE id_tarjeta = $id LIMIT 1";
$tarejeta = $mysqli->query($sqltarjeta);
$rowtarjeta= $tarejeta->fetch_assoc();
$id_tarjeta = $rowtarjeta['id_tarjeta']; 


$sqlpaciente = "SELECT id_tarjeta, CONCAT_WS(' ', nombre, primer_apellido, segundo_apellido) AS nombre_paciente FROM datos_identificacion
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
       
        <br>

        <div class="row justify-content-end">        
            <div class="col-auto">             
                <form action="citaprenatal.php" method="GET"> 

                    <input type="hidden"   name='id_tarjeta' value=<?= $rowtarjeta['id_tarjeta']; ?>> 

                    <?php if($row_usuario['a_agregar']==1):  ?>
                            <a href="#" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#nuevoModalRecienN" data-bs-id="<?= $rowtarjeta['id_tarjeta']; ?>"><i class="fa-solid fa-baby"></i> Agregar recién nacido</a> </td>
                    <?php endif;?>

                    <?php if($row_usuario['a_agregar']==0):  ?>
                            <a href="#" class="btn btn-outline-warning disabled" data-bs-toggle="modal" data-bs-target="#nuevoModalRecienN" data-bs-id="<?= $rowtarjeta['id_tarjeta']; ?>" ><i class="fa-solid fa-baby"></i> Agregar recién nacido</a> </td>
                    <?php endif;?>
                </form> 
            </div>
     
            <div class="col-auto">  
                <form action="editaTarjetaR.php" method="GET"> 
                     
                     <input type="hidden"   name='id_tarjeta' value=<?= $rowtarjeta['id_tarjeta']; ?>> 
 
                     <?php if($row_usuario['a_agregar']==1):  ?>
                     <Button type ="submit" class="btn btn-secondary"><i class="fa-solid fa-file-medical"></i> Reverso</Button>
                     <?php endif;?>
 
                     <?php if($row_usuario['a_agregar']==0):  ?>
                     <Button type ="submit" class="btn btn-secondary disabled"><i class="fa-solid fa-file-medical"></i> Reverso</Button>
                     <?php endif;?> 
                </form>     
            </div>
        </div>
        
        <br>
        
        <form id="reciennacidos" name="reciennacidos" action="actualizaRencienN.php" method="POST">

        <input type="hidden" name="id_tarjeta" id="id_tarjeta" class="form-control" value="<?php echo $rowtarjeta['id_tarjeta']; ?>" > 
        <input type="hidden" name="clues_id" id="clues_id" class="form-control" value="<?php echo $row_usuario['CLUES']; ?>" >    

        <!----INICIO---->
        <center><div class="bg-black p-2 text-white bg-opacity-90 modal-title fs-5">DATOS DE LA PERSONA RECIÉN NACIDA</div></center>

        <br>             
                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-center text-start ">  

                    <div class="col-md-3 m-l">
                    <div class="input-group mb-3">
                        <span class="input-group-text">ÚNICO</span>
                        <select name="rn_unico" id="rn_unico" class="form-select" >
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowtarjeta['rn_unico']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowtarjeta['rn_unico']) { echo 'selected'; } ?>>NO</option>   
                        </select>
                    </div>	
                    </div>
                    <div class="col-md-3 m-l">
                    <div class="input-group mb-3">
                        <span class="input-group-text">GEMELAR</span>
                        <select name="rn_gemelar" id="rn_gemelar" class="form-select" >
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowtarjeta['rn_gemelar']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowtarjeta['rn_gemelar']) { echo 'selected'; } ?>>NO</option>   
                        </select>
                    </div>	
                    </div>
                    <div class="col-md-3 m-l">
                    <div class="input-group mb-3">
                        <span class="input-group-text">TRES O MÁS</span>
                        <select name="rn_tres_mas" id="rn_tres_mas" class="form-select" >
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowtarjeta['rn_tres_mas']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowtarjeta['rn_tres_mas']) { echo 'selected'; } ?>>NO</option>   
                        </select>
                    </div>	
                    </div>
         
                </div>    
                </div>      

                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-center text-start ">  

                    <div class="col-md-4 m-l">
                    <label for="rn_apego_seno_amt" class="form-label">APEGO INMEDIATO AL SENO MATERNO:</label>
                        <select name="rn_apego_seno_amt" id="rn_apego_seno_amt" class="form-select" >
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowtarjeta['rn_apego_seno_amt']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowtarjeta['rn_apego_seno_amt']) { echo 'selected'; } ?>>NO</option>   
                        </select>
                    </div>
                 
                    <div class="col-md-4 m-l">
                    <label for="rn_egreso_lac_mat_exc" class="form-label">EGRESO CON LACTANCIA MATERNA EXCLUSIVA:</label>
                        <select name="rn_egreso_lac_mat_exc" id="rn_egreso_lac_mat_exc" class="form-select" >
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowtarjeta['rn_egreso_lac_mat_exc']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowtarjeta['rn_egreso_lac_mat_exc']) { echo 'selected'; } ?>>NO</option>   
                        </select>
                    </div>

                    <div class="col-md-4 m-l">
                    <label for="rn_sucedaneo_leche_mat_b24x" class="form-label">SUCEDÁNEO DE LA LECHE MATERNA POR B24X:</label>
                        <select name="rn_sucedaneo_leche_mat_b24x" id="rn_sucedaneo_leche_mat_b24x" class="form-select" >
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowtarjeta['rn_sucedaneo_leche_mat_b24x']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowtarjeta['rn_sucedaneo_leche_mat_b24x']) { echo 'selected'; } ?>>NO</option>   
                        </select>	
                    </div>

                </div>    
                </div>  

                <center><left> <h3 class="modal-title fs-5" id="editaModalLabel"> LA O (LAS) PERSONA (S) RECIÉN NACIDA RECIBIERON TRATAMIENTO PROFILÁCTICO PARA:</h3></left></center>

                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-center text-start ">  

                    <div class="col-md-3 m-l">
                    <div class="input-group mb-3">
                        <span class="input-group-text">B24X</span>
                        <select name="rn_tratamiento_b24x" id="rn_tratamiento_b24x" class="form-select" >
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowtarjeta['rn_tratamiento_b24x']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowtarjeta['rn_tratamiento_b24x']) { echo 'selected'; } ?>>NO</option>   
                        </select>
                    </div>	
                    </div>

                    <div class="col-md-3 m-l">
                    <div class="input-group mb-3">
                        <span class="input-group-text">A539</span>
                        <select name="rn_tratamiento_a539" id="rn_tratamiento_a539" class="form-select" >
                            <option value="">Seleccionar...</option>
                            <option value="SI"<?php if('SI' == $rowtarjeta['rn_tratamiento_a539']) { echo 'selected'; } ?>>SI</option>
                            <option value="NO"<?php if('NO' == $rowtarjeta['rn_tratamiento_a539']) { echo 'selected'; } ?>>NO</option>   
                        </select>
                    </div>	
                    </div>                
                </div>	
                </div>

                <div class="container-fluid p-2 text-center">
                    <div class="row justify-content-center text-center ">
                         <div cclass="col-md-3 m-l">
                            <button type="submit" class="btn btn-outline-primary" id="enviar" name="enviar" disabled ><i class="fa-solid fa-floppy-disk"></i> Guardar</button>
                        </div>
                    </div>
                </div>	

    </form>

                <table id="tabla" class="display" style="width:100%">
                <thead>
                    <tr align="center">
                    <th COLSPAN=2>Condición al Nacimiento</th>
                    <th COLSPAN=3></th>
                    <th COLSPAN=2>A los 5 Minutos</th>
                    <th COLSPAN=2>Tamizaje</th>
                    <th COLSPAN=2>Acciones</th>
                    </tr>
                    <th style="width:15px">Vivo</th>                    
                    <th style="width:15px">Muerto</th>
                    <th style="width:15px">Sexo</th>
                    <th style="width:18px">Peso gr</th>
                    <th style="width:18px">Talla cm</th>
                    <th style="width:15px">Apgar</th>
                    <th style="width:15px">Silverman</th>
                    <th style="width:15px">Metabólico</th>
                    <th style="width:15px">Auditivo</th>
                    <th style="width:13px">Editar</th>
                    <th style="width:13px">Eliminar</th>
                    </tr>
                 </thead>
                
                <tbody>
                    <?php while($row = $listarn->fetch_assoc()) { ?>
                    <tr>
                    <?php $row['id_recien_nacida']; ?>

                    <td align="center"><?php echo utf8_encode(utf8_decode($row['rn_vivo'])); ?></td>
                    <td align="center"><?php echo utf8_encode(utf8_decode($row['rn_muerto'])); ?></td>
                    <td align="center"><?php echo utf8_encode(utf8_decode($row['srn_concepto'])); ?></td>
                    <td align="center"><?php echo utf8_encode(utf8_decode($row['rn_peso'])); ?></td>
                    <td align="center"><?php echo utf8_encode(utf8_decode($row['rn_talla'])); ?></td>
                   
                    <?php if(($row['rn_apgar']==1)||($row['rn_apgar']==2)||($row['rn_apgar']==3)):  ?>
                        <td align="center"style="background-color: #FF333F";><?php echo utf8_encode(utf8_decode($row['rn_apgar'])); ?></td>            
                    <?php endif;?>
                    <?php if(($row['rn_apgar']==4)||($row['rn_apgar']==5)||($row['rn_apgar']==6)):  ?>
                        <td align="center"style="background-color: #FFFF00";><?php echo utf8_encode(utf8_decode($row['rn_apgar'])); ?></td>            
                    <?php endif;?>
                    <?php if(($row['rn_apgar']==7)||($row['rn_apgar']==8)||($row['rn_apgar']==9)||($row['rn_apgar']==10)):  ?>
                        <td align="center"style="background-color: #9ACD32";><?php echo utf8_encode(utf8_decode($row['rn_apgar'])); ?></td>            
                    <?php endif;?>

                    <td align="center"><?php echo utf8_encode(utf8_decode($row['rn_silverman'])); ?></td>
                    <td align="center"><?php echo utf8_encode(utf8_decode($row['rn_tamiz_metabolico'])); ?></td>
                    <td align="center"><?php echo utf8_encode(utf8_decode($row['rn_tamiz_auditivo'])); ?></td>

                  
                    <?php if($row_usuario['a_modificar']==1):  ?>
                    <td align="center"><a href="#" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editaModalRecienN" data-bs-id="<?= $row['id_recien_nacida']; ?>"><i class="fa-solid fa-baby"></i> </a></td>
                    <?php endif;?>
                    <?php if($row_usuario['a_modificar']==0):  ?>
                    <td align="center"><a href="#" class="btn btn-sm btn-warning disabled" data-bs-toggle="modal" data-bs-target="#editaModalRecienN" data-bs-id="<?= $row['id_recien_nacida']; ?>"><i class="fa-solid fa-baby"></i></a></td>
                    <?php endif;?>
                    
                    <?php if($row_usuario['a_eliminar']==1):  ?>
                    <td align="center"><a href="#" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#eliminaModalRecienN" data-bs-id="<?= $row['id_recien_nacida']; ?>"><i class="fa-solid fa-trash"></i> </a></td>
                    <?php endif;?>
                    <?php if($row_usuario['a_eliminar']==0):  ?>
                    <td align="center"><a href="#" class="btn btn-sm btn-danger disabled" data-bs-toggle="modal" data-bs-target="#eliminaModalRecienN" data-bs-id="<?= $row['id_recien_nacida']; ?>"><i class="fa-solid fa-trash"></i></a></td>
                    <?php endif;?>
                    </tr>
                     <?php } ?>

                        </tbody>
                </table>

<?php 
$querySRN= "SELECT id_sexo_rn, srn_concepto FROM sexo_rn ORDER BY id_sexo_rn";
$listarsrn = $mysqli->query($querySRN); 
?>

<?php include 'nuevoModalRecienN.php'; ?>
<?php $listarsrn->data_seek(0); ?>
<?php include 'editaModalRecienN.php'; ?>
<?php include 'eliminaModalRecienN.php'; ?>

<script>
    let nuevoModal = document.getElementById('nuevoModalRecienN')
    let editaModal = document.getElementById('editaModalRecienN')
    let eliminaModal = document.getElementById('eliminaModalRecienN')

    nuevoModal.addEventListener('shown.bs.modal', event => {
        nuevoModal.querySelector('.modal-body #rn_vivo').focus()
    })
    nuevoModal.addEventListener('hide.bs.modal', event => {
        nuevoModal.querySelector('.modal-body #rn_vivo').value = ""
        nuevoModal.querySelector('.modal-body #rn_muerto').value = ""
        nuevoModal.querySelector('.modal-body #rn_sexo').value = ""
        nuevoModal.querySelector('.modal-body #rn_peso').value = ""
        nuevoModal.querySelector('.modal-body #rn_talla').value = ""
        nuevoModal.querySelector('.modal-body #rn_apgar').value = ""
        nuevoModal.querySelector('.modal-body #rn_silverman').value = ""
        nuevoModal.querySelector('.modal-body #rn_tamiz_metabolico').value = ""
        nuevoModal.querySelector('.modal-body #rn_tamiz_auditivo').value = ""
       
    })

    nuevoModal.addEventListener('shown.bs.modal', event => {
        let button = event.relatedTarget
            let id = button.getAttribute('data-bs-id')
            let inputId = nuevoModal.querySelector('.modal-body #id_tarjeta')            
            let url = "getIdtarjetas_rn.php"
            let formData = new FormData()
            formData.append('id_tarjeta', id)
            
            fetch(url, {
                    method: "POST",
                    body: formData
                }).then(response => response.json())
                .then(data => {
                    inputId.value = data.id_tarjeta
                }).catch(err => console.log(err))

        })

        editaModal.addEventListener('hide.bs.modal', event => {
        editaModal.querySelector('.modal-body #rn_vivo1').value = ""
        editaModal.querySelector('.modal-body #rn_muerto1').value = ""
        editaModal.querySelector('.modal-body #rn_sexo').value = ""
        editaModal.querySelector('.modal-body #rn_peso').value = ""
        editaModal.querySelector('.modal-body #rn_talla').value = ""
        editaModal.querySelector('.modal-body #rn_apgar').value = ""
        editaModal.querySelector('.modal-body #rn_silverman').value = ""
        editaModal.querySelector('.modal-body #rn_tamiz_metabolico').value = ""
        editaModal.querySelector('.modal-body #rn_tamiz_auditivo').value = ""    
        editaModal.querySelector('.modal-body #fk_id_tarjeta').value = ""                     

    })


    editaModal.addEventListener('shown.bs.modal', event => {
        let button = event.relatedTarget
        let id = button.getAttribute('data-bs-id')

        let inputId = editaModal.querySelector('.modal-body #id_recien_nacida')
        let inputrn_vivo= editaModal.querySelector('.modal-body #rn_vivo1')
        let inputrn_muerto = editaModal.querySelector('.modal-body #rn_muerto1') 
        let inputrn_sexo = editaModal.querySelector('.modal-body #rn_sexo') 
        let inputrn_peso = editaModal.querySelector('.modal-body #rn_peso')
        let inputrn_talla= editaModal.querySelector('.modal-body #rn_talla')
        let inputrn_apgar = editaModal.querySelector('.modal-body #rn_apgar') 
        let inputrn_silverman = editaModal.querySelector('.modal-body #rn_silverman') 
        let inputrn_tamiz_metabolico = editaModal.querySelector('.modal-body #rn_tamiz_metabolico')
        let inputrn_tamiz_auditivo = editaModal.querySelector('.modal-body #rn_tamiz_auditivo')
        let inputfk_id_tarjeta = editaModal.querySelector('.modal-body #fk_id_tarjeta')
        let url = "getRecienNac.php"
        let formData = new FormData()
        formData.append('id_recien_nacida', id)

        fetch(url, {
                method: "POST",
                body: formData
            }).then(response => response.json())
            .then(data => {
                inputId.value = data.id_recien_nacida                    
                inputrn_vivo.value = data.rn_vivo
                inputrn_muerto.value = data.rn_muerto
                inputrn_sexo.value = data.rn_sexo
                inputrn_peso.value = data.rn_peso
                inputrn_talla.value = data.rn_talla
                inputrn_apgar.value = data.rn_apgar
                inputrn_silverman.value = data.rn_silverman
                inputrn_tamiz_metabolico.value = data.rn_tamiz_metabolico
                inputrn_tamiz_auditivo.value = data.rn_tamiz_auditivo    
                inputfk_id_tarjeta.value = data.fk_id_tarjeta      
            }).catch(err => console.log(err))

    })

    eliminaModal.addEventListener('shown.bs.modal', event => {
        let button = event.relatedTarget
        let id = button.getAttribute('data-bs-id')
        eliminaModal.querySelector('.modal-footer #id_recien_nacida').value = id
    })


</script>     

<script>
        $(document).ready(function () {
         $('#rn_unico').change(function (e) {
            
            if ($(this).val() === 'SI' ) {
                $('#rn_gemelar').val('NO');
                $('#rn_tres_mas').val('NO');    
                $('#enviar').prop("disabled", false);              
                $('#rn_apego_seno_amt').focus();                          
            } 
            else{
                $('#rn_gemelar').val('');
                $('#rn_tres_mas').val('');  
                $('#enviar').prop("disabled", true);  
            }
            })
        });
        $(document).ready(function () {
         $('#rn_gemelar').change(function (e) {
            
            if ($(this).val() === 'SI' ) {
                $('#rn_unico').val('NO');
                $('#rn_tres_mas').val('NO');    
                $('#enviar').prop("disabled", false);             
                $('#rn_apego_seno_amt').focus();                          
            } 
            else{
                $('#rn_unico').val('');
                $('#rn_tres_mas').val(''); 
                $('#enviar').prop("disabled", true); 
            }
            })
        });
        $(document).ready(function () {
         $('#rn_tres_mas').change(function (e) {
            
            if ($(this).val() === 'SI' ) {
                $('#rn_gemelar').val('NO');
                $('#rn_unico').val('NO'); 
                $('#enviar').prop("disabled", false);               
                $('#rn_apego_seno_amt').focus();                          
            }
             else{
                $('#rn_gemelar').val('');
                $('#rn_unico').val('');  
                $('#enviar').prop("disabled", true); 
            }
            })
        });
</script>

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