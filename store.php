<?php
include('db.php'); // Ensure your database connection is set up

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $temperature = $_POST['temperature'];
    $humidity = $_POST['humidity'];
    $moisture = $_POST['moisture'];

    // Validate the data
    if (!empty($temperature) && !empty($humidity) && !empty($moisture)) {
        // Prepare and execute the SQL query to insert data into the database
        $sql = "INSERT INTO sensor_readings (temperature, humidity, moisture, reading_time) VALUES (:temperature, :humidity, :moisture, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':temperature', $temperature);
        $stmt->bindParam(':humidity', $humidity);
        $stmt->bindParam(':moisture', $moisture);

        if ($stmt->execute()) {
            echo "Data stored successfully.";
        } else {
            echo "Failed to store data.";
        }
    } else {
        echo "Invalid input.";
    }
}
?>
