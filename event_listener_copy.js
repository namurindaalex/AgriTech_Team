// Utility function to hide all sections and show only the desired section
function showSection(sectionId) {
    const sections = document.querySelectorAll('.content-section');
    sections.forEach(section => section.style.display = 'none'); // Hide all sections
    document.getElementById(sectionId).style.display = 'block'; // Show the specific section
}

// Event listener for the Dashboard link
document.getElementById('dashboard-link').addEventListener('click', function() {
    showSection('dashboard-content'); // Show dashboard content
    fetchWeatherData(); // Fetch weather data for the dashboard
});

// Event listener for the My Profile link
document.getElementById('profile-link').addEventListener('click', function() {
    showSection('profile-content'); // Show profile content
});

// Event listener for the Weather Forecast link
document.getElementById('weather-forecast-link').addEventListener('click', function() {
    showSection('weather-forecast-content'); // Show weather forecast content
    fetchWeatherData(); // Fetch weather data for the forecast
});

// Event listener for the Farm Health link
document.getElementById('farm-health-link').addEventListener('click', function() {
    showSection('farm-health-content'); // Show farm health content
    fetchNasaImagery(); // Fetch NASA imagery for farm health
});


document.getElementById("recommendations-link").addEventListener('click', function () {
            showSection('recommendations-content'); // Show recommendations
    });

document.querySelectorAll('.toggle-btn').forEach(function(button) {
        button.addEventListener('click', function(event) {
            event.preventDefault(); // Prevent default anchor behavior
    
            // Find the closest `.more-content` element
            const moreContent = this.closest('.recommandations-item').querySelector('.more-content');
    
            // Toggle the display of the `.more-content`
            if (moreContent.style.display === "none") {
                moreContent.style.display = "block";
                this.textContent = "Read less"; // Update button text
            } else {
                moreContent.style.display = "none";
                this.textContent = "Continue Reading"; // Reset button text
            }
        });
    });
    
document.getElementById("community-resources-link").addEventListener('click', function () {
        showSection('community-resources-content'); // Show Community Resources
});
document.getElementById("farm-analytics-link").addEventListener('click', function () {
        showSection('farm-analytics-content'); // Show Farm Analytics
});
document.getElementById("sensors-analytics-link").addEventListener('click', function () {
        showSection('sensors-analytics-content'); // Show Sensor Analytics
});



function toggleReadMore(button, contentId) {
    var content = document.getElementById(contentId);

    if (content.style.display === "none") {
        content.style.display = "block"; // Show the hidden content
        button.textContent = "Read Less"; // Change button text
    } else {
        content.style.display = "none"; // Hide the content
        button.textContent = "Read More"; // Reset button text
    }
}
 
// Fetch weather data function (for both dashboard and forecast)
function fetchWeatherData() {
    const apiKey = 'e42271f29595c6ba99fbbe289cb312b9'; // Consider moving this to a secure location
    const city = 'Kampala'; // Set the location of the farm
    const apiUrl = `https://api.openweathermap.org/data/2.5/weather?q=${city}&appid=${apiKey}&units=metric`;

    const weatherDetails = document.getElementById('weather-details');
    const weatherDetailsDashboard = document.getElementById('weather-details-dashboard');
    weatherDetailsDashboard.innerHTML = 'Loading current weather data...'; // Feedback while loading
    weatherDetails.innerHTML = 'Loading weather data...'; // Feedback while loading
    
    fetch(apiUrl)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            const iconCode = data.weather[0].icon; // Get the icon code from API response
            const iconUrl = `http://openweathermap.org/img/wn/${iconCode}@2x.png`;
            const visibilityKm = (data.visibility / 1000).toFixed(2);
            weatherDetailsDashboard.innerHTML = `
                <div class="current-weather-card">
                    <div class='weather-info-upper-cards'>
                        <div class='weather-info-card-upper upper-image'>
                            <img class="current-weather-image" src=${iconUrl} alt="Clouds" />   
                        </div>
                        <div class='weather-info-card-upper'>
                            <p class="weather-info-card-upper-temp">${data.main.temp.toFixed(1)} °C</p>
                        </div>
                    </div>
                    <div class='weather-info-down-card'>
                        <div class='weather-info-card-down'>
                            <h4><i class='bx bxs-droplet ic'></i></h4>
                            <p>${data.main.humidity}%</p>
                        </div>
                        <div class='weather-info-card-down'>
                            <h4><i class='bx bx-wind ic'></i></h4>
                            <p>${data.wind.speed} Km/h</p>
                        </div>
                        <div class='weather-info-card-down'>
                            <h4><i class='bx bxs-bullseye'></i></h4>
                            <p>${visibilityKm} Km</p>
                        </div>
                    </div>
                </div>
            `;
            weatherDetails.innerHTML=`
                        <h3>${data.name}</h3>
                        <div class='weather-info-cards'>
                            <div class='weather-info-card'>
                                <h5>Temperature</h5>
                                <h4><i class='bx bxs-thermometer ic' ></i></h4>
                                <p>${data.main.temp}°C</p>
                            </div>
                            <div class='weather-info-card'>
                                <h5>clouds</h5>
                                <h4><i class='bx bx-cloud-drizzle ic' ></i></h4> 
                                <p class="cloud">${data.weather[0].description}</p>
                            </div>
                            <div class='weather-info-card'>
                                <h5>Humidity</h5>
                                <h4><i class='bx bxs-droplet ic'></i></h4>
                                <p>${data.main.humidity}%</p>
                            </div>
                            <div class='weather-info-card'>
                                <h5>Wind Speed</h5>
                                <h4><i class='bx bx-wind ic'></i></h4>
                                <p>${data.wind.speed} Km/h</p>
                            </div>
                        </div>
            `

        })
        .catch(error => {
            weatherDetails.innerHTML = 'Error fetching weather data.';
            console.error('Error:', error);
        });
}

// Function to fetch and display NASA imagery for farm health
function fetchNasaImagery() {
    const apiKey = 'xlEz0ujYNy6jDJagTSQ2G0eiAWRIO9B9SU6AZTwp';
    const lat = '0.3344113949480149'; // Latitude
    const lon = '32.60134773998587'; // Longitude
    const date = '2024-10-02'; // Desired date for imagery
    const apiUrl = `https://api.nasa.gov/planetary/earth/imagery?lon=${lon}&lat=${lat}&date=${date}&api_key=${apiKey}`;

    const nasaImage = document.getElementById('nasa-farm-image');
    const message = document.getElementById('nasa-image-message'); // Element for messages

    fetch(apiUrl)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.blob(); // Return response as a Blob
        })
        .then(blob => {
            const imageUrl = URL.createObjectURL(blob); // Create a URL for the image blob
            nasaImage.src = imageUrl; // Set the src attribute of the img element
            nasaImage.style.display = 'block'; // Show the image
            message.textContent = ''; // Clear any previous messages
        })
        .catch(error => {
            console.error('Error fetching NASA imagery:', error);
            message.textContent = 'Error fetching image.'; // Show error message
        });
}

// Set the default view to dashboard when the page loads
function setDefaultView() {
    showSection('dashboard-content'); // Show dashboard by default
    fetchWeatherData(); // Fetch weather data for the dashboard
}

// Call the function to set the default view on page load

// Get the sidebar and menu icon
const sidebar = document.querySelector('.farmer-details');
const menuIcon = document.getElementById('menu-icon');

// Toggle the sidebar on menu icon click
menuIcon.addEventListener('click', function(event) {
    event.preventDefault(); // Prevent the default link behavior
    sidebar.classList.toggle('show'); // Toggle 'show' class to open/close sidebar
});

window.onload = setDefaultView;
