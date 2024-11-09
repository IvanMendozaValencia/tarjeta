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

$sqlperiodo_lac= "SELECT id_periodo_lactancia, pl_fecha, pl_peso, pl_lac_mat_exc, pl_leche_mat_b24x, pl_sig_sin, pl_medicamentos, pl_observaciones, fk_id_tarjeta, ssapl_concepto, medpl_concepto   FROM periodo_lactancia INNER JOIN datos_identificacion ON id_tarjeta =fk_id_tarjeta INNER JOIN medicamentos_puer_lact ON medicamentos_puer_lact.id_medicamentos_puer_lact = periodo_lactancia.pl_medicamentos INNER JOIN sig_sin_alarma_puer_lact ON sig_sin_alarma_puer_lact.id_sig_sin_alarma_puer_lact = periodo_lactancia.pl_sig_sin WHERE id_tarjeta = $id ";
$listaperiodo_lac = $mysqli->query($sqlperiodo_lac);

$sqlperiodo_lact = "SELECT id_tarjeta, pl_fecha_baja, pl_termino_lact, pl_otro  FROM datos_identificacion  WHERE id_tarjeta = $id LIMIT 1";
$periodo_alctancia = $mysqli->query($sqlperiodo_lact);
$rowperiodo= $periodo_alctancia->fetch_assoc();
$id_tarjeta = $rowperiodo['id_tarjeta']; 


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

                    <input type="hidden"   name='id_tarjeta' value=<?= $rowperiodo['id_tarjeta']; ?>> 

                    <?php if($row_usuario['a_agregar']==1):  ?>
                            <a href="#" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#nuevoModalCitaLactancia" data-bs-id="<?= $rowperiodo['id_tarjeta']; ?>"><i class="fa-solid fa-person-breastfeeding"></i> Agregar atención</a> </td>
                    <?php endif;?>

                    <?php if($row_usuario['a_agregar']==0):  ?>
                            <a href="#" class="btn btn-outline-info disabled" data-bs-toggle="modal" data-bs-target="#nuevoModalCitaLactancia" data-bs-id="<?= $rowperiodo['id_tarjeta']; ?>" ><i class="fa-solid fa-person-breastfeeding"></i> Agregar atención</a> </td>
                    <?php endif;?>
                </form> 
            </div>
     
            <div class="col-auto">  
                <form action="editaTarjetaR.php" method="GET"> 
                     
                     <input type="hidden"   name='id_tarjeta' value=<?= $rowperiodo['id_tarjeta']; ?>> 
 
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
        
            <form id="periodolactancia" name="periodolactancia" action="actualizaPeriodoLactancia.php" method="POST">

             <input type="hidden" name="id_tarjeta" id="id_tarjeta" class="form-control" value="<?php echo $rowperiodo['id_tarjeta']; ?>" > 
             <input type="hidden" name="clues_id" id="clues_id" class="form-control" value="<?php echo $row_usuario['CLUES']; ?>" >    

        <!----INICIO---->
             <center><div class="bg-black p-2 text-white bg-opacity-90 modal-title fs-5">ATENCIÓN EN EL PERIODO DE LACTANCIA</div></center>

             <br>            
        
                <center> <div class="bg-secondary p-2 text-white bg-opacity-90 modal-title fs-5">BAJA</div></center>
                <div class="container-fluid p-2 text-center">
                <div class="row justify-content-center text-start ">
                        
                    <div class="col-md-3 m-l">
                    <div class="input-group mb-3">
                        <span class="input-group-text">FECHA:</span>
                        <label for="pl_fecha_baja" class="form-label"  ></label>
                        <input type="date" name="pl_fecha_baja" id="pl_fecha_baja" class="form-control" value="<?php echo $rowperiodo['pl_fecha_baja']; ?>" >
                    </div>
                    </div>	

                    <div class="col-md-4 m-l">
                    <div class="input-group mb-3">
                        <span class="input-group-text">MOTIVO:</span>
                        <select name="pl_termino_lact" id="pl_termino_lact" class="form-select">
                            <option value="">Seleccionar...</option>
                            <option value="1"<?php if('1' == $rowperiodo['pl_termino_lact']) { echo 'selected'; } ?>>1.TERMINO DE LACTANCIA</option>
                            <option value="2"<?php if('2' == $rowperiodo['pl_termino_lact']) { echo 'selected'; } ?>>2.OTRO</option>
                        </select>
                    </div>
                    </div>   
                    
                    <div class="col-md-5 m-l">
                    <div class="input-group mb-3">
                        <span class="input-group-text">ESPECIFIQUE<label class="form-label" style="color: red">*:</label></span>
                        <input  type="text" name="pl_otro" id="pl_otro"  class="form-control" disabled required value="<?php echo $rowperiodo['pl_otro']; ?>">                        
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

                <table id="tabla" class="display" style="width:100%">
                    <thead>
                        <tr align="center">
                        <th style="width:15px">Fecha</th>                    
                        <th style="width:15px">Peso</th>
                        <th style="width:18px">Lactancia Materna Exclusica</th>
                        <th style="width:18px">Sucedáneos de Leche Materna Por B24x</th>
                        <th style="width:15px">Signos y Sintomas de Alarma</th>
                        <th style="width:18px">Medicamentos</th>
                        <th style="width:15px">Observaciones</th>
                        <th style="width:10px">Editar</th>
                        <th style="width:10px">Eliminar</th>
                        </tr>
                    </thead>
                
                    <tbody>
                        <?php while($row = $listaperiodo_lac->fetch_assoc()) { ?>
                        <tr>
                        <?php $row['id_periodo_lactancia']; ?>
                     
                        <td align="center"><?php echo utf8_encode(utf8_decode($row['pl_fecha'])); ?></td>
                        <td align="center"><?php echo utf8_encode(utf8_decode($row['pl_peso'])); ?></td>
                        <td align="center"><?php echo utf8_encode(utf8_decode($row['pl_lac_mat_exc'])); ?></td>
                        <td align="center"><?php echo utf8_encode(utf8_decode($row['pl_leche_mat_b24x'])); ?></td>
                        <td align="center"><?php echo utf8_encode(utf8_decode($row['ssapl_concepto'])); ?></td> 
                        <td align="center"><?php echo utf8_encode(utf8_decode($row['medpl_concepto'])); ?></td>
                        <td align="center"><?php echo utf8_encode(utf8_decode($row['pl_observaciones'])); ?></td>
                        <?php if($row_usuario['a_modificar']==1):  ?>
                        <td align="center"><a href="#" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editaModalCitaLactancia" data-bs-id="<?= $row['id_periodo_lactancia']; ?>"><i class="fa-solid fa-person-breastfeeding"></i></a></td>
                        <?php endif;?>
                        <?php if($row_usuario['a_modificar']==0):  ?>
                        <td align="center"><a href="#" class="btn btn-sm btn-warning disabled" data-bs-toggle="modal" data-bs-target="#editaModalCitaLactancia" data-bs-id="<?= $row['id_periodo_lactancia']; ?>"><i class="fa-solid fa-person-breastfeeding"></i></a></td>
                        <?php endif;?>
                        
                        <?php if($row_usuario['a_eliminar']==1):  ?>
                        <td align="center"><a href="#" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#eliminaModalCitaLactancia" data-bs-id="<?= $row['id_periodo_lactancia']; ?>"><i class="fa-solid fa-trash"></i> </a></td>
                        <?php endif;?>
                        <?php if($row_usuario['a_eliminar']==0):  ?>
                        <td align="center"><a href="#" class="btn btn-sm btn-danger disabled" data-bs-toggle="modal" data-bs-target="#eliminaModalCitaLactancia" data-bs-id="<?= $row['id_periodo_lactancia']; ?>"><i class="fa-solid fa-trash"></i></a></td>
                        <?php endif;?>
                        </tr>
                        <?php } ?>

                    </tbody>
                </table>

                <?php 
$querySSAPL= "SELECT id_sig_sin_alarma_puer_lact, ssapl_concepto FROM sig_sin_alarma_puer_lact ORDER BY id_sig_sin_alarma_puer_lact";
$listaSSAPL = $mysqli->query($querySSAPL);
?>   
<?php 
$queryMEDPL= "SELECT id_medicamentos_puer_lact, medpl_concepto FROM medicamentos_puer_lact ORDER BY id_medicamentos_puer_lact";
$listaMEDPL = $mysqli->query($queryMEDPL);
?>
            
<?php include 'nuevoModalCitaLactancia.php'; ?>
<?php $listaSSAPL->data_seek(0); ?>
<?php $listaMEDPL->data_seek(0); ?>
<?php include 'editaModalCitaLactancia.php'; ?>
<?php include 'eliminaModalCitaLactancia.php'; ?>

<script>
    let nuevoModal = document.getElementById('nuevoModalCitaLactancia')
    let editaModal = document.getElementById('editaModalCitaLactancia')
    let eliminaModal = document.getElementById('eliminaModalCitaLactancia')

    nuevoModal.addEventListener('shown.bs.modal', event => {
        nuevoModal.querySelector('.modal-body #pl_fecha').focus()
    })
    nuevoModal.addEventListener('hide.bs.modal', event => {
        nuevoModal.querySelector('.modal-body #pl_fecha').value = ""
        nuevoModal.querySelector('.modal-body #pl_peso').value = ""
        nuevoModal.querySelector('.modal-body #pl_lac_mat_exc').value = ""
        nuevoModal.querySelector('.modal-body #pl_leche_mat_b24x').value = ""
        nuevoModal.querySelector('.modal-body #pl_sig_sin').value = ""
        nuevoModal.querySelector('.modal-body #pl_medicamentos').value = ""
        nuevoModal.querySelector('.modal-body #pl_observaciones').value = ""
       
    })
    
    nuevoModal.addEventListener('shown.bs.modal', event => {
        let button = event.relatedTarget
            let id = button.getAttribute('data-bs-id')
            let inputId = nuevoModal.querySelector('.modal-body #id_tarjeta')            
            let url = "getIdtarjetas_lactancia.php"
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
        editaModal.querySelector('.modal-body #pl_fecha').value = ""
        editaModal.querySelector('.modal-body #pl_peso').value = ""
        editaModal.querySelector('.modal-body #pl_lac_mat_exc').value = ""
        editaModal.querySelector('.modal-body #pl_leche_mat_b24x').value = ""
        editaModal.querySelector('.modal-body #pl_sig_sin').value = ""
        editaModal.querySelector('.modal-body #pl_medicamentos').value = ""
        editaModal.querySelector('.modal-body #pl_observaciones').value = ""
        editaModal.querySelector('.modal-body #fk_id_tarjeta').value = ""                     

    })


    editaModal.addEventListener('shown.bs.modal', event => {
        let button = event.relatedTarget
        let id = button.getAttribute('data-bs-id')
        
        let inputId = editaModal.querySelector('.modal-body #id_periodo_lactancia')
        let inputpl_fecha = editaModal.querySelector('.modal-body #pl_fecha')
        let inputpl_peso = editaModal.querySelector('.modal-body #pl_peso') 
        let inputpl_lac_mat_exc = editaModal.querySelector('.modal-body #pl_lac_mat_exc') 
        let inputpl_leche_mat_b24x = editaModal.querySelector('.modal-body #pl_leche_mat_b24x')
        let inputpl_sig_sin = editaModal.querySelector('.modal-body #pl_sig_sin')
        let inputpl_medicamentos = editaModal.querySelector('.modal-body #pl_medicamentos') 
        let inputpl_observaciones = editaModal.querySelector('.modal-body #pl_observaciones') 
        let inputfk_id_tarjeta = editaModal.querySelector('.modal-body #fk_id_tarjeta')
        let url = "getPeriodoLactancia.php"
        let formData = new FormData()
        formData.append('id_periodo_lactancia', id)
       
        fetch(url, {
                method: "POST",
                body: formData
            }).then(response => response.json())
            .then(data => {
                inputId.value = data.id_periodo_lactancia                    
                inputpl_fecha.value = data.pl_fecha
                inputpl_peso.value = data.pl_peso
                inputpl_lac_mat_exc.value = data.pl_lac_mat_exc
                inputpl_leche_mat_b24x.value = data.pl_leche_mat_b24x
                inputpl_sig_sin.value = data.pl_sig_sin
                inputpl_medicamentos.value = data.pl_medicamentos
                inputpl_observaciones.value = data.pl_observaciones    
                inputfk_id_tarjeta.value = data.fk_id_tarjeta       
            }).catch(err => console.log(err))

        })

    eliminaModal.addEventListener('shown.bs.modal', event => {
        let button = event.relatedTarget
        let id = button.getAttribute('data-bs-id')
        eliminaModal.querySelector('.modal-footer #id_periodo_lactancia').value = id
    })


</script>     


    <script src="assets/js/bootstrap.bundle.min.js"></script>

    <script>
            $(document).ready(function () {
         $('#pl_termino_lact').change(function (e) {
            
            if ($(this).val() === '2' ) {
                $('#pl_otro').prop("disabled", false);              
                $('#pl_otro').focus();                          
            } 
            else{ 
                $('#pl_otro').prop("disabled", true);  
            }
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