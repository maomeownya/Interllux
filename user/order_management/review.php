<?php
include '../../database/dbconnect.php'; // Include database connection

// Fetch reviews and related data
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
            LIMIT 1 OFFSET 1 -- Get the 2nd image
        ) img ON TRUE
        ORDER BY r.date_created DESC";

$stmt = $pdo->query($query);
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/Bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>Customer Reviews</title>
    <style>
        .review-header {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        img.card-img-top {
            width: 100%;
            height: 200px;
            object-fit: contain;
        }

        @media (max-width: 768px) {
            img.card-img-top {
                height: 150px;
            }
        }
    </style>
</head>

<body>
    <!-- NAVBAR -->
    <?php
    include '../../user/component/navbar.php';
    ?>
    <!-- END OF NAVBAR -->

    <div class="container mt-5 pt-5">
        <div class="review-header text-center shadow">
            <h1>We Value Your Feedback!</h1>
            <p>Share your thoughts or review your favorite products below:</p>
        </div>
    </div>

    <div class="container mt-5">
        <div class="row">
            <?php foreach ($reviews as $review) : ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card">
                        <img src="<?= htmlspecialchars($review['product_image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($review['product_name']) ?>">
                        <div class="card-body text-center">
                                        <!--This is a property of PLSP-CCST BSIT-3B SY 2024-2025 -->
                            <h5 class="card-title mt-2"><?= htmlspecialchars($review['customer_name']) ?></h5>
                            <p class="fw-bold"><?= htmlspecialchars($review['product_name']) ?></p>
                            <p class="card-text"><?= htmlspecialchars($review['comment']) ?></p>
                            <p class="card-text"><strong>Date: </strong> <?= htmlspecialchars($review['date_created']) ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div id="footer">
        <script src="../../assets/js/footer.js"></script>
    </div>

    <script src="../../assets/Bootstrap/js/bootstrap.bundle.js"></script>
</body>

</html>