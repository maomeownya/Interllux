<?php
ob_start();
include '../partials/admin-header.php';
include '../database/dbconnect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['product-name'];
    $brand = $_POST['brand-name'];
    $sku = $_POST['sku'];
    $upc = $_POST['upc'];
    $category = $_POST['category'];
    $color = $_POST['color'];
    $condition = $_POST['condition'];
    $material = $_POST['material'];
    $description = $_POST['description'];
    $quantity = !empty($_POST['inventory-qty']) ? intval($_POST['inventory-qty']) : 0;
    $normal_threshold = !empty($_POST['normal-threshold']) ? intval($_POST['normal-threshold']) : null;
    $low_threshold = !empty($_POST['low-threshold']) ? intval($_POST['low-threshold']) : null;
    $price = !empty($_POST['price']) ? floatval($_POST['price']) : 0;
    $image_url = $_POST['image-url'];

    // Assuming admin_id is 1 for now. In a real application, you'd get this from the session. This is a property of PLSP-CCST BSIT-3B SY 2024-2025 -->

    $admin_id = 1;

    try {
        $pdo->beginTransaction();

        // Get the next available product_id
        $stmt = $pdo->query("SELECT COALESCE(MAX(product_id), 0) + 1 AS next_id FROM product");
        $next_id = $stmt->fetchColumn();

        $stmt = $pdo->prepare("INSERT INTO product (product_id, admin_id, name, category, brand, price, quantity, condition, material, description, sku, upc, color, low_threshold, normal_threshold) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$next_id, $admin_id, $name, $category, $brand, $price, $quantity, $condition, $material, $description, $sku, $upc, $color, $low_threshold, $normal_threshold]);

        if (!empty($image_url)) {
            // Get the next available img_id
            $img_stmt = $pdo->query("SELECT COALESCE(MAX(img_id), 0) + 1 AS next_img_id FROM img");
            $next_img_id = $img_stmt->fetchColumn();

            // Insert new image with manually set img_id
            $image_stmt = $pdo->prepare("INSERT INTO img (img_id, product_id, img_path) VALUES (?, ?, ?)");
            $image_stmt->execute([$next_img_id, $next_id, $image_url]);
        }

        // Fetch the inserted product details
        $fetch_product_stmt = $pdo->prepare("SELECT * FROM product WHERE product_id = ?");
        $fetch_product_stmt->execute([$next_id]);
        $inserted_product = $fetch_product_stmt->fetch(PDO::FETCH_ASSOC);

        // Store the inserted product details in the session
        $_SESSION['inserted_product'] = $inserted_product;

        $pdo->commit();

        $_SESSION['show_success_modal'] = true;
        $_SESSION['new_product_id'] = $next_id;

        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } catch (PDOException $e) {
        $pdo->rollBack();
        $error_message = "Error adding product: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Product</title>
    <!-- Add your CSS links here -->
</head>

<body>
    <main class="p-2 px-4">
        <section id="add-product">
            <div class="content p-4">
                <h1 class="fw-bold mb-0">Add New Product</h1>

                <?php if (isset($error_message)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
                <?php endif; ?>

                <form action="" method="post">
                    <div class="row mt-4">
                        <div class="col-md-3">
                            <label for="product-name" class="form-label fw-bold">Product Name</label>
                            <input type="text" class="form-control" id="product-name" name="product-name" required>
                        </div>
                        <div class="col-md-3">
                            <label for="brand-name" class="form-label fw-bold">Brand Name</label>
                            <input type="text" class="form-control" id="brand-name" name="brand-name" required>
                        </div>
                        <div class="col-md-3">
                            <label for="sku" class="form-label fw-bold">SKU</label>
                            <input type="text" class="form-control" id="sku" name="sku" required>
                        </div>
                        <div class="col-md-3">
                            <label for="upc" class="form-label fw-bold">UPC</label>
                            <input type="text" class="form-control" id="upc" name="upc" required>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-3">
                            <label for="category" class="form-label fw-bold">Category</label>
                            <input type="text" class="form-control" id="category" name="category" required>
                        </div>
                        <div class="col-md-3">
                            <label for="color" class="form-label fw-bold">Color</label>
                            <input type="text" class="form-control" id="color" name="color" required>
                        </div>
                        <div class="col-md-3">
                            <label for="condition" class="form-label fw-bold">Condition</label>
                            <input type="text" class="form-control" id="condition" name="condition" required>
                        </div>
                        <div class="col-md-3">
                            <label for="material" class="form-label fw-bold">Material</label>
                            <input type="text" class="form-control" id="material" name="material" required>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <label for="description" class="form-label fw-bold">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4"
                                required></textarea>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-3">
                            <label for="inventory-qty" class="form-label fw-bold">Inventory Qty</label>
                            <input type="number" class="form-control" id="inventory-qty" name="inventory-qty" required
                                min="0" oninput="updateStatus()">
                        </div>
                        <div class="col-md-3">
                            <label for="normal-threshold" class="form-label fw-bold">Normal Threshold Qty</label>
                            <input type="number" class="form-control" id="normal-threshold" name="normal-threshold"
                                required min="0" oninput="updateStatus()">
                        </div>
                        <div class="col-md-3">
                            <label for="low-threshold" class="form-label fw-bold">Low Threshold Qty</label>
                            <input type="number" class="form-control" id="low-threshold" name="low-threshold" required
                                min="0" oninput="updateStatus()">
                        </div>
                        <div class="col-md-3">
                            <label for="price" class="form-label fw-bold">Price</label>
                            <input type="number" class="form-control" id="price" name="price" required min="0"
                                step="0.01">
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label for="image-url" class="form-label fw-bold">Image URL</label>
                            <input type="url" class="form-control" id="image-url" name="image-url">
                        </div>
                        <div class="col-md-3">
                            <label for="status" class="form-label fw-bold">Status</label>
                            <input type="text" class="form-control" id="status" name="status" readonly>
                        </div>
                    </div>

                    <div class="row mt-5">
                        <div class="col text-end">
                            <button type="button" class="btn btn-light me-2" data-bs-toggle="modal"
                                data-bs-target="#cancelConfirmModal">Cancel</button>
                            <button type="submit" class="btn btn-dark">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </main>

    <!-- Cancel Confirmation Modal -->
    <div class="modal fade" id="cancelConfirmModal" tabindex="-1" aria-labelledby="cancelConfirmModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header justify-content-center pb-1" style="border-bottom: none;">
                    <h5 class="modal-title text-danger text-center fs-3 fw-light" id="cancelConfirmModalLabel">CANCEL
                        CHANGES?</h5>
                </div>
                <div class="modal-body text-center">
                    Are you sure you want to cancel? Any unsaved changes will be lost.
                </div>
                <div class="modal-footer justify-content-center text-center pt-1" style="border-top: none;">
                    <button type="button" class="btn btn-dark me-3" data-bs-dismiss="modal">Stay</button>
                    <a href="./inventory-overview.php" class="btn btn-danger ms-3">Leave</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Added Successful Modal -->
    <div class="modal fade" id="add-success" tabindex="-1" aria-labelledby="addSuccessfulModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content p-3">
                <div class="modal-header justify-content-center pb-1" style="border-bottom: none;">
                    <h5 class="modal-title text-success text-center fs-3 fw-light" id="addSuccessfulModalLabel">PRODUCT
                        ADDED</h5>
                </div>
                <div class="modal-body text-center">
                    <p>The product has been successfully added!</p>
                    <?php if (isset($_SESSION['inserted_product'])): ?>
                    <div class="mt-3">
                        <h6>Product Details:</h6>
                        <ul class="list-unstyled">
                            <li><strong>Name:</strong>
                                <?php echo htmlspecialchars($_SESSION['inserted_product']['name']); ?></li>
                            <li><strong>SKU:</strong>
                                <?php echo htmlspecialchars($_SESSION['inserted_product']['sku']); ?></li>
                            <li><strong>Price:</strong>
                                $<?php echo number_format($_SESSION['inserted_product']['price'], 2); ?></li>
                            <li><strong>Quantity:</strong> <?php echo $_SESSION['inserted_product']['quantity']; ?></li>
                        </ul>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer justify-content-center text-center pt-1" style="border-top: none;">
                    <a href="./inventory-overview.php" class="btn btn-dark">Go to Inventory</a>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php if (isset($_SESSION['show_success_modal']) && $_SESSION['show_success_modal']): ?>
        var addSuccessModal = new bootstrap.Modal(document.getElementById('add-success'));
        addSuccessModal.show();
        <?php
        // Clear the session variables
        unset($_SESSION['show_success_modal']);
        unset($_SESSION['new_product_id']);
        unset($_SESSION['inserted_product']);
        endif;
        ?>

        updateStatus();
    });

    function updateStatus() {
        const inventoryQty = parseInt(document.getElementById('inventory-qty').value) || 0;
        const normalThreshold = parseInt(document.getElementById('normal-threshold').value) || 0;
        const lowThreshold = parseInt(document.getElementById('low-threshold').value) || 0;
        const statusInput = document.getElementById('status');

        if (inventoryQty === 0) {
            statusInput.value = 'Out of Stock';
        } else if (inventoryQty <= lowThreshold) {
            statusInput.value = 'Low Stock';
        } else if (inventoryQty <= normalThreshold) {
            statusInput.value = 'Normal Stock';
        } else {
            statusInput.value = 'High Stock';
        }
    }
    </script>

    <?php
    include '../partials/admin-footer.php';
    ob_end_flush();
    ?>
</body>

</html>