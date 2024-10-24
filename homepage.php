<?php
session_start();

include('db.php'); // Include your database connection file

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: start.php');
    exit();
}

// Access the session data (username)
$username = $_SESSION['user']['username']; // Now we're accessing username from the session array

// Fetch user details using PDO
$sql = "SELECT * FROM Farmers WHERE username = :username LIMIT 1"; // Adjust table and fields as per your database
$stmt = $conn->prepare($sql);
$stmt->bindParam(':username', $username, PDO::PARAM_STR);
$stmt->execute();

if ($stmt->rowCount() === 1) {
    // Fetch associative array of user details
    $farmer = $stmt->fetch(PDO::FETCH_ASSOC);
    $farmer_id = $farmer['farmer_id']; // Get the farmer's unique ID from the fetched details
} else {
    echo "User not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>welcome to AgriDisaster Shield community dashboard</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link
      rel="stylesheet"
      href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
      crossorigin=""
    />
    <script
      src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
      integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
      crossorigin=""
    ></script>
    <script
      type="text/javascript"
      src="http://code.jquery.com/jquery-1.7.1.min.js"
    ></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <link rel="stylesheet" href="general.css">
    <style>
        #map {
        height: 100%;
      }
    </style>

</head>
<body>
    <div class="dashboard-container">
        <header class="dashboard-header">
            <img src="images/logo.jpg" alt="Logo" class="logo">
            <h1 class="welcome-text" id="welcome-text">Welcome, <?php echo htmlspecialchars($_SESSION['user']['username']); ?>!<br>
            </h1>
            <nav class="nav">
                
                <a href="#" id="menu-icon" class="menu-btn"><i class='bx bx-menu'></i></a> <!-- Menu icon -->
            </nav>
        </header>

        <div class="dashboard-content">
            <div class="farmer-details">
                <ul>
                    <li><a href="#" id="dashboard-link"><i class="fas fa-tachometer-alt"></i>Dashboard</a></li>
                    <li><a href="#" id="profile-link"><i class="fas fa-user"></i>My Profile</a></li>
                   <li><a href="comparision.php" id="farm-details-link"><i class="fas fa-check-circle"></i>Crop suitability checker</a></li>
                    <li><a href="#" id="weather-forecast-link"><i class="fas fa-cloud-sun-rain"></i>Weather Forecast</a></li>
                    <li><a href="#" id="farm-health-link"><i class="fas fa-seedling"></i>Farm Health</a></li>

                    <li><a href="#" id="farm-analytics-link"><i class="fas fa-wifi"></i>Sensors Records</a></li>
                    <li><a href="#" id="sensors-analytics-link"><i class="fas fa-chart-line"></i>Sensors Analytics</a></li>
                    <!-- <li><a href="#" id="market-analysis-link"><i class="fas fa-chart-pie"></i>Market Analysis</a></li> -->
                    <li><a href="#" id="notifications-link"><i class="fas fa-bell"></i>Notifications & Alerts</a></li>
                    <li><a href="#" id="recommendations-link"><i class="fas fa-lightbulb"></i>Recommendations</a></li>
                    <li><a href="#" id="community-resources-link"><i class="fas fa-users"></i>Community & Resources</a></li>
                    <li><a href="start.php" class="logout-btn">Logout</a></li>
                </ul>
            </div>

            <div class="dashboard-actions">
                <div id="dashboard-content" class="content-section" style="display: block;">
                    <div class="dashboard-text">
                        <h2 style="text-align:center;">Discover Your Farm's Growth and Performance at a Glance</h2>
                    </div>
                    <div class="dashboard-grid">
                        <div class="dashboard-item" id="section1">
                            <h3>Current Weather</h3>
                            <div id="weather-details-dashboard">Loading weather data...</div>
                        </div>
                        <div class="dashboard-item" id="section2">
                            <div class="flip-card">
                                <div class="flip-card-inner" id="flipCard">
                                    <div class="flip-card-front">
                                        <div id="sensorGaugeFront" style="width: 100%; height: 100%;"></div> <!-- Front content (Humidity) -->
                                    </div>
                                    <div class="flip-card-back">
                                        <div id="sensorGaugeBack" style="width: 100%; height: 100%;"></div> <!-- Back content (Soil Moisture) -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="dashboard-item" id="section3">
                            <div class="flip-card" id="soilMoistureAnalyticsDash">
                                
                            </div>
                        </div>
                        <div class="dashboard-item" style="background:#1B5E20;" id="section4">
                            <div id="temperatureThermometerDash"></div>
                        </div>
                        <div class="dashboard-item" id="section5">
                            <div id="waterManagementDash"></div>
                        </div>

                        <div class="dashboard-item">
                            <div id="recommendation-card" class="card">
                                <div class="card-body">
                                    <h3 id="recommendation-title"></h3>
                                    <p id="recommendation-content"></p>
                                    <a href="#" id="view-all-link" class="view-all-btn">View All Recommendations</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="profile-content" class="content-section" style="display:none;">
                    <div id="profile-wrapper">
                        <div id="profile-image-section">
                            <!-- Replace the src with the farmer's image URL -->
                             <div class="farmer-image-div">
                                <img src="images/user.png" alt="Farmer's Picture" id="farmer-image" class="farmer-image">
                             </div>
                            <div class="farmer-name">
                                <?php echo htmlspecialchars($farmer['username']); ?>
                            </div>
                        </div>
                        <div id="profile-details">
                            <div class="profile-detail-row">
                                <span class="profile-label">Phone:</span>
                                <span class="profile-value"><?php echo htmlspecialchars($farmer['phone_number']); ?></span>
                            </div>
                            <div class="profile-detail-row">
                                <span class="profile-label">Email:</span>
                                <span class="profile-value"><?php echo htmlspecialchars($farmer['email']); ?></span>
                            </div>
                            <div class="profile-detail-row">
                                <span class="profile-label">Farm Location:</span>
                                <span class="profile-value"><?php echo htmlspecialchars($farmer['farm_location']); ?></span>
                            </div>
                            <div class="profile-detail-row">
                                <span class="profile-label">Farm Size:</span>
                                <span class="profile-value"><?php echo htmlspecialchars($farmer['farm_size']); ?> acres</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!--
                <div id="farm-details-content" class="content-section" style="display:none;">
                    <h2>Farm Details</h2>
                    <ul>
                        <li><strong>Farm Location:</strong> <?php echo htmlspecialchars($farmer['farm_location']); ?></li>
                        <li><strong>Farm Size:</strong> <?php echo htmlspecialchars($farmer['farm_size']); ?> acres/hectares</li>
                        <li><strong>Crop Type:</strong> <?php echo htmlspecialchars($farmer['crop_type']); ?></li>
                    </ul>
                </div>
                -->

                <div id="weather-forecast-content" class="content-section" style="display:none;">
                    <h2 style="text-align:center">Plan Ahead with Precise Weather and Forecast Updates</h2>
                    <div id="weather-details" class="weather-info">Loading weather data...</div>
                    <button 
                        style="margin-top: 10px; padding: 10px 20px; background-color: #137517; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px;" 
                        onclick="window.location.href='myfarm.php';">
                        View my farm weather condition
                    </button>

                    <div id="forecast_and_map" class="two-box-container"><br><br>                    
                        <div id="forecast-content-card" class="forecast-content-card">
                            <div class="forecast-title">
                                <h2>Forecast</h2>
                                <h3>5days</h3>
                            </div>
                            <ul id="forecast-content" class="forecast-content">
                                <h1>Loading Data...</h1>
                            </ul>
                        </div>
                        <div id="map-content-card" class="map-content-card">
                            <div id="map">

                         </div>
                        </div>
                    </div>
                    
                </div>

                <div id="farm-health-content" class="content-section" style="display: none;">
                    <img id="nasa-farm-image" src="" alt="Farm Satellite Image" style="width:100%; height:600px; max-width:600px;">
                    <p id="nasa-image-message"></p> <!-- Message area to show status -->
                </div>

               
                <div id="notifications-content" class="content-section" style="display: none;">
                    <h2>Notifications & Alerts</h2>
                    <!-- <ul id="notifications-list"></ul> -->
                    <div class="notifications-and-alerts">
                        <div class="notifications-container">
                            <h1>World wide Notifications</h1>
                            <div id="notifications" class="notifications"></div> <!-- Container for notifications -->
                        </div>
                        <div class="alerts-container">
                            <h1>Alerts</h1>
                            <div id="alerts" class="alerts"></div>
                        </div>
                    </div>
                    <div class="notifications-and-alerts-map" id="notifications-map">
                    </div>
                </div>

                <div id="farm-analytics-content" class="content-section" style="display:none;">
                    <h2 style="text-align:center">Sensor Daily Records</h2>
                    <h3 id="date-time" style="text-align:center"></h3>
                    <div class="analytics-container">
                        <div class="sensor-analytics-records chart-container">
                                <div id="humidityGauge"></div>
                                <div id="soilMoistureGauge"></div>
                        </div>
                        <div class="sensor-analytics-records chart-container">
                            <div id="rainfallDonut"></div>
                            <div id="temperatureThermometer"></div>
                        </div>
                    
                    </div>
                </div>


                <div id="recommendations-content" class="content-section" style="display:none;">
                    <h2 style="text-align:center;">Farming Recommendations</h2>
                    <div class="recommendations-grid">
                        <div class="recommandations-item">
                            <h3>Soil Quality</h3>
                            <p>Based on recent soil data, the following recommendations can improve your crop yield:</p>
                            <ul class="recommendation-list">
                                <li>Consider adding organic compost to improve soil nutrients.</li>
                                <div class="more-content" style="display:none;">
                                    <li>pH levels indicate slightly acidic soil; apply lime to neutralize the pH.</li>
                                    <li>Ensure proper drainage to prevent waterlogging.</li>
                                </div>
                            </ul>
                            <a href="#" class="toggle-btn" data-target="soil-quality-more">Continue Reading</a>
                        </div>
                        <div class="recommandations-item">
                            <h3>Water Management</h3>
                            <p>Your recent water usage data suggests the following:</p>
                            <ul class="recommendation-list">
                                <li>Optimize drip irrigation schedules to reduce water usage by 15%.</li>
                                <div class="more-content" style="display:none;">
                                    <li>Install moisture sensors to automate irrigation and save water.</li>
                                    <li>Utilize rainwater harvesting systems during the rainy season.</li>
                                </div>
                            </ul>
                            <a href="#" class="toggle-btn" data-target="water-management-more">Continue Reading</a>
                        </div>
                        <div class="recommandations-item">
                            <h3>Crop Health</h3>
                            <p>To ensure healthy crops, we recommend the following actions:</p>
                            <ul class="recommendation-list">
                                <li>Monitor crop health regularly for signs of disease or pest infestation.</li>
                                <div class="more-content" style="display:none;">
                                    <li>Apply organic pesticides to prevent pest attacks on crops.</li>
                                    <li>Rotate crops each season to maintain soil fertility.</li>
                                </div>
                            </ul>
                            <a href="#" class="toggle-btn" data-target="crop-health-more">Continue Reading</a>
                        </div>
                        <div class="recommandations-item">
                            <h3>Weather-Based Recommendations</h3>
                            <p>Considering the upcoming weather forecasts, these are the key recommendations:</p>
                            <ul class="recommendation-list">
                                <li>Rain expected in 3 days; reduce irrigation to avoid overwatering.</li>
                                <div class="more-content" style="display:none;">
                                    <li>Protect crops from heavy rainfall by ensuring proper drainage systems.</li>
                                    <li>Consider planting drought-resistant varieties for upcoming dry spells.</li>
                                </div>
                            </ul>
                            <a href="#" class="toggle-btn" data-target="weather-based-more">Continue Reading</a>
                        </div>
                    </div>
                </div>

                <div id="community-resources-content" class="content-section" style="display:none;">
                    <h2 style="text-align:center;">Community & Resources</h2>
                    <div class="dashboard-grid">
                        <div class="community-item">
                            <div class="content">
                                <div class="content-icon">
                                    <i class="fas fa-ambulance"></i>
                                </div>
                                <h3>Disaster Response Groups</h3>
                                
                                <div class="read-more" id="disaster-response-more" style="display: none;">
                                    <ul>
                                        <li><a href="https://www.redcross.org" target="_blank">Red Cross Disaster Relief</a></li>
                                        <li><a href="https://www.ifrc.org" target="_blank">IFRC</a></li>
                                        <li><a href="https://www.wfp.org" target="_blank">World Food Programme</a></li>
                                    </ul>
                                </div>
                                <button class="read-more-btn" onclick="toggleReadMore(this,'disaster-response-more')">Read More</button>
                            </div>
                        </div>

                        <div class="community-item">
                            <div class="content">
                                <div class="content-icon">
                                    <i class="fas fa-network-wired"></i> 
                                </div>
                                <h3>Farming Communities</h3>
                                <div class="read-more" id="farming-communities-more" style="display: none;">
                                    <ul>
                                        <li><a href="https://www.agriculture.com" target="_blank">Global Farmers Network</a></li>
                                        <li><a href="https://www.yourlocalfarmers.org" target="_blank">Local Farmers Union</a></li>
                                        <li><a href="https://www.farmingfirst.org" target="_blank">Farming First</a></li>
                                    </ul>
                                </div>
                                <button class="read-more-btn" onclick="toggleReadMore(this,'farming-communities-more')">Read More</button>
                            </div>
                        </div>

                        <div class="community-item">
                            <div class="content">
                                <div class="content-icon">
                                    <i class="fas fa-user-tie"></i>
                                </div>
                                <h3> Contact Experts</h3>
                                <div class="read-more" id="expert-contacts-more" style="display: none;">
                                    <ul>
                                        <li><strong>Steve Roberts, CCM</strong><br><a href="https://www.linkedin.com/in/steve-roberts-ccm/">Steve Roberts</a></li>
                                        
                                    </ul>
                                </div>
                                <button class="read-more-btn" onclick="toggleReadMore(this, 'expert-contacts-more')">Read More</button>
                            </div>
                        </div>

                        <div class="community-item">
                            <div class="content">
                                <div class="content-icon">
                                    <i class="fas fa-book"></i> 
                                </div>
                                <h3>Educational Resources</h3>
                                <div class="read-more" id="educational-resources-more" style="display: none;">
                                    <ul>
                                        <li><a href="https://www.courses.agriculture.edu" target="_blank">Free Farming Courses</a></li>
                                        <li><a href="https://www.farmersbooks.org" target="_blank">E-books on Farming Techniques</a></li>
                                        <li><a href="https://www.youtube.com/channel/FarmingChannel" target="_blank">YouTube Farming Channel</a></li>
                                    </ul>
                                </div>
                                <button class="read-more-btn" onclick="toggleReadMore(this,'educational-resources-more')">Read More</button>
                            </div>
                        </div>

                        <div class="community-item">
                            <div class="content">
                                <div class="content-icon">
                                    <i class="fas fa-leaf"></i> 
                                </div>
                                <h3>Irrigation and Water Management Practices</h3>
                                <div class="read-more" id="sustainable-farming-more" style="display: none;">
                                    <ul>
                                        <li><a href="https://www.irrigationefficiency.com" target="_blank">Irrigation Efficiency Guide</a></li>
                                        <li><a href="https://www.permacultureprinciples.com" target="_blank">Permaculture & Water Conservation</a></li>
                                        <li><a href="https://www.no-tillfarming.org" target="_blank">No-Till Farming for Soil & Water Management</a></li>
                                        <li><a href="https://www.agroforestry.org" target="_blank">Agroforestry & Water Resource Optimization</a></li>
                                        <li><a href="https://www.irrigationdisasterresilience.com" target="_blank">Disaster-Resilient Irrigation Systems</a></li>
                                    </ul>
                                </div>
                                <button class="read-more-btn" onclick="toggleReadMore(this,'sustainable-farming-more')">Read More</button>
                            </div>
                        </div>
        
                        <div class="community-item">
                            <div class="content">
                                <div class="content-icon">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                                <h3> Financial Support for Farmers</h3>
                                <div class="read-more" id="financial-support-more" style="display: none;">
                                    <ul>
                                        <li><a href="https://www.farmersloans.gov" target="_blank">Farmers Loan Program</a></li>
                                        <li><a href="https://www.agriculturalgrants.org" target="_blank">Agricultural Grants</a></li>
                                        <li><a href="https://www.microfinanceinstitutes.com" target="_blank">Microfinance for Farmers</a></li>
                                        <li><a href="https://www.govagri.com/aid" target="_blank">Government Agricultural Aid</a></li>
                                    </ul>
                                </div>
                                <button class="read-more-btn" onclick="toggleReadMore(this,'financial-support-more')">Read More</button>
                            </div>
                        </div>
                    </div>
                </div>   

                <div id="sensors-analytics-content" class="content-section" style="display:none">
                    <h2 style="text-align:center">Sensor Analytics</h2>
                    <h3 style="text-align:center" id="last-update"></h3>
                    <div class="analytics-container">
                        <div class="sensor-analytics-records chart-container">
                            <div id="temperatureLineChart"></div>
                            <div id="humidityLineChart"></div>
                        </div>
                        <div class="sensor-analytics-records  chart-container">
                            <div id="soilMoistureBarChart"></div>
                            <div id="rainfallPieChart"></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <script>
                const africastalking = {
                    initialize: function (config) {
                        this.username = config.username;
                        this.apiKey = config.apiKey;
                        console.log("Africa's Talking initialized with username: ", this.username);
                    },
                    SMS: {
                        send: async function (options) {
                            const url = `https://api.africastalking.com/version1/messaging`;
                            const headers = new Headers({
                                'Content-Type': 'application/json',
                                'Authorization': 'Basic ' + btoa(`${this.username}:${this.apiKey}`)
                            });

                            try {
                                const response = await fetch(url, {
                                    method: 'POST',
                                    headers: headers,
                                    body: JSON.stringify({
                                        to: options.to,
                                        message: options.message,
                                        from: 'YourSenderID' // Optional: Specify a sender ID (if required)
                                    })
                                });

                                if (!response.ok) {
                                    const errorDetails = await response.json();
                                    throw new Error(`Error: ${response.status}, ${errorDetails.message}`);
                                }

                                return await response.json();
                            } catch (error) {
                                console.error('Error sending SMS:', error);
                                throw error; // Propagate error to be handled by the caller
                            }
                        }
                    }
                };

                // Initialize Africa's Talking with your credentials
                africastalking.initialize({
                    username: 'agritech_info',
                    apiKey: 'atsk_b8d45b359f3a13af7e0962ad5a343cc1e283efedd61ba13b0de4ea2f79f493ded5380661'
                });
                document.getElementById('weather-forecast-link').addEventListener('click', function () {
                    const sections = document.querySelectorAll('.content-section');
                    sections.forEach(section => section.style.display = 'none');
                    document.getElementById('weather-forecast-link').style.display = 'block';
                    fetchWeatherForecast();
                    // fetchWeatherForecast2();
                });

                document.getElementById('notifications-link').addEventListener('click', function () {
                    const sections = document.querySelectorAll('.content-section');
                    sections.forEach(section => section.style.display = 'none');
                    document.getElementById('notifications-content').style.display = 'block';
                    fetchWeatherForecast();
                    // fetchWeatherForecast2();
                });

                async function fetchWeatherForecast() {
                    
                    const apiKey = 'e42271f29595c6ba99fbbe289cb312b9'; // Your OpenWeatherMap API key
                    const lat = '0.3344113949480149'; // Latitude for your farm
                    const lon = '32.60134773998587'; // Longitude for your farm
                    const apiUrl = `https://api.openweathermap.org/data/2.5/forecast?lat=${lat}&lon=${lon}&appid=${apiKey}&units=metric`;

                    try {
                        const response = await fetch(apiUrl);
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        const data = await response.json();
                        displayWeatherForecast(data);
                        // displayWeatherForecast2(data);
                    } catch (error) {
                        console.error('Error fetching weather data:', error);
                    }
                }

                function displayWeatherForecast(data) {
                    // const notificationsList = document.getElementById('notifications-list');
                    const forecatList=document.getElementById("forecast-content");
                    forecatList.innerHTML='';
                    // notificationsList.innerHTML = ''; // Clear previous weather alerts

                    data.list.forEach(item => {
                        const date = new Date(item.dt * 1000);
                        // const date = new Date(item.dt * 1000);
                        const options = { day: 'numeric', month: 'short', weekday: 'short' };
                        const formattedDate = date.toLocaleDateString('en-US', options);
                        if (date.getHours() === 12) { // Only take forecasts for noon
                            const temp = item.main.temp.toFixed(1); // Display one decimal place
                            const description = item.weather[0].description;
                            const iconCode = item.weather[0].icon; // Get the icon code from API response
                            const iconUrl = `http://openweathermap.org/img/wn/${iconCode}@2x.png`; // Construct the icon URL

                            const weatherIcon = document.createElement('img') // Assuming you have an <img> element with id 'weather-icon'
                            weatherIcon.src = iconUrl; // Set the icon URL to the img element



                            const li = document.createElement('li');
                            li.classList.add('weather-item');
                            li.innerHTML = `
                                <img src="${iconUrl}" alt="icon">
                                <span class="temp">${temp}°C</span>
                                <span class="date">${formattedDate}</span>
                               
                            `;
                            // notificationsList.appendChild(li);
                            forecatList.appendChild(li);
                            sendSmsNotification(li.textContent); // Send SMS notification
                        }
                    });
                }

                // Function to send SMS notifications
                async function sendSmsNotification(message) {
                    const farmerPhoneNumber = '+256780393671'; // Replace with the farmer's phone number

                    const options = {
                        to: farmerPhoneNumber,
                        message: message,
                    };

                    try {
                        const response = await africastalking.SMS.send(options);
                        console.log('SMS sent:', response);
                    } catch (error) {
                        console.error('Error sending SMS:', error);
                    }
                }


                function formatDateTime() {
                    const now = new Date();
                    now.setHours(now.getHours() - 24); // Subtract 24 hours

                    const options = { hour: 'numeric', minute: 'numeric', hour12: true, weekday: 'short', day: 'numeric', month: 'short' };
                    const formattedDate = now.toLocaleString('en-US', options); // Format the date

                    // Set the formatted date and time into the h3 element with "Last Update:"
                    document.getElementById('date-time').textContent = "Last Update: " + formattedDate;
                }

                // Call the function to set the date and time
                    formatDateTime();

    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.plot.ly/plotly-latest.min.js"></script>

    <script src="myCharts.js"></script>
    <script src="event_listener_copy.js"></script>
    <script src="maps.js"></script>
    <script src="notifications.js"></script>
    
</body>
</html>
