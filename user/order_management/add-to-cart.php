<?php
session_start();
include '../../database/dbconnect.php'; // DB connection

// Initialize cart if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Get product ID from request
$product_id = $_POST['product_id'] ?? null;

if ($product_id) {
    // Fetch product details and associated image path
    $stmt = $pdo->prepare("
        SELECT p.product_id, p.name, p.price, i.img_path 
        FROM product p
        LEFT JOIN img i ON p.product_id = i.product_id 
        WHERE p.product_id = :product_id
    ");
    $stmt->execute(['product_id' => $product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        // Add or update product in cart
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity']++;
        } else {
            $_SESSION['cart'][$product_id] = [
                'name' => $product['name'],
                'price' => is_numeric($product['price']) ? (float)$product['price'] : 0, // Validate price
                'quantity' => 1,
                'img' => $product['img_path'] // Use the image path from the database
            ];
        }

        // Calculate total items in cart
        $total_items = array_sum(array_column($_SESSION['cart'], 'quantity'));

        // Send response
        echo json_encode([
            'success' => true,
            'total_items' => $total_items,
            'cart' => $_SESSION['cart']
        ]);
        return;
    }

    // Send response if product not found
    echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
    return;
}

// Get cart items for display
$cart_items = $_SESSION['cart'];

// Calculate subtotal by validating price and quantity before multiplying
$subtotal = array_sum(array_map(function ($item) {
  $price = is_numeric($item['price']) ? (float)$item['price'] : 0; // Validate price
  $quantity = is_numeric($item['quantity']) ? (int)$item['quantity'] : 0; // Validate quantity
  return $price * $quantity;
}, $cart_items));
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Cart Page</title>

  <!-- BOOTSTRAP CSS -->
  <link rel="stylesheet" href="../../assets/Bootstrap/css/bootstrap.css">

  <!-- BOOTSTRAP ICON -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <!-- FONT AWESOME CDN -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

  <!-- CUSTOM CSS-->
  <link rel="stylesheet" href="../../assets/css/add-to-cart.css">

</head>

<body>
  <!-- NAVBAR -->
  <nav class="navbar bg-body-tertiary fixed-top shadow-sm py-0">
    <div class="container-fluid">
      <!-- BACK ICON -->
      <a class="navbar-brand" href="../../user/user_auth/index.php">
        <button class="btn btn-sm px-1">
          <i class="bi bi-arrow-left-short text-dark fs-1 fw-bold" style="font-size: 1.5rem;"></i>
        </button>
      </a>
      <!-- LOGO/NAME -->
      <a class="navbar-brand  mx-auto dm-serif-display letter-spacing-1 text-dark" href="#">
        <img src="../../assets/image/logo.png" alt="Interllux Logo" width="30" height="24">
        Interllux
      </a>
      <p class="navbar-brand">
        <i class="bi bi-arrow-left-short text-light" style="font-size: 1.5rem;"></i>
      </p>
    </div>
  </nav>
  <!-- END NAVBAR -->
          <!-- Cart Content -->
    <div class="container mt-5 pb-5">
        <h2 class="mb-4 mt-5 pt-5">Shopping Cart</h2>
        <div class="row g-2">
            <!-- CART ITEMS -->
            <div class="col-md-8">
                <?php if (!empty($cart_items)): ?>
                    <?php foreach ($cart_items as $id => $item): ?>
                        <div class="cart-item border rounded px-2 container d-flex align-items-center">
                            <input class="form-check-input me-2 mb-3 mb-sm-0 p-1 shadow-none border-secondary" type="checkbox">
                            <img src="<?= htmlspecialchars($item['img']) ?>" alt="Product Image" class="m-0 rounded" width="70">
                            <div class="flex-grow-1 text-center text-sm-start">
                                <p class="mb-1 fw-bold"><?= htmlspecialchars($item['name']) ?></p>
                                <p class="mb-0 fs-6 text-muted">Price: ₱<?= number_format($item['price'], 2) ?></p>
                            </div>
                            <div class="d-flex flex-column align-items-start justify-content-end">
                                <div class="input-group input-group-sm me-0 me-sm-3 mb-3 mb-sm-0" style="width: 90px;">
                                    <button class="btn btn-outline-dark btn-sm decrease-btn">-</button>
                                    <input type="text" class="form-control ps-3 quantity-input" value="<?= $item['quantity'] ?>" min="1">
                                    <button class="btn btn-outline-dark btn-sm increase-btn">+</button>
                                </div>            <!--This is a property of PLSP-CCST BSIT-3B SY 2024-2025 -->
                                <div class="mt-md-2 mt-sm-0">
                                    <span class="product-price ms-3" data-price="<?= htmlspecialchars($item['price']) ?>">₱<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted">Your cart is empty.</p>
                <?php endif; ?>
            </div>

            <!-- Cart Summary -->
            <div class="col-md-4 border p-3 rounded">
                <div class="cart-total">
                    <h5>Order Summary</h5>
                    <div class="d-flex justify-content-between">
                      <span>Subtotal</span>
                      <span id="subtotal">₱<?= is_numeric($subtotal) ? number_format($subtotal, 2) : "0.00" ?></span>
                      </div>
                    <div class="d-flex justify-content-between">
                        <span>Shipping Fee</span>
                        <span>₱500.00</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <strong>Total</strong>
                        <strong id="total">₱<?= number_format($subtotal + 500, 2) ?></strong>
                    </div>
                    <a href="../../user/order_management/payment.php">
                        <button class="btn btn-dark w-100 mt-3">Proceed to Checkout</button>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>


  <div id="footer">
    <script src="../../assets/js/footer.js"></script>
  </div>

  <script src="../../assets/Bootstrap/css/bootstrap.bundle.js"></script>

  <script src="../../assets/js/add-to-cart.js"></script>
</body>

</html>