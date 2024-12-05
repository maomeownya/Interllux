<?php
session_start();
include '../../database/dbconnect.php';

try {
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}

if (isset($_POST['signIn'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM admin WHERE email = :email AND password = :password");
    $stmt->execute(['email' => $email, 'password' => $password]);
    $user = $stmt->fetch();

    if ($user) {
        // Start session and redirect to index.php if email and password are correct
        session_start();
        $_SESSION['admin_id'] = $user['admin_id'];
        header("Location: ../dashboard.php");
        exit();  // Ensure no further code is executed after redirection
    } else {
        // Either email or password is incorrect
        echo "Invalid email or password.";
    }
}
?>
<!--This is a property of PLSP-CCST BSIT-3B SY 2024-2025 -->
