
setTimeout(function () {

// chart 1
var ctx1 = document.getElementById('chart1').getContext('2d');

// Función para cargar datos de la API
fetch('rutag.php')
    .then(response => response.json())
    .then(data => {
        // Procesar los datos de la API
        const labels = [];
        const enProceso = [];
        const finalizados = [];

        data.forEach(row => {
            labels.push(getMonthName(row.mes)); // Convierte el número del mes en nombre
            enProceso.push(row.en_proceso);
            finalizados.push(row.finalizados);
        });

        // Crear la gráfica
        new Chart(ctx1, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'En ruta',
                        data: enProceso,
                        backgroundColor: '#14abef',
                        borderColor: "transparent",
                        pointRadius: "0",
                        borderWidth: 3
                    },
                    {
                        label: 'Finalizados',
                        data: finalizados,
                        backgroundColor: "rgba(20, 171, 239, .35)",
                        borderColor: "transparent",
                        pointRadius: "0",
                        borderWidth: 1
                    }
                ]
            },
            options: {
                maintainAspectRatio: false,
                legend: {
                    display: false,
                    labels: {
                        fontColor: '#585757',
                        boxWidth: 40
                    }
                },
                tooltips: {
                    displayColors: false
                },
                scales: {
                    xAxes: [{
                        ticks: {
                            beginAtZero: true,
                            fontColor: '#585757'
                        },
                        gridLines: {
                            display: true,
                            color: "rgba(0, 0, 0, .05)"
                        }
                    }],
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            fontColor: '#585757'
                        },
                        gridLines: {
                            display: true,
                            color: "rgba(0, 0, 0, .05)"
                        }
                    }]
                }
            }
        });
    })
    .catch(error => console.error('Error al cargar datos:', error));

// Función para convertir número de mes a nombre
function getMonthName(month) {
    const months = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
    return months[month - 1];
}



	// chart 2
	var ctx2 = document.getElementById("chart2").getContext('2d');
	var myChart2 = new Chart(ctx2, {
		type: 'doughnut',
		data: {
			labels: ["Direct", "Affiliate", "E-mail", "Other"],
			datasets: [{
				backgroundColor: [
					"#14abef",
					"#02ba5a",
					"#d13adf",
					"#fba540"
				],
				data: [5856, 2602, 1802, 1105],
				borderWidth: [0, 0, 0, 0]
			}]
		},
		options: {
			maintainAspectRatio: false,
			legend: {
				position :"bottom",
				display: false,
				labels: {
					fontColor: '#ddd',
					boxWidth:15
				}
			}
			,
			tooltips: {
				displayColors:false
			}
		}
	});

}, 1000)