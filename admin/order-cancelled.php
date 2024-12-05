<?php include '../partials/admin-header.php'; ?>
<?php include '../database/dbconnect.php'; ?>

<?php


// Pagination setup
$results_per_page = 10; // You can adjust the results per page here
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Get the page number from the URL, default to 1
$offset = ($page - 1) * $results_per_page;
$selected_date_filter = isset($_GET['date_filter']) ? $_GET['date_filter'] : ''; //Date Functionality
$selected_date_filter = isset($_GET['date_filter']) ? $_GET['date_filter'] : 'Select Date';
$date_filter = '';

// Search functionality
$search_term = isset($_GET['search']) ? '%' . $_GET['search'] . '%' : '%'; // Default to search all if nothing entered
//This is a property of PLSP-CCST BSIT-3B SY 2024-2025 -->

$selected_date_filter = isset($_GET['date_filter']) ? $_GET['date_filter'] : 'Select Date';
switch ($selected_date_filter) {
    case 'Today':
        $date_filter = "AND t.transac_date::date = CURRENT_DATE";
        break;
    case 'This Week':
        $date_filter = "AND t.transac_date >= CURRENT_DATE - INTERVAL '7 days'";
        break;
    case 'Last Week':
        $date_filter = "AND t.transac_date >= CURRENT_DATE - INTERVAL '14 days' AND t.transac_date < CURRENT_DATE - INTERVAL '7 days'";
        break;
    case 'This Month':
        $date_filter = "AND EXTRACT(MONTH FROM t.transac_date) = EXTRACT(MONTH FROM CURRENT_DATE) AND EXTRACT(YEAR FROM t.transac_date) = EXTRACT(YEAR FROM CURRENT_DATE)";
        break;
    case 'Last Month':
        $date_filter = "AND EXTRACT(MONTH FROM t.transac_date) = EXTRACT(MONTH FROM CURRENT_DATE - INTERVAL '1 month') AND EXTRACT(YEAR FROM o.order_date) = EXTRACT(YEAR FROM CURRENT_DATE - INTERVAL '1 month')";
        break;
    case 'This Quarter':
        // Assuming quarter is calculated using dates. Adjust to your needs.
        $quarter_start_date = date('Y-m-d', strtotime('first day of january'));
        $quarter_end_date = date('Y-m-d', strtotime('last day of march'));
        $date_filter = "AND t.transac_date BETWEEN '$quarter_start_date' AND '$quarter_end_date'";
        break;
    case 'This Year':
        $date_filter = "AND EXTRACT(YEAR FROM t.transac_date) = EXTRACT(YEAR FROM CURRENT_DATE)";
        break;
    default:
        // No date filter selected, do nothing
        break;
}
// Query for cancelled orders
$query = 
   " SELECT 
    o.orders_id, 
    CONCAT(u.user_firstname, ' ', u.user_lastname) AS customer_name,
    STRING_AGG(p.name || ' (Qty: ' || oi.quantity || 'x)', ', ') AS order_items,
    o.total_amount, 
    t.transac_date, 
    t.reason AS cancel_reason
FROM 
    orders o
JOIN 
    users u ON o.users_id = u.id
JOIN 
    orders_item oi ON o.orders_id = oi.orders_id
JOIN 
    product p ON oi.product_id = p.product_id
JOIN 
    transaction t ON o.orders_id = t.orders_id
WHERE 
    o.order_status = 'cancelled' AND
    o.order_date IS NOT NULL
    $date_filter
    AND (CAST(o.orders_id AS TEXT) LIKE :search OR 
         u.user_firstname LIKE :search OR 
         u.user_lastname LIKE :search)
GROUP BY 
    o.orders_id, u.user_firstname, u.user_lastname, o.total_amount, t.transac_date, t.reason
ORDER BY 
    o.order_date ASC
LIMIT :results_per_page OFFSET :offset
";

try {
    // Prepare the statement
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':search', $search_term, PDO::PARAM_STR);
    $stmt->bindParam(':results_per_page', $results_per_page, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    // Fetch the results
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Count the total number of cancelled orders for pagination
    $count_query = "
        SELECT COUNT(*) AS total
        FROM orders o
        WHERE o.order_status = 'cancelled' AND o.order_date IS NOT NULL";
    $count_stmt = $pdo->query($count_query);
    $total_orders = $count_stmt->fetch(PDO::FETCH_ASSOC)['total'];
    $total_pages = ceil($total_orders / $results_per_page);
} catch (PDOException $e) {
    echo "Failed to fetch data: " . $e->getMessage();
    exit;
}
?>

<main class="p-2 px-4">
    <section id="order-cancelled">
        <div class="content p-4">
            <div class="row align-items-center">
                <div class="col-12 col-md-6">
                    <h1 class="fw-bold mb-0">Cancelled Order List</h1>
                </div>

                <div class="col-12 col-md-6 d-flex justify-content-md-end justify-content-start mt-2 mt-md-0">
                <div class="dropdown">
                        <!-- Set the button label dynamically with PHP -->
                        <button class="btn dropdown-toggle btn-sm border border-dark-subtle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php echo htmlspecialchars($selected_date_filter); ?>
                        </button>
                        <ul class="dropdown-menu border border-dark-subtle" aria-labelledby="dropdownMenuButton">
                            <li><a class="dropdown-item" href="?date_filter=Today">Today</a></li>
                            <li><a class="dropdown-item" href="?date_filter=This Week">This Week</a></li>
                            <li><a class="dropdown-item" href="?date_filter=Last Week">Last Week</a></li>
                            <li><a class="dropdown-item" href="?date_filter=This Month">This Month</a></li>
                            <li><a class="dropdown-item" href="?date_filter=Last Month">Last Month</a></li>
                            <li><a class="dropdown-item" href="?date_filter=This Quarter">This Quarter</a></li>
                            <li><a class="dropdown-item" href="?date_filter=This Year">This Year</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="row align-items-center mt-3">
                <div class="search-bar col-auto">
                    <form action="" method="get" class="d-flex">
                        <input type="text" name="search" class="form-control me-2" placeholder="Type your search here" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" style="width: 280px;">
                        <button type="submit" class="btn btn-dark"> <i class="fa-solid fa-magnifying-glass"> </i></button>
                    </form>
                </div>
            </div>
        </div>

        <div class="content table-responsive p-4 pt-2">
            <table class="table table-hover fs-6">
                <thead>
                    <tr>
                        <th scope="col">ORDER NO.</th>
                        <th scope="col">CUSTOMER</th>
                        <th scope="col">ORDER ITEMS</th>
                        <th scope="col">TOTAL AMOUNT</th>
                        <th scope="col">ORDER DATE</th>
                        <th scope="col">CANCEL REASON</th>
                    </tr>
                </thead>
                <tbody class="fw-light">
                    <?php if (!empty($orders)): ?>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <th scope="row"><?php echo htmlspecialchars($order['orders_id']); ?></th>
                                <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                <td><?php echo htmlspecialchars($order['order_items']); ?></td>
                                <td>₱<?php echo number_format($order['total_amount'], 2); ?></td>
                                <td><?php echo htmlspecialchars($order['transac_date']); ?></td>
                                <td><?php echo htmlspecialchars($order['cancel_reason']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No cancelled orders found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot class="fw-light">
                    <tr>
                        <td colspan="6">
                            <div class="d-flex justify-content-between small">
                                <span>Showing <?php echo (($page - 1) * $results_per_page + 1) . ' to ' . min($page * $results_per_page, $total_orders); ?> of <?php echo $total_orders; ?> results</span>
                                <div>
                                    <?php if ($page > 1): ?>
                                        <a href="?page=<?php echo $page - 1; ?>&search=<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>"  class="btn btn-dark btn-sm">Previous</a>
                                    <?php endif; ?>
                                    <?php if ($page < $total_pages): ?>
                                        <a href="?page=<?php echo $page + 1; ?>&search=<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>"  class="btn btn-dark btn-sm">Next</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </section>
</main>



<?php include '../partials/admin-footer.php'; ?>