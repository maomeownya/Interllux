<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../../assets/Bootstrap/css/bootstrap.css">

    <!-- BOOTSTRAP ICON -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- FONT AWESOME CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <title>About Us - Interllux</title>

    <style>
        /* Custom styles for the About Us section */
        .about-us-section {
            background-color: #f8f9fa;
            padding: 50px 0;
        }

        .about-us-container {
            max-width: 900px;
            margin: 0 auto;
        }

        .about-us-heading {
            font-size: 2.5rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 20px;
        }

        .about-us-text {
            font-size: 1.1rem;
            color: #555;
            line-height: 1.8;
        }

        .about-us-text strong {
            color: #000;
        }

        about-us-image {
            width: 100%;
            max-width: 700px;
            /* Limit the width to make it smaller on desktop */
            border-radius: 10px;
        }

        /* Responsive styles for smaller screens */
        @media (max-width: 768px) {
            .about-us-image {
                max-width: 100%;
                /* Make the image take up full width on small screens */
            }
        }

        .about-us-footer {
            margin-top: 50px;
            font-size: 0.9rem;
            color: #777;
        }
    </style>
</head>

<body>
    <!-- NAVBAR -->
     <div id="navbar">
        <script src="../../assets/js/navbar.js"></script>
     </div>
    <!-- END NAVBAR -->

    <!-- ABOUT US SECTION -->
    <section class="about-us-section mt-5 pt-5">
        <div class="container about-us-container">
            <h2 class="about-us-heading text-center">About Us</h2>

            <p class="about-us-text">
                Interllux is your premier online destination for 100% authentic luxury goods. We specialize in offering
                a curated selection of designer handbags, clothing, shoes, and accessories—both brand new and
                pre-owned—at competitive prices.
            </p>
            <p class="about-us-text">
                Luxury items are an investment, and we understand that purchasing or consigning designer goods is a big
                decision. Our mission is to make that journey as smooth and trustworthy as possible.
            </p>
            <p class="about-us-text">
                At Interllux, we believe that trust is the foundation of every successful transaction. Buyers of luxury
                goods seek only genuine sellers, and consigners expect professionalism and credibility from consignment
                shops. That's why we guarantee that every product we sell is 100% authentic, or your money back. Our
                team of expert authenticators inspects each item carefully, scrutinizing every detail—from stamps and
                seams to stitching—ensuring that only the highest quality products make it to our online store.
            </p>
            <p class="about-us-text">
                We take pride in offering some of the best prices and a wide selection of luxury items, all backed by
                our dedicated customer service team, ready to assist you at every step. Whether you're purchasing your
                dream item or browsing our collection, we aim to make sure you feel confident in your choice.
            </p>
            <p class="about-us-text">
                <strong>Interllux</strong> operates exclusively online, bringing the luxury shopping experience to
                you—wherever you are—without the need for a physical store. We are an independent entity, separate from
                the manufacturers and brand owners of the designer products we offer, allowing us to deliver top-notch
                quality and unparalleled service.
            </p>

            <!-- Optional: Add an image to accompany the text This is a property of PLSP-CCST BSIT-3B SY 2024-2025 -->
            <div class="text-center mt-4">
                <img src="../../assets/image/logo.png" alt="Luxury Goods" class="about-us-image">
            </div>

            <!-- Footer message -->
            <div class="about-us-footer text-center">
                Welcome to Interllux—luxury, authenticated, and delivered with trust.
            </div>
        </div>
    </section>

    <div id="footer">
        <script src="../../assets/js/footer.js"></script>
    </div>
    <!-- BOOTSTRAP JS and dependencies -->
  <script src="../../assets/Bootstrap/js/bootstrap.bundle.js"></script>
</body>

</html>