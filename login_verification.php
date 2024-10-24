<?php
session_start();
include('db.php'); // Include your database connection file

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
        echo "Invalid username or password.";
    }
}
?>
