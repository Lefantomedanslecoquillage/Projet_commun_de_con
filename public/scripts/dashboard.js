function options(unit) {
	this.responsive = true,
	this.maintainAspectRatio = false,
	this.scales = {
		x: {
			type: "time",
			time: {
				unit: "minute",
				displayFormats: { minute: "HH:mm" },
				tooltipFormat: "dd/MM/yyyy HH:mm"
			},
			title: { display: true, text: "Heure" }
		},
		y: {
			type: "logarithmic",
			title: { display: true, text: unit }
		}
	},
	this.plugins = {
		tooltip: { mode: "index", intersect: false },
		legend: { position: "top" }
	}
}

const co2Chart = new Chart(document.getElementById("co2Chart"), {
	type: "line",
	data: {
		datasets: [{
			label: "CO2",
			data: co2Data,
			borderColor: "rgb(255, 99, 132)",
			backgroundColor: "rgba(255, 99, 132, 0.1)",
			tension: 0.3
		}]
	},
	options: new options("ppm")
})

const ch4Chart = new Chart(document.getElementById("ch4Chart"), {
	type: "line",
	data: {
		datasets: [{
			label: "CH4",
			data: ch4Data,
			borderColor: "rgb(46, 204, 113)",
			backgroundColor: "rgba(46, 204, 113, 0.1)",
			tension: 0.3
		}]
	},
	options: new options("ppb")
})

const vocChart = new Chart(document.getElementById("vocChart"), {
	type: "line",
	data: {
		datasets: [{
			label: "VOC",
			data: vocData,
			borderColor: "rgb(75, 192, 192)",
			backgroundColor: "rgba(75, 192, 192, 0.1)",
			tension: 0.3
		}]
	},
	options: new options("ppb")
})

let refreshInterval = setInterval(() => {
	fetch(`index.php?action=dashboard&range=${range}&format=json`)
		.then(response => {
			if (!response.ok) throw new Error("Erreur réseau")
			return response.json()
		})
		.then(newData => {
			co2Chart.data.datasets[0].data = newData.co2;
			co2Chart.update()

			ch4Chart.data.datasets[0].data = newData.ch4;
			ch4Chart.update()

			vocChart.data.datasets[0].data = newData.voc;
			vocChart.update()
		})
		.catch(error => console.error("Échec du rafraîchissement :", error))
}, 5000)