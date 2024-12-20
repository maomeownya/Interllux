<?php
if ($reportType == 'sales') {
    try {
        // Fetch sales overview
        $overviewQuery = "SELECT 
            COUNT(*) as total_orders,
            SUM(total_amount) as total_sales,
            SUM(shipping_cost) as total_shipping_cost,
            SUM(CASE WHEN free_shipping = true THEN 1 ELSE 0 END) as free_shipping_orders,
            SUM(sub_amount) as total_revenue,
            SUM((SELECT SUM(quantity) FROM orders_item WHERE orders_item.orders_id = orders.orders_id)) as total_quantity_sold,
            SUM(CASE WHEN order_status = 'cancelled' THEN 1 ELSE 0 END) as total_cancelled,
            SUM(CASE WHEN order_status = 'return/refund' THEN 1 ELSE 0 END) as total_return_refund
        FROM orders
        WHERE order_date BETWEEN :start_date AND :end_date";
        $stmt = $pdo->prepare($overviewQuery);
        $stmt->bindParam(':start_date', $startDate, PDO::PARAM_STR);
        $stmt->bindParam(':end_date', $endDate, PDO::PARAM_STR);
        $stmt->execute();
        $overview = $stmt->fetch(PDO::FETCH_ASSOC);

        // Fetch sales breakdown by product <!--This is a property of PLSP-CCST BSIT-3B SY 2024-2025 -->

        $salesBreakdownQuery = "SELECT 
            p.product_id, p.name, SUM(oi.quantity) as quantity_sold, 
            p.price as unit_price, SUM(oi.item_price) as total_amount
        FROM orders_item oi
        JOIN product p ON oi.product_id = p.product_id
        JOIN orders o ON oi.orders_id = o.orders_id
        WHERE o.order_date BETWEEN :start_date AND :end_date
        GROUP BY p.product_id
        LIMIT :limit OFFSET :offset";
        $stmt = $pdo->prepare($salesBreakdownQuery);
        $stmt->bindParam(':start_date', $startDate, PDO::PARAM_STR);
        $stmt->bindParam(':end_date', $endDate, PDO::PARAM_STR);
        $stmt->bindParam(':limit', $itemsPerPage, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $salesBreakdownResult = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch transaction summary
        $transactionSummaryQuery = "SELECT 
            transac_type, COUNT(*) as count, SUM(transac_total_amount) as total_amount
        FROM transaction
        WHERE transac_date BETWEEN :start_date AND :end_date
        GROUP BY transac_type
        LIMIT :limit OFFSET :offset";
        $stmt = $pdo->prepare($transactionSummaryQuery);
        $stmt->bindParam(':start_date', $startDate, PDO::PARAM_STR);
        $stmt->bindParam(':end_date', $endDate, PDO::PARAM_STR);
        $stmt->bindParam(':limit', $itemsPerPage, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $transactionSummaryResult = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch return/refund details
        $returnRefundQuery = "SELECT 
            o.orders_id, t.reason, t.return_date, t.transac_total_amount as refund_amount
        FROM transaction t
        JOIN orders o ON t.orders_id = o.orders_id
        WHERE t.transac_type IN ('return', 'refund') AND t.transac_date BETWEEN :start_date AND :end_date
        LIMIT :limit OFFSET :offset";
        $stmt = $pdo->prepare($returnRefundQuery);
        $stmt->bindParam(':start_date', $startDate, PDO::PARAM_STR);
        $stmt->bindParam(':end_date', $endDate, PDO::PARAM_STR);
        $stmt->bindParam(':limit', $itemsPerPage, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $returnRefundResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div id="sales-option">
    <div class="content p-4 pt-0 mt-0">
        <div class="col-12 col-md-6">
            <p class="fw-bold fs-2 mb-0"><?php echo date('F j, Y', strtotime($startDate)); ?> -
                <?php echo date('F j, Y', strtotime($endDate)); ?></p>
            <p class="fw-light fs-4 mb-0">Report Generated: <?php echo date('F j, Y'); ?></p>
        </div>

        <p class="fw-bold mt-2 mb-0 fs-2">Sales Overview:</p>
        <div class="container-fluid px-0">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-lg-3">
                    <div class="card p-4 m-3">
                        <div class="card-info">
                            <p class="fw-light m-0">Total Orders:</p>
                            <p class="fs-2 fw-bold m-0"><?php echo $overview['total_orders']; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-sm-12 col-lg-3">
                    <div class="card p-4 m-3">
                        <div class="card-info">
                            <p class="fw-light m-0">Total Sales:</p>
                            <p class="fs-2 fw-bold m-0">₱<?php echo number_format($overview['total_sales'], 2); ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-sm-12 col-lg-3">
                    <div class="card p-4 m-3">
                        <div class="card-info">
                            <p class="fw-light m-0">Total Shipping Cost:</p>
                            <p class="fs-2 fw-bold m-0">
                                ₱<?php echo number_format($overview['total_shipping_cost'], 2); ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-sm-12 col-lg-3">
                    <div class="card p-4 m-3">
                        <div class="card-info">
                            <p class="fw-light m-0">Free Shipping Orders:</p>
                            <p class="fs-2 fw-bold m-0"><?php echo $overview['free_shipping_orders']; ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 col-sm-12 col-lg-3">
                    <div class="card p-4 m-3">
                        <div class="card-info">
                            <p class="fw-light m-0">Total Revenue:</p>
                            <p class="fs-2 fw-bold m-0">₱<?php echo number_format($overview['total_revenue'], 2); ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-sm-12 col-lg-3">
                    <div class="card p-4 m-3">
                        <div class="card-info">
                            <p class="fw-light m-0">Total Quantity Sold:</p>
                            <p class="fs-2 fw-bold m-0"><?php echo $overview['total_quantity_sold']; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-sm-12 col-lg-3">
                    <div class="card p-4 m-3">
                        <div class="card-info">
                            <p class="fw-light m-0">Total Cancel:</p>
                            <p class="fs-2 fw-bold m-0"><?php echo $overview['total_cancelled']; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-sm-12 col-lg-3">
                    <div class="card p-4 m-3">
                        <div class="card-info">
                            <p class="fw-light m-0">Total Return/Refund:</p>
                            <p class="fs-2 fw-bold m-0"><?php echo $overview['total_return_refund']; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content table-responsive p-4 pt-2">
        <div class="col-12 col-md-6">
            <h1 class="fw-bold fs-2">Sales Breakdown by Product:</h1>
        </div>
        <table id="sales-breakdown-table" class="table table-hover fs-6">
            <thead>
                <tr>
                    <th scope="col">PRODUCT ID</th>
                    <th scope="col">PRODUCT NAME</th>
                    <th scope="col">QUANTITY SOLD</th>
                    <th scope="col">UNIT PRICE</th>
                    <th scope="col">TOTAL AMOUNT</th>
                </tr>
            </thead>
            <tbody class="fw-light">
                <?php foreach ($salesBreakdownResult as $row): ?>
                <tr>
                    <td><?php echo $row['product_id']; ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo $row['quantity_sold']; ?></td>
                    <td>₱<?php echo number_format($row['unit_price'], 2); ?></td>
                    <td>₱<?php echo number_format($row['total_amount'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="content table-responsive p-4 pt-0">
        <div class="col-12 col-md-6">
            <h1 class="fw-bold fs-2">Transaction Summary:</h1>
        </div>
        <table id="transaction-summ-table" class="table table-hover fs-6">
            <thead>
                <tr>
                    <th scope="col">TRANSACTION TYPE</th>
                    <th scope="col">COUNT</th>
                    <th scope="col">TOTAL AMOUNT</th>
                </tr>
            </thead>
            <tbody class="fw-light">
                <?php foreach ($transactionSummaryResult as $row): ?>
                <tr>
                    <td><?php echo ucfirst(htmlspecialchars($row['transac_type'])); ?></td>
                    <td><?php echo $row['count']; ?></td>
                    <td>₱<?php echo number_format($row['total_amount'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="content table-responsive p-4 pt-0">
        <div class="col-12 col-md-6">
            <h1 class="fw-bold fs-2">Return/Refund Details:</h1>
        </div>
        <table id="return-refund-table" class="table table-hover fs-6">
            <thead>
                <tr>
                    <th scope="col">ORDER ID</th>
                    <th scope="col">REASON</th>
                    <th scope="col">RETURN DATE</th>
                    <th scope="col">REFUND AMOUNT</th>
                </tr>
            </thead>
            <tbody class="fw-light">
                <?php foreach ($returnRefundResult as $row): ?>
                <tr>
                    <td><?php echo $row['orders_id']; ?></td>
                    <td><?php echo htmlspecialchars($row['reason']); ?></td>
                    <td><?php echo $row['return_date']; ?></td>
                    <td>₱<?php echo number_format($row['refund_amount'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>