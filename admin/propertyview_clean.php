<?php
session_start();
require("config.php");

// Check authentication
if(!isset($_SESSION['auser'])) {
    header("location:index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Ventura - Property Management</title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png">
    
    <!-- CSS Files -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/feathericon.min.css">
    <link rel="stylesheet" href="assets/plugins/datatables/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Header -->
    <?php include("header.php"); ?>
    
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content container-fluid">
            <!-- Page Header -->
            <div class="page-header">
                <div class="row">
                    <div class="col">
                        <h3 class="page-title">Property</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item active">Property</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title mt-0 mb-4">Property View</h4>
                            <?php 
                            if(isset($_GET['msg'])) {
                                echo '<div class="alert alert-info">' . htmlspecialchars($_GET['msg']) . '</div>';
                            }
                            ?>
                            
                            <table id="datatable-buttons" class="table table-striped dt-responsive nowrap">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Type</th>
                                        <th>BHK</th>
                                        <th>S/R</th>
                                        <th>Area</th>
                                        <th>Price</th>
                                        <th>Location</th>
                                        <th>Status</th>
                                        <th>Purchase Status</th>
                                        <th>Added Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $query = mysqli_query($con, "SELECT pid, title, type, bhk, stype, size, price, location, status, purchase_status, date FROM property");
                                    while($row = mysqli_fetch_assoc($query)):
                                    ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                                            <td><?php echo htmlspecialchars($row['type']); ?></td>
                                            <td><?php echo htmlspecialchars($row['bhk']); ?></td>
                                            <td><?php echo htmlspecialchars($row['stype']); ?></td>
                                            <td><?php echo htmlspecialchars($row['size']); ?></td>
                                            <td><?php echo htmlspecialchars($row['price']); ?></td>
                                            <td><?php echo htmlspecialchars($row['location']); ?></td>
                                            <td><?php echo htmlspecialchars($row['status']); ?></td>
                                            <td>
                                                <?php 
                                                if($row['purchase_status'] == 'sold') {
                                                    echo '<span class="badge badge-danger">SOLD</span>';
                                                } else {
                                                    echo '<span class="badge badge-success">Available</span>';
                                                }
                                                ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($row['date']); ?></td>
                                            <td>
                                                <a href="propertyedit.php?id=<?php echo $row['pid'];?>" class="btn btn-info btn-sm">Edit</a>
                                                <a href="propertydelete.php?id=<?php echo $row['pid'];?>" class="btn btn-danger btn-sm">Delete</a>
                                                <a href="generate_invoice.php?pid=<?php echo $row['pid'];?>" target="_blank" class="btn btn-primary btn-sm">Download Invoice</a>
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

    <!-- Scripts -->
    <script src="assets/js/jquery-3.2.1.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/plugins/slimscroll/jquery.slimscroll.min.js"></script>
    <script src="assets/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="assets/plugins/datatables/dataTables.bootstrap4.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>
