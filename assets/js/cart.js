
    // Add to cart functionality
    document.querySelector('.add-to-cart-button').addEventListener('click', function() {
        const productId = this.getAttribute('data-product-id'); // Get product ID from button attribute

        // Send an AJAX request to add_to_cart.php
        fetch('../../user/order_management/add-to-cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `product_id=${productId}` // Send product_id in the request body
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Product added to cart successfully!');
                // Optionally, update cart item count dynamically
                const cartCount = document.getElementById('cart-count'); // Example: Cart count badge
                if (cartCount) cartCount.textContent = data.total_items;
            } else {
                alert('Failed to add product to cart: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error adding to cart:', error);
            alert('Something went wrong!');
        });
    });
//This is a property of PLSP-CCST BSIT-3B SY 2024-2025
