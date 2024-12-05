<?php include '../partials/admin-header.php'; ?>

<main class="p-2 px-4">
    <!-- Pending Orders Section -->
    <section id="pending-orders">
        <div class="content p-4 pb-0">
            <h1 class="fw-bold mb-0">Pending Orders</h1>
            <div class="container-fluid mt-2" style="max-height: 200px; overflow-y: auto;">
                <ul class="list-unstyled">
                    <!-- Static Order Item -->
                    <li class="list-group-item list-group-item-secondary rounded-pill my-2 p-2 px-4 d-flex justify-content-between align-items-center"> New Order #12345
                        <div>
                            <button type="button" class="btn btn-success btn-sm fw-semibold" data-bs-toggle="modal"
                                data-bs-target="#approveModal">APPROVE</button>
                            <button type="button" class="btn btn-danger btn-sm fw-semibold" data-bs-toggle="modal"
                                data-bs-target="#declineModal">DECLINE</button>
                        </div>
                    </li>

                    <!-- Approve Modal -->
                    <!--This is a property of PLSP-CCST BSIT-3B SY 2024-2025 -->
                    <div class="custom-modal modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header justify-content-center pb-1">
                                    <h5 class="modal-title text-dark text-center fs-3 fw-bold" id="approveModalLabel">
                                        Order Confirmation
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <label class="form-label fw-bold me-2">Customer's Name:</label>
                                    <span>John Doe</span>

                                    <ol class="list-group list-group-numbered mt-2">
                                        <li class="list-group-item d-flex justify-content-between align-items-start">
                                            <div class="me-auto">
                                                <div>Product 1</div>
                                                <small>Price: 100 x 2</small>
                                            </div>
                                            <span class="badge bg-secondary rounded-pill">Subtotal: 200.00</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-start">
                                            <div class="me-auto">
                                                <div>Product 2</div>
                                                <small>Price: 150 x 1</small>
                                            </div>
                                            <span class="badge bg-secondary rounded-pill">Subtotal: 150.00</span>
                                        </li>
                                    </ol>
                                    <p class="fw-bold mt-3">Shipping Fee: <span class="float-end">50.00</span></p>
                                    <p class="fw-bold">Total: <span class="float-end">400.00</span></p>
                                    <hr>

                                    <h4 class="fw-bold text-center">Payment Confirmation</h4>
                                    <form id="payment-form" action=" " method="POST" onsubmit="return validateForm()">
                                        <div class="mb-3">
                                            <label for="payment-method-12345" class="form-label fw-bold">
                                                Method:<span class="text-danger">*</span>
                                            </label>
                                            <select class="form-select" name="payment_method" id="payment-method-12345" required>
                                                <option value="">Select Payment Method</option>
                                                <option value="Online Banking">Online Banking</option>
                                                <option value="E-Wallet">E-Wallet</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="payment-account-12345" class="form-label fw-bold">
                                                Account:<span class="text-danger">*</span>
                                            </label>
                                            <input type="text" name="payment_account" id="payment-account-12345" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="payment-date-12345" class="form-label fw-bold">
                                                Payment Date:<span class="text-danger">*</span>
                                            </label>
                                            <input type="date" name="payment_date" id="payment-date-12345" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="payment-ref-12345" class="form-label fw-bold">
                                                Ref #: <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" name="payment_ref" id="payment-ref-12345" class="form-control" required>
                                        </div>

                                        <!-- Warning message -->
                                        <div id="warning-message" class="alert alert-danger" style="display: none;">
                                            Please fill in all the required fields before submitting.
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-dark btn-sm fw-semibold">Confirm Payment</button>
                                        </div>

                                        <!-- Hidden fields -->
                                        <input type="hidden" name="order_id" value="12345">
                                        <input type="hidden" name="user_id" value="1">
                                        <input type="hidden" name="total_amount" value="400.00">
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                </ul>
            </div>
        </div>

        <!-- Decline Modal -->
        <div class="custom-modal modal fade" id="declineModal" tabindex="-1" aria-labelledby="declineModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header justify-content-center pb-1">
                        <h5 class="modal-title text-dark text-center fs-3 fw-bold" id="declineModalLabel">
                            Order Cancellation
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <label class="form-label fw-bold me-2">Customer's Name:</label>
                        <span>Name</span>

                        <ol class="list-group list-group-numbered">
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="me-auto">
                                    <div>Product Name</div>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-secondary rounded-pill">xQuantity</span>
                                    <br>
                                    <span class="badge bg-secondary rounded-pill">₱Item Price</span>
                                </div>
                            </li>
                        </ol>

                        <p class="fw-bold mt-3">Shipping Fee: <span class="float-end">50.00</span></p>
                        <p class="fw-bold">Total: <span class="float-end">50.00</span></p>
                
                        <hr>

                        <!-- Reason for Cancellation -->
                        <form action=" " method="POST">
                            <input type="hidden" name="order_number" value="Order ID">

                            <div class="mb-3">
                                <label for="cancellation-reason" class="form-label fw-bold">Reason:</label>
                                <textarea class="form-control" name="reason" id="cancellation-reason" rows="4" placeholder="Reason for cancellation" required></textarea>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-dark btn-sm fw-semibold">Save</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </section>

    <hr class="border border-dark border-2 mt-2" style="opacity: 1;">

    <!-- Order Tracker Section -->
    <section id="order-tracker">
        <div class="content table-responsive p-4">
            <h1 class="fw-bold mb-0">Order Tracker</h1>
            <div class="search-bar col-auto">
                <form action="" method="get" class="d-flex my-3">
                    <input type="text" name="search" class="form-control me-2" placeholder="Type your search here"
                        value="Search Term" style="width: 280px;">
                    <button type="submit" class="btn btn-dark">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </form>
            </div>

            <table class="table table-hover fs-6">
                <thead>
                    <tr>
                        <th>ORDER ID</th>
                        <th>CUSTOMER</th>
                        <th>PRODUCT NAME</th>
                        <th>PRICE</th>
                        <th>STATUS</th>
                        <th>ACTION</th>
                    </tr>
                </thead>
                <tbody class="fw-light">
                    <tr>
                        <td>Order ID</td>
                        <td>Customer Name</td>
                        <td>Product Details</td>
                        <td>Total Amount</td>
                        <td>Order Status</td>
                        <td>
                            <a href="order-details.php" class="text-black fw-semibold text-decoration-underline">Edit</a>
                        </td>
                    </tr>
                </tbody>
                <tfoot class="fw-light">
                    <tr>
                        <td colspan="6">
                            <div class="d-flex justify-content-between small">
                                <span>Showing 1 to 1 of 1 results</span>
                                <span> Next <i class="fa-solid fa-chevron-right fa-2xs" style="color: #000000;"></i></span>
                            </div>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </section>
</main>

<?php include '../partials/admin-footer.php'; ?>

<script>
    var cancelModal = document.getElementById('cancel-modal');
    cancelModal.addEventListener('show.bs.modal', function(event) {

        var button = event.relatedTarget;


        var orderNumber = button.getAttribute('data-order-number');


        var orderMessage = cancelModal.querySelector('#order-number-message');
        orderMessage.textContent = 'Are you sure you want to cancel Order ' + orderNumber + '?';
    });


    var shippedModal = document.getElementById('Shipped-modal');
    shippedModal.addEventListener('show.bs.modal', function(event) {
        var button = event.relatedTarget;
        var orderNumber = button.getAttribute('data-order-number');
        var orderInput = shippedModal.querySelector('#order-number');
        orderInput.value = orderNumber;
    });

    var doneModal = document.getElementById('Done-modal');
    doneModal.addEventListener('show.bs.modal', function(event) {
        var button = event.relatedTarget;
    });