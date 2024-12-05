<?php
include '../../database/dbconnect.php';

session_start();

if (!isset($_SESSION['id'])) {
    die("User not logged in.");
}

$user_id = $_SESSION['id'];

// Process account details update
if (isset($_POST['editEmail']) && isset($_POST['editPassword'])) {
    $email = $_POST['editEmail'];
    $password = $_POST['editPassword'];

    // Check if password matches confirm password
    if ($password !== $_POST['editConfirmPassword']) {
        die("Passwords do not match.");
    }

    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Update account details in the database
    $query = "UPDATE users SET email = :email, password = :password WHERE id = :user_id";
    try {
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':user_id', $user_id);
        if ($stmt->execute()) {
            echo "Update successful";
        } else {
            echo "Error updating account details";
        }
    } catch (PDOException $e) {
        die("Error updating account details: " . $e->getMessage());
    }
}

// Process personal details update
if (isset($_POST['editFirstName']) && isset($_POST['editLastName']) && isset($_POST['editContact'])) {
    $first_name = $_POST['editFirstName'];
    $last_name = $_POST['editLastName'];
    $contact = $_POST['editContact'];
    $street_address = $_POST['editStreet'];
    $barangay = $_POST['editBarangay'];
    $city = $_POST['editCity'];
    $province = $_POST['editProvince'];
    $zip_code = $_POST['editZipcode'];
    $country = $_POST['editCountry'];
    $shipping_region = $_POST['editRegion'];

    // Update personal details in the database
    $query = "UPDATE users SET 
              user_firstname = :first_name, 
              user_lastname = :last_name, 
              phone_num = :contact, 
              street_address = :street_address, 
              barangay = :barangay,
              shipping_region = :shipping_region 
              province = :province, 
              city = :city,
              zip_code = :zip_code, 
              country = :country, 
              WHERE id = :user_id";
    try {
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':contact', $contact);
        $stmt->bindParam(':street_address', $street_address);
        $stmt->bindParam(':barangay', $barangay);
        $stmt->bindParam(':shipping_region', $shipping_region);
        $stmt->bindParam(':province', $province);
        $stmt->bindParam(':city', $city);
        $stmt->bindParam(':zip_code', $zip_code);
        $stmt->bindParam(':country', $country);
        $stmt->bindParam(':user_id', $user_id);
        if ($stmt->execute()) {
            echo "Update successful";
        } else {
            echo "Error updating personal details";
        }
    } catch (PDOException $e) {
        die("Error updating personal details: " . $e->getMessage());
    }
}

exit(); // Prevent page redirection for debugging purposes
?>
            <!--This is a property of PLSP-CCST BSIT-3B SY 2024-2025 -->
