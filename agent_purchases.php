<?php
session_start();
include("config.php");
include("include/access_control.php");

if(!isset($_SESSION['uemail'])) {
    header("location:login.php");
}

// Get agent ID from session
$agentId = $_SESSION['uid'];

// Get agent details
$agentQuery = mysqli_query($con, "SELECT * FROM user WHERE uid = '$agentId'");
$agent = mysqli_fetch_assoc($agentQuery);

// Get agent's properties with purchase details
$purchasesQuery = mysqli_query($con, "
    SELECT 
        p.*,
        pur.purchase_id,
        pur.purchase_price,
        pur.purchase_date,
        pur.payment_status,
        buyer.uname as buyer_name,
        buyer.uemail as buyer_email,
        buyer.uphone as buyer_phone
    FROM property p
    LEFT JOIN purchases pur ON p.pid = pur.property_id
    LEFT JOIN user buyer ON pur.buyer_id = buyer.uid
    WHERE p.uid = '$agentId'
    ORDER BY pur.purchase_date DESC, p.pid DESC
");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>My Property Sales - Real Estate PHP</title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
</head>
<body>

<div id="page-wrapper">
    <div class="row">
        <?php include("include/header.php");?>
        
        <div class="banner-full-row page-banner" style="background-image:url('images/breadcromb.jpg');">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <h2 class="page-name float-left text-white text-uppercase mt-1 mb-0"><b>My Property Sales</b></h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="full-row">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <h2 class="text-secondary double-down-line text-center mb-4">My Property Sales Dashboard</h2>
                        
                        <!-- Summary Cards -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <h4 class="card-title">
                                            <?php
                                            $totalProperties = mysqli_num_rows(mysqli_query($con, "SELECT * FROM property WHERE uid = '$agentId'"));
                                            echo $totalProperties;
                                            ?>
                                        </h4>
                                        <p class="card-text">Total Properties</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <h4 class="card-title">
                                            <?php
                                            $soldProperties = mysqli_num_rows(mysqli_query($con, "SELECT * FROM property WHERE uid = '$agentId' AND status = 'sold out'"));
                                            echo $soldProperties;
                                            ?>
                                        </h4>
                                        <p class="card-text">Properties Sold</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body">
                                        <h4 class="card-title">
                                            <?php
                                            $availableProperties = mysqli_num_rows(mysqli_query($con, "SELECT * FROM property WHERE uid = '$agentId' AND status = 'available'"));
                                            echo $availableProperties;
                                            ?>
                                        </h4>
                                        <p class="card-text">Available Properties</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body">
                                        <h4 class="card-title">
                                            <?php
                                            $totalSales = mysqli_num_rows(mysqli_query($con, "SELECT * FROM purchases WHERE seller_id = '$agentId'"));
                                            echo $totalSales;
                                            ?>
                                        </h4>
                                        <p class="card-text">Total Sales</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Properties Table -->
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">My Properties & Sales</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Property</th>
                                                <th>Type</th>
                                                <th>Location</th>
                                                <th>Price</th>
                                                <th>Status</th>
                                                <th>Buyer</th>
                                                <th>Purchase Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while($row = mysqli_fetch_assoc($purchasesQuery)): ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($row['title']); ?></strong><br>
                                                    <small><?php echo htmlspecialchars($row['type']); ?></small>
                                                </td>
                                                <td><?php echo htmlspecialchars($row['type']); ?></td>
                                                <td><?php echo htmlspecialchars($row['location']); ?></td>
                                                <td>Ksh <?php echo number_format($row['price']); ?></td>
                                                <td>
                                                    <?php if($row['status'] == 'sold out'): ?>
                                                        <span class="badge badge-danger">SOLD</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-success">Available</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if($row['buyer_name']): ?>
                                                        <strong><?php echo htmlspecialchars($row['buyer_name']); ?></strong><br>
                                                        <small><?php echo htmlspecialchars($row['buyer_email']); ?></small><br>
                                                        <small><?php echo htmlspecialchars($row['buyer_phone']); ?></small>
                                                    <?php else: ?>
                                                        <em>Not sold yet</em>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if($row['purchase_date']): ?>
                                                        <?php echo date('M d, Y', strtotime($row['purchase_date'])); ?>
                                                    <?php else: ?>
                                                        <em>-</em>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <a href="propertydetail.php?pid=<?php echo $row['pid']; ?>" class="btn btn-sm btn-info">View</a>
                                                    <?php if($row['status'] == 'available'): ?>
                                                        <a href="purchase.php?pid=<?php echo $row['pid']; ?>" class="btn btn-sm btn-success">Sell</a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Sales -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h4 class="card-title">Recent Sales</h4>
                            </div>
                            <div class="card-body">
                                <?php
                                $recentSales = mysqli_query($con, "
                                    SELECT 
                                        p.title,
                                        p.type,
                                        p.location,
                                        pur.purchase_price,
                                        buyer.uname as buyer_name,
                                        buyer.uemail as buyer_email,
                                        pur.purchase_date
                                    FROM purchases pur
                                    JOIN property p ON pur.property_id = p.pid
                                    JOIN user buyer ON pur.buyer_id = buyer.uid
                                    WHERE pur.seller_id = '$agentId'
                                    ORDER BY pur.purchase_date DESC
                                    LIMIT 5
                                ");
                                
                                if(mysqli_num_rows($recentSales) > 0):
                                    while($sale = mysqli_fetch_assoc($recentSales)):
                                ?>
                                <div class="alert alert-success">
                                    <strong><?php echo htmlspecialchars($sale['title']); ?></strong> sold to 
                                    <strong><?php echo htmlspecialchars($sale['buyer_name']); ?></strong> for 
                                    <strong>Ksh <?php echo number_format($sale['purchase_price']); ?></strong> on 
                                    <?php echo date('M d, Y', strtotime($sale['purchase_date'])); ?>
                                </div>
                                <?php
                                    endwhile;
                                else:
                                    echo "<p>No recent sales.</p>";
                                endif;
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <?php include("include/footer.php");?>
    </div>
</div>

<!-- JS Scripts -->
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/owl.carousel.min.js"></script>
<script src="js/wow.js"></script>
<script src="js/custom.js"></script>
</body>
</html>
