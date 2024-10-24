<?php
session_start(); // Start the session

// Include your database connection
include('db.php'); // Ensure your database connection is set up correctly.

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the incoming data
    $data = json_decode(file_get_contents('php://input'), true);
    $crops = $data['crops'];

    // Assuming you have the user ID stored in the session after login
    $userId = $_SESSION['user_id']; // or however you identify the user

    // Fetch the phone number for the logged-in user
    $stmt = $conn->prepare("SELECT phone_number FROM farmers WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $phone_number = $row['phone_number'];
    } else {
        echo json_encode(['success' => false, 'message' => 'User not found.']);
        exit();
    }

    // Send SMS using Africa's Talking API
    $apiUsername = 'agritech_info'; // Africa's Talking username
    $apiKey = 'atsk_3d1de832681583f53d7b28e4553da08d6ed56ef908cf38cad252da916e25efd5cda1cb5d'; // Your API key
    $apiUrl = 'https://api.africastalking.com/version1/messaging';

    // Format phone number starting with 0 to +256 (Ugandan format)
    if (strpos($phone_number, '0') === 0) {
        $phone_number = '+256' . substr($phone_number, 1);
    }

    // Prepare the message
    $message = "Suitable crops for your farm are: $crops";
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
    $curl_error = curl_error($ch);
    curl_close($ch);

    // Log the raw response for debugging
    file_put_contents('response_log.txt', $response . PHP_EOL, FILE_APPEND);

    // Check if the SMS was sent successfully
    $responseDecoded = json_decode($response, true);
    if (isset($responseDecoded['SMSMessageData']['Recipients']) && count($responseDecoded['SMSMessageData']['Recipients']) > 0) {
        // Respond back to the frontend with success
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
}
?>
