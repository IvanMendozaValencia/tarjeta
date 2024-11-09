<?php

session_start();

require 'funcs/conexion.php';
require 'funcs/funcs.php';


if(!isset($_SESSION["id_usuario"]))
{
    header("Location: index.php");
}


$idUsusario = $_SESSION['id_usuario'];
$sql = "SELECT usuarios.id, usuario, nombre, correo, clues_id, last_session, id_tipo, a_usuarios,  a_tarjeta, a_configuracion, a_consultas, a_estadisticas, a_agregar, a_modificar, a_eliminar, CLUES,  NOMBRE_DE_LA_INSTITUCION, ENTIDAD,  MUNICIPIO,  LOCALIDAD, CLAVE_DE_LA_JURISDICCION, JURISDICCION, NOMBRE_DE_TIPOLOGIA FROM usuarios inner join clues on clues.clues = usuarios.clues_id WHERE id  ='$idUsusario'";
$result = $mysqli->query($sql);
$row_usuario = $result->fetch_assoc();
$id_tipo_usuario=$row_usuario['id_tipo'];

$id_tarjeta = $_GET['id_tarjeta'];


$sqlVisitas = "SELECT id_visita, fecha_visita, result_visita, personal_visita, fk_visita_tarjeta, rv_concepto, pv_concepto FROM  visitas INNER JOIN datos_identificacion on fk_visita_tarjeta = id_tarjeta INNER JOIN resultado ON resultado.id_resultado = visitas.result_visita INNER JOIN personalvisita ON personalvisita.id_personalvisita = visitas.personal_visita WHERE fk_visita_tarjeta = $id_tarjeta ORDER BY fecha_visita DESC";
$listaVisitas = $mysqli->query($sqlVisitas);


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


    <title>Visitas Prenatales</title>
    
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
    <div class="container py-2">
        <h3 class="text-center">Lista-Visitas Prenatales</h3>   
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
                    <form action="editaTarjeta.php" method="GET"> 

                       <?php if($row_usuario['a_agregar']==1):  ?>
                       <a href="#" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#nuevoModalVisita" data-bs-id="<?= $rowpaciente['id_tarjeta']; ?>"><i class="fa-solid fa-house-medical-flag"></i> Nueva visita</a> </td>
                       <?php endif;?>
           
                       <?php if($row_usuario['a_agregar']==0):  ?>
                       <a href="#" class="btn btn-outline-success disabled" data-bs-toggle="modal" data-bs-target="#nuevoModalVisita" data-bs-id="<?= $rowpaciente['id_tarjeta']; ?>" ><i class="fa-solid fa-house-medical-flag"></i> Nueva visita</a> </td>
                       <?php endif;?>
                    
                    <input type="hidden" name='id' value=<?=$id_tarjeta  ?>> 
                    <Button type ="submit" class="btn btn-dark">Regresar</Button>
                    </form>

                </div>
            </div>      
            <br>
            <table id="tabla" class="display" style="width:100%">
                <thead>
                    <tr>
                    <th>Fecha</th>
                    <th>Resultado visita</th> 
                    <th>Personal visita</th>
                    <th style="width:15px">Editar</th>
                    <th style="width:15px">Eliminar</th>
                    </tr>
                 </thead>

                <tbody>
                    <?php while($row = $listaVisitas->fetch_assoc()) { ?>
                    <tr>
                    <?php $row['id_visita']; ?>

                    <td><?php echo utf8_encode(utf8_decode($row['fecha_visita'])); ?></td>
                    <td><?php echo utf8_encode(utf8_decode($row['rv_concepto'])); ?></td>
                    <td><?php echo utf8_encode(utf8_decode($row['pv_concepto'])); ?></td>


                    <?php if($row_usuario['a_modificar']==1):  ?>
                    <td align="center"><a href="#" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editaModalVisita" data-bs-id="<?= $row['id_visita']; ?>"><i class="fa-solid fa-star-of-life"></i> </a></td>
                    <?php endif;?>
                    <?php if($row_usuario['a_modificar']==0):  ?>
                    <td align="center"><a href="#" class="btn btn-sm btn-warning disabled" data-bs-toggle="modal" data-bs-target="#editaModalVisita" data-bs-id="<?= $row['id_visita']; ?>"><i class="fa-solid fa-star-of-life"></i></a></td>
                    <?php endif;?>
                    
                    <?php if($row_usuario['a_eliminar']==1):  ?>
                    <td align="center"><a href="#" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#eliminaModalVisita" data-bs-id="<?= $row['id_visita']; ?>"><i class="fa-solid fa-trash"></i> </a></td>
                    <?php endif;?>
                    <?php if($row_usuario['a_eliminar']==0):  ?>
                    <td align="center"><a href="#" class="btn btn-sm btn-danger disabled" data-bs-toggle="modal" data-bs-target="#eliminaModalVisita" data-bs-id="<?= $row['id_visita']; ?>"><i class="fa-solid fa-trash"></i></a></td>
                    <?php endif;?>
                    </tr>
                     <?php } ?>

                </tbody>
        </table>

<?php
$queryR= "SELECT id_personalvisita, pv_concepto FROM personalvisita ORDER BY id_personalvisita";
$listapersonalvisita = $mysqli->query($queryR); 
?>

<?php
$queryPV= "SELECT id_resultado, rv_concepto FROM resultado ORDER BY id_resultado";
$listaresultado = $mysqli->query($queryPV); 
?>


<?php include 'nuevoModalVisita.php'; ?>
<?php $listaresultado->data_seek(0); ?>
<?php $listapersonalvisita->data_seek(0); ?>

<?php include 'editaModalVisita.php'; ?>
<?php include 'eliminaModalVisita.php'; ?>

<script>
    let nuevoModal = document.getElementById('nuevoModalVisita')
    let editaModal = document.getElementById('editaModalVisita')
    let eliminaModal = document.getElementById('eliminaModalVisita')

    nuevoModal.addEventListener('shown.bs.modal', event => {
        nuevoModal.querySelector('.modal-body #fecha_visita').focus()
    })
    nuevoModal.addEventListener('hide.bs.modal', event => {
        nuevoModal.querySelector('.modal-body #fecha_visita').value = ""
        nuevoModal.querySelector('.modal-body #result_visita').value = ""
        nuevoModal.querySelector('.modal-body #personal_visita').value = ""
       
    })

    nuevoModal.addEventListener('shown.bs.modal', event => {
        let button = event.relatedTarget
            let id = button.getAttribute('data-bs-id')
            let inputId = nuevoModal.querySelector('.modal-body #id_tarjeta')            
            let url = "getIdtarjetas.php"
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
        editaModal.querySelector('.modal-body #fecha_visita').value = ""
        editaModal.querySelector('.modal-body #result_visita').value = ""
        editaModal.querySelector('.modal-body #personal_visita').value = ""                         

    })

    editaModal.addEventListener('shown.bs.modal', event => {
        let button = event.relatedTarget
        let id = button.getAttribute('data-bs-id')

        let inputId = editaModal.querySelector('.modal-body #id_visita')
        let inputFecha_visita= editaModal.querySelector('.modal-body #fecha_visita')
        let inputResult_visita = editaModal.querySelector('.modal-body #result_visita') 
        let inputPersonal_visita = editaModal.querySelector('.modal-body #personal_visita') 
        let inputfk_visita_tarjeta = editaModal.querySelector('.modal-body #fk_visita_tarjeta')
        let url = "getVisitas.php"
        let formData = new FormData()
        formData.append('id_visita', id)

        fetch(url, {
                method: "POST",
                body: formData
            }).then(response => response.json())
            .then(data => {

                inputId.value = data.id_visita                    
                inputFecha_visita.value = data.fecha_visita
                inputResult_visita.value = data.result_visita
                inputPersonal_visita.value = data.personal_visita
                inputfk_visita_tarjeta.value = data.fk_visita_tarjeta
                
            }).catch(err => console.log(err))

    })

    eliminaModal.addEventListener('shown.bs.modal', event => {
        let button = event.relatedTarget
        let id = button.getAttribute('data-bs-id')
        eliminaModal.querySelector('.modal-footer #id_visita').value = id
    })
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

