<?php 
ini_set('session.cache_limiter','public');
session_cache_limiter(false);
session_start();
include("config.php");
include("include/access_control.php");

if(!isset($_SESSION['uemail']))
{
    header("location:login.php");
}

$uid = $_SESSION['uid'];
$user_type = isset($_SESSION['utype']) ? $_SESSION['utype'] : 'user';

// Get user details
$user_query = mysqli_query($con, "SELECT * FROM user WHERE uid='$uid'");
$user_data = mysqli_fetch_assoc($user_query);

// Get user's properties count
$properties_count = mysqli_num_rows(mysqli_query($con, "SELECT * FROM property WHERE uid='$uid'"));

// Get available properties
$available_properties = mysqli_num_rows(mysqli_query($con, "SELECT * FROM property WHERE uid='$uid' AND status='available'"));

// Get sold properties
$sold_properties = mysqli_num_rows(mysqli_query($con, "SELECT * FROM property WHERE uid='$uid' AND status='sold out'"));

// Get recent properties
$recent_properties = mysqli_query($con, "SELECT * FROM property WHERE uid='$uid' ORDER BY date DESC LIMIT 5");

// Get total earnings (for agents/builders)
$total_earnings = 0;
if($user_type == 'agent' || $user_type == 'builder') {
    $earnings_query = mysqli_query($con, "SELECT SUM(price) as total FROM property WHERE uid='$uid' AND status='sold out'");
    $earnings_data = mysqli_fetch_assoc($earnings_query);
    $total_earnings = $earnings_data['total'] ?? 0;
}

// Get recent feedback
$recent_feedback = mysqli_query($con, "SELECT * FROM feedback WHERE uid='$uid' ORDER BY date DESC LIMIT 3");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>User Dashboard - Real Estate PHP</title>
    
    <!-- CSS Links -->
    <link href="https://fonts.googleapis.com/css?family=Muli:400,400i,500,600,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Comfortaa:400,700" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/bootstrap-slider.css">
    <link rel="stylesheet" type="text/css" href="css/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="css/layerslider.css">
    <link rel="stylesheet" type="text/css" href="css/color.css">
    <link rel="stylesheet" type="text/css" href="css/owl.carousel.min.css">
    <link rel="stylesheet" type="text/css" href="css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="css/login.css">
    
    <style>
        .dashboard-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
            transition: transform 0.3s;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .property-item {
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            background: #fff;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-available {
            background: #d4edda;
            color: #155724;
        }
        .status-sold {
            background: #f8d7da;
            color: #721c24;
        }
        .sidebar {
            background: #2c3e50;
            color: white;
            min-height: 100vh;
            padding: 20px 0;
        }
        .sidebar a {
            color: white;
            padding: 10px 20px;
            display: block;
            text-decoration: none;
            transition: background 0.3s;
        }
        .sidebar a:hover {
            background: #34495e;
            text-decoration: none;
        }
        .sidebar .active {
            background: #3498db;
        }
    </style>
</head>
<body>

<div id="page-wrapper">
    <div class="row">
        <!-- Header -->
        <?php include("include/header.php");?>
        
        <!-- Banner -->
        <div class="banner-full-row page-banner" style="background-image:url('images/breadcromb.jpg');">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <h2 class="page-name float-left text-white text-uppercase mt-1 mb-0"><b>User Dashboard</b></h2>
                    </div>
                    <div class="col-md-6">
                        <nav aria-label="breadcrumb" class="float-left float-md-right">
                            <ol class="breadcrumb bg-transparent m-0 p-0">
                                <li class="breadcrumb-item text-white"><a href="index.php">Home</a></li>
                                <li class="breadcrumb-item active">Dashboard</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dashboard Content -->
        <div class="full-row">
            <div class="container">
                <div class="row">
                    <!-- Sidebar -->
                    <div class="col-lg-3">
                        <div class="sidebar">
                            <h5 class="text-center py-3">Dashboard Menu</h5>
                            <a href="user_dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                            <a href="submitproperty.php"><i class="fas fa-plus"></i> Add Property</a>
                            <a href="user_dashboard.php?section=my_properties"><i class="fas fa-home"></i> My Properties</a>
                            <a href="user_dashboard.php?section=sold_properties"><i class="fas fa-check-circle"></i> Sold Properties</a>
                            <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
                            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                        </div>
                    </div>

                    <!-- Main Content -->
                    <div class="col-lg-9">
                        <!-- Welcome Section -->
                        <div class="dashboard-card mb-4">
                            <h4>Welcome, <?php echo htmlspecialchars($user_data['uname']); ?>!</h4>
                            <p class="text-muted">Manage your properties and track your real estate activities.</p>
                        </div>

                        <!-- Statistics Cards -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="stat-card">
                                    <h3><?php echo $properties_count; ?></h3>
                                    <p>Total Properties</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card" style="background: linear-gradient(135deg, #56ab2f 0%, #a8e6cf 100%);">
                                    <h3><?php echo $available_properties; ?></h3>
                                    <p>Available</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card" style="background: linear-gradient(135deg, #ff6b6b 0%, #ffa8a8 100%);">
                                    <h3><?php echo $sold_properties; ?></h3>
                                    <p>Sold</p>
                                </div>
                            </div>
                            <?php if($user_type == 'agent' || $user_type == 'builder'): ?>
                            <div class="col-md-3">
                                <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                    <h3>KES <?php echo number_format($total_earnings); ?></h3>
                                    <p>Total Earnings</p>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Recent Properties -->
                        <div class="dashboard-card mb-4">
                            <h5>Recent Properties</h5>
                            <div class="row">
                                <?php while($property = mysqli_fetch_assoc($recent_properties)): ?>
                                <div class="col-md-6">
                                    <div class="property-item">
                                        <h6><?php echo htmlspecialchars($property['title']); ?></h6>
                                        <p><strong>Type:</strong> <?php echo htmlspecialchars($property['type']); ?></p>
                                        <p><strong>Price:</strong> KES <?php echo number_format($property['price']); ?></p>
                                        <p><strong>Location:</strong> <?php echo htmlspecialchars($property['location']); ?></p>
                                        <span class="status-badge status-<?php echo $property['status']; ?>">
                                            <?php echo ucfirst($property['status']); ?>
                                        </span>
                                        <div class="mt-2">
                                            <a href="propertydetail.php?pid=<?php echo $property['pid']; ?>" class="btn btn-sm btn-info">View</a>
                                            <a href="submitpropertyupdate.php?id=<?php echo $property['pid']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                        </div>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                            </div>
                        </div>

                        <!-- Recent Feedback -->
                        <div class="dashboard-card">
                            <h5>Recent Feedback</h5>
                            <?php if(mysqli_num_rows($recent_feedback) > 0): ?>
                                <?php while($feedback = mysqli_fetch_assoc($recent_feedback)): ?>
                                <div class="alert alert-info">
                                    <strong><?php echo date('M d, Y', strtotime($feedback['date'])); ?></strong><br>
                                    <?php echo htmlspecialchars(substr($feedback['fdescription'], 0, 100)); ?>...
                                </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <p>No feedback yet.</p>
                            <?php endif; ?>
                        </div>

                        <!-- My Properties Section -->
                        <?php if(isset($_GET['section']) && $_GET['section'] == 'my_properties'): ?>
                        <div class="dashboard-card mt-4">
                            <h5>All My Properties</h5>
                            <?php
                            $all_properties = mysqli_query($con, "SELECT * FROM property WHERE uid='$uid' ORDER BY date DESC");
                            while($property = mysqli_fetch_assoc($all_properties)):
                            ?>
                            <div class="property-item mb-3">
                                <div class="row">
                                    <div class="col-md-8">
                                        <h6><?php echo htmlspecialchars($property['title']); ?></h6>
                                        <p><strong>Type:</strong> <?php echo htmlspecialchars($property['type']); ?> | 
                                           <strong>BHK:</strong> <?php echo htmlspecialchars($property['bhk']); ?></p>
                                        <p><strong>Price:</strong> KES <?php echo number_format($property['price']); ?></p>
                                        <p><strong>Location:</strong> <?php echo htmlspecialchars($property['location']); ?></p>
                                    </div>
                                    <div class="col-md-4 text-right">
                                        <span class="status-badge status-<?php echo $property['status']; ?>">
                                            <?php echo ucfirst($property['status']); ?>
                                        </span>
                                        <div class="mt-2">
                                            <a href="propertydetail.php?pid=<?php echo $property['pid']; ?>" class="btn btn-sm btn-info">View</a>
                                            <?php if($property['status'] == 'available'): ?>
                                            <a href="submitpropertyupdate.php?id=<?php echo $property['pid']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                        <?php endif; ?>

                        <!-- Sold Properties Section -->
                        <?php if(isset($_GET['section']) && $_GET['section'] == 'sold_properties'): ?>
                        <div class="dashboard-card mt-4">
                            <h5>Sold Properties</h5>
                            <?php
                            $sold_properties_list = mysqli_query($con, "SELECT * FROM property WHERE uid='$uid' AND status='sold out' ORDER BY date DESC");
                            if(mysqli_num_rows($sold_properties_list) > 0):
                                while($property = mysqli_fetch_assoc($sold_properties_list)):
                            ?>
                            <div class="property-item mb-3">
                                <h6><?php echo htmlspecialchars($property['title']); ?></h6>
                                <p><strong>Sold Price:</strong> KES <?php echo number_format($property['price']); ?></p>
                                <p><strong>Location:</strong> <?php echo htmlspecialchars($property['location']); ?></p>
                                <p><strong>Sold Date:</strong> <?php echo date('M d, Y', strtotime($property['date'])); ?></p>
                            </div>
                            <?php
                                endwhile;
                            else:
                                echo "<p>No sold properties yet.</p>";
                            endif;
                            ?>
                        </div>
                        <?php endif; ?>
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
<script src="https://kit.fontawesome.com/a076d05399.js"></script>
</body>
</html>
