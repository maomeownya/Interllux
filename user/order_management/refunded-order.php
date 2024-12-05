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

    <title>Return/Refunded - Interllux</title>
</head>

<body>
<?php
  include '../../user/component/navbar.php';
  ?>

    <!-- CANCELLED ORDER ITEMS -->
    <div class="container-fluid text-light mt-md-4 mt-3 pt-5">
        <div class="row border bg-secondary shadow py-2 pt-4 text-center">
            <h5 class="fw-bold">Order Refunded</h5>
            <p>Your order has been refunded. Please check the details below.</p>
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

                        <button type="button" class="view-order-btn btn btn-sm p-1"><u class="fw-bold">
                                <a href="details-refunded.php" class="text-dark">
                                    View Order
                                </a></u></button>
                    </div>

            <!--This is a property of PLSP-CCST BSIT-3B SY 2024-2025 -->
                    <div class="card-body p-0 pe-2 text-end price-info">
                        <p class="m-0 fw-bold">To Ship</p>
                        <p class="m-0 mt-5 fw-bold">Php.1200</p>
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