<?php
	
	require 'funcs/conexion.php';
	require 'funcs/funcs.php';
	
	$mensaje = null;
	
	if(isset($_GET["id"])AND isset($_GET['val']))
	{
		$idUsuario = $_GET['id'];
		$token = $_GET['val'];
		$mensaje = validaIdToken($idUsuario, $token);
	}

	
?>

<!doctype html>
<html lang="es">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Registro</title>
		<link rel="stylesheet" href="css/bootstrap.min.css" >
		<link rel="stylesheet" href="css/bootstrap-theme.min.css" >
		<script src="js/bootstrap.min.js" ></script>
		
	</head>
	
	<body>
		<div class="container">
			<div class="jumbotron">
				
				<h1><?php echo $mensaje; ?></h1>
				
				<br />
				<p><a class="btn btn-primary btn-lg" href="index.php" role="button">Iniciar Sesi&oacute;n</a></p>
			</div>
		</div>
	    <script>
      function inhabilitar(){
          alert ("Esta función está inhabilitada.\n\n SNSP-GUERRERO.")
          return false
      }
      document.oncontextmenu = inhabilitar
    </script>
  </body>
</html>														