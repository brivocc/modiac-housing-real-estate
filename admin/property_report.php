<?php
session_start();
require("config.php");

if(!isset($_SESSION['auser'])) {
    header("location:index.php");
    exit;
}

// Get filter parameters
$filter_status = isset($_GET['status']) ? $_GET['status'] : '';
$filter_purchase = isset($_GET['purchase_status']) ? $_GET['purchase_status'] : '';
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

// Build query with filters
$where_conditions = [];
$params = [];
$types = "";

if($filter_status) {
    $where_conditions[] = "p.status = ?";
    $params[] = $filter_status;
    $types .= "s";
}

if($filter_purchase) {
    $where_conditions[] = "p.purchase_status = ?";
    $params[] = $filter_purchase;
    $types .= "s";
}

if($start_date) {
    $where_conditions[] = "p.date >= ?";
    $params[] = $start_date;
    $types .= "s";
}

if($end_date) {
    $where_conditions[] = "p.date <= ?";
    $params[] = $end_date;
    $types .= "s";
}

$where_clause = $where_conditions ? "WHERE " . implode(" AND ", $where_conditions) : "";

// Get property data with owner and buyer information
$query = "
    SELECT 
        p.*,
        u.uname as owner_name,
        u.uemail as owner_email,
        buyer.uname as buyer_name,
        buyer.uemail as buyer_email,
        pur.purchase_date,
        pur.purchase_price
    FROM property p
    LEFT JOIN user u ON p.uid = u.uid
    LEFT JOIN purchases pur ON p.pid = pur.property_id
    LEFT JOIN user buyer ON pur.buyer_id = buyer.uid
    $where_clause
    ORDER BY p.date DESC
";

$stmt = $con->prepare($query);
if($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Calculate summary statistics
$summary_query = "
    SELECT 
        COUNT(*) as total_properties,
        SUM(CASE WHEN p.purchase_status = 'sold' THEN 1 ELSE 0 END) as sold_properties,
        SUM(CASE WHEN p.purchase_status = 'available' THEN 1 ELSE 0 END) as available_properties,
        SUM(CASE WHEN p.purchase_status = 'sold' THEN p.price ELSE 0 END) as total_sales_value,
        AVG(CASE WHEN p.purchase_status = 'sold' THEN p.price ELSE NULL END) as avg_sale_price
    FROM property p
    $where_clause
";

$summary_stmt = $con->prepare($summary_query);
if($params) {
    $summary_stmt->bind_param($types, ...$params);
}
$summary_stmt->execute();
$summary = $summary_stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Property Report - Admin</title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    
    <style>
        @media print {
            .no-print, .no-print * {
                display: none !important;
            }
            body {
                font-size: 12px;
            }
            .table {
                font-size: 11px;
            }
            .report-header {
                text-align: center;
                margin-bottom: 20px;
                border-bottom: 2px solid #000;
                padding-bottom: 10px;
            }
        }
        
        .report-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 15px;
        }
        
        .summary-cards {
            margin-bottom: 30px;
        }
        
        .summary-card {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            text-align: center;
            margin-bottom: 15px;
        }
        
        .summary-card h3 {
            margin: 0;
            color: #007bff;
            font-size: 24px;
        }
        
        .summary-card p {
            margin: 5px 0 0 0;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container-fluid mt-4">
        <!-- Header -->
        <div class="report-header">
            <h1>Property Management Report</h1>
            <p class="text-muted">Generated on <?php echo date('F j, Y \a\t g:i A'); ?></p>
            <?php if($start_date || $end_date): ?>
                <p class="text-muted">
                    Date Range: 
                    <?php echo $start_date ? date('M j, Y', strtotime($start_date)) : 'Beginning'; ?> - 
                    <?php echo $end_date ? date('M j, Y', strtotime($end_date)) : 'Today'; ?>
                </p>
            <?php endif; ?>
        </div>

        <!-- Filter Form (No Print) -->
        <div class="card no-print mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Filter Report</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="property_report.php">
                    <div class="row">
                        <div class="col-md-3">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="">All Status</option>
                                <option value="available" <?php echo $filter_status == 'available' ? 'selected' : ''; ?>>Available</option>
                                <option value="sold" <?php echo $filter_status == 'sold' ? 'selected' : ''; ?>>Sold</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Purchase Status</label>
                            <select name="purchase_status" class="form-control">
                                <option value="">All Purchase Status</option>
                                <option value="available" <?php echo $filter_purchase == 'available' ? 'selected' : ''; ?>>Available</option>
                                <option value="sold" <?php echo $filter_purchase == 'sold' ? 'selected' : ''; ?>>Sold</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Start Date</label>
                            <input type="date" name="start_date" class="form-control" value="<?php echo $start_date; ?>">
                        </div>
                        <div class="col-md-3">
                            <label>End Date</label>
                            <input type="date" name="end_date" class="form-control" value="<?php echo $end_date; ?>">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <button type="button" class="btn btn-success" onclick="window.print()">Print Report</button>
                            <button type="button" class="btn btn-info" onclick="downloadCSV()">Download CSV</button>
                            <a href="property_report.php" class="btn btn-secondary">Reset</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row summary-cards">
            <div class="col-md-3">
                <div class="summary-card">
                    <h3><?php echo number_format($summary['total_properties']); ?></h3>
                    <p>Total Properties</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-card">
                    <h3><?php echo number_format($summary['available_properties']); ?></h3>
                    <p>Available Properties</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-card">
                    <h3><?php echo number_format($summary['sold_properties']); ?></h3>
                    <p>Sold Properties</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-card">
                    <h3>Ksh <?php echo number_format($summary['total_sales_value']); ?></h3>
                    <p>Total Sales Value</p>
                </div>
            </div>
        </div>

        <!-- Properties Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Property Details</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Location</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th>Purchase Status</th>
                                <th>Owner</th>
                                <th>Owner Email</th>
                                <th>Buyer</th>
                                <th>Buyer Email</th>
                                <th>Added Date</th>
                                <th>Purchase Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['pid']; ?></td>
                                <td><?php echo htmlspecialchars($row['title']); ?></td>
                                <td><?php echo htmlspecialchars($row['type']); ?></td>
                                <td><?php echo htmlspecialchars($row['location']); ?></td>
                                <td>Ksh <?php echo number_format($row['price']); ?></td>
                                <td><?php echo ucfirst($row['status']); ?></td>
                                <td>
                                    <?php if($row['purchase_status'] == 'sold'): ?>
                                        <span class="badge badge-danger">Sold</span>
                                    <?php else: ?>
                                        <span class="badge badge-success">Available</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($row['owner_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['owner_email']); ?></td>
                                <td><?php echo htmlspecialchars($row['buyer_name'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($row['buyer_email'] ?? '-'); ?></td>
                                <td><?php echo date('M j, Y', strtotime($row['date'])); ?></td>
                                <td><?php echo $row['purchase_date'] ? date('M j, Y', strtotime($row['purchase_date'])) : '-'; ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-4 no-print">
            <p class="text-muted">Report generated by Real Estate Management System</p>
        </div>
    </div>

    <!-- JavaScript for CSV Download -->
    <script>
        function downloadCSV() {
            const table = document.querySelector('table');
            let csv = [];
            
            // Get headers
            const headers = [];
            table.querySelectorAll('thead th').forEach(th => {
                headers.push('"' + th.textContent.trim() + '"');
            });
            csv.push(headers.join(','));
            
            // Get rows
            table.querySelectorAll('tbody tr').forEach(tr => {
                const row = [];
                tr.querySelectorAll('td').forEach(td => {
                    row.push('"' + td.textContent.trim() + '"');
                });
                csv.push(row.join(','));
            });
            
            // Create and download file
            const csvContent = csv.join('\n');
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            link.setAttribute('href', url);
            link.setAttribute('download', 'property_report_<?php echo date('Y-m-d'); ?>.csv');
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>
</body>
</html>
<?php
$stmt->close();
$summary_stmt->close();
$con->close();
?>
