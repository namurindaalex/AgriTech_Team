<?php
// Read the variables sent via POST from our API
$sessionId   = $_POST["sessionId"];
$serviceCode = $_POST["serviceCode"];
$phoneNumber = $_POST["phoneNumber"];
$text        = $_POST["text"];

if ($text == "") {
    // This is the first request. Note how we start the response with CON
    $response  = "CON Welcome to the Weather Service. What would you like to check? \n";
    $response .= "1. Current Weather \n";
    $response .= "2. Weather Forecast \n";
    $response .= "3. Weather Alerts \n";
    $response .= "4. Contact Disaster Response Groups \n";
    $response .= "5. My Account Info";

} else if ($text == "1") {
    // Business logic for current weather
    $response = "CON Please enter your location to get the current weather:";

} else if ($text == "2") {
    // Business logic for weather forecast
    $response = "CON Please enter your location to get the weather forecast:";

} else if ($text == "3") {
    // Business logic for weather alerts
    $response = "CON Choose the location for weather alerts: \n";
    $response .= "1. Location A \n";
    $response .= "2. Location B \n";

} else if ($text == "4") {
    // Business logic for contacting disaster response groups
    $response = "CON Choose a disaster response group to contact: \n";
    $response .= "1. Red Cross \n";
    $response .= "2. Local Emergency Services \n";

} else if ($text == "5") {
    // Business logic for account information
    $response = "CON Choose account information you want to view: \n";
    $response .= "1. My Account Number \n";
    $response .= "2. My Phone Number \n";

} else if ($text == "1*1") { 
    // This is a second level response for current weather
    // Here you would typically call a weather API to get actual data
    $currentWeather = "Sunny, 25°C"; // Example response
    $response = "END The current weather is ".$currentWeather.".";

} else if ($text == "2*1") {
    // This is a second level response for weather forecast
    // Here you would typically call a weather API to get actual data
    $forecast = "Tomorrow: Cloudy, 22°C"; // Example response
    $response = "END The weather forecast is: ".$forecast.".";

} else if ($text == "3*1") {
    // This is a terminal request for weather alerts for Location A
    $alert = "Severe Thunderstorm Warning in Location A."; // Example alert
    $response = "END Alert: ".$alert;

} else if ($text == "3*2") {
    // This is a terminal request for weather alerts for Location B
    $alert = "Flood Warning in Location B."; // Example alert
    $response = "END Alert: ".$alert;

} else if ($text == "4*1") {
    // This is a second level response for contacting Red Cross
    $redCrossContact = "Contact Red Cross: 0800-123-456"; // Example contact
    $response = "END To contact the Red Cross, call ".$redCrossContact.".";

} else if ($text == "4*2") {
    // This is a second level response for contacting Local Emergency Services
    $localEmergencyContact = "Contact Local Emergency Services: 112"; // Example contact
    $response = "END To contact Local Emergency Services, call ".$localEmergencyContact.".";

} else if ($text == "5*1") { 
    // This is a second level response for account number
    $accountNumber  = "ACC1001"; // Example account number
    $response = "END Your account number is ".$accountNumber;

} else if ($text == "5*2") {
    // This is a second level response for phone number
    $response = "END Your phone number is ".$phoneNumber;
}

// Echo the response back to the API
header('Content-type: text/plain');
echo $response;
