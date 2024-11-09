<?php

session_start();

require 'funcs/conexion.php';
require 'funcs/funcs.php';

if(!isset($_SESSION["id_usuario"]))
{
    header("Location: index.php");
}


$idUsusario = $_SESSION['id_usuario'];
$sql = "SELECT id, usuario, nombre, correo, last_session, id_tipo, a_usuarios, a_programas, a_beneficiarios, a_componentes, a_actividades, a_apoyos, a_consultas, a_estadisticas, a_agregar, a_modificar, a_eliminar, unidad_administrativa, b_programa_id FROM usuarios WHERE id ='$idUsusario'";
$result = $mysqli->query($sql);
$row_usuario = $result->fetch_assoc();
$id_usuario_programa=$row_usuario['b_programa_id'];
$id_tipo_usuario=$row_usuario['id_tipo'];


if ($id_tipo_usuario != 1){
    $sqlProgramas = "SELECT programa.id_programa, programa.nombre_programa FROM programa INNER JOIN usuarios on programa.id_programa = usuarios.b_programa_id where usuarios.b_programa_id = $id_usuario_programa and visible=1 group by id_programa order by nombre_programa ASC";
    $programas = $mysqli->query($sqlProgramas);
}else{
    $sqlProgramas = "SELECT programa.id_programa, programa.nombre_programa FROM programa where visible=1  order by nombre_programa ASC";
    $programas = $mysqli->query($sqlProgramas);
}

$f_inicio = 0;
$f_final = 0;
$_programa = '';
$_nombre_componente = '';
$_nombre_tipo_apoyo = '';
$_nombre_evento = '';
//*PRIMERA CONSULTA 4 VARIABLES Y FECHA
// SI SELECCIONA PROGRAMA, COMPONENTE, ACTIVIDAD, EVENTO Y FECHA
if((!empty($_GET['programa'])) && (!empty($_GET['componente']))  && (!empty($_GET['tipo_apoyo']))  && (!empty($_GET['evento_id'])) && (!empty($_GET['fechainicio']) && !empty($_GET['fechafinal'])) ){
    $id_usuario_programa =($_GET['programa']);
    $id_componente =($_GET['componente']);
    $id_actividad =($_GET['tipo_apoyo']);
    $id_evento =($_GET['evento_id']);
  
    $sqlProgramas = "SELECT 
    programa.id_programa,    
    nombre_programa,
    componente.id_componente,
    componente.nombre_componente,
    tipo_apoyo.id_tipo_apoyo,
    tipo_apoyo.nombre_tipo_apoyo,
    evento.id_evento, 
    evento.nombre_evento
FROM
    programa
    INNER JOIN componente on componente.com_id_programa = programa.id_programa
    INNER JOIN tipo_apoyo on tipo_apoyo.ta_id_componente = componente.id_componente
    INNER JOIN evento on evento.id_accion_evento = tipo_apoyo.id_tipo_apoyo
    WHERE programa.id_programa  = $id_usuario_programa 
    AND componente.id_componente = $id_componente
    AND tipo_apoyo.id_tipo_apoyo = $id_actividad
    AND evento.id_evento = $id_evento ";

    $programas = $mysqli->query($sqlProgramas);
    $row_programa  = $programas->fetch_assoc();
    $_programa=$row_programa ['nombre_programa'];
    $_nombre_componente=$row_programa ['nombre_componente'];
    $_nombre_tipo_apoyo=$row_programa ['nombre_tipo_apoyo'];
    $_nombre_evento=$row_programa ['nombre_evento'];
    
    $f_inicio =($_GET['fechainicio']);
    $f_final =($_GET['fechafinal']);

                
        //-----GRÁFICO PRESUPUESTO EJERCIDO POR ACTIVIDAD
        //////////
            $pastel = "SELECT tipo_apoyo.id_tipo_apoyo, tipo_apoyo.nombre_tipo_apoyo, tipo_apoyo.ta_id_componente, tipo_apoyo.meta_um_servicio, tipo_apoyo.meta_um_beneficio, tipo_apoyo.meta_cantidad_servicio, tipo_apoyo.meta_cantidad_beneficio, tipo_apoyo.meta_alcanzada_cantidad_servicio, tipo_apoyo.meta_alcanzada_cantidad_beneficio, tipo_apoyo.tipo_apoyo_presupuesto_ejercido, componente.nombre_componente, programa.nombre_programa, programa.id_programa, evento.fecha_evento,
            reg_partidas.monto_partidas, sum(reg_partidas.monto_partidas) as programa_presupuesto_ejercido_fechas  
            FROM  programa
            INNER JOIN componente on componente.com_id_programa = programa.id_programa
            INNER JOIN tipo_apoyo on tipo_apoyo.ta_id_componente = componente.id_componente
            INNER JOIN evento on evento.id_accion_evento = tipo_apoyo.id_tipo_apoyo
            INNER JOIN reg_partidas on reg_partidas.id_evento_partidas = evento.id_evento  
            where programa.id_programa=$id_usuario_programa  and componente.id_componente='$id_componente' and tipo_apoyo.id_tipo_apoyo='$id_actividad' and evento.id_evento='$id_evento' and (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento<=  ' + $f_final + ') ORDER BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo";

        $pastel1 = $mysqli->query($pastel);
        $pastel_rows = array();
        $pastel_table6 = array();

        $pastel_table6['cols']= array(
            array('label'=>'Actividad', 'type'=>'string'),
            array('label'=>'Monto', 'type'=>'number')

        );
            foreach($pastel1 as $p){
                $pastel_temp = array();
                $pastel_temp[] = array('v'=>(string) $p['nombre_tipo_apoyo']);
                $pastel_temp[] = array('v'=>(integer) $p['programa_presupuesto_ejercido_fechas']);
                $pastel_rows[] = array ('c'=>$pastel_temp);
            }
            $pastel_table6['rows']=$pastel_rows;
            $pasteljsonTable6 =json_encode($pastel_table6);  //GRAFICO 11

        //////////Metas programadas vs Metas alcanzadas por Actividad
            $tabla = "SELECT tipo_apoyo.id_tipo_apoyo, tipo_apoyo.nombre_tipo_apoyo, tipo_apoyo.ta_id_componente, tipo_apoyo.meta_um_servicio, tipo_apoyo.meta_um_beneficio, tipo_apoyo.meta_cantidad_servicio, tipo_apoyo.meta_cantidad_beneficio, tipo_apoyo.meta_alcanzada_cantidad_servicio, tipo_apoyo.meta_alcanzada_cantidad_beneficio, tipo_apoyo.tipo_apoyo_presupuesto_ejercido, componente.nombre_componente, programa.nombre_programa, programa.id_programa,COUNT( meta_alcanzada_cantidad_servicio ) AS tipo_apoyoA,
            concat(round(( meta_alcanzada_cantidad_servicio/meta_cantidad_servicio * 100 ),2),'%') AS porcentaje_servicio,
            COUNT( meta_alcanzada_cantidad_beneficio ) AS tipo_apoyoA,
            concat(round(( meta_alcanzada_cantidad_beneficio/meta_cantidad_beneficio * 100 ),2),'%') AS porcentaje_beneficio,  servicios.nombre_servicios,  beneficios.nombre_beneficios,
            evento.fecha_evento,
            reg_partidas.monto_partidas,
            sum(reg_partidas.monto_partidas) as programa_presupuesto_ejercido_fechas  
            FROM programa
            INNER JOIN componente on componente.com_id_programa = programa.id_programa
            INNER JOIN tipo_apoyo on tipo_apoyo.ta_id_componente = componente.id_componente
            INNER JOIN evento on evento.id_accion_evento = tipo_apoyo.id_tipo_apoyo
            INNER JOIN reg_partidas on reg_partidas.id_evento_partidas = evento.id_evento  
            INNER JOIN beneficios ON beneficios.id_beneficios = tipo_apoyo.meta_um_beneficio 
            INNER JOIN servicios ON servicios.id_servicios = tipo_apoyo.meta_um_servicio
            where programa.id_programa=$id_usuario_programa  and componente.id_componente='$id_componente' and tipo_apoyo.id_tipo_apoyo='$id_actividad' and evento.id_evento='$id_evento' and (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento<=  ' + $f_final + ') group by id_tipo_apoyo ORDER BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo";
            $tabla1_exportar = $mysqli->query($tabla);
            $tabla1 = $mysqli->query($tabla);
            $tabla_rows = array();
            $tabla_table = array();

            $tabla_table['cols']= array(
                array('label'=>'Componente', 'type'=>'string'),
                array('label'=>'Actividad', 'type'=>'string'),
                array('label'=>'Monto ejercido', 'type'=>'number'),
                array('label'=>'U.M. Servicios', 'type'=>'string'),
                array('label'=>'Meta', 'type'=>'number'),
                array('label'=>'Avance', 'type'=>'number'),
                array('label'=>'%', 'type'=>'string'),
                array('label'=>'U.M. Beneficio', 'type'=>'string'),
                array('label'=>'Meta', 'type'=>'number'),
                array('label'=>'Avance', 'type'=>'number'),
                array('label'=>'%', 'type'=>'string'),

            );
                foreach($tabla1 as $b){
                    $tabla_temp = array();
                    $tabla_temp[] = array('v'=>(string) $b['nombre_componente']);
                    $tabla_temp[] = array('v'=>(string) $b['nombre_tipo_apoyo']);
                    $tabla_temp[] = array('v'=>(integer) $b['programa_presupuesto_ejercido_fechas']);
                    $tabla_temp[] = array('v'=>(string) $b['nombre_servicios']);
                    $tabla_temp[] = array('v'=>(integer) $b['meta_cantidad_servicio']);
                    $tabla_temp[] = array('v'=>(integer) $b['meta_alcanzada_cantidad_servicio']);
                    $tabla_temp[] = array('v'=>(string) $b['porcentaje_servicio']);
                    $tabla_temp[] = array('v'=>(string) $b['nombre_beneficios']);
                    $tabla_temp[] = array('v'=>(integer) $b['meta_cantidad_beneficio']);
                    $tabla_temp[] = array('v'=>(integer) $b['meta_alcanzada_cantidad_beneficio']);
                    $tabla_temp[] = array('v'=>(string) $b['porcentaje_beneficio']);
                    $tabla_rows[] = array ('c'=>$tabla_temp);
                }
                $tabla_table['rows']=$tabla_rows;
                $tablajsonTable =json_encode($tabla_table);   //GRAFICO 12  

                ////////////////Desglose de eventos realizados

                $tabla_evento = "SELECT 
                evento.id_evento,
                evento.nombre_evento,
                evento.pob_obj_evento,
                evento.nivel_geo_evento,
                evento.lugar_evento,
                evento.monto_evento,
                evento.fecha_evento,
                evento.fecha_modificacion_evento,
                evento.usuario_evento,
                evento.id_accion_evento,
                evento.cantidad_servicio,
                evento.cantidad_beneficio,
                evento.hombres, 
                evento.mujeres,
                region.region,
                municipio.municipio,
                localidad.localidad, 
                tipo_apoyo.nombre_tipo_apoyo,
                servicios.nombre_servicios,
                beneficios.nombre_beneficios,
                componente.nombre_componente,
                COUNT(evento.hombres) AS tipo_apoyoA,       
                concat(round((evento.hombres/cantidad_beneficio * 100 ),2),'%') AS porcentaje_hombres,
                COUNT(evento.mujeres) AS tipo_apoyoA,       
                concat(round((evento.mujeres/cantidad_beneficio * 100 ),2),'%') AS porcentaje_mujeres  
                FROM
                evento
                INNER JOIN
                localidad ON localidad.cve_localidad = evento.lugar_evento
                    INNER JOIN
                municipio ON municipio.cve_municipios = localidad.l_cve_municipio
                    INNER JOIN
                region ON region.cve_regiones = municipio.m_cve_region
                    INNER JOIN
                usuarios ON usuarios.id = evento.usuario_evento
                    INNER JOIN
                tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
                    INNER JOIN
                componente ON componente.id_componente = tipo_apoyo.ta_id_componente
                    INNER JOIN
                programa ON componente.com_id_programa = programa.id_programa  
                INNER JOIN
                beneficios ON beneficios.id_beneficios = tipo_apoyo.meta_um_beneficio
                        INNER JOIN
                servicios ON servicios.id_servicios = tipo_apoyo.meta_um_servicio
                where programa.id_programa=$id_usuario_programa  and componente.id_componente='$id_componente' and tipo_apoyo.id_tipo_apoyo='$id_actividad' and evento.id_evento='$id_evento' and (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento<=  ' + $f_final + ')
                group by evento.id_evento
                ORDER BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento";

                $tabla1_evento_exportar = $mysqli->query($tabla_evento);
                $tabla1_evento = $mysqli->query($tabla_evento);
                $tabla_evento_rows = array();
                $tabla_evento_table = array();

                $tabla_evento_table['cols']= array(
                    array('label'=>'Componente', 'type'=>'string'),
                    array('label'=>'Actividad', 'type'=>'string'),
                    array('label'=>'Evento', 'type'=>'string'),
                    array('label'=>'Monto ejercido', 'type'=>'number'),
                    array('label'=>'Fecha', 'type'=>'string'),
                    array('label'=>'U.M. Servicios', 'type'=>'string'),
                    array('label'=>'Meta alcanzada', 'type'=>'number'),
                    array('label'=>'U.M. Beneficio', 'type'=>'string'),
                    array('label'=>'Meta alcanzada', 'type'=>'number'),
                    array('label'=>'Hombres', 'type'=>'number'),
                    array('label'=>'%', 'type'=>'string'),
                    array('label'=>'Mujeres', 'type'=>'number'),
                    array('label'=>'%', 'type'=>'string'),             

                );
                    foreach($tabla1_evento as $b){
                        $tabla_evento_temp = array();
                        $tabla_evento_temp[] = array('v'=>(string) $b['nombre_componente']);
                        $tabla_evento_temp[] = array('v'=>(string) $b['nombre_tipo_apoyo']);
                        $tabla_evento_temp[] = array('v'=>(string) $b['nombre_evento']);
                        $tabla_evento_temp[] = array('v'=>(integer) $b['monto_evento']);
                        $tabla_evento_temp[] = array('v'=>(string) $b['fecha_evento']);
                        $tabla_evento_temp[] = array('v'=>(string) $b['nombre_servicios']);                    
                        $tabla_evento_temp[] = array('v'=>(integer) $b['cantidad_servicio']);
                        $tabla_evento_temp[] = array('v'=>(string) $b['nombre_beneficios']);
                        $tabla_evento_temp[] = array('v'=>(integer) $b['cantidad_beneficio']);
                        $tabla_evento_temp[] = array('v'=>(integer) $b['hombres']);
                        $tabla_evento_temp[] = array('v'=>(string) $b['porcentaje_hombres']);
                        $tabla_evento_temp[] = array('v'=>(integer) $b['mujeres']);
                        $tabla_evento_temp[] = array('v'=>(string) $b['porcentaje_mujeres']);
                        $tabla_evento_rows[] = array ('c'=>$tabla_evento_temp);
                    }
                    $tabla_evento_table['rows']=$tabla_evento_rows;
                    $tabla_evento_jsonTable =json_encode($tabla_evento_table);  //GRAFICO 15


            //-----GRÁFICO POR OBJETO DEL GASTO
                    //////////
                        $pastel = "SELECT reg_partidas.id_reg_partidas, reg_partidas.partida, reg_partidas.cantidad_partidas, reg_partidas.monto_partidas, reg_partidas.id_evento_partidas,
                        sum(reg_partidas.monto_partidas) as totalsuma,
                        objeto_de_gasto.partida_gasto_texto,
                        evento.fecha_evento,
                        reg_partidas.monto_partidas,
                        sum(reg_partidas.monto_partidas) as programa_presupuesto_ejercido_fechas
                        FROM reg_partidas
                        INNER JOIN evento on evento.id_evento = reg_partidas.id_evento_partidas
                        INNER JOIN tipo_apoyo on tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
                        INNER JOIN componente on componente.id_componente = tipo_apoyo.ta_id_componente
                        INNER JOIN programa on programa.id_programa = componente.com_id_programa
                        INNER JOIN objeto_de_gasto on objeto_de_gasto.clave = reg_partidas.partida
                        where programa.id_programa=$id_usuario_programa  and componente.id_componente='$id_componente' and tipo_apoyo.id_tipo_apoyo='$id_actividad' and evento.id_evento='$id_evento' and (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento<=  ' + $f_final + ')
                        group by reg_partidas.partida";

                        $pastel1 = $mysqli->query($pastel);
                        $pastel_rows = array();
                        $pastel_table9 = array();

                        $pastel_table9['cols']= array(
                            array('label'=>'Objeto de gasto', 'type'=>'string'),
                            array('label'=>'Presupuesto ejercido', 'type'=>'number')

                        );
                            foreach($pastel1 as $p){
                                $pastel_temp = array();
                                $pastel_temp[] = array('v'=>(string) $p['partida_gasto_texto']);
                                $pastel_temp[] = array('v'=>(integer) $p['totalsuma']);
                                $pastel_rows[] = array ('c'=>$pastel_temp);
                            }
                            $pastel_table9['rows']=$pastel_rows;
                            $pasteljsonTable9 =json_encode($pastel_table9);  //GRAFICO 18

                        //////////
        ////////////////Tabla Presupuesto ejercido a nivel de objeto de gasto (partidas y monto ejercido)

            $tabla_partida = "SELECT 
            id_reg_partidas,
            partida,
            cantidad_partidas,
            monto_partidas,
            id_evento_partidas,
            partida_gasto_texto,
            evento.nombre_evento,
            programa.nombre_programa,
            componente.nombre_componente,
            tipo_apoyo.nombre_tipo_apoyo,
            capitulo,
            evento.fecha_evento,
            reg_partidas.monto_partidas,
            sum(reg_partidas.monto_partidas) as programa_presupuesto_ejercido_fechas
        FROM
            reg_partidas
                INNER JOIN
            objeto_de_gasto ON clave = partida
            INNER JOIN 
            evento ON evento.id_evento = reg_partidas.id_evento_partidas
            INNER JOIN 
            tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
            INNER JOIN 
            componente ON componente.id_componente = tipo_apoyo.ta_id_componente
            INNER JOIN
            programa ON programa.id_programa = componente.com_id_programa
            WHERE
            programa.id_programa=$id_usuario_programa  and componente.id_componente='$id_componente' and tipo_apoyo.id_tipo_apoyo='$id_actividad' and evento.id_evento='$id_evento' and (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento<=  ' + $f_final + ')
            group by reg_partidas.id_reg_partidas
            ORDER BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento, reg_partidas.id_reg_partidas";
            $tabla1_partida_exportar = $mysqli->query($tabla_partida);
            $tabla1_partida = $mysqli->query($tabla_partida);
            $tabla_partida_rows = array();
            $tabla_partida_table = array();

            $tabla_partida_table['cols']= array(
                array('label'=>'Programa', 'type'=>'string'),
                array('label'=>'Componente', 'type'=>'string'),
                array('label'=>'Actividad', 'type'=>'string'),
                array('label'=>'Evento', 'type'=>'string'),
                array('label'=>'Capítulo', 'type'=>'number'),
                array('label'=>'Partida', 'type'=>'string'),
                array('label'=>'Monto', 'type'=>'number'),

            );
                foreach($tabla1_partida as $b){
                    $tabla_partida_temp = array();
                    $tabla_partida_temp[] = array('v'=>(string) $b['nombre_programa']);
                    $tabla_partida_temp[] = array('v'=>(string) $b['nombre_componente']);
                    $tabla_partida_temp[] = array('v'=>(string) $b['nombre_tipo_apoyo']);
                    $tabla_partida_temp[] = array('v'=>(string) $b['nombre_evento']);
                    $tabla_partida_temp[] = array('v'=>(integer) $b['capitulo']);
                    $tabla_partida_temp[] = array('v'=>(string) $b['partida_gasto_texto']);
                    $tabla_partida_temp[] = array('v'=>(integer) $b['monto_partidas']);
                    $tabla_partida_rows[] = array ('c'=>$tabla_partida_temp);
                }
                $tabla_partida_table['rows']=$tabla_partida_rows;
                $tabla_partida_jsonTable =json_encode($tabla_partida_table);  //GRAFICO 19

                //-----GRÁFICO POR OBJETO DEL GASTO CAPÍTULO
                    //////////
                    $pastel = "SELECT capitulo,  evento.fecha_evento, SUM(monto_partidas) AS capitulo_total
                    FROM reg_partidas
                    INNER JOIN evento on evento.id_evento = reg_partidas.id_evento_partidas
                    INNER JOIN tipo_apoyo on tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
                    INNER JOIN componente on componente.id_componente = tipo_apoyo.ta_id_componente
                    INNER JOIN programa on programa.id_programa = componente.com_id_programa
                    INNER JOIN objeto_de_gasto on objeto_de_gasto.clave = reg_partidas.partida
                    where programa.id_programa=$id_usuario_programa  and componente.id_componente='$id_componente' and tipo_apoyo.id_tipo_apoyo='$id_actividad' and evento.id_evento='$id_evento' and (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento<=  ' + $f_final + ')
                    group by capitulo";

                    $pastel1 = $mysqli->query($pastel);
                    $pastel_rows = array();
                    $pastel_table10 = array();

                    $pastel_table10['cols']= array(
                        array('label'=>'Capítulo', 'type'=>'string'),
                        array('label'=>'Presupuesto ejercido', 'type'=>'number')

                    );
                        foreach($pastel1 as $p){
                            $pastel_temp = array();
                            $pastel_temp[] = array('v'=>(integer) $p['capitulo']);
                            $pastel_temp[] = array('v'=>(integer) $p['capitulo_total']);
                            $pastel_rows[] = array ('c'=>$pastel_temp);
                        }
                        $pastel_table10['rows']=$pastel_rows;
                        $pasteljsonTable10 =json_encode($pastel_table10);  //GRAFICO 16

                    //////////

                    ////////////////Tabla Presupuesto ejercido a nivel de objeto de gasto (capitulo y monto ejercido)

                    $tabla_capitulo = "SELECT 
                    programa.nombre_programa,
                    componente.nombre_componente,
                    tipo_apoyo.nombre_tipo_apoyo,
                    evento.nombre_evento,
                    capitulo,
                    evento.fecha_evento,
                    SUM(monto_partidas) AS capitulo_total
                FROM
                    reg_partidas
                        INNER JOIN
                    objeto_de_gasto ON clave = partida
                    INNER JOIN 
                    evento ON evento.id_evento = reg_partidas.id_evento_partidas
                    INNER JOIN 
                    tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
                    INNER JOIN 
                    componente ON componente.id_componente = tipo_apoyo.ta_id_componente
                    INNER JOIN
                    programa ON programa.id_programa = componente.com_id_programa
                    WHERE
                    programa.id_programa=$id_usuario_programa  and componente.id_componente='$id_componente' and tipo_apoyo.id_tipo_apoyo='$id_actividad' and evento.id_evento='$id_evento' and (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento<=  ' + $f_final + ')
                    GROUP BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento, capitulo
                    ORDER BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento, capitulo";
                    $tabla1_capitulo_exportar = $mysqli->query($tabla_capitulo);
                    $tabla1_capitulo = $mysqli->query($tabla_capitulo);
                    $tabla_capitulo_rows = array();
                    $tabla_capitulo_table = array();

                    $tabla_capitulo_table['cols']= array(
                        array('label'=>'Programa', 'type'=>'string'),
                        array('label'=>'Componente', 'type'=>'string'),
                        array('label'=>'Actividad', 'type'=>'string'),
                        array('label'=>'Evento', 'type'=>'string'),
                        array('label'=>'Capítulo', 'type'=>'number'),
                        array('label'=>'Monto', 'type'=>'number'),

                    );
                        foreach($tabla1_capitulo as $b){
                            $tabla_capitulo_temp = array();
                            $tabla_capitulo_temp[] = array('v'=>(string) $b['nombre_programa']);
                            $tabla_capitulo_temp[] = array('v'=>(string) $b['nombre_componente']);
                            $tabla_capitulo_temp[] = array('v'=>(string) $b['nombre_tipo_apoyo']);
                            $tabla_capitulo_temp[] = array('v'=>(string) $b['nombre_evento']);
                            $tabla_capitulo_temp[] = array('v'=>(integer) $b['capitulo']);
                            $tabla_capitulo_temp[] = array('v'=>(integer) $b['capitulo_total']);
                            $tabla_capitulo_rows[] = array ('c'=>$tabla_capitulo_temp);
                        }
                        $tabla_capitulo_table['rows']=$tabla_capitulo_rows;
                        $tabla_capitulo_jsonTable =json_encode($tabla_capitulo_table);  //GRAFICO 17        
        

              
////RESUMEN POR TIPO DE ACCIONES (TRANSVERSALIDAD, ACCIONES, PREVENCION)

    $tabla_acciones = "SELECT programa.nombre_programa, tipo_apoyo.tipo_accion, sum(evento.cantidad_beneficio) as personas_atendidas, sum(evento.hombres) as hombres, sum(evento.mujeres) as mujeres,
    sum(evento.monto_evento)as inversion_ejercido,
    count(tipo_apoyo.tipo_accion) as acciones
    FROM
        tipo_apoyo 
    INNER JOIN
        componente ON componente.id_componente = tipo_apoyo.ta_id_componente
    INNER JOIN
        programa ON componente.com_id_programa = programa.id_programa
    INNER JOIN  
        evento ON evento.id_accion_evento = tipo_apoyo.id_tipo_apoyo   
    WHERE programa.id_programa = $id_usuario_programa  and componente.id_componente='$id_componente' and tipo_apoyo.id_tipo_apoyo='$id_actividad' and evento.id_evento='$id_evento' AND  (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento <=  ' + $f_final + ')
    group by  programa.id_programa, tipo_accion
    order by  tipo_accion";
    $tabla_acciones1_exportar = $mysqli->query($tabla_acciones);
    $tabla_acciones1 = $mysqli->query($tabla_acciones);
    $tabla_acciones_rows = array();
    $tabla_acciones_table = array();

    $tabla_acciones_table['cols']= array(
        array('label'=>'Programa', 'type'=>'string'),
        array('label'=>'Tipo de Acción', 'type'=>'string'),
        array('label'=>'Personas Atendidas', 'type'=>'number'),
		array('label'=>'Hombres', 'type'=>'number'),
		array('label'=>'Mujeres', 'type'=>'number'),
        array('label'=>'Monto ejercido', 'type'=>'number'),  
        array('label'=>'Número de acciones', 'type'=>'number'),

    );
        foreach($tabla_acciones1 as $b){
            $tabla_temp = array();
            $tabla_temp[] = array('v'=>(string) $b['nombre_programa']);
            $tabla_temp[] = array('v'=>(string) $b['tipo_accion']);
            $tabla_temp[] = array('v'=>(integer) $b['personas_atendidas']);
			$tabla_temp[] = array('v'=>(integer) $b['hombres']);
			$tabla_temp[] = array('v'=>(integer) $b['mujeres']);
            $tabla_temp[] = array('v'=>(integer) $b['inversion_ejercido']);
            $tabla_temp[] = array('v'=>(integer) $b['acciones']);
            $tabla_acciones_rows[] = array ('c'=>$tabla_temp);
        }
        $tabla_acciones_table['rows']=$tabla_acciones_rows;
        $tabla_accionesjsonTable =json_encode($tabla_acciones_table);  //GRAFICO 13   
////  

////DESGLOSE POR TIPO DE ACCIONES (TRANSVERSALIDAD, ACCIONES, PREVENCION)

$tabla_acciones = "SELECT programa.nombre_programa, componente.nombre_componente, tipo_apoyo.tipo_accion, evento.nombre_evento, sum(evento.cantidad_beneficio) as personas_atendidas, sum(evento.hombres) as hombres, sum(evento.mujeres) as mujeres,
sum(evento.monto_evento)as inversion_ejercido
FROM
	tipo_apoyo 
INNER JOIN
	componente ON componente.id_componente = tipo_apoyo.ta_id_componente
INNER JOIN
	programa ON componente.com_id_programa = programa.id_programa
INNER JOIN  
	evento ON evento.id_accion_evento = tipo_apoyo.id_tipo_apoyo   
WHERE programa.id_programa = $id_usuario_programa  and componente.id_componente='$id_componente' and tipo_apoyo.id_tipo_apoyo='$id_actividad' and evento.id_evento='$id_evento' AND  (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento <=  ' + $f_final + ')
group by programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento, evento.fecha_evento, tipo_accion
order by  tipo_accion";
$tabla_acciones1_desglose_exportar = $mysqli->query($tabla_acciones);
$tabla_acciones1 = $mysqli->query($tabla_acciones);
$tabla_acciones_rows = array();
$tabla_acciones_table = array();

$tabla_acciones_table['cols']= array(
    array('label'=>'Programa', 'type'=>'string'),
    array('label'=>'Componente', 'type'=>'string'),
    array('label'=>'Tipo de Acción', 'type'=>'string'),
    array('label'=>'Evento', 'type'=>'string'),
    array('label'=>'Personas Atendidas', 'type'=>'number'),
	array('label'=>'Hombres', 'type'=>'number'),
	array('label'=>'Mujeres', 'type'=>'number'),
    array('label'=>'Monto ejercido', 'type'=>'number'),  


);
    foreach($tabla_acciones1 as $b){
        $tabla_temp = array();    
        $tabla_temp[] = array('v'=>(string) $b['nombre_programa']);
        $tabla_temp[] = array('v'=>(string) $b['nombre_componente']);
        $tabla_temp[] = array('v'=>(string) $b['tipo_accion']);
        $tabla_temp[] = array('v'=>(string) $b['nombre_evento']);
        $tabla_temp[] = array('v'=>(integer) $b['personas_atendidas']);
		$tabla_temp[] = array('v'=>(integer) $b['hombres']);
		$tabla_temp[] = array('v'=>(integer) $b['mujeres']);
        $tabla_temp[] = array('v'=>(integer) $b['inversion_ejercido']);

        $tabla_acciones_rows[] = array ('c'=>$tabla_temp);
    }
    $tabla_acciones_table['rows']=$tabla_acciones_rows;
    $tabla_acciones_desglosejsonTable =json_encode($tabla_acciones_table);   //GRAFICO 14 
	
	///////INICIO REGION, MUNICIPIO ---EVENTO
//-----GRÁFICO POR REGION GRAFICO PAY

    $pastel = "SELECT  region.region, COUNT(region.region) AS Total_Cuenta 
    FROM
    evento
    INNER JOIN localidad ON localidad.cve_localidad = evento.lugar_evento
    INNER JOIN municipio ON municipio.cve_municipios = localidad.l_cve_municipio
    INNER JOIN region ON region.cve_regiones = municipio.m_cve_region
    INNER JOIN tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
    INNER JOIN componente ON componente.id_componente = tipo_apoyo.ta_id_componente
    INNER JOIN programa ON programa.id_programa = componente.com_id_programa
    WHERE programa.id_programa = $id_usuario_programa   and componente.id_componente='$id_componente' and tipo_apoyo.id_tipo_apoyo='$id_actividad' and evento.id_evento='$id_evento'
    AND  (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento <=  ' + $f_final + ')  
    GROUP BY region.region ORDER BY  region.region  ASC";


    $pastel1 = $mysqli->query($pastel);
    $pastel_rows = array();
    $pastel_table1evento = array();

    $pastel_table1evento['cols']= array(
        array('label'=>'Región', 'type'=>'string'),
        array('label'=>'Cantidad', 'type'=>'number')

    );
        foreach($pastel1 as $p){
            $pastel_temp = array();
            $pastel_temp[] = array('v'=>(string) $p['region']);
            $pastel_temp[] = array('v'=>(integer) $p['Total_Cuenta']);
            $pastel_rows[] = array ('c'=>$pastel_temp);
        }
        $pastel_table1evento['rows']=$pastel_rows;
        $pasteljsonTable1evento =json_encode($pastel_table1evento);

//////////

//-----GRÁFICO POR MUNICIPIO -GRÁFICO PAY

        ///////////

            $pastel = "SELECT  municipio.municipio, COUNT(municipio.municipio) AS Total_Cuenta 
            FROM
            evento 
	        INNER JOIN localidad ON localidad.cve_localidad = evento.lugar_evento
            INNER JOIN municipio ON municipio.cve_municipios = localidad.l_cve_municipio
            INNER JOIN region ON region.cve_regiones = municipio.m_cve_region
            INNER JOIN tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
            INNER JOIN componente ON componente.id_componente = tipo_apoyo.ta_id_componente
            INNER JOIN programa ON programa.id_programa = componente.com_id_programa
            WHERE programa.id_programa = $id_usuario_programa   and componente.id_componente='$id_componente' and tipo_apoyo.id_tipo_apoyo='$id_actividad' and evento.id_evento='$id_evento'
            AND  (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento <=  ' + $f_final + ')   
            GROUP BY municipio.municipio ORDER BY  municipio.municipio  ASC";

            $tabla_localidades = "SELECT programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento, evento.fecha_evento, region.region, municipio.municipio, localidad.localidad, localidad.latitud_dec, localidad.longitud_dec, COUNT(localidad.localidad) AS Total_Cuenta 
           FROM
            evento 
	        INNER JOIN localidad ON localidad.cve_localidad = evento.lugar_evento
            INNER JOIN municipio ON municipio.cve_municipios = localidad.l_cve_municipio
            INNER JOIN region ON region.cve_regiones = municipio.m_cve_region
            INNER JOIN tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
            INNER JOIN componente ON componente.id_componente = tipo_apoyo.ta_id_componente
            INNER JOIN programa ON programa.id_programa = componente.com_id_programa
            WHERE programa.id_programa = $id_usuario_programa   and componente.id_componente='$id_componente' and tipo_apoyo.id_tipo_apoyo='$id_actividad' and evento.id_evento='$id_evento'
            AND  (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento <=  ' + $f_final + ')   
            GROUP BY localidad.localidad ORDER BY  localidad.localidad  ASC";

        $tablalocexportar = $mysqli->query($tabla_localidades);
        $pastel1 = $mysqli->query($pastel);
        $pastel_rows = array();
        $pastel_table2evento = array();

        $pastel_table2evento['cols']= array(
            array('label'=>'Municipio %', 'type'=>'string'),
            array('label'=>'Cantidad', 'type'=>'number')

        );
            foreach($pastel1 as $p){
                $pastel_temp = array();
                $pastel_temp[] = array('v'=>(string) $p['municipio']);
                $pastel_temp[] = array('v'=>(integer) $p['Total_Cuenta']);
                $pastel_rows[] = array ('c'=>$pastel_temp);
            }
            $pastel_table2evento['rows']=$pastel_rows;
            $pasteljsonTable2evento =json_encode($pastel_table2evento);

        //////////
//-----GRÁFICO POR GÉNERO
			
				$sqlh="UPDATE hombre_mujer SET hombre_mujer.cantidad_h_m =(SELECT SUM(evento.hombres) AS Total_CuentaH 
                FROM
                evento 
	            INNER JOIN localidad ON localidad.cve_localidad = evento.lugar_evento
                INNER JOIN municipio ON municipio.cve_municipios = localidad.l_cve_municipio
                INNER JOIN region ON region.cve_regiones = municipio.m_cve_region
                INNER JOIN tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
                INNER JOIN componente ON componente.id_componente = tipo_apoyo.ta_id_componente
                INNER JOIN programa ON programa.id_programa = componente.com_id_programa
                WHERE programa.id_programa = $id_usuario_programa  and componente.id_componente='$id_componente' and tipo_apoyo.id_tipo_apoyo='$id_actividad' and evento.id_evento='$id_evento'
                AND  (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento <=  ' + $f_final + '))
				WHERE hombre_mujer.h_m ='HOMBRE'";
				$mysqli->query($sqlh);
				
				$sqlm="UPDATE hombre_mujer SET hombre_mujer.cantidad_h_m =( SELECT SUM( evento.mujeres) AS Total_CuentaM 
                FROM
                evento 
	            INNER JOIN localidad ON localidad.cve_localidad = evento.lugar_evento
                INNER JOIN municipio ON municipio.cve_municipios = localidad.l_cve_municipio
                INNER JOIN region ON region.cve_regiones = municipio.m_cve_region
                INNER JOIN tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
                INNER JOIN componente ON componente.id_componente = tipo_apoyo.ta_id_componente
                INNER JOIN programa ON programa.id_programa = componente.com_id_programa
                WHERE programa.id_programa = $id_usuario_programa  and componente.id_componente='$id_componente' and tipo_apoyo.id_tipo_apoyo='$id_actividad' and evento.id_evento='$id_evento'
                AND  (evento.fecha_evento >= ' + $f_inicio + ' and evento.fecha_evento <=  ' + $f_final + '))
				WHERE hombre_mujer.h_m ='MUJER'";
				$mysqli->query($sqlm);	
		
				$pastel = "SELECT h_m, cantidad_h_m FROM hombre_mujer";
                $pastel1 = $mysqli->query($pastel);
                $pastel_rows = array();
                $pastel_table3evento = array();

                $pastel_table3evento['cols']= array(
                    array('label'=>'Género', 'type'=>'string'),
                    array('label'=>'Cantidad', 'type'=>'number')

                );
                    foreach($pastel1 as $p){
                        $pastel_temp = array();
                        $pastel_temp[] = array('v'=>(string) $p['h_m']);
                        $pastel_temp[] = array('v'=>(integer) $p['cantidad_h_m']);
                        $pastel_rows[] = array ('c'=>$pastel_temp);
                    }
                    $pastel_table3evento['rows']=$pastel_rows;
                    $pasteljsonTable3evento =json_encode($pastel_table3evento);

                
///////FIN REGION, MUNICIPIO, GENERO --- EVENTO        

//// FIN DE SI NO  SELECCIONA PROGRAMA, COMPONENTE, ACTIVIDAD, EVENTO Y LAS FECHAS
//*SEUGNDA CONSULTA 4 VARIABLES
}// SI SELECCIONA PROGRAMA, COMPONENTE ACTIVIDAD, EVENTO
elseif((!empty($_GET['programa'])) && (!empty($_GET['componente'])) && (!empty($_GET['tipo_apoyo'])) && (!empty($_GET['evento_id'])) ){
    $id_usuario_programa =($_GET['programa']);
    $id_componente =($_GET['componente']);
    $id_actividad =($_GET['tipo_apoyo']);
    $id_evento =($_GET['evento_id']);
  
    $sqlProgramas = "SELECT 
    programa.id_programa,    
    nombre_programa,
    componente.id_componente,
    componente.nombre_componente,
    tipo_apoyo.id_tipo_apoyo,
    tipo_apoyo.nombre_tipo_apoyo,
    evento.id_evento, 
    evento.nombre_evento
FROM
    programa
    INNER JOIN componente on componente.com_id_programa = programa.id_programa
    INNER JOIN tipo_apoyo on tipo_apoyo.ta_id_componente = componente.id_componente
    INNER JOIN evento on evento.id_accion_evento = tipo_apoyo.id_tipo_apoyo
    WHERE programa.id_programa  = $id_usuario_programa 
    AND componente.id_componente = $id_componente
    AND tipo_apoyo.id_tipo_apoyo = $id_actividad
    AND evento.id_evento = $id_evento ";

    $programas = $mysqli->query($sqlProgramas);
    $row_programa  = $programas->fetch_assoc();
    $_programa=$row_programa ['nombre_programa'];
    $_nombre_componente=$row_programa ['nombre_componente'];
    $_nombre_tipo_apoyo=$row_programa ['nombre_tipo_apoyo'];
    $_nombre_evento=$row_programa ['nombre_evento'];
              
        //-----GRÁFICO PRESUPUESTO EJERCIDO POR ACTIVIDAD
        //////////
            $pastel = "SELECT tipo_apoyo.id_tipo_apoyo, tipo_apoyo.nombre_tipo_apoyo, tipo_apoyo.ta_id_componente, tipo_apoyo.meta_um_servicio, tipo_apoyo.meta_um_beneficio, tipo_apoyo.meta_cantidad_servicio, tipo_apoyo.meta_cantidad_beneficio, tipo_apoyo.meta_alcanzada_cantidad_servicio, tipo_apoyo.meta_alcanzada_cantidad_beneficio, tipo_apoyo.tipo_apoyo_presupuesto_ejercido, componente.nombre_componente, programa.nombre_programa, programa.id_programa, evento.fecha_evento,
            reg_partidas.monto_partidas, sum(reg_partidas.monto_partidas) as programa_presupuesto_ejercido_fechas  
            FROM  programa
            INNER JOIN componente on componente.com_id_programa = programa.id_programa
            INNER JOIN tipo_apoyo on tipo_apoyo.ta_id_componente = componente.id_componente
            INNER JOIN evento on evento.id_accion_evento = tipo_apoyo.id_tipo_apoyo
            INNER JOIN reg_partidas on reg_partidas.id_evento_partidas = evento.id_evento  
            where programa.id_programa=$id_usuario_programa  and componente.id_componente='$id_componente' and tipo_apoyo.id_tipo_apoyo='$id_actividad' and evento.id_evento='$id_evento' ORDER BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo";

        $pastel1 = $mysqli->query($pastel);
        $pastel_rows = array();
        $pastel_table6 = array();

        $pastel_table6['cols']= array(
            array('label'=>'Actividad', 'type'=>'string'),
            array('label'=>'Monto', 'type'=>'number')

        );
            foreach($pastel1 as $p){
                $pastel_temp = array();
                $pastel_temp[] = array('v'=>(string) $p['nombre_tipo_apoyo']);
                $pastel_temp[] = array('v'=>(integer) $p['programa_presupuesto_ejercido_fechas']);
                $pastel_rows[] = array ('c'=>$pastel_temp);
            }
            $pastel_table6['rows']=$pastel_rows;
            $pasteljsonTable6 =json_encode($pastel_table6);  //GRAFICO 11

        //////////Metas programadas vs Metas alcanzadas por Actividad
            $tabla = "SELECT tipo_apoyo.id_tipo_apoyo, tipo_apoyo.nombre_tipo_apoyo, tipo_apoyo.ta_id_componente, tipo_apoyo.meta_um_servicio, tipo_apoyo.meta_um_beneficio, tipo_apoyo.meta_cantidad_servicio, tipo_apoyo.meta_cantidad_beneficio, tipo_apoyo.meta_alcanzada_cantidad_servicio, tipo_apoyo.meta_alcanzada_cantidad_beneficio, tipo_apoyo.tipo_apoyo_presupuesto_ejercido, componente.nombre_componente, programa.nombre_programa, programa.id_programa,COUNT( meta_alcanzada_cantidad_servicio ) AS tipo_apoyoA,
            concat(round(( meta_alcanzada_cantidad_servicio/meta_cantidad_servicio * 100 ),2),'%') AS porcentaje_servicio,
            COUNT( meta_alcanzada_cantidad_beneficio ) AS tipo_apoyoA,
            concat(round(( meta_alcanzada_cantidad_beneficio/meta_cantidad_beneficio * 100 ),2),'%') AS porcentaje_beneficio,  servicios.nombre_servicios,  beneficios.nombre_beneficios,
            evento.fecha_evento,
            reg_partidas.monto_partidas,
            sum(reg_partidas.monto_partidas) as programa_presupuesto_ejercido_fechas  
            FROM programa
            INNER JOIN componente on componente.com_id_programa = programa.id_programa
            INNER JOIN tipo_apoyo on tipo_apoyo.ta_id_componente = componente.id_componente
            INNER JOIN evento on evento.id_accion_evento = tipo_apoyo.id_tipo_apoyo
            INNER JOIN reg_partidas on reg_partidas.id_evento_partidas = evento.id_evento  
            INNER JOIN beneficios ON beneficios.id_beneficios = tipo_apoyo.meta_um_beneficio 
            INNER JOIN servicios ON servicios.id_servicios = tipo_apoyo.meta_um_servicio
            where programa.id_programa=$id_usuario_programa  and componente.id_componente='$id_componente' and tipo_apoyo.id_tipo_apoyo='$id_actividad' and evento.id_evento='$id_evento' group by id_tipo_apoyo ORDER BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo";
            $tabla1_exportar = $mysqli->query($tabla);
            $tabla1 = $mysqli->query($tabla);
            $tabla_rows = array();
            $tabla_table = array();

            $tabla_table['cols']= array(
                array('label'=>'Componente', 'type'=>'string'),
                array('label'=>'Actividad', 'type'=>'string'),
                array('label'=>'Monto ejercido', 'type'=>'number'),
                array('label'=>'U.M. Servicios', 'type'=>'string'),
                array('label'=>'Meta', 'type'=>'number'),
                array('label'=>'Avance', 'type'=>'number'),
                array('label'=>'%', 'type'=>'string'),
                array('label'=>'U.M. Beneficio', 'type'=>'string'),
                array('label'=>'Meta', 'type'=>'number'),
                array('label'=>'Avance', 'type'=>'number'),
                array('label'=>'%', 'type'=>'string'),

            );
                foreach($tabla1 as $b){
                    $tabla_temp = array();
                    $tabla_temp[] = array('v'=>(string) $b['nombre_componente']);
                    $tabla_temp[] = array('v'=>(string) $b['nombre_tipo_apoyo']);
                    $tabla_temp[] = array('v'=>(integer) $b['programa_presupuesto_ejercido_fechas']);
                    $tabla_temp[] = array('v'=>(string) $b['nombre_servicios']);
                    $tabla_temp[] = array('v'=>(integer) $b['meta_cantidad_servicio']);
                    $tabla_temp[] = array('v'=>(integer) $b['meta_alcanzada_cantidad_servicio']);
                    $tabla_temp[] = array('v'=>(string) $b['porcentaje_servicio']);
                    $tabla_temp[] = array('v'=>(string) $b['nombre_beneficios']);
                    $tabla_temp[] = array('v'=>(integer) $b['meta_cantidad_beneficio']);
                    $tabla_temp[] = array('v'=>(integer) $b['meta_alcanzada_cantidad_beneficio']);
                    $tabla_temp[] = array('v'=>(string) $b['porcentaje_beneficio']);
                    $tabla_rows[] = array ('c'=>$tabla_temp);
                }
                $tabla_table['rows']=$tabla_rows;
                $tablajsonTable =json_encode($tabla_table);   //GRAFICO 12  

                ////////////////Desglose de eventos realizados

                $tabla_evento = "SELECT 
                evento.id_evento,
                evento.nombre_evento,
                evento.pob_obj_evento,
                evento.nivel_geo_evento,
                evento.lugar_evento,
                evento.monto_evento,
                evento.fecha_evento,
                evento.fecha_modificacion_evento,
                evento.usuario_evento,
                evento.id_accion_evento,
                evento.cantidad_servicio,
                evento.cantidad_beneficio,
                evento.hombres, 
                evento.mujeres,
                region.region,
                municipio.municipio,
                localidad.localidad, 
                tipo_apoyo.nombre_tipo_apoyo,
                servicios.nombre_servicios,
                beneficios.nombre_beneficios,
                componente.nombre_componente,
                COUNT(evento.hombres) AS tipo_apoyoA,       
                concat(round((evento.hombres/cantidad_beneficio * 100 ),2),'%') AS porcentaje_hombres,
                COUNT(evento.mujeres) AS tipo_apoyoA,       
                concat(round((evento.mujeres/cantidad_beneficio * 100 ),2),'%') AS porcentaje_mujeres  
                FROM
                evento
                INNER JOIN
                localidad ON localidad.cve_localidad = evento.lugar_evento
                    INNER JOIN
                municipio ON municipio.cve_municipios = localidad.l_cve_municipio
                    INNER JOIN
                region ON region.cve_regiones = municipio.m_cve_region
                    INNER JOIN
                usuarios ON usuarios.id = evento.usuario_evento
                    INNER JOIN
                tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
                    INNER JOIN
                componente ON componente.id_componente = tipo_apoyo.ta_id_componente
                    INNER JOIN
                programa ON componente.com_id_programa = programa.id_programa  
                INNER JOIN
                beneficios ON beneficios.id_beneficios = tipo_apoyo.meta_um_beneficio
                        INNER JOIN
                servicios ON servicios.id_servicios = tipo_apoyo.meta_um_servicio
                where programa.id_programa=$id_usuario_programa  and componente.id_componente='$id_componente' and tipo_apoyo.id_tipo_apoyo='$id_actividad' and evento.id_evento='$id_evento'
                group by evento.id_evento
                ORDER BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento";

                $tabla1_evento_exportar = $mysqli->query($tabla_evento);
                $tabla1_evento = $mysqli->query($tabla_evento);
                $tabla_evento_rows = array();
                $tabla_evento_table = array();

                $tabla_evento_table['cols']= array(
                    array('label'=>'Componente', 'type'=>'string'),
                    array('label'=>'Actividad', 'type'=>'string'),
                    array('label'=>'Evento', 'type'=>'string'),
                    array('label'=>'Monto ejercido', 'type'=>'number'),
                    array('label'=>'Fecha', 'type'=>'string'),
                    array('label'=>'U.M. Servicios', 'type'=>'string'),
                    array('label'=>'Meta alcanzada', 'type'=>'number'),
                    array('label'=>'U.M. Beneficio', 'type'=>'string'),
                    array('label'=>'Meta alcanzada', 'type'=>'number'),
                    array('label'=>'Hombres', 'type'=>'number'),
                    array('label'=>'%', 'type'=>'string'),
                    array('label'=>'Mujeres', 'type'=>'number'),
                    array('label'=>'%', 'type'=>'string'),             

                );
                    foreach($tabla1_evento as $b){
                        $tabla_evento_temp = array();
                        $tabla_evento_temp[] = array('v'=>(string) $b['nombre_componente']);
                        $tabla_evento_temp[] = array('v'=>(string) $b['nombre_tipo_apoyo']);
                        $tabla_evento_temp[] = array('v'=>(string) $b['nombre_evento']);
                        $tabla_evento_temp[] = array('v'=>(integer) $b['monto_evento']);
                        $tabla_evento_temp[] = array('v'=>(string) $b['fecha_evento']);
                        $tabla_evento_temp[] = array('v'=>(string) $b['nombre_servicios']);                    
                        $tabla_evento_temp[] = array('v'=>(integer) $b['cantidad_servicio']);
                        $tabla_evento_temp[] = array('v'=>(string) $b['nombre_beneficios']);
                        $tabla_evento_temp[] = array('v'=>(integer) $b['cantidad_beneficio']);
                        $tabla_evento_temp[] = array('v'=>(integer) $b['hombres']);
                        $tabla_evento_temp[] = array('v'=>(string) $b['porcentaje_hombres']);
                        $tabla_evento_temp[] = array('v'=>(integer) $b['mujeres']);
                        $tabla_evento_temp[] = array('v'=>(string) $b['porcentaje_mujeres']);
                        $tabla_evento_rows[] = array ('c'=>$tabla_evento_temp);
                    }
                    $tabla_evento_table['rows']=$tabla_evento_rows;
                    $tabla_evento_jsonTable =json_encode($tabla_evento_table);  //GRAFICO 15


            //-----GRÁFICO POR OBJETO DEL GASTO
                    //////////
                        $pastel = "SELECT reg_partidas.id_reg_partidas, reg_partidas.partida, reg_partidas.cantidad_partidas, reg_partidas.monto_partidas, reg_partidas.id_evento_partidas,
                        sum(reg_partidas.monto_partidas) as totalsuma,
                        objeto_de_gasto.partida_gasto_texto,
                        evento.fecha_evento,
                        reg_partidas.monto_partidas,
                        sum(reg_partidas.monto_partidas) as programa_presupuesto_ejercido_fechas
                        FROM reg_partidas
                        INNER JOIN evento on evento.id_evento = reg_partidas.id_evento_partidas
                        INNER JOIN tipo_apoyo on tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
                        INNER JOIN componente on componente.id_componente = tipo_apoyo.ta_id_componente
                        INNER JOIN programa on programa.id_programa = componente.com_id_programa
                        INNER JOIN objeto_de_gasto on objeto_de_gasto.clave = reg_partidas.partida
                        where programa.id_programa=$id_usuario_programa  and componente.id_componente='$id_componente' and tipo_apoyo.id_tipo_apoyo='$id_actividad' and evento.id_evento='$id_evento'
                        group by reg_partidas.partida";

                        $pastel1 = $mysqli->query($pastel);
                        $pastel_rows = array();
                        $pastel_table9 = array();

                        $pastel_table9['cols']= array(
                            array('label'=>'Objeto de gasto', 'type'=>'string'),
                            array('label'=>'Presupuesto ejercido', 'type'=>'number')

                        );
                            foreach($pastel1 as $p){
                                $pastel_temp = array();
                                $pastel_temp[] = array('v'=>(string) $p['partida_gasto_texto']);
                                $pastel_temp[] = array('v'=>(integer) $p['totalsuma']);
                                $pastel_rows[] = array ('c'=>$pastel_temp);
                            }
                            $pastel_table9['rows']=$pastel_rows;
                            $pasteljsonTable9 =json_encode($pastel_table9);  //GRAFICO 18

                        //////////
        ////////////////Tabla Presupuesto ejercido a nivel de objeto de gasto (partidas y monto ejercido)

            $tabla_partida = "SELECT 
            id_reg_partidas,
            partida,
            cantidad_partidas,
            monto_partidas,
            id_evento_partidas,
            partida_gasto_texto,
            evento.nombre_evento,
            programa.nombre_programa,
            componente.nombre_componente,
            tipo_apoyo.nombre_tipo_apoyo,
            capitulo,
            evento.fecha_evento,
            reg_partidas.monto_partidas,
            sum(reg_partidas.monto_partidas) as programa_presupuesto_ejercido_fechas
        FROM
            reg_partidas
                INNER JOIN
            objeto_de_gasto ON clave = partida
            INNER JOIN 
            evento ON evento.id_evento = reg_partidas.id_evento_partidas
            INNER JOIN 
            tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
            INNER JOIN 
            componente ON componente.id_componente = tipo_apoyo.ta_id_componente
            INNER JOIN
            programa ON programa.id_programa = componente.com_id_programa
            WHERE
            programa.id_programa=$id_usuario_programa  and componente.id_componente='$id_componente' and tipo_apoyo.id_tipo_apoyo='$id_actividad' and evento.id_evento='$id_evento'
            group by reg_partidas.id_reg_partidas
            ORDER BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento, reg_partidas.id_reg_partidas";
            $tabla1_partida_exportar = $mysqli->query($tabla_partida);
            $tabla1_partida = $mysqli->query($tabla_partida);
            $tabla_partida_rows = array();
            $tabla_partida_table = array();

            $tabla_partida_table['cols']= array(
                array('label'=>'Programa', 'type'=>'string'),
                array('label'=>'Componente', 'type'=>'string'),
                array('label'=>'Actividad', 'type'=>'string'),
                array('label'=>'Evento', 'type'=>'string'),
                array('label'=>'Capítulo', 'type'=>'number'),
                array('label'=>'Partida', 'type'=>'string'),
                array('label'=>'Monto', 'type'=>'number'),

            );
                foreach($tabla1_partida as $b){
                    $tabla_partida_temp = array();
                    $tabla_partida_temp[] = array('v'=>(string) $b['nombre_programa']);
                    $tabla_partida_temp[] = array('v'=>(string) $b['nombre_componente']);
                    $tabla_partida_temp[] = array('v'=>(string) $b['nombre_tipo_apoyo']);
                    $tabla_partida_temp[] = array('v'=>(string) $b['nombre_evento']);
                    $tabla_partida_temp[] = array('v'=>(integer) $b['capitulo']);
                    $tabla_partida_temp[] = array('v'=>(string) $b['partida_gasto_texto']);
                    $tabla_partida_temp[] = array('v'=>(integer) $b['monto_partidas']);
                    $tabla_partida_rows[] = array ('c'=>$tabla_partida_temp);
                }
                $tabla_partida_table['rows']=$tabla_partida_rows;
                $tabla_partida_jsonTable =json_encode($tabla_partida_table);  //GRAFICO 19

                //-----GRÁFICO POR OBJETO DEL GASTO CAPÍTULO
                    //////////
                    $pastel = "SELECT capitulo,  evento.fecha_evento, SUM(monto_partidas) AS capitulo_total
                    FROM reg_partidas
                    INNER JOIN evento on evento.id_evento = reg_partidas.id_evento_partidas
                    INNER JOIN tipo_apoyo on tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
                    INNER JOIN componente on componente.id_componente = tipo_apoyo.ta_id_componente
                    INNER JOIN programa on programa.id_programa = componente.com_id_programa
                    INNER JOIN objeto_de_gasto on objeto_de_gasto.clave = reg_partidas.partida
                    where programa.id_programa=$id_usuario_programa  and componente.id_componente='$id_componente' and tipo_apoyo.id_tipo_apoyo='$id_actividad' and evento.id_evento='$id_evento' 
                    group by capitulo";

                    $pastel1 = $mysqli->query($pastel);
                    $pastel_rows = array();
                    $pastel_table10 = array();

                    $pastel_table10['cols']= array(
                        array('label'=>'Capítulo', 'type'=>'string'),
                        array('label'=>'Presupuesto ejercido', 'type'=>'number')

                    );
                        foreach($pastel1 as $p){
                            $pastel_temp = array();
                            $pastel_temp[] = array('v'=>(integer) $p['capitulo']);
                            $pastel_temp[] = array('v'=>(integer) $p['capitulo_total']);
                            $pastel_rows[] = array ('c'=>$pastel_temp);
                        }
                        $pastel_table10['rows']=$pastel_rows;
                        $pasteljsonTable10 =json_encode($pastel_table10);  //GRAFICO 16

                    //////////

                    ////////////////Tabla Presupuesto ejercido a nivel de objeto de gasto (capitulo y monto ejercido)

                    $tabla_capitulo = "SELECT 
                    programa.nombre_programa,
                    componente.nombre_componente,
                    tipo_apoyo.nombre_tipo_apoyo,
                    evento.nombre_evento,
                    capitulo,
                    evento.fecha_evento,
                    SUM(monto_partidas) AS capitulo_total
                FROM
                    reg_partidas
                        INNER JOIN
                    objeto_de_gasto ON clave = partida
                    INNER JOIN 
                    evento ON evento.id_evento = reg_partidas.id_evento_partidas
                    INNER JOIN 
                    tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
                    INNER JOIN 
                    componente ON componente.id_componente = tipo_apoyo.ta_id_componente
                    INNER JOIN
                    programa ON programa.id_programa = componente.com_id_programa
                    WHERE
                    programa.id_programa=$id_usuario_programa  and componente.id_componente='$id_componente' and tipo_apoyo.id_tipo_apoyo='$id_actividad' and evento.id_evento='$id_evento'
                    GROUP BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento, capitulo
                    ORDER BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento, capitulo";
                    $tabla1_capitulo_exportar = $mysqli->query($tabla_capitulo);
                    $tabla1_capitulo = $mysqli->query($tabla_capitulo);
                    $tabla_capitulo_rows = array();
                    $tabla_capitulo_table = array();

                    $tabla_capitulo_table['cols']= array(
                        array('label'=>'Programa', 'type'=>'string'),
                        array('label'=>'Componente', 'type'=>'string'),
                        array('label'=>'Actividad', 'type'=>'string'),
                        array('label'=>'Evento', 'type'=>'string'),
                        array('label'=>'Capítulo', 'type'=>'number'),
                        array('label'=>'Monto', 'type'=>'number'),

                    );
                        foreach($tabla1_capitulo as $b){
                            $tabla_capitulo_temp = array();
                            $tabla_capitulo_temp[] = array('v'=>(string) $b['nombre_programa']);
                            $tabla_capitulo_temp[] = array('v'=>(string) $b['nombre_componente']);
                            $tabla_capitulo_temp[] = array('v'=>(string) $b['nombre_tipo_apoyo']);
                            $tabla_capitulo_temp[] = array('v'=>(string) $b['nombre_evento']);
                            $tabla_capitulo_temp[] = array('v'=>(integer) $b['capitulo']);
                            $tabla_capitulo_temp[] = array('v'=>(integer) $b['capitulo_total']);
                            $tabla_capitulo_rows[] = array ('c'=>$tabla_capitulo_temp);
                        }
                        $tabla_capitulo_table['rows']=$tabla_capitulo_rows;
                        $tabla_capitulo_jsonTable =json_encode($tabla_capitulo_table);  //GRAFICO 17 

 
////RESUMEN POR TIPO DE ACCIONES (TRANSVERSALIDAD, ACCIONES, PREVENCION)

$tabla_acciones = "SELECT programa.nombre_programa, tipo_apoyo.tipo_accion, sum(evento.cantidad_beneficio) as personas_atendidas, sum(evento.hombres) as hombres, sum(evento.mujeres) as mujeres,
sum(evento.monto_evento)as inversion_ejercido,
count(tipo_apoyo.tipo_accion) as acciones
FROM
    tipo_apoyo 
INNER JOIN
    componente ON componente.id_componente = tipo_apoyo.ta_id_componente
INNER JOIN
    programa ON componente.com_id_programa = programa.id_programa
INNER JOIN  
    evento ON evento.id_accion_evento = tipo_apoyo.id_tipo_apoyo   
WHERE programa.id_programa = $id_usuario_programa  and componente.id_componente='$id_componente' and tipo_apoyo.id_tipo_apoyo='$id_actividad' and evento.id_evento='$id_evento' 
group by  programa.id_programa, tipo_accion
order by  tipo_accion";
$tabla_acciones1_exportar = $mysqli->query($tabla_acciones);
$tabla_acciones1 = $mysqli->query($tabla_acciones);
$tabla_acciones_rows = array();
$tabla_acciones_table = array();

$tabla_acciones_table['cols']= array(
    array('label'=>'Programa', 'type'=>'string'),
    array('label'=>'Tipo de Acción', 'type'=>'string'),
    array('label'=>'Personas Atendidas', 'type'=>'number'),
	array('label'=>'Hombres', 'type'=>'number'),
	array('label'=>'Mujeres', 'type'=>'number'),
    array('label'=>'Monto ejercido', 'type'=>'number'),  
    array('label'=>'Número de acciones', 'type'=>'number'),

);
    foreach($tabla_acciones1 as $b){
        $tabla_temp = array();
        $tabla_temp[] = array('v'=>(string) $b['nombre_programa']);
        $tabla_temp[] = array('v'=>(string) $b['tipo_accion']);
        $tabla_temp[] = array('v'=>(integer) $b['personas_atendidas']);
		$tabla_temp[] = array('v'=>(integer) $b['hombres']);
		$tabla_temp[] = array('v'=>(integer) $b['mujeres']);
        $tabla_temp[] = array('v'=>(integer) $b['inversion_ejercido']);
        $tabla_temp[] = array('v'=>(integer) $b['acciones']);
        $tabla_acciones_rows[] = array ('c'=>$tabla_temp);
    }
    $tabla_acciones_table['rows']=$tabla_acciones_rows;
    $tabla_accionesjsonTable =json_encode($tabla_acciones_table);  //GRAFICO 13   
////                  
////DESGLOSE POR TIPO DE ACCIONES (TRANSVERSALIDAD, ACCIONES, PREVENCION)

$tabla_acciones = "SELECT programa.nombre_programa, componente.nombre_componente, tipo_apoyo.tipo_accion, evento.nombre_evento, sum(evento.cantidad_beneficio) as personas_atendidas, sum(evento.hombres) as hombres, sum(evento.mujeres) as mujeres,
sum(evento.monto_evento)as inversion_ejercido
FROM
	tipo_apoyo 
INNER JOIN
	componente ON componente.id_componente = tipo_apoyo.ta_id_componente
INNER JOIN
	programa ON componente.com_id_programa = programa.id_programa
INNER JOIN  
	evento ON evento.id_accion_evento = tipo_apoyo.id_tipo_apoyo   
WHERE programa.id_programa = $id_usuario_programa  and componente.id_componente='$id_componente' and tipo_apoyo.id_tipo_apoyo='$id_actividad' and evento.id_evento='$id_evento'
group by programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento, evento.fecha_evento, tipo_accion
order by  tipo_accion";
$tabla_acciones1_desglose_exportar = $mysqli->query($tabla_acciones);
$tabla_acciones1 = $mysqli->query($tabla_acciones);
$tabla_acciones_rows = array();
$tabla_acciones_table = array();

$tabla_acciones_table['cols']= array(
    array('label'=>'Programa', 'type'=>'string'),
    array('label'=>'Componente', 'type'=>'string'),
    array('label'=>'Tipo de Acción', 'type'=>'string'),
    array('label'=>'Evento', 'type'=>'string'),
    array('label'=>'Personas Atendidas', 'type'=>'number'),
	array('label'=>'Hombres', 'type'=>'number'),
	array('label'=>'Mujeres', 'type'=>'number'),
    array('label'=>'Monto ejercido', 'type'=>'number'),  

);
    foreach($tabla_acciones1 as $b){
        $tabla_temp = array();    
        $tabla_temp[] = array('v'=>(string) $b['nombre_programa']);
        $tabla_temp[] = array('v'=>(string) $b['nombre_componente']);
        $tabla_temp[] = array('v'=>(string) $b['tipo_accion']);
        $tabla_temp[] = array('v'=>(string) $b['nombre_evento']);
        $tabla_temp[] = array('v'=>(integer) $b['personas_atendidas']);
		$tabla_temp[] = array('v'=>(integer) $b['hombres']);
		$tabla_temp[] = array('v'=>(integer) $b['mujeres']);
        $tabla_temp[] = array('v'=>(integer) $b['inversion_ejercido']);

        $tabla_acciones_rows[] = array ('c'=>$tabla_temp);
    }
    $tabla_acciones_table['rows']=$tabla_acciones_rows;
    $tabla_acciones_desglosejsonTable =json_encode($tabla_acciones_table);   //GRAFICO 14  
////
///////INICIO REGION, MUNICIPIO, GENERO ---EVENTO
//-----GRÁFICO POR REGION GRAFICO PAY

    $pastel = "SELECT  region.region, COUNT(region.region) AS Total_Cuenta 
    FROM
    evento
    INNER JOIN localidad ON localidad.cve_localidad = evento.lugar_evento
    INNER JOIN municipio ON municipio.cve_municipios = localidad.l_cve_municipio
    INNER JOIN region ON region.cve_regiones = municipio.m_cve_region
    INNER JOIN tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
    INNER JOIN componente ON componente.id_componente = tipo_apoyo.ta_id_componente
    INNER JOIN programa ON programa.id_programa = componente.com_id_programa
    WHERE programa.id_programa = $id_usuario_programa   and componente.id_componente='$id_componente' and tipo_apoyo.id_tipo_apoyo='$id_actividad' and evento.id_evento='$id_evento'
    GROUP BY region.region ORDER BY  region.region  ASC";


    $pastel1 = $mysqli->query($pastel);
    $pastel_rows = array();
    $pastel_table1evento = array();

    $pastel_table1evento['cols']= array(
        array('label'=>'Región', 'type'=>'string'),
        array('label'=>'Cantidad', 'type'=>'number')

    );
        foreach($pastel1 as $p){
            $pastel_temp = array();
            $pastel_temp[] = array('v'=>(string) $p['region']);
            $pastel_temp[] = array('v'=>(integer) $p['Total_Cuenta']);
            $pastel_rows[] = array ('c'=>$pastel_temp);
        }
        $pastel_table1evento['rows']=$pastel_rows;
        $pasteljsonTable1evento =json_encode($pastel_table1evento);

//////////

//-----GRÁFICO POR MUNICIPIO -GRÁFICO PAY

        ///////////

            $pastel = "SELECT  municipio.municipio, COUNT(municipio.municipio) AS Total_Cuenta 
            FROM
            evento 
	        INNER JOIN localidad ON localidad.cve_localidad = evento.lugar_evento
            INNER JOIN municipio ON municipio.cve_municipios = localidad.l_cve_municipio
            INNER JOIN region ON region.cve_regiones = municipio.m_cve_region
            INNER JOIN tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
            INNER JOIN componente ON componente.id_componente = tipo_apoyo.ta_id_componente
            INNER JOIN programa ON programa.id_programa = componente.com_id_programa
            WHERE programa.id_programa = $id_usuario_programa   and componente.id_componente='$id_componente' and tipo_apoyo.id_tipo_apoyo='$id_actividad' and evento.id_evento='$id_evento'
            GROUP BY municipio.municipio ORDER BY  municipio.municipio  ASC";

            $tabla_localidades = "SELECT programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento, evento.fecha_evento, region.region, municipio.municipio, localidad.localidad, localidad.latitud_dec, localidad.longitud_dec, COUNT(localidad.localidad) AS Total_Cuenta 
            FROM
            evento 
	        INNER JOIN localidad ON localidad.cve_localidad = evento.lugar_evento
            INNER JOIN municipio ON municipio.cve_municipios = localidad.l_cve_municipio
            INNER JOIN region ON region.cve_regiones = municipio.m_cve_region
            INNER JOIN tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
            INNER JOIN componente ON componente.id_componente = tipo_apoyo.ta_id_componente
            INNER JOIN programa ON programa.id_programa = componente.com_id_programa
            WHERE programa.id_programa = $id_usuario_programa   and componente.id_componente='$id_componente' and tipo_apoyo.id_tipo_apoyo='$id_actividad' and evento.id_evento='$id_evento'
            GROUP BY localidad.localidad ORDER BY  localidad.localidad  ASC";

        $tablalocexportar = $mysqli->query($tabla_localidades);
        $pastel1 = $mysqli->query($pastel);
        $pastel_rows = array();
        $pastel_table2evento = array();

        $pastel_table2evento['cols']= array(
            array('label'=>'Municipio %', 'type'=>'string'),
            array('label'=>'Cantidad', 'type'=>'number')

        );
            foreach($pastel1 as $p){
                $pastel_temp = array();
                $pastel_temp[] = array('v'=>(string) $p['municipio']);
                $pastel_temp[] = array('v'=>(integer) $p['Total_Cuenta']);
                $pastel_rows[] = array ('c'=>$pastel_temp);
            }
            $pastel_table2evento['rows']=$pastel_rows;
            $pasteljsonTable2evento =json_encode($pastel_table2evento);

        //////////
//-----GRÁFICO POR GÉNERO
				$sqlh="UPDATE hombre_mujer SET hombre_mujer.cantidad_h_m =( SELECT SUM( evento.hombres) AS Total_CuentaH 
                FROM
                evento 
	            INNER JOIN localidad ON localidad.cve_localidad = evento.lugar_evento
                INNER JOIN municipio ON municipio.cve_municipios = localidad.l_cve_municipio
                INNER JOIN region ON region.cve_regiones = municipio.m_cve_region
                INNER JOIN tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
                INNER JOIN componente ON componente.id_componente = tipo_apoyo.ta_id_componente
                INNER JOIN programa ON programa.id_programa = componente.com_id_programa
                WHERE programa.id_programa = $id_usuario_programa  and componente.id_componente='$id_componente' and tipo_apoyo.id_tipo_apoyo='$id_actividad' and evento.id_evento='$id_evento')
				WHERE hombre_mujer.h_m ='HOMBRE'";
				$mysqli->query($sqlh);
				
				$sqlm="UPDATE hombre_mujer SET hombre_mujer.cantidad_h_m =( SELECT SUM( evento.mujeres) AS Total_CuentaM 
                FROM
                evento 
	            INNER JOIN localidad ON localidad.cve_localidad = evento.lugar_evento
                INNER JOIN municipio ON municipio.cve_municipios = localidad.l_cve_municipio
                INNER JOIN region ON region.cve_regiones = municipio.m_cve_region
                INNER JOIN tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
                INNER JOIN componente ON componente.id_componente = tipo_apoyo.ta_id_componente
                INNER JOIN programa ON programa.id_programa = componente.com_id_programa
                WHERE programa.id_programa = $id_usuario_programa  and componente.id_componente='$id_componente' and tipo_apoyo.id_tipo_apoyo='$id_actividad' and evento.id_evento='$id_evento')
				WHERE hombre_mujer.h_m ='MUJER'";
				$mysqli->query($sqlm);	
            //////////
                $pastel = "SELECT h_m, cantidad_h_m FROM hombre_mujer";
                $pastel1 = $mysqli->query($pastel);
                $pastel_rows = array();
                $pastel_table3evento = array();

                $pastel_table3evento['cols']= array(
                    array('label'=>'Género', 'type'=>'string'),
                    array('label'=>'Cantidad', 'type'=>'number')

                );
                    foreach($pastel1 as $p){
                        $pastel_temp = array();
                        $pastel_temp[] = array('v'=>(string) $p['h_m']);
                        $pastel_temp[] = array('v'=>(integer) $p['cantidad_h_m']);
                        $pastel_rows[] = array ('c'=>$pastel_temp);
                    }
                    $pastel_table3evento['rows']=$pastel_rows;
                    $pasteljsonTable3evento =json_encode($pastel_table3evento);

                
///////FIN REGION, MUNICIPIO, GENERO --- EVENTO

//// SI NO  SELECCIONA PROGRAMA, COMPONENTE, ACTIVIDAD, EVENTO 

}
//*TERCERA CONSULTA 3 VARIABLES Y FECHA
// SI SELECCIONA PROGRAMA, COMPONENTE, ACTIVIDAD Y FECHA
elseif((!empty($_GET['programa'])) && (!empty($_GET['componente'])) && (!empty($_GET['tipo_apoyo'])) && (!empty($_GET['fechainicio']) && !empty($_GET['fechafinal'])) ){
    $id_usuario_programa =($_GET['programa']);
    $id_componente =($_GET['componente']);
    $id_actividad =($_GET['tipo_apoyo']);

  
    $sqlProgramas = "SELECT 
    programa.id_programa,    
    nombre_programa,
    componente.id_componente,
    componente.nombre_componente,
    tipo_apoyo.id_tipo_apoyo,
    tipo_apoyo.nombre_tipo_apoyo
FROM
    programa
    INNER JOIN componente on componente.com_id_programa = programa.id_programa
    INNER JOIN tipo_apoyo on tipo_apoyo.ta_id_componente = componente.id_componente

    WHERE programa.id_programa  = $id_usuario_programa 
    AND componente.id_componente = $id_componente
    AND tipo_apoyo.id_tipo_apoyo = $id_actividad ";

    $programas = $mysqli->query($sqlProgramas);
    $row_programa  = $programas->fetch_assoc();
    $_programa=$row_programa ['nombre_programa'];
    $_nombre_componente=$row_programa ['nombre_componente'];
    $_nombre_tipo_apoyo=$row_programa ['nombre_tipo_apoyo'];
    
    $f_inicio =($_GET['fechainicio']);
    $f_final =($_GET['fechafinal']);
 
        //-----GRÁFICO PRESUPUESTO EJERCIDO POR ACTIVIDAD
        //////////
            $pastel = "SELECT tipo_apoyo.id_tipo_apoyo, tipo_apoyo.nombre_tipo_apoyo, tipo_apoyo.ta_id_componente, tipo_apoyo.meta_um_servicio, tipo_apoyo.meta_um_beneficio, tipo_apoyo.meta_cantidad_servicio, tipo_apoyo.meta_cantidad_beneficio, tipo_apoyo.meta_alcanzada_cantidad_servicio, tipo_apoyo.meta_alcanzada_cantidad_beneficio, tipo_apoyo.tipo_apoyo_presupuesto_ejercido, componente.nombre_componente, programa.nombre_programa, programa.id_programa, evento.fecha_evento,
            reg_partidas.monto_partidas, sum(reg_partidas.monto_partidas) as programa_presupuesto_ejercido_fechas  
            FROM  programa
            INNER JOIN componente on componente.com_id_programa = programa.id_programa
            INNER JOIN tipo_apoyo on tipo_apoyo.ta_id_componente = componente.id_componente
            INNER JOIN evento on evento.id_accion_evento = tipo_apoyo.id_tipo_apoyo
            INNER JOIN reg_partidas on reg_partidas.id_evento_partidas = evento.id_evento  
            where programa.id_programa=$id_usuario_programa  and componente.id_componente='$id_componente' and tipo_apoyo.id_tipo_apoyo='$id_actividad' and (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento<=  ' + $f_final + ') ORDER BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo";

        $pastel1 = $mysqli->query($pastel);
        $pastel_rows = array();
        $pastel_table6 = array();

        $pastel_table6['cols']= array(
            array('label'=>'Actividad', 'type'=>'string'),
            array('label'=>'Monto', 'type'=>'number')

        );
            foreach($pastel1 as $p){
                $pastel_temp = array();
                $pastel_temp[] = array('v'=>(string) $p['nombre_tipo_apoyo']);
                $pastel_temp[] = array('v'=>(integer) $p['programa_presupuesto_ejercido_fechas']);
                $pastel_rows[] = array ('c'=>$pastel_temp);
            }
            $pastel_table6['rows']=$pastel_rows;
            $pasteljsonTable6 =json_encode($pastel_table6);  //GRAFICO 11

        //////////Metas programadas vs Metas alcanzadas por Actividad
            $tabla = "SELECT tipo_apoyo.id_tipo_apoyo, tipo_apoyo.nombre_tipo_apoyo, tipo_apoyo.ta_id_componente, tipo_apoyo.meta_um_servicio, tipo_apoyo.meta_um_beneficio, tipo_apoyo.meta_cantidad_servicio, tipo_apoyo.meta_cantidad_beneficio, tipo_apoyo.meta_alcanzada_cantidad_servicio, tipo_apoyo.meta_alcanzada_cantidad_beneficio, tipo_apoyo.tipo_apoyo_presupuesto_ejercido, componente.nombre_componente, programa.nombre_programa, programa.id_programa,COUNT( meta_alcanzada_cantidad_servicio ) AS tipo_apoyoA,
            concat(round(( meta_alcanzada_cantidad_servicio/meta_cantidad_servicio * 100 ),2),'%') AS porcentaje_servicio,
            COUNT( meta_alcanzada_cantidad_beneficio ) AS tipo_apoyoA,
            concat(round(( meta_alcanzada_cantidad_beneficio/meta_cantidad_beneficio * 100 ),2),'%') AS porcentaje_beneficio,  servicios.nombre_servicios,  beneficios.nombre_beneficios,
            evento.fecha_evento,
            reg_partidas.monto_partidas,
            sum(reg_partidas.monto_partidas) as programa_presupuesto_ejercido_fechas  
            FROM programa
            INNER JOIN componente on componente.com_id_programa = programa.id_programa
            INNER JOIN tipo_apoyo on tipo_apoyo.ta_id_componente = componente.id_componente
            INNER JOIN evento on evento.id_accion_evento = tipo_apoyo.id_tipo_apoyo
            INNER JOIN reg_partidas on reg_partidas.id_evento_partidas = evento.id_evento  
            INNER JOIN beneficios ON beneficios.id_beneficios = tipo_apoyo.meta_um_beneficio 
            INNER JOIN servicios ON servicios.id_servicios = tipo_apoyo.meta_um_servicio
            where programa.id_programa=$id_usuario_programa  and componente.id_componente='$id_componente' and tipo_apoyo.id_tipo_apoyo='$id_actividad' and (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento<=  ' + $f_final + ') group by id_tipo_apoyo ORDER BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo";
            $tabla1_exportar = $mysqli->query($tabla);
            $tabla1 = $mysqli->query($tabla);
            $tabla_rows = array();
            $tabla_table = array();

            $tabla_table['cols']= array(
                array('label'=>'Componente', 'type'=>'string'),
                array('label'=>'Actividad', 'type'=>'string'),
                array('label'=>'Monto ejercido', 'type'=>'number'),
                array('label'=>'U.M. Servicios', 'type'=>'string'),
                array('label'=>'Meta', 'type'=>'number'),
                array('label'=>'Avance', 'type'=>'number'),
                array('label'=>'%', 'type'=>'string'),
                array('label'=>'U.M. Beneficio', 'type'=>'string'),
                array('label'=>'Meta', 'type'=>'number'),
                array('label'=>'Avance', 'type'=>'number'),
                array('label'=>'%', 'type'=>'string'),

            );
                foreach($tabla1 as $b){
                    $tabla_temp = array();
                    $tabla_temp[] = array('v'=>(string) $b['nombre_componente']);
                    $tabla_temp[] = array('v'=>(string) $b['nombre_tipo_apoyo']);
                    $tabla_temp[] = array('v'=>(integer) $b['programa_presupuesto_ejercido_fechas']);
                    $tabla_temp[] = array('v'=>(string) $b['nombre_servicios']);
                    $tabla_temp[] = array('v'=>(integer) $b['meta_cantidad_servicio']);
                    $tabla_temp[] = array('v'=>(integer) $b['meta_alcanzada_cantidad_servicio']);
                    $tabla_temp[] = array('v'=>(string) $b['porcentaje_servicio']);
                    $tabla_temp[] = array('v'=>(string) $b['nombre_beneficios']);
                    $tabla_temp[] = array('v'=>(integer) $b['meta_cantidad_beneficio']);
                    $tabla_temp[] = array('v'=>(integer) $b['meta_alcanzada_cantidad_beneficio']);
                    $tabla_temp[] = array('v'=>(string) $b['porcentaje_beneficio']);
                    $tabla_rows[] = array ('c'=>$tabla_temp);
                }
                $tabla_table['rows']=$tabla_rows;
                $tablajsonTable =json_encode($tabla_table);   //GRAFICO 12  

                ////////////////Desglose de eventos realizados

                $tabla_evento = "SELECT 
                evento.id_evento,
                evento.nombre_evento,
                evento.pob_obj_evento,
                evento.nivel_geo_evento,
                evento.lugar_evento,
                evento.monto_evento,
                evento.fecha_evento,
                evento.fecha_modificacion_evento,
                evento.usuario_evento,
                evento.id_accion_evento,
                evento.cantidad_servicio,
                evento.cantidad_beneficio,
                evento.hombres, 
                evento.mujeres,
                region.region,
                municipio.municipio,
                localidad.localidad, 
                tipo_apoyo.nombre_tipo_apoyo,
                servicios.nombre_servicios,
                beneficios.nombre_beneficios,
                componente.nombre_componente,
                COUNT(evento.hombres) AS tipo_apoyoA,       
                concat(round((evento.hombres/cantidad_beneficio * 100 ),2),'%') AS porcentaje_hombres,
                COUNT(evento.mujeres) AS tipo_apoyoA,       
                concat(round((evento.mujeres/cantidad_beneficio * 100 ),2),'%') AS porcentaje_mujeres  
                FROM
                evento
                INNER JOIN
                localidad ON localidad.cve_localidad = evento.lugar_evento
                    INNER JOIN
                municipio ON municipio.cve_municipios = localidad.l_cve_municipio
                    INNER JOIN
                region ON region.cve_regiones = municipio.m_cve_region
                    INNER JOIN
                usuarios ON usuarios.id = evento.usuario_evento
                    INNER JOIN
                tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
                    INNER JOIN
                componente ON componente.id_componente = tipo_apoyo.ta_id_componente
                    INNER JOIN
                programa ON componente.com_id_programa = programa.id_programa  
                INNER JOIN
                beneficios ON beneficios.id_beneficios = tipo_apoyo.meta_um_beneficio
                        INNER JOIN
                servicios ON servicios.id_servicios = tipo_apoyo.meta_um_servicio
                where programa.id_programa=$id_usuario_programa  and componente.id_componente='$id_componente' and tipo_apoyo.id_tipo_apoyo='$id_actividad' and (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento<=  ' + $f_final + ')
                group by evento.id_evento
                ORDER BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento";

                $tabla1_evento_exportar = $mysqli->query($tabla_evento);
                $tabla1_evento = $mysqli->query($tabla_evento);
                $tabla_evento_rows = array();
                $tabla_evento_table = array();

                $tabla_evento_table['cols']= array(
                    array('label'=>'Componente', 'type'=>'string'),
                    array('label'=>'Actividad', 'type'=>'string'),
                    array('label'=>'Evento', 'type'=>'string'),
                    array('label'=>'Monto ejercido', 'type'=>'number'),
                    array('label'=>'Fecha', 'type'=>'string'),
                    array('label'=>'U.M. Servicios', 'type'=>'string'),
                    array('label'=>'Meta alcanzada', 'type'=>'number'),
                    array('label'=>'U.M. Beneficio', 'type'=>'string'),
                    array('label'=>'Meta alcanzada', 'type'=>'number'),
                    array('label'=>'Hombres', 'type'=>'number'),
                    array('label'=>'%', 'type'=>'string'),
                    array('label'=>'Mujeres', 'type'=>'number'),
                    array('label'=>'%', 'type'=>'string'),             

                );
                    foreach($tabla1_evento as $b){
                        $tabla_evento_temp = array();
                        $tabla_evento_temp[] = array('v'=>(string) $b['nombre_componente']);
                        $tabla_evento_temp[] = array('v'=>(string) $b['nombre_tipo_apoyo']);
                        $tabla_evento_temp[] = array('v'=>(string) $b['nombre_evento']);
                        $tabla_evento_temp[] = array('v'=>(integer) $b['monto_evento']);
                        $tabla_evento_temp[] = array('v'=>(string) $b['fecha_evento']);
                        $tabla_evento_temp[] = array('v'=>(string) $b['nombre_servicios']);                    
                        $tabla_evento_temp[] = array('v'=>(integer) $b['cantidad_servicio']);
                        $tabla_evento_temp[] = array('v'=>(string) $b['nombre_beneficios']);
                        $tabla_evento_temp[] = array('v'=>(integer) $b['cantidad_beneficio']);
                        $tabla_evento_temp[] = array('v'=>(integer) $b['hombres']);
                        $tabla_evento_temp[] = array('v'=>(string) $b['porcentaje_hombres']);
                        $tabla_evento_temp[] = array('v'=>(integer) $b['mujeres']);
                        $tabla_evento_temp[] = array('v'=>(string) $b['porcentaje_mujeres']);
                        $tabla_evento_rows[] = array ('c'=>$tabla_evento_temp);
                    }
                    $tabla_evento_table['rows']=$tabla_evento_rows;
                    $tabla_evento_jsonTable =json_encode($tabla_evento_table);  //GRAFICO 15


            //-----GRÁFICO POR OBJETO DEL GASTO
                    //////////
                        $pastel = "SELECT reg_partidas.id_reg_partidas, reg_partidas.partida, reg_partidas.cantidad_partidas, reg_partidas.monto_partidas, reg_partidas.id_evento_partidas,
                        sum(reg_partidas.monto_partidas) as totalsuma,
                        objeto_de_gasto.partida_gasto_texto,
                        evento.fecha_evento,
                        reg_partidas.monto_partidas,
                        sum(reg_partidas.monto_partidas) as programa_presupuesto_ejercido_fechas
                        FROM reg_partidas
                        INNER JOIN evento on evento.id_evento = reg_partidas.id_evento_partidas
                        INNER JOIN tipo_apoyo on tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
                        INNER JOIN componente on componente.id_componente = tipo_apoyo.ta_id_componente
                        INNER JOIN programa on programa.id_programa = componente.com_id_programa
                        INNER JOIN objeto_de_gasto on objeto_de_gasto.clave = reg_partidas.partida
                        where programa.id_programa=$id_usuario_programa  and componente.id_componente='$id_componente' and tipo_apoyo.id_tipo_apoyo='$id_actividad' and (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento<=  ' + $f_final + ')
                        group by reg_partidas.partida";

                        $pastel1 = $mysqli->query($pastel);
                        $pastel_rows = array();
                        $pastel_table9 = array();

                        $pastel_table9['cols']= array(
                            array('label'=>'Objeto de gasto', 'type'=>'string'),
                            array('label'=>'Presupuesto ejercido', 'type'=>'number')

                        );
                            foreach($pastel1 as $p){
                                $pastel_temp = array();
                                $pastel_temp[] = array('v'=>(string) $p['partida_gasto_texto']);
                                $pastel_temp[] = array('v'=>(integer) $p['totalsuma']);
                                $pastel_rows[] = array ('c'=>$pastel_temp);
                            }
                            $pastel_table9['rows']=$pastel_rows;
                            $pasteljsonTable9 =json_encode($pastel_table9);  //GRAFICO 18

                        //////////
        ////////////////Tabla Presupuesto ejercido a nivel de objeto de gasto (partidas y monto ejercido)

            $tabla_partida = "SELECT 
            id_reg_partidas,
            partida,
            cantidad_partidas,
            monto_partidas,
            id_evento_partidas,
            partida_gasto_texto,
            evento.nombre_evento,
            programa.nombre_programa,
            componente.nombre_componente,
            tipo_apoyo.nombre_tipo_apoyo,
            capitulo,
            evento.fecha_evento,
            reg_partidas.monto_partidas,
            sum(reg_partidas.monto_partidas) as programa_presupuesto_ejercido_fechas
        FROM
            reg_partidas
                INNER JOIN
            objeto_de_gasto ON clave = partida
            INNER JOIN 
            evento ON evento.id_evento = reg_partidas.id_evento_partidas
            INNER JOIN 
            tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
            INNER JOIN 
            componente ON componente.id_componente = tipo_apoyo.ta_id_componente
            INNER JOIN
            programa ON programa.id_programa = componente.com_id_programa
            WHERE
            programa.id_programa=$id_usuario_programa  and componente.id_componente='$id_componente' and tipo_apoyo.id_tipo_apoyo='$id_actividad' and (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento<=  ' + $f_final + ')
            group by reg_partidas.id_reg_partidas
            ORDER BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento, reg_partidas.id_reg_partidas";
            $tabla1_partida_exportar = $mysqli->query($tabla_partida);
            $tabla1_partida = $mysqli->query($tabla_partida);
            $tabla_partida_rows = array();
            $tabla_partida_table = array();

            $tabla_partida_table['cols']= array(
                array('label'=>'Programa', 'type'=>'string'),
                array('label'=>'Componente', 'type'=>'string'),
                array('label'=>'Actividad', 'type'=>'string'),
                array('label'=>'Evento', 'type'=>'string'),
                array('label'=>'Capítulo', 'type'=>'number'),
                array('label'=>'Partida', 'type'=>'string'),
                array('label'=>'Monto', 'type'=>'number'),

            );
                foreach($tabla1_partida as $b){
                    $tabla_partida_temp = array();
                    $tabla_partida_temp[] = array('v'=>(string) $b['nombre_programa']);
                    $tabla_partida_temp[] = array('v'=>(string) $b['nombre_componente']);
                    $tabla_partida_temp[] = array('v'=>(string) $b['nombre_tipo_apoyo']);
                    $tabla_partida_temp[] = array('v'=>(string) $b['nombre_evento']);
                    $tabla_partida_temp[] = array('v'=>(integer) $b['capitulo']);
                    $tabla_partida_temp[] = array('v'=>(string) $b['partida_gasto_texto']);
                    $tabla_partida_temp[] = array('v'=>(integer) $b['monto_partidas']);
                    $tabla_partida_rows[] = array ('c'=>$tabla_partida_temp);
                }
                $tabla_partida_table['rows']=$tabla_partida_rows;
                $tabla_partida_jsonTable =json_encode($tabla_partida_table);  //GRAFICO 19

                //-----GRÁFICO POR OBJETO DEL GASTO CAPÍTULO
                    //////////
                    $pastel = "SELECT capitulo,  evento.fecha_evento, SUM(monto_partidas) AS capitulo_total
                    FROM reg_partidas
                    INNER JOIN evento on evento.id_evento = reg_partidas.id_evento_partidas
                    INNER JOIN tipo_apoyo on tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
                    INNER JOIN componente on componente.id_componente = tipo_apoyo.ta_id_componente
                    INNER JOIN programa on programa.id_programa = componente.com_id_programa
                    INNER JOIN objeto_de_gasto on objeto_de_gasto.clave = reg_partidas.partida
                    where programa.id_programa=$id_usuario_programa  and componente.id_componente='$id_componente' and tipo_apoyo.id_tipo_apoyo='$id_actividad' and (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento<=  ' + $f_final + ')
                    group by capitulo";

                    $pastel1 = $mysqli->query($pastel);
                    $pastel_rows = array();
                    $pastel_table10 = array();

                    $pastel_table10['cols']= array(
                        array('label'=>'Capítulo', 'type'=>'string'),
                        array('label'=>'Presupuesto ejercido', 'type'=>'number')

                    );
                        foreach($pastel1 as $p){
                            $pastel_temp = array();
                            $pastel_temp[] = array('v'=>(integer) $p['capitulo']);
                            $pastel_temp[] = array('v'=>(integer) $p['capitulo_total']);
                            $pastel_rows[] = array ('c'=>$pastel_temp);
                        }
                        $pastel_table10['rows']=$pastel_rows;
                        $pasteljsonTable10 =json_encode($pastel_table10);  //GRAFICO 16

                    //////////

                    ////////////////Tabla Presupuesto ejercido a nivel de objeto de gasto (capitulo y monto ejercido)

                    $tabla_capitulo = "SELECT 
                    programa.nombre_programa,
                    componente.nombre_componente,
                    tipo_apoyo.nombre_tipo_apoyo,
                    evento.nombre_evento,
                    capitulo,
                    evento.fecha_evento,
                    SUM(monto_partidas) AS capitulo_total
                FROM
                    reg_partidas
                        INNER JOIN
                    objeto_de_gasto ON clave = partida
                    INNER JOIN 
                    evento ON evento.id_evento = reg_partidas.id_evento_partidas
                    INNER JOIN 
                    tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
                    INNER JOIN 
                    componente ON componente.id_componente = tipo_apoyo.ta_id_componente
                    INNER JOIN
                    programa ON programa.id_programa = componente.com_id_programa
                    WHERE
                    programa.id_programa=$id_usuario_programa  and componente.id_componente='$id_componente' and tipo_apoyo.id_tipo_apoyo='$id_actividad' and (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento<=  ' + $f_final + ')
                    GROUP BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento, capitulo
                    ORDER BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento, capitulo";
                    $tabla1_capitulo_exportar = $mysqli->query($tabla_capitulo);
                    $tabla1_capitulo = $mysqli->query($tabla_capitulo);
                    $tabla_capitulo_rows = array();
                    $tabla_capitulo_table = array();

                    $tabla_capitulo_table['cols']= array(
                        array('label'=>'Programa', 'type'=>'string'),
                        array('label'=>'Componente', 'type'=>'string'),
                        array('label'=>'Actividad', 'type'=>'string'),
                        array('label'=>'Evento', 'type'=>'string'),
                        array('label'=>'Capítulo', 'type'=>'number'),
                        array('label'=>'Monto', 'type'=>'number'),

                    );
                        foreach($tabla1_capitulo as $b){
                            $tabla_capitulo_temp = array();
                            $tabla_capitulo_temp[] = array('v'=>(string) $b['nombre_programa']);
                            $tabla_capitulo_temp[] = array('v'=>(string) $b['nombre_componente']);
                            $tabla_capitulo_temp[] = array('v'=>(string) $b['nombre_tipo_apoyo']);
                            $tabla_capitulo_temp[] = array('v'=>(string) $b['nombre_evento']);
                            $tabla_capitulo_temp[] = array('v'=>(integer) $b['capitulo']);
                            $tabla_capitulo_temp[] = array('v'=>(integer) $b['capitulo_total']);
                            $tabla_capitulo_rows[] = array ('c'=>$tabla_capitulo_temp);
                        }
                        $tabla_capitulo_table['rows']=$tabla_capitulo_rows;
                        $tabla_capitulo_jsonTable =json_encode($tabla_capitulo_table);  //GRAFICO 17 

////RESUMEN POR TIPO DE ACCIONES (TRANSVERSALIDAD, ACCIONES, PREVENCION)

$tabla_acciones = "SELECT programa.nombre_programa, tipo_apoyo.tipo_accion, sum(evento.cantidad_beneficio) as personas_atendidas, sum(evento.hombres) as hombres, sum(evento.mujeres) as mujeres,
sum(evento.monto_evento)as inversion_ejercido,
count(tipo_apoyo.tipo_accion) as acciones
FROM
    tipo_apoyo 
INNER JOIN
    componente ON componente.id_componente = tipo_apoyo.ta_id_componente
INNER JOIN
    programa ON componente.com_id_programa = programa.id_programa
INNER JOIN  
    evento ON evento.id_accion_evento = tipo_apoyo.id_tipo_apoyo   
WHERE programa.id_programa = $id_usuario_programa  and componente.id_componente='$id_componente' and tipo_apoyo.id_tipo_apoyo='$id_actividad' AND  (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento <=  ' + $f_final + ')
group by  programa.id_programa, tipo_accion
order by  tipo_accion";
$tabla_acciones1_exportar = $mysqli->query($tabla_acciones);
$tabla_acciones1 = $mysqli->query($tabla_acciones);
$tabla_acciones_rows = array();
$tabla_acciones_table = array();

$tabla_acciones_table['cols']= array(
    array('label'=>'Programa', 'type'=>'string'),
    array('label'=>'Tipo de Acción', 'type'=>'string'),
    array('label'=>'Personas Atendidas', 'type'=>'number'),
	array('label'=>'Hombres', 'type'=>'number'),
	array('label'=>'Mujeres', 'type'=>'number'),
    array('label'=>'Monto ejercido', 'type'=>'number'),  
    array('label'=>'Número de acciones', 'type'=>'number'),

);
    foreach($tabla_acciones1 as $b){
        $tabla_temp = array();
        $tabla_temp[] = array('v'=>(string) $b['nombre_programa']);
        $tabla_temp[] = array('v'=>(string) $b['tipo_accion']);
        $tabla_temp[] = array('v'=>(integer) $b['personas_atendidas']);
		$tabla_temp[] = array('v'=>(integer) $b['hombres']);
		$tabla_temp[] = array('v'=>(integer) $b['mujeres']);
        $tabla_temp[] = array('v'=>(integer) $b['inversion_ejercido']);
        $tabla_temp[] = array('v'=>(integer) $b['acciones']);
        $tabla_acciones_rows[] = array ('c'=>$tabla_temp);
    }
    $tabla_acciones_table['rows']=$tabla_acciones_rows;
    $tabla_accionesjsonTable =json_encode($tabla_acciones_table);  //GRAFICO 13  
////  
////DESGLOSE POR TIPO DE ACCIONES (TRANSVERSALIDAD, ACCIONES, PREVENCION)

$tabla_acciones = "SELECT programa.nombre_programa, componente.nombre_componente, tipo_apoyo.tipo_accion, evento.nombre_evento, sum(evento.cantidad_beneficio) as personas_atendidas, sum(evento.hombres) as hombres, sum(evento.mujeres) as mujeres,
sum(evento.monto_evento)as inversion_ejercido
FROM
	tipo_apoyo 
INNER JOIN
	componente ON componente.id_componente = tipo_apoyo.ta_id_componente
INNER JOIN
	programa ON componente.com_id_programa = programa.id_programa
INNER JOIN  
	evento ON evento.id_accion_evento = tipo_apoyo.id_tipo_apoyo   
WHERE programa.id_programa = $id_usuario_programa  and componente.id_componente='$id_componente' and tipo_apoyo.id_tipo_apoyo='$id_actividad'  AND  (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento <=  ' + $f_final + ')
group by programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento, evento.fecha_evento, tipo_accion
order by  tipo_accion";
$tabla_acciones1_desglose_exportar = $mysqli->query($tabla_acciones);
$tabla_acciones1 = $mysqli->query($tabla_acciones);
$tabla_acciones_rows = array();
$tabla_acciones_table = array();

$tabla_acciones_table['cols']= array(
    array('label'=>'Programa', 'type'=>'string'),
    array('label'=>'Componente', 'type'=>'string'),
    array('label'=>'Tipo de Acción', 'type'=>'string'),
    array('label'=>'Evento', 'type'=>'string'),
    array('label'=>'Personas Atendidas', 'type'=>'number'),
	array('label'=>'Hombres', 'type'=>'number'),
	array('label'=>'Mujeres', 'type'=>'number'),
    array('label'=>'Monto ejercido', 'type'=>'number'),  


);
    foreach($tabla_acciones1 as $b){
        $tabla_temp = array();    
        $tabla_temp[] = array('v'=>(string) $b['nombre_programa']);
        $tabla_temp[] = array('v'=>(string) $b['nombre_componente']);
        $tabla_temp[] = array('v'=>(string) $b['tipo_accion']);
        $tabla_temp[] = array('v'=>(string) $b['nombre_evento']);
        $tabla_temp[] = array('v'=>(integer) $b['personas_atendidas']);
		$tabla_temp[] = array('v'=>(integer) $b['hombres']);
		$tabla_temp[] = array('v'=>(integer) $b['mujeres']);
        $tabla_temp[] = array('v'=>(integer) $b['inversion_ejercido']);

        $tabla_acciones_rows[] = array ('c'=>$tabla_temp);
    }
    $tabla_acciones_table['rows']=$tabla_acciones_rows;
    $tabla_acciones_desglosejsonTable =json_encode($tabla_acciones_table);   //GRAFICO 14  
///////INICIO REGION, MUNICIPIO, GENERO ---EVENTO
//-----GRÁFICO POR REGION GRAFICO PAY

    $pastel = "SELECT  region.region, COUNT(region.region) AS Total_Cuenta 
    FROM
    evento
    INNER JOIN localidad ON localidad.cve_localidad = evento.lugar_evento
    INNER JOIN municipio ON municipio.cve_municipios = localidad.l_cve_municipio
    INNER JOIN region ON region.cve_regiones = municipio.m_cve_region
    INNER JOIN tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
    INNER JOIN componente ON componente.id_componente = tipo_apoyo.ta_id_componente
    INNER JOIN programa ON programa.id_programa = componente.com_id_programa
    WHERE programa.id_programa = $id_usuario_programa   and componente.id_componente='$id_componente' and tipo_apoyo.id_tipo_apoyo='$id_actividad' 
    AND  (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento <=  ' + $f_final + ')  
    GROUP BY region.region ORDER BY  region.region  ASC";


    $pastel1 = $mysqli->query($pastel);
    $pastel_rows = array();
    $pastel_table1evento = array();

    $pastel_table1evento['cols']= array(
        array('label'=>'Región', 'type'=>'string'),
        array('label'=>'Cantidad', 'type'=>'number')

    );
        foreach($pastel1 as $p){
            $pastel_temp = array();
            $pastel_temp[] = array('v'=>(string) $p['region']);
            $pastel_temp[] = array('v'=>(integer) $p['Total_Cuenta']);
            $pastel_rows[] = array ('c'=>$pastel_temp);
        }
        $pastel_table1evento['rows']=$pastel_rows;
        $pasteljsonTable1evento =json_encode($pastel_table1evento);

//////////

//-----GRÁFICO POR MUNICIPIO -GRÁFICO PAY

        ///////////

            $pastel = "SELECT  municipio.municipio, COUNT(municipio.municipio) AS Total_Cuenta 
            FROM
            evento 
	        INNER JOIN localidad ON localidad.cve_localidad = evento.lugar_evento
            INNER JOIN municipio ON municipio.cve_municipios = localidad.l_cve_municipio
            INNER JOIN region ON region.cve_regiones = municipio.m_cve_region
            INNER JOIN tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
            INNER JOIN componente ON componente.id_componente = tipo_apoyo.ta_id_componente
            INNER JOIN programa ON programa.id_programa = componente.com_id_programa
            WHERE programa.id_programa = $id_usuario_programa   and componente.id_componente='$id_componente' and tipo_apoyo.id_tipo_apoyo='$id_actividad' 
            AND  (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento <=  ' + $f_final + ')   
            GROUP BY municipio.municipio ORDER BY  municipio.municipio  ASC";

            $tabla_localidades = "SELECT programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento, evento.fecha_evento, region.region, municipio.municipio, localidad.localidad, localidad.latitud_dec, localidad.longitud_dec, COUNT(localidad.localidad) AS Total_Cuenta 
            FROM
            evento 
	        INNER JOIN localidad ON localidad.cve_localidad = evento.lugar_evento
            INNER JOIN municipio ON municipio.cve_municipios = localidad.l_cve_municipio
            INNER JOIN region ON region.cve_regiones = municipio.m_cve_region
            INNER JOIN tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
            INNER JOIN componente ON componente.id_componente = tipo_apoyo.ta_id_componente
            INNER JOIN programa ON programa.id_programa = componente.com_id_programa
            WHERE programa.id_programa = $id_usuario_programa   and componente.id_componente='$id_componente' and tipo_apoyo.id_tipo_apoyo='$id_actividad' 
            AND  (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento <=  ' + $f_final + ')   
            GROUP BY localidad.localidad ORDER BY  localidad.localidad  ASC";

        $tablalocexportar = $mysqli->query($tabla_localidades);
        $pastel1 = $mysqli->query($pastel);
        $pastel_rows = array();
        $pastel_table2evento = array();

        $pastel_table2evento['cols']= array(
            array('label'=>'Municipio %', 'type'=>'string'),
            array('label'=>'Cantidad', 'type'=>'number')

        );
            foreach($pastel1 as $p){
                $pastel_temp = array();
                $pastel_temp[] = array('v'=>(string) $p['municipio']);
                $pastel_temp[] = array('v'=>(integer) $p['Total_Cuenta']);
                $pastel_rows[] = array ('c'=>$pastel_temp);
            }
            $pastel_table2evento['rows']=$pastel_rows;
            $pasteljsonTable2evento =json_encode($pastel_table2evento);

        //////////
//-----GRÁFICO POR GÉNERO
				$sqlh="UPDATE hombre_mujer SET hombre_mujer.cantidad_h_m =( SELECT SUM( evento.hombres) AS Total_CuentaH
                FROM
                evento 
	            INNER JOIN localidad ON localidad.cve_localidad = evento.lugar_evento
                INNER JOIN municipio ON municipio.cve_municipios = localidad.l_cve_municipio
                INNER JOIN region ON region.cve_regiones = municipio.m_cve_region
                INNER JOIN tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
                INNER JOIN componente ON componente.id_componente = tipo_apoyo.ta_id_componente
                INNER JOIN programa ON programa.id_programa = componente.com_id_programa
                WHERE programa.id_programa = $id_usuario_programa  and componente.id_componente='$id_componente' and tipo_apoyo.id_tipo_apoyo='$id_actividad' 
                AND  (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento <=  ' + $f_final + '))
				WHERE hombre_mujer.h_m ='HOMBRE'";
				$mysqli->query($sqlh);
				
				$sqlm="UPDATE hombre_mujer SET hombre_mujer.cantidad_h_m =( SELECT SUM( evento.mujeres) AS Total_CuentaM 
                FROM
                evento 
	            INNER JOIN localidad ON localidad.cve_localidad = evento.lugar_evento
                INNER JOIN municipio ON municipio.cve_municipios = localidad.l_cve_municipio
                INNER JOIN region ON region.cve_regiones = municipio.m_cve_region
                INNER JOIN tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
                INNER JOIN componente ON componente.id_componente = tipo_apoyo.ta_id_componente
                INNER JOIN programa ON programa.id_programa = componente.com_id_programa
                WHERE programa.id_programa = $id_usuario_programa  and componente.id_componente='$id_componente' and tipo_apoyo.id_tipo_apoyo='$id_actividad' 
                AND  (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento <=  ' + $f_final + '))
				WHERE hombre_mujer.h_m ='MUJER'";
				$mysqli->query($sqlm);	
            //////////
				$pastel = "SELECT h_m, cantidad_h_m FROM hombre_mujer";
                $pastel1 = $mysqli->query($pastel);
                $pastel_rows = array();
                $pastel_table3evento = array();

                $pastel_table3evento['cols']= array(
                    array('label'=>'Género', 'type'=>'string'),
                    array('label'=>'Cantidad', 'type'=>'number')

                );
                    foreach($pastel1 as $p){
                        $pastel_temp = array();
                        $pastel_temp[] = array('v'=>(string) $p['h_m']);
                        $pastel_temp[] = array('v'=>(integer) $p['cantidad_h_m']);
                        $pastel_rows[] = array ('c'=>$pastel_temp);
                    }
                    $pastel_table3evento['rows']=$pastel_rows;
                    $pasteljsonTable3evento =json_encode($pastel_table3evento);

                
///////FIN REGION, MUNICIPIO, GENERO --- EVENTO
//*CUARTA CONSULTA 3 VARIABLES
}// SI SELECCIONA PROGRAMA, COMPONENTE, ACTIVIDAD
elseif((!empty($_GET['programa'])) && (!empty($_GET['componente'])) && (!empty($_GET['tipo_apoyo'])) ){
    $id_usuario_programa =($_GET['programa']);
    $id_componente =($_GET['componente']);
    $id_actividad =($_GET['tipo_apoyo']);
   
    $sqlProgramas = "SELECT 
    programa.id_programa,    
    nombre_programa,
    componente.id_componente,
    componente.nombre_componente,
    tipo_apoyo.id_tipo_apoyo,
    tipo_apoyo.nombre_tipo_apoyo
FROM
    programa
    INNER JOIN componente on componente.com_id_programa = programa.id_programa
    INNER JOIN tipo_apoyo on tipo_apoyo.ta_id_componente = componente.id_componente
    WHERE programa.id_programa  = $id_usuario_programa 
    AND componente.id_componente = $id_componente
    AND tipo_apoyo.id_tipo_apoyo = $id_actividad ";

    $programas = $mysqli->query($sqlProgramas);
    $row_programa  = $programas->fetch_assoc();
    $_programa=$row_programa ['nombre_programa'];
    $_nombre_componente=$row_programa ['nombre_componente'];
    $_nombre_tipo_apoyo=$row_programa ['nombre_tipo_apoyo'];
                    
        //-----GRÁFICO PRESUPUESTO EJERCIDO POR ACTIVIDAD
        //////////
            $pastel = "SELECT tipo_apoyo.id_tipo_apoyo, tipo_apoyo.nombre_tipo_apoyo, tipo_apoyo.ta_id_componente, tipo_apoyo.meta_um_servicio, tipo_apoyo.meta_um_beneficio, tipo_apoyo.meta_cantidad_servicio, tipo_apoyo.meta_cantidad_beneficio, tipo_apoyo.meta_alcanzada_cantidad_servicio, tipo_apoyo.meta_alcanzada_cantidad_beneficio, tipo_apoyo.tipo_apoyo_presupuesto_ejercido, componente.nombre_componente, programa.nombre_programa, programa.id_programa, evento.fecha_evento,
            reg_partidas.monto_partidas, sum(reg_partidas.monto_partidas) as programa_presupuesto_ejercido_fechas  
            FROM  programa
            INNER JOIN componente on componente.com_id_programa = programa.id_programa
            INNER JOIN tipo_apoyo on tipo_apoyo.ta_id_componente = componente.id_componente
            INNER JOIN evento on evento.id_accion_evento = tipo_apoyo.id_tipo_apoyo
            INNER JOIN reg_partidas on reg_partidas.id_evento_partidas = evento.id_evento  
            where programa.id_programa=$id_usuario_programa  and componente.id_componente='$id_componente' and tipo_apoyo.id_tipo_apoyo='$id_actividad'  ORDER BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo";

        $pastel1 = $mysqli->query($pastel);
        $pastel_rows = array();
        $pastel_table6 = array();

        $pastel_table6['cols']= array(
            array('label'=>'Actividad', 'type'=>'string'),
            array('label'=>'Monto', 'type'=>'number')

        );
            foreach($pastel1 as $p){
                $pastel_temp = array();
                $pastel_temp[] = array('v'=>(string) $p['nombre_tipo_apoyo']);
                $pastel_temp[] = array('v'=>(integer) $p['programa_presupuesto_ejercido_fechas']);
                $pastel_rows[] = array ('c'=>$pastel_temp);
            }
            $pastel_table6['rows']=$pastel_rows;
            $pasteljsonTable6 =json_encode($pastel_table6);  //GRAFICO 11

        //////////Metas programadas vs Metas alcanzadas por Actividad
            $tabla = "SELECT tipo_apoyo.id_tipo_apoyo, tipo_apoyo.nombre_tipo_apoyo, tipo_apoyo.ta_id_componente, tipo_apoyo.meta_um_servicio, tipo_apoyo.meta_um_beneficio, tipo_apoyo.meta_cantidad_servicio, tipo_apoyo.meta_cantidad_beneficio, tipo_apoyo.meta_alcanzada_cantidad_servicio, tipo_apoyo.meta_alcanzada_cantidad_beneficio, tipo_apoyo.tipo_apoyo_presupuesto_ejercido, componente.nombre_componente, programa.nombre_programa, programa.id_programa,COUNT( meta_alcanzada_cantidad_servicio ) AS tipo_apoyoA,
            concat(round(( meta_alcanzada_cantidad_servicio/meta_cantidad_servicio * 100 ),2),'%') AS porcentaje_servicio,
            COUNT( meta_alcanzada_cantidad_beneficio ) AS tipo_apoyoA,
            concat(round(( meta_alcanzada_cantidad_beneficio/meta_cantidad_beneficio * 100 ),2),'%') AS porcentaje_beneficio,  servicios.nombre_servicios,  beneficios.nombre_beneficios,
            evento.fecha_evento,
            reg_partidas.monto_partidas,
            sum(reg_partidas.monto_partidas) as programa_presupuesto_ejercido_fechas  
            FROM programa
            INNER JOIN componente on componente.com_id_programa = programa.id_programa
            INNER JOIN tipo_apoyo on tipo_apoyo.ta_id_componente = componente.id_componente
            INNER JOIN evento on evento.id_accion_evento = tipo_apoyo.id_tipo_apoyo
            INNER JOIN reg_partidas on reg_partidas.id_evento_partidas = evento.id_evento  
            INNER JOIN beneficios ON beneficios.id_beneficios = tipo_apoyo.meta_um_beneficio 
            INNER JOIN servicios ON servicios.id_servicios = tipo_apoyo.meta_um_servicio
            where programa.id_programa=$id_usuario_programa  and componente.id_componente='$id_componente' and tipo_apoyo.id_tipo_apoyo='$id_actividad' group by id_tipo_apoyo ORDER BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo";
            $tabla1_exportar = $mysqli->query($tabla);
            $tabla1 = $mysqli->query($tabla);
            $tabla_rows = array();
            $tabla_table = array();

            $tabla_table['cols']= array(
                array('label'=>'Componente', 'type'=>'string'),
                array('label'=>'Actividad', 'type'=>'string'),
                array('label'=>'Monto ejercido', 'type'=>'number'),
                array('label'=>'U.M. Servicios', 'type'=>'string'),
                array('label'=>'Meta', 'type'=>'number'),
                array('label'=>'Avance', 'type'=>'number'),
                array('label'=>'%', 'type'=>'string'),
                array('label'=>'U.M. Beneficio', 'type'=>'string'),
                array('label'=>'Meta', 'type'=>'number'),
                array('label'=>'Avance', 'type'=>'number'),
                array('label'=>'%', 'type'=>'string'),

            );
                foreach($tabla1 as $b){
                    $tabla_temp = array();
                    $tabla_temp[] = array('v'=>(string) $b['nombre_componente']);
                    $tabla_temp[] = array('v'=>(string) $b['nombre_tipo_apoyo']);
                    $tabla_temp[] = array('v'=>(integer) $b['programa_presupuesto_ejercido_fechas']);
                    $tabla_temp[] = array('v'=>(string) $b['nombre_servicios']);
                    $tabla_temp[] = array('v'=>(integer) $b['meta_cantidad_servicio']);
                    $tabla_temp[] = array('v'=>(integer) $b['meta_alcanzada_cantidad_servicio']);
                    $tabla_temp[] = array('v'=>(string) $b['porcentaje_servicio']);
                    $tabla_temp[] = array('v'=>(string) $b['nombre_beneficios']);
                    $tabla_temp[] = array('v'=>(integer) $b['meta_cantidad_beneficio']);
                    $tabla_temp[] = array('v'=>(integer) $b['meta_alcanzada_cantidad_beneficio']);
                    $tabla_temp[] = array('v'=>(string) $b['porcentaje_beneficio']);
                    $tabla_rows[] = array ('c'=>$tabla_temp);
                }
                $tabla_table['rows']=$tabla_rows;
                $tablajsonTable =json_encode($tabla_table);   //GRAFICO 12  

                ////////////////Desglose de eventos realizados

                $tabla_evento = "SELECT 
                evento.id_evento,
                evento.nombre_evento,
                evento.pob_obj_evento,
                evento.nivel_geo_evento,
                evento.lugar_evento,
                evento.monto_evento,
                evento.fecha_evento,
                evento.fecha_modificacion_evento,
                evento.usuario_evento,
                evento.id_accion_evento,
                evento.cantidad_servicio,
                evento.cantidad_beneficio,
                evento.hombres, 
                evento.mujeres,
                region.region,
                municipio.municipio,
                localidad.localidad, 
                tipo_apoyo.nombre_tipo_apoyo,
                servicios.nombre_servicios,
                beneficios.nombre_beneficios,
                componente.nombre_componente,
                COUNT(evento.hombres) AS tipo_apoyoA,       
                concat(round((evento.hombres/cantidad_beneficio * 100 ),2),'%') AS porcentaje_hombres,
                COUNT(evento.mujeres) AS tipo_apoyoA,       
                concat(round((evento.mujeres/cantidad_beneficio * 100 ),2),'%') AS porcentaje_mujeres  
                FROM
                evento
                INNER JOIN
                localidad ON localidad.cve_localidad = evento.lugar_evento
                    INNER JOIN
                municipio ON municipio.cve_municipios = localidad.l_cve_municipio
                    INNER JOIN
                region ON region.cve_regiones = municipio.m_cve_region
                    INNER JOIN
                usuarios ON usuarios.id = evento.usuario_evento
                    INNER JOIN
                tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
                    INNER JOIN
                componente ON componente.id_componente = tipo_apoyo.ta_id_componente
                    INNER JOIN
                programa ON componente.com_id_programa = programa.id_programa  
                INNER JOIN
                beneficios ON beneficios.id_beneficios = tipo_apoyo.meta_um_beneficio
                        INNER JOIN
                servicios ON servicios.id_servicios = tipo_apoyo.meta_um_servicio
                where programa.id_programa=$id_usuario_programa  and componente.id_componente='$id_componente' and tipo_apoyo.id_tipo_apoyo='$id_actividad' 
                group by evento.id_evento
                ORDER BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento";

                $tabla1_evento_exportar = $mysqli->query($tabla_evento);    
                $tabla1_evento = $mysqli->query($tabla_evento);
                $tabla_evento_rows = array();
                $tabla_evento_table = array();

                $tabla_evento_table['cols']= array(
                    array('label'=>'Componente', 'type'=>'string'),
                    array('label'=>'Actividad', 'type'=>'string'),
                    array('label'=>'Evento', 'type'=>'string'),
                    array('label'=>'Monto ejercido', 'type'=>'number'),
                    array('label'=>'Fecha', 'type'=>'string'),
                    array('label'=>'U.M. Servicios', 'type'=>'string'),
                    array('label'=>'Meta alcanzada', 'type'=>'number'),
                    array('label'=>'U.M. Beneficio', 'type'=>'string'),
                    array('label'=>'Meta alcanzada', 'type'=>'number'),
                    array('label'=>'Hombres', 'type'=>'number'),
                    array('label'=>'%', 'type'=>'string'),
                    array('label'=>'Mujeres', 'type'=>'number'),
                    array('label'=>'%', 'type'=>'string'),             

                );
                    foreach($tabla1_evento as $b){
                        $tabla_evento_temp = array();
                        $tabla_evento_temp[] = array('v'=>(string) $b['nombre_componente']);
                        $tabla_evento_temp[] = array('v'=>(string) $b['nombre_tipo_apoyo']);
                        $tabla_evento_temp[] = array('v'=>(string) $b['nombre_evento']);
                        $tabla_evento_temp[] = array('v'=>(integer) $b['monto_evento']);
                        $tabla_evento_temp[] = array('v'=>(string) $b['fecha_evento']);
                        $tabla_evento_temp[] = array('v'=>(string) $b['nombre_servicios']);                    
                        $tabla_evento_temp[] = array('v'=>(integer) $b['cantidad_servicio']);
                        $tabla_evento_temp[] = array('v'=>(string) $b['nombre_beneficios']);
                        $tabla_evento_temp[] = array('v'=>(integer) $b['cantidad_beneficio']);
                        $tabla_evento_temp[] = array('v'=>(integer) $b['hombres']);
                        $tabla_evento_temp[] = array('v'=>(string) $b['porcentaje_hombres']);
                        $tabla_evento_temp[] = array('v'=>(integer) $b['mujeres']);
                        $tabla_evento_temp[] = array('v'=>(string) $b['porcentaje_mujeres']);
                        $tabla_evento_rows[] = array ('c'=>$tabla_evento_temp);
                    }
                    $tabla_evento_table['rows']=$tabla_evento_rows;
                    $tabla_evento_jsonTable =json_encode($tabla_evento_table);  //GRAFICO 15


            //-----GRÁFICO POR OBJETO DEL GASTO
                    //////////
                        $pastel = "SELECT reg_partidas.id_reg_partidas, reg_partidas.partida, reg_partidas.cantidad_partidas, reg_partidas.monto_partidas, reg_partidas.id_evento_partidas,
                        sum(reg_partidas.monto_partidas) as totalsuma,
                        objeto_de_gasto.partida_gasto_texto,
                        evento.fecha_evento,
                        reg_partidas.monto_partidas,
                        sum(reg_partidas.monto_partidas) as programa_presupuesto_ejercido_fechas
                        FROM reg_partidas
                        INNER JOIN evento on evento.id_evento = reg_partidas.id_evento_partidas
                        INNER JOIN tipo_apoyo on tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
                        INNER JOIN componente on componente.id_componente = tipo_apoyo.ta_id_componente
                        INNER JOIN programa on programa.id_programa = componente.com_id_programa
                        INNER JOIN objeto_de_gasto on objeto_de_gasto.clave = reg_partidas.partida
                        where programa.id_programa=$id_usuario_programa  and componente.id_componente='$id_componente' and tipo_apoyo.id_tipo_apoyo='$id_actividad'
                        group by reg_partidas.partida";

                        $pastel1 = $mysqli->query($pastel);
                        $pastel_rows = array();
                        $pastel_table9 = array();

                        $pastel_table9['cols']= array(
                            array('label'=>'Objeto de gasto', 'type'=>'string'),
                            array('label'=>'Presupuesto ejercido', 'type'=>'number')

                        );
                            foreach($pastel1 as $p){
                                $pastel_temp = array();
                                $pastel_temp[] = array('v'=>(string) $p['partida_gasto_texto']);
                                $pastel_temp[] = array('v'=>(integer) $p['totalsuma']);
                                $pastel_rows[] = array ('c'=>$pastel_temp);
                            }
                            $pastel_table9['rows']=$pastel_rows;
                            $pasteljsonTable9 =json_encode($pastel_table9);  //GRAFICO 18

                        //////////
        ////////////////Tabla Presupuesto ejercido a nivel de objeto de gasto (partidas y monto ejercido)

            $tabla_partida = "SELECT 
            id_reg_partidas,
            partida,
            cantidad_partidas,
            monto_partidas,
            id_evento_partidas,
            partida_gasto_texto,
            evento.nombre_evento,
            programa.nombre_programa,
            componente.nombre_componente,
            tipo_apoyo.nombre_tipo_apoyo,
            capitulo,
            evento.fecha_evento,
            reg_partidas.monto_partidas,
            sum(reg_partidas.monto_partidas) as programa_presupuesto_ejercido_fechas
        FROM
            reg_partidas
                INNER JOIN
            objeto_de_gasto ON clave = partida
            INNER JOIN 
            evento ON evento.id_evento = reg_partidas.id_evento_partidas
            INNER JOIN 
            tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
            INNER JOIN 
            componente ON componente.id_componente = tipo_apoyo.ta_id_componente
            INNER JOIN
            programa ON programa.id_programa = componente.com_id_programa
            WHERE
            programa.id_programa=$id_usuario_programa  and componente.id_componente='$id_componente' and tipo_apoyo.id_tipo_apoyo='$id_actividad'
            group by reg_partidas.id_reg_partidas
            ORDER BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento, reg_partidas.id_reg_partidas";
            $tabla1_partida_exportar = $mysqli->query($tabla_partida);
            $tabla1_partida = $mysqli->query($tabla_partida);
            $tabla_partida_rows = array();
            $tabla_partida_table = array();

            $tabla_partida_table['cols']= array(
                array('label'=>'Programa', 'type'=>'string'),
                array('label'=>'Componente', 'type'=>'string'),
                array('label'=>'Actividad', 'type'=>'string'),
                array('label'=>'Evento', 'type'=>'string'),
                array('label'=>'Capítulo', 'type'=>'number'),
                array('label'=>'Partida', 'type'=>'string'),
                array('label'=>'Monto', 'type'=>'number'),

            );
                foreach($tabla1_partida as $b){
                    $tabla_partida_temp = array();
                    $tabla_partida_temp[] = array('v'=>(string) $b['nombre_programa']);
                    $tabla_partida_temp[] = array('v'=>(string) $b['nombre_componente']);
                    $tabla_partida_temp[] = array('v'=>(string) $b['nombre_tipo_apoyo']);
                    $tabla_partida_temp[] = array('v'=>(string) $b['nombre_evento']);
                    $tabla_partida_temp[] = array('v'=>(integer) $b['capitulo']);
                    $tabla_partida_temp[] = array('v'=>(string) $b['partida_gasto_texto']);
                    $tabla_partida_temp[] = array('v'=>(integer) $b['monto_partidas']);
                    $tabla_partida_rows[] = array ('c'=>$tabla_partida_temp);
                }
                $tabla_partida_table['rows']=$tabla_partida_rows;
                $tabla_partida_jsonTable =json_encode($tabla_partida_table);  //GRAFICO 19

                //-----GRÁFICO POR OBJETO DEL GASTO CAPÍTULO
                    //////////
                    $pastel = "SELECT capitulo,  evento.fecha_evento, SUM(monto_partidas) AS capitulo_total
                    FROM reg_partidas
                    INNER JOIN evento on evento.id_evento = reg_partidas.id_evento_partidas
                    INNER JOIN tipo_apoyo on tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
                    INNER JOIN componente on componente.id_componente = tipo_apoyo.ta_id_componente
                    INNER JOIN programa on programa.id_programa = componente.com_id_programa
                    INNER JOIN objeto_de_gasto on objeto_de_gasto.clave = reg_partidas.partida
                    where programa.id_programa=$id_usuario_programa  and componente.id_componente='$id_componente' and tipo_apoyo.id_tipo_apoyo='$id_actividad'
                    group by capitulo";

                    $pastel1 = $mysqli->query($pastel);
                    $pastel_rows = array();
                    $pastel_table10 = array();

                    $pastel_table10['cols']= array(
                        array('label'=>'Capítulo', 'type'=>'string'),
                        array('label'=>'Presupuesto ejercido', 'type'=>'number')

                    );
                        foreach($pastel1 as $p){
                            $pastel_temp = array();
                            $pastel_temp[] = array('v'=>(integer) $p['capitulo']);
                            $pastel_temp[] = array('v'=>(integer) $p['capitulo_total']);
                            $pastel_rows[] = array ('c'=>$pastel_temp);
                        }
                        $pastel_table10['rows']=$pastel_rows;
                        $pasteljsonTable10 =json_encode($pastel_table10);  //GRAFICO 16

                    //////////

                    ////////////////Tabla Presupuesto ejercido a nivel de objeto de gasto (capitulo y monto ejercido)

                    $tabla_capitulo = "SELECT 
                    programa.nombre_programa,
                    componente.nombre_componente,
                    tipo_apoyo.nombre_tipo_apoyo,
                    evento.nombre_evento,
                    capitulo,
                    evento.fecha_evento,
                    SUM(monto_partidas) AS capitulo_total
                FROM
                    reg_partidas
                        INNER JOIN
                    objeto_de_gasto ON clave = partida
                    INNER JOIN 
                    evento ON evento.id_evento = reg_partidas.id_evento_partidas
                    INNER JOIN 
                    tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
                    INNER JOIN 
                    componente ON componente.id_componente = tipo_apoyo.ta_id_componente
                    INNER JOIN
                    programa ON programa.id_programa = componente.com_id_programa
                    WHERE
                    programa.id_programa=$id_usuario_programa  and componente.id_componente='$id_componente' and tipo_apoyo.id_tipo_apoyo='$id_actividad'
                    GROUP BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento, capitulo
                    ORDER BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento, capitulo";
                    $tabla1_capitulo_exportar = $mysqli->query($tabla_capitulo);
                    $tabla1_capitulo = $mysqli->query($tabla_capitulo);
                    $tabla_capitulo_rows = array();
                    $tabla_capitulo_table = array();

                    $tabla_capitulo_table['cols']= array(
                        array('label'=>'Programa', 'type'=>'string'),
                        array('label'=>'Componente', 'type'=>'string'),
                        array('label'=>'Actividad', 'type'=>'string'),
                        array('label'=>'Evento', 'type'=>'string'),
                        array('label'=>'Capítulo', 'type'=>'number'),
                        array('label'=>'Monto', 'type'=>'number'),

                    );
                        foreach($tabla1_capitulo as $b){
                            $tabla_capitulo_temp = array();
                            $tabla_capitulo_temp[] = array('v'=>(string) $b['nombre_programa']);
                            $tabla_capitulo_temp[] = array('v'=>(string) $b['nombre_componente']);
                            $tabla_capitulo_temp[] = array('v'=>(string) $b['nombre_tipo_apoyo']);
                            $tabla_capitulo_temp[] = array('v'=>(string) $b['nombre_evento']);
                            $tabla_capitulo_temp[] = array('v'=>(integer) $b['capitulo']);
                            $tabla_capitulo_temp[] = array('v'=>(integer) $b['capitulo_total']);
                            $tabla_capitulo_rows[] = array ('c'=>$tabla_capitulo_temp);
                        }
                        $tabla_capitulo_table['rows']=$tabla_capitulo_rows;
                        $tabla_capitulo_jsonTable =json_encode($tabla_capitulo_table);  //GRAFICO 17 

////RESUMEN POR TIPO DE ACCIONES (TRANSVERSALIDAD, ACCIONES, PREVENCION)

$tabla_acciones = "SELECT programa.nombre_programa, tipo_apoyo.tipo_accion, sum(evento.cantidad_beneficio) as personas_atendidas, sum(evento.hombres) as hombres, sum(evento.mujeres) as mujeres,
sum(evento.monto_evento)as inversion_ejercido,
count(tipo_apoyo.tipo_accion) as acciones
FROM
    tipo_apoyo 
INNER JOIN
    componente ON componente.id_componente = tipo_apoyo.ta_id_componente
INNER JOIN
    programa ON componente.com_id_programa = programa.id_programa
INNER JOIN  
    evento ON evento.id_accion_evento = tipo_apoyo.id_tipo_apoyo   
WHERE programa.id_programa = $id_usuario_programa  and componente.id_componente='$id_componente' and tipo_apoyo.id_tipo_apoyo='$id_actividad' 
group by  programa.id_programa, tipo_accion
order by  tipo_accion";
$tabla_acciones1_exportar = $mysqli->query($tabla_acciones);
$tabla_acciones1 = $mysqli->query($tabla_acciones);
$tabla_acciones_rows = array();
$tabla_acciones_table = array();

$tabla_acciones_table['cols']= array(
    array('label'=>'Programa', 'type'=>'string'),
    array('label'=>'Tipo de Acción', 'type'=>'string'),
    array('label'=>'Personas Atendidas', 'type'=>'number'),
	array('label'=>'Hombres', 'type'=>'number'),
	array('label'=>'Mujeres', 'type'=>'number'),
    array('label'=>'Monto ejercido', 'type'=>'number'),  
    array('label'=>'Número de acciones', 'type'=>'number'),

);
    foreach($tabla_acciones1 as $b){
        $tabla_temp = array();
        $tabla_temp[] = array('v'=>(string) $b['nombre_programa']);
        $tabla_temp[] = array('v'=>(string) $b['tipo_accion']);
        $tabla_temp[] = array('v'=>(integer) $b['personas_atendidas']);
		$tabla_temp[] = array('v'=>(integer) $b['hombres']);
		$tabla_temp[] = array('v'=>(integer) $b['mujeres']);
        $tabla_temp[] = array('v'=>(integer) $b['inversion_ejercido']);
        $tabla_temp[] = array('v'=>(integer) $b['acciones']);
        $tabla_acciones_rows[] = array ('c'=>$tabla_temp);
    }
    $tabla_acciones_table['rows']=$tabla_acciones_rows;
    $tabla_accionesjsonTable =json_encode($tabla_acciones_table);  //GRAFICO 13  
////  
////DESGLOSE POR TIPO DE ACCIONES (TRANSVERSALIDAD, ACCIONES, PREVENCION)

$tabla_acciones = "SELECT programa.nombre_programa, componente.nombre_componente, tipo_apoyo.tipo_accion, evento.nombre_evento, sum(evento.cantidad_beneficio) as personas_atendidas, sum(evento.hombres) as hombres, sum(evento.mujeres) as mujeres,
sum(evento.monto_evento)as inversion_ejercido
FROM
	tipo_apoyo 
INNER JOIN
	componente ON componente.id_componente = tipo_apoyo.ta_id_componente
INNER JOIN
	programa ON componente.com_id_programa = programa.id_programa
INNER JOIN  
	evento ON evento.id_accion_evento = tipo_apoyo.id_tipo_apoyo   
WHERE programa.id_programa = $id_usuario_programa  and componente.id_componente='$id_componente' and tipo_apoyo.id_tipo_apoyo='$id_actividad' 
group by programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento, evento.fecha_evento, tipo_accion
order by  tipo_accion";
$tabla_acciones1_desglose_exportar = $mysqli->query($tabla_acciones);
$tabla_acciones1 = $mysqli->query($tabla_acciones);
$tabla_acciones_rows = array();
$tabla_acciones_table = array();

$tabla_acciones_table['cols']= array(
    array('label'=>'Programa', 'type'=>'string'),
    array('label'=>'Componente', 'type'=>'string'),
    array('label'=>'Tipo de Acción', 'type'=>'string'),
    array('label'=>'Evento', 'type'=>'string'),
    array('label'=>'Personas Atendidas', 'type'=>'number'),
	array('label'=>'Hombres', 'type'=>'number'),
	array('label'=>'Mujeres', 'type'=>'number'),
    array('label'=>'Monto ejercido', 'type'=>'number'),  


);
    foreach($tabla_acciones1 as $b){
        $tabla_temp = array();    
        $tabla_temp[] = array('v'=>(string) $b['nombre_programa']);
        $tabla_temp[] = array('v'=>(string) $b['nombre_componente']);
        $tabla_temp[] = array('v'=>(string) $b['tipo_accion']);
        $tabla_temp[] = array('v'=>(string) $b['nombre_evento']);
        $tabla_temp[] = array('v'=>(integer) $b['personas_atendidas']);
		$tabla_temp[] = array('v'=>(integer) $b['hombres']);
		$tabla_temp[] = array('v'=>(integer) $b['mujeres']);
        $tabla_temp[] = array('v'=>(integer) $b['inversion_ejercido']);

        $tabla_acciones_rows[] = array ('c'=>$tabla_temp);
    }
    $tabla_acciones_table['rows']=$tabla_acciones_rows;
    $tabla_acciones_desglosejsonTable =json_encode($tabla_acciones_table);   //GRAFICO 14  
////
///////INICIO REGION, MUNICIPIO, GENERO ---EVENTO
//-----GRÁFICO POR REGION GRAFICO PAY

    $pastel = "SELECT  region.region, COUNT(region.region) AS Total_Cuenta 
    FROM
    evento
    INNER JOIN localidad ON localidad.cve_localidad = evento.lugar_evento
    INNER JOIN municipio ON municipio.cve_municipios = localidad.l_cve_municipio
    INNER JOIN region ON region.cve_regiones = municipio.m_cve_region
    INNER JOIN tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
    INNER JOIN componente ON componente.id_componente = tipo_apoyo.ta_id_componente
    INNER JOIN programa ON programa.id_programa = componente.com_id_programa
    WHERE programa.id_programa = $id_usuario_programa   and componente.id_componente='$id_componente' and tipo_apoyo.id_tipo_apoyo='$id_actividad' 
    GROUP BY region.region ORDER BY  region.region  ASC";


    $pastel1 = $mysqli->query($pastel);
    $pastel_rows = array();
    $pastel_table1evento = array();

    $pastel_table1evento['cols']= array(
        array('label'=>'Región', 'type'=>'string'),
        array('label'=>'Cantidad', 'type'=>'number')

    );
        foreach($pastel1 as $p){
            $pastel_temp = array();
            $pastel_temp[] = array('v'=>(string) $p['region']);
            $pastel_temp[] = array('v'=>(integer) $p['Total_Cuenta']);
            $pastel_rows[] = array ('c'=>$pastel_temp);
        }
        $pastel_table1evento['rows']=$pastel_rows;
        $pasteljsonTable1evento =json_encode($pastel_table1evento);

//////////

//-----GRÁFICO POR MUNICIPIO -GRÁFICO PAY

        ///////////

            $pastel = "SELECT  municipio.municipio, COUNT(municipio.municipio) AS Total_Cuenta 
            FROM
            evento 
	        INNER JOIN localidad ON localidad.cve_localidad = evento.lugar_evento
            INNER JOIN municipio ON municipio.cve_municipios = localidad.l_cve_municipio
            INNER JOIN region ON region.cve_regiones = municipio.m_cve_region
            INNER JOIN tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
            INNER JOIN componente ON componente.id_componente = tipo_apoyo.ta_id_componente
            INNER JOIN programa ON programa.id_programa = componente.com_id_programa
            WHERE programa.id_programa = $id_usuario_programa   and componente.id_componente='$id_componente' and tipo_apoyo.id_tipo_apoyo='$id_actividad' 
            GROUP BY municipio.municipio ORDER BY  municipio.municipio  ASC";

            $tabla_localidades = "SELECT programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento, evento.fecha_evento, region.region, municipio.municipio, localidad.localidad, localidad.latitud_dec, localidad.longitud_dec, COUNT(localidad.localidad) AS Total_Cuenta 
            FROM
            evento 
	        INNER JOIN localidad ON localidad.cve_localidad = evento.lugar_evento
            INNER JOIN municipio ON municipio.cve_municipios = localidad.l_cve_municipio
            INNER JOIN region ON region.cve_regiones = municipio.m_cve_region
            INNER JOIN tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
            INNER JOIN componente ON componente.id_componente = tipo_apoyo.ta_id_componente
            INNER JOIN programa ON programa.id_programa = componente.com_id_programa
            WHERE programa.id_programa = $id_usuario_programa   and componente.id_componente='$id_componente' and tipo_apoyo.id_tipo_apoyo='$id_actividad' 
            GROUP BY localidad.localidad ORDER BY  localidad.localidad  ASC";

        $tablalocexportar = $mysqli->query($tabla_localidades);
        $pastel1 = $mysqli->query($pastel);
        $pastel_rows = array();
        $pastel_table2evento = array();

        $pastel_table2evento['cols']= array(
            array('label'=>'Municipio %', 'type'=>'string'),
            array('label'=>'Cantidad', 'type'=>'number')

        );
            foreach($pastel1 as $p){
                $pastel_temp = array();
                $pastel_temp[] = array('v'=>(string) $p['municipio']);
                $pastel_temp[] = array('v'=>(integer) $p['Total_Cuenta']);
                $pastel_rows[] = array ('c'=>$pastel_temp);
            }
            $pastel_table2evento['rows']=$pastel_rows;
            $pasteljsonTable2evento =json_encode($pastel_table2evento);

        //////////
//-----GRÁFICO POR GÉNERO
				$sqlh="UPDATE hombre_mujer SET hombre_mujer.cantidad_h_m =( SELECT SUM( evento.hombres) AS Total_CuentaH
                FROM
                evento 
	            INNER JOIN localidad ON localidad.cve_localidad = evento.lugar_evento
                INNER JOIN municipio ON municipio.cve_municipios = localidad.l_cve_municipio
                INNER JOIN region ON region.cve_regiones = municipio.m_cve_region
                INNER JOIN tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
                INNER JOIN componente ON componente.id_componente = tipo_apoyo.ta_id_componente
                INNER JOIN programa ON programa.id_programa = componente.com_id_programa
                WHERE programa.id_programa = $id_usuario_programa  and componente.id_componente='$id_componente' and tipo_apoyo.id_tipo_apoyo='$id_actividad' )
				WHERE hombre_mujer.h_m ='HOMBRE'";
				$mysqli->query($sqlh);
				
				$sqlm="UPDATE hombre_mujer SET hombre_mujer.cantidad_h_m =( SELECT SUM( evento.mujeres) AS Total_CuentaM 
                FROM
                evento 
	            INNER JOIN localidad ON localidad.cve_localidad = evento.lugar_evento
                INNER JOIN municipio ON municipio.cve_municipios = localidad.l_cve_municipio
                INNER JOIN region ON region.cve_regiones = municipio.m_cve_region
                INNER JOIN tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
                INNER JOIN componente ON componente.id_componente = tipo_apoyo.ta_id_componente
                INNER JOIN programa ON programa.id_programa = componente.com_id_programa
                WHERE programa.id_programa = $id_usuario_programa  and componente.id_componente='$id_componente' and tipo_apoyo.id_tipo_apoyo='$id_actividad' )
				WHERE hombre_mujer.h_m ='MUJER'";
				$mysqli->query($sqlm);
            //////////
                $pastel = "SELECT h_m, cantidad_h_m FROM hombre_mujer";
                $pastel1 = $mysqli->query($pastel);
                $pastel_rows = array();
                $pastel_table3evento = array();

                $pastel_table3evento['cols']= array(
                    array('label'=>'Género', 'type'=>'string'),
                    array('label'=>'Cantidad', 'type'=>'number')

                );
                    foreach($pastel1 as $p){
                        $pastel_temp = array();
                        $pastel_temp[] = array('v'=>(string) $p['h_m']);
                        $pastel_temp[] = array('v'=>(integer) $p['cantidad_h_m']);
                        $pastel_rows[] = array ('c'=>$pastel_temp);
                    }
                    $pastel_table3evento['rows']=$pastel_rows;
                    $pasteljsonTable3evento =json_encode($pastel_table3evento);

                
///////FIN REGION, MUNICIPIO, GENERO --- EVENTO
//// SI NO  SELECCIONA PROGRAMA, COMPONENTE, ACTIVIDAD 
}
//*QUINTA CONSULTA 2 VARIABLES Y FECHA
// SI SELECCIONA PROGRAMA, COMPONENTE Y FECHA
elseif((!empty($_GET['programa'])) && (!empty($_GET['componente'])) && (!empty($_GET['fechainicio']) && !empty($_GET['fechafinal'])) ){
    $id_usuario_programa =($_GET['programa']);
    $id_componente =($_GET['componente']);
  
    $sqlProgramas = "SELECT 
    programa.id_programa,    
    nombre_programa,
    componente.id_componente,
    componente.nombre_componente
FROM
    programa
    INNER JOIN componente on componente.com_id_programa = programa.id_programa
    WHERE programa.id_programa  = $id_usuario_programa 
    AND componente.id_componente = $id_componente ";

    $programas = $mysqli->query($sqlProgramas);
    $row_programa  = $programas->fetch_assoc();
    $_programa=$row_programa ['nombre_programa'];
    $_nombre_componente=$row_programa ['nombre_componente'];
    
    $f_inicio =($_GET['fechainicio']);
    $f_final =($_GET['fechafinal']);

        //-----GRÁFICO PRESUPUESTO EJERCIDO POR ACTIVIDAD
        //////////
            $pastel = "SELECT tipo_apoyo.id_tipo_apoyo, tipo_apoyo.nombre_tipo_apoyo, tipo_apoyo.ta_id_componente, tipo_apoyo.meta_um_servicio, tipo_apoyo.meta_um_beneficio, tipo_apoyo.meta_cantidad_servicio, tipo_apoyo.meta_cantidad_beneficio, tipo_apoyo.meta_alcanzada_cantidad_servicio, tipo_apoyo.meta_alcanzada_cantidad_beneficio, tipo_apoyo.tipo_apoyo_presupuesto_ejercido, componente.nombre_componente, programa.nombre_programa, programa.id_programa, evento.fecha_evento,
            reg_partidas.monto_partidas, sum(reg_partidas.monto_partidas) as programa_presupuesto_ejercido_fechas  
            FROM  programa
            INNER JOIN componente on componente.com_id_programa = programa.id_programa
            INNER JOIN tipo_apoyo on tipo_apoyo.ta_id_componente = componente.id_componente
            INNER JOIN evento on evento.id_accion_evento = tipo_apoyo.id_tipo_apoyo
            INNER JOIN reg_partidas on reg_partidas.id_evento_partidas = evento.id_evento  
            where programa.id_programa=$id_usuario_programa  and componente.id_componente='$id_componente' and (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento<=  ' + $f_final + ') ORDER BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo";

        $pastel1 = $mysqli->query($pastel);
        $pastel_rows = array();
        $pastel_table6 = array();

        $pastel_table6['cols']= array(
            array('label'=>'Actividad', 'type'=>'string'),
            array('label'=>'Monto', 'type'=>'number')

        );
            foreach($pastel1 as $p){
                $pastel_temp = array();
                $pastel_temp[] = array('v'=>(string) $p['nombre_tipo_apoyo']);
                $pastel_temp[] = array('v'=>(integer) $p['programa_presupuesto_ejercido_fechas']);
                $pastel_rows[] = array ('c'=>$pastel_temp);
            }
            $pastel_table6['rows']=$pastel_rows;
            $pasteljsonTable6 =json_encode($pastel_table6);  //GRAFICO 11

        //////////Metas programadas vs Metas alcanzadas por Actividad
            $tabla = "SELECT tipo_apoyo.id_tipo_apoyo, tipo_apoyo.nombre_tipo_apoyo, tipo_apoyo.ta_id_componente, tipo_apoyo.meta_um_servicio, tipo_apoyo.meta_um_beneficio, tipo_apoyo.meta_cantidad_servicio, tipo_apoyo.meta_cantidad_beneficio, tipo_apoyo.meta_alcanzada_cantidad_servicio, tipo_apoyo.meta_alcanzada_cantidad_beneficio, tipo_apoyo.tipo_apoyo_presupuesto_ejercido, componente.nombre_componente, programa.nombre_programa, programa.id_programa,COUNT( meta_alcanzada_cantidad_servicio ) AS tipo_apoyoA,
            concat(round(( meta_alcanzada_cantidad_servicio/meta_cantidad_servicio * 100 ),2),'%') AS porcentaje_servicio,
            COUNT( meta_alcanzada_cantidad_beneficio ) AS tipo_apoyoA,
            concat(round(( meta_alcanzada_cantidad_beneficio/meta_cantidad_beneficio * 100 ),2),'%') AS porcentaje_beneficio,  servicios.nombre_servicios,  beneficios.nombre_beneficios,
            evento.fecha_evento,
            reg_partidas.monto_partidas,
            sum(reg_partidas.monto_partidas) as programa_presupuesto_ejercido_fechas  
            FROM programa
            INNER JOIN componente on componente.com_id_programa = programa.id_programa
            INNER JOIN tipo_apoyo on tipo_apoyo.ta_id_componente = componente.id_componente
            INNER JOIN evento on evento.id_accion_evento = tipo_apoyo.id_tipo_apoyo
            INNER JOIN reg_partidas on reg_partidas.id_evento_partidas = evento.id_evento  
            INNER JOIN beneficios ON beneficios.id_beneficios = tipo_apoyo.meta_um_beneficio 
            INNER JOIN servicios ON servicios.id_servicios = tipo_apoyo.meta_um_servicio
            where programa.id_programa=$id_usuario_programa  and componente.id_componente='$id_componente' and (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento<=  ' + $f_final + ') group by id_tipo_apoyo ORDER BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo";
            $tabla1_exportar = $mysqli->query($tabla);
            $tabla1 = $mysqli->query($tabla);
            $tabla_rows = array();
            $tabla_table = array();

            $tabla_table['cols']= array(
                array('label'=>'Componente', 'type'=>'string'),
                array('label'=>'Actividad', 'type'=>'string'),
                array('label'=>'Monto ejercido', 'type'=>'number'),
                array('label'=>'U.M. Servicios', 'type'=>'string'),
                array('label'=>'Meta', 'type'=>'number'),
                array('label'=>'Avance', 'type'=>'number'),
                array('label'=>'%', 'type'=>'string'),
                array('label'=>'U.M. Beneficio', 'type'=>'string'),
                array('label'=>'Meta', 'type'=>'number'),
                array('label'=>'Avance', 'type'=>'number'),
                array('label'=>'%', 'type'=>'string'),

            );
                foreach($tabla1 as $b){
                    $tabla_temp = array();
                    $tabla_temp[] = array('v'=>(string) $b['nombre_componente']);
                    $tabla_temp[] = array('v'=>(string) $b['nombre_tipo_apoyo']);
                    $tabla_temp[] = array('v'=>(integer) $b['programa_presupuesto_ejercido_fechas']);
                    $tabla_temp[] = array('v'=>(string) $b['nombre_servicios']);
                    $tabla_temp[] = array('v'=>(integer) $b['meta_cantidad_servicio']);
                    $tabla_temp[] = array('v'=>(integer) $b['meta_alcanzada_cantidad_servicio']);
                    $tabla_temp[] = array('v'=>(string) $b['porcentaje_servicio']);
                    $tabla_temp[] = array('v'=>(string) $b['nombre_beneficios']);
                    $tabla_temp[] = array('v'=>(integer) $b['meta_cantidad_beneficio']);
                    $tabla_temp[] = array('v'=>(integer) $b['meta_alcanzada_cantidad_beneficio']);
                    $tabla_temp[] = array('v'=>(string) $b['porcentaje_beneficio']);
                    $tabla_rows[] = array ('c'=>$tabla_temp);
                }
                $tabla_table['rows']=$tabla_rows;
                $tablajsonTable =json_encode($tabla_table);   //GRAFICO 12  

                ////////////////Desglose de eventos realizados

                $tabla_evento = "SELECT 
                evento.id_evento,
                evento.nombre_evento,
                evento.pob_obj_evento,
                evento.nivel_geo_evento,
                evento.lugar_evento,
                evento.monto_evento,
                evento.fecha_evento,
                evento.fecha_modificacion_evento,
                evento.usuario_evento,
                evento.id_accion_evento,
                evento.cantidad_servicio,
                evento.cantidad_beneficio,
                evento.hombres, 
                evento.mujeres,
                region.region,
                municipio.municipio,
                localidad.localidad, 
                tipo_apoyo.nombre_tipo_apoyo,
                servicios.nombre_servicios,
                beneficios.nombre_beneficios,
                componente.nombre_componente,
                COUNT(evento.hombres) AS tipo_apoyoA,       
                concat(round((evento.hombres/cantidad_beneficio * 100 ),2),'%') AS porcentaje_hombres,
                COUNT(evento.mujeres) AS tipo_apoyoA,       
                concat(round((evento.mujeres/cantidad_beneficio * 100 ),2),'%') AS porcentaje_mujeres  
                FROM
                evento
                INNER JOIN
                localidad ON localidad.cve_localidad = evento.lugar_evento
                    INNER JOIN
                municipio ON municipio.cve_municipios = localidad.l_cve_municipio
                    INNER JOIN
                region ON region.cve_regiones = municipio.m_cve_region
                    INNER JOIN
                usuarios ON usuarios.id = evento.usuario_evento
                    INNER JOIN
                tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
                    INNER JOIN
                componente ON componente.id_componente = tipo_apoyo.ta_id_componente
                    INNER JOIN
                programa ON componente.com_id_programa = programa.id_programa  
                INNER JOIN
                beneficios ON beneficios.id_beneficios = tipo_apoyo.meta_um_beneficio
                        INNER JOIN
                servicios ON servicios.id_servicios = tipo_apoyo.meta_um_servicio
                where programa.id_programa=$id_usuario_programa  and componente.id_componente='$id_componente' and (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento<=  ' + $f_final + ')
                group by evento.id_evento
                ORDER BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento";

                $tabla1_evento_exportar = $mysqli->query($tabla_evento);
                $tabla1_evento = $mysqli->query($tabla_evento);
                $tabla_evento_rows = array();
                $tabla_evento_table = array();

                $tabla_evento_table['cols']= array(
                    array('label'=>'Componente', 'type'=>'string'),
                    array('label'=>'Actividad', 'type'=>'string'),
                    array('label'=>'Evento', 'type'=>'string'),
                    array('label'=>'Monto ejercido', 'type'=>'number'),
                    array('label'=>'Fecha', 'type'=>'string'),
                    array('label'=>'U.M. Servicios', 'type'=>'string'),
                    array('label'=>'Meta alcanzada', 'type'=>'number'),
                    array('label'=>'U.M. Beneficio', 'type'=>'string'),
                    array('label'=>'Meta alcanzada', 'type'=>'number'),
                    array('label'=>'Hombres', 'type'=>'number'),
                    array('label'=>'%', 'type'=>'string'),
                    array('label'=>'Mujeres', 'type'=>'number'),
                    array('label'=>'%', 'type'=>'string'),             

                );
                    foreach($tabla1_evento as $b){
                        $tabla_evento_temp = array();
                        $tabla_evento_temp[] = array('v'=>(string) $b['nombre_componente']);
                        $tabla_evento_temp[] = array('v'=>(string) $b['nombre_tipo_apoyo']);
                        $tabla_evento_temp[] = array('v'=>(string) $b['nombre_evento']);
                        $tabla_evento_temp[] = array('v'=>(integer) $b['monto_evento']);
                        $tabla_evento_temp[] = array('v'=>(string) $b['fecha_evento']);
                        $tabla_evento_temp[] = array('v'=>(string) $b['nombre_servicios']);                    
                        $tabla_evento_temp[] = array('v'=>(integer) $b['cantidad_servicio']);
                        $tabla_evento_temp[] = array('v'=>(string) $b['nombre_beneficios']);
                        $tabla_evento_temp[] = array('v'=>(integer) $b['cantidad_beneficio']);
                        $tabla_evento_temp[] = array('v'=>(integer) $b['hombres']);
                        $tabla_evento_temp[] = array('v'=>(string) $b['porcentaje_hombres']);
                        $tabla_evento_temp[] = array('v'=>(integer) $b['mujeres']);
                        $tabla_evento_temp[] = array('v'=>(string) $b['porcentaje_mujeres']);
                        $tabla_evento_rows[] = array ('c'=>$tabla_evento_temp);
                    }
                    $tabla_evento_table['rows']=$tabla_evento_rows;
                    $tabla_evento_jsonTable =json_encode($tabla_evento_table);  //GRAFICO 15


            //-----GRÁFICO POR OBJETO DEL GASTO
                    //////////
                        $pastel = "SELECT reg_partidas.id_reg_partidas, reg_partidas.partida, reg_partidas.cantidad_partidas, reg_partidas.monto_partidas, reg_partidas.id_evento_partidas,
                        sum(reg_partidas.monto_partidas) as totalsuma,
                        objeto_de_gasto.partida_gasto_texto,
                        evento.fecha_evento,
                        reg_partidas.monto_partidas,
                        sum(reg_partidas.monto_partidas) as programa_presupuesto_ejercido_fechas
                        FROM reg_partidas
                        INNER JOIN evento on evento.id_evento = reg_partidas.id_evento_partidas
                        INNER JOIN tipo_apoyo on tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
                        INNER JOIN componente on componente.id_componente = tipo_apoyo.ta_id_componente
                        INNER JOIN programa on programa.id_programa = componente.com_id_programa
                        INNER JOIN objeto_de_gasto on objeto_de_gasto.clave = reg_partidas.partida
                        where programa.id_programa=$id_usuario_programa  and componente.id_componente='$id_componente' and (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento<=  ' + $f_final + ')
                        group by reg_partidas.partida";

                        $pastel1 = $mysqli->query($pastel);
                        $pastel_rows = array();
                        $pastel_table9 = array();

                        $pastel_table9['cols']= array(
                            array('label'=>'Objeto de gasto', 'type'=>'string'),
                            array('label'=>'Presupuesto ejercido', 'type'=>'number')

                        );
                            foreach($pastel1 as $p){
                                $pastel_temp = array();
                                $pastel_temp[] = array('v'=>(string) $p['partida_gasto_texto']);
                                $pastel_temp[] = array('v'=>(integer) $p['totalsuma']);
                                $pastel_rows[] = array ('c'=>$pastel_temp);
                            }
                            $pastel_table9['rows']=$pastel_rows;
                            $pasteljsonTable9 =json_encode($pastel_table9);  //GRAFICO 18

                        //////////
        ////////////////Tabla Presupuesto ejercido a nivel de objeto de gasto (partidas y monto ejercido)

            $tabla_partida = "SELECT 
            id_reg_partidas,
            partida,
            cantidad_partidas,
            monto_partidas,
            id_evento_partidas,
            partida_gasto_texto,
            evento.nombre_evento,
            programa.nombre_programa,
            componente.nombre_componente,
            tipo_apoyo.nombre_tipo_apoyo,
            capitulo,
            evento.fecha_evento,
            reg_partidas.monto_partidas,
            sum(reg_partidas.monto_partidas) as programa_presupuesto_ejercido_fechas
        FROM
            reg_partidas
                INNER JOIN
            objeto_de_gasto ON clave = partida
            INNER JOIN 
            evento ON evento.id_evento = reg_partidas.id_evento_partidas
            INNER JOIN 
            tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
            INNER JOIN 
            componente ON componente.id_componente = tipo_apoyo.ta_id_componente
            INNER JOIN
            programa ON programa.id_programa = componente.com_id_programa
            WHERE
            programa.id_programa=$id_usuario_programa  and componente.id_componente='$id_componente' and (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento<=  ' + $f_final + ')
            group by reg_partidas.id_reg_partidas
            ORDER BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento, reg_partidas.id_reg_partidas";
            $tabla1_partida_exportar = $mysqli->query($tabla_partida);
            $tabla1_partida = $mysqli->query($tabla_partida);
            $tabla_partida_rows = array();
            $tabla_partida_table = array();

            $tabla_partida_table['cols']= array(
                array('label'=>'Programa', 'type'=>'string'),
                array('label'=>'Componente', 'type'=>'string'),
                array('label'=>'Actividad', 'type'=>'string'),
                array('label'=>'Evento', 'type'=>'string'),
                array('label'=>'Capítulo', 'type'=>'number'),
                array('label'=>'Partida', 'type'=>'string'),
                array('label'=>'Monto', 'type'=>'number'),

            );
                foreach($tabla1_partida as $b){
                    $tabla_partida_temp = array();
                    $tabla_partida_temp[] = array('v'=>(string) $b['nombre_programa']);
                    $tabla_partida_temp[] = array('v'=>(string) $b['nombre_componente']);
                    $tabla_partida_temp[] = array('v'=>(string) $b['nombre_tipo_apoyo']);
                    $tabla_partida_temp[] = array('v'=>(string) $b['nombre_evento']);
                    $tabla_partida_temp[] = array('v'=>(integer) $b['capitulo']);
                    $tabla_partida_temp[] = array('v'=>(string) $b['partida_gasto_texto']);
                    $tabla_partida_temp[] = array('v'=>(integer) $b['monto_partidas']);
                    $tabla_partida_rows[] = array ('c'=>$tabla_partida_temp);
                }
                $tabla_partida_table['rows']=$tabla_partida_rows;
                $tabla_partida_jsonTable =json_encode($tabla_partida_table);  //GRAFICO 19

                //-----GRÁFICO POR OBJETO DEL GASTO CAPÍTULO
                    //////////
                    $pastel = "SELECT capitulo,  evento.fecha_evento, SUM(monto_partidas) AS capitulo_total
                    FROM reg_partidas
                    INNER JOIN evento on evento.id_evento = reg_partidas.id_evento_partidas
                    INNER JOIN tipo_apoyo on tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
                    INNER JOIN componente on componente.id_componente = tipo_apoyo.ta_id_componente
                    INNER JOIN programa on programa.id_programa = componente.com_id_programa
                    INNER JOIN objeto_de_gasto on objeto_de_gasto.clave = reg_partidas.partida
                    where programa.id_programa=$id_usuario_programa  and componente.id_componente='$id_componente' and (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento<=  ' + $f_final + ')
                    group by capitulo";

                    $pastel1 = $mysqli->query($pastel);
                    $pastel_rows = array();
                    $pastel_table10 = array();

                    $pastel_table10['cols']= array(
                        array('label'=>'Capítulo', 'type'=>'string'),
                        array('label'=>'Presupuesto ejercido', 'type'=>'number')

                    );
                        foreach($pastel1 as $p){
                            $pastel_temp = array();
                            $pastel_temp[] = array('v'=>(integer) $p['capitulo']);
                            $pastel_temp[] = array('v'=>(integer) $p['capitulo_total']);
                            $pastel_rows[] = array ('c'=>$pastel_temp);
                        }
                        $pastel_table10['rows']=$pastel_rows;
                        $pasteljsonTable10 =json_encode($pastel_table10);  //GRAFICO 16

                    //////////

                    ////////////////Tabla Presupuesto ejercido a nivel de objeto de gasto (capitulo y monto ejercido)

                    $tabla_capitulo = "SELECT 
                    programa.nombre_programa,
                    componente.nombre_componente,
                    tipo_apoyo.nombre_tipo_apoyo,
                    evento.nombre_evento,
                    capitulo,
                    evento.fecha_evento,
                    SUM(monto_partidas) AS capitulo_total
                FROM
                    reg_partidas
                        INNER JOIN
                    objeto_de_gasto ON clave = partida
                    INNER JOIN 
                    evento ON evento.id_evento = reg_partidas.id_evento_partidas
                    INNER JOIN 
                    tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
                    INNER JOIN 
                    componente ON componente.id_componente = tipo_apoyo.ta_id_componente
                    INNER JOIN
                    programa ON programa.id_programa = componente.com_id_programa
                    WHERE
                    programa.id_programa=$id_usuario_programa  and componente.id_componente='$id_componente' and (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento<=  ' + $f_final + ')
                    GROUP BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento, capitulo
                    ORDER BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento, capitulo";
                    $tabla1_capitulo_exportar = $mysqli->query($tabla_capitulo);
                    $tabla1_capitulo = $mysqli->query($tabla_capitulo);
                    $tabla_capitulo_rows = array();
                    $tabla_capitulo_table = array();

                    $tabla_capitulo_table['cols']= array(
                        array('label'=>'Programa', 'type'=>'string'),
                        array('label'=>'Componente', 'type'=>'string'),
                        array('label'=>'Actividad', 'type'=>'string'),
                        array('label'=>'Evento', 'type'=>'string'),
                        array('label'=>'Capítulo', 'type'=>'number'),
                        array('label'=>'Monto', 'type'=>'number'),

                    );
                        foreach($tabla1_capitulo as $b){
                            $tabla_capitulo_temp = array();
                            $tabla_capitulo_temp[] = array('v'=>(string) $b['nombre_programa']);
                            $tabla_capitulo_temp[] = array('v'=>(string) $b['nombre_componente']);
                            $tabla_capitulo_temp[] = array('v'=>(string) $b['nombre_tipo_apoyo']);
                            $tabla_capitulo_temp[] = array('v'=>(string) $b['nombre_evento']);
                            $tabla_capitulo_temp[] = array('v'=>(integer) $b['capitulo']);
                            $tabla_capitulo_temp[] = array('v'=>(integer) $b['capitulo_total']);
                            $tabla_capitulo_rows[] = array ('c'=>$tabla_capitulo_temp);
                        }
                        $tabla_capitulo_table['rows']=$tabla_capitulo_rows;
                        $tabla_capitulo_jsonTable =json_encode($tabla_capitulo_table);  //GRAFICO 17 

////RESUMEN POR TIPO DE ACCIONES (TRANSVERSALIDAD, ACCIONES, PREVENCION)

$tabla_acciones = "SELECT programa.nombre_programa, tipo_apoyo.tipo_accion, sum(evento.cantidad_beneficio) as personas_atendidas, sum(evento.hombres) as hombres, sum(evento.mujeres) as mujeres,
sum(evento.monto_evento)as inversion_ejercido,
count(tipo_apoyo.tipo_accion) as acciones
FROM
    tipo_apoyo 
INNER JOIN
    componente ON componente.id_componente = tipo_apoyo.ta_id_componente
INNER JOIN
    programa ON componente.com_id_programa = programa.id_programa
INNER JOIN  
    evento ON evento.id_accion_evento = tipo_apoyo.id_tipo_apoyo   
WHERE programa.id_programa = $id_usuario_programa  and componente.id_componente='$id_componente' AND  (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento <=  ' + $f_final + ')
group by  programa.id_programa, tipo_accion
order by  tipo_accion";
$tabla_acciones1_exportar = $mysqli->query($tabla_acciones);
$tabla_acciones1 = $mysqli->query($tabla_acciones);
$tabla_acciones_rows = array();
$tabla_acciones_table = array();

$tabla_acciones_table['cols']= array(
    array('label'=>'Programa', 'type'=>'string'),
    array('label'=>'Tipo de Acción', 'type'=>'string'),
    array('label'=>'Personas Atendidas', 'type'=>'number'),
	array('label'=>'Hombres', 'type'=>'number'),
	array('label'=>'Mujeres', 'type'=>'number'),
    array('label'=>'Monto ejercido', 'type'=>'number'),  
    array('label'=>'Número de acciones', 'type'=>'number'),

);
    foreach($tabla_acciones1 as $b){
        $tabla_temp = array();
        $tabla_temp[] = array('v'=>(string) $b['nombre_programa']);
        $tabla_temp[] = array('v'=>(string) $b['tipo_accion']);
        $tabla_temp[] = array('v'=>(integer) $b['personas_atendidas']);
		$tabla_temp[] = array('v'=>(integer) $b['hombres']);
		$tabla_temp[] = array('v'=>(integer) $b['mujeres']);
        $tabla_temp[] = array('v'=>(integer) $b['inversion_ejercido']);
        $tabla_temp[] = array('v'=>(integer) $b['acciones']);
        $tabla_acciones_rows[] = array ('c'=>$tabla_temp);
    }
    $tabla_acciones_table['rows']=$tabla_acciones_rows;
    $tabla_accionesjsonTable =json_encode($tabla_acciones_table);  //GRAFICO 13  
////  

////DESGLOSE POR TIPO DE ACCIONES (TRANSVERSALIDAD, ACCIONES, PREVENCION)

$tabla_acciones = "SELECT programa.nombre_programa, componente.nombre_componente, tipo_apoyo.tipo_accion, evento.nombre_evento, sum(evento.cantidad_beneficio) as personas_atendidas, sum(evento.hombres) as hombres, sum(evento.mujeres) as mujeres,
sum(evento.monto_evento)as inversion_ejercido
FROM
	tipo_apoyo 
INNER JOIN
	componente ON componente.id_componente = tipo_apoyo.ta_id_componente
INNER JOIN
	programa ON componente.com_id_programa = programa.id_programa
INNER JOIN  
	evento ON evento.id_accion_evento = tipo_apoyo.id_tipo_apoyo   
WHERE programa.id_programa = $id_usuario_programa  and componente.id_componente='$id_componente' AND  (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento <=  ' + $f_final + ')
group by programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento, evento.fecha_evento, tipo_accion
order by  tipo_accion";
$tabla_acciones1_desglose_exportar = $mysqli->query($tabla_acciones);
$tabla_acciones1 = $mysqli->query($tabla_acciones);
$tabla_acciones_rows = array();
$tabla_acciones_table = array();

$tabla_acciones_table['cols']= array(
    array('label'=>'Programa', 'type'=>'string'),
    array('label'=>'Componente', 'type'=>'string'),
    array('label'=>'Tipo de Acción', 'type'=>'string'),
    array('label'=>'Evento', 'type'=>'string'),
    array('label'=>'Personas Atendidas', 'type'=>'number'),
	array('label'=>'Hombres', 'type'=>'number'),
	array('label'=>'Mujeres', 'type'=>'number'),
    array('label'=>'Monto ejercido', 'type'=>'number'),  
);
    foreach($tabla_acciones1 as $b){
        $tabla_temp = array();    
        $tabla_temp[] = array('v'=>(string) $b['nombre_programa']);
        $tabla_temp[] = array('v'=>(string) $b['nombre_componente']);
        $tabla_temp[] = array('v'=>(string) $b['tipo_accion']);
        $tabla_temp[] = array('v'=>(string) $b['nombre_evento']);
        $tabla_temp[] = array('v'=>(integer) $b['personas_atendidas']);
		$tabla_temp[] = array('v'=>(integer) $b['hombres']);
		$tabla_temp[] = array('v'=>(integer) $b['mujeres']);
        $tabla_temp[] = array('v'=>(integer) $b['inversion_ejercido']);

        $tabla_acciones_rows[] = array ('c'=>$tabla_temp);
    }
    $tabla_acciones_table['rows']=$tabla_acciones_rows;
    $tabla_acciones_desglosejsonTable =json_encode($tabla_acciones_table);   //GRAFICO 14    
///////INICIO REGION, MUNICIPIO, GENERO ---EVENTO
//-----GRÁFICO POR REGION GRAFICO PAY

    $pastel = "SELECT  region.region, COUNT(region.region) AS Total_Cuenta 
    FROM
    evento
    INNER JOIN localidad ON localidad.cve_localidad = evento.lugar_evento
    INNER JOIN municipio ON municipio.cve_municipios = localidad.l_cve_municipio
    INNER JOIN region ON region.cve_regiones = municipio.m_cve_region
    INNER JOIN tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
    INNER JOIN componente ON componente.id_componente = tipo_apoyo.ta_id_componente
    INNER JOIN programa ON programa.id_programa = componente.com_id_programa
    WHERE programa.id_programa = $id_usuario_programa   and componente.id_componente='$id_componente' 
    AND  (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento <=  ' + $f_final + ')  
    GROUP BY region.region ORDER BY  region.region  ASC";


    $pastel1 = $mysqli->query($pastel);
    $pastel_rows = array();
    $pastel_table1evento = array();

    $pastel_table1evento['cols']= array(
        array('label'=>'Región', 'type'=>'string'),
        array('label'=>'Cantidad', 'type'=>'number')

    );
        foreach($pastel1 as $p){
            $pastel_temp = array();
            $pastel_temp[] = array('v'=>(string) $p['region']);
            $pastel_temp[] = array('v'=>(integer) $p['Total_Cuenta']);
            $pastel_rows[] = array ('c'=>$pastel_temp);
        }
        $pastel_table1evento['rows']=$pastel_rows;
        $pasteljsonTable1evento =json_encode($pastel_table1evento);

//////////

//-----GRÁFICO POR MUNICIPIO -GRÁFICO PAY

        ///////////

            $pastel = "SELECT  municipio.municipio, COUNT(municipio.municipio) AS Total_Cuenta 
            FROM
            evento 
	        INNER JOIN localidad ON localidad.cve_localidad = evento.lugar_evento
            INNER JOIN municipio ON municipio.cve_municipios = localidad.l_cve_municipio
            INNER JOIN region ON region.cve_regiones = municipio.m_cve_region
            INNER JOIN tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
            INNER JOIN componente ON componente.id_componente = tipo_apoyo.ta_id_componente
            INNER JOIN programa ON programa.id_programa = componente.com_id_programa
            WHERE programa.id_programa = $id_usuario_programa   and componente.id_componente='$id_componente' 
            AND  (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento <=  ' + $f_final + ')   
            GROUP BY municipio.municipio ORDER BY  municipio.municipio  ASC";

            $tabla_localidades = "SELECT programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento, evento.fecha_evento, region.region, municipio.municipio, localidad.localidad, localidad.latitud_dec, localidad.longitud_dec, COUNT(localidad.localidad) AS Total_Cuenta 
            FROM
            evento 
	        INNER JOIN localidad ON localidad.cve_localidad = evento.lugar_evento
            INNER JOIN municipio ON municipio.cve_municipios = localidad.l_cve_municipio
            INNER JOIN region ON region.cve_regiones = municipio.m_cve_region
            INNER JOIN tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
            INNER JOIN componente ON componente.id_componente = tipo_apoyo.ta_id_componente
            INNER JOIN programa ON programa.id_programa = componente.com_id_programa
            WHERE programa.id_programa = $id_usuario_programa   and componente.id_componente='$id_componente' 
            AND  (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento <=  ' + $f_final + ')   
            GROUP BY localidad.localidad ORDER BY  localidad.localidad  ASC";

        $tablalocexportar = $mysqli->query($tabla_localidades);
        $pastel1 = $mysqli->query($pastel);
        $pastel_rows = array();
        $pastel_table2evento = array();

        $pastel_table2evento['cols']= array(
            array('label'=>'Municipio %', 'type'=>'string'),
            array('label'=>'Cantidad', 'type'=>'number')

        );
            foreach($pastel1 as $p){
                $pastel_temp = array();
                $pastel_temp[] = array('v'=>(string) $p['municipio']);
                $pastel_temp[] = array('v'=>(integer) $p['Total_Cuenta']);
                $pastel_rows[] = array ('c'=>$pastel_temp);
            }
            $pastel_table2evento['rows']=$pastel_rows;
            $pasteljsonTable2evento =json_encode($pastel_table2evento);

        //////////
//-----GRÁFICO POR GÉNERO
				$sqlh="UPDATE hombre_mujer SET hombre_mujer.cantidad_h_m =( SELECT SUM( evento.hombres) AS Total_CuentaH
                FROM
                evento 
	            INNER JOIN localidad ON localidad.cve_localidad = evento.lugar_evento
                INNER JOIN municipio ON municipio.cve_municipios = localidad.l_cve_municipio
                INNER JOIN region ON region.cve_regiones = municipio.m_cve_region
                INNER JOIN tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
                INNER JOIN componente ON componente.id_componente = tipo_apoyo.ta_id_componente
                INNER JOIN programa ON programa.id_programa = componente.com_id_programa
                WHERE programa.id_programa = $id_usuario_programa  and componente.id_componente='$id_componente' 
                AND  (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento <=  ' + $f_final + ') )
				WHERE hombre_mujer.h_m ='HOMBRE'";
				$mysqli->query($sqlh);
				
				$sqlm="UPDATE hombre_mujer SET hombre_mujer.cantidad_h_m =( SELECT SUM( evento.mujeres) AS Total_CuentaM 
                FROM
                evento 
	            INNER JOIN localidad ON localidad.cve_localidad = evento.lugar_evento
                INNER JOIN municipio ON municipio.cve_municipios = localidad.l_cve_municipio
                INNER JOIN region ON region.cve_regiones = municipio.m_cve_region
                INNER JOIN tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
                INNER JOIN componente ON componente.id_componente = tipo_apoyo.ta_id_componente
                INNER JOIN programa ON programa.id_programa = componente.com_id_programa
                WHERE programa.id_programa = $id_usuario_programa  and componente.id_componente='$id_componente' 
                AND  (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento <=  ' + $f_final + ') )
				WHERE hombre_mujer.h_m ='MUJER'";
				$mysqli->query($sqlm);
            //////////
				$pastel = "SELECT h_m, cantidad_h_m FROM hombre_mujer";
                $pastel1 = $mysqli->query($pastel);
                $pastel_rows = array();
                $pastel_table3evento = array();

                $pastel_table3evento['cols']= array(
                    array('label'=>'Género', 'type'=>'string'),
                    array('label'=>'Cantidad', 'type'=>'number')

                );
                    foreach($pastel1 as $p){
                        $pastel_temp = array();
                        $pastel_temp[] = array('v'=>(string) $p['h_m']);
                        $pastel_temp[] = array('v'=>(integer) $p['cantidad_h_m']);
                        $pastel_rows[] = array ('c'=>$pastel_temp);
                    }
                    $pastel_table3evento['rows']=$pastel_rows;
                    $pasteljsonTable3evento =json_encode($pastel_table3evento);

                
///////FIN REGION, MUNICIPIO, GENERO --- EVENTO
//// SI NO  SELECCIONA PROGRAMA, COMPONENTE Y FECHAS LAS FECHAS
//*SEXTA CONSULTA 2 VARIABLES
}// SI SELECCIONA PROGRAMA, COMPONENTE
elseif((!empty($_GET['programa'])) && (!empty($_GET['componente'])) ){
        $id_usuario_programa =($_GET['programa']);
        $id_componente =($_GET['componente']);
    
        $sqlProgramas = "SELECT 
        programa.id_programa,    
        nombre_programa,
        componente.id_componente,
        componente.nombre_componente
        FROM
        programa
        INNER JOIN componente on componente.com_id_programa = programa.id_programa
        WHERE programa.id_programa  = $id_usuario_programa 
        AND componente.id_componente = $id_componente ";

        $programas = $mysqli->query($sqlProgramas);
        $row_programa  = $programas->fetch_assoc();
        $_programa=$row_programa ['nombre_programa'];
        $_nombre_componente=$row_programa ['nombre_componente'];

        //-----GRÁFICO PRESUPUESTO EJERCIDO POR ACTIVIDAD
        //////////
            $pastel = "SELECT tipo_apoyo.id_tipo_apoyo, tipo_apoyo.nombre_tipo_apoyo, tipo_apoyo.ta_id_componente, tipo_apoyo.meta_um_servicio, tipo_apoyo.meta_um_beneficio, tipo_apoyo.meta_cantidad_servicio, tipo_apoyo.meta_cantidad_beneficio, tipo_apoyo.meta_alcanzada_cantidad_servicio, tipo_apoyo.meta_alcanzada_cantidad_beneficio, tipo_apoyo.tipo_apoyo_presupuesto_ejercido, componente.nombre_componente, programa.nombre_programa, programa.id_programa, evento.fecha_evento,
            reg_partidas.monto_partidas, sum(reg_partidas.monto_partidas) as programa_presupuesto_ejercido_fechas  
            FROM  programa
            INNER JOIN componente on componente.com_id_programa = programa.id_programa
            INNER JOIN tipo_apoyo on tipo_apoyo.ta_id_componente = componente.id_componente
            INNER JOIN evento on evento.id_accion_evento = tipo_apoyo.id_tipo_apoyo
            INNER JOIN reg_partidas on reg_partidas.id_evento_partidas = evento.id_evento  
            where programa.id_programa=$id_usuario_programa  and componente.id_componente='$id_componente'  ORDER BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo";

        $pastel1 = $mysqli->query($pastel);
        $pastel_rows = array();
        $pastel_table6 = array();

        $pastel_table6['cols']= array(
            array('label'=>'Actividad', 'type'=>'string'),
            array('label'=>'Monto', 'type'=>'number')

        );
            foreach($pastel1 as $p){
                $pastel_temp = array();
                $pastel_temp[] = array('v'=>(string) $p['nombre_tipo_apoyo']);
                $pastel_temp[] = array('v'=>(integer) $p['programa_presupuesto_ejercido_fechas']);
                $pastel_rows[] = array ('c'=>$pastel_temp);
            }
            $pastel_table6['rows']=$pastel_rows;
            $pasteljsonTable6 =json_encode($pastel_table6);  //GRAFICO 11

        //////////Metas programadas vs Metas alcanzadas por Actividad
            $tabla = "SELECT tipo_apoyo.id_tipo_apoyo, tipo_apoyo.nombre_tipo_apoyo, tipo_apoyo.ta_id_componente, tipo_apoyo.meta_um_servicio, tipo_apoyo.meta_um_beneficio, tipo_apoyo.meta_cantidad_servicio, tipo_apoyo.meta_cantidad_beneficio, tipo_apoyo.meta_alcanzada_cantidad_servicio, tipo_apoyo.meta_alcanzada_cantidad_beneficio, tipo_apoyo.tipo_apoyo_presupuesto_ejercido, componente.nombre_componente, programa.nombre_programa, programa.id_programa,COUNT( meta_alcanzada_cantidad_servicio ) AS tipo_apoyoA,
            concat(round(( meta_alcanzada_cantidad_servicio/meta_cantidad_servicio * 100 ),2),'%') AS porcentaje_servicio,
            COUNT( meta_alcanzada_cantidad_beneficio ) AS tipo_apoyoA,
            concat(round(( meta_alcanzada_cantidad_beneficio/meta_cantidad_beneficio * 100 ),2),'%') AS porcentaje_beneficio,  servicios.nombre_servicios,  beneficios.nombre_beneficios,
            evento.fecha_evento,
            reg_partidas.monto_partidas,
            sum(reg_partidas.monto_partidas) as programa_presupuesto_ejercido_fechas  
            FROM programa
            INNER JOIN componente on componente.com_id_programa = programa.id_programa
            INNER JOIN tipo_apoyo on tipo_apoyo.ta_id_componente = componente.id_componente
            INNER JOIN evento on evento.id_accion_evento = tipo_apoyo.id_tipo_apoyo
            INNER JOIN reg_partidas on reg_partidas.id_evento_partidas = evento.id_evento  
            INNER JOIN beneficios ON beneficios.id_beneficios = tipo_apoyo.meta_um_beneficio 
            INNER JOIN servicios ON servicios.id_servicios = tipo_apoyo.meta_um_servicio
            where programa.id_programa=$id_usuario_programa  and componente.id_componente='$id_componente' group by id_tipo_apoyo ORDER BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo";
            $tabla1_exportar = $mysqli->query($tabla);
            $tabla1 = $mysqli->query($tabla);
            $tabla_rows = array();
            $tabla_table = array();

            $tabla_table['cols']= array(
                array('label'=>'Componente', 'type'=>'string'),
                array('label'=>'Actividad', 'type'=>'string'),
                array('label'=>'Monto ejercido', 'type'=>'number'),
                array('label'=>'U.M. Servicios', 'type'=>'string'),
                array('label'=>'Meta', 'type'=>'number'),
                array('label'=>'Avance', 'type'=>'number'),
                array('label'=>'%', 'type'=>'string'),
                array('label'=>'U.M. Beneficio', 'type'=>'string'),
                array('label'=>'Meta', 'type'=>'number'),
                array('label'=>'Avance', 'type'=>'number'),
                array('label'=>'%', 'type'=>'string'),

            );
                foreach($tabla1 as $b){
                    $tabla_temp = array();
                    $tabla_temp[] = array('v'=>(string) $b['nombre_componente']);
                    $tabla_temp[] = array('v'=>(string) $b['nombre_tipo_apoyo']);
                    $tabla_temp[] = array('v'=>(integer) $b['programa_presupuesto_ejercido_fechas']);
                    $tabla_temp[] = array('v'=>(string) $b['nombre_servicios']);
                    $tabla_temp[] = array('v'=>(integer) $b['meta_cantidad_servicio']);
                    $tabla_temp[] = array('v'=>(integer) $b['meta_alcanzada_cantidad_servicio']);
                    $tabla_temp[] = array('v'=>(string) $b['porcentaje_servicio']);
                    $tabla_temp[] = array('v'=>(string) $b['nombre_beneficios']);
                    $tabla_temp[] = array('v'=>(integer) $b['meta_cantidad_beneficio']);
                    $tabla_temp[] = array('v'=>(integer) $b['meta_alcanzada_cantidad_beneficio']);
                    $tabla_temp[] = array('v'=>(string) $b['porcentaje_beneficio']);
                    $tabla_rows[] = array ('c'=>$tabla_temp);
                }
                $tabla_table['rows']=$tabla_rows;
                $tablajsonTable =json_encode($tabla_table);   //GRAFICO 12  

                ////////////////Desglose de eventos realizados

                $tabla_evento = "SELECT 
                evento.id_evento,
                evento.nombre_evento,
                evento.pob_obj_evento,
                evento.nivel_geo_evento,
                evento.lugar_evento,
                evento.monto_evento,
                evento.fecha_evento,
                evento.fecha_modificacion_evento,
                evento.usuario_evento,
                evento.id_accion_evento,
                evento.cantidad_servicio,
                evento.cantidad_beneficio,
                evento.hombres, 
                evento.mujeres,
                region.region,
                municipio.municipio,
                localidad.localidad, 
                tipo_apoyo.nombre_tipo_apoyo,
                servicios.nombre_servicios,
                beneficios.nombre_beneficios,
                componente.nombre_componente,
                COUNT(evento.hombres) AS tipo_apoyoA,       
                concat(round((evento.hombres/cantidad_beneficio * 100 ),2),'%') AS porcentaje_hombres,
                COUNT(evento.mujeres) AS tipo_apoyoA,       
                concat(round((evento.mujeres/cantidad_beneficio * 100 ),2),'%') AS porcentaje_mujeres  
                FROM
                evento
                INNER JOIN
                localidad ON localidad.cve_localidad = evento.lugar_evento
                    INNER JOIN
                municipio ON municipio.cve_municipios = localidad.l_cve_municipio
                    INNER JOIN
                region ON region.cve_regiones = municipio.m_cve_region
                    INNER JOIN
                usuarios ON usuarios.id = evento.usuario_evento
                    INNER JOIN
                tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
                    INNER JOIN
                componente ON componente.id_componente = tipo_apoyo.ta_id_componente
                    INNER JOIN
                programa ON componente.com_id_programa = programa.id_programa  
                INNER JOIN
                beneficios ON beneficios.id_beneficios = tipo_apoyo.meta_um_beneficio
                        INNER JOIN
                servicios ON servicios.id_servicios = tipo_apoyo.meta_um_servicio
                where programa.id_programa=$id_usuario_programa  and componente.id_componente='$id_componente' 
                group by evento.id_evento
                ORDER BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento";

                $tabla1_evento_exportar = $mysqli->query($tabla_evento);
                $tabla1_evento = $mysqli->query($tabla_evento);
                $tabla_evento_rows = array();
                $tabla_evento_table = array();

                $tabla_evento_table['cols']= array(
                    array('label'=>'Componente', 'type'=>'string'),
                    array('label'=>'Actividad', 'type'=>'string'),
                    array('label'=>'Evento', 'type'=>'string'),
                    array('label'=>'Monto ejercido', 'type'=>'number'),
                    array('label'=>'Fecha', 'type'=>'string'),
                    array('label'=>'U.M. Servicios', 'type'=>'string'),
                    array('label'=>'Meta alcanzada', 'type'=>'number'),
                    array('label'=>'U.M. Beneficio', 'type'=>'string'),
                    array('label'=>'Meta alcanzada', 'type'=>'number'),
                    array('label'=>'Hombres', 'type'=>'number'),
                    array('label'=>'%', 'type'=>'string'),
                    array('label'=>'Mujeres', 'type'=>'number'),
                    array('label'=>'%', 'type'=>'string'),             

                );
                    foreach($tabla1_evento as $b){
                        $tabla_evento_temp = array();
                        $tabla_evento_temp[] = array('v'=>(string) $b['nombre_componente']);
                        $tabla_evento_temp[] = array('v'=>(string) $b['nombre_tipo_apoyo']);
                        $tabla_evento_temp[] = array('v'=>(string) $b['nombre_evento']);
                        $tabla_evento_temp[] = array('v'=>(integer) $b['monto_evento']);
                        $tabla_evento_temp[] = array('v'=>(string) $b['fecha_evento']);
                        $tabla_evento_temp[] = array('v'=>(string) $b['nombre_servicios']);                    
                        $tabla_evento_temp[] = array('v'=>(integer) $b['cantidad_servicio']);
                        $tabla_evento_temp[] = array('v'=>(string) $b['nombre_beneficios']);
                        $tabla_evento_temp[] = array('v'=>(integer) $b['cantidad_beneficio']);
                        $tabla_evento_temp[] = array('v'=>(integer) $b['hombres']);
                        $tabla_evento_temp[] = array('v'=>(string) $b['porcentaje_hombres']);
                        $tabla_evento_temp[] = array('v'=>(integer) $b['mujeres']);
                        $tabla_evento_temp[] = array('v'=>(string) $b['porcentaje_mujeres']);
                        $tabla_evento_rows[] = array ('c'=>$tabla_evento_temp);
                    }
                    $tabla_evento_table['rows']=$tabla_evento_rows;
                    $tabla_evento_jsonTable =json_encode($tabla_evento_table);  //GRAFICO 15


            //-----GRÁFICO POR OBJETO DEL GASTO
                    //////////
                        $pastel = "SELECT reg_partidas.id_reg_partidas, reg_partidas.partida, reg_partidas.cantidad_partidas, reg_partidas.monto_partidas, reg_partidas.id_evento_partidas,
                        sum(reg_partidas.monto_partidas) as totalsuma,
                        objeto_de_gasto.partida_gasto_texto,
                        evento.fecha_evento,
                        reg_partidas.monto_partidas,
                        sum(reg_partidas.monto_partidas) as programa_presupuesto_ejercido_fechas
                        FROM reg_partidas
                        INNER JOIN evento on evento.id_evento = reg_partidas.id_evento_partidas
                        INNER JOIN tipo_apoyo on tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
                        INNER JOIN componente on componente.id_componente = tipo_apoyo.ta_id_componente
                        INNER JOIN programa on programa.id_programa = componente.com_id_programa
                        INNER JOIN objeto_de_gasto on objeto_de_gasto.clave = reg_partidas.partida
                        where programa.id_programa=$id_usuario_programa  and componente.id_componente='$id_componente' 
                        group by reg_partidas.partida";

                        $pastel1 = $mysqli->query($pastel);
                        $pastel_rows = array();
                        $pastel_table9 = array();

                        $pastel_table9['cols']= array(
                            array('label'=>'Objeto de gasto', 'type'=>'string'),
                            array('label'=>'Presupuesto ejercido', 'type'=>'number')

                        );
                            foreach($pastel1 as $p){
                                $pastel_temp = array();
                                $pastel_temp[] = array('v'=>(string) $p['partida_gasto_texto']);
                                $pastel_temp[] = array('v'=>(integer) $p['totalsuma']);
                                $pastel_rows[] = array ('c'=>$pastel_temp);
                            }
                            $pastel_table9['rows']=$pastel_rows;
                            $pasteljsonTable9 =json_encode($pastel_table9);  //GRAFICO 18

                        //////////
        ////////////////Tabla Presupuesto ejercido a nivel de objeto de gasto (partidas y monto ejercido)

            $tabla_partida = "SELECT 
            id_reg_partidas,
            partida,
            cantidad_partidas,
            monto_partidas,
            id_evento_partidas,
            partida_gasto_texto,
            evento.nombre_evento,
            programa.nombre_programa,
            componente.nombre_componente,
            tipo_apoyo.nombre_tipo_apoyo,
            capitulo,
            evento.fecha_evento,
            reg_partidas.monto_partidas,
            sum(reg_partidas.monto_partidas) as programa_presupuesto_ejercido_fechas
        FROM
            reg_partidas
                INNER JOIN
            objeto_de_gasto ON clave = partida
            INNER JOIN 
            evento ON evento.id_evento = reg_partidas.id_evento_partidas
            INNER JOIN 
            tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
            INNER JOIN 
            componente ON componente.id_componente = tipo_apoyo.ta_id_componente
            INNER JOIN
            programa ON programa.id_programa = componente.com_id_programa
            WHERE
            programa.id_programa=$id_usuario_programa  and componente.id_componente='$id_componente'
            group by reg_partidas.id_reg_partidas
            ORDER BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento, reg_partidas.id_reg_partidas";
            $tabla1_partida_exportar = $mysqli->query($tabla_partida);
            $tabla1_partida = $mysqli->query($tabla_partida);
            $tabla_partida_rows = array();
            $tabla_partida_table = array();

            $tabla_partida_table['cols']= array(
                array('label'=>'Programa', 'type'=>'string'),
                array('label'=>'Componente', 'type'=>'string'),
                array('label'=>'Actividad', 'type'=>'string'),
                array('label'=>'Evento', 'type'=>'string'),
                array('label'=>'Capítulo', 'type'=>'number'),
                array('label'=>'Partida', 'type'=>'string'),
                array('label'=>'Monto', 'type'=>'number'),

            );
                foreach($tabla1_partida as $b){
                    $tabla_partida_temp = array();
                    $tabla_partida_temp[] = array('v'=>(string) $b['nombre_programa']);
                    $tabla_partida_temp[] = array('v'=>(string) $b['nombre_componente']);
                    $tabla_partida_temp[] = array('v'=>(string) $b['nombre_tipo_apoyo']);
                    $tabla_partida_temp[] = array('v'=>(string) $b['nombre_evento']);
                    $tabla_partida_temp[] = array('v'=>(integer) $b['capitulo']);
                    $tabla_partida_temp[] = array('v'=>(string) $b['partida_gasto_texto']);
                    $tabla_partida_temp[] = array('v'=>(integer) $b['monto_partidas']);
                    $tabla_partida_rows[] = array ('c'=>$tabla_partida_temp);
                }
                $tabla_partida_table['rows']=$tabla_partida_rows;
                $tabla_partida_jsonTable =json_encode($tabla_partida_table);  //GRAFICO 19

                //-----GRÁFICO POR OBJETO DEL GASTO CAPÍTULO
                    //////////
                    $pastel = "SELECT capitulo,  evento.fecha_evento, SUM(monto_partidas) AS capitulo_total
                    FROM reg_partidas
                    INNER JOIN evento on evento.id_evento = reg_partidas.id_evento_partidas
                    INNER JOIN tipo_apoyo on tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
                    INNER JOIN componente on componente.id_componente = tipo_apoyo.ta_id_componente
                    INNER JOIN programa on programa.id_programa = componente.com_id_programa
                    INNER JOIN objeto_de_gasto on objeto_de_gasto.clave = reg_partidas.partida
                    where programa.id_programa=$id_usuario_programa  and componente.id_componente='$id_componente' 
                    group by capitulo";

                    $pastel1 = $mysqli->query($pastel);
                    $pastel_rows = array();
                    $pastel_table10 = array();

                    $pastel_table10['cols']= array(
                        array('label'=>'Capítulo', 'type'=>'string'),
                        array('label'=>'Presupuesto ejercido', 'type'=>'number')

                    );
                        foreach($pastel1 as $p){
                            $pastel_temp = array();
                            $pastel_temp[] = array('v'=>(integer) $p['capitulo']);
                            $pastel_temp[] = array('v'=>(integer) $p['capitulo_total']);
                            $pastel_rows[] = array ('c'=>$pastel_temp);
                        }
                        $pastel_table10['rows']=$pastel_rows;
                        $pasteljsonTable10 =json_encode($pastel_table10);  //GRAFICO 16

                    //////////

                    ////////////////Tabla Presupuesto ejercido a nivel de objeto de gasto (capitulo y monto ejercido)

                    $tabla_capitulo = "SELECT 
                    programa.nombre_programa,
                    componente.nombre_componente,
                    tipo_apoyo.nombre_tipo_apoyo,
                    evento.nombre_evento,
                    capitulo,
                    evento.fecha_evento,
                    SUM(monto_partidas) AS capitulo_total
                FROM
                    reg_partidas
                        INNER JOIN
                    objeto_de_gasto ON clave = partida
                    INNER JOIN 
                    evento ON evento.id_evento = reg_partidas.id_evento_partidas
                    INNER JOIN 
                    tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
                    INNER JOIN 
                    componente ON componente.id_componente = tipo_apoyo.ta_id_componente
                    INNER JOIN
                    programa ON programa.id_programa = componente.com_id_programa
                    WHERE
                    programa.id_programa=$id_usuario_programa  and componente.id_componente='$id_componente' 
                    GROUP BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento, capitulo
                    ORDER BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento, capitulo";
                    $tabla1_capitulo_exportar = $mysqli->query($tabla_capitulo);
                    $tabla1_capitulo = $mysqli->query($tabla_capitulo);
                    $tabla_capitulo_rows = array();
                    $tabla_capitulo_table = array();

                    $tabla_capitulo_table['cols']= array(
                        array('label'=>'Programa', 'type'=>'string'),
                        array('label'=>'Componente', 'type'=>'string'),
                        array('label'=>'Actividad', 'type'=>'string'),
                        array('label'=>'Evento', 'type'=>'string'),
                        array('label'=>'Capítulo', 'type'=>'number'),
                        array('label'=>'Monto', 'type'=>'number'),

                    );
                        foreach($tabla1_capitulo as $b){
                            $tabla_capitulo_temp = array();
                            $tabla_capitulo_temp[] = array('v'=>(string) $b['nombre_programa']);
                            $tabla_capitulo_temp[] = array('v'=>(string) $b['nombre_componente']);
                            $tabla_capitulo_temp[] = array('v'=>(string) $b['nombre_tipo_apoyo']);
                            $tabla_capitulo_temp[] = array('v'=>(string) $b['nombre_evento']);
                            $tabla_capitulo_temp[] = array('v'=>(integer) $b['capitulo']);
                            $tabla_capitulo_temp[] = array('v'=>(integer) $b['capitulo_total']);
                            $tabla_capitulo_rows[] = array ('c'=>$tabla_capitulo_temp);
                        }
                        $tabla_capitulo_table['rows']=$tabla_capitulo_rows;
                        $tabla_capitulo_jsonTable =json_encode($tabla_capitulo_table);  //GRAFICO 17 

////RESUMEN POR TIPO DE ACCIONES (TRANSVERSALIDAD, ACCIONES, PREVENCION)

$tabla_acciones = "SELECT programa.nombre_programa, tipo_apoyo.tipo_accion, sum(evento.cantidad_beneficio) as personas_atendidas, sum(evento.hombres) as hombres, sum(evento.mujeres) as mujeres,
sum(evento.monto_evento)as inversion_ejercido,
count(tipo_apoyo.tipo_accion) as acciones
FROM
    tipo_apoyo 
INNER JOIN
    componente ON componente.id_componente = tipo_apoyo.ta_id_componente
INNER JOIN
    programa ON componente.com_id_programa = programa.id_programa
INNER JOIN  
    evento ON evento.id_accion_evento = tipo_apoyo.id_tipo_apoyo   
WHERE programa.id_programa = $id_usuario_programa  and componente.id_componente='$id_componente' 
group by  programa.id_programa, tipo_accion
order by  tipo_accion";
$tabla_acciones1_exportar = $mysqli->query($tabla_acciones);
$tabla_acciones1 = $mysqli->query($tabla_acciones);
$tabla_acciones_rows = array();
$tabla_acciones_table = array();

$tabla_acciones_table['cols']= array(
    array('label'=>'Programa', 'type'=>'string'),
    array('label'=>'Tipo de Acción', 'type'=>'string'),
    array('label'=>'Personas Atendidas', 'type'=>'number'),
	array('label'=>'Hombres', 'type'=>'number'),
	array('label'=>'Mujeres', 'type'=>'number'),
    array('label'=>'Monto ejercido', 'type'=>'number'),  
    array('label'=>'Número de acciones', 'type'=>'number'),

);
    foreach($tabla_acciones1 as $b){
        $tabla_temp = array();
        $tabla_temp[] = array('v'=>(string) $b['nombre_programa']);
        $tabla_temp[] = array('v'=>(string) $b['tipo_accion']);
        $tabla_temp[] = array('v'=>(integer) $b['personas_atendidas']);
		$tabla_temp[] = array('v'=>(integer) $b['hombres']);
		$tabla_temp[] = array('v'=>(integer) $b['mujeres']);
        $tabla_temp[] = array('v'=>(integer) $b['inversion_ejercido']);
        $tabla_temp[] = array('v'=>(integer) $b['acciones']);
        $tabla_acciones_rows[] = array ('c'=>$tabla_temp);
    }
    $tabla_acciones_table['rows']=$tabla_acciones_rows;
    $tabla_accionesjsonTable =json_encode($tabla_acciones_table);  //GRAFICO 13    
////  
////DESGLOSE POR TIPO DE ACCIONES (TRANSVERSALIDAD, ACCIONES, PREVENCION)

$tabla_acciones = "SELECT programa.nombre_programa, componente.nombre_componente, tipo_apoyo.tipo_accion, evento.nombre_evento, sum(evento.cantidad_beneficio) as personas_atendidas, sum(evento.hombres) as hombres, sum(evento.mujeres) as mujeres,
sum(evento.monto_evento)as inversion_ejercido
FROM
	tipo_apoyo 
INNER JOIN
	componente ON componente.id_componente = tipo_apoyo.ta_id_componente
INNER JOIN
	programa ON componente.com_id_programa = programa.id_programa
INNER JOIN  
	evento ON evento.id_accion_evento = tipo_apoyo.id_tipo_apoyo   
WHERE programa.id_programa = $id_usuario_programa  and componente.id_componente='$id_componente' 
group by programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento, evento.fecha_evento, tipo_accion
order by  tipo_accion";
$tabla_acciones1_desglose_exportar = $mysqli->query($tabla_acciones);
$tabla_acciones1 = $mysqli->query($tabla_acciones);
$tabla_acciones_rows = array();
$tabla_acciones_table = array();

$tabla_acciones_table['cols']= array(
    array('label'=>'Programa', 'type'=>'string'),
    array('label'=>'Componente', 'type'=>'string'),
    array('label'=>'Tipo de Acción', 'type'=>'string'),
    array('label'=>'Evento', 'type'=>'string'),
    array('label'=>'Personas Atendidas', 'type'=>'number'),
	array('label'=>'Hombres', 'type'=>'number'),
	array('label'=>'Mujeres', 'type'=>'number'),
    array('label'=>'Monto ejercido', 'type'=>'number'),  


);
    foreach($tabla_acciones1 as $b){
        $tabla_temp = array();    
        $tabla_temp[] = array('v'=>(string) $b['nombre_programa']);
        $tabla_temp[] = array('v'=>(string) $b['nombre_componente']);
        $tabla_temp[] = array('v'=>(string) $b['tipo_accion']);
        $tabla_temp[] = array('v'=>(string) $b['nombre_evento']);
        $tabla_temp[] = array('v'=>(integer) $b['personas_atendidas']);
		$tabla_temp[] = array('v'=>(integer) $b['hombres']);
		$tabla_temp[] = array('v'=>(integer) $b['mujeres']);
        $tabla_temp[] = array('v'=>(integer) $b['inversion_ejercido']);

        $tabla_acciones_rows[] = array ('c'=>$tabla_temp);
    }
    $tabla_acciones_table['rows']=$tabla_acciones_rows;
    $tabla_acciones_desglosejsonTable =json_encode($tabla_acciones_table);   //GRAFICO 14  
///////INICIO REGION, MUNICIPIO, GENERO ---EVENTO
//-----GRÁFICO POR REGION GRAFICO PAY

    $pastel = "SELECT  region.region, COUNT(region.region) AS Total_Cuenta 
    FROM
    evento
    INNER JOIN localidad ON localidad.cve_localidad = evento.lugar_evento
    INNER JOIN municipio ON municipio.cve_municipios = localidad.l_cve_municipio
    INNER JOIN region ON region.cve_regiones = municipio.m_cve_region
    INNER JOIN tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
    INNER JOIN componente ON componente.id_componente = tipo_apoyo.ta_id_componente
    INNER JOIN programa ON programa.id_programa = componente.com_id_programa
    WHERE programa.id_programa = $id_usuario_programa   and componente.id_componente='$id_componente'  
    GROUP BY region.region ORDER BY  region.region  ASC";


    $pastel1 = $mysqli->query($pastel);
    $pastel_rows = array();
    $pastel_table1evento = array();

    $pastel_table1evento['cols']= array(
        array('label'=>'Región', 'type'=>'string'),
        array('label'=>'Cantidad', 'type'=>'number')

    );
        foreach($pastel1 as $p){
            $pastel_temp = array();
            $pastel_temp[] = array('v'=>(string) $p['region']);
            $pastel_temp[] = array('v'=>(integer) $p['Total_Cuenta']);
            $pastel_rows[] = array ('c'=>$pastel_temp);
        }
        $pastel_table1evento['rows']=$pastel_rows;
        $pasteljsonTable1evento =json_encode($pastel_table1evento);

//////////

//-----GRÁFICO POR MUNICIPIO -GRÁFICO PAY

        ///////////

            $pastel = "SELECT  municipio.municipio, COUNT(municipio.municipio) AS Total_Cuenta 
            FROM
            evento 
	        INNER JOIN localidad ON localidad.cve_localidad = evento.lugar_evento
            INNER JOIN municipio ON municipio.cve_municipios = localidad.l_cve_municipio
            INNER JOIN region ON region.cve_regiones = municipio.m_cve_region
            INNER JOIN tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
            INNER JOIN componente ON componente.id_componente = tipo_apoyo.ta_id_componente
            INNER JOIN programa ON programa.id_programa = componente.com_id_programa
            WHERE programa.id_programa = $id_usuario_programa   and componente.id_componente='$id_componente'   
            GROUP BY municipio.municipio ORDER BY  municipio.municipio  ASC";

            $tabla_localidades = "SELECT programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento, evento.fecha_evento, region.region, municipio.municipio, localidad.localidad, localidad.latitud_dec, localidad.longitud_dec, COUNT(localidad.localidad) AS Total_Cuenta 
            FROM
            evento 
	        INNER JOIN localidad ON localidad.cve_localidad = evento.lugar_evento
            INNER JOIN municipio ON municipio.cve_municipios = localidad.l_cve_municipio
            INNER JOIN region ON region.cve_regiones = municipio.m_cve_region
            INNER JOIN tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
            INNER JOIN componente ON componente.id_componente = tipo_apoyo.ta_id_componente
            INNER JOIN programa ON programa.id_programa = componente.com_id_programa
            WHERE programa.id_programa = $id_usuario_programa   and componente.id_componente='$id_componente'   
            GROUP BY localidad.localidad ORDER BY  localidad.localidad  ASC";

        $tablalocexportar = $mysqli->query($tabla_localidades);
        $pastel1 = $mysqli->query($pastel);
        $pastel_rows = array();
        $pastel_table2evento = array();

        $pastel_table2evento['cols']= array(
            array('label'=>'Municipio %', 'type'=>'string'),
            array('label'=>'Cantidad', 'type'=>'number')

        );
            foreach($pastel1 as $p){
                $pastel_temp = array();
                $pastel_temp[] = array('v'=>(string) $p['municipio']);
                $pastel_temp[] = array('v'=>(integer) $p['Total_Cuenta']);
                $pastel_rows[] = array ('c'=>$pastel_temp);
            }
            $pastel_table2evento['rows']=$pastel_rows;
            $pasteljsonTable2evento =json_encode($pastel_table2evento);

        //////////               
//-----GRÁFICO POR GÉNERO
				$sqlh="UPDATE hombre_mujer SET hombre_mujer.cantidad_h_m =(SELECT SUM( evento.hombres) AS Total_CuentaH
                FROM
                evento 
	            INNER JOIN localidad ON localidad.cve_localidad = evento.lugar_evento
                INNER JOIN municipio ON municipio.cve_municipios = localidad.l_cve_municipio
                INNER JOIN region ON region.cve_regiones = municipio.m_cve_region
                INNER JOIN tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
                INNER JOIN componente ON componente.id_componente = tipo_apoyo.ta_id_componente
                INNER JOIN programa ON programa.id_programa = componente.com_id_programa
                WHERE programa.id_programa = $id_usuario_programa  and componente.id_componente='$id_componente') 
				WHERE hombre_mujer.h_m ='HOMBRE'";
				$mysqli->query($sqlh);
				
				$sqlm="UPDATE hombre_mujer SET hombre_mujer.cantidad_h_m =(SELECT SUM( evento.mujeres) AS Total_CuentaM 
                FROM
                evento 
	            INNER JOIN localidad ON localidad.cve_localidad = evento.lugar_evento
                INNER JOIN municipio ON municipio.cve_municipios = localidad.l_cve_municipio
                INNER JOIN region ON region.cve_regiones = municipio.m_cve_region
                INNER JOIN tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
                INNER JOIN componente ON componente.id_componente = tipo_apoyo.ta_id_componente
                INNER JOIN programa ON programa.id_programa = componente.com_id_programa
                WHERE programa.id_programa = $id_usuario_programa  and componente.id_componente='$id_componente') 
				WHERE hombre_mujer.h_m ='MUJER'";
				$mysqli->query($sqlm);
            //////////
				$pastel = "SELECT h_m, cantidad_h_m FROM hombre_mujer";
                $pastel1 = $mysqli->query($pastel);
                $pastel_rows = array();
                $pastel_table3evento = array();

                $pastel_table3evento['cols']= array(
                    array('label'=>'Género', 'type'=>'string'),
                    array('label'=>'Cantidad', 'type'=>'number')

                );
                    foreach($pastel1 as $p){
                        $pastel_temp = array();
                        $pastel_temp[] = array('v'=>(string) $p['h_m']);
                        $pastel_temp[] = array('v'=>(integer) $p['cantidad_h_m']);
                        $pastel_rows[] = array ('c'=>$pastel_temp);
                    }
                    $pastel_table3evento['rows']=$pastel_rows;
                    $pasteljsonTable3evento =json_encode($pastel_table3evento);

                
///////FIN REGION, MUNICIPIO, GENERO --- EVENTO
//// FIN DE SI NO  SELECCIONA PROGRAMA, COMPONENTE 
//*SEPTIMA CONSULTA 1 VARIABLE Y FECHA
}// SI SELECCIONA PROGRAMA Y FECHA
elseif((!empty($_GET['programa'])) && (!empty($_GET['fechainicio']) && !empty($_GET['fechafinal'])) ){
    $id_usuario_programa =($_GET['programa']);
  
    $sqlProgramas = "SELECT 
    programa.id_programa,    
    nombre_programa
    FROM
    programa

    WHERE programa.id_programa  = $id_usuario_programa  ";

    $programas = $mysqli->query($sqlProgramas);
    $row_programa  = $programas->fetch_assoc();
    $_programa=$row_programa ['nombre_programa'];
    
    $f_inicio =($_GET['fechainicio']);
    $f_final =($_GET['fechafinal']);

        //-----GRÁFICO PRESUPUESTO EJERCIDO POR ACTIVIDAD
        //////////
            $pastel = "SELECT tipo_apoyo.id_tipo_apoyo, tipo_apoyo.nombre_tipo_apoyo, tipo_apoyo.ta_id_componente, tipo_apoyo.meta_um_servicio, tipo_apoyo.meta_um_beneficio, tipo_apoyo.meta_cantidad_servicio, tipo_apoyo.meta_cantidad_beneficio, tipo_apoyo.meta_alcanzada_cantidad_servicio, tipo_apoyo.meta_alcanzada_cantidad_beneficio, tipo_apoyo.tipo_apoyo_presupuesto_ejercido, componente.nombre_componente, programa.nombre_programa, programa.id_programa, evento.fecha_evento,
            reg_partidas.monto_partidas, sum(reg_partidas.monto_partidas) as programa_presupuesto_ejercido_fechas  
            FROM  programa
            INNER JOIN componente on componente.com_id_programa = programa.id_programa
            INNER JOIN tipo_apoyo on tipo_apoyo.ta_id_componente = componente.id_componente
            INNER JOIN evento on evento.id_accion_evento = tipo_apoyo.id_tipo_apoyo
            INNER JOIN reg_partidas on reg_partidas.id_evento_partidas = evento.id_evento  
            where programa.id_programa=$id_usuario_programa  and (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento<=  ' + $f_final + ') ORDER BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo";

        $pastel1 = $mysqli->query($pastel);
        $pastel_rows = array();
        $pastel_table6 = array();

        $pastel_table6['cols']= array(
            array('label'=>'Actividad', 'type'=>'string'),
            array('label'=>'Monto', 'type'=>'number')

        );
            foreach($pastel1 as $p){
                $pastel_temp = array();
                $pastel_temp[] = array('v'=>(string) $p['nombre_tipo_apoyo']);
                $pastel_temp[] = array('v'=>(integer) $p['programa_presupuesto_ejercido_fechas']);
                $pastel_rows[] = array ('c'=>$pastel_temp);
            }
            $pastel_table6['rows']=$pastel_rows;
            $pasteljsonTable6 =json_encode($pastel_table6);  //GRAFICO 11

        //////////Metas programadas vs Metas alcanzadas por Actividad
            $tabla = "SELECT tipo_apoyo.id_tipo_apoyo, tipo_apoyo.nombre_tipo_apoyo, tipo_apoyo.ta_id_componente, tipo_apoyo.meta_um_servicio, tipo_apoyo.meta_um_beneficio, tipo_apoyo.meta_cantidad_servicio, tipo_apoyo.meta_cantidad_beneficio, tipo_apoyo.meta_alcanzada_cantidad_servicio, tipo_apoyo.meta_alcanzada_cantidad_beneficio, tipo_apoyo.tipo_apoyo_presupuesto_ejercido, componente.nombre_componente, programa.nombre_programa, programa.id_programa,COUNT( meta_alcanzada_cantidad_servicio ) AS tipo_apoyoA,
            concat(round(( meta_alcanzada_cantidad_servicio/meta_cantidad_servicio * 100 ),2),'%') AS porcentaje_servicio,
            COUNT( meta_alcanzada_cantidad_beneficio ) AS tipo_apoyoA,
            concat(round(( meta_alcanzada_cantidad_beneficio/meta_cantidad_beneficio * 100 ),2),'%') AS porcentaje_beneficio,  servicios.nombre_servicios,  beneficios.nombre_beneficios,
            evento.fecha_evento,
            reg_partidas.monto_partidas,
            sum(reg_partidas.monto_partidas) as programa_presupuesto_ejercido_fechas  
            FROM programa
            INNER JOIN componente on componente.com_id_programa = programa.id_programa
            INNER JOIN tipo_apoyo on tipo_apoyo.ta_id_componente = componente.id_componente
            INNER JOIN evento on evento.id_accion_evento = tipo_apoyo.id_tipo_apoyo
            INNER JOIN reg_partidas on reg_partidas.id_evento_partidas = evento.id_evento  
            INNER JOIN beneficios ON beneficios.id_beneficios = tipo_apoyo.meta_um_beneficio 
            INNER JOIN servicios ON servicios.id_servicios = tipo_apoyo.meta_um_servicio
            where programa.id_programa=$id_usuario_programa and (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento<=  ' + $f_final + ') group by id_tipo_apoyo ORDER BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo";
            $tabla1_exportar = $mysqli->query($tabla);
            $tabla1 = $mysqli->query($tabla);
            $tabla_rows = array();
            $tabla_table = array();

            $tabla_table['cols']= array(
                array('label'=>'Componente', 'type'=>'string'),
                array('label'=>'Actividad', 'type'=>'string'),
                array('label'=>'Monto ejercido', 'type'=>'number'),
                array('label'=>'U.M. Servicios', 'type'=>'string'),
                array('label'=>'Meta', 'type'=>'number'),
                array('label'=>'Avance', 'type'=>'number'),
                array('label'=>'%', 'type'=>'string'),
                array('label'=>'U.M. Beneficio', 'type'=>'string'),
                array('label'=>'Meta', 'type'=>'number'),
                array('label'=>'Avance', 'type'=>'number'),
                array('label'=>'%', 'type'=>'string'),

            );
                foreach($tabla1 as $b){
                    $tabla_temp = array();
                    $tabla_temp[] = array('v'=>(string) $b['nombre_componente']);
                    $tabla_temp[] = array('v'=>(string) $b['nombre_tipo_apoyo']);
                    $tabla_temp[] = array('v'=>(integer) $b['programa_presupuesto_ejercido_fechas']);
                    $tabla_temp[] = array('v'=>(string) $b['nombre_servicios']);
                    $tabla_temp[] = array('v'=>(integer) $b['meta_cantidad_servicio']);
                    $tabla_temp[] = array('v'=>(integer) $b['meta_alcanzada_cantidad_servicio']);
                    $tabla_temp[] = array('v'=>(string) $b['porcentaje_servicio']);
                    $tabla_temp[] = array('v'=>(string) $b['nombre_beneficios']);
                    $tabla_temp[] = array('v'=>(integer) $b['meta_cantidad_beneficio']);
                    $tabla_temp[] = array('v'=>(integer) $b['meta_alcanzada_cantidad_beneficio']);
                    $tabla_temp[] = array('v'=>(string) $b['porcentaje_beneficio']);
                    $tabla_rows[] = array ('c'=>$tabla_temp);
                }
                $tabla_table['rows']=$tabla_rows;
                $tablajsonTable =json_encode($tabla_table);   //GRAFICO 12  

                ////////////////Desglose de eventos realizados

                $tabla_evento = "SELECT 
                evento.id_evento,
                evento.nombre_evento,
                evento.pob_obj_evento,
                evento.nivel_geo_evento,
                evento.lugar_evento,
                evento.monto_evento,
                evento.fecha_evento,
                evento.fecha_modificacion_evento,
                evento.usuario_evento,
                evento.id_accion_evento,
                evento.cantidad_servicio,
                evento.cantidad_beneficio,
                evento.hombres, 
                evento.mujeres,
                region.region,
                municipio.municipio,
                localidad.localidad, 
                tipo_apoyo.nombre_tipo_apoyo,
                servicios.nombre_servicios,
                beneficios.nombre_beneficios,
                componente.nombre_componente,
                COUNT(evento.hombres) AS tipo_apoyoA,       
                concat(round((evento.hombres/cantidad_beneficio * 100 ),2),'%') AS porcentaje_hombres,
                COUNT(evento.mujeres) AS tipo_apoyoA,       
                concat(round((evento.mujeres/cantidad_beneficio * 100 ),2),'%') AS porcentaje_mujeres  
                FROM
                evento
                INNER JOIN
                localidad ON localidad.cve_localidad = evento.lugar_evento
                    INNER JOIN
                municipio ON municipio.cve_municipios = localidad.l_cve_municipio
                    INNER JOIN
                region ON region.cve_regiones = municipio.m_cve_region
                    INNER JOIN
                usuarios ON usuarios.id = evento.usuario_evento
                    INNER JOIN
                tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
                    INNER JOIN
                componente ON componente.id_componente = tipo_apoyo.ta_id_componente
                    INNER JOIN
                programa ON componente.com_id_programa = programa.id_programa  
                INNER JOIN
                beneficios ON beneficios.id_beneficios = tipo_apoyo.meta_um_beneficio
                        INNER JOIN
                servicios ON servicios.id_servicios = tipo_apoyo.meta_um_servicio
                where programa.id_programa=$id_usuario_programa and (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento<=  ' + $f_final + ')
                group by evento.id_evento
                ORDER BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento";
                
                $tabla1_evento_exportar = $mysqli->query($tabla_evento);
                $tabla1_evento = $mysqli->query($tabla_evento);
                $tabla_evento_rows = array();
                $tabla_evento_table = array();

                $tabla_evento_table['cols']= array(
                    array('label'=>'Componente', 'type'=>'string'),
                    array('label'=>'Actividad', 'type'=>'string'),
                    array('label'=>'Evento', 'type'=>'string'),
                    array('label'=>'Monto ejercido', 'type'=>'number'),
                    array('label'=>'Fecha', 'type'=>'string'),
                    array('label'=>'U.M. Servicios', 'type'=>'string'),
                    array('label'=>'Meta alcanzada', 'type'=>'number'),
                    array('label'=>'U.M. Beneficio', 'type'=>'string'),
                    array('label'=>'Meta alcanzada', 'type'=>'number'),
                    array('label'=>'Hombres', 'type'=>'number'),
                    array('label'=>'%', 'type'=>'string'),
                    array('label'=>'Mujeres', 'type'=>'number'),
                    array('label'=>'%', 'type'=>'string'),             

                );
                    foreach($tabla1_evento as $b){
                        $tabla_evento_temp = array();
                        $tabla_evento_temp[] = array('v'=>(string) $b['nombre_componente']);
                        $tabla_evento_temp[] = array('v'=>(string) $b['nombre_tipo_apoyo']);
                        $tabla_evento_temp[] = array('v'=>(string) $b['nombre_evento']);
                        $tabla_evento_temp[] = array('v'=>(integer) $b['monto_evento']);
                        $tabla_evento_temp[] = array('v'=>(string) $b['fecha_evento']);
                        $tabla_evento_temp[] = array('v'=>(string) $b['nombre_servicios']);                    
                        $tabla_evento_temp[] = array('v'=>(integer) $b['cantidad_servicio']);
                        $tabla_evento_temp[] = array('v'=>(string) $b['nombre_beneficios']);
                        $tabla_evento_temp[] = array('v'=>(integer) $b['cantidad_beneficio']);
                        $tabla_evento_temp[] = array('v'=>(integer) $b['hombres']);
                        $tabla_evento_temp[] = array('v'=>(string) $b['porcentaje_hombres']);
                        $tabla_evento_temp[] = array('v'=>(integer) $b['mujeres']);
                        $tabla_evento_temp[] = array('v'=>(string) $b['porcentaje_mujeres']);
                        $tabla_evento_rows[] = array ('c'=>$tabla_evento_temp);
                    }
                    $tabla_evento_table['rows']=$tabla_evento_rows;
                    $tabla_evento_jsonTable =json_encode($tabla_evento_table);  //GRAFICO 15


            //-----GRÁFICO POR OBJETO DEL GASTO
                    //////////
                        $pastel = "SELECT reg_partidas.id_reg_partidas, reg_partidas.partida, reg_partidas.cantidad_partidas, reg_partidas.monto_partidas, reg_partidas.id_evento_partidas,
                        sum(reg_partidas.monto_partidas) as totalsuma,
                        objeto_de_gasto.partida_gasto_texto,
                        evento.fecha_evento,
                        reg_partidas.monto_partidas,
                        sum(reg_partidas.monto_partidas) as programa_presupuesto_ejercido_fechas
                        FROM reg_partidas
                        INNER JOIN evento on evento.id_evento = reg_partidas.id_evento_partidas
                        INNER JOIN tipo_apoyo on tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
                        INNER JOIN componente on componente.id_componente = tipo_apoyo.ta_id_componente
                        INNER JOIN programa on programa.id_programa = componente.com_id_programa
                        INNER JOIN objeto_de_gasto on objeto_de_gasto.clave = reg_partidas.partida
                        where programa.id_programa=$id_usuario_programa and (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento<=  ' + $f_final + ')
                        group by reg_partidas.partida";

                        $pastel1 = $mysqli->query($pastel);
                        $pastel_rows = array();
                        $pastel_table9 = array();

                        $pastel_table9['cols']= array(
                            array('label'=>'Objeto de gasto', 'type'=>'string'),
                            array('label'=>'Presupuesto ejercido', 'type'=>'number')

                        );
                            foreach($pastel1 as $p){
                                $pastel_temp = array();
                                $pastel_temp[] = array('v'=>(string) $p['partida_gasto_texto']);
                                $pastel_temp[] = array('v'=>(integer) $p['totalsuma']);
                                $pastel_rows[] = array ('c'=>$pastel_temp);
                            }
                            $pastel_table9['rows']=$pastel_rows;
                            $pasteljsonTable9 =json_encode($pastel_table9);  //GRAFICO 18

                        //////////
        ////////////////Tabla Presupuesto ejercido a nivel de objeto de gasto (partidas y monto ejercido)

            $tabla_partida = "SELECT 
            id_reg_partidas,
            partida,
            cantidad_partidas,
            monto_partidas,
            id_evento_partidas,
            partida_gasto_texto,
            evento.nombre_evento,
            programa.nombre_programa,
            componente.nombre_componente,
            tipo_apoyo.nombre_tipo_apoyo,
            capitulo,
            evento.fecha_evento,
            reg_partidas.monto_partidas,
            sum(reg_partidas.monto_partidas) as programa_presupuesto_ejercido_fechas
        FROM
            reg_partidas
                INNER JOIN
            objeto_de_gasto ON clave = partida
            INNER JOIN 
            evento ON evento.id_evento = reg_partidas.id_evento_partidas
            INNER JOIN 
            tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
            INNER JOIN 
            componente ON componente.id_componente = tipo_apoyo.ta_id_componente
            INNER JOIN
            programa ON programa.id_programa = componente.com_id_programa
            WHERE
            programa.id_programa=$id_usuario_programa and (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento<=  ' + $f_final + ')
            group by reg_partidas.id_reg_partidas
            ORDER BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento, reg_partidas.id_reg_partidas";
            $tabla1_partida_exportar = $mysqli->query($tabla_partida);
            $tabla1_partida = $mysqli->query($tabla_partida);
            $tabla_partida_rows = array();
            $tabla_partida_table = array();

            $tabla_partida_table['cols']= array(
                array('label'=>'Programa', 'type'=>'string'),
                array('label'=>'Componente', 'type'=>'string'),
                array('label'=>'Actividad', 'type'=>'string'),
                array('label'=>'Evento', 'type'=>'string'),
                array('label'=>'Capítulo', 'type'=>'number'),
                array('label'=>'Partida', 'type'=>'string'),
                array('label'=>'Monto', 'type'=>'number'),

            );
                foreach($tabla1_partida as $b){
                    $tabla_partida_temp = array();
                    $tabla_partida_temp[] = array('v'=>(string) $b['nombre_programa']);
                    $tabla_partida_temp[] = array('v'=>(string) $b['nombre_componente']);
                    $tabla_partida_temp[] = array('v'=>(string) $b['nombre_tipo_apoyo']);
                    $tabla_partida_temp[] = array('v'=>(string) $b['nombre_evento']);
                    $tabla_partida_temp[] = array('v'=>(integer) $b['capitulo']);
                    $tabla_partida_temp[] = array('v'=>(string) $b['partida_gasto_texto']);
                    $tabla_partida_temp[] = array('v'=>(integer) $b['monto_partidas']);
                    $tabla_partida_rows[] = array ('c'=>$tabla_partida_temp);
                }
                $tabla_partida_table['rows']=$tabla_partida_rows;
                $tabla_partida_jsonTable =json_encode($tabla_partida_table);  //GRAFICO 19

                //-----GRÁFICO POR OBJETO DEL GASTO CAPÍTULO
                    //////////
                    $pastel = "SELECT capitulo,  evento.fecha_evento, SUM(monto_partidas) AS capitulo_total
                    FROM reg_partidas
                    INNER JOIN evento on evento.id_evento = reg_partidas.id_evento_partidas
                    INNER JOIN tipo_apoyo on tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
                    INNER JOIN componente on componente.id_componente = tipo_apoyo.ta_id_componente
                    INNER JOIN programa on programa.id_programa = componente.com_id_programa
                    INNER JOIN objeto_de_gasto on objeto_de_gasto.clave = reg_partidas.partida
                    where programa.id_programa=$id_usuario_programa and (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento<=  ' + $f_final + ')
                    group by capitulo";

                    $pastel1 = $mysqli->query($pastel);
                    $pastel_rows = array();
                    $pastel_table10 = array();

                    $pastel_table10['cols']= array(
                        array('label'=>'Capítulo', 'type'=>'string'),
                        array('label'=>'Presupuesto ejercido', 'type'=>'number')

                    );
                        foreach($pastel1 as $p){
                            $pastel_temp = array();
                            $pastel_temp[] = array('v'=>(integer) $p['capitulo']);
                            $pastel_temp[] = array('v'=>(integer) $p['capitulo_total']);
                            $pastel_rows[] = array ('c'=>$pastel_temp);
                        }
                        $pastel_table10['rows']=$pastel_rows;
                        $pasteljsonTable10 =json_encode($pastel_table10);  //GRAFICO 16

                    //////////

                    ////////////////Tabla Presupuesto ejercido a nivel de objeto de gasto (capitulo y monto ejercido)

                    $tabla_capitulo = "SELECT 
                    programa.nombre_programa,
                    componente.nombre_componente,
                    tipo_apoyo.nombre_tipo_apoyo,
                    evento.nombre_evento,
                    capitulo,
                    evento.fecha_evento,
                    SUM(monto_partidas) AS capitulo_total
                FROM
                    reg_partidas
                        INNER JOIN
                    objeto_de_gasto ON clave = partida
                    INNER JOIN 
                    evento ON evento.id_evento = reg_partidas.id_evento_partidas
                    INNER JOIN 
                    tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
                    INNER JOIN 
                    componente ON componente.id_componente = tipo_apoyo.ta_id_componente
                    INNER JOIN
                    programa ON programa.id_programa = componente.com_id_programa
                    WHERE
                    programa.id_programa=$id_usuario_programa and (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento<=  ' + $f_final + ')
                    GROUP BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento, capitulo
                    ORDER BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento, capitulo";
                    $tabla1_capitulo_exportar = $mysqli->query($tabla_capitulo);
                    $tabla1_capitulo = $mysqli->query($tabla_capitulo);
                    $tabla_capitulo_rows = array();
                    $tabla_capitulo_table = array();

                    $tabla_capitulo_table['cols']= array(
                        array('label'=>'Programa', 'type'=>'string'),
                        array('label'=>'Componente', 'type'=>'string'),
                        array('label'=>'Actividad', 'type'=>'string'),
                        array('label'=>'Evento', 'type'=>'string'),
                        array('label'=>'Capítulo', 'type'=>'number'),
                        array('label'=>'Monto', 'type'=>'number'),

                    );
                        foreach($tabla1_capitulo as $b){
                            $tabla_capitulo_temp = array();
                            $tabla_capitulo_temp[] = array('v'=>(string) $b['nombre_programa']);
                            $tabla_capitulo_temp[] = array('v'=>(string) $b['nombre_componente']);
                            $tabla_capitulo_temp[] = array('v'=>(string) $b['nombre_tipo_apoyo']);
                            $tabla_capitulo_temp[] = array('v'=>(string) $b['nombre_evento']);
                            $tabla_capitulo_temp[] = array('v'=>(integer) $b['capitulo']);
                            $tabla_capitulo_temp[] = array('v'=>(integer) $b['capitulo_total']);
                            $tabla_capitulo_rows[] = array ('c'=>$tabla_capitulo_temp);
                        }
                        $tabla_capitulo_table['rows']=$tabla_capitulo_rows;
                        $tabla_capitulo_jsonTable =json_encode($tabla_capitulo_table);  //GRAFICO 17 

////RESUMEN POR TIPO DE ACCIONES (TRANSVERSALIDAD, ACCIONES, PREVENCION)

$tabla_acciones = "SELECT programa.nombre_programa, tipo_apoyo.tipo_accion, sum(evento.cantidad_beneficio) as personas_atendidas, sum(evento.hombres) as hombres, sum(evento.mujeres) as mujeres,
sum(evento.monto_evento)as inversion_ejercido,
count(tipo_apoyo.tipo_accion) as acciones
FROM
    tipo_apoyo 
INNER JOIN
    componente ON componente.id_componente = tipo_apoyo.ta_id_componente
INNER JOIN
    programa ON componente.com_id_programa = programa.id_programa
INNER JOIN  
    evento ON evento.id_accion_evento = tipo_apoyo.id_tipo_apoyo   
WHERE programa.id_programa = $id_usuario_programa  AND  (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento <=  ' + $f_final + ')
group by  programa.id_programa, tipo_accion
order by  tipo_accion";

$tabla_acciones1_exportar = $mysqli->query($tabla_acciones);
$tabla_acciones1 = $mysqli->query($tabla_acciones);
$tabla_acciones_rows = array();
$tabla_acciones_table = array();

$tabla_acciones_table['cols']= array(
    array('label'=>'Programa', 'type'=>'string'),
    array('label'=>'Tipo de Acción', 'type'=>'string'),
    array('label'=>'Personas Atendidas', 'type'=>'number'),
	array('label'=>'Hombres', 'type'=>'number'),
	array('label'=>'Mujeres', 'type'=>'number'),
    array('label'=>'Monto ejercido', 'type'=>'number'),  
    array('label'=>'Número de acciones', 'type'=>'number'),

);
    foreach($tabla_acciones1 as $b){
        $tabla_temp = array();
        $tabla_temp[] = array('v'=>(string) $b['nombre_programa']);
        $tabla_temp[] = array('v'=>(string) $b['tipo_accion']);
        $tabla_temp[] = array('v'=>(integer) $b['personas_atendidas']);
		$tabla_temp[] = array('v'=>(integer) $b['hombres']);
		$tabla_temp[] = array('v'=>(integer) $b['mujeres']);
        $tabla_temp[] = array('v'=>(integer) $b['inversion_ejercido']);
        $tabla_temp[] = array('v'=>(integer) $b['acciones']);
        $tabla_acciones_rows[] = array ('c'=>$tabla_temp);
    }
    $tabla_acciones_table['rows']=$tabla_acciones_rows;
    $tabla_accionesjsonTable =json_encode($tabla_acciones_table);  //GRAFICO 13  
////  
////DESGLOSE POR TIPO DE ACCIONES (TRANSVERSALIDAD, ACCIONES, PREVENCION)

$tabla_acciones = "SELECT programa.nombre_programa, componente.nombre_componente, tipo_apoyo.tipo_accion, evento.nombre_evento, sum(evento.cantidad_beneficio) as personas_atendidas, sum(evento.hombres) as hombres, sum(evento.mujeres) as mujeres,
sum(evento.monto_evento)as inversion_ejercido
FROM
	tipo_apoyo 
INNER JOIN
	componente ON componente.id_componente = tipo_apoyo.ta_id_componente
INNER JOIN
	programa ON componente.com_id_programa = programa.id_programa
INNER JOIN  
	evento ON evento.id_accion_evento = tipo_apoyo.id_tipo_apoyo   
WHERE programa.id_programa = $id_usuario_programa AND  (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento <=  ' + $f_final + ')
group by programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento, evento.fecha_evento, tipo_accion
order by  tipo_accion";
$tabla_acciones1_desglose_exportar = $mysqli->query($tabla_acciones);
$tabla_acciones1 = $mysqli->query($tabla_acciones);
$tabla_acciones_rows = array();
$tabla_acciones_table = array();

$tabla_acciones_table['cols']= array(
    array('label'=>'Programa', 'type'=>'string'),
    array('label'=>'Componente', 'type'=>'string'),
    array('label'=>'Tipo de Acción', 'type'=>'string'),
    array('label'=>'Evento', 'type'=>'string'),
    array('label'=>'Personas Atendidas', 'type'=>'number'),
	array('label'=>'Hombres', 'type'=>'number'),
	array('label'=>'Mujeres', 'type'=>'number'),
    array('label'=>'Monto ejercido', 'type'=>'number'),  


);
    foreach($tabla_acciones1 as $b){
        $tabla_temp = array();    
        $tabla_temp[] = array('v'=>(string) $b['nombre_programa']);
        $tabla_temp[] = array('v'=>(string) $b['nombre_componente']);
        $tabla_temp[] = array('v'=>(string) $b['tipo_accion']);
        $tabla_temp[] = array('v'=>(string) $b['nombre_evento']);
        $tabla_temp[] = array('v'=>(integer) $b['personas_atendidas']);
		$tabla_temp[] = array('v'=>(integer) $b['hombres']);
		$tabla_temp[] = array('v'=>(integer) $b['mujeres']);
        $tabla_temp[] = array('v'=>(integer) $b['inversion_ejercido']);

        $tabla_acciones_rows[] = array ('c'=>$tabla_temp);
    }
    $tabla_acciones_table['rows']=$tabla_acciones_rows;
    $tabla_acciones_desglosejsonTable =json_encode($tabla_acciones_table);   //GRAFICO 14   
///////INICIO REGION, MUNICIPIO, GENERO ---EVENTO
//-----GRÁFICO POR REGION GRAFICO PAY

    $pastel = "SELECT  region.region, COUNT(region.region) AS Total_Cuenta 
    FROM
    evento
    INNER JOIN localidad ON localidad.cve_localidad = evento.lugar_evento
    INNER JOIN municipio ON municipio.cve_municipios = localidad.l_cve_municipio
    INNER JOIN region ON region.cve_regiones = municipio.m_cve_region
    INNER JOIN tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
    INNER JOIN componente ON componente.id_componente = tipo_apoyo.ta_id_componente
    INNER JOIN programa ON programa.id_programa = componente.com_id_programa
    WHERE programa.id_programa = $id_usuario_programa 
    AND  (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento <=  ' + $f_final + ')  
    GROUP BY region.region ORDER BY  region.region  ASC";


    $pastel1 = $mysqli->query($pastel);
    $pastel_rows = array();
    $pastel_table1evento = array();

    $pastel_table1evento['cols']= array(
        array('label'=>'Región', 'type'=>'string'),
        array('label'=>'Cantidad', 'type'=>'number')

    );
        foreach($pastel1 as $p){
            $pastel_temp = array();
            $pastel_temp[] = array('v'=>(string) $p['region']);
            $pastel_temp[] = array('v'=>(integer) $p['Total_Cuenta']);
            $pastel_rows[] = array ('c'=>$pastel_temp);
        }
        $pastel_table1evento['rows']=$pastel_rows;
        $pasteljsonTable1evento =json_encode($pastel_table1evento);

//////////

//-----GRÁFICO POR MUNICIPIO -GRÁFICO PAY

        ///////////

            $pastel = "SELECT  municipio.municipio, COUNT(municipio.municipio) AS Total_Cuenta 
            FROM
            evento 
	        INNER JOIN localidad ON localidad.cve_localidad = evento.lugar_evento
            INNER JOIN municipio ON municipio.cve_municipios = localidad.l_cve_municipio
            INNER JOIN region ON region.cve_regiones = municipio.m_cve_region
            INNER JOIN tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
            INNER JOIN componente ON componente.id_componente = tipo_apoyo.ta_id_componente
            INNER JOIN programa ON programa.id_programa = componente.com_id_programa
            WHERE programa.id_programa = $id_usuario_programa 
            AND  (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento <=  ' + $f_final + ')   
            GROUP BY municipio.municipio ORDER BY  municipio.municipio  ASC";

            $tabla_localidades = "SELECT programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento, evento.fecha_evento, region.region, municipio.municipio, localidad.localidad, localidad.latitud_dec, localidad.longitud_dec, COUNT(localidad.localidad) AS Total_Cuenta 
            FROM
            evento 
	        INNER JOIN localidad ON localidad.cve_localidad = evento.lugar_evento
            INNER JOIN municipio ON municipio.cve_municipios = localidad.l_cve_municipio
            INNER JOIN region ON region.cve_regiones = municipio.m_cve_region
            INNER JOIN tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
            INNER JOIN componente ON componente.id_componente = tipo_apoyo.ta_id_componente
            INNER JOIN programa ON programa.id_programa = componente.com_id_programa
            WHERE programa.id_programa = $id_usuario_programa 
            AND  (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento <=  ' + $f_final + ') 
            GROUP BY localidad.localidad ORDER BY  localidad.localidad  ASC";

        $tablalocexportar = $mysqli->query($tabla_localidades);
        $pastel1 = $mysqli->query($pastel);
        $pastel_rows = array();
        $pastel_table2evento = array();

        $pastel_table2evento['cols']= array(
            array('label'=>'Municipio %', 'type'=>'string'),
            array('label'=>'Cantidad', 'type'=>'number')

        );
            foreach($pastel1 as $p){
                $pastel_temp = array();
                $pastel_temp[] = array('v'=>(string) $p['municipio']);
                $pastel_temp[] = array('v'=>(integer) $p['Total_Cuenta']);
                $pastel_rows[] = array ('c'=>$pastel_temp);
            }
            $pastel_table2evento['rows']=$pastel_rows;
            $pasteljsonTable2evento =json_encode($pastel_table2evento);

        //////////                
//-----GRÁFICO POR GÉNERO
				$sqlh="UPDATE hombre_mujer SET hombre_mujer.cantidad_h_m =(SELECT SUM( evento.hombres) AS Total_CuentaH
                FROM
                evento 
	            INNER JOIN localidad ON localidad.cve_localidad = evento.lugar_evento
                INNER JOIN municipio ON municipio.cve_municipios = localidad.l_cve_municipio
                INNER JOIN region ON region.cve_regiones = municipio.m_cve_region
                INNER JOIN tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
                INNER JOIN componente ON componente.id_componente = tipo_apoyo.ta_id_componente
                INNER JOIN programa ON programa.id_programa = componente.com_id_programa
                WHERE programa.id_programa = $id_usuario_programa 
                AND  (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento <=  ' + $f_final + '))
				WHERE hombre_mujer.h_m ='HOMBRE'";
				$mysqli->query($sqlh);
				
				$sqlm="UPDATE hombre_mujer SET hombre_mujer.cantidad_h_m =(SELECT SUM( evento.mujeres) AS Total_CuentaM 
                FROM
                evento 
	            INNER JOIN localidad ON localidad.cve_localidad = evento.lugar_evento
                INNER JOIN municipio ON municipio.cve_municipios = localidad.l_cve_municipio
                INNER JOIN region ON region.cve_regiones = municipio.m_cve_region
                INNER JOIN tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
                INNER JOIN componente ON componente.id_componente = tipo_apoyo.ta_id_componente
                INNER JOIN programa ON programa.id_programa = componente.com_id_programa
                WHERE programa.id_programa = $id_usuario_programa 
                AND  (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento <=  ' + $f_final + '))
				WHERE hombre_mujer.h_m ='MUJER'";
				$mysqli->query($sqlm);
            //////////
				$pastel = "SELECT h_m, cantidad_h_m FROM hombre_mujer";
                $pastel1 = $mysqli->query($pastel);
                $pastel_rows = array();
                $pastel_table3evento = array();

                $pastel_table3evento['cols']= array(
                    array('label'=>'Género', 'type'=>'string'),
                    array('label'=>'Cantidad', 'type'=>'number')

                );
                    foreach($pastel1 as $p){
                        $pastel_temp = array();
                        $pastel_temp[] = array('v'=>(string) $p['h_m']);
                        $pastel_temp[] = array('v'=>(integer) $p['cantidad_h_m']);
                        $pastel_rows[] = array ('c'=>$pastel_temp);
                    }
                    $pastel_table3evento['rows']=$pastel_rows;
                    $pasteljsonTable3evento =json_encode($pastel_table3evento);

                
///////FIN REGION, MUNICIPIO, GENERO --- EVENTO
//// SI NO  SELECCIONA PROGRAMA Y FECHAS LAS FECHAS
//*OCTAVA CONSULTA 1 VARIABLE
}//SI SELECIONA PROGRAMA
elseif(!empty($_GET['programa'])) {
    $id_usuario_programa =($_GET['programa']);
  
    $sqlProgramas = "SELECT 
    programa.id_programa,    
    nombre_programa
    FROM
    programa

    WHERE programa.id_programa  = $id_usuario_programa  ";

    $programas = $mysqli->query($sqlProgramas);
    $row_programa  = $programas->fetch_assoc();
    $_programa=$row_programa ['nombre_programa'];
    
    $f_inicio =($_GET['fechainicio']);
    $f_final =($_GET['fechafinal']);

        //-----GRÁFICO PRESUPUESTO EJERCIDO POR ACTIVIDAD
        //////////
        $pastel = "SELECT tipo_apoyo.id_tipo_apoyo, tipo_apoyo.nombre_tipo_apoyo, tipo_apoyo.ta_id_componente, tipo_apoyo.meta_um_servicio, tipo_apoyo.meta_um_beneficio, tipo_apoyo.meta_cantidad_servicio, tipo_apoyo.meta_cantidad_beneficio, tipo_apoyo.meta_alcanzada_cantidad_servicio, tipo_apoyo.meta_alcanzada_cantidad_beneficio, tipo_apoyo.tipo_apoyo_presupuesto_ejercido, componente.nombre_componente, programa.nombre_programa, programa.id_programa, evento.fecha_evento,
        reg_partidas.monto_partidas, sum(reg_partidas.monto_partidas) as programa_presupuesto_ejercido_fechas  
        FROM  programa
        INNER JOIN componente on componente.com_id_programa = programa.id_programa
        INNER JOIN tipo_apoyo on tipo_apoyo.ta_id_componente = componente.id_componente
        INNER JOIN evento on evento.id_accion_evento = tipo_apoyo.id_tipo_apoyo
        INNER JOIN reg_partidas on reg_partidas.id_evento_partidas = evento.id_evento  
        where programa.id_programa=$id_usuario_programa 
        ORDER BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo";

        $pastel1 = $mysqli->query($pastel);
        $pastel_rows = array();
        $pastel_table6 = array();

        $pastel_table6['cols']= array(
            array('label'=>'Actividad', 'type'=>'string'),
            array('label'=>'Monto', 'type'=>'number')

        );
        foreach($pastel1 as $p){
            $pastel_temp = array();
            $pastel_temp[] = array('v'=>(string) $p['nombre_tipo_apoyo']);
            $pastel_temp[] = array('v'=>(integer) $p['programa_presupuesto_ejercido_fechas']);
            $pastel_rows[] = array ('c'=>$pastel_temp);
        }
        $pastel_table6['rows']=$pastel_rows;
        $pasteljsonTable6 =json_encode($pastel_table6);  //GRAFICO 11

    //////////Metas programadas vs Metas alcanzadas por Actividad
        $tabla = "SELECT tipo_apoyo.id_tipo_apoyo, tipo_apoyo.nombre_tipo_apoyo, tipo_apoyo.ta_id_componente, tipo_apoyo.meta_um_servicio, tipo_apoyo.meta_um_beneficio, tipo_apoyo.meta_cantidad_servicio, tipo_apoyo.meta_cantidad_beneficio, tipo_apoyo.meta_alcanzada_cantidad_servicio, tipo_apoyo.meta_alcanzada_cantidad_beneficio, tipo_apoyo.tipo_apoyo_presupuesto_ejercido, componente.nombre_componente, programa.nombre_programa, programa.id_programa,COUNT( meta_alcanzada_cantidad_servicio ) AS tipo_apoyoA,
        concat(round(( meta_alcanzada_cantidad_servicio/meta_cantidad_servicio * 100 ),2),'%') AS porcentaje_servicio,
        COUNT( meta_alcanzada_cantidad_beneficio ) AS tipo_apoyoA,
        concat(round(( meta_alcanzada_cantidad_beneficio/meta_cantidad_beneficio * 100 ),2),'%') AS porcentaje_beneficio,  servicios.nombre_servicios,  beneficios.nombre_beneficios,
        evento.fecha_evento,
        reg_partidas.monto_partidas,
        sum(reg_partidas.monto_partidas) as programa_presupuesto_ejercido_fechas  
        FROM programa
        INNER JOIN componente on componente.com_id_programa = programa.id_programa
        INNER JOIN tipo_apoyo on tipo_apoyo.ta_id_componente = componente.id_componente
        INNER JOIN evento on evento.id_accion_evento = tipo_apoyo.id_tipo_apoyo
        INNER JOIN reg_partidas on reg_partidas.id_evento_partidas = evento.id_evento  
        INNER JOIN beneficios ON beneficios.id_beneficios = tipo_apoyo.meta_um_beneficio 
        INNER JOIN servicios ON servicios.id_servicios = tipo_apoyo.meta_um_servicio
        where programa.id_programa=$id_usuario_programa 
        group by id_tipo_apoyo ORDER BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo";
        $tabla1_exportar = $mysqli->query($tabla);
        $tabla1 = $mysqli->query($tabla);
        $tabla_rows = array();
        $tabla_table = array();

        $tabla_table['cols']= array(
            array('label'=>'Componente', 'type'=>'string'),
            array('label'=>'Actividad', 'type'=>'string'),
            array('label'=>'Monto ejercido', 'type'=>'number'),
            array('label'=>'U.M. Servicios', 'type'=>'string'),
            array('label'=>'Meta', 'type'=>'number'),
            array('label'=>'Avance', 'type'=>'number'),
            array('label'=>'%', 'type'=>'string'),
            array('label'=>'U.M. Beneficio', 'type'=>'string'),
            array('label'=>'Meta', 'type'=>'number'),
            array('label'=>'Avance', 'type'=>'number'),
            array('label'=>'%', 'type'=>'string'),

        );
        foreach($tabla1 as $b){
            $tabla_temp = array();
            $tabla_temp[] = array('v'=>(string) $b['nombre_componente']);
            $tabla_temp[] = array('v'=>(string) $b['nombre_tipo_apoyo']);
            $tabla_temp[] = array('v'=>(integer) $b['programa_presupuesto_ejercido_fechas']);
            $tabla_temp[] = array('v'=>(string) $b['nombre_servicios']);
            $tabla_temp[] = array('v'=>(integer) $b['meta_cantidad_servicio']);
            $tabla_temp[] = array('v'=>(integer) $b['meta_alcanzada_cantidad_servicio']);
            $tabla_temp[] = array('v'=>(string) $b['porcentaje_servicio']);
            $tabla_temp[] = array('v'=>(string) $b['nombre_beneficios']);
            $tabla_temp[] = array('v'=>(integer) $b['meta_cantidad_beneficio']);
            $tabla_temp[] = array('v'=>(integer) $b['meta_alcanzada_cantidad_beneficio']);
            $tabla_temp[] = array('v'=>(string) $b['porcentaje_beneficio']);
            $tabla_rows[] = array ('c'=>$tabla_temp);
        }
        $tabla_table['rows']=$tabla_rows;
        $tablajsonTable =json_encode($tabla_table);   //GRAFICO 12  

            ////////////////Desglose de eventos realizados

        $tabla_evento = "SELECT 
        evento.id_evento,
        evento.nombre_evento,
        evento.pob_obj_evento,
        evento.nivel_geo_evento,
        evento.lugar_evento,
        evento.monto_evento,
        evento.fecha_evento,
        evento.fecha_modificacion_evento,
        evento.usuario_evento,
        evento.id_accion_evento,
        evento.cantidad_servicio,
        evento.cantidad_beneficio,
        evento.hombres, 
        evento.mujeres,
        region.region,
        municipio.municipio,
        localidad.localidad, 
        tipo_apoyo.nombre_tipo_apoyo,
        servicios.nombre_servicios,
        beneficios.nombre_beneficios,
        componente.nombre_componente,
        COUNT(evento.hombres) AS tipo_apoyoA,       
        concat(round((evento.hombres/cantidad_beneficio * 100 ),2),'%') AS porcentaje_hombres,
        COUNT(evento.mujeres) AS tipo_apoyoA,       
        concat(round((evento.mujeres/cantidad_beneficio * 100 ),2),'%') AS porcentaje_mujeres  
        FROM
        evento
        INNER JOIN
        localidad ON localidad.cve_localidad = evento.lugar_evento
            INNER JOIN
        municipio ON municipio.cve_municipios = localidad.l_cve_municipio
            INNER JOIN
        region ON region.cve_regiones = municipio.m_cve_region
            INNER JOIN
        usuarios ON usuarios.id = evento.usuario_evento
            INNER JOIN
        tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
            INNER JOIN
        componente ON componente.id_componente = tipo_apoyo.ta_id_componente
            INNER JOIN
        programa ON componente.com_id_programa = programa.id_programa  
        INNER JOIN
        beneficios ON beneficios.id_beneficios = tipo_apoyo.meta_um_beneficio
                INNER JOIN
        servicios ON servicios.id_servicios = tipo_apoyo.meta_um_servicio
        where programa.id_programa=$id_usuario_programa 
        group by evento.id_evento
        ORDER BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento";
        
        $tabla1_evento_exportar = $mysqli->query($tabla_evento);
        $tabla1_evento = $mysqli->query($tabla_evento);
        $tabla_evento_rows = array();
        $tabla_evento_table = array();

        $tabla_evento_table['cols']= array(
            array('label'=>'Componente', 'type'=>'string'),
            array('label'=>'Actividad', 'type'=>'string'),
            array('label'=>'Evento', 'type'=>'string'),
            array('label'=>'Monto ejercido', 'type'=>'number'),
            array('label'=>'Fecha', 'type'=>'string'),
            array('label'=>'U.M. Servicios', 'type'=>'string'),
            array('label'=>'Meta alcanzada', 'type'=>'number'),
            array('label'=>'U.M. Beneficio', 'type'=>'string'),
            array('label'=>'Meta alcanzada', 'type'=>'number'),
            array('label'=>'Hombres', 'type'=>'number'),
            array('label'=>'%', 'type'=>'string'),
            array('label'=>'Mujeres', 'type'=>'number'),
            array('label'=>'%', 'type'=>'string'),             

        );
        foreach($tabla1_evento as $b){
            $tabla_evento_temp = array();
            $tabla_evento_temp[] = array('v'=>(string) $b['nombre_componente']);
            $tabla_evento_temp[] = array('v'=>(string) $b['nombre_tipo_apoyo']);
            $tabla_evento_temp[] = array('v'=>(string) $b['nombre_evento']);
            $tabla_evento_temp[] = array('v'=>(integer) $b['monto_evento']);
            $tabla_evento_temp[] = array('v'=>(string) $b['fecha_evento']);
            $tabla_evento_temp[] = array('v'=>(string) $b['nombre_servicios']);                    
            $tabla_evento_temp[] = array('v'=>(integer) $b['cantidad_servicio']);
            $tabla_evento_temp[] = array('v'=>(string) $b['nombre_beneficios']);
            $tabla_evento_temp[] = array('v'=>(integer) $b['cantidad_beneficio']);
            $tabla_evento_temp[] = array('v'=>(integer) $b['hombres']);
            $tabla_evento_temp[] = array('v'=>(string) $b['porcentaje_hombres']);
            $tabla_evento_temp[] = array('v'=>(integer) $b['mujeres']);
            $tabla_evento_temp[] = array('v'=>(string) $b['porcentaje_mujeres']);
            $tabla_evento_rows[] = array ('c'=>$tabla_evento_temp);
        }
        $tabla_evento_table['rows']=$tabla_evento_rows;
        $tabla_evento_jsonTable =json_encode($tabla_evento_table);  //GRAFICO 15


        //-----GRÁFICO POR OBJETO DEL GASTO
                //////////
        $pastel = "SELECT reg_partidas.id_reg_partidas, reg_partidas.partida, reg_partidas.cantidad_partidas, reg_partidas.monto_partidas, reg_partidas.id_evento_partidas,
        sum(reg_partidas.monto_partidas) as totalsuma,
        objeto_de_gasto.partida_gasto_texto,
        evento.fecha_evento,
        reg_partidas.monto_partidas,
        sum(reg_partidas.monto_partidas) as programa_presupuesto_ejercido_fechas
        FROM reg_partidas
        INNER JOIN evento on evento.id_evento = reg_partidas.id_evento_partidas
        INNER JOIN tipo_apoyo on tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
        INNER JOIN componente on componente.id_componente = tipo_apoyo.ta_id_componente
        INNER JOIN programa on programa.id_programa = componente.com_id_programa
        INNER JOIN objeto_de_gasto on objeto_de_gasto.clave = reg_partidas.partida
        where programa.id_programa=$id_usuario_programa
        group by reg_partidas.partida";

        $pastel1 = $mysqli->query($pastel);
        $pastel_rows = array();
        $pastel_table9 = array();

        $pastel_table9['cols']= array(
            array('label'=>'Objeto de gasto', 'type'=>'string'),
            array('label'=>'Presupuesto ejercido', 'type'=>'number')

        );
        foreach($pastel1 as $p){
            $pastel_temp = array();
            $pastel_temp[] = array('v'=>(string) $p['partida_gasto_texto']);
            $pastel_temp[] = array('v'=>(integer) $p['totalsuma']);
            $pastel_rows[] = array ('c'=>$pastel_temp);
        }
        $pastel_table9['rows']=$pastel_rows;
        $pasteljsonTable9 =json_encode($pastel_table9);  //GRAFICO 18

                        //////////
        ////////////////Tabla Presupuesto ejercido a nivel de objeto de gasto (partidas y monto ejercido)

        $tabla_partida = "SELECT 
        id_reg_partidas,
        partida,
        cantidad_partidas,
        monto_partidas,
        id_evento_partidas,
        partida_gasto_texto,
        evento.nombre_evento,
        programa.nombre_programa,
        componente.nombre_componente,
        tipo_apoyo.nombre_tipo_apoyo,
        capitulo,
        evento.fecha_evento,
        reg_partidas.monto_partidas,
        sum(reg_partidas.monto_partidas) as programa_presupuesto_ejercido_fechas
        FROM
        reg_partidas
        INNER JOIN
        objeto_de_gasto ON clave = partida
        INNER JOIN 
        evento ON evento.id_evento = reg_partidas.id_evento_partidas
        INNER JOIN 
        tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
        INNER JOIN 
        componente ON componente.id_componente = tipo_apoyo.ta_id_componente
        INNER JOIN
        programa ON programa.id_programa = componente.com_id_programa
        WHERE
        programa.id_programa=$id_usuario_programa 
        group by reg_partidas.id_reg_partidas
        ORDER BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento, reg_partidas.id_reg_partidas";
        $tabla1_partida_exportar = $mysqli->query($tabla_partida);
        $tabla1_partida = $mysqli->query($tabla_partida);
        $tabla_partida_rows = array();
        $tabla_partida_table = array();

        $tabla_partida_table['cols']= array(
            array('label'=>'Programa', 'type'=>'string'),
            array('label'=>'Componente', 'type'=>'string'),
            array('label'=>'Actividad', 'type'=>'string'),
            array('label'=>'Evento', 'type'=>'string'),
            array('label'=>'Capítulo', 'type'=>'number'),
            array('label'=>'Partida', 'type'=>'string'),
            array('label'=>'Monto', 'type'=>'number'),

        );
        foreach($tabla1_partida as $b){
            $tabla_partida_temp = array();
            $tabla_partida_temp[] = array('v'=>(string) $b['nombre_programa']);
            $tabla_partida_temp[] = array('v'=>(string) $b['nombre_componente']);
            $tabla_partida_temp[] = array('v'=>(string) $b['nombre_tipo_apoyo']);
            $tabla_partida_temp[] = array('v'=>(string) $b['nombre_evento']);
            $tabla_partida_temp[] = array('v'=>(integer) $b['capitulo']);
            $tabla_partida_temp[] = array('v'=>(string) $b['partida_gasto_texto']);
            $tabla_partida_temp[] = array('v'=>(integer) $b['monto_partidas']);
            $tabla_partida_rows[] = array ('c'=>$tabla_partida_temp);
        }
        $tabla_partida_table['rows']=$tabla_partida_rows;
        $tabla_partida_jsonTable =json_encode($tabla_partida_table);  //GRAFICO 19

                //-----GRÁFICO POR OBJETO DEL GASTO CAPÍTULO
                    //////////
        $pastel = "SELECT capitulo,  evento.fecha_evento, SUM(monto_partidas) AS capitulo_total
        FROM reg_partidas
        INNER JOIN evento on evento.id_evento = reg_partidas.id_evento_partidas
        INNER JOIN tipo_apoyo on tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
        INNER JOIN componente on componente.id_componente = tipo_apoyo.ta_id_componente
        INNER JOIN programa on programa.id_programa = componente.com_id_programa
        INNER JOIN objeto_de_gasto on objeto_de_gasto.clave = reg_partidas.partida
        where programa.id_programa=$id_usuario_programa 
        group by capitulo";

        $pastel1 = $mysqli->query($pastel);
        $pastel_rows = array();
        $pastel_table10 = array();

        $pastel_table10['cols']= array(
            array('label'=>'Capítulo', 'type'=>'string'),
            array('label'=>'Presupuesto ejercido', 'type'=>'number')

        );
        foreach($pastel1 as $p){
            $pastel_temp = array();
            $pastel_temp[] = array('v'=>(integer) $p['capitulo']);
            $pastel_temp[] = array('v'=>(integer) $p['capitulo_total']);
            $pastel_rows[] = array ('c'=>$pastel_temp);
        }
        $pastel_table10['rows']=$pastel_rows;
        $pasteljsonTable10 =json_encode($pastel_table10);  //GRAFICO 16

                    //////////

        ////////////////Tabla Presupuesto ejercido a nivel de objeto de gasto (capitulo y monto ejercido)

        $tabla_capitulo = "SELECT 
        programa.nombre_programa,
        componente.nombre_componente,
        tipo_apoyo.nombre_tipo_apoyo,
        evento.nombre_evento,
        capitulo,
        evento.fecha_evento,
        SUM(monto_partidas) AS capitulo_total
        FROM
        reg_partidas
            INNER JOIN
        objeto_de_gasto ON clave = partida
        INNER JOIN 
        evento ON evento.id_evento = reg_partidas.id_evento_partidas
        INNER JOIN 
        tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
        INNER JOIN 
        componente ON componente.id_componente = tipo_apoyo.ta_id_componente
        INNER JOIN
        programa ON programa.id_programa = componente.com_id_programa
        WHERE
        programa.id_programa=$id_usuario_programa 
        GROUP BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento, capitulo
        ORDER BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento, capitulo";
        $tabla1_capitulo_exportar = $mysqli->query($tabla_capitulo);
        $tabla1_capitulo = $mysqli->query($tabla_capitulo);
        $tabla_capitulo_rows = array();
        $tabla_capitulo_table = array();

        $tabla_capitulo_table['cols']= array(
            array('label'=>'Programa', 'type'=>'string'),
            array('label'=>'Componente', 'type'=>'string'),
            array('label'=>'Actividad', 'type'=>'string'),
            array('label'=>'Evento', 'type'=>'string'),
            array('label'=>'Capítulo', 'type'=>'number'),
            array('label'=>'Monto', 'type'=>'number'),

        );
        foreach($tabla1_capitulo as $b){
            $tabla_capitulo_temp = array();
            $tabla_capitulo_temp[] = array('v'=>(string) $b['nombre_programa']);
            $tabla_capitulo_temp[] = array('v'=>(string) $b['nombre_componente']);
            $tabla_capitulo_temp[] = array('v'=>(string) $b['nombre_tipo_apoyo']);
            $tabla_capitulo_temp[] = array('v'=>(string) $b['nombre_evento']);
            $tabla_capitulo_temp[] = array('v'=>(integer) $b['capitulo']);
            $tabla_capitulo_temp[] = array('v'=>(integer) $b['capitulo_total']);
            $tabla_capitulo_rows[] = array ('c'=>$tabla_capitulo_temp);
        }
        $tabla_capitulo_table['rows']=$tabla_capitulo_rows;
        $tabla_capitulo_jsonTable =json_encode($tabla_capitulo_table);  //GRAFICO 17 

////RESUMEN POR TIPO DE ACCIONES (TRANSVERSALIDAD, ACCIONES, PREVENCION)

$tabla_acciones = "SELECT programa.nombre_programa, tipo_apoyo.tipo_accion, sum(evento.cantidad_beneficio) as personas_atendidas, sum(evento.hombres) as hombres, sum(evento.mujeres) as mujeres,
sum(evento.monto_evento)as inversion_ejercido,
count(tipo_apoyo.tipo_accion) as acciones
FROM
    tipo_apoyo 
INNER JOIN
    componente ON componente.id_componente = tipo_apoyo.ta_id_componente
INNER JOIN
    programa ON componente.com_id_programa = programa.id_programa
INNER JOIN  
    evento ON evento.id_accion_evento = tipo_apoyo.id_tipo_apoyo   
WHERE programa.id_programa = $id_usuario_programa 
group by  programa.id_programa, tipo_accion
order by  tipo_accion";

$tabla_acciones1_exportar = $mysqli->query($tabla_acciones);
$tabla_acciones1 = $mysqli->query($tabla_acciones);
$tabla_acciones_rows = array();
$tabla_acciones_table = array();

$tabla_acciones_table['cols']= array(
    array('label'=>'Programa', 'type'=>'string'),
    array('label'=>'Tipo de Acción', 'type'=>'string'),
    array('label'=>'Personas Atendidas', 'type'=>'number'),
	array('label'=>'Hombres', 'type'=>'number'),
	array('label'=>'Mujeres', 'type'=>'number'),
    array('label'=>'Monto ejercido', 'type'=>'number'),  
    array('label'=>'Número de acciones', 'type'=>'number'),

);
    foreach($tabla_acciones1 as $b){
        $tabla_temp = array();
        $tabla_temp[] = array('v'=>(string) $b['nombre_programa']);
        $tabla_temp[] = array('v'=>(string) $b['tipo_accion']);
        $tabla_temp[] = array('v'=>(integer) $b['personas_atendidas']);
		$tabla_temp[] = array('v'=>(integer) $b['hombres']);
		$tabla_temp[] = array('v'=>(integer) $b['mujeres']);
        $tabla_temp[] = array('v'=>(integer) $b['inversion_ejercido']);
        $tabla_temp[] = array('v'=>(integer) $b['acciones']);
        $tabla_acciones_rows[] = array ('c'=>$tabla_temp);
    }
    $tabla_acciones_table['rows']=$tabla_acciones_rows;
    $tabla_accionesjsonTable =json_encode($tabla_acciones_table);  //GRAFICO 13  
////  
////DESGLOSE POR TIPO DE ACCIONES (TRANSVERSALIDAD, ACCIONES, PREVENCION)

$tabla_acciones = "SELECT programa.nombre_programa, componente.nombre_componente, tipo_apoyo.tipo_accion, evento.nombre_evento, sum(evento.cantidad_beneficio) as personas_atendidas, sum(evento.hombres) as hombres, sum(evento.mujeres) as mujeres,
sum(evento.monto_evento)as inversion_ejercido
FROM
	tipo_apoyo 
INNER JOIN
	componente ON componente.id_componente = tipo_apoyo.ta_id_componente
INNER JOIN
	programa ON componente.com_id_programa = programa.id_programa
INNER JOIN  
	evento ON evento.id_accion_evento = tipo_apoyo.id_tipo_apoyo   
WHERE programa.id_programa = $id_usuario_programa
group by programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento, evento.fecha_evento, tipo_accion
order by  tipo_accion";
$tabla_acciones1_desglose_exportar = $mysqli->query($tabla_acciones);
$tabla_acciones1 = $mysqli->query($tabla_acciones);
$tabla_acciones_rows = array();
$tabla_acciones_table = array();

$tabla_acciones_table['cols']= array(
    array('label'=>'Programa', 'type'=>'string'),
    array('label'=>'Componente', 'type'=>'string'),
    array('label'=>'Tipo de Acción', 'type'=>'string'),
    array('label'=>'Evento', 'type'=>'string'),
    array('label'=>'Personas Atendidas', 'type'=>'number'),
	array('label'=>'Hombres', 'type'=>'number'),
	array('label'=>'Mujeres', 'type'=>'number'),
    array('label'=>'Monto ejercido', 'type'=>'number'),  


);
    foreach($tabla_acciones1 as $b){
        $tabla_temp = array();    
        $tabla_temp[] = array('v'=>(string) $b['nombre_programa']);
        $tabla_temp[] = array('v'=>(string) $b['nombre_componente']);
        $tabla_temp[] = array('v'=>(string) $b['tipo_accion']);
        $tabla_temp[] = array('v'=>(string) $b['nombre_evento']);
        $tabla_temp[] = array('v'=>(integer) $b['personas_atendidas']);
		$tabla_temp[] = array('v'=>(integer) $b['hombres']);
		$tabla_temp[] = array('v'=>(integer) $b['mujeres']);
        $tabla_temp[] = array('v'=>(integer) $b['inversion_ejercido']);

        $tabla_acciones_rows[] = array ('c'=>$tabla_temp);
    }
    $tabla_acciones_table['rows']=$tabla_acciones_rows;
    $tabla_acciones_desglosejsonTable =json_encode($tabla_acciones_table);   //GRAFICO 14   
////
///////INICIO REGION, MUNICIPIO, GENERO ---EVENTO
//-----GRÁFICO POR REGION GRAFICO PAY

    $pastel = "SELECT  region.region, COUNT(region.region) AS Total_Cuenta 
    FROM
    evento
    INNER JOIN localidad ON localidad.cve_localidad = evento.lugar_evento
    INNER JOIN municipio ON municipio.cve_municipios = localidad.l_cve_municipio
    INNER JOIN region ON region.cve_regiones = municipio.m_cve_region
    INNER JOIN tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
    INNER JOIN componente ON componente.id_componente = tipo_apoyo.ta_id_componente
    INNER JOIN programa ON programa.id_programa = componente.com_id_programa
    WHERE programa.id_programa = $id_usuario_programa 
    GROUP BY region.region ORDER BY  region.region  ASC";


    $pastel1 = $mysqli->query($pastel);
    $pastel_rows = array();
    $pastel_table1evento = array();

    $pastel_table1evento['cols']= array(
        array('label'=>'Región', 'type'=>'string'),
        array('label'=>'Cantidad', 'type'=>'number')

    );
        foreach($pastel1 as $p){
            $pastel_temp = array();
            $pastel_temp[] = array('v'=>(string) $p['region']);
            $pastel_temp[] = array('v'=>(integer) $p['Total_Cuenta']);
            $pastel_rows[] = array ('c'=>$pastel_temp);
        }
        $pastel_table1evento['rows']=$pastel_rows;
        $pasteljsonTable1evento =json_encode($pastel_table1evento);

//////////

//-----GRÁFICO POR MUNICIPIO -GRÁFICO PAY

        ///////////

            $pastel = "SELECT  municipio.municipio, COUNT(municipio.municipio) AS Total_Cuenta 
            FROM
            evento 
	        INNER JOIN localidad ON localidad.cve_localidad = evento.lugar_evento
            INNER JOIN municipio ON municipio.cve_municipios = localidad.l_cve_municipio
            INNER JOIN region ON region.cve_regiones = municipio.m_cve_region
            INNER JOIN tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
            INNER JOIN componente ON componente.id_componente = tipo_apoyo.ta_id_componente
            INNER JOIN programa ON programa.id_programa = componente.com_id_programa
            WHERE programa.id_programa = $id_usuario_programa   
            GROUP BY municipio.municipio ORDER BY  municipio.municipio  ASC";

            $tabla_localidades = "SELECT programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento, evento.fecha_evento, region.region, municipio.municipio, localidad.localidad, localidad.latitud_dec, localidad.longitud_dec, COUNT(localidad.localidad) AS Total_Cuenta 
            FROM
            evento 
	        INNER JOIN localidad ON localidad.cve_localidad = evento.lugar_evento
            INNER JOIN municipio ON municipio.cve_municipios = localidad.l_cve_municipio
            INNER JOIN region ON region.cve_regiones = municipio.m_cve_region
            INNER JOIN tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
            INNER JOIN componente ON componente.id_componente = tipo_apoyo.ta_id_componente
            INNER JOIN programa ON programa.id_programa = componente.com_id_programa
            WHERE programa.id_programa = $id_usuario_programa   
            GROUP BY localidad.localidad ORDER BY  localidad.localidad  ASC";

        $tablalocexportar = $mysqli->query($tabla_localidades);
        $pastel1 = $mysqli->query($pastel);
        $pastel_rows = array();
        $pastel_table2evento = array();

        $pastel_table2evento['cols']= array(
            array('label'=>'Municipio %', 'type'=>'string'),
            array('label'=>'Cantidad', 'type'=>'number')

        );
            foreach($pastel1 as $p){
                $pastel_temp = array();
                $pastel_temp[] = array('v'=>(string) $p['municipio']);
                $pastel_temp[] = array('v'=>(integer) $p['Total_Cuenta']);
                $pastel_rows[] = array ('c'=>$pastel_temp);
            }
            $pastel_table2evento['rows']=$pastel_rows;
            $pasteljsonTable2evento =json_encode($pastel_table2evento);

        //////////           
//-----GRÁFICO POR GÉNERO
				$sqlh="UPDATE hombre_mujer SET hombre_mujer.cantidad_h_m =(SELECT SUM( evento.hombres) AS Total_CuentaH
                FROM
                evento 
	            INNER JOIN localidad ON localidad.cve_localidad = evento.lugar_evento
                INNER JOIN municipio ON municipio.cve_municipios = localidad.l_cve_municipio
                INNER JOIN region ON region.cve_regiones = municipio.m_cve_region
                INNER JOIN tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
                INNER JOIN componente ON componente.id_componente = tipo_apoyo.ta_id_componente
                INNER JOIN programa ON programa.id_programa = componente.com_id_programa
                WHERE programa.id_programa = $id_usuario_programa)
				WHERE hombre_mujer.h_m ='HOMBRE'";
				$mysqli->query($sqlh);
				
				$sqlm="UPDATE hombre_mujer SET hombre_mujer.cantidad_h_m =(SELECT SUM( evento.mujeres) AS Total_CuentaM 
                FROM
                evento 
	            INNER JOIN localidad ON localidad.cve_localidad = evento.lugar_evento
                INNER JOIN municipio ON municipio.cve_municipios = localidad.l_cve_municipio
                INNER JOIN region ON region.cve_regiones = municipio.m_cve_region
                INNER JOIN tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
                INNER JOIN componente ON componente.id_componente = tipo_apoyo.ta_id_componente
                INNER JOIN programa ON programa.id_programa = componente.com_id_programa
                WHERE programa.id_programa = $id_usuario_programa)
				WHERE hombre_mujer.h_m ='MUJER'";
				$mysqli->query($sqlm);
            //////////
				$pastel = "SELECT h_m, cantidad_h_m FROM hombre_mujer";
                $pastel1 = $mysqli->query($pastel);
                $pastel_rows = array();
                $pastel_table3evento = array();

                $pastel_table3evento['cols']= array(
                    array('label'=>'Género', 'type'=>'string'),
                    array('label'=>'Cantidad', 'type'=>'number')

                );
                    foreach($pastel1 as $p){
                        $pastel_temp = array();
                        $pastel_temp[] = array('v'=>(string) $p['h_m']);
                        $pastel_temp[] = array('v'=>(integer) $p['cantidad_h_m']);
                        $pastel_rows[] = array ('c'=>$pastel_temp);
                    }
                    $pastel_table3evento['rows']=$pastel_rows;
                    $pasteljsonTable3evento =json_encode($pastel_table3evento);

                
///////FIN REGION, MUNICIPIO, GENERO --- EVENTO
//// SI NO  SELECCIONA EL PROGRAMA
//*NOVENA CONSULTA VARIABLE FECHA
}// SI SOLO SELECIONA FECHAS
elseif (!empty($_GET['fechainicio']) && !empty($_GET['fechafinal'])) {

    $f_inicio =($_GET['fechainicio']);
    $f_final =($_GET['fechafinal']);
               
        //-----GRÁFICO PRESUPUESTO EJERCIDO POR ACTIVIDAD
        //////////
        if ($id_tipo_usuario != 1){
            $pastel = "SELECT tipo_apoyo.id_tipo_apoyo, tipo_apoyo.nombre_tipo_apoyo, tipo_apoyo.ta_id_componente, tipo_apoyo.meta_um_servicio, tipo_apoyo.meta_um_beneficio, tipo_apoyo.meta_cantidad_servicio, tipo_apoyo.meta_cantidad_beneficio, tipo_apoyo.meta_alcanzada_cantidad_servicio, tipo_apoyo.meta_alcanzada_cantidad_beneficio, tipo_apoyo.tipo_apoyo_presupuesto_ejercido, componente.nombre_componente, programa.nombre_programa, programa.id_programa, evento.fecha_evento,
            reg_partidas.monto_partidas, sum(reg_partidas.monto_partidas) as programa_presupuesto_ejercido_fechas  
            FROM  programa
            INNER JOIN componente on componente.com_id_programa = programa.id_programa
            INNER JOIN tipo_apoyo on tipo_apoyo.ta_id_componente = componente.id_componente
            INNER JOIN evento on evento.id_accion_evento = tipo_apoyo.id_tipo_apoyo
            INNER JOIN reg_partidas on reg_partidas.id_evento_partidas = evento.id_evento  
            where programa.id_programa=$id_usuario_programa  and (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento<=  ' + $f_final + ') and visible=1 ORDER BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo";
        }else{
            $pastel = "SELECT tipo_apoyo.id_tipo_apoyo, tipo_apoyo.nombre_tipo_apoyo, tipo_apoyo.ta_id_componente, tipo_apoyo.meta_um_beneficio, tipo_apoyo.meta_cantidad_servicio, tipo_apoyo.meta_cantidad_beneficio, tipo_apoyo.tipo_apoyo_presupuesto_ejercido, componente.nombre_componente, programa.nombre_programa, programa.id_programa, evento.fecha_evento,
            reg_partidas.monto_partidas, sum(reg_partidas.monto_partidas) as programa_presupuesto_ejercido_fechas 
            FROM programa
            INNER JOIN componente on componente.com_id_programa = programa.id_programa
            INNER JOIN tipo_apoyo on tipo_apoyo.ta_id_componente = componente.id_componente
            INNER JOIN evento on evento.id_accion_evento = tipo_apoyo.id_tipo_apoyo
            INNER JOIN reg_partidas on reg_partidas.id_evento_partidas = evento.id_evento  
            where (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento<=  ' + $f_final + ') and visible=1 ORDER BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo";
        }


        $pastel1 = $mysqli->query($pastel);
        $pastel_rows = array();
        $pastel_table6 = array();

        $pastel_table6['cols']= array(
            array('label'=>'Actividad', 'type'=>'string'),
            array('label'=>'Monto', 'type'=>'number')

        );
            foreach($pastel1 as $p){
                $pastel_temp = array();
                $pastel_temp[] = array('v'=>(string) $p['nombre_tipo_apoyo']);
                $pastel_temp[] = array('v'=>(integer) $p['programa_presupuesto_ejercido_fechas']);
                $pastel_rows[] = array ('c'=>$pastel_temp);
            }
            $pastel_table6['rows']=$pastel_rows;
            $pasteljsonTable6 =json_encode($pastel_table6);  //GRAFICO 11

        //////////Metas programadas vs Metas alcanzadas por Actividad

        if ($id_tipo_usuario != 1){
            $tabla = "SELECT tipo_apoyo.id_tipo_apoyo, tipo_apoyo.nombre_tipo_apoyo, tipo_apoyo.ta_id_componente, tipo_apoyo.meta_um_servicio, tipo_apoyo.meta_um_beneficio, tipo_apoyo.meta_cantidad_servicio, tipo_apoyo.meta_cantidad_beneficio, tipo_apoyo.meta_alcanzada_cantidad_servicio, tipo_apoyo.meta_alcanzada_cantidad_beneficio, tipo_apoyo.tipo_apoyo_presupuesto_ejercido, componente.nombre_componente, programa.nombre_programa, programa.id_programa,COUNT( meta_alcanzada_cantidad_servicio ) AS tipo_apoyoA,
            concat(round(( meta_alcanzada_cantidad_servicio/meta_cantidad_servicio * 100 ),2),'%') AS porcentaje_servicio,
            COUNT( meta_alcanzada_cantidad_beneficio ) AS tipo_apoyoA,
            concat(round(( meta_alcanzada_cantidad_beneficio/meta_cantidad_beneficio * 100 ),2),'%') AS porcentaje_beneficio,  servicios.nombre_servicios,  beneficios.nombre_beneficios,
            evento.fecha_evento,
            reg_partidas.monto_partidas,
            sum(reg_partidas.monto_partidas) as programa_presupuesto_ejercido_fechas  
            FROM programa
            INNER JOIN componente on componente.com_id_programa = programa.id_programa
            INNER JOIN tipo_apoyo on tipo_apoyo.ta_id_componente = componente.id_componente
            INNER JOIN evento on evento.id_accion_evento = tipo_apoyo.id_tipo_apoyo
            INNER JOIN reg_partidas on reg_partidas.id_evento_partidas = evento.id_evento  
            INNER JOIN beneficios ON beneficios.id_beneficios = tipo_apoyo.meta_um_beneficio 
            INNER JOIN servicios ON servicios.id_servicios = tipo_apoyo.meta_um_servicio
            where programa.id_programa=$id_usuario_programa and (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento<=  ' + $f_final + ') and visible=1 group by id_tipo_apoyo ORDER BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo";
            $tabla1_exportar = $mysqli->query($tabla);
        }else{
            $tabla = "SELECT tipo_apoyo.id_tipo_apoyo, tipo_apoyo.nombre_tipo_apoyo, tipo_apoyo.ta_id_componente, tipo_apoyo.meta_um_servicio, tipo_apoyo.meta_um_beneficio, tipo_apoyo.meta_cantidad_servicio, tipo_apoyo.meta_cantidad_beneficio, tipo_apoyo.meta_alcanzada_cantidad_servicio, tipo_apoyo.meta_alcanzada_cantidad_beneficio, tipo_apoyo.tipo_apoyo_presupuesto_ejercido, componente.nombre_componente, programa.nombre_programa, programa.id_programa,COUNT( meta_alcanzada_cantidad_servicio ) AS tipo_apoyoA,
            concat(round(( meta_alcanzada_cantidad_servicio/meta_cantidad_servicio * 100 ),2),'%') AS porcentaje_servicio,
            COUNT( meta_alcanzada_cantidad_beneficio ) AS tipo_apoyoA,
            concat(round(( meta_alcanzada_cantidad_beneficio/meta_cantidad_beneficio * 100 ),2),'%') AS porcentaje_beneficio, servicios.nombre_servicios,  beneficios.nombre_beneficios, 
            evento.fecha_evento,
            reg_partidas.monto_partidas,
            sum(reg_partidas.monto_partidas) as programa_presupuesto_ejercido_fechas 
            FROM programa
            INNER JOIN componente on componente.com_id_programa = programa.id_programa
            INNER JOIN tipo_apoyo on tipo_apoyo.ta_id_componente = componente.id_componente
            INNER JOIN evento on evento.id_accion_evento = tipo_apoyo.id_tipo_apoyo
            INNER JOIN reg_partidas on reg_partidas.id_evento_partidas = evento.id_evento  
            INNER JOIN beneficios ON beneficios.id_beneficios = tipo_apoyo.meta_um_beneficio 
            INNER JOIN servicios ON servicios.id_servicios = tipo_apoyo.meta_um_servicio 
            WHERE (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento<=  ' + $f_final + ') and visible=1
            group by id_tipo_apoyo ORDER BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo";
            $tabla1_exportar = $mysqli->query($tabla);
        }

            
            $tabla1 = $mysqli->query($tabla);
            $tabla_rows = array();
            $tabla_table = array();

            $tabla_table['cols']= array(
                array('label'=>'Componente', 'type'=>'string'),
                array('label'=>'Actividad', 'type'=>'string'),
                array('label'=>'Monto ejercido', 'type'=>'number'),
                array('label'=>'U.M. Servicios', 'type'=>'string'),
                array('label'=>'Meta', 'type'=>'number'),
                array('label'=>'Avance', 'type'=>'number'),
                array('label'=>'%', 'type'=>'string'),
                array('label'=>'U.M. Beneficio', 'type'=>'string'),
                array('label'=>'Meta', 'type'=>'number'),
                array('label'=>'Avance', 'type'=>'number'),
                array('label'=>'%', 'type'=>'string'),

            );
                foreach($tabla1 as $b){
                    $tabla_temp = array();
                    $tabla_temp[] = array('v'=>(string) $b['nombre_componente']);
                    $tabla_temp[] = array('v'=>(string) $b['nombre_tipo_apoyo']);
                    $tabla_temp[] = array('v'=>(integer) $b['programa_presupuesto_ejercido_fechas']);
                    $tabla_temp[] = array('v'=>(string) $b['nombre_servicios']);
                    $tabla_temp[] = array('v'=>(integer) $b['meta_cantidad_servicio']);
                    $tabla_temp[] = array('v'=>(integer) $b['meta_alcanzada_cantidad_servicio']);
                    $tabla_temp[] = array('v'=>(string) $b['porcentaje_servicio']);
                    $tabla_temp[] = array('v'=>(string) $b['nombre_beneficios']);
                    $tabla_temp[] = array('v'=>(integer) $b['meta_cantidad_beneficio']);
                    $tabla_temp[] = array('v'=>(integer) $b['meta_alcanzada_cantidad_beneficio']);
                    $tabla_temp[] = array('v'=>(string) $b['porcentaje_beneficio']);
                    $tabla_rows[] = array ('c'=>$tabla_temp);
                }
                $tabla_table['rows']=$tabla_rows;
                $tablajsonTable =json_encode($tabla_table);   //GRAFICO 12  

                ////////////////Desglose de eventos realizados
                if ($id_tipo_usuario != 1){
                $tabla_evento = "SELECT 
                evento.id_evento,
                evento.nombre_evento,
                evento.pob_obj_evento,
                evento.nivel_geo_evento,
                evento.lugar_evento,
                evento.monto_evento,
                evento.fecha_evento,
                evento.fecha_modificacion_evento,
                evento.usuario_evento,
                evento.id_accion_evento,
                evento.cantidad_servicio,
                evento.cantidad_beneficio,
                evento.hombres, 
                evento.mujeres,
                region.region,
                municipio.municipio,
                localidad.localidad, 
                tipo_apoyo.nombre_tipo_apoyo,
                servicios.nombre_servicios,
                beneficios.nombre_beneficios,
                componente.nombre_componente,
                COUNT(evento.hombres) AS tipo_apoyoA,       
                concat(round((evento.hombres/cantidad_beneficio * 100 ),2),'%') AS porcentaje_hombres,
                COUNT(evento.mujeres) AS tipo_apoyoA,       
                concat(round((evento.mujeres/cantidad_beneficio * 100 ),2),'%') AS porcentaje_mujeres  
                FROM
                evento
                INNER JOIN
                localidad ON localidad.cve_localidad = evento.lugar_evento
                    INNER JOIN
                municipio ON municipio.cve_municipios = localidad.l_cve_municipio
                    INNER JOIN
                region ON region.cve_regiones = municipio.m_cve_region
                    INNER JOIN
                usuarios ON usuarios.id = evento.usuario_evento
                    INNER JOIN
                tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
                    INNER JOIN
                componente ON componente.id_componente = tipo_apoyo.ta_id_componente
                    INNER JOIN
                programa ON componente.com_id_programa = programa.id_programa  
                INNER JOIN
                beneficios ON beneficios.id_beneficios = tipo_apoyo.meta_um_beneficio
                        INNER JOIN
                servicios ON servicios.id_servicios = tipo_apoyo.meta_um_servicio
                WHERE
                programa.id_programa=$id_usuario_programa and
                (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento<=  ' + $f_final + ') and visible=1
                group by evento.id_evento
                ORDER BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento";
                $tabla1_evento_exportar = $mysqli->query($tabla_evento);
                }else{
                    $tabla_evento = "SELECT 
                evento.id_evento,
                evento.nombre_evento,
                evento.pob_obj_evento,
                evento.nivel_geo_evento,
                evento.lugar_evento,
                evento.monto_evento,
                evento.fecha_evento,
                evento.fecha_modificacion_evento,
                evento.usuario_evento,
                evento.id_accion_evento,
                evento.cantidad_servicio,
                evento.cantidad_beneficio,
                evento.hombres, 
                evento.mujeres,
                region.region,
                municipio.municipio,
                localidad.localidad, 
                tipo_apoyo.nombre_tipo_apoyo,
                servicios.nombre_servicios,
                beneficios.nombre_beneficios,
                componente.nombre_componente,
                COUNT(evento.hombres) AS tipo_apoyoA,       
                concat(round((evento.hombres/cantidad_beneficio * 100 ),2),'%') AS porcentaje_hombres,
                COUNT(evento.mujeres) AS tipo_apoyoA,       
                concat(round((evento.mujeres/cantidad_beneficio * 100 ),2),'%') AS porcentaje_mujeres  
                FROM
                evento
                INNER JOIN
                localidad ON localidad.cve_localidad = evento.lugar_evento
                    INNER JOIN
                municipio ON municipio.cve_municipios = localidad.l_cve_municipio
                    INNER JOIN
                region ON region.cve_regiones = municipio.m_cve_region
                    INNER JOIN
                usuarios ON usuarios.id = evento.usuario_evento
                    INNER JOIN
                tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
                    INNER JOIN
                componente ON componente.id_componente = tipo_apoyo.ta_id_componente
                    INNER JOIN
                programa ON componente.com_id_programa = programa.id_programa  
                INNER JOIN
                beneficios ON beneficios.id_beneficios = tipo_apoyo.meta_um_beneficio
                        INNER JOIN
                servicios ON servicios.id_servicios = tipo_apoyo.meta_um_servicio
                WHERE
                (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento<=  ' + $f_final + ') and visible=1
                group by evento.id_evento
                ORDER BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento";
                $tabla1_evento_exportar = $mysqli->query($tabla_evento);
                }
                
                $tabla1_evento = $mysqli->query($tabla_evento);
                $tabla_evento_rows = array();
                $tabla_evento_table = array();

                $tabla_evento_table['cols']= array(
                    array('label'=>'Componente', 'type'=>'string'),
                    array('label'=>'Actividad', 'type'=>'string'),
                    array('label'=>'Evento', 'type'=>'string'),
                    array('label'=>'Monto ejercido', 'type'=>'number'),
                    array('label'=>'Fecha', 'type'=>'string'),
                    array('label'=>'U.M. Servicios', 'type'=>'string'),
                    array('label'=>'Meta alcanzada', 'type'=>'number'),
                    array('label'=>'U.M. Beneficio', 'type'=>'string'),
                    array('label'=>'Meta alcanzada', 'type'=>'number'),
                    array('label'=>'Hombres', 'type'=>'number'),
                    array('label'=>'%', 'type'=>'string'),
                    array('label'=>'Mujeres', 'type'=>'number'),
                    array('label'=>'%', 'type'=>'string'),             

                );
                    foreach($tabla1_evento as $b){
                        $tabla_evento_temp = array();
                        $tabla_evento_temp[] = array('v'=>(string) $b['nombre_componente']);
                        $tabla_evento_temp[] = array('v'=>(string) $b['nombre_tipo_apoyo']);
                        $tabla_evento_temp[] = array('v'=>(string) $b['nombre_evento']);
                        $tabla_evento_temp[] = array('v'=>(integer) $b['monto_evento']);
                        $tabla_evento_temp[] = array('v'=>(string) $b['fecha_evento']);
                        $tabla_evento_temp[] = array('v'=>(string) $b['nombre_servicios']);                    
                        $tabla_evento_temp[] = array('v'=>(integer) $b['cantidad_servicio']);
                        $tabla_evento_temp[] = array('v'=>(string) $b['nombre_beneficios']);
                        $tabla_evento_temp[] = array('v'=>(integer) $b['cantidad_beneficio']);
                        $tabla_evento_temp[] = array('v'=>(integer) $b['hombres']);
                        $tabla_evento_temp[] = array('v'=>(string) $b['porcentaje_hombres']);
                        $tabla_evento_temp[] = array('v'=>(integer) $b['mujeres']);
                        $tabla_evento_temp[] = array('v'=>(string) $b['porcentaje_mujeres']);
                        $tabla_evento_rows[] = array ('c'=>$tabla_evento_temp);
                    }
                    $tabla_evento_table['rows']=$tabla_evento_rows;
                    $tabla_evento_jsonTable =json_encode($tabla_evento_table);  //GRAFICO 15


            //-----GRÁFICO POR OBJETO DEL GASTO
                    //////////
                    if ($id_tipo_usuario != 1){
                        $pastel = "SELECT reg_partidas.id_reg_partidas, reg_partidas.partida, reg_partidas.cantidad_partidas, reg_partidas.monto_partidas, reg_partidas.id_evento_partidas,
                        sum(reg_partidas.monto_partidas) as totalsuma,
                        objeto_de_gasto.partida_gasto_texto,
                        evento.fecha_evento,
                        reg_partidas.monto_partidas,
                        sum(reg_partidas.monto_partidas) as programa_presupuesto_ejercido_fechas
                        FROM reg_partidas
                        INNER JOIN evento on evento.id_evento = reg_partidas.id_evento_partidas
                        INNER JOIN tipo_apoyo on tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
                        INNER JOIN componente on componente.id_componente = tipo_apoyo.ta_id_componente
                        INNER JOIN programa on programa.id_programa = componente.com_id_programa
                        INNER JOIN objeto_de_gasto on objeto_de_gasto.clave = reg_partidas.partida
                        where programa.id_programa=$id_usuario_programa and (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento<=  ' + $f_final + ') and visible=1
                        group by reg_partidas.partida";
                        }else{
                        $pastel = "SELECT reg_partidas.id_reg_partidas, reg_partidas.partida, reg_partidas.cantidad_partidas, reg_partidas.monto_partidas, reg_partidas.id_evento_partidas,
                        sum(reg_partidas.monto_partidas) as totalsuma,
                        objeto_de_gasto.partida_gasto_texto,
                                        evento.fecha_evento,
                        reg_partidas.monto_partidas,
                        sum(reg_partidas.monto_partidas) as programa_presupuesto_ejercido_fechas
                        FROM reg_partidas
                        INNER JOIN evento on evento.id_evento = reg_partidas.id_evento_partidas
                        INNER JOIN tipo_apoyo on tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
                        INNER JOIN componente on componente.id_componente = tipo_apoyo.ta_id_componente
                        INNER JOIN programa on programa.id_programa = componente.com_id_programa
                        INNER JOIN objeto_de_gasto on objeto_de_gasto.clave = reg_partidas.partida
                        WHERE
                        (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento<=  ' + $f_final + ') and visible=1
                        group by reg_partidas.partida";
                        }

                        $pastel1 = $mysqli->query($pastel);
                        $pastel_rows = array();
                        $pastel_table9 = array();

                        $pastel_table9['cols']= array(
                            array('label'=>'Objeto de gasto', 'type'=>'string'),
                            array('label'=>'Presupuesto ejercido', 'type'=>'number')

                        );
                            foreach($pastel1 as $p){
                                $pastel_temp = array();
                                $pastel_temp[] = array('v'=>(string) $p['partida_gasto_texto']);
                                $pastel_temp[] = array('v'=>(integer) $p['totalsuma']);
                                $pastel_rows[] = array ('c'=>$pastel_temp);
                            }
                            $pastel_table9['rows']=$pastel_rows;
                            $pasteljsonTable9 =json_encode($pastel_table9);  //GRAFICO 18

                        //////////
        ////////////////Tabla Presupuesto ejercido a nivel de objeto de gasto (partidas y monto ejercido)

        if ($id_tipo_usuario != 1){
            $tabla_partida = "SELECT 
            id_reg_partidas,
            partida,
            cantidad_partidas,
            monto_partidas,
            id_evento_partidas,
            partida_gasto_texto,
            evento.nombre_evento,
            programa.nombre_programa,
            componente.nombre_componente,
            tipo_apoyo.nombre_tipo_apoyo,
            capitulo,
            evento.fecha_evento,
            reg_partidas.monto_partidas,
            sum(reg_partidas.monto_partidas) as programa_presupuesto_ejercido_fechas
        FROM
            reg_partidas
                INNER JOIN
            objeto_de_gasto ON clave = partida
            INNER JOIN 
            evento ON evento.id_evento = reg_partidas.id_evento_partidas
            INNER JOIN 
            tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
            INNER JOIN 
            componente ON componente.id_componente = tipo_apoyo.ta_id_componente
            INNER JOIN
            programa ON programa.id_programa = componente.com_id_programa
            WHERE
            programa.id_programa=$id_usuario_programa and (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento<=  ' + $f_final + ') and visible=1
            group by reg_partidas.id_reg_partidas
            ORDER BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento, reg_partidas.id_reg_partidas";
            $tabla1_partida_exportar = $mysqli->query($tabla_partida);
        }else{
            $tabla_partida = "SELECT 
            id_reg_partidas,
            partida,
            cantidad_partidas,
            monto_partidas,
            id_evento_partidas,
            partida_gasto_texto,
            evento.nombre_evento,
            programa.nombre_programa,
            componente.nombre_componente,
            tipo_apoyo.nombre_tipo_apoyo,
            capitulo,
            evento.fecha_evento,
            reg_partidas.monto_partidas,
            sum(reg_partidas.monto_partidas) as programa_presupuesto_ejercido_fechas
        FROM
            reg_partidas
                INNER JOIN
            objeto_de_gasto ON clave = partida
            INNER JOIN 
            evento ON evento.id_evento = reg_partidas.id_evento_partidas
            INNER JOIN 
            tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
            INNER JOIN 
            componente ON componente.id_componente = tipo_apoyo.ta_id_componente
            INNER JOIN
            programa ON programa.id_programa = componente.com_id_programa
            WHERE
            (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento<=  ' + $f_final + ') and visible=1
            group by reg_partidas.id_reg_partidas
            ORDER BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento, reg_partidas.id_reg_partidas";
            $tabla1_partida_exportar = $mysqli->query($tabla_partida);
        }


            $tabla1_partida = $mysqli->query($tabla_partida);
            $tabla_partida_rows = array();
            $tabla_partida_table = array();

            $tabla_partida_table['cols']= array(
                array('label'=>'Programa', 'type'=>'string'),
                array('label'=>'Componente', 'type'=>'string'),
                array('label'=>'Actividad', 'type'=>'string'),
                array('label'=>'Evento', 'type'=>'string'),
                array('label'=>'Capítulo', 'type'=>'number'),
                array('label'=>'Partida', 'type'=>'string'),
                array('label'=>'Monto', 'type'=>'number'),

            );
                foreach($tabla1_partida as $b){
                    $tabla_partida_temp = array();
                    $tabla_partida_temp[] = array('v'=>(string) $b['nombre_programa']);
                    $tabla_partida_temp[] = array('v'=>(string) $b['nombre_componente']);
                    $tabla_partida_temp[] = array('v'=>(string) $b['nombre_tipo_apoyo']);
                    $tabla_partida_temp[] = array('v'=>(string) $b['nombre_evento']);
                    $tabla_partida_temp[] = array('v'=>(integer) $b['capitulo']);
                    $tabla_partida_temp[] = array('v'=>(string) $b['partida_gasto_texto']);
                    $tabla_partida_temp[] = array('v'=>(integer) $b['monto_partidas']);
                    $tabla_partida_rows[] = array ('c'=>$tabla_partida_temp);
                }
                $tabla_partida_table['rows']=$tabla_partida_rows;
                $tabla_partida_jsonTable =json_encode($tabla_partida_table);  //GRAFICO 19


                //-----GRÁFICO POR OBJETO DEL GASTO CAPÍTULO
                    //////////
                    if ($id_tipo_usuario != 1){
                    $pastel = "SELECT capitulo,  evento.fecha_evento, SUM(monto_partidas) AS capitulo_total
                    FROM reg_partidas
                    INNER JOIN evento on evento.id_evento = reg_partidas.id_evento_partidas
                    INNER JOIN tipo_apoyo on tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
                    INNER JOIN componente on componente.id_componente = tipo_apoyo.ta_id_componente
                    INNER JOIN programa on programa.id_programa = componente.com_id_programa
                    INNER JOIN objeto_de_gasto on objeto_de_gasto.clave = reg_partidas.partida
                    where programa.id_programa=$id_usuario_programa  and (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento<=  ' + $f_final + ') and visible=1
                    group by capitulo";
                    }else{
                        $pastel = "SELECT capitulo,  evento.fecha_evento, SUM(monto_partidas) AS capitulo_total
                    FROM reg_partidas
                    INNER JOIN evento on evento.id_evento = reg_partidas.id_evento_partidas
                    INNER JOIN tipo_apoyo on tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
                    INNER JOIN componente on componente.id_componente = tipo_apoyo.ta_id_componente
                    INNER JOIN programa on programa.id_programa = componente.com_id_programa
                    INNER JOIN objeto_de_gasto on objeto_de_gasto.clave = reg_partidas.partida
                    where (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento<=  ' + $f_final + ') and visible=1
                    group by capitulo";

                    }
                    $pastel1 = $mysqli->query($pastel);
                    $pastel_rows = array();
                    $pastel_table10 = array();

                    $pastel_table10['cols']= array(
                        array('label'=>'Capítulo', 'type'=>'string'),
                        array('label'=>'Presupuesto ejercido', 'type'=>'number')

                    );
                        foreach($pastel1 as $p){
                            $pastel_temp = array();
                            $pastel_temp[] = array('v'=>(integer) $p['capitulo']);
                            $pastel_temp[] = array('v'=>(integer) $p['capitulo_total']);
                            $pastel_rows[] = array ('c'=>$pastel_temp);
                        }
                        $pastel_table10['rows']=$pastel_rows;
                        $pasteljsonTable10 =json_encode($pastel_table10);  //GRAFICO 16

                    //////////

                    ////////////////Tabla Presupuesto ejercido a nivel de objeto de gasto (capitulo y monto ejercido)
                    if ($id_tipo_usuario != 1){
                    $tabla_capitulo = "SELECT 
                    programa.nombre_programa,
                    componente.nombre_componente,
                    tipo_apoyo.nombre_tipo_apoyo,
                    evento.nombre_evento,
                    capitulo,
                    evento.fecha_evento,
                    SUM(monto_partidas) AS capitulo_total
                FROM
                    reg_partidas
                        INNER JOIN
                    objeto_de_gasto ON clave = partida
                    INNER JOIN 
                    evento ON evento.id_evento = reg_partidas.id_evento_partidas
                    INNER JOIN 
                    tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
                    INNER JOIN 
                    componente ON componente.id_componente = tipo_apoyo.ta_id_componente
                    INNER JOIN
                    programa ON programa.id_programa = componente.com_id_programa
                    WHERE
                    programa.id_programa=$id_usuario_programa and (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento<=  ' + $f_final + ') and visible=1
                    GROUP BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento, capitulo
                    ORDER BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento, capitulo";
                    $tabla1_capitulo_exportar = $mysqli->query($tabla_capitulo);
                    }else{
                        $tabla_capitulo = "SELECT 
                        capitulo,
                        evento.fecha_evento,
                        SUM(monto_partidas) AS capitulo_total
                    FROM
                        reg_partidas
                            INNER JOIN
                        objeto_de_gasto ON clave = partida
                        INNER JOIN 
                        evento ON evento.id_evento = reg_partidas.id_evento_partidas
                        INNER JOIN 
                        tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
                        INNER JOIN 
                        componente ON componente.id_componente = tipo_apoyo.ta_id_componente
                        INNER JOIN
                        programa ON programa.id_programa = componente.com_id_programa
                        WHERE
                        (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento<=  ' + $f_final + ') and visible=1
                        GROUP BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento, capitulo
                        ORDER BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento, capitulo";
                        $tabla1_capitulo_exportar = $mysqli->query($tabla_capitulo);
                    }
                    $tabla1_capitulo = $mysqli->query($tabla_capitulo);
                    $tabla_capitulo_rows = array();
                    $tabla_capitulo_table = array();

                    $tabla_capitulo_table['cols']= array(
                        array('label'=>'Programa', 'type'=>'string'),
                        array('label'=>'Componente', 'type'=>'string'),
                        array('label'=>'Actividad', 'type'=>'string'),
                        array('label'=>'Evento', 'type'=>'string'),
                        array('label'=>'Capítulo', 'type'=>'number'),
                        array('label'=>'Monto', 'type'=>'number'),

                    );
                        foreach($tabla1_capitulo as $b){
                            $tabla_capitulo_temp = array();
                            $tabla_capitulo_temp[] = array('v'=>(string) $b['nombre_programa']);
                            $tabla_capitulo_temp[] = array('v'=>(string) $b['nombre_componente']);
                            $tabla_capitulo_temp[] = array('v'=>(string) $b['nombre_tipo_apoyo']);
                            $tabla_capitulo_temp[] = array('v'=>(string) $b['nombre_evento']);
                            $tabla_capitulo_temp[] = array('v'=>(integer) $b['capitulo']);
                            $tabla_capitulo_temp[] = array('v'=>(integer) $b['capitulo_total']);
                            $tabla_capitulo_rows[] = array ('c'=>$tabla_capitulo_temp);
                        }
                        $tabla_capitulo_table['rows']=$tabla_capitulo_rows;
                        $tabla_capitulo_jsonTable =json_encode($tabla_capitulo_table);  //GRAFICO 17 

////RESUMEN POR TIPO DE ACCIONES (TRANSVERSALIDAD, ACCIONES, PREVENCION)
if ($id_tipo_usuario != 1){
    $tabla_acciones = "SELECT programa.nombre_programa, tipo_apoyo.tipo_accion, sum(evento.cantidad_beneficio) as personas_atendidas, sum(evento.hombres) as hombres, sum(evento.mujeres) as mujeres, sum(evento.hombres) as hombres, sum(evento.mujeres) as mujeres,
    sum(evento.monto_evento)as inversion_ejercido,
    count(tipo_apoyo.tipo_accion) as acciones
    FROM
        tipo_apoyo 
    INNER JOIN
        componente ON componente.id_componente = tipo_apoyo.ta_id_componente
    INNER JOIN
        programa ON componente.com_id_programa = programa.id_programa
    INNER JOIN  
        evento ON evento.id_accion_evento = tipo_apoyo.id_tipo_apoyo   
    WHERE programa.id_programa = $id_usuario_programa  AND  (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento <=  ' + $f_final + ') and visible=1
    group by  programa.id_programa, tipo_accion
    order by  tipo_accion";
    $tabla_acciones1_exportar = $mysqli->query($tabla_acciones);
}else{
    $tabla_acciones = "SELECT programa.nombre_programa, tipo_apoyo.tipo_accion, sum(evento.cantidad_beneficio) as personas_atendidas, sum(evento.hombres) as hombres, sum(evento.mujeres) as mujeres, sum(evento.hombres) as hombres, sum(evento.mujeres) as mujeres,
    sum(evento.monto_evento)as inversion_ejercido,
    count(tipo_apoyo.tipo_accion) as acciones
    FROM
        tipo_apoyo 
    INNER JOIN
        componente ON componente.id_componente = tipo_apoyo.ta_id_componente
    INNER JOIN
        programa ON componente.com_id_programa = programa.id_programa
    INNER JOIN  
        evento ON evento.id_accion_evento = tipo_apoyo.id_tipo_apoyo   
    WHERE  (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento <=  ' + $f_final + ') and visible=1
    group by  programa.id_programa, tipo_accion
    order by  tipo_accion";
    $tabla_acciones1_exportar = $mysqli->query($tabla_acciones);
}    
    $tabla_acciones1 = $mysqli->query($tabla_acciones);
    $tabla_acciones_rows = array();
    $tabla_acciones_table = array();

    $tabla_acciones_table['cols']= array(
        array('label'=>'Programa', 'type'=>'string'),
        array('label'=>'Tipo de Acción', 'type'=>'string'),
        array('label'=>'Personas Atendidas', 'type'=>'number'),
		array('label'=>'Hombres', 'type'=>'number'),
		array('label'=>'Mujeres', 'type'=>'number'),
        array('label'=>'Monto ejercido', 'type'=>'number'),  
        array('label'=>'Número de acciones', 'type'=>'number'),

    );
        foreach($tabla_acciones1 as $b){
            $tabla_temp = array();
            $tabla_temp[] = array('v'=>(string) $b['nombre_programa']);
            $tabla_temp[] = array('v'=>(string) $b['tipo_accion']);
            $tabla_temp[] = array('v'=>(integer) $b['personas_atendidas']);
			$tabla_temp[] = array('v'=>(integer) $b['hombres']);
			$tabla_temp[] = array('v'=>(integer) $b['mujeres']);
            $tabla_temp[] = array('v'=>(integer) $b['inversion_ejercido']);
            $tabla_temp[] = array('v'=>(integer) $b['acciones']);
            $tabla_acciones_rows[] = array ('c'=>$tabla_temp);
        }
        $tabla_acciones_table['rows']=$tabla_acciones_rows;
        $tabla_accionesjsonTable =json_encode($tabla_acciones_table);  //GRAFICO 13     
////  

////DESGLOSE POR TIPO DE ACCIONES (TRANSVERSALIDAD, ACCIONES, PREVENCION)
    if ($id_tipo_usuario != 1){
    $tabla_acciones = "SELECT programa.nombre_programa, componente.nombre_componente, tipo_apoyo.tipo_accion, evento.nombre_evento, sum(evento.cantidad_beneficio) as personas_atendidas, sum(evento.hombres) as hombres, sum(evento.mujeres) as mujeres,
        sum(evento.monto_evento)as inversion_ejercido
        FROM
            tipo_apoyo 
        INNER JOIN
            componente ON componente.id_componente = tipo_apoyo.ta_id_componente
        INNER JOIN
            programa ON componente.com_id_programa = programa.id_programa
        INNER JOIN  
            evento ON evento.id_accion_evento = tipo_apoyo.id_tipo_apoyo   
        WHERE programa.id_programa = $id_usuario_programa AND  (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento <=  ' + $f_final + ') and visible=1
        group by programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento, evento.fecha_evento, tipo_accion
        order by  tipo_accion";
        $tabla_acciones1_desglose_exportar = $mysqli->query($tabla_acciones);
    }else{
        $tabla_acciones = "SELECT programa.nombre_programa, componente.nombre_componente, tipo_apoyo.tipo_accion, evento.nombre_evento, sum(evento.cantidad_beneficio) as personas_atendidas, sum(evento.hombres) as hombres, sum(evento.mujeres) as mujeres,
        sum(evento.monto_evento)as inversion_ejercido
        FROM
            tipo_apoyo 
        INNER JOIN
            componente ON componente.id_componente = tipo_apoyo.ta_id_componente
        INNER JOIN
            programa ON componente.com_id_programa = programa.id_programa
        INNER JOIN  
            evento ON evento.id_accion_evento = tipo_apoyo.id_tipo_apoyo   
        WHERE (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento <=  ' + $f_final + ') and visible=1
        group by programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento, evento.fecha_evento, tipo_accion
        order by  tipo_accion";
        $tabla_acciones1_desglose_exportar = $mysqli->query($tabla_acciones);
    }
    $tabla_acciones1 = $mysqli->query($tabla_acciones);
    $tabla_acciones_rows = array();
    $tabla_acciones_table = array();
    
    $tabla_acciones_table['cols']= array(
        array('label'=>'Programa', 'type'=>'string'),
        array('label'=>'Componente', 'type'=>'string'),
        array('label'=>'Tipo de Acción', 'type'=>'string'),
        array('label'=>'Evento', 'type'=>'string'),
        array('label'=>'Personas Atendidas', 'type'=>'number'),
		array('label'=>'Hombres', 'type'=>'number'),
		array('label'=>'Mujeres', 'type'=>'number'),
        array('label'=>'Monto ejercido', 'type'=>'number'),  
    
    
    );
        foreach($tabla_acciones1 as $b){
            $tabla_temp = array();    
            $tabla_temp[] = array('v'=>(string) $b['nombre_programa']);
            $tabla_temp[] = array('v'=>(string) $b['nombre_componente']);
            $tabla_temp[] = array('v'=>(string) $b['tipo_accion']);
            $tabla_temp[] = array('v'=>(string) $b['nombre_evento']);
            $tabla_temp[] = array('v'=>(integer) $b['personas_atendidas']);
			$tabla_temp[] = array('v'=>(integer) $b['hombres']);
			$tabla_temp[] = array('v'=>(integer) $b['mujeres']);
            $tabla_temp[] = array('v'=>(integer) $b['inversion_ejercido']);
    
            $tabla_acciones_rows[] = array ('c'=>$tabla_temp);
        }
        $tabla_acciones_table['rows']=$tabla_acciones_rows;
        $tabla_acciones_desglosejsonTable =json_encode($tabla_acciones_table);   //GRAFICO 14  
////
///////INICIO REGION, MUNICIPIO, GENERO ---EVENTO
//-----GRÁFICO POR REGION GRAFICO PAY
if ($id_tipo_usuario != 1){
    $pastel = "SELECT  region.region, COUNT(region.region) AS Total_Cuenta 
    FROM
    evento
    INNER JOIN localidad ON localidad.cve_localidad = evento.lugar_evento
    INNER JOIN municipio ON municipio.cve_municipios = localidad.l_cve_municipio
    INNER JOIN region ON region.cve_regiones = municipio.m_cve_region
    INNER JOIN tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
    INNER JOIN componente ON componente.id_componente = tipo_apoyo.ta_id_componente
    INNER JOIN programa ON programa.id_programa = componente.com_id_programa
    WHERE
    programa.id_programa=$id_usuario_programa
    AND (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento  <=  ' + $f_final + ') and visible=1 
    GROUP BY region.region ORDER BY  region.region  ASC";
}else{
    $pastel = "SELECT  region.region, COUNT(region.region) AS Total_Cuenta 
    FROM
    evento
    INNER JOIN localidad ON localidad.cve_localidad = evento.lugar_evento
    INNER JOIN municipio ON municipio.cve_municipios = localidad.l_cve_municipio
    INNER JOIN region ON region.cve_regiones = municipio.m_cve_region
    INNER JOIN tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
    INNER JOIN componente ON componente.id_componente = tipo_apoyo.ta_id_componente
    INNER JOIN programa ON programa.id_programa = componente.com_id_programa
    WHERE (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento  <=  ' + $f_final + ') and visible=1 
    GROUP BY region.region ORDER BY  region.region  ASC";
}

$pastel1 = $mysqli->query($pastel);
$pastel_rows = array();
$pastel_table1evento = array();

$pastel_table1evento['cols']= array(
    array('label'=>'Región', 'type'=>'string'),
    array('label'=>'Cantidad', 'type'=>'number')

);
    foreach($pastel1 as $p){
        $pastel_temp = array();
        $pastel_temp[] = array('v'=>(string) $p['region']);
        $pastel_temp[] = array('v'=>(integer) $p['Total_Cuenta']);
        $pastel_rows[] = array ('c'=>$pastel_temp);
    }
    $pastel_table1evento['rows']=$pastel_rows;
    $pasteljsonTable1evento =json_encode($pastel_table1evento);

//////////

//-----GRÁFICO POR MUNICIPIO -GRÁFICO PAY

    ///////////
    if ($id_tipo_usuario != 1){
        $pastel = "SELECT  municipio.municipio, COUNT(municipio.municipio) AS Total_Cuenta 
        FROM
        evento 
        INNER JOIN localidad ON localidad.cve_localidad = evento.lugar_evento
        INNER JOIN municipio ON municipio.cve_municipios = localidad.l_cve_municipio
        INNER JOIN region ON region.cve_regiones = municipio.m_cve_region
        INNER JOIN tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
        INNER JOIN componente ON componente.id_componente = tipo_apoyo.ta_id_componente
        INNER JOIN programa ON programa.id_programa = componente.com_id_programa
        WHERE
        programa.id_programa=$id_usuario_programa
        AND (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento <=  ' + $f_final + ') and visible=1  
        GROUP BY municipio.municipio ORDER BY  municipio.municipio  ASC";

        $tabla_localidades = "SELECT programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento, evento.fecha_evento, region.region, municipio.municipio, localidad.localidad, localidad.latitud_dec, localidad.longitud_dec, COUNT(localidad.localidad) AS Total_Cuenta 
        FROM
        evento 
        INNER JOIN localidad ON localidad.cve_localidad = evento.lugar_evento
        INNER JOIN municipio ON municipio.cve_municipios = localidad.l_cve_municipio
        INNER JOIN region ON region.cve_regiones = municipio.m_cve_region
        INNER JOIN tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
        INNER JOIN componente ON componente.id_componente = tipo_apoyo.ta_id_componente
        INNER JOIN programa ON programa.id_programa = componente.com_id_programa
        WHERE
        programa.id_programa=$id_usuario_programa
        AND (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento <=  ' + $f_final + ') and visible=1 
        GROUP BY localidad.localidad ORDER BY  localidad.localidad  ASC";
    }else{
        $pastel = "SELECT  municipio.municipio, COUNT(municipio.municipio) AS Total_Cuenta 
        FROM
        evento 
        INNER JOIN localidad ON localidad.cve_localidad = evento.lugar_evento
        INNER JOIN municipio ON municipio.cve_municipios = localidad.l_cve_municipio
        INNER JOIN region ON region.cve_regiones = municipio.m_cve_region
        INNER JOIN tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
        INNER JOIN componente ON componente.id_componente = tipo_apoyo.ta_id_componente
        INNER JOIN programa ON programa.id_programa = componente.com_id_programa
        WHERE (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento <=  ' + $f_final + ') and visible=1   
        GROUP BY municipio.municipio ORDER BY  municipio.municipio  ASC";

        $tabla_localidades = "SELECT programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento, evento.fecha_evento, region.region, municipio.municipio, localidad.localidad, localidad.latitud_dec, localidad.longitud_dec, COUNT(localidad.localidad) AS Total_Cuenta 
        FROM
        evento 
        INNER JOIN localidad ON localidad.cve_localidad = evento.lugar_evento
        INNER JOIN municipio ON municipio.cve_municipios = localidad.l_cve_municipio
        INNER JOIN region ON region.cve_regiones = municipio.m_cve_region
        INNER JOIN tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
        INNER JOIN componente ON componente.id_componente = tipo_apoyo.ta_id_componente
        INNER JOIN programa ON programa.id_programa = componente.com_id_programa
        WHERE (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento <=  ' + $f_final + ') and visible=1 
        GROUP BY localidad.localidad ORDER BY  localidad.localidad  ASC";
    }
        $tablalocexportar = $mysqli->query($tabla_localidades);
    $pastel1 = $mysqli->query($pastel);
    $pastel_rows = array();
    $pastel_table2evento = array();

    $pastel_table2evento['cols']= array(
        array('label'=>'Municipio %', 'type'=>'string'),
        array('label'=>'Cantidad', 'type'=>'number')

    );
        foreach($pastel1 as $p){
            $pastel_temp = array();
            $pastel_temp[] = array('v'=>(string) $p['municipio']);
            $pastel_temp[] = array('v'=>(integer) $p['Total_Cuenta']);
            $pastel_rows[] = array ('c'=>$pastel_temp);
        }
        $pastel_table2evento['rows']=$pastel_rows;
        $pasteljsonTable2evento =json_encode($pastel_table2evento);

    //////////              
//-----GRÁFICO POR GÉNERO
        if ($id_tipo_usuario != 1){
            $sqlh="UPDATE hombre_mujer SET hombre_mujer.cantidad_h_m =(SELECT SUM( evento.hombres) AS Total_CuentaH
            FROM
            evento 
            INNER JOIN localidad ON localidad.cve_localidad = evento.lugar_evento
            INNER JOIN municipio ON municipio.cve_municipios = localidad.l_cve_municipio
            INNER JOIN region ON region.cve_regiones = municipio.m_cve_region
            INNER JOIN tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
            INNER JOIN componente ON componente.id_componente = tipo_apoyo.ta_id_componente
            INNER JOIN programa ON programa.id_programa = componente.com_id_programa
            WHERE
            programa.id_programa=$id_usuario_programa
            AND (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento <=  ' + $f_final + ') and visible=1)
            WHERE hombre_mujer.h_m ='HOMBRE'";
            $mysqli->query($sqlh);
            
            $sqlm="UPDATE hombre_mujer SET hombre_mujer.cantidad_h_m =(SELECT SUM( evento.mujeres) AS Total_CuentaM 
            FROM
            evento 
            INNER JOIN localidad ON localidad.cve_localidad = evento.lugar_evento
            INNER JOIN municipio ON municipio.cve_municipios = localidad.l_cve_municipio
            INNER JOIN region ON region.cve_regiones = municipio.m_cve_region
            INNER JOIN tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
            INNER JOIN componente ON componente.id_componente = tipo_apoyo.ta_id_componente
            INNER JOIN programa ON programa.id_programa = componente.com_id_programa
            WHERE
            programa.id_programa=$id_usuario_programa
            AND (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento <=  ' + $f_final + ') and visible=1) 
            WHERE hombre_mujer.h_m ='MUJER'";
            $mysqli->query($sqlm);
        }else{
            $sqlh="UPDATE hombre_mujer SET hombre_mujer.cantidad_h_m =(SELECT SUM( evento.hombres) AS Total_CuentaH
            FROM
            evento 
            INNER JOIN localidad ON localidad.cve_localidad = evento.lugar_evento
            INNER JOIN municipio ON municipio.cve_municipios = localidad.l_cve_municipio
            INNER JOIN region ON region.cve_regiones = municipio.m_cve_region
            INNER JOIN tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
            INNER JOIN componente ON componente.id_componente = tipo_apoyo.ta_id_componente
            INNER JOIN programa ON programa.id_programa = componente.com_id_programa
            WHERE (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento <=  ' + $f_final + ') and visible=1)
            WHERE hombre_mujer.h_m ='HOMBRE'";
            $mysqli->query($sqlh);
            
            $sqlm="UPDATE hombre_mujer SET hombre_mujer.cantidad_h_m =(SELECT SUM( evento.mujeres) AS Total_CuentaM 
            FROM
            evento 
            INNER JOIN localidad ON localidad.cve_localidad = evento.lugar_evento
            INNER JOIN municipio ON municipio.cve_municipios = localidad.l_cve_municipio
            INNER JOIN region ON region.cve_regiones = municipio.m_cve_region
            INNER JOIN tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
            INNER JOIN componente ON componente.id_componente = tipo_apoyo.ta_id_componente
            INNER JOIN programa ON programa.id_programa = componente.com_id_programa
            WHERE (evento.fecha_evento >= ' + $f_inicio + ' and  evento.fecha_evento <=  ' + $f_final + ') and visible=1)
            WHERE hombre_mujer.h_m ='MUJER'";
            $mysqli->query($sqlm);
        }
        //////////
            $pastel = "SELECT h_m, cantidad_h_m FROM hombre_mujer";
            $pastel1 = $mysqli->query($pastel);
            $pastel_rows = array();
            $pastel_table3evento = array();

            $pastel_table3evento['cols']= array(
                array('label'=>'Género', 'type'=>'string'),
                array('label'=>'Cantidad', 'type'=>'number')

            );
                foreach($pastel1 as $p){
                    $pastel_temp = array();
                    $pastel_temp[] = array('v'=>(string) $p['h_m']);
                    $pastel_temp[] = array('v'=>(integer) $p['cantidad_h_m']);
                    $pastel_rows[] = array ('c'=>$pastel_temp);
                }
                $pastel_table3evento['rows']=$pastel_rows;
                $pasteljsonTable3evento =json_encode($pastel_table3evento);

            
///////FIN REGION, MUNICIPIO, GENERO --- EVENTO
//// SI NO  SELECCIONA EL LAS FECHAS
}
//*DECIMA CONSULTA SIN SELECCIONAR NADA INICIO GENERAL COMPARANDO SI ES ADMIN/USUARIO
else
{

//-----GRÁFICO POR OBJETO DEL GASTO

        if ($id_tipo_usuario != 1){
            $pastel = "SELECT reg_partidas.id_reg_partidas, reg_partidas.partida, reg_partidas.cantidad_partidas, reg_partidas.monto_partidas, reg_partidas.id_evento_partidas,
            sum(reg_partidas.monto_partidas) as totalsuma,
            objeto_de_gasto.partida_gasto_texto
            FROM reg_partidas
            INNER JOIN evento on evento.id_evento = reg_partidas.id_evento_partidas
            INNER JOIN tipo_apoyo on tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
            INNER JOIN componente on componente.id_componente = tipo_apoyo.ta_id_componente
            INNER JOIN programa on programa.id_programa = componente.com_id_programa
            INNER JOIN objeto_de_gasto on objeto_de_gasto.clave = reg_partidas.partida
            WHERE programa.id_programa = $id_usuario_programa and visible=1
            GROUP BY  reg_partidas.partida";
        }else{
            $pastel = "SELECT reg_partidas.id_reg_partidas, reg_partidas.partida, reg_partidas.cantidad_partidas, reg_partidas.monto_partidas, reg_partidas.id_evento_partidas,
            sum(reg_partidas.monto_partidas) as totalsuma,
            objeto_de_gasto.partida_gasto_texto
            FROM reg_partidas
            INNER JOIN evento on evento.id_evento = reg_partidas.id_evento_partidas
            INNER JOIN tipo_apoyo on tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
            INNER JOIN componente on componente.id_componente = tipo_apoyo.ta_id_componente
            INNER JOIN programa on programa.id_programa = componente.com_id_programa
            INNER JOIN objeto_de_gasto on objeto_de_gasto.clave = reg_partidas.partida
            where visible=1
            GROUP BY  reg_partidas.partida";
            }

            $pastel1 = $mysqli->query($pastel);
            $pastel_rows = array();
            $pastel_table9 = array();

            $pastel_table9['cols']= array(
                array('label'=>'Objeto de gasto', 'type'=>'string'),
                array('label'=>'Presupuesto ejercido', 'type'=>'number')

            );
            foreach($pastel1 as $p){
                $pastel_temp = array();
                $pastel_temp[] = array('v'=>(string) $p['partida_gasto_texto']);
                $pastel_temp[] = array('v'=>(integer) $p['totalsuma']);
                $pastel_rows[] = array ('c'=>$pastel_temp);
            }
            $pastel_table9['rows']=$pastel_rows;
            $pasteljsonTable9 =json_encode($pastel_table9);  //GRAFICO 18


//-----GRÁFICO PRESUPUESTO EJERCIDO POR ACTIVIDAD 

        if ($id_tipo_usuario != 1){
            $pastel = "SELECT tipo_apoyo.id_tipo_apoyo, tipo_apoyo.nombre_tipo_apoyo, tipo_apoyo.ta_id_componente, tipo_apoyo.meta_um_servicio, tipo_apoyo.meta_um_beneficio, tipo_apoyo.meta_cantidad_servicio, tipo_apoyo.meta_cantidad_beneficio, tipo_apoyo.meta_alcanzada_cantidad_servicio, tipo_apoyo.meta_alcanzada_cantidad_beneficio, tipo_apoyo.tipo_apoyo_presupuesto_ejercido, componente.nombre_componente, programa.nombre_programa, programa.id_programa  FROM tipo_apoyo INNER JOIN componente on componente.id_componente = tipo_apoyo.ta_id_componente INNER JOIN programa on componente.com_id_programa = programa.id_programa where programa.id_programa=$id_usuario_programa and visible=1 group by id_tipo_apoyo ORDER BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo";

        }else{
            $pastel = "SELECT tipo_apoyo.id_tipo_apoyo, tipo_apoyo.nombre_tipo_apoyo, tipo_apoyo.ta_id_componente, tipo_apoyo.meta_um_beneficio, tipo_apoyo.meta_cantidad_servicio, tipo_apoyo.meta_cantidad_beneficio, tipo_apoyo.tipo_apoyo_presupuesto_ejercido, componente.nombre_componente, programa.nombre_programa, programa.id_programa  FROM tipo_apoyo INNER JOIN componente on componente.id_componente = tipo_apoyo.ta_id_componente INNER JOIN programa on componente.com_id_programa = programa.id_programa where  visible=1 group by id_tipo_apoyo ORDER BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo";
        }


        $pastel1 = $mysqli->query($pastel);
        $pastel_rows = array();
        $pastel_table6 = array();

        $pastel_table6['cols']= array(
            array('label'=>'Actividad', 'type'=>'string'),
            array('label'=>'Monto', 'type'=>'number')

        );
        foreach($pastel1 as $p){
            $pastel_temp = array();
            $pastel_temp[] = array('v'=>(string) $p['nombre_tipo_apoyo']);
            $pastel_temp[] = array('v'=>(integer) $p['tipo_apoyo_presupuesto_ejercido']);
            $pastel_rows[] = array ('c'=>$pastel_temp);
        }
        $pastel_table6['rows']=$pastel_rows;
        $pasteljsonTable6 =json_encode($pastel_table6);  //GRAFICO 11 
    

//////////Metas programadas vs Metas alcanzadas por Actividad

        if ($id_tipo_usuario != 1){
            $tabla = "SELECT tipo_apoyo.id_tipo_apoyo, tipo_apoyo.nombre_tipo_apoyo, tipo_apoyo.ta_id_componente, tipo_apoyo.meta_um_servicio, tipo_apoyo.meta_um_beneficio, tipo_apoyo.meta_cantidad_servicio, tipo_apoyo.meta_cantidad_beneficio, tipo_apoyo.meta_alcanzada_cantidad_servicio, tipo_apoyo.meta_alcanzada_cantidad_beneficio,tipo_apoyo.tipo_apoyo_presupuesto_ejercido as programa_presupuesto_ejercido_fechas, componente.nombre_componente, programa.nombre_programa, programa.id_programa,COUNT( meta_alcanzada_cantidad_servicio ) AS tipo_apoyoA,
            concat(round(( meta_alcanzada_cantidad_servicio/meta_cantidad_servicio * 100 ),2),'%') AS porcentaje_servicio,
            COUNT( meta_alcanzada_cantidad_beneficio ) AS tipo_apoyoA,
            concat(round(( meta_alcanzada_cantidad_beneficio/meta_cantidad_beneficio * 100 ),2),'%') AS porcentaje_beneficio,  servicios.nombre_servicios,  beneficios.nombre_beneficios  FROM tipo_apoyo INNER JOIN componente on componente.id_componente = tipo_apoyo.ta_id_componente INNER JOIN programa on componente.com_id_programa = programa.id_programa INNER JOIN beneficios ON beneficios.id_beneficios = tipo_apoyo.meta_um_beneficio INNER JOIN servicios ON servicios.id_servicios = tipo_apoyo.meta_um_servicio where programa.id_programa=$id_usuario_programa and visible=1 group by id_tipo_apoyo ORDER BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo";
            $tabla1_exportar = $mysqli->query($tabla);
        }else{
            $tabla = "SELECT tipo_apoyo.id_tipo_apoyo, tipo_apoyo.nombre_tipo_apoyo, tipo_apoyo.ta_id_componente, tipo_apoyo.meta_um_servicio, tipo_apoyo.meta_um_beneficio, tipo_apoyo.meta_cantidad_servicio, tipo_apoyo.meta_cantidad_beneficio, tipo_apoyo.meta_alcanzada_cantidad_servicio, tipo_apoyo.meta_alcanzada_cantidad_beneficio, tipo_apoyo.tipo_apoyo_presupuesto_ejercido as programa_presupuesto_ejercido_fechas, componente.nombre_componente, programa.nombre_programa, programa.id_programa,COUNT( meta_alcanzada_cantidad_servicio ) AS tipo_apoyoA,
            concat(round(( meta_alcanzada_cantidad_servicio/meta_cantidad_servicio * 100 ),2),'%') AS porcentaje_servicio,
            COUNT( meta_alcanzada_cantidad_beneficio ) AS tipo_apoyoA,
            concat(round(( meta_alcanzada_cantidad_beneficio/meta_cantidad_beneficio * 100 ),2),'%') AS porcentaje_beneficio, servicios.nombre_servicios,  beneficios.nombre_beneficios  FROM tipo_apoyo INNER JOIN componente on componente.id_componente = tipo_apoyo.ta_id_componente INNER JOIN programa on componente.com_id_programa = programa.id_programa INNER JOIN beneficios ON beneficios.id_beneficios = tipo_apoyo.meta_um_beneficio INNER JOIN servicios ON servicios.id_servicios = tipo_apoyo.meta_um_servicio where visible=1 group by id_tipo_apoyo ORDER BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo";
            $tabla1_exportar = $mysqli->query($tabla);
        }


        $tabla1 = $mysqli->query($tabla);
        $tabla_rows = array();
        $tabla_table = array();

        $tabla_table['cols']= array(
            array('label'=>'Componente', 'type'=>'string'),
            array('label'=>'Actividad', 'type'=>'string'),
            array('label'=>'Monto ejercido', 'type'=>'number'),
            array('label'=>'U.M. Servicios', 'type'=>'string'),
            array('label'=>'Meta', 'type'=>'number'),
            array('label'=>'Avance', 'type'=>'number'),
            array('label'=>'%', 'type'=>'string'),
            array('label'=>'U.M. Beneficio', 'type'=>'string'),
            array('label'=>'Meta', 'type'=>'number'),
            array('label'=>'Avance', 'type'=>'number'),
            array('label'=>'%', 'type'=>'string'),

        );
        foreach($tabla1 as $b){
            $tabla_temp = array();
            $tabla_temp[] = array('v'=>(string) $b['nombre_componente']);
            $tabla_temp[] = array('v'=>(string) $b['nombre_tipo_apoyo']);
            $tabla_temp[] = array('v'=>(integer) $b['programa_presupuesto_ejercido_fechas']);
            $tabla_temp[] = array('v'=>(string) $b['nombre_servicios']);
            $tabla_temp[] = array('v'=>(integer) $b['meta_cantidad_servicio']);
            $tabla_temp[] = array('v'=>(integer) $b['meta_alcanzada_cantidad_servicio']);
            $tabla_temp[] = array('v'=>(string) $b['porcentaje_servicio']);
            $tabla_temp[] = array('v'=>(string) $b['nombre_beneficios']);
            $tabla_temp[] = array('v'=>(integer) $b['meta_cantidad_beneficio']);
            $tabla_temp[] = array('v'=>(integer) $b['meta_alcanzada_cantidad_beneficio']);
            $tabla_temp[] = array('v'=>(string) $b['porcentaje_beneficio']);
            $tabla_rows[] = array ('c'=>$tabla_temp);
        }
        $tabla_table['rows']=$tabla_rows;
        $tablajsonTable =json_encode($tabla_table);   //GRAFICO 12  

////////////////Desglose de eventos realizados
        if ($id_tipo_usuario != 1){
            $tabla_evento = "SELECT 
            evento.id_evento,
            evento.nombre_evento,
            evento.pob_obj_evento,
            evento.nivel_geo_evento,
            evento.lugar_evento,
            evento.monto_evento,
            evento.fecha_evento,
            evento.fecha_modificacion_evento,
            evento.usuario_evento,
            evento.id_accion_evento,
            evento.cantidad_servicio,
            evento.cantidad_beneficio,
            evento.hombres, 
            evento.mujeres,
            region.region,
            municipio.municipio,
            localidad.localidad, 
            tipo_apoyo.nombre_tipo_apoyo,
            servicios.nombre_servicios,
            beneficios.nombre_beneficios,
            componente.nombre_componente,
            COUNT(evento.hombres) AS tipo_apoyoA,       
            concat(round((evento.hombres/cantidad_beneficio * 100 ),2),'%') AS porcentaje_hombres,
            COUNT(evento.mujeres) AS tipo_apoyoA,       
            concat(round((evento.mujeres/cantidad_beneficio * 100 ),2),'%') AS porcentaje_mujeres  

        FROM
            evento
                INNER JOIN
            localidad ON localidad.cve_localidad = evento.lugar_evento
                INNER JOIN
            municipio ON municipio.cve_municipios = localidad.l_cve_municipio
                INNER JOIN
            region ON region.cve_regiones = municipio.m_cve_region
                INNER JOIN
            usuarios ON usuarios.id = evento.usuario_evento
                INNER JOIN
            tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
                INNER JOIN
            componente ON componente.id_componente = tipo_apoyo.ta_id_componente
                INNER JOIN
            programa ON componente.com_id_programa = programa.id_programa  
                INNER JOIN
            beneficios ON beneficios.id_beneficios = tipo_apoyo.meta_um_beneficio
                INNER JOIN
            servicios ON servicios.id_servicios = tipo_apoyo.meta_um_servicio
        WHERE
             programa.id_programa=$id_usuario_programa and visible=1
        group by evento.id_evento
        ORDER BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento";
        $tabla1_evento_exportar = $mysqli->query($tabla_evento);
        }else{
            $tabla_evento = "SELECT 
            evento.id_evento,
            evento.nombre_evento,
            evento.pob_obj_evento,
            evento.nivel_geo_evento,
            evento.lugar_evento,
            evento.monto_evento,
            evento.fecha_evento,
            evento.fecha_modificacion_evento,
            evento.usuario_evento,
            evento.id_accion_evento,
            evento.cantidad_servicio,
            evento.cantidad_beneficio,
            evento.hombres, 
            evento.mujeres,
            region.region,
            municipio.municipio,
            localidad.localidad, 
            tipo_apoyo.nombre_tipo_apoyo,
            servicios.nombre_servicios,
            beneficios.nombre_beneficios,
            componente.nombre_componente,
            COUNT(evento.hombres) AS tipo_apoyoA,       
            concat(round((evento.hombres/cantidad_beneficio * 100 ),2),'%') AS porcentaje_hombres,
            COUNT(evento.mujeres) AS tipo_apoyoA,       
            concat(round((evento.mujeres/cantidad_beneficio * 100 ),2),'%') AS porcentaje_mujeres  
        FROM
            evento
                INNER JOIN
            localidad ON localidad.cve_localidad = evento.lugar_evento
                INNER JOIN
            municipio ON municipio.cve_municipios = localidad.l_cve_municipio
                INNER JOIN
            region ON region.cve_regiones = municipio.m_cve_region
                INNER JOIN
            usuarios ON usuarios.id = evento.usuario_evento
                INNER JOIN
            tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
                INNER JOIN
            componente ON componente.id_componente = tipo_apoyo.ta_id_componente
                INNER JOIN
            programa ON componente.com_id_programa = programa.id_programa  
                INNER JOIN
            beneficios ON beneficios.id_beneficios = tipo_apoyo.meta_um_beneficio
                INNER JOIN
            servicios ON servicios.id_servicios = tipo_apoyo.meta_um_servicio
            where visible=1
        group by evento.id_evento
        ORDER BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento";
        $tabla1_evento_exportar = $mysqli->query($tabla_evento);
        }
        
        
        $tabla1_evento = $mysqli->query($tabla_evento);
        $tabla_evento_rows = array();
        $tabla_evento_table = array();
    
        $tabla_evento_table['cols']= array(
            array('label'=>'Componente', 'type'=>'string'),
            array('label'=>'Actividad', 'type'=>'string'),
            array('label'=>'Evento', 'type'=>'string'),
            array('label'=>'Monto ejercido', 'type'=>'number'),
            array('label'=>'Fecha', 'type'=>'string'),
            array('label'=>'U.M. Servicios', 'type'=>'string'),
            array('label'=>'Meta alcanzada', 'type'=>'number'),
            array('label'=>'U.M. Beneficio', 'type'=>'string'),
            array('label'=>'Meta alcanzada', 'type'=>'number'),
            array('label'=>'Hombres', 'type'=>'number'),
            array('label'=>'%', 'type'=>'string'),
            array('label'=>'Mujeres', 'type'=>'number'),
            array('label'=>'%', 'type'=>'string'),
    
        );
        foreach($tabla1_evento as $b){
            $tabla_evento_temp = array();
            $tabla_evento_temp[] = array('v'=>(string) $b['nombre_componente']);
            $tabla_evento_temp[] = array('v'=>(string) $b['nombre_tipo_apoyo']);
            $tabla_evento_temp[] = array('v'=>(string) $b['nombre_evento']);
            $tabla_evento_temp[] = array('v'=>(integer) $b['monto_evento']);
            $tabla_evento_temp[] = array('v'=>(string) $b['fecha_evento']);
            $tabla_evento_temp[] = array('v'=>(string) $b['nombre_servicios']);                    
            $tabla_evento_temp[] = array('v'=>(integer) $b['cantidad_servicio']);
            $tabla_evento_temp[] = array('v'=>(string) $b['nombre_beneficios']);
            $tabla_evento_temp[] = array('v'=>(integer) $b['cantidad_beneficio']);
            $tabla_evento_temp[] = array('v'=>(integer) $b['hombres']);
            $tabla_evento_temp[] = array('v'=>(string) $b['porcentaje_hombres']);
            $tabla_evento_temp[] = array('v'=>(integer) $b['mujeres']);
            $tabla_evento_temp[] = array('v'=>(string) $b['porcentaje_mujeres']);
            $tabla_evento_rows[] = array ('c'=>$tabla_evento_temp);
        }
        $tabla_evento_table['rows']=$tabla_evento_rows;
        $tabla_evento_jsonTable =json_encode($tabla_evento_table);  //GRAFICO 15

////////////////Tabla Presupuesto ejercido a nivel de objeto de gasto (partidas y monto ejercido)
        if ($id_tipo_usuario != 1){
            $tabla_partida = "SELECT 
            id_reg_partidas,
            partida,
            cantidad_partidas,
            monto_partidas,
            id_evento_partidas,
            partida_gasto_texto,
            evento.nombre_evento,
            programa.nombre_programa,
            componente.nombre_componente,
            tipo_apoyo.nombre_tipo_apoyo,
            capitulo
        FROM
            reg_partidas
                INNER JOIN
            objeto_de_gasto ON clave = partida
            INNER JOIN 
            evento ON evento.id_evento = reg_partidas.id_evento_partidas
            INNER JOIN 
            tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
            INNER JOIN 
            componente ON componente.id_componente = tipo_apoyo.ta_id_componente
            INNER JOIN
            programa ON programa.id_programa = componente.com_id_programa
        WHERE
             programa.id_programa=$id_usuario_programa and visible=1
             group by reg_partidas.id_reg_partidas
        ORDER BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento, reg_partidas.id_reg_partidas";
        $tabla1_partida_exportar = $mysqli->query($tabla_partida);
        }else{
            $tabla_partida = "SELECT 
            id_reg_partidas,
            partida,
            cantidad_partidas,
            monto_partidas,
            id_evento_partidas,
            partida_gasto_texto,
            evento.nombre_evento,
            programa.nombre_programa,
            componente.nombre_componente,
            tipo_apoyo.nombre_tipo_apoyo,
            capitulo
        FROM
            reg_partidas
                INNER JOIN
            objeto_de_gasto ON clave = partida
            INNER JOIN 
            evento ON evento.id_evento = reg_partidas.id_evento_partidas
            INNER JOIN 
            tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
            INNER JOIN 
            componente ON componente.id_componente = tipo_apoyo.ta_id_componente
            INNER JOIN
            programa ON programa.id_programa = componente.com_id_programa
            where visible=1
            group by reg_partidas.id_reg_partidas
            
        ORDER BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento, reg_partidas.id_reg_partidas";
        $tabla1_partida_exportar = $mysqli->query($tabla_partida);
        }
        
        
        $tabla1_partida = $mysqli->query($tabla_partida);
        $tabla_partida_rows = array();
        $tabla_partida_table = array();
    
        $tabla_partida_table['cols']= array(
            array('label'=>'Programa', 'type'=>'string'),
            array('label'=>'Componente', 'type'=>'string'),
            array('label'=>'Actividad', 'type'=>'string'),
            array('label'=>'Evento', 'type'=>'string'),
            array('label'=>'Capítulo', 'type'=>'number'),
            array('label'=>'Partida', 'type'=>'string'),
            array('label'=>'Monto', 'type'=>'number'),

        );
        foreach($tabla1_partida as $b){
            $tabla_partida_temp = array();
            $tabla_partida_temp[] = array('v'=>(string) $b['nombre_programa']);
            $tabla_partida_temp[] = array('v'=>(string) $b['nombre_componente']);
            $tabla_partida_temp[] = array('v'=>(string) $b['nombre_tipo_apoyo']);
            $tabla_partida_temp[] = array('v'=>(string) $b['nombre_evento']);
            $tabla_partida_temp[] = array('v'=>(integer) $b['capitulo']);
            $tabla_partida_temp[] = array('v'=>(string) $b['partida_gasto_texto']);
            $tabla_partida_temp[] = array('v'=>(integer) $b['monto_partidas']);
            $tabla_partida_rows[] = array ('c'=>$tabla_partida_temp);
        }
        $tabla_partida_table['rows']=$tabla_partida_rows;
        $tabla_partida_jsonTable =json_encode($tabla_partida_table);  //GRAFICO 19

                 //-----GRÁFICO POR OBJETO DEL GASTO CAPÍTULO
        if ($id_tipo_usuario != 1){
            $pastel = "SELECT capitulo,  evento.fecha_evento, SUM(monto_partidas) AS capitulo_total
            FROM reg_partidas
            INNER JOIN evento on evento.id_evento = reg_partidas.id_evento_partidas
            INNER JOIN tipo_apoyo on tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
            INNER JOIN componente on componente.id_componente = tipo_apoyo.ta_id_componente
            INNER JOIN programa on programa.id_programa = componente.com_id_programa
            INNER JOIN objeto_de_gasto on objeto_de_gasto.clave = reg_partidas.partida
            where programa.id_programa=$id_usuario_programa and visible=1 
            group by capitulo";
            }else{
                $pastel = "SELECT capitulo,  evento.fecha_evento, SUM(monto_partidas) AS capitulo_total
            FROM reg_partidas
            INNER JOIN evento on evento.id_evento = reg_partidas.id_evento_partidas
            INNER JOIN tipo_apoyo on tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
            INNER JOIN componente on componente.id_componente = tipo_apoyo.ta_id_componente
            INNER JOIN programa on programa.id_programa = componente.com_id_programa
            INNER JOIN objeto_de_gasto on objeto_de_gasto.clave = reg_partidas.partida 
            where visible=1                      
            group by capitulo";

            }
        $pastel1 = $mysqli->query($pastel);
        $pastel_rows = array();
        $pastel_table10 = array();

        $pastel_table10['cols']= array(
            array('label'=>'Capítulo', 'type'=>'string'),
            array('label'=>'Presupuesto ejercido', 'type'=>'number')

        );
        foreach($pastel1 as $p){
            $pastel_temp = array();
            $pastel_temp[] = array('v'=>(integer) $p['capitulo']);
            $pastel_temp[] = array('v'=>(integer) $p['capitulo_total']);
            $pastel_rows[] = array ('c'=>$pastel_temp);
        }
        $pastel_table10['rows']=$pastel_rows;
        $pasteljsonTable10 =json_encode($pastel_table10);  //GRAFICO 16
    
            ////////////////Tabla Presupuesto ejercido a nivel de objeto de gasto (capitulo y monto ejercido)
        if ($id_tipo_usuario != 1){
            $tabla_capitulo = "SELECT 
            programa.nombre_programa,
            componente.nombre_componente,
            tipo_apoyo.nombre_tipo_apoyo,
            evento.nombre_evento,
            capitulo,
            evento.fecha_evento,
            SUM(monto_partidas) AS capitulo_total
            FROM
            reg_partidas
                INNER JOIN
            objeto_de_gasto ON clave = partida
            INNER JOIN 
            evento ON evento.id_evento = reg_partidas.id_evento_partidas
            INNER JOIN 
            tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
            INNER JOIN 
            componente ON componente.id_componente = tipo_apoyo.ta_id_componente
            INNER JOIN
            programa ON programa.id_programa = componente.com_id_programa
            WHERE
            programa.id_programa=$id_usuario_programa and visible=1
            GROUP BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento, capitulo
            ORDER BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento, capitulo";
            $tabla1_capitulo_exportar = $mysqli->query($tabla_capitulo);
        }else{
            $tabla_capitulo = "SELECT 
            programa.nombre_programa,
            componente.nombre_componente,
            tipo_apoyo.nombre_tipo_apoyo,
            evento.nombre_evento,
            capitulo,
            evento.fecha_evento,
            SUM(monto_partidas) AS capitulo_total
            FROM
            reg_partidas
                INNER JOIN
            objeto_de_gasto ON clave = partida
            INNER JOIN 
            evento ON evento.id_evento = reg_partidas.id_evento_partidas
            INNER JOIN 
            tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
            INNER JOIN 
            componente ON componente.id_componente = tipo_apoyo.ta_id_componente
            INNER JOIN
            programa ON programa.id_programa = componente.com_id_programa
            where visible=1
            GROUP BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento, capitulo
            ORDER BY programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento, capitulo";
            $tabla1_capitulo_exportar = $mysqli->query($tabla_capitulo);
            }
            $tabla1_capitulo = $mysqli->query($tabla_capitulo);
            $tabla_capitulo_rows = array();
            $tabla_capitulo_table = array();

            $tabla_capitulo_table['cols']= array(
                array('label'=>'Programa', 'type'=>'string'),
                array('label'=>'Componente', 'type'=>'string'),
                array('label'=>'Actividad', 'type'=>'string'),
                array('label'=>'Evento', 'type'=>'string'),
                array('label'=>'Capítulo', 'type'=>'number'),
                array('label'=>'Monto', 'type'=>'number'),

            );
            foreach($tabla1_capitulo as $b){
                $tabla_capitulo_temp = array();
                $tabla_capitulo_temp[] = array('v'=>(string) $b['nombre_programa']);
                $tabla_capitulo_temp[] = array('v'=>(string) $b['nombre_componente']);
                $tabla_capitulo_temp[] = array('v'=>(string) $b['nombre_tipo_apoyo']);
                $tabla_capitulo_temp[] = array('v'=>(string) $b['nombre_evento']);
                $tabla_capitulo_temp[] = array('v'=>(integer) $b['capitulo']);
                $tabla_capitulo_temp[] = array('v'=>(integer) $b['capitulo_total']);
                $tabla_capitulo_rows[] = array ('c'=>$tabla_capitulo_temp);
            }
            $tabla_capitulo_table['rows']=$tabla_capitulo_rows;
            $tabla_capitulo_jsonTable =json_encode($tabla_capitulo_table);  //GRAFICO 17
                            
        ////RESUMEN POR TIPO DE ACCIONES (TRANSVERSALIDAD, ACCIONES, PREVENCION)
        if ($id_tipo_usuario != 1){
            $tabla_acciones = "SELECT programa.nombre_programa, tipo_apoyo.tipo_accion, sum(evento.cantidad_beneficio) as personas_atendidas, sum(evento.hombres) as hombres, sum(evento.mujeres) as mujeres,
            sum(evento.monto_evento)as inversion_ejercido,
            count(tipo_apoyo.tipo_accion) as acciones
            FROM
                tipo_apoyo 
            INNER JOIN
                componente ON componente.id_componente = tipo_apoyo.ta_id_componente
            INNER JOIN
                programa ON componente.com_id_programa = programa.id_programa
            INNER JOIN  
                evento ON evento.id_accion_evento = tipo_apoyo.id_tipo_apoyo   
                WHERE
                programa.id_programa = $id_usuario_programa and visible=1
            group by  programa.id_programa, tipo_accion
            order by  tipo_accion";
            $tabla_acciones1_exportar = $mysqli->query($tabla_acciones);
        }else{
            $tabla_acciones = "SELECT programa.nombre_programa, tipo_apoyo.tipo_accion, sum(evento.cantidad_beneficio) as personas_atendidas, sum(evento.hombres) as hombres, sum(evento.mujeres) as mujeres,
            sum(evento.monto_evento)as inversion_ejercido,
            count(tipo_apoyo.tipo_accion) as acciones
            FROM
                tipo_apoyo 
            INNER JOIN
                componente ON componente.id_componente = tipo_apoyo.ta_id_componente
            INNER JOIN
                programa ON componente.com_id_programa = programa.id_programa
            INNER JOIN  
                evento ON evento.id_accion_evento = tipo_apoyo.id_tipo_apoyo
                where visible=1 
            group by  programa.id_programa, tipo_accion
            order by  tipo_accion";
            $tabla_acciones1_exportar = $mysqli->query($tabla_acciones);
            }
            $tabla_acciones1 = $mysqli->query($tabla_acciones);
            $tabla_acciones_rows = array();
            $tabla_acciones_table = array();

            $tabla_acciones_table['cols']= array(
                array('label'=>'Programa', 'type'=>'string'),
                array('label'=>'Tipo de Acción', 'type'=>'string'),
                array('label'=>'Personas Atendidas', 'type'=>'number'),
				array('label'=>'Hombres', 'type'=>'number'),
				array('label'=>'Mujeres', 'type'=>'number'),
                array('label'=>'Monto ejercido', 'type'=>'number'),  
                array('label'=>'Número de acciones', 'type'=>'number'),

            );
            foreach($tabla_acciones1 as $b){
                $tabla_temp = array();
                $tabla_temp[] = array('v'=>(string) $b['nombre_programa']);
                $tabla_temp[] = array('v'=>(string) $b['tipo_accion']);
                $tabla_temp[] = array('v'=>(integer) $b['personas_atendidas']);
				$tabla_temp[] = array('v'=>(integer) $b['hombres']);
				$tabla_temp[] = array('v'=>(integer) $b['mujeres']);
                $tabla_temp[] = array('v'=>(integer) $b['inversion_ejercido']);
                $tabla_temp[] = array('v'=>(integer) $b['acciones']);
                $tabla_acciones_rows[] = array ('c'=>$tabla_temp);
            }
            $tabla_acciones_table['rows']=$tabla_acciones_rows;
            $tabla_accionesjsonTable =json_encode($tabla_acciones_table);  //GRAFICO 13    

    ////DESGLOSE POR TIPO DE ACCIONES (TRANSVERSALIDAD, ACCIONES, PREVENCION)
        if ($id_tipo_usuario != 1){
            $tabla_acciones = "SELECT programa.nombre_programa, componente.nombre_componente, tipo_apoyo.tipo_accion, evento.nombre_evento, sum(evento.cantidad_beneficio) as personas_atendidas, sum(evento.hombres) as hombres, sum(evento.mujeres) as mujeres,
            sum(evento.monto_evento)as inversion_ejercido
            FROM
                tipo_apoyo 
            INNER JOIN
                componente ON componente.id_componente = tipo_apoyo.ta_id_componente
            INNER JOIN
                programa ON componente.com_id_programa = programa.id_programa
            INNER JOIN  
                evento ON evento.id_accion_evento = tipo_apoyo.id_tipo_apoyo   
            WHERE programa.id_programa = $id_usuario_programa and visible=1
            group by programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento, evento.fecha_evento, tipo_accion
            order by  tipo_accion";
            $tabla_acciones1_desglose_exportar = $mysqli->query($tabla_acciones);
        }else{
            $tabla_acciones = "SELECT programa.nombre_programa, componente.nombre_componente, tipo_apoyo.tipo_accion, evento.nombre_evento, sum(evento.cantidad_beneficio) as personas_atendidas, sum(evento.hombres) as hombres, sum(evento.mujeres) as mujeres,
            sum(evento.monto_evento)as inversion_ejercido
            FROM
                tipo_apoyo 
            INNER JOIN
                componente ON componente.id_componente = tipo_apoyo.ta_id_componente
            INNER JOIN
                programa ON componente.com_id_programa = programa.id_programa
            INNER JOIN  
                evento ON evento.id_accion_evento = tipo_apoyo.id_tipo_apoyo   
                where visible=1
            group by programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento, evento.fecha_evento, tipo_accion
            order by  tipo_accion";
            $tabla_acciones1_desglose_exportar = $mysqli->query($tabla_acciones);
            }
            $tabla_acciones1 = $mysqli->query($tabla_acciones);
            $tabla_acciones_rows = array();
            $tabla_acciones_table = array();
            
            $tabla_acciones_table['cols']= array(
                array('label'=>'Programa', 'type'=>'string'),
                array('label'=>'Componente', 'type'=>'string'),
                array('label'=>'Tipo de Acción', 'type'=>'string'),
                array('label'=>'Evento', 'type'=>'string'),
                array('label'=>'Personas Atendidas', 'type'=>'number'),
				array('label'=>'Hombres', 'type'=>'number'),
				array('label'=>'Mujeres', 'type'=>'number'),
                array('label'=>'Monto ejercido', 'type'=>'number'),  
            
            
            );
            foreach($tabla_acciones1 as $b){
                $tabla_temp = array();    
                $tabla_temp[] = array('v'=>(string) $b['nombre_programa']);
                $tabla_temp[] = array('v'=>(string) $b['nombre_componente']);
                $tabla_temp[] = array('v'=>(string) $b['tipo_accion']);
                $tabla_temp[] = array('v'=>(string) $b['nombre_evento']);
                $tabla_temp[] = array('v'=>(integer) $b['personas_atendidas']);
				$tabla_temp[] = array('v'=>(integer) $b['hombres']);
				$tabla_temp[] = array('v'=>(integer) $b['mujeres']);
                $tabla_temp[] = array('v'=>(integer) $b['inversion_ejercido']);
        
                $tabla_acciones_rows[] = array ('c'=>$tabla_temp);
            }
            $tabla_acciones_table['rows']=$tabla_acciones_rows;
            $tabla_acciones_desglosejsonTable =json_encode($tabla_acciones_table);   //GRAFICO 14 

///////INICIO REGION, MUNICIPIO, GENERO ---EVENTO
//-----GRÁFICO POR REGION GRAFICO PAY
    if ($id_tipo_usuario != 1){
		$pastel = "SELECT  region.region, COUNT(region.region) AS Total_Cuenta 
		FROM
		evento
		INNER JOIN localidad ON localidad.cve_localidad = evento.lugar_evento
		INNER JOIN municipio ON municipio.cve_municipios = localidad.l_cve_municipio
		INNER JOIN region ON region.cve_regiones = municipio.m_cve_region
		INNER JOIN tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
		INNER JOIN componente ON componente.id_componente = tipo_apoyo.ta_id_componente
		INNER JOIN programa ON programa.id_programa = componente.com_id_programa
		WHERE programa.id_programa = $id_usuario_programa and visible=1
		GROUP BY region.region ORDER BY  region.region  ASC ";
	}else{
		$pastel = "SELECT  region.region, COUNT(region.region) AS Total_Cuenta 
		FROM
		evento
		INNER JOIN localidad ON localidad.cve_localidad = evento.lugar_evento
		INNER JOIN municipio ON municipio.cve_municipios = localidad.l_cve_municipio
		INNER JOIN region ON region.cve_regiones = municipio.m_cve_region
		INNER JOIN tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
		INNER JOIN componente ON componente.id_componente = tipo_apoyo.ta_id_componente
		INNER JOIN programa ON programa.id_programa = componente.com_id_programa
        where visible=1
		GROUP BY region.region ORDER BY  region.region  ASC ";
	}
    $pastel1 = $mysqli->query($pastel);
    $pastel_rows = array();
    $pastel_table1evento = array();

    $pastel_table1evento['cols']= array(
        array('label'=>'Región', 'type'=>'string'),
        array('label'=>'Cantidad', 'type'=>'number')

    );
        foreach($pastel1 as $p){
            $pastel_temp = array();
            $pastel_temp[] = array('v'=>(string) $p['region']);
            $pastel_temp[] = array('v'=>(integer) $p['Total_Cuenta']);
            $pastel_rows[] = array ('c'=>$pastel_temp);
        }
        $pastel_table1evento['rows']=$pastel_rows;
        $pasteljsonTable1evento =json_encode($pastel_table1evento);

//////////

//-----GRÁFICO POR MUNICIPIO -GRÁFICO PAY

        ///////////
    if ($id_tipo_usuario != 1){
		$pastel = "SELECT  municipio.municipio, COUNT(municipio.municipio) AS Total_Cuenta 
		FROM
		evento 
		INNER JOIN localidad ON localidad.cve_localidad = evento.lugar_evento
		INNER JOIN municipio ON municipio.cve_municipios = localidad.l_cve_municipio
		INNER JOIN region ON region.cve_regiones = municipio.m_cve_region
		INNER JOIN tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
		INNER JOIN componente ON componente.id_componente = tipo_apoyo.ta_id_componente
		INNER JOIN programa ON programa.id_programa = componente.com_id_programa
		WHERE programa.id_programa = $id_usuario_programa and visible=1  
		GROUP BY municipio.municipio ORDER BY  municipio.municipio  ASC ";

        $tabla_localidades = "SELECT programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento, evento.fecha_evento, region.region, municipio.municipio, localidad.localidad, localidad.latitud_dec, localidad.longitud_dec, COUNT(localidad.localidad) AS Total_Cuenta 
        FROM
        evento 
        INNER JOIN localidad ON localidad.cve_localidad = evento.lugar_evento
        INNER JOIN municipio ON municipio.cve_municipios = localidad.l_cve_municipio
        INNER JOIN region ON region.cve_regiones = municipio.m_cve_region
        INNER JOIN tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
        INNER JOIN componente ON componente.id_componente = tipo_apoyo.ta_id_componente
        INNER JOIN programa ON programa.id_programa = componente.com_id_programa
        WHERE programa.id_programa = $id_usuario_programa and visible=1 
        GROUP BY localidad.localidad ORDER BY  localidad.localidad  ASC ";
	}else{
		$pastel = "SELECT  municipio.municipio, COUNT(municipio.municipio) AS Total_Cuenta 
		FROM
		evento 
		INNER JOIN localidad ON localidad.cve_localidad = evento.lugar_evento
		INNER JOIN municipio ON municipio.cve_municipios = localidad.l_cve_municipio
		INNER JOIN region ON region.cve_regiones = municipio.m_cve_region
		INNER JOIN tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
		INNER JOIN componente ON componente.id_componente = tipo_apoyo.ta_id_componente
		INNER JOIN programa ON programa.id_programa = componente.com_id_programa  
        where visible=1 
		GROUP BY municipio.municipio ORDER BY  municipio.municipio  ASC ";

        $tabla_localidades = "SELECT programa.nombre_programa, componente.nombre_componente, tipo_apoyo.nombre_tipo_apoyo, evento.nombre_evento, evento.fecha_evento, region.region, municipio.municipio, localidad.localidad, localidad.latitud_dec, localidad.longitud_dec, COUNT(localidad.localidad) AS Total_Cuenta 
		FROM
		evento 
		INNER JOIN localidad ON localidad.cve_localidad = evento.lugar_evento
		INNER JOIN municipio ON municipio.cve_municipios = localidad.l_cve_municipio
		INNER JOIN region ON region.cve_regiones = municipio.m_cve_region
		INNER JOIN tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
		INNER JOIN componente ON componente.id_componente = tipo_apoyo.ta_id_componente
		INNER JOIN programa ON programa.id_programa = componente.com_id_programa   
        where visible=1
		GROUP BY localidad.localidad ORDER BY  localidad.localidad  ASC ";
	}
        $tablalocexportar = $mysqli->query($tabla_localidades);
        $pastel1 = $mysqli->query($pastel);
        $pastel_rows = array();
        $pastel_table2evento = array();

        $pastel_table2evento['cols']= array(
            array('label'=>'Municipio %', 'type'=>'string'),
            array('label'=>'Cantidad', 'type'=>'number')

        );
            foreach($pastel1 as $p){
                $pastel_temp = array();
                $pastel_temp[] = array('v'=>(string) $p['municipio']);
                $pastel_temp[] = array('v'=>(integer) $p['Total_Cuenta']);
                $pastel_rows[] = array ('c'=>$pastel_temp);
            }
            $pastel_table2evento['rows']=$pastel_rows;
            $pasteljsonTable2evento =json_encode($pastel_table2evento);

        //////////        
//-----GRÁFICO POR GÉNERO

	if ($id_tipo_usuario != 1){		
		$sqlh="UPDATE hombre_mujer SET hombre_mujer.cantidad_h_m =(SELECT SUM( evento.hombres) AS Total_CuentaH 
		FROM
		evento 
		INNER JOIN localidad ON localidad.cve_localidad = evento.lugar_evento
		INNER JOIN municipio ON municipio.cve_municipios = localidad.l_cve_municipio
		INNER JOIN region ON region.cve_regiones = municipio.m_cve_region
		INNER JOIN tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
		INNER JOIN componente ON componente.id_componente = tipo_apoyo.ta_id_componente
		INNER JOIN programa ON programa.id_programa = componente.com_id_programa
		WHERE programa.id_programa = $id_usuario_programa and visible=1) 
        WHERE hombre_mujer.h_m ='HOMBRE'";
		$mysqli->query($sqlh);
		
		$sqlm="UPDATE hombre_mujer SET hombre_mujer.cantidad_h_m =(SELECT SUM(evento.mujeres) AS Total_CuentaM 
		FROM
		evento 
		INNER JOIN localidad ON localidad.cve_localidad = evento.lugar_evento
		INNER JOIN municipio ON municipio.cve_municipios = localidad.l_cve_municipio
		INNER JOIN region ON region.cve_regiones = municipio.m_cve_region
		INNER JOIN tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
		INNER JOIN componente ON componente.id_componente = tipo_apoyo.ta_id_componente
		INNER JOIN programa ON programa.id_programa = componente.com_id_programa
		WHERE programa.id_programa = $id_usuario_programa and visible=1) 
        WHERE hombre_mujer.h_m ='MUJER'";
		$mysqli->query($sqlm);	
	}else{
		
		$sqlh="UPDATE hombre_mujer SET hombre_mujer.cantidad_h_m =( SELECT  SUM( evento.hombres) AS Total_CuentaH 
		FROM
		evento 
		INNER JOIN localidad ON localidad.cve_localidad = evento.lugar_evento
		INNER JOIN municipio ON municipio.cve_municipios = localidad.l_cve_municipio
		INNER JOIN region ON region.cve_regiones = municipio.m_cve_region
		INNER JOIN tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
		INNER JOIN componente ON componente.id_componente = tipo_apoyo.ta_id_componente
		INNER JOIN programa ON programa.id_programa = componente.com_id_programa where visible=1)
        WHERE hombre_mujer.h_m ='HOMBRE'";
		$mysqli->query($sqlh);
		
		$sqlm="UPDATE hombre_mujer SET hombre_mujer.cantidad_h_m =( SELECT  SUM( evento.mujeres) AS Total_CuentaM 
		FROM
		evento 
		INNER JOIN localidad ON localidad.cve_localidad = evento.lugar_evento
		INNER JOIN municipio ON municipio.cve_municipios = localidad.l_cve_municipio
		INNER JOIN region ON region.cve_regiones = municipio.m_cve_region
		INNER JOIN tipo_apoyo ON tipo_apoyo.id_tipo_apoyo = evento.id_accion_evento
		INNER JOIN componente ON componente.id_componente = tipo_apoyo.ta_id_componente
		INNER JOIN programa ON programa.id_programa = componente.com_id_programa where visible=1)
        WHERE hombre_mujer.h_m ='MUJER'";
		$mysqli->query($sqlm);	
	}
				$pastel = "SELECT h_m, cantidad_h_m FROM hombre_mujer";
                $pastel1 = $mysqli->query($pastel);
                $pastel_rows = array();
                $pastel_table3evento = array();

                $pastel_table3evento['cols']= array(
                    array('label'=>'Género', 'type'=>'string'),
                    array('label'=>'Cantidad', 'type'=>'number')

                );
                    foreach($pastel1 as $p){
                        $pastel_temp = array();
                        $pastel_temp[] = array('v'=>(string) $p['h_m']);
                        $pastel_temp[] = array('v'=>(integer) $p['cantidad_h_m']);
                        $pastel_rows[] = array ('c'=>$pastel_temp);
                    }
                    $pastel_table3evento['rows']=$pastel_rows;
                    $pasteljsonTable3evento =json_encode($pastel_table3evento);

                
///////FIN REGION, MUNICIPIO, GENERO --- EVENTO
}


?>

<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="icono/ico.ico">
    <title>Estadísticas</title>

        <script>
        $(document).ready(function() {
        $('#tabla').DataTable();
        } );
		</script>

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
        <script type="text/javascript" src="https://www.google.com/jsapi" ></script>
        <script type="text/javascript" src="hattp://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>            
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        
        <!-- links para exportar a excel -->
        <script src="https://unpkg.com/xlsx@0.16.9/dist/xlsx.full.min.js"></script>
        <script src="https://unpkg.com/file-saverjs@latest/FileSaver.min.js"></script>
        <script src="https://unpkg.com/tableexport@latest/dist/js/tableexport.min.js"></script>

       <script language="javascript"> 
			$(document).ready(function(){
				$("#programa").change(function () {

					$('#tipo_apoyo').find('option').remove().end().append('<option value="whatever"></option>').val('whatever');
                    $('#evento_id').find('option').remove().end().append('<option value="whatever"></option>').val('whatever');
                    $('#partida_asignacion').find('option').remove().end().append('<option value="whatever"></option>').val('whatever');
					$("#programa option:selected").each(function () {
						id_programa = $(this).val();
						$.post("includes/getComponente.php", { id_programa: id_programa }, function(data){
							$("#componente").html(data);
						});            
					});
				})
			});
		
            $(document).ready(function(){
				$("#componente").change(function () {
                    $('#evento_id').find('option').remove().end().append('<option value="whatever"></option>').val('whatever');                    
					$("#componente option:selected").each(function () {
						id_componente = $(this).val();
						$.post("includes/getTipoapoyo.php", { id_componente: id_componente }, function(data){
							$("#tipo_apoyo").html(data);
						});            
					});
				})
			});

            $(document).ready(function(){
				$("#tipo_apoyo").change(function () {
					$("#tipo_apoyo option:selected").each(function () {
						id_tipo_apoyo = $(this).val();
						$.post("includes/getEvento.php", { id_tipo_apoyo: id_tipo_apoyo }, function(data){
							$("#evento_id").html(data);
						});            
					});
				})
			});

            $(document).ready(function(){
				$("#programa").change(function () {
					$("#programa option:selected").each(function () {
						id_programa = $(this).val();
						$.post("includes/getPartidaasignacion.php", { id_programa: id_programa }, function(data){
							$("#partida_asignacion").html(data);
						});            
					});
				})
			});

		</script>
        
        <script type="text/javascript">                                       

////////////////////////////////////////////////////*****************////////////////////////////////////////////////////////////////////////

        google.load('visualization','1',{'packages':['corechart']});           
        
        google.setOnLoadCallback(drawChartPastel6);
        function drawChartPastel6(){
            var data = new google.visualization.DataTable(<?=$pasteljsonTable6?>); //GRAFICO 11
            var option = {
                title:"Presupuesto ejercido por actividad",
                is3D:'true',
                width:600,
                heigth:200
            };

            var chart = new google.visualization.PieChart(document.getElementById('grafico_Pastel6'));
            
            chart.draw(data, option);

            google.visualization.events.addListener(chart, 'ready', function () {
                chart.innerHTML = '<img src="' + chart.getImageURI() + '">';
                console.log(chart.innerHTML);
                
                }); document.getElementById('png_grafico_Pastel6').outerHTML = '<a href="' + chart.getImageURI() + '" >Imprimir</a>'; 
        }

////////////////////////////////////////////////////*****************////////////////////////////////////////////////////////////////////////

        google.charts.load('current', {'packages':['table']});
        google.charts.setOnLoadCallback(drawTable);

        function drawTable() {
            var data = new google.visualization.DataTable(<?=$tablajsonTable?>); //GRAFICO 12
            
            var table = new google.visualization.Table(document.getElementById('table_div')); 

            table.draw(data, {showRowNumber: true, width: '100%', height: '100%'});

        }

////////////////////////////////////////////////////*****************////////////////////////////////////////////////////////////////////////

        ////////// RESUMEN
        google.charts.load('current', {'packages':['table']});
        google.charts.setOnLoadCallback(drawTable_acciones_resumen);

        function drawTable_acciones_resumen() {
            var data = new google.visualization.DataTable(<?=$tabla_accionesjsonTable?>); //GRAFICO 13
                                
            var table = new google.visualization.Table(document.getElementById('table_acciones_div')); 

            table.draw(data, {showRowNumber: true, width: '100%', height: '100%'});
        }

////////////////////////////////////////////////////*****************////////////////////////////////////////////////////////////////////////

        ////////// DESGLOSE
        google.charts.load('current', {'packages':['table']});
        google.charts.setOnLoadCallback(drawTable_acciones_desglose);

        function drawTable_acciones_desglose() {
            var data = new google.visualization.DataTable(<?=$tabla_acciones_desglosejsonTable?>); //GRAFICO 14
            
            var table = new google.visualization.Table(document.getElementById('table_acciones_desglose_div'));
            table.draw(data, {showRowNumber: true, width: '100%', height: '100%'});
            
        } 
             
////////////////////////////////////////////////////*****************////////////////////////////////////////////////////////////////////////

        google.charts.load('current', {'packages':['table']});
        google.charts.setOnLoadCallback(drawTable_evento);

        
        function drawTable_evento() {
            var data = new google.visualization.DataTable(<?=$tabla_evento_jsonTable?>); //GRAFICO 15
            
            var table = new google.visualization.Table(document.getElementById('table_div_evento'));

            table.draw(data, {showRowNumber: true, width: '100%', height: '100%'});
        }

////////////////////////////////////////////////////*****************////////////////////////////////////////////////////////////////////////

        google.load('visualization','1',{'packages':['corechart']});           
        
        google.setOnLoadCallback(drawChartPastel10);
        function drawChartPastel10(){
            var data = new google.visualization.DataTable(<?=$pasteljsonTable10?>); //GRAFICO 16
            var option = {
                title:"Distribución de gastos por capítulo y monto ejercido",
                is3D:'true',
                width:600,
                heigth:200
            };//ColumnChart
            var chart = new google.visualization.PieChart(document.getElementById('grafico_Pastel10'));
            
            chart.draw(data, option);

            google.visualization.events.addListener(chart, 'ready', function () {
            chart.innerHTML = '<img src="' + chart.getImageURI() + '">';
            console.log(chart.innerHTML);
                
            }); document.getElementById('png_grafico_Pastel10').outerHTML = '<a href="' + chart.getImageURI() + '" >Imprimir</a>';                             
        }

////////////////////////////////////////////////////*****************////////////////////////////////////////////////////////////////////////

        google.charts.load('current', {'packages':['table']});
        google.charts.setOnLoadCallback(drawTable_capitulo);

        function drawTable_capitulo() {
            var data = new google.visualization.DataTable(<?=$tabla_capitulo_jsonTable?>); //GRAFICO 17
            
            var table = new google.visualization.Table(document.getElementById('table_div_capitulo'));

            table.draw(data, {showRowNumber: true, width: '100%', height: '100%'});
        }  

////////////////////////////////////////////////////*****************////////////////////////////////////////////////////////////////////////

        google.load('visualization','1',{'packages':['corechart']});           
                        
        google.setOnLoadCallback(drawChartPastel9);
        function drawChartPastel9(){
            var data = new google.visualization.DataTable(<?=$pasteljsonTable9?>); //GRAFICO 18
            var option = {
                title:"Distribución de gastos por partida y monto ejercido",
                is3D:'true',
                width:600,
                heigth:200
            };//ColumnChart
            var chart = new google.visualization.PieChart(document.getElementById('grafico_Pastel9'));
            
            chart.draw(data, option);

            google.visualization.events.addListener(chart, 'ready', function () {
            chart.innerHTML = '<img src="' + chart.getImageURI() + '">';
            console.log(chart.innerHTML);
                
            }); document.getElementById('png_grafico_Pastel9').outerHTML = '<a href="' + chart.getImageURI() + '" >Imprimir</a>';                             
        }


////////////////////////////////////////////////////*****************////////////////////////////////////////////////////////////////////////
                
        google.charts.load('current', {'packages':['table']});
        google.charts.setOnLoadCallback(drawTable_partida);

        function drawTable_partida() {
            var data = new google.visualization.DataTable(<?=$tabla_partida_jsonTable?>); //GRAFICO 19
            
            var table = new google.visualization.Table(document.getElementById('table_div_partida'));

            table.draw(data, {showRowNumber: true, width: '100%', height: '100%'});
        }
		
//// INICIO REGION,MUNICIPIO, GENERO ---EVENTO
                        google.load('visualization','1',{'packages':['corechart']});           
                    
                        google.setOnLoadCallback(drawChartPastel1evento);
                        function drawChartPastel1evento(){
                            var data = new google.visualization.DataTable(<?=$pasteljsonTable1evento?>);
                            var option = {
                                title:"Evento distribuido por región",
                                is3D:'true',
                                width:600,
                                heigth:200
                            };
                            var chart = new google.visualization.PieChart(document.getElementById('grafico_Pastel1evento'));
                            
                            chart.draw(data, option);

                            google.visualization.events.addListener(chart, 'ready', function () {
                            chart.innerHTML = '<img src="' + chart.getImageURI() + '">';
                            console.log(chart.innerHTML);
                                
                            }); document.getElementById('png_grafico_Pastel1evento').outerHTML = '<a href="' + chart.getImageURI() + '" >Imprimir</a>'; 
                        }


                        google.load('visualization','1',{'packages':['corechart']});           
                        
                        google.setOnLoadCallback(drawChartPastel2evento);
                        function drawChartPastel2evento(){
                            var data = new google.visualization.DataTable(<?=$pasteljsonTable2evento?>);
                            var option = {
                                title:"Evento distribuido por municipio",
                                is3D:'true',
                                width:600,
                                heigth:200
                            };
                            var chart = new google.visualization.PieChart(document.getElementById('grafico_Pastel2evento'));
                            
                            chart.draw(data, option);

                            google.visualization.events.addListener(chart, 'ready', function () {
                            chart.innerHTML = '<img src="' + chart.getImageURI() + '">';
                            console.log(chart.innerHTML);
                                
                            }); document.getElementById('png_grafico_Pastel2evento').outerHTML = '<a href="' + chart.getImageURI() + '" >Imprimir</a>'; 
                        }


                        google.load('visualization','1',{'packages':['corechart']});           
                        
                        google.setOnLoadCallback(drawChartPastel3evento);
                        function drawChartPastel3evento(){
                            var data = new google.visualization.DataTable(<?=$pasteljsonTable3evento?>);
                            var option = {
                                title:"Evento distribuido por género",
                                is3D:'true',
                                width:600,
                                heigth:200
                            };
                            var chart = new google.visualization.PieChart(document.getElementById('grafico_Pastel3evento'));
                            
                            chart.draw(data, option);

                            google.visualization.events.addListener(chart, 'ready', function () {
                            chart.innerHTML = '<img src="' + chart.getImageURI() + '">';
                            console.log(chart.innerHTML);
                                
                            }); document.getElementById('png_grafico_Pastel3evento').outerHTML = '<a href="' + chart.getImageURI() + '" >Imprimir</a>';                             
                        }

//// FIN INICIO REGION,MUNICIPIO, GENERO ---EVENTO
	
               
   </script>
		
		<title>Estadisticas</title>

		
		<style>
			body {
			background: white;
			}
		</style>
</head>

<body class="d-flex flex-column h-100">

    <div class="container py-3">
    
        <h2 class="text-center">Estadísticas - Actividades</h2>   
        
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
            <a href="inicio.php" class="btn btn-dark" ></i>Regresar</a>  


           <div class="col-md-3 m-l">
               <center>
                 <form action="grafico_actividades.php" action="GET">
                <br>

                    <h5 class="text-center">Programa</h5>&nbsp;&nbsp;     
                     <?php 
                        echo $_programa;
                     ?>
                     <select name="programa" id="programa" class="form-select">
                            <option value="">Seleccionar...</option>
                            <?php while ($row_programas = $programas->fetch_assoc()) { ?>
                                <option value="<?php echo $row_programas["id_programa"]; ?>"><?= $row_programas["nombre_programa"] ?></option>
                            <?php } ?>
                    </select> 
                     <h5 class="text-center">Componente</h5>&nbsp;&nbsp;  
                     <?php 
                        echo $_nombre_componente;
                     ?>
                    <div> <select name="componente" id="componente" class="form-select" ></select></div>     
                     <h5 class="text-center">Actividad</h5>&nbsp;&nbsp; 
                     <?php 
                        echo $_nombre_tipo_apoyo;
                     ?>   
                    <div> <select name="tipo_apoyo" id="tipo_apoyo" class="form-select" ></select></div>     
                    <h5 class="text-center">Evento</h5>&nbsp;&nbsp;  
                    <?php 
                        echo $_nombre_evento;
                     ?>  
                    <div> <select name="evento_id" id="evento_id" class="form-select" ></select></div>     
                    <h5 class="text-center">Estadística por período</h5>
                    <?php 
                    echo "Período seleccionado del ".$f_inicio; echo " al ".$f_final;              
                    ?>
                    <br>
                        <label for="fechainicio" class="form-label">Fecha inicio:</label>
                        <input type="date" class="from-control" name='fechainicio'>
                        <label for="fechafinal" class="form-label">Fecha final:</label>                
                        <input type="date" class="from-control" name='fechafinal'>
                        <input type="submit" name="fechas" class="btn btn-primary" value="Consultar"> 


                      
                </form>
                </center>


            </div>
             </div>

                <h1>Actividades</h1>
                            <div id='png_grafico_Pastel6'></div>
                        <div id="grafico_Pastel6" style="height: 600px; width: 800px;"></div><!--GRAFICO 11-->

                        <h5>Metas programadas vs Metas alcanzadas por Actividad</h5>
                        <br> 
                        <div class="col-sm-3 m-l">
                        <button id="btnExportar_table_div" class="btn btn-success">
                        <i class="fas fa-file-excel"></i> Exportar
                        </button> 
                        <p></p>
                        </div>                       
                        <div id="table_div"></div><!--GRAFICO 12-->
                        <br>
                        <p></p>
                        <hr>
                        <h5>Resumen de tipo de acciones</h5>
                        <br>
                        <div class="col-sm-3 m-l">
                        <button id="btnExportar_table_acciones_div" class="btn btn-success">
                        <i class="fas fa-file-excel"></i> Exportar
                        </button> 
                        <p></p>
                        </div> 
                       <center> <div id="table_acciones_div"></div></center><!--GRAFICO 13-->
                       
                        <br>
                        <p></p>
                        <hr>

                        <h5>Desglose de tipo de acciones</h5>
                        <br>    
                        <div class="col-sm-3 m-l">
                        <button id="btnExportar_table_acciones_desglose_div" class="btn btn-success">
                        <i class="fas fa-file-excel"></i> Exportar
                        </button> 
                        <p></p>
                        </div>                     
                        <center><div id="table_acciones_desglose_div"></div></center><!--GRAFICO 14-->
                        <br>
                        <p></p>
                        <hr>

                        <h5>Desglose de eventos realizados</h5>
                        <br>
                        <div class="col-sm-3 m-l">
                        <button id="btnExportar_table_div_evento" class="btn btn-success">
                        <i class="fas fa-file-excel"></i> Exportar
                        </button> 
                        <p></p>
                        </div> 
                        <div id="table_div_evento"></div><!--GRAFICO 15-->
                        <p></p>
                        <hr>
                        <h2>. Presupuesto ejercido a nivel de objeto de gasto (capítulo y monto ejercido)</h2>
                        <p></p>
                        <div id='png_grafico_Pastel10'></div>
                        <div id="grafico_Pastel10" style="height: 600px; width: 800px;"></div><!--GRAFICO 16-->
                        <br>
                        <h5>Desglose de eventos realizados a nivel de capítulos</h5>
                        <br>
                        <div class="col-sm-3 m-l">
                        <button id="btnExportar_table_div_capitulo" class="btn btn-success">
                        <i class="fas fa-file-excel"></i> Exportar
                        </button> 
                        <p></p>
                        </div> 
                        <div id="table_div_capitulo"></div><!--GRAFICO 17-->
                        <p></p>
                        <hr>
                        <h2>. Presupuesto ejercido a nivel de objeto de gasto (partidas y monto ejercido)</h2>
                        <p></p>
                        <div id='png_grafico_Pastel9'></div>
                        <div id="grafico_Pastel9" style="height: 600px; width: 800px;"></div><!--GRAFICO 18-->
                        <br>
                        <h5>Desglose de eventos realizados a nivel de partidas</h5>
                        <br>
                        <div class="col-sm-3 m-l">
                        <button id="btnExportar_table_div_partida" class="btn btn-success">
                        <i class="fas fa-file-excel"></i> Exportar
                        </button> 
                        <p></p>
                        </div> 
                        <div id="table_div_partida"></div><!--GRAFICO 19-->
                        <p></p>
                        <hr>
						<h1>Eventos realizados por Región y Municipio</h1>
                        <div id='png_grafico_Pastel1evento'></div>
                        <div id="grafico_Pastel1evento" style="height: 600px; width: 800px;"></div> 
                        <div id='png_grafico_Pastel2evento'></div> 
                        <p></p>
                        <div class="col-sm-3 m-l">
                        <button id="btnExportar_table_localidad" class="btn btn-success">
                        <i class="fas fa-file-excel"></i> Exportar coordenadas
                        </button> 
                        </div>                     
                        <div id="grafico_Pastel2evento" style="height: 600px; width: 800px;"></div> 
						<div id='png_grafico_Pastel3evento'></div>
                        <div id="grafico_Pastel3evento" style="height: 600px; width: 800px;"></div>

                <!--  ////////////////////-->
                      <table hidden id="tabla_table_div" class="display" style="width:50%">
                                <thead>
                                <tr>                                    
                                <th>Componente</th>
                                <th>Actividad</th>
                                <th>Monto ejercido</th>
                                <th>U.M. Servicios</th>
                                <th>Meta</th>
                                <th>Avance</th>
                                <th>%</th>
                                <th>U.M. Beneficio</th>
                                <th>Meta</th>
                                <th>Avance</th>
                                <th>%</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php while($row = $tabla1_exportar->fetch_assoc()) { ?>
                                <tr>
                                <td><?php echo $row['nombre_componente']; ?></td>
                                <td><?php echo $row['nombre_tipo_apoyo']; ?></td>
                                <td><?php echo $row['programa_presupuesto_ejercido_fechas']; ?></td>
                                <td><?php echo $row['nombre_servicios']; ?></td>
                                <td><?php echo $row['meta_cantidad_servicio']; ?></td>
                                <td><?php echo $row['meta_alcanzada_cantidad_servicio']; ?></td>
                                <td><?php echo $row['porcentaje_servicio']; ?></td>
                                <td><?php echo $row['nombre_beneficios']; ?></td>
                                <td><?php echo $row['meta_cantidad_beneficio']; ?></td>
                                <td><?php echo $row['meta_alcanzada_cantidad_beneficio']; ?></td>
                                <td><?php echo $row['porcentaje_beneficio']; ?></td>
                                </tr>
                                <?php } ?>
                                </tbody>
                     </table>
                     <!--  ////////////////////-->
                     <p></p>
                    <!--  ////////////////////-->
                    <table hidden  id="tabla_table_acciones_div" class="display" style="width:50%">
                                <thead>
                                <tr>                                    
                                <th>Programa</th>
                                <th>Tipo de Acción</th>
                                <th>Personas Atendidas</th>
								<th>Hombres</th>
								<th>Mujeres</th>
                                <th>Monto ejercido</th>
                                <th>Número de acciones</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php while($row = $tabla_acciones1_exportar->fetch_assoc()) { ?>
                                <tr>
                                <td><?php echo $row['nombre_programa']; ?></td>
                                <td><?php echo $row['tipo_accion']; ?></td>
                                <td><?php echo $row['personas_atendidas']; ?></td>
								<td><?php echo $row['hombres']; ?></td>
								<td><?php echo $row['mujeres']; ?></td>
                                <td><?php echo $row['inversion_ejercido']; ?></td>
                                <td><?php echo $row['acciones']; ?></td>
                                </tr>
                                <?php } ?>
                                </tbody>
                    </table>
                    <!--  ////////////////////-->
                    <p></p>
                                <!--  ////////////////////-->
                                <table hidden id="tabla_table_acciones_desglose_div" class="display" style="width:50%">
                                <thead>
                                <tr>                                    
                                <th>Programa</th>
                                <th>Tipo de Acción</th>
                                <th>Evento</th>
                                <th>Personas Atendidas</th>
								<th>Hombres</th>
								<th>Mujeres</th>								
                                <th>Monto ejercido</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php while($row = $tabla_acciones1_desglose_exportar->fetch_assoc()) { ?>
                                <tr>
                                <td><?php echo $row['nombre_programa']; ?></td>
                                <td><?php echo $row['tipo_accion']; ?></td>
                                <td><?php echo $row['nombre_evento']; ?></td>
                                <td><?php echo $row['personas_atendidas']; ?></td>
								<td><?php echo $row['hombres']; ?></td>
								<td><?php echo $row['mujeres']; ?></td>
                                <td><?php echo $row['inversion_ejercido']; ?></td>
                                </tr>
                                <?php } ?>
                                </tbody>
                    </table>
                    <!--  ////////////////////-->
                    <p></p>
                    <tr>    
                                <table hidden id="tabla_table_div_evento" class="display" style="width:50%">                                
                                <th>Actividad</th>
                                <th>Evento</th>
                                <th>Monto ejercido</th>
                                <th>Fecha</th>
                                <th>U.M. Servicios</th>
                                <th>Meta alcanzada</th>
                                <th>U.M. Beneficio</th>
                                <th>Meta alcanzada</th>
                                <th>Hombres'</th>
                                <th>%</th>
                                <th>Mujeres</th>
                                <th>%</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php while($row = $tabla1_evento_exportar->fetch_assoc()) { ?>
                                <tr>
                                <td><?php echo $row['nombre_tipo_apoyo']; ?></td>
                                <td><?php echo $row['nombre_evento']; ?></td>
                                <td><?php echo $row['monto_evento']; ?></td>
                                <td><?php echo $row['fecha_evento']; ?></td>
                                <td><?php echo $row['nombre_servicios']; ?></td>
                                <td><?php echo $row['cantidad_servicio']; ?></td>
                                <td><?php echo $row['nombre_beneficios']; ?></td>
                                <td><?php echo $row['cantidad_beneficio']; ?></td>
                                <td><?php echo $row['hombres']; ?></td>
                                <td><?php echo $row['porcentaje_hombres']; ?></td>
                                <td><?php echo $row['mujeres']; ?></td>
                                <td><?php echo $row['porcentaje_mujeres']; ?></td>
                                </tr>
                                <?php } ?>
                                </tbody>
                    </table>
                    <!--  ////////////////////-->

                    <!--  ////////////////////-->
                   <p></p>
                    <tr>                           
                                <table hidden id="tabla_table_div_capitulo" class="display" style="width:50%">                                
                                <th>Programa</th>
                                <th>Componente</th>
                                <th>Actividad</th>
                                <th>Evento</th>
                                <th>Capítulo</th>
                                <th>Monto</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php while($row = $tabla1_capitulo_exportar->fetch_assoc()) { ?>
                                <tr>
                                <td><?php echo $row['nombre_programa']; ?></td>
                                <td><?php echo $row['nombre_componente']; ?></td>
                                <td><?php echo $row['nombre_tipo_apoyo']; ?></td>
                                <td><?php echo $row['nombre_evento']; ?></td>
                                <td><?php echo $row['capitulo']; ?></td>
                                <td><?php echo $row['capitulo_total']; ?></td>
                                </tr>
                                <?php } ?>
                                </tbody>
                    </table>
                    <!--  ////////////////////-->

                    <!--  ////////////////////-->
                    <p></p>
                    <tr>                           
                                <table hidden id="tabla_table_div_partida" class="display" style="width:50%">                                
                                <th>Programa</th>
                                <th>Componente</th>
                                <th>Actividad</th>
                                <th>Evento</th>
                                <th>Capítulo</th>
                                <th>Partida</th>
                                <th>Monto</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php while($row = $tabla1_partida_exportar->fetch_assoc()) { ?>
                                <tr>
                                <td><?php echo $row['nombre_programa']; ?></td>
                                <td><?php echo $row['nombre_componente']; ?></td>
                                <td><?php echo $row['nombre_tipo_apoyo']; ?></td>
                                <td><?php echo $row['nombre_evento']; ?></td>
                                <td><?php echo $row['capitulo']; ?></td>
                                <td><?php echo $row['partida_gasto_texto']; ?></td>
                                <td><?php echo $row['monto_partidas']; ?></td>
                                </tr>
                                <?php } ?>
                                </tbody>
                    </table>
                    <!--  ////////////////////-->

                    <!--  ////////////////////-->
                   <p></p>
                    <tr>                           
                                <table hidden id="tabla_localidad_exportar" class="display" style="width:50%">                                
                                <th>Programa</th>
                                <th>Componente</th>
                                <th>Actividad</th>
                                <th>Evento</th>
                                <th>Fecha</th>
                                <th>Región</th>
                                <th>Municipio</th>
                                <th>Localidad</th>
                                <th>Latitud</th>
                                <th>Longitud</th>
                                <th>Cantidad</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php while($row = $tablalocexportar->fetch_assoc()) { ?>
                                <tr>
                                <td><?php echo $row['nombre_programa']; ?></td>
                                <td><?php echo $row['nombre_componente']; ?></td>
                                <td><?php echo $row['nombre_tipo_apoyo']; ?></td>
                                <td><?php echo $row['nombre_evento']; ?></td>
                                <td><?php echo $row['fecha_evento']; ?></td>
                                <td><?php echo $row['region']; ?></td>
                                <td><?php echo $row['municipio']; ?></td>
                                <td><?php echo $row['localidad']; ?></td>
                                <td><?php echo $row['latitud_dec']; ?></td>
                                <td><?php echo $row['longitud_dec']; ?></td>
                                <td><?php echo $row['Total_Cuenta']; ?></td>                                
                                </tr>
                                <?php } ?>
                                </tbody>
                    </table>
                    <!--  ////////////////////-->

                     </div>
       <br>

    </div>  

    <script src="assets/js/bootstrap.bundle.min.js"></script>
    
            <!-- script para exportar a excel -->
            <script>
                const $btnExportar_table_div = document.querySelector("#btnExportar_table_div"),
                    $tabla_table_div = document.querySelector("#tabla_table_div");

                $btnExportar_table_div.addEventListener("click", function() {
                    let tableExport = new TableExport($tabla_table_div, {
                        exportButtons: false, // No queremos botones
                        filename: "Metas programadas vs Metas alcanzadas por Actividad", //Nombre del archivo de Excel
                        sheetname: "Met_Pog_VS_Met_Alc_por_Act", //Título de la hoja
                    });
                    let datos = tableExport.getExportData();
                    let preferenciasDocumento = datos.tabla_table_div.xlsx;
                    tableExport.export2file(preferenciasDocumento.data, preferenciasDocumento.mimeType, preferenciasDocumento.filename, preferenciasDocumento.fileExtension, preferenciasDocumento.merges, preferenciasDocumento.RTL, preferenciasDocumento.sheetname);
                });
            </script>
           
            <!-- script para exportar a excel -->
            <script>
                const $btnExportar_table_acciones_div = document.querySelector("#btnExportar_table_acciones_div"),
                    $tabla_table_acciones_div = document.querySelector("#tabla_table_acciones_div");

                $btnExportar_table_acciones_div.addEventListener("click", function() {
                    let tableExport = new TableExport($tabla_table_acciones_div, {
                        exportButtons: false, // No queremos botones
                        filename: "Resumen de tipo de acciones", //Nombre del archivo de Excel
                        sheetname: "Res_Tip_Acc", //Título de la hoja
                    });
                    let datos = tableExport.getExportData();
                    let preferenciasDocumento = datos.tabla_table_acciones_div.xlsx;
                    tableExport.export2file(preferenciasDocumento.data, preferenciasDocumento.mimeType, preferenciasDocumento.filename, preferenciasDocumento.fileExtension, preferenciasDocumento.merges, preferenciasDocumento.RTL, preferenciasDocumento.sheetname);
                });
            </script>

            <!-- script para exportar a excel -->
            <script>
            const $btnExportar_table_acciones_desglose_div = document.querySelector("#btnExportar_table_acciones_desglose_div"),
                $tabla_table_acciones_desglose_div = document.querySelector("#tabla_table_acciones_desglose_div");

            $btnExportar_table_acciones_desglose_div.addEventListener("click", function() {
                let tableExport = new TableExport($tabla_table_acciones_desglose_div, {
                    exportButtons: false, // No queremos botones
                    filename: "Desglose de tipo de acciones", //Nombre del archivo de Excel
                    sheetname: "Des_Tip_Acc", //Título de la hoja
                });
                let datos = tableExport.getExportData();
                let preferenciasDocumento = datos.tabla_table_acciones_desglose_div.xlsx;
                tableExport.export2file(preferenciasDocumento.data, preferenciasDocumento.mimeType, preferenciasDocumento.filename, preferenciasDocumento.fileExtension, preferenciasDocumento.merges, preferenciasDocumento.RTL, preferenciasDocumento.sheetname);
            });
            </script>
        
            <!-- script para exportar a excel -->
            <script>
                const $btnExportar_table_div_evento = document.querySelector("#btnExportar_table_div_evento"),
                    $tabla_table_div_evento = document.querySelector("#tabla_table_div_evento");

                $btnExportar_table_div_evento.addEventListener("click", function() {
                    let tableExport = new TableExport($tabla_table_div_evento, {
                        exportButtons: false, // No queremos botones
                        filename: "Desglose de eventos realizados", //Nombre del archivo de Excel
                        sheetname: "Deg_Eve_Rea", //Título de la hoja
                    });
                    let datos = tableExport.getExportData();
                    let preferenciasDocumento = datos.tabla_table_div_evento.xlsx;
                    tableExport.export2file(preferenciasDocumento.data, preferenciasDocumento.mimeType, preferenciasDocumento.filename, preferenciasDocumento.fileExtension, preferenciasDocumento.merges, preferenciasDocumento.RTL, preferenciasDocumento.sheetname);
                });
            </script>

            <!-- script para exportar a excel -->
            <script>
            const $btnExportar_table_div_capitulo = document.querySelector("#btnExportar_table_div_capitulo"),
                $tabla_table_div_capitulo = document.querySelector("#tabla_table_div_capitulo");

            $btnExportar_table_div_capitulo.addEventListener("click", function() {
                let tableExport = new TableExport($tabla_table_div_capitulo, {
                    exportButtons: false, // No queremos botones
                    filename: "Desglose de eventos realizados a nivel de capítulos", //Nombre del archivo de Excel
                    sheetname: "Deg_Eve_Rea_Niv_Cap", //Título de la hoja
                });
                let datos = tableExport.getExportData();
                let preferenciasDocumento = datos.tabla_table_div_capitulo.xlsx;
                tableExport.export2file(preferenciasDocumento.data, preferenciasDocumento.mimeType, preferenciasDocumento.filename, preferenciasDocumento.fileExtension, preferenciasDocumento.merges, preferenciasDocumento.RTL, preferenciasDocumento.sheetname);
            });
            </script>

            <!-- script para exportar a excel -->
            <script>
            const $btnExportar_table_div_partida = document.querySelector("#btnExportar_table_div_partida"),
                $tabla_table_div_partida = document.querySelector("#tabla_table_div_partida");

            $btnExportar_table_div_partida.addEventListener("click", function() {
                let tableExport = new TableExport($tabla_table_div_partida, {
                    exportButtons: false, // No queremos botones
                    filename: "Desglose de eventos realizados a nivel de partidas", //Nombre del archivo de Excel 
                    sheetname: "Deg_Eve_Rea_Niv_Par", //Título de la hoja
                });
                let datos = tableExport.getExportData();
                let preferenciasDocumento = datos.tabla_table_div_partida.xlsx;
                tableExport.export2file(preferenciasDocumento.data, preferenciasDocumento.mimeType, preferenciasDocumento.filename, preferenciasDocumento.fileExtension, preferenciasDocumento.merges, preferenciasDocumento.RTL, preferenciasDocumento.sheetname);
            });

            </script>

            <!-- script para exportar a excel -->
            <script>
            const $btnExportar_table_localidad = document.querySelector("#btnExportar_table_localidad"),
                $tabla_localidad_exportar = document.querySelector("#tabla_localidad_exportar");

            $btnExportar_table_localidad.addEventListener("click", function() {
                let tableExport = new TableExport($tabla_localidad_exportar, {
                    exportButtons: false, // No queremos botones
                    filename: "Desglose de eventos realizados a nivel localidad", //Nombre del archivo de Excel 
                    sheetname: "Deg_Eve_Rea_Niv_Loc", //Título de la hoja
                });
                let datos = tableExport.getExportData();
                let preferenciasDocumento = datos.tabla_localidad_exportar.xlsx;
                tableExport.export2file(preferenciasDocumento.data, preferenciasDocumento.mimeType, preferenciasDocumento.filename, preferenciasDocumento.fileExtension, preferenciasDocumento.merges, preferenciasDocumento.RTL, preferenciasDocumento.sheetname);
            });

            </script>


    <script>
      function inhabilitar(){
          alert ("Esta función está inhabilitada.\n\n SIE-SEMUJER.")
          return false
      }
      document.oncontextmenu = inhabilitar
    </script>
  </body>

</html>

