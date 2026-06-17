<?php
include_once __DIR__ . "/head.html";
include_once __DIR__ . "/header.php";

require_once __DIR__ . '/../models/Database.php'; // adapte si besoin
$pdo = Database::getConnection();


// Seuils pour les alertes

$seuil_co2 = 800;   // ppm
$seuil_ch4 = 50;    // à adapter
$seuil_voc = 200;   // à adapter

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

// Connexion BDD

require_once __DIR__ . '/../models/Database.php';
$pdo = Database::getConnection();

// Dernière mesure météo
$stmt = $pdo->query("SELECT temperature, humidite FROM groupe_4B ORDER BY horodatage DESC LIMIT 1");
$meteo = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
$meteo = is_array($meteo) ? $meteo : ["temperature" => null, "humidite" => null];

// Avant-dernière mesure
$stmt2 = $pdo->query("SELECT temperature, humidite FROM groupe_4B ORDER BY horodatage DESC LIMIT 1 OFFSET 1");
$meteo_precedent = $stmt2 ? $stmt2->fetch(PDO::FETCH_ASSOC) : false;
$meteo_precedent = is_array($meteo_precedent) ? $meteo_precedent : ["temperature" => null, "humidite" => null];

// Variations température
$variation_temp = ($meteo['temperature'] !== null && $meteo_precedent['temperature'] !== null)
    ? $meteo['temperature'] - $meteo_precedent['temperature']
    : null;
$fleche_temp = $variation_temp > 0 ? '↗️' : ($variation_temp < 0 ? '↘️' : '→');
$signe_temp = $variation_temp > 0 ? '+' : '';

// Variations humidité
$variation_hum = ($meteo['humidite'] !== null && $meteo_precedent['humidite'] !== null)
    ? $meteo['humidite'] - $meteo_precedent['humidite']
    : null;
$fleche_hum = $variation_hum > 0 ? '↗️' : ($variation_hum < 0 ? '↘️' : '→');
$signe_hum = $variation_hum > 0 ? '+' : '';
$stmt = $pdo->query("SELECT timestamp, CO2, CH4, VOC FROM ambient_air ORDER BY timestamp DESC LIMIT 2");
$rows = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
$rows = is_array($rows) ? array_reverse($rows) : [];

$tsValues = [];
$co2Values = [];
$ch4Values = [];
$vocValues = [];

foreach ($rows as $row) {
    if (!isset($row['timestamp']) || $row['timestamp'] === null) {
        continue;
    }

    $tsValues[] = $row['timestamp'];
    $co2Values[] = $row['CO2'] !== null ? (float)$row['CO2'] : null;
    $ch4Values[] = $row['CH4'] !== null ? (float)$row['CH4'] : null;
    $vocValues[] = $row['VOC'] !== null ? (float)$row['VOC'] : null;
}

$temperatureValue = $meteo['temperature'] !== null ? $meteo['temperature'] : '—';
$humidityValue = $meteo['humidite'] !== null ? $meteo['humidite'] : '—';
$temperatureEvolution = $variation_temp === null ? '→ stable' : $fleche_temp . ' ' . $signe_temp . number_format($variation_temp, 2) . ' °C';
$humidityEvolution = $variation_hum === null ? '→ stable' : $fleche_hum . ' ' . $signe_hum . number_format($variation_hum, 2) . ' %';
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
                    <li class="alert-item">⚠️ <?= htmlspecialchars($alerte, ENT_QUOTES, 'UTF-8') ?></li>
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
                    <p id="temperatureValue" class="value"><?= htmlspecialchars((string)$temperatureValue, ENT_QUOTES, 'UTF-8') ?> °C</p>
                    <p id="temperatureEvolution" class="evolution"><?= htmlspecialchars($temperatureEvolution, ENT_QUOTES, 'UTF-8') ?></p>
                </div>
                <div class="ctn air-section-container">
                    <h1>Humidité</h1>
                    <p id="humidityValue" class="value"><?= htmlspecialchars((string)$humidityValue, ENT_QUOTES, 'UTF-8') ?> %</p>
                    <p id="humidityEvolution" class="evolution"><?= htmlspecialchars($humidityEvolution, ENT_QUOTES, 'UTF-8') ?></p>
                </div>
            </div>
        </a>
    </div>
</main>