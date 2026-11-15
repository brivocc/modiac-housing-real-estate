<header id="header" class="transparent-header-modern fixed-header-bg-white w-100">
            <div class="top-header bg-secondary">
                <div class="container">
                    <div class="row">
                        <div class="col-md-8">
                            <ul class="top-contact list-text-white  d-table">
                                <li><a href="#"><i class="fas fa-phone-alt text-success mr-1"></i>+254 722 123 456</a></li>
<li><a href="#"><i class="fas fa-envelope text-success mr-1"></i>info@modiachousing.co.ke</a></li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <div class="top-contact float-right">
                                <ul class="list-text-white d-table">
								<li><i class="fas fa-user text-success mr-1"></i>
								<?php  if(isset($_SESSION['uemail']))
								{ ?>
								<a href="logout.php">Logout</a>&nbsp;&nbsp;<?php } else { ?>
								<a href="login.php">Login</a>&nbsp;&nbsp;
								
								| </li>
								<li><i class="fas fa-user-plus text-success mr-1"></i><a href="register.php"> Register</li><?php } ?>
								</ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <div class="main-nav secondary-nav hover-success-nav py-2">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-2">
                        <a class="navbar-brand position-relative" href="index.php">
                            <img class="nav-logo" src="images/logo/Modiac.png" alt="Modiac Housing" style="max-height: 50px; width: auto;">
                        </a>
                    </div>
                    <div class="col-lg-10">
                        <div class="d-flex justify-content-between align-items-center">
                            <nav class="d-flex">
                                <a class="nav-link px-3" href="index.php">Home</a>
                                <a class="nav-link px-3" href="about.php">About Us</a>
                                <a class="nav-link px-3" href="contact.php">Contact Us</a>
                                <a class="nav-link px-3" href="property.php">Property Listings</a>
                                <a class="nav-link px-3" href="agent.php">Our Agents</a>
                                <?php if(isset($_SESSION['uemail'])) { ?>
                                <div class="dropdown">
                                    <a class="nav-link px-3 dropdown-toggle" href="#" role="button" data-toggle="dropdown">My Account</a>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="profile.php">My Profile</a>
                                        <a class="dropdown-item" href="feature.php">My Properties</a>
                                        <a class="dropdown-item" href="logout.php">Logout</a>
                                    </div>
                                </div>
                                <?php } else { ?>
                                <a class="nav-link px-3" href="login.php">Login/Register</a>
                                <?php } ?>
                            </nav>
                            <a class="btn btn-success" style="border-radius:30px;" href="submitproperty.php">List Your Property</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </header>
