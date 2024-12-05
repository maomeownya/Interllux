<?php
include '../partials/admin-header.php';
include '../database/dbconnect.php';  // Ensure correct path to DB connection

// Get the selected number of items per page
$items_per_page = isset($_GET['items-filter']) ? intval($_GET['items-filter']) : 10;

// Get the current page number
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;

// Calculate the offset for the query
$offset = ($page - 1) * $items_per_page;

// Retrieve the search query
$search_query = isset($_GET['search']) ? $_GET['search'] : '';

// Fetch sum of (quantity * price) from the database
$inventory_sql = "SELECT SUM(quantity * price) AS total_inventory_price, SUM(quantity) as total_inventory_quantity FROM product";
$inventory_stmt = $pdo->query($inventory_sql);
$inventory_result = $inventory_stmt->fetch(PDO::FETCH_ASSOC);

$total_inventory_price = $inventory_result['total_inventory_price'] ?? 0;
$total_inventory_quantity = $inventory_result['total_inventory_quantity'] ?? 0;
//This is a property of PLSP-CCST BSIT-3B SY 2024-2025

// Fetch total number of products for pagination
$total_products_sql = "SELECT COUNT(*) as total_products FROM product WHERE (name ILIKE :search 
                OR brand ILIKE :search 
                OR category ILIKE :search 
                OR condition ILIKE :search)";
$total_products_stmt = $pdo->prepare($total_products_sql);
$total_products_stmt->execute(['search' => "%$search_query%"]);
$total_products = $total_products_stmt->fetchColumn();

// Fetch data for the current page
$product_sql = "SELECT product_id, name, category, brand, price, quantity, condition, color 
                FROM product 
                WHERE (name ILIKE :search 
                OR brand ILIKE :search 
                OR category ILIKE :search 
                OR condition ILIKE :search 
                OR color ILIKE :search)
                ORDER BY product_id
                LIMIT :limit OFFSET :offset";
$product_stmt = $pdo->prepare($product_sql);
$product_stmt->bindValue(':search', "%$search_query%", PDO::PARAM_STR);
$product_stmt->bindValue(':limit', $items_per_page, PDO::PARAM_INT);
$product_stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$product_stmt->execute();
$products = $product_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="p-2 px-4">
    <section id="inventory">
        <div class="content p-4">
            <div class="row align-items-center">
                <div class="col-12 col-md-6">
                    <h1 class="fw-bold mb-0">Store Inventory</h1>
                </div>

                <div class="col-12 col-md-6 d-flex justify-content-md-end justify-content-start mt-2 mt-md-0">
                    <form action=" " method="get">
                        <div class="dropdown">
                            <button class="btn dropdown-toggle btn-sm border border-dark-subtle" type="button"
                                id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">Items per
                                page</button>
                            <ul class="dropdown-menu border border-dark-subtle" aria-labelledby="dropdownMenuButton">
                                <li><button class="dropdown-item" type="submit" name="items-filter" value="10">10
                                        items per page</button></li>
                                <li><button class="dropdown-item" type="submit" name="items-filter" value="15">15
                                        items per page</button></li>
                                <li><button class="dropdown-item" type="submit" name="items-filter" value="20">20
                                        items per page</button></li>
                                <li><button class="dropdown-item" type="submit" name="items-filter" value="25">25
                                        items per page</button></li>
                                <li><button class="dropdown-item" type="submit" name="items-filter" value="30">30
                                        items per page</button></li>
                            </ul>
                        </div>
                    </form>
                </div>
            </div>

            <div class="cards">
                <div class="row g-3 mt-1">
                    <div class="col-md-6">
                        <div class="card p-2">
                            <div class="card-info">
                                <p class="fw-light m-0">Total Inventory Quantity:</p>
                                <p class="fs-2 fw-bold m-0"> <?php echo number_format($total_inventory_quantity); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card p-2">
                            <div class="card-info">
                                <p class="fw-light m-0">Total Inventory Price:</p>
                                <p class="fs-2 fw-bold m-0">₱<?php echo number_format($total_inventory_price, 2); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row align-items-center mt-3">
                <div class="search-bar col-auto">
                    <form action="" method="get" class="d-flex">
                        <input type="text" name="search" class="form-control me-2" placeholder="Type your search here"
                            value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
                            style="width: 280px;">
                        <button type="submit" class="btn btn-dark"> <i
                                class="fa-solid fa-magnifying-glass"></i></button>
                    </form>
                </div>
                <div class="col-auto ms-auto">
                    <a href="./add-product.php" class="btn btn-success mt-3 mt-sm-0 fw-medium">Add Item</a>
                </div>
            </div>

        </div>

        <!-- Store Inventory Table -->
        <div class="content table-responsive p-4 pt-2">
            <table class="table table-hover fs-6">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">PRODUCT NAME</th>
                        <th scope="col">BRAND</th>
                        <th scope="col">CONDITION</th>
                        <th scope="col">BAG TYPE</th>
                        <th scope="col">COLOR</th>
                        <th scope="col">PRICE</th>
                        <th scope="col">STATUS</th>
                        <th scope="col">ACTION</th>
                    </tr>
                </thead>
                <tbody class="fw-light">
                    <?php
                    foreach ($products as $row) {
                        // Determine the status based on quantity
                        if ($row['quantity'] == 0) {
                            $status = "No stock";
                        } elseif ($row['quantity'] <= 5) {
                            $status = "Low stock";
                        } else {
                            $status = "In stock";
                        }

                        echo "<tr onclick=\"window.location='./product-details.php?id=".$row['product_id']."';\" style=\"cursor: pointer;\">";
                        echo "<th scope=\"row\">".$row['product_id']."</th>";
                        echo "<td>".$row['name']."</td>";
                        echo "<td>".$row['brand']."</td>";
                        echo "<td>".$row['condition']."</td>";
                        echo "<td>".$row['category']."</td>";
                        echo "<td>".$row['color']."</td>";
                        echo "<td>".$row['price']."</td>";
                        echo "<td>".$status."</td>";
                        echo "<td><a href=\"./edit-details.php?id=".$row['product_id']."\" class=\"text-black fw-semibold\">Edit</a></td>";
                        echo "</tr>";
                    }
                    if (empty($products)) {
                        echo "<tr><td colspan='9'>No results found</td></tr>";
                    }
                    ?>
                </tbody>
                <tfoot class="fw-light">
                    <tr>
                        <td colspan="9">
                            <div class="d-flex justify-content-between small">
                                <span>Showing <?php echo ($offset + 1); ?> to
                                    <?php echo min($offset + $items_per_page, $total_products); ?> of
                                    <?php echo $total_products; ?> results</span>
                                <span>
                                    <?php if ($page > 1): ?>
                                    <a href="?page=<?php echo $page - 1; ?>&items-filter=<?php echo $items_per_page; ?>&search=<?php echo urlencode($search_query); ?>"
                                        class="btn btn-outline-dark btn-sm">Previous</a>
                                    <?php endif; ?>
                                    <?php if ($offset + $items_per_page < $total_products): ?>
                                    <a href="?page=<?php echo $page + 1; ?>&items-filter=<?php echo $items_per_page; ?>&search=<?php echo urlencode($search_query); ?>"
                                        class="btn btn-outline-dark btn-sm">Next</a>
                                    <?php endif; ?>
                                </span>
                            </div>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </section>
</main>

<?php include '../partials/admin-footer.php'; ?>