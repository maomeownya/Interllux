<?php
// Database connection
include '../../database/dbconnect.php';

try {
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}

// Function to check if email exists
function emailExists($email, $pdo) {
    $sql = "SELECT * FROM users WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':email' => $email]);
    return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
}

// Handling form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signUp'])) {
    $firstName = trim($_POST['user_firstname']);
    $lastName = trim($_POST['user_lastname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    // Basic validation
    if ($password !== $confirmPassword) {
        echo "<script>
                alert('Passwords do not match!');
                window.location.href = 'loginpage.php';
              </script>";
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>
                alert('Invalid email format!');
                window.location.href = 'loginpage.php';
              </script>";
        exit;
    }

    // Check if email already exists
    if (emailExists($email, $pdo)) {
        echo "<script>
                alert('Email already exists! Please register with a different email.');
                window.location.href = 'loginpage.php';
              </script>";
        exit;
    }

    // Insert into database without hashing the password
    $sql = "INSERT INTO users (user_firstname, user_lastname, email, password) VALUES (:user_firstname, :user_lastname, :email, :password)";
    $stmt = $pdo->prepare($sql);

    try {
        $stmt->execute([
            ':user_firstname' => $firstName,
            ':user_lastname' => $lastName,
            ':email' => $email,
            ':password' => $password // Storing plain text password
        ]);
        // Show success message and redirect using JavaScript
        echo "<script>
                alert('Registration successful!');
                window.location.href = 'loginpage.php';
              </script>";
    } catch (PDOException $e) {
        echo "<script>
                alert('Error: " . $e->getMessage() . "');
                window.location.href = 'signup.php';
              </script>";
    }
}
?>

<!-- Modal HTML (if you want to use a modal) -->
<div id="successModal" style="display:none;">
    <div class="modal-content">
        <h2>Registration Successful</h2>
        <p>You have successfully registered. Redirecting to the sign-in page...</p>
    </div>
</div>

<script>
// Optional: Display modal with a delay before redirecting
setTimeout(function() {
    document.getElementById("successModal").style.display = "block";
    setTimeout(function() {
        window.location.href = "loginpage.php"; // Replace with actual sign-in page URL
    }, 2000); // Delay for 2 seconds before redirect
}, 500); // Show modal shortly after registration
</script>
            <!--This is a property of PLSP-CCST BSIT-3B SY 2024-2025 -->