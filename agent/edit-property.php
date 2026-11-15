<?php
ini_set('session.cache_limiter','public');
session_cache_limiter(false);
session_start();
include("../config.php");
include("../include/property_access.php");

if(!isset($_SESSION['uemail'])) {
    header("location:../login.php");
    exit();
}

// Get property ID and verify ownership
$property_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user_id = $_SESSION['uid'];

// Verify ownership
if(!verifyPropertyOwnership($property_id, $user_id)) {
    $_SESSION['error'] = "You don't have permission to edit this property.";
    header("location:../feature.php");
    exit();
}

// Get property details
$property = getPropertyForEdit($property_id, $user_id);
if(!$property) {
    $_SESSION['error'] = "Property not found or you don't have permission to edit it.";
    header("location:../feature.php");
    exit();
}

// Handle form submission
$msg = "";
$error = "";

if(isset($_POST['update_property'])) {
    // Sanitize and validate input
    $title = mysqli_real_escape_string($con, $_POST['title']);
    $content = mysqli_real_escape_string($con, $_POST['content']);
    $ptype = mysqli_real_escape_string($con, $_POST['ptype']);
    $bhk = mysqli_real_escape_string($con, $_POST['bhk']);
    $bed = (int)$_POST['bed'];
    $balc = (int)$_POST['balc'];
    $hall = (int)$_POST['hall'];
    $stype = mysqli_real_escape_string($con, $_POST['stype']);
    $bath = (int)$_POST['bath'];
    $kitc = (int)$_POST['kitc'];
    $floor = mysqli_real_escape_string($con, $_POST['floor']);
    $price = (float)$_POST['price'];
    $city = mysqli_real_escape_string($con, $_POST['city']);
    $asize = (int)$_POST['asize'];
    $loc = mysqli_real_escape_string($con, $_POST['loc']);
    $state = mysqli_real_escape_string($con, $_POST['state']);
    $status = mysqli_real_escape_string($con, $_POST['status']);
    $feature = mysqli_real_escape_string($con, $_POST['feature']);
    $totalfloor = mysqli_real_escape_string($con, $_POST['totalfl']);
    $isFeatured = (int)$_POST['isFeatured'];

    // Handle image uploads
    $image_fields = ['aimage', 'aimage1', 'aimage2', 'aimage3', 'aimage4'];
    $fimage_fields = ['fimage', 'fimage1', 'fimage2'];
    
    $image_paths = [];
    $fimage_paths = [];
    
    // Map field names to database column names
    $image_field_map = [
        'aimage' => 'pimage',
        'aimage1' => 'pimage1',
        'aimage2' => 'pimage2',
        'aimage3' => 'pimage3',
        'aimage4' => 'pimage4'
    ];
    
    $fimage_field_map = [
        'fimage' => 'mapimage',
        'fimage1' => 'topmapimage',
        'fimage2' => 'groundmapimage'
    ];
    
    // Process main images
    foreach($image_fields as $field) {
        $db_field = $image_field_map[$field];
        if(isset($_FILES[$field]) && $_FILES[$field]['error'] == 0) {
            $filename = time() . '_' . $_FILES[$field]['name'];
            $target_path = "../admin/property/" . $filename;
            
            if(move_uploaded_file($_FILES[$field]['tmp_name'], $target_path)) {
                $image_paths[$field] = $filename;
            } else {
                $image_paths[$field] = $property[$db_field];
            }
        } else {
            $image_paths[$field] = $property[$db_field];
        }
    }
    
    // Process floor plan images
    foreach($fimage_fields as $field) {
        $db_field = $fimage_field_map[$field];
        if(isset($_FILES[$field]) && $_FILES[$field]['error'] == 0) {
            $filename = time() . '_' . $_FILES[$field]['name'];
            $target_path = "../admin/property/" . $filename;
            
            if(move_uploaded_file($_FILES[$field]['tmp_name'], $target_path)) {
                $fimage_paths[$field] = $filename;
            } else {
                $fimage_paths[$field] = $property[$db_field];
            }
        } else {
            $fimage_paths[$field] = $property[$db_field];
        }
    }

    // Update property
    $sql = "UPDATE property SET 
            title = '$title',
            pcontent = '$content',
            type = '$ptype',
            bhk = '$bhk',
            stype = '$stype',
            bedroom = $bed,
            bathroom = $bath,
            balcony = $balc,
            kitchen = $kitc,
            hall = $hall,
            floor = '$floor',
            size = $asize,
            price = $price,
            location = '$loc',
            city = '$city',
            state = '$state',
            feature = '$feature',
            pimage = '{$image_paths['aimage']}',
            pimage1 = '{$image_paths['aimage1']}',
            pimage2 = '{$image_paths['aimage2']}',
            pimage3 = '{$image_paths['aimage3']}',
            pimage4 = '{$image_paths['aimage4']}',
            status = '$status',
            mapimage = '{$fimage_paths['fimage']}',
            topmapimage = '{$fimage_paths['fimage1']}',
            groundmapimage = '{$fimage_paths['fimage2']}',
            totalfloor = '$totalfloor',
            isFeatured = $isFeatured
            WHERE pid = $property_id AND uid = $user_id";
    
    $result = mysqli_query($con, $sql);
    
    if($result) {
        $_SESSION['success'] = "Property updated successfully!";
        header("location:../feature.php");
        exit();
    } else {
        $error = "Error updating property: " . mysqli_error($con);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Edit Property - Real Estate</title>
    
    <!-- CSS Links -->
    <link href="https://fonts.googleapis.com/css?family=Muli:400,400i,500,600,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Comfortaa:400,700" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../css/bootstrap-slider.css">
    <link rel="stylesheet" type="text/css" href="../css/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="../css/style.css">
    <link rel="stylesheet" type="text/css" href="../css/login.css">
</head>
<body>

<div id="page-wrapper">
    <div class="row">
        <!-- Header -->
        <?php include("../include/header.php"); ?>
        
        <div class="full-row">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <h2 class="text-secondary double-down-line text-center">Edit Property</h2>
                        <?php if($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="row p-5 bg-white">
                    <form method="post" enctype="multipart/form-data" class="w-100">
                        
                        <div class="description">
                            <h5 class="text-secondary">Basic Information</h5><hr>
                            
                            <div class="row">
                            <div class="col-xl-12">
                                    <div class="form-group row">
                                        <label class="col-lg-2 col-form-label">Title</label>
                                        <div class="col-lg-9">
                                            <input type="text" class="form-control" name="title" required value="<?php echo htmlspecialchars($property['title']); ?>">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-2 col-form-label">Content</label>
                                        <div class="col-lg-9">
                                            <textarea class="form-control" name="content" rows="5" required><?php echo htmlspecialchars($property['pcontent']); ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-xl-6">
                                    <div class="form-group row">
                                        <label class="col-lg-3 col-form-label">Property Type</label>
                                        <div class="col-lg-9">
                                            <select class="form-control" required name="ptype">
                                                <option value="">Select Type</option>
                                                <option value="apartment" <?php echo $property['type'] == 'apartment' ? 'selected' : ''; ?>>Apartment</option>
                                                <option value="flat" <?php echo $property['type'] == 'flat' ? 'selected' : ''; ?>>Flat</option>
                                                <option value="building" <?php echo $property['type'] == 'building' ? 'selected' : ''; ?>>Building</option>
                                                <option value="house" <?php echo $property['type'] == 'house' ? 'selected' : ''; ?>>House</option>
                                                <option value="villa" <?php echo $property['type'] == 'villa' ? 'selected' : ''; ?>>Villa</option>
                                                <option value="office" <?php echo $property['type'] == 'office' ? 'selected' : ''; ?>>Office</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-3 col-form-label">Selling Type</label>
                                        <div class="col-lg-9">
                                            <select class="form-control" required name="stype">
                                                <option value="">Select Status</option>
                                                <option value="rent" <?php echo $property['stype'] == 'rent' ? 'selected' : ''; ?>>Rent</option>
                                                <option value="sale" <?php echo $property['stype'] == 'sale' ? 'selected' : ''; ?>>Sale</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-3 col-form-label">Bathroom</label>
                                        <div class="col-lg-9">
                                            <input type="number" class="form-control" name="bath" required value="<?php echo $property['bathroom']; ?>">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-3 col-form-label">Kitchen</label>
                                        <div class="col-lg-9">
                                            <input type="number" class="form-control" name="kitc" required value="<?php echo $property['kitchen']; ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-xl-6">
                                    <div class="form-group row">
                                        <label class="col-lg-3 col-form-label">BHK</label>
                                        <div class="col-lg-9">
                                            <select class="form-control" required name="bhk">
                                                <option value="">Select BHK</option>
                                                <option value="1 BHK" <?php echo $property['bhk'] == '1 BHK' ? 'selected' : ''; ?>>1 BHK</option>
                                                <option value="2 BHK" <?php echo $property['bhk'] == '2 BHK' ? 'selected' : ''; ?>>2 BHK</option>
                                                <option value="3 BHK" <?php echo $property['bhk'] == '3 BHK' ? 'selected' : ''; ?>>3 BHK</option>
                                                <option value="4 BHK" <?php echo $property['bhk'] == '4 BHK' ? 'selected' : ''; ?>>4 BHK</option>
                                                <option value="5 BHK" <?php echo $property['bhk'] == '5 BHK' ? 'selected' : ''; ?>>5 BHK</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-3 col-form-label">Bedroom</label>
                                        <div class="col-lg-9">
                                            <input type="number" class="form-control" name="bed" required value="<?php echo $property['bedroom']; ?>">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-3 col-form-label">Balcony</label>
                                        <div class="col-lg-9">
                                            <input type="number" class="form-control" name="balc" required value="<?php echo $property['balcony']; ?>">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-3 col-form-label">Hall</label>
                                        <div class="col-lg-9">
                                            <input type="number" class="form-control" name="hall" required value="<?php echo $property['hall']; ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <h5 class="text-secondary">Price & Location</h5><hr>
                            <div class="row">
                                <div class="col-xl-6">
                                    <div class="form-group row">
                                        <label class="col-lg-3 col-form-label">Floor</label>
                                        <div class="col-lg-9">
                                            <select class="form-control" required name="floor">
                                                <option value="">Select Floor</option>
                                                <option value="1st Floor" <?php echo $property['floor'] == '1st Floor' ? 'selected' : ''; ?>>1st Floor</option>
                                                <option value="2nd Floor" <?php echo $property['floor'] == '2nd Floor' ? 'selected' : ''; ?>>2nd Floor</option>
                                                <option value="3rd Floor" <?php echo $property['floor'] == '3rd Floor' ? 'selected' : ''; ?>>3rd Floor</option>
                                                <option value="4th Floor" <?php echo $property['floor'] == '4th Floor' ? 'selected' : ''; ?>>4th Floor</option>
                                                <option value="5th Floor" <?php echo $property['floor'] == '5th Floor' ? 'selected' : ''; ?>>5th Floor</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-3 col-form-label">Price</label>
                                        <div class="col-lg-9">
                                            <input type="number" class="form-control" name="price" required value="<?php echo $property['price']; ?>">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-3 col-form-label">City</label>
                                        <div class="col-lg-9">
                                            <input type="text" class="form-control" name="city" required value="<?php echo htmlspecialchars($property['city']); ?>">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-3 col-form-label">State</label>
                                        <div class="col-lg-9">
                                            <input type="text" class="form-control" name="state" required value="<?php echo htmlspecialchars($property['state']); ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-xl-6">
                                    <div class="form-group row">
                                        <label class="col-lg-3 col-form-label">Total Floor</label>
                                        <div class="col-lg-9">
                                            <input type="text" class="form-control" name="totalfl" required value="<?php echo $property['totalfloor']; ?>">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-3 col-form-label">Area Size</label>
                                        <div class="col-lg-9">
                                            <input type="number" class="form-control" name="asize" required value="<?php echo $property['size']; ?>">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-3 col-form-label">Address</label>
                                        <div class="col-lg-9">
                                            <input type="text" class="form-control" name="loc" required value="<?php echo htmlspecialchars($property['location']); ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label class="col-lg-2 col-form-label">Feature</label>
                                <div class="col-lg-9">
                                    <textarea class="form-control" name="feature" rows="4" required><?php echo htmlspecialchars($property['feature']); ?></textarea>
                                </div>
                            </div>
                            
                            <h5 class="text-secondary">Images</h5><hr>
                            <div class="row">
                                <div class="col-xl-4">
                                    <div class="form-group">
                                        <label>Main Image</label>
                                        <input type="file" class="form-control-file" name="aimage">
                                        <img src="../admin/property/<?php echo $property['pimage']; ?>" alt="Main" class="img-thumbnail mt-2" style="max-width: 200px;">
                                    </div>
                                </div>
                                <div class="col-xl-4">
                                    <div class="form-group">
                                        <label>Image 1</label>
                                        <input type="file" class="form-control-file" name="aimage1">
                                        <img src="../admin/property/<?php echo $property['pimage1']; ?>" alt="Image 1" class="img-thumbnail mt-2" style="max-width: 200px;">
                                    </div>
                                </div>
                                <div class="col-xl-4">
                                    <div class="form-group">
                                        <label>Image 2</label>
                                        <input type="file" class="form-control-file" name="aimage2">
                                        <img src="../admin/property/<?php echo $property['pimage2']; ?>" alt="Image 2" class="img-thumbnail mt-2" style="max-width: 200px;">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-xl-4">
                                    <div class="form-group">
                                        <label>Image 3</label>
                                        <input type="file" class="form-control-file" name="aimage3">
                                        <img src="../admin/property/<?php echo $property['pimage3']; ?>" alt="Image 3" class="img-thumbnail mt-2" style="max-width: 200px;">
                                    </div>
                                </div>
                                <div class="col-xl-4">
                                    <div class="form-group">
                                        <label>Image 4</label>
                                        <input type="file" class="form-control-file" name="aimage4">
                                        <img src="../admin/property/<?php echo $property['pimage4']; ?>" alt="Image 4" class="img-thumbnail mt-2" style="max-width: 200px;">
                                    </div>
                                </div>
                                <div class="col-xl-4">
                                    <div class="form-group">
                                        <label>Floor Plan</label>
                                        <input type="file" class="form-control-file" name="fimage">
                                        <img src="../admin/property/<?php echo $property['mapimage']; ?>" alt="Floor Plan" class="img-thumbnail mt-2" style="max-width: 200px;">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-xl-6">
                                    <div class="form-group">
                                        <label>Top Map Image</label>
                                        <input type="file" class="form-control-file" name="fimage1">
                                        <img src="../admin/property/<?php echo $property['topmapimage']; ?>" alt="Top Map" class="img-thumbnail mt-2" style="max-width: 200px;">
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="form-group">
                                        <label>Ground Map Image</label>
                                        <input type="file" class="form-control-file" name="fimage2">
                                        <img src="../admin/property/<?php echo $property['groundmapimage']; ?>" alt="Ground Map" class="img-thumbnail mt-2" style="max-width: 200px;">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-xl-6">
                                    <div class="form-group row">
                                        <label class="col-lg-3 col-form-label">Status</label>
                                        <div class="col-lg-9">
                                            <select class="form-control" required name="status">
                                                <option value="available" <?php echo $property['status'] == 'available' ? 'selected' : ''; ?>>Available</option>
                                                <option value="sold out" <?php echo $property['status'] == 'sold out' ? 'selected' : ''; ?>>Sold Out</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="form-group row">
                                        <label class="col-lg-3 col-form-label">Featured</label>
                                        <div class="col-lg-9">
                                            <select class="form-control" required name="isFeatured">
                                                <option value="0" <?php echo $property['isFeatured'] == 0 ? 'selected' : ''; ?>>No</option>
                                                <option value="1" <?php echo $property['isFeatured'] == 1 ? 'selected' : ''; ?>>Yes</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <div class="col-lg-9 offset-lg-2">
                                    <button type="submit" name="update_property" class="btn btn-success">Update Property</button>
                                    <a href="../feature.php" class="btn btn-secondary ml-2">Cancel</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <?php include("../include/footer.php"); ?>
    </div>
</body>
</html>
<?php
// Close database connection
mysqli_close($con);
?>
