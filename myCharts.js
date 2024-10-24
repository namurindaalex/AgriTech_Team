const fetchInterval = 15000;  // 15 seconds

// Arrays to store humidity, soil moisture, and temperature data
let humidityData = [];
let moistureData = [];
let temperatureData = [];
let timestampsData = [];

var humidityS=0;
var temperatureS=0;
var moistureS=0;


// Fetch data from ThingSpeak every 15 seconds
const fetchThingSpeakData = async () => {
    const url = `https://api.thingspeak.com/channels/2683924/feeds.json?api_key=Z6NEVNX75TN9JVGT&results=2`;


  try {
    const response = await fetch(url);
    const data = await response.json();

    // Check if feeds exist in the response
    if (data.feeds && data.feeds.length > 0) {
      const feed = data.feeds[0]; // fetch the latest feed

      // Extract and store data for humidity, moisture, and temperature
      const temperature = parseFloat(feed.field1);
      const humidity = parseFloat(feed.field2);
      const moisture = parseFloat(feed.field3);
      const timestamp = new Date(feed.created_at).toLocaleTimeString();
      humidityS=humidity; temperatureS=temperature; moistureS=moisture;
      // Store the data in arrays
      humidityData.push(humidity);
      moistureData.push(moisture);
      temperatureData.push(temperature);
      timestampsData.push(timestamp);

      // Limit the array length to avoid excessive data (optional)
      const maxDataLength = 100;
      if (humidityData.length > maxDataLength) {
        humidityData.shift();
        moistureData.shift();
        temperatureData.shift();
        timestampsData.shift();
      }

      // Plot the data
    //   plotData(timestampsData, humidityData, moistureData, temperatureData);
      plotData(timestampsData, humidityS, moistureS, temperatureS,humidityData,moistureData,temperatureData);
    } else {
      console.error("No data found in ThingSpeak feeds.");
    }
  } catch (error) {
    console.error("Error fetching ThingSpeak data:", error);
  }
};



const plotData = (timestamps, humidity, moisture, temperature,humidityD,moistureD,temperatureD) => {
    // Humidity Gauge
Plotly.newPlot('humidityGauge', [{
    type: 'indicator',
    mode: 'gauge+number',
    value: Number(humidity),
    title: { text: "Humidity [%]" },
    gauge: {
      axis: { range: [0, 100] },
      bar: { color: "#027c49" }, // Color for the gauge bar
      steps: [
        { range: [0, 50], color: "#e0e0e0" },
        { range: [50, 100], color: "#cfe5d4" }
      ]
    }
  }], { responsive: true });
  
  // Soil Moisture Gauge
  Plotly.newPlot('soilMoistureGauge', [{
    type: 'indicator',
    mode: 'gauge+number',
    value: Number(moisture),
    title: { text: "Soil Moisture [%]" },
    gauge: {
      axis: { range: [0, 100] },
      bar: { color: "#2a6b2d" },
      steps: [
        { range: [0, 50], color: "#e0e0e0" },
        { range: [50, 100], color: "#cfe5d4" }
      ]
    }
  }], { responsive: true });
  
  // Rainfall Status Donut Chart
  Plotly.newPlot('rainfallDonut', [{
    values: [10, 20, 50, 20], // Example values: Moderate has the highest value
    labels: ['None', 'Light', 'Moderate', 'Heavy'],
    type: 'pie',
    hole: 0.4,
    marker: {
      colors: ['#f1c40f', '#3498db', '#027c49', '#e74c3c'] // Customize colors for categories
    }
  }], {
    title: 'Rainfall Status',
    responsive: true
  });
  
 // Temperature Gauge with Steps, Threshold, and Delta
 var data = [
    {
      domain: { x: [0, 1], y: [0, 1] },
      value: Number(temperature),
      title: { text: "Temperature [°C]" },
      type: "indicator",
      mode: "gauge+number+delta",
      delta: { reference: 20 },
      gauge: {
        axis: { range: [null, 50] },
        steps: [
          { range: [0, 25], color: "lightgray" },
          { range: [25, 40], color: "gray" }
        ],
        threshold: {
          line: { color: "red", width: 4 },
          thickness: 0.75,
          value: 40
        }
      }
    }
  ];
  
  Plotly.newPlot('temperatureThermometer', data);



















  const temperatureTrace = {
    x: ['Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5', 'Day 6', 'Day 7'], // Labels for each day of the week
    y: temperatureD,
    mode: 'lines+markers',
    name: 'Temperature (°C)',
    line: { color: 'orange' },
    xaxis: {
        title: 'Days',
    },
    yaxis: {
        title: 'Temperature (°C)',
    }
};
const temperatureLayout = { 
    title: 'Temperature Over a Week', 
        xaxis: {
    title: 'Days',
},
yaxis: {
    title: 'Temperature (°C)',
} };
Plotly.newPlot('temperatureLineChart', [temperatureTrace], temperatureLayout);

// Line Chart for Humidity
const humidityTrace = {
    x: ['Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5', 'Day 6', 'Day 7'],
    y: humidityD,
    mode: 'lines+markers',
    name: 'Humidity (%)',
    xaxis: {
        title: 'Days',
    },
    yaxis: {
        title: 'Temperature (°C)',
    } 
};
const humidityLayout = { title: 'Humidity Over a Week',xaxis: {
    title: 'Days',
},
yaxis: {
    title: 'Humidity (%)',
}  };
Plotly.newPlot('humidityLineChart', [humidityTrace], humidityLayout);

// Bar Chart for Soil Moisture
const soilMoistureTrace = {
    x: ['Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5', 'Day 6', 'Day 7'], // Labels for each day of the week
    y: moistureD,
    type: 'bar',
    name: 'Soil Moisture (%)'
};
const soilMoistureLayout = { title: 'Soil Moisture Levels Over a Week',xaxis: {
    title: 'Days',
},
yaxis: {
    title: 'Soil Moisture (%)',
}  };
Plotly.newPlot('soilMoistureBarChart', [soilMoistureTrace], soilMoistureLayout);


plotHumidity(humidity);
plotSoilMoisture(moisture);  // Plot soil moisture on the back
plotTemperature(temperature);  // Plot soil moisture on the back
plotSoilMoistureAnalyticsDash(moistureD);

}

fetchThingSpeakData();
setInterval(fetchThingSpeakData, fetchInterval);





























  
  





















  











let isFrontShowing = true; // Track which side is currently showing

// Function to plot Humidity on the front side
function plotHumidity(humidityDash) {
    Plotly.newPlot('sensorGaugeFront', [{
        type: 'indicator',
        mode: 'gauge+number',
        value: humidityDash, // Replace with real-time humidity data
        title: { text: "Humidity [%]" },
        gauge: {
            axis: { range: [0, 100] },
            bar: { color: "#027c49" }, // Color for the gauge bar
            steps: [
                { range: [0, 50], color: "#e0e0e0" },
                { range: [50, 100], color: "#cfe5d4" }
            ]
        }
    }], { responsive: true });
}

// Function to plot Soil Moisture on the back side
function plotSoilMoisture(moistureDash) {
    Plotly.newPlot('sensorGaugeBack', [{
        type: 'indicator',
        mode: 'gauge+number',
        value: moistureDash, // Replace with real-time soil moisture data
        title: { text: "Soil Moisture [%]" },
        gauge: {
            axis: { range: [0, 100] },
            bar: { color: "#2a6b2d" }, // Color for the gauge bar
            steps: [
                { range: [0, 50], color: "#e0e0e0" },
                { range: [50, 100], color: "#cfe5d4" }
            ]
        }
    }], { responsive: true });
}
function plotSoilMoistureAnalyticsDash(moistureDash) {
    const soilMoistureTrace = {
      x: ['Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5', 'Day 6', 'Day 7'], // Labels for each day of the week
      y: moistureDash,
      type: 'bar',
      name: 'Soil Moisture (%)'
  };
  const soilMoistureLayout = { title: 'Soil Moisture',xaxis: {
      title: 'Days',
  },
  yaxis: {
      title: 'Soil Moisture (%)',
  }  };
  Plotly.newPlot('soilMoistureAnalyticsDash', [soilMoistureTrace], soilMoistureLayout);
}
function waterManagementDash(moistureDash) {
    const soilMoistureTrace = {
      x: ['Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5', 'Day 6', 'Day 7'], // Labels for each day of the week
      y: moistureDash,
      type: 'bar',
      name: 'Soil Moisture (%)'
  };
  const soilMoistureLayout = { title: 'Soil Moisture',xaxis: {
      title: 'Days',
  },
  yaxis: {
      title: 'Soil Moisture (%)',
  }  };
  Plotly.newPlot('soilMoistureAnalyticsDash', [soilMoistureTrace], soilMoistureLayout);
}






// Function to flip the card
function flipCard() {
    const flipCard = document.getElementById('flipCard');
    
    if (isFrontShowing) {
        flipCard.classList.add('flipped'); // Add flip animation to show the back
    } else {
        flipCard.classList.remove('flipped'); // Remove flip animation to show the front
    }

    isFrontShowing = !isFrontShowing; // Toggle the state
}

// Initial plot for both sides



// Flip the card every 10 seconds
setInterval(flipCard, 10000); // 10,000 milliseconds = 10 seconds





let currentFlip = 0;
const flipCardInner = document.querySelector('.flip-card-inner');

function flipChart() {
    currentFlip = (currentFlip + 1) % 3; // Rotate through 3 charts

    if (currentFlip === 0) {
        flipCardInner.style.transform = 'rotateY(0deg)'; // Show temperature
    } else if (currentFlip === 1) {
        flipCardInner.style.transform = 'rotateY(180deg)'; // Show humidity
    } else {
        flipCardInner.style.transform = 'rotateY(360deg)'; // Show soil moisture
    }
}

// Set an interval to flip every 10 seconds
setInterval(flipChart, 10000);

document.addEventListener('DOMContentLoaded', () => {
    // Temperature chart
    Plotly.newPlot('temperatureLineChartDash', [temperatureTrace], temperatureLayout);

    // Humidity chart
    Plotly.newPlot('humidityLineChartDash', [humidityTrace], humidityLayout);

    // Soil Moisture chart
    Plotly.newPlot('soilMoistureBarChartDash', [soilMoistureTrace], soilMoistureLayout);
});





function plotTemperature(temperatureDash){
    var data = [
        {
          domain: { x: [0, 1], y: [0, 1] },
          value: temperatureDash, // Your temperature value
          title: { text: "Temperature [°C]" },
          type: "indicator",
          mode: "gauge+number+delta",
          delta: { reference: 20 },
          gauge: {
            axis: { range: [null, 50] }, // Gauge range
            steps: [
              { range: [0, 25], color: "lightgray" },
              { range: [25, 40], color: "gray" }
            ],
            threshold: {
              line: { color: "red", width: 4 },
              thickness: 0.75,
              value: 40 // Red line at 40°C
            }
          }
        }
      ];
      
    // Plot the temperature gauge inside the 'temperatureThermometer' container
    Plotly.newPlot('temperatureThermometerDash', data);
}








// Array of recommendations
const recommendations = [
    {
        title: "Soil Quality",
        content: "Consider adding organic compost to improve soil nutrients.",
    },
    {
        title: "Water Management",
        content: "Optimize drip irrigation schedules to reduce water usage by 15%.",
    },
    {
        title: "Crop Health",
        content: "Monitor crop health regularly for signs of disease or pest infestation.",
    },
    {
        title: "Weather-Based Recommendations",
        content: "Rain expected in 3 days; reduce irrigation to avoid overwatering.",
    }
];

// Get the HTML elements where we'll display the recommendations
const recommendationTitle = document.getElementById("recommendation-title");
const recommendationContent = document.getElementById("recommendation-content");

// Function to rotate through recommendations
let currentRecommendation = 0;

function displayNextRecommendation() {
    const recommendation = recommendations[currentRecommendation];
    recommendationTitle.textContent = recommendation.title;
    recommendationContent.textContent = recommendation.content;
    
    // Increment to the next recommendation, or go back to the first if at the end
    currentRecommendation = (currentRecommendation + 1) % recommendations.length;
}

// Start by displaying the first recommendation
displayNextRecommendation();

// Update the card every 5 seconds (5000 milliseconds)
setInterval(displayNextRecommendation, 5000);

