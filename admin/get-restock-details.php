<?php
include '../database/dbconnect.php';

header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $restockId = intval($_GET['id']);
    
    $query = "SELECT r.*, p.name as product_name 
              FROM restock r
              JOIN product p ON r.product_id = p.product_id
              WHERE r.restock_id = :restock_id";
    
    try {
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':restock_id', $restockId, PDO::PARAM_INT);
        $stmt->execute();
        
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Format the date to be compatible with the date input
            $row['restock_delivery_date'] = date('Y-m-d', strtotime($row['restock_delivery_date']));
            
            echo json_encode($row);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Restock not found"]);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Database error: " . $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(["error" => "No restock ID provided"]);
}
?>
<!--This is a property of PLSP-CCST BSIT-3B SY 2024-2025 -->
