<?php
include_once "head.html";
include_once "header.php";
?>

<link rel="stylesheet" href="styles/dashboard.css">

<script>
	let tsData = <?= json_encode($tsValues, JSON_UNESCAPED_UNICODE) . "\n"; ?>
	let co2Data = <?= json_encode($co2Values, JSON_UNESCAPED_UNICODE) . "\n"; ?>
	let ch4Data = <?= json_encode($ch4Values, JSON_UNESCAPED_UNICODE) . "\n"; ?>
	let vocData = <?= json_encode($vocValues, JSON_UNESCAPED_UNICODE) . "\n"; ?>
</script>
<script defer src="scripts/dashboard.js"></script>

<main>
	<div class="ctn alerts-container">
		<h1>Alertes</h1>
		<p class="pending">À compléter</p>
	</div>

	<div class="sensors-container">
		<a class="btn ctn" href="index.php?section=air">
			<h1>Qualité de l'air ambient</h1>
			<div class="summary-wrapper">
				<div id="sectionCO2" class="ctn air-section-container">
					<h1>CO2</h1>
					<p class="value"></p>
					<progress value="0" max="800"></progress>
					<p class="evolution"></p>
					<div class="status-container">
						<p class="status-icon">●</p>
						<p class="status"></p>
					</div>
				</div>
				<div id="sectionCH4" class="ctn air-section-container">
					<h1>CH4</h1>
					<p class="value"></p>
					<progress value="0" max="300"></progress>
					<p class="evolution"></p>
					<div class="status-container">
						<p class="status-icon">●</p>
						<p class="status"></p>
					</div>
				</div>
				<div id="sectionVOC" class="ctn air-section-container">
					<h1>VOC</h1>
					<p class="value"></p>
					<progress value="0" max="100"></progress>
					<p class="evolution"></p>
					<div class="status-container">
						<p class="status-icon">●</p>
						<p class="status"></p>
					</div>
				</div>
			</div>
		</a>
		<div class="summary-wrapper">
			<a class="btn ctn air-section-container" href="index.php?section=light">
				<h1>Luminosité</h1>
				<p class="pending">À compléter</p>
			</a>
			<a class="btn ctn air-section-container" href="index.php?section=sound">
				<h1>Niveau sonore</h1>
				<p class="pending">À compléter</p>
			</a>
		</div>
		<a class="btn ctn" href="index.php?section=environment">
			<h1>Météo</h1>
			<div class="summary-wrapper">
				<div class="ctn air-section-container">
					<h1>Température</h1>
					<p class="pending">À compléter</p>
				</div>
				<div class="ctn air-section-container">
					<h1>Humidité</h1>
					<p class="pending">À compléter</p>
				</div>
			</div>
		</a>
	</div>
</main>

<pre style="margin: 1rem;"><code>Maquette :
╔════════════════════════════════════════════════════════════════════════════════╗
║ [logo] SmartWake                          [mode clair/sombre]  [login/logout]  ║
╟────────────────────────────┬───────────────────────────────────────────────────╢
║ Alertes                    │  ┌─── Gazes ───────────────────────────────────┐  ║
║                            │  │ ┌───────────┐  ┌───────────┐  ┌───────────┐ │  ║
║  CO2 > 1000 ppm  10:42     │  │ │    CO₂    │  │    CH₄    │  │    VOC    │ │  ║
║    Dépassement de 25%      │  │ │  450 ppm  │  │  160 ppb  │  │  12 ppb   │ │  ║
║                            │  │ │  ████░░░  │  │  ███░░░░  │  │  █████░░  │ │  ║
║                            │  │ │  seuil    │  │  seuil 5  │  │  seuil 1  │ │  ║
║  Son > 85 dB     10:38     │  │ │  1000     │  │           │  │           │ │  ║
║    Pointe à 92 dB          │  │ │  ↓ 5%     │  │  ↑ 12%    │  │  → stable │ │  ║
║                            │  │ └───────────┘  └───────────┘  └───────────┘ │  ║
║                            │  └─────────────────────────────────────────────┘  ║
║  Température < 5°C 09:15   │                                                   ║
║    Gel possible (2.1°C)    │  ┌────────────────┐  ┌─────────────────┐          ║
║                            │  │  Luminosité    │  │  Niveau Sonore  │          ║
║                            │  │   320 lux      │  │    62 dB        │          ║
║  Environnement dégradé     │  │  █████░░░ 78%  │  │  ████████░ 82%  │          ║
║    IAQ > 150 (médiocre)    │  │  seuil 800     │  │  seuil  85      │          ║
║                            │  │   ↑ 12%        │  │   ↑ 8%          │          ║
║                            │  └────────────────┘  └─────────────────┘          ║
║                            │                                                   ║
║                            │  ┌─── Météo ───────────────────────────────────┐  ║
║                            │  │ ┌────────────────┐  ┌────────────────┐      │  ║
║                            │  │ │  Température   │  │  Humidité      │      │  ║
║                            │  │ │   23.4 °C      │  │    58 %        │      │  ║
║                            │  │ │  █████░░░ 65%  │  │  ██████░░ 60%  │      │  ║
║                            │  │ │  seuil 40/5    │  │  seuil 80/20   │      │  ║
║                            │  │ │   → stable     │  │   ↑ 8%         │      │  ║
║                            │  │ └────────────────┘  └────────────────┘      │  ║
║                            │  └─────────────────────────────────────────────┘  ║
║                            │                                                   ║
║                            │  Dernière synchronisation : il y a 3 s            ║
╚════════════════════════════╧═══════════════════════════════════════════════════╝
</code></pre>