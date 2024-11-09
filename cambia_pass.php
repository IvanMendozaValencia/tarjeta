<?php
	
	require 'funcs/conexion.php';
	require 'funcs/funcs.php';
	
	if(empty($_GET['user_id']))
	{
		header('Location: index.php');
	}
	
	if(empty($_GET['token']))
	{
		header('Location: index.php');
	}
	
	$user_id = $mysqli->real_escape_string($_GET['user_id']);
	$token = $mysqli->real_escape_string($_GET['token']);
	
	if(!verificaTokenPass($user_id, $token))
	{
		echo 'No se pudo verificar los datos';
		exit;
	}

	
?>

<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cambiar Password</title>

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
                    <div class="panel-title">Cambiar Password</div>
                    <div style="float:right; font-size: 80%; position: relative; top:-10px"><a href="index.php">Iniciar
                            Sesi&oacute;n</a></div>
                </div>

                <div style="padding-top:30px" class="panel-body">

                    <form id="loginform" class="form-horizontal" role="form" action="guarda_pass.php" method="POST"
                        autocomplete="off">

                        <input type="hidden" id="user_id" name="user_id" value="<?php echo $user_id; ?>" />

                        <input type="hidden" id="token" name="token" value="<?php echo $token; ?>" />

                        <div class="form-group">
                            <label for="password" class="col-md-3 control-label">Nuevo Password</label>
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

                        <div style="margin-top:10px" class="form-group">
                            <div class="col-sm-12 controls">
                                <button id="btn-login" type="submit" class="btn btn-success">Modificar</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
    function inhabilitar() {
        alert("Esta función está inhabilitada.\n\n SIE-SEMUJER.")
        return false
    }
    document.oncontextmenu = inhabilitar
    </script>
</body>

</html>