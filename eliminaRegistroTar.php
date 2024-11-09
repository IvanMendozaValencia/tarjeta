<?php



session_start();

require 'funcs/conexion.php';


$id_tarjeta = $mysqli->real_escape_string($_POST['id_tarjeta']); 

//validando no tenga registgros con visitas

$sqlvisita   = ("SELECT 
    visitas.fk_visita_tarjeta
FROM
    datos_identificacion
        INNER JOIN
    visitas ON datos_identificacion.id_tarjeta = visitas.fk_visita_tarjeta
WHERE
    id_tarjeta = $id_tarjeta");
$resultado = $mysqli->query($sqlvisita);
$rows = $resultado->num_rows;
$visita = $resultado->fetch_array();

//validando no tenga registgros con recien nacidos
$sqlrn   = ("SELECT 
    recien_nacida.fk_id_tarjeta
FROM
    datos_identificacion
        INNER JOIN
    recien_nacida ON datos_identificacion.id_tarjeta = recien_nacida.fk_id_tarjeta
WHERE
    id_tarjeta = $id_tarjeta");
$resultado = $mysqli->query($sqlrn);
$rows = $resultado->num_rows;
$recien_nacida = $resultado->fetch_array();

//validando no tenga registgros con periodo de lactancia
$sqlpl   = ("SELECT 
    periodo_lactancia.fk_id_tarjeta
FROM
    datos_identificacion
        INNER JOIN
    periodo_lactancia ON datos_identificacion.id_tarjeta = periodo_lactancia.fk_id_tarjeta
WHERE
    id_tarjeta = $id_tarjeta");
$resultado = $mysqli->query($sqlpl);
$rows = $resultado->num_rows;
$periodo_lactancia = $resultado->fetch_array();

//validando no tenga registgros con puerperio
$sqlpuer   = ("SELECT 
    puerperio.fk_id_tarjeta
FROM
    datos_identificacion
        INNER JOIN
    puerperio ON datos_identificacion.id_tarjeta = puerperio.fk_id_tarjeta
WHERE
    id_tarjeta = $id_tarjeta");
$resultado = $mysqli->query($sqlpuer);
$rows = $resultado->num_rows;
$puerperio = $resultado->fetch_array();

//validando no tenga registgros con citas
$sqlcitas   = ("SELECT 
    citas_prenatales.fk_id_tarjeta
FROM
    datos_identificacion
        INNER JOIN
    citas_prenatales ON datos_identificacion.id_tarjeta = citas_prenatales.fk_id_tarjeta
WHERE
    id_tarjeta = $id_tarjeta");
$resultado = $mysqli->query($sqlcitas);
$rows = $resultado->num_rows;
$citas = $resultado->fetch_array();

  //Validamos que la consulta haya retornado información
  if(($visita > 0 )||($recien_nacida > 0 )||($periodo_lactancia > 0 )||($puerperio > 0 )||($citas > 0 )){
    $_SESSION['color'] = "danger";
    $_SESSION['msg'] = "No es possible eliminar el registro ya que está vinculado con una(s): Cita, Visita, Recién Nacido(s), Periodo de Puerperio, Periodo de Lactancia";
  }else{
         $sql = "DELETE FROM datos_identificacion WHERE id_tarjeta='$id_tarjeta'";
        if ($mysqli->query($sql)) {
            $_SESSION['color'] = "success";
            $_SESSION['msg'] = "Registro eliminado";
        }     
  }

header('Location: registrostarjeta.php');

