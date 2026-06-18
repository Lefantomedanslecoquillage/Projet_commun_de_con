<?php
include_once __DIR__ . "/head.html";
include_once __DIR__ . "/header.php";
?>

<link rel="stylesheet" href="styles/dashboardAir.css">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>

<script>
	const co2Data = <?= json_encode($chartData["co2"], JSON_UNESCAPED_UNICODE) . "\n"; ?>
	const ch4Data = <?= json_encode($chartData["ch4"], JSON_UNESCAPED_UNICODE) . "\n"; ?>
	const vocData = <?= json_encode($chartData["voc"], JSON_UNESCAPED_UNICODE) . "\n"; ?>
	const range = <?= $range . "\n"; ?>
</script>
<script defer src="scripts/dashboardAir.js"></script>

<main>
	<div class="charts-container">
		<div class="chart-wrapper">
			<canvas id="co2Chart"></canvas>
		</div>
		<div class="chart-wrapper">
			<canvas id="ch4Chart"></canvas>
		</div>
		<div class="chart-wrapper">
			<canvas id="vocChart"></canvas>
		</div>
	</div>
	<div class="range-buttons">
		<a href="?section=air&range=5" class="<?php echo $range === 5 ? "active" : ""; ?>">5 min</a>
		<a href="?section=air&range=15" class="<?php echo $range === 15 ? "active" : ""; ?>">15 min</a>
		<a href="?section=air&range=30" class="<?php echo $range === 30 ? "active" : ""; ?>">30 min</a>
		<a href="?section=air&range=60" class="<?php echo $range === 60 ? "active" : ""; ?>">60 min</a>
	</div>
</main>
