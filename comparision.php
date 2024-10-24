<?php
include('db.php'); // Ensure your database connection is set up correctly.

session_start();

if (!isset($_SESSION['user'])) {
    header('Location: start.php');
    exit();
}

// Fetch the latest weather data from ThingSpeak
$THINGSPEAK_CHANNEL_ID = '2683924';
$THINGSPEAK_API_KEY = 'AQTTKKHPO8II9UQ3';
$THINGSPEAK_READ_URL = "https://api.thingspeak.com/channels/$THINGSPEAK_CHANNEL_ID/feeds.json?api_key=$THINGSPEAK_API_KEY&results=1";

$weatherData = json_decode(file_get_contents($THINGSPEAK_READ_URL), true);
$currentFeed = $weatherData['feeds'][0];

// Extract data from the response
$currentTemp = $currentFeed['field1'] ?? ''; // Assuming field1 is for temperature
$currentHumidity = $currentFeed['field2'] ?? ''; // Assuming field2 is for humidity
$currentMoisture = $currentFeed['field3'] ?? ''; // Assuming field3 is for soil moisture

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data
    $username = $_SESSION['user']['username']; // Now we're accessing username from the session array

    // Fetch user details including phone number using PDO
    $sql = "SELECT farmer_id, phone_number FROM farmers WHERE username = :username LIMIT 1"; // Adjust table and fields as per your database
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() === 1) {
        // Fetch associative array of user details
        $farmer = $stmt->fetch(PDO::FETCH_ASSOC);
        $farmer_id = $farmer['farmer_id']; // Get the farmer's unique ID
        $phone_number = $farmer['phone_number']; // Get the farmer's phone number
    } else {
        echo "User not found.";
        exit();
    }
    
    $message = isset($_POST['comparisonResult']) ? $_POST['comparisonResult'] : 'No comparison result.';   
   
    // Send OTP via SMS using cURL
    $apiUsername = 'agritech_info'; // Africa's Talking username
    $apiKey = 'atsk_3d1de832681583f53d7b28e4553da08d6ed56ef908cf38cad252da916e25efd5cda1cb5d'; // Your API key
    $apiUrl = 'https://api.africastalking.com/version1/messaging';

    // Format phone number starting with 0 to +256 (Ugandan format)
    if (strpos($phone_number, '0') === 0) {
        $phone_number = '+256' . substr($phone_number, 1);
    }

    // Prepare the message
    $to = $phone_number;

    // Create the data for the POST request
    $postData = [
        'username' => $apiUsername,
        'to' => $to,
        'message' => $message
    ];

    // Set up the cURL request
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'apiKey: ' . $apiKey,
        'Content-Type: application/x-www-form-urlencoded'
    ]);

    // Execute the request and get the response
    $response = curl_exec($ch);
    curl_close($ch);

    // Handle the response
    if ($response === true) {
        echo "Message sent successfully to $phone_number.";
    } else {
        $responseDecoded = json_decode($response, true);
        if (isset($responseDecoded['SMSMessageData']['Recipients']) && count($responseDecoded['SMSMessageData']['Recipients']) > 0) {
            echo "Message sent successfully to $phone_number.";
        } else {
            $errorMessage = isset($responseDecoded['SMSMessageData']['Message']) ? $responseDecoded['SMSMessageData']['Message'] : "Unknown error";
            //echo "Message sending failed: $errorMessage";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crop Suitability Checker</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap');
        * {
           
            box-sizing: border-box;
            font-family: 'Poppins',Arial, Helvetica, sans-serif;
        }
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #6df28a, #027c49);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            width:100%;

        }

        #contentdiv {
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
            height: auto;
            width: 400px; /* Fixed width for better alignment */
            
        }
        h1 {
         
            color: #4CAF50; /* Dark Green */
            margin-bottom: 30px;
            font-size: 2.5em; /* Larger font size */
        }
        form {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            width: 100%;
        }
        label {
            margin: 15px 0 5px;
            display: block;
            font-weight: bold;
            color: #333;
        }
        input {
            margin: 10px 0 20px; /* Spacing for better alignment */
            padding: 10px;
            width: calc(100% - 22px); /* Adjust width to account for padding */
            border: 2px solid #4CAF50; /* Green border */
            border-radius: 5px;
            font-size: 1em; /* Increase font size */
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em; /* Increase font size */
            width: 100%; /* Full width */
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #45a049; /* Darker green */
        }
        .results {
            margin-top: 20px;
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            display: none; /* Initially hidden */
        }
        ul {
            padding-left: 20px; /* Indentation for list */
        }
        li {
            color: #333; /* Darker text color for list items */
            margin: 5px 0; /* Spacing between items */
        }
        .form-crop{
            display:flex;
            flex-direction:column;
            align-items: center;
        }
    </style>
</head>
<body>
    <div class="contentdiv">
        <h1 style="color:#1B5E20; text-align:center;color:#fff">Crop Suitability Checker</h1>
        <form id="cropForm" method="POST" class="form-crop">
            <label for="temperature">Temperature [°C]:</label>
            <input type="number" id="temperature" name="temperature" value="<?= htmlspecialchars($currentTemp) ?>" readonly required>

            <label for="humidity">Humidity [%]:</label>
            <input type="number" id="humidity" name="humidity" value="<?= htmlspecialchars($currentHumidity) ?>" readonly required>

            <label for="moisture">Soil Moisture [%]:</label>
            <input type="number" id="moisture" name="moisture" value="<?= htmlspecialchars($currentMoisture) ?>" readonly required>

            <input type="hidden" id="comparisonResult" name="comparisonResult">

            <button type="submit" style="background:#1B5E20;">Check Suitable Crops</button>
        </form>

        <div class="results" id="results">
            <h2>Suitable Crops:</h2>
            <ul id="suitableCropsList"></ul>
        </div>
    </div>

    <script>
    class Crop {
        constructor(name, optimalTemperature, optimalHumidity, optimalSoilMoisture) {
            this.name = name;
            this.optimalTemperature = optimalTemperature; // {min: value, max: value}
            this.optimalHumidity = optimalHumidity;       // {min: value, max: value}
            this.optimalSoilMoisture = optimalSoilMoisture; // {min: value, max: value}
        }

        isSuitable(currentTemp, currentHumidity, currentMoisture) {
            return (currentTemp >= this.optimalTemperature.min && currentTemp <= this.optimalTemperature.max) &&
                   (currentHumidity >= this.optimalHumidity.min && currentHumidity <= this.optimalHumidity.max) &&
                   (currentMoisture >= this.optimalSoilMoisture.min && currentMoisture <= this.optimalSoilMoisture.max);
        }
    }

    const crops = [
        new Crop("Corn", {min: 20, max: 50}, {min: 60, max: 80}, {min: 20, max: 40}),
        new Crop("Wheat", {min: 10, max: 30}, {min: 50, max: 70}, {min: 10, max: 20}),
        new Crop("Rice", {min: 20, max: 35}, {min: 70, max: 90}, {min: 25, max: 80}),
        new Crop("Coffee", {min: 15, max: 30}, {min: 60, max: 70}, {min: 15, max: 25}),
        new Crop("Tomatoes", {min: 20, max: 30}, {min: 50, max: 70}, {min: 10, max: 70}),
        new Crop("Potatoes", {min: 15, max: 20}, {min: 60, max: 80}, {min: 15, max: 25}),
        new Crop("Lettuce", {min: 15, max: 25}, {min: 70, max: 80}, {min: 20, max: 30}),
        new Crop("Cucumbers", {min: 18, max: 30}, {min: 60, max: 80}, {min: 15, max: 30}),
        new Crop("Soya", {min: 18, max: 35}, {min: 60, max: 80}, {min: 15, max: 30}),
    ];

    document.getElementById('cropForm').onsubmit = function(event) {
        event.preventDefault(); // Prevent the form from submitting normally

        const temperature = parseFloat(document.getElementById('temperature').value);
        const humidity = parseFloat(document.getElementById('humidity').value);
        const moisture = parseFloat(document.getElementById('moisture').value);

        const suitableCrops = crops.filter(crop => crop.isSuitable(temperature, humidity, moisture));

        const resultsDiv = document.getElementById('results');
        const cropsList = document.getElementById('suitableCropsList');

        cropsList.innerHTML = ''; // Clear previous results

        if (suitableCrops.length > 0) {
            suitableCrops.forEach(crop => {
                const li = document.createElement('li');
                li.textContent = crop.name;
                cropsList.appendChild(li);
            });
            resultsDiv.style.display = 'block'; // Show the results

            const cropNames = suitableCrops.map(crop => crop.name).join(', ');
            document.getElementById('comparisonResult').value = `Basing on the current weather, you are recommended to grow: ${cropNames}.`;
        } else {
            const li = document.createElement('li');
            li.textContent = 'No suitable crops found.';
            cropsList.appendChild(li);
            resultsDiv.style.display = 'block'; // Show the results
            document.getElementById('comparisonResult').value = 'No suitable crops found.';
        }

        this.submit(); // Now submit the form to the server
    }
</script>
</body>
</html>
