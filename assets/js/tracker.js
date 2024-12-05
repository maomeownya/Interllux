// ########## TRACKER JS ############### //
document.addEventListener('DOMContentLoaded', function() {
  // Initialize functions on page load
  handleTabSwitching();
  handleTruncateText();
  handleReviewSectionLoad();
  handleReviewButtonClick();
  handleOrderButtonClick();
  loadProductDetails();

  // Automatically switch to specific tab based on URL hash or default to 'to-pay'
  if (window.location.hash === '#to-rate') {
    switchTab('to-rate');
  } else {
    switchTab('to-pay');
  }
  //This is a property of PLSP-CCST BSIT-3B SY 2024-2025
  // Listen for window resize to truncate product names
  window.addEventListener('resize', truncateText);
  window.addEventListener('load', truncateText);

  // Function to switch between tabs
  function handleTabSwitching() {
    document.querySelectorAll('.nav-item').forEach(item => {
      item.addEventListener('click', function() {
        const target = this.dataset.target;
        switchTab(target);
      });
    });
  }

  function switchTab(target) {
    const navItems = document.querySelectorAll('.nav-item');
    const tabContents = document.querySelectorAll('.tab-content');

    if (!navItems.length || !tabContents.length) {
      console.error('Navigation items or tab contents are missing.');
      return;
    }

    navItems.forEach(item => item.classList.remove('active'));
    tabContents.forEach(tab => tab.classList.remove('active'));

    const targetNav = document.querySelector(`.nav-item[data-target="${target}"]`);
    const targetContent = document.getElementById(target);

    if (targetNav && targetContent) {
      targetNav.classList.add('active');
      targetContent.classList.add('active');
    } else {
      console.warn(`Target tab "${target}" not found.`);
    }
  }

  // Truncate product names for mobile view
  function handleTruncateText() {
    const productNames = document.querySelectorAll('.product-name');
    productNames.forEach(name => {
      if (window.innerWidth <= 768) { // Mobile view
        if (name.dataset.fullText === undefined) {
          name.dataset.fullText = name.textContent; // Store the full name
        }
        const fullText = name.dataset.fullText;
        name.textContent = fullText.length > 13 ? fullText.substring(0, 13) + '...' : fullText;
      } else {
        if (name.dataset.fullText) {
          name.textContent = name.dataset.fullText; // Restore full name on desktop
        }
      }
    });
  }

  function truncateText() {
    handleTruncateText();
  }

  // Load and update the Review Section when returning to Track Page
  function handleReviewSectionLoad() {
    const productData = JSON.parse(localStorage.getItem('reviewProduct'));
    if (productData && productData.reviewSubmitted) {
      const productCard = document.querySelector(`[data-id="${productData.id}"]`);
      if (productCard) {
        updateProductCard(productCard, productData);
      } else {
        console.warn(`Product card with ID ${productData.id} not found.`);
      }
    }
  }

  // Handle "Write a Review" button click
  function handleReviewButtonClick() {
    document.querySelectorAll('.write-review-btn').forEach(button => {
      button.addEventListener('click', function() {
        const productData = JSON.parse(this.getAttribute('data-product'));

        // Store the product details in localStorage
        localStorage.setItem('reviewProduct', JSON.stringify(productData));

        // Redirect to review.html
        window.location.href = 'write-a-review.php';
      });
    });
  }

  // Handle "View Order" button click
  function handleOrderButtonClick() {
    document.querySelectorAll('.view-order-btn').forEach(button => {
      button.addEventListener('click', function() {
        const productId = this.dataset.id;
        const productCategory = this.dataset.category;
        // Redirect to order_details.html with product data in the URL
        window.location.href = `order_details.php?product=${productId}&category=${productCategory}`;
      });
    });
  }

  // Update product card's review status
  function updateProductCard(productCard, productData) {
    // Update "Write a Review" button to "Edit Review"
    const reviewButton = productCard.querySelector('.write-review-btn');
    if (reviewButton) {
      reviewButton.textContent = 'Edit Review';
      reviewButton.classList.remove('btn-dark');
      reviewButton.classList.add('btn-primary');
    } else {
      console.warn('Review button not found in product card.');
    }
  }

  // Load saved product details for each section dynamically
  function loadProductDetails() {
    const tabs = ['payDetails', 'shipDetails', 'shippedDetails', 'reviewDetails'];

    tabs.forEach(tab => {
      const details = JSON.parse(localStorage.getItem(tab));
      if (details) {
        console.log(`Loaded ${tab}:`, details);
        // Optionally populate the tab section if needed
      }
    });
  }
});

// Navigate to order_details.html with the order status as a URL parameter
function viewOrder(status) {
  window.location.href = `order.php?status=${status}`;
}
// ########## END TRACKER JS ############### //
