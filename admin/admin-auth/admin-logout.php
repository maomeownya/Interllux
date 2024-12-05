<?php
session_start();
session_unset();
session_destroy();
header("Location: ./admin-login.php");
exit();
?>
<!--This is a property of PLSP-CCST BSIT-3B SY 2024-2025 -->
