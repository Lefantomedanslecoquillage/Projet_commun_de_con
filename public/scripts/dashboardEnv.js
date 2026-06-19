function Options(unit) {
	this.responsive = true
	this.maintainAspectRatio = false

	this.scales = {
		x: {
			type: "time",
			time: {
				unit: "minute",
				displayFormats: {
					minute: "HH:mm"
				}
			},
			title: {
				display: true,
				text: "Heure"
			}
		},
		y: {
			title: {
				display: true,
				text: unit
			}
		}
	}
}

const lightChart = new Chart(
	document.getElementById("lightChart"),
	{
		type: "line",
		data: {
			datasets: [{
				label: "Luminosité",
				data: lightData,
				borderColor: "rgb(255,206,86)",
				backgroundColor: "rgba(255,206,86,0.1)",
				tension: 0.3
			}]
		},
		options: new Options("lux")
	}
)

const soundChart = new Chart(
	document.getElementById("soundChart"),
	{
		type: "line",
		data: {
			datasets: [{
				label: "Niveau sonore",
				data: soundData,
				borderColor: "rgb(54,162,235)",
				backgroundColor: "rgba(54,162,235,0.1)",
				tension: 0.3
			}]
		},
		options: new Options("dB")
	}
)

const tempChart = new Chart(
	document.getElementById("tempChart"),
	{
		type: "line",
		data: {
			datasets: [{
				label: "Température",
				data: tempData,
				borderColor: "rgb(75, 192, 192)",
				backgroundColor: "rgba(75, 192, 192,0.1)",
				tension: 0.3
			}]
		},
		options: new Options("°C")
	}
)

const humChart = new Chart(
	document.getElementById("humChart"),
	{
		type: "line",
		data: {
			datasets: [{
				label: "Humidité",
				data: humData,
				borderColor: "rgb(153, 102, 255)",
				backgroundColor: "rgba(153, 102, 255,0.1)",
				tension: 0.3
			}]
		},
		options: new Options("%")
	}
)

let refreshInterval = setInterval(() => {
	fetch(`index.php?section=environment&range=${range}&format=json`, { cache: "no-store" })
		.then(response => {
			if (!response.ok) throw new Error("Erreur réseau")
			return response.json()
		})
		.then(newData => {
			lightChart.data.datasets[0].data = newData.light
			lightChart.update()

			soundChart.data.datasets[0].data = newData.sound
			soundChart.update()

			tempChart.data.datasets[0].data = newData.temperature
			tempChart.update()

			humChart.data.datasets[0].data = newData.humidity
			humChart.update()
		})
		.catch(error => console.error("Échec du rafraîchissement :", error))
}, 15000)