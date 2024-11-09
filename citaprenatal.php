<?php

session_start();

require 'funcs/conexion.php';
require 'funcs/funcs.php';


if(!isset($_SESSION["id_usuario"]))
{
    header("Location: index.php");
}


$idUsusario = $_SESSION['id_usuario'];
$sql = "SELECT usuarios.id, usuario, nombre, correo, clues_id, last_session, id_tipo, a_usuarios,  a_tarjeta, a_configuracion, a_consultas, a_estadisticas, a_agregar, a_modificar, a_eliminar, CLUES,  NOMBRE_DE_LA_INSTITUCION, ENTIDAD,  MUNICIPIO,  LOCALIDAD, CLAVE_DE_LA_JURISDICCION, JURISDICCION, NOMBRE_DE_TIPOLOGIA FROM usuarios INNER JOIN clues ON clues.clues = usuarios.clues_id WHERE id  ='$idUsusario'";
$result = $mysqli->query($sql);
$row_usuario = $result->fetch_assoc();
$id_tipo_usuario=$row_usuario['id_tipo'];

$id_tarjeta = $_GET['id_tarjeta'];


$sqlCitas = "SELECT id_cita_prenatal, cp_fecha_consulta, cp_acompanante, cp_semana_gestacion, cp_peso, cp_pres_art, cp_fondo_uterino, a_concepto, cp_imc, cp_frec_cardiaca, cp_sig_sin_alarma, ssa_concepto, cp_qs_glucemia FROM  citas_prenatales INNER JOIN datos_identificacion ON fk_id_tarjeta = id_tarjeta INNER JOIN acompanante ON acompanante.id_acompanante = citas_prenatales.cp_acompanante INNER JOIN sig_sin_alarma ON citas_prenatales.cp_sig_sin_alarma = sig_sin_alarma.id_sig_sin_alarma  WHERE fk_id_tarjeta = $id_tarjeta ORDER BY cp_fecha_consulta DESC";
$listaCitas = $mysqli->query($sqlCitas);

$sqlpaciente = "SELECT CONCAT_WS(' ', nombre, primer_apellido, segundo_apellido) AS nombre_paciente FROM datos_identificacion
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
    <div class="container py-2">
        <h3 class="text-center">Lista-Citas Prenatales</h3>   
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

                    <?php if($row_usuario['a_agregar']==1): ?>
                        <td><a href="nuevaCitaPreNant.php?id=<?= $id_tarjeta ?>" class="btn btn-outline-warning"><i class="fa-solid fa-stethoscope"></i> Nueva cita</a> </td>
                    <?php endif;?>
                    <?php if($row_usuario['a_agregar']==0): ?>
                        <td><a href="nuevaCitaPreNant.php?id=<?= $id_tarjeta ?>" class="btn btn-outline-warning disabled"><i class="fa-solid fa-stethoscope"></i> Nueva cita</a> </td>
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
                    <th>Acompañante</th> 
                    <th>I.M.C.</th>
                    <th>Glucosa en ayuno</th>
                    <th>Presión Arterial</th>
                    <th>Frec. Car. Fetal</th> 
                    <th>Sig. Sin. Alarma</th>            
                    <th>Información</th>
                    <th>Eliminar</th>
                    </tr>
                 </thead>

                <tbody>
                    <?php while($row = $listaCitas->fetch_assoc()) { ?>
                    <tr>
                    <?php $row['id_cita_prenatal']; ?>

                    <td align="center"><?php echo utf8_encode(utf8_decode($row['cp_fecha_consulta'])); ?></td>
                    <td align="center"><?php echo utf8_encode(utf8_decode($row['a_concepto'])); ?></td>
                    <!-- IMC -->
                    <?php if(($row['cp_imc']>=18.5)&&($row['cp_imc']<=25)):?>
                        <td align="center"style="background-color: #9ACD32";><?php echo utf8_encode(utf8_decode($row['cp_imc'])); ?></td>            
                    <?php else:?>
                        <td align="center"style="background-color: #FFFF00";><?php echo utf8_encode(utf8_decode($row['cp_imc'])); ?></td>            
                    <?php endif;?>
                    <!-- GLUCOSA EN AYUNO -->
                    <?php if($row['cp_qs_glucemia']>= '200'):?>
                        <td align="center"style="background-color: #FF333F";><?php echo utf8_encode(utf8_decode($row['cp_qs_glucemia'])); ?></td>  
                      <?php elseif ($row['cp_qs_glucemia'] >= '126' && $row['cp_qs_glucemia']  <= '199'):?>
                        <td align="center"style="background-color: #FFFF00";><?php echo utf8_encode(utf8_decode($row['cp_qs_glucemia'])); ?></td>
                    <?php else: ?> 
                        <td align="center"style="background-color: #9ACD32";><?php echo utf8_encode(utf8_decode($row['cp_qs_glucemia'])); ?></td>            
                    <?php endif;?>
                    <!-- PRESION ARTERIAL -->            
                    <?php if($row['cp_pres_art']<= '130/89'):?>
                        <td align="center"style="background-color: #9ACD32";><?php echo utf8_encode(utf8_decode($row['cp_pres_art'])); ?></td>  
                      <?php else:?>
                        <td align="center"style="background-color: #FF333F";><?php echo utf8_encode(utf8_decode($row['cp_pres_art'])); ?></td>            
                    <?php endif;?>
                   
                    <!-- FRECUENCIA CARDIACA -->            
                    <?php if($row['cp_frec_cardiaca']>= '120' && $row['cp_frec_cardiaca'] <= '160'):?>
                        <td align="center"style="background-color: #9ACD32";><?php echo utf8_encode(utf8_decode($row['cp_frec_cardiaca'])); ?></td>  
                      <?php else:?>
                        <td align="center"style="background-color: #FF333F";><?php echo utf8_encode(utf8_decode($row['cp_frec_cardiaca'])); ?></td>            
                    <?php endif;?>

                    <!-- SIGNOS Y SINTOMAS DE ALARMA -->         
                    <?php if($row['cp_sig_sin_alarma']== 0):?>
                        <td align="center"style="background-color: #9ACD32";><?php echo utf8_encode(utf8_decode($row['ssa_concepto'])); ?></td>  
                      <?php elseif ($row['cp_sig_sin_alarma'] == 6 || ($row['cp_sig_sin_alarma']  >=10 && $row['cp_sig_sin_alarma']  <=12)):?>
                        <td align="center"style="background-color: #FFFF00";><?php echo utf8_encode(utf8_decode($row['ssa_concepto'])); ?></td>
                    <?php else: ?> 
                        <td align="center"style="background-color: #FF333F";><?php echo utf8_encode(utf8_decode($row['ssa_concepto'])); ?></td>            
                    <?php endif;?>

                 
                    <?php if($row_usuario['a_modificar']==1):  ?>
                    <td align="center"><a href="editaCitaPreNant.php?id=<?php echo $row['id_cita_prenatal']; ?>" class="btn btn-sm btn-info"><i class="fa-solid fa-star-of-life"></i></a> </td>
                    <?php endif;?>

                    <?php if($row_usuario['a_modificar']==0):  ?>
                    <td align="center"><a href="editaCitaPreNant.php?id=<?php echo $row['id_cita_prenatal']; ?>" class="btn btn-sm btn-info disabled"><i class="fa-solid fa-star-of-life"></i></a> </td>
                    <?php endif;?>
                    
                    <?php if($row_usuario['a_eliminar']==1):  ?>
                        <td align="center"><a href="#" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#eliminaModalCitaPre" data-bs-id="<?= $row['id_cita_prenatal']; ?>"><i class="fa-solid fa-trash"></i></i> </a></td>
                    <?php endif;?>
                    <?php if($row_usuario['a_eliminar']==0):  ?>
                    <td align="center"><a href="#" class="btn btn-sm btn-danger disabled" data-bs-toggle="modal" data-bs-target="#eliminaModalCitaPre" data-bs-id="<?= $row['id_cita_prenatal']; ?>"><i class="fa-solid fa-trash"></i></i> </a></td>
                    <?php endif;?>
                    </tr>
                     <?php } ?>

                </tbody>
        </table>

<?php include 'eliminaModalCitaPre.php'; ?>

<script>

    let eliminaModal = document.getElementById('eliminaModalCitaPre')


    eliminaModal.addEventListener('shown.bs.modal', event => {
        let button = event.relatedTarget
        let id = button.getAttribute('data-bs-id')
        eliminaModal.querySelector('.modal-footer #id_cita_prenatal').value = id
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

