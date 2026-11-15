<?php
session_start();
require("../config.php");

if(!isset($_SESSION['uid'])) {
    header("Location: ../login.php");
    exit;
}

$userId = $_SESSION['uid'];

// Fetch properties where the logged-in user is the seller
$query = "
    SELECT p.pid, p.title, p.type, p.location, p.city, p.price, p.purchase_status, 
           pur.buyer_id, u.uname AS buyer_name, u.uemail AS buyer_email
    FROM property p
    LEFT JOIN purchases pur ON p.pid = pur.property_id AND pur.seller_id = ?
    LEFT JOIN user u ON pur.buyer_id = u.uid
    WHERE p.uid = ?
    ORDER BY p.pid DESC
";

$stmt = $con->prepare($query);
$stmt->bind_param("ii", $userId, $userId);
$stmt->execute();
$result = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0" />
    <title>My Properties - Purchase Status</title>
    <link rel="stylesheet" href="../admin/assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../admin/assets/css/style.css" />
</head>
<body>
    <div class="container mt-5">
        <h2>My Properties and Purchase Status</h2>
        <table class="table table-bordered table-striped mt-4">
            <thead>
                <tr>
                    <th>Property ID</th>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Location</th>
                    <th>City</th>
                    <th>Price</th>
                    <th>Purchase Status</th>
                    <th>Buyer Name</th>
                    <th>Buyer Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['pid']); ?></td>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td><?php echo htmlspecialchars($row['type']); ?></td>
                    <td><?php echo htmlspecialchars($row['location']); ?></td>
                    <td><?php echo htmlspecialchars($row['city']); ?></td>
                    <td>Ksh <?php echo number_format($row['price']); ?></td>
                    <td>
                        <?php 
                        if ($row['purchase_status'] === 'sold') {
                            echo '<span class="badge badge-danger">SOLD</span>';
                        } else {
                            echo '<span class="badge badge-success">Available</span>';
                        }
                        ?>
                    </td>
                    <td><?php echo htmlspecialchars($row['buyer_name'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($row['buyer_email'] ?? '-'); ?></td>
                    <td>
                        <a href="propertydetail.php?pid=<?php echo $row['pid']; ?>" class="btn btn-sm btn-info">View</a>
                        <?php if ($row['purchase_status'] == 'available'): ?>
                            <a href="purchase.php?pid=<?php echo $row['pid']; ?>" class="btn btn-sm btn-success">Sell</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <a href="../agent-profile.php" class="btn btn-primary">Back to Profile</a>
    </div>
</body>
</html>
<?php
$stmt->close();
$con->close();
?>
