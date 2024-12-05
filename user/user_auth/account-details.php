<?php
include '../../database/dbconnect.php';

session_start();

if (!isset($_SESSION['id'])) {
  die("User not logged in.");
}
$user_id = $_SESSION['id'];

$query = "SELECT email, user_firstname, user_lastname, phone_num, shipping_region, street_address, barangay, city, province, zip_code, country FROM users WHERE id = :user_id";

try {
  // Prepare the SQL statement
  $stmt = $pdo->prepare($query);
  $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
  $stmt->execute();
  $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
  if (!$user_data) {
    die("User details not found.");
  }
} catch (PDOException $e) {
  die("Error fetching user details: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profile Information</title>
  <link rel="stylesheet" href="../../assets/Bootstrap/css/bootstrap.css">
  <script src="../../assets/Bootstrap/js/bootstrap.bundle.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

  <style>
    .profile-background {
      background: url('../../assets/image/profile-background.jpeg') no-repeat center center;
      background-size: cover;
      width: 100%;
      height: 150px;
    }

    footer a {
      color: white !important;
    }
  </style>
</head>

<body>
  <div id="navbar">
    <script src="../../assets/js/navbar.js"></script>
  </div>

  <!-- PROFILE BACKGROUND-->
  <div class="profile-background container-fluid pt-3"></div>

  <div class="container-fluid px-3">
    <div class="container-fluid  d-flex justify-content-end mb-3 mt-3">
      <!-- EDIT BUTTONS -->
      <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#editEmailModal">
        Edit Account Details
      </button>
      <button class="btn btn-light border-dark ms-4" data-bs-toggle="modal" data-bs-target="#editDetailsModal">
        Edit Personal Details
      </button>
    </div>
  </div>
  <hr class="mt-1">

  <div class="container">
    <!-- DISPLAY ACCOUNT DETAILS -->
    <h5 class="mb-4 mt-3">Account Details</h5>
    <form>
      <div class="row mb-4">
        <!-- DISPLAY EMAIL -->
        <div class="col-md-6">
          <label for="email" class="form-label">Email</label>
          <input type="text" id="email" class="form-control" value="<?php echo htmlspecialchars($user_data['email']); ?>" disabled>
        </div>
        <!-- DISPLAY PASSWORD -->
        <div class="col-md-6">
          <label for="password" class="form-label">Password</label>
          <input type="password" id="password" class="form-control" value="password123" disabled>
        </div>
      </div>
      <hr>

      <!-- DISPLAY PERSONAL INFORMATION -->
      <h5 class="mt-3 mb-3">Personal Information</h5>
      <div class="row">
        <!-- DISPLAY FIRST NAME -->
        <div class="col-md-4 mb-4">
          <label for="firstName" class="form-label">First Name</label>
          <input type="text" id="firstName" class="form-control" value="<?php echo htmlspecialchars($user_data['user_firstname']); ?>" disabled>
        </div>

        <!-- DISPLAY LAST NAME-->
        <div class="col-md-4 mb-4">
          <label for="lastName" class="form-label">Last Name</label>
          <input type="text" id="lastName" class="form-control" value="<?php echo htmlspecialchars($user_data['user_lastname']); ?>" disabled>
        </div>

        <!-- DISPLAY CONTACT NUMBER-->
        <div class="col-md-4 mb-4">
          <label for="contact" class="form-label">Contact Number</label>
          <input type="tel" id="contact" class="form-control" value="<?php echo htmlspecialchars($user_data['phone_num']); ?>" disabled>
        </div>
      </div>

      <hr>

      <!-- DISPLAY ADDRESS INFO -->
      <h5 class="mt-3 mb-3">Shipping Address</h5>
      <div class="row">
        <!-- DISPLAY street_address ADDRESS-->
        <div class="col-md-4 mb-3">
          <label for="street_address" class="form-label">street_address Address</label>
          <input type="text" id="street_address" class="form-control" value="<?php echo htmlspecialchars($user_data['street_address']); ?>" disabled>
        </div>

        <!-- DISPLAY BARANGGAY -->
        <div class="col-md-4 mb-3">
          <label for="barangay" class="form-label">Barangay</label>
          <input type="text" id="barangay" class="form-control" value="<?php echo htmlspecialchars($user_data['barangay']); ?>" disabled>
        </div>

        <!-- DISPLAY CITY-->
        <div class="col-md-4 mb-3">
          <label for="city" class="form-label">City</label>
          <input type="text" id="city" class="form-control" value="<?php echo htmlspecialchars($user_data['city']); ?>" disabled>
        </div>
      </div>

      <div class="row">
        <!-- DISPLAY PROVINCE -->
        <div class="col-md-4 mb-3">
          <label for="province" class="form-label">Province</label>
          <input type="text" id="province" class="form-control" value="<?php echo htmlspecialchars($user_data['province']); ?>" disabled>
        </div>
        <!-- DISPLAY ZIP CODE -->
        <div class="col-md-4 mb-3">
          <label for="zip_code" class="form-label">ZIP Code</label>
          <input type="number" id="zip_code" class="form-control" value="<?php echo htmlspecialchars($user_data['zip_code']); ?>" disabled>
        </div>

        <!-- DISPLAY COUNTRY -->
        <div class="col-md-4 mb-3">
          <label for="country" class="form-label">Country</label>
          <input type="text" id="country" class="form-control" value="<?php echo htmlspecialchars($user_data['country']); ?>" disabled>
        </div>
      </div>
    </form>
  </div>

<!--This is a property of PLSP-CCST BSIT-3B SY 2024-2025 -->

  <!-- EDIT EMAIL/PASSWORD -->
  <div class="modal fade" id="editEmailModal" tabindex="-1" aria-labelledby="editEmailModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editEmailModalLabel">Edit Account Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <!-- Form to Wrap the Inputs -->
          <form method="POST" action="account-update.php">
            <!-- Hidden Input for User ID -->
            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">

            <!-- EDIT EMAIL -->
            <div class="mb-3">
              <label for="editEmail" class="form-label">Email</label>
              <input type="email" id="editEmail" class="form-control shadow-none" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
            </div>

            <!-- EDIT PASSWORD -->
            <div class="mb-3">
              <label for="editPassword" class="form-label">Password</label>
              <input type="password" id="editPassword" class="form-control shadow-none" name="password" required>
            </div>

            <!-- CONFIRM PASSWORD -->
            <div class="mb-3">
              <label for="editConfirmPassword" class="form-label">Confirm Password</label>
              <input type="password" id="editConfirmPassword" class="form-control shadow-none" name="confirm_password" required>
            </div>

            <!-- BUTTONS SAVE/CANCEL -->
            <div class="modal-footer">
              <button type="button" class="btn btn-light border-dark" data-bs-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-dark">Save Changes</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>


  <!-- EDIT PERSONAL DETAILS -->
  <div class="modal fade" id="editDetailsModal" tabindex="-1" aria-labelledby="editDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editDetailsModalLabel">Edit Personal Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <!-- Form to Wrap the Inputs -->
          <form method="POST" action="account-update.php">
            <!-- Hidden Input for User ID (if needed) -->
            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">

            <!-- EDIT FIRST NAME -->
            <div class="mb-3">
              <label for="editFirstName" class="form-label">First Name</label>
              <input type="text" id="editFirstName" class="form-control shadow-none" name="first_name" value="<?php echo htmlspecialchars($user_data['user_firstname']); ?>" required>
            </div>

            <!-- EDIT LAST NAME -->
            <div class="mb-3">
              <label for="editLastName" class="form-label">Last Name</label>
              <input type="text" id="editLastName" class="form-control shadow-none" name="last_name" value="<?php echo htmlspecialchars($user_data['user_lastname']); ?>" required>
            </div>

            <!-- EDIT CONTACT NUMBER -->
            <div class="mb-3">
              <label for="editContact" class="form-label">Contact Number</label>
              <input type="tel" id="editContact" class="form-control shadow-none" name="contact" value="<?php echo htmlspecialchars($user_data['phone_num']); ?>" required>
            </div>

            <!-- EDIT STREET ADDRESS -->
            <div class="mb-3">
              <label for="editStreet" class="form-label">Street Address</label>
              <input type="text" id="editStreet" class="form-control shadow-none" name="street" value="<?php echo htmlspecialchars($user_data['street_address']); ?>" required>
            </div>

            <!-- EDIT BARANGGAY -->
            <div class="mb-3">
              <label for="editBarangay" class="form-label">Barangay</label>
              <input type="text" id="editBarangay" class="form-control shadow-none" name="barangay" value="<?php echo htmlspecialchars($user_data['barangay']); ?>" required>
            </div>

            <!-- EDIT REGION -->
            <div class="mb-3">
              <label for="editRegion" class="form-label">Region</label>
              <select id="editRegion" class="form-control shadow-none border-secondary" name="region" required>
                <option value="Metro Manila" <?php echo ($user_data['shipping_region'] === 'Metro Manila') ? 'selected' : ''; ?>>Metro Manila</option>
                <option value="Luzon Province" <?php echo ($user_data['shipping_region'] === 'Luzon Province') ? 'selected' : ''; ?>>Luzon Province</option>
              </select>
            </div>

            <!-- EDIT PROVINCE -->
<div class="form-group" id="provinceField">
  <label for="editProvince" class="form-label">Province</label>
  <select id="editProvince" class="form-control shadow-none border-secondary" name="province" required>
    <option value="">Select Province</option>
  </select>
</div>


            <!-- EDIT CITY -->
            <div class="mb-3">
              <label for="editCity" class="form-label">City/Municipality</label>
              <input type="text" id="editCity" class="form-control shadow-none" name="city" value="<?php echo htmlspecialchars($user_data['city']); ?>" required>
            </div>

            <!-- EDIT ZIP CODE -->
            <div class="mb-3">
              <label for="editZipcode" class="form-label">ZIP Code</label>
              <input type="number" id="editZipcode" class="form-control shadow-none" name="zip_code" value="<?php echo htmlspecialchars($user_data['zip_code']); ?>" required>
            </div>

            <!-- EDIT COUNTRY -->
            <div class="mb-3">
              <label for="editCountry" class="form-label">Country</label>
              <input type="text" id="editCountry" class="form-control shadow-none border-secondary" name="country" value="<?php echo htmlspecialchars($user_data['country']); ?>" readonly>
            </div>

            <!-- BUTTONS SAVE/CANCEL -->
            <div class="modal-footer">
              <button type="button" class="btn btn-light border-dark" data-bs-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-dark">Save Changes</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div id="footer">
    <script src="../../assets/js/footer.js"></script>
  </div>

  <script>
    // Data for provinces in Luzon
    const provinces = {
      "Luzon Province": [
        "Abra", "Albay", "Aurora", "Bataan", "Batangas",
        "Benguet", "Bulacan", "Cagayan", "Camarines Norte", "Camarines Sur",
        "Catanduanes", "Cavite", "Ifugao", "Ilocos Norte", "Ilocos Sur",
        "Isabela", "Kalinga", "La Union", "Laguna", "Mountain Province",
        "Nueva Ecija", "Nueva Vizcaya", "Occidental Mindoro", "Oriental Mindoro", "Pampanga",
        "Pangasinan", "Quezon", "Quirino", "Rizal", "Romblon",
        "Sorsogon", "Tarlac", "Zambales"
      ]
    };

    // HTML elements
    const regionSelect = document.getElementById("editRegion");
    const provinceDropdown = document.getElementById("editProvince");
    const provinceField = document.getElementById("provinceField");

    // Function to populate provinces based on the selected region
    function populateProvinces(region) {
      // Clear previous options
      provinceDropdown.innerHTML = '<option value="">Select Province</option>';

      // Add new options if the region exists in the provinces data
      if (provinces[region]) {
        provinces[region].forEach(province => {
          const option = document.createElement("option");
          option.value = province;
          option.textContent = province;
          provinceDropdown.appendChild(option);
        });
      }
    }

    // Event listener for region selection change
    regionSelect.addEventListener("change", () => {
      const selectedRegion = regionSelect.value;

      // Show province dropdown only for "Luzon Province"
      if (selectedRegion === "Luzon Province") {
        provinceDropdown.parentElement.style.display = "block"; // Show the dropdown
        populateProvinces(selectedRegion);
      } else {
        provinceDropdown.parentElement.style.display = "none"; // Hide the dropdown
      }
    });

    // Initialize the dropdown visibility on page load
    window.onload = () => {
  const selectedRegion = regionSelect.value;
  if (selectedRegion === "Luzon Province") {
    provinceField.style.display = "block";
    populateProvinces(selectedRegion);
  } else {
    provinceField.style.display = "none";
  }

  // Pre-select province if already selected
  if (provinceDropdown && <?php echo json_encode($user_data['province']); ?>) {
    provinceDropdown.value = "<?php echo htmlspecialchars($user_data['province']); ?>";
  }
};
  </script>