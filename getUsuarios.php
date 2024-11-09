<?php

require 'funcs/conexion.php';

$id = $mysqli->real_escape_string($_POST['id']);

$sql = "SELECT usuarios.id, usuario, nombre, correo, last_session, id_tipo, a_usuarios,  a_configuracion, a_tarjeta, a_consultas, a_estadisticas, a_agregar, a_modificar, a_eliminar, clues_id  FROM  usuarios WHERE id = $id LIMIT 1";

$resultado = $mysqli->query($sql);
$rows = $resultado->num_rows;

$personal = [];

if ($rows > 0) {
    $personal = $resultado->fetch_array();
}

echo json_encode($personal, JSON_UNESCAPED_UNICODE);
