<?php
include_once "head.html";
include_once "header.php";

require_once __DIR__ . '/../models/Database.php'; // adapte si besoin
$pdo = Database::getConnection();


// Seuils pour les alertes

$seuil_co2 = 800;   // ppm
$seuil_ch4 = 50;    // à adapter
$seuil_voc = 200;   // à adapter

$stmt = $pdo->query("SELECT CO2, CH4, VOC FROM ambient_air ORDER BY timestamp DESC LIMIT 1");
$air = $stmt->fetch(PDO::FETCH_ASSOC);

$alertes = [];
if ($air['CO2'] > $seuil_co2){
    $alertes[] = "Le niveau de CO2 est trop élevé.";
}

if ($air['CH4'] > $seuil_ch4){
    $alertes[] = "Le niveau de CH4 est trop élevé.";
}

if ($air['VOC'] > $seuil_voc){
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

// Connexion BDD

require_once __DIR__ . '/../models/Database.php';
$pdo = Database::getConnection();

// Dernière mesure météo
$stmt = $pdo->query("SELECT temperature, humidite FROM groupe_4B ORDER BY horodatage DESC LIMIT 1");
$meteo = $stmt->fetch(PDO::FETCH_ASSOC);

// Avant-dernière mesure
$stmt2 = $pdo->query("SELECT temperature, humidite FROM groupe_4B ORDER BY horodatage DESC LIMIT 1 OFFSET 1");
$meteo_precedent = $stmt2->fetch(PDO::FETCH_ASSOC);

// Variations température
$variation_temp = $meteo['temperature'] - $meteo_precedent['temperature'];
$fleche_temp = $variation_temp > 0 ? '↗️' : ($variation_temp < 0 ? '↘️' : '→');
$signe_temp = $variation_temp > 0 ? '+' : '';

// Variations humidité
$variation_hum = $meteo['humidite'] - $meteo_precedent['humidite'];
$fleche_hum = $variation_hum > 0 ? '↗️' : ($variation_hum < 0 ? '↘️' : '→');
$signe_hum = $variation_hum > 0 ? '+' : '';
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
        
        <?php if (empty($alertes)): ?>
            <p class="ok">✅ Aucun problème</p>
        <?php else: ?>
            <ul class="alert-list">
                <?php foreach ($alertes as $alerte): ?>
                    <li class="alert-item">⚠️ <?= $alerte ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

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
                    <p class="value"><?= $meteo['temperature'] ?> °C</p>
                    <p class="evolution"><?= $fleche_temp ?> <?= $signe_temp . number_format($variation_temp, 2) ?> °C</p>
                </div>
                <div class="ctn air-section-container">
                    <h1>Humidité</h1>
                    <p class="value"><?= $meteo['humidite'] ?> %</p>
                    <p class="evolution"><?= $fleche_hum ?> <?= $signe_hum . number_format($variation_hum, 2) ?> %</p>
                </div>
            </div>
        </a>
    </div>
</main>