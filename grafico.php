<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grafico</title>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.8.0/chart.min.js"></script>

</head>
<body>
    <div style="width: 600px">
	<canvas id="grafica"></canvas>
    </div>
</body>
<script>
        const labels = ['Enero', 'Febrero', 'Marzo', 'Abril']

        const graph = document.querySelector("#grafica");

        const data = {
            labels: labels,
            datasets: [{
                label:"Ejemplo 1",
                data: [1, 2, 3, 4],
                backgroundColor: 'rgba(125, 129, 176, 0.2)'
            }]
        };

        const config = {
            type: 'bar',
            data: data,
            };
        new Chart(graph, config);
</script>
</html>

