<?php
session_start();
header('Content-Type: application/json');

// Check if cart exists in session
if (!isset($_SESSION['cart'])) {
    echo json_encode(['success' => false, 'message' => 'Cart is empty']);
    exit;
}

// Get data from the AJAX request
$data = json_decode(file_get_contents('php://input'), true);
$product_id = $data['product_id'];
$new_quantity = $data['quantity'];

// Validate data
if ($new_quantity < 1 || !isset($_SESSION['cart'][$product_id])) {
    echo json_encode(['success' => false, 'message' => 'Invalid quantity or product']);
    exit;
}

// Update quantity in session
$_SESSION['cart'][$product_id]['quantity'] = $new_quantity;

// Recalculate total price and grand total
$total_price = 0;
foreach ($_SESSION['cart'] as $item) {
    $total_price += $item['price'] * $item['quantity'];
}

$shipping_fee = 500; // Fixed shipping fee
$grand_total = $total_price + $shipping_fee;

// Return the updated totals as JSON
echo json_encode([
    'success' => true,
    'total_price' => $total_price,
    'grand_total' => $grand_total
]);
//This is a property of PLSP-CCST BSIT-3B SY 2024-2025