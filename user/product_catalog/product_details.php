<?php
include '../../database/dbconnect.php';  // Ensure correct path to DB connection

$product_id = $_GET['product_id'] ?? 1; // Default to 1 if not provided

$query = "
SELECT 
    p.product_id,
    p.name,
    p.description,
    p.price,
    p.brand,
    p.category,
    p.material,
    MIN(i.img_path) AS img_path,
    STRING_AGG(i.img_path, ',') AS thumbnails
FROM 
    product AS p
JOIN 
    img AS i ON p.product_id = i.product_id
WHERE 
    p.product_id = :product_id
GROUP BY 
    p.product_id, p.name, p.description, p.price, p.brand, p.category, p.material";

$stmt = $pdo->prepare($query);
$stmt->execute(['product_id' => $product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if ($product):
    $thumbnails = explode(',', $product['thumbnails']); // Split thumbnails into an array
?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Product Details - Interllux</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
            <!--This is a property of PLSP-CCST BSIT-3B SY 2024-2025 -->
        <!-- BOOTSTRAP CSS -->
        <link rel="stylesheet" href="../../assets/Bootstrap/css/bootstrap.css">

        <!-- CUSTOM CSS -->
        <link rel="stylesheet" href="../../assets/css/cart/style.css">

        <!-- BOOTSTRAP ICON -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
        <style>
            /* Style the main image container */
            .product-image {
                display: flex;
                flex-direction: column;
                /* Stack the main image and thumbnails vertically */
                align-items: center;
                /* Center align the content */
                width: 60%;
                /* Optional: Adjust width for layout responsiveness */
                margin: 0 auto;
                /* Center the container */
            }

            /* Thumbnail container styling */
            .thumbnail-container {
                display: flex;
                /* Arrange thumbnails in a horizontal row */
                justify-content: center;
                /* Center thumbnails horizontally */
                gap: 15px;
                /* Larger space between thumbnails */
                margin-right: 300px;
            }

            /* Thumbnail image styling */
            .thumbnail-container img {
                max-width: 120px;
                /* Increase thumbnail width */
                max-height: 120px;
                /* Increase thumbnail height */
                cursor: pointer;
                /* Indicate thumbnails are clickable */
                border: 2px solid #ccc;
                /* Add a thicker border for emphasis */
                border-radius: 8px;
                /* Slightly rounded corners */
                transition: transform 0.2s;
                /* Smooth zoom effect on hover */
            }

            /* Hover effect for thumbnails */
            .thumbnail-container img:hover {
                transform: scale(1.15);
                /* Slight zoom effect on hover */
                border-color: #333;
                /* Change border color on hover */
            }


            @media (max-width: 768px) {
                .thumbnail-container img {
                    max-width: 100px;
                    /* Smaller thumbnails for small screens */
                    max-height: 100px;
                }

                .thumbnail-container {
                    gap: 10px;
                    /* Reduce gap on smaller screens */
                }
            }
        </style>


    </head>

    <body>
        <div id="navbar">
            <script src="../../assets/js/navbar.js"></script>
        </div>

        <div class="product-container">
            <div class="product-image text-center">
                <!-- Main Product Image -->
                <img id="main-product-image" src="<?= htmlspecialchars($thumbnails[0] ?? '../../assets/image/default.png') ?>" alt="Product Image" class="img-fluid mb-3">

                <!-- Thumbnails for additional images -->
                <div class="thumbnail-container d-flex justify-content-center gap-3 mt-3">
                    <?php foreach ($thumbnails as $thumbnail): ?>
                        <img src="<?= htmlspecialchars($thumbnail) ?>" alt="Thumbnail" class="thumbnail img-thumbnail" onclick="changeMainImage('<?= htmlspecialchars($thumbnail) ?>')">
                    <?php endforeach; ?>
                </div>

            </div>


            <div class="product-info">
                <h1 class="brand-name"><?= htmlspecialchars($product['brand']) ?></h1>
                <h2 class="product-name"><?= htmlspecialchars($product['name']) ?></h2>
                <p class="product-price">₱ <?= number_format($product['price'], 2) ?></p>
                <hr>
                <!-- Horizontal line to break the layout -->
                <div class="product-description">
                    <h3>Product Details<br></h3>
                    <p>
                        CATEGORY: <?= htmlspecialchars($product['category']) ?><br><br>
                        MATERIAL: <?= htmlspecialchars($product['material']) ?><br><br>
                        <?= nl2br(htmlspecialchars($product['description'])) ?><br><br>
                    </p>
                </div>
                <!-- Help Section -->
                <div class="action-section">
                    <button class="addtocart-button" data-product-id="<?= htmlspecialchars($product['product_id']) ?>">ADD TO CART</button>
                    <div class="help-section">
                        <h5>Need Help?</h5>
                        <p>Chat via WhatsApp: <a href="tel:+639616195710" class="text-dark">+639616195710</a></p>
                        <p>WhatsApp: <a href="https://wa.me/639616195710" target="_blank" class="text-dark">Interllux WhatsApp Link</a></p>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <p class="text-center">Product not found.</p>
    <?php endif; ?>


    <?php
  include '../../user/order_management/recommendation.php';
  ?>

    <div id="footer">
        <script src="../../assets/js/footer.js"></script>
    </div>

    <script>
        function changeMainImage(imageSrc) {
            const mainImage = document.getElementById('main-product-image');
            if (mainImage) {
                mainImage.src = imageSrc;
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/cart.js"></script>

    </body>

    </html>