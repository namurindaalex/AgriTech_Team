fetch('fetch_satellite_data.php')
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            document.getElementById('recommendations').innerHTML = 'Satellite data not available.';
        } else {
            // Update moistureChart with real-time data
            moistureChart.data.datasets[0].data = [data.moisture1, data.moisture2, data.moisture3];
            moistureChart.update();
        }
    });
