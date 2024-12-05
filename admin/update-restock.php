<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../database/dbconnect.php';

header('Content-Type: application/json');

function sendJsonResponse($success, $message = '', $data = null) {
    $response = [
        'success' => $success,
        'message' => $message,
        'data' => $data
    ];
    echo json_encode($response);
    exit;
}

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Validate and sanitize input This is a property of PLSP-CCST BSIT-3B SY 2024-2025 --
    $restockId = isset($_POST['restock_id']) && $_POST['restock_id'] !== '' ? intval($_POST['restock_id']) : null;
    $productId = isset($_POST['product_id']) ? intval($_POST['product_id']) : null;
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : null;
    $trackingNo = isset($_POST['tracking_no']) ? trim($_POST['tracking_no']) : null;
    $status = isset($_POST['status']) ? strtolower(trim($_POST['status'])) : null;
    $deliveryDate = isset($_POST['delivery_date']) ? trim($_POST['delivery_date']) : null;
    $adminId = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : 1; // Fallback to 1 if not set

    // Input validation
    if (!$productId || !$quantity || !$trackingNo || !$status || !$deliveryDate) {
        throw new Exception('Missing required fields');
    }

    // Validate status values
    $validStatuses = ['pending', 'in transit', 'delivered'];
    if (!in_array($status, $validStatuses)) {
        throw new Exception('Invalid status value');
    }

    $pdo->beginTransaction();

    // Store the old status if updating
    $oldStatus = null;
    if ($restockId !== null) {
        $oldStatusStmt = $pdo->prepare("SELECT restock_delivery_status FROM restock WHERE restock_id = $1");
        $oldStatusStmt->execute([$restockId]);
        $oldStatus = $oldStatusStmt->fetchColumn();
    }

    if ($restockId === null) {
        // Insert new restock
        $stmt = $pdo->prepare("
            INSERT INTO restock 
            (product_id, admin_id, restock_quantity, restock_delivery_date, restock_delivery_status, delivery_reference_number)
            VALUES ($1, $2, $3, $4, $5, $6)
            RETURNING restock_id
        ");
        $stmt->execute([$productId, $adminId, $quantity, $deliveryDate, $status, $trackingNo]);
    } else {
        // Update existing restock
        $stmt = $pdo->prepare("
            UPDATE restock SET
            restock_quantity = $1,
            restock_delivery_date = $2,
            restock_delivery_status = $3,
            delivery_reference_number = $4
            WHERE restock_id = $5
            RETURNING restock_id
        ");
        $stmt->execute([$quantity, $deliveryDate, $status, $trackingNo, $restockId]);
    }

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result === false) {
        throw new Exception("Failed to insert/update restock");
    }
    
    $newRestockId = $result['restock_id'];

    // Update product quantity only if status changed to delivered
    if ($status === 'delivered' && $oldStatus !== 'delivered') {
        $updateProductStmt = $pdo->prepare("
            UPDATE product 
            SET quantity = quantity + $1 
            WHERE product_id = $2
        ");
        $updateProductStmt->execute([$quantity, $productId]);
    }

    // Manual notification creation (since we can't modify the trigger)
    if ($status === 'delivered') {
        $notifStmt = $pdo->prepare("
            INSERT INTO notif (admin_id, recipient_type, message, notif_type, product_id)
            VALUES ($1, $2, $3, $4, $5)
        ");
        $message = "Restock delivery completed for product ID " . $productId;
        $notifStmt->execute([$adminId, 'admin', $message, 'restock', $productId]);
    }

    $pdo->commit();
    sendJsonResponse(true, 'Restock updated successfully', ['restock_id' => $newRestockId]);

} catch (PDOException $e) {
    if (isset($pdo)) {
        $pdo->rollBack();
    }
    error_log("Database Error: " . $e->getMessage());
    sendJsonResponse(false, 'Database error: ' . $e->getMessage());
} catch (Exception $e) {
    if (isset($pdo)) {
        $pdo->rollBack();
    }
    error_log("Error: " . $e->getMessage());
    sendJsonResponse(false, $e->getMessage());
}
?>