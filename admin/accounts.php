<?php
include '../partials/admin-header.php';
include '../database/dbconnect.php';

$search = $_GET['search'] ?? '';
$page = $_GET['page'] ?? 1; // Default to the first page
$results_per_page = 10; // Changes results per page
$offset = ($page - 1) * $results_per_page;
$date_filter = $_GET['date_filter'] ?? null;

// Define the date range based on the selected filter
switch ($date_filter) {
    case 'today':
        $start_date = date('Y-m-d');
        $end_date = date('Y-m-d');
        break;
    case 'this_week':
        $start_date = date('Y-m-d', strtotime('monday this week'));
        $end_date = date('Y-m-d', strtotime('sunday this week'));
        break;
    case 'last_week':
        $start_date = date('Y-m-d', strtotime('monday last week'));
        $end_date = date('Y-m-d', strtotime('sunday last week'));
        break;
    case 'this_month':
        $start_date = date('Y-m-01');
        $end_date = date('Y-m-t');
        break;
    case 'last_month':
        $start_date = date('Y-m-01', strtotime('first day of last month'));
        $end_date = date('Y-m-t', strtotime('last day of last month'));
        break;
    case 'this_quarter':
        $month = (int)date('m');
        if ($month <= 3) {
            $start_date = date('Y-01-01');
            $end_date = date('Y-03-31');
        } elseif ($month <= 6) {
            $start_date = date('Y-04-01');
            $end_date = date('Y-06-30');
        } elseif ($month <= 9) {
            $start_date = date('Y-07-01');
            $end_date = date('Y-09-30');
        } else {
            $start_date = date('Y-10-01');
            $end_date = date('Y-12-31');
        }
        break;
    case 'this_year':
        $start_date = date('Y-01-01');
        $end_date = date('Y-12-31');
        break;
    default:
        $start_date = null;
        $end_date = null;
        break;
}

try {
    // Count total rows for users
    $count_query = "SELECT COUNT(u.id) AS total
                    FROM users u
                    WHERE (CONCAT(u.user_firstname, ' ', u.user_lastname) ILIKE :search OR u.email ILIKE :search)";

    // Apply the date filter conditionally
    if ($start_date && $end_date) {
        $count_query .= " AND u.date_created BETWEEN :start_date AND :end_date";
    }

    $count_stmt = $pdo->prepare($count_query);
    $count_stmt->bindValue(':search', '%' . $search . '%');

    // Bind date filter values if present
    if ($start_date && $end_date) {
        $count_stmt->bindValue(':start_date', $start_date);
        $count_stmt->bindValue(':end_date', $end_date);
    }

    $count_stmt->execute();
    $total_results = $count_stmt->fetchColumn();
    //This is a property of PLSP-CCST BSIT-3B SY 2024-2025 

    // Fetch paginated results for users
    $query = "SELECT u.id, u.user_firstname, u.user_lastname, u.email, u.date_created
              FROM users u
              WHERE (CONCAT(u.user_firstname, ' ', u.user_lastname) ILIKE :search OR u.email ILIKE :search)";

    // Apply the date filter conditionally
    if ($start_date && $end_date) {
        $query .= " AND u.date_created BETWEEN :start_date AND :end_date";
    }

    $query .= " ORDER BY u.id ASC
                LIMIT :results_per_page OFFSET :offset";

    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':search', '%' . $search . '%');
    $stmt->bindValue(':results_per_page', $results_per_page, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

    // Bind date filter values if present
    if ($start_date && $end_date) {
        $stmt->bindValue(':start_date', $start_date);
        $stmt->bindValue(':end_date', $end_date);
    }

    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calculate pagination info
    $total_pages = ceil($total_results / $results_per_page);
} catch (PDOException $e) {
    echo "Failed to fetch data: " . $e->getMessage();
    exit;
}
?>

<main class="p-2 px-4">
    <section id="accounts">
        <div class="content p-4">
            <div class="row align-items-center">
                <div class="col-12 col-md-6">
                    <h1 class="fw-bold mb-0">Accounts</h1>
                </div>
                <div class="col-12 col-md-6 d-flex justify-content-md-end justify-content-start mt-2 mt-md-0">
                    <div class="dropdown">
                        <button class="btn dropdown-toggle btn-sm border border-dark-subtle" type="button"
                            id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">Select Date</button>
                        <ul class="dropdown-menu border border-dark-subtle" aria-labelledby="dropdownMenuButton">
                            <li><a class="dropdown-item"
                                    href="?search=<?= htmlspecialchars($search) ?>&page=<?= $page ?>&date_filter=today">Today</a>
                            </li>
                            <li><a class="dropdown-item"
                                    href="?search=<?= htmlspecialchars($search) ?>&page=<?= $page ?>&date_filter=this_week">This
                                    Week</a></li>
                            <li><a class="dropdown-item"
                                    href="?search=<?= htmlspecialchars($search) ?>&page=<?= $page ?>&date_filter=last_week">Last
                                    Week</a></li>
                            <li><a class="dropdown-item"
                                    href="?search=<?= htmlspecialchars($search) ?>&page=<?= $page ?>&date_filter=this_month">This
                                    Month</a></li>
                            <li><a class="dropdown-item"
                                    href="?search=<?= htmlspecialchars($search) ?>&page=<?= $page ?>&date_filter=last_month">Last
                                    Month</a></li>
                            <li><a class="dropdown-item"
                                    href="?search=<?= htmlspecialchars($search) ?>&page=<?= $page ?>&date_filter=this_quarter">This
                                    Quarter</a></li>
                            <li><a class="dropdown-item"
                                    href="?search=<?= htmlspecialchars($search) ?>&page=<?= $page ?>&date_filter=this_year">This
                                    Year</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="row align-items-center mt-3">
                <div class="search-bar col-auto">
                    <form action="" method="get" class="d-flex">
                        <input type="text" name="search" class="form-control me-2" placeholder="Type your search here"
                            value="<?= htmlspecialchars($search) ?>" style="width: 280px;">
                        <button type="submit" class="btn btn-dark"><i class="fa-solid fa-magnifying-glass"></i></button>
                    </form>
                </div>
            </div>
        </div>

        <div class="content table-responsive p-4 pt-2">
            <table class="table table-hover fs-6" id="accounts-table">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">FIRST NAME</th>
                        <th scope="col">LAST NAME</th>
                        <th scope="col">EMAIL</th>
                        <th scope="col">DATE CREATED</th>
                    </tr>
                </thead>
                <tbody class="fw-light">
                    <?php if (!empty($users)): ?>
                    <?php foreach ($users as $user): ?>
                    <tr style="cursor: pointer;" data-user-id="<?= htmlspecialchars($user['id']) ?>">
                        <td><?= htmlspecialchars($user['id']) ?></td>
                        <td><?= htmlspecialchars($user['user_firstname']) ?></td>
                        <td><?= htmlspecialchars($user['user_lastname']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= htmlspecialchars($user['date_created']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">No users found.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot class="fw-light">
                    <tr>
                        <td colspan="5">
                            <div class="d-flex justify-content-between small">
                                <span>Showing <?= $offset + 1 ?> to
                                    <?= min($offset + $results_per_page, $total_results) ?> of <?= $total_results ?>
                                    results</span>
                                <span>
                                    <?php if ($page > 1): ?>
                                    <a
                                        href="?search=<?= htmlspecialchars($search) ?>&page=<?= $page - 1 ?>&date_filter=<?= htmlspecialchars($date_filter) ?>">Previous</a>
                                    <?php endif; ?>
                                    <?php if ($page < $total_pages): ?>
                                    <a
                                        href="?search=<?= htmlspecialchars($search) ?>&page=<?= $page + 1 ?>&date_filter=<?= htmlspecialchars($date_filter) ?>">Next</a>
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

<!-- User details Modal -->
<div class="modal fade" id="user-modal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="false">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header" style="border-bottom: none;">
                <h5 class="modal-title fw-bold fs-3" id="userModalLabel">User Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body pt-0">
                <div class="container">
                    <p class="my-0" id="user-first-name"><strong>First Name:</strong> <span></span></p>
                    <p class="my-0" id="user-last-name"><strong>Last Name:</strong> <span></span></p>
                    <p class="my-0" id="user-email"><strong>Email:</strong> <span></span></p>
                    <p class="my-0" id="user-phone"><strong>Phone Number:</strong> <span></span></p>
                    <p class="my-0" id="user-date-created"><strong>Date Created:</strong> <span></span></p>
                </div>

                <h5 class="mt-4">Past Orders:</h5>
                <div class="table-responsive">
                    <table class="table table-bordered" style="min-width: 1000px;">
                        <thead>
                            <tr>
                                <th scope="col">Order Number</th>
                                <th scope="col">Product Name</th>
                                <th scope="col">Quantity</th>
                                <th scope="col">Total Amount</th>
                                <th scope="col">Order Date</th>
                                <th scope="col">Delivered Date</th>
                                <th scope="col">Order Status</th>
                            </tr>
                        </thead>
                        <tbody id="past-orders">
                            <!-- Past orders will be populated here -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const userRows = document.querySelectorAll('#accounts-table tbody tr');
    const userModal = new bootstrap.Modal(document.getElementById('user-modal'));

    userRows.forEach(row => {
        row.addEventListener('click', function() {
            const userId = this.getAttribute('data-user-id');
            fetchUserDetails(userId);
        });
    });

    function fetchUserDetails(userId) {
        fetch(`get-user-details.php?id=${userId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateUserModal(data.user, data.orders);
                    userModal.show();
                } else {
                    alert('Error fetching user details');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while fetching user details');
            });
    }

    function updateUserModal(user, orders) {
        document.querySelector('#user-first-name span').textContent = user.user_firstname;
        document.querySelector('#user-last-name span').textContent = user.user_lastname;
        document.querySelector('#user-email span').textContent = user.email;
        document.querySelector('#user-phone span').textContent = user.phone_num || '---';
        document.querySelector('#user-date-created span').textContent = user.date_created;

        const pastOrdersBody = document.getElementById('past-orders');
        pastOrdersBody.innerHTML = '';

        if (orders.length === 0) {
            pastOrdersBody.innerHTML = '<tr><td colspan="7" class="text-center">No orders found</td></tr>';
        } else {
            orders.forEach(order => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${order.orders_id}</td>
                    <td>${order.product_name}</td>
                    <td>${order.quantity}</td>
                    <td>₱${parseFloat(order.total_amount).toFixed(2)}</td>
                    <td>${order.order_date}</td>
                    <td>${order.delivered_date || '---'}</td>
                    <td>${order.order_status}</td>
                `;
                pastOrdersBody.appendChild(row);
            });
        }
    }
});
</script>

<?php include '../partials/admin-footer.php'; ?>