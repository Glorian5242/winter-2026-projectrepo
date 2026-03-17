<?php
session_start();
require_once 'config.php';

$config = [
    'primary' => 'Square',  
    'secondary' => '',      
    'auto_verify' => 'Enabled'
];


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['download_report'])) {
    try {
        $stmt = $pdo->query("SELECT package_name, total_earnings, start_date, end_date FROM `package_reports` ORDER BY `report_date` DESC");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($data)) {
            $filename = "Package_Report_" . date('Y-m-d') . ".csv";
            

            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            
            $output = fopen('php://output', 'w');
            
            
            fputcsv($output, ['Package Name', 'Total Earnings', 'Start Date', 'End Date']);
            
         
            foreach ($data as $row) {
                fputcsv($output, $row);
            }
            
            fclose($output);
            exit; 
        }
    } catch (PDOException $e) {
        die("Export Error: " . $e->getMessage());
    }
}

// 2. FETCH DATA FOR THE ON-SCREEN TABLE
try {
    $stmt = $pdo->query("SELECT * FROM `package_reports` ORDER BY `report_date` DESC");
    $reportData = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}

include 'includes/header.php'; 
?>
<link rel="stylesheet" href="assets/css/report.css">

<div class="report-wrapper">
    <div class="page-header">
        <div class="header-content">
            <h1>Package Report</h1>
            <p>View the earnings and customer details per package</p>
        </div>
        
        <form method="POST">
            <button type="submit" name="download_report" class="btn-export">Download Report</button>
        </form>
    </div>

    <h2 class="section-title">Recent Package Reports</h2>
    <div class="table-card">
        <table class="report-table">
            <thead>
                <tr>
                    <th>Package Name</th>
                    <th>Total Earnings (in $)</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($reportData)): ?>
                    <tr><td colspan="4" style="text-align:center;">No report data found.</td></tr>
                <?php else: ?>
                    <?php foreach ($reportData as $row): ?>
                    <tr>
                        <td class="id-col"><?= htmlspecialchars($row['package_name']) ?></td>
                        <td><strong>$<?= number_format($row['total_earnings'], 2) ?></strong></td>
                        <td><?= date('M d, Y', strtotime($row['start_date'])) ?></td>
                        <td><?= date('M d, Y', strtotime($row['end_date'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="assets/js/report.js"></script>
<?php include 'includes/footer.php'; ?>
