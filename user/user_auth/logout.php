<?php
session_start();

// Unset all session variables
$_SESSION = [];

// Destroy the session
session_destroy();

// Redirect to login page or home page
header("Location: ../../user/user_auth/loginpage.php");
exit;
?>
            <!--This is a property of PLSP-CCST BSIT-3B SY 2024-2025 -->