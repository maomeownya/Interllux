<?php
include '../partials/admin-header.php';
include '../database/dbconnect.php';

// Initialize variables
$product = [];
$image = [];
$error_message = '';

// Check if product ID is provided
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($product_id > 0) {
    try {
        // Fetch product details
        $stmt = $pdo->prepare("SELECT * FROM product WHERE product_id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            $error_message = "Product not found.";
        } else {
            // Fetch image
            $image_stmt = $pdo->prepare("SELECT img_path FROM img WHERE product_id = ? LIMIT 1");
            $image_stmt->execute([$product_id]);
            $image = $image_stmt->fetch(PDO::FETCH_ASSOC);
        }
    } catch (PDOException $e) {
        $error_message = "Error fetching product details: " . $e->getMessage();
    }
} else {
    $error_message = "Invalid product ID.";
}

// Function to safely display product data
function display_data($key, $default = '') {
    global $product;
    return htmlspecialchars($product[$key] ?? $default);
}

// Function to determine product status <!--This is a property of PLSP-CCST BSIT-3B SY 2024-2025 -->

function get_product_status($quantity, $low_threshold) {
    if ($quantity <= 0) {
        return "Out of Stock";
    } elseif ($quantity <= $low_threshold) {
        return "Low Stock";
    } else {
        return "In Stock";
    }
}
?>

<main class="p-2 px-4">
    <section id="product-details">
        <div class="content p-4">
            <?php if ($error_message): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error_message; ?>
            </div>
            <?php else: ?>
            <div class="row align-items-center">
                <div class="col-12 col-md-6">
                    <h1 class="fw-bold mb-0">Product Details</h1>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-4 mb-4">
                    <div class="col-auto">
                        <img src="<?php echo $image['img_path'] ?? '../assets/image/placeholder-item.png'; ?>"
                            class="img-fluid border" alt="Product Image">
                    </div>
                </div>

                <div class="col-md-8 mb-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="product-name" class="form-label fw-bold">Product Name</label>
                            <input type="text" class="form-control bg-white" id="product-name"
                                value="<?php echo display_data('name'); ?>" disabled>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="brand-name" class="form-label fw-bold">Brand Name</label>
                            <input type="text" class="form-control bg-white" id="brand-name"
                                value="<?php echo display_data('brand'); ?>" disabled>
                        </div>
                    </div>
                    <div class="row mt-2 gx-1">
                        <div class="col mb-3">
                            <label for="description" class="form-label fw-bold">Description</label>
                            <textarea class="form-control bg-white" id="description" rows="4"
                                disabled><?php echo display_data('description'); ?></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="category" class="form-label fw-bold">Category</label>
                            <input type="text" class="form-control bg-white" id="category"
                                value="<?php echo display_data('category'); ?>" disabled>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="condition" class="form-label fw-bold">Condition</label>
                            <input type="text" class="form-control bg-white" id="condition"
                                value="<?php echo display_data('condition'); ?>" disabled>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="color" class="form-label fw-bold">Color</label>
                            <input type="text" class="form-control bg-white" id="color"
                                value="<?php echo display_data('color'); ?>" disabled>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="material" class="form-label fw-bold">Material</label>
                            <input type="text" class="form-control bg-white" id="material"
                                value="<?php echo display_data('material'); ?>" disabled>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="product-id" class="form-label fw-bold">Product ID</label>
                    <input type="number" class="form-control bg-white" id="product-id"
                        value="<?php echo display_data('product_id'); ?>" disabled>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="sku" class="form-label fw-bold">SKU</label>
                    <input type="text" class="form-control bg-white" id="sku" value="<?php echo display_data('sku'); ?>"
                        disabled>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="upc" class="form-label fw-bold">UPC</label>
                    <input type="text" class="form-control bg-white" id="upc" value="<?php echo display_data('upc'); ?>"
                        disabled>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="price" class="form-label fw-bold">Price</label>
                    <input type="number" class="form-control bg-white" id="price"
                        value="<?php echo display_data('price'); ?>" disabled>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="inventory-qty" class="form-label fw-bold">Inventory Qty</label>
                    <input type="number" class="form-control bg-white" id="inventory-qty"
                        value="<?php echo display_data('quantity', '0'); ?>" disabled>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="normal-threshold" class="form-label fw-bold">Normal Threshold</label>
                    <input type="number" class="form-control bg-white" id="normal-threshold"
                        value="<?php echo display_data('normal_threshold', '0'); ?>" disabled>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="low-threshold" class="form-label fw-bold">Low Threshold</label>
                    <input type="number" class="form-control bg-white" id="low-threshold"
                        value="<?php echo display_data('low_threshold', '0'); ?>" disabled>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="status" class="form-label fw-bold">Status</label>
                    <input type="text" class="form-control bg-white" id="status"
                        value="<?php echo get_product_status($product['quantity'], $product['low_threshold']); ?>"
                        disabled>
                </div>
                <div class="col-md-3 ms-auto d-flex align-items-end justify-content-md-end">
                    <a href="./inventory-overview.php" class="btn btn-sm btn-light me-2" style="width: 120px;">Back</a>
                    <a href="./edit-details.php?id=<?php echo $product_id; ?>" class="btn btn-dark btn-sm fw-medium"
                        style="width: 120px;">Edit</a>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php include '../partials/admin-footer.php'; ?>