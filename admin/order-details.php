<?php include '../partials/admin-header.php'; ?>

    <main class="p-2 px-4">
        <section id="order-details">
            <div class="content p-4">
                <div class="row align-items-center">
                    <div class="col-12 col-md-6">
                        <h1 class="fw-bold mb-0">Order Details</h1>
                    </div>
                </div>

                <!-- OVERVIEW -->
                 <!--This is a property of PLSP-CCST BSIT-3B SY 2024-2025 -->
                <div class="mt-4 mb-2">
                    <p class="fw-bold mt-2 mb-0 fs-4">Overview:</p>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <p><strong>Order No:</strong> ORD111</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Total Amount:</strong> ₱00.00</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Order Date:</strong> yyyy-mm-dd</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Tracking Number:</strong> ---</p>
                        </div>
                    </div>
                </div>

                <!-- ORDER TRACKER -->
                <div class="table-responsive">
                    <p class="fw-bold mb-2 fs-4">Order Tracker:</p>
                    <div class="col-md-10">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">ORDER STATUS</th>
                                    <th scope="col">DATE</th>
                                    <th scope="col">ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Confirmation Row -->
                                <tr>
                                    <td>Confirmation</td>
                                    <td>27/11/2024</td>
                                    <td>
                                        <button class="btn btn-secondary" disabled>Done</button>
                                    </td>
                                </tr>

                                <!-- Shipped Row -->
                                <tr>
                                    <td>Shipped</td>
                                    <td>27/11/2024</td>
                                    <td>
                                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#shippedModal">Update</button>
                                    </td>
                                </tr>

                                <!-- Delivered Row -->
                                <tr>
                                    <td>Delivered</td>
                                    <td>---</td>
                                    <td>
                                        <button class="btn btn-success" id="deliveredBtn" data-bs-toggle="modal" data-bs-target="#deliveredModal" disabled>Update</button>
                                    </td>
                                </tr>

                                <!-- Complete Row -->
                                <tr>
                                    <td>Complete</td>
                                    <td>---</td>
                                    <td>
                                        <button class="btn btn-success" disabled>Not Yet</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Shipped Modal -->
                <div class="modal fade" id="shippedModal" tabindex="-1" aria-labelledby="shippedModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content p-3">
                            <div class="modal-header justify-content-center pb-1" style="border-bottom: none;">
                                <h5 class="modal-title text-center fs-3 fw-bold" id="shippedModalLabel">Order #111 has been shipped</h5>
                            </div>
                            <div class="modal-body text-center fs-5 pt-0">
                                Enter tracking number:
                                <input type="text" class="form-control mt-2" id="trackingNumber" placeholder="Enter here" required>
                                <div id="trackingNumberWarning" class="text-danger fw-bold mt-2" style="display: none;">Tracking number is required!</div>
                            </div>
                            <div class="modal-footer justify-content-center text-center pt-1" style="border-top: none;">
                                <button type="button" class="btn btn-success me-3" id="saveShippedBtn">Save</button>
                                <button type="button" class="btn btn-danger ms-3" data-bs-dismiss="modal">Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Delivered Modal -->
                <div class="modal fade" id="deliveredModal" tabindex="-1" aria-labelledby="deliveredModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content p-3">
                            <div class="modal-header justify-content-center pb-1" style="border-bottom: none;">
                                <h5 class="modal-title text-center fs-3 fw-bold" id="deliveredModalLabel">Order #111</h5>
                            </div>
                            <div class="modal-body text-center fs-4 fw-bold pt-0">
                                Has the order been delivered?
                            </div>
                            <div class="modal-footer justify-content-center text-center pt-1" style="border-top: none;">
                                <button type="button" class="btn btn-success me-3" id="saveDeliveredBtn">Save</button>
                                <button type="button" class="btn btn-danger ms-3" data-bs-dismiss="modal">Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- CUSTOMER INFO -->
                <div class="my-3">
                    <p class="fw-bold mt-2 mb-0 fs-4">Customer Information:</p>
                    <div class="row mt-2">
                        <div class="col-md-4">
                            <p><strong>Name:</strong> Jane Doe</p>
                            <p><strong>Shipping Address:</strong> Carmelite St., Brgy. San Lucas 1, San Pablo City, Laguna, 4000</p>
                        </div>

                        <div class="col-md-4">
                            <p><strong>Method:</strong> E-Wallet</p>
                            <p><strong>Account:</strong> 123456789</p>
                            <p><strong>Payment Date:</strong> 27/11/2024</p>
                            <p><strong>Reference Number:</strong> 987654321</p>
                        </div>

                        <div class="col-md-4">
                            <p><strong>Subtotal:</strong> ₱00.00</p>
                            <p><strong>Shipping Fee:</strong> ₱00.00</p>
                            <p><strong>Total Amount:</strong> ₱00.00</p>
                        </div>
                    </div>
                </div>


                <!-- ORDER ITEMS -->
                <div class="table-responsive mt-4">
                <table class="table table-hover fs-6">
                    <thead>
                        <tr>
                            <th scope="col">PRODUCT NAME</th>
                            <th scope="col">QUANTITY</th>
                            <th scope="col">UNIT PRICE</th>
                            <th scope="col">ITEM PRICE</th>
                        </tr>
                    </thead>
                    <tbody class="fw-light">
                        <tr>
                            <td>Lv Bag</td>
                            <td>1</td>
                            <td>₱00.00</td>
                            <td>₱00.00</td>
                        </tr>
                        <tr>
                            <td>Channel Bag</td>
                            <td>1</td>
                            <td>₱00.00</td>
                            <td>₱00.00</td>
                        </tr>
                    </tbody>
                </table>
                </div>


                <!-- CUSTOMER REVIEWS -->
                <div class="mt-3">
                    <h5><strong>Customer Review</strong></h5>
                    <p class="col-md-10">Lorem ipsum dolor sit amet consectetur adipisicing elit. Odit commodi aperiam dolorum incidunt velit dolor veritatis a, nostrum facere laboriosam! Aliquam, temporibus cupiditate magni veritatis alias quis quasi maiores ullam?</p>
                </div>

                <div class="mt-4 text-end">
                    <a href="./order-overview.php" class="btn btn-dark">Back to Orders</a>
                </div>
            </div>
        </section>
    </main>

<?php include '../partials/admin-footer.php'; ?>


<script>
    // JavaScript logic to handle button state updates, modal closing, and form validation
    const deliveredBtn = document.getElementById('deliveredBtn'); // The Delivered button
    const trackingNumberInput = document.getElementById('trackingNumber');
    const trackingNumberWarning = document.getElementById('trackingNumberWarning'); // Warning message

    // Disable Delivered button initially
    deliveredBtn.disabled = true;

    document.getElementById('saveShippedBtn').addEventListener('click', function() {
        // Check if the tracking number is entered
        if (trackingNumberInput.value.trim() === '') {
            trackingNumberWarning.style.display = 'block'; // Show warning message
            return; // Prevent modal from closing if input is empty
        }

        // Hide warning if tracking number is provided
        trackingNumberWarning.style.display = 'none';

        const shippedRowAction = document.querySelector('tbody tr:nth-child(2) td:last-child button');
        shippedRowAction.classList.remove('btn-success');
        shippedRowAction.classList.add('btn-secondary');
        shippedRowAction.setAttribute('disabled', 'true');
        shippedRowAction.textContent = 'Done';

        // Close the modal using the Bootstrap Modal instance
        const shippedModal = bootstrap.Modal.getInstance(document.getElementById('shippedModal'));
        shippedModal.hide();

        // Enable the Delivered button when Shipped is done
        deliveredBtn.disabled = false;
    });

    document.getElementById('saveDeliveredBtn').addEventListener('click', function() {
    // Get the current date in 'dd/mm/yyyy' format
    const currentDate = new Date();
    const formattedDate = currentDate.toLocaleDateString('en-GB'); // Format as 'dd/mm/yyyy'

    // Set the "Delivered" row date to the current date
    document.querySelector('tbody tr:nth-child(3) td:nth-child(2)').textContent = formattedDate;

    // Change the button to "Done" and disable it
    const deliveredRowAction = document.querySelector('tbody tr:nth-child(3) td:last-child button');
    deliveredRowAction.classList.remove('btn-success');
    deliveredRowAction.classList.add('btn-secondary');
    deliveredRowAction.setAttribute('disabled', 'true');
    deliveredRowAction.textContent = 'Done';

    // Close the modal using the Bootstrap Modal instance
    const deliveredModal = bootstrap.Modal.getInstance(document.getElementById('deliveredModal'));
    deliveredModal.hide();
    });
</script>