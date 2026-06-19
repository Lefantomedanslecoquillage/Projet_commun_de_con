const sectionCO2 = document.getElementById("sectionCO2")
const sectionCH4 = document.getElementById("sectionCH4")
const sectionVOC = document.getElementById("sectionVOC")
const sectionLight = document.getElementById("sectionLight")
const sectionSound = document.getElementById("sectionSound")
const sectionTemp = document.getElementById("sectionTemp")
const sectionHum = document.getElementById("sectionHum")

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

    if (!isRecentData()) {
        return
    }

    setOnline(sectionCO2)
    setOnline(sectionCH4)
    setOnline(sectionVOC)
}

function updateSectionWeather() {
    setOffline(sectionTemp)
    setOffline(sectionHum)

    updateGasSection(sectionTemp, [weatherData[0]["temperature"], weatherData[1]["temperature"]], "°C")
    updateGasSection(sectionHum, [weatherData[0]["humidite"], weatherData[1]["humidite"]], "%")

    const latestTimestamp = weatherData[weatherData.length - 1]["horodatage"]
    const utcDateStr = latestTimestamp.replace(" ", "T") + "Z"
    const timestampMs = Date.parse(utcDateStr)
    if (Date.now() - timestampMs >= 60_000) {
        return
    }

    setOnline(sectionTemp)
    setOnline(sectionHum)
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
            weatherData = Array.isArray(newData.weather) ? newData.weather : []

            updateGenericSection(sectionLight,lightData,"lux")
            updateGenericSection(sectionSound,soundData,"dB")
            updateSectionAir()
            updateSectionWeather()
        })
        .catch(error => {
            console.error("Échec du rafraîchissement :", error)

            setOffline(sectionCO2)
            setOffline(sectionCH4)
            setOffline(sectionVOC)
            setOffline(sectionLight)
            setOffline(sectionSound)
            setOffline(sectionTemp)
            setOffline(sectionHum)
        })
}, 5000)

updateGenericSection(sectionLight,lightData,"lux")
updateGenericSection(sectionSound,soundData,"dB")
updateSectionAir()
updateSectionWeather()