<?php
include '../partials/admin-header.php';
include '../database/dbconnect.php';  // Ensure correct path to DB connection

$current_year = date('Y');
$next_year = $current_year + 1;
$years = [$current_year, $next_year];
$months = range(1, 12);
$quotas = [];
$success_message = '';
$error_message = '';

// Fetch existing quotas
$quota_sql = "SELECT year, month, quota_amount FROM monthly_quota WHERE year IN (?, ?)";
$quota_stmt = $pdo->prepare($quota_sql);
$quota_stmt->execute([$current_year, $next_year]);
$quota_results = $quota_stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($quota_results as $row) {
    $quotas[$row['year']][$row['month']] = $row['quota_amount'];
}
//This is a property of PLSP-CCST BSIT-3B SY 2024-2025 -->

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $pdo->beginTransaction();

        $upsert_sql = "INSERT INTO monthly_quota (year, month, quota_amount) 
                       VALUES (:year, :month, :quota_amount) 
                       ON CONFLICT (year, month) 
                       DO UPDATE SET quota_amount = :quota_amount";
        $upsert_stmt = $pdo->prepare($upsert_sql);

        foreach ($years as $year) {
            foreach ($months as $month) {
                $quota_amount = isset($_POST["quota_{$year}_{$month}"]) ? floatval($_POST["quota_{$year}_{$month}"]) : 0;
                
                $upsert_stmt->execute([
                    ':year' => $year,
                    ':month' => $month,
                    ':quota_amount' => $quota_amount
                ]);
            }
        }

        $pdo->commit();
        $success_message = "Monthly quotas have been updated successfully.";
        
        // Refresh quotas after update
        $quota_stmt->execute([$current_year, $next_year]);
        $quota_results = $quota_stmt->fetchAll(PDO::FETCH_ASSOC);

        $quotas = [];
        foreach ($quota_results as $row) {
            $quotas[$row['year']][$row['month']] = $row['quota_amount'];
        }
    } catch (Exception $e) {
        $pdo->rollBack();
        $error_message = "Error updating monthly quotas: " . $e->getMessage();
    }
}
?>

<main class="p-2 px-4">
    <section id="monthly-quota">
        <div class="content p-4">
            <h1 class="fw-bold mb-0">Edit Monthly Quota</h1>

            <?php if ($success_message): ?>
            <div class="alert alert-success mt-3" role="alert">
                <?php echo $success_message; ?>
            </div>
            <?php endif; ?>

            <?php if ($error_message): ?>
            <div class="alert alert-danger mt-3" role="alert">
                <?php echo $error_message; ?>
            </div>
            <?php endif; ?>

            <form action="" method="post">
                <?php foreach ($years as $year): ?>
                <h2 class="mt-4"><?php echo $year; ?></h2>
                <div class="row mt-4">
                    <?php foreach ($months as $month): ?>
                    <div class="col-md-3 mb-3">
                        <label for="quota_<?php echo $year; ?>_<?php echo $month; ?>" class="form-label fw-bold">
                            <?php echo date('F', mktime(0, 0, 0, $month, 1)); ?> <?php echo $year; ?>
                        </label>
                        <input type="number" step="0.01" class="form-control"
                            id="quota_<?php echo $year; ?>_<?php echo $month; ?>"
                            name="quota_<?php echo $year; ?>_<?php echo $month; ?>"
                            value="<?php echo isset($quotas[$year][$month]) ? number_format($quotas[$year][$month], 2, '.', '') : '0.00'; ?>"
                            required>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endforeach; ?>

                <div class="row mt-5">
                    <div class="col text-end">
                        <a href="./dashboard.php" class="btn btn-light me-2">Back</a>
                        <input type="submit" class="btn btn-dark" name="submit" value="Save Monthly Quotas">
                    </div>
                </div>
            </form>
        </div>
    </section>
</main>

<?php 
include '../partials/admin-footer.php';
ob_end_flush();
?>