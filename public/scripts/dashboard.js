const sectionCO2 = document.getElementById("sectionCO2")
const sectionCH4 = document.getElementById("sectionCH4")
const sectionVOC = document.getElementById("sectionVOC")
const temperatureValue = document.getElementById("temperatureValue")
const temperatureEvolution = document.getElementById("temperatureEvolution")
const humidityValue = document.getElementById("humidityValue")
const humidityEvolution = document.getElementById("humidityEvolution")
const sectionLight = document.getElementById("sectionLight")
const sectionSound = document.getElementById("sectionSound")

function setOffline(section) {
    const valueElement = section?.querySelector(".value")
    const evolutionElement = section?.querySelector(".evolution")
    const statusIconElement = section?.querySelector(".status-icon")
    const statusElement = section?.querySelector(".status")

    if (valueElement) valueElement.style.display = "none"
    if (evolutionElement) evolutionElement.style.display = "none"
    if (statusIconElement) statusIconElement.style.color = "var(--danger-color)"
    if (statusElement) statusElement.textContent = "Hors ligne"
}

function setOnline(section) {
    const statusIconElement = section?.querySelector(".status-icon")
    const statusElement = section?.querySelector(".status")

    if (statusIconElement) statusIconElement.style.color = "var(--active-color)"
    if (statusElement) statusElement.textContent = "En ligne"
}

function updateGasSection(section, data, unit) {
    if (!section || !Array.isArray(data) || data.length < 2) {
        setOffline(section)
        return
    }

    const previousRaw = data[data.length - 2]
    const latestRaw = data[data.length - 1]

    if (previousRaw === null || previousRaw === undefined || latestRaw === null || latestRaw === undefined) {
        setOffline(section)
        return
    }

    const previousValue = Number(previousRaw)
    const latestValue = Number(latestRaw)

    if (Number.isNaN(previousValue) || Number.isNaN(latestValue)) {
        setOffline(section)
        return
    }

    const valueElement = section.querySelector(".value")
    const evolutionElement = section.querySelector(".evolution")
    const progressElement = section.querySelector("progress")

    if (valueElement) {
        valueElement.style.display = "block"
        valueElement.textContent = `${latestValue} ${unit}`
    }

    if (progressElement) {
        progressElement.value = latestValue
    }

    if (!evolutionElement) {
        return
    }

    evolutionElement.style.display = "block"

    if (previousValue === 0) {
        evolutionElement.textContent = "→ stable"
        return
    }

    const progression = ((latestValue - previousValue) / previousValue * 100).toFixed(2)

    if (Number(progression) > 0) {
        evolutionElement.textContent = `↗ +${progression} %`
    } else if (Number(progression) < 0) {
        evolutionElement.textContent = `↘ ${progression} %`
    } else {
        evolutionElement.textContent = "→ stable"
    }
}

function updateWeatherSection(latestValue, previousValue, unit, valueElement, evolutionElement) {
    if (!valueElement || !evolutionElement || latestValue === null || latestValue === undefined) {
        return
    }

    const latestNumericValue = Number(latestValue)
    const previousNumericValue = previousValue === null || previousValue === undefined ? null : Number(previousValue)

    if (Number.isNaN(latestNumericValue)) {
        return
    }

    valueElement.textContent = `${latestNumericValue} ${unit}`

    if (previousNumericValue === null || Number.isNaN(previousNumericValue)) {
        evolutionElement.textContent = "→ stable"
        return
    }

    const variation = latestNumericValue - previousNumericValue
    const formattedVariation = Math.abs(variation).toFixed(2)

    if (variation > 0) {
        evolutionElement.textContent = `↗ +${formattedVariation} ${unit}`
    } else if (variation < 0) {
        evolutionElement.textContent = `↘ -${formattedVariation} ${unit}`
    } else {
        evolutionElement.textContent = "→ stable"
    }
}

function isRecentData() {
    if (!Array.isArray(tsData) || tsData.length < 1) {
        return false
    }

    const latestTimestamp = tsData[tsData.length - 1]

    if (typeof latestTimestamp !== "string" || latestTimestamp.trim() === "") {
        return false
    }

    const utcDateStr = latestTimestamp.replace(" ", "T") + "Z"
    const timestampMs = Date.parse(utcDateStr)

    if (Number.isNaN(timestampMs)) {
        return false
    }

    return Date.now() - timestampMs <= 60_000
}

function updateGenericSection(section,data,unit){

    if(!section || !Array.isArray(data) || data.length < 2){
        setOffline(section)
        return
    }

    const previous = Number(data[data.length-2])
    const latest = Number(data[data.length-1])

    const valueElement = section.querySelector(".value")
    const evolutionElement = section.querySelector(".evolution")
    const progressElement = section.querySelector("progress")

    valueElement.textContent = `${latest} ${unit}`

    progressElement.value = latest

    const variation =
        previous === 0
        ? 0
        : ((latest-previous)/previous*100)

    if(variation>0){
        evolutionElement.textContent =
            `↗ +${variation.toFixed(2)} %`
    }
    else if(variation<0){
        evolutionElement.textContent =
            `↘ ${variation.toFixed(2)} %`
    }
    else{
        evolutionElement.textContent =
            "→ stable"
    }

    setOnline(section)
}

function updateSectionAir() {
    setOffline(sectionCO2)
    setOffline(sectionCH4)
    setOffline(sectionVOC)

    updateGasSection(sectionCO2, co2Data, "ppm")
    updateGasSection(sectionCH4, ch4Data, "ppb")
    updateGasSection(sectionVOC, vocData, "ppb")
    updateGenericSection(sectionLight,lightData,"lux")
    updateGenericSection(sectionSound,soundData,"dB")

    if (!isRecentData()) {
        return
    }

    setOnline(sectionCO2)
    setOnline(sectionCH4)
    setOnline(sectionVOC)
}

function updateDashboardWeather(data) {
    if (!data || typeof data !== "object") {
        return
    }

    updateWeatherSection(data.temperature, data.previousTemperature, "°C", temperatureValue, temperatureEvolution)
    updateWeatherSection(data.humidite, data.previousHumidite, "%", humidityValue, humidityEvolution)
}

let refreshInterval = setInterval(() => {
    fetch("index.php?format=json", { cache: "no-store" })
        .then(response => {
            if (!response.ok) {
                throw new Error("Erreur réseau")
            }

            return response.json()
        })
        .then(newData => {
            tsData = Array.isArray(newData.ts) ? newData.ts : []
            co2Data = Array.isArray(newData.co2) ? newData.co2 : []
            ch4Data = Array.isArray(newData.ch4) ? newData.ch4 : []
            vocData = Array.isArray(newData.voc) ? newData.voc : []
            lightData = Array.isArray(newData.light) ? newData.light : []
            soundData = Array.isArray(newData.sound) ? newData.sound : []

            updateSectionAir()
            updateDashboardWeather(newData)
            updateGenericSection(sectionLight,lightData,"lux")
            updateGenericSection(sectionSound,soundData,"dB")
        })
        .catch(error => {
            console.error("Échec du rafraîchissement :", error)

            setOffline(sectionCO2)
            setOffline(sectionCH4)
            setOffline(sectionVOC)
            setOffline(sectionLight)
            setOffline(sectionSound)
        })
}, 5000)

updateSectionAir()