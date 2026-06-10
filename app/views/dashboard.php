<?php
include_once "head.html";
include_once "header.php";
?>

<link rel="stylesheet" href="styles/dashboard.css">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>

<script>
	const co2Data = <?php echo json_encode($chartData['co2'], JSON_UNESCAPED_UNICODE); ?>;
	const vocData = <?php echo json_encode($chartData['voc'], JSON_UNESCAPED_UNICODE); ?>;
</script>
<script defer src="scripts/dashboard.js"></script>

<main>
	<div class="charts-container">
		<div class="chart-wrapper">
			<canvas id="co2Chart"></canvas>
		</div>
		<div class="chart-wrapper">
			<canvas id="vocChart"></canvas>
		</div>
	</div>
</main>
