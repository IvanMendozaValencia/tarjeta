<?php

session_start();

require 'funcs/conexion.php';
require 'funcs/funcs.php';


if(!isset($_SESSION["id_usuario"]))
{
    header("Location: index.php");
}


$idUsusario = $_SESSION['id_usuario'];
$sql = "SELECT usuarios.id, usuario, nombre, correo, clues_id, last_session, id_tipo, a_usuarios, a_tarjeta, a_configuracion, a_consultas, a_estadisticas, a_agregar, a_modificar, a_eliminar, CLUES,  NOMBRE_DE_LA_INSTITUCION, ENTIDAD,  MUNICIPIO,  LOCALIDAD, CLAVE_DE_LA_JURISDICCION, JURISDICCION, NOMBRE_DE_TIPOLOGIA FROM usuarios inner join clues on clues.clues = usuarios.clues_id WHERE id  ='$idUsusario'";
$result = $mysqli->query($sql);
$row_usuario = $result->fetch_assoc();
$id_tipo_usuario=$row_usuario['id_tipo'];

$sqlTarjeta = "SELECT id_tarjeta, curp, nombre, primer_apellido, segundo_apellido, edad,  fecha_nacimiento FROM datos_identificacion order by curp ASC";
$listaTarjeta = $mysqli->query($sqlTarjeta);

$sqlTarjeta0 = "SELECT nombre, edad FROM datos_identificacion order by nombre ASC";
$listaTarjeta0 = $mysqli->query($sqlTarjeta0);

$sqljurisdiccion="SELECT JURISDICCION, count(JURISDICCION) as CANTIDAD FROM clues group by JURISDICCION order by cantidad desc";
$listajurisdiccion=$mysqli->query($sqljurisdiccion);

$sqljurisdiccion="SELECT JURISDICCION, count(JURISDICCION) as CANTIDAD FROM clues group by JURISDICCION order by cantidad desc";
$listajurisdiccion1=$mysqli->query($sqljurisdiccion);

$sqlFechas = "SELECT  fecha_nacimiento, count(fecha_nacimiento)as Cantidad FROM datos_identificacion group by fecha_nacimiento";
$listaFechas = $mysqli->query($sqlFechas);




?>

<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="icono/ico.ico">

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

		<!-- para generar el grafico cloudflare-->
		<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.8.0/chart.min.js"></script>

		<!-- para generar el grafico amcharts-->
		<script src="https://cdn.amcharts.com/lib/5/index.js"></script>
		<script src="https://cdn.amcharts.com/lib/5/xy.js"></script>
		<script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>
		<script src="https://cdn.amcharts.com/lib/5/locales/de_DE.js"></script>
		<script src="https://cdn.amcharts.com/lib/5/geodata/germanyLow.js"></script>
		<script src="https://cdn.amcharts.com/lib/5/fonts/notosans-sc.js"></script>

		<!-- para el grafico de pastel-->
		<script src="https://cdn.amcharts.com/lib/5/percent.js"></script>
		
		<!-- para el grafico de chart js-->
		<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


    <title>Grafico</title>
    
    <script>
			$(document).ready(function() {
			$('#tabla').DataTable();
			} );
			
		</script>
		
		<style>
			body {
			background: white;
			}
		</style>

		<!-- para generar el grafico amcharts-->
		<style>
		#chartdiv {
		width: 100%;
		height: 300px;
		}
		</style>

		<style>
		#chartdiv1 {
		width: 100%;
		height: 300px;
		}
		</style>

		<style>
		#chartdiv2 {
		width: 100%;
		height: 300px;
		}
		</style>

		<style>
		#grafico_pastel {
		width: 100%;
		height: 300px;
		}
		</style>

		<style>
		#grafico_fechas {
		width: 100%;
		height: 250px;
		}
		</style>
		<style>
			body {
			font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
			}
		</style>

		<style>
		#myChart1 {
		position: relative; height: 60vh; width: 120vw;
		}
		</style>

		
</head>

<body class="d-flex flex-column h-100">
    <div class="container py-2">
        <h3 class="text-center">Grafico-Tarjeta de Atención Integral del Embarazo, Puerperio y Período de Lactancia</h3>   
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
                    <a href="../inicio.php" class="btn btn-dark" ></i> Menú principal</a>    
                </div>
            </div>  
			<br>
			<div class="col-md-2 m-l">
				<form action="edades.php" action="GET">
				<h5 class="text-center">Top 10</h5>&nbsp;&nbsp; 
					<select name="id" id="id" class="form-select">
							<option value="">Seleccionar...</option>
							<?php 
							for($i=1;$i<=10;$i++) { echo "<option value='".$i."'>".$i."</option>"; } 
							?>
					</select>
					<br>
					<input type="submit" name="btconsultar" class="btn btn-primary" value="Consultar">                       
				</form>
			</div>
			<br>
			
			<div style="width: 600px" >
				<h3>Chart js nombre  - edad</h3>
				<canvas id="myChart" ></canvas>
			</div>

			<div style="width: 600px">
			<canvas id="grafica"></canvas>		
			</div>
			
			<div style="width: 600px" id="chartdiv"></div>
			<div id="chartdiv1"></div>
			<h3>Jurisdicciones cantidad</h3>
			<br>
			<div id="chartdiv2"></div>
			<h3>Jurisdicciones porcentaje</h3>
			<br>
			<div id="grafico_pastel"></div>
			<h3>Grafico de fechas</h3>
			<br>
			<div id="grafico_fechas"></div>

                     
<script src="assets/js/bootstrap.bundle.min.js"></script>
<script>
        var ctx = document.getElementById('myChart')
        var myChart = new Chart(ctx, {
            type:'bar',//bar line pie doughnut 
            data:{
                datasets: [{
                    label: 'Personas edad',
                    backgroundColor: ['rgba(0, 255, 251,  0.7)','rgba(238, 0, 255, 0.7)','rgba(92, 0, 9, 0.7)', '#rgba(0, 81, 92, 0.7)', 'rgba(0, 3, 92, 0.7)', 'rgba(190, 28, 02, 0.7)', 'rgba(255, 17, 0, 0.7)', '#90CAF9, 0.5', '#64B5F6', '#42A5F5', '#2196F3', '#0D47A1'],
                    borderColor: ['red'],
                    borderWidth:1
                }]
            },
            options:{
                scales:{
                    y:{
                        beginAtZero:true
                    }
                }
            }
        })

        let url = '../edades.php'
        fetch(url)
            .then( response => response.json() )
            .then( datos => mostrar(datos) )
            .catch( error => console.log(error) )

        const mostrar = (datos) =>{
            datos.forEach(element => {
                myChart.data['labels'].push(element.nombre)
                myChart.data['datasets'][0].data.push(element.edad)
                myChart.update()
            });
            console.log(myChart.data)
        }    

    </script>
<script>
		

		const graph = document.querySelector("#grafica");
								
		
		
		const labels = ['enero','febrero']
		const data = {
			labels: labels,
			datasets: [{
				label:"Ejemplo 1",
				data: [5,9],
				backgroundColor: 'rgba(86, 61, 124, 0.7)'
			}]
		
	};
	

		
		const config = {
			type: 'bar',
			data: data,
			};
		new Chart(graph, config);
</script>
<!--Grafico brarras con datos-->
<script>
		am5.ready(function() {

		// Create root element
		// https://www.amcharts.com/docs/v5/getting-started/#Root_element
		var root = am5.Root.new("chartdiv");

		// Set themes
		// https://www.amcharts.com/docs/v5/concepts/themes/
		root.setThemes([
		am5themes_Animated.new(root)
		]);

		// Create chart
		// https://www.amcharts.com/docs/v5/charts/xy-chart/
		var chart = root.container.children.push(am5xy.XYChart.new(root, {
		panX: true,
		panY: true,
		wheelX: "panX",
		wheelY: "zoomX",
		pinchZoomX: true,
		paddingLeft:0,
		paddingRight:1
		}));

		// Add cursor
		// https://www.amcharts.com/docs/v5/charts/xy-chart/cursor/
		var cursor = chart.set("cursor", am5xy.XYCursor.new(root, {}));
		cursor.lineY.set("visible", false);


		// Create axes
		// https://www.amcharts.com/docs/v5/charts/xy-chart/axes/
		var xRenderer = am5xy.AxisRendererX.new(root, { 
		minGridDistance: 30, 
		minorGridEnabled: true
		});

		xRenderer.labels.template.setAll({
		rotation: -90,
		centerY: am5.p50,
		centerX: am5.p100,
		paddingRight: 15
		});

		xRenderer.grid.template.setAll({
		location: 1
		})

		var xAxis = chart.xAxes.push(am5xy.CategoryAxis.new(root, {
		maxDeviation: 0.3,
		categoryField: "Nombre",
		renderer: xRenderer,
		tooltip: am5.Tooltip.new(root, {})
		}));

		var yRenderer = am5xy.AxisRendererY.new(root, {
		strokeOpacity: 0.1
		})

		var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
		maxDeviation: 0.3,
		renderer: yRenderer
		}));

		// Create series
		// https://www.amcharts.com/docs/v5/charts/xy-chart/series/
		var series = chart.series.push(am5xy.ColumnSeries.new(root, {
		name: "Nombre y edad",
		xAxis: xAxis,
		yAxis: yAxis,
		valueYField: "Edad",
		sequencedInterpolation: true,
		categoryXField: "Nombre",
		tooltip: am5.Tooltip.new(root, {
			labelText: "{valueY}"
		})
		}));

		series.columns.template.setAll({ cornerRadiusTL: 5, cornerRadiusTR: 5, strokeOpacity: 0 });
		series.columns.template.adapters.add("fill", function (fill, target) {
		return chart.get("colors").getIndex(series.columns.indexOf(target));
		});

		series.columns.template.adapters.add("stroke", function (stroke, target) {
		return chart.get("colors").getIndex(series.columns.indexOf(target));
		});

		// Set data
		var data = [
			<?php while($row = $listaTarjeta->fetch_assoc()) { ?>{ 
					Nombre:  "<?php echo $row['nombre'] ?>",
					Edad:    <?php echo $row['edad'] ?>
				},
		<?php } ?>
		];

		xAxis.data.setAll(data);
		series.data.setAll(data);


		// Make stuff animate on load
		// https://www.amcharts.com/docs/v5/concepts/animations/
		series.appear(1000);
		chart.appear(1000, 100);

		}); // end am5.ready()
</script>
<!--Grafico inicial-->
<script>
		am5.ready(function() {

		// Create root element
		// https://www.amcharts.com/docs/v5/getting-started/#Root_element
		var root = am5.Root.new("chartdiv1");

		// Set themes
		// https://www.amcharts.com/docs/v5/concepts/themes/
		root.setThemes([
		am5themes_Animated.new(root)
		]);

		// Create chart
		// https://www.amcharts.com/docs/v5/charts/xy-chart/
		var chart = root.container.children.push(am5xy.XYChart.new(root, {
		panX: true,
		panY: true,
		wheelX: "panX",
		wheelY: "zoomX",
		pinchZoomX: true,
		paddingLeft:0,
		paddingRight:1
		}));

		// Add cursor
		// https://www.amcharts.com/docs/v5/charts/xy-chart/cursor/
		var cursor = chart.set("cursor", am5xy.XYCursor.new(root, {}));
		cursor.lineY.set("visible", false);


		// Create axes
		// https://www.amcharts.com/docs/v5/charts/xy-chart/axes/
		var xRenderer = am5xy.AxisRendererX.new(root, { 
		minGridDistance: 30, 
		minorGridEnabled: true
		});

		xRenderer.labels.template.setAll({
		rotation: -90,
		centerY: am5.p50,
		centerX: am5.p100,
		paddingRight: 15
		});

		xRenderer.grid.template.setAll({
		location: 1
		})

		var xAxis = chart.xAxes.push(am5xy.CategoryAxis.new(root, {
		maxDeviation: 0.3,
		categoryField: "country",
		renderer: xRenderer,
		tooltip: am5.Tooltip.new(root, {})
		}));

		var yRenderer = am5xy.AxisRendererY.new(root, {
		strokeOpacity: 0.1
		})

		var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
		maxDeviation: 0.3,
		renderer: yRenderer
		}));

		// Create series
		// https://www.amcharts.com/docs/v5/charts/xy-chart/series/
		var series = chart.series.push(am5xy.ColumnSeries.new(root, {
		name: "Series 1",
		xAxis: xAxis,
		yAxis: yAxis,
		valueYField: "value",
		sequencedInterpolation: true,
		categoryXField: "country",
		tooltip: am5.Tooltip.new(root, {
			labelText: "{valueY}"
		})
		}));

		series.columns.template.setAll({ cornerRadiusTL: 5, cornerRadiusTR: 5, strokeOpacity: 0 });
		series.columns.template.adapters.add("fill", function (fill, target) {
		return chart.get("colors").getIndex(series.columns.indexOf(target));
		});

		series.columns.template.adapters.add("stroke", function (stroke, target) {
		return chart.get("colors").getIndex(series.columns.indexOf(target));
		});


		// Set data
		var data = [{
		country: "USA",
		value: 2025
		}, {
		country: "China",
		value: 1882
		}, {
		country: "Japan",
		value: 1809
		}, {
		country: "Germany",
		value: 1322
		}, {
		country: "UK",
		value: 1122
		}, {
		country: "France",
		value: 1114
		}, {
		country: "India",
		value: 984
		}, {
		country: "Spain",
		value: 711
		}, {
		country: "Netherlands",
		value: 665
		}, {
		country: "South Korea",
		value: 443
		}, {
		country: "Canada",
		value: 441
		}];

		xAxis.data.setAll(data);
		series.data.setAll(data);


		// Make stuff animate on load
		// https://www.amcharts.com/docs/v5/concepts/animations/
		series.appear(1000);
		chart.appear(1000, 100);

		}); // end am5.ready()
</script>
<!--Grafico braras-->
<script>
		am5.ready(function() {

		// Create root element
		// https://www.amcharts.com/docs/v5/getting-started/#Root_element
		var root = am5.Root.new("chartdiv2");

		// Set themes
		// https://www.amcharts.com/docs/v5/concepts/themes/
		root.setThemes([
		am5themes_Animated.new(root)
		]);

		// Create chart
		// https://www.amcharts.com/docs/v5/charts/xy-chart/
		var chart = root.container.children.push(am5xy.XYChart.new(root, {
		panX: false,
		panY: false,
		wheelX: "panX",
		wheelY: "zoomX",
		pinchZoomX: false,
		paddingLeft:0,
		paddingRight:1
		}));

		// Add cursor
		// https://www.amcharts.com/docs/v5/charts/xy-chart/cursor/
		var cursor = chart.set("cursor", am5xy.XYCursor.new(root, {}));
		cursor.lineY.set("visible", false);


		// Create axes
		// https://www.amcharts.com/docs/v5/charts/xy-chart/axes/
		var xRenderer = am5xy.AxisRendererX.new(root, { 
		minGridDistance: 30, 
		minorGridEnabled: true
		});

		xRenderer.labels.template.setAll({
		rotation: -90,
		centerY: am5.p50,
		centerX: am5.p100,
		paddingRight: 15
		});

		xRenderer.grid.template.setAll({
		location: 1
		})

		var xAxis = chart.xAxes.push(am5xy.CategoryAxis.new(root, {
		maxDeviation: 0.3,
		categoryField: "Jurisdiccion",
		renderer: xRenderer,
		tooltip: am5.Tooltip.new(root, {})
		}));

		var yRenderer = am5xy.AxisRendererY.new(root, {
		strokeOpacity: 0.1
		})

		var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
		maxDeviation: 0.3,
		renderer: yRenderer
		}));

		// Create series
		// https://www.amcharts.com/docs/v5/charts/xy-chart/series/
		var series = chart.series.push(am5xy.ColumnSeries.new(root, {
		name: "Series 1",
		xAxis: xAxis,
		yAxis: yAxis,
		valueYField: "Cantidad",
		sequencedInterpolation: true,
		categoryXField: "Jurisdiccion",
		tooltip: am5.Tooltip.new(root, {
			labelText: "{valueY}"
		})
		}));

		series.columns.template.setAll({ cornerRadiusTL: 5, cornerRadiusTR: 5, strokeOpacity: 0 });
		series.columns.template.adapters.add("fill", function (fill, target) {
		return chart.get("colors").getIndex(series.columns.indexOf(target));
		});

		series.columns.template.adapters.add("stroke", function (stroke, target) {
		return chart.get("colors").getIndex(series.columns.indexOf(target));
		});

		// Set data
		var data = [
			<?php while($row = $listajurisdiccion->fetch_assoc()) { ?>{ 
					Jurisdiccion:  "<?php echo $row['JURISDICCION'] ?>",
					Cantidad:       <?php echo $row['CANTIDAD'] ?>
				},
		<?php } ?>
		];
		
		xAxis.data.setAll(data);
		series.data.setAll(data);


		// Make stuff animate on load
		// https://www.amcharts.com/docs/v5/concepts/animations/
		series.appear(1000);
		chart.appear(1000, 100);

		}); // end am5.ready()
</script>
<!--Grafico pastel-->
<script>
		am5.ready(function() {

		// Create root element
		// https://www.amcharts.com/docs/v5/getting-started/#Root_element
		var root = am5.Root.new("grafico_pastel");

		// Set themes
		// https://www.amcharts.com/docs/v5/concepts/themes/
		root.setThemes([
		am5themes_Animated.new(root)
		]);

		// Create chart
		// https://www.amcharts.com/docs/v5/charts/percent-charts/pie-chart/
		var chart = root.container.children.push(
		am5percent.PieChart.new(root, {
			endAngle: 270
		})
		);

		// Create series
		// https://www.amcharts.com/docs/v5/charts/percent-charts/pie-chart/#Series
		var series = chart.series.push(
		am5percent.PieSeries.new(root, {
			valueField: "Cantidad",
			categoryField: "Jurisdiccion",
			endAngle: 270
		})
		);

		series.states.create("hidden", {
		endAngle: -90
		});

		// Set data
		// https://www.amcharts.com/docs/v5/charts/percent-charts/pie-chart/#Setting_data
		series.data.setAll([
			<?php while($row = $listajurisdiccion1->fetch_assoc()) { ?>{ 
						Jurisdiccion:  "<?php echo $row['JURISDICCION'] ?>",
						Cantidad:       <?php echo $row['CANTIDAD'] ?>
					},
			<?php } ?>
		]);

		series.appear(1000, 100);

		}); // end am5.ready()
</script>
<!--Grafico fechas-->
<script>
var root = am5.Root.new("grafico_fechas"); 

root.setThemes([
am5themes_Animated.new(root)
]);

var chart = root.container.children.push( 
am5xy.XYChart.new(root, {
	panY: false,
	wheelY: "zoomX",
	layout: root.verticalLayout,
	maxTooltipDistance: 0
}) 
);

// Define data
var datas = [			
	<?php while($row = $listaFechas->fetch_assoc()) { ?>{ 
		fecha_nacimiento:  new Date("<?php echo $row['fecha_nacimiento'] ?>").getTime(),
				Cantidad:       <?php echo $row['Cantidad'] ?>
			},
	<?php } ?>
];

// Create Y-axis
var yAxis = chart.yAxes.push(
am5xy.ValueAxis.new(root, {
	extraTooltipPrecision: 1,
	renderer: am5xy.AxisRendererY.new(root, {})
})
);

// Create X-Axis
let xAxis = chart.xAxes.push(
am5xy.DateAxis.new(root, {
	baseInterval: { timeUnit: "day", count: 1 },
	startLocation: 0.5,
	endLocation: 0.5,
	renderer: am5xy.AxisRendererX.new(root, {
	minGridDistance: 30
	})
})
);

xAxis.get("dateFormats")["day"] = "MM/dd";
xAxis.get("periodChangeDateFormats")["day"] = "MM/dd";

// Create series
function createSeries(name, field) {
var series = chart.series.push( 
	am5xy.LineSeries.new(root, { 
	name: name,
	xAxis: xAxis, 
	yAxis: yAxis, 
	valueYField: field, 
	valueXField: "fecha_nacimiento",
	tooltip: am5.Tooltip.new(root, {}),
	maskBullets: false
	}) 
);

series.bullets.push(function() {
	return am5.Bullet.new(root, {
	sprite: am5.Circle.new(root, {
		radius: 5,
		fill: series.get("fill")
	})
	});
});

series.strokes.template.set("strokeWidth", 2);

series.get("tooltip").label.set("text", "[bold]{name}[/]\n{valueX.formatDate()}: {valueY}")
series.data.setAll(datas);
}

createSeries("Series", "Cantidad");

// Add cursor
chart.set("cursor", am5xy.XYCursor.new(root, {
behavior: "zoomXY",
xAxis: xAxis
}));

xAxis.set("tooltip", am5.Tooltip.new(root, {
themeTags: ["axis"]
}));

yAxis.set("tooltip", am5.Tooltip.new(root, {
themeTags: ["axis"]
}));	
</script>
<script>

</script>
</body>
</html>