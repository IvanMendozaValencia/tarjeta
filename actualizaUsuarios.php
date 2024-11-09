<?php



session_start();

require 'funcs/conexion.php';


$id = $mysqli->real_escape_string($_POST['id']);
$usuario = $mysqli->real_escape_string($_POST['nombre']); 
$tipo_usuario = $mysqli->real_escape_string($_POST['tipo_usuario']); 
$clues_id = $mysqli->real_escape_string($_POST['clues_id']);


if (isset($_POST['a_usuarios'])){

  $a_usuarios = true;
}else{
  $a_usuarios = 0;
}

if(isset($_POST['a_configuracion'])){
  $a_configuracion = true;
}else{
  $a_configuracion = 0;
}

if(isset($_POST['a_tarjeta'])){
  $a_tarjeta = true;
}else{
  $a_tarjeta = 0;
}

if(isset($_POST['a_consultas'])){
  $a_consultas = true;
}else{
  $a_consultas = 0;
}

if (isset($_POST['a_estadisticas'])){
  $a_estadisticas = true;
}else{
  $a_estadisticas = 0;
}

if (isset($_POST['a_agregar'])){
  $a_agregar = true;
}else{
  $a_agregar = 0;
}

if(isset($_POST['a_modificar'])){
  $a_modificar = true;
}else{
  $a_modificar = 0;
}

if (isset($_POST['a_eliminar'])){
  $a_eliminar = true;
}else{
  $a_eliminar = 0;
}


$sql = "UPDATE usuarios SET nombre ='$usuario', id_tipo ='$tipo_usuario', a_usuarios ='$a_usuarios', a_configuracion ='$a_configuracion', a_tarjeta ='$a_tarjeta', a_consultas ='$a_consultas', a_estadisticas ='$a_estadisticas', a_agregar='$a_agregar', a_modificar='$a_modificar', a_eliminar='$a_eliminar', clues_id ='$clues_id' WHERE id='$id'";

  if ($mysqli->query($sql)){

    $_SESSION['color'] = "success";
    $_SESSION['msg'] = "Registro actualizado";

  }


header('Location: usuarios.php');
