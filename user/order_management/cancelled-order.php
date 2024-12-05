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

    <title>Cancelled - Interllux</title>
</head>

<body>
<?php
  include '../../user/component/navbar.php';
  ?>

    <!-- CANCELLED ORDER ITEMS -->
    <div class="container-fluid text-light  mt-md-4 mt-3 pt-5">
        <div class="row mb-3 bg-secondary py-2 pt-4 text-center">
            <h5 class="fw-bold">Order Cancelled</h5>
            <p>Your order has been cancelled. Please check the details below.</p>
        </div>
    </div>
    <hr class="w-100">
    <div class="container mt-4 vh-100">
        <div class="row">
            <div class="col-12">
                <div class="card d-flex flex-row p-2">
                    <img src="../../assets/image/ClassicFlapBag-4.jpg" class="img-fluid" alt="Classic Flap Bag"
                        style="width: 100px; height: 100px; object-fit: cover;">
                    <div class="card-body text-start p-0 ps-2 mt-2">
                        <p class="product-name fw-bold mb-0">Classic Flap Bag.</p>
                        <p class="mb-3">Color: Black</p>

                        <button type="button" class="view-order-btn btn btn-sm p-1"><u class="fw-bold">
                                <a href="details-cancelled_order.php" class="text-dark">
                                    View Order</u>
                            </a></button>

                    </div>
                    <div class="card-body p-0 pe-2 text-end price-info">
                        <p class="m-0 fw-bold">Waiting for Seller</p>
                        <p class="m-0 mt-4">Quantity: ×1</p>
                        <p class="m-0 mt-1 fw-bold">Php.900</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <div id="footer">
        <script src="../../assets/js/footer.js"></script>
    </div>
</body>

</html>
            <!--This is a property of PLSP-CCST BSIT-3B SY 2024-2025 -->