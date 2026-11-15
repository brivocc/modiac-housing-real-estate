<?php
session_start();
include("config.php");
include("include/access_control.php");

if(!isset($_SESSION['uemail'])) {
    header("location:login.php");
}

$agentId = $_SESSION['uid'];

// Get agent details
$agentQuery = mysqli_query($con, "SELECT * FROM user WHERE uid = '$agentId'");
$agent = mysqli_fetch_assoc($agentQuery);

// Get statistics
$totalProperties = mysqli_num_rows(mysqli_query($con, "SELECT * FROM property WHERE uid = '$agentId'"));
$soldProperties = mysqli_num_rows(mysqli_query($con, "SELECT * FROM property WHERE uid = '$agentId' AND status = 'sold out'"));
$availableProperties = mysqli_num_rows(mysqli_query($con, "SELECT * FROM property WHERE uid = '$agentId' AND status = 'available'"));
$totalSales = mysqli_num_rows(mysqli_query($con, "SELECT * FROM purchases WHERE seller_id = '$agentId'"));

// Get properties
$properties = mysqli_query($con, "
    SELECT p.*, pur.purchase_date, buyer.uname as buyer_name
    FROM property p
    LEFT JOIN purchases pur ON p.pid = pur.property_id
    LEFT JOIN user buyer ON pur.buyer_id = buyer.uid
    WHERE p.uid = '$agentId'
    ORDER BY p.date DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Agent Dashboard - Real Estate PHP</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div id="page-wrapper">
    <?php include("include/header.php");?>
    
    <div class="banner-full-row page-banner" style="background-image:url('images/breadcromb.jpg');">
        <div class="container">
            <h2 class="page-name text-white text-uppercase">Agent Dashboard</h2>
        </div>
    </div>

    <div class="full-row">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h2 class="text-secondary double-down-line text-center mb-4">My Properties Dashboard</h2>
                    
                    <!-- Stats Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h4><?php echo $totalProperties; ?></h4>
                                    <p>Total Properties</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h4><?php echo $soldProperties; ?></h4>
                                    <p>Sold Properties</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h4><?php echo $availableProperties; ?></h4>
                                    <p>Available Properties</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h4><?php echo $totalSales; ?></h4>
                                    <p>Total Sales</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Properties Table -->
                    <div class="card">
                        <div class="card-header">
                            <h4>My Properties</h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Type</th>
                                        <th>Location</th>
                                        <th>Price</th>
                                        <th>Status</th>
                                        <th>Buyer</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($property = mysqli_fetch_assoc($properties)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($property['title']); ?></td>
                                        <td><?php echo htmlspecialchars($property['type']); ?></td>
                                        <td><?php echo htmlspecialchars($property['location']); ?></td>
                                        <td>Ksh <?php echo number_format($property['price']); ?></td>
                                        <td>
                                            <?php if($property['status'] == 'sold out'): ?>
                                                <span class="badge badge-danger">Sold</span>
                                            <?php else: ?>
                                                <span class="badge badge-success">Available</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $property['buyer_name'] ? htmlspecialchars($property['buyer_name']) : '-'; ?></td>
                                        <td>
                                            <a href="propertydetail.php?pid=<?php echo $property['pid']; ?>" class="btn btn-sm btn-info">View</a>
                                            <?php if($property['status'] == 'available'): ?>
                                                <a href="submitpropertyupdate.php?id=<?php echo $property['pid']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include("include/footer.php");?>
</div>

<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
