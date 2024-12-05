<?php
session_start();

include '../../database/dbconnect.php';
try {
    // Initialize PDO connection
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Set error mode to exceptions
} catch (PDOException $e) {
    // Handle connection errors
    echo "Connection failed: " . $e->getMessage();
    exit();
}

if (isset($_POST['signIn'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Query to check if the email and password match in the users table
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email AND password = :password");
    $stmt->execute(['email' => $email, 'password' => $password]);
    $user = $stmt->fetch();

    // Verify email and password
    if ($user) {
        // Start session and redirect to index.php if email and password are correct
        session_start();
        $_SESSION['id'] = $user['id'];
        header("Location: index.php");
        exit();  // Ensure no further code is executed after redirection
    } else {
        // Either email or password is incorrect
        echo "Invalid email or password.";
    }
}
?>
            <!--This is a property of PLSP-CCST BSIT-3B SY 2024-2025 -->