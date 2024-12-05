<?php
session_start();
include '../../database/dbconnect.php';

if (!isset($_SESSION['id'])) {
  echo "User is not logged in.";
  exit();
}

$userId = $_SESSION['id'];
// Initialize counts
$toPayCount = 0;
$toShipCount = 0;
$shippedCount = 0;
$toRateCount = 0;

try {
  // Query for "To Pay" orders (pending status)
  // Query for "To Pay" orders (pending status)
  $sql = "
SELECT COUNT(*) AS count 
FROM orders o
WHERE o.order_status = 'pending' AND o.users_id = :userId";
  $stmt = $pdo->prepare($sql); // Use prepare
  $stmt->execute(['userId' => $userId]); // Bind parameter
  $toPayCount = $stmt->fetchColumn(); // Fetch result

  // Query for "To Ship" orders (completed status)
  $sql = "
SELECT COUNT(*) AS count 
FROM orders o
WHERE o.order_status = 'processing' AND o.users_id = :userId";
  $stmt = $pdo->prepare($sql);
  $stmt->execute(['userId' => $userId]);
  $toShipCount = $stmt->fetchColumn();

  // Query for "Shipped" orders
  $sql = "
SELECT COUNT(*) AS count 
FROM orders o
WHERE o.order_status = 'shipped' AND o.users_id = :userId";
  $stmt = $pdo->prepare($sql);
  $stmt->execute(['userId' => $userId]);
  $shippedCount = $stmt->fetchColumn();

  // Query for "To Rate" orders (delivered status)
  $sql = "
SELECT COUNT(*) AS count 
FROM orders o
WHERE o.order_status = 'delivered' AND o.users_id = :userId";
  $stmt = $pdo->prepare($sql);
  $stmt->execute(['userId' => $userId]);
  $toRateCount = $stmt->fetchColumn();
} catch (PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="stylesheet" href="../../assets/Bootstrap/css/bootstrap.css">

  <link rel="stylesheet" href="../../assets/css/cart/style.css">

  <!-- BOOTSTRAP ICON -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <!--This is a property of PLSP-CCST BSIT-3B SY 2024-2025
  FONT AWESOME CDN -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">


  <title>Tracker - Interllux</title>

  <style>
    .nav-item.active::after {
      content: '';
      position: absolute;
      left: 0;
      right: 0;
      bottom: -2px;
      height: 2px;
      background-color: black;
    }

    .tab-content {
      display: none;
    }

    .tab-content.active {
      display: block;
    }

    .product-name {
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    @media (max-width: 768px) {
      .product-name {
        display: inline-block;
        max-width: calc(1ch * 13);
        overflow: hidden;
        text-overflow: ellipsis;
      }
    }
  </style>

</head>

<body>
  <?php
  include '../../user/component/navbar.php';
  ?>

  <div class="container mt-md-5 mt-4 pt-5">
    <div class="row border pt-3">
      <div class="col">
        <button type="button" class="nav-item btn position-relative p-1 border-0" data-target="to-pay">
          To Pay
          <?php if ($toPayCount > 0) : ?>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-dark">
              <?php echo $toPayCount; ?>
              <span class="visually-hidden">unread messages</span>
            </span>
          <?php endif; ?>
        </button>
      </div>

      <div class="col">
        <button type="button" class="nav-item btn position-relative p-1 border-0" data-target="to-ship">
          To Ship
          <?php if ($toShipCount > 0) : ?>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-dark">
              <?php echo $toShipCount; ?>
              <span class="visually-hidden">unread messages</span>
            </span>
          <?php endif; ?>
        </button>
      </div>
      <div class="col">
        <button type="button" class="nav-item btn position-relative p-1 border-0" data-target="shipped">
          Shipped
          <?php if ($shippedCount > 0) : ?>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-dark">
              <?php echo $shippedCount; ?>
              <span class="visually-hidden">unread messages</span>
            </span>
          <?php endif; ?>
        </button>
      </div>

      <div class="col">
        <button type="button" class="nav-item btn position-relative p-1 border-0" data-target="to-rate">
          To Review
          <?php if ($toRateCount > 0) : ?>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-dark">
              <?php echo $toRateCount; ?>
              <span class="visually-hidden">unread messages</span>
            </span>
          <?php endif; ?>
        </button>
      </div>
    </div>
  </div>


  <!-- TO PAY CONTAINER -->
  <?php
  include '../../database/dbconnect.php';

  try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "
    SELECT 
      p.name AS product_name,
      p.color,
      oi.quantity,
      oi.unit_price,
      o.order_status,
      o.shipping_cost,
      i.img_path,
      o.orders_id
    FROM orders o
    JOIN orders_item oi ON o.orders_id = oi.orders_id
    JOIN product p ON oi.product_id = p.product_id
    JOIN img i ON p.product_id = i.product_id
    WHERE o.order_status = 'pending' AND o.users_id = :userId";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['userId' => $userId]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($orders as $order) {
      $itemTotal = $order['quantity'] * $order['unit_price'];
      $grandTotal = $itemTotal + $order['shipping_cost'];

      echo '<div id="to-pay" class="tab-content container mt-3">
            <div class="row">
              <div class="col-12">
                <div class="card d-flex flex-row p-2">
                  <img src="' . $order['img_path'] . '" class="img-fluid" alt="' . $order['product_name'] . '"
                    style="width: 100px; height: 100px; object-fit: cover;">
                  <div class="card-body text-start p-0 ps-2 mt-2">
                    <p class="product-name fw-bold mb-0">' . $order['product_name'] . '</p>
                    <p class="mb-3">Color: ' . $order['color'] . '</p>
                    <a href="details-to-pay.php" onclick="saveToPayDetails()">
                      <button type="button" class="view-order-btn btn btn-sm p-1">
                        <u class="fw-bold">View Order</u>
                      </button>
                    </a>
                  </div>
                  <div class="card-body p-0 pe-2 text-end price-info">
                    <p class="m-0 fw-bold">' . $order['order_status'] . '</p>
                    <p class="m-0 mt-4">Quantity: ×' . $order['quantity'] . '</p>
                    <p class="m-0 mt-4 fw-bold text-primary">Total: Php ' . number_format($grandTotal, 2) . '</p>
                    <a href="../../user/user_auth/contact-us.php#cancel-policy">
                      <button type="button" class="btn btn-sm btn-dark mt-2">Contact Us</button>
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>';
    }
  } catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
  }
  ?>



  <!-- to ship -->
  <?php
  include '../../database/dbconnect.php';

  try {
    $sql = "
    SELECT 
      p.name AS product_name,
      p.color,
      oi.quantity,
      oi.unit_price,
      o.order_status,
      o.shipping_cost,
      i.img_path
    FROM orders o
    JOIN orders_item oi ON o.orders_id = oi.orders_id
    JOIN product p ON oi.product_id = p.product_id
    JOIN img i ON p.product_id = i.product_id
    WHERE o.order_status = 'processing' AND o.users_id = :userId";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['userId' => $userId]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($orders)) {
      foreach ($orders as $order) {
        // Calculate item total and grand total
        $itemTotal = $order['quantity'] * $order['unit_price'];
        $grandTotal = $itemTotal + $order['shipping_cost'];

        echo '<div id="to-ship" class="tab-content container mt-3">
              <div class="row">
                <div class="col-12">
                  <div class="card d-flex flex-row p-2">
                    <img src="' . $order['img_path'] . '" class="img-fluid" alt="' . $order['product_name'] . '"
                      style="width: 100px; height: 100px; object-fit: cover;">
                    <div class="card-body text-start p-0 ps-2 mt-2">
                      <p class="product-name fw-bold mb-0">' . $order['product_name'] . '</p>
                      <p class="mb-3">Color: ' . $order['color'] . '</p>
                      <a href="details-to-ship.php" onclick="saveToShipDetails()">
                        <button type="button" class="view-order-btn btn btn-sm p-1">
                          <u class="fw-bold">View Order</u>
                        </button>
                      </a>
                    </div>
                    <div class="card-body p-0 pe-2 text-end price-info">
                      <p class="m-0 fw-bold">' . ucfirst($order['order_status']) . '</p>
                      <p class="m-0 mt-5 fw-bold text-primary">Total: Php ' . number_format($grandTotal, 2) . '</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>';
      }
    }
  } catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
  }
  ?>


  <!-- to Receive -->

  <?php
  include '../../database/dbconnect.php';

  try {
    $sql = "
    SELECT 
      p.name AS product_name,
      p.color,
      oi.quantity,
      oi.unit_price,
      o.order_status,
      o.shipping_cost,
      i.img_path
    FROM orders o
    JOIN orders_item oi ON o.orders_id = oi.orders_id
    JOIN product p ON oi.product_id = p.product_id
    JOIN img i ON p.product_id = i.product_id
    WHERE o.order_status = 'shipped' AND o.users_id = :userId";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['userId' => $userId]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($orders)) {
      foreach ($orders as $order) {
        // Calculate item total and grand total
        $itemTotal = $order['quantity'] * $order['unit_price'];
        $grandTotal = $itemTotal + $order['shipping_cost'];

        echo '<div id="to-ship" class="tab-content container mt-3">
              <div class="row">
                <div class="col-12">
                  <div class="card d-flex flex-row p-2">
                    <img src="' . $order['img_path'] . '" class="img-fluid" alt="' . $order['product_name'] . '"
                      style="width: 100px; height: 100px; object-fit: cover;">
                    <div class="card-body text-start p-0 ps-2 mt-2">
                      <p class="product-name fw-bold mb-0">' . $order['product_name'] . '</p>
                      <p class="mb-3">Color: ' . $order['color'] . '</p>
                      <a href="details-to-ship.php" onclick="saveToShipDetails()">
                        <button type="button" class="view-order-btn btn btn-sm p-1">
                          <u class="fw-bold">View Order</u>
                        </button>
                      </a>
                    </div>
                    <div class="card-body p-0 pe-2 text-end price-info">
                      <p class="m-0 fw-bold">' . ucfirst($order['order_status']) . '</p>
                      <p class="m-0 mt-5 fw-bold text-primary">Total: Php ' . number_format($grandTotal, 2) . '</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>';
      }
    }
  } catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
  }
  ?>

  <!-- to rate -->
  <?php
  include '../../database/dbconnect.php';

  try {
    $sql = "
    SELECT 
      p.name AS product_name,
      p.color,
      oi.quantity,
      oi.unit_price,
      o.order_status,
      o.shipping_cost,
      i.img_path
    FROM orders o
    JOIN orders_item oi ON o.orders_id = oi.orders_id
    JOIN product p ON oi.product_id = p.product_id
    JOIN img i ON p.product_id = i.product_id
    WHERE o.order_status = 'delivered' AND o.users_id = :userId";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['userId' => $userId]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($orders)) {
      foreach ($orders as $order) {
        // Calculate item total and grand total
        $itemTotal = $order['quantity'] * $order['unit_price'];
        $grandTotal = $itemTotal + $order['shipping_cost'];

        echo '<div id="to-ship" class="tab-content container mt-3">
              <div class="row">
                <div class="col-12">
                  <div class="card d-flex flex-row p-2">
                    <img src="' . $order['img_path'] . '" class="img-fluid" alt="' . $order['product_name'] . '"
                      style="width: 100px; height: 100px; object-fit: cover;">
                    <div class="card-body text-start p-0 ps-2 mt-2">
                      <p class="product-name fw-bold mb-0">' . $order['product_name'] . '</p>
                      <p class="mb-3">Color: ' . $order['color'] . '</p>
                      <a href="details-to-ship.php" onclick="saveToShipDetails()">
                        <button type="button" class="view-order-btn btn btn-sm p-1">
                          <u class="fw-bold">View Order</u>
                        </button>
                      </a>
                    </div>
                    <div class="card-body p-0 pe-2 text-end price-info">
                      <p class="m-0 fw-bold">' . ucfirst($order['order_status']) . '</p>
                      <p class="m-0 mt-5 fw-bold text-primary">Total: Php ' . number_format($grandTotal, 2) . '</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>';
      }
    }
  } catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
  }
  ?>




  <script src="../../assets/Bootstrap/js/bootstrap.bundle.js"></script>

  <script src="../../assets/js/tracker.js"></script>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      // Get URL parameters
      const urlParams = new URLSearchParams(window.location.search);
      const tab = urlParams.get("tab"); // Read the "tab" parameter

      // Function to activate tab based on its ID
      function activateTab(tabId) {
        // Remove active class from all buttons and tab contents
        document.querySelectorAll(".nav-item").forEach(button => button.classList.remove("active"));
        document.querySelectorAll(".tab-content").forEach(content => content.classList.remove("active"));

        // Add active class to the selected button and tab content
        const selectedButton = document.querySelector(`.nav-item[data-target="${tabId}"]`);
        const selectedTabContent = document.getElementById(tabId);

        if (selectedButton) selectedButton.classList.add("active");
        if (selectedTabContent) selectedTabContent.classList.add("active");
      }

      // Display the selected tab based on the query parameter, if any
      if (tab) {
        activateTab(tab);
      }

      // Add click event listener to all nav items
      document.querySelectorAll(".nav-item").forEach(button => {
        button.addEventListener("click", function() {
          const targetTab = this.getAttribute("data-target");
          activateTab(targetTab);
        });
      });

      // Inject the PHP variables into JavaScript
      const toPayCount = <?php echo $toPayCount; ?>;
      const toShipCount = <?php echo $toShipCount; ?>;
      const shippedCount = <?php echo $shippedCount; ?>;
      const toRateCount = <?php echo $toRateCount; ?>;

      // Update the text content of the relevant elements
      document.querySelector('#to-pay-count').textContent = toPayCount;
      document.querySelector('#to-ship-count').textContent = toShipCount;
      document.querySelector('#shipped-count').textContent = shippedCount;
      document.querySelector('#to-rate-count').textContent = toRateCount;
    });
  </script>


</body>

</html>