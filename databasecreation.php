<?php
// Database connection details
$db_host = 'localhost';
$db_user = 'root';
$db_password = '';
$db_name = 'smart_irrigation';

try {
    // Connect to MySQL server without specifying a database
    $pdo = new PDO("mysql:host=$db_host;", $db_user, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if the database exists, if not, create it
    $createDbQuery = "CREATE DATABASE IF NOT EXISTS $db_name";
    $pdo->exec($createDbQuery);

    // Connect to the newly created database
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

   // Create the "Farmers" table if it does not exist
    $createFarmersTableQuery = "
    CREATE TABLE IF NOT EXISTS Farmers (
        farmer_id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(100),
        phone_number VARCHAR(15),
        farm_location VARCHAR(255),  -- Location description (can be coordinates or address)
        farm_size FLOAT,              -- Size of the farm in acres/hectares
        registered_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP     
    ) ENGINE=InnoDB;
    ";
    $pdo->exec($createFarmersTableQuery);


    // Create the "Sensors" table if it does not exist
    $createSensorsTableQuery = "
    CREATE TABLE IF NOT EXISTS Sensors (
        sensor_id INT AUTO_INCREMENT PRIMARY KEY,
        farmer_id INT,
        sensor_type VARCHAR(50),      
        installation_date DATE,
        sensor_location VARCHAR(255), -- Description or coordinates of where the sensor is installed
        FOREIGN KEY (farmer_id) REFERENCES Farmers(farmer_id) ON DELETE CASCADE
    ) ENGINE=InnoDB;
    ";
    $pdo->exec($createSensorsTableQuery);

   // Create the "weather_data" table if it does not exist
        $createWeatherDataTableQuery = "
        CREATE TABLE IF NOT EXISTS weather_data (
            id INT AUTO_INCREMENT PRIMARY KEY,
            temperature FLOAT NOT NULL,         
            humidity FLOAT NOT NULL,          
            moisture FLOAT NOT NULL,             
            recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
        ) ENGINE=InnoDB;
        ";
        $pdo->exec($createWeatherDataTableQuery);

    // Create the "satellite_data" table if it does not exist
    $createsatellite_dataTableQuery = "
    CREATE TABLE IF NOT EXISTS satellite_data (
        data_id INT AUTO_INCREMENT PRIMARY KEY,
        farmer_id INT,
        soil_moisture FLOAT,          -- Soil moisture percentage from satellite
        vegetation_health FLOAT,      -- Vegetation health index (e.g., NDVI)
        data_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,  -- Time when the satellite data was fetched
        FOREIGN KEY (farmer_id) REFERENCES Farmers(farmer_id) ON DELETE CASCADE
    ) ENGINE=InnoDB;
    ";
    $pdo->exec($createsatellite_dataTableQuery);

    // Create the "Recommendations" table if it does not exist
    $createRecommendationsTableQuery = "
    CREATE TABLE IF NOT EXISTS Recommendations (
        recommendation_id INT AUTO_INCREMENT PRIMARY KEY,
        farmer_id INT,
        water_needed FLOAT,           -- Amount of water needed for the field (in liters or gallons)
        recommendation_text TEXT,     -- Text description of the recommendation 
        recommendation_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,  -- When the recommendation was generated
        FOREIGN KEY (farmer_id) REFERENCES Farmers(farmer_id) ON DELETE CASCADE
    ) ENGINE=InnoDB;
    ";
    $pdo->exec($createRecommendationsTableQuery);

    // Create the "Notifications" table if it does not exist
    $createNotificationsTableQuery = "
    CREATE TABLE IF NOT EXISTS Notifications (
        notification_id INT AUTO_INCREMENT PRIMARY KEY,
        farmer_id INT,
        message TEXT,                 -- Notification message
        status ENUM('unread', 'read') DEFAULT 'unread',  -- Status of the notification
        sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (farmer_id) REFERENCES Farmers(farmer_id) ON DELETE CASCADE
    ) ENGINE=InnoDB;
    ";
    $pdo->exec($createNotificationsTableQuery);

    // Create the "admin" table if it does not exist
    $createadminTableQuery = "
    CREATE TABLE IF NOT EXISTS admin (
        admin_id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(100)
    ) ENGINE=InnoDB;
    ";
    $pdo->exec($createadminTableQuery);

} catch (PDOException $e) {
    die("ERROR: Could not connect. " . $e->getMessage());
}
?>
