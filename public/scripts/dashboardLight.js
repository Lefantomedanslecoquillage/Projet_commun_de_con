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
        options: new options("lux")
    }
)