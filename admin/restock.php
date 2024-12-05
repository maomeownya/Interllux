<?php
include '../partials/admin-header.php';
include '../database/dbconnect.php';

// Fetch pending restocks
$pendingRestockQuery = "SELECT p.product_id, p.name, p.quantity
                        FROM product p
                        WHERE p.quantity <= COALESCE(p.low_threshold, 5)
                        ORDER BY p.quantity ASC
                        LIMIT 10";
try {
    $pendingRestockStmt = $pdo->query($pendingRestockQuery);
} catch (PDOException $e) {
    die("Error in pending restock query: " . $e->getMessage());
}

// Fetch restock tracker data
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$itemsPerPage = 10;
$offset = ($page - 1) * $itemsPerPage;

$search = isset($_GET['search']) ? $_GET['search'] : '';
$searchCondition = $search ? "WHERE p.name ILIKE :search OR r.restock_id::TEXT ILIKE :search OR r.delivery_reference_number ILIKE :search" : "";
// This is a property of PLSP-CCST BSIT-3B SY 2024-2025 -->

$restockQuery = "SELECT r.*, p.name as product_name
                 FROM restock r
                 JOIN product p ON r.product_id = p.product_id
                 $searchCondition
                 ORDER BY r.restock_id DESC
                 LIMIT :limit OFFSET :offset";
try {
    $restockStmt = $pdo->prepare($restockQuery);
    if ($search) {
        $searchParam = "%$search%";
        $restockStmt->bindParam(':search', $searchParam, PDO::PARAM_STR);
    }
    $restockStmt->bindParam(':limit', $itemsPerPage, PDO::PARAM_INT);
    $restockStmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $restockStmt->execute();
} catch (PDOException $e) {
    die("Error in restock tracker query: " . $e->getMessage());
}

$totalRestocksQuery = "SELECT COUNT(*) as total FROM restock r JOIN product p ON r.product_id = p.product_id $searchCondition";
try {
    $totalRestocksStmt = $pdo->prepare($totalRestocksQuery);
    if ($search) {
        $totalRestocksStmt->bindParam(':search', $searchParam, PDO::PARAM_STR);
    }
    $totalRestocksStmt->execute();
    $totalRestocks = $totalRestocksStmt->fetchColumn();
} catch (PDOException $e) {
    die("Error in total restocks query: " . $e->getMessage());
}

$totalPages = ceil($totalRestocks / $itemsPerPage);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restock Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    .custom-modal .modal-content {
        border-radius: 15px;
    }

    .sidebar-submenu.show+ul {
        display: block !important;
    }

    .sidebar-submenu+ul {
        display: none;
    }
    </style>
</head>

<body>

    <main class="p-2 px-4">
        <section id="restock">
            <div class="content p-4 pb-0">
                <div class="row align-items-center">
                    <div class="col-12 col-md-6">
                        <h1 class="fw-bold mb-0">Pending Restock</h1>
                    </div>
                </div>

                <div class="container-fluid mt-2" id="pending-restock" style="max-height: 200px; overflow-y: auto;">
                    <ul class="list-unstyled">
                        <?php 
                        if ($pendingRestockStmt->rowCount() > 0) {
                            while ($row = $pendingRestockStmt->fetch(PDO::FETCH_ASSOC)): 
                        ?>
                        <li
                            class="list-group-item list-group-item-secondary rounded-pill my-2 p-2 px-4 d-flex justify-content-between align-items-center">
                            <?php echo htmlspecialchars($row['name']); ?> is low on stock
                            (<?php echo $row['quantity']; ?> left).
                            <button type="button" class="btn btn-success btn-sm fw-semibold restock-btn"
                                data-product-id="<?php echo $row['product_id']; ?>"
                                data-product-name="<?php echo htmlspecialchars($row['name']); ?>">RESTOCK</button>
                        </li>
                        <?php 
                            endwhile;
                        } else {
                            echo "<li>No products need restocking at this time.</li>";
                        }
                        ?>
                    </ul>
                </div>

                <hr class="border border-dark border-2 mt-2" style="opacity: 1;">
            </div>

            <div class="content p-4 pt-2">
                <div class="row align-items-center">
                    <div class="col-12 col-md-6">
                        <h1 class="fw-bold mb-0">Restock Tracker</h1>
                    </div>
                </div>

                <div class="row align-items-center mt-3">
                    <div class="search-bar col-auto">
                        <form action="" method="get" class="d-flex">
                            <input type="text" name="search" class="form-control me-2"
                                placeholder="Type your search here" value="<?php echo htmlspecialchars($search); ?>"
                                style="width: 280px;">
                            <button type="submit" class="btn btn-dark"> <i class="fa-solid fa-magnifying-glass">
                                </i></button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="content table-responsive p-4 pt-2">
                <table class="table table-hover fs-6">
                    <thead>
                        <tr>
                            <th scope="col">RESTOCK ID</th>
                            <th scope="col">PRODUCT ID</th>
                            <th scope="col">PRODUCT NAME</th>
                            <th scope="col">ADMIN ID</th>
                            <th scope="col">QUANTITY</th>
                            <th scope="col">DELIVERY DATE</th>
                            <th scope="col">STATUS</th>
                            <th scope="col">REFERENCE NO.</th>
                            <th scope="col">ACTION</th>
                        </tr>
                    </thead>
                    <tbody class="fw-light">
                        <?php while ($row = $restockStmt->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?php echo $row['restock_id']; ?></td>
                            <td><?php echo $row['product_id']; ?></td>
                            <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                            <td><?php echo $row['admin_id']; ?></td>
                            <td><?php echo $row['restock_quantity']; ?></td>
                            <td><?php echo $row['restock_delivery_date']; ?></td>
                            <td><?php echo $row['restock_delivery_status']; ?></td>
                            <td><?php echo $row['delivery_reference_number']; ?></td>
                            <td class="text-black fw-semibold text-decoration-underline edit-restock"
                                data-restock-id="<?php echo $row['restock_id']; ?>" style="cursor: pointer;">Edit</td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                    <tfoot class="fw-light">
                        <tr>
                            <td colspan="9">
                                <div class="d-flex justify-content-between small">
                                    <span>Showing <?php echo $offset + 1; ?> to
                                        <?php echo min($offset + $itemsPerPage, $totalRestocks); ?> of
                                        <?php echo $totalRestocks; ?> results</span>
                                    <span>
                                        <?php if ($page > 1): ?>
                                        <a
                                            href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>">Previous</a>
                                        <?php endif; ?>
                                        <?php if ($page < $totalPages): ?>
                                        <a
                                            href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>">Next</a>
                                        <?php endif; ?>
                                    </span>
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Restock Edit modal -->
            <div class="custom-modal modal fade" id="restock-edit-modal" tabindex="-1"
                aria-labelledby="restockModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content p-1">
                        <div class="modal-header justify-content-center pb-1" style="border-bottom: none;">
                            <h5 class="modal-title text-dark text-center fs-3 fw-bold" id="restockModalLabel">Restock
                                Edit</h5>
                        </div>
                        <div class="modal-body mx-5">
                            <form id="restock-form" action="update-restock.php" method="post">
                                <input type="hidden" id="restock-id" name="restock_id">
                                <input type="hidden" id="product-id" name="product_id">
                                <div class="mb-2 d-flex align-items-baseline">
                                    <label for="product-name" class="form-label fw-bold me-2">Product Name:</label>
                                    <span id="product-name" class="item-name"></span>
                                </div>
                                <div class="mb-2 d-flex align-items-center">
                                    <label for="quantity" class="form-label fw-bold me-2">Quantity:</label>
                                    <input type="number" class="form-control w-50" id="quantity" name="quantity"
                                        required>
                                </div>
                                <div class="mb-2 d-flex align-items-center">
                                    <label for="tracking-no" class="form-label fw-bold me-2">Tracking Number:</label>
                                    <input type="text" class="form-control w-50" id="tracking-no" name="tracking_no"
                                        required>
                                </div>
                                <div class="mb-2 d-flex align-items-center">
                                    <label for="status" class="form-label fw-bold me-2">Status:</label>
                                    <select class="form-select w-50" id="status" name="status" required>
                                        <option value="delivered">Delivered</option>
                                        <option value="in transit">In Transit</option>
                                        <option value="pending">Pending</option>
                                    </select>
                                </div>
                                <div class="mb-2 d-flex align-items-center">
                                    <label for="delivery-date" class="form-label fw-bold me-2">Delivery Date:</label>
                                    <input type="date" class="form-control w-50" id="delivery-date" name="delivery_date"
                                        required>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer pt-1" style="border-top: none;">
                            <button type="submit" form="restock-form" class="btn btn-light"
                                id="confirmEdit">Save</button>
                            <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize dropdowns
        var dropdowns = document.querySelectorAll('.dropdown-toggle');
        dropdowns.forEach(function(dropdown) {
            new bootstrap.Dropdown(dropdown);
        });

        // Ensure submenus are clickable and toggle correctly
        var subMenus = document.querySelectorAll('.sidebar-submenu');
        subMenus.forEach(function(subMenu) {
            subMenu.addEventListener('click', function(e) {
                e.preventDefault();
                this.classList.toggle('show');
                var subItems = this.nextElementSibling;
                if (subItems) {
                    subItems.classList.toggle('show');
                }
            });
        });

        // Restock page specific JavaScript
        const restockButtons = document.querySelectorAll('.restock-btn');
        const editButtons = document.querySelectorAll('.edit-restock');
        const restockModal = new bootstrap.Modal(document.getElementById('restock-edit-modal'));
        const restockForm = document.getElementById('restock-form');

        restockButtons.forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.getAttribute('data-product-id');
                const productName = this.getAttribute('data-product-name');
                document.getElementById('product-id').value = productId;
                document.getElementById('product-name').textContent = productName;
                document.getElementById('restock-id').value = ''; // New restock
                restockForm.reset();
                document.getElementById('status').value =
                'pending'; // Set default status for new restock
                document.getElementById('delivery-date').valueAsDate =
            new Date(); // Set default date to today
                restockModal.show();
            });
        });

        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const restockId = this.getAttribute('data-restock-id');
                fetch(`get-restock-details.php?id=${restockId}`)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('restock-id').value = data.restock_id;
                        document.getElementById('product-id').value = data.product_id;
                        document.getElementById('product-name').textContent = data
                            .product_name;
                        document.getElementById('quantity').value = data.restock_quantity;
                        document.getElementById('tracking-no').value = data
                            .delivery_reference_number;
                        document.getElementById('status').value = data
                            .restock_delivery_status.toLowerCase();
                        document.getElementById('delivery-date').value = data
                            .restock_delivery_date;
                        restockModal.show();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while fetching restock details.');
                    });
            });
        });

        restockForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('update-restock.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => {
                            throw err;
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        alert('Restock updated successfully!');
                        restockModal.hide();
                        location.reload(); // Reload the page to show updated data
                    } else {
                        throw new Error(data.message || 'Unknown error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while processing your request: ' + (error.message ||
                        'Unknown error'));
                });
        });
    });
    </script>

</body>

</html>

<?php
include '../partials/admin-footer.php';
?>