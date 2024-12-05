<?php include '../partials/admin-header.php'; ?>
<?php include '../database/dbconnect.php'; ?>
<?php

$results_per_page = 10;
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($current_page - 1) * $results_per_page;

// Search filter
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Add search filter
$search_filter = '';
if (!empty($search)) {
    $search = "%$search%"; // Add wildcard for LIKE clause
    $search_filter = "AND (t.transaction_id::TEXT ILIKE :search 
                           OR o.orders_id::TEXT ILIKE :search 
                           OR t.reason ILIKE :search)";
} else {
    // If no search term is provided, ensure that the search filter is empty
    $search_filter = '';
}

// Date filter
$selected_date_filter = isset($_GET['date_filter']) ? $_GET['date_filter'] : 'Select Date';
$date_filter = ''; // Default to no filter
// This is a property of PLSP-CCST BSIT-3B SY 2024-2025 -->

// Build the date filter based on the selected option
switch ($selected_date_filter) {
    case 'Today':
        $date_filter = "AND t.transac_date::date = CURRENT_DATE";
        break;
    case 'This Week':
        $date_filter = "AND t.transac_date >= CURRENT_DATE - INTERVAL '7 days'";
        break;
    case 'Last Week':
        $date_filter = "AND t.transac_date >= CURRENT_DATE - INTERVAL '14 days' 
                        AND t.transac_date < CURRENT_DATE - INTERVAL '7 days'";
        break;
    case 'This Month':
        $date_filter = "AND EXTRACT(MONTH FROM t.transac_date) = EXTRACT(MONTH FROM CURRENT_DATE) 
                        AND EXTRACT(YEAR FROM t.transac_date) = EXTRACT(YEAR FROM CURRENT_DATE)";
        break;
    case 'Last Month':
        $date_filter = "AND EXTRACT(MONTH FROM t.transac_date) = EXTRACT(MONTH FROM CURRENT_DATE - INTERVAL '1 month') 
                        AND EXTRACT(YEAR FROM t.transac_date) = EXTRACT(YEAR FROM CURRENT_DATE - INTERVAL '1 month')";
        break;
    case 'This Year':
        $date_filter = "AND EXTRACT(YEAR FROM t.transac_date) = EXTRACT(YEAR FROM CURRENT_DATE)";
        break;
    default:
        // No filter
        break;
}

// Main query
$query =
    "SELECT 
        t.transaction_id AS transaction_id, 
        o.orders_id AS order_no, 
        UPPER(t.transac_type::TEXT) AS type,   -- Cast enum to TEXT before applying UPPER
        t.transac_total_amount AS total, 
        t.transac_date AS date, 
        t.reason, 
        t.tracking_number 
    FROM transaction t
    LEFT JOIN orders o ON t.orders_id = o.orders_id
    WHERE t.transac_type IN ('return', 'refund') $date_filter $search_filter
    ORDER BY t.transac_date ASC  -- Order by transac_date in ascending order
    LIMIT :results_per_page OFFSET :offset
";

// Count total results for pagination
$count_query =
    " SELECT COUNT(*) AS total_count
    FROM transaction t
    LEFT JOIN orders o ON t.orders_id = o.orders_id
    WHERE t.transac_type IN ('return', 'refund') $date_filter $search_filter
";

// Prepare the queries separately
$stmt = $pdo->prepare($query);
$stmt_count = $pdo->prepare($count_query);

// Bind parameters for the main query (if any)
if (!empty($search)) {
    $stmt->bindValue(':search', $search);
}
$stmt->bindValue(':results_per_page', $results_per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

// Bind parameters for the count query (if any)
if (!empty($search)) {
    $stmt_count->bindValue(':search', $search);
}

// Execute the queries
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Execute the count query to get total count for pagination
$stmt_count->execute();
$total_count = $stmt_count->fetch(PDO::FETCH_ASSOC)['total_count'];
$total_pages = ceil($total_count / $results_per_page);
?>

<main class="p-2 px-4">
    <section id="order-return-refund">
        <div class="content p-4">
            <div class="row align-items-center">
                <div class="col-12 col-md-6">
                    <h1 class="fw-bold mb-0">Delivered Order List</h1>
                </div>

                <div class="col-12 col-md-6 d-flex justify-content-md-end justify-content-start mt-2 mt-md-0">
                    <div class="dropdown">
                        <!-- Set the button label dynamically  -->
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
                        <input type="text" name="search" class="form-control me-2" placeholder="Type your search here" value="" style="width: 280px;">
                        <button type="submit" class="btn btn-dark"> <i class="fa-solid fa-magnifying-glass"> </i></button>
                    </form>
                </div>
            </div>
        </div>

        <div class="content table-responsive p-4 pt-2">
            <table class="table table-hover fs-6">
                <thead>
                    <tr>
                        <th scope="col">TRANSAC NO.</th>
                        <th scope="col">ORDER NO.</th>
                        <th scope="col">TYPE</th>
                        <th scope="col">TOTAL</th>
                        <th scope="col">DATE </th>
                        <th scope="col">REASON</th>
                        <th scope="col">TRACKING NO.</th>
                    </tr>
                <tbody class="fw-light">
                    <?php if (!empty($results)): ?>
                        <?php foreach ($results as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['transaction_id']) ?></td>
                                <td><?= htmlspecialchars($row['order_no']) ?></td>
                                <td><?= htmlspecialchars($row['type']) ?></td>
                                <td>₱<?= htmlspecialchars(number_format($row['total'], 2)) ?></td>
                                <td><?= htmlspecialchars($row['date']) ?></td>
                                <td><?= htmlspecialchars($row['reason'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($row['tracking_number'] ?? 'N/A') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">No results found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot class="fw-light">
                    <tr>
                        <td colspan="7">
                            <div class="d-flex justify-content-between small">
                                <span>Showing <?php echo (($current_page - 1) * $results_per_page + 1) . ' to ' . min($current_page * $results_per_page, $total_count); ?> of <?php echo $total_count; ?> results</span>
                                <div>
                                    <?php if ($current_page > 1): ?>
                                        <a href="?page=<?php echo $current_page - 1; ?>&search=<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" class="btn btn-dark btn-sm">Previous</a>
                                    <?php endif; ?>
                                    <?php if ($current_page < $total_pages): ?>
                                        <a href="?page=<?php echo $current_page + 1; ?>&search=<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" class="btn btn-dark btn-sm">Next</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tfoot>
            </table>


            <?php include '../partials/admin-footer.php'; ?>