<?php
session_start(); // Start the session

// Check if the user is logged in by verifying the session
if (!isset($_SESSION['id'])) {
  header("Location: loginpage.php");
  exit();
}

$user_id = $_SESSION['id']; // Retrieve the logged-in user's ID

// Database connection (adjust according to your actual db configuration)
require_once '../../database/dbconnect.php';

try {
  $pdo = new PDO($dsn, $user, $password);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
  exit();
}

// Fetch user's order details
$stmt = $pdo->prepare("
    SELECT 
        o.order_date, o.order_status, o.sub_amount, o.total_amount, o.shipping_cost, 
        o.shipped_date, o.delivered_date, u.user_firstname, u.user_lastname, u.phone_num, 
        u.street_address, u.barangay, u.city, u.province, u.zip_code, u.country, u.email
    FROM orders o
    JOIN users u ON o.users_id = u.id
    WHERE u.id = :user_id
");
$stmt->execute(['user_id' => $user_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../../assets/Bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <title>Order Details</title>
</head>

<body>
  <nav class="navbar bg-body-tertiary fixed-top shadow-sm py-0">
    <div class="container-fluid">
      <a class="navbar-brand" href="tracker.php#to-pay">
        <button class="btn btn-sm px-1">
          <i class="bi bi-arrow-left-short text-dark fs-1 fw-bold" style="font-size: 1.5rem;"></i>
        </button>
      </a>
      <a class="navbar-brand mx-auto dm-serif-display letter-spacing-1 text-dark" href="../../user/user_auth/index.php">
        <img src="../../assets/image/logo.png" alt="Interllux Logo" width="30" height="24">
        Interllux
      </a>
    </div>
  </nav>

  <div class="container-fluid mt-5 pt-4">
    <!-- SHIPPING DETAILS -->
    <div class="card mb-3">
      <div class="card-header bg-dark text-light">Shipping Details</div>
      <div class="card-body">
        <div class="card-text mb-2"><strong>Recipient: </strong><?php echo htmlspecialchars($order['user_firstname'] . ' ' . $order['user_lastname']); ?></div>
        <div class="card-text mb-2"><strong>Contact: </strong><?php echo htmlspecialchars($order['phone_num']); ?></div>
        <div class="card-text mb-2"><strong>Address: </strong><?php echo htmlspecialchars("{$order['street_address']}, {$order['barangay']}, {$order['city']}, {$order['province']}, {$order['zip_code']}, {$order['country']}"); ?></div>
      </div>
    </div>

    <!-- ORDER SUMMARY -->
    <div class="card mb-3">
      <div class="card-header bg-dark text-light">Order Summary</div>
      <div class="card-body mb-3">
                    <!--This is a property of PLSP-CCST BSIT-3B SY 2024-2025 -->
        <div class="d-flex justify-content-between">
          <p><strong>Subtotal: </strong></p>
          <p><?php echo 'Php ' . number_format($order['sub_amount'], 2); ?></p>
        </div>
        <div class="d-flex justify-content-between">
          <p><strong>Shipping Fee: </strong></p>
          <p><?php echo 'Php ' . number_format($order['shipping_cost'], 2); ?></p>
        </div>
        <hr>
        <div class="d-flex justify-content-between">
          <p><strong>Total: </strong></p>
          <p><?php echo 'Php ' . number_format($order['total_amount'], 2); ?></p>
        </div>
      </div>
    </div>

    <!-- ORDER INFORMATION -->
    <div class="card mb-3">
      <div class="card-header bg-dark text-light">Order Information</div>
      <div class="card-body mb-3">
        <p><strong>Order Date: </strong><?php echo htmlspecialchars($order['order_date']); ?></p>
        <p><strong>Order Status: </strong><?php echo htmlspecialchars($order['order_status']); ?></p>
        <p><strong>Shipment Date: </strong><?php echo htmlspecialchars($order['shipped_date']); ?></p>
        <p><strong>Email: </strong><?php echo htmlspecialchars($order['email']); ?></p>
      </div>
    </div>
  </div>
</body>

</html>