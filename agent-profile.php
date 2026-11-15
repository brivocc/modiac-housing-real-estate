<?php 
ini_set('session.cache_limiter','public');
session_cache_limiter(false);
session_start();
include("config.php");

// Get agent ID from URL
$agent_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch agent details
$agent_query = mysqli_query($con, "SELECT * FROM user WHERE uid = $agent_id AND utype='agent'");
$agent = mysqli_fetch_array($agent_query);

if(!$agent) {
    header("Location: agent.php");
    exit();
}

// Fetch properties listed by this agent
$properties_query = mysqli_query($con, "SELECT * FROM property WHERE uid = $agent_id AND status='available' ORDER BY date DESC");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css?family=Muli:400,400i,500,600,700&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Comfortaa:400,700" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/bootstrap-slider.css">
    <link rel="stylesheet" type="text/css" href="css/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="css/layerslider.css">
    <link rel="stylesheet" type="text/css" href="css/color.css" id="color-change">
    <link rel="stylesheet" type="text/css" href="css/owl.carousel.min.css">
    <link rel="stylesheet" type="text/css" href="css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="fonts/flaticon/flaticon.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <title><?php echo $agent['uname']; ?> - Real Estate Agent</title>
</head>
<body>

<div id="page-wrapper">
    <div class="row"> 
        <?php include("include/header.php");?>

        <div class="banner-full-row page-banner" style="background-image:url('images/breadcromb.jpg');">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <h2 class="page-name float-left text-white text-uppercase mt-1 mb-0"><b><?php echo $agent['uname']; ?></b></h2>
                    </div>
                    <div class="col-md-6">
                        <nav aria-label="breadcrumb" class="float-left float-md-right">
                            <ol class="breadcrumb bg-transparent m-0 p-0">
                                <li class="breadcrumb-item text-white"><a href="index.php">Home</a></li>
                                <li class="breadcrumb-item text-white"><a href="agent.php">Agents</a></li>
                                <li class="breadcrumb-item active"><?php echo $agent['uname']; ?></li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <div class="full-row">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="agent-photo">
                            <img src="admin/user/<?php echo $agent['uimage'];?>" alt="<?php echo $agent['uname'];?>" class="img-fluid rounded">
                        </div>
                        <div class="agent-contact-info bg-gray p-4 rounded mt-4">
                            <h4 class="mb-3">Contact Information</h4>
                            <ul class="list-unstyled">
                                <li class="mb-2"><i class="fas fa-user text-success mr-2"></i><strong><?php echo $agent['uname'];?></strong></li>
                                <li class="mb-2"><i class="fas fa-envelope text-success mr-2"></i><?php echo $agent['uemail'];?></li>
                                <li class="mb-2"><i class="fas fa-phone text-success mr-2"></i><?php echo $agent['uphone'];?></li>
                                <li class="mb-2"><i class="fas fa-tag text-success mr-2"></i>Real Estate Agent</li>
                            </ul>
                            <div class="mt-4">
                                <a href="mailto:<?php echo $agent['uemail'];?>" class="btn btn-success d-block mb-2">Send Email</a>
                                <a href="tel:<?php echo $agent['uphone'];?>" class="btn btn-primary d-block">Call Now</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-8">
                        <div class="agent-details bg-white p-4 rounded shadow">
                            <h3 class="mb-4">About <?php echo $agent['uname'];?></h3>
                            <p class="text-justify"><?php echo isset($agent['aboutme']) && !empty($agent['aboutme']) ? $agent['aboutme'] : 'Experienced real estate agent dedicated to helping clients find their dream properties. With extensive knowledge of the local market and a commitment to exceptional service, I ensure every transaction is smooth and successful.';?></p>
                            
                            <h4 class="mt-5 mb-4">Properties Listed by <?php echo $agent['uname'];?></h4>
                            
                            <a href="agent/purchased_properties.php" class="btn btn-primary mb-4">View My Property Sales</a>
                            
                            <?php if(mysqli_num_rows($properties_query) > 0): ?>
                                <div class="row">
                                <?php while($property = mysqli_fetch_array($properties_query)): ?>
                                    <div class="col-md-6 mb-4">
                                        <div class="property-item">
                                            <div class="property-image">
                                                <img src="admin/property/<?php echo $property['pimage'];?>" alt="<?php echo $property['title'];?>" class="img-fluid rounded">
                                                <div class="property-price">KSh <?php echo number_format($property['price']);?></div>
                                            </div>
                                            <div class="property-content mt-3">
                                                <h5><a href="propertydetail.php?pid=<?php echo $property['pid'];?>"><?php echo $property['title'];?></a></h5>
                                                <p><i class="fas fa-map-marker-alt text-success"></i> <?php echo $property['location'];?>, <?php echo $property['city'];?></p>
                                                <div class="property-features">
                                                    <span><i class="fas fa-bed"></i> <?php echo $property['bedroom'];?> Beds</span>
                                                    <span><i class="fas fa-bath"></i> <?php echo $property['bathroom'];?> Baths</span>
                                                    <span><i class="fas fa-expand"></i> <?php echo $property['size'];?> sqft</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                                </div>
                            <?php else: ?>
                                <p>No properties currently listed by this agent.</p>
                            <?php endif; ?>
                            
                            <div class="contact-agent mt-5">
                                <h4 class="mb-4">Contact <?php echo $agent['uname'];?></h4>
                                <form id="contactAgentForm">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <input type="text" class="form-control" placeholder="Your Name" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <input type="email" class="form-control" placeholder="Your Email" required>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <input type="tel" class="form-control" placeholder="Your Phone" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <input type="text" class="form-control" placeholder="Subject" required>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <textarea class="form-control" rows="4" placeholder="Your Message" required></textarea>
                                    </div>
                                    <input type="hidden" name="agent_email" value="<?php echo $agent['uemail'];?>">
                                    <button type="submit" class="btn btn-success">Send Message</button>
                                </form>
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
<script>
$(document).ready(function() {
    $('#contactAgentForm').on('submit', function(e) {
        e.preventDefault();
        alert('Thank you for your message! The agent will contact you soon.');
        this.reset();
    });
});
</script>
</body>
</html>
