<?php
	require 'funcs/conexion.php';
	require 'funcs/funcs.php';
	
	$errors = array();
	
	if(!empty($_POST))
	{
		$email = $mysqli->real_escape_string($_POST['email']);
		if(!isEmail($email))
		{
			$errors[]="Debe ingresar un correo electrónico valido";	
		}
		    if(emailExiste($email))
			{
				$user_id = getValor('id', 'correo', $email);
				$nombre = getValor('nombre', 'correo', $email);				
				$token = generaTokenPass($user_id);
				
					$url = 'http://'.$_SERVER["SERVER_NAME"].'/salud/cambia_pass.php?user_id='.$user_id.'&token='.$token;						
					$asunto = 'Recuperar Password - Sistema de la Tarjeta de Atención Integral del Embarazo, Puerperio y Periodo de Lactancia SNSP-Guerrero';
					$cuerpo = "Hola $nombre: <br /><br />Se ha solicitado un reinicio de contrase&ntilde;a, visita la siguinete direcci&oacute;n: <a href='$url'>$url</a>";
					if(enviarEmail($email, $nombre, $asunto, $cuerpo))
					{
						echo "Hemos enviado un correo electrónico:$email para restablecer tu password.<br />";
						echo"<br><a href='index.php' >Iniciar Sesión</a>";
						exit;
					}
					else
					{
						$errors[]='Error al enviar el Correo electrónico';
					}
								
			}
			else
			{
				$errors[]='No existe el coerrero electrónico';
			}			
	}
	
	
?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" href="icono/ico.ico">
    <title>Recuperar Password</title>

    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>

</head>
<style>
body {
    //background-color:rgb(159, 34, 65, 100%);
    background-color: #870000;
    /* fallback for old browsers */
    background-color: -webkit-linear-gradient(to top, #190A05, #870000);
    /* Chrome 10-25, Safari 5.1-6 */
    background-color: linear-gradient(to top, #190A05, #870000);
    /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
}
</style>

<style>
section {
    background-color: #bc955c;
}
</style>

<body>

    <div class="container">
        <div id="loginbox" style="margin-top:50px;" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
            <div class="panel panel-danger">
                <div class="panel-heading">
                    <div class="panel-title">Recuperar Password</div>
                    <div style="float:right; font-size: 80%; position: relative; top:-10px"><a href="index.php">Iniciar
                            Sesi&oacute;n</a></div>
                </div>

                <div style="padding-top:30px" class="panel-body">

                    <div style="display:none" id="login-alert" class="alert alert-danger col-sm-12"></div>

                    <form id="loginform" class="form-horizontal" role="form" action="<?php $_SERVER['PHP_SELF'] ?>"
                        method="POST" autocomplete="off">

                        <div style="margin-bottom: 25px" class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            <input id="email" type="email" class="form-control" name="email" placeholder="email"
                                required>
                        </div>

                        <div style="margin-top:10px" class="form-group">
                            <div class="col-sm-12 controls">
                                <button id="btn-login" type="submit" class="btn btn-danger">Enviar</a>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-12 control">
                                <div style="border-top: 1px solid#888; padding-top:15px; font-size:85%">
                                    No tiene una cuenta! <a href="registro.php">Registrate aquí</a>
                                </div>
                            </div>
                        </div>
                    </form>
                    <?php echo resultBlock($errors); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-14 text-white">
        <br>
        <center><img src="imagenes/logo.png" alt="" width="260" height="50px"></center>
        </h1>
        <br>

    </div>

    <script>
    function inhabilitar() {
        alert("Esta función está inhabilitada.\n\n SNSP-GUERRERO.")
        return false
    }
    document.oncontextmenu = inhabilitar
    </script>
</body>

</html>