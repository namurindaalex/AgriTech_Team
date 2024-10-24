<?php
include('db.php'); // Ensure your database connection is set up correctly.

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $farm_location = $_POST['farm_location'];
    $farm_size = $_POST['farm_size'];

    // Save registration data in session temporarily
    $_SESSION['registration_data'] = [
        'username' => $username,
        'password' => $password, // Will be hashed after OTP verification
        'email' => $email,
        'phone_number' => $phone_number,
        'farm_location' => $farm_location,
        'farm_size' => $farm_size
    ];

    // Generate a random 6-digit OTP
    $otp = rand(100000, 999999);

    // Store OTP in session for verification later
    $_SESSION['otp'] = $otp;

    // Send OTP via SMS using cURL
    $apiUsername = 'agritech_info'; // Africa's Talking username
    $apiKey = 'atsk_3d1de832681583f53d7b28e4553da08d6ed56ef908cf38cad252da916e25efd5cda1cb5d'; // Your API key
    $apiUrl = 'https://api.africastalking.com/version1/messaging';

    // Format phone number starting with 0 to +256 (Ugandan format)
    if (strpos($phone_number, '0') === 0) {
        $phone_number = '+256' . substr($phone_number, 1);
    }

    // Prepare the message
    $message = "Your OTP code is: $otp";
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
        // Redirect to OTP verification page
        header("Location: otp_verification.php");
        exit();
    } else {
        header("Location: otp_verification.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="login.css">
    <title>Smart Irrigation - Register</title>
</head>
<body>
    <!-- Registration Form -->
    <div id="registerForm" class="container">
        <div class="login-card register-card">
            <div class="logo">
                <img class="logo-image" src="images/logo.jpg" alt="AgriTech" />
            </div>
            <h3>New Farmer Registration</h3>
            <form method="POST" action="register.php" class="login">
                <div class="form-group">
                    <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                </div>
                <div class="form-group">
                    <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" id="phone_number" name="phone_number" placeholder="Phone Number" required>
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" id="farm_location" name="farm_location" placeholder="Farm Location" required>
                </div>
                <div class="form-group">
                    <input type="number" class="form-control" id="farm_size" name="farm_size" placeholder="Farm Size [in acres]" required>
                </div>
                <button type="submit" class="btn-login">Register</button>
            </form>
            <p>Already have an account?<a href="start.php" style="color:#28a745"> Login here</a></p>
        </div>
    </div>
</body>
</html>
