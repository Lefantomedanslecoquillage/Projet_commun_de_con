function options(unit) {
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
        options: new options("dB")
    }
)