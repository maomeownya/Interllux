document.addEventListener("DOMContentLoaded", function () {
  const initializeCartItems = () => {
    const cartItems = document.querySelectorAll(".cart-item");

    cartItems.forEach((item) => {
      const quantityInput = item.querySelector(".quantity-input");
      const decreaseButton = item.querySelector(".decrease-btn");
      const increaseButton = item.querySelector(".increase-btn");
      const productPriceElement = item.querySelector(".product-price");
      const unitPrice = parseFloat(productPriceElement.getAttribute("data-price") || 0); // Ensure numeric value

      const updatePrice = () => {
        const quantity = parseInt(quantityInput.value) || 1; // Default to 1
        const totalPrice = (unitPrice * quantity).toFixed(2);
        productPriceElement.textContent = `₱${totalPrice}`;

        // Recalculate subtotal and total
        const subtotalElement = document.getElementById("subtotal");
        const totalElement = document.getElementById("total");
        const newSubtotal = Array.from(cartItems).reduce((acc, cartItem) => {
          const itemQuantity = parseInt(cartItem.querySelector(".quantity-input").value) || 1;
          const itemUnitPrice = parseFloat(cartItem.querySelector(".product-price").getAttribute("data-price") || 0);
          return acc + itemQuantity * itemUnitPrice;
        }, 0);

        subtotalElement.textContent = `₱${newSubtotal.toFixed(2)}`;
        totalElement.textContent = `₱${(newSubtotal + 500).toFixed(2)}`; // Add shipping fee
      };

      decreaseButton.addEventListener("click", () => {
        let quantity = parseInt(quantityInput.value) || 1;
        if (quantity > 1) {
          quantityInput.value = quantity - 1;
        }
        updatePrice();
      });

      increaseButton.addEventListener("click", () => {
        let quantity = parseInt(quantityInput.value) || 1;
        quantityInput.value = quantity + 1;
        updatePrice();
      });

      quantityInput.addEventListener("input", () => {
        let quantity = parseInt(quantityInput.value);
        if (isNaN(quantity) || quantity < 1) {
          quantityInput.value = 1;
        }
        updatePrice();
      });

      updatePrice(); // Initialize the price on load
    });
  };

  initializeCartItems();
});
//This is a property of PLSP-CCST BSIT-3B SY 2024-2025