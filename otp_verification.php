<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_otp = $_POST['otp'];

    // Check if entered OTP matches the one stored in session
    if ($user_otp == $_SESSION['otp']) {
        // OTP is correct, proceed with registration
        include('db.php');

        // Retrieve registration data from session
        $registration_data = $_SESSION['registration_data'];
        $username = $registration_data['username'];
        $password = password_hash($registration_data['password'], PASSWORD_DEFAULT);
        $email = $registration_data['email'];
        $phone_number = $registration_data['phone_number'];
        $farm_location = $registration_data['farm_location'];
        $farm_size = $registration_data['farm_size'];

        // Insert the new farmer's details into the database
        $query = "INSERT INTO Farmers (username, password, email, phone_number, farm_location, farm_size)
                  VALUES (:username, :password, :email, :phone_number, :farm_location, :farm_size)";
        $stmt = $conn->prepare($query);

        if ($stmt->execute([
            ':username' => $username,
            ':password' => $password,
            ':email' => $email,
            ':phone_number' => $phone_number,
            ':farm_location' => $farm_location,
            ':farm_size' => $farm_size
        ])) {
            echo "Registration successful! You can now log in.";
            // Redirect to login page
            header('Location: start.php');
            exit();
        } else {
            echo "Registration failed. Please try again.";
        }
    } else {
        echo "Invalid OTP. Please try again.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification</title>
    <style>
       body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', Arial, sans-serif;
            background: linear-gradient(135deg, #6df28a, #027c49);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .otp-container {
            background-color: #fff; /* White background for the form */
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 300px; /* Set a fixed width */
        }

        label {
            margin-bottom: 10px;
            font-size: 1.2em; /* Larger font for the label */
            color: #333; /* Dark text color */
        }

        input[type="text"] {
            width: 90%; /* Full width input */
            padding: 10px;
            border: 1px solid #ccc; /* Light border */
            border-radius: 5px;
            margin-bottom: 20px; /* Space below the input */
            font-size: 1em; /* Font size for the input */
        }

        button {
            background-color: #4CAF50; /* Green button */
            color: white; /* White text color */
            border: none; /* Remove border */
            border-radius: 5px;
            padding: 10px 15px; /* Padding for the button */
            margin-left: 30%;
            font-size: 1em; /* Font size for the button */
            cursor: pointer; /* Pointer cursor on hover */
            transition: background-color 0.3s; /* Smooth transition */
        }

        button:hover {
            background-color: #45a049; /* Darker green on hover */
        }
    </style>
</head>
<body>
    <!-- OTP Verification Form -->
    <div class="otp-container">
        <form method="POST" action="">
            <label for="otp">Enter OTP:</label><br><br>
            <input type="text" name="otp" id="otp" required>
            <button type="submit">Verify OTP</button>
        </form>
    </div>
</body>
</html>

