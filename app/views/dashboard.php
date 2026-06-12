<?php
include_once "head.html";
include_once "header.php";
?>

<link rel="stylesheet" href="styles/dashboard.css">

<main>
	<div class="ctn alerts-container">
		<h1>Alertes</h1>
		<p class="pending">À compléter</p>
	</div>

	<div class="sensors-container">
		<a class="btn ctn" href="index.php?section=air">
			<h1>Qualité de l'air ambient</h1>
			<div class="summary-wrapper">
				<div class="ctn air-section-container">
					<h1>CO2</h1>
					<p class="pending">À compléter</p>
				</div>
				<div class="ctn air-section-container">
					<h1>CH4</h1>
					<p class="pending">À compléter</p>
				</div>
				<div class="ctn air-section-container">
					<h1>VOC</h1>
					<p class="pending">À compléter</p>
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
║    Dépassement de 25%      │  │ │  450 ppm  │  │  2.3 ppm  │  │  0.8 ppm  │ │  ║
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