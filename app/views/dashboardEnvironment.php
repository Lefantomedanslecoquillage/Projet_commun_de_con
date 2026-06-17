<?php
include_once __DIR__ . "/head.html";
include_once __DIR__ . "/header.php";

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

$temperatureValue = $meteo['temperature'] !== null ? $meteo['temperature'] : '—';
$humidityValue = $meteo['humidite'] !== null ? $meteo['humidite'] : '—';
$temperatureEvolution = $variation_temp === null ? '→ stable' : $fleche_temp . ' ' . $signe_temp . number_format($variation_temp, 2) . ' °C';
$humidityEvolution = $variation_hum === null ? '→ stable' : $fleche_hum . ' ' . $signe_hum . number_format($variation_hum, 2) . ' %';
?>

<main>
    <div class="ctn air-section-container">
        <h1>Température</h1>
        <p class="value"><?= htmlspecialchars((string)$temperatureValue, ENT_QUOTES, 'UTF-8') ?> °C</p>
        <p class="evolution"><?= htmlspecialchars($temperatureEvolution, ENT_QUOTES, 'UTF-8') ?></p>
    </div>
    <div class="ctn air-section-container">
        <h1>Humidité</h1>
        <p class="value"><?= htmlspecialchars((string)$humidityValue, ENT_QUOTES, 'UTF-8') ?> %</p>
        <p class="evolution"><?= htmlspecialchars($humidityEvolution, ENT_QUOTES, 'UTF-8') ?></p>
    </div>
</main>