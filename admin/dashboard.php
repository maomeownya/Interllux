<?php
include '../partials/admin-header.php';
include '../database/dbconnect.php';  // Ensure correct path to DB connection

// Function to get date range based on filter
function getDateRange($filter) {
    $end_date = date('Y-m-d');
    switch ($filter) {
        case 'today':
            $start_date = $end_date;
            break;
        case 'this-week':
            $start_date = date('Y-m-d', strtotime('last monday'));
            break;
        case 'last-week':
            $start_date = date('Y-m-d', strtotime('-2 week last monday'));
            $end_date = date('Y-m-d', strtotime('-1 week last sunday'));
            break;
        case 'this-month':
            $start_date = date('Y-m-01');
            break;
        case 'last-month':
            $start_date = date('Y-m-01', strtotime('last month'));
            $end_date = date('Y-m-t', strtotime('last month'));
            break;
        case 'this-quarter':
            $start_date = date('Y-m-d', strtotime(date('Y') . '-' . (floor((date('n') - 1) / 3) * 3 + 1) . '-01'));
            break;
        case 'this-year':
            $start_date = date('Y-01-01');
            break;
        default:
            $start_date = $end_date;
    }
    return [$start_date, $end_date];
}

$date_filter = isset($_GET['date-filter']) ? $_GET['date-filter'] : 'today';
list($start_date, $end_date) = getDateRange($date_filter);

// Fetch metrics from database This is a property of PLSP-CCST BSIT-3B SY 2024-2025 -->

$metrics_query = $pdo->prepare("
    SELECT 
        COALESCE(SUM(total_amount), 0) AS total_srp,
        COALESCE(SUM(total_amount - shipping_cost), 0) AS net_sales,
        COUNT(DISTINCT orders_id) AS orders_count,
        COALESCE(SUM((SELECT SUM(quantity) FROM orders_item WHERE orders_item.orders_id = orders.orders_id)), 0) AS items_sold
    FROM orders 
    WHERE order_date BETWEEN :start_date AND :end_date
");
$metrics_query->execute(['start_date' => $start_date, 'end_date' => $end_date]);
$metrics = $metrics_query->fetch(PDO::FETCH_ASSOC);

// Fetch total inventory SRP
$inventory_query = $pdo->query("SELECT SUM(price * quantity) AS total_inventory_srp FROM product");
$inventory_metrics = $inventory_query->fetch(PDO::FETCH_ASSOC);

// Fetch monthly quota and current sales
$current_year = date('Y');
$current_month = date('n');
$quota_query = $pdo->prepare("
    SELECT mq.quota_amount, COALESCE(SUM(o.total_amount), 0) AS current_sales
    FROM monthly_quota mq
    LEFT JOIN orders o ON EXTRACT(YEAR FROM o.order_date) = mq.year AND EXTRACT(MONTH FROM o.order_date) = mq.month
    WHERE mq.year = :year AND mq.month = :month
    GROUP BY mq.quota_amount
");
$quota_query->execute(['year' => $current_year, 'month' => $current_month]);
$quota_data = $quota_query->fetch(PDO::FETCH_ASSOC);

$monthly_quota = $quota_data['quota_amount'] ?? 0;
$current_sales = $quota_data['current_sales'] ?? 0;

// New query for daily sales chart
$daily_sales_query = $pdo->prepare("
    SELECT DATE(order_date) as date, SUM(total_amount) as total_sales
    FROM orders
    WHERE order_date BETWEEN :start_date AND :end_date
    GROUP BY DATE(order_date)
    ORDER BY DATE(order_date)
");
$daily_sales_query->execute(['start_date' => $start_date, 'end_date' => $end_date]);
$daily_sales_data = $daily_sales_query->fetchAll(PDO::FETCH_ASSOC);

// New query for top selling products
$top_products_query = $pdo->prepare("
    SELECT p.name, SUM(oi.quantity) as total_quantity
    FROM orders_item oi
    JOIN product p ON oi.product_id = p.product_id
    JOIN orders o ON oi.orders_id = o.orders_id
    WHERE o.order_date BETWEEN :start_date AND :end_date
    GROUP BY p.product_id
    ORDER BY total_quantity DESC
    LIMIT 5
");
$top_products_query->execute(['start_date' => $start_date, 'end_date' => $end_date]);
$top_products_data = $top_products_query->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
    .card {
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .card-info {
        height: 100%;
    }

    canvas {
        width: 100% !important;
        height: 300px !important;
    }
    </style>
</head>

<body>

    <main class="p-2 px-4">
        <section id="dashboard">
            <div class="content p-4 pb-2">
                <div class="row align-items-center">
                    <div class="col-12 col-md-6">
                        <h1 class="fw-bold mb-0">Dashboard</h1>
                    </div>

                    <div class="col-12 col-md-6 d-flex justify-content-md-end justify-content-start mt-2 mt-md-0">
                        <a href="./monthly-quota.php" class="btn btn-dark btn-sm me-2">Edit Monthly Quota</a>
                        <form action="" method="get">
                            <div class="dropdown">
                                <button class="btn dropdown-toggle btn-sm border border-dark-subtle" type="button"
                                    id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                    <?php echo ucwords(str_replace('-', ' ', $date_filter)); ?>
                                </button>
                                <ul class="dropdown-menu border border-dark-subtle"
                                    aria-labelledby="dropdownMenuButton">
                                    <li><button class="dropdown-item" type="submit" name="date-filter"
                                            value="today">Today</button></li>
                                    <li><button class="dropdown-item" type="submit" name="date-filter"
                                            value="this-week">This Week</button></li>
                                    <li><button class="dropdown-item" type="submit" name="date-filter"
                                            value="last-week">Last Week</button></li>
                                    <li><button class="dropdown-item" type="submit" name="date-filter"
                                            value="this-month">This Month</button></li>
                                    <li><button class="dropdown-item" type="submit" name="date-filter"
                                            value="last-month">Last Month</button></li>
                                    <li><button class="dropdown-item" type="submit" name="date-filter"
                                            value="this-quarter">This Quarter</button></li>
                                    <li><button class="dropdown-item" type="submit" name="date-filter"
                                            value="this-year">This Year</button></li>
                                </ul>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="content p-4 py-2">
                <p class="mb-2 fs-4 fw-medium">Sales Status</p>
                <div class="container">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card p-4 m-3">
                                <div class="card-info">
                                    <p class="fw-light m-0">Total Sales
                                        (<?php echo ucwords(str_replace('-', ' ', $date_filter)); ?>):</p>
                                    <p class="fs-2 fw-bold m-0">₱<?php echo number_format($metrics['total_srp'], 2); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card p-4 m-3">
                                <div class="card-info">
                                    <p class="fw-light m-0">Net Sales
                                        (<?php echo ucwords(str_replace('-', ' ', $date_filter)); ?>):</p>
                                    <p class="fs-2 fw-bold m-0">₱<?php echo number_format($metrics['net_sales'], 2); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="container">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card p-4 m-3">
                                <div class="card-info">
                                    <p class="fw-light m-0">Total Inventory Value</p>
                                    <p class="fs-2 fw-bold m-0">
                                        ₱<?php echo number_format($inventory_metrics['total_inventory_srp'], 2); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card p-4 m-3">
                                <div class="card-info">
                                    <p class="fw-light m-0">Monthly Quota Progress (<?php echo date('F Y'); ?>):</p>
                                    <p class="fs-2 fw-bold m-0">₱<?php echo number_format($current_sales, 2); ?> /
                                        ₱<?php echo number_format($monthly_quota, 2); ?></p>
                                    <div class="progress mt-2">
                                        <div class="progress-bar" role="progressbar"
                                            style="width: <?php echo $monthly_quota > 0 ? min(($current_sales / $monthly_quota) * 100, 100) : 0; ?>%;"
                                            aria-valuenow="<?php echo $monthly_quota > 0 ? ($current_sales / $monthly_quota) * 100 : 0; ?>"
                                            aria-valuemin="0" aria-valuemax="100">
                                            <?php echo $monthly_quota > 0 ? round(($current_sales / $monthly_quota) * 100, 1) : 0; ?>%
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="container">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card p-4 m-3">
                                <div class="card-info">
                                    <p class="fw-light m-0">Orders
                                        (<?php echo ucwords(str_replace('-', ' ', $date_filter)); ?>):</p>
                                    <p class="fs-2 fw-bold m-0"><?php echo $metrics['orders_count']; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card p-4 m-3">
                                <div class="card-info">
                                    <p class="fw-light m-0">Items Sold
                                        (<?php echo ucwords(str_replace('-', ' ', $date_filter)); ?>):</p>
                                    <p class="fs-2 fw-bold m-0"><?php echo $metrics['items_sold'] ?? 0; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="container mt-4">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card p-4 m-3">
                                <div class="card-info">
                                    <h5 class="card-title fw-bold mb-4">Daily Sales</h5>
                                    <canvas id="dailySalesChart"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card p-4 m-3">
                                <div class="card-info">
                                    <h5 class="card-title fw-bold mb-4">Top Selling Products</h5>
                                    <canvas id="topProductsChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const dateFilter = document.getElementById('dropdownMenuButton');
        const dropdownItems = document.querySelectorAll('.dropdown-item');

        dropdownItems.forEach(item => {
            item.addEventListener('click', function() {
                dateFilter.textContent = this.textContent;
            });
        });

        // Daily Sales Chart
        var dailySalesCtx = document.getElementById('dailySalesChart').getContext('2d');
        var dailySalesChart = new Chart(dailySalesCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_column($daily_sales_data, 'date')); ?>,
                datasets: [{
                    label: 'Daily Sales',
                    data: <?php echo json_encode(array_column($daily_sales_data, 'total_sales')); ?>,
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1,
                    fill: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Sales (PHP)'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // Top Products Chart
        var topProductsCtx = document.getElementById('topProductsChart').getContext('2d');
        var topProductsChart = new Chart(topProductsCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($top_products_data, 'name')); ?>,
                datasets: [{
                    label: 'Quantity Sold',
                    data: <?php echo json_encode(array_column($top_products_data, 'total_quantity')); ?>,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.6)',
                        'rgba(54, 162, 235, 0.6)',
                        'rgba(255, 206, 86, 0.6)',
                        'rgba(75, 192, 192, 0.6)',
                        'rgba(153, 102, 255, 0.6)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Quantity Sold'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    });
    </script>

</body>

</html>

<?php include '../partials/admin-footer.php';?>