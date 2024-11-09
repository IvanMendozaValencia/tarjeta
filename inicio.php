<?php
	session_start();
	require 'funcs/conexion.php';
	require 'funcs/funcs.php';
	
	if(!isset($_SESSION["id_usuario"]))
	{
		header("Location: index.php");
	}
	
  $idUsusario = $_SESSION['id_usuario'];
	$sql = "SELECT usuarios.id, usuario, nombre, correo, clues_id, last_session, id_tipo, a_usuarios, a_tarjeta, a_configuracion, a_consultas, a_estadisticas, a_agregar, a_modificar, a_eliminar, CLUES,  NOMBRE_DE_LA_INSTITUCION, ENTIDAD,  MUNICIPIO,  LOCALIDAD, CLAVE_DE_LA_JURISDICCION, JURISDICCION, NOMBRE_DE_TIPOLOGIA, NOMBRE_DE_LA_UNIDAD FROM usuarios inner join clues on clues.clues = usuarios.clues_id WHERE id  ='$idUsusario'";
	$result = $mysqli->query($sql);
	$row = $result->fetch_assoc();
  
  $id_tipo_usuario=$row['id_tipo'];	

?>

<!doctype html>
<html lang="en">

  <head>
    <meta charset="utf-8">
	
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://getbootstrap.com/docs/5.3/assets/css/docs.css" rel="stylesheet">
        <link rel="icon" href="icono/ico.ico">
    <title>Bienvenido</title>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
   
  </head>
    <style>
		body {
        background: #870000;
        /* fallback for old browsers */
        background: -webkit-linear-gradient(to top, #190A05, #870000);
        /* Chrome 10-25, Safari 5.1-6 */
        background: linear-gradient(to top, #190A05, #870000);
        /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
		}
	</style>

    <style>
		section {
        background: #870000;
        /* fallback for old browsers */
        background: -webkit-linear-gradient(to top, #190A05, #870000);
        /* Chrome 10-25, Safari 5.1-6 */
        background: linear-gradient(to top, #190A05, #870000);
        /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
		}
	</style>

    <style>
		nav {
        background: #870000; /* fallback for old browsers */
        background: -webkit-linear-gradient(to right, #190A05, #870000); /* Chrome 10-25, Safari 5.1-6 */
        background: linear-gradient(to right, #190A05, #870000); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
		}
	</style>
    <style>
      h2 .red-text {
        color:rgb(159, 34, 65, 100%);
      }
    </style>


  <body class="p-3 m-0 border-0 bd-example m-0 border-0" >

  <nav class="navbar navbar-expand-lg ">
  <div class="container-fluid  mt-8 bg-light">
    <a class="navbar-brand" href="#">Accesos</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">

           
              <?php if(($row['a_usuarios']==1)&&($row['id_tipo']!=3)):  ?>
              <li class="nav-item dropdown">
                <a class="nav-link active dropdown-toggle"  href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Configuración </a>        
              
              <ul class="dropdown-menu">
                  <?php if(($row['a_usuarios']==1)&&($row['id_tipo']!=3)):  ?>
                  <li><a class="dropdown-item" href="usuarios.php">Usuarios</a></li>              
                  <?php endif;?>
       
                  <?php if(($row['a_configuracion']==1)&&($row['id_tipo']!=3)):  ?>
                  <li>
                  <hr class="dropdown-divider">
                  </li>
                  <li><a class="dropdown-item " href="respaldodb.php">Respaldo Base de Datos</a></li>
                  <?php endif;?>   

                </ul>
              <?php endif;?>

              <?php if(($row['a_usuarios']==1)&&($row['id_tipo']!=3)):  ?>
              <li class="nav-item dropdown">
                <a class="nav-link active dropdown-toggle"  href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Registro </a>        
              
              <ul class="dropdown-menu">
                  <?php if(($row['a_tarjeta']==1)&&($row['id_tipo']!=3)):  ?>
                  <li><a class="dropdown-item" href="registrostarjeta.php">Tarjeta de atención</a></li>              
                  <?php endif;?>
                </ul>
              <?php endif;?>
                     
              
              <?php if(($row['a_consultas']==1)&&($row['id_tipo']!=3)):  ?>
              <li class="nav-item dropdown">
              <a class="nav-link active dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Consultas
              </a>
              <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="inicio.php">Información Integral</a></li>
              <li>                <hr class="dropdown-divider">              </li>
              <li><a class="dropdown-item" href="inicio.php">Visitas</a></li>
              <li>                <hr class="dropdown-divider">              </li>
              <li><a class="dropdown-item" href="inicio.php">Nacimientos</a></li>
              </li></ul>
              <?php endif;?> 
               

              <?php if(($row['a_estadisticas']==1)&&($row['id_tipo']!=3)):  ?>
              <li class="nav-item dropdown">
              <a class="nav-link active dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Estadísticas
              </a>
              <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="inicio.php">Información Integral</a></li>
              <li>                <hr class="dropdown-divider">              </li>
              <li><a class="dropdown-item" href="inicio.php">Visitas</a></li>
              <li>                <hr class="dropdown-divider">              </li>
              <li><a class="dropdown-item" href="inicio.php">Nacimientos</a></li>
              
              </li></ul>
              <?php endif;?>
              

              <li class="nav-item">
              <a class="nav-link active" href="#"data-bs-toggle="modal" data-bs-target="#segurosalir">Cerrar Sesi&oacute;n</a>     
              </li>
 
        
      </ul>

    </div>
  </div>          
</nav>
<hr>
      <div class="col-md-14 text-white">
      <br>
            <div class="card text-center">
          <div class="card-body text-center">
		   <div class="card-header">SERVICIO NACIONAL DE SALUD PÚBLICA - COORDINACIÓN GUERRERO</div>             
                 <h2 ><center><?php echo 'Bienvenid@ '.utf8_encode(utf8_decode($row['nombre'])); ?></center>         
                <h5 class="red-text" style="color:rgb(171, 0, 51, 100%);"><?php echo 'CLUES:  '.utf8_encode(utf8_decode($row['CLUES'])); ?>
                <h5 class="red-text" style="color:rgb(171, 0, 51, 100%);"><?php echo utf8_encode(utf8_decode($row['NOMBRE_DE_LA_INSTITUCION'])); ?>
                <h5 class="red-text" style="color:rgb(171, 0, 51, 100%);"><?php echo utf8_encode(utf8_decode($row['ENTIDAD'])); ?>-<?php echo utf8_encode(utf8_decode($row['MUNICIPIO'])); ?>
                <h5 class="red-text" style="color:rgb(171, 0, 51, 100%);"><?php echo utf8_encode(utf8_decode($row['LOCALIDAD'])); ?>
                <h5 class="red-text" style="color:rgb(171, 0, 51, 100%);"><?php echo utf8_encode(utf8_decode($row['CLAVE_DE_LA_JURISDICCION'])); ?>-<?php echo utf8_encode(utf8_decode($row['JURISDICCION'])); ?>
                <h5 class="red-text" style="color:rgb(171, 0, 51, 100%);"><?php echo utf8_encode(utf8_decode($row['NOMBRE_DE_LA_UNIDAD'])); ?>
                <div class="card-footer text-muted">
                <img  src="imagenes/logo.png" alt="" width="260" height="50px"></h1> 
                </div>
            </div>
          </div>
      </div> 
  
    <section >
        <p class="text-center text-black fw-light fs-4"> 
            <a class="text-black text-decoration-none text-white">TARJETA DE ATENCIÓN INTEGRAL DEL EMBARAZO, PUERPERIO Y PERIODO DE LACTANCIA</a> 
        </p>
    </section>

    <?php include 'segurosalir.php'; ?>
<script>
       let segurosalir = document.getElementById('segurosalir')

       segurosalir.addEventListener('shown.bs.modal', event => {
        let button = event.relatedTarget
      //  let id = button.getAttribute('data-bs-id')
       // segurosalir.querySelector('.modal-footer #id_tarjeta').value = id
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