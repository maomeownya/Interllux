<?php
include('../../database/dbconnect.php');

try {
  // Total reviews count
  $sql_total_reviews = "SELECT COUNT(*) AS total_reviews FROM reviews";
  $stmt_total_reviews = $pdo->prepare($sql_total_reviews);
  if (!$stmt_total_reviews->execute()) {
    throw new Exception("Failed to execute total reviews query");
  }
  $total_reviews = $stmt_total_reviews->fetchColumn();

  // Latest reviews query
  $query = "SELECT r.date_created,
                 r.comment,
                 u.user_firstname AS customer_name,
                 p.name AS product_name,
                 img.img_path AS product_image
          FROM reviews r
          INNER JOIN users u ON r.users_id = u.id
          INNER JOIN orders o ON r.orders_id = o.orders_id
          INNER JOIN orders_item oi ON o.orders_id = oi.orders_id
          INNER JOIN product p ON oi.product_id = p.product_id
          INNER JOIN LATERAL (
              SELECT img_path 
              FROM img
              WHERE product_id = p.product_id 
              ORDER BY img_id ASC 
              LIMIT 1 OFFSET 1
          ) img ON TRUE
          ORDER BY r.date_created DESC
          LIMIT 5";

  $stmt = $pdo->prepare($query);
  if (!$stmt->execute()) {
    throw new Exception("Failed to execute latest reviews query");
  }
  $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  echo "PDO Error: " . $e->getMessage();
  exit;
} catch (Exception $e) {
  echo "General Error: " . $e->getMessage();
  exit;
}
?>




<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=Edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="../../assets/Bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <!-- <link rel="stylesheet" href="../../assets/css/landingpage.css"> -->

  <title>Interllux</title>

  <style>
    html {
      scroll-behavior: smooth;
    }

    body {
      overflow-x: hidden;
      margin: 0;
      padding: 0;
    }

    .carousel-control-prev-icons,
    .carousel-control-next-icons {

      /* Makes the icons black */
      background-size: 100%;
      /* Ensures the icon fills the button */
      background-image: none;
    }

    .carousel-control-prev-icons::before,
    .carousel-control-next-icons::before {
      content: '';
      /* Ensures no default arrows */
      border: solid black;
      /* Adds black color to the arrow */
      border-width: 0 5px 5px 0;
      display: inline-block;
      padding: 7px;
      margin-top: 6px;
    }

    .carousel-control-prev-icons::before {
      transform: rotate(135deg);
      /* Left arrow */
    }

    .carousel-control-next-icons::before {
      transform: rotate(-45deg);
      /* Right arrow */
    }

    .carousel img {
      width: 100%;
      height: auto;
      max-height: 400px;
      object-fit: cover;

    }

    .col-lg-5 img {
      width: 100%;
      height: 100%;
      max-height: 100%;
      /* Sets a consistent height for both images */
      object-fit: cover;
      /* Ensures the image covers the defined area without stretching */
    }


    .overlay {
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: rgba(0, 0, 0, 0.4);
      z-index: 1;
    }

    .floating-arrow {
      animation: float 2s ease-in-out infinite;
      z-index: 2;
      position: absolute;
      top: 390px;
      left: 600px;
    }

    @keyframes float {

      0%,
      100% {
        transform: translateY(0);
      }

      50% {
        transform: translateY(-10px);
      }
    }

    /* Container for horizontal scrolling */
    .scroll-container {
      overflow-x: auto;
      display: flex;
      gap: 15px;
      padding: 10px;
      scroll-snap-type: x mandatory;
    }

    /* Individual card styling */
    .review-card {
      flex: 0 0 250px;
      scroll-snap-align: start;
      background-color: #fff;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }


    /* Card content styling */
    .card-body .stars {
      color: #ffd700;
    }

    .card-text {
      display: -webkit-box;
      line-clamp: 3;
      -webkit-box-orient: vertical;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .shopnow {
      background-color: #ffffff15 !important;

    }


    .shopnow:hover {
      opacity: 80% !important;
    }
  </style>

</head>

<body>
  <?php
  include '../../user/component/navbar.php';
  ?>

  <!-- ######### LANDING PAGE SECTION ########-->
  <div class="container-fluid mt-5 pt-3">
    <div class="row position-relative">
      <div class="col-lg-7 d-lg-block d-none p-0 pe-2 ">
        <div id="carouselExampleAutoplaying" class="carousel slide" data-bs-ride="carousel">
          <div class="carousel-inner">
            <div class="carousel-item active">
              <img src="../../assets/image/1.png" class="block w-100" alt="...">
            </div>
            <div class="carousel-item">
              <img src="../../assets/image/2.png" class="d-block w-100" alt="...">
            </div>
            <div class="carousel-item">
              <img src="../../assets/image/3.png" class="d-block w-100" alt="...">
            </div>
          </div>
          <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="prev">
            <span class="carousel-control-prev-icons" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="next">
            <span class="carousel-control-next-icons" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
          </button>
        </div>
      </div>

      <div class="col-lg-5  p-0 position-relative d-flex justify-content-center align-items-center">
        <div class="overlay"></div>
          <img class="img-fluid" src="../../assets/image/banner.png" alt="">
        <a href="../../user/product_catalog/product_catalog.php" class="z-2 position-absolute">
          <button type="button" class="shopnow btn btn-light btn-lg rounded px-5 text-light">Shop Now</button>
        </a>
      </div>

      <a href="#reviews-section" class="floating-arrow z-3 d-none d-lg-block">
        <button type="button" class="btn btn-light rounded-circle shadow">
          <i class="fa fa-angle-down"></i>
        </button>
      </a>
    </div>
  </div>

  <!-- ######### REVIEWS SECTION ########-->
  <div class="container-fluid mt-3 pt-5" id="reviews-section">
    <h3 class="text-center pt-1 fw-bold">Our Customers, Our Voice</h3>
    <p class="text-center">from <?php echo htmlspecialchars($total_reviews); ?> reviews</p>

    <!-- Horizontal Scrollable Cards -->
    <div class="scroll-container container">
      <?php foreach ($reviews as $review) : ?>
        <div class="col-lg-4 col-md-6 mb-4">
          <div class="card">
            <img src="<?= htmlspecialchars($review['product_image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($review['product_name']) ?>">
            <div class="card-body text-center">
              <h5 class="card-title mt-2"><?= htmlspecialchars($review['customer_name']) ?></h5>
              <p class="fw-bold"><?= htmlspecialchars($review['product_name']) ?></p>
              <p class="card-text"><?= htmlspecialchars($review['comment']) ?></p>
              <p class="card-text"><strong>Date: </strong> <?= htmlspecialchars($review['date_created']) ?></p>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- READ MORE REVIEWS BUTTON -->
    <div class="text-center mt-3">
      <a href="../../user/order_management/review.php">
        <button class="btn btn-dark">Read More Reviews</button>
      </a>
    </div>
  </div>

  <!-- YOU MAY ALSO LIKE CONTAINER
  This is a property of PLSP-CCST BSIT-3B SY 2024-2025 -->

  <?php
  include '../../user/order_management/recommendation.php';
  ?>

  <div id="footer">
    <script src="../../assets/js/footer.js"></script>
  </div>

  <script src="../../assets/Bootstrap/js/bootstrap.bundle.js"></script>
</body>

</html>