
<?php
include('config.php');
$cedula    = $_REQUEST['cedula'];

//Verificando si existe algun cliente en bd ya con dicha cedula asignada
//Preparamos un arreglo que es el que contendrá toda la información
$jsonData = array();
$selectQuery   = ("SELECT cedula FROM clientes WHERE cedula='".$cedula."' ");
$query         = mysqli_query($con, $selectQuery);
$totalCliente  = mysqli_num_rows($query);

  //Validamos que la consulta haya retornado información
  if( $totalCliente <= 0 ){
    $jsonData['success'] = 0;
   // $jsonData['message'] = 'No existe Cédula ' .$cedula;
    $jsonData['message'] = '';
} else{
    //Si hay datos entonces retornas algo
    $jsonData['success'] = 1;
    $jsonData['message'] = '<p style="color:red;">Ya existe la Cédula <strong>(' .$cedula.')<strong></p>';
  }

//Mostrando mi respuesta en formato Json
header('Content-type: application/json; charset=utf-8');
echo json_encode( $jsonData );
?>

<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link type="text/css" rel="shortcut icon" href="img/logo-mywebsite-urian-viera.svg"/>
  <title>Como validar si ya existe el Cliente en BD antes de enviar el formulario en tiempo real :: WebDeveloper Urian Viera</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/material-design-iconic-font/2.2.0/css/material-design-iconic-font.min.css">
  <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
  <link rel="stylesheet" type="text/css" href="css/cargando.css">
  <link rel="stylesheet" type="text/css" href="css/maquinawrite.css">
  <style> 
        table tr th{
            background:rgba(0, 0, 0, .6);
            color: #fff;
        }
        tbody tr{
          font-size: 12px !important;

        }
        h3{
            color:crimson; 
            margin-top: 100px;
        }
        a:hover{
            cursor: pointer;
            color: #333 !important;
        }
        em{
          font-size: 15px;
        }
      </style>
</head>
<body>
  
<div class="cargando">
    <div class="loader-outter"></div>
    <div class="loader-inner"></div>
</div>

<nav class="navbar navbar-expand-lg navbar-light navbar-dark fixed-top" style="background-color: #563d7c !important;">
    <ul class="navbar-nav mr-auto collapse navbar-collapse">
      <li class="nav-item active">
        <a href="index.php"> 
          <img src="img/logo-mywebsite-urian-viera.svg" alt="Web Developer Urian Viera" width="120">
        </a>
      </li>
    </ul>
    <div class="my-2 my-lg-0" id="maquinaescribir">
      <h5 class="navbar-brand">Web Developer Urian Viera <span>&#160;</span></h5>
    </div>
</nav>



<div class="container mt-5 p-5">

  <h4 class="text-center">
    Como validar si ya existe el Cliente en BD antes de enviar el formulario en tiempo real
    <br>
    <em>(PHP-MYSQL-JQUERY-AJAX-JAVASCRIPT-BOOTSTRAP)</em>
    <br>
    <em>(Regresar una respuesta a la petición AJAX - Validar una respuesta de una petición AJAX)</em>
  </h4>
  <hr>


<div class="row text-center" style="background-color: #cecece">
  <div class="col-md-6"> 
    <strong>Registrar Nuevo Cliente</strong>
  </div>
  <div class="col-md-6"> 
    <strong>Lista de Clientes </strong>
  </div>
</div>

<div class="row clearfix">
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
  <div class="body">
      <div class="row clearfix">

        <!----- formulario --->
        <div class="col-sm-5">
          <form name="formCliente" id="formCliente" action="" method="POST">
              <div class="row">
                
                <div class="col-md-12 mt-2">
                    <label for="name" class="form-label">Cédula del Cliente <em>(RUT -DIN)</em></label>
                    <input type="number" class="form-control" name="cedula" id="cedula" required='true' autofocus>
                    <div id="respuesta"> </div>
                </div>

                <div class="col-md-12">
                    <label for="name" class="form-label">Nombre del Cliente</label>
                    <input type="text" class="form-control" name="nombre" id="nombre" required='true' autofocus>
                </div>
                <div class="col-md-12 mt-2">
                    <label for="email" class="form-label">Correo</label>
                    <input type="email" class="form-control" name="correo" id="correo" required='true'>
                </div>
                <div class="col-md-12 mt-2">
                    <label for="celular" class="form-label">Celular</label>
                    <input type="number" class="form-control" name="celular" id="celular" required='true'>
                </div>

              </div>
                <div class="row justify-content-start text-center mt-5">
                    <div class="col-12">
                        <button class="btn btn-primary btn-block" value="Registrar Nuevo Cliente" id="btnEnviar">
                           <i class="zmdi zmdi-spinner zmdi-hc-lg zmdi-hc-spin"></i>
                            Registrar Nuevo Cliente
                        </button>
                    </div>
                </div>
          </form>
        </div>  
      <!--fin form -->

         

          <div class="col-sm-7">
              <div class="row" id="listClientes">


              </div>
          </div>



        </div>
      </div>
  </div>
</div>
</div>


<script src="js/jquery-2.2.4.min.js" type="text/javascript"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/popper.min.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
      //Apenas cargue el La pagina cargara la lista de Clientes.
      $("#listClientes").load("listClientes.php"); //load es una funcion de Jquery
      $(".zmdi-hc-spin").hide(); //Oculto la animacion del boton enviar

      //Efecto Pre-Carga
      $(window).load(function() {
          $(".cargando").fadeOut(500);
      });


    //Codigo para limitar la cantidad maxima que tendra dicho Input
    $('input#cedula').keypress(function (event) {
      if (event.which < 48 || event.which > 57 || this.value.length === 5) {
        return false;
      }
    });
    

    //Validar la cantidad maxima en el campo celular
    $('input#celular').keypress(function (event) {
      if (event.which < 48 || event.which > 57 || this.value.length === 10) {
        return false;
      }
    });


//Validando si existe la Cedula en BD antes de enviar el Form
$("#cedula").on("keyup", function() {
  var cedula = $("#cedula").val(); //CAPTURANDO EL VALOR DE INPUT CON ID CEDULA
  var longitudCedula = $("#cedula").val().length; //CUENTO LONGITUD

//Valido la longitud 
  if(longitudCedula >= 3){
    var dataString = 'cedula=' + cedula;

      $.ajax({
          url: 'verificarCedula.php',
          type: "GET",
          data: dataString,
          dataType: "JSON",

          success: function(datos){

                if( datos.success == 1){

                $("#respuesta").html(datos.message);

                $("input").attr('disabled',true); //Desabilito el input nombre
                $("input#cedula").attr('disabled',false); //Habilitando el input cedula
                $("#btnEnviar").attr('disabled',true); //Desabilito el Botton

                }else{

                $("#respuesta").html(datos.message);

                $("input").attr('disabled',false); //Habilito el input nombre
                $("#btnEnviar").attr('disabled',false); //Habilito el Botton

                    }
                  }
                });
              }
          });


        //Funcion para enviar el formulario de registro.
        $('#btnEnviar').click(function(e){
            e.preventDefault();

          //Muestro el efecto cargando en el boton
          $(".zmdi-hc-spin").show();  

          setTimeout(function() {
            $(".zmdi-hc-spin").hide();
            $("#btnEnviar").attr('disabled',false); //Desabilito el boton enviar
          }, 1000);

          url = "nuevoCliente.php";
          $.ajax({
              type: "POST",
              url: url,
              data: $("#formCliente").serialize(),
              success: function(datos)
              {
                $("#listClientes").load("listClientes.php"); //Cargo nuevamenta la lista de Clientes, pero ya actualizada.
                $("#formCliente")[0].reset(); //Limpio todos los input de mi formulario
              }
          });
        });


 });
      
</script>

</body>
</html>

ALTER TABLE `personal_com` ADD PRIMARY KEY(`id_personal_com`);
INSERT INTO personal_com (id_personal_com, pc_concepto) VALUES
('0', '0.NINGUNO'),
('1', '1.BRIGADISTA Y/O PROMOTOR(A) DE SALUD'),   
('2', '2.AUXILIAR DE SALUD'),
('3', '3.ENFERMERÍA'),   
('4', '4.PARTERÍA PROFESIONAL'),
('5', '5.PARTERÍA TRADICIONAL'),   
('6', '6.SUPERVISOR(A) DE AUXILIARES DE SALUD'),
('7', '7.MÉDICA(O)'),   
('8', '8.OTRO');

USE CNATAL;
CREATE TABLE trimestre ( id_trimestre INT NOT NULL,  tri_concepto VARCHAR(50) NOT NULL );
ALTER TABLE trimestre ADD PRIMARY KEY(id_trimestre);
INSERT INTO trimestre (id_trimestre, tri_concepto) VALUES
('1', '1° (0 a 13.6 semanas)'),
('2', '2° (14 a 26.6 semanas) '),   
('3', '3° (27 a 42.6 semanas)');

USE CNATAL;
CREATE TABLE trans_vector ( id_trans_vector INT NOT NULL,  tv_concepto VARCHAR(50) NOT NULL );
ALTER TABLE trans_vector ADD PRIMARY KEY(id_trans_vector);
INSERT INTO trans_vector (id_trans_vector, tv_concepto) VALUES
('0', '0.NINGUNA'),
('1', '1.DENGUE'),
('2', '2.ZIKA'), 
('3', '3.CHIKUNGUNYA'), 
('4', '4.CHAGAS');

USE CNATAL;
CREATE TABLE o_antecedente ( id_o_antecedente INT NOT NULL,  oa_concepto VARCHAR(50) NOT NULL );
ALTER TABLE o_antecedente ADD PRIMARY KEY(id_o_antecedente);
INSERT INTO o_antecedente (id_o_antecedente, oa_concepto) VALUES
('0', '0.NINGUNO'),
('1', '1.ENDOCRINOPATÍA'),
('2', '2.EPILEPSIA'), 
('3', '3.LUPUS'), 
('4', '4.ARTRITIS REUMATOIDE'), 
('5', '5.TOXOPLASMOSIS'), 
('6', '6.ITS'), 
('7', '7.HEPATITIS B'), 
('8', '8.HEPATITIS C'), 
('9', '9.INSUFICIENCIA VENOSA'), 
('10', '10.TROMBOSIS VENOSA PROFUNDA'), 
('11', '11.CAPACIDAD MENTAL DISMINUIDA'); 

USE CNATAL;
CREATE TABLE gpo_sanguineo ( id_gpo_sanguineo VARCHAR(3) NOT NULL,  gs_concepto VARCHAR(3) NOT NULL );
ALTER TABLE gpo_sanguineo ADD PRIMARY KEY(id_gpo_sanguineo);
INSERT INTO gpo_sanguineo (id_gpo_sanguineo, gs_concepto) VALUES
A+('A+', 'A+'),
B+('B+', 'B+'), 
AB+('AB+', 'AB+'),
AB-('AB-', 'AB-'), 
A-('A-', 'A-'),
B-('B-', 'B-'), 
O+('O+', 'O+'),
O-('O-', 'O-');

USE CNATAL;
CREATE TABLE riesgos (id_riesgos INT NOT NULL,  r_concepto VARCHAR(50) NOT NULL );
ALTER TABLE riesgos ADD PRIMARY KEY(id_riesgos);
INSERT INTO riesgos (id_riesgos, r_concepto) VALUES
('1', '1.PATOLOGÍA CRÓNICA ÓRGANO FUNCIONAL'),
('2', '2.PATOLOGÍA CRÓNICA INFECCIOSA'), 
('3', '3.PATOLOGÍA MORBILIDAD MATERNA EXTREMA'),
('4', '4.CON FACTORES DE RIESGO SOCIALES'), 
('5', '5.ANTECEDENTES OBSTÉTRICOS DE RIESGO'),
('0', '0.NINGUNA');

USE CNATAL;
CREATE TABLE res_ult_emb (id_res_ult_emb INT NOT NULL,  rue_concepto VARCHAR(50) NOT NULL );
ALTER TABLE res_ult_emb ADD PRIMARY KEY(id_res_ult_emb);
INSERT INTO res_ult_emb (id_res_ult_emb, rue_concepto) VALUES
('1', '1.ABORTO'),
('2', '2.CESÁREA'), 
('3', '3.MOLA'),
('4', '4.PARTO EUTÓCICO O DISTÓCICO');

USE CNATAL;
CREATE TABLE anticonceptivos (id_anticonceptivos INT NOT NULL,  anti_concepto VARCHAR(50) NOT NULL );
ALTER TABLE anticonceptivos ADD PRIMARY KEY(id_anticonceptivos);
INSERT INTO anticonceptivos (id_anticonceptivos, anti_concepto) VALUES
('0', '0.NINGUNO'),
('1', '1.HORMONAL'), 
('2', '2.DIU (INCLUYE DIU MEDICADO)'),
('3', '3.PRESERVATIVO'),
('4', '4.OTRO MÉTODO');


USE CNATAL;
CREATE TABLE ant_gineco_otro (id_ant_gineco_otro INT NOT NULL,  agineco_concepto VARCHAR(50) NOT NULL );
ALTER TABLE ant_gineco_otro ADD PRIMARY KEY(id_ant_gineco_otro);
INSERT INTO ant_gineco_otro (id_ant_gineco_otro, agineco_concepto) VALUES
('1', '1.CIRUGÍA UTERINA PREVIA O MIOMATOSIS'),
('2', '2.TRANSFUSIÓN SANGUÍNEA EN EVENTO OBSTÉTRICO'), 
('3', '3.POLIHIDRAMNIOS'),
('4', '4.PARTO PRETÉRMINO'), 
('5', '5.PRODUCTO MACROSÓMICO'),
('6', '6.PRODUCTO BAJO PESO'), 
('7', '7.INCOMPETENCIA ÍSTMICO-CERVICAL');

USE CNATAL;
CREATE TABLE baja_embarazo (id_baja_embarazo INT NOT NULL,  be_concepto VARCHAR(50) NOT NULL );
ALTER TABLE baja_embarazo ADD PRIMARY KEY(id_baja_embarazo);
INSERT INTO baja_embarazo (id_baja_embarazo, be_concepto) VALUES
('1', '1.RESOLUCIÓN DEL EMBARAZO'),
('2', '2.CAMBIO DE DOMICILIO'),
('3', '3.DEFUNCIÓN'),
('4', '4.BAJA POR ERROR EN DIAGNÓSTICO');





//consulta de catalogos
$queryTP = "SELECT id_tipo_personal, tp_concepto FROM tipo_personal ORDER BY id_tipo_personal";
$resultadoTP = $mysqli->query($queryTP);

$queryServ = "SELECT id_servicio, s_concepto FROM servicio ORDER BY id_servicio";
$resultadoServ = $mysqli->query($queryServ);

$queryEnti = "SELECT id_entidad, e_concepto FROM entidad ORDER BY id_entidad";
$resultadoEnti = $mysqli->query($queryEnti);

$queryDer = "SELECT id_derechohabiencia, der_concepto FROM derechohabiencia ORDER BY id_derechohabiencia";
$resultadoDer = $mysqli->query($queryDer);

$queryEc = "SELECT id_edo_conyugal, ec_concepto FROM edo_conyugal ORDER BY id_edo_conyugal";
$resultadoEc = $mysqli->query($queryEc);

$queryEsc = "SELECT id_escolaridad, esc_concepto FROM escolaridad ORDER BY id_escolaridad";
$resultadoEsc = $mysqli->query($queryEsc);

$queryLenH = "SELECT id_lengua_habla, len_concepto FROM lengua_habla ORDER BY id_lengua_habla";
$resultadoLenH = $mysqli->query($queryLenH);

$queryPC = "SELECT id_personal_com, pc_concepto FROM personal_com ORDER BY id_personal_com";
$resultadoPC = $mysqli->query($queryPC);

$queryTri = "SELECT id_trimestre, tri_concepto FROM trimestre ORDER BY id_trimestre";
$resultadoTri = $mysqli->query($queryTri);

$queryTV = "SELECT id_trans_vector, tv_concepto FROM trans_vector ORDER BY id_trans_vector";
$resultadoTV = $mysqli->query($queryTV);

$queryOA = "SELECT id_o_antecedente, oa_concepto FROM o_antecedente ORDER BY id_o_antecedente";
$resultadoOA = $mysqli->query($queryOA);

$queryGS = "SELECT id_gpo_sanguineo, gs_concepto FROM gpo_sanguineo ORDER BY id_gpo_sanguineo";
$resultadoGS = $mysqli->query($queryGS);

$queryRi = "SELECT id_riesgos, r_concepto FROM riesgos ORDER BY id_riesgos";
$resultadoRi = $mysqli->query($queryRi);

$queryUemb = "SELECT id_res_ult_emb, rue_concepto FROM res_ult_emb ORDER BY id_res_ult_emb";
$resultadoUemb = $mysqli->query($queryUemb);

$queryAnti = "SELECT id_anticonceptivos, anti_concepto FROM anticonceptivos ORDER BY id_anticonceptivos";
$resultadoAnti = $mysqli->query($queryAnti);

$queryAgi = "SELECT id_ant_gineco_otro, agineco_concepto FROM ant_gineco_otro ORDER BY id_ant_gineco_otro";
$resultadoAgi = $mysqli->query($queryAgi);

$queryAcom = "SELECT id_acompanante, a_concepto FROM acompanante ORDER BY id_acompanante";
$resultadoAcom = $mysqli->query($queryAcom);

$querySSA = "SELECT id_sig_sin_alarma, ssa_concepto FROM sig_sin_alarma ORDER BY id_sig_sin_alarma";
$resultadoSSA = $mysqli->query($querySSA);

$queryMedica = "SELECT id_medicamentos_emba, me_emba_concepto FROM medicamentos_emba ORDER BY id_medicamentos_emba";
$resultadoMedica = $mysqli->query($queryMedica);


$queryOE = "SELECT id_orientacion_edu, oe_concepto FROM orientacion_edu ORDER BY id_orientacion_edu";
$resultadoOE = $mysqli->query($queryOE);

$queryRef= "SELECT id_refenecia, re_concepto FROM refenecia ORDER BY id_refenecia";
$resultadoRef = $mysqli->query($queryRef);


$queryMRef= "SELECT id_motivo_referencia, mre_concepto FROM motivo_referencia ORDER BY id_motivo_referencia";
$resultadoMRef = $mysqli->query($queryMRef);             

USE CNATAL;
CREATE TABLE acompanante (id_acompanante INT NOT NULL,  id_acompanante VARCHAR(50) NOT NULL );
ALTER TABLE acompanante ADD PRIMARY KEY(id_acompanante);
INSERT INTO acompanante (id_acompanante, a_concepto) VALUES
('1' '1.PAREJA'),
('2' '2.FAMILIAR'),
('3' '3.NADIE'),
('4' '4.MADRINA/PADRINO OBSTÉTRICA(O)');
$queryAcom = "SELECT id_acompanante, a_concepto FROM acompanante ORDER BY id_acompanante";
$resultadoAcom = $mysqli->query($queryAcom);


USE CNATAL;
CREATE TABLE sig_sin_alarma (id_sig_sin_alarma INT NOT NULL,  ssa_concepto VARCHAR(50) NOT NULL );
ALTER TABLE sig_sin_alarma ADD PRIMARY KEY(id_sig_sin_alarma);
INSERT INTO sig_sin_alarma (id_sig_sin_alarma, ssa_concepto) VALUES
('0' '0.NINGUNO'),
('1' '1.EPIGASTRALGIA'),
('2' '2.HIPOMOVILIDAD FETAL'),
('3' '3.HEMORRAGIA'),
('4' '4.FIEBRE'),
('5' '5.SALIDA DE LÍQUIDO'),
('6' '6.DIFICULTAD PARA RESPIRAR'),
('7' '7.CEFALEA INTENSA'),
('8' '8.ICTERICIA'),
('9' '9.CONVULSIONES'),
('10' '10.PALIDÉZ'),
('11' '11.CAMBIOS DE ÁNIMO'),
('12' '12.FLUJO VAGINAL'),
('13' '13.AMAUROSIS'),
('14' '14.ACUFENOS'),
('15' '15.FOSFENOS'),
('16' '16.EDEMA'),
('17' '17.ERITEMA PALMAR'),
('18' '18.DOLOR EN ÁREA HEPÁTICA'),
('88' '88. OTROS');
$querySSA = "SELECT id_sig_sin_alarma, ssa_concepto FROM sig_sin_alarma ORDER BY id_sig_sin_alarma";
$resultadoSSA = $mysqli->query($querySSA);

USE CNATAL;
CREATE TABLE medicamentos_emba (id_medicamentos_emba INT NOT NULL,  me_emba_concepto VARCHAR(50) NOT NULL );
ALTER TABLE medicamentos_emba ADD PRIMARY KEY(id_medicamentos_emba);
INSERT INTO medicamentos_emba (id_medicamentos_emba, me_emba_concepto) VALUES
('0', '0.NINGUNO'),
('1', '1.VITAMINAS Y ÁCIDO FÓLICO'),
('2', '2.ANTIANÉMICO'),
('3', '3.CORTICOIDES PARA MADURACIÓN PULMONAR'),
('4', '4.ANALGÉSICO Y ANTIPIRÉTICO Y ANTIINFLAMATORIOS NO ESTEROIDEOS'),
('5', '5.ANTIHISTAMÍNICOS H2'),
('6', '6.ANTIBIÓTICOS'),
('7', '7.ANTICONVULSIVANTE'),
('8', '8.ANTIEMÉTICO'),
('9', '9.ANTIESPASMÓDICO'),
('10', '10.ANTIHIPERTENSIVO'),
('11', '11.ANTIMICÓTICOS Y ANTIPARASITARIOS'),
('12', '12.ANTIVIRAL'),
('13', '13.HIPOGLUCEMIANTE (ORALES E INYECTABLES)'),
('14', '14.UTEROINHIBIDORES'),
('15', '15.ANTIFÍMICOS');    

$queryMedica = "SELECT id_medicamentos_emba, me_emba_concepto FROM medicamentos_emba ORDER BY id_medicamentos_emba";
$resultadoMedica = $mysqli->query($queryMedica);



USE CNATAL;
CREATE TABLE orientacion_edu (id_orientacion_edu INT NOT NULL,  oe_concepto VARCHAR(50) NOT NULL );
ALTER TABLE orientacion_edu ADD PRIMARY KEY(id_orientacion_edu);
INSERT INTO orientacion_edu (id_orientacion_edu, oe_concepto) VALUES
('1', '1.CUIDADOS DEL EMBARAZO Y PUERPERIO'),
('2', '2.SIGNOS Y SÍNTOMAS DE ALARMA'),
('3', '3.CALIDAD E HIGIENE DE LOS ALIMENTOS'),
('4', '4.USO DE MEDICAMENTOS DURANTE EL EMBARAZO'),
('5', '5.ESTILOS DE VIDA SALUDABLES'),
('6', '6.METODOLOGÍA ANTICONCEPTIVA'),
('7', '7.CUIDADOS DE LA PERSONA RECIÉN NACIDA'),
('8', '8.LACTANCIA MATERNA'),
('9', '9.DETECCIÓN OPORTUNA DE DEFECTOS AL NACIMIENTO'),
('88', '88.OTROS');
$queryOE = "SELECT id_orientacion_edu, oe_concepto FROM orientacion_edu ORDER BY id_orientacion_edu";
$resultadoOE = $mysqli->query($queryOE);

USE CNATAL; 
CREATE TABLE refenecia (id_refenecia INT NOT NULL,  re_concepto VARCHAR(50) NOT NULL );
ALTER TABLE refenecia ADD PRIMARY KEY(id_refenecia);
INSERT INTO refenecia (id_refenecia, re_concepto) VALUES                      
('1', '1.MÓDULO MATER'),
('2', '2.CONSULTA EXTERNA CON ESPECIALISTA'),
('3', '3.URGENCIA OBSTÉTRICA'),
('4', '4.UNEME CAPASITS');
$queryRef= "SELECT id_refenecia, re_concepto FROM refenecia ORDER BY id_refenecia";
$resultadoRef = $mysqli->query($queryRef);

  
USE CNATAL; 
CREATE TABLE motivo_referencia (id_motivo_referencia INT NOT NULL,  mre_concepto VARCHAR(50) NOT NULL );
ALTER TABLE motivo_referencia ADD PRIMARY KEY(id_motivo_referencia);
INSERT INTO motivo_referencia (id_motivo_referencia, mre_concepto) VALUES                        
('1', '1.BAJO RIESGO (DE TÉRMINO)'),
('2', '2.ALTO RIESGO'),
('3', '3.URGENCIA OBSTÉTRICA'),
('4', '4.VIOLENCIA FAMILIAR'),
('5', '5.DEPRESIÓN PRENATAL');  
$queryMRef= "SELECT id_motivo_referencia, mre_concepto FROM motivo_referencia ORDER BY id_motivo_referencia";
$resultadoMRef = $mysqli->query($queryMRef);                   

resultado
USE CNATAL; 
CREATE TABLE resultado (id_resultado INT NOT NULL,  rv_concepto VARCHAR(50) NOT NULL );
ALTER TABLE resultado ADD PRIMARY KEY(id_resultado);
INSERT INTO resultado (id_resultado, rv_concepto) VALUES
('1', 1.NUEVA CITA'),
('2', 2.NO SE ENCONTRÓ'),
('3', 3.CAMBIO DE DOMICILIO'),
('4', 4.RECHAZO A LA ATENCIÓN'),
('8', 8.OTRO');
$queryMRef= "SELECT id_resultado, rv_concepto FROM resultado ORDER BY id_resultado";
$resultadoMRef = $mysqli->query($queryMRef); 

personalvisita
USE CNATAL; 
CREATE TABLE personalvisita (id_personalvisita INT NOT NULL,  pv_concepto VARCHAR(50) NOT NULL );
ALTER TABLE personalvisita ADD PRIMARY KEY(id_personalvisita);
INSERT INTO personalvisita (id_personalvisita, pv_concepto) VALUES
('1', 1.TRABAJO SOCIAL'),
('2', 2.BRIGADISTA Y/O PROMOTOR(A) DE SALUD'),
('3', 3.PARTERÍA PROFESIONAL'),
('4', 4.ENFERMERÍA'),
('5', 5.AUXILIAR DE SALUD'),
('6', 6.MÉDICA(O)');
$queryPV= "SELECT id_personalvisita, pv_concepto FROM personalvisita ORDER BY id_personalvisita";
$resultadoPV = $mysqli->query($queryPV); 


USE CNATAL; 
CREATE TABLE sexo_rn (id_sexo_rn INT NOT NULL,  srn_concepto VARCHAR(12) NOT NULL );
ALTER TABLE sexo_rn ADD PRIMARY KEY(id_sexo_rn);
INSERT INTO sexo_rn (id_sexo_rn, srn_concepto) VALUES
('1', 'MUJER'),
('2', 'HOMBRE'),
('3', 'INTERSEXUAL');
$querySRN= "SELECT id_sexo_rn, srn_concepto FROM sexo_rn ORDER BY id_sexo_rn";
$resultadoSRN = $mysqli->query($querySRN);

sig_sin_alarma_puer_lact
USE CNATAL; 
CREATE TABLE sig_sin_alarma_puer_lact (id_sig_sin_alarma_puer_lact INT NOT NULL,  ssapl_concepto VARCHAR(75) NOT NULL );
ALTER TABLE sig_sin_alarma_puer_lact ADD PRIMARY KEY(id_sig_sin_alarma_puer_lact);
INSERT INTO sig_sin_alarma_puer_lact (id_sig_sin_alarma_puer_lact, ssapl_concepto) VALUES
('0', '0.NINGUNO'),
('1', '1.LOQUIOS FÉTIDOS'),
('2', '2.SANGRADO TRANSVAGINAL'),
('3', '3.FIEBRE'),
('4', '4.PRESIÓN ARTERIAL ALTA'),
('5', '5.DIFICULTAD PARA RESPIRAR'),
('6', '6.DEHISCENCIA'),
('7', '7.CEFALEA INTENSA'),
('8', '8.ICTERICIA'),
('9', '9.CONVULSIONES'),
('10', '10.EDEMA DE MIEMBROS INFERIORES (SIGNO DE HOMANS)'),
('11', '11.PÉRDIDA DEL ESTADO DE ALERTA'),
('12', '12.EPIGASTRALGIA'),
('13', '13.ACÚFENOS'),
('14', '14.FOSFENOS'),
('15', '15.TINITUS'),
('16', '16.DOLOR TORÁCICO'),
('17', '17.HEMOPTISIS'),
('18', '18.CIANOSIS'),
('19', '19.DIAFORÉSIS'),
('20', '20.CAMBIOS DE ÁNIMO'),
('88', '88.OTROS');
$querySSAPL= "SELECT id_sig_sin_alarma_puer_lact, ssapl_concepto FROM sig_sin_alarma_puer_lact ORDER BY id_sig_sin_alarma_puer_lact";
$resultadoSSAPL = $mysqli->query($querySSAPL);


medicamentos_puer_lact
USE CNATAL; 
CREATE TABLE medicamentos_puer_lact (id_medicamentos_puer_lact INT NOT NULL,  medpl_concepto VARCHAR(75) NOT NULL );
ALTER TABLE medicamentos_puer_lact ADD PRIMARY KEY(id_medicamentos_puer_lact);
INSERT INTO medicamentos_puer_lact (id_medicamentos_puer_lact, medpl_concepto) VALUES
('1', '1.VITAMINAS Y ÁCIDO FÓLICO'),
('2', '2.ANTIANÉMICO'),
('3', '3.ANALGÉSICOS, ANTIPIRÉTICOS Y ANTIINFLAMATORIOS NO ESTEROIDEOS'),
('4', '4.ANTIULCEROSOS'),
('5', '5.ANTIBIÓTICOS'),
('6', '6.ANTICONVULSIVANTE'),
('7', '7.ANTIESPASMÓDICO'),
('8', '8.ANTIHIPERTENSIVO'),
('9', '9.ANTIMICÓTICO'),
('10', '10.ANTIVIRAL'),
('11', '11.HIPOGLUCEMIANTE (ORALES E INYECTABLE)'),
('12', '12.INHIBIDOR DE LA LACTANCIA (CASOS EXCLUSIVOS, PREVIA VALORACIÓN)');
$queryMEDPL= "SELECT id_medicamentos_puer_lact, medpl_concepto FROM medicamentos_puer_lact ORDER BY id_medicamentos_puer_lact";
$resultadoMEDPL = $mysqli->query($queryMEDPL);


USE CNATAL; 
CREATE TABLE anticonceptivoesp (id_anticonceptivoesp INT NOT NULL,  antesp_concepto VARCHAR(60) NOT NULL );
ALTER TABLE anticonceptivoesp ADD PRIMARY KEY(id_anticonceptivoesp);
INSERT INTO anticonceptivoesp (id_anticonceptivoesp, antesp_concepto) VALUES
('1', '1.ORAL'),   
('2', '2.INYECTABLE MENSUAL'),
('3', '3.INYECTABLE BIMESTRAL'),   
('4', '4.INYECTABLE TRIMESTRAL'),
('5', '5.IMPLANTE SUBDÉRMICO'),   
('6', '6.PARCHE DÉRMICO'),
('7', '7.DIU'),   
('8', '8.DIU MEDICADO'),
('9', '9.OTB'),
('10', '10.OTRO MÉTODO'); 

$queryANTESP= "SELECT id_anticonceptivoesp, antesp_concepto FROM anticonceptivoesp ORDER BY id_anticonceptivoesp";
$resultadoANTESP = $mysqli->query($queryANTESP);

USE CNATAL; 
CREATE TABLE atendidoen (id_atendidoen INT NOT NULL,  atnen_concepto VARCHAR(60) NOT NULL );
ALTER TABLE atendidoen ADD PRIMARY KEY(id_atendidoen);
INSERT INTO atendidoen (id_atendidoen, atnen_concepto) VALUES
('2', '2.IMSS'),   
('3', '3.ISSSTE'),
('4', '4.PEMEX'),   
('5', '5.SEDENA'),
('6', ' 6.SEMAR'),   
('10', '10.IMSS BIENESTAR'),
('11', '11.ISSFAM'),   
('14', '14.OPD IMSS BIENESTAR'),
('15', '15.SECRETARIA DE SALUD'),
('16', '16.UNIDAD MÉDICA PRIVADA'), 
('17', '17.VÍA PÚBLICA'), 
('18', '18.TRASLADO'), 
('19', '19.DOMICILIO'), 
('99', '99.SE IGNORA'), 
('88', '88.OTRO'); 
$queryATNEN= "SELECT id_atendidoen, atnen_concepto FROM atendidoen ORDER BY id_atendidoen";
$resultadoATNEN = $mysqli->query($queryATNEN);

USE CNATAL; 
CREATE TABLE atendidopor (id_atendidopor INT NOT NULL,  atnpor_concepto VARCHAR(60) NOT NULL );
ALTER TABLE atendidopor ADD PRIMARY KEY(id_atendidopor);
INSERT INTO atendidopor (id_atendidopor, atnpor_concepto) VALUES
('1', '1.MÉDICA (O) PASANTE'),   
('2', '2.MÉDICA (O) GENERAL'),
('3', '3.MÉDICA (O) RESIDENTE DE GINECOLOGÍA Y OBSTETRICIA'),
('4', '4.MÉDICA (O) ESPECIALISTA DE GINECOLOGÍA Y OBSTETRICIA'),
('5', '5.PARTERA (O) PROFESIONAL'),
('6', '6.PARTERA (O)TRADICIONAL'),
('7', '7.OTRAS (OS)'); 
$queryATNPOR= "SELECT id_atendidopor, atnpor_concepto FROM atendidopor ORDER BY id_atendidopor";
$resultadoATNPOR = $mysqli->query($queryATNPOR);

USE CNATAL; 
CREATE TABLE complicacioneslist (id_complicacioneslist INT NOT NULL,  compli_concepto VARCHAR(60) NOT NULL );
ALTER TABLE complicacioneslist ADD PRIMARY KEY(id_complicacioneslist);
INSERT INTO complicacioneslist (id_complicacioneslist, compli_concepto) VALUES
('1','1.NINGUNA'),
('2', '2.ENFERMEDAD HIPERTENSIVA DEL EMBARAZO'),
('3', '3.HEMORRAGIA'),
('4', '4.SEPSIS'),
('5', '5.ENFERMEDAD RESPIRATORIA'),
('6', '6.FALLA ORGÁNICA'),
('7', '7.FALLA METABÓLICA'),   
('8', '8.FALLA NEUROLÓGICA'),
('9', '9.OTRA');
$queryCOMPLIST= "SELECT id_complicacioneslist, compli_concepto FROM complicacioneslist ORDER BY id_complicacioneslist";
$resultadoCOMPLIST = $mysqli->query($queryCOMPLIST);

