<?php
include('db.php'); // Ensure your database connection is set up correctly.

session_start();

if (!isset($_SESSION['user'])) {
    header('Location: start.php');
    exit();
}

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
   
    // Send OTP via SMS using cURL (Your API logic remains unchanged here)
    $apiUsername = 'agritech_info';
    $apiKey = 'atsk_3d1de832681583f53d7b28e4553da08d6ed56ef908cf38cad252da916e25efd5cda1cb5d'; // Ensure this is securely stored
    $apiUrl = 'https://api.africastalking.com/version1/messaging';

    // Format phone number
    if (strpos($phone_number, '0') === 0) {
        $phone_number = '+256' . substr($phone_number, 1);
    }

    $to = $phone_number;

    $postData = [
        'username' => $apiUsername,
        'to' => $to,
        'message' => $message
    ];

    // cURL request setup
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'apiKey: ' . $apiKey,
        'Content-Type: application/x-www-form-urlencoded'
    ]);

    $response = curl_exec($ch);
    $curl_error = curl_error($ch);
    curl_close($ch);

    file_put_contents('response_log.txt', $response . PHP_EOL, FILE_APPEND);

    // Handle the response
    if ($response === true) {
        echo "Message sent successfully to $phone_number.";
    } else {
        $responseDecoded = json_decode($response, true);

        if (isset($responseDecoded['SMSMessageData']['Recipients']) && count($responseDecoded['SMSMessageData']['Recipients']) > 0) {
            echo "Message sent successfully to $phone_number.";
        } else {
            $errorMessage = isset($responseDecoded['SMSMessageData']['Message']) ? $responseDecoded['SMSMessageData']['Message'] : "Unknown error";
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
        body {
            font-family: Arial, sans-serif;
            background-color: #e9f5e9;
            padding: 40px;
            margin: 0;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        h1 {
            text-align: left;
            color: #4CAF50;
            margin-bottom: 30px;
            font-size: 3em;
        }
        form {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            width: 500px;
        }
        .results {
            margin-top: 20px;
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        ul {
            padding-left: 20px;
            font-size: 1.5em;
        }
        li {
            color: #333;
            margin: 10px 0;
        }
        span {
            font-weight: bold;
            font-size: 1.8em;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 15px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.5em;
            width: 100%;
            margin-top: 20px;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

<h1>Crop Suitability Checker</h1>

<form id="cropForm" method="POST">
    <input type="hidden" id="comparisonResult" name="comparisonResult">

    <div class="results" id="currentValues">
        <h3>Current Conditions:</h3>
        <ul>
            <li>Temperature: <span id="temperature"></span>°C</li>
            <li>Humidity: <span id="humidity"></span>%</li>
            <li>Soil Moisture: <span id="moisture"></span>%</li>
        </ul>
    </div>

    <button type="submit">Check Suitable Crops</button>
</form>

<div class="results" id="results">
    <h2>Suitable Crops:</h2>
    <ul id="suitableCropsList"></ul>
</div>

<script>
    // ThingSpeak API parameters
    const THINGSPEAK_CHANNEL_ID = '2683924';
    const THINGSPEAK_API_KEY = 'AQTTKKHPO8II9UQ3';
    const THINGSPEAK_READ_URL = `https://api.thingspeak.com/channels/${THINGSPEAK_CHANNEL_ID}/feeds.json?api_key=${THINGSPEAK_API_KEY}&results=1`;

    // Function to fetch data from ThingSpeak
    function fetchAndDisplayData() {
        fetch(THINGSPEAK_READ_URL)
            .then(response => response.json())
            .then(data => {
                const latestFeed = data.feeds[0];
                const temperature = latestFeed.field1; // Adjust fields as per ThingSpeak data
                const humidity = latestFeed.field2;
                const moisture = latestFeed.field3;

                // Update the UI with the new values
                document.getElementById('temperature').textContent = temperature;
                document.getElementById('humidity').textContent = humidity;
                document.getElementById('moisture').textContent = moisture;

                // Store the data in the database
                storeDataInDatabase(temperature, humidity, moisture);
            })
            .catch(error => console.error('Error fetching data from ThingSpeak:', error));
    }

    // Function to send data to PHP for database storage
    function storeDataInDatabase(temperature, humidity, moisture) {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'store_readings.php', true); // The PHP file that will handle storing data
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                console.log('Data stored successfully:', xhr.responseText);
            }
        };
        xhr.send(`temperature=${temperature}&humidity=${humidity}&moisture=${moisture}`);
    }

    // Fetch data every 10 seconds
    setInterval(fetchAndDisplayData, 10000);

    // Fetch the initial data when the page loads
    window.onload = fetchAndDisplayData;
</script>

</body>
</html>
