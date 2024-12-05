<?php
session_start();
include '../../database/dbconnect.php'; // DB connection

$first_name = "Guest"; // Default to 'Guest'
if (isset($_SESSION['id'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $_SESSION['id']]);
        $user = $stmt->fetch();

        if ($user && !empty($user['user_firstname'])) {
            $first_name = $user['user_firstname'];
        }
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
    }
}

// Retrieve cart items from the session
$cart_items = $_SESSION['cart'] ?? [];

// Calculate the total price
$total_price = 0;
foreach ($cart_items as $item) {
    $total_price += $item['price'] * $item['quantity'];
}

// Assuming a fixed shipping fee (can be dynamic later)
$shipping_fee = 500; // Fixed shipping fee, you can change this based on logic

// Calculate the grand total (total price + shipping fee)
$grand_total = $total_price + $shipping_fee;
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payment Page</title>

  <!-- BOOTSTRAP CSS -->
  <link rel="stylesheet" href="../../assets/Bootstrap/css/bootstrap.css">

  <!-- BOOTSTRAP ICON -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <!-- FONT AWESOME CDN -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>
  <!-- NAVBAR -->
  <nav class="navbar bg-body-tertiary fixed-top shadow-sm py-0">
    <div class="container-fluid">
      <a class="navbar-brand" href="../../user/order_management/add-to-cart.php">
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
  <!-- END NAVBAR -->

 
  <div class="container mt-5 pt-5">
    <div class="row g-4">
      <!-- Checked-Out Products (Left Column) -->
      <div class="col-md-5">
        <h4 class="ms-2">Check-Out Products</h4>
        <div class="card">
          <div class="card-body">
            <!-- Loop through cart items -->
            <?php foreach ($cart_items as $product_id => $item): ?>
              <div class="d-flex align-items-center mb-3" id="product_<?= $product_id ?>">
                <img src="<?= htmlspecialchars($item['img']); ?>" alt="Product Image" class="rounded" width="70">
                <div class="ms-3">
                  <p class="mb-1 fw-bold"><?= htmlspecialchars($item['name']); ?></p>
                  <p class="mb-0 text-muted">Qty: <span class="item-quantity"><?= htmlspecialchars($item['quantity']); ?></span></p>
                  <p class="mb-0 text-muted">₱<span class="item-price"><?= number_format($item['price'], 2); ?></span></p>
                </div>
                            <!--This is a property of PLSP-CCST BSIT-3B SY 2024-2025 -->
                <div class="ms-auto">
                  <button class="btn btn-sm btn-outline-dark" onclick="updateQuantity(<?= $product_id ?>, -1)">-</button>
                  <button class="btn btn-sm btn-outline-dark" onclick="updateQuantity(<?= $product_id ?>, 1)">+</button>
                  <button class="btn btn-sm " onclick="removeItem(<?= $product_id ?>)">Remove</button>
                </div>
              </div>
            <?php endforeach; ?>
            <!-- End of cart items -->

            <hr>
            <!-- Total -->
            <div class="d-flex justify-content-between">
              <strong>Total</strong>
              <strong>₱<span id="total-price"><?= number_format($total_price, 2); ?></span></strong>
            </div>
            <hr>
            <!-- Shipping Fee -->
            <div class="d-flex justify-content-between">
              <strong>Shipping Fee</strong>
              <strong>₱<?= number_format($shipping_fee, 2); ?></strong>
            </div>
            <hr>
            <!-- Grand Total -->
            <div class="d-flex justify-content-between">
              <strong>Grand Total</strong>
              <strong>₱<span id="grand-total"><?= number_format($grand_total, 2); ?></span></strong>
            </div>
          </div>
        </div>
      </div>

      <!-- Payment Form (Right Column) -->
      <div class="col-md-7">
        <h4>Payment</h4>
        <form>
          <!-- Delivery Information -->
          <div class="card mb-4">
  <div class="card-header bg-dark text-white">
    <h5 class="mb-0">Delivery Information</h5>
  </div>
  <div class="card-body">
    <div class="row g-3">
      <!-- First Name -->
      <div class="col-md-6">
        <label for="firstName" class="form-label">First Name</label>
        <input type="text" class="form-control" id="firstName" value="<?= isset($user) ? htmlspecialchars($user['user_firstname']) : '' ?>" required>
      </div>

      <!-- Last Name -->
      <div class="col-md-6">
        <label for="lastName" class="form-label">Last Name</label>
        <input type="text" class="form-control" id="lastName" value="<?= isset($user) ? htmlspecialchars($user['user_lastname']) : '' ?>" required>
      </div>

      <!-- Email -->
      <div class="col-12">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" value="<?= isset($user) ? htmlspecialchars($user['email']) : '' ?>" placeholder="example@example.com" required>
      </div>

      <!-- Address -->
      <div class="col-12">
        <label for="address" class="form-label">Address</label>
        <input type="text" class="form-control" id="address" value="<?= isset($user) ? htmlspecialchars($user['street_address']) : '' ?>" placeholder="1234 Main St" required>
      </div>

      <!-- Barangay -->
      <div class="col-md-6">
        <label for="barangay" class="form-label">Barangay</label>
        <input type="text" class="form-control" id="barangay" value="<?= isset($user) ? htmlspecialchars($user['barangay']) : '' ?>" required>
      </div>

      <!-- City -->
      <div class="col-md-6">
        <label for="city" class="form-label">City</label>
        <input type="text" class="form-control" id="city" value="<?= isset($user) ? htmlspecialchars($user['city']) : '' ?>" readonly>
      </div>

      <!-- Province -->
      <div class="col-md-6">
        <label for="province" class="form-label">Province</label>
        <input type="text" class="form-control" id="province" value="<?= isset($user) ? htmlspecialchars($user['province']) : '' ?>" readonly>
      </div>

      <!-- Region -->
      <div class="col-md-6">
        <label for="region" class="form-label">Region</label>
        <select id="region" class="form-select" required>
          <option selected disabled>Select Region...</option>
        </select>
      </div>

      <!-- Country -->
      <div class="col-md-6">
        <label for="country" class="form-label">Country</label>
        <select id="country" class="form-select" required>
          <option selected disabled>Select Country...</option>
          <option <?= (isset($user) && $user['country'] == 'Philippines') ? 'selected' : '' ?>>Philippines</option>
          <option <?= (isset($user) && $user['country'] == 'Other') ? 'selected' : '' ?>>Other</option>
        </select>
      </div>

      <!-- Zip Code -->
      <div class="col-md-6">
        <label for="zip" class="form-label">Zip Code</label>
        <input type="text" class="form-control" id="zip" value="<?= isset($user) ? htmlspecialchars($user['zip_code']) : '' ?>" required>
      </div>
    </div>
  </div>
</div>

          <!-- Payment Options -->
          <div class="card">
            <div class="card-header bg-dark text-white">
              <h5 class="mb-0">Payment</h5>
            </div>
            <div class="card-body">
              <p>All transactions are secure and encrypted.</p>
              <p>An Interllux agent will contact you shortly for payment instructions. For faster transactions, you can
                directly WhatsApp us: <strong>+63 961 619 5710</strong>.</p>

              <!-- Payment Methods -->
              <div class="form-check">
                <input class="form-check-input" type="radio" name="paymentMethod" id="onlineBanking" required>
                <label class="form-check-label" for="onlineBanking">Online Banking</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="paymentMethod" id="eWallet" required>
                <label class="form-check-label" for="eWallet">E-wallet</label>
              </div>
            </div>
          </div>

          <!-- Submit Button -->
          <div class="mt-3">
            <a href="#">
              <button type="submit" class="btn btn-dark w-100">Place Order</button>
            </a>

          </div>
        </form>
      </div>
    </div>
  </div>

  <br>
  <br>
  <br>

  <!-- BOOTSTRAP JS -->
  <script src="../../assets/Bootstrap/js/bootstrap.bundle.js"></script>
  <script src= "/assets/js/checkout.js"></script>
    <script>
    // Update quantity (increase or decrease)
    function updateQuantity(productId, change) {
      var currentQuantity = parseInt(document.querySelector(`#product_${productId} .item-quantity`).textContent);
      var newQuantity = currentQuantity + change;
      if (newQuantity < 1) return; // Prevent quantity from going below 1

      // Send AJAX request to update the session cart
      fetch('../../user/order_management/update-cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ product_id: productId, quantity: newQuantity })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          document.querySelector(`#product_${productId} .item-quantity`).textContent = newQuantity;
          updateTotals(data.total_price, data.grand_total);
        }
      });
    }

    // Remove item from the cart
    function removeItem(productId) {
      fetch('../../user/order_management/remove-from-cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ product_id: productId })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          document.querySelector(`#product_${productId}`).remove();
          updateTotals(data.total_price, data.grand_total);
        }
      });
    }

    // Update total price and grand total
    function updateTotals(totalPrice, grandTotal) {
      document.getElementById('total-price').textContent = totalPrice.toFixed(2);
      document.getElementById('grand-total').textContent = grandTotal.toFixed(2);
    }
  </script>
</body>

</html>