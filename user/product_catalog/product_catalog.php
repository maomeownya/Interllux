<?php
include '../../database/dbconnect.php';  // Ensure correct path to DB connection

// Fetch sorting filter from request
$sort_order = $_GET['sort_order'] ?? 'ASC';
if (!in_array($sort_order, ['ASC', 'DESC'])) {
  $sort_order = 'ASC';
}

// Prepare and execute product query
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
  p.name ASC";
$stmt = $pdo->query($query);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch unique brands
$brandQuery = "SELECT DISTINCT brand FROM product ORDER BY brand ASC";
$brandStmt = $pdo->query($brandQuery);
$brands = $brandStmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">
            <!--This is a property of PLSP-CCST BSIT-3B SY 2024-2025 -->
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
  <title>Catalog - Interllux</title>
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
  </style>
</head>

<body class="">
  <?php
  include '../../user/component/navbar.php';
  ?>

  <div class="container mt-3">
    <!-- Sorting -->
    <div class="row sorting-bar pb-2 pt-3">
      <div class="col-12 d-flex justify-content-center gap-2">
        <!-- Sort By Dropdown -->
        <select id="sortOptions" class="form-select w-auto rounded-0 shadow-none border-black mt-5 " aria-label="Sort By">
          <option value="default" selected>Sort By</option>
          <option value="price-asc">Price: Low to High</option>
          <option value="price-desc">Price: High to Low</option>
        </select>

        <!-- Filter by Brand Dropdown -->
        <select id="brandFilter" class="form-select w-auto rounded-0 shadow-none border-black mt-5" aria-label="Filter by Brand">
          <option value="all" selected>All Brands</option>
          <?php foreach ($brands as $brand) : ?>
            <option value="<?= htmlspecialchars($brand['brand']) ?>"><?= htmlspecialchars($brand['brand']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
  </div>


  <div id="product-list" class="row" style="padding: 0px 70px;">
    <?php if (!empty($products)) : ?>
      <?php foreach ($products as $product) : ?>
        <div class="col-6 col-md-3 product-card" data-brand="<?= htmlspecialchars($product['brand']) ?>">
          <div class="card mb-3 rounded-2">
            <a href="product_details.php?product_id=<?= htmlspecialchars($product['product_id']) ?>" class="card-link nav-link text-dark">
              <img src="<?= htmlspecialchars($product['img_path']) ?>" class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>">
              <div class="card-body">
                <h5 class="card-title" style="font-size: 14px;"><?= htmlspecialchars($product['name']) ?></h5>
                <p class="card-text"><?= htmlspecialchars($product['overview']) ?></p>
                <p class="fw-semibold m-1">₱<?= number_format($product['price'], 2) ?></p>
              </div>
            </a>
            <button class="btn btn-dark mt-3" style="border-top-left-radius: 0; border-top-right-radius: 0;">Add to Cart</button>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else : ?>
      <p class="col-12 text-center">No products found.</p>
    <?php endif; ?>
  </div>

  <!-- See More Button: Only show if more than 8 products -->
  <?php if (count($products) > 8) : ?>
    <div class="text-center mt-3">
      <button id="seeMoreButton" class="btn btn-dark">See More</button>
    </div>
  <?php endif; ?>

  <div id="footer">
    <script src="../../assets/js/footer.js"></script>
  </div>

  <script src="../../assets/Bootstrap/js/bootstrap.bundle.js"></script>
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      const productCards = Array.from(document.querySelectorAll(".product-card"));
      const sortOptions = document.getElementById("sortOptions");
      const brandFilter = document.getElementById("brandFilter");
      const productList = document.getElementById("product-list");
      const seeMoreButton = document.getElementById("seeMoreButton");

      let isExpanded = false;

      // Sorting Functionality
      sortOptions.addEventListener("change", function() {
        let sortedCards;

        switch (sortOptions.value) {
          case "price-asc":
            sortedCards = productCards.sort((a, b) => getPrice(a) - getPrice(b));
            break;
          case "price-desc":
            sortedCards = productCards.sort((a, b) => getPrice(b) - getPrice(a));
            break;
          default:
            sortedCards = productCards; // Default order
        }

        // Clear product list and re-append sorted cards
        productList.innerHTML = "";
        sortedCards.forEach(card => productList.appendChild(card));

        // Update visibility (for See More functionality)
        updateProductVisibility();
      });

      // Brand Filtering Functionality
      brandFilter.addEventListener("change", function() {
        const selectedBrand = brandFilter.value;

        productCards.forEach(card => {
          const brand = card.getAttribute("data-brand");
          card.style.display = (selectedBrand === "all" || brand === selectedBrand) ? "block" : "none";
        });
      });

      // Helper function to extract price
      function getPrice(card) {
        const priceText = card.querySelector(".fw-semibold").innerText.replace(/[₱,]/g, "");
        return parseFloat(priceText);
      }

      // Show/Hide Cards on See More Button Click
      function updateProductVisibility() {
        const itemsToShow = window.innerWidth >= 768 ? 8 : 4; // Adjust for screen size
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

      // Handle window resize for responsive behavior
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