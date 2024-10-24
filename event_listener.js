// Event listener for the Dashboard link
document.getElementById('dashboard-link').addEventListener('click', function() {
    // Hide all other content sections
    const sections = document.querySelectorAll('.content-section');
    sections.forEach(section => section.style.display = 'none');

    // Show the dashboard content section
    document.getElementById('dashboard-content').style.display = 'block';
});

// Event listener for the My Profile link
document.getElementById('profile-link').addEventListener('click', function() {
    // Hide all other content sections
    const sections = document.querySelectorAll('.content-section');
    sections.forEach(section => section.style.display = 'none');

    // Show the profile content section
    document.getElementById('profile-content').style.display = 'block';
});

// Event listener for the Weather Forecast link
document.getElementById('weather-forecast-link').addEventListener('click', function() {
    // Hide all other content sections
    const sections = document.querySelectorAll('.content-section');
    sections.forEach(section => section.style.display = 'none');

    // Show the weather forecast content section
    document.getElementById('weather-forecast-content').style.display = 'block';

    // Fetch weather data
    fetchWeatherData();
});

// Fetch weather data function
function fetchWeatherDataDashboard() {
    const apiKey = 'e42271f29595c6ba99fbbe289cb312b9'; // Consider moving this to a secure location
    const city = 'Kampala'; // Set the location of the farm
    const apiUrl = `https://api.openweathermap.org/data/2.5/weather?q=${city}&appid=${apiKey}&units=metric`;

    const weatherDetailsDashboard = document.getElementById('weather-details-dashboard');
    // weatherDetails.innerHTML = 'Loading weather data...'; // Feedback while loading

    fetch(apiUrl)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            weatherDetailsDashboard.innerHTML = `
                <strong>Location:</strong> ${data.name} <br>
                <strong>Temperature:</strong> ${data.main.temp}°C <br>
                <strong>Condition:</strong> ${data.weather[0].description} <br>
                <strong>Humidity:</strong> ${data.main.humidity}% <br>
                <strong>Wind:</strong> ${data.wind.speed} km/h
            `;
        })
        .catch(error => {
            weatherDetailsDashboard.innerHTML = 'Error fetching weather data.';
            console.error('Error:', error);
        });
}

// Function to fetch and display NASA imagery
function fetchNasaImagery() {
    const apiKey = 'xlEz0ujYNy6jDJagTSQ2G0eiAWRIO9B9SU6AZTwp'; // Your NASA API key
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

// Call the function to fetch NASA imagery when the Farm Health section is displayed
document.getElementById('farm-health-link').addEventListener('click', function() {
    // Hide all other content sections
    const sections = document.querySelectorAll('.content-section');
    sections.forEach(section => section.style.display = 'none');

    // Show the farm health content section
    document.getElementById('farm-health-content').style.display = 'block';

    // Fetch and display NASA imagery
    fetchNasaImagery();
});



// Event listener for the Dashboard link
document.getElementById('dashboard-link').addEventListener('click', function() {
    // Hide all other content sections
    const sections = document.querySelectorAll('.content-section');
    sections.forEach(section => section.style.display = 'none');

    // Show the dashboard content section
    document.getElementById('dashboard-content').style.display = 'block';

    // Fetch and display weather data in Section 1
    fetchWeatherData();
});

// Fetch weather data function
function fetchWeatherData() {
    const apiKey = 'e42271f29595c6ba99fbbe289cb312b9'; // Consider moving this to a secure location
    const city = 'Kampala'; // Set the location of the farm
    const apiUrl = `https://api.openweathermap.org/data/2.5/weather?q=${city}&appid=${apiKey}&units=metric`;

    const weatherDetails = document.getElementById('weather-details');
    weatherDetails.innerHTML = 'Loading weather data...'; // Feedback while loading

    fetch(apiUrl)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            weatherDetails.innerHTML = `
                <strong>Location:</strong> ${data.name} <br>
                <strong>Temperature:</strong> ${data.main.temp}°C <br>
                <strong>Condition:</strong> ${data.weather[0].description} <br>
                <strong>Humidity:</strong> ${data.main.humidity}% <br>
                <strong>Wind:</strong> ${data.wind.speed} km/h
            `;
        })
        .catch(error => {
            weatherDetails.innerHTML = 'Error fetching weather data.';
            console.error('Error:', error);
        });
}


// Function to set the default view
function setDefaultView() {
    // Hide all content sections
    const sections = document.querySelectorAll('.content-section');
    sections.forEach(section => section.style.display = 'none');

    // Show the dashboard content section
    document.getElementById('dashboard-content').style.display = 'block';

    // Fetch and display weather data in Section 1
    fetchWeatherData();
}

// Event listener for the Dashboard link
document.getElementById('dashboard-link').addEventListener('click', function() {
    // Hide all other content sections
    const sections = document.querySelectorAll('.content-section');
    sections.forEach(section => section.style.display = 'none');

    // Show the dashboard content section
    document.getElementById('dashboard-content').style.display = 'block';

    // Fetch and display weather data in Section 1
    fetchWeatherData();
});

// Fetch weather data function
// function fetchWeatherData() {
//     const apiKey = 'e42271f29595c6ba99fbbe289cb312b9'; // Consider moving this to a secure location
//     const city = 'Mbarara'; // Set the location of the farm
//     const apiUrl = `https://api.openweathermap.org/data/2.5/weather?q=${city}&appid=${apiKey}&units=metric`;

//     const weatherDetails = document.getElementById('weather-details');
//     weatherDetails.innerHTML = 'Loading weather data...'; // Feedback while loading

//     fetch(apiUrl)
//         .then(response => {
//             if (!response.ok) {
//                 throw new Error(`HTTP error! status: ${response.status}`);
//             }
//             return response.json();
//         })
//         .then(data => {
//             weatherDetails.innerHTML = `
//                 <strong>Location:</strong> ${data.name} <br>
//                 <strong>Temperature:</strong> ${data.main.temp}°C <br>
//                 <strong>Condition:</strong> ${data.weather[0].description} <br>
//                 <strong>Humidity:</strong> ${data.main.humidity}% <br>
//                 <strong>Wind:</strong> ${data.wind.speed} km/h
//             `;
//         })
//         .catch(error => {
//             weatherDetails.innerHTML = 'Error fetching weather data.';
//             console.error('Error:', error);
//         });
// }

// Call the function to set the default view on page load
window.onload = setDefaultView;
