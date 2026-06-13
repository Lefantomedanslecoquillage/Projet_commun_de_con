const sectionCO2 = document.getElementById("sectionCO2")
const sectionCH4 = document.getElementById("sectionCH4")
const sectionVOC = document.getElementById("sectionVOC")

function updateSectionAir() {
	sectionCO2.querySelector(".value").style.display = "none"
	sectionCO2.querySelector(".evolution").style.display = "none"
	sectionCO2.querySelector(".status-icon").style.color = "var(--danger-color)"
	sectionCO2.querySelector(".status").textContent = "Hors ligne"

	sectionCH4.querySelector(".value").style.display = "none"
	sectionCH4.querySelector(".evolution").style.display = "none"
	sectionCH4.querySelector(".status-icon").style.color = "var(--danger-color)"
	sectionCH4.querySelector(".status").textContent = "Hors ligne"

	sectionVOC.querySelector(".value").style.display = "none"
	sectionVOC.querySelector(".evolution").style.display = "none"
	sectionVOC.querySelector(".status-icon").style.color = "var(--danger-color)"
	sectionVOC.querySelector(".status").textContent = "Hors ligne"
	if (co2Data.length < 2)
		return

	sectionCO2.querySelector(".value").style.display = "block"
	sectionCO2.querySelector(".value").textContent = `${co2Data[1]} ppm`
	sectionCO2.querySelector("progress").value = co2Data[1]
	const co2Progression = ((co2Data[1] - co2Data[0]) / co2Data[0] * 100).toFixed(2)
	let co2Symbol = co2Progression > 0 ? "↗ +" : "↘ "
	sectionCO2.querySelector(".evolution").style.display = "block"
	sectionCO2.querySelector(".evolution").textContent = `${co2Symbol}${co2Progression} %`
	if (co2Progression == 0) {
		sectionVOC.querySelector(".evolution").textContent = "→ stable"
	}

	sectionCH4.querySelector(".value").style.display = "block"
	sectionCH4.querySelector(".value").textContent = `${ch4Data[1]} ppb`
	sectionCH4.querySelector("progress").value = ch4Data[1]
	const ch4Progression = ((ch4Data[1] - ch4Data[0]) / ch4Data[0] * 100).toFixed(2)
	let ch4Symbol = ch4Progression > 0 ? "↗ +" : "↘ "
	sectionCH4.querySelector(".evolution").style.display = "block"
	sectionCH4.querySelector(".evolution").textContent = `${ch4Symbol}${ch4Progression} %`
	if (ch4Progression == 0) {
		sectionVOC.querySelector(".evolution").textContent = "→ stable"
	}

	sectionVOC.querySelector(".value").style.display = "block"
	sectionVOC.querySelector(".value").textContent = `${vocData[1]} ppb`
	sectionVOC.querySelector("progress").value = vocData[1]
	const vocProgression = ((vocData[1] - vocData[0]) / vocData[0] * 100).toFixed(2)
	let vocSymbol = vocProgression > 0 ? "↗ +" : "↘ "
	sectionVOC.querySelector(".evolution").style.display = "block"
	sectionVOC.querySelector(".evolution").textContent = `${vocSymbol}${vocProgression} %`
	if (vocProgression == 0) {
		sectionVOC.querySelector(".evolution").textContent = "→ stable"
	}
	if (Date.now() / 1000 - co2Data[1]["timestamp"] > 60) {
		return

	sectionCO2.querySelector(".status-icon").style.color = "var(--active-color)"
	sectionCO2.querySelector(".status").textContent = "En ligne"

	sectionCH4.querySelector(".status-icon").style.color = "var(--active-color)"
	sectionCH4.querySelector(".status").textContent = "En ligne"

	sectionVOC.querySelector(".status-icon").style.color = "var(--active-color)"
	sectionVOC.querySelector(".status").textContent = "En ligne"
	}
}

let refreshInterval = setInterval(() => {
	fetch(`index.php?format=json`)
		.then(response => {
			if (!response.ok) throw new Error("Erreur réseau")
			return response.json()
		})
		.then(newData => {
			co2Data = newData.co2
			ch4Data = newData.ch4
			vocData = newData.voc

			updateSectionAir()
		})
		.catch(error => console.error("Échec du rafraîchissement :", error))
}, 5000)

updateSectionAir();
