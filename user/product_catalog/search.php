<?php
include '../../database/dbconnect.php';  // Ensure correct path to DB connection

// Fetch parameters
$search_keyword = $_GET['search'] ?? '';
$sort_order = $_GET['sort_order'] ?? 'ASC'; // Default sorting order
$items_to_show = $_GET['items_to_show'] ?? 8; // Default items to show

// Validate sort order
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
LEFT JOIN 
  img AS i ON p.product_id = i.product_id
WHERE 
  p.name ILIKE :keyword OR p.brand ILIKE :keyword
GROUP BY 
  p.product_id, p.name, p.overview, p.price, p.brand
ORDER BY 
  p.price $sort_order
LIMIT :items_to_show";

$stmt = $pdo->prepare($query);
$stmt->bindValue(':keyword', '%' . $search_keyword . '%', PDO::PARAM_STR);
$stmt->bindValue(':items_to_show', (int)$items_to_show, PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Count total products for "See More"
$countQuery = "
SELECT COUNT(DISTINCT p.product_id) 
FROM product AS p
LEFT JOIN img AS i ON p.product_id = i.product_id
WHERE p.name ILIKE :keyword OR p.brand ILIKE :keyword";
$countStmt = $pdo->prepare($countQuery);
$countStmt->execute([':keyword' => '%' . $search_keyword . '%']);
$total_products = $countStmt->fetchColumn();
?>
<!--This is a property of PLSP-CCST BSIT-3B SY 2024-2025 -->
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
  <title>Search Results - Interllux</title>
</head>

<body>
<div id="navbar">
    <script src="../../assets/js/navbar.js"></script>
</div>

<div class="container-fluid mt-4">
    <h1 class="text-center">Search Results</h1>
    <form method="get" id="searchForm" class="d-flex justify-content-center my-3">
        <input type="text" name="search" class="form-control w-50" placeholder="Search products..." value="<?= htmlspecialchars($search_keyword) ?>">
        <select name="sort_order" class="form-select w-auto mx-2" onchange="document.getElementById('searchForm').submit();">
            <option value="ASC" <?= $sort_order === 'ASC' ? 'selected' : '' ?>>Price: Low to High</option>
            <option value="DESC" <?= $sort_order === 'DESC' ? 'selected' : '' ?>>Price: High to Low</option>
        </select>
        <button type="submit" class="btn btn-dark">Search</button>
    </form>
</div>

<div id="product-list" class="row px-3">
  <?php if (!empty($products)): ?>
    <?php foreach ($products as $product): ?>
      <div class="col-6 col-md-3 product-card">
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
  <?php else: ?>
    <p class="col-12 text-center">No products match your search criteria.</p>
  <?php endif; ?>
</div>

<!-- See More Button -->
<?php if ($total_products > $items_to_show): ?>
    <div class="text-center mt-3">
        <form method="get">
            <input type="hidden" name="search" value="<?= htmlspecialchars($search_keyword) ?>">
            <input type="hidden" name="sort_order" value="<?= htmlspecialchars($sort_order) ?>">
            <input type="hidden" name="items_to_show" value="<?= $items_to_show + 8 ?>">
            <button type="submit" class="btn btn-dark">See More</button>
        </form>
    </div>
<?php endif; ?>

<div id="footer">
    <script src="../../assets/js/footer.js"></script>
</div>

<script src="../../assets/Bootstrap/js/bootstrap.bundle.js"></script>
</body>
</html>
