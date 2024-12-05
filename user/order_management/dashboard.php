<?php
include '../../database/dbconnect.php';
session_start();

$first_name = "Guest"; // Default to 'Guest'
$order_counts = [
  'pending' => 0,
  'processing' => 0,
  'shipped' => 0,
  'delivered' => 0,
  'completed' => 0,
  'cancelled' => 0,
  'return/refund' => 0,
];

if (isset($_SESSION['id'])) {
  try {
    // Get user's first name
    $stmt = $pdo->prepare("SELECT user_firstname FROM users WHERE id = :id");
    $stmt->execute(['id' => $_SESSION['id']]);
    $user = $stmt->fetch();

    if ($user && !empty($user['user_firstname'])) {
      $first_name = $user['user_firstname'];
    }

    // Get the counts of orders based on user id and order status
    $stmt = $pdo->prepare("
            SELECT order_status, COUNT(oi.orders_item_id) AS order_count
            FROM orders o
            JOIN orders_item oi ON o.orders_id = oi.orders_id
            WHERE o.users_id = :user_id
            GROUP BY order_status
        ");
    $stmt->execute(['user_id' => $_SESSION['id']]);

    // Fetch and store counts for each order status
    while ($row = $stmt->fetch()) {
      $order_counts[$row['order_status']] = $row['order_count'];
    }
  } catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- BOOTSTRAP CSS -->
  <link rel="stylesheet" href="../../assets/Bootstrap/css/bootstrap.css">

  <!-- BOOTSTRAP ICON LINK -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <!-- FONT AWESOME CDN -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

  <link rel="stylesheet" href="../../assets/css/order.css">

  <title>Dashboard - Interllux</title>

  <style>
  </style>

</head>

<body>

  <?php
  include '../../user/component/navbar.php';
  ?>

  <div class="profile-container container-fluid py-5 mt-3 text-dark" style="height: 200px;">
    <h5>Hello, <?php echo htmlspecialchars($first_name);?>!</h5>
  </div>
  <!-- ORDER TRACKER BUTTONS -->
  <div class="container mt-3 pb-5">
    <h5 class="mb-3">My Orders</h5>
    <div class="row text-center">
      <!-- TO PAY -->
      <div class="col p-0">
        <a class="text-dark" href="tracker.php?tab=to-pay">
          <button type="button" class="tracker-btn btn btn-sm position-relative p-0 px-2">
            <i class="bi bi-credit-card fs-1"></i><br>To Pay
                        <!--This is a property of PLSP-CCST BSIT-3B SY 2024-2025 -->
            <span class="position-absolute top-0 start-100 translate-middle pe-3 badge rounded-pill bg-dark 
                        <?php echo ($order_counts['pending'] == 0) ? 'd-none' : ''; ?>">
              <?php echo $order_counts['pending']; ?>
            </span>
          </button>
        </a>
      </div>
      <!-- TO SHIP -->
      <div class="col p-0">
        <a class="text-dark" href="tracker.php?tab=to-ship">
          <button type="button" class="tracker-btn btn btn-sm position-relative p-0 px-2">
            <i class="bi bi-box fs-1"></i><br>To Ship
            <span class="position-absolute top-0 start-100 translate-middle pe-3 badge rounded-pill bg-dark 
                        <?php echo ($order_counts['processing'] == 0) ? 'd-none' : ''; ?>">
              <?php echo $order_counts['processing']; ?>
            </span>
          </button>
        </a>
      </div>
      <!-- SHIPPED -->
      <div class="col p-0">
        <a class="text-dark" href="tracker.php?tab=shipped">
          <button type="button" class="tracker-btn btn btn-sm position-relative p-0 px-2">
            <i class="bi bi-truck fs-1"></i><br>Shipped
            <span class="position-absolute top-0 start-100 translate-middle pe-3 badge rounded-pill bg-dark 
                        <?php echo ($order_counts['shipped'] == 0) ? 'd-none' : ''; ?>">
              <?php echo $order_counts['shipped']; ?>
            </span>
          </button>
        </a>
      </div>
      <!-- rate -->
      <div class="col p-0">
        <a class="text-dark" href="tracker.php?tab=to-rate">
          <button type="button" class="tracker-btn btn btn-sm position-relative p-0 px-2">
            <i class="bi-check2-square fs-1"></i><br>Delivered
            <span class="position-absolute top-0 start-100 translate-middle pe-3 badge rounded-pill bg-dark 
                        <?php echo ($order_counts['delivered'] == 0) ? 'd-none' : ''; ?>">
              <?php echo $order_counts['delivered']; ?>
            </span>
          </button>
        </a>
      </div>

    </div>
  </div>
  <!-- CANCELED ORDERS AND RETURN REFUND ORDERS -->
  <div class="container mt-3 pb-5 border-2 border-bottom">
    <div class="row text-center">
      <div class="col p-0">
        <a class="text-dark" href="delivered.php">
          <button type="button" class="tracker-btn btn btn-sm position-relative p-0 px-2">
            <i class="bi bi-star fs-1"></i><br>Rate
            <span class="position-absolute top-0 start-100 translate-middle pe-3 badge rounded-pill bg-dark 
                        <?php echo ($order_counts['completed'] == 0) ? 'd-none' : ''; ?>">
              <?php echo $order_counts['completed']; ?>
            </span>
          </button>
        </a>
      </div>
      <!-- CANCELLED  -->
      <div class="col p-0">
        <a class="text-dark" href="cancelled-order.php">
          <button type="button" class="tracker-btn btn btn-sm position-relative p-0 px-2">
            <i class="bi bi-bag-x fs-1"></i><br>Cancelled
            <span class="position-absolute top-0 start-100 translate-middle pe-3 badge rounded-pill bg-dark 
                        <?php echo ($order_counts['cancelled'] == 0) ? 'd-none' : ''; ?>">
              <?php echo $order_counts['cancelled']; ?>
            </span>
          </button>
        </a>
      </div>
      <!-- REFEUND -->
      <div class="col p-0">
        <a class="text-dark" href="refunded-order.php">
          <button type="button" class="tracker-btn btn btn-sm position-relative p-0 px-2">
            <i class="bi bi-box-arrow-in-left fs-1"></i><br>Returned
            <span class="position-absolute top-0 start-100 translate-middle pe-3 badge rounded-pill bg-dark 
                        <?php echo ($order_counts['return/refund'] == 0) ? 'd-none' : ''; ?>">
              <?php echo $order_counts['return/refund']; ?>
            </span>
          </button>
        </a>
      </div>
      <!-- HISTORY -->
      <div class="col p-0">
      </div>
    </div>
  </div> <!-- END CANCELLED -->


  <!-- YOU MAY ALSO LIKE CONTAINER -->


  <!-- PRODUCT CATALOG -->
  <?php
  include '../../user/order_management/recommendation.php';
  ?>

  <!-- FOOTER -->
  <div id="footer">
    <script src="../../assets/js/footer.js"></script>
  </div>

  <!-- BOOTSTRAP JS-->
  <script src="../../assets/Bootstrap/js/bootstrap.bundle.js"></script>

  <!-- CUSTOM JS -->
  <script src="../../assets/js/order.js"></script>
</body>

</html>