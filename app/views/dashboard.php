<?php
include_once "head.html";
include_once "header.php";
?>
<main>
	<h1>Bienvenue, <?php echo htmlspecialchars($_SESSION['user']); ?> !</h1>

	<div class="chart-wrapper">
		<canvas id="sensorChart"></canvas>
	</div>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>

<script>
	const chartData = <?php echo json_encode($chartData, JSON_UNESCAPED_UNICODE); ?>;

	document.addEventListener('DOMContentLoaded', function() {
		const ctx = document.getElementById('sensorChart').getContext('2d');
		new Chart(ctx, {
			type: 'line',
			data: chartData,
			options: {
				responsive: true,
				maintainAspectRatio: false,
				scales: {
					x: {
						type: 'time',
						time: {
							unit: 'minute',
							displayFormats: { minute: 'HH:mm' },
							tooltipFormat: 'dd/MM/yyyy HH:mm'
						},
						title: { display: true, text: 'Heure' }
					},
					y: {
						beginAtZero: true,
						title: { display: true, text: 'Valeur' }
					}
				},
				plugins: {
					tooltip: { mode: 'index', intersect: false },
					legend: { position: 'top' }
				}
			}
		});
	});
</script>
