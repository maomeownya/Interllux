<?php
include '../../database/dbconnect.php';  // Ensure correct path to DB connection

// Fetch sorting filter from request
$sort_order = $_GET['sort_order'] ?? 'ASC';  // Default to ascending

// Validate sort order
if (!in_array($sort_order, ['ASC', 'DESC'])) {
  $sort_order = 'ASC';
}

// Prepare and execute the SQL query
$query = "
SELECT 
  p.product_id, 
  p.name, 
  p.overview, 
  p.price, 
  p.brand, 
  MIN(i.img_path) AS img_path
FROM 
  product AS p
JOIN 
  img AS i ON p.product_id = i.product_id
GROUP BY 
  p.product_id, p.name, p.overview, p.price, p.brand
ORDER BY 
  RANDOM()";

$stmt = $pdo->prepare($query);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../../assets/Bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="../../assets/css/product_catalog.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">
  <title>Thrifty</title>
  <style>
    /* CSS for smooth transition */
    .product-card {
      transition: opacity 3s ease, max-height 3s ease;
      overflow: hidden;
      opacity: 1;
      max-height: 500px;
      /* Adjust as needed */
    }

    .product-card.hidden {
      opacity: 0;
      max-height: 0;
      pointer-events: none;
    }

    .product-card:hover {
      cursor: pointer;
    }

    .sorting-bar {
      position: sticky;
      top: 60px;
      /* Adjust to match your header's height */
      z-index: 1000;
      /* Ensure it's above other elements */
      background-color: #f8f9fa;
      /* Ensure it's always visible with a background */
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
      /* Slight shadow for separation */
      padding-top: 0.75rem;
      padding-bottom: 0.75rem;
    }
  </style>

</head>

<body>
  <div class="container px-4 mt-5">
    <div class="row position-relative">
      <div class="col-4 pe-1 ps-4">
        <hr class="">
      </div>
      <div class="col-4 text-center p-0 mt-1 fs-6s">You May Also Like</div>
      <div class="col-4 ps-1 pe-4">
        <hr>
      </div>
    </div>
  </div>
  <div class="container">
    <div class="row g-3 mt-3" id="productGrid">
      <!--Displaying of fetched data-->
      <div id="product-list" class="row">
        <?php if (!empty($products)) : ?>
          <?php foreach (array_slice($products, 0, 4) as $product) : ?>
            <div class="col-6 col-md-3 product-card" data-brand="<?= htmlspecialchars($product['brand']) ?>">
              <div class="card mb-3 rounded-0">
                <a href="../product_catalog/product_details.php?product_id=<?= htmlspecialchars($product['product_id']) ?>" class="card-link nav-link text-dark">
                  <img src="<?= htmlspecialchars($product['img_path']) ?>" class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>">
                  <div class="card-body">
                    <h5 class="card-title" style="font-size: 14px;"><?= htmlspecialchars($product['name']) ?></h5>
                    <p class="card-text"><?= htmlspecialchars($product['overview']) ?></p>
                    <p class="fw-semibold m-1">₱<?= number_format($product['price'], 2) ?></p>
                  </div>
                </a>

                <button class="btn btn-dark btn-add mt-3">Add to Cart</button>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else : ?>
          <p class="col-12 text-center">No products found.</p>
        <?php endif; ?>
      </div>
    </div>

    <div class="text-center mt-3">
      <button id="seeMoreButton" class="btn btn-dark"><a href="../../user/product_catalog/product_catalog.php" class="nav-link">See More</a></button>
    </div>
  </div>

  <script src="../../assets/Bootstrap/js/bootstrap.bundle.js"></script>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      const productCards = document.querySelectorAll(".product-card");
      const seeMoreButton = document.getElementById("seeMoreButton");
      const itemsToShowDesktop = 4;
      const itemsToShowMobile = 4;
      let isExpanded = false;

      function updateProductVisibility() {
        const itemsToShow = window.innerWidth >= 768 ? itemsToShowDesktop : itemsToShowMobile;
        productCards.forEach((card, index) => {
          if (isExpanded || index < itemsToShow) {
            card.style.display = "block";
          } else {
            card.style.display = "none";
          }
        });
      }

      // Initial visibility setup
      updateProductVisibility();

      // Handle window resize for responsive update
      window.addEventListener("resize", updateProductVisibility);

      // Toggle visibility when "See More" is clicked
      seeMoreButton.addEventListener("click", () => {
        isExpanded = !isExpanded;
        seeMoreButton.textContent = isExpanded ? "See Less" : "See More";
        updateProductVisibility();
      });
    });
  </script>
</body>

</html>
            <!--This is a property of PLSP-CCST BSIT-3B SY 2024-2025 -->