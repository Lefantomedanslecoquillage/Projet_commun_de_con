new Chart(document.getElementById("co2Chart"), {
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
	options: {
		responsive: true,
		maintainAspectRatio: false,
		scales: {
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
				beginAtZero: true,
				title: { display: true, text: "ppm" }
			}
		},
		plugins: {
			tooltip: { mode: "index", intersect: false },
			legend: { position: "top" }
		}
	}
});

new Chart(document.getElementById("vocChart"), {
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
	options: {
		responsive: true,
		maintainAspectRatio: false,
		scales: {
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
				beginAtZero: true,
				title: { display: true, text: "ppb" }
			}
		},
		plugins: {
			tooltip: { mode: "index", intersect: false },
			legend: { position: "top" }
		}
	}
});
