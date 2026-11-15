<?php 
include("config.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found - Real Estate PHP</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <div id="page-wrapper">
        <!-- Header -->
        <?php include("include/header.php");?>
        
        <div class="full-row bg-gray">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center p-5">
                            <h1 class="text-secondary mb-4">404 - Page Not Found</h1>
                            <div class="mb-4">
                                <i class="fas fa-exclamation-triangle text-warning" style="font-size: 72px;"></i>
                            </div>
                            <h3 class="text-secondary mb-4">Oops! The page you're looking for doesn't exist.</h3>
                            <p class="text-secondary mb-5">It looks like the page you're trying to access has been moved, deleted, or never existed. Don't worry, we'll help you find what you're looking for.</p>
                            
                            <div class="row justify-content-center">
                                <div class="col-md-6">
                                    <div class="input-group mb-4">
                                        <input type="text" class="form-control" placeholder="Search properties..." id="search404">
                                        <div class="input-group-append">
                                            <button class="btn btn-success" type="button" onclick="searchProperties()">
                                                <i class="fas fa-search"></i> Search
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <a href="index.php" class="btn btn-success btn-block">
                                        <i class="fas fa-home"></i> Home
                                    </a>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <a href="propertygrid.php" class="btn btn-outline-success btn-block">
                                        <i class="fas fa-building"></i> All Properties
                                    </a>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <a href="contact.php" class="btn btn-outline-success btn-block">
                                        <i class="fas fa-phone"></i> Contact Us
                                    </a>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <a href="javascript:history.back()" class="btn btn-outline-secondary btn-block">
                                        <i class="fas fa-arrow-left"></i> Go Back
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <?php include("include/footer.php");?>
    </div>

    <script>
    function searchProperties() {
        var searchTerm = document.getElementById('search404').value;
        if(searchTerm.trim() !== '') {
            window.location.href = 'propertygrid.php?search=' + encodeURIComponent(searchTerm);
        }
    }
    
    // Allow Enter key to trigger search
    document.getElementById('search404').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            searchProperties();
        }
    });
    </script>
</body>
</html>
