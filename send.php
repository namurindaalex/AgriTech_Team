<?php
include('db.php'); // Ensure your database connection is set up correctly.
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: start.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Fetch user details including phone number using PDO
    $username = $_SESSION['user']['username']; // Accessing username from session

    $sql = "SELECT phone_number FROM farmers WHERE username = :username LIMIT 1"; // Adjust table and fields as per your database
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() === 1) {
        // Fetch associative array of user details
        $farmer = $stmt->fetch(PDO::FETCH_ASSOC);
        $phone_number = $farmer['phone_number']; // Get the farmer's phone number
    } else {
        echo "User not found.";
        exit();
    }

    // Get the message from the POST request
    $message = isset($_POST['message']) ? $_POST['message'] : 'No suitable crops found.';

    // Send SMS using Africa's Talking API
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
    if ($response !== false) {
        $responseDecoded = json_decode($response, true);
        if (isset($responseDecoded['SMSMessageData']['Recipients']) && count($responseDecoded['SMSMessageData']['Recipients']) > 0) {
            echo "Message sent successfully to $phone_number.";
        } else {
            echo "Message sending failed.";
        }
    } else {
        echo "cURL error: " . curl_error($ch);
    }
}
?>
