<?php


session_start();


require 'funcs/conexion.php';
require 'funcs/funcs.php';

if(!isset($_SESSION["id_usuario"]))
{
    header("Location: index.php");
}

$idUsusario = $_SESSION['id_usuario'];
$sql = "SELECT usuarios.id, usuario, nombre, correo, last_session, id_tipo, a_usuarios, a_tarjeta, a_configuracion, a_consultas, a_estadisticas, a_agregar, a_modificar, a_eliminar, id_tipo, clues_id FROM usuarios WHERE usuarios.id ='$idUsusario'";
$result = $mysqli->query($sql);
$row_usuario  = $result->fetch_assoc();



$sqlUsuarios = "SELECT usuarios.id, usuario, nombre, correo, last_session, id_tipo, a_usuarios, a_tarjeta, a_configuracion, a_consultas, a_estadisticas, a_agregar, a_modificar, a_eliminar, tipo, clues_id  FROM usuarios inner join  tipo_usuario on usuarios.id_tipo = tipo_usuario.id  ORDER BY usuario ASC";
$usuario = $mysqli->query($sqlUsuarios);


?>

<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="icono/ico.ico">
    <title>Usuarios</title>

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

        <h2 class="text-center">Administración de Usuarios</h2>   
        
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
                <a href="../Salud/inicio.php" class="btn btn-dark" ></i> Regresar</a>
            </div>
        </div>
       <br>
        <table id="tabla" class="display" style="width:100%">
            <thead>
                <tr>
                <th>Usuario</th>
                <th>Nombre</th>
                <th>CLUES</th>
                <th>Correo</th>
                <th>Sessión</th>
                <th>Tipo</th>	
                <th>Editar</th>
                </tr>
            </thead>
           
            <tbody>
            <?php while($row = $usuario->fetch_assoc()) { ?>
            <tr>
                <?php  $row['id']; ?>
                <td><?php echo $row['usuario']; ?></td>
                <td><?php echo utf8_encode(utf8_decode($row['nombre'])); ?></td>
                <td><?php echo utf8_encode(utf8_decode($row['clues_id'])); ?></td>
                <td><?php echo utf8_encode(utf8_decode($row['correo'])); ?></td>
                <td><?php echo utf8_encode(utf8_decode($row['last_session'])); ?></td>
                <td><?php echo utf8_encode(utf8_decode($row['tipo'])); ?></td>

                <td align="center"><a href="#" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editaModalusuario" data-bs-id="<?= $row['id']; ?>"><i class="fa-solid fa-pen-to-square"></i></a></td>
               
            </tr>
                <?php } ?>

            </tbody>
        </table>
    </div>


    <?php
    $sqltipo_usuario = "SELECT id, tipo FROM tipo_usuario order by tipo";
    $tipo_usuario = $mysqli->query($sqltipo_usuario);
    ?>

    <?php include 'editaModalusuario.php'; ?>

    <script>
        let editaModal = document.getElementById('editaModalusuario')

           editaModal.addEventListener('hide.bs.modal', event => {
                editaModal.querySelector('.modal-body #nombre').value = ""                
                editaModal.querySelector('.modal-body #tipo_usuario').value = ""
                editaModal.querySelector('.modal-body #clues_id').value = ""
                editaModal.querySelector('.modal-body #a_usuarios').value = ""
                editaModal.querySelector('.modal-body #a_configuracion').value = ""
                editaModal.querySelector('.modal-body #a_tarjeta').value = ""
                editaModal.querySelector('.modal-body #a_consultas').value = ""
                editaModal.querySelector('.modal-body #a_estadisticas').value = "" 
                editaModal.querySelector('.modal-body #a_agregar').value = ""
                editaModal.querySelector('.modal-body #a_modificar').value = ""
                editaModal.querySelector('.modal-body #a_eliminar').value = ""     
                editaModal.querySelectorAll('.modal-body input[type=checkbox]').forEach(function(checkElement) 
            {               
                checkElement.checked = false;
            });
        })

        editaModal.addEventListener('shown.bs.modal', event => {
            let button = event.relatedTarget
            let id = button.getAttribute('data-bs-id')

            let inputId = editaModal.querySelector('.modal-body #id')
            let inputNombre = editaModal.querySelector('.modal-body #nombre')
            let inputTipoUsuario = editaModal.querySelector('.modal-body #tipo_usuario')
            let inputClues_id = editaModal.querySelector('.modal-body #clues_id')
            let inputA_usuarios = editaModal.querySelector('.modal-body #a_usuarios')
            let inputA_configuracion = editaModal.querySelector('.modal-body #a_configuracion') 
            let inputA_tarjetas = editaModal.querySelector('.modal-body #a_tarjeta')   
            let inputA_consultas = editaModal.querySelector('.modal-body #a_consultas')
            let inputA_estadisticas = editaModal.querySelector('.modal-body #a_estadisticas') 
            let inputA_agregar = editaModal.querySelector('.modal-body #a_agregar')
            let inputA_modificar = editaModal.querySelector('.modal-body #a_modificar')
            let inputA_eliminar = editaModal.querySelector('.modal-body #a_eliminar')
            let url = "getUsuarios.php"
            let formData = new FormData()
            formData.append('id', id)
             
            fetch(url, {
                    method: "POST",
                    body: formData
                }).then(response => response.json())
                .then(data => {

                    inputId.value = data.id
                    inputNombre.value = data.nombre
                    inputTipoUsuario.value = data.id_tipo
                    inputClues_id.value = data.clues_id
                    inputA_usuarios.value = data.a_usuarios                   
                    inputA_configuracion.value = data.a_configuracion
                    inputA_tarjetas.value = data.a_tarjeta 
                    inputA_consultas.value = data.a_consultas
                    inputA_estadisticas.value = data.a_estadisticas   
                    inputA_agregar.value = data.a_agregar
                    inputA_modificar.value = data.a_modificar
                    inputA_eliminar .value = data.a_eliminar

                    if(a_usuarios.value == 1){
                        a_usuarios.click(); 
                    }                                       
                    if(a_configuracion.value == 1){
                        a_configuracion.click();
                    }
                    if(a_tarjeta.value == 1){
                        a_tarjeta.click();                     
                    }   
                    if(a_consultas.value == 1){
                        a_consultas.click();
                    }
                    if(a_estadisticas.value == 1){
                        a_estadisticas.click();                    
                    }
                    if(a_agregar.value == 1){
                        a_agregar.click();
                    }
                    if(a_modificar.value == 1){
                        a_modificar.click();           
                    }
                    if(a_eliminar.value == 1){
                        a_eliminar.click();                    
                    }

                }).catch(err => console.log(err))

        }) 

    </script>
  
    <script src="assets/js/bootstrap.bundle.min.js"></script>

   
    <script>

//Codigo para limitar la cantidad maxima que tendra dicho Input
$('input#clues_id').keypress(function (event) {
// if (event.which < 48 || event.which > 57 || this.value.length === 18) {
if (this.value.length === 11) {
return false;
}
});
//Validando si existe la clues_id en BD antes de enviar el Form
$("#clues_id").on("keyup", function() {
  var clues = $("#clues_id").val(); //CAPTURANDO EL VALOR DE INPUT CON ID CLUES
  var longitudclues = $("#clues_id").val().length; //CUENTO LONGITUD

//Valido la longitud 
  if(longitudclues >= 10){
    var dataString = 'clues_id=' + clues;

      $.ajax({
          url: 'getBuscaClues.php',
          type: "GET",
          data: dataString,
          dataType: "JSON",

          success: function(datos){

                if( datos.success == 1){
                    $("#respuesta").html(datos.message);                  
                    $("#enviar").attr('disabled',true); //Desabilito el Botton
                }else{

                    $("#respuesta").html(datos.message);
                    $("#enviar").attr('disabled',false); //Habilito el Botton
                    }
                  }
                });
              }
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