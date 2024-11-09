<?php
	
	require 'funcs/conexion.php';
	require 'funcs/funcs.php';
	
	$errors = array();
	if(!empty($_POST))
	{
		$nombre = $mysqli->real_escape_string($_POST['nombre']);
		$usuario = $mysqli->real_escape_string($_POST['usuario']);
		$password = $mysqli->real_escape_string($_POST['password']);
		$con_password = $mysqli->real_escape_string($_POST['con_password']);
		$email = $mysqli->real_escape_string($_POST['email']);
		$clues = $mysqli->real_escape_string($_POST['clues']);
		$captcha = $mysqli->real_escape_string($_POST['g-recaptcha-response']);
		
		$activo =0;
		$tipo_usuario=3;
		$secret ='6Ld5naspAAAAAKJynLHFQ9aA9feGSnvf3gnj684k';//<!-- SHHH --< -->- 
		//$secret ='6LeRbagoAAAAAJiWLPjA6ePV75XE_OaZbqQvGzEU';//<!-- SHHH --< -->- esta en 000web host 6LesXC8oAAAAAGH7tIAforzgSe22vBBBVsuAqlOU  /SER 6LdWmKQpAAAAAERNsDv2_lPx7KtHQ7AXEvjOmcfx
		//<!--sitio clave >- esta en 000web host-- 6LesXC8oAAAAAKYAu7XhvmrpC-8QqVmMlyb5GQwF -->
		if(!$captcha)
		{
			$errors[]="Por favor verifica el captcha";
		}
		
		if(isNull($nombre, $usuario, $password, $con_password, $email))
		{
			$errors[]="Debe llenar todos los campos";
		}
		
		
		if(!isEmail($email))
		{
			$errors[]="Dirección de correo no es valida";
		}
		
		if(!validaPassword($password, $con_password))
		{
			$errors[]="Las contraseñas no coinciden";
		}
		
		if(usuarioExiste($usuario))
		{
			$errors[]="El usuario $usuario ya existe";
		}
		
		if(emailExiste($email))
		{
			$errors[]="El email $email ya existe";
		}
		
		if(count($errors)==0)
		{
			
			$arr = 1;
			
			if ($arr==1 )
			{
				$pass_hash = hashPassword($password);
				$token = generateToken();
				$registro = registraUsuario($usuario, $pass_hash, $nombre, $email, $clues, $activo, $token, $tipo_usuario);
				
				if($registro >0)
				{
					
					$url = 'http://'.$_SERVER["SERVER_NAME"].'/salud/activar.php?id='.$registro.'&val='.$token;				
					$asunto = 'Activar cuenta';
					$cuerpo = "Estimado(a) $nombre: <br /><br />Recibimos su solicitud para hacer uso del Sistema de la Tarjeta de Atención Integral del Embarazo, Puerperio y Periodo de Lactancia SNSP-Guerrero, una vez activada su cuenta deberá ponerse en contacto con el administrador para que le otorgue los accesos y privilegios que tendrá dentro del sistema, para continuar con el proceso de registro, es indispensable de Click en la siguiente liga para <a href='$url'>Activar tu cuenta</a>";
					if(enviarEmail($email, $nombre, $asunto, $cuerpo))
					{
						echo "Para terminar con el proceso de registro y verificar su autenticidad de favor siga las instrucciones que le hemos enviado a la dirección de correo electrónico:$email";
						echo"<br><a href='index.php' >Iniciar Sesión</a>";
						exit;
					}
					else
					{
						$errors[]='Error al enviar el Correo electronico';
					}

				}
				else
				{
					$errors[]='Error al resgistrar';
				}
			}
			/**else
			{
				$errors[]='Error al comprobar Captcha';
			}**/
		}
		
	}
	

	
?>

<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="icono/ico.ico">
    <title>Registro</title>

    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script src="https://www.google.com/recaptcha/api.js"></script>

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

<body>
    <div class="container">
        <div id="signupbox" style="margin-top:10px" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
            <div class="panel panel-danger">
                <div class="panel-heading">
                    <div class="panel-title">Reg&iacute;strate</div>
                    <div style="float:right; font-size: 75%; position: relative; top:-10px"><a id="signinlink"
                            href="index.php">Iniciar Sesi&oacute;n</a></div>
                </div>

                <div class="panel-body">

                    <form id="signupform" class="form-horizontal" role="form" action="<?php $_SERVER['PHP_SELF'] ?>"
                        method="POST" autocomplete="off">

                        <div id="signupalert" style="display:none" class="alert alert-danger">
                            <p>Error:</p>
                            <span></span>
                        </div>

                        <div class="form-group">
                            <label for="nombre" class="col-md-3 control-label">Nombre:</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" name="nombre" placeholder="Nombre"
                                    value="<?php if(isset($nombre)) echo $nombre; ?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="usuario" class="col-md-3 control-label">Usuario</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" name="usuario" placeholder="Usuario"
                                    value="<?php if(isset($usuario)) echo $usuario; ?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password" class="col-md-3 control-label">Password</label>
                            <div class="col-md-9">
                                <input type="password" class="form-control" name="password" placeholder="Password"
                                    required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="con_password" class="col-md-3 control-label">Confirmar Password</label>
                            <div class="col-md-9">
                                <input type="password" class="form-control" name="con_password"
                                    placeholder="Confirmar Password" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email" class="col-md-3 control-label">Email</label>
                            <div class="col-md-9">
                                <input type="email" class="form-control" name="email" placeholder="Email"
                                    value="<?php if(isset($email)) echo $email; ?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="clues" class="col-md-3 control-label">CLUES</label>
                            <div class="col-md-9">
                                <input type="clues" class="form-control" name="clues" placeholder="CLUES"
                                    value="<?php if(isset($clues)) echo $clues; ?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="captcha" class="col-md-3 control-label"></label>
                            <div class="g-recaptcha col-md-9" data-sitekey="6LeRbagoAAAAACfoTdn7W-CQZKTFRbzhSRv0aVeE">
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-offset-3 col-md-9">
                                <button id="btn-signup" type="submit" class="btn btn-danger"><i
                                        class="icon-hand-right"></i>Registrar</button>
                            </div>
                        </div>
                    </form>
                    <?php echo resultBlock($errors); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-14 text-white">

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