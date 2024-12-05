<?php
ob_start();
include '../partials/admin-header.php';
include '../database/dbconnect.php';
session_start();

$product = []; // Initialize $product to prevent undefined variable warnings
$image = []; // Initialize $image to prevent undefined variable warnings

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

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
    $quantity = !empty($_POST['inventory-qty']) ? intval($_POST['inventory-qty']) : null;
    $normal_threshold = !empty($_POST['normal-threshold']) ? intval($_POST['normal-threshold']) : null;
    $low_threshold = !empty($_POST['low-threshold']) ? intval($_POST['low-threshold']) : null;
    $price = !empty($_POST['price']) ? floatval($_POST['price']) : null;
    $image_url = $_POST['image-url'];

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("UPDATE product SET name = ?, brand = ?, sku = ?, upc = ?, category = ?, color = ?, condition = ?, material = ?, description = ?, quantity = ?, normal_threshold = ?, low_threshold = ?, price = ? WHERE product_id = ?");
        $stmt->execute([$name, $brand, $sku, $upc, $category, $color, $condition, $material, $description, $quantity, $normal_threshold, $low_threshold, $price, $product_id]);

        if (!empty($image_url)) {
            // Check if an image record exists for this product
            $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM img WHERE product_id = ?");
            $check_stmt->execute([$product_id]);
            $image_exists = $check_stmt->fetchColumn();

            if ($image_exists) {
                // Update existing image record
                $image_stmt = $pdo->prepare("UPDATE img SET img_path = ? WHERE product_id = ?");
                $image_stmt->execute([$image_url, $product_id]);
            } else {
                // Insert new image record
                $image_stmt = $pdo->prepare("INSERT INTO img (product_id, img_path) VALUES (?, ?)");
                $image_stmt->execute([$product_id, $image_url]);
            }
        }

        $pdo->commit();

        $_SESSION['show_success_modal'] = true;
        header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $product_id);
        exit();
    } catch (PDOException $e) {
        $pdo->rollBack();
        $error_message = "Error updating product: " . $e->getMessage();
    }
} elseif ($product_id > 0) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM product WHERE product_id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            $error_message = "Product not found.";
        } else {
            $image_stmt = $pdo->prepare("SELECT img_path FROM img WHERE product_id = ? LIMIT 1");
            $image_stmt->execute([$product_id]);
            $image = $image_stmt->fetch(PDO::FETCH_ASSOC);
        }
    } catch (PDOException $e) {
        $error_message = "Error fetching product details: " . $e->getMessage();
    }
} else {
    // $error_message = "Invalid product ID.";
}
    //This is a property of PLSP-CCST BSIT-3B SY 2024-2025 -->

if (isset($_SESSION['delete_error'])) {
    $error_message = $_SESSION['delete_error'];
    unset($_SESSION['delete_error']);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product Details</title>
    <!-- Add your CSS links here -->
</head>

<body>
    <main class="p-2 px-4">
        <section id="edit-details">
            <div class="content p-4">
                <h1 class="fw-bold mb-0">Edit Product Details</h1>

                <?php if (isset($error_message)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
                <?php endif; ?>

                <form action="" method="post">
                    <div class="row mt-4">
                        <div class="col-md-3">
                            <label for="product-name" class="form-label fw-bold">Product Name</label>
                            <input type="text" class="form-control" id="product-name" name="product-name"
                                value="<?php echo htmlspecialchars($product['name'] ?? ''); ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="brand-name" class="form-label fw-bold">Brand Name</label>
                            <input type="text" class="form-control" id="brand-name" name="brand-name"
                                value="<?php echo htmlspecialchars($product['brand'] ?? ''); ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="sku" class="form-label fw-bold">SKU</label>
                            <input type="text" class="form-control" id="sku" name="sku"
                                value="<?php echo htmlspecialchars($product['sku'] ?? ''); ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="upc" class="form-label fw-bold">UPC</label>
                            <input type="text" class="form-control" id="upc" name="upc"
                                value="<?php echo htmlspecialchars($product['upc'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-3">
                            <label for="category" class="form-label fw-bold">Category</label>
                            <input type="text" class="form-control" id="category" name="category"
                                value="<?php echo htmlspecialchars($product['category'] ?? ''); ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="color" class="form-label fw-bold">Color</label>
                            <input type="text" class="form-control" id="color" name="color"
                                value="<?php echo htmlspecialchars($product['color'] ?? ''); ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="condition" class="form-label fw-bold">Condition</label>
                            <input type="text" class="form-control" id="condition" name="condition"
                                value="<?php echo htmlspecialchars($product['condition'] ?? ''); ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="material" class="form-label fw-bold">Material</label>
                            <input type="text" class="form-control" id="material" name="material"
                                value="<?php echo htmlspecialchars($product['material'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <label for="description" class="form-label fw-bold">Description</label>
                            <textarea class="form-control" id="description" name="description"
                                rows="4"><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-3">
                            <label for="inventory-qty" class="form-label fw-bold">Inventory Qty</label>
                            <input type="number" class="form-control" id="inventory-qty" name="inventory-qty"
                                value="<?php echo htmlspecialchars($product['quantity'] ?? ''); ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="normal-threshold" class="form-label fw-bold">Normal Threshold Qty</label>
                            <input type="number" class="form-control" id="normal-threshold" name="normal-threshold"
                                value="<?php echo htmlspecialchars($product['normal_threshold'] ?? ''); ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="low-threshold" class="form-label fw-bold">Low Threshold Qty</label>
                            <input type="number" class="form-control" id="low-threshold" name="low-threshold"
                                value="<?php echo htmlspecialchars($product['low_threshold'] ?? ''); ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="price" class="form-label fw-bold">Price</label>
                            <input type="number" class="form-control" id="price" name="price" step="0.01"
                                value="<?php echo htmlspecialchars($product['price'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label for="image-url" class="form-label fw-bold">Image URL</label>
                            <input type="url" class="form-control" id="image-url" name="image-url"
                                value="<?php echo htmlspecialchars($image['img_path'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="row mt-5">
                        <div class="col text-start">
                            <input type="button" class="btn btn-danger" data-bs-toggle="modal"
                                data-bs-target="#delete-modal" value="Delete">
                        </div>
                        <div class="col-md col-12 text-end">
                            <button type="button" class="btn btn-light me-2" data-bs-toggle="modal"
                                data-bs-target="#cancelConfirmModal">Cancel</button>
                            <button type="button" class="btn btn-dark" data-bs-toggle="modal"
                                data-bs-target="#confirm-modal">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </main>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="delete-modal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-3">
                <div class="modal-header justify-content-center pb-1" style="border-bottom: none;">
                    <h5 class="modal-title text-danger text-center fs-3 fw-light" id="deleteModalLabel">CONFIRM PRODUCT
                        DELETION?</h5>
                </div>
                <div class="modal-body text-center">
                    Are you sure you want to delete this product?
                </div>
                <div class="modal-footer justify-content-center text-center pt-1" style="border-top: none;">
                    <form action="delete-product.php" method="post">
                        <input type="hidden" name="product-id" value="<?php echo htmlspecialchars($product_id); ?>">
                        <button type="button" class="btn btn-dark me-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger ms-3" id="confirmDelete">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Confirmation Modal -->
    <div class="modal fade" id="confirm-modal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-3">
                <div class="modal-header justify-content-center pb-1" style="border-bottom: none;">
                    <h5 class="modal-title text-success text-center fw-light" id="confirmModalLabel">CONFIRM PRODUCT
                        DETAILS?</h5>
                </div>
                <div class="modal-body text-center">
                    Are these details correct?
                </div>
                <div class="modal-footer justify-content-center text-center pt-1" style="border-top: none;">
                    <button type="button" class="btn btn-success me-3" id="confirmEdit">Save</button>
                    <button type="button" class="btn btn-danger ms-3" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Successful Modal -->
    <div class="modal fade" id="edit-success" tabindex="-1" aria-labelledby="editSuccessfulModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-3">
                <div class="modal-header justify-content-center pb-1" style="border-bottom: none;">
                    <h5 class="modal-title text-success text-center fs-3 fw-light" id="editSuccessfulModalLabel">CHANGES
                        SAVED</h5>
                </div>
                <div class="modal-body text-center">
                    The product has been edited successfully!
                </div>
                <div class="modal-footer justify-content-center text center pt-1" style="border-top: none;">
                    <a href="./inventory-overview.php" class="btn btn-dark">Go to Inventory</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Cancel Confirmation Modal -->
    <div class="modal fade" id="cancelConfirmModal" tabindex="-1" aria-labelledby="cancelConfirmModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header justify-content-center pb-1" style="border-bottom: none;">
                    <h5 class="modal-title text-danger text-center fs-3 fw-light" id="confirmModalLabel">CANCEL CHANGES?
                    </h5>
                </div>
                <div class="modal-body text-center">
                    Are you sure you want to cancel? Any unsaved changes will be lost.
                </div>
                <div class="modal-footer justify-content-center text center pt-1" style="border-top: none;">
                    <button type="button" class="btn btn-dark me-3" data-bs-dismiss="modal">Stay</button>
                    <a href="./inventory-overview.php" class="btn btn-danger ms-3">Leave</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Successful Modal -->
    <div class="modal fade" id="delete-success" tabindex="-1" aria-labelledby="deleteSuccessfulModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-3">
                <div class="modal-header justify-content-center pb-1" style="border-bottom: none;">
                    <h5 class="modal-title text-success text-center fs-3 fw-light" id="deleteSuccessfulModalLabel">
                        PRODUCT DELETED</h5>
                </div>
                <div class="modal-body text-center">
                    The product has been deleted successfully!
                </div>
                <div class="modal-footer justify-content-center text-center pt-1" style="border-top: none;">
                    <a href="./inventory-overview.php" class="btn btn-dark">Go to Inventory</a>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const confirmEditBtn = document.getElementById('confirmEdit');

        confirmEditBtn.addEventListener('click', function(e) {
            e.preventDefault();
            form.submit();
        });

        <?php if (isset($_SESSION['show_success_modal']) && $_SESSION['show_success_modal']): ?>
        var editSuccessModal = new bootstrap.Modal(document.getElementById('edit-success'));
        editSuccessModal.show();
        <?php
        // Clear the session variable
        unset($_SESSION['show_success_modal']);
        endif;
        ?>

        <?php if (isset($_SESSION['delete_success']) && $_SESSION['delete_success']): ?>
        var deleteSuccessModal = new bootstrap.Modal(document.getElementById('delete-success'));
        deleteSuccessModal.show();
        <?php
        // Clear the session variable
        unset($_SESSION['delete_success']);
        endif;
        ?>
    });
    </script>

    <?php
    include '../partials/admin-footer.php';
    ob_end_flush();
    ?>
</body>

</html>