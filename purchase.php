<?php
session_start();
include("config.php");
include("include/access_control.php");

if(!isset($_SESSION['uemail'])) {
    redirectToLogin();
}

$propertyId = isset($_GET['pid']) ? intval($_GET['pid']) : 0;
$error = "";
$msg = "";

// Check if property exists and is available
$access = checkPropertyAccess($propertyId);
if (!$access['exists']) {
    $error = "Property not found.";
} elseif (!$access['isAvailable']) {
    $error = "This property is already sold.";
}

// Get property details
$propertyQuery = mysqli_query($con, "SELECT * FROM property WHERE pid = '$propertyId'");
$property = mysqli_fetch_assoc($propertyQuery);

// Get user details
$userId = $_SESSION['uid'];
$userQuery = mysqli_query($con, "SELECT * FROM user WHERE uid = '$userId'");
$user = mysqli_fetch_assoc($userQuery);

// Handle purchase form submission
if(isset($_POST['purchase'])) {
    if ($access['exists'] && $access['isAvailable']) {
        $purchasePrice = $property['price'];
        $sellerId = $property['uid'];
        
        // Start transaction
        mysqli_begin_transaction($con);
        
        try {
            // Insert purchase record
            $insertQuery = "INSERT INTO purchases (property_id, buyer_id, seller_id, purchase_price, payment_status) 
                           VALUES ('$propertyId', '$userId', '$sellerId', '$purchasePrice', 'pending')";
            mysqli_query($con, $insertQuery);
            
            // Update property status to sold
            $updateQuery = "UPDATE property SET purchase_status = 'sold' WHERE pid = '$propertyId'";
            mysqli_query($con, $updateQuery);
            
            mysqli_commit($con);
            $msg = "<p class='alert alert-success'>Purchase request submitted successfully! Our team will contact you shortly.</p>";
            
        } catch (Exception $e) {
            mysqli_rollback($con);
            $error = "<p class='alert alert-danger'>Error processing purchase. Please try again.</p>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Purchase Property - Real Estate PHP</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>

<div id="page-wrapper">
    <div class="row">
        <?php include("include/header.php");?>
        
        <div class="banner-full-row page-banner" style="background-image:url('images/breadcromb.jpg');">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <h2 class="page-name float-left text-white text-uppercase mt-1 mb-0"><b>Purchase Property</b></h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="full-row">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8">
                        <h2 class="text-secondary double-down-line text-center">Purchase Property</h2>
                        <div class="row p-5 bg-white">
                            <?php echo $error; ?>
                            <?php echo $msg; ?>
                            
                            <?php if(empty($error) && empty($msg)): ?>
                            <div class="col-md-12">
                                <h4>Property Details</h4>
                                <table class="table table-bordered">
                                    <tr>
                                        <td><strong>Property Title:</strong></td>
                                        <td><?php echo htmlspecialchars($property['title']); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Property Type:</strong></td>
                                        <td><?php echo htmlspecialchars($property['type']); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Location:</strong></td>
                                        <td><?php echo htmlspecialchars($property['location']); ?>, <?php echo htmlspecialchars($property['city']); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Price:</strong></td>
                                        <td>Ksh <?php echo number_format($property['price']); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Size:</strong></td>
                                        <td><?php echo htmlspecialchars($property['size']); ?> Sqft</td>
                                    </tr>
                                </table>

                                <h4>Your Details</h4>
                                <table class="table table-bordered">
                                    <tr>
                                        <td><strong>Name:</strong></td>
                                        <td><?php echo htmlspecialchars($user['uname']); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Email:</strong></td>
                                        <td><?php echo htmlspecialchars($user['uemail']); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Phone:</strong></td>
                                        <td><?php echo htmlspecialchars($user['uphone']); ?></td>
                                    </tr>
                                </table>

                                <form method="post" onsubmit="return confirm('Are you sure you want to purchase this property?');">
                                    <div class="form-group">
                                        <label>Additional Notes (Optional):</label>
                                        <textarea class="form-control" name="notes" rows="3" placeholder="Any special requirements or questions..."></textarea>
                                    </div>
                                    <button type="submit" name="purchase" class="btn btn-success btn-lg">
                                        Confirm Purchase
                                    </button>
                                    <a href="propertydetail.php?pid=<?php echo $propertyId; ?>" class="btn btn-secondary btn-lg">Cancel</a>
                                </form>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="sidebar-widget mt-5">
                            <h4 class="double-down-line-left text-secondary position-relative pb-4 mb-4">Important Information</h4>
                            <div class="alert alert-info">
                                <strong>Next Steps:</strong>
                                <ul>
                                    <li>After confirming purchase, our team will contact you within 24 hours</li>
                                    <li>Property viewing can be arranged</li>
                                    <li>Legal documentation will be provided</li>
                                    <li>Payment terms will be discussed</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include("include/footer.php");?>
    </div>
</div>

<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/custom.js"></script>
</body>
</html>
