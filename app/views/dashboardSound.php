<?php
include_once __DIR__ . "/head.html";
include_once __DIR__ . "/header.php";
?>

<link rel="stylesheet" href="styles/dashboardAir.css">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>

<script>
const soundData = <?= json_encode($chartData["sound"]) ?>;
const range = <?= $range ?>;
</script>

<script defer src="scripts/dashboardSound.js"></script>

<main>
    <div class="charts-container">
        <div class="chart-wrapper">
            <canvas id="soundChart"></canvas>
        </div>
    </div>

	<div class="range-buttons">
    <a href="?section=sound&range=5" class="<?= $range === 5 ? 'active' : '' ?>">5 min</a>
    <a href="?section=sound&range=15" class="<?= $range === 15 ? 'active' : '' ?>">15 min</a>
    <a href="?section=sound&range=30" class="<?= $range === 30 ? 'active' : '' ?>">30 min</a>
    <a href="?section=sound&range=60" class="<?= $range === 60 ? 'active' : '' ?>">60 min</a>
</div>
</main>