
// ########## REVIEW JS ########## //

// Load product data from localStorage
const productData = JSON.parse(localStorage.getItem('reviewProduct'));
if (productData) {
  document.getElementById('product-info').innerHTML = `
  <div class="card d-flex flex-row">
    <img src="${productData.image}" class="img-fluid" alt="${productData.name}" style="width: 100px; height: 100px; object-fit: cover;">
    <div class="card-body text-start p-0 ps-2 mt-2">
      <p class="fw-bold product-name mb-0">${productData.name}</p>
      <p class="mb-1">Color: ${productData.color}</p>
      <p class="fw-bold text-success">Price: $${productData.price}</p>
    </div>
  </div>
`;
}
//This is a property of PLSP-CCST BSIT-3B SY 2024-2025
// Handle form submission
document.getElementById('reviewForm').addEventListener('submit', function (e) {
  e.preventDefault();
  alert('Review has been submitted.');

  // Save review status and text
  productData.reviewSubmitted = true;
  productData.reviewText = document.getElementById('reviewText').value; 
  localStorage.setItem('reviewProduct', JSON.stringify(productData));

  // Redirect to tracker.html
  window.location.href = '../../user/order_management/tracker.php';
});

// ########## REVIEW JS ########## //