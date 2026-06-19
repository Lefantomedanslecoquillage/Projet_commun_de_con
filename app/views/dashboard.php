<?php
include_once __DIR__ . "/head.html";
include_once __DIR__ . "/header.php";

require_once __DIR__ . '/../models/Database.php'; // adapte si besoin
$pdo = Database::getConnection();

// Seuils pour les alertes
$seuil_co2 = 800;   // ppm
$seuil_ch4 = 300;    // à adapter
$seuil_voc = 100;   // à adapter

$stmt = $pdo->query("SELECT CO2, CH4, VOC FROM ambient_air ORDER BY timestamp DESC LIMIT 1");
$air = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
$air = is_array($air) ? $air : ["CO2" => null, "CH4" => null, "VOC" => null];

$alertes = [];
if ($air['CO2'] !== null && $air['CO2'] > $seuil_co2){
    $alertes[] = "Le niveau de CO2 est trop élevé.";
}

if ($air['CH4'] !== null && $air['CH4'] > $seuil_ch4){
    $alertes[] = "Le niveau de CH4 est trop élevé.";
}

if ($air['VOC'] !== null && $air['VOC'] > $seuil_voc){
    $alertes[] = "Le niveau de VOC est trop élevé.";
}

if (!empty($alertes)){
    $cause = strtolower(str_replace(" ", "_", $alertes[0]));
    $stmt = $pdo->prepare("UPDATE etats_actionneurs SET etat = 1, declenche_par = ?, derniere_action = NOW() WHERE composant = 'led_rouge'");
    $stmt->execute([$cause]);
} else {
    $stmt = $pdo->prepare("UPDATE etats_actionneurs SET etat = 0, declenche_par = ?, derniere_action = NOW() WHERE composant = 'led_rouge'");
    $stmt->execute();
}
?>


<link rel="stylesheet" href="styles/dashboard.css">
<script>
    let soundData = <?= json_encode($soundData) ?>;
    let lightData = <?= json_encode($lightData) ?>;
    let tsData = <?= json_encode($tsValues, JSON_UNESCAPED_UNICODE) . "\n"; ?>
    let co2Data = <?= json_encode($co2Values, JSON_UNESCAPED_UNICODE) . "\n"; ?>
    let ch4Data = <?= json_encode($ch4Values, JSON_UNESCAPED_UNICODE) . "\n"; ?>
    let vocData = <?= json_encode($vocValues, JSON_UNESCAPED_UNICODE) . "\n"; ?>
	let weatherData = <?= json_encode($weatherData, JSON_UNESCAPED_UNICODE) . "\n"; ?>
</script>
<script defer src="scripts/dashboard.js"></script>

<main>
	<div style="display: flex; flex-direction: column; gap: 1rem;">
		<div class="ctn alerts-container">
			<h1>Période</h1>
			<?php if($lightStatus === "NIGHT"): ?>
			<div style="display: flex; align-items: center; gap: 1rem;">
				<img class="icon" src="images/night.svg">
				<p>Nuit</p>
			</div>
			<?php else: ?>
				<div style="display: flex; align-items: center; gap: 1rem;">
					<img class="icon" src="images/day.svg">
					<p>Jour</p>
				</div>
			<?php endif; ?>
		</div>
		<div class="ctn alerts-container">
			<h1>Sommeil</h1>
			<p style="text-transform: capitalize"><?= $soundStatus; ?></p>
		</div>
		<div class="ctn alerts-container">
			<h1>Alertes</h1>
			<?php if (empty($alertes)): ?>
				<p class="ok">✅ Aucun problème</p>
			<?php else: ?>
				<ul class="alert-list">
					<?php foreach ($alertes as $alerte): ?>
						<li class="alert-item">⚠️ <?= htmlspecialchars($alerte, ENT_QUOTES, 'UTF-8') ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
	</div>
	<?php include_once __DIR__ . "/dashboard.html"; ?>
</main>