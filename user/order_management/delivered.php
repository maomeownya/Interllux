<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- BOOTSTRAP CSS -->
  <link rel="stylesheet" href="../../assets/Bootstrap/css/bootstrap.css">

  <!-- BOOTSTRAP ICON -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <!-- FONT AWESOME CDN -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

  <title>Delivered - Interllux</title>
</head>

<body>
<?php
  include '../../user/component/navbar.php';
  ?>

  <!-- CANCELLED ORDER ITEMS -->
  <div class="container-fluid text-light mt-md-4 mt-3 pt-5">
    <div class="row border bg-secondary shadow py-2 pt-4 text-center">
      <h5 class="fw-bold">Delivered</h5>
      <p>Your order has been delivered. Please check the details below.</p>
    </div>
  </div>
  <hr class="w-100">
  <div class="container mt-4 vh-100">
    <div class="row">
      <!-- PRODUCT 1 -->
      <div class="col-12">
        <div class="card d-flex flex-row p-2">
          <img src="../../assets/image/AlmaBB-1.jpg" class="img-fluid" alt="Alma BB"
            style="width: 100px; height: 100px; object-fit: cover;">
          <div class="card-body text-start p-0 ps-2 mt-2">
            <p class="product-name fw-bold mb-0">Alma BB</p>
            <p class="mb-3">Color: Brown</p>
            <!--This is a property of PLSP-CCST BSIT-3B SY 2024-2025 -->
            <button type="button" class="view-order-btn btn btn-sm p-1"><u class="fw-bold">
                <a href="details-delivered.php" class="text-dark">
                  View Order
                </a></u></button>
          </div>


          <div class="card-body p-0 pe-2 text-end price-info">
            <p class="m-0 fw-bold">To Ship</p>
            <p class="m-0 mt-4 fw-bold">Php.1200</p>

            <button type="button" class="rturn btn btn-outline-secondary btn-sm mt-1"><a href="../../user/user_auth/contact-us.php #cancel-policy" class="text-decoration-none text-dark">Return/Refund</a></button>
            <button type="button" class="btn btn-dark btn-sm" data-bs-toggle="modal" data-bs-target="#confirmModal">
              Order Received
            </button>

            <!-- Modal -->
            <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel"
              aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="confirmModalLabel">Confirm Action</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body text-center">
                    <!-- Centered Text -->
                    Are you sure you have received this order?
                    <p> Once confirmed, no refund or return will be allowed.</p>
                  </div>
                  <div class="modal-footer justify-content-center">
                    <!-- Cancel Button -->
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <!-- Confirm Button -->
                    <a href="../../user/order_management/tracker.php #to-rate" class="btn btn-dark">Confirm</a>
                  </div>
                </div>
              </div>
            </div>


            <!-- Include Bootstrap JS (Ensure It's Loaded) -->
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- FOOTER -->
  <div id="footer">
    <script src="../../assets/js/footer.js"></script>
    <script src="../../assets/js/tracker.js"></script>

  </div>
</body>

</html>