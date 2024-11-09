<?php

session_start();

require 'funcs/conexion.php';
require 'funcs/funcs.php';


if(!isset($_SESSION["id_usuario"]))
{
    header("Location: index.php");
}


$idUsusario = $_SESSION['id_usuario'];
$sql = "SELECT usuarios.id, usuario, nombre, correo, clues_id, last_session, id_tipo, a_usuarios, a_tarjeta, a_configuracion, a_consultas, a_estadisticas, a_agregar, a_modificar, a_eliminar, CLUES,  NOMBRE_DE_LA_INSTITUCION, ENTIDAD,  MUNICIPIO,  LOCALIDAD, CLAVE_DE_LA_JURISDICCION, JURISDICCION, NOMBRE_DE_TIPOLOGIA FROM usuarios inner join clues on clues.clues = usuarios.clues_id WHERE id  ='$idUsusario'";
$result = $mysqli->query($sql);
$row_usuario = $result->fetch_assoc();
$id_tipo_usuario=$row_usuario['id_tipo'];

$sqlTarjeta = "SELECT id_tarjeta, curp, nombre, primer_apellido, segundo_apellido, edad, imc, ag_gestas FROM datos_identificacion order by curp ASC";
$listaTarjeta = $mysqli->query($sqlTarjeta);


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


    <title>Registros</title>
    
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
        <h3 class="text-center">Lista-Tarjeta de Atención Integral del Embarazo, Puerperio y Período de Lactancia</h3>   
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

                    <?php if($row_usuario['a_agregar']==1):  ?>
                    <td><a href="nuevatarjeta.php" class="btn btn-outline-primary"><i class="fa-solid fa-hospital-user"></i> Nueva tarjeta</a> </td>
                    <?php endif;?>
                    <?php if($row_usuario['a_agregar']==0):  ?>
                        <td><a href="nuevatarjeta.php" class="btn btn-outline-primary disabled"><i class="fa-solid fa-hospital-user"></i> Nueva tarjeta</a> </td>
                    <?php endif;?>
               
                    <a href="../Salud/inicio.php" class="btn btn-dark"> Menú principal</a>    
                </div>
            </div>      
            <br>
            <table id="tabla" class="display"  style="width:100%">
                <thead>
                    <tr>
                    <th>CURP</th>
                    <th>Nombre</th> 
                    <th>Primer apellido</th>
                    <th>Segundo apellido</th>
                    <th>Edad</th>
                    <th>I.M.C.</th> 
                    <th>Gestas</th>            
                    <th>Información</th>
                    <th>Eliminar</th>
                    </tr>
                 </thead>

                <tbody>
                    <?php while($row = $listaTarjeta->fetch_assoc()) { ?>
                    <tr>
                    <?php $row['id_tarjeta']; ?>
                    
                    <td><?php echo utf8_encode(utf8_decode($row['curp'])); ?></td>
                    <td><?php echo (($row['nombre'])); ?></td>
                    <td><?php echo utf8_encode(utf8_decode($row['primer_apellido'])); ?></td>
                    <td><?php echo utf8_encode(utf8_decode($row['segundo_apellido'])); ?></td>
                    <td align="center"><?php echo utf8_encode(utf8_decode($row['edad'])); ?></td>
                    <!-- IMC -->
                    <?php if(($row['imc']>=18.5)&&($row['imc']<=29.9)):?>
                        <td align="center"style="background-color: #9ACD32";><?php echo utf8_encode(utf8_decode($row['imc'])); ?></td>            
                    <?php else:?>
                        <td align="center"style="background-color: #FFFF00";><?php echo utf8_encode(utf8_decode($row['imc'])); ?></td>            
                    <?php endif;?>
                    <!-- GESTAS -->
                    <?php if(($row['ag_gestas']>=0)&&($row['ag_gestas']<=3)):?>
                        <td align="center"style="background-color: #9ACD32";><?php echo utf8_encode(utf8_decode($row['ag_gestas'])); ?></td>            
                    <?php else:?>
                        <td align="center"style="background-color: #FFFF00";><?php echo utf8_encode(utf8_decode($row['ag_gestas'])); ?></td>            
                    <?php endif;?>                    
                  
                    <?php if($row_usuario['a_modificar']==1):  ?>
                    <td align="center"><a href="editaTarjeta.php?id=<?php echo $row['id_tarjeta']; ?>" class="btn btn-sm btn-info"><i class="fa-solid fa-star-of-life"></i></a> </td>
                    <?php endif;?>

                    <?php if($row_usuario['a_modificar']==0):  ?>
                    <td align="center"><a href="editaTarjeta.php?id=<?php echo $row['id_tarjeta']; ?>" class="btn btn-sm btn-info disabled"><i class="fa-solid fa-star-of-life"></i></a> </td>
                    <?php endif;?>
                    
                    <?php if($row_usuario['a_eliminar']==1):  ?>
                        <td align="center"><a href="#" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#eliminaModalRegistroTar" data-bs-id="<?= $row['id_tarjeta']; ?>"><i class="fa-solid fa-trash"></i></i> </a></td>
                    <?php endif;?>
                    <?php if($row_usuario['a_eliminar']==0):  ?>
                    <td align="center"><a href="#" class="btn btn-sm btn-danger disabled" data-bs-toggle="modal" data-bs-target="#eliminaModalRegistroTar" data-bs-id="<?= $row['id_tarjeta']; ?>"><i class="fa-solid fa-trash"></i></i> </a></td>
                    <?php endif;?>
                    </tr>
                     <?php } ?>

                </tbody>
        </table>

          

<script src="assets/js/bootstrap.bundle.min.js"></script>
<?php include 'eliminaModalRegistroTar.php'; ?>
<script>
       let eliminaModal = document.getElementById('eliminaModalRegistroTar')

        eliminaModal.addEventListener('shown.bs.modal', event => {
        let button = event.relatedTarget
        let id = button.getAttribute('data-bs-id')
        eliminaModal.querySelector('.modal-footer #id_tarjeta').value = id
    })
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

