<?php
session_start();
include('db.php'); // Include your database connection file

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: start.php');
    exit();
}

// Access the session data (username)
$username = $_SESSION['user']['username'];

// Fetch user details using PDO
$sql = "SELECT farm_location FROM farmers WHERE username = :username LIMIT 1"; 
$stmt = $conn->prepare($sql);
$stmt->bindParam(':username', $username, PDO::PARAM_STR);
$stmt->execute();

if ($stmt->rowCount() === 1) {
    $farmer = $stmt->fetch(PDO::FETCH_ASSOC);
    $farm_location = $farmer['farm_location'];
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
    <title>my farm details</title>
    <link rel="stylesheet" href="general.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        /* Basic reset for body and elements */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9; /* Light background for contrast */
            color: #333;
        }

        /* Main container */
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        /* Header styles */
        header {
            text-align: center;
            background-color: #4CAF50; /* Green header */
            color: white;
            padding: 15px;
            border-radius: 8px;
        }

        /* Weather display */
        #weather-details {
            margin: 20px 0;
            padding: 15px;
            border-radius: 8px;
            display: flex;
            flex-direction: column; /* Stack items vertically */
            align-items: center; /* Center items */
        }

        .weather-info {
            display: flex;
            align-items: center; /* Align items vertically */
            margin-bottom: 10px;
            padding: 10px; /* Add padding to each section */
            border-radius: 8px;
            width: 80%; /* Full width for each section */
            margin-left: 4%;
        }

        .weather-info img{
            width: 60px;
            height: auto;
            
        }

        /* Section backgrounds - customize as needed */
        .temp { background-color: #e0f7fa; } /* Light cyan */
        .condition { background-color: #ffe0b2; } /* Light orange */
        .humidity { background-color: #f1f8e9; } /* Light green */
        .wind { background-color: #e8eaf6; } /* Light purple */

    

        /* Recommendations section */
        #recommendations {
            margin-top: 20px;
            padding: 15px;
            border-radius: 8px;
            background-color: #fff3cd; /* Light yellow background for recommendations */
            border: 1px solid #ffeeba;
        }

        /* Responsive styles */
        @media (max-width: 600px) {
            .container {
                padding: 10px;
            }

            .weather-info {
                flex-direction: column; /* Stack items vertically on small screens */
                align-items: flex-start; /* Align items to the left */
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Monitor Your Farm from Anywhere</h1>
            <h2 style="color:#e0f7fa;">Farm Location: <?php echo htmlspecialchars($farm_location); ?></h2>
        </header>
        <div id="weather-details">
            Loading weather data...
        </div>
        <div id="recommendations">
            <strong>Recommendations:</strong>
            <p id="weather-recommendation">Based on your weather conditions...</p>
        </div>
    </div>

    <script>
        // Fetch weather data function
        function fetchWeatherData() {
            const farmLocation = "<?php echo addslashes($farm_location); ?>"; // Ensure safe usage in JS
            const apiKey = 'e42271f29595c6ba99fbbe289cb312b9'; // Consider moving this to a secure location
            const apiUrl = `https://api.openweathermap.org/data/2.5/weather?q=${farmLocation}&appid=${apiKey}&units=metric`;

            const weatherDetails = document.getElementById('weather-details');
            const recommendationElement = document.getElementById('weather-recommendation');
            weatherDetails.innerHTML = 'Loading weather data...'; // Feedback while loading

            fetch(apiUrl)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log(data); // Debug: Check what data we get from the API
                    const weatherIconUrl = `https://openweathermap.org/img/wn/${data.weather[0].icon}@2x.png`; // Get weather icon URL
                    weatherDetails.innerHTML = `
                        <div class="weather-info temp">
                            <img src="images/temp.png" alt="icon">
                            <div>
                                <strong>Temperature:</strong> ${data.main.temp}°C 
                            </div>
                        </div>
                        <div class="weather-info condition">
                            <img src="images/condition.png" alt="Condition icon">
                            <div>
                                <strong>Condition:</strong> ${data.weather[0].description} 
                            </div>
                        </div>
                        <div class="weather-info humidity">
                            <img src="images/humid.png" alt="Humidity icon">
                            <div>
                                <strong>Humidity:</strong> ${data.main.humidity}% 
                            </div>
                        </div>
                        <div class="weather-info wind">
                            <img src="images/wind.png" alt="Wind icon">
                            <div>
                                <strong>Wind:</strong> ${data.wind.speed} km/h 
                            </div>
                        </div>
                    `;

                    // Generate recommendations based on the weather condition
                    generateRecommendations(data.weather[0].description);
                })
                .catch(error => {
                    weatherDetails.innerHTML = 'Error fetching weather data.';
                    console.error('Error:', error);
                });
        }

        // Generate recommendations based on weather conditions

        // Extended recommendation logic for gardening
        function generateRecommendations(condition) {
            const recommendationElement = document.getElementById('weather-recommendation');
            let recommendation;

            if (condition.includes("thunderstorm")) {
                recommendation = "A thunderstorm is expected! Ensure all tools are secured and consider delaying any outdoor planting until the storm passes to avoid soil erosion.";
            } else if (condition.includes("drizzle")) {
                recommendation = "Light drizzle is occurring. This is a great time for seedlings to absorb moisture. Avoid fertilizing today to prevent runoff.";
            } else if (condition.includes("rain")) {
                recommendation = "It's raining! This is beneficial for your garden, but ensure that any newly planted seedlings are not waterlogged. Check drainage around your plants.";
            } else if (condition.includes("snow")) {
                recommendation = "Snow is expected. If your garden has delicate plants, cover them with mulch or protective cloth to prevent frost damage.";
            } else if (condition.includes("mist")) {
                recommendation = "Mist is present. While it's generally good for plants, ensure that any sensitive crops are protected from potential fungal diseases.";
            } else if (condition.includes("fog")) {
                recommendation = "Fog can hinder visibility but provides moisture to plants. Monitor your garden closely for signs of rot or fungal growth.";
            } else if (condition.includes("sand")) {
                recommendation = "A sandstorm is possible. Protect your plants with windbreaks or tarps, and ensure your garden beds are well-mulched to prevent soil erosion.";
            } else if (condition.includes("smoke")) {
                recommendation = "Smoke in the area can affect air quality. If you have sensitive plants, consider using shade cloths to protect them from excessive heat.";
            } else if (condition.includes("haze")) {
                recommendation = "Hazy conditions may indicate poor air quality. Limit outdoor activities and check your plants for any signs of stress.";
            } else if (condition.includes("clear")) {
                recommendation = "It's a clear day! Ideal for planting and weeding. Consider sowing seeds or planting new crops today.";
            } else if (condition.includes("cloud")) {
                recommendation = "Cloudy weather can be beneficial for planting as it reduces heat stress on young plants. Great time for transplanting!";
            } else if (condition.includes("partly cloudy")) {
                recommendation = "Partly cloudy skies offer a nice balance for gardening. Perfect for harvesting and preparing the soil for planting.";
            } else if (condition.includes("blizzard")) {
                recommendation = "A blizzard is coming! Ensure that your garden beds are protected, and consider using row covers for any remaining crops.";
            } else if (condition.includes("tornado")) {
                recommendation = "A tornado warning is in effect! Secure all garden equipment and seek shelter immediately. Protect your plants from wind damage afterward.";
            } else {
                recommendation = "Conditions are normal. Keep monitoring the weather.";
            }

            recommendationElement.textContent = recommendation;
        }


        // Call the function to fetch weather data when the page loads
        document.addEventListener('DOMContentLoaded', fetchWeatherData);
    </script>
</body>
</html>
