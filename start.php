<?php
session_start();
include('databasecreation.php');  
include('db.php'); // Include your database connection file

$error = ''; // Initialize an empty error message
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare a statement to retrieve user data using PDO
    $query = "SELECT * FROM Farmers WHERE username = :username LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    // Fetch the user details
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verify if the user exists and if the password matches
    if ($user && password_verify($password, $user['password'])) {
        // Set user details in session
        $_SESSION['user'] = [
            'username' => $user['username'],
            'full_name' => $user['full_name'],
            'email' => $user['email'],
            'farm_location' => $user['farm_location'],
            'farm_size' => $user['farm_size']
        ];
        
        // Redirect to the homepage
        header("Location: homepage.php");
        exit();
    } else {
        $error = "Invalid username or password.";
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
    <title>Smart Irrigation - Login</title>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="logo">
                    <img class="logo-image" src="images/logo.jpg"" alt="AgriTech" />
            </div>
            <form action="start.php" method="POST" class="login">
                <input type="text" name="username" placeholder="Username" required><br>
                <input type="password" name="password" placeholder="Password" required><br>
                <?php if (!empty($error)) : ?>
                    <p class="error-message"><?php echo $error; ?></p>
                <?php endif; ?>
                <button type="submit">Login</button>
            </form>
            <p> Don't have an account? <a href="register.php" style="color:#28a745">Register here</a></p>
        </div>
    </div>
</body>
</html>
