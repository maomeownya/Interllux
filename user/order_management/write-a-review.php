<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Product Review</title>
  <!-- Bootstrap CSS -->
  <link href="../../assets/Bootstrap/css/bootstrap.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>
  <nav class="navbar bg-body-tertiary fixed-top shadow-sm py-0">
    <div class="container-fluid">
      <!-- Left Arrow Icon -->
      <a class="navbar-brand" href="tracker.php#to-rate">
        <button class="btn btn-sm px-1">
          <i class="bi bi-arrow-left-short text-dark fs-1 fw-bold" style="font-size: 1.5rem;"></i>
        </button>
      </a>

      <!-- Navbar Brand Name Centered -->
      <a class="navbar-brand  mx-auto dm-serif-display letter-spacing-1 text-dark" href="../../user/user_auth/index.php">
        <img src="../../assets/image/logo.png" alt="Interllux Logo" width="30" height="24">
        Interllux
      </a>
      <p class="navbar-brand">
        <i class="bi bi-arrow-left-short text-light" style="font-size: 1.5rem;"></i>
      </p>
    </div>
  </nav>

  <div class="container mt-5">
    <h2 class="text-center pt-5">Product Review</h2>
    <div id="product-info" class="card mb-4">
    <div class="card d-flex flex-row">
    <img src="../../assets/image/Bottega Veneta Cassette.png" class="img-fluid" alt="Bottega Veneta Cassette" style="width: 100px; height: 100px; object-fit: cover;">
    <div class="card-body text-start p-0 ps-2 mt-2">
                  <!--This is a property of PLSP-CCST BSIT-3B SY 2024-2025 -->
      <p class="fw-bold product-name mb-0">Bottega Veneta Cassette</p>
      <p class="mb-1">Color: Black</p>
      <p class="fw-bold text-success">Price: $2500</p>
    </div>
  </div>
    </div>
    <form id="reviewForm">


      <!-- Review Text -->
      <div class="mb-4">
        <label for="reviewText" class="form-label fw-bold">Your Review:</label>
        <textarea class="form-control shadow-none" id="reviewText" name="reviewText" rows="4"
          placeholder="Write your review here..."></textarea>
      </div>

      <!-- Submit Button -->
      <button type="submit" class="btn btn-dark w-100">Submit Review</button>
    </form>
  </div>

  <!-- Bootstrap JS -->
  <script src="../../assets/Bootstrap/js/bootstrap.bundle.js"></script>

  <script src="../../assets/js/review.js"></script>

</body>

</html>